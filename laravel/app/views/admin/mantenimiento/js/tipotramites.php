    <script type="text/javascript">
var cabeceraG=[]; // Cabecera del Datatable
var columnDefsG=[]; // Columnas de la BD del datatable
var targetsG=-1; // Posiciones de las columnas del datatable
var TipoTramitesG={id:0,nombre:"",estado:1}; // Datos Globales
$(document).ready(function() {
    /*  1: Onblur ,Onchange y para número es a travez de una función 1: 
        2: Descripción de cabecera
        3: Color Cabecera
    */
   
    slctGlobalHtml('slct_estado, #slct_solicitante, #slct_inicia, #slct_seguimiento, #slct_cant_solicitante','simple');
    var idG={   nombre        :'onBlur|Nombre Tipo de Servicio|#DCE6F1', //#DCE6F1
                solicitante   :'1|Quien es el solicitante|#DCE6F1',
                inicia        :'1|Quien inicia el trámite|#DCE6F1',
                seguimiento   :'1|Seguimiento|#DCE6F1',
                cant_solicitante :'1|Cantidad de solicitantes|#DCE6F1',
                estado        :'2|Estado|#DCE6F1', //#DCE6F1
             };

    var resG=dataTableG.CargarCab(idG);
    cabeceraG=resG; // registra la cabecera
    var resG=dataTableG.CargarCol(cabeceraG,columnDefsG,targetsG,1,'tipotramites','t_tipotramites');
    columnDefsG=resG[0]; // registra las columnas del datatable
    targetsG=resG[1]; // registra los contadores
    var resG=dataTableG.CargarBtn(columnDefsG,targetsG,1,'BtnEditar','t_tipotramites','fa-edit');
    columnDefsG=resG[0]; // registra la colunmna adiciona con boton
    targetsG=resG[1]; // registra el contador actualizado
    MostrarAjax('tipotramites');
    

    $('#tipotramiteModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-

      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' TipoTramite');
      $('#form_tipotramites_modal [data-toggle="tooltip"]').css("display","none");
      $("#form_tipotramites_modal input[type='hidden']").remove();

        if(titulo=='Nuevo'){
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_tipotramites_modal #slct_estado').val(1);
            $('#form_tipotramites_modal #slct_seguimiento').val(0);
            $('#form_tipotramites_modal #slct_cant_solicitante').val(1);
            ValidaInterno( 0 );
            $('#form_tipotramites_modal #txt_nombre').focus();
        } else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');

            $('#form_tipotramites_modal #txt_nombre').val( TipoTramitesG.nombre );
            $('#form_tipotramites_modal #slct_estado').val( TipoTramitesG.estado );
            $('#form_tipotramites_modal #slct_solicitante').val( TipoTramitesG.solicitante );
            $('#form_tipotramites_modal #slct_inicia').val( TipoTramitesG.inicia );
            $('#form_tipotramites_modal #slct_seguimiento').val( TipoTramitesG.seguimiento );
            $('#form_tipotramites_modal #slct_cant_solicitante').val( TipoTramitesG.cant_solicitante );
            ValidaInterno( TipoTramitesG.solicitante );
            $("#form_tipotramites_modal").append("<input type='hidden' value='"+TipoTramitesG.id+"' name='id'>");
        }
             $('#form_tipotramites_modal select').multiselect('rebuild');
    });

    $('#tipotramiteModal').on('hide.bs.modal', function (event) {
       $('#form_tipotramites_modal input').val('');
     //   var modal = $(this);
       // modal.find('.modal-body input').val('');
    });
});

ValidaInterno = function( v ){
    $("#form_tipotramites_modal .validacantsolicitante").show();
    if( v == 'Interno' ){
        $("#form_tipotramites_modal .validacantsolicitante").hide();
        $('#form_tipotramites_modal #slct_cant_solicitante').val( 1 );
        $('#form_tipotramites_modal #slct_cant_solicitante').multiselect( 'rebuild' );
    }
}

BtnEditar=function(btn,id){
    var tr = btn.parentNode.parentNode; // Intocable
    TipoTramitesG.id=id;
    TipoTramitesG.nombre=$(tr).find("td:eq(0)").text();
    TipoTramitesG.solicitante=$(tr).find("td:eq(1)").text();
    TipoTramitesG.inicia=$(tr).find("td:eq(2)").text();
    TipoTramitesG.seguimiento=$(tr).find("td:eq(3) .seguimiento").val();
    TipoTramitesG.cant_solicitante=$(tr).find("td:eq(4) .cant_solicitante").val();
    TipoTramitesG.estado=$(tr).find("td:eq(5)>span").attr("data-estado");
    $("#BtnEditar").click();
};

MostrarAjax=function(t){
    if( t=="tipotramites" ){
        if( columnDefsG.length>0 ){
            dataTableG.CargarDatos(t,'tipotramite','cargar',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    }
}

GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
    if(typeof(fn)!='undefined' && fn.col==1){
        return row.solicitante;
    }
    if(typeof(fn)!='undefined' && fn.col==2){
        return row.inicia;
    }
    if(typeof(fn)!='undefined' && fn.col==3){
        let seguimiento = 'No';
        if( row.seguimiento == 1 ){ seguimiento = 'Si'}
        return  seguimiento+
                "<input type='hidden' value='"+row.seguimiento+"' class='seguimiento'>";
    }
    if(typeof(fn)!='undefined' && fn.col==4){
        return  row.cant_solicitante+
                "<input type='hidden' value='"+row.cant_solicitante+"' class='cant_solicitante'>";
    }
    if(typeof(fn)!='undefined' && fn.col==5){
        var estadohtml='';
        estadohtml='<span id="'+row.id+'" onClick="activar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-danger">Inactivo</span>';
        if(row.estado==1){
            estadohtml='<span id="'+row.id+'" onClick="desactivar('+row.id+')" data-estado="'+row.estado+'" class="btn btn-success">Activo</span>';
        }
        return estadohtml;
    }
}
activarTabla=function(){
    $("#t_tipotramites").dataTable(); // inicializo el datatable    
};

activar = function(id){
    TipoTramites.CambiarEstadoTipoTramites(id, 1);
};
desactivar = function(id){
    TipoTramites.CambiarEstadoTipoTramites(id, 0);
};
Editar = function(){
    if(validaTipoTramites()){
        TipoTramites.AgregarEditarTipoTramite(1);
    }
};
Agregar = function(){
    if(validaTipoTramites()){
        TipoTramites.AgregarEditarTipoTramite(0);
    }
};

validaTipoTramites = function(){
    var r=true;
    if( $("#form_tipotramites_modal #txt_nombre").val()=='' ){
        alert("Ingrese Nombre de TipoTramite");
        r=false;
    }
    else if( $("#form_tipotramites_modal #slct_solicitante").val()=='' ){
        alert("Seleccione Quien es el solicitante");
        r=false;
    }
    else if( $("#form_tipotramites_modal #slct_inicia").val()=='' ){
        alert("Seleccione Quien inicia el trámite");
        r=false;
    }
    return r;
};
</script>