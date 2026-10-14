@props([
    'variant' => 'primary',
    'icon' => null,
    'pill' => true
])

@php
    $chipClass = match($variant) {
        'success' => 'stat-chip-success',
        'warning' => 'stat-chip-warning',
        'danger'  => 'stat-chip-danger',
        'info'    => 'stat-chip-info',
        default   => 'stat-chip-primary',
    };
    $roundedClass = $pill ? 'rounded-pill' : 'rounded-2';
@endphp

<span {{ $attributes->merge(['class' => "badge badge-status-chip {$chipClass} {$roundedClass} px-2.5 py-1 text-uppercase fw-semibold d-inline-flex align-items-center gap-1"]) }}>
    @if($icon)
        <i class="bi {{ $icon }}"></i>
    @endif
    {{ $slot }}
</span>
