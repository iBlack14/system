@extends('plantilla.app')

@section('contenido')
<div class="container-fluid py-2 acotec-dashboard">
    <!-- Header de Bienvenida / Control Operativo -->
    <div class="dashboard-hero d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> Control Operativo
                </span>
                <span class="text-muted small">• Actualizado en tiempo real</span>
            </div>
            <h1 class="dashboard-page-title mb-0">Vista General del Sistema</h1>
            <p class="text-muted small mb-0 mt-1">Supervisión integral de órdenes de trabajo, operaciones y flujo de taller.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('ordenestrabajo.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-list-task me-1"></i> Ver Órdenes de Trabajo
            </a>
        </div>
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

    <!-- Accesos Rápidos -->
    <div class="mt-4 pt-2">
        @include('dashboard.quick-links')
    </div>
</div>
@endsection
