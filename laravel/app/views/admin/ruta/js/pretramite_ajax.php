<script type="text/javascript">
var Bandeja={
    MostrarPreTramites:function(data,evento){
        $.ajax({
            url         : 'pretramite/listarpretramites',
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
                    evento(obj.datos);                    
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    DetallePreTramite:function(data){
        $.ajax({
            url         : 'pretramite/anexosbytramite',
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
                    /*evento(obj.data);           */         
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    GetMisDatos:function(data,evento){
        $.ajax({
            url         : 'pretramite/misdatos',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
              /*  $("body").append('<div class="overlay"></div><div class="loading-img"></div>');*/
            },
            success : function(obj) {
                /*$(".overlay,.loading-img").remove();*/
                evento(obj);
            },
            error: function(){
            /*    $(".overlay,.loading-img").remove();*/
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    GetTipoSolicitante:function(data,evento){
        $.ajax({
            url         : 'tiposolicitante/listar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
              /*  $("body").append('<div class="overlay"></div><div class="loading-img"></div>');*/
            },
            success : function(obj) {
                /*$(".overlay,.loading-img").remove();*/
                if(obj.rst==1){
                    evento(obj.datos);                    
                }
            },
            error: function(){
            /*    $(".overlay,.loading-img").remove();*/
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    getEmpresasByPersona:function(data,evento){
        $.ajax({
            url         : 'pretramite/empresasbypersona',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
              /*  $("body").append('<div class="overlay"></div><div class="loading-img"></div>');*/
            },
            success : function(obj) {
                /*$(".overlay,.loading-img").remove();*/
                if(obj.rst==1){
                    evento(obj.datos);                    
                }
            },
            error: function(){
            /*    $(".overlay,.loading-img").remove();*/
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    getClasificadoresTramite:function(data,evento){
        $.ajax({
            url         : 'pretramite/clasificadorestramite',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
              /*  $("body").append('<div class="overlay"></div><div class="loading-img"></div>');*/
            },
            success : function(obj) {
                /*$(".overlay,.loading-img").remove();*/
                if(obj.rst==1){
                    evento(obj.datos);                    
                }
            },
            error: function(){
            /*    $(".overlay,.loading-img").remove();*/
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    getRequisitosbyclatramite:function(data,evento,tramite){
        $.ajax({
            url         : 'pretramite/requisitosbyctramite',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
              /*  $("body").append('<div class="overlay"></div><div class="loading-img"></div>');*/
            },
            success : function(obj) {
                /*$(".overlay,.loading-img").remove();*/
                if(obj.rst==1){
                    evento(obj.datos,tramite);                    
                }
            },
            error: function(){
            /*    $(".overlay,.loading-img").remove();*/
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    GuardarPreTramite:function(data,evento){
        var datos = {'info':data};
        $.ajax({
            url         : 'pretramite/createpretramite',
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
                    msjG.mensaje("success", obj.msj,3000);
                    evento();
                    $(".crearPreTramite").addClass('hidden');
                    $('#FormCrearPreTramite').find('input[type="text"],input[type="email"],textarea,select').val('');        
                }
                else{
                    msjG.mensaje("warning", obj.msj,3000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    GetPreTramitebyid:function(data,evento){
        $.ajax({
            url         : 'pretramite/getbyid',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
                /*$("body").append('<div class="overlay"></div><div class="loading-img"></div>');*/
            },
            success : function(obj) {
               /* $(".overlay,.loading-img").remove();*/
                if(obj.rst==1){
                    evento(obj.datos);                       
                }
            },
            error: function(){
               /* $(".overlay,.loading-img").remove();*/
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    },
    GetAreasbyCTramite:function(data,info){
        $.ajax({
            url         : 'pretramite/listar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
            },
            success : function(obj) {
                if(obj.rst==1){
                    var result = obj.datos;
                    if(result.length > 1){
                        $(".rowArea").removeClass('hidden');
                         $("#buscartramite #reporte").hide();
                        slctGlobal.listarSlct('pretramite','slcAreasct','simple',null,data);
                        document.querySelector('#txt_clasificador_id').value=info.id;
                        document.querySelector('#txt_clasificador_nomb').value=info.nombre;
                    }else{
                        info.area = result[0].nombre;
                        info.areaid = result[0].id;
                        poblateData('tramite',info);
                        $('#buscartramite').modal('hide');
                    }            
                }
            },
            error: function(){
                msjG.mensaje("danger","Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.",3000);
            }
        });
    }

};
</script>
