<?php

namespace App\Http\Controllers;

use App\Models\Wirid;
use App\Models\Yasinan;
use App\Models\Kegiatan;
use App\Models\Pengurus;
use App\Models\Bandongan;
use Illuminate\Http\Request;
use App\Models\AbsensiJamaah;
use App\Models\AbsensiWaqiah;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Mahadiyah extends Controller
{
    public function dashboard(Request $request){
        $waqiah = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->leftJoin('santri as s', 's.kepkam', '=', 'p.nis')
            ->leftJoin('absen_waqiah as w', function($join) {
                $join->on('w.nis', '=', 's.nis');
            })
            ->select(
                'p.nama',
                'w.tanggal',
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'H' THEN 1 ELSE 0 END), 0) AS hadir"),
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'S' THEN 1 ELSE 0 END), 0) AS sakit"),
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'I' THEN 1 ELSE 0 END), 0) AS izin"),
                DB::raw("COALESCE(SUM(CASE WHEN w.status = 'A' THEN 1 ELSE 0 END), 0) AS alfa")
            )
            ->where('user.role', 's.kepkam')
            ->groupBy('p.nama', 'w.tanggal')
            ->get();
        $absensi = function($tabel, $kegiatan, $value){
            $result = User::leftJoin('pengurus as p', 'user.username', '=', 'p.nis')
            ->leftJoin('santri as s', 's.kepkam', '=', 'p.nis')
            ->leftJoin($tabel.' as k', function($join) use($kegiatan, $value){
                $join->on('k.nis', '=', 's.nis')
                ->where("k.$kegiatan", $value);
            })
            ->select(
                'p.nama',
                "k.tanggal",
                DB::raw("COALESCE(SUM(CASE WHEN k.status = 'H' THEN 1 ELSE 0 END), 0) AS hadir"),
                DB::raw("COALESCE(SUM(CASE WHEN k.status = 'S' THEN 1 ELSE 0 END), 0) AS sakit"),
                DB::raw("COALESCE(SUM(CASE WHEN k.status = 'I' THEN 1 ELSE 0 END), 0) AS izin"),
                DB::raw("COALESCE(SUM(CASE WHEN k.status = 'A' THEN 1 ELSE 0 END), 0) AS alfa")
            )
            ->where('user.role', 'kepkam')
            ->groupBy('p.nama', 'k.tanggal')
            ->get();
            return $result;
        };
        $subuh = $absensi('absen_jamaah', 'sholat', 2);
        $dhuhur = $absensi('absen_jamaah', 'sholat', 3);
        $ashar = $absensi('absen_jamaah', 'sholat', 4);
        $maghrib = $absensi('absen_jamaah', 'sholat', 5);
        $isya = $absensi('absen_jamaah', 'sholat', 6);
        $ngasore = $absensi('absen_ngaji', 'ngaji', 10);
        $ngamalam = $absensi('absen_ngaji', 'ngaji', 11);
        return view('mahadiyah.dashboard', compact(
            'waqiah',
            'subuh',
            'dhuhur',
            'ashar',
            'maghrib',
            'isya',
            'ngasore',
            'ngamalam',
        ));
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
        
    }
}
