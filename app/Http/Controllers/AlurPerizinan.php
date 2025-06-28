<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\AlasanIzin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AlurPerizinan extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function accizin(Request $request, $nis){
        try {
            switch ($this->user->role) {
                case 'kepkam': $table = 'acckepkam'; break;
                case 'keamanan': $table = 'acckeamanan'; break;
                case 'pengasuh': $table = 'accpengasuh'; break;
                default: throw new \Exception((string) $this->user->role);
            }
            $santri = Perizinan::where('nis', $nis)->first();
            $santri->update([
                $table => Carbon::now(),
                'status' => $request->status,
            ]);
            return redirect('/perizinan');
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function lapor(Request $request, $nis){
        try {
            $table = match ($this->user->role) {
                'kepkam' => 'laporkepkam',
                'keamanan' => 'laporkeamanan',
                'pengasuh' => 'laporpengasuh',
                default => null,
            };
            $santri = Perizinan::where('nis', $nis)->first();
            $santri->update([
                $table => Carbon::now(),
                'status' => $request->status,
            ]);
            return redirect('/perizinan');
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
