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

$(document).ready(function(){
    $('#txt_fecha_documento').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: false,
        showDropdowns: true
    });

    $("[data-toggle='offcanvas']").click();
    $("#txt_tramite").keypress(preventSubmit);
    var data = {estado:1};
    var ids = [];

    slctGlobal.listarSlctFuncion('local','listarlocales','slct_local','multiple',UsuarioLocalId,{estado:1, usuario_local:1});
    slctGlobal.listarSlctFuncion('area','listara','slct_area','multiple',null,{estado:1, areapersona:1});
    slctGlobal.listarSlctFuncion('flujo','listar','slct_flujo','multiple',null,{estado:1});
    slctGlobal.listarSlctFuncion('documento','listar','slct_documento','multiple',null,{estado:1});

    slctGlobal.listarSlct2('rol','slct_rol_modal',data);
    slctGlobal.listarSlct2('verbo','slct_verbo_modal',data);
    slctGlobal.listarSlct2('documento','slct_documento_modal',data);

    $("#generar_1").click(reportet);
    $("#generar_2").click(exportar);
    $("#generar_3").click(exportar2);
    $("#generar_4").click(exportar3);
    $("#txt_tramite").focus();
});

exportar = () =>{
    if( valida(1) ){
        swal({   
            title: "Reporte de Producción - Datos",   
            text: "Por favor espere mientras carga el Reporte...",   
            timer: 4000,   
            showConfirmButton: false 
        });
        var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
        window.location = 'reportetramite/exportproduccionexpedientes'+'?'+datos;
    }
}

exportar2 = () =>{
    if( valida(2) ){
        swal({   
            title: "Reporte de Producción - Totales",   
            text: "Por favor espere mientras carga el Reporte...",   
            timer: 4000,   
            showConfirmButton: false 
        });
        var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
        window.location = 'reportetramite/exportproduccionexpedienteslocal'+'?'+datos;
    }
}

exportar3 = () =>{
    if( valida(2) ){
        swal({   
            title: "Reporte de Producción - Estados",   
            text: "Por favor espere mientras carga el Reporte...",   
            timer: 4000,   
            showConfirmButton: false 
        });
        var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
        window.location = 'reportetramite/exportproduccionexpedientesestado'+'?'+datos;
    }
}

eventoSlctGlobalSimple = () =>{}

valida=function(nro){
    var r=true;
    if( nro==1 ){
        if( $.trim( $("#txt_fecha_documento").val() )=='' &&
            $.trim( $("#slct_proceso").val() )=='' &&
            $.trim( $("#slct_local").val() )=='' &&
            $.trim( $("#slct_area").val() )=='' &&
            $.trim( $("#slct_documento").val() )==''
        ){
            msjG.mensaje("warning","Debe ingresar almenos 1 filtro para iniciar la búsqueda.",5000);
            $("#txt_fecha_documento").focus();
            r=false;
        }
    }
    else if( nro==2 ){
        if( $.trim( $("#txt_fecha_documento").val() )=='' 
        ){
            msjG.mensaje("warning","Debe ingresar el rango de fecha para iniciar la búsqueda.",5000);
            $("#txt_fecha_documento").focus();
            r=false;
        }
    }
    return r;
}

reportet=function(){
    if( valida(1) ){
        var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
        Tramite.mostrar( datos,HTMLreportet,'t' );
    }
}

HTMLreportet=function(datos){
    var btnruta='';
    var html="";

    $("#t_reportet_tab_1").dataTable().fnDestroy();
    $("#t_reportet_tab_1 tbody").html('');
    /******************************************************/

    $.each(datos,function(index,data){
        html+="<tr>"+
            "<td>"+data.local+"</td>"+
            "<td>"+data.area+"</td>"+
            "<td>"+data.documento+"</td>"+
            "<td>"+data.proceso+"</td>"+
            "<td>"+data.docs+"</td>"+
            "<td>"+data.tramites+"</td>";
        html+="</tr>";
    });

    $("#t_reportet_tab_1 tbody").html(html);
    $("#t_reportet_tab_1").dataTable({
            //"scrollY": "400px",
            //"scrollCollapse": true,
            //"scrollX": true,
            "bPaginate": true,
            "bLengthChange": true,
            "bInfo": true,
            "visible": true,
            "order": [[ 0, "desc" ]],
            "pageLength": 5,
    });
    $("#reportet_tab_1").show();
};



</script>
