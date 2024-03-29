<script type="text/javascript">
var cabeceraG1=[]; // Cabecera del Datatable
var columnDefsG1=[]; // Columnas de la BD del datatable
var targetsG1=-1; // Posiciones de las columnas del datatable
var ClasificadorTramitesG={
        id:0,
        nombre:"",
        tipo_tramite:"",
        valida_pendiente: 1,
        estado:1
        }; // Datos Globales
$(document).ready(function() {
    /*  1: Onblur ,Onchange y para número es a travez de una función 1: 
        2: Descripción de cabecera
        3: Color Cabecera
    */
    var datos={estado:1};
    slctGlobal.listarSlct('tipotramite','slct_tipo_tramite','simple',null,datos);
    data = {estado:1, tipo:'Ingreso', solicitante: 'Interno'};
    slctGlobal.listarSlct('documento','slct_documento_id','simple',null,data); 
    slctGlobalHtml('slct_estado_clasificador, #slct_unidad_documentaria, #slct_valida_pendiente','simple');
    var idG1={  tipo_tramite        :'3|TIpoTramite|#DCE6F1', //#DCE6F1
                nombre        :'onBlur|Nombre|#DCE6F1', //#DCE6F1
                documento  :'3|Documento|#DCE6F1', //#DCE6F1
                estado        :'2|Estado|#DCE6F1', //#DCE6F1
                a          :'1|  |#DCE6F1',
                b          :'1|  |#DCE6F1',
                c          :'1|  |#DCE6F1',
                d          :'1|  |#DCE6F1',
                e          :'1|  |#DCE6F1',
                f          :'1|  |#DCE6F1',
                g          :'1|  |#DCE6F1',
                h          :'1|  |#DCE6F1',
                i          :'1|  |#DCE6F1',
             };

    var resG1=dataTableG.CargarCab(idG1);
    cabeceraG1=resG1; // registra la cabecera
    var resG1=dataTableG.CargarCol(cabeceraG1,columnDefsG1,targetsG1,1,'clasificadortramites','t_clasificadortramites');
    columnDefsG1=resG1[0]; // registra las columnas del datatable
    targetsG1=resG1[1]; // registra los contadores
    //var resG1=dataTableG.CargarBtn(columnDefsG1,targetsG1,1,'BtnEditar','t_clasificadortramites','fa-edit');
    //columnDefsG1=resG1[0]; // registra la colunmna adiciona con boton
    //targetsG1=resG1[1]; // registra el contador actualizado
    MostrarAjax('clasificadortramites');


    $('#clasificadortramitesModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-

      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Clasificador Tramite');
      $('#form_clasificadortramites_modal [data-toggle="tooltip"]').css("display","none");
      $("#form_clasificadortramites_modal input[type='hidden']").remove();

        if(titulo=='Nuevo'){
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_clasificadortramites_modal #slct_estado_clasificador').val(1);
            $('#form_clasificadortramites_modal #txt_nombre').focus();
            $('#form_clasificadortramites_modal #slct_tipo_tramite').val('' );
            $('#form_clasificadortramites_modal #slct_documento_id').val('' );
            $('#form_clasificadortramites_modal #slct_valida_pendiente').val(1);
        } else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');

            $('#form_clasificadortramites_modal #txt_nombre').val( ClasificadorTramitesG.nombre );
            $('#form_clasificadortramites_modal #slct_tipo_tramite').val( ClasificadorTramitesG.tipo_tramite );
            $('#form_clasificadortramites_modal #slct_documento_id').val( ClasificadorTramitesG.documento_id );
            $('#form_clasificadortramites_modal #slct_valida_pendiente').val( ClasificadorTramitesG.valida_pendiente );

            $('#form_clasificadortramites_modal #slct_estado_clasificador').val( ClasificadorTramitesG.estado );
            $("#form_clasificadortramites_modal").append("<input type='hidden' value='"+ClasificadorTramitesG.id+"' name='id'>");
           

        }
           
             $('#form_clasificadortramites_modal select').multiselect('rebuild');

    });

    $('#clasificadortramitesModal').on('hide.bs.modal', function (event) {
       $('#form_clasificadortramites_modal input').val('');
       $('#form_clasificadortramites_modal textarea').val('');
     //   var modal = $(this);
       // modal.find('.modal-body input').val('');
    });
});

BtnEditar=function(btn,id){
    var tr = btn.parentNode.parentNode; // Intocable
    ClasificadorTramitesG.id=id;
    ClasificadorTramitesG.nombre=$(tr).find("td:eq(1)").text();
    ClasificadorTramitesG.tipo_tramite=$(tr).find("td:eq(0) input[name='txt_tipo_tramite']").val();
    ClasificadorTramitesG.documento_id=$(tr).find("td:eq(2) input[name='txt_documento_id']").val();
    ClasificadorTramitesG.valida_pendiente=$(tr).find("td .valida_pendiente").val();
    ClasificadorTramitesG.estado=$(tr).find("td:eq(3)>span").attr("data-estado");
    $("#BtnEditar_clasificador").click();
};

MostrarAjax=function(t){
    if( t=="clasificadortramites" ){
        if( columnDefsG1.length>0 ){
            dataTableG.CargarDatos(t,'clasificadortramite','cargar',columnDefsG1);
        }
        else{
            alert('Faltas datos');
        }
    }
        if( t=="proceso" ){
        if( columnDefsG.length>0 ){
            dataTableG.CargarDatos(t,'flujo','listarproceso',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    }
}

GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
    if(typeof(fn)!='undefined' && fn.col==0){
        return row.tipo_tramite+"<input type='hidden'name='txt_tipo_tramite' value='"+row.tipo_tramite_id+"'>";
    }

    if(typeof(fn)!='undefined' && fn.col==1){
        var estadohtml='';
        estadohtml='<span id="'+row.id+'" onClick="CargarProceso(\''+row.id+'\',\''+row.nombre+'\',\''+row.area_id+'\',\''+row.area+'\')" class="btn btn-success"><i class="fa fa-lg fa-check"></i></span>';
        return estadohtml;
    }

    if(typeof(fn)!='undefined' && fn.col==2){
        return row.documento+"<input type='hidden'name='txt_documento_id' value='"+row.documento_id+"'>";
    }

    if(typeof(fn)!='undefined' && fn.col==3){
        var estadohtml='';
        estadohtml='<span id="'+row.id+'" onClick="activar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-danger">Inactivo</span>';
        if(row.estado==1){
            estadohtml='<span id="'+row.id+'" onClick="desactivar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-success">Activo</span>';
        }
        return estadohtml;
    }

    if(typeof(fn)!='undefined' && fn.col==4){
        var grupo='';
        grupo+= '<span id="'+row.id+'" title="Asignar Proceso" onClick="CargarActividad(\''+row.id+'\',\''+row.nombre.split('"').join("-")+'\',this)" data-estado="'+row.estado+'" class="btn btn-info"><i class="glyphicon glyphicon-list-alt"></i></span>';
        return grupo;
    }

    if(typeof(fn)!='undefined' && fn.col==5  && row.ruta_flujo_id!==null){
        var grupo='';
        grupo+= '<a onclick="cargarRutaId('+row.ruta_flujo_id+',2)" class="btn btn-warning btn-sm"><i class="fa fa-search-plus fa-lg"></i> </a>';
        return grupo;
    }

    if(typeof(fn)!='undefined' && fn.col==6){
        var grupo='';
        grupo+= '<span id="'+row.id+'" title="Datos del Servicio" onClick="CargarCampos(\''+row.id+'\',\''+row.nombre+'\',this)" data-estado="'+row.estado+'" class="btn bg-teal"><i class="glyphicon glyphicon-folder-open"></i></span>';
        return grupo;
    }

    if(typeof(fn)!='undefined' && fn.col==7){
        var grupo='';
        grupo+= '<span id="'+row.id+'" title="Asignación de Datos del Servicio" onClick="AsignarCampos(\''+row.id+'\',\''+row.ruta_flujo_id+'\',\''+row.nombre+'\',this)" data-estado="'+row.estado+'" class="btn btn-default"><i class="glyphicon glyphicon-wrench"></i></span>';
        return grupo;
    }

    if(typeof(fn)!='undefined' && fn.col==8){
        var grupo='';
        grupo+= '<span id="'+row.id+'" title="Eventos del Servicio" onClick="EventosServicio(\''+row.id+'\',\''+row.ruta_flujo_id+'\',\''+row.nombre+'\',this)" data-estado="'+row.estado+'" class="btn bg-navy"><i class="fa fa-stack-overflow"></i></span>';
        return grupo;
    }

    if(typeof(fn)!='undefined' && fn.col==9){
        var grupo='';
        grupo+= '<span id="'+row.id+'" title="(Finalizar / Anular) Proceso" onClick="FinalizarAnular(\''+row.id+'\',\''+row.ruta_flujo_id+'\',\''+row.nombre+'\',this)" data-estado="'+row.estado+'" class="btn btn-info"><i class="fa fa-cog"></i></span>';
        return grupo;
    }

    if(typeof(fn)!='undefined' && fn.col==10){
        var grupo='';
        grupo+= '<span id="'+row.id+'" title="Requisitos" onClick="CargarCostoPersonal(\''+row.id+'\',\''+row.nombre+'\',this)" data-estado="'+row.estado+'" class="btn btn-info"><i class="glyphicon glyphicon-ok"></i></span>';
        return grupo;
    }

    if(typeof(fn)!='undefined' && fn.col==11){
        var estadohtml='';
        var pendiente = '<input type="hidden" class="valida_pendiente" name="valida_pendiente" value="'+row.valida_pendiente+'">';
        estadohtml='<span id="'+row.id+'" onClick="activarEF('+row.id+')" data-estado="'+row.estado_final+'" class="btn btn-danger">Pendiente</span>'+pendiente;
        if(row.estado_final==1){
            estadohtml='<span id="'+row.id+'" onClick="desactivarEF('+row.id+')" data-estado="'+row.estado_final+'" class="btn btn-success">Producción</span>'+pendiente;
        }
        return estadohtml;
    }

    if(typeof(fn)!='undefined' && fn.col==12){
        var estadohtml='<a class="form-control btn btn-primary" onclick="BtnEditar(this,'+row.id+')"><i class="fa fa-lg fa-edit"></i></a>';
        return estadohtml;
    }
}

activarEF = function(id){
    ClasificadorTramites.CambiarEstadoEFClasificadorTramites(id, 1);
};

desactivarEF = function(id){
    ClasificadorTramites.CambiarEstadoEFClasificadorTramites(id, 0);
};

activarTabla=function(){
    $("#t_clasificadortramites").dataTable(); // inicializo el datatable    
};

activar = function(id){
    ClasificadorTramites.CambiarEstadoClasificadorTramites(id, 1);
};
desactivar = function(id){
    ClasificadorTramites.CambiarEstadoClasificadorTramites(id, 0);
};
Editar = function(){
    if(validaClasificadorTramites()){
        ClasificadorTramites.AgregarEditarClasificadorTramite(1);
    }
};
Agregar = function(){
    if(validaClasificadorTramites()){
        ClasificadorTramites.AgregarEditarClasificadorTramite(0);
    }
};
ActPest=function(nro){
    Pest=nro;
};
validaClasificadorTramites = function(){
    var r=true;
    
    if( $("#form_clasificadortramites_modal #txt_nombre").val()=='' ){
        alert("Ingrese Nombre de Clasificador Tramite");
        r=false;
    }
    else if( $("#form_clasificadortramites_modal #slct_tipo_tramite").val()=='' ){
        alert("Seleccione Tipo de Trámite");
        r=false;
    }
    else if( $("#form_clasificadortramites_modal #slct_unidad_documentaria").val()=='' ){
        alert("Seleccione Unidad Documentaria");
        r=false;
    }

    return r;
};

eventoFG=function(evento){
    if(evento=='cargarRutaFlujo'){
        var flujo_id=$.trim($("#txt_flujo2_id").val());
        var area_id=$.trim($("#txt_area2_id").val());

        if( flujo_id!='' && area_id!='' ){
            var datos={ flujo_id:flujo_id,area_id:area_id };
            $("#tabla_ruta_flujo").css("display","");
            Asignar.mostrarRutaFlujo(datos,mostrarRutaFlujoHTML);
        }
    }
};

mostrarRutaFlujoHTML=function(datos){
    var html="";
    var cont=0;
    var botton="";
    var color="";
    var clase="";
     $('#t_ruta_flujo').dataTable().fnDestroy();
     $("#txt_ruta_flujo_id").remove();
    $.each(datos,function(index,data){
        imagen="";
        clase="";
        cont++;
        if(cont==1){
            $("#form_actividad").append('<input type="hidden" id="txt_ruta_flujo_id" name="txt_ruta_flujo_id" value="'+data.id+'">');
            
            imagen="<a id='ruta_flujo_id' data-id='"+data.id+"' class='btn btn-success btn-sm'><i class='fa fa-check-square fa-lg'></i></a>";
        }
    html+="<tr>"+
        "<td>"+cont+"</td>"+
        "<td>"+data.flujo+"</td>"+
        "<td>"+data.area+"</td>"+
        "<td>"+data.persona+"</td>"+
//        "<td>"+data.ok+"</td>"+
//        "<td>"+data.error+"</td>"+
        "<td>"+data.fruta+"</td>"+
        "<td>"+imagen+
            '<a onclick="cargarRutaId('+data.id+',2)" class="btn btn-warning btn-sm"><i class="fa fa-search-plus fa-lg"></i> </a>'+
        "</td>";
    html+="</tr>";
    });
    $("#tb_ruta_flujo").html(html); 
    $('#t_ruta_flujo').dataTable({
        "ordering": false
    });
};
</script>