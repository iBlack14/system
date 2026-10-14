<?php

namespace App\Console\Commands;

use App\Models\WooCommerceCategory;
use App\Services\WooCommerceCatalogService;
use Illuminate\Console\Command;

class WooCommerceCatalogSync extends Command
{
    protected $signature = 'woo:catalog-sync {--commit : Crear productos como borradores en WooCommerce} {--limit= : Limitar productos} {--category= : ID local de categoría WooCommerce}';
    protected $description = 'Previsualiza o crea productos nuevos del ERP sin tocar productos preexistentes de WooCommerce';

    public function handle(WooCommerceCatalogService $catalog): int
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $products = $catalog->candidates($limit);
        $category = $this->option('category') ? WooCommerceCategory::findOrFail($this->option('category')) : null;

        $this->table(['ERP ID', 'SKU', 'Nombre', 'Precio', 'Stock'], $products->map(fn ($p) => [
            $p->id, $p->codigo, $p->nombre, $p->precio_venta, $p->stock,
        ]));

        if (! $this->option('commit')) {
            $this->info("Simulación: {$products->count()} productos elegibles. No se modificó WooCommerce.");
            return self::SUCCESS;
        }

        $ok = 0;
        foreach ($products as $product) {
            try {
                $catalog->syncNewProduct($product, $category);
                $ok++;
                $this->info("Creado como borrador: {$product->codigo}");
            } catch (\Throwable $e) {
                $this->error("{$product->codigo}: {$e->getMessage()}");
            }
        }

        $this->info("Finalizado: {$ok}/{$products->count()} productos creados.");
        return $ok === $products->count() ? self::SUCCESS : self::FAILURE;
    }
}
