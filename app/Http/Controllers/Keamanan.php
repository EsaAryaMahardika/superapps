<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Santri;
use App\Models\Larangan;
use App\Models\Perizinan;
use App\Models\AlasanIzin;
use App\Models\Pelanggaran;
use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;

class Keamanan extends Controller
{
    public function pelanggaran() {
        $santri = Santri::get(['nis','nama']);
        $larangan = Larangan::all();
        $pelanggaran = Pelanggaran::get();
        return view('keamanan.pelanggaran', compact('santri', 'larangan', 'pelanggaran'));
    }
    public function i_pelanggaran(Request $request) {
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
        return redirect('/keamanan/pelanggaran');
    }
    public function dashboard() {
        // $currentMonth = date('M');
        $currentMonth = Carbon::now()->month;
        $pelanggar = Pelanggaran::whereMonth ('tanggal', $currentMonth)->count();
        $keluar = Perizinan::where('jenis', 'K')->whereMonth ('berangkat', $currentMonth)->count();
        $pulang = Perizinan::where('jenis', 'P')->whereMonth ('berangkat', $currentMonth)->count();
        return view('keamanan.dashboard', compact('pelanggar', 'keluar', 'pulang'));
    }
}
