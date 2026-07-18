<?php

namespace App\Http\Controllers\Mahadiyah;

use App\Models\Santri;
use App\Models\Pengurus;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        $query = Santri::query();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('nis', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('kepkam')) {
            $query->where('kepkam', $request->kepkam);
        }

        $kepkams     = Pengurus::with('jabatan.divisi')
            ->whereHas('jabatan.divisi', fn($q) => $q->where('tipe', 'kepkam'))
            ->orderBy('nama')->get()->keyBy('nis');

        $totalSantri = $query->count();
        $allSantri   = $query->orderBy('kepkam')->orderBy('nama')->get();
        $grouped     = $allSantri->groupBy('kepkam');

        return view('mahadiyah.santri', compact('grouped', 'kepkams', 'totalSantri'));
    }

    public function store(Request $request)
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

        return redirect('/mahadiyah/santri')->with('success', 'Santri berhasil ditambahkan.');
    }

    public function update(Request $request, $nis)
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

        return redirect('/mahadiyah/santri' . '?' . http_build_query(request()->except('_token', '_method')))
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy($nis)
    {
        Santri::where('nis', $nis)->firstOrFail()->delete();
        return redirect('/mahadiyah/santri' . '?' . http_build_query(request()->except('_token', '_method')))
            ->with('success', 'Santri berhasil dihapus.');
    }

    public function import(Request $request)
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

            $kepkamNis = null;
            if (!empty($kepkam)) {
                if (Pengurus::where('nis', $kepkam)->exists()) {
                    $kepkamNis = $kepkam;
                } else {
                    $errors[] = "Baris {$row}: Kepkam NIS {$kepkam} tidak ditemukan, santri disimpan tanpa kepkam.";
                }
            }

            Santri::create(['nis' => $nis, 'nama' => $nama, 'kepkam' => $kepkamNis]);
            $success++;
        }

        fclose($handle);

        $msg = "{$success} santri berhasil diimport.";
        if ($skipped > 0) $msg .= " {$skipped} baris dilewati.";

        $sessionData = ['success' => $msg];
        if (!empty($errors)) {
            $sessionData['import_errors'] = array_slice($errors, 0, 10);
        }

        return redirect('/mahadiyah/santri')->with($sessionData);
    }

    public function template()
    {
        $filename = 'template_import_santri.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nis', 'nama', 'kepkam']);
            fputcsv($file, ['123456789', 'Ahmad Fauzi', '']);
            fputcsv($file, ['987654321', 'Muhammad Ali', '724233185']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
