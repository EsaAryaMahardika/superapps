<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\Translator;
use App\Models\Perizinan;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nama hari ala pesantren: Minggu → Ahad. Berlaku untuk semua output
        // tanggal Carbon (translatedFormat & isoFormat) di seluruh aplikasi.
        // Dua translator dipatch: yang global (dipakai ->translatedFormat / ->dayName)
        // dan singleton locale 'id' (dipakai saat view memanggil ->locale('id')).
        Carbon::setLocale('id');
        $namaHari = [
            'weekdays'       => ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'weekdays_short' => ['Ahd', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            'weekdays_min'   => ['Ah', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
        ];
        Carbon::getTranslator()->setTranslations($namaHari);
        Translator::get('id')->setTranslations($namaHari);

        View::composer(['sidebar', 'layout', 'kepkam.layout'], function ($view) {
            $view->with('user', Auth::user());
        });

        // Badge "perizinan menunggu" di navbar pengasuh — dibutuhkan di semua
        // halaman, jadi disuntik lewat composer ketimbang tiap controller.
        View::composer('pengasuh.layout', function ($view) {
            $view->with('perizinanMenungguCount', Perizinan::where('status', 0)->count());
        });
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
