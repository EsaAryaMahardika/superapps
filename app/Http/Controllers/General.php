<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
}
