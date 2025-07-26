<?php

namespace App\Http\Controllers;

use App\Models\AbsensiWaqiah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Mahadiyah extends Controller
{
    public function dashboard(){
        return view('mahadiyah.dashboard');
    }
    public function absensi()
    {
        return view('mahadiyah.absensi-pengurus');
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
