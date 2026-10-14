<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Informe N° {{ $informe->id }}</title>

<style>
    @page {
        margin: 30px 30px 30px 30px; /* espacio para header y footer */
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 14px;
        color: #333;
        margin: 0;
        padding-bottom: 50px; /* 🔥 clave */
    }
    

    /* ==========================
       ENCABEZADO
    ========================== */
    .header-table {
        width: 100%;
        border-bottom: 3px solid #ff7300;
        margin-bottom: 20px;
    }

    .header-left { vertical-align: top; }
    .header-right { text-align: right; vertical-align: top; }
    .logo { width: 160px; height: auto; }
    h2 { margin: 0; color: #ff7300; }

    /* ==========================
       INFO GENERAL
    ========================== */
    .info {
        background: #f8f9fa;
        border-left: 4px solid #ff7300;
        padding: 10px;
        margin-bottom: 20px;
    }
    p { margin: 3px 0; }

    /* ==========================
       SECCIONES
    ========================== */
    .section-title {
        background-color: #ff7300;
        padding: 6px 10px;
        color: #f8f9fa;
        font-weight: bold;
        margin-top: 15px;
    }
    .item { margin-left: 15px; padding-bottom: 6px; line-height: 1.6; }

    .imagen-container {
        width: 100%;
        max-width: 450px;
        margin: 10px auto;
        text-align: center;
    }

    .imagen-container img {
        max-width: 100%;
        max-height: 450px;
        width: auto;
        height: auto;
        object-fit: contain;
        border: 1px solid #999;
        padding: 5px;
    }

    .aspecto-bloque {
    margin-bottom: 10px;
    }

    .aspecto-header {
    page-break-inside: avoid; /* 🔥 clave */
    }

    .section-title {
    page-break-after: avoid; /* 🔥 evita que el título quede solo */
    }

    .item {
    line-height: 1.6;
    }

    /* ==========================
       PIE DE PÁGINA
    ========================== */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        border-top: 2px solid #ff7300;
        text-align: center;
        font-size: 11px;
        color: #666;
        padding-top: 5px;
    }

    .page-break-inside-avoid {
    page-break-inside: avoid; /* DomPDF respeta esto para evitar dividir el bloque */
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
            <h2>
                INFORME {{ strtoupper($informe->informe_tipo) }} 
                N° INF-{{ str_pad($informe->id, 4, '0', STR_PAD_LEFT) }}
            </h2>
            <p><strong>ACOTEC E.I.R.L.</strong></p>
            <p><strong>RUC: 20601096391</strong></p>
            <p>mrojas@acotec.pe , acoteceirl@gmail.com</p>
            <p>areacomercial@acotec.pe</p>
        </td>

        <td class="header-right">
            <img src="{{ public_path('images/logo.jpeg') }}" class="logo">
        </td>
    </tr>
</table>

{{-- ==========================
     DATOS GENERALES
========================== --}}
<div class="info">
    <p><strong>Fecha:</strong> 
        {{ \Carbon\Carbon::parse($informe->fecha_creacion ?? now())->format('d/m/Y') }}
    </p>
    <p><strong>Orden de Trabajo:</strong> 
        {{ $informe->ordenTrabajo->codigo_ot_cliente ?? '-' }}        
    </p>
    <p><strong>Cliente:</strong> 
        {{ $informe->ordenTrabajo->cliente->razon_social ?? '-' }}
    </p>
    <p><strong>Sede / Oficina:</strong>
        {{ $informe->ordenTrabajo->sedes->nombre ?? '-' }}</p>
    </p>
    <p><strong>Solicitante:</strong> 
        {{ $informe->ordenTrabajo->responsables->nombre ?? '-' }}</p>
    </p>
    <p><strong>Descripción de Trabajo:</strong> 
        {{ $informe->ordenTrabajo->descripcion ?? '-' }}
    </p>
    <p><strong>Elaborado por:</strong> 
        {{ $informe->user->name ?? '-' }}
    </p>
</div>
    <p><strong>Nota: Para presentar cualquier observación sobre el presente informe, el plazo máximo es de 20 días después de haber cargado 
        la documentación al portal.
    </strong> 
    </p>

{{-- ==========================
     ASPECTOS NUMERADOS
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

        <div class="section-title">
            {{ $numeroAspecto }}. {{ $aspecto->aspectoCatalogo->nombre }}
        </div>

        @if($items->count())
            <div class="item">
                {{ $numeroAspecto }}.1. {{ $items->first()->contenido }}
            </div>
        @endif

    </div>

    {{-- RESTO DE ITEMS --}}
    @php $numeroItem = 2; @endphp
    @foreach($items->slice(1) as $item)
        <div class="item">
            {{ $numeroAspecto }}.{{ $numeroItem }}. 
            {{ $item->contenido }}
        </div>
        @php $numeroItem++; @endphp
    @endforeach


    {{-- IMÁGENES --}}
    @foreach($grupos as $grupoId => $imagenes)

        @php
            $descripcion = $imagenes->first()->grupo_descripcion;
            $imagenesOrdenadas = $imagenes->sortBy('orden')->values();
        @endphp

        <div class="page-break-inside-avoid" style="margin-bottom:15px; text-align:center;">

            @if($descripcion)
                <div style="font-weight:bold; margin-bottom:8px;">
                    {{ $descripcion }}
                </div>
            @endif

            <table width="100%" style="border-collapse: separate; border-spacing: 10px 10px; margin: 0 auto;">
                
                @php $chunks = $imagenesOrdenadas->chunk(2); @endphp

                @foreach($chunks as $row)
                    <tr>
                        @foreach($row as $img)
                            <td style="width:50%; text-align:center; padding:5px; border:1px solid #ccc;">
                                <img src="{{ public_path('storage/'.$img->ruta_imagen) }}"
                                     style="max-width:100%; max-height:330px; object-fit:contain;">
                            </td>
                        @endforeach

                        @if(count($row) < 2)
                            <td style="width:50%;"></td>
                        @endif
                    </tr>
                @endforeach

            </table>

        </div>

    @endforeach

</div> {{-- FIN aspecto-bloque --}}

@php $numeroAspecto++; @endphp
@endforeach

{{-- ==========================
     PIE DE PÁGINA FIJO
========================== --}}
<div class="footer">
    <p>Informe generado por <strong>ACOTEC E.I.R.L.</strong></p>
    <p>Jr. Amazonas Mz. 69 Lt 31 Las Palmeras - Pacasmayo - La Libertad - Perú</p>
</div>

{{-- ==========================
     SCRIPT DOMPDF PARA NÚMERO DE PÁGINA
========================== --}}
<script type="text/php">
if (isset($pdf)) { 
    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
    $size = 10;
    $text = "Página {PAGE_NUM} de {PAGE_COUNT} | Informe generado por ACOTEC E.I.R.L.";
    
    // centrado horizontal
    $x = ($pdf->get_width() - $fontMetrics->get_text_width($text, $font, $size)) / 2;
    
    // 30pt arriba del borde inferior
    $y = $pdf->get_height() - 30;
    
    $pdf->page_text($x, $y, $text, $font, $size);
}
</script>

</body>
</html>