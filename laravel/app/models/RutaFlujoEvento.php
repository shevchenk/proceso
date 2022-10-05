<?php

class RutaFlujoEvento extends \Eloquent {
	public $table = "rutas_flujo_eventos";

    public function Eliminarevento(){
        $r = Request::all();
        $id = $r['ruta_flujo_evento_id'];

        $rutaFlujoEvento = RutaFlujoEvento::find($id);
        $rutaFlujoEvento->estado = 0;
        $rutaFlujoEvento->usuario_updated_at = Auth::user()->id;
        $rutaFlujoEvento->save();
    }

    public function Guardarevento(){
        $r = Request::all();
        $id = $r['id'];
        $clasificadorTramite = ClasificadorTramite::find( $id );
        $condicion_valida = '';
        $condicion_usuario = '';
        if( is_array($r['id_campo']) ){
            foreach( $r['id_campo'] as $index => $value ){
                if($index == 0){
                    $condicion_valida = '|C'.$r['id_campo'][$index].'|'.$r['condicion'][$index].'|'.$r['valor_condicion'][$index];
                }
                else{
                    $condicion_valida .= '**'.$r['anidado'][($index-1)].'|C'.$r['id_campo'][$index].'|'.$r['condicion'][$index].'|'.$r['valor_condicion'][$index];
                }
            }
        }

        $rutaFlujoEvento = new RutaFlujoEvento;
        $rutaFlujoEvento->condicion_valida = $condicion_valida;
        $rutaFlujoEvento->url_evento = $r['url_evento'];
        $rutaFlujoEvento->ruta_flujo_id = $r['ruta_flujo_id'];
        $rutaFlujoEvento->clasificador_tramite_id = $r['id'];
        $rutaFlujoEvento->usuario_created_at = Auth::user()->id;
        $rutaFlujoEvento->save();
    }

    public function Listareventos(){
        $r = Request::all();
        $r['estado'] = 1;
        
        $eventos=
            DB::table('rutas_flujo_eventos AS rfe')
            ->select('rfe.id', 'rfe.url_evento', 'rfe.condicion_valida')
            ->where( 
                function($query) use( $r ){
                    if ( isset( $r['estado'] ) AND $r['estado'] == 1 ) {
                        $query->where('rfe.estado','=','1');
                    }

                    if ( isset( $r['ruta_flujo_id'] ) ) {
                        $query->where('rfe.ruta_flujo_id','=', $r['ruta_flujo_id']);
                    }

                    if ( isset( $r['id'] ) ) { //id es porq se viene almacenando de esa forma la clasificación del trámite
                        $query->where('rfe.clasificador_tramite_id','=', $r['id']);
                    }
                }
            )
            ->groupBy('rfe.id')
            ->orderBy('rfe.id', 'desc')
            ->get();
                
        return $eventos;
    }

    /*public function Listarareas(){
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
    }*/
}