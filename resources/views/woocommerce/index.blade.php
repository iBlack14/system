@extends('plantilla.app')

@section('contenido')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h3 mb-1">Integración WooCommerce</h1>
            <p class="text-muted mb-0">El ERP controla automáticamente SKU, nombre, descripción, precio y stock de sus productos vinculados.</p>
        </div>
        <form method="POST" action="{{ route('woocommerce.products.sync-all') }}" onsubmit="return confirm('Se enviarán todos los productos pendientes del ERP. Los productos existentes de WooCommerce no serán modificados. ¿Continuar?')">
            @csrf
            <button class="btn btn-primary"><i class="bi bi-cloud-arrow-up me-1"></i>Sincronización inicial</button>
        </form>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="row g-3 mb-4">
        @foreach(['erp'=>'Productos ERP','linked'=>'Vinculados','pending'=>'Pendientes','orders'=>'Pedidos web','errors'=>'Errores','duplicates'=>'Códigos duplicados'] as $key=>$label)
        <div class="col-6 col-sm-4 col-md-2"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="fs-3 fw-semibold">{{ $stats[$key] }}</div></div></div></div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header fw-semibold">Productos del ERP</div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0 mobile-records">
                <thead><tr><th>SKU</th><th>Nombre</th><th class="text-end">Precio</th><th class="text-end">Stock</th><th>Estado</th><th>Acción</th></tr></thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td data-mobile-label="SKU"><code>{{ $product->codigo }}</code></td>
                        <td data-mobile-label="Nombre">{{ $product->nombre }}</td>
                        <td data-mobile-label="Precio" class="text-end">S/ {{ number_format((float)$product->precio_venta, 2) }}</td>
                        <td data-mobile-label="Stock" class="text-end">{{ $product->stock }}</td>
                        <td data-mobile-label="Estado">
                            @if($product->wooCommerceLink?->woocommerce_product_id)
                                <span class="badge bg-success">Sincronizado</span>
                            @elseif($product->wooCommerceLink?->status === 'error')
                                <span class="badge bg-danger">Error</span>
                            @else
                                <span class="badge bg-secondary">Pendiente</span>
                            @endif
                        </td>
                        <td data-mobile-label="Acción" class="mobile-record-actions">
                            @if($product->wooCommerceLink?->woocommerce_product_id)
                                <div class="small text-muted">Woo #{{ $product->wooCommerceLink->woocommerce_product_id }}</div>
                            @elseif($product->wooCommerceLink?->status === 'error')
                                <span class="small text-danger" title="{{ $product->wooCommerceLink->last_error }}">Requiere revisión</span>
                            @else
                                <span class="small text-muted">Pendiente de sincronización inicial</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $products->links() }}</div>
    </div>
</div>
@endsection
