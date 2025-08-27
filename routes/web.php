<?php

use App\Http\Controllers\Madin;
use App\Http\Controllers\Kantor;
use App\Http\Controllers\General;
use App\Http\Controllers\Keamanan;
use App\Http\Controllers\Mahadiyah;
use App\Http\Controllers\KepalaKamar;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlurPerizinan;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [General::class, 'login'])->name('login');
    Route::post('/login', [General::class, 'auth']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [General::class, 'dashboard']);

    Route::get('/search-santri', [General::class, 'santri']);
    Route::get('/search-pengurus', [General::class, 'pengurus']);
    Route::get('/search-kepkam', [General::class, 'kepkam']);

    Route::get('/perizinan', [AlurPerizinan::class, 'perizinan']);
    Route::post('/perizinan', [AlurPerizinan::class, 'createizin']);
    Route::put('/perizinan/{nis}', [AlurPerizinan::class, 'accizin']);
    Route::put('/lapor/{nis}', [AlurPerizinan::class, 'lapor']);

    Route::get('/logout', [General::class, 'logout']);

    // Keamanan
    Route::prefix('keamanan')->group(function(){
        Route::get('/', [Keamanan::class, 'dashboard']);
        Route::get('/pelanggaran', [Keamanan::class, 'pelanggaran']);
        Route::post('/pelanggaran', [Keamanan::class, 'i_pelanggaran']);
    });

    // Mahadiyah
    Route::prefix('mahadiyah')->group(function(){
        Route::get('/', [Mahadiyah::class, 'dashboard']);
        Route::get('/absensi-mingguan', [Mahadiyah::class, 'mingguan']);
        Route::get('/absensi-kegiatan', [Mahadiyah::class, 'kegiatan']);
        Route::get('/absensi-pengurus', [Mahadiyah::class, 'absensi']);
        Route::get( '/absen-pengurus', [Mahadiyah::class, 'create_absen']);
        Route::post( '/absen-pengurus', [Mahadiyah::class, 'store_absen']);
    });

    // Madin
    Route::prefix('madin')->group(function(){
        Route::get('/', [Madin::class, 'dashboard']);
        Route::get('/absensi-diniyah', [Madin::class, 'absensi']);
        Route::get('/absensi-pengajar', [Madin::class, 'pengajar']);
    });
    
    // Kantor
    Route::prefix('kantor')->group(function() {
        Route::get('/', [Kantor::class, 'kantor']);
        Route::get('/boyong', [Kantor::class, 'boyong']);
        Route::post('/boyong', [Kantor::class, 'i_boyong']);
    });

    // Kepala Kamar
    Route::prefix('kepkam')->group(function(){
        Route::get('/', [KepalaKamar::class, 'dashboard']);
        Route::get('/absensi', [KepalaKamar::class, 'absensi']);
        Route::post('/absen', [KepalaKamar::class, 'absen']);
        Route::get('/mingguan', [KepalaKamar::class, 'mingguan']);
        Route::post('/mingguan', [KepalaKamar::class, 'i_mingguan']);
    });
});
Route::get('/ok', function () {
    return "OK";
});