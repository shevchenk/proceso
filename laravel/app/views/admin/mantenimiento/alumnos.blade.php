<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.mantenimiento.js.alumnos' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
    <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Carga masiva de alumnos
                <small> </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                <li><a href="#">Mantenimientos</a></li>
                <li class="active">Carga masiva de alumnos</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- Inicia contenido -->
                    <div class="col-lg-6 col-md-12">
                        <form id="AlumnoCargaForm" autocomplete="off">
                        <div class="panel panel-info panel-line">
                            <div class="panel-heading">
                            <h3 class="panel-title">Descargar Plantilla</h3>
                            <div class="panel-actions">
                                <a class="btn btn-warning btn-round btn-outline" id="btn_Descargar" data-toggle="tooltip" data-placement="top" title="DESCARGAR PLANTILLA">
                                    <i class="glyphicon glyphicon-download"></i>
                                </a>
                            </div>
                            </div>
                            <div class="panel-body row">
                            <div class="col-lg-6">
                                <label>Buscar y seleccionar archivo:</label>
                                <div class="input-group">
                                    <textarea readOnly class="form-control" rows="2" id="txt_alumno"  name="txt_alumno"></textarea>
                                    <input type="text" style="display: none;" id="txt_alumno_archivo" name="txt_alumno_archivo">
                                    <label class="btn btn-dark btn-round btn-outline input-group-addon ocultar" data-toggle="tooltip" data-placement="top" title="" data-original-title="BUSCAR ARCHIVO">
                                        <i class="glyphicon glyphicon-cloud-upload"></i>
                                        <input type="file" style="display: none;" onchange="masterG.onImagen(event,'#txt_alumno','#txt_alumno_archivo','#img_alumno');" >
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <a id="img_href">
                                    <img id="img_alumno" src="Config/default.png" style="height: 100px;width: 200px;border-radius: 8px;border: 1px solid grey;margin-top: 5px;padding: 8px">
                                </a>
                            </div>
                            </div>
                            <div class="panel-footer">
                            <div class="text-right">
                                <button type="button" id="btn_Procesar" class="btn btn-primary btn-round btn-outline">Procesar Archivo </button>
                            </div>
                            </div>
                        </div>
                        </form>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <form id="AlumnoCargaForm" autocomplete="off">
                        <div class="panel panel-info panel-line">
                            <div class="panel-heading">
                                <h3 class="panel-title">Resultados con errores</h3>
                            </div>
                            <div class="panel-body row">
                                <table id="tableResultado" class="table table-bordered table-warning">
                                    <thead>
                                        <tr>
                                            <th>Fila</th>
                                            <th>Paterno</th>
                                            <th>Materno</th>
                                            <th>Nombre</th>
                                            <th>DNI</th>
                                            <th>Campo Error</th>
                                            <th>Mensaje Error</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>                            
                            </div>
                        </div>
                        </form>
                    </div>
                    <!-- Finaliza contenido -->
                </div>
            </div>

        </section><!-- /.content -->
@stop

@section('formulario')
@stop