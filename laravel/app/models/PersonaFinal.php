<?php

class PersonaFinal extends Base
{
    public $table = "personas";

    public static function getAreas($personaId) {
        //subconsulta
        $sql = DB::table('cargo_persona as cp')
                ->join(
                        'cargos as c', 'cp.cargo_id', '=', 'c.id'
                )
                ->join(
                        'area_cargo_persona as acp', 'cp.id', '=', 'acp.cargo_persona_id'
                )
                ->join(
                        'areas as a', 'acp.area_id', '=', 'a.id'
                )
                ->select(
                        DB::raw("c.id, c.nombre, GROUP_CONCAT(a.id) AS info")
                )
                ->whereRaw("cp.persona_id=$personaId AND cp.estado=1 AND c.estado=1 AND acp.estado=1")
                ->groupBy('c.id')
                ->get();

        return $sql;
    }
}
