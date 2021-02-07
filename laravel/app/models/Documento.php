<?php

class Documento extends Base
{
    public $table = "documentos";
    public static $where =['id', 'nombre', 'area', 'posicion', 'posicion_fecha', 'estado'];
    public static $select =['id', 'nombre', 'area', 'posicion', 'posicion_fecha', 'estado'];
    
    public static function getCargarCount( $array )
    {
        $sSql=" SELECT  COUNT(doc.id) cant
                FROM documentos doc
                LEFT JOIN areas a ON a.id = doc.area_id
                WHERE 1=1 ";
        $sSql.= $array['where'];
        $oData = DB::select($sSql);
        return $oData[0]->cant;
    }

    public static function getCargar( $array )
    {
        $sSql=" SELECT doc.id, doc.nombre, doc.tipo, doc.area, doc.posicion, doc.posicion_fecha, doc.estado, 
               doc.tipo AS tipos, doc.area_id, a.nombre as areas, doc.nemonico, doc.solicitante, doc.pide_nro,
                (
                CASE doc.posicion
                    WHEN '0' THEN 'Centro'
                    WHEN '1' THEN 'Izquierda'
                    ELSE 'Derecha'
                END
                ) as posiciones,
                (
                CASE doc.posicion_fecha
                    WHEN '0' THEN 'Sin Fecha'
                    WHEN '1' THEN 'Arriba Izquierda'
                    WHEN '2' THEN 'Arriba Derecha'
                    WHEN '3' THEN 'Abajo Izquierda'
                    ELSE 'Abajo Derecha'
                END
                ) as posiciones_fecha, doc.publico

                FROM documentos doc
                LEFT JOIN areas a ON a.id = doc.area_id
                                 
                WHERE 1=1 ";
        $sSql.= $array['where'].
                $array['order'].
                $array['limit'];
        $oData = DB::select($sSql);
        return $oData;
    }

    public static function getDocumento(){
        $r=DB::table('documentos')
                ->select('id', 'nombre', 'estado', 'area', 'posicion', 'posicion_fecha', 'tipo', 'solicitante', 'pide_nro AS val')
                ->where( 
                    function($query){
                        if ( Input::get('estado') ) {
                            $query->where('estado','=','1');
                        }

                        if ( Input::get('tipo')=='Ingreso' ) {
                            $query->where('tipo','=','Ingreso');
                        }
                        else{
                            $query->where('tipo','=','Salida');
                        }

                        if ( Input::has('solicitante') ) {
                            $query->where('solicitante','=', Input::get('solicitante'));
                        }

                        if ( Input::has('area_id') ) {
                            $query->where('area_id','=', Input::get('area_id'));
                        }
                    }
                )
                ->orderBy('nombre')
                ->get();
                
        return $r;
    }

}
