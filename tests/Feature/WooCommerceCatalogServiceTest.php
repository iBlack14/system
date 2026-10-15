<?php

namespace Tests\Feature;

use App\Models\Producto;
use App\Services\WooCommerceCatalogService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WooCommerceCatalogServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('services.woocommerce', [
            'url' => 'https://store.test', 'consumer_key' => 'ck_test', 'consumer_secret' => 'cs_test',
            'version' => 'wc/v3', 'timeout' => 5, 'product_status' => 'publish', 'sync_enabled' => false,
        ]);
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); $table->string('codigo'); $table->string('nombre'); $table->string('descripcion')->nullable();
            $table->decimal('stock', 12, 3); $table->decimal('precio_venta', 14, 2); $table->timestamps();
        });
        Schema::create('woocommerce_categories', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('woocommerce_id')->nullable(); $table->string('name'); $table->string('slug')->nullable(); $table->unsignedBigInteger('parent_woocommerce_id')->nullable(); $table->timestamps();
        });
        Schema::create('woocommerce_product_links', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('producto_id')->unique(); $table->unsignedBigInteger('woocommerce_category_id')->nullable(); $table->unsignedBigInteger('woocommerce_product_id')->nullable()->unique(); $table->string('sku'); $table->string('status')->default('pending'); $table->boolean('sync_price')->default(true); $table->boolean('sync_stock')->default(true); $table->timestamp('last_synced_at')->nullable(); $table->text('last_error')->nullable(); $table->timestamps();
        });
    }

    public function test_it_creates_an_erp_product_as_published_without_adopting_existing_products(): void
    {
        $product = Producto::create(['codigo' => 'ERP-001', 'nombre' => 'Bomba', 'descripcion' => 'Industrial', 'stock' => 8, 'precio_venta' => 199.9]);
        Http::fake([
            '*/products?*' => Http::response([], 200),
            '*/products' => Http::response(['id' => 501], 201),
        ]);

        $link = app(WooCommerceCatalogService::class)->syncProduct($product);

        $this->assertSame(501, $link->woocommerce_product_id);
        Http::assertSent(fn ($request) => $request->method() === 'POST'
            && $request['status'] === 'publish' && $request['sku'] === 'ERP-001'
            && $request['stock_quantity'] === 8 && ! isset($request['categories']));
    }

    public function test_it_updates_only_a_product_previously_linked_by_the_connector(): void
    {
        $product = Producto::create(['codigo' => 'ERP-002', 'nombre' => 'Motor', 'stock' => 4, 'precio_venta' => 350]);
        $link = $product->wooCommerceLink()->create(['woocommerce_product_id' => 502, 'sku' => 'ERP-002', 'status' => 'synced']);
        Http::fake(['*/products/502' => Http::response(['id' => 502], 200)]);

        app(WooCommerceCatalogService::class)->syncProduct($product);

        Http::assertSent(fn ($request) => $request->method() === 'PUT'
            && $request['regular_price'] === '350.00' && $request['stock_quantity'] === 4);
        $this->assertSame('synced', $link->fresh()->status);
    }
}
