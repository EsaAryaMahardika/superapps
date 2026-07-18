<?php

use App\Http\Controllers\Madin;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Kantor;
use App\Http\Controllers\General;
use App\Http\Controllers\Keamanan;
use App\Http\Controllers\AlurPerizinan;
use Illuminate\Support\Facades\Route;
// Mahadiyah sub-controllers
use App\Http\Controllers\Mahadiyah\DashboardController as MahadiyahDashboard;
use App\Http\Controllers\Mahadiyah\AbsensiController as MahadiyahAbsensi;
use App\Http\Controllers\Mahadiyah\PengurusController as MahadiyahPengurus;
use App\Http\Controllers\Mahadiyah\RekapController as MahadiyahRekap;
// KepalaKamar sub-controllers
use App\Http\Controllers\KepalaKamar\DashboardController as KepkamDashboard;
use App\Http\Controllers\KepalaKamar\AbsensiController as KepkamAbsensi;
use App\Http\Controllers\KepalaKamar\RekapController as KepkamRekap;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [General::class, 'login'])->name('login');
    Route::post('/login', [General::class, 'auth']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [General::class, 'dashboard']);
    Route::post('/logout', [General::class, 'logout'])->name('logout');

    // Perizinan — diakses oleh kepkam & keamanan
    Route::middleware(['role:kepkam,keamanan'])->group(function () {
        Route::get('/perizinan', [AlurPerizinan::class, 'perizinan']);
        Route::post('/perizinan', [AlurPerizinan::class, 'createizin']);
        Route::put('/perizinan/{nis}', [AlurPerizinan::class, 'accizin']);
        Route::put('/lapor/{nis}', [AlurPerizinan::class, 'lapor']);
    });

    // Keamanan
    Route::prefix('keamanan')->middleware(['role:keamanan'])->group(function () {
        Route::get('/', [Keamanan::class, 'dashboard']);
        Route::get('/pelanggaran', [Keamanan::class, 'pelanggaran']);
        Route::post('/pelanggaran', [Keamanan::class, 'i_pelanggaran']);
    });

    // Mahadiyah
    Route::prefix('mahadiyah')->middleware(['role:mahadiyah'])->group(function () {
        // Dashboard
        Route::get('/', [MahadiyahDashboard::class, 'index']);

        // Absensi Pengurus
        Route::get('/absensi-mingguan', [MahadiyahAbsensi::class, 'mingguan']);
        Route::get('/absensi-kegiatan', [MahadiyahAbsensi::class, 'kegiatan']);
        Route::get('/absensi-pengurus', [MahadiyahAbsensi::class, 'index']);
        Route::get('/absen-pengurus', [MahadiyahAbsensi::class, 'create']);
        Route::post('/absen-pengurus', [MahadiyahAbsensi::class, 'store']);
        Route::get('/edit-absen/{tipe}/{tanggal}', [MahadiyahAbsensi::class, 'edit']);
        Route::put('/edit-absen/{tipe}/{tanggal}', [MahadiyahAbsensi::class, 'update']);
        Route::post('/libur-pengurus', [MahadiyahAbsensi::class, 'liburStore']);
        Route::delete('/libur-pengurus', [MahadiyahAbsensi::class, 'liburDestroy']);

        // Rekap
        Route::get('/rekap-kegiatan', [MahadiyahRekap::class, 'rekapKegiatan']);
        Route::get('/rekap-kegiatan/download', [MahadiyahRekap::class, 'downloadRekapKegiatan']);
        Route::get('/rekap-absensi-pengurus', [MahadiyahRekap::class, 'rekapAbsensiPengurus']);
        Route::get('/rekap-absensi-pengurus/download', [MahadiyahRekap::class, 'downloadRekapAbsensiPengurus']);
        Route::get('/rekap-absensi-pengurus/excel', [MahadiyahRekap::class, 'excelRekapAbsensiPengurus']);

        Route::get('/santri', [\App\Http\Controllers\Mahadiyah\SantriController::class, 'index']);
        Route::post('/santri', [\App\Http\Controllers\Mahadiyah\SantriController::class, 'store']);
        Route::get('/santri/template', [\App\Http\Controllers\Mahadiyah\SantriController::class, 'template']);
        Route::post('/santri/import', [\App\Http\Controllers\Mahadiyah\SantriController::class, 'import']);
        Route::delete('/santri/bulk', [\App\Http\Controllers\Mahadiyah\SantriController::class, 'destroyBulk']);
        Route::put('/santri/{nis}', [\App\Http\Controllers\Mahadiyah\SantriController::class, 'update']);
        Route::delete('/santri/{nis}', [\App\Http\Controllers\Mahadiyah\SantriController::class, 'destroy']);

        // CRUD Pengurus
        Route::get('/generate-nis', [MahadiyahPengurus::class, 'generateNis']);
        Route::get('/pengurus/template', [MahadiyahPengurus::class, 'templatePengurus']);
        Route::post('/pengurus/import', [MahadiyahPengurus::class, 'importPengurus']);
        Route::get('/pengurus', [MahadiyahPengurus::class, 'index']);
        Route::post('/pengurus', [MahadiyahPengurus::class, 'store']);
        Route::put('/pengurus/{nis}', [MahadiyahPengurus::class, 'update']);
        Route::delete('/pengurus/{nis}', [MahadiyahPengurus::class, 'destroy']);

        // CRUD Divisi
        Route::post('/divisi', [MahadiyahPengurus::class, 'divisiStore']);
        Route::put('/divisi/{id}', [MahadiyahPengurus::class, 'divisiUpdate']);
        Route::delete('/divisi/{id}', [MahadiyahPengurus::class, 'divisiDestroy']);

        // CRUD Jabatan
        Route::post('/jabatan', [MahadiyahPengurus::class, 'jabatanStore']);
        Route::put('/jabatan/{id}', [MahadiyahPengurus::class, 'jabatanUpdate']);
        Route::delete('/jabatan/{id}', [MahadiyahPengurus::class, 'jabatanDestroy']);
    });

    // Madin
    Route::prefix('madin')->middleware(['role:madin'])->group(function () {
        Route::get('/', [Madin::class, 'dashboard']);
        Route::get('/absensi-diniyah', [Madin::class, 'absensi']);
        Route::get('/absensi-pengajar', [Madin::class, 'pengajar']);
    });

    // Kantor
    Route::prefix('kantor')->middleware(['role:kantor'])->group(function () {
        Route::get('/', [Kantor::class, 'kantor']);
        Route::get('/boyong', [Kantor::class, 'boyong']);
        Route::post('/boyong', [Kantor::class, 'i_boyong']);
    });

    // Admin
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::get('/', [Admin::class, 'dashboard']);
        Route::get('/asrama', [Admin::class, 'asrama']);
        Route::post('/asrama', [Admin::class, 'asramaStore']);
        Route::put('/asrama/{id}', [Admin::class, 'asramaUpdate']);
        Route::delete('/asrama/{id}', [Admin::class, 'asramaDestroy']);
        Route::get('/asrama/{asrama_id}/kamar', [Admin::class, 'kamarIndex']);
        Route::post('/asrama/{asrama_id}/kamar', [Admin::class, 'kamarStore']);
        Route::put('/asrama/{asrama_id}/kamar/{kamar_id}', [Admin::class, 'kamarUpdate']);
        Route::delete('/asrama/{asrama_id}/kamar/{kamar_id}', [Admin::class, 'kamarDestroy']);
        Route::get('/asrama/{asrama_id}/kamar/{kamar_id}/santri', [Admin::class, 'kamarSantri']);
        Route::post('/asrama/{asrama_id}/kamar/{kamar_id}/santri', [Admin::class, 'kamarAssignSantri']);
        Route::delete('/asrama/{asrama_id}/kamar/{kamar_id}/santri/{nis}', [Admin::class, 'kamarUnassignSantri']);

        Route::get('/santri', [Admin::class, 'santri']);
        Route::post('/santri', [Admin::class, 'santriStore']);
        Route::get('/santri/template', [Admin::class, 'santriTemplate']);
        Route::post('/santri/import', [Admin::class, 'santriImport']);
        Route::delete('/santri/bulk', [Admin::class, 'santriDestroyBulk']);
        Route::put('/santri/{nis}', [Admin::class, 'santriUpdate']);
        Route::delete('/santri/{nis}', [Admin::class, 'santriDestroy']);
        Route::get('/pengurus', [Admin::class, 'pengurus']);
        Route::get('/pengurus/template', [Admin::class, 'pengurusTemplate']);
        Route::post('/pengurus/import', [Admin::class, 'pengurusImport']);
        Route::get('/pengurus/{nis}/edit', [Admin::class, 'pengurusEdit']);
        Route::put('/pengurus/{nis}', [Admin::class, 'pengurusUpdate']);
        Route::get('/logs', [Admin::class, 'logs']);
        Route::get('/users', [Admin::class, 'index']);
        Route::get('/users/create', [Admin::class, 'create']);
        Route::post('/users', [Admin::class, 'store']);
        Route::get('/users/{id}/edit', [Admin::class, 'edit']);
        Route::put('/users/{id}', [Admin::class, 'update']);
        Route::delete('/users/bulk', [Admin::class, 'destroyBulk']);
        Route::delete('/users/{id}', [Admin::class, 'destroy']);
        Route::post('/users/{id}/reset-password', [Admin::class, 'resetPassword']);
    });

    // Kepala Kamar
    Route::prefix('kepkam')->middleware(['role:kepkam'])->group(function () {
        // Dashboard
        Route::get('/', [KepkamDashboard::class, 'index']);

        // Absensi
        Route::get('/absensi', [KepkamAbsensi::class, 'index']);
        Route::get('/absensi/check-completed', [KepkamAbsensi::class, 'checkCompleted']);
        Route::post('/absen', [KepkamAbsensi::class, 'store']);
        Route::delete('/absensi/{id}', [KepkamAbsensi::class, 'destroy']);

        // Rekap & Mingguan
        Route::get('/mingguan', [KepkamRekap::class, 'mingguan']);
        Route::post('/mingguan', [KepkamRekap::class, 'storeMingguan']);
        Route::get('/rekap-harian', [KepkamRekap::class, 'rekapHarian']);
        Route::get('/rekap-harian/download', [KepkamRekap::class, 'downloadRekapHarian']);

        // Search
        Route::get('/search-santri', [General::class, 'santri']);
        Route::get('/search-kepkam', [General::class, 'kepkam']);
    });
});