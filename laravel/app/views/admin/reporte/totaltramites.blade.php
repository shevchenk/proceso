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
    @include( 'admin.reporte.js.totaltramites_ajax' )
    @include( 'admin.reporte.js.totaltramites' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Vista de Trámites
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Vista de trámites</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs logo modal-header">
                <li class="logo tab_1 active">
                    <a href="#tab_1" data-toggle="tab">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-cloud fa-lg"></i> </button>
                        Por Trámite
                    </a>
                </li>
                <li class="logo tab_2">
                    <a href="#tab_2" data-toggle="tab">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-cloud fa-lg"></i> </button>
                        Trámites por Fechas
                    </a>
                </li>
                <li class="logo tab_3">
                    <a href="#tab_3" data-toggle="tab">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-edit fa-lg"></i> </button>
                        Trámites por Fechas y (Area o Proceso)
                    </a>
                </li>
                <li class="logo tab_4">
                    <a href="#tab_4" data-toggle="tab">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-edit fa-lg"></i> </button>
                        Nro de Trámites pendientes por Área
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1" onclick="ActPest(1);">
                    <form id="form_1" name="form_1" method="post">
                        <div class="col-xl-12">
                            <fieldset>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <label class="control-label">Trámite:</label>
                                            <input type="text" class="form-control" placeholder="Tipo + Nro + Año  => Ej: EX {{ rand(3000,9999) }} {{ date("Y") }}" id="txt_tramite_1" name="txt_tramite_1"/>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label"></label>
                                            <input type="button" class="form-control btn btn-primary" id="generar_1" name="generar_1" value="Mostrar">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <hr>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <table id="t_reportet_tab_1" class="table table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Tramite</th>
                                            <th>Tipo Sol.</th>
                                            <th>Solicitante</th>
                                            <th>Sumilla</th>
                                            <th>Estado</th>
                                            <th>Paso</th> <!-- Paso a la fecha -->
                                            <th>Area</th>
                                            <!-- <th>Total de pasos</th> -->
                                            <th>Fecha Inicio</th>
                                            <th>Pasos Sin alertas</th>
                                            <th>Pasos Con alertas</th>
                                            <th>Pasos Alertas validadas</th>
                                            <th> [ ] </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="col-xl-12">
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
                            <div class="form-group">
                                <table id="t_reported_tab_1" class="table table-bordered" width="100%">
                                    <thead>
                                        <tr>
                                            <th colspan="7" style='background-color:#DEFAFA; width: 30% !important;'>Datos del paso</th>
                                            <th style='background-color:#F5DF9D; width: 35% !important;'>Acciones a realizar</th>
                                            <th style='background-color:#FCD790; width: 35% !important;'>Acciones realizadas</th>
                                        </tr>
                                        <tr>
                                            <th style='background-color:#DEFAFA'>N°</th>
                                            <th style='background-color:#DEFAFA'>Área</th>
                                            <th style='background-color:#DEFAFA'>Tiempo</th>
                                            <th style='background-color:#DEFAFA'>Inicio</th>
                                            <th style='background-color:#DEFAFA'>Final</th>
                                            <th style='background-color:#DEFAFA'>Estado final</th>
                                            <th style='background-color:#DEFAFA'>Archivo Desmonte</th>

                                            <th style='background-color:#F5DF9D'>Rol "tiene que"
                                            Accion
                                            Tipo Doc.
                                            (Descripcion)
                                            </th>

                                            <th style='background-color:#FCD790'>Estado
                                            (N° Doc.
                                            Descripcion)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="tab_2" onclick="ActPest(2);">
                    <form id="form_2" name="form_2" method="post">
                        <div class="box">
                            <fieldset>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <label class="control-label">Fecha(s) de Inicio(s) del(os) Trámite(s)::</label>
                                            <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha_2" name="txt_fecha_2" onfocus="blur()"/>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label"></label>
                                            <input type="button" class="form-control btn btn-primary" id="generar_2" name="generar_2" value="Mostrar">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <hr>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <table id="t_reportet_tab_2" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tramite</th>
                                            <th>Tipo Sol.</th>
                                            <th>Solicitante</th>
                                            <th>Sumilla</th>
                                            <th>Estado</th>
                                            <th>Paso a la fecha</th>
                                            <th>Total de pasos</th>
                                            <th>Fecha Inicio</th>
                                            <th>Pasos Sin alertas</th>
                                            <th>Pasos Con alertas</th>
                                            <th>Pasos Alertas validadas</th>
                                            <th> [ ] </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <table id="t_reported_tab_2" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="6" style='background-color:#DEFAFA'>Datos del paso</th>
                                            <th style='background-color:#F5DF9D'>Acciones a realizar</th>
                                            <th style='background-color:#FCD790'>Acciones realizadas</th>
                                        </tr>
                                        <tr>
                                            <th style='background-color:#DEFAFA'>Paso</th>
                                            <th style='background-color:#DEFAFA'>Área</th>
                                            <th style='background-color:#DEFAFA'>Tiempo</th>
                                            <th style='background-color:#DEFAFA'>Inicio</th>
                                            <th style='background-color:#DEFAFA'>Final</th>
                                            <th style='background-color:#DEFAFA'>Estado final</th>

                                            <th style='background-color:#F5DF9D'>Rol "tiene que"
                                            Accion
                                            Tipo Doc.
                                            (Descripcion)
                                            </th>

                                            <th style='background-color:#FCD790'>Estado
                                            (N° Doc.
                                            Descripcion)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="tab_3" onclick="ActPest(3);">
                    <form id="form_3" name="form_3" method="post">
                        <div class="box">
                            <fieldset>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <label class="control-label">Fecha(s) de Inicio(s) del(os) Trámite(s):</label>
                                            <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha_3" name="txt_fecha_3" onfocus="blur()"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-3">
                                            <label class="control-label">Área:</label>
                                            <select id="slct_area_3" name="slct_area_3[]" class="form-control" multiple></select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label">Categoria:</label>
                                            <select id="slct_categoria_3" name="slct_categoria_3[]" class="form-control" multiple></select>
                                        </div>
                                        <div class="col-sm-5">
                                            <label class="control-label">Proceso:</label>
                                            <select id="slct_proceso_3" name="slct_proceso_3[]" class="form-control" multiple></select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label"></label>
                                            <input type="button" class="form-control btn btn-primary" id="generar_3" name="generar_3" value="Mostrar">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <hr>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <table id="t_reportep_tab_3" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nombre del proceso</th>
                                            <th>Dueño del Proceso</th>
                                            <th>Área del dueño</th>
                                            <th>N° de Áreas de la ruta</th>
                                            <th>N° de Pasos de la ruta</th>
                                            <th>Tiempo total de la ruta</th>
                                            <th>Fecha Creación</th>
                                            <th>Fecha Producción</th>
                                            <th>N° Trámites</th>
                                            <th>Inconclusos</th>
                                            <th>Conclusos</th>
                                            <th> [ ] </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <table id="t_reportet_tab_3" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tramite</th>
                                            <th>Tipo Sol.</th>
                                            <th>Solicitante</th>
                                            <th>Sumilla</th>
                                            <th>Estado</th>
                                            <th>Paso a la fecha</th>
                                            <th>Total de pasos</th>
                                            <th>Fecha Inicio</th>
                                            <th>Pasos Sin alertas</th>
                                            <th>Pasos Con alertas</th>
                                            <th>Pasos Alertas validadas</th>
                                            <th> [ ] </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <hr>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <table id="t_reported_tab_3" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th colspan="6" style='background-color:#DEFAFA'>Datos del paso</th>
                                            <th style='background-color:#F5DF9D'>Acciones a realizar</th>
                                            <th style='background-color:#FCD790'>Acciones realizadas</th>
                                            <th style='background-color:#FCD790'>&nbsp;</th>
                                        </tr>
                                        <tr>
                                            <th style='background-color:#DEFAFA'>Paso</th>
                                            <th style='background-color:#DEFAFA'>Área</th>
                                            <th style='background-color:#DEFAFA'>Tiempo</th>
                                            <th style='background-color:#DEFAFA'>Inicio</th>
                                            <th style='background-color:#DEFAFA'>Final</th>
                                            <th style='background-color:#DEFAFA'>Estado final</th>

                                            <th style='background-color:#F5DF9D'>Rol "tiene que"
                                            Accion
                                            Tipo Doc.
                                            (Descripcion)
                                            </th>

                                            <th style='background-color:#FCD790'>Estado
                                            (N° Doc.
                                            Descripcion)
                                            </th>
                                            <th style='background-color:#FCD790'>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane" id="tab_4" onclick="ActPest(4);">
                    <form id="form_4" name="form_4" method="post">
                        <div class="box">
                            <fieldset>
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            <label class="control-label">Seleccione el(las) Área(s) Involucrada(s) en el(los) Proceso(s):</label>
                                            <select id="slct_area_4" name="slct_area_4[]" class="form-control" multiple></select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label"></label>
                                            <input type="button" class="form-control btn btn-primary" id="generar_4" name="generar_4" value="Mostrar">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <label class="control-label">¿Desea mostrar los procesos de las áreas dueñas?</label>
                                            <select id="slct_sino" name="slct_sino" class="form-control">
                                                <option value="0" selected>No</option>
                                                <option value="1">Si</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="control-label">Fecha(s) de Inicio(s) del(os) Paso(s) del(os) Trámite(s):</label>
                                            <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha_4" name="txt_fecha_4"/>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <hr>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <table id="t_reportetp_tab_4" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>&nbsp;<th>
                                        </tr>
                                        <tr>
                                            <th>Área(s)<br>Involucrada(s)<br>en el(los) Proceso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.tab-content -->
        </div><!-- nav-tabs-custom -->

        <form name="form_ruta_flujo" id="form_ruta_flujo" style="display:none" method="POST" action="">
            <div class="row form-group">
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
                        <input class="form-control" type="text" id="txt_proceso" name="txt_proceso" readonly>
                    </div>
                    <div class="col-sm-4">
                        <label class="control-label">Area del Dueño del Proceso:</label>
                        <input class="form-control" type="text" id="txt_area" name="txt_area" readonly>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-12">
                    <div class="box-body">
                        <table id="areasasignacion" class="table table-bordered" style="min-height:300px">
                            <thead> 
                                <tr class="head">
                                    <th style="width:250px !important;min-width: 200px !important;" >
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
                    <a class="btn btn-default btn-sm btn-sm" id="btn_close">
                        <i class="fa fa-remove fa-lg"></i>&nbsp;Close
                    </a>
                </div>
            </div>
        </form>
    </section><!-- /.content -->

@stop
@section('formulario')
     @include( 'admin.ruta.form.ruta' )
@stop
