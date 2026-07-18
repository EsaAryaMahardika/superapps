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
            case 'admin':
                return redirect('/admin');
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
            case 'madin':
                return redirect('/madin');
            default:
                return redirect('/login');
        }
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function santrikepkam(){
        $nis = Auth::user()->username;
        $asrama = Asrama::select('id')->where('kepkam', $nis)->first();
        $data = Santri::select('nis','nama')
            ->where('nama', 'like', '%'.request('q').'%')
            ->when($asrama, fn($q) => $q->where('asr_id', $asrama->id))
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
