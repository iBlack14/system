<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\WooCommerceCategory;
use App\Models\WooCommerceOrder;
use App\Models\WooCommerceProductLink;
use App\Models\WooCommerceWebhookEvent;
use App\Services\WooCommerceCatalogService;
use Illuminate\Http\Request;

class WooCommerceIntegrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:productos_list');
    }

    public function index()
    {
        $duplicateCodes = Producto::query()->select('codigo')->whereNotNull('codigo')
            ->groupBy('codigo')->havingRaw('COUNT(*) > 1')->pluck('codigo');

        $products = Producto::query()
            ->with('wooCommerceLink.category')
            ->whereNotIn('codigo', $duplicateCodes)
            ->orderBy('codigo')
            ->paginate(50);

        return view('woocommerce.index', [
            'products' => $products,
            'stats' => [
                'erp' => Producto::count(),
                'linked' => WooCommerceProductLink::whereNotNull('woocommerce_product_id')->count(),
                'pending' => Producto::whereDoesntHave('wooCommerceLink', fn ($link) => $link->whereNotNull('woocommerce_product_id'))->count(),
                'orders' => WooCommerceOrder::count(),
                'errors' => WooCommerceWebhookEvent::where('status', 'failed')->count(),
                'duplicates' => $duplicateCodes->count(),
            ],
        ]);
    }

    public function importCategories(WooCommerceCatalogService $catalog)
    {
        $count = $catalog->importCategories();
        return back()->with('success', "Se importaron o actualizaron {$count} categorías desde WooCommerce.");
    }

    public function storeCategory(Request $request, WooCommerceCatalogService $catalog)
    {
        $data = $request->validate(['name' => 'required|string|max:191', 'parent_id' => 'nullable|exists:woocommerce_categories,id']);
        $parent = isset($data['parent_id']) ? WooCommerceCategory::find($data['parent_id']) : null;
        $catalog->createCategory($data['name'], $parent);
        return back()->with('success', 'Categoría creada en WooCommerce y vinculada al ERP.');
    }

    public function syncProduct(Request $request, Producto $producto, WooCommerceCatalogService $catalog)
    {
        $data = $request->validate(['category_id' => 'nullable|exists:woocommerce_categories,id']);
        $category = isset($data['category_id']) ? WooCommerceCategory::find($data['category_id']) : null;

        try {
            $catalog->syncNewProduct($producto, $category);
            return back()->with('success', "{$producto->codigo} fue creado como borrador en WooCommerce.");
        } catch (\Throwable $e) {
            return back()->withErrors(['woocommerce' => $e->getMessage()]);
        }
    }

    public function syncAll(WooCommerceCatalogService $catalog)
    {
        $products = $catalog->candidates();
        $synced = 0;
        $errors = 0;

        foreach ($products as $product) {
            try {
                $catalog->syncProduct($product);
                $synced++;
            } catch (\Throwable $e) {
                WooCommerceProductLink::updateOrCreate(
                    ['producto_id' => $product->id],
                    ['sku' => trim((string) $product->codigo), 'status' => 'error', 'last_error' => $e->getMessage()]
                );
                $errors++;
            }
        }

        return back()->with('success', "Sincronización finalizada: {$synced} productos enviados y {$errors} observaciones. Los productos existentes de WooCommerce no fueron modificados.");
    }

    public function updateProductCategory(Request $request, Producto $producto, WooCommerceCatalogService $catalog)
    {
        $data = $request->validate(['category_id' => 'required|exists:woocommerce_categories,id']);
        $link = $producto->wooCommerceLink;

        if (! $link?->woocommerce_product_id) {
            return back()->withErrors(['woocommerce' => 'El producto todavía no está vinculado con WooCommerce.']);
        }

        $link->update(['woocommerce_category_id' => $data['category_id']]);

        try {
            $catalog->updateLinkedProduct($link->refresh());
            return back()->with('success', "La categoría de {$producto->codigo} se actualizó en WooCommerce.");
        } catch (\Throwable $e) {
            $link->update(['status' => 'error', 'last_error' => $e->getMessage()]);
            return back()->withErrors(['woocommerce' => $e->getMessage()]);
        }
    }
}
