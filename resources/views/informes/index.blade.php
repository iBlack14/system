@extends('plantilla.app')
@push('estilos')
<style>
.informes-table-wrap {
    width: 100%;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: .75rem;
    background: #fff;
}

#listadoTable {
    min-width: 1450px;
    margin-bottom: 0 !important;
}

#listadoTable th,
#listadoTable td {
    padding: .7rem .75rem;
    vertical-align: middle;
}

#listadoTable th { white-space: nowrap; }
#listadoTable td:nth-child(4),
#listadoTable td:nth-child(5),
#listadoTable td:nth-child(6) {
    white-space: normal;
    min-width: 180px;
}

.informes-table-wrap .dt-scroll-body {
    max-height: calc(100vh - 355px);
    overflow-y: auto !important;
    scrollbar-width: thin;
    scrollbar-color: #94a3b8 #eef2f7;
}

.informes-table-wrap .dt-scroll-body::-webkit-scrollbar { width: 10px; height: 12px; }
.informes-table-wrap .dt-scroll-body::-webkit-scrollbar-track { background: #eef2f7; }
.informes-table-wrap .dt-scroll-body::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border: 2px solid #eef2f7;
    border-radius: 999px;
}

.informes-table-wrap .dt-scroll-head th {
    background: #f8fafc !important;
    color: #334155;
    font-size: .76rem;
    letter-spacing: .025em;
    text-transform: uppercase;
}

.informes-table-wrap .dt-scroll-head th:first-child,
.informes-table-wrap .dt-scroll-body td:first-child {
    position: sticky;
    left: 0;
    z-index: 4;
    min-width: 132px;
    box-shadow: 6px 0 10px -10px rgba(15, 23, 42, .65);
}

.informes-table-wrap .dt-scroll-head th:first-child {
    z-index: 6;
    background: #f8fafc !important;
}

.informes-table-wrap .dt-scroll-body tr:nth-child(odd) td:first-child { background: #f1f3f5; }
.informes-table-wrap .dt-scroll-body tr:nth-child(even) td:first-child { background: #fff; }

.informes-table-wrap td:first-child .btn,
.informes-table-wrap td:first-child a.btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 2px !important;
    border-radius: .45rem;
}

@media (max-width: 767.98px) {
    .informes-table-wrap { overflow: visible; border: 0; }
    .informes-table-wrap .dt-scroll-head { display: none; }
    .informes-table-wrap .dt-scroll-body {
        max-height: none;
        overflow: visible !important;
        border: 0 !important;
    }
    .informes-table-wrap .dt-scroll-body table {
        min-width: 0 !important;
        width: 100% !important;
    }
    .informes-table-wrap td:first-child .btn,
    .informes-table-wrap td:first-child a.btn {
        width: 44px;
        height: 44px;
    }
}
</style>
@endpush

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title flex-grow-1">Informes</h3>

                    @can('informes_create')
                    <button type="button" class="btn btn-primary" id="btnCreate">
                        <i class="bi bi-plus-circle"></i> Nuevo
                    </button>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive informes-table-wrap">
                        <table id="listadoTable" class="table table-striped table-hover table-sm mobile-records">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Usuario</th>
                                    <th>ID Informe</th>
                                    <th>Cliente</th>
                                    <th>Sede</th>
                                    <th>Descripción</th>
                                    <th>Tipo Informe</th>
                                    <th>Orden Trabajo</th> 
                                    <th>Fecha de Creación</th>                                  
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer clearfix"></div>
            </div>
        </div>
    </div>
</div>

@canany(['informes_create', 'informes_edit'])
    @include('informes.action')
@endcanany
@endsection

@push('scripts')
<script>
class InformesManager extends CrudManager {

    constructor() {
        super("{{ url('informes') }}");
        this.initializeDataTable();
        

        // 🔥 NUEVO: capturar orden de grupos
       // this.setupOrdenGrupos();

        this.setupLiveSearchSelect({
            inputId: 'cliente_razon_social',
            hiddenId: 'cliente_id',
            url: "{{ route('clientes.buscar') }}",
            template: (item) => `${item.numero_documento} - ${item.razon_social}`,

            onSelect: (cliente) => {
                this.populateSelect(
                    'ot_codigo',
                    `{{ url('ordenestrabajo/por-cliente') }}/${cliente.id}?estado=1`,
                    item => `<option value="${item.id}">${item.codigo_ot_cliente} — ${item.descripcion ?? ''}</option>`,
                    { placeholder: 'Seleccione una Orden de Trabajo' }
                );
            }
        });
    }

    showCreateModal() {
        super.showCreateModal();
        this.resetForm();

        this.elements.modalTitle.textContent = 'Registrar Informe';
        this.elements.methodField.value = '';
        this.form.action = this.baseUrl;

        const otSelect = document.getElementById('ot_codigo');
        if (otSelect) {
            otSelect.disabled = false;
            otSelect.innerHTML = '<option value="">Seleccione una OT</option>';
        }

        const clienteRazon = document.getElementById('cliente_razon_social');
        if (clienteRazon) clienteRazon.value = '';
        const clienteId = document.getElementById('cliente_id');
        if (clienteId) clienteId.value = '';

        const otEstado = document.getElementById('ot_estado');
        if (otEstado) otEstado.value = '';

        const otDesc = document.getElementById('ot_descripcion');
        if (otDesc) otDesc.value = '';

        const informeTipoText = document.getElementById('informe_tipo_text');
        if (informeTipoText) informeTipoText.value = 'INFORME INICIAL';

        const informeTipo = document.getElementById('informe_tipo');
        if (informeTipo) informeTipo.value = 'inicial';

        const fechaField = document.getElementById('fecha_creacion');
        if (fechaField) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            fechaField.value = `${yyyy}-${mm}-${dd}`;
        }

        document.querySelectorAll('.items-container').forEach(el => el.innerHTML = '');
        document.querySelectorAll('.grupos-container').forEach(el => el.innerHTML = '');

        if (typeof aplicarAspectosPorEstado === 'function') {
            aplicarAspectosPorEstado(1);
        }

        if (typeof setResumenCollapsed === 'function') {
            setResumenCollapsed(false);
        }
        if (typeof actualizarBadgesResumen === 'function') {
            actualizarBadgesResumen();
        }
    }

    initializeDataTable() {

        this.tabla = $(this.elements.table).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: this.baseUrl,
                type: 'GET',
                timeout: 30000,
                error: (xhr) => {
                    console.error('Error al cargar informes:', xhr.responseText || xhr.statusText);
                    this.showNotification('error', 'No se pudo cargar la lista de informes');
                },
                complete: () => {
                    if ($.fn.dataTable.isDataTable(this.elements.table)) {
                        $(this.elements.table).DataTable().processing(false);
                    }
                }
            },
            columns: [
                { data: 'action', orderable: false, searchable: false },
                { data:'usuario'},
                { data: 'id' },
                { data: 'cliente' },
                { data:'sedes'},
                { data:'descripcion'},
                { data: 'tipo_informe' },
                { data: 'orden_trabajo_id' },         
                { data: 'fecha_creacion'}           
            ],
            responsive: false,
            scrollX: true,
            autoWidth: true,
            columnDefs: [
                { targets: 0, width: '132px', className: 'text-center' },
                { targets: [1, 3, 4, 5, 6], orderable: false },
                { targets: [2, 6, 7, 8], className: 'text-center' }
            ],
            order: [[2, 'desc']]
        });
    }

    async showEditModal(id) {

        try {

            this.isEditing = true;

            const data = await this.fetchData(`${this.baseUrl}/${id}`);
            if (!data) return;

            this.resetForm();

            console.log(this.elements.modalTitle);

            this.elements.modalTitle.textContent = 'Editar Informe';
            this.elements.methodField.value = 'PUT';
            this.form.action = `${this.baseUrl}/${id}`;

            const cliente = data.ordenTrabajo?.cliente ?? {};
            document.getElementById('cliente_razon_social').value = cliente.razon_social ?? '';
            document.getElementById('cliente_id').value = cliente.id ?? '';

            const ot = data.ordenTrabajo ?? {};

            const otSelect = document.getElementById('ot_codigo');
            if (otSelect) {
                otSelect.innerHTML = `
                    <option value="${ot.id ?? ''}" selected>
                        ${ot.codigo_ot_cliente ?? 'Sin OT'}
                    </option>`;
                otSelect.disabled = true;
            }

            document.getElementById('ot_estado').value =
                ot.estado_catalogo?.nombre ?? '';

            document.getElementById('ot_descripcion').value =
                ot.descripcion ?? '';

            document.getElementById('fecha_creacion').value =
                (data.fecha ?? '').substring(0, 10);

            if (ot.estado) aplicarAspectosPorEstado(ot.estado);

            // limpiar
            document.querySelectorAll('.items-container').forEach(el => el.innerHTML = '');
            document.querySelectorAll('.grupos-container').forEach(el => el.innerHTML = '');

            // cargar data
            cargarAspectosInforme(data);

            if (typeof actualizarBadgesResumen === 'function') {
                actualizarBadgesResumen();
            }
            if (typeof setResumenCollapsed === 'function') {
                setResumenCollapsed(true);
            }

            this.modal.show();

        } catch (error) {

            console.error('ERROR', error);
            this.showNotification('error', 'Error al cargar informe');
        }
    }

    focusFirstField() {
        const el = document.querySelector('[data-focus]');
        if (el) el.focus();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new InformesManager();
});

document.getElementById('mnuMantenimiento').classList.add('menu-open');
document.getElementById('itemInformes').classList.add('active');
</script>
@endpush
