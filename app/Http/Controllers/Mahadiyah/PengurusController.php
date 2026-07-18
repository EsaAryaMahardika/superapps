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

    public function templatePengurus()
    {
        $filename = 'template_import_pengurus.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $jabatanList = Jabatan::with('divisi')->orderBy('divisi_id')->orderBy('nama')->get();

        $callback = function () use ($jabatanList) {
            $file = fopen('php://output', 'w');

            // Data pengurus
            fputcsv($file, ['nis', 'nama', 'jabatan_id']);
            fputcsv($file, ['123456789', 'Ahmad Fauzi', '6']);
            fputcsv($file, ['987654321', 'Muhammad Ali', '']);

            // Referensi jabatan
            fputcsv($file, []);
            fputcsv($file, ['--- REFERENSI JABATAN (salin jabatan_id ke kolom jabatan_id di atas) ---']);
            fputcsv($file, ['jabatan_id', 'nama_jabatan', 'divisi', 'tipe']);
            foreach ($jabatanList as $j) {
                fputcsv($file, [$j->id, $j->nama, $j->divisi->nama ?? '-', $j->divisi->tipe ?? '-']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importPengurus(Request $request)
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
                    'nis'        => trim($line[0] ?? ''),
                    'nama'       => trim($line[1] ?? ''),
                    'jabatan_id' => trim($line[2] ?? ''),
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

            $validJabatanId = null;
            if (!empty($jabatanId)) {
                if (Jabatan::where('id', $jabatanId)->exists()) {
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

        return redirect('/mahadiyah/pengurus')->with($sessionData);
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
