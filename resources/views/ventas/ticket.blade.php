<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ $venta->serie }}-{{ $venta->correlativo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 0; padding: 0; }
        .ticket { width: 240px; margin: 0 auto; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .company-logo { display: block; width: 210px; height: auto; margin: 0 auto 7px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .totales td { padding: 3px 0; }
    </style>
</head>
<body>
@php
    $logoPath = public_path('assets/branding/acotec-logo.png');
    $logoDataUri = is_file($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp
<div class="ticket">

    {{-- ENCABEZADO --}}
    <div class="center">
        @if($logoDataUri)
            <img class="company-logo" src="{{ $logoDataUri }}" alt="{{ $empresa->razon_social }}">
        @else
            <h3 class="bold">{{ $empresa->razon_social }}</h3>
        @endif
        <p>{{ $empresa->direccion }}</p>
        <p>RUC: {{ $empresa->ruc }}</p>
        <p class="bold">
            {{ $venta->comprobante_tipo_codigo == '01' ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA' }}
        </p>
        <p class="bold">
            {{ $venta->serie }}-{{ str_pad($venta->correlativo,8,'0',STR_PAD_LEFT) }}
        </p>
    </div>

    <div class="line"></div>

    {{-- CLIENTE --}}
    <p><strong>Cliente:</strong> {{ $venta->cliente->razon_social ?? '-' }}</p>
    <p><strong>Documento:</strong> {{ $venta->cliente->documentoTipo->descripcion }} {{ $venta->cliente->numero_documento }}</p>
    <p><strong>Dirección:</strong> {{ $venta->cliente->direccion ?? '-' }}</p>
    <p><strong>Fecha:</strong> 
        {{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}
    </p>

    <div class="line"></div>

    {{-- DETALLE --}}
    <table>
        <tr class="bold">
            <td style="width: 45%;">Descripción</td>
            <td style="width: 15%; text-align: center;">Cant.</td>
            <td style="width: 20%; text-align: right;">P. Unit.</td>
            <td style="width: 20%; text-align: right;">Total</td>
        </tr>

        <tr>
            <td colspan="4" style="border-top: 1px solid #000;"></td>
        </tr>

        {{-- 🔹 PRODUCTOS --}}
        @foreach($productos ?? [] as $detalle)
        <tr>
            <td>{{ $detalle->producto->nombre ?? 'Producto' }}</td>
            <td style="text-align: center;">{{ $detalle->cantidad }}</td>
            <td style="width: 20%; text-align: right;">{{ number_format($detalle->precio_unitario,2) }}</td>
            <td style="text-align: right;">{{ number_format($detalle->subtotal,2) }}</td>
        </tr>
        @endforeach

        {{-- 🔹 SERVICIOS --}}
        @foreach($servicios ?? [] as $detalle)
        <tr>
            <td>{{ $detalle->servicio->descripcion ?? 'Servicio' }}</td>
            <td style="text-align: center;">{{ $detalle->cantidad }}</td>
            <td style="width: 20%; text-align: right;">{{ number_format($detalle->precio_unitario,2) }}</td>
            <td style="text-align: right;">{{ number_format($detalle->subtotal,2) }}</td>
        </tr>
        @endforeach

        {{-- 🔹 TRANSPORTE --}}
        @foreach($transportes ?? [] as $detalle)
        <tr>
            <td>{{ $detalle->transporte->name ?? 'Transporte' }}</td>
            <td style="text-align: center;">{{ $detalle->cantidad }}</td>
            <td style="width: 20%; text-align: right;">{{ number_format($detalle->precio_unitario,2) }}</td>
            <td style="text-align: right;">{{ number_format($detalle->subtotal,2) }}</td>
        </tr>
        @endforeach

        {{-- 🔹 TRANSPORTE PERSONAL --}}
        @foreach($tpersonal ?? [] as $detalle)
        <tr>
            <td>{{ $detalle->tpersonal->name ?? 'T. Personal' }}</td>
            <td style="text-align: center;">{{ $detalle->cantidad }}</td>
            <td style="width: 20%; text-align: right;">{{ number_format($detalle->precio_unitario,2) }}</td>
            <td style="text-align: right;">{{ number_format($detalle->subtotal,2) }}</td>
        </tr>
        @endforeach

        <tr>
            <td colspan="4" style="border-top: 1px solid #000;"></td>
        </tr>
    </table>

    {{-- TOTALES --}}
    <table class="totales">
        <tr>
            <td>OP. GRAVADAS:</td>
            <td style="text-align: right;">S/ {{ number_format($opGravada ?? 0,2) }}</td>
        </tr>
        <tr>
            <td>OP. EXONERADAS:</td>
            <td style="text-align: right;">S/ 0.00</td>
        </tr>
        <tr>
            <td>OP. INAFECTAS:</td>
            <td style="text-align: right;">S/ 0.00</td>
        </tr>
        <tr>
            <td>IGV (18%):</td>
            <td style="text-align: right;">S/ {{ number_format($igv ?? 0,2) }}</td>
        </tr>
        <tr class="bold">
            <td>TOTAL:</td>
            <td style="text-align: right;">S/ {{ number_format($totalGeneral ?? $venta->total ?? 0,2) }}</td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- QR --}}
    @if(!empty($qrDataUri))
    <div style="text-align:center; margin-top:10px;">
        <img src="{{ $qrDataUri }}" width="120">
    </div>
    @endif

    <div class="center">
        <p>¡Gracias por su compra!</p>
    </div>

</div>
</body>
</html>
