<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
  <style>
    #modalUpdate .modal-dialog { max-width: 1080px; }
    #modalUpdate .modal-header { padding: .9rem 1.25rem !important; }
    #modalUpdate .modal-body { padding: 1.1rem 1.25rem !important; }
    #modalUpdate .modal-body > .row { --bs-gutter-x: 1rem; --bs-gutter-y: 1rem; }
    #modalUpdate .card-header { padding: .75rem 1rem !important; }
    #modalUpdate .card-body { padding: 1rem; }
    #modalUpdate .form-label { margin-bottom: .3rem; font-size: .86rem; }
    #modalUpdate .form-text { font-size: .76rem; line-height: 1.3; }
    #modalUpdate textarea { min-height: 72px; }
    #modalUpdate .modal-footer { padding: .75rem 1.25rem !important; }
    #modalUpdate #currentPdfLink { align-items: center; gap: .25rem; }
    #modalUpdate #currentPdfLink:not(.d-none) { display: inline-flex !important; }
  </style>
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <form id="formUpdate" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="method_field" name="_method">

        <div class="modal-header border-0 bg-body-tertiary px-4 py-3">
          <div>
            <div class="text-primary small fw-semibold text-uppercase">Operaciones</div>
            <h4 class="modal-title fs-5 mb-0" id="modalTitle">Nueva orden de servicio</h4>
            <div class="text-muted small">Registra el documento del cliente y sus condiciones de entrega.</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body p-4">
          <div class="row g-4">
            <div class="col-lg-5">
              <section class="card border shadow-none h-100">
                <div class="card-header bg-transparent border-bottom py-3">
                  <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-primary"></i>Cliente y orden de trabajo</h5>
                </div>
                <div class="card-body">
                  <div class="mb-4 position-relative">
                    <label for="cliente_razon_social" class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-search"></i></span>
                      <input type="text" id="cliente_razon_social" class="form-control" autocomplete="off" placeholder="RUC o razón social" data-error-field="cliente_id">
                    </div>
                    <div class="form-text">Escribe para buscar un cliente registrado.</div>
                    <div class="invalid-feedback"></div>
                    <input type="hidden" id="cliente_id" name="cliente_id">
                  </div>

                  <div class="mb-3">
                    <label for="ot_codigo" class="form-label fw-semibold">Orden de Trabajo <span class="text-danger">*</span></label>
                    <select name="ot_codigo" id="ot_codigo" class="form-select" required>
                      <option value="">Primero selecciona un cliente</option>
                    </select>
                    <div class="invalid-feedback"></div>
                  </div>

                  <div class="rounded-3 bg-body-tertiary p-3 small text-muted">
                    <i class="bi bi-info-circle me-1"></i>Solo se muestran órdenes de trabajo habilitadas para generar una orden de servicio.
                  </div>
                </div>
              </section>
            </div>

            <div class="col-lg-7">
              <section class="card border shadow-none h-100">
                <div class="card-header bg-transparent border-bottom py-3">
                  <h5 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Documento de servicio</h5>
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="codigo_orden_servicio" class="form-label fw-semibold">Código OS. Cliente <span class="text-danger">*</span></label>
                      <input type="text" id="codigo_orden_servicio" name="codigo_orden_servicio" class="form-control" maxlength="50" placeholder="Ej. OS-2026-001" required>
                    </div>
                    <div class="col-md-6">
                      <label for="monto" class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
                      <div class="input-group"><span class="input-group-text">S/</span><input type="number" id="monto" name="monto" class="form-control" min="0" max="999999.99" step="0.01" placeholder="0.00" required></div>
                    </div>
                    <div class="col-12">
                      <label for="descripcion" class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                      <textarea id="descripcion" name="descripcion" class="form-control" rows="3" maxlength="255" placeholder="Describe el alcance de la orden" required></textarea>
                    </div>
                    <div class="col-md-6">
                      <label for="forma_pago" class="form-label fw-semibold">Forma de Pago <span class="text-danger">*</span></label>
                      <select name="forma_pago" id="forma_pago" class="form-select" required><option value="contado">Contado</option><option value="credito">Crédito</option></select>
                    </div>
                    <div class="col-md-6">
                      <label for="dias" class="form-label fw-semibold">Días</label>
                      <div class="input-group"><input type="number" id="dias" name="dias" class="form-control" min="0" value="0" disabled><span class="input-group-text">días</span></div>
                    </div>
                    <div class="col-12">
                      <label for="archivo_pdf" class="form-label fw-semibold">Adjuntar PDF <span id="pdfRequired" class="text-danger">*</span></label>
                      <input type="file" id="archivo_pdf" name="archivo_pdf" class="form-control" accept="application/pdf">
                      <div class="form-text">Máximo 5 MB. Al editar, déjalo vacío para conservar el documento actual.</div>
                      <a id="currentPdfLink" class="btn btn-sm btn-outline-danger mt-2 d-none" href="#" download><i class="bi bi-download"></i>Descargar PDF actual</a>
                    </div>
                  </div>
                </div>
              </section>
            </div>

            <div class="col-12">
              <section class="card border shadow-none">
                <div class="card-header bg-transparent border-bottom py-3"><h5 class="card-title mb-0"><i class="bi bi-truck me-2 text-primary"></i>Entrega y condiciones</h5></div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-5"><label for="lugar_entrega" class="form-label fw-semibold">Lugar de Entrega <span class="text-danger">*</span></label><input type="text" id="lugar_entrega" name="lugar_entrega" class="form-control" maxlength="255" placeholder="Dirección o sede" required></div>
                    <div class="col-md-3"><label for="fecha_entrega" class="form-label fw-semibold">Fecha de Entrega <span class="text-danger">*</span></label><input type="date" id="fecha_entrega" name="fecha_entrega" class="form-control" min="{{ now()->format('Y-m-d') }}" required></div>
                    <div class="col-md-4"><label for="penalidad" class="form-label fw-semibold">Penalidad <span class="text-danger">*</span></label><input type="text" id="penalidad" name="penalidad" class="form-control" maxlength="255" placeholder="Ej. 0.10% por día" required></div>
                    <div class="col-md-3"><label for="estado" class="form-label fw-semibold">Estado</label><input type="text" id="estado" name="estado" class="form-control bg-body-tertiary" readonly value="Registrado"></div>
                    <div class="col-md-9"><label for="observaciones" class="form-label fw-semibold">Observaciones</label><textarea id="observaciones" name="observaciones" class="form-control" rows="2" maxlength="255" placeholder="Información adicional (opcional)"></textarea></div>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top bg-body-tertiary px-4 py-3">
          <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnSubmit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i>Guardar orden</button>
        </div>
      </form>
    </div>
  </div>
</div>
