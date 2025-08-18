<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
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
        return view('kepkam.dashboard', compact('waqiah'));
    }
    public function absensi() {
        $waqiah = AbsensiWaqiah::with('santri')->get();
        $subuh = AbsensiJamaah::where('sholat', 2)->with('santri')->get();
        $dhuhur = AbsensiJamaah::where('sholat', 3)->with('santri')->get();
        $ashar = AbsensiJamaah::where('sholat', 4)->with('santri')->get();
        $maghrib = AbsensiJamaah::where('sholat', 5)->with('santri')->get();
        $isya = AbsensiJamaah::where('sholat', 6)->with('santri')->get();
        $kegiatan = Kegiatan::all();
        $santri = Santri::select('nis', 'nama')->get();
        return view('kepkam.absensi', compact(
            'waqiah',
            'kegiatan',
            'santri',
            'subuh',
            'dhuhur',
            'ashar',
            'maghrib',
            'isya',
        ));
    }
    public function absen(Request $request) {
        $tanggal = $request->tanggal;
        $kegiatan = $request->kegiatan;
        match($kegiatan) {
            '1' => $exists = AbsensiWaqiah::where('tanggal', $tanggal)->exists(),
            default => $exists = AbsensiJamaah::where(['tanggal' => $tanggal, 'sholat' => $kegiatan])->exists()
        };
        if ($exists) {
            session()->flash('error', 'Absensi hari ini sudah dibuat');
            return redirect('/absensi');
        }
        foreach ($request->santri as $nis => $status) {
            $santri = Santri::where('nis', (string)$nis)->first();   
            if ($santri) {
                match($kegiatan){
                    '1' => AbsensiWaqiah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal],['status' => $status]),
                    '2' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan],['status' => $status]),
                    '3' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan],['status' => $status]),
                    '4' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan],['status' => $status]),
                    '5' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan],['status' => $status]),
                    '6' => AbsensiJamaah::updateOrCreate(['nis' => (string)$nis, 'tanggal' => $tanggal, 'sholat' => $kegiatan],['status' => $status]),
                };
            }
        }
        session()->flash('success', 'Absensi berhasil dibuat');
        return redirect('/absensi');
    }
}
