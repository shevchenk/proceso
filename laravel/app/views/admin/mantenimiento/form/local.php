<!-- /.modal -->
<div class="modal fade" id="localModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_locales_modal" name="form_locales_modal" action="" method="post" >
          <div class="form-group">
            <label class="control-label">Local
                <a id="error_local" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Local">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <input type="text" class="form-control" placeholder="Ingrese Local" name="txt_local" id="txt_local">
          </div>
          <div class="form-group">
            <label class="control-label">Dirección
                <a id="error_direccion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Dirección">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <input type="text" class="form-control" placeholder="Ingrese Dirección" name="txt_direccion" id="txt_direccion">
          </div>
          <div class="form-group">
            <label class="control-label">Fecha de Inicio del Local
                <a id="error_fecha_inicio" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Fecha de Inicio del Local">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <input type="text" class="form-control fecha" placeholder="Ingrese Fecha de Inicio del Local" name="txt_fecha_inicio" id="txt_fecha_inicio">
          </div>
          <div class="form-group">
            <label class="control-label">Fecha de Cierre del Local
                <a id="error_fecha_final" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Fecha de Cierre del Local">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <input type="text" class="form-control fecha" placeholder="Ingrese Fecha de Cierre del Local" name="txt_fecha_final" id="txt_fecha_final">
          </div>
          <div class="form-group">
            <label class="control-label">Estado:
            </label>
            <select class="form-control" name="slct_estado" id="slct_estado">
                <option value='0'>Inactivo</option>
                <option value='1' selected>Activo</option>
            </select>
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
<!-- /.modal -->
