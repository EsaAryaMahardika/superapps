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
    public function dashboard(Request $request)
    {
        $waqiah = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->leftJoin('santri as s', 's.kepkam', '=', 'p.nis')
            ->leftJoin('absen_waqiah as w', function ($join) {
                $join->on('w.nis', '=', 's.nis');
            })
            ->select(
                'p.nama',
                'p.nis',
                DB::raw("STR_TO_DATE(w.tanggal, '%d/%m/%Y') as tanggal"),
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'H' THEN 1 ELSE 0 END), 0) AS hadir"),
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'S' THEN 1 ELSE 0 END), 0) AS sakit"),
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'I' THEN 1 ELSE 0 END), 0) AS izin"),
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'A' THEN 1 ELSE 0 END), 0) AS alfa")
            )
            ->where('user.role', 's.kepkam')
            ->groupBy('p.nis', 'p.nama', 'w.tanggal')
            ->get();
        $absensi = function ($tabel, $kegiatan, $value) {
            $result = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
                ->leftJoin('santri as s', 's.kepkam', '=', 'p.nis')
                ->leftJoin($tabel . ' as k', function ($join) use ($kegiatan, $value) {
                    $join->on('k.nis', '=', 's.nis')
                        ->where("k.$kegiatan", $value);
                })
                ->select(
                    'p.nama',
                    'p.nis',
                    DB::raw("STR_TO_DATE(k.tanggal, '%d/%m/%Y') as tanggal"),
                    DB::raw("COALESCE(SUM(CASE WHEN k.status = 'H' THEN 1 ELSE 0 END), 0) AS hadir"),
                    DB::raw("COALESCE(SUM(CASE WHEN k.status = 'S' THEN 1 ELSE 0 END), 0) AS sakit"),
                    DB::raw("COALESCE(SUM(CASE WHEN k.status = 'I' THEN 1 ELSE 0 END), 0) AS izin"),
                    DB::raw("COALESCE(SUM(CASE WHEN k.status = 'A' THEN 1 ELSE 0 END), 0) AS alfa")
                )
                ->where('user.role', 'kepkam')
                ->groupBy('p.nis', 'p.nama', 'k.tanggal')
                ->get();
            return $result;
        };
        $subuh = $absensi('absen_jamaah', 'sholat', 2);
        $dhuhur = $absensi('absen_jamaah', 'sholat', 3);
        $ashar = $absensi('absen_jamaah', 'sholat', 4);
        $maghrib = $absensi('absen_jamaah', 'sholat', 5);
        $isya = $absensi('absen_jamaah', 'sholat', 6);
        $ngasore = $absensi('absen_ngaji', 'ngaji', 10);
        $ngamalam = $absensi('absen_ngaji', 'ngaji', 11);

        $kepkams = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->leftJoin('santri as s', 's.kepkam', '=', 'p.nis')
            ->where('user.role', 'kepkam')
            ->select('p.nama', 'p.nis', DB::raw('COUNT(s.nis) as jml_santri'))
            ->groupBy('p.nama', 'p.nis')
            ->orderBy('p.nama')
            ->get();
        return view('mahadiyah.dashboard', compact(
            'waqiah',
            'subuh',
            'dhuhur',
            'ashar',
            'maghrib',
            'isya',
            'ngasore',
            'ngamalam',
            'kepkams'
        ));
    }
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
}
