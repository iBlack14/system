@extends('plantilla.app')
@push('estilos')

@endpush

@php
    $stats = $stats ?? [
        'total_productos' => \App\Models\Producto::count(),
        'stock_critico'   => \App\Models\Producto::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
        'agotados'        => \App\Models\Producto::where('stock', '<=', 0)->count(),
        'valorizado'      => \App\Models\Producto::selectRaw('SUM(stock * precio_venta) as total')->value('total') ?? 0,
    ];
@endphp

@section('contenido')
<div class="container-fluid productos-page py-3">
    <!-- Flash Notifications -->
    <x-flash-alert />

    <!-- Page Header / Hero -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-7 fw-semibold">
                    <i class="bi bi-boxes me-1"></i> Control de Almacén & Inventario
                </span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1 tracking-tight">Catálogo de Productos</h1>
            <p class="text-muted small mb-0">Gestión de materiales, precios de lista, control de stock y parametrización de márgenes</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('productos_create')
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-3 fw-medium" id="btnCreate">
                <i class="bi bi-plus-lg fs-6"></i>
                <span>Nuevo Producto</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3" data-kpi-filter="all" title="Ver todos los productos" style="cursor: pointer;">
            <x-kpi-card 
                title="Total de Productos" 
                :value="$stats['total_productos']" 
                badge="Catálogo"
                subtitle="<i class='bi bi-check2-circle text-primary me-1'></i> Artículos registrados" 
                icon="bi-box-seam" 
                variant="primary" 
            />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-kpi-card 
                title="Valorizado de Venta" 
                :value="'S/ ' . number_format($stats['valorizado'], 2)" 
                subtitle="<i class='bi bi-graph-up-arrow text-success me-1'></i> Stock × Precio lista" 
                icon="bi-cash-stack" 
                variant="success" 
            />
        </div>
        <div class="col-sm-6 col-xl-3" data-kpi-filter="critical" title="Filtrar productos con stock crítico" style="cursor: pointer;">
            <x-kpi-card 
                title="Stock Crítico (≤ 5)" 
                :value="$stats['stock_critico']" 
                badge="Por Reponer"
                subtitle="<i class='bi bi-exclamation-triangle text-warning me-1'></i> Requiere abastecimiento" 
                icon="bi-hourglass-split" 
                variant="warning" 
            />
        </div>
        <div class="col-sm-6 col-xl-3" data-kpi-filter="out" title="Filtrar productos agotados" style="cursor: pointer;">
            <x-kpi-card 
                title="Agotados (0)" 
                :value="$stats['agotados']" 
                badge="Sin Stock"
                subtitle="<i class='bi bi-x-octagon text-danger me-1'></i> Disponibilidad nula" 
                icon="bi-slash-circle" 
                variant="danger" 
            />
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-3 productos-card">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-list-ul text-primary"></i>
                            Listado General de Productos
                        </h5>
                        <p class="text-muted small mb-0 mt-0.5">Explora, busca y actualiza precios o inventario en tiempo real</p>
                    </div>

                    <!-- Quick Filter Badges -->
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="text-muted small fw-medium me-1 d-none d-md-inline">Filtro rápido:</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary active rounded-pill px-3 py-1 btn-filter-stock" data-filter="all">
                            Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 btn-filter-stock" data-filter="available">
                            Disponibles
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 btn-filter-stock" data-filter="critical">
                            Críticos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 btn-filter-stock" data-filter="out">
                            Agotados
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="table-responsive rounded-3 border">
                        <table id="listadoTable" class="table table-hover align-middle mb-0 w-100 table-sticky-header">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 100px;">Acciones</th>
                                    <th class="text-center" style="width: 120px;">Código</th>
                                    <th>Producto / Material</th>
                                    <th class="text-center" style="width: 80px;">Unidad</th>
                                    <th class="text-center" style="width: 135px;">Stock</th>
                                    <th class="text-end" style="width: 115px;">P. Venta</th>
                                    <th class="text-end" style="width: 105px;">Costo</th>
                                    <th class="text-center" style="width: 85px;">Utilidad</th>
                                    <th class="text-center" style="width: 145px;">Ubicación</th>
                                    <th class="text-center" style="width: 90px;">Afec.</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer with Batch Import Box -->
                @can('productos_create')
                <div class="card-footer bg-light-subtle border-top py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success p-2 rounded-2">
                                <i class="bi bi-file-earmark-excel fs-6"></i>
                            </span>
                            <div>
                                <strong class="small d-block text-dark fw-semibold">Carga Masiva de Productos</strong>
                                <span class="text-muted" style="font-size: 0.78rem;">Sube un archivo Excel (.xlsx / .csv) para actualizar o crear productos en lote.</span>
                            </div>
                        </div>

                        <form action="{{ route('productos.importar') }}" method="POST" enctype="multipart/form-data" 
                            class="d-flex align-items-center gap-2">
                            @csrf
                            <input type="file" name="archivo" class="form-control form-control-sm rounded-2 bg-white" style="max-width: 260px;" accept=".xlsx,.xls,.csv" required>
                            <button type="submit" class="btn btn-outline-success btn-sm rounded-2 px-3 fw-medium d-inline-flex align-items-center gap-1.5 shadow-sm">
                                <i class="bi bi-upload"></i> Importar Excel
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>

@canany(['productos_create', 'productos_edit'])
    @include('productos.action')
@endcan
@endsection

@push('scripts')
<script>
class ProductoManager extends CrudManager {
    constructor() {
        super("{{ url('productos') }}");
        this.stockFilter = 'all';
        this.initializeDataTable();        

        this.populateSelect('unidad_codigo', '{{ route("unidades.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );

        this.populateSelect('afectacion_tipo_codigo', '{{ route("afectacion-tipos.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );

        this.initStockFilterButtons();
    }

    initStockFilterButtons() {
        document.querySelectorAll('.btn-filter-stock').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.btn-filter-stock').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.stockFilter = btn.dataset.filter;
                this.tabla.ajax.reload();
            });
        });

        document.querySelectorAll('[data-kpi-filter]').forEach(card => {
            card.addEventListener('click', () => {
                const filterVal = card.dataset.kpiFilter;
                const targetBtn = document.querySelector(`.btn-filter-stock[data-filter="${filterVal}"]`);
                if (targetBtn) targetBtn.click();
            });
        });
    }

    initializeDataTable() {
        this.tabla = $(this.elements.table).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: this.baseUrl,
                type: 'GET',
                data: (d) => {
                    if (this.stockFilter && this.stockFilter !== 'all') {
                        d.stock_status = this.stockFilter;
                    }
                }
            },
            columns: [
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        return `<div class="d-flex align-items-center justify-content-center gap-1">${data}</div>`;
                    }
                },
                { 
                    data: 'codigo', 
                    name: 'codigo',
                    render: function(data) {
                        return `<span class="badge bg-light text-primary border border-primary-subtle font-monospace px-2.5 py-1 fs-7 fw-semibold">${data || '—'}</span>`;
                    }
                },
                { 
                    data: 'nombre', 
                    name: 'nombre',
                    render: function(data, type, row) {
                        let imgHtml = (row.imagen && row.imagen.trim() !== '') 
                            ? `<div class="product-avatar-box me-2"><img src="{{ asset('uploads/productos') }}/${row.imagen}" alt="" loading="lazy"></div>`
                            : '';

                        return `<div class="d-flex align-items-center">
                            ${imgHtml}
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark" style="font-size: 13.5px; line-height: 1.35;">${data}</span>
                                ${row.descripcion ? `<span class="text-truncate text-muted small mt-0.5" style="max-width: 320px; font-size: 11.5px;">${row.descripcion}</span>` : ''}
                            </div>
                        </div>`;
                    }
                },
                { 
                    data: 'unidad_codigo', 
                    name: 'unidad_codigo',
                    render: function(data) {
                        return `<span class="badge bg-light text-dark border px-2 py-1 small fw-semibold font-monospace">${data || '—'}</span>`;
                    }
                },
                { 
                    data: 'stock', 
                    name: 'stock',
                    render: function(data, type, row) {
                        let num = parseFloat(data) || 0;
                        if (num <= 0) {
                            return `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold">
                                <i class="bi bi-x-circle me-1"></i>Agotado (0)
                            </span>`;
                        } else if (num <= 5) {
                            return `<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold">
                                <i class="bi bi-exclamation-triangle me-1"></i>Bajo (${num})
                            </span>`;
                        } else {
                            return `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold">
                                <i class="bi bi-check-circle me-1"></i>${num}
                            </span>`;
                        }
                    }
                },
                { 
                    data: 'precio_venta', 
                    name: 'precio_venta',
                    render: function(data) {
                        let val = parseFloat(data) || 0;
                        return `<span class="fw-bold text-dark" style="font-variant-numeric: tabular-nums;">S/ ${val.toFixed(2)}</span>`;
                    }
                },
                { 
                    data: 'costo_unitario', 
                    name: 'costo_unitario',
                    render: function(data) {
                        let val = parseFloat(data) || 0;
                        return `<span class="text-muted small" style="font-variant-numeric: tabular-nums;">S/ ${val.toFixed(2)}</span>`;
                    }
                },
                { 
                    data: 'utilidad', 
                    name: 'utilidad',
                    render: function(data) {
                        let val = parseFloat(data) || 0;
                        return `<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5 fw-semibold">+${val.toFixed(0)}%</span>`;
                    }
                },
                { 
                    data: 'almacen', 
                    name: 'almacen',
                    render: function(data, type, row) {
                        let alm = data ? `Alm. ${data}` : 'Alm. 1';
                        let col = row.ubicacion_columna ? `C${row.ubicacion_columna}` : '-';
                        let fil = row.ubicacion_fila ? `F${row.ubicacion_fila}` : '-';
                        return `<span class="badge bg-light text-muted border px-2 py-1 small">
                            <i class="bi bi-geo-alt text-primary me-1"></i>${alm} · ${col}/${fil}
                        </span>`;
                    }
                },
                { 
                    data: 'afectacion_tipo_codigo', 
                    name: 'afectacion_tipo_codigo',
                    render: function(data) {
                        return `<span class="badge bg-light text-muted border px-1.5 py-0.5 small">${data || '10'}</span>`;
                    }
                }
            ],
            columnDefs: [
                { targets: 0, width: '100px', className: 'text-center', orderable: false, searchable: false },
                { targets: 1, width: '120px', className: 'text-center' },
                { targets: 2, className: 'text-start' },
                { targets: 3, width: '80px', className: 'text-center' },
                { targets: 4, width: '135px', className: 'text-center' },
                { targets: 5, width: '115px', className: 'text-end' },
                { targets: 6, width: '105px', className: 'text-end' },
                { targets: 7, width: '85px', className: 'text-center' },
                { targets: 8, width: '145px', className: 'text-center' },
                { targets: 9, width: '90px', className: 'text-center' }
            ],
            order: [[1, 'desc']],
            responsive: true
        });
    }

    async showEditModal(id) {
        try {
            const response = await this.fetchData(`${this.baseUrl}/${id}`);
            
            this.isEditing = true;
            this.resetForm();
            
            this.elements.modalTitle.textContent = 'Editar Producto / Insumo';
            this.elements.methodField.value = 'PUT';
            
            // Llenar campos específicos
            document.getElementById('unidad_codigo').value = response.unidad_codigo;
            document.getElementById('afectacion_tipo_codigo').value = response.afectacion_tipo_codigo;
            document.getElementById('codigo').value = response.codigo || '';
            document.getElementById('nombre').value = response.nombre || '';
            document.getElementById('stock').value = response.stock;
            document.getElementById('descripcion').value = response.descripcion || '';
            document.getElementById('costo_unitario').value = response.costo_unitario;
            document.getElementById('ubicacion_columna').value = response.ubicacion_columna;
            document.getElementById('ubicacion_fila').value = response.ubicacion_fila;            
            document.getElementById('precio_venta').value = response.precio_venta;
            document.getElementById('utilidad').value = response.utilidad;
            document.getElementById('almacen').value = response.almacen;            
            document.getElementById('porcentaje5').value = response.porcentaje5;
            document.getElementById('porcentaje10').value = response.porcentaje10;
            document.getElementById('porcentaje50').value = response.porcentaje50;
            document.getElementById('porcentaje100').value = response.porcentaje100;

            if (response.imagen && response.imagen !== "") {
                document.getElementById('imagen_producto').src = "{{ asset('uploads/productos') }}/" + response.imagen;
                document.getElementById('imagen_producto').style.display = 'block';
            } else {
                document.getElementById('imagen_producto').style.display = 'none';
            }

            this.form.action = `${this.baseUrl}/${id}`;
            this.modal.show();
            
        } catch (error) {
            this.showNotification('error', 'Error al cargar los datos');
            console.error('Error al cargar datos:', error);
        }
    }

    focusFirstField() {
        document.getElementById('codigo').focus();
    }

    showCreateModal() {
        super.showCreateModal();
        this.elements.modalTitle.textContent = 'Nuevo Producto / Insumo';
        document.getElementById('unidad_codigo').value = 'NIU';
        document.getElementById('afectacion_tipo_codigo').value = '10';
        document.getElementById('imagen_producto').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ProductoManager();
});
document.getElementById('mnuAlmacen').classList.add('menu-open');
document.getElementById('itemProductos').classList.add('active');
</script>
@endpush
