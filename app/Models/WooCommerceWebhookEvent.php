<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WooCommerceWebhookEvent extends Model
{
    protected $table = 'woocommerce_webhook_events';

    protected $fillable = ['delivery_id', 'topic', 'status', 'payload', 'error', 'processed_at'];
    protected $casts = ['payload' => 'array', 'processed_at' => 'datetime'];
}
