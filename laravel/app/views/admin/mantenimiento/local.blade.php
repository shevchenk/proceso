<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}  
    {{ HTML::script('lib/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}
    {{ HTML::script('lib/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.es.js') }}
    
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )

    @include( 'admin.mantenimiento.js.local_ajax' )
    @include( 'admin.mantenimiento.js.local' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
    <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Mantenimiento de Locales
                <small> </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                <li><a href="#">Mantenimientos</a></li>
                <li class="active">Mantenimiento de Locales</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- Inicia contenido -->
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Filtros</h3>
                        </div><!-- /.box-header -->
                        <form id="form_locales" name="form_locales" method="POST" action="">
                        <div class="box-body table-responsive">
                            <table id="t_locales" class="table table-bordered table-hover">
                                <thead>
                                <tr><th colspan="5" style="text-align:center;background-color:#A7C0DC;"><h2>Locales</h2></th></tr>
                                <tr></tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr></tr>
                                </tfoot>
                            </table>
                            <a class="btn btn-primary"
                            data-toggle="modal" data-target="#localModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                            <a style="display:none" id="BtnEditar" data-toggle="modal" data-target="#localModal" data-titulo="Editar"></a>
                        </div><!-- /.box-body -->
                        </form>
                    </div><!-- /.box -->
                    <!-- Finaliza contenido -->
                </div>
            </div>

        </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.local' )
@stop
