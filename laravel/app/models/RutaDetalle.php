<?php
class RutaDetalle extends Eloquent
{
    public $table="rutas_detalle";

    public function getRutadetalle()
    {
        $area_id="";
        $flujo_id="";
        $ruta_detalle_id="";
        $adicional="";
        
        if( Input::has('tramite') AND Input::get('tramite')!='' ){
            $tramite=explode(" ",trim(Input::get('tramite')));
            $tramitef='';
            for($i=0; $i<count($tramite); $i++){
              $tramitef.=" AND tr.id_union LIKE '%".$tramite[$i]."%' ";
            }

            $adicional=
            ' WHERE FIND_IN_SET( rd.area_id, (
                    SELECT GROUP_CONCAT(a.id)
                    FROM area_cargo_persona acp
                    INNER JOIN areas a ON a.id=acp.area_id AND a.estado=1
                    INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1
                    WHERE acp.estado=1
                    AND cp.persona_id='.Auth::user()->id.'
                    ) )>0
            '.$tramitef.'
            AND rd.condicion=0
            AND rd.estado=1
            GROUP BY rd.id
            HAVING ( IFNULL(rd.dtiempo_final,"")="" )
            ORDER BY fi,rd.created_at';
        }

        if ( Input::has('ruta_detalle_id') ) {
            $ruta_detalle_id= Input::get('ruta_detalle_id');
            $adicional=' WHERE rd.id="'.$ruta_detalle_id.'"';
            $rdval=RutaDetalle::find($ruta_detalle_id);
            if( trim($rdval->fecha_proyectada)=='' ){
                $sql="SELECT CalcularFechaFinal( '".$rdval->fecha_inicio."', (".$rdval->dtiempo."*1440), ".$rdval->area_id." ) fproy";
                $fproy= DB::select($sql);
                $rdval['fecha_proyectada']=$fproy[0]->fproy;
                $rdval->save();
            }
        }
            

        $t0 = (Input::get('ruta_detalle_id')?',IFNULL(concat(\'_\',rdv.doc_digital_id),"")':'');

        $set=DB::statement('SET group_concat_max_len := @@max_allowed_packet');
        $query =
            'SELECT rd.archivado,rd.ruta_id,DATE_ADD(rd.fecha_inicio, INTERVAL 19 HOUR) as hora_fin_mayor,DATE_ADD(rd.fecha_inicio, INTERVAL 4 HOUR) as hora_fin_menor, NOW() as fecha_actual,rd.id, rd.dtiempo_final, r.flujo_id, 
             rd.ruta_flujo_id as rd_ruta_flujo_id, rd.ruta_flujo_id_dep,
            CONCAT(t.nombre," : ",rd.dtiempo) tiempo, rd.tiempo_id idtiempo,rd.motivo_edit motivo,
            rd.observacion,r.ruta_flujo_id, IFNULL(rd.persona_responsable_id,"") persona_responsable_id,
            IFNULL(CONCAT(p2.paterno," ",p2.materno,", ",p2.nombre),"") persona_responsable,
            a.id area_id, a.nombre AS area,f.nombre AS flujo,
            tr.id_union AS id_doc,tr.id id_tr, rd.estado_ruta,
            rd.norden, IFNULL(rd.fecha_inicio,"") AS fecha_inicio, tr.tramite_id, rd.ruta_detalle_id_ant,
                (SELECT GROUP_CONCAT(DISTINCT(CONCAT(pre.paterno," ",pre.materno,", ",pre.nombre," => ", are.nombre ," => ",rdre.motivo_retorno)) ORDER BY rdre.id ASC SEPARATOR " | ") 
                FROM rutas_detalle rdre
                INNER JOIN rutas_detalle rdre2 ON rdre2.ruta_detalle_id_ant = rdre.id AND rdre2.estado = 0
                INNER JOIN areas are ON are.id = rdre2.area_id
                INNER JOIN personas pre ON pre.id = rdre.usuario_retorno
                WHERE rdre.ruta_id = r.id 
                AND rdre.estado = 1 
                AND rdre.motivo_retorno IS NOT NULL 
                AND rdre.norden = rd.norden 
                AND rdre.condicion = 3) AS motivo_retorno,
            IFNULL(
                CASE
                    WHEN tm.id IS NOT NULL AND tstm.id = 0 THEN "Área"
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NULL THEN "Persona"
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NOT NULL THEN "Empresa"
                    ELSE "S/T"
                END
            ,"") AS tipo_solicitante,
            IFNULL(
                CASE
                    WHEN tm.id IS NOT NULL AND tstm.id = 0 THEN atm.nombre
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NULL THEN CONCAT(ptm.paterno," ",ptm.materno," ",ptm.nombre)
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NOT NULL THEN etm.razon_social
                    ELSE (SELECT nombre FROM areas WHERE id = tr.area_id)
                END
            , "") AS solicitante,
            IFNULL(
                CASE
                    WHEN tm.id IS NOT NULL AND tstm.id = 0 THEN "S/N"
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NULL THEN ptm.dni
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NOT NULL THEN etm.ruc
                    ELSE "S/N"
                END
            , "") AS id_solicitante,
            IFNULL(
                CASE
                    WHEN tm.id IS NOT NULL AND tstm.id = 0 THEN "S/D"
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NULL THEN ptm.direccion
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NOT NULL THEN etm.direccion_fiscal
                    ELSE "S/D"
                END
            , "") AS dir_solicitante,
            IFNULL(
                CASE
                    WHEN tm.id IS NOT NULL AND tstm.id = 0 THEN "S/E"
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NULL THEN ptm.email
                    ELSE "S/E"
                END
            , "") AS email_solicitante,
            IFNULL(
                CASE
                    WHEN tm.id IS NOT NULL AND tstm.id = 0 THEN "S/T"
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NULL THEN CONCAT( TRIM(ptm.celular), " / ", TRIM(ptm.telefono))
                    WHEN tm.id IS NOT NULL AND tm.empresa_id IS NOT NULL THEN etm.telefono
                    ELSE "S/T"
                END
            , "") AS tel_solicitante, tm.clasificador_tramite_id,
            tr.fecha_tramite,tr.sumilla, rd.archivo,
            IFNULL(GROUP_CONCAT(
                CONCAT(
                    rdv.id,
                     "=>",
                    rdv.nombre,
                     "=>",
                    IF(rdv.finalizo=0,"Pendiente","Finalizó"),
                    "=>",
                    IF(rdv.condicion=0,"NO",CONCAT("+",rdv.condicion) ),
                    "=>",
                    IFNULL(rdv.documento,"")'.$t0.',
                    "=>",
                    IFNULL(rdv.observacion,""),
                    "=>",
                    IFNULL(ro.nombre,""),
                    "=>",
                    IFNULL(ve.nombre,""),
                    "=>",
                    IFNULL(do.nombre,""),
                    "=>",
                    rdv.orden,
                    "=>",
                    IFNULL(concat(p.paterno," ",p.materno,", ",p.nombre),""),
                    "=>",
                    IFNULL(rdv.updated_at,""),
                    "=>",
                    IFNULL(rdv.usuario_updated_at,""),
                     "=>",
                    IFNULL(rdv.adicional,""),
                     "=>",
                    IFNULL(ro.id,""),
                     "=>",
                    IFNULL(p.id,"")
                )
                ORDER BY rdv.orden ASC
            SEPARATOR "|"),"") AS verbo,
            IFNULL(GROUP_CONCAT(
                CONCAT(
                    "<b>",
                    rdv.orden,
                    "</b>",
                     ".- ",
                    ro.nombre,
                     " tiene que ",
                    ve.nombre,
                     " ",
                    IFNULL(do.nombre,""),
                     " (",
                    rdv.nombre,
                     ")=>",
                    IF(rdv.finalizo=0,"<font color=#EC2121>Pendiente</font>",CONCAT("<font color=#22D72F>Finalizó(",p.paterno," ",p.materno,", ",p.nombre,")</font>") )
                )
            ORDER BY rdv.orden ASC
            SEPARATOR "|"),"") AS verbo2,IFNULL(rd.fecha_inicio,"9999") fi,
            rd.fecha_proyectada AS fecha_max, now() AS hoy
            ,IFNULL( max( IF(rdv.finalizo=1,rdv.condicion,NULL) ) ,"0") maximo, l.local, lo.local local_origen
            FROM rutas_detalle rd
            INNER JOIN rutas r ON (r.id=rd.ruta_id AND r.estado=1)
            INNER JOIN rutas_detalle_verbo rdv ON (rd.id=rdv.ruta_detalle_id AND rdv.estado=1)
            INNER JOIN areas a ON a.id=rd.area_id
            INNER JOIN flujos f ON f.id=r.flujo_id
            INNER JOIN tablas_relacion tr ON tr.id=r.tabla_relacion_id
            INNER JOIN tiempos t ON t.id=rd.tiempo_id 
            LEFT JOIN locales l ON l.id = r.local_id
            LEFT JOIN locales lo ON lo.id = r.local_origen_id
            LEFT JOIN tramites tm ON tm.id = tr.tramite_id
            LEFT JOIN personas ptm ON ptm.id = tm.persona_id 
            LEFT JOIN empresas etm ON etm.id = tm.empresa_id 
            LEFT JOIN areas atm ON atm.id = tm.area_id_sol
            LEFT JOIN tipo_solicitante tstm ON tstm.id = tm.tipo_solicitante_id
            LEFT JOIN personas p ON p.id=rdv.usuario_updated_at
            LEFT JOIN personas p2 ON p2.id=rd.persona_responsable_id
            LEFT JOIN roles ro ON ro.id=rdv.rol_id
            LEFT JOIN verbos ve ON ve.id=rdv.verbo_id
            LEFT JOIN documentos do ON do.id=rdv.documento_id'.
            $adicional;
        $rd = DB::select($query);
        //echo $query;
        //die();

        if ( Input::get('ruta_detalle_id') ) {
            return $rd[0];
        }
        else{
            return $rd;
        }
    }

    public function getTramite()
    {
        $array['tramite']='';
        if( Input::has('tramite') AND Input::get('tramite')!='' ){
        $tramite=explode(" ",trim(Input::get('tramite')));
            for($i=0; $i<count($tramite); $i++){
              $array['tramite'].=" AND tr.id_union LIKE '%".$tramite[$i]."%' ";
            }
        }
        $sql="  SELECT r.ruta_flujo_id,r.id,tr.id as tramite_id,tr.id_union,tr.fecha_tramite,
                IFNULL(ts.nombre,'') as solicitante,
                IF(tr.tipo_persona=1 or tr.tipo_persona=6,
                    CONCAT(tr.paterno,' ',tr.materno,', ',tr.nombre),
                    IF(tr.tipo_persona=2,
                        CONCAT(tr.razon_social,' | RUC:',tr.ruc),
                        IF(tr.tipo_persona=3,
                            a.nombre,
                            IF(tr.tipo_persona=4 or tr.tipo_persona=5,
                                tr.razon_social,''
                            )
                        )
                    )
                ) des_solicitante,tr.sumilla
                from rutas r
                inner join tablas_relacion tr ON r.tabla_relacion_id=tr.id and tr.estado=1
                LEFT join tipo_solicitante ts ON ts.id=tr.tipo_persona and ts.estado=1
                LEFT JOIN areas a ON a.id=tr.area_id
                WHERE r.estado=1
                ".$array['tramite'];
        $rd = DB::select($sql);
        
        return $rd;
    }

    public function getTramiteXArea()
    {
        $array['tramite']='';
        $array['area']='';
        if( Input::has('tramite') AND Input::get('tramite')!='' ){
        $tramite=explode(" ",trim(Input::get('tramite')));
            for($i=0; $i<count($tramite); $i++){
              $array['tramite'].=" AND tr.id_union LIKE '%".$tramite[$i]."%' ";
            }
        }

        $array['usuario']=Auth::user()->id;
        $sql="SELECT GROUP_CONCAT(DISTINCT(a.id) ORDER BY a.id) areas
                FROM area_cargo_persona acp
                INNER JOIN areas a ON a.id=acp.area_id AND a.estado=1
                INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1
                WHERE acp.estado=1
                AND cp.persona_id= ".$array['usuario'];
          $totalareas=DB::select($sql);
          $areas = $totalareas[0]->areas;
          $array['area'].=" AND rd.area_id IN (".$areas.") ";

        $sql="  SELECT r.ruta_flujo_id,r.id,tr.id as tramite_id,tr.id_union,tr.fecha_tramite,
                IFNULL(ts.nombre,'') as solicitante,
                IF(tr.tipo_persona=1 or tr.tipo_persona=6,
                    CONCAT(tr.paterno,' ',tr.materno,', ',tr.nombre),
                    IF(tr.tipo_persona=2,
                        CONCAT(tr.razon_social,' | RUC:',tr.ruc),
                        IF(tr.tipo_persona=3,
                            a.nombre,
                            IF(tr.tipo_persona=4 or tr.tipo_persona=5,
                                tr.razon_social,''
                            )
                        )
                    )
                ) des_solicitante,tr.sumilla
                from rutas r
                inner join tablas_relacion tr ON r.tabla_relacion_id=tr.id and tr.estado=1
                inner join rutas_detalle rd ON rd.ruta_id=r.id and rd.estado=1 AND rd.norden = 1
                LEFT join tipo_solicitante ts ON ts.id=tr.tipo_persona and ts.estado=1
                LEFT JOIN areas a ON a.id=tr.area_id
                WHERE r.estado=1
                ".$array['tramite'].
                 $array['area'];
        //echo $sql;
        $rd = DB::select($sql);
        
        return $rd;
    }

    public function getRutadetallev()
    {
        $area_id="";
        $flujo_id="";
        $ruta_detalle_id="";
        $adicional="";

        if ( Input::get('tramite') ) {
            $tramite= Input::get('tramite');

            $adicional=
            'WHERE tr.id_union like "'.$tramite.'%"
            AND rd.area_id IN (
                    SELECT a.id
                    FROM area_cargo_persona acp
                    INNER JOIN areas a ON a.id=acp.area_id AND a.estado=1
                    INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1
                    WHERE acp.estado=1
                    AND cp.persona_id='.Auth::user()->id.'
                    )
            AND rd.condicion=0
            AND rd.alerta=1 
            AND rd.estado=1
            AND rd.alerta_tipo>0 
            GROUP BY rd.id
            HAVING ( IFNULL(rd.dtiempo_final,"")!="" )
            ORDER BY fi,rd.created_at';
        }

        if ( Input::get('ruta_detalle_id') ) {
            $ruta_detalle_id= Input::get('ruta_detalle_id');
            $adicional='WHERE rd.id="'.$ruta_detalle_id.'"';
        }

        $set=DB::statement('SET group_concat_max_len := @@max_allowed_packet');
        $query =
            'SELECT rd.id, rd.dtiempo_final, r.flujo_id,
            CONCAT(t.nombre," : ",rd.dtiempo) tiempo,
            rd.observacion,r.ruta_flujo_id,r.id AS ruta_id,
            a.nombre AS area,f.nombre AS flujo,
            s.nombre AS software,tr.id_union AS id_doc,
            rd.norden, IFNULL(rd.fecha_inicio,"") AS fecha_inicio,
            IFNULL(rd.dtiempo_final,"") AS fecha_final,
            IFNULL(GROUP_CONCAT(
                CONCAT(
                    rdv.id,
                     "=>",
                    rdv.nombre,
                     "=>",
                    IF(rdv.finalizo=0,"Pendiente","Finalizó"),
                    "=>",
                    IF(rdv.condicion=1,"+1",
                        IF(rdv.condicion=2,"+2","NO")
                    )
                )
            SEPARATOR "|"),"") AS verbo,
            IFNULL(GROUP_CONCAT(
                CONCAT(
                    rdv.nombre,
                     "=>",
                    IF(rdv.finalizo=0,"<font color=#EC2121>Pendiente</font>","<font color=#22D72F>Finalizó</font>")
                )
            SEPARATOR "|"),"") AS verbo2,IFNULL(rd.dtiempo_final,"9999") fi,
            IFNULL(
                CalcularFechaFinal(
                rd.fecha_inicio, 
                (rd.dtiempo*t.totalminutos),
                rd.area_id
                )
            ,"<font color=#E50D1C>Tranquilo! el paso anterior aún no ha acabado</font>" )AS fecha_max, now() AS hoy,
            IF(rd.alerta_tipo=1,"NO CUMPLE TIEMPO",
                IF(rd.alerta_tipo=2,"NO CUMPLE TIEMPO ALERTA",
                    IF(rd.alerta_tipo=3,"ALERTA ACTIVADA","")
                )
            ) alerta_tipo,CONCAT(rd.alerta,"-",rd.alerta_tipo) codalerta
            FROM rutas_detalle rd
            INNER JOIN rutas r ON (r.id=rd.ruta_id AND r.estado=1)
            LEFT JOIN rutas_detalle_verbo rdv ON (rd.id=rdv.ruta_detalle_id AND rdv.estado=1)
            INNER JOIN areas a ON a.id=rd.area_id
            INNER JOIN flujos f ON f.id=r.flujo_id
            INNER JOIN tablas_relacion tr ON tr.id=r.tabla_relacion_id
            INNER JOIN softwares s ON s.id=tr.software_id
            INNER JOIN tiempos t ON t.id=rd.tiempo_id '.$adicional;
        $rd = DB::select($query);

        if ( Input::get('ruta_detalle_id') ) {
            return $rd[0];
        }
        else{
            return $rd;
        }
    }

    public function getListaareas()
    {
        $query='SELECT a.id,a.nombre,a.estado
                FROM area_cargo_persona acp
                INNER JOIN areas a ON a.id=acp.area_id AND a.estado=1
                INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1
                WHERE acp.estado=1
                AND cp.persona_id='.Auth::user()->id.
                ' ORDER BY a.nombre';
        $area=DB::select($query);
                
        return $area;
    }

    public static function getObservaciones()
    {
        $ruta_detalle_id = Input::get('ruta_detalle_id');
        $sql = "SELECT d.nombre AS tipo_documento, v.nombre AS verbo, dd.titulo AS documento, rdv.observacion, CONCAT(p.paterno, ' ', p.materno, ', ', p.nombre) AS usuario, rdv.updated_at AS fecha_observacion
                FROM rutas_detalle_verbo rdv 
                INNER JOIN verbos v ON v.id = rdv.verbo_id
                INNER JOIN personas p ON p.id = rdv.usuario_updated_at
                LEFT JOIN doc_digital dd ON dd.id = rdv.doc_digital_id
                LEFT JOIN documentos d ON d.id = rdv.documento_id
                WHERE rdv.estado = 1 
                AND IFNULL(rdv.observacion,'') != ''
                AND rdv.ruta_detalle_id = $ruta_detalle_id ";
        $data = DB::select($sql);
        return $data;
    }

    // ARCHIVOS PROCESO DESMONTE
    public static function verArchivosDesmontesMotorizado( $array )
    {
        $sql =" SELECT rd.archivo, rd.norden
                    FROM rutas_detalle rd  ";
        $sql .=" WHERE rd.estado=1 AND rd.archivo!='' AND rd.condicion = 0 ".
                $array['ruta_id'].
                $array['norden'];
        $sql .=" GROUP BY rd.norden
                 ORDER BY rd.norden";
        $oData['data'] = DB::select($sql);
        return $oData;
    }
}
?>
