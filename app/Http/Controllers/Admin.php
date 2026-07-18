<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Asrama;
use App\Models\Pengurus;
use App\Models\Santri;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin extends Controller
{
    public function logs(Request $request)
    {
        $query = ActivityLog::latest();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs    = $query->paginate(50)->withQueryString();
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');

        // Pre-load nama pengurus untuk semua username di halaman ini (hindari N+1)
        $usernames   = $logs->pluck('username')->filter()->unique()->values();
        $namaPengurus = Pengurus::whereIn('nis', $usernames)->pluck('nama', 'nis');

        return view('admin.logs', compact('logs', 'modules', 'namaPengurus'));
    }

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

        $logs = ActivityLog::latest()->take(50)->get();

        return view('admin.dashboard', compact('stats', 'logs'));
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
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%');
            });
        }
        if ($request->filled('kepkam')) {
            $query->where('kepkam', $request->kepkam);
        }

        $kepkams = Pengurus::with('jabatan.divisi')
            ->whereHas('jabatan.divisi', fn($q) => $q->where('tipe', 'kepkam'))
            ->orderBy('nama')->get()->keyBy('nis');

        $totalSantri = $query->count();

        // Group by kepkam
        $allSantri = $query->orderBy('kepkam')->orderBy('nama')->get();
        $grouped   = $allSantri->groupBy('kepkam');

        return view('admin.santri', compact('grouped', 'kepkams', 'totalSantri'));
    }

    public function santriImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file    = $request->file('file');
        $handle  = fopen($file->getPathname(), 'r');
        $header  = null;
        $success = 0;
        $skipped = 0;
        $errors  = [];
        $row     = 0;

        // Deteksi separator otomatis: baca baris pertama dan cek , atau ;
        $firstLine = fgets($handle);
        rewind($handle);
        $separator = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';

        while (($line = fgetcsv($handle, 1000, $separator)) !== false) {
            $row++;

            // Baris pertama = header, skip
            if ($row === 1) {
                $header = array_map('strtolower', array_map('trim', $line));
                continue;
            }

            // Skip baris kosong
            if (empty(array_filter($line))) continue;

            // Map kolom
            $data = [];
            if ($header) {
                foreach ($header as $i => $col) {
                    $data[$col] = trim($line[$i] ?? '');
                }
            } else {
                // Tanpa header: kolom 0=nis, 1=nama, 2=kepkam
                $data = [
                    'nis'    => trim($line[0] ?? ''),
                    'nama'   => trim($line[1] ?? ''),
                    'kepkam' => trim($line[2] ?? ''),
                ];
            }

            $nis    = $data['nis']    ?? '';
            $nama   = $data['nama']   ?? '';
            $kepkam = $data['kepkam'] ?? '';

            if (empty($nis) || empty($nama)) {
                $errors[] = "Baris {$row}: NIS atau Nama kosong, dilewati.";
                $skipped++;
                continue;
            }

            if (Santri::where('nis', $nis)->exists()) {
                $errors[] = "Baris {$row}: NIS {$nis} sudah ada, dilewati.";
                $skipped++;
                continue;
            }

            // Validasi kepkam jika diisi
            $kepkamNis = null;
            if (!empty($kepkam)) {
                if (Pengurus::where('nis', $kepkam)->exists()) {
                    $kepkamNis = $kepkam;
                } else {
                    $errors[] = "Baris {$row}: Kepkam NIS {$kepkam} tidak ditemukan, santri tetap disimpan tanpa kepkam.";
                }
            }

            Santri::create([
                'nis'    => $nis,
                'nama'   => $nama,
                'kepkam' => $kepkamNis,
            ]);
            $success++;
        }

        fclose($handle);

        $msg = "{$success} santri berhasil diimport.";
        if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";

        $sessionData = ['success' => $msg];
        if (!empty($errors)) {
            $sessionData['import_errors'] = array_slice($errors, 0, 10); // max 10 pesan error
        }

        return redirect('/admin/santri')->with($sessionData);
    }

    public function santriTemplate()
    {
        $filename = 'template_import_santri.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Header baris pertama
            fputcsv($file, ['nis', 'nama', 'kepkam']);

            // Contoh data
            fputcsv($file, ['123456789', 'Ahmad Fauzi', '']);
            fputcsv($file, ['987654321', 'Muhammad Ali', '724233185']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function santriStore(Request $request)
    {
        $request->validate([
            'nis'    => 'required|string|max:20|unique:santri,nis',
            'nama'   => 'required|string|max:100',
            'kepkam' => 'nullable|exists:pengurus,nis',
        ]);

        Santri::create([
            'nis'    => $request->nis,
            'nama'   => $request->nama,
            'kepkam' => $request->kepkam ?: null,
        ]);

        return redirect('/admin/santri')->with('success', 'Santri berhasil ditambahkan.');
    }

    public function santriUpdate(Request $request, $nis)
    {
        $santri = Santri::where('nis', $nis)->firstOrFail();
        $request->validate([
            'nama'   => 'required|string|max:100',
            'kepkam' => 'nullable|exists:pengurus,nis',
        ]);
        $santri->update([
            'nama'   => $request->nama,
            'kepkam' => $request->kepkam ?: null,
        ]);
        return redirect('/admin/santri' . '?' . http_build_query(request()->except('_token', '_method')))
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    public function santriDestroy($nis)
    {
        Santri::where('nis', $nis)->firstOrFail()->delete();
        return redirect('/admin/santri' . '?' . http_build_query(request()->except('_token', '_method')))
            ->with('success', 'Santri berhasil dihapus.');
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

    public function pengurusTemplate()
    {
        $filename = 'template_import_pengurus.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $jabatanList = \App\Models\Jabatan::with('divisi')->orderBy('divisi_id')->orderBy('nama')->get();

        $callback = function () use ($jabatanList) {
            $file = fopen('php://output', 'w');

            // Sheet 1: Data pengurus
            fputcsv($file, ['nis', 'nama', 'jabatan_id']);
            fputcsv($file, ['123456789', 'Ahmad Fauzi', '6']);
            fputcsv($file, ['987654321', 'Muhammad Ali', '']);

            // Separator
            fputcsv($file, []);
            fputcsv($file, ['--- REFERENSI JABATAN (salin jabatan_id ke kolom jabatan_id di atas) ---']);
            fputcsv($file, ['jabatan_id', 'nama_jabatan', 'divisi', 'tipe']);

            foreach ($jabatanList as $j) {
                fputcsv($file, [
                    $j->id,
                    $j->nama,
                    $j->divisi->nama ?? '-',
                    $j->divisi->tipe ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pengurusImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file    = $request->file('file');
        $handle  = fopen($file->getPathname(), 'r');
        $header  = null;
        $success = 0;
        $skipped = 0;
        $errors  = [];
        $row     = 0;

        // Deteksi separator otomatis
        $firstLine = fgets($handle);
        rewind($handle);
        $separator = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';

        while (($line = fgetcsv($handle, 1000, $separator)) !== false) {
            $row++;

            if ($row === 1) {
                $header = array_map('strtolower', array_map('trim', $line));
                continue;
            }

            if (empty(array_filter($line))) continue;

            $data = [];
            if ($header) {
                foreach ($header as $i => $col) {
                    $data[$col] = trim($line[$i] ?? '');
                }
            } else {
                $data = [
                    'nis'  => trim($line[0] ?? ''),
                    'nama' => trim($line[1] ?? ''),
                ];
            }

            $nis       = $data['nis']        ?? '';
            $nama      = $data['nama']       ?? '';
            $jabatanId = $data['jabatan_id'] ?? '';

            if (empty($nis) || empty($nama)) {
                $errors[] = "Baris {$row}: NIS atau Nama kosong, dilewati.";
                $skipped++;
                continue;
            }

            if (Pengurus::where('nis', $nis)->exists()) {
                $errors[] = "Baris {$row}: NIS {$nis} sudah ada, dilewati.";
                $skipped++;
                continue;
            }

            // Validasi jabatan_id jika diisi
            $validJabatanId = null;
            if (!empty($jabatanId)) {
                if (\App\Models\Jabatan::where('id', $jabatanId)->exists()) {
                    $validJabatanId = (int) $jabatanId;
                } else {
                    $errors[] = "Baris {$row}: jabatan_id {$jabatanId} tidak ditemukan, pengurus disimpan tanpa jabatan.";
                }
            }

            Pengurus::create(['nis' => $nis, 'nama' => $nama, 'jabatan_id' => $validJabatanId]);
            $success++;
        }

        fclose($handle);

        $msg = "{$success} pengurus berhasil diimport.";
        if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";

        $sessionData = ['success' => $msg];
        if (!empty($errors)) {
            $sessionData['import_errors'] = array_slice($errors, 0, 10);
        }

        return redirect('/admin/pengurus')->with($sessionData);
    }

    public function asramaUpdate(Request $request, $id)
    {
        $asrama = Asrama::findOrFail($id);
        $request->validate(['nama' => 'required|string|max:50']);
        $asrama->update(['nama' => $request->nama]);
        return redirect('/admin/asrama')->with('success', 'Asrama berhasil diperbarui.');
    }

    public function asramaDestroy($id)
    {
        $asrama = Asrama::findOrFail($id);
        if ($asrama->kamar()->count() > 0) {
            return redirect('/admin/asrama')->with('error', 'Asrama tidak bisa dihapus karena masih memiliki kamar.');
        }
        $asrama->delete();
        return redirect('/admin/asrama')->with('success', 'Asrama berhasil dihapus.');
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
