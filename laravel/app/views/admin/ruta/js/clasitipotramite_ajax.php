<script type="text/javascript">
var Pois={
    ListarAreas:function(evento){
        var datos = $("#form_campo_asignacion").serialize().split("txt_").join("").split("slct_").join("");
        var accion = "clasificadortramite/listarareas";
        
        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            async       : false,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    evento(obj);
                } else {
                    var cont = 0;
                    $.each(obj.msj, function(index, datos){
                        cont++;
                         if(cont==1){
                            msjG.mensaje('warning',obj.msj,4000);
                       }
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    ListarCampos:function(evento, asignacion){
        var datos = $("#form_campo").serialize().split("txt_").join("").split("slct_").join("");
        
        if( typeof(asignacion) != 'undefined' ){
            var datos = $("#form_campo_asignacion").serialize().split("txt_").join("").split("slct_").join("");
        }
        var accion = "clasificadortramite/listarcampos";
        
        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            async       : false,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    evento(obj.data);
                    msjG.mensaje('success',obj.msj,4000);
                } else {
                    var cont = 0;
                    $.each(obj.msj, function(index, datos){
                        cont++;
                         if(cont==1){
                            msjG.mensaje('warning',obj.msj,4000);
                       }
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    ListarCamposAreas:function(evento, datos){
        if( typeof(datos) == 'undefined' ){
            datos = $("#form_campo_asignacion").serialize().split("txt_").join("").split("slct_").join("");
        }
        console.log(datos);
        var accion = "clasificadortramite/listarcamposareas";
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    if( typeof(datos.sub) == 'undefined' ){
                        evento(obj.data, obj.ruta_flujo_id);
                    }
                    else{
                        evento(obj.data, obj.norden, obj.ruta_flujo_id);
                    }
                    msjG.mensaje('success',obj.msj,4000);
                } else {
                    msjG.mensaje('warning',obj.msj,4000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    ListarCamposAreasSub:function(datos,evento){
        var accion = "clasificadortramite/listarcamposareas";
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    evento(obj.data, obj.norden, obj.ruta_flujo_id);
                    msjG.mensaje('success',obj.msj,4000);
                } else {
                    msjG.mensaje('warning',obj.msj,4000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    RegistrarCampos:function(){
        var datos = $("#form_campo").serialize().split("txt_").join("").split("slct_").join("");
        var accion = "clasificadortramite/registrarcampos";
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    /** foreach ****/
                    $("#add_campo .campos").each(function(index, el) {
                        $(this).find('.campo_id').val( obj.lista[index] );
                    });
                    /****/
                    msjG.mensaje('success',obj.msj,4000);
                } else {
                    var cont = 0;
                    $.each(obj.msj, function(index, datos){
                        cont++;
                         if(cont==1){
                            msjG.mensaje('warning',obj.msj,4000);
                       }
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    AsignarCampos:function(){
        $("#form_campo_asignacion #ruta_flujo_id").attr("disabled","disabled");
        var datos = $("#form_campo_asignacion").serialize().split("txt_").join("").split("slct_").join("");
        var accion = "clasificadortramite/asignarcampos";
        $("#form_campo_asignacion #ruta_flujo_id").removeAttr("disabled");
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    msjG.mensaje('success',obj.msj,4000);
                } else {
                    var cont = 0;
                    $.each(obj.msj, function(index, datos){
                        cont++;
                         if(cont==1){
                            msjG.mensaje('warning',obj.msj,4000);
                       }
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    AsignarCampo:function(datos){
        var accion = "clasificadortramite/asignarcampo";
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    msjG.mensaje('success',obj.msj,4000);
                } else {
                    msjG.mensaje('warning',obj.msj,4000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    AgregarEditarCostoPersonal:function(AE){
        var datos = $("#form_requisitos_modal").serialize().split("txt_").join("").split("slct_").join("");
        var id=$("#form_requisitos_modal #txt_poi_id").val();
        var accion = (AE==1) ? "clasificadortramite/editarrequisito" : "clasificadortramite/crearrequisito";
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    data={id:id};
                    Pois.CargarCostoPersonal(data);
                    $("#form_requisitos_modal input[type='hidden']").not("#form_requisitos_modal #txt_poi_id").remove();
                    msjG.mensaje('success',obj.msj,4000);
                    $('#requisitoModal .modal-footer [data-dismiss="modal"]').click();

                } else {
                    var cont = 0;

                    $.each(obj.msj, function(index, datos){
                        cont++;
                         if(cont==1){
                            alert(datos[0]);
                       }

                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    CambiarEstadoCostoPersonal: function(id, AD){
        $("#form_requisitos_modal").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_requisitos_modal").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var id=$("#form_requisitos_modal #txt_poi_id").val();
        var datos = $("#form_requisitos_modal").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'clasificadortramite/cambiarestadorequisito',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay, .loading-img").remove();

                if (obj.rst==1) {
                    data={id:id};
                    Pois.CargarCostoPersonal(data);
                     $("#form_requisitos_modal input[type='hidden']").not("#form_requisitos_modal #txt_poi_id").remove();
                    msjG.mensaje('success',obj.msj,4000);
                    $('#requisitoModal .modal-footer [data-dismiss="modal"]').click();
                } else {
                    $.each(obj.msj, function(index, datos) {
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });
    },
    CargarCostoPersonal:function( data ){
        $.ajax({
            url         : 'clasificadortramite/listarrequisito',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    costopersonalHTML(obj.datos);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.'+
                                '</div>');
            }
        });
    }, 
    
    AgregarEditarEstratPei:function(AE){
        var datos = $("#form_tipotramites_modal").serialize().split("txt_").join("").split("slct_").join("");
        var accion = (AE==1) ? "tipotramite/editar" : "tipotramite/crear";
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    Pois.CargarEstratPei();
                    var datos={estado:1};slctGlobal.listarSlct('tipotramite','slct_tipo_tramite','simple',null,datos);
                    msjG.mensaje('success',obj.msj,4000);
                    $('#tipotramiteModal .modal-footer [data-dismiss="modal"]').click();

                } else {
                    var cont = 0;

                    $.each(obj.msj, function(index, datos){
                        cont++;
                         if(cont==1){
                            alert(datos[0]);
                       }

                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
    CambiarEstadoEstratPei: function(id, AD){
        $("#form_tipotramites_modal").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_tipotramites_modal").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var id=$("#form_tipotramites_modal #txt_poi_id").val();
        var datos = $("#form_tipotramites_modal").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'tipotramite/cambiarestado',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay, .loading-img").remove();

                if (obj.rst==1) {
                     data={id:id};
                     Pois.CargarEstratPei(data);
                     var datos={estado:1};slctGlobal.listarSlct('tipotramite','slct_tipo_tramite','simple',null,datos);
                     $("#form_tipotramites_modal input[type='hidden']").not("#form_estrat_pei_modal #txt_poi_id").remove();
                     msjG.mensaje('success',obj.msj,4000);
                     $('#tipotramiteModal .modal-footer [data-dismiss="modal"]').click();
                } else {
                    $.each(obj.msj, function(index, datos) {
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });
    },
    CargarEstratPei:function(  ){
        $.ajax({
            url         : 'tipotramite/listartipotramite',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    estratpeiHTML(obj.datos);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.'+
                                '</div>');
            }
        });
    },
    agregarProceso:function(){

        var datos=$("#form_actividad").serialize().split("txt_").join("").split("slct_").join("").split("_modal").join("");
        $.ajax({
            url         : 'clasificadortramite/agregarproceso',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    $("#tb_ruta_flujo").html("");
                    $("#form_actividad input[type='hidden'],#form_actividad input[type='text'],#form_actividad select,#form_actividad textarea").not('.mant').val("");
                    MostrarAjax('clasificadortramites');
                        $("#msj").html('<div class="alert alert-dismissable alert-success">' +
                        '<i class="fa fa-ban"></i>' +
                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
                        '<b>' + obj.msj + '</b>' +
                        '</div>');
                        $("#msj").effect('shake');
 //                       $("#msj").fadeOut(4000);
                }
                else{
                    alert(obj.msj);
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });
    },
    ActualizarRutaDetalle:function(datos){
        var accion = "clasificadortramite/actualizarrutadetalle";
        
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
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                    msjG.mensaje('success',obj.msj,4000);
                } else {
                    msjG.mensaje('warning',obj.msj,4000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });

    },
};
</script>
