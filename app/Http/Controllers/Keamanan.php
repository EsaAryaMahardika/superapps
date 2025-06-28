<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Larangan;
use App\Models\Perizinan;
use App\Models\AlasanIzin;
use App\Models\Pelanggaran;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class Keamanan extends Controller
{
    public function pelanggaran() {
        $santri = Santri::get(['nis','nama']);
        $larangan = Larangan::all();
        $pelanggaran = Pelanggaran::get();
        return view('keamanan.pelanggaran', compact('santri', 'larangan', 'pelanggaran'));
    }
    function i_pelanggaran(Request $request) {
        $validated = $request->validate([
            'nis' => 'required',
            'pelanggaran_id' => 'required|exists:larangan,id',
            'hukuman' => 'required|string|max:255',
        ]);
        Pelanggaran::create([
            'nis' => $validated['nis'],
            'langgar_id' => $validated['pelanggaran_id'],
            'hukuman' => $validated['hukuman'],
        ]);
        // Flash message using session helper
        session()->flash('success', 'Pelanggaran berhasil ditambahkan');
        return redirect('/pelanggaran');
    }
    public function perizinan() {
        $santri = Santri::get(['nis','nama']);
        $alasan = AlasanIzin::all();
        $perizinan = Perizinan::get();
        return view('keamanan.perizinan', compact('santri', 'alasan', 'perizinan'));
    }
    public function dashboard() {
        $currentYear = date('Y');
        $pelanggar = Pelanggaran::whereYear('tanggal', $currentYear)->count();
        $keluar = Perizinan::where('jenis', 'K')->whereYear('berangkat', $currentYear)->count();
        $pulang = Perizinan::where('jenis', 'P')->whereYear('berangkat', $currentYear)->count();
        return view('keamanan.dashboard', compact('pelanggar', 'keluar', 'pulang'));
    }
}
