<!-- /.modal -->
<div class="modal fade" id="documentoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_documentos_modal" name="f" action="" method="post">

          <div class="form-group">
            <label class="control-label">Nombre:</label>
            <input type="text" class="form-control" name="txt_nombre" id="txt_nombre">
          </div>

          <div class="form-group">
            <label class="control-label">Nemónico:</label>
            <input type="text" class="form-control" name="txt_nemonico" id="txt_nemonico">
          </div>

          <div class="form-group hidden">
            <label class="control-label">Tipo:</label>
            <select class="form-control" name="slct_tipo" id="slct_tipo" onchange="validarSolicitante();">
                <option value='Ingreso' selected>Ingreso</option>
            </select>
          </div>

          <div class="form-group hidden">
            <label class="control-label">Este documento es público::</label>
            <select class="form-control" name="slct_publico" id="slct_publico">
                <option value='0' selected>No</option>
            </select>
          </div>

          <div class="form-group hidden">
            <label class="control-label">Indique el nivel del documento::</label>
            <select class="form-control" name="slct_nivel" id="slct_nivel">
                <option value='1' selected>1</option>
            </select>
          </div>
          
          <div class="form-group hidden">
            <label class="control-label">Quien es el solicitante::</label>
            <select class="form-control" name="slct_solicitante" id="slct_solicitante">
                <option value='Interno' selected>Interno</option>
            </select>
          </div>

          <div class="form-group hidden">
            <label class="control-label">Pide nro de documento?:</label>
            <select class="form-control" name="slct_pide_nro" id="slct_pide_nro">
                <option value='0' selected>No</option>
            </select>
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
