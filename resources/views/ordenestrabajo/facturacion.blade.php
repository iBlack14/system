<div class="modal fade" id="modalFacturadoOT" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formFacturadoOT"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Subir documentos de Facturación
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="facturacion_ot_id">

                    <div class="mb-3">
                        <label class="form-label">Código OT Cliente</label>
                        <input type="text" id="facturacion_codigo"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="facturacion_descripcion"
                                  class="form-control"
                                  rows="3" readonly></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Solicitante</label>
                        <input type="text" id="facturacion_responsable"
                               class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar Factura Emitida *</label>
                        <input type="file"
                               name="archivo_factura"
                               class="form-control"
                               accept="application/pdf"
                               required>
                    </div>                    

                    <div class="mb-3">
                        <label class="form-label">Adjuntar Sustento *</label>
                        <input type="file"
                               name="archivo_sustento"
                               class="form-control"
                               accept="application/pdf"
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Subir Documentos Facturación OT
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
