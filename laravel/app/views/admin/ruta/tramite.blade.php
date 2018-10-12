<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    
    {{ HTML::script('lib/input-mask/js/jquery.inputmask.js') }}
    {{ HTML::script('lib/input-mask/js/jquery.inputmask.date.extensions.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )

    @include( 'admin.ruta.js.ruta_ajax' )
    @include( 'admin.ruta.js.validar_ajax' )
    @include( 'admin.ruta.js.tramite' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1 style="text-align: center; font-size: 35px">
                        <b>
                            Anular Trámite
                        </b>
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Ruta</a></li>
                        <li class="active">Anular Trámite</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!--<div class="row">-->
                                <form name="form_validar" id="form_validar" method="POST">
                                    <div class="col-xl-12">
                                        <fieldset>
                                            <div class="row form-group" >                                           
                                                <div class="col-sm-12">

                                                    <!--CUADRO DE TEXTO-->
                                                    <div class="col-sm-3"></div>
                                                    <!--*******************************-->
                                                    <div class="col-sm-6">
                                                        <!--<label class="control-label">Trámite:</label>-->
                                                        <input style="text-align: center" type="text" class="form-control" placeholder="Ingrese Nro Trámite:" id="txt_tramite" name="txt_tramite"/>
                                                    </div>

                                                    <!--*******************************-->
                                                    <div class="col-sm-3"></div>
                                                    <!--*******************************-->  

                                                </div>

                                                <div class="col-sm-12">
                                                    <!--*******************************-->
                                                    <div class="col-sm-5"></div>
                                                    <!--*******************************-->
                                                    <!--BOTON MOSTRAR-->
                                                    <div class="col-sm-2">
                                                        <label class="control-label"></label>                                
                                                        <input type="button" class="form-control btn btn-primary" id="btn_buscar" name="btn_buscar" value="Buscar Trámite">
                                                    </div>
                                                    <!--*******************************-->
                                                    <div class="col-sm-5"></div>
                                                    <!--*******************************-->
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-xs-12">
                                        <!--<div class="box">-->
                                            <div class="row form-group" id="tabla_ruta_detalle" style="display:none;">
                                                <div class="col-sm-12">
                                                    <div class="box-body table-responsive">
                                                        <!--<table id="t_ruta_detalle" class="table table-bordered table-hover">-->
                                                        <table id="t_ruta_detalle" class="table table-bordered" width="100%">
                                                            <thead>
                                                                <tr style="background-color:#E5E5E5;">
                                                                    <th style="width:5%; text-align: center; border: black 2px solid;">#</th>
                                                                    <th style="width:20%; text-align: center; border: black 2px solid;">Nro Trámite</th>
                                                                    <th style="width:10%; text-align: center; border: black 2px solid;">Fecha Trámite</th>
                                                                    <th style="width:20%; text-align: center; border: black 2px solid;">Tipo Solicitante</th>
                                                                    <th style="width:20%; text-align: center; border: black 2px solid;">Solicitante</th>
                                                                    <th style="width:15%; text-align: center; border: black 2px solid;">Sumilla</th>
                                                                    <th style="width:10%; text-align: center; border: black 2px solid;"> [ ] </th>
                                                                </tr>
                                                            </thead>

                                                            <tbody id="tb_ruta_detalle"></tbody>

                                                            <thead>
                                                                <tr style="background-color:#E5E5E5;">
                                                                    <th style="text-align: center; border: black 2px solid;">#</th>
                                                                    <th style="text-align: center; border: black 2px solid;">Nro Trámite</th>
                                                                    <th style="text-align: center; border: black 2px solid;">Fecha Trámite</th>
                                                                    <th style="text-align: center; border: black 2px solid;">Tipo Solicitante</th>
                                                                    <th style="text-align: center; border: black 2px solid;">Solicitante</th>
                                                                    <th style="text-align: center; border: black 2px solid;">Sumilla</th>
                                                                    <th style="text-align: center; border: black 2px solid;"> [ ] </th>
                                                                </tr>
                                                            </thead>

                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <!--</div>-->
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
                        
                    <!--</div>-->

                </section><!-- /.content -->


<script type="text/javascript">
    $("#txt_tramite").keydown(function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            $("#btn_buscar").click();
            return false;
        }
    });
</script>


@stop
@section('formulario')
     @include( 'admin.ruta.form.ruta' )
@stop
