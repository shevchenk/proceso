<?php

class PersonaFinalController extends BaseController
{
    /**
     * Store a newly created resource in storage.
     * POST /persona/cargarareas
     *
     * @return Response
     */
    public function postCargarareas()
    {
        $personaId = Input::get('persona_id');
        $areas = PersonaFinal::getAreas($personaId);
        return Response::json(array('rst'=>1,'datos'=>$areas));
    }
    /**
     * Store a newly created resource in storage.
     * POST /persona/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'paterno' => $required.'|'.$regex,
                'materno' => $required.'|'.$regex,
                'email' => 'required|email|unique:personas,email',
                'password'      => 'required|min:6',
                'dni'      => 'required|numeric|min:8|unique:personas,dni',
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
                'exists'       => ':attribute ya existe',
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
            $rol = Input::get('rol');$rst='';
            /*if ($rol==9 or $rol==8) {
                $rst=Persona::BuscarJefe1(Input::get('area'));
                if($rst>=1){
                return Response::json(
                    array(
                    'rst'=>3,
                    'msj'=>'Área con Gerente, Inhabilitar y volver a crear',
                    )
                );
                }else {
                 Persona::ActualizarResponsable(Input::get('area'));   
                }
            }*/
            DB::beginTransaction();
            $persona = new Persona;
            $persona['paterno'] = Input::get('paterno');
            $persona['materno'] = Input::get('materno');
            $persona['nombre'] = Input::get('nombre');
            $persona['email'] = Input::get('email');
            $persona['email_mdi'] = Input::get('email_mdi');
            $persona['celular'] = trim(Input::get('celular'));
            $persona['telefono'] = trim(Input::get('telefono'));
            $persona['direccion'] = trim(Input::get('direccion'));
            $persona['dni'] = Input::get('dni');
            $persona['sexo'] = Input::get('sexo');
            $persona['password'] = Input::get('password');
            $persona['doc_privados'] = Input::get('doc_privados');
            
            if (Input::get('fecha_nacimiento')<>'') 
                $persona['fecha_nacimiento'] = Input::get('fecha_nacimiento');
            if (Input::has('nivel'))
                $persona['nivel'] = Input::get('nivel');
            if (Input::has('nivel_proceso'))
                $persona['nivel_proceso'] = Input::get('nivel_proceso');
            
            $local = trim( implode( ",", Input::get('local') ) );
            $persona['local_id'] = $local;
            /*if ($rol==9 or $rol==8){
            $persona['responsable_asigt']=1;
            $persona['responsable_dert']=1;}*/
            $persona['responsable_area'] = Input::get('responsable_area');
            $persona['area_id'] = Input::get('area');
            $persona['rol_id'] = Input::get('rol');
            $persona['modalidad'] = Input::get('modalidad');
            $persona['vista_doc'] = Input::get('vista_doc');
            $persona['estado'] = Input::get('estado');
            $persona['usuario_created_at'] = Auth::user()->id;
            $persona['verified'] = true;
            $persona['token'] = null;
            $persona->save();
            $personaId = $persona->id;
            //si es cero no seguir, si es 1 ->estado se debe copiar de celulas
            $estado = Input::get('estado');
            if ($estado == 0 ) {
                DB::commit();
                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                    )
                );
            }
            
            /* Aqui la asignación de la persona */
            DB::table('cargo_persona')
            ->where('persona_id', $personaId)
            ->update(array('estado' => 0));

            $cargo_id = Input::get('cargo_id');
            if( is_array($cargo_id) AND count($cargo_id) > 0 ){
                for ($i=0; $i < count($cargo_id); $i++) { 
                    $cargoId = $cargo_id[$i];
                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    if( isset($cargoPersona->cargo_id) ){
                        DB::table('cargo_persona')
                            ->where('persona_id', '=', $personaId)
                            ->where('cargo_id', '=', $cargoId)
                            ->update(
                                array(
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }
                    else{
                        DB::table('cargo_persona')
                            ->insert(
                                array(
                                    'persona_id'=> $personaId,
                                    'cargo_id'=> $cargoId,
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }

                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    DB::table('area_cargo_persona')
                    ->where('cargo_persona_id', '=', $cargoPersona->id)
                    ->update(
                        array(
                            'estado' => 0,
                            'usuario_updated_at' => Auth::user()->id
                            )
                    );

                    $areas = Input::get('areas_'.$cargoId);

                    if( is_array($areas) AND count($areas) ){
                        for ($j=0; $j < count($areas); $j++) { 
                            $areaId = $areas[$j];
                            $areaCargoPersona = DB::table('area_cargo_persona')
                                            ->where('cargo_persona_id', '=', $cargoPersona->id)
                                            ->where('area_id', '=', $areaId)
                                            ->first();

                            if( isset($areaCargoPersona->area_id) ){
                                DB::table('area_cargo_persona')
                                ->where('cargo_persona_id', '=', $cargoPersona->id)
                                ->where('area_id', '=', $areaId)
                                ->update(
                                    array(
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                            else{
                                DB::table('area_cargo_persona')->insert(
                                    array(
                                        'area_id' => $areaId,
                                        'cargo_persona_id' => $cargoPersona->id,
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                        }
                    }
                    
                }
            }
            /************************************/
            DB::commit();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente'.$personaId,
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /persona/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'paterno' => $required.'|'.$regex,
                'materno' => $required.'|'.$regex,
                'email' => 'required|email|unique:personas,email,'.Input::get('id'),
                'dni'      => 'required|numeric|min:8|unique:personas,dni,'.Input::get('id'),
                //'password'      => 'required|min:6',
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
            
            $rol = Input::get('rol');$rst='';
            /*if ($rol==9 or $rol==8) {
                $rst=Persona::BuscarJefe(Input::get('area'),Input::get('id'));
                if($rst>=1){
                return Response::json(
                    array(
                    'rst'=>3,
                    'msj'=>'Área con Gerente, Inhabilitar y volver a actualizar',
                    )
                );
                }else {
                 Persona::ActualizarResponsable(Input::get('area'));   
                }
            }*/
            DB::beginTransaction();
            $personaId = Input::get('id');
            $persona = Persona::find($personaId);
            /*if ($rol==9 or $rol==8){
            $persona['responsable_asigt']=1;
            $persona['responsable_dert']=1;}*/
            $persona['paterno'] = Input::get('paterno');
            $persona['materno'] = Input::get('materno');
            $persona['nombre'] = Input::get('nombre');
            $persona['email'] = Input::get('email');
            $persona['email_mdi'] = Input::get('email_mdi');
            $persona['dni'] = Input::get('dni');
            $persona['celular'] = trim(Input::get('celular'));
            $persona['telefono'] = trim(Input::get('telefono'));
            $persona['direccion'] = trim(Input::get('direccion'));
            $persona['sexo'] = Input::get('sexo');
            $persona['area_id'] = Input::get('area');
            $persona['rol_id'] = Input::get('rol');
            $persona['doc_privados'] = Input::get('doc_privados');

            $local = trim( implode( ",", Input::get('local') ) );
            $persona['local_id'] = $local;
            
            if (Input::has('password'))
                $persona['password'] = Input::get('password');
            if (Input::has('fecha_nacimiento'))
                $persona['fecha_nacimiento'] = Input::get('fecha_nacimiento');
            if (Input::has('nivel'))
                $persona['nivel'] = Input::get('nivel');
            if (Input::has('nivel_proceso'))
                $persona['nivel_proceso'] = Input::get('nivel_proceso');

            $persona['responsable_area'] = Input::get('responsable_area');
            $persona['modalidad'] = Input::get('modalidad');
            $persona['vista_doc'] = Input::get('vista_doc');
            $persona['estado'] = Input::get('estado');
            $persona['usuario_updated_at'] = Auth::user()->id;
            $persona->save();
            
            //$cargos = Input::get('cargos_selec');
            $estado = Input::get('estado');

            DB::table('cargo_persona')
                ->where('persona_id', $personaId)
                ->update(array('estado' => 0));

            if ($estado == 0 ) {
                DB::commit();
                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente.',
                    )
                );
            }
            
            /* Aqui la asignación de la persona */
            DB::table('cargo_persona')
            ->where('persona_id', $personaId)
            ->update(array('estado' => 0));

            $cargo_id = Input::get('cargo_id');
            if( is_array($cargo_id) AND count($cargo_id) > 0 ){
                for ($i=0; $i < count($cargo_id); $i++) { 
                    $cargoId = $cargo_id[$i];
                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    if( isset($cargoPersona->cargo_id) ){
                        DB::table('cargo_persona')
                            ->where('persona_id', '=', $personaId)
                            ->where('cargo_id', '=', $cargoId)
                            ->update(
                                array(
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }
                    else{
                        DB::table('cargo_persona')
                            ->insert(
                                array(
                                    'persona_id'=> $personaId,
                                    'cargo_id'=> $cargoId,
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }

                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    DB::table('area_cargo_persona')
                    ->where('cargo_persona_id', '=', $cargoPersona->id)
                    ->update(
                        array(
                            'estado' => 0,
                            'usuario_updated_at' => Auth::user()->id
                            )
                    );

                    $areas = Input::get('areas_'.$cargoId);

                    if( is_array($areas) AND count($areas) ){
                        for ($j=0; $j < count($areas); $j++) { 
                            $areaId = $areas[$j];
                            $areaCargoPersona = DB::table('area_cargo_persona')
                                            ->where('cargo_persona_id', '=', $cargoPersona->id)
                                            ->where('area_id', '=', $areaId)
                                            ->first();

                            if( isset($areaCargoPersona->area_id) ){
                                DB::table('area_cargo_persona')
                                ->where('cargo_persona_id', '=', $cargoPersona->id)
                                ->where('area_id', '=', $areaId)
                                ->update(
                                    array(
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                            else{
                                DB::table('area_cargo_persona')->insert(
                                    array(
                                        'area_id' => $areaId,
                                        'cargo_persona_id' => $cargoPersona->id,
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                        }
                    }
                    
                }
            }
            /************************************/
            DB::commit();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    public function postCrearalumno()
    {
        //si la peticion es ajax
        //if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'paterno' => $required.'|'.$regex,
                'materno' => $required.'|'.$regex,
                'password'      => 'required|min:6',
                'dni'      => 'required|numeric|min:8|unique:personas,dni',
            );

            if( Input::has('email') AND trim(Input::get('email'))!=''){
                $reglas['email'] = 'required|email|unique:personas,email';
            }

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
                'exists'       => ':attribute ya existe',
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
            $rol = Input::get('rol');$rst='';
            /*if ($rol==9 or $rol==8) {
                $rst=Persona::BuscarJefe1(Input::get('area'));
                if($rst>=1){
                return Response::json(
                    array(
                    'rst'=>3,
                    'msj'=>'Área con Gerente, Inhabilitar y volver a crear',
                    )
                );
                }else {
                 Persona::ActualizarResponsable(Input::get('area'));   
                }
            }*/
            DB::beginTransaction();
            $persona = new Persona;
            $persona['paterno'] = Input::get('paterno');
            $persona['materno'] = Input::get('materno');
            $persona['nombre'] = Input::get('nombre');
            $persona['email'] = Input::get('email');
            $persona['email_mdi'] = Input::get('email_mdi');
            $persona['celular'] = trim(Input::get('celular'));
            $persona['telefono'] = trim(Input::get('telefono'));
            $persona['direccion'] = trim(Input::get('direccion'));
            $persona['dni'] = Input::get('dni');
            $persona['sexo'] = Input::get('sexo');
            $persona['password'] = Input::get('password');
            $persona['doc_privados'] = Input::get('doc_privados');
            if (Input::get('fecha_nacimiento')<>'') 
            $persona['fecha_nacimiento'] = Input::get('fecha_nacimiento');        
            /*if ($rol==9 or $rol==8){
            $persona['responsable_asigt']=1;
            $persona['responsable_dert']=1;}*/
            $persona['responsable_area'] = Input::get('responsable_area');
            $persona['area_id'] = 10;
            $persona['rol_id'] = 46;
            $persona['local_id'] = Input::get('local');
            $persona['modalidad'] = Input::get('modalidad');
            $persona['vista_doc'] = Input::get('vista_doc');
            $persona['estado'] = Input::get('estado');
            $persona['usuario_created_at'] = Auth::user()->id;
            $persona['verified'] = true;
            $persona['token'] = null;
            $persona->save();
            $personaId = $persona->id;
            //si es cero no seguir, si es 1 ->estado se debe copiar de celulas
            $estado = Input::get('estado');
            if ($estado == 0 ) {
                DB::commit();
                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                    )
                );
            }
            
            /* Aqui la asignación de la persona */
            DB::table('cargo_persona')
            ->where('persona_id', $personaId)
            ->update(array('estado' => 0));

            $cargo_id = Input::get('cargo_id');
            if( is_array($cargo_id) AND count($cargo_id) > 0 ){
                for ($i=0; $i < count($cargo_id); $i++) { 
                    $cargoId = $cargo_id[$i];
                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    if( isset($cargoPersona->cargo_id) ){
                        DB::table('cargo_persona')
                            ->where('persona_id', '=', $personaId)
                            ->where('cargo_id', '=', $cargoId)
                            ->update(
                                array(
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }
                    else{
                        DB::table('cargo_persona')
                            ->insert(
                                array(
                                    'persona_id'=> $personaId,
                                    'cargo_id'=> $cargoId,
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }

                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    DB::table('area_cargo_persona')
                    ->where('cargo_persona_id', '=', $cargoPersona->id)
                    ->update(
                        array(
                            'estado' => 0,
                            'usuario_updated_at' => Auth::user()->id
                            )
                    );

                    $areas = Input::get('areas_'.$cargoId);

                    if( is_array($areas) AND count($areas) ){
                        for ($j=0; $j < count($areas); $j++) { 
                            $areaId = $areas[$j];
                            $areaCargoPersona = DB::table('area_cargo_persona')
                                            ->where('cargo_persona_id', '=', $cargoPersona->id)
                                            ->where('area_id', '=', $areaId)
                                            ->first();

                            if( isset($areaCargoPersona->area_id) ){
                                DB::table('area_cargo_persona')
                                ->where('cargo_persona_id', '=', $cargoPersona->id)
                                ->where('area_id', '=', $areaId)
                                ->update(
                                    array(
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                            else{
                                DB::table('area_cargo_persona')->insert(
                                    array(
                                        'area_id' => $areaId,
                                        'cargo_persona_id' => $cargoPersona->id,
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                        }
                    }
                    
                }
            }
            /************************************/
            DB::commit();
            if( Input::has('apiproceso') ){
                return $persona;
            }
            else{
                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro realizado correctamente'.$personaId,
                    )
                );
            }
        //}
    }

    /**
     * Update the specified resource in storage.
     * POST /persona/editar
     *
     * @return Response
     */
    public function postEditaralumno()
    {
        if ( Request::ajax() ) {
            
            $rol = Input::get('rol');$rst='';
            /*if ($rol==9 or $rol==8) {
                $rst=Persona::BuscarJefe(Input::get('area'),Input::get('id'));
                if($rst>=1){
                return Response::json(
                    array(
                    'rst'=>3,
                    'msj'=>'Área con Gerente, Inhabilitar y volver a actualizar',
                    )
                );
                }else {
                 Persona::ActualizarResponsable(Input::get('area'));   
                }
            }*/
            DB::beginTransaction();
            $personaId = Input::get('id');
            $persona = Persona::find($personaId);
            /*if ($rol==9 or $rol==8){
            $persona['responsable_asigt']=1;
            $persona['responsable_dert']=1;}*/
            $persona['email'] = Input::get('email');
            $persona['email_mdi'] = Input::get('email_mdi');
            $persona['celular'] = trim(Input::get('celular'));
            $persona['telefono'] = trim(Input::get('telefono'));
            $persona['direccion'] = trim(Input::get('direccion'));
            $persona['sexo'] = Input::get('sexo');
            $persona['doc_privados'] = Input::get('doc_privados');
            
            if (Input::has('password'))
                $persona['password'] = Input::get('password');
            if (Input::has('fecha_nacimiento'))
                $persona['fecha_nacimiento'] = Input::get('fecha_nacimiento');

            $persona['local_id'] = Input::get('local');
            $persona['responsable_area'] = Input::get('responsable_area');
            $persona['modalidad'] = Input::get('modalidad');
            $persona['vista_doc'] = Input::get('vista_doc');
            $persona['estado'] = Input::get('estado');
            $persona['usuario_updated_at'] = Auth::user()->id;
            $persona->save();
            
            //$cargos = Input::get('cargos_selec');
            $estado = Input::get('estado');

            DB::table('cargo_persona')
                ->where('persona_id', $personaId)
                ->update(array('estado' => 0));

            if ($estado == 0 ) {
                DB::commit();
                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente.',
                    )
                );
            }
            
            /* Aqui la asignación de la persona */
            DB::table('cargo_persona')
            ->where('persona_id', $personaId)
            ->update(array('estado' => 0));

            $cargo_id = Input::get('cargo_id');
            if( is_array($cargo_id) AND count($cargo_id) > 0 ){
                for ($i=0; $i < count($cargo_id); $i++) { 
                    $cargoId = $cargo_id[$i];
                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    if( isset($cargoPersona->cargo_id) ){
                        DB::table('cargo_persona')
                            ->where('persona_id', '=', $personaId)
                            ->where('cargo_id', '=', $cargoId)
                            ->update(
                                array(
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }
                    else{
                        DB::table('cargo_persona')
                            ->insert(
                                array(
                                    'persona_id'=> $personaId,
                                    'cargo_id'=> $cargoId,
                                    'estado'=>1,
                                    'usuario_updated_at' => Auth::user()->id
                                )
                            );
                    }

                    $cargoPersona = DB::table('cargo_persona')
                                    ->where('persona_id', '=', $personaId)
                                    ->where('cargo_id', '=', $cargoId)
                                    ->first();
                    DB::table('area_cargo_persona')
                    ->where('cargo_persona_id', '=', $cargoPersona->id)
                    ->update(
                        array(
                            'estado' => 0,
                            'usuario_updated_at' => Auth::user()->id
                            )
                    );

                    $areas = Input::get('areas_'.$cargoId);

                    if( is_array($areas) AND count($areas) ){
                        for ($j=0; $j < count($areas); $j++) { 
                            $areaId = $areas[$j];
                            $areaCargoPersona = DB::table('area_cargo_persona')
                                            ->where('cargo_persona_id', '=', $cargoPersona->id)
                                            ->where('area_id', '=', $areaId)
                                            ->first();

                            if( isset($areaCargoPersona->area_id) ){
                                DB::table('area_cargo_persona')
                                ->where('cargo_persona_id', '=', $cargoPersona->id)
                                ->where('area_id', '=', $areaId)
                                ->update(
                                    array(
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                            else{
                                DB::table('area_cargo_persona')->insert(
                                    array(
                                        'area_id' => $areaId,
                                        'cargo_persona_id' => $cargoPersona->id,
                                        'estado' => 1,
                                        'usuario_created_at' => Auth::user()->id
                                    )
                                );
                            }
                        }
                    }
                    
                }
            }
            /************************************/
            DB::commit();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }
}
