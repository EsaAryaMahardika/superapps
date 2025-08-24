<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Pengurus;
use App\Models\Asrama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class General extends Controller
{
    public function login(){
        return view('login');
    }
    public function auth(Request $request){
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
            // return dd('OK');
        } else {
            session()->flash('error', 'Username / Password salah');
            return redirect('login');
        }
    }
    public function dashboard(){
        $user = Auth::user()->role;
        switch ($user) {
            case 'kepkam':
                return redirect('/kepkam');
            case 'keamanan':
                return redirect('/keamanan');
            case 'pengasuh':
                return redirect('/');
            case 'mahadiyah':
                return redirect('/mahadiyah');
            case 'kantor':
                return redirect('/kantor');
            default:
                return redirect('/login');
        }
    }
    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
    public function santrikepkam(){
        $nis = Auth::user()->id;
        $asrama = Asrama::select('id')->where('kepkam', $nis)->first();
        $data = Santri::select('nis','nama')
            ->where('nama', 'like', '%'.request('q').'%')
            ->where('asr_id', $asrama)
            ->paginate(10);
        return response()->json($data);
    }
    public function santri(){
        $data = Santri::select('nis','nama')
            ->where('nama', 'like', '%'.request('q').'%')
            ->paginate(10);
        return response()->json($data);
    }
    public function kepkam(){
        $data = Pengurus::select('nis','nama')
            ->where('nama', 'like', '%'.request('q').'%')
            ->paginate(10);
        return response()->json($data);
    }
    public function pengurus(){
        $data = Pengurus::select('nis','nama')
            ->where('nama', 'like', '%'.request('q').'%')
            ->paginate(10);
        return response()->json($data);
    }
}
