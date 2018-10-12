<script type="text/javascript">
$(document).ready(function() {
    var data = {estado:1};
    var ids = [];

    window.RolIdG='<?php echo Auth::user()->rol_id; ?>';
    window.UsuarioId='<?php echo Auth::user()->id; ?>';
    window.AreaId='<?php echo Auth::user()->area_id; ?>';

    slctGlobal.listarSlct('areauser','slct_area_id','multiple',ids,data);
    $("#generar").click(function (){
        area_id = $('#slct_area_id').val();
        if ($.trim(area_id)!=='') {
            data = {area_id:area_id};
            Usuario.mostrar(data);
            $('#op_2').text('CARGANDO..');
            Usuario.mostrarReporte2(data);
        } else {
            alert("Seleccione Área");
        }
    });

     $("#btnprintuserall").click(function (){
        area_id = $('#slct_area_id').val();
        if ($.trim(area_id)!=='') {
            openPlantillaArea(window.RolIdG, area_id,4,0);
        } else {
            alert("Seleccione Área");
        }
    });

});

$(document).on('click', '#btnexport', function(event) {
    var area=$("#slct_area_id").val();
    if ( area!=="") {
            $(this).attr('href','reporte/exportusuarios'+'?area_id='+$("#slct_area_id").val().join("','")+'&export=1');
    } else {
            alert("Seleccione area");
    }
});


HTMLreporte=function(datos){
    var html="";
    
    var alerta_tipo= '';
    $('#t_reporte').dataTable().fnDestroy();
    pos=0;
    $.each(datos,function(index,data){
        pos++;
        html+="<tr>"+
            "<td>"+data.paterno+"</td>"+
            "<td>"+data.materno+"</td>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+data.email+"</td>"+
            "<td>"+data.dni+"</td>"+
            "<td>"+data.fecha_nacimiento+"</td>"+
            "<td>"+data.sexo+"</td>"+
            "<td>"+data.estado+"</td>"+
            "<td>"+data.area+"</td>";
        html+="</tr>";
    });
    $("#tb_reporte").html(html);
    $("#t_reporte").dataTable(
        {
            "order": [[ 0, "asc" ],[1, "asc"],[2, "asc"]],
        }
    ); 
    $("#reporte").show();
};


HTMLreporte2=function(datos){

    var html="";    
    var alerta_tipo= '';
    var pos=0;
    $.each(datos,function(index,data){
        pos++;

        if(data.estado == 'Activo') {
            class_estado = 'label label-success';
            text_estado = 'ACTIVO';
        } else {
            class_estado = 'label label-danger';
            text_estado = 'INACTIVO';
        }
        
        var img_qr = Usuario.obtenerQRUser(window.RolIdG, data.area_id, data.dni,4,0); // Obtengo los QR por User

        html+='<div class="col-md-12" style="border-bottom: 5px solid #F5F5F5;margin-bottom: 5px;">';        
            html+='<div class="col-md-5">'+
                    '<h3>'+data.nombre+' '+data.paterno+' '+data.materno+'</h3>';
                    //'<h3>'+data.paterno+' '+data.materno+'</h3>'+
            html+='<h5><span class="label label-default">&nbsp;DNI&nbsp;</span> '+data.dni+'</h5>';

            if(window.RolIdG == 8 || window.RolIdG == 9) { //if(window.AreaId == 10 && (window.RolIdG == 8 || window.RolIdG == 9))
                //if(data.area_id == 10) {
                    var resolucion = (data.resolucion)?data.resolucion:'';
                    var cod_inspector = (data.cod_inspector)?data.cod_inspector:'';
                    html += '<form id="formr'+data.norden+'" name="formr'+data.norden+'" class="form-inline">'+
                              '<div class="form-group" style="padding: 8px 10px;">'+
                                '<label for="" style="width: 110px;">N° Resoluci&oacute;n</label>'+
                                //'<input type="text" class="form-control" onkeypress="return justNumbers(event);" id="txt_nro_resolucion'+data.norden+'" name="txt_nro_resolucion" value="'+data.resolucion+'" placeholder="0000000">'+
                                '<input type="text" class="form-control" id="txt_nro_resolucion'+data.norden+'" name="txt_nro_resolucion" value="'+resolucion+'" placeholder="0000000">'+
                              '</div>'+
                              '<div class="form-group" style="padding: 0px 10px;">'+
                                '<label for="" style="width: 110px;">Cod. Inspector</label>'+
                                '<input type="text" class="form-control" id="txt_codigo_inspector'+data.norden+'" name="txt_codigo_inspector" value="'+cod_inspector+'" placeholder="0000000">'+
                              '</div>'+
                              '<button type="button" name="btnactualizaU" id="btnactualizaU" class="btn btn-default btn-sm" onclick="guardarResoUser('+data.norden+')">Actualizar</button>'+
                            '</form>'+
                            '<div id="men'+data.norden+'" style="display: none;" class="alert alert-danger" role="alert"></div>';
                /*} else {
                    html+='<h5><span class="label label-default">&nbsp;E-mail&nbsp;</span> '+data.email+'</h5>';
                }*/                    
            } else {
                html+='<h5><span class="label label-default">&nbsp;E-mail&nbsp;</span> '+data.email+'</h5>';
            }

            html+='<p><a class="btn btn-primary btn-xs" href="#" onclick="abrirCargaImagen('+data.norden+',\''+data.dni+'\');"><i class="fa fa-print"></i>&nbsp;IMAGEN</a></p>';

            html+="</div>";
            html+='<div class="col-md-3 text-center">'+
                    '<h4>'+data.area+'</h4>'+
                    '<p><span class="'+class_estado+'" style="padding: 8px; font-size: 12px;">'+text_estado+'</span></p>';
            html+="</div>";
            html+='<div class="col-md-2 text-center">'+
                    '<p><img id="imgU'+data.dni+'" src="img/carnet/'+data.imagen_dni+'" style="width: 100px; height:140;" href="javascript:void(0);"></p>';
            html+="</div>";
            html+='<div class="col-md-2 text-center">'+
                    '<p id="uqr'+data.dni+'">'+img_qr+'</p>'+
                    '<p><a class="btn btn-default btn-xs" href="#" onclick="openPlantilla('+window.RolIdG +', '+data.area_id+',\''+data.dni+'\',4,0); return false;" data-titulo="Previsualizar"><i class="fa fa-print"></i>&nbsp;Ver</a>&nbsp;'+
                    '<a class="btn btn-danger btn-xs" href="#" onclick="openImagen('+window.RolIdG +', '+data.area_id+',\''+data.dni+'\',4,0); return false;" data-titulo="Previsualizar"><i class="fa fa-print"></i>&nbsp;Print</a></p>';
            html+="</div>";

        html+='</div>';
    });
    $("#reporte2").html(html);
    $("#reporte2").show();
    $('#op_2').text('REPORTE QR')
};

guardarResoUser=function(persona_id){
    $("#men"+persona_id).html('').hide();

    var nro_resolucion = $("#txt_nro_resolucion"+persona_id).val();
    var cod_inspector = $("#txt_codigo_inspector"+persona_id).val();
    
    if(nro_resolucion == '')
        $("#men"+persona_id).html('Por favor ingrese la RESOLUCI&Oacute;N!').show();
    else if(cod_inspector == '')
        $("#men"+persona_id).html('Por favor ingrese el COD. INSPECTOR!').show();
    else        
        Usuario.actualizarCodResolucion(persona_id, nro_resolucion, cod_inspector);
        //alert(persona_id+ ' - '+ nro_resolucion+ ' - '+ cod_inspector);

}

abrirCargaImagen=function(persona_id, dni){
    $("#fileModal").modal("show");
    $("#file_dni").val(dni);

};

sendImage=function(){

    var file = document.getElementById("cargo_comprobante").files[0];
    var mnorden = document.getElementById("file_dni").value;

    var reader = new FileReader();
        
    reader.readAsDataURL(file);

    reader.onload = function () {
        $.post("persona/actualizarimagen",{norden:mnorden, image:reader.result},function(result){
            console.log(result); 
            if(result.result == 1){

                $("#fileModal").modal("hide");
                $("#file_dni").val(0);
                $("#imgU"+result.norden).attr('src',result.ruta+'?'+Math.random(100) );
                //$("#cargoID"+result.norden).hide("slow");
                //$("#cargoID"+result.norden).html("<span class='btn btn-info btn-md' title=\"Ver nota de cargo\" onClick='verImagen(\""+result.ruta+"\")' > <i class=\"fa fa-image\"></i> </span>");
                //$("#cargoID"+result.norden).show("slow");

            }
        });
    };

}

openPlantilla=function(rol_id, area_id,dni,tamano,tipo){
    window.open("documentodig/vistauserqr/"+rol_id+"/"+area_id+"/"+dni+"/"+tamano+"/"+tipo,
                "PrevisualizarPlantilla",
                "toolbar=no,menubar=no,resizable,scrollbars,status,width=900,height=700");
};

openImagen=function(rol_id, area_id,dni,tamano,tipo){    
    window.open("documentodig/crearcarnetqr/"+rol_id+"/"+area_id+"/"+dni+"/"+tamano+"/"+tipo,
                "PrevisualizarCarnet",
                "toolbar=no,menubar=no,resizable,scrollbars,status,width=700,height=400");
    //popup = window.open();
    //popup.document.write("<img src='documentodig/crearcarnetqr/"+area_id+"/"+dni+"/"+tamano+"/"+tipo>"'>");
    
    //popup.document.write("<img src='http://proceso.munindependencia.pe/documentodig/crearcarnetqr/"+area_id+"/"+dni+"/"+tamano+"/"+tipo+"'>")    
    /*popup.document.write("<img src='http://localhost/ingind/public/documentodig/crearcarnetqr/"+area_id+"/"+dni+"/"+tamano+"/"+tipo+"'>")    
    popup.focus(); //required for IE
    popup.print();*/
};

openPlantillaArea=function(rol_id, area_id,tamano,tipo){
    window.open("documentodig/vistatodosuserqr/"+rol_id+"/"+area_id+"/"+tamano+"/"+tipo,
                "PrevisualizarPlantilla",
                "toolbar=no,menubar=no,resizable,scrollbars,status,width=1200,height=700");
};




eventoSlctGlobalSimple=function(slct,valores){
};

function justNumbers(e)
{
  var keynum = window.event ? window.event.keyCode : e.which;
  if ((keynum == 8) || (keynum == 46))
    return true;
  
  return /\d/.test(String.fromCharCode(keynum));
}
</script>
