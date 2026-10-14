@extends('plantilla.app')

@push('estilos')
@endpush

@section('contenido')
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">

            <div class="card mb-4">

                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title flex-grow-1">
                        Salidas de Almacén
                    </h3>

                    @can('salidas_create')
                    <button
                        type="button"
                        class="btn btn-primary"
                        id="btnCreate">

                        <i class="bi bi-plus-circle"></i>
                        Nuevo
                    </button>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table
                            id="listadoTable"
                            class="table table-striped table-hover table-sm mobile-records">

                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Usuario</th>
                                    <th>ID</th>
                                    <th>Serie</th>
                                    <th>Correlativo</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
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

@canany(['salidas_create', 'salidas_edit'])
    @include('salidas.action')
@endcanany

@endsection


@push('scripts')
<script>

class SalidasAlmacenManager extends CrudManager {

    constructor() {
        super("{{ url('salidas') }}");

        this.initializeDataTable();

        this.elements.modal.addEventListener('hidden.bs.modal', () => {
            this.resetForm();
        });

        /*
        =====================================
        SELECT COMPROBANTE
        =====================================
        */
        this.populateSelect(
            'comprobante_tipo_codigo',
            '{{ route("comprobante-tipos.select") }}',
            item => `<option value="${item.codigo}">
                        ${item.codigo} - ${item.descripcion}
                    </option>`
        );

        /*
        =====================================
        BUSCAR PRODUCTOS
        =====================================
        */
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

        /*
        =====================================
        BUSCAR CLIENTE
        =====================================
        */
        this.setupLiveSearchSelect({
            inputId: 'cliente_razon_social',
            hiddenId: 'cliente_id',
            url: "{{ route('clientes.buscar') }}",

            template: (item) => {
                return `${item.numero_documento} - ${item.razon_social}`;
            }
        });

        /*
        =====================================
        EVENTO COMPROBANTE → SERIE
        =====================================
        */
        const selectComprobante = document.getElementById('comprobante_tipo_codigo');

        if (selectComprobante) {
            selectComprobante.addEventListener('change', (e) => {
                this.getSerie(e.target.value);
            });
        }
    }

    /*
    =====================================
    DATATABLE
    =====================================
    */
    initializeDataTable() {
        this.tabla = $('#listadoTable').DataTable({
            processing: true,
            serverSide: true,

            ajax: {
                url: this.baseUrl,
                type: 'GET'
            },

            columns: [
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'usuario',
                    name: 'usuario',
                    defaultContent: '-'
                },

                {
                    data: 'id',
                    name: 'id'
                },

                {
                    data: 'serie',
                    name: 'serie'
                },

                {
                    data: 'correlativo',
                    name: 'correlativo'
                },


                {
                    data: 'cliente',
                    name: 'cliente.razon_social',
                    defaultContent: '-'
                },
                {
                    data: 'fecha',
                    name: 'fecha'
                },
                {
                    data: 'motivo',
                    name: 'motivo'
                },
                {
                    data: 'estado',
                    name: 'estado',
                    orderable: false,
                    searchable: false
                }
            ],

            columnDefs: [
                {
                    targets: 0,
                    width: '15%',
                    className: 'text-center'
                }
            ],

            responsive: true
        });
    }

    /*
    =====================================
    AGREGAR PRODUCTOS
    =====================================
    */
    addMaterialesToTable(item, cantidad = 1, precio_venta = null) {

    const tbody = document.querySelector('#tablaDetallesMateriales tbody');
    if (!tbody) return;

    // limpiar búsqueda
    document.getElementById('producto_id').value = '';
    document.getElementById('producto_nombre').value = '';

    /*
    =====================================
    SI YA EXISTE → SOLO SUMAR CANTIDAD
    =====================================
    */
    const existingRow = [...tbody.querySelectorAll('tr')]
        .find(row => row.dataset.productoId == item.id);

    if (existingRow) {

        const inputCantidad = existingRow.querySelector('.inputCantidad');
        const precioInput   = existingRow.querySelector('.precio-venta');

        const nuevaCantidad = (
            parseFloat(inputCantidad.value) || 0
        ) + (
            parseFloat(cantidad) || 0
        );

        inputCantidad.value = nuevaCantidad;

        // recalcular subtotal visual
        const precio = parseFloat(precioInput.value) || 0;
        const subtotalCell = existingRow.querySelector('.subtotal');

        if (subtotalCell) {
            subtotalCell.textContent = (nuevaCantidad * precio).toFixed(2);
        }

        this.calculateMaterialesTotals();
        return;
    }

    /*
    =====================================
    NUEVA FILA
    =====================================
    */
    const rowCount = tbody.rows.length + 1;

    const precio = parseFloat(
        precio_venta ?? item.precio_venta ?? 0
    ) || 0;

    const subtotal = (parseFloat(cantidad) || 0) * precio;

    const tr = document.createElement('tr');
    tr.dataset.productoId = item.id;

    tr.innerHTML = `
        <td class="text-center">
            <button
                type="button"
                class="btn btn-danger btn-sm btnEliminarFila">
                <i class="bi bi-trash"></i>
            </button>
        </td>

        <td class="text-center">${rowCount}</td>

        <td>
            ${item.codigo ? item.codigo + ' - ' : ''}
            ${item.nombre ?? item.label ?? 'Producto'}

            <!-- ESTE CAMPO VIAJA AL BACKEND -->
            <input
                type="hidden"
                name="material_detalles[${rowCount}][producto_id]"
                value="${item.id}">
        </td>

        <td>
            <!-- ESTE CAMPO VIAJA AL BACKEND -->
            <input
                type="number"
                name="material_detalles[${rowCount}][cantidad]"
                value="${cantidad}"
                min="1"
                step="1"
                class="form-control form-control-sm inputCantidad">
        </td>

        <td>
            <!-- ESTE CAMPO VIAJA AL BACKEND -->
            <input
                type="number"
                name="material_detalles[${rowCount}][precio_venta]"
                value="${precio.toFixed(2)}"
                min="0.01"
                step="0.01"
                class="form-control form-control-sm precio-venta">
        </td>

        <td class="text-end subtotal">
            ${subtotal.toFixed(2)}
        </td>
    `;

    /*
    =====================================
    EVENTOS
    =====================================
    */

    tr.querySelector('.inputCantidad')
        .addEventListener('input', () => {
            this.calculateMaterialesTotals();
        });

    tr.querySelector('.precio-venta')
        .addEventListener('input', () => {
            this.calculateMaterialesTotals();
        });

    tr.querySelector('.btnEliminarFila')
        .addEventListener('click', () => {
            tr.remove();
            this.calculateMaterialesTotals();
        });

    tbody.appendChild(tr);

    /*
    =====================================
    RECALCULAR TOTALES
    =====================================
    */
    this.calculateMaterialesTotals();
}

    /*
    =====================================
    TOTALES DE MATERIALES
    =====================================
    */
    calculateMaterialesTotals() {

        const tbody = document.querySelector('#tablaDetallesMateriales tbody');
        if (!tbody) return;

        let total = 0;

        tbody.querySelectorAll('tr').forEach(row => {

            const cantidad = parseFloat(
                row.querySelector('.inputCantidad')?.value
            ) || 0;

            const precio = parseFloat(
                row.querySelector('.precio-venta')?.value
            ) || 0;

            const subtotal = cantidad * precio;

            row.querySelector('.subtotal').textContent =
                subtotal.toFixed(2);

            total += subtotal;
        });

        document.getElementById('totalmateriales').value =
            total.toFixed(2);

        this.calculateTotals();
    }

    /*
    =====================================
    TOTAL GENERAL
    =====================================
    */
    calculateTotals() {

        const totalMateriales = parseFloat(
            document.getElementById('totalmateriales')?.value
        ) || 0;

        const porcentajeIGV = 0.18;

        const op_gravada = totalMateriales / (1 + porcentajeIGV);
        const impuesto = totalMateriales - op_gravada;

        document.getElementById('op_gravada').value =
            op_gravada.toFixed(2);

        document.getElementById('op_exonerada').value =
            '0.00';

        document.getElementById('op_inafecta').value =
            '0.00';

        document.getElementById('impuesto').value =
            impuesto.toFixed(2);

        document.getElementById('total').value =
            totalMateriales.toFixed(2);
    }

   async showEditModal(id) {
    try {

        console.log("🔎 ID recibido en showEditModal:", id);
        console.log("🔎 URL solicitada:", `${this.baseUrl}/${id}`);

        const response = await this.fetchData(`${this.baseUrl}/${id}`);

        console.log("📦 JSON recibido del servidor:", response);

        this.resetForm();
        this.isEditing = true;

        /*
        =========================
        CONFIGURACIÓN MODAL
        =========================
        */

        this.elements.modalTitle.textContent =
            'Editar registro - Salida de Almacén';

        this.elements.methodField.value = 'PUT';

        this.form.action = `${this.baseUrl}/${id}`;

        /*
        =========================
        CAMPOS PRINCIPALES
        =========================
        */

        document.getElementById('cliente_id').value =
            response.cliente_id || '';

        document.getElementById('cliente_razon_social').value =
            response.cliente?.razon_social || '';

        document.getElementById('comprobante_tipo_codigo').value =
            response.comprobante_tipo_codigo || '';

        document.getElementById('serie').value =
            response.serie || '';

        document.getElementById('correlativo').value =
            response.correlativo || '';

        document.getElementById('motivo').value =
            response.motivo || '';

        /*
        =========================
        CARGAR DETALLES
        =========================
        */

        if (response.detalles && response.detalles.length > 0) {

            response.detalles.forEach(detalle => {

                this.addMaterialesToTable(
                    {
                        id: detalle.producto_id,
                        codigo: detalle.producto?.codigo || '',
                        nombre: detalle.producto?.nombre || 'Producto'
                    },
                    parseFloat(detalle.cantidad),
                    parseFloat(detalle.precio_unitario)
                );
            });
        }

        /*
        =========================
        TOTAL FINAL
        =========================
        */

        document.getElementById('total').value =
            parseFloat(response.total || 0).toFixed(2);

        this.calculateMaterialesTotals();

        this.modal.show();

    } catch (error) {

        console.error("Error al cargar datos:", error);

        this.showNotification(
            'error',
            'Error al cargar los datos'
        );
    }
}

    /*
    =====================================
    MODAL NUEVO
    =====================================
    */
    showCreateModal() {
        super.showCreateModal();

        this.resetForm();

        this.elements.modalTitle.textContent =
            'Nueva Salida de Almacén';

        this.elements.methodField.value = '';
    }

    /*
    =====================================
    RESET FORM
    =====================================
    */
    resetForm() {
        if (this.form) {
            this.form.reset();
        }

        const tbody = document.querySelector('#tablaDetallesMateriales tbody');
        if (tbody) tbody.innerHTML = '';

        document.getElementById('totalmateriales').value = '0.00';
        document.getElementById('op_gravada').value = '0.00';
        document.getElementById('op_exonerada').value = '0.00';
        document.getElementById('op_inafecta').value = '0.00';
        document.getElementById('impuesto').value = '0.00';
        document.getElementById('total').value = '0.00';

        this.clearFormErrors?.();
    }

    /*
    =====================================
    OBTENER SERIE
    =====================================
    */
    async getSerie(codigo) {

        if (!codigo) return;

        const url =
            "{{ route('ventas.get-serie') }}" +
            "?comprobante_tipo_codigo=" + codigo;

        fetch(url)
            .then(response => response.json())
            .then(data => {

                document.getElementById('serie').value =
                    data.serie ?? '';

                document.getElementById('correlativo').value =
                    data.numero ?? '';
            })
            .catch(error => {
                console.error(error);
            });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new SalidasAlmacenManager();
});

document.getElementById('mnuAlmacen')?.classList.add('menu-open');
document.getElementById('itemSalidas')?.classList.add('active');

</script>
@endpush