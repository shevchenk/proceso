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

        $eventos =  DB::table('rutas_flujo_eventos AS rfe')
                    ->join('rutas AS r', 'r.ruta_flujo_id', '=', 'rfe.ruta_flujo_id')
                    ->where('r.id', $r['ruta_id'])
                    ->where('rfe.estado', 1)
                    ->get();

        dd($eventos, $eventos[0]->git);
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

        return $lista;
    }

}