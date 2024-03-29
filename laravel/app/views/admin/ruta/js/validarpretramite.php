<script type="text/javascript">
var area_id_GVP = 0;
$(document).ready(function() {
    ListarPreTramites();

    slctGlobal.listarSlct('persona','slct_persona','simple',null,{apellido_nombre:1});
    slctGlobalHtml('slct_estado_tramite','multiple');


    $(document).on('click', '#btnImage', function(event) {
        $('#txt_file').click();
    });

    $(document).on('change', '#txt_file', function(event) {
        readURLI(this, 'file');
    });
    
     $('#buscartramite').on('hidden.bs.modal', function(){
        $(".rowArea").addClass('hidden');
    });

    $("#btn_agregar").click( ()=>{
        let html = '';
        const now = new Date();
        let id = now.getTime();
        html =  '<tr class="'+id+'">'+
                    "<td class='input-group'>"+
                        '<input type="text" readonly class="form-control" id="pdf_nombre'+id+'"  name="pdf_nombre[]" value="" readonly="">'+
                        '<input type="text" style="display: none;" id="pdf_archivo'+id+'" name="pdf_archivo[]">'+
                        '<div class="input-group-btn">'+
                        '<label class="btn btn-warning btn-flat">'+
                            '<i class="fa fa-file-pdf-o fa-lg" style="margin-left:5px;"></i>'+
                            '<i class="fa fa-file-excel-o fa-lg" style="margin-left:5px;"></i>'+
                            '<i class="fa fa-file-image-o fa-lg" style="margin-left:5px;"></i>'+
                            '<i class="fa fa-file-powerpoint-o fa-lg" style="margin-left:5px;"></i>'+
                            '<input type="file" style="display: none;" onchange="masterG.onImagen(event,\'#pdf_nombre'+id+'\',\'#pdf_archivo'+id+'\',\'#pdf_img'+id+'\');">'+
                        '</label>'+
                        '</div>'+
                    '</td>'+
                    "<td>"+
                        '<a><img id="pdf_img'+id+'" class="img-circle" style="height: 80px;width: 140px;border-radius: 8px;border: 1px solid grey;margin-top: 5px;padding: 8px"></a>'+
                    '</td>'+
                    '<td><a class="btn btn-danger btn-lg" onClick="EliminarArchivo('+id+');">'+
                        '<i class="fa fa-trash fa-lg"></i>'+
                    '</a></td>'+
                "</tr>";
        $("#tb_archivos").append(html);
    });

    function readURLI(input, tipo) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                if (tipo == 'file') {                 
                    $('#btnImage').text('IMAGEN CARGADA');
                    $('#btnImage').addClass('btn btn-success');
                    $('.img-tramite').attr('src',e.target.result);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("form[name='FormTramite']").submit(function(e) {
        e.preventDefault();
        var r = true;
        if( $("#txt_usertelf").val()=='' && $("#txt_usercel").val()==''){
            msjG.mensaje("warning", 'Ingrese número de teléfono y/o número de celular',5000);
            r = false;
        }
        else if( $("#txt_useremail").val()=='' ){
            msjG.mensaje("warning", 'Ingrese email',3000);
            r = false;
        }
        else if( $("#txt_userdirec").val()=='' ){
            msjG.mensaje("warning", 'Ingrese dirección',3000);
            r = false;
        }
        else if( $('input[name="rdb_estado"]:checked').val()*1 == 2 && $.trim($("#txt_observaciones").val()) == '' ){
            msjG.mensaje("warning", 'Ingrese la observación del trámite a desaprobar',4000);
            r = false;
        }
        
        
        $(".DatosPersonalizadosG .glyphicon-remove").each( function(index){
            id = this.dataset.id;
            campo = $("#campo"+ id +" .form-control").attr("data-campo");
            if( r == true ){
                msjG.mensaje("warning","Se requiere dato del campo: "+ campo,5000);
                $("#campo"+ id +" .form-control").focus();
            }
            r = false;
        });

        if( $.trim( $('input[name="rdb_estado"]:checked').val() ) == '' && r == true ){
            msjG.mensaje("warning", 'Seleccione estado del servicio',4000);
            r = false;
        }

        if( r == true ){
            $.ajax({
                type: "POST",
                url: 'tramitec/create',
                data: new FormData($(this)[0]),
                processData: false,
                contentType: false,
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function (obj) {
                    $(".overlay,.loading-img").remove();
                    if(obj.rst==1){
                        limpiar();
                        ListarPreTramites();
                        msjG.mensaje("success", obj.msj,3000);
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
        }
     });

    function limpiar(){
        $('#FormTramite').find('input[type="text"],input[type="email"],textarea,select').val('');
        $('#FormTramite').find('span').text('');
        $('#FormTramite').find('img').attr('src','index.img');
        $('#btnImage').removeClass('btn-success'); 
        $('.content-body').addClass('hidden');
    }

    $(document).on('click', '#btnCancelar', function(event) {
        event.preventDefault();
        limpiar();  
    });

    UsuarioId='<?php echo Auth::user()->id; ?>';
    DataUser = '<?php echo Auth::user(); ?>';
    /*Inicializar tramites*/
    var data={'persona':UsuarioId,'estado':1};
    /*end Inicializar tramites*/

    /*inicializate selects*/
    slctGlobal.listarSlct('tipotramite','cbo_tipotramite','simple',null,data);  
    slctGlobal.listarSlct('documento','cbo_tipodoc','simple',null,data);        
    slctGlobal.listarSlct('tiposolicitante','cbo_tiposolicitante','simple',null,data);
    /*end inicializate selects*/

    $(document).on('change', '#cbo_tiposolicitante', function(event) {
        var data={'id':$(this).val(),'estado':1};
        Bandeja.GetTipoSolicitante(data,Mostrar);
    });

    $(document).on('click', '#btnnuevo', function(event) {
        $(".crearPreTramite").removeClass('hidden');
        window.scrollTo(0,document.body.scrollHeight);
    });


             $('#rutaModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var text = $.trim( button.data('text') );
      var id= $.trim( button.data('id') );
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      $("#form_ruta_tiempo #txt_nombre").val(text);
      $("#form_ruta_tiempo").append('<input type="hidden" value="'+id+'" id="txt_area_id_modal">');
      /*alert(id);
      for(i=0; i<areasGId.length; i++){
        alert(areasGId[i]+"=="+id);
        if(areasGId[i]==id){
            alert("encontrado "+areasGId[i]);
        }
      }*/
      var position=tiempoGId.indexOf(id);
      var posicioninicial=areasGId.indexOf(id);
      //alert("tiempo= "+position +" | areapos="+posicioninicial);
      var tid=0;
      var validapos=0;
      var detalle=""; var detalle2="";

      if(position>=0){
        tid=position;
        //alert("actualizando");
        detalle=tiempoG[tid][0].split("_");
        detalle[0]=posicioninicial;
        tiempoG[tid][0]=detalle.join("_");

        detalle2=verboG[tid][0].split("_");
        detalle2[0]=posicioninicial;
        verboG[tid][0]=detalle2.join("_");
      }
      else{
        //alert("registrando");
        tiempoGId.push(id);
        tiempoG.push([]);
        tid=tiempoG.length-1;
        tiempoG[tid].push(posicioninicial+"__");

        verboG.push([]);
        verboG[tid].push(posicioninicial+"______");
      }

      var posicioninicialf=posicioninicial;
        for(var i=1; i<tbodyArea[posicioninicial].length; i++){
            posicioninicialf++;
            validapos=areasGId.indexOf(id,posicioninicialf);
            posicioninicialf=validapos;
            if( i>=tiempoG[tid].length ){
                tiempoG[tid].push(validapos+"__");

                verboG[tid].push(validapos+"______");
            }
            else{
                detalle=tiempoG[tid][i].split("_");
                detalle[0]=validapos;
                tiempoG[tid][i]=detalle.join("_");

                detalle2=verboG[tid][i].split("_");
                detalle2[0]=validapos;
                verboG[tid][i]=detalle2.join("_");
            }
        }

      pintarTiempoG(tid);



      $("#form_ruta_verbo #txt_nombre").val(text);
      $("#form_ruta_verbo").append('<input type="hidden" value="'+id+'" id="txt_area_id_modal">');
    });

    $('#rutaModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      $("#form_ruta_tiempo input[type='hidden']").remove();
      $("#form_ruta_verbo input[type='hidden']").remove();
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });

/*     $('#FormTramite').bootstrapValidator({
                    feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh',
                    },
                    excluded: ':disabled',
                    fields: {
                        txt_tdoc: {
                           validators: {
                                notEmpty: {
                                    message: 'select a date'
                                }
                            }
                        },
                        txt_folio: {
                            validators: {
                                notEmpty: {
                                    message: 'select a date'
                                }
                            }
                        }
                    }
                });*/
                /* end validanting info*/
});

EliminarArchivo = (t)=>{
    $('tr.'+t).remove();
}

ListarPreTramites = ()=>{
    var estados = $("#slct_estado_tramite").val();
    var data={'persona':0,'estado':1, 'estado_tramite':estados};  //, 'filtro_fecha': $("#filtro_fecha").val()
    Bandeja.MostrarPreTramites(data,HTMLPreTramite);
}

HTMLPreTramite = function(data){
    $('#t_reporte').dataTable().fnDestroy();
    var html =''; var validador = 0; var archivo = '';
    $.each(data,function(index, el) {
        color = ''; archivo = '';
            if( el.estado_atencion == 1 ){
                color = 'alert-success';
                validador = 1;
            }
            else if( el.estado_atencion == 2 ){
                color = 'alert-danger';
                validador = 1;
            }
        html+="<tr class='"+color+"'>";
        html+=    "<td>"+el.pretramite +"</td>";
        
        if(el.empresa){
            html+=    "<td>"+el.empresa+"</td>";                
        }else{
            html+=    "<td>"+el.usuario+"</td>";
        }

        if( $.trim(el.ruta_archivo) != '' ){
            archivo = "<a class='btn btn-info btn-lg' href='"+el.ruta_archivo+"' target='_blank'><i class='fa fa-file-pdf-o fa-lg'></i>";
        }
        
        html+=    "<td>"+el.solicitante+"</td>";
        html+=    "<td>"+el.tipotramite+"</td>";
        html+=    "<td>"+el.tipodoc+"</td>";
        html+=    "<td>"+$.trim(el.local)+"</td>";
        html+=    "<td>"+el.tramite+"</td>";
        html+=    "<td>"+el.fecha+"</td>";
        html+=    "<td>"+archivo+"</td>";
        html+=    "<td><ul><li>"+$.trim(el.expediente).split(",").join("</li><li>")+"</li></ul></td>";
        html+=    "<td>"+el.atencion+"</td>";
        html+=    "<td>"+el.updated_at+"</td>";
        html+=    "<td>"+$.trim(el.observacion)+"</td>";
        html+=    "<td>"+$.trim(el.id_union)+"</td>";
        html+=    '<td><span class="btn btn-primary btn-sm" id-pretramite="'+el.pretramite+'" onclick="PreDetallepret('+el.pretramite+','+validador+','+el.area_id+')"><i class="glyphicon glyphicon-th-list"></i></span></td>';

        var url = "documentodig/ticket/"+el.pretramite;

        //html+=    '<td><span class="btn btn-primary btn-sm" id-pretramite="'+el.pretramite+'" onclick="imprimirTicket(\''+url+'\')"><i class="glyphicon glyphicon-search"></i></span></td>';
        html+="</tr>";            
    });
    $("#tb_reporte").html(html);
    $("#t_reporte").dataTable(
        {
            "order": [[ 0, "desc" ]],
            "pageLength": 5,
        }
    ); 
}

PreDetallepret = (id, validador, area_id)=>{
    $('#txt_codpt').val(id);
    $(".observacion").show();
    $(".persona").removeAttr('disabled');
    if( validador == 1 ){
        $(".observacion").hide();
        $(".persona").attr('disabled','true');
    }
    Detallepret();
    area_id_GVP = area_id;
}


Detallepret = function(){
    $('input[type="radio"]').removeAttr('checked');
    $('input[type="radio"]').parent().removeClass('checked');
    $('#txt_observaciones').val('');
    var codpretramite = $('#txt_codpt').val();
    var persona = $('#slct_persona').val();
    var data = {};
    data.idpretramite = true;
    if(codpretramite){
        data.idpretramite = codpretramite;
    }
    if(persona){
        data.persona = persona;
    }
    Bandeja.GetPreTramitebyid(data,poblarDetalle);        
}

poblarDetalle = function(data){
    if(data.length > 0){
        var result = data[0];
        if(!result.tramiteid){
            document.querySelector('#spanTipoT').innerHTML=result.tipotramite;
            document.querySelector('#spanTipoD').innerHTML=result.tipodoc;
            document.querySelector('#txt_tdoc').value=result.nrotipodoc;
            document.querySelector('#spanNumTP').innerHTML=result.nrotipodoc;
            document.querySelector('#spanTLocal').innerHTML=result.local;
            
            document.querySelector('#spanTSoli').innerHTML=result.solicitante;
            document.querySelector('#txt_folio').value=result.folio;
            document.querySelector('#spanFolio').innerHTML=result.folio;

            document.querySelector('#txt_usernomb').value= result.nombusuario;
            document.querySelector('#txt_userapepat').value= result.apepusuario;
            document.querySelector('#txt_userapemat').value= result.apemusuario;
            document.querySelector('#txt_userdni').value= result.dniU;
            document.querySelector('#txt_useremail').value=  $.trim(result.email);
            document.querySelector('#txt_usertelf').value=  $.trim(result.telefono);
            document.querySelector('#txt_usercel').value=  $.trim(result.celular);
            $('#txt_userdirec').val($.trim(result.direccion));


            if(result.empresaid){
                document.querySelector('#spanTE').innerHTML=result.tipoempresa;
                document.querySelector('#spanRazonS').innerHTML=result.empresa;
                document.querySelector('#spanDF').innerHTML=result.edireccion;
                document.querySelector('#spanRUC').innerHTML=result.ruc;
                document.querySelector('#spanRepresentante').innerHTML=result.reprelegal;
                document.querySelector('#spanTelefono').innerHTML=result.etelf;
                document.querySelector('.empresa').classList.remove('hidden');            
            }else{
                document.querySelector('.empresa').classList.add('hidden'); 
            }

            document.querySelector('#spanNombreT').innerHTML=result.tramite;
            document.querySelector('#spanArea').innerHTML=result.area;
            document.querySelector('.content-body').classList.remove('hidden');


            document.querySelector('#txt_pretramiteid').value=result.pretramite;
            document.querySelector('#txt_personaid').value=result.personaid;
            document.querySelector('#txt_ctramite').value=result.ctid;
            document.querySelector('#txt_empresaid').value=result.empresaid;
            document.querySelector('#txt_tsolicitante').value=result.tsid;
            document.querySelector('#txt_tdocumento').value=result.tdocid;
            document.querySelector('#txt_area').value=result.areaid;
            document.querySelector('#txt_local').value=result.local_id;

            masterG.SelectImagen(result.ruta_archivo,"#pdf_img","#pdf_href");

            var data = {area_id: area_id_GVP, ruta_flujo_id: result.ruta_flujo_id, ruta_id: 0, norden: 1}
            Bandeja.mostrarCampos(data,mostrarCamposHTML);
        }else{
            document.querySelector('.content-body').classList.add('hidden');
            alert('Ya fue gestionado!');
        }
    }else{
        document.querySelector('.content-body').classList.add('hidden');
        msjG.mensaje("warning", 'No se encontró el pre tramite',3000);
    }
}

mostrarCamposHTML = (result) => {
    $(".DatosPersonalizadosG").addClass('hidden');
    $(".DatosPersonalizadosG .box-body").html('');

    $.each(result,function(index,r){
        campo = '';
        subtitulo = '';
        color = 'error';
        icono = 'remove';
        html = '';
        lista = $.trim( r.lista ).split("*");
        campo_valor = $.trim(r.campo_valor);
        ruta_campo_id = $.trim(r.ruta_campo_id);
        fecha = '';
        if( ruta_campo_id == '' ){
            ruta_campo_id = 0;
        }

        if( index == 0 ){
            $(".DatosPersonalizadosG").removeClass('hidden');
        }

        if( r.tipo == 0 ){
            campo = r.campo.split("/")[0];
            sub_titulo = r.campo.split("/")[1];
            col = 12;
        }
        
        if( r.tipo == 0 ){
            html = 
                '<div class="col-sm-12 bg-info" style="margin: 10px 0px 10px 0px">'+
                    '<h5 class="text-center"><b>'+ campo +'</b> '+
                        '<small style="color:red">'+ sub_titulo +'</small>'+
                    '</h5>'+
                    '<hr style="border:dotted;">'+
                '</div>';
        }
        else{
            campogenerado = '<input type="text" class="form-control" value="'+ campo_valor +'" disabled>';
            ruta_flujo_campo_id = '';
            ruta_campo = '';

            if( r.modificar == 1 ){
                ruta_flujo_campo_id = '<input type="hidden" name="ruta_flujo_campo_id[]" value="'+ r.id +'">';
                ruta_campo = '<input type="hidden" id="ruta_campo_id'+ r.id +'" name="ruta_campo_id[]" value="'+ ruta_campo_id +'">';
                if( r.obligar == 0 ){
                    color = 'warning';
                    icono = 'warning-sign';
                }

                if( r.tipo != 6 ){
                    
                    onKey = ''; readOnly = ''; fecha  = ''; formatoFecha = ''; minView= 3; maxView= 4; startView= 3;

                    if( campo_valor != '' ){
                        color = 'success';
                        icono = 'ok';
                    }
                    
                    if( r.tipo == 1 ){
                        onKey = ' onKeyUp="masterG.validaEmailEvento(this, '+ r.capacidad +', cambiarColor)" ';
                    }
                    else if( r.tipo == 2 ){
                        onKey = ' onKeyPress="return masterG.validaDecimal(event, this)" ';
                        onKey += ' onKeyUp="masterG.validaDecimalMaxEvento(this, '+ r.capacidad +', cambiarColor)" ';
                    }
                    else if( r.tipo >= 3 && r.tipo <= 5 ){
                        fecha = 'fecha'; 
                        readOnly = 'readonly';
                        if( r.tipo == 3 ){
                            formatoFecha = 'yyyy-mm-dd';
                            minView= 2;
                            maxView= 4;
                            startView= 2;
                        }
                        else if( r.tipo == 4 ){
                            formatoFecha = 'yyyy-mm';
                            minView= 3;
                            maxView= 4;
                            startView= 3;
                        }
                        else if( r.tipo == 5 ){
                            formatoFecha = 'yyyy';
                            minView= 4;
                            maxView= 4;
                            startView= 4;
                        }
                        onKey = ' onChange="masterG.validaDatosEvento(this, cambiarColor)" ';
                    }
                    else if( r.tipo == 7 ){
                        onKey = ' onKeyPress="return masterG.validaNumerosMax(event, this, '+ r.capacidad +')" ';
                        onKey += ' onKeyUp="masterG.validaDatosEvento(this, cambiarColor)" ';
                    }
                    else if( r.tipo == 8 ){
                        //onKey = ' onKeyPress="return masterG.validaLetras(event, this, '+ r.capacidad +')" ';
                        onKey = ' onKeyUp="masterG.validaDatosEvento(this, cambiarColor)" ';
                    }
                    campogenerado = 
                            '<div id="campo'+ r.id +'" class="has-'+ color +' has-feedback">'+
                                '<input type="text" class="form-control '+ fecha +'" name="campo_valor[]" value="'+ campo_valor +'"'+ onKey + readOnly +
                                    ' data-id="'+ r.id +'"'+
                                    ' data-capacidad="'+ r.capacidad +'"' +
                                    ' data-obligar="'+ r.obligar +'"' +
                                    ' data-campo="'+ r.campo +'"' +
                                '>'+
                                '<span data-id="'+ r.id +'" class="glyphicon glyphicon-'+ icono +' form-control-feedback"></span>'+
                            '</div>';
                }
                else{
                    options = '';
                    for (let i = 0; i < lista.length; i++) {
                        selected = '';
                        if( campo_valor != '' && campo_valor == lista[i] ){
                            selected = 'selected';
                            color = 'success';
                            icono = 'ok';
                        }
                        options+='<option value="'+ lista[i] +'" '+ selected +'>'+ lista[i] +'</option>';
                    }

                    onKey = ' onChange="masterG.validaDatosEvento(this, cambiarColor)" ';

                    campogenerado = 
                        '<div id="campo'+ r.id +'" class="form-group has-'+ color +' has-feedback">'+
                            '<select class="form-control" name="campo_valor[]" '+ onKey +
                            ' data-id="'+ r.id +'"'+
                            ' data-capacidad="'+ r.capacidad +'"' +
                            ' data-obligar="'+ r.obligar +'"' +
                            ' data-campo="'+ r.campo +'"' +
                        '>'+
                                '<option value=""> .::Seleccione::. </option>'+
                                options +
                            '</select>'+
                            '<span data-id="'+ r.id +'" class="glyphicon glyphicon-'+ icono +' form-control-feedback"></span>'+
                        '</div>';
                }
            }

            html =
                '<div class="col-sm-'+ r.col +'">'+
                    ruta_flujo_campo_id +
                    ruta_campo +
                    '<label>'+ r.campo +':</label>'+
                    campogenerado
                '</div>';
        }
        
        $(".DatosPersonalizadosG .box-body").append(html);

        if( fecha != '' ){
            $("#campo"+ r.id +" .fecha").datetimepicker({
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
        }
        
    });
}

cambiarColor = (t, estado)=>{
    id = t.dataset.id;
    obligar = t.dataset.obligar;
    $("#campo"+id).removeClass('has-error').removeClass('has-success').removeClass('has-warning');
    $("#campo"+id+" span").removeClass('glyphicon-remove').removeClass('glyphicon-ok').removeClass('glyphicon-warning-sign');
    if ( estado ) {
        $("#campo"+id).addClass('has-success');
        $("#campo"+id+" span").addClass('glyphicon-ok');
    }
    else{
        if( t.value == '' && obligar == 0 ){
            $("#campo"+id).addClass('has-warning');
            $("#campo"+id+" span").addClass('glyphicon-warning-sign');
        }
        else{
            $("#campo"+id).addClass('has-error');
            $("#campo"+id+" span").addClass('glyphicon-remove');
        }
    }
}

getCTramites  = function(){
    data = {};
    Bandeja.getClasificadoresTramite(data,HTMLClasificadores);
}

HTMLClasificadores = function(data){
    $('#t_clasificador').dataTable().fnDestroy();
    var html = '';
    if(data){
        $.each(data,function(index, el) {
            html+='<tr>';
            html+='<td>'+el.id+'</td>';
            html+='<td>'+el.nombre_clasificador_tramite+'</td>';
            html+='<td><span class="btn btn-info btn-sm" id="'+el.id+'" nombre="'+el.nombre_clasificador_tramite+'" onClick="getRequisitos(this)">Ver</span></td>';
            html+='<td><span class="btn btn-info btn-sm" id="'+el.id+'" nombre="'+el.nombre_clasificador_tramite+'" onclick="cargarRutaId('+el.ruta_flujo_id+',2)">Ver Ruta</span></td>';
            html+='<td><span class="btn btn-primary btn-sm" id="'+el.id+'" nombre="'+el.nombre_clasificador_tramite+'" onclick="selectClaTramite(this)">Seleccionar</span></td>';
            html+='</tr>';        
        });
    }
        $("#tb_clasificador").html(html);
        $("#t_clasificador").dataTable(); 
        $("#buscartramite").modal('show');
}

getRequisitos = function(obj){
    data = {'idclatramite':obj.getAttribute('id'),'estado':1};
    Bandeja.getRequisitosbyclatramite(data,HTMLRequisitos,obj.getAttribute('nombre'));
}

HTMLRequisitos = function(data,tramite){
    $("#tb_requisitos").html('');
    if(data){
        var html ='';
        $.each(data,function(index, el) {
            html+='<tr><ul>';
            html+='<td style="text-align: left;"><li>'+el.nombre+'</li></td>';
            html+='<td>'+el.cantidad+'</td>';
            html+='<ul></tr>';
        });
        $("#tb_requisitos").html(html);
        $("#nombtramite").text(tramite);
        $("#requisitos").modal('show');
    }
}

selectClaTramite = function(obj){
    data ={'id':obj.getAttribute('id'),'nombre':obj.getAttribute('nombre')};
/*    Bandeja.GetAreasbyCTramite({'idc':obj.getAttribute('id')},data);*/
    poblateData('tramite',data);
    $('#buscartramite').modal('hide');
}

selectCA = function(obj){
    var areaid= obj.value;
    var area_nomb = document.querySelectorAll("#slcAreasct option[value='"+areaid+"']");
    var cla_id = document.querySelector('#txt_clasificador_id').value;
    var cla_nomb = document.querySelector('#txt_clasificador_nomb').value;
    var data ={'id':cla_id,'nombre':cla_nomb,'area':area_nomb[0].textContent,'areaid':areaid};
    poblateData('tramite',data);
    $('#buscartramite').modal('hide');
}

poblateData = function(tipo,data){
    if(tipo=='tramite'){
        document.querySelector('#spanNombreT').innerHTML=data.nombre;
/*        document.querySelector('#spanArea').innerHTML=data.area;*/
        document.querySelector('#txt_ctramite').value=data.id;
/*        document.querySelector('#txt_area').value=data.areaid;*/
    }
}

consultar = function(){
    var busqueda = document.querySelector("#txtbuscarclasificador");

    var data = {};
    data.estado = 1;
    if(busqueda){
       data.buscar = busqueda.value;
    }
    Bandeja.getClasificadoresTramite(data,HTMLClasificadores);
}

generarTramite = function(){
    datos=$("#FormTramite").serialize().split("txt_").join("").split("slct_").join("").split("%5B%5D").join("[]").split("+").join(" ").split("%7C").join("|").split("&");
    data = '{';
    for (var i = 0; i < datos.length ; i++) {
        var elemento = datos[i].split('=');
        data+=(i == 0) ? '"'+elemento[0]+'":"'+elemento[1] : '","' + elemento[0]+'":"'+elemento[1];   
    }
    data+='"}';
/*    img  = document.querySelector('#txt_file').files[0];*/
    var form = new FormData($("#FormTramite")[0]);
    //console.log(form);
    Bandeja.GuardarTramite(data);
}

cargarRutaId=function(ruta_flujo_id,permiso,ruta_id){
    $("#rutaflujoModal #txt_ruta_flujo_id_modal").remove();
    $("#rutaflujoModal #form_ruta_flujo").append('<input type="hidden" id="txt_ruta_flujo_id_modal" value="'+ruta_flujo_id+'">');
    $("#rutaflujoModal #txt_titulo").text("Vista");
    $("#rutaflujoModal #texto_fecha_creacion").text("Fecha Vista:");
    $("#rutaflujoModal #fecha_creacion").html('<?php echo date("Y-m-d"); ?>');
    $("#rutaflujoModal #form_ruta_flujo .form-group").css("display","");
    Ruta.CargarDetalleRuta(ruta_flujo_id,permiso,CargarDetalleRutaHTML,ruta_id);
    $("#rutaflujoModal").modal('show');
}
CargarDetalleRutaHTML=function(permiso,datos){
areasG="";  areasG=[]; // texto area
areasGId="";  areasGId=[]; // id area
estadoG="";  estadoG=[]; // Normal / Paralelo
theadArea="";  theadArea=[]; // cabecera area
tbodyArea="";  tbodyArea=[]; // cuerpo area
tfootArea="";  tfootArea=[]; // pie area

tiempoGId="";  tiempoGId=[]; // id posicion del modal en base a una area.
tiempoG="";  tiempoG=[];
verboG="";  verboG=[];
posicionDetalleVerboG=0;
validandoconteo=0;
    $.each(datos,function(index,data){
        validandoconteo++;
        if(validandoconteo==1){
            $("#txt_persona").val(data.persona);
            $("#txt_proceso_1").val(data.flujo);
            $("#txt_area").val(data.area);
        }
        adicionarRutaDetalleAutomatico(data.area2,data.area_id2,data.tiempo_id+"_"+data.dtiempo,data.verbo,data.imagen,data.imagenc,data.imagenp,data.estado_ruta);
    });
    pintarAreasG(permiso);
    //alertatodo();
}

AbreTv=function(val){
    $("#areasasignacion [data-id='"+val+"']").click();
}

adicionarRutaDetalleAutomatico=function(valorText,valor,tiempo,verbo,imagen,imagenc,imagenp,estruta){
    valor=""+valor;
    var adjunta=false; var position=areasGId.indexOf(valor);
    if( position>=0 ){
        adjunta=true;
    }

    var verboaux=verbo.split("|");
    var verbo1=[];
    var verbo2=[];
    var verbo3=[];
    var verbo4=[];
    var verbo5=[];
    var verbo6=[];
    var imgfinal=imagen;
    for(i=0;i<verboaux.length;i++ ){
        verbo1.push(verboaux[i].split("^^")[0]);
        verbo2.push(verboaux[i].split("^^")[1]);
        verbo3.push(verboaux[i].split("^^")[2]);
        verbo4.push(verboaux[i].split("^^")[3]);
        verbo5.push(verboaux[i].split("^^")[4]);
        verbo6.push(verboaux[i].split("^^")[5]);

        if($.trim(verboaux[i].split("^^")[1])>0){
            imgfinal=imagenc;
        }
    }

    if(estruta>1){
        imgfinal=imagenp;
    }

    estadoG.push(estruta);
    areasG.push(valorText);
    areasGId.push(valor);

    if( adjunta==false ){
        head='<th class="eliminadetalleg" style="width:110px;min-width:100px !important;">'+valorText+'</th>';
        theadArea.push(head);

        body=   '<tr>'+
                    '<td class="areafinal" onclick="AbreTv('+valor+');" style="height:100px; background-image: url('+"'"+'img/admin/area/'+imgfinal+"'"+');">&nbsp;'+
                    '<span class="badge bg-yellow">'+areasG.length+'</span>'+
                    '</td>'+
                '</tr>';
        tbodyArea.push([]);
        tbodyArea[ (tbodyArea.length-1) ].push(body);

        foot=   '<th class="eliminadetalleg">'+
                    '<div style="text-align:center;">'+
                    '<a class="btn bg-olive btn-sm" data-toggle="modal" data-target="#rutaModal" data-id="'+valor+'" data-text="'+valorText+'">'+
                        '<i class="fa fa-desktop fa-lg"></i>'+
                    '</a>'+
                    '</div>'+
                '</th>';
        tfootArea.push(foot);
    }
    else{

        theadArea.push(0);
        tfootArea.push(0);
        tbodyArea.push([]);
        tbodyArea[ (tbodyArea.length-1) ].push(position+"|"+tbodyArea[position].length );
        body=   '<tr>'+
                    '<td class="areafinal" onclick="AbreTv('+valor+');" style="height:100px; background-image: url('+"'"+'img/admin/area/'+imgfinal+"'"+');">&nbsp;'+
                    '<span class="badge bg-yellow">'+areasG.length+'</span>'+
                    '</td>'+
                '</tr>';
        tbodyArea[position].push(body);

    }

      var position=tiempoGId.indexOf(valor);
      var posicioninicial=areasGId.indexOf(valor);
      //alert("tiempo= "+position +" | areapos="+posicioninicial);
      var tid=0;
      var validapos=0;
      var detalle=""; var detalle2="";
      
      if(position>=0){
        tid=position;
        //alert("actualizando");
        /*detalle=tiempoG[tid][0].split("_");
        detalle[0]=posicioninicial;
        tiempoG[tid][0]=detalle.join("_");

        detalle2=verboG[tid][0].split("_");
        detalle2[0]=posicioninicial;
        verboG[tid][0]=detalle2.join("_");
        */
      }
      //else{
        //alert("registrando");

    if( tiempo!='_' || verbo!='' ){
        if( adjunta==false ){ // primer registro
            tiempoGId.push(valor);
            tiempoG.push([]);
            tid=tiempoG.length-1;
            tiempoG[tid].push(posicioninicial+"_"+tiempo);

            verboG.push([]);
            verboG[tid].push(posicioninicial+"_"+verbo1.join("|")+"_"+verbo2.join("|")+"_"+verbo3.join("|")+"_"+verbo4.join("|")+"_"+verbo5.join("|")+"_"+verbo6.join("|"));
        }
      //}
        else{
            var posicioninicialf=posicioninicial;
            for(var i=1; i<tbodyArea[posicioninicial].length; i++){
                posicioninicialf++;
                validapos=areasGId.indexOf(valor,posicioninicialf);
                posicioninicialf=validapos;
                if( i>=tiempoG[tid].length ){
                    //alert(tiempo+" | "+verbo+" | "+valor+" | "+posicioninicial+"-"+validapos);
                    tiempoG[tid].push(validapos+"_"+tiempo);

                    verboG[tid].push(validapos+"_"+verbo1.join("|")+"_"+verbo2.join("|")+"_"+verbo3.join("|")+"_"+verbo4.join("|")+"_"+verbo5.join("|")+"_"+verbo6.join("|"));
                }
                /*else{
                    detalle=tiempoG[tid][i].split("_");
                    detalle[0]=validapos;
                    tiempoG[tid][i]=detalle.join("_");

                    detalle2=verboG[tid][i].split("_");
                    detalle2[0]=validapos;
                    verboG[tid][i]=detalle2.join("_");
                }*/
            }
        }
    }
}

pintarAreasG=function(permiso){
    var htm=''; var click=""; var imagen=""; var clickeli="";
    $("#areasasignacion .eliminadetalleg").remove();
    $("#slct_area_id_2").val("");$("#slct_area_id_2").multiselect("refresh");
    $("#slct_area_id_2").multiselect("disable");

    if(permiso!=2){
        $("#slct_area_id_2").multiselect("enable");
    }

    for ( var i=0; i<areasG.length; i++ ) {
        click="";
        imagen="";
        clickeli="";
        if(permiso!=2){
            clickeli=" onclick='EliminarDetalle("+i+");' ";
        }

        if ( i>0 ) {
            if(permiso!=2){
                click=" onclick='CambiarDetalle("+i+");' ";
            }
            imagen="<i class='fa fa-sort-up fa-sm'></i>";
        }

        htm+=   "<tr id='tr-detalle-"+i+"'>"+
                    "<td>"+
                        (i+1)+
                    "</td>"+
                    "<td>"+
                        areasG[i]+
                    "</td>"+
                "</tr>";


        if(theadArea[i]!=0){

            $("#areasasignacion>thead>tr.head").append(theadArea[i]);
            $("#areasasignacion>tfoot>tr.head").append(tfootArea[i]);

            var detbody='<td class="eliminadetalleg">'+
                            '<table class="table table-bordered">';
            for(j=0; j<tbodyArea[i].length ; j++){
                if(j>0){
                    detbody+=   '<tr>'+
                                    '<td style="height:8px;">&nbsp;'+
                                    '</td>'+
                                '</tr>';
                }
                detbody+=tbodyArea[i][j];
            }
            detbody+='</table> </td>';
            $("#areasasignacion>tbody>tr.body").append(detbody);
        }
        
    };

    $("#areasasignacion>thead>tr.head").append('<th class="eliminadetalleg" style="min-width:1000px important!;">[]</th>'); // aqui para darle el area global

    $("#tb_rutaflujodetalleAreas").html(htm);
}
////////////////////// Agregando para el mostrar detalle
pintarTiempoG=function(tid){
    var htm="";var detalle="";var detalle2="";
    $("#tb_tiempo").html(htm);
    $("#tb_verbo").html(htm);

    posicionDetalleVerboG=0; // Inicializando posicion del detalle al pintar

    var subdetalle1="";var subdetalle2="";var subdetalle3="";var subdetalle4="";var subdetalle5="";var subdetalle6="";var imagen="";

    for(var i=0;i<tiempoG[tid].length;i++){
        // tiempo //
        detalle=tiempoG[tid][i].split("_");

        htm=   '<tr>'+
                    '<td>'+(detalle[0]*1+1)+'</td>'+
                    '<td>'+
                        '<select disabled class="form-control" id="slct_tipo_tiempo_'+detalle[0]+'_modal">'+
                            $('#slct_tipo_tiempo_modal').html()+
                        '</select>'+
                    '</td>'+
                    '<td>'+
                        '<input readonly class="form-control" type="number" id="txt_tiempo_'+detalle[0]+'_modal" value="'+detalle[2]+'">'+
                    '</td>'+
                '</tr>';
        $("#tb_tiempo").append(htm);

        $('#slct_tipo_tiempo_'+detalle[0]+'_modal').val(detalle[1]);
        //fin tiempo

        //verbo
        
        detalle2=verboG[tid][i].split("_");

        subdetalle1=detalle2[1].split('|');
        subdetalle2=detalle2[2].split('|');
        subdetalle3=detalle2[3].split('|');
        subdetalle4=detalle2[4].split('|');
        subdetalle5=detalle2[5].split('|');
        subdetalle6=detalle2[6].split('|');

        selectestado='';
        for(var j=0; j<subdetalle1.length; j++){
            posicionDetalleVerboG++;
            imagen="";
            
            
            if( (j+1)==subdetalle1.length ){
                selectestado='<br><select disabled id="slct_paralelo_'+detalle2[0]+'_modal">'+
                             '<option value="1">Normal</option>'+
                             '<option value="2">Paralelo</option>'+
                             '</select>';
            }

            htm=   '<tr id="tr_detalle_verbo_'+posicionDetalleVerboG+'">'+
                        '<td>'+(detalle2[0]*1+1)+selectestado+'</td>'+
                        '<td>'+
                            '<input readonly type="number" class="form-control txt_orden_'+detalle2[0]+'_modal" placeholder="Ing. Orden" value="'+subdetalle6[j]+'">'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_rol_'+detalle2[0]+'_modal">'+
                                $('#slct_rol_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_verbo_'+detalle2[0]+'_modal">'+
                                $('#slct_verbo_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_documento_'+detalle2[0]+'_modal">'+
                                $('#slct_documento_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+
                            '<textarea disabled class="form-control txt_verbo_'+detalle2[0]+'_modal" placeholder="Ing. Acción">'+subdetalle1[j]+'</textarea>'+
                        '</td>'+
                        '<td>'+
                            '<select disabled class="form-control slct_condicion_'+detalle2[0]+'_modal">'+
                                $('#slct_condicion_modal').html()+
                            '</select>'+
                        '</td>'+
                        '<td>'+imagen+'</td>'+
                    '</tr>';
            $("#tb_verbo").append(htm);

            if( (j+1)==subdetalle1.length ){
                $("#slct_paralelo_"+detalle2[0]+"_modal").val(estadoG[detalle2[0]]);
            }

            if(subdetalle2[j]==""){ // En caso no tenga valores se inicializa
                subdetalle2[j]="0";
            }
            //alert(subdetalle2[j]);
            $(".slct_condicion_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle2[j]);
            $(".slct_rol_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle3[j]);
            $(".slct_verbo_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle4[j]);
            $(".slct_documento_"+detalle2[0]+"_modal:eq("+j+")").val(subdetalle5[j]);
        }
        //fin verbo
    }
}

</script>
