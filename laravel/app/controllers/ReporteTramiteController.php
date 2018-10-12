<?php
class ReporteTramiteController extends BaseController
{

    private $archivos = array();
    public function postTramiteunico()
    {
      $array=array();
      $fecha='';
      $array['where']='';
      
      if( Input::has('tramite') AND Input::get('tramite')!='' ){
        $tramite=explode(" ",trim(Input::get('tramite')));
        for($i=0; $i<count($tramite); $i++){
          $array['where'].=" AND re.referido LIKE '%".$tramite[$i]."%' ";
        }
      }
      


      $r = ReporteTramite::TramiteUnico( $array );


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
        $array['where']=" AND r.id=".Input::get('ruta_id')." ";
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

        $rst=ReporteTramite::ExpedienteUnico(); 
        //$times = array();
        
        foreach ($rst as $ind => $ndc){
            $this->addVideoLink($rst[$ind]->referido);

            if($ndc->doc_digital_id != null){
              $rst[$ind]->referido .= ' <a target="_blank" href="documentodig/vista/'.$ndc->doc_digital_id.'/4/0"><span class="btn btn-default btn-sm" title="Ver documento"><i class="fa fa-eye"></i></span></a> ';
            }
        }

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
