@extends('plantilla.app')
@push('estilos')
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
                    <div class="table-responsive">
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
                    item => `<option value="${item.id}">${item.id} - ${item.codigo_ot_cliente} -  ${item.descripcion}</option>`
                );
            }
        });

        this.setupOTDescripcion();
    }

    /* =====================================================
       🔥 ORDEN DE GRUPOS (NUEVO)
    ===================================================== */
    // setupOrdenGrupos() {

    //     const form = this.form;
    //     if (!form) return;

    //     form.addEventListener('submit', () => {

    //         // eliminar anteriores (evita duplicados)
    //         form.querySelectorAll('input[name^="orden_grupo"]').forEach(el => el.remove());

    //         document.querySelectorAll('.grupo-imagen').forEach((grupo, index) => {

    //             const grupoId = grupo.dataset.grupo;

    //             if (!grupoId) return;

    //             const input = document.createElement('input');
    //             input.type = 'hidden';
    //             input.name = `orden_grupo[${grupoId}]`;
    //             input.value = index + 1;

    //             form.appendChild(input);
    //         });

    //     });
    // }

    /* ===================================================== */

    setupOTDescripcion() {

        const selectOT = document.getElementById('ot_codigo');
        const descripcionField = document.getElementById('ot_descripcion');
        const estadoField = document.getElementById('ot_estado');

        if (!selectOT || !descripcionField || !estadoField) return;

        selectOT.addEventListener('change', async (e) => {

            const otCodigo = e.target.value;

            descripcionField.value = '';
            estadoField.value = '';

            if (!otCodigo) return;

            try {
                const response = await fetch(`{{ url('ordenestrabajo/detalle') }}/${otCodigo}`);
                const data = await response.json();

                descripcionField.value = data.descripcion ?? 'Sin descripción';
                estadoField.value = data.estado ?? 'Sin estado';

            } catch (error) {
                console.error('Error OT', error);
                descripcionField.value = 'Error';
                estadoField.value = 'Error';
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
            responsive: true,
            order: [[1, 'asc']]
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