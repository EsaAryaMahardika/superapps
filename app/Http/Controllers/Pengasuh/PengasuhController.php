<?php

namespace App\Http\Controllers\Pengasuh;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Pengurus;
use App\Models\Kamar;
use App\Models\Perizinan;
use App\Models\Pelanggaran;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiNgaji;
use App\Models\Bandongan;
use App\Models\Wirid;
use App\Models\Yasinan;
use App\Support\DemoPengasuh;
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

    /** Rekap status absensi hari ini per kegiatan. */
    private function buildActivityRecap(string $tanggal): array
    {
        $recap = [];

        foreach ($this->activityConfig() as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::select(
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
            )->where('tanggal', $tanggal);

            if ($act['col']) $query->where($act['col'], $act['val']);

            $row = $query->first();

            $recap[] = [
                'name' => $act['name'],
                'H' => (int) ($row->hadir ?? 0),
                'S' => (int) ($row->sakit ?? 0),
                'I' => (int) ($row->izin ?? 0),
                'A' => (int) ($row->alfa ?? 0),
            ];
        }

        return $recap;
    }

    /** Santri dengan alfa terbanyak bulan ini. */
    private function buildCriticalSantri()
    {
        $monthStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $monthEnd   = Carbon::now()->endOfMonth()->format('d/m/Y');

        return AbsensiJamaah::select(
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
    }

    // ==================== DASHBOARD ====================
    // Dashboard hanya menampilkan ringkasan + grid menu; detailnya ada di
    // masing-masing halaman.
    public function index(Request $request)
    {
        return view('pengasuh.dashboard', [
            'totalSantri'        => Santri::count(),
            'totalPengurusDalam' => Pengurus::where('asal', 'dalam')->count(),
            'totalPengurusLuar'  => Pengurus::where('asal', 'luar')->count(),
        ]);
    }

    // ==================== KESEHATAN / INFRASTRUKTUR / LOGISTIK ====================
    // Modul-modul ini belum punya sumber data asli — isinya masih data contoh.
    public function kesehatan()
    {
        return view('pengasuh.kesehatan', ['demo' => DemoPengasuh::kesehatan()]);
    }

    public function infrastruktur()
    {
        return view('pengasuh.infrastruktur', ['demo' => DemoPengasuh::infrastruktur()]);
    }

    public function logistik()
    {
        return view('pengasuh.logistik', ['demo' => DemoPengasuh::logistik()]);
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

        // Ringkasan harian & santri kritis (sebelumnya tampil di dashboard)
        $activityRecap  = $this->buildActivityRecap(Carbon::now()->format('d/m/Y'));
        $criticalSantri = $this->buildCriticalSantri();

        return view('pengasuh.absensi', compact(
            'rekapKamar', 'activities', 'activeTab', 'actConfig',
            'sumH', 'sumS', 'sumI', 'sumA', 'startFmt', 'endFmt',
            'activityRecap', 'criticalSantri'
        ));
    }

    // ==================== PEMBAYARAN (data dummy) ====================
    public function pembayaran(Request $request)
    {
        $demo = DemoPengasuh::pembayaran();

        // Filter dijalankan di PHP karena sumbernya masih array dummy.
        $search = strtolower(trim($request->input('search', '')));
        $status = $request->input('status', '');

        $daftar = collect($demo['daftar'])
            ->when($search !== '', fn($c) => $c->filter(fn($r) => str_contains(strtolower($r['nama']), $search)))
            ->when($status !== '', fn($c) => $c->filter(fn($r) => $r['status'] === $status))
            ->values();

        return view('pengasuh.pembayaran', [
            'ringkasan'     => $demo['ringkasan'],
            'daftar'        => $daftar,
            'topTunggakan'  => $demo['top_tunggakan'],
            'search'        => $request->input('search', ''),
            'status'        => $status,
        ]);
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

    // API: Search pengurus for track record
    public function searchPengurus(Request $request)
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $results = Pengurus::select('nis', 'nama', 'asal')
            ->with('jabatan')
            ->where('nama', 'like', "%{$q}%")
            ->orWhere('nis', 'like', "%{$q}%")
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'nis' => $p->nis,
                'nama' => $p->nama,
                'asal' => $p->asal,
                'jabatan' => $p->jabatan->nama ?? '-',
            ]);

        return response()->json($results);
    }

    // API: Get pengurus track record detail (rekap absensi kegiatan)
    public function pengurusDetail(Request $request, $nis)
    {
        $pengurus = Pengurus::with('jabatan.divisi')->where('nis', $nis)->firstOrFail();

        $activities = [
            ['tipe' => 'bandongan', 'label' => 'Bandongan', 'model' => Bandongan::class],
            ['tipe' => 'wirid',     'label' => 'Wirid',     'model' => Wirid::class],
            ['tipe' => 'yasinan',   'label' => 'Yasinan',   'model' => Yasinan::class],
        ];

        $stats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        $riwayat = collect();

        foreach ($activities as $act) {
            $records = $act['model']::where('nis', $nis)->orderByDesc('tanggal')->get();
            foreach ($records as $r) {
                if (isset($stats[$r->status])) $stats[$r->status]++;
                $riwayat->push([
                    'tanggal' => $r->tanggal,
                    'kegiatan' => $act['label'],
                    'status' => $r->status,
                ]);
            }
        }

        $total = array_sum($stats);
        $kehadiran = $total > 0 ? round(($stats['H'] / $total) * 100) : 100;

        $riwayat = $riwayat->sortByDesc(fn($r) => Carbon::createFromFormat('d-m-Y', $r['tanggal']))
            ->take(10)->values();

        return response()->json([
            'nis' => $pengurus->nis,
            'nama' => $pengurus->nama,
            'jabatan' => $pengurus->jabatan->nama ?? '-',
            'divisi' => $pengurus->jabatan->divisi->nama ?? '-',
            'asal' => $pengurus->asal,
            'stats' => [
                'hadir' => $stats['H'],
                'sakit' => $stats['S'],
                'izin' => $stats['I'],
                'alfa' => $stats['A'],
                'kehadiran' => $kehadiran,
            ],
            'riwayat' => $riwayat,
        ]);
    }
}
