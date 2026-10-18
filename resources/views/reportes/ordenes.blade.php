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
    vertical-align: middle;
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

.report-detail-panel[hidden] {
    display: none !important;
}
</style>
@endpush

@section('contenido')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary">
            <i class="bi bi-clipboard-data-fill me-2"></i>
            Reporte de Órdenes de Trabajo
        </h3>
    </div>

    <div class="card shadow-sm border-0">

        {{-- FILTROS --}}
        <div class="card-header bg-light border-0">
            <form method="GET" action="{{ route('reportes.ordenes') }}" class="row g-2">

                {{-- CLIENTE --}}
                <div class="col-md-4">
                    <label>Cliente</label>
                    <select name="cliente_id" class="form-select select2">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->razon_social }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ESTADO --}}
                <div class="col-md-3">
                    <label>Estado</label>
                    <select name="estado" class="form-select select2">
                        <option value="">Todos</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}"
                                {{ request('estado') == $estado->id ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- CÓDIGO OT --}}
                <div class="col-md-3">
                    <label>Código OT Cliente</label>
                    <input
                        type="text"
                        name="codigo_ot_cliente"
                        class="form-control"
                        value="{{ request('codigo_ot_cliente') }}"
                        placeholder="Buscar código OT">
                </div>


                {{-- MOTIVO --}}
                <div class="col-md-3">
                    <label>Motivo</label>
                    <input
                        type="text"
                        name="motivo"
                        class="form-control"
                        value="{{ request('motivo') }}"
                        placeholder="Buscar Motivo">
                </div>

                {{-- BOTONES --}}
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-funnel-fill"></i>
                    </button>

                    <a href="{{ route('reportes.ordenes') }}"
                       class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>

            </form>
        </div>

        <div class="card-body">

            {{-- TOTAL --}}
            <div class="alert alert-primary d-flex justify-content-between">
                <strong>Total de Órdenes</strong>
                <h4 class="mb-0 fw-bold">
                    {{ $ordenes->total() }}
                </h4>
            </div>

            {{-- TABLA PRINCIPAL --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="text-center">
                        <tr>
                            <th width="60">Timeline</th>
                            <th>Fecha</th>
                            <th>Código OT</th>
                            <th>Cliente</th>
                            <th>Descripción</th>
                            <th>Técnico</th>
                            <th>Sede</th>
                            <th>Estado</th>
                            <th width="80">Ver</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($ordenes as $ot)

                        {{-- FILA PRINCIPAL --}}
                        <tr class="text-center">

                            <td>
                                <a href="{{ route('ordenes.timeline', $ot->id) }}"
                                class="btn btn-sm btn-outline-primary"
                                title="Ver Timeline">
                                    <i class="bi bi-graph-up"></i>
                                </a>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($ot->created_at)->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($ot->created_at)->format('H:i') }}
                                </small>
                            </td>

                            <td class="fw-bold">
                                {{ $ot->codigo_ot_cliente ?? '-' }}
                            </td>

                            <td class="text-start">
                                {{ $ot->cliente->razon_social ?? '-' }}
                            </td>

                            <td class="text-start">
                                {{ $ot->descripcion ?? '-' }}
                            </td>

                            <td>
                                {{ $ot->tecnico->nombre ?? '-' }}
                            </td>

                            <td>
                                {{ $ot->sedes->nombre ?? '-' }}
                            </td>

                            <td>
                                <span class="badge
                                    bg-{{ $ot->estadoCatalogo->color ?? 'secondary' }}
                                    text-{{ $ot->estadoCatalogo->color_texto ?? 'white' }}
                                    border
                                    border-{{ $ot->estadoCatalogo->color ?? 'secondary' }}
                                    fw-semibold">

                                    {{ $ot->estadoCatalogo->nombre ?? 'Sin estado' }}

                                </span>
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary report-detail-toggle"
                                    data-detail-target="detalle-{{ $ot->id }}"
                                    aria-expanded="false"
                                    aria-controls="detalle-{{ $ot->id }}"
                                    title="Ver detalle">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </td>

                        </tr>

                        {{-- DETALLE OCULTO --}}
                        <tr>
                            <td colspan="9" class="p-0 border-0">

                                <div
                                    id="detalle-{{ $ot->id }}"
                                    class="report-detail-panel"
                                    hidden>

                                    <div class="p-3 bg-light border rounded shadow-sm">

                                        <h6 class="mb-3 text-secondary">
                                            <i class="bi bi-list-ul me-1"></i>
                                            Detalle de la Orden de Trabajo
                                        </h6>

                                        {{-- TABLA DETALLE PRINCIPAL --}}
                                        <table class="table table-sm table-bordered mb-3">
                                            <tbody>

                                                <tr>
                                                    <th width="25%">Responsable</th>
                                                    <td>
                                                        {{ $ot->responsables->nombre ?? '-' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Usuario Registro</th>
                                                    <td>
                                                        {{ $ot->user->name ?? '-' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Motivo</th>
                                                    <td>
                                                        {{ $ot->motivo ?? '-' }}
                                                    </td>
                                                </tr>

                                               
                                                

                                            </tbody>
                                        </table>

                                        {{-- TABLA DOCUMENTOS --}}
                                        <table class="table table-sm table-bordered mb-0">

                                            <thead class="table-secondary text-center">
                                                <tr>
                                                    <th colspan="6">
                                                        Documentación y Archivos Relacionados
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                                {{-- FILA 1 --}}
                                                <tr>
                                                    <th>Parte Firmado</th>
                                                    <td>
                                                        @if(!empty($ot->archivo_pdf))
                                                            <a href="{{ asset('uploads/ordenes_trabajo/' . $ot->archivo_pdf) }}"
                                                               target="_blank">
                                                                <i class="bi bi-file-earmark-pdf text-primary"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <th>Factura</th>
                                                    <td>
                                                        @if(!empty($ot->archivo_factura))
                                                            <a href="{{ asset('uploads/ordenes_trabajo/facturacion/' . $ot->archivo_factura) }}"
                                                               target="_blank">
                                                                <i class="bi bi-coin text-success"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <th>Nota Crédito</th>
                                                    <td>
                                                        @if(!empty($ot->notaCredito->archivo_nota))
                                                            <a href="{{ asset('storage/' . $ot->notaCredito->archivo_nota) }}"
                                                               target="_blank">
                                                                <i class="bi bi-file-earmark-pdf text-danger"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>

                                                {{-- FILA 2 --}}
                                                <tr>
                                                    <th>Validación N.C.</th>
                                                    <td>
                                                        @if(!empty($ot->notaCredito->valida_nota))
                                                            <a href="{{ asset('storage/' . $ot->notaCredito->valida_nota) }}"
                                                               target="_blank">
                                                                <i class="bi bi-shield-check text-success"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <th>Detalle N.C.</th>
                                                    <td>
                                                        @if(!empty($ot->notaCredito->detalles_nota))
                                                            <a href="{{ asset('storage/' . $ot->notaCredito->detalles_nota) }}"
                                                               target="_blank">
                                                                <i class="bi bi-file-earmark-text text-primary"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <th>Voucher Depósito</th>
                                                    <td>
                                                        @if(!empty($ot->pagos->voucher_pago))
                                                            <a href="{{ asset('uploads/ordenes_trabajo/pagos/' . $ot->pagos->voucher_pago) }}"
                                                               target="_blank">
                                                                <i class="bi bi-postcard-fill text-danger"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>

                                                {{-- FILA 3 --}}
                                                <tr>
                                                    <th>Subida Portal</th>
                                                    <td>
                                                        @if(!empty($ot->proceso_pago))
                                                            <a href="{{ asset('uploads/ordenes_trabajo/procesopago/' . $ot->proceso_pago) }}"
                                                               target="_blank">
                                                                <i class="bi bi-images text-warning"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <th>Sustento</th>
                                                    <td>
                                                        @if(!empty($ot->archivo_sustento))
                                                            <a href="{{ asset('uploads/ordenes_trabajo/facturacion/' . $ot->archivo_sustento) }}"
                                                               target="_blank">
                                                                <i class="bi bi-person-vcard-fill text-primary"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    <th>Orden Servicio</th>
                                                    <td>
                                                        @if(!empty($ot->ordenServicio->archivo_pdf))
                                                            <a href="{{ asset('uploads/ordenes_servicio/' . $ot->ordenServicio->archivo_pdf) }}"
                                                               target="_blank">
                                                                <i class="bi bi-file-earmark-pdf-fill text-success"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>

                                                {{-- FILA 4 --}}
                                                <tr>
                                                    <th>Documentación</th>
                                                    <td>
                                                        @if(!empty($ot->captura_info))
                                                            <a href="{{ asset('uploads/ot_documentacion/' . $ot->captura_info) }}"
                                                               target="_blank">
                                                                <i class="bi bi-images text-primary"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    {{-- COTIZACIÓN --}}
                                                    <th>Cotización</th>
                                                    <td>
                                                        @php
                                                            $cotizacion = $ot->cotizacion;
                                                        @endphp

                                                        @if($ot->estado >= 3 && $cotizacion)
                                                            <a href="{{ route('cotizaciones.imprimir', $cotizacion->id) }}"
                                                               target="_blank">
                                                                <i class="bi bi-file-earmark-text text-danger"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>

                                                    {{-- INFORME --}}
                                                    <th>Informe</th>
                                                    <td>
                                                        @php
                                                            $informe = $ot->informes->first();
                                                        @endphp

                                                        @if($ot->estado >= 2 && $informe)
                                                            <a href="{{ route('informes.imprimir', $informe->id) }}"
                                                               target="_blank">
                                                                <i class="bi bi-journal-text text-dark"
                                                                   style="font-size:18px;"></i>
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>

                                                {{-- FILA 5 --}}
                                                <tr>

                                                 {{-- NÚMERO DE OPERACIÓN --}}
                                                <th>Número de Operación</th>
                                                <td>
                                                    @if($ot->estado == 13)
                                                        {{$ot->pagos->num_operacion}}
                                                    @else
                                                        No existen datos
                                                    @endif
                                                </td>

                                                

                                                {{-- FECHA DE PAGO --}}
                                                <th>Fecha de Pago</th>
                                                <td>
                                                    @if($ot->estado == 13)
                                                        {{$ot->pagos->fecha_pago}}
                                                    @else
                                                        No existen datos
                                                    @endif
                                                </td>

                                                </tr>

                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="text-center">
                                No hay órdenes de trabajo registradas
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $ordenes->appends(request()->all())->links('pagination::bootstrap-5') }}
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

            const detailPanel = document.getElementById(button.dataset.detailTarget);
            if (!detailPanel) return;

            const willOpen = detailPanel.hidden;
            detailPanel.hidden = !willOpen;
            button.setAttribute('aria-expanded', String(willOpen));
            button.setAttribute('title', willOpen ? 'Ocultar detalle' : 'Ver detalle');

            const icon = button.querySelector('i');
            if (icon) icon.className = willOpen ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

});
</script>

@endpush
