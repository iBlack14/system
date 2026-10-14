@extends('plantilla.app') 
@push('estilos')
@endpush

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <div class="card mb-4">

                <!-- HEADER -->
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title flex-grow-1">
                        Notas de Crédito
                    </h3>

                    @can('notacredito_create')
                    <button type="button" class="btn btn-primary" id="btnCreate">
                        <i class="bi bi-plus-circle"></i> Nuevo
                    </button>
                    @endcan
                </div>

                <!-- BODY -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="listadoTable" class="table table-stripesd table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>ID</th>
                                    <th>Serie</th>
                                    <th>Correlativo</th>
                                    <th>Cliente</th>
                                    <th>Monto Nota </th>
                                    <th># OTs</th> 
                                    <th>OTS</th> 
                                    <th>Visor Nota</th>
                                    <th>Detalles</th>
                                    <th>Valida Nota</th>                                                                      
                                    <th>Fecha</th>
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

{{-- MODAL --}}
@canany(['notacredito_create', 'notacredito_edit'])
    @include('notacredito.action')
@endcanany

@endsection

@push('scripts')
<script>

class NotaCreditoManager extends CrudManager {

    constructor() {
        super("{{ url('notacredito') }}");
        this.initializeDataTable();


        this.setupLiveSearchSelect({
        inputId: 'cliente_razon_social',
        hiddenId: 'cliente_id',
        url: "{{ route('clientes.buscar') }}",
        template: (item) => {
            return `${item.numero_documento} - ${item.razon_social}`;
        },

        onSelect: (cliente) => {

            this.populateOTTable(
                'tablaOTs',
                `{{ url('ordenestrabajo/por-cliente') }}/${cliente.id}?estado=11`
            );
        }
        });
    }

    



    populateOTTable(tableId, url) {

        fetch(url)
        .then(res => res.json())
        .then(data => {

        let tbody = document.querySelector(`#${tableId} tbody`);
        tbody.innerHTML = '';

        data.forEach(item => {

            let monto = parseFloat(item.monto ?? 0);
            let sinIGV = monto / 1.18;
            let rebate = sinIGV * 0.05; // 👈 ajusta si tu lógica es diferente

            let row = `
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="checkOT" name="ots[]" value="${item.id}" data-rebate="${rebate}">
                    </td>
                    <td>${item.codigo_ot_cliente}</td>
                    <td>${item.descripcion}</td>
                    <td>${item.codigo_orden_servicio}</td>
                    <td>S/ ${monto.toFixed(2)}</td>
                    <td>S/ ${sinIGV.toFixed(2)}</td>
                    <td class="fw-bold text-success">S/ ${rebate.toFixed(2)}</td>                 
                </tr>

                
            `;
            tbody.innerHTML += row;
        });

         // reset total
        document.getElementById('totalRebate').innerText = 'S/ 0.00';

        })
            .catch(err => {
            console.error('Error cargando OTs:', err);
        });
    }



    /* ===================================================== */
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
                { data: 'id' },
                { data: 'serie' },
                { data: 'correlativo' },
                { data: 'cliente' },
                { data: 'monto_nota' },
                { data: 'cantidad_ots' },
                { data: 'ots' },                
                { data: 'archivo' },       
                { data: 'detalles_nota' },         
                { data: 'valida_nota' },                                
                { data: 'fecha_registro' }
            ],
            responsive: true,
            order: [[1, 'desc']]
        });
    }

    

    /* ===================================================== */
        async showEditModal(id) {

        try {

        const data = await this.fetchData(`${this.baseUrl}/${id}`);

        this.isEditing = true;
        this.resetForm();

        this.elements.modalTitle.textContent = 'Editar Nota de Crédito';
        this.elements.methodField.value = 'PUT';
        this.form.action = `${this.baseUrl}/${id}`;

        // 🔥 quitar required en edición
        ['archivo_nota','valida_nota','detalles_nota'].forEach(id => {
            document.getElementById(id)?.removeAttribute('required');
        });

        /* ================= DATOS ================= */
        document.getElementById('nota_id').value = data.id ?? '';
        document.getElementById('serie').value = data.serie ?? '';
        document.getElementById('correlativo').value = data.correlativo ?? '';
        document.getElementById('monto_nota').value = data.monto_nota ?? '';

        /* ================= CLIENTE ================= */
        let clienteId = '';

        if (data.ordenes_trabajo && data.ordenes_trabajo.length > 0) {

            const cliente = data.ordenes_trabajo[0]?.cliente;

            if (cliente) {
                clienteId = cliente.id;
                document.getElementById('cliente_id').value = cliente.id ?? '';
                document.getElementById('cliente_razon_social').value = cliente.razon_social ?? '';
            }
        }

        /* ================= TABLA OTs ================= */
        const tbody = document.querySelector('#tablaOTs tbody');

        if (tbody) {

            tbody.innerHTML = '';

            if (clienteId) {

                // 🔥 1. OTs disponibles (estado 11)
                const response = await fetch(`{{ url('ordenestrabajo/por-cliente-nota') }}/${clienteId}/${data.id}`);

                const allOTs = await response.json();
                

                // 🔥 2. OTs ya asignadas (estado 12, vienen en data)
                const otsAsignadas = data.ordenes_trabajo ?? [];

                // 🔥 IDs seleccionados
                const idsAsignados = otsAsignadas.map(ot => ot.id);
               
                /* 🔥 pintar */
                allOTs.forEach(ot => {

                    const monto = parseFloat(ot.monto ?? 0);
                    const sinIGV = monto / 1.18;
                    const rebate = sinIGV * 0.05;

                    const checked = idsAsignados.includes(ot.id) ? 'checked' : '';

                    const row = `
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" 
                                       class="checkOT"
                                       name="ots[]" 
                                       value="${ot.id}"
                                       data-rebate="${rebate}"
                                       ${checked}>
                            </td>

                            <td>${ot.codigo_ot_cliente}</td>
                            <td>${ot.descripcion}</td>
                            <td>${ot.codigo_orden_servicio ?? '-'}</td>

                            <td class="text-end">${monto.toFixed(2)}</td>
                            <td class="text-end">${sinIGV.toFixed(2)}</td>
                            <td class="text-end rebate">${rebate.toFixed(2)}</td>
                        </tr>
                    `;

                    tbody.insertAdjacentHTML('beforeend', row);
                });

                calcularTotalRebate();

            } else {

                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Sin cliente seleccionado
                        </td>
                    </tr>
                `;
            }
        }

        this.modal.show();

    } catch (error) {

        console.error('ERROR', error);
        this.showNotification('error', 'Error al cargar nota de crédito');
    }
}

                    showCreateModal() {

                        this.isEditing = false;
                        this.resetForm();

                        this.elements.modalTitle.textContent = 'Nueva Nota de Crédito';
                        this.elements.methodField.value = 'POST';
                        this.form.action = this.baseUrl;

                        // 🔥 limpiar cliente
                        document.getElementById('cliente_id').value = '';
                        document.getElementById('cliente_razon_social').value = '';

                        // 🔥 limpiar tabla OTs
                        const tbody = document.querySelector('#tablaOTs tbody');
                        if (tbody) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Seleccione un cliente
                                    </td>
                                </tr>
                            `;
                        }

                        // 🔥 reset total
                        document.getElementById('totalRebate').innerText = 'S/ 0.00';

                        // 🔥 limpiar monto
                        document.getElementById('monto_nota').value = '';

                        this.modal.show();
                    }

            focusFirstField() {
                const el = document.getElementById('serie');
                if (el) el.focus();
            }
        }


        function calcularTotalRebate() {

            let total = 0;

            document.querySelectorAll('.checkOT:checked').forEach(chk => {
                total += parseFloat(chk.dataset.rebate || 0);
            });

            // 🔥 total sin IGV (solo informativo)
            document.getElementById('totalRebate').innerText =
                'S/ ' + total.toFixed(2);

            // 🔥 monto con IGV (lo que se guarda)
            let totalConIGV = total * 1.18;

            document.getElementById('monto_nota').value =
                totalConIGV.toFixed(2);
            
            let totalSinIGV = totalConIGV / 1.18;

            document.getElementById('totalrebates_nota').value =
                totalSinIGV.toFixed(2);

            let notaIGV = totalConIGV * 0.18;

            document.getElementById('igv_nota').value =
                notaIGV.toFixed(2);
        }
        
        function mostrarAlerta(mensaje, tipo = 'danger') {

            const container = document.getElementById('alertContainer');

            container.innerHTML = `
                <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            // 🔥 desaparecer automático
            setTimeout(() => {
                container.innerHTML = '';
            }, 4000);
        }
        

/* Capturar OTs seleccionadas*/

/* document.getElementById('formUpdate').addEventListener('submit', function () {

    // eliminar anteriores
    this.querySelectorAll('input[name="ots[]"]').forEach(el => el.remove());

    let seleccionadas = document.querySelectorAll('.checkOT:checked');

    seleccionadas.forEach(check => {

        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ots[]';
        input.value = check.value;

        this.appendChild(input);
    });

}); */

    document.addEventListener('change', function(e) {

    if (e.target.classList.contains('checkOT')) {
        calcularTotalRebate();
    }

    if (e.target.id === 'checkAll') {

        let checked = e.target.checked;

        document.querySelectorAll('.checkOT').forEach(chk => {
            chk.checked = checked;
        });

        calcularTotalRebate();
    }
});


document.getElementById('formUpdate').addEventListener('submit', function (e) {

    let seleccionadas = document.querySelectorAll('.checkOT:checked');

    if (seleccionadas.length === 0) {

        e.preventDefault();
        e.stopImmediatePropagation(); // 🔥 clave para bloquear CrudManager

        Swal.fire({
            icon: 'warning',
            title: 'Debe seleccionar OTs',
            text: 'No puedes registrar una nota de crédito sin al menos una Orden de Trabajo',
            confirmButtonText: 'Entendido'
        });

        return false;
    }

});

/* ================= INIT ================= */
document.addEventListener('DOMContentLoaded', () => {

    const manager = new NotaCreditoManager();

    document.getElementById('btnCreate')?.addEventListener('click', () => {
        manager.showCreateModal();
    });

});

document.addEventListener('click', function(e) {

    if (e.target.closest('#btnSave')) { // 👈 ajusta ID si es otro

        let seleccionadas = document.querySelectorAll('.checkOT:checked');

        if (seleccionadas.length === 0) {

            e.preventDefault();
            e.stopImmediatePropagation();

            Swal.fire({
                icon: 'warning',
                title: 'Falta seleccionar OTs',
                text: 'Debe elegir al menos una Orden de Trabajo antes de guardar',
                confirmButtonText: 'Ok'
            });

            return false;
        }
    }

});

/* MENÚ ACTIVO */
document.getElementById('mnuVenta')?.classList.add('menu-open');
document.getElementById('itemNotaCredito')?.classList.add('active');

</script>
@endpush