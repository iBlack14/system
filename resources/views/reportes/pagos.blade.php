@extends('plantilla.app')

@push('estilos')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<style>
.card {
    border-radius: 10px;
}

.report-detail-row[hidden] {
    display: none !important;
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
</style>
@endpush

@section('contenido')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-success">
            <i class="bi bi-cash-coin me-2"></i>
            Reporte de Pagos
        </h3>
    </div>

    <div class="card shadow-sm border-0">

        {{-- FILTROS --}}
        <div class="card-header bg-light border-0">
            <form method="GET" action="{{ route('reportes.pagos') }}" class="row g-2">

                <div class="col-md-3">
                    <label>Fecha Inicio</label>
                    <input type="date"
                        name="fecha_inicio"
                        class="form-control"
                        value="{{ request('fecha_inicio') }}">
                </div>

                <div class="col-md-3">
                    <label>Fecha Fin</label>
                    <input type="date"
                        name="fecha_fin"
                        class="form-control"
                        value="{{ request('fecha_fin') }}">
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

                    <button class="btn btn-success w-100">
                        <i class="bi bi-funnel-fill"></i>
                    </button>

                    <a href="{{ route('reportes.pagos') }}"
                        class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>

                    <!-- <a href="{{ route('reportes.pagos.exportar', request()->all()) }}"
                        class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a> -->

                </div>

            </form>
        </div>

        <div class="card-body">

            {{-- TOTAL --}}
            <div class="alert alert-success d-flex justify-content-between">
                <strong>Total Pagos</strong>
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
                            <th>OT</th>
                            <th>Cliente</th>
                            <!-- <th>Usuario</th> -->
                            <th>Monto</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($pagos as $pago)

                        <tr class="text-center">

                            <td>
                                {{ $pago->created_at->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">
                                    {{ $pago->created_at->format('H:i') }}
                                </small>
                            </td>

                            <td>
                                {{ $pago->ordenTrabajo->codigo_ot_cliente ?? '-' }}
                            </td>

                            <td class="text-start">
                                {{ $pago->ordenTrabajo->cliente->razon_social ?? '-' }}
                            </td>

                            <!-- <td>
                                {{ $pago->user->name ?? '-' }}
                            </td> -->

                            <td class="fw-bold text-success">
                                S/ {{ number_format($pago->monto_pagado ?? 0, 2) }}
                            </td>

                            <td>
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary report-detail-toggle"
                                    data-detail-target="detalle-{{ $pago->id }}"
                                    aria-controls="detalle-{{ $pago->id }}"
                                    aria-expanded="false"
                                    title="Ver detalle">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </td>

                        </tr>

                        {{-- DETALLE --}}
                        <tr class="report-detail-row bg-light" id="detalle-{{ $pago->id }}" hidden>
                            <td colspan="6">

                                <div class="p-3 border rounded bg-white shadow-sm">

                                    <h6 class="mb-3 text-secondary">
                                        <i class="bi bi-receipt me-1"></i>
                                        Detalle del Pago
                                    </h6>

                                    <table class="table table-sm table-bordered mb-0">

                                        <tbody>

                                            <tr>
                                                <th>Monto Pagado</th>
                                                <td>{{ $pago->monto_pagado ?? '-' }}</td>
                                            </tr>

                                             <tr>
                                                <th>Monto Detracción</th>
                                                <td>{{ $pago->monto_detraccion ?? '-' }}</td>
                                            </tr>

                                            <tr>
                                                <th>Número de Operación</th>
                                                <td>{{ $pago->num_operacion ?? '-' }}</td>
                                            </tr>

                                            <tr>
                                                <th>Fecha de Pago</th>
                                                <td>{{ $pago->fecha_pago ?? '-' }}</td>
                                            </tr>

                                            <tr>
                                                <th>Banco</th>
                                                <td>{{ $pago->bancos->descripcion ?? '-' }}</td>
                                            </tr>

                                            <tr>
                                                <th>Voucher de Pago</th>
                                                <td>
                                                    @if(!empty($pago->voucher_pago))
                                                        <a href="{{ asset('uploads/ordenes_trabajo/pagos/' . $pago->voucher_pago) }}"
                                                        target="_blank">
                                                            <i class="bi bi-postcard-fill text-danger"
                                                            style="font-size:18px;"></i>
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Observación</th>
                                                <td>{{ $pago->observacion ?? '-' }}</td>
                                            </tr>

                                            <!-- <tr>
                                                <th>Registrado por</th>
                                                <td>{{ $pago->user->name ?? '-' }}</td>
                                            </tr> -->

                                        </tbody>

                                    </table>

                                </div>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                No hay pagos registrados
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $pagos->appends(request()->all())->links('pagination::bootstrap-5') }}
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

    document.querySelectorAll('.report-detail-toggle').forEach(button => {
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
            if (icon) icon.className = willOpen ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
});
</script>

@endpush
