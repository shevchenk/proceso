<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }} 
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}

    {{ HTML::style('css/checkbox.css') }}
    {{ HTML::script('lib/jssonido.js') }}

    {{ HTML::style('lib/jquerysctipttop.css') }}
    {{ HTML::script('lib/ckeditor/ckeditor.js') }}
    {{ HTML::style('css/admin/plantilla.css') }}
{{--     {{ HTML::script('lib/bootstrap-treetable/js/javascript.js') }} --}}
     


    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.ruta.js.ruta_ajax' )
    @include( 'admin.ruta.js.validar_ajax' )

    
    @include( 'admin.reporte.js.bandejatramitedig_ajax' )
    @include( 'admin.reporte.js.bandejatramitedig' )

    @include( 'admin.ruta.js.nuevodocdigital_ajax' )
    @include( 'admin.ruta.js.nuevodocdigital' )

    @include( 'admin.ruta.js.indedocs' )
    @include( 'admin.ruta.js.indedocs_ajax' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<style type="text/css">
    /*
    Component: Mailbox
*/

    .treegrid-indent {
        width: 0px;
        height: 16px;
        display: inline-block;
        position: relative;
    }

    .treegrid-expander {
        width: 0px;
        height: 16px;
        display: inline-block;
        position: relative;
        left:-17px;
        cursor: pointer;
    }

.mailbox .table-mailbox {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}
.mailbox .table-mailbox tr.unread > td {
  background-color: rgba(0, 0, 0, 0.05);
  color: #008D4C;
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

td.details-control {
    background: url('lib/web/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('lib/web/details_close.png') no-repeat center center;
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
table>thead>tr>td,table>tfoot>tr>td{
    font-size: 12.5px;
}
table>tbody>tr>td{
    color: #00A65A;
}
</style>
            <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Bandeja de Activades Pendientes
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Bandeja de Inconclusos y Gestión</li>
        </ol>
    </section>

        <!-- Main content -->
        <section class="content">
            <!-- Inicia contenido -->
            <div id="audiobi"></div>
            <!--
            <audio autoplay>
                <source src="http://localhost/ingind/public/sonido/alarma2.mp3" type="audio/mp3">
                Tu navegador no soporta HTML5 audio.
            </audio>
            -->

            <div class="mailbox row">
                <div class="col-md-12">
                    <div class="row form-group" id="reporte" >
                        <div class="col-sm-12">
                            <div class="box-body table-responsive">
                            <!-- THE MESSAGES -->
                            <form name="form_filtros" id="form_filtros" method="POST" action="">
                                <table id="t_reporte_ajax" class="table table-mailbox">
                                    <thead>
                                        <tr>
                                            <td>#</td>
                                            <td id="th_dg" style='width:250px !important;' class="unread">Documento generado por el paso anterior<br>
                                            <input style='width:250px' name="txt_id_ant" id="txt_id_ant" onBlur="MostrarAjax();" onKeyPress="return enterGlobal(event,'th_dg',1)" onkeyup="Limpiar('txt_id_union,#txt_solicitante,#txt_proceso');" type="text" placeholder="" />
                                            </td>
                                            <td id="th_pd" style='width:250px !important;' class="unread">Primer documento ingresado<br>
                                            <input style='width:250px' name="txt_id_union" id="txt_id_union" onBlur="MostrarAjax();" onKeyPress="return enterGlobal(event,'th_pd',1)" onkeyup="Limpiar('txt_id_ant,#txt_solicitante,#txt_proceso');" type="text" placeholder="" />
                                            </td>
                                            <td>Tiempo</td>
                                            <td id="th_fi" style='width:250px !important;' class="unread">Fecha de Inicio<br>
                                            <input style='width:250px' name="txt_fecha_inicio_b" id="txt_fecha_inicio_b" onChange="MostrarAjax();" type="text" />
                                            </td>
                                            <td id="th_ep" style='width:250px !important;' class="unread">Estado de la Actividad<br>
                                            <select name="slct_tiempo_final" id="slct_tiempo_final" onChange="MostrarAjax();" />
                                            <option value="">.::Todo::.</option>
                                            <option value="1">Dentro del Tiempo</option>
                                            <option value="0">Fuera del Tiempo</option>
                                            </select>
                                            </td>
                                            <td>Paso</td>
                                            <td id="th_pr" style='width:250px !important;' class="unread">Proceso<br>
                                            <input style='width:250px' name="txt_proceso" id="txt_proceso" onBlur="MostrarAjax();" onKeyPress="return enterGlobal(event,'th_pr',1)" onkeyup="Limpiar('txt_id_ant,#txt_id_union,#txt_solicitante');" type="text" placeholder="" />
                                            </td>
                                            <td id="th_so" style='width:250px !important;' class="unread">Solicitante<br>
                                            <input style='width:250px' name="txt_solicitante" id="txt_solicitante" onBlur="MostrarAjax();" onKeyPress="return enterGlobal(event,'th_so',1)" onkeyup="Limpiar('txt_id_ant,#txt_id_union,#txt_proceso');" type="text" placeholder="" />
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>#</td>
                                            <td>Doc. Generado por el paso anterior</td>
                                            <td>Primer documento ingreso</td>
                                            <td>Tiempo</td>
                                            <td>Fecha de Inicio</td>
                                            <td>Estado de la Actividad</td>
                                            <td>Paso</td>
                                            <td>Proceso</td>
                                            <td>Solicitante</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                            </div><!-- /.table-responsive -->
                        </div>
                    </div>

                    <form name="form_ruta_detalle" id="form_ruta_detalle" method="POST" action="">
                                    <div id="bandeja_detalle" class="row form-group" style="display:none">
                                        <div class="col-sm-12">
                                            <h1><span id="txt_titulo2">Gestionar</span>
                                            <small>
                                                <i class="fa fa-angle-double-right fa-lg"></i>
                                                <span id="texto_fecha_creacion2">:</span>
                                            </small>
                                            <a class="btn btn-sm btn-warning" id="VisualizarR" onclick="mostrarRuta(this)">
                                                <i class="glyphicon glyphicon-search"></i>
                                                Visualizar Ruta
                                            </a>
                                            <a class="btn btn-sm btn-primary" id="ExpedienteU">
                                                <i class="fa fa-search fa-lg"></i>
                                                .::Expediente::.
                                            </a>
                                            <a class="btn btn-sm btn-primary" id="RetornarP" onclick="retornar()">
                                                <i class="glyphicon glyphicon-repeat"></i>
                                                Retornar Paso
                                            </a>
                                            </h1>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="col-sm-4">
                                                <label class="control-label">Nro Trámite:</label>
                                                <input type="text" class="form-control" id="txt_id_doc" readonly>
                                            </div>
                                            <!-- 
                                            <div class="col-sm-4">
                                                <label class="control-label">Solicitante:</label>
                                                <input type="text" class="form-control" id="txt_solicitante" readonly>
                                            </div>
                                            -->
                                            <div class="col-sm-4">
                                                <label class="control-label">Area:</label>
                                                <input type="text" class="form-control" id="txt_area" readonly>
                                            </div>
                                            
                                            <div class="col-sm-4">
                                                <label class="control-label">Proceso:</label>
                                                <input type="text" class="form-control" id="txt_flujo" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            </br>
                                        </div>
                                        <div class="col-sm-12">                                            
                                            <!-- 
                                            <div class="col-sm-4">
                                                <label class="control-label">Area:</label>
                                                <input type="text" class="form-control" id="txt_area" readonly>
                                            </div>
                                            -->
                                            <div class="col-sm-1">
                                                <label class="control-label">Paso:</label>
                                                <input type="text" class="form-control" id="txt_orden" readonly>
                                            </div>
                                            <div class="col-sm-1">
                                                <label class="control-label">Tiempo:</label>
                                                <input type="text" class="form-control" id="txt_tiempo" readonly>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Fecha Inicio:</label>
                                                <input type="text" class="form-control" id="txt_fecha_inicio" readonly>
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <label class="control-label">Tiempo Final:</label>
                                                <input type="text" class="form-control" id="txt_respuesta" name="txt_respuesta" readonly>
                                            </div>

                                            <div class="col-sm-2">
                                                <label class="control-label">Responsable de la Actividadd:</label>
                                                <?php
                                                    if( Auth::user()->rol_id==8 OR Auth::user()->rol_id==9 ){
                                                ?>
                                                        <select id="slct_persona" data-id="0" onChange="ActualizarResponsable(this.value)"></select>
                                                <?php
                                                    }
                                                    else{
                                                ?>
                                                        <div id="slct_persona"></div>
                                                <?php
                                                    }
                                                ?>
                                                
                                            </div>

                                            <div class="col-sm-4">
                                                <label class="control-label">Sumilla:</label>
                                                <textarea type="text" class="form-control" id="txt_sumilla" readonly></textarea>
                                            </div>

                                        </div>
                                        <div class="col-sm-12">
                                            </br>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="sectionmicro">
                                                <div class="col-sm-3 text-right">
                                                    <label class="control-label">Sub Procesos:</label>
                                                </div>    
                                                <div class="col-sm-6">
                                                    
                                                    <select id="slct_micro" name="slct_micro">
                                                        <option>Seleccione</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label class="control-label">&nbsp;&nbsp;&nbsp;</label>
                                                    <a class="btn btn-success btn-sm"  id="btn_siguiente_rd" style="display: none;">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="col-sm-12" style="margin-top:10px;margin-bottom: 20px">
                                            
                                            <div class="table-responsive">
                                                <div class="col-md-12 foto_desmonte" id="div_fotos_desmonte" style="padding-bottom: 5px; margin-bottom: 5px; border: 3px solid #3c8dbc36; padding-top: 5px; padding-left: 0px; padding-right: 0px; background-color: #fff;">
                                                    <style>
                                                        .foto_desmonte {
                                                            overflow:hidden;
                                                            border: 2px solid #3c8dbc40;
                                                            background:#fefefe;
                                                            -moz-border-radius:5px;
                                                            -webkit-border-radius:5px;
                                                            border-radius: 10px;
                                                            -moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
                                                            -webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
                                                        }
                                                    </style>
                                                    <div id="d_ver_fotos" class="col-md-10"></div>
                                                    <div id="d_subir_fotos" class="col-md-2 valido">
                                                        <input type="hidden" id="txt_archivo_desmonte" name="txt_archivo_desmonte[]">
                                                        <div class="validoarchivo" style="">
                                                            
                                                                <table id="t_darchivo" class="table table-bordered" style="margin-bottom: 0px !important;">
                                                                    <thead class="bg-aqua disabled color-palette" style="background-color: #3c8dbc !important;">
                                                                        <tr>
                                                                            <th style="width: 80%;">Subir Archivo</th>
                                                                            <th>
                                                                                <a class="btn btn-default btn-xs" onclick="AgregarD(this)" style="font-size: 10px;"><i class="fa fa-plus fa-lg"></i></a>
                                                                            </th> 
                                                                        </tr> 
                                                                    </thead> 
                                                                    <tbody id="tb_darchivo"> 
                                                                        <tr style="display: none">
                                                                            <td><input type="hidden" value="0"></td>
                                                                            <td><input type="hidden" value="0"></td>
                                                                        </tr>
                                                                    </tbody>
                                                                    <tfoot id="tb_darchivo"> 
                                                                        <tr>
                                                                            <td colspan="3" class="text-center"><button type="button" onclick="guardarArhivoDesmonte()" id="btn_guardarfoto" name="btn_guardarfoto" class="btn btn-info">Guardar Archivo</button></td>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                           
                                                        </div>
                                                    </div>
                                                </div>

                                                <table class="table table-bordered" id="tbldetalleverbo">
                                                <thead>
                                                    <tr>
                                                        <td style="text-align:center" rowspan="2">Nro</td>
                                                        <td style="text-align:center;width:60px !important;" rowspan="2">¿cond- icional?</td>
                                                        <td style="text-align:center" rowspan="2">Rol que Realiza</td>
                                                        <td style="text-align:center" colspan="3">Acciones a Realizar</td>
                                                        <td style="text-align:center" colspan="2">Acciones Realizadas</td>
                                                        <td style="text-align:center;width:150px !important;" rowspan="2">Persona</td>
                                                        <td style="text-align:center" rowspan="2">Fecha</td>
                                                        <td style="text-align:center" rowspan="2">[-]</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align:center">Verbo</td>
                                                        <td style="text-align:center">Tipo Documento</td>
                                                        <td style="text-align:center;width:250px !important;">Descripcion</td>
                                                        <td style="text-align:center; width: 250px;">Documento Generado</td>
                                                        <td style="text-align:center">Observaciones</td>
                                                        <!--th style="text-align:center">Adjuntar Doc. Generado</th-->
                                                    </tr>
                                                </thead>
                                                <tbody id="t_detalle_verbo">
                                                   
                                                </tbody>
                                                <tfoot>
                                                     <tr class="trNuevo hidden">
                                                        <td id="tdNro" style="vertical-align : middle;">0</td>
                                                        <td id="tdCondicional" style="vertical-align : middle;">NO</td>
                                                        <td id="tdRol" style="vertical-align : middle;">
                                                            <select class="form-control cboRoles" id="cboRoles" name="cboRoles"></select>
                                                        </td>
                                                        <td id="tdVerbo" style="vertical-align : middle;">
                                                            Generar
                                                            {{-- <input type="text" name="txtverbo" id="txtverbo" class="form-control" placeholder=""> --}}
                                                        </td>
                                                        <td id="tdTipoDoc" style="vertical-align : middle;">
                                                            {{-- <input type="text" name="txttipoDoc" id="txttipoDoc" class="form-control" placeholder=""> --}}
                                                            <select class="form-control cbotipoDoc" id="cbotipoDoc" name="cbotipoDoc"></select>
                                                        </td>
                                                        <td id="tdDescripcion" style="vertical-align : middle;">
                                                            <input type="text" name="txtdescripcion" id="txtdescripcion" class="form-control txtdescripcion" placeholder="">
                                                        </td>
                                                        <td id="tdDocumento" style="vertical-align : middle;">
                                                            <input type="text" name="txtdocumento" id="txtdocumento" class="form-control txtdocumento" placeholder="" disabled>
                                                        </td>
                                                        <td id="tdObservaciones" style="vertical-align : middle;">
                                                            <textarea class="form-control" id="txtobservacion" name="txtobservacion" disabled></textarea>
                                                            {{-- <input type="text" name="txtobservacion" id="txtobservacion" class="form-control" placeholder=""> --}}
                                                        </td>
                                                        <td id="tdPersona" style="vertical-align : middle;" disabled>
                                                            <select class="form-control" id="cboPersona" name="cboPersona" disabled></select>
                                                        </td>
                                                        <td id="tdFecha" style="vertical-align : middle;" disabled>
                                                          {{--   <input type="text" name="txtfecha" id="txtfecha" class="form-control" placeholder=""> --}}
                                                        </td>
                                                        <td id="tdCheck" style="vertical-align : middle;">
                                                            <div style="display:flex;">
                                                                <span id="btnSave" name="btnSave" class="btn btn-success btn-sm" style="margin-right: 5px;" onclick="saveVerbo()"><i class="glyphicon glyphicon-ok"></i></span>  
                                                                <span id="btnDelete" name="btnDelete" class="btn btn-danger  btn-sm btnDelete" onclick="Deletetr(this)"><i class="glyphicon glyphicon-remove"></i></span>
                                                            </div>
                                                  
                                                            {{-- <input type="checkbox" name="chkValida" id="chkValida" value=""> --}}
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            </div>
                                            <button id="btnAdd" class="btn btn-yellow" style="width: 100%;margin-top:-20px" type="button" onclick="Addtr(event)"><span class="glyphicon glyphicon-plus"></span> AGREGAR </button>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="col-sm-3">
                                                <label class="control-label">Tipo de respuesta de la Actividad:</label>
                                                <select id="slct_tipo_respuesta" name="slct_tipo_respuesta">
                                                    <option>Seleccione</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Detalle de respuesta de la Actividad:</label>
                                                <select id="slct_tipo_respuesta_detalle" name="slct_tipo_respuesta_detalle">
                                                    <option>Seleccione</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Descripción de respuesta de la Actividad:</label>
                                                <textarea class="form-control" id="txt_observacion" name="txt_observacion" rows="3"></textarea>
                                            </div>
                                            <div class="col-sm-3 sectionarchivado">
                                                <label class="control-label">Archivar Trámite:</label>
                                                <select id="slct_archivado" name="slct_archivado">
                                                    <option value="0">No</option>
                                                    <option value="2">Si</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Estado Final de la Actividad(Alerta):</label>
                                                <input type="hidden" class="form-control" id="txt_finalizado" name="txt_finalizado" value="0">
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
                                            <div class="col-sm-6 text-left">
                                                <a class="btn btn-primary btn-sm" id="btn_guardar_todo">
                                                    <i class="fa fa-save fa-lg"></i>&nbsp;Guardar
                                                </a>
                                            </div>  
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
                                            <div class="col-sm-4">
                                                <label class="control-label">Proceso:</label>
                                                <input class="form-control" type="text" id="txt_proceso_1" name="txt_proceso_1" readonly>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="control-label">Area del Dueño del Proceso:</label>
                                                <input class="form-control" type="text" id="txt_area_1" name="txt_area_1" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group" style="display:none" >
                                        <div class="col-sm-12">
                                            <div class="box-body table-responsive">
                                                <table id="areasasignacion" class="table table-bordered" style="min-height:300px">
                                                    <thead> 
                                                        <tr class="head">
                                                            <th style="width:250px !important;min-width: 200px !important;" >
                                                            </th>
                                                            <th class="eliminadetalleg" style="min-width:1000px !important;">[]</th>
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
                                            <a class="btn btn-default btn-sm btn-sm" id="btn_close">
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
    @include( 'admin.reporte.form.expediente' )
    @include( 'admin.ruta.form.indedocs' )
    @include( 'admin.ruta.form.ruta' )
    @include( 'admin.mantenimiento.form.docdigital' )
    @include( 'admin.ruta.form.ListdocDigital' )
@stop
