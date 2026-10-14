<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('woocommerce_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('woocommerce_id')->nullable()->unique();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('parent_woocommerce_id')->nullable();
            $table->timestamps();
        });

        Schema::create('woocommerce_product_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->unique()->constrained('productos')->cascadeOnDelete();
            $table->foreignId('woocommerce_category_id')->nullable()->constrained('woocommerce_categories')->nullOnDelete();
            $table->unsignedBigInteger('woocommerce_product_id')->nullable()->unique();
            $table->string('sku', 100)->index();
            $table->string('status', 20)->default('pending')->index();
            $table->boolean('sync_price')->default(true);
            $table->boolean('sync_stock')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        Schema::create('woocommerce_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('woocommerce_order_id')->unique();
            $table->string('number', 50)->nullable();
            $table->string('status', 40)->index();
            $table->string('currency', 10)->nullable();
            $table->decimal('total', 14, 2)->default(0);
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->boolean('stock_applied')->default(false);
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('woocommerce_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('woocommerce_order_id')->constrained('woocommerce_orders')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->foreignId('woocommerce_product_link_id')->nullable()->constrained('woocommerce_product_links')->nullOnDelete();
            $table->unsignedBigInteger('woocommerce_line_item_id');
            $table->unsignedBigInteger('woocommerce_product_id')->nullable();
            $table->unsignedBigInteger('woocommerce_variation_id')->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('name');
            $table->decimal('quantity', 12, 3);
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('stock_applied_quantity', 12, 3)->default(0);
            $table->timestamps();
            $table->unique(['woocommerce_order_id', 'woocommerce_line_item_id'], 'woo_order_line_unique');
        });

        Schema::create('woocommerce_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_id')->unique();
            $table->string('topic', 100)->nullable();
            $table->string('status', 20)->default('received')->index();
            $table->json('payload');
            $table->text('error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('woocommerce_webhook_events');
        Schema::dropIfExists('woocommerce_order_items');
        Schema::dropIfExists('woocommerce_orders');
        Schema::dropIfExists('woocommerce_product_links');
        Schema::dropIfExists('woocommerce_categories');
    }
};
