@php
    $tipoInforme = $informe->tipo ?? old('informe_tipo');
@endphp

<!-- MODAL INFORME -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-hidden="true">

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
                #modalUpdate .card-header.section-header {
                    background: #f8fafc;
                    border-bottom: 1px solid #e2e8f0;
                    color: #0f172a;
                    font-size: 0.95rem;
                    font-weight: 700;
                    padding: 0.65rem 1rem;
                }
                #modalUpdate .card-header.aspecto-header {
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                    color: #ffffff;
                    padding: 0.75rem 1rem;
                    border-bottom: 1px solid #334155;
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
                #modalUpdate .item-row .input-group-text {
                    background-color: #f8fafc;
                    border-color: #cbd5e1;
                }
                #modalUpdate .item-row textarea {
                    font-weight: 500;
                    color: #0f172a;
                    line-height: 1.4;
                }
            </style>

            <form id="formUpdate" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="method_field">

                <!-- MODAL HEADER -->
                <div class="modal-header py-3 px-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text fs-4 me-2 text-white"></i>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalTitle">Registrar Informe Técnico</h5>
                            <small class="text-white-50" style="font-size: 0.78rem;">Gestión de aspectos técnicos, observaciones y registro fotográfico</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- MODAL BODY -->
                <div class="modal-body p-4 bg-light">

                    <!-- SECCIÓN 1: DATOS DEL CLIENTE -->
                    <div class="card mb-3 shadow-sm bg-white">
                        <div class="card-header section-header d-flex align-items-center">
                            <i class="bi bi-building me-2 text-primary fs-5"></i>
                            <span>1. Datos del Cliente</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="position-relative">
                                <label class="form-label">
                                    Cliente / Empresa <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="cliente_razon_social" 
                                       class="form-control" 
                                       placeholder="Escriba RUC o Razón Social del cliente..."
                                       autocomplete="off">
                                <input type="hidden" name="cliente_id" id="cliente_id">
                                <small class="text-muted" style="font-size: 0.75rem;">Busque el cliente para desplegar sus órdenes de trabajo asociadas</small>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: ORDEN DE TRABAJO -->
                    <div class="card mb-3 shadow-sm bg-white">
                        <div class="card-header section-header d-flex align-items-center">
                            <i class="bi bi-clipboard-check me-2 text-primary fs-5"></i>
                            <span>2. Orden de Trabajo Relacionada</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Seleccione Orden de Trabajo <span class="text-danger">*</span>
                                    </label>
                                    <select id="ot_codigo" name="ot_codigo" class="form-select" required>
                                        <option value="">Seleccione una OT...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado Actual de la OT</label>
                                    <input type="text" id="ot_estado" class="form-control" readonly placeholder="Se cargará al elegir OT">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Descripción del Servicio</label>
                                    <textarea id="ot_descripcion" class="form-control" rows="2" readonly placeholder="Descripción de la orden de trabajo..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: INFORMACIÓN DEL INFORME -->
                    <div class="card mb-3 shadow-sm bg-white">
                        <div class="card-header section-header d-flex align-items-center">
                            <i class="bi bi-info-circle me-2 text-primary fs-5"></i>
                            <span>3. Clasificación del Informe</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tipo de Informe Técnico</label>
                                    <input type="text" id="informe_tipo_text" class="form-control" readonly value="INFORME INICIAL">
                                    <input type="hidden" id="informe_tipo" name="informe_tipo" value="inicial">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Registro</label>
                                    <input type="text" id="fecha_creacion" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: ASPECTOS TÉCNICOS DINÁMICOS -->
                    <div class="d-flex align-items-center mb-2 px-1">
                        <i class="bi bi-layers-fill text-primary me-2"></i>
                        <h6 class="fw-bold text-dark mb-0">4. Aspectos Técnicos y Evaluaciones</h6>
                    </div>

                    @foreach ($aspectosCatalogo as $aspecto)
                        <div class="card mb-3 shadow-sm aspecto-card bg-white"
                             data-aspecto-id="{{ $aspecto->id }}"
                             data-aplica-inicial="{{ $aspecto->aplica_inicial }}"
                             data-aplica-final="{{ $aspecto->aplica_final }}"
                             style="display:none">

                            <div class="card-header aspecto-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary text-white me-2 px-2 py-1">Aspecto {{ $aspecto->orden }}</span>
                                    <span class="fw-bold text-white fs-6">{{ $aspecto->nombre }}</span>
                                </div>
                                <div>
                                    @if ($aspecto->aplica_inicial)
                                        <span class="badge bg-info text-dark me-1" style="font-size: 0.7rem;">Inicial</span>
                                    @endif
                                    @if ($aspecto->aplica_final)
                                        <span class="badge bg-success text-white" style="font-size: 0.7rem;">Final</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body p-3">

                                <!-- ITEMS DEL ASPECTO -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                        <div class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.88rem;">
                                            <i class="bi bi-list-check text-primary me-1"></i> Puntos y Observaciones
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-primary btn-add-item fw-semibold shadow-sm px-3"
                                                data-aspecto="{{ $aspecto->id }}">
                                            <i class="bi bi-plus-lg me-1"></i> Agregar Punto
                                        </button>
                                    </div>
                                    <div class="items-container" data-aspecto="{{ $aspecto->id }}"></div>
                                </div>

                                <!-- IMÁGENES DEL ASPECTO -->
                                @if ($aspecto->permite_imagenes)
                                <div class="mt-4 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-1">
                                        <div class="fw-bold text-dark d-flex align-items-center" style="font-size: 0.88rem;">
                                            <i class="bi bi-camera-fill text-success me-1"></i> Registro Fotográfico
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-success btn-add-grupo fw-semibold shadow-sm px-3"
                                                data-aspecto="{{ $aspecto->id }}">
                                            <i class="bi bi-folder-plus me-1"></i> Nuevo Grupo de Fotos
                                        </button>
                                    </div>
                                    <div class="grupos-container mt-2" id="grupo_container_{{ $aspecto->id }}"></div>
                                </div>
                                @endif

                            </div>
                        </div>
                    @endforeach

                </div>

                <!-- MODAL FOOTER -->
                <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" id="btnSubmit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Guardar Informe Técnico
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>

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
                      placeholder="Escriba el detalle u observación..."
                      ${isNew ? 'required' : ''}>${contenido}</textarea>
            <div class="input-group-text p-1 d-flex flex-column justify-content-center gap-1 bg-light border">
                <button type="button"
                        class="btn btn-sm btn-light border py-0 px-2 btn-item-up fw-bold text-dark"
                        title="Subir">↑</button>
                <button type="button"
                        class="btn btn-sm btn-light border py-0 px-2 btn-item-down fw-bold text-dark"
                        title="Bajar">↓</button>
            </div>
            <button type="button" class="btn btn-outline-danger px-3 btn-remove-item fw-bold" title="Eliminar este punto">✕</button>
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
                        <img src="storage/${img.ruta_imagen}" class="img-fluid rounded" style="max-height:120px; width:100%; object-fit:cover;">
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
                       accept="image/*"
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
document.addEventListener('change', function (e) {
    if (!e.target.classList.contains('input-imagen-grupo')) return;
    const aspectoId = e.target.dataset.aspecto;
    const grupoId = e.target.dataset.grupo;
    Array.from(e.target.files).forEach(file => {
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
    });
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