@extends('plantilla.app')

@push('estilos')
<!-- SELECT2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<style>
.card {
    border-radius: 10px;
}

.table thead {
    background: #343a40;
    color: white;
    font-size: 13px;
}

.table td,
.table th {
    padding: 6px;
    font-size: 13px;
}

.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 4px 10px;
    font-size: 13px;
    border-radius: 6px !important;
}

/* Select2 */
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

.sale-detail-row[hidden] {
    display: none !important;
}
</style>
@endpush

@section('contenido')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">
            <i class="bi bi-bar-chart-line-fill me-2"></i>Reporte de Ventas
        </h3>
    </div>

    <div class="card shadow-sm border-0">

        {{-- FILTROS --}}
        <div class="card-header bg-light border-0">
            <form method="GET" action="{{ route('reportes.ventas') }}" class="row g-2">

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
                    <label>Vendedor</label>
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

                <div class="col-md-3">
                    <label>Cliente</label>
                    <select name="cliente_id" class="form-select select2">
                        <option value="">Todos</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}"
                                {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->razon_social }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">

                    <button class="btn btn-primary w-100">
                        <i class="bi bi-funnel-fill"></i>
                    </button>

                    <a href="{{ route('reportes.ventas') }}"
                        class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>

                    <a href="{{ route('reportes.ventas.exportar', request()->all()) }}"
                        class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>

                </div>

            </form>
        </div>

        <div class="card-body">

            {{-- TOTAL --}}
            <div class="alert alert-success d-flex justify-content-between">
                <strong>Total Ventas</strong>
                <h4 class="mb-0 fw-bold">
                    S/ {{ number_format($totalGeneral, 2) }}
                </h4>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="text-center">
                        <tr>
                            <th>Fecha</th>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($ventas as $venta)

                        <tr class="text-center">

                            <td>
                                {{ $venta->created_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">
                                    {{ $venta->created_at->format('H:i') }}
                                </small>
                            </td>

                            <td>
                                {{ $venta->serie }}-{{ str_pad($venta->correlativo,8,'0',STR_PAD_LEFT) }}
                            </td>

                            <td class="text-start">
                                {{ $venta->cliente->razon_social ?? '-' }}
                            </td>

                            <td>
                                {{ $venta->user->name ?? '-' }}
                            </td>

                            <td class="fw-bold text-success">
                                S/ {{ number_format($venta->total, 2) }}
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary sale-detail-toggle"
                                    data-detail-target="detalle-{{ $venta->id }}"
                                    aria-controls="detalle-{{ $venta->id }}"
                                    aria-expanded="false"
                                    title="Ver detalle">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </td>

                        </tr>

                        {{-- DETALLE --}}
                        <tr class="sale-detail-row bg-light" id="detalle-{{ $venta->id }}" hidden>
                            <td colspan="6">

                                <div class="p-3 border rounded bg-white shadow-sm">

                                    <h6 class="mb-3 text-secondary">
                                        <i class="bi bi-list-ul me-1"></i>
                                        Detalle de la Venta
                                    </h6>

                                    <table class="table table-sm table-bordered mb-0">

                                        <thead class="table-secondary text-center">
                                            <tr>
                                                <th>Item</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            {{-- PRODUCTOS --}}
                                            @if($venta->productos->count())
                                                <tr class="table-light fw-bold">
                                                    <td colspan="4" class="text-primary">
                                                        PRODUCTOS
                                                    </td>
                                                </tr>

                                                @foreach($venta->productos as $p)
                                                <tr>
                                                    <td>{{ $p->producto->nombre ?? '-' }}</td>
                                                    <td class="text-center">{{ $p->cantidad }}</td>
                                                    <td class="text-center">
                                                        S/ {{ number_format($p->precio_unitario, 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        S/ {{ number_format($p->subtotal, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif


                                            {{-- SERVICIOS --}}
                                            @if($venta->servicios->count())
                                                <tr class="table-light fw-bold">
                                                    <td colspan="4" class="text-success">
                                                        SERVICIOS
                                                    </td>
                                                </tr>

                                                @foreach($venta->servicios as $item)
                                                <tr>
                                                    <td>{{ $item->servicio->descripcion ?? '-' }}</td>
                                                    <td class="text-center">{{ $item->cantidad }}</td>
                                                    <td class="text-center">
                                                        S/ {{ number_format($item->precio_unitario, 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        S/ {{ number_format($item->subtotal, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif


                                            {{-- TRANSPORTE HERRAMIENTAS --}}
                                            @if(isset($venta->transportes) && $venta->transportes->count())
                                                <tr class="table-light fw-bold">
                                                    <td colspan="4" class="text-warning">
                                                        TRANSPORTE DE HERRAMIENTAS
                                                    </td>
                                                </tr>

                                                @foreach($venta->transportes as $item)
                                                <tr>
                                                    <td>{{ $item->transporte->name ?? 'Transporte' }}</td>
                                                    <td class="text-center">{{ $item->cantidad }}</td>
                                                    <td class="text-center">
                                                        S/ {{ number_format($item->precio_unitario, 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        S/ {{ number_format($item->subtotal, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif


                                            {{-- TRANSPORTE PERSONAL --}}
                                            @if(isset($venta->tpersonal) && $venta->tpersonal->count())
                                                <tr class="table-light fw-bold">
                                                    <td colspan="4" class="text-danger">
                                                        TRANSPORTE DE PERSONAL
                                                    </td>
                                                </tr>

                                                @foreach($venta->tpersonal as $item)
                                                <tr>
                                                    <td>{{ $item->tpersonal->name ?? 'T. Personal' }}</td>
                                                    <td class="text-center">{{ $item->cantidad }}</td>
                                                    <td class="text-center">
                                                        S/ {{ number_format($item->precio_unitario, 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        S/ {{ number_format($item->subtotal, 2) }}
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif


                                            {{-- RESUMEN --}}
                                            
                                            @php
                                                $totalVenta = (float) ($venta->total ?? 0);
                                                $opGravada = round($totalVenta / 1.18, 2);
                                                $igv = round($totalVenta - $opGravada, 2);
                                            @endphp

                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-end fw-semibold">
                                                    Gravada
                                                </td>
                                                <td class="text-end">
                                                    S/ {{ number_format($opGravada, 2) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-end fw-semibold">
                                                    IGV
                                                </td>
                                                <td class="text-end">
                                                    S/ {{ number_format($igv, 2) }}
                                                </td>
                                            </tr>

                                            <tr class="fw-bold">
                                                <td colspan="2"></td>
                                                <td class="text-end">
                                                    Total
                                                </td>
                                                <td class="text-end text-success">
                                                    S/ {{ number_format($venta->total ?? 0, 2) }}
                                                </td>
                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                No hay ventas registradas
                            </td>
                        </tr>

                    @endforelse
                    </tbody>

                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $ventas->appends(request()->all())->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        width: '100%',
        placeholder: 'Seleccione una opción',
        allowClear: true
    });

    document.querySelectorAll('.sale-detail-toggle').forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();

            const detailRow = document.getElementById(button.dataset.detailTarget);
            if (!detailRow) return;

            const willOpen = detailRow.hidden;
            detailRow.hidden = !willOpen;
            button.setAttribute('aria-expanded', String(willOpen));
            button.setAttribute('title', willOpen ? 'Ocultar detalle' : 'Ver detalle');

            const icon = button.querySelector('i');
            if (icon) {
                icon.className = willOpen ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    });
});
</script>

@endpush
