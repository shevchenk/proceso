<?php
class RutaFlujoDetalle extends Eloquent
{
    public $table="rutas_flujo_detalle";

    public static function FinalizarAnular()
    {
        $ruta_flujo_detalle_id = Input::get('ruta_flujo_detalle_id');
        $valor = Input::get('valor');

        $rd = RutaFlujoDetalle::find( $ruta_flujo_detalle_id );
        $rd->archivado = $valor;
        $rd->usuario_updated_at = Auth::user()->id;
        $rd->save();
    }

}
?>
