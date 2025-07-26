<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use App\Models\AbsensiWaqiah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $kegiatan = Kegiatan::all();
        $santri = Santri::select('nis', 'nama')->get();
        return view('kepkam.absensi', compact('waqiah', 'kegiatan', 'santri'));
        // return dd($waqiah->santri->nama);
    }
    public function absen(Request $request){
        foreach ($request->santri as $nis => $status) {
            $nis = (string) $nis;
            $santri = Santri::where('nis', $nis)->first();
            if ($santri) {
                $cek = AbsensiWaqiah::where('tanggal', $request->tanggal)->get();
                if ($cek != NULL){
                    AbsensiWaqiah::updateOrCreate(
                        [
                            'nis' => $nis,
                            'tanggal' => $request->tanggal
                        ],
                        [
                            'status' => $status,
                        ]
                    );
                    session()->flash('success', 'Absensi berhasil dibuat');
                } else {
                    session()->flash('error', 'Absensi hari ini sudah dibuat');
                }
            }
        }
        return redirect('/absensi');
    }
}
