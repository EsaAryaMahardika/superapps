<?php

namespace App\Http\Controllers\KepalaKamar;

use Carbon\Carbon;
use App\Models\User;
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

    public function index()
    {
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

            return $dates->map(function ($date) use ($data) {
                if ($data->has($date)) {
                    $item = $data->get($date);
                    $item->is_filled = true;
                    return $item;
                }
                return (object) ['tanggal' => $date, 'hadir' => '-', 'sakit' => '-', 'izin' => '-', 'alfa' => '-', 'is_filled' => false];
            });
        };

        $subuh    = $absen_kegiatan('AbsensiJamaah', 'sholat', 2);
        $dhuhur   = $absen_kegiatan('AbsensiJamaah', 'sholat', 3);
        $ashar    = $absen_kegiatan('AbsensiJamaah', 'sholat', 4);
        $maghrib  = $absen_kegiatan('AbsensiJamaah', 'sholat', 5);
        $isya     = $absen_kegiatan('AbsensiJamaah', 'sholat', 6);
        $ngasore  = $absen_kegiatan('AbsensiNgaji',  'ngaji',  10);
        $ngamalam = $absen_kegiatan('AbsensiNgaji',  'ngaji',  11);

        $waqiahData = AbsensiWaqiah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
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
            }
            return (object) ['tanggal' => $date, 'hadir' => '-', 'sakit' => '-', 'izin' => '-', 'alfa' => '-', 'is_filled' => false];
        });

        return view('kepkam.dashboard', compact('waqiah', 'subuh', 'dhuhur', 'ashar', 'maghrib', 'isya', 'ngasore', 'ngamalam'));
    }
}
