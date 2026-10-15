@extends('plantilla.app')
@push('estilos')
<style>
.os-table-wrap {
    width: 100%;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: .75rem;
    background: #fff;
}

#listadoTable {
    min-width: 2200px;
    margin-bottom: 0 !important;
}

#listadoTable th,
#listadoTable td {
    padding: .65rem .75rem;
    vertical-align: middle;
    white-space: nowrap;
}

.os-table-wrap .dt-scroll-body {
    max-height: calc(100vh - 355px);
    overflow-y: auto !important;
    scrollbar-width: thin;
    scrollbar-color: #94a3b8 #eef2f7;
}

.os-table-wrap .dt-scroll-body::-webkit-scrollbar {
    width: 10px;
    height: 12px;
}

.os-table-wrap .dt-scroll-body::-webkit-scrollbar-track { background: #eef2f7; }
.os-table-wrap .dt-scroll-body::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border: 2px solid #eef2f7;
    border-radius: 999px;
}

.os-table-wrap .dt-scroll-head th {
    background: #f8fafc !important;
    color: #334155;
    font-size: .76rem;
    letter-spacing: .025em;
    text-transform: uppercase;
}

.os-table-wrap .dt-scroll-head th:first-child,
.os-table-wrap .dt-scroll-body td:first-child {
    position: sticky;
    left: 0;
    z-index: 4;
    min-width: 150px;
    box-shadow: 6px 0 10px -10px rgba(15, 23, 42, .65);
}

.os-table-wrap .dt-scroll-head th:first-child {
    z-index: 6;
    background: #f8fafc !important;
}

.os-table-wrap .dt-scroll-body tr:nth-child(odd) td:first-child { background: #f1f3f5; }
.os-table-wrap .dt-scroll-body tr:nth-child(even) td:first-child { background: #fff; }

.os-table-wrap td:first-child .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 2px !important;
    border-radius: .45rem;
}

.os-table-wrap td:first-child .btn i { font-size: .82rem; }

@media (max-width: 767.98px) {
    .os-table-wrap { overflow: visible; border: 0; }
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
                    <h3 class="card-title flex-grow-1">Ordenes de Servicio</h3>
                    @can('ordenesservicio_create')
                    <button type="button" class="btn btn-primary" id="btnCreate">
                        <i class="bi bi-plus-circle"></i> Nuevo
                    </button>
                    @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive os-table-wrap">
                        <table id="listadoTable" class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                   <th>Usuario</th>
                                    <th>Código OS. Cliente</th>
                                    <th>OT. Cliente</th>
                                    <th>Cliente</th>
                                    <th>Código Conformidad</th>
                                    <th>F. de Creación</th>
                                    <th>F. de Entrega</th>                                    
                                    <th>Forma Pago</th>
                                    <th>Lugar Entrega</th>
                                    <th>Descripción</th>
                                    <th>Penalidad</th>
                                    <th>Monto</th>
                                    <th>Estado</th>                           
                                    <th>Observaciones</th>
                                    <th>Días</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    @can('ordenesservicio_create')
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
@canany(['ordenesservicio_create', 'ordenesservicio_edit'])
    @include('ordenesservicio.action')
    
@endcan
@include('ordenesservicio.conformidad')
@endsection
@push('scripts')
<script>
class OrdenesServicioManager extends CrudManager {
    constructor() {
        super("{{ url('ordenesservicio') }}");
        this.initializeDataTable();        

      this.populateSelect('documento_tipo_codigo', '{{ route("documento-tipos.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );

        this.setupLiveSearchSelect({
            inputId: 'cliente_razon_social',
            hiddenId: 'cliente_id',
            url: "{{ route('clientes.buscar') }}",
            template: (item) => {
                return `${item.numero_documento} - ${item.razon_social}`;
        },
        

        onSelect: (cliente) => {
        // cuando seleccionas cliente, llenamos el combo de órdenes de trabajo
            this.populateSelect(
            'ot_codigo',
            `{{ url('ordenestrabajo/por-cliente') }}/${cliente.id}?estado=6`,
            item => `<option value="${item.id}">${item.id} - ${item.codigo_ot_cliente} - ${item.descripcion}</option>`
                             );
        }      
            });  
        //document.getElementById('btnRegistrarCliente').addEventListener('click', () => this.registerClient());  
        //this.setupOTDescripcion();    
    }

   setupOTDescripcion() {
    const selectOT = document.getElementById('ot_codigo');
    const descripcionField = document.getElementById('ot_descripcion');

    if (!selectOT || !descripcionField) return;

    selectOT.addEventListener('change', async (e) => {
        const otCodigo = e.target.value;
        descripcionField.value = '';

        if (!otCodigo) return;

        try {
            const response = await fetch(
                `{{ url('ordenestrabajo/detalle') }}/${otCodigo}`
            );

            const data = await response.json();

            descripcionField.value = data.descripcion ?? 'Sin descripción';

        } catch (error) {
            console.error('Error al cargar descripción OT', error);
            descripcionField.value = 'Error al cargar descripción';
        }
            });
    }


showConformidadOSModal(id) {

    console.clear();
    console.log('▶️ showConformidadOSModal ID:', id);

    const modalEl = document.getElementById('modalConformidadOSE');
    const codigoEl = document.getElementById('conformidad_codigoos');
    const descripcionEl = document.getElementById('conformidad_descripcionos');
    const form = document.getElementById('formConformidadOSE');

    // 🔴 VALIDACIÓN ESTRICTA DEL DOM
    if (!modalEl) {
        alert('❌ modalConformidadOS NO existe en el DOM');
        return;
    }

    if (!codigoEl || !descripcionEl || !form) {
        alert('❌ Inputs o formulario no encontrados');
        return;
    }

    // 🔥 ELIMINAR BACKDROP HUÉRFANO (CLAVE)
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');

    // 🔹 Reset seguro
    codigoEl.value = '';
    descripcionEl.value = '';

    fetch(`${this.baseUrl}/${id}`)
        .then(res => {
            if (!res.ok) throw new Error('No se pudo obtener la OS');
            return res.json();
        })
        .then(data => {
            console.log('✅ DATA:', data);

            codigoEl.value = data.codigo_orden_servicio ?? '—';
            descripcionEl.value = data.descripcion ?? '—';
        })
        .catch(err => {
            console.error('⚠️ Error fetch:', err.message);

            codigoEl.value = 'NO DISPONIBLE';
            descripcionEl.value = 'No se pudo cargar la información';
        })
        .finally(() => {

            form.action = `${this.baseUrl}/${id}/conformidad`;

            const modal = new bootstrap.Modal(modalEl, {
                backdrop: 'static',
                keyboard: false
            });

            modal.show();
            console.log('🟢 Modal mostrado correctamente');
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
                { data: 'codigo_orden_servicio', name: 'codigo_orden_servicio' },
                { data: 'codigo_ot_cliente', name: 'ordenTrabajo.codigo_ot_cliente'},                
                { data: 'cliente', name: 'cliente.razon_social' },
                { data: 'codigo_conformidad', name: 'codigo_conformidad' },
                { data: 'fecha_creacion', name: 'fecha_creacion' },
                { data: 'fecha_entrega', name: 'fecha_entrega' },
                { data: 'forma_pago', name: 'forma_pago' },
                { data: 'lugar_entrega', name: 'lugar_entrega' },
                { data: 'descripcion', name: 'descripcion' },
                { data: 'penalidad', name: 'penalidad' },
                {data: 'monto', name: 'monto'},
                { data: 'estado', name: 'estado' },                
                { data: 'observaciones', name: 'observaciones' },
                { data: 'dias', name: 'dias' }
            ],
            columnDefs: [
                { targets: 0, width: '150px', className: 'text-center' },           
                { targets: [2,6,7,10,11], className: 'text-center' }
            ],
            responsive: false,
            scrollX: true,
            autoWidth: true
        });
    }

    async registerClient() {
        // Recoge los datos del formulario
        const documento_tipo_codigo = document.getElementById('documento_tipo_codigo').value;
        const numero_documento = document.getElementById('numero_documento').value;
        const razon_social = document.getElementById('razon_social').value;

        if (!documento_tipo_codigo || !numero_documento || !razon_social) {
            this.showNotification('warning', 'Completa todos los campos obligatorios de cliente');
            return;
        }
        try {
            const url = "{{ route('clientes.store') }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    documento_tipo_codigo,
                    numero_documento,
                    razon_social
                })
            });

            const data = await response.json();

            if (response.ok && data.success && data.cliente) {
                // Asigna los datos al formulario principal
                document.getElementById('cliente_id').value = data.cliente.id;
                document.getElementById('cliente_razon_social').value = data.cliente.razon_social;

                // Cambia a la tab de "Buscar Cliente"
                new bootstrap.Tab(document.getElementById('nav-buscar-tab')).show();
                this.showNotification('success', 'Cliente registrado correctamente');
                document.getElementById('numero_documento').value = '';
                document.getElementById('razon_social').value = '';
            } 
            else if (response.status === 422) {
                this.handleFormErrors({ status: 422, data }); 
            } else {
                this.showNotification('error', data.message || 'Error al registrar cliente');
            }
        } catch (error) {
            this.showNotification('error', 'Error de red al registrar cliente');
            console.error(error);
        }
    }

 async showEditModal(id) {
    try {
        this.isEditing = true;

        const response = await this.fetchData(`${this.baseUrl}/${id}`);
        const data = response; // 👈 fetchData YA devuelve el objeto

        if (!data) {
            this.showNotification('error', 'No se encontraron datos para esta orden de servicio.');
            return;
        }

        this.resetForm();

        this.elements.modalTitle.textContent = 'Editar Orden de Servicio';
        this.elements.methodField.value = 'PUT';
        this.form.action = `${this.baseUrl}/${id}`;

        // =========================
        // DATOS PRINCIPALES
        // =========================
        document.getElementById('codigo_orden_servicio').value = data.codigo_orden_servicio ?? '';
        document.getElementById('cliente_id').value = data.cliente_id ?? '';
        document.getElementById('cliente_razon_social').value = data.cliente?.razon_social ?? '';

        // =========================
        // OT (MISMO MÉTODO QUE COTIZACIONES)
        // =========================
        const otSelect = document.getElementById('ot_codigo');

        if (otSelect) {
            otSelect.innerHTML = '<option value="">Seleccione</option>';

            if (data.ot_codigo) {
                otSelect.innerHTML += `
                    <option value="${data.ot_codigo}" selected>
                       ${data.ot_codigo} - ${data.ot_codigo_cliente} - ${data.ot_descripcion}
                    </option>
                `;
            }
        }

        // Descripción OT
        //document.getElementById('ot_descripcion').value = data.ot_descripcion ?? '';

        // =========================
        // RESTO DE CAMPOS
        // =========================
        document.getElementById('fecha_entrega').value = data.fecha_entrega ?? '';
        document.getElementById('forma_pago').value = data.forma_pago ?? '';
        document.getElementById('lugar_entrega').value = data.lugar_entrega ?? '';
        document.getElementById('descripcion').value = data.descripcion ?? '';
        document.getElementById('penalidad').value = data.penalidad ?? '';
        document.getElementById('monto').value = data.monto ?? '';
        document.getElementById('estado').value = data.estado ?? '';
        document.getElementById('observaciones').value = data.observaciones ?? '';
        document.getElementById('dias').value = data.dias ?? 0;

        const pdfInput = document.getElementById('archivo_pdf');
        const pdfRequired = document.getElementById('pdfRequired');
        const currentPdfLink = document.getElementById('currentPdfLink');
        pdfInput.required = false;
        pdfRequired?.classList.add('d-none');
        if (data.archivo_pdf_url) {
            currentPdfLink.href = data.archivo_pdf_url;
            currentPdfLink.classList.remove('d-none');
        } else {
            currentPdfLink.classList.add('d-none');
        }

        document.getElementById('forma_pago').dispatchEvent(new Event('change'));
        updateDiasState();

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

    const pdfInput = document.getElementById('archivo_pdf');
    const pdfRequired = document.getElementById('pdfRequired');
    const currentPdfLink = document.getElementById('currentPdfLink');
    pdfInput.required = true;
    pdfRequired?.classList.remove('d-none');
    currentPdfLink.classList.add('d-none');
    currentPdfLink.removeAttribute('href');

    // 🔥 LIMPIAR SELECT OT (CLAVE)
    const otSelect = document.getElementById('ot_codigo');
    if (otSelect) {
        otSelect.innerHTML = '<option value="">Seleccione</option>';
        otSelect.value = '';
    }
    document.getElementById('codigo_orden_servicio').focus();
}
}

document.addEventListener('DOMContentLoaded', () => {
    window.ordenesServicioManager = new OrdenesServicioManager();

});

document.getElementById('mnuMantenimiento').classList.add('menu-open');
document.getElementById('itemOrdenesServicio').classList.add('active');



function updateDiasState() {
    const forma = document.getElementById('forma_pago').value;
    const dias = document.getElementById('dias');

    if (forma === 'credito') {
        dias.disabled = false;
    } else {
        dias.disabled = true;
        dias.value = 0;
    }
}

document.getElementById('forma_pago').addEventListener('change', updateDiasState);

</script>
@endpush
