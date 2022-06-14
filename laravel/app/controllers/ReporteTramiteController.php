<?php
class ReporteTramiteController extends BaseController
{

    private $archivos = array();
    public function postTramiteunico()
    {
      $array=array();
      $fecha='';
      $array['where']='';
      $array['having']='';
      
      if( Input::has('tramite') AND Input::get('tramite')!='' ){
        $tramite=explode(" ",trim(Input::get('tramite')));
        for($i=0; $i<count($tramite); $i++){
          $array['where'].=" AND re.referido LIKE '%".$tramite[$i]."%' ";
        }
      }

      if( Input::has('solicitante_anu') AND Input::get('solicitante_anu')!='' ){
        $solicitante_anu=explode(" ",trim(Input::get('solicitante_anu')));
        for($i=0; $i<count($solicitante_anu); $i++){
            if( $i == 0 ){
                $array['having'].=" HAVING persona LIKE '%".$solicitante_anu[$i]."%' ";
            }
            else{
                $array['having'].=" AND persona LIKE '%".$solicitante_anu[$i]."%' ";
            }
        }
      }

      if( Input::has('estado_anu') AND Input::get('estado_anu')!='' ){
          $estado_anu = trim(Input::get('estado_anu'));
          if( $array['having'] == '' ){
              $array['having'].=" HAVING estado = '".$estado_anu."' ";
          }
          else{
              $array['having'].=" AND estado = '".$estado_anu."' ";
          }
      }

      if( Input::has('anulado') AND Input::has('fecha_anu') AND Input::get('fecha_anu')!= '' ){
        $fecha_anu = explode(" - ", trim(Input::get('fecha_anu')));
        $array['where'].= " AND DATE(tm.updated_at) BETWEEN '".$fecha_anu[0]."' AND '".$fecha_anu[1]."' ";
      }

      if( Input::has('fecha_inicio_anu') AND Input::get('fecha_inicio_anu')!= '' ){
        $fecha_inicio_anu = explode(" - ", trim(Input::get('fecha_inicio_anu')));
        $array['where'].= " AND DATE(r.fecha_inicio) BETWEEN '".$fecha_inicio_anu[0]."' AND '".$fecha_inicio_anu[1]."' ";
      }

      if( Input::has('local_anu') AND Input::get('local_anu')!= '' ){
        $local_anu = implode(",", Input::get('local_anu'));
        $array['where'].= " AND FIND_IN_SET(r.local_id, '".$local_anu."') > 0 ";
      }
      

      if( !Input::has('anulado') ){
          $r = ReporteTramite::TramiteUnico( $array );
      }
      else{
        $r = ReporteTramite::TramiteAnulado( $array );
      }


      return Response::json(
          array(
              'rst'=>1,
              'datos'=>$r
          )
      );
    }

    public function postTramitedetalle()
    {
      $array=array();
      $array['where']='';

      if( Input::has('ruta_id') AND Input::get('ruta_id')!='' ){
        $array['where'].=" AND r.id=".Input::get('ruta_id')." ";
      }

      if( !Input::has('anulado') ){
        $array['where'].=" AND r.estado=1 ";
      }
      else{
        $array['where'].=" AND r.estado=0 ";
      }
      $r = ReporteTramite::TramiteDetalle( $array );
      
      return Response::json(
          array(
              'rst'=>1,
              'datos'=>$r
          )
      );
    }

    public function postExpedienteunico(){
      $array=array();
      $array['where']='';
      
      if( !Input::has('anulado') ){
        $array['where'].=" AND r.estado=1 ";
      }
      else{
        $array['where'].=" AND r.estado=0 ";
      }

        $rst=ReporteTramite::ExpedienteUnico(); 
        //$times = array();
        
        /*foreach ($rst[1] as $ind => $ndc){
            //$this->addVideoLink($rst[$ind]->referido);

            if($ndc->doc_digital_id != null){
              $rst[$ind]->referido .= ' <a target="_blank" href="doc_digital/'.$ndc->doc_digital_id.'"><span class="btn btn-default btn-sm" title="Ver documento"><i class="fa fa-eye"></i></span></a> ';
            }
        }*/

        

        return Response::json(
            array(
                'rst'=>1,
                'datos'=>$rst, 
                //'tiempos'=>$time,
            )
        );
    }


    function addVideoLink(&$reference){
        $ad=explode(" - ", $reference);
        if(isset($ad[1])){
            $nom = str_replace(' - ', '%', trim($ad[0]));
            $num = (int)preg_replace("/[^0-9]/", "", trim($ad[1]));
            $anio = trim($ad[2]);
            $gr = trim($ad[3]);
            $strFind = '%'.$nom.'%'.$num.'%'.$anio.'%'.$gr.'%';
            $r = FtpFiles::getVideoLink($strFind);
            if(count($r)>0){
                foreach ($r as $index => $obj) {
                    $v0 = substr($obj->link, 0,strrpos($obj->link, "/")+1);
                    $v1 = substr($obj->link, strrpos($obj->link, "/")+1);

                    $vidName = $v0.rawurlencode($v1);
                    $vidName = str_replace("A?O", 'A%D1O', $vidName);
                    $vidName = str_replace("%3F%20", '%BA%20', $vidName);
                    $reference .= ' <b><a class="btn btn-info btn-sm" href="javascript:window.open(atob(\''.base64_encode( $vidName ).'\'));"> <i class="fa fa-film"></i></a></b>';
                }
            }
        }
    }

    public function postValidasolicitudes()
    {
      $array=array();
      $array['where']='';
      $array['having']='';
      
      if( Input::has('tramite') AND Input::get('tramite')!='' ){
        $tramite=explode(" ",trim(Input::get('tramite')));
        for($i=0; $i<count($tramite); $i++){
          $array['where'].=" AND tr.id_union LIKE '%".$tramite[$i]."%' ";
        }
      }

      if( Input::has('solicitante') AND Input::get('solicitante')!='' ){
        $solicitante=explode(" ",trim(Input::get('solicitante')));
        for($i=0; $i<count($solicitante); $i++){
            if( $i == 0 ){
                $array['having'].=" HAVING solicitante LIKE '%".$solicitante[$i]."%' ";
            }
            else{
                $array['having'].=" AND solicitante LIKE '%".$solicitante[$i]."%' ";
            }
        }
      }

      if( Input::has('estado') AND Input::get('estado')!= '' ){
        $estado = implode(",", Input::get('estado'));
        $array['where'].= " AND FIND_IN_SET(pt.estado_atencion, '".$estado."') > 0 ";
      }

      if( Input::has('fecha_estado') AND Input::get('fecha_estado')!= '' ){
        $fecha_estado = explode(" - ", trim(Input::get('fecha_estado')));
        $array['where'].= " AND DATE(pt.updated_at) BETWEEN '".$fecha_estado[0]."' AND '".$fecha_estado[1]."' ";
      }

      if( Input::has('local') AND Input::get('local')!= '' ){
        $local = implode(",", Input::get('local'));
        $array['where'].= " AND FIND_IN_SET(pt.local_id, '".$local."') > 0 ";
      }

      $r = ReporteTramite::ValidaSolicitudes( $array );

      return Response::json(
          array(
              'rst'=>1,
              'datos'=>$r
          )
      );
    }

    public function getExportvalidasolicitudes()
    {
      $array=array();
      $array['where']='';
      $array['having']='';
      
      if( Input::has('tramite') AND Input::get('tramite')!='' ){
        $tramite=explode(" ",trim(Input::get('tramite')));
        for($i=0; $i<count($tramite); $i++){
          $array['where'].=" AND tr.id_union LIKE '%".$tramite[$i]."%' ";
        }
      }

      if( Input::has('solicitante') AND Input::get('solicitante')!='' ){
        $solicitante=explode(" ",trim(Input::get('solicitante')));
        for($i=0; $i<count($solicitante); $i++){
            if( $i == 0 ){
                $array['having'].=" HAVING solicitante LIKE '%".$solicitante[$i]."%' ";
            }
            else{
                $array['having'].=" AND solicitante LIKE '%".$solicitante[$i]."%' ";
            }
        }
      }

      if( Input::has('estado') AND Input::get('estado')!= '' ){
        $estado = implode(",", Input::get('estado'));
        $array['where'].= " AND FIND_IN_SET(pt.estado_atencion, '".$estado."') > 0 ";
      }

      if( Input::has('fecha_estado') AND Input::get('fecha_estado')!= '' ){
        $fecha_estado = explode(" - ", trim(Input::get('fecha_estado')));
        $array['where'].= " AND DATE(pt.updated_at) BETWEEN '".$fecha_estado[0]."' AND '".$fecha_estado[1]."' ";
      }

      if( Input::has('local') AND Input::get('local')!= '' ){
        $local = implode(",", Input::get('local'));
        $array['where'].= " AND FIND_IN_SET(pt.local_id, '".$local."') > 0 ";
      }

      $result = ReporteTramite::ValidaSolicitudes( $array );

      /*style*/
      $styleThinBlackBorderAllborders = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('argb' => 'FF000000'),
              ),
          ),
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          )
      );
      $styleThinAllborders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        )
      );
      $styleAlignmentBold= array(
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      $styleAlignment= array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      /*end style*/

      /*export*/
      /* instanciar phpExcel!*/              
      $objPHPExcel = new PHPExcel();

      /*configure*/
      $objPHPExcel->getProperties()->setCreator("Sistemas")
          ->setSubject("-");

      $objPHPExcel->getDefaultStyle()->getFont()->setName('Bookman Old Style');
      $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
      /*end configure*/

      /*head*/
      $head=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($head[0].'3', 'Tipo solicitante')
                  ->setCellValue($head[1].'3', 'Solicitante')
                  ->setCellValue($head[2].'3', 'Tipo de servicio solicitado')
                  ->setCellValue($head[3].'3', 'Documento presentado')
                  ->setCellValue($head[4].'3', 'Lugar de procedencia')
                  ->setCellValue($head[5].'3', 'Nombre del servicio solicitado')
                  ->setCellValue($head[6].'3', 'Fecha registrada')
                  ->setCellValue($head[7].'3', 'Requisitos PDF')
                  ->setCellValue($head[8].'3', 'Expedientes generados')
                  ->setCellValue($head[9].'3', 'Estado del servicio')
                  ->setCellValue($head[10].'3', 'Fecha del estado')
                  ->setCellValue($head[11].'3', 'Observaciones')
                  ->setCellValue($head[12].'3', 'Nro de expediente')

                  ->mergeCells('A1:M1')
                  ->setCellValue('A1', 'REPORTE DE PRODUCCIÓN DE VALIDACIÓN DE SOLICITUDES')
                  ->getStyle('A1:M1')->getFont()->setSize(18);

      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setAutoSize(true);
      /*end head*/
      /*body*/
              
      $cabecera=array();
      $max = 12;
      $ini = 4;
      $array_buscar = array('<b>','</b>','<br>','<hr>');
      $array_reemplazar = array('','',' => ','|');
      if($result){
        foreach ($result as $key => $value) {
          $archivo = "NO";
          if( trim( $value->ruta_archivo ) != "" ){
            $archivo = "SI";
          }
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue( $head[0] . $ini, $value->tipo_solicitante)
                      ->setCellValue( $head[1] . $ini, $value->solicitante)
                      ->setCellValue( $head[2] . $ini, $value->tipo_tramite)
                      ->setCellValue( $head[3] . $ini, $value->documento)
                      ->setCellValue( $head[4] . $ini, $value->local)
                      ->setCellValue( $head[5] . $ini, $value->servicio)
                      ->setCellValue( $head[6] . $ini, $value->fecha)
                      ->setCellValue( $head[7] . $ini, $archivo)
                      ->setCellValue( $head[8] . $ini, str_replace($array_buscar, $array_reemplazar, $value->expediente))
                      ->setCellValue( $head[9] . $ini, $value->estado)
                      ->setCellValue( $head[10] . $ini, $value->updated_at)
                      ->setCellValue( $head[11] . $ini, $value->observacion)
                      ->setCellValue( $head[12] . $ini, $value->tramite)
          ;
          $ini++;
        }
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($head[8])->setAutoSize(true);
      }
      /*end body*/
      $objPHPExcel->getActiveSheet()->getStyle('A3:'.$head[$max].'3')->applyFromArray($styleThinBlackBorderAllborders);
      $objPHPExcel->getActiveSheet()->getStyle('A4:'.$head[$max].($ini-1))->applyFromArray($styleThinAllborders);
      
      $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleAlignment);
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Valida');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="reportebca.xls"'); // file name of excel
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');
      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      $objWriter->save('php://output');
      exit;
      /* end export*/
    }

    public function getExportvalidasolicitudesproduccion()
    {
      $array=array();
      $array['where']='';
      $array['having']='';
      
      /*if( Input::has('tramite') AND Input::get('tramite')!='' ){
        $tramite=explode(" ",trim(Input::get('tramite')));
        for($i=0; $i<count($tramite); $i++){
          $array['where'].=" AND tr.id_union LIKE '%".$tramite[$i]."%' ";
        }
      }

      if( Input::has('solicitante') AND Input::get('solicitante')!='' ){
        $solicitante=explode(" ",trim(Input::get('solicitante')));
        for($i=0; $i<count($solicitante); $i++){
            if( $i == 0 ){
                $array['having'].=" HAVING solicitante LIKE '%".$solicitante[$i]."%' ";
            }
            else{
                $array['having'].=" AND solicitante LIKE '%".$solicitante[$i]."%' ";
            }
        }
      }*/

      if( Input::has('estado') AND Input::get('estado')!= '' ){
        $estado = implode(",", Input::get('estado'));
        $array['where'].= " AND FIND_IN_SET(pt.estado_atencion, '".$estado."') > 0 ";
      }

      if( Input::has('fecha_estado') AND Input::get('fecha_estado')!= '' ){
        $fecha_estado = explode(" - ", trim(Input::get('fecha_estado')));
        $array['where'].= " AND DATE(pt.updated_at) BETWEEN '".$fecha_estado[0]."' AND '".$fecha_estado[1]."' ";
      }

      if( Input::has('local') AND Input::get('local')!= '' ){
        $local = implode(",", Input::get('local'));
        $array['where'].= " AND FIND_IN_SET(pt.local_id, '".$local."') > 0 ";
      }

      $result = ReporteTramite::ValidaSolicitudesProduccion( $array );

      /*style*/
      $styleThinBlackBorderAllborders = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('argb' => 'FF000000'),
              ),
          ),
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          )
      );
      $styleThinAllborders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        )
      );
      $styleAlignmentBold= array(
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      $styleAlignment= array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      /*end style*/

      /*export*/
      /* instanciar phpExcel!*/              
      $objPHPExcel = new PHPExcel();

      /*configure*/
      $objPHPExcel->getProperties()->setCreator("Sistemas")
          ->setSubject("-");

      $objPHPExcel->getDefaultStyle()->getFont()->setName('Bookman Old Style');
      $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
      /*end configure*/

      /*head*/
      $head=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($head[0].'5', 'Lugar de procedencia')
                  ->setCellValue($head[1].'5', 'Nro Solicitudes')
                  ->setCellValue($head[2].'5', 'Pendientes')
                  ->setCellValue($head[3].'5', 'Aprobados')
                  ->setCellValue($head[4].'5', 'Desaprobados')

                  ->mergeCells('A1:M1')
                  ->setCellValue('A1', 'REPORTE DE PRODUCCIÓN DE VALIDACIÓN DE SOLICITUDES')
                  ->getStyle('A1:M1')->getFont()->setSize(18);

      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
      /*end head*/
      /*body*/

      $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue( 'A3', 'Fechas')
                    ->setCellValue( 'B3', trim(Input::get('fecha_estado')))
                    ->mergeCells('B3:D3')
                    ->getStyle('A3:D3')->getFont()->setSize(12);
              
      $cabecera=array();
      $max = 4;
      $ini = 6;
      if($result){
        foreach ($result as $key => $value) {
          if( trim($value->local) == '' ){
            $value->local = 'Sin Local';
          }
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue( $head[0] . $ini, $value->local)
                      ->setCellValue( $head[1] . $ini, $value->solicitudes)
                      ->setCellValue( $head[2] . $ini, $value->pendientes)
                      ->setCellValue( $head[3] . $ini, $value->aprobados)
                      ->setCellValue( $head[4] . $ini, $value->desaprobados)
          ;
          $ini++;
        }
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($head[8])->setAutoSize(true);
      }
      $objPHPExcel->getActiveSheet()->getStyle('A5:'.$head[$max].'5')->applyFromArray($styleAlignmentBold);
      /*end body*/
      $objPHPExcel->getActiveSheet()->getStyle('A6:'.$head[$max].'3')->applyFromArray($styleThinBlackBorderAllborders);
      $objPHPExcel->getActiveSheet()->getStyle('A5:'.$head[$max].($ini-1))->applyFromArray($styleThinAllborders);
      $objPHPExcel->getActiveSheet()->getStyle('B6:'.$head[$max].($ini-1))->applyFromArray($styleAlignment);
      
      $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($styleAlignment);
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Valida - Producción');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="reportebca.xls"'); // file name of excel
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');
      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      $objWriter->save('php://output');
      exit;
      /* end export*/
    }

    public function postProduccionexpedientes()
    {
      $array=array();
      $array['where']='';
      $array['having']='';
      
      if( Input::has('flujo') AND Input::get('flujo')!= '' ){
        $flujo = implode(",", Input::get('flujo'));
        $array['where'].= " AND FIND_IN_SET(r.flujo_id, '".$flujo."') > 0 ";
      }

      if( Input::has('fecha_documento') AND Input::get('fecha_documento')!= '' ){
        $fecha_documento = explode(" - ", trim(Input::get('fecha_documento')));
        $array['where'].= " AND DATE(rdv.updated_at) BETWEEN '".$fecha_documento[0]."' AND '".$fecha_documento[1]."' ";
      }

      if( Input::has('local') AND Input::get('local')!= '' ){
        $local = implode(",", Input::get('local'));
        $array['where'].= " AND FIND_IN_SET(r.local_id, '".$local."') > 0 ";
      }

      if( Input::has('area') AND Input::get('area')!= '' ){
        $area = implode(",", Input::get('area'));
        $array['where'].= " AND FIND_IN_SET(rd.area_id, '".$area."') > 0 ";
      }

      if( Input::has('documento') AND Input::get('documento')!= '' ){
        $documento = implode(",", Input::get('documento'));
        $array['where'].= " AND FIND_IN_SET(rdv.documento_id, '".$documento."') > 0 ";
      }

      $r = ReporteTramite::ProduccionExpedientes( $array );

      return Response::json(
          array(
              'rst'=>1,
              'datos'=>$r
          )
      );
    }

    public function getExportproduccionexpedientes()
    {
      $array=array();
      $array['where']='';
      $array['having']='';
      
      if( Input::has('flujo') AND Input::get('flujo')!= '' ){
        $flujo = implode(",", Input::get('flujo'));
        $array['where'].= " AND FIND_IN_SET(r.flujo_id, '".$flujo."') > 0 ";
      }

      if( Input::has('fecha_documento') AND Input::get('fecha_documento')!= '' ){
        $fecha_documento = explode(" - ", trim(Input::get('fecha_documento')));
        $array['where'].= " AND DATE(rdv.updated_at) BETWEEN '".$fecha_documento[0]."' AND '".$fecha_documento[1]."' ";
      }

      if( Input::has('local') AND Input::get('local')!= '' ){
        $local = implode(",", Input::get('local'));
        $array['where'].= " AND FIND_IN_SET(r.local_id, '".$local."') > 0 ";
      }

      if( Input::has('area') AND Input::get('area')!= '' ){
        $area = implode(",", Input::get('area'));
        $array['where'].= " AND FIND_IN_SET(rd.area_id, '".$area."') > 0 ";
      }

      if( Input::has('documento') AND Input::get('documento')!= '' ){
        $documento = implode(",", Input::get('documento'));
        $array['where'].= " AND FIND_IN_SET(rdv.documento_id, '".$documento."') > 0 ";
      }

      $result = ReporteTramite::ProduccionExpedientes( $array );

      /*style*/
      $styleThinBlackBorderAllborders = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('argb' => 'FF000000'),
              ),
          ),
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          )
      );
      $styleThinAllborders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        )
      );
      $styleAlignmentBold= array(
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      $styleAlignment= array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      /*end style*/

      /*export*/
      /* instanciar phpExcel!*/              
      $objPHPExcel = new PHPExcel();

      /*configure*/
      $objPHPExcel->getProperties()->setCreator("Sistemas")
          ->setSubject("-");

      $objPHPExcel->getDefaultStyle()->getFont()->setName('Bookman Old Style');
      $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
      /*end configure*/

      /*head*/
      $head=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($head[0].'3', 'Lugar de procedencia')
                  ->setCellValue($head[1].'3', 'Área')
                  ->setCellValue($head[2].'3', 'Documento')
                  ->setCellValue($head[3].'3', 'Proceso')
                  ->setCellValue($head[4].'3', 'Nro Documentos')
                  ->setCellValue($head[5].'3', 'Nro Trámites')

                  ->mergeCells('A1:G1')
                  ->setCellValue('A1', 'REPORTE DE PRODUCCIÓN DE GESTIÓN DE EXPEDIENTES')
                  ->getStyle('A1:G1')->getFont()->setSize(18);

      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
      /*end head*/
      /*body*/
              
      $cabecera=array();
      $max = 5;
      $ini = 4;
      if($result){
        foreach ($result as $key => $value) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue( $head[0] . $ini, $value->local)
                      ->setCellValue( $head[1] . $ini, $value->area)
                      ->setCellValue( $head[2] . $ini, $value->documento)
                      ->setCellValue( $head[3] . $ini, $value->proceso)
                      ->setCellValue( $head[4] . $ini, $value->docs)
                      ->setCellValue( $head[5] . $ini, $value->tramites)
          ;
          $ini++;
        }
      }
      /*end body*/
      $objPHPExcel->getActiveSheet()->getStyle('A3:'.$head[$max].'3')->applyFromArray($styleThinBlackBorderAllborders);
      $objPHPExcel->getActiveSheet()->getStyle('A4:'.$head[$max].($ini-1))->applyFromArray($styleThinAllborders);
      
      $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleAlignment);
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Valida');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="reportebca.xls"'); // file name of excel
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');
      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      $objWriter->save('php://output');
      exit;
      /* end export*/
    }

    public function getExportproduccionexpedienteslocal()
    {
      $array=array();
      $array['where']='';
      $array['having']='';
      
      if( Input::has('flujo') AND Input::get('flujo')!= '' ){
        $flujo = implode(",", Input::get('flujo'));
        $array['where'].= " AND FIND_IN_SET(r.flujo_id, '".$flujo."') > 0 ";
      }

      if( Input::has('fecha_documento') AND Input::get('fecha_documento')!= '' ){
        $fecha_documento = explode(" - ", trim(Input::get('fecha_documento')));
        $array['where'].= " AND DATE(rdv.updated_at) BETWEEN '".$fecha_documento[0]."' AND '".$fecha_documento[1]."' ";
      }

      if( Input::has('local') AND Input::get('local')!= '' ){
        $local = implode(",", Input::get('local'));
        $array['where'].= " AND FIND_IN_SET(r.local_id, '".$local."') > 0 ";
      }

      if( Input::has('area') AND Input::get('area')!= '' ){
        $area = implode(",", Input::get('area'));
        $array['where'].= " AND FIND_IN_SET(rd.area_id, '".$area."') > 0 ";
      }

      if( Input::has('documento') AND Input::get('documento')!= '' ){
        $documento = implode(",", Input::get('documento'));
        $array['where'].= " AND FIND_IN_SET(rdv.documento_id, '".$documento."') > 0 ";
      }

      $result = ReporteTramite::ProduccionExpedientesLocal( $array );

      /*style*/
      $styleThinBlackBorderAllborders = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('argb' => 'FF000000'),
              ),
          ),
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          )
      );
      $styleThinAllborders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        )
      );
      $styleAlignmentBold= array(
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      $styleAlignment= array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      /*end style*/

      /*export*/
      /* instanciar phpExcel!*/              
      $objPHPExcel = new PHPExcel();

      /*configure*/
      $objPHPExcel->getProperties()->setCreator("Sistemas")
          ->setSubject("-");

      $objPHPExcel->getDefaultStyle()->getFont()->setName('Bookman Old Style');
      $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
      /*end configure*/

      /*head*/
      $head=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($head[0].'3', 'Lugar de procedencia')
                  ->setCellValue($head[1].'3', 'Nro Documentos')
                  ->mergeCells('A1:C1')
                  ->setCellValue('A1', 'TOTAL POR LOCAL')

                  ->setCellValue($head[4].'3', 'Área')
                  ->setCellValue($head[5].'3', 'Nro Documentos')
                  ->mergeCells('E1:G1')
                  ->setCellValue('E1', 'TOTAL POR ÁREA')
                  ->getStyle('A1:G1')->getFont()->setSize(18);

      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);

      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
      /*end head*/
      /*body*/
              
      if($result){
        $max = 1;
        $ini = 4;
        foreach ($result[0] as $key => $value) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue( $head[0] . $ini, $value->local)
                      ->setCellValue( $head[1] . $ini, $value->docs)
          ;
          $ini++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$head[$max].'3')->applyFromArray($styleThinBlackBorderAllborders);
        $objPHPExcel->getActiveSheet()->getStyle('A4:'.$head[$max].($ini-1))->applyFromArray($styleThinAllborders);

        $max = 5;
        $ini = 4;
        foreach ($result[1] as $key => $value) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue( $head[4] . $ini, $value->area)
                      ->setCellValue( $head[5] . $ini, $value->docs)
          ;
          $ini++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('E3:'.$head[$max].'3')->applyFromArray($styleThinBlackBorderAllborders);
        $objPHPExcel->getActiveSheet()->getStyle('E4:'.$head[$max].($ini-1))->applyFromArray($styleThinAllborders);
      }
      /*end body*/
      
      
      $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleAlignment);
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Totales Local - Área');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="reportebca.xls"'); // file name of excel
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');
      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      $objWriter->save('php://output');
      exit;
      /* end export*/
    }

    public function getExportproduccionexpedientesestado()
    {
      $array=array();
      $array['where']='';
      $array['having']='';
      
      if( Input::has('flujo') AND Input::get('flujo')!= '' ){
        $flujo = implode(",", Input::get('flujo'));
        $array['where'].= " AND FIND_IN_SET(r.flujo_id, '".$flujo."') > 0 ";
      }

      if( Input::has('fecha_documento') AND Input::get('fecha_documento')!= '' ){
        $fecha_documento = explode(" - ", trim(Input::get('fecha_documento')));
        $array['where'].= " AND DATE(rdv.updated_at) BETWEEN '".$fecha_documento[0]."' AND '".$fecha_documento[1]."' ";
      }

      if( Input::has('local') AND Input::get('local')!= '' ){
        $local = implode(",", Input::get('local'));
        $array['where'].= " AND FIND_IN_SET(r.local_id, '".$local."') > 0 ";
      }

      if( Input::has('area') AND Input::get('area')!= '' ){
        $area = implode(",", Input::get('area'));
        $array['where'].= " AND FIND_IN_SET(rd.area_id, '".$area."') > 0 ";
      }

      if( Input::has('documento') AND Input::get('documento')!= '' ){
        $documento = implode(",", Input::get('documento'));
        $array['where'].= " AND FIND_IN_SET(rdv.documento_id, '".$documento."') > 0 ";
      }

      $result = ReporteTramite::ProduccionExpedientesEstado( $array );

      /*style*/
      $styleThinBlackBorderAllborders = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN,
                  'color' => array('argb' => 'FF000000'),
              ),
          ),
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          )
      );
      $styleThinAllborders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            ),
        )
      );
      $styleAlignmentBold= array(
          'font'    => array(
              'bold'      => true
          ),
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      $styleAlignment= array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          ),
      );
      /*end style*/

      /*export*/
      /* instanciar phpExcel!*/              
      $objPHPExcel = new PHPExcel();

      /*configure*/
      $objPHPExcel->getProperties()->setCreator("Sistemas")
          ->setSubject("-");

      $objPHPExcel->getDefaultStyle()->getFont()->setName('Bookman Old Style');
      $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
      /*end configure*/

      /*head*/
      $head=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');
      $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue($head[0].'3', 'Lugar de procedencia')
                  ->setCellValue($head[1].'3', 'Nro Expedientes')
                  ->setCellValue($head[2].'3', 'Anulados')
                  ->setCellValue($head[3].'3', 'En Proceso')
                  ->setCellValue($head[4].'3', 'Concluidos')
                  ->mergeCells('A1:E1')
                  ->setCellValue('A1', 'TOTAL PRODUCCIÓN POR ESTADOS')
                  ->getStyle('A1:E1')->getFont()->setSize(18);

      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
      /*end head*/
      /*body*/
              
      if($result){
        $max = 4;
        $ini = 4;
        foreach ($result as $key => $value) {
          $objPHPExcel->setActiveSheetIndex(0)
                      ->setCellValue( $head[0] . $ini, $value->local)
                      ->setCellValue( $head[1] . $ini, $value->tramites)
                      ->setCellValue( $head[2] . $ini, $value->anulados)
                      ->setCellValue( $head[3] . $ini, $value->procesos)
                      ->setCellValue( $head[4] . $ini, ($value->tramites - $value->anulados - $value->procesos))
          ;
          $ini++;
        }

        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$head[$max].'3')->applyFromArray($styleThinBlackBorderAllborders);
        $objPHPExcel->getActiveSheet()->getStyle('A4:'.$head[$max].($ini-1))->applyFromArray($styleThinAllborders);
      }
      /*end body*/
      
      
      $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleAlignment);
      // Rename worksheet
      $objPHPExcel->getActiveSheet()->setTitle('Producción Estados');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="reportebca.xls"'); // file name of excel
      header('Cache-Control: max-age=0');
      // If you're serving to IE 9, then the following may be needed
      header('Cache-Control: max-age=1');
      // If you're serving to IE over SSL, then the following may be needed
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
      header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
      header ('Pragma: public'); // HTTP/1.0
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
      $objWriter->save('php://output');
      exit;
      /* end export*/
    }

}
