<div class="card quote-panel client-form-card p-3 bg-white h-100">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-primary-subtle text-primary p-1.5 rounded-2">
            <i class="bi bi-person-badge"></i>
        </span>
        <span class="fw-bold text-dark small text-uppercase"><span class="quote-step">1</span>Cliente y orden de trabajo</span>
    </div>

    <div class="row g-2">
        <div class="col-12">
            <label for="cliente_razon_social" class="form-label small fw-medium mb-1">
                Cliente <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="cliente_razon_social" class="form-control form-control-sm"
                    autocomplete="off" placeholder="Escribe RUC o razón social..." data-error-field="cliente_id">
            </div>
            <div class="selection-help"><i class="bi bi-cursor-fill text-primary"></i><span>Escribe al menos 3 caracteres y <strong>selecciona un resultado de la lista</strong>. Solo escribir el número no selecciona al cliente.</span></div>
            <div class="invalid-feedback"></div>
            <input type="hidden" id="cliente_id" name="cliente_id">
        </div>

        <div class="col-12 mt-2">
            <label for="ot_codigo" class="form-label small fw-medium mb-1">
                Orden de Trabajo (OT) <span class="text-danger">*</span>
            </label>
            <select name="ot_codigo" id="ot_codigo" class="form-select form-select-sm w-100" required>
                <option value="">Selecciona primero un cliente de la lista</option>
            </select>
            <div class="invalid-feedback"></div>
        </div>
    </div>
</div>
