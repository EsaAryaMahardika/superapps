<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\AlasanIzin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Purify\Facades\Purify;

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
    public function perizinan()
    {
        if (!in_array($this->user->role, ['kepkam', 'keamanan'])) {
            return redirect('/');
        }

        $santri    = Santri::select('nis', 'nama')->get();
        $alasan    = AlasanIzin::all();
        $perizinan = Perizinan::all();
        $view      = $this->user->role === 'kepkam' ? 'kepkam.perizinan' : 'keamanan.perizinan';

        return view($view, compact('santri', 'alasan', 'perizinan'));
    }
    public function createizin(Request $request)
    {
        $request->validate([
            'nis'    => 'required|string|exists:santri,nis',
            'jenis'  => 'required|in:K,P',
            'alasan' => 'required|integer|exists:alasan_izin,id',
            'kembali' => 'required|date',
        ]);

        try {
            Perizinan::updateOrCreate(
                ['nis' => $request->nis],
                [
                    'jenis' => $request->jenis,
                    'alasan' => $request->alasan,
                    'berangkat' => Carbon::now(),
                    'es_kembali' => Carbon::parse($request->kembali),
                    'status' => 0, // 0: Pending/Proses
                ]
            );
            return redirect('/perizinan')->with('success', 'Perizinan berhasil dibuat');
        } catch (\Throwable $e) {
            \Log::error('[Perizinan] createizin failed', ['error' => $e->getMessage()]);
            return redirect('/perizinan')->with('error', 'Gagal membuat perizinan.');
        }
    }
    public function accizin(Request $request, $nis)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $table = match ($this->user->role) {
            'kepkam'   => 'acckepkam',
            'keamanan' => 'acckeamanan',
            'pengasuh' => 'accpengasuh',
            default    => abort(403),
        };

        $perizinan = Perizinan::where('nis', $nis)->firstOrFail();
        $perizinan->update([
            $table   => Carbon::now(),
            'status' => $request->status,
        ]);

        return redirect('/perizinan');
    }

    public function lapor(Request $request, $nis)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1,2',
        ]);

        $table = match ($this->user->role) {
            'kepkam'   => 'laporkepkam',
            'keamanan' => 'laporkeamanan',
            'pengasuh' => 'laporpengasuh',
            default    => abort(403),
        };

        $perizinan = Perizinan::where('nis', $nis)->firstOrFail();
        $perizinan->update([
            $table   => Carbon::now(),
            'status' => $request->status,
        ]);

        return redirect('/perizinan');
    }
}
