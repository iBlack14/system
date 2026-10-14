<div class="border border-primary rounded p-3">
    <div class="row">
        <div class="col-md-12">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-buscar-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-buscar" type="button" role="tab" aria-controls="nav-buscar"
                        aria-selected="true">Buscar Cliente</button>
                    
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-buscar" role="tabpanel" aria-labelledby="nav-buscar-tab">
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="cliente_razon_social" class="form-label">Cliente</label>
                            <input type="text" id="cliente_razon_social" class="form-control form-control-sm"
                                autocomplete="off" placeholder="Buscar cliente..." data-error-field="cliente_id">
                            <div class="invalid-feedback"></div>
                            <input type="hidden" id="cliente_id" name="cliente_id">
                        </div>

                    </div>
                </div>               
            </div>
        </div>
    </div>
</div>