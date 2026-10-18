@extends('plantilla.app')
@push('estilos')
<style>
    #offcanvasServicios {
        z-index: 2000 !important;
    }

    .offcanvas-backdrop {
        z-index: 1999 !important;
    }

    #offcanvasTransporte {
        z-index: 2000 !important;
    }

    .offcanvas-backdrop {
        z-index: 1999 !important;
    }
    
    #offcanvasTpersonal {
        z-index: 2000 !important;
    }

    .offcanvas-backdrop {
        z-index: 1999 !important;
    }
    
</style>
@endpush
@section('contenido')
<div class="container-fluid cotizaciones-page py-3">
    <!-- Page Header / Hero -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fs-7 fw-semibold">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Módulo Comercial
                </span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-1 tracking-tight">Gestión de Cotizaciones</h1>
            <p class="text-muted small mb-0">Elaboración de propuestas comerciales, costeo de servicios y vinculación a Órdenes de Trabajo</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @can('cotizaciones_create')
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-3 fw-medium" id="btnCreate">
                <i class="bi bi-plus-lg fs-6"></i>
                <span>Nueva Cotización</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 dashboard-kpi-card stat-border-primary border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Total Cotizaciones</span>
                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_cotizaciones'] ?? 0 }}</h3>
                    <span class="badge bg-primary-subtle text-primary rounded-pill small">Registros</span>
                </div>
                <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-check2-circle text-primary me-1"></i> Histórico acumulado
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 dashboard-kpi-card stat-border-success border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Monto Total Cotizado</span>
                    <div class="p-2 bg-success-subtle text-success rounded-3">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark">S/ {{ number_format($stats['total_monto'] ?? 0, 2) }}</h3>
                </div>
                <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-graph-up-arrow text-success me-1"></i> Facturación estimada
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 dashboard-kpi-card stat-border-warning border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Cotizaciones Pendientes</span>
                    <div class="p-2 bg-warning-subtle text-warning rounded-3">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_pendientes'] ?? 0 }}</h3>
                    <span class="badge bg-warning-subtle text-warning rounded-pill small">Por Aprobar</span>
                </div>
                <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-clock-history text-warning me-1"></i> Requiere seguimiento
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 dashboard-kpi-card stat-border-info border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-medium">Aprobadas / Registradas</span>
                    <div class="p-2 bg-info-subtle text-info rounded-3">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-dark">{{ $stats['total_aprobadas'] ?? 0 }}</h3>
                    <span class="badge bg-info-subtle text-info rounded-pill small">Efectivas</span>
                </div>
                <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                    <i class="bi bi-shield-check text-info me-1"></i> Listas para ejecución OT
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm rounded-3 cotizaciones-card">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-table text-primary"></i>
                            Listado General de Cotizaciones
                        </h5>
                        <p class="text-muted small mb-0 mt-0.5">Explora, filtra, edita o exporta cotizaciones en formato PDF con o sin IGV</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive rounded-3 border">
                        <table id="listadoTable" class="table table-hover align-middle mb-0 w-100 mobile-records">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 135px;">Acciones</th>
                                    <th class="text-center" style="width: 65px;">N° Cot.</th>
                                    <th class="text-center" style="width: 95px;">Fecha</th>                                    
                                    <th>Cliente</th>
                                    <th>Sede</th>
                                    <th>Descripción OT</th>
                                    <th class="text-center">Cód. OT Cliente</th>
                                    <th class="text-center">Forma Pago</th>                                    
                                    <th class="text-end">Total (S/)</th>
                                    <th class="text-center">Estado</th>                
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
{{-- El modal debe existir siempre que la pantalla cargue. Los botones y las
     acciones del servidor continúan protegidos por sus permisos. --}}
@include('cotizaciones.action')
@include('servicios.addservices')
@include('transporte.addtransporte')
@include('tpersonal.addtpersonal')
@endsection
@push('scripts')
<script>  
    // ================================
// CONTADORES GLOBALES 
// ================================
let servicioIndex = 0;
let transporteIndex=0;
let tpersonalIndex=0;
let materialesIndex=0; 
let condicionIndex=0; 
class CotizacionesManager extends CrudManager {
    
    constructor() {
        super("{{ url('cotizaciones') }}");
        this.afterSuccess = this.handleCotizacionSuccess.bind(this);
        this.initializeDataTable();        

        this.elements.modal?.addEventListener('hidden.bs.modal', () => {
            this.resetForm();
        });
        

        //buscar el producto - material y agregarlo a la tabla
        this.setupLiveSearchSelect({
            inputId: 'producto_nombre',
            hiddenId: 'producto_id',
            url: "{{ route('productos.buscar') }}",
            template: (item) => {
                return `${item.codigo} - ${item.nombre} (S/ ${item.precio_venta})`;
            },
            getId: item => item.id,
            onSelect: (item) => this.addMaterialesToTable(item)
        });
        this.populateSelect('documento_tipo_codigo', '{{ route("documento-tipos.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );

        //buscar el transporte y agregar a la tabla
       this.setupLiveSearchSelect({
        inputId: 'transporte_nombre',
        hiddenId: 'transporte_id',
        url: "{{ route('transporte.buscar') }}",
        template: (item) => item.label, // usamos el campo que realmente devuelve el backend
        getId: item => item.id,
        onSelect: (item) => this.addTransporteToTable(item)
        });

       //buscar el transporte personal y agregar a la tabla
       this.setupLiveSearchSelect({
        inputId: 'tpersonal_nombre',
        hiddenId: 'tpersonal_id',
        url: "{{ route('tpersonal.buscar') }}",
        template: (item) => item.label, // usamos el campo que realmente devuelve el backend
        getId: item => item.id,
        onSelect: (item) => this.addTPersonalToTable(item)
        });

        // buscar el servicio y agregar a la tabla

       this.setupLiveSearchSelect({
        inputId: 'servicio_nombre',
        hiddenId: 'servicio_id',
        url: "{{ route('servicios.buscar') }}",
        template: (item) => {
                return `${item.id} - ${item.descripcion} (S/ ${item.precio})`;
            },
        getId: item => item.id,
        onSelect: (item) => this.addServiciosToTable(item)
        });


         //buscar la condicion y agregar a la tabla
       this.setupLiveSearchSelect({
        inputId: 'condicion_descripcion',
        hiddenId: 'condicion_id',
        url: "{{ route('condiciones.buscar') }}",
        template: (item) => {
                return `${item.id} - ${item.descripcion}`;
            },
        getId: item => item.id,
        onSelect: (item) => {
            // La selección ya es una confirmación: agregarla inmediatamente
            // para que no se pierda si el usuario guarda sin pulsar "Agregar".
            this.addCondicionesToTable(item);
        }
        });

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
             `{{ url('ordenestrabajo/por-cliente') }}/${cliente.id}?estado=2`,
            item => `<option value="${item.id}">${item.id} - ${item.codigo_ot_cliente} - ${item.descripcion}</option>`
                             );
        }
        

        });
        

        //document.getElementById('btnRegistrarCliente').addEventListener('click', () => this.registerClient());
 
    } 

    validateBeforeSubmit() {
        const condiciones = document.querySelectorAll('#tablaDetallesCondiciones tbody tr');

        if (condiciones.length === 0) {
            this.showNotification('warning', 'Debe seleccionar y agregar al menos una condición comercial');
            document.getElementById('condicion_descripcion')?.focus();
            document.getElementById('seccionCondiciones')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        return true;
    }
   

    evaluarVisibilidadModulo({ wrapId, tableId, chkId = null }) {
    const wrap = document.getElementById(wrapId);
    const chk = chkId ? document.getElementById(chkId) : null;

    let hasRows = false;

    if ($.fn.DataTable.isDataTable(tableId)) {
        const dt = $(tableId).DataTable();
        hasRows = dt.rows().count() > 0;

        if (!hasRows) {
            dt.clear().draw(false); // 🔥 LIMPIA EL DATATABLE
        }
    }

    if (hasRows) {
        wrap?.classList.remove('d-none');
        wrap && (wrap.style.display = 'block');
        if (chk) chk.checked = true;

        setTimeout(() => {
            $(tableId).DataTable().columns.adjust().draw(false);
        }, 50);

    } else {
        wrap?.classList.add('d-none');
        wrap && (wrap.style.display = 'none');
        if (chk) chk.checked = false;
    }
}

resetForm() {

    /* ===========================
     * 1️⃣ Reset formulario base
     * =========================== */
    if (this.form) {
        this.form.reset();
        this.form.querySelectorAll?.('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        this.form.querySelectorAll?.('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }

    /* ===========================
     * 2️⃣ Configuración módulos
     * =========================== */
    const config = [
        { chk: 'chkMateriales',  tabla: 'tablaDetallesMateriales',  seccion: 'seccionMateriales' },
        { chk: 'chkTPersonal',   tabla: 'tablaDetallesTPersonal',   seccion: 'seccionTPersonal' },
        { chk: 'chkServicio',    tabla: 'tablaDetallesServicios',   seccion: 'seccionServicios' },
        { chk: 'chkTransporte',  tabla: 'tablaDetallesTransporte',  seccion: 'seccionTransporte' },
        { chk: null,             tabla: 'tablaDetallesCondiciones', seccion: null } // fija
    ];

    config.forEach(item => {

        /* 🔹 Checkbox OFF */
        if (item.chk) {
            const chk = document.getElementById(item.chk);
            if (chk) chk.checked = false;
        }

        /* 🔹 Limpiar tabla */
        const tbody = document.querySelector(`#${item.tabla} tbody`);
        if (tbody) tbody.innerHTML = '';

        /* 🔹 OCULTAR SECCIÓN COMPLETA */
        if (item.seccion) {
            const sec = document.getElementById(item.seccion);
            if (sec) sec.style.display = 'none';
        }
    });

    /* ===========================
     * 3️⃣ Reset totales
     * =========================== */
    [
        'total_materiales',
        'total_tpersonal',
        'total_servicios',
        'total_transporte',
        'total'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '0.00';
    });

    /* ===========================
     * 4️⃣ Campos clave
     * =========================== */
    [
        'cliente_id',
        'cliente_razon_social',
        'ot_codigo'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    /* ===========================
     * 5️⃣ Estado interno
     * =========================== */
    this.isEditing = false;
}
//aqui termina el resetForm

 
    


    syncCheckboxAndTable(chkId, wrapId, tableId) {
    const chk = document.getElementById(chkId);
    const wrap = document.getElementById(wrapId);
    const table = document.getElementById(tableId);

    if (!chk || !wrap || !table) return;

    const tbody = table.querySelector('tbody');
    const hasRows = tbody && tbody.children.length > 0;

    // 🔒 regla absoluta
    if (chk.checked && hasRows) {
        wrap.classList.remove('d-none');
        } else {
        wrap.classList.add('d-none');
        }
    }





    async handleCotizacionSuccess(response, isEditing) {
        // Solo mostrar opción de ticket para ventas NUEVAS (no ediciones)
        if (!isEditing && response.venta_id) {
            setTimeout(() => {
                this.showTicketOption(response);
            }, 1000); // Esperar 1 segundo para que se vea la notificación de registro primero
        }
    }

    async showTicketOption(response){
        const result = await Swal.fire({
            title: '¡Cotizaciones registrada!',
            text: '¿Deseas imprimir el ticket?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Imprimir Ticket',
            cancelButtonText: 'Continuar',
            reverseButtons: true,
            timer: 8000,
            timerProgressBar: true
        });
        
        if (result.isConfirmed) {
            const imprimirRuta = "{{ route('cotizaciones.imprimir', ['id' => ':id']) }}";
            window.open(imprimirRuta.replace(':id', response.cotizaciones_id), '_blank');
        }
    }

    initEvents() {
        const inputs = document.querySelectorAll('.inputCantidad');
        inputs.forEach(input => {
            input.addEventListener('change', () => this.calculateMaterialTotals());
        });
    }

    addServiciosToTable(item, cantidad = 1, precio_unitario = null, subtotal = null) {
    const tbody = document.querySelector('#tablaDetallesServicios tbody');
    if (!tbody) return;

    // Limpiar búsqueda
    document.getElementById('servicio_id').value = '';
    document.getElementById('servicio_nombre').value = '';

    // 🔒 Evitar duplicados por servicio_id
    const existingRow = [...tbody.querySelectorAll('tr')]
        .find(row => row.dataset.servicioId == item.id);

    if (existingRow) {
        const inputCantidad = existingRow.querySelector('.inputCantidad');
        inputCantidad.value = parseInt(inputCantidad.value) + cantidad;
        this.calculateServicioTotals();
        return;
    }

    // 🔢 índice REAL (NO depende del DOM)
    const index = servicioIndex++;

    const precio = parseFloat(precio_unitario ?? item.precio_unitario ?? item.precio);
    const sub = subtotal ?? (precio * cantidad);

    const tr = document.createElement('tr');
    tr.dataset.servicioId = item.id;

    tr.innerHTML = `
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btnEliminarFila">
                <i class="bi bi-trash"></i>
            </button>
            <button type="button" class="btn btn-secondary btn-sm btnSubirFila ms-1" title="Subir">↑</button>
            <button type="button" class="btn btn-secondary btn-sm btnBajarFila ms-1" title="Bajar">↓</button>
        </td>

        <td class="text-center">${index + 1}</td>

        <td>${item.id} - ${item.descripcion ?? item.label ?? 'Servicio sin nombre'}</td>

        <td>
            <input type="number"
                   name="servicio_detalles[${index}][cantidad]"
                   value="${cantidad}"
                   min="1"
                   class="form-control form-control-sm inputCantidad">
        </td>

        <td>
            <input type="number"
                   name="servicio_detalles[${index}][precio]"
                   value="${precio.toFixed(2)}"
                   step="0.01"
                   class="form-control form-control-sm precio-unitario">
        </td>

        <td class="text-end">
            <input type="hidden"
                   name="servicio_detalles[${index}][subtotal]"
                   value="${sub.toFixed(2)}"
                   class="inputSubtotal">
            <span class="subtotal-text">${sub.toFixed(2)}</span>
        </td>

        <input type="hidden"
               name="servicio_detalles[${index}][servicio_id]"
               value="${item.id}">
    `;

    // Eventos
    tr.querySelector('.inputCantidad').addEventListener('input', () => this.calculateServicioTotals());
    tr.querySelector('.precio-unitario').addEventListener('input', () => this.calculateServicioTotals());
    tr.querySelector('.btnEliminarFila').addEventListener('click', () => {
    tr.remove();
    this.calculateServicioTotals();
});

// ✅ FLECHAS ORDEN SERVICIOS
tr.querySelector('.btnSubirFila').addEventListener('click', () => {
    const anterior = tr.previousElementSibling;
    if (anterior) tr.parentNode.insertBefore(tr, anterior);
});
tr.querySelector('.btnBajarFila').addEventListener('click', () => {
    const siguiente = tr.nextElementSibling;
    if (siguiente) tr.parentNode.insertBefore(siguiente, tr);
});

tbody.appendChild(tr);
this.calculateServicioTotals();
}

addCondicionesToTable(item) {

    const tbody = document.querySelector('#tablaDetallesCondiciones tbody');
    if (!tbody) return;

    // Limpiar búsqueda
    document.getElementById('condicion_id').value = '';
    document.getElementById('condicion_descripcion').value = '';

    // 🔒 Evitar duplicados
    const existe = [...tbody.querySelectorAll('tr')]
        .some(row => row.dataset.condicionId == item.id);

    if (existe) {
        alert('⚠️ Esta condición ya fue agregada');
        return;
    }

    const index = condicionIndex++;

    const tr = document.createElement('tr');
    tr.dataset.condicionId = item.id;

    tr.innerHTML = `
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btnEliminarFila">
                <i class="bi bi-trash"></i>
            </button>
            <button type="button" class="btn btn-secondary btn-sm btnSubirFila ms-1" title="Subir">↑</button>
            <button type="button" class="btn btn-secondary btn-sm btnBajarFila ms-1" title="Bajar">↓</button>
        </td>

        <td class="text-center">${index + 1}</td>

        <td>
            ${item.descripcion}
            <input type="hidden"
                   name="condicion_detalles[${index}][condicion_id]"
                   value="${item.id}">
        </td>
    `;

    tr.querySelector('.btnEliminarFila').addEventListener('click', () => {
    tr.remove();
});

// ✅ FLECHAS ORDEN CONDICIONES
tr.querySelector('.btnSubirFila').addEventListener('click', () => {
    const anterior = tr.previousElementSibling;
    if (anterior) tr.parentNode.insertBefore(tr, anterior);
});
tr.querySelector('.btnBajarFila').addEventListener('click', () => {
    const siguiente = tr.nextElementSibling;
    if (siguiente) tr.parentNode.insertBefore(siguiente, tr);
});

tbody.appendChild(tr);
}

calculateServicioTotals() {
    const tbody = document.querySelector('#tablaDetallesServicios tbody');
    if (!tbody) return;

    let total = 0;

    tbody.querySelectorAll('tr').forEach(row => {
        const cantidad = parseFloat(row.querySelector('.inputCantidad')?.value) || 0;
        const precio   = parseFloat(row.querySelector('.precio-unitario')?.value) || 0;

        const subtotal = cantidad * precio;

        const subtotalCell = row.querySelector('.subtotal');
        if (subtotalCell) subtotalCell.textContent = subtotal.toFixed(2);

        const subtotalInput = row.querySelector('.subtotal-input');
        if (subtotalInput) subtotalInput.value = subtotal.toFixed(2);

        total += subtotal;
    });

    // 🔹 total del módulo
    document.getElementById('totalservicios').value = total.toFixed(2);

    // 🔥 recalcular total general
    this.calculateTotals();
}




addMaterialesToTable(item, cantidad = 1, precioEdit = null, subtotalEdit = null) {

    const tbody = document.querySelector('#tablaDetallesMateriales tbody');
    if (!tbody) return;

    document.getElementById('producto_id').value = '';
    document.getElementById('producto_nombre').value = '';

    const existingRow = [...tbody.querySelectorAll('tr')]
        .find(row => row.dataset.productoId == item.id);

    if (existingRow) {

        const inputCantidad = existingRow.querySelector('.inputCantidad');
        inputCantidad.value = parseFloat(inputCantidad.value) + cantidad;

        this.calculateMaterialesTotals();
        return;
    }

    const rowCount = tbody.rows.length + 1;

    // 🔥 Precio base real (si viene de edición usa precio_base)
    const precioBase = parseFloat(
        item.precio_base ?? item.precio_venta ?? 0
    );

    // 🔥 Precio mostrado (si es edición usa el precio guardado)
    const precioMostrar = parseFloat(
        precioEdit ?? item.precio_venta ?? 0
    );

    const subtotal = subtotalEdit ?? (precioMostrar * cantidad);

    const tr = document.createElement('tr');
    tr.dataset.productoId = item.id;
    tr.dataset.precioBase = precioBase;
    tr.dataset.descuentoActivo = 0; // 🔥 NUEVO

    // 🔥 Descuentos seguros (si no existen → 0)
    tr.dataset.p5   = parseFloat(item.descuento_5 ?? 0);
    tr.dataset.p10  = parseFloat(item.descuento_10 ?? 0);
    tr.dataset.p50  = parseFloat(item.descuento_50 ?? 0);
    tr.dataset.p100 = parseFloat(item.descuento_100 ?? 0);

    const precio1_5 = precioBase - (precioBase * (parseFloat(item.descuento_5 ?? 0) / 100));
const precio6_10 = precioBase - (precioBase * (parseFloat(item.descuento_10 ?? 0) / 100));
const precio11_50 = precioBase - (precioBase * (parseFloat(item.descuento_50 ?? 0) / 100));
const precio50_100 = precioBase - (precioBase * (parseFloat(item.descuento_100 ?? 0) / 100));
    
    tr.innerHTML = `
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btnEliminarFila">
                <i class="bi bi-trash"></i>
            </button>
            <button type="button" class="btn btn-warning btn-sm btnAplicarDescuentoFila">
                <i class="bi bi-percent"></i>
            </button>
            <button type="button" class="btn btn-secondary btn-sm btnSubirFila mt-1" title="Subir">↑</button>
            <button type="button" class="btn btn-secondary btn-sm btnBajarFila mt-1" title="Bajar">↓</button>
        </td>

<td class="text-center">${rowCount}</td>

<td>${item.id} - ${item.nombre}</td>

<td>
    <input type="number"
           name="material_detalles[${rowCount}][cantidad]"
           value="${cantidad}"
           min="0.1"
           step="0.01"
           class="form-control form-control-sm inputCantidad">
</td>

<td class="text-end">
    <input type="number"
           name="material_detalles[${rowCount}][precio_venta]"
           value="${precioMostrar.toFixed(2)}"
           step="0.01"
           class="form-control form-control-sm precio-venta">
</td>

<td class="text-end">
    <div class="d-flex flex-column align-items-end">

        <input type="text"
               class="form-control form-control-sm text-end"
               value="${precio1_5.toFixed(2)}"
               readonly>

        <small class="badge rounded-pill bg-danger">
            - ${parseFloat(item.descuento_5 ?? 0)} %
        </small>

    </div>
</td>


<td class="text-end">
    <div class="d-flex flex-column align-items-end">

        <input type="text"
               class="form-control form-control-sm text-end"
               value="${precio6_10.toFixed(2)}"
               readonly>

        <small class="badge rounded-pill bg-primary">
            - ${parseFloat(item.descuento_10 ?? 0)} %
        </small>

    </div>
</td>

<td class="text-end">
    <div class="d-flex flex-column align-items-end">

        <input type="text"
               class="form-control form-control-sm text-end"
               value="${precio11_50.toFixed(2)}"
               readonly>

        <small class="badge rounded-pill bg-success">
            - ${parseFloat(item.descuento_50 ?? 0)} %
        </small>

    </div>
</td>


<td class="text-end">
    <div class="d-flex flex-column align-items-end">

        <input type="text"
               class="form-control form-control-sm text-end"
               value="${precio50_100.toFixed(2)}"
               readonly>

        <small class="badge rounded-pill bg-info">
            - ${parseFloat(item.descuento_100 ?? 0)} %
        </small>

    </div>
</td>

<td class="text-end subtotal">
    ${subtotal.toFixed(2)}
</td>

<input type="hidden"
       name="material_detalles[${rowCount}][producto_id]"
       value="${item.id}">
`;

    
    const self = this;

    // Eliminar
    tr.querySelector('.btnEliminarFila')
        .addEventListener('click', function () {
            tr.remove();
            self.calculateMaterialesTotals();
        });

    // Recalcular por cantidad
    tr.querySelector('.inputCantidad')
    .addEventListener('input', function () {

        const cantidadActual = parseFloat(this.value) || 0;
        const precioBase = parseFloat(tr.dataset.precioBase) || 0;

        const btn = tr.querySelector('.btnAplicarDescuentoFila');

        // 🔥 Si tenía descuento activo → quitarlo automáticamente
        if (tr.dataset.descuentoActivo == "1") {

            tr.dataset.descuentoActivo = 0;

            btn.classList.remove('btn-success');
            btn.classList.add('btn-warning');

            tr.querySelector('.precio-venta').value =
                precioBase.toFixed(2);
        }

        const precioActual =
            parseFloat(tr.querySelector('.precio-venta').value) || 0;

        tr.querySelector('.subtotal').textContent =
            (cantidadActual * precioActual).toFixed(2);

        self.calculateMaterialesTotals();
    });

    // Recalcular por precio manual
    tr.querySelector('.precio-venta')
        .addEventListener('input', function () {
            const cantidadActual = parseFloat(tr.querySelector('.inputCantidad').value) || 0;
            const precioActual = parseFloat(this.value) || 0;

            tr.querySelector('.subtotal').textContent =
                (cantidadActual * precioActual).toFixed(2);

            self.calculateMaterialesTotals();
        });

    // 🔥 DESCUENTO
   tr.querySelector('.btnAplicarDescuentoFila')
        .addEventListener('click', function (e) {
            e.preventDefault();
            self.aplicarDescuentoFila(tr);
        });

    // ✅ FLECHAS ORDEN MATERIALES
    tr.querySelector('.btnSubirFila').addEventListener('click', () => {
        const anterior = tr.previousElementSibling;
        if (anterior) tr.parentNode.insertBefore(tr, anterior);
    });
    tr.querySelector('.btnBajarFila').addEventListener('click', () => {
        const siguiente = tr.nextElementSibling;
        if (siguiente) tr.parentNode.insertBefore(siguiente, tr);
    });

    tbody.appendChild(tr);
    this.calculateMaterialesTotals();
}

aplicarDescuentoFila(tr) {

    const cantidad = parseFloat(tr.querySelector('.inputCantidad').value) || 0;
    const precioBase = parseFloat(tr.dataset.precioBase) || 0;

    const inputPrecio = tr.querySelector('.precio-venta');
    const btn = tr.querySelector('.btnAplicarDescuentoFila');

    if (cantidad <= 0 || precioBase <= 0) return;

    // 🔥 Si ya está aplicado → quitar descuento
    if (tr.dataset.descuentoActivo == "1") {

        inputPrecio.value = precioBase.toFixed(2);
        tr.dataset.descuentoActivo = 0;

        btn.classList.remove('btn-success');
        btn.classList.add('btn-warning');

        tr.querySelector('.subtotal').textContent =
            (precioBase * cantidad).toFixed(2);

        this.calculateMaterialesTotals();
        return;
    }

    // 🔥 Obtener escalas
    const p5   = parseFloat(tr.dataset.p5)   || 0;
    const p10  = parseFloat(tr.dataset.p10)  || 0;
    const p50  = parseFloat(tr.dataset.p50)  || 0;
    const p100 = parseFloat(tr.dataset.p100) || 0;

    let porcentaje = 0;    

if (cantidad >= 51) {
    porcentaje = p100;
} 
else if (cantidad >= 11) {
    porcentaje = p50;
} 
else if (cantidad >= 6) {
    porcentaje = p10;
} 
else {
    porcentaje = p5;
}

    if (porcentaje <= 0) return;

    const nuevoPrecio =
        precioBase - (precioBase * porcentaje / 100);

    inputPrecio.value = nuevoPrecio.toFixed(2);

    tr.dataset.descuentoActivo = 1;

    btn.classList.remove('btn-warning');
    btn.classList.add('btn-success');

    tr.querySelector('.subtotal').textContent =
        (nuevoPrecio * cantidad).toFixed(2);

    this.calculateMaterialesTotals();
}

calculateMaterialesTotals() {
    const tbody = document.querySelector('#tablaDetallesMateriales tbody');
    if (!tbody) return;

    let total = 0;

    tbody.querySelectorAll('tr').forEach(row => {
        const cantidad = parseFloat(row.querySelector('.inputCantidad')?.value) || 0;
        const precio   = parseFloat(row.querySelector('.precio-venta')?.value) || 0;

        const subtotal = cantidad * precio;

        const subtotalCell = row.querySelector('.subtotal');
        if (subtotalCell) subtotalCell.textContent = subtotal.toFixed(2);

        total += subtotal;
    });

    document.getElementById('totalmateriales').value = total.toFixed(2);

    // 🔥 CLAVE
    this.calculateTotals();
}





    
    addTransporteToTable(item, cantidad = 1, precio_unitario = null, subtotal = null) {
    const tbody = document.querySelector('#tablaDetallesTransporte tbody');
    if (!tbody) return;

    document.getElementById('transporte_id').value = '';
    document.getElementById('transporte_nombre').value = '';

    const existingRow = [...tbody.querySelectorAll('tr')].find(row => row.dataset.transporteId == item.id);
    if (existingRow) {
        const inputCantidad = existingRow.querySelector('.inputCantidad');
        const nuevaCantidad = parseInt(inputCantidad.value) + cantidad;
        inputCantidad.value = nuevaCantidad;

        const precio = parseFloat(existingRow.querySelector('.precio-unitario').value);
        existingRow.querySelector('.subtotal').textContent = (precio * nuevaCantidad).toFixed(2);
    } else {
        const rowCount = tbody.rows.length + 1;
        const precio = parseFloat(precio_unitario ?? item.precio);
        const sub = subtotal ?? (precio * cantidad);

        const tr = document.createElement('tr');
        tr.dataset.transporteId = item.id;

        tr.innerHTML = `
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btnEliminarFila">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            <td class="text-center">${rowCount}</td>
            <td>${item.label}</td>
            <td>
                <input type="number" name="transporte_detalles[${rowCount}][cantidad]" value="${cantidad}" min="1" class="form-control form-control-sm inputCantidad">
            </td>
           
            <td class="text-end">
                    <input type="number" name="transporte_detalles[${rowCount}][precio]" value="${precio.toFixed(2)}" min="1" step="0.01" class="form-control form-control-sm precio-unitario">
                    
                </td>

            <td class="text-end subtotal">${sub.toFixed(2)}</td>

            <input type="hidden" name="transporte_detalles[${rowCount}][transporte_id]" value="${item.id}">
        `;

        tr.querySelector('.inputCantidad').addEventListener('change', () => this.calculateTransporteTotals());
        tr.querySelector('.precio-unitario').addEventListener('change', () => this.calculateTransporteTotals());
        tr.querySelector('.btnEliminarFila').addEventListener('click', () => { tr.remove(); this.calculateTransporteTotals(); });
        tbody.appendChild(tr);
    }

    this.calculateTransporteTotals();
}


calculateTransporteTotals() {
    const tbody = document.querySelector('#tablaDetallesTransporte tbody');
    if (!tbody) return;

    let total = 0;

    tbody.querySelectorAll('tr').forEach(row => {
        const cantidad = parseFloat(row.querySelector('.inputCantidad')?.value) || 0;
        const precio   = parseFloat(row.querySelector('.precio-unitario')?.value) || 0;

        const subtotal = cantidad * precio;

        const subtotalCell = row.querySelector('.subtotal');
        if (subtotalCell) subtotalCell.textContent = subtotal.toFixed(2);

        total += subtotal;
    });

    // 🔹 total del módulo
    document.getElementById('totaltransporte').value = total.toFixed(2);

    // 🔥 recalcular total general
    this.calculateTotals();
}


//transporte de personal 
addTPersonalToTable(item, cantidad = 1, precio_unitario = null, subtotal = null) {
    const tbody = document.querySelector('#tablaDetallesTPersonal tbody');
    if (!tbody) return;

    document.getElementById('tpersonal_id').value = '';
    document.getElementById('tpersonal_nombre').value = '';

    const existingRow = [...tbody.querySelectorAll('tr')].find(row => row.dataset.transporteId == item.id);
    if (existingRow) {
        const inputCantidad = existingRow.querySelector('.inputCantidad');
        const nuevaCantidad = parseInt(inputCantidad.value) + cantidad;
        inputCantidad.value = nuevaCantidad;

        const precio = parseFloat(existingRow.querySelector('.precio-unitario').value);
        existingRow.querySelector('.subtotal').textContent = (precio * nuevaCantidad).toFixed(2);
    } else {
        const rowCount = tbody.rows.length + 1;
        const precio = parseFloat(precio_unitario ?? item.precio);
        const sub = subtotal ?? (precio * cantidad);

        const tr = document.createElement('tr');
        tr.dataset.transporteId = item.id;

        tr.innerHTML = `
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btnEliminarFila">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            <td class="text-center">${rowCount}</td>
            <td>${item.label}</td>
            <td>
                <input type="number" name="tpersonal_detalles[${rowCount}][cantidad]" value="${cantidad}" min="1" class="form-control form-control-sm inputCantidad">
            </td>
           
            <td class="text-end">
                    <input type="number" name="tpersonal_detalles[${rowCount}][precio]" value="${precio.toFixed(2)}" min="1" step="0.01" class="form-control form-control-sm precio-unitario">
                    
                </td>

            <td class="text-end subtotal">${sub.toFixed(2)}</td>

            <input type="hidden" name="tpersonal_detalles[${rowCount}][tpersonal_id]" value="${item.id}">
        `;

        tr.querySelector('.inputCantidad').addEventListener('change', () => this.calculateTPersonalTotals());
        tr.querySelector('.precio-unitario').addEventListener('change', () => this.calculateTPersonalTotals());
        tr.querySelector('.btnEliminarFila').addEventListener('click', () => { tr.remove(); this.calculateTPersonalTotals(); });
        tbody.appendChild(tr);
    }

    this.calculateTPersonalTotals();
}

calculateTPersonalTotals() {
    const tbody = document.querySelector('#tablaDetallesTPersonal tbody');
    if (!tbody) return;

    let total = 0;

    tbody.querySelectorAll('tr').forEach(row => {
        const cantidad = parseFloat(row.querySelector('.inputCantidad')?.value) || 0;
        const precio   = parseFloat(row.querySelector('.precio-unitario')?.value) || 0;

        const subtotal = cantidad * precio;

        const subtotalCell = row.querySelector('.subtotal');
        if (subtotalCell) subtotalCell.textContent = subtotal.toFixed(2);

        total += subtotal;
    });

    // 🔹 total del módulo
    document.getElementById('totaltpersonal').value = total.toFixed(2);

    // 🔥 recalcular total general
    this.calculateTotals();
}


calculateTotals() {

    // 🔹 Totales por módulo
    const totalMateriales = parseFloat(document.getElementById('totalmateriales')?.value) || 0;
    const totalServicios  = parseFloat(document.getElementById('totalservicios')?.value) || 0;
    const totalTransporte = parseFloat(document.getElementById('totaltransporte')?.value) || 0;
    const totalTPersonal  = parseFloat(document.getElementById('totaltpersonal')?.value) || 0;

    // 🔹 Total general
    const totalGeneral = totalMateriales + totalServicios + totalTransporte + totalTPersonal;

    // 🔹 Configuración IGV
    const porcentajeIGV = 0.18;

    // 🔹 Cálculo fiscal (TOTAL INCLUYE IGV)
    const op_gravada = totalGeneral / (1 + porcentajeIGV);
    const impuesto   = totalGeneral - op_gravada;

    // 🔹 Mostrar resultados
    document.getElementById('op_gravada').value   = op_gravada.toFixed(2);
    document.getElementById('op_exonerada').value = '0.00';
    document.getElementById('op_inafecta').value  = '0.00';
    document.getElementById('impuesto').value     = impuesto.toFixed(2);
    document.getElementById('total').value        = totalGeneral.toFixed(2);
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
                { data: 'fecha', name: 'fecha' },                
                { data: 'cliente', name: 'cliente.razon_social' },
                { data: 'sede', name: 'ordenTrabajo.sedes.nombre' }, 
                { data: 'descripcion_ot', name: 'ordenTrabajo.descripcion' },  
                { data: 'codigo_ot_cliente', name: 'ordenTrabajo.codigo_ot_cliente' },
                { data: 'forma_pago', name: 'forma_pago' },                         
                //{ data: 'tipo_comprobante', name: 'comprobante_tipos.descripcion' },
                //{ data: 'tipo_comprobante', name: 'comprobante_tipos.descripcion' },
                //{ data: 'tipo_comprobante', name: 'tipo_comprobante' },
                //{ data: 'serie', name: 'serie' },
                //{ data: 'correlativo', name: 'correlativo' },
                { data: 'total', name: 'total' },
                { data: 'estado', name: 'estado' },
                //{ data: 'observaciones', name: 'observaciones' }
            ],
            columnDefs: [
                { targets: 0, width: '135px', className: 'text-center', orderable: false, searchable: false },
                { targets: 1, width: '65px', className: 'text-center fw-bold text-dark' },
                { targets: 2, width: '95px', className: 'text-center' },
                { targets: 6, className: 'text-center' },
                { targets: 7, className: 'text-center' },
                { targets: 8, className: 'text-end' },
                { targets: 9, className: 'text-center' }
            ],
            order: [[1, 'desc']],
            responsive: true
        });
    }

    showCreateModal(){
        if (!this.elements.modal || !this.form || !this.modal) {
            this.showNotification('error', 'No se pudo cargar el formulario de cotización. Actualice la página.');
            return;
        }

        this.isEditing = false;
        this.resetForm();
        const modalTitle = this.elements.modalTitle || document.getElementById('modalTitle');
        if (modalTitle) modalTitle.textContent = 'Nueva cotización';
        if (this.elements.methodField) this.elements.methodField.value = 'POST';
        this.form.action = this.baseUrl;

        const otSelect = document.getElementById('ot_codigo');
        if (otSelect) otSelect.value = '';

        this.modal.show();
    }

    activarModulo({ wrapId, tableId = null, chkId = null }) {

    // 🔹 Mostrar wrapper
    const wrap = document.getElementById(wrapId);
    if (wrap) {
        wrap.classList.remove('d-none');
        wrap.style.display = 'block';
    }

    // 🔹 Activar checkbox si existe
    if (chkId) {
        const chk = document.getElementById(chkId);
        if (chk && !chk.checked) {
            chk.checked = true;

            // 🔥 Dispara lógica existente
            chk.dispatchEvent(new Event('change'));
        }
    }

    // 🔹 Ajustar DataTable si aplica
    if (tableId && $.fn.DataTable.isDataTable(tableId)) {
        setTimeout(() => {
            $(tableId).DataTable().columns.adjust().draw(false);
        }, 50);
    }
}



async showEditModal(id) {
    try {

        if (!this.elements.modal || !this.form || !this.modal) {
            this.showNotification('error', 'No se pudo cargar el formulario de cotización. Verifique action.blade.php.');
            return;
        }

         this.isEditing = true;

        const response = await this.fetchData(`${this.baseUrl}/${id}`);
        const data = response.data ?? response;

        if (!data) {
            this.showNotification('error', 'No se encontraron datos para esta cotización.');
            return;
        }

       
        // SOLO limpiar antes de cargar datos
        this.resetForm();
        this.isEditing = true;



        // 🔹 Configuración del modal
        const modalTitle = this.elements.modalTitle || document.getElementById('modalTitle');
        if (modalTitle) modalTitle.textContent = 'Editar Cotización';

        if (this.elements.methodField) this.elements.methodField.value = 'PUT';
        this.form.action = `${this.baseUrl}/${id}`;

        // 🔹 Campos principales
        document.getElementById('forma_pago').value = data.forma_pago ?? '';
        //document.getElementById('validez').value = data.validez ?? '';
        //document.getElementById('ot_codigo').value = data.ot_codigo ?? '';
        document.getElementById('estado').value = data.estado ?? '';
        document.getElementById('observaciones').value = data.observaciones ?? '';
        //document.getElementById('garantia').value = data.garantia ?? '';
        //document.getElementById('tiempo_entrega').value = data.tiempo_entrega ?? '';

        // 🔹 Cliente
        document.getElementById('cliente_id').value = data.cliente_id ?? '';
        document.getElementById('cliente_razon_social').value = data.cliente ?? '';


        // =========================
        // 🔹 OT (solo si existe)
        // =========================
        const otSelect = document.getElementById('ot_codigo');

    if (otSelect) {
            otSelect.innerHTML = '<option value="">Seleccione</option>';

            if (data.ot_codigo) {
                    otSelect.innerHTML += `
                        <option value="${data.ot_codigo}" selected>
                        ${data.ot_codigo} -  ${data.ot_cliente}
                        </option>
                        `;
            }
    }
        // 🔹 Usuario (si existe el campo)
        const usuarioInput = document.getElementById('usuario_nombre');
        if (usuarioInput) {
            usuarioInput.value = data.user ?? '';
        }

        // 🔹 Limpiar tablas (sin función inexistente)
       // document.querySelector('#tablaDetallesMateriales tbody')?.replaceChildren();
       // document.querySelector('#tablaDetallesServicios tbody')?.replaceChildren();
       // document.querySelector('#tablaDetallesManoObra tbody')?.replaceChildren();
       //document.querySelector('#tablaDetallesTransportes tbody')?.replaceChildren();

        // =========================
        // 🔹 MATERIALES / PRODUCTOS
        // =========================
        // 🔹 Activar materiales     
        if (data.productos?.length) {
        this.activarModulo({
        wrapId: 'wrapMateriales',
        tableId: '#tablaDetallesMateriales',
        chkId: 'chkMateriales'
        });

        data.productos.forEach(detalle => {
    this.addMaterialesToTable(
        {
            id: detalle.producto_id,
            nombre: detalle.nombre,
            precio_venta: detalle.precio_base ?? detalle.precio_venta,
            precio_base: detalle.precio_base ?? detalle.precio_venta,
            descuento_5: detalle.descuento_5 ?? 0,
            descuento_10: detalle.descuento_10 ?? 0,
            descuento_50: detalle.descuento_50 ?? 0,
            descuento_100: detalle.descuento_100 ?? 0
        },
        Number(detalle.cantidad),
        Number(detalle.precio_venta),   // precio guardado en la cotización
        Number(detalle.subtotal)
        );
    });
        }


        // =========================
        // 🔹 TRANSPORTE DE PERSONAL
        // =========================
        if (data.tpersonal?.length) {
        this.activarModulo({
        wrapId: 'wrapTPersonal',
        tableId: '#tablaDetallesTPersonal',
        chkId: 'chkTPersonal'
        });

        data.tpersonal.forEach(detalle => {
        this.addTPersonalToTable(
        { id: detalle.tpersonal_id, label: detalle.name },
        Number(detalle.cantidad),
        Number(detalle.precio_unitario),
        Number(detalle.subtotal)
        );
            });
        }

        // =========================
        // 🔹 SERVICIOS
        // =========================
        if (data.servicios?.length) {
        this.activarModulo({
        wrapId: 'wrapServicios',
        tableId: '#tablaDetallesServicios',
        chkId: 'chkServicio'
        });

        data.servicios.forEach(detalle => {
        this.addServiciosToTable(
        { id: detalle.servicio_id, descripcion: detalle.descripcion },
        Number(detalle.cantidad),
        Number(detalle.precio_unitario),
        Number(detalle.subtotal)
        );
            });
        }        

        // =========================
        // 🔹 CONDICIONES (SECCIÓN FIJA)
        // =========================
        if (data.condiciones?.length) {
        data.condiciones.forEach(detalle => {
        this.addCondicionesToTable({
            id: detalle.condicion_id,
            descripcion: detalle.descripcion
                });
            });
        }       


        // =========================
        // 🔹 TRANSPORTE DE HERRAMIENTAS
        // =========================
        if (data.transporte?.length) {
        this.activarModulo({
        wrapId: 'wrapTransporte',
        tableId: '#tablaDetallesTransporte',
        chkId: 'chkTransporte'
        });

        data.transporte.forEach(detalle => {
        this.addTransporteToTable(
        { id: detalle.transporte_id, label: detalle.name },
        Number(detalle.cantidad),
        Number(detalle.precio_unitario),
        Number(detalle.subtotal)
        );
            });
        }    


        // =========================
        // 🔹 RECÁLCULO DE TOTALES
        // =========================
        this.calculateMaterialesTotals();       
        this.calculateServicioTotals();
        this.calculateTransporteTotals();
        this.calculateTPersonalTotals();

        // 🔹 Total general
        if (document.getElementById('total')) {
            document.getElementById('total').value = parseFloat(data.total ?? 0).toFixed(2);
        }

        // =========================
        // 🔹 VISIBILIDAD FINAL DE MÓDULOS
    // =========================
    this.evaluarVisibilidadModulo({
    wrapId: 'wrapMateriales',
    tableId: '#tablaDetallesMateriales',
    chkId: 'chkMateriales'
    });

    this.evaluarVisibilidadModulo({
    wrapId: 'wrapServicios',
    tableId: '#tablaDetallesServicios',
    chkId: 'chkServicio'
    });

    this.evaluarVisibilidadModulo({
    wrapId: 'wrapTPersonal',
    tableId: '#tablaDetallesTPersonal',
    chkId: 'chkTPersonal'
    });

    this.evaluarVisibilidadModulo({
    wrapId: 'wrapTransporte',
    tableId: '#tablaDetallesTransporte',
    chkId: 'chkTransporte'
    });


        // 🔹 Mostrar modal
        this.modal.show();
        
    } catch (error) {
        console.error('❌ Error completo showEditModal:', error);
        const detalle = error?.message ? ` (${error.message})` : '';
        this.showNotification('error', `Error al cargar los datos de la cotización${detalle}`);
    }
    }


    focusFirstField() {
        document.getElementById('producto_nombre').focus();
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
   

}


document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | OFFCANVAS: SERVICIOS, TRANSPORTE, TPERSONAL
    |--------------------------------------------------------------------------
    */

    const offcanvasIds = [
        'offcanvasServicios',
        'offcanvasTransporte',
        'offcanvasTpersonal'
    ];

    offcanvasIds.forEach(id => {

        const offcanvas = document.getElementById(id);
        if (!offcanvas) return;

        // Cuando se abre el offcanvas
        offcanvas.addEventListener('shown.bs.offcanvas', function () {

            // Cargar unidades en todos
            populateUnidadSelect(id);

            // Solo Transporte y Tpersonal usan ubigeo
            if (id === 'offcanvasTransporte' || id === 'offcanvasTpersonal') {
                initUbigeo(id);
            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | BOTONES PARA ABRIR OFFCANVAS
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (e) {

        const btnServicio   = e.target.closest('#btnNuevoServicio');
        const btnTransporte = e.target.closest('#btnNuevoTransporte');
        const btnTpersonal  = e.target.closest('#btnNuevoTpersonal');

        if (btnServicio) {
            bootstrap.Offcanvas
                .getOrCreateInstance(document.getElementById('offcanvasServicios'))
                .show();
        }

        if (btnTransporte) {
            bootstrap.Offcanvas
                .getOrCreateInstance(document.getElementById('offcanvasTransporte'))
                .show();
        }

        if (btnTpersonal) {
            bootstrap.Offcanvas
                .getOrCreateInstance(document.getElementById('offcanvasTpersonal'))
                .show();
        }

    });


    /*
    |--------------------------------------------------------------------------
    | SUBMIT AJAX PARA LOS 3 FORMULARIOS
    |--------------------------------------------------------------------------
    */

    document.addEventListener('submit', function (e) {

        const form = e.target;

        if (!form.matches('#formServicio, #formTransporte, #formTpersonal')) return;

        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';

            if (!contentType.includes('application/json')) {
                throw new Error(`El servidor devolvió una respuesta no válida (HTTP ${response.status})`);
            }

            const data = await response.json();

            if (!response.ok) {
                const validationMessage = data.errors
                    ? Object.values(data.errors).flat()[0]
                    : null;
                throw new Error(validationMessage || data.message || `Error HTTP ${response.status}`);
            }

            return data;
        })
        .then(data => {

            if (data.success) {

                alert('Registro guardado correctamente');
                form.reset();

                const offcanvasEl = form.closest('.offcanvas');
                if (offcanvasEl) {
                    bootstrap.Offcanvas.getInstance(offcanvasEl).hide();
                }

            } else {
                console.error(data);
                alert('Error al guardar');
            }

        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'No se pudo guardar el registro');
        });

    });


    /*
    |--------------------------------------------------------------------------
    | Inicializaciones generales
    |--------------------------------------------------------------------------
    */

    new CotizacionesManager();

    document.getElementById('mnuVenta')?.classList.add('menu-open');
    document.getElementById('itemCotizaciones')?.classList.add('active');

});



/*
|--------------------------------------------------------------------------
| FUNCIÓN GLOBAL PARA CARGAR UNIDADES
|--------------------------------------------------------------------------
*/

function populateUnidadSelect(offcanvasId) {

    const select = document.querySelector(`#${offcanvasId} select[name="unidad_codigo"]`);
    if (!select) return;

    fetch('{{ route("unidades.select") }}')
        .then(response => response.json())
        .then(data => {

            select.innerHTML = '<option value="">Seleccione...</option>';

            data.forEach(item => {
                select.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${item.codigo}">
                        ${item.codigo} - ${item.descripcion}
                    </option>`
                );
            });

        })
        .catch(error => console.error('Error cargando unidades:', error));
}



/*
|--------------------------------------------------------------------------
| UBIGEO REUTILIZABLE (TRANSPORTE Y TPERSONAL)
|--------------------------------------------------------------------------
*/

function initUbigeo(offcanvasId) {

    const container = document.getElementById(offcanvasId);
    if (!container) return;

    const departmentSelect = container.querySelector('[name="department_id"]');
    const provinceSelect   = container.querySelector('[name="province_id"]');
    const districtSelect   = container.querySelector('[name="districts_id"]');

    if (!departmentSelect) return;

    // Limpiar selects al abrir
    provinceSelect.innerHTML = '<option value="">Seleccione provincia</option>';
    districtSelect.innerHTML = '<option value="">Seleccione distrito</option>';

    // 🔹 Cargar departamentos
    fetch('{{ route("departamentos.select") }}')
        .then(res => res.json())
        .then(data => {

            departmentSelect.innerHTML = '<option value="">Seleccione departamento</option>';

            data.forEach(item => {
                departmentSelect.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${item.id.toString().padStart(2, '0')}">
                        ${item.id} - ${item.name}
                    </option>`
                );
            });

        });


    // 🔹 Cambio de departamento
    departmentSelect.onchange = function () {

        const departmentId = this.value;

        provinceSelect.innerHTML = '<option value="">Cargando provincias...</option>';
        districtSelect.innerHTML = '<option value="">Seleccione provincia primero</option>';

        if (!departmentId) return;

        fetch(`{{ url('provincias/por-departamento') }}/${departmentId}`)
            .then(res => res.json())
            .then(data => {

                provinceSelect.innerHTML = '<option value="">Seleccione provincia</option>';

                data.forEach(item => {
                    provinceSelect.insertAdjacentHTML(
                        'beforeend',
                        `<option value="${item.id.toString().padStart(4, '0')}">
                            ${item.name}
                        </option>`
                    );
                });

            });

    };


    // 🔹 Cambio de provincia
    provinceSelect.onchange = function () {

        const provinceId = this.value;

        districtSelect.innerHTML = '<option value="">Cargando distritos...</option>';

        if (!provinceId) return;

        fetch(`{{ url('distritos/por-provincia') }}/${provinceId}`)
            .then(res => res.json())
            .then(data => {

                districtSelect.innerHTML = '<option value="">Seleccione distrito</option>';

                data.forEach(item => {
                    districtSelect.insertAdjacentHTML(
                        'beforeend',
                        `<option value="${item.id.toString().padStart(6, '0')}">
                            ${item.name}
                        </option>`
                    );
                });

            });

    };
}
</script>
@endpush
