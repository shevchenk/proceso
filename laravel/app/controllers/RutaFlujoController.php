<?php
class RutaFlujoController extends \BaseController
{
   /*     public function postCargar()
    {
        if ( Request::ajax() ) {
            $rf             = new RutaFlujo();
            $cargar         = Array();
            $cargar         = $rf->getRutaFlujo();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $cargar
                )
            );
        }
    }
*/


 public function exportExcel($propiedades,$estilos,$cabecera,$data){
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

      $head=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');

      /*instanciar phpExcel*/            
      $objPHPExcel = new PHPExcel();
      /*end instanciar phpExcel*/

      /*configure*/
      $objPHPExcel->getProperties()->setCreator($propiedades['creador'])
                                  ->setSubject($propiedades['subject']);

      $objPHPExcel->getDefaultStyle()->getFont()->setName($propiedades['font-name']);
      $objPHPExcel->getDefaultStyle()->getFont()->setSize($propiedades['font-size']);
      $objPHPExcel->getActiveSheet()->setTitle($propiedades['tittle']);
      /*end configure*/

      /*set up structure*/
      array_unshift($data,(object) $cabecera);
      foreach($data as $key => $value){
        $cont = 0;

        if($key == 0){ // set style to header
          end($value);       
          $objPHPExcel->getActiveSheet()->getStyle('A1:'.$head[key($value)].'1')->applyFromArray($styleThinBlackBorderAllborders);
        }

        foreach($value as $index => $val){
          $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($head[$cont])->setAutoSize(true);
            
          if($index == 'norden' && $key > 0){ //set orden in excel
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($head[$cont].($key + 1), $key);                
          }else{ //poblate info
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($head[$cont].($key + 1), $val);
          }

          $cont++;
        }          
      }
      /*end set up structure*/

      $objPHPExcel->setActiveSheetIndex(0);
      // Redirect output to a client’s web browser (Excel5)
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="reporte.xls"'); // file name of excel
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
    }

    

public function postCargar()
    {
        if ( Request::ajax() ) {
            /*********************FIJO*****************************/
            $array=array();
            $array['where']='';$array['usuario']=Auth::user()->id;
            $array['limit']='';$array['order']='';$array['groupby']='';
            $array['inner']='';
            
            if (Input::has('draw')) {
                if (Input::has('order')) {
                    $inorder=Input::get('order');
                    $incolumns=Input::get('columns');
                    $array['order']=  ' ORDER BY '.
                                      $incolumns[ $inorder[0]['column'] ]['name'].' '.
                                      $inorder[0]['dir'];
                }

                $array['limit']=' LIMIT '.Input::get('start').','.Input::get('length');
                $aParametro["draw"]=Input::get('draw');
            }
            /************************************************************/
            $array['where']="  AND rf.area_id IN (
                                        SELECT a.id
                                        FROM area_cargo_persona acp
                                        INNER JOIN areas a ON a.id = acp.area_id AND a.estado = 1
                                        INNER JOIN cargo_persona cp ON cp.id = acp.cargo_persona_id AND cp.estado = 1
                                        WHERE acp.estado = 1 AND cp.persona_id = ".$array['usuario']."
                                    ) ";
            if( Input::has('vista') AND Input::get("vista")==1 ){
                $array['groupby']=" GROUP BY rf.id ";
                $array['inner']=" INNER JOIN rutas_flujo_detalle rfd ON rfd.ruta_flujo_id=rf.id ";
                $array['where']="  AND rfd.area_id IN (
                                            SELECT a.id
                                            FROM area_cargo_persona acp
                                            INNER JOIN areas a ON a.id = acp.area_id AND a.estado = 1
                                            INNER JOIN cargo_persona cp ON cp.id = acp.cargo_persona_id AND cp.estado = 1
                                            WHERE acp.estado = 1 AND cp.persona_id = ".$array['usuario']."
                                        ) ";
            }

            if( Input::has('totalruta') AND Input::get("totalruta")==1 ){
                $array['groupby']='';
                $array['inner']='';
                $array['where']='';
            }

            if( Input::has("flujo") ){
                $flujo=Input::get("flujo");
                if( trim( $flujo )!='' ){
                    $array['where'].=" AND f.nombre LIKE '%".$flujo."%' ";
                }
            }


            if( Input::has("area") ){
                $area=Input::get("area");
                if( trim( $area )!='' ){
                    $array['where'].=" AND a.nombre LIKE '%".$area."%' ";
                }
            }

            if( Input::has("fruta") ){
                $fruta=explode(" - ",Input::get("fruta"));
                if( count( $fruta )>1 ){
                    $array['where'].=" AND DATE(rf.created_at) BETWEEN '".$fruta[0]."' AND '".$fruta[1]."' ";
                }
            }

            if( Input::has("estado") ){
                $estado=Input::get("estado");
                if( trim( $estado )!='' ){
                    $array['where'].=" AND rf.estado='".$estado."' ";
                }
            }
            else{
                $array['where'].=" AND rf.estado IN (1,2) ";
            }

            if( Input::has("tipo_flujo") ){
                $tipo_flujo=Input::get("tipo_flujo");
                if( trim( $tipo_flujo )!='' ){
                    $array['where'].=" AND f.tipo_flujo='".$tipo_flujo."' ";
                }
            }

           // $array['order']=" ORDER BY rf.nombre ";

            $cant  = RutaFlujo::getCargarCount( $array );
            $aData = RutaFlujo::getCargar( $array );

            $aParametro['rst'] = 1;
            $aParametro["recordsTotal"]=$cant;
            $aParametro["recordsFiltered"]=$cant;
            $aParametro['data'] = $aData;
            $aParametro['msj'] = "No hay registros aún";
            return Response::json($aParametro);

        }
    }

    public function postRegistrar()
    {
        if ( Request::ajax() ) {
            DB::beginTransaction();
            $rutaFlujo="";
            $mensajefinal=".::Se registro correctamente::.";
            $modificap=false;
            if ( Input::get('ruta_flujo_id') ) {
                $modificap=true;
                $mensajefinal=".::Actualización finalizada::.";
                $rutaFlujo = RutaFlujo::find( Input::get('ruta_flujo_id') );
                $rutaFlujo['usuario_updated_at']= Auth::user()->id;

                $rutaFlujo['nactualizar']=$rutaFlujo->nactualizar*1+1;
            }
            else{
                $rutaFlujo = new RutaFlujo;
                $rutaFlujo['usuario_created_at']= Auth::user()->id;
                $rutaFlujo['estado']= 2;
                $rutaFlujo['flujo_id']= Input::get('flujo_id');
                $rutaFlujo['persona_id']= Auth::user()->id;
                $rutaFlujo['area_id']= Input::get('area_id');
                $rutaFlujo['tipo_ruta']=Input::get('tipo_ruta');
            }

            

            $rutaFlujo->save();

            if( !Input::has('ruta_flujo_id') ){
                $flujoTipoRespuesta=new FlujoTipoRespuesta;
                $flujoTipoRespuesta['flujo_id']=Input::get('flujo_id');
                $flujoTipoRespuesta['tipo_respuesta_id']=3;
                $flujoTipoRespuesta['tiempo_id']=2;
                $flujoTipoRespuesta['dtiempo']=1;
                $flujoTipoRespuesta['usuario_created_at']=Auth::user()->id;
                $flujoTipoRespuesta->save();

                $flujoTipoRespuestaD=new FlujoTipoRespuesta;
                $flujoTipoRespuestaD['flujo_id']=Input::get('flujo_id');
                $flujoTipoRespuestaD['tipo_respuesta_id']=2;
                $flujoTipoRespuestaD['tiempo_id']=0;
                $flujoTipoRespuestaD['dtiempo']=0;
                $flujoTipoRespuesta['usuario_created_at']=Auth::user()->id;
                $flujoTipoRespuestaD->save();
            }

            /*Agregar Valores Auxiliares*/
            $auxflujo   = Flujo::find( Input::get('flujo_id') );
            $auxpersona = Persona::find( Auth::user()->id );
            $auxarea    = Area::find( Input::get('area_id') );
            /****************************/
            $estadoG= explode( "*", Input::get('estadoG') );
            $areasGid= explode( "*", Input::get('areasGId') );
            $theadArea= explode( "*", Input::get('theadArea') );
            $tbodyArea= explode( "*", Input::get('tbodyArea') );

            $tiempoGid= explode( "*", Input::get('tiempoGId') );
            $tiempoG= explode( "*", Input::get('tiempoG') );
            $verboG= explode( "*", Input::get('verboG') );

            $modificaG=Input::get('modificaG');

            $finalizar= DB::table('rutas_flujo_detalle')
                          ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                          ->where('norden', '>', count($areasGid))
                          ->where('estado', '=', 1)
                          ->update( array(
                                        'estado'=> 0,
                                        'usuario_updated_at'=> Auth::user()->id
                                    )
                            );

            for($i=0; $i<count($areasGid); $i++ ){
                $validapase=explode("*".$i."*",$modificaG);
                $valor=1;
                if ( Input::get('ruta_flujo_id') ) {
                    $valor= DB::table('rutas_flujo_detalle')
                                ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                ->where('norden', '=', ($i+1))
                                ->where('area_id', '=', $areasGid[$i] )
                                ->where('estado', '=', 1)
                                ->count();
                }
                
                if( $modificap==false || $valor==0 || ($modificap==true && count($validapase)>1) ){ //Validacion solo q actualice o genre cuando sea nuevo o permitido
                    $rutaFlujoDetalle="";
                    if ( Input::get('ruta_flujo_id') ) {
                        if($valor==0){
                            $rfd=DB::table('rutas_flujo_detalle')
                                ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                ->where('norden', '=', ($i+1))
                                ->where('estado', '=', 1)
                                ->update(array(
                                            'estado' => 0,
                                            'usuario_updated_at'=> Auth::user()->id
                                        )
                                );

                            $em= "  SELECT a.id,cp.persona_id,p.email,
                                    p.paterno,p.materno,p.nombre,a.nombre area
                                    FROM area_cargo_persona acp
                                    INNER JOIN areas a ON a.id=acp.area_id AND a.estado=1
                                    INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1
                                    INNER JOIN personas p ON p.id=cp.persona_id AND p.estado=1
                                    WHERE acp.estado=1
                                    AND cp.cargo_id=5
                                    AND IFNULL(p.modalidad,1)!=2
                                    AND acp.area_id=".$areasGid[$i]."
                                    ORDER BY cp.persona_id";
                            $qem= DB::select($em);
                            //echo $em;
                            if( count($qem)>0 ){
                                for($k=0;$k<count($qem);$k++){
                                $parametros=array(
                                    'paso'      => ($i+1),
                                    'persona'   => $qem[$k]->paterno.' '.$qem[$k]->materno.','.$qem[$k]->nombre,
                                    'area'      => $qem[$k]->area,
                                    'procesoe'  => $auxflujo->nombre,
                                    'personae'  => $auxpersona->paterno.' '.$auxpersona->materno.','.$auxpersona->nombre,
                                    'areae'     => $auxarea->nombre
                                );

                                    try{
                                        Mail::queue('emails', $parametros , 
                                            function($message) use ($qem,$k) {
                                            $message
                                                ->to($qem[$k]->email)
                                                ->subject('.::Se ha involucrado en nuevo proceso::.');
                                            }
                                        );
                                    }
                                    catch(Exception $e){
                                        //echo $qem[$k]->email."<br>";
                                    }
                                }
                            }

                            $rutaFlujoDetalle = new RutaFlujoDetalle;
                            $rutaFlujoDetalle['usuario_created_at']= Auth::user()->id;
                        }
                        else{
                            $rfd=DB::table('rutas_flujo_detalle')
                                ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                ->where('norden', '=', ($i+1))
                                ->where('estado', '=', 1)
                                ->first();
                            $rutaFlujoDetalle = RutaFlujoDetalle::find( $rfd->id );
                            $rutaFlujoDetalle['usuario_updated_at']= Auth::user()->id;
                        }
                        //$rutaFlujoDetalle
                    }
                    else{
                        $em= "  SELECT a.id,cp.persona_id,p.email,p.paterno,p.materno,p.nombre,a.nombre area
                                FROM area_cargo_persona acp
                                INNER JOIN areas a ON a.id=acp.area_id AND a.estado=1
                                INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1
                                INNER JOIN personas p ON p.id=cp.persona_id AND p.estado=1
                                WHERE acp.estado=1
                                AND cp.cargo_id=5
                                AND IFNULL(p.modalidad,1)!=2
                                AND acp.area_id=".$areasGid[$i]."
                                ORDER BY cp.persona_id";
                        $qem= DB::select($em);
                        //echo "2".$em;
                        if( count($qem)>0 ){
                            for($k=0;$k<count($qem);$k++){
                            $parametros=array(
                                'paso'      => ($i+1),
                                'persona'   => $qem[$k]->paterno.' '.$qem[$k]->materno.','.$qem[$k]->nombre,
                                'area'      => $qem[$k]->area,
                                'procesoe'  => $auxflujo->nombre,
                                'personae'  => $auxpersona->paterno.' '.$auxpersona->materno.','.$auxpersona->nombre,
                                'areae'     => $auxarea->nombre
                            );

                                try{
                                    Mail::queue('emails', $parametros , 
                                        function($message) use( $qem,$k ) {
                                        $message
                                            ->to($qem[$k]->email)
                                            ->subject('.::Se ha involucrado en nuevo proceso::.');
                                        }
                                    );
                                }
                                catch(Exception $e){
                                    //echo $qem[$k]->email."<br>";
                                }
                            }
                        }

                        $rutaFlujoDetalle = new RutaFlujoDetalle;
                        $rutaFlujoDetalle['usuario_created_at']= Auth::user()->id;
                    }
                    if(trim($estadoG[$i])==null or trim($estadoG[$i])=='' or trim($estadoG[$i])=='0'){
                        $estadoG[$i]=1;
                    }
                    $rutaFlujoDetalle['ruta_flujo_id']= $rutaFlujo->id;
                    $rutaFlujoDetalle['area_id']= $areasGid[$i];
                    $rutaFlujoDetalle['estado_ruta']= $estadoG[$i];
                    $rutaFlujoDetalle['norden']= ($i+1);

                    $post = array_search($areasGid[$i], $tiempoGid);

                    $posdetalleTiempoG= array("0","0");
                    // Inicializa valores en caso no tenga datos...
                    $rutaFlujoDetalle['tiempo_id']="1";
                    $rutaFlujoDetalle['dtiempo']="0";
                    $rutaFlujoDetalle['detalle']="";
                    $rutaFlujoDetalle['ruta_flujo_id2']=null;

                    if( trim($post)!='' and $post*1>=0 ){
                        $detalleTiempoG=explode( ",", $tiempoG[$post] );
                        
                        if( $theadArea[$i]=="0" ){
                            $posdetalleTiempoG= explode( "|", $tbodyArea[$i] );
                        }

                        $dtg="";

                        if( isset($detalleTiempoG[ $posdetalleTiempoG[1] ]) and trim($detalleTiempoG[ $posdetalleTiempoG[1] ])!=''){
                            $dtg=explode( "_", $detalleTiempoG[ $posdetalleTiempoG[1] ] );
                            if( trim($dtg[1])!='' ){
                                $rutaFlujoDetalle['tiempo_id']=$dtg[1];
                                $rutaFlujoDetalle['dtiempo']=$dtg[2];
                                $rutaFlujoDetalle['detalle']=$dtg[3];
                                if(isset($dtg[4])){
                                $rutaFlujoDetalle['ruta_flujo_id2']=$dtg[4];}
                            }
                        }

                    }

                    $rutaFlujoDetalle->save();

                    $cantrfd= DB::table('rutas_flujo_detalle_verbo')
                                ->where('ruta_flujo_detalle_id', '=', $rutaFlujoDetalle->id)
                                ->count();
                        $probando="";
                        $rfdv="";
                        if($cantrfd>0){
                            $rfdv=DB::table('rutas_flujo_detalle_verbo')
                                ->where('ruta_flujo_detalle_id', '=', $rutaFlujoDetalle->id)
                                ->where('estado', '=', 1)
                                ->update(array(
                                            'estado' => 0,
                                            'usuario_updated_at'=> Auth::user()->id
                                        )
                                );
                           $probando="editar";
                            
                        }
                        /*return Response::json(
                            array(
                                'rst'   => 1,
                                'msj'   => "Probando Ando",
                                'datos' => $probando,
                                'cantrfd' => $cantrfd,
                                'rfdv' => $rfdv,
                                'ruta_flujo_id'=>$rutaFlujo->id
                            )
                        );*/

                    // probando para los verbos
                    $posdetalleTiempoG= array("0","0");

                    if( trim($post)!='' and $post*1>=0 ){
                        $detalleTiempoG=explode( ",", $verboG[$post] );
                        
                        if( $theadArea[$i]=="0" ){
                            $posdetalleTiempoG= explode( "|", $tbodyArea[$i] );
                        }

                        $dtg="";

                        if( isset($detalleTiempoG[ $posdetalleTiempoG[1] ]) and trim($detalleTiempoG[ $posdetalleTiempoG[1] ])!=''){
                            $dtg=explode( "_", $detalleTiempoG[ $posdetalleTiempoG[1] ] );
                            //if( trim($dtg[1])!='' ){
                                $detdtg=explode("|",$dtg[1]);
                                $detdtg2=explode("|",$dtg[2]);
                                $detdtg3=explode("|",$dtg[3]);
                                $detdtg4=explode("|",$dtg[4]);
                                $detdtg5=explode("|",$dtg[5]);
                                $detdtg6=explode("|",$dtg[6]);

                                for($j=0;$j<count($detdtg);$j++){
                                    $rutaFlujoDetalleVerbo="";
                                    
                                    $rutaFlujoDetalleVerbo= new RutaFlujoDetalleVerbo;
                                    $rutaFlujoDetalleVerbo['usuario_created_at']= Auth::user()->id;
                                    $rutaFlujoDetalleVerbo['ruta_flujo_detalle_id']= $rutaFlujoDetalle->id;
                                    $rutaFlujoDetalleVerbo['nombre']=$detdtg[$j];
                                    $rutaFlujoDetalleVerbo['condicion']=$detdtg2[$j];
                                    if($detdtg3[$j]!=''){
                                    $rutaFlujoDetalleVerbo['rol_id']=$detdtg3[$j];
                                    }

                                    if($detdtg4[$j]!=''){
                                    $rutaFlujoDetalleVerbo['verbo_id']=$detdtg4[$j];
                                    }

                                    if($detdtg5[$j]!=''){
                                    $rutaFlujoDetalleVerbo['documento_id']=$detdtg5[$j];
                                    }

                                    if($detdtg6[$j]!=''){
                                    $rutaFlujoDetalleVerbo['orden']=$detdtg6[$j];
                                    }

                                    $rutaFlujoDetalleVerbo->save();
                                }
                            //}
                        }

                    }
                }// Fin del if cuando se valida
                //DB::rollback();
            }

            DB::commit();
            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => $mensajefinal,
                    'ruta_flujo_id'=>$rutaFlujo->id,
                )
            );
        }
    }



    public function postValidar()
    {
        if ( Request::ajax() ) {
            $rf             = new RutaFlujo();
            $cargar         = Array();
            $cargar         = $rf->getValidar();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $cargar
                )
            );
        }
    }

    public function postCdetalle()
    {
        if ( Request::ajax() ) {
            $rf             = new RutaFlujo();
            $cargar         = Array();
            $cargar         = $rf->getRutaFlujoDetalle();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $cargar
                )
            );
        }
    }

    public function postActivar()
    {
        if ( Request::ajax() ) {
            $rpt=array();
            $validaVerbo='';$validaTiempo='';
            $rf                 = new RutaFlujo();

            $validaTiempo = $rf->validaTiempo();

            if($validaVerbo==''){
            $validaVerbo = $rf->validaOrden();
            }

            if($validaVerbo==''){
            $validaVerbo = $rf->validaRol();
            }

            if($validaVerbo==''){
            $validaVerbo = $rf->validaVerbo();
            }

            if($validaVerbo==''){
            $validaVerbo = $rf->validaDocuento();
            }

            if($validaVerbo==''){
            $validaVerbo = $rf->validaDescripcion();
            }

            if($validaTiempo=='' and $validaVerbo==''){
            $actualizar         = Array();
            $actualizar         = $rf->actualizarProduccion();
            $rpt=array(
                    'rst'   => 1,
                    'msj' => ".::Se actualizó correctamente::."
                );
            }
            elseif ($validaTiempo!=''){
                $rpt=array(
                    'rst'   => 2,
                    'msj' => $validaTiempo
                );
            }
            elseif ($validaVerbo!=''){
                $rpt=array(
                    'rst'   => 2,
                    'msj' => $validaVerbo
                );
            }

            return Response::json(
                $rpt
            );
        }
    }

    public function postActualizar()
    {
        if ( Request::ajax() ) {
            $rpt=array();
            $rf                 = new RutaFlujo();

            $actualizar         = Array();
            $actualizar         = $rf->actualizarRuta();
            $rpt=array(
                    'rst'   => 1,
                    'msj' => ".::Se actualizó correctamente::."
                );

            return Response::json(
                $rpt
            );
        }
    }


    public function postCreardos()
    {
        if ( Request::ajax() ) {
            DB::beginTransaction();
            $rutaFlujo="";
            $mensajefinal=".::Se registro correctamente::.";
            $rutaFlujo = new RutaFlujoAux;
            $rutaFlujo['usuario_created_at']= Auth::user()->id;
            $rutaFlujo['estado']= 1;

            $rutaFlujo['flujo_id']= Input::get('flujo_id');
            $rutaFlujo['persona_id']= Auth::user()->id;
            $rutaFlujo['area_id']= Input::get('area_id');
            $rutaFlujo['ruta_id_dep']= Input::get('ruta_flujo_id');

            $rutaFlujo->save();

            $areasGid= explode( "*", Input::get('areasGId') );
            $theadArea= explode( "*", Input::get('theadArea') );
            $tbodyArea= explode( "*", Input::get('tbodyArea') );

            $tiempoGid= explode( "*", Input::get('tiempoGId') );
            $tiempoG= explode( "*", Input::get('tiempoG') );
            $verboG= explode( "*", Input::get('verboG') );


            for($i=0; $i<count($areasGid); $i++ ){
                $rutaFlujoDetalle = new RutaFlujoDetalleAux;
                $rutaFlujoDetalle['usuario_created_at']= Auth::user()->id;
                $rutaFlujoDetalle['ruta_flujo_id']= $rutaFlujo->id;
                $rutaFlujoDetalle['area_id']= $areasGid[$i];
                $rutaFlujoDetalle['norden']= ($i+1);

                $post = array_search($areasGid[$i], $tiempoGid);

                $posdetalleTiempoG= array("0","0");
                // Inicializa valores en caso no tenga datos...
                $rutaFlujoDetalle['tiempo_id']="1";
                $rutaFlujoDetalle['dtiempo']="0";
                $rutaFlujoDetalle['detalle']="";

                if( trim($post)!='' and $post*1>=0 ){
                    $detalleTiempoG=explode( ",", $tiempoG[$post] );
                    
                    if( $theadArea[$i]=="0" ){
                        $posdetalleTiempoG= explode( "|", $tbodyArea[$i] );
                    }

                    $dtg="";

                    if( isset($detalleTiempoG[ $posdetalleTiempoG[1] ]) and trim($detalleTiempoG[ $posdetalleTiempoG[1] ])!=''){
                        $dtg=explode( "_", $detalleTiempoG[ $posdetalleTiempoG[1] ] );
                        if( trim($dtg[1])!='' ){
                            $rutaFlujoDetalle['tiempo_id']=$dtg[1];
                            $rutaFlujoDetalle['dtiempo']=$dtg[2];
                            $rutaFlujoDetalle['detalle']=$dtg[3];
                        }
                    }

                }

                $rutaFlujoDetalle->save();

                // probando para los verbos
                $posdetalleTiempoG= array("0","0");

                if( trim($post)!='' and $post*1>=0 ){
                    $detalleTiempoG=explode( ",", $verboG[$post] );
                    
                    if( $theadArea[$i]=="0" ){
                        $posdetalleTiempoG= explode( "|", $tbodyArea[$i] );
                    }

                    $dtg="";

                    if( isset($detalleTiempoG[ $posdetalleTiempoG[1] ]) and trim($detalleTiempoG[ $posdetalleTiempoG[1] ])!=''){
                        $dtg=explode( "_", $detalleTiempoG[ $posdetalleTiempoG[1] ] );
                        //if( trim($dtg[1])!='' ){
                            $detdtg=explode("|",$dtg[1]);
                            $detdtg2=explode("|",$dtg[2]);
                            $detdtg3=explode("|",$dtg[3]);
                            $detdtg4=explode("|",$dtg[4]);
                            $detdtg5=explode("|",$dtg[5]);
                            $detdtg6=explode("|",$dtg[6]);

                            for($j=0;$j<count($detdtg);$j++){
                                $rutaFlujoDetalleVerbo="";
                                
                                $rutaFlujoDetalleVerbo= new RutaFlujoDetalleVerboAux;
                                $rutaFlujoDetalleVerbo['usuario_created_at']= Auth::user()->id;
                                $rutaFlujoDetalleVerbo['ruta_flujo_detalle_id']= $rutaFlujoDetalle->id;
                                $rutaFlujoDetalleVerbo['nombre']=$detdtg[$j];
                                $rutaFlujoDetalleVerbo['condicion']=$detdtg2[$j];
                                if($detdtg3[$j]!=''){
                                $rutaFlujoDetalleVerbo['rol_id']=$detdtg3[$j];
                                }

                                if($detdtg4[$j]!=''){
                                $rutaFlujoDetalleVerbo['verbo_id']=$detdtg4[$j];
                                }

                                if($detdtg5[$j]!=''){
                                $rutaFlujoDetalleVerbo['documento_id']=$detdtg5[$j];
                                }

                                if($detdtg6[$j]!=''){
                                $rutaFlujoDetalleVerbo['orden']=$detdtg6[$j];
                                }

                                $rutaFlujoDetalleVerbo->save();
                            }
                        //}
                    }

                }

                //DB::rollback();
            }

            $verificando=true;
            $veriuno=true;

            $qinicial=" SELECT * 
                        FROM rutas_flujo_detalle 
                        WHERE ruta_flujo_id='".Input::get('ruta_flujo_id')."'
                        AND estado=1
                        ORDER BY ruta_flujo_id,norden";
            $qrinicial=DB::select($qinicial);

            $qinicialaux=" SELECT * 
                        FROM rutas_flujo_detalle_aux 
                        WHERE ruta_flujo_id='".$rutaFlujo->id."'
                        AND estado=1
                        ORDER BY ruta_flujo_id,norden";
            $qrinicialaux=DB::select($qinicialaux);

            $ruta_id=Input::get('ruta_id');
// cuando  incial es mayor a aux, inical es menor q aux cuando ambos son iguales...
            if(count($qrinicial)>count($qrinicialaux)){ // c
                    DB::table('rutas_detalle')
                       ->where('ruta_id', '=', $ruta_id)
                       ->where('norden', '>', count($qrinicialaux))
                       ->where('estado', '=', '1')
                       ->update(array(
                                    'condicion'=>2,
                                    'usuario_updated_at'=>Auth::user()->id,
                                    'updated_at'=>date("Y-m-d H:i:s")
                                    )
                       );
            }
            for( $i=0; $i< count($qrinicialaux); $i++ ){
                if( count($qrinicial)>$i ){ // indica q aux es mayor o igual
                    $veriuno=true;
                    if($qrinicial[$i]->norden!=$qrinicialaux[$i]->norden){
                        $verificando=false;
                        $veriuno=false;
                    }
                    elseif($qrinicial[$i]->area_id!=$qrinicialaux[$i]->area_id){
                        $verificando=false;
                        $veriuno=false;
                    }
                    elseif($qrinicial[$i]->tiempo_id!=$qrinicialaux[$i]->tiempo_id){
                        $verificando=false;
                        $veriuno=false;
                    }
                    elseif($qrinicial[$i]->dtiempo!=$qrinicialaux[$i]->dtiempo){
                        $verificando=false;
                        $veriuno=false;
                    }
                    elseif($qrinicial[$i]->detalle!=$qrinicialaux[$i]->detalle){
                        $verificando=false;
                        $veriuno=false;
                    }
                    elseif($qrinicial[$i]->estado_ruta!=$qrinicialaux[$i]->estado_ruta){
                        $verificando=false;
                        $veriuno=false;
                    }
                    //else{
                    $qdetalleedit=array();
                    if($veriuno==false){
                        $sqldetalle="SELECT *
                                     FROM rutas_detalle
                                     WHERE ruta_id='".$ruta_id."'
                                     AND norden='".$qrinicialaux[$i]->norden."'
                                     AND estado=1
                                     ORDER BY norden ";
                        $qdetalleedit= DB::select($sqldetalle);
                        try{
                        $rda=RutaDetalle::find($qdetalleedit[0]->id);
                        $rda['usuario_updated_at']= Auth::user()->id;
                        $rda['area_id']=$qdetalleedit[0]->area_id;
                        $rda['tiempo_id']=$qdetalleedit[0]->tiempo_id;
                        $rda['dtiempo']=$qdetalleedit[0]->dtiempo;
                        $rda['detalle']=$qdetalleedit[0]->detalle;
                        $rda['estado_ruta']=$qdetalleedit[0]->estado_ruta;
                        $rda->save();
                        }
                        catch (Exception $e) {
                            //echo $ruta_id."|".$qrinicialaux[$i]->norden."|".$i."|".$sqldetalle;
                            DB::rollback();
                            exit(0);
                        }
                        //aqui actualizando la data de la ruta actual de tramite
                    }
                    $qinicialverbo="    SELECT nombre,condicion,
                    IFNULL(rol_id,'') rol_id,IFNULL(verbo_id,'') verbo_id,
                    IFNULL(documento_id,'') documento_id,IFNULL(orden,'') orden
                                        FROM rutas_flujo_detalle_verbo 
                                        WHERE ruta_flujo_detalle_id='".$qrinicial[$i]->id."'
                                        AND estado=1
                                        ORDER BY ruta_flujo_detalle_id,nombre";
                    $qrinicialverbo=DB::select($qinicialverbo);

                    $qinicialverboaux=" SELECT nombre,condicion,
                    IFNULL(rol_id,'') rol_id,IFNULL(verbo_id,'') verbo_id,
                    IFNULL(documento_id,'') documento_id,IFNULL(orden,'') orden
                                        FROM rutas_flujo_detalle_verbo_aux 
                                        WHERE ruta_flujo_detalle_id='".$qrinicialaux[$i]->id."'
                                        AND estado=1
                                        ORDER BY ruta_flujo_detalle_id,nombre";
                    $qrinicialverboaux=DB::select($qinicialverboaux);

                    if(count($qrinicialverbo)>count($qrinicialverboaux)){ // c
                            DB::table('rutas_detalle_verbo AS rdv')
                               ->join('rutas_detalle AS rd',
                                      'rdv.ruta_detalle_id','=','rd.id')
                               ->where('rd.norden', '=', $qrinicialaux[$i]->norden)
                               ->where('rd.ruta_id', '=', $ruta_id)
                               ->where('rdv.orden', '>', count($qrinicialverboaux))
                               ->where('rdv.estado', '=', '1')
                               ->where('rd.estado', '=', '1')
                               ->update(array(
                                            'rdv.estado'=>0,
                                            'rdv.usuario_updated_at'=>Auth::user()->id,
                                            'rdv.updated_at'=>date("Y-m-d H:i:s")
                                            )
                               );
                    }

                    for( $j=0; $j< count($qrinicialverboaux); $j++ ){
                        if( count($qrinicialverbo)>$j ){
                            $veriunov=true;
                            if($qrinicialverbo[$j]->nombre!=$qrinicialverboaux[$j]->nombre){
                                $verificando=false;
                                $veriunov=false;
                            }
                            elseif($qrinicialverbo[$j]->condicion!=$qrinicialverboaux[$j]->condicion){
                                $verificando=false;
                                $veriunov=false;
                            }
                            elseif($qrinicialverbo[$j]->rol_id!=$qrinicialverboaux[$j]->rol_id){
                                $verificando=false;
                                $veriunov=false;
                            }
                            elseif($qrinicialverbo[$j]->verbo_id!=$qrinicialverboaux[$j]->verbo_id){
                                $verificando=false;
                                $veriunov=false;
                            }
                            elseif($qrinicialverbo[$j]->documento_id!=$qrinicialverboaux[$j]->documento_id){
                                $verificando=false;
                                $veriunov=false;
                            }
                            elseif($qrinicialverbo[$j]->orden!=$qrinicialverboaux[$j]->orden){
                                $verificando=false;
                                $veriunov=false;
                            }

                            if($veriunov==false){
                                $qdetalleeditv=DB::table('rutas_detalle_verbo AS rdv')
                                               ->join('rutas_detalle AS rd',
                                                      'rdv.ruta_detalle_id','=','rd.id')
                                               ->select('rdv.id','rdv.ruta_detalle_id')
                                               ->where('rd.norden', '=', $qrinicialaux[$i]->norden)
                                               ->where('rd.ruta_id', '=', $ruta_id)
                                               ->where('rdv.orden', '=', $qrinicialverboaux[$j]->orden)
                                               ->where('rdv.estado', '=', '1')
                                               ->where('rd.estado', '=', '1')
                                               ->get();

                                $rd= RutaDetalleVerbo::find($qdetalleeditv[0]->id);
                                $rd['ruta_detalle_id']= $qdetalleeditv[0]->ruta_detalle_id;
                                $rd['usuario_updated_at']= Auth::user()->id;
                                $rd['nombre']=$qrinicialverboaux[$j]->nombre;
                                $rd['condicion']=$qrinicialverboaux[$j]->condicion;
                                if(trim($qrinicialverboaux[$j]->rol_id)!=''){
                                $rd['rol_id']=$qrinicialverboaux[$j]->rol_id;
                                }

                                if(trim($qrinicialverboaux[$j]->verbo_id)!=''){
                                $rd['verbo_id']=$qrinicialverboaux[$j]->verbo_id;
                                }

                                if(trim($qrinicialverboaux[$j]->documento_id)!=''){
                                $rd['documento_id']=$qrinicialverboaux[$j]->documento_id;
                                }

                                if(trim($qrinicialverboaux[$j]->orden)!=''){
                                $rd['orden']=$qrinicialverboaux[$j]->orden;
                                }

                                $rd->save();
                                //aqui actualizando la data de la ruta actual de tramite
                            }
                        }
                        else{
                            if(count($qdetalleedit)==0){
                                $sqldetalle="SELECT *
                                             FROM rutas_detalle
                                             WHERE ruta_id='".$ruta_id."'
                                             AND norden='".$qrinicialaux[$i]->norden."'
                                             AND estado=1
                                             ORDER BY norden ";
                                $qdetalleedit= DB::select($sqldetalle);
                            }

                            $rd= new RutaDetalleVerbo;
                            $rd['usuario_created_at']= Auth::user()->id;
                            $rd['ruta_detalle_id']= $qdetalleedit[0]->id;
                            $rd['nombre']=$qrinicialverboaux[$j]->nombre;
                            $rd['condicion']=$qrinicialverboaux[$j]->condicion;
                            if(trim($qrinicialverboaux[$j]->rol_id)!=''){
                            $rd['rol_id']=$qrinicialverboaux[$j]->rol_id;
                            }

                            if(trim($qrinicialverboaux[$j]->verbo_id)!=''){
                            $rd['verbo_id']=$qrinicialverboaux[$j]->verbo_id;
                            }

                            if(trim($qrinicialverboaux[$j]->documento_id)!=''){
                            $rd['documento_id']=$qrinicialverboaux[$j]->documento_id;
                            }

                            if(trim($qrinicialverboaux[$j]->orden)!=''){
                            $rd['orden']=$qrinicialverboaux[$j]->orden;
                            }

                            $rd->save();
                            $verificando=false;
                        }
                    }// finliza for!! verbo
                }
                else{
                    $rd=new RutaDetalle;
                    $rd['ruta_id']= $ruta_id;
                    $rd['usuario_created_at']= Auth::user()->id;
                    $rd['area_id']=$qrinicialaux[$i]->area_id;
                    $rd['tiempo_id']=$qrinicialaux[$i]->tiempo_id;
                    $rd['dtiempo']=$qrinicialaux[$i]->dtiempo;
                    $rd['detalle']=$qrinicialaux[$i]->detalle;
                    $rd['estado_ruta']=$qrinicialaux[$i]->estado_ruta;
                    $rd['norden']=$qrinicialaux[$i]->norden;
                    $rd->save();
                    $verificando=false;

                    $qinicialverboadd=" SELECT nombre,condicion,
                    IFNULL(rol_id,'') rol_id,IFNULL(verbo_id,'') verbo_id,
                    IFNULL(documento_id,'') documento_id,IFNULL(orden,'') orden
                                        FROM rutas_flujo_detalle_verbo_aux 
                                        WHERE ruta_flujo_detalle_id='".$qrinicialaux[$i]->id."'
                                        AND estado=1
                                        ORDER BY ruta_flujo_detalle_id,nombre";
                    $qrinicialverboadd=DB::select($qinicialverboadd);

                    for($j=0;$j<count($qrinicialverboadd);$j++){
                        $rdv= new RutaDetalleVerbo;
                        $rdv['usuario_created_at']= Auth::user()->id;
                        $rdv['ruta_detalle_id']= $rd->id;
                        $rdv['nombre']=$qrinicialverboadd[$j]->nombre;
                        $rdv['condicion']=$qrinicialverboadd[$j]->condicion;
                        if(trim($qrinicialverboadd[$j]->rol_id)!=''){
                        $rdv['rol_id']=$qrinicialverboadd[$j]->rol_id;
                        }

                        if(trim($qrinicialverboadd[$j]->verbo_id)!=''){
                        $rdv['verbo_id']=$qrinicialverboadd[$j]->verbo_id;
                        }

                        if(trim($qrinicialverboadd[$j]->documento_id)!=''){
                        $rdv['documento_id']=$qrinicialverboadd[$j]->documento_id;
                        }

                        if(trim($qrinicialverboadd[$j]->orden)!=''){
                        $rdv['orden']=$qrinicialverboadd[$j]->orden;
                        }

                        $rdv->save();
                    }
                    //falta crear sus verbos!!
                }
                //}
            }//finaliza el for!!

        $envioestado=0;
        $iniciara= Input::get('iniciara');
        $ruta_id=Input::get('ruta_id');
        $ruta_detalle_id= Input::get('ruta_detalle_id');
        $estado_final=Input::get('estado_final');
        $condicional=Input::get('condicional');
        $crear_nuevo= Input::get('crear_nuevo');

        if($verificando==false AND $crear_nuevo=='1'){
            $rf= RutaFlujo::find(Input::get('ruta_flujo_id'));
            $rf['n_flujo_error']=$rf['n_flujo_error']+1;
            $rf['usuario_created_at']= Auth::user()->id;
            $rf->save();

            $rutaFlujo = new RutaFlujo;
            $rutaFlujo['usuario_created_at']= Auth::user()->id;
            $rutaFlujo['estado']= 1;

            $rutaFlujo['flujo_id']= Input::get('flujo_id');
            $rutaFlujo['persona_id']= Auth::user()->id;
            $rutaFlujo['area_id']= Input::get('area_id');
            $rutaFlujo['n_flujo_ok']= '1';
            $rutaFlujo['ruta_id_dep']= Input::get('ruta_flujo_id');

            $rutaFlujo->save();

            // Actualizamos modelo del flujo
            $ract = Ruta::find($ruta_id);
            $ract['ruta_flujo_id']=$rutaFlujo->id;
            $ract->save();

            /*$sqlparaactualizar="SELECT (SELECT count(rf2.id) 
                                        FROM rutas_flujo rf2 
                                        WHERE rf2.id<=rf.id 
                                        AND rf2.flujo_id=rf.flujo_id 
                                        AND rf2.orden>0
                                        ) AS cant,rf.*
                                FROM rutas_flujo rf
                                WHERE rf.id='".$rutaFlujo->id."' ";
            $sqlparaactualizar=DB::select($sqlparaactualizar);

            $rff = RutaFlujo::find($sqlparaactualizar[0]->id);
            $rff['orden']=$sqlparaactualizar[0]->cant;
            $rff->save();*/

            $areasGid= explode( "*", Input::get('areasGId') );
            $theadArea= explode( "*", Input::get('theadArea') );
            $tbodyArea= explode( "*", Input::get('tbodyArea') );

            $tiempoGid= explode( "*", Input::get('tiempoGId') );
            $tiempoG= explode( "*", Input::get('tiempoG') );
            $verboG= explode( "*", Input::get('verboG') );

            for($i=0; $i<count($areasGid); $i++ ){
                $rutaFlujoDetalle = new RutaFlujoDetalle;
                $rutaFlujoDetalle['usuario_created_at']= Auth::user()->id;
                $rutaFlujoDetalle['ruta_flujo_id']= $rutaFlujo->id;
                $rutaFlujoDetalle['area_id']= $areasGid[$i];
                $rutaFlujoDetalle['norden']= ($i+1);

                $post = array_search($areasGid[$i], $tiempoGid);

                $posdetalleTiempoG= array("0","0");
                // Inicializa valores en caso no tenga datos...
                $rutaFlujoDetalle['tiempo_id']="1";
                $rutaFlujoDetalle['dtiempo']="0";
                $rutaFlujoDetalle['detalle']="";

                if( trim($post)!='' and $post*1>=0 ){
                    $detalleTiempoG=explode( ",", $tiempoG[$post] );
                    
                    if( $theadArea[$i]=="0" ){
                        $posdetalleTiempoG= explode( "|", $tbodyArea[$i] );
                    }

                    $dtg="";

                    if( isset($detalleTiempoG[ $posdetalleTiempoG[1] ]) and trim($detalleTiempoG[ $posdetalleTiempoG[1] ])!=''){
                        $dtg=explode( "_", $detalleTiempoG[ $posdetalleTiempoG[1] ] );
                        if( trim($dtg[1])!='' ){
                            $rutaFlujoDetalle['tiempo_id']=$dtg[1];
                            $rutaFlujoDetalle['dtiempo']=$dtg[2];
                            $rutaFlujoDetalle['detalle']=$dtg[3];
                        }
                    }

                }

                $rutaFlujoDetalle->save();

                // probando para los verbos
                $posdetalleTiempoG= array("0","0");

                if( trim($post)!='' and $post*1>=0 ){
                    $detalleTiempoG=explode( ",", $verboG[$post] );
                    
                    if( $theadArea[$i]=="0" ){
                        $posdetalleTiempoG= explode( "|", $tbodyArea[$i] );
                    }

                    $dtg="";

                    if( isset($detalleTiempoG[ $posdetalleTiempoG[1] ]) and trim($detalleTiempoG[ $posdetalleTiempoG[1] ])!=''){
                        $dtg=explode( "_", $detalleTiempoG[ $posdetalleTiempoG[1] ] );
                        //if( trim($dtg[1])!='' ){
                            $detdtg=explode("|",$dtg[1]);
                            $detdtg2=explode("|",$dtg[2]);
                            $detdtg3=explode("|",$dtg[3]);
                            $detdtg4=explode("|",$dtg[4]);
                            $detdtg5=explode("|",$dtg[5]);
                            $detdtg6=explode("|",$dtg[6]);

                            for($j=0;$j<count($detdtg);$j++){
                                $rutaFlujoDetalleVerbo="";
                                
                                $rutaFlujoDetalleVerbo= new RutaFlujoDetalleVerbo;
                                $rutaFlujoDetalleVerbo['usuario_created_at']= Auth::user()->id;
                                $rutaFlujoDetalleVerbo['ruta_flujo_detalle_id']= $rutaFlujoDetalle->id;
                                $rutaFlujoDetalleVerbo['nombre']=$detdtg[$j];
                                $rutaFlujoDetalleVerbo['condicion']=$detdtg2[$j];
                                if($detdtg3[$j]!=''){
                                $rutaFlujoDetalleVerbo['rol_id']=$detdtg3[$j];
                                }

                                if($detdtg4[$j]!=''){
                                $rutaFlujoDetalleVerbo['verbo_id']=$detdtg4[$j];
                                }

                                if($detdtg5[$j]!=''){
                                $rutaFlujoDetalleVerbo['documento_id']=$detdtg5[$j];
                                }

                                if($detdtg6[$j]!=''){
                                $rutaFlujoDetalleVerbo['orden']=$detdtg6[$j];
                                }

                                $rutaFlujoDetalleVerbo->save();
                            }
                        //}
                    }

                }// fin if

                //DB::rollback();
            }// fin for
        }

        if( $iniciara!="" ){
            $sqldetalle="SELECT * 
                         FROM rutas_detalle
                         WHERE ruta_id='".$ruta_id."'
                         AND estado=1
                         ORDER BY norden ";
            $qdetalle= DB::select($sqldetalle);

            for($i=0; $i<count($qdetalle); $i++){
                if( trim($qdetalle[$i]->dtiempo_final)!='' 
                    AND $qdetalle[$i]->norden>=$iniciara ){
                    $rda=RutaDetalle::find($qdetalle[$i]->id);
                    $rda['condicion']=2;
                    $rda->save();

                    $rd=new RutaDetalle;
                    $rd['ruta_id']=$qdetalle[$i]->ruta_id;
                    $rd['area_id']=$qdetalle[$i]->area_id;
                    $rd['tiempo_id']=$qdetalle[$i]->tiempo_id;
                    $rd['dtiempo']=$qdetalle[$i]->dtiempo;
                    $rd['detalle']=$qdetalle[$i]->detalle;
                    $rd['norden']=$qdetalle[$i]->norden;

                    if($qdetalle[$i]->norden==$iniciara){
                        $rd['fecha_inicio']=date("Y-m-d");
                    }
                    $rd->save();

                    $sqldetalleverbo="SELECT * 
                                      FROM rutas_detalle_verbo
                                      WHERE ruta_detalle_id='".$qdetalle[$i]->id."'
                                      AND estado=1
                                      ORDER BY id ";
                    $qdetalleverbo= DB::select($sqldetalleverbo);
                    for($j=0; $j<count($qdetalleverbo); $j++ ){
                        $rdv= new RutaDetalleVerbo;
                        $rdv['ruta_detalle_id']=$rd->id;
                        $rdv['nombre']=$qdetalleverbo[$j]->nombre;
                        $rdv['condicion']=$qdetalleverbo[$j]->condicion;

                        if(trim($qdetalleverbo[$j]->rol_id)!=''){
                        $rdv['rol_id']=$qdetalleverbo[$j]->rol_id;
                        }

                        if(trim($qdetalleverbo[$j]->verbo_id)!=''){
                        $rdv['verbo_id']=$qdetalleverbo[$j]->verbo_id;
                        }

                        if(trim($qdetalleverbo[$j]->documento_id)!=''){
                        $rdv['documento_id']=$qdetalleverbo[$j]->documento_id;
                        }

                        $rdv['orden']=$qdetalleverbo[$j]->orden;

                        $rdv->save();
                    }
                }
                elseif( trim($qdetalle[$i]->dtiempo_final)=='' AND $qdetalle[$i]->norden<=$iniciara ){ //+$condicional  le quite porq el condiconal deberia ya estar dentro del calculo
                    if($qdetalle[$i]->norden==$iniciara){
                        $rda=RutaDetalle::find($qdetalle[$i]->id);
                        $rda['fecha_inicio']=date("Y-m-d");
                        $rda->save();
                    }
                    else{
                        $rda=RutaDetalle::find($qdetalle[$i]->id);
                        $rda['condicion']=1;
                        $rda->save();
                    }
                }
            }
        }

            $rdvalida=RutaDetalle::find($ruta_detalle_id);
            $rdvalida['alerta']=$estado_final;
            $rdvalida->save();



            DB::commit();
            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => $mensajefinal,
                    'ruta_flujo_id'=>$rutaFlujo->id,
                    'envioestado'=>$envioestado,
                )
            );
        }
    }

}
