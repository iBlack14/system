<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

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
        Paginator::useBootstrap();
        // 🔥 FORZAR HORA DE PERÚ EN PRODUCCIÓN
        date_default_timezone_set('America/Lima');

        // Opcional: idioma fechas
        Carbon::setLocale('es');

    }
}
