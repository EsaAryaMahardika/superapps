<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Asrama;
use App\Models\Pengurus;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total'     => User::count(),
            'admin'     => User::where('role', 'admin')->count(),
            'mahadiyah' => User::where('role', 'mahadiyah')->count(),
            'kepkam'    => User::where('role', 'kepkam')->count(),
            'keamanan'  => User::where('role', 'keamanan')->count(),
            'kantor'    => User::where('role', 'kantor')->count(),
            'madin'     => User::where('role', 'madin')->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function index(Request $request)
    {
        $query = User::with('pengurus');
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $query->where('username', 'like', '%' . $request->q . '%');
        }
        $users = $query->orderBy('role')->orderBy('username')->paginate(20)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function create()
    {
        $pengurus = Pengurus::orderBy('nama')->get();
        return view('admin.create', compact('pengurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:pengurus,nis|unique:user,username',
            'password' => 'required|string|min:4',
            'role'     => 'required|in:admin,mahadiyah,kepkam,keamanan,kantor,madin',
        ]);

        User::create([
            'username' => $request->username,
            'password' => $request->password, // auto-hashed via cast
            'role'     => $request->role,
        ]);

        return redirect('/admin/users')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $pengurus = Pengurus::orderBy('nama')->get();
        return view('admin.edit', compact('user', 'pengurus'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|exists:pengurus,nis|unique:user,username,' . $id,
            'role'     => 'required|in:admin,mahadiyah,kepkam,keamanan,kantor,madin',
            'password' => 'nullable|string|min:4',
        ]);

        $data = ['username' => $request->username, 'role' => $request->role];
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);
        return redirect('/admin/users')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->username === Auth::user()->username) {
            return redirect('/admin/users')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect('/admin/users')->with('success', 'Akun berhasil dihapus.');
    }

    public function santri(Request $request)
    {
        $query = Santri::query();
        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('kepkam')) {
            $query->where('kepkam', $request->kepkam);
        }
        $santri  = $query->orderBy('nama')->paginate(30)->withQueryString();
        $kepkams = Pengurus::with('jabatan.divisi')
            ->whereIn('nis', Santri::select('kepkam')->distinct()->pluck('kepkam'))
            ->orderBy('nama')->get();
        return view('admin.santri', compact('santri', 'kepkams'));
    }

    public function pengurus(Request $request)
    {
        $query = Pengurus::with('jabatan.divisi');
        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%');
        }
        $pengurus = $query->orderBy('nama')->paginate(25)->withQueryString();
        return view('admin.pengurus', compact('pengurus'));
    }

    public function pengurusEdit($nis)
    {
        $pengurus = Pengurus::where('nis', $nis)->firstOrFail();
        return view('admin.pengurus-edit', compact('pengurus'));
    }

    public function pengurusUpdate(Request $request, $nis)
    {
        $pengurus = Pengurus::where('nis', $nis)->firstOrFail();

        $request->validate([
            'nis'  => 'required|string|max:20|unique:pengurus,nis,' . $nis . ',nis',
            'nama' => 'required|string|max:100',
        ]);

        if ($request->nis !== $nis) {
            User::where('username', $nis)->update(['username' => $request->nis]);
        }

        $pengurus->update(['nis' => $request->nis, 'nama' => $request->nama]);
        return redirect('/admin/pengurus')->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function generateNis()
    {
        do {
            $nis = (string) random_int(100000000, 999999999);
        } while (Pengurus::where('nis', $nis)->exists());

        return response()->json(['nis' => $nis]);
    }

    // ------------------- //
    // ASRAMA & KAMAR       //
    // ------------------- //
    public function asrama()
    {
        $asrama = Asrama::withCount('kamar')->orderBy('nama')->get();
        return view('admin.asrama', compact('asrama'));
    }

    public function asramaStore(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:50']);
        Asrama::create(['nama' => $request->nama]);
        return redirect('/admin/asrama')->with('success', 'Asrama berhasil ditambahkan.');
    }

    public function kamarIndex($asrama_id)
    {
        $asrama = Asrama::findOrFail($asrama_id);
        $kamar  = Kamar::with('kepkam.jabatan.divisi')->where('asrama_id', $asrama_id)->orderBy('nama')->get();
        $kepkamList = Pengurus::whereHas('jabatan.divisi', fn($q) => $q->where('tipe', 'kepkam'))
            ->orderBy('nama')->get();
        return view('admin.kamar', compact('asrama', 'kamar', 'kepkamList'));
    }

    public function kamarStore(Request $request, $asrama_id)
    {
        Asrama::findOrFail($asrama_id);
        $request->validate([
            'kepkam_nis' => 'required|exists:pengurus,nis',
        ]);
        $pengurus = Pengurus::where('nis', $request->kepkam_nis)->firstOrFail();
        Kamar::create([
            'asrama_id'  => $asrama_id,
            'nama'       => $pengurus->nama,
            'kepkam_nis' => $pengurus->nis,
        ]);
        return redirect("/admin/asrama/{$asrama_id}/kamar")->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function kamarUpdate(Request $request, $asrama_id, $kamar_id)
    {
        $kamar = Kamar::where('asrama_id', $asrama_id)->findOrFail($kamar_id);
        $request->validate([
            'kepkam_nis' => 'required|exists:pengurus,nis',
        ]);
        $pengurus = Pengurus::where('nis', $request->kepkam_nis)->firstOrFail();
        $kamar->update([
            'nama'       => $pengurus->nama,
            'kepkam_nis' => $pengurus->nis,
        ]);
        return redirect("/admin/asrama/{$asrama_id}/kamar")->with('success', 'Kamar berhasil diperbarui.');
    }

    public function kamarDestroy($asrama_id, $kamar_id)
    {
        Kamar::where('asrama_id', $asrama_id)->findOrFail($kamar_id)->delete();
        return redirect("/admin/asrama/{$asrama_id}/kamar")->with('success', 'Kamar berhasil dihapus.');
    }

    public function kamarSantri($asrama_id, $kamar_id)
    {
        $kamar   = Kamar::with('kepkam.jabatan.divisi', 'asrama')->where('asrama_id', $asrama_id)->findOrFail($kamar_id);
        $santri  = Santri::where('kepkam', $kamar->kepkam_nis)->orderBy('nama')->get();
        $semuaSantri = Santri::whereNull('kepkam')->orWhere('kepkam', '')->orderBy('nama')->get();
        return view('admin.kamar-santri', compact('kamar', 'santri', 'semuaSantri'));
    }

    public function kamarAssignSantri(Request $request, $asrama_id, $kamar_id)
    {
        $kamar = Kamar::where('asrama_id', $asrama_id)->findOrFail($kamar_id);
        $request->validate([
            'santri' => 'required|array|min:1',
            'santri.*' => 'exists:santri,nis',
        ]);
        Santri::whereIn('nis', $request->santri)->update(['kepkam' => $kamar->kepkam_nis]);
        return redirect("/admin/asrama/{$asrama_id}/kamar/{$kamar_id}/santri")
            ->with('success', count($request->santri) . ' santri berhasil diassign.');
    }

    public function kamarUnassignSantri($asrama_id, $kamar_id, $nis)
    {
        Kamar::where('asrama_id', $asrama_id)->findOrFail($kamar_id);
        Santri::where('nis', $nis)->update(['kepkam' => null]);
        return redirect("/admin/asrama/{$asrama_id}/kamar/{$kamar_id}/santri")
            ->with('success', 'Santri berhasil dilepas dari kamar.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->update(['password' => '1234']);
        return redirect('/admin/users')->with('success', 'Password direset ke 1234.');
    }
}
