<div class="modal fade" id="modalConformidadOSE" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formConformidadOSE"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Ingresar Código de Conformidad
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="conformidad_os">

                    <div class="mb-3">
                        <label class="form-label">Código de Servicio</label>
                        <input type="text" id="conformidad_codigoos"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="conformidad_descripcionos"
                                  class="form-control"
                                  rows="3" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Código de Conformidad</label>
                        <input type="text" name="codigo_conformidados" id="codigo_conformidados"
                               class="form-control" required>
                    </div>                   

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Código de Conformidad de Orden de Servicio
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
