<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Models\WooCommerceProductLink;
use App\Services\WooCommerceOrderService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WooCommerceOrderServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); $table->string('codigo'); $table->string('nombre');
            $table->decimal('stock', 12, 3); $table->decimal('precio_venta', 14, 2); $table->timestamps();
        });
        Schema::create('woocommerce_categories', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('woocommerce_id')->nullable(); $table->string('name'); $table->string('slug')->nullable(); $table->unsignedBigInteger('parent_woocommerce_id')->nullable(); $table->timestamps();
        });
        Schema::create('woocommerce_product_links', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('producto_id')->unique(); $table->unsignedBigInteger('woocommerce_category_id')->nullable(); $table->unsignedBigInteger('woocommerce_product_id')->nullable()->unique(); $table->string('sku'); $table->string('status'); $table->boolean('sync_price')->default(true); $table->boolean('sync_stock')->default(true); $table->timestamp('last_synced_at')->nullable(); $table->text('last_error')->nullable(); $table->timestamps();
        });
        Schema::create('woocommerce_orders', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('woocommerce_order_id')->unique(); $table->string('number')->nullable(); $table->string('status'); $table->string('currency')->nullable(); $table->decimal('total', 14, 2); $table->string('customer_email')->nullable(); $table->string('customer_name')->nullable(); $table->boolean('stock_applied')->default(false); $table->json('payload'); $table->timestamp('processed_at')->nullable(); $table->timestamps();
        });
        Schema::create('woocommerce_order_items', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('woocommerce_order_id'); $table->unsignedBigInteger('producto_id')->nullable(); $table->unsignedBigInteger('woocommerce_product_link_id')->nullable(); $table->unsignedBigInteger('woocommerce_line_item_id'); $table->unsignedBigInteger('woocommerce_product_id')->nullable(); $table->unsignedBigInteger('woocommerce_variation_id')->nullable(); $table->string('sku')->nullable(); $table->string('name'); $table->decimal('quantity', 12, 3); $table->decimal('price', 14, 2); $table->decimal('total', 14, 2); $table->decimal('stock_applied_quantity', 12, 3)->default(0); $table->timestamps();
        });
    }

    public function test_a_linked_order_changes_stock_once_and_cancellation_restores_it(): void
    {
        Queue::fake();
        $product = Producto::create(['codigo' => 'ACO-TEST', 'nombre' => 'Producto de prueba', 'stock' => 10, 'precio_venta' => 5]);
        $initialStock = (float) $product->stock;
        $remoteProductId = 900000000 + $product->id;

        WooCommerceProductLink::create([
            'producto_id' => $product->id,
            'woocommerce_product_id' => $remoteProductId,
            'sku' => $product->codigo,
            'status' => 'draft',
        ]);

        $payload = [
            'id' => 990000001,
            'number' => 'TEST-1',
            'status' => 'processing',
            'currency' => 'PEN',
            'total' => '10.00',
            'billing' => ['email' => 'test@example.com', 'first_name' => 'Test', 'last_name' => 'Cliente'],
            'line_items' => [[
                'id' => 880000001,
                'product_id' => $remoteProductId,
                'variation_id' => 0,
                'sku' => $product->codigo,
                'name' => $product->nombre,
                'quantity' => 2,
                'price' => 5,
                'total' => '10.00',
            ]],
        ];

        $service = app(WooCommerceOrderService::class);
        $service->process($payload);
        $this->assertSame($initialStock - 2, (float) $product->fresh()->stock);

        $service->process($payload);
        $this->assertSame($initialStock - 2, (float) $product->fresh()->stock);

        $payload['status'] = 'cancelled';
        $service->process($payload);
        $this->assertSame($initialStock, (float) $product->fresh()->stock);
    }
}
