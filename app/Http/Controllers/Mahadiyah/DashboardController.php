<?php

namespace App\Http\Controllers\Mahadiyah;

use Carbon\Carbon;
use App\Models\Pengurus;
use App\Models\User;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\AbsensiNgaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    private function getActivityConfig(): array
    {
        return [
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 2,  'label' => 'Subuh'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 3,  'label' => 'Dhuhur'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 4,  'label' => 'Ashar'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 5,  'label' => 'Maghrib'],
            ['model' => 'AbsensiJamaah', 'col' => 'sholat', 'val' => 6,  'label' => 'Isya'],
            ['model' => 'AbsensiWaqiah', 'col' => null,     'val' => null,'label' => 'Waqiah'],
            ['model' => 'AbsensiNgaji',  'col' => 'ngaji',  'val' => 10, 'label' => 'Ngaji Sore'],
            ['model' => 'AbsensiNgaji',  'col' => 'ngaji',  'val' => 11, 'label' => 'Ngaji Malam'],
        ];
    }

    private function getTableActivityMap(): array
    {
        return [
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 2,  'label' => 'Subuh'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 3,  'label' => 'Dhuhur'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 4,  'label' => 'Ashar'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 5,  'label' => 'Maghrib'],
            ['table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 6,  'label' => 'Isya'],
            ['table' => 'absen_waqiah', 'col' => null,     'val' => null,'label' => 'Waqiah'],
            ['table' => 'absen_ngaji',  'col' => 'ngaji',  'val' => 10, 'label' => 'Ngaji Sore'],
            ['table' => 'absen_ngaji',  'col' => 'ngaji',  'val' => 11, 'label' => 'Ngaji Malam'],
        ];
    }

    public function index(Request $request)
    {
        $summaryDate = $request->filled('summary_date')
            ? Carbon::parse($request->summary_date)
            : Carbon::now();
        $today = $summaryDate->format('d/m/Y');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate   = Carbon::parse($request->end_date)->endOfDay();
        } else {
            $endDate   = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        }

        $allKepkams   = $this->getAllKepkams();
        $totalKepkam  = $allKepkams->count();
        $totalSantri  = DB::table('santri')->count();

        [$kepkamSudahAbsen, $kepkamBelumAbsen, $listKepkamSudah, $listKepkamBelum]
            = $this->buildKepkamStatus($allKepkams, $today);

        [$santriHadir, $santriSakit, $santriIzin, $santriAlpa,
         $listSantriHadirNama, $listSantriSakitNama, $listSantriIzinNama, $listSantriAlpaNama]
            = $this->buildSantriStatus($today);

        [$chartLabelsFormatted, $chartDatasets] = $this->buildChartData($startDate, $endDate);

        $rankStartDate = $request->filled('rank_start_date')
            ? Carbon::parse($request->rank_start_date)->startOfDay()
            : $startDate->copy();
        $rankEndDate = $request->filled('rank_end_date')
            ? Carbon::parse($request->rank_end_date)->endOfDay()
            : $endDate->copy();

        $topRanking = $this->buildTopRanking($rankStartDate, $rankEndDate);

        return view('mahadiyah.dashboard', compact(
            'kepkamSudahAbsen', 'kepkamBelumAbsen',
            'santriHadir', 'santriSakit', 'santriIzin', 'santriAlpa',
            'listKepkamSudah', 'listKepkamBelum',
            'listSantriHadirNama', 'listSantriSakitNama',
            'listSantriIzinNama', 'listSantriAlpaNama',
            'chartLabelsFormatted', 'chartDatasets',
            'totalKepkam', 'totalSantri',
            'startDate', 'endDate', 'summaryDate',
            'topRanking', 'rankStartDate', 'rankEndDate'
        ));
    }

    private function getAllKepkams()
    {
        return User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->where('user.role', 'kepkam')
            ->select('p.nis', 'p.nama')
            ->orderBy('p.nama')
            ->get();
    }

    private function buildKepkamStatus($allKepkams, string $today): array
    {
        $activities = $this->getActivityConfig();
        $sudahCount = 0; $belumCount = 0;
        $listSudah  = []; $listBelum  = [];

        foreach ($allKepkams as $kepkam) {
            $santriNis = DB::table('santri')->where('kepkam', $kepkam->nis)->pluck('nis')->toArray();
            $done = []; $missing = [];

            foreach ($activities as $act) {
                $isFilled = false;
                if (!empty($santriNis)) {
                    $modelClass = "\\App\\Models\\{$act['model']}";
                    $q = $modelClass::where('tanggal', $today)->whereIn('nis', $santriNis);
                    if ($act['col']) $q->where($act['col'], $act['val']);
                    $isFilled = $q->exists();
                }
                $isFilled ? ($done[] = "✅ {$act['label']}") : ($missing[] = "❌ {$act['label']}");
            }

            if (!empty($done))    { $sudahCount++; $listSudah[] = ['nama' => $kepkam->nama, 'kegiatan' => $done]; }
            if (!empty($missing)) { $belumCount++; $listBelum[] = ['nama' => $kepkam->nama, 'kegiatan' => $missing]; }
        }

        return [$sudahCount, $belumCount, $listSudah, $listBelum];
    }

    private function buildSantriStatus(string $today): array
    {
        $activityMap = $this->getTableActivityMap();
        $hadirNis = collect(); $sakitDetail = []; $izinDetail = []; $alpaDetail = [];

        foreach ($activityMap as $act) {
            $q = DB::table($act['table'])->where('tanggal', $today);
            if ($act['col']) $q->where($act['col'], $act['val']);

            $hadirNis = $hadirNis->merge((clone $q)->where('status', 'H')->pluck('nis'));
            foreach ((clone $q)->where('status', 'S')->pluck('nis') as $nis) $sakitDetail[$nis][] = $act['label'];
            foreach ((clone $q)->where('status', 'I')->pluck('nis') as $nis) $izinDetail[$nis][]  = $act['label'];
            foreach ((clone $q)->where('status', 'A')->pluck('nis') as $nis) $alpaDetail[$nis][]  = $act['label'];
        }

        $groupFn = fn($q, $simple, $map) => $this->groupByKepkam($q, $simple, $map);

        return [
            $hadirNis->unique()->count(),
            count($sakitDetail), count($izinDetail), count($alpaDetail),
            $groupFn(DB::table('santri')->whereIn('santri.nis', $hadirNis->unique()), true, []),
            $groupFn(DB::table('santri')->whereIn('santri.nis', array_keys($sakitDetail)), false, $sakitDetail),
            $groupFn(DB::table('santri')->whereIn('santri.nis', array_keys($izinDetail)),  false, $izinDetail),
            $groupFn(DB::table('santri')->whereIn('santri.nis', array_keys($alpaDetail)),  false, $alpaDetail),
        ];
    }

    private function groupByKepkam($query, bool $isSimpleList, array $detailsMap): array
    {
        $data = $query->leftJoin('pengurus', 'santri.kepkam', '=', 'pengurus.nis')
            ->select('santri.nis', 'santri.nama', 'pengurus.nama as kepkam_nama')
            ->orderBy('kepkam_nama')->orderBy('santri.nama')->get();

        $grouped = [];
        foreach ($data as $row) {
            $key = $row->kepkam_nama ?? 'Tanpa Kepkam';
            $grouped[$key][] = $isSimpleList
                ? $row->nama
                : ['nama' => $row->nama, 'kegiatan' => $detailsMap[$row->nis] ?? []];
        }
        ksort($grouped);

        return array_values(array_map(fn($k, $l) => ['kepkam' => $k, 'list' => $l], array_keys($grouped), $grouped));
    }

    private function buildChartData($startDate, $endDate): array
    {
        $chartActivities = [
            ['key' => 'Subuh',      'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 2],
            ['key' => 'Dhuhur',     'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 3],
            ['key' => 'Ashar',      'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 4],
            ['key' => 'Maghrib',    'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 5],
            ['key' => 'Isya',       'table' => 'absen_jamaah', 'col' => 'sholat', 'val' => 6],
            ['key' => 'Waqiah',     'table' => 'absen_waqiah', 'col' => null,     'val' => null],
            ['key' => 'Ngaji Sore', 'table' => 'absen_ngaji',  'col' => 'ngaji',  'val' => 10],
            ['key' => 'Ngaji Malam','table' => 'absen_ngaji',  'col' => 'ngaji',  'val' => 11],
        ];

        $labels = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) { $labels[] = $curr->format('d/m/Y'); $curr->addDay(); }

        $datasets = [];
        foreach ($chartActivities as $act) {
            $data = [];
            foreach ($labels as $date) {
                $q = DB::table($act['table'])->where('tanggal', $date)->where('status', 'H');
                if ($act['col']) $q->where($act['col'], $act['val']);
                $data[] = $q->count();
            }
            $datasets[] = ['label' => $act['key'], 'data' => $data];
        }

        $labelsFormatted = array_map(fn($d) => Carbon::createFromFormat('d/m/Y', $d)->format('d M'), $labels);

        return [$labelsFormatted, $datasets];
    }

    private function buildTopRanking($rankStartDate, $rankEndDate): array
    {
        $dateRange = [];
        $curr = $rankStartDate->copy();
        while ($curr->lte($rankEndDate)) { $dateRange[] = $curr->format('d/m/Y'); $curr->addDay(); }

        $topProblematic = DB::table(function ($q) use ($dateRange) {
            $q->select('nis')->from('absen_jamaah')->where('status', 'A')->whereIn('tanggal', $dateRange)
              ->unionAll(DB::table('absen_waqiah')->select('nis')->where('status', 'A')->whereIn('tanggal', $dateRange))
              ->unionAll(DB::table('absen_ngaji')->select('nis')->where('status', 'A')->whereIn('tanggal', $dateRange));
        }, 'all_alpa')
            ->select('nis', DB::raw('count(*) as alpa_count'))
            ->groupBy('nis')->orderByDesc('alpa_count')->limit(5)->get();

        $topRanking = [];
        foreach ($topProblematic as $tp) {
            $santri = DB::table('santri')
                ->leftJoin('pengurus', 'santri.kepkam', '=', 'pengurus.nis')
                ->where('santri.nis', $tp->nis)
                ->select('santri.nama', 'pengurus.nama as kepkam_nama')->first();

            if (!$santri) continue;

            $details = $this->fetchAlpaDetails($tp->nis, $dateRange);
            usort($details, fn($a, $b) => strcmp($b['raw_date'], $a['raw_date']));

            $grouped = [];
            Carbon::setLocale('id');
            foreach ($details as $d) {
                $fullDate = Carbon::createFromFormat('d/m/Y', $d['tanggal'])->translatedFormat('l, d F Y');
                $grouped[$fullDate]['nama'] = $fullDate;
                $grouped[$fullDate]['kegiatan'][] = $d['kegiatan'];
            }

            $topRanking[] = [
                'nama'    => $santri->nama,
                'kepkam'  => $santri->kepkam_nama ?? 'Tanpa Kepkam',
                'count'   => $tp->alpa_count,
                'details' => array_values($grouped),
            ];
        }

        return $topRanking;
    }

    private function fetchAlpaDetails(string $nis, array $dateRange): array
    {
        $details = [];

        foreach (DB::table('absen_jamaah')->where('nis', $nis)->where('status', 'A')->whereIn('tanggal', $dateRange)->select('tanggal', 'sholat as val')->get() as $d) {
            $names = [1 => 'Subuh', 2 => 'Subuh', 3 => 'Dhuhur', 4 => 'Ashar', 5 => 'Maghrib', 6 => 'Isya'];
            $details[] = ['tanggal' => $d->tanggal, 'kegiatan' => $names[$d->val] ?? 'Sholat', 'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_{$d->val}"];
        }
        foreach (DB::table('absen_waqiah')->where('nis', $nis)->where('status', 'A')->whereIn('tanggal', $dateRange)->select('tanggal')->get() as $d) {
            $details[] = ['tanggal' => $d->tanggal, 'kegiatan' => 'Waqiah', 'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . '_7'];
        }
        foreach (DB::table('absen_ngaji')->where('nis', $nis)->where('status', 'A')->whereIn('tanggal', $dateRange)->select('tanggal', 'ngaji as val')->get() as $d) {
            $names = [10 => 'Ngaji Sore', 11 => 'Ngaji Malam'];
            $details[] = ['tanggal' => $d->tanggal, 'kegiatan' => $names[$d->val] ?? 'Ngaji', 'raw_date' => Carbon::createFromFormat('d/m/Y', $d->tanggal)->format('Y-m-d') . "_{$d->val}"];
        }

        return $details;
    }
}
