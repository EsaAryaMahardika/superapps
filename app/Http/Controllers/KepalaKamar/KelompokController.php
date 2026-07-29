<?php

namespace App\Http\Controllers\KepalaKamar;

use App\Models\Santri;
use App\Models\KelompokSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class KelompokController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        $kelompok = KelompokSantri::where('kepkam_nis', $this->user->username)
            ->withCount('santri')
            ->with('santri')
            ->orderBy('nama')
            ->get();

        $santri = Santri::select('nis', 'nama', 'kelompok_id')
            ->where('kepkam', $this->user->username)
            ->orderBy('nama')
            ->get();

        return view('kepkam.kelompok', compact('kelompok', 'santri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        KelompokSantri::create([
            'nama'       => $request->nama,
            'kepkam_nis' => $this->user->username,
            'keterangan' => $request->keterangan,
        ]);

        session()->flash('success', 'Kelompok berhasil dibuat');
        return redirect('/kepkam/kelompok');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'       => 'required|string|max:100',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kelompok = KelompokSantri::where('id', $id)
            ->where('kepkam_nis', $this->user->username)
            ->firstOrFail();

        $kelompok->update([
            'nama'       => $request->nama,
            'keterangan' => $request->keterangan,
        ]);

        session()->flash('success', 'Kelompok berhasil diperbarui');
        return redirect('/kepkam/kelompok');
    }

    public function destroy($id)
    {
        $kelompok = KelompokSantri::where('id', $id)
            ->where('kepkam_nis', $this->user->username)
            ->firstOrFail();

        // Reset santri yang ada di kelompok ini
        Santri::where('kelompok_id', $kelompok->id)->update(['kelompok_id' => null]);

        $kelompok->delete();

        session()->flash('success', 'Kelompok berhasil dihapus');
        return redirect('/kepkam/kelompok');
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'santri'   => 'required|array|min:1',
            'santri.*' => 'required|string|exists:santri,nis',
        ]);

        $kelompok = KelompokSantri::where('id', $id)
            ->where('kepkam_nis', $this->user->username)
            ->firstOrFail();

        // Pastikan hanya santri milik kepkam ini yang di-assign
        Santri::where('kepkam', $this->user->username)
            ->whereIn('nis', $request->santri)
            ->update(['kelompok_id' => $kelompok->id]);

        session()->flash('success', count($request->santri) . ' santri berhasil dimasukkan ke kelompok "' . $kelompok->nama . '"');
        return redirect('/kepkam/kelompok');
    }

    public function unassign(Request $request, $id)
    {
        $request->validate([
            'santri'   => 'required|array|min:1',
            'santri.*' => 'required|string|exists:santri,nis',
        ]);

        $kelompok = KelompokSantri::where('id', $id)
            ->where('kepkam_nis', $this->user->username)
            ->firstOrFail();

        Santri::where('kepkam', $this->user->username)
            ->where('kelompok_id', $kelompok->id)
            ->whereIn('nis', $request->santri)
            ->update(['kelompok_id' => null]);

        session()->flash('success', count($request->santri) . ' santri berhasil dikeluarkan dari kelompok');
        return redirect('/kepkam/kelompok');
    }
}
