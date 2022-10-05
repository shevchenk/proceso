<script type="text/javascript">
var cabeceraG=[]; // Cabecera del Datatable
var columnDefsG=[]; // Columnas de la BD del datatable
var targetsG=-1; // Posiciones de las columnas del datatable

var CostoPersonalG={id:0,nombre:"",cantidad:"",estado:1, ruta_archivo:""}; // Datos Globales
var EstratPeiG={id:0,descripcion:"",estado:1}; // Datos Globales

    // Datos Globales
$(document).ready(function() {
    /*  1: Onblur ,Onchange y para número es a travez de una función 1: 
        2: Descripción de cabecera
        3: Color Cabecera
    */
    //$( "#add_campo" ).sortable();
    $(".FormG").hide();
    $('#add_campo').sortable({
        dragClass: 'highlight',
        animation: 150
    });
    $('#add_campo2').sortable({
        dragClass: 'highlight',
        animation: 150
    })


    //$( "#add_campo" ).disableSelection();
       data = {estado:1};
    var ids = [];
    slctGlobal.listarSlct('software','slct_software_id_modal','simple',ids,data);
    slctGlobal.listarSlct2('rol','slct_rol_modal',data);
    slctGlobal.listarSlct2('verbo','slct_verbo_modal',data);
    slctGlobal.listarSlct2('documento','slct_documento_modal',data);
    //////////////////////////////////////////////////////////////////
    $("#btn_close, .btn_close").click(Close);
    $(".btn_add_campo").click(AddCampo);
    $(".btn_campo_evento").click(AddCampoEvento);
    $(".btn_guardar_evento").click(GuardarEvento);
    
    //$(".btn_asig_campo").click(AsigCampo);
    $("#slct_campo").change(CambioCampo); $(".sub_titulo").hide();
    $("#btn_RegistrarCampos").click(RegistrarCampos);
    $("#btn_AsignarCampos").click(AsignarFCampos);
    slctGlobalHtml('form_campo_asignacion #slct_campos, #form_campo_asignacion #slct_areas','multiple');

    //////////////////////////////////////////////////////////////////
    slctGlobalHtml('form_tipotramites_modal #slct_estado','simple');
    slctGlobalHtml('form_requisitos_modal #slct_estado','simple');
    CargarEstratPei();

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
    $('#requisitoModal').on('show.bs.modal', function (event) { 
        
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-

      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Requisito');
      $('#form_requisitos_modal [data-toggle="tooltip"]').css("display","none");
//      $("#form_requisitos_modal input[type='hidden']").remove();
        ruta_archivo = '';
        $('#form_requisitos_modal #pdf_archivo').val('');
        $('#form_requisitos_modal #id').remove();
        if(titulo=='Nuevo'){
            //$("#form_requisitos_modal").append("<input type='hidden' value='263' name='txt_contratacion_id'>");
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','AgregarCostoPersonal();');
            $('#form_requisitos_modal #slct_estado').val(1);
            $('#form_requisitos_modal #txt_nombre').focus();
        } else {
            ruta_archivo = CostoPersonalG.ruta_archivo;
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','EditarCostoPersonal();');

            $('#form_requisitos_modal #txt_nombre').val( CostoPersonalG.nombre );
            $('#form_requisitos_modal #txt_cantidad').val( CostoPersonalG.cantidad );
            $('#form_requisitos_modal #slct_estado').val( CostoPersonalG.estado );
            $('#form_requisitos_modal #pdf_nombre').val( ruta_archivo );
            $("#form_requisitos_modal").append("<input type='hidden' value='"+CostoPersonalG.id+"' name='id' id='id'>");
        }
        masterG.SelectImagen(ruta_archivo,"#pdf_img","#pdf_href");
             $('#form_requisitos_modal select').multiselect('rebuild');
    });
    
    $('#requisitoModal').on('hide.bs.modal', function (event) {
       $('#requisitoModal :visible').val('');
       $('#requisitoModal textarea').val('');
        $('#requisitoModal select').val('');
     //   var modal = $(this);
       // modal.find('.modal-body input').val('');
    });
    
    $('#tipotramiteModal').on('show.bs.modal', function (event) { 
        
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-

      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Tipo de Trámite');
      $('#form_tipotramites_modal [data-toggle="tooltip"]').css("display","none");
//      $("#form_tipotramites_modal input[type='hidden']").remove();
 
        if(titulo=='Nueva'){
            //$("#form_tipotramites_modal").append("<input type='hidden' value='263' name='txt_contratacion_id'>");
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','AgregarEstratPei();');
            $('#form_tipotramites_modal #slct_estado').val(1);
            $('#form_tipotramites_modal #txt_descripcion').focus();
           
        } else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','EditarEstratPei();');

            $('#form_tipotramites_modal #txt_nombre').val( EstratPeiG.descripcion );
            $('#form_tipotramites_modal #slct_estado').val( EstratPeiG.estado );
            $("#form_tipotramites_modal").append("<input type='hidden' value='"+EstratPeiG.id+"' name='id'>");
            
          
        }
             $('#form_tipotramites_modal select').multiselect('rebuild');
    });
    
    $('#tipotramiteModal').on('hide.bs.modal', function (event) {
       $('#tipotramiteModal :visible').val('');
        $('#tipotramiteModal select').val('');
     //   var modal = $(this);
       // modal.find('.modal-body input').val('');
    });
     $("#btn_guardar_todo").click(guardarasignacion);
    
});

Limpiar = (v)=>{
    $(v).val('');
}

desactivarCostoPersonal = function(id){
      Pois.CambiarEstadoCostoPersonal(id, 0); 
};

activarCostoPersonal = function(id){
      Pois.CambiarEstadoCostoPersonal(id, 1);   
};

desactivarEstratPei = function(id){
      Pois.CambiarEstadoEstratPei(id, 0); 
};

activarEstratPei = function(id){
      Pois.CambiarEstadoEstratPei(id, 1);   
};

Editar = function(){
    if(validaContrataciones()){
        Pois.AgregarEditarPois(1);
        $("#form_costo_personal .form-group").css("display","none");
    }
};
Agregar = function(){
    if(validaContrataciones()){
       Pois.AgregarEditarPois(0);
       $("#form_costo_personal .form-group").css("display","none");
    }
};

validaContrataciones = function(){
    var r=true;

        if( $("#form_pois_modal #txt_objetivo_general").val()=='' ){
            alert("Ingrese Objetivo General");
            r=false;
        }
        if( $("#form_pois_modal #slct_area").val()=='' ){
            alert("Seleccione Área");
            r=false;
        }

    return r;
};

CargarCostoPersonal=function(id,titulo,boton){
    
    var tr = boton.parentNode.parentNode;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    tr.style.backgroundColor = "#9CD9DE";
    
    $(".FormG").hide();
    $("#form_costo_personal").show();
    $("#form_requisitos_modal #txt_poi_id").val(id);
    $("#form_costo_personal #txt_titulo").text(titulo);

    //$("#form_actividad .form-group, #form_campo .form-group, #form_campo_asignacion .form-group").css("display","none");
    data={id:id};
    Pois.CargarCostoPersonal(data);
};

CargarActividad=function(id,titulo,boton){
    
    var tr = boton.parentNode.parentNode;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    tr.style.backgroundColor = "#9CD9DE";
    
    $(".FormG").hide();
    $("#form_actividad").show();
    $("#form_actividad #id").val(id);
    $("#form_actividad #txt_titulo").text(titulo);
    //$("#form_costo_personal .form-group, #form_campo .form-group, #form_campo_asignacion .form-group").css("display","none");
};

CargarCampos=function(id,titulo,boton){
    
    var tr = boton.parentNode.parentNode;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    tr.style.backgroundColor = "#9CD9DE";
    
    $(".FormG").hide();
    $("#form_campo").show();
    $("#form_campo #id").val(id);
    $("#form_campo #txt_titulo").text(titulo);
    $("#add_campo, #add_campo2").html(''); // Limpia los registros para cargar los nuevos 
    Pois.ListarCampos(ListarCamposHTML);
    //$("#form_costo_personal .form-group, #form_actividad .form-group, #form_campo_asignacion .form-group").css("display","none");

};

AsignarCampos=function(id, ruta_flujo_id, titulo, boton){  
    var tr = boton.parentNode.parentNode;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    tr.style.backgroundColor = "#9CD9DE";
    
    $(".FormG").hide();
    $("#form_campo_asignacion").show();
    $("#form_campo_asignacion #id").val(id);
    $("#form_campo_asignacion #ruta_flujo_id").val(ruta_flujo_id);
    $("#form_campo_asignacion #txt_titulo").text(titulo);
    $("#add_campo3").html(''); // Limpia los registros para cargar los nuevos 
    //Pois.ListarAreas(ListarAreasHTML);
    //Pois.ListarCampos(ListarCampos2HTML, 1);
    Pois.ListarCamposAreas(ListarCamposAreasHTML);
    //$("#form_costo_personal .form-group, #form_actividad .form-group, #form_campo .form-group").css("display","none");
};

FinalizarAnular=function(id, ruta_flujo_id, titulo, boton){
    var tr = boton.parentNode.parentNode;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    tr.style.backgroundColor = "#9CD9DE";
    
    $(".FormG").hide();
    $("#form_fa").show();
    $("#form_fa #id").val(id);
    $("#form_fa #ruta_flujo_id").val(ruta_flujo_id);
    $("#form_fa #txt_titulo").text(titulo);
    $("#add_campo4").html(''); // Limpia los registros para cargar los nuevos 
    var datos = $("#form_fa").serialize().split("txt_").join("").split("slct_").join("");
    Pois.ListarCamposAreas(ListarAreasHTML, datos);
    //$("#form_costo_personal .form-group, #form_actividad .form-group, #form_campo .form-group").css("display","none");
};

EventosServicio = (id, ruta_flujo_id, titulo, boton)=>{
    var tr = boton.parentNode.parentNode;
    var trs = tr.parentNode.children;
    for(var i =0;i<trs.length;i++)
        trs[i].style.backgroundColor="#f9f9f9";
    tr.style.backgroundColor = "#9CD9DE";
    
    $(".FormG").hide();
    $("#form_evento").show();
    $("#form_evento #id").val(id);
    $("#form_evento #ruta_flujo_id").val(ruta_flujo_id);
    $("#form_evento #txt_titulo").text(titulo);
    $("#add_campo4").html(''); // Limpia los registros para cargar los nuevos 
    
    Pois.ListarCampos(ListarCamposEventoHTML, 'evento');
    Pois.ListarEventos(ListarEventosHTML);
    //$("#form_costo_personal .form-group, #form_actividad .form-group, #form_campo .form-group").css("display","none");
};

////////////////////////////////////////////////////////////////////////////////////////////////////
ListarCamposHTML = (result) => {
    $.each(result,function(index,r){
        check = false;
        if( r.obligar == 1 ){ check = true }
        AddCampo(r.id, r);
    });
}
/*
ListarCampos2HTML = (result) => {
    $("#slct_campos").html('');
    html = '';
    $.each(result, function(index, r){
        html += '<option value="'+ r.id +'">'+ r.campo +'</option>';
    })
    $("#slct_campos").html(html);
    $("#slct_campos").multiselect('rebuild');
}
*/
ListarAreasHTML = (result, ruta_flujo_id) => {
    $("#add_campo4").html('');// inicializando
    //#75DDEC, #FFF3A2, #F58DD7
    var cab = [
        {  'color': 'alert-info' },
        {  'color': 'alert-secondary'},
        {  'color': 'alert-warning' }
    ];
    //TODO: Proceso///////////////////////////////////////////////////////////////////////
    
    var html ='';
    var ruta_flujo_detalle_id = 0; var norden = 0; var id = 0;
    var style = 'style="padding-left: #em;"';
    var tab = 4;
    var child = 0;
    var parent = 0;
    var clase = '';
    var config = {
        visualizar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        modificar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        disabled : 'disabled'
    };

    var subproceso = '';
    var select =    '<select>'+
                        '<option value = "0" selected> Continuar </option>'+
                        '<option value = "1"> Finalizar </option>'+
                        '<option value = "2"> Anular </option>'+
                    '</select>';
    
    $.each(result,function(index, r) {
        config.visualizar.valor = '0';
        config.visualizar.checked = '';
        config.visualizar.funcion = 'ActivarCheck';
        config.visualizar.color = 'danger';

        config.modificar.valor = '0';
        config.modificar.checked = '';
        config.modificar.funcion = 'ActivarCheck';
        config.modificar.color = 'danger';

        config.disabled = 'disabled';

        if( $.trim(r.id) != '' && r.estado == 1 ){
            config.visualizar.valor = '1';
            config.visualizar.checked = 'checked';
            config.visualizar.funcion = 'InactivarCheck';
            config.visualizar.color = 'success';
            if( r.modificar == 1 ){
                config.modificar.checked = 'checked';
                config.modificar.funcion = 'InactivarCheck';
                config.modificar.color = 'success';
            }
            config.modificar.valor = r.modificar;
            config.disabled = '';
        }

        if(  r.ruta_flujo_detalle_id != ruta_flujo_detalle_id ){

            if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
                $("#add_campo4 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
                let sp = subproceso.split("^^");
                for (let i = 0; i < sp.length; i++) {
                    const spd = sp[i].split('^');
                    
                    clase = 'class="'+cab[2].color+' cursorZoom N'+ spd[1] + '-' + norden +'"';
                    style_aux = style.replace("#", 2);
                    html="<tr data-id='ID-"+ spd[1] + "-" + norden + "' "+clase+" ondblclick='ListarSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
                    html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
                    html+=    "<td>&nbsp;</td>";
                    html+="</tr>";
                    $("#add_campo4").append(html);
                }
            }

            ruta_flujo_detalle_id =  r.ruta_flujo_detalle_id;
            norden = r.norden;
            id = ruta_flujo_id + "-" + norden;
            clase = 'class="'+cab[0].color+'"';
            child = 1;
            
            html="<tr data-id='ID-"+ id +"' "+clase+">";
            html+=    "<td class='col-md-12' data-column=name>"+
                            "<i class=''></i><i class='fa fa-angle-right'></i>"+norden+" | "+r.area+
                      "</td>";
            html+=    "<td>"+select+"</td>";
            html+="</tr>";
            $("#add_campo4").append(html);
            $("#add_campo4 tr[data-id='ID-"+ id +"'] select").attr('onChange', 'FinalizarAnularRD(this, '+ ruta_flujo_detalle_id +')').val(r.archivado);
            subproceso = $.trim(r.sub);
        }

        /*
        if( $.trim( r.tipo ) == 0 ){
            
            clase = 'class="'+cab[1].color+' campo'+ id +"-"+ r.ruta_flujo_campo_id +'"';
            style_aux = style.replace("#", 2);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ r.campo +"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="0">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo4").append(html);
            parent = id +"-"+ r.ruta_flujo_campo_id;
            child++;
        }
        else if( child == 1 && $.trim( r.tipo ) != '' ){
            $("#add_campo4 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');

            clase = 'class="'+cab[1].color+'"';
            style_aux = style.replace("#", 2);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"S"+ "' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ "Campos Sin Título" +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo4").append(html);
            parent = id +"-"+ r.ruta_flujo_campo_id+"S";
            
            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +'"';
            style_aux = style.replace("#", 4);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo4").append(html);
            child++;
        }
        else if( $.trim( r.tipo ) != '' ){
            if( !$("#add_campo4 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").hasClass('glyphicon glyphicon-chevron-right') ){
                $("#add_campo4 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
            }

            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +'"';
            style_aux = style.replace("#", 4);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo4").append(html);
        }
        */
    });

    if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
        $("#add_campo4 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
        let sp = subproceso.split("^^");
        for (let i = 0; i < sp.length; i++) {
            const spd = sp[i].split('^');
            
            clase = 'class="'+cab[2].color+' cursorZoom N' + spd[1] + '-' + norden +'"';
            style_aux = style.replace("#", 2);
            html="<tr data-id='ID-"+ spd[1] + "-" + norden + "' "+clase+" ondblclick='ListarSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";
            $("#add_campo4").append(html);
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////

    /*tree-table*/
    $(function () {
        var $table = $('#add_campo4'),
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
}

FinalizarAnularRD = (t, ruta_flujo_detalle_id) => {
    var datos = { ruta_flujo_detalle_id: ruta_flujo_detalle_id, valor: $(t).val() };
    Pois.ActualizarRutaDetalle(datos);
}

ListarSub = (t, ruta_flujo_id, norden) => {
    $(t).removeAttr('ondblclick');
    var datos = { id: $("#form_fa #id").val(), ruta_flujo_id: ruta_flujo_id, soloareas:1, norden: norden, sub:1 };
    Pois.ListarCamposAreas(ListarSubHTML, datos);
}

ListarSubHTML = (result, nordensub, ruta_flujo_id_sub) => {
    var nordensub_aux = ruta_flujo_id_sub + "-" + nordensub.split(".").join("_");
    $("#add_campo4 [data-parent^='ID-"+nordensub_aux+"']").remove();// inicializando
    //#75DDEC, #FFF3A2, #F58DD7
    var cab = [
        {  'color': 'alert-info' },
        {  'color': 'alert-secondary'},
        {  'color': 'alert-warning' }
    ];
    //TODO: Proceso///////////////////////////////////////////////////////////////////////
    
    var html ='';
    var ruta_flujo_detalle_id = 0; var norden = 0; var id = 0;
    var style = 'style="padding-left: #em;"';
    var tab = 4;
    var child = 0;
    var parent = 0;
    var clase = '';
    var config = {
        visualizar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        modificar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        disabled : 'disabled'
    };

    var subproceso = '';
    var select =    '<select>'+
                        '<option value = "0" selected> Continuar </option>'+
                        '<option value = "1"> Finalizar </option>'+
                        '<option value = "2"> Anular </option>'+
                    '</select>';
    
    $.each(result,function(index, r) {
        config.visualizar.valor = '0';
        config.visualizar.checked = '';
        config.visualizar.funcion = 'ActivarCheck';
        config.visualizar.color = 'danger';

        config.modificar.valor = '0';
        config.modificar.checked = '';
        config.modificar.funcion = 'ActivarCheck';
        config.modificar.color = 'danger';

        config.disabled = 'disabled';

        if( $.trim(r.id) != '' && r.estado == 1 ){
            config.visualizar.valor = '1';
            config.visualizar.checked = 'checked';
            config.visualizar.funcion = 'InactivarCheck';
            config.visualizar.color = 'success';
            if( r.modificar == 1 ){
                config.modificar.checked = 'checked';
                config.modificar.funcion = 'InactivarCheck';
                config.modificar.color = 'success';
            }
            config.modificar.valor = r.modificar;
            config.disabled = '';
        }

        if(  r.ruta_flujo_detalle_id != ruta_flujo_detalle_id ){

            if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
                $("#add_campo4 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
                let sp = subproceso.split("^^");
                for (let i = 0; i < sp.length; i++) {
                    const spd = sp[i].split('^');
                    
                    clase = 'class="'+cab[2].color+' cursorZoom N'+spd[1]+'-'+norden.split(".").join("_")+' N'+nordensub_aux+' sub"';
                    style_aux = style.replace("#", (2+ 2*norden.split(".").length));
                    html="<tr data-id='ID-"+ spd[1] + "-" + norden.split(".").join("_") + "' "+clase+" ondblclick='ListarSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
                    html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
                    html+=    "<td>&nbsp;</td>";
                    html+="</tr>";
                    $("#add_campo4 tr.N"+nordensub_aux+":last").after(html);
                }
            }

            ruta_flujo_detalle_id =  r.ruta_flujo_detalle_id;
            norden = nordensub+'.'+('00'+r.norden).slice(-2);
            id = ruta_flujo_id_sub + "-" + norden.split(".").join("_");
            clase = 'class="'+cab[0].color+' N'+nordensub_aux+' sub"';
            child = 1;
            
            style_aux = style.replace("#", (2+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"' "+clase+" data-parent='ID-"+ nordensub_aux +"' data-level='0'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name>"+
                            "<i class=''></i><i class='fa fa-angle-right'></i>"+norden+" | "+r.area+
                      "</td>";
            html+=    "<td>"+select+"</td>";
            html+="</tr>";
            $("#add_campo4 tr.N"+nordensub_aux+":last").after(html);
            $("#add_campo4 tr[data-id='ID-"+ id +"'] select").attr('onChange', 'FinalizarAnularRD(this, '+ ruta_flujo_detalle_id +')').val(r.archivado);

            subproceso = $.trim(r.sub);
        }

        /*
        if( $.trim( r.tipo ) == 0 ){
            
            clase = 'class="'+cab[1].color+' campo'+ id +"-"+ r.ruta_flujo_campo_id +' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (4+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ r.campo +"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="0">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id_sub +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo4 tr.N"+nordensub_aux+":last").after(html);
            parent = id +"-"+ r.ruta_flujo_campo_id;
            child++;
        }
        else if( child == 1 && $.trim( r.tipo ) != '' ){
            $("#add_campo4 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');

            clase = 'class="'+cab[1].color+' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (4+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"S"+ "' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ "Campos Sin Título" +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo4 tr.N"+nordensub_aux+":last").after(html);
            parent = id +"-"+ r.ruta_flujo_campo_id+"S";
            
            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (6+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id_sub +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo4 tr.N"+nordensub_aux+":last").after(html);
            child++;
        }
        else if( $.trim( r.tipo ) != '' ){
            if( !$("#add_campo4 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").hasClass('glyphicon glyphicon-chevron-right') ){
                $("#add_campo4 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
            }

            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (6+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id_sub +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo4 tr.N"+nordensub_aux+":last").after(html);
        }
        */
    });

    if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
        $("#add_campo4 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
        
        let sp = subproceso.split("^^");
        for (let i = 0; i < sp.length; i++) {
            const spd = sp[i].split('^');
            
            clase = 'class="'+cab[2].color+' cursorZoom N'+spd[1]+'-'+norden.split(".").join("_")+' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (2+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ spd[1] + "-" + norden.split(".").join("_") + "' "+clase+" ondblclick='ListarSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";
            $("#add_campo4 tr.N"+nordensub_aux+":last").after(html);
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////

    /*tree-table*/
    $(function () {
        var $table = $('#add_campo4'),
        rows = $table.find("[data-parent^='ID-"+nordensub_aux+"'], [data-id='ID-"+nordensub_aux+"']");

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
}

ListarCamposAreasHTML = (result, ruta_flujo_id) => {
    $("#add_campo3").html('');// inicializando
    //#75DDEC, #FFF3A2, #F58DD7
    var cab = [
        {  'color': 'alert-info' },
        {  'color': 'alert-secondary'},
        {  'color': 'alert-warning' }
    ];
    //TODO: Proceso///////////////////////////////////////////////////////////////////////
    
    var html ='';
    var ruta_flujo_detalle_id = 0; var norden = 0; var id = 0;
    var style = 'style="padding-left: #em;"';
    var tab = 4;
    var child = 0;
    var parent = 0;
    var clase = '';
    var config = {
        visualizar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        modificar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        disabled : 'disabled'
    };

    var subproceso = '';
    
    $.each(result,function(index, r) {
        config.visualizar.valor = '0';
        config.visualizar.checked = '';
        config.visualizar.funcion = 'ActivarCheck';
        config.visualizar.color = 'danger';

        config.modificar.valor = '0';
        config.modificar.checked = '';
        config.modificar.funcion = 'ActivarCheck';
        config.modificar.color = 'danger';

        config.disabled = 'disabled';

        if( $.trim(r.id) != '' && r.estado == 1 ){
            config.visualizar.valor = '1';
            config.visualizar.checked = 'checked';
            config.visualizar.funcion = 'InactivarCheck';
            config.visualizar.color = 'success';
            if( r.modificar == 1 ){
                config.modificar.checked = 'checked';
                config.modificar.funcion = 'InactivarCheck';
                config.modificar.color = 'success';
            }
            config.modificar.valor = r.modificar;
            config.disabled = '';
        }

        if(  r.ruta_flujo_detalle_id != ruta_flujo_detalle_id ){

            if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
                let sp = subproceso.split("^^");
                for (let i = 0; i < sp.length; i++) {
                    const spd = sp[i].split('^');
                    
                    clase = 'class="'+cab[2].color+' cursorZoom N'+ spd[1] + '-' + norden +'"';
                    style_aux = style.replace("#", 2);
                    html="<tr data-id='ID-"+ spd[1] + "-" + norden + "' "+clase+" ondblclick='ListarCamposSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
                    html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
                    html+=    "<td>&nbsp;</td>";
                    html+=    "<td>&nbsp;</td>";
                    html+="</tr>";
                    $("#add_campo3").append(html);
                }
            }

            ruta_flujo_detalle_id =  r.ruta_flujo_detalle_id;
            norden = r.norden;
            id = ruta_flujo_id + "-" + norden;
            clase = 'class="'+cab[0].color+'"';
            child = 1;
            
            html="<tr data-id='ID-"+ id +"' "+clase+">";
            html+=    "<td class='col-md-12' data-column=name>"+
                            "<i class=''></i><i class='fa fa-angle-right'></i>"+norden+" | "+r.area+
                      "</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";
            $("#add_campo3").append(html);

            subproceso = $.trim(r.sub);
        }

        
        if( $.trim( r.tipo ) == 0 ){
            $("#add_campo3 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
            
            clase = 'class="'+cab[1].color+' campo'+ id +"-"+ r.ruta_flujo_campo_id +'"';
            style_aux = style.replace("#", 2);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ r.campo +"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="0">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo3").append(html);
            parent = id +"-"+ r.ruta_flujo_campo_id;
            child++;
        }
        else if( child == 1 && $.trim( r.tipo ) != '' ){
            $("#add_campo3 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');

            clase = 'class="'+cab[1].color+'"';
            style_aux = style.replace("#", 2);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"S"+ "' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ "Campos Sin Título" +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo3").append(html);
            parent = id +"-"+ r.ruta_flujo_campo_id+"S";
            
            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +'"';
            style_aux = style.replace("#", 4);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo3").append(html);
            child++;
        }
        else if( $.trim( r.tipo ) != '' ){
            if( !$("#add_campo3 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").hasClass('glyphicon glyphicon-chevron-right') ){
                $("#add_campo3 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
            }

            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +'"';
            style_aux = style.replace("#", 4);
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo3").append(html);
        }
    });

    if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
        let sp = subproceso.split("^^");
        for (let i = 0; i < sp.length; i++) {
            const spd = sp[i].split('^');
            
            clase = 'class="'+cab[2].color+' cursorZoom N' + spd[1] + '-' + norden +'"';
            style_aux = style.replace("#", 2);
            html="<tr data-id='ID-"+ spd[1] + "-" + norden + "' "+clase+" ondblclick='ListarCamposSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";
            $("#add_campo3").append(html);
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////

    /*tree-table*/
    $(function () {
        var $table = $('#add_campo3'),
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
}

ListarCamposSub = (t, ruta_flujo_id, norden) => {
    $(t).removeAttr('ondblclick');
    var datos = { id: $("#form_campo_asignacion #id").val(), ruta_flujo_id: ruta_flujo_id, norden: norden };
    Pois.ListarCamposAreasSub(datos, ListarCamposSubHTML);
}

ListarCamposSubHTML = (result, nordensub, ruta_flujo_id_sub) => {
    var nordensub_aux = ruta_flujo_id_sub + "-" + nordensub.split(".").join("_");
    $("#add_campo3 [data-parent^='ID-"+nordensub_aux+"']").remove();// inicializando
    //#75DDEC, #FFF3A2, #F58DD7
    var cab = [
        {  'color': 'alert-info' },
        {  'color': 'alert-secondary'},
        {  'color': 'alert-warning' }
    ];
    //TODO: Proceso///////////////////////////////////////////////////////////////////////
    
    var html ='';
    var ruta_flujo_detalle_id = 0; var norden = 0; var id = 0;
    var style = 'style="padding-left: #em;"';
    var tab = 4;
    var child = 0;
    var parent = 0;
    var clase = '';
    var config = {
        visualizar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        modificar : {
            valor : 0,
            checked : '',
            disabled : '',
            funcion : 'ActivarCheck',
            color : 'danger',
        },
        disabled : 'disabled'
    };

    var subproceso = '';
    
    $.each(result,function(index, r) {
        config.visualizar.valor = '0';
        config.visualizar.checked = '';
        config.visualizar.funcion = 'ActivarCheck';
        config.visualizar.color = 'danger';

        config.modificar.valor = '0';
        config.modificar.checked = '';
        config.modificar.funcion = 'ActivarCheck';
        config.modificar.color = 'danger';

        config.disabled = 'disabled';

        if( $.trim(r.id) != '' && r.estado == 1 ){
            config.visualizar.valor = '1';
            config.visualizar.checked = 'checked';
            config.visualizar.funcion = 'InactivarCheck';
            config.visualizar.color = 'success';
            if( r.modificar == 1 ){
                config.modificar.checked = 'checked';
                config.modificar.funcion = 'InactivarCheck';
                config.modificar.color = 'success';
            }
            config.modificar.valor = r.modificar;
            config.disabled = '';
        }

        if(  r.ruta_flujo_detalle_id != ruta_flujo_detalle_id ){

            if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
                let sp = subproceso.split("^^");
                for (let i = 0; i < sp.length; i++) {
                    const spd = sp[i].split('^');
                    
                    clase = 'class="'+cab[2].color+' cursorZoom N'+spd[1]+'-'+norden.split(".").join("_")+' N'+nordensub_aux+' sub"';
                    style_aux = style.replace("#", (2+ 2*norden.split(".").length));
                    html="<tr data-id='ID-"+ spd[1] + "-" + norden.split(".").join("_") + "' "+clase+" ondblclick='ListarCamposSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
                    html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
                    html+=    "<td>&nbsp;</td>";
                    html+=    "<td>&nbsp;</td>";
                    html+="</tr>";
                    $("#add_campo3 tr.N"+nordensub_aux+":last").after(html);
                }
            }

            ruta_flujo_detalle_id =  r.ruta_flujo_detalle_id;
            norden = nordensub+'.'+('00'+r.norden).slice(-2);
            id = ruta_flujo_id_sub + "-" + norden.split(".").join("_");
            clase = 'class="'+cab[0].color+' N'+nordensub_aux+' sub"';
            child = 1;
            
            style_aux = style.replace("#", (2+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"' "+clase+" data-parent='ID-"+ nordensub_aux +"' data-level='0'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name>"+
                            "<i class=''></i><i class='fa fa-angle-right'></i>"+norden+" | "+r.area+
                      "</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";
            $("#add_campo3 tr.N"+nordensub_aux+":last").after(html);

            subproceso = $.trim(r.sub);
        }

        
        if( $.trim( r.tipo ) == 0 ){
            $("#add_campo3 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
            
            clase = 'class="'+cab[1].color+' campo'+ id +"-"+ r.ruta_flujo_campo_id +' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (4+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ r.campo +"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="0">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id_sub +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo3 tr.N"+nordensub_aux+":last").after(html);
            parent = id +"-"+ r.ruta_flujo_campo_id;
            child++;
        }
        else if( child == 1 && $.trim( r.tipo ) != '' ){
            $("#add_campo3 tr[data-id='ID-"+ id +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');

            clase = 'class="'+cab[1].color+' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (4+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"S"+ "' "+clase+"  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+ "Campos Sin Título" +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";

            $("#add_campo3 tr.N"+nordensub_aux+":last").after(html);
            parent = id +"-"+ r.ruta_flujo_campo_id+"S";
            
            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (6+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id_sub +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo3 tr.N"+nordensub_aux+":last").after(html);
            child++;
        }
        else if( $.trim( r.tipo ) != '' ){
            if( !$("#add_campo3 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").hasClass('glyphicon glyphicon-chevron-right') ){
                $("#add_campo3 tr[data-id='ID-"+ parent +"']").find("td:eq(0) i:eq(0)").addClass('glyphicon glyphicon-chevron-right').parent().addClass('cursorZoom');
            }

            clase = 'class="campo'+ id +"-"+ r.ruta_flujo_campo_id +' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (6+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ id +"-"+ r.ruta_flujo_campo_id +"' "+clase+" data-parent='ID-"+ parent +"' data-level='2'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class=''></i><i class='fa fa-angle-double-right'></i>"+r.campo+"</td>";
            html+=    "<td>"+
                            '<input '+ config.disabled +' type="hidden" class="visualizar" name="visualizar[]" value="'+ config.visualizar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="modificar" name="modificar[]" value="'+ config.modificar.valor +'">'+
                            '<input '+ config.disabled +' type="hidden" class="area_id" name="area_id[]" value="'+ r.area_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="norden" name="norden[]" value="'+ norden +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_campo_id" name="ruta_flujo_campo_id[]" value="'+ r.ruta_flujo_campo_id +'">'+
                            '<input '+ config.disabled +' type="hidden" class="ruta_flujo_id" name="ruta_flujo_id[]" value="'+ ruta_flujo_id_sub +'">'+
                            "<label class='lbl_campo_visualizar btn alert-"+ config.visualizar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.visualizar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"visualizar\")' autocomplete='off' "+ config.visualizar.checked +"> &nbsp;&nbsp; "+ "Visualizar" +
                            "</label>"+
                        "</td>";
            html+=      "<td>"+
                            "<label class='lbl_campo_modificar btn alert-"+ config.modificar.color +"'>"+
                                "<input type='checkbox' onChange='"+ config.modificar.funcion +"(this, \""+ id +"-"+ r.ruta_flujo_campo_id +"\", \"modificar\")' autocomplete='off' "+ config.modificar.checked +"> &nbsp;&nbsp; "+ "Modificar" +
                            "</label>"+
                        "</td>";
            html+="</tr>";

            $("#add_campo3 tr.N"+nordensub_aux+":last").after(html);
        }
    });

    if( subproceso != '' ){ //Antes de pasar al siguiente paso, cargamos los sub procesos
        let sp = subproceso.split("^^");
        for (let i = 0; i < sp.length; i++) {
            const spd = sp[i].split('^');
            
            clase = 'class="'+cab[2].color+' cursorZoom N'+spd[1]+'-'+norden.split(".").join("_")+' N'+nordensub_aux+' sub"';
            style_aux = style.replace("#", (2+ 2*norden.split(".").length));
            html="<tr data-id='ID-"+ spd[1] + "-" + norden.split(".").join("_") + "' "+clase+" ondblclick='ListarCamposSub(this, \""+spd[1]+"\",\""+norden+"\");'  data-parent='ID-"+ id +"' data-level='1'>";
            html+=    "<td class='col-md-12' "+ style_aux +" data-column=name><i class='glyphicon glyphicon-chevron-right'></i><i class='fa fa-angle-double-right'></i>"+ spd[0] +"</td>";
            html+=    "<td>&nbsp;</td>";
            html+=    "<td>&nbsp;</td>";
            html+="</tr>";
            $("#add_campo3 tr.N"+nordensub_aux+":last").after(html);
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////

    /*tree-table*/
    $(function () {
        var $table = $('#add_campo3'),
        rows = $table.find("[data-parent^='ID-"+nordensub_aux+"'], [data-id='ID-"+nordensub_aux+"']");

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
}

ListarCamposEventoHTML = (result) => {
    let htmlSelect= '';
    $("#slct_campos_eventos").html('<option value="">.::Seleccione::.</option>');
    var style = 'style="padding-left: #em;"';
    var config = {
        disabled : 'disabled'
    };
    
    $.each(result,function(index, r) {
        style_aux = '';
        if( $.trim( r.tipo ) == 0 ){
            config.disabled = 'disabled';
            r.campo = "* "+r.campo;
        }
        else{
            config.disabled = '';
            r.campo = "&nbsp;&nbsp;&nbsp;&nbsp;"+r.campo;
        }
        htmlSelect = '<option value="'+r.id+'" '+config.disabled+' '+style_aux+'>'+r.campo+'</option>';
        $("#slct_campos_eventos").append(htmlSelect);
    });
    /////////////////////////////////////////////////////////////////////////////////////////////////////
}

AddCampoEvento = () => {
    let html = '';
    let campo = $("#slct_campos_eventos option:selected").text();
    let id_campo = $("#slct_campos_eventos").val();
    let condicion = $("#slct_condicion").html();
    let anidado = $("#slct_anidado").html();
    let disabled = '';
    if( $(".campos_evento").length == 0 ){
        disabled = 'disabled';
    }

    if( $("#campo"+id_campo).length > 0 ){
        msjG.mensaje('warning', "El campo '"+$.trim(campo)+"' ya fue cargado" ,3000);
        return false;
    }
    html =  "<tr class='campos_evento' id='campo"+ id_campo +"'>"+
                "<td><select class='form-control' name='slct_anidado[]' "+ disabled +">"+ anidado +"</select></td>"+
                "<td>"+ campo +"</td>"+
                "<td><select class='form-control' name='slct_condicion[]'>"+ condicion +"</select></td>"+
                "<td><input type='text' class='form-control' name='txt_valor_condicion[]' placerholder='Ingrese el valor de la condición' value=''></td>"+
                "<td><input type='hidden' name='txt_id_campo[]' value='"+ id_campo +"'>"+
                    "<a class='btn btn-danger' onclick='EliminarEvento(this)'><i class='fa fa-remove fa-lg'></i></a>"+
                "</td>"+
            "</tr>";
    $("#add_campo5").append(html);
}

GuardarEvento = () => {
    r = true;
    con = 0;
    $.each(  $("#add_campo5 tr") , ( index, value) => {
        con++;
        if( !$(value).find("select:eq(0)").is(":disabled") && $(value).find("select:eq(0)").val() == ''  && r == true){
            r = false;
            msjG.mensaje('warning', "Seleccione grupo lógico del campo '"+$.trim( $(value).find("td:eq(1)").text() )+"'" ,4000);
        }
        else if( $(value).find("select:eq(1)").val() == ''  && r == true){
            r = false;
            msjG.mensaje('warning', "Seleccione condición del campo '"+$.trim( $(value).find("td:eq(1)").text() )+"'" ,4000);
        }
        else if( $(value).find("td:eq(3) input").val() == ''  && r == true){
            r = false;
            msjG.mensaje('warning', "Ingrese valor de la condición del campo '"+$.trim( $(value).find("td:eq(1)").text() )+"'" ,4000);
        }
    })
    if( $(".txt_url_evento").val() == '' && r == true){
        r = false;
        msjG.mensaje('warning', "Ingrese URL del evento - API",4000);
    }
    
    if( r == true && con > 0 ){
        Pois.GuardarEvento(HTMLGuardarEvento);
    }
    else if( r==true ){
        msjG.mensaje('warning', "Ingrese almenos 1 campo con su condición",4000);
    }
}

HTMLGuardarEvento = (obj) => {
    if(obj.rst==1){
        msjG.mensaje('success',obj.msj,4000);
        $("#add_campo5").html("");
        $(".txt_url_evento").val('');
        $("#slct_campos_eventos").val('');
        Pois.ListarEventos(ListarEventosHTML);
    } else {
        msjG.mensaje('warning',obj.msj,4000);
    }
}

ListarEventosHTML = (result) => {
    let html = '';
    $("#eventos").html(html);
    $.each(  result.data , ( index, value) => {
        html =  "<tr>"+
                    "<td>"+ value.condicion_valida +"</td>"+
                    "<td>"+ value.url_evento +"</td>"+
                    "<td>"+
                        "<a class='btn btn-danger' onclick='EliminarEventoF("+value.id+")'><i class='fa fa-trash fa-lg'></i></a>"+
                    "</td>"+
                "</tr>";
        $("#eventos").append(html);
    });
}

EliminarEvento = (t) => {
    let btn = t.parentNode.parentNode; // Intocable
    let btn2 = t.parentNode.parentNode.parentNode; // Intocable
    $(btn).remove();
    $("#add_campo5 select").removeAttr("disabled");
    $("#add_campo5 tr:eq(0) td:eq(0) select").attr("disabled","disabled").val('');
}

EliminarEventoF = (id) => {
    Pois.EliminarEvento(HTMLGuardarEvento, id);
}

/*
AsigCampo = (result) => {
    areas = $("#slct_areas").val();
    campos = $("#slct_campos").val();

    checked = '';
    funcion = 'ActivarCheck';
    color = 'info';
    modificar = 0;

    if( typeof(result.area_id) != 'undefined' && typeof(result.ruta_flujo_campo_id) != 'undefined' ){
        if( result.modificar == 1 ){
            checked = 'checked';
            funcion = 'InactivarCheck';
            color = 'danger';
            modificar = 1;
        }
        areas = [result.area_id];
        campos = [result.ruta_flujo_campo_id];
    }
    
    for( i = 0; i < areas.length; i++ ){
        for( j = 0; j < campos.length; j++ ){
        id = areas[i]+'0'+campos[j];

            if( $.trim( $(".campo"+ id).html() ) == '' ){
                html = 
                    "<tr class='campo"+id+"'>"+
                        "<td class='text-bold'>"+ $("#slct_areas option[value='"+areas[i]+"']").text() + "</td>"+
                        "<td class='text-bold'>"+ $("#slct_campos option[value='"+campos[j]+"']").text() + "</td>"+
                        "<td>"+
                            "<div class='form-group'>"+
                                "<label class='input-group-addon'>"+
                                    "<label class='lbl_campo btn alert-"+ color +"'>"+
                                        '<input type="hidden" class="modificar" name="modificar[]" value="'+ modificar +'">'+
                                        '<input type="hidden" name="area_id[]" value="'+ areas[i] +'">'+
                                        '<input type="hidden" name="ruta_flujo_campo_id[]" value="'+ campos[j] +'">'+
                                        "<input type='checkbox' onChange='"+ funcion +"(this, "+ id +")' autocomplete='off' "+ checked +"> &nbsp;&nbsp; "+ "Modificar" +
                                    "</label>"+
                                "</label>"+
                                "<label class='btn btn-danger input-group-addon btn-sm' onClick='QuitarCampo("+ id +")'>"+
                                    "<i class='fa fa-trash fa-lg'></i>"+
                                "</label>"+
                            "</div>"+
                        "</td>"+
                    "</tr>";
                $("#add_campo3").append(html);
            }
        }
    }

}
*/

AddCampo = ( id, r ) => {
    campo = $.trim($("#txt_campo").val());
    sub_titulo = $.trim($("#txt_sub_titulo").val());
    tipo_campo = $.trim($("#slct_campo").val());
    $("#txt_campo, #txt_sub_titulo").val('');

    checked = ''; 
    funcion = 'ActivarCheck'; 
    color = 'info';
    html = ''; html2 = '';
    id2 = id;
    obligar = 0;
    col = 6; tipo = 8; capacidad = 20; lista = '';

    if( typeof(id) == 'undefined' || typeof(id) == 'object' ){
        id = $.now();
        id2 = 0;
    }

    if( typeof(r) != 'undefined' ){
        if( r.obligar == 1 ){
            checked = 'checked';
            funcion = 'InactivarCheck';
            color = 'danger';
            obligar = 1;
        }

        if( typeof(r.campo.split("/")[1]) != 'undefined' ){
            campo = r.campo.split("/")[0];
            sub_titulo = r.campo.split("/")[1];
            tipo_campo = 2;
            col = 12;
        }
        else{
            campo = r.campo;
            tipo_campo = 1;
            col = r.col;
            tipo = r.tipo;
            capacidad = r.capacidad;
            lista = r.lista;
        }

    }
 
    if( tipo_campo == 2 ){
        html = '<div class="col-sm-12 campos campo'+ id +'">'+
                    '<br>'+
                    '<input type="hidden" name="campo[]" value="'+ campo +'/'+ sub_titulo +'">'+
                    '<input type="hidden" name="col[]" value="'+ col +'">'+
                    '<input type="hidden" class="campo_id" name="campo_id[]" value="'+ id2 +'">'+
                    '<input type="hidden" name="obligar[]" value="0">'+

                    '<h5 class="text-center"><b>'+ campo +'</b> '+
                        '<small style="color:red"> '+ sub_titulo +'</small>'+
                        '<span class="btn btn-danger" onClick="QuitarCampo('+ id +')"><i class="fa fa-trash"></i></span>'+
                    '</h5>'+
                    '<hr style="border:dotted;">'+
                '</div>';

        html2 = 
            "<tr class='campo"+ id +"'>"+
                "<td colspan='4' class='text-center text-bold lbl_campo alert-warning'>"+ 
                    campo + " / " + sub_titulo +
                    "<input type='hidden' name='tipo[]' value='0'>"+
                    "<input type='hidden' name='capacidad[]' value='20'>"+
                    "<input type='hidden' name='lista[]' value=''>"+
                "</td>"+
            "</tr>";
    }
    else{
        html=
            "<div class='col-sm-6 campos campo"+ id +"'>"+
                "<input class='form-control hidden campo' onBlur='BloquearNombre("+ id +")' name='campo[]' type='text' value='"+ campo +"'>"+
                "<label class='campo_nombre' onClick='CambiarNombre("+ id +")'>"+ campo +":</label>"+
                "<div class='form-group'>"+
                    "<div class='input-group'>"+
                        "<select name='col[]' class='form-control' onChange='CambiaCol(this.value,"+ id +");'>"+
                            "<option value=2> 2 </option>"+
                            "<option value=3> 3 </option>"+
                            "<option value=4> 4 </option>"+
                            "<option value=5> 5 </option>"+
                            "<option value=6 selected> 6 </option>"+
                            "<option value=7> 7 </option>"+
                            "<option value=8> 8 </option>"+
                            "<option value=9> 9 </option>"+
                            "<option value=10> 10 </option>"+
                            "<option value=12> 12 </option>"+
                        "</select>"+
                        "<label class='input-group-addon'>"+
                            "<label class='lbl_campo btn alert-"+ color +"'>"+
                                "<input type='hidden' class='campo_id' name='campo_id[]' value="+ id2 +">"+
                                '<input type="hidden" class="txt_obligar" name="obligar[]" value="'+ obligar +'">'+
                                "<input type='checkbox' onChange='"+ funcion +"(this, "+ id +")' autocomplete='off' "+ checked +"> &nbsp;&nbsp; "+ "Obligatorio" +
                            "</label>"+
                        "</label>"+

                        "<label class='btn btn-danger input-group-addon btn-sm' onClick='QuitarCampo("+ id +")'>"+
                            "<i class='fa fa-trash fa-lg'></i>"+
                        "</label>"+
                    "</div>"
                "</div>"+
            "</div>";

        html2 = 
            "<tr class='campo"+ id +"'>"+
                "<td class='lbl_campo alert-"+ color +"'>"+ 
                    campo + 
                "</td>"+
                "<td>"+ 
                    "<select name='tipo[]' class='form-control' onChange='CambiaCampo(this.value,"+ id +");'>"+
                        "<option value=1> Email </option>"+
                        "<option value=2> Decimal 0.00 </option>"+
                        "<option value=3> Fecha (YYYY-MM-DD)</option>"+
                        "<option value=4> Fecha (YYYY-MM)</option>"+
                        "<option value=5> Fecha (YYYY)</option>"+
                        "<option value=6> Lista </option>"+
                        "<option value=7> Número </option>"+
                        "<option value=8 selected> Texto </option>"+
                    "</select>"+
                "</td>"+
                "<td><input class='form-control capacidad' name='capacidad[]' type='number' onKeyPress='return masterG.validaNumeros(event);' placeholder='Capacidad #' value="+ capacidad +"></td>"+
                "<td><textarea class='form-control campo_lista hidden' name='lista[]' onKeyPress='return masterG.NoEnter(event);' placeholder='Ejemplo: A*B*C'>"+lista+"</textarea></td>"+
            "</tr>";
    }


    $("#add_campo").append(html);
    $("#add_campo2").append(html2);
    if( tipo_campo != 2 ){
        if( col != 6 ){
            CambiaCol(col, id);
            $("#add_campo .campo"+id+"  select").val(col);
        }
        
        if( tipo != 8 ){
            CambiaCampo(tipo, id);
            $("#add_campo2 .campo"+id+"  select").val(tipo);
            $("#add_campo2 .campo"+id+"  .capacidad").val(capacidad);
            $("#add_campo2 .campo"+id+"  .campo_lista").val(lista);
        }
    }
    $("#txt_campo").focus()
}

CambiarNombre = (id) => {
    $("#add_campo .campo"+id +" .campo").removeClass('hidden');
    $("#add_campo .campo"+id +" .campo_nombre").hide();
    $("#add_campo .campo"+id +" .campo").focus();
}

BloquearNombre = (id) => {
    $("#add_campo .campo"+id +" .campo").addClass('hidden');
    $("#add_campo .campo"+id +" .campo_nombre").show().text( $("#add_campo .campo"+id +" .campo").val()+":" );
}

QuitarCampo = (id) => {
    $("#add_campo .campo"+id +", #add_campo2 .campo"+id+", #add_campo3 .campo"+id).remove();
}

CambiaCol = (val,id) => {
    $("#add_campo .campo"+id).not(".disabled").removeClass().addClass("campos campo"+id+" col-sm-"+val);
}

CambiaCampo = (val, id) => {
    $(".campo"+ id +" .capacidad").val('20');
    $(".campo"+ id +" .capacidad").removeClass('hidden');
    $(".campo"+ id +" .campo_lista").removeClass('hidden');
    
    if( val == 1 ){
        $(".campo"+ id +" .campo_lista").addClass('hidden');
        $(".campo"+ id +" .capacidad").val('80');
        $(".campo"+ id +" .capacidad").focus();
    }
    else if( val >= 3 && val <=5 ){
        $(".campo"+ id +" .capacidad").addClass('hidden');
        $(".campo"+ id +" .campo_lista").addClass('hidden');
    }
    else if( val == 6 ){
        $(".campo"+ id +" .capacidad").addClass('hidden');
        $(".campo"+ id +" .campo_lista").val('');
        $(".campo"+ id +" .campo_lista").focus();
    }
    else{
        $(".campo"+ id +" .campo_lista").addClass('hidden');
        $(".campo"+ id +" .campo_lista").val('');
        $(".campo"+ id +" .capacidad").focus();
    }
}

ActivarCheck = (t, id, tipo) => {
    if( typeof(tipo) == 'undefined' ){ 
        tipo = 'x'; 
        $(t).attr("onChange", "InactivarCheck(this, '"+ id +"')");
    }
    else{
        $(t).attr("onChange", "InactivarCheck(this, '"+ id +"', '"+ tipo +"')");
        if( tipo == 'visualizar' ){
            $(".campo"+ id +" .visualizar, .campo"+ id +" .modificar, .campo"+ id +" .area_id, .campo"+ id +" .norden, .campo"+ id +" .ruta_flujo_campo_id, .campo"+ id +" .ruta_flujo_id").removeAttr('disabled');
        }
        else{
            if( $(".campo"+ id +" .visualizar").val() == 0 ){
                //$(".campo"+ id +" .lbl_campo_visualizar input[type=checkbox]").click();
                $(".campo"+ id +" .lbl_campo_visualizar input[type=checkbox]").attr('onChange',"InactivarCheck(this, '"+ id +"', 'visualizar')");
                $(".campo"+ id +" .lbl_campo_visualizar input[type=checkbox]").prop('checked',true);
                $(".campo" + id+" .lbl_campo_visualizar").removeClass("alert-danger").addClass("alert-success");
                $(".campo"+ id +" .visualizar").val(1);
            }
        }
    }
    //var label = $(t).parent('label');
    $(".campo" + id+" .lbl_campo, .campo" + id+" .lbl_campo_"+tipo).removeClass("alert-danger").addClass("alert-success");
    $(".campo" + id+" .txt_obligar, .campo"+ id +" ."+tipo).val(1);

    if( tipo != 'x' ){ //Solo si existe tipo
        var datos = {
            clasificador_tramite_id: $("#form_campo_asignacion #id").val(),
            visualizar: $(".campo"+ id +" .visualizar").val(),
            modificar: $(".campo"+ id +" .modificar").val(),
            area_id: $(".campo"+ id +" .area_id").val(),
            norden: $(".campo"+ id +" .norden").val(),
            ruta_flujo_campo_id: $(".campo"+ id +" .ruta_flujo_campo_id").val(),
            ruta_flujo_id: $(".campo"+ id +" .ruta_flujo_id").val()
        };
        Pois.AsignarCampo(datos);
    }
}

InactivarCheck = (t, id, tipo) => {
    if( typeof(tipo) == 'undefined' ){ 
        tipo = 'x'; 
        $(t).attr("onChange", "ActivarCheck(this, '"+ id +"')");
    }
    else{
        $(t).attr("onChange", "ActivarCheck(this, '"+ id +"', '"+ tipo +"')");
        if( tipo == 'visualizar' ){
            $(".campo"+ id +" .visualizar, .campo"+ id +" .modificar, .campo"+ id +" .area_id, .campo"+ id +" .norden, .campo"+ id +" .ruta_flujo_campo_id, .campo"+ id +" .ruta_flujo_id").attr('disabled','disabled');
            if( $(".campo"+ id +" .modificar").val() == 1 ){
                //$(".campo"+ id +" .lbl_campo_modificar input[type=checkbox]").click();
                $(".campo"+ id +" .lbl_campo_modificar input[type=checkbox]").attr('onChange',"ActivarCheck(this, '"+ id +"', 'modificar')");
                $(".campo"+ id +" .lbl_campo_modificar input[type=checkbox]").prop('checked',false);
                $(".campo" + id+" .lbl_campo_modificar").removeClass("alert-success").addClass("alert-danger");
                $(".campo"+ id +" .modificar").val(0);
            }
        }
    }
    //var label = $(t).parent('label');
    $(".campo" + id+" .lbl_campo, .campo" + id+" .lbl_campo_"+tipo).removeClass("alert-success").addClass("alert-danger");
    $(".campo" + id+" .txt_obligar, .campo"+ id +" ."+tipo).val(0);

    if( tipo != 'x' ){ //Solo si existe tipo
        var datos = {
            clasificador_tramite_id: $("#form_campo_asignacion #id").val(),
            visualizar: $(".campo"+ id +" .visualizar").val(),
            modificar: $(".campo"+ id +" .modificar").val(),
            area_id: $(".campo"+ id +" .area_id").val(),
            norden: $(".campo"+ id +" .norden").val(),
            ruta_flujo_campo_id: $(".campo"+ id +" .ruta_flujo_campo_id").val(),
            ruta_flujo_id: $(".campo"+ id +" .ruta_flujo_id").val()
        };
        Pois.AsignarCampo(datos);
    }
}

CambioCampo = () => {
    $(".sub_titulo").hide();

    if( $("#slct_campo").val() == 2 ){
        $(".sub_titulo").show();
    }
    $("#txt_campo").focus()
}

ValidarCampos = () => {
    r = true;

    return r;
}

RegistrarCampos = () => {
    if( ValidarCampos() ){
        Pois.RegistrarCampos();
    }
}

AsignarFCampos = () => {
    if( ValidarCampos() ){
        Pois.AsignarCampos();
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////
CargarEstratPei=function(){

    Pois.CargarEstratPei();
};


costopersonalHTML=function(datos){
  var html="";
    var alerta_tipo= '';
    $('#t_costo_personal').dataTable().fnDestroy();
    pos=0;
    $.each(datos,function(index,data){
        pos++;
        html+="<tr>"+
             "<td>"+pos+"</td>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+data.cantidad+"</td>";
        if( $.trim(data.ruta_archivo)!='' ){
            html+="<td data-url='"+data.ruta_archivo+"'><a class='btn btn-info btn-lg' href='"+data.ruta_archivo+"' target='_blank'><i class='fa fa-file fa-lg'></i></td>";
        }
        else{
            html+='<td data-url="'+data.ruta_archivo+'"> - </td>';
        }
        html+="<td align='center'><a class='form-control btn btn-primary' data-toggle='modal' data-target='#requisitoModal' data-titulo='Editar' onclick='BtnEditarCostoPersonal(this,"+data.id+")'><i class='fa fa-lg fa-edit'></i></a></td>";
        if(data.estado==1){
            html+='<td align="center"><span id="'+data.id+'" onClick="desactivarCostoPersonal('+data.id+')" data-estado="'+data.estado+'" class="btn btn-success">Activo</span></td>';
        }
        else {
           html+='<td align="center"><span id="'+data.id+'" onClick="activarCostoPersonal('+data.id+')" data-estado="'+data.estado+'" class="btn btn-danger">Inactivo</span></td>';

        }

        html+="</tr>";
    });
    $("#tb_costo_personal").html(html);
    $("#t_costo_personal").dataTable(
    ); 


};

estratpeiHTML=function(datos){
  var html="";
    var alerta_tipo= '';
    $('#t_estrat_pei').dataTable().fnDestroy();
    pos=0;
    $.each(datos,function(index,data){
        pos++;
        html+="<tr>"+
             "<td>"+pos+"</td>"+
            "<td>"+data.nombre+"</td>";
        html+="<td align='center'><a class='form-control btn btn-primary' data-toggle='modal' data-target='#tipotramiteModal' data-titulo='Editar' onclick='BtnEditarEstratPei(this,"+data.id+")'><i class='fa fa-lg fa-edit'></i></a></td>";
        if(data.estado==1){
            html+='<td align="center"><span id="'+data.id+'" onClick="desactivarEstratPei('+data.id+')" data-estado="'+data.estado+'" class="btn btn-success">Activo</span></td>';
        }
        else {
           html+='<td align="center"><span id="'+data.id+'" onClick="activarEstratPei('+data.id+')" data-estado="'+data.estado+'" class="btn btn-danger">Inactivo</span></td>';

        }

        html+="</tr>";
    });
    $("#tb_estrat_pei").html(html);
    $("#t_estrat_pei").dataTable(
    ); 


};


eventoSlctGlobalSimple=function(){
};

BtnEditarCostoPersonal=function(btn,id){
    var tr = btn.parentNode.parentNode; // Intocable
    CostoPersonalG.id=id;
    CostoPersonalG.nombre=$(tr).find("td:eq(1)").text();
    CostoPersonalG.cantidad=$(tr).find("td:eq(2)").text();
    CostoPersonalG.ruta_archivo=$(tr).find("td:eq(3)").attr('data-url');
    CostoPersonalG.estado=$(tr).find("td:eq(5)>span").attr("data-estado");

};


BtnEditarEstratPei=function(btn,id){
    var tr = btn.parentNode.parentNode; // Intocable
    EstratPeiG.id=id;
    EstratPeiG.descripcion=$(tr).find("td:eq(1)").text();
    EstratPeiG.estado=$(tr).find("td:eq(3)>span").attr("data-estado");

};

validaCostoPersonal = function(){
    var r=true;
    if( $("#form_requisitos_modal #txt_modalidad").val()=='' ){
        alert("Ingrese Modalidad");
        r=false;
    }
    return r;
};
EditarCostoPersonal = function(){
    if(validaCostoPersonal()){
        Pois.AgregarEditarCostoPersonal(1);
    }
};
AgregarCostoPersonal = function(){
    if(validaCostoPersonal()){
        Pois.AgregarEditarCostoPersonal(0);
    }
};


EditarEstratPei = function(){
    if(validaEstratPei()){
        Pois.AgregarEditarEstratPei(1);
    }
};
AgregarEstratPei = function(){
    if(validaEstratPei()){
        Pois.AgregarEditarEstratPei(0);
    }
};

validaEstratPei = function(){
    var r=true;
    if( $("#form_tipotramites_modal #txt_nombre").val()=='' ){
        alert("Ingrese Nombre");
        r=false;
    }
    return r;
};


Close=function(){
    //$("#form_costo_personal .form-group, #form_campo .form-group, #form_campo_asignacion .form-group").css("display","none");
    $(".FormG").hide();
}


guardarasignacion=function(){
    if( $("#txt_flujo2_id").val()=='' ){
        alert("Seleccione un Tipo Flujo");
    }

    else{
        Pois.agregarProceso();
    }
}

cargarRutaId=function(ruta_flujo_id,permiso,ruta_id){
    $("#rutaflujoModal #txt_ruta_flujo_id_modal").remove();
    $("#rutaflujoModal #form_ruta_flujo").append('<input type="hidden" id="txt_ruta_flujo_id_modal" value="'+ruta_flujo_id+'">');
    $("#rutaflujoModal #txt_titulo").text("Vista");
    $("#rutaflujoModal #texto_fecha_creacion").text("Fecha Vista:");
    $("#rutaflujoModal #fecha_creacion").html('<?php echo date("Y-m-d"); ?>');
    $("#rutaflujoModal #form_ruta_flujo .form-group").css("display","");
    Ruta.CargarDetalleRuta(ruta_flujo_id,permiso,CargarDetalleRutaHTML,ruta_id);
    $("#rutaflujoModal").modal('show');
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
            $("#txt_persona_1").val(data.persona);
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
</script>