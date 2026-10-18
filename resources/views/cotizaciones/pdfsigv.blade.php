<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización N° {{ $cotizacion->id }}</title>

 <style>

    /* ==========================
       CONFIGURACIÓN GENERAL
    ========================== */

    @page {
        margin: 20px 25px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #333;
    }

    p {
        margin: 3px 0;
    }

    /* ==========================
       CONTROL DE SALTOS DE PÁGINA
    ========================== */

    .bloque {
        margin-bottom: 15px;
    }

    .section-title {
        background-color: #e9ecef;
        padding: 6px 10px;
        color: #131211;
        font-weight: bold;
        margin-top: 12px;
        page-break-after: avoid;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        page-break-inside: auto;
    }

    thead {
        display: table-header-group;
    }

    tr {
        page-break-inside: auto;
        page-break-after: auto;
    }

    td, th {
        border: 1px solid #ddd;
        padding: 6px;
    }

    th {
        background-color: #ff7300;
        color: #fff;
        text-align: left;
    }

    tr:nth-child(even) td {
        background-color: #f9f9f9;
    }

    .text-right {
        text-align: right;
        white-space: nowrap;
    }

    .total {
        font-weight: bold;
        background-color: #e2e6ea;
    }

    /* ==========================
       ENCABEZADO
    ========================== */

    .header-table {
        width: 100%;
        border-bottom: 3px solid #ff7300;
        margin-bottom: 20px;
        page-break-inside: avoid;
    }

    .header-left {
        vertical-align: top;
    }

    .header-right {
        text-align: right;
        vertical-align: top;
    }

    .logo {
        width: 160px;
        height: auto;
    }

    h2 {
        margin: 0;
        color: #ff7300;
    }

    /* ==========================
       INFO GENERAL
    ========================== */

    .info {
        background: #f8f9fa;
        border-left: 4px solid #ff7300;
        padding: 10px;
        margin-bottom: 20px;
        page-break-inside: avoid;
    }

    /* ==========================
       TRANSPORTES (NO ROMPER)
    ========================== */

    .transporte-bloque {
        page-break-inside: avoid; /* evita que se rompa */
        margin-bottom: 15px;
    }

    .transporte-bloque table {
        page-break-inside: avoid; /* tabla completa sin romper */
    }

    .transporte-bloque .total {
        page-break-inside: avoid; /* total junto con tabla */
    }

    

    /* ==========================
       RESUMEN FISCAL
    ========================== */

    .resumen-fiscal {
        width: 40%;
        margin-left: auto;
        margin-top: 20px;
        border-collapse: collapse;
    }

    .resumen-fiscal td {
        border: none;
        padding: 5px 8px;
        font-size: 11px;
        font-weight: bold;
    }

    .resumen-fiscal .importe-total td {
        background: #ff7300;
        color: #fff;
        border-top: 2px solid #e76700;
    }

    /* ==========================
   CONDICIONES COMERCIALES
========================== */
.condiciones-bloque {
    page-break-inside: avoid; /* evita que el bloque se rompa */
    margin-bottom: 15px;
}

.condiciones-bloque table {
    width: 100%;
    border-collapse: collapse;
    page-break-inside: avoid; /* evita que la tabla se divida */
}

.condiciones-bloque tr {
    page-break-inside: avoid; /* evita que las filas se dividan */
}

/* ==========================
   AGRUPAR ÚLTIMO TRANSPORTE + RESUMEN
========================== */
.bloque-final {
    page-break-inside: avoid;
}

.bloque-final table,
.bloque-final .resumen-fiscal {
    page-break-inside: avoid;
}

    /* ==========================
       FOOTER
    ========================== */

    .footer {
        border-top: 2px solid #ff7300;
        text-align: center;
        padding-top: 10px;
        font-size: 11px;
        color: #666;
        margin-top: 30px;
        page-break-inside: avoid;
    }

</style>   
@include('cotizaciones.partials.pdf-styles')
</head>

<body>

{{-- ==========================
     ENCABEZADO
========================== --}}
<table class="header-table">
    <tr>
        <td class="header-left">
            <h2>COTIZACIÓN N° ACO1 - {{ $cotizacion->id }}</h2>
            <p><strong>ACOTEC E.I.R.L.</strong></p>
            <p><strong>RUC: 20601096391</strong></p>
            <p>mrojas@acotec.pe , acoteceirl@gmail.com</p>
            <p>areacomercial@acotec.pe</p>
        </td>
        <td class="header-right">
            <img src="{{ public_path('assets/branding/acotec-logo.png') }}" class="logo" alt="Acotec E.I.R.L.">
        </td>
    </tr>
</table>

{{-- ==========================
     DATOS GENERALES
========================== --}}
<div class="info">
    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($cotizacion->fecha ?? now())->format('d/m/Y') }}</p>
    <p><strong>Orden de Trabajo:</strong> {{ $cotizacion->ordenTrabajo->codigo_ot_cliente ?? '-' }}</p>
    <p><strong>Cliente:</strong> {{ $cotizacion->cliente->razon_social ?? '---' }}</p>
    <p><strong>Sede / Oficina:</strong> {{ $cotizacion->ordenTrabajo->sedes->nombre ?? '-' }}</p>    
    <p><strong>Solicitante:</strong> {{ $cotizacion->ordenTrabajo->responsables->nombre ?? '-' }}</p>
    <p><strong>Descripción de Trabajo:</strong> {{ $cotizacion->ordenTrabajo->descripcion ?? '-' }}</p>
    <p><strong>Proveedor:</strong> AUTOMATIZACIÓN Y CONTROL EN LA TECNOLOGÍA E.I.R.L.</p>    
</div>

{{-- ==========================
     MATERIALES
========================== --}}
@if($cotizacion->productos->count())
<div class="bloque">
<div class="section-title">Materiales</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cotizacion->productos as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->producto->nombre }}</td>
            <td>{{ $item->producto->unidad_codigo }}</td>
            <td>{{ $item->cantidad }}</td>
            <td class="text-right">S/ {{ number_format($item->precio_unitario / 1.18, 2) }}</td>            
            <td class="text-right">S/ {{ number_format($item->subtotal / 1.18, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="5">Total Materiales</td>
            <td class="text-right">S/ {{ number_format($totalProductos / 1.18, 2) }}</td>
        </tr>
    </tbody>
</table>
</div>
@endif

{{-- ==========================
     SERVICIOS
========================== --}}
@if($cotizacion->servicios->count())
<div class="bloque">
<div class="section-title">Servicios</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cotizacion->servicios as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->servicio->descripcion }}</td>
            <td>{{ $item->servicio->unidad_codigo }}</td>
            <td>{{ $item->cantidad }}</td>            
            <td class="text-right">S/ {{ number_format($item->precio_unitario / 1.18, 2) }}</td>
            <td class="text-right">S/ {{ number_format($item->subtotal / 1.18, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="5">Total Servicios</td>
            <td class="text-right">S/ {{ number_format($totalServicios / 1.18, 2) }}</td>
        </tr>
    </tbody>
</table>
</div>
@endif

@php
    $hayTransporteHerramientas = $cotizacion->transportes->count();
    $hayTransportePersonal = $cotizacion->tpersonal->count();
@endphp

{{-- ==========================
     TRANSPORTE DE HERRAMIENTAS
========================== --}}
@if($hayTransporteHerramientas && !$hayTransportePersonal)
<div class="bloque transporte-bloque bloque-final">
@else
<div class="bloque transporte-bloque">
@endif

@if($hayTransporteHerramientas)
<div class="section-title">Transporte de Herramientas</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cotizacion->transportes as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>Transporte de Herramientas</td>
            <td>{{ $item->transporte->unidad_codigo }}</td>
            <td>{{ $item->cantidad }}</td>
            <td class="text-right">S/ {{ number_format($item->precio_unitario / 1.18, 2) }}</td>
            <td class="text-right">S/ {{ number_format($item->subtotal / 1.18, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="5">Total Transporte de Herramientas</td>
            <td class="text-right">S/ {{ number_format($totalTransporte / 1.18, 2) }}</td>
        </tr>
    </tbody>
</table>
@endif
</div>


{{-- ==========================
     TRANSPORTE PERSONAL
========================== --}}
@if($hayTransportePersonal)
<div class="bloque transporte-bloque bloque-final">
<div class="section-title">Transporte de Personal</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cotizacion->tpersonal as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>Transporte de Personal</td>
            <td>{{ $item->tpersonal->unidad_codigo }}</td>
            <td>{{ $item->cantidad }}</td>
            <td class="text-right">S/ {{ number_format($item->precio_unitario / 1.18, 2) }}</td>
            <td class="text-right">S/ {{ number_format($item->subtotal / 1.18, 2) }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="5">Total Transporte de Personal</td>
            <td class="text-right">S/ {{ number_format($totalTPersonal / 1.18, 2) }}</td>
        </tr>
    </tbody>
</table>
</div>
@endif


{{-- ==========================
     RESUMEN FISCAL
========================== --}}
@if(!$hayTransporteHerramientas && !$hayTransportePersonal)
<div class="bloque bloque-final">
@else
<div class="bloque bloque-final">
@endif

<table class="resumen-fiscal">
    <tr>
        <td><strong>OP. GRAVADA</strong></td>
        <td class="text-right"><strong>S/ {{ number_format($opGravada, 2) }}</strong></td>
    </tr>
    <tr>
        <td><strong>IGV (18%)</strong></td>
        <td class="text-right"><strong>S/ {{ number_format($igv, 2) }}</strong></td>
    </tr>
    <tr class="importe-total">
        <td>IMPORTE TOTAL</td>
        <td class="text-right">S/ {{ number_format($totalGeneral, 2) }}</td>
    </tr>
</table>
</div>

{{-- ==========================
     CONDICIONES COMERCIALES
========================== --}}
@if($cotizacion->condiciones->count())
<div class="bloque condiciones-bloque">

    <div class="section-title">
        CONDICIONES COMERCIALES
    </div>

    <table>
        <tbody>
            @foreach($cotizacion->condiciones as $i => $item)
            <tr>
                <td>
                    {{ $i + 1 }}.- 
                    {{ $item->condiciones->descripcion }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endif

{{-- ==========================
     PIE
========================== --}}
<div class="footer">
    <p>Gracias por confiar en <strong>ACOTEC E.I.R.L.</strong></p>
    <p>Jr. Amazonas Mz. 69 Lt 31 Las Palmeras - Pacasmayo - La Libertad - Perú</p>
</div>

</body>
</html>
