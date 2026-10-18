@extends('plantilla.app')
@push('estilos')
<style>
/* La tabla contiene muchas columnas: es preferible desplazarla horizontalmente
   antes que comprimir direcciones y teléfonos hasta volverlos ilegibles. */
.proveedores-table-wrap {
    overflow-x: auto;
    scrollbar-gutter: stable;
}

.proveedores-scroll-top {
    display: none;
    height: 15px;
    margin-bottom: 8px;
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-color: #94a3b8 #eef2f7;
    scrollbar-width: thin;
}

.proveedores-scroll-top.is-scrollable {
    display: block;
}

.proveedores-scroll-top-content {
    height: 1px;
}

#listadoTable {
    width: 100% !important;
    min-width: 1000px;
}

#listadoTable th {
    white-space: nowrap;
}

#listadoTable th,
#listadoTable td {
    padding: .7rem .65rem;
    vertical-align: middle;
}

#listadoTable td:nth-child(1),
#listadoTable td:nth-child(2),
#listadoTable td:nth-child(3),
#listadoTable td:nth-child(7) {
    white-space: nowrap;
}

#listadoTable td:nth-child(4),
#listadoTable td:nth-child(5),
#listadoTable td:nth-child(6),
#listadoTable td:nth-child(9) {
    line-height: 1.45;
    overflow-wrap: break-word;
}

#listadoTable td:nth-child(8) {
    overflow-wrap: anywhere;
}

@media (min-width: 1500px) {
    #listadoTable {
        min-width: 1380px;
    }
}
</style>

@endpush
@section('contenido')
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-truck text-primary"></i> Proveedores
                        </h3>
                        <p class="text-muted small mb-0 mt-1">Gestión y directorio de proveedores registrados</p>
                    </div>
                    @can('proveedores_create')
                    <button type="button" class="btn btn-primary" id="btnCreate">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
                    </button>
                    @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="proveedoresScrollTop" class="proveedores-scroll-top" aria-hidden="true">
                        <div class="proveedores-scroll-top-content"></div>
                    </div>
                    <div class="table-responsive proveedores-table-wrap">
                        <table id="listadoTable" class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Tipo Documento</th>
                                    <th>Número Documento</th>
                                    <th>Razón Social</th>
                                    <th>Dirección</th>
                                    <th>Contacto</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Rubro Descrip.</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!--end::Row-->
</div>
@canany(['proveedores_create', 'proveedores_edit'])
    @include('proveedores.action')
@endcanany
@endsection
@push('scripts')
<script>
class ProveedorManager extends CrudManager {
    constructor() {
        super("{{ url('proveedores') }}");
        this.initializeDataTable();
        this.setupSynchronizedScrollbars();
        this.populateSelect('documento_tipo_codigo', '{{ route("documento-tipos.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );
    }

    initializeDataTable() {
        const compactLayout = window.matchMedia('(max-width: 1499px)').matches;

        this.tabla = $(this.elements.table).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: this.baseUrl,
                type: 'GET'
            },
           columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false},
                { data: 'documento_tipo_codigo', name: 'documento_tipo_codigo'},
                { data: 'numero_documento', name: 'numero_documento' },
                { data: 'razon_social', name: 'razon_social' },
                { data: 'direccion', name: 'direccion' },
                { data: 'prov_contacto', name: 'prov_contacto' },
                { data: 'telefono', name: 'telefono' },
                { data: 'email', name: 'email' },
                { data: 'rubro', name: 'rubro' }
            ],
            columnDefs: [
                { targets: 0, width: '80px', className: 'text-center' },
                { targets: 1, width: '105px', className: 'text-center' },
                { targets: 2, width: '125px', className: 'text-center' },
                { targets: 3, width: '180px' },
                { targets: 4, width: '230px' },
                { targets: 5, width: '145px' },
                { targets: 6, width: '130px' },
                { targets: 7, width: '210px', visible: !compactLayout },
                { targets: 8, width: '175px', visible: !compactLayout }
            ],
            autoWidth: false,
            responsive: false,
            order: [[1, 'asc']]
        });
    }

    setupSynchronizedScrollbars() {
        const topScroll = document.getElementById('proveedoresScrollTop');
        const topContent = topScroll?.querySelector('.proveedores-scroll-top-content');
        const bottomScroll = document.querySelector('.proveedores-table-wrap');

        if (!topScroll || !topContent || !bottomScroll) return;

        let syncing = false;

        const updateScrollbar = () => {
            const contentWidth = bottomScroll.scrollWidth;
            topContent.style.width = `${contentWidth}px`;
            topScroll.classList.toggle('is-scrollable', contentWidth > bottomScroll.clientWidth + 1);
            topScroll.scrollLeft = bottomScroll.scrollLeft;
        };

        topScroll.addEventListener('scroll', () => {
            if (syncing) return;
            syncing = true;
            bottomScroll.scrollLeft = topScroll.scrollLeft;
            syncing = false;
        });

        bottomScroll.addEventListener('scroll', () => {
            if (syncing) return;
            syncing = true;
            topScroll.scrollLeft = bottomScroll.scrollLeft;
            syncing = false;
        });

        this.tabla.on('draw.dt column-visibility.dt', () => requestAnimationFrame(updateScrollbar));
        window.addEventListener('resize', updateScrollbar);

        if ('ResizeObserver' in window) {
            new ResizeObserver(updateScrollbar).observe(bottomScroll);
        }

        requestAnimationFrame(updateScrollbar);
    }

    async showEditModal(id) {
        try {
            const response = await this.fetchData(`${this.baseUrl}/${id}`);
            
            this.isEditing = true;
            this.resetForm();
            
            this.elements.modalTitle.textContent = 'Editar registro';
            this.elements.methodField.value = 'PUT';
            
            // Llenar campos específicos
            document.getElementById('documento_tipo_codigo').value = response.documento_tipo_codigo;
            document.getElementById('numero_documento').value = response.numero_documento || '';
            document.getElementById('razon_social').value = response.razon_social || '';
            document.getElementById('direccion').value = response.direccion || '';
            document.getElementById('prov_contacto').value = response.prov_contacto || '';
            document.getElementById('telefono').value = response.telefono || '';
            document.getElementById('email').value = response.email || '';
            document.getElementById('rubro').value = response.rubro || '';

            this.form.action = `${this.baseUrl}/${id}`;
            
            this.modal.show();
            
        } catch (error) {
            this.showNotification('error', 'Error al cargar los datos');
            console.error('Error al cargar datos:', error);
        }
    }
    focusFirstField() {
        document.getElementById('razon_social').focus();
    }
    showCreateModal(){
        super.showCreateModal();
        document.getElementById('documento_tipo_codigo').value = '01';
    }
}
document.addEventListener('DOMContentLoaded', () => {
    new ProveedorManager();
});
document.getElementById('mnuAlmacen').classList.add('menu-open');
document.getElementById('itemProveedores').classList.add('active');
</script>
@endpush
