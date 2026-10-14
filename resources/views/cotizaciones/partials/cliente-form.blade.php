<div class="card border border-light-subtle shadow-sm rounded-3 p-3 bg-white h-100">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-primary-subtle text-primary p-1.5 rounded-2">
            <i class="bi bi-person-badge"></i>
        </span>
        <span class="fw-bold text-dark small text-uppercase">Datos del Cliente y Orden de Trabajo</span>
    </div>

    <div class="row g-2">
        <div class="col-12">
            <label for="cliente_razon_social" class="form-label small fw-medium mb-1">
                Cliente <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="cliente_razon_social" class="form-control form-control-sm"
                    autocomplete="off" placeholder="Buscar cliente por RUC o Razón Social..." data-error-field="cliente_id">
            </div>
            <div class="invalid-feedback"></div>
            <input type="hidden" id="cliente_id" name="cliente_id">
        </div>

        <div class="col-12 mt-2">
            <label for="ot_codigo" class="form-label small fw-medium mb-1">
                Orden de Trabajo (OT) <span class="text-danger">*</span>
            </label>
            <select name="ot_codigo" id="ot_codigo" class="form-select form-select-sm w-100" required>
                <option value="">-- Seleccione primero un cliente --</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>
    </div>
</div>