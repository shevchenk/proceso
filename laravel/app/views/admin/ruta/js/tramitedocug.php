<script type="text/javascript">
var cabeceraG=[]; // Cabecera del Datatable
var columnDefsG=[]; // Columnas de la BD del datatable
var targetsG=-1; // Posiciones de las columnas del datatable
var CantidadG = 0;
$(document).ready(function() {

     $('#fecha_nacimiento').daterangepicker({
                format: 'YYYY-MM-DD',
                singleDatePicker: true,
                showDropdowns: true
    });

    $("#t_usuarios").dataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ordering": true,
        "searching": false,
    });


    UsuarioId='<?php echo Auth::user()->id; ?>';
    DataUser = '<?php echo Auth::user(); ?>';
    UsuarioLocalId='<?php echo trim(Auth::user()->local_id); ?>';
    poblateData('x',DataUser);
    /*Inicializar tramites*/
    var data={'persona':UsuarioId,'estado':1};
    Bandeja.MostrarTramites(data,HTMLTramite);
    /*end Inicializar tramites*/
    slctGlobal.listarSlctFuncion('local','listarlocales','slct_local','simple',UsuarioLocalId,{estado:1, usuario_local:1});
    /*inicializate selects*/
    slctGlobalHtml('cbo_tipodocumento, #slct_tipo_usuario, #cbo_tipotramite, #cbo_tipodoc','simple');
    slctGlobal.listarSlct('persona','cbo_persona','simple',null,{estado_persona:1});
    slctGlobal.listarSlct('empresa','cbo_empresa','simple',null,{estado:1});
    slctGlobal.listarSlct('area','slct_areas','simple',null,{estado:1, areapersona:1, areagestion:1});
    slctGlobal.listarSlctFuncion('tiposolicitante','listar?pretramite=1','cbo_tiposolicitante','simple',null,{'estado':1,'validado':1});
    
    /*end inicializate selects*/
    
    data = {estado:1};
    var ids = [];
    slctGlobal.listarSlct('software','slct_software_id_modal','simple',ids,data);
    slctGlobal.listarSlct2('rol','slct_rol_modal',data);
    slctGlobal.listarSlct2('verbo','slct_verbo_modal',data);
    
    slctGlobal.listarSlct2('documento','slct_documento_modal',data);
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


    $(document).on('click', '#btnTipoSolicitante', function(event) {
        var tiposolicitante = $("#cbo_tiposolicitante").val();
        var pide_empresa = $("#cbo_tiposolicitante option:selected").attr('data-select');
        if( $.trim(pide_empresa) == '|0|'){
            Bandeja.GetPersons({'apellido_nombre':1},HTMLPersonas);
        }else if( $.trim(pide_empresa) == '|1|'){
            Bandeja.getEmpresasByPersona({'estado':1},ValidacionEmpresa);
        }
        else {
            solicitante = 'Indefinido';
            alert("Seleccionar Tipo de Solicitante");
        }
    });

    $(document).on('click', '#btnAgregarP', function(event) {
        $("#selectPersona").modal('hide');
        $("#CrearUsuario").modal('show');
        /* Act on the event */
    });

    $(document).on('click', '#btnAgregarEmpresa', function(event) {
        $("#empresasbyuser").modal('hide');
        $("#crearEmpresa").modal('show');
        /* Act on the event */
    });
    
    $(document).on('click', '#btnSeleccionarPersona', function(event) {
//        $("#crearEmpresa").modal('hide');
        
        $("#selectPersona").modal('show');
        Bandeja.GetPersons({'apellido_nombre':1},HTMLPersonas);
        /* Act on the event */
    });

    $(document).on('click', '#btnnuevo', function(event) {
        $(".crearPreTramite").removeClass('hidden');
        
        window.scrollTo(0,document.body.scrollHeight);
    });
    
    $('#buscartramite').on('hide.bs.modal', function (event) {
//      var modal = $(this); //captura el modal
//      $("#form_ruta_tiempo input[type='hidden']").remove();
//      $("#form_ruta_verbo input[type='hidden']").remove();
      $("#buscartramite #reporte").show();
    });

    $("#btnReferido").click( ()=>{
        $("#referenteModal").modal('show');
    })
     /*validaciones*/

    $(document).on('click', '.btnEnviar', function(event) {
        generarUsuario();
    });

    $("div.solicitantes").hide();
    
    $("#cbo_tiposolicitante").change(function(){
        $('#txt_ruc').val(''); 
        $('#txt_userdni2').val('');
        //$("div.solicitantes").show();
        
        var solicitante = $("#cbo_tiposolicitante option:selected").attr('data-val');
        if( $("#cbo_tiposolicitante").val() == '' ){
            solicitante = 'Indefinido';
        }
        else if( $("#cbo_tiposolicitante").val() == '0' ){
            solicitante = 'Interno';
            $("div.solicitantes").hide();
        }
        $("#cbo_tipotramite, #cbo_tipodoc").multiselect('destroy');
        data = {estado:1, tipo:'Salida', solicitante: solicitante};
        slctGlobal.listarSlct('tipotramite','cbo_tipotramite','simple',null,data);
        data = {estado:1, tipo:'Ingreso', solicitante: solicitante};
        slctGlobal.listarSlct('documento','cbo_tipodoc','simple',null,data);
	});

    $('#referenteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
      
        var modal = $(this); //captura el modal
        var idG={   referido        :'onBlur|Referido|#DCE6F1', //#DCE6F1
                        fecha_hora_referido   :'onChange|Fecha Referido|#DCE6F1|fechaG', //#DCE6F1
                        id        :'1|[]|#DCE6F1', //#DCE6F1
        };

        var resG=dataTableG.CargarCab(idG);
        cabeceraG=resG; // registra la cabecera
        var resG=dataTableG.CargarCol(cabeceraG,columnDefsG,targetsG,0,'referente','t_referente');
        columnDefsG=resG[0]; // registra las columnas del datatable
        targetsG=resG[1]; // registra los contadores

        $("#t_referidos").dataTable().fnDestroy();
    });

    $('#referenteModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        $("#t_referente>thead>tr:eq(0),#t_referente>tfoot>tr:eq(0)").html('');
        cabeceraG=[]; // Cabecera del Datatable
        columnDefsG=[]; // Columnas de la BD del datatable
        targetsG=-1; // Posiciones de las columnas del datatable
        $("#t_referidos").dataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": true,
            "searching": false,
        });
    });

    $('#selectPersona').on('show.bs.modal', function (event) {
        $("#t_usuarios").dataTable().fnDestroy();
    });

    $('#selectPersona').on('hide.bs.modal', function (event) {
        $("#t_usuarios").dataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": true,
            "searching": false,
        });
    });

    $('#empresasbyuser').on('show.bs.modal', function (event) {
        $("#t_usuarios").dataTable().fnDestroy();
    });

    $('#empresasbyuser').on('hide.bs.modal', function (event) {
        $("#t_usuarios").dataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "ordering": true,
            "searching": false,
        });
    });
    


});


ValidarLimite = function(){
    let cant = $("#cbo_tipotramite option:selected").data('evento');
    if( typeof(cant) != 'undefined' && $("#cbo_tiposolicitante").val() != '0' ){
        CantidadG = cant.split("|").join("");
        console.log(CantidadG);
        $("div.solicitantes").show();
    }
    else{
        $("div.solicitantes").hide();
    }

    $("#t_usuarios").dataTable().fnDestroy(); //Reinicia solicitantes cada vez q cambia de tipo
    $("#tb_usuarios").html('');
    $("#t_usuarios").dataTable({
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "ordering": true,
        "searching": false,
    });

    $("#t_referidos").dataTable().fnDestroy(); //Reinicia solicitantes cada vez q cambia de tipo
    $("#tb_referidos").html('');
    $("#t_referidos").dataTable({
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "ordering": true,
        "searching": false,
    });
}

eventoSlctGlobalSimple=function(slct,valores){
    /*if( slct=="slct_areas" ){
        
    }*/
}


MostrarAjax=function(t){
    if( t=="referente" ){
        if( columnDefsG.length>0 ){
            dataTableG.CargarDatos(t,'referido','cargar',columnDefsG);
        }
        else{
            alert('Faltas datos');
        }
    } 
};

GeneraFn=function(row,fn){ // No olvidar q es obligatorio cuando queire funcion fn
   if(typeof(fn)!='undefined' && fn.col==2){
      var estadohtml='';
      estadohtml='<span id="'+row.id+'" onClick="SeleccionaReferido(\''+row.id+'\',\''+row.ruta_id+'\',\''+row.tabla_relacion_id+'\',\''+row.ruta_detalle_id+'\',\''+row.referido+'\')" class="btn btn-success">Seleccionar</span>';
      return estadohtml;
  }
};

SeleccionaReferido = (id, ruta_id, tabla_relacion_id, ruta_detalle_id, referido) => {
    validarRegistro = false;
    if( $.trim($("#r"+id).html()) != '' || $.trim($("#r"+id).html()) != '' ){
        validarRegistro = true;
    }
    
    if( validarRegistro == false ){
        html=   '<tr id="r'+id+'">'+
                '<td>'+referido+
                    '<input type="hidden" value="'+tabla_relacion_id+'" name="tabla_relacion_id_ref[]">'+
                    '<input type="hidden" value="'+ruta_id+'" name="ruta_id_ref[]">'+
                    '<input type="hidden" value="'+ruta_detalle_id+'" name="ruta_detalle_id_ref[]">'+
                '</td>'+
                '<td><span class="btn btn-danger btn-sm" onClick="EliminarTr(\'r'+id+'\',\'referidos\')"><i class="fa fa-trash"></i></span></td>'
            '</tr>';
        $("#tb_referidos").append(html);
    }
    else{
        msjG.mensaje("warning", 'Referido ya fue seleccionado!',3000);
    }
}
/*
CargarPreTramites = function(){
    var data={'persona':UsuarioId,'estado':1};
    Bandeja.MostrarPreTramites(data,HTMLPreTramite);
}
*/

ValidarDoc = ()=> {
    valor = $("#cbo_tipodoc option:selected").attr('data-val');
    $(".tipo_documento").hide();
    if( valor == 1 ){
        $(".tipo_documento").show();
    }
    $("#txt_tipodoc").val('');
}

HTMLTramite = function(data){
    if(data){
        var html ='';
        $.each(data,function(index, el) {
            html+="<tr>";
            html+=    "<td>"+el.idtramite +"</td>";
            html+=    "<td>"+el.usuario+"</td>";
            
            if(el.empresa){
                html+=    "<td>"+el.empresa+"</td>";
            }else{
                html+=    "<td>&nbsp;</td>";
            }
            
            html+=    "<td>"+el.solicitante+"</td>";
            html+=    "<td>"+el.tipotramite+"</td>";
            html+=    "<td>"+el.tipodoc+"</td>";
            html+=    "<td>"+el.tramite+"</td>";
            html+=    "<td>"+el.fecha+"</td>";
            var url = "documentodig/ticket/"+el.idtramite;
            html+=    '<td><span class="btn btn-primary btn-sm" id-tramite="'+el.tramite+'" onclick="imprimirTicket(\''+url+'\')"><i class="glyphicon glyphicon-search"></i></span></td>';
            html+="</tr>";
        });
        $("#tb_reporte").html(html);
    }else{
        msjG.mensaje("warning", 'No hay registros',3000);
    }
}

function imprimirTicket(url){
    parametrosPop="height=600,width=350,toolbar=No,location = No,scrollbars=yes,left=-15,top=800,status=No,resizable= No,fullscreen =No'";
    printTicket=window.open(url,'tTicket',parametrosPop);
    printTicket.focus();  
}

/*
Detallepret = function(obj){
    var id_pretramite = obj.getAttribute('id-pretramite');
    var data = {'idpretramite':id_pretramite};
    Bandeja.GetPreTramitebyid(data,poblarDetalle);

}

poblarDetalle = function(data){
    var result = data[0];
    document.querySelector('#spanTipoTramite').innerHTML = result.tipotramite;
    document.querySelector('#spanTipoDoc').innerHTML = result.tipodoc;
    document.querySelector('#spanNombreTramite').innerHTML = result.tramite;
    document.querySelector('#spanNumFolio').innerHTML = result.folio;
    document.querySelector('#spanNumTipoDoc').innerHTML = result.nrotipodoc;
    document.querySelector('#spanTipoSolicitante').innerHTML = result.solicitante;
    document.querySelector('#spanArea').innerHTML = result.area;

    if(result.empresa){
        document.querySelector('#spanRuc').innerHTML = result.ruc;
        document.querySelector('#spanTipoEmpresa').innerHTML = result.tipoempresa;
        document.querySelector('#spanRazonSocial').innerHTML = result.empresa;
        document.querySelector('#spanNombComer').innerHTML = result.nomcomercial;
        document.querySelector('#spanDomiFiscal').innerHTML = result.edireccion;
        document.querySelector('#spanTelefonoE').innerHTML = result.etelf;
        document.querySelector('#spanFechavE').innerHTML = result.efvigencia;
        $('.empresadetalle').removeClass('hidden');        
    }else{
        $('.empresadetalle').addClass('hidden');
    }

    document.querySelector('#spanDniU').innerHTML = result.dniU;
    document.querySelector('#spanNombreU').innerHTML = result.nombusuario;
    document.querySelector('#spanNombreApeP').innerHTML = result.apepusuario;
    document.querySelector('#spanNombreApeM').innerHTML = result.apemusuario;
    document.querySelector('#spanTelefonoU').innerHTML = '';
    document.querySelector('#spanDirecU').innerHTML = '';
    $('#detallepretramite').modal('show');
}

Voucherpret = function(obj){
    var id_tramite = obj.getAttribute('id-tramite');
    var data = {'idtramite':id_tramite};
    Bandeja.GetPreTramitebyid(data,poblarVoucher);
}

poblarVoucher = function(data){
    var result = data[0];
    document.querySelector('#spanvfecha').innerHTML=result.fregistro;
    document.querySelector('#spanvcodpretramite').innerHTML=result.pretramite;
    document.querySelector('#spantArea').innerHTML=result.area;
    document.querySelector('#spanImprimir').setAttribute('idtramite',result.pretramite);

   if(result.empresa){
        document.querySelector('#spanveruc').innerHTML=result.ruc;
        document.querySelector('#spanvetipo').innerHTML=result.tipoempresa;
        document.querySelector('#spanverazonsocial').innerHTML=result.empresa;
        document.querySelector('#spanvenombreco').innerHTML=result.nomcomercial;
        document.querySelector('#spanvedirecfiscal').innerHTML=result.edireccion;
        document.querySelector('#spanvetelf').innerHTML=result.etelf;
        document.querySelector('#spanverepre').innerHTML=result.reprelegal;
        $('.vempresa').removeClass('hidden');
    }else{
        $('.vempresa').addClass('hidden');
    }

    document.querySelector('#spanvudni').innerHTML=result.dniU;
    document.querySelector('#spanvunomb').innerHTML=result.nombusuario;
    document.querySelector('#spanvuapep').innerHTML=result.apepusuario;
    document.querySelector('#spanvuapem').innerHTML=result.apemusuario;
    document.querySelector('#spanvnombtramite').innerHTML=result.tramite;
    
    $('#voucher').modal('show');
}

exportPDF = function(obj){
    var idtramite = obj.getAttribute('idtramite');
    if(idpretramite){
        obj.setAttribute('href','pretramite/voucherpretramite'+'?idpretramite='+idtramite);
    }else{
        event.preventDefault();
    }
}*/

Mostrar = function(data){
    if(data[0].pide_empresa == 1){
        $(".usuario").removeClass('hidden');
        $(".empresa").removeClass('hidden');
        Bandeja.getEmpresasByPersona({'persona':UsuarioId},ValidacionEmpresa);
    }else{
        $(".empresa").addClass('hidden');
        $(".usuario").removeClass('hidden');
        poblateData('usuario',DataUser);
    }
}

ValidacionEmpresa = function(data){
     $('#t_empresa').dataTable().fnDestroy();
    //if(data.length > 1){
        var html = '';
        $.each(data,function(index, el) {
            html+='<tr id=e'+el.id+'>';
            html+='<td class="ruc">'+el.ruc+
                    '<input type="hidden" class="persona_id" value="'+el.persona_id+'">'+
                    '</td>';
            html+='<td class="tipo">'+ $.trim(el.tipo)+'</td>';
            html+='<td class="razon_social">'+ $.trim(el.razon_social)+'</td>';
            html+='<td class="nombre_comercial">'+ $.trim(el.nombre_comercial)+'</td>';
            html+='<td class="direccion_fiscal">'+ $.trim(el.direccion_fiscal)+'</td>';
            html+='<td class="telefono">'+ $.trim(el.telefono)+'</td>';
            html+='<td class="fecha_vigencia">'+ $.trim(el.fecha_vigencia)+'</td>';
            html+='<td class="estado">'+ $.trim(el.estado)+'</td>';
            html+='<td class="representante">'+ $.trim(el.representante)+'</td>';
            html+='<td class="dnirepre">'+ $.trim(el.dnirepre)+'</td>';
            html+='<td><span class="btn btn-primary btn-sm" id-empresa='+el.id+' onClick="selectEmpresa(this)">Seleccionar</span></td>';
            html+='</tr>';
        });
        $('#tb_empresa').html(html);
         $("#t_empresa").dataTable(); 
        $('#empresasbyuser').modal('show');
    /*}else if(data.length == 1){
        poblateData('empresa',data[0]);
    }else{
        $(".empresa").addClass('hidden');
        alert('no cuenta con una empresa');
    }*/
}

selectEmpresa = function(obj){
    var idempresa = obj.getAttribute('id-empresa');
    if(idempresa != ''){
        //Bandeja.GetEmpresabyId({id:idempresa});
        datos = {
            id: idempresa,
            tipo: $("#tb_empresa #e"+idempresa+" .tipo").text(),
            persona_id: $("#tb_empresa #e"+idempresa+" .persona_id").val(),
            razon_social: $("#tb_empresa #e"+idempresa+" .razon_social").text(),
            ruc: $("#tb_empresa #e"+idempresa+" .ruc").text(),
            telefono: $("#tb_empresa #e"+idempresa+" .telefono").text(),
            direccion_fiscal: $("#tb_empresa #e"+idempresa+" .direccion_fiscal").text(),
        }
        poblateData('empresa',datos);
    }else{
        msjG.mensaje("warning", 'Seleccione empresa',3000);
    }
}

HTMLPersonas = function(data){
     $('#t_persona').dataTable().fnDestroy();
    if(data.length > 0){
        var html = '';
        $.each(data,function(index, el) {
            html+='<tr id=p'+el.id+'>';
            html+='<td class="nombre">'+$.trim(el.name)+
                    '<input class="celular" type="hidden" value="'+$.trim(el.celular)+'">'+
                    '<input class="telefono" type="hidden" value="'+$.trim(el.telefono)+'">'+
                    '<input class="direccion" type="hidden" value="'+$.trim(el.direccion)+'">'+
                    '</td>';
            html+='<td class="paterno">'+$.trim(el.paterno)+'</td>';
            html+='<td class="materno">'+$.trim(el.materno)+'</td>';
            html+='<td class="dni">'+$.trim(el.dni)+'</td>';
            html+='<td class="email">'+$.trim(el.email)+'</td>';
           /* html+='<td class="telefono">'+el.telefono+'</td>';*/
            html+='<td><span class="btn btn-primary btn-sm" id-user='+el.id+' onClick="selectUser(this)">Seleccionar</span></td>';
            html+='</tr>';
        });
        $('#tb_persona').html(html);
        $('#t_persona thead th').each( function () {
            var title = $('#t_persona tfoot th').eq( $(this).index() ).text();
            if( title!= 'SELECCIONAR' ){
                $(this).html( '<input type="text" class="col-sm-12" placeholder="Buscar '+title+'">' );
            }
        } );

        // DataTable
        var table = $('#t_persona').DataTable({
            ordering: false,
        });

        $("#t_persona_filter").addClass("hidden");

        // Apply the search
        table.columns().eq( 0 ).each( function ( colIdx ) {
            $( 'input', table.column( colIdx ).header() ).on( 'keyup change', function () {
                table
                    .column( colIdx )
                    .search( this.value )
                    .draw();
            } );
        } );
        $('#selectPersona').modal('show'); 
    }else{
        $(".empresa").addClass('hidden');
        msjG.mensaje("warning", 'Error',3000);
    }
}

selectUser = function(obj){
    if( CantidadG != 1 || ( CantidadG == 1 && $.trim($("#tb_usuarios").html()) == '' ) ){
        var iduser = obj.getAttribute('id-user');
        if(iduser){
            //Bandeja.GetPersonabyId({persona_id:iduser});
            datos = {
                id: iduser,
                nombre: $("#tb_persona #p"+iduser+" .nombre").text(),
                paterno: $("#tb_persona #p"+iduser+" .paterno").text(),
                materno: $("#tb_persona #p"+iduser+" .materno").text(),
                dni: $("#tb_persona #p"+iduser+" .dni").text(),
                email: $("#tb_persona #p"+iduser+" .email").text(),
                celular: $("#tb_persona #p"+iduser+" .celular").val(),
                telefono: $("#tb_persona #p"+iduser+" .telefono").val(),
                direccion: $("#tb_persona #p"+iduser+" .direccion").val(),
            };
            console.log(datos);
            poblateData('persona',datos);
            //$('#selectPersona').modal('hide');
        }
        else{
            msjG.mensaje("warning", 'Seleccione persona',3000);
        }
    }
    else{
        msjG.mensaje("warning", 'El servicio seleccionado no puede contener más de 1 solicitante.',5000);
    }
}

poblateData = function(tipo,data){
    document.querySelector('#txt_userdni').value=  '<?php echo Auth::user()->dni; ?>';
    document.querySelector('#txt_usernomb').value='<?php echo Auth::user()->nombre; ?>';
    document.querySelector('#txt_userapepat').value='<?php echo Auth::user()->paterno; ?>';
    document.querySelector('#txt_userapemat').value='<?php echo Auth::user()->materno; ?>';
    
    validarRegistro = false;
    if( $.trim($("#t_usuarios #e"+data.id).html()) != '' || $.trim($("#t_usuarios #p"+data.id).html()) != '' ){
        validarRegistro = true;
    }
    
    if( validarRegistro == false ){
        if(tipo == 'empresa'){
            html=   '<tr id="e'+data.id+'">'+
                        '<td>Empresa | '+data.tipo+' <input type="hidden" value="'+data.id+'" name="empresa_id_sol[]"><input type="hidden" value="'+data.persona_id+'" name="persona_id_sol[]"></td>'+
                        '<td>'+ data.razon_social +'</td>'+
                        '<td>'+ data.ruc +'</td>'+
                        '<td><input class="form-control" name="txt_telefono_sol[]" type="text" value="'+ $.trim(data.telefono) +'"></td>'+
                        '<td>-<input type="hidden" name="txt_celular_sol[]" value=""></td>'+
                        '<td>-<input type="hidden" name="txt_email_sol[]" value=""></td>'+
                        '<td><TextArea class="col-md-12" name="txt_direccion_sol[]" row=3>'+ $.trim(data.direccion_fiscal) +'</TextArea></td>'+
                        '<td><span class="btn btn-danger btn-sm" onClick="EliminarTr(\'e'+data.id+'\',\'usuarios\')"><i class="fa fa-trash"></i></span></td>'
                    '</tr>';
            $("#tb_usuarios").append(html);
        }
        else if(tipo== 'persona'){
            html=   '<tr id="p'+data.id+'">'+
                        '<td>Persona<input type="hidden" value="0" name="empresa_id_sol[]"><input type="hidden" value="'+data.id+'" name="persona_id_sol[]"></td>'+
                        '<td>'+ data.paterno + ' ' + data.materno + ', ' + data.nombre +'</td>'+
                        '<td>'+ data.dni +'</td>'+
                        '<td><input class="form-control" name="txt_telefono_sol[]" type="text" value="'+ $.trim(data.telefono) +'"></td>'+
                        '<td><input class="form-control" name="txt_celular_sol[]" type="text" value="'+ $.trim(data.celular) +'"></td>'+
                        '<td><TextArea class="col-md-12" name="txt_email_sol[]" row=3>'+ $.trim(data.email) +'</TextArea></td>'+
                        '<td><TextArea class="col-md-12" name="txt_direccion_sol[]" row=3>'+ $.trim(data.direccion) +'</TextArea></td>'+
                        '<td><span class="btn btn-danger btn-sm" onClick="EliminarTr(\'p'+data.id+'\',\'usuarios\')"><i class="fa fa-trash"></i></span></td>'
                    '</tr>';
            $("#tb_usuarios").append(html);
        }
    }
    else{
        msjG.mensaje("warning", 'Solicitante ya fue seleccionado!',3000);
    }
    
    if(tipo== 'selectpersona'){
        document.querySelector('#FrmCrearEmpresa #txt_persona_id2').value=data.id;
        document.querySelector('#FrmCrearEmpresa #txt_persona2').value=data.nombre+" "+data.paterno+" "+data.materno;
        document.querySelector('#txt_idclasitramite').value=data.id;
//        document.querySelector('#txt_idarea').value=data.areaid;
    }


    if(tipo== 'tramite'){
        document.querySelector('#txt_nombretramite').value=data.nombre;
        document.querySelector('#txt_idclasitramite').value=data.id;
        document.querySelector('#txt_idarea').value=data.area_id;
    }
}

EliminarTr = (t, idname) =>{
    $("#t_"+idname).dataTable().fnDestroy();
    $("#"+t).remove();
    $("#t_"+idname).dataTable({
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        "ordering": true,
        "searching": false,
    });
}

consultar = function(){
    var busqueda = document.querySelector("#txtbuscarclasificador");
    var tipotramite = document.querySelector('#cbo_tipotramite');
    var area = document.querySelector('#slct_areas');

    var data = {};
    data.estado = 1;
    if( $("#cbo_tipotramite").val()!==""){
        if(busqueda){
        data.buscar = busqueda.value;
        }
        if(tipotramite){
        data.tipotra = tipotramite.value;
        }
        if(area){
        data.areaini = area.value;
        }
        Bandeja.getClasificadoresTramite(data,HTMLClasificadores);
        $(".rowArea").addClass('hidden');
        $('#buscartramite').modal('show');
    }
    else{
        msjG.mensaje("warning", 'Seleccione tipo de servicio',3000);
    }
    
}

HTMLClasificadores = function(data){
    $("#t_clasificador").dataTable().fnDestroy();
    var html = '';
    if(data.length > 0){
        $.each(data,function(index, el) {
            html+='<tr>';
            html+='<td>'+el.id+'</td>';
            html+='<td style="text-align: left">'+el.nombre_clasificador_tramite+'</td>';
            html+='<td><span class="btn btn-info btn-sm" id="'+el.id+'" nombre="'+el.nombre_clasificador_tramite+'" onClick="getRequisitos(this)">Ver</span></td>';
            html+='<td><span class="btn btn-info btn-sm" id="'+el.id+'" nombre="'+el.nombre_clasificador_tramite+'" onclick="cargarRutaId('+el.ruta_flujo_id+',2)">Ver Ruta</span></td>';
            html+='<td><span class="btn btn-primary btn-sm" id="'+el.id+'" nombre="'+el.nombre_clasificador_tramite+'" area_id="'+el.area_id+'" onclick="selectClaTramite(this)">Seleccionar</span></td>';
            html+='</tr>';        
        });
    }
        $("#tb_clasificador").html(html);
        $("#t_clasificador").dataTable(
                {
                    "order": [[ 0, "asc" ],[1, "asc"]],
                }
        ); 
        $("#t_clasificador").show();        
}

selectClaTramite = function(obj){
    console.log(obj);
    data ={'id':obj.getAttribute('id'),'nombre':obj.getAttribute('nombre'),'area_id':obj.getAttribute('area_id')};
    poblateData('tramite',data);
    $('#buscartramite').modal('hide');
   /* Bandeja.GetAreasbyCTramite({'idc':obj.getAttribute('id')},data);*/
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
/*
confirmInfo = function(data,tipo){
    if(tipo == 'incompleto'){ //falta seleccionar su area
        var areaSelect = document.querySelector("#slcAreasct");
        if(areaSelect.value != ''){
            data.area = areaSelect.value;
            poblateData('tramite',data);
            $('#buscartramite').modal('hide');
        }else{
            alert('seleccione una area');
        }
    }else{
        poblateData('tramite',data);
        $('#buscartramite').modal('hide');
    }
}
*/
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
            if( $.trim(el.ruta_archivo)!='' ){
                html+="<td data-url='"+el.ruta_archivo+"'><a class='btn btn-info btn-lg' href='"+el.ruta_archivo+"' target='_blank'><i class='fa fa-file fa-lg'></i></td>";
            }
            else{
                html+='<td data-url="'+el.ruta_archivo+'"> - </td>';
            }
            html+='<ul></tr>';
        });
        $("#tb_requisitos").html(html);
        $("#nombtramite").text(tramite);
        $("#requisitos").modal('show');
    }
}

generarPreTramite = function(){
    if( $(".tipo_documento").css("display") == 'none' ){
        $("#txt_tipodoc").val('S/N');
    }
    /*if($("#cbo_tipodocumento").val()==''){
        msjG.mensaje("warning", 'Selecciona Tipo documento de ingreso',3000);
    }
    else */
    if($("#slct_areas").val()==''){
        msjG.mensaje("warning", 'Selecciona Área de inicio del servicio',3000);
    }
    else if($("#cbo_tiposolicitante").val()==''){
        msjG.mensaje("warning", 'Selecciona Tipo de solicitante',3000);
    }
    else if( $("#tb_usuarios tr").legnth==0 ){
        msjG.mensaje("warning", 'Busque y seleccione solicitante',3000);
    }
    /*else if( $("#txt_usertelf2").val()=='' && $("#txt_usercel2").val()==''){
        msjG.mensaje("warning", 'Ingrese número de teléfono y/o número de celular',5000);
    }
    else if( $("#txt_useremail2").val()=='' ){
        msjG.mensaje("warning", 'Ingrese email',3000);
    }
    else if( $("#txt_userdirec2").val()=='' ){
        msjG.mensaje("warning", 'Ingrese dirección',3000);
    }*/
    else if($("#cbo_tipotramite").val()==''){
        msjG.mensaje("warning", 'Seleccione Tipo de trámite',3000);
    }
    else if($("#txt_nombretramite").val()==''){
        msjG.mensaje("warning", 'Busque y seleccione trámite',3000);
    }
    else if( $("#slct_local").val()=='' ){
        msjG.mensaje("warning", 'Seleccione Lugar de procedencia',3000);
    }
    else if($("#cbo_tipodoc").val()==''){
        msjG.mensaje("warning", 'Seleccione Tipo de documento',3000);
    }
    else if( $("#txt_numfolio").val()=='' ){
        msjG.mensaje("warning", 'Ingrese número de folio',3000);
    }
    else if( $("#txt_tipodoc").val()=='' ){
        msjG.mensaje("warning", 'Ingrese número del documento presentado',3000);
    }
    else{
        $("#t_usuarios").dataTable().fnDestroy();
        $("#t_referidos").dataTable().fnDestroy();
        datos=$("#FormCrearPreTramite").serialize().split("txt_").join("").split("slct_").join("");
        //.split("%5B%5D").join("[]").split("+").join(" ").split("%7C").join("|").split("&");
        /*data = '{';
        for (var i = 0; i < datos.length ; i++) {
            var elemento = datos[i].split('=');
            data+=(i == 0) ? '"'+elemento[0]+'":"'+elemento[1] : '","' + elemento[0]+'":"'+elemento[1];   
        }
        data+='"}';*/
        Bandeja.GuardarPreTramite(datos);
       
    }
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

generarUsuario = function(){
    if($("#nombre").val() == ''){
        alert('Digite su nombre');
    }else if($("#paterno").val() == ''){
        alert('Digite su apellido paterno');
    }else if($("#materno").val() == ''){
        alert('Digite su apellido materno');
    }else if($("#dni").val() == ''){
        alert('Digite su dni');
    }else if($("#email").val() == ''){
        alert('Digite su email');
    }else if($("#sexo").val() == ''){
        alert('Seleccione sexo');
    }else if($("#celular").val() == '' && $("#telefono").val() == ''){
        alert('Ingrese Celular y/o Teléfono');
    }else{
        Bandeja.guardarUsuario();        
    }
}

generarEmpresa = function(){
    if($("#txt_ruc2").val() == ''){
        alert('Digite su ruc');
    }else if($("#txt_razonsocial2").val() == ''){
        alert('Digite su razon social');
    }else if($("#txt_nombcomer").val() == ''){
        alert('Digite su nombre comercial');
    }else if($("#txt_direcfiscal").val() == ''){
        alert('Digite su direccion fiscal');
    }else if($("#cbo_tipoempresa").val() == ''){
        alert('Seleccione un tipo de empresa');
    }else{
        Bandeja.guardarEmpresa();        
    }
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
