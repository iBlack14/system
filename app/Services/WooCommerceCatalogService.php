<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\WooCommerceCategory;
use App\Models\WooCommerceProductLink;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WooCommerceCatalogService
{
    public function __construct(private WooCommerceClient $client) {}

    public function importCategories(): int
    {
        $page = 1;
        $count = 0;

        do {
            $categories = $this->client->get('products/categories', ['per_page' => 100, 'page' => $page]);
            foreach ($categories as $category) {
                WooCommerceCategory::updateOrCreate(
                    ['woocommerce_id' => $category['id']],
                    [
                        'name' => $category['name'],
                        'slug' => $category['slug'] ?? null,
                        'parent_woocommerce_id' => $category['parent'] ?: null,
                    ]
                );
                $count++;
            }
            $page++;
        } while (count($categories) === 100);

        return $count;
    }

    public function createCategory(string $name, ?WooCommerceCategory $parent = null): WooCommerceCategory
    {
        $remote = $this->client->post('products/categories', array_filter([
            'name' => $name,
            'parent' => $parent?->woocommerce_id,
        ], fn ($value) => $value !== null));

        return WooCommerceCategory::updateOrCreate(
            ['woocommerce_id' => $remote['id']],
            [
                'name' => $remote['name'],
                'slug' => $remote['slug'] ?? null,
                'parent_woocommerce_id' => $remote['parent'] ?: null,
            ]
        );
    }

    public function candidates(?int $limit = null): Collection
    {
        $query = Producto::query()
            ->whereNotNull('codigo')->where('codigo', '<>', '')
            ->whereDoesntHave('wooCommerceLink', fn ($link) => $link->whereNotNull('woocommerce_product_id'))
            ->whereNotIn('codigo', function ($subquery) {
                $subquery->select('codigo')->from('productos')->whereNotNull('codigo')
                    ->groupBy('codigo')->havingRaw('COUNT(*) > 1');
            })
            ->orderBy('id');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function syncNewProduct(Producto $producto, ?WooCommerceCategory $category = null): WooCommerceProductLink
    {
        $sku = trim((string) $producto->codigo);
        if ($sku === '') {
            throw new RuntimeException("El producto {$producto->id} no tiene código.");
        }

        if (Producto::where('codigo', $sku)->count() > 1) {
            throw new RuntimeException("El código {$sku} está duplicado en el ERP.");
        }

        return DB::transaction(function () use ($producto, $category, $sku) {
            $link = WooCommerceProductLink::where('producto_id', $producto->id)->lockForUpdate()->first();
            if ($link?->woocommerce_product_id) {
                return $this->updateLinkedProduct($link);
            }

            // Protección fundamental: nunca adoptar ni modificar un producto preexistente.
            $existing = $this->client->get('products', ['sku' => $sku, 'per_page' => 2]);
            if ($existing !== []) {
                throw new RuntimeException("El SKU {$sku} ya existe en WooCommerce y no fue creado por este conector.");
            }

            $payload = [
                'name' => $producto->nombre,
                'type' => 'simple',
                'status' => config('services.woocommerce.product_status', 'publish'),
                'sku' => $sku,
                'regular_price' => number_format((float) $producto->precio_venta, 2, '.', ''),
                'manage_stock' => true,
                'stock_quantity' => max(0, (int) floor((float) $producto->stock)),
                'description' => $producto->descripcion ?: '',
            ];
            if ($category?->woocommerce_id) {
                $payload['categories'] = [['id' => $category->woocommerce_id]];
            }

            $remote = $this->client->post('products', $payload);

            return WooCommerceProductLink::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'woocommerce_category_id' => $category?->id,
                    'woocommerce_product_id' => $remote['id'],
                    'sku' => $sku,
                    'status' => config('services.woocommerce.product_status', 'publish'),
                    'last_synced_at' => now(),
                    'last_error' => null,
                ]
            );
        });
    }

    public function syncProduct(Producto $producto): WooCommerceProductLink
    {
        $link = WooCommerceProductLink::where('producto_id', $producto->id)->first();

        if (! $link?->woocommerce_product_id) {
            return $this->syncNewProduct($producto);
        }

        $link->update([
            'sku' => trim((string) $producto->codigo),
        ]);

        return $this->updateLinkedProduct($link->refresh());
    }

    public function updateLinkedProduct(WooCommerceProductLink $link): WooCommerceProductLink
    {
        $link->loadMissing('producto', 'category');
        $payload = [
            'sku' => trim((string) $link->producto->codigo),
            'name' => $link->producto->nombre,
            'description' => $link->producto->descripcion ?: '',
        ];
        if ($link->sync_price) {
            $payload['regular_price'] = number_format((float) $link->producto->precio_venta, 2, '.', '');
        }
        if ($link->sync_stock) {
            $payload['manage_stock'] = true;
            $payload['stock_quantity'] = max(0, (int) floor((float) $link->producto->stock));
        }
        $this->client->put("products/{$link->woocommerce_product_id}", $payload);
        $link->update([
            'sku' => trim((string) $link->producto->codigo),
            'status' => 'synced',
            'last_synced_at' => now(),
            'last_error' => null,
        ]);

        return $link->refresh();
    }
}
