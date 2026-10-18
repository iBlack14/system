<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe N° INF-{{ str_pad($informe->id, 4, '0', STR_PAD_LEFT) }}</title>

    <style>
        @page {
            margin: 22px 28px 32px;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 8.8px;
            color: #1e293b;
            line-height: 1.45;
            margin: 0;
            padding-bottom: 25px;
        }

        p { margin: 1px 0; }

        /* Header */
        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .header-table td {
            padding: 0;
            vertical-align: top;
            border: 0;
            background: #ffffff !important;
        }

        .header-left { width: 58%; }
        .header-right { width: 42%; text-align: right; }

        .logo {
            width: 170px;
            max-height: 46px;
            object-fit: contain;
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 9.5px;
            font-weight: bold;
            color: #111827;
            letter-spacing: .2px;
            margin-bottom: 2px;
        }

        .company-meta {
            color: #4b5563;
            font-size: 7.8px;
            line-height: 1.35;
        }
        .company-meta strong { color: #111827; }

        /* Official Document Box */
        .doc-box {
            width: 100%;
            border: 1px solid #1e293b;
            border-collapse: collapse;
            margin-left: auto;
            max-width: 225px;
        }

        .doc-box-ruc {
            background: #ffffff;
            text-align: center;
            font-size: 8.8px;
            font-weight: bold;
            color: #111827;
            padding: 4px 6px;
            border-bottom: 1px solid #1e293b;
            letter-spacing: .4px;
        }

        .doc-box-title {
            background: #1e293b;
            color: #ffffff;
            text-align: center;
            font-size: 8.5px;
            font-weight: bold;
            padding: 4px 6px;
            letter-spacing: .5px;
        }

        .doc-box-body {
            background: #ffffff;
            text-align: center;
            padding: 5px 6px;
        }

        .doc-type {
            font-size: 8px;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .doc-number {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 2px;
        }

        .doc-date {
            font-size: 8px;
            color: #4b5563;
            font-weight: 600;
        }

        .header-divider {
            height: 1px;
            background: #cbd5e1;
            margin: 8px 0 10px;
        }

        /* Ficha Técnica / Datos Generales */
        .client-section {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .client-section td {
            padding: 4px 7px;
            font-size: 8.5px;
            vertical-align: top;
            border: 1px solid #e2e8f0;
        }

        .client-label {
            width: 90px;
            font-weight: bold;
            color: #374151;
            font-size: 7.8px;
            text-transform: uppercase;
        }

        .client-val {
            color: #111827;
        }

        .notice-row {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 4px 7px;
            margin-bottom: 10px;
            font-size: 7.8px;
            color: #475569;
            page-break-inside: avoid;
        }

        /* Aspectos / Secciones Técnicas */
        .aspecto-bloque {
            margin-bottom: 12px;
        }

        .aspecto-header {
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .section-header {
            font-size: 8.5px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: .3px;
            padding: 2px 0;
            margin-top: 10px;
            margin-bottom: 5px;
            border-bottom: 1px solid #94a3b8;
        }

        .item {
            margin-left: 10px;
            margin-bottom: 5px;
            line-height: 1.45;
            font-size: 8.5px;
            color: #1f2937;
        }

        /* Registro Fotográfico */
        .photo-group {
            margin: 8px 0;
            page-break-inside: avoid;
        }

        .photo-group-title {
            font-size: 8px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .2px;
        }

        .photo-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin: 0 auto;
        }

        .photo-card {
            width: 50%;
            text-align: center;
            padding: 5px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            vertical-align: top;
        }

        .photo-card img {
            max-width: 100%;
            max-height: 240px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 28px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 7.5px;
            color: #64748b;
            padding-top: 4px;
        }
    </style>
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
                    <td class="doc-box-title">INFORME TÉCNICO DE SERVICIO</td>
                </tr>
                <tr>
                    <td class="doc-box-body">
                        <div class="doc-type">TIPO: {{ strtoupper($informe->informe_tipo ?? 'TÉCNICO') }}</div>
                        <div class="doc-number">INF-{{ str_pad($informe->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <div class="doc-date">FECHA: {{ \Carbon\Carbon::parse($informe->fecha_creacion ?? now())->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="header-divider"></div>

{{-- ==========================
     FICHA TÉCNICA DEL SERVICIO
========================== --}}
<table class="client-section">
    <tr>
        <td class="client-label">N° O.T.</td>
        <td class="client-val" style="width: 42%;"><strong>{{ $informe->ordenTrabajo->codigo_ot_cliente ?? '-' }}</strong></td>
        <td class="client-label">Solicitante</td>
        <td class="client-val">{{ $informe->ordenTrabajo->responsables->nombre ?? '-' }}</td>
    </tr>
    <tr>
        <td class="client-label">Cliente</td>
        <td class="client-val"><strong>{{ $informe->ordenTrabajo->cliente->razon_social ?? '-' }}</strong></td>
        <td class="client-label">Elaborado por</td>
        <td class="client-val"><strong>{{ $informe->user->name ?? '-' }}</strong></td>
    </tr>
    <tr>
        <td class="client-label">Sede / Planta</td>
        <td class="client-val">{{ $informe->ordenTrabajo->sedes->nombre ?? '-' }}</td>
        <td class="client-label">Fecha Emisión</td>
        <td class="client-val">{{ \Carbon\Carbon::parse($informe->fecha_creacion ?? now())->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td class="client-label">Descripción</td>
        <td class="client-val" colspan="3">{{ $informe->ordenTrabajo->descripcion ?? '-' }}</td>
    </tr>
</table>

<div class="notice-row">
    <strong>Observaciones:</strong> El plazo máximo para formular observaciones al presente informe técnico es de 20 días calendario posteriores a su entrega formal.
</div>

{{-- ==========================
     ASPECTOS NUMERADOS Y REGISTRO FOTOGRÁFICO
========================== --}}
@php $numeroAspecto = 1; @endphp
@foreach($informe->aspectos as $aspecto)

@php
    $items = $aspecto->items;
    $grupos = $aspecto->imagenes
        ->sortBy('orden_grupo')
        ->groupBy('grupo_id');
@endphp

<div class="aspecto-bloque">

    {{-- HEADER (TÍTULO + PRIMER ITEM SI EXISTE) --}}
    <div class="aspecto-header">
        <div class="section-header">
            {{ $numeroAspecto }}. {{ $aspecto->aspectoCatalogo->nombre }}
        </div>

        @if($items->count())
            <div class="item">
                <strong>{{ $numeroAspecto }}.1.</strong> {!! nl2br(e($items->first()->contenido)) !!}
            </div>
        @endif
    </div>

    {{-- RESTO DE ITEMS --}}
    @php $numeroItem = 2; @endphp
    @foreach($items->slice(1) as $item)
        <div class="item">
            <strong>{{ $numeroAspecto }}.{{ $numeroItem }}.</strong> {!! nl2br(e($item->contenido)) !!}
        </div>
        @php $numeroItem++; @endphp
    @endforeach

    {{-- IMÁGENES / REGISTRO FOTOGRÁFICO --}}
    @foreach($grupos as $grupoId => $imagenes)

        @php
            $descripcion = $imagenes->first()->grupo_descripcion;
            $imagenesOrdenadas = $imagenes->sortBy('orden')->values();
        @endphp

        <div class="photo-group">
            @if($descripcion)
                <div class="photo-group-title">
                    {{ $descripcion }}
                </div>
            @endif

            <table class="photo-table">
                @php $chunks = $imagenesOrdenadas->chunk(2); @endphp

                @foreach($chunks as $row)
                    <tr>
                        @foreach($row as $img)
                            <td class="photo-card">
                                <img src="{{ storage_path('app/public/'.$img->ruta_imagen) }}" alt="Evidencia fotográfica">
                            </td>
                        @endforeach

                        @if(count($row) < 2)
                            <td style="width:50%; border:0; background:transparent;"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>

    @endforeach

</div>

@php $numeroAspecto++; @endphp
@endforeach

{{-- ==========================
     PIE DE PÁGINA FIJO
========================== --}}
<div class="footer">
    <p>Informe generado por <strong>ACOTEC E.I.R.L.</strong> &bull; Automatización y Control en la Tecnología &bull; RUC: 20601096391</p>
    <p>Jr. Amazonas Mz. 69 Lt. 31 Las Palmeras - Pacasmayo - La Libertad - Perú</p>
</div>

{{-- ==========================
     SCRIPT DOMPDF PARA NÚMEROS DE PÁGINA
========================== --}}
<script type="text/php">
if (isset($pdf)) { 
    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
    $size = 7.5;
    $text = "Página {PAGE_NUM} de {PAGE_COUNT}  |  Informe Técnico INF-" . str_pad($informe->id, 4, '0', STR_PAD_LEFT);
    
    $x = ($pdf->get_width() - $fontMetrics->get_text_width($text, $font, $size)) / 2;
    $y = $pdf->get_height() - 18;
    
    $pdf->page_text($x, $y, $text, $font, $size, array(0.4, 0.45, 0.5));
}
</script>

</body>
</html>
