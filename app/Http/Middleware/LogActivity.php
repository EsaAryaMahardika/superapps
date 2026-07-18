<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\Pengurus;
use App\Models\Santri;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    private array $map = [
        // Auth
        'General@auth'    => ['auth.login',  'Login ke sistem', 'auth'],
        'General@logout'  => ['auth.logout', 'Logout dari sistem', 'auth'],

        // Admin
        'Admin@store'              => ['admin.user.store',      'Membuat akun user baru', 'admin'],
        'Admin@update'             => ['admin.user.update',     'Mengubah data akun user', 'admin'],
        'Admin@destroy'            => ['admin.user.destroy',    'Menghapus akun user', 'admin'],
        'Admin@resetPassword'      => ['admin.user.reset',      'Mereset password user', 'admin'],
        'Admin@kamarAssignSantri'  => ['admin.kamar.assign',    'Assign santri ke kamar', 'admin'],
        'Admin@kamarUnassignSantri'=> ['admin.kamar.unassign',  'Melepas santri dari kamar', 'admin'],
        'Admin@asramaStore'        => ['admin.asrama.store',    'Menambah asrama baru', 'admin'],
        'Admin@asramaUpdate'       => ['admin.asrama.update',   'Mengubah data asrama', 'admin'],
        'Admin@asramaDestroy'      => ['admin.asrama.destroy',  'Menghapus asrama', 'admin'],
        'Admin@kamarStore'         => ['admin.kamar.store',     'Menambah kamar baru', 'admin'],
        'Admin@kamarUpdate'        => ['admin.kamar.update',    'Mengubah data kamar', 'admin'],
        'Admin@kamarDestroy'       => ['admin.kamar.destroy',   'Menghapus kamar', 'admin'],
        'Admin@pengurusUpdate'     => ['admin.pengurus.update', 'Mengubah data pengurus (admin)', 'admin'],
        'Admin@santriStore'        => ['admin.santri.store',    'Menambah santri baru', 'admin'],
        'Admin@santriUpdate'       => ['admin.santri.update',   'Mengubah data santri', 'admin'],
        'Admin@santriDestroy'      => ['admin.santri.destroy',  'Menghapus santri', 'admin'],
        'Admin@santriImport'       => ['admin.santri.import',   'Import santri dari CSV', 'admin'],
        'Admin@pengurusImport'     => ['admin.pengurus.import', 'Import pengurus dari CSV', 'admin'],

        // KepalaKamar - Absensi
        'KepalaKamar\AbsensiController@store'   => ['kepkam.absensi.store',   'Membuat absensi santri', 'kepkam'],
        'KepalaKamar\AbsensiController@destroy' => ['kepkam.absensi.destroy', 'Menghapus absensi santri', 'kepkam'],

        // KepalaKamar - Rekap
        'KepalaKamar\RekapController@storeMingguan'       => ['kepkam.mingguan.store',  'Membuat catatan mingguan santri', 'kepkam'],
        'KepalaKamar\RekapController@downloadRekapHarian' => ['kepkam.rekap.download',  'Download rekap harian PDF', 'kepkam'],

        // Mahadiyah - Absensi
        'Mahadiyah\AbsensiController@store'       => ['mahadiyah.absensi.store',   'Membuat absensi pengurus', 'mahadiyah'],
        'Mahadiyah\AbsensiController@update'      => ['mahadiyah.absensi.update',  'Mengubah absensi pengurus', 'mahadiyah'],
        'Mahadiyah\AbsensiController@liburStore'  => ['mahadiyah.libur.store',     'Menandai libur kegiatan pengurus', 'mahadiyah'],
        'Mahadiyah\AbsensiController@liburDestroy'=> ['mahadiyah.libur.destroy',   'Membatalkan libur kegiatan pengurus', 'mahadiyah'],

        // Mahadiyah - Pengurus
        'Mahadiyah\PengurusController@store'         => ['mahadiyah.pengurus.store',   'Menambah pengurus baru', 'mahadiyah'],
        'Mahadiyah\PengurusController@update'        => ['mahadiyah.pengurus.update',  'Mengubah data pengurus', 'mahadiyah'],
        'Mahadiyah\PengurusController@destroy'       => ['mahadiyah.pengurus.destroy', 'Menghapus pengurus', 'mahadiyah'],
        'Mahadiyah\PengurusController@divisiStore'   => ['mahadiyah.divisi.store',     'Menambah divisi baru', 'mahadiyah'],
        'Mahadiyah\PengurusController@divisiUpdate'  => ['mahadiyah.divisi.update',    'Mengubah divisi', 'mahadiyah'],
        'Mahadiyah\PengurusController@divisiDestroy' => ['mahadiyah.divisi.destroy',   'Menghapus divisi', 'mahadiyah'],
        'Mahadiyah\PengurusController@jabatanStore'  => ['mahadiyah.jabatan.store',    'Menambah jabatan baru', 'mahadiyah'],
        'Mahadiyah\PengurusController@jabatanUpdate' => ['mahadiyah.jabatan.update',   'Mengubah jabatan', 'mahadiyah'],
        'Mahadiyah\PengurusController@jabatanDestroy'=> ['mahadiyah.jabatan.destroy',  'Menghapus jabatan', 'mahadiyah'],

        // Mahadiyah - Rekap
        'Mahadiyah\RekapController@downloadRekapAbsensiPengurus' => ['mahadiyah.rekap.pdf',      'Download rekap absensi pengurus PDF', 'mahadiyah'],
        'Mahadiyah\RekapController@excelRekapAbsensiPengurus'    => ['mahadiyah.rekap.excel',    'Download rekap absensi pengurus Excel', 'mahadiyah'],
        'Mahadiyah\RekapController@downloadRekapKegiatan'        => ['mahadiyah.rekap.kegiatan', 'Download rekap kegiatan PDF', 'mahadiyah'],

        // Keamanan
        'Keamanan@i_pelanggaran' => ['keamanan.pelanggaran.store', 'Mencatat pelanggaran santri', 'keamanan'],

        // Perizinan
        'AlurPerizinan@createizin' => ['perizinan.store',  'Membuat pengajuan izin santri', 'perizinan'],
        'AlurPerizinan@accizin'    => ['perizinan.update', 'Mengkonfirmasi perizinan santri', 'perizinan'],
        'AlurPerizinan@lapor'      => ['perizinan.lapor',  'Melaporkan santri sudah kembali', 'perizinan'],
    ];

    public function handle(Request $request, Closure $next)
    {
        // Ambil data SEBELUM request diproses (untuk before/after comparison)
        $beforeData = $this->captureBeforeData($request);

        $response = $next($request);

        if (
            in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) &&
            Auth::check() &&
            ($response->isRedirection() || $response->isSuccessful())
        ) {
            $this->logRequest($request, $beforeData);
        }

        return $response;
    }

    /**
     * Ambil data lama sebelum request diproses untuk keperluan before/after.
     */
    private function captureBeforeData(Request $request): array
    {
        $route = $request->route();
        if (!$route) return [];

        $before = [];

        // User data (admin update/delete/reset)
        $userId = $route->parameter('id');
        if ($userId && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $user = User::find($userId);
            if ($user) {
                $before['user'] = ['username' => $user->username, 'role' => $user->role];
            }
        }

        // Pengurus data
        $nis = $route->parameter('nis');
        if ($nis && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $p = Pengurus::with('jabatan.divisi')->where('nis', $nis)->first();
            if ($p) {
                $before['pengurus'] = [
                    'nis'    => $p->nis,
                    'nama'   => $p->nama,
                    'jabatan'=> $p->jabatan?->nama ?? '-',
                    'divisi' => $p->jabatan?->divisi?->nama ?? '-',
                ];
            }
        }

        // Divisi data
        $divisiId = $route->parameter('id');
        if ($divisiId && str_contains($route->getActionName() ?? '', 'divisi')) {
            $divisi = Divisi::find($divisiId);
            if ($divisi) $before['divisi'] = ['nama' => $divisi->nama, 'tipe' => $divisi->tipe];
        }

        // Jabatan data
        if ($divisiId && str_contains($route->getActionName() ?? '', 'jabatan')) {
            $jabatan = Jabatan::with('divisi')->find($divisiId);
            if ($jabatan) $before['jabatan'] = ['nama' => $jabatan->nama, 'divisi' => $jabatan->divisi?->nama ?? '-'];
        }

        return $before;
    }

    private function logRequest(Request $request, array $beforeData): void
    {
        $route      = $request->route();
        $controller = class_basename($route?->getControllerClass() ?? '');
        $method     = $route?->getActionMethod() ?? '';

        $key   = $this->findMapKey($controller, $method, $route);
        $entry = $this->map[$key] ?? null;

        if ($entry) {
            ActivityLog::log($entry[0], $this->buildDescription($entry[0], $entry[1], $request, $beforeData), $entry[2]);
        } else {
            $methodLabel = match(strtoupper($request->method())) {
                'POST'         => 'Membuat data',
                'PUT', 'PATCH' => 'Mengubah data',
                'DELETE'       => 'Menghapus data',
                default        => 'Akses',
            };
            ActivityLog::log("{$controller}.{$method}", "{$methodLabel} — {$controller}/{$method}", $this->guessModule($request));
        }
    }

    private function findMapKey(string $controller, string $method, $route): string
    {
        $actionStr = $route?->getActionName() ?? '';
        foreach ($this->map as $key => $_) {
            if (str_ends_with($actionStr, $key)) return $key;
        }
        return "{$controller}@{$method}";
    }

    private function buildDescription(string $action, string $base, Request $request, array $before = []): string
    {
        $parts = [];

        // ── UPDATE USER (admin) ─────────────────────────────────────
        if (str_contains($action, 'admin.user.update')) {
            $oldUser = $before['user'] ?? null;
            $newUsername = $request->input('username');
            $newRole     = $request->input('role');

            if ($oldUser) {
                $parts[] = "User: {$oldUser['username']}";
                if ($newUsername && $newUsername !== $oldUser['username']) {
                    $parts[] = "Username: {$oldUser['username']} → {$newUsername}";
                }
                if ($newRole && $newRole !== $oldUser['role']) {
                    $parts[] = "Role: {$oldUser['role']} → {$newRole}";
                }
            } else {
                if ($newUsername) $parts[] = "Username: {$newUsername}";
                if ($newRole)     $parts[] = "Role: {$newRole}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── DELETE USER (admin) ─────────────────────────────────────
        if (str_contains($action, 'admin.user.destroy')) {
            $oldUser = $before['user'] ?? null;
            if ($oldUser) $parts[] = "Username: {$oldUser['username']} (Role: {$oldUser['role']})";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── RESET PASSWORD USER ─────────────────────────────────────
        if (str_contains($action, 'admin.user.reset')) {
            $oldUser = $before['user'] ?? null;
            if ($oldUser) $parts[] = "Username: {$oldUser['username']}";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── CREATE USER ─────────────────────────────────────────────
        if (str_contains($action, 'admin.user.store')) {
            $nis  = $request->input('username');
            $role = $request->input('role');
            $nama = $nis ? Pengurus::where('nis', $nis)->value('nama') : null;
            if ($nama) $parts[] = "Nama: {$nama}";
            if ($nis)  $parts[] = "Username: {$nis}";
            if ($role) $parts[] = "Role: {$role}";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── UPDATE PENGURUS ─────────────────────────────────────────
        if (str_contains($action, 'pengurus.update')) {
            $oldP    = $before['pengurus'] ?? null;
            $newNama = $request->input('nama');
            $newNis  = $request->input('nis');
            $newJabId = $request->input('jabatan_id');
            $newJab  = $newJabId ? Jabatan::with('divisi')->find($newJabId) : null;

            if ($oldP) {
                $parts[] = "Pengurus: {$oldP['nama']}";
                if ($newNama && $newNama !== $oldP['nama']) {
                    $parts[] = "Nama: {$oldP['nama']} → {$newNama}";
                }
                if ($newNis && $newNis !== $oldP['nis']) {
                    $parts[] = "NIS: {$oldP['nis']} → {$newNis}";
                }
                if ($newJab) {
                    $newDivisi  = $newJab->divisi?->nama ?? '-';
                    $newJabNama = $newJab->nama;
                    if ($newJabNama !== $oldP['jabatan'] || $newDivisi !== $oldP['divisi']) {
                        $parts[] = "Jabatan: {$oldP['jabatan']} ({$oldP['divisi']}) → {$newJabNama} ({$newDivisi})";
                    }
                }
            } else {
                if ($newNama) $parts[] = "Nama: {$newNama}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── DELETE PENGURUS ─────────────────────────────────────────
        if (str_contains($action, 'pengurus.destroy')) {
            $oldP = $before['pengurus'] ?? null;
            if ($oldP) $parts[] = "Pengurus: {$oldP['nama']} ({$oldP['jabatan']}, {$oldP['divisi']})";
            else {
                $nis = $request->route('nis');
                if ($nis) $parts[] = "NIS: {$nis}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── CREATE PENGURUS ─────────────────────────────────────────
        if (str_contains($action, 'pengurus.store')) {
            $nama    = $request->input('nama');
            $nis     = $request->input('nis');
            $jabId   = $request->input('jabatan_id');
            $jabatan = $jabId ? Jabatan::with('divisi')->find($jabId) : null;
            if ($nama) $parts[] = "Nama: {$nama}";
            if ($nis)  $parts[] = "NIS: {$nis}";
            if ($jabatan) $parts[] = "Jabatan: {$jabatan->nama} ({$jabatan->divisi?->nama})";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── UPDATE DIVISI ───────────────────────────────────────────
        if (str_contains($action, 'divisi.update')) {
            $oldD    = $before['divisi'] ?? null;
            $newNama = $request->input('nama');
            $newTipe = $request->input('tipe');
            if ($oldD) {
                $parts[] = "Divisi: {$oldD['nama']}";
                if ($newNama && $newNama !== $oldD['nama']) $parts[] = "Nama: {$oldD['nama']} → {$newNama}";
                if ($newTipe && $newTipe !== $oldD['tipe']) $parts[] = "Tipe: {$oldD['tipe']} → {$newTipe}";
            } else {
                if ($newNama) $parts[] = "Nama: {$newNama}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── UPDATE JABATAN ──────────────────────────────────────────
        if (str_contains($action, 'jabatan.update')) {
            $oldJ    = $before['jabatan'] ?? null;
            $newNama = $request->input('nama');
            $newDivId = $request->input('divisi_id');
            $newDivNama = $newDivId ? Divisi::find($newDivId)?->nama : null;
            if ($oldJ) {
                $parts[] = "Jabatan: {$oldJ['nama']} ({$oldJ['divisi']})";
                if ($newNama && $newNama !== $oldJ['nama']) $parts[] = "Nama: {$oldJ['nama']} → {$newNama}";
                if ($newDivNama && $newDivNama !== $oldJ['divisi']) $parts[] = "Divisi: {$oldJ['divisi']} → {$newDivNama}";
            } else {
                if ($newNama) $parts[] = "Nama: {$newNama}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── DELETE SANTRI ───────────────────────────────────────────
        if (str_contains($action, 'santri.destroy')) {
            $nis = $request->route('nis');
            if ($nis) {
                $santri = Santri::where('nis', $nis)->value('nama');
                $parts[] = $santri ? "Santri: {$santri} (NIS: {$nis})" : "NIS: {$nis}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── CREATE SANTRI ───────────────────────────────────────────
        if (str_contains($action, 'santri.store')) {
            $nis    = $request->input('nis');
            $nama   = $request->input('nama');
            $kepkam = $request->input('kepkam');
            if ($nama) $parts[] = "Nama: {$nama}";
            if ($nis)  $parts[] = "NIS: {$nis}";
            if ($kepkam) {
                $namaKepkam = Pengurus::where('nis', $kepkam)->value('nama');
                if ($namaKepkam) $parts[] = "Kepkam: {$namaKepkam}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── UPDATE SANTRI ───────────────────────────────────────────
        if (str_contains($action, 'santri.update')) {
            $nis     = $request->route('nis');
            $newNama = $request->input('nama');
            $kepkam  = $request->input('kepkam');
            if ($nis) {
                $oldNama = Santri::where('nis', $nis)->value('nama');
                if ($oldNama && $newNama && $newNama !== $oldNama) {
                    $parts[] = "Santri: {$oldNama} → {$newNama}";
                } elseif ($oldNama) {
                    $parts[] = "Santri: {$oldNama}";
                }
            }
            if ($kepkam) {
                $namaKepkam = Pengurus::where('nis', $kepkam)->value('nama');
                if ($namaKepkam) $parts[] = "Kepkam: {$namaKepkam}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── IMPORT SANTRI ───────────────────────────────────────────
        if (str_contains($action, 'santri.import')) {
            $file = $request->file('file');
            if ($file) $parts[] = "File: {$file->getClientOriginalName()}";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }
        if (str_contains($action, 'absensi') || str_contains($action, 'libur')) {
            $kegiatanMap = ['7' => 'Bandongan', '8' => 'Wirid', '9' => 'Yasinan'];
            $kegiatan    = $request->input('kegiatan');
            if ($kegiatan && isset($kegiatanMap[$kegiatan])) $parts[] = "Kegiatan: {$kegiatanMap[$kegiatan]}";
            $tipe = $request->input('tipe') ?? $request->route('tipe');
            if ($tipe) $parts[] = 'Kegiatan: ' . ucfirst($tipe);
            $tanggal = $request->input('tanggal') ?? $request->route('tanggal');
            if ($tanggal) $parts[] = "Tanggal: {$tanggal}";
            $ket = $request->input('keterangan');
            if ($ket) $parts[] = "Ket: {$ket}";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── KAMAR ASSIGN/UNASSIGN ───────────────────────────────────
        if (str_contains($action, 'kamar')) {
            $santriNis = $request->route('nis');
            if ($santriNis) {
                $santri = Santri::where('nis', $santriNis)->value('nama');
                if ($santri) $parts[] = "Santri: {$santri}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── REKAP / DOWNLOAD ────────────────────────────────────────
        if (str_contains($action, 'rekap') || str_contains($action, 'download')) {
            $start = $request->input('start_date');
            $end   = $request->input('end_date');
            if ($start && $end) $parts[] = "Periode: {$start} s/d {$end}";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── MINGGUAN ────────────────────────────────────────────────
        if (str_contains($action, 'mingguan')) {
            $tanggal = $request->input('tanggal');
            if ($tanggal) $parts[] = "Tanggal: {$tanggal}";
            $jumlah = is_array($request->input('santri')) ? count($request->input('santri')) : null;
            if ($jumlah) $parts[] = "{$jumlah} santri";
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        // ── PERIZINAN ───────────────────────────────────────────────
        if (str_contains($action, 'perizinan')) {
            $nis = $request->route('nis') ?? $request->input('nis');
            if ($nis) {
                $santri = Santri::where('nis', $nis)->value('nama');
                if ($santri) $parts[] = "Santri: {$santri}";
            }
            return $parts ? $base . ' — ' . implode(', ', $parts) : $base;
        }

        return $base;
    }

    private function guessModule(Request $request): string
    {
        $path = $request->path();
        foreach (['mahadiyah', 'kepkam', 'keamanan', 'kantor', 'madin', 'admin'] as $mod) {
            if (str_starts_with($path, $mod)) return $mod;
        }
        return 'general';
    }
}
