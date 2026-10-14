<!-- Modal -->
<div class="modal fade" id="modalUpdate" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-sm-down">
    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
      <form id="formUpdate" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="method_field" name="_method">
        
        <div class="modal-header bg-white border-bottom py-3 px-4">
          <div class="d-flex align-items-center gap-2.5">
            <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
              <i class="bi bi-box-seam fs-6"></i>
            </span>
            <div>
              <h5 class="modal-title fw-bold text-dark mb-0" id="modalTitle">Nuevo Producto / Insumo</h5>
              <small class="text-muted">Configuración técnica, estructura de costos y ubicación</small>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4 bg-light-subtle">
          <!-- Section 1: Identificación y Clasificación -->
          <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white mb-3">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span class="badge bg-primary-subtle text-primary p-1.5 rounded-2">
                <i class="bi bi-card-heading"></i>
              </span>
              <span class="fw-bold text-dark small text-uppercase">1. Identificación y Clasificación</span>
            </div>

            <div class="row g-3">
              <div class="col-md-3">
                <label for="codigo" class="form-label small fw-semibold text-dark mb-1">
                  Código <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-light text-muted"><i class="bi bi-upc-scan"></i></span>
                  <input type="text" id="codigo" name="codigo" class="form-control font-monospace text-uppercase" placeholder="Ej: ACO-001" required>
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-5">
                <label for="nombre" class="form-label small fw-semibold text-dark mb-1">
                  Nombre del Producto <span class="text-danger">*</span>
                </label>
                <input type="text" id="nombre" name="nombre" class="form-control form-control-sm" placeholder="Ej: TEE PVC 3' SCH80" required>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-2">
                <label for="unidad_codigo" class="form-label small fw-semibold text-dark mb-1">
                  Unidad <span class="text-danger">*</span>
                </label>
                <select name="unidad_codigo" id="unidad_codigo" class="form-select form-select-sm" required></select>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-2">
                <label for="afectacion_tipo_codigo" class="form-label small fw-semibold text-dark mb-1">
                  Afec. Tipo <span class="text-danger">*</span>
                </label>
                <select name="afectacion_tipo_codigo" id="afectacion_tipo_codigo" class="form-select form-select-sm" required></select>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-8">
                <label for="descripcion" class="form-label small fw-semibold text-dark mb-1">Descripción Detallada</label>
                <input type="text" id="descripcion" name="descripcion" class="form-control form-control-sm" placeholder="Especificaciones, marca o detalles técnicos (opcional)">
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-4">
                <label for="imagen" class="form-label small fw-semibold text-dark mb-1">Foto / Imagen</label>
                <input type="file" id="imagen" name="imagen" class="form-control form-control-sm" accept="image/*">
                <div class="invalid-feedback"></div>
                <div class="mt-2 text-center" id="contenedor_preview_imagen">
                  <img src="" alt="Previsualización" id="imagen_producto" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 80px; display: none;">
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Estructura de Costos, Precios y Márgenes -->
          <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white mb-3">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span class="badge bg-success-subtle text-success p-1.5 rounded-2">
                <i class="bi bi-cash-coin"></i>
              </span>
              <span class="fw-bold text-dark small text-uppercase">2. Estructura de Precios y Márgenes</span>
            </div>

            <div class="row g-3 align-items-center">
              <div class="col-md-4">
                <label for="costo_unitario" class="form-label small fw-semibold text-dark mb-1">
                  Costo Unitario (S/) <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-light text-muted">S/</span>
                  <input type="number" step="0.01" id="costo_unitario" name="costo_unitario" class="form-control text-end fw-semibold" placeholder="0.00" required>
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-4">
                <label for="utilidad" class="form-label small fw-semibold text-dark mb-1">
                  Margen Utilidad (%) <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-light text-muted">%</span>
                  <input type="number" step="0.01" id="utilidad" name="utilidad" class="form-control text-end fw-semibold" placeholder="0.00" required>
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-4">
                <label for="precio_venta" class="form-label small fw-bold text-primary mb-1">
                  Precio de Venta Sugerido <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-primary-subtle text-primary border-primary-subtle fw-bold">S/</span>
                  <input type="text" id="precio_venta" name="precio_venta" class="form-control text-end fw-bold text-primary bg-primary-subtle border-primary-subtle fs-6" readonly>
                </div>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <!-- Escalas de precios por volumen -->
            <div class="mt-3 pt-2 border-top">
              <span class="text-muted small fw-medium d-block mb-2">
                <i class="bi bi-percent me-1"></i> Descuentos / Márgenes por Escalas de Volumen:
              </span>
              <div class="row g-2">
                <div class="col-6 col-md-3">
                  <label for="porcentaje5" class="form-label text-muted small mb-1" style="font-size: 0.76rem;">Escala 1 - 5 (%)</label>
                  <input type="number" step="0.01" id="porcentaje5" name="porcentaje5" class="form-control form-control-sm text-end" required>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-6 col-md-3">
                  <label for="porcentaje10" class="form-label text-muted small mb-1" style="font-size: 0.76rem;">Escala 6 - 10 (%)</label>
                  <input type="number" step="0.01" id="porcentaje10" name="porcentaje10" class="form-control form-control-sm text-end" required>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-6 col-md-3">
                  <label for="porcentaje50" class="form-label text-muted small mb-1" style="font-size: 0.76rem;">Escala 11 - 50 (%)</label>
                  <input type="number" step="0.01" id="porcentaje50" name="porcentaje50" class="form-control form-control-sm text-end" required>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-6 col-md-3">
                  <label for="porcentaje100" class="form-label text-muted small mb-1" style="font-size: 0.76rem;">Escala 50 - 100+ (%)</label>
                  <input type="number" step="0.01" id="porcentaje100" name="porcentaje100" class="form-control form-control-sm text-end" required>
                  <div class="invalid-feedback"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 3: Inventario y Ubicación Física -->
          <div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center gap-2 mb-3">
              <span class="badge bg-warning-subtle text-warning p-1.5 rounded-2">
                <i class="bi bi-geo-alt"></i>
              </span>
              <span class="fw-bold text-dark small text-uppercase">3. Inventario y Ubicación Física</span>
            </div>

            <div class="row g-3">
              <div class="col-md-3">
                <label for="stock" class="form-label small fw-semibold text-dark mb-1">
                  Stock Disponible <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                  <span class="input-group-text bg-light text-muted"><i class="bi bi-stack"></i></span>
                  <input type="number" step="0.01" id="stock" name="stock" class="form-control text-end fw-bold" required>
                </div>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-3">
                <label for="almacen" class="form-label small fw-semibold text-dark mb-1">
                  Almacén <span class="text-danger">*</span>
                </label>
                <input type="text" id="almacen" name="almacen" class="form-control form-control-sm" placeholder="Ej: Principal / 1" required>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-3">
                <label for="ubicacion_columna" class="form-label small fw-semibold text-dark mb-1">
                  Ub. Columna <span class="text-danger">*</span>
                </label>
                <input type="text" id="ubicacion_columna" name="ubicacion_columna" class="form-control form-control-sm" placeholder="Ej: A / 1" required>
                <div class="invalid-feedback"></div>
              </div>

              <div class="col-md-3">
                <label for="ubicacion_fila" class="form-label small fw-semibold text-dark mb-1">
                  Ub. Fila <span class="text-danger">*</span>
                </label>
                <input type="text" id="ubicacion_fila" name="ubicacion_fila" class="form-control form-control-sm" placeholder="Ej: 3 / B" required>
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
            <i class="bi bi-check-circle me-1"></i> Guardar Producto
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function calcularPrecioVenta() {
    let costo = parseFloat(document.getElementById("costo_unitario").value);
    let utilidad = parseFloat(document.getElementById("utilidad").value);

    if (!isNaN(costo) && !isNaN(utilidad) && costo >= 0) {
        let precioVenta = costo * (1 + (utilidad / 100));
        document.getElementById("precio_venta").value = precioVenta.toFixed(2);
    } else {
        document.getElementById("precio_venta").value = "";
    }
}

document.getElementById("costo_unitario").addEventListener("input", calcularPrecioVenta);
document.getElementById("utilidad").addEventListener("input", calcularPrecioVenta);

// Previsualización instantánea de imagen al seleccionarla
document.getElementById("imagen").addEventListener("change", function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById("imagen_producto");
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            preview.src = evt.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
