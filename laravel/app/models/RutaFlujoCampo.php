<?php

class RutaFlujoCampo extends \Eloquent {
	public $table = "rutas_flujo_campos";

    public function Registrarcampos(){
        $r = Request::all();
        $id = $r['id'];
        $recorrido = count($r['campo_id']);
        $clasificadorTramite = ClasificadorTramite::find( $id );
        $lista = array();

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
            
            
            $RutaFlujoCampo->estado = 1;
            $RutaFlujoCampo->save();

            array_push( $lista, $RutaFlujoCampo->id );
        }
        DB::commit();

        return $lista;
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
            ->orderBy('id')
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