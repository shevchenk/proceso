<script type="text/javascript">
var ClasificadorTramites={
    AgregarEditarClasificadorTramite:function(AE){
        var datos = $("#form_clasificadortramites_modal").serialize().split("txt_").join("").split("slct_").join("");
        var accion = (AE==1) ? "clasificadortramite/editar" : "clasificadortramite/crear";

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
                  
                    MostrarAjax('clasificadortramites');
                    msjG.mensaje('success',obj.msj,4000);
                    $('#clasificadortramitesModal .modal-footer [data-dismiss="modal"]').click();

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
    CargarClasificadorTramites:function(evento){
        $.ajax({
            url         : 'clasificadortramite/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                
            },
            success : function(obj) {
                var html="";
                var estadohtml="";
                if(obj.rst==1){
                    MostrarAjax('clasificadortramites');
                    $.each(obj.datos,function(index,data){
                        estadohtml='<span id="'+data.id+'" onClick="activar('+data.id+')" class="btn btn-danger">Inactivo</span>';
                        if(data.estado==1){
                            estadohtml='<span id="'+data.id+'" onClick="desactivar('+data.id+')" class="btn btn-success">Activo</span>';
                        }

                        html+="<tr>"+
                            "<td id='nombre_"+data.id+"'>"+data.nombre+"</td>"+
                            "<td id='estado_"+data.id+"' data-estado='"+data.estado+"'>"+estadohtml+"</td>"+
                            '<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#clasificadortramiteModal" data-id="'+data.id+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';

                        html+="</tr>";
                    });
                }
                $("#tb_clasificadortramites").html(html); 
                evento();  
            },
            error: function(){
            }
        });
    },
      CambiarEstadoClasificadorTramites: function(id, AD){
        $("#form_clasificadortramites_modal").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_clasificadortramites_modal").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos = $("#form_clasificadortramites_modal").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'clasificadortramite/cambiarestado',
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
                    MostrarAjax('clasificadortramites');
                    msjG.mensaje('success',obj.msj,4000);
                    $('#clasificadortramiteModal .modal-footer [data-dismiss="modal"]').click();
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
    CambiarEstadoEFClasificadorTramites: function(id, AD){
        $("#form_clasificadortramites_modal").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_clasificadortramites_modal").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos = {id: id, estado: AD};
        $.ajax({
            url         : 'clasificadortramite/cambiarestadoef',
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
                    MostrarAjax('clasificadortramites');
                    msjG.mensaje('success',obj.msj,4000);
                    $('#clasificadortramiteModal .modal-footer [data-dismiss="modal"]').click();
                } else {
                    msjG.mensaje('warning',obj.msj,4000);
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
