@extends('plantilla.app')
@push('estilos')

@endpush
@section('contenido')
<div class="container-fluid unidades-page py-3">
    <!-- Page Header / Hero -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-7 fw-semibold">
                    <i class="bi bi-box-seam me-1"></i> Catálogo de Almacén
                </span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1 tracking-tight">Unidades de Medida</h1>
            <p class="text-muted small mb-0">Administración de estándares de medición (SUNAT / ISO) para productos, insumos y servicios</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('unidades_create')
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-3 fw-medium" id="btnCreate">
                <i class="bi bi-plus-lg fs-6"></i>
                <span>Nueva Unidad</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 dashboard-kpi-card stat-border-primary border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Unidades</span>
                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                        <i class="bi bi-rulers fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_unidades'] ?? 0 }}</h3>
                    <span class="badge bg-primary-subtle text-primary rounded-pill small">Registros</span>
                </div>
                <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-shield-check text-primary me-1"></i> Catálogo estandarizado
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 dashboard-kpi-card stat-border-success border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Unidades en Uso</span>
                    <div class="p-2 bg-success-subtle text-success rounded-3">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['unidades_en_uso'] ?? 0 }}</h3>
                    <span class="badge bg-success-subtle text-success rounded-pill small">Con Productos</span>
                </div>
                <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-arrow-repeat text-success me-1"></i> Vinculadas activamente
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-lg-4">
            <div class="card h-100 dashboard-kpi-card stat-border-info border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Productos Asignados</span>
                    <div class="p-2 bg-info-subtle text-info rounded-3">
                        <i class="bi bi-boxes fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_productos'] ?? 0 }}</h3>
                    <span class="badge bg-info-subtle text-info rounded-pill small">Artículos</span>
                </div>
                <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-tags text-info me-1"></i> Total en inventario
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-3 unidades-card">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-list-columns text-primary"></i>
                            Listado de Unidades de Medida
                        </h5>
                        <p class="text-muted small mb-0 mt-0.5">Consulta los códigos homologados y sus descripciones oficiales</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive rounded-3 border">
                        <table id="listadoTable" class="table table-hover align-middle mb-0 w-100 table-sticky-header">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 120px;">Acciones</th>
                                    <th class="text-center" style="width: 150px;">Código SUNAT</th>
                                    <th>Descripción de Unidad</th>
                                    <th class="text-center" style="width: 180px;">Productos Asociados</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@canany(['unidades_create', 'unidades_edit'])
    @include('unidades.action')
@endcanany
@endsection
@push('scripts')
<script>
class UnidadManager extends CrudManager {
    constructor() {
        super("{{ url('unidades') }}");
        this.initializeDataTable();
    }

    initializeDataTable() {
        this.tabla = $(this.elements.table).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: this.baseUrl,
                type: 'GET'
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'codigo', name: 'codigo' },
                { data: 'descripcion', name: 'descripcion' },
                { data: 'productos_asociados', name: 'productos_count', searchable: false }
            ],
            columnDefs: [
                { targets: 0, width: '120px', className: 'text-center', orderable: false, searchable: false },
                { targets: 1, width: '150px', className: 'text-center' },
                { targets: 2, className: 'text-start fw-medium text-dark' },
                { targets: 3, width: '180px', className: 'text-center' }
            ],
            responsive: true,
            order: [[1, 'asc']]
        });
    }

    async showEditModal(id) {
        try {
            const response = await this.fetchData(`${this.baseUrl}/${id}`);
            
            this.isEditing = true;
            this.resetForm();
            
            this.elements.modalTitle.textContent = 'Editar Unidad de Medida';
            this.elements.methodField.value = 'PUT';
            
            // Llenar campos específicos
            document.getElementById('codigo').value = response.codigo || '';
            document.getElementById('descripcion').value = response.descripcion || '';

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
}
document.addEventListener('DOMContentLoaded', () => {
    new UnidadManager();
});
document.getElementById('mnuAlmacen').classList.add('menu-open');
document.getElementById('itemUnidades').classList.add('active');
</script>
@endpush