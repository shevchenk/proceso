<!-- /.modal -->
<div class="modal fade" id="listDocDigital" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">Documentos Digitales</h4>
      </div>
      <div class="modal-body">
          <div class="row form-group">
              <div class="col-sm-12">
              <form id="Form_lstDigital" name="Form_lstDigital" method="POST" action="">
           <!--        <div class="col-sm-6">
                      <label class="control-label">Seleccione Tipo Documento a Listar:</label>
                  <select id="slct_tipo_documento" name="slct_tipo_documento">
                    <option value="">.::Todo::.</option>
                  </select>
                  </div> -->
                  <!-- <div class="col-sm-1">
                  <span class="btn btn-primary btn-lg" onclick=""><i class="fa fa-search fa-lg"></i></span>
                  </div> -->
                  <div class="col-sm-12"><br></div>
                  <div class="col-sm-6">
                    <div class="col-sm-4">
                       <label class="control-label">Fecha:</label>
                       <input type="text" class="form-control" placeholder="AAAA-MM-DD" id="fechaDoc" name="fechaDoc" value="<?php echo date('Y-m-d')?>"/>
                    </div>
                    <div class="col-sm-4">
                        <br>
                        <a class="btn btn-primary" onclick="CargarDocumentosFecha();">
                          <i class="fa fa-search">Listar Documentos</i>
                        </a>
                    </div>
                    <br>
                    <div class="col-sm-12 box-body table-responsive">
                        <table id="t_doc_digital" class="table table-bordered">
                            <thead class="bg-info">
                                <tr>
                                    <th style="width: 30%">Documento</th>
                                    <th style="width: 30%">Asunto</th>
                                    <th style="width: 5%">Seleccionar</th>
                                    <th style="width: 5%">Vista Impresión</th>

                                </tr>
                            </thead>
                            <tbody id="tb_doc_digital">
                            </tbody>
                            <tfoot class="bg-info">
                                <tr>
                                    <th style="width: 30%">Documento</th>
                                    <th style="width: 30%">Asunto</th>
                                      <th style="width: 5%">Seleccionar</th>
                                    <th style="width: 5%">Vista Impresión</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    Probando yo ando
                  </div>
              </form>
              </div>
              <div class="col-sm-12"><div  class="col-sm-12" id="mensaje"></div></div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->
