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
    // Route::get('/{any}', function () {
    //     return view('login');
    // })->where('any', '.*');
});
Route::get('/login', [General::class, 'login'])->name('login');
Route::post('/login', [General::class, 'auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [General::class, 'dashboard']);
    Route::get('/santri', [General::class, 'santri']);
    Route::get('/pengurus', [General::class, 'pengurus']);
    Route::get('/perizinan', [AlurPerizinan::class, 'perizinan']);
    Route::put('/perizinan/{nis}', [AlurPerizinan::class, 'accizin']);

    // Keamanan
    Route::get('/keamanan', [Keamanan::class, 'dashboard']);
    Route::get('/pelanggaran', [Keamanan::class, 'pelanggaran']);
    Route::post('/pelanggaran', [Keamanan::class, 'i_pelanggaran']);

    // Mahadiyah
    Route::get('/mahadiyah', [Mahadiyah::class, 'dashboard']);
    Route::get('/absensi-pengurus', [Mahadiyah::class, 'absensi']);
    Route::get('/absensi-mingguan', [Mahadiyah::class, 'mingguan']);
    Route::get('/absensi-kegiatan', [Mahadiyah::class, 'kegiatan']);

    // Madin
    Route::get('/madin', [Madin::class, 'dashboard']);
    Route::get('/absensi-diniyah', [Madin::class, 'absensi']);
    Route::get('/absensi-pengajar', [Madin::class, 'pengajar']);

    // Kepala Kamar
    Route::get('/kepkam', [KepalaKamar::class, 'dashboard']);
    Route::get('/absensi', [KepalaKamar::class, 'absensi']);
    Route::post('/absen', [KepalaKamar::class, 'absen']);
    
    // Kantor
    Route::get('/kantor', [Kantor::class, 'kantor']);
    Route::get('/boyong', [Kantor::class, 'boyong']);
    Route::post('/boyong', [Kantor::class, 'i_boyong']);

    Route::get('/logout', [General::class, 'logout']);
});
Route::get('/ok', function () {
    return "OK";
});