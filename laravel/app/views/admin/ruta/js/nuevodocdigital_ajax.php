<script type="text/javascript">
var docdigital={
    AgregarEditar:function(areas,event,poblate = 0){
        $("#formNuevoDocDigital input[name='word']").remove();
        $("#formNuevoDocDigital").append("<input type='hidden' value='"+CKEDITOR.instances.plantillaWord.getData()+"' name='word'>");
        $("#txt_titulofinal").val($("#lblDocumento").text()+$(".txttittle").val()+$("#lblArea").text());
        var datos=$("#formNuevoDocDigital").serialize().split("txt_").join("").split("slct_").join("");
        datos+="&areasselect="+JSON.stringify(areas);
        var accion="documentodig/crear";
        if(event == 1){
            var accion="documentodig/editar";
        }

        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    alertBootstrap('success', obj.msj, 6);
                    $("#NuevoDocDigital").modal('hide');

                  /*  openPrevisualizarPlantilla('',obj.iddocdigital);*/
                    if(poblate != 0){
                        var campos = $("#txt_campos").attr('c_text');
                        $("#"+$("#txt_campos").attr('c_text')).val(obj.nombre);
                        $("#"+$("#txt_campos").attr('c_id')).val(obj.iddocdigital);
                    }

                }
                else{
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    Cargar:function(evento,campos,data){
        $.ajax({
            url         : 'documentodig/cargar',
            type        : 'POST',
            cache       : false,
            data : data,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    evento(obj.datos,campos);
                   /* PlantillaObj=obj.datos;*/
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    CargarDetalle:function(id){
        var datos = {
            id:id
        };
        $.ajax({
            url         : 'plantilla/cargardetalle',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    if (obj.datos == null) {
                        $('#word').val(obj.datos);
                    } else {
                        $('#word').val('');
                    }
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    CambiarEstado:function(id,AD){
       /* $("#form_plantilla").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_plantilla").append("<input type='hidden' value='"+AD+"' name='estado'>");*/
       /* var datos=$("#form_plantilla").serialize().split("txt_").join("").split("slct_").join("");*/
        var datos={'id':id,'estado':AD};
        $.ajax({
            url         : 'plantilladoc/cambiarestado',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    $('#t_plantilla').dataTable().fnDestroy();
                    Plantillas.Cargar(activarTabla);
                   /* alertBootstrap('success', obj.msj, 6);
                    $('#plantillaModal .modal-footer [data-dismiss="modal"]').click();*/
                }
               /* else{
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }*/
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    Previsualizar:function(){
    },
    CargarInfo:function(data,evento){
        $.ajax({
            url         : 'plantilladoc/cargar',
            type        : 'POST',
            cache       : false,
            data        : data,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    evento(obj.datos);
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    CargarAreas:function(evento){
        $.ajax({
            url         : 'area/areasgerencia',
            type        : 'POST',
            cache       : false,
         /*   data        : data,*/
            dataType    : 'json',
            beforeSend : function() {
              /*  $("body").append('<div class="overlay"></div><div class="loading-img"></div>');*/
            },
            success : function(obj) {
                if(obj.rst==1){
                    evento(obj.datos);
                }
               /* $(".overlay,.loading-img").remove();*/
            },
            error: function(){
                /*$(".overlay,.loading-img").remove();*/
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    CargarCorrelativo:function(data,evento){
        $.ajax({
            url         : 'documentodig/correlativo',
            type        : 'POST',
            cache       : false,
            data        : data,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    evento(obj.datos);
                }  
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                        '<i class="fa fa-ban"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },
    EliminarDocumento:function(data){
        $.ajax({
            url         : 'documentodig/cambiarestadodoc',
            type        : 'POST',
            cache       : false,
            data        : data,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    Plantillas.Cargar(HTMLCargar);
                }  
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                        '<i class="fa fa-ban"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },
    GenerarDoc:function(){
        var accion="documentodig/generardoc";
        var datos=$("#Form_lstDigital").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    alertBootstrap('success', obj.msj, 6);
                    $("#listDocDigital #td_documento").text(obj.titulo);
                    $("#listDocDigital #txt_titulofinal").val(obj.titulo);
                    $("#listDocDigital #txt_titulo").val(obj.correlativo);
                    $("#listDocDigital #txt_doc_digital_id").val(obj.doc_digital_id);
                    $("#listDocDigital #img_qr").attr('src',obj.png);
                    CargarDocumentosFecha();
                }
                else{
                    alertBootstrap('warning', obj.msj, 6);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    GuardarArchivo:function(){
        var accion="documentodig/guardararchivo";
        var datos=$("#Form_lstDigital").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    alertBootstrap('success', obj.msj, 6);
                    CargarDocumentosFecha();
                }
                else{
                    alertBootstrap('warning', obj.msj, 6);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
    ActualizarDoc:function(datos){
        var accion="documentodig/actualizararchivo";
        
        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                $("#listDocDigital #td_documento").text(obj.titulo);
                $("#listDocDigital #txt_doc_digital_id").val(obj.doc_digital_id);
                $("#listDocDigital #img_qr").attr('src',obj.png);
                $("#listDocDigital #doc_url, #listDocDigital #doc_nombre").val('');
                if(obj.doc_url != ''){
                    $("#listDocDigital #doc_url").val(obj.doc_url);
                }
                else{
                    $("#listDocDigital #doc_nombre").val(obj.doc_archivo);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                alertBootstrap('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente', 6);
            }
        });
    },
}
</script>
