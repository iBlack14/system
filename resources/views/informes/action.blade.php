@php
    $tipoInforme = $informe->tipo ?? old('informe_tipo');
@endphp

<!-- MODAL INFORME -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content shadow-lg border-0">

            <style>
                #modalUpdate .modal-dialog {
                    width: min(1250px, calc(100vw - 2.5rem));
                    max-width: 1250px;
                    margin: 1rem auto;
                    height: calc(100vh - 2rem);
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
                    height: 100%;
                    border-radius: 0.75rem;
                    overflow: hidden;
                }
                #modalUpdate form {
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                    min-height: 0;
                }
                #modalUpdate .modal-header {
                    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
                    color: #ffffff;
                    border-bottom: none;
                    padding: 0.45rem 1rem !important;
                    flex-shrink: 0;
                }
                #modalUpdate .modal-body {
                    padding: 0.65rem 0.85rem !important;
                    overflow-y: auto;
                    min-height: 0;
                    background-color: #f8fafc;
                    scrollbar-width: thin;
                    scrollbar-color: #94a3b8 #f1f5f9;
                }
                #modalUpdate .modal-body::-webkit-scrollbar {
                    width: 7px;
                    height: 7px;
                }
                #modalUpdate .modal-body::-webkit-scrollbar-track {
                    background: #f1f5f9;
                }
                #modalUpdate .modal-body::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 4px;
                }
                #modalUpdate .modal-body::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }
                #modalUpdate .modal-footer {
                    padding: 0.45rem 1rem !important;
                    position: relative;
                    z-index: 20;
                    box-shadow: 0 -4px 10px rgba(15, 23, 42, 0.05);
                    flex-shrink: 0;
                }

                /* Panel Cabecera Colapsable */
                #modalUpdate .informe-header-panel {
                    border: 1px solid #cbd5e1;
                    border-radius: 0.5rem;
                    position: relative;
                    z-index: 40;
                    overflow: visible !important;
                }
                #modalUpdate .informe-header-panel .card-body {
                    padding: 0.6rem 0.85rem !important;
                    overflow: visible !important;
                }
                #modalUpdate .informe-header-panel .position-relative {
                    overflow: visible !important;
                }
                #modalUpdate .informe-header-panel .search-list,
                #modalUpdate .search-list {
                    z-index: 2200 !important;
                    width: 100% !important;
                    max-height: 220px !important;
                    overflow-x: hidden !important;
                    overflow-y: auto !important;
                    background: #ffffff !important;
                    border: 1px solid #cbd5e1 !important;
                    border-radius: 0.45rem !important;
                    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18) !important;
                }

                /* Formularios compactos */
                #modalUpdate .form-label.compact-label {
                    color: #0f172a !important;
                    font-weight: 600 !important;
                    font-size: 0.78rem !important;
                    margin-bottom: 0.15rem !important;
                    display: block;
                }
                #modalUpdate .form-control-sm, 
                #modalUpdate .form-select-sm {
                    color: #0f172a !important;
                    border-color: #cbd5e1 !important;
                    font-size: 0.82rem !important;
                    font-weight: 500 !important;
                    padding: 0.25rem 0.5rem !important;
                    min-height: 31px;
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

                /* Aspectos */
                #modalUpdate .aspectos-grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 0.65rem;
                    align-items: start;
                }
                #modalUpdate .aspecto-card {
                    margin-bottom: 0 !important;
                    border: 1px solid #cbd5e1;
                    border-radius: 0.45rem;
                    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04) !important;
                    overflow: hidden;
                    background: #ffffff;
                }
                #modalUpdate .card-header.aspecto-header {
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
                    color: #ffffff !important;
                    padding: 0.38rem 0.75rem !important;
                    border-bottom: 1px solid #334155 !important;
                    user-select: none;
                }
                #modalUpdate .aspecto-header .aspecto-toggle-icon {
                    transition: transform 0.2s ease;
                }
                #modalUpdate .aspecto-card.collapsed .aspecto-toggle-icon {
                    transform: rotate(-90deg);
                }
                #modalUpdate .aspecto-card.collapsed .aspecto-body {
                    display: none;
                }
                #modalUpdate .aspecto-card .card-body {
                    padding: 0.55rem 0.75rem !important;
                }

                /* Item Row */
                #modalUpdate .item-row {
                    margin-bottom: 0.45rem !important;
                }
                #modalUpdate .item-row textarea {
                    font-weight: 500;
                    color: #0f172a;
                    line-height: 1.35;
                    font-size: 0.83rem;
                    padding: 0.3rem 0.5rem;
                    min-height: 44px;
                    border-color: #cbd5e1;
                }
                #modalUpdate .item-row .input-group-text {
                    background-color: #f8fafc;
                    border-color: #cbd5e1;
                    padding: 2px 3px;
                }
                #modalUpdate .item-row .btn-item-up,
                #modalUpdate .item-row .btn-item-down {
                    padding: 1px 5px !important;
                    font-size: 0.72rem !important;
                    line-height: 1.2;
                }
                #modalUpdate .item-row .btn-remove-item {
                    padding: 0 10px !important;
                    font-size: 0.82rem !important;
                    font-weight: 700;
                }

                /* Grupos de Imágenes */
                #modalUpdate .grupo-imagen {
                    margin-bottom: 0.5rem !important;
                    border-color: #cbd5e1;
                    border-radius: 0.4rem;
                }
                #modalUpdate .grupo-imagen .card-header {
                    padding: 0.3rem 0.65rem !important;
                    font-size: 0.8rem !important;
                    background: #f8fafc;
                }
                #modalUpdate .grupo-imagen .card-body {
                    padding: 0.5rem 0.65rem !important;
                }
                #modalUpdate .grupo-preview {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.45rem;
                    margin-left: 0;
                    margin-right: 0;
                }
                #modalUpdate .grupo-preview .imagen-item {
                    flex: 0 0 95px;
                    width: 95px;
                    max-width: 95px;
                    margin: 0 !important;
                    padding: 0;
                }
                #modalUpdate .grupo-preview .imagen-item img {
                    width: 100% !important;
                    height: 72px !important;
                    max-height: 72px !important;
                    object-fit: cover;
                }
                #modalUpdate .btn-remove-imagen,
                #modalUpdate .btn-remove-img {
                    width: 22px;
                    height: 22px;
                    padding: 0 !important;
                    font-size: 0.68rem;
                }

                @media (max-width: 1199.98px) {
                    #modalUpdate .aspectos-grid {
                        grid-template-columns: 1fr;
                    }
                }
                @media (max-width: 767.98px) {
                    #modalUpdate .modal-dialog {
                        width: 100%;
                        margin: 0;
                        height: 100dvh;
                    }
                    #modalUpdate .modal-header {
                        padding: 0.4rem 0.65rem !important;
                    }
                    #modalUpdate .modal-body {
                        padding: 0.5rem !important;
                    }
                    #modalUpdate .modal-footer {
                        padding: 0.45rem 0.65rem !important;
                    }
                }
            </style>

            <form id="formUpdate" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="method_field">

                <!-- MODAL HEADER -->
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text fs-5 me-2 text-white"></i>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalTitle" style="font-size: 0.95rem;">Registrar Informe</h5>
                            <small class="text-white-50 d-none d-sm-inline" style="font-size: 0.72rem;">Gestión de aspectos técnicos, observaciones y registro fotográfico</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-light py-0 px-2 fw-medium d-inline-flex align-items-center gap-1" id="btnToggleFullscreen" title="Pantalla completa / Restaurar tamaño" style="font-size: 0.78rem; height: 28px;">
                            <i class="bi bi-arrows-fullscreen" id="iconFullscreen"></i>
                            <span class="d-none d-md-inline" id="textFullscreen">Pantalla Completa</span>
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size: 0.8rem;"></button>
                    </div>
                </div>

                <!-- MODAL BODY -->
                <div class="modal-body">

                    <!-- PANEL DE CABECERA COLAPSABLE: CLIENTE / OT / INFORME -->
                    <div class="card mb-2 shadow-sm bg-white informe-header-panel">
                        <!-- BARRA SUPERIOR RESUMEN / CONTROL COLAPSABLE -->
                        <div class="card-header py-1 px-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2" style="min-height: 38px;">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="fw-bold text-primary d-flex align-items-center" style="font-size: 0.82rem;">
                                    <i class="bi bi-briefcase-fill me-1"></i> Datos de la Orden
                                </span>

                                <!-- BADGES RESUMEN EN VIVO -->
                                <div class="d-flex align-items-center flex-wrap gap-1" id="resumenBadgesContainer">
                                    <span class="badge bg-white text-dark border text-truncate" style="max-width: 280px; font-weight: 500;" title="Cliente">
                                        <i class="bi bi-building text-primary me-1"></i><span id="badgeCliente">Sin cliente</span>
                                    </span>
                                    <span class="badge bg-white text-dark border" style="font-weight: 500;" title="Orden de Trabajo">
                                        <i class="bi bi-clipboard-check text-success me-1"></i>OT: <span id="badgeOT">Sin OT</span>
                                    </span>
                                    <span class="badge bg-white text-dark border" style="font-weight: 500;" title="Estado OT">
                                        <i class="bi bi-shield-check text-info me-1"></i><span id="badgeEstado">--</span>
                                    </span>
                                    <span class="badge bg-white text-primary border" style="font-weight: 600;" title="Tipo de Informe">
                                        <i class="bi bi-file-earmark-text me-1"></i><span id="badgeTipo">INFORME INICIAL</span>
                                    </span>
                                    <span class="badge bg-white text-secondary border" style="font-weight: 500;" title="Fecha">
                                        <i class="bi bi-calendar3 me-1"></i><span id="badgeFecha">--</span>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold d-inline-flex align-items-center gap-1" id="btnToggleResumen" style="font-size: 0.76rem; height: 26px;">
                                    <i class="bi bi-chevron-up" id="iconToggleResumen"></i>
                                    <span id="textToggleResumen">Ocultar campos</span>
                                </button>
                            </div>
                        </div>

                        <!-- CUERPO COLAPSABLE CON LOS CAMPOS -->
                        <div class="card-body" id="resumenCamposBody">
                            <div class="row g-2 align-items-start">
                                <!-- 1. Cliente -->
                                <div class="col-12 col-md-4 position-relative">
                                    <label class="form-label compact-label">
                                        Cliente <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           id="cliente_razon_social" 
                                           class="form-control form-control-sm" 
                                           placeholder="Buscar RUC o Razón Social..."
                                           autocomplete="off">
                                    <input type="hidden" name="cliente_id" id="cliente_id">
                                </div>

                                <!-- 2. Orden de Trabajo -->
                                <div class="col-12 col-md-3">
                                    <label class="form-label compact-label">
                                        Orden de Trabajo <span class="text-danger">*</span>
                                    </label>
                                    <select id="ot_codigo" name="ot_codigo" class="form-select form-select-sm" required>
                                        <option value="">Seleccione una OT...</option>
                                    </select>
                                </div>

                                <!-- 3. Estado OT -->
                                <div class="col-6 col-md-2">
                                    <label class="form-label compact-label">Estado OT</label>
                                    <input type="text" id="ot_estado" class="form-control form-control-sm" readonly placeholder="Al elegir OT">
                                </div>

                                <!-- 4. Tipo de Informe -->
                                <div class="col-6 col-md-1">
                                    <label class="form-label compact-label">Tipo</label>
                                    <input type="text" id="informe_tipo_text" class="form-control form-control-sm" readonly value="INFORME INICIAL">
                                    <input type="hidden" id="informe_tipo" name="informe_tipo" value="inicial">
                                </div>

                                <!-- 5. Fecha -->
                                <div class="col-12 col-md-2">
                                    <label class="form-label compact-label">Fecha Creación</label>
                                    <input type="text" id="fecha_creacion" class="form-control form-control-sm" readonly>
                                </div>

                                <!-- 6. Descripción de OT -->
                                <div class="col-12 mt-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="form-label compact-label mb-0 text-nowrap" style="min-width: 85px;">Desc. OT:</label>
                                        <input type="text" id="ot_descripcion" class="form-control form-control-sm py-0" readonly placeholder="Descripción de la orden de trabajo...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BARRA DE CONTROL DE ASPECTOS TÉCNICOS -->
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1 flex-wrap gap-2">
                        <span class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.84rem;">
                            <i class="bi bi-card-checklist text-primary me-1"></i> Aspectos Técnicos del Informe
                        </span>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-xs btn-light border py-0 px-2 fw-medium text-secondary" id="btnExpandAllAspectos" style="font-size: 0.74rem; height: 25px;">
                                <i class="bi bi-arrows-expand me-1"></i>Expandir todos
                            </button>
                            <button type="button" class="btn btn-xs btn-light border py-0 px-2 fw-medium text-secondary" id="btnCollapseAllAspectos" style="font-size: 0.74rem; height: 25px;">
                                <i class="bi bi-arrows-collapse me-1"></i>Colapsar todos
                            </button>
                        </div>
                    </div>

                    <!-- GRID DE ASPECTOS -->
                    <div class="aspectos-grid">
                    @foreach ($aspectosCatalogo as $aspecto)
                        <div class="card mb-2 shadow-sm aspecto-card bg-white"
                             data-aspecto-id="{{ $aspecto->id }}"
                             data-aplica-inicial="{{ $aspecto->aplica_inicial }}"
                             data-aplica-final="{{ $aspecto->aplica_final }}"
                             style="display:none">

                            <div class="card-header aspecto-header d-flex justify-content-between align-items-center"
                                 role="button"
                                 title="Clic para plegar / desplegar aspecto"
                                 onclick="toggleAspectoCard(this)">
                                <div class="d-flex align-items-center text-truncate pe-2">
                                    <i class="bi bi-chevron-down me-2 aspecto-toggle-icon" style="font-size: 0.75rem;"></i>
                                    <span class="fw-bold text-white text-truncate" style="font-size: 0.86rem;">{{ $aspecto->orden }}.- {{ $aspecto->nombre }}</span>
                                </div>

                                <div class="d-flex align-items-center gap-1 flex-shrink-0" onclick="event.stopPropagation();">
                                    <button type="button"
                                            class="btn btn-xs btn-primary btn-add-item fw-semibold py-0 px-2 shadow-sm"
                                            data-aspecto="{{ $aspecto->id }}"
                                            style="font-size: 0.74rem; height: 24px;">
                                        <i class="bi bi-plus-lg me-1"></i> Item
                                    </button>

                                    @if ($aspecto->permite_imagenes)
                                    <button type="button"
                                            class="btn btn-xs btn-success btn-add-grupo fw-semibold py-0 px-2 shadow-sm"
                                            data-aspecto="{{ $aspecto->id }}"
                                            style="font-size: 0.74rem; height: 24px;">
                                        <i class="bi bi-folder-plus me-1"></i> Fotos
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body aspecto-body">

                                <!-- ITEMS DEL ASPECTO -->
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                        <div class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.8rem;">
                                            <i class="bi bi-list-check text-primary me-1"></i> Items
                                        </div>
                                        <button type="button"
                                                class="btn btn-xs btn-outline-primary btn-add-item fw-semibold py-0 px-2"
                                                data-aspecto="{{ $aspecto->id }}"
                                                style="font-size: 0.72rem; height: 22px;">
                                            <i class="bi bi-plus-lg me-1"></i> Agregar item
                                        </button>
                                    </div>
                                    <div class="items-container" data-aspecto="{{ $aspecto->id }}"></div>
                                </div>

                                <!-- IMÁGENES DEL ASPECTO -->
                                @if ($aspecto->permite_imagenes)
                                <div class="mt-3 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1">
                                        <div class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.8rem;">
                                            <i class="bi bi-camera-fill text-success me-1"></i> Imágenes
                                        </div>
                                        <button type="button"
                                                class="btn btn-xs btn-outline-success btn-add-grupo fw-semibold py-0 px-2"
                                                data-aspecto="{{ $aspecto->id }}"
                                                style="font-size: 0.72rem; height: 22px;">
                                            <i class="bi bi-folder-plus me-1"></i> Nuevo grupo
                                        </button>
                                    </div>
                                    <div class="grupos-container mt-1" id="grupo_container_{{ $aspecto->id }}"></div>
                                </div>
                                @endif

                            </div>
                        </div>
                    @endforeach
                    </div>

                </div>

                <!-- MODAL FOOTER -->
                <div class="modal-footer bg-white border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 fw-medium" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" id="btnSubmit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Informe
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>

/* =========================================================
   PANTALLA COMPLETA / FULLSCREEN TOGGLE
========================================================= */
function toggleFullscreenModal() {
    const modalDialog = document.querySelector('#modalUpdate .modal-dialog');
    const btn = document.getElementById('btnToggleFullscreen');
    const icon = document.getElementById('iconFullscreen');
    const text = document.getElementById('textFullscreen');

    if (!modalDialog) return;

    const isFullscreen = modalDialog.classList.toggle('modal-fullscreen');

    if (isFullscreen) {
        if (icon) icon.className = 'bi bi-fullscreen-exit';
        if (text) text.textContent = 'Restaurar';
        btn?.setAttribute('title', 'Restaurar tamaño normal');
        localStorage.setItem('informe_modal_fullscreen', '1');
    } else {
        if (icon) icon.className = 'bi bi-arrows-fullscreen';
        if (text) text.textContent = 'Pantalla Completa';
        btn?.setAttribute('title', 'Pantalla completa');
        localStorage.setItem('informe_modal_fullscreen', '0');
    }
}

document.getElementById('btnToggleFullscreen')?.addEventListener('click', toggleFullscreenModal);

// Restaurar preferencia guardada al abrir el modal
document.getElementById('modalUpdate')?.addEventListener('show.bs.modal', function () {
    const prefersFullscreen = localStorage.getItem('informe_modal_fullscreen') === '1';
    const modalDialog = document.querySelector('#modalUpdate .modal-dialog');
    const icon = document.getElementById('iconFullscreen');
    const text = document.getElementById('textFullscreen');

    if (modalDialog && prefersFullscreen) {
        modalDialog.classList.add('modal-fullscreen');
        if (icon) icon.className = 'bi bi-fullscreen-exit';
        if (text) text.textContent = 'Restaurar';
    }
    actualizarBadgesResumen();
});

/* =========================================================
   COLAPSAR / EXPANDIR CABECERA (DATOS DE LA ORDEN)
========================================================= */
function setResumenCollapsed(collapsed) {
    const body = document.getElementById('resumenCamposBody');
    const icon = document.getElementById('iconToggleResumen');
    const text = document.getElementById('textToggleResumen');
    const btn = document.getElementById('btnToggleResumen');

    if (!body) return;

    if (collapsed) {
        body.style.display = 'none';
        if (icon) icon.className = 'bi bi-chevron-down';
        if (text) text.textContent = 'Mostrar campos';
        btn?.classList.remove('btn-outline-primary');
        btn?.classList.add('btn-primary');
    } else {
        body.style.display = '';
        if (icon) icon.className = 'bi bi-chevron-up';
        if (text) text.textContent = 'Ocultar campos';
        btn?.classList.add('btn-outline-primary');
        btn?.classList.remove('btn-primary');
    }
}

document.getElementById('btnToggleResumen')?.addEventListener('click', function () {
    const body = document.getElementById('resumenCamposBody');
    const isHidden = body && body.style.display === 'none';
    setResumenCollapsed(!isHidden);
});

/* =========================================================
   ACTUALIZAR BADGES RESUMEN EN TIEMPO REAL
========================================================= */
function actualizarBadgesResumen() {
    const cliente = document.getElementById('cliente_razon_social')?.value?.trim();
    const otSelect = document.getElementById('ot_codigo');
    let otText = '';
    if (otSelect && otSelect.selectedIndex >= 0 && otSelect.options[otSelect.selectedIndex]) {
        otText = otSelect.options[otSelect.selectedIndex].text;
    }
    const estado = document.getElementById('ot_estado')?.value?.trim();
    const tipo = document.getElementById('informe_tipo_text')?.value?.trim();
    const fecha = document.getElementById('fecha_creacion')?.value?.trim();

    const badgeCliente = document.getElementById('badgeCliente');
    if (badgeCliente) badgeCliente.textContent = cliente || 'Sin cliente';

    const badgeOT = document.getElementById('badgeOT');
    if (badgeOT) badgeOT.textContent = (otText && !otText.includes('Seleccione')) ? otText : 'Sin OT';

    const badgeEstado = document.getElementById('badgeEstado');
    if (badgeEstado) badgeEstado.textContent = estado || '--';

    const badgeTipo = document.getElementById('badgeTipo');
    if (badgeTipo) badgeTipo.textContent = tipo || 'INFORME';

    const badgeFecha = document.getElementById('badgeFecha');
    if (badgeFecha) badgeFecha.textContent = fecha || '--';
}

// Escuchar cambios en cliente y fecha
document.getElementById('cliente_razon_social')?.addEventListener('input', actualizarBadgesResumen);
document.getElementById('fecha_creacion')?.addEventListener('change', actualizarBadgesResumen);

/* =========================================================
   ACORDEÓN DE ASPECTOS TÉCNICOS
========================================================= */
function toggleAspectoCard(headerEl) {
    const card = headerEl.closest('.aspecto-card');
    if (card) {
        card.classList.toggle('collapsed');
    }
}

document.getElementById('btnExpandAllAspectos')?.addEventListener('click', function () {
    document.querySelectorAll('.aspecto-card').forEach(card => card.classList.remove('collapsed'));
});

document.getElementById('btnCollapseAllAspectos')?.addEventListener('click', function () {
    document.querySelectorAll('.aspecto-card').forEach(card => card.classList.add('collapsed'));
});

/* ===========================
   HELPER: HTML DE UN ITEM
=========================== */
function buildItemHTML(aspectoId, itemId, contenido = '', isNew = false) {
    return `
        <div class="input-group shadow-sm mb-2 item-row">
            <input type="hidden"
                   name="items[${aspectoId}][${itemId}][id]"
                   value="${isNew ? '' : itemId}">
            <textarea name="items[${aspectoId}][${itemId}][contenido]"
                      class="form-control" rows="2"
                      placeholder="Escriba el item..."
                      ${isNew ? 'required' : ''}>${contenido}</textarea>
            <div class="input-group-text p-1 d-flex flex-column justify-content-center gap-1 bg-light border">
                <button type="button"
                        class="btn btn-sm btn-light border py-0 px-2 btn-item-up fw-bold text-dark"
                        title="Subir">↑</button>
                <button type="button"
                        class="btn btn-sm btn-light border py-0 px-2 btn-item-down fw-bold text-dark"
                        title="Bajar">↓</button>
            </div>
            <button type="button" class="btn btn-outline-danger px-3 btn-remove-item fw-bold">✕</button>
        </div>`;
}

/* ===========================
   MOSTRAR ASPECTOS POR ESTADO
=========================== */
function aplicarAspectosPorEstado(estado) {
    const numEstado = parseInt(estado) || 1;
    document.querySelectorAll('.aspecto-card').forEach(card => {
        const aplicaInicial = card.dataset.aplicaInicial == "1";
        const aplicaFinal   = card.dataset.aplicaFinal == "1";
        card.style.display = 'none';

        if (numEstado <= 3 && aplicaInicial) {
            card.style.display = 'block';
        } else if (numEstado >= 4 && numEstado < 8 && (aplicaInicial || aplicaFinal)) {
            card.style.display = 'block';
        } else if (numEstado >= 8) {
            card.style.display = 'block';
        }
    });
}

/* ===========================
   CAMBIO DE OT
=========================== */
document.addEventListener('change', async function (e) {
    if (e.target.id !== 'ot_codigo') return;
    const otId = e.target.value;
    if (!otId) return;

    try {
        const response = await fetch(`{{ url('ordenestrabajo/detalle') }}/${otId}`);
        if (!response.ok) throw new Error('Respuesta inválida');
        const data = await response.json();

        document.getElementById('ot_descripcion').value = data.descripcion ?? '';
        document.getElementById('ot_estado').value = data.estado_nombre 
            ? `${data.estado} - ${data.estado_nombre}` 
            : (data.estado ?? 'Sin estado');

        let tipo = '', tipoTexto = '';
        if (data.estado <= 3) {
            tipo = 'inicial'; 
            tipoTexto = 'INFORME INICIAL';
        } else {
            tipo = 'final';   
            tipoTexto = 'INFORME FINAL';
        }
        document.getElementById('informe_tipo').value = tipo;
        document.getElementById('informe_tipo_text').value = tipoTexto;

        aplicarAspectosPorEstado(data.estado);
        actualizarBadgesResumen();

    } catch (error) {
        console.error('Error al cargar OT', error);
    }
});

/* =========================
   AGREGAR / SUBIR / BAJAR ITEM
========================= */
document.addEventListener('click', function (e) {

    if (e.target.closest('.btn-add-item')) {
        const btn = e.target.closest('.btn-add-item');
        const aspectoId = btn.dataset.aspecto;
        const container = document.querySelector(`.items-container[data-aspecto="${aspectoId}"]`);
        if (!container) return;
        const uniqueId = 'new_' + Date.now();
        const div = document.createElement('div');
        div.classList.add('item-row');
        div.innerHTML = buildItemHTML(aspectoId, uniqueId, '', true);
        container.appendChild(div);
        div.querySelector('textarea')?.focus();
    }

    if (e.target.closest('.btn-remove-item')) {
        const btn = e.target.closest('.btn-remove-item');
        btn.closest('.item-row')?.remove();
    }

    if (e.target.closest('.btn-item-up')) {
        const btn = e.target.closest('.btn-item-up');
        const item = btn.closest('.item-row');
        const anterior = item.previousElementSibling;
        if (anterior && anterior.classList.contains('item-row')) {
            item.parentNode.insertBefore(item, anterior);
        }
    }

    if (e.target.closest('.btn-item-down')) {
        const btn = e.target.closest('.btn-item-down');
        const item = btn.closest('.item-row');
        const siguiente = item.nextElementSibling;
        if (siguiente && siguiente.classList.contains('item-row')) {
            item.parentNode.insertBefore(siguiente, item);
        }
    }
});

/* =========================================================
   CARGAR ASPECTOS + GRUPOS + IMÁGENES (EDITAR)
========================================================= */
function cargarAspectosInforme(data) {

    if (!Array.isArray(data.aspectos)) return;

    data.aspectos.forEach(aspecto => {

        const aspectoId = aspecto.aspecto_id;
        const card = document.querySelector(`.aspecto-card[data-aspecto-id="${aspectoId}"]`);

        if (!card) {
            console.warn('No existe card para aspecto:', aspectoId);
            return;
        }

        /* ITEMS */
        const itemsContainer = card.querySelector('.items-container');
        if (itemsContainer) {
            itemsContainer.innerHTML = '';
            (aspecto.items || []).forEach(item => {
                const div = document.createElement('div');
                div.classList.add('item-row');
                div.innerHTML = buildItemHTML(aspectoId, item.id, item.contenido ?? '', false);
                itemsContainer.appendChild(div);
            });
        }

        /* GRUPOS + IMÁGENES */
        const grupoContainer = document.getElementById(`grupo_container_${aspectoId}`);
        if (!grupoContainer) return;

        grupoContainer.innerHTML = '';

        (aspecto.grupos || []).forEach(grupo => {

            crearGrupo(aspectoId, grupo.grupo_id);

            const grupoDiv = grupoContainer.querySelector(`.grupo-imagen[data-grupo="${grupo.grupo_id}"]`);
            if (!grupoDiv) return;

            const descInput = grupoDiv.querySelector('.grupo-descripcion');
            if (descInput) descInput.value = grupo.descripcion ?? '';

            const preview = grupoDiv.querySelector('.grupo-preview');

            (grupo.imagenes || []).forEach(img => {
                const col = document.createElement('div');
                col.classList.add('col-md-3', 'mb-2', 'imagen-item');
                col.innerHTML = `
                    <div class="border rounded p-1 position-relative shadow-sm bg-white">
                        <img src="{{ url('informes') }}/${data.id}?imagen=${img.id}" class="img-fluid rounded" style="max-height:120px; width:100%; object-fit:cover;" alt="Fotografía del informe">
                        <button type="button"
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 btn-remove-imagen rounded-circle m-1"
                                data-id="${img.id}" title="Eliminar foto">✕</button>
                    </div>`;
                preview.appendChild(col);
            });
        });
    });
}

/* =========================
   CREAR GRUPO DE IMÁGENES
========================= */
function crearGrupo(aspectoId, grupoId) {

    const container = document.getElementById('grupo_container_' + aspectoId);
    if (!container) return;

    const div = document.createElement('div');
    div.classList.add('card', 'mb-3', 'grupo-imagen', 'border', 'shadow-sm');
    div.dataset.grupo = grupoId;

    div.innerHTML = `
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
            <span class="fw-bold text-dark" style="font-size:0.85rem;"><i class="bi bi-images me-1 text-success"></i> Grupo de Fotografías</span>
            <div>
                <button type="button" class="btn btn-sm btn-light border btn-grupo-up py-0 px-2 fw-bold" title="Subir">↑</button>
                <button type="button" class="btn btn-sm btn-light border btn-grupo-down py-0 px-2 fw-bold" title="Bajar">↓</button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-grupo py-0 px-2 ms-2 fw-bold" title="Eliminar Grupo">✕</button>
            </div>
        </div>
        <div class="card-body p-3 bg-white">
            <div class="mb-2">
                <label class="form-label mb-1" style="font-size:0.8rem;">Título / Descripción del Grupo</label>
                <input type="text"
                       class="form-control form-control-sm grupo-descripcion"
                       placeholder="Ej: Tablero Principal, Inspección de Bobinado, Conexiones...">
            </div>
            <div class="mb-2">
                <label class="form-label mb-1" style="font-size:0.8rem;">Seleccionar Fotografías</label>
                <input type="file"
                       class="form-control form-control-sm input-imagen-grupo"
                       data-aspecto="${aspectoId}"
                       data-grupo="${grupoId}"
                       accept="image/*,.heic,.heif,image/heic,image/heif"
                       multiple>
            </div>
            <div class="row g-2 grupo-preview mt-2" id="preview_${grupoId}"></div>
        </div>`;

    container.appendChild(div);

    new Sortable(document.getElementById('preview_' + grupoId), { animation: 150 });
}

/* =========================
   SUBIR / BAJAR GRUPO
========================= */
document.addEventListener('click', function (e) {

    if (e.target.closest('.btn-grupo-up')) {
        const btn = e.target.closest('.btn-grupo-up');
        const grupo = btn.closest('.grupo-imagen');
        const anterior = grupo.previousElementSibling;
        if (anterior && anterior.classList.contains('grupo-imagen')) {
            grupo.parentNode.insertBefore(grupo, anterior);
        }
        return;
    }

    if (e.target.closest('.btn-grupo-down')) {
        const btn = e.target.closest('.btn-grupo-down');
        const grupo = btn.closest('.grupo-imagen');
        const siguiente = grupo.nextElementSibling;
        if (siguiente && siguiente.classList.contains('grupo-imagen')) {
            grupo.parentNode.insertBefore(siguiente, grupo);
        }
        return;
    }
});

/* =========================
   ELIMINAR GRUPO
========================= */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete-grupo');
    if (!btn) return;
    const grupo = btn.closest('.grupo-imagen');
    const grupoId = grupo.dataset.grupo;
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'grupos_eliminar[]';
    input.value = grupoId;
    document.getElementById('formUpdate').appendChild(input);
    grupo.remove();
});

/* =========================
   ELIMINAR IMAGEN EXISTENTE
========================= */
function handleRemoveImagen(e) {
    const btn = e.target.closest('.btn-remove-imagen');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const imgId = btn.dataset.id;
    const imgItem = btn.closest('.imagen-item');
    if (!imgItem) return;

    imgItem.style.opacity = '0.4';
    imgItem.style.pointerEvents = 'none';

    if (!document.querySelector(`input[name="imagenes_eliminar[]"][value="${imgId}"]`)) {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'imagenes_eliminar[]';
        hidden.value = imgId;
        document.getElementById('formUpdate').appendChild(hidden);
    }
}

document.addEventListener('click', handleRemoveImagen);
document.addEventListener('touchend', handleRemoveImagen, { passive: false });

/* =========================
   ELIMINAR IMAGEN NUEVA
========================= */
function handleRemoveImgNueva(e) {
    const btn = e.target.closest('.btn-remove-img');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    btn.closest('.imagen-item')?.remove();
}

document.addEventListener('click', handleRemoveImgNueva);
document.addEventListener('touchend', handleRemoveImgNueva, { passive: false });

/* =========================
   AGREGAR GRUPO
========================= */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-add-grupo');
    if (!btn) return;
    const aspectoId = btn.dataset.aspecto;
    const grupoId = 'grupo_' + Date.now();
    crearGrupo(aspectoId, grupoId);
});

/* =========================
   PREVIEW IMÁGENES NUEVAS
========================= */
document.addEventListener('change', async function (e) {
    if (!e.target.classList.contains('input-imagen-grupo')) return;
    const aspectoId = e.target.dataset.aspecto;
    const grupoId = e.target.dataset.grupo;

    for (const originalFile of Array.from(e.target.files)) {
        let file = originalFile;
        const isHeic = /\.(heic|heif)$/i.test(originalFile.name)
            || ['image/heic', 'image/heif', 'image/heic-sequence', 'image/heif-sequence'].includes(originalFile.type);

        if (isHeic) {
            try {
                if (typeof window.convertHeicToJpeg !== 'function') {
                    throw new Error('El conversor HEIC no está disponible');
                }

                let converted = await window.convertHeicToJpeg({
                    blob: originalFile,
                    toType: 'image/jpeg',
                    quality: 0.85
                });

                if (Array.isArray(converted)) converted = converted[0];

                const jpegName = originalFile.name.replace(/\.(heic|heif)$/i, '') + '.jpg';
                file = new File([converted], jpegName, {
                    type: 'image/jpeg',
                    lastModified: originalFile.lastModified
                });
            } catch (error) {
                console.error('No se pudo convertir la imagen HEIC:', error);
                Swal.fire({
                    icon: 'error',
                    title: `No se pudo convertir ${originalFile.name}`,
                    text: 'Pruebe con otra fotografía HEIC o conviértala a JPG.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000
                });
                continue;
            }
        }

        const reader = new FileReader();
        reader.onload = function (ev) {
            const preview = document.getElementById('preview_' + grupoId);
            const col = document.createElement('div');
            col.classList.add('col-md-3', 'mb-2', 'imagen-item');
            col.innerHTML = `
                <div class="border rounded p-1 position-relative shadow-sm bg-white">
                    <img src="${ev.target.result}" class="img-fluid rounded" style="max-height:120px; width:100%; object-fit:cover;">
                    <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 btn-remove-img rounded-circle m-1" title="Eliminar">✕</button>
                </div>`;
            const input = document.createElement('input');
            input.type = 'file';
            input.style.display = 'none';
            input.name = `imagenes[${aspectoId}][${grupoId}][]`;
            input.files = createFileList(file);
            col.appendChild(input);
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    }
});

/* =========================
   SUBMIT — capturar orden grupos + items
========================= */
document.getElementById('formUpdate').addEventListener('submit', function () {

    const form = this;

    // Limpiar inputs previos para evitar duplicados
    form.querySelectorAll(
        'input[name^="orden_grupo"], input[name^="descripcion_grupo"], input[name^="orden_imagen_id"]'
    ).forEach(el => el.remove());

    /* ORDEN GRUPOS */
    document.querySelectorAll('.grupo-imagen').forEach((grupo, index) => {

        const grupoId = grupo.dataset.grupo;
        const descripcion = grupo.querySelector('.grupo-descripcion')?.value ?? '';

        const inputDesc = document.createElement('input');
        inputDesc.type = 'hidden';
        inputDesc.name = `descripcion_grupo[${grupoId}]`;
        inputDesc.value = descripcion;
        form.appendChild(inputDesc);

        const inputOrden = document.createElement('input');
        inputOrden.type = 'hidden';
        inputOrden.name = `orden_grupo[${grupoId}]`;
        inputOrden.value = index + 1;
        form.appendChild(inputOrden);

        grupo.querySelectorAll('.imagen-item').forEach((img) => {
            const btn = img.querySelector('.btn-remove-imagen');
            if (btn && btn.dataset.id) {
                const inputImg = document.createElement('input');
                inputImg.type = 'hidden';
                inputImg.name = `orden_imagen_id[${grupoId}][]`;
                inputImg.value = btn.dataset.id;
                form.appendChild(inputImg);
            }
        });
    });

    /* ORDEN ITEMS — reescribir name con orden visual actual */
    document.querySelectorAll('.items-container').forEach(container => {

        const aspectoId = container.dataset.aspecto;

        container.querySelectorAll('.item-row').forEach((row, index) => {

            const hiddenId = row.querySelector('input[type="hidden"]');
            const textarea = row.querySelector('textarea');

            if (!hiddenId || !textarea) return;

            const itemId = hiddenId.value || ('new_' + Date.now() + '_' + index);
            const newIndex = index + 1; // orden visual

            hiddenId.name = `items[${aspectoId}][${newIndex}][id]`;
            textarea.name = `items[${aspectoId}][${newIndex}][contenido]`;
        });
    });
});

/* =========================
   HELPERS
========================= */
function createFileList(file) {
    const dt = new DataTransfer();
    dt.items.add(file);
    return dt.files;
}

</script>
