<?php

namespace App\Http\Controllers;

use App\Models\WooCommerceWebhookEvent;
use App\Services\WooCommerceOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WooCommerceWebhookController extends Controller
{
    public function __invoke(Request $request, WooCommerceOrderService $orders)
    {
        $raw = $request->getContent();
        $secret = (string) config('services.woocommerce.webhook_secret');
        $signature = (string) $request->header('X-WC-Webhook-Signature');
        $expected = base64_encode(hash_hmac('sha256', $raw, $secret, true));

        if ($secret === '' || $signature === '' || ! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Firma inválida.'], 401);
        }

        $topic = (string) $request->header('X-WC-Webhook-Topic');
        $deliveryId = (string) ($request->header('X-WC-Webhook-Delivery-ID') ?: hash('sha256', $topic.$raw));
        $payload = $request->json()->all();

        $event = WooCommerceWebhookEvent::firstOrCreate(
            ['delivery_id' => $deliveryId],
            ['topic' => $topic, 'status' => 'received', 'payload' => $payload]
        );

        if (! $event->wasRecentlyCreated && $event->status === 'processed') {
            return response()->json(['message' => 'Evento ya procesado.']);
        }

        try {
            if (str_starts_with($topic, 'order.')) {
                $orders->process($payload);
            }

            $event->update(['status' => 'processed', 'processed_at' => now(), 'error' => null]);
            return response()->json(['message' => 'Evento procesado.']);
        } catch (\Throwable $e) {
            $event->update(['status' => 'failed', 'error' => $e->getMessage()]);
            Log::error('Error procesando webhook WooCommerce', ['delivery_id' => $deliveryId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'No se pudo procesar el evento.'], 500);
        }
    }
}
