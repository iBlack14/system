<?php

use App\Http\Controllers\WooCommerceWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/woocommerce', WooCommerceWebhookController::class)
    ->middleware('throttle:120,1')
    ->name('webhooks.woocommerce');
