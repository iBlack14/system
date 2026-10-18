<div class="mb-3 dashboard-quick-links">
    <div class="section-heading d-flex align-items-center justify-content-between gap-2 mb-2">
        <h2><i class="bi bi-lightning-charge-fill text-warning"></i> Accesos directos</h2>
        <span>Módulos de acceso frecuente</span>
    </div>

    <div class="quick-access-grid">
        @foreach ([
            ['ordenestrabajo_list', 'ordenestrabajo.index', 'clipboard-check-fill', 'Órdenes de Trabajo', 'Gestión operativa', '#2563eb'],
            ['informes_list', 'informes.index', 'journal-text', 'Informes', 'Reportes técnicos', '#0284c7'],
            ['cotizaciones_list', 'cotizaciones.index', 'file-earmark-text-fill', 'Cotizaciones', 'Propuestas comerciales', '#0d9488'],
            ['ventas_list', 'ventas.index', 'receipt-cutoff', 'Ventas', 'Comprobantes y facturación', '#16a34a'],
            ['compras_list', 'compras.index', 'bag-check-fill', 'Compras', 'Proveedores y compras', '#7c3aed'],
            ['clientes_list', 'clientes.index', 'people-fill', 'Clientes', 'Cartera de clientes', '#4f46e5'],
            ['productos_list', 'productos.index', 'box-seam-fill', 'Productos', 'Almacén e inventario', '#ea580c'],
        ] as [$permission, $destination, $icon, $label, $description, $accentColor])
            @can($permission)
            <a class="quick-access-card" href="{{ route($destination) }}">
                <div class="quick-icon-wrapper" style="background-color: {{ $accentColor }}15; color: {{ $accentColor }};">
                    <i class="bi bi-{{ $icon }}"></i>
                </div>
                <div class="quick-info">
                    <div class="quick-title-row">
                        <span class="quick-label">{{ $label }}</span>
                        <i class="bi bi-arrow-up-right quick-card-arrow"></i>
                    </div>
                    <span class="quick-desc">{{ $description }}</span>
                </div>
            </a>
            @endcan
        @endforeach
    </div>
</div>
