<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Madin extends Controller
{
    public function dashboard(){
        return view('diniyah.dashboard');
    }
    public function absensi()
    {
        return view('diniyah.absensi-diniyah');
    }
    public function pengajar()
    {
        return view('diniyah.absensi-pengajar');
    }
}
