<div class="offcanvas offcanvas-end" 
     tabindex="-1" 
     id="offcanvasServicios" 
     aria-labelledby="offcanvasServiciosLabel">

  <div class="offcanvas-header">
    <h5 id="offcanvasServiciosLabel">Nuevo Servicio</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <form id="formServicio" method="post" action="{{ route('servicios.store') }}">
      @csrf
      <input type="hidden" id="method_field" name="_method">

      <div class="mb-3">
        <label>Unidad</label>
        <select name="unidad_codigo" class="form-select" required></select>
      </div>

      <div class="mb-3">
        <label>Descripción</label>
        <input type="text" name="descripcion" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Precio</label>
        <input type="text" name="precio" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        Guardar Servicio
      </button>
    </form>
  </div>
</div>
