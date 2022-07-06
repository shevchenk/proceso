<script type="text/javascript">
var AlumnoG = {
    id : '',
    paterno : '',
    materno : '',
    nombre : '',
    dni : '',
    email : '',
    celular : '',
    habilitado : '',
    estado : '',
    accion: ''
}

$(document).ready( ()=> {
    $("#AlumnoCargaForm #btn_Procesar").click(Alumno.Procesar);
    $("#AlumnoCargaForm #btn_Descargar").click(Alumno.Descargar);
});

var Alumno = {
    Descargar : ()=>{
        window.location = 'upload/Plantillas/Alumnos.xlsx';
    },

    Procesar : ()=> {
        if( $("#AlumnoCargaForm #txt_alumno").val() != '' ){
            AjaxAlumno.Procesar(Alumno.HTMLGuardar);
        }
        else{
            msjG.mensaje('warning', 'Busque y seleccione el archivo a procesar', 5000 );
        }
    },

    HTMLGuardar : (result)=> {
        if( result.rst == 1 ){
            msjG.mensaje('success', result.msj, 5000 );
            Alumno.ResultadoHTML(result.datos);
            $("#AlumnoCargaForm input, #AlumnoCargaForm textarea").val('');
            $("#img_alumno").attr('src', 'Config/default.png');
        }
        else{
            msjG.mensaje('warning', result.msj, 3000 );
        }
    },

    ResultadoHTML : (result) => {
        html = '';
        $("#tableResultado tbody").html(html);
        $.each(result, function( index, value ){
            html =  '<tr>'+
                        '<td>'+value[0]+'</td>'+
                        '<td>'+value[1]+'</td>'+
                        '<td>'+value[2]+'</td>'+
                        '<td>'+value[3]+'</td>'+
                        '<td>'+value[4]+'</td>'+
                        '<td>'+value[5]+'</td>'+
                        '<td>'+value[6]+'</td>'+
                    '</tr>';
            $("#tableResultado tbody").append(html);
        })
    }

}

var AjaxAlumno={
    Procesar: (evento)=> {
        var data=$("#AlumnoCargaForm").serialize().split("txt_").join("").split("slct_").join("");
        url='persona/masivo';
        masterG.postAjax(url,data,evento);
    },
};
</script>
