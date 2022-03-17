<?php

class RutaFlujoCampo extends \Eloquent {
	public $table = "rutas_flujo_campos";

    public function Registrarcampos(){
        $r = Request::all();
        $id = $r['id'];
        $clasificadorTramite = ClasificadorTramite::find( $id );
        $lista = array();
        $orden = 0.0;
        $orden_aux = 0;
        $recorrido = 0;
        if( isset($r['campo_id']) ){
            $recorrido = count($r['campo_id']);
        }

        DB::beginTransaction();
            $sql = "UPDATE rutas_flujo_campos SET estado = 0, updated_at = now(), usuario_updated_at = ".Auth::user()->id." WHERE clasificador_tramite_id = ".$id;
            DB::update($sql);
        for ($i=0; $i < $recorrido; $i++) { 
            $RutaFlujoCampo = array();
            if( $r['campo_id'][$i] == 0 ){
                $RutaFlujoCampo = new RutaFlujoCampo;
                $RutaFlujoCampo->usuario_created_at = Auth::user()->id;
            }
            else{
                $RutaFlujoCampo = RutaFlujoCampo::find( $r['campo_id'][$i] );
                $RutaFlujoCampo->usuario_created_at = Auth::user()->id;
            }
            $RutaFlujoCampo->ruta_flujo_id = $clasificadorTramite->ruta_flujo_id;
            $RutaFlujoCampo->clasificador_tramite_id = $clasificadorTramite->id;
            $RutaFlujoCampo->campo = $r['campo'][$i];
            $RutaFlujoCampo->col = $r['col'][$i];
            $RutaFlujoCampo->obligar = $r['obligar'][$i];
            $RutaFlujoCampo->capacidad = $r['capacidad'][$i];
            $RutaFlujoCampo->tipo = $r['tipo'][$i];
            $RutaFlujoCampo->lista = $r['lista'][$i];
            
            if( $r['tipo'][$i] == 0 ){
                $orden_aux ++ ;
                $orden = $orden_aux;
            }
            else{
                $orden += 0.01;
            }
            $RutaFlujoCampo->orden = $orden;
            
            $RutaFlujoCampo->estado = 1;
            $RutaFlujoCampo->save();

            array_push( $lista, $RutaFlujoCampo->id );
        }
        DB::commit();

        return $lista;
    }

    public function Mostrarcampos(){
        $r = Request::all();
        $r['estado'] = 1;
        
        $campos=
            DB::table('rutas_flujo_campos AS rfc')
            ->join('rutas_flujo_campos_areas AS rfca', function($join) use($r) {
                $join->on('rfca.ruta_flujo_campo_id', '=', 'rfc.id');
                if( isset( $r['area_id'] ) ){
                    $join->where('rfca.area_id', '=', $r['area_id']);
                }
                $join->where('rfca.estado', '=', '1');
            })
            ->leftJoin('rutas_campos AS rc', function($join) use($r) {
                $join->on('rc.ruta_flujo_campo_id', '=', 'rfc.id');
                if( isset( $r['ruta_id'] ) ){
                    $join->where('rc.ruta_id', '=', $r['ruta_id']);
                }
                $join->where('rc.estado', '=', '1');
            })
            ->select('rfc.id', 'rfc.campo', 'rfc.col', 'rfc.obligar', 'rfc.capacidad', 'rfc.tipo', 'rfc.lista', 'rfca.modificar', 'rc.campo_valor', 'rc.id AS ruta_campo_id')
            ->where( 
                function($query) use( $r ){
                    if ( isset( $r['estado'] ) ) {
                        $query->where('rfc.estado','=','1');
                    }

                    if ( isset( $r['ruta_flujo_id'] ) ) {
                        $query->where('rfc.ruta_flujo_id','=', $r['ruta_flujo_id']);
                    }
                }
            )
            ->groupBy('rfc.id')
            ->orderBy('rfc.orden')
            ->get();
                
        return $campos;
    }

    public function Listarcampos(){
        $r = Request::all();
        $r['estado'] = 1;
        
        $campos=
            DB::table('rutas_flujo_campos')
            ->select('id', 'campo', 'col', 'obligar', 'capacidad', 'tipo', 'lista')
            ->where( 
                function($query) use( $r ){
                    $id = $r['id'];
                    if ( isset( $r['estado'] ) ) {
                        $query->where('estado','=','1');
                    }

                    $query->where('clasificador_tramite_id', '=', $id);
                }
            )
            ->orderBy('orden')
            ->get();
                
        return $campos;
    }

    public function Listarareas(){
        $r = Request::all();
        $r['estado'] = 1;
        $id = $r['id'];
        $clasificadorTramite = ClasificadorTramite::find( $id );
        
        $areas=
            DB::table('rutas_flujo AS rf')
            ->join('rutas_flujo_detalle AS rfd', 'rfd.ruta_flujo_id', '=', 'rf.id')
            ->join('areas AS a', 'a.id', '=', 'rfd.area_id')
            ->select('a.id', 'a.nombre')
            ->where('rfd.estado', '=', 1)
            ->where( 
                function($query) use( $r, $clasificadorTramite ){
                    $id = $r['id'];
                    if ( isset( $r['estado'] ) ) {
                        $query->where('a.estado','=','1');
                    }

                    $query->where('rf.id', '=', $clasificadorTramite->ruta_flujo_id);
                }
            )
            ->groupBy('a.id', 'a.nombre')
            ->orderBy('a.nombre')
            ->get();
                
        return $areas;
    }
}