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


}
