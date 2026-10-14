@extends('plantilla.app')

@section('contenido')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header">
            <h3 class="card-title">
                Cronología de Orden de Trabajo
            </h3>
        </div>

        <div class="card-body">

            <div class="mb-4">
                <h5>
                    OT:
                    {{ $orden->codigo_ot_cliente }}
                </h5>                

                <h6>
                    Cliente:
                    {{ $orden->cliente->razon_social ?? '-' }}
                </h6>

                
            </div>

            <button type="button"
                    class="btn btn-outline-primary"
                    onclick="history.back()">
                <i class="bi bi-arrow-left"></i>
                Volver
            </button>

            <div class="timeline-horizontal">

                @foreach($timeline as $item)

                    <div class="timeline-item">

                        <div class="timeline-badge bg-{{ $item['color'] }}">
                        </div>

                        <div class="timeline-content">

                            <h6 class="mb-1">
                                {{ $item['estado'] }}
                            </h6>

                            <small>
                                Inicio:
                                {{ $item['fecha_inicio'] }}
                            </small>

                            <br>

                            <small>
                                Fin:
                                {{ $item['fecha_fin'] }}
                            </small>

                            <br>

                            <small class="text-primary fw-bold">
                                {{ $item['duracion'] }}
                            </small>

                            <br>

                            <small>
                                Usuario:
                                {{ $item['usuario'] }}
                            </small>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

@endsection

@push('estilos')
<style>

.timeline-horizontal {
    display: flex;
    overflow-x: auto;
    padding: 30px 10px;
    gap: 40px;
    align-items: flex-start;
}

.timeline-item {
    min-width: 250px;
    position: relative;
    text-align: center;
}

.timeline-item::after {
    content: '';
    position: absolute;
    top: 18px;
    left: 100%;
    width: 40px;
    height: 3px;
    background: #dcdcdc;
}

.timeline-item:last-child::after {
    display: none;
}

.timeline-badge {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    margin: auto;
    margin-bottom: 15px;
}

.timeline-content {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,.05);
}

</style>
@endpush
