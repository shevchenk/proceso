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
            $cant = 0;
            $aux = array();
            $sql =  DB::table('rutas_campos')
                    ->select(DB::raw('COUNT(id) AS cant, GROUP_CONCAT(campo_valor) datos'))
                    ->where('ruta_id', $r['ruta_id'])
                    ->where('estado', 1)
                    ->where(
                        function($query) use( $valued, $cant ){
                            foreach( $valued as $k => $v ){
                                $vd = explode("|", $v);

                                if($k == 0){
                                    $query->where('ruta_flujo_campo_id', '=', substr($vd[1], 1))
                                    ->where('campo_valor', $vd[2], $vd[3]);
                                }
                                else{
                                    $query->orWhere('ruta_flujo_campo_id', '=', substr($vd[1], 1))
                                    ->where('campo_valor', $vd[2], $vd[3]);
                                }

                                if( $vd[0] == 'OR' ){
                                    array_push($aux, $vd[3]);
                                }
                                else{
                                    $cant++;
                                }
                            }
                        }
                    )
                    ->groupBy('ruta_id')
                    ->first();
            if( isset($aux[0]) AND $aux[0]!='' ){
                $ar = array();
                if( $sql->cant > 0 ){
                    $ab = explode(",",$sql->datos);
                    $ar = array_intersect($aux, $ab);
                }
                
                if( isset($ar[0]) AND $ar[0] != '' ){
                    $sql->cant = 0;
                    dd('h1', $value->url_evento);
                }
            }
            
            if( $sql->cant > 0 AND $cant <= $sql->cant ){
                dd('h2', $value->url_evento);
            }
        }

        return $lista;
    }

}