<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Mahadiyah extends Controller
{
    public function dashboard(){
        return view('mahadiyah.dashboard');
    }
    public function absensi()
    {
        return view('mahadiyah.absensi-pengurus');
    }
    public function mingguan()
    {
        return view('mahadiyah.absen-mingguan');
    }
}
