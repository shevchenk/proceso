<script type="text/javascript">
var persona_id, cargos_selec=[], PersonaObj;
var Persona={
    AgregarEditarPersona:function(AE){
        $("#form_personas_modal input[name='cargos_selec']").remove();
        //$("#form_personas_modal").append("<input type='hidden' value='"+cargos_selec+"' name='cargos_selec'>");
        
        var datos=$("#form_personas_modal").serialize().split("txt_").join("").split("slct_").join("");
        var accion="personafinal/crear";
        if(AE==1){
            accion="personafinal/editar";
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
                   
                    $('#t_personas').dataTable().fnDestroy();
                    MostrarAjax('persona');
                    //Persona.CargarPersonas(activarTabla);
                    
                    msjG.mensaje('success',obj.msj,4000);
                    $('#personaModal .modal-footer [data-dismiss="modal"]').click();
                    //cargos_selec=[];
                }
                else if(obj.rst==3){
                     msjG.mensaje('warning',obj.msj,4000);
                }
                else{ 
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
    CargarPersonas:function(evento){
        $.ajax({
            url         : 'persona/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                
                slctGlobal.listarSlct('cargo','slct_cargos','simple');
                //para que cargue antes el cargo
            },
            success : function(obj) {
                if(obj.rst==1){
                    MostrarAjax('persona');
                   // HTMLCargarPersona(obj.datos);
                    //PersonaObj=obj.datos;
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });
    },
    CargarAreas:function(persona_id){
        //getOpciones
        $.ajax({
            url         : 'personafinal/cargarareas',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {persona_id:persona_id},
            async       : false,
            beforeSend : function() {
                
            },
            success : function(obj) {
                //CARGAR areas
                if(obj.datos.length > 0 ){

                    $.each(obj.datos,function(index,data){
                    
                        var html="";
                        html="<li class='list-group-item'><div class='row'>";
                        html+="<div class='col-sm-4' id='cargo_"+data.id+"'><input type='hidden' value='"+data.id+"' name='cargo_id[]'><h5>"+data.nombre+"</h5></div>";
                        var areas = data.info.split(",");
                        html+="<div class='col-sm-6'><select class='form-control' multiple='multiple' name='slct_areas_"+data.id+"[]' id='slct_areas_"+data.id+"'></select></div>";
                        //var envio = {cargo_id: data.id};

                        html+='<div class="col-sm-2">';
                        html+='<button type="button" id="'+data.id+'" Onclick="EliminarArea(this)" class="btn btn-danger btn-sm" >';
                        html+='<i class="fa fa-minus fa-sm"></i> </button></div>';
                        html+="</div></li>";

                        $("#t_cargoPersona").append(html);
                        $("#slct_areas_"+data.id).html( $("#slct_area_aux").html() );
                        slctGlobalHtml('slct_areas_'+data.id, 'multiple', areas);
                        //cargos_selec.push(data.id);
                    });
                }
            },
            error: function(){
            }
        });
    },
    CambiarEstadoPersonas:function(id,AD){
        $("#form_personas_modal").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_personas_modal").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_personas_modal").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'persona/cambiarestado',
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
                    $('#t_personas').dataTable().fnDestroy();
                    MostrarAjax('persona');
                    //Persona.CargarPersonas(activarTabla);
                  
                    msjG.mensaje('success',obj.msj,4000);
                    $('#personaModal .modal-footer [data-dismiss="modal"]').click();
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
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);

            }
        });

    },
};
</script>
