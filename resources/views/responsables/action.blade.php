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
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">Datos del Cliente</div>
            <div class="card-body">
              @include('responsables.partials.cliente-form')
            </div>
          </div>

          <!-- CARD: INFORMACIÓN DE LA SEDE -->
          <div class="card mb-3 shadow-sm">
            <div class="card-header bg-primary text-white">Información del Responsable</div>
            <div class="card-body">
              <div class="row">

              <!-- ✔ NOMBRE DE LA SEDE -->
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                  </div>
                </div>

                <!-- ✔ CARGO -->
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="cargo" class="form-label">Cargo <span class="text-danger">*</span></label>
                    <input type="text" id="cargo" name="cargo" class="form-control" required>
                  </div>
                </div>

                <!-- ✔ TELÉFONO -->
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                    <input type="text" id="telefono" name="telefono" class="form-control" required>
                  </div>
                </div>

                <!-- ✔ EMAIL -->
                <div class="col-lg-6">
                  <div class="form-group mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="text" id="email" name="email" class="form-control" required>
                  </div>
                </div>

               <!-- Activo -->
            <div class="col-md-6">
              <label for="activo" class="form-label">Estado <span class="text-danger">*</span></label>
              <select name="activo" id="activo" class="form-select" required>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
              <div class="invalid-feedback"></div>
            </div>                          

          </div>  
        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnSubmit" class="btn btn-primary">Enviar</button>
        </div>

      </form>
    </div>
  </div>
</div>
