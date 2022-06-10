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
    
    @include( 'admin.reportes.js.validasolicitudes_ajax' )
    @include( 'admin.reportes.js.validasolicitudes' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="text-align: center; font-size: 35px; color:#646464 ">
            
            REPORTE DE VALIDACIÓN DE SOLICITUDES
            
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">REPORTE DE VALIDACIÓN DE SOLICITUDES</li>
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
                            <div class="col-sm-3">
                                <label class="control-label">Fechas de estado:</label>
                                <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha_estado" name="txt_fecha_estado" value=""/>
                            </div>
                            
                            <div class="col-sm-3">
                                <label class="control-label">Estados:</label>
                                <select class="form-control" name="slct_estado[]" id="slct_estado" multiple>
                                    <option value='0'>Pendiente</option>
                                    <option value='1'>Aprobado</option>
                                    <option value='2'>Desaprobado</option>
                                </select>
                            </div>
                            
                            <div class="col-sm-3">
                                <label class="control-label">Lugar de procedencia:</label>
                                <select class="form-control" name="slct_local[]" id="slct_local" multiple></select>
                            </div>
                            <!--*******************************-->
                            <div class="col-sm-4">
                                <label class="control-label">Trámite:</label>
                                <input style="text-align: center" type="text" class="form-control" placeholder="Tipo + Nro + Año  => Ej: EX {{ rand(3000,9999) }} {{ date('Y') }}" id="txt_tramite" name="txt_tramite"/>
                            </div>

                            <div class="col-sm-5">
                                <label class="control-label">Solicitante:</label>
                                <input style="text-align: center" type="text" class="form-control" placeholder="Paterno + Materno + Nombre => No interesa el orden" id="txt_solicitante" name="txt_solicitante"/>
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
                                <input type="button" class="form-control btn alert-info" id="generar_3" name="generar_3" value="Exportar Productividad">
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
                                
                                <td style="text-align: center; border: 1px solid;">Tipo solicitante</td>
                                <td style="text-align: center; border: 1px solid;">Solicitante</td>
                                <td style="text-align: center; border: 1px solid;">Tipo de servicio solicitado</td>
                                <td style="text-align: center; border: 1px solid;">Documento presentado</td>
                                <td style="text-align: center; border: 1px solid;">Lugar de procedencia</td>
                                <td style="text-align: center; border: 1px solid;">Nombre del servicio solicitado</td>
                                <td style="text-align: center; border: 1px solid;">Fecha registrada</td>
                                <td style="text-align: center; border: 1px solid;">Requisitos PDF</td>
                                <td style="text-align: center; border: 1px solid;">Expedientes generados</td>
                                <td style="text-align: center; border: 1px solid;">Estado del servicio</td>
                                <td style="text-align: center; border: 1px solid;">Fecha del estado</td>
                                <td style="text-align: center; border: 1px solid;">Observaciones</td>
                                <td style="text-align: center; border: 1px solid;">Nro de expediente</td>
                                <td style="text-align: center; border: 1px solid;">Seleccionar</td>
                                
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
