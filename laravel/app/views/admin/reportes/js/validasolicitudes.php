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
    $('#txt_fecha_estado').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: false,
        showDropdowns: true
    });

    $("[data-toggle='offcanvas']").click();
    $("#txt_tramite").keypress(preventSubmit);
    var data = {estado:1};
    var ids = [];

    slctGlobalHtml('slct_estado','multiple');
    slctGlobal.listarSlctFuncion('local','listarlocales','slct_local','multiple',null,{estado:1});

    slctGlobal.listarSlct2('rol','slct_rol_modal',data);
    slctGlobal.listarSlct2('verbo','slct_verbo_modal',data);
    slctGlobal.listarSlct2('documento','slct_documento_modal',data);

    $("#generar_1").click(reportet);
    $("#generar_2").click(exportar);
    $("#generar_3").click(exportar2);
    $("#txt_tramite").focus();
});

exportar = () =>{
    if( valida(1) ){
        msjG2.alert('info', "Por favor espere mientras carga el Reporte...", 4000);
        var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
        window.location = 'reportetramite/exportvalidasolicitudes'+'?'+datos;
    }
}

exportar2 = () =>{
    if( valida(2) ){
        msjG2.alert('info', "Por favor espere mientras carga el Reporte...", 4000);
        var datos=$("#form_tramiteunico").serialize().split("txt_").join("").split("slct_").join("");
        window.location = 'reportetramite/exportvalidasolicitudesproduccion'+'?'+datos;
    }
}

eventoSlctGlobalSimple = () =>{}

valida=function(nro){
    var r=true;
    if( nro==1 ){
        if( $.trim( $("#txt_fecha_estado").val() )=='' &&
            $.trim( $("#slct_local").val() )=='' &&
            $.trim( $("#slct_estado").val() )=='' &&
            $.trim( $("#txt_tramite").val() )=='' &&
            $.trim( $("#txt_solicitante").val() )==''
        ){
            msjG.mensaje("warning","Debe ingresar almenos 1 filtro para iniciar la búsqueda.",5000);
            $("#txt_fecha_estado").focus();
            r=false;
        }
    }
    else if( nro==2 ){
        if( $.trim( $("#txt_fecha_estado").val() )=='' 
        ){
            msjG.mensaje("warning","Debe ingresar el rango de fecha para iniciar la búsqueda.",5000);
            $("#txt_fecha_estado").focus();
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
        btnruta='<a onclick="cargarRutaId('+data.ruta_flujo_id+',2,'+data.id+')" class="btn btn-warning btn-sm"><i class="fa fa-search-plus fa-lg"></i> </a>';
        btnexpediente='<a onclick="expedienteUnico('+data.id+')" class="btn btn-default btn-sm"><i class="fa fa-search-plus fa-lg"></i> </a>';
        clases = ''; archivo = ''; btn = '';
        if( data.estado == 'Aprobado' ){
            clases="class='alert-success'";
        }
        else if( data.estado == 'Desaprobado' ){
            clases="class='alert-danger'";
        }

        if( $.trim(data.ruta_archivo) != '' ){
            archivo = "<a class='btn btn-info btn-lg' href='"+data.ruta_archivo+"' target='_blank'><i class='fa fa-file-pdf-o fa-lg'></i>";
        }

        html+="<tr "+clases+">"+
            "<td>"+data.tipo_solicitante+"</td>"+
            "<td>"+data.solicitante+"</td>"+
            "<td>"+data.tipo_tramite+"</td>"+
            "<td>"+data.documento+"</td>"+
            "<td>"+data.local+"</td>"+
            "<td>"+data.servicio+"</td>"+
            "<td>"+data.fecha+"</td>"+
            "<td>"+archivo+"</td>"+
            "<td>"+data.expediente+"</td>"+
            "<td>"+data.estado+"</td>"+
            "<td>"+data.updated_at+"</td>"+
            "<td>"+data.observacion+"</td>"+
            "<td>"+data.tramite+"</td>";
            
            if( $.trim(data.tramite)!='' ){
                btn = '<a class="btn btn-default btn-lg" target="_blank" href="https://mitramitecampus2.inturtramites.pe/?tramite='+$.trim(data.tramite)+'&fecha='+$.trim(data.fecha_tramite)+'"><i class="fa fa-eye"></i></a>';
            }
        html+='<td>'+btn+'</td>';
        html+="</tr>";
    });

    $("#t_reportet_tab_1 tbody").html(html);
    $("#t_reportet_tab_1").dataTable({
            //"scrollY": "400px",
            //"scrollCollapse": true,
            //"scrollX": true,
            "bPaginate": true,
            "bLengthChange": true,
            "bInfo": false,
            "visible": true,
            "order": [[ 0, "desc" ]],
            "pageLength": 5,
    });
    $("#reportet_tab_1").show();
};



</script>
