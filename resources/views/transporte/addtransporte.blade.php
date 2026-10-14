<!-- Offcanvas Transporte -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasTransporte">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Nuevo Transporte de Herramientas</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="formTransporte" method="post" action="{{ route('transporte.store') }}">
      @csrf
      <input type="hidden" id="method_field_transporte" name="_method">

      <div class="row">

        <!-- Departamento -->
        <div class="col-lg-6 mb-3">
          <label class="form-label">Departamento *</label>
          <select name="department_id" id="department_id" class="form-select" required></select>
          <div class="invalid-feedback"></div>
        </div>

        <!-- Provincia -->
        <div class="col-lg-6 mb-3">
          <label class="form-label">Provincia *</label>
          <select name="province_id" id="province_id" class="form-select" required></select>
          <div class="invalid-feedback"></div>
        </div>

        <!-- Distrito -->
        <div class="col-lg-6 mb-3">
          <label class="form-label">Distrito *</label>
          <select name="districts_id" id="districts_id" class="form-select" required></select>
          <div class="invalid-feedback"></div>
        </div>

        <!-- Unidad -->
        <div class="col-lg-6 mb-3">
          <label class="form-label">Unidad *</label>
          <select name="unidad_codigo" id="unidad_codigo" class="form-select" required></select>
          <div class="invalid-feedback"></div>
        </div>

        <!-- Precio -->
        <div class="col-lg-6 mb-3">
          <label class="form-label">Precio *</label>
          <input type="text" name="precio" id="precio" class="form-control" required>
          <div class="invalid-feedback"></div>
        </div>

      </div>

      <div class="mt-3 text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>

    </form>
  </div>
</div>
