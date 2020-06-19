<?php

class RutaDetalleMicro extends Base
{
    public $table = "rutas_detalle_micro";
    public static $where = ['id', 'ruta_flujo_id', 'ruta_id','norden', 'estado'];
    public static $selec = ['id', 'ruta_flujo_id', 'ruta_id','norden', 'estado'];
    
        public static function getListar(){
        $ruta_id = Input::get('ruta_id');
        $norden = Input::get('norden');
        $sql = "INSERT INTO rutas_detalle_micro (ruta_flujo_id, ruta_id, norden, estado, created_at, usuario_created_at)
                SELECT rfdm.ruta_flujo_id2, $ruta_id, $norden, 1, NOW(), 1
                FROM rutas r
                INNER JOIN rutas_flujo rf ON rf.id = r.ruta_flujo_id 
                INNER JOIN rutas_flujo_detalle_micro rfdm ON rfdm.ruta_flujo_id = rf.id 
                LEFT JOIN (
                    SELECT rdm.ruta_flujo_id, rdm.ruta_id, rdm.norden
                    FROM rutas_detalle_micro rdm
                    INNER JOIN rutas_flujo rf ON rf.id = rdm.ruta_flujo_id
                    WHERE rdm.estado = 1
                ) v ON v.ruta_id = r.id AND v.norden = rfdm.norden
                WHERE r.id = $ruta_id
                AND rfdm.norden = $norden
                AND rfdm.estado = 1
                AND v.ruta_id IS NULL";
        DB::insert($sql);

        $rdm=DB::table('rutas_detalle_micro as rdm')
                ->join('rutas_flujo as rf','rf.id','=','rdm.ruta_flujo_id')
                ->join('flujos as f','f.id','=','rf.flujo_id')
                ->select('f.nombre','rdm.id','rdm.estado')
                ->where( 'rdm.ruta_id','=', $ruta_id)
                ->where( 'rdm.norden','=', $norden)
                ->where( 'rdm.estado','=',1)
                ->get();
        return $rdm;
    }

}
