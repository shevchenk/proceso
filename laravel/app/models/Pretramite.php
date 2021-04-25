<?php

class Pretramite extends Eloquent {
	protected $fillable = [];


	public static function getPreTramites(){
        $filtro = ''; $estados = '';
        if( Input::has('filtro_fecha') AND Input::get('filtro_fecha')!='' ){
            $filtro = ' AND DATE(pt.fecha_pretramite) = \''.Input::get('filtro_fecha').'\'';
        }

        if( Input::has('persona') AND Input::get('persona') != 0 ){
            $filtro .= ' AND pt.persona_id = '.Input::get('persona');
        }

        if( Input::has('estado_tramite') AND is_array( Input::get('estado_tramite') ) ){
            $estados = implode(",", Input::get('estado_tramite'));
            $filtro .= ' AND pt.estado_atencion IN ('.$estados.')';
        }

        $sql = "SELECT pt.id as pretramite,CONCAT_WS(' ',p.nombre,p.paterno,p.materno) as usuario,e.razon_social as empresa,
				ts.nombre solicitante,tt.nombre_tipo_tramite tipotramite,d.nombre tipodoc,ct.nombre_clasificador_tramite as tramite,
                pt.fecha_pretramite fecha, pt.documento, pt.ruta_archivo, 
                IF(pt.estado_atencion = 0, 'Pendiente',
                    IF(pt.estado_atencion = 1, 'Aprobado', 'Desaprobado')
                ) atencion, pt.estado_atencion, pt.updated_at, t.observacion, tr.id_union, DATE(t.fecha_tramite) AS fecha_tramite,
                pt.observacion AS observacion2,
                (SELECT GROUP_CONCAT('<b>', tr_aux.id_union, ' </b><br>' ,tr_aux.fecha_tramite) 
                FROM tablas_relacion tr_aux
                INNER JOIN tramites t_aux ON t_aux.id = tr_aux.tramite_id AND t_aux.estado = 1 
                WHERE t_aux.persona_id = pt.persona_id 
                AND tr_aux.estado = 1) expediente
				FROM pretramites pt 
				INNER JOIN personas p on p.id=pt.persona_id 
				INNER JOIN clasificador_tramite ct on ct.id=pt.clasificador_tramite_id
				INNER JOIN tipo_tramite tt on tt.id=ct.tipo_tramite_id 
				INNER JOIN tipo_solicitante ts on ts.id=pt.tipo_solicitante_id 
				INNER JOIN documentos d on d.id=pt.tipo_documento_id 
                LEFT JOIN empresas e on e.id=pt.empresa_id 
                LEFT JOIN tramites t ON t.pretramite_id=pt.id AND t.estado = 1
                LEFT JOIN tablas_relacion tr ON tr.tramite_id=t.id AND tr.estado = 1 
                WHERE pt.estado = 1 ".
                $filtro."
                ORDER BY pt.fecha_pretramite DESC";
		$r= DB::select($sql);
        return $r; 		
    }

    public static function getTramites(){
        $sql = "select pt.id as idtramite,CONCAT_WS(' ',p.nombre,p.paterno,p.materno) as usuario,e.razon_social as empresa,
                ts.nombre solicitante,tt.nombre_tipo_tramite tipotramite,d.nombre tipodoc,ct.nombre_clasificador_tramite as tramite,
                pt.fecha_tramite fecha  
                from tramites pt 
                INNER JOIN personas p on p.id=pt.persona_id 
                INNER JOIN clasificador_tramite ct on ct.id=pt.clasificador_tramite_id
                INNER JOIN tipo_tramite tt on tt.id=ct.tipo_tramite_id 
                LEFT JOIN empresas e on e.id=pt.empresa_id 
                INNER JOIN tipo_solicitante ts on ts.id=pt.tipo_solicitante_id 
                INNER JOIN documentos d on d.id=pt.tipo_documento_id 
                WHERE pt.estado = 1 AND pt.usuario_created_at=".Auth::user()->id.
                " ORDER BY pt.id DESC 
                LIMIT 0,10";
        $r= DB::select($sql);
        return $r;      
    }

    public static function getPreTramiteById(){
        $sql = "";
    	$sql.= "select pt.id as pretramite,a.nombre as area,pt.persona_id personaid,pt.tipo_documento_id tdocid,pt.clasificador_tramite_id ctid,pt.tipo_solicitante_id tsid,pt.area_id areaid,pt.fecha_pretramite fregistro,p.dni dniU,p.nombre nombusuario,p.paterno apepusuario,p.materno apemusuario,
				pt.empresa_id empresaid,e.ruc ruc,e.tipo_id tipoempresa_id,e.razon_social as empresa,e.nombre_comercial nomcomercial,e.direccion_fiscal edireccion,
				e.telefono etelf,e.fecha_vigencia efvigencia,CONCAT_WS(' ',p2.nombre,p2.paterno,p2.materno) as reprelegal,
				p2.dni repredni,
				ts.nombre solicitante,tt.nombre_tipo_tramite tipotramite,d.nombre tipodoc,ct.nombre_clasificador_tramite as tramite,
                pt.fecha_pretramite fecha ,pt.nro_folios folio, pt.documento as nrotipodoc,ts.pide_empresa statusemp,
                CASE e.tipo_id WHEN 1 THEN 'Natural' WHEN 2 THEN 'Juridico' WHEN 3 THEN 'Organizacion Social' END as tipoempresa,
                p.email, p.direccion, p.celular, p.telefono, pt.ruta_archivo
				from pretramites pt 
				INNER JOIN personas p on p.id=pt.persona_id 
				INNER JOIN clasificador_tramite ct on ct.id=pt.clasificador_tramite_id
				INNER JOIN tipo_tramite tt on tt.id=ct.tipo_tramite_id 
				LEFT JOIN empresas e on e.id=pt.empresa_id 
				LEFT JOIN personas p2 on p2.id=e.persona_id
				INNER JOIN tipo_solicitante ts on ts.id=pt.tipo_solicitante_id 
				INNER JOIN documentos d on d.id=pt.tipo_documento_id 
                LEFT JOIN areas a on a.id=pt.area_id 
				WHERE pt.estado = 1";

        if(Input::has('idpretramite')){
            $sql.=" and pt.id=".Input::get('idpretramite');
        }else if(Input::has('persona')){
           $sql.=" and pt.persona_id=".Input::get('persona');
        }

        if(Input::get('validacion')){
            $query = "select id tramiteid from tramites where pretramite_id=".Input::get('idpretramite');
            $tramite= DB::select($query);       
            if(count($tramite)>0){ //si ya es un tramite
                return $tramite;
            }else{ //si aun no 
                return DB::select($sql);
            }
        }else{
           return DB::select($sql); 
        }
    }


    public static function getEmpresasUser(){
        $sql = '';
    	$sql.= "select e.*,CONCAT_WS(' ',p.nombre,p.paterno,p.materno) as representante,p.dni as dnirepre,p.area_id,
		CASE e.tipo_id WHEN 1 THEN 'Natural' WHEN 2 THEN 'Juridico' WHEN 3 THEN 'Organizacion Social' END as tipo  
                from empresas e 
                LEFT JOIN empresa_persona ep on e.id=ep.empresa_id and ep.estado=1 
                LEFT JOIN personas p on e.persona_id=p.id
                where e.estado=1 GROUP BY e.id";

        if(Input::has('persona')){
            $sql.=" and ep.persona_id=".Auth::user()->id;
        }

        $r= DB::select($sql);
        return $r; 
    }

    public static function getClasificadoresTramite(){
    	$clasificadores=DB::table('clasificador_tramite AS ct')
                ->join('rutas_flujo_detalle AS rfd',function($join){
                    $join->on('rfd.ruta_flujo_id','=','ct.ruta_flujo_id')
                    ->where('rfd.norden','=',2)
                    ->where('rfd.estado','=',1);
                })
                ->select(DB::raw('ct.*, rfd.area_id'))
                ->where( 
                    function($query){
                    	if ( Input::get('buscar') ) {
                            $query->where('ct.id','=',Input::get('buscar'));
                            $query->orWhere('ct.nombre_clasificador_tramite','LIKE','%'.Input::get('buscar').'%');
                        }
                        if ( Input::get('tipotra') ) {
                            $query->where('ct.tipo_tramite_id','=',Input::get('tipotra'));
                        }
                        $query->where('ct.estado','=','1');
                        $query->where('ct.estado_final','=','1');
                    }
                )
                ->get();  
        return $clasificadores;
    }

    public static function getrequisitosbyClaTramite(){
    	$requisitos=DB::table('requisitos')
                ->select()               
                ->where( 
                    function($query){
                    	if ( Input::get('idclatramite') ) {
                            $query->where('clasificador_tramite_id','=',Input::get('idclatramite'));
                        }
                        $query->where('estado','=','1');
                    }
                )
                ->get();  
        return $requisitos;
    }

    public static function getAreasbyClaTramite(){
        $areas=DB::table('clasificador_tramite_area as cta')
                ->join('areas as a', 'cta.area_id', '=', 'a.id')
                ->select('a.id','a.nombre')               
                ->where( 
                    function($query){
                        if ( Input::get('idc') ) {
                            $query->where('cta.clasificador_tramite_id','=',Input::get('idc'));
                        }
                        $query->where('cta.estado','=','1');
                    }
                )
                ->get();  
        return $areas;
    }
    
        public static function Correlativo($unidad_documentaria){
        
    	$año= date("Y");
        $r2=array(array('correlativo'=>'000001','ano'=>$año));
    	/*$sql = "SELECT LPAD(id+1,6,'0') as correlativo,'$año' ano FROM doc_digital ORDER BY id DESC LIMIT 1";*/
        $sql = "SELECT IFNULL(LPAD(MAX(p.correlativo)+1,6,'0'),LPAD(1,6,'0')) as correlativo 
                FROM pretramites p 
                inner join clasificador_tramite ct on ct.id = p.clasificador_tramite_id AND ct.unidad_documentaria = '$unidad_documentaria'
                WHERE p.estado=1 
                AND YEAR(p.created_at)=YEAR(CURDATE())
                ORDER BY p.correlativo DESC LIMIT 1";
    	$r= DB::select($sql);
        return (isset($r[0])) ? $r[0] : $r2[0];
        
    }

    public static function FileToFile($file, $url)
    {
        $urld=explode("/",$url);
        $urlt=array();
        for ($i=0; $i < (count($urld)-1) ; $i++) { 
            array_push($urlt, $urld[$i]);
            $urltexto=implode("/",$urlt);
            if ( !is_dir($urltexto) ) {
                mkdir($urltexto,0777);
            }
        }
        
        list($type, $file) = explode(';', $file);
        list(, $type) = explode('/', $type);
        if ($type=='jpeg') $type='jpg';
        if ($type=='x-icon') $type='ico';
        if (strpos($type,'document')!==False) $type='docx';
        if (strpos($type,'msword')!==False) $type='doc';
        if (strpos($type,'presentation')!==False) $type='pptx';
        if (strpos($type,'powerpoint')!==False) $type='ppt';
        if (strpos($type, 'sheet') !== False) $type='xlsx';
        if (strpos($type, 'excel') !== False) $type='xls';
        if (strpos($type, 'pdf') !== False) $type='pdf';
        if ($type=='plain') $type='txt';
        list(, $file)      = explode(',', $file);
        $file = base64_decode($file);
        file_put_contents($url , $file);
        return $url.$type;
    }
}
