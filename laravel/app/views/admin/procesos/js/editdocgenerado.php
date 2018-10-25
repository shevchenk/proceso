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

    /*load combos*/
    data = {estado:1};
    slctGlobal.listarSlct('area','slct_areas','simple',null,data);
    slctGlobalHtml('slct_tipo_respuesta,#slct_tipo_respuesta_detalle','simple');
    slctGlobal.listarSlct('tiempo','sltiempo','simple',null,data);
    /*end load combos*/

    /*load datepickerrange*/
    $('#txt_fechaRange').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: false,
        showDropdowns: true
    });
    /*end load datepickerrange*/

    $("#btn_guardar_todo").click(guardarTodo);
    hora();

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


    /*open modal and poblate*/
    $('#edit_fecha_tramite').on('show.bs.modal', function (event) {
        $('#txtnrotramite').val($("#txt_id_doc").val());
        $('#txtsolicitante').val($("#txt_solicitante").val());
        $('#txtsumilla').val($("#txt_sumilla").val());
        $('#txtproceso').val($("#txt_flujo").val());
        $('#txtarea').val($("#txt_area").val());
        $('#txttiempoa').val($("#txt_tiempo").val().split(':')[1]);
    });
    /*end open modal and poblate*/

    $('#txt_fecha_inicio_b').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: false,
        showDropdowns: true
    }, function(start, end, label) {
      MostrarAjax();
    });

});

Close=function(todo){
    $("#bandeja_detalle").hide();
    $("#txt_observacion").val("");
    if ( typeof(todo)!='undefined' ){
        $("#txt_id_ant,#txt_id_union").val("");
    }
}

Limpiar=function(text){
    $("#"+text).val("");
}

MostrarAjax=function(tt){
    if (tt === "docs"){
        if( columnDefsG.length>0 ){
            dataTableG.CargarDatos(tt,'docs','cargar',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    }else{    
        Close();
        Bandeja.MostrarAjax();
    }
}

hora=function(){
    //Bandeja.FechaActual();
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

activar=function(id,ruta_detalle_id,td){//establecer como visto
    var tr = td;
    $(tr).attr('onClick','desactivar('+id+','+ruta_detalle_id+',this)');
    $(tr).removeClass('unread');
    $(tr).find('i').removeClass('fa-ban').addClass('fa-eye');

    Bandeja.CambiarEstado(ruta_detalle_id, id,1);
    //tambien debera cargar un detalle en la parte de abajo
    desactivar(id,ruta_detalle_id,td);
};

desactivar=function(id,ruta_detalle_id,td){//establecer como no visto
    var tr = td;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    
    $(tr).attr("style","background-color:#9CD9DE;");

    var data ={ruta_detalle_id:ruta_detalle_id};
    mostrarDetallle(ruta_detalle_id);
};

validacheck=function(val,idcheck){
    var verboaux="";
    var validacheck=0;

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
    $('#btneditTiempo').attr('id-detalle-ruta',datos.id);
    $('#btnUpdateDoc').attr('id-detalle-ruta',datos.id);
    $('#sltiempo>option[value='+datos.idtiempo+']').prop('selected',true);
    $('#txtmotivo').text(datos.motivo);

    $('#slct_tipo_respuesta,#slct_tipo_respuesta_detalle').multiselect('destroy');
    //$('#slct_tipo_respuesta,#slct_tipo_respuesta_detalle').attr('disabled',"true");
    slctGlobal.listarSlct('tiporespuesta','slct_tipo_respuesta','simple',ids,data,0,'#slct_tipo_respuesta_detalle','TR');
    slctGlobal.listarSlct('tiporespuestadetalle','slct_tipo_respuesta_detalle','simple',ids,data,1);
    $("#form_ruta_detalle [data-target='#expedienteModal']").attr("data-id",datos.id_tr);

    $("#form_ruta_detalle #txt_fecha_tramite").val(datos.fecha_tramite);
    $("#form_ruta_detalle #txt_sumilla").val(datos.sumilla);
    $("#form_ruta_detalle #txt_solicitante").val(datos.solicitante);

    $("#form_ruta_detalle #txt_flujo").val(datos.flujo);
    $("#form_ruta_detalle #txt_area").val(datos.area);
    $("#form_ruta_detalle #txt_id_doc").val(datos.id_doc);
    $("#form_ruta_detalle #txt_orden").val(datos.norden);
    $("#form_ruta_detalle #txt_fecha_inicio").val(datos.fecha_inicio);
    $("#form_ruta_detalle #txt_tiempo").val(datos.tiempo);

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

                        obs = "<textarea disabled class='area"+valorenviado+"' name='area_"+detalle[i].split("=>")[0]+"' id='area_"+detalle[i].split("=>")[0]+"'></textarea>";
                        imagen="<input disabled type='checkbox' class='check"+valorenviado+"' onChange='validacheck("+valorenviado+",this.id);' value='"+detalle[i].split("=>")[0]+"' name='chk_verbo_"+detalle[i].split("=>")[0]+"' id='chk_verbo_"+detalle[i].split("=>")[0]+"'>";
                        imagenadd= '<input name="" id="" disabled type="text" value="'+detalle[i].split("=>")[4]+'" class="txt'+valorenviado+' txt_'+detalle[i].split("=>")[0]+'"/>';
                        if(verbo=="Generar"){
                            //imagenadd= '<input data-pos="'+(i*1+1)+'" type="text" class="txt'+valorenviado+' txt_'+detalle[i].split("=>")[0]+'" id="documento_'+detalle[i].split("=>")[0]+'" name="documento_'+detalle[i].split("=>")[0]+'"/>';
                            archivo='<input class="form-control" id="archivo_'+detalle[i].split("=>")[0]+'" name="archivo_'+detalle[i].split("=>")[0]+'" type="file">';
                        }
                    }
                }else{
                    if(verbo=="Generar"){

                        imagenadd= '<div id="d_'+i+'"><input name="txtdocumento[]" disabled id="txtdocumento" rtverbo="'+detalle[i].split("=>")[0]+'" doc_dig_id="-1" type="text" value="'+detalle[i].split("=>")[4].split(" <a")[0]+'" class="txt_'+detalle[i].split("=>")[0]+'"/><span id="btn_'+i+'" onkeyup="rmDis('+i+')" onclick="lapiz('+i+','+detalle[i].split("=>")[0]+')" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i></span><span onclick="getDoc('+i+','+detalle[i].split("=>")[0]+'); return false;" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-search"></i></span><div>';
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
                            /*"<td>"+archivo+"</td>"+*/
                            "<td>"+persona+"</td>"+
                            "<td>"+fecha+"</td>"+
                            "<td>"+imagen+"</td>"+
                        "</tr>";
            }
            $("#t_detalle_verbo").html(html);
            
        }

}

guardarTodo=function(){
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
    


eventoSlctGlobalSimple=function(slct,valores){
    if( slct=="slct_tipo_respuesta" ){
        var detval=valores.split("|").join("").split("_");
        fechaAux="";
        if ( detval[1]==1 ) {
        fechaAux=detval[2];
        }
    }
}

/*update documento*/
updateDoc = function(obj){
    var id =obj.getAttribute('id-detalle-ruta');
    var repeat = $("input[id='txtdocumento']").map(function(){return $(this);}).get();
    var objCambios = [];
    $.each(repeat,function(index, el) {
        objCambios.push({'rutadetalleid':id,'rtverbo':el[0].getAttribute('rtverbo'),'edit':el[0].value,'doc_dig_id':el[0].getAttribute('doc_dig_id')});
    });
    Bandeja.editNombDocumento(JSON.stringify(objCambios),mostrarDetallle);   
}
/*end update documento */

/*update tiempo*/
 updateTiempo = function(obj){
    var id =obj.getAttribute('id-detalle-ruta');
    var motivo = $("#txtmotivo").val();
    var tiempoid= $("#sltiempo").val();
    var newtiempo = $("#txttiempoa").val();
    var datos = {'id' : id,'motivo':motivo,'tiempoid':tiempoid,'tiempo':newtiempo};
    Bandeja.editTiempoTramite(JSON.stringify(datos),mostrarDetallle);
 }
/*end update tiempo*/




var actualIDI;


function rmDis(id){
    $("#d_"+id).find("#txtdocumento").attr('doc_dig_id','');
}

function lapiz(id,rdi){
    $("#d_"+id).find("#txtdocumento").removeAttr("disabled");
}

function getDoc(id,rdi){

    $("#docsModal").modal("show");
    actualIDI = id;
}

function cargarDoc(id,referido,fecha){
    $("#d_"+actualIDI).find("#txtdocumento").attr("disabled","disabled").val(referido);
    $("#d_"+actualIDI).find("#txtdocumento").attr('doc_dig_id',id);
    $("#docsModal").modal("hide");
}

if (typeof GeneraFn !== "function") { 
  GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
    if(typeof(fn)!='undefined' && fn.col==3){
        console.log(row);
          var estadohtml='';
        estadohtml='<span id="'+row.id+'" onClick="cargarDoc(\''+row.id+'\',\''+row.titulo.split("|")[0]+'\',\''+row.titulo.split("|")[1]+'\')" class="btn btn-success"><i class="fa fa-lg fa-check"></i></span>';
        return estadohtml;
    }
  };
}



</script>
