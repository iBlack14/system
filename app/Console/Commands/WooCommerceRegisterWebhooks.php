<?php

namespace App\Console\Commands;

use App\Services\WooCommerceClient;
use Illuminate\Console\Command;

class WooCommerceRegisterWebhooks extends Command
{
    protected $signature = 'woo:webhooks-register {--commit : Registrar los webhooks en WooCommerce}';
    protected $description = 'Registra webhooks de pedidos apuntando al ERP';

    public function handle(WooCommerceClient $client): int
    {
        $url = rtrim(config('app.url'), '/').'/api/webhooks/woocommerce';
        if (! str_starts_with($url, 'https://')) {
            $this->error("La URL debe ser HTTPS y pública: {$url}");
            return self::FAILURE;
        }

        if (! $this->option('commit')) {
            $this->info("Simulación. URL de entrega: {$url}");
            $this->info('Temas: order.created, order.updated');
            return self::SUCCESS;
        }

        foreach (['order.created', 'order.updated'] as $topic) {
            $client->post('webhooks', [
                'name' => "ERP Acotec - {$topic}",
                'topic' => $topic,
                'delivery_url' => $url,
                'secret' => config('services.woocommerce.webhook_secret'),
                'status' => 'active',
            ]);
            $this->info("Registrado: {$topic}");
        }

        return self::SUCCESS;
    }
}
