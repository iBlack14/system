<div class="border border-primary rounded p-3">
    <div class="row">
        <div class="col-md-12">

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Seleccione los módulos a incluir</div>
            <div class="card-body">
                <div class="form-check form-check-inline">
                    <input class="form-check-input toggle-section" type="checkbox" data-target="#manoObraSection" id="chkManoObra">
                    <label class="form-check-label" for="chkManoObra">Mano de Obra</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input toggle-section" type="checkbox" data-target="#transporteSection" id="chkTransporte">
                    <label class="form-check-label" for="chkTransporte">Transporte</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input toggle-section" type="checkbox" data-target="#servicioSection" id="chkServicio">
                    <label class="form-check-label" for="chkServicio">Servicios</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input toggle-section" type="checkbox" data-target="#materialSection" id="chkMaterial">
                    <label class="form-check-label" for="chkMaterial">Materiales</label>
                </div>
            </div>
        </div>
            
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-tpersonal" role="tabpanel" aria-labelledby="nav-tpersonal-tab">
                   <div class="border border-primary rounded p-3">
                            <div class="input-group input-group-sm">
                              <input type="text" id="tpersonal_id" class="form-control" placeholder="Buscar Transporte de Personal" autocomplete="off">
                              <input type="hidden" id="mano-tpersonal_id" name="tpersonal_id">
                              <button type="button" id="btnAgregarTPersonal" class="btn btn-success">Agregar</button>
                            </div>
                        <div class="row mt-4">
                        <div class="col-lg-12">
                          @include('cotizaciones.partials.detalles-tpersonal')
                        </div>                       
                        </div>
                        </div>
                </div>
                <div class="tab-pane fade" id="nav-servicios" role="tabpanel" aria-labelledby="nav-servicios-tab">
                   <div class="border border-primary rounded p-3">
                            <div class="input-group input-group-sm">
                              <input type="text" id="servicio_id" class="form-control" placeholder="Buscar servicio" autocomplete="off">
                              <input type="hidden" id="servicio_id" name="servicio_id">
                              <button type="button" id="btnAgregarServicio" class="btn btn-success">Agregar</button>
                            </div>
                            <div class="row mt-4">
                            <div class="col-lg-12">
                          @include('cotizaciones.partials.detalles-servicios')
                        </div> 
                        </div> 
                          </div>
                </div>
                <div class="tab-pane fade" id="nav-transporte" role="tabpanel" aria-labelledby="nav-transporte-tab">
                   <div class="border border-primary rounded p-3">
                            <div class="input-group input-group-sm">
                              <input type="text" id="transporte_id" class="form-control" placeholder="Buscar transporte" autocomplete="off">
                              <input type="hidden" id="transporte_id" name="transporte_id">
                              <button type="button" id="btnAgregarTransporte" class="btn btn-success">Agregar</button>
                            </div>
                            
                    </div>
                </div>
                 <div class="tab-pane fade" id="nav-materiales" role="tabpanel" aria-labelledby="nav-materiales-tab">
                   <div class="border border-primary rounded p-3">
                            <div class="input-group input-group-sm">
                              <input type="text" id="materiales_id" class="form-control" placeholder="Buscar materiales" autocomplete="off">
                              <input type="hidden" id="materiales_id" name="materiales_id">
                              <button type="button" id="btnAgregarMateriales" class="btn btn-success">Agregar</button>
                            </div>
                          </div>
                </div>
                
                <div class="tab-pane fade" id="nav-registrar" role="tabpanel" aria-labelledby="nav-registra-tab">
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label for="documento_tipo_codigo" class="form-label">Documento Tipo</label>
                            <select name="documento_tipo_codigo" id="documento_tipo_codigo"
                                class="form-select form-select-sm">

                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="numero_documento" class="form-label">Número documento</label>
                            <input type="text" class="form-control form-control-sm" id="numero_documento"
                                name="numero_documento">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="razon_social" class="form-label">Razón Social</label>
                            <input type="text" class="form-control form-control-sm" id="razon_social"
                                name="razon_social">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 d-flex align-items-end justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm" id="btnRegistrarCliente">Guardar Cliente</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>