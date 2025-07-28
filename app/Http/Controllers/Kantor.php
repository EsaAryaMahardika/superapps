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
        return view('kantor.dashboard');
    }
    public function boyong(){
        $boyong = Boyong::all();
        $alasan = AlasanBoyong::all();
        $asrama = Asrama::all();
        $kelas = Kelas::all();
        $kepkam = Pengurus::where('jab_id', 1)->get();
        $rencana = Rencana::all();
        return view('kantor.boyong', compact('boyong','alasan', 'asrama', 'kelas', 'kepkam', 'rencana'));
    }
    public function i_boyong(Request $request) {
        Boyong::create([
            'nis' => $request->nis,
            'nama' => $request->nama,
            'kelas' => $request->kelas,
            'kepkam' => $request->kepkam,
            'asr_id' => $request->asrama,
            'alasan' => $request->alasan,
            'rencana' => $request->rencana
        ]);
        session()->flash('success', 'Data berhasil disimpan');
        return redirect('/boyong');
    }
}
