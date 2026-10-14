<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WooCommerceOrder extends Model
{
    protected $table = 'woocommerce_orders';

    protected $fillable = [
        'woocommerce_order_id', 'number', 'status', 'currency', 'total',
        'customer_email', 'customer_name', 'stock_applied', 'payload', 'processed_at',
    ];

    protected $casts = ['stock_applied' => 'boolean', 'payload' => 'array', 'processed_at' => 'datetime'];

    public function items() { return $this->hasMany(WooCommerceOrderItem::class, 'woocommerce_order_id'); }
}
