<?php

namespace App\Http\Controllers\Pengasuh;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Kamar;
use App\Models\Perizinan;
use App\Models\Pelanggaran;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiNgaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PengasuhController extends Controller
{
    private function activityConfig(): array
    {
        return [
            ['id' => 'subuh',    'name' => 'Subuh',       'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => 'dhuhur',   'name' => 'Dhuhur',      'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => 'ashar',    'name' => 'Ashar',       'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => 'maghrib',  'name' => 'Maghrib',     'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => 'isya',     'name' => 'Isya',        'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => 'waqiah',   'name' => 'Waqiah',      'model' => 'AbsensiWaqiah', 'col' => null,      'val' => null],
            ['id' => 'ngasore',  'name' => 'Ngaji Malam 1', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 10],
            ['id' => 'ngamalam', 'name' => 'Ngaji Malam 2', 'model' => 'AbsensiNgaji', 'col' => 'ngaji', 'val' => 11],
        ];
    }

    private function getDateRange(Request $request): array
    {
        $period = $request->input('period', 'hari_ini');
        $now = Carbon::now();

        return match ($period) {
            'hari_ini' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'minggu_ini' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'bulan_ini' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'tahun_ini' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                $request->filled('start') ? Carbon::parse($request->start)->startOfDay() : $now->copy()->startOfMonth(),
                $request->filled('end') ? Carbon::parse($request->end)->endOfDay() : $now->copy()->endOfDay(),
            ],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    // ==================== DASHBOARD ====================
    public function index(Request $request)
    {
        $today = Carbon::now()->format('d/m/Y');
        $totalSantri = Santri::count();

        // Today's attendance summary across all activities
        $todayStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        $activityRecap = [];

        foreach ($this->activityConfig() as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::select(
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
            )->where('tanggal', $today);

            if ($act['col']) $query->where($act['col'], $act['val']);

            $row = $query->first();
            $h = (int) ($row->hadir ?? 0);
            $s = (int) ($row->sakit ?? 0);
            $i = (int) ($row->izin ?? 0);
            $a = (int) ($row->alfa ?? 0);

            $activityRecap[] = [
                'name' => $act['name'],
                'H' => $h, 'S' => $s, 'I' => $i, 'A' => $a,
            ];

            $todayStats['H'] += $h;
            $todayStats['S'] += $s;
            $todayStats['I'] += $i;
            $todayStats['A'] += $a;
        }

        // Active permits (status = 0 pending or 1 approved, not yet returned)
        $izinAktif = Perizinan::whereIn('status', [0, 1])->count();

        // Critical santri: top alfa this month
        $monthStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $monthEnd = Carbon::now()->endOfMonth()->format('d/m/Y');

        $criticalSantri = AbsensiJamaah::select(
            'absen_jamaah.nis',
            'santri.nama',
            'santri.kepkam',
            DB::raw("COUNT(CASE WHEN absen_jamaah.status = 'A' THEN 1 END) as total_alfa")
        )
            ->join('santri', 'absen_jamaah.nis', '=', 'santri.nis')
            ->whereBetween('absen_jamaah.tanggal', [$monthStart, $monthEnd])
            ->groupBy('absen_jamaah.nis', 'santri.nama', 'santri.kepkam')
            ->havingRaw("COUNT(CASE WHEN absen_jamaah.status = 'A' THEN 1 END) > 0")
            ->orderByDesc('total_alfa')
            ->limit(5)
            ->get();

        // Perizinan menunggu & terlambat
        $perizinanMenunggu = Perizinan::with(['santri', 'alasanizin', 'statusizin'])
            ->where('status', 0)
            ->limit(5)
            ->get();

        $perizinanTerlambat = Perizinan::with(['santri', 'alasanizin', 'statusizin'])
            ->where('status', 1)
            ->where('es_kembali', '<', Carbon::now())
            ->limit(5)
            ->get();

        return view('pengasuh.dashboard', compact(
            'totalSantri', 'todayStats', 'izinAktif',
            'activityRecap', 'criticalSantri',
            'perizinanMenunggu', 'perizinanTerlambat'
        ));
    }

    // ==================== REKAP ABSENSI ====================
    public function absensi(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);
        $startFmt = $start->format('d/m/Y');
        $endFmt = $end->format('d/m/Y');

        $activities = collect($this->activityConfig());
        $activeTab = $request->input('tab', 'subuh');

        // Get dates in range
        $dates = collect();
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dates->push($cursor->format('d/m/Y'));
            $cursor->addDay();
        }

        // Get active activity config
        $actConfig = $activities->firstWhere('id', $activeTab) ?? $activities->first();

        // Get all kamar with kepkam
        $kamarList = Kamar::with(['kepkam', 'asrama'])->get();

        // Build rekap per kamar
        $modelClass = "\\App\\Models\\{$actConfig['model']}";
        $rekapKamar = [];
        $sumH = 0; $sumS = 0; $sumI = 0; $sumA = 0;

        foreach ($kamarList as $kamar) {
            $query = $modelClass::select(
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa"),
                DB::raw("COUNT(*) as total")
            )
                ->whereHas('santri', fn($q) => $q->where('kepkam', $kamar->kepkam_nis))
                ->whereBetween('tanggal', [$startFmt, $endFmt]);

            if ($actConfig['col']) $query->where($actConfig['col'], $actConfig['val']);

            $row = $query->first();
            $h = (int) ($row->hadir ?? 0);
            $s = (int) ($row->sakit ?? 0);
            $i = (int) ($row->izin ?? 0);
            $a = (int) ($row->alfa ?? 0);
            $total = (int) ($row->total ?? 0);

            if ($total > 0) {
                $rekapKamar[] = [
                    'kamar' => $kamar->nama,
                    'asrama' => $kamar->asrama->nama ?? '-',
                    'kepkam' => $kamar->kepkam->nama ?? '-',
                    'total' => $total,
                    'H' => $h, 'S' => $s, 'I' => $i, 'A' => $a,
                ];
                $sumH += $h; $sumS += $s; $sumI += $i; $sumA += $a;
            }
        }

        return view('pengasuh.absensi', compact(
            'rekapKamar', 'activities', 'activeTab', 'actConfig',
            'sumH', 'sumS', 'sumI', 'sumA', 'startFmt', 'endFmt'
        ));
    }

    // ==================== PEMBAYARAN (Coming Soon) ====================
    public function pembayaran()
    {
        return view('pengasuh.pembayaran');
    }

    // ==================== PERIZINAN ====================
    public function perizinan(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);

        $query = Perizinan::with(['santri', 'alasanizin', 'statusizin'])
            ->whereBetween('berangkat', [$start->format('Y-m-d 00:00:00'), $end->format('Y-m-d 23:59:59')]);

        $statusFilter = $request->input('status');
        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', (int) $statusFilter);
        }

        $search = $request->input('search');
        if ($search) {
            $query->whereHas('santri', fn($q) => $q->where('nama', 'like', "%{$search}%"));
        }

        $perizinanList = $query->orderByDesc('berangkat')->get();

        // Counts
        $countAll = Perizinan::whereBetween('berangkat', [$start->format('Y-m-d 00:00:00'), $end->format('Y-m-d 23:59:59')])->count();
        $countMenunggu = Perizinan::whereBetween('berangkat', [$start->format('Y-m-d 00:00:00'), $end->format('Y-m-d 23:59:59')])->where('status', 0)->count();
        $countDisetujui = Perizinan::whereBetween('berangkat', [$start->format('Y-m-d 00:00:00'), $end->format('Y-m-d 23:59:59')])->where('status', 1)->count();
        $countKembali = Perizinan::whereBetween('berangkat', [$start->format('Y-m-d 00:00:00'), $end->format('Y-m-d 23:59:59')])->where('status', 2)->count();

        return view('pengasuh.perizinan', compact(
            'perizinanList', 'countAll', 'countMenunggu', 'countDisetujui', 'countKembali'
        ));
    }

    // Approve/reject perizinan
    public function perizinanAction(Request $request, $nis)
    {
        $request->validate(['action' => 'required|in:approve,reject,kembali']);

        $perizinan = Perizinan::where('nis', $nis)->firstOrFail();

        match ($request->action) {
            'approve' => $perizinan->update(['accpengasuh' => Carbon::now(), 'status' => 1]),
            'reject' => $perizinan->update(['accpengasuh' => Carbon::now(), 'status' => 3]),
            'kembali' => $perizinan->update(['laporpengasuh' => Carbon::now(), 'status' => 2]),
        };

        return redirect()->back()->with('success', 'Status perizinan berhasil diperbarui.');
    }

    // ==================== TRACK RECORD ====================
    public function trackRecord(Request $request)
    {
        return view('pengasuh.track-record');
    }

    // API: Search santri for track record
    public function searchSantri(Request $request)
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $results = Santri::select('nis', 'nama', 'kepkam')
            ->where('nama', 'like', "%{$q}%")
            ->orWhere('nis', 'like', "%{$q}%")
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    // API: Get santri track record detail
    public function santriDetail(Request $request, $nis)
    {
        $santri = Santri::where('nis', $nis)->firstOrFail();

        // Get kamar info
        $kamar = Kamar::where('kepkam_nis', $santri->kepkam)->with('asrama')->first();

        // Pelanggaran with larangan detail
        $pelanggaran = Pelanggaran::where('nis', $nis)
            ->with('larangan')
            ->get()
            ->map(fn($p) => [
                'desc' => $p->larangan->nama ?? 'Pelanggaran',
                'poin' => $p->larangan->poin ?? 0,
                'hukuman' => $p->hukuman,
            ]);

        // Count alfa from absen_jamaah
        $totalAlfa = AbsensiJamaah::where('nis', $nis)->where('status', 'A')->count();

        // Count total kehadiran
        $totalAbsen = AbsensiJamaah::where('nis', $nis)->count();
        $totalHadir = AbsensiJamaah::where('nis', $nis)->where('status', 'H')->count();
        $kehadiran = $totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100) : 100;

        // Calculate poin (start from 100, subtract pelanggaran)
        $totalPoinPelanggaran = $pelanggaran->sum('poin');
        $sisaPoin = 100 - $totalPoinPelanggaran;

        return response()->json([
            'nis' => $santri->nis,
            'nama' => $santri->nama,
            'kamar' => $kamar->nama ?? '-',
            'asrama' => $kamar->asrama->nama ?? '-',
            'kepkam' => $kamar->kepkam->nama ?? '-',
            'stats' => [
                'pelanggaran' => $pelanggaran->count(),
                'prestasi' => 0, // Coming Soon
                'alfa' => $totalAlfa,
                'poin' => $sisaPoin,
                'kehadiran' => $kehadiran,
            ],
            'pelanggaran' => $pelanggaran,
            'prestasi' => [], // Coming Soon
        ]);
    }
}
