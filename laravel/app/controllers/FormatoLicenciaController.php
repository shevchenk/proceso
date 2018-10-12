<?php
//use PhpOffice\PhpWord\SimpleType\DocProtect;
class FormatoLicenciaController extends \BaseController
{
    //protected $_errorController;
    /**
     * Valida sesion activa
     */
    /*
    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth');
        $this->_errorController = $ErrorController;
    }
    */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            //$cargos = FormatoLicenciaContruccion::get(Input::all());
            $sql = "SELECT lc.* 
                FROM licencia_construccion lc
                    WHERE estado = 1;";        
            $cargos = DB::select($sql);

            return Response::json(array('rst'=>1,'datos'=>$cargos));
        }
    }
       
    public function postCrearliccontruc()
    {
        if ( Request::ajax() ) {
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                //'expediente' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $Ssql="SELECT MAX(correlativo) as correlativo FROM licencia_construccion WHERE anio = '".date('Y')."';";
            $lic_cons=DB::select($Ssql);
            $correlativo = ($lic_cons[0]->correlativo + 1);

            $formatolic = new FormatoLicenciaContruccion;
            if($correlativo)
                $formatolic->correlativo = $correlativo;

            if(Input::get('expediente'))
                $formatolic->expediente = Input::get('expediente');

            if(Input::get('fecha_emision'))
                $formatolic->fecha_emision = Input::get('fecha_emision');

            if(Input::get('fecha_vence'))
                $formatolic->fecha_vence = Input::get('fecha_vence');

            if(Input::get('licencia_edifica'))
                $formatolic->licencia_edifica = Input::get('licencia_edifica');

            if(Input::get('modalidad'))
                $formatolic->modalidad = Input::get('modalidad');

            if(Input::get('uso'))
                $formatolic->uso = Input::get('uso');

            if(Input::get('zonifica'))
                $formatolic->zonifica = Input::get('zonifica');

            if(Input::get('altura'))
                $formatolic->altura = Input::get('altura');

            if(Input::get('person_id'))
                $formatolic->persona_id = Input::get('person_id');

            if(Input::get('administrado'))
                $formatolic->persona = Input::get('administrado');

            if(Input::get('propietario'))
                $formatolic->propietario = Input::get('propietario');

            if(Input::get('departamento'))
                $formatolic->departamento = Input::get('departamento');

            if(Input::get('provincia'))
                $formatolic->provincia = Input::get('provincia');

            if(Input::get('distrito'))
                $formatolic->distrito = Input::get('distrito');

            if(Input::get('dir_urbaniza'))
                $formatolic->dir_urbaniza = Input::get('dir_urbaniza');

            if(Input::get('dir_mz'))
                $formatolic->dir_mz = Input::get('dir_mz');

            if(Input::get('dir_lote'))
                $formatolic->dir_lote = Input::get('dir_lote');

            if(Input::get('dir_calle'))
                $formatolic->dir_calle = Input::get('dir_calle');

            if(Input::get('area_terreno'))
                $formatolic->area_terreno = Input::get('area_terreno');

            if(Input::get('valor_obra'))
                $formatolic->valor_obra = Input::get('valor_obra');        

            if(Input::get('piso'))
                $formatolic->nom_piso = Input::get('piso');
            if(Input::get('area_techada'))
                $formatolic->nom_area = Input::get('area_techada');

            if(Input::get('piso_1'))
                $formatolic->piso_1 = Input::get('piso_1');

            if(Input::get('area_1'))
                $formatolic->area_1 = Input::get('area_1');

            if(Input::get('piso_2'))
                $formatolic->piso_2 = Input::get('piso_2');
            
            if(Input::get('area_2'))
                $formatolic->area_2 = Input::get('area_2');
            
            if(Input::get('piso_3'))
                $formatolic->piso_3 = Input::get('piso_3');
            
            if(Input::get('area_3'))
                $formatolic->area_3 = Input::get('area_3');
            
            if(Input::get('piso_4'))
                $formatolic->piso_4 = Input::get('piso_4');
            
            if(Input::get('area_4'))
                $formatolic->area_4 = Input::get('area_4');
            
            if(Input::get('piso_5'))
                $formatolic->piso_5 = Input::get('piso_5');
            
            if(Input::get('area_5'))
                $formatolic->area_5 = Input::get('area_5');

            if(Input::get('derecho_licencia'))
                $formatolic->derecho_licencia = Input::get('derecho_licencia');
            
            if(Input::get('recibo'))
                $formatolic->recibo = Input::get('recibo');
            
            if(Input::get('fecha_recibo'))
                $formatolic->fecha_recibo = Input::get('fecha_recibo');

            $formatolic->anio = date('Y');
            $formatolic->estado = 1;
            $formatolic->created_at = date('Y-m-d h:m:s');
            $formatolic->persona_id_created_at = Auth::user()->id;
            $formatolic->save();

            return Response::json(array('rst'=>1, 'msj'=>'Registro realizado correctamente.', 'id'=>$formatolic->id));
        }
    }

    public function postCambiarestado()
    {
        if ( Request::ajax() ) {
            $estado = Input::get('estado');
            $cargoId = Input::get('id');
            $formatolic = FormatoLicenciaContruccion::find($cargoId);
            $formatolic->persona_id_updated_at = Auth::user()->id;
            $formatolic->estado = Input::get('estado');
            $formatolic->updated_at = date('Y-m-d h:m:s');
            $formatolic->persona_id_updated_at = Auth::user()->id;
            $formatolic->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    public function getVerdoclicenciaconstruc($id, $tamano, $tipo) //$data
    {
        ini_set("max_execution_time", 300);
        ini_set('memory_limit','512M');        

        /*end get destinatario data*/
        $vistaprevia='';
        $size = 100;
        $png = QrCode::format('png')
                    ->margin(0)
                    ->size(100)
                    //->color(40,40,40)
                    ->generate("http://proceso.munindependencia.pe/formatolicencia/vistaqrliccontruccion/".$id."/".$tamano."/".$tipo);

        $png = base64_encode($png);
        $png= "<img src='data:image/png;base64," . $png . "' width='100' height='100'>";

        $oData=FormatoLicenciaContruccion::verDataFormatoLicencia($id);
        
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
            'reporte'=>1,            
            'correlativo' => $oData[0]->correlativo,
            'expediente' => $oData[0]->expediente,
            'fecha_emi' => $oData[0]->fecha_emision,
            'fecha_vence' => $oData[0]->fecha_vence,                            
            'licencia_edifica' => $oData[0]->licencia_edifica,
            'mod' => $oData[0]->modalidad,
            'uso' => $oData[0]->uso,
            'zonifica' => $oData[0]->zonifica,
            'altura' => $oData[0]->altura,
            //'persona' => 'CARDENAS MONTOYA, MARIA DOLORES',
            'persona' => $oData[0]->persona,
            'propietario' => $oData[0]->propietario,
            'departamento' => $oData[0]->departamento,
            'provincia' => $oData[0]->provincia,
            'distrito' => $oData[0]->distrito,
            'dir_urbaniza' => $oData[0]->dir_urbaniza,
            'dir_mz' => $oData[0]->dir_mz,
            'dir_lote' => $oData[0]->dir_lote,
            'dir_calle' => $oData[0]->dir_calle,
            'area_terre' => number_format($oData[0]->area_terreno, 2),
            'valor_obra' => number_format($oData[0]->valor_obra, 2),

            'piso' => $oData[0]->nom_piso,
            'area_techada' => $oData[0]->nom_area,
            'piso_1' => $oData[0]->piso_1,
            'area_1' => $oData[0]->area_1,
            'piso_2' => $oData[0]->piso_2,
            'area_2' => $oData[0]->area_2,
            'piso_3' => $oData[0]->piso_3,
            'area_3' => $oData[0]->area_3,
            'piso_4' => $oData[0]->piso_4,
            'area_4' => $oData[0]->area_4,
            'piso_5' => $oData[0]->piso_5,
            'area_5' => $oData[0]->area_5,
            'derecho_licencia' => number_format($oData[0]->derecho_licencia, 2),
            'recibo' => $oData[0]->recibo,
            'fecha_recibo' => $oData[0]->fecha_recibo,

            'fecha_actual_texto' => date("d") . " del " . $mes_ac . " de " . date("Y"),
            'anio' => date("Y"),
            'tamano'=>$tamano,
            'vistaprevia'=>$vistaprevia,
            'imagen'=>$png
        ];

        $view = \View::make('admin.mantenimiento.templates.plantilla_lconstr', $params);
        $html = $view->render();

        $pdf = App::make('dompdf');
        $html = preg_replace('/>\s+</', '><', $html);
        $pdf->loadHTML($html);

        $pdf->setPaper('a'.$tamano)->setOrientation('portrait');

        return $pdf->stream();
    }

    public function getVistaqrliccontruccion($id, $tamano, $tipo)
    {
        ini_set("max_execution_time", 300);
        ini_set('memory_limit','512M');        

        /*end get destinatario data*/        
        $vistaprevia='Documento Vista Previa';

        $oData=FormatoLicenciaContruccion::verDataFormatoLicencia($id);

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
            'correlativo' => $oData[0]->correlativo,
            'expediente' => $oData[0]->expediente,
            'fecha_emi' => $oData[0]->fecha_emision,
            'fecha_vence' => $oData[0]->fecha_vence,
            'licencia_edifica' => $oData[0]->licencia_edifica,
            'mod' => $oData[0]->modalidad,
            'uso' => $oData[0]->uso,
            'zonifica' => $oData[0]->zonifica,
            'altura' => $oData[0]->altura,
            'persona' => 'CARDENAS MONTOYA, MARIA DOLORES',
            'propietario' => $oData[0]->propietario,
            'departamento' => $oData[0]->departamento,
            'provincia' => $oData[0]->provincia,
            'distrito' => $oData[0]->distrito,
            'dir_urbaniza' => $oData[0]->dir_urbaniza,
            'dir_mz' => $oData[0]->dir_mz,
            'dir_lote' => $oData[0]->dir_lote,
            'dir_calle' => $oData[0]->dir_calle,
            'area_terre' => number_format($oData[0]->area_terreno, 2),
            'valor_obra' => number_format($oData[0]->valor_obra, 2),

            'piso' => $oData[0]->nom_piso,
            'area_techada' => $oData[0]->nom_area,
            'piso_1' => $oData[0]->piso_1,
            'area_1' => $oData[0]->area_1,
            'piso_2' => $oData[0]->piso_2,
            'area_2' => $oData[0]->area_2,
            'piso_3' => $oData[0]->piso_3,
            'area_3' => $oData[0]->area_3,
            'piso_4' => $oData[0]->piso_4,
            'area_4' => $oData[0]->area_4,
            'piso_5' => $oData[0]->piso_5,
            'area_5' => $oData[0]->area_5,
            'derecho_licencia' => number_format($oData[0]->derecho_licencia, 2),
            'recibo' => $oData[0]->recibo,
            'fecha_recibo' => $oData[0]->fecha_recibo,

            'fecha_actual_texto' => date("d") . " del " . $mes_ac . " de " . date("Y"),
            'anio' => date("Y"),
            'tamano'=>$tamano,
            'vistaprevia'=>$vistaprevia,
            'imagen'=>''
        ];

        $view = \View::make('admin.mantenimiento.templates.plantilla_lconstr', $params);
        $html = $view->render();

        $pdf = App::make('dompdf');
        $html = preg_replace('/>\s+</', '><', $html);
        $pdf->loadHTML($html);

        $pdf->setPaper('a'.$tamano)->setOrientation('portrait');

        return $pdf->stream();
    }

    public function postBuscarpersona()
    {
        if(Input::get('tipobus') == 1)
        {
            $dni = Input::get('dni');
            $sql = "SELECT p.* 
                FROM personas p
                    WHERE p.dni = '$dni';"; 
        }
        else
        {
            $where = '';
            $nombres = Input::get('nombres');
            $arr_nom = explode(" ", $nombres);

            foreach ($arr_nom as $key => $value) {
                if($key == 0) $condicion = ' WHERE (';
                else $condicion = ' AND ';
                $where .= $condicion." CONCAT(p.paterno, p.materno, p.nombre) LIKE '%".$value."%' ";
            }
            $sql = "SELECT p.* 
                FROM personas p ".$where." );"; 
            //echo $sql;
        }
        $r = DB::select($sql);
        
        return Response::json(
                          array(
                              'rst'=>1,
                              'datos'=>$r
                          )
                    );
    }

    public function postCrearpersona()
    {
        if ( Request::ajax() ) {
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                //'expediente' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $persona = new Persona;
            $persona->paterno = Input::get('paterno');
            $persona->materno = Input::get('materno');
            $persona->nombre = Input::get('nombre');
            $persona->dni = Input::get('dni');

            $persona->estado = 1;
            $persona->created_at = date('Y-m-d h:m:s');
            $persona->usuario_created_at = Auth::user()->id;
            $persona->save();

            return Response::json(
                        array('rst'=>1, 
                                'msj'=>'Registro realizado correctamente.',
                                'id'=>$persona->id,
                                'nombres'=>$persona->nombre.' '.$persona->materno.' '.$persona->paterno));
        }
    }
}
