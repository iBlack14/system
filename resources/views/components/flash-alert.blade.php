@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3" role="alert">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success text-white p-2 rounded-circle">
                <i class="bi bi-check-lg"></i>
            </span>
            <div>
                <strong class="d-block text-success-emphasis fw-semibold">Operación exitosa</strong>
                <span class="text-success small">{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3" role="alert">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger text-white p-2 rounded-circle">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </span>
            <div>
                <strong class="d-block text-danger-emphasis fw-semibold">Error</strong>
                <span class="text-danger small">{{ session('error') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif
