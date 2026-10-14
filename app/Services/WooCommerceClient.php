<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WooCommerceClient
{
    private function request(): PendingRequest
    {
        $url = rtrim((string) config('services.woocommerce.url'), '/');
        $key = (string) config('services.woocommerce.consumer_key');
        $secret = (string) config('services.woocommerce.consumer_secret');

        if ($url === '' || $key === '' || $secret === '') {
            throw new RuntimeException('La conexión de WooCommerce no está configurada.');
        }

        $request = Http::baseUrl($url.'/wp-json/'.config('services.woocommerce.version', 'wc/v3'))
            ->withBasicAuth($key, $secret)
            ->acceptJson()
            ->asJson()
            ->timeout(config('services.woocommerce.timeout', 30))
            ->retry(2, 500, throw: false);

        if ($caBundle = config('services.woocommerce.ca_bundle')) {
            $request->withOptions(['verify' => $caBundle]);
        }

        return $request;
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->decode($this->request()->get($endpoint, $query));
    }

    public function post(string $endpoint, array $data): array
    {
        return $this->decode($this->request()->post($endpoint, $data));
    }

    public function put(string $endpoint, array $data): array
    {
        return $this->decode($this->request()->put($endpoint, $data));
    }

    private function decode($response): array
    {
        if (! $response->successful()) {
            $message = $response->json('message') ?: $response->body();
            throw new RuntimeException("WooCommerce respondió {$response->status()}: {$message}");
        }

        return $response->json() ?? [];
    }
}
