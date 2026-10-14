<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formUpdate" method="post">
        @csrf
        <input type="hidden" id="method_field" name="_method">
        <div class="modal-header">
          <h4 class="modal-title fs-5" id="modalTitle">Nuevo registro</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="department_id" class="form-label">Departamento <span class="text-danger">*</span></label>
                <select name="department_id" id="department_id" class="form-select" required></select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="province_id" class="form-label">Provincia <span class="text-danger">*</span></label>
                <select name="province_id" id="province_id" class="form-select" required></select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
             <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="districts_id" class="form-label">Distrito <span class="text-danger">*</span></label>
                <select name="districts_id" id="districts_id" class="form-select" required></select>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="unidad_codigo" class="form-label">Unidad <span class="text-danger">*</span></label>
                <select name="unidad_codigo" id="unidad_codigo" class="form-select" required></select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
                <input type="text" id="precio" name="precio" class="form-control" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" id="btnSubmit" class="btn btn-primary">Enviar</button>
        </div>
      </form>
    </div>
  </div>
</div>