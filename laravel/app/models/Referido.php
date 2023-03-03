<?php

class Referido extends Base
{
    public $table = "referidos";

    public function getReferido(){
        DB::beginTransaction();

        DB::commit();

        return  array(
            'rst'=>1,
            'msj'=>'Registro realizado con éxito'
        );
    }
    
     public static function getListarCount( $array )
    {
        $sSql=" SELECT COUNT(r.id) cant
                FROM (SELECT id, estado, referido, MIN(fecha_hora_referido) fecha_hora_referido
                FROM referidos r
                WHERE r.estado=1 AND r.referido!=''
                GROUP BY r.referido) r
                WHERE r.estado = 1 ";
        $sSql.= $array['where'];
        $oData = DB::select($sSql);
        return $oData[0]->cant;
    }
    
    public static function getListar( $array )
    {
        $sSql=" SELECT MIN(r.id) id, MIN(r.ruta_id) ruta_id, MIN(r.tabla_relacion_id) tabla_relacion_id, MIN(r.ruta_detalle_id) ruta_detalle_id
                ,r.referido, MIN(r.fecha_hora_referido) fecha_hora_referido
                FROM referidos r
                WHERE r.estado=1 AND r.referido!=''";
        $sSql.= $array['where']." GROUP BY r.referido ".
                $array['order'].
                $array['limit'];
        
        $oData = DB::select($sSql);
        return $oData;
    }
}
