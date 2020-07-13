<?php

?>
<style type="text/css">@media print {BODY {font-size: 14pt;font-family: Arial;}}@media screen {BODY {font-size: 12pt;font-family: Arial;}}@media screen , print {BODY {color: #000000;}}body {margin-left: 0px;margin-right: 0px;}.Estilo2 {font-size: 2px;font-weight: bold;}.Estilo4 {font-size: 14px;font-weight: bold;}.Estilo6 {font-size: 14px;text-align:center}.Estilo7 {font-size: 10px;font-weight: bold;}.Estilo8 {font-size: 30px;text-align:center}.Estilo9 {font-size: 14px;text-align:left}.MasGrande {font-size: 14px}.item {font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10px;color: #000;font-weight:bold;}.titulo {font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;color: #000;font-weight: bold;}.textoblack {font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10px;color: #000000;text-decoration: none;text-transform: uppercase;font-weight: bold;}.textoblack2 {font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 13px;color: #000000;text-decoration: none;text-transform: uppercase;font-weight: bold;}body {font-family: Arial;}table {display: table;border-collapse: separate;border-spacing: 2px;border-color: gray;}td, th {display: table-cell;vertical-align: inherit;}</style>

<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>

    <table width="100%"  cellspacing="0" cellpadding="0">
        <tr><td colspan="4"><p align="center" class="titulo"></p><hr><p align="center" class="titulo">DATOS DEL TRÁMITE</p></td></tr>
        <tr><td colspan="4" align="right">&nbsp;</td></tr>
        <tr>
            <td align="right" class="item" height="20">Fecha:</td>
            <td class="textoblack2" colspan="3">&nbsp;&nbsp;{{ $fregistro }} </td>
        </tr>
        <tr>
            <td align="right" class="item" height="20">Documento:</td>
            <td class="textoblack2" colspan="3">&nbsp;&nbsp;{{ $id_union }} </td>
        </tr>
        <tr>
          <td align="right" class="item" height="20">Tipo de tramite:</td>
          <td colspan="2" class="textoblack2">&nbsp;&nbsp;{{ $tipotramite }}<div></div></td>
          <td class="textoblack">&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td align="right" class="item" height="20">Trámite:</td>
            <td class="textoblack2" colspan="3">&nbsp;&nbsp;{{ $tramite }} </td>
        </tr>
        <tr>
          <td align="right" class="item" >Folios:</td>
          <td colspan="3" class="textoblack2">&nbsp;&nbsp;{{ $folio }}</td>
        <tr>
        <tr>
          <td align="right" class="item" >Observación:</td>
          <td colspan="3" class="textoblack2">{{ $observacion }}</td>
        <tr>
        <tr>
          <td align="right" class="item" >Area Responsable:</td>
          <td colspan="3" class="textoblack2">&nbsp;&nbsp;{{ $area }}</td>
        <tr>
        <tr><td colspan="4"><hr><p align="center" class="titulo">DATOS DEL SOLICITANTE</p></td></tr>
        <tr>
          <td align="right" class="item" >Tipo Solicitante:</td>
          <td colspan="3" class="textoblack2">&nbsp;&nbsp;{{ $solicitante }}</td>
        </tr>
        <tr>
          <td align="right" class="item" >Solicitante:</td>
          <td colspan="3" class="textoblack2">&nbsp;&nbsp;{{ $nombusuario.' '.$apepusuario.' '.$apemusuario }}</td>
        </tr>
        <tr>
          <td align="right" class="item" >DNI:</td>
          <td colspan="3" class="textoblack2">&nbsp;&nbsp;{{ $dniU }}</td>
        </tr>
        <?php if( trim($empresaid)!='' ){ ?>
        <tr>
          <td align="right" height="20" class="item">Razón Social:</td>
          <td class="textoblack2" colspan="3">&nbsp;&nbsp;{{ $empresa }}</td>
        </tr>
        <tr>
          <td align="right" height="20" class="item">RUC:</td>
          <td class="textoblack2" colspan="3">&nbsp;&nbsp;{{ $ruc }}</td>
        </tr>
        <tr>
          <td align="right" height="20" class="item">Domicilio Fiscal:</td>
          <td class="textoblack2" colspan="3">&nbsp;&nbsp;{{ $edireccion }}</td>
        </tr>
        <tr>
          <td align="right" height="20" class="item">Telefono:</td>
          <td class="textoblack2" colspan="3">&nbsp;&nbsp;{{ $etelf }}</td>
        </tr>
        <?php } ?>
        <tr><td colspan="4"><hr><p align="center" class="titulo">OPERADOR</p></td></tr>
        <tr>
          <td align="center" class="textoblack" colspan="4">&nbsp;&nbsp; {{ $operador }}</td>
        </tr>
        <tr>
          <td align="center" class="textoblack" colspan="4">&nbsp;&nbsp; {{ $area_operador }}</td>
        </tr>
        <tr><td colspan="4" valign="top"><div id="scroll_requi"></div></td></tr>
    </table>

</body>
</html>
