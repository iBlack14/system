<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Informe N° {{ $informe->id }}</title>

<style>
    @page { margin: 22px 28px 34px; }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        line-height: 1.35;
        color: #333;
        margin: 0;
        padding-bottom: 30px;
    }

    p { margin: 2px 0; }

    .header-table {
        width: 100%;
        border-collapse: collapse;
        border-bottom: 3px solid #ff7300;
        margin-bottom: 10px;
    }

    .header-table td { padding: 0 0 7px; }
    .header-left { vertical-align: top; }
    .header-right { text-align: right; vertical-align: top; }
    .logo { width: 150px; max-height: 65px; object-fit: contain; }
    h2 { margin: 0 0 4px; color: #ff7300; font-size: 17px; }

    .info {
        background: #f8f9fa;
        border-left: 4px solid #ff7300;
        padding: 7px 9px;
        margin-bottom: 7px;
        page-break-inside: avoid;
    }

    .nota {
        margin: 0 0 7px;
        font-size: 9.5px;
        line-height: 1.3;
    }

    .aspecto-bloque { margin-bottom: 7px; }
    .aspecto-header { page-break-inside: avoid; }

    .section-title {
        background-color: #ff7300;
        padding: 4px 8px;
        color: #fff;
        font-weight: bold;
        margin: 7px 0 4px;
        page-break-after: avoid;
    }

    .item {
        margin-left: 10px;
        padding-bottom: 3px;
        line-height: 1.35;
    }

    .photo-group {
        margin: 8px 0 10px;
        page-break-inside: auto;
    }

    .photo-group-title {
        background: #f4f5f7;
        border: none;
        color: #374151;
        font-weight: bold;
        font-size: 11px;
        line-height: 1.3;
        letter-spacing: .2px;
        padding: 5px 8px 10px;
        text-align: center;
        page-break-after: avoid;
    }

    .photo-row { page-break-inside: avoid; }

    .photo-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        border-spacing: 0;
        border: none !important;
        outline: none !important;
        margin: 0;
    }

    .photo-table tr,
    .photo-table td {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .photo-card {
        text-align: center;
        vertical-align: middle;
        padding: 3px 2px;
        border: none !important;
        outline: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .photo-card img {
        display: block;
        margin: 0 auto;
        padding: 2px;
        border: 1px solid #d2d6da !important;
        outline: none !important;
        background: #fff !important;
        box-shadow: none !important;
    }

    .photo-card-empty { border: 0; background: transparent; }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 25px;
        border-top: 1px solid #ff7300;
        text-align: center;
        font-size: 8px;
        color: #666;
        padding-top: 3px;
    }
</style>
</head>

<body>

<table class="header-table">
    <tr>
        <td class="header-left">
            <h2>
                INFORME {{ strtoupper($informe->informe_tipo ?? 'TÉCNICO') }}
                N° INF-{{ str_pad($informe->id, 4, '0', STR_PAD_LEFT) }}
            </h2>
            <p><strong>ACOTEC E.I.R.L.</strong></p>
            <p><strong>RUC: 20601096391</strong></p>
            <p>mrojas@acotec.pe, acoteceirl@gmail.com</p>
            <p>areacomercial@acotec.pe</p>
        </td>
        <td class="header-right">
            <img src="{{ public_path('images/logo.jpeg') }}" class="logo" alt="ACOTEC E.I.R.L.">
        </td>
    </tr>
</table>

<div class="info">
    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($informe->fecha_creacion ?? now())->format('d/m/Y') }}</p>
    <p><strong>Orden de Trabajo:</strong> {{ $informe->ordenTrabajo->codigo_ot_cliente ?? '-' }}</p>
    <p><strong>Cliente:</strong> {{ $informe->ordenTrabajo->cliente->razon_social ?? '-' }}</p>
    <p><strong>Sede / Oficina:</strong> {{ $informe->ordenTrabajo->sedes->nombre ?? '-' }}</p>
    <p><strong>Solicitante:</strong> {{ $informe->ordenTrabajo->responsables->nombre ?? '-' }}</p>
    <p><strong>Descripción de Trabajo:</strong> {{ $informe->ordenTrabajo->descripcion ?? '-' }}</p>
    <p><strong>Elaborado por:</strong> {{ $informe->user->name ?? '-' }}</p>
</div>

<p class="nota"><strong>Nota:</strong> Para presentar cualquier observación sobre el presente informe, el plazo máximo es de 20 días después de haber cargado la documentación al portal.</p>

@php $numeroAspecto = 1; @endphp
@foreach($informe->aspectos as $aspecto)
    @php
        $items = $aspecto->items;
        $grupos = $aspecto->imagenes->sortBy('orden_grupo')->groupBy('grupo_id');
    @endphp

    <div class="aspecto-bloque">
        <div class="aspecto-header">
            <div class="section-title">
                {{ $numeroAspecto }}. {{ $aspecto->aspectoCatalogo->nombre }}
            </div>

            @if($items->count())
                <div class="item">
                    <strong>{{ $numeroAspecto }}.1.</strong> {!! nl2br(e($items->first()->contenido)) !!}
                </div>
            @endif
        </div>

        @php $numeroItem = 2; @endphp
        @foreach($items->slice(1) as $item)
            <div class="item">
                <strong>{{ $numeroAspecto }}.{{ $numeroItem }}.</strong> {!! nl2br(e($item->contenido)) !!}
            </div>
            @php $numeroItem++; @endphp
        @endforeach

        @foreach($grupos as $grupoId => $imagenes)
            @php
                $descripcion = $imagenes->first()->grupo_descripcion;
                $imagenesOrdenadas = $imagenes->sortBy('orden')->values();
                $filas = [];
                $filaActual = [];
                $espacioOcupado = 0;

                foreach ($imagenesOrdenadas as $imagen) {
                    $rutaFoto = storage_path('app/public/'.$imagen->ruta_imagen);
                    $dimensiones = is_file($rutaFoto) ? @getimagesize($rutaFoto) : false;
                    $esVertical = $dimensiones && $dimensiones[1] > $dimensiones[0];
                    $columnas = $esVertical ? 3 : 5;

                    if ($espacioOcupado + $columnas > 15) {
                        $filas[] = $filaActual;
                        $filaActual = [];
                        $espacioOcupado = 0;
                    }

                    $anchoMaximo = $esVertical ? 120 : 205;
                    $anchoFoto = $anchoMaximo;
                    $altoFoto = 180;

                    if ($dimensiones && $dimensiones[0] > 0 && $dimensiones[1] > 0) {
                        $escala = min($anchoMaximo / $dimensiones[0], 180 / $dimensiones[1], 1);
                        $anchoFoto = round($dimensiones[0] * $escala, 1);
                        $altoFoto = round($dimensiones[1] * $escala, 1);
                    }

                    $filaActual[] = [
                        'imagen' => $imagen,
                        'ruta' => $rutaFoto,
                        'ancho' => $anchoFoto,
                        'alto' => $altoFoto,
                        'columnas' => $columnas,
                        'vertical' => $esVertical,
                    ];
                    $espacioOcupado += $columnas;
                }

                if (count($filaActual)) {
                    $filas[] = $filaActual;
                }
            @endphp

            <div class="photo-group">
                <table class="photo-table" border="0" cellspacing="0" cellpadding="0">
                    <colgroup>
                        @for($columna = 0; $columna < 15; $columna++)
                            <col style="width: 6.666%;">
                        @endfor
                    </colgroup>
                    <thead>
                        <tr>
                            <th colspan="15" class="photo-group-title">
                                {{ $descripcion ?: 'Evidencia del trabajo realizado' }}
                            </th>
                        </tr>
                    </thead>
                    @foreach($filas as $fila)
                        <tr class="photo-row">
                            @php $columnasUsadas = 0; @endphp
                            @foreach($fila as $foto)
                                <td class="photo-card" colspan="{{ $foto['columnas'] }}" style="border: 0; outline: 0; background: transparent;">
                                    @if($foto['vertical'])
                                        <img src="{{ $foto['ruta'] }}"
                                             height="{{ $foto['alto'] }}"
                                             alt="Evidencia fotogr&aacute;fica">
                                    @else
                                        <img src="{{ $foto['ruta'] }}"
                                             width="{{ $foto['ancho'] }}"
                                             alt="Evidencia fotogr&aacute;fica">
                                    @endif
                                </td>
                                @php $columnasUsadas += $foto['columnas']; @endphp
                            @endforeach

                            @if($columnasUsadas < 15)
                                <td class="photo-card photo-card-empty" colspan="{{ 15 - $columnasUsadas }}"></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
    </div>

    @php $numeroAspecto++; @endphp
@endforeach

<div class="footer">
    <p>Informe generado por <strong>ACOTEC E.I.R.L.</strong> — Jr. Amazonas Mz. 69 Lt. 31 Las Palmeras, Pacasmayo, La Libertad, Perú</p>
</div>

<script type="text/php">
if (isset($pdf)) {
    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
    $size = 8;
    $text = "Página {PAGE_NUM} de {PAGE_COUNT} | Informe generado por ACOTEC E.I.R.L.";
    $x = ($pdf->get_width() - $fontMetrics->get_text_width($text, $font, $size)) / 2;
    $y = $pdf->get_height() - 16;
    $pdf->page_text($x, $y, $text, $font, $size);
}
</script>

</body>
</html>
