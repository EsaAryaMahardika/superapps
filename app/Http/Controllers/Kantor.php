<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengurus;
use App\Models\Asrama;
use App\Models\AlasanBoyong;
use App\Models\Rencana;
use App\Models\Boyong;
use Illuminate\Http\Request;

class Kantor extends Controller
{
    public function kantor(){
        $boyong = Boyong::select('nis')->get();
        return view('kantor.dashboard', compact('boyong'));
    }
    public function boyong(){
        $boyong = Boyong::all();
        $alasan = AlasanBoyong::all();
        $asrama = Asrama::all();
        $kelas = Kelas::all();
        $kepkam = Pengurus::all();
        $rencana = Rencana::all();
        return view('kantor.boyong', compact('boyong','alasan', 'asrama', 'kelas', 'kepkam', 'rencana'));
    }
    public function i_boyong(Request $request) {
        Boyong::create([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'kep_id' => $request->kepkam,
            'asr_id' => $request->asrama,
            'ala_id' => $request->alasan,
            'ren_id' => $request->rencana
        ]);
        session()->flash('success', 'Data berhasil disimpan');
        return redirect('/boyong');
    }
}
