<?php

class TipoTramite extends Base
{
    public $table = "tipo_tramite";
    public static $where =['id', 'nombre_tipo_tramite', 'estado'];
    public static $selec =['id', 'nombre_tipo_tramite', 'estado'];
    
    public static function getCargarCount( $array )
    {
        $sSql=" SELECT  COUNT(tt.id) cant
                FROM tipo_tramite tt
                WHERE 1=1 ";
        $sSql.= $array['where'];
        $oData = DB::select($sSql);
        return $oData[0]->cant;
    }
    public static function getCargar( $array )
    {
        $sSql=" SELECT tt.id, tt.nombre_tipo_tramite nombre, tt.estado, tt.solicitante, tt.inicia, tt.seguimiento, tt.cant_solicitante
                FROM tipo_tramite tt
                WHERE 1=1 ";
        $sSql.= $array['where'].
                $array['order'].
                $array['limit'];
        $oData = DB::select($sSql);
        return $oData;
    }
    public function getTipoTramite(){
        $tipotramite=DB::table('tipo_tramite')
                ->select('id','nombre_tipo_tramite as nombre','estado', 'cant_solicitante AS evento')
                ->where( 
                    function($query){
                        if ( Input::get('estado') ) {
                            $query->where('estado','=','1');
                        }

                        if ( Input::has('solicitante') ) {
                            $query->where('solicitante','=', Input::get('solicitante'));
                            if ( Input::has('tipo') AND  Input::get('tipo') == 'Ingreso') {
                                $query->where('inicia','!=', 'Institución');
                            }
                            elseif ( Input::has('tipo') AND  Input::get('tipo') == 'Salida') {
                                $query->where('inicia','!=', 'Cliente');
                            }
                        }
                    }
                )
                ->orderBy('nombre_tipo_tramite')
                ->get();
                
        return $tipotramite;
    }
    
        public static function getCargar_tipo( )
    {
        $sSql=" SELECT tt.id, tt.nombre_tipo_tramite nombre, tt.estado
                FROM tipo_tramite tt
                WHERE tt.estado<2 ";
        $oData = DB::select($sSql);
        return $oData;
    }
}
