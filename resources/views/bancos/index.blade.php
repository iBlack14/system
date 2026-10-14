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
                    <h3 class="card-title flex-grow-1">Bancos</h3>
                    @can('bancos_create')
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
                                    <th>Código</th>
                                    <th>Descripción</th>
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
@canany(['bancos_create', 'bancos_edit'])
    @include('bancos.action')
@endcanany
@endsection
@push('scripts')
<script>
class BancosManager extends CrudManager {
    constructor() {
        super("{{ url('bancos') }}");
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
                { data: 'action', name: 'action', orderable: false, searchable: false},
                { data: 'id', name: 'id'},
                { data: 'descripcion', name: 'descripcion' }
            ],
            columnDefs: [
                { targets: 0, width: '15%', className: 'text-center' },
                { targets: 1, width: '15%', className: 'text-center' },
                { targets: 2, width: '70%' , className: 'text-center'}
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
            
            this.elements.modalTitle.textContent = 'Editar registro';
            this.elements.methodField.value = 'PUT';
            
            // Llenar campos específicos
            //document.getElementById('id').value = response.id || '';
            document.getElementById('descripcion').value = response.descripcion || '';            

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
    new BancosManager();
});
document.getElementById('mnuConfiguracion').classList.add('menu-open');
document.getElementById('itemBancos')?.classList.add('active');
</script>
@endpush
