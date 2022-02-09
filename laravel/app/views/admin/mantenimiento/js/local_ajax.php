<script type="text/javascript">
var LocalObj;
var Locales = {
    AgregarEditarLocal:function(AE){
        var datos = $("#form_locales_modal").serialize().split("txt_").join("").split("slct_").join("");
        var accion = (AE==1) ? "local/editar" : "local/crear";

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
                    MostrarAjax('locales');
                    msjG.mensaje('success',obj.msj,4000);
                    $('#localModal .modal-footer [data-dismiss="modal"]').click();

                } else {
                    $.each(obj.msj, function(index, datos){
                        $("#error_"+index).attr("data-original-title", datos);
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
    CambiarEstadoLocales: function(id, AD){
        $("#form_locales_modal").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_locales_modal").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos = $("#form_locales_modal").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'local/cambiarestado',
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
                    MostrarAjax('locales');
                    msjG.mensaje('success',obj.msj,4000);
                    $('#localModal .modal-footer [data-dismiss="modal"]').click();
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
    }
};
</script>
