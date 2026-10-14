<div class="mb-4 dashboard-quick-links">
    <div class="section-heading d-flex align-items-center justify-content-between mb-2">
        <div>
            <small>Módulos principales</small>
            <h2><i class="bi bi-grid-fill text-primary"></i> Accesos rápidos</h2>
        </div>
        <span>Atajos a las tareas más utilizadas</span>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3">
        @foreach ([
            ['clientes_list', 'clientes.index', 'people-fill', 'Clientes', 'Gestión de cartera de clientes', '#3b82f6'],
            ['ventas_list', 'ventas.index', 'receipt-cutoff', 'Ventas', 'Comprobantes y facturación', '#10b981'],
            ['productos_list', 'productos.index', 'box-seam-fill', 'Productos', 'Control de stock y precios', '#f58216'],
            ['compras_list', 'compras.index', 'bag-check-fill', 'Compras', 'Registro de proveedores y compras', '#8b5cf6'],
            ['cotizaciones_list', 'cotizaciones.index', 'file-earmark-text-fill', 'Cotizaciones', 'Emisión y cálculo de propuestas', '#06b6d4'],
        ] as [$permission, $destination, $icon, $label, $description, $accentColor])
            @can($permission)
            <div class="col">
                <a class="card h-100 border-0 shadow-sm rounded-3 text-decoration-none quick-access-card p-3 d-flex flex-column" href="{{ route($destination) }}">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="quick-icon-wrapper" style="background-color: {{ $accentColor }}15; color: {{ $accentColor }};">
                            <i class="bi bi-{{ $icon }}"></i>
                        </div>
                        <i class="bi bi-arrow-up-right text-muted small quick-card-arrow"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">{{ $label }}</h6>
                    <p class="text-muted small mb-0 flex-grow-1" style="font-size: 11.5px; line-height: 1.4;">{{ $description }}</p>
                    <div class="pt-2 mt-2 border-top d-flex align-items-center justify-content-between text-primary small fw-semibold" style="font-size: 11px;">
                        <span>Acceder</span>
                        <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
                    </div>
                </a>
            </div>
            @endcan
        @endforeach
    </div>
</div>
