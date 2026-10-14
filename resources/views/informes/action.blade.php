@php
    $tipoInforme = $informe->tipo ?? old('informe_tipo');
@endphp

<!-- MODAL -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form id="formUpdate" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="method_field">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Registrar Informe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="card mb-3 shadow-sm">
                        <div class="card-header bg-primary text-white">Datos del Cliente</div>
                        <div class="card-body">
                            <label class="form-label">Cliente</label>
                            <input type="text" id="cliente_razon_social" class="form-control" autocomplete="off">
                            <input type="hidden" name="cliente_id" id="cliente_id">
                        </div>
                    </div>

                    <div class="card mb-3 shadow-sm">
                        <div class="card-header bg-primary text-white">Orden de Trabajo</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Orden de Trabajo</label>
                                    <select id="ot_codigo" name="ot_codigo" class="form-select" required>
                                        <option value="">Seleccione OT</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado</label>
                                    <input type="text" id="ot_estado" class="form-control" readonly>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">Descripción</label>
                                    <textarea id="ot_descripcion" class="form-control" rows="3" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3 shadow-sm">
                        <div class="card-header bg-primary text-white">Información del Informe</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Tipo de Informe</label>
                                    <input type="text" id="informe_tipo_text" class="form-control" readonly>
                                    <input type="hidden" id="informe_tipo" name="informe_tipo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha de Creación</label>
                                    <input type="text" id="fecha_creacion" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($aspectosCatalogo as $aspecto)
                        <div class="card mb-3 shadow-sm aspecto-card"
                             data-aspecto-id="{{ $aspecto->id }}"
                             data-aplica-inicial="{{ $aspecto->aplica_inicial }}"
                             data-aplica-final="{{ $aspecto->aplica_final }}"
                             style="display:none">

                            <div class="card-header bg-secondary text-white">
                                {{ $aspecto->orden }}.- {{ $aspecto->nombre }}
                            </div>

                            <div class="card-body">

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Items</strong>
                                        <button type="button"
                                                class="btn btn-sm btn-primary btn-add-item"
                                                data-aspecto="{{ $aspecto->id }}">
                                            + Agregar item
                                        </button>
                                    </div>
                                    <div class="items-container" data-aspecto="{{ $aspecto->id }}"></div>
                                </div>

                                @if ($aspecto->permite_imagenes)
                                <div class="mb-3">
                                    <label class="form-label">Imágenes</label>
                                    <button type="button"
                                            class="btn btn-sm btn-success mb-2 btn-add-grupo"
                                            data-aspecto="{{ $aspecto->id }}">
                                        + Nuevo grupo
                                    </button>
                                    <div class="grupos-container mt-3" id="grupo_container_{{ $aspecto->id }}"></div>
                                </div>
                                @endif

                            </div>
                        </div>
                    @endforeach

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnSubmit" class="btn btn-primary">Guardar Informe</button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- ✅ SortableJS antes de todo el JS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>

/* ===========================
   HELPER: HTML DE UN ITEM
   Centralizado para que agregar y cargar usen el mismo HTML
=========================== */
function buildItemHTML(aspectoId, itemId, contenido = '', isNew = false) {
    return `
        <div class="input-group">
            <input type="hidden"
                   name="items[${aspectoId}][${itemId}][id]"
                   value="${isNew ? '' : itemId}">
            <textarea name="items[${aspectoId}][${itemId}][contenido]"
                      class="form-control" rows="2"
                      placeholder="Escriba el item..."
                      ${isNew ? 'required' : ''}>${contenido}</textarea>
            <div class="input-group-text p-0 border-0 d-flex flex-column gap-1 px-1">
                <button type="button"
                        class="btn btn-sm btn-outline-secondary btn-item-up"
                        title="Subir">↑</button>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary btn-item-down"
                        title="Bajar">↓</button>
            </div>
            <button type="button" class="btn btn-outline-danger btn-remove-item">✕</button>
        </div>`;
}

/* ===========================
   MOSTRAR ASPECTOS POR ESTADO
=========================== */
function aplicarAspectosPorEstado(estado) {
    document.querySelectorAll('.aspecto-card').forEach(card => {
        const aplicaInicial = card.dataset.aplicaInicial == "1";
        const aplicaFinal   = card.dataset.aplicaFinal == "1";
        card.style.display = 'none';
        if ((estado == 1 || estado == 2 || estado == 3) && aplicaInicial) {
            card.style.display = 'block';
        }
        if ((estado == 4 || estado == 5 || estado == 6 || estado == 7) && (aplicaInicial || aplicaFinal)) {
            card.style.display = 'block';
        }
        if (estado >= 8) {
            card.style.display = 'none';
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
        document.getElementById('ot_estado').value = `${data.estado} - ${data.estado_nombre}`;
        let tipo = '', tipoTexto = '';
        if (data.estado == 1) { tipo = 'inicial'; tipoTexto = 'INFORME INICIAL'; }
        if (data.estado == 4) { tipo = 'final';   tipoTexto = 'INFORME FINAL'; }
        document.getElementById('informe_tipo').value = tipo;
        document.getElementById('informe_tipo_text').value = tipoTexto;
        aplicarAspectosPorEstado(data.estado);
    } catch (error) {
        console.error('Error al cargar OT', error);
    }
});

/* =========================
   AGREGAR ITEM
========================= */
document.addEventListener('click', function (e) {

    if (e.target.classList.contains('btn-add-item')) {
        const aspectoId = e.target.dataset.aspecto;
        const container = document.querySelector(`.items-container[data-aspecto="${aspectoId}"]`);
        if (!container) return;
        const uniqueId = 'new_' + Date.now();
        const div = document.createElement('div');
        div.classList.add('item-row', 'mb-2');
        div.innerHTML = buildItemHTML(aspectoId, uniqueId, '', true);
        container.appendChild(div);
    }

    /* =========================
       ELIMINAR ITEM
    ========================= */
    if (e.target.classList.contains('btn-remove-item')) {
        e.target.closest('.item-row')?.remove();
    }

    /* =========================
       ↑ SUBIR ITEM
    ========================= */
    if (e.target.classList.contains('btn-item-up')) {
        const item     = e.target.closest('.item-row');
        const anterior = item.previousElementSibling;
        if (anterior && anterior.classList.contains('item-row')) {
            item.parentNode.insertBefore(item, anterior);
        }
    }

    /* =========================
       ↓ BAJAR ITEM
    ========================= */
    if (e.target.classList.contains('btn-item-down')) {
        const item     = e.target.closest('.item-row');
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
            console.warn('❌ No existe card para aspecto:', aspectoId);
            return;
        }

        /* ITEMS */
        const itemsContainer = card.querySelector('.items-container');
        if (itemsContainer) {
            itemsContainer.innerHTML = '';
            (aspecto.items || []).forEach(item => {
                const div = document.createElement('div');
                div.classList.add('item-row', 'mb-2');
                // ✅ Usa el mismo helper — incluye botones ↑ ↓
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
                    <div class="border p-1 position-relative">
                        <img src="storage/${img.ruta_imagen}" class="img-fluid">
                        <button type="button"
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 btn-remove-imagen"
                                data-id="${img.id}">✕</button>
                    </div>`;
                preview.appendChild(col);
            });
        });
    });

    console.log('✅ Aspectos cargados correctamente');
}

/* =========================
   CREAR GRUPO
========================= */
function crearGrupo(aspectoId, grupoId) {

    const container = document.getElementById('grupo_container_' + aspectoId);
    if (!container) return;

    const div = document.createElement('div');
    div.classList.add('card', 'mb-3', 'grupo-imagen');
    div.dataset.grupo = grupoId;

    div.innerHTML = `
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span>Grupo</span>
            <div>
                <button type="button" class="btn btn-sm btn-secondary btn-grupo-up" title="Subir">↑</button>
                <button type="button" class="btn btn-sm btn-secondary btn-grupo-down" title="Bajar">↓</button>
                <button type="button" class="btn btn-sm btn-danger btn-delete-grupo" title="Eliminar">✕</button>
            </div>
        </div>
        <div class="card-body">
            <input type="text"
                   class="form-control mb-2 grupo-descripcion"
                   placeholder="Descripción del grupo">
            <input type="file"
                   class="form-control mb-2 input-imagen-grupo"
                   data-aspecto="${aspectoId}"
                   data-grupo="${grupoId}"
                   multiple>
            <div class="row grupo-preview" id="preview_${grupoId}"></div>
        </div>`;

    container.appendChild(div);

    new Sortable(document.getElementById('preview_' + grupoId), { animation: 150 });
}

/* =========================
   SUBIR / BAJAR GRUPO
========================= */
document.addEventListener('click', function (e) {

    if (e.target.classList.contains('btn-grupo-up')) {
        const grupo    = e.target.closest('.grupo-imagen');
        const anterior = grupo.previousElementSibling;
        if (anterior && anterior.classList.contains('grupo-imagen')) {
            grupo.parentNode.insertBefore(grupo, anterior);
        }
        return;
    }

    if (e.target.classList.contains('btn-grupo-down')) {
        const grupo     = e.target.closest('.grupo-imagen');
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
    if (!e.target.classList.contains('btn-delete-grupo')) return;
    const grupo   = e.target.closest('.grupo-imagen');
    const grupoId = grupo.dataset.grupo;
    const input   = document.createElement('input');
    input.type    = 'hidden';
    input.name    = 'grupos_eliminar[]';
    input.value   = grupoId;
    document.getElementById('formUpdate').appendChild(input);
    grupo.remove();
});

/* =========================
   ELIMINAR IMAGEN EXISTENTE
========================= */
// document.addEventListener('click', e => {
//     if (!e.target.classList.contains('btn-remove-imagen')) return;
//     const imgId   = e.target.dataset.id;
//     const imgItem = e.target.closest('.imagen-item');
//     imgItem.style.opacity       = '0.4';
//     imgItem.style.pointerEvents = 'none';
//     if (!document.querySelector(`input[name="imagenes_eliminar[]"][value="${imgId}"]`)) {
//         const hidden  = document.createElement('input');
//         hidden.type   = 'hidden';
//         hidden.name   = 'imagenes_eliminar[]';
//         hidden.value  = imgId;
//         document.getElementById('formUpdate').appendChild(hidden);
//     }
// });

// ✅ DESPUÉS — compatible con Huawei/Honor Chrome
function handleRemoveImagen(e) {
    const btn = e.target.closest('.btn-remove-imagen');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const imgId   = btn.dataset.id;
    const imgItem = btn.closest('.imagen-item');
    if (!imgItem) return;

    imgItem.style.opacity       = '0.4';
    imgItem.style.pointerEvents = 'none';

    if (!document.querySelector(`input[name="imagenes_eliminar[]"][value="${imgId}"]`)) {
        const hidden  = document.createElement('input');
        hidden.type   = 'hidden';
        hidden.name   = 'imagenes_eliminar[]';
        hidden.value  = imgId;
        document.getElementById('formUpdate').appendChild(hidden);
    }
}

document.addEventListener('click',    handleRemoveImagen);
document.addEventListener('touchend', handleRemoveImagen, { passive: false });

/* =========================
   ELIMINAR IMAGEN NUEVA
========================= */
// document.addEventListener('click', function (e) {
//     if (e.target.classList.contains('btn-remove-img')) {
//         e.target.closest('.imagen-item').remove();
//     }
// });

// ✅ DESPUÉS
function handleRemoveImgNueva(e) {
    const btn = e.target.closest('.btn-remove-img');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    btn.closest('.imagen-item')?.remove();
}

document.addEventListener('click',    handleRemoveImgNueva);
document.addEventListener('touchend', handleRemoveImgNueva, { passive: false });

/* =========================
   AGREGAR GRUPO
========================= */
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('btn-add-grupo')) return;
    const aspectoId = e.target.dataset.aspecto;
    const grupoId   = 'grupo_' + Date.now();
    crearGrupo(aspectoId, grupoId);
});

/* =========================
   PREVIEW IMÁGENES NUEVAS
========================= */
document.addEventListener('change', function (e) {
    if (!e.target.classList.contains('input-imagen-grupo')) return;
    const aspectoId = e.target.dataset.aspecto;
    const grupoId   = e.target.dataset.grupo;
    Array.from(e.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function (ev) {
            const preview = document.getElementById('preview_' + grupoId);
            const col     = document.createElement('div');
            col.classList.add('col-md-3', 'mb-2', 'imagen-item');
            col.innerHTML = `
                <div class="border p-1 position-relative">
                    <img src="${ev.target.result}" class="img-fluid">
                    <button type="button"
                            class="btn btn-sm btn-danger position-absolute top-0 end-0 btn-remove-img">✕</button>
                </div>`;
            const input  = document.createElement('input');
            input.type   = 'file';
            input.name   = `imagenes[${aspectoId}][${grupoId}][]`;
            input.files  = createFileList(file);
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

        const grupoId     = grupo.dataset.grupo;
        const descripcion = grupo.querySelector('.grupo-descripcion')?.value ?? '';

        const inputDesc  = document.createElement('input');
        inputDesc.type   = 'hidden';
        inputDesc.name   = `descripcion_grupo[${grupoId}]`;
        inputDesc.value  = descripcion;
        form.appendChild(inputDesc);

        const inputOrden  = document.createElement('input');
        inputOrden.type   = 'hidden';
        inputOrden.name   = `orden_grupo[${grupoId}]`;
        inputOrden.value  = index + 1;
        form.appendChild(inputOrden);

        grupo.querySelectorAll('.imagen-item').forEach((img) => {
            const btn = img.querySelector('.btn-remove-imagen');
            if (btn && btn.dataset.id) {
                const inputImg  = document.createElement('input');
                inputImg.type   = 'hidden';
                inputImg.name   = `orden_imagen_id[${grupoId}][]`;
                inputImg.value  = btn.dataset.id;
                form.appendChild(inputImg);
            }
        });
    });

    /* ✅ ORDEN ITEMS — reescribir name con orden visual actual */
    document.querySelectorAll('.items-container').forEach(container => {

        const aspectoId = container.dataset.aspecto;

        container.querySelectorAll('.item-row').forEach((row, index) => {

            const hiddenId  = row.querySelector('input[type="hidden"]');
            const textarea  = row.querySelector('textarea');

            if (!hiddenId || !textarea) return;

            const itemId    = hiddenId.value || ('new_' + Date.now() + '_' + index);
            const newIndex  = index + 1; // orden visual

            // Renombrar los campos con el índice visual actual
            hiddenId.name  = `items[${aspectoId}][${newIndex}][id]`;
            textarea.name  = `items[${aspectoId}][${newIndex}][contenido]`;
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

function renderAspectos(catalogo) {
    document.querySelectorAll('.aspecto-card').forEach(e => e.remove());
    const container = document.querySelector('#modalUpdate .modal-body');
    catalogo.forEach(aspecto => {
        const div = document.createElement('div');
        div.classList.add('card', 'mb-3', 'aspecto-card');
        div.dataset.aspectoId = aspecto.id;
        div.innerHTML = `
            <div class="card-header bg-secondary text-white">
                ${aspecto.orden} - ${aspecto.nombre}
            </div>
            <div class="card-body">
                <div class="items-container" data-aspecto="${aspecto.id}"></div>
                <div class="grupos-container mt-3" id="grupo_container_${aspecto.id}"></div>
            </div>`;
        container.appendChild(div);
    });
}

function aplicarAspectosPorTipoInforme(tipo) {
    document.querySelectorAll('.aspecto-card').forEach(card => {
        const aplicaInicial = card.dataset.aplicaInicial === '1';
        const aplicaFinal   = card.dataset.aplicaFinal === '1';
        card.style.display = 'none';
        card.querySelectorAll('textarea').forEach(t => t.removeAttribute('required'));
        if (tipo === 'inicial' && aplicaInicial) {
            card.style.display = 'block';
            card.querySelectorAll('textarea').forEach(t => t.setAttribute('required', 'required'));
        }
        if (tipo === 'final' && aplicaFinal) {
            card.style.display = 'block';
            card.querySelectorAll('textarea').forEach(t => t.setAttribute('required', 'required'));
        }
    });
}

function mostrarAspectosDesdeInforme(data) {
    document.querySelectorAll('.aspecto-card').forEach(card => {
        card.style.display = 'none';
        card.querySelectorAll('textarea').forEach(el => el.disabled = true);
    });
    data.aspectos.forEach(aspecto => {
        const card = document.querySelector(`.aspecto-card[data-aspecto-id="${aspecto.aspecto_id}"]`);
        if (!card) return;
        card.style.display = 'block';
        card.querySelectorAll('textarea').forEach(el => el.disabled = false);
    });
}

</script>