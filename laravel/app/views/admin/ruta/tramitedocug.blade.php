<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::style('lib/jquery-bootstrap-validator/bootstrapValidator.min.css') }}
    
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('lib/jquery-bootstrap-validator/bootstrapValidator.min.js') }}

    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}

    {{ HTML::script('lib/jquery.validate.js') }}
    <script src='lib/recaptcha/api.js'></script>

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.ruta.js.tramitedocug_ajax' )
    @include( 'admin.ruta.js.tramitedocug' )
    @include( 'admin.ruta.js.ruta_ajax' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<style type="text/css">
.box{
    border: 2px solid #c1c1c1;
    border-radius: 5px;
}
.filtros{
    margin-top: 10px;
    margin-bottom: 0px;
}

.right{
  text-align: right;
}

td, th{
    text-align:center;
}
  
.modal-body label,.modal-body span{
  font-size: 13px;
}

.form-control{
    border-radius: 5px !important;
}
    /*
    Component: Mailbox
*/
.mailbox .table-mailbox {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}
.mailbox .table-mailbox tr.unread > td {
  background-color: rgba(0, 0, 0, 0.05);
  color: #000;
  font-weight: 600;
}
.mailbox .table-mailbox .unread
/*.mailbox .table-mailbox tr > td > .fa.fa-ban,*/
/*.mailbox .table-mailbox tr > td > .glyphicon.glyphicon-star,
.mailbox .table-mailbox tr > td > .glyphicon.glyphicon-star-empty*/ {
  /*color: #f39c12;*/
  cursor: pointer;
}
.mailbox .table-mailbox tr > td.small-col {
  width: 30px;
}
.mailbox .table-mailbox tr > td.name {
  width: 150px;
  font-weight: 600;
}
.mailbox .table-mailbox tr > td.time {
  text-align: right;
  width: 100px;
}
.mailbox .table-mailbox tr > td {
  white-space: nowrap;
}
.mailbox .table-mailbox tr > td > a {
  color: #444;
}

.btn-yellow{
    color: #0070ba;
    background-color: ghostwhite;
    border-color: #ccc;
    font-weight: bold;
}

    fieldset{
        max-width: 100% !important;
        border: 3px solid #999;
        padding:10px 20px 2px 20px;
        border-radius: 10px; 
    }

    .margin-top-10{
         margin-top: 10px;   
    }



@media screen and (max-width: 767px) {
  .mailbox .nav-stacked > li:not(.header) {
    float: left;
    width: 50%;
  }
  .mailbox .nav-stacked > li:not(.header).header {
    border: 0!important;
  }
  .mailbox .search-form {
    margin-top: 10px;
  }
}
</style>

        <!-- Main content -->
        <section class="content">
            <!-- Inicia contenido -->

            <div class="crearPreTramite">
              <h3>INICIO DE GESTIÓN</h3>

              <form id="FormCrearPreTramite" method="post">
                <div class="col-md-12" style="margin-top:10px">
                    <!-- <div class="col-md-2" style="padding-top: 5px;">
                      <span>TIPO DOC. INGRESO: </span>
                    </div>
                    <div class="col-md-3">
                      <select class="form-control" id="cbo_tipodocumento" name="cbo_tipodocumento">
                          <option value>.::Seleccione::.</option>
                          <option value="1">Documento Simple</option>
                          <option value="2">Expediente</option>
                      </select>
                    </div> -->
                    <div class="col-md-4">
                      
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-2" style="padding-top: 5px">
                        <span>ÁREA DE INICIO DEL SERVICIO:</span>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="slct_areas" id="slct_areas">
                        </select>
                    </div>
                </div>

                <div class="col-md-12 tipoSolicitante" style="margin-top:10px">
                    <div class="col-md-2" style="padding-top: 5px">
                        <span>TIPO SOLICITANTE:</span>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="cbo_tiposolicitante" name="cbo_tiposolicitante">
                              <option value="-1">Selecciona</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top:10px">
                    <div class="col-md-2" style="padding-top: 5px;">
                      <span>TIPO DE SERVICIO: </span>
                    </div>
                    <div class="col-md-5">
                      <select class="form-control" onChange="ValidarLimite();" id="cbo_tipotramite" name="cbo_tipotramite">
                          <option value="-1">Selecciona</option>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <span class="btn btn-primary btn-sm" onclick="consultar()">Buscar servicio</span>
                    </div>
                </div>

                <div class="col-md-12 table-responsive solicitantes" style="margin-top:10px">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title" tabindex="0">Solicitante(s)</h3>
                            <div class="box-tools pull-left">
                                <span class="btn btn-primary btn-sm" id="btnTipoSolicitante">Buscar solicitante</span>
                            </div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body col-md-12">
                            <table id="t_usuarios" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="bg-navy">
                                        <th style="width:80px;">Tipo Solicitante</th>
                                        <th style="width:120px;">Solicitante</th>
                                        <th style="width:50px;">DNI / RUC</th>
                                        <th style="width:50px;">Teléfono</th>
                                        <th style="width:50px;">Celular (Solo Persona Natural)</th>
                                        <th style="width:100px;">Email</th>
                                        <th style="width:150px;">Dirección</th>
                                        <th style="width:40px;">[-]</th>
                                    </tr>
                                </thead>
                                <tbody id="tb_usuarios"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 table-responsive" style="margin-top:10px">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title" tabindex="0">Referido(s)</h3>
                            <div class="box-tools pull-left">
                                <span class="btn btn-primary btn-sm" id="btnReferido">Buscar documento a referir</span>
                            </div>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body col-md-6 text-center">
                            <table id="t_referidos" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="bg-navy">
                                        <th style="width:120px;">Referido</th>
                                        <th style="width:40px;">[-]</th>
                                    </tr>
                                </thead>
                                <tbody id="tb_referidos"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="padding: 2% 6% 1% 4%;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
                    <div class="col-md-12 form-group">
                      <div class="col-md-7">
                        <div class="col-md-4">
                          <span>SERVICIO SELECCIONADO:: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_nombretramite" id="txt_nombretramite" class="form-control" disabled>
                          <input type="hidden" name="txt_idclasitramite" id="txt_idclasitramite" class="form-control">
                          <input type="hidden" name="txt_idarea" id="txt_idarea" class="form-control">
                          <input type="hidden" name="txt_persona_id" id="txt_persona_id">
                        </div>
                      </div>
                      <div class="col-sm-5">
                        <div class="col-md-4">
                          <span>LUGAR DE PROCEDENCIA: </span>
                        </div>
                        <div class="col-md-8">
                          <select class="form-control" name="slct_local" id="slct_local">
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-7">
                        <div class="col-md-4" style="padding-top: 5px;">
                          <span>DOCUMENTO PRESENTADO(Para solicitar el servicio): </span>
                        </div>
                        <div class="col-md-8">
                          <select class="form-control" onChange="ValidarDoc();" id="cbo_tipodoc" name="cbo_tipodoc">
                              <option value="-1">Selecciona</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-5  form-group">
                        <div class="col-md-5">
                          <span>CANTIDAD DE FOLIOS(Considere la suma de las páginas de los requisitos, más el documento presentado para solicitar el servicio): </span>
                        </div>
                        <div class="col-md-7">
                          <input type="text" name="txt_numfolio" id="txt_numfolio" class="form-control">
                        </div>
                      </div>

                      <div class="col-md-5  form-group tipo_documento" style="display:none;">
                        <div class="col-md-5">
                          <span>N° DEL DOCUMENTO PRESENTADO: </span>
                        </div>
                        <div class="col-md-7">
                          <input type="text" name="txt_tipodoc" id="txt_tipodoc" class="form-control">
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12" style="padding: 2% 4% 2% 4%; display:none;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
                    <legend style="width: 8%">Operador</legend>
                    <div class="col-md-12 form-group">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>DNI: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_userdni" id="txt_userdni" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>NOMBRE: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_usernomb" id="txt_usernomb" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 form-group">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>APELLIDO PATERNO: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_userapepat" id="txt_userapepat" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>APELLIDO MATERNO: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_userapemat" id="txt_userapemat" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 form-group hidden">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>TELEFONO: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_usertelf" id="txt_usertelf" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>DIRECCION: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_userdirec" id="txt_userdirec" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12 usuario" style="padding: 2% 4% 2% 4%;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
                    <legend style="width: 8%">Observación</legend>
                    <div class="col-md-12 form-group">
                      <div class="col-md-12">
                          <textarea name="txt_observacion" id="txt_observacion" rows="3" class="form-control"></textarea>
                      </div>
                    </div>
                  </fieldset>
                </div>

               

                <div class="col-md-12 form-group" style="text-align: right;padding-right: 4%;">                  
                  <span class="btn btn-warning btn-sm" onclick="generarPreTramite()">Generar</span>
                {{--   <input type="submit" class="btn btn-primary btn-sm btnAction" id="" value="Guardar" onclick="generarPreTramite()"> --}}
                  {{-- <span class="btn btn-primary btn-sm">CANCELAR</span>              --}}   
                </div>
                <br><hr><br>
                <div class="col-md-12" id="reporte">
                    <div class="box-body table-responsive">
                        <table id="t_reporte" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>COD</th>
                                    <th>PERSONA SOLICITANTE</th>
                                    <th>EMPRESA SOLICITANTE</th>
                                    <th>TIPO SOLICITANTE</th>
                                    <th>TIPO TRAMITE</th>
                                    <th>TIPO DOCUMENTO</th>
                                    <th>NOMBRE TRAMITE</th>
                                    <th>FECHA REGISTRADA</th>
                                    <th>Imprimir</th>
                                </tr>
                            </thead>
                            <tbody id="tb_reporte">
                            </tbody>
                        </table>
                    </div>
                </div>
              </form>
            </div>
            <!-- Finaliza contenido -->
    </section><!-- /.content -->
@stop
@section('formulario')
  @include( 'admin.ruta.form.crearUsuario' )
  @include( 'admin.ruta.form.crearEmpresa' )
  @include( 'admin.ruta.form.selectPersona' )
  @include( 'admin.ruta.form.buscartramite' )
  @include( 'admin.ruta.form.empresasbyuser' )
  @include( 'admin.ruta.form.rutaflujo' )
  @include( 'admin.ruta.form.ruta' )
  @include( 'admin.ruta.form.referente' )
  @include( 'admin.ruta.form.persona2' )
@stop
