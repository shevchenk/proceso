<!-- /.modal -->
<div class="modal fade" id="tipotramiteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_tipotramites_modal" name="form_tipotramites_modal" action="" method="post">

          <div class="form-group">
            <label class="control-label">Nombre:</label>
            <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre">
          </div>

          <div class="form-group">
            <label class="control-label">Quien es el solicitante:</label>
            <select class="form-control" onChange="ValidaInterno(this.value);" name="slct_solicitante" id="slct_solicitante">
                <option value="">.::Seleccione::.</option>
                <option value='Cliente'>Cliente</option>
                <option value='Externo'>Externo</option>
                <option value='Interno'>Interno</option>
            </select>
          </div>

          <div class="form-group">
            <label class="control-label">Quien inicia el trámite:</label>
            <select class="form-control" name="slct_inicia" id="slct_inicia">
                <option value="">.::Seleccione::.</option>
                <option value="Ambos">Ambos</option>
                <option value='Cliente'>Cliente</option>
                <option value='Institución'>Institución</option>
            </select>
          </div>

          <div class="form-group">
            <label class="control-label">El solicitante puede realizar seguimiento:</label>
            <select class="form-control" name="slct_seguimiento" id="slct_seguimiento">
                <option value="">.::Seleccione::.</option>
                <option value="0">No</option>
                <option value='1'>Si</option>
            </select>
          </div>

          <div class="form-group validacantsolicitante">
            <label class="control-label">Cantidad de solicitantes:</label>
            <select class="form-control" name="slct_cant_solicitante" id="slct_cant_solicitante">
                <option value='1'>1</option>
                <option value="Muchos">Muchos</option>
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
