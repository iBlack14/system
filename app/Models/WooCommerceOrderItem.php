<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WooCommerceOrderItem extends Model
{
    protected $table = 'woocommerce_order_items';

    protected $fillable = [
        'woocommerce_order_id', 'producto_id', 'woocommerce_product_link_id',
        'woocommerce_line_item_id', 'woocommerce_product_id', 'woocommerce_variation_id',
        'sku', 'name', 'quantity', 'price', 'total', 'stock_applied_quantity',
    ];
}
