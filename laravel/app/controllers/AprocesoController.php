<?php
class AprocesoController extends \BaseController
{

    public function getGuardar()
    {

            $allInputs = $_POST;
            var_dump($allInputs);
            die();

/*
            $r = RutaFlujo::getGuardar();
            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => $r['mensaje'],
                    'ruta_flujo_id'=>$r['ruta_flujo_id']
                )
            );
        }
*/
    }


    public function getCrear(){
        
 
        $fichero = public_path()."/file/log.dat";
        $actual = file_get_contents($fichero);
        $cod = "fecha_inicio=".Input::get('fecha_inicio')."\r\ncodigo=".Input::get('codigo')."\r\nsumilla=".Input::get('sumilla')."\r\nruta_flujo_id=".Input::get('ruta_flujo_id')."\r\nid_incidencia=".Input::get('id_incidencia');
        $actual .= '[GET] : '.date("m-d h:ia  ",time())."DATA:\r\n $cod  \r\n\r\n";
        file_put_contents($fichero, $actual);
        

        $res = json_decode(file_get_contents("http://10.0.1.20/mdi/?getInfoNum&getCall=".$cod));

        if($res->result==1){
            Input::replace(['codigo' => $res->data[0]->ANI.' - '.$res->data[0]->Destination]);
        }


        if(Input::has('codigo')){

            $r           = new Ruta;
            $res         = Array();
            $res         = $r->crearRuta02();

            return Response::json( array('rst'   => $res['rst'],'msj'   => $res['msj']));
        }else{
            return Response::json(array('rst'  => 0,'msj'   => "Sin datos."));
        }


    }

    public function postCrear(){
        $fichero = public_path()."/file/log.dat";
        $actual = file_get_contents($fichero);
        
        $cod = "fecha_inicio=".Input::get('fecha_inicio')."\r\ncodigo=".Input::get('codigo')."\r\nsumilla=".Input::get('sumilla')."\r\nruta_flujo_id=".Input::get('ruta_flujo_id')."\r\nid_incidencia=".Input::get('id_incidencia');
        $actual .= '[POST] : '.date("m-d h:ia  ",time())."DATA:\r\n $cod  \r\n\r\n";
        file_put_contents($fichero, $actual);

 
        $res = json_decode(file_get_contents("http://10.0.1.20/mdi/?getInfoNum&getCall=".$cod));

        if($res->result==1){
            Input::replace(['codigo' => $res->data[0]->ANI.' - '.$res->data[0]->Destination]);
        }


        if(Input::has('codigo')){

            $r           = new Ruta;
            $res         = Array();
            $res         = $r->crearRuta02();

            return Response::json( array('rst'   => $res['rst'],'msj'   => $res['msj']));
        }else{
            return Response::json(array('rst'  => 0,'msj'   => "Sin datos."));
        }

    }



}
