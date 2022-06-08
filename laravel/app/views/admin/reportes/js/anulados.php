<script type="text/javascript">

    var preventSubmit = function(event) {
        if(event.keyCode == 13) {
            event.preventDefault();
            setTimeout(function(){reportet();},100);
            return false;
        }
    }

    UsuarioId='<?php echo Auth::user()->id; ?>';
    DataUser = '<?php echo Auth::user(); ?>';
    UsuarioLocalId='<?php echo trim(Auth::user()->local_id); ?>';

$(document).ready( () => {
    $('#txt_fecha_anu, #txt_fecha_inicio_anu').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: false,
        showDropdowns: true
    });

    $("[data-toggle='offcanvas']").click();
    //$("#txt_tramite").keypress(preventSubmit);
    var data = {estado:1};
    var ids = [];
    slctGlobal.listarSlctFuncion('local','listarlocales','slct_local_anu','multiple',UsuarioLocalId,{estado:1, usuario_local:1});

    slctGlobal.listarSlct2('rol','slct_rol_modal',data);
    slctGlobal.listarSlct2('verbo','slct_verbo_modal',data);
    slctGlobal.listarSlct2('documento','slct_documento_modal',data);

    $("#generar_1").click(function (){
        reportet();
    });
    $("#txt_tramite").focus();
});

eventoSlctGlobalSimple  = () => {}

valida= (nro) =>{
    var r=true;
    if( nro==1 ){
        if( $.trim( $("#txt_fecha_anu").val() )=='' &&
            $.trim( $("#txt_fecha_inicio_anu").val() )=='' &&
            $.trim( $("#slct_local_anu").val() )=='' &&
            $.trim( $("#txt_tramite").val() )=='' &&
            $.trim( $("#txt_solicitante_anu").val() )==''
        ){
            msjG.mensaje("warning","Debe ingresar almenos 1 filtro para iniciar la búsqueda.",5000);
            $("#txt_fecha_anulado").focus();
            r=false;
        }
    }
    return r;
}

reportet= () =>{
    if( valida(1) ){
        var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
        Tramite.mostrar( datos,HTMLreportet,'t' );
    }
}

detalle= (ruta_id, boton) =>{
    $("#btn_close").click();
    var tr = boton.parentNode.parentNode;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    tr.style.backgroundColor = "#E1E1E1";

    $("#form_tramiteunico").append("<input type='hidden' id='txt_ruta_id' name='txt_ruta_id' value='"+ruta_id+"'>");
    var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
    $("#form_tramiteunico #txt_ruta_id").remove();
    Tramite.mostrar( datos,HTMLreported,'d' );
};

HTMLreportet= (datos) =>{
    var btnruta='';
    var html="";

    $("#t_reportet_tab_1").dataTable().fnDestroy();
    $("#t_reportet_tab_1 tbody").html('');
    /*******************DETALLE****************************/
    $("#t_reported_tab_1").dataTable().fnDestroy();
    $("#t_reported_tab_1 tbody").html('');
    /******************************************************/

    $.each(datos, (index,data) =>{
        btnruta='<a onclick="cargarRutaId('+data.ruta_flujo_id+',2,'+data.id+')" class="btn btn-warning btn-sm"><i class="fa fa-search-plus fa-lg"></i> </a>';
        btnexpediente='<a onclick="expedienteUnico('+data.id+')" class="btn btn-default btn-sm"><i class="fa fa-search-plus fa-lg"></i> </a>';
        //clases1="class='TramiteOk'";
        clases2="";
        clases1="";
        /*if(data.detalle!=''){
            clases2="class='TramiteOk'";
        }*/
        html+="<tr>"+
            "<td "+clases1+">"+data.tramite+"</td>"+
            '<td><a onClick="detalle('+data.id+',this)" class="btn btn-primary btn-sm" data-id="'+data.id+'" data-titulo="Editar"><i class="fa fa-search fa-lg"></i> </a> '+btnruta+btnexpediente+'</td>'+
            "<td>"+data.fecha_inicio+"</td>"+
            "<td>"+$.trim(data.local_origen)+"</td>"+
            "<td>"+data.local+"</td>"+
            "<td>"+data.proceso+"</td>"+
            "<td "+clases2+">"+data.tipo_solicitante+"</td>"+
            "<td "+clases2+">"+data.persona+"</td>"+
            "<td>"+data.fecha_anulacion+"</td>"+
            "<td>"+data.responsable_anulacion+"</td>";
        html+="</tr>";
    });

    $("#t_reportet_tab_1 tbody").html(html);
    $("#t_reportet_tab_1").dataTable({
            "scrollY": "400px",
            "scrollCollapse": true,
            "scrollX": true,
            "bPaginate": false,
            "bLengthChange": false,
            "bInfo": false,
            "visible": false,
    });
    $("#reportet_tab_1").show();
};

HTMLreported= (datos) =>{
    var html="";
    var alertOk ='success';//verde
    var alertError ='danger';//rojo
    var alertCorregido ='warning';//ambar
    var alerta='';
    var estado_final='';

    $("#t_reported_tab_1 tbody").html('');
    $("#t_reported_tab_1").dataTable().fnDestroy();

    $.each(datos, (index,data) =>{
        if (data.alerta=='0') alerta=alertOk;
        if (data.alerta=='1' || data.condicion>1) alerta=alertError;
        if (data.alerta=='2' || data.condicion==1) alerta=alertCorregido;

        
        estado_final='Pendiente';
        if(data.dtiempo_final!=''){
            if(data.alerta=='0'){
                estado_final='Concluido';
            }
            else if(data.alerta=='1' && data.alerta_tipo=='1'){
                estado_final='A Destiempo';
            }
            else if(data.alerta=='1' && data.alerta_tipo=='2'){
                estado_final='Lo He Detenido a Destiempo';
            }
            else if(data.alerta=='1' && data.alerta_tipo=='3'){
                estado_final='Lo He Detenido';
            }
            else if(data.alerta=='2'){
                estado_final='Lo He Detenido R.';
            }
        }

        var img = ''; var archivo='';

        if( $.trim(data.archivo)!='' ){
            $.each(data.archivo.split("|"), (index, varchivo) =>{
                if( $.trim(varchivo)!='' && varchivo.substr(-3)=='pdf' ){
                    img= 'img/archivo/pdf.jpg';
                }
                else if( $.trim(varchivo)!='' && (varchivo.substr(-4)=='docx' || varchivo.substr(-3)=='doc') ){
                    img= 'img/archivo/word.png';
                }
                else if( $.trim(varchivo)!='' && (varchivo.substr(-4)=='xlsx' || varchivo.substr(-3)=='xls' || varchivo.substr(-3)=='csv') ){
                    img= 'img/archivo/excel.jpg';
                }
                else if( $.trim(varchivo)!='' && (varchivo.substr(-4)=='pptx' || varchivo.substr(-3)=='ppt') ){
                    img= 'img/archivo/ppt.png';
                }
                else if( $.trim(varchivo)!='' && varchivo.substr(-3)=='txt' ){
                    img= 'img/archivo/txt.jpg';
                }
                else{
                    img= varchivo;
                }
                archivo +=  "<a href='"+ varchivo +"' target='_blank'>"+
                                "<img src='"+ img +"' alt='' class='img-responsive foto_desmonte' width='60' height='50' border='0'>"+
                            "</a>";
            });
        }

        html+="<tr class='"+alerta+"'>"+
                "<td>"+data.norden+"</td>"+
                "<td>"+data.area+"</td>"+
                "<td>"+data.tiempo+': '+data.dtiempo+"</td>"+
                "<td>"+data.fecha_inicio+"</td>"+
                "<td>"+data.dtiempo_final+"</td>"+
                "<td>"+estado_final+"</td>"+
                "<td>"+data.verbo2.split("|").join("<br>")+"</td>"+
                "<td>"+ archivo +"</td>"+
                "<td>"+data.ordenv.split("|").join("<br>")+"</td>"+
                "<td>"+data.retorno+"</td>"+ // SE AÑADIO
                "<td>"+data.motivo_retorno+"</td>"; // SE AÑADIO
        html+=  "</tr>";

    });

    $("#t_reported_tab_1 tbody").html(html);
    $("#t_reported_tab_1").dataTable({
            "scrollY": "400px",
            "scrollCollapse": true,
            "scrollX": true,
            "bPaginate": false,
            "bLengthChange": false,
            "bInfo": false,
            "visible": false,
    });
    $("#reported_tab_1").show();
}


</script>
