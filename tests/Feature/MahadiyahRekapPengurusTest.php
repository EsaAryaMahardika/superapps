<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Smoke test read-only untuk modul Mahadiyah: keterangan izin di rekap absensi
 * pengurus, field asal pengurus, dan diksi nama hari Ahad.
 */
class MahadiyahRekapPengurusTest extends TestCase
{
    private function loginMahadiyah(): User
    {
        $user = User::where('role', 'mahadiyah')->first();

        if (!$user) {
            $this->markTestSkipped('Tidak ada user dengan role mahadiyah di database.');
        }

        return $user;
    }

    public function test_nama_hari_ahad_bukan_minggu(): void
    {
        $ahad = Carbon::parse('next sunday');

        $this->assertSame('Ahad', $ahad->translatedFormat('l'));
        $this->assertSame('Ahad', $ahad->dayName);
        $this->assertSame('Ah', $ahad->locale('id')->isoFormat('dd'));
        // Hari lain tidak ikut berubah
        $this->assertSame('Senin', Carbon::parse('next monday')->translatedFormat('l'));
    }

    public function test_rekap_absensi_pengurus_menampilkan_keterangan_izin(): void
    {
        $response = $this->actingAs($this->loginMahadiyah())
            ->get('/mahadiyah/rekap-absensi-pengurus');

        $response->assertOk();
        $response->assertSee('Jumlah Izin');   // legenda
        $response->assertSee('Izin');          // kolom ringkasan / baris total
    }

    public function test_halaman_pengurus_punya_field_asal(): void
    {
        $response = $this->actingAs($this->loginMahadiyah())->get('/mahadiyah/pengurus');

        $response->assertOk();
        $response->assertSee('Asal Pengurus');
        $response->assertSee('Pengurus Dalam');
        $response->assertSee('Pengurus Luar');
    }

    public function test_template_csv_pengurus_memuat_kolom_asal(): void
    {
        $response = $this->actingAs($this->loginMahadiyah())->get('/mahadiyah/pengurus/template');

        $response->assertOk();
        $this->assertStringContainsString('asal', $response->streamedContent());
    }
}
