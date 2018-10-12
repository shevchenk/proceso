<script type="text/javascript">
var Eje1={
    Eje: function(val){
        $("#form_actividad_modal").append("<input type='hidden' value='"+id+"' name='id'>");
        $.ajax({
            url         : '',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay, .loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });
    },

};
</script>
