<?php

namespace App\Providers;

use App\Models\Producto;
use App\Observers\ProductoObserver;
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
        Producto::observe(ProductoObserver::class);
        Paginator::useBootstrap();
        // 🔥 FORZAR HORA DE PERÚ EN PRODUCCIÓN
        date_default_timezone_set('America/Lima');

        // Opcional: idioma fechas
        Carbon::setLocale('es');

    }
}
