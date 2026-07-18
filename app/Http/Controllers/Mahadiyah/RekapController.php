<?php

namespace App\Http\Controllers\Mahadiyah;

use Carbon\Carbon;
use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RekapAbsensiPengurusExport;
use App\Models\LiburPengurus;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class RekapController extends Controller
{
    private function getActivities(): array
    {
        return [
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['model' => 'AbsensiWaqiah', 'col' => null,     'val' => null],
            ['model' => 'AbsensiNgaji',  'col' => 'ngaji',  'val' => 10],
            ['model' => 'AbsensiNgaji',  'col' => 'ngaji',  'val' => 11],
        ];
    }

    private function checkKepkamActivity($kepkamNis, $tanggal, $activities): bool
    {
        $santriNis = DB::table('santri')->where('kepkam', $kepkamNis)->pluck('nis')->toArray();
        if (empty($santriNis)) return false;

        foreach ($activities as $activity) {
            $modelClass = "\\App\\Models\\{$activity['model']}";
            $query = $modelClass::where('tanggal', $tanggal)->whereIn('nis', $santriNis);
            if ($activity['col']) $query->where($activity['col'], $activity['val']);
            if ($query->exists()) return true;
        }

        return false;
    }

    private function getKepkams()
    {
        return User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->where('user.role', 'kepkam')
            ->select('p.nama', 'p.nis')
            ->orderBy('p.nama')
            ->get();
    }

    private function buildDateRange($startDate, $endDate, string $format = 'd/m/Y'): array
    {
        $dates = [];
        $curr  = $startDate->copy();
        while ($curr <= $endDate) {
            $dates[] = $curr->format($format);
            $curr->addDay();
        }
        return $dates;
    }

    private function buildRekapKepkam($kepkams, array $dates, array $activities): array
    {
        $totalDays = count($dates);
        $rekapData = [];

        foreach ($kepkams as $kepkam) {
            $row = ['nis' => $kepkam->nis, 'nama' => $kepkam->nama, 'daily_status' => [], 'total' => 0, 'percentage' => 0];
            foreach ($dates as $date) {
                $filled = $this->checkKepkamActivity($kepkam->nis, $date, $activities);
                $row['daily_status'][$date] = $filled;
                if ($filled) $row['total']++;
            }
            $row['percentage'] = $totalDays > 0 ? round(($row['total'] / $totalDays) * 100) : 0;
            $rekapData[] = $row;
        }

        return $rekapData;
    }

    // ------------------- //
    // REKAP KEGIATAN       //
    // ------------------- //
    public function rekapKegiatan(Request $request)
    {
        $endDate   = $request->input('end_date')   ? Carbon::parse($request->end_date)   : Carbon::now();
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(6);

        $dates      = $this->buildDateRange($startDate, $endDate);
        $totalDays  = count($dates);
        $kepkams    = $this->getKepkams();
        $activities = $this->getActivities();
        $rekapData  = $this->buildRekapKepkam($kepkams, $dates, $activities);

        return view('mahadiyah.rekap-kegiatan', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));
    }

    public function downloadRekapKegiatan(Request $request)
    {
        try {
            $endDate   = $request->input('end_date')   ? Carbon::parse($request->end_date)   : Carbon::now();
            $startDate = $request->input('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(6);

            $dates      = $this->buildDateRange($startDate, $endDate);
            $totalDays  = count($dates);
            $kepkams    = $this->getKepkams();
            $activities = $this->getActivities();
            $rekapData  = $this->buildRekapKepkam($kepkams, $dates, $activities);

            $pdf = Pdf::loadView('mahadiyah.rekap-kegiatan-pdf', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays'));
            $pdf->setPaper('a4', 'landscape');

            $filename = 'Rekap_Kegiatan_KepKam_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('[PDF Download] Error generating PDF', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    // ------------------- //
    // REKAP ABSENSI PENGURUS //
    // ------------------- //
    public function rekapAbsensiPengurus(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfWeek();
        $endDate   = $request->input('end_date')   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfWeek();

        $dates     = $this->buildDateRange($startDate, $endDate, 'd-m-Y');
        $totalDays = count($dates);

        [$pengurusList, $nisList, $bandonganData, $wiridData, $yasinanData, $liburData] = $this->loadPengurusAbsensiData($dates);

        $rekapData         = $this->buildRekapPengurus($pengurusList, $nisList, $dates, $bandonganData, $wiridData, $yasinanData, $liburData);
        $dailySummary      = $this->buildDailySummaryByTipe($dates, $rekapData);

        return view('mahadiyah.rekap-absensi-pengurus', compact('rekapData', 'dates', 'startDate', 'endDate', 'totalDays', 'dailySummary'));
    }

    public function downloadRekapAbsensiPengurus(Request $request)
    {
        try {
            $tipe      = $request->input('tipe', 'all');
            $startDate = $request->input('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfWeek();
            $endDate   = $request->input('end_date')   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfWeek();

            $dates     = $this->buildDateRange($startDate, $endDate, 'd-m-Y');
            $totalDays = count($dates);

            [$pengurusList, $nisList, $bandonganData, $wiridData, $yasinanData, $liburData] = $this->loadPengurusAbsensiData($dates);
            $rekapData    = $this->buildRekapPengurus($pengurusList, $nisList, $dates, $bandonganData, $wiridData, $yasinanData, $liburData);
            $dailySummary = $this->buildDailySummaryByTipe($dates, $rekapData);

            // Filter rekapData sesuai tipe
            if ($tipe !== 'all') {
                $rekapData = array_values(array_filter($rekapData, fn($r) => $r['tipe'] === $tipe));
            }

            $pdf = Pdf::loadView('mahadiyah.rekap-absensi-pengurus-pdf', compact(
                'rekapData', 'dates', 'startDate', 'endDate', 'totalDays', 'tipe', 'dailySummary'
            ));
            $pdf->setPaper('a4', 'landscape');

            $filename = 'Rekap_Absensi_Pengurus_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('[PDF Download] Error generating PDF', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Gagal membuat PDF: ' . $e->getMessage()], 500);
        }
    }

    public function excelRekapAbsensiPengurus(Request $request)
    {
        $tipe      = $request->input('tipe', 'all');
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfWeek();
        $endDate   = $request->input('end_date')   ? Carbon::parse($request->end_date)   : Carbon::now()->endOfWeek();

        $dates    = $this->buildDateRange($startDate, $endDate, 'd-m-Y');
        $periode  = $startDate->format('d-m-Y') . ' sd ' . $endDate->format('d-m-Y');

        [$pengurusList, $nisList, $bandonganData, $wiridData, $yasinanData, $liburData] = $this->loadPengurusAbsensiData($dates);
        $rekapData = $this->buildRekapPengurus($pengurusList, $nisList, $dates, $bandonganData, $wiridData, $yasinanData, $liburData);

        $filename = 'Rekap_Absensi_Pengurus_' . $startDate->format('d-m-Y') . '_to_' . $endDate->format('d-m-Y') . '.xlsx';

        return Excel::download(
            new RekapAbsensiPengurusExport($rekapData, $dates, $tipe, $periode),
            $filename
        );
    }

    private function loadPengurusAbsensiData(array $dates): array
    {
        $pengurusList = Pengurus::with(['jabatan.divisi'])->get()->sortBy(function ($p) {
            $tipe  = $p->jabatan->divisi->tipe ?? 'z_none';
            $order = $tipe === 'kepkam' ? 0 : 1;
            return sprintf('%d_%s_%s_%s', $order, $p->jabatan->divisi->nama ?? 'z', $p->jabatan->nama ?? 'z', $p->nama);
        });

        $nisList = $pengurusList->pluck('nis')->toArray();

        // Load data libur untuk range tanggal ini
        $liburData = LiburPengurus::forDateRange($dates);

        return [
            $pengurusList,
            $nisList,
            DB::table('bandongan')->whereIn('tanggal', $dates)->whereIn('nis', $nisList)->get()->groupBy('nis'),
            DB::table('wirid')->whereIn('tanggal', $dates)->whereIn('nis', $nisList)->get()->groupBy('nis'),
            DB::table('yasinan')->whereIn('tanggal', $dates)->whereIn('nis', $nisList)->get()->groupBy('nis'),
            $liburData,
        ];
    }

    private function buildDailySummaryByTipe(array $dates, array $rekapData): array
    {
        $tipes = ['all', 'kepkam', 'non'];
        $summary = [];

        foreach ($tipes as $tipe) {
            foreach ($dates as $date) {
                $bH = $bT = $wH = $wT = $yH = $yT = 0;

                foreach ($rekapData as $row) {
                    if ($tipe !== 'all' && $row['tipe'] !== $tipe) continue;
                    $att = $row['attendance'][$date] ?? [];

                    $bStatus = $att['bandongan'] ?? null;
                    $wStatus = $att['wirid']     ?? null;
                    $yStatus = $att['yasinan']   ?? null;

                    // Skip status L (libur) — tidak masuk hitungan total
                    if (!is_null($bStatus) && $bStatus !== 'L') {
                        $bT++;
                        if ($bStatus === 'H') $bH++;
                    }
                    if (!is_null($wStatus) && $wStatus !== 'L') {
                        $wT++;
                        if ($wStatus === 'H') $wH++;
                    }
                    if (!is_null($yStatus) && $yStatus !== 'L') {
                        $yT++;
                        if ($yStatus === 'H') $yH++;
                    }
                }

                $summary[$tipe][$date] = [
                    'bandongan' => $bH, 'bandongan_total' => $bT,
                    'wirid'     => $wH, 'wirid_total'     => $wT,
                    'yasinan'   => $yH, 'yasinan_total'   => $yT,
                ];
            }
        }

        return $summary;
    }

    private function buildDailySummary(array $dates, $bandonganData, $wiridData, $yasinanData): array
    {
        $summary = [];
        foreach ($dates as $date) {
            $bHadir = 0; $bTotal = 0;
            $wHadir = 0; $wTotal = 0;
            $yHadir = 0; $yTotal = 0;

            foreach ($bandonganData as $records) {
                $rec = $records->firstWhere('tanggal', $date);
                if ($rec) { $bTotal++; if ($rec->status === 'H') $bHadir++; }
            }
            foreach ($wiridData as $records) {
                $rec = $records->firstWhere('tanggal', $date);
                if ($rec) { $wTotal++; if ($rec->status === 'H') $wHadir++; }
            }
            foreach ($yasinanData as $records) {
                $rec = $records->firstWhere('tanggal', $date);
                if ($rec) { $yTotal++; if ($rec->status === 'H') $yHadir++; }
            }

            $summary[$date] = [
                'bandongan' => $bHadir, 'bandongan_total' => $bTotal,
                'wirid'     => $wHadir, 'wirid_total'     => $wTotal,
                'yasinan'   => $yHadir, 'yasinan_total'   => $yTotal,
            ];
        }
        return $summary;
    }

    private function buildRekapPengurus($pengurusList, $nisList, array $dates, $bandonganData, $wiridData, $yasinanData, array $liburData = []): array
    {
        $rekapData = [];

        foreach ($pengurusList as $p) {
            $tipe = $p->jabatan->divisi->tipe ?? 'non';
            $row  = [
                'pengurus'   => $p,
                'tipe'       => $tipe,
                'nama'       => $p->nama,
                'jabatan'    => $p->jabatan->nama ?? '-',
                'divisi'     => $p->jabatan->divisi->nama ?? '-',
                'attendance' => [],
                'summary'    => [
                    'bandongan' => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                    'wirid'     => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                    'yasinan'   => ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'total' => 0],
                ],
            ];

            $pB = isset($bandonganData[$p->nis]) ? $bandonganData[$p->nis]->keyBy('tanggal') : collect();
            $pW = isset($wiridData[$p->nis])     ? $wiridData[$p->nis]->keyBy('tanggal')     : collect();
            $pY = isset($yasinanData[$p->nis])   ? $yasinanData[$p->nis]->keyBy('tanggal')   : collect();

            foreach ($dates as $date) {
                // Cek libur per kegiatan — jika libur, status = 'L' (tidak masuk hitungan)
                $bIsLibur = in_array($date, $liburData['bandongan'] ?? []);
                $wIsLibur = in_array($date, $liburData['wirid']     ?? []);
                $yIsLibur = in_array($date, $liburData['yasinan']   ?? []);

                $bStatus = $bIsLibur ? 'L' : ($pB->get($date)->status ?? null);
                $wStatus = $wIsLibur ? 'L' : ($pW->get($date)->status ?? null);
                $yStatus = ($tipe === 'kepkam') ? null
                    : ($yIsLibur ? 'L' : ($pY->get($date)->status ?? null));

                $row['attendance'][$date] = ['bandongan' => $bStatus, 'wirid' => $wStatus, 'yasinan' => $yStatus];

                // Hanya hitung jika bukan libur
                if ($bStatus && $bStatus !== 'L') { $row['summary']['bandongan'][$bStatus]++; $row['summary']['bandongan']['total']++; }
                if ($wStatus && $wStatus !== 'L') { $row['summary']['wirid'][$wStatus]++;     $row['summary']['wirid']['total']++; }
                if ($yStatus && $yStatus !== 'L') { $row['summary']['yasinan'][$yStatus]++;   $row['summary']['yasinan']['total']++; }
            }

            $rekapData[] = $row;
        }

        return $rekapData;
    }
}
