<?php

class Local extends Base
{
    public $table = "locales";
    public static $where = ['id', 'local', 'estado'];
    public static $selec = ['id', 'local', 'estado'];

    public static function getCargarCount( $array )
    {
        $sSql=" SELECT  COUNT(l.id) cant
                FROM locales l
                WHERE 1=1 ";
        $sSql.= $array['where'];
        $oData = DB::select($sSql);
        return $oData[0]->cant;
    }

    public static function getCargar( $array )
    {
        $sSql=" SELECT l.id, l.local, l.direccion, l.fecha_inicio, l.fecha_final, l.estado
                FROM locales l
                WHERE 1=1 ";
        $sSql.= $array['where'].
                $array['order'].
                $array['limit'];
        $oData = DB::select($sSql);
        return $oData;
    }

     public function getListar(){
        $local=DB::table('locales')
                ->select('id','local AS nombre','estado')
                ->where( 
                    function($query){
                        if ( Input::get('estado') ) {
                            $query->where('estado','=','1');
                        }
                        if ( Input::has('usuario_local') ) {
                            $locales = Auth::user()->local_id;
                            $query->whereRaw('FIND_IN_SET(id , "'.$locales.'") > 0 ');
                        }
                        if ( Input::has('area_local_id') ) {
                            $area = AREA::find(Input::get('area_local_id'));
                            $locales = $area->locales_id;
                            $query->whereRaw('FIND_IN_SET(id , "'.$locales.'") > 0 ');
                        }
                        
                    }
                )
                ->orderBy('local')
                ->get();
                
        return $local;
    }

    public function getLocal(){
        $local_inventario=DB::table('inventario_local')
                ->select('id','local','estado')
                ->where( 
                    function($query){
                        if ( Input::get('estado') ) {
                            $query->where('estado','=','1');
                        }
                    }
                )
                ->orderBy('local')
                ->get();
                
        return $local_inventario;
    }

}
