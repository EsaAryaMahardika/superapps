<?php

namespace App\Http\Controllers\KepalaKamar;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiNgaji;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    private function activityConfig(): array
    {
        return [
            ['id' => 'subuh',    'name' => 'Subuh',       'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2],
            ['id' => 'dhuhur',   'name' => 'Dhuhur',      'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3],
            ['id' => 'ashar',    'name' => 'Ashar',       'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4],
            ['id' => 'maghrib',  'name' => 'Maghrib',     'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5],
            ['id' => 'isya',     'name' => 'Isya',        'model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6],
            ['id' => 'waqiah',   'name' => 'Waqiah',      'model' => 'AbsensiWaqiah', 'col' => null,      'val' => null],
            ['id' => 'ngasore',  'name' => 'Ngaji Malam 1',  'model' => 'AbsensiNgaji',  'col' => 'ngaji',   'val' => 10],
            ['id' => 'ngamalam', 'name' => 'Ngaji Malam 2', 'model' => 'AbsensiNgaji',  'col' => 'ngaji',   'val' => 11],
        ];
    }

    public function index()
    {
        $kepkam = $this->user->username;
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::now()->subDays($i)->format('d/m/Y'));
        }

        $today     = $dates->last();
        $yesterday = $dates->get(5);

        // --- 1. Per-activity daily summary (for chart) ---
        $chartData = [];
        $dailyTotals = []; // [date => ['H'=>n, 'S'=>n, 'I'=>n, 'A'=>n]]

        foreach ($dates as $d) {
            $dailyTotals[$d] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        }

        foreach ($this->activityConfig() as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::select(
                'tanggal',
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
            )
                ->whereHas('santri', fn($q) => $q->where('kepkam', $kepkam))
                ->whereIn('tanggal', $dates->toArray());

            if ($act['col']) $query->where($act['col'], $act['val']);

            $rows = $query->groupBy('tanggal')->get()->keyBy('tanggal');

            $chartData[$act['id']] = $dates->map(function ($date) use ($rows) {
                if ($rows->has($date)) {
                    $r = $rows->get($date);
                    return ['H' => (int) $r->hadir, 'S' => (int) $r->sakit, 'I' => (int) $r->izin, 'A' => (int) $r->alfa];
                }
                return ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
            })->toArray();

            // Accumulate daily totals
            foreach ($dates as $d) {
                $idx = $dates->search($d);
                $dailyTotals[$d]['H'] += $chartData[$act['id']][$idx]['H'];
                $dailyTotals[$d]['S'] += $chartData[$act['id']][$idx]['S'];
                $dailyTotals[$d]['I'] += $chartData[$act['id']][$idx]['I'];
                $dailyTotals[$d]['A'] += $chartData[$act['id']][$idx]['A'];
            }
        }

        // --- 2. Summary stats (today vs yesterday) ---
        $todayStats     = $dailyTotals[$today];
        $yesterdayStats = $dailyTotals[$yesterday];

        $todayTotal     = array_sum($todayStats);
        $yesterdayTotal = array_sum($yesterdayStats);

        $todayHadirPct     = $todayTotal > 0 ? round(($todayStats['H'] / $todayTotal) * 100) : 0;
        $yesterdayHadirPct = $yesterdayTotal > 0 ? round(($yesterdayStats['H'] / $yesterdayTotal) * 100) : 0;
        $hadirTrend        = $todayHadirPct - $yesterdayHadirPct;

        // --- 3. Heatmap: per-santri × per-date (for selected activity) ---
        $santriList = Santri::select('nis', 'nama')
            ->where('kepkam', $kepkam)
            ->orderBy('nama')
            ->get();

        $heatmap = [];
        foreach ($this->activityConfig() as $act) {
            $modelClass = "\\App\\Models\\{$act['model']}";
            $query = $modelClass::select('nis', 'tanggal', 'status')
                ->whereHas('santri', fn($q) => $q->where('kepkam', $kepkam))
                ->whereIn('tanggal', $dates->toArray());

            if ($act['col']) $query->where($act['col'], $act['val']);

            $rows = $query->get();

            foreach ($rows as $row) {
                $heatmap[$act['id']][$row->nis][$row->tanggal] = $row->status;
            }
        }

        // --- 2b. Unique santri detail for S/I/A today ---
        $detailSakit = [];
        $detailIzin  = [];
        $detailAlfa  = [];

        $santriNames = $santriList->pluck('nama', 'nis');

        foreach ($this->activityConfig() as $act) {
            $actHeatmap = $heatmap[$act['id']] ?? [];
            foreach ($actHeatmap as $nis => $dateMap) {
                $statusToday = $dateMap[$today] ?? null;
                if ($statusToday === 'S') {
                    $detailSakit[$nis]['nama'] = $santriNames[$nis] ?? $nis;
                    $detailSakit[$nis]['kegiatan'][] = $act['name'];
                } elseif ($statusToday === 'I') {
                    $detailIzin[$nis]['nama'] = $santriNames[$nis] ?? $nis;
                    $detailIzin[$nis]['kegiatan'][] = $act['name'];
                } elseif ($statusToday === 'A') {
                    $detailAlfa[$nis]['nama'] = $santriNames[$nis] ?? $nis;
                    $detailAlfa[$nis]['kegiatan'][] = $act['name'];
                }
            }
        }

        $stats = [
            'hadir'     => $todayStats['H'],
            'sakit'     => count($detailSakit),
            'izin'      => count($detailIzin),
            'alfa'      => count($detailAlfa),
            'total'     => $todayTotal,
            'hadir_pct' => $todayHadirPct,
            'trend'     => $hadirTrend,
        ];

        // Chart labels (formatted short)
        $chartLabels = $dates->map(fn($d) => Carbon::createFromFormat('d/m/Y', $d)->locale('id')->isoFormat('ddd, DD/MM'))->toArray();

        $activities = $this->activityConfig();

        return view('kepkam.dashboard', compact(
            'stats', 'chartData', 'chartLabels', 'dates',
            'heatmap', 'santriList', 'activities',
            'detailSakit', 'detailIzin', 'detailAlfa'
        ));
    }
}
