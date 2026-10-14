<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-lg"> {{-- 🔥 antes modal-xl --}}
    <div class="modal-content">

      <form id="formUpdate" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="method_field" name="_method">
        <input type="hidden" id="nota_id" name="nota_id">

        <!-- HEADER -->
        <div class="modal-header py-2">
          <h5 class="modal-title" id="modalTitle">Nueva Nota de Crédito</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body py-2">

          <div id="alertContainer"></div>

          <!-- =========================
               ASIGNACIÓN DE OTs
          ========================== -->
          <div class="card mb-2 shadow-sm">
            <div class="card-header py-2 bg-success text-white">
              Asignación de Órdenes de Trabajo
            </div>

            <div class="card-body py-2">

              <!-- CLIENTE -->
              <div class="row mb-2">
                <div class="col-md-12">
                  <input type="text" id="cliente_razon_social"
                         class="form-control form-control-sm"
                         autocomplete="off"
                         placeholder="Buscar cliente...">
                  <input type="hidden" id="cliente_id" name="cliente_id">
                </div>
              </div>

              <!-- TOTAL -->
              <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted">Selecciona una o varias OTs</small>
                <span class="fw-bold text-success">
                  Total: <span id="totalRebate">S/ 0.00</span>
                </span>
              </div>

              <!-- TABLA -->
              <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                <table class="table table-sm table-bordered mb-0" id="tablaOTs">
                  <thead class="table-light sticky-top">
                    <tr>
                      <th width="5%" class="text-center">
                        <input type="checkbox" id="checkAll">
                      </th>
                      <th>Código</th>
                      <th>Descripción</th>
                      <th>OS</th>
                      <th>Monto</th>
                      <th>Sin IGV</th>
                      <th>Rebate</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>

            </div>
          </div>

          <!-- IDENTIFICACIÓN -->
          <div class="card mb-2 shadow-sm">
            <div class="card-header py-2 bg-primary text-white">
              Identificación
            </div>

            <div class="card-body py-2">
              <div class="row">

                <div class="col-md-4">
                  <input type="text" name="serie" id="serie"
                         class="form-control form-control-sm"
                         placeholder="Serie" required>
                </div>

                <div class="col-md-4">
                  <input type="text" name="correlativo" id="correlativo"
                         class="form-control form-control-sm"
                         placeholder="Correlativo" required>
                </div>

              </div>
            </div>
          </div>

          <!-- TOTALES -->
          <div class="card mb-2 shadow-sm">
            <div class="card-header py-2 bg-dark text-white">
              Totales
            </div>

            <div class="card-body py-2">
              <div class="row">

                <div class="col-md-4">
                  <input type="text" id="totalrebates_nota"
                         class="form-control form-control-sm"
                         placeholder="Total Rebates" readonly>
                </div>

                <div class="col-md-4">
                  <input type="text" name="igv_nota" id="igv_nota"
                         class="form-control form-control-sm"
                         placeholder="IGV" readonly>
                </div>

                <div class="col-md-4">
                  <input type="text" name="monto_nota" id="monto_nota"
                         class="form-control form-control-sm"
                         placeholder="Total Nota" readonly>
                </div>

              </div>
            </div>
          </div>

          <!-- DOCUMENTOS -->
          <div class="card mb-2 shadow-sm">
            <div class="card-header py-2 bg-danger text-white">
              Documentos
            </div>

            <div class="card-body py-2">
              <div class="row">

                <div class="col-md-4">
                  <input type="file" name="valida_nota" id="valida_nota"
                         class="form-control form-control-sm"
                         accept=".pdf" required>
                </div>

                <div class="col-md-4">
                  <input type="file" name="detalles_nota" id="detalles_nota"
                         class="form-control form-control-sm"
                         accept=".pdf" required>
                </div>

                <div class="col-md-4">
                  <input type="file" name="archivo_nota" id="archivo_nota"
                         class="form-control form-control-sm"
                         accept=".pdf" required>
                </div>

              </div>
            </div>
          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
            Cancelar
          </button>

          <button type="submit" id="btnSubmit" class="btn btn-primary btn-sm">
            Guardar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>