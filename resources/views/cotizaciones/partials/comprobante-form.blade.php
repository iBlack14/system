<div class="card quote-panel p-3 bg-white h-100">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-secondary-subtle text-secondary p-1.5 rounded-2">
            <i class="bi bi-wallet2"></i>
        </span>
        <span class="fw-bold text-dark small text-uppercase">Datos comerciales</span>
    </div>
    <div class="row g-2">
        <!-- Fila 1: Forma de pago y Estado -->
        <div class="col-md-6">
            <label for="forma_pago" class="form-label small fw-medium mb-1">Forma de pago <span class="text-danger">*</span></label>
            <select name="forma_pago" id="forma_pago" class="form-select form-select-sm" required>
                <option value="contado">Contado</option>
                <option value="credito">Crédito</option>
            </select>
        </div>

        <div class="col-md-6">
            <label for="estado" class="form-label small fw-medium mb-1">Estado</label>
            <input type="text" id="estado" name="estado" class="form-control form-control-sm bg-light" readonly placeholder="Generado autom.">
            <div class="invalid-feedback"></div>
        </div>

        <!-- Fila 2: Observaciones -->
        <div class="col-12 mt-2">
            <label for="observaciones" class="form-label small fw-medium mb-1">Observaciones</label>
            <input type="text" id="observaciones" name="observaciones" class="form-control form-control-sm" placeholder="Notas adicionales o requerimientos...">
            <div class="invalid-feedback"></div>
        </div>
    </div>
</div>
