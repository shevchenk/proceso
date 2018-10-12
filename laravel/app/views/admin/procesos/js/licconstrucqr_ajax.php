<script type="text/javascript">

var Data = {
    AgregarEditarRol:function(AE){
        var datos = $("#form_forlic").serialize().split("txt_").join("").split("slct_").join("");
        //var accion = (AE==1) ? "actividadcategoria/editar" : "actividadcategoria/crear";
        $.ajax({
            url         : 'formatolicencia/crearliccontruc',
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
                    //window.location='formatolicencia/verdoclicenciaconstruc/1';
                    //window.open('formatolicencia/verdoclicenciaconstruc/'+obj.id+'/4/0','_blank');
                    window.open("formatolicencia/verdoclicenciaconstruc/"+obj.id+"/4/0",
                                "PrevisualizarPlantilla",
                                "toolbar=no,menubar=no,resizable,scrollbars,status,width=900,height=700");
                    /*swal({
                          title: "Excelente!",   
                          text: obj.msj,
                          type: "success",
                          closeOnConfirm: true
                          }, function(){
                             $('input[type="text"]').not('.data_fija').val('');
                             $('#txt_person_id').val('');
                             Data.CargarDatos(activarTabla);
                      });*/
                    $('input[type="text"]').not('.data_fija').val('');
                    $('#txt_person_id').val('');
                    Data.CargarDatos(activarTabla);

                } else {
                    swal("Mensaje!", "Debe ingresar el Expediente!", "error")
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });
    },

    CargarDatos:function(evento){
        $.ajax({
            url         : 'formatolicencia/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                slctGlobal.listarSlct('menu','slct_menus','simple');//para que cargue antes el menu
            },
            success : function(obj) {
                if(obj.rst==1){
                    HTMLDatos(obj.datos);
                    CargoObj=obj.datos;
                }
                $(".overlay,.loading-img").remove();
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

    buscarPersona:function(data){
        $.ajax({
            url         : 'formatolicencia/buscarpersona',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    HTMLDatosBusqueda(obj.datos);
                    //console.log(obj.datos);
                    $('#btnbuscar_user').attr('disabled', false).text('Buscar');
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

    insertarPersona:function(AE){
        var datos = $("#form_data").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'formatolicencia/crearpersona',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            success : function(obj) {
                $(".overlay, .loading-img").remove();
                if(obj.rst==1){
                   $('#form_data #btnsave_data').attr('disabled', false).html('<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>');
                   $("#myModalUser .close").click();

                   $('#txt_person_id').val(obj.id);
                   $('#txt_administrado').val(obj.nombres);

                } else {
                    swal("Mensaje!", "Debe ingresar el DNI!", "error")
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                msjG.mensaje('danger','<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.',4000);
            }
        });
    },

    CambiarEstadoLicenContruc:function(id,AD){
        $("#form_forlic").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_forlic").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_forlic").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'formatolicencia/cambiarestado',
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
                    $('#t_cargos').dataTable().fnDestroy();
                    Data.CargarDatos(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-info">'+
                                        '<i class="fa fa-info"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#cargoModal .modal-footer [data-dismiss="modal"]').click();
                    
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
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.'+
                                '</div>');
            }
        });
    }

};
</script>
