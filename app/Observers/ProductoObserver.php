<?php

namespace App\Observers;

use App\Jobs\SyncWooCommerceProduct;
use App\Models\Producto;
use App\Models\WooCommerceProductLink;

class ProductoObserver
{
    public function updated(Producto $producto): void
    {
        if (! $producto->wasChanged(['stock', 'precio_venta'])) {
            return;
        }

        $linkId = WooCommerceProductLink::where('producto_id', $producto->id)
            ->whereNotNull('woocommerce_product_id')
            ->value('id');

        if ($linkId) {
            SyncWooCommerceProduct::dispatch($linkId)->afterCommit();
        }
    }
}
