<!-- /.modal -->
<div class="modal fade" id="requisitoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_requisitos_modal" name="form_requisitos_modal" action="" method="post">
          <input type="hidden"  name="txt_poi_id" id="txt_poi_id">
          <div class="form-group">
            <label class="control-label">Nombre:</label>
            <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre">
          </div>
          <div class="form-group">
            <label class="control-label">Cantidad:</label>
            <input type="text" class="form-control" placeholder="Ingrese Cantidad" name="txt_cantidad" id="txt_cantidad">
          </div>
          <div class="form-group">
            <label class="control-label">Subir Archivo:</label>
            <div class="input-group">            
              <input type="text" readonly class="form-control" id="pdf_nombre"  name="pdf_nombre" value="" readonly="">
              <a class="btn btn-lg btn-danger input-group-addon" onclick='Limpiar("#pdf_nombre");'><i class="fa fa-eraser"></i></a>
            </div>
              <input type="text" style="display: none;" id="pdf_archivo" name="pdf_archivo">
              <label class="btn btn-warning btn-lg  btn-flat margin">
                  <i class="fa fa-file-pdf-o fa-lg"></i>
                  <input type="file" style="display: none;" onchange="masterG.onImagen(event,'#pdf_nombre','#pdf_archivo','#pdf_img');">
              </label>
              <div>
              <a id="">
              <img id="pdf_img" class="img-circle" style="height: 80px;width: 140px;border-radius: 8px;border: 1px solid grey;margin-top: 5px;padding: 8px">
              </a>
              </div>
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
