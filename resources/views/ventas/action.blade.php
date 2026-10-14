<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content fs-7">
            <form id="formUpdate" method="post">
                @csrf
                <input type="hidden" id="method_field" name="_method">       

                <div class="modal-header">
                    <h4 class="modal-title fs-5 text-primary" id="modalTitle">Nuevo registro - Ventas</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Formulario de Comprobante y Cliente -->
                    <div class="row">
                        <div class="col-lg-6">
                            @include('ventas.partials.cliente-form')
                        </div>
                        <div class="col-lg-6">
                            @include('ventas.partials.comprobante-form')                            
                        </div>
                    </div>

                                      <hr>

                    <!-- Checkboxes para módulos -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input toggle-section" type="checkbox" id="chkServicio"
                            data-target="#seccionServicios">
                        <label class="form-check-label" for="chkServicio">Servicios</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input toggle-section" type="checkbox" id="chkTransporte"
                            data-target="#seccionTransporte">
                        <label class="form-check-label" for="chkTransporte">Transporte de Herramientas</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input toggle-section" type="checkbox" id="chkMateriales"
                            data-target="#seccionMateriales">
                        <label class="form-check-label" for="chkMateriales">Materiales</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input toggle-section" type="checkbox" id="chkTPersonal"
                            data-target="#seccionTPersonal">
                        <label class="form-check-label" for="chkTPersonal">Transporte de Personal</label>
                    </div>

                    <hr>

                    <!-- Secciones de módulos -->
                    <div id="seccionServicios" style="display: none;" class="mt-3">
                        <div class="card border-primary">
                            <div class="card-header bg-light fw-bold text-primary">Módulo de Servicios</div>
                            <div class="card-body">
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="servicio_nombre" class="form-control"
                                        placeholder="Buscar servicio" autocomplete="off">
                                    <input type="hidden" id="servicio_id" name="servicio_id">
                                    <button type="button" id="btnAgregarServicio" class="btn btn-success">Agregar</button>
                                </div>
                                @include('ventas.partials.detalles-servicios')
                            </div>
                        </div>
                    </div>

                    <div id="seccionTransporte" style="display: none;" class="mt-3">
                        <div class="card border-secondary">
                            <div class="card-header bg-light fw-bold text-secondary">Módulo de Transporte</div>
                            <div class="card-body">
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="transporte_nombre" class="form-control"
                                        placeholder="Buscar transporte" autocomplete="off">
                                    <input type="hidden" id="transporte_id" name="transporte_id">
                                    <button type="button" id="btnAgregarTransporte" class="btn btn-success">Agregar</button>
                                </div>
                                @include('ventas.partials.detalles-transporte')
                            </div>
                        </div>
                    </div>

                    <div id="seccionMateriales" style="display: none;" class="mt-3">
                        <div class="card border-warning">
                            <div class="card-header bg-light fw-bold text-warning">Módulo de Materiales</div>
                            <div class="card-body">
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="producto_nombre" class="form-control"
                                        placeholder="Buscar material" autocomplete="off">
                                    <input type="hidden" id="producto_id" name="producto_id">
                                    <button type="button" id="btnAgregarMaterial" class="btn btn-success">Agregar</button>
                                </div>
                                @include('ventas.partials.detalles-materiales')
                            </div>
                        </div>
                    </div>

                    <div id="seccionTPersonal" style="display: none;" class="mt-3">
                        <div class="card border-success">
                            <div class="card-header bg-light fw-bold text-success">Módulo Transporte de Personal</div>
                            <div class="card-body">
                                <div class="input-group input-group-sm mb-3">
                                    <input type="text" id="tpersonal_nombre" class="form-control"
                                        placeholder="Buscar transporte de personal" autocomplete="off">
                                    <input type="hidden" id="tpersonal_id" name="tpersonal_id">
                                    <button type="button" id="btnAgregarTPersonal" class="btn btn-success">Agregar</button>
                                </div>
                                @include('ventas.partials.detalles-tpersonal')
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de detalles -->
                    <div class="row mt-4">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8">
                            @include('ventas.partials.tabla-detalles')
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancelarModal" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnSubmit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Guardar
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
        if (target) target.style.display = 'none'; // ocultar al inicio

        chk.addEventListener('change', function () {
            if (target) target.style.display = this.checked ? 'block' : 'none';
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


