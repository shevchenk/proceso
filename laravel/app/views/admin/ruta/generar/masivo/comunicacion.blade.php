<!DOCTYPE html>
<!--{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
{{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
-->
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::style('lib/jquery-bootstrap-validator/bootstrapValidator.min.css') }}
    
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('lib/jquery-bootstrap-validator/bootstrapValidator.min.js') }}


    {{ HTML::style('lib/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}  
    {{ HTML::script('lib/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}
    {{ HTML::script('lib/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.es.js') }}

    {{ HTML::script('lib/jquery.validate.js') }}
    <script src='lib/recaptcha/api.js'></script>

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.ruta.generar.masivo.js.comunicacion_ajax' )
    @include( 'admin.ruta.generar.masivo.js.comunicacion' )
    @include( 'admin.ruta.js.ruta_ajax' )

    @include( 'admin.ruta.js.nuevodocdigital_ajax' )
    @include( 'admin.ruta.js.nuevodocdigital' )
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

.multiselect-container{
  position: relative;
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
              <h3>COMUNICACIÓN CON ÁREAS</h3>

              <form id="FormCrearPreTramite" method="post">
                <div class="col-md-12 table-responsive" style="margin-top:10px">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title" tabindex="0">Origen</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body col-md-12">
                          <div class="col-md-12" style="padding-top: 10px">
                            <div class="col-md-3">
                              <span>ÁREA:</span>
                            </div>
                            <div class="col-md-3">
                              <select class="form-control select" name="slct_areas" id="slct_areas">
                              </select>
                            </div>
                            <div class="col-md-3 hidden">
                              <select class="form-control" name="slct_areast" id="slct_areast">
                              </select>
                            </div>
                          </div>
                          <div class="col-md-12" style="padding-top: 10px">
                            <div class="col-md-3">
                              <span>LUGAR DE ORIGEN:</span>
                            </div>
                            <div class="col-md-3">
                              <select class="form-control select" name="slct_local_origen_id" id="slct_local_origen_id">
                              </select>
                            </div>
                          </div>
                          <!--
                            <div class="col-md-3 hidden">
                              <select class="form-control" name="slct_areas_total[]" id="slct_areas_total" multiple>
                              </select>
                            </div>
                          -->
                        </div>
                    </div>
                </div>

                <div class="col-md-12 table-responsive" style="margin-top:10px">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title" tabindex="0">Destino</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body col-md-12">
                          <div class="col-md-12" style="padding-top: 10px">
                              <div class="col-md-3">
                                  <span>TIEMPO ASIGNADO(DÍAS):</span>
                              </div>
                              <div class="col-md-2">
                                  <input class="form-control" type="text" id="txt_dias" name="txt_dias" value=3 placeholder='Número de Días' maxlength="2" onkeypress="return validaNumeros(event);">
                              </div>
                          </div>
                          <div class="col-md-12" style="padding-top: 10px">
                              <div class="col-md-3">
                                  <span>N° DE ÁREAS:</span>
                              </div>
                              <div class="col-md-2">
                                  <input class="form-control" type="text" id="txt_numareas" name="txt_numareas" placeholder='Número de Áreas' onkeyup="cargarTabla()" maxlength="2" onkeypress="return validaNumeros(event);">
                                  <div class="radio">
                                      <label style="margin-left:-12px">
                                          <input class="chk form-control" type="checkbox" name="chk_todasareas" id="chk_todasareas" value="tareas"> Todas Las Áreas
                                      </label>
                                  </div>
                              </div>
                          </div>

                          <div class="col-sm-12">
                              <div>
                                  <table id="t_numareas" class="table table-bordered table-striped" style="width: 50%;">
                                      <thead>
                                          <tr class="bg-navy">
                                              <th colspan=2 style="width: 99%">Área(s) a comunicar</th>
                                              <th><label><input class="chk2 form-control" name="chk_rpta_total" value="trpta" type="checkbox" >Rpta?</label></th>
                                          </tr>
                                      </thead>
                                      <tbody id="tb_numareas"></tbody>
                                  </table>
                              </div>
                          </div>    
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

                <div class="col-md-12 table-responsive" style="margin-top:10px">
                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title" tabindex="0">Archivo(s)</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body col-md-6 text-center">
                            <table id="t_archivo_generado" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="bg-navy">
                                        <th style="width:120px;">Documento Digital</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>
                                      <input type="text" readonly="" class="form-control" id="documento_0" name="documento" value="">
                                      <input type="hidden" id="txt_documento_id" name="txt_documento_id" value="">
                                      <input type="hidden" id="txt_doc_digital_id_0" name="txt_doc_digital_id" value="">
                                      <span class="btn btn-success" onclick="RegistraridsDelBoton(0)" data-toggle="modal" data-target="#listDocDigital" id="btn_list_digital" data-texto="documento_0" data-id="txt_doc_digital_id_0"><i class="glyphicon glyphicon-file"></i></span></td>
                                  </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="box-body col-md-6 text-center">
                            <table id="t_archivos" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="bg-navy">
                                        <th style="width:120px;">Archivo(s) de Apoyo</th>
                                        <th style="width:80px;">Imagen</th>
                                        <th style="width:40px;">
                                          <span class="btn btn-sm btn-success" id="btn_agregar"><i class="fa fa-plus"></i>
                                          </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tb_archivos"></tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="col-md-12 usuario" style="padding: 2% 4% 2% 4%;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
                    <legend style="width: 8%">Asunto</legend>
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
                <div class="col-md-12 hidden" id="reporte">
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
  @include( 'admin.ruta.generar.masivo.form.referente' )
  @include( 'admin.ruta.form.ListdocDigital' )
@stop
<?php 
/*
@include( 'admin.ruta.form.crearUsuario' )
@include( 'admin.ruta.form.crearEmpresa' )
@include( 'admin.ruta.form.selectPersona' )
@include( 'admin.ruta.form.buscartramite' )
@include( 'admin.ruta.form.empresasbyuser' )
@include( 'admin.ruta.form.rutaflujo' )
@include( 'admin.ruta.form.ruta' )
*/
?>