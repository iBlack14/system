@props([
    'title',
    'value',
    'badge' => null,
    'subtitle' => null,
    'icon' => 'bi-graph-up',
    'variant' => 'primary'
])

@php
    $borderClass = match($variant) {
        'success' => 'stat-border-success',
        'warning' => 'stat-border-warning',
        'danger'  => 'stat-border-danger',
        'info'    => 'stat-border-info',
        default   => 'stat-border-primary',
    };
    $badgeClass = match($variant) {
        'success' => 'bg-success-subtle text-success',
        'warning' => 'bg-warning-subtle text-warning',
        'danger'  => 'bg-danger-subtle text-danger',
        'info'    => 'bg-info-subtle text-info',
        default   => 'bg-primary-subtle text-primary',
    };
@endphp

<div class="card h-100 dashboard-kpi-card {{ $borderClass }} border-0 shadow-sm p-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="text-muted small fw-medium">{{ $title }}</span>
        <div class="p-2 {{ $badgeClass }} rounded-3">
            <i class="bi {{ $icon }} fs-5"></i>
        </div>
    </div>
    <div class="d-flex align-items-baseline gap-2">
        <h3 class="fw-bold mb-0 text-dark">{{ $value }}</h3>
        @if($badge)
            <span class="badge {{ $badgeClass }} rounded-pill small">{{ $badge }}</span>
        @endif
    </div>
    @if($subtitle)
        <div class="mt-2 text-muted" style="font-size: 0.78rem;">
            {!! $subtitle !!}
        </div>
    @endif
</div>
