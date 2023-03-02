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
                  <div class="col-sm-4">
                    <div class="col-sm-3">
                      <label class="control-label">Fecha:</label>
                      <input type="text" class="form-control" placeholder="AAAA-MM-DD" id="fechaDoc" name="fechaDoc" value="<?php echo date('Y-m-d')?>"/>
                    </div>
                    <div class="col-sm-4">
                      <br>
                      <a class="btn btn-primary" onclick="CargarDocumentosFecha();">
                        <i class="fa fa-search">Listar Documentos</i>
                      </a>
                    </div>
                    <div class="col-sm-12">
                      <hr>
                    </div>
                    <div class="col-sm-12 box-body table-responsive">
                        <table id="t_doc_digital" class="table table-bordered">
                            <thead class="bg-info">
                                <tr>
                                    <th style="width: 30%">Documento</th>
                                    <th style="width: 5%">Editar</th>
                                    <th style="width: 5%">Seleccionar</th>

                                </tr>
                            </thead>
                            <tbody id="tb_doc_digital">
                            </tbody>
                            <tfoot class="bg-info">
                                <tr>
                                    <th style="width: 30%">Documento</th>
                                    <th style="width: 5%">Editar</th>
                                    <th style="width: 5%">Seleccionar</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                  </div>
                  <div class="col-sm-8">
                    <div class="form-group row">
                          <div class="col-md-12">
                            <label class="control-label">Generar documento:</label>
                          </div>
                          <div class="col-md-12">
                            <div class="col-md-5">
                              <label>Área:</label>
                              <select id="slct_area_id" name="slct_area_id" onChange="ListarDocumentos(this.value);"></select>
                            </div>
                            <div class="col-md-5">
                              <label>Tipo de documento:</label>
                              <select id="slct_tipo_documento_id" name="slct_tipo_documento_id" onChange="CalcularCorrelativo();"></select>
                            </div>
                            <div class="col-md-2">
                              <label>Correlativo:</label>
                              <input type="text" maxlength="6" class="form-control txttittle" placeholder="Ingrese Titulo" name="txt_titulo" id="txt_titulo">
                            </div>
                            <div class="col-md-2" style="display:none;">
                              <label>Nemónico:</label>
                              <label id="lblArea" style="margin-top:5px;">AREA - 2020</label>
                            </div>
                          <div>
                          <div class="col-md-12">
                          <div class="col-md-3">
                          <br>
                            <label>Fecha del documento:</label>
                            <input type='text' id="txt_fecha_documento" name="txt_fecha_documento" value="<?php echo date("Y-m-d");?>" readonly class='form-control' placeholder='YYYY-MM-DD'> 
                          </div>
                          <div class="col-md-4">
                          <br>
                            <a class="btn btn-info btn-lg" onclick='GenerarCodigo();' ><i class="fa fa-qrcode"> Generar QR </i></a>
                          </div>
                          </div>
                        <input type="hidden" id="txt_titulofinal" name="txt_titulofinal" value="">
                        <div class="col-md-12">
                        <br>
                          <table id="t_darchivo" class="table table-bordered">
                            <thead class="bg-warning color-palette">
                                <tr>
                                    <th style="width:40%"># Documento Generado</th>
                                    <th style="width:20%">QR Generado</th>
                                    <th style="width:40%">Subir Archivo ó Registrar URL del Archivo</th>
                                </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td style="vertical-align:middle; text-aling:center;" id="td_documento"></td>
                                <td><img id="img_qr" style="height: 80px;width: 140px;border-radius: 8px;border: 1px solid grey;margin-top: 5px;padding: 8px" /></td>
                                <td>
                                  <input type="text" readonly="" class="form-control input-sm" id="doc_nombre" name="doc_nombre" value="">
                                  <input type="text" style="display: none;" id="doc_archivo" name="doc_archivo">
                                  <label class="btn btn-default btn-flat margin btn-xs">
                                    <i class="fa fa-file-pdf-o fa-lg"></i>
                                    <i class="fa fa-file-word-o fa-lg"></i>
                                    <i class="fa fa-file-image-o fa-lg"></i>
                                    <input type="file" style="display: none;" onchange="onArchivos(event,this);">
                                  </label>
                                  <input type='hidden' name='txt_doc_digital_id' id='txt_doc_digital_id' value=''>
                                  <input type="text" id="doc_url" name="doc_url" class="form-control" value='' placeholder='Ingrese una URL'>
                                  <a class="btn btn-info" onclick="GuardarArchivo()"><i class="fa fa-save"></i>Guardar Archivo</a>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
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
