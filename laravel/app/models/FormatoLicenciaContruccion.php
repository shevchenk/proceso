<?php

class FormatoLicenciaContruccion extends Base
{
    public $table = "licencia_construccion";
    public static $where =['id', 'persona', 'correlativo', 'expediente', 'fecha_emision', 'fecha_vence', 'licencia_edifica', 'modalidad', 'uso', 'zonifica', 'altura', 'propietario', 'distrito'];
    public static $selec =['id', 'persona', 'correlativo', 'expediente', 'fecha_emision', 'fecha_vence', 'licencia_edifica', 'modalidad', 'uso', 'zonifica', 'altura', 'propietario', 'distrito'];

    public static function verDataFormatoLicencia($id)
    {
        $sql = "SELECT lc.* 
				FROM licencia_construccion lc
					WHERE lc.id = $id;";        
        $r = DB::select($sql);
        return $r;
    }
}
