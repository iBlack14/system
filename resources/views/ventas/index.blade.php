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
                    <h3 class="card-title flex-grow-1">Ventas</h3>
                    @can('ventas_create')
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
                                    <th>Fecha</th>                                    
                                    <th>Cliente</th>
                                    <th>OT Codigo</th>
                                    <th>Serie</th>
                                    <th> Correlativo</th>
                                    <th>Forma Pago</th>                                    
                                    <th>Total</th>
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
@canany(['ventas_create', 'ventas_edit'])
    @include('ventas.action')
@endcanany
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
class VentasManager extends CrudManager {
    
    constructor() {
        super("{{ url('ventas') }}");
        this.afterSuccess = this.handleVentasSuccess.bind(this);
        this.initializeDataTable();

        this.elements.modal.addEventListener('hidden.bs.modal', () => {
            this.resetForm();
        });
        
         this.populateSelect('documento_tipo_codigo', '{{ route("documento-tipos.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );

        this.populateSelect('comprobante_tipo_codigo', '{{ route("comprobante-tipos.select") }}', item =>
            `<option value="${item.codigo}">${item.codigo} - ${item.descripcion}</option>`
        );
       

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

        //buscar el servicio y agregar a la tabla
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

       this.setupLiveSearchSelect({
        inputId: 'cliente_razon_social',
        hiddenId: 'cliente_id',
        url: "{{ route('clientes.buscar') }}",
        template: (item) => `${item.numero_documento} - ${item.razon_social}`,
        getId: item => item.id,
        
        onSelect: (item) => {

        document.getElementById('cliente_id').value = item.id;
        document.getElementById('cliente_razon_social').value = item.razon_social;

        /* OT del cliente */
        const otSelect = document.getElementById('ot_codigo');
        if (otSelect) {
            otSelect.innerHTML = '<option value="">Seleccione OT</option>';

            this.populateSelect(
                'ot_codigo',
                `{{ url('ordenes-trabajo/por-cliente') }}/${item.id}`,
                ot => `<option value="${ot.codigo}">${ot.codigo} - ${ot.codigo_ot_cliente}</option>`
            );
        }


        /* Cotizaciones del cliente */
        const cotSelect = document.getElementById('cotizacion_id');
        if (cotSelect) {
            cotSelect.innerHTML = '<option value="">Seleccione cotización</option>';

            this.populateSelect(
            'cotizacion_id',
            `{{ url('cotizaciones/por-cliente') }}/${item.id}?estado=9`,
            cot => `<option value="${cot.id}">COT-${cot.id} - OT ${cot.orden_trabajo.codigo_ot_cliente} - ${cot.orden_trabajo.descripcion}</option>`
            );
        }

    }
});

        // 🔥 EVENTO PARA CARGAR COTIZACIÓN
        const cotizacionSelect = document.getElementById('cotizacion_id');

            if (cotizacionSelect) {
                cotizacionSelect.addEventListener('change', (e) => {
                    const cotizacionId = e.target.value;

                // 🔥 SI CAMBIA O SE BORRA → LIMPIAR TODO
                this.clearDetallesCotizacion();

                if (!cotizacionId) {
                console.log('⚠️ Cotización vacía → formulario limpio');
                return;
                }

                // 🚀 CARGAR NUEVA
                this.loadCotizacion(cotizacionId);
                        });
            }
       
        

           // Evento para el select de comprobante_tipo_codigo
        const selectComprobante = document.getElementById('comprobante_tipo_codigo');
        if (selectComprobante) {
            selectComprobante.addEventListener('change', (e) => {
                this.getSerie(e.target.value); // Ahora sí llama al método de la clase
            });
        }

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
    if (this.form) this.form.reset();
    this.clearFormErrors?.();


    /* ===========================
     * 2️⃣ Configuración módulos
     * =========================== */
    const config = [
        { chk: 'chkMateriales',  tabla: 'tablaDetallesMateriales',  seccion: 'seccionMateriales' },
        { chk: 'chkTPersonal',   tabla: 'tablaDetallesTPersonal',   seccion: 'seccionTPersonal' },
        { chk: 'chkServicio',    tabla: 'tablaDetallesServicios',   seccion: 'seccionServicios' },
        { chk: 'chkTransporte',  tabla: 'tablaDetallesTransporte',  seccion: 'seccionTransporte' },
        //{ chk: null,             tabla: 'tablaDetallesCondiciones', seccion: null } // fija
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
        'ot_codigo',
        'cotizacion_id'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    }); 

    /* ===========================
     * 6️⃣ Estado interno
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


    async handleVentasSuccess(response, isEditing) {
        // Solo mostrar opción de ticket para ventas NUEVAS (no ediciones)
        if (!isEditing && response.ventas_id) {
            setTimeout(() => {
                this.showTicketOption(response);
            }, 1000); // Esperar 1 segundo para que se vea la notificación de registro primero
        }
    }

    

        clearDetallesCotizacion() {

        // 🔥 RESET DE ÍNDICES (CLAVE)
        servicioIndex = 0;
        transporteIndex = 0;
        tpersonalIndex = 0;
        materialesIndex = 0;

        const modulos = [
            { chk: 'chkMateriales', tabla: 'tablaDetallesMateriales', wrap: 'wrapMateriales' },
            { chk: 'chkTPersonal',  tabla: 'tablaDetallesTPersonal',  wrap: 'wrapTPersonal' },
            { chk: 'chkServicio',   tabla: 'tablaDetallesServicios',  wrap: 'wrapServicios' },
            { chk: 'chkTransporte', tabla: 'tablaDetallesTransporte', wrap: 'wrapTransporte' }
        ];

        modulos.forEach(m => {

            // 🔹 checkbox OFF
            const chk = document.getElementById(m.chk);
            if (chk) {
                chk.checked = false;
                chk.dispatchEvent(new Event('change')); // 🔥 FORZAR OCULTAR
            }

            // 🔹 limpiar tabla
            const tbody = document.querySelector(`#${m.tabla} tbody`);
            if (tbody) tbody.innerHTML = '';

            // 🔹 ocultar wrapper SIEMPRE
            const wrap = document.getElementById(m.wrap);
            if (wrap) {
                wrap.classList.add('d-none');
                wrap.style.display = 'none';
            }
        });

    // 🔹 reset totales CORRECTOS (ojo nombres)
    [
        'totalmateriales',
        'totalservicios',
        'totaltransporte',
        'totaltpersonal',
        'total'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '0.00';
    });
}                                         




    async loadCotizacion(cotizacionId) {
    if (!cotizacionId) return;

    try {
        const response = await fetch(`{{ url('cotizaciones') }}/${cotizacionId}`);
        const result = await response.json();

        if (!result.success) {
            console.error('❌ Error al obtener cotización');
            return;
        }

        const data = result.data;

        // 🔥 LIMPIAR TODO antes de cargar
        this.clearDetallesCotizacion();

        // =========================
        // 🔹 DATOS PRINCIPALES
        // =========================
        document.getElementById('cliente_id').value = data.cliente_id ?? '';
        document.getElementById('cliente_razon_social').value = data.cliente ?? '';

        // OT
        const otSelect = document.getElementById('ot_codigo');
        if (otSelect && data.ot_codigo) {

            const otCodigo = data.ot_codigo;

            otSelect.innerHTML = `
            <option value="${otCodigo}" selected>
            ${otCodigo} - ${data.ot_cliente ?? ''}
            </option>
            `;

            // 🔥 CLAVE: guardar en hidden
            document.getElementById('ot_codigo_hidden').value = otCodigo;
        }

        // =========================
        // 🔹 MATERIALES
        // =========================
        if (data.productos?.length) {

            data.productos.forEach(detalle => {
                this.addMaterialesToTable(
                    {
                        id: detalle.producto_id,
                        nombre: detalle.nombre,
                        precio_venta: detalle.precio_venta
                    },
                    Number(detalle.cantidad),
                    Number(detalle.precio_venta),
                    Number(detalle.subtotal)
                );
            });

          const chk = document.getElementById('chkMateriales');

            this.activarModulo({
                wrapId: 'wrapMateriales',
                tableId: '#tablaDetallesMateriales',
                chkId: 'chkMateriales'
            });

            // 🔥 DISPARAR EVENTO REAL
                if (chk) {
                    chk.checked = true;
                    chk.dispatchEvent(new Event('change'));
                }  

            
        }

        // =========================
        // 🔹 SERVICIOS
        // =========================
        if (data.servicios?.length) {

            data.servicios.forEach(detalle => {
                this.addServiciosToTable(
                    {
                        id: detalle.servicio_id,
                        descripcion: detalle.descripcion
                    },
                    Number(detalle.cantidad),
                    Number(detalle.precio_unitario),
                    Number(detalle.subtotal)
                );
            });

            const chk = document.getElementById('chkServicio');

            this.activarModulo({
            wrapId: 'wrapServicios',
            tableId: '#tablaDetallesServicios',
            chkId: 'chkServicio'
            });

            // 🔥 DISPARAR EVENTO REAL
                if (chk) {
                    chk.checked = true;
                    chk.dispatchEvent(new Event('change'));
                } 

            
        }

        // =========================
        // 🔹 TRANSPORTE
        // =========================
        if (data.transporte?.length) {

            data.transporte.forEach(detalle => {
                this.addTransporteToTable(
                    {
                        id: detalle.transporte_id,
                        label: detalle.name
                    },
                    Number(detalle.cantidad),
                    Number(detalle.precio_unitario),
                    Number(detalle.subtotal)
                );
            });

            const chk = document.getElementById('chkTransporte');

            this.activarModulo({
            wrapId: 'wrapTransporte',
            tableId: '#tablaDetallesTransporte',
            chkId: 'chkTransporte'
            });

            // 🔥 DISPARAR EVENTO REAL
                if (chk) {
                    chk.checked = true;
                    chk.dispatchEvent(new Event('change'));
                } 

            
        }

        // =========================
        // 🔹 TRANSPORTE PERSONAL
        // =========================
        if (data.tpersonal?.length) {

            data.tpersonal.forEach(detalle => {
                this.addTPersonalToTable(
                    {
                        id: detalle.tpersonal_id,
                        label: detalle.name
                    },
                    Number(detalle.cantidad),
                    Number(detalle.precio_unitario),
                    Number(detalle.subtotal)
                );
            });

          const chk = document.getElementById('chkTPersonal');  
            
        this.activarModulo({
        wrapId: 'wrapTPersonal',
        tableId: '#tablaDetallesTPersonal',
        chkId: 'chkTPersonal'
        });

        // 🔥 DISPARAR EVENTO REAL
                if (chk) {
                    chk.checked = true;
                    chk.dispatchEvent(new Event('change'));
                } 

            
        }

        // =========================
        // 🔹 RECALCULAR TODO
        // =========================
        this.calculateMaterialesTotals();
        this.calculateServicioTotals();
        this.calculateTransporteTotals();
        this.calculateTPersonalTotals();

    } catch (error) {
        console.error('❌ Error cargando cotización:', error);
    }
}

    async showTicketOption(response){
        const result = await Swal.fire({
            title: '¡Venta registrada!',
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
            const imprimirRuta = "{{ route('ventas.imprimir', ['id' => ':id']) }}";
            window.open(imprimirRuta.replace(':id', response.ventas_id), '_blank');
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

    tbody.appendChild(tr);
    this.calculateServicioTotals();
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




addMaterialesToTable(item, cantidad = 1, precio_venta = null, subtotal = null) {
    const tbody = document.querySelector('#tablaDetallesMateriales tbody');
    if (!tbody) return;

    document.getElementById('producto_id').value = '';
    document.getElementById('producto_nombre').value = '';

    const existingRow = [...tbody.querySelectorAll('tr')].find(row => row.dataset.productoId == item.id);
    if (existingRow) {
        const inputCantidad = existingRow.querySelector('.inputCantidad');
        const nuevaCantidad = parseInt(inputCantidad.value) + cantidad;
        inputCantidad.value = nuevaCantidad;

        const precio = parseFloat(existingRow.querySelector('.precio-venta').value);
        existingRow.querySelector('.subtotal').textContent = (precio * nuevaCantidad).toFixed(2);
    } else {
        const rowCount = tbody.rows.length + 1;
        console.log(item);

        const precio = parseFloat(precio_venta ?? item.precio_venta);
        const sub = subtotal ?? (precio * cantidad);

        const tr = document.createElement('tr');
        tr.dataset.productoId = item.id;

        tr.innerHTML = `
               <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btnEliminarFila">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
            <td class="text-center">${rowCount}</td>
            <td>${item.id ? item.id + ' - ' : ''}${item.nombre ?? item.label ?? 'Producto sin nombre'}</td>
            <td>
                <input type="number" name="material_detalles[${rowCount}][cantidad]" value="${cantidad}" min="0.1" step="0.01" class="form-control form-control-sm inputCantidad">
            </td>
             <td class="text-end">
                    <input type="number" name="material_detalles[${rowCount}][precio_venta]" value="${precio.toFixed(2)}" min="0.1" step="0.01" class="form-control form-control-sm precio-venta">
                    
                </td>

            <td class="text-end subtotal">${sub.toFixed(2)}</td>

            <!-- Campos que viajan al backend -->
            <input type="hidden" name="material_detalles[${rowCount}][producto_id]" value="${item.id}">
        `;

        tr.querySelector('.inputCantidad').addEventListener('change', () => this.calculateMaterialesTotals());
        tr.querySelector('.precio-venta').addEventListener('change', () => this.calculateMaterialesTotals());
        tr.querySelector('.btnEliminarFila').addEventListener('click', () => { tr.remove(); this.calculateMaterialesTotals(); });
        tbody.appendChild(tr);
    }

    this.calculateMaterialesTotals();
   // this.calculateTotals()
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
                { data: 'codigo_ot_cliente', name: 'ordenTrabajo.codigo_ot_cliente' },
                { data: 'serie', name: 'serie' },
                { data: 'correlativo', name: 'correlativo' },
                { data: 'forma_pago', name: 'forma_pago' },
                //{ data: 'validez', name: 'validez' },                
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
                { targets: 0, width: '15%', className: 'text-center' }
            ],
            responsive: true
        });
    }

//     showCreateModal() {

//     // 🔹 Abre modal
//     super.showCreateModal();

//     // 🔥 Limpiar TODO correctamente (usa tu método centralizado)
//     this.resetForm();

//     // 🔥 ❌ ELIMINADO: #tablaDetalles no existe
//     // document.querySelector('#tablaDetalles tbody').innerHTML = '';

//     // 🔹 Valores por defecto
//     const doc = document.getElementById('documento_tipo_codigo');
//     if (doc) doc.value = '01';

//     const comp = document.getElementById('comprobante_tipo_codigo');
//     if (comp) comp.value = '01';

//     // 🔹 Serie y correlativo
//     this.getSerie('01');

//     // 🔹 Asegurar método POST
//     if (this.elements.methodField) {
//         this.elements.methodField.value = 'POST';
//     }

//     // 🔹 Limpiar OT (opcional pero recomendado)
//     const ot = document.getElementById('ot_codigo');
//     if (ot) ot.value = '';

// }

showCreateModal(){
        super.showCreateModal();
        this.resetForm();
        //document.querySelector('#tablaDetalles tbody').innerHTML = '';
        //document.getElementById('documento_tipo_codigo').value = '01';
        this.elements.methodField.value = 'POST';
        const otCodigo = document.getElementById('ot_codigo');
        if (otCodigo) otCodigo.value = '';
        //this.getSerie('01');
    }

async showEditModal(id) {
    try {

         this.isEditing = true;

        const response = await this.fetchData(`${this.baseUrl}/${id}`);
        const data = response.data;

        if (!data) {
            this.showNotification('error', 'No se encontraron datos para esta venta.');
            return;
        }

       
        // SOLO limpiar antes de cargar datos
        this.resetForm();
        this.isEditing = true;



        // 🔹 Configuración del modal
        this.elements.modalTitle.textContent = 'Editar Venta';
        this.elements.methodField.value = 'PUT';
        this.form.action = `${this.baseUrl}/${id}`;

        // 🔹 Campos principales
        document.getElementById('forma_pago').value = data.forma_pago ?? '';
        document.getElementById('comprobante_tipo_codigo').value = data.comprobante_tipo_codigo ?? '';
        document.getElementById('serie').value = data.serie || '';
        document.getElementById('correlativo').value = data.correlativo || '';
        //document.getElementById('validez').value = data.validez ?? '';
        //document.getElementById('ot_codigo').value = data.ot_codigo ?? '';
        //document.getElementById('ot_codigo_hidden').value = data.ot_codigo ?? '';
        //document.getElementById('estado').value = data.estado ?? '';
        //document.getElementById('observaciones').value = data.observaciones ?? '';       

        // 🔹 Cliente
        document.getElementById('cliente_id').value = data.cliente_id ?? '';
        document.getElementById('cliente_razon_social').value = data.cliente ?? '';


        
        // 🔹 COTIZACIÓN + OT
        // =========================
       const cotSelect = document.getElementById('cotizacion_id');
        if (cotSelect) {
            cotSelect.innerHTML = '<option value="">Seleccione cotización</option>';

            if (data.cotizacion_id) {
                cotSelect.innerHTML += `
                <option value="${data.cotizacion_id}" selected>
                COT-${data.cotizacion_id} - OT ${data.ot_cliente ?? ''}
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

        // 🔹 Luego cargas la tabla
        data.productos.forEach(detalle => {
        this.addMaterialesToTable(
        { 
            id: detalle.producto_id, 
            nombre: detalle.nombre,
            precio_venta: detalle.precio_venta
        },
        Number(detalle.cantidad),
        Number(detalle.precio_venta),
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
        { id: detalle.servicio_id, descripcion: detalle.descripcion},
        Number(detalle.cantidad),
        Number(detalle.precio_unitario),
        Number(detalle.subtotal)
        );
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
        this.showNotification('error', 'Error al cargar los datos de la venta');
    }
}




    focusFirstField() {
        document.getElementById('producto_nombre').focus();
    }
    

    async getSerie(codigo) {
        if (!codigo) return;
        const url= "{{ route('ventas.get-serie') }}" + "?comprobante_tipo_codigo=" + codigo;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Si no hay datos, limpiar
                if (!data.serie || !data.numero) {
                    document.getElementById('serie').value = '';
                    document.getElementById('correlativo').value = '';
                    return;
                }
                // Actualizar inputs
                document.getElementById('serie').value = data.serie;
                document.getElementById('correlativo').value = data.numero;
            })
            .catch(error => console.error('Error al obtener la serie y correlativo:', error));
    }
   

}
document.addEventListener('DOMContentLoaded', () => {
    new VentasManager();
});
document.getElementById('mnuVenta').classList.add('menu-open');
document.getElementById('itemVentas').classList.add('active');

</script>

@endpush
