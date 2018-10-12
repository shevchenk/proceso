<script type="text/javascript">
$(document).ready(function() {
    Data.CargarDatos(activarTabla);

    $(".fechas").datetimepicker({
        format: "yyyy-mm-dd",
        language: 'es',
        showMeridian: false,
        time: false,
        minView: 3,
        startView: 2,
        autoclose: true,
        todayBtn: false
    });

    $('#btnempty').click(function() {
        $('input[type="text"]').not('.data_fija').val('');
    });
    

    // -- 
    $('#cbo_tipobus').change(function() {
        if($(this).val() == 2) {
            $('#txt_nombres').val('').show();
            $('#txt_dni').hide();
        } else {
            $('#txt_dni').val('').show();
            $('#txt_nombres').hide();
        }
        $("#tb_busqueda").html('');
    });

    $('#btnbuscar_user').click(function() {
        $("#div-insert-bus").hide();
        if($('#cbo_tipobus').val() == 1 && $('#txt_dni').val() == '') {
            $('#txt_dni').focus();
            swal("Mensaje", "Ingrese DNI para su busqueda!");
        } else if($('#cbo_tipobus').val() == 2 && $('#txt_nombres').val() == '') {
            $('#txt_nombres').focus();
            swal("Mensaje", "Ingrese Nombres y/o Apellidos para su busqueda!");
        } else {
            $('#btnbuscar_user').attr('disabled', true).text('Carg...');
            data = {tipobus: $('#cbo_tipobus').val(), dni:$('#txt_dni').val(), nombres:$('#txt_nombres').val()}
            Data.buscarPersona(data);            
        }
    });

    $('#btnnuevo_user').click(function() {
        $("#div-result-bus").hide();
        $("#tb_busqueda").html('');
        $("#div-insert-bus").show();
    });

    $('#btnsave_data').click(function() {
        if($('#form_data #txt_dni').val() == '') {
            $('#form_data #txt_dni').focus();
            swal("Mensaje", "Ingrese número DNI!");
        } else if($('#form_data #txt_nombre').val() == '' && $('#form_data #txt_paterno').val() == '' && $('#form_data #txt_materno').val() == '') {
            $('#form_data #txt_nombre').focus();
            swal("Mensaje", "Ingrese Nombres y Apellidos!");
        } else {
            $('#form_data #btnsave_data').attr('disabled', true).text('...');            
            data = {dni:$('#form_data #txt_dni').val(), 
                    nombre:$('#form_data #txt_nombres').val(),
                    paterno:$('#form_data #txt_paterno').val(),
                    materno:$('#form_data #txt_materno').val()}
            Data.insertarPersona(data);            
        }
    });

    $('#modal-user').on('click', function(){
        var modal = $('#myModalUser'); //captura el modal
        modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        $("#div-result-bus").hide();
        $("#div-insert-bus").hide();
        $("#tb_busqueda").html('');

        $('#cbo_tipobus').val(1).change();
    });
    // --

});


Agregar = function(){
    if(validaCampos()){
        sweetalertG.confirm("Confirmación!", "Desea guardar la resolución?", function(){
            Data.AgregarEditarRol();
        });
    }
};
validaCampos = function(){
    var r=true;

    if( $("#txt_expediente").val()=='' ){
        $("#txt_expediente").focus();
        swal("Mensaje", "Ingrese Expediente!");
        r=false;
    }    
    else if( $("#fecha_vence").val()=='' ){
        $("#fecha_vence").focus();
        swal("Mensaje", "Ingrese la Fecha Vencimiento!");
        r=false;
    }
    else if( $("#txt_licencia_edifica").val()=='' ){
        $("#txt_licencia_edifica").focus();
        swal("Mensaje", "Ingrese Licencia de Edificación!");
        r=false;
    }
    else if( $("#txt_modalidad").val()=='' ){
        $("#txt_modalidad").focus();
        swal("Mensaje", "Ingrese Modalidad!");
        r=false;
    }
    else if( $("#txt_uso").val()=='' ){
        $("#txt_uso").focus();
        swal("Mensaje", "Ingrese Uso!");
        r=false;
    }
    else if( $("#txt_zonifica").val()=='' ){
        $("#txt_zonifica").focus();
        swal("Mensaje", "Ingrese Zonificación!");
        r=false;
    }
    else if( $("#txt_altura").val()=='' ){
        $("#txt_altura").focus();
        swal("Mensaje", "Ingrese Altura!");
        r=false;
    }
    else if( $("#txt_propietario").val()=='' ){
        $("#txt_propietario").focus();
        swal("Mensaje", "Ingrese Propietario!");
        r=false;
    }
    else if( $("#txt_dir_urbaniza").val()=='' ){
        $("#txt_altura").focus();
        swal("Mensaje", "Ingrese Dirección!");
        r=false;
    }
    else if( $("#txt_area_terreno").val()=='' ){
        $("#txt_area_terreno").focus();
        swal("Mensaje", "Ingrese Area Terreno!");
        r=false;
    }
    return r;
};


// --
activarTabla=function(){
    $("#t_cargos").dataTable(); // inicializo el datatable    
};
desactivar=function(id){
    sweetalertG.confirm("Confirmación!", "Desea Eliminar el Registro?", function(){
            Data.CambiarEstadoLicenContruc(id,0);
    });
};


HTMLDatos=function(datos){
    var html="";
    $('#t_cargos').dataTable().fnDestroy();

    $.each(datos,function(index,data){
        html+="<tr>"+
            "<td >"+data.expediente+"</td>"+
            "<td >"+data.fecha_emision+"</td>"+
            "<td >"+data.fecha_vence+"</td>"+
            "<td >"+data.licencia_edifica+"</td>"+
            "<td >"+data.modalidad+"</td>"+
            "<td >"+data.distrito+"</td>";
        
        html+='<td>'+
                '<a class="btn btn-danger active btn-xs" href="#" onclick="desactivar('+data.id+')"><i class="glyphicon glyphicon-ban-circle"></i>&nbsp;borrar</a>';
        html+="</td>";

        html+='<td>'+
                '<a class="btn btn-warning active btn-xs" href="#" onclick="openPlantilla('+data.id+',4,0); return false;" data-titulo="Previsualizar"><i class="fa fa-print"></i>&nbsp;Ver</a>';
        html+="</td>";
        

        html+="</tr>";
    });
    $("#tb_cargos").html(html); 
    activarTabla();
};


HTMLDatosBusqueda=function(datos){
    var html="";

    if(datos!='')
    {
        $.each(datos,function(index, data){
            var nombres = data.nombre+' '+data.paterno+' '+data.materno;
            html+="<tr>"+
                "<td >"+data.dni+"</td>"+
                "<td >"+data.nombre+"</td>"+
                "<td >"+data.paterno+' '+data.materno+"</td>";
            
            html+='<td>'+
                    '<div class="checkbox">'+
                      //'<label><input type="checkbox" value="'+data.id+'"></label>'+
                      '<button type="button" onclick="obtenerIdPersona('+data.id+',\''+nombres+'\');" id="btnadd'+data.id+'" name="btnadd'+data.id+'" class="btn btn-primary" data-toggle="modal" data-target="#myModalUser"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>'
                    '</div>';
            html+="</td>";
            

            html+="</tr>";
        });
    }
    else
    {
        html+="<tr>"+
                "<td colspan='4' style='text-align: center;'>No se encontraron datos de busqueda.</td>";
        html+="</tr>";
    }
    $("#div-result-bus").show(); 
    $("#tb_busqueda").html(html); 
};

obtenerIdPersona = function(id, nombres){
    $('#txt_person_id').val(id);
    $('#txt_administrado').val(nombres);
}

openPlantilla=function(id, tamano, tipo){
    window.open("formatolicencia/verdoclicenciaconstruc/"+id+"/"+tamano+"/"+tipo,
                "PrevisualizarPlantilla",
                "toolbar=no,menubar=no,resizable,scrollbars,status,width=900,height=700");
};

function justNumbers(e)
{
  var keynum = window.event ? window.event.keyCode : e.which;
  if ((keynum == 8) || (keynum == 46))
    return true;
  
  return /\d/.test(String.fromCharCode(keynum));
}

</script>
