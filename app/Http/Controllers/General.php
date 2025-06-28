<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            // return 'Berhasil';
        }
        // return redirect('/login')->with('error', 'Invalid credentials');
        return 'Gagal';
    }
    public function dashboard(){
        $user = Auth::user()->role;
        switch ($user) {
            case 'kepkam':
                return redirect('/');
            case 'keamanan':
                return redirect('/keamanan');
            case 'pengasuh':
                return redirect('/');
            default:
                return redirect('/');
        }
    }
    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
}
