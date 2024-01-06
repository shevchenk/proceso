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
    @include( 'admin.ruta.js.ruta' )
    
    @include( 'admin.reportes.js.expediente_ajax' )
    @include( 'admin.reportes.js.expediente' )
    
    @include( 'admin.reportes.js.estados_ajax' )
    @include( 'admin.reportes.js.estados' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="text-align: center; font-size: 35px; color:#646464 ">
            
            REPORTE DE SOLICITUDES DE USUARIOS
            
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">REPORTE DE SOLICITUDES DE USUARIOS</li>
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
                                <label class="control-label">Fechas de creación del trámite:</label>
                                <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="txt_fecha_inicio_anu" name="txt_fecha_inicio_anu" value=""/>
                            </div>

                            <div class="col-sm-3">
                                <label class="control-label">Lugar de procedencia:</label>
                                <select class="form-control" name="slct_local_anu[]" id="slct_local_anu" multiple></select>
                            </div>

                            <div class="col-sm-3">
                                <label class="control-label">Estados:</label>
                                <select class="form-control" name="slct_estado_anu" id="slct_estado_anu">
                                    <option value=''>Todos</option>
                                    <option value='Concluido'>Concluido</option>
                                    <option value='Inconcluso'>Inconcluso</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <!--*******************************-->
                            <div class="col-sm-3">
                                <label class="control-label">Trámite:</label>
                                <input style="text-align: center" type="text" class="form-control" placeholder="Tipo + Nro + Año  => Ej: EX {{ rand(3000,9999) }} {{ date('Y') }}" id="txt_tramite" name="txt_tramite"/>
                            </div>

                            <div class="col-sm-3">
                                <label class="control-label">Solicitante:</label>
                                <input style="text-align: center" type="text" class="form-control" placeholder="Paterno + Materno + Nombre => No interesa el orden" id="txt_solicitante_anu" name="txt_solicitante_anu"/>
                            </div>

                            <div class="col-sm-3">
                                <label class="control-label">Sumilla:</label>
                                <input style="text-align: center" type="text" class="form-control" placeholder="Sumilla" id="txt_sumilla" name="txt_sumilla"/>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <!--*******************************-->
                            <div class="col-sm-5"></div>
                            <!--*******************************-->
                            <!--BOTON MOSTRAR-->
                            <div class="col-sm-2">
                                <label class="control-label"></label>
                                <input type="button" class="form-control btn btn-primary" id="generar_1" name="generar_1" value="Mostrar">
                            </div>
                            <!--*******************************-->
                            <div class="col-sm-5"></div>
                            <!--*******************************-->
                        </div>
                    </div>
                </fieldset>
            </div>
            
            <div class="col-xl-12">
                <div class="form-group">
                    <table id="t_reportet_tab_1" class="table table-bordered" width="100%">
                        <thead>
                            
                            <tr style="background-color:#E5E5E5;">
                                
                                <td style="width:10%; text-align: center; border: 1px solid;">Documento de Inicio</td>
                                <td style="width:10%; text-align: center; border: 1px solid;">Documento Generado</td>
                                <td style="width:10%; text-align: center; border: 1px solid;">Lugar de procedencia</td>
                                <td style="width:20%; text-align: center; border: 1px solid;">Proceso</td>
                                <td style="width:15%; text-align: center; border: 1px solid;">Solicitante</td>
                                <td style="width:15%; text-align: center; border: 1px solid;">Sumilla</td>
                                <td style="width:5%; text-align: center; border: 1px solid;">Estado</td>
                                <td style="width:10%; text-align: center; border: 1px solid;">Fecha de Inicio</td>
                                <td style="width:5%; text-align: center; border: 1px solid;"> [ ] </td>
                                
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
                    <table id="t_reported_tab_1" class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <!-- COLSPAN, es como combinar celdas en excel. pones la cantidad de filas que se agrupara como titulo -->
                                <td colspan="6" style='background-color:#DEFAFA; width: 30% !important;text-align: center; border: 1px solid;'>Datos del paso</td>
                                <td style='background-color:#F5DF9D; width: 35% !important;text-align: center; border: 1px solid;'>Acciones a realizar</td>
                                <td colspan="4" style='background-color:#FCD790; width: 35% !important;text-align: center; border: 1px solid;'>Acciones realizadas</td>
                            </tr>
                            <tr>
                                <!-- DATOS DEL PASO -->
                                <td style='background-color:#DEFAFA;text-align: center; border: 1px solid;'>N°</td>
                                <td style='background-color:#DEFAFA;text-align: center; border: 1px solid;'>Área</td>
                                <td style='background-color:#DEFAFA;text-align: center; border: 1px solid;'>Tiempo</td>
                                <td style='background-color:#DEFAFA;text-align: center; border: 1px solid;'>Inicio</td>
                                <td style='background-color:#DEFAFA;text-align: center; border: 1px solid;'>Final</td>
                                <td style='background-color:#DEFAFA;text-align: center; border: 1px solid;'>Estado final</td>
                                <!--**********************************************-->
                                <!-- ACCIONES A REALIZAR -->
                                <td style='background-color:#F5DF9D;text-align: center; border: 1px solid;'>Rol "tiene que"
                                Accion
                                Tipo Doc.
                                (Descripcion)
                                </td>
                                <!--**********************************************-->
                                <!-- ACCIONES REALIZADAS -->
                                <td style='background-color:#FCD790;text-align: center; border: 1px solid;'>Archivo de Apoyo</td>
                                <td style='background-color:#FCD790;text-align: center; border: 1px solid;'>Estado (Documento generado)
                                </td>
                                <td style='background-color:#FCD790;text-align: center; border: 1px solid;'>Responsable
                                de
                                Retorno
                                </td>
                                <td style='background-color:#FCD790;text-align: center; border: 1px solid;'>Motivo
                                del
                                Retorno
                                </td>
                                <!--**********************************************-->
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
    @include( 'admin.ruta.form.rutaflujo' )
    @include( 'admin.ruta.form.ruta' )
    @include( 'admin.reportes.form.expediente' )
@stop
