<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
  aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formUpdate" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="method_field" name="_method">

        <!-- HEADER -->
        <div class="modal-header">
          <h4 class="modal-title fs-5" id="modalTitle">Nuevo registro</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body">

          <!-- CARD: DATOS DEL CLIENTE -->
         <div class="row">

    <!-- =========================
         CARD: DATOS DEL CLIENTE
    ========================== -->
    <div class="col-md-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">
                Datos del Cliente
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label for="cliente_razon_social" class="form-label">
                        Cliente <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           id="cliente_razon_social"
                           class="form-control form-control-sm"
                           autocomplete="off"
                           placeholder="Buscar cliente..."
                           data-error-field="cliente_id">

                    <div class="invalid-feedback"></div>

                    <input type="hidden" id="cliente_id" name="cliente_id">
                </div>     

            </div>
        </div>
    </div>

    <!-- =========================
         CARD: ORDEN DE TRABAJO
    ========================== -->
    <div class="col-md-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">
                Orden de Trabajo
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label for="ot_codigo" class="form-label">
                        Orden de Trabajo <span class="text-danger">*</span>
                    </label>

                    <select name="ot_codigo"
                            id="ot_codigo"
                            class="form-select form-select-sm"
                            required>
                        <option value="">Seleccione una OT</option>
                    </select>

                    <div class="invalid-feedback"></div>
                </div>

                


            </div>
        </div>
    </div>

      


          <!-- CARD: INFORMACIÓN ORDEN DE SERVICIO -->
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">Información de la Orden de Servicio</div>
            <div class="card-body">
              <div class="row">

                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="codigo_orden_servicio" class="form-label">Código OS. Cliente <span class="text-danger">*</span></label>
                    <input type="text" id="codigo_orden_servicio" name="codigo_orden_servicio" class="form-control" required>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control" required>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="monto" class="form-label">Monto <span class="text-danger">*</span></label>
                    <input type="text" id="monto" name="monto" class="form-control" required>
                  </div>
                </div>

                <!-- ✔ FORMA DE PAGO -->
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="forma_pago" class="form-label">Forma de Pago <span class="text-danger">*</span></label>
                    <select name="forma_pago" id="forma_pago" class="form-select" required>
                      <option value="contado">Contado</option>
                      <option value="credito">Crédito</option>
                    </select>
                  </div>
                </div>

                <!-- ✔ DÍAS -->
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="dias" class="form-label">Días</label>
                    <input type="number" id="dias" name="dias" class="form-control" value="0" disabled>
                  </div>
                </div>


                <div class="col-lg-6">
                        <label class="form-label">Adjuntar PDF *</label>
                        <input type="file"
                               name="archivo_pdf"
                               class="form-control"
                               accept="application/pdf"
                               required>
                </div>

              </div>
            </div>
          </div>

          <!-- CARD: ENTREGA -->
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">Entrega</div>
            <div class="card-body">
              <div class="row">

                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="lugar_entrega" class="form-label">Lugar de Entrega <span class="text-danger">*</span></label>
                    <input type="text" id="lugar_entrega" name="lugar_entrega" class="form-control" required>
                  </div>

                  <div class="form-group mb-3">
                    <label for="fecha_entrega" class="form-label">Fecha de Entrega <span class="text-danger">*</span></label>
                    <input type="date" id="fecha_entrega" name="fecha_entrega" class="form-control" required>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="penalidad" class="form-label">Penalidad <span class="text-danger">*</span></label>
                    <input type="text" id="penalidad" name="penalidad" class="form-control" required>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- CARD: ESTADO Y OBSERVACIONES -->
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">Estado y Observaciones</div>
            <div class="card-body">
              <div class="row">

                <div class="col-lg-3">
                  <div class="form-group mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" id="estado" name="estado" class="form-control" readonly value="Registrado">
                  </div>
                </div>

                <div class="col-lg-9">
                  <div class="form-group mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <input type="text" id="observaciones" name="observaciones" class="form-control">
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>

        </div><!-- /.modal-body: the footer must stay outside the scrolling area -->

        <!-- FOOTER -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnSubmit" class="btn btn-primary">Enviar</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- SCRIPT CORREGIDO -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    const formaPago = document.getElementById("forma_pago");
    const dias = document.getElementById("dias");

    function actualizarDias() {
        if (formaPago.value === "credito") {
            dias.disabled = false;
            dias.value = "";       // permite ingresar
            dias.required = true;
        } else {
            dias.disabled = true;
            dias.value = 0;        // coloca 0 automáticamente
            dias.required = false;
        }
    }

    actualizarDias(); // ejecutar al cargar
    formaPago.addEventListener("change", actualizarDias);

});
</script>
