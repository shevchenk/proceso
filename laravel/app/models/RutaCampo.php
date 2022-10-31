<?php

class RutaCampo extends \Eloquent {
	public $table = "rutas_campos";

    public function Guardarrutacampos(){
        $r = Request::all();
        $recorrido = count($r['ruta_campo_id']);
        $lista = array(
            'ruta_flujo_campo_id' => array(),
            'ruta_campo_id' => array()
        );

        DB::beginTransaction();
            
        for ($i=0; $i < $recorrido; $i++) { 
            $RutaCampo = array();
            if( $r['ruta_campo_id'][$i] == 0 ){
                $RutaCampo = new RutaCampo;
                $RutaCampo->usuario_created_at = Auth::user()->id;
                $RutaCampo->ruta_id = $r['ruta_id'];
            }
            else{
                $RutaCampo = RutaCampo::find( $r['ruta_campo_id'][$i] );
                $RutaCampo->usuario_created_at = Auth::user()->id;
            }
            
            $RutaCampo->ruta_flujo_campo_id = $r['ruta_flujo_campo_id'][$i];
            $RutaCampo->campo_valor = $r['campo_valor'][$i];
            $RutaCampo->estado = 1;
            $RutaCampo->save();

            array_push( $lista['ruta_flujo_campo_id'], $RutaCampo->ruta_flujo_campo_id );
            array_push( $lista['ruta_campo_id'], $RutaCampo->id );
        }
        DB::commit();


        $eventos =  DB::table('rutas_flujo_eventos AS rfe')
                    ->join('rutas AS r', 'r.ruta_flujo_id', '=', 'rfe.ruta_flujo_id')
                    ->where('r.id', $r['ruta_id'])
                    ->where('rfe.estado', 1)
                    ->get();

        foreach( $eventos as $key => $value ){
            $valued = explode("^^", $value->condicion_valida);
            $value->url_evento;
            $cant = 0;
            $aux = '';
            $sql =  DB::table('rutas_campos')
                    ->select(DB::raw('COUNT(id) AS cant'))
                    ->where('ruta_id', $r['ruta_id'])
                    ->where('estado', 1)
                    ->where(
                        function($query) use( $valued ){
                            foreach( $valued as $k => $v ){
                                $vd = explode("|", $v);
                                $cant++;
                                if($k == 0){
                                    $query->where('ruta_flujo_campo_id', '=', substr($vd[1], 1))
                                    ->where('campo_valor', $vd[2], $vd[3]);
                                }
                                else{
                                    $query->orWhere('ruta_flujo_campo_id', '=', substr($vd[1], 1))
                                    ->where('campo_valor', $vd[2], $vd[3]);
                                }

                                if( $vd[0] == 'OR' ){
                                    $aux = $vd[3];
                                }
                            }
                        }
                    );
            if( $aux != '' ){
                // addselect MAX(FIND_IN_SET("",)) AS res                
            }
            $sql->groupBy('ruta_id')
                ->get();   
        }
        

        
        /*$busqueda = array_search( "2705", array_column($datos, "codigo") );
        $busqueda++;
        while( isset($datos[$busqueda]) AND $datos[$busqueda]['operador'] != '' ){
            $busqueda++;
        }*/
        dd($datos[$busqueda-1]);

        return $lista;
    }

}