<?php

class recoveryController extends \BaseController
{


    public function postGuardar(){


            if (Input::hasFile('documento')) {
                $path = 'img/documentos/recovery/';
                $allFiles = Input::file();
                $uploadSuccess=true;
                $allRes = array();

                $destinationPath = public_path().'/'.$path;
                $i = 0 ;
                //var_dump($allFiles);
                foreach ($allFiles['documento'] as $key => $file) {
                    $extension = $file->getClientOriginalExtension();
                    $filename        = str_random(5) . '_' . time();
                    $up   = $file->move($destinationPath, $filename.'.'.$extension);
                    $allRes[$i++]=$path.$filename.'.'.$extension;
                    if(!$up){$uploadSuccess=false;}
                }


                if($uploadSuccess && count($allRes)>0){

                    $nDoc = new DocumentoRecuperado;
                    $nDoc->numero = Input::get('numero');
                    $nDoc->tipo_doc = Input::get('tipo_documento');
                    $nDoc->fecha_doc = Input::get('fecha');
                    $nDoc->area = Auth::user()->area_id;
                    $nDoc->archivo = json_encode($allRes);
                    $nDoc->created_at = date('Y-m-d H:m:s');
                    $nDoc->usuario_created_at = Auth::user()->id;
                    $nDoc->estado = 1;
                    $nDoc->save();

                    // SUBIDO Y GUARDADO 
                    //var_dump($allRes);die("SAVED");
                }else{
                    // NO SUBIDO
                    //var_dump($allRes);die("NOUPLOAD"); 
                }
            }else{
                  // die("NOFILE"); 
                // NO FILE
            }
            return Redirect::to('admin.mantenimiento.recovery');
    }


    public function postActualizar(){


            if (Input::hasFile('documento')) {

                $nDoc = DocumentoRecuperado::find(Input::get('edit'));

                $path = 'img/documentos/recovery/';
                $allFiles = Input::file();
                $uploadSuccess=true;

                $allRes = json_decode($nDoc->archivo);

                $destinationPath = public_path().'/'.$path;
                foreach ($allFiles['documento'] as $key => $file) {
                    $extension = $file->getClientOriginalExtension();
                    $filename        = str_random(5) . '_' . time();
                    $up   = $file->move($destinationPath, $filename.'.'.$extension);
                    $allRes[]=$path.$filename.'.'.$extension;
                    if(!$up){$uploadSuccess=false;}
                }

                if($uploadSuccess && count($allRes)>0){
                    
                    $nDoc->archivo = json_encode($allRes);
                    $nDoc->updated_at = date('Y-m-d H:m:s');
                    $nDoc->usuario_updated_at = Auth::user()->id;
                    $nDoc->estado = 1;
                    $nDoc->save();

                    // SUBIDO Y GUARDADO 
                    //die("SAVED");
                }else{
                    // NO SUBIDO
                    //die("NOUPLOAD"); 
                }
            }else{
                    //die("NOFILE"); 
                // NO FILE
            }
            return Redirect::to('admin.mantenimiento.recovery');
    }
    /**
     * Store a newly created resource in storage.
     * POST /cargo/listar
     *
     * @return Response
     */
    public function postLoad(){

        //$numero = (Input::has('numero') ? Input::get('numero'):'');
        $result = DocumentoRecuperado::getRecuperadosArea(Auth::user()->area_id);

        return Response::json($result); 

    }


}
