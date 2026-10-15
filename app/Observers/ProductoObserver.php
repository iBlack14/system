<?php

namespace App\Observers;

use App\Models\Producto;
use App\Models\WooCommerceProductLink;
use App\Services\WooCommerceCatalogService;

class ProductoObserver
{
    public function created(Producto $producto): void
    {
        $this->sync($producto);
    }

    public function updated(Producto $producto): void
    {
        if (! $producto->wasChanged(['codigo', 'nombre', 'descripcion', 'stock', 'precio_venta'])) {
            return;
        }

        $this->sync($producto);
    }

    private function sync(Producto $producto): void
    {
        if (! config('services.woocommerce.sync_enabled')) {
            return;
        }

        try {
            app(WooCommerceCatalogService::class)->syncProduct($producto->fresh());
        } catch (\Throwable $e) {
            WooCommerceProductLink::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'sku' => trim((string) $producto->codigo),
                    'status' => 'error',
                    'last_error' => $e->getMessage(),
                ]
            );
            report($e);
        }
    }
}
