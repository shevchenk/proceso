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
    @include( 'admin.ruta.js.expediente_ajax' )
    @include( 'admin.ruta.js.expediente' )
    

@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 style="text-align: center; font-size: 35px">
            <b>        
            Reporte de tramite por proceso.
            </b>            
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
        <form id="form_tramiteunico" name="form_tramiteunico" method="post">
            <div class="col-xl-12">
                <fieldset>
                    <div class="row form-group">
                        
                        <div class="col-sm-12">
                            <!--CUADRO DE TEXTO-->
                            <div class="col-sm-3" align="center">
                                Rango de fechas
                            </div>
                            <!--*******************************-->
                            <div class="col-sm-3" align="center">
                                Nombre documento
                            </div>
                            <!--*******************************-->
                            <div class="col-sm-3" align="center">
                                Tipo de proceso
                            </div>
                            <!--*******************************-->                          
                        </div>

                        <form id="xForm" name="xForm">
                            <div class="col-sm-12">
                                <!--CUADRO DE TEXTO-->
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="rango_fecha" name="rango_fecha" onfocus="blur()"/>
                                </div>
                                <!--*******************************-->
                                <div class="col-sm-3">
                                    <!--<label class="control-label">Trámite:</label>-->
                                    <input style="text-align: center" type="text" class="form-control" placeholder="Tipo + Nro + Año  => Ej: EX {{ rand(3000,9999) }} {{ date("Y") }}" id="docName" name="docName" />
                                </div>
                                <!--*******************************-->
                                <div class="col-sm-3">
                                    <select id="proceso" name="proceso[]" class="form-control" multiple></select>
                                </div>
                                <!--*******************************-->
                                <div class="col-sm-3">
                                    <input type="button" class="form-control btn btn-primary" id="generar_1" name="generar_1" value="Mostrar">
                                </div>
                            </div>
                        </form>
                    </div>
                </fieldset>
            </div>
            
            <br>

            <div class="col-xl-12">
                <div class="form-group">
                    <table id="t_reported_tab_1" class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <td>Documento inicial</td>
                                <td>Documento actual</td>
                                <td>Flujo inicial</td>
                                <td>Flujo actual</td>
                                <td>Paso actual</td>
                                <td>Paso final</td>
                                <td>Fecha Inicial</td>
                                <td>Fecha Tramite</td>
                                <td>Fecha final</td>
                            </tr>
                        </thead>
                        <tbody id="contentTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </section><!-- /.content -->
<script type="text/javascript">

    
    var data = {estado:1};
    var ids = [];


        slctGlobal.listarSlct('flujo','proceso','multiple',ids,data);
        

    $('#rango_fecha').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: false,
        showDropdowns: true
    });

    $("#generar_1").click(function(){
        var url="reporteprocesos/tramitesdocumento";
        var dat = {docName:$("#docName").val(),rango_fecha:$("#rango_fecha").val()};
        console.log(dat);
        $.post(url,dat).done(function(data){
            console.log(data);

            var tr ="";

            for (var i = 0; i < data.datos.length; i++) {
                tr = tr + "<tr>";
                tr = tr + "<td> " + data.datos[i].documento_inicial + " </td>";
                tr = tr + "<td> " + data.datos[i].documento_actual + " </td>";
                tr = tr + "<td> " + data.datos[i].flujo_1 + " </td>";
                tr = tr + "<td> " + data.datos[i].flujo_2 + " </td>";
                tr = tr + "<td> " + data.datos[i].paso_actual + " </td>";
                tr = tr + "<td> " + data.datos[i].paso_final + " </td>";
                tr = tr + "<td> " + data.datos[i].fip1 + " </td>";
                tr = tr + "<td> " + data.datos[i].fip2 + " </td>";
                tr = tr + "<td> " + data.datos[i].ff + " </td>";
            tr = tr + "</tr>";
            }
            $("#contentTable").html(tr);

        });
    });

</script>
@stop
@section('formulario')
    @include( 'admin.ruta.form.rutaflujo' )
    @include( 'admin.ruta.form.ruta' )
    @include( 'admin.reporte.form.expediente' )
@stop
