<?php

namespace App\Http\Controllers\Mahadiyah;

use App\Models\Pengurus;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PengurusController extends Controller
{
    public function index()
    {
        $divisiKepkam = Divisi::where('tipe', 'kepkam')
            ->with(['jabatan.pengurus'])
            ->orderBy('nama')
            ->get();

        $divisiNon = Divisi::where('tipe', 'non')
            ->with(['jabatan.pengurus'])
            ->orderBy('nama')
            ->get();

        $allJabatan = Jabatan::with('divisi')->orderBy('divisi_id')->orderBy('nama')->get();

        return view('mahadiyah.pengurus', compact('divisiKepkam', 'divisiNon', 'allJabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'        => 'required|string|max:20|unique:pengurus,nis',
            'nama'       => 'required|string|max:100',
            'jabatan_id' => 'nullable|exists:jabatan,id',
        ]);

        Pengurus::create([
            'nis'        => $request->nis,
            'nama'       => $request->nama,
            'jabatan_id' => $request->jabatan_id ?: null,
        ]);

        session()->flash('success', 'Pengurus berhasil ditambahkan.');
        return redirect('/mahadiyah/pengurus');
    }

    public function generateNis()
    {
        do {
            $nis = (string) random_int(100000000, 999999999);
        } while (Pengurus::where('nis', $nis)->exists());

        return response()->json(['nis' => $nis]);
    }

    public function update(Request $request, $nis)
    {
        $pengurus = Pengurus::where('nis', $nis)->firstOrFail();

        $request->validate([
            'nis'        => 'required|string|max:20|unique:pengurus,nis,' . $nis . ',nis',
            'nama'       => 'required|string|max:100',
            'jabatan_id' => 'nullable|exists:jabatan,id',
        ]);

        if ($request->nis !== $nis) {
            User::where('username', $nis)->update(['username' => $request->nis]);
        }

        $pengurus->update([
            'nis'        => $request->nis,
            'nama'       => $request->nama,
            'jabatan_id' => $request->jabatan_id ?: null,
        ]);

        session()->flash('success', 'Data pengurus berhasil diperbarui.');
        return redirect('/mahadiyah/pengurus');
    }

    public function destroy($nis)
    {
        Pengurus::where('nis', $nis)->firstOrFail()->delete();
        session()->flash('success', 'Pengurus berhasil dihapus.');
        return redirect('/mahadiyah/pengurus');
    }

    // ------------------- //
    // CRUD DIVISI          //
    // ------------------- //
    public function divisiStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:kepkam,non',
        ]);

        Divisi::create(['nama' => $request->nama, 'tipe' => $request->tipe]);

        session()->flash('success', 'Divisi berhasil ditambahkan.');
        return redirect('/mahadiyah/pengurus');
    }

    public function divisiUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:kepkam,non',
        ]);

        Divisi::findOrFail($id)->update(['nama' => $request->nama, 'tipe' => $request->tipe]);

        session()->flash('success', 'Divisi berhasil diperbarui.');
        return redirect('/mahadiyah/pengurus');
    }

    public function divisiDestroy($id)
    {
        Divisi::findOrFail($id)->delete();
        session()->flash('success', 'Divisi berhasil dihapus.');
        return redirect('/mahadiyah/pengurus');
    }

    // ------------------- //
    // CRUD JABATAN         //
    // ------------------- //
    public function jabatanStore(Request $request)
    {
        $request->validate([
            'divisi_id' => 'required|exists:divisi,id',
            'nama'      => 'required|array|min:1',
            'nama.*'    => 'required|string|max:100',
        ]);

        $divisiId = $request->divisi_id;
        $now = now();

        $rows = collect($request->nama)
            ->filter(fn($n) => trim($n) !== '')
            ->map(fn($n) => [
                'divisi_id'  => $divisiId,
                'nama'       => trim($n),
                'created_at' => $now,
                'updated_at' => $now,
            ])->values()->all();

        if (empty($rows)) {
            session()->flash('error', 'Minimal isi satu nama jabatan.');
            return redirect('/mahadiyah/pengurus');
        }

        Jabatan::insert($rows);

        $count = count($rows);
        session()->flash('success', "$count jabatan berhasil ditambahkan.");
        return redirect('/mahadiyah/pengurus');
    }

    public function jabatanUpdate(Request $request, $id)
    {
        $request->validate([
            'divisi_id' => 'required|exists:divisi,id',
            'nama'      => 'required|string|max:100',
        ]);

        Jabatan::findOrFail($id)->update([
            'divisi_id' => $request->divisi_id,
            'nama'      => $request->nama,
        ]);

        session()->flash('success', 'Jabatan berhasil diperbarui.');
        return redirect('/mahadiyah/pengurus');
    }

    public function jabatanDestroy($id)
    {
        Jabatan::findOrFail($id)->delete();
        session()->flash('success', 'Jabatan berhasil dihapus.');
        return redirect('/mahadiyah/pengurus');
    }
}
