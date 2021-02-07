<script type="text/javascript">
var cabeceraG=[]; // Cabecera del Datatable
var columnDefsG=[]; // Columnas de la BD del datatable
var targetsG=-1; // Posiciones de las columnas del datatable
var DocumentosG={id:0,nombre:"", tipo:"",area:"", posicion:"", posicion_fecha:"", estado:1}; // Datos Globales
$(document).ready(function() {
    /*  1: Onblur ,Onchange y para número es a travez de una función 1: 
        2: Descripción de cabecera
        3: Color Cabecera
    */

    slctGlobalHtml('slct_area,#slct_posicion,#slct_posicion_fecha,#slct_estado,#slct_tipo,#slct_pide_nro, #slct_solicitante, #slct_publico','simple');
    slctGlobal.listarSlctFuncion('area','listara','slct_area_id','simple',null,{estado:1,areapersona:1});
    var idG={   nombre           :'onBlur|Nombre del Documento|#DCE6F1', //#DCE6F1
                nemonico         :'onBlur|Nemónico|#DCE6F1', //#DCE6F1
                tipos            :'4|Visible|#DCE6F1||tipo', //#DCE6F1
                areas            :'onBlur|Área|#DCE6F1', //#DCE6F1
                estado           :'2|Estado|#DCE6F1', //#DCE6F1
             };

    var resG=dataTableG.CargarCab(idG);
    cabeceraG=resG; // registra la cabecera
    var resG=dataTableG.CargarCol(cabeceraG,columnDefsG,targetsG,1,'documentos','t_documentos');
    columnDefsG=resG[0]; // registra las columnas del datatable
    targetsG=resG[1]; // registra los contadores
    var resG=dataTableG.CargarBtn(columnDefsG,targetsG,1,'BtnEditar','t_documentos','fa-edit');
    columnDefsG=resG[0]; // registra la colunmna adiciona con boton
    targetsG=resG[1]; // registra el contador actualizado
    MostrarAjax('documentos');


    $('#documentoModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-

      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Documento');
      $('#form_documentos_modal [data-toggle="tooltip"]').css("display","none");
      $("#form_documentos_modal input[type='hidden']").remove();
        if(titulo=='Nuevo'){
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_documentos_modal #slct_estado').val(1);
            $('#form_documentos_modal #slct_pide').val(0);
            $('#form_documentos_modal #txt_nombre').focus();
        } else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');

            $('#form_documentos_modal #txt_nombre').val( DocumentosG.nombre );
            $('#form_documentos_modal #txt_nemonico').val( DocumentosG.nemonico );
            $('#form_documentos_modal #slct_tipo').val( DocumentosG.tipo );
            $('#form_documentos_modal #slct_publico').val( DocumentosG.publico );
            $('#form_documentos_modal #slct_area_id').val( DocumentosG.area_id );
            $('#form_documentos_modal #slct_estado').val( DocumentosG.estado );
            $('#form_documentos_modal #slct_solicitante').val( DocumentosG.solicitante );
            $('#form_documentos_modal #slct_pide_nro').val( DocumentosG.pide_nro );
            $("#form_documentos_modal").append("<input type='hidden' value='"+DocumentosG.id+"' name='id'>");
        }
             $('#form_documentos_modal select').not(".mant").multiselect('rebuild');
             validarSolicitante();
    });

    $('#documentoModal').on('hide.bs.modal', function (event) {
       $('#form_documentos_modal input').val('');
       $('#form_documentos_modal select').not('.mant').val('');

     //   var modal = $(this);
       // modal.find('.modal-body input').val('');
    });
});

validarSolicitante = ()=>{
    $(".solicitante, .publico").hide();
    if( $("#slct_tipo").val() == 'Ingreso' ){
        $(".solicitante").show();
    }
    else{
        $(".publico").show();
    }
}

BtnEditar=function(btn,id){
    var tr = btn.parentNode.parentNode; // Intocable
    DocumentosG.id=id;
    DocumentosG.nombre=$(tr).find("td:eq(0)").text();
    DocumentosG.nemonico=$(tr).find("td:eq(1)").text();
    DocumentosG.tipo=$(tr).find("td:eq(2) input[name='slct_tipo']").val();
    DocumentosG.publico=$(tr).find("td:eq(2) input[name='slct_publico']").val();
    DocumentosG.area_id=$(tr).find("td:eq(2) input[name='slct_area_id']").val();
    DocumentosG.solicitante=$(tr).find("td:eq(2) input[name='slct_solicitante']").val();
    DocumentosG.pide_nro=$(tr).find("td:eq(2) input[name='slct_pide_nro']").val();
    DocumentosG.estado=$(tr).find("td:eq(4)>span").attr("data-estado");
    $("#BtnEditar").click();
};

MostrarAjax=function(t){
    if( t=="documentos" ){
        if( columnDefsG.length>0 ){
            console.log(columnDefsG);
            dataTableG.CargarDatos(t,'documento','cargar',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    }
}

GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
        //console.log(row);
    if(typeof(fn)!='undefined' && fn.col==2){
        return row.tipos+"<input type='hidden' name='slct_tipo' value='"+row.tipo+"'>"+
                "<input type='hidden' name='slct_publico' value='"+$.trim(row.publico)+"'>"+
                "<input type='hidden' name='slct_area_id' value='"+$.trim(row.area_id)+"'>"+
                "<input type='hidden' name='slct_solicitante' value='"+$.trim(row.solicitante)+"'>"+
                "<input type='hidden' name='slct_pide_nro' value='"+$.trim(row.pide_nro)+"'>";
    }
    if(typeof(fn)!='undefined' && fn.col==4){
        var estadohtml='';
        estadohtml='<span id="'+row.id+'" onClick="activar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-danger">Inactivo</span>';
        if(row.estado==1){
            estadohtml='<span id="'+row.id+'" onClick="desactivar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-success">Activo</span>';
        }
        return estadohtml;
    }
}


activarTabla=function(){
    $("#t_documentos").dataTable(); // inicializo el datatable    
};

activar = function(id){
    Documentos.CambiarEstadoDocumentos(id, 1);
};
desactivar = function(id){
    Documentos.CambiarEstadoDocumentos(id, 0);
};
Editar = function(){
    if(validaDocumentos()){
        Documentos.AgregarEditarDocumento(1);
    }
};
Agregar = function(){
    if(validaDocumentos()){
        Documentos.AgregarEditarDocumento(0);
    }
};

validaDocumentos = function(){
    var r=true;
    if( $("#form_documentos_modal #txt_nombre").val()=='' ){
        alert("Ingrese Nombre del Documento");
        r=false;
    }
    else if( $("#form_documentos_modal #txt_nemonico").val()=='' ){
        alert("Ingrese Nemónico del Documento");
        r=false;
    }
    else if( $("#form_documentos_modal #slct_tipo").val()=='' ){
        alert("Seleccione Tipo documento");
        r=false;
    }
    else if( $("#form_documentos_modal #slct_tipo").val() == 'Ingreso' &&  $("#form_documentos_modal #slct_solicitante").val()=='' ){
        alert("Seleccione solicitante");
        r=false;
    }

    else if( $("#form_documentos_modal #slct_tipo").val() == 'Salida' &&  $("#form_documentos_modal #slct_publico").val()=='' ){
        alert("Seleccione si es documento público");
        r=false;
    }

    /*else if( $("#form_documentos_modal #slct_area_id").val()=='' ){
        alert("Seleccione Area");
        r=false;
    }*/
    /*else if( $("#form_documentos_modal #slct_posicion").val()=='' ){
        alert("Seleccione Posicion");
        r=false;
    }
    else if( $("#form_documentos_modal #slct_posicion_fecha").val()=='' ){
        alert("Seleccione Posicion Fecha");
        r=false;
    }*/
    return r;
};
</script>
