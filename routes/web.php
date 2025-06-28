<?php

use App\Http\Controllers\AlurPerizinan;
use App\Http\Controllers\General;
use App\Http\Controllers\Keamanan;
use App\Http\Controllers\Madin;
use App\Http\Controllers\Mahadiyah;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    // Route::get('/{any}', function () {
    //     return view('login');
    // })->where('any', '.*');
});
Route::get('/login', [General::class, 'login'])->name('login');
Route::post('/login', [General::class, 'auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [General::class, 'dashboard']);

    // Keamanan
    Route::get('/pelanggaran', [Keamanan::class, 'pelanggaran']);
    Route::post('/pelanggaran', [Keamanan::class, 'i_pelanggaran']);
    Route::get('/perizinan', [Keamanan::class, 'perizinan']);
    Route::put('/perizinan/{nis}', [AlurPerizinan::class, 'accizin']);
    Route::get('/keamanan', [Keamanan::class, 'dashboard']);

    // Mahadiyah
    Route::get('/mahadiyah', [Mahadiyah::class, 'dashboard']);
    Route::get('/absensi-pengurus', [Mahadiyah::class, 'absensi']);
    Route::get('/absensi-mingguan', [Mahadiyah::class, 'mingguan']);

    // Madin
    Route::get('/madin', [Madin::class, 'dashboard']);
    Route::get('/absensi-diniyah', [Madin::class, 'absensi']);
    Route::get('/absensi-pengajar', [Madin::class, 'pengajar']);

    Route::get('/logout', [General::class, 'logout']);
});
