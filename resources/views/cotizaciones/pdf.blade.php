<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización N° {{ $cotizacion->id }}</title>

    @include('cotizaciones.partials.pdf-styles')
</head>

<body>

{{-- ==========================
     ENCABEZADO
========================== --}}
<table class="header-table">
    <tr>
        <td class="header-left">
            <img src="{{ public_path('assets/branding/acotec-logo.png') }}" class="logo" alt="Acotec E.I.R.L.">
            <div class="company-name">AUTOMATIZACIÓN Y CONTROL EN LA TECNOLOGÍA E.I.R.L.</div>
            <div class="company-meta">
                <strong>RUC:</strong> 20601096391 &nbsp;|&nbsp; <strong>E-mail:</strong> areacomercial@acotec.pe<br>
                Jr. Amazonas Mz. 69 Lt. 31 Las Palmeras - Pacasmayo, La Libertad - Perú
            </div>
        </td>
        <td class="header-right">
            <table class="doc-box">
                <tr>
                    <td class="doc-box-ruc">R.U.C. N° 20601096391</td>
                </tr>
                <tr>
                    <td class="doc-box-title">COTIZACIÓN</td>
                </tr>
                <tr>
                    <td class="doc-box-body">
                        <div class="doc-number">N° ACO1 - {{ str_pad($cotizacion->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="doc-date">FECHA: {{ \Carbon\Carbon::parse($cotizacion->fecha ?? now())->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="header-divider"></div>

{{-- ==========================
     INFORMACIÓN DEL CLIENTE Y SERVICIO
========================== --}}
<table class="client-section">
    <tr>
        <td class="client-label">Cliente</td>
        <td class="client-val" style="width: 42%;"><strong>{{ $cotizacion->cliente->razon_social ?? '---' }}</strong></td>
        <td class="client-label">N° O.T.</td>
        <td class="client-val"><strong>{{ $cotizacion->ordenTrabajo->codigo_ot_cliente ?? '-' }}</strong></td>
    </tr>
    <tr>
        <td class="client-label">R.U.C. / Doc</td>
        <td class="client-val">{{ $cotizacion->cliente->numero_documento ?? '-' }}</td>
        <td class="client-label">Sede / Planta</td>
        <td class="client-val">{{ $cotizacion->ordenTrabajo->sedes->nombre ?? '-' }}</td>
    </tr>
    <tr>
        <td class="client-label">Dirección</td>
        <td class="client-val">{{ $cotizacion->cliente->direccion ?? '-' }}</td>
        <td class="client-label">Solicitante</td>
        <td class="client-val">{{ $cotizacion->ordenTrabajo->responsables->nombre ?? '-' }}</td>
    </tr>
    <tr>
        <td class="client-label">Descripción</td>
        <td class="client-val" colspan="3">{{ $cotizacion->ordenTrabajo->descripcion ?? '-' }}</td>
    </tr>
</table>

{{-- ==========================
     MATERIALES
========================== --}}
@if($cotizacion->productos->count())
<div class="bloque">
    <div class="section-header">1. Materiales y Repuestos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">Ítem</th>
                <th>Descripción</th>
                <th style="width: 50px;" class="text-center">Unidad</th>
                <th style="width: 45px;" class="text-center">Cant.</th>
                <th style="width: 75px;" class="text-right">P. Unit.</th>
                <th style="width: 80px;" class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->productos as $i => $item)
            <tr>
                <td class="text-center">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $item->producto->nombre }}</td>
                <td class="text-center">{{ $item->producto->unidad_codigo }}</td>
                <td class="text-center">{{ $item->cantidad }}</td>
                <td class="text-right">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="5" style="text-align: right; padding-right: 8px;">Subtotal Materiales</td>
                <td class="text-right">S/ {{ number_format($totalProductos, 2) }}</td>
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
    <div class="section-header">2. Servicios Técnicos Especializados</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">Ítem</th>
                <th>Descripción</th>
                <th style="width: 50px;" class="text-center">Unidad</th>
                <th style="width: 45px;" class="text-center">Cant.</th>
                <th style="width: 75px;" class="text-right">P. Unit.</th>
                <th style="width: 80px;" class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->servicios as $i => $item)
            <tr>
                <td class="text-center">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $item->servicio->descripcion }}</td>
                <td class="text-center">{{ $item->servicio->unidad_codigo }}</td>
                <td class="text-center">{{ $item->cantidad }}</td>
                <td class="text-right">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="5" style="text-align: right; padding-right: 8px;">Subtotal Servicios</td>
                <td class="text-right">S/ {{ number_format($totalServicios, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

{{-- ==========================
     TRANSPORTE DE HERRAMIENTAS
========================== --}}
@if($cotizacion->transportes->count())
<div class="bloque transporte-bloque">
    <div class="section-header">3. Transporte de Herramientas y Equipos</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">Ítem</th>
                <th>Descripción</th>
                <th style="width: 50px;" class="text-center">Unidad</th>
                <th style="width: 45px;" class="text-center">Cant.</th>
                <th style="width: 75px;" class="text-right">P. Unit.</th>
                <th style="width: 80px;" class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->transportes as $i => $item)
            <tr>
                <td class="text-center">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>Transporte de Herramientas</td>
                <td class="text-center">{{ $item->transporte->unidad_codigo }}</td>
                <td class="text-center">{{ $item->cantidad }}</td>
                <td class="text-right">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="5" style="text-align: right; padding-right: 8px;">Subtotal Transporte de Herramientas</td>
                <td class="text-right">S/ {{ number_format($totalTransporte, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

{{-- ==========================
     TRANSPORTE DE PERSONAL
========================== --}}
@if($cotizacion->tpersonal->count())
<div class="bloque transporte-bloque">
    <div class="section-header">4. Transporte y Movilización de Personal</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">Ítem</th>
                <th>Descripción</th>
                <th style="width: 50px;" class="text-center">Unidad</th>
                <th style="width: 45px;" class="text-center">Cant.</th>
                <th style="width: 75px;" class="text-right">P. Unit.</th>
                <th style="width: 80px;" class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->tpersonal as $i => $item)
            <tr>
                <td class="text-center">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>Transporte de Personal</td>
                <td class="text-center">{{ $item->tpersonal->unidad_codigo }}</td>
                <td class="text-center">{{ $item->cantidad }}</td>
                <td class="text-right">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="5" style="text-align: right; padding-right: 8px;">Subtotal Transporte de Personal</td>
                <td class="text-right">S/ {{ number_format($totalTPersonal, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endif

{{-- ==========================
     RESUMEN FISCAL
========================== --}}
<div class="bloque resumen-fiscal-bloque">
    <table class="financial-table">
        <tr>
            <td style="color: #4b5563;">Op. Gravada:</td>
            <td class="text-right" style="color: #111827; font-weight: bold;">S/ {{ number_format($opGravada, 2) }}</td>
        </tr>
        <tr>
            <td style="color: #4b5563;">I.G.V. (18%):</td>
            <td class="text-right" style="color: #111827; font-weight: bold;">S/ {{ number_format($igv, 2) }}</td>
        </tr>  
        <tr class="total-row">
            <td>TOTAL GENERAL:</td>
            <td class="text-right">S/ {{ number_format($totalGeneral, 2) }}</td>
        </tr>
    </table>
</div>

{{-- ==========================
     CONDICIONES COMERCIALES
========================== --}}
@if($cotizacion->condiciones->count())
<table class="terms-box">
    <tr>
        <td class="terms-header">Condiciones Comerciales</td>
    </tr>
    <tr>
        <td class="terms-body">
            @foreach($cotizacion->condiciones as $i => $item)
            <p><strong>{{ $i + 1 }}.-</strong> {{ $item->condiciones->descripcion }}</p>
            @endforeach
        </td>
    </tr>
</table>
@endif

{{-- ==========================
     PIE
========================== --}}
<div class="footer">
    <p>ACOTEC E.I.R.L. &bull; Automatización y Control en la Tecnología &bull; RUC: 20601096391</p>
    <p>Jr. Amazonas Mz. 69 Lt. 31 Las Palmeras - Pacasmayo - La Libertad - Perú &bull; www.acotec.pe</p>
</div>

</body>
</html>
