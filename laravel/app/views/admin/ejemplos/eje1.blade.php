<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
@parent
@include( 'admin.js.slct_global_ajax' )
@include( 'admin.js.slct_global' )

@include( 'admin.ejemplos.js.eje1_ajax' )
@include( 'admin.ejemplos.js.eje1' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Mantenimiento de POI
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">Mantenimientos</a></li>
        <li class="active">Mantenimiento de Contrataciones</li>
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
                <form id="form_pois" name="form_pois" method="POST" action="">
                    <div class="box-body table-responsive">
                        <table id="t_pois" class="table table-bordered table-hover">
                            <thead>
                                <tr><th colspan="15" style="text-align:center;background-color:#A7C0DC;"><h2>Plan Operativo Institucional</h2></th></tr>
                                <tr></tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr></tr>
                            </tfoot>
                        </table>
                        <a class="btn btn-primary" id="BtnEje1"><i class="fa fa-plus fa-lg"></i>&nbsp;Eje1</a>
                        <a class="btn btn-primary" id="BtnEje2"><i class="fa fa-plus fa-lg"></i>&nbsp;Eje2</a>
                        <a style="display:none" id="BtnEditar" data-toggle="modal" data-target="#poiModal" data-titulo="Editar"></a>
                    </div><!-- /.box-body -->
                </form>
            </div><!-- /.box -->
            <!-- Finaliza contenido -->
        </div>
    </div>

</section><!-- /.content -->
@stop

@section('formulario')
@include( 'admin.ejemplos.form.eje1' )
@stop
