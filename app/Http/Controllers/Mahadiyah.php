<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Wirid;
use App\Models\Yasinan;
use App\Models\Kegiatan;
use App\Models\Pengurus;
use App\Models\Bandongan;
use Illuminate\Http\Request;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class Mahadiyah extends Controller
{
    // LEGACY DASHBOARD REMOVED
    public function absensi()
    {
        $bandongan = Bandongan::with('pengurus')->get();
        $wirid = Wirid::with('pengurus')->get();
        $yasinan = Yasinan::with('pengurus')->get();
        return view('mahadiyah.absensi-pengurus', compact(
            'bandongan',
            'wirid',
            'yasinan'
        ));
    }
    public function create_absen()
    {
        $kegiatan = Kegiatan::where('ket', 'P')->get();
        $pengurus = Pengurus::select('nis', 'nama')->get();
        return view('mahadiyah.create-absensi', compact('kegiatan', 'pengurus'));
    }
    public function store_absen(Request $request)
    {
        $kegiatan = $request->kegiatan;
        $tanggal = $request->tanggal;
        match ($kegiatan) {
            '7' => $exists = Bandongan::where('tanggal', $tanggal)->exists(),
            '8' => $exists = Wirid::where('tanggal', $tanggal)->exists(),
            '9' => $exists = Yasinan::where('tanggal', $tanggal)->exists()
        };
        if ($exists) {
            session()->flash('error', 'Absensi hari ini sudah dibuat');
            return redirect('/mahadiyah/absensi-pengurus');
        }
        foreach ($request->pengurus as $nis => $status) {
            $pengurus = Pengurus::where('nis', (string) $nis)->first();
            if ($pengurus) {
                match ($kegiatan) {
                    '7' => Bandongan::create(['nis' => (string) $nis, 'tanggal' => $tanggal, 'status' => $status]),
                    '8' => Wirid::create(['nis' => (string) $nis, 'tanggal' => $tanggal, 'status' => $status]),
                    '9' => Yasinan::create(['nis' => (string) $nis, 'tanggal' => $tanggal, 'status' => $status])
                };
            }
        }
        session()->flash('success', 'Absensi berhasil dibuat');
        return redirect('/mahadiyah/absensi-pengurus');
    }
    public function mingguan()
    {
        return view('mahadiyah.absen-mingguan');
    }
    public function kegiatan()
    {

    }

    // ------------------- //
    // REKAP KEGIATAN KEPALA KAMAR //
    // ------------------- //
    public function rekapKegiatan(Request $request)
    {
        // Get date range or default to last 7 days
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now();

        $startDate = $request->input('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->subDays(6);

        // Generate array of dates in range
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d/m/Y');
            $currentDate->addDay();
        }

        $totalDays = count($dates);

        // Get all kepala kamar
        $kepkams = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->where('user.role', 'kepkam')
            ->select('p.nama', 'p.nis')
            ->orderBy('p.nama')
            ->get();

        // Define all activities to check
        $activities = [
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        // Build recap data for each kepala kamar
        $rekapData = [];
        foreach ($kepkams as $kepkam) {
            $row = [
                'nis' => $kepkam->nis,
                'nama' => $kepkam->nama,
                'daily_status' => [],
                'total' => 0,
                'percentage' => 0
            ];

            // Check each date
            foreach ($dates as $date) {
                $hasFilled = $this->checkKepkamActivity($kepkam->nis, $date, $activities);
                $row['daily_status'][$date] = $hasFilled;
                if ($hasFilled) {
                    $row['total']++;
                }
            }

            // Calculate current percentage
            $row['percentage'] = $totalDays > 0 ? round(($row['total'] / $totalDays) * 100) : 0;

            $rekapData[] = $row;
        }

        return view('mahadiyah.rekap-kegiatan', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));
    }

    private function checkKepkamActivity($kepkamNis, $tanggal, $activities)
    {
        // Get santri under this kepkam
        $santriNis = DB::table('santri')
            ->where('kepkam', $kepkamNis)
            ->pluck('nis')
            ->toArray();

        if (empty($santriNis)) {
            return false;
        }

        // Check if any activity has attendance for this date
        foreach ($activities as $activity) {
            $modelClass = "\\App\\Models\\{$activity['model']}";
            $query = $modelClass::where('tanggal', $tanggal)
                ->whereIn('nis', $santriNis);

            if ($activity['col']) {
                $query->where($activity['col'], $activity['val']);
            }

            if ($query->exists()) {
                return true;
            }
        }

        return false;
    }

    public function downloadRekapKegiatan(Request $request)
    {
        try {
            // Get date range or default to last 7 days
            $endDate = $request->input('end_date')
                ? Carbon::parse($request->end_date)
                : Carbon::now();

            $startDate = $request->input('start_date')
                ? Carbon::parse($request->start_date)
                : Carbon::now()->subDays(6);

            // Generate array of dates in range
            $dates = [];
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dates[] = $currentDate->format('d/m/Y');
                $currentDate->addDay();
            }

            $totalDays = count($dates);

            // Get all kepala kamar
            $kepkams = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
                ->where('user.role', 'kepkam')
                ->select('p.nama', 'p.nis')
                ->orderBy('p.nama')
                ->get();

            // Define all activities to check
            $activities = [
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
                ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
                ['model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
                ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
                ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
            ];

            // Build recap data for each kepala kamar
            $rekapData = [];
            foreach ($kepkams as $kepkam) {
                $row = [
                    'nis' => $kepkam->nis,
                    'nama' => $kepkam->nama,
                    'daily_status' => [],
                    'total' => 0,
                    'percentage' => 0
                ];

                // Check each date
                foreach ($dates as $date) {
                    $hasFilled = $this->checkKepkamActivity($kepkam->nis, $date, $activities);
                    $row['daily_status'][$date] = $hasFilled;
                    if ($hasFilled) {
                        $row['total']++;
                    }
                }

                // Calculate current percentage
                $row['percentage'] = $totalDays > 0 ? round(($row['total'] / $totalDays) * 100) : 0;

                $rekapData[] = $row;
            }

            // Load PDF view
            $pdf = Pdf::loadView('mahadiyah.rekap-kegiatan-pdf', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));

            // Set landscape orientation for better table display
            $pdf->setPaper('a4', 'landscape');

            // Download PDF
            $filename = 'Rekap_Kegiatan_KepKam_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('[PDF Download] Error generating PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // ------------------- //
    // MAIN DASHBOARD - RINGKASAN ABSENSI //
    // ------------------- //
    public function dashboard(Request $request)
    {
        // Resilient Date Parsing
        $summaryDate = $request->filled('summary_date')
            ? Carbon::parse($request->summary_date)
            : Carbon::now();
        $today = $summaryDate->format('d/m/Y');

        // Chart Date Range Logic
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        }

        // Get all kepala kamar with names
        $allKepkams = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->where('user.role', 'kepkam')
            ->select('p.nis', 'p.nama')
            ->orderBy('p.nama')
            ->get();

        $totalKepkam = $allKepkams->count();

        // Check which kepkam has filled attendance today (any activity)
        $activities = [
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2, 'label' => 'Subuh'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3, 'label' => 'Dhuhur'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4, 'label' => 'Ashar'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5, 'label' => 'Maghrib'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6, 'label' => 'Isya'],
            ['model' => 'AbsensiWaqiah', 'col' => null, 'val' => null, 'label' => 'Waqiah'],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10, 'label' => 'Ngaji Sore'],
            ['model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11, 'label' => 'Ngaji Malam'],
        ];

        $kepkamSudahAbsenCount = 0;
        $kepkamBelumAbsenCount = 0;
        $listKepkamSudah = [];
        $listKepkamBelum = [];

        foreach ($allKepkams as $kepkam) {
            $activitiesDone = [];
            $activitiesMissing = [];

            $santriNis = DB::table('santri')
                ->where('kepkam', $kepkam->nis)
                ->pluck('nis')
                ->toArray();

            foreach ($activities as $act) {
                $isFilled = false;
                if (!empty($santriNis)) {
                    $modelClass = "\\App\\Models\\{$act['model']}";
                    $query = $modelClass::where('tanggal', $today)->whereIn('nis', $santriNis);
                    if ($act['col']) {
                        $query->where($act['col'], $act['val']);
                    }
                    $isFilled = $query->exists();
                }

                if ($isFilled) {
                    $activitiesDone[] = "✅ " . $act['label'];
                } else {
                    $activitiesMissing[] = "❌ " . $act['label'];
                }
            }

            if (!empty($activitiesDone)) {
                $kepkamSudahAbsenCount++;
                $listKepkamSudah[] = [
                    'nama' => $kepkam->nama,
                    'kegiatan' => $activitiesDone
                ];
            }

            if (!empty($activitiesMissing)) {
                $kepkamBelumAbsenCount++;
                $listKepkamBelum[] = [
                    'nama' => $kepkam->nama,
                    'kegiatan' => $activitiesMissing
                ];
            }
        }

        $kepkamSudahAbsen = $kepkamSudahAbsenCount;
        $kepkamBelumAbsen = $kepkamBelumAbsenCount;

        // Collect unique santri hadir, sakit, izin, alpa today with activity details
        $listSantriHadirNis = collect();
        $listSantriSakitDetail = []; // nis => [activities]
        $listSantriIzinDetail = [];
        $listSantriAlpaDetail = [];

        $activityMap = [
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 2, 'label' => 'Subuh'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 3, 'label' => 'Dhuhur'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 4, 'label' => 'Ashar'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 5, 'label' => 'Maghrib'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 6, 'label' => 'Isya'],
            ['table' => 'absen_waqiah', 'col' => null, 'val' => null, 'label' => 'Waqiah'],
            ['table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 10, 'label' => 'Ngaji Sore'],
            ['table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 11, 'label' => 'Ngaji Malam'],
        ];

        foreach ($activityMap as $act) {
            $query = DB::table($act['table'])->where('tanggal', $today);
            if ($act['col']) {
                $query->where($act['col'], $act['val']);
            }

            // Hadir unique
            $hadirNis = (clone $query)->where('status', 'H')->pluck('nis');
            $listSantriHadirNis = $listSantriHadirNis->merge($hadirNis);

            // Sakit with activity detail
            $sakitNis = (clone $query)->where('status', 'S')->pluck('nis');
            foreach ($sakitNis as $nis) {
                $listSantriSakitDetail[$nis][] = $act['label'];
            }

            // Izin with activity detail
            $izinNis = (clone $query)->where('status', 'I')->pluck('nis');
            foreach ($izinNis as $nis) {
                $listSantriIzinDetail[$nis][] = $act['label'];
            }

            // Alpa with activity detail
            $alpaNis = (clone $query)->where('status', 'A')->pluck('nis');
            foreach ($alpaNis as $nis) {
                $listSantriAlpaDetail[$nis][] = $act['label'];
            }
        }

        // Unique counts
        $santriHadir = $listSantriHadirNis->unique()->count();
        $santriSakit = count($listSantriSakitDetail);
        $santriIzin = count($listSantriIzinDetail);
        $santriAlpa = count($listSantriAlpaDetail);

        // Helper to Group by Kepkam
        $groupByKepkam = function ($query, $isSimpleList = false, $detailsMap = []) {
            $data = $query
                ->leftJoin('pengurus', 'santri.kepkam', '=', 'pengurus.nis')
                ->select('santri.nis', 'santri.nama', 'pengurus.nama as kepkam_nama')
                ->orderBy('kepkam_nama')
                ->orderBy('santri.nama')
                ->get();

            $grouped = [];
            foreach ($data as $row) {
                $kepkamName = $row->kepkam_nama ?? 'Tanpa Kepkam';

                if ($isSimpleList) {
                    $grouped[$kepkamName][] = $row->nama;
                } else {
                    $activities = $detailsMap[$row->nis] ?? [];
                    $grouped[$kepkamName][] = [
                        'nama' => $row->nama,
                        'kegiatan' => $activities
                    ];
                }
            }
            ksort($grouped);

            $final = [];
            foreach ($grouped as $k => $list) {
                $final[] = ['kepkam' => $k, 'list' => $list];
            }
            return $final;
        };

        // Get Hadir Groups
        $hadirNisList = $listSantriHadirNis->unique();
        $listSantriHadirNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $hadirNisList),
            true,
            []
        );

        // Get Sakit Groups
        $sakitNisList = array_keys($listSantriSakitDetail);
        $listSantriSakitNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $sakitNisList),
            false,
            $listSantriSakitDetail
        );

        // Get Izin Groups
        $izinNisList = array_keys($listSantriIzinDetail);
        $listSantriIzinNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $izinNisList),
            false,
            $listSantriIzinDetail
        );

        // Get Alpa Groups
        $alpaNisList = array_keys($listSantriAlpaDetail);
        $listSantriAlpaNama = $groupByKepkam(
            DB::table('santri')->whereIn('santri.nis', $alpaNisList),
            false,
            $listSantriAlpaDetail
        );

        // Chart data: last 7 days, hadir per activity
        // Chart data: based on selected range
        $chartLabels = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $chartLabels[] = $curr->format('d/m/Y');
            $curr->addDay();
        }

        $chartActivities = [
            ['key' => 'Subuh', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 2],
            ['key' => 'Dhuhur', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 3],
            ['key' => 'Ashar', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 4],
            ['key' => 'Maghrib', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 5],
            ['key' => 'Isya', 'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 6],
            ['key' => 'Waqiah', 'table' => 'absen_waqiah', 'col' => null, 'val' => null],
            ['key' => 'Ngaji Sore', 'table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 10],
            ['key' => 'Ngaji Malam', 'table' => 'absen_ngaji', 'col' => 'ngaji', 'val' => 11],
        ];

        $chartDatasets = [];
        foreach ($chartActivities as $act) {
            $data = [];
            foreach ($chartLabels as $date) {
                $query = DB::table($act['table'])
                    ->where('tanggal', $date)
                    ->where('status', 'H');
                if ($act['col']) {
                    $query->where($act['col'], $act['val']);
                }
                $data[] = $query->count();
            }
            $chartDatasets[] = [
                'label' => $act['key'],
                'data' => $data,
            ];
        }

        // Format chart labels for display (dd MMM)
        $chartLabelsFormatted = array_map(function ($d) {
            return Carbon::createFromFormat('d/m/Y', $d)->format('d M');
        }, $chartLabels);

        // Total Santri Count
        $totalSantri = DB::table('santri')->count();

        // Date Range for Ranking (Resilient Parsing)
        $rankStartDate = $request->filled('rank_start_date')
            ? Carbon::parse($request->rank_start_date)->startOfDay()
            : $startDate->copy();
        $rankEndDate = $request->filled('rank_end_date')
            ? Carbon::parse($request->rank_end_date)->endOfDay()
            : $endDate->copy();

        $dateRangeRanking = [];
        $currRank = $rankStartDate->copy();
        while ($currRank->lte($rankEndDate)) {
            $dateRangeRanking[] = $currRank->format('d/m/Y');
            $currRank->addDay();
        }

        $topProblematic = DB::table(function ($query) use ($dateRangeRanking) {
            $query->select('nis')
                ->from('absen_jamaah')
                ->where('status', 'A')
                ->whereIn('tanggal', $dateRangeRanking)
                ->unionAll(
                    DB::table('absen_waqiah')
                        ->select('nis')
                        ->where('status', 'A')
                        ->whereIn('tanggal', $dateRangeRanking)
                )
                ->unionAll(
                    DB::table('absen_ngaji')
                        ->select('nis')
                        ->where('status', 'A')
                        ->whereIn('tanggal', $dateRangeRanking)
                );
        }, 'all_alpa')
            ->select('nis', DB::raw('count(*) as alpa_count'))
            ->groupBy('nis')
            ->orderByDesc('alpa_count')
            ->limit(5)
            ->get();

        $topRanking = [];
        foreach ($topProblematic as $tp) {
            $santri = DB::table('santri')
                ->leftJoin('pengurus', 'santri.kepkam', '=', 'pengurus.nis')
                ->where('santri.nis', $tp->nis)
                ->select('santri.nama', 'pengurus.nama as kepkam_nama')
                ->first();

            if ($santri) {
                $santriDetails = [];

                // Fetch Details from absen_jamaah
                $details_jamaah = DB::table('absen_jamaah')
                    ->where('nis', $tp->nis)
                    ->where('status', 'A')
                    ->whereIn('tanggal', $dateRangeRanking)
                    ->select('tanggal', 'sholat as val')
                    ->get();
                foreach ($details_jamaah as $d) {
                    $names = [1 => 'Subuh', 2 => 'Subuh', 3 => 'Dhuhur', 4 => 'Ashar', 5 => 'Maghrib', 6 => 'Isya'];
                    $sholatName = $names[$d->val] ?? 'Sholat';
                    $santriDetails[] = [
                        'tanggal' => $d->tanggal,
                        'kegiatan' => $sholatName,
                        'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_" . $d->val
                    ];
                }

                // Fetch Details from absen_waqiah
                $details_waqiah = DB::table('absen_waqiah')
                    ->where('nis', $tp->nis)
                    ->where('status', 'A')
                    ->whereIn('tanggal', $dateRangeRanking)
                    ->select('tanggal')
                    ->get();
                foreach ($details_waqiah as $d) {
                    $santriDetails[] = [
                        'tanggal' => $d->tanggal,
                        'kegiatan' => "Waqiah",
                        'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_7"
                    ];
                }

                // Fetch Details from absen_ngaji
                $details_ngaji = DB::table('absen_ngaji')
                    ->where('nis', $tp->nis)
                    ->where('status', 'A')
                    ->whereIn('tanggal', $dateRangeRanking)
                    ->select('tanggal', 'ngaji as val')
                    ->get();
                foreach ($details_ngaji as $d) {
                    $names = [10 => 'Ngaji Sore', 11 => 'Ngaji Malam'];
                    $ngajiName = $names[$d->val] ?? 'Ngaji';
                    $santriDetails[] = [
                        'tanggal' => $d->tanggal,
                        'kegiatan' => $ngajiName,
                        'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_" . $d->val
                    ];
                }

                // Sort details by date descending
                usort($santriDetails, function ($a, $b) {
                    return strcmp($b['raw_date'], $a['raw_date']);
                });

                // Group by Full Date (Day, Date) in Indonesian
                $grouped = [];
                Carbon::setLocale('id');
                foreach ($santriDetails as $d) {
                    $fullDate = Carbon::createFromFormat('d/m/Y', $d['tanggal'])->translatedFormat('l, d F Y');
                    if (!isset($grouped[$fullDate])) {
                        $grouped[$fullDate] = [
                            'nama' => $fullDate,
                            'kegiatan' => []
                        ];
                    }
                    $grouped[$fullDate]['kegiatan'][] = $d['kegiatan'];
                }

                $topRanking[] = [
                    'nama' => $santri->nama,
                    'kepkam' => $santri->kepkam_nama ?? 'Tanpa Kepkam',
                    'count' => $tp->alpa_count,
                    'details' => array_values($grouped)
                ];
            }
        }

        return view('mahadiyah.dashboard', compact(
            'kepkamSudahAbsen',
            'kepkamBelumAbsen',
            'santriHadir',
            'santriSakit',
            'santriIzin',
            'santriAlpa',
            'listKepkamSudah',
            'listKepkamBelum',
            'listSantriHadirNama',
            'listSantriSakitNama',
            'listSantriIzinNama',
            'listSantriAlpaNama',
            'chartLabelsFormatted',
            'chartDatasets',
            'totalKepkam',
            'totalSantri',
            'startDate',
            'endDate',
            'summaryDate',
            'topRanking',
            'rankStartDate',
            'rankEndDate'
        ));
    }
}
