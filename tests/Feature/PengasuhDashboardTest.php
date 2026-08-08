<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pengurus;
use App\Models\Santri;
use Tests\TestCase;

/**
 * Smoke test read-only untuk dashboard pengasuh: kartu total santri/pengurus
 * dalam-luar, dan endpoint pencarian global (santri & pengurus) untuk fitur
 * track record.
 */
class PengasuhDashboardTest extends TestCase
{
    private function loginPengasuh(): User
    {
        $user = User::where('role', 'pengasuh')->first();

        if (!$user) {
            $this->markTestSkipped('Tidak ada user dengan role pengasuh di database.');
        }

        return $user;
    }

    public function test_dashboard_menampilkan_kartu_pengurus_dalam_luar(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh');

        $response->assertOk();
        $response->assertSee('Pengurus Dalam');
        $response->assertSee('Pengurus Luar');
        $response->assertSee('id="globalSearch"', false);
    }

    public function test_navbar_hanya_beranda_cari_profil(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh');

        $response->assertOk();
        $response->assertSee('>Beranda<', false);
        $response->assertSee('>Cari<', false);
        $response->assertSee('>Profil<', false);
        // Label yang dulu menempati slot bottom nav sudah tidak ada lagi.
        // (Perizinan sengaja tidak diuji: masih dipertahankan di sidebar desktop.)
        foreach (['>Bayar<', '>Absen<', '>Izin<', '>Akun<', '>Utama<'] as $label) {
            $response->assertDontSee($label, false);
        }
        $response->assertSee('href="/profil"', false);
    }

    public function test_dashboard_tidak_lagi_punya_kolom_pencarian(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh');

        $response->assertOk();
        // Kolom pencarian dilepas dari isi dashboard; aksesnya lewat navbar "Cari"
        $response->assertDontSee('Cari menu, laporan, atau nama santri', false);
        $response->assertSee('id="searchOverlay"', false);
    }

    public function test_pencarian_global_tersedia_di_semua_halaman(): void
    {
        foreach (['/pengasuh', '/pengasuh/absensi', '/pengasuh/kesehatan', '/pengasuh/logistik'] as $url) {
            $response = $this->actingAs($this->loginPengasuh())->get($url);

            $response->assertOk();
            $response->assertSee('id="searchOverlay"', false);
            $response->assertSee('openSearch()', false);
            $response->assertSee('Progres Pembangunan');   // isi MENU_ITEMS ikut termuat
        }
    }

    /**
     * Test ini jalan di atas database dev, jadi perubahan datanya dibungkus
     * transaksi dan di-rollback — jangan pernah delete/update permanen di sini.
     */
    public function test_badge_perizinan_hanya_muncul_saat_ada_antrean(): void
    {
        $user = $this->loginPengasuh();
        // Badge kini hanya tersisa di sidebar desktop (bottom nav tinggal 3 slot)
        $badgeMark = 'bg-[#EE5D50] text-white text-[10px]';

        $jumlahAwal = \App\Models\Perizinan::where('status', 0)->count();

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Tanpa antrean → badge tidak dirender sama sekali
            \App\Models\Perizinan::where('status', 0)->update(['status' => 1]);
            $this->actingAs($user)->get('/pengasuh')
                ->assertOk()
                ->assertDontSee($badgeMark, false);

            // Begitu ada satu yang menunggu, badge muncul dengan angkanya
            $santri = Santri::first();
            $alasan = \App\Models\AlasanIzin::first();
            if (!$santri || !$alasan) $this->markTestSkipped('Data santri/alasan izin belum ada untuk uji.');

            // Insert langsung: kolom 'pengajuan' tidak ada di $fillable model
            \Illuminate\Support\Facades\DB::table('perizinan')->insert([
                'nis'        => $santri->nis,
                'jenis'      => 'P',
                'alasan'     => $alasan->id,
                'pengajuan'  => now(),
                'berangkat'  => now(),
                'es_kembali' => now()->addDay(),
                'status'     => 0,
            ]);

            $this->actingAs($user)->get('/pengasuh')
                ->assertOk()
                ->assertSee($badgeMark, false)
                ->assertSee('>1', false);
        } finally {
            \Illuminate\Support\Facades\DB::rollBack();
        }

        // Data harus kembali persis seperti semula
        $this->assertSame($jumlahAwal, \App\Models\Perizinan::where('status', 0)->count());
    }

    public function test_halaman_profil_bisa_dibuka_dan_punya_logout(): void
    {
        // Di mobile, sidebar (tempat tombol logout) disembunyikan, jadi /profil
        // adalah satu-satunya jalan keluar — form logout wajib ada di sini.
        $response = $this->actingAs($this->loginPengasuh())->get('/profil');

        $response->assertOk();
        $response->assertSee('action="/logout"', false);
        $response->assertSee('Logout');
    }

    public function test_logout_benar_benar_mengakhiri_sesi(): void
    {
        $this->actingAs($this->loginPengasuh())
            ->post('/logout')
            ->assertRedirect();

        $this->assertGuest();
        // Setelah logout, halaman pengasuh tidak boleh bisa dibuka lagi
        $this->get('/pengasuh')->assertRedirect();
    }

    public function test_search_santri_api_mengembalikan_json(): void
    {
        $santri = Santri::first();
        if (!$santri) $this->markTestSkipped('Tidak ada data santri.');

        $response = $this->actingAs($this->loginPengasuh())
            ->get('/pengasuh/api/search-santri?q=' . substr($santri->nama, 0, 3));

        $response->assertOk();
        $response->assertJsonStructure([['nis', 'nama', 'kepkam']]);
    }

    public function test_search_pengurus_api_mengembalikan_json(): void
    {
        $pengurus = Pengurus::first();
        if (!$pengurus) $this->markTestSkipped('Tidak ada data pengurus.');

        $response = $this->actingAs($this->loginPengasuh())
            ->get('/pengasuh/api/search-pengurus?q=' . substr($pengurus->nama, 0, 3));

        $response->assertOk();
        $response->assertJsonStructure([['nis', 'nama', 'asal', 'jabatan']]);
    }

    public function test_pengurus_detail_api_mengembalikan_stats(): void
    {
        $pengurus = Pengurus::first();
        if (!$pengurus) $this->markTestSkipped('Tidak ada data pengurus.');

        $response = $this->actingAs($this->loginPengasuh())
            ->get('/pengasuh/api/pengurus/' . $pengurus->nis);

        $response->assertOk();
        $response->assertJsonStructure(['nis', 'nama', 'jabatan', 'divisi', 'asal', 'stats' => ['hadir', 'sakit', 'izin', 'alfa', 'kehadiran'], 'riwayat']);
    }

    public function test_track_record_page_mendukung_toggle_pengurus(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh/track-record');

        $response->assertOk();
        $response->assertSee('id="type-santri"', false);
        $response->assertSee('id="type-pengurus"', false);
    }

    public function test_dashboard_hanya_grid_menu_tanpa_panel(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh');

        $response->assertOk();
        // Semua tile mengarah ke halaman, bukan membuka panel di dashboard
        foreach ([
            '/pengasuh/absensi', '/pengasuh/perizinan', '/pengasuh/pembayaran',
            '/pengasuh/kesehatan', '/pengasuh/infrastruktur', '/pengasuh/logistik',
            '/pengasuh/track-record?type=santri', '/pengasuh/track-record?type=pengurus',
        ] as $href) {
            $response->assertSee('href="' . $href . '"', false);
        }
        // Tidak ada lagi panel/tab yang dirender di dashboard
        $response->assertDontSee('id="tab-', false);
        $response->assertDontSee('id="panelHeader"', false);
        $response->assertDontSee('switchTab(', false);
        $response->assertDontSee('Coming Soon');
    }

    public function test_kartu_ringkasan_tidak_bisa_digeser_samping(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh');

        $response->assertOk();
        // Kartu ringkasan memakai grid 3 kolom, bukan flex overflow-x-auto
        $response->assertSee('grid grid-cols-3 gap-2 md:gap-3 mb-4', false);
        $response->assertDontSee('flex gap-3 mb-4 overflow-x-auto', false);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('halamanModulDummy')]
    public function test_halaman_modul_dummy_bisa_dibuka(string $url, string $judul, string $isi): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get($url);

        $response->assertOk();
        $response->assertSee($judul);
        $response->assertSee($isi);
        $response->assertSee('Mode Tes');
        $response->assertDontSee('Coming Soon');
    }

    public static function halamanModulDummy(): array
    {
        return [
            'kesehatan'     => ['/pengasuh/kesehatan', 'Kesehatan Santri', 'Santri Dalam Perawatan'],
            'infrastruktur' => ['/pengasuh/infrastruktur', 'Infrastruktur', 'Laporan Perairan'],
            'logistik'      => ['/pengasuh/logistik', 'Logistik', 'Operasional Laundry'],
        ];
    }

    public function test_halaman_absensi_memuat_perhatian_khusus(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh/absensi');

        $response->assertOk();
        $response->assertSee('Santri Perhatian Khusus');
        $response->assertSee('Rekap Absensi Hari Ini');
    }

    public function test_semua_sub_fitur_terdaftar_di_pencarian_dashboard(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh');

        $response->assertOk();
        foreach ([
            'Rekap Absensi Santri', 'Perizinan Santri', 'Daftar Pembayaran',
            'Top Tunggakan SPP', 'Track Record Santri', 'Track Record Pengurus',
            'Santri Perhatian Khusus', 'IKS Poskestren',
            'Progres Pembangunan', 'Laporan Perairan', 'Distribusi Galon', 'Operasional Laundry',
        ] as $label) {
            $response->assertSee($label);
        }
    }

    public function test_halaman_pembayaran_menampilkan_data_dummy(): void
    {
        $response = $this->actingAs($this->loginPengasuh())->get('/pengasuh/pembayaran');

        $response->assertOk();
        $response->assertDontSee('Coming Soon');
        $response->assertSee('Mode Tes');
        $response->assertSee('Daftar Pembayaran');
        $response->assertSee('Top Tunggakan Terbanyak');
        $response->assertSee('Achmad Hafidz Fadli');
    }

    public function test_filter_status_pembayaran_bekerja(): void
    {
        $response = $this->actingAs($this->loginPengasuh())
            ->get('/pengasuh/pembayaran?status=Lunas');

        $response->assertOk();
        $response->assertSee('Achmad Hafidz Fadli');            // status Lunas
        $response->assertDontSee('Muhammad Rafa Aditya Purwanto'); // status Menunggak
    }
}
