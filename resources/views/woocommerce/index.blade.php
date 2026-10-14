@extends('plantilla.app')

@section('contenido')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h3 mb-1">Integración WooCommerce</h1>
            <p class="text-muted mb-0">Solo administra productos creados explícitamente por este módulo.</p>
        </div>
        <form method="POST" action="{{ route('woocommerce.categories.import') }}">
            @csrf
            <button class="btn btn-outline-primary"><i class="bi bi-arrow-repeat me-1"></i>Importar categorías</button>
        </form>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="row g-3 mb-4">
        @foreach(['erp'=>'Productos ERP','linked'=>'Vinculados','pending'=>'Pendientes','orders'=>'Pedidos web','errors'=>'Errores','duplicates'=>'Códigos duplicados'] as $key=>$label)
        <div class="col-6 col-sm-4 col-md-2"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="fs-3 fw-semibold">{{ $stats[$key] }}</div></div></div></div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Crear categoría</div>
        <div class="card-body">
            <form method="POST" action="{{ route('woocommerce.categories.store') }}" class="row g-2">
                @csrf
                <div class="col-md-5"><input name="name" class="form-control" placeholder="Nombre de categoría" required></div>
                <div class="col-md-5"><select name="parent_id" class="form-select"><option value="">Sin categoría superior</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Crear</button></div>
            </form>
        </div>
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
                        <td data-mobile-label="Estado">@if($product->wooCommerceLink)<span class="badge bg-success">{{ $product->wooCommerceLink->status }}</span>@else<span class="badge bg-secondary">No vinculado</span>@endif</td>
                        <td data-mobile-label="Acción" class="mobile-record-actions">
                            @if(!$product->wooCommerceLink)
                            <form method="POST" action="{{ route('woocommerce.products.sync', $product) }}" class="d-flex flex-wrap gap-2" onsubmit="return confirm('Se creará este producto como BORRADOR en WooCommerce. ¿Continuar?')">
                                @csrf
                                <select name="category_id" class="form-select form-select-sm"><option value="">Sin categoría</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
                                <button class="btn btn-sm btn-primary text-nowrap">Crear borrador</button>
                            </form>
                            @else
                                <form method="POST" action="{{ route('woocommerce.products.category', $product) }}" class="d-flex flex-wrap gap-2">
                                    @csrf @method('PUT')
                                    <select name="category_id" class="form-select form-select-sm" required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected($product->wooCommerceLink->woocommerce_category_id === $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary text-nowrap">Mover categoría</button>
                                </form>
                                <div class="small text-muted mt-1">Woo #{{ $product->wooCommerceLink->woocommerce_product_id }}</div>
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
