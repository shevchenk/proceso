<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LICENCIA DE CONSTRUCCION</title>
<style>
.rotar1  
    {  
      -webkit-transform: rotate(-25deg);  
      -moz-transform: rotate(-25deg);  
      -ms-transform: rotate(-25deg); 
      -o-transform: rotate(-25deg);  
      transform: rotate(-25deg);  
       
      -webkit-transform-origin: 50% 50%;  
      -moz-transform-origin: 50% 50%;  
      -ms-transform-origin: 50% 50%;  
      -o-transform-origin: 50% 50%;  
      transform-origin: 50% 50%;  
       
      font-size: 50px;  
      width: 600px;  
      position: absolute; 
      right: -70px;
      top: 230px;  
      font-size: 40px; 
      opacity: 0.12;
    } 
    
html, body{
    font-size: 11px;
    line-height: 15px;
    font-family: arial, sans-serif;
}

.text-negrita {
    font-weight: bold;
}

.logo {
     left: 70px;position: absolute;top: -17px;
}
.logo img {
    height: 117px;
    width: 100px;
}
.nombre-municipio {
    position: absolute;
    top:  0px; 
    left: 208px;
    font-style: italic;
    font-size: 14px;
}
.nombre-vistaprevia {
    position: absolute;
    top:  60px; 
    left: 280px;
    font-style: italic;
    font-weight: bold;
    color: red;
    font-size: 14px;
    text-decoration: underline; 
}
.nombre-anio {
    font-style: italic;
    position: absolute;
    top:  50px; 
    left: 240px;
    font-size: 14px;
    padding: 0px;
    margin: 10px;
}
.gerencia {
    position: absolute;
    top:  25px; 
    left: 150px;
    font-style: italic;
    font-size: 15px;
}
.nombre-documento {
    text-align: center;
    font-size: 19px;
    text-decoration: underline; 
}

.nombre-documento-left {
    text-align: left;
    font-size: 19px;
    text-decoration: underline; 
}

.nombre-documento-right {
    text-align: right;
    font-size: 19px;
    text-decoration: underline; 
}

.fecha-documento-left {
    text-align: left;
    font-size: 12px;

}

.fecha-documento-right {
    text-align: right;
    font-size: 12px;

}

.cuerpo-documento {
    font-size: 12px;
}
.tabla-cabecera {
    border: none;
}
.tabla-cabecera td {
    vertical-align: top;
    border: none;
    padding: 5px;
}
.qr {
  position: absolute;
  top: -21px; 
  left: 580px;
}

.body-rest{
    margin-left: 1.8cm;       
}

.row {
  padding: 2;
  margin: 0;
}
@page {
    margin-bottom: 60px;
    margin-top: 150px;
}
header { 
    position: fixed;
    left: 0px;
    top: -100px;
    right: 0px;
    height: 130px;
}
footer {
    position: fixed;
    left: 0px;
    bottom: -20px;
    right: 0px;
    height: 10px;
    border-bottom: 2px solid #ddd;
}
footer .page:after {
    content: counter(page);
}
footer p {
    text-align: right;
}
footer .izq {
    text-align: left;
}

/* Estilos Certificados */


</style>

</head>

<footer class="body-rest">
    <table>
      <tr>
        <td>
            <p class="izq">
                Av. Túpac Amaru Km. 4.5 - Independencia / Teléfono 712-4100
            </p>
        </td>
        <td>
          &nbsp;
        </td>
      </tr>
    </table>
</footer>
<?php if ($tamano==4) { ?>
<header>
    <div class="logo"><img align="left" src="img/logo_muni.png" style="width: 80px; height: width: 80px;;"></div>
    <h4 class="nombre-anio">&nbsp;</h4>
    <h4 class="nombre-vistaprevia">{{ $vistaprevia }}</h4>
    <?php if ($reporte==1) { ?><div class="qr">{{ $imagen }}</div><?php } ?>
    <div class="rotar1"><?php if($vistaprevia!=''){echo "Documento No Válido";} ?></div> 
</header>
<?php } ?>

<?php if ($reporte==2) {  // VISTA IMPRESIÓN ?>
    <div style="overflow:hidden;">
        <h2 class="" style="text-align: center; font-weight: bold;">
          DOCUMENTO CANINO MUNICIPAL
        </h2>
    </div>

    <div style="overflow:hidden;">
     <table style="width:100%">
        <tr>
            <td><img src="http://proceso.munindependencia.pe/img/carnet_cane/<?php echo @$foto; ?>" style="border: 0px; height: 100px; width: 100px;"/></td>
        </tr>
        <tr>
            <td>REGISTRO NUMERO:&nbsp;&nbsp; <strong>{{ $serie }}</strong></td>
        </tr>
        <tr>
            <td>APELLIDOS:&nbsp;&nbsp; <strong>{{ $apellidos }}</strong></td>
        </tr>
        <tr>
            <td>NOMBRE:&nbsp;&nbsp; <strong>{{ $nombres }}</strong></td>
        </tr>
        <tr>
            <td>FECHA ENTREGA:&nbsp;&nbsp; <strong>{{ $fecha_entrega }}</strong></td>
        </tr>
        <tr>
            <td>FECHA NACIMIENTO:&nbsp;&nbsp; <strong>{{ $fecha_nace }}</strong></td>
        </tr>
        <tr>
            <td>SEXO:&nbsp;&nbsp; <strong>{{ $sexo }}</strong></td>
        </tr>
        <tr>
            <td>RAZA:&nbsp;&nbsp; <strong>{{ $raza }}</strong></td>
        </tr>
        <tr>
            <td><br/><br/><br/><br/>Fecha: {{ $fecha_actual_texto }}</td>
        </tr>
     </table>
    </div>
<?php } ?>



</body>
</html>
