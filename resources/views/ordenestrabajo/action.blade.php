<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content shadow-lg border-0">

            <style>
                #modalUpdate .modal-dialog {
                    width: min(980px, calc(100vw - 2rem));
                    max-width: 980px;
                    margin: 1.5rem auto;
                    transition: all 0.2s ease-in-out;
                }
                #modalUpdate .modal-dialog.modal-fullscreen {
                    width: 100vw !important;
                    max-width: 100vw !important;
                    height: 100vh !important;
                    margin: 0 !important;
                    border-radius: 0 !important;
                }
                #modalUpdate .modal-dialog.modal-fullscreen .modal-content {
                    border-radius: 0 !important;
                    height: 100vh !important;
                }
                #modalUpdate .modal-content {
                    border-radius: 0.65rem;
                    overflow: hidden;
                    box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.25) !important;
                }
                #modalUpdate #formUpdate {
                    display: flex;
                    flex-direction: column;
                    margin-bottom: 0;
                }
                #modalUpdate .modal-header {
                    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
                    color: #ffffff;
                    border-bottom: none;
                    padding: 0.5rem 1.15rem !important;
                    flex-shrink: 0;
                }
                #modalUpdate .modal-body {
                    padding: 0.75rem 1rem !important;
                    overflow-y: auto;
                    max-height: calc(100vh - 140px);
                    background-color: #f8fafc;
                    scrollbar-width: thin;
                    scrollbar-color: #94a3b8 #f1f5f9;
                }
                #modalUpdate .modal-body::-webkit-scrollbar {
                    width: 6px;
                    height: 6px;
                }
                #modalUpdate .modal-body::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 4px;
                }
                #modalUpdate .modal-footer {
                    padding: 0.5rem 1.15rem !important;
                    background-color: #ffffff;
                    border-top: 1px solid #e2e8f0;
                    flex-shrink: 0;
                }
                #modalUpdate .card {
                    border: 1px solid #cbd5e1;
                    border-radius: 0.45rem;
                    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
                    background: #ffffff;
                }
                #modalUpdate .card-header {
                    background: #f8fafc;
                    border-bottom: 1px solid #e2e8f0;
                    color: #0f172a;
                    font-size: 0.82rem;
                    font-weight: 700;
                    padding: 0.4rem 0.75rem !important;
                }
                #modalUpdate .card-body {
                    padding: 0.65rem 0.8rem !important;
                    overflow: visible !important;
                }
                #modalUpdate .form-label.compact-label {
                    color: #0f172a !important;
                    font-weight: 600 !important;
                    font-size: 0.78rem !important;
                    margin-bottom: 0.2rem !important;
                    display: block;
                }
                #modalUpdate .form-control-sm, 
                #modalUpdate .form-select-sm,
                #modalUpdate .form-control,
                #modalUpdate .form-select {
                    color: #0f172a !important;
                    border-color: #cbd5e1 !important;
                    font-size: 0.82rem !important;
                    font-weight: 500 !important;
                    background-color: #ffffff;
                    padding: 0.28rem 0.55rem !important;
                    min-height: 32px;
                    border-radius: 0.35rem;
                }
                #modalUpdate .form-control:focus, 
                #modalUpdate .form-select:focus {
                    border-color: #2563eb !important;
                    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
                }
                #modalUpdate .form-control[readonly],
                #modalUpdate .form-control:disabled {
                    background-color: #f1f5f9 !important;
                    color: #1e293b !important;
                    font-weight: 600 !important;
                    border-color: #cbd5e1 !important;
                    opacity: 1 !important;
                }
                #modalUpdate textarea.form-control {
                    min-height: 60px;
                    line-height: 1.35;
                    font-size: 0.82rem !important;
                    padding: 0.35rem 0.55rem;
                }
                #modalUpdate .select2-container .select2-selection--single {
                    height: 32px !important;
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 0.35rem !important;
                    background-color: #ffffff !important;
                    display: flex !important;
                    align-items: center !important;
                }
                #modalUpdate .select2-container .select2-selection--single .select2-selection__rendered {
                    color: #0f172a !important;
                    font-size: 0.82rem !important;
                    font-weight: 500 !important;
                    padding-left: 6px !important;
                    line-height: 30px !important;
                }
                #modalUpdate .select2-container .select2-selection--single .select2-selection__placeholder {
                    color: #64748b !important;
                    font-weight: 500 !important;
                    font-size: 0.82rem !important;
                }
                #modalUpdate .select2-container .select2-selection--single .select2-selection__arrow {
                    height: 30px !important;
                    right: 6px !important;
                }
                .select2-dropdown {
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 0.375rem !important;
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15) !important;
                    z-index: 2200 !important;
                }
                .select2-results__option {
                    color: #1e293b !important;
                    font-size: 0.82rem !important;
                    padding: 6px 10px !important;
                    font-weight: 500 !important;
                }
                .select2-results__option--highlighted[aria-selected] {
                    background-color: #2563eb !important;
                    color: #ffffff !important;
                }
                #modalUpdate .ot-client-card {
                    position: relative;
                    z-index: 40;
                    overflow: visible !important;
                }
                #modalUpdate .ot-client-card .card-body,
                #modalUpdate .ot-client-card .position-relative {
                    overflow: visible !important;
                }
                #modalUpdate .ot-client-card .search-list,
                #modalUpdate .search-list {
                    z-index: 2200 !important;
                    width: 100% !important;
                    max-height: 200px !important;
                    overflow-x: hidden !important;
                    overflow-y: auto !important;
                    background: #ffffff !important;
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 0.45rem !important;
                    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18) !important;
                }
                #modalUpdate .ot-details-card {
                    position: relative;
                    z-index: 10;
                }
                @media (max-width: 991.98px) {
                    #modalUpdate .modal-dialog {
                        width: calc(100vw - 1rem);
                        margin: 0.5rem auto;
                    }
                }
                @media (max-width: 767.98px) {
                    #modalUpdate .modal-dialog {
                        width: 100%;
                        margin: 0;
                        height: 100dvh;
                    }
                    #modalUpdate .modal-body {
                        max-height: none;
                    }
                }
            </style>

            <form id="formUpdate" method="post">
                @csrf
                <input type="hidden" id="method_field" name="_method">

                <!-- HEADER -->
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clipboard-plus fs-5 me-2 text-white"></i>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalTitle" style="font-size: 0.95rem;">
                                Nueva Orden de Trabajo
                            </h5>
                            <small class="text-white-50 d-none d-sm-inline" style="font-size: 0.72rem;">Complete los datos del servicio y asigne el personal responsable</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-light py-0 px-2 fw-medium d-inline-flex align-items-center gap-1" id="btnToggleFullscreenOT" title="Pantalla completa / Restaurar tamaño" style="font-size: 0.78rem; height: 28px;">
                            <i class="bi bi-arrows-fullscreen" id="iconFullscreenOT"></i>
                            <span class="d-none d-md-inline" id="textFullscreenOT">Pantalla Completa</span>
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size: 0.8rem;"></button>
                    </div>
                </div>

                <!-- BODY: DISEÑO EN 2 COLUMNAS SIN ESPACIO MUERTO -->
                <div class="modal-body">
                    <div class="row g-2 align-items-stretch">

                        <!-- COLUMNA 1: CLIENTE Y PERSONAL ASIGNADO -->
                        <div class="col-lg-6 col-12 d-flex">
                            <div class="card w-100 shadow-sm bg-white ot-client-card">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-building me-2 text-primary"></i>
                                    <span>1. Cliente y Personal Asignado</span>
                                </div>
                                <div class="card-body d-flex flex-column gap-2">

                                    <!-- Cliente -->
                                    <div class="position-relative">
                                        <label class="form-label compact-label">
                                            Buscar Cliente <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="cliente_razon_social"
                                               class="form-control form-control-sm"
                                               placeholder="Escriba RUC o Razón Social..."
                                               autocomplete="off">
                                        <input type="hidden" id="cliente_id" name="cliente_id">
                                    </div>

                                    <!-- Sede -->
                                    <div>
                                        <label class="form-label compact-label">
                                            Sede <span class="text-danger">*</span>
                                        </label>
                                        <select name="sede_id" id="sede_id" class="form-select form-select-sm" required>
                                            <option value="">Seleccione una sede</option>
                                        </select>
                                    </div>

                                    <!-- Solicitante -->
                                    <div>
                                        <label class="form-label compact-label">
                                            Solicitante <span class="text-danger">*</span>
                                        </label>
                                        <select name="responsable_id" id="responsable_id" class="form-select form-select-sm" required>
                                            <option value="">Seleccione un solicitante</option>
                                        </select>
                                    </div>

                                    <!-- Técnico -->
                                    <div class="position-relative">
                                        <label class="form-label compact-label">
                                            Técnico <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               id="tecnico_nombre"
                                               class="form-control form-control-sm"
                                               placeholder="Buscar técnico por nombre o DNI..."
                                               autocomplete="off">
                                        <input type="hidden" id="tecnico_id" name="tecnico_id">
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- COLUMNA 2: DETALLES OPERATIVOS DE LA OT -->
                        <div class="col-lg-6 col-12 d-flex">
                            <div class="card w-100 shadow-sm bg-white ot-details-card">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-clipboard-check me-2 text-primary"></i>
                                    <span>2. Detalles de la Orden de Trabajo</span>
                                </div>
                                <div class="card-body d-flex flex-column gap-2">

                                    <!-- Código OT y Motivo -->
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label compact-label">
                                                Código OT <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   id="codigo_ot_cliente" 
                                                   name="codigo_ot_cliente" 
                                                   class="form-control form-control-sm"
                                                   placeholder="Ej: OT-2026-001" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label compact-label">
                                                Motivo <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   id="motivo" 
                                                   name="motivo" 
                                                   class="form-control form-control-sm"
                                                   placeholder="Ej: Mantenimiento" required>
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div>
                                        <label class="form-label compact-label">Estado</label>
                                        <input type="text" 
                                               id="estado" 
                                               name="estado"
                                               class="form-control form-control-sm"
                                               readonly
                                               value="Registrado">
                                    </div>

                                    <!-- Descripción -->
                                    <div class="flex-grow-1">
                                        <label class="form-label compact-label">
                                            Descripción <span class="text-danger">*</span>
                                        </label>
                                        <textarea id="descripcion" 
                                                  name="descripcion" 
                                                  class="form-control form-control-sm" 
                                                  rows="2"
                                                  placeholder="Detalle el trabajo o servicio a realizar..." required></textarea>
                                    </div>

                                    <!-- Observaciones -->
                                    <div>
                                        <label class="form-label compact-label">Observaciones</label>
                                        <input type="text" 
                                               id="observaciones" 
                                               name="observaciones" 
                                               class="form-control form-control-sm"
                                               minlength="3"
                                               placeholder="Observaciones o notas especiales...">
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer bg-white border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 fw-medium" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" id="btnSubmit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Orden de Trabajo
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('btnToggleFullscreenOT')?.addEventListener('click', function() {
    const modalDialog = document.querySelector('#modalUpdate .modal-dialog');
    const icon = document.getElementById('iconFullscreenOT');
    const text = document.getElementById('textFullscreenOT');
    if (!modalDialog) return;
    const isFullscreen = modalDialog.classList.toggle('modal-fullscreen');
    if (isFullscreen) {
        if (icon) icon.className = 'bi bi-fullscreen-exit';
        if (text) text.textContent = 'Restaurar';
        this.setAttribute('title', 'Restaurar tamaño normal');
    } else {
        if (icon) icon.className = 'bi bi-arrows-fullscreen';
        if (text) text.textContent = 'Pantalla Completa';
        this.setAttribute('title', 'Pantalla completa');
    }
});
</script>
