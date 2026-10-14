@extends('plantilla.app')
@push('estilos')

@endpush
@section('contenido')
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title flex-grow-1">Sedes</h3>
                    @can('sedes_create')
                    <button type="button" class="btn btn-primary" id="btnCreate">
                        <i class="bi bi-plus-circle"></i> Nuevo
                    </button>
                    @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="listadoTable" class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Codigo</th>
                                    <th>Cliente</th>
                                    <th>Sede</th>
                                    <th>Dirección</th>                                   
                                    <th style="text-align: left">Teléfono</th>                                   
                                    <th>Estado</th>
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
@canany(['sedes_create', 'sedes_edit'])
    @include('sedes.action')
@endcanany
@endsection
@push('scripts')
<script>
class SedesManager extends CrudManager {
    constructor() {

        super("{{ url('sedes') }}");
        this.initializeDataTable(); 
        
        this.setupLiveSearchSelect({
            inputId: 'cliente_razon_social',
            hiddenId: 'cliente_id',
            url: "{{ route('clientes.buscar') }}",
            template: (item) => {
                return `${item.numero_documento} - ${item.razon_social}`;
            }
        });  
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
                { data: 'action', name: 'action', orderable: false, searchable: false},
                { data: 'id', name: 'id' },
                { data: 'cliente', name: 'cliente' },
                { data: 'nombre', name: 'nombre',},
                { data: 'direccion', name: 'direccion'},
                { data: 'telefono', name: 'telefono' },
                { data: 'activo', name: 'activo' }
            ],
            columnDefs: [
                { targets: 0, width: '10%', className: 'text-center' },
                { targets: 1, width: '10%', className: 'text-center'  },
                { targets: 2, width: '10%' },
                { targets: 3, width: '10%' },
                { targets: 4, width: '10%', className: 'text-center' },
                { targets: 5, width: '10%', className: 'text-center' },
                { targets: 6, width: '10%' },
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
        
        this.elements.modalTitle.textContent = 'Editar Sede';
        this.elements.methodField.value = 'PUT';

        // Asignar valores de la OT
        //document.getElementById('codigo_sede').value = response.codigo_sede || '';
        document.getElementById('cliente_id').value = response.cliente_id || '';
        document.getElementById('cliente_razon_social').value = response.cliente?.razon_social || '';
        document.getElementById('nombre').value = response.nombre || '';
        document.getElementById('direccion').value = response.direccion || '';       
        document.getElementById('telefono').value = response.telefono || '';
        document.getElementById('activo').value = response.activo ? '1' : '0';   
        
        this.form.action = `${this.baseUrl}/${id}`;
        this.modal.show();
        
    } catch (error) {
        this.showNotification('error', 'Error al cargar los datos');
        console.error('Error al cargar datos:', error);
    }

    }
    focusFirstField() {
        this.form.querySelector('input:not([type="hidden"]):not([readonly]), select, textarea')?.focus();
    }   
}
document.addEventListener('DOMContentLoaded', () => {
    new SedesManager();
});
document.getElementById('mnuVenta').classList.add('menu-open');
document.getElementById('itemSedes').classList.add('active');
</script>
@endpush
