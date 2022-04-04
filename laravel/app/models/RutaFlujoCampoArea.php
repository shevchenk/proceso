<?php

class RutaFlujoCampoArea extends \Eloquent {
	public $table = "rutas_flujo_campos_areas";

    public function Asignarcampos(){
        $r = Request::all();
        $id = $r['id'];
        $ruta_flujo_campo_id = ''; $area_id = ''; $modificar = ''; $visualizar = ''; $norden = '';
        $cantidad = 0;
        if( isset($r['norden']) ){
            $area_id = $r['area_id'];
            $modificar = $r['modificar'];
            $visualizar = $r['visualizar'];
            $norden = $r['norden'];
            $ruta_flujo_id = $r['ruta_flujo_id'];
            $ruta_flujo_campo_id = $r['ruta_flujo_campo_id'];
            $cantidad = count($visualizar);
        }

        DB::beginTransaction();
            $ruta_flujo_id_aux = '';
        for ($i=0; $i < $cantidad; $i++) { 
            if( $ruta_flujo_id_aux != $ruta_flujo_id[$i] ){
                $ruta_flujo_id_aux = $ruta_flujo_id[$i];
                $sql = "UPDATE rutas_flujo_campos_areas 
                        SET estado = 0, updated_at = now(), usuario_updated_at = ".Auth::user()->id." 
                        WHERE clasificador_tramite_id = ".$id."
                        AND ruta_flujo_id = '".$ruta_flujo_id_aux."'";
                DB::update($sql);
            }

            $RutaFlujoCampoArea =   RutaFlujoCampoArea::where( 'ruta_flujo_campo_id', $ruta_flujo_campo_id[$i] )
                                    ->where('clasificador_tramite_id', $id)
                                    ->where( 'area_id', $area_id[$i] )
                                    ->where( 'norden', $norden[$i] )
                                    ->where( 'ruta_flujo_id', $ruta_flujo_id[$i] )
                                    ->first();
            if( !isset($RutaFlujoCampoArea->id) ){
                $RutaFlujoCampoArea = new RutaFlujoCampoArea;
                $RutaFlujoCampoArea->clasificador_tramite_id = $id;
                $RutaFlujoCampoArea->usuario_created_at = Auth::user()->id;
                $RutaFlujoCampoArea->ruta_flujo_campo_id = $ruta_flujo_campo_id[$i];
                $RutaFlujoCampoArea->area_id = $area_id[$i];
                $RutaFlujoCampoArea->norden = $norden[$i];
                $RutaFlujoCampoArea->ruta_flujo_id = $ruta_flujo_id[$i];
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

    public function Asignarcampo(){
        $r = Request::all();
        DB::beginTransaction();
        $RutaFlujoCampoArea =   RutaFlujoCampoArea::where( 'ruta_flujo_campo_id', $r['ruta_flujo_campo_id'] )
                                ->where('clasificador_tramite_id', $r['clasificador_tramite_id'])
                                ->where( 'area_id', $r['area_id'] )
                                ->where( 'norden', $r['norden'] )
                                ->where( 'ruta_flujo_id', $r['ruta_flujo_id'] )
                                ->first();
        if( !isset($RutaFlujoCampoArea->id) ){
            $RutaFlujoCampoArea = new RutaFlujoCampoArea;
            $RutaFlujoCampoArea->clasificador_tramite_id = $r['clasificador_tramite_id'];
            $RutaFlujoCampoArea->usuario_created_at = Auth::user()->id;
            $RutaFlujoCampoArea->ruta_flujo_campo_id = $r['ruta_flujo_campo_id'];
            $RutaFlujoCampoArea->area_id = $r['area_id'];
            $RutaFlujoCampoArea->norden = $r['norden'];
            $RutaFlujoCampoArea->ruta_flujo_id = $r['ruta_flujo_id'];
        }
        else{
            $RutaFlujoCampoArea = RutaFlujoCampoArea::find( $RutaFlujoCampoArea->id );
            $RutaFlujoCampoArea->usuario_created_at = Auth::user()->id;
        }
        $RutaFlujoCampoArea->modificar = $r['modificar'];
        $RutaFlujoCampoArea->estado = $r['visualizar'];
        $RutaFlujoCampoArea->save();
        DB::commit();
    }

    public function Listarcamposareas(){
        $r = Request::all();
        $r['estado'] = 1;
        
        if( Input::has('soloareas') ){
            $campos = DB::table('rutas_flujo_detalle AS rfd')
                        ->join('areas AS a', function($join){
                            $join->on('a.id', '=', 'rfd.area_id');
                        })
                        ->leftJoin('rutas_flujo_detalle_micro AS rfdm', function($join){
                            $join->on('rfdm.ruta_flujo_id', '=', 'rfd.ruta_flujo_id')
                            ->on('rfdm.norden', '=', 'rfd.norden')
                            ->where('rfdm.estado', '=', '1');
                        })
                        ->leftJoin('rutas_flujo AS rf', function($join){
                            $join->on('rf.id', '=', 'rfdm.ruta_flujo_id2');
                        })
                        ->leftJoin('flujos AS f', function($join){
                            $join->on('f.id', '=', 'rf.flujo_id');
                        })
                        ->select('rfd.id AS ruta_flujo_detalle_id', 'rfd.area_id', 'a.nombre AS area', 'rfd.norden', 'rfd.archivado', 'rfd.detalle', 
                        DB::raw('GROUP_CONCAT( f.nombre, "^", rfdm.ruta_flujo_id2 SEPARATOR "^^") AS sub'))
                        ->where('rfd.estado', '=', '1')
                        ->where('rfd.ruta_flujo_id', '=', $r['ruta_flujo_id'])
                        ->groupBy('rfd.id')
                        ->orderByRaw('rfd.norden ASC')
                        ->get();
        }
        else{
            $campos = DB::table('rutas_flujo_detalle AS rfd')
                        ->join('areas AS a', function($join){
                            $join->on('a.id', '=', 'rfd.area_id');
                        })
                        ->leftJoin('rutas_flujo_detalle_micro AS rfdm', function($join){
                            $join->on('rfdm.ruta_flujo_id', '=', 'rfd.ruta_flujo_id')
                            ->on('rfdm.norden', '=', 'rfd.norden')
                            ->where('rfdm.estado', '=', '1');
                        })
                        ->leftJoin('rutas_flujo AS rf', function($join){
                            $join->on('rf.id', '=', 'rfdm.ruta_flujo_id2');
                        })
                        ->leftJoin('flujos AS f', function($join){
                            $join->on('f.id', '=', 'rf.flujo_id');
                        })
                        ->join('rutas_flujo_campos AS rfc', function($join) use($r){
                            $join->where('rfc.clasificador_tramite_id', '=', $r['id'])
                            ->where('rfc.estado', '=', '1');
                        })
                        ->leftJoin('rutas_flujo_campos_areas AS rfca', function($join) use($r){
                            $join->on('rfca.ruta_flujo_campo_id', '=', 'rfc.id')
                            ->on('rfca.area_id', '=', 'rfd.area_id')
                            ->where('rfca.ruta_flujo_id', '=', $r['ruta_flujo_id'])
                            ->where('rfca.clasificador_tramite_id', '=', $r['id']);
                            if( !Input::has('norden') ){
                                $join->on('rfca.norden', '=', 'rfd.norden');
                            }
                            else{
                                $join->whereExp("rfca.norden", "=", "CONCAT( '".$r['norden'].".',LPAD(rfd.norden, 2, '0') )");
                            }
                        })
                        ->select('rfd.id AS ruta_flujo_detalle_id', 'rfd.area_id', 'a.nombre AS area', 'rfd.norden', 'rfd.archivado', 'rfd.detalle', 
                        DB::raw('GROUP_CONCAT( f.nombre, "^", rfdm.ruta_flujo_id2 SEPARATOR "^^") AS sub'), 'rfc.id AS ruta_flujo_campo_id', 
                        'rfc.campo', 'rfc.orden', 'rfc.tipo', 'rfca.id', DB::raw('IFNULL(rfca.estado,0) AS estado'), 'rfca.modificar')
                        ->where('rfd.estado', '=', '1')
                        ->where('rfd.ruta_flujo_id', '=', $r['ruta_flujo_id'])
                        ->groupBy('rfd.id', 'rfc.id')
                        ->orderByRaw('rfd.norden ASC, rfc.orden ASC')
                        ->get();
        }
                
        return $campos;
    }
}