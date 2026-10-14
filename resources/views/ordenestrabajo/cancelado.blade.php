<div class="modal fade" id="modalCanceladoOT" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <form id="formCanceladoOT" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        💰 Registrar Pago de Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="cancelado_ot_id">

                    <div class="row">

                        <!-- 🔹 IZQUIERDA: INFORMACIÓN -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-header bg-light">
                                    <strong>📄 Información de la OT</strong>
                                </div>

                                <div class="card-body">

                                    <div class="mb-3">
                                        <label class="form-label">Código OT</label>
                                        <input type="text" id="cancelado_codigo"
                                            class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea id="cancelado_descripcion"
                                            class="form-control"
                                            rows="3" readonly></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Responsable</label>
                                        <input type="text" id="cancelado_responsable"
                                            class="form-control" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Monto de Venta</label>
                                        <input type="text" id="cancelado_montoventa"
                                            class="form-control fw-bold text-success" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Rebate de Nota de Crédito</label>
                                        <input type="text" id="cancelado_rebatenotacredito"
                                            class="form-control fw-bold text-success" readonly>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- 🔹 DERECHA: REGISTRO DE PAGO -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-header bg-light">
                                    <strong>💳 Datos del Pago</strong>
                                </div>

                                <div class="card-body">

                                    <div class="mb-3">
                                        <label class="form-label">Monto Pagado *</label>
                                        <input type="number" step="0.01"
                                            name="monto_pagado"
                                            id="monto_pagado"
                                            class="form-control"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Monto Detracción *</label>
                                        <input type="number" step="0.01"
                                            name="monto_detraccion"
                                            id="monto_detraccion"
                                            class="form-control"
                                            required>
                                    </div>



                                    <div class="mb-3">
                                        <label class="form-label">Fecha de Pago *</label>
                                        <input type="date"
                                            name="fecha_pago"
                                            id="fecha_pago"
                                            class="form-control"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Banco *</label>
                                        <select name="banco_id" id="banco_id"
                                            class="form-select" required></select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">N° Operación</label>
                                        <input type="text"
                                            name="num_operacion"
                                            id="num_operacion"
                                            class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Voucher *</label>
                                        <input type="file"
                                            name="voucher_pago"
                                            class="form-control"
                                            accept="image/png, image/jpeg, image/jpg"
                                            required>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Registrar Pago
                    </button>

                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>