@extends('plantilla.app')

@push('estilos')
<!-- SELECT2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<style>
.card { border-radius: 10px; }

.table thead {
    background: #343a40;
    color: white;
    font-size: 13px;
}

.table td, .table th {
    padding: 6px;
    font-size: 13px;
}

.pagination { margin-bottom: 0; }

.pagination .page-link {
    padding: 4px 10px;
    font-size: 13px;
    border-radius: 6px !important;
}

/* Select2 visual */
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #ced4da !important;
    border-radius: 6px !important;
    padding: 4px 8px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}
</style>
@endpush

@section('contenido')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-danger">
            <i class="bi bi-bag-fill me-2"></i>Reporte de Compras
        </h3>
    </div>

    <div class="card shadow-sm border-0">

        {{-- FILTROS --}}
        <div class="card-header bg-light border-0">
            <form method="GET" action="{{ route('reportes.compras') }}" class="row g-3">

                <div class="col-md-2">
                    <label>Fecha Inicio</label>
                    <input type="date"
                           name="fecha_inicio"
                           class="form-control"
                           value="{{ request('fecha_inicio') }}">
                </div>

                <div class="col-md-2">
                    <label>Fecha Fin</label>
                    <input type="date"
                           name="fecha_fin"
                           class="form-control"
                           value="{{ request('fecha_fin') }}">
                </div>

                <div class="col-md-2">
                    <label>Usuario</label>
                    <select name="user_id" class="form-select select2">
                        <option value="">Todos</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}"
                                {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Proveedor</label>
                    <select name="proveedor_id" class="form-select select2">
                        <option value="">Todos</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}"
                                {{ request('proveedor_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->razon_social }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Producto</label>
                    <select name="producto_id" class="form-select select2">
                        <option value="">Todos</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}"
                                {{ request('producto_id') == $prod->id ? 'selected' : '' }}>
                                {{ $prod->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-danger w-100">
                        <i class="bi bi-funnel-fill"></i>
                    </button>

                    <a href="{{ route('reportes.compras') }}"
                       class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>

            </form>
        </div>

        <div class="card-body">

            {{-- TOTAL --}}
            <div class="mb-4">
                <div class="alert alert-danger d-flex justify-content-between align-items-center shadow-sm">
                    <strong>Total Compras</strong>
                    <h4 class="mb-0 fw-bold">
                        S/ {{ number_format($totalGeneral, 2) }}
                    </h4>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="text-center">
                        <tr>
                            <th>Fecha</th>
                            <th>Comprobante</th>
                            <th>Proveedor</th>
                            <th>Usuario</th>
                            <th>IGV</th>
                            <th>Total</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($compras as $compra)

                        <tr class="text-center">

                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y') }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-danger">
                                    {{ $compra->serie }}-{{ str_pad($compra->correlativo,8,'0',STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <td class="text-start">
                                {{ $compra->proveedor->razon_social ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $compra->user->name ?? '-' }}
                                </span>
                            </td>

                            <td>
                                S/ {{ number_format($compra->impuesto,2) }}
                            </td>

                            <td>
                                <span class="fw-bold text-danger">
                                    S/ {{ number_format($compra->total,2) }}
                                </span>
                            </td>

                            <td>
                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#detalle-{{ $compra->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>

                        </tr>

                        {{-- DETALLE --}}
                        <tr class="collapse bg-light" id="detalle-{{ $compra->id }}">
                            <td colspan="7">

                                <div class="p-3 border rounded bg-white shadow-sm">

                                    <h6 class="mb-3 text-secondary">
                                        <i class="bi bi-list-ul me-1"></i>Detalle de la Compra
                                    </h6>

                                    <table class="table table-sm table-bordered mb-0">

                                        <thead class="table-secondary text-center">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            @foreach($compra->detalles as $item)
                                            <tr>
                                                <td>{{ $item->producto->nombre ?? '-' }}</td>
                                                <td class="text-center">{{ $item->cantidad }}</td>
                                                <td class="text-center">
                                                    S/ {{ number_format($item->valor_unitario,2) }}
                                                </td>
                                                <td class="text-end fw-bold">
                                                    S/ {{ number_format($item->cantidad * $item->valor_unitario,2) }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-end fw-semibold">Gravada</td>
                                                <td class="text-end">
                                                    S/ {{ number_format($compra->op_gravada,2) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-end fw-semibold">IGV</td>
                                                <td class="text-end">
                                                    S/ {{ number_format($compra->impuesto,2) }}
                                                </td>
                                            </tr>

                                            <tr class="fw-bold">
                                                <td colspan="2"></td>
                                                <td class="text-end">Total</td>
                                                <td class="text-end text-danger">
                                                    S/ {{ number_format($compra->total,2) }}
                                                </td>
                                            </tr>

                                            

                                        </tbody>

                                    </table>

                                </div>

                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No hay compras registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $compras->onEachSide(1)->appends(request()->all())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        width: '100%',
        placeholder: 'Seleccione una opción',
        allowClear: true
    });
});
</script>

@endpush