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
<!--    @include( 'admin.ruta.js.ruta_ajax' )-->
@include( 'admin.ruta.js.clasitipotramite' )
@include( 'admin.ruta.css.clasitipotramite' )
@include( 'admin.ruta.js.clasitipotramite_ajax' )
@include( 'admin.mantenimiento.js.clasificadortramite' )
@include( 'admin.mantenimiento.js.clasificadortramite_ajax' )
@include( 'admin.ruta.js.proceso' )
@include( 'admin.ruta.js.asignar_ajax' )
@include( 'admin.ruta.js.ruta_ajax' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Configurar Servicio
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">Reporte</a></li>
        <li class="active">Mantenimiento</li>
    </ol>
</section>

<!-- Main content -->
<!-- Main content -->
<section class="content">
    <form id="form_clasificadortramites" name="form_clasificadortramites" method="POST" action="">
        <div class="box-body table-responsive">
            <table id="t_clasificadortramites" class="table table-bordered table-hover">
                <thead>
                    <tr><th colspan="14" style="text-align:center;background-color:#A7C0DC;"><h2>Servicios al usuario</h2></th></tr>
                    <tr></tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr></tr>
                </tfoot>
            </table>
            <a class="btn btn-primary"
                data-toggle="modal" data-target="#clasificadortramitesModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
            <a style="display:none" id="BtnEditar_clasificador" data-toggle="modal" data-target="#clasificadortramitesModal" data-titulo="Editar"></a>
        </div><!-- /.box-body -->
    </form>
    <br>
    <form class="FormG" id="form_costo_personal" name="form_costo_personal" method="POST" action="">
        <div class="form-group">
            <div class="box-header table-responsive">
                <div class="col-xs-12">
                    <h3>
                        Mantenimiento de Requisitos |
                        <small>Nombre de Trámite:  <label type="text" id="txt_titulo"></label></small>
                    </h3>                           
                </div>
            </div>
            <div class="box-body table-responsive">
                <table id="t_costo_personal" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Archivo</th>
                            <th>[]</th>
                            <th>[]</th>
                        </tr>
                        <!--<tr><th colspan="12" style="text-align:center;background-color:#A7C0DC;"><h2><spam id="txt_titulo">Contrataciones</spam></h2></th></tr>-->

                    </thead>
                    <tbody id="tb_costo_personal">
                    </tbody>

                </table>
                <a class="btn btn-primary"
                    data-toggle="modal" data-target="#requisitoModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                <a style="display:none" id="BtnEditar" data-toggle="modal" data-target="#requisitoModal" data-titulo="Editar"></a>
                <a class="btn btn-default btn-sm btn-sm" id="btn_close">
                    <i class="fa fa-remove fa-lg"></i>&nbsp;Cerrar
                </a>

            </div><!-- /.box-body -->
        </div>
    </form>
    <form class="FormG" name="form_actividad" id="form_actividad" method="POST" action="">
        <input class="form-control mant" type="hidden" name="id" id="id">
        <div class="row form-group" >
            <div class="box-header table-responsive">
                <div class="col-xs-12">
                    <h3>
                        Seleccionar Proceso |
                        <small>Nombre de Trámite:  <label type="text" id="txt_titulo"></label></small>
                    </h3>                           
                </div>
            </div>
            <div class="col-sm-12">

                <div class="col-sm-8">
                    <label class="control-label">Proceso:</label>
                    <input type="hidden" id="txt_flujo2_id" name="txt_flujo2_id">
                    <input type="hidden" id="txt_area2_id" name="txt_area2_id">
                    <input class="form-control" id="txt_proceso" name="txt_proceso" type="text"  value="" readonly="">

                </div>
                <div class="col-sm-1">
                    <br>
                    <span class="btn btn-primary" data-toggle="modal" data-target="#procesoModal" data-texto="txt_proceso" data-id="txt_flujo2_id" data-idarea="txt_area2_id" data-evento="cargarRutaFlujo" id="btn_buscar">
                        <i class="fa fa-search fa-lg"></i>
                    </span>
                </div>
            </div>

        </div>


        <div class="row form-group" id="tabla_ruta_flujo" style="display:none;">
            <div class="col-sm-12">
                <div class="box-body table-responsive">
                    <table id="t_ruta_flujo" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Proceso</th>
                                <th>Area</th>
                                <th>Dueño del Proceso</th>
                                <!-- <th>Nro Trámite Ok</th>
                                <th>Nro Trámite Error</th>-->
                                <th>Fecha Creación</th>
                                <th> [ ] </th>
                            </tr>
                        </thead>
                        <tbody id="tb_ruta_flujo">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Proceso</th>
                                <th>Area</th>
                                <th>Dueño del Proceso</th>
                                <!--<th>Nro Trámite Ok</th>
                                <th>Nro Trámite Error</th>-->
                                <th>Fecha Creación</th>
                                <th> [ ] </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-sm-12">
                <a class="btn btn-primary btn-sm" id="btn_guardar_todo">
                    <i class="fa fa-save fa-lg"></i>&nbsp;Guardar
                </a>
            </div>
        </div>
    </form>

    <form class="FormG" id="form_campo" name="form_campo" method="POST" action="">
        <input class="form-control mant" type="hidden" name="id" id="id">
        <div class="form-group" >
            <div class="box-header table-responsive">
                <div class="col-xs-12">
                    <h3>
                        Campos adicionales |
                        <small>Nombre de Trámite:  <label type="text" id="txt_titulo"></label></small>
                    </h3>                           
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-2">
                        <label class="form-label">Campo / Título:</label>
                        <input class="form-control" id="txt_campo" type="text"  value="">
                    </div>
                    <div class="col-sm-2 sub_titulo">
                        <label class="form-label">Sub Título:</label>
                        <input class="form-control" id="txt_sub_titulo" type="text"  value="">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label">Tipo de dato:</label>
                        <select class="form-control" id="slct_campo">
                            <option value=1 selected>Campo</option>
                            <option value=2>Título</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <br>
                        <a class="btn btn-primary btn_add_campo">
                            <i class="fa fa-plus fa-lg"></i>&nbsp;Agregar Campo
                        </a>
                        <a class="btn btn-warning btn-sm btn_close">
                            <i class="fa fa-remove fa-lg"></i>&nbsp;Cerrar
                        </a>
                    </div>
                </div>
                <div class="col-sm-12 text-center bg-navy"><h4>Diseño de Campos</h4></div>
                <div class="row" id="add_campo"></div>
                <br><hr>
                <div class="row table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan='4' class="text-center bg-navy">Configuración de Campos</th>
                            </tr>
                            <tr>
                                <th class="text-center bg-navy">Campo</th>
                                <th class="text-center bg-navy">Tipo</th>
                                <th class="text-center bg-navy">Capacidad</th>
                                <th class="text-center bg-navy">Lista separar por *</th>
                            </tr>
                        </thead>
                        <tbody id="add_campo2"></tbody>
                    </table>
                </div>
                <br><hr>
                <div class="row">
                    <div class="col-sm-2">
                        <a class="btn btn-success" id="btn_RegistrarCampos">
                            <i class="fa fa-save fa-lg"></i>&nbsp;Registrar Campos
                        </a>
                    </div>
                </div>

            </div><!-- /.box-body -->
        </div>
    </form>

    <form class="FormG" id="form_campo_asignacion" name="form_campo_asignacion" method="POST" action="">
        <input class="form-control mant" type="hidden" name="id" id="id">
        <input class="form-control mant" type="hidden" name="ruta_flujo_id" id="ruta_flujo_id">
        <div class="form-group">
            <div class="box-header table-responsive">
                <div class="col-xs-12">
                    <h3>
                        Asignación de Campos adicionales |
                        <small>Nombre de Trámite:  <label type="text" id="txt_titulo"></label></small>
                    </h3>                           
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <br>
                        <a class="btn btn-warning btn-sm btn_close">
                            <i class="fa fa-remove fa-lg"></i>&nbsp;Cerrar
                        </a>
                    </div>
                </div>
                <br><hr>
                <div class="row table-responsive col-md-6 col-md-offset-2">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Paso | Área / Campo</th>
                                <th class="text-center" style="min-width:120px;">Visible</th>
                                <th class="text-center" style="min-width:120px;">Modificar</th>
                            </tr>
                        </thead>
                        <tbody id="add_campo3"></tbody>
                    </table>
                </div>
                <br><hr>
                <!--
                <div class="row">
                    <div class="col-sm-2">
                        <a class="btn btn-success" id="btn_AsignarCampos">
                            <i class="fa fa-save fa-lg"></i>&nbsp;Asignar Campos
                        </a>
                    </div> 
                </div>
                -->

            </div><!-- /.box-body -->
        </div>
    </form>

    <form class="FormG" id="form_fa" name="form_fa" method="POST" action="">
        <input class="form-control mant" type="hidden" name="id" id="id">
        <input class="form-control mant" type="hidden" name="soloareas" id="soloareas" value='1'>
        <input class="form-control mant" type="hidden" name="ruta_flujo_id" id="ruta_flujo_id">
        <div class="form-group" >
            <div class="box-header table-responsive">
                <div class="col-xs-12">
                    <h3>
                        Asignación de Finalizar o Anular  |
                        <small>Nombre de Trámite:  <label type="text" id="txt_titulo"></label></small>
                    </h3>                           
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <br>
                        <a class="btn btn-warning btn-sm btn_close">
                            <i class="fa fa-remove fa-lg"></i>&nbsp;Cerrar
                        </a>
                    </div>
                </div>
                <br><hr>
                <div class="row table-responsive col-md-6 col-md-offset-2">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Paso | Área</th>
                                <th class="text-center" style="min-width:120px;">Finalizar / Anular</th>
                            </tr>
                        </thead>
                        <tbody id="add_campo4"></tbody>
                    </table>
                </div>
                <br><hr>

            </div><!-- /.box-body -->
        </div>
    </form>

    <form class="FormG" id="form_evento" name="form_evento" method="POST" action="">
        <input class="form-control mant" type="hidden" name="id" id="id">
        <input class="form-control mant" type="hidden" name="ruta_flujo_id" id="ruta_flujo_id">
        <div class="form-group" >
            <div class="box-header table-responsive">
                <div class="col-xs-12">
                    <h3>
                        Configuración de Eventos  |
                        <small>Nombre de Trámite:  <label type="text" id="txt_titulo"></label></small>
                    </h3>                           
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <br>
                        <a class="btn btn-warning btn-sm btn_close">
                            <i class="fa fa-remove fa-lg"></i>&nbsp;Cerrar
                        </a>
                    </div>
                </div>
                <div class="row col-md-12">
                    <br>
                    <div class="col-md-6">
                        <label>Campos creados:</label>
                        <select id="slct_campos_eventos" class="form-control">
                            <option value="">.::Seleccione::.</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <br>
                        <a class="btn btn-success btn_campo_evento">
                            <i class="fa fa-plus fa-lg"></i>&nbsp;Agregar
                        </a>
                    </div>
                    <select id="slct_condicion" class="form-control hidden">
                        <option value=''>.::Seleccione condición::.</option>
                        <option value='='>"=" Igual</option>
                        <option value='>='>">=" Mayor e Igual</option>
                        <option value='<='>"<=" Menor e Igual</option>
                        <option value='>'>">" Mayor</option>
                        <option value='<'>"<" Menor</option>
                        <option value='<>'>"<>" Diferente</option>
                    </select>
                    <select id="slct_anidado" class="form-control hidden">
                        <option value=''>.::Seleccione condición::.</option>
                        <option value='AND'>Y</option>
                        <option value='OR'>O</option>
                    </select>
                    <div class="col-md-7 table-responsive">
                        <table class="table table-bordered">
                            <thead class="alert-info">
                                <tr>
                                    <th class="text-center">Grupo Lógico</th>
                                    <th class="text-center">Campo</th>
                                    <th class="text-center">Condición</th>
                                    <th class="text-center">Valor de la condición</th>
                                    <th class="text-center">[]</th>
                                </tr>
                            </thead>
                            <tbody id="add_campo5"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        <label>Ingrese API(https:) ó Función(fn:controlador@funcion):</label>
                                        <input type="text" class="form-control txt_url_evento" name="txt_url_evento" placeholder="Ingrese API(https:) ó Función(fn:controlador@funcion)">
                                    </td>
                                    <td>
                                        <label>Ingrese nombre del evento:</label>
                                        <input type="text" class="form-control txt_evento" name="txt_evento" placeholder="Ingrese nombre del evento">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">
                                        <a class="btn btn-info btn_guardar_evento">
                                            <i class="fa fa-save fa-lg"></i>&nbsp;Guardar
                                        </a>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-7 table-responsive">
                        <table class="table table-bordered">
                            <thead class="alert-danger">
                                <tr>
                                    <th colspan='4' class="text-center"><h4>Eventos Configurados</h4></th>
                                </tr>
                                <tr>
                                    <th class="text-center">Evento</th>
                                    <th class="text-center">Condición del Evento</th>
                                    <th class="text-center">Url del evento - Api</th>
                                    <th class="text-center">[]</th>
                                </tr>
                            </thead>
                            <tbody id="eventos"></tbody>
                        </table>
                    </div>
                </div>


            </div><!-- /.box-body -->
        </div>
    </form>

</section><!-- /.content -->

@stop
@section('formulario')
@include( 'admin.mantenimiento.form.tipotramite' )
@include( 'admin.ruta.form.requisito' )
@include( 'admin.mantenimiento.form.clasificadortramite' )
@include( 'admin.ruta.form.proceso' )
@include( 'admin.ruta.form.rutaflujo' )
@include( 'admin.ruta.form.ruta' )
@stop
