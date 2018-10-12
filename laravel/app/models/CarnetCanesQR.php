<?php
class CarnetCanesQR extends Base
{
    public $table = "carnet_canes";
    public static $where =['id', 'serie', 'paterno', 'materno', 'nombre', 'fecha_entrega', 'fecha_nace', 'sexo', 'raza', 'foto', 'estado'];
    public static $selec =['id', 'serie', 'paterno', 'materno', 'nombre', 'fecha_entrega', 'fecha_nace', 'sexo', 'raza', 'foto', 'estado'];

    public static function verData($id)
    {
        $sql = "SELECT * 
				FROM carnet_canes
					WHERE id = $id;";        
        $r = DB::select($sql);
        return $r;
    }
}