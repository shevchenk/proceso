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
    @include( 'admin.procesos.js.cargafiscatributaria_ajax' )
    @include( 'admin.procesos.js.cargafiscatributaria' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            CARGA FISCALIZACION TRIBUTARIA
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Procesos</a></li>
            <li class="active">Fiscalizaci&oacute;n</li>
        </ol>
    </section>

        <!-- Main content -->
        <section class="content">
            <!-- Inicia contenido -->
            <div class="panel panel-info">
              <div class="panel-heading" style="overflow: hidden;">
                    <form id="form_file" name="form_file" action="" enctype="multipart/form-data" method="post">
                        <div class="col-sm-4">
                            <label>Seleccionar Archivo</label>
                            <input type="file" class="form-control" id="carga" name="carga" >
                        </div>
                    </form> 
              </div>
              <div class="panel-body" style="overflow: hidden;">
                <div class="col-sm-4"><button type="button" id="btn_cargar" class="btn btn-primary">Guardar</button></div>
              </div>
            </div>
            <!-- Finaliza contenido -->
        </div>
    </section><!-- /.content -->
@stop
