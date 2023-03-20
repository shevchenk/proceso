<?php
use FC\ServicioController;

class RutaCampo extends \Eloquent {
	public $table = "rutas_campos";

    public function Guardarrutacampos(){
        $r = Request::all();
        $recorrido = count($r['ruta_campo_id']);
        $lista = array(
            'ruta_flujo_campo_id' => array(),
            'ruta_campo_id' => array(),
        );

        DB::beginTransaction();
            
        for ($i=0; $i < $recorrido; $i++) { 
            $RutaCampo = array();
            if( $r['ruta_campo_id'][$i] == 0 ){
                $RutaCampo = new RutaCampo;
                $RutaCampo->usuario_created_at = Auth::user()->id;
                $RutaCampo->ruta_id = $r['ruta_id'];
            }
            else{
                $RutaCampo = RutaCampo::find( $r['ruta_campo_id'][$i] );
                $RutaCampo->usuario_created_at = Auth::user()->id;
            }
            
            $RutaCampo->ruta_flujo_campo_id = $r['ruta_flujo_campo_id'][$i];
            $RutaCampo->campo_valor = $r['campo_valor'][$i];
            $RutaCampo->estado = 1;
            $RutaCampo->save();

            array_push( $lista['ruta_flujo_campo_id'], $RutaCampo->ruta_flujo_campo_id );
            array_push( $lista['ruta_campo_id'], $RutaCampo->id );
        }

        $eventos =  DB::table('rutas_flujo_eventos AS rfe')
                    ->join('rutas AS r', 'r.ruta_flujo_id', '=', 'rfe.ruta_flujo_id')
                    ->where('r.id', $r['ruta_id'])
                    ->where('rfe.estado', 1)
                    ->get();
        $anular = 0;

        foreach( $eventos as $key => $value ){
            $valued = explode("^^", $value->condicion_valida);
            $cant = 0;
            $ruta_flujo_campo_id_aux = array();
            $ruta_flujo_campo_id_sql = array();
            $aux = array();
            $sql =  DB::table('rutas_campos')
                    ->select(DB::raw('COUNT(id) AS cant, GROUP_CONCAT(campo_valor) datos, GROUP_CONCAT(DISTINCT(ruta_flujo_campo_id) ORDER BY ruta_flujo_campo_id) ruta_flujo_campo_id'))
                    ->where('ruta_id', $r['ruta_id'])
                    ->where('estado', 1)
                    ->where(
                        function($query) use( $valued, $cant, $ruta_flujo_campo_id_aux ){
                            foreach( $valued as $k => $v ){
                                $vd = explode("|", $v);

                                if($k == 0){
                                    $query->where('ruta_flujo_campo_id', '=', substr($vd[1], 1))
                                    ->where('campo_valor', $vd[2], $vd[3]);
                                }
                                else{
                                    $query->orWhere('ruta_flujo_campo_id', '=', substr($vd[1], 1))
                                    ->where('campo_valor', $vd[2], $vd[3]);
                                }
                                
                                array_push( $ruta_flujo_campo_id_aux, substr($vd[1], 1) );
                                
                                if( $vd[0] == 'OR' ){
                                    array_push($aux, $vd[3]);
                                }
                                else{
                                    $cant++;
                                }
                            }
                            
                            Session::set('ruta_flujo_campo_id_aux', $ruta_flujo_campo_id_aux);
                        }
                    )
                    ->groupBy('ruta_id')
                    ->first();
            
            $ruta_flujo_campo_id_aux = Session::get('ruta_flujo_campo_id_aux');
            sort($ruta_flujo_campo_id_aux);
            if( isset($sql->ruta_flujo_campo_id) AND $sql->ruta_flujo_campo_id != '' ){
                $ruta_flujo_campo_id_sql = explode(',', $sql->ruta_flujo_campo_id);
            }
            
            $resultado = array_diff($ruta_flujo_campo_id_aux, $ruta_flujo_campo_id_sql); // validación de los campos según eventos
            $resultado2 = array_intersect($ruta_flujo_campo_id_aux, $lista['ruta_flujo_campo_id']); // validacion de los cammpos enviados almenos 1
            
            $ruta = array();

            if( $resultado == [] AND count($resultado2) > 0  ){
                $ruta = explode( "@", str_replace( "fn:", "", $value->url_evento) );
            }

            if( isset($ruta[0]) AND isset($ruta[1]) AND $ruta[0] != '' AND $ruta[1] != '' ){ //Validación y ejecución de API
                $RutaCampo = RutaCampo::where('ruta_id', $r['ruta_id'])->where('ruta_flujo_campo_id', $_ENV['IDSERVICIO'])->first();
                $RutaCampoTeso = RutaCampo::where('ruta_id', $r['ruta_id'])->where('ruta_flujo_campo_id', $_ENV['IDSERVICIOTESO'])->first();
                $RutaCampoAcad = RutaCampo::where('ruta_id', $r['ruta_id'])->where('ruta_flujo_campo_id', $_ENV['IDSERVICIOACAD'])->first();
                $matricula_id = 0;
                $obs_tesoreria = "";
                $obs_academica = "";
                if( isset($RutaCampo->campo_valor) ){
                    $matricula_id = $RutaCampo->campo_valor;
                }
                if( isset($RutaCampoTeso->campo_valor) ){
                    $obs_tesoreria = $RutaCampo->campo_valor;
                }
                if( isset($RutaCampoAcad->campo_valor) ){
                    $obs_academica = $RutaCampo->campo_valor;
                }
                $datos = array(
                    "opcion" => $ruta[1],
                    "matricula_id" => $matricula_id,
                    "obs_tesoreria" => $obs_tesoreria,
                    "obs_academica" => $obs_academica,
                    "ruta_id" => $r['ruta_id'],
                    "dni" => Auth::user()->dni
                );

                $api = new Api\ApiController;
                $objArr = $api->{$datos['opcion']}($datos);

                if( isset($objArr['rst']) AND $objArr['rst']*1 == 1 ){ 
                    if( isset($objArr['anular']) AND $objArr['anular'] == 1 ){
                        $anular = 1;
                    }
                }
                else{
                    DB::rollBack();
                    return array(
                        'rst'   => 2,
                        'msj'   => 'No se pudo completar, vuelva a intentarlo',
                        'data' => $lista,
                        'obj' => $objArr
                    );
                }
            }
        }

        DB::commit();

        return array(
            'rst'   => 1,
            'msj'   => 'Campos guardados',
            'data' => $lista,
            'anular' => $anular,
        );
    }

}