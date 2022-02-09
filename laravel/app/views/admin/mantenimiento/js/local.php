<script type="text/javascript">
var cabeceraG=[]; // Cabecera del Datatable
var columnDefsG=[]; // Columnas de la BD del datatable
var targetsG=-1; // Posiciones de las columnas del datatable
var LocalG={id:0,local:"", direccion:"", fecha_inicio:"", fecha_final:"" ,estado:1}; // Datos Globales
$(document).ready(function() {
    /*  1: Onblur ,Onchange y para número es a travez de una función 1: 
        2: Descripción de cabecera
        3: Color Cabecera
    */
    var idG={   local        :'onBlur|Nombre Local|#DCE6F1', //#DCE6F1
                direccion    :'onBlur|Dirección|#DCE6F1', //#DCE6F1
                fecha_inicio :'1|Fecha Inicio|#DCE6F1', //#DCE6F1
                fecha_final  :'1|Fecha Cierre|#DCE6F1', //#DCE6F1
                estado       :'2|Estado|#DCE6F1', //#DCE6F1
             };

    var resG=dataTableG.CargarCab(idG);
    cabeceraG=resG; // registra la cabecera
    var resG=dataTableG.CargarCol(cabeceraG,columnDefsG,targetsG,1,'locales','t_locales');
    columnDefsG=resG[0]; // registra las columnas del datatable
    targetsG=resG[1]; // registra los contadores
    var resG=dataTableG.CargarBtn(columnDefsG,targetsG,1,'BtnEditar','t_locales','fa-edit');
    columnDefsG=resG[0]; // registra la colunmna adiciona con boton
    targetsG=resG[1]; // registra el contador actualizado
    MostrarAjax('locales');

    $('#localModal').on('shown.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-

      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Local');
      $('#form_locales_modal [data-toggle="tooltip"]').css("display","none");
      $("#form_locales_modal input[type='hidden']").remove();

        if(titulo=='Nuevo'){
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_locales_modal #slct_estado').val(1);
            $('#form_locales_modal #txt_local').focus();
        } else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_locales_modal #txt_local').val( LocalG.local );
            $('#form_locales_modal #txt_direccion').val( LocalG.direccion );
            $('#form_locales_modal #txt_fecha_inicio').val( LocalG.fecha_inicio );
            $('#form_locales_modal #txt_fecha_final').val( LocalG.fecha_final );
            $('#form_locales_modal #slct_estado').val( LocalG.estado );
            $("#form_locales_modal").append("<input type='hidden' value='"+LocalG.id+"' name='id'>");
        }
    });

    $('#localModal').on('hidden.bs.modal', function (event) {
        var modal = $(this);
        modal.find('.modal-body input').val('');
    });

    formatoFecha = 'yyyy-mm-dd';
    minView= 2;
    maxView= 4;
    startView= 2;
    $("#form_locales_modal .fecha").datetimepicker({
        format: formatoFecha,
        language: 'es',
        showMeridian: false,
        time: false,
        minView: minView,
        maxView: maxView,
        startView: startView, // 1->hora, 2->dia , 3->mes
        autoclose: true,
        todayBtn: false
    });
});

BtnEditar=function(btn,id){
    var tr = btn.parentNode.parentNode; // Intocable
    LocalG.id=id;
    LocalG.local=$(tr).find("td:eq(0)").text();
    LocalG.direccion=$(tr).find("td:eq(1)").text();
    LocalG.fecha_inicio=$(tr).find("td:eq(2)").text();
    LocalG.fecha_final=$(tr).find("td:eq(3)").text();
    LocalG.estado=$(tr).find("td:eq(4)>span").attr("data-estado");
    $("#BtnEditar").click();
};

MostrarAjax=function(t){
    if( t=="locales" ){
        if( columnDefsG.length>0 ){
            dataTableG.CargarDatos(t,'local','cargar',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    }
}

GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
    if(typeof(fn)!='undefined' && fn.col==4){
        var estadohtml='';
        estadohtml='<span id="'+row.id+'" onClick="activar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-danger">Inactivo</span>';
        if(row.estado==1){
            estadohtml='<span id="'+row.id+'" onClick="desactivar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-success">Activo</span>';
        }
        return estadohtml;
    }

    if(typeof(fn)!='undefined' && fn.col==2){
        return $.trim(row.fecha_inicio);
    }

    if(typeof(fn)!='undefined' && fn.col==3){
        return $.trim(row.fecha_final);
    }
}

activar = function(id){
    Locales.CambiarEstadoLocales(id, 1);
};
desactivar = function(id){
    Locales.CambiarEstadoLocales(id, 0);
};
Editar = function(){
    if(validaLocales()){
        Locales.AgregarEditarLocal(1);
    }
};
Agregar = function(){
    if(validaLocales()){
        Locales.AgregarEditarLocal(0);
    }
};
validaLocales = function(){
    var r=true;
    if( $("#form_locales_modal #txt_local").val()=='' ){
        msj = "Ingrese Nombre del Local";
        msjG.mensaje('warning',msj,4000);
        $("#form_locales_modal #txt_local").focus();
        r=false;
    }
    else if( $("#form_locales_modal #txt_direccion").val()=='' ){
        msj = "Ingrese Dirección del Local";
        msjG.mensaje('warning',msj,4000);
        $("#form_locales_modal #txt_direccion").focus();
        r=false;
    }
    else if( $("#form_locales_modal #txt_fecha_inicio").val()=='' ){
        msj = "Ingrese Fecha de Inicio del Local";
        msjG.mensaje('warning',msj,4000);
        $("#form_locales_modal #txt_fecha_inicio").focus();
        r=false;
    }
    else if( $("#form_locales_modal #txt_fecha_final").val()=='' ){
        msj = "Ingrese Fecha de Cierre del Local";
        msjG.mensaje('warning',msj,4000);
        $("#form_locales_modal #txt_fecha_final").focus();
        r=false;
    }
    return r;
};
</script>
