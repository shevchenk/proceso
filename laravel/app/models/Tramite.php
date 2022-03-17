<?php

class Tramite extends Eloquent {
	protected $fillable = [];

	public static function getAllTramites($array){
		$sql ="select tr.id_union,t.id codigo,ct.nombre_clasificador_tramite tramite,t.fecha_tramite fecha_ingreso,CONCAT_WS(' ',p.nombre,p.paterno,p.materno) persona,t.estado estado,t.observacion observacion,'' area,t.documento numdoc 
			from tramites t 
			inner join personas p on t.persona_id=p.id 
			inner join clasificador_tramite ct on t.clasificador_tramite_id=ct.id
			inner join tablas_relacion tr on tr.tramite_id=t.id
			where t.estado=1";

		if(Input::get('buscar')){
			$sql.=" and ct.nombre_clasificador_tramite LIKE '%".Input::get('buscar')."%'";
                        $sql.="or (1=1 ";
                        $sql.=$array['where'];
                        $sql.=")";
		}

//		if(Input::get('persona')){
//			$sql.=" and t.persona_id='".Input::get('persona')."'";
//		}

		$r= DB::select($sql);
        return $r; 
	}

	public static function getTramiteById($id){
		$sql = "select tr.id_union,t.id tramiteid,a.nombre as area,t.persona_id personaid,t.tipo_documento_id tdocid,t.clasificador_tramite_id ctid,t.tipo_solicitante_id tsid,t.area_id areaid,t.fecha_tramite fregistro,p.dni dniU,p.nombre nombusuario,p.paterno apepusuario,p.materno apemusuario,
				t.empresa_id empresaid,e.ruc ruc,(CASE e.tipo_id when 1 then 'Natural' when 2 then 'Jur��dica' when 3 then 'Organizaci��n Social' else e.tipo_id end) as tipoempresa,e.razon_social as empresa,e.razon_social as empresa,e.nombre_comercial nomcomercial,e.direccion_fiscal edireccion,
				e.telefono etelf,e.fecha_vigencia efvigencia,CONCAT_WS(' ',p2.nombre,p2.paterno,p2.materno) as reprelegal,
				p2.dni repredni,
				ts.nombre solicitante,tt.nombre_tipo_tramite tipotramite,d.nombre tipodoc,ct.nombre_clasificador_tramite as tramite,
				t.fecha_tramite fecha ,t.nro_folios folio, t.documento as nrotipodoc,ts.pide_empresa statusemp, t.observacion,
				CONCAT(p3.paterno,' ',p3.materno,', ',p3.nombre) operador,
				a2.nombre area_operador
				from tramites t 
				INNER JOIN areas a ON a.id=t.area_id
               	INNER JOIN tablas_relacion tr on tr.tramite_id=t.id
				INNER JOIN personas p on p.id=t.persona_id 
				INNER JOIN clasificador_tramite ct on ct.id=t.clasificador_tramite_id
				INNER JOIN tipo_tramite tt on tt.id=ct.tipo_tramite_id 
				LEFT JOIN empresas e on e.id=t.empresa_id 
				LEFT JOIN personas p2 on p2.id=e.persona_id
				INNER JOIN tipo_solicitante ts on ts.id=t.tipo_solicitante_id 
				INNER JOIN documentos d on d.id=t.tipo_documento_id 
				INNER JOIN personas p3 on p3.id=t.usuario_created_at
				INNER JOIN areas a2 on a2.id=p3.area_id
				WHERE t.estado = 1 and t.id=";

				if( trim($id)!='' ){
					$sql.=$id;
				}
				else{
					$sql.=Input::get('idtramite');
				}
				//echo $sql;
		$r= DB::select($sql);
        return $r[0]; 
	}

	public static function getSolicitantes(){
		$id = Input::get('tramite_id');
		$sql =" SELECT 
					CASE
						WHEN ta.empresa_id IS NULL THEN 'Persona'
						ELSE 'Empresa'
					END AS tipo_solicitante,
					CASE
						WHEN ta.empresa_id IS NULL THEN CONCAT(p.paterno,' ',p.materno,' ',p.nombre)
						ELSE e.razon_social
					END AS solicitante,
					CASE
						WHEN ta.empresa_id IS NULL THEN p.dni
						ELSE e.ruc				
					END AS id_solicitante,
					CASE
						WHEN ta.empresa_id IS NULL THEN p.direccion
						ELSE e.direccion_fiscal
					END AS dir_solicitante,
					CASE
						WHEN ta.empresa_id IS NULL THEN CONCAT(p.celular, ' / ', p.telefono)
						ELSE e.telefono
					END AS tel_solicitante
				FROM tramites_anexo ta
				INNER JOIN personas p ON p.id = ta.persona_id 
				LEFT JOIN empresas e ON e.id = ta.empresa_id 
				WHERE ta.estado = 1 AND ta.tramite_id = '$id'";
		$r= DB::select($sql);
        return $r; 
	}
}
