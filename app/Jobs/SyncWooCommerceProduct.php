<?php

namespace App\Jobs;

use App\Models\WooCommerceProductLink;
use App\Services\WooCommerceCatalogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncWooCommerceProduct implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public array $backoff = [10, 60, 300, 900];

    public function __construct(public int $linkId) {}

    public function handle(WooCommerceCatalogService $catalog): void
    {
        $link = WooCommerceProductLink::find($this->linkId);
        if (! $link?->woocommerce_product_id) {
            return;
        }

        try {
            $catalog->updateLinkedProduct($link);
        } catch (\Throwable $e) {
            $link->update(['status' => 'error', 'last_error' => $e->getMessage()]);
            throw $e;
        }
    }
}
