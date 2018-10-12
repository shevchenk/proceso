<!-- /.modal -->
<div class="modal fade" id="cargoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body" style="overflow: hidden;">
        <form id="form_cargos" name="form_cargos" action="" method="post">
          
          <div class="col-md-12">
            <div class="form-group row">
              <label for="" class="col-md-2 col-form-label">SERIE</label>
              <div class="col-md-4">
                <input class="form-control" name="txt_serie" id="txt_serie" type="text" value="" placeholder="IND003155">
              </div>
            </div>

            <div class="form-group row">              
              <div class="col-md-4">
                <label class="col-form-label">Nombre
                  <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                      <i class="fa fa-exclamation"></i>
                  </a>
                </label>
                <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre">
              </div>
              
              <div class="col-md-4">
                <label class="col-form-label">Paterno
                  <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                      <i class="fa fa-exclamation"></i>
                  </a>
                </label>
                <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_paterno" id="txt_paterno">
              </div>

              <div class="col-md-4">
                <label class="col-form-label">Materno
                  <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                      <i class="fa fa-exclamation"></i>
                  </a>
                </label>
                <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_materno" id="txt_materno">
              </div>
            </div>


            <div class="form-group row">              
              <div class="col-md-4">
                <label class="col-form-label">Fecha Entrega</label>
                <div class="input-group">
                  <input class="form-control fechas" placeholder="yyyy-mm-dd" id="txt_fecha_entrega" name="txt_fecha_entrega" type="text" style="cursor:pointer;" readonly>
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></span>
                </div>
              </div>
              
              <div class="col-md-4">
                <label class="col-form-label">Fecha Nace</label>
                <div class="input-group">
                  <input class="form-control fechas" placeholder="yyyy-mm-dd" id="txt_fecha_nace" name="txt_fecha_nace" type="text" style="cursor:pointer;" readonly>
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></span>
                </div>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-4">
                <label class="col-form-label">Sexo</label>
                <input type="text" class="form-control" placeholder="Ingrese Sexo" name="txt_sexo" id="txt_sexo">
              </div>
              
              <div class="col-md-4">
                <label class="col-form-label">Raza</label>
                <input type="text" class="form-control" placeholder="Ingrese Raza" name="txt_raza" id="txt_raza">
              </div>
            </div>
          <!--
            <div class="form-group row">
              <div class="col-md-8">
                  <label>Seleccionar Foto</label>
                  <input type="file" class="form-control" id="carga" name="carga" >
              </div>
            </div>
          -->
            <div class="form-group row">
              <label class="control-label">Estado:
              </label>
              <select class="form-control" name="slct_estado" id="slct_estado">
                  <option value='0'>Inactivo</option>
                  <option value='1' selected>Activo</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="fileModalLabel">Subir imagen del cargo.</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form enctype="multipart/form-data" action="" method="POST" id="xFrm">
          <input type="file" id="cargo_comprobante" name="cargo_comprobante">
          <input type="hidden" id="file_dni" name="file_dni">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" onClick="sendImage();" class="btn btn-primary">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->