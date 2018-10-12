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
@stop

<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<style type="text/css">
    .treegrid-indent {
        width: 0px;
        height: 16px;
        display: inline-block;
        position: relative;
    }

    .treegrid-expander {
        width: 0px;
        height: 16px;
        display: inline-block;
        position: relative;
        left:-17px;
        cursor: pointer;
    }

    .mailbox .table-mailbox {
      border-left: 1px solid #ddd;
      border-right: 1px solid #ddd;
      border-bottom: 1px solid #ddd;
    }
    .mailbox .table-mailbox tr.unread > td {
      background-color: rgba(0, 0, 0, 0.05);
      color: #000;
      font-weight: 600;
    }
    .mailbox .table-mailbox .unread
    /*.mailbox .table-mailbox tr > td > .fa.fa-ban,*/
    /*.mailbox .table-mailbox tr > td > .glyphicon.glyphicon-star,
    .mailbox .table-mailbox tr > td > .glyphicon.glyphicon-star-empty*/ {
      /*color: #f39c12;*/
      cursor: pointer;
    }
    .mailbox .table-mailbox tr > td.small-col {
      width: 30px;
    }
    .mailbox .table-mailbox tr > td.name {
      width: 150px;
      font-weight: 600;
    }
    .mailbox .table-mailbox tr > td.time {
      text-align: right;
      width: 100px;
    }
    .mailbox .table-mailbox tr > td {
      white-space: nowrap;
    }
    .mailbox .table-mailbox tr > td > a {
      color: #444;
    }

    .btn-yellow{
        color: #0070ba;
        background-color: ghostwhite;
        border-color: #ccc;
        font-weight: bold;
    }

    td.details-control {
        background: url('lib/web/details_open.png') no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url('lib/web/details_close.png') no-repeat center center;
    }

    @media screen and (max-width: 767px) {
      .mailbox .nav-stacked > li:not(.header) {
        float: left;
        width: 50%;
      }
      .mailbox .nav-stacked > li:not(.header).header {
        border: 0!important;
      }
      .mailbox .search-form {
        margin-top: 10px;
      }
    }

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
            Recuperacion de Documentos Digitales.
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Bandeja de Inconclusos y Gestión</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-1"></div>
          <div id="signupbox" class="formato col-md-10">
              <div class="form-horizontal" style="padding: 20px;">

                <!-- Inicia contenido -->
                <div id="audiobi"></div>
                <!--
                <audio autoplay>
                    <source src="http://localhost/ingind/public/sonido/alarma2.mp3" type="audio/mp3">
                    Tu navegador no soporta HTML5 audio.
                </audio>
                -->
                <div class="mailbox row">
                    <div class="col-md-12">
                        <div class="row form-group" id="reporte" >
                            <div class="col-sm-12">
                                <div class="box-body table-responsive">
                                <!-- THE MESSAGES -->

                                    <div class="divform" align="center">
                                    	<button class="btn btn-sm btn-primary" onclick="addNew();"><i class="fa fa-plus"></i> Nuevo</button>
                                    </div>

                                    <div class="divform" style="display:none; overflow: hidden;">
                                        <form action="recovery/guardar" method="POST" enctype="multipart/form-data" name="form_rec" id="form_rec">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-11">
                                                <div class="form-group row">
                                                    <label class="col-md-3">Tipo de documento</label>
                                                    <div class="col-md-7">
                                                        <select name="tipo_documento" class="form-control"><option value="1">PROVEIDO</option><option value="3">MEMORANDO</option><option value="4">OFICIO</option><option value="5">INFORME</option><option value="7">CARTA</option><option value="9">REQUERIMIENTO</option><option value="10">COMPROBANTE DE PAGO</option><option value="11">RESOLUCIÓN DE EJECUCIÓN COACTIVA</option><option value="12">RESOLUCIÓN DETERMINACIÓN</option><option value="13">LICENCIA</option><option value="14">CERTIFICADO DE DEFENSA CIVIL</option><option value="15">DIRECTIVA</option><option value="16">DICTAMEN</option><option value="17">ACTA</option><option value="18">CERTIFICADO</option><option value="19">AUTORIZACIÓN</option><option value="20">DECLARACIÓN JURADA</option><option value="21">RESOLUCIÓN SUB GERENCIA</option><option value="22">RESOLUCIÓN DE GERENCIA</option><option value="23">RESOLUCIÓN GERENCIA MUNICIPAL</option><option value="24">RESOLUCIÓN DE ALCALDÍA</option><option value="25">NOTIFICACIÓN PREVENTIVA</option><option value="26">ORDEN DE COMPRA</option><option value="27">ORDEN DE SERVICIO</option><option value="29">CONSTANCIA</option><option value="30">MEMORANDO MÚLTIPLE</option><option value="31">MEMORANDO CIRCULAR</option><option value="32">INFORME MÚLTIPLE</option><option value="33">INFORME CIRCULAR</option><option value="34">CARTA A REGIDOR</option><option value="35">REPORTE DIARIO</option><option value="36">REPORTE CONSOLIDADOS</option><option value="37">ACUERDO DE CONCEJO</option><option value="38">NOTA DE CRÉDITO PRESUPUESTAL </option><option value="39">CONTRATO</option><option value="40">PROYECTO DE CONTRATO</option><option value="41">NIA NOTA DE ING A ALMACÉN</option><option value="42">FICHA DE INSCRIPCIÓN</option><option value="43">TIKET DE PAGO</option><option value="44">COMPROBANTE</option><option value="45">CHEQUE</option><option value="46">ORDEN DE PAGO</option><option value="47">CARTA NOTARIAL</option><option value="48">CONTESTACIONES</option><option value="49">ALEGATOS</option><option value="50">ESCRITO</option><option value="51">INFORME TÉCNICO</option><option value="52">DEMANDA</option><option value="53">EXCEPCIÓN</option><option value="54">DENUNCIA</option><option value="55">MEDIO IMPUGNATORIO</option><option value="56">DEFENSA PREVIA</option><option value="57">CARTA DE INICIO</option><option value="58">INFORME DE META</option><option value="59">INFOR DE CARTA INIC</option><option value="60">ACTA DE CONSTATACION</option><option value="61">RESOLUCION DE SANCIÓN</option><option value="62">ACTA DE LEVANT DE CLAUSURA</option><option value="63">ACTA DE CLAUSURA</option><option value="64">ACTA DE RETENCIÓN </option><option value="65">ACTA DE INTERNAMIENTO</option><option value="66">ACTA DE DEVOLUCION</option><option value="67">ACTA DE DECOMISO</option><option value="68">ACTA DE LIBERACIÓN</option><option value="69">ACTA DE DETERIORO</option><option value="70">RESOLUCIÓN DETERMINISTA DE IMPUESTO PREDIAL</option><option value="71">RESOLUCIÓN DE MULTA TRIBUTARIA</option><option value="72">FICHA DE VERIFICACION TECNICA</option><option value="73">ACTA DE INSPECCION PREDIAL</option><option value="74">ACTA DE INSPECCION NO REALIZADA</option><option value="75">CERTIFICADO PRESUPUESTAL</option><option value="76">ROL DE SERVICIO</option><option value="77">ORDENANZA</option><option value="78">DECRETO DE ALCALDÍA</option><option value="79">EXPEDIENDE DE CONTRATACION</option><option value="80">INFORME DIAGNOSTICO</option><option value="81">LIQUIDACIÓN</option><option value="82">DOCUMENTO</option><option value="83">ORDEN DE TRABAJO</option><option value="84">ACTIVIDAD</option><option value="85">INFORME DE PRECALIFICACIÓN</option><option value="86">DESCARGO</option><option value="87">RESOLUCIÓN DE SGP</option><option value="88">TIEMPO</option><option value="89">RECURSO DE RECONCILIACIÓN</option><option value="90">ACEPTACION</option><option value="91">APELACIÓN</option><option value="94">NOTIFICACIÓN ADMINISTRATIVA</option><option value="95">INFORME PERSONAL</option><option value="96">INVITACIÓN A COTIZAR</option><option value="97">OFICIO PNCSCDU</option><option value="98">OFICIO PNCSCDU</option><option value="99">AUTORIZACIÓN CDSE</option><option value="100">MEMORANDO AIP</option><option value="101">CARTA AIP</option><option value="102">RESOLUCIÓN DE ALCALDÍA DIVORCIO</option><option value="103">CONSTANCIA DE POSESIÓN DE LOTE</option><option value="104">CERTIFICADO DE NUMERACIÓN</option><option value="105">CERTIFICADO DE JURISDICCIÓN</option><option value="106">CERTIFICADO DE PARÁMETROS URBANÍSTICOS Y EDIFICATORIOS</option><option value="107">RESOLUCIÓN REGISTRAL</option><option value="108">MEMORANDO INTERNO</option><option value="109">OFICIO CIRCULAR</option><option value="110">PAPELETA DE AUTORIZACIÓN DE VACACIONES</option><option value="112">CITACION CIRCULAR</option><option value="113">ACTA DE CONCILIACION</option><option value="114">RESOLUCIÓN DE GERENCIA GR</option><option value="115">INFORME OI</option><option value="116">CONFORMIDAD DE OBRA</option><option value="117">AMPLIACIÓN DE AUTORIZACIÓN</option><option value="118">CONSTANCIA DE LIBRE DISPONIBILIDAD</option><option value="120">ACTA DE VERIFICACIÓN</option><option value="122">INFORME SP</option><option value="123">INFORME TÉCNICO SANITARIO</option><option value="124">CONSTANCIA NEGATIVA CATASTRAL</option><option value="125">CERTIFICADO NEGATIVO DE DEUDAS TRIBUTARIAS</option><option value="126">CARTA CIRCULAR</option><option value="127">DOCUMENTO SIN NUMERACION</option><option value="128">INFORME LEGAL</option><option value="129">CONSTANCIA DE NUMERACION</option><option value="130">RESOLUCIÓN SUB GERENCIA</option><option value="131">CERTIFICADO DE NOMENCLATURA</option></select>
                                                    </div>
                                                </div>                                            

                                                <div class="form-group row">
                                                    <label class="col-md-3">Número de documento</label>
                                                    <div class="col-md-2"><input class="form-control" type="text" name="numero" onkeypress="return justNumbers(event);" placeholder="001"></div>
                                                    <label class="col-md-2" style="padding-left: 0px; padding-right: 0px;">Fecha del doc.</label>
                                                    <div class="col-md-3">
                                                        <div class="input-group">
                                                          <input class="form-control data_fija fechas" placeholder="yyyy-mm-dd" id="fechaPicker" name="fecha" type="text" style="cursor:pointer;" value="" readonly>
                                                          <span class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--
                                                    <div class="row">
                                                        <div class="col-md-3">Fecha del documento</div>
                                                        <div class="col-md-4">                                                
                                                            <div class="input-group">
                                                              <input class="form-control datepicker" id="fechaPicker" type="text" name="fecha">
                                                            </div>
                                                        </div>
                                                    </div>
                                                -->
                                                <div class="form-group row">
                                                    <label class="col-md-3">Archivo:</label>
                                                    <div class="col-md-4"><input type="file" name="documento[]" multiple="multiple"></div>
                                                    <div class="col-md-3 text-right"><input type="submit" value="Guardar" class="btn btn-primary"></div>
                                                </div>
                                                <div class="form-group row"><hr/></div>
                                            </div> 
                                        </form>
                                    </div> 

                                    <br/>
                                    <form name="form_filtros" id="form_filtros" method="POST" action="">
                                        <!-- <table id="t_reporte_ajax" class="table table-mailbox">-->
                                        <div class="box-body table-responsive">
                                        <table id="t_reporte_ajax" class="table table-bordered table-striped" style="width: 99%;">
                                            <thead>
                                                <tr>
                                                    <th>#</th>                                                    
                                                    <th id="th_dg" style='width:350px !important;' class="unread">Tipo Documento<br>
                                                    </th>                                                    
                                                    <th id="th_pd" style='width:250px !important;' class="unread">Numero<br>
                                                    </th>                                                    
                                                    <th id="th_fi" style='width:250px !important;' class="unread">Fecha<br>
                                                    </th>                                                    
                                                    <th id="th_pr" style='width:100px !important;' class="unread">Documento<br>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="tblContent">

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Tipo Documento</th>
                                                    <th>Numero</th>
                                                    <th>Fecha</th>
                                                    <th>Documento</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        </div>
                                    </form>

                                </div><!-- /.table-responsive -->
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                              </div>
                              <div class="modal-body">
                                
                                <form action="recovery/actualizar" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-4">Tipo de documento</div>
                                        <div class="col-md-4" id="m_tipo">
                                            PROVEIDO
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">Número de documento</div>
                                        <div class="col-md-4"><input class="form-control" type="text" name="numero" id="m_numero" readonly="readonly"></div>
                                    </div>  


                                    <div class="row">
                                        <div class="col-md-4">Fecha del documento</div>
                                        <div class="col-md-4"><input class="form-control datepicker" type="text" name="fecha" id="m_fecha" readonly="readonly"></div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-md-4">Archivo:</div>
                                        <div class="col-md-4"><input type="file" name="documento[]" multiple="multiple"></div>
                                    </div>  


                                    <div class="row">
                                        <input type="hidden" name="edit" value="" id="editID">
                                        <div class="col-md-4"><input type="submit" value="Guardar"></div>
                                    </div> 
                                           
                                </form>


                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                          </div>
                        </div>
                     
                    </div><!-- /.col (RIGHT) -->
                </div>
        <!-- Finaliza contenido -->
        <!-- </div>-->

               </div>
          </div>
      </div>
    </section><!-- /.content -->



<script type="text/javascript">
var tipodoc={1:'PROVEIDO',3:'MEMORANDO',4:'OFICIO',5:'INFORME',7:'CARTA',10:'COMPROBANTE DE PAGO',11:'RESOLUCIÓN DE EJECUCIÓN COACTIVA',12:'RESOLUCIÓN DETERMINACIÓN',13:'LICENCIA',14:'CERTIFICADO DE DEFENSA CIVIL',15:'DIRECTIVA',16:'DICTAMEN',17:'ACTA',18:'CERTIFICADO',19:'AUTORIZACIÓN',20:'DECLARACIÓN JURADA',21:'RESOLUCIÓN SUB GERENCIA',22:'RESOLUCIÓN DE GERENCIA',23:'RESOLUCIÓN GERENCIA MUNICIPAL',24:'RESOLUCIÓN DE ALCALDÍA',25:'NOTIFICACIÓN PREVENTIVA',26:'ORDEN DE COMPRA',27:'ORDEN DE SERVICIO',29:'CONSTANCIA',30:'MEMORANDO MÚLTIPLE',31:'MEMORANDO CIRCULAR',32:'INFORME MÚLTIPLE',33:'INFORME CIRCULAR',34:'CARTA A REGIDOR',35:'REPORTE DIARIO',36:'REPORTE CONSOLIDADOS',37:'ACUERDO DE CONCEJO',38:'NOTA DE CRÉDITO PRESUPUESTAL',39:'CONTRATO',40:'PROYECTO DE CONTRATO',41:'NIA NOTA DE ING A ALMACÉN',42:'FICHA DE INSCRIPCIÓN',43:'TIKET DE PAGO',44:'COMPROBANTE',45:'CHEQUE',46:'ORDEN DE PAGO',47:'CARTA NOTARIAL',48:'CONTESTACIONES',49:'ALEGATOS',50:'ESCRITO',51:'INFORME TÉCNICO',52:'DEMANDA',53:'EXCEPCIÓN',54:'DENUNCIA',55:'MEDIO IMPUGNATORIO',56:'DEFENSA PREVIA',57:'CARTA DE INICIO',58:'INFORME DE META',59:'INFOR DE CARTA INIC',60:'ACTA DE CONSTATACION',61:'RESOLUCION DE SANCIÓN',62:'ACTA DE LEVANT DE CLAUSURA',63:'ACTA DE CLAUSURA',64:'ACTA DE RETENCIÓN </option',65:'ACTA DE INTERNAMIENTO',66:'ACTA DE DEVOLUCION',67:'ACTA DE DECOMISO',68:'ACTA DE LIBERACIÓN',69:'ACTA DE DETERIORO',70:'RESOLUCIÓN DETERMINISTA DE IMPUESTO PREDIAL',71:'RESOLUCIÓN DE MULTA TRIBUTARIA',72:'FICHA DE VERIFICACION TECNICA',73:'ACTA DE INSPECCION PREDIAL',74:'ACTA DE INSPECCION NO REALIZADA',75:'CERTIFICADO PRESUPUESTAL',76:'ROL DE SERVICIO',77:'ORDENANZA',78:'DECRETO DE ALCALDÍA',79:'EXPEDIENDE DE CONTRATACION',80:'INFORME DIAGNOSTICO',81:'LIQUIDACIÓN',82:'DOCUMENTO',83:'ORDEN DE TRABAJO',84:'ACTIVIDAD',85:'INFORME DE PRECALIFICACIÓN',86:'DESCARGO',87:'RESOLUCIÓN DE SGP',88:'TIEMPO',89:'RECURSO DE RECONCILIACIÓN',90:'ACEPTACION',91:'APELACIÓN',94:'NOTIFICACIÓN ADMINISTRATIVA',95:'INFORME PERSONAL',96:'INVITACIÓN A COTIZAR',97:'OFICIO PNCSCDU',98:'OFICIO PNCSCDU',99:'AUTORIZACIÓN CDSE',100:'MEMORANDO AIP',101:'CARTA AIP',102:'RESOLUCIÓN DE ALCALDÍA DIVORCIO',103:'CONSTANCIA DE POSESIÓN DE LOTE',104:'CERTIFICADO DE NUMERACIÓN',105:'CERTIFICADO DE JURISDICCIÓN',106:'CERTIFICADO DE PARÁMETROS URBANÍSTICOS Y EDIFICATORIOS',107:'RESOLUCIÓN REGISTRAL',108:'MEMORANDO INTERNO',109:'OFICIO CIRCULAR',110:'PAPELETA DE AUTORIZACIÓN DE VACACIONES',112:'CITACION CIRCULAR',113:'ACTA DE CONCILIACION',114:'RESOLUCIÓN DE GERENCIA GR',115:'INFORME OI',116:'CONFORMIDAD DE OBRA',117:'AMPLIACIÓN DE AUTORIZACIÓN',118:'CONSTANCIA DE LIBRE DISPONIBILIDAD',120:'ACTA DE VERIFICACIÓN',122:'INFORME SP',123:'INFORME TÉCNICO SANITARIO',124:'CONSTANCIA NEGATIVA CATASTRAL',125:'CERTIFICADO NEGATIVO DE DEUDAS TRIBUTARIAS',126:'CARTA CIRCULAR',127:'DOCUMENTO SIN NUMERACION',128:'INFORME LEGAL',129:'CONSTANCIA DE NUMERACION',130:'RESOLUCIÓN SUB GERENCIA',131:'CERTIFICADO DE NOMENCLATURA'};


    function addNew(){
    	$(".divform").toggle('slow');
    }

    function showImage(url){
		 window.open(url); 
	}

	$(document).ready(function() {
        $(".fechas").datetimepicker({
            format: "yyyy-mm-dd",
            language: 'es',
            showMeridian: false,
            time: false,
            minView: 3,
            startView: 2,
            autoclose: true,
            todayBtn: false
        });

        //$("#fechaPicker").datepicker({ dateFormat: 'yy-mm-dd' });

		$.post('recovery/load',{},function(data){
			var trd="";
			
			if(data.length>0)for (var i = 0; i < data.length; i++) {
                //console.log(data[i].archivo);
                if(IsJsonString(data[i].archivo)){
                    var jsx = JSON.parse(data[i].archivo);
                    var imgs = "";
                    for (var j = 0; j < jsx.length; j++) {
                        imgs +='<span class="btn btn-primary btn-sm" onclick="showImage(\''+data[i].dir+jsx[j]+'\');"><i class="fa fa-image"></i></span>'; 
                    }
                }else{
                    var imgs = '<span class="btn btn-primary btn-sm" onclick="showImage(\''+data[i].dir+data[i].archivo+'\');"><i class="fa fa-image"></i></span>';
                }
                

				trd=trd+'<tr id="TR_'+data[i].id+'"><td>'+(i+1)+'</td> <td>'+tipodoc[data[i].tipo_doc]+'</td> <td>'+data[i].numero+'</td><td>'+data[i].fecha_doc+'</td><td> <span class="btn btn-warning btn-sm" onclick="edit(\''+data[i].id+'\');"><i class="fa fa-edit"></i></span> - '+imgs+'</td></tr>';

			}else{
				trd='<tr><td colspan="4">No hay documentos registrados.</td></tr>';
			}
			//console.log(trd);

			$("#tblContent").html(trd);
            $("#t_reporte_ajax").dataTable();
		});
	});

    function edit(x){
        $("#editID").val(x);
        $("#exampleModalCenter").modal("show");
        $("#m_tipo").text($("#TR_"+x).find('td').eq(1).text());
        $("#m_numero").val($("#TR_"+x).find('td').eq(2).text());
        $("#m_fecha").val($("#TR_"+x).find('td').eq(3).text());
    }


	function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    function justNumbers(e)
    {
      var keynum = window.event ? window.event.keyCode : e.which;
      if ((keynum == 8) || (keynum == 46))
        return true;
      
      return /\d/.test(String.fromCharCode(keynum));
    }
</script>
@stop
