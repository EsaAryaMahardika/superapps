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
    public function perizinan() {
        $santri = match ($this->user->role) {
            // 'kepkam' => Santri::select(['nis','nama'])->where('kamar_id', $this->user->kamar_id)->get(),
            'kepkam' => Santri::select('nis','nama')->get(),
            'keamanan' => Santri::select('nis','nama')->get(),
            default => redirect('/'),
        };
        $alasan = AlasanIzin::all();
        $perizinan = match ($this->user->role) {
            // 'kepkam' => Perizinan::whereHas('santri', fn($q) => $q->where('kamar_id', $this->user->kamar_id))->get(),
            'kepkam' => Perizinan::all(),
            'keamanan' => Perizinan::all(),
            default => redirect('/'),
        };
        $view = match ($this->user->role) {
            'kepkam' => 'kepkam.perizinan',
            'keamanan' => 'keamanan.perizinan',
            default => redirect('/'),
        };
        return view($view, compact('santri', 'alasan', 'perizinan'));
    }
    public function createizin(Request $request) {
        $nis = $request->nis;
    }
    public function accizin(Request $request, $nis){
        try {
            switch ($this->user->role) {
                case 'kepkam': $table = 'acckepkam'; break;
                case 'keamanan': $table = 'acckeamanan'; break;
                case 'pengasuh': $table = 'accpengasuh'; break;
                default: redirect('/');
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
                default => redirect('/'),
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
