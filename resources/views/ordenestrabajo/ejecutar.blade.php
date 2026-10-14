<div class="modal fade" id="modalEjecutarOT" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formEjecutarOT"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Ejecutar Orden de Trabajo
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="ejecutar_ot_id">

                    <div class="mb-3">
                        <label class="form-label">Código OT Cliente</label>
                        <input type="text" id="ejecutar_codigo"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="ejecutar_descripcion"
                                  class="form-control"
                                  rows="3" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Solicitante</label>
                        <input type="text" id="ejecutar_responsable"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar PDF *</label>
                        <input type="file"
                               name="archivo"
                               class="form-control"
                               accept="application/pdf"
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Ejecutar OT
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
