<script type="text/javascript">
temporalBandeja=0;
valposg=0;
var fechaTG='<?php echo date("Y-m-d") ?>';
var horaTG='<?php echo date("H:i:s") ?>';
var areasG=[]; // texto area
var areasGId=[]; // id area
var theadArea=[]; // cabecera area
var tbodyArea=[]; // cuerpo area
var tfootArea=[]; // pie area

var tiempoGId=[]; // id posicion del modal en base a una area.
var tiempoG=[];
var verboG=[];
var posicionDetalleVerboG=0;

var fechaAux="";
$(document).ready(function() {
    //$("[data-toggle='offcanvas']").click();
    $('#txt_fecha, #txt_fecha_ini').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: false,
        showDropdowns: true
    });
    var data = {estado:1};
    slctGlobal.listarSlct('flujo','slct_procesos','simple',null,data);
    slctGlobal.listarSlct('lista/tipovizualizacion','slct_tipo_visualizacion','multiple',null,null);
    $('#slct_tipo_visualizacion').change(function() {
        FiltrarBandeja( $('#slct_tipo_visualizacion').val());
    });

    
    //ActualizarBandeja();
    


    //$("#form_validar").attr("onkeyup","return enterGlobal(event,'btn_buscar')");
    $("#btn_close2").click(Close);
    $("#btn_guardar_tiempo,#btn_guardar_verbo").remove();
    var data = {estado:1,usuario:1};
    var ids = [];
    slctGlobal.listarSlct('flujo','slct_flujo2_id','simple',ids,data);
    data = {estado:1}
    slctGlobal.listarSlct('flujo','slct_flujo_id','simple',ids,data);
    slctGlobal.listarSlct('area','slct_area_id','simple',ids,data);

    slctGlobal.listarSlct('ruta_detalle','slct_area2_id','simple');
    slctGlobalHtml('slct_tipo_respuesta,#slct_tipo_respuesta_detalle','simple');

    slctGlobal.listarSlct2('rol','slct_rol_modal',data);
    slctGlobal.listarSlct2('verbo','slct_verbo_modal',data);
    slctGlobal.listarSlct2('documento','slct_documento_modal',data);
    
    $("#btn_close").click(cerrar);
    //$("#btn_guardar_todo").click(guardarTodo);
    //$("#btn_buscar").click(buscar);
    hora();
    
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
    //$("#btn_guardar_todo").click(guardarTodo);
    //$("#areasasignacion").DataTable();


    // Exportar Datos
    $(document).on('click', '#btnexport', function(event) {
                
        if($('#txt_fecha').val() == '' && $('#txt_fecha_ini').val() == '')
        {
            msjG2.alert('info', "Por favor seleccione al menos una Fecha!", 3000);
            event.preventDefault();
        }
        else if($("#slct_areas").val() == '')
        {
            msjG2.alert('info', "Por favor seleccione Area!", 3000);
            event.preventDefault();
        }
        else
        {
            var fecha = $("#txt_fecha").val();
            var fecha_ini = $("#txt_fecha_ini").val();
            var proceso = $("#slct_procesos").val();

            msjG2.alert('info', 'Por favor espere mientras carga el Reporte...', 4000);
            //$(this).attr('href','reportepersonal/exportreportepersonal'+'?fecha_ini='+fecha_ini+'&fecha_fin='+fecha_fin+area);
            window.location = 'reporte/exporttramiteconclu'+'?fecha='+fecha+'&fecha_ini='+fecha_ini+'&proceso='+proceso;

        }
    });
    // --

});

ActualizarBandeja=function(){
    if($('#txt_fecha').val() == '' && $('#txt_fecha_ini').val() == '')
    {
        msjG2.alert('info', "Por favor seleccione al menos una Fecha!", 3000);
        return false;
    }
    else if($("#slct_areas").val() == '')
    {
        msjG2.alert('info', "Por favor seleccione Area!", 3000);
        return false;
    }else{
        var datos=$("#form_concluido").serialize().split("txt_").join("").split("slct_").join("");
        Bandeja.Mostrar(datos);
    }
}

hora=function(){
    tiempo=horaTG.split(":");
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
    }
    
    horaTG=tiempo.join(":");
    $("#txt_respuesta").val(fechaTG+" "+horaTG);
    $("#div_cumple>span").html("CUMPLIENDO TIEMPO");
    $("#txt_alerta").val("0");
    $("#txt_alerta_tipo").val("0");

    $("#div_cumple").removeClass("progress-bar-danger").removeClass("progress-bar-warning").addClass("progress-bar-success");
        
        if ( fechaAux!='' && fechaAux < $("#txt_respuesta").val() ) {
            $("#txt_alerta").val("1");
            $("#txt_alerta_tipo").val("2");
            $("#div_cumple").removeClass("progress-bar-success").removeClass("progress-bar-warning").addClass("progress-bar-danger");
            $("#div_cumple>span").html("SE DETIENE FUERA DEL TIEMPO");
        }
        else if ( fechaAux!='' ) {
            $("#txt_alerta").val("1");
            $("#txt_alerta_tipo").val("3");
            $("#div_cumple").removeClass("progress-bar-success").removeClass("progress-bar-danger").addClass("progress-bar-warning");
            $("#div_cumple>span").html("SE DETIENE DENTRO DEL TIEMPO");
        }
        else if ( $("#txt_fecha_max").val() < $("#txt_respuesta").val() ) {
            $("#txt_alerta").val("1");
            $("#txt_alerta_tipo").val("1");
            $("#div_cumple").removeClass("progress-bar-success").removeClass("progress-bar-warning").addClass("progress-bar-danger");
            $("#div_cumple>span").html("NO CUMPLE TIEMPO");
        }
tiempo = setTimeout('hora()',60000);
}

FiltrarBandeja=function(values){
    var form = new FormData();
    //form.append('param',values);
    if (values !== null) {
        for (var i = 0; i < values.length; i++) {
            form.append(String(i),values[i]);
        }
    }
    Bandeja.Mostrar(form);

};
/*activarTabla=function(){
    
};*/
activar=function(id,ruta_detalle_id,td){//establecer como visto
    var tr = td;
    $(tr).attr('onClick','desactivar('+id+','+ruta_detalle_id+',this)');
    $(tr).removeClass('unread');
    $(tr).attr('data-toggle','tooltip');
    $(tr).attr('data-placement','top');
    $(tr).find('i').removeClass('fa-ban').addClass('fa-eye');

    Bandeja.CambiarEstado(ruta_detalle_id, id,1);
    //tambien debera cargar un detalle en la parte de abajo
    detalle(id,ruta_detalle_id,td);
};
desactivar=function(id,ruta_detalle_id,td){//establecer como no visto
    //Bandeja.CambiarEstado(ruta_detalle_id, id,2);
    detalle(id,ruta_detalle_id,td);
};
mostrarModal=function(id,ruta_detalle_id){//establecer como no visto
    //mostrar modal
    Bandeja.MostrarUsuarios(ruta_detalle_id);
    //$('#usuarios_vieron_tramite').modal('show');
};

detalle=function(id,ruta_detalle_id, tr){
    var tr = tr;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    
    $(tr).attr("style","background-color:#9CD9DE;");

    var data ={ruta_detalle_id:ruta_detalle_id}
    //Bandeja.MostrarDetalle(data);
    mostrarDetallle(ruta_detalle_id);
};

HTMLreporte=function(datos){
    var html="";
    
    var alerta_tipo= '';
    $.each(datos,function(index,data){
        var ruta_detalle_id=data.ruta_detalle_id;
        var persona_visual=data.persona_visual;
        var estado;
        var id=data.id;
        if(data.id==1){//est visto
            //el boton debera cambiar  a no visto
            estado='onClick="desactivar('+id+','+ruta_detalle_id+',this)"';
            tr='<tr  data-toggle="tooltip" data-placement="top" title="Visto por: '+persona_visual+'" '+estado+'>';
            img='<td onClick="mostrarModal('+id+','+ruta_detalle_id+')" class="small-col"><i  class="fa fa-eye"></i></td>';

        } else {
            //unread
            estado='onClick="activar('+id+','+ruta_detalle_id+',this)"';
            tr='<tr class="unread" '+estado+'>';
            img='<td class="small-col"><i  class="fa fa-ban"></i></td>';
        }

        html+=tr+
            //"<td class='small-col'></td>"+
            img+
            "<td>"+data.id_union+"</td>"+
            "<td>"+data.tiempo+"</td>"+
            "<td>"+data.fecha_inicio+"</td>"+
            "<td>"+data.norden+"</td>"+
            "<td>"+data.fecha_tramite+"</td>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+data.respuesta+' '+data.respuestad+"</td>"+
            "<td>"+data.observacion+"</td>"+
            "<td>"+data.tipo_solicitante+"</td>"+
            "<td>"+data.solicitante+"</td>";
            if (data.id==='') {
                data.id='0';
            }
        //html+="<td>"+estado+"</td>";
        html+="</tr>";
    });
    $('#t_reporte').dataTable().fnDestroy();
    $("#tb_reporte").html(html);
    $("#reporte").show();
    $("#t_reporte").dataTable(); // inicializo el datatable  
    //activarTabla();
    //$('[data-toggle="tooltip"]').tooltip();
};

validacheck=function(val,idcheck){
    var verboaux="";
    var validacheck=0;
    //$("#slct_tipo_respuesta,#slct_tipo_respuesta_detalle").multiselect("enable");
    //$("#txt_observacion").removeAttr("disabled");

    $(".check1,.check2").removeAttr("disabled");
    if( val==1 && $("#"+idcheck).is(':checked') ){
        $(".check2").removeAttr("checked");
        $(".check2").attr("disabled","true");
    }
    else if( val==2 && $("#"+idcheck).is(':checked') ){
        $(".check1").removeAttr("checked");
        $(".check1").attr("disabled","true");
    }

    $("#t_detalle_verbo input[type='checkbox']").each(
        function( index ) { 
            if ( $(this).is(':checked') ) {
                verboaux+= "|"+$(this).val();
            }
            /*else if( !$(this).attr("disabled") ){
                validacheck=1;
            }*/
        }
    );

    /*if(validacheck==1){
        $("#slct_tipo_respuesta,#slct_tipo_respuesta_detalle").multiselect("disable");
        $("#txt_observacion").attr("disabled","true");
    }*/
}

cerrar=function(){
    $("#form_ruta_detalle .form-group").css("display","none");
    $("#form_ruta_detalle input[type='text'],#form_ruta_detalle textarea,#form_ruta_detalle select").val("");
    $('#form_ruta_detalle select').multiselect('refresh');
    $("#form_ruta_detalle t_detalle_verbo").html("");
}

/*buscar=function(){
    if( $("#txt_tramite").val()!="" ){
     var datos={ tramite:$("#txt_tramite").val() };
    $("#tabla_ruta_detalle").css("display","");
    Validar.mostrarRutaDetalle(datos,mostrarRutaDetalleHTML);
    }
    else{
        alert("Ingrese Nro Trámite y busque nuevamente");
    }
}*/

mostrarRutaFlujo=function(){
    /*$("#form_ruta_detalle>.form-group").css("display","none");
    var flujo_id=$.trim($("#slct_flujo2_id").val());
    var area_id=$.trim($("#slct_area2_id").val());

    if( flujo_id!='' && area_id!='' ){
        var datos={ flujo_id:flujo_id,area_id:area_id };
        $("#tabla_ruta_detalle").css("display","");
        Validar.mostrarRutaDetalle(datos,mostrarRutaDetalleHTML);
    }*/
}
/*
mostrarRutaDetalleHTML=function(datos){
    var html="";
    var cont=0;
    var botton="";
    var color="";
    var clase="";
     $('#t_ruta_detalle').dataTable().fnDestroy();
     $("#txt_ruta_detalle_id").remove();
    $.each(datos,function(index,data){
        imagen="";
        clase="";
        cont++;
        if($.trim(data.fecha_inicio)!=''){
            imagen="<a onClick='mostrarDetallle("+data.id+")' class='btn btn-primary btn-sm'><i class='fa fa-edit fa-lg'></i></a>";
        }
    html+="<tr>"+
        "<td>"+cont+"</td>"+
        "<td>"+data.id_doc+"</td>"+
        "<td>"+data.norden+"</td>"+
        "<td>"+data.verbo2.split("|").join("<br>")+"</td>"+
        "<td>"+data.fecha_inicio+"</td>"+
        "<td>"+data.fecha_max+"</td>"+
        "<td>"+data.flujo+"</td>"+
        "<td>"+data.area+"</td>"+
        "<td>"+imagen+
            '<a onclick="cargarRutaId('+data.ruta_flujo_id+',2)" class="btn btn-warning btn-sm"><i class="fa fa-search-plus fa-lg"></i> </a>'+
        "</td>";
    html+="</tr>";

    });
    $("#tb_ruta_detalle").html(html); 
    $('#t_ruta_detalle').dataTable({
        "ordering": false
    });
}
*/

mostrarDetallle=function(id){ //OK
    $("#form_ruta_detalle>.form-group").css("display","");
    $("#form_ruta_detalle>#ruta_detalle_id").remove();
    $("#form_ruta_detalle").append("<input type='hidden' id='ruta_detalle_id' name='ruta_detalle_id' value='"+id+"'>");
    var datos={ruta_detalle_id:id}
    Validar.mostrarDetalle(datos,mostrarDetalleHTML);
}

mostrarDetalleHTML=function(datos){
    fechaAux="";
    var data={ flujo_id:datos.flujo_id, estado:1,fecha_inicio:datos.fecha_inicio }
    var ids = [];
    $('#slct_tipo_respuesta,#slct_tipo_respuesta_detalle').multiselect('destroy');
    //$('#slct_tipo_respuesta,#slct_tipo_respuesta_detalle').attr('disabled',"true");
    slctGlobal.listarSlct('tiporespuesta','slct_tipo_respuesta','simple',ids,data,0,'#slct_tipo_respuesta_detalle','TR');
    slctGlobal.listarSlct('tiporespuestadetalle','slct_tipo_respuesta_detalle','simple',ids,data,1);
    var data = {ruta_flujo_id: datos.ruta_flujo_id, ruta_id: datos.ruta_id}
    Validar.mostrarCampos(data,mostrarCamposHTML);

    $("#form_ruta_detalle #txt_fecha_tramite").val(datos.fecha_tramite);
    $("#form_ruta_detalle #txt_sumilla").val(datos.sumilla);
    $("#form_ruta_detalle #txt_solicitante").val(datos.solicitante);

    $("#form_ruta_detalle #txt_flujo").val(datos.flujo);
    $("#form_ruta_detalle #txt_area").val(datos.area);
    $("#form_ruta_detalle #txt_id_doc").val(datos.id_doc);
    $("#form_ruta_detalle #txt_orden").val(datos.norden);
    $("#form_ruta_detalle #txt_fecha_inicio").val(datos.fecha_inicio);
    $("#form_ruta_detalle #txt_tiempo").val(datos.tiempo);
    $("#txt_respuesta").val(datos.dtiempo_final);

    $("#form_ruta_detalle>#txt_fecha_max").remove();
    $("#form_ruta_detalle").append("<input type='hidden' id='txt_fecha_max' name='txt_fecha_max' value='"+datos.fecha_max+"'>");

    $("#t_detalle_verbo").html("");
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
        if ( datos.verbo!='' ) {
            detalle=datos.verbo.split("|");
            html="";
            for (var i = 0; i < detalle.length; i++) {

                imagen = "<i class='fa fa-check fa-lg'></i>";
                imagenadd = "<ul><li>"+detalle[i].split("=>")[4].split("^").join("</li><li>")+"</li></ul>";
                obs = detalle[i].split("=>")[5];

                rol = detalle[i].split("=>")[6];
                verbo = detalle[i].split("=>")[7];
                documento = detalle[i].split("=>")[8];
                orden = detalle[i].split("=>")[9];
                archivo="";
                denegar=false;

                persona=detalle[i].split("=>")[10];
                fecha ='';
                if(persona!=''){
                    fecha=detalle[i].split("=>")[11];
                }

                if(detalle[i].split("=>")[2]=="Pendiente"){
                    if(detalle[i].split("=>")[3]=="NO"){
                        valorenviado=0;
                    }
                    else if(detalle[i].split("=>")[3]=="+1"){
                        valorenviado=1;
                    }
                    else if(detalle[i].split("=>")[3]=="+2"){
                        valorenviado=2;
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

                        obs = "<textarea class='area"+valorenviado+"' name='area_"+detalle[i].split("=>")[0]+"' id='area_"+detalle[i].split("=>")[0]+"'></textarea>";
                        imagen="<input type='checkbox' class='check"+valorenviado+"' onChange='validacheck("+valorenviado+",this.id);' value='"+detalle[i].split("=>")[0]+"' name='chk_verbo_"+detalle[i].split("=>")[0]+"' id='chk_verbo_"+detalle[i].split("=>")[0]+"'>";
                        imagenadd= '<input disabled type="text" class="txt'+valorenviado+' txt_'+detalle[i].split("=>")[0]+'"/>';
                        if(verbo=="Generar"){
                            imagenadd= '<input data-pos="'+(i*1+1)+'" type="text" class="txt'+valorenviado+' txt_'+detalle[i].split("=>")[0]+'" id="documento_'+detalle[i].split("=>")[0]+'" name="documento_'+detalle[i].split("=>")[0]+'"/>';
                            archivo='<input class="form-control" id="archivo_'+detalle[i].split("=>")[0]+'" name="archivo_'+detalle[i].split("=>")[0]+'" type="file">';
                        }
                    }
                }

                html+=  "<tr>"+
                            "<td>"+orden+"</td>"+
                            "<td>"+detalle[i].split("=>")[3]+"</td>"+
                            "<td>"+rol+"</td>"+
                            "<td>"+verbo+"</td>"+
                            "<td>"+documento+"</td>"+
                            "<td>"+detalle[i].split("=>")[1]+"</td>"+
                            "<td id='td_"+detalle[i].split("=>")[0]+"'>"+imagenadd+"</td>"+
                            "<td>"+obs+"</td>"+
                            //"<td>"+archivo+"</td>"+
                            "<td>"+persona+"</td>"+
                            "<td>"+fecha+"</td>"+
                            "<td>"+imagen+"</td>"+
                        "</tr>";
            }
            $("#t_detalle_verbo").html(html);
            
        }

}

mostrarCamposHTML = (result) => {
    $(".DatosPersonalizadosG").addClass('hidden');
    $(".DatosPersonalizadosG .box-body").html('');

    $.each(result,function(index,r){
        campo = '';
        subtitulo = '';
        color = 'error';
        icono = 'remove';
        html = '';
        lista = $.trim( r.lista ).split("*");
        campo_valor = $.trim(r.campo_valor);
        ruta_campo_id = $.trim(r.ruta_campo_id);
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

            html =
                '<div class="col-sm-'+ r.col +'">'+
                    ruta_flujo_campo_id +
                    ruta_campo +
                    '<label>'+ r.campo +':</label>'+
                    campogenerado
                '</div>';
        }
        
        $(".DatosPersonalizadosG .box-body").append(html);
        
    });
}

adicionaDetalleVerbo=function(val){
    valposg++;
var  imagen='<div class="input-group" id="div_'+valposg+'">'+
            '   <div class="input-group-addon btn btn-danger" onclick="eliminaDetalleVerbo('+valposg+');">'+
            '       <i class="fa fa-minus fa-lg"></i>'+
            '   </div>'+
            '   <input type="text" class="txt_'+val+'" data-inputmask="'+"'alias'"+': '+"'email'"+'" data-mask/>'+
            '</div>';
$("#td_"+val).append(imagen);
$('[data-mask]').inputmask('aaa-********');
}

eliminaDetalleVerbo=function(pos){
    $("#div_"+pos).remove();
}

/*guardarTodo=function(){
    var verboaux="";
    var codaux="";
    var obsaux="";
    var contcheck=0;
    var conttotalcheck=0;
    var alerta=false;
    var codauxd="";
    var validacheck=0;
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
                verboaux+="|"+$(this).val();
                codaux+= "|"+codauxd.substr(1);
                obsaux+="|"+$("#area_"+$(this).val()).val();
                contcheck++;

                if( $("#documento_"+$(this).val()).val()=="" ){
                    alert("Ingrese Nro del documento generado de la acción "+$("#documento_"+$(this).val()).attr("data-pos"));
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
        $("#form_ruta_detalle>#verbog").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='verbog' name='verbog' value='"+verboaux+"'>");
        codaux=codaux.substr(1);
        $("#form_ruta_detalle>#codg").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='codg' name='codg' value='"+codaux+"'>");
        obsaux=obsaux.substr(1);
        $("#form_ruta_detalle>#obsg").remove();
        $("#form_ruta_detalle").append("<input type='hidden' id='obsg' name='obsg' value='"+obsaux+"'>");
    }

    if( $("#slct_tipo_respuesta").val()=='' && conttotalcheck>0 && contcheck==0 && alerta==false ) {
            alert("Seleccione al menos 1 check");
    }
    else if ( $("#slct_tipo_respuesta").val()=='' && validacheck==0 && alerta==false ) {
        alert("Seleccione Tipo de Respuesta");
    }
    else if ( $("#slct_tipo_respuesta_detalle").val()=='' && validacheck==0 && alerta==false ) {
        alert("Seleccione Detalle Tipo Respuesta");
    }
    else if ( $("#txt_observacion").val()=='' && validacheck==0 && alerta==false ) {
        alert("Ingrese observacion del paso");
    }
    else if ( $("#slct_tipo_respuesta").val()!='' && validacheck==1 && alerta==false 
                && $("#slct_tipo_respuesta option[value='"+$("#slct_tipo_respuesta").val()+"']").attr("data-evento").split("_")[1]=='0'
            ) {
        alert("El tipo de respuesta seleccionada solo esta permitida cuando este activada todas las acciones habilitadas");
    }
    else if ( $("#slct_tipo_respuesta_detalle").val()=='' && $("#slct_tipo_respuesta").val()!='' ) {
        alert("Seleccione Detalle Tipo Respuesta");
    }
    else if( alerta==false ){
        if( confirm("Favor de confirmar para actualizar su información") ){
            if(validacheck==0 || $("#slct_tipo_respuesta").val()!=''){
                $('#slct_tipo_visualizacion').multiselect('deselectAll');
                $('#slct_tipo_visualizacion').multiselect('refresh');
                Validar.guardarValidacion();
            }
            else{
                Validar.guardarValidacion(mostrarDetallle,$("#form_ruta_detalle>#ruta_detalle_id").val() );
            }
        }
    }
}
*/
eventoSlctGlobalSimple=function(slct,valores){
    if( slct=="slct_tipo_respuesta" ){
        var detval=valores.split("|").join("").split("_");
        fechaAux="";
        if ( detval[1]==1 ) {
        fechaAux=detval[2];
        }
    }
    else if( slct=="slct_flujo2_id" ){
        var valor=valores.split('|').join("");
        $("#slct_area2_id").val(valor);
        $("#slct_area2_id").multiselect('refresh');

        var flujo_id=$.trim($("#slct_flujo2_id").val());
        var area_id=$.trim($("#slct_area2_id").val());

        if( flujo_id!='' && area_id!='' ){
            var datos={ flujo_id:flujo_id,area_id:area_id };
            $("#tabla_ruta_detalle").css("display","");
            Validar.mostrarRutaDetalle(datos,mostrarRutaDetalleHTML);
        }
    }
}

////////////////////// Agregando para el mostrar detalle
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

Close=function(){
    $("#form_ruta_flujo .form-group").css("display","none");
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

cargarRutaId=function(ruta_flujo_id,permiso){ // OK
    $("#txt_ruta_flujo_id_modal").remove();
    $("#form_ruta_flujo").append('<input type="hidden" id="txt_ruta_flujo_id_modal" value="'+ruta_flujo_id+'">');
    $("#txt_titulo").text("Vista");
    $("#texto_fecha_creacion").text("Fecha Vista:");
    $("#fecha_creacion").html('<?php echo date("Y-m-d"); ?>');
    $("#form_ruta_flujo .form-group").css("display","");
    Ruta.CargarDetalleRuta(ruta_flujo_id,permiso,CargarDetalleRutaHTML);
    //alert('Actualizando '+ruta_flujo_id+ "Con permiso =>"+permiso);
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
            $("#slct_flujo_id").val(data.flujo_id);
            $("#slct_area_id").val(data.area_id);
            $("#slct_flujo_id,#slct_area_id").multiselect('disable');
            $("#slct_flujo_id,#slct_area_id").multiselect('refresh');
            $("#txt_persona").val(data.persona);
        }
        adicionarRutaDetalleAutomatico(data.area2,data.area_id2,data.tiempo_id+"_"+data.dtiempo,data.verbo,data.imagen,data.imagenc,data.imagenp,data.estado_ruta);
    });
    pintarAreasG(permiso);
    //alertatodo();
}

AbreTv=function(val){
    $("#areasasignacion [data-id='"+val+"']").click();
}
</script>
