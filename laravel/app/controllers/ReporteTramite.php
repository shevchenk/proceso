<?php

class ReporteTramite extends Eloquent 
{
    public static function TramiteUnico( $array )
    {
        $sql =" SELECT tr.id_union AS tramite,IF(re.referido=tr.id_union,'',re.referido) detalle,
                re.norden, r.id, r.ruta_flujo_id, tr.sumilla as sumilla,
                IF( MIN( IF( rd.dtiempo_final IS NULL AND rd.fecha_inicio IS NOT NULL, 0, 1) ) = 0
                                AND MAX( rd.alerta_tipo ) < 2, 'Inconcluso',
                                IF( (MIN( IF( rd.dtiempo_final IS NOT NULL AND rd.fecha_inicio IS NOT NULL, 0, 1) ) = 0
                                                OR MIN( IF( rd.dtiempo_final IS NULL AND rd.fecha_inicio IS NULL, 0, 1) ) = 0)
                                                AND MAX( rd.alerta_tipo ) > 1, 'Trunco', 'Concluido'
                                )
                ) estado,
                f.nombre proceso,
                IFNULL(r.fecha_inicio,'') AS fecha_inicio
                FROM referidos re 
                INNER JOIN rutas r ON r.id=re.ruta_id AND r.estado=1
                INNER JOIN tablas_relacion tr ON tr.id=r.tabla_relacion_id AND tr.estado=1
                INNER JOIN rutas_detalle rd ON rd.ruta_id=r.id AND rd.estado=1
                INNER JOIN flujos f ON f.id=r.flujo_id
                WHERE re.estado=1".
                $array['where'].
                "GROUP BY r.id";

        $r= DB::select($sql);
        return $r;
    }

    public static function TramiteAnulado( $array )
    {
        $sql =" SELECT tr.id_union AS tramite,
                r.id, r.ruta_flujo_id, tr.sumilla as sumilla,
                f.nombre proceso,
                IFNULL(r.fecha_inicio,'') AS fecha_inicio,
                IFNULL(tstm.nombre,'') AS tipo_solicitante,
                IFNULL(
                    CASE
                        WHEN tm.id IS NOT NULL AND tstm.id = 0 THEN atm.nombre
                        WHEN tm.id IS NOT NULL AND tstm.pide_empresa = 0 THEN CONCAT(ptm.paterno,' ',ptm.materno,' ',ptm.nombre)
                        WHEN tm.id IS NOT NULL AND tstm.pide_empresa = 1 THEN etm.razon_social
                        ELSE (SELECT nombre FROM areas WHERE id = tr.area_id)
                    END
                , '') AS persona, 
                l.local, lo.local local_origen,
                CONCAT(ptma.paterno,' ',ptma.materno,' ',ptma.nombre) responsable_anulacion, tm.updated_at fecha_anulacion
                FROM tablas_relacion tr 
                INNER JOIN rutas r ON tr.id=r.tabla_relacion_id AND r.estado=0 
                INNER JOIN referidos re ON re.ruta_id = r.id
                INNER JOIN rutas_detalle rd ON rd.ruta_id=r.id AND rd.estado=1
                INNER JOIN flujos f ON f.id=r.flujo_id
                LEFT JOIN tramites tm ON tm.id = tr.tramite_id
                LEFT JOIN locales l ON l.id = r.local_id
                LEFT JOIN locales lo ON lo.id = r.local_origen_id
                LEFT JOIN personas ptm ON ptm.id = tm.persona_id 
                LEFT JOIN empresas etm ON etm.id = tm.empresa_id 
                LEFT JOIN areas atm ON atm.id = tm.area_id_sol
                LEFT JOIN tipo_solicitante tstm ON tstm.id = tm.tipo_solicitante_id
                LEFT JOIN personas ptma ON ptma.id = tm.usuario_updated_at
                WHERE tr.estado=0".
                $array['where'].
                " GROUP BY r.id ".$array['having'];

        $r= DB::select($sql);
        return $r;
    }

    public static function TramiteDetalle( $array )
    {
        $sql="  SELECT rd.id, rd.ruta_id,
                       IFNULL(a.nombre,'') as area, 
                       rd.condicion,
                       IFNULL(t.nombre,'') as tiempo, 
                       IFNULL(dtiempo,'') as dtiempo,
                       IFNULL(rd.fecha_inicio,'') as fecha_inicio,
                       IFNULL(dtiempo_final,'') as dtiempo_final,
                       IFNULL(motivo_retorno, '') as motivo_retorno,
                       norden,
                       alerta,
                       alerta_tipo,
                       /*ACA SE MUESTRA EL NOMBRE COMPLETO DE LA PERSONA QUE RETORNO*/
                        IF( rd.condicion=3,
                            IFNULL(CONCAT('<b>',p1.paterno,' ',p1.materno,', ',p1.nombre,'</b>','<br>','<br>',a.nombre,'</br>','</br>'),''),
                            ''
                        ) as retorno,
                IFNULL(GROUP_CONCAT(
                  CONCAT(
                    '<b>',
                    IFNULL(v.orden,' '),
                    '</b>',
                    '.- ',
                    ro.nombre,
                    ' tiene que ',
                    vs.nombre,
                    ' ',
                    IFNULL(d.nombre,''),
                    ' (',
                    v.nombre,
                    ' )'
                  )
                    ORDER BY v.orden ASC
                SEPARATOR '|'),'') AS verbo2,
                IFNULL(GROUP_CONCAT(
                    CONCAT(
                        '<b>',
                        IFNULL(v.orden,' '),
                        '</b>',
                        '.- ',
                        IF(v.finalizo=0,
                            '<font color=#EC2121>Pendiente</font>'
                            ,CONCAT('<font color=#22D72F>Finalizó</font>(',p.paterno,' ',p.materno,', ',p.nombre,' '
                                ,IFNULL(CONCAT('<b>',
                                    IF( v.doc_digital_id > 0,CONCAT(v.documento,'<a class=\'btn btn-default btn-sm\' href=\'doc_digital/', v.doc_digital_id ,'\' target=\'_blank\'><i class=\'fa fa-eye\'></i></a>'), v.documento)
                                    ,'</b>'),''),'//'
                                ,IFNULL(v.observacion,''),'//'
                                ,IFNULL(CONCAT('<b>',v.updated_at,'</b>'),''),')' 
                            ) 
                        )
                    )
                    ORDER BY v.orden ASC
                SEPARATOR '|'),'') AS ordenv,
                rd.archivo

                FROM rutas AS r 
                INNER JOIN rutas_detalle AS rd ON r.id = rd.ruta_id AND rd.estado = 1
                INNER JOIN rutas_detalle_verbo AS v ON rd.id = v.ruta_detalle_id AND v.estado=1
                INNER JOIN areas AS a ON rd.area_id = a.id 
                INNER JOIN tiempos AS t ON rd.tiempo_id = t.id 
                LEFT JOIN roles as ro ON v.rol_id=ro.id
                LEFT JOIN verbos as vs ON v.verbo_id=vs.id
                LEFT JOIN documentos as d ON v.documento_id=d.id
                LEFT JOIN personas as p ON v.usuario_updated_at=p.id
                LEFT JOIN personas as p1 ON rd.usuario_retorno=p1.id

                WHERE r.estado = 1".
                $array['where']."
                GROUP BY rd.id";

        $set=DB::statement('SET group_concat_max_len := @@max_allowed_packet');
        $r= DB::select($sql);
        return $r;
    }

    public static function ExpedienteUnico(){
        $r = array([],[],[]);
        $ruta_id = Input::get('ruta_id');
            
        $sql = "SELECT re.ruta_id,re.ruta_detalle_id,re.referido,re.fecha_hora_referido fecha_hora,f.nombre proceso,a.nombre area,re.norden, 'r' tipo, re.doc_digital_id
                FROM referidos re 
                INNER JOIN referidos_relaciones rr ON rr.ruta_id = $ruta_id AND rr.ruta_id_ref = re.ruta_id AND rr.estado = 1
                INNER JOIN rutas r ON re.ruta_id=r.id AND r.estado = 1
                INNER JOIN flujos f ON r.flujo_id=f.id 
                LEFT JOIN rutas_detalle rd ON re.ruta_detalle_id=rd.id 
                LEFT JOIN areas a ON rd.area_id=a.id  
                WHERE re.estado=1 
                UNION
                SELECT re.ruta_id,re.ruta_detalle_id,sustento,fecha_hora_sustento fecha_hora,f.nombre proceso,a.nombre area,rd.norden,'s' tipo,s.doc_digital_id
                FROM sustentos s
                INNER JOIN referidos re ON re.id=s.referido_id AND re.estado = 1
                INNER JOIN referidos_relaciones rr ON rr.ruta_id = $ruta_id AND rr.ruta_id_ref = re.ruta_id AND rr.estado = 1
                INNER JOIN rutas r ON re.ruta_id=r.id AND r.estado = 1
                INNER JOIN flujos f ON r.flujo_id=f.id
                INNER JOIN rutas_detalle rd ON rd.id=s.ruta_detalle_id
                INNER JOIN areas a ON rd.area_id=a.id  
                WHERE s.estado = 1 
                ORDER BY ruta_id,norden,tipo";
        $r[0]=DB::select($sql);
    
        $sql = "SELECT re.ruta_id,re.ruta_detalle_id,re.referido,re.fecha_hora_referido fecha_hora,f.nombre proceso,a.nombre area,re.norden, 'r' tipo, re.doc_digital_id
                FROM referidos re 
                INNER JOIN rutas r ON re.ruta_id=r.id AND r.estado = 1
                INNER JOIN flujos f ON r.flujo_id=f.id 
                LEFT JOIN rutas_detalle rd ON re.ruta_detalle_id=rd.id 
                LEFT JOIN areas a ON rd.area_id=a.id  
                WHERE re.estado=1 and re.ruta_id = $ruta_id
                UNION
                SELECT re.ruta_id,re.ruta_detalle_id,sustento,fecha_hora_sustento fecha_hora,f.nombre proceso,a.nombre area,rd.norden,'s' tipo,s.doc_digital_id
                FROM sustentos s
                INNER JOIN referidos re ON re.id=s.referido_id AND re.ruta_id = $ruta_id
                INNER JOIN rutas r ON re.ruta_id=r.id AND r.estado = 1
                INNER JOIN flujos f ON r.flujo_id=f.id
                INNER JOIN rutas_detalle rd ON rd.id=s.ruta_detalle_id
                INNER JOIN areas a ON rd.area_id=a.id  
                WHERE s.estado = 1 and re.estado = 1
                ORDER BY ruta_id,norden,tipo";
        $r[1]=DB::select($sql);
    
        $sql = "SELECT re.ruta_id,re.ruta_detalle_id,re.referido,re.fecha_hora_referido fecha_hora,f.nombre proceso,a.nombre area,re.norden, 'r' tipo, re.doc_digital_id
                FROM referidos re 
                INNER JOIN referidos_relaciones rr ON rr.ruta_id_ref = $ruta_id AND rr.ruta_id = re.ruta_id AND rr.estado = 1
                INNER JOIN rutas r ON re.ruta_id=r.id AND r.estado = 1
                INNER JOIN flujos f ON r.flujo_id=f.id 
                LEFT JOIN rutas_detalle rd ON re.ruta_detalle_id=rd.id 
                LEFT JOIN areas a ON rd.area_id=a.id  
                WHERE re.estado=1 
                UNION
                SELECT re.ruta_id,re.ruta_detalle_id,sustento,fecha_hora_sustento fecha_hora,f.nombre proceso,a.nombre area,rd.norden,'s' tipo,s.doc_digital_id
                FROM sustentos s
                INNER JOIN referidos re ON re.id=s.referido_id AND re.estado = 1
                INNER JOIN referidos_relaciones rr ON rr.ruta_id_ref = $ruta_id AND rr.ruta_id = re.ruta_id AND rr.estado = 1
                INNER JOIN rutas r ON re.ruta_id=r.id AND r.estado = 1
                INNER JOIN flujos f ON r.flujo_id=f.id
                INNER JOIN rutas_detalle rd ON rd.id=s.ruta_detalle_id
                INNER JOIN areas a ON rd.area_id=a.id  
                WHERE s.estado = 1 
                ORDER BY ruta_id,norden,tipo";
        $r[2]=DB::select($sql);
        
        return $r;
    }
}
