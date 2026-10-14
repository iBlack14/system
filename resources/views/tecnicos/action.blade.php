<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
  aria-labelledby="staticBackdropLabel" aria-hidden="true">

  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form id="formUpdate" method="post">
        @csrf
        <input type="hidden" id="method_field" name="_method">

        <!-- Header -->
        <div class="modal-header">
          <h4 class="modal-title fs-5" id="modalTitle">Nuevo registro</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <div class="row g-3">

            <!-- DNI -->
            <div class="col-md-6">
              <label for="dni" class="form-label">DNI <span class="text-danger">*</span></label>
              <input type="text" id="dni" name="dni" class="form-control" required>
              <div class="invalid-feedback"></div>
            </div>

            <!-- Nombre -->
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre y Apellidos <span class="text-danger">*</span></label>
              <input type="text" id="nombre" name="nombre" class="form-control" required>
              <div class="invalid-feedback"></div>
            </div>

            <!-- Departamento -->
            <div class="col-md-6">
              <label for="department_id" class="form-label">Departamento <span class="text-danger">*</span></label>
              <select name="department_id" id="department_id" class="form-select" required></select>
              <div class="invalid-feedback"></div>
            </div>

            <!-- Provincia -->
            <div class="col-md-6">
              <label for="province_id" class="form-label">Provincia <span class="text-danger">*</span></label>
              <select name="province_id" id="province_id" class="form-select" required></select>
              <div class="invalid-feedback"></div>
            </div>

             <div class="col-md-6">
              <label for="districts_id" class="form-label">Distrito <span class="text-danger">*</span></label>
              <select name="districts_id" id="districts_id" class="form-select" required></select>
              <div class="invalid-feedback"></div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-control">
              <div class="invalid-feedback"></div>
            </div>

            <!-- Teléfono -->
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" id="telefono" name="telefono" class="form-control">
              <div class="invalid-feedback"></div>
            </div>

            <!-- Profesión -->
            <div class="col-md-6">
              <label for="profesion" class="form-label">Profesión <span class="text-danger">*</span></label>
              <input type="text" id="profesion" name="profesion" class="form-control" required>
              <div class="invalid-feedback"></div>
            </div>

            <!-- Puesto -->
            <div class="col-md-6">
              <label for="puesto" class="form-label">Puesto <span class="text-danger">*</span></label>
              <input type="text" id="puesto" name="puesto" class="form-control" required>
              <div class="invalid-feedback"></div>
            </div>

            <!-- Activo -->
            <div class="col-md-6">
              <label for="activo" class="form-label">Activo <span class="text-danger">*</span></label>
              <select name="activo" id="activo" class="form-select" required>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
              <div class="invalid-feedback"></div>
            </div>

          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnSubmit" class="btn btn-primary">Enviar</button>
        </div>

      </form>
    </div>
  </div>
</div>
