<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

    {{ Html::style('lib/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}
    {{ Html::script('lib/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}
    {{ Html::script('lib/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.es.js') }}

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )

    @include( 'admin.mantenimiento.js.carnetcanqr_ajax' )
    @include( 'admin.mantenimiento.js.carnetcanqr' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<style type="text/css">
    .formato{
        background-color: #FFF;
        margin:10px auto;
        -webkit-box-shadow: 5px 5px 20px #999;
        -moz-box-shadow: 5px 5px 20px #999;
        filter: shadow(color=#999999, direction=135, strength=2);
    }    
</style>
    <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <small style="font-weight: bold;">Carnet de Canes</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                <li><a href="#">Procesos</a></li>
                <li class="active">Carnet de Canes</li>
            </ol>
        </section>


        <!-- Main content -->
        <section class="content">
        <div class="row">
            <div class="formato col-md-1"></div>
            <div id="signupbox" class="formato col-md-10">
                <div class="form-horizontal">
                    <div class="">
                        <h2 class="text-center"><span class="fa fa-edit fa-1x"></span> CARNET DE CANES</h2>
                        <h5>Listado de DATOS</h5>
                        <hr/>
                    </div>

                    <div class="box-body table-responsive">
                        <table id="t_cargos" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Serie</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Fecha Nace</th>
                                    <th>Sexo</th>
                                    <th>Estado</th>
                                    <th> [ ] </th>
                                </tr>
                            </thead>
                            <tbody id="tb_cargos">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Serie</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Fecha Nace</th>
                                    <th>Sexo</th>
                                    <th>Estado</th>
                                    <th> [ ] </th>
                                </tr>
                            </tfoot>
                        </table>
                        <a class='btn btn-primary btn-sm' id="btn_nuevo"
                        data-toggle="modal" data-target="#cargoModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                    </div><!-- /.box-body -->
                        
                </div>
            </div>
        </div>
        </section><!-- /.content -->

@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.carnetcanqr' )
@stop
