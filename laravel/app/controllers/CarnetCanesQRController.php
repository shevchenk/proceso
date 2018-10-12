<?php

class CarnetCanesQRController extends \BaseController
{
    /**
     * cargar modulos, mantenimiento
     * POST /cargo/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $cargos = CarnetCanesQR::get(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$cargos));
        }
    }
    
    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                //'path' =>$regex.'|unique:modulos,path,',
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }

            $cargo = new CarnetCanesQR;
            $cargo->serie = Input::get('serie');
            $cargo->nombre = Input::get('nombre');
            $cargo->paterno = Input::get('paterno');
            $cargo->materno = Input::get('materno');
            $cargo->fecha_entrega = Input::get('fecha_entrega');
            $cargo->fecha_nace = Input::get('fecha_nace');
            $cargo->sexo = Input::get('sexo');
            $cargo->raza = Input::get('raza');

            $cargo->estado = Input::get('estado');
            $cargo->persona_id_created_at = Auth::user()->id;
            $cargo->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
                )
            );
        }
    }
    
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }
            $cargoId = Input::get('id');

            $cargos = CarnetCanesQR::find($cargoId);
            $cargos->nombre = Input::get('nombre');
            $cargos->estado = Input::get('estado');
            $cargos->usuario_updated_at = Auth::user()->id;
            $cargos->save();
                        
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Changed the specified resource from storage.
     * POST /cargo/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if ( Request::ajax() ) {
            $estado = Input::get('estado');
            $cargoId = Input::get('id');
            $cargo = CarnetCanesQR::find($cargoId);
            $cargo->persona_id_updated_at = Auth::user()->id;
            $cargo->estado = Input::get('estado');
            $cargo->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }


    /* ***************** GENERACIÓN DE IMAGEN PARA IMPRIMIR ********************** */
    public function getCrearcarnetqr($id, $serie, $tamano, $tipo)
    {
        ini_set("max_execution_time", 300);
        ini_set('memory_limit','512M');        

        /*end get destinatario data*/
        //$vistaprevia='';
        $size = 80; // TAMAÑO EN PX

        $png = QrCode::format('png')
                        ->margin(0)
                        ->size($size)
                        ->color(40,40,40)
                        ->generate("http://proceso.munindependencia.pe/carnetcanes/vistacarnetqrvalida/".$id."/".$serie."/".$tamano."/".$tipo)
                        ;
        
        file_put_contents("img/carnet_cane/temp.png", $png);
        $oData=CarnetCanesQR::verData($id);

        //ini_set("display_errors", true);
        header('Content-type: image/png');
        header('Content-Disposition: attachment; filename="carnet.jpg"');
        
        $nombres = $oData[0]->nombre;
        $apellidos = $oData[0]->paterno.' '.$oData[0]->materno;
        $serie = $oData[0]->serie;
        $fecha_entrega = $oData[0]->fecha_entrega;
        $fecha_nace = $oData[0]->fecha_nace;
        $sexo = $oData[0]->sexo;
        $raza = $oData[0]->raza;
        $rutaFoto = "http://proceso.munindependencia.pe/img/carnet_cane/".$oData[0]->foto;

        //http://proceso.munindependencia.pe/img/carnet_cane/42892330.jpg
        $rutaQR = "img/carnet_cane/temp.png";

        $im = $this->crearCarnet($nombres, $apellidos, $serie, $fecha_entrega, $fecha_nace, $sexo, $raza, $rutaFoto, $rutaQR);

        imagejpeg($im);
        imagedestroy($im);
    }

    public function crearCarnet($nombres, $apellidos, $serie, $fecha_entrega, $fecha_nace, $sexo, $raza, $rutaFoto, $rutaQR)
    {
        $im = imagecreatefromjpeg ('http://proceso.munindependencia.pe/img/carnet_cane/carnet.jpg');

        $black = imagecolorallocate($im, 0, 0, 0);

        function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0)
        {
            if ($spacing == 0)
            {
                imagettftext($image, $size, $angle, $x, $y, $color, $font, ($text));
            }
            else
            {
                $temp_x = $x;
                for ($i = 0; $i < strlen($text); $i++)
                {
                    $bbox = imagettftext($image, $size, $angle, $temp_x, $y, $color, $font, ($text[$i]));
                    $temp_x += $spacing + ($bbox[2] - $bbox[0]);
                }
            }
        }

        function getImageFromUrl($rutaQR){
            $fi = explode(".", $rutaQR);
            switch ($fi[count($fi)-1]){
                case 'png':
                    $stamp = imagecreatefrompng($rutaQR);
                    break;
                case 'jpg':
                case 'jpeg':
                    $stamp = imagecreatefromjpeg($rutaQR);
                    break;
                case 'gif':
                    $stamp = imagecreatefromgif($rutaQR);
                    break;
            }

            return $stamp;
        }

        $font = 'fonts/carnet/Hack-Bold.ttf';
        $font2 = 'fonts/carnet/Hack-Regular.ttf';

        imagettftext($im, 10, 0, 53, 190, $black, $font, $serie);

        imagettftextSp($im, 9, 0, 170, 110, $black, $font, utf8_decode($nombres),-0.05);
        imagettftextSp($im, 9, 0, 170, 82, $black, $font, utf8_decode($apellidos),-0.05);        
         
        imagettftext($im, 8, 0, 257, 110, $black, $font, $fecha_entrega);
        imagettftext($im, 8, 0, 257, 138, $black, $font2, $fecha_nace);
        imagettftext($im, 8, 0, 257, 168, $black, $font2, $sexo);
        imagettftext($im, 8, 0, 180, 197, $black, $font2, $raza);

        $stamp = getImageFromUrl($rutaFoto);
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        imagecolortransparent($stamp, imagecolorallocate($stamp, 255, 0, 255));
        $myRed = 255;
        $myGreen = 0;
        $myBlue = 0;
        imagealphablending($stamp, false);

        $r=$sx/2;
        for($x=0;$x<$sx;$x++)
            for($y=0;$y<$sy;$y++){
                $_x = $x - $sx/2;
                $_y = $y - $sy/2;
                if((($_x*$_x) + ($_y*$_y)) < ($r*$r)){
                    //imagesetpixel($stamp,$x,$y,$c);
                }else{
                    $alphacolor = imagecolorallocatealpha($stamp, 0, 0, 0, 127);
                    imagesetpixel($stamp, $x, $y, $alphacolor );
                }
            }

        imagecopyresampled($im, $stamp, 27, 28, 0, 0, 113, 103, imagesx($stamp), imagesy($stamp));

        $stamp = getImageFromUrl($rutaQR);
        $marge_right = 180;
        $marge_bottom = 116;
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        imagecopyresampled($im, $stamp, $marge_right, $marge_bottom, 0, 0, 65, 65, imagesx($stamp), imagesy($stamp));

        return $im;
    }
    /* *************************************************************************** */

    public function getVistacarnetqrvalida($id, $serie, $tamano, $tipo)
    {
        ini_set("max_execution_time", 300);
        ini_set('memory_limit','512M');        

        /*end get destinatario data*/        
        $vistaprevia='Documento Vista Previa';
        
        $size = 80; // TAMAÑO EN PX 
        $png = QrCode::format('png')->margin(0)->size($size)->generate("http://proceso.munindependencia.pe/carnetcanes/vistacarnetqrvalida/".$id."/".$serie."/".$tamano."/".$tipo);
        $png = base64_encode($png);
        $png= "<img src='data:image/png;base64," . $png . "' width='65' height='65'>";
        
        $oData=CarnetCanesQR::verData($id);

        if(date("m") == '01') $mes_ac = 'Enero';
        else if(date("m") == '02') $mes_ac = 'Febrero';
        else if(date("m") == '03') $mes_ac = 'Marzo';
        else if(date("m") == '04') $mes_ac = 'Abril';
        else if(date("m") == '05') $mes_ac = 'Mayo';
        else if(date("m") == '06') $mes_ac = 'Junio';
        else if(date("m") == '07') $mes_ac = 'Julio';
        else if(date("m") == '08') $mes_ac = 'Agosto';
        else if(date("m") == '09') $mes_ac = 'Septiembre';
        else if(date("m") == '10') $mes_ac = 'Octubre';
        else if(date("m") == '11') $mes_ac = 'Noviembre';
        else if(date("m") == '12') $mes_ac = 'Diciembre';        

        $params = [
            'reporte'=>2,
            'nombres'=>$oData[0]->nombre,
            'apellidos'=>$oData[0]->paterno.' '.$oData[0]->materno,
            'serie'=>$oData[0]->serie,
            'fecha_entrega'=>$oData[0]->fecha_entrega,
            'fecha_nace'=>$oData[0]->fecha_nace,
            'sexo'=>$oData[0]->sexo,
            'raza'=>$oData[0]->raza,
            'estado'=>$oData[0]->estado,            
            'foto'=>$oData[0]->foto,
            'fecha_actual_texto' => date("d") . " del " . $mes_ac . " de " . date("Y"),
            'tamano'=>$tamano,
            'vistaprevia'=>$vistaprevia,
            'imagen'=>$png
        ];

        $view = \View::make('admin.mantenimiento.templates.plantilla_carnetcan', $params);
        $html = $view->render();

        $pdf = App::make('dompdf');
        $html = preg_replace('/>\s+</', '><', $html);
        $pdf->loadHTML($html);

        $pdf->setPaper('a'.$tamano)->setOrientation('portrait');

        return $pdf->stream();
        //\PDFF::loadHTML($html)->setPaper('a4')->setOrientation('landscape')->setWarnings(false)->stream();
    }


    public function postActualizarimagen()
    {
        //si la peticion es ajax
        if ( Request::ajax()){
            ini_set('memory_limit','128M');
            ini_set('set_time_limit', '300');
            ini_set('display_errors', true);
            
            $norden = Input::get('norden');
            $mFile = Input::get('image');
            
            $file = 'uc'.$norden;
            $url = "img/carnet_cane/".$norden;

            if($fileName = $this->fileToFile($mFile,$url)){
                $idUsr = Auth::user()->id;
                $this->resizeImage($fileName,$fileName,1000);
                $url_update = explode("/", $fileName);
                $url_update = $url_update[count($url_update)-1];
                $mSql = "UPDATE carnet_canes SET foto = '$url_update', persona_id_updated_at='".$idUsr."', updated_at = CURRENT_TIMESTAMP WHERE id = '$norden' LIMIT 1;";
                DB::update($mSql);
                $redimImg = true;
            }
 
            return Response::json(array('result'=>'1','red'=>$redimImg,'ruta'=>$fileName,'norden'=>$norden));
        }
    }

    public function fileToFile($file, $url)
    {
        if ( !is_dir('file') ) {
            mkdir('file',0777);
        }
        if ( !is_dir('file/meta') ) {
            mkdir('file/actividad',0777);
        }

        list($type, $file) = explode(';', $file);
        list(, $type) = explode('/', $type);
        if ($type=='jpeg') $type='jpg';
        if (strpos($type,'document')!==False) $type='docx';
        if (strpos($type, 'sheet') !== False) $type='xlsx';
        if (strpos($type, 'pdf') !== False) $type='pdf';
        if ($type=='plain') $type='txt';
        list(, $file)      = explode(',', $file);
        $file = base64_decode($file);
        $url = $url.'.'.$type;
        file_put_contents($url , $file);
        return $url;
    } 

    function resizeImage($src,$destination,$maxSize=-1,$fillSaquare = FALSE, $quality = 100)
    {
        /*
            ########### 
            MODO DE USO
            ########### 
            
                $src 
                    - Ruta de la imagen / URL de la imagen
                
                $destination
                    - ruta donde guardar imagen
                
                $maxSize [OPCIONAL]
                    - Tamaño maximo de pixeles (aplica de alto o ancho)
                
                $fillSaquare [OPCIONAL default:FALSE] 
                    - TRUE  : Rellena con blanco para generar el cuadrado
                    - FALSE : Redimensiona la imagen
                
                $quality [OPCIONAL default:100]
                    - Calidad de la imagen de 1 a 100%

            ########### 
            RESPUESTAS
            ########### 
            
                -2 = Archivo no existe
                -1 = Archivo invalido
                 0 = Error al guardar / destino inaccesible / permiso denegado
                 1 = Guardado
        */

        if("http://" != substr($src, 0,6) && "http://" != substr($src, 0,7)){
            if (!file_exists($src)) {
                return -2;
            }
        }

        ini_set('memory_limit','-1');

        $ext = explode(".", $src);
        $ext = strtolower($ext[count($ext)-1]);
        list($width, $height) = getimagesize($src);

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $tImage = imagecreatefromjpeg($src);
                break;
            case 'png':
                $tImage = imagecreatefrompng($src);
                break;
            case 'gif':
                $tImage = imagecreatefromgif($src);
                break;
            default:
                return -1;
                break;
        }

        $width = imagesx( $tImage );
        $height = imagesy( $tImage );

        if($width > $height){
            $squareSize = $width;
        }else{
            $squareSize = $height;
        }

        if($maxSize != -1 && $squareSize>$maxSize){
            $squareSize = $maxSize;
        }


        if($width> $height) {
            $width_t=$squareSize;
            $height_t=round($height/$width*$squareSize);
            $offsetY=ceil(($width_t-$height_t)/2);
            $ossetX=0;
        } elseif($height> $width) {
            $height_t=$squareSize;
            $width_t=round($width/$height*$squareSize);
            $ossetX=ceil(($height_t-$width_t)/2);
            $offsetY=0;
        }
        else {
            $width_t=$height_t=$squareSize;
            $ossetX=$offsetY=0;
        }

        if(!$fillSaquare){
            $ossetX=$offsetY=0;
            $new = imagecreatetruecolor( $width_t , $height_t );
        }else{
            $new = imagecreatetruecolor( $squareSize , $squareSize );
        }


        $bg = imagecolorallocate ( $new, 255, 255, 255 );
        imagefill ( $new, 0, 0, $bg );
        imagecopyresampled( $new , $tImage , $ossetX, $offsetY, 0, 0, $width_t, $height_t, $width, $height );
        $status = 0;
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    //header('Content-Type: image/jpeg');
                    if(imagejpeg($new, $destination, $quality))$status=1;
                    break;
                case 'png':
                    //header('Content-type: image/png'); 
                    if(imagepng($new, $destination))$status=1;
                    break;
                case 'gif':
                    //header('Content-Type: image/gif');
                    if(imagegif($new, $destination))$status=1;

                    break;
                default:
                    return -1;
                    break;
            }

        imagedestroy($new);
        return $status;
    }

}
