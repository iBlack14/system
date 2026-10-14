<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content shadow-lg border-0">

            <form id="formUpdate" method="post">
                @csrf
                <input type="hidden" id="method_field" name="_method">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white sticky-top">
                    <h5 class="modal-title fw-semibold" id="modalTitle">
                        <i class="bi bi-clipboard-plus me-2"></i> Nueva Orden de Trabajo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- ================= CLIENTE ================= -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            <i class="bi bi-building me-2 text-primary"></i> Cliente
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label fw-semibold">Buscar Cliente *</label>
                                    <input type="text"
                                           id="cliente_razon_social"
                                           class="form-control form-control-sm"
                                           placeholder="Buscar cliente..."
                                           autocomplete="off">
                                    <input type="hidden" id="cliente_id" name="cliente_id">
                                </div>

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label fw-semibold">Sede *</label>
                                    <select name="sede_id" id="sede_id" class="form-select form-select-sm">
                                        <option value="">Seleccione</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ================= RESPONSABLE ================= -->
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            <i class="bi bi-person-lines-fill me-2 text-primary"></i> Solicitante
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label fw-semibold">Solicitante *</label>
                                    <select name="responsable_id" id="responsable_id" class="form-select form-select-sm">
                                        <option value="">Seleccione</option>
                                    </select>
                                </div>

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label fw-semibold">Técnico *</label>
                                    <input type="text"
                                           id="tecnico_nombre"
                                           class="form-control form-control-sm"
                                           placeholder="Buscar técnico...">
                                    <input type="hidden" id="tecnico_id" name="tecnico_id">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ================= DATOS OT ================= -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            <i class="bi bi-clipboard-check me-2 text-primary"></i> Datos de la OT
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-lg-4 col-md-6 col-12">
                                    <label class="form-label fw-semibold">Código OT *</label>
                                    <input type="text" id="codigo_ot_cliente" name="codigo_ot_cliente" class="form-control form-control-sm">
                                </div>

                                <div class="col-lg-4 col-md-6 col-12">
                                    <label class="form-label fw-semibold">Motivo *</label>
                                    <input type="text" id="motivo" name="motivo" class="form-control form-control-sm">
                                </div>

                                <div class="col-lg-4 col-md-6 col-12">
                                    <label class="form-label fw-semibold">Estado</label>
                                    <input type="text" id="estado" name="estado"
                                           class="form-control form-control-sm bg-light"
                                           readonly>
                                </div>

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label fw-semibold">Descripción *</label>
                                    <input type="text" id="descripcion" name="descripcion" class="form-control form-control-sm">
                                </div>

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label fw-semibold">Observaciones</label>
                                    <input type="text" id="observaciones" name="observaciones" class="form-control form-control-sm">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" id="btnSubmit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Guardar
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>