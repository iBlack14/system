<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content shadow-lg border-0">

            <style>
                #modalUpdate .modal-header {
                    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
                    color: #ffffff;
                    border-bottom: none;
                }
                #modalUpdate .card {
                    border: 1px solid #cbd5e1;
                    border-radius: 0.5rem;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                }
                #modalUpdate .card-header {
                    background: #f8fafc;
                    border-bottom: 1px solid #e2e8f0;
                    color: #0f172a;
                    font-size: 0.95rem;
                    font-weight: 700;
                    padding: 0.65rem 1rem;
                }
                #modalUpdate .form-label {
                    color: #0f172a !important;
                    font-weight: 600 !important;
                    font-size: 0.85rem !important;
                    margin-bottom: 0.35rem !important;
                    display: block;
                }
                #modalUpdate .form-control, 
                #modalUpdate .form-select {
                    color: #0f172a !important;
                    border-color: #cbd5e1 !important;
                    font-size: 0.875rem !important;
                    font-weight: 500 !important;
                    background-color: #ffffff;
                }
                #modalUpdate .form-control:focus, 
                #modalUpdate .form-select:focus {
                    border-color: #2563eb !important;
                    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
                }
                #modalUpdate .form-control[readonly],
                #modalUpdate .form-control:disabled {
                    background-color: #f1f5f9 !important;
                    color: #0f172a !important;
                    font-weight: 600 !important;
                    border-color: #cbd5e1 !important;
                    opacity: 1 !important;
                }
                #modalUpdate .select2-container .select2-selection--single {
                    height: 38px !important;
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 0.375rem !important;
                    background-color: #ffffff !important;
                    display: flex !important;
                    align-items: center !important;
                }
                #modalUpdate .select2-container .select2-selection--single .select2-selection__rendered {
                    color: #0f172a !important;
                    font-size: 0.875rem !important;
                    font-weight: 600 !important;
                    padding-left: 8px !important;
                    line-height: 36px !important;
                }
                #modalUpdate .select2-container .select2-selection--single .select2-selection__placeholder {
                    color: #64748b !important;
                    font-weight: 500 !important;
                }
                #modalUpdate .select2-container .select2-selection--single .select2-selection__arrow {
                    height: 36px !important;
                    right: 8px !important;
                }
                .select2-dropdown {
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 0.375rem !important;
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15) !important;
                    z-index: 1065 !important;
                }
                .select2-results__option {
                    color: #1e293b !important;
                    font-size: 0.875rem !important;
                    padding: 8px 12px !important;
                    font-weight: 500 !important;
                }
                .select2-results__option--highlighted[aria-selected] {
                    background-color: #2563eb !important;
                    color: #ffffff !important;
                }
                /* El autocompletado del cliente debe flotar sobre las tarjetas siguientes. */
                #modalUpdate .ot-client-card {
                    position: relative;
                    z-index: 30;
                    overflow: visible !important;
                }
                #modalUpdate .ot-client-card .card-body,
                #modalUpdate .ot-client-card .row,
                #modalUpdate .ot-client-card .position-relative {
                    overflow: visible !important;
                }
                #modalUpdate .ot-client-card .search-list {
                    z-index: 2100 !important;
                    width: 100% !important;
                    max-height: 240px !important;
                    overflow-x: hidden !important;
                    overflow-y: auto !important;
                    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18) !important;
                }
                #modalUpdate .ot-personnel-card {
                    position: relative;
                    z-index: 20;
                    overflow: visible !important;
                }
                #modalUpdate .ot-personnel-card .card-body,
                #modalUpdate .ot-personnel-card .row,
                #modalUpdate .ot-personnel-card .position-relative {
                    overflow: visible !important;
                }
                #modalUpdate .ot-personnel-card .search-list {
                    z-index: 2100 !important;
                    width: 100% !important;
                    max-height: 240px !important;
                    overflow-x: hidden !important;
                    overflow-y: auto !important;
                    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18) !important;
                }
                #modalUpdate .ot-details-card {
                    position: relative;
                    z-index: 1;
                }
            </style>

            <form id="formUpdate" method="post">
                @csrf
                <input type="hidden" id="method_field" name="_method">

                <!-- HEADER -->
                <div class="modal-header py-3 px-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clipboard-plus fs-4 me-2 text-white"></i>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalTitle">
                                Nueva Orden de Trabajo
                            </h5>
                            <small class="text-white-50" style="font-size: 0.78rem;">Complete los datos del servicio y asigne el personal responsable</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body p-4 bg-light">

                    <!-- ================= CLIENTE Y SEDE ================= -->
                    <div class="card mb-3 shadow-sm bg-white ot-client-card">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-building me-2 text-primary fs-5"></i>
                            <span>1. Cliente y Sede</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">

                                <div class="col-lg-6 col-md-12 position-relative">
                                    <label class="form-label">
                                        Cliente <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="cliente_razon_social"
                                           class="form-control"
                                           placeholder="Escriba RUC o Razón Social..."
                                           autocomplete="off">
                                    <input type="hidden" id="cliente_id" name="cliente_id">
                                    <small class="text-muted" style="font-size: 0.75rem;">Busque y seleccione el cliente de la lista</small>
                                </div>

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label">
                                        Sede del Cliente <span class="text-danger">*</span>
                                    </label>
                                    <select name="sede_id" id="sede_id" class="form-select" required>
                                        <option value="">Seleccione una sede</option>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.75rem;">Se listan las sedes asociadas al cliente seleccionado</small>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ================= SOLICITANTE Y PERSONAL ================= -->
                    <div class="card mb-3 shadow-sm bg-white ot-personnel-card">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-person-lines-fill me-2 text-primary fs-5"></i>
                            <span>2. Solicitante y Personal Asignado</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">

                                <div class="col-lg-6 col-md-12">
                                    <label class="form-label">
                                        Solicitante (Contacto de Cliente) <span class="text-danger">*</span>
                                    </label>
                                    <select name="responsable_id" id="responsable_id" class="form-select" required>
                                        <option value="">Seleccione un solicitante</option>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.75rem;">Responsable o solicitante por parte del cliente</small>
                                </div>

                                <div class="col-lg-6 col-md-12 position-relative">
                                    <label class="form-label">
                                        Técnico Asignado <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           id="tecnico_nombre"
                                           class="form-control"
                                           placeholder="Buscar técnico por nombre o DNI..."
                                           autocomplete="off">
                                    <input type="hidden" id="tecnico_id" name="tecnico_id">
                                    <small class="text-muted" style="font-size: 0.75rem;">Escriba para buscar técnico de ACOTEC</small>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ================= DATOS DE LA OT ================= -->
                    <div class="card shadow-sm bg-white ot-details-card">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-clipboard-check me-2 text-primary fs-5"></i>
                            <span>3. Detalles Operativos de la Orden de Trabajo</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">

                                <div class="col-lg-4 col-md-6 col-12">
                                    <label class="form-label">
                                        Código OT Cliente <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           id="codigo_ot_cliente" 
                                           name="codigo_ot_cliente" 
                                           class="form-control"
                                           placeholder="Ej: OT-2026-001" required>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12">
                                    <label class="form-label">
                                        Motivo / Tipo de Servicio <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           id="motivo" 
                                           name="motivo" 
                                           class="form-control"
                                           placeholder="Ej: Mantenimiento Correctivo" required>
                                </div>

                                <div class="col-lg-4 col-md-6 col-12">
                                    <label class="form-label">
                                        Estado de la OT
                                    </label>
                                    <input type="text" 
                                           id="estado" 
                                           name="estado"
                                           class="form-control"
                                           readonly
                                           value="Registrado">
                                </div>

                                <div class="col-lg-12 col-12">
                                    <label class="form-label">
                                        Descripción del Trabajo <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="descripcion" 
                                              name="descripcion" 
                                              class="form-control" 
                                              rows="3"
                                              placeholder="Detalle el trabajo o servicio a realizar..." required></textarea>
                                </div>

                                <div class="col-lg-12 col-12">
                                    <label class="form-label">
                                        Observaciones Adicionales
                                    </label>
                                    <input type="text" 
                                           id="observaciones" 
                                           name="observaciones" 
                                           class="form-control"
                                           minlength="3"
                                           placeholder="Observaciones o notas especiales...">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" id="btnSubmit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Orden de Trabajo
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
