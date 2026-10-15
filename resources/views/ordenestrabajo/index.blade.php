@extends('plantilla.app')
@push('estilos')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 0.375rem !important;
    background-color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    color: #0f172a !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    padding-left: 10px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 8px !important;
}

.select2-dropdown {
    border: 1px solid #cbd5e1 !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
    z-index: 1065 !important;
}

.select2-results__option {
    color: #1e293b !important;
    font-size: 0.875rem !important;
    padding: 8px 12px !important;
}

.select2-results__option--highlighted[aria-selected] {
    background-color: #2563eb !important;
    color: #ffffff !important;
}

/* Mantiene visibles todas las columnas de la relación de órdenes. */
.ordenes-table-wrap {
    width: 100%;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    background: #ffffff;
    -webkit-overflow-scrolling: touch;
}

#listadoTable {
    min-width: 1800px;
    margin-bottom: 0 !important;
}

#listadoTable th,
#listadoTable td {
    vertical-align: middle;
}

#listadoTable th {
    white-space: nowrap;
}

.ordenes-table-wrap .dt-container > .row:first-child,
.ordenes-table-wrap .dataTables_wrapper > .row:first-child {
    padding: 0.85rem 1rem;
    margin: 0;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.ordenes-table-wrap .dt-scroll-body {
    max-height: calc(100vh - 355px);
    overflow-y: auto !important;
    border-bottom: 1px solid #e2e8f0 !important;
    scrollbar-color: #94a3b8 #eef2f7;
    scrollbar-width: thin;
}

.ordenes-table-wrap .dt-scroll-body::-webkit-scrollbar {
    width: 10px;
    height: 12px;
}

.ordenes-table-wrap .dt-scroll-body::-webkit-scrollbar-track {
    background: #eef2f7;
}

.ordenes-table-wrap .dt-scroll-body::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border: 2px solid #eef2f7;
    border-radius: 999px;
}

.ordenes-table-wrap .dt-scroll-head th {
    background: #f8fafc !important;
    color: #334155;
    font-size: 0.76rem;
    letter-spacing: 0.025em;
    text-transform: uppercase;
}

.ordenes-table-wrap .dt-scroll-head th:first-child,
.ordenes-table-wrap .dt-scroll-body td:first-child {
    position: sticky;
    left: 0;
    z-index: 4;
    min-width: 118px;
    box-shadow: 6px 0 10px -10px rgba(15, 23, 42, 0.65);
}

.ordenes-table-wrap .dt-scroll-head th:first-child {
    z-index: 6;
    background: #f8fafc !important;
}

.ordenes-table-wrap .dt-scroll-body tr:nth-child(odd) td:first-child {
    background: #f1f3f5;
}

.ordenes-table-wrap .dt-scroll-body tr:nth-child(even) td:first-child {
    background: #ffffff;
}

.ordenes-table-wrap .dt-container > .row:last-child,
.ordenes-table-wrap .dataTables_wrapper > .row:last-child {
    padding: 0.8rem 1rem;
    margin: 0;
    background: #ffffff;
}

@media (max-width: 767.98px) {
    .ordenes-table-wrap {
        overflow: visible;
        border: 0;
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
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title flex-grow-1">Ordenes de Trabajo</h3>
                    @can('ordenestrabajo_create')
                    <button type="button" class="btn btn-primary" id="btnCreate">
                        <i class="bi bi-plus-circle"></i> Nuevo
                    </button>
                    @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive ordenes-table-wrap">
                        <table id="listadoTable" class="table table-striped table-hover table-sm mobile-records">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                   <th>Usuario</th>
                                    <th>Código OT. Cliente</th>
                                    <th>Cliente</th>
                                    <th>Sede</th>
                                    <th>Solicitante</th>
                                    <th>F. de Registro</th>
                                    <th>F. de Atención</th>
                                    <th>Técnico</th>
                                    <th>Motivo</th>                                    
                                    <th>Descripción</th>                                
                                    <th>Estado</th>                           
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    @can('ordenestrabajo_create')
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                            </div>
                        @endif                        
                    @endcan
                </div>
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!--end::Row-->
</div>
@canany(['ordenestrabajo_create', 'ordenestrabajo_edit'])
    @include('ordenestrabajo.action')
    @include('ordenestrabajo.ejecutar')
    @include('ordenestrabajo.documentacion')
    @include('ordenestrabajo.facturacion')
    @include('ordenestrabajo.procesopago')
    @include('ordenestrabajo.cancelado')
    @include('ordenestrabajo.notacredito')
@endcan
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

class OrdenTrabajoManager extends CrudManager {
    constructor() {
        super("{{ url('ordenestrabajo') }}");
        this.initializeDataTable();  
        
        this.initSelect2();          

        this.setupLiveSearchSelect({
        inputId: 'cliente_razon_social',
        hiddenId: 'cliente_id',
        url: "{{ route('clientes.buscar') }}",
        template: (item) => {
        return `${item.numero_documento} - ${item.razon_social}`;
        },

        onSelect: (cliente) => {

        /* ================== SEDES ================== */
        this.populateSelect(
            'sede_id',
            `{{ url('sedes/por-cliente') }}/${cliente.id}`,
            item => `<option value="${item.id}">${item.nombre}</option>`,
            {
                placeholder: 'Seleccione una sede'
            }
        );

        /* ================== RESPONSABLES ================== */
        this.populateSelect(
            'responsable_id',
            `{{ url('responsables/por-cliente') }}/${cliente.id}`,
            item => `<option value="${item.id}">${item.nombre} - ${item.cargo ?? ''}</option>`,
                {
                placeholder: 'Seleccione un solicitante'
                }
            );

            }
        });      

        
        this.setupLiveSearchSelect({
            inputId: 'tecnico_nombre',
            hiddenId: 'tecnico_id',
            url: "{{ route('tecnicos.buscar') }}",
            template: (item) => {
                return item.dni ? `${item.dni} - ${item.nombre}` : item.nombre;
            }
        });

       
    }

    initSelect2() {
    $('#sede_id').select2({
        dropdownParent: $('#modalUpdate'),
        width: '100%',
        placeholder: 'Seleccione una sede',
        allowClear: true
    });

    $('#responsable_id').select2({
        dropdownParent: $('#modalUpdate'),
        width: '100%',
        placeholder: 'Seleccione un solicitante',
        allowClear: true
    });
}

   showEjecutarModal(id) {

    fetch(`${this.baseUrl}/${id}`)
        .then(res => res.json())
        .then(data => {

            document.getElementById('ejecutar_codigo').value =
                data.codigo_ot_cliente;

            document.getElementById('ejecutar_descripcion').value =
                data.descripcion;

            document.getElementById('ejecutar_responsable').value =
                data.responsables?.nombre ?? '-';

            const form = document.getElementById('formEjecutarOT');
            form.action = `${this.baseUrl}/${id}/ejecutar`;

            new bootstrap.Modal(
                document.getElementById('modalEjecutarOT')
            ).show();
        });
}

   showDocumentacionModal(id) {

    fetch(`${this.baseUrl}/${id}`)
        .then(res => res.json())
        .then(data => {

            document.getElementById('documentacion_codigo').value =
                data.codigo_ot_cliente;

            document.getElementById('documentacion_descripcion').value =
                data.descripcion;

            document.getElementById('documentacion_responsable').value =
                data.responsables?.nombre ?? '-';

            const form = document.getElementById('formDocumentacionOT');
            form.action = `${this.baseUrl}/${id}/documentacion`;

            new bootstrap.Modal(
                document.getElementById('modalDocumentacionOT')
            ).show();
        });
}

   showFacturacionModal(id) {

        fetch(`${this.baseUrl}/${id}`)
            .then(res => res.json())
            .then(data => {

            document.getElementById('facturacion_codigo').value =
                data.codigo_ot_cliente;

            document.getElementById('facturacion_descripcion').value =
                data.descripcion;

            document.getElementById('facturacion_responsable').value =
                data.responsables?.nombre ?? '-';

            const form = document.getElementById('formFacturadoOT');
            form.action = `${this.baseUrl}/${id}/facturacion`;

            new bootstrap.Modal(
                document.getElementById('modalFacturadoOT')
            ).show();
        });
    }


    showNotaCreditoModal(id) {

        fetch(`${this.baseUrl}/${id}`)
            .then(res => res.json())
            .then(data => {

            document.getElementById('notacredito_codigo').value =
                data.codigo_ot_cliente;

            document.getElementById('notacredito_descripcion').value =
                data.descripcion;

            document.getElementById('notacredito_responsable').value =
                data.responsables?.nombre ?? '-';

            const form = document.getElementById('formNotaCreditoOT');
            form.action = `${this.baseUrl}/${id}/notacredito`;

            new bootstrap.Modal(
                document.getElementById('modalNotaCreditoOT')
            ).show();
        });
    }

    showCanceladoModal(id) {

        fetch(`${this.baseUrl}/${id}`)
        .then(res => res.json())
        .then(data => {

        

            document.getElementById('cancelado_codigo').value =
                data.codigo_ot_cliente;

            document.getElementById('cancelado_descripcion').value =
                data.descripcion;

            document.getElementById('cancelado_responsable').value =
                data.responsables?.nombre ?? '-';

            const total = parseFloat(data.ventas?.total || 0);

            document.getElementById('cancelado_montoventa').value =
                `S/ ${total.toFixed(2)}`;            

            document.getElementById('cancelado_rebatenotacredito').value =
                'S/ ' + parseFloat(data.rebate || 0).toFixed(2);

            // 🔥 AQUÍ LLAMAS LA FUNCIÓN
            populateBancoSelect('modalCanceladoOT');

            const form = document.getElementById('formCanceladoOT');
            form.action = `${this.baseUrl}/${id}/cancelado`;

            new bootstrap.Modal(
                document.getElementById('modalCanceladoOT')
            ).show();
        });
    }

 

    showProcesopagoModal(id) {

        fetch(`${this.baseUrl}/${id}`)
            .then(res => res.json())
            .then(data => {

            document.getElementById('procesopago_codigo').value =
                data.codigo_ot_cliente;

            document.getElementById('procesopago_descripcion').value =
                data.descripcion;

            document.getElementById('procesopago_responsable').value =
                data.responsables?.nombre ?? '-';

            const form = document.getElementById('formProcesopagoOT');
            form.action = `${this.baseUrl}/${id}/procesopago`;

            new bootstrap.Modal(
                document.getElementById('modalProcesopagoOT')
            ).show();
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
                { data: 'usuario', name: 'usuario' },
                { data: 'codigo_ot_cliente', name: 'codigo_ot_cliente' },
                { data: 'cliente', name: 'cliente.razon_social' },
                { data: 'sedes', name: 'sedes.nombre' },
                { data: 'responsables', name: 'responsables.nombre' },
                { data: 'fecha_registro', name: 'fecha_registro' },
                { data: 'fecha_atencion', name: 'fecha_atencion' },
                { data: 'tecnico', name: 'tecnico.nombre' },
                { data: 'motivo', name: 'motivo' },                
                { data: 'descripcion', name: 'descripcion' },           
                { data: 'estado', name: 'estado' },
                { data: 'observaciones', name: 'observaciones' }
            ],
            columnDefs: [
                { targets: 0, width: '118px', className: 'text-center' },              
                { targets: [2,6,7,10,11], className: 'text-center' }
            ],
            responsive: false,
            scrollX: true,
            autoWidth: true
        });
    }

async showEditModal(id) {
    try {
        const response = await this.fetchData(`${this.baseUrl}/${id}`);

        this.isEditing = true;
        this.resetForm();

        this.elements.modalTitle.textContent = 'Editar Orden de Trabajo';
        this.elements.methodField.value = 'PUT';

        /* ================= CLIENTE ================= */
        document.getElementById('cliente_id').value = response.cliente_id || '';
        document.getElementById('cliente_razon_social').value =
            response.cliente?.razon_social || '';

        /* ================= SEDES ================= */
        const sedeSelect = document.getElementById('sede_id');
        sedeSelect.innerHTML = '<option value="">Seleccione una sede</option>';

        if (response.sede_id && response.sedes) {
            sedeSelect.innerHTML += `
                <option value="${response.sede_id}" selected>
                    ${response.sedes.nombre}
                </option>
            `;
        }

        /* ================= RESPONSABLE ================= */
        const responsableSelect = document.getElementById('responsable_id');
        responsableSelect.innerHTML = '<option value="">Seleccione un solicitante</option>';

        if (response.responsable_id && response.responsables) {
            responsableSelect.innerHTML += `
                <option value="${response.responsable_id}" selected>
                    ${response.responsables.nombre}
                </option>
            `;
        }

        /* 🔥 REFRESCAR SELECT2 */
        $('#sede_id').trigger('change');
        $('#responsable_id').trigger('change');

        /* ================= TÉCNICO ================= */
        document.getElementById('tecnico_id').value = response.tecnico_id || '';
        document.getElementById('tecnico_nombre').value =
            response.tecnico?.nombre || '';

        /* ================= OT ================= */
        document.getElementById('codigo_ot_cliente').value =
            response.codigo_ot_cliente || '';
        document.getElementById('motivo').value = response.motivo || '';
        document.getElementById('descripcion').value = response.descripcion || '';
        document.getElementById('estado').value = response.estado || '';
        document.getElementById('observaciones').value =
            response.observaciones || '';

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
   showCreateModal() {
        super.showCreateModal();
        this.resetForm();

        // =========================
        // LIMPIAR CAMPOS NORMALES
        // =========================
        document.getElementById('cliente_id').value = '';
        document.getElementById('cliente_razon_social').value = '';
        document.getElementById('tecnico_id').value = '';
        document.getElementById('tecnico_nombre').value = '';

        document.getElementById('codigo_ot_cliente').value = '';
        document.getElementById('motivo').value = '';
        document.getElementById('descripcion').value = '';
        document.getElementById('observaciones').value = '';
        document.getElementById('estado').value = 'Registrado';

        // =========================
        // LIMPIAR SELECT2 (CLAVE)
        // =========================
        $('#sede_id').val(null).trigger('change');
        $('#responsable_id').val(null).trigger('change');
        $('#ot_codigo').val(null).trigger('change');

        // =========================
        // FOCUS FINAL
        // =========================
        document.getElementById('codigo_ot_cliente').focus();
    }




   showNotification(type, message) {
    const iconMap = {
        success: 'success',
        warning: 'warning',
        error: 'error'
    };

    const titleMap = {
        success: '¡Éxito!',
        warning: 'Advertencia',
        error: 'Error'
    };

    Swal.fire({
        icon: iconMap[type] || 'info',
        title: titleMap[type],
        html: message,
        timer: 2500,
        showConfirmButton: false,
        timerProgressBar: true,
        toast: true,
        position: 'top-end'
    });
}


}

//document.addEventListener('DOMContentLoaded', () => {
  //  new OrdenTrabajoManager();
document.addEventListener('DOMContentLoaded', () => {
    window.ordenTrabajoManager = new OrdenTrabajoManager();

});
document.getElementById('mnuMantenimiento').classList.add('menu-open');
document.getElementById('itemOrdenesTrabajo').classList.add('active');

function populateBancoSelect(modalId) {

    const select = document.querySelector(`#${modalId} select[name="banco_id"]`);
    if (!select) return;

    fetch('{{ url("bancos/getbancos") }}') // o '/api/bancos' si decides usar esa
        .then(response => response.json())
        .then(data => {
            console.log('BANCOS:', data); // 🔥 DEBUG


            select.innerHTML = '<option value="">Seleccione banco...</option>';

            data.forEach(item => {
                select.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${item.id}">
                        ${item.descripcion}
                    </option>`
                );
            });

        })
        .catch(error => console.error('Error cargando bancos:', error));
}


</script>
@endpush
