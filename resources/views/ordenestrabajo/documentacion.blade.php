<div class="modal fade" id="modalDocumentacionOT" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formDocumentacionOT"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Subir Documentación Orden de Trabajo
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="documentacion_ot_id">

                    <div class="mb-3">
                        <label class="form-label">Código OT Cliente</label>
                        <input type="text" id="documentacion_codigo"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="documentacion_descripcion"
                                  class="form-control"
                                  rows="3" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Solicitante</label>
                        <input type="text" id="documentacion_responsable"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar Imagen *</label>
                        <input type="file"
                               name="archivo"
                               class="form-control"
                               accept="image/png, image/jpeg, image/jpg"
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Documentación OT
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
