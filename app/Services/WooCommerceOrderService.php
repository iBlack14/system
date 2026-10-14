<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\WooCommerceOrder;
use App\Models\WooCommerceProductLink;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WooCommerceOrderService
{
    private const STOCK_OUT_STATUSES = ['processing', 'completed', 'on-hold'];
    private const STOCK_RETURN_STATUSES = ['cancelled', 'refunded', 'failed'];

    public function process(array $payload): WooCommerceOrder
    {
        if (! isset($payload['id'], $payload['status'])) {
            throw new RuntimeException('El webhook no contiene un pedido válido.');
        }

        return DB::transaction(function () use ($payload) {
            $billing = $payload['billing'] ?? [];
            $order = WooCommerceOrder::updateOrCreate(
                ['woocommerce_order_id' => $payload['id']],
                [
                    'number' => $payload['number'] ?? (string) $payload['id'],
                    'status' => $payload['status'],
                    'currency' => $payload['currency'] ?? null,
                    'total' => $payload['total'] ?? 0,
                    'customer_email' => $billing['email'] ?? null,
                    'customer_name' => trim(($billing['first_name'] ?? '').' '.($billing['last_name'] ?? '')) ?: null,
                    'payload' => $payload,
                    'processed_at' => now(),
                ]
            );

            foreach ($payload['line_items'] ?? [] as $line) {
                // Únicamente productos creados/vinculados explícitamente por el conector.
                $link = WooCommerceProductLink::where('woocommerce_product_id', $line['product_id'] ?? 0)->first();
                if (! $link) {
                    continue;
                }

                $item = $order->items()->updateOrCreate(
                    ['woocommerce_line_item_id' => $line['id']],
                    [
                        'producto_id' => $link->producto_id,
                        'woocommerce_product_link_id' => $link->id,
                        'woocommerce_product_id' => $line['product_id'] ?? null,
                        'woocommerce_variation_id' => $line['variation_id'] ?? null,
                        'sku' => $line['sku'] ?? $link->sku,
                        'name' => $line['name'] ?? $link->sku,
                        'quantity' => $line['quantity'],
                        'price' => $line['price'] ?? 0,
                        'total' => $line['total'] ?? 0,
                    ]
                );

                $producto = Producto::whereKey($link->producto_id)->lockForUpdate()->firstOrFail();
                $applied = (float) $item->stock_applied_quantity;
                $quantity = (float) $line['quantity'];

                if (in_array($payload['status'], self::STOCK_OUT_STATUSES, true) && $applied <= 0) {
                    if ((float) $producto->stock < $quantity) {
                        throw new RuntimeException("Stock insuficiente para {$producto->codigo}.");
                    }
                    $producto->decrement('stock', $quantity);
                    $item->update(['stock_applied_quantity' => $quantity]);
                } elseif (in_array($payload['status'], self::STOCK_RETURN_STATUSES, true) && $applied > 0) {
                    $producto->increment('stock', $applied);
                    $item->update(['stock_applied_quantity' => 0]);
                }
            }

            $order->update(['stock_applied' => $order->items()->where('stock_applied_quantity', '>', 0)->exists()]);
            return $order->refresh();
        });
    }
}
