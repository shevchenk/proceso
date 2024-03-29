<?php

class DocumentoController extends \BaseController 
{
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
                    $array['where'].=" AND doc.nombre LIKE '%".$nombre."%' ";
                }
            }

            if( Input::has("nemonico") ){
                $nemonico=Input::get("nemonico");
                if( trim( $nemonico )!='' ){
                    $array['where'].=" AND doc.nemonico LIKE '%".$nemonico."%' ";
                }
            }
            if( Input::has("tipos") ){
                $tipo=Input::get("tipos");
                if( trim( $tipo )!='' ){
                    $array['where'].=" AND doc.tipo='".$tipo."'";
                }
            }
            if( Input::has("areas") ){
                $area=Input::get("areas");
                if( trim( $area )!='' ){
                    $array['where'].=" AND a.nombre LIKE '%".$area."%'";
                }
            }
            if( Input::has("posiciones") ){
                $posicion=Input::get("posiciones");
                if( trim( $posicion )!='' ){
                    $array['where'].=" AND doc.posicion=".$posicion;
                }
            }
            if( Input::has("posiciones_fecha") ){
                $posicion_fecha=Input::get("posiciones_fecha");
                if( trim( $posicion_fecha )!='' ){
                    $array['where'].=" AND doc.posicion_fecha=".$posicion_fecha;
                }
            }
            if( Input::has("estado") ){
                $estado=Input::get("estado");
                if( trim( $estado )!='' ){
                    $array['where'].=" AND doc.estado='".$estado."' ";
                }
            }

            $valida = strpos($_SERVER['HTTP_REFERER'], 'unidaddocumentaria');
            if( $valida !== false ){
                $array['where'].=" AND doc.solicitante='Interno'";
            }
            else{
                $array['where'].=" AND doc.solicitante!='Interno'";
            }

            $array['where'].=" AND doc.id > 0";

            $array['order']=" ORDER BY doc.nombre ";

            $cant  = Documento::getCargarCount( $array );
            $aData = Documento::getCargar( $array );

            $aParametro['rst'] = 1;
            $aParametro["recordsTotal"]=$cant;
            $aParametro["recordsFiltered"]=$cant;
            $aParametro['data'] = $aData;
            $aParametro['msj'] = "No hay registros aún";
            return Response::json($aParametro);

        }
    }

    /**
     * listar verbos para select
     * POST /verbo/listar
     */
    public function postListar()
    {
        if ( Request::ajax() ) {
             $listar = Documento::getDocumento();
             return Response::json(
                 array(
                     'rst'   => 1,
                     'datos' => $listar
                 )
             );
        }
    }

    public function postCrear()
    {
        if ( Request::ajax() ) {
            $regex = 'regex:/^([a-zA-Z1-90 .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
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

            $documento = new Documento;
            $documento['nombre']  = Input::get('nombre');
            $documento['nemonico'] = Input::get('nemonico');
            $documento['tipo']  = Input::get('tipo');
            $documento['area_id']    = Input::get('area_id');
            $documento['area']    = Input::get('area');
            $documento['posicion'] = Input::get('posicion');
            $documento['posicion_fecha'] = Input::get('posicion_fecha');
            $documento['estado'] = Input::get('estado');
            $documento['solicitante'] = Input::get('solicitante');
            $documento['pide_nro'] = Input::get('pide_nro');
            $documento['publico'] = Input::get('publico');
            $documento['nivel'] = Input::get('nivel');
            $documento['usuario_created_at'] = Auth::user()->id;
            $documento->save();

            return Response::json(array('rst'=>1, 'msj'=>'Registro realizado correctamente', 'documento_id'=>$documento->id));
        }
    }


    /**
     * Actualizar verbo
     * POST /verbo/editar
     */
   public function postEditar()
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

            $documentoId = Input::get('id');
            $documento = Documento::find($documentoId);
            $documento['nombre'] = Input::get('nombre');
            $documento['nemonico'] = Input::get('nemonico');
            $documento['tipo']  = Input::get('tipo');
            $documento['area_id']    = Input::get('area_id');
            $documento['area']   = Input::get('area');
            $documento['posicion'] = Input::get('posicion');
            $documento['posicion_fecha'] = Input::get('posicion_fecha');
            $documento['publico'] = 1;
            $documento['estado'] = Input::get('estado');
            $documento['solicitante'] = Input::get('solicitante');
            $documento['pide_nro'] = Input::get('pide_nro');
            $documento['publico'] = Input::get('publico');
            $documento['nivel'] = Input::get('nivel');
            $documento['usuario_created_at'] = Auth::user()->id;
            $documento->save();

            return Response::json(array('rst'=>1, 'msj'=>'Registro actualizado correctamente'));
        }
    }

    public function postCambiarestado()
    {

        if ( Request::ajax() ) {

            $documento = Documento::find(Input::get('id'));
            $documento->usuario_created_at = Auth::user()->id;
            $documento->estado = Input::get('estado');
            $documento->save();
           
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
