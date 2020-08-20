<script type="text/javascript">
var camposP = {};
$(document).ready(function() {
    slctGlobal.listarSlctFuncion('area','listara','slct_area_id','simple',null,{estado:1,areapersona:1});
/*    slctGlobal.listarSlctFuncion('plantilladoc','cargar','slct_plantilla','simple',null,{'area':1});

    slctGlobal.listarSlct('area','slct_areas','multiple',null,{estado:1,areagestion:1});
    slctGlobal.listarSlct('area','slct_copia','multiple',null,{estado:1,areagestion:1});*/

    /*slctGlobal.listarSlctFuncion('area','areasgerencia','slct_areas','multiple',null,{estado:1,areagestion:1});
    slctGlobal.listarSlctFuncion('area','areasgerencia','slct_copia','multiple',null,{estado:1,areagestion:1});*/

    /*slctGlobalHtml('slct_tipoenvio','simple');
    slctGlobal.listarSlct('area','slct_areasp','simple',null,{estado:1,areagestion:1}); 
    HTML_Ckeditor(); 

    $(document).on('change', '#slct_tipoenvio', function(event) {
        event.preventDefault();
        if($(this).val() == 1){ //persona
            $(".araesgerencia").addClass('hidden');
            $(".areaspersona").removeClass('hidden');
            $(".todassubg").addClass('hidden');
        }else if($(this).val() == 2){ //gerencia
             $(".araesgerencia").removeClass('hidden');
             $(".areaspersona").addClass('hidden');
             $(".personasarea").addClass('hidden');
             $(".todassubg").removeClass('hidden');
        }else{
            $(".araesgerencia,.areaspersona,.personasarea,.todassubg,.copias").addClass('hidden');
        }
    });

    $('.chk').on('ifChanged', function(event){
        if(event.target.value == 'allgesub'){
            if(event.target.checked == true){
                $(".copias").addClass('hidden');
                $(".araesgerencia").addClass('hidden');
                $('#slct_areas option').prop('selected', true);
                $('#slct_areas').multiselect('refresh');
            }else{
                $(".copias").removeClass('hidden');
                $(".araesgerencia").removeClass('hidden');

                $('#slct_areas option').prop('selected', false);
                $('#slct_areas').multiselect('refresh');
            }
        }
    });

    $(document).on('change','#slct_areasp',function(event){
        $('#slct_personaarea').multiselect('destroy');
        slctGlobal.listarSlctFuncion('area','personaarea','slct_personaarea','simple',null,{'area_id':$(this).val()});  
        $(".personasarea").removeClass('hidden');
    });

    $(document).on('change', '#slct_plantilla', function(event) {
        event.preventDefault();
        docdigital.CargarInfo({'id':$(this).val()},HTMLPlantilla);
    });

    $(document).on('click', '#btnCrear', function(event) {
        event.preventDefault();       
        Agregar();
    });*/


    /*$('#NuevoDocDigital').on('show.bs.modal', function (event) {
        $("#slct_tipoenvio").multiselect('destroy');
        slctGlobalHtml('slct_tipoenvio','simple');
        var button = $(event.relatedTarget); // captura al boton
	    var text = $.trim( button.data('texto') );
	    var id= $.trim( button.data('id') );
	    $("#formNuevoDocDigital input[name='txt_campos']").remove();
        $("#formNuevoDocDigital").append("<input type='hidden' c_text='"+text+"' c_id='"+id+"' id='txt_campos' name='txt_campos'>");
    });*/

     $('#listDocDigital').on('show.bs.modal', function (event) {
        $("#listDocDigital #fechaDoc, #listDocDigital #txt_fecha_documento").datetimepicker({
            format: "yyyy-mm-dd",
            language: 'es',
            showMeridian: false,
            time:false,
            minView:2,
            startView:2,
            autoclose: true,
            todayBtn: false
        });
     	var button = $(event.relatedTarget); // captura al boton
	    var text = $.trim( button.data('texto') );
	    var id= $.trim( button.data('id') );
	    camposP = {'nombre':text,'id':id};
        
    });

    data = {estado:1};
    slctGlobal.listarSlct('documento','slct_tipo_documento_id','simple',null,data); 

    /*function limpia(area) {
        $(area).find('input[type="text"],input[type="hidden"],input[type="email"],textarea,select').val('');
        $("#lblDocumento").text('');
        $("#lblArea").text('');
        CKEDITOR.instances.plantillaWord.setData('');
        $(".araesgerencia,.areaspersona,.personasarea").addClass('hidden');
        $("#slct_copia").val(['']);
        $('input').iCheck('uncheck');
        $("#slct_copia").multiselect('refresh');
        $(".copias").removeClass('hidden');
    };

    $('#NuevoDocDigital').on('hidden.bs.modal', function(){
        limpia(this);
    });*/
});

CalcularCorrelativo = ()=>{
    if( $("#slct_area_id").val()=='' ){
        msjG.mensaje('warning', 'Seleccione área', 5000);
    }
    else if( $("#slct_tipo_documento_id").val()=='' ){
        msjG.mensaje('warning', 'Seleccione un Tipo de Documento', 5000);
    }
    else{
        data = {'tipo_doc':$("#slct_tipo_documento_id").val(), tipo_corre:2, area_id:$("#slct_area_id").val() }
        docdigital.CargarCorrelativo(data,HTMLCargarCorrelativo);
    }
}

CargarDocumentosFecha = ()=>{
    var data={activo:1, tipo:'asignar', fecha: $("#listDocDigital #fechaDoc").val()};
    docdigital.Cargar(HTMLCargar,camposP,data);
}


/*doc digital */
/*HTML_Ckeditor=function(){
    CKEDITOR.replace( 'plantillaWord' );
};

HTMLPlantilla = function(data){
    if(data.length > 0){
        var result = data[0];
        var tittle = result.tipodoc + "N-XX-2016" + "/MDI";
        document.querySelector("#lblDocumento").innerHTML= result.tipodoc+"-Nº";
        document.querySelector("#lblArea").innerHTML= "-"+new Date().getFullYear()+"-"+result.nemonico_doc;
        document.querySelector('#txt_area_plantilla').value = result.area_id;
        CKEDITOR.instances.plantillaWord.setData( result.cuerpo );
        docdigital.CargarCorrelativo({'tipo_doc':result.tipo_documento_id},HTMLCargarCorrelativo);
    }
}*/

HTMLCargar=function(datos,campos){
	var c_text = campos.nombre;
	var c_id = campos.id;

        console.log(datos);
    var html="";
    $('#t_doc_digital').dataTable().fnDestroy();
    $.each(datos,function(index,data){
        
        if($.trim(data.ruta) == 0 && $.trim(data.rutadetallev) == 0){
            html+="<tr class='danger'>";
        }else{
            html+="<tr class='success'>";
        }
      
        html+="<td>"+data.titulo+"</td>";
        //html+="<td>"+data.asunto+"</td>";
        vistaDoc = '';

        if( $.trim(data.doc_url) != '' || $.trim(data.doc_archivo) != '' ){
            vistaDoc = "<a class='btn btn-info btn-sm' id='"+data.id+"' onclick='verDocumento("+data.id+")'><i class='fa fa-eye'></i> </a>";
        }
        
        html+="<td>"+
            vistaDoc+
            "<a class='btn btn-warning btn-sm' id='"+data.id+"' onclick='ActualizarDoc("+data.id+")'><i class='fa fa-edit'></i> </a>"+
            "</td>";
        
        html+="<td><a class='btn btn-success btn-sm' c_text='"+c_text+"' c_id='"+c_id+"'  id='"+data.id+"' title='"+data.titulo+"' onclick='SelectDocDig(this)'><i class='glyphicon glyphicon-ok'></i> </a></td>";
        html+="</tr>";
    });
    $("#tb_doc_digital").html(html);
    $("#t_doc_digital").dataTable();
};

verDocumento= (id)=>{
    url = 'http://proceso.jssoluciones.pe/doc_digital/'+id;
    window.open(url, '_blank');
    return false;
}

ActualizarDoc= (id)=>{
    var data = {doc_digital_id: id};
    docdigital.ActualizarDoc(data);
}

SelectDocDig = function(obj,id){	
	var id = obj.getAttribute('id');
	var nombre = obj.getAttribute('title');
    console.log("#"+obj.getAttribute('c_text'), "#"+obj.getAttribute('c_id'));
    console.log(id, nombre);
	$("#"+obj.getAttribute('c_text')).val(nombre).attr("readonly",'true');
	$("#"+obj.getAttribute('c_id')).val(id);
	$("#listDocDigital").modal('hide');
}

openPlantilla=function(obj,id,tamano,tipo){
    var iddoc = id;
    if(id==0 || id ==''){
        iddoc=obj.getAttribute('id');
    }
    window.open("documentodig/vista/"+iddoc+"/"+tamano+"/"+tipo,
                "PrevisualizarPlantilla",
                "toolbar=no,menubar=no,resizable,scrollbars,status,width=900,height=700");
};

HTMLCargarCorrelativo=function(obj){
    $(".txttittle").val("");
    $(".txttittle").val(obj.correlativo);
    console.log(obj.area+ " - " +obj.año);
    $("#listDocDigital #txt_titulofinal").val($("#slct_tipo_documento_id option:selected").text()+ ' N° ' + obj.correlativo +' - '+obj.area+ " - " +obj.año);
    $("#listDocDigital #lblArea").text(obj.area+ " - " +obj.año);
}

onArchivos = function (event,obj) {
    var tr=obj.parentNode.parentNode;
    console.log(tr);
    var files = event.target.files || event.dataTransfer.files;
    if (!files.length)
        return;
    var image = new Image();
    var reader = new FileReader();
    reader.onload = (e) => {
        $(tr).find('input:eq(1)').val(e.target.result);
    };
    reader.readAsDataURL(files[0]);
    $(tr).find('input:eq(0)').val(files[0].name);
    console.log(files[0].name);
}

GenerarCodigo = ()=>{
    if( $("#slct_area_id").val()=='' ){
        msjG.mensaje('warning', 'Seleccione área', 5000);
    }
    else if( $("#slct_tipo_documento_id").val()=='' ){
        msjG.mensaje('warning', 'Seleccione un Tipo de Documento', 5000);
    }
    else{
        docdigital.GenerarDoc();
    }
}

GuardarArchivo = ()=>{
    docdigital.GuardarArchivo();
}
/*
AreaSeleccionadas = function(){
    areasSelect = [];
    if($('#slct_tipoenvio').val() == 1){ //persona
        areasSelect.push({'area_id':$('#slct_areasp').val(),'persona_id':$('#slct_personaarea').val(),'tipo':1});
    }else{ //gerencias
        $('#slct_areas  option:selected').each(function(index,el) {
            var area_id = $(el).val();
            var persona_id  = $(el).attr('data-relation').split('|');
            areasSelect.push({'area_id':area_id,'persona_id':persona_id[1],'tipo':1});
        });
    }
    if($("#slct_copia").val() != ''){
        $('#slct_copia  option:selected').each(function(index,el) {
            var area_id = $(el).val();
            var persona_id  = $(el).attr('data-relation').split('|');
            areasSelect.push({'area_id':area_id,'persona_id':persona_id[1],'tipo':2});
        });
    }
    return areasSelect;
}

copias = function(){
    $(".copias").removeClass('hidden');
}

Agregar=function(){
    docdigital.AgregarEditar(AreaSeleccionadas(),0,1);
};*/
/*end doc digital */
</script>