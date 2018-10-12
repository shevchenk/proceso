<!-- /.modal -->
<div class="modal fade" id="clasificadortramitesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_clasificadortramites_modal" name="form_clasificadortramites_modal" action="" method="post">

          <div class="form-group">
            <label class="control-label">Nombre:</label>
            <textarea  class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre"></textarea> 
          </div>
          <div class="form-group">
            <label class="control-label">Tipo Trámite:
            </label>
            <select class="form-control" name="slct_tipo_tramite" id="slct_tipo_tramite">
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">Estado:
            </label>
            <select class="form-control" name="slct_estado_clasificador" id="slct_estado_clasificador">
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
