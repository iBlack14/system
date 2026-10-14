<div class="border border-success rounded p-3">
    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tecnico-tab" role="tablist">
                    <button class="nav-link active" id="nav-buscar-tecnico-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-buscar-tecnico" type="button" role="tab"
                        aria-controls="nav-buscar-tecnico" aria-selected="true">
                        Buscar Técnico
                    </button>
                    
                </div>
            </nav>
            <div class="tab-content" id="nav-tecnico-tabContent">
                <!-- Buscar Técnico -->
                <div class="tab-pane fade show active" id="nav-buscar-tecnico" role="tabpanel"
                    aria-labelledby="nav-buscar-tecnico-tab">
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="tecnico_nombre" class="form-label">Técnico</label>
                            <input type="text" id="tecnico_nombre" class="form-control form-control-sm"
                                autocomplete="off" placeholder="Buscar técnico..." data-error-field="tecnico_id">
                            <div class="invalid-feedback"></div>
                            <input type="hidden" id="tecnico_id" name="tecnico_id">
                        </div>
                    </div>
                </div>

                <!-- Registrar Técnico -->
                <div class="tab-pane fade" id="nav-registrar-tecnico" role="tabpanel"
                    aria-labelledby="nav-registrar-tecnico-tab">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="tecnico_dni" class="form-label">DNI</label>
                            <input type="text" class="form-control form-control-sm" id="tecnico_dni" name="tecnico_dni">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="tecnico_nombre_registro" class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control form-control-sm" id="tecnico_nombre_registro"
                                name="tecnico_nombre_registro">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="tecnico_profesion" class="form-label">Profesión</label>
                            <input type="text" class="form-control form-control-sm" id="tecnico_profesion"
                                name="tecnico_profesion">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="tecnico_email" class="form-label">Email</label>
                            <input type="text" class="form-control form-control-sm" id="tecnico_email"
                                name="tecnico_email">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="tecnico_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control form-control-sm" id="tecnico_telefono"
                                name="tecnico_telefono">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 d-flex align-items-end justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" id="btnRegistrarTecnico">
                                Guardar Técnico
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
