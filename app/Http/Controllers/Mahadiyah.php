<?php

namespace App\Http\Controllers;

use App\Models\Wirid;
use App\Models\Yasinan;
use App\Models\Bandongan;
use Illuminate\Http\Request;
use App\Models\AbsensiWaqiah;
use App\Models\Kegiatan;
use App\Models\Pengurus;
use Illuminate\Support\Facades\DB;

class Mahadiyah extends Controller
{
    public function dashboard(){
        return view('mahadiyah.dashboard');
    }
    public function absensi()
    {
        $bandongan = Bandongan::with('pengurus')->get();
        $wirid = Wirid::with('pengurus')->get();
        $yasinan = Yasinan::with('pengurus')->get();
        return view('mahadiyah.absensi-pengurus', compact(
            'bandongan',
            'wirid',
            'yasinan'
        ));
    }
    public function create_absen() {
        $kegiatan = Kegiatan::where('ket', 'P')->get();
        $pengurus = Pengurus::select('nis', 'nama')->get();
        return view('mahadiyah.create-absensi', compact('kegiatan', 'pengurus'));
    }
    public function store_absen(Request $request){
        $kegiatan = $request->kegiatan;
        $tanggal = $request->tanggal;
        match($kegiatan) {
            '7' => $exists = Bandongan::where('tanggal', $tanggal)->exists(),
            '8' => $exists = Wirid::where('tanggal', $tanggal)->exists(),
            '9' => $exists = Yasinan::where('tanggal', $tanggal)->exists()
        };
        if ($exists) {
            session()->flash('error', 'Absensi hari ini sudah dibuat');
            return redirect('/mahadiyah/absensi-pengurus');
        }
        foreach ($request->pengurus as $nis => $status) {
            $pengurus = Pengurus::where('nis', (string)$nis)->first();   
            if ($pengurus) {
                match($kegiatan){
                    '7' => Bandongan::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'status' => $status]),
                    '8' => Wirid::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'status' => $status]),
                    '9' => Yasinan::create(['nis' => (string)$nis, 'tanggal' => $tanggal, 'status' => $status])
                };
            }
        }
        session()->flash('success', 'Absensi berhasil dibuat');
        return redirect('/mahadiyah/absensi-pengurus');
    }
    public function mingguan()
    {
        return view('mahadiyah.absen-mingguan');
    }
    public function kegiatan()
    {
        $waqiah = AbsensiWaqiah::select(
            'tanggal',
            DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alfa")
        )
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->get();
        return view('mahadiyah.absensi-kegiatan', compact('waqiah'));
    }
}
