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
<?php if ($tamano==4) {   ?>
<header>
    <div class="logo"><img align="left" src="img/logo_muni.png" style="width: 80px; height: width: 80px;;"></div>
    <h4 class="nombre-anio">&nbsp;</h4>
    <h4 class="nombre-vistaprevia">{{ $vistaprevia }}</h4>
    <?php if ($reporte==1) { ?><div class="qr">{{ $imagen }}</div><?php } ?>
    <div class="rotar1"><?php if($vistaprevia!=''){echo "Documento No Válido";} ?></div> 
</header>
<?php } ?>

<?php if ($reporte==1) {   ?>
    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="60%"></td>
        <td width="25%">EXPEDIENTE</td>
        <td width="1%">:</td>
        <td width="14%" style="text-align:right;">{{ $expediente }}</td>
      </tr>
      <tr>
        <td width="60%"></td>
        <td width="25%">FECHA DE EMISION</td>
        <td width="1%">:</td>
        <td width="14%" style="text-align:right;">{{ $fecha_emi }}</td>
      </tr>
      <tr>
        <td width="60%"></td>
        <td width="25%">FECHA DE VENCIMIENTO</td>
        <td width="1%">:</td>
        <td width="14%" style="text-align:right;">{{ $fecha_vence }}</td>
      </tr>
    </table> 
    </div>

    <div style="overflow:hidden;">
        <h2 class="nombre-documento">
          <?php if($correlativo <= 9) $correlativo = '00'.$correlativo;
                else if($correlativo <= 99) $correlativo = '0'.$correlativo;
                else $correlativo = $correlativo;?>
            RESOLUCION DE LICENCIA DE EDIFICACION N° <?=$correlativo?> - {{ $anio }} - GDU/MDI
        </h2>
    </div>

    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="70%">LICENCIA DE EDIFICACION:&nbsp;&nbsp; <strong>{{ $licencia_edifica }}</strong></td>
        <td width="30%">MODALIDAD:&nbsp;&nbsp; <strong>{{ $mod }}</strong></td>
      </tr>  
     </table> 
    </div>
      
    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="35%">USO:&nbsp;&nbsp; <strong>{{ $uso }}</strong></td>
        <td width="35%">ZONIFICACION:&nbsp;&nbsp; <strong>{{ $zonifica }}</strong></td>
        <td width="30%">ALTURA:&nbsp;&nbsp; <strong>{{ $altura }}</strong></td>
      </tr>  
     </table> 
    </div>

    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="70%">ADMINISTRADO:&nbsp;&nbsp; <strong>{{ $persona }}</strong></td>
        <td width="30%">PROPIETARIO:&nbsp;&nbsp; <strong>{{ $propietario }}</strong></td>
      </tr>  
     </table> 
    </div>
     
    <div style="overflow:hidden;">
     <table style="width:100%">
       <tr>
        <td width="15%">UBICACION:</td>
        <td colspan="2" width="85%">&nbsp;</td>
      </tr>
      <tr style="text-align: center;">
        <td width="40%"><strong>{{ $departamento }}</strong></td>
        <td width="15%"><strong>{{ $provincia }}</strong></td>
        <td width="45%"><strong>{{ $distrito }}</strong></td>
      </tr>
      <tr style="text-align: center;">
        <td width="40%">Departamento</td>
        <td width="15%">Provincia</td>
        <td width="45%">Distrito</td>
      </tr>
     </table> 
    </div>

    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="45%"><strong>{{ $dir_urbaniza }}</strong></td>
        <td width="10%"><strong>{{ $dir_mz }}</strong></td>
        <td width="10%"><strong>{{ $dir_lote }}</strong></td>
        <td width="35%"><strong>{{ $dir_calle }}</strong></td>
      </tr>
      <tr>
        <td width="45%">Urbanización/AA.HH/Otro</td>
        <td width="10%">Mz.</td>
        <td width="10%">Lote</td>
        <td width="35%">Av./Jr./Calle/Pasaje</td>
      </tr>
     </table> 
    </div>
     
    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="60%">AREA DE TERRENO:&nbsp;&nbsp; <strong>{{ $area_terre }}</strong> M2</td>
        <td width="40%">VALOR DE OBRA:&nbsp;&nbsp; <strong>S/. {{ $valor_obra }}</strong></td>
      </tr>  
     </table> 
    </div>

    <br/>
    <div style="overflow:hidden;">
      <table style="width:44%; border: 0px solid #CCC; text-align: center;">
      <tr>
        <td style="border: 1px solid #CCC; font-size:12px;" width="50%">{{ $piso }}</td>
        <td style="border: 1px solid #CCC; font-size:12px;" width="50%">{{ $area_techada }}</td>
      </tr>
      <?php 
            $tot_area = 0;
            if($piso_1 != '' && $area_1 != ''): $tot_area = $tot_area + $area_1; ?>
      <tr>
        <td style="border: 1px solid #CCC;" width="50%"><?=$piso_1?></td>
        <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_1, 2)?> m2</td>
      </tr>
      <?php endif;?>
      <?php if($piso_2 != '' && $area_2 != ''): $tot_area = $tot_area + $area_2; ?>
      <tr>
        <td style="border: 1px solid #CCC;" width="50%"><?=$piso_2?></td>
        <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_2, 2)?> m2</td>
      </tr>
      <?php endif;?>
      <?php if($piso_3 != '' && $area_3 != ''): $tot_area = $tot_area + $area_3; ?>
      <tr>
        <td style="border: 1px solid #CCC;" width="50%"><?=$piso_3?></td>
        <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_3, 2)?> m2</td>
      </tr>
      <?php endif;?>
      <?php if($piso_4 != '' && $area_4 != ''): $tot_area = $tot_area + $area_4; ?>
      <tr>
        <td style="border: 1px solid #CCC;" width="50%"><?=$piso_4?></td>
        <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_4, 2)?> m2</td>
      </tr>
      <?php endif;?>
      <?php if($piso_5 != '' && $area_5 != ''): $tot_area = $tot_area + $area_5; ?>
      <tr>
        <td style="border: 1px solid #CCC;" width="50%"><?=$piso_5?></td>
        <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_5, 2)?> m2</td>
      </tr>
      <?php endif;?>
      <tr>
        <td style="border: 1px solid #CCC;" width="50%">&nbsp;</td>
        <td style="border: 1px solid #CCC;" width="50%"><?=number_format($tot_area,2)?> m2</td>
      </tr>
     </table>
    </div>
    <br/><br/>
      
    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="100%">DERECHO DE LICENCIA:&nbsp;&nbsp; S/. <strong>{{ $derecho_licencia }}</strong> (Recibo N° {{ $recibo }} {{ $fecha_recibo }}) </td>
      </tr>  
     </table> 
    </div>
     
    <br/><br/>
    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td><strong>OBSERVACIONES</strong></td>
      </tr> 
      <tr>
        <td>
          <ol>
          <li value="1">A excepción de las obras preliminares, para el inicio de la(s) obra(s) autorizada(s) con la licencia, el administrado debe presentar el Anexo H.</li>
          <li>La  obra a ejecutarse deberá ajustarse al proyecto autorizado. ante cualquier modificación sustancial que se efectué sin autorización, la Municipalidad puede disponer la adopción de medidas provisionales de inmediata ejecución previstas en el Numeral 6 del Artículo 10º de la ley 29090-Ley de Regularización de Habilitaciones Urbanas y de Edificaciones.</li>
          <li>La licencia tiene una vigencia de 36 meses prorrogable por única vez por 12 meses, debiendo ser solicitada dentro de los 30 días calendario anterior a su vencimiento.</li>
          <li>Vencido el plazo de la licencia, esta puede ser revalida por 36 meses.</li>
          <li>Los horarios para ejecución de obra quedara establecido de lunes a viernes de 7:30 am a 18:00 pm y sábados de 8am a 13:00 pm.</li>
          </ol>
        </td>
      </tr> 
     </table> 
    </div>
     
    <br/>
    <div style="overflow:hidden;">Fecha: {{ $fecha_actual_texto }}</div>

    <br/><br/><br/><br/><br/><br/><br/>
    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr style="text-align:center;">
        <td width="50%"></td>
        <td width="50%">........................................................................................................</td>
      </tr>
      <tr style="text-align:center;">
        <td width="50%"></td>
        <td width="50%">Sello y firma del Funcionario Municipal que otorga la Licencia</td>
      </tr>
     </table> 
    </div>

<?php } else if ($reporte==2) {  // VISTA IMPRESIÓN ?>
    <div style="overflow:hidden;">
     <table style="width:100%">
      <tr>
        <td width="60%"></td>
        <td width="25%">EXPEDIENTE</td>
        <td width="1%">:</td>
        <td width="14%" style="text-align:right;">{{ $expediente }}</td>
      </tr>
      <tr>
        <td width="60%"></td>
        <td width="25%">FECHA DE EMISION</td>
        <td width="1%">:</td>
        <td width="14%" style="text-align:right;">{{ $fecha_emi }}</td>
      </tr>
      <tr>
        <td width="60%"></td>
        <td width="25%">FECHA DE VENCIMIENTO</td>
        <td width="1%">:</td>
        <td width="14%" style="text-align:right;">{{ $fecha_vence }}</td>
      </tr>
    </table> 
    </div>

    <div style="overflow:hidden;">
        <h2 class="" style="text-align: center; font-weight: bold;">
          <?php if($correlativo <= 9) $correlativo = '00'.$correlativo;
                else if($correlativo <= 99) $correlativo = '0'.$correlativo;
                else $correlativo = $correlativo;?>
            RESOLUCION DE LICENCIA DE EDIFICACION N° <?=$correlativo?> - {{ $anio }} - GDU/MDI
        </h2>
    </div>

    <div style="overflow:hidden;">
     <table style="width:100%">
        <tr>
            <td>LICENCIA DE EDIFICACION:&nbsp;&nbsp; <strong>{{ $licencia_edifica }}</strong></td>
        </tr>
        <tr>
            <td>MODALIDAD:&nbsp;&nbsp; <strong>{{ $mod }}</strong></td>
        </tr>
        <tr>
            <td>USO:&nbsp;&nbsp; <strong>{{ $uso }}</strong></td>
        </tr>
        <tr>
            <td>ZONIFICACION:&nbsp;&nbsp; <strong>{{ $zonifica }}</strong></td>
        </tr>
        <tr>
            <td>ALTURA:&nbsp;&nbsp; <strong>{{ $altura }}</strong></td>
        </tr>
        <tr>
            <td>ADMINISTRADO:&nbsp;&nbsp; <strong>{{ $persona }}</strong></td>
        </tr>
        <tr>
            <td>PROPIETARIO:&nbsp;&nbsp; <strong>{{ $propietario }}</strong></td>
        </tr>
        <tr>
            <td>Departamento:&nbsp;&nbsp; <strong>{{ $departamento }}</strong></td>
        </tr>
        <tr>
            <td>Provincia:&nbsp;&nbsp; <strong>{{ $provincia }}</strong></td>
        </tr>
        <tr>
            <td>Distrito:&nbsp;&nbsp; <strong>{{ $distrito }}</strong></td>
        </tr>
        <tr>
            <td>Urbanización/AA.HH/Otro:&nbsp;&nbsp; <strong>{{ $dir_urbaniza }}</strong></td>
        </tr>
        <tr>
            <td>Mz:&nbsp;&nbsp; <strong>{{ $dir_mz }}</strong></td>
        </tr>
        <tr>
            <td>Lote:&nbsp;&nbsp; <strong>{{ $dir_lote }}</strong></td>
        </tr>
        <tr>
            <td>Av./Jr./Calle/Pasaje:&nbsp;&nbsp; <strong>{{ $dir_calle }}</strong></td>
        </tr>
        <tr>
            <td>AREA DE TERRENO:&nbsp;&nbsp; <strong>{{ $area_terre }}</strong></td>
        </tr>
        <tr>
            <td>VALOR DE OBRA:&nbsp;&nbsp; <strong>{{ $valor_obra }}</strong></td>
        </tr>
        <tr>
            <td>
                <table style="width:44%; border: 0px solid #CCC; text-align: center;">
                  <tr>
                    <td style="border: 1px solid #CCC; font-size:12px;" width="50%">{{ $piso }}</td>
                    <td style="border: 1px solid #CCC; font-size:12px;" width="50%">{{ $area_techada }}</td>
                  </tr>
                  <?php 
                        $tot_area = 0;
                        if($piso_1 != '' && $area_1 != ''): $tot_area = $tot_area + $area_1; ?>
                  <tr>
                    <td style="border: 1px solid #CCC;" width="50%"><?=$piso_1?></td>
                    <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_1, 2)?> m2</td>
                  </tr>
                  <?php endif;?>
                  <?php if($piso_2 != '' && $area_2 != ''): $tot_area = $tot_area + $area_2; ?>
                  <tr>
                    <td style="border: 1px solid #CCC;" width="50%"><?=$piso_2?></td>
                    <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_2, 2)?> m2</td>
                  </tr>
                  <?php endif;?>
                  <?php if($piso_3 != '' && $area_3 != ''): $tot_area = $tot_area + $area_3; ?>
                  <tr>
                    <td style="border: 1px solid #CCC;" width="50%"><?=$piso_3?></td>
                    <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_3, 2)?> m2</td>
                  </tr>
                  <?php endif;?>
                  <?php if($piso_4 != '' && $area_4 != ''): $tot_area = $tot_area + $area_4; ?>
                  <tr>
                    <td style="border: 1px solid #CCC;" width="50%"><?=$piso_4?></td>
                    <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_4, 2)?> m2</td>
                  </tr>
                  <?php endif;?>
                  <?php if($piso_5 != '' && $area_5 != ''): $tot_area = $tot_area + $area_5; ?>
                  <tr>
                    <td style="border: 1px solid #CCC;" width="50%"><?=$piso_5?></td>
                    <td style="border: 1px solid #CCC;" width="50%"><?=number_format($area_5, 2)?> m2</td>
                  </tr>
                  <?php endif;?>
                  <tr>
                    <td style="border: 1px solid #CCC;" width="50%">&nbsp;</td>
                    <td style="border: 1px solid #CCC;" width="50%"><?=number_format($tot_area,2)?> m2</td>
                  </tr>
                 </table>
            </td>
        </tr>
        <tr>
            <td>DERECHO DE LICENCIA:&nbsp;&nbsp; <strong>{{ $derecho_licencia }}</strong> (Recibo N° {{ $recibo }} {{ $fecha_recibo }})</td>
        </tr>
        <tr>
            <td><br/><br/><br/><br/>Fecha: {{ $fecha_actual_texto }}</td>
        </tr>
     </table>
    </div>
<?php } ?>



</body>
</html>
