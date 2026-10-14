<?php

namespace Tests\Feature;

use Tests\TestCase;

class WooCommerceWebhookTest extends TestCase
{
    public function test_it_rejects_a_webhook_with_an_invalid_signature(): void
    {
        config()->set('services.woocommerce.webhook_secret', 'test-secret');

        $this->postJson('/api/webhooks/woocommerce', ['id' => 123], [
            'X-WC-Webhook-Signature' => 'invalid',
            'X-WC-Webhook-Topic' => 'order.created',
        ])->assertUnauthorized();
    }
}
