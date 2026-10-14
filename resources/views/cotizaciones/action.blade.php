<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content fs-7">
            <form id="formUpdate" method="post">
                @csrf
                <input type="hidden" id="method_field" name="_method">

                <div class="modal-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                            <i class="bi bi-file-earmark-ruled fs-6"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Registro de Cotización</h5>
                            <small class="text-muted">Complete los datos comerciales y desglose de módulos</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light-subtle">
                    <!-- Formulario de Comprobante y Cliente -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-6">
                            @include('cotizaciones.partials.cliente-form')
                        </div>
                        <div class="col-lg-6">                            
                            @include('cotizaciones.partials.comprobante-form')
                        </div>
                    </div>

                    <!-- Selector de Módulos (Pill Toggles) -->
                    <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <span class="fw-bold text-dark small text-uppercase letter-spacing-1">
                                    <i class="bi bi-layers-fill text-primary me-1"></i> Módulos a Incluir
                                </span>
                                <p class="text-muted small mb-0">Active las secciones que aplican a esta propuesta comercial:</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 pt-1">
                            <label class="module-pill-toggle" for="chkServicio">
                                <input class="toggle-section" type="checkbox" id="chkServicio" data-target="#seccionServicios">
                                <i class="bi bi-gear-fill text-primary"></i>
                                <span>Servicios</span>
                            </label>

                            <label class="module-pill-toggle" for="chkTransporte">
                                <input class="toggle-section" type="checkbox" id="chkTransporte" data-target="#seccionTransporte">
                                <i class="bi bi-truck text-secondary"></i>
                                <span>Transporte de Herramientas</span>
                            </label>

                            <label class="module-pill-toggle" for="chkMateriales">
                                <input class="toggle-section" type="checkbox" id="chkMateriales" data-target="#seccionMateriales">
                                <i class="bi bi-box-seam text-warning"></i>
                                <span>Materiales</span>
                            </label>

                            <label class="module-pill-toggle" for="chkTPersonal">
                                <input class="toggle-section" type="checkbox" id="chkTPersonal" data-target="#seccionTPersonal">
                                <i class="bi bi-people-fill text-success"></i>
                                <span>Transporte de Personal</span>
                            </label>
                        </div>
                    </div>

                    <!-- Secciones de módulos -->
                    <div id="seccionServicios" style="display: none;" class="mb-4">
                        <div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden bg-white">
                            <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-primary small d-flex align-items-center gap-2">
                                    <i class="bi bi-gear-fill"></i> Módulo de Servicios
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="servicio_nombre" class="form-control"
                                        placeholder="Buscar servicio..." autocomplete="off">
                                    <input type="hidden" id="servicio_id" name="servicio_id">
                                    <button type="button" class="btn btn-outline-primary btn-sm ms-2 rounded-2" id="btnNuevoServicio">
                                        <i class="bi bi-plus-lg me-1"></i> Nuevo Servicio
                                    </button>
                                </div>
                                @include('cotizaciones.partials.detalles-servicios')
                            </div>
                        </div>
                    </div>

                    <div id="seccionTransporte" style="display: none;" class="mb-4">
                        <div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden bg-white">
                            <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-secondary small d-flex align-items-center gap-2">
                                    <i class="bi bi-truck"></i> Módulo de Transporte de Herramientas
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="transporte_nombre" class="form-control"
                                        placeholder="Buscar transporte..." autocomplete="off">
                                    <input type="hidden" id="transporte_id" name="transporte_id">
                                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2 rounded-2" id="btnNuevoTransporte">
                                        <i class="bi bi-plus-lg me-1"></i> Nuevo Transporte
                                    </button>
                                </div>
                                @include('cotizaciones.partials.detalles-transporte')
                            </div>
                        </div>
                    </div>

                    <div id="seccionMateriales" style="display: none;" class="mb-4">
                        <div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden bg-white">
                            <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-warning-emphasis small d-flex align-items-center gap-2">
                                    <i class="bi bi-box-seam"></i> Módulo de Materiales
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="producto_nombre" class="form-control"
                                        placeholder="Buscar material o producto..." autocomplete="off">
                                    <input type="hidden" id="producto_id" name="producto_id">
                                    <button type="button" id="btnAgregarMaterial" class="btn btn-primary btn-sm ms-2 rounded-2">
                                        <i class="bi bi-plus-lg me-1"></i> Agregar
                                    </button>
                                </div>
                                @include('cotizaciones.partials.detalles-materiales')
                            </div>
                        </div>
                    </div>

                    <div id="seccionTPersonal" style="display: none;" class="mb-4">
                        <div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden bg-white">
                            <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-success small d-flex align-items-center gap-2">
                                    <i class="bi bi-people-fill"></i> Módulo Transporte de Personal
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="tpersonal_nombre" class="form-control"
                                        placeholder="Buscar transporte de personal..." autocomplete="off">
                                    <input type="hidden" id="tpersonal_id" name="tpersonal_id">
                                    <button type="button" class="btn btn-outline-success btn-sm ms-2 rounded-2" id="btnNuevoTpersonal">
                                        <i class="bi bi-plus-lg me-1"></i> Nuevo T. Personal
                                    </button>
                                </div>
                                @include('cotizaciones.partials.detalles-tpersonal')
                            </div>
                        </div>
                    </div>

                    <!-- Módulo Condiciones -->
                    <div id="seccionCondiciones" class="mb-4">
                        <div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden bg-white">
                            <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark small d-flex align-items-center gap-2">
                                    <i class="bi bi-card-checklist text-primary"></i> Condiciones Comerciales
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <div class="input-group input-group-sm mb-3">
                                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" id="condicion_descripcion" class="form-control"
                                        placeholder="Buscar condición estándar..." autocomplete="off">
                                    <input type="hidden" id="condicion_id" name="condicion_id">
                                    <button type="button" id="btnAgregarCondicion" class="btn btn-primary btn-sm ms-2 rounded-2">
                                        <i class="bi bi-plus-lg me-1"></i> Agregar
                                    </button>
                                </div>
                                @include('cotizaciones.partials.detalles-condiciones')
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de totales y resumen -->
                    <div class="row g-3">
                        <div class="col-lg-5">
                            <div class="card border border-light-subtle rounded-3 p-3 bg-white h-100 shadow-sm">
                                <div class="d-flex align-items-center gap-2 text-muted small fw-semibold mb-2">
                                    <i class="bi bi-info-circle text-primary"></i> Información del Cálculo
                                </div>
                                <p class="text-muted small mb-0" style="line-height: 1.5;">
                                    Los subtotales de cada módulo se consolidan automáticamente. Las operaciones gravadas calcularán el 18% de IGV correspondiente al valor de venta.
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="card border border-light-subtle rounded-3 shadow-sm bg-white overflow-hidden p-3">
                                @include('cotizaciones.partials.tabla-detalles')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" id="btnCancelarModal" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Guardar Cotización
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ===============================
    // MOSTRAR / OCULTAR MÓDULOS POR CHECKBOX
    // ===============================
    document.querySelectorAll('.toggle-section').forEach(chk => {
        const target = document.querySelector(chk.dataset.target);
        if (target) target.style.display = 'none';

        chk.addEventListener('change', function () {
            if (target) {
                target.style.display = this.checked ? 'block' : 'none';
            }
        });
    });

    // ===============================
    // 🔥 LIMPIAR TODO AL CERRAR MODAL
    // ===============================
    $('#modalUpdate').on('hidden.bs.modal', function () {

        console.log('🧹 Modal cerrado → reset total');

        if (typeof window.resetForm === 'function') {
            window.resetForm();
        } else if (typeof resetForm === 'function') {
            resetForm();
        }
    });

});
</script>
@endpush



