<script type="text/javascript">
var cabeceraG=[]; // Cabecera del Datatable
var columnDefsG=[]; // Columnas de la BD del datatable
var targetsG=-1; // Posiciones de las columnas del datatable
var CantidadG = 0;
$(document).ready(function() {

    UsuarioId='<?php echo Auth::user()->id; ?>';
    DataUser = '<?php echo Auth::user(); ?>';
    UsuarioLocalId='<?php echo trim(Auth::user()->local_id); ?>';
    //poblateData('x',DataUser);
    /*Inicializar tramites*/
    var data={'persona':UsuarioId,'estado':1, 'hoy':1};
    Bandeja.MostrarTramites(data,HTMLTramite);
    /*end Inicializar tramites*/
    slctGlobal.listarSlct('area','slct_areas','simple',null,{estado:1, areapersona:1, areagestion:1});
    slctGlobal.listarSlct('area','slct_areast','simple',null,{estado:1, areagestion:1});
    //slctGlobal.listarSlct('area','slct_areas_total','multiple',null,{estado:1});
    /*data = {estado:1, tipo:'Ingreso', solicitante: 'Interno'};
    slctGlobal.listarSlct('documento','slct_documento_id','simple',null,data);*/
    /*inicializate selects*/
    
    slctGlobal.listarSlctFuncion('local','listarlocales','slct_local_origen_id','simple',UsuarioLocalId,{estado:1, usuario_local:1});
    
    /*end inicializate selects*/
    data = {estado:1};
    var ids = [];
    
    $('.chk').on('ifChanged', function(event){
        $("#slct_areas_total").multiselect('selectAll',false);
        if(event.target.checked == true){
            $("#txt_numareas").prop('disabled',true);
            $("#txt_numareas").val('');
            let html= "<tr>";
            html+=      "<td class='text-center'>";
            html+=          "Todas las áreas";
            html+=      "</td>";
            html+= "</tr>";
            $("#tb_numareas").html(html);
        }else{
            $("#txt_numareas").prop('disabled',false);
            $("#tb_numareas").html('');
            $("#txt_numareas").focus();
        }
    });

    $('.chk2').on('ifChanged', function(event){
        let cantidad = document.querySelector("#txt_numareas").value;
        let r = 0;
        if(event.target.checked == true){
            r = 1;
        }
        
        for(let i=0 ; i<cantidad ; i++){
            $("#rpta"+i).val(r);
            $("#chk_rpta"+i).removeAttr("checked");
            if( r == 1 ){
                $("#chk_rpta"+i).prop("checked",true);
            }
        }
    });


    $("#btnReferido").click( ()=>{
        $("#referenteModal").modal('show');
    })

    $("#btn_agregar").click( ()=>{
        let html = '';
        const now = new Date();
        let id = now.getTime();
        html =  '<tr class="'+id+'">'+
                    "<td class='input-group'>"+
                        '<input type="text" readonly class="form-control" id="pdf_nombre'+id+'"  name="pdf_nombre[]" value="" readonly="">'+
                        '<input type="text" style="display: none;" id="pdf_archivo'+id+'" name="pdf_archivo[]">'+
                        '<div class="input-group-btn">'+
                        '<label class="btn btn-warning btn-flat">'+
                            '<i class="fa fa-file-pdf-o fa-lg" style="margin-left:5px;"></i>'+
                            '<i class="fa fa-file-excel-o fa-lg" style="margin-left:5px;"></i>'+
                            '<i class="fa fa-file-image-o fa-lg" style="margin-left:5px;"></i>'+
                            '<i class="fa fa-file-powerpoint-o fa-lg" style="margin-left:5px;"></i>'+
                            '<input type="file" style="display: none;" onchange="masterG.onImagen(event,\'#pdf_nombre'+id+'\',\'#pdf_archivo'+id+'\',\'#pdf_img'+id+'\');">'+
                        '</label>'+
                        '</div>'+
                    '</td>'+
                    "<td>"+
                        '<a><img id="pdf_img'+id+'" class="img-circle" style="height: 80px;width: 140px;border-radius: 8px;border: 1px solid grey;margin-top: 5px;padding: 8px"></a>'+
                    '</td>'+
                    '<td><a class="btn btn-danger btn-lg" onClick="EliminarArchivo('+id+');">'+
                        '<i class="fa fa-trash fa-lg"></i>'+
                    '</a></td>'+
                "</tr>";
        $("#tb_archivos").append(html);
    });
     /*validaciones*/

    $(document).on('click', '.btnEnviar', function(event) {
        generarUsuario();
    });

    $("div.solicitantes").hide();
    
    $('#referenteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
      
        var modal = $(this); //captura el modal
        var idG={   referido  :'onBlur|Referido|#DCE6F1', //#DCE6F1
                    id        :'1|[]|#DCE6F1', //#DCE6F1
        };

        var resG=dataTableG.CargarCab(idG);
        cabeceraG=resG; // registra la cabecera
        var resG=dataTableG.CargarCol(cabeceraG,columnDefsG,targetsG,0,'referente','t_referente');
        columnDefsG=resG[0]; // registra las columnas del datatable
        targetsG=resG[1]; // registra los contadores

        $("#t_referidos").dataTable().fnDestroy();
    });

    $('#referenteModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        $("#t_referente>thead>tr:eq(0),#t_referente>tfoot>tr:eq(0)").html('');
        cabeceraG=[]; // Cabecera del Datatable
        columnDefsG=[]; // Columnas de la BD del datatable
        targetsG=-1; // Posiciones de las columnas del datatable
        $("#t_referidos").dataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": true,
            "searching": false,
        });
    });

});

EliminarArchivo = (t)=>{
    $('tr.'+t).remove();
}

validaNumeros=function(e) { // 1
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8 || tecla==0 || tecla==46) return true;//8 barra, 0 flechas desplaz
    patron = /\d/; // Solo acepta números
    te = String.fromCharCode(tecla); // 5
    return patron.test(te); // 6
}

cargarTabla = function(){
    $("#slct_areas_total").multiselect('selectAll',false);
    var cantidad = document.querySelector("#txt_numareas").value;
    $("#tb_numareas").html('');
    if(cantidad>0 && cantidad<31){
        html = '';
                
        for(var i=0 ; i<cantidad ; i++){
            html= "<tr>"+
                        "<td>"+
                            "<select class='form-control select_area' onChange='CargarLocalArea(this,"+i+")' id='select_area"+i+"' name='slct_area[]'></select>"+
                         "</td>"+
                        "<td>"+
                            "<select class='form-control select_local_destino' id='select_local_destino"+i+"' name='slct_local_destino["+i+"][]' multiple></select>"+
                         "</td>"+
                         "<td>"+
                            "<input type='hidden' name='rpta[]' id='rpta"+i+"' value='0'>"+
                            //"<label><input class='form-control' type='checkbox' id='chk_rpta"+i+"' onChange='ValidaRpta("+i+",this);'> Requiere Rpta?</label>"+
                         "</td>"+
                    "</tr>";
            $("#tb_numareas").append(html);
            $("#select_area"+i).html( $("#slct_areast").html() );
            slctGlobalHtml('select_area'+i,'simple');
            //slctGlobal.listarSlct('area','select_area'+i+'','simple',null,{estado:1});
        }
    }
    else {
        $("#txt_numareas").val('');
    }
}

ValidaRpta = (i,v) =>{
    $("#rpta"+i).val(0);
    if( $(v).is(":checked") ){
        $("#rpta"+i).val(1);
    }
}

CargarLocalArea = (t, i)=>{
    slctGlobal.listarSlctFuncion('local','listarlocales','select_local_destino'+i,'multiple',null,{estado:1, area_local_id: t.value});
}

eventoSlctGlobalSimple=function(slct,valores){
    /*if( slct=="slct_areas" ){
        
    }*/
    if( slsct = "slct_local_destino_id" ){
        console.log(slct, $("#"+slct).val());

    }
}


MostrarAjax=function(t){
    if( t=="referente" ){
        if( columnDefsG.length>0 ){
            dataTableG.CargarDatos(t,'referido','cargar',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    } 
};

GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
   if(typeof(fn)!='undefined' && fn.col==1){
      var estadohtml='';
      estadohtml='<span id="'+row.id+'" onClick="SeleccionaReferido(\''+row.id+'\',\''+row.ruta_id+'\',\''+row.tabla_relacion_id+'\',\''+row.ruta_detalle_id+'\',\''+row.referido+'\')" class="btn btn-success">Seleccionar</span>';
      return estadohtml;
  }
};

SeleccionaReferido = (id, ruta_id, tabla_relacion_id, ruta_detalle_id, referido) => {
    validarRegistro = false;
    if( $.trim($("#r"+id).html()) != '' || $.trim($("#r"+id).html()) != '' ){
        validarRegistro = true;
    }
    
    if( validarRegistro == false ){
        html=   '<tr id="r'+id+'">'+
                '<td>'+referido+
                    '<input type="hidden" value="'+tabla_relacion_id+'" name="tabla_relacion_id_ref[]">'+
                    '<input type="hidden" value="'+ruta_id+'" name="ruta_id_ref[]">'+
                    '<input type="hidden" value="'+ruta_detalle_id+'" name="ruta_detalle_id_ref[]">'+
                '</td>'+
                '<td><span class="btn btn-danger btn-sm" onClick="EliminarTr(\'r'+id+'\',\'referidos\')"><i class="fa fa-trash"></i></span></td>'
            '</tr>';
        $("#tb_referidos").append(html);
    }
    else{
        msjG.mensaje("warning", 'Referido ya fue seleccionado!',3000);
    }
}

HTMLTramite = function(data){
    if(data){
        var html ='';
        $.each(data,function(index, el) {
            html+="<tr>";
            html+=    "<td>"+el.idtramite +"</td>";
            html+=    "<td>"+el.usuario+"</td>";
            
            if(el.empresa){
                html+=    "<td>"+el.empresa+"</td>";
            }else{
                html+=    "<td>&nbsp;</td>";
            }
            
            html+=    "<td>"+el.solicitante+"</td>";
            html+=    "<td>"+el.tipotramite+"</td>";
            html+=    "<td>"+el.tipodoc+"</td>";
            html+=    "<td>"+el.tramite+"</td>";
            html+=    "<td>"+el.fecha+"</td>";
            var url = "documentodig/ticket/"+el.idtramite;
            html+=    '<td><span class="btn btn-primary btn-sm" id-tramite="'+el.tramite+'" onclick="imprimirTicket(\''+url+'\')"><i class="glyphicon glyphicon-search"></i></span></td>';
            html+="</tr>";
        });
        $("#tb_reporte").html(html);
    }else{
        msjG.mensaje("warning", 'No hay registros',3000);
    }
}

function imprimirTicket(url){
    parametrosPop="height=600,width=350,toolbar=No,location = No,scrollbars=yes,left=-15,top=800,status=No,resizable= No,fullscreen =No'";
    printTicket=window.open(url,'tTicket',parametrosPop);
    printTicket.focus();  
}

EliminarTr = (t, idname) =>{
    $("#t_"+idname).dataTable().fnDestroy();
    $("#"+t).remove();
    $("#t_"+idname).dataTable({
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "ordering": true,
        "searching": false,
    });
}

generarPreTramite = function(){
    if($("#slct_areas").val()==''){
        msjG.mensaje("warning", 'Selecciona Área de origen',3000);
    }
    else if($("#slct_local_origen_id").val()==''){
        msjG.mensaje("warning", 'Selecciona Local de origen',3000);
    }
    else if( $("#txt_numareas").val()=='' && !$("#chk_todasareas").is(':checked') ){
        msjG.mensaje("warning", 'Ingrese número de áreas a comunicar',3000);
    }
    else if( $("#txt_observacion").val()=='' ){
        msjG.mensaje("warning", 'Ingrese la sumilla',3000);
    }
    else{
        //$("#t_usuarios").dataTable().fnDestroy();
        let alerta = false;
        let index = 1;
        $("#tb_numareas").find("select").each( function(){
            if( alerta == false && $.trim( $(this).val() ) == '' ){
                msjG.mensaje("warning", 'Seleccione el área y su lugar de procedencia a comunicar',3000);
                $(this).focus();
                alerta = true;
            }
            index++;
        })

        if( alerta == false ){
            $("#t_referidos").dataTable().fnDestroy();        
            datos=$("#FormCrearPreTramite").serialize().split("txt_").join("").split("slct_").join("");
            Bandeja.GuardarPreTramite(datos);

        }
       
    }
}

</script>
