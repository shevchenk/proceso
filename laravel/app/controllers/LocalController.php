<?php

class LocalController extends \BaseController
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
    /**
     * cargar roles, mantenimiento
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

            if( Input::has("local") ){
                $local=Input::get("local");
                if( trim( $local )!='' ){
                    $array['where'].=" AND l.local LIKE '%".$local."%' ";
                }
            }

			if( Input::has("direccion") ){
                $direccion=Input::get("direccion");
                if( trim( $direccion )!='' ){
                    $array['where'].=" AND l.direccion LIKE '%".$direccion."%' ";
                }
            }

            if( Input::has("estado") ){
                $estado=Input::get("estado");
                if( trim( $estado )!='' ){
                    $array['where'].=" AND l.estado='".$estado."' ";
                }
            }

            $array['order']=" ORDER BY l.local ";

            $cant  = Local::getCargarCount( $array );
            $aData = Local::getCargar( $array );

            $aParametro['rst'] = 1;
            $aParametro["recordsTotal"]=$cant;
            $aParametro["recordsFiltered"]=$cant;
            $aParametro['data'] = $aData;
            $aParametro['msj'] = "No hay registros aún";
            return Response::json($aParametro);

        }
    }
    /**
     * cargar roles, mantenimiento
     * POST /rol/listar
     *
     * @return Response
     */
    public function postListarlocales()
    {
        if ( Request::ajax() ) {
            $a      = new Local;
            $listar = Array();
            $listar = $a->getListar();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $listar
                )
            );
        }
    }

	public function postListar()
    {
        if ( Request::ajax() ) {
            $a      = new Local;
            $listar = Array();
            $listar = $a->getLocal();
         
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
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                'local' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $local = new Local;
            $local->local = Input::get('local');
            $local->direccion = Input::get('direccion');
            $local->fecha_inicio = Input::get('fecha_inicio');
            $local->fecha_final = Input::get('fecha_final');
            $local->estado = Input::get('estado');
            $local->usuario_created_at = Auth::user()->id;
            $local->save();

            return Response::json(array('rst'=>1, 'msj'=>'Registro realizado correctamente', 'local_id'=>$local->id));
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
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                'local' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required' => ':attribute Es requerido',
                'regex'    => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json( array('rst'=>2, 'msj'=>$validator->messages()) );
            }

            $localId = Input::get('id');
            $local = Local::find($localId);
            $local->local = Input::get('local');
            $local->direccion = Input::get('direccion');
            $local->fecha_inicio = Input::get('fecha_inicio');
            $local->fecha_final = Input::get('fecha_final');
            $local->estado = Input::get('estado');
            $local->usuario_updated_at = Auth::user()->id;
            $local->save();

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

            $local = Local::find(Input::get('id'));
            $local->usuario_created_at = Auth::user()->id;
            $local->estado = Input::get('estado');
            $local->save();
           
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
