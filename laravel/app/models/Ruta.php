<?php
class Ruta extends Eloquent
{
    public $table="rutas";

    /**
     * Areas relationship
     */

    public static function getlistarDetalleRuta()
    {
        $result=DB::table('rutas_flujo_detalle as rfd')
                ->join('areas as a','a.id','=','rfd.area_id')
                ->select(DB::raw('CONCAT(rfd.norden," - ",a.nemonico) as nombre'),'rfd.norden as id')
                ->where('rfd.ruta_flujo_id','=',Input::get('ruta_flujo_id'))
                ->where('rfd.estado','=',1)
                ->get();
        return $result;
    }
    
    public static function getCargarMicro()
    {
        $result=DB::table('rutas_flujo_detalle_micro as rfdm')
                ->join('rutas_flujo as rf','rf.id','=','rfdm.ruta_flujo_id2')
                ->join('flujos as f','f.id','=','rf.flujo_id')
                ->join('rutas_flujo_detalle as rfd','rfd.id','=','rfdm.norden')
                ->select('rfdm.id','rfdm.ruta_flujo_id2','f.nombre','rfd.norden')
                ->where('rfdm.ruta_flujo_id','=',Input::get('ruta_flujo_id'))
                ->where('rfdm.estado','=',1)
                ->get();
        return $result;
    }
    
    public static function getCorrelativoAct($persona_id)
    {
        $result=DB::table('actividad_personal as ap')
                ->select(DB::raw('count(ap.id) as cant'))
                ->where('ap.persona_id','=',$persona_id)
                ->where('ap.estado','=',1)
                ->where('ap.area_id','=',Auth::user()->area_id)
                ->where(DB::raw('DATE(ap.created_at)'),'=',date('Y-m-d'))
                ->get();
        return $result[0]->cant;
    }
    
    public function crearRutaMicro(){
        DB::beginTransaction();
                            
                            $rdm= RutaDetalleMicro::where('id','=',Input::get('ruta_detalle_micro_id'))
                                            ->where('ruta_id','=',Input::get('ruta_id'))
                                            ->where('estado','=',1)->first();
                            //actualizar
                            //var_dump($rdm->id);exit();
                            $rd=RutaDetalle::where('norden','=',$rdm->norden)
                                            ->where('ruta_id','=',$rdm->ruta_id)
                                            ->where('estado','=',1)
                                            ->whereNull('dtiempo_final')
                                            ->first();
                            $rd->ruta_flujo_id=$rdm->ruta_flujo_id;
                            $rd->save();
                            
                            $rf= RutaFlujo::find($rd->ruta_flujo_id);
                            
                            $rutaflujodetalle = DB::table('rutas_flujo_detalle')
                                    ->where('ruta_flujo_id', '=', $rf->id)
                                    ->where('estado', '=', '1')
                                    ->orderBy('norden', 'ASC')
                                    ->get();
                            foreach ($rutaflujodetalle as $rfd) {
                                $cero='';
                                if($rfd->norden<10){
                                    $cero='0'; 
                                }
                                $rutaDetalle = new RutaDetalle;
                                $rutaDetalle['ruta_id'] = $rd->ruta_id;
                                $rutaDetalle['area_id'] = $rfd->area_id;
                                $rutaDetalle['tiempo_id'] = $rfd->tiempo_id;
                                $rutaDetalle['dtiempo'] = $rfd->dtiempo;
                                $rutaDetalle['ruta_flujo_id_dep']=$rdm->ruta_flujo_id;
                                $rutaDetalle['detalle']=$rfd->detalle;
                                $rutaDetalle['archivado']=$rfd->archivado;
                                $rutaDetalle['norden'] = $rd->norden.'.'.$cero.$rfd->norden;
                                $rutaDetalle['estado_ruta'] = $rfd->estado_ruta;
                                $rutaDetalle['usuario_created_at'] = Auth::user()->id;
                                $rutaDetalle->save();

                                $qrutaDetalleVerbo = DB::table('rutas_flujo_detalle_verbo')
                                        ->where('ruta_flujo_detalle_id', '=', $rfd->id)
                                        ->where('estado', '=', '1')
                                        ->orderBy('orden', 'ASC')
                                        ->get();
                                
                                if (count($qrutaDetalleVerbo) > 0) {
                                    foreach ($qrutaDetalleVerbo as $rdv) {
                                        $rutaDetalleVerbo = new RutaDetalleVerbo;
                                        $rutaDetalleVerbo['ruta_detalle_id'] = $rutaDetalle->id;
                                        $rutaDetalleVerbo['nombre'] = $rdv->nombre;
                                        $rutaDetalleVerbo['condicion'] = $rdv->condicion;
                                        $rutaDetalleVerbo['rol_id'] = $rdv->rol_id;
                                        $rutaDetalleVerbo['verbo_id'] = $rdv->verbo_id;
                                        $rutaDetalleVerbo['documento_id'] = $rdv->documento_id;
                                        $rutaDetalleVerbo['orden'] = $rdv->orden;
                                        $rutaDetalleVerbo['usuario_created_at'] = Auth::user()->id;
                                        $rutaDetalleVerbo->save();
                                    }
                                }
                            }
                            //2do nivel 
                            $rutaflujodetallemicro= RutaFlujoDetalleMicro::where('ruta_flujo_id','=',$rf->id)
                                                            ->where('estado','=',1)
                                                            ->orderBy('norden','ASC')->get();
                                            
                            foreach ($rutaflujodetallemicro as $rfdm) {
                                $cero='';
                                if($rfdm->norden<10){
                                    $cero='0';
                                }
                                $rdmcreate= new RutaDetalleMicro;
                                $rdmcreate->ruta_flujo_id=$rfdm->ruta_flujo_id2;
                                $rdmcreate->ruta_id=$rd->ruta_id;
                                $rdmcreate->norden=$rd->norden.'.'.$cero.$rfdm->norden;
                                $rdmcreate->usuario_created_at=Auth::user()->id;       
                                $rdmcreate->save();
                            }
                            //--
            DB::commit();
            return  array(
                    'rst'=>1,
                    'msj'=>'Registro realizado con éxito'
            );
    }
    
    public function crearRuta(){
        DB::beginTransaction();
        $codigounico="";
        $codigounico=Input::get('codigo');
        $id_documento='';        
        /*
        //$fecha_inicio=date('Y-m-d H:i:s');
        $fecha_inicio=date('Y-m-d H:i:s');
        if( $fecha_inicio=='0000-00-00 00:00:00' ){
            $fecha_inicio=date('Y-m-d H:i:s');
        }
        */
        $selectfecha = "SELECT NOW() as fecha;";
        $fecha_actual = DB::select($selectfecha);
        $fecha_inicio=$fecha_actual[0]->fecha;        

        if( Input::has('documento_id') ){
            $id_documento=Input::get('documento_id');
        }
        $ruta_id= Input::get('ruta_id');
        $rutadetalle_id= Input::get('rutadetalle_id');
        $tablarelacion_id= Input::get('tablarelacion_id');

        $tablaRelacion=DB::table('tablas_relacion as tr')
                        ->join(
                            'rutas as r',
                            'tr.id','=','r.tabla_relacion_id'
                        )
                        ->where('tr.id_union', '=', $codigounico)
                        ->where('r.ruta_flujo_id', '=', Input::get('ruta_flujo_id'))
                        ->where('tr.estado', '=', '1')
                        ->where('r.estado', '=', '1')
                        ->get();

        if(count($tablaRelacion)>0){
            DB::rollback();
            return  array(
                    'rst'=>2,
                    'msj'=>'El trámite ya fue registrado anteriormente'
                );
        }
        else{

        $tablaRelacion=new TablaRelacion;
        $tablaRelacion['software_id']=1;

        $tablaRelacion['id_union']=Input::get('codigo');
        
        $tablaRelacion['fecha_tramite']= $fecha_inicio; //Input::get('fecha_tramite');
        $tablaRelacion['tipo_persona']=Input::get('tipo_persona');

        if( Input::has('paterno') AND Input::has('materno') AND Input::has('nombre') ){
            $tablaRelacion['paterno']=Input::get('paterno');
            $tablaRelacion['materno']=Input::get('materno');
            $tablaRelacion['nombre']=Input::get('nombre');
        }
        elseif( Input::has('razon_social') AND Input::has('ruc') ){
            $tablaRelacion['razon_social']=Input::get('razon_social');
            $tablaRelacion['ruc']=Input::get('ruc');
        }
        elseif( Input::has('area_p_id') ){
            $tablaRelacion['area_id']=Input::get('area_p_id');
        }
        elseif( Input::has('carta_id') ){ // Este caso solo es para asignar carta inicio
            $tablaRelacion['area_id']=Auth::user()->area_id;
        }
        elseif( Input::has('razon_social') ){
            $tablaRelacion['razon_social']=Input::get('razon_social');
        }


        if( Input::has('referente') AND trim(Input::get('referente'))!='' ){
            $tablaRelacion['referente']=Input::get('referente');
        }

        if( Input::has('responsable') AND trim(Input::get('responsable'))!='' ){
            $tablaRelacion['responsable']=Input::get('responsable');
        }
        $tablaRelacion['sumilla']=Input::get('sumilla');

        if( Input::has('doc_digital_id')){
             $tablaRelacion['doc_digital_id']=Input::get('doc_digital_id');
        }

        $tablaRelacion['persona_autoriza_id']=Input::get('id_autoriza');
        $tablaRelacion['persona_responsable_id']=Input::get('id_responsable');

        $tablaRelacion['usuario_created_at']=Auth::user()->id;
        $tablaRelacion->save();

        $rutaFlujo=RutaFlujo::find(Input::get('ruta_flujo_id'));

        $ruta= new Ruta;
        $ruta['tabla_relacion_id']=$tablaRelacion->id;
        $ruta['fecha_inicio']= $fecha_inicio;
        $ruta['ruta_flujo_id']=$rutaFlujo->id;
        $ruta['flujo_id']=$rutaFlujo->flujo_id;
        $ruta['persona_id']=$rutaFlujo->persona_id;
        if( Input::has('doc_digital_id')){
            $ruta['doc_digital_id']=Input::get('doc_digital_id');
        }
        $ruta['area_id']=$rutaFlujo->area_id;
        $ruta['usuario_created_at']= Auth::user()->id;
        $ruta->save();
        /**************CARTA *************************************************/
        /*
        $carta=array();
        if( Input::has('carta_id') ){
            $carta= Carta::find(Input::get('carta_id'));
        }
        else{
            $carta= new Carta;
            $carta['flujo_id']=$ruta->flujo_id;
            $carta['correlativo']=0;
            $carta['nro_carta']=Input::get('codigo');
            $carta['objetivo']="";
            $carta['entregable']="";
            $carta['alcance']="MDI";
            $carta['flujo_id']=$ruta->flujo_id;

            if( trim(Auth::user()->area_id)!='' ){
                $carta['area_id']=Auth::user()->area_id;
            }
            else{
                $carta['area_id']=$ruta->area_id;
            }
        }
            $carta['union']=1;
            $carta['usuario_updated_at']=Auth::user()->id;
            $carta['ruta_id']=$ruta->id;
            $carta->save();
        */
        /*********************************************************************/
        /************Agregado de referidos*************/
        $referido=new Referido;
        $referido['ruta_id']=$ruta->id;
        $referido['tabla_relacion_id']=$tablaRelacion->id;
        if($tablarelacion_id!=''){
            $referido['tabla_relacion_id']=$tablarelacion_id;
        }
        else{
            $detalle=explode("-",$tablaRelacion->id_union);
            if( $detalle[0]=='DS' or $detalle[0]=='EX' or $detalle[0]=='AN' ){
                $sql="  SELECT id
                        FROM tablas_relacion
                        WHERE id_union='$tablaRelacion->id_union'
                        ORDER BY id
                        LIMIT 0,1";
                $rsql=DB::select($sql);
                if( count($rsql)>0 ){
                    $referido['tabla_relacion_id']=$rsql[0]->id;
                }
            }
        }

        if( Input::has('doc_digital_id')){
               $referido['doc_digital_id']=Input::get('doc_digital_id');
        }
      
        $referido['tipo']=0;
        $referido['ruta_detalle_verbo_id']=0;
        $referido['referido']=$tablaRelacion->id_union;
        $referido['fecha_hora_referido']=$tablaRelacion->created_at;
        $referido['usuario_referido']=$tablaRelacion->usuario_created_at;
        $referido['usuario_created_at']=Auth::user()->id;
        $referido->save();
        /**********************************************/

        $qrutaDetalle=DB::table('rutas_flujo_detalle')
            ->where('ruta_flujo_id', '=', $rutaFlujo->id)
            ->where('estado', '=', '1')
            ->orderBy('norden','ASC')
            ->get();
            $validaactivar=0;
        
        $conteo=0;$array['fecha']=''; // inicializando valores para desglose

            foreach($qrutaDetalle as $rd){
                $cero='';
                if($rd->norden<10){
                    $cero='0';
                }
                $rutaDetalle = new RutaDetalle;
                $rutaDetalle['ruta_id']=$ruta->id;
                $rutaDetalle['area_id']=$rd->area_id;
                $rutaDetalle['tiempo_id']=$rd->tiempo_id;
                $rutaDetalle['dtiempo']=$rd->dtiempo;
                $rutaDetalle['norden']=$cero.$rd->norden;
                $rutaDetalle['ruta_flujo_id']=$rd->ruta_flujo_id2;
                $rutaDetalle['estado_ruta']=$rd->estado_ruta;
                $rutaDetalle['detalle']=$rd->detalle;
                $rutaDetalle['archivado']=$rd->archivado;
                if($rd->norden==1 or ($rd->norden>1 and $validaactivar==0 and $rd->estado_ruta==2) ){
                    $rutaDetalle['fecha_inicio']=$fecha_inicio;
                    $sql="SELECT CalcularFechaFinal( '".$fecha_inicio."', (".$rd->dtiempo."*1440), ".$rd->area_id." ) fproy";
                    $fproy= DB::select($sql);
                    $rutaDetalle['fecha_proyectada']=$fproy[0]->fproy;
                }
                else{
                    $validaactivar=1;
                }
                $rutaDetalle['usuario_created_at']= Auth::user()->id;
                $rutaDetalle->save();
                /**************CARTA DESGLOSE*********************************/
                /*
                $cartaDesglose=array();
                if( Input::has('carta_id') ){
                    $carta_id=Input::get('carta_id');
                    $sql="  SELECT id
                            FROM carta_desglose
                            WHERE carta_id='$carta_id'
                            AND estado=1
                            ORDER BY id
                            LIMIT $conteo,1";
                    $cd=DB::select($sql);
                    if(count($cd)==0){
                        DB::rollback();
                        return  array(
                                'rst'=>2,
                                'msj'=>'Numero de actidades del proceso no concuerda con numero de actividades de la carta'
                            );
                    }
                    $conteo++;
                    $cartaDesglose=CartaDesglose::find($cd[0]->id);
                }
                else{
                    $sql="  SELECT id
                            FROM personas
                            WHERE estado=1
                            AND rol_id IN (8,9,70)
                            AND area_id='".$rutaDetalle->area_id."'";
                    $person=DB::select($sql);
                        //MEDIR LOS TIEMPOS//
                        $cantmin=0;
                        if( $rutaDetalle->tiempo_id==1 ){
                            $cantmin=60;
                        }
                        elseif( $rutaDetalle->tiempo_id==2 ){
                            $cantmin=1440;
                        }

                        if( $array['fecha']=='' ){
                            $array['fecha']= $fecha_inicio;
                        }
                        $array['tiempo']=($rutaDetalle->dtiempo*$cantmin);
                        $array['area']=$rutaDetalle->area_id;
                        $ff=Carta::CalcularFechaFin($array);
                        $fi=$array['fecha'];
                        $array['fecha']=$ff;

                    $cartaDesglose= new CartaDesglose;
                    $cartaDesglose['carta_id']=$carta->id;
                    $cartaDesglose['tipo_actividad_id']=19;
                    $cartaDesglose['actividad']="Actividad";
                        if( isset($person[0]->id) ){
                        $cartaDesglose['persona_id']=$person[0]->id;
                        }
                    $cartaDesglose['area_id']=$rutaDetalle->area_id;
                    $cartaDesglose['recursos']="";
                    $cartaDesglose['fecha_inicio']=$fi;
                    $cartaDesglose['fecha_fin']=$ff;
                    $cartaDesglose['hora_inicio']="08:00";
                    $cartaDesglose['hora_fin']="17:30";
                    $cartaDesglose['fecha_alerta']=$ff;
                }
                    $cartaDesglose['ruta_detalle_id']=$rutaDetalle->id;
                    $cartaDesglose->save();
                */
                /*************************************************************/
                if( $rd->norden==1 AND Input::has('carta_id') ){
                    $rutaDetalleVerbo = new RutaDetalleVerbo;
                    $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                    $rutaDetalleVerbo['nombre']= '-';
                    $rutaDetalleVerbo['condicion']= '0';
                    $rol_id=1;
                        if( Input::has('rol_id') AND Input::get('rol_id')!='' ){
                            $rol_id=Input::get('rol_id');
                        }
                        elseif( isset(Auth::user()->rol_id) ){
                            $rol_id=Auth::user()->rol_id;
                        }
                    $rutaDetalleVerbo['rol_id']= $rol_id;
                    $rutaDetalleVerbo['verbo_id']= '1';
                    $rutaDetalleVerbo['documento_id']= '57';//Carta de inicio
                    $rutaDetalleVerbo['orden']= '0';
                    $rutaDetalleVerbo['finalizo']='1';
                    $rutaDetalleVerbo['documento']=Input::get('codigo');
                    $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                    $rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
                    $rutaDetalleVerbo->save();
                }

                $qrutaDetalleVerbo=DB::table('rutas_flujo_detalle_verbo')
                                ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                ->where('estado', '=', '1')
                                ->orderBy('orden', 'ASC')
                                ->get();
                    if(count($qrutaDetalleVerbo)>0){
                        foreach ($qrutaDetalleVerbo as $rdv) {
                            $rutaDetalleVerbo = new RutaDetalleVerbo;
                            $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                            $rutaDetalleVerbo['nombre']= $rdv->nombre;
                            $rutaDetalleVerbo['condicion']= $rdv->condicion;
                            $rutaDetalleVerbo['rol_id']= $rdv->rol_id;
                            $rutaDetalleVerbo['verbo_id']= $rdv->verbo_id;
                            $rutaDetalleVerbo['documento_id']= $rdv->documento_id;
                            $rutaDetalleVerbo['orden']= $rdv->orden;
                            $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                            $rutaDetalleVerbo->save();
                        }
                    }
            }
            
            $insertMicro="INSERT INTO rutas_detalle_micro (ruta_flujo_id,ruta_id,norden,usuario_created_at)
                          SELECT rfdm.ruta_flujo_id2,".$ruta->id.",IF(rfdm.norden<10,CONCAT('0',norden),norden) AS norden,".Auth::user()->id."
                          FROM rutas_flujo_detalle_micro rfdm
                          WHERE rfdm.ruta_flujo_id=".$rutaFlujo->id." AND rfdm.estado=1";
                          
            DB::insert($insertMicro);

        DB::commit();

        return  array(
                    'rst'=>1,
                    'msj'=>'Registro realizado con éxito'
                );
        }
    }



























##
## ---------------------------------------------------------------------
##

 public function crearRuta02(){ 
        DB::beginTransaction();

        $idAPI = 1272;
        $areaAPI = 19;
        $rutaFlujoIDGET=(Input::get('ruta_flujo_id') == 5569 ? 5806 : Input::get('ruta_flujo_id'));
        $x = (Input::get('id_incidencia')==''?time():Input::get('id_incidencia'));
        $IDSISC_NUMEROID=$x.':'.Input::get('codigo');
        $id_documento='';
        
        $selectfecha = "SELECT NOW() as fecha;";
        $fecha_actual = DB::select($selectfecha);
        $fecha_inicio=$fecha_actual[0]->fecha;        

        if( Input::has('documento_id') ){
            $id_documento=Input::get('documento_id');
        }
        
        $tablarelacion_id= Input::get('tablarelacion_id');

        $tablaRelacion=DB::table('tablas_relacion as tr')
                        ->join(
                            'rutas as r',
                            'tr.id','=','r.tabla_relacion_id'
                        )
                        ->where('tr.id_union', 'like', Input::get('id_incidencia').":%")
                        ->where('r.ruta_flujo_id', '=', $rutaFlujoIDGET)
                        ->where('tr.estado', '=', '1')
                        ->where('r.estado', '=', '1')
                        ->get();


        if(count($tablaRelacion)>0){
            DB::rollback();
            return  array(
                    'rst'=>2,
                    'msj'=>'El trámite ya fue registrado anteriormente'
                );
        }else{

        $tablaRelacion=new TablaRelacion;
        $tablaRelacion['software_id']=1;

        $tablaRelacion['id_union']=$IDSISC_NUMEROID;
        
        $tablaRelacion['fecha_tramite']= $fecha_inicio; //Input::get('fecha_tramite');
        $tablaRelacion['tipo_persona']=3;
        if( Input::has('paterno') AND Input::has('materno') AND Input::has('nombre') ){
            $tablaRelacion['paterno']=Input::get('paterno');
            $tablaRelacion['materno']=Input::get('materno');
            $tablaRelacion['nombre']=Input::get('nombre');
        }
        elseif( Input::has('razon_social') AND Input::has('ruc') ){
            $tablaRelacion['razon_social']=Input::get('razon_social');
            $tablaRelacion['ruc']=Input::get('ruc');
        }

            
        elseif( Input::has('carta_id') ){ // Este caso solo es para asignar carta inicio
            $tablaRelacion['area_id']=$areaAPI;
        }
        elseif( Input::has('razon_social') ){
            $tablaRelacion['razon_social']=Input::get('razon_social');
        }

        $tablaRelacion['referente']='';
        
        if( Input::has('responsable') AND trim(Input::get('responsable'))!='' ){
            $tablaRelacion['responsable']=Input::get('responsable');
        }
        $tablaRelacion['sumilla']=Input::get('sumilla');

        if( Input::has('doc_digital_id')){
             $tablaRelacion['doc_digital_id']=Input::get('doc_digital_id');
        }

        $tablaRelacion['persona_autoriza_id']=Input::get('id_autoriza');
        $tablaRelacion['persona_responsable_id']=Input::get('id_responsable');

        $tablaRelacion['usuario_created_at']=$idAPI;
        $tablaRelacion->save();

        $rutaFlujo=RutaFlujo::find($rutaFlujoIDGET);

        $ruta= new Ruta;
        $ruta['tabla_relacion_id']=$tablaRelacion->id;
        $ruta['fecha_inicio']= $fecha_inicio;
        $ruta['ruta_flujo_id']=$rutaFlujo->id;
        $ruta['flujo_id']=$rutaFlujo->flujo_id;
        $ruta['persona_id']=$rutaFlujo->persona_id;
        if( Input::has('doc_digital_id')){
            $ruta['doc_digital_id']=Input::get('doc_digital_id');
        }
        $ruta['area_id']=$rutaFlujo->area_id;
        $ruta['usuario_created_at']= $idAPI;
        $ruta->save();

        $referido=new Referido;
        $referido['ruta_id']=$ruta->id;
        $referido['tabla_relacion_id']=$tablaRelacion->id;
        
        if($tablarelacion_id!=''){
            $referido['tabla_relacion_id']=$tablarelacion_id;
        }else{
            $detalle=explode("-",$tablaRelacion->id_union);
            if( $detalle[0]=='DS' or $detalle[0]=='EX' or $detalle[0]=='AN' ){
                $sql="  SELECT id
                        FROM tablas_relacion
                        WHERE id_union='$tablaRelacion->id_union'
                        ORDER BY id
                        LIMIT 0,1";
                $rsql=DB::select($sql);
                if( count($rsql)>0 ){
                    $referido['tabla_relacion_id']=$rsql[0]->id;
                }
            }
        }

        if( Input::has('doc_digital_id')){
               $referido['doc_digital_id']=Input::get('doc_digital_id');
        }
      
        $referido['tipo']=0;
        $referido['ruta_detalle_verbo_id']=0;
        $referido['referido']=$tablaRelacion->id_union;
        $referido['fecha_hora_referido']=$tablaRelacion->created_at;
        $referido['usuario_referido']=$tablaRelacion->usuario_created_at;
        $referido['usuario_created_at']=$idAPI;
        $referido->save();
        /**********************************************/

        $qrutaDetalle=DB::table('rutas_flujo_detalle')
            ->where('ruta_flujo_id', '=', $rutaFlujo->id)
            ->where('estado', '=', '1')
            ->orderBy('norden','ASC')
            ->get();
            $validaactivar=0;
        
        $conteo=0;$array['fecha']=''; // inicializando valores para desglose

            $actualdate = date("Y-m-d H:i:s");
            $iterator = 0;
            foreach($qrutaDetalle as $rd){
                $cero='';
                if($rd->norden<10){ 
                    $cero='0';
                }

                $rutaDetalle = new RutaDetalle;
                $rutaDetalle['ruta_id']=$ruta->id;
                $rutaDetalle['area_id']=$rd->area_id;
                $rutaDetalle['tiempo_id']=$rd->tiempo_id;
                $rutaDetalle['dtiempo']=$rd->dtiempo;
                $rutaDetalle['norden']=$cero.$rd->norden;
                $rutaDetalle['ruta_flujo_id']=$rd->ruta_flujo_id2;
                $rutaDetalle['estado_ruta']=$rd->estado_ruta;
                $rutaDetalle['detalle']=$rd->detalle;
                $rutaDetalle['archivado']=$rd->archivado;


                if($rd->norden==1 or ($rd->norden>1 and $validaactivar==0 and $rd->estado_ruta==2) ){

                    $rutaDetalle['fecha_inicio']=$fecha_inicio;

                    $sql="SELECT CalcularFechaFinal( '".$fecha_inicio."', (".$rd->dtiempo."*1440), ".$rd->area_id." ) fproy";
                    $fproy= DB::select($sql);
                    $rutaDetalle['fecha_proyectada']=$fproy[0]->fproy;
                }

                else{
                    $validaactivar=1;
                }
                if(Input::has('automatico') || true){                
                    if($iterator == 1){
                        $rutaDetalle['fecha_inicio']=$actualdate;
                    }

                    if($iterator == 0){
                        $rutaDetalle['dtiempo_final']= $actualdate;
                    }
                    $iterator++;
                } 
                


                $rutaDetalle['usuario_created_at']= $idAPI;
                $rutaDetalle->save();
                
                $qrutaDetalleVerbo=DB::table('rutas_flujo_detalle_verbo')
                                ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                ->where('estado', '=', '1')
                                ->orderBy('orden', 'ASC')
                                ->get();
                    if(count($qrutaDetalleVerbo)>0){

                        foreach ($qrutaDetalleVerbo as $rdv) {
                            $rutaDetalleVerbo = new RutaDetalleVerbo;
                            $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                            $rutaDetalleVerbo['nombre']= $rdv->nombre;
                            $rutaDetalleVerbo['condicion']= $rdv->condicion;
                            $rutaDetalleVerbo['rol_id']= $rdv->rol_id;
                            $rutaDetalleVerbo['verbo_id']= $rdv->verbo_id;
                            $rutaDetalleVerbo['documento_id']= $rdv->documento_id;
                            $rutaDetalleVerbo['orden']= $rdv->orden;

                            if($rd->norden == 1){
                                $rutaDetalleVerbo['finalizo']='1';
                                $rutaDetalleVerbo['documento']=$IDSISC_NUMEROID;
                            }

                            $rutaDetalleVerbo['usuario_created_at']= $idAPI;
                            $rutaDetalleVerbo->save();
                        }
                    }
            }
            
            $insertMicro="INSERT INTO rutas_detalle_micro (ruta_flujo_id,ruta_id,norden,usuario_created_at)
                          SELECT rfdm.ruta_flujo_id2,".$ruta->id.",IF(rfdm.norden<10,CONCAT('0',norden),norden) AS norden,".$idAPI."
                          FROM rutas_flujo_detalle_micro rfdm
                          WHERE rfdm.ruta_flujo_id=".$rutaFlujo->id." AND rfdm.estado=1";
                          
            DB::insert($insertMicro);

        DB::commit();

        return  array(
                    'rst'=>1,
                    'msj'=>'Registro realizado con éxito'
                );
        }
    }



##
## ----------------------------------------------------------------------------
##


















    public function crearRutaGestion(){
        DB::beginTransaction();
        $codigounico="";
        $codigounico=Input::get('codigo2');
        $id_documento='';
        if( Input::has('documento_id2') ){
            $id_documento=Input::get('documento_id2');
        }
        $ruta_id= Input::get('ruta_id2');
        $rutadetalle_id= Input::get('rutadetalle_id2');
        $tablarelacion_id= Input::get('tablarelacion_id2');

        $tablaRelacion=DB::table('tablas_relacion as tr')
                        ->join(
                            'rutas as r',
                            'tr.id','=','r.tabla_relacion_id'
                        )
                        ->where('tr.id_union', '=', $codigounico)
                        ->where('r.ruta_flujo_id', '=', 3620)
                        ->where('tr.estado', '=', '1')
                        ->where('r.estado', '=', '1')
                        ->get();

        if(count($tablaRelacion)>0){
            DB::rollback();
            return  array(
                    'rst'=>2,
                    'msj'=>'El trámite ya fue registrado anteriormente'
                );
        }
        else{

        $fecha_inicio2= Input::get('fecha_inicio2');
        if( $fecha_inicio2=='0000-00-00 00:00:00' ){
            $fecha_inicio2=date('Y-m-d H:i:s');
        }
        $tablaRelacion=new TablaRelacion;
        $tablaRelacion['software_id']=1;

        $tablaRelacion['id_union']=Input::get('codigo2');
        
        $tablaRelacion['fecha_tramite']= $fecha_inicio2; //Input::get('fecha_tramite');
        $tablaRelacion['tipo_persona']=Input::get('tipo_persona2');

        if( Input::has('paterno2') AND Input::has('materno2') AND Input::has('nombre2') ){
            $tablaRelacion['paterno']=Input::get('paterno2');
            $tablaRelacion['materno']=Input::get('materno2');
            $tablaRelacion['nombre']=Input::get('nombre2');
        }
        elseif( Input::has('razon_social2') AND Input::has('ruc2') ){
            $tablaRelacion['razon_social']=Input::get('razon_social2');
            $tablaRelacion['ruc']=Input::get('ruc2');
        }
        elseif( Input::has('area_p_id2') ){
            $tablaRelacion['area_id']=Input::get('area_p_id2');
        }
        elseif( Input::has('carta_id') ){ // Este caso solo es para asignar carta inicio
            $tablaRelacion['area_id']=Auth::user()->area_id;
        }
        elseif( Input::has('razon_social2') ){
            $tablaRelacion['razon_social']=Input::get('razon_social2');
        }

        if( Input::has('doc_digital_id2')){
            $tablaRelacion['doc_digital_id']=Input::get('doc_digital_id2');
        }


        if( Input::has('referente2') AND trim(Input::get('referente2'))!='' ){
            $tablaRelacion['referente']=Input::get('referente2');
        }

        if( Input::has('responsable') AND trim(Input::get('responsable'))!='' ){
            $tablaRelacion['responsable']=Input::get('responsable');
        }
        $tablaRelacion['sumilla']=Input::get('sumilla2');

        $tablaRelacion['persona_autoriza_id']=Input::get('id_autoriza');
        $tablaRelacion['persona_responsable_id']=Input::get('id_responsable');

        $tablaRelacion['usuario_created_at']=Auth::user()->id;
        $tablaRelacion->save();

        $rutaFlujo=RutaFlujo::find(3620);//3620

        $ruta= new Ruta;
        $ruta['tabla_relacion_id']=$tablaRelacion->id;
        $ruta['fecha_inicio']= $fecha_inicio2;
        $ruta['ruta_flujo_id']=$rutaFlujo->id;
        $ruta['flujo_id']=$rutaFlujo->flujo_id;
        $ruta['persona_id']=$rutaFlujo->persona_id;
        $ruta['area_id']=$rutaFlujo->area_id;

        if( Input::has('doc_digital_id2')){
            $ruta['doc_digital_id']=Input::get('doc_digital_id2');
        }
        $ruta['usuario_created_at']= Auth::user()->id;
        $ruta->save();
        /**************CARTA *************************************************/
        /*$carta=array();
        if( Input::has('carta_id') ){
            $carta= Carta::find(Input::get('carta_id'));
        }
        else{
            $carta= new Carta;
            $carta['flujo_id']=$ruta->flujo_id;
            $carta['correlativo']=0;
            $carta['nro_carta']=Input::get('codigo2');
            $carta['objetivo']="";
            $carta['entregable']="";
            $carta['alcance']="MDI";
            $carta['flujo_id']=$ruta->flujo_id;

            if( trim(Auth::user()->area_id)!='' ){
                $carta['area_id']=Auth::user()->area_id;
            }
            else{
                $carta['area_id']=$ruta->area_id;
            }
        }
            $carta['union']=1;
            $carta['usuario_updated_at']=Auth::user()->id;
            $carta['ruta_id']=$ruta->id;
            $carta->save();
        */
        /*********************************************************************/
        /************Agregado de referidos*************/
        $referido=new Referido;
        $referido['ruta_id']=$ruta->id;
        $referido['tabla_relacion_id']=$tablaRelacion->id;
        if($tablarelacion_id!=''){
            $referido['tabla_relacion_id']=$tablarelacion_id;
        }
        if( Input::has('doc_digital_id2')){
            $referido['doc_digital_id']=Input::get('doc_digital_id2');
        }
        $referido['tipo']=0;
        $referido['ruta_detalle_verbo_id']=0;
        $referido['referido']=$tablaRelacion->id_union;
        $referido['fecha_hora_referido']=$tablaRelacion->created_at;
        $referido['usuario_referido']=$tablaRelacion->usuario_created_at;
        $referido['usuario_created_at']=Auth::user()->id;
        $referido->save();
        /**********************************************/

        $qrutaDetalle=DB::table('rutas_flujo_detalle')
            ->where('ruta_flujo_id', '=', 3620)
            ->where('estado', '=', '1')
            ->orderBy('norden','ASC')
            ->get();
            $validaactivar=0;
        
        $conteo=0;$array['fecha']=''; // inicializando valores para desglose

        $tiempo = [];
        $areas = [];
        if(Input::has('areasSelect')){
/*            $tiempo = json_decode(Input::get('diasTiempo'));*/
            $areas = json_decode(Input::get('areasSelect'));
        }elseif(Input::has('areasTodas')){
         /*   $tiempo = Input::get('tiempo');*/
            $areas = json_decode(Input::get('areasTodas'));
        }

            foreach ($areas as $index => $val) {
                $cero='';
                if($index<9){
                    $cero='0';
                }
                $rutaDetalle = new RutaDetalle;
                $rutaDetalle['ruta_id']=$ruta->id;
                $rutaDetalle['area_id']=$val->area_id;
                $rutaDetalle['tiempo_id']=2;         
/*
                if (is_array($tiempo)){
                    $rutaDetalle['dtiempo']=$tiempo[$index];                    
                }else{*/
                    $rutaDetalle['dtiempo']=$val->tiempo;
/*                }
*/
                $rutaDetalle['norden']=$cero.($index+1);
                if($index==0){
                    $rutaDetalle['fecha_inicio']=$fecha_inicio2;
                    $sql="SELECT CalcularFechaFinal( '".$fecha_inicio2."', (".$val->tiempo."*1440), ".$val->area_id." ) fproy";
                    $fproy= DB::select($sql);
                    $rutaDetalle['fecha_proyectada']=$fproy[0]->fproy;
                }
                else{
                    $validaactivar=1;
                }

                if ($index < 2) {
                     $rutaDetalle['estado_ruta']=1;
                }elseif($index >= 2){
                     $rutaDetalle['estado_ruta']=2;
                }
                $rutaDetalle['usuario_created_at']= Auth::user()->id;
                $rutaDetalle->save();
/*            }

            foreach($qrutaDetalle as $rd){
                $rutaDetalle = new RutaDetalle;
                $rutaDetalle['ruta_id']=$ruta->id;
                $rutaDetalle['area_id']=$rd->area_id;
                $rutaDetalle['tiempo_id']=$rd->tiempo_id;
                $rutaDetalle['dtiempo']=$rd->dtiempo;
                $rutaDetalle['norden']=$rd->norden;
                if($rd->norden==1){
                    $rutaDetalle['fecha_inicio']=$fecha_inicio2;
                }
                else{
                    $validaactivar=1;
                }

                if ($rd->norden < 3) {
                     $rutaDetalle['estado_ruta']=1;
                }elseif($rd->norden >= 3){
                     $rutaDetalle['estado_ruta']=2;
                }
                $rutaDetalle['usuario_created_at']= Auth::user()->id;
                $rutaDetalle->save();*/
                /**************CARTA DESGLOSE*********************************/
                /*
                $cartaDesglose=array();
                if( Input::has('carta_id') ){
                    $carta_id=Input::get('carta_id');
                    $sql="  SELECT id
                            FROM carta_desglose
                            WHERE carta_id='$carta_id'
                            AND estado=1
                            ORDER BY id
                            LIMIT $conteo,1";
                    $cd=DB::select($sql);
                    $conteo++;
                    $cartaDesglose=CartaDesglose::find($cd[0]->id);
                }
                else{
                    $sql="  SELECT id
                            FROM personas
                            WHERE estado=1
                            AND rol_id IN (8,9,70)
                            AND area_id='".$rutaDetalle->area_id."'";
                    $person=DB::select($sql);
                        ///MEDIR LOS TIEMPOS////
                        $cantmin=0;
                        if( $rutaDetalle->tiempo_id==1 ){
                            $cantmin=60;
                        }
                        elseif( $rutaDetalle->tiempo_id==2 ){
                            $cantmin=1440;
                        }

                        if( $array['fecha']=='' ){
                            $array['fecha']= Input::get('fecha_inicio');
                        }
                        $array['tiempo']=($rutaDetalle->dtiempo*$cantmin);
                        $array['area']=$rutaDetalle->area_id;
                        $ff=$rutaDetalle['fecha_proyectada'];
                        $fi=$array['fecha'];
                        $array['fecha']=$ff;

                    $cartaDesglose= new CartaDesglose;
                    $cartaDesglose['carta_id']=$carta->id;
                    $cartaDesglose['tipo_actividad_id']=19;
                    $cartaDesglose['actividad']="Actividad";
                        if( isset($person[0]->id) ){
                        $cartaDesglose['persona_id']=$person[0]->id;
                        }
                    $cartaDesglose['area_id']=$rutaDetalle->area_id;
                    $cartaDesglose['recursos']="";
                    $cartaDesglose['fecha_inicio']=$fi;
                    $cartaDesglose['fecha_fin']=$ff;
                    $cartaDesglose['hora_inicio']="08:00";
                    $cartaDesglose['hora_fin']="17:30";
                    $cartaDesglose['fecha_alerta']=$ff;
                }
                    $cartaDesglose['ruta_detalle_id']=$rutaDetalle->id;
                    $cartaDesglose->save();
                */
                /*************************************************************/
                if( $index==0 AND Input::has('carta_id') ){
                    $rutaDetalleVerbo = new RutaDetalleVerbo;
                    $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                    $rutaDetalleVerbo['nombre']= '-';
                    $rutaDetalleVerbo['condicion']= '0';
                    $rol_id=1;
                        if( Input::has('rol_id') AND Input::get('rol_id')!='' ){
                            $rol_id=Input::get('rol_id');
                        }
                        elseif( isset(Auth::user()->rol_id) ){
                            $rol_id=Auth::user()->rol_id;
                        }
                    $rutaDetalleVerbo['rol_id']= $rol_id;
                    $rutaDetalleVerbo['verbo_id']= '1';
                    $rutaDetalleVerbo['documento_id']= '57';//Carta de inicio
                    $rutaDetalleVerbo['orden']= '0';
                    $rutaDetalleVerbo['finalizo']='1';
                    $rutaDetalleVerbo['documento']=Input::get('codigo');
                    $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                    $rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
                    $rutaDetalleVerbo->save();
                }



                /*$qrutaDetalleVerbo=DB::table('rutas_flujo_detalle_verbo')
                                ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                ->where('estado', '=', '1')
                                ->orderBy('orden', 'ASC')
                                ->get();
                    if(count($qrutaDetalleVerbo)>0){
                        foreach ($qrutaDetalleVerbo as $rdv) {
                            $rutaDetalleVerbo = new RutaDetalleVerbo;
                            $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                            $rutaDetalleVerbo['nombre']= $rdv->nombre;
                            $rutaDetalleVerbo['condicion']= $rdv->condicion;
                            $rutaDetalleVerbo['rol_id']= $rdv->rol_id;

                            $rutaDetalleVerbo['verbo_id']= $rdv->verbo_id;
                            $rutaDetalleVerbo['documento_id']= $rdv->documento_id;
                            $rutaDetalleVerbo['orden']= $rdv->orden;
                            $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                            $rutaDetalleVerbo->save();
                        }
                    }*/
                    $array_verbos = [];
                    if($index==0){
                        $array_verbos = [1,5,4];
                        /*foreach ($array_verbos as $key => $value) {
                            $verbo = Verbo::find($value);

                            $rutaDetalleVerbo = new RutaDetalleVerbo;
                            $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                            $rutaDetalleVerbo['nombre']= $verbo->nombre;
                            $rutaDetalleVerbo['condicion']= 0;

                            if($value == 5){
                                $Area = Area::find($val);
                                if($Area->area_gestion == 1){
                                    $rutaDetalleVerbo['rol_id']= 8;     
                                }elseif($Area->area_gestion == 2){
                                    $rutaDetalleVerbo['rol_id']= 9;                                    
                                }
                            }else{
                                $rutaDetalleVerbo['rol_id']= 1;                                
                            }

                            $rutaDetalleVerbo['verbo_id']= $value;
                             $rutaDetalleVerbo['documento_id']= '';
                            $rutaDetalleVerbo['orden']= $key + 1;
                            $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                            $rutaDetalleVerbo->save();                           
                        }*/
                    }elseif( Input::get('select_tipoenvio')==1 && $val->copia==0){ //con retorno
                        $array_verbos = [2,1,5,4];
         /*               foreach ($array_verbos as $key => $value) {
                            $verbo = Verbo::find($value);

                            $rutaDetalleVerbo = new RutaDetalleVerbo;
                            $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                            $rutaDetalleVerbo['nombre']= $verbo->nombre;
                            $rutaDetalleVerbo['condicion']= 0;

                            if($value == 5){
                                $Area = Area::find($val);
                                if($Area->area_gestion == 1){
                                    $rutaDetalleVerbo['rol_id']= 8;     
                                }elseif($Area->area_gestion == 2){
                                    $rutaDetalleVerbo['rol_id']= 9;                                    
                                }
                            }else{
                                $rutaDetalleVerbo['rol_id']= 1;                                
                            }

                            $rutaDetalleVerbo['verbo_id']= $value;
                             $rutaDetalleVerbo['documento_id']= '';
                            $rutaDetalleVerbo['orden']= $key + 1;
                            $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                            $rutaDetalleVerbo->save();                           
                        }*/
                    }else if(Input::get('select_tipoenvio')==2  or $val->copia==1){ //sin retorno
                        $array_verbos = [2,14];
         /*               foreach ($array_verbos as $key => $value) {
                            $verbo = Verbo::find($value);

                            $rutaDetalleVerbo = new RutaDetalleVerbo;
                            $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                            $rutaDetalleVerbo['nombre']= $verbo->nombre;
                            $rutaDetalleVerbo['condicion']= 0;
                            $rutaDetalleVerbo['rol_id']= 1;

                            $rutaDetalleVerbo['verbo_id']= $value;
                             $rutaDetalleVerbo['documento_id']= '';
                            $rutaDetalleVerbo['orden']= $key + 1;
                            $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                            $rutaDetalleVerbo->save();                           
                        }*/
                    }

                    foreach ($array_verbos as $key => $value) {
                        $verbo = Verbo::find($value);

                        $rutaDetalleVerbo = new RutaDetalleVerbo;
                        $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                        $rutaDetalleVerbo['nombre']= $verbo->nombre;
                        $rutaDetalleVerbo['condicion']= 0;

                        if($value == 5){
                            $Area = Area::find($val->area_id);
                            if($Area->area_gestion == 1){
                                $rutaDetalleVerbo['rol_id']= 8;     
                            }elseif($Area->area_gestion == 2){
                                $rutaDetalleVerbo['rol_id']= 9;                                    
                            }
                        }else{
                            $rutaDetalleVerbo['rol_id']= 1;                                
                        }

                        $rutaDetalleVerbo['verbo_id']= $value;
                         $rutaDetalleVerbo['documento_id']= '';
                        $rutaDetalleVerbo['orden']= $key + 1;
                        $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                        $rutaDetalleVerbo->save();                           
                    }
            }
            DB::commit();
            return  array(
                    'rst'=>1,
                    'msj'=>'Registro realizado con éxito'
            );
        }
    }



     public function crearOrdenTrabajo(){

        DB::beginTransaction();
        if(Input::has('info')){
            $info = Input::get('info');
            if(count($info) > 0){
                $persona_id=Auth::user()->id;
                /*si crea para otra persona*/
                if($info[0]['persona']){
                    $persona_id = $info[0]['persona'];
                }
                /*fin si crea para otra persona*/
               
                $correlativo = $this->Correlativo($persona_id);
                $codigounico="OT-".$correlativo->correlativo."-".$persona_id."-".date("Y");
                $tablaRelacion=DB::table('tablas_relacion as tr')
                                ->join(
                                    'rutas as r',
                                    'tr.id','=','r.tabla_relacion_id'
                                )
                                ->where('tr.id_union', '=', $codigounico)
                                ->where('r.ruta_flujo_id', '=', 3720)
                                ->where('tr.estado', '=', '1')
                                ->where('r.estado', '=', '1')
                                ->get();

                if(count($tablaRelacion)>0){
                    DB::rollback();
                    return  array(
                            'rst'=>2,
                            'msj'=>'El trámite ya fue registrado anteriormente'
                        );
                }
                else{

                $tablaRelacion=new TablaRelacion;
                $tablaRelacion['software_id']=1;

                $tablaRelacion['id_union']=$codigounico;
                
                $tablaRelacion['fecha_tramite']= date("Y-m-d"); //Input::get('fecha_tramite');
                $tablaRelacion['tipo_persona']= 1;

                if( Input::has('paterno3') AND Input::has('materno3') AND Input::has('nombre3') ){
                    $tablaRelacion['paterno']=Input::get('paterno3');
                    $tablaRelacion['materno']=Input::get('materno3');
                    $tablaRelacion['nombre']=Input::get('nombre3');
                }
                elseif( Input::has('razon_social3') AND Input::has('ruc3') ){
                    $tablaRelacion['razon_social']=Input::get('razon_social3');
                    $tablaRelacion['ruc']=Input::get('ruc2');
                }
                elseif( Input::has('area_p_id3') ){
                    $tablaRelacion['area_id']=Input::get('area_p_id3');
                }
                elseif( Input::has('carta_id') ){ // Este caso solo es para asignar carta inicio
                    $tablaRelacion['area_id']=Auth::user()->area_id;
                }
                elseif( Input::has('razon_social3') ){
                    $tablaRelacion['razon_social']=Input::get('razon_social3');
                }


                if( Input::has('referente3') AND trim(Input::get('referente3'))!='' ){
                    $tablaRelacion['referente']=Input::get('referente2');
                }

                if( Input::has('responsable') AND trim(Input::get('responsable'))!='' ){
                    $tablaRelacion['responsable']=Input::get('responsable');
                }
                $tablaRelacion['sumilla']='';

                $tablaRelacion['persona_autoriza_id']=Auth::user()->id;
                $tablaRelacion['persona_responsable_id']=Auth::user()->id;

                $tablaRelacion['area_id']=Auth::user()->area_id;
                $tablaRelacion['usuario_created_at']=Auth::user()->id;
                $tablaRelacion->save();

                $rutaFlujo=RutaFlujo::find(3720); //3283
                $Persona = Persona::find($persona_id);

                $ruta= new Ruta;
                $ruta['tabla_relacion_id']=$tablaRelacion->id;
                $ruta['fecha_inicio']= date("Y-m-d");
                $ruta['ruta_flujo_id']=$rutaFlujo->id;
                $ruta['flujo_id']=$rutaFlujo->flujo_id;
                $ruta['persona_id']=$Persona->id;
                $ruta['area_id']=$Persona->area_id;
                $ruta['usuario_created_at']= Auth::user()->id;
                $ruta->save();
                /**************CARTA *************************************************/
                /*
                $carta=array();
                if( Input::has('carta_id') ){
                    $carta= Carta::find(Input::get('carta_id'));
                }
                else{
                    $carta= new Carta;
                    $carta['flujo_id']=$ruta->flujo_id;
                    $carta['correlativo']=0;
                    $carta['nro_carta']=$codigounico;
                    $carta['objetivo']="";
                    $carta['entregable']="";
                    $carta['alcance']="MDI";
                    $carta['flujo_id']=$ruta->flujo_id;

                    if( trim(Auth::user()->area_id)!='' ){
                        $carta['area_id']=Auth::user()->area_id;
                    }
                    else{
                        $carta['area_id']=$ruta->area_id;
                    }
                }
                    $carta['union']=1;
                    $carta['usuario_updated_at']=Auth::user()->id;
                    $carta['ruta_id']=$ruta->id;
                    $carta->save();
                */

                $qrutaDetalle=DB::table('rutas_flujo_detalle')
                    ->where('ruta_flujo_id', '=', 3720)
                    ->where('estado', '=', '1')
                    ->orderBy('norden','ASC')
                    ->get();

                     foreach ($info as $key => $value) {

                        $ttranscurrido = $value['ttranscurrido'];
                        $minTrascurrido = explode(':', $ttranscurrido)[0] * 60 + explode(':', $ttranscurrido)[1];

                        $rutaDetalle = new RutaDetalle;
                        $rutaDetalle['ruta_id']=$ruta->id;
                        $rutaDetalle['area_id']=$Persona->area_id;
                        $rutaDetalle['tiempo_id']=2;         
                        $rutaDetalle['dtiempo'] = 1;
                        $rutaDetalle['fecha_inicio']= date("Y-m-d", strtotime($value['finicio']))." ".explode(' ',$value['hinicio'])[0];
                        $rutaDetalle['dtiempo_final']= date("Y-m-d", strtotime($value['ffin']))." ".explode(' ',$value['hfin'])[0];
                        $rutaDetalle['estado_ruta']=1;
                        $rutaDetalle['ot_tiempo_transcurrido']=$minTrascurrido;
                        $rutaDetalle['actividad']=$value['actividad'];
                        $rutaDetalle['norden']=$key + 1;
                        $rutaDetalle['usuario_created_at']= Auth::user()->id;
                        $rutaDetalle->save();

                        /**************CARTA DESGLOSE*********************************/
/*                        $cartaDesglose=array();
                        $array = [];
                        if( Input::has('carta_id') ){
                            $carta_id=Input::get('carta_id');
                            $sql="  SELECT id
                                    FROM carta_desglose
                                    WHERE carta_id='$carta_id'
                                    AND estado=1
                                    ORDER BY id
                                    LIMIT $conteo,1";
                            $cd=DB::select($sql);
                            $conteo++;
                            $cartaDesglose=CartaDesglose::find($cd[0]->id);
                        }
                        else{
                            $sql="  SELECT id
                                    FROM personas
                                    WHERE estado=1
                                    AND rol_id IN (8,9,70)
                                    AND area_id='".$rutaDetalle->area_id."'";
                            $person=DB::select($sql);*/
                                /***********MEDIR LOS TIEMPOS**************************/
/*                                $cantmin=0;
                                if( $rutaDetalle->tiempo_id==1 ){
                                    $cantmin=60;
                                }
                                elseif( $rutaDetalle->tiempo_id==2 ){
                                    $cantmin=1440;
                                }

                                if( $array['fecha']=='' ){
                                    $array['fecha']= Input::get('fecha_inicio');
                                }
                                $array['tiempo']=($rutaDetalle->dtiempo*$cantmin);
                                $array['area']=$rutaDetalle->area_id;
                                $ff=Carta::CalcularFechaFin($array);
                                $fi=$array['fecha'];
                                $array['fecha']=$ff;

                            $cartaDesglose= new CartaDesglose;
                            $cartaDesglose['carta_id']=$carta->id;
                            $cartaDesglose['tipo_actividad_id']=19;
                            $cartaDesglose['actividad']="Actividad";
                                if( isset($person[0]->id) ){
                                $cartaDesglose['persona_id']=$person[0]->id;
                                }
                            $cartaDesglose['area_id']=$rutaDetalle->area_id;
                            $cartaDesglose['recursos']="";
                            $cartaDesglose['fecha_inicio']=$fi;
                            $cartaDesglose['fecha_fin']=$ff;
                            $cartaDesglose['hora_inicio']="08:00";
                            $cartaDesglose['hora_fin']="17:30";
                            $cartaDesglose['fecha_alerta']=$ff;
                        }
                        $cartaDesglose['ruta_detalle_id']=$rutaDetalle->id;
                        $cartaDesglose->save();*/
                        /*************************************************************/

           /*                     $array_verbos = [1];
                                foreach ($array_verbos as $key => $value) {*/
                                    $verbo = Verbo::find(6);

                                    $rutaDetalleVerbo = new RutaDetalleVerbo;
                                    $rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
                                    $rutaDetalleVerbo['nombre']= $verbo->nombre;
                                    $rutaDetalleVerbo['condicion']= 0;
                                    $rutaDetalleVerbo['finalizo']= 1;                                  
                                    $rutaDetalleVerbo['rol_id']= $Persona->rol_id;;     
                                    $rutaDetalleVerbo['verbo_id']= 6;
                                    $rutaDetalleVerbo['documento_id']= 28;
                                    $rutaDetalleVerbo['orden']= $key + 1    ;
                                    $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                                    $rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
                                    $rutaDetalleVerbo->save();                           
                             /*   }*/




                            }

                }

                    DB::commit();
                    return  array(
                            'rst'=>1,
                            'msj'=>'Registro realizado con éxito'
                    );
                }
            }
    }

    
    public static function Correlativo($persona){
        $año= date("Y");
        $r2=array(array('correlativo'=>'000001','ano'=>$año));
        /*$sql = "SELECT LPAD(id+1,6,'0') as correlativo,'$año' ano FROM doc_digital ORDER BY id DESC LIMIT 1";*/
        $sql = "select LPAD(count(tr.id)+1,6,'0') as correlativo from tablas_relacion tr 
                inner join rutas r on r.tabla_relacion_id=tr.id and r.ruta_flujo_id=3720 and r.persona_id=".$persona."
                where tr.estado=1";
        $r= DB::select($sql);
        return (isset($r[0])) ? $r[0] : $r2[0];
    }
    
    public static function OrdenTrabajoDia()
    {     
        $sSql = '';
        $sSql.= "SELECT rd.id norden,rd.actividad,rd.fecha_inicio,rd.dtiempo_final,ABS(rd.ot_tiempo_transcurrido) ot_tiempo_transcurrido ,SEC_TO_TIME(ABS(rd.ot_tiempo_transcurrido) * 60) formato 
                FROM  tablas_relacion tr 
                INNER JOIN rutas r ON r.tabla_relacion_id=tr.id AND r.estado=1 AND r.persona_id=".Auth::user()->id."
                INNER JOIN rutas_detalle rd ON rd.ruta_id=r.id AND rd.estado=1
                WHERE tr.estado=1 AND tr.id_union like 'OT%'";

        if(Input::has('fecha') && Input::get('fecha')){
            $fecha = Input::get('fecha');
            $sSql.= " AND DATE(tr.created_at)='".$fecha."'";
        }
        
        $oData= DB::select($sSql);
        return $oData;
    }
    
            public static function ActividadDia()
    {   
        $persona=" AND at.persona_id=".Auth::user()->id;
        
        if(Input::has('tipopersona') && Input::get('tipopersona')){
            $persona= " AND at.usuario_created_at=".Auth::user()->id." AND at.tipo=2";
        }
        
        $sSql = '';
        $sSql.= "SELECT CONCAT_WS(' ',p.nombre,p.paterno,p.materno) as persona,at.id norden,at.actividad,at.fecha_inicio,at.dtiempo_final,ABS(at.ot_tiempo_transcurrido) ot_tiempo_transcurrido ,SEC_TO_TIME(ABS(at.ot_tiempo_transcurrido) * 60) formato,at.usuario_created_at,at.persona_id, at.cargo_dir, at.area_id 
                FROM  actividad_personal at 
                INNER JOIN personas p on at.persona_id=p.id
                WHERE at.estado=1";

        if(Input::has('fecha') && Input::get('fecha')){
            $fecha = Input::get('fecha');
            $sSql.= " AND DATE(at.created_at)='".$fecha."'";
        }
        $sSql.=$persona;
        
        $oData= DB::select($sSql);
        return $oData;
    }

    public static function ActividadById($norden)
    {   

        $sSql = '';
        $sSql.= "SELECT CONCAT_WS(' ',p.nombre,p.paterno,p.materno) as persona,at.id norden,at.actividad,at.fecha_inicio,at.dtiempo_final,ABS(at.ot_tiempo_transcurrido) ot_tiempo_transcurrido ,SEC_TO_TIME(ABS(at.ot_tiempo_transcurrido) * 60) formato,at.usuario_created_at,at.persona_id, at.cargo_dir, at.area_id
                FROM  actividad_personal at 
                INNER JOIN personas p on at.persona_id=p.id
                WHERE at.estado=1";

        $sSql.= " AND at.id='".$norden."'";
        
        
        $oData= DB::select($sSql);
        return $oData;
    }

}
?>
