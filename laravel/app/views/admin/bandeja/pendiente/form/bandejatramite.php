<div class="modal fade" id="retornarModal" tabindex="-1" role="dialog" aria-hidden="true">
<!-- <div class="modal fade" id="areaModal" tabindex="-1" role="dialog" aria-hidden="true"> -->
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">Retornar a la actividad anterior</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
                <!-- Inicia contenido -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Motivo del retorno</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-12">
                          <textarea class="form-control" id="txt_motivo_retorno" rows='8' placeholder="Ingrese el motivo"></textarea>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                <!-- Finaliza contenido -->
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" onClick="retornarOk()">Confirmar</button>
      </div>
    </div>
  </div>
</div>


<!-- /.modal -->
<div class="modal fade" id="usuarios_vieron_tramite" tabindex="-1" role="dialog" aria-hidden="true">
<!-- <div class="modal fade" id="areaModal" tabindex="-1" role="dialog" aria-hidden="true"> -->
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">Usuarios</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
                <!-- Inicia contenido -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Filtros</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="t_usuarios" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Persona</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tb_usuarios">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Persona</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </tfoot>
                        </table>

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                <!-- Finaliza contenido -->
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>
    </div>
  </div>
</div>
<!-- /.modal -->
