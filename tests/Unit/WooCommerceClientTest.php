<?php

namespace Tests\Unit;

use App\Services\WooCommerceClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WooCommerceClientTest extends TestCase
{
    public function test_it_uses_the_configured_api_and_basic_authentication(): void
    {
        config()->set('services.woocommerce', [
            'url' => 'https://store.test',
            'consumer_key' => 'ck_test',
            'consumer_secret' => 'cs_test',
            'version' => 'wc/v3',
            'timeout' => 5,
            'ca_bundle' => null,
        ]);

        Http::fake(['https://store.test/wp-json/wc/v3/products*' => Http::response([['id' => 10]], 200)]);

        $result = app(WooCommerceClient::class)->get('products', ['per_page' => 1]);

        $this->assertSame(10, $result[0]['id']);
        Http::assertSent(fn ($request) => $request->url() === 'https://store.test/wp-json/wc/v3/products?per_page=1'
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('ck_test:cs_test')));
    }
}
