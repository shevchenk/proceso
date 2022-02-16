<script type="text/javascript">
var cabeceraG=[]; // Cabecera del Datatable
var columnDefsG=[]; // Columnas de la BD del datatable
var targetsG=-1; // Posiciones de las columnas del datatable
var PersonasG={
        id:0,
        paterno:"",
        materno:"",
        nombre:"",
        dni:"",
        sexo:"",
        email:"",
        email_mdi:"",
        fecha_nacimiento:"",
        password:"",
        area:"",     
        rol:"",
        modalidad:"",
        vista_doc:"",
        doc_privados:"",
        estado:1
        };

$(document).ready(function() {  
    
      
        var idG={   paterno       :'onBlur|Apellido Paterno|#DCE6F1', //#DCE6F1
                materno       :'onBlur|Apellido Materno|#DCE6F1', //#DCE6F1
                nombre        :'onBlur|Nombre|#DCE6F1', //#DCE6F1
                dni           :'onBlur|DNI|#DCE6F1', //#DCE6F1
                sexo          :'3|Género|#DCE6F1', //#DCE6F1
                email         :'onBlur|Email|#DCE6F1', //#DCE6F1
                email_mdi         :'onBlur|Email corporativo|#DCE6F1|||1', //#DCE6F1
              //  password      :'onBlur|Password|#DCE6F1', //#DCE6F1
                area          :'3|Área de la Persona|#DCE6F1|||1', 
                rol           :'3|Rol de la Persona|#DCE6F1|||1', //#DCE6F1
                modalidad     :'3|Modalidad|#DCE6F1|||1', //#DCE6F1
                vista_doc     :'3|Vista Documento|#DCE6F1|||1', //#DCE6F1
                estado        :'2|Estado|#DCE6F1|||1', //#DCE6F1
             };

    var resG=dataTableG.CargarCab(idG);
    cabeceraG=resG; // registra la cabecera
    var resG=dataTableG.CargarCol(cabeceraG,columnDefsG,targetsG,1,'persona','t_persona');
    columnDefsG=resG[0]; // registra las columnas del datatable
    targetsG=resG[1]; // registra los contadores
    var resG=dataTableG.CargarBtn(columnDefsG,targetsG,1,'BtnEditar','t_persona','fa-edit');
    columnDefsG=resG[0]; // registra la colunmna adiciona con boton
    targetsG=resG[1]; // registra el contador actualizado
    MostrarAjax('persona');

    var datos={estado:1};
    slctGlobal.listarSlct2('area','slct_area_aux',datos);    

    $('#personaModal').on('show.bs.modal', function (event) {
        
        $('#txt_fecha_nacimiento').daterangepicker({
            format: 'YYYY-MM-DD',
            singleDatePicker: true,
            showDropdowns: true
        });

        var button = $(event.relatedTarget); // captura al boton
        var titulo = button.data('titulo'); // extrae del atributo data-
        //var persona_id = button.data('id'); //extrae el id del atributo data

        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' Alumno');
        $('#form_personas_modal [data-toggle="tooltip"]').css("display","none");
        $("#form_personas_modal input[type='hidden']").remove();
        
        var datos={estado:1};
        slctGlobal.listarSlct('cargo','slct_cargos','simple');
        $("#slct_area_aux option[value='']").remove(); //aqui para limpiar el dato vacio
        $("#t_cargoPersona").html('');
        
        $("#personaModal #txt_paterno, #personaModal #txt_materno, #personaModal #txt_nombre, #personaModal #txt_dni").attr("disabled","true");
        
        if(titulo=='Nuevo'){
            $("#personaModal #txt_paterno, #personaModal #txt_materno, #personaModal #txt_nombre, #personaModal #txt_dni").removeAttr("disabled");
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_personas_modal #txt_nombre').focus();
            slctGlobal.listarSlct('area','slct_area','simple',null,datos);
            slctGlobal.listarSlct('rol','slct_rol','simple',null,datos);
            slctGlobal.listarSlctFuncion('local','listarlocales','slct_local','simple',null,datos);
        }
        else{
            Persona.CargarAreas(PersonasG.id); //no es multiselect
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            //PersonasG
            $('#form_personas_modal #txt_paterno').val( PersonasG.paterno );
            $('#form_personas_modal #txt_materno').val( PersonasG.materno );
            $('#form_personas_modal #txt_nombre').val( PersonasG.nombre );
            $('#form_personas_modal #txt_dni').val( PersonasG.dni );
            $('#form_personas_modal #slct_sexo').val( PersonasG.sexo );
            $('#form_personas_modal #txt_email').val( PersonasG.email );
            $('#form_personas_modal #txt_email_mdi').val( PersonasG.email_mdi );
            $('#form_personas_modal #txt_fecha_nacimiento').val( PersonasG.fecha_nacimiento );
            $('#form_personas_modal #txt_password').val( '' );
            $('#form_personas_modal #slct_modalidad').val( PersonasG.modalidad );
            $('#form_personas_modal #slct_vista_doc').val( PersonasG.vista_doc );
            $('#form_personas_modal #slct_doc_privados').val( PersonasG.doc_privados );
            $('#form_personas_modal #slct_responsable_area').val( PersonasG.responsable_area );
            $('#form_personas_modal #txt_telefono').val( PersonasG.telefono );
            $('#form_personas_modal #txt_celular').val( PersonasG.celular );
            $('#form_personas_modal #txt_direccion').val( PersonasG.direccion );
            

            $('#form_personas_modal #slct_estado').val( PersonasG.estado );
            $("#form_personas_modal").append("<input type='hidden' value='"+PersonasG.id+"' name='id'>");

            var datos={estado:1};
            //var idsarea=[]; idsarea.push(PersonasG.area_id);
            //var idsrol=[]; idsrol.push(PersonasG.rol_id);
            //slctGlobal.listarSlct('area','slct_area','simple',idsarea,datos);
            
            slctGlobal.listarSlct('area','slct_area','simple',PersonasG.area,datos);
         //   alert(PersonasG.fecha_nacimiento_id);
            slctGlobal.listarSlct('rol','slct_rol','simple',PersonasG.rol,datos);
            slctGlobal.listarSlctFuncion('local','listarlocales','slct_local','simple',PersonasG.local_id,datos);
            //slctGlobal.listarSlctFijo('rol','slct_rol',PersonasG.rol);
        }

        $("#cargo_2").parent().parent().remove();
        $("#cargo_2").parent().parent().remove();
        
        $("#slct_alumno").val(1);
        ActivarAlumno(1);
        
        $( "#form_personas_modal #slct_estado" ).trigger('change');
        $( "#form_personas_modal #slct_estado" ).change(function() {
            if ($( "#form_personas_modal #slct_estado" ).val()==1) {
                $('#f_areas_cargo').removeAttr('disabled');
            }
            else {
                $('#f_areas_cargo').attr('disabled', 'disabled');
            }
        });
    });

    $('#personaModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        $('#slct_cargos,#slct_rol,#slct_area,#slct_local').multiselect('destroy');
        $("#t_cargoPersona").html('');
    });
});
BtnEditar=function(btn,id){
    var tr = btn.parentNode.parentNode; // Intocable
    PersonasG.id=id;
    PersonasG.paterno=$(tr).find("td:eq(0)").text();
    PersonasG.materno=$(tr).find("td:eq(1)").text();
    PersonasG.nombre=$(tr).find("td:eq(2)").text();
    PersonasG.dni=$(tr).find("td:eq(3)").text();
    PersonasG.sexo=$(tr).find("td:eq(4) input[name='txt_sexo']").val();
    PersonasG.email=$(tr).find("td:eq(5)").text();
    PersonasG.email_mdi=$(tr).find("td:eq(6)").text();
    // se detecta el atributo que se esta enviando atravez del hiden del txt_sexo
    PersonasG.fecha_nacimiento=$.trim($(tr).find("td:eq(4) input[name='txt_sexo']").attr('fecha_nacimiento')); 
    PersonasG.responsable_area=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('responsable_area'); 
      //PersonasG.password=$(tr).find("td:eq(6) input[name='txt_password']").val();
    PersonasG.telefono=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('telefono');
    PersonasG.celular=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('celular');
    PersonasG.direccion=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('direccion');
    PersonasG.local_id=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('local_id');
    PersonasG.area=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('area');
    PersonasG.rol=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('rol');
    PersonasG.modalidad=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('modalidad'); 
    PersonasG.vista_doc=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('vista_doc');
    PersonasG.doc_privados=$(tr).find("td:eq(4) input[name='txt_sexo']").attr('doc_privados');

    console.log(PersonasG);

    
    PersonasG.estado=1;
    $("#BtnEditar").click();
};

MostrarAjax=function(t){
    if( t=="persona" ){
        if( columnDefsG.length>0 ){
            dataTableG.CargarDatos(t,'persona','cargar',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    }
}
GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
    
    if(typeof(fn)!='undefined' && fn.col==4){
        //se envia de manera ocultada la fecha de nacimiento en el txt_sexo
        return row.sexo+"<input type='hidden'name='txt_sexo' fecha_nacimiento='"+row.fecha_nacimiento+"' responsable_area='"+row.responsable_area+"' "+
                "telefono='"+row.telefono+"' celular='"+row.celular+"' direccion='"+row.direccion+"' doc_privados='"+row.doc_privados+"' "+
                "local_id='"+$.trim(row.local_id)+"' "+
                "area='"+row.area_id+"' rol='"+row.rol_id+"' modalidad='"+row.modalidad_id+"' vista_doc='"+row.vista_doc_id+"' "+
                "value='"+row.sexo_id+"'>";
    }

    /*
    else if(typeof(fn)!='undefined' && fn.col==7){
        return row.area+"<input type='hidden'name='txt_area' telefono='"+row.telefono+"' celular='"+row.celular+"' direccion='"+row.direccion+"' value='"+row.area_id+"'>";
    }

    else if(typeof(fn)!='undefined' && fn.col==8){
        return row.rol+"<input type='hidden'name='txt_rol' value='"+row.rol_id+"'>";
    }

    if(typeof(fn)!='undefined' && fn.col==9){
        return row.modalidad+"<input type='hidden'name='txt_modalidad' value='"+row.modalidad_id+"'>";
    }

    if(typeof(fn)!='undefined' && fn.col==10){
        return row.vista_doc+"<input type='hidden' name='txt_vista_doc' value='"+row.vista_doc_id+"'><input type='hidden' name='txt_doc_privados' id='ID_txt_doc_privados' value='"+row.doc_privados+"'>";
    }

    else if(typeof(fn)!='undefined' && fn.col==11){
        var estadohtml='';
        estadohtml='<span id="'+row.id+'" data-estado="'+row.estado+'" class="btn btn-danger">Inactivo</span>';
        if(row.estado==1){
            estadohtml='<span id="'+row.id+'" data-estado="'+row.estado+'" class="btn btn-success">Activo</span>';
        }
        return estadohtml;
    }*/
}

eventoSlctGlobalSimple=function(){
}

activarTabla=function(){
    $("#t_personas").dataTable(); // inicializo el datatable    
};

Editar=function(){
    if(validaPersonas()){
        Persona.AgregarEditarPersona(1);
    }
};

activar=function(id){
    Persona.CambiarEstadoPersonas(id,1);
};
desactivar=function(id){
    Persona.CambiarEstadoPersonas(id,0);
};

Agregar=function(){
    if(validaPersonas()){
        Persona.AgregarEditarPersona(0);
    }
};
AgregarArea=function(){
    //añadir registro "opcion" por usuario
    var cargo_id=$('#slct_cargos option:selected').val();
    var cargo=$('#slct_cargos option:selected').text();
    var buscar_cargo = $('#cargo_'+cargo_id).text();
    if (cargo_id!=='') {
        if (buscar_cargo==="") {

            var html='';
            html+="<li class='list-group-item'><div class='row'>";
            html+="<div class='col-sm-4' id='cargo_"+cargo_id+"'><input type='hidden' value='"+cargo_id+"' name='cargo_id[]'><h5>"+cargo+"</h5></div>";

            html+="<div class='col-sm-6'>";
            html+="<select class='form-control' multiple='multiple' name='slct_areas_"+cargo_id+"[]' id='slct_areas_"+cargo_id+"'></select></div>";
            //var envio = {cargo_id: cargo_id,estado:1};

            html+="</div></li>";

            $("#t_cargoPersona").append(html);
            $("#slct_areas_"+cargo_id).html( $("#slct_area_aux").html() );
            slctGlobalHtml('slct_areas_'+cargo_id, 'multiple');
            //slctGlobal.listarSlct('area','slct_areas'+cargo_id,'multiple',null,envio);
            //cargos_selec.push(cargo_id);
        } else 
            alert("Ya se agrego este Rol de sistema");
    } else 
        alert("Seleccione Rol de sistema");

};
EliminarArea=function(obj){
    //console.log(obj);
    var valor= obj.id;
    obj.parentNode.parentNode.parentNode.remove();
    //var index = cargos_selec.indexOf(valor);
    //cargos_selec.splice( index, 1 );
};
validaPersonas=function(){
 /*   $('#form_personas_modal [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=valida("txt","nombre","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;*/
    var r=true;
    if( $("#form_personas_modal #txt_nombre").val()=='' ){
        alert("Ingrese Nombre");
        r=false;
    }
    else if( $("#form_personas_modal #txt_paterno").val()==''){
        alert("Ingrese Apellido Paterno");
        r=false;
    }
    else if( $("#form_personas_modal #txt_materno").val()=='' ){
        alert("Ingrese Apellido Materno");
        r=false;
    }
    
    else if( $("#form_personas_modal #txt_dni").val()=='' ){
        alert("Ingrese Numero DNI");
        r=false;
    }
    else if( $("#form_personas_modal #slct_local").val()=='' ){
        alert("Seleccione Local");
        r=false;
    }

    $("#slct_estado").val(1);
    //else if( $("#form_personas_modal #txt_password").val()=='' ){
    //    alert("Ingrese Password");
    //    r=false;
    //}
    return r;
};

ActivarAlumno = (v)=>{
    if( v==1 ){
        var html='';
            html+="<li class='list-group-item'><div class='row'>";
            html+="<div class='col-sm-4' id='cargo_2'><input type='hidden' value='2' name='cargo_id[]'><h5>Alumno</h5></div>";

            html+="<div class='col-sm-6'>";
            html+="<input type='text' class='form-control' name='slct_areas_2[]' value='10'></div>";

            html+="</div></li>";

            $("#t_cargoPersona").append(html);
    }
    else{
        $("#cargo_2").parent().parent().remove();
    }
}
</script>
