<!-- /.modal -->
<div class="modal fade" id="flujoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_flujos_modal" name="form_flujos_modal" action="" method="post">


          <div class="form-group">
            <label class="control-label">Nombre:</label>
            <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre">
          </div>


          <div class="form-group">
              <label class="control-label">Categoria:</label>
                <div id="div_categoria_user" >
                                    
                </div>
                
                <div id="div_categoria_master" style="display: none;">
                  <select class="form-control" name="slct_categoria_id" id="slct_categoria_id">
                  <option value="">.::Seleccione::.</option>
                  </select>
                </div>
          </div>




         <div class="form-group">
              <label class="control-label">Area del Proceso:</label>
              <select class="form-control" name="slct_area_id" id="slct_area_id">
              <option value="">.::Seleccione::.</option>
              </select>
          </div>

          <div class="form-group">
              <label class="control-label">Nivel del Proceso:</label>
              <select class="form-control" name="slct_nivel_proceso" id="slct_nivel_proceso">
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              </select>
          </div>

          <input type="hidden" id="slct_tipo_flujo" name="slct_tipo_flujo" value=1>
                
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
