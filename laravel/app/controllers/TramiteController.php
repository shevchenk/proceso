<?php

class TramiteController extends BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /tramite
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	public function postListartramites(){
                $array=array();
                $array['where']='';
                
            if( Input::has("buscar") AND Input::get('buscar')!='' ){
                 $buscar=explode(" ",trim(Input::get('buscar')));
                    for($i=0; $i<count($buscar); $i++){
                       $array['where'].=" AND tr.id_union LIKE '%".$buscar[$i]."%' ";
                    }
            }
                
		$rst=Tramite::getAllTramites($array);
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
	}

	public function postGetbyid(){
		$rst=Tramite::getTramiteById();
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
	}

	public function getVouchertramite()
	{

		/*get data*/
		$rst=Tramite::getTramiteById();
		$data = $rst[0];
		/*end get data*/

		$html = "<html>";
		$html.= "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
		$html.="
				<body>
				<style>
				table, tr , td, th {
				text-align: left !important;
				border-collapse: collapse;
				border: 1px solid #ccc;
				width: 100%;
				font-size: .9em;
				font-family: arial, sans-serif;
				}
				Th, td {
				padding: 5px;
				}
				</style>";
		$html.="<h3>VOCHER TRAMITE</h3>";
		$html.="
				<table>
					<tr>
						<th>FECHA: </th>
						<td>".$data->tipotramite."</td>
					</tr>
					<tr>
						<th>COD TRAMITE: </th>
						<td>".$data->tramiteid."</td>
					</tr>";

		$html.="
					<tr>
						<th>DNI: </th>
						<td>".$data->dniU."</td>
					</tr>
					<tr>
						<th>APELLIDO PATERNO: </th>
						<td>".$data->apepusuario."</td>
					</tr>
					<tr>
						<th>APELLIDO MATERNO: </th>
						<td>".$data->apemusuario."</td>
					</tr>
					<tr>
						<th>NOMBRE USUARIO: </th>
						<td>".$data->nombusuario."</td>
					</tr>";
					
		if($data->ruc){
			$html.="
						<tr>
							<th>RUC: </th>
							<td>".$data->ruc."</td>
						</tr>
						<tr>
							<th>TIPO EMPRESA: </th>
							<td>".$data->tipoempresa."</td>
						</tr>
						<tr>
							<th>RAZON SOCIAL: </th>
							<td>".$data->empresa."</td>
						</tr>
						<tr>
							<th>NOMBRE COMERCIAL: </th>
							<td>".$data->nomcomercial."</td>
						</tr>
						<tr>
							<th>DIRECCION FISCAL: </th>
							<td>".$data->edireccion."</td>
						</tr>
						<tr>
							<th>TELEFONO: </th>
							<td>".$data->etelf."</td>
						</tr>
						<tr>
							<th>REPRESENTANTE: </th>
							<td>".$data->reprelegal."</td>
						</tr>";
		}

		$html.="		<tr>
							<th>NOMBRE TRAMITE: </th>
							<td>".$data->tramite."</td>
						</tr>					
						<tr>
							<th>AREA: </th>
							<td>".$data->area."</td>
						</tr>";
				
		$html.="</table><hr>
		</body>
		</html>";

		return PDF::load($html, 'A4', 'landscape')->download('voucher-tramite-'.$data->tramiteid);
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /tramite/create
	 *
	 * @return Response
	 */
	public function postCreate()
	{
		//$img = $_FILES['txt_file'];
		$data = $_POST;

		if($data){ //img y data
			/*$name = md5($img['name']).'_'.$data['txt_pretramiteid'].'.jpeg';
			//$root = public_path().'/img/tramite/'.$name;
			if(move_uploaded_file($img['tmp_name'], $root)){ //move
			}*/
			$name = '';
			$tramite = Tramite::where('pretramite_id', $data['txt_pretramiteid'])->first();
			DB::beginTransaction();
			
			if( !isset($tramite->id) ){
			
				$pretramite = Pretramite::find($data['txt_pretramiteid']);
				$pretramite->estado_atencion = $data['rdb_estado'];
				$pretramite->observacion = trim($data['txt_observaciones']);
				$pretramite->save();

				$persona = Persona::find($data['txt_personaid']);
				$persona->telefono = $data['txt_usertelf'];
				$persona->celular = $data['txt_usercel'];
				$persona->email = urldecode($data['txt_useremail']);
				$persona->direccion = urldecode($data['txt_userdirec']);
				$persona->save();
			
				if( $pretramite->estado_atencion == 1 ){
					$clasificadorTramite = ClasificadorTramite::find($data['txt_ctramite']);
					if( $clasificadorTramite->documento_id == '' ){
						DB::rollback();
						return  array(
							'rst'=>2,
							'msj'=>'El servicio configurado no cuenta con documento asignado',
						);
					}
					$codigo = Pretramite::Correlativo($clasificadorTramite->documento_id);
					$documento = DB::table('documentos')
								->select('nombre', 'nemonico')
								->where('id',$clasificadorTramite->documento_id)
								->first();
					//$codigo->correlativo = '000075'; //para probar correlativo
					$titulo = $codigo->correlativo;
					$buscar = array('@@@@@@','@@@@@','@@@@','@@@','@@','@','####','##');
					$reemplazar = array( str_pad( $titulo, 6, "0", STR_PAD_LEFT ), str_pad( $titulo, 5, "0", STR_PAD_LEFT ),
						str_pad( $titulo, 4, "0", STR_PAD_LEFT ), str_pad( $titulo, 3, "0", STR_PAD_LEFT ),
						str_pad( $titulo, 2, "0", STR_PAD_LEFT ), str_pad( $titulo, 1, "0", STR_PAD_LEFT ),
						date("Y"), date("y")
					);
					$titulofinal = str_replace( $buscar, $reemplazar, $documento->nemonico );
					/*******************************************************************************/
					$pretramite->titulo = $titulofinal;
					$pretramite->correlativo = $codigo->correlativo;
					$pretramite->año = date("Y");
					

					$cantidad=true;
					$conteo=0;
					$conteoMax=10;
					
					while ( $cantidad==true ) {
						$cantidad=false;
						try {
							$pretramite->save();
						} catch (Exception $e) {
							$d=explode("duplicate",strtolower($e));
							if(count($d)>1){
								$cantidad=true;
								$pretramite->correlativo++;
								$titulo = $pretramite->correlativo;
								$reemplazar = array( str_pad( $titulo, 6, "0", STR_PAD_LEFT ), str_pad( $titulo, 5, "0", STR_PAD_LEFT ),
									str_pad( $titulo, 4, "0", STR_PAD_LEFT ), str_pad( $titulo, 3, "0", STR_PAD_LEFT ),
									str_pad( $titulo, 2, "0", STR_PAD_LEFT ), str_pad( $titulo, 1, "0", STR_PAD_LEFT ),
									date("Y"), date("y")
								);
								$titulofinal = str_replace( $buscar, $reemplazar, $documento->nemonico );
								$pretramite->titulo = $titulofinal;
							}
							else{
								$conteo=$conteoMax+1;
							}
						}
						$conteo++;
						if($conteo==$conteoMax){
							$cantidad=false;
						}
					}

					if( $conteo >= 10 ){
						DB::rollback();
						return  array(
								'rst'=>2,
								'msj'=>'Problemas al generar el correlativo, comuniquese con el área de TI',
								'clasi' => $clasificadorTramite,
								'codigo' => $titulo
							);
					}

					$tramite = new Tramite;
					$tramite['pretramite_id'] = $data['txt_pretramiteid'];
					$tramite['persona_id'] = $data['txt_personaid'];
	
					if($data['txt_empresaid']){
						$tramite['empresa_id'] = $data['txt_empresaid'];      	
					}
	
					$tramite['area_id'] = $data['txt_area'];
					$tramite['local_id'] = $data['txt_local'];
					$tramite['local_origen_id'] = $data['txt_local'];
					$tramite['clasificador_tramite_id'] = $data['txt_ctramite'];
					$tramite['tipo_solicitante_id'] = $data['txt_tsolicitante'];
					$tramite['tipo_documento_id'] = $data['txt_tdocumento'];
					$tramite['documento'] =$data['txt_tdoc'];
					$tramite['nro_folios'] = $data['txt_folio'];
					$tramite['observacion'] = trim($data['txt_observaciones']);
					$tramite['imagen'] = $name;
					$tramite['fecha_tramite'] = date('Y-m-d H:i:s');
					$tramite['usuario_created_at'] = Auth::user()->id;
					$tramite->save();
				
					/*start to create process*/
					if($tramite->id){ // if registry was succesfully
						$anexo = new Anexo;
						$anexo['tramite_id'] = $tramite->id;
						$anexo['persona_id'] = $tramite->persona_id;
						if($data['txt_empresaid']){
							$anexo['empresa_id'] = $data['txt_empresaid'];
						}
						$anexo['fecha_anexo'] = $tramite->fecha_tramite;
						$anexo['documento_id'] = $tramite->tipo_documento_id;
						$anexo['nombre'] = 'A';
						$anexo['nro_folios'] = $tramite->nro_folios;
						$anexo['obeservacion'] = $tramite->observacion;
						$anexo['usuario_created_at'] = Auth::user()->id;
						$anexo->save();
						//$codigo = str_pad($tramite->id, 7, "0", STR_PAD_LEFT).'-'.date('Y'); //cod
						//$codigo= $clasificadorTramite->unidad_documentaria.'-'.$codigo->correlativo.'-'.date('Y');
						/*get ruta flujo*/
						/* $sql="SELECT flujo_id
								FROM areas_internas
								WHERE area_id='".$tramite->area_id."' 
								AND estado=1";
						$area_interna=DB::select($sql);*/
						$clasificador = ClasificadorTramite::find($tramite->clasificador_tramite_id);
						$ruta_flujo = RutaFlujo::find($clasificador->ruta_flujo_id);
						$ruta_flujo_id = $ruta_flujo->id;
						//dd($ruta_flujo);
						/* end get ruta flujo*/


						/*proceso*/
						$tablaRelacion=DB::table('tablas_relacion as tr')
							->join(
								'rutas as r',
								'tr.id','=','r.tabla_relacion_id'
							)
							->where('tr.id_union', '=', $pretramite->titulo)
							->where('r.ruta_flujo_id', '=', $ruta_flujo_id)
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

							$tablaRelacion['id_union']=$pretramite->titulo;
							
							$tablaRelacion['fecha_tramite']= $tramite->fecha_tramite; //Input::get('fecha_tramite');
							$tablaRelacion['tipo_persona']=$tramite->tipo_solicitante_id;
							$tablaRelacion['tramite_id'] = $tramite->id;

							/* if( Input::has('paterno') AND Input::has('materno') AND Input::has('nombre') ){*/
							if($data['txt_personaid']){
								/*$tablaRelacion['paterno']=Input::get('paterno');
								$tablaRelacion['materno']=Input::get('materno');
								$tablaRelacion['nombre']=Input::get('nombre');*/
								$persona = Persona::find($data['txt_personaid']);
								$tablaRelacion['paterno']=$persona['paterno'];
								$tablaRelacion['materno']=$persona['materno'];
								$tablaRelacion['nombre']=$persona['nombre'];
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
							$tablaRelacion['sumilla']=$tramite->observacion;

							$tablaRelacion['persona_autoriza_id']=Input::get('id_autoriza');
							$tablaRelacion['persona_responsable_id']=Input::get('id_responsable');

							$tablaRelacion['usuario_created_at']=Auth::user()->id;
							$tablaRelacion->save();

							$rutaFlujo=RutaFlujo::find($ruta_flujo_id);

							$ruta= new Ruta;
							$ruta['tabla_relacion_id']=$tablaRelacion->id;
							$ruta['fecha_inicio']= $tramite->fecha_tramite;
							$ruta['ruta_flujo_id']=$rutaFlujo->id;
							$ruta['flujo_id']=$rutaFlujo->flujo_id;
							$ruta['persona_id']=$rutaFlujo->persona_id;
							$ruta['area_id']=$rutaFlujo->area_id;
							$ruta['local_id'] = $tramite->local_id;
							$ruta['local_origen_id'] = $tramite->local_origen_id;
							$ruta['usuario_created_at']= Auth::user()->id;
							$ruta->save();

							/*TODO:**************Registro de Campos de Rutas**********************/
							if( isset( $data['ruta_campo_id'] ) ){
								$recorrido = count($data['ruta_campo_id']);
									
								for ($i=0; $i < $recorrido; $i++) { 
									$RutaCampo = array();
									if( $data['ruta_campo_id'][$i] == 0 ){
										$RutaCampo = new RutaCampo;
										$RutaCampo->usuario_created_at = Auth::user()->id;
										$RutaCampo->ruta_id = $ruta->id;
									}
									else{
										$RutaCampo = RutaCampo::find( $data['ruta_campo_id'][$i] );
										$RutaCampo->usuario_created_at = Auth::user()->id;
									}
									
									$RutaCampo->ruta_flujo_campo_id = $data['ruta_flujo_campo_id'][$i];
									$RutaCampo->campo_valor = $data['campo_valor'][$i];
									$RutaCampo->estado = 1;
									$RutaCampo->save();
								}
							}
							
							/*********************************************************************/
							/**************CARTA *************************************************/
							$carta=array();
							if( Input::has('carta_id') ){
								$carta= Carta::find(Input::get('carta_id'));
							}
							else{
								$carta= new Carta;
								$carta['flujo_id']=$ruta->flujo_id;
								$carta['correlativo']=0;
								$carta['nro_carta']=$codigo;
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
							//$carta->save();


							/*********************************************************************/
							/************Agregado de referidos*************/
							$referido=new Referido;
							$referido['ruta_id']=$ruta->id;
							$referido['tabla_relacion_id']=$tablaRelacion->id;
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
								$activarsegundo = 0;
							foreach($qrutaDetalle as $rd){
								$areaG = Area::find($rd->area_id);

								$rutaDetalle = new RutaDetalle;
								$rutaDetalle['ruta_id']=$ruta->id;
								$rutaDetalle['area_id']=$rd->area_id;
								$rutaDetalle['tiempo_id']=$rd->tiempo_id;
								$rutaDetalle['dtiempo']=$rd->dtiempo;
								$rutaDetalle['norden']=$rd->norden;
								$rutaDetalle['estado_ruta']=$rd->estado_ruta;
								/*if($rd->norden==1 or ($rd->norden>1 and $validaactivar==0 and $rd->estado_ruta==2) ){
									$rutaDetalle['fecha_inicio']=Input::get('fecha_inicio');
								}*/
								/*if($rd->norden==1 or $rd->norden==2 or ($rd->norden>1 and $validaactivar==0 and $rd->estado_ruta==2) ){*/
								if($rd->norden==1 or ($rd->norden==2 AND $activarsegundo==1) or ($rd->norden>1 and $validaactivar==0 and $rd->estado_ruta==2) ){	
									//if($rd->norden==1 && $rd->area_id == 3){ //If solo para mesa de partes la condicional de ($rd->norden==2 AND $activarsegundo==1) fue agregado tb
									if( $rd->norden==1 ){ // && $areaG->mesa_parte == 1 || If solo para mesa de partes la condicional de ($rd->norden==2 AND $activarsegundo==1) fue agregado tb
										$rutaDetalle['dtiempo_final']=date("Y-m-d H:i:s");
										$rutaDetalle['tipo_respuesta_id']=1;
										$rutaDetalle['tipo_respuesta_detalle_id']=1;
										$rutaDetalle['observacion']="";
										$activarsegundo=1;
									}
									elseif($rd->norden==2 && $activarsegundo==1){ 
										$rutaDetalle['ruta_detalle_id_ant']=$ruta_detalle_id_aux;
									}
									$rutaDetalle['updated_at']=date("Y-m-d H:i:s");
									$rutaDetalle['usuario_updated_at']=Auth::user()->id;
									$rutaDetalle['fecha_inicio']=date("Y-m-d H:i:s");
								}
								else{
									$validaactivar=1;
								}
								$rutaDetalle['usuario_created_at']= Auth::user()->id;
								$rutaDetalle->save();
								$ruta_detalle_id_aux =$rutaDetalle->id;
								/**************CARTA DESGLOSE*********************************/
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
										/***********MEDIR LOS TIEMPOS**************************/
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
										//$ff=Carta::CalcularFechaFin($array);
										$fi=$array['fecha'];
										//$array['fecha']=$ff;

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
									//$cartaDesglose['fecha_fin']=$ff;
									$cartaDesglose['hora_inicio']="08:00";
									$cartaDesglose['hora_fin']="17:30";
									//$cartaDesglose['fecha_alerta']=$ff;
								}
									$cartaDesglose['ruta_detalle_id']=$rutaDetalle->id;
									//$cartaDesglose->save();


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
									/*foreach ($qrutaDetalleVerbo as $rdv) {
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
									}*/
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

													//if($rd->norden==1 && $rd->area_id == 3){ // If solo por el tema de mesa de partes
													if( $rd->norden==1 ){ // && $areaG->mesa_parte == 1 || If solo por el tema de mesa de partes
														if( $rdv->verbo_id == 1 ){
															$documentoG = Documento::where('area_id', $areaG->id)->first();
															$DocDigitalAuto = $this->DocDigitalAuto( $pretramite->ruta_archivo, $areaG->id, $rdv->documento_id );
															$rutaDetalleVerbo['documento']= $DocDigitalAuto->titulo;
															$rutaDetalleVerbo['doc_digital_id']= $DocDigitalAuto->id;

															$referido=new Referido;
															$referido['ruta_id']= $ruta->id;
															$referido['ruta_detalle_id']= $rutaDetalle->id;
															$referido['norden']= $rdv->orden;
															$referido['tabla_relacion_id']= $tablaRelacion->id;
															$referido['doc_digital_id']= $DocDigitalAuto->id;
															$referido['documento_id']= $rdv->documento_id;
															$referido['estado_ruta']= 1;
															$referido['tipo']= 1;
															$referido['ruta_detalle_verbo_id']= $rutaDetalleVerbo->id;
															$referido['referido']= $DocDigitalAuto->titulo;
															$referido['fecha_hora_referido']= $rutaDetalleVerbo->created_at;
															$referido['usuario_referido']= $rutaDetalleVerbo->usuario_created_at;
															$referido['usuario_created_at']= $rutaDetalleVerbo->usuario_created_at;
															$referido->save();
														}
														$rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
														$rutaDetalleVerbo['updated_at']= date("Y-m-d H:i:s");
														$rutaDetalleVerbo['finalizo']=1;
													}

													$rutaDetalleVerbo->save();
												}
								}
							}
							DB::commit();
							return Response::json(
								array(
								'rst'=>1,
								'msj'=>'Registro realizado correctamente',
								)
							);
						}
						/*end proceso*/
						


					
						/*end start to create process*/
					
						
					} //end if registry was succesfully

				}
				else{
					DB::commit();
					return Response::json(
						array(
						'rst'=>1,
						'msj'=>'Registro realizado correctamente',
						)
					);
				}
			}
			else{
				DB::rollback();
				return  array(
						'rst'=>2,
						'msj'=>'El trámite ya fue registrado anteriormente'
					);
			}
			
		} //end if img y data
	}

	protected function DocDigitalAuto($url, $area_id, $tipo_documento_id){
		
			//$tipo_documento_id = 86;
			//$area_id = 3;
			$año= date("Y");
			$r2=array(array('correlativo'=>'1'));
			/*$sql = "SELECT LPAD(id+1,6,'0') as correlativo,'$año' ano FROM doc_digital ORDER BY id DESC LIMIT 1";*/
			$sql = "SELECT IFNULL(MAX(dd.correlativo)+1,1) as correlativo
					FROM doc_digital_temporal dd 
					INNER JOIN plantilla_doc pd on dd.plantilla_doc_id=pd.id 
					AND pd.tipo_documento_id=".$tipo_documento_id." 
					AND pd.area_id= ".$area_id.
					" WHERE dd.estado=1 
					AND YEAR(dd.created_at)=YEAR(CURDATE())";
			$r= DB::select($sql);
			$titulo = (isset($r[0])) ? $r[0]->correlativo : $r2[0]->correlativo; 

            //DB::beginTransaction();
            $plantilla = DB::table('plantilla_doc')
                        ->where('tipo_documento_id', $tipo_documento_id)
                        ->where('area_id', $area_id)
                        ->first();
			
			if( !isset($plantilla->id) ){
				$plantilla = new PlantillaDocumento;
				$plantilla->descripcion = '';
				$plantilla->tipo_documento_id = $tipo_documento_id;
				$plantilla->area_id = $area_id;
				$plantilla->cuerpo = '.';
				$plantilla->estado = 1;
				$plantilla->usuario_created_at = Auth::user()->id;
				$plantilla->save();
			}

            $area = DB::table('areas')
                    ->select('nemonico')
                    ->where('id',$area_id)
                    ->first();

            $documento = DB::table('documentos')
                    ->select('nombre', 'nemonico')
                    ->where('id',$tipo_documento_id)
                    ->first();

            $buscar = array('@@@@@@','@@@@@','@@@@','@@@','@@','@','####','##');
            $reemplazar = array( str_pad( $titulo, 6, "0", STR_PAD_LEFT ), str_pad( $titulo, 5, "0", STR_PAD_LEFT ),
                str_pad( $titulo, 4, "0", STR_PAD_LEFT ), str_pad( $titulo, 3, "0", STR_PAD_LEFT ),
                str_pad( $titulo, 2, "0", STR_PAD_LEFT ), str_pad( $titulo, 1, "0", STR_PAD_LEFT ),
                date("Y"), date("y")
            );
            $titulofinal = str_replace( $buscar, $reemplazar, $documento->nemonico );
            //$titulofinal = $documento->nemonico.' N° '.$titulo.' - '.$area->nemonico." - ".date("Y");

            $DocDigital = new DocumentoDigital;
            $DocDigital->titulo = $titulofinal;
            $DocDigital->asunto = '';
            $DocDigital->correlativo = $titulo*1;
            $DocDigital->doc_privado = 0;
            $DocDigital->cuerpo = '.';
            $DocDigital->plantilla_doc_id = $plantilla->id;
            $DocDigital->area_id = $area_id;
            $DocDigital->envio_total = 0;
            $DocDigital->tipo_envio = 1;
			$DocDigital->doc_archivo= $url;
            $DocDigital->persona_id = Auth::user()->id; 
            $DocDigital->usuario_created_at = Auth::user()->id;
            
            $cantidad=true;
            $conteo=0;
            $conteoMax=10;
            $correlativoinicial=$titulo;
            $correlativoaux=$correlativoinicial;
            while ( $cantidad==true ) {
                $cantidad=false;
                try {
                    $DocDigital->save();
                } catch (Exception $e) {
                    $d=explode("duplicate",strtolower($e));
                    if(count($d)>1){
                        $cantidad=true;
                        $DocDigital->correlativo++;
                        $correlativoaux=$DocDigital->correlativo;
                        $reemplazar = array( str_pad( $correlativoaux, 6, "0", STR_PAD_LEFT ), str_pad( $correlativoaux, 5, "0", STR_PAD_LEFT ),
                            str_pad( $correlativoaux, 4, "0", STR_PAD_LEFT ), str_pad( $correlativoaux, 3, "0", STR_PAD_LEFT ),
                            str_pad( $correlativoaux, 2, "0", STR_PAD_LEFT ), str_pad( $correlativoaux, 1, "0", STR_PAD_LEFT ),
                            date("Y"), date("y")
                        );
                        $DocDigital->titulo = str_replace( $buscar, $reemplazar, $documento->nemonico );
                        //$DocDigital->titulo=str_replace($correlativoinicial,$correlativoaux,$DocDigital->titulo);
                        $DocDigital->correlativo = $correlativoaux*1;
                        $correlativoinicial = $correlativoaux;
                    }
                    else{
                        $conteo=$conteoMax+1;
                    }
                }
                $conteo++;
                if($conteo==$conteoMax){
                    $cantidad=false;
                }
            }

            if($conteo==$conteoMax){
                DB::rollback();
                return Response::json(array('rst'=>3, 'msj'=>'Registro Inválido revise sus datos seleccionados','correlativo'=>$correlativoaux."|".$correlativoinicial));
            }
            elseif($conteo==$conteoMax+1){
                DB::rollback();
                return Response::json(array('rst'=>3, 'msj'=>'Registro Inválido o Existe un problema con el servidor, revise sus datos seleccionados','correlativo'=>$correlativoaux."|".$correlativoinicial));
            }

            if($DocDigital->id){
                $created=Input::get('fecha').' '.date ("h:i:s");     
                $DocHistorial = new DocumentoFechaH;
                $DocHistorial->documento_id = $DocDigital->id;
                $DocHistorial->fecha_documento = $DocDigital->created_at;
                $DocHistorial->comentario ='Inicio';
                $DocHistorial->usuario_created_at = Auth::user()->id;
                $DocHistorial->save();

                $sql= DB::table("doc_digital_temporal")
                        ->insert([
                            'id' => $DocDigital->id,
                            'plantilla_doc_id' => $plantilla->id,
                            'titulo' => $DocDigital->titulo,
                            'asunto' => '',
                            'correlativo' => $DocDigital->correlativo,
                            'doc_privado' => 0,
                            'area_id' => $area_id,
                            'envio_total' => 0,
                            'tipo_envio' => 1,
							'doc_archivo' => $url,
                            'usuario_created_at' => Auth::user()->id,
                            'persona_id' => Auth::user()->id,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
            }
            
            //DB::commit();
			return $DocDigital;
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /tramite
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /tramite/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /tramite/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /tramite/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /tramite/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
