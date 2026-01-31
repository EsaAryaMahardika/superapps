<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Kegiatan;
use App\Models\Larangan;
use App\Models\AbsensiNgaji;
use Illuminate\Http\Request;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiMingguan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class KepalaKamar extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function dashboard()
    {
        // Generate last 7 days
        $dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $dates->push(Carbon::now()->subDays($i)->format('d/m/Y'));
        }

        $absen_kegiatan = function ($model, $kegiatan, $value) use ($dates) {
            $modelClass = "\\App\\Models\\$model";
            $data = $modelClass::select(
                'tanggal',
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
            )
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                })
                ->where($kegiatan, $value)
                ->groupBy('tanggal')
                ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') desc")
                ->limit(7)
                ->get()
                ->keyBy('tanggal');

            // Map over the last 7 days and fill missing data
            return $dates->map(function ($date) use ($data) {
                if ($data->has($date)) {
                    $item = $data->get($date);
                    $item->is_filled = true;
                    return $item;
                } else {
                    return (object) [
                        'tanggal' => $date,
                        'hadir' => '-',
                        'sakit' => '-',
                        'izin' => '-',
                        'alfa' => '-',
                        'is_filled' => false
                    ];
                }
            });
        };

        $subuh = $absen_kegiatan('AbsensiJamaah', 'sholat', 2);
        $dhuhur = $absen_kegiatan('AbsensiJamaah', 'sholat', 3);
        $ashar = $absen_kegiatan('AbsensiJamaah', 'sholat', 4);
        $maghrib = $absen_kegiatan('AbsensiJamaah', 'sholat', 5);
        $isya = $absen_kegiatan('AbsensiJamaah', 'sholat', 6);
        $ngasore = $absen_kegiatan('AbsensiNgaji', 'ngaji', 10);
        $ngamalam = $absen_kegiatan('AbsensiNgaji', 'ngaji', 11);

        $waqiahData = AbsensiWaqiah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
            ->whereHas('santri', function ($q) {
                $q->where('kepkam', $this->user->username);
            })
            ->groupBy('tanggal')
            ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') desc")
            ->limit(7)
            ->get()
            ->keyBy('tanggal');

        $waqiah = $dates->map(function ($date) use ($waqiahData) {
            if ($waqiahData->has($date)) {
                $item = $waqiahData->get($date);
                $item->is_filled = true;
                return $item;
            } else {
                return (object) [
                    'tanggal' => $date,
                    'hadir' => '-',
                    'sakit' => '-',
                    'izin' => '-',
                    'alfa' => '-',
                    'is_filled' => false
                ];
            }
        });

        return view('kepkam.dashboard', compact(
            'waqiah',
            'subuh',
            'dhuhur',
            'ashar',
            'maghrib',
            'isya',
            'ngasore',
            'ngamalam'
        ));
    }
    // ---------------- //
    // ABSENSI KEGIATAN //
    // ---------------- //
    public function absensi()
    {
        $today = Carbon::now()->format('d/m/Y');

        // Define all activities configuration
        // ID corresponds to the value used in the form select and match expression
        $activities = [
            ['id' => '1', 'title' => 'Absensi Waqiah', 'model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['id' => '2', 'title' => 'Absensi Subuh', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => '3', 'title' => 'Absensi Dhuhur', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => '4', 'title' => 'Absensi Ashar', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => '5', 'title' => 'Absensi Maghrib', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => '6', 'title' => 'Absensi Isya', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => '10', 'title' => 'Absensi Ngaji Sore', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['id' => '11', 'title' => 'Absensi Ngaji Malam', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        $completed = [];
        $pending = [];

        foreach ($activities as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $today)
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                })
                ->with('santri');

            if ($act['col']) {
                $query->where($act['col'], $act['val']);
            }

            $data = $query->get();

            if ($data->isNotEmpty()) {
                // Prepare mapped statuses for easier consumption by Edit JS
                $statuses = [];
                foreach ($data as $d) {
                    $statuses[$d->nis] = $d->status;
                }

                $completed[] = [
                    'id' => $act['id'],
                    'title' => $act['title'],
                    'data' => $data,
                    'statuses' => $statuses
                ];
            } else {
                $pending[] = [
                    'id' => $act['id'],
                    'title' => $act['title']
                ];
            }
        }

        $kegiatan = Kegiatan::where('ket', 'S')->get();
        $santri = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();

        $completedIds = array_column($completed, 'id');

        return view('kepkam.absensi', compact('completed', 'pending', 'kegiatan', 'santri', 'today', 'activities', 'completedIds'));
    }
    public function absen(Request $request)
    {
        $tanggal = Carbon::parse($request->tanggal)->format('d/m/Y');

        // Target activities: Defaults to just the selected one
        $targetActivities = [$request->kegiatan];

        // Merge with any selected additional activities
        if ($request->has('additional_activities') && is_array($request->additional_activities)) {
            $targetActivities = array_merge($targetActivities, $request->additional_activities);
            // Remove duplicates just in case User selected the current activity in the list too
            $targetActivities = array_unique($targetActivities);
        }

        foreach ($targetActivities as $actId) {
            foreach ($request->santri as $nis => $status) {
                // Optimization: Don't check Santri::where every loop if possible, 
                // but for safety in this existing codebase structure, we keep it simple.
                // Or better: we assume NIS is valid since it comes from the form of valid santris.

                match ($actId) {
                    '1' => AbsensiWaqiah::updateOrCreate(
                        ['nis' => (string) $nis, 'tanggal' => $tanggal],
                        ['status' => $status]
                    ),
                    '2', '3', '4', '5', '6' => AbsensiJamaah::updateOrCreate(
                        ['nis' => (string) $nis, 'tanggal' => $tanggal, 'sholat' => $actId],
                        ['status' => $status]
                    ),
                    '10', '11' => AbsensiNgaji::updateOrCreate(
                        ['nis' => (string) $nis, 'tanggal' => $tanggal, 'ngaji' => $actId],
                        ['status' => $status]
                    ),
                    default => null
                };
            }
        }

        $msg = count($targetActivities) > 1 ? 'Absensi berhasil disimpan untuk ' . count($targetActivities) . ' kegiatan' : 'Absensi berhasil disimpan';
        session()->flash('success', $msg);
        return redirect('/kepkam/absensi');
    }

    public function checkCompleted(Request $request)
    {
        // Convert Y-m-d to d/m/Y format
        $date = Carbon::parse($request->date)->format('d/m/Y');

        $activities = [
            ['id' => '1', 'model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['id' => '2', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => '3', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => '4', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => '5', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => '6', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => '10', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['id' => '11', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        $completedIds = [];

        foreach ($activities as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::where('tanggal', $date)
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                });

            if ($act['col']) {
                $query->where($act['col'], $act['val']);
            }

            if ($query->exists()) {
                $completedIds[] = $act['id'];
            }
        }

        return response()->json(['completedIds' => $completedIds]);
    }

    public function hapusAbsen($id)
    {
        $today = Carbon::now()->format('d/m/Y');

        try {
            match ($id) {
                '1' => AbsensiWaqiah::where('tanggal', $today)
                    ->whereHas('santri', function ($q) {
                            $q->where('kepkam', $this->user->username);
                        })
                    ->delete(),
                '2', '3', '4', '5', '6' => AbsensiJamaah::where('tanggal', $today)
                    ->where('sholat', $id)
                    ->whereHas('santri', function ($q) {
                            $q->where('kepkam', $this->user->username);
                        })
                    ->delete(),
                '10', '11' => AbsensiNgaji::where('tanggal', $today)
                    ->where('ngaji', $id)
                    ->whereHas('santri', function ($q) {
                            $q->where('kepkam', $this->user->username);
                        })
                    ->delete(),
                default => null
            };

            session()->flash('success', 'Absensi berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus absensi');
        }

        return redirect('/kepkam/absensi');
    }
    // ---------------- //
    // ABSENSI MINGGUAN //
    // ---------------- //
    public function mingguan()
    {
        $santri = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();
        $larangan = Larangan::select('id', 'nama')->where('ket', 'K')->get();
        $mingguan = AbsensiMingguan::whereHas('santri', function ($q) {
            $q->where('kepkam', $this->user->username);
        })->with('santri', 'larangan')->get();
        return view('kepkam.mingguan', compact('larangan', 'mingguan', 'santri'));
    }

    // ------------------- //
    // REKAPAN HARIAN      //
    // ------------------- //
    public function rekapHarian(Request $request)
    {
        // Get selected date or default to today
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->tanggal)->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');

        // Get all santri under this kepkam
        $santriList = Santri::select('nis', 'nama')
            ->where('kepkam', $this->user->username)
            ->orderBy('nama')
            ->get();

        // Define activities
        $activities = [
            ['name' => 'Subuh', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['name' => 'Dhuhur', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['name' => 'Ashar', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['name' => 'Maghrib', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['name' => 'Isya', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['name' => 'Waqiah', 'model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['name' => 'Ngaji Sore', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['name' => 'Ngaji Malam', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        // Fetch attendance data for each activity
        $attendanceData = [];
        foreach ($activities as $activity) {
            $modelClass = "\\App\\Models\\{$activity['model']}";
            $query = $modelClass::where('tanggal', $tanggal)
                ->whereHas('santri', function ($q) {
                    $q->where('kepkam', $this->user->username);
                });

            if ($activity['col']) {
                $query->where($activity['col'], $activity['val']);
            }

            $records = $query->get()->keyBy('nis');
            $attendanceData[$activity['name']] = $records;
        }

        // Build recap data
        $rekapData = [];
        foreach ($santriList as $santri) {
            $row = [
                'nis' => $santri->nis,
                'nama' => $santri->nama,
                'attendance' => []
            ];

            foreach ($activities as $activity) {
                $status = '-';
                if (isset($attendanceData[$activity['name']][$santri->nis])) {
                    $status = $attendanceData[$activity['name']][$santri->nis]->status;
                }
                $row['attendance'][$activity['name']] = $status;
            }

            $rekapData[] = $row;
        }

        // Get kepala kamar name from pengurus relation
        $kepalaKamar = $this->user->pengurus->nama;

        return view('kepkam.rekap-harian', compact('rekapData', 'activities', 'tanggal', 'kepalaKamar'));
    }

    public function downloadRekapHarian(Request $request)
    {
        // Get selected date or default to today
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->tanggal)->format('d/m/Y')
            : Carbon::now()->format('d/m/Y');

        // Get all santri under this kepkam
        $santriList = Santri::select('nis', 'nama')
            ->where('kepkam', $this->user->username)
            ->orderBy('nama')
            ->get();

        // Define activities (matching the rekapHarian method)
        $activities = [
            ['name' => 'Subuh', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['name' => 'Dhuhur', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['name' => 'Ashar', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['name' => 'Maghrib', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['name' => 'Isya', 'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['name' => 'Waqiah', 'model' => 'AbsensiWaqiah', 'col' => null, 'val' => null],
            ['name' => 'Ngaji Sore', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['name' => 'Ngaji Malam', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];

        $rekapData = [];
        foreach ($santriList as $index => $santri) {
            $row = [
                'no' => $index + 1,
                'nis' => $santri->nis,
                'nama' => $santri->nama,
                'attendance' => []
            ];

            foreach ($activities as $activity) {
                $modelClass = "App\\Models\\" . $activity['model'];
                $query = $modelClass::where('nis', $santri->nis)
                    ->where('tanggal', $tanggal);

                if ($activity['col'] !== null) {
                    $query->where($activity['col'], $activity['val']);
                }

                $record = $query->first();
                $status = $record ? $record->status : '-';
                $row['attendance'][$activity['name']] = $status;
            }

            $rekapData[] = $row;
        }

        // Get kepala kamar name
        $kepalaKamar = $this->user->pengurus->nama;

        // Debug: check data
        // dd(compact('rekapData', 'activities', 'tanggal', 'kepalaKamar'));

        // Load PDF view
        $pdf = Pdf::loadView('kepkam.rekap-harian-pdf', compact('rekapData', 'activities', 'tanggal', 'kepalaKamar'));

        // Set paper size to Legal (longer than A4) to fit all data in one page
        $pdf->setPaper('legal', 'portrait');

        // Download PDF
        $filename = 'Rekap_Harian_' . str_replace('/', '-', $tanggal) . '.pdf';
        return $pdf->download($filename);
    }

    public function i_mingguan(Request $request)
    {
        $tanggal = Carbon::createFromFormat('d-m-Y', $request->tanggal)->format('d/m/Y');
        $larangan = $request->larangan;
        foreach ($request->santri as $nis) {
            $santri = Santri::where('nis', (string) $nis)->first();
            if ($santri) {
                AbsensiMingguan::create(['nis' => (string) $nis, 'pelanggaran' => $larangan, 'tanggal' => $tanggal]);
            }
        }
        session()->flash('success', 'Absensi berhasil disimpan');
        return redirect('/kepkam/mingguan');
    }
}
