<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
      <form id="formUpdate" method="post">
        @csrf
        <input type="hidden" id="method_field" name="_method">
        
        <div class="modal-header bg-white border-bottom py-3 px-4">
          <div class="d-flex align-items-center gap-2.5">
            <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
              <i class="bi bi-rulers fs-6"></i>
            </span>
            <div>
              <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Nueva Unidad de Medida</h5>
              <small class="text-muted">Parámetros de medición y catalogación</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4 bg-light-subtle">
          <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white">
            <div class="row g-3">
              <div class="col-12">
                <label for="codigo" class="form-label small fw-semibold text-dark mb-1">
                  Código SUNAT / ISO <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-hash"></i>
                  </span>
                  <input type="text" id="codigo" name="codigo" class="form-control font-monospace text-uppercase" placeholder="Ej: NIU, KGM, GLN, BX" maxlength="10" required>
                </div>
                <div class="form-text small text-muted">Código alfanumérico estándar para facturación electrónica.</div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-12">
                <label for="descripcion" class="form-label small fw-semibold text-dark mb-1">
                  Descripción Oficial <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-card-text"></i>
                  </span>
                  <input type="text" id="descripcion" name="descripcion" class="form-control text-uppercase" placeholder="Ej: UNIDADES, KILOGRAMOS, GALONES" required>
                </div>
                <div class="form-text small text-muted">Nombre completo visible en documentos, proformas y almacén.</div>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-white border-top py-3 px-4">
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-check-circle me-1"></i> Guardar Unidad
          </button>
        </div>
      </form>
    </div>
  </div>
</div>