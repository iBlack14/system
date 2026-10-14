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
                    <h3 class="card-title flex-grow-1">Transporte de Personal</h3>
                    @can('tpersonal_create')
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
                                    <th>Departamento</th>
                                    <th>Provincia</th>
                                    <th>Distrito</th>
                                    <th>Unidad</th>
                                    <th>Precio</th>
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
@canany(['tpersonal_create', 'tpersonal_edit'])
    @include('tpersonal.action')
@endcanany
@endsection
@push('scripts')
<script>
class TPersonalManager extends CrudManager {
constructor() {
    super("{{ url('tpersonal') }}");
    this.initializeDataTable();

    //cargar unidades
    this.populateSelect('unidad_codigo', '{{ route("unidades.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );

    // Cargar departamentos
    this.populateSelect('department_id', '{{ route("departamentos.select") }}', item =>
        `<option value="${item.id.toString().padStart(2, '0')}">${item.id} - ${item.name}</option>`
    );
     // --- Reutilizando el patrón onSelect ---
    //this.onSelectDepartamento();

    const departmentSelect = document.getElementById('department_id');    
    if (departmentSelect) {
        departmentSelect.addEventListener('change', (e) => this.onSelectDepartamento(e.target.value));
    }

    const provinceSelect = document.getElementById('province_id');
    if (provinceSelect) {
    provinceSelect.addEventListener('change', (e) => this.onSelectProvincia(e.target.value));
    }


    }
    onSelectDepartamento(departmentId) {
    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('districts_id');

    if (!provinceSelect) {
        console.warn("⚠️ No se encontró el select con id 'province_id'");
        return;
    }

    // Limpiar selects dependientes
    provinceSelect.innerHTML = '<option value="">Cargando provincias...</option>';
    if (districtSelect) {
        districtSelect.innerHTML = '<option value="">Seleccione una provincia primero</option>';
    }

    if (!departmentId) {
        provinceSelect.innerHTML = '<option value="">Seleccione un departamento primero</option>';
        return;
    }

    // 🔹 Llamada al backend
    fetch(`{{ url('provincias/por-departamento') }}/${departmentId}`)
        .then(response => response.json())
        .then(data => {
            console.log("📦 Provincias recibidas:", data);

            if (!Array.isArray(data) || data.length === 0) {
                provinceSelect.innerHTML = '<option value="">No hay provincias disponibles</option>';
                return;
            }

            // Poblar el select
            provinceSelect.innerHTML = '<option value="">Seleccione una provincia</option>';
            data.forEach(item => {
                provinceSelect.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${item.id.toString().padStart(4, '0')}">${item.name}</option>`
                );
            });
        })
        .catch(error => {
            console.error("❌ Error al cargar provincias:", error);
            provinceSelect.innerHTML = '<option value="">Error al cargar provincias</option>';
        });
}

onSelectProvincia(provinceId) {
    const districtSelect = document.getElementById('districts_id');

    if (!districtSelect) {
        console.warn("⚠️ No se encontró el select con id 'districts_id'");
        return;
    }

    // Limpiar el select de distritos
    districtSelect.innerHTML = '<option value="">Cargando distritos...</option>';

    if (!provinceId) {
        districtSelect.innerHTML = '<option value="">Seleccione una provincia primero</option>';
        return;
    }

    // 🔹 Llamada al backend
    fetch(`{{ url('distritos/por-provincia') }}/${provinceId}`)
        .then(response => response.json())
        .then(data => {
            console.log("📦 Distritos recibidos:", data);

            if (!Array.isArray(data) || data.length === 0) {
                districtSelect.innerHTML = '<option value="">No hay distritos disponibles</option>';
                return;
            }

            // Poblar el select
            districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
            data.forEach(item => {
                districtSelect.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${item.id.toString().padStart(6, '0')}">${item.name}</option>`
                );
            });
        })
        .catch(error => {
            console.error("❌ Error al cargar distritos:", error);
            districtSelect.innerHTML = '<option value="">Error al cargar distritos</option>';
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
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'id', name: 'id'},
            { data: 'department_name', name: 'department_name'},
            { data: 'province_name', name: 'province_name'},
            { data: 'district_name', name: 'district_name' },
            { data: 'unidad_codigo', name: 'unidad_codigo' },
            { data: 'precio', name: 'precio' }
            ],
            columnDefs: [
                { targets: 0, width: '10%', className: 'text-center' },
                { targets: 1, width: '10%', className: 'text-center' },
                { targets: 2, width: '20%' , className: 'text-center'},
                { targets: 3, width: '20%' , className: 'text-center'},
                { targets: 4, width: '20%' , className: 'text-center'},
                { targets: 4, width: '10%' , className: 'text-center'},
                { targets: 5, width: '10%' , className: 'text-center'}
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
            const departmentSelect = document.getElementById('department_id');
            departmentSelect.value = response.department_id || '';

            const provinceSelect = document.getElementById('province_id');
            provinceSelect.innerHTML = '<option value="">Cargando provincias...</option>';

            const provinciasResponse = await fetch(`{{ url('provincias/por-departamento') }}/${response.department_id}`);
            const provincias = await provinciasResponse.json();

            provinceSelect.innerHTML = '<option value="">Seleccione una provincia</option>';
            provincias.forEach(p => {
            provinceSelect.insertAdjacentHTML(
                'beforeend',
                `<option value="${p.id.toString().padStart(4, '0')}">${p.name}</option>`
            );
            });

            provinceSelect.value = response.province_id || '';

            // 4️⃣ Poblar Distritos de la provincia seleccionada
        const districtSelect = document.getElementById('districts_id');
        districtSelect.innerHTML = '<option value="">Cargando distritos...</option>';

        const distritosResponse = await fetch(`{{ url('distritos/por-provincia') }}/${response.province_id}`);
        const distritos = await distritosResponse.json();

        districtSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
        distritos.forEach(d => {
            districtSelect.insertAdjacentHTML(
                'beforeend',
                `<option value="${d.id.toString().padStart(6, '0')}">${d.name}</option>`
            );
        });
        districtSelect.value = response.districts_id || '';

        document.getElementById('unidad_codigo').value = response.unidad_codigo || '';        

        // 5️⃣ Llenar precio
        document.getElementById('precio').value = response.precio || '';

        // 6️⃣ Mostrar modal
        //this.modal.show();




//            document.getElementById('department_id').value = response.department_id || '';
  //          document.getElementById('province_id').value = response.province_id || '';
    //        document.getElementById('districts_id').value = response.districts_id || '';
      //      document.getElementById('precio').value = response.precio || '';

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
    new TPersonalManager();
});
document.getElementById('mnuVenta').classList.add('menu-open');
document.getElementById('itemTPersonal').classList.add('active');
</script>
@endpush
