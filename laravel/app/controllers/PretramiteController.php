<?php

class PretramiteController extends BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /pretramite
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	public function postMisdatos(){
		$persona = Persona::find(Auth::user()->id);
          return Response::json(
              array(
                  'paterno'=>$persona->paterno,
                  'materno'=>$persona->materno,
                  'nombre'=>$persona->nombre,
                  'celular'=>$persona->celular,
                  'telefono'=>$persona->telefono,
                  'direccion'=>$persona->direccion,
                  'dni'=>$persona->dni,
                  'email'=>$persona->email,
              )
          );
	}

	public function postListartramites(){
		$rst=Pretramite::getTramites();
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
	}

    public function postListarpretramites(){
        $rst=Pretramite::getPreTramites();
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
    }

	public function postListar(){ //listar clasificacion tramite area
		
		$rst=Pretramite::getAreasbyClaTramite();
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
	}

	public function postGetbyid(){
		$rst=Pretramite::getPreTramiteById();
        return Response::json(
            array(
                'rst'=>1,
                'datos'=>$rst
            )
        );
	}

	public function postEmpresasbypersona(){
		$rst=Pretramite::getEmpresasUser();
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
	}

	public function postClasificadorestramite(){
		$rst=Pretramite::getClasificadoresTramite();
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
	}

	public function postRequisitosbyctramite(){
		$rst=Pretramite::getrequisitosbyClaTramite();
          return Response::json(
              array(
                  'rst'=>1,
                  'datos'=>$rst
              )
          );
	}
	/**
	 * Show the form for creating a new resource.
	 * GET /pretramite/create
	 *
	 * @return Response
	 */
	public function postCreatepretramite(){
		if ( Request::ajax() ) {
			$array_data = json_decode(Input::get('info'));
			$persona_id = Auth::user()->id;
			$clasificadorTramite = ClasificadorTramite::find($array_data->idclasitramite);
			
			$valida = 	Pretramite::where('persona_id', $persona_id)
						->where('clasificador_tramite_id', $array_data->idclasitramite)
						->where('estado_atencion', 0)
						->first();

			if( isset($valida->id) AND $clasificadorTramite->valida_pendiente == 1 ){
				return Response::json(
					array(
					'rst'=>2,
					'msj'=>'El servicio ya fue registrado, esta pendiente de ser atendido.',
					)
				);
			}
			
			$pretramite = new Pretramite;

	        $pretramite['clasificador_tramite_id'] = $array_data->idclasitramite;

	        if($array_data->idempresa){
	        	$pretramite['empresa_id'] = $array_data->idempresa;        	
	        }

	        $pretramite['persona_id'] =  Auth::user()->id;
	        $pretramite['tipo_solicitante_id'] = $array_data->cbo_tiposolicitante;
	        $pretramite['tipo_documento_id'] = $array_data->cbo_tipodoc;
	        $pretramite['tipo_tramite_id'] = $array_data->cbo_tipotramite;
	        $pretramite['documento'] = urldecode($array_data->tipodoc);
	        $pretramite['nro_folios'] = $array_data->numfolio;
	        $pretramite['area_id'] = $array_data->idarea;
	        $pretramite['local_id'] = $array_data->local;
	        $pretramite['local_origen_id'] = $array_data->local;
	        $pretramite['fecha_pretramite'] = date('Y-m-d H:i:s');
			$pretramite['usuario_created_at'] = Auth::user()->id;
			$pretramite['documento_id'] = $clasificadorTramite->documento_id; // Se asigna el documento interno a generar.
			$pretramite->save();
			
			if( trim($array_data->pdf_archivo)!='' ){
                $urld=explode(".", urldecode($array_data->pdf_nombre));
                $url = "upload/pretramite/pt-".$pretramite->id.".".end($urld);
				$pretramite->ruta_archivo = $url;
				$pretramite->save();
                
                Pretramite::FileToFile(urldecode($array_data->pdf_archivo), $url);
            }

			
			$persona = Persona::find(Auth::user()->id);
			$persona->telefono = $array_data->usertelf;
			$persona->celular = $array_data->usercel;
			$persona->email = urldecode($array_data->useremail);
			$persona->direccion = urldecode($array_data->userdirec);
			$persona->save();

	        return Response::json(
	            array(
	            'rst'=>1,
				'msj'=>'Registro realizado correctamente',
	            )
	        );
	 	}
	}

	public function postCreateservicio()
	{
		//$array_data = json_decode(Input::get('info'));
		$array_data = Input::all();
		$clasificadorTramite = ClasificadorTramite::find($array_data['idclasitramite']);
		$tipoTramite = TipoTramite::find($clasificadorTramite->tipo_tramite_id);

		if( $tipoTramite->solicitante != 'Interno' ){
			if( $tipoTramite->cant_solicitante == 1 AND count( $array_data['persona_id_sol'] ) > $tipoTramite->cant_solicitante ){
				return Response::json(
					array(
					'rst'=>2,
					'msj'=>'El servicio seleccionado no puede contener más de 1 solicitante.',
					)
				);
			}
		}
		else{

		}

		DB::beginTransaction();
		
		$pretramite = new Pretramite;
		$codigo = Pretramite::Correlativo($clasificadorTramite->documento_id);
		$documento = DB::table('documentos')
					->select('nombre', 'nemonico')
					->where('id',$clasificadorTramite->documento_id)
					->first();
		
		$titulo = $codigo->correlativo;
		$buscar = array('@@@@@@','@@@@@','@@@@','@@@','@@','@','####','##');
		$reemplazar = array( str_pad( $titulo, 6, "0", STR_PAD_LEFT ), str_pad( $titulo, 5, "0", STR_PAD_LEFT ),
			str_pad( $titulo, 4, "0", STR_PAD_LEFT ), str_pad( $titulo, 3, "0", STR_PAD_LEFT ),
			str_pad( $titulo, 2, "0", STR_PAD_LEFT ), str_pad( $titulo, 1, "0", STR_PAD_LEFT ),
			date("Y"), date("y")
		);
		$titulofinal = str_replace( $buscar, $reemplazar, $documento->nemonico );
        
        $pretramite['clasificador_tramite_id'] = $array_data['idclasitramite'];

        if($tipoTramite->solicitante != 'Interno' ){
			if( $array_data['empresa_id_sol'][0] != 0 ){
				$pretramite['empresa_id'] = $array_data['empresa_id_sol'][0];
				$pretramite['persona_id'] = $array_data['persona_id_sol'][0];
			}
			else{
				$pretramite['persona_id'] =  $array_data['persona_id_sol'][0];
			}
        }
		else{
			$pretramite['area_id_sol'] =  $array_data['areas'];
		}

		$pretramite['titulo'] = $titulofinal;
        $pretramite['correlativo'] = $codigo->correlativo;
		$pretramite->año = date("Y");

        $pretramite['tipo_solicitante_id'] = $array_data['cbo_tiposolicitante'];
        $pretramite['tipo_documento_id'] = $array_data['cbo_tipodoc'];
        //$pretramite['tipo_tramite_id'] = $array_data['cbo_tipodocumento'];
        $pretramite['documento'] = $array_data['tipodoc'];
        $pretramite['nro_folios'] = $array_data['numfolio'];
        $pretramite['area_id'] = $array_data['idarea'];
        $pretramite['local_id'] = $array_data['local'];
        $pretramite['local_origen_id'] = $array_data['local'];
        $pretramite['estado_atencion'] = 1;
        $pretramite['fecha_pretramite'] = date('Y-m-d H:i:s');
        $pretramite['usuario_created_at'] = Auth::user()->id;
		$pretramite['documento_id'] = $clasificadorTramite->documento_id;

		if( isset($array_data['archivo_ins']) AND trim($array_data['archivo_ins']) != '' ){
			$pretramite['ruta_archivo'] = $array_data['url'].$array_data['archivo_ins'];
		}
		elseif( isset($array_data['archivo_mat']) AND trim($array_data['archivo_mat']) != '' ){
			$pretramite['ruta_archivo'] = $array_data['url'].$array_data['archivo_mat'];
		}
		elseif( isset($array_data['archivo_pro']) AND trim($array_data['archivo_pro']) != '' ){
			$pretramite['ruta_archivo'] = $array_data['url'].$array_data['archivo_pro'];
		}

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
					'msj'=>'Problemas al generar el correlativo, comuniquese con el área de TI'
				);
		}

        /*tramite*/
        if($pretramite->id){ // if registry was succesfully
            $tramite = new Tramite;
            $tramite['pretramite_id'] = $pretramite->id;

	        if( $tipoTramite->solicitante != 'Interno' ){
				if( trim($pretramite->empresa_id) != '' ){
					$tramite['empresa_id'] = $pretramite->empresa_id;  
					$tramite['persona_id'] = $pretramite->persona_id; 
				}
				else{
					$tramite['persona_id'] = $pretramite->persona_id;
				}
	        }
			else{
				$tramite['area_id_sol'] = $pretramite->area_id_sol;
			}
            
            $tramite['area_id'] = $array_data['idarea'];
            $tramite['local_id'] = $pretramite->local_id;
            $tramite['local_origen_id'] = $pretramite->local_origen_id;
	        $tramite['clasificador_tramite_id'] = $pretramite->clasificador_tramite_id;
	        $tramite['tipo_solicitante_id'] = $pretramite->tipo_solicitante_id;
	        $tramite['tipo_documento_id'] = $pretramite->tipo_documento_id;
	        $tramite['documento'] = $pretramite->documento;
	        $tramite['nro_folios'] = $pretramite->nro_folios;
	        $tramite['observacion'] = urldecode(trim($array_data['observacion']));
	        $tramite['imagen'] = '';
			$tramite['seguimiento'] = $tipoTramite->seguimiento;
	        $tramite['fecha_tramite'] = date('Y-m-d H:i:s');
	        $tramite['usuario_created_at'] = Auth::user()->id;
	        $tramite->save();


			if( $tipoTramite->solicitante != 'Interno' ){
				for( $i = 0; $i < count($array_data['persona_id_sol']); $i++ ){
					$persona = Persona::find($array_data['persona_id_sol'][$i]);
					$persona->telefono = $array_data['telefono_sol'][$i];
					$persona->celular = $array_data['celular_sol'][$i];
					$persona->email = urldecode($array_data['email_sol'][$i]);
					$persona->direccion = urldecode($array_data['direccion_sol'][$i]);
					$persona->save();
	
					$anexo = new Anexo;
					$anexo['tramite_id'] = $tramite->id;
					$anexo['persona_id'] = $array_data['persona_id_sol'][$i];
					if( $array_data['empresa_id_sol'][$i] != 0 ){
						$anexo['empresa_id'] = $array_data['empresa_id_sol'][$i];
					}
					$anexo['fecha_anexo'] = $tramite->fecha_tramite;
					$anexo['documento_id'] = $tramite->tipo_documento_id;
					$anexo['nombre'] = 'A';
					$anexo['nro_folios'] = $tramite->nro_folios;
					$anexo['obeservacion'] = urldecode(trim($array_data['observacion']));
					$anexo['usuario_created_at'] = Auth::user()->id;
					$anexo->save();	
				}
			}
            
			$codigo= $clasificadorTramite->unidad_documentaria.'-'.$codigo->correlativo.'-'.date('Y');
			
            $clasificador = ClasificadorTramite::find($array_data['idclasitramite']);
            $ruta_flujo = RutaFlujo::find($clasificador->ruta_flujo_id);
            $ruta_flujo_id = $ruta_flujo->id;
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
                $tablaRelacion['tramite_id']=$tramite->id;
		        $tablaRelacion['id_union']=$pretramite->titulo;
		        
		        $tablaRelacion['fecha_tramite']= $tramite->fecha_tramite; //Input::get('fecha_tramite');
		        $tablaRelacion['tipo_persona']=$tramite->tipo_solicitante_id;

		       	if( trim($tramite->persona_id) != '' ){
		            $persona = Persona::find($tramite->persona_id);
		        	$tablaRelacion['paterno']=$persona['paterno'];
		            $tablaRelacion['materno']=$persona['materno'];
		            $tablaRelacion['nombre']=$persona['nombre'];
		        }
		        if( trim($tramite->empresa_id) != ''){
					$empresa = Empresa::find( $tramite->empresa_id );
		            $tablaRelacion['razon_social']= $empresa->razon_social;
		            $tablaRelacion['ruc']=$empresa->ruc;
		        }
		        elseif( trim($tramite->area_id_sol) != '' ){
		            $tablaRelacion['area_id']= $tramite->area_id_sol;
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

		        $tablaRelacion['persona_autoriza_id']='';
		        $tablaRelacion['persona_responsable_id']='';

		        $tablaRelacion['usuario_created_at']=Auth::user()->id;
		        $tablaRelacion->save();

		        $rutaFlujo=$ruta_flujo;//RutaFlujo::find($ruta_flujo_id);

		        $ruta= new Ruta;
		        $ruta['tabla_relacion_id']=$tablaRelacion->id;
		        $ruta['fecha_inicio']= $tramite->fecha_tramite;
		        $ruta['ruta_flujo_id']=$rutaFlujo->id;
		        $ruta['flujo_id']=$rutaFlujo->flujo_id;
		        $ruta['persona_id']=$rutaFlujo->persona_id;
		        $ruta['area_id']=$rutaFlujo->area_id;
		        $ruta['local_id']=$tramite->local_id;
		        $ruta['local_origen_id']=$tramite->local_origen_id;
		        $ruta['usuario_created_at']= Auth::user()->id;
		        $ruta->save();
		        
		        /************Agregado de referidos*************/
				$referido=new Referido;
				$referido['ruta_id']=$ruta->id;
				$referido['tabla_relacion_id']=$tablaRelacion->id;
				$referido['ruta_detalle_verbo_id']=0;
				$referido['tipo']=0;
				$referido['referido']=$tablaRelacion->id_union;
				$referido['fecha_hora_referido']=$tablaRelacion->created_at;
				$referido['usuario_referido']=$tablaRelacion->usuario_created_at;
				$referido['usuario_created_at']=Auth::user()->id;
				$referido->save();

				if( isset($array_data['ruta_id_ref'][0]) ){
					for( $i = 0; $i < count($array_data['ruta_id_ref']); $i++ ){
						$referidoRelacion=new ReferidoRelacion;
						$referidoRelacion['ruta_id']=$ruta->id;
						$referidoRelacion['ruta_id_ref']=$array_data['ruta_id_ref'][$i];
						$referidoRelacion['estado']=1;
						$referidoRelacion['usuario_created_at'] = Auth::user()->id;
						$referidoRelacion->save();
					}
				}
				
		        /**********************************************/

		        $qrutaDetalle=DB::table('rutas_flujo_detalle')
        		            ->where('ruta_flujo_id', '=', $rutaFlujo->id)
        		            ->where('estado', '=', '1')
        		            ->orderBy('norden','ASC')
        		            ->get();
                $validaactivar=0;
				
    			$conteo=0;$array['fecha']=''; // inicializando valores para desglose
	            foreach($qrutaDetalle as $rd){
	                $rutaDetalle = new RutaDetalle;
	                $rutaDetalle['ruta_id']=$ruta->id;
	                $rutaDetalle['area_id']=$rd->area_id;
	                $rutaDetalle['tiempo_id']=$rd->tiempo_id;
	                $rutaDetalle['dtiempo']=$rd->dtiempo;
	                $rutaDetalle['norden']=$rd->norden;
	                $rutaDetalle['estado_ruta']=$rd->estado_ruta;
	                
                    if($rd->norden==1 or ($rd->norden>1 and $validaactivar==0 and $rd->estado_ruta==2) ){
                        $rutaDetalle['fecha_inicio']=date("Y-m-d H:i:s");
                    }
	                else{
	                    $validaactivar=1;
	                }
	                $rutaDetalle['usuario_created_at']= Auth::user()->id;
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
                            $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
                            $rutaDetalleVerbo->save();
                        }
                    }
                }

				if( isset($array_data['apiproceso']) ){ //Solo si viene por API
					$rutaDetalleVerbo = new RutaDetalleVerbo;
					$rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
					$rutaDetalleVerbo['nombre']= "Generar";
					$rutaDetalleVerbo['condicion']= 0;
					$rutaDetalleVerbo['rol_id']= 3;
					$rutaDetalleVerbo['verbo_id']= 1;
					$rutaDetalleVerbo['documento_id']= 0;
					$rutaDetalleVerbo['orden']= 0;
					$rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
					$rutaDetalleVerbo->save();

					$documento = DB::table('documentos')
								->select('nombre', 'nemonico')
								->where('id',$array_data['tipo_documento_id'])
								->first();

					if( trim($pretramite->ruta_archivo) != '' ){
						$correlativo = 1;
						$sql = "SELECT IFNULL(MAX(dd.correlativo)+1,1) as correlativo
								FROM doc_digital dd 
								INNER JOIN plantilla_doc pd on dd.plantilla_doc_id=pd.id 
								AND pd.tipo_documento_id=".$array_data['tipo_documento_id']." 
								AND pd.area_id= ".$pretramite->area_id."
								WHERE dd.estado=1 
								AND YEAR(dd.created_at)=YEAR(CURDATE())";
						$r= DB::select($sql);
						$correlativo = (isset($r[0])) ? $r[0]->correlativo : $correlativo;

						$buscar = array('@@@@@@','@@@@@','@@@@','@@@','@@','@','####','##');
						$reemplazar = array( str_pad( $correlativo, 6, "0", STR_PAD_LEFT ), str_pad( $correlativo, 5, "0", STR_PAD_LEFT ),
							str_pad( $correlativo, 4, "0", STR_PAD_LEFT ), str_pad( $correlativo, 3, "0", STR_PAD_LEFT ),
							str_pad( $correlativo, 2, "0", STR_PAD_LEFT ), str_pad( $correlativo, 1, "0", STR_PAD_LEFT ),
							date("Y"), date("y")
						);
						$titulofinal = str_replace( $buscar, $reemplazar, $documento->nemonico );

						$DocDigitalAuto = $this->DocDigitalAuto( $pretramite->ruta_archivo, $pretramite->area_id, $array_data['tipo_documento_id'], $titulofinal, $correlativo, 'url' );
						$rutaDetalleVerbo['documento']= $DocDigitalAuto->titulo;
						$rutaDetalleVerbo['doc_digital_id']= $DocDigitalAuto->id;
					}
					else{
						$rutaDetalleVerbo['documento']= $pretramite->titulo;
					}
					$rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
					$rutaDetalleVerbo['finalizo']=1;
					$rutaDetalleVerbo->save();

					$referido=new Referido;
					$referido['ruta_id']= $ruta->id;
					$referido['ruta_detalle_id']= $rutaDetalle->id;
					$referido['norden']= 0;
					$referido['tabla_relacion_id']= $tablaRelacion->id;
					$referido['referido']= $rutaDetalleVerbo->documento;
					if( isset($DocDigitalAuto->id) ){
						$referido['doc_digital_id']= $DocDigitalAuto->id;
					}
					$referido['documento_id']= 0;
					$referido['estado_ruta']= 1;
					$referido['tipo']= 1;
					$referido['ruta_detalle_verbo_id']= $rutaDetalleVerbo->id;
					
					$referido['fecha_hora_referido']= $rutaDetalleVerbo->created_at;
					$referido['usuario_referido']= $rutaDetalleVerbo->usuario_created_at;
					$referido['usuario_created_at']= $rutaDetalleVerbo->usuario_created_at;
					$referido->save();

					$archivos = $array_data['archivo_ins'].",".$array_data['archivo_mat'].",".$array_data['archivo_pro'].",".$array_data['archivo_cur'];
					$darchivos = explode(",", $archivos);
					for( $i = 0; $i < count($darchivos); $i++ ){
						if( trim($darchivos[$i]) != '' ){
							$rutaDetalleVerbo = new RutaDetalleVerbo;
							$rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
							$rutaDetalleVerbo['nombre']= "Generar";
							$rutaDetalleVerbo['condicion']= 0;
							$rutaDetalleVerbo['rol_id']= 3;
							$rutaDetalleVerbo['verbo_id']= 1;
							$rutaDetalleVerbo['documento_id']= 0;
							$rutaDetalleVerbo['orden']= '0.'.($i+1);
							$rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
							$rutaDetalleVerbo->save();
							
							$año= date("Y");
							$correlativo = 1;
							/*$sql = "SELECT LPAD(id+1,6,'0') as correlativo,'$año' ano FROM doc_digital ORDER BY id DESC LIMIT 1";*/
							$sql = "SELECT IFNULL(MAX(dd.correlativo)+1,1) as correlativo
									FROM doc_digital dd 
									INNER JOIN plantilla_doc pd on dd.plantilla_doc_id=pd.id 
									AND pd.tipo_documento_id=".$array_data['tipo_documento_id']." 
									AND pd.area_id= ".$pretramite->area_id."
									WHERE dd.estado=1 
									AND YEAR(dd.created_at)=YEAR(CURDATE())";
							$r= DB::select($sql);
							$correlativo = (isset($r[0])) ? $r[0]->correlativo : $correlativo;

							$buscar = array('@@@@@@','@@@@@','@@@@','@@@','@@','@','####','##');
							$reemplazar = array( str_pad( $correlativo, 6, "0", STR_PAD_LEFT ), str_pad( $correlativo, 5, "0", STR_PAD_LEFT ),
								str_pad( $correlativo, 4, "0", STR_PAD_LEFT ), str_pad( $correlativo, 3, "0", STR_PAD_LEFT ),
								str_pad( $correlativo, 2, "0", STR_PAD_LEFT ), str_pad( $correlativo, 1, "0", STR_PAD_LEFT ),
								date("Y"), date("y")
							);
							$titulofinal = str_replace( $buscar, $reemplazar, $documento->nemonico );

							$DocDigitalAuto = $this->DocDigitalAuto( $array_data['url'].$darchivos[$i], $pretramite->area_id, $array_data['tipo_documento_id'], $titulofinal, $correlativo, 'url' );
							$rutaDetalleVerbo['documento']= $DocDigitalAuto->titulo;
							$rutaDetalleVerbo['doc_digital_id']= $DocDigitalAuto->id;
							$rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
							$rutaDetalleVerbo['finalizo']=1;
							$rutaDetalleVerbo->save();

							$sustento=new Sustento;
							$sustento['referido_id']=$referido->id;
							$sustento['ruta_detalle_id']=$rutaDetalle->id;
							$sustento['ruta_detalle_verbo_id']=$rutaDetalleVerbo->id;
							$sustento['documento_id']=$rutaDetalleVerbo->documento_id;
							$sustento['sustento']=$rutaDetalleVerbo->documento;
							$sustento['doc_digital_id']=$rutaDetalleVerbo->doc_digital_id; // JHOUBERT
							$sustento['fecha_hora_sustento']=$rutaDetalleVerbo->updated_at;
							$sustento['usuario_sustento']=$rutaDetalleVerbo->usuario_updated_at;
							$sustento['usuario_created_at']=Auth::user()->id;
							$sustento->save();
						}
					}
				}
			}
		} //end if registry was succesfully
		DB::commit();
		if( isset($array_data['apiproceso']) ){
			foreach($array_data['campos'] as $index => $val ){
				$rutaCampo = new RutaCampo;
				$rutaCampo->ruta_id = $ruta->id;
				$rutaCampo->ruta_flujo_campo_id = $index;
				$rutaCampo->campo_valor = $val;
				$rutaCampo->estado = 1;
				$rutaCampo->usuario_created_at = Auth::user()->id;
				$rutaCampo->save();
			}
			return $pretramite->titulo;
		}
		else{
			return Response::json(
				array(
				'rst'=>1,
				'msj'=>'Registro realizado correctamente',
				)
			);
		}
	}

	public function postCreateservicioareadig()
	{ 
		$array_data = Input::all();

		$locales = array();
		for( $i=0; $i < $array_data['numareas']; $i++ ){
			$locales = array_unique(array_merge($locales, $array_data['local_destino'][$i])); //Identificando locales únicos
		}
		sort($locales);
		$titulofinal_aux = ''; $titulofinal = ''; $codigo = array(); $fechahoy = date('Y-m-d H:i:s'); $DocDigitalAuto = array(); $url = ''; $archivo_acumulado = '';
		
		for( $l = 0; $l < count($locales); $l++ ){
			DB::beginTransaction();
			if( $l > 0 ){
				if( $l == 1 ){
					$titulofinal_aux = $pretramite->titulo;
					$pretramite->titulo = $titulofinal_aux.'-001';
					$pretramite->save();
					$tablaRelacion->id_union = $titulofinal_aux.'-001';
					$tablaRelacion->save();
					DB::table('rutas_detalle as rd')
					->join('rutas_detalle_verbo as rdv', 'rdv.ruta_detalle_id','=','rd.id')
					->where('rd.ruta_id', $ruta->id)
					->where('rdv.verbo_id', 1)
					->where('rdv.doc_digital_id', $DocDigitalAuto->id)
					->update(['documento' => $titulofinal_aux.'-001']);

					DB::table('referidos')
					->where('ruta_id', $ruta->id)
					->where('doc_digital_id', $DocDigitalAuto->id)
					->update(['referido' => $titulofinal_aux.'-001']);
				}
				$titulofinal = $titulofinal_aux.'-'.str_pad( ($l+1), 3, "0", STR_PAD_LEFT );
			}

			$pretramite = new Pretramite;			
			$pretramite['clasificador_tramite_id'] = 0;

			$pretramite['area_id_sol'] =  $array_data['areas'];
			$pretramite['local_origen_id'] =  $array_data['local_origen_id'];
			$pretramite['titulo'] = $titulofinal;
			$pretramite['correlativo'] = $codigo->correlativo;
			$pretramite->año = date("Y");

			$pretramite['tipo_solicitante_id'] = 0;
			$pretramite['tipo_documento_id'] = $documento_id;
			$pretramite['tipo_tramite_id'] = 0;
			$pretramite['documento'] = '';
			$pretramite['nro_folios'] = 0;
			$pretramite['area_id'] = $array_data['areas'];//Área de inicio
			$pretramite['local_id'] = $locales[$l];
			$pretramite['estado_atencion'] = 1;
			$pretramite['fecha_pretramite'] = $fechahoy;
			$pretramite['usuario_created_at'] = Auth::user()->id;
			$pretramite['documento_id'] = $documento_id;
			$pretramite['observacion'] = urldecode(trim($array_data['observacion']));
			$cantidad=true;
			$conteo=0;
			$conteoMax=10;
			//rutas_flujo, personas, flujos, documentos, clasificador_tramite => todas estas tablas con el valor "0"
			
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
						'titulo' => $titulofinal,
						'correlativo' => $pretramite->correlativo
					);
			}

			if( $l == 0 AND trim($array_data['pdf_archivo_base'])!='' ){
                $urld=explode(".", $array_data['pdf_nombre_base']);
                $url = "upload/pretramite/pt-".$pretramite->id.".".end($urld);
				$pretramite->ruta_archivo = $url;
				$pretramite->save();
                
                Pretramite::FileToFile($array_data['pdf_archivo_base'], $url);
            }
			else{
				$pretramite->ruta_archivo = $url;
				$pretramite->save();
			}
			
			/*tramite*/
			if($pretramite->id){ // if registry was succesfully
				$tramite = new Tramite;
				$tramite['pretramite_id'] = $pretramite->id;
				$tramite['area_id_sol'] = $pretramite->area_id_sol;
				$tramite['local_origen_id'] = $pretramite->local_origen_id;
				
				$tramite['area_id'] = $pretramite->area_id;
				$tramite['local_id'] = $pretramite->local_id;
				$tramite['clasificador_tramite_id'] = $pretramite->clasificador_tramite_id;
				$tramite['tipo_solicitante_id'] = $pretramite->tipo_solicitante_id;
				$tramite['tipo_documento_id'] = $pretramite->tipo_documento_id;
				$tramite['documento'] = $pretramite->documento;
				$tramite['nro_folios'] = $pretramite->nro_folios;
				$tramite['observacion'] = $pretramite->observacion;
				$tramite['imagen'] = '';
				$tramite['seguimiento'] = 0;
				$tramite['fecha_tramite'] = $fechahoy;
				$tramite['usuario_created_at'] = Auth::user()->id;
				$tramite->save();
				
				$ruta_flujo = RutaFlujo::find(0);
				$ruta_flujo_id = $ruta_flujo->id;
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
					$tablaRelacion['tramite_id']=$tramite->id;
					$tablaRelacion['id_union']=$pretramite->titulo;
					
					$tablaRelacion['fecha_tramite']= $tramite->fecha_tramite; //Input::get('fecha_tramite');
					$tablaRelacion['tipo_persona']=$tramite->tipo_solicitante_id;
					$tablaRelacion['area_id']= $tramite->area_id_sol;

					if( Input::has('referente') AND trim(Input::get('referente'))!='' ){
						$tablaRelacion['referente']=Input::get('referente');
					}

					if( Input::has('responsable') AND trim(Input::get('responsable'))!='' ){
						$tablaRelacion['responsable']=Input::get('responsable');
					}
					$tablaRelacion['sumilla']=$tramite->observacion;

					$tablaRelacion['persona_autoriza_id']='';
					$tablaRelacion['persona_responsable_id']='';

					$tablaRelacion['usuario_created_at']=Auth::user()->id;
					$tablaRelacion->save();

					$rutaFlujo=$ruta_flujo;//RutaFlujo::find($ruta_flujo_id);

					$ruta= new Ruta;
					$ruta['tabla_relacion_id']=$tablaRelacion->id;
					$ruta['fecha_inicio']= $tramite->fecha_tramite;
					$ruta['ruta_flujo_id']=$rutaFlujo->id;
					$ruta['flujo_id']=$rutaFlujo->flujo_id;
					$ruta['persona_id']=$rutaFlujo->persona_id;
					$ruta['area_id']=$tramite->area_id_sol;
					$ruta['local_id']=$tramite->local_id;
					$ruta['local_origen_id']=$tramite->local_origen_id;
					$ruta['usuario_created_at']= Auth::user()->id;
					$ruta->save();
					
					/************Agregado de referidos*************/
					$referido=new Referido;
					$referido['ruta_id']=$ruta->id;
					$referido['tabla_relacion_id']=$tablaRelacion->id;
					$referido['ruta_detalle_verbo_id']=0;
					$referido['tipo']=0;
					$referido['referido']=$tablaRelacion->id_union;
					$referido['fecha_hora_referido']=$tablaRelacion->created_at;
					$referido['usuario_referido']=$tablaRelacion->usuario_created_at;
					$referido['usuario_created_at']=Auth::user()->id;
					$referido->save();

					if( isset($array_data['ruta_id_ref'][0]) ){
						for( $i = 0; $i < count($array_data['ruta_id_ref']); $i++ ){
							$referidoRelacion=new ReferidoRelacion;
							$referidoRelacion['ruta_id']=$ruta->id;
							$referidoRelacion['ruta_id_ref']=$array_data['ruta_id_ref'][$i];
							$referidoRelacion['estado']=1;
							$referidoRelacion['usuario_created_at'] = Auth::user()->id;
							$referidoRelacion->save();
						}
					}
					
					/**********************************************/

					$qrutaDetalle=DB::table('rutas_flujo_detalle')
								->where('ruta_flujo_id', '=', $rutaFlujo->id)
								->where('estado', '=', '1')
								->orderBy('norden','ASC')
								->get();
					$validaactivar=0;
					
					$area = array($array_data['areas']);
					$area = array_merge($area,$array_data['area']);					
					$contador = 0; $ruta_detalle_id_aux = '';
					$sql="SELECT CalcularFechaFinal( '".$fechahoy."', (1*1440), ".$pretramite->area_id_sol." ) fproy";
					$fproy= DB::select($sql);
					
					foreach($area as $index => $val){
						if( $index > 0 AND !in_array($locales[$l], $array_data['local_destino'][($index-1)]) ){ //Valida que el proceso solo tenga las áreas que contengan el local recorrido
							continue;
						}
						$contador++;
						$cero='';
						if($contador<10){
							$cero='0';
						}
						$rutaDetalle = new RutaDetalle;
						$rutaDetalle['ruta_id']=$ruta->id;
						$rutaDetalle['area_id']=$val;
						$rutaDetalle['tiempo_id']=2;
						$rutaDetalle['dtiempo']=1;
						$rutaDetalle['norden']= $cero.$contador;

						$rutaDetalle['fecha_inicio']=$fechahoy;
						$rutaDetalle['fecha_proyectada']=$fproy[0]->fproy;
						
						if($contador == 1){
							$rutaDetalle['dtiempo_final']=$fechahoy;
							$rutaDetalle['tipo_respuesta_id']=1;
							$rutaDetalle['tipo_respuesta_detalle_id']=1;
							$rutaDetalle['observacion']="";
						}
						else{
							$rutaDetalle['ruta_detalle_id_ant']=$ruta_detalle_id_aux;
						}
						
						if ($contador < 3) {
							$rutaDetalle['estado_ruta']=1;
						}
						elseif($contador >= 3){
							$rutaDetalle['estado_ruta']=2;
						}
						
						$rutaDetalle['usuario_created_at']= Auth::user()->id;
						$rutaDetalle['usuario_updated_at']= Auth::user()->id;
						$rutaDetalle['updated_at']=$fechahoy;
						$rutaDetalle->save();

						$array_verbos = array();
						if($contador == 1){
							/*****************************Genera los archivos de apoyo para los expedientes*********************/
							if ( isset($array_data['pdf_archivo']) AND $l == 0 ) {
								foreach( $array_data['pdf_archivo'] as $key => $archivo){
									$url_archivo = "img/admin/ruta_detalle/".date("Y-m-d")."-".$rutaDetalle->id.'-'.$array_data['pdf_nombre'][$key];
									Pretramite::FileToFile($archivo, $url_archivo);
									if( $key == 0 ){
										$archivo_acumulado = $url_archivo;
									}
									else{
										$archivo_acumulado.= "|".$url_archivo;
									}
								}
							}

							$rutaDetalle->archivo = $archivo_acumulado;
							$rutaDetalle->save();
							/*****************************************************************************************************/
							$ruta_detalle_id_aux = $rutaDetalle->ruta_detalle_id_ant;
							$array_verbos = array(1,4);
						}
						else{
							$array_verbos = array(2,14);
						}
					
						foreach ($array_verbos as $key => $rdv) {
							$verbo = Verbo::find($rdv);

							$rutaDetalleVerbo = new RutaDetalleVerbo;
							$rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
							$rutaDetalleVerbo['nombre']= $verbo->nombre;
							$rutaDetalleVerbo['condicion']= 0;
							$rutaDetalleVerbo['rol_id']= 3;
							$rutaDetalleVerbo['verbo_id']= $rdv;
							$rutaDetalleVerbo['documento_id']= 0;
							$rutaDetalleVerbo['orden']= ($key + 1);
							$rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
							$rutaDetalleVerbo->save();

							if( $contador == 1 ){ 
								if( $rdv == 1 ){
									if( $l == 0 ){
										$DocDigitalAuto = $this->DocDigitalAuto( $pretramite->ruta_archivo, $pretramite->area_id_sol, $documento_id, $titulofinal, $pretramite->correlativo );
									}
									$rutaDetalleVerbo['documento']= $titulofinal;
									$rutaDetalleVerbo['doc_digital_id']= $DocDigitalAuto->id;

									$referido=new Referido;
									$referido['ruta_id']= $ruta->id;
									$referido['ruta_detalle_id']= $rutaDetalle->id;
									$referido['norden']= ($key + 1);
									$referido['tabla_relacion_id']= $tablaRelacion->id;
									$referido['doc_digital_id']= $DocDigitalAuto->id;
									$referido['documento_id']= $documento_id;
									$referido['estado_ruta']= 1;
									$referido['tipo']= 1;
									$referido['ruta_detalle_verbo_id']= $rutaDetalleVerbo->id;
									$referido['referido']= $titulofinal;
									$referido['fecha_hora_referido']= $rutaDetalleVerbo->created_at;
									$referido['usuario_referido']= $rutaDetalleVerbo->usuario_created_at;
									$referido['usuario_created_at']= $rutaDetalleVerbo->usuario_created_at;
									$referido->save();
								}
								$rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
								$rutaDetalleVerbo['updated_at']= $fechahoy;
								$rutaDetalleVerbo['finalizo']=1;
								$rutaDetalleVerbo->save();
							}
						}
					}
				}
			} //end if registry was succesfully
			DB::commit();
		}
		return Response::json(
            array(
            'rst'=>1,
            'msj'=>'Registro realizado correctamente',
            )
        );
	}

	public function postCreateservicioarea()
	{ 
		$array_data = Input::all();

		$urld=explode(".", $array_data['pdf_nombre_base']);
		if( strtolower(end($urld)) != 'pdf' ){
			return  array(
				'rst'=>2,
				'msj'=>'Archivo PDF Generado no es un archivo con extensión "PDF"',
			);
		}

		$locales = array();
		for( $i=0; $i < $array_data['numareas']; $i++ ){
			$locales = array_unique(array_merge($locales, $array_data['local_destino'][$i])); //Identificando locales únicos
		}
		sort($locales);
		$titulofinal_aux = ''; $titulofinal = ''; $codigo = array(); $fechahoy = date('Y-m-d H:i:s'); $DocDigitalAuto = array(); $url = ''; $archivo_acumulado = '';
		
		for( $l = 0; $l < count($locales); $l++ ){
			DB::beginTransaction();
			if( $l > 0 ){
				if( $l == 1 ){
					$titulofinal_aux = $pretramite->titulo;
					$pretramite->titulo = $titulofinal_aux.'-001';
					$pretramite->save();
					$tablaRelacion->id_union = $titulofinal_aux.'-001';
					$tablaRelacion->save();
					DB::table('rutas_detalle as rd')
					->join('rutas_detalle_verbo as rdv', 'rdv.ruta_detalle_id','=','rd.id')
					->where('rd.ruta_id', $ruta->id)
					->where('rdv.verbo_id', 1)
					->where('rdv.doc_digital_id', $DocDigitalAuto->id)
					->update(['documento' => $titulofinal_aux.'-001']);

					DB::table('referidos')
					->where('ruta_id', $ruta->id)
					->where('doc_digital_id', $DocDigitalAuto->id)
					->update(['referido' => $titulofinal_aux.'-001']);
				}
				$titulofinal = $titulofinal_aux.'-'.str_pad( ($l+1), 3, "0", STR_PAD_LEFT );
			}

			$pretramite = new Pretramite;
			$documento_id = 0;//Input::get('documento_id');
			if( $l == 0 ){
				$codigo = Pretramite::Correlativo($documento_id);
				$documento = DB::table('documentos')
							->select('nombre', 'nemonico')
							->where('id',$documento_id)
							->first();
				
				$titulo = $codigo->correlativo;
				$buscar = array('@@@@@@','@@@@@','@@@@','@@@','@@','@','####','##');
				$reemplazar = array( str_pad( $titulo, 6, "0", STR_PAD_LEFT ), str_pad( $titulo, 5, "0", STR_PAD_LEFT ),
					str_pad( $titulo, 4, "0", STR_PAD_LEFT ), str_pad( $titulo, 3, "0", STR_PAD_LEFT ),
					str_pad( $titulo, 2, "0", STR_PAD_LEFT ), str_pad( $titulo, 1, "0", STR_PAD_LEFT ),
					date("Y"), date("y")
				);
				$titulofinal = str_replace( $buscar, $reemplazar, $documento->nemonico );
			}
			
			$pretramite['clasificador_tramite_id'] = 0;

			$pretramite['area_id_sol'] =  $array_data['areas'];
			$pretramite['local_origen_id'] =  $array_data['local_origen_id'];
			$pretramite['titulo'] = $titulofinal;
			$pretramite['correlativo'] = $codigo->correlativo;
			$pretramite->año = date("Y");

			$pretramite['tipo_solicitante_id'] = 0;
			$pretramite['tipo_documento_id'] = $documento_id;
			$pretramite['tipo_tramite_id'] = 0;
			$pretramite['documento'] = '';
			$pretramite['nro_folios'] = 0;
			$pretramite['area_id'] = $array_data['areas'];//Área de inicio
			$pretramite['local_id'] = $locales[$l];
			$pretramite['estado_atencion'] = 1;
			$pretramite['fecha_pretramite'] = $fechahoy;
			$pretramite['usuario_created_at'] = Auth::user()->id;
			$pretramite['documento_id'] = $documento_id;
			$pretramite['observacion'] = urldecode(trim($array_data['observacion']));
			$cantidad=true;
			$conteo=0;
			$conteoMax=10;
			//rutas_flujo, personas, flujos, documentos, clasificador_tramite => todas estas tablas con el valor "0"
			
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
						'titulo' => $titulofinal,
						'correlativo' => $pretramite->correlativo
					);
			}

			if( $l == 0 AND trim($array_data['pdf_archivo_base'])!='' ){
                $urld=explode(".", $array_data['pdf_nombre_base']);
                $url = "upload/pretramite/pt-".$pretramite->id.".".end($urld);
				$pretramite->ruta_archivo = $url;
				$pretramite->save();
                
                Pretramite::FileToFile($array_data['pdf_archivo_base'], $url);
            }
			else{
				$pretramite->ruta_archivo = $url;
				$pretramite->save();
			}
			
			/*tramite*/
			if($pretramite->id){ // if registry was succesfully
				$tramite = new Tramite;
				$tramite['pretramite_id'] = $pretramite->id;
				$tramite['area_id_sol'] = $pretramite->area_id_sol;
				$tramite['local_origen_id'] = $pretramite->local_origen_id;
				
				$tramite['area_id'] = $pretramite->area_id;
				$tramite['local_id'] = $pretramite->local_id;
				$tramite['clasificador_tramite_id'] = $pretramite->clasificador_tramite_id;
				$tramite['tipo_solicitante_id'] = $pretramite->tipo_solicitante_id;
				$tramite['tipo_documento_id'] = $pretramite->tipo_documento_id;
				$tramite['documento'] = $pretramite->documento;
				$tramite['nro_folios'] = $pretramite->nro_folios;
				$tramite['observacion'] = $pretramite->observacion;
				$tramite['imagen'] = '';
				$tramite['seguimiento'] = 0;
				$tramite['fecha_tramite'] = $fechahoy;
				$tramite['usuario_created_at'] = Auth::user()->id;
				$tramite->save();
				
				$ruta_flujo = RutaFlujo::find(0);
				$ruta_flujo_id = $ruta_flujo->id;
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
					$tablaRelacion['tramite_id']=$tramite->id;
					$tablaRelacion['id_union']=$pretramite->titulo;
					
					$tablaRelacion['fecha_tramite']= $tramite->fecha_tramite; //Input::get('fecha_tramite');
					$tablaRelacion['tipo_persona']=$tramite->tipo_solicitante_id;
					$tablaRelacion['area_id']= $tramite->area_id_sol;

					if( Input::has('referente') AND trim(Input::get('referente'))!='' ){
						$tablaRelacion['referente']=Input::get('referente');
					}

					if( Input::has('responsable') AND trim(Input::get('responsable'))!='' ){
						$tablaRelacion['responsable']=Input::get('responsable');
					}
					$tablaRelacion['sumilla']=$tramite->observacion;

					$tablaRelacion['persona_autoriza_id']='';
					$tablaRelacion['persona_responsable_id']='';

					$tablaRelacion['usuario_created_at']=Auth::user()->id;
					$tablaRelacion->save();

					$rutaFlujo=$ruta_flujo;//RutaFlujo::find($ruta_flujo_id);

					$ruta= new Ruta;
					$ruta['tabla_relacion_id']=$tablaRelacion->id;
					$ruta['fecha_inicio']= $tramite->fecha_tramite;
					$ruta['ruta_flujo_id']=$rutaFlujo->id;
					$ruta['flujo_id']=$rutaFlujo->flujo_id;
					$ruta['persona_id']=$rutaFlujo->persona_id;
					$ruta['area_id']=$tramite->area_id_sol;
					$ruta['local_id']=$tramite->local_id;
					$ruta['local_origen_id']=$tramite->local_origen_id;
					$ruta['usuario_created_at']= Auth::user()->id;
					$ruta->save();
					
					/************Agregado de referidos*************/
					$referido=new Referido;
					$referido['ruta_id']=$ruta->id;
					$referido['tabla_relacion_id']=$tablaRelacion->id;
					$referido['ruta_detalle_verbo_id']=0;
					$referido['tipo']=0;
					$referido['referido']=$tablaRelacion->id_union;
					$referido['fecha_hora_referido']=$tablaRelacion->created_at;
					$referido['usuario_referido']=$tablaRelacion->usuario_created_at;
					$referido['usuario_created_at']=Auth::user()->id;
					$referido->save();

					if( isset($array_data['ruta_id_ref'][0]) ){
						for( $i = 0; $i < count($array_data['ruta_id_ref']); $i++ ){
							$referidoRelacion=new ReferidoRelacion;
							$referidoRelacion['ruta_id']=$ruta->id;
							$referidoRelacion['ruta_id_ref']=$array_data['ruta_id_ref'][$i];
							$referidoRelacion['estado']=1;
							$referidoRelacion['usuario_created_at'] = Auth::user()->id;
							$referidoRelacion->save();
						}
					}
					
					/**********************************************/

					$qrutaDetalle=DB::table('rutas_flujo_detalle')
								->where('ruta_flujo_id', '=', $rutaFlujo->id)
								->where('estado', '=', '1')
								->orderBy('norden','ASC')
								->get();
					$validaactivar=0;
					
					$area = array($array_data['areas']);
					$area = array_merge($area,$array_data['area']);					
					$contador = 0; $ruta_detalle_id_aux = '';
					$sql="SELECT CalcularFechaFinal( '".$fechahoy."', (1*1440), ".$pretramite->area_id_sol." ) fproy";
					$fproy= DB::select($sql);
					
					foreach($area as $index => $val){
						if( $index > 0 AND !in_array($locales[$l], $array_data['local_destino'][($index-1)]) ){ //Valida que el proceso solo tenga las áreas que contengan el local recorrido
							continue;
						}
						$contador++;
						$cero='';
						if($contador<10){
							$cero='0';
						}
						$rutaDetalle = new RutaDetalle;
						$rutaDetalle['ruta_id']=$ruta->id;
						$rutaDetalle['area_id']=$val;
						$rutaDetalle['tiempo_id']=2;
						$rutaDetalle['dtiempo']=1;
						$rutaDetalle['norden']= $cero.$contador;

						$rutaDetalle['fecha_inicio']=$fechahoy;
						$rutaDetalle['fecha_proyectada']=$fproy[0]->fproy;
						
						if($contador == 1){
							$rutaDetalle['dtiempo_final']=$fechahoy;
							$rutaDetalle['tipo_respuesta_id']=1;
							$rutaDetalle['tipo_respuesta_detalle_id']=1;
							$rutaDetalle['observacion']="";
						}
						else{
							$rutaDetalle['ruta_detalle_id_ant']=$ruta_detalle_id_aux;
						}
						
						if ($contador < 3) {
							$rutaDetalle['estado_ruta']=1;
						}
						elseif($contador >= 3){
							$rutaDetalle['estado_ruta']=2;
						}
						
						$rutaDetalle['usuario_created_at']= Auth::user()->id;
						$rutaDetalle['usuario_updated_at']= Auth::user()->id;
						$rutaDetalle['updated_at']=$fechahoy;
						$rutaDetalle->save();

						$array_verbos = array();
						if($contador == 1){
							/*****************************Genera los archivos de apoyo para los expedientes*********************/
							if ( isset($array_data['pdf_archivo']) AND $l == 0 ) {
								foreach( $array_data['pdf_archivo'] as $key => $archivo){
									$url_archivo = "img/admin/ruta_detalle/".date("Y-m-d")."-".$rutaDetalle->id.'-'.$array_data['pdf_nombre'][$key];
									Pretramite::FileToFile($archivo, $url_archivo);
									if( $key == 0 ){
										$archivo_acumulado = $url_archivo;
									}
									else{
										$archivo_acumulado.= "|".$url_archivo;
									}
								}
							}

							$rutaDetalle->archivo = $archivo_acumulado;
							$rutaDetalle->save();
							/*****************************************************************************************************/
							$ruta_detalle_id_aux = $rutaDetalle->ruta_detalle_id_ant;
							$array_verbos = array(1,4);
						}
						else{
							$array_verbos = array(2,14);
						}
					
						foreach ($array_verbos as $key => $rdv) {
							$verbo = Verbo::find($rdv);

							$rutaDetalleVerbo = new RutaDetalleVerbo;
							$rutaDetalleVerbo['ruta_detalle_id']= $rutaDetalle->id;
							$rutaDetalleVerbo['nombre']= $verbo->nombre;
							$rutaDetalleVerbo['condicion']= 0;
							$rutaDetalleVerbo['rol_id']= 3;
							$rutaDetalleVerbo['verbo_id']= $rdv;
							$rutaDetalleVerbo['documento_id']= 0;
							$rutaDetalleVerbo['orden']= ($key + 1);
							$rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
							$rutaDetalleVerbo->save();

							if( $contador == 1 ){ 
								if( $rdv == 1 ){
									if( $l == 0 ){
										$DocDigitalAuto = $this->DocDigitalAuto( $pretramite->ruta_archivo, $pretramite->area_id_sol, $documento_id, $titulofinal, $pretramite->correlativo );
									}
									$rutaDetalleVerbo['documento']= $titulofinal;
									$rutaDetalleVerbo['doc_digital_id']= $DocDigitalAuto->id;

									$referido=new Referido;
									$referido['ruta_id']= $ruta->id;
									$referido['ruta_detalle_id']= $rutaDetalle->id;
									$referido['norden']= ($key + 1);
									$referido['tabla_relacion_id']= $tablaRelacion->id;
									$referido['doc_digital_id']= $DocDigitalAuto->id;
									$referido['documento_id']= $documento_id;
									$referido['estado_ruta']= 1;
									$referido['tipo']= 1;
									$referido['ruta_detalle_verbo_id']= $rutaDetalleVerbo->id;
									$referido['referido']= $titulofinal;
									$referido['fecha_hora_referido']= $rutaDetalleVerbo->created_at;
									$referido['usuario_referido']= $rutaDetalleVerbo->usuario_created_at;
									$referido['usuario_created_at']= $rutaDetalleVerbo->usuario_created_at;
									$referido->save();
								}
								$rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
								$rutaDetalleVerbo['updated_at']= $fechahoy;
								$rutaDetalleVerbo['finalizo']=1;
								$rutaDetalleVerbo->save();
							}
						}
					}
				}
			} //end if registry was succesfully
			DB::commit();
		}
		return Response::json(
            array(
            'rst'=>1,
            'msj'=>'Registro realizado correctamente',
            )
        );
	}

	protected function DocDigitalAuto($url, $area_id, $tipo_documento_id, $titulofinal, $correlativo, $tipo = ''){
		
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

		$documento = DB::table('documentos')
					->select('nombre', 'nemonico')
					->where('id',$tipo_documento_id)
					->first();

		$DocDigital = new DocumentoDigital;
		$DocDigital->titulo = $titulofinal;
		$DocDigital->asunto = '';
		$DocDigital->correlativo = $correlativo;
		$DocDigital->doc_privado = 0;
		$DocDigital->cuerpo = '.';
		$DocDigital->plantilla_doc_id = $plantilla->id;
		$DocDigital->area_id = $area_id;
		$DocDigital->envio_total = 0;
		$DocDigital->tipo_envio = 1;
		if( $tipo == 'url' ){
			$DocDigital->doc_archivo= '';
			$DocDigital->doc_url= $url;
		}
		else{
			$DocDigital->doc_archivo= $url;
			$DocDigital->doc_url= '';
		}

		$DocDigital->persona_id = Auth::user()->id; 
		$DocDigital->usuario_created_at = Auth::user()->id;

		$cantidad=true;
		$conteo=0;
		$conteoMax=10;
		$correlativoinicial = $correlativo;
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
		
		if($DocDigital->id){
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
						'doc_archivo' => $DocDigital->doc_archivo,
						'doc_url' => $DocDigital->doc_url,
						'usuario_created_at' => Auth::user()->id,
						'persona_id' => Auth::user()->id,
						'created_at' => date('Y-m-d H:i:s')
					]);
		}
		
		//DB::commit();
		return $DocDigital;
	}

	public function postCreate()
	{
		$array_data = json_decode(Input::get('info'));

		$clasificadorTramite = ClasificadorTramite::find($array_data->idclasitramite);

		$pretramite = new Pretramite;
        $codigo = Pretramite::Correlativo($clasificadorTramite->unidad_documentaria);        //var_dump($codigo);exit();      
        $pretramite['clasificador_tramite_id'] = $array_data->idclasitramite;

        if($array_data->idempresa AND $array_data->cbo_tiposolicitante==2){
        	$pretramite['empresa_id'] = $array_data->idempresa;  
            $pretramite['persona_id'] = $array_data->persona_id;
        }else{
        	$pretramite['persona_id'] =  $array_data->persona_id;
        }
        $pretramite['correlativo'] = $codigo->correlativo;
		$pretramite->año = date("Y");
        $pretramite['tipo_solicitante_id'] = $array_data->cbo_tiposolicitante;
        $pretramite['tipo_documento_id'] = $array_data->cbo_tipodoc;
        //$pretramite['tipo_tramite_id'] = $array_data->cbo_tipodocumento;
        $pretramite['documento'] = $array_data->tipodoc;
        $pretramite['nro_folios'] = $array_data->numfolio;
        $pretramite['area_id'] = $array_data->idarea;
        $pretramite['local_id'] = $array_data->local;
        $pretramite['estado_atencion'] = 1;
        $pretramite['fecha_pretramite'] = date('Y-m-d H:i:s');
        $pretramite['usuario_created_at'] = Auth::user()->id;


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
					$codigo->correlativo=str_pad($pretramite->correlativo,6,"0",STR_PAD_LEFT);
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
					'msj'=>'Problemas al generar el correlativo, comuniquese con el área de TI'
				);
		}
		
		$persona = Persona::find($array_data->persona_id);
		$persona->telefono = $array_data->usertelf2;
		$persona->celular = $array_data->usercel2;
		$persona->email = urldecode($array_data->useremail2);
		$persona->direccion = urldecode($array_data->userdirec2);
		$persona->save();


        /*tramite*/
        if($pretramite->id){ // if registry was succesfully
            $tramite = new Tramite;
            $tramite['pretramite_id'] = $pretramite->id;

	        if($pretramite->empresa_id AND $array_data->cbo_tiposolicitante==2 ){
	        	$tramite['empresa_id'] = $pretramite->empresa_id;  
                $tramite['persona_id'] = $pretramite->persona_id; 
	        }else{
	        	$tramite['persona_id'] = $pretramite->persona_id;
	        }
            
            $tramite['area_id'] = $array_data->idarea;
            $tramite['local_id'] = $pretramite->local_id;
	        $tramite['clasificador_tramite_id'] = $pretramite->clasificador_tramite_id;
	        $tramite['tipo_solicitante_id'] = $pretramite->tipo_solicitante_id;
	        $tramite['tipo_documento_id'] = $pretramite->tipo_documento_id;
	        $tramite['documento'] = $pretramite->documento;
	        $tramite['nro_folios'] = $pretramite->nro_folios;
	        $tramite['observacion'] = urldecode(trim($array_data->observacion));
	        $tramite['imagen'] = '';
	        $tramite['fecha_tramite'] = date('Y-m-d H:i:s');
	        $tramite['usuario_created_at'] = Auth::user()->id;
	        $tramite->save();
                           
			$codigo= $clasificadorTramite->unidad_documentaria.'-'.$codigo->correlativo.'-'.date('Y');
			
            $clasificador = ClasificadorTramite::find($array_data->idclasitramite);
            $ruta_flujo = RutaFlujo::find($clasificador->ruta_flujo_id);
            $ruta_flujo_id = $ruta_flujo->id;
        	/* end get ruta flujo*/


        	/*proceso*/
        	$tablaRelacion=DB::table('tablas_relacion as tr')
                ->join(
                    'rutas as r',
                    'tr.id','=','r.tabla_relacion_id'
                )
                ->where('tr.id_union', '=', $codigo)
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
                $tablaRelacion['tramite_id']=$tramite->id;
		        $tablaRelacion['id_union']=$codigo;
		        
		        $tablaRelacion['fecha_tramite']= $tramite->fecha_tramite; //Input::get('fecha_tramite');
		        $tablaRelacion['tipo_persona']=$tramite->tipo_solicitante_id;

		       /* if( Input::has('paterno') AND Input::has('materno') AND Input::has('nombre') ){*/
		       	if($tramite->persona_id){
		            /*$tablaRelacion['paterno']=Input::get('paterno');
		            $tablaRelacion['materno']=Input::get('materno');
		            $tablaRelacion['nombre']=Input::get('nombre');*/
		            $persona = Persona::find($tramite->persona_id);
		        	$tablaRelacion['paterno']=$persona['paterno'];
		            $tablaRelacion['materno']=$persona['materno'];
		            $tablaRelacion['nombre']=$persona['nombre'];
		        }
		        if($array_data->idempresa AND $array_data->cbo_tiposolicitante==2){
		            $tablaRelacion['razon_social']=$array_data->razonsocial;
		            $tablaRelacion['ruc']=$array_data->ruc;
		        }
		        elseif( Input::has('area_p_id') ){
		            $tablaRelacion['area_id']=Input::get('area_p_id');
		        }
		        /*elseif( Input::has('carta_id') ){ // Este caso solo es para asignar carta inicio
		            $tablaRelacion['area_id']=Auth::user()->area_id;
		        }*/
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

		        $tablaRelacion['persona_autoriza_id']='';
		        $tablaRelacion['persona_responsable_id']='';

		        $tablaRelacion['usuario_created_at']=Auth::user()->id;
		        $tablaRelacion->save();

		        $rutaFlujo=$ruta_flujo;//RutaFlujo::find($ruta_flujo_id);

		        $ruta= new Ruta;
		        $ruta['tabla_relacion_id']=$tablaRelacion->id;
		        $ruta['fecha_inicio']= $tramite->fecha_tramite;
		        $ruta['ruta_flujo_id']=$rutaFlujo->id;
		        $ruta['flujo_id']=$rutaFlujo->flujo_id;
		        $ruta['persona_id']=$rutaFlujo->persona_id;
		        $ruta['area_id']=$rutaFlujo->area_id;
		        $ruta['local_id']=$tramite->local_id;
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
	            $carta->save();*/


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
	            foreach($qrutaDetalle as $rd){
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
                    if($rd->norden==1 or ($rd->norden>1 and $validaactivar==0 and $rd->estado_ruta==2) ){
                        /*if($rd->norden==1 && $rd->area_id == 52){
                            $rutaDetalle['dtiempo_final']=date("Y-m-d H:i:s");
                            $rutaDetalle['tipo_respuesta_id']=2;
                                        $rutaDetalle['tipo_respuesta_detalle_id']=1;
                            $rutaDetalle['observacion']="";
                            $rutaDetalle['usuario_updated_at']=Auth::user()->id;
                            $rutaDetalle['updated_at']=date("Y-m-d H:i:s");
                        }*/
                        $rutaDetalle['fecha_inicio']=date("Y-m-d H:i:s");
                    }
	                else{
	                    $validaactivar=1;
	                }
	                $rutaDetalle['usuario_created_at']= Auth::user()->id;
	                $rutaDetalle->save();
	                /**************CARTA DESGLOSE*********************************/
	                /*$cartaDesglose=array();
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
	                    $rutaDetalleVerbo['documento']='';
	                    $rutaDetalleVerbo['usuario_created_at']= Auth::user()->id;
	                    $rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
	                    $rutaDetalleVerbo->save();
                	}*/

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


                                        /*if($rd->norden==1){*/
                            /*if($rd->norden==1 && $rd->area_id == 52){
                                $rutaDetalleVerbo['usuario_updated_at']= Auth::user()->id;
                                $rutaDetalleVerbo['updated_at']= date("Y-m-d H:i:s");
                                $rutaDetalleVerbo['finalizo']=1;
                            }*/

                            $rutaDetalleVerbo->save();
                        }
                    }
                }
		                /*DB::commit();
		                return Response::json(
				            array(
				            'rst'=>1,
				            'msj'=>'Registro realizado correctamente',
				            )
			        	);*/
			}
		} //end if registry was succesfully
		return Response::json(
            array(
            'rst'=>1,
            'msj'=>'Registro realizado correctamente',
            )
        );
	}

	public function getVoucherpretramite()
	{

		/*get data*/
		$rst=Pretramite::getPreTramiteById();
		$data = $rst[0];
		/*end get data*/

		$html = "<html><meta charset=\"UTF-8\">";
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
		$html.="<h3>VOCHER PRE TRAMITE</h3>";
		$html.="
				<table>
					<tr>
						<th>FECHA: </th>
						<td>".$data->fregistro."</td>
					</tr>
					<tr>
						<th>COD PRE TRAMITE: </th>
						<td>".$data->pretramite."</td>
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
					
		if($data->empresa){
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
				
		$html.="</table><hr>
		</body>
		</html>";

		return PDF::load($html, 'A4', 'landscape')->download('voucher-pretramite-'.$data->pretramite);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /pretramite
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /pretramite/{id}
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
	 * GET /pretramite/{id}/edit
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
	 * PUT /pretramite/{id}
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
	 * DELETE /pretramite/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
