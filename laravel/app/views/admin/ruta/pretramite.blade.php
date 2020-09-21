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

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.ruta.js.ruta_ajax' )
    @include( 'admin.ruta.js.validar_ajax' )
    @include( 'admin.ruta.js.pretramite_ajax' )
    @include( 'admin.ruta.js.pretramite' )
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
            <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Listado de Pre Tramites
            <span class="btn btn-success" id="btnnuevo" style="margin-left: 10px">Nuevo <i class="glyphicon glyphicon-plus"></i></span>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Listado de Tramites</li>
        </ol>
    </section>

        <!-- Main content -->
        <section class="content">
            <!-- Inicia contenido -->
         

            <div class="box-body">
                <div class="row form-group" id="reporte">
                    <div class="col-sm-12">
                        <div class="box-body table-responsive">
                            <table id="t_reporte" class="table table-bordered">
                                <thead>
                                    <tr>
                                      <th>Fecha:</th>
                                      <td colspan=2>
                                          <div class="input-group">
                                          <input type='text' id="filtro_fecha" class="form-control mant" value='<?php echo date("Y-m-d");?>' >
                                          <a class='btn btn-primary btn-sm input-group-addon' onClick='CargarPreTramites();'><i class="fa fa-search"></i></a>
                                          </div>
                                      </td>
                                    </tr>
                                    <tr>
                                        <th>N° DEL PRE TRÁMITE</th>
                                        <th>NOMBRE DEL SOLICITANTE</th>
                                        <th>TIPO SOLICITANTE</th>
                                        <th>TIPO TRAMITE</th>
                                        <th>DOCUMENTO PRESENTADO</th>
                                        <th>NRO DEL DOCUMENTO</th>
                                        <th>NOMBRE TRÁMITE</th>
                                        <th>FECHA REGISTRADA</th>
                                        <th>REQUISITOS EN UN SOLO ARCHIVO PDF</th>
                                        <th>ESTADO PARA MESA DE PARTES</th>
                                        <th>FECHA DEL ESTADO</th>
                                        <th>OBSERVACIONES</th>
                                        <th>NRO DE EXPEDIENTE</th>
                                        <th>AVANCE DEL TRÁMITE</th>
                                    </tr>
                                </thead>
                                <tbody id="tb_reporte">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="crearPreTramite hidden">
              <h1>Crear Pre Tramite</h1>
              <form id="FormCrearPreTramite" method="post">
                <div class="col-md-12 form-group">
                  <div class="col-md-5">
                     <div class="col-md-4" style="padding-top: 5px;">
                      <span>TIPO TRÁMITE: </span>
                    </div>
                    <div class="col-md-8">
                      <select class="form-control select" id="cbo_tipotramite" name="cbo_tipotramite">
                          <option value="-1">Selecciona</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <span class="btn btn-primary btn-sm" onclick="consultar()">BUSCAR TRÁMITE</span>
                  </div>
                </div>

                <div class="col-md-12" style="padding: 0% 4% 1% 4%;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
                    <div class="col-md-12 form-group">
                      <div class="col-md-7">
                        <div class="col-md-4">
                          <span>NOMBRE TRÁMITE: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_nombretramite" id="txt_nombretramite" class="form-control" disabled>
                          <input type="hidden" name="txt_idclasitramite" id="txt_idclasitramite" class="form-control">
                          <input type="hidden" name="txt_idarea" id="txt_idarea" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-5  form-group">
                        <div class="col-md-5">
                          <span>NÚMERO DE FOLIO: </span>
                        </div>
                        <div class="col-md-7">
                          <input type="text" name="txt_numfolio" id="txt_numfolio" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-7">
                        <div class="col-md-4" style="padding-top: 5px;">
                          <span>TIPO DOCUMENTO: </span>
                        </div>
                        <div class="col-md-8">
                          <select class="form-control select" id="cbo_tipodoc" name="cbo_tipodoc">
                              <option value="-1">Selecciona</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-5  form-group">
                        <div class="col-md-5">
                          <span>NUMERO TIPO DOCUMENTO: </span>
                        </div>
                        <div class="col-md-7">
                          <input type="text" name="txt_tipodoc" id="txt_tipodoc" class="form-control">
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12 tipoSolicitante">
                  <div class="col-md-6">
                    <div class="col-md-4" style="padding-top: 5px">
                      <span>TIPO SOLICITANTE: </span>
                    </div>
                    <div class="col-md-8">
                      <select class="form-control select" id="cbo_tiposolicitante" name="cbo_tiposolicitante">
                            <option value="-1">Selecciona</option>
                      </select>
                    </div>
                  </div>
                </div>

                 <div class="col-md-12 empresa hidden" style="padding: 2% 4% 0% 4%;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
                    <div class="col-md-12 form-group">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>RUC: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="hidden" name="txt_idempresa" id="txt_idempresa" class="form-control">
                          <input type="text" name="txt_ruc" id="txt_ruc" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>TIPO EMPRESA: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_tipoempresa" id="txt_tipoempresa" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 form-group">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>RAZON SOCIAL: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_razonsocial" id="txt_razonsocial" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>NOMBRE COMERCIAL: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_nombcomercial" id="txt_nombcomercial" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 form-group">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>DOMICILIO FISCAL: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_domiciliofiscal" id="txt_domiciliofiscal" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>TELÉFONO: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_emptelefono" id="txt_emptelefono" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 form-group">
                      <div class="col-md-3">
                        <div class="col-md-6">
                          <span>FECHA VIGENCIA: </span>
                        </div>
                        <div class="col-md-6">
                          <input type="text" name="txt_empfechav" id="txt_empfechav" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>REPRES. LEGAL: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_reprelegal" id="txt_reprelegal" class="form-control" disabled>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="col-md-5">
                          <span>REPRESE. DNI: </span>
                        </div>
                        <div class="col-md-7">
                          <input type="text" name="txt_repredni" id="txt_repredni" class="form-control" disabled>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12 usuario hidden" style="padding: 2% 4% 2% 4%;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
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
                    <div class="col-md-12 form-group">
                      <div class="col-md-4">
                        <div class="col-md-4">
                          <span>TELÉFONO: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_usertelf" id="txt_usertelf" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="col-md-4">
                          <span>CELULAR: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_usercel" id="txt_usercel" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="col-md-4">
                          <span>EMAIL: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_useremail" id="txt_useremail" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 form-group">
                      <div class="col-md-8">
                        <div class="col-md-4">
                          <span>DIRECCIÓN: </span>
                        </div>
                        <div class="col-md-8">
                          <textarea type="text" name="txt_userdirec" id="txt_userdirec" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12 usuario hidden" style="padding: 2% 4% 2% 4%;">
                  <fieldset style="max-width: 100% !important;border: 3px solid #ddd;padding: 15px;">
                    <div class="col-md-12 form-group">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>SUBIR ARCHIVO PDF: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" readonly class="form-control" id="pdf_nombre"  name="pdf_nombre" value="" readonly="">
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
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12 form-group" style="text-align: right;padding-right: 4%;">                  
                  <span class="btn btn-primary btn-sm" onclick="generarPreTramite()">GENERAR</span>
                {{--   <input type="submit" class="btn btn-primary btn-sm btnAction" id="" value="Guardar" onclick="generarPreTramite()"> --}}
                  {{-- <span class="btn btn-primary btn-sm">CANCELAR</span>              --}}   
                </div>
              </form>
            </div>

                </div><!-- /.col (RIGHT) -->
            </div>
            <!-- Finaliza contenido -->
        </div>
    </section><!-- /.content -->
@stop
@section('formulario')
  @include( 'admin.ruta.form.detallepretramite' )
  @include( 'admin.ruta.form.voucherpretramite' )
  @include( 'admin.ruta.form.buscartramite' )
  @include( 'admin.ruta.form.empresasbyuser' )
  @include( 'admin.ruta.form.rutaflujo' )
  @include( 'admin.ruta.form.ruta' )
@stop
