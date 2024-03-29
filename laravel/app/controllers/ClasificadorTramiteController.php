<?php

class ClasificadorTramiteController extends \BaseController
{
    protected $_errorController;
    /**
     * Valida sesion activa
     */
    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth');
        $this->_errorController = $ErrorController;
    }

    public function postListarareas()
    {
        if ( Request::ajax() ) {
            $a      = new RutaFlujoCampo;
            $lista = $a->Listarareas();

            return Response::json(
                array(
                    'rst'   => 1,
                    'data' => $lista
                )
            );
        }
    }

    public function postMostrarcampos()
    {
        if ( Request::ajax() ) {
            $a      = new RutaFlujoCampo;
            $lista = $a->Mostrarcampos();

            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Campos listados',
                    'data' => $lista
                )
            );
        }
    }

    public function postListarcampos()
    {
        if ( Request::ajax() ) {
            $a      = new RutaFlujoCampo;
            $lista = $a->Listarcampos();

            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Campos listados',
                    'data' => $lista
                )
            );
        }
    }

    public function postGuardarrutacampos()
    {
        if ( Request::ajax() ) {
            $a      = new RutaCampo;
            $lista = $a->Guardarrutacampos();

            return Response::json($lista);
        }
    }

    public function postActualizarrutadetalle()
    {
        if ( Request::ajax() ) {
            RutaFlujoDetalle::FinalizarAnular();
            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Evento actualizado!'
                )
            );
        }
    }

    public function postListarcamposareas()
    {
        if ( Request::ajax() ) {
            $a      = new RutaFlujoCampoArea;
            $lista = $a->Listarcamposareas();

            $r =    array(
                        'rst'   => 1,
                        'msj'   => 'Campos y Áreas listados',
                        'data' => $lista
                    );
            $r['ruta_flujo_id'] = Input::get('ruta_flujo_id');
            if( Input::has('norden') ){
                $r['norden'] = Input::get('norden');
            }

            if( count($lista) == 0 ){
                $r['rst'] = 2;
                $r['msj'] = 'Falta diseñar y configurar Datos del Servicio';
            }

            return Response::json(
                $r
            );
        }
    }

    public function postRegistrarcampos()
    {
        ini_set('max_input_vars',5000);
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '128M');
        ini_set('upload_max_filesize', '128M');
        ini_set('max_execution_time',300);
        if ( Request::ajax() ) {
            $a      = new RutaFlujoCampo;
            $lista = $a->Registrarcampos();

            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Campos registrados y/o actualizados',
                    'lista' => $lista
                )
            );
        }
    }

    public function postGuardarevento()
    {
        if ( Request::ajax() ) {
            $a      = new RutaFlujoEvento;
            $a->Guardarevento();

            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Evento registrado',
                )
            );
        }
    }

    public function postListareventos()
    {
        if ( Request::ajax() ) {
            $a       = new RutaFlujoEvento;
            $eventos = $a->Listareventos();

            return Response::json(
                array(
                    'rst'   => 1,
                    'data'  => $eventos,
                    'msj'   => 'Eventos listados',
                )
            );
        }
    }

    public function postEliminarevento()
    {
        if ( Request::ajax() ) {
            $a       = new RutaFlujoEvento;
            $a->Eliminarevento();

            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Evento eliminado, se volvió a cargar los eventos',
                )
            );
        }
    }

    public function postAsignarcampos()
    {
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '64M');
        //ini_set('upload_max_filesize', '64M');
        ini_set('max_execution_time',300);

        if ( Request::ajax() ) {
            $a      = new RutaFlujoCampoArea;
            $a->Asignarcampos();

            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Campos asignados y/o actualizados',
                )
            );
        }
    }

    public function postAsignarcampo()
    {
        if ( Request::ajax() ) {
            $a      = new RutaFlujoCampoArea;
            $a->Asignarcampo();

            return Response::json(
                array(
                    'rst'   => 1,
                    'msj'   => 'Campo asignado y/o actualizados',
                )
            );
        }
    }
    /**
     * cargar clasificadortramites, mantenimiento
     * POST /rol/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        if ( Request::ajax() ) {
            /*********************FIJO*****************************/
            $array=array();
            $array['where']='';$array['usuario']=Auth::user()->id;
            $array['limit']='';$array['order']='';
            
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

            if( Input::has("nombre") ){
                $nombre=Input::get("nombre");
                if( trim( $nombre )!='' ){
                    $array['where'].=" AND ct.nombre_clasificador_tramite LIKE '%".$nombre."%' ";
                }
            }
            if( Input::has("tipo_tramite") ){
                $tipo_tramite=Input::get("tipo_tramite");
                if( trim( $tipo_tramite )!='' ){
                    $array['where'].=" AND tt.nombre_tipo_tramite LIKE '%".$tipo_tramite."%' ";
                }
            }

            if( Input::has("unidoc") ){
                $unidoc=Input::get("unidoc");
                if( trim( $unidoc )!='' ){
                    $array['where'].=" AND ct.unidad_documentaria='".$unidoc."' ";
                }
            }

            if( Input::has("estado") ){
                $estado=Input::get("estado");
                if( trim( $estado )!='' ){
                    $array['where'].=" AND ct.estado='".$estado."' ";
                }
            }

            $array['order']=" ORDER BY ct.nombre_clasificador_tramite ";

            $cant  = ClasificadorTramite::getCargarCount( $array );
            $aData = ClasificadorTramite::getCargar( $array );

            $aParametro['rst'] = 1;
            $aParametro["recordsTotal"]=$cant;
            $aParametro["recordsFiltered"]=$cant;
            $aParametro['data'] = $aData;
            $aParametro['msj'] = "No hay registros aún";
            return Response::json($aParametro);

        }
    }
    /**
     * cargar clasificadortramites, mantenimiento
     * POST /rol/listar
     *
     * @return Response
     */
   public function postListar()
    {
        if ( Request::ajax() ) {
            $a      = new ClasificadorTramite;
            $listar = Array();
            $listar = $a->getClasificadorTramite();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $listar
                )
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /rol/crear
     *
     * @return Response
     */

    public function postCrear()
    {
        if ( Request::ajax() ) {
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú()]{2,250})$/i';
            $required = 'required';
            $reglas = array(
                'nombre' => $required,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
                'exists'       => ':attribute ya existe',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $clasificadortramite = new ClasificadorTramite;
            $clasificadortramite->nombre_clasificador_tramite = Input::get('nombre');
            $clasificadortramite->tipo_tramite_id = Input::get('tipo_tramite');
            $clasificadortramite->documento_id = Input::get('documento_id');
            //$clasificadortramite->area = Input::get('area');
            $clasificadortramite->valida_pendiente = Input::get('valida_pendiente');
            $clasificadortramite->estado = Input::get('estado_clasificador');
            $clasificadortramite->usuario_created_at = Auth::user()->id;
            $clasificadortramite->save();

            return Response::json(array('rst'=>1, 'msj'=>'Registro realizado correctamente', 'clasificadortramite_id'=>$clasificadortramite->id));
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /rol/editar
     *
     * @return Response
     */
   public function postEditar()
    {
        if ( Request::ajax() ) {
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú()]{2,250})$/i';
            $required = 'required';
            $reglas = array(
                'nombre' => $required,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $clasificadortramiteId = Input::get('id');
            $clasificadortramite = ClasificadorTramite::find($clasificadortramiteId);
            $clasificadortramite->nombre_clasificador_tramite = Input::get('nombre');
            $clasificadortramite->tipo_tramite_id = Input::get('tipo_tramite');
            $clasificadortramite->documento_id = Input::get('documento_id');
            $clasificadortramite->valida_pendiente = Input::get('valida_pendiente');
            $clasificadortramite->estado = Input::get('estado_clasificador');
            $clasificadortramite->usuario_updated_at = Auth::user()->id;
            $clasificadortramite->save();

            return Response::json(array('rst'=>1, 'msj'=>'Registro actualizado correctamente'));
        }
    }

    /**
     * Changed the specified resource from storage.
     * POST /rol/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {

        if ( Request::ajax() ) {

            $clasificadortramite = ClasificadorTramite::find(Input::get('id'));
            $clasificadortramite->usuario_created_at = Auth::user()->id;
            $clasificadortramite->estado = Input::get('estado');
            $clasificadortramite->save();
           
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

    public function postCambiarestadoef()
    {

        if ( Request::ajax() ) {

            $clasificadortramite = ClasificadorTramite::find(Input::get('id'));
            if( trim($clasificadortramite->ruta_flujo_id) != 0 ){
                $clasificadortramite->usuario_created_at = Auth::user()->id;
                $clasificadortramite->estado_final = Input::get('estado');
                $clasificadortramite->save();

                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                    )
                );
            }
            else{
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>'Busque y seleccione el proceso',
                    )
                );
            }
        }
    }
    
    public function postListarrequisito()
    {
        if ( Request::ajax() ) {
            $array=array();
            $array['where']='';
            
             if( Input::has("id") ){
                $poi_id=Input::get("id");
                if( trim( $poi_id )!='' ){
                    $array['where'].=" AND r.clasificador_tramite_id=".$poi_id;
                }
            }
            $a      = new Requisito;
            $listar = Array();
            $listar = $a->getCargar($array);

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $listar
                )
            );
        }
    }
    
    public function postCambiarestadorequisito()
    {

        if ( Request::ajax() ) {

            $costopersonal = Requisito::find(Input::get('id'));
            $costopersonal->usuario_created_at = Auth::user()->id;
            $costopersonal->estado = Input::get('estado');
            $costopersonal->save();
           
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }
    
    public function postCrearrequisito()
    {
        if ( Request::ajax() ) {
            $regex = 'regex:/^([a-zA-Z1-9 .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                'nombre' => $required,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $costopersonal = new Requisito;
            $costopersonal->clasificador_tramite_id = Input::get('poi_id');
            $costopersonal->nombre = Input::get('nombre');
            $costopersonal->cantidad = Input::get('cantidad');
            $costopersonal->estado = Input::get('estado');
            $costopersonal->usuario_created_at = Auth::user()->id;
            $costopersonal->save();

            if( trim(Input::get('pdf_archivo'))!='' ){
                $urld=explode(".", urldecode(Input::get('pdf_nombre')));
                $url = "upload/requisito/R-".$costopersonal->id.".".end($urld);
				$costopersonal->ruta_archivo = $url;
				$costopersonal->save();
                
                Pretramite::FileToFile(Input::get('pdf_archivo'), $url);
            }

            return Response::json(array('rst'=>1, 'msj'=>'Registro realizado correctamente', 'costo_personal_id'=>$costopersonal->id));
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /rol/editar
     *
     * @return Response
     */
    public function postEditarrequisito()
    {
        if ( Request::ajax() ) {
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                'nombre' => $required,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $costopersonalId = Input::get('id');
            $costopersonal = Requisito::find($costopersonalId);
            $costopersonal->nombre = Input::get('nombre');
            $costopersonal->cantidad = Input::get('cantidad');
            $costopersonal->estado = Input::get('estado');
            $costopersonal->usuario_updated_at = Auth::user()->id;
            $costopersonal->save();

            if( trim(Input::get('pdf_archivo'))!='' ){
                $urld=explode(".", urldecode(Input::get('pdf_nombre')));
                $url = "upload/requisito/R-".$costopersonal->id.".".end($urld);
				$costopersonal->ruta_archivo = $url;
				$costopersonal->save();
                
                Pretramite::FileToFile(Input::get('pdf_archivo'), $url);
            }
            elseif( trim(Input::get('pdf_nombre'))=='' ){
                $costopersonal->ruta_archivo = '';
                $costopersonal->save();
            }

            return Response::json(array('rst'=>1, 'msj'=>'Registro actualizado correctamente'));
        }
    }
    
    public function postAgregarproceso()
    {

        if ( Request::ajax() ) {

            $costopersonal = ClasificadorTramite::find(Input::get('id'));
            $costopersonal->usuario_created_at = Auth::user()->id;
            $costopersonal->ruta_flujo_id = Input::get('ruta_flujo_id');
            $costopersonal->save();
           
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
