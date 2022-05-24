<script type="text/javascript">
temporalBandeja=0;
valposg=0;
var ClickActCamp = false;
var fechaTG='';
var horaTG='';
var TiempoFinalTG='';
var areasG=[]; // texto area
var areasGId=[]; // id area
var theadArea=[]; // cabecera area
var tbodyArea=[]; // cuerpo area
var tfootArea=[]; // pie area

var tiempoGId=[]; // id posicion del modal en base a una area.
var tiempoG=[];
var verboG=[];
var posicionDetalleVerboG=0;

var RolIdG='';
var UsuarioId='';
var fechaAux="";
$(document).ready(function() {
    $('#txt_observacion').attr('disabled','true');
    slctGlobal.listarSlct2('rol','slct_rol_modal',data);
    slctGlobal.listarSlct2('verbo','slct_verbo_modal',data);
    slctGlobal.listarSlct2('documento','slct_documento_modal',data);
    $("#btn_close").click(Close_ruta);
    $("#btn_actualizar_campos").click(ActualizarCampos);

    $('#rutaModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var text = $.trim( button.data('text') );
      var id= $.trim( button.data('id') );
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      $("#form_ruta_tiempo #txt_nombre").val(text);
      $("#form_ruta_tiempo").append('<input type="hidden" value="'+id+'" id="txt_area_id_modal">');
      /*alert(id);
      for(i=0; i<areasGId.length; i++){
        alert(areasGId[i]+"=="+id);
        if(areasGId[i]==id){
            alert("encontrado "+areasGId[i]);
        }
      }*/
      var position=tiempoGId.indexOf(id);
      var posicioninicial=areasGId.indexOf(id);
      //alert("tiempo= "+position +" | areapos="+posicioninicial);
      var tid=0;
      var validapos=0;
      var detalle=""; var detalle2="";

      if(position>=0){
        tid=position;
        //alert("actualizando");
        detalle=tiempoG[tid][0].split("_");
        detalle[0]=posicioninicial;
        tiempoG[tid][0]=detalle.join("_");

        detalle2=verboG[tid][0].split("_");
        detalle2[0]=posicioninicial;
        verboG[tid][0]=detalle2.join("_");
      }
      else{
        //alert("registrando");
        tiempoGId.push(id);
        tiempoG.push([]);
        tid=tiempoG.length-1;
        tiempoG[tid].push(posicioninicial+"__");

        verboG.push([]);
        verboG[tid].push(posicioninicial+"______");
      }

      var posicioninicialf=posicioninicial;
        for(var i=1; i<tbodyArea[posicioninicial].length; i++){
            posicioninicialf++;
            validapos=areasGId.indexOf(id,posicioninicialf);
            posicioninicialf=validapos;
            if( i>=tiempoG[tid].length ){
                tiempoG[tid].push(validapos+"__");

                verboG[tid].push(validapos+"______");
            }
            else{
                detalle=tiempoG[tid][i].split("_");
                detalle[0]=validapos;
                tiempoG[tid][i]=detalle.join("_");

                detalle2=verboG[tid][i].split("_");
                detalle2[0]=validapos;
                verboG[tid][i]=detalle2.join("_");
            }
        }

      pintarTiempoG(tid);



      $("#form_ruta_verbo #txt_nombre").val(text);
      $("#form_ruta_verbo").append('<input type="hidden" value="'+id+'" id="txt_area_id_modal">');
    });

    $('#rutaModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      $("#form_ruta_tiempo input[type='hidden']").remove();
      $("#form_ruta_verbo input[type='hidden']").remove();
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });

    $(document).on('click', '#ExpedienteU', function(event) {
        event.preventDefault();
        $("#expedienteModal").modal('show');
        expedienteUnico();
    });

    $("[data-toggle='offcanvas']").click();
    RolIdG='<?php echo Auth::user()->rol_id; ?>';
    UsuarioId='<?php echo Auth::user()->id; ?>';
    ResponsableG='<?php echo Auth::user()->responsable_area; ?>';

    slctGlobal.listarSlct('lista/tipovizualizacion','slct_tipo_visualizacion','multiple',null,null);    

    $("#btnAdd").addClass('hidden');
    if( ResponsableG == 1 ){
        var data={estado_persona:1,solo_area:1};
        //slctGlobal.listarSlct('persona','cboPersona','simple',null,data);
        slctGlobal.listarSlct('persona','slct_persona','simple',null,data);
        $("#btnAdd").removeClass('hidden');
    }

    Bandeja.verificarFueraTiempo();
    Bandeja.MostrarAjax();

    slctGlobal.listarSlct('ruta_detalle','slct_area2_id','simple');
    slctGlobalHtml('slct_tipo_respuesta,#slct_tipo_respuesta_detalle','simple');

    $("#btn_guardar_todo").click(guardarTodo);
    Bandeja.FechaActual(hora);

    $('#expedienteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var text = $.trim( button.data('text') );
      var id= $.trim( button.data('id') );
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      
    });

    $('#expedienteModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      $("#form_expediente input[type='hidden']").remove();
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });

    $('#retornarModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var text = $.trim( button.data('text') );
      var id= $.trim( button.data('id') );
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      
    });

    $('#retornarModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body .form-control').val('');
    });

    $("#txt_fecha_inicio_c").datetimepicker({
            format: "yyyy-mm-dd",
            language: 'es',
            showMeridian: false,
            time:false,
            minView:2,
            startView:2,
            autoclose: true,
            todayBtn: false
        });
    // --
    $(document).on('click', '.btnDeleteitem', function (event) {
            $(this).parent().parent().remove();
    });
    // --
});

ActualizarResponsable=function(val){
    if( $.trim( $("#slct_persona").attr("data-id") )!='' ){
        var data={persona_id:val,ruta_detalle_id:$("#slct_persona").attr("data-id")};
        var ruta='asignacion/responsable';
        Bandeja.AsignacionPersonas(data,ruta);
    }
    else{
        alert('.::No cuenta con cronograma::.');
        $("#slct_persona").val('');
        $('#slct_persona').multiselect('rebuild');
    }
}

ActualizarPersona=function(t){
    var data={persona_id:$(t).val(),ruta_detalle_verbo_id:$(t).attr("data-id")};
    var ruta='asignacion/persona';
    Bandeja.AsignacionPersonas(data,ruta);
}

Close=function(todo){
    $("#bandeja_detalle").hide();
    $("#txt_observacion").val("");
    if ( typeof(todo)!='undefined' ){
        $("#txt_id_ant,#txt_id_union").val("");
    }
}
Close_ruta=function(){
    $("#form_ruta_flujo .form-group").css("display","none");
}

Limpiar=function(text){
    $("#"+text).val("");
}

MostrarAjax=function(){
    Close();
    Bandeja.MostrarAjax();
}

hora=function(){

    //Bandeja.FechaActual();
    /*tiempo=horaTG.split(":");
    tiempo[1]=tiempo[1]*1+1;
    if(tiempo[1]*1==60){
        tiempo[0]=tiempo[0]*1+1;
        tiempo[1]='0';
    }

    if(tiempo[0]*1<10){
    tiempo[0] = "0" + tiempo[0]*1;
    }

    if(tiempo[1]*1<10){
    tiempo[1] = "0" + tiempo[1]*1;
    }*/

    var currentdate = new Date(); 
    var fechaTG = currentdate.getFullYear() + "-"
                + ('00' + (currentdate.getMonth()+1)).slice(-2)  + "-" 
                + ('00' + currentdate.getDate()).slice(-2);
    
    var horaTG = ('00' + currentdate.getHours()).slice(-2) + ":"  
                + ('00' + currentdate.getMinutes()).slice(-2) + ":" 
                + ('00' + currentdate.getSeconds()).slice(-2);
    $("#txt_respuesta").val(fechaTG+" "+horaTG);
    $("#div_cumple>span").html("CUMPLIENDO TIEMPO");
    $("#txt_alerta").val("0");
    $("#txt_alerta_tipo").val("0");

    $("#div_cumple").removeClass("progress-bar-danger").removeClass("progress-bar-warning").addClass("progress-bar-success");
        
    if ( $("#txt_fecha_max").val() < $("#txt_respuesta").val() ) {
        $("#txt_alerta").val("1");
        $("#txt_alerta_tipo").val("1");
        $("#div_cumple").removeClass("progress-bar-success").removeClass("progress-bar-warning").addClass("progress-bar-danger");
        $("#div_cumple>span").html("NO CUMPLE TIEMPO");
    }
    TiempoFinalTG = setTimeout('hora()',1000);
}

activar=function(id,ruta_detalle_id,td,ruta_id=''){//establecer como visto
    var tr = td;
    $(tr).attr('onClick','desactivar('+id+','+ruta_detalle_id+',this,'+ruta_id+')');
    $(tr).removeClass('unread');
    $(tr).find('i').removeClass('fa-ban').addClass('fa-eye');

    Bandeja.CambiarEstado(ruta_detalle_id, id,1);
    //tambien debera cargar un detalle en la parte de abajo
    desactivar(id,ruta_detalle_id,td,ruta_id);
};

desactivar=function(id,ruta_detalle_id,td,rutaid = ''){//establecer como no visto
    var tr = td;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    
    $(tr).attr("style","background-color:#9CD9DE;");

    var data ={ruta_detalle_id:ruta_detalle_id};
    mostrarDetallle(ruta_detalle_id,rutaid);
};

validacheck=function(val,idcheck){
    var verboaux="";
    var validacheck=0;
    var usuario=idcheck.split("_")[3];
    if( usuario=='' || usuario=='0' || ( usuario!='' && usuario==UsuarioId ) || ( RolIdG==8 || RolIdG==9 ) ){
        if( val>0 ){
            $("#t_detalle_verbo input[type='checkbox']").removeAttr('disabled');
        }
        disabled=false;
        $("#t_detalle_verbo input[type='checkbox']").each(
            function( index ) { 
                if ( val>0 && $(this).is(':checked') && $(this).attr("class")=='check'+val ) {
                    disabled=true;
                }
                    verboaux+= "|"+$(this).val();
                    if( val>0 && $(this).attr("class")!='check0' && $(this).attr("class")!='check'+val ){
                        $(this).attr("disabled","true");
                        $(this).removeAttr("checked");
                    }
            }
        );

        if(disabled==false && val>0){
            $("#t_detalle_verbo input[type='checkbox']").removeAttr('disabled');
        }
    }
    else{
        $("#"+idcheck).removeAttr("checked");
        alert(".::Ud no cuenta con permisos para realizar esta tarea::.");
    }

    $("#t_detalle_verbo input[type='checkbox']").each( function(){ 
        if( !$(this).is(':checked') ){
            validacheck++;
        }
    })
    $('#txt_observacion').removeAttr('disabled');
    if(validacheck>0){
        $('#txt_observacion').val('');
        $('#txt_observacion').attr('disabled','true');
    }
}


mostrarDetallle=function(id,rtid = ''){ //OK
    $("#form_ruta_detalle>.form-group").css("display","");
    $("#form_ruta_detalle>#ruta_detalle_id").remove();
    $("#form_ruta_detalle").append("<input type='hidden' id='ruta_detalle_id' name='ruta_detalle_id' value='"+id+"'>");
    $("#form_ruta_detalle>#ruta_id").remove();
    $("#form_ruta_detalle").append("<input type='hidden' id='ruta_id' name='ruta_id' value='"+rtid+"'>");
    var datos={ruta_detalle_id:id};
    Validar.mostrarDetalle(datos,mostrarDetalleHTML);
}

mostrarDetalleHTML=function(datos){
    fechaAux="";
    var data={ flujo_id:datos.flujo_id, estado:1,fecha_inicio:datos.fecha_inicio }
    var ids = [];
    $('#slct_tipo_respuesta,#slct_tipo_respuesta_detalle').multiselect('destroy');
    
    /*add new ruta detalle verbo*/
    var filtro={estado:1};
    slctGlobal.listarSlct2('documento','cbotipoDoc',filtro);
    slctGlobal.listarSlct2('rol','cboRoles',filtro);
    ruta_flujo_id2 = datos.ruta_flujo_id;
    if( $.trim(datos.ruta_flujo_id_dep) != '' ){
        ruta_flujo_id2 = datos.ruta_flujo_id_dep;
    }
    var data = {area_id: datos.area_id, ruta_flujo_id2: ruta_flujo_id2, ruta_flujo_id: datos.ruta_flujo_id, ruta_id: datos.ruta_id, clasificador_tramite_id: datos.clasificador_tramite_id, norden: datos.norden}
    Validar.mostrarCampos(data,mostrarCamposHTML);
    /*Bandeja.poblarCombo('documento','cbotipoDoc',filtro,HTMLCombo);
    Bandeja.poblarCombo('rol','cboRoles',filtro,HTMLCombo);*/
    /*add new ruta detalle verbo*/
    $('#btn_siguiente_rd').attr('onClick', 'asignarTramitePaso('+datos.ruta_id+');');
    // --
    if((!datos.rd_ruta_flujo_id * 1) > 0){
        var dataG={norden:datos.norden,ruta_id:datos.ruta_id};
        slctGlobal.listarSlctFuncion('ruta','listarmicro','slct_micro',null,null,dataG);
        $('#form_ruta_detalle #slct_micro').multiselect('destroy');
        $(".sectionmicro").css("display","");
        $("#btn_siguiente_rd").show().html('<i class="glyphicon glyphicon-check"></i>&nbsp;Activar Sub Proceso');
    }else{
        $("#btn_siguiente_rd").hide();
        $(".sectionmicro").css("display","none");
    }

    $("#RetornarP").show();
    if( datos.norden*1 == 1 ){
        $("#RetornarP").hide();
    }
    // --
    //***************************Mostrar archivado**********************************/
    /*if(datos.archivado==1){
        $(".sectionarchivado").css("display","");
        slctGlobalHtml('slct_archivado','simple');
        
    }else{
    }
    */
    $(".sectionarchivado").css("display","none");
    $('#slct_archivado').multiselect('destroy');

    $(".motivoretorno").hide();
    $("#motivo_retorno").text('');
    if( $.trim(datos.motivo_retorno) != '' ){
        $(".motivoretorno").show();
        $("#motivo_retorno").html("<ul><li>"+datos.motivo_retorno.split("|").join("</li></li>")+"</li></ul>");
    }

    //--
    /*ruta flujo id para visualizar la ruta */
    $("#VisualizarR").attr('ruta_flujo_id',datos.ruta_flujo_id);
    /*end ruta flujo id para visualizar la ruta*/

    /*puede regresar al paso anterior*/
 /*   var hora_fin_mayor =  new Date(datos.hora_fin_mayor);
    var hora_fin_menor =  new Date(datos.hora_fin_menor);
    var hora_actual = new Date(datos.fecha_actual);
    var hora_fecha_inicio = new Date(datos.fecha_inicio);
    if((hora_fecha_inicio.getHours() <= 13 && hora_fin_menor < hora_actual) || (hora_fecha_inicio.getHours() > 13 && hora_fin_mayor < hora_actual)){*/
    $("#RetornarP").removeClass('hidden');
   /* }else{
        $("#RetornarP").addClass('hidden');
    }*/
    /*fin puede regresar al paso anterior*/

    if( RolIdG==8 || RolIdG==9 ){
        $("#slct_persona").attr("data-id",datos.id); //carta_deglose_id
        $("#slct_persona").val('');
        $('#slct_persona').multiselect('rebuild');
        $("#slct_persona").val(datos.persona_responsable_id);
        $('#slct_persona').multiselect('rebuild');
    }
    else{
        $("#slct_persona").html(datos.persona_responsable);
    }
    //$('#slct_tipo_respuesta,#slct_tipo_respuesta_detalle').attr('disabled',"true");
    //slctGlobal.listarSlct('tiporespuesta','slct_tipo_respuesta','simple',ids,data,0,'#slct_tipo_respuesta_detalle','TR');
    //slctGlobal.listarSlct('tiporespuestadetalle','slct_tipo_respuesta_detalle','simple',ids,data,1);
    $("#form_ruta_detalle [data-target='#expedienteModal']").attr("data-id",datos.id_tr);
    
    $("#form_ruta_detalle #txt_fecha_tramite").val(datos.fecha_tramite);
    $("#form_ruta_detalle #txt_sumilla").val(datos.sumilla);
    $("#form_ruta_detalle #txt_solicitante").val(datos.solicitante);

    $("#form_ruta_detalle #txt_flujo").val(datos.flujo);
    $("#form_ruta_detalle #txt_area").val(datos.area);
    $("#form_ruta_detalle #txt_local").val(datos.local);
    $("#form_ruta_detalle #txt_local_origen").val(datos.local_origen);
    $("#form_ruta_detalle #txt_id_doc").val(datos.id_doc);
    $("#form_ruta_detalle #txt_orden").val(datos.norden);
    $("#form_ruta_detalle #txt_fecha_inicio").val(datos.fecha_inicio);
    $("#form_ruta_detalle #txt_tiempo").val(datos.tiempo);

    $(".solicitantesimple, .solicitantemultiple, .observaciones").hide();
    if( $.trim(datos.tramite_id) != '' ){
        $(".solicitantemultiple").show();
        var data = {tramite_id: datos.tramite_id};
        Validar.mostrarSolicitantes(data,mostrarSolicitantesHTML);
    }
    else{
        $(".solicitantesimple").show();
    }

    if( $.trim(datos.ruta_detalle_id_ant) != '' ){
        $(".observaciones").show();
        var data = {ruta_detalle_id: datos.ruta_detalle_id_ant};
        Validar.mostrarObservaciones(data,mostrarObservacionesHTML);
    }
    
    $("#ptra_tipo_solicitante").text(datos.tipo_solicitante);
    $("#ptra_id_solicitante").text(datos.id_solicitante);
    $("#ptra_solicitante").text(datos.solicitante);
    $("#ptra_tel_solicitante").text(datos.tel_solicitante);
    $("#ptra_email_solicitante").text(datos.email_solicitante);
    $("#ptra_dir_solicitante").text(datos.dir_solicitante);
    
    

    $("#form_ruta_detalle>#txt_fecha_max").remove();
    $("#form_ruta_detalle").append("<input type='hidden' id='txt_fecha_max' name='txt_fecha_max' value='"+datos.fecha_max+"'>");


    // FOTOS PROCESO DESMONTE:
    AgregarD = function (obj) {
        var tabla=obj.parentNode.parentNode.parentNode.parentNode;
        var html = '';
        html += "<tr>";
        html += "<td>";
        html += '<input type="text"  readOnly class="form-control input-sm" id="pago_nombre"  name="pago_nombre[]" value="">' +
                '<input type="text"  style="display: none;" id="pago_archivo" name="pago_archivo[]">' +
                '<label class="btn btn-default btn-flat margin btn-xs">' +
                '<i class="fa fa-file-pdf-o fa-lg"></i>' +
                '<i class="fa fa-file-word-o fa-lg"></i>' +
                '<i class="fa fa-file-image-o fa-lg"></i>' +
                '<input type="file" style="display: none;" onchange="onPagos(event,this);" >' +
                '</label>';
        html += "</td>" +
                '<td><a id="btnDeleteitem"  name="btnDeleteitem" class="btn btn-danger btn-xs btnDeleteitem">' +
                '<i class="fa fa-trash fa-lg"></i>' +
                '</a></td>';
        html += "</tr>";
        $(tabla).find("tbody").append(html);
    }    

    onPagos = function (event,obj) {
        var tr=obj.parentNode.parentNode;
       console.log(tr);
        var files = event.target.files || event.dataTransfer.files;
        if (!files.length)
            return;
        var image = new Image();
        var reader = new FileReader();
        reader.onload = (e) => {
            $(tr).find('input:eq(1)').val(e.target.result);
        };
        reader.readAsDataURL(files[0]);
        $(tr).find('input:eq(0)').val(files[0].name);
        console.log(files[0].name);
    }
    /*
    html_pd = '';
    var foto = ''
    var data_fotos = $.trim(datos.archivo).split("|");
    $.each(data_fotos, function (index, d_foto) {
        if (d_foto.length != 0) {
            var cant_foto = d_foto.length;

            if(d_foto.substring((cant_foto-3), cant_foto) == 'png' || 
                d_foto.substring((cant_foto-3), cant_foto) == 'jpg' ||
                d_foto.substring((cant_foto-3), cant_foto) == 'gif' ||
                d_foto.substring((cant_foto-4), cant_foto) == 'jpeg' )
                foto = d_foto;
            else
                foto = 'img/admin/ruta_detalle/marca_doc.jpg';

            html_pd += '<div class="col-md-1" id="ad'+index+'" style="padding-left: 0px; padding-right: 10px;">'+
                            '<a href="'+d_foto+'" target="_blank"><img src="'+foto+'" alt=""  border="0" class="img-responsive foto_desmonte"></a>'+
                            '<div class="text-center"><button type="button" id="'+index+'" onclick="eliminarArchivoDes(this.id)" class="btn btn-danger btn-xs"><span class="fa fa-trash fa-lg" aria-hidden="true"></span> Eliminar</button></div>'+
                        '</div>';
        }
    });
    */
    $("#d_ver_fotos").html('');
    $.ajax({
        url: 'ruta_detalle/verarchivosdesmontesmotorizado',
        type:'POST',
        cache       : false,
        dataType    : 'json',
        data        : { ruta_id:datos.ruta_id, norden:datos.norden },
        success: function(obj)
        {
            datos = obj.datos;
            var html_pd = '';
            var foto = '';

            $.each(datos, function (index, data) {
                var d_foto = data.archivo;
                //alert(d_foto.length);                
                if (($.trim(d_foto.length) * 1) > 0) {

                    var data_fotos = $.trim(data.archivo).split("|");

                    $.each(data_fotos, function (index, d_foto) {
                        var cant_foto = d_foto.length;
                        if(cant_foto != 0)
                        {
                            if(d_foto.substring((cant_foto-3), cant_foto) == 'png' || 
                                d_foto.substring((cant_foto-3), cant_foto) == 'jpg' ||
                                d_foto.substring((cant_foto-3), cant_foto) == 'gif' ||
                                d_foto.substring((cant_foto-4), cant_foto) == 'jpeg' )
                                foto = d_foto;
                            else if ( d_foto.substring((cant_foto-3), cant_foto) == 'xls' || d_foto.substring((cant_foto-4), cant_foto) == 'xlsx' )
                                foto = 'Config/excel.jpg';
                            else if ( d_foto.substring((cant_foto-3), cant_foto) == 'pdf' )
                                foto = 'Config/pdf.jpg';
                            else if ( d_foto.substring((cant_foto-3), cant_foto) == 'doc' || d_foto.substring((cant_foto-4), cant_foto) == 'docx' )
                                foto = 'Config/word.png';
                            else if ( d_foto.substring((cant_foto-3), cant_foto) == 'ppt' || d_foto.substring((cant_foto-4), cant_foto) == 'pptx' )
                                foto = 'Config/ppt.png';
                            else if ( d_foto.substring((cant_foto-3), cant_foto) == 'txt' )
                                foto = 'Config/txt.jpg';
                            else
                                foto = 'Config/default.png';

                            html_pd += '<div class="col-md-1" id="ad'+index+'" style="padding-left: 0px; padding-right: 10px;">'+
                                            '<a href="'+d_foto+'" target="_blank"><img src="'+foto+'" alt=""  border="0" class="img-responsive foto_desmonte" style="height: 120px !important;"></a>';
                            
                            if($('#txt_orden').val() == data.norden)
                                html_pd += '<div class="text-center"><button type="button" id="'+index+'" onclick="eliminarArchivoDes(this.id, \''+foto+'\')" class="btn btn-danger btn-xs"><span class="fa fa-trash fa-lg" aria-hidden="true"></span> Eliminar</button></div>';
                                        
                            html_pd += '</div>';
                        }
                    });
                    $("#d_ver_fotos").html(html_pd);
                }
            });
            //$("#d_ver_fotos").html(html_pd);       
        },
        error: function(jqXHR, textStatus, error)
        {
          console.log(jqXHR.responseText);
        }
    });

    
    // --

  /*  $("#t_detalle_verbo").html("");*/
    var detalle="";
    var html="";
    var imagen="";
    var obs="";
    var cod="";
    var rol="";
    var verbo="";
    var documento="";
    var orden="";
    var archivo="";

    $("#t_detalle_verbo").html("");
        if ( datos.verbo!='' ) {
            detalle=datos.verbo.split("|");
            html="";
            referidos = 0;
            for (var i = 0; i < detalle.length; i++) {

                imagen = "<i class='fa fa-check fa-lg'></i>";
                imagenadd = "<ul><li>"+detalle[i].split("=>")[4].split("^").join("</li><li>")+"</li></ul>";
                obs = detalle[i].split("=>")[5];

                rol = detalle[i].split("=>")[6];
                verbo = detalle[i].split("=>")[7];
                documento = detalle[i].split("=>")[8];
                rol_id= detalle[i].split("=>")[14];

                if(detalle[i].split("=>")[13] == 1 && detalle[i].split("=>")[2]=="Pendiente" && (RolIdG==8 || RolIdG==9)){
                    orden = '<span id="btnDelete" name="btnDelete" class="btn btn-danger  btn-xs btnDelete" onclick="eliminardv('+detalle[i].split("=>")[0]+')"><i class="glyphicon glyphicon-trash"></i></span>';
                }else{
                    orden = detalle[i].split("=>")[9];
                }
                
                archivo="";
                denegar=false;

                persona=detalle[i].split("=>")[10];
                fecha ='';
                if( detalle[i].split("=>")[2]!="Pendiente" ){
                    fecha=detalle[i].split("=>")[11];
                }
                else if( detalle[i].split("=>")[2]=="Pendiente" && (RolIdG==8 || RolIdG==9)){
                    persona="<select class='slcPersona' data-id='"+detalle[i].split("=>")[0]+"' onChange='ActualizarPersona(this);'>"+$("#slct_persona").html()+"</select>";
                }

                if(detalle[i].split("=>")[2]=="Pendiente"){
                    if(detalle[i].split("=>")[3]=="NO"){
                        valorenviado=0;
                    }
                    else{
                        valorenviado=detalle[i].split("=>")[3]*1;
                    }

                    if( datos.maximo!=0 && valorenviado!=0 && valorenviado!=datos.maximo ){
                        denegar=true;
                    }
                    /*imagenadd=  '<div class="input-group success">'+
                                '   <div class="input-group-addon btn btn-success" onclick="adicionaDetalleVerbo('+detalle[i].split("=>")[0]+');">'+
                                '       <i class="fa fa-plus fa-lg"></i>'+
                                '   </div>'+
                                '   <input type="text" class="txt'+valorenviado+' txt_'+detalle[i].split("=>")[0]+'" data-inputmask="'+"'alias'"+': '+"'email'"+'" data-mask/>'+
                                '</div>';*/
                    if(denegar==true){
                        imagen="";
                    }
                    else{

                        obs = "<textarea class='form-control area"+valorenviado+"' name='area_"+detalle[i].split("=>")[0]+"' id='area_"+detalle[i].split("=>")[0]+"'></textarea>";
                        //imagen="<input type='checkbox' class='check"+valorenviado+"' onChange='validacheck("+valorenviado+",this.id);' value='"+detalle[i].split("=>")[0]+"' name='chk_verbo_"+detalle[i].split("=>")[0]+"' id='chk_verbo_"+detalle[i].split("=>")[0]+"_"+$.trim(detalle[i].split("=>")[12])+"'>";
                        imagen='<div class="checkbox">'+
                                    '<label style="font-size: 1.5em">'+
                                        '<input class="check'+valorenviado+'" onChange="validacheck('+valorenviado+',this.id);" type="checkbox" value="'+detalle[i].split("=>")[0]+'" name="chk_verbo_'+detalle[i].split("=>")[0]+'" id="chk_verbo_'+detalle[i].split("=>")[0]+'_'+$.trim(detalle[i].split("=>")[12])+'">'+
                                        '<span class="cr" style="background-color: #fff;"><i class="cr-icon fa fa-check"></i></span>'+                                
                                    '</label>'+
                                '</div>';
                        
                        imagenadd= '<input disabled type="text" class="form-control txt'+valorenviado+' txt_'+detalle[i].split("=>")[0]+'"/>';
                        if(verbo=="Generar"){
                            imagenadd= '<input data-pos="'+(i*1+1)+'" type="text" readonly class="form-control txt'+valorenviado+' txt_'+detalle[i].split("=>")[0]+'" id="documento_'+detalle[i].split("=>")[0]+'" name="documento_'+detalle[i].split("=>")[0]+'" value="" />'+
                                        '<input type="hidden" id="txt_documento_id_'+detalle[i].split("=>")[0]+'" name="txt_documento_id_'+detalle[i].split("=>")[0]+'" value="">'+
                                        '<input type="hidden" id="txt_doc_digital_id_'+detalle[i].split("=>")[0]+'" name="txt_doc_digital_id_'+detalle[i].split("=>")[0]+'" value="">'+

                                            '<span class="btn btn-success" onclick="RegistraridsDelBoton('+detalle[i].split("=>")[0]+')" data-toggle="modal" data-target="#listDocDigital" id="btn_list_digital" data-texto="documento_'+detalle[i].split("=>")[0]+'" data-id="txt_doc_digital_id_'+detalle[i].split("=>")[0]+'"><i class="glyphicon glyphicon-file"></i></span>'+
                                            //'<span class="btn btn-success" data-toggle="modal" data-target="#NuevoDocDigital" id="btn_nuevo_docdigital" data-texto="documento_'+detalle[i].split("=>")[0]+'" data-id="txt_doc_digital_id_'+detalle[i].split("=>")[0]+'"><i class="glyphicon glyphicon-paperclip"></i></span>'+


                                        /*'<span class="btn btn-primary" data-toggle="modal" data-target="#indedocsModal" data-texto="documento_'+detalle[i].split("=>")[0]+'" data-id="txt_documento_id_'+detalle[i].split("=>")[0]+'" id="btn_buscar_indedocs">'+
                                            '<i class="fa fa-search fa-lg"></i>'+
                                         '</span>'+*/
                                         '<span class="btn btn-warning" onClick="Liberar(\'documento_'+detalle[i].split("=>")[0]+'\')" >'+
                                            '<i class="fa fa-pencil fa-lg"></i>'+
                                         '</span>';
                            archivo='<input class="form-control" id="archivo_'+detalle[i].split("=>")[0]+'" name="archivo_'+detalle[i].split("=>")[0]+'" type="file">';
                        }
                    }
                }


                if(verbo=="Generar"){
                    referidos+=1;
                    html+= "<tr class='referidos' count="+referidos+">";
                }else{
                    html+= "<tr>";
                }

                html+=    "<td style='vertical-align : middle;'>"+orden+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+detalle[i].split("=>")[3]+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+rol+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+verbo+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+documento+"</td>";
                //html+=    "<td style='vertical-align : middle;'>"+detalle[i].split("=>")[1]+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+detalle[i].split("=>")[1]+"</td>";
                html+=    "<td style='vertical-align : middle;' id='td_"+detalle[i].split("=>")[0]+"'>"+imagenadd+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+obs+"</td>";
                            //"<td>"+archivo+"</td>"+
                html+=    "<td style='vertical-align : middle;'>"+persona+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+fecha+"</td>";
                html+=    "<td style='vertical-align : middle;'>"+imagen+"</td>";
                html+= "</tr>";
                $("#t_detalle_verbo").append(html);
                html = "";
               /* if( $.trim( detalle[i].split("=>")[12] )!='' && (RolIdG==8 || RolIdG==9) ){
                    $("#t_detalle_verbo select[data-id='"+detalle[i].split("=>")[0]+"'] option[value='"+detalle[i].split("=>")[12]+"']").attr("selected",true);
                }*/
                if($.trim( detalle[i].split("=>")[15] )!='' && (RolIdG==8 || RolIdG==9)){
                    $("#t_detalle_verbo select[data-id='"+detalle[i].split("=>")[0]+"'] option[value='"+detalle[i].split("=>")[15]+"']").attr('selected',true);
                 //   console.log($("#t_detalle_verbo select[data-id='"+detalle[i].split("=>")[0]+"'] option[value='"+detalle[i].split("=>")[15]+"']").html());
                //    console.log(detalle[i].split("=>")[0]);
                 //   console.log('hola2');                   
                }
            }

            /*last referido*/
            last_ref =0;
            $('#t_detalle_verbo tr[class=referidos]').each(function () {
                 last_ref=$(this).attr('count');
            });
            $('#t_detalle_verbo .referidos[count='+last_ref+']').addClass('danger');
            $('#t_detalle_verbo .referidos[count='+last_ref+']').addClass('referidoSelect');
            /*end last referido */
        }
    hora();
    $("#txt_observacion").attr('disabled','true');

}

mostrarSolicitantesHTML = (result) => {
    if( result.length == 0 ){
        $(".solicitantesimple").show();
        $(".solicitantemultiple").hide();
    }
    else{
        html = '';
        $("#t_usuarios").dataTable().fnDestroy();
        $("#tb_usuarios").html('');
        $.each(result,function(index,r){
            html =  '<tr>'+
                        '<td>'+ $.trim(r.tipo_solicitante) +'</td>'+
                        '<td>'+ $.trim(r.solicitante) +'</td>'+
                        '<td>'+ $.trim(r.id_solicitante) +'</td>'+
                        '<td>'+ $.trim(r.tel_solicitante) +'</td>'+
                        '<td>'+ $.trim(r.email_solicitante) +'</td>'+
                        '<td>'+ $.trim(r.dir_solicitante) +'</td>'+
                    '</tr>';
            $("#tb_usuarios").append(html);
        });
        $("#t_usuarios").dataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": true,
            "searching": false,
        });
    }
}

mostrarObservacionesHTML = (result) => {
    if( result.length == 0 ){
        $(".observaciones").hide();
    }
    else{
        html = '';
        $("#t_observaciones").dataTable().fnDestroy();
        $("#tb_observaciones").html('');
        $.each(result,function(index,r){
            html =  '<tr>'+
                        '<td>'+ $.trim(r.verbo) +'</td>'+
                        '<td>'+ $.trim(r.tipo_documento) +'</td>'+
                        '<td>'+ $.trim(r.documento) +'</td>'+
                        '<td>'+ $.trim(r.observacion) +'</td>'+
                        '<td>'+ $.trim(r.usuario) +'</td>'+
                        '<td>'+ $.trim(r.fecha_observacion) +'</td>'+
                    '</tr>';
            $("#tb_observaciones").append(html);
        });
        $("#t_observaciones").dataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": true,
            "searching": false,
        });
    }
}

mostrarCamposHTML = (result) => {
    $(".DatosPersonalizadosG").addClass('hidden');
    $(".DatosPersonalizadosG .box-body").html('');
    
    ClickActCamp = true; //Inicializa en positivo
    $.each(result,function(index,r){
        campo = '';
        subtitulo = '';
        color = 'error';
        icono = 'remove';
        html = '';
        lista = $.trim( r.lista ).split("*");
        campo_valor = $.trim(r.campo_valor);
        ruta_campo_id = $.trim(r.ruta_campo_id);
        fecha = '';
        if( ruta_campo_id == '' ){
            ruta_campo_id = 0;
        }

        if( index == 0 ){
            $(".DatosPersonalizadosG").removeClass('hidden');
        }

        if( r.tipo == 0 ){
            campo = r.campo.split("/")[0];
            sub_titulo = r.campo.split("/")[1];
            col = 12;
        }
        
        if( r.tipo == 0 ){
            html = 
                '<div class="col-sm-12 bg-info" style="margin: 10px 0px 10px 0px">'+
                    '<h5 class="text-center"><b>'+ campo +'</b> '+
                        '<small style="color:red">'+ sub_titulo +'</small>'+
                    '</h5>'+
                    '<hr style="border:dotted;">'+
                '</div>';
        }
        else{
            campogenerado = '<input type="text" class="form-control" value="'+ campo_valor +'" disabled>';
            ruta_flujo_campo_id = '';
            ruta_campo = '';

            if( r.modificar == 1 ){
                ruta_flujo_campo_id = '<input type="hidden" name="ruta_flujo_campo_id[]" value="'+ r.id +'">';
                ruta_campo = '<input type="hidden" id="ruta_campo_id'+ r.id +'" name="ruta_campo_id[]" value="'+ ruta_campo_id +'">';
                if( r.obligar == 0 ){
                    color = 'warning';
                    icono = 'warning-sign';
                }

                if( r.tipo != 6 ){
                    
                    onKey = ''; readOnly = ''; fecha  = ''; formatoFecha = ''; minView= 3; maxView= 4; startView= 3;

                    if( campo_valor != '' ){
                        color = 'success';
                        icono = 'ok';
                    }
                    
                    if( r.tipo == 1 ){
                        onKey = ' onKeyUp="masterG.validaEmailEvento(this, '+ r.capacidad +', cambiarColor)" ';
                    }
                    else if( r.tipo == 2 ){
                        onKey = ' onKeyPress="return masterG.validaDecimal(event, this)" ';
                        onKey += ' onKeyUp="masterG.validaDecimalMaxEvento(this, '+ r.capacidad +', cambiarColor)" ';
                    }
                    else if( r.tipo >= 3 && r.tipo <= 5 ){
                        fecha = 'fecha'; 
                        readOnly = 'readonly';
                        if( r.tipo == 3 ){
                            formatoFecha = 'yyyy-mm-dd';
                            minView= 2;
                            maxView= 4;
                            startView= 2;
                        }
                        else if( r.tipo == 4 ){
                            formatoFecha = 'yyyy-mm';
                            minView= 3;
                            maxView= 4;
                            startView= 3;
                        }
                        else if( r.tipo == 5 ){
                            formatoFecha = 'yyyy';
                            minView= 4;
                            maxView= 4;
                            startView= 4;
                        }
                        onKey = ' onChange="masterG.validaDatosEvento(this, cambiarColor)" ';
                    }
                    else if( r.tipo == 7 ){
                        onKey = ' onKeyPress="return masterG.validaNumerosMax(event, this, '+ r.capacidad +')" ';
                        onKey += ' onKeyUp="masterG.validaDatosEvento(this, cambiarColor)" ';
                    }
                    else if( r.tipo == 8 ){
                        //onKey = ' onKeyPress="return masterG.validaLetras(event, this, '+ r.capacidad +')" ';
                        onKey = ' onKeyUp="masterG.validaDatosEvento(this, cambiarColor)" ';
                    }
                    campogenerado = 
                            '<div id="campo'+ r.id +'" class="has-'+ color +' has-feedback">'+
                                '<input type="text" class="form-control '+ fecha +'" name="campo_valor[]" value="'+ campo_valor +'"'+ onKey + readOnly +
                                    ' data-id="'+ r.id +'"'+
                                    ' data-capacidad="'+ r.capacidad +'"' +
                                    ' data-obligar="'+ r.obligar +'"' +
                                    ' data-campo="'+ r.campo +'"' +
                                '>'+
                                '<span data-id="'+ r.id +'" class="glyphicon glyphicon-'+ icono +' form-control-feedback"></span>'+
                            '</div>';
                }
                else{
                    options = '';
                    for (let i = 0; i < lista.length; i++) {
                        selected = '';
                        if( campo_valor != '' && campo_valor == lista[i] ){
                            selected = 'selected';
                            color = 'success';
                            icono = 'ok';
                        }
                        options+='<option value="'+ lista[i] +'" '+ selected +'>'+ lista[i] +'</option>';
                    }

                    onKey = ' onChange="masterG.validaDatosEvento(this, cambiarColor)" ';

                    campogenerado = 
                        '<div id="campo'+ r.id +'" class="form-group has-'+ color +' has-feedback">'+
                            '<select class="form-control" name="campo_valor[]" '+ onKey +
                            ' data-id="'+ r.id +'"'+
                            ' data-capacidad="'+ r.capacidad +'"' +
                            ' data-obligar="'+ r.obligar +'"' +
                            ' data-campo="'+ r.campo +'"' +
                        '>'+
                                '<option value=""> .::Seleccione::. </option>'+
                                options +
                            '</select>'+
                            '<span data-id="'+ r.id +'" class="glyphicon glyphicon-'+ icono +' form-control-feedback"></span>'+
                        '</div>';
                }

                if( color == 'error' ){
                    ClickActCamp = false;
                }
            }

            html =
                '<div class="col-sm-'+ r.col +'">'+
                    ruta_flujo_campo_id +
                    ruta_campo +
                    '<label>'+ r.campo +':</label>'+
                    campogenerado
                '</div>';
        }
        
        $(".DatosPersonalizadosG .box-body").append(html);

        if( fecha != '' ){
            $("#campo"+ r.id +" .fecha").datetimepicker({
                format: formatoFecha,
                language: 'es',
                showMeridian: false,
                time: false,
                minView: minView,
                maxView: maxView,
                startView: startView, // 1->hora, 2->dia , 3->mes
                autoclose: true,
                todayBtn: false
            });
        }
        
    });
}

ActualizarCampos = () => {
        r = true;
    $(".DatosPersonalizadosG .glyphicon-remove").each( function(index){
        id = this.dataset.id;
        campo = $("#campo"+ id +" .form-control").attr("data-campo");
        if( r == true ){
            msjG.mensaje("warning","Se requiere dato del campo: "+ campo,5000);
            $("#campo"+ id +" .form-control").focus();
        }
        r = false;
    });

    if( r == true ){
        ClickActCamp = true;
        Validar.guardarRutaCampos(guardarRutaCamposHTML);
    }

}

guardarRutaCamposHTML = (result) => {
    $.each( result.ruta_flujo_campo_id ,function(index, el) {
        $("#ruta_campo_id" + el).val( result.ruta_campo_id[index] );
    });
}

cambiarColor = (t, estado)=>{
    id = t.dataset.id;
    obligar = t.dataset.obligar;
    $("#campo"+id).removeClass('has-error').removeClass('has-success').removeClass('has-warning');
    $("#campo"+id+" span").removeClass('glyphicon-remove').removeClass('glyphicon-ok').removeClass('glyphicon-warning-sign');
    if ( estado ) {
        $("#campo"+id).addClass('has-success');
        $("#campo"+id+" span").addClass('glyphicon-ok');
    }
    else{
        if( t.value == '' && obligar == 0 ){
            $("#campo"+id).addClass('has-warning');
            $("#campo"+id+" span").addClass('glyphicon-warning-sign');
        }
        else{
            $("#campo"+id).addClass('has-error');
            $("#campo"+id+" span").addClass('glyphicon-remove');
        }
    }
}

Liberar=function(txt){
    $("#"+txt).removeAttr("readonly");
    $("#"+txt).val("");
    $("#txt_documento_id_"+txt.split("_")[1]).val('');
    $("#"+txt).focus();
}

guardarTodo=function(){
    var verboaux="";
    var codaux="";
    var obsaux="";
    var coddocaux="";
    var coddocdig = "";
    var contcheck=0;
    var conttotalcheck=0;
    var alerta=false;
    var codauxd="";
    var validacheck=0;

    
    if( ( $(".DatosPersonalizadosG .glyphicon-remove").length > 0 && alerta == false ) || ClickActCamp == false ){
        msjG.mensaje("warning","Registre los datos que contienen una 'X' en DATOS PERSONALIZADOS y/o presione el botón actualizar para validar su registro",8000);
        $(".DatosPersonalizadosG #datos_personalizados").focus();
        alerta = true;
    }

    $("#t_detalle_verbo input[type='checkbox']").each(
        function( index ) { 
            if ( $(this).is(':checked') && alerta==false ) {
                codauxd="";
                $("#td_"+$(this).val()+" input[type='text']").each(
                    function( indx ) {
                        if( $(this).val()!="" ){
                            codauxd+="^"+$(this).val();
                        }
                    }
                );

               // console.log($(this).val());
                verboaux+="|"+$(this).val();
                codaux+= "|"+codauxd.substr(1);
                obsaux+="|"+$("#area_"+$(this).val()).val();
                coddocaux+="|"+$("#txt_documento_id_"+$(this).val()).val();
                coddocdig+=(typeof $("#txt_doc_digital_id_"+$(this).val()).val()) != 'undefined' ? "|"+$("#txt_doc_digital_id_"+$(this).val()).val() : "|"+ "";
                contcheck++;

                if( $("#documento_"+$(this).val()).val()=="" ){
                    alert("Busque y Seleccione Nro del documento generado de la tarea "+$("#documento_"+$(this).val()).attr("data-pos"));
                    alerta=true;
                }
            }
            else if( !$(this).attr("disabled") ){
                validacheck=1;
            }
            conttotalcheck++;
        }
    );

    if(conttotalcheck>0){
        verboaux=verboaux.substr(1);
        var r_verbo = $(".referidoSelect").find('.check0').val();
        $("#form_ruta_detalle>#vreferido").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='vreferido' name='vreferido' value='"+r_verbo+"'>");

        $("#form_ruta_detalle>#verbog").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='verbog' name='verbog' value='"+verboaux+"'>");
        codaux=codaux.substr(1);
        $("#form_ruta_detalle>#codg").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='codg' name='codg' value='"+codaux+"'>");
        obsaux=obsaux.substr(1);
        $("#form_ruta_detalle>#obsg").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='obsg' name='obsg' value='"+obsaux+"'>");
        coddocaux=coddocaux.substr(1);
        $("#form_ruta_detalle>#coddocg").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='coddocg' name='coddocg' value='"+coddocaux+"'>");
        coddocdig=coddocdig.substr(1);
        $("#form_ruta_detalle>#coddocdig").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='coddocdig' name='coddocdig' value='"+coddocdig+"'>");
    }

    if( conttotalcheck>0 && contcheck==0 && alerta==false ) {
            alert("Seleccione al menos 1 tarea (check)");
    }
    /*else if ( $("#slct_tipo_respuesta").val()=='' && validacheck==0 && alerta==false ) {
        alert("Seleccione Tipo de Respuesta");
    }
    else if ( $("#slct_tipo_respuesta_detalle").val()=='' && validacheck==0 && alerta==false ) {
        alert("Seleccione Detalle Tipo Respuesta");
    }*/
    else if ( $("#txt_observacion").val()=='' && validacheck==0 && alerta==false ) {
        alert("Ingrese Descripción de la Actividad");
    }
    /*else if ( $("#slct_tipo_respuesta").val()!='' && validacheck==1 && alerta==false 
                && $("#slct_tipo_respuesta option[value='"+$("#slct_tipo_respuesta").val()+"']").attr("data-evento").split("_")[1]=='0'
            ) {
        alert("El tipo de respuesta seleccionada solo esta permitida cuando este activada todas las tareas habilitadas");
    }
    else if ( $("#slct_tipo_respuesta_detalle").val()=='' && $("#slct_tipo_respuesta").val()!='' ) {
        alert("Seleccione Detalle Tipo Respuesta");
    }
    else if ( $("#txt_observacion").val()!='' && $("#slct_tipo_respuesta").val()=='' ) {
        alert("La Descripción de respuesta de la Actividad, solo esta permitido cuando seleccione Tipo de respuesta");
    }*/
    else if( alerta==false ){
        /*if( confirm("Favor de confirmar para actualizar su información") ){
            if(validacheck==0 || $("#slct_tipo_respuesta").val()!=''){
                $('#slct_tipo_visualizacion').multiselect('deselectAll');
                $('#slct_tipo_visualizacion').multiselect('refresh');
                Validar.guardarValidacion();
            }
            else{
                Validar.guardarValidacion(mostrarDetallle,$("#form_ruta_detalle>#ruta_detalle_id").val() );
            }
        }*/
        var ultimo=Validar.VerificarUltimopaso({ruta_id:$('#ruta_id').val()});
        if(ultimo.rst==1){$("#form_ruta_detalle #txt_finalizado").val(2);}
        else{$("#form_ruta_detalle #txt_finalizado").val(0);}

        var msj = 'Por favor confirmar para actualizar su información';
        if( $("#btn_siguiente_rd").is(':visible') && $('#form_ruta_detalle #slct_micro option').length > 1 && alerta == false ){
           msj= "Tiene sub proceso sin seleccionar!, desea continuar de todas formas para actualizar su información?";
        }
        
        sweetalertG.confirm("¿Desea Continuar?", ultimo.msj+msj, function(){
            if(validacheck==0 || $("#slct_tipo_respuesta").val()!=''){
                $('#slct_tipo_visualizacion').multiselect('deselectAll');
                $('#slct_tipo_visualizacion').multiselect('refresh');
                Validar.guardarValidacion();
            }
            else{
                Validar.guardarValidacion(mostrarDetallle,$("#form_ruta_detalle>#ruta_detalle_id").val() );
            }
        });
    }
}

// ARCHIVOS PROCESO DESMONTE
guardarArhivoDesmonte = function(){
    var datos=$("#form_ruta_detalle").serialize().split("txt_").join("").split("slct_").join("").split("_modal").join("");
    Validar.guardarArhivoDesmonte(mostrarDetallle, $("#form_ruta_detalle>#ruta_detalle_id").val(), datos); 
    $("#tb_darchivo").html(''); 
}

eliminarArchivoDes = function(id, foto){    
    sweetalertG.confirm("¿Estás seguro?", "Confirme la eliminación de archivo.", function(){                     
       /*
       var archivos = '';
       $("#d_ver_fotos a").each(function(){
            archivos += $(this).attr('href')+'|';
        });
       */
       //alert(foto);
       Validar.eliminarArchivoDes($("#form_ruta_detalle>#ruta_detalle_id").val(), foto);
       $("#ad"+id).remove();
    });    
}
// --

eventoSlctGlobalSimple=function(slct,valores){
    if( slct=="slct_tipo_respuesta" ){
        var detval=valores.split("|").join("").split("_");
        fechaAux="";
        if ( detval[1]==1 ) {
        fechaAux=detval[2];
        }
    }
}

/*add new verb to generate*/
Addtr = function(e){
    e.preventDefault();
    var template = $("#tbldetalleverbo").find('.trNuevo').clone().removeClass('trNuevo').removeClass('hidden');
    $("#tbldetalleverbo tbody").append(template);
}
/*end add new verb to generate*/

/*delete tr*/
Deletetr = function(object){
    object.parentNode.parentNode.parentNode.remove();
}
/*end delete tr*/

/*poblate combo*/
HTMLCombo = function(obj,data){
    if(data){
         html='';
        $.each(data,function(index, el) {
            html+='<option value='+el.id+'>'+el.nombre+'</option>';
        });
        $('#'+obj).html(html);
    } 
}
/*end poblate combo */

/*save new ruta_detalle_verbo*/
saveVerbo = function(){
    var id_rutadverbo = document.querySelector("#ruta_detalle_id");
    var condional = 0;
    var rol = $("#t_detalle_verbo #cboRoles").val();
    var verbo = 1;
    var doc = $("#t_detalle_verbo #cbotipoDoc").val();
    var nomb = $("#t_detalle_verbo #txtdescripcion").val();

    var data = {
        'ruta_detalle_id':id_rutadverbo.value,
        'nombre':nomb,
        'documento':doc,
        'condicion':0,
        'rol_id':rol,
        'verbo_id':1,
        'adicional' : 1,
        'orden':0,
    };
    Bandeja.Guardarrdv(JSON.stringify(data),mostrarDetallle);

}
/*end save new ruta_detalle_verbo */

/*delete rdv*/
eliminardv = function(id){
    if(id){
        var r = confirm("¿Estas seguro de eliminar?");
        if(r == true){
            var id_rutadverbo = document.querySelector("#ruta_detalle_id");
            var data = {'ruta_detalle_id':id_rutadverbo.value,'ruta_detalle_verbo_id':id,};
            Bandeja.Deleterdv(JSON.stringify(data),mostrarDetallle);            
        }
    }
}
/*end delete rdv*/
 
expedienteUnico = function(){
    var rd_id=document.querySelector("#ruta_id").value;
    if(rd_id){
        Bandeja.ExpedienteUnico({'ruta_id':rd_id},HTMLExpedienteUnico);        
    }else{
        alert('Error');
    }
}

function HTMLExpedienteUnico(data){
    if(data.length > 0){
        $("#tb_tretable").html('');// inicializando
        //#75DDEC, #FFF3A2, #F58DD7
        var cab = [
            { 'id':'ER', 'nombre': 'Expediente donde he Referido', 'color': 'alert-info', 'icon': '<a class="text-aqua" href="#"><i class="fa fa-square"></i></a>' },
            { 'id':'EA', 'nombre': 'Expediente Actual', 'color': 'alert-warning', 'icon': '<a class="text-orange" href="#"><i class="fa fa-square"></i></a>' },
            { 'id':'EER', 'nombre': 'Expediente donde estoy Referido', 'color': 'alert-success', 'icon': '<a class="text-green" href="#"><i class="fa fa-square"></i></a>' }
        ];
        //TODO: Expedientes///////////////////////////////////////////////////////////////////////
        for (let i = 0; i < data.length; i++) {
            var html ='';
            var cont = 0;
            var last_ref = 0;
            var parent = 0; var child = 0; var aux_ref = '';
            var ruta_id = 0;
            var dd = '';
            var dd2 = '';
            var clase = '';
            if( data[i].length > 0 ){
                html="<tr data-id='"+cab[i].id+cont+"' style='cursor: zoom-in;'>";
                html+=    "<td class='col-md-12' data-column=name><i class='glyphicon glyphicon-chevron-right'></i>"+cab[i].nombre+cab[i].icon+"</td>";
                html+="</tr>";
                $("#tb_tretable").append(html);

                $.each(data[i],function(index, el) {
                    cont+=1;
                    parent = 0;child = 2;
                    clase = 'col-md-12';

                    referido = (el.referido !=null) ? el.referido : '';
                    fhora = (el.fecha_hora !=null) ? el.fecha_hora : '';
                    proc =(el.proceso !=null) ? el.proceso : '';
                    area =(el.area !=null) ? el.area : '';
                    nord =(el.norden !=null) ? el.norden : '';

                    if(el.doc_digital_id!=null){
                        referido += '<a class="btn btn-default btn-sm" href="doc_digital/'+el.doc_digital_id+'" target="_blank" data-titulo="Previsualizar"><i class="fa fa-eye fa-lg"></i> </a>';
                    }

                    if( el.ruta_id != ruta_id ){
                        cont = 1;
                        ruta_id = el.ruta_id;
                        //dd = 'style="background-color: '+cab[i].color+'"'; //rgba(255, 227, 34, 0.42);
                        dd = 'class="'+cab[i].color+'"'
                        dd2 =  '&nbsp;&nbsp;';
                        html="<tr data-id='"+cab[i].id+el.ruta_id+"-"+cont+"' "+dd+" data-parent='"+cab[i].id+parent+"' data-level="+child+">";
                        html+=    "<td class='col-md-12' data-column=name>"+dd2+"<i class=''></i><i class='fa fa-angle-right'></i>"+referido+"</td>";
                        html+=    "<td>"+fhora+"</td>";
                        html+=    "<td>"+proc+"</td>";
                        html+=    "<td>"+area+"</td>";
                        html+=    "<td>"+nord+"</td>";
                        html+="</tr>";
                    }
                    else{
                        parent = 1;
                        dd2 =  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class=""></i><i class="fa fa-angle-double-right"></i>';
                        dd = '';

                        if( cont == 2){ //Solo para colocar la flecha en caso tenga detalle!!!
                            $("#tb_tretable tr[data-id='"+cab[i].id+el.ruta_id+"-1']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().attr('style',"cursor: zoom-in;");
                        }

                        if(el.tipo=='r'){
                            last_ref = cont;
                            aux_ref = referido;
                        }
                        else if(el.tipo == 's'){
                            $("#tb_tretable tr[data-id='"+cab[i].id+el.ruta_id+"-"+last_ref+"']").find("td:eq(0)").html(dd2+aux_ref).parent().attr('style',"cursor: zoom-in;"); //.parent().attr('style',"background-color: rgba(210, 184, 0, 0.42);");
                            $("#tb_tretable tr[data-id='"+cab[i].id+el.ruta_id+"-"+last_ref+"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right');
                            parent = last_ref;
                            child = 3;
                            dd2 =  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class=""></i><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i>';
                        }
                        
                        html="<tr data-id='"+cab[i].id+el.ruta_id+"-"+cont+"' "+dd+" data-parent='"+cab[i].id+el.ruta_id+"-"+parent+"' data-level="+child+">";
                        html+=    "<td class='"+clase+"' data-column=name>"+dd2+referido+"</td>";
                        html+=    "<td>"+fhora+"</td>";
                        html+=    "<td>"+proc+"</td>";
                        html+=    "<td>"+area+"</td>";
                        html+=    "<td>"+nord+"</td>";
                        html+="</tr>";
                    }
                    $("#tb_tretable").append(html);
                });
            }
        }
        //////////////////////////////////////////////////////////////////////////////////////////

        /*tree-table*/
        $(function () {
            var $table = $('#tree-table'),
            rows = $table.find('tr');

            rows.each(function (index, row) {
                var
                    $row = $(row),
                    level = $row.data('level'),
                    id = $row.data('id'),
                    $columnName = $row.find('td[data-column="name"]'),
                    children = $table.find('tr[data-parent="' + id + '"]');

                if (children.length) {
                    var expander = $columnName.prepend('' +
                        //'<span class="treegrid-expander glyphicon glyphicon-chevron-right"></span>' +
                        '');

                    children.hide();

                    expander.on('click', function (e) {
                        var $target = $(e.target);
                        if ($target.find('i:eq(0)').hasClass('glyphicon glyphicon-chevron-right')) {
                            $target.find('i:eq(0)')
                                .removeClass('glyphicon glyphicon-chevron-right')
                                .addClass('glyphicon glyphicon-chevron-down');

                            children.show();
                        } else {
                            $target.find('i:eq(0)')
                                .removeClass('glyphicon glyphicon-chevron-down')
                                .addClass('glyphicon glyphicon-chevron-right');

                            reverseHide($table, $row);
                        }
                    });
                }

                $columnName.prepend('' +
                    '<span class="treegrid-indent" style="width:' + 15 * level + 'px"></span>' +
                    ''
                );
            });

            reverseHide = function (table, element) {
                var
                    $element = $(element),
                    id = $element.data('id'),
                    children = table.find('tr[data-parent="' + id + '"]');

                if (children.length) {
                    children.each(function (i, e) {
                        reverseHide(table, e);
                    });

                    $element.find('td:eq(0) i:eq(0).glyphicon-chevron-down')
                        .removeClass('glyphicon glyphicon-chevron-down')
                        .addClass('glyphicon glyphicon-chevron-right');

                    children.hide();
                }
            };
        });
        /*end tree-table*/
    }
    else{
        alert('no hay expediente unico');
    }
}

    /*return to last order*/
    retornar = function(){
        $("#retornarModal").modal('show');
    }

    retornarOk = function(){
        var motivo = document.querySelector("#txt_motivo_retorno").value;
        if( $.trim(motivo) != '' ){
            var rd_id=document.querySelector("#ruta_detalle_id").value;
            var ruta_id=document.querySelector("#ruta_id").value;
            var nroden=document.querySelector("#txt_orden").value;
            Bandeja.retornarPaso({'ruta_detalle_id':rd_id,'ruta_id':ruta_id,'orden':nroden, 'motivo':motivo});
        }else{
            msjG.mensaje("warning","Ingrese el motivo del retorno",4000);
        }
    }
    /*end return to last order*/

    mostrarRuta = function(obj){
        var ruta_flujo_id = obj.getAttribute('ruta_flujo_id');
        var ruta_id=document.querySelector("#ruta_id").value;
        cargarRutaId(ruta_flujo_id,2,ruta_id);
    }

    cargarRutaId=function(ruta_flujo_id,permiso,ruta_id){
        $("#txt_ruta_flujo_id_modal").remove();
        $("#form_ruta_flujo").append('<input type="hidden" id="txt_ruta_flujo_id_modal" value="'+ruta_flujo_id+'">');
        $("#txt_titulo").text("Vista");
        $("#texto_fecha_creacion").text("Fecha Vista:");
        $("#fecha_creacion").html('<?php echo date("Y-m-d"); ?>');
        $("#form_ruta_flujo .form-group").css("display","");
        Ruta.CargarDetalleRuta(ruta_flujo_id,permiso,CargarDetalleRutaHTML,ruta_id);
    }

    CargarDetalleRutaHTML=function(permiso,datos){
        areasG="";  areasG=[]; // texto area
        areasGId="";  areasGId=[]; // id area
        estadoG="";  estadoG=[]; // Normal / Paralelo
        theadArea="";  theadArea=[]; // cabecera area
        tbodyArea="";  tbodyArea=[]; // cuerpo area
        tfootArea="";  tfootArea=[]; // pie area

        tiempoGId="";  tiempoGId=[]; // id posicion del modal en base a una area.
        tiempoG="";  tiempoG=[];
        verboG="";  verboG=[];
        posicionDetalleVerboG=0;
        validandoconteo=0;
        $.each(datos,function(index,data){
            validandoconteo++;
            if(validandoconteo==1){
                $("#txt_persona").val(data.persona);
                $("#txt_proceso_1").val(data.flujo);
                $("#txt_area_1").val(data.area);
            }
            adicionarRutaDetalleAutomatico(data.area2,data.area_id2,data.tiempo_id+"_"+data.dtiempo,data.verbo,data.imagen,data.imagenc,data.imagenp,data.estado_ruta);
        });
        pintarAreasG(permiso);
        //alertatodo();
    }

    AbreTv=function(val){
        $("#areasasignacion [data-id='"+val+"']").click();
    }

    adicionarRutaDetalleAutomatico=function(valorText,valor,tiempo,verbo,imagen,imagenc,imagenp,estruta){
        valor=""+valor;
        var adjunta=false; var position=areasGId.indexOf(valor);
        if( position>=0 ){
            adjunta=true;
        }

        var verboaux=verbo.split("|");
        var verbo1=[];
        var verbo2=[];
        var verbo3=[];
        var verbo4=[];
        var verbo5=[];
        var verbo6=[];
        var imgfinal=imagen;
        for(i=0;i<verboaux.length;i++ ){
            verbo1.push(verboaux[i].split("^^")[0]);
            verbo2.push(verboaux[i].split("^^")[1]);
            verbo3.push(verboaux[i].split("^^")[2]);
            verbo4.push(verboaux[i].split("^^")[3]);
            verbo5.push(verboaux[i].split("^^")[4]);
            verbo6.push(verboaux[i].split("^^")[5]);

            if($.trim(verboaux[i].split("^^")[1])>0){
                imgfinal=imagenc;
            }
        }

        if(estruta>1){
            imgfinal=imagenp;
        }

        estadoG.push(estruta);
        areasG.push(valorText);
        areasGId.push(valor);

        if( adjunta==false ){
            head='<th class="eliminadetalleg" style="width:110px;min-width:100px !important;">'+valorText+'</th>';
            theadArea.push(head);

            body=   '<tr>'+
                        '<td class="areafinal" onclick="AbreTv('+valor+');" style="height:100px; background-image: url('+"'"+'img/admin/area/'+imgfinal+"'"+');">&nbsp;'+
                        '<span class="badge bg-yellow">'+areasG.length+'</span>'+
                        '</td>'+
                    '</tr>';
            tbodyArea.push([]);
            tbodyArea[ (tbodyArea.length-1) ].push(body);

            foot=   '<th class="eliminadetalleg">'+
                        '<div style="text-align:center;">'+
                        '<a class="btn bg-olive btn-sm" data-toggle="modal" data-target="#rutaModal" data-id="'+valor+'" data-text="'+valorText+'">'+
                            '<i class="fa fa-desktop fa-lg"></i>'+
                        '</a>'+
                        '</div>'+
                    '</th>';
            tfootArea.push(foot);
        }
        else{

            theadArea.push(0);
            tfootArea.push(0);
            tbodyArea.push([]);
            tbodyArea[ (tbodyArea.length-1) ].push(position+"|"+tbodyArea[position].length );
            body=   '<tr>'+
                        '<td class="areafinal" onclick="AbreTv('+valor+');" style="height:100px; background-image: url('+"'"+'img/admin/area/'+imgfinal+"'"+');">&nbsp;'+
                        '<span class="badge bg-yellow">'+areasG.length+'</span>'+
                        '</td>'+
                    '</tr>';
            tbodyArea[position].push(body);

        }

          var position=tiempoGId.indexOf(valor);
          var posicioninicial=areasGId.indexOf(valor);
          //alert("tiempo= "+position +" | areapos="+posicioninicial);
          var tid=0;
          var validapos=0;
          var detalle=""; var detalle2="";
          
          if(position>=0){
            tid=position;
            //alert("actualizando");
            /*detalle=tiempoG[tid][0].split("_");
            detalle[0]=posicioninicial;
            tiempoG[tid][0]=detalle.join("_");

            detalle2=verboG[tid][0].split("_");
            detalle2[0]=posicioninicial;
            verboG[tid][0]=detalle2.join("_");
            */
          }
          //else{
            //alert("registrando");

        if( tiempo!='_' || verbo!='' ){
            if( adjunta==false ){ // primer registro
                tiempoGId.push(valor);
                tiempoG.push([]);
                tid=tiempoG.length-1;
                tiempoG[tid].push(posicioninicial+"_"+tiempo);

                verboG.push([]);
                verboG[tid].push(posicioninicial+"_"+verbo1.join("|")+"_"+verbo2.join("|")+"_"+verbo3.join("|")+"_"+verbo4.join("|")+"_"+verbo5.join("|")+"_"+verbo6.join("|"));
            }
          //}
            else{
                var posicioninicialf=posicioninicial;
                for(var i=1; i<tbodyArea[posicioninicial].length; i++){
                    posicioninicialf++;
                    validapos=areasGId.indexOf(valor,posicioninicialf);
                    posicioninicialf=validapos;
                    if( i>=tiempoG[tid].length ){
                        //alert(tiempo+" | "+verbo+" | "+valor+" | "+posicioninicial+"-"+validapos);
                        tiempoG[tid].push(validapos+"_"+tiempo);

                        verboG[tid].push(validapos+"_"+verbo1.join("|")+"_"+verbo2.join("|")+"_"+verbo3.join("|")+"_"+verbo4.join("|")+"_"+verbo5.join("|")+"_"+verbo6.join("|"));
                    }
                    /*else{
                        detalle=tiempoG[tid][i].split("_");
                        detalle[0]=validapos;
                        tiempoG[tid][i]=detalle.join("_");

                        detalle2=verboG[tid][i].split("_");
                        detalle2[0]=validapos;
                        verboG[tid][i]=detalle2.join("_");
                    }*/
                }
            }
        }
    }


pintarAreasG=function(permiso){
    var htm=''; var click=""; var imagen=""; var clickeli="";
    $("#areasasignacion .eliminadetalleg").remove();
    $("#slct_area_id_2").val("");$("#slct_area_id_2").multiselect("refresh");
    $("#slct_area_id_2").multiselect("disable");

    if(permiso!=2){
        $("#slct_area_id_2").multiselect("enable");
    }

    for ( var i=0; i<areasG.length; i++ ) {
        click="";
        imagen="";
        clickeli="";
        if(permiso!=2){
            clickeli=" onclick='EliminarDetalle("+i+");' ";
        }

        if ( i>0 ) {
            if(permiso!=2){
                click=" onclick='CambiarDetalle("+i+");' ";
            }
            imagen="<i class='fa fa-sort-up fa-sm'></i>";
        }

        htm+=   "<tr id='tr-detalle-"+i+"'>"+
                    "<td>"+
                        (i+1)+
                    "</td>"+
                    "<td>"+
                        areasG[i]+
                    "</td>"+
                "</tr>";


        if(theadArea[i]!=0){

            $("#areasasignacion>thead>tr.head").append(theadArea[i]);
            $("#areasasignacion>tfoot>tr.head").append(tfootArea[i]);

            var detbody='<td class="eliminadetalleg">'+
                            '<table class="table table-bordered">';
            for(j=0; j<tbodyArea[i].length ; j++){
                if(j>0){
                    detbody+=   '<tr>'+
                                    '<td style="height:8px;">&nbsp;'+
                                    '</td>'+
                                '</tr>';
                }
                detbody+=tbodyArea[i][j];
            }
            detbody+='</table> </td>';
            $("#areasasignacion>tbody>tr.body").append(detbody);
        }
        
    };

    $("#areasasignacion>thead>tr.head").append('<th class="eliminadetalleg" style="min-width:1000px important!;">[]</th>'); // aqui para darle el area global
    $("#tb_rutaflujodetalleAreas").html(htm);
    }




    pintarTiempoG=function(tid){
    var htm="";var detalle="";var detalle2="";
    $("#tb_tiempo").html(htm);
    $("#tb_verbo").html(htm);

    posicionDetalleVerboG=0; // Inicializando posicion del detalle al pintar

    var subdetalle1="";var subdetalle2="";var subdetalle3="";var subdetalle4="";var subdetalle5="";var subdetalle6="";var imagen="";

    for(var i=0;i<tiempoG[tid].length;i++){
        // tiempo //
        detalle=tiempoG[tid][i].split("_");

        htm=   '<tr>'+
                    '<td>'+(detalle[0]*1+1)+'</td>'+
                    '<td>'+
                        '<select disabled class="form-control" id="slct_tipo_tiempo_'+detalle[0]+'_modal">'+
                            $('#slct_tipo_tiempo_modal').html()+
                        '</select>'+
                    '</td>'+
                    '<td>'+
                        '<input readonly class="form-control" type="number" id="txt_tiempo_'+detalle[0]+'_modal" value="'+detalle[2]+'">'+
                    '</td>'+
                '</tr>';
        $("#tb_tiempo").append(htm);

        $('#slct_tipo_tiempo_'+detalle[0]+'_modal').val(detalle[1]);
        //fin tiempo

        //verbo
        
        detalle2=verboG[tid][i].split("_");

        subdetalle1=detalle2[1].split('|');
        subdetalle2=detalle2[2].split('|');
        subdetalle3=detalle2[3].split('|');
        subdetalle4=detalle2[4].split('|');
        subdetalle5=detalle2[5].split('|');
        subdetalle6=detalle2[6].split('|');

        selectestado='';
        for(var j=0; j<subdetalle1.length; j++){
            posicionDetalleVerboG++;
            imagen="";
            
            
            if( (j+1)==subdetalle1.length ){
                selectestado='<br><select disabled id="slct_paralelo_'+detalle2[0]+'_modal">'+
                             '<option value="1">Normal</option>'+
                             '<option value="2">Paralelo</option>'+
                             '</select>';
            }

            htm=   '<tr id="tr_detalle_verbo_'+posicionDetalleVerboG+'">'+
                        '<td>'+(detalle2[0]*1+1)+selectestado+'</td>'+
                        '<td>'+
                            '<input readonly type="number" class="form-control txt_orden_'+detalle2[0]+'_modal" placeholder="Ing. Orden" value="'+subdetalle6[j]+'">'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_rol_'+detalle2[0]+'_modal">'+
                                $('#slct_rol_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_verbo_'+detalle2[0]+'_modal">'+
                                $('#slct_verbo_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_documento_'+detalle2[0]+'_modal">'+
                                $('#slct_documento_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<textarea disabled class="form-control txt_verbo_'+detalle2[0]+'_modal" placeholder="Ing. Acción">'+subdetalle1[j]+'</textarea>'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_condicion_'+detalle2[0]+'_modal">'+
                                $('#slct_condicion_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+imagen+'</td>'+
                    '</tr>';
            $("#tb_verbo").append(htm);

            if( (j+1)==subdetalle1.length ){
                $("#slct_paralelo_"+detalle2[0]+"_modal").val(estadoG[detalle2[0]]);
            }

            if(subdetalle2[j]==""){ // En caso no tenga valores se inicializa
                subdetalle2[j]="0";
            }
            //alert(subdetalle2[j]);
            $(".slct_condicion_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle2[j]);
            $(".slct_rol_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle3[j]);
            $(".slct_verbo_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle4[j]);
            $(".slct_documento_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle5[j]);
        }
        //fin verbo
    }
}

// Nuevos procesos para boton Tramites
asignarTramitePaso = function(ruta_id){
    if($('#form_ruta_detalle #slct_micro').val()!=''){
      sweetalertG.confirm("Confirmación!", "Desea Agregar el Sub Proceso Seleccionado?", function(){
        var data={ruta_id:ruta_id,ruta_detalle_micro_id:$('#form_ruta_detalle #slct_micro').val()};
        Bandeja.AdicionarMicroProceso(data);
    });
    }else{
        swal("Mensaje", "Seleccione Sub Proceso");
    }
}
// --
openPlantillaa=function(id,tamano,tipo){
    window.open("documentodig/vista/"+id+"/"+tamano+"/"+tipo,
                "PrevisualizarPlantilla",
                "toolbar=no,menubar=no,resizable,scrollbars,status,width=900,height=700");
};
</script>
