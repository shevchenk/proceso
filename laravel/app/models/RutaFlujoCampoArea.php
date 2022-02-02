<?php

class RutaFlujoCampoArea extends \Eloquent {
	public $table = "rutas_flujo_campos_areas";

    public function Asignarcampos(){
        $r = Request::all();
        $id = $r['id'];
        $ruta_flujo_campo_id = ''; $area_id = ''; $modificar = '';
        $cantidad = 0;
        if( isset($r['ruta_flujo_campo_id']) ){
            $ruta_flujo_campo_id = $r['ruta_flujo_campo_id'];
            $cantidad = count($ruta_flujo_campo_id);
            
            $area_id = $r['area_id'];
            $modificar = $r['modificar'];
        }


        DB::beginTransaction();
            $sql = "UPDATE rutas_flujo_campos_areas SET estado = 0, updated_at = now(), usuario_updated_at = ".Auth::user()->id." WHERE clasificador_tramite_id = ".$id;
            DB::update($sql);
        for ($i=0; $i < $cantidad; $i++) { 
            $RutaFlujoCampoArea =   RutaFlujoCampoArea::where( 'ruta_flujo_campo_id', $ruta_flujo_campo_id[$i] )
                                    ->where( 'area_id', $area_id[$i] )
                                    ->first();
            if( !isset($RutaFlujoCampoArea->id) ){
                $RutaFlujoCampoArea = new RutaFlujoCampoArea;
                $RutaFlujoCampoArea->clasificador_tramite_id = $id;
                $RutaFlujoCampoArea->usuario_created_at = Auth::user()->id;
                $RutaFlujoCampoArea->ruta_flujo_campo_id = $ruta_flujo_campo_id[$i];
                $RutaFlujoCampoArea->area_id = $area_id[$i];
            }
            else{
                $RutaFlujoCampoArea = RutaFlujoCampoArea::find( $RutaFlujoCampoArea->id );
                $RutaFlujoCampoArea->usuario_created_at = Auth::user()->id;
            }
            $RutaFlujoCampoArea->modificar = $modificar[$i];
            $RutaFlujoCampoArea->estado = 1;
            $RutaFlujoCampoArea->save();

        }
        DB::commit();

    }

    public function Listarcamposareas(){
        $r = Request::all();
        $r['estado'] = 1;
        
        $campos=
            DB::table('rutas_flujo_campos_areas')
            ->select('id', 'area_id', 'ruta_flujo_campo_id', 'modificar')
            ->where( 
                function($query) use( $r ){
                    $id = $r['id'];
                    if ( isset( $r['estado'] ) ) {
                        $query->where('estado','=','1');
                    }

                    $query->where('clasificador_tramite_id', '=', $id);
                }
            )
            ->orderBy('area_id', 'ruta_flujo_campo_id')
            ->get();
                
        return $campos;
    }
}