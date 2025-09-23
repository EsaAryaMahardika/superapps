<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Pengurus;
use App\Models\Asrama;
use App\Models\AlasanBoyong;
use App\Models\Rencana;
use App\Models\Boyong;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

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
            'nis' => Purify::clean($request->nis),
            'nama' => Purify::clean($request->nama),
            'kelas' => Purify::clean($request->kelas),
            'kep_id' => Purify::clean($request->kepkam),
            'asr_id' => Purify::clean($request->asrama),
            'ala_id' => Purify::clean($request->alasan),
            'ren_id' => Purify::clean($request->rencana)
        ]);
        session()->flash('success', 'Data berhasil disimpan');
        return redirect('/boyong');
    }
}
