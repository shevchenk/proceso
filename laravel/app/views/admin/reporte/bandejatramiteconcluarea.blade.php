<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.ruta.js.ruta_ajax' )
    @include( 'admin.ruta.js.validar_ajax' )
    @include( 'admin.reporte.js.bandejatramiteconcluarea_ajax' )
    @include( 'admin.reporte.js.bandejatramiteconcluarea' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<style type="text/css">
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
            Bandeja de Concluidos por Area y Procesos
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Bandeja de Concluidos</li>
        </ol>
    </section>

        <!-- Main content -->
        <section class="content">
            <!-- Inicia contenido -->

            <div class="mailbox row">
                <div class="col-md-12">
                    <form name="form_concluido" id="form_concluido" method="POST" action="">
                    <div class="row">
                        <div class="col-sm-3 text-center">
                            <label class="control-label">Fecha final del paso</label>
                            <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha" name="txt_fecha" value=""/> <!-- {{ date('Y-m-d - Y-m-d')}} -->
                        </div>

                        <div class="col-sm-3 text-center">
                            <label class="control-label">Fecha inicio del paso</label>
                            <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha_ini" name="txt_fecha_ini" value=""/> <!--  onfocus="blur()" -->
                        </div>

                        <div class="col-sm-1" style="padding:24px; padding-left: 0px;">
                            <!-- <input type="button" class="form-control btn btn-primary" onclick="ActualizarBandeja();" value="Mostrar"> -->
                            <span class="btn btn-primary btn-md" id="generar" name="generar" onclick="ActualizarBandeja();"><i class="glyphicon glyphicon-search"></i> Mostrar</span>
                        </div>
                        <div class="col-sm-1" style="padding:24px; padding-left: 0px;">
                            <!-- <a class='btn btn-success btn-md' id="btnexport" name="btnexport" href='' target="_blank"><i class="glyphicon glyphicon-download-alt"></i> Export</i></a> -->
                            <a class='btn btn-success btn-md' id="btnexport" name="btnexport" href='#'><i class="glyphicon glyphicon-download-alt"></i> Export</i></a>
                        </div>

                    </div><!-- /.row -->
                    <div class='row'>
                        <div class="col-md-4 col-sm-4 text-center" style="">
                            <label class="control-label">Área</label>
                            <select name="slct_areas" id="slct_areas" onChange="ListarProcesosArea();" >
                            </select>
                        </div>

                        <div class="col-md-4 col-sm-4 text-center slct_procesos hidden">
                            <label class="control-label">Procesos</label>
                            <select name="slct_procesos" id="slct_procesos" ><!-- onChange="Bandeja.MostrarAjax();" -->
                            </select>
                        </div>
                    </div>
                    <div class="row pad form-group" id="reporte" >
                        <div class="col-sm-12">
                            <div class="box-body table-responsive">
                            <!-- THE MESSAGES -->
                                <table class="table table-mailbox" id="t_reporte">
                                    <thead>
                                        <tr>
                                            
                                            <th></th>
                                            <th>Tramite</th>
                                            <th>Tiempo</th>
                                            <th>Fecha Inicio</th>
                                            <th>Paso</th>
                                            <th>Fecha tramite</th>
                                            <th>Nombre</th>
                                            <th>Respuesta</th>
                                            <th>observacion</th>
                                            <th>Tipo solicitante</th>
                                            <th>Solicitante</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="tb_reporte">
                                        
                                        
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div>
                    </div>
                    </form>

                    <form name="form_ruta_detalle" id="form_ruta_detalle" method="POST" action="">
                                    <div class="row form-group" style="display:none">
                                        <div class="col-sm-12">
                                            <h1><span id="txt_titulo2">Gestionar</span>
                                            <small>
                                                <i class="fa fa-angle-double-right fa-lg"></i>
                                                <span id="texto_fecha_creacion2">:</span>
                                            </small>
                                            </h1>
                                        </div>
                                        <div class="col-md-12 col-lg-10 DatosPersonalizadosG">
                                            <div class="box box-warning">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title" tabindex="0" id="datos_personalizados">DATOS PERSONALIZADOS</h3>
                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="box-body row">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="box box-warning">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">INFORMACIÓN DE LA ACTIVIDAD</h3>

                                                    <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="box-body row" style="">
                                                    <div class="col-sm-12">
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Nro Trámite:</label>
                                                            <input type="text" class="form-control" id="txt_id_doc" readonly>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label class="control-label">Solicitante:</label>
                                                            <input type="text" class="form-control" id="txt_solicitante" readonly>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Fecha Trámite:</label>
                                                            <input type="text" class="form-control" id="txt_fecha_tramite" readonly>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label class="control-label">Sumilla:</label>
                                                            <textarea type="text" class="form-control" id="txt_sumilla" readonly></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="col-sm-4">
                                                            <label class="control-label">Proceso:</label>
                                                            <input type="text" class="form-control" id="txt_flujo" readonly>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Local:</label>
                                                            <input type="text" class="form-control" id="txt_local" readonly>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Area:</label>
                                                            <input type="text" class="form-control" id="txt_area" readonly>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Paso:</label>
                                                            <input type="text" class="form-control" id="txt_orden" readonly>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">Fecha Inicio:</label>
                                                            <input type="text" class="form-control" id="txt_fecha_inicio" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="col-sm-3">
                                                            <label class="control-label">Tiempo asignado al paso:</label>
                                                            <input type="text" class="form-control" id="txt_tiempo" readonly>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <label class="control-label">Tiempo Final:</label>
                                                            <input type="text" class="form-control" id="txt_respuesta" name="txt_respuesta" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12" style="margin-top:10px;margin-bottom: 20px">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="">
                                                        <tr>
                                                            <th class="bg-info" style="text-align:center" rowspan="2">Nro</th>
                                                            <th class="bg-info" style="text-align:center;width:60px !important;" rowspan="2">¿cond- icional?</th>
                                                            <th class="bg-info" style="text-align:center" rowspan="2">Rol que Realiza</th>
                                                            <th class="bg-info" style="text-align:center" colspan="3">Acciones a Realizar</th>
                                                            <th class="bg-danger" style="text-align:center" colspan="2">Acciones Realizadas</th>
                                                            <th class="bg-danger" style="text-align:center;width:150px !important;" rowspan="2">Persona</th>
                                                            <th class="bg-danger" style="text-align:center" rowspan="2">Fecha</th>
                                                            <th class="bg-danger" style="text-align:center" rowspan="2">[-]</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="bg-info" style="text-align:center">Verbo</th>
                                                            <th class="bg-info" style="text-align:center">Tipo Documento</th>
                                                            <th class="bg-info" style="text-align:center;width:250px !important;">Descripcion</th>
                                                            <th class="bg-danger" style="text-align:center; width: 300px;">Documento Generado</th>
                                                            <th class="bg-danger" style="text-align:center">Observaciones</th>
                                                            <!--th style="text-align:center">Adjuntar Doc. Generado</th-->
                                                        </tr>
                                                    </thead>
                                                    <tbody id="t_detalle_verbo"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-sm-12" style="display:none">
                                            <div class="col-sm-3">
                                                <label class="control-label">Tipo de respuesta del Paso:</label>
                                                <select id="slct_tipo_respuesta" name="slct_tipo_respuesta" disabled>
                                                    <option>Seleccione</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Detalle de respuesta del Paso:</label>
                                                <select id="slct_tipo_respuesta_detalle" name="slct_tipo_respuesta_detalle" disabled>
                                                    <option>Seleccione</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Descripción de respuesta del Paso:</label>
                                                <textarea class="form-control" id="txt_observacion" name="txt_observacion" rows="3" disabled></textarea>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Estado Final del Paso(Alerta):</label>
                                                <input type="hidden" class="form-control" id="txt_alerta" name="txt_alerta">
                                                <input type="hidden" class="form-control" id="txt_alerta_tipo" name="txt_alerta_tipo">
                                                <div class="progress progress-striped active">
                                                    <div id="div_cumple" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                                        <span>Cumple</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <a class="btn btn-default btn-sm btn-sm" id="btn_close">
                                                <i class="fa fa-remove fa-lg"></i>&nbsp;Close
                                            </a>
                                        </div>
                                    </div>
                                </form>

                                <form name="form_ruta_flujo" id="form_ruta_flujo" method="POST" action="">
                                    <div class="row form-group" style="display:none">
                                        <div class="col-sm-12">
                                            <h1><span id="txt_titulo">Nueva Ruta</span>
                                            <small>
                                                <i class="fa fa-angle-double-right fa-lg"></i>
                                                <span id="texto_fecha_creacion">Fecha Creación:</span>
                                                <span id="fecha_creacion"></span>
                                            </small>
                                            </h1>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="col-sm-4">
                                                <label class="control-label">Dueño del Proceso:</label>
                                                <input class="form-control" type="text" id="txt_persona" name="txt_persona" readonly>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Proceso:</label>
                                                <select class="form-control" name="slct_flujo_id" id="slct_flujo_id">
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Area del Dueño del Proceso:</label>
                                                <select class="form-control" name="slct_area_id" id="slct_area_id">
                                                </select>
                                            </div>
                                            <!--div class="col-sm-2">
                                                <label class="control-label"># Ok:</label>
                                                <input class="form-control" type="text" id="txt_ok" name="txt_ok" readonly>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label"># Error:</label>
                                                <input class="form-control" type="text" id="txt_error" name="txt_error" readonly>
                                            </div-->
                                        </div>                                        
                                    </div>
                                    <div class="row form-group" style="display:none">
                                        <div class="col-sm-12">
                                            <div class="box-body table-responsive">
                                                <table id="areasasignacion" class="table table-bordered" style="min-height:300px">
                                                    <thead> 
                                                        <tr class="head">
                                                            <th style="width:150px !important;min-width: 200px !important;" >
                                                            </th>
                                                            <th class="eliminadetalleg" style="min-width:1000px important!;">[]</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="body">
                                                            <td>
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr><th colspan="2">
                                                                        </th></tr>
                                                                        <tr class="head">
                                                                            <th>#</th>
                                                                            <th>Area</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="tb_rutaflujodetalleAreas">
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="head">
                                                            <th>#</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <a class="btn btn-default btn-sm btn-sm" id="btn_close2">
                                                <i class="fa fa-remove fa-lg"></i>&nbsp;Close
                                            </a>
                                        </div>
                                    </div>
                                </form>
                </div><!-- /.col (RIGHT) -->
            </div>
            <!-- Finaliza contenido -->
        </div>
    </section><!-- /.content -->
@stop
@section('formulario')
     @include( 'admin.reporte.form.bandejatramite' )
@stop
