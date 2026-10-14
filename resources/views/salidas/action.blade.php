<!-- Modal -->
<div class="modal fade"
    id="modalUpdate"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    tabindex="-1"
    aria-labelledby="staticBackdropLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-xl">
        <div class="modal-content fs-7">

            <form id="formUpdate" method="POST">
                @csrf
                <input type="hidden" id="method_field" name="_method">

                <!-- ===================================== -->
                <!-- HEADER -->
                <!-- ===================================== -->
                <div class="modal-header">
                    <h4 class="modal-title fs-5 text-primary" id="modalTitle">
                        Nuevo registro - Salidas de Almacén
                    </h4>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>

                <!-- ===================================== -->
                <!-- BODY -->
                <!-- ===================================== -->
                <div class="modal-body">

                    <!-- ===================================== -->
                    <!-- CLIENTE + COMPROBANTE -->
                    <!-- ===================================== -->
                    <div class="row">

                        <div class="col-lg-6">
                            @include('salidas.partials.cliente-form')
                        </div>

                        <div class="col-lg-6">
                            @include('salidas.partials.comprobante-form')
                        </div>

                    </div>

                    <!-- ===================================== -->
                    <!-- MOTIVO -->
                    <!-- ===================================== -->
                    <div class="row mt-3">

                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Motivo de salida
                                </label>

                                <textarea
                                    name="motivo"
                                    id="motivo"
                                    rows="3"
                                    class="form-control"
                                    placeholder="Ingrese el motivo de la salida"
                                    required></textarea>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <!-- ===================================== -->
                    <!-- MÓDULO DE MATERIALES -->
                    <!-- ===================================== -->
                    <div id="seccionMateriales" class="mt-3">

                        <div class="card border-warning">

                            <div class="card-header bg-light fw-bold text-warning">
                                Módulo de Materiales
                            </div>

                            <div class="card-body">

                                <div class="input-group input-group-sm mb-3">

                                    <input
                                        type="text"
                                        id="producto_nombre"
                                        class="form-control"
                                        placeholder="Buscar material"
                                        autocomplete="off">

                                    <input
                                        type="hidden"
                                        id="producto_id"
                                        name="producto_id">

                                    <button
                                        type="button"
                                        id="btnAgregarMaterial"
                                        class="btn btn-success">

                                        Agregar
                                    </button>

                                </div>

                                @include('salidas.partials.detalles-materiales')

                            </div>
                        </div>
                    </div>

                    <!-- ===================================== -->
                    <!-- TABLA DE DETALLES -->
                    <!-- ===================================== -->
                    <div class="row mt-4">

                        <div class="col-lg-4"></div>

                        <div class="col-lg-8">
                            @include('salidas.partials.tabla-detalles')
                        </div>

                    </div>

                </div>

                <!-- ===================================== -->
                <!-- FOOTER -->
                <!-- ===================================== -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="btnCancelarModal"
                        data-bs-dismiss="modal">

                        Cancelar
                    </button>

                    <button
                        type="submit"
                        id="btnSubmit"
                        class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Guardar
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    =====================================
    LIMPIAR TODO AL CERRAR MODAL
    =====================================
    */
    $('#modalUpdate').on('hidden.bs.modal', function () {

        console.log('🧹 Modal cerrado → reset total');

        $('#formUpdate')[0].reset();

        $('#method_field').val('');
        $('#producto_id').val('');
        $('#producto_nombre').val('');
        $('#motivo').val('');

        if (typeof window.resetForm === 'function') {
            window.resetForm();
        } else if (typeof resetForm === 'function') {
            resetForm();
        }
    });

});
</script>
@endpush