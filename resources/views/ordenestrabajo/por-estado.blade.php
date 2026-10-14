@extends('plantilla.app')

@push('estilos')
@endpush

@section('contenido')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Órdenes de Trabajo – Estado: {{ $estadoCatalogo->nombre }}
            <span class="badge"
                  style="background-color: {{ $estadoCatalogo->color }};
                         color: {{ $estadoCatalogo->color_texto }}">                
            </span>
        </h4>

        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">

            @php
            $headers = [
            'doc_cargada'   => '📎 Doc. cargada',
            'informe_pdf'   => '📄 Ver Informe',
            'monto_cotizado'=> '💰 Monto cotizado',
            'sedes' => 'Sede',
            'codigo_ot_cliente'=>'Código OT',
            'parte_firmado_pdf'=>'Ver Parte Firmado',
            'codigo_orden_servicio' => 'Cod. Orden Servicio',
            'pdf_ordenservicio'=>'Ver O.S.',
            'montoos' => 'Monto O.S',
            'visor_factura' => 'Ver Factura' ,
            'archivo_nota' => ' Ver Nota Crédito',
            'detalles_nota' => ' Ver Detalle Nota',
            'valida_nota' => 'Ver Validación nota',
            'visor_sustento' => 'Ver Sustento',
            'visor_subida_portal' =>'Visor Portal',
            'total'=>'Total',
            'tiempo' => 'Tiempo Parcial',
            'tiempo_creacion' => '⏱ Tiempo Total',
            ];
            @endphp


                <table id="tablaEstado"
                       class="table table-bordered table-striped table-hover align-middle mobile-records">

                    <thead class="table-light">
                        <tr>
                            @foreach ($columnas as $col)
                            <th>
                                {{ $headers[$col] ?? ucfirst(str_replace('_', ' ', $col)) }}
                            </th>
                            @endforeach                          
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($ordenes as $orden)
                            <tr>

                                @foreach ($columnas as $col)
                                    <td data-mobile-label="{{ $headers[$col] ?? ucfirst(str_replace('_', ' ', $col)) }}">

                                        @switch($col)

                                            @case('codigo_ot_cliente')
                                                {{ $orden->codigo_ot_cliente }}
                                            @break

                                            @case('cliente')
                                                {{ $orden->cliente->razon_social ?? '-' }}
                                            @break

                                            @case('sedes')
                                                {{ $orden->sedes->nombre ?? '-' }}
                                            @break

                                            @case('motivo')
                                                {{ $orden->motivo }}
                                            @break

                                            @case('codigo_orden_servicio')
                                                {{ optional($orden->ordenServicio)->codigo_orden_servicio ?? '-' }}
                                            @break

                                           @case('fecha_cancelada')
                                                {{ optional($orden->pagos)->fecha_pago 
                                                ? \Carbon\Carbon::parse(optional($orden->pagos)->fecha_pago)->format('d/m/Y') 
                                                : '-' 
                                                }}
                                            @break

                                            @case('monto_depositado')                                                
                                                S/ {{ number_format($orden->pagos->monto_pagado, 2) }}  
                                            @break

                                            @case('monto_detraccion')
                                                S/{{ optional($orden->pagos)->monto_detraccion, 2 }}
                                            @break

                                            @case('comprobante_tipo')
                                                {{ $orden->ventas->comprobanteTipo->descripcion ?? '-' }}
                                            @break

                                            @case('serie')
                                                {{ $orden->ventas->serie ?? '-' }}
                                            @break

                                            @case('correlativo')
                                                {{ $orden->ventas->correlativo ?? '-' }}
                                            @break

                                            @case('numero_operacion_bancaria')
                                                {{ optional($orden->pagos)->num_operacion ?? '-' }}
                                            @break                                            

                                            @case('monto')
                                                S/{{ optional($orden->ordenServicio)->monto, 2 }}
                                            @break

                                            @case('montoos')
                                                S/{{ optional($orden->ordenServicio)->monto, 2 }}
                                            @break

                                            @case('codigo_conformidad')
                                                {{ optional($orden->ordenServicio)->codigo_conformidad ?? '-' }}
                                            @break
                                            

                                            @case('banco')
                                                {{ optional(optional($orden->pagos)->bancos)->descripcion ?? '-' }}
                                            @break

                                            @case('total')
                                                {{ optional($orden->ventas)->total 
                                                ? 'S/ ' . number_format($orden->ventas->total, 2) 
                                                : '-' }}
                                            @break

                                            @case('estado')
                                                @if(!$orden->estadoCatalogo)
                                                    <span class="badge bg-secondary">
                                                        Sin estado
                                                    </span>
                                                @else
                                                    <span class="badge 
                                                    bg-{{ $orden->estadoCatalogo->color }} 
                                                    text-{{ $orden->estadoCatalogo->color_texto }} 
                                                    border border-{{ $orden->estadoCatalogo->color }} 
                                                    fw-semibold">
                                                    {{ $orden->estadoCatalogo->nombre }}
                                                    </span>
                                                @endif

                                            @break

                                            @case('monto_cotizado')
                                            @if($orden->cotizacion)
                                            S/ {{ number_format($orden->cotizacion->total, 2) }}        
                                            @else
                                            -
                                            @endif
                                            @break

                                            @case('observaciones')
                                                {{ $orden->observaciones ?? '-' }}
                                            @break

                                            @case('parte_firmado_pdf')
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->archivo_pdf)
                                                    <a href="{{ asset('uploads/ordenes_trabajo/'.$orden->archivo_pdf) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-danger">
                                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif 
                                            </div>
                                            @break

                                            @case('visor_factura')
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->archivo_factura)
                                                    <a href="{{ asset('uploads/ordenes_trabajo/facturacion/' . $orden->archivo_factura) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-success">
                                                        <i class="bi bi-coin"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif 
                                            </div>
                                            @break

                                            @case('archivo_nota') <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->notaCredito && $orden->notaCredito->archivo_nota) 
                                                <a href="{{ asset('storage/' . $orden->notaCredito->archivo_nota) }}" target="_blank" title="Ver Nota de Crédito"> 
                                                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size:18px;"> </i> </a> 
                                                    @else 
                                                    - 
                                                    @endif </div>
                                            @break

                                            @case('valida_nota') <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->notaCredito && $orden->notaCredito->valida_nota) 
                                                <a href="{{ asset('storage/' . $orden->notaCredito->valida_nota) }}" target="_blank" 
                                                title="Ver validación de Nota de Crédito">
                                                <i class="bi bi-shield-check text-success" style="font-size:18px;"></i>
                                                </a> 
                                                    @else 
                                                    - 
                                                    @endif </div>
                                            @break

                                            @case('detalles_nota') <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->notaCredito && $orden->notaCredito->detalles_nota) 
                                                <a href="{{ asset('storage/' . $orden->notaCredito->detalles_nota) }}" target="_blank" 
                                                    title="Ver detalle de Nota de Crédito">
                                                    <i class="bi bi-file-earmark-text text-primary" style="font-size:18px;"></i>
                                                </a> 
                                                    @else 
                                                    - 
                                                    @endif </div>
                                            @break
                                            

                                            @case('visor_deposito')
                                                <div class="d-flex justify-content-center align-items-center">
                                                    @if($orden->pagos?->voucher_pago)
                                                    <a href="{{ asset('uploads/ordenes_trabajo/pagos/' . $orden->pagos->voucher_pago) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="bi bi-postcard-fill"></i>
                                                    </a>
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            @break



                                            @case('visor_subida_portal')
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->proceso_pago)
                                                    <a href="{{ asset('uploads/ordenes_trabajo/procesopago/' . $orden->proceso_pago) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-warning">
                                                        <i class="bi bi-images"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            <div>
                                            @break

                                            @case('visor_sustento')
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->archivo_sustento)
                                                    <a href="{{ asset('uploads/ordenes_trabajo/facturacion/' . $orden->archivo_sustento) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-warning">
                                                        <i class="bi bi-person-vcard-fill"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            @break

                                            @case('pdf_ordenservicio')
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if(optional($orden->ordenServicio)->archivo_pdf)                                               
                                                    <a href="{{ asset('uploads/ordenes_servicio/' . $orden->ordenServicio->archivo_pdf) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-success">
                                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            @break

                                            @case('fecha_atencion')
                                                {{ $orden->fecha_atencion ?? '-' }}
                                            @break

                                            @case('doc_cargada')
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if($orden->captura_info)
                                                    <a href="{{ asset('uploads/ot_documentacion/'.$orden->captura_info) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bi bi-images"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif 
                                            </div>
                                            @break

                                            @case('ver_pdf')

                                            @php
                                            $cotizacion = $orden->cotizacion;
                                            @endphp

                                            @if(
                                            ($orden->estado == 3) && $cotizacion)

                                            <a href="{{ route('cotizaciones.imprimir', $cotizacion->id) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                            </a>

                                            @else
        -
                                            @endif

                                            @break

                                            @case('informe_pdf')
                                            <div class="d-flex justify-content-center align-items-center">

                                            @php
                                            $informe = $orden->informes->first();
                                            @endphp

                                            @if(
                                            ($orden->estado == 2 || $orden->estado == 5) && $informe)

                                            <a href="{{ route('informes.imprimir', $informe->id) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                            </a>

                                            @else
                                                -
                                            @endif
                                            </div>

                                            @break

                                            @case('tiempo')
                                                <span class="text-muted">
                                                    {{ $orden->tiempo_transcurrido ?? '-' }}
                                                </span>
                                            @break

                                            @case('tiempo_creacion')
                                                <span class="text-muted">
                                                    {{ $orden->tiempo_desde_creacion }}
                                                </span>
                                            @break

                                        @endswitch

                                    </td>
                                @endforeach                              

                            </tr>                       
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#tablaEstado').DataTable({
        ordering: true,
        responsive: true,
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});
</script>
@endpush