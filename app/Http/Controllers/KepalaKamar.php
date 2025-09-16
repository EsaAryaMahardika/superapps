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
    public function dashboard(){
        $absen_kegiatan = function($model, $kegiatan, $value) {
            $modelClass = "\\App\\Models\\$model";
            $result = $modelClass::select(
                'tanggal',
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
            )
            ->whereHas('santri', function($q) {
                $q->where('kepkam', $this->user->username);
            })
            ->where($kegiatan, $value)
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get();
            return $result;
        };
        $subuh = $absen_kegiatan('AbsensiJamaah', 'sholat', 2);
        $dhuhur = $absen_kegiatan('AbsensiJamaah', 'sholat', 3);
        $ashar = $absen_kegiatan('AbsensiJamaah', 'sholat', 4);
        $maghrib = $absen_kegiatan('AbsensiJamaah', 'sholat', 5);
        $isya = $absen_kegiatan('AbsensiJamaah', 'sholat', 6);
        $ngasore = $absen_kegiatan('AbsensiNgaji', 'ngaji', 10);
        $ngamalam = $absen_kegiatan('AbsensiNgaji', 'ngaji', 11);
        $waqiah = AbsensiWaqiah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->whereHas('santri', function($q) {$q->where('kepkam', $this->user->username);})
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        return view('kepkam.dashboard', compact(
            'waqiah',
            'subuh',
            'dhuhur',
            'ashar',
            'maghrib',
            'isya',
            'ngasore',
            'ngamalam'));
    }
    // ---------------- //
    // ABSENSI KEGIATAN //
    // ---------------- //
    public function absensi() {
        $waqiah = AbsensiWaqiah::whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
        })->with('santri')->get();
        $absensiharian = function($model, $kegiatan, $value) {
            $class = "\\App\\Models\\$model";
            $result = $class::where($kegiatan, $value)->whereHas('santri', function($q) {
                $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
            return $result;
        };
        $subuh = $absensiharian('AbsensiJamaah', 'sholat', 2);
        $dhuhur = $absensiharian('AbsensiJamaah', 'sholat', 3);
        $ashar = $absensiharian('AbsensiJamaah', 'sholat', 4);
        $maghrib = $absensiharian('AbsensiJamaah', 'sholat', 5);
        $isya = $absensiharian('AbsensiJamaah', 'sholat', 6);
        $ngasore = $absensiharian('AbsensiNgaji', 'ngaji', 10);
        $ngamalam = $absensiharian('AbsensiNgaji', 'ngaji', 11);
        $kegiatan = Kegiatan::where('ket', 'S')->get();
        $santri = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();
        return view('kepkam.absensi', compact(
            'waqiah',
            'kegiatan',
            'santri',
            'subuh',
            'dhuhur',
            'ashar',
            'maghrib',
            'isya',
            'ngasore',
            'ngamalam'
        ));
    }
    public function absen(Request $request) {
        $tanggal = Carbon::parse($request->tanggal)->format('d/m/Y');
        $kegiatan = $request->kegiatan;
        foreach ($request->santri as $nis => $status) {
            $santri = Santri::where('nis', (string)$nis)->first();   
            if ($santri) {
                match($kegiatan){
                    '1' => AbsensiWaqiah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal], ['status' => $status]),
                    '2' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan], ['status' => $status]),
                    '3' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan], ['status' => $status]),
                    '4' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan], ['status' => $status]),
                    '5' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan], ['status' => $status]),
                    '6' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan], ['status' => $status]),
                    '10' => AbsensiNgaji::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'ngaji' => $kegiatan], ['status' => $status]),
                    '11' => AbsensiNgaji::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'ngaji' => $kegiatan], ['status' => $status]),
                };
            }
        }
        session()->flash('success', 'Absensi berhasil disimpan');
        return redirect('/kepkam/absensi');
    }
    // ---------------- //
    // ABSENSI MINGGUAN //
    // ---------------- //
    public function mingguan() {
        $santri = Santri::select('nis', 'nama')->where('kepkam', $this->user->username)->get();
        $larangan = Larangan::select('id', 'nama')->where('ket' , 'K')->get();
        $mingguan = AbsensiMingguan::whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
        })->with('santri', 'larangan')->get();
        return view('kepkam.mingguan', compact('larangan', 'mingguan', 'santri'));
    }
    public function i_mingguan(Request $request) {
        $tanggal = Carbon::parse($request->tanggal)->format('d/m/Y');
        $larangan = $request->larangan;
        foreach ($request->santri as $nis) {
            $santri = Santri::where('nis', (string)$nis)->first();
            if ($santri) {
                AbsensiMingguan::create(['nis' => (string)$nis, 'pelanggaran' => $larangan, 'tanggal' => $tanggal]);
            }
        }
        session()->flash('success', 'Absensi berhasil disimpan');
        return redirect('/kepkam/mingguan');
    }
}
