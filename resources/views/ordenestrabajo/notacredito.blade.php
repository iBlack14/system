<div class="modal fade" id="modalNotaCreditoOT" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formNotaCreditoOT"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Subir documentos de Nota Crédito
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="notacredito_ot_id">

                    <div class="mb-3">
                        <label class="form-label">Código OT Cliente</label>
                        <input type="text" id="notacredito_codigo"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="notacredito_descripcion"
                                  class="form-control"
                                  rows="3" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Solicitante</label>
                        <input type="text" id="notacredito_responsable"
                               class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Adjuntar Nota de Crédito *</label>
                        <input type="file"
                               name="archivo_nota"
                               class="form-control"
                               accept="application/pdf"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar Validación de Nota *</label>
                        <input type="file"
                               name="valida_nota"
                               class="form-control"
                               accept="application/pdf"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar Detalles Nota *</label>
                        <input type="file"
                               name="detalles_nota"
                               class="form-control"
                               accept="application/pdf"
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Subir documentos de Nota Crédito
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
