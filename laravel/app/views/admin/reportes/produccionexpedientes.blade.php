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
    
    @include( 'admin.reportes.js.produccionexpedientes_ajax' )
    @include( 'admin.reportes.js.produccionexpedientes' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="text-align: center; font-size: 35px; color:#646464 ">
            
            REPORTE DE PRODUCCIÓN DE GESTIÓN DE EXPEDIENTES
            
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <!-- Custom Tabs -->
        <form id="form_tramiteunico" name="form_tramiteunico" method="post">
            <div class="col-xl-12">
                <fieldset>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-lg-3 col-sm-3">
                                <label class="control-label">Fechas del documento:</label>
                                <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha_documento" name="txt_fecha_documento" value=""/>
                            </div>
                            
                            <div class="col-lg-3 col-sm-3">
                                <label class="control-label">Proceso:</label>
                                <select class="form-control" name="slct_flujo[]" id="slct_flujo" multiple></select>
                            </div>
                            
                            <div class="col-lg-3 col-sm-3">
                                <label class="control-label">Lugar de procedencia:</label>
                                <select class="form-control" name="slct_local[]" id="slct_local" multiple></select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-lg-3 col-sm-3">
                                <label class="control-label">Área:</label>
                                <select class="form-control" name="slct_area[]" id="slct_area" multiple></select>
                            </div>

                            <div class="col-lg-3 col-sm-3">
                                <label class="control-label">Documento:</label>
                                <select class="form-control" name="slct_documento[]" id="slct_documento" multiple></select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <!--*******************************-->
                            <div class="col-sm-2"></div>
                            <!--*******************************-->
                            <!--BOTON MOSTRAR-->
                            <div class="col-sm-2">
                                <label class="control-label"></label>
                                <input type="button" class="form-control btn btn-primary" id="generar_1" name="generar_1" value="Mostrar">
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label"></label>
                                <input type="button" class="form-control btn btn-warning" id="generar_2" name="generar_2" value="Exportar Datos">
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label"></label>
                                <input type="button" class="form-control btn alert-info" id="generar_3" name="generar_3" value="Exportar Total Área - Total Local">
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label"></label>
                                <input type="button" class="form-control btn alert-warning" id="generar_4" name="generar_4" value="Exportar Producción por Estados">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            
            <div class="col-xl-12">
                <div class="form-group">
                    <table id="t_reportet_tab_1" class="table table-bordered" width="100%">
                        <thead>
                            
                            <tr style="background-color:#E5E5E5;">
                                
                                <td style="text-align: center; border: 1px solid;">Lugar de procedencia</td>
                                <td style="text-align: center; border: 1px solid;">Área</td>
                                <td style="text-align: center; border: 1px solid;">Documento</td>
                                <td style="text-align: center; border: 1px solid;">Proceso</td>
                                <td style="text-align: center; border: 1px solid;">Nro Documentos</td>
                                <td style="text-align: center; border: 1px solid;">Nro Trámites</td>
                                
                            </tr>
                            
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </section><!-- /.content -->

@stop
@section('formulario')
@stop
