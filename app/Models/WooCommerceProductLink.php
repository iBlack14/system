<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WooCommerceProductLink extends Model
{
    protected $table = 'woocommerce_product_links';

    protected $fillable = [
        'producto_id', 'woocommerce_category_id', 'woocommerce_product_id', 'sku',
        'status', 'sync_price', 'sync_stock', 'last_synced_at', 'last_error',
    ];

    protected $casts = [
        'sync_price' => 'boolean',
        'sync_stock' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function producto() { return $this->belongsTo(Producto::class); }
    public function category() { return $this->belongsTo(WooCommerceCategory::class, 'woocommerce_category_id'); }
}
