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
        $waqiah = AbsensiWaqiah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        $subuh = AbsensiJamaah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->where('sholat', 2)
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        $dhuhur = AbsensiJamaah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->where('sholat', 3)
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        $ashar = AbsensiJamaah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->where('sholat', 4)
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        $maghrib = AbsensiJamaah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->where('sholat', 5)
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        $isya = AbsensiJamaah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->where('sholat', 6)
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        $ngasore = AbsensiNgaji::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->where('ngaji', 10)
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        $ngamalam = AbsensiNgaji::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->where('ngaji', 11)
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
        $subuh = AbsensiJamaah::where('sholat', 2)
            ->whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
        $dhuhur = AbsensiJamaah::where('sholat', 3)
            ->whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
        $ashar = AbsensiJamaah::where('sholat', 4)
            ->whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
        $maghrib = AbsensiJamaah::where('sholat', 5)
            ->whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
        $isya = AbsensiJamaah::where('sholat', 6)
            ->whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
        $ngasore = AbsensiNgaji::where('ngaji', 10)
            ->whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
        $ngamalam = AbsensiNgaji::where('ngaji', 11)
            ->whereHas('santri', function($q) {
            $q->where('kepkam', $this->user->username);
            })->with('santri')->get();
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
        $option = [
            1 => 'Waqiah',
            2 => 'Subuh',
            3 => 'Dhuhur',
            4 => 'Ashar',
            5 => 'Maghrib',
            6 => 'Isya',
            10 => 'Ngaji Sore',
            11 => 'Ngaji Malam'
        ];
        $selected = $option[$kegiatan];
        match($kegiatan) {
            '1' => $exists = AbsensiWaqiah::where('tanggal', $tanggal)->exists(),
            '2' => $exists = AbsensiJamaah::where(['tanggal' => $tanggal, 'sholat' => $kegiatan])->exists(),
            '3' => $exists = AbsensiJamaah::where(['tanggal' => $tanggal, 'sholat' => $kegiatan])->exists(),
            '4' => $exists = AbsensiJamaah::where(['tanggal' => $tanggal, 'sholat' => $kegiatan])->exists(),
            '5' => $exists = AbsensiJamaah::where(['tanggal' => $tanggal, 'sholat' => $kegiatan])->exists(),
            '6' => $exists = AbsensiJamaah::where(['tanggal' => $tanggal, 'sholat' => $kegiatan])->exists(),
            '10' => $exists = AbsensiNgaji::where(['tanggal' => $tanggal, 'ngaji' => $kegiatan])->exists(),
            '11' => $exists = AbsensiNgaji::where(['tanggal' => $tanggal, 'ngaji' => $kegiatan])->exists()
        };
        if ($exists) {
            session()->flash('error', 'Absensi '.$selected.' tanggal '.$tanggal.' sudah pernah dibuat');
            return redirect('/kepkam/absensi');
        }
        foreach ($request->santri as $nis => $status) {
            $santri = Santri::where('nis', (string)$nis)->first();   
            if ($santri) {
                match($kegiatan){
                    '1' => AbsensiWaqiah::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'status' => $status]),
                    '2' => AbsensiJamaah::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan, 'status' => $status]),
                    '3' => AbsensiJamaah::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan, 'status' => $status]),
                    '4' => AbsensiJamaah::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan, 'status' => $status]),
                    '5' => AbsensiJamaah::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan, 'status' => $status]),
                    '6' => AbsensiJamaah::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan, 'status' => $status]),
                    '10' => AbsensiNgaji::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'ngaji' => $kegiatan, 'status' => $status]),
                    '11' => AbsensiNgaji::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'ngaji' => $kegiatan, 'status' => $status]),
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
        $pelanggaran = Larangan::where('id', $larangan)->first();
        $existsLarangan = AbsensiMingguan::where('pelanggaran', $larangan)->exists();
        $existsTanggal = AbsensiMingguan::where('tanggal', $tanggal)->exists();
        if ($existsLarangan && $existsTanggal) {
            session()->flash('error', 'Absensi pelanggaran '. $pelanggaran->nama .' minggu ini sudah pernah dibuat');
            return redirect('/kepkam/mingguan');
        }
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
