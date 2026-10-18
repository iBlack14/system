@extends('plantilla.app')

@section('contenido')
<div class="container-fluid py-2 acotec-dashboard">
    <!-- Accesos Directos Principales en la Cabecera -->
    <div class="mb-3">
        @include('dashboard.quick-links')
    </div>

    <!-- Grid de Métricas / Estados -->
    <div class="mb-3">
        <div class="section-heading d-flex align-items-center justify-content-between gap-2 mb-2">
            <h2><i class="bi bi-diagram-3 text-primary"></i> Flujo operativo de órdenes</h2>
            <span>{{ $estados->count() }} estados del proceso</span>
        </div>
        @php
            $fases = [
                ['Comercial y registro', 'bi-briefcase', 'Fase 1'],
                ['Operación y campo', 'bi-tools', 'Fase 2'],
                ['Documentación y facturación', 'bi-file-earmark-check', 'Fase 3'],
                ['Administración y liquidación', 'bi-cash-coin', 'Fase 4'],
            ];
        @endphp
        <div class="workflow-grid">
            @foreach ($estados->chunk(3) as $faseIndex => $estadosFase)
            @php($fase = $fases[$faseIndex] ?? ['Continuidad operativa', 'bi-arrow-repeat', 'Fase '.($faseIndex + 1)])
            <section class="workflow-phase" aria-labelledby="fase-{{ $faseIndex }}">
                <header>
                    <span class="workflow-phase-icon"><i class="bi {{ $fase[1] }}"></i></span>
                    <div><small>{{ $fase[2] }}</small><h3 id="fase-{{ $faseIndex }}">{{ $fase[0] }}</h3></div>
                </header>
                <div class="workflow-states">
                    @foreach ($estadosFase as $estado)
                    <a href="{{ route('ordenestrabajo.porEstado', $estado->id) }}" class="workflow-state text-decoration-none">
                        <span class="stat-indicator-dot stat-dot-{{ $estado->color }}"></span>
                        <span class="workflow-state-name" title="{{ $estado->nombre }}">{{ $estado->nombre }}</span>
                        <strong>{{ $ordenesPorEstado[$estado->id] ?? 0 }}</strong>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    @endforeach
                </div>
            </section>
            @endforeach
        </div>
    </div>
</div>
@endsection
