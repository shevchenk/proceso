<?php
namespace Api;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiController extends \BaseController
{
    public function index()
    {
        $r = Request::all();
        $result=array();
        $keyvalidar = base64_encode(hash_hmac("sha256", $r['datos'].date("Ymd"), $_ENV['KEY'], true));
        $keyenviado = str_replace(" ","+", $r['key']);
        $datos = (array) json_decode($r['datos']);
        if( $keyenviado == $keyvalidar ){
            if($datos['opcion']=='IniciarProceso'){
                $result = $this->IniciarProceso($datos);
            }
            elseif($datos['opcion']=='AprobarMatricula'){
                $result = $this->AprobarMatricula($datos);
            }
            elseif($datos['opcion']=='AnularMatricula'){
                $result = $this->AnularMatricula($datos);
            }
            elseif($datos['opcion']=='RegistrarMatricula'){
                $result = $this->RegistrarMatricula($datos);
            }
            elseif($datos['opcion']=='CorregirMatricula'){
                $result = $this->CorregirMatricula($datos);
            }
            elseif($datos['opcion']=='Prueba'){
                $result = array();
            }
            else{
                $result = array();
            }
        }
        else{
            $result = array(
                    'Validación' => 'Error en validación de seguridad (Key Inválido)',
                    'keyvalidar' => $keyvalidar,
                    'keyenviado' => $keyenviado
                    );
        }
        return Response::json($result);
    }

    public function Store()
    {}

    public function Show()
    {}

    public function response($code=200, $status="", $message="")
    {
        http_response_code($code);
        if( !empty($status) && !empty($message) )
        {
            $response = array(
                        "status" => $status ,
                        "message"=>$message,
                        "server" => $_SERVER['REMOTE_ADDR']
                    );  
            echo json_encode($response, JSON_PRETTY_PRINT);
        }            
    }

    public function IniciarProceso($r)
    {
        $result = array();
        $persona = \Persona::where('dni', $r['dni_responsable'])->first();
        $persona_alumno = \Persona::where('dni', $r['dni_alumno'])->first();
        $local_estudios = \Local::where('local', $r['local_estudios'])->first();
        if( isset($persona->id) ){
            Auth::loginUsingId($persona->id);
        }
        else{
            $result['rst'] = 2;
            return $result;
        }

        if( !isset($local_estudios->id) ){
            $result['rst'] = 3;
            return $result;
        }

        if( !isset($persona_alumno->id) ){
            Input::merge([
                'telefono' => $r['telefono_alumno'],
                'celular' => $r['celular_alumno'],
                'email' => $r['email_alumno'],
                'fecha_nacimiento' => $r['fecha_nacimiento'],
                'direccion' => $r['direccion_alumno'],
                'dni' => $r['dni_alumno'],
                'sexo' => substr($r['sexo'],0,1),
                'password' => $r['dni_alumno'],
                'responsable_area' => "",
                'local_id' => $local_estudios->id,
                'modalidad' => 1,
                'vista_doc' => 1,
                'estado' => 1,
                'paterno' => $r['paterno_alumno'],
                'materno' => $r['materno_alumno'],
                'nombre' => $r['nombre_alumno'],
                'email_mdi' => null,
                'doc_privados' => null,
            ]);
            $personaFinal = new \PersonaFinalController;
            $persona_alumno = $personaFinal->postCrearpersona();
        }
        
        if( !isset($persona_alumno->id) ){
            $result['rst'] = 4;
            return $result;
        }

        $empresa = array();
        $empresa['e2'] = array(
            'cbo_tiposolicitante' => 1,
            'cbo_tipotramite' => 1,
            'idclasitramite' => 746, 
            'idarea' => 85, 
            'local' => $local_estudios->id, 
            'cbo_tipodoc' => 492,
            'numfolio' => 1,
            'campos' => array(
                '2954' => $r['id'],
                '2956' => $r['sexo'],
                '2957' => $r['fecha_nacimiento'],
                '2958' => $r['celular_alumno'],
                '2959' => $r['email_alumno'],

                '2961' => $r['carrera'],
                '2962' => $r['curso'],
                '2963' => $r['modalidad'],
                '2964' => $r['fecha_inicio'],
                '2965' => $r['horario'],
                '2966' => $r['frecuencia'],
                '2967' => $r['local_estudios'],

                '2969' => $r['inscripcion'],
                '2970' => $r['matricula'],
                '2971' => $r['cuotas'],
                '2972' => $r['1c'],
                '2973' => $r['2c'],
                '2974' => $r['3c'],
                '2976' => $r['adicional1'],
                '2977' => $r['adicional2'],

                '2979' => $r['nro_ins'],
                '2980' => $r['tipo_ins'],
                '2982' => $r['monto_ins'],

                '2984' => $r['nro_mat'],
                '2985' => $r['tipo_mat'],
                '2987' => $r['monto_mat'],

                '2989' => $r['nro_cur'],
                '2990' => $r['tipo_cur'],
                '2992' => $r['monto_cur'],
                '2993' => $r['total_cur'],

                '2995' => $r['nro_pro'],
                '2996' => $r['tipo_pro'],
                '2998' => $r['monto_pro'],

                '3002' => $r['cajero'],
                '3001' => $r['vendedor'],
                '3003' => $r['responsable'],
                '3004' => $r['created_at'],
                '3000' => $r['medio_captacion2'],
                '3005' => $r['supervisor'],
                '3006' => $r['updated_at'],
            ),
        );

        $empresa['e42'] = array(
            'cbo_tiposolicitante' => 1,
            'cbo_tipotramite' => 1,
            'idclasitramite' => 746, 
            'idarea' => 85, 
            'local' => $local_estudios->id, 
            'cbo_tipodoc' => 492,
            'numfolio' => 1,
            'campos' => array(
                '2954' => $r['id'],
                '2956' => $r['sexo'],
                '2957' => $r['fecha_nacimiento'],
                '2958' => $r['celular_alumno'],
                '2959' => $r['email_alumno'],

                '2961' => $r['carrera'],
                '2962' => $r['curso'],
                '2963' => $r['modalidad'],
                '2964' => $r['fecha_inicio'],
                '2965' => $r['horario'],
                '2966' => $r['frecuencia'],
                '2967' => $r['local_estudios'],

                '2969' => $r['inscripcion'],
                '2970' => $r['matricula'],
                '2971' => $r['cuotas'],
                '2972' => $r['1c'],
                '2973' => $r['2c'],
                '2974' => $r['3c'],
                '2976' => $r['adicional1'],
                '2977' => $r['adicional2'],

                '2979' => $r['nro_ins'],
                '2980' => $r['tipo_ins'],
                '2982' => $r['monto_ins'],

                '2984' => $r['nro_mat'],
                '2985' => $r['tipo_mat'],
                '2987' => $r['monto_mat'],

                '2989' => $r['nro_cur'],
                '2990' => $r['tipo_cur'],
                '2992' => $r['monto_cur'],
                '2993' => $r['total_cur'],

                '2995' => $r['nro_pro'],
                '2996' => $r['tipo_pro'],
                '2998' => $r['monto_pro'],

                '3002' => $r['cajero'],
                '3001' => $r['vendedor'],
                '3003' => $r['responsable'],
                '3004' => $r['created_at'],
                '3000' => $r['medio_captacion2'],
                '3005' => $r['supervisor'],
                '3006' => $r['updated_at'],
            ),
        );

        Input::merge([
            'areas' => 0,
            'cbo_tiposolicitante' => $empresa[$r['empresa_id']]['cbo_tiposolicitante'],
            'cbo_tipotramite' => $empresa[$r['empresa_id']]['cbo_tipotramite'], 
            'empresa_id_sol' => array(0),
            'persona_id_sol' => array( $persona_alumno->id ),
            'telefono_sol' => array( $r['telefono_alumno'] ),
            'celular_sol' => array( $r['celular_alumno'] ),
            'email_sol' => array( $r['email_alumno'] ),
            'direccion_sol' => array( $r['direccion_alumno'] ),
            'idclasitramite' => $empresa[$r['empresa_id']]['idclasitramite'], 
            'idarea' => $empresa[$r['empresa_id']]['idarea'], 
            'local' => $empresa[$r['empresa_id']]['local'], 
            'cbo_tipodoc' => $empresa[$r['empresa_id']]['cbo_tipodoc'],
            'numfolio' => $empresa[$r['empresa_id']]['numfolio'],
            'tipodoc' => 'S/N',
            'observacion' => 'Proceso automático',
            'apiproceso' => 1,
            'campos' => $empresa[$r['empresa_id']]['campos'],
            'archivo_ins' => $r['archivo_ins'],
            'archivo_mat' => $r['archivo_mat'],
            'archivo_pro' => $r['archivo_pro'],
            'archivo_cur' => $r['archivo_cur'],
            'url' => $r['url'],
            'tipo_documento_id' => $r['tipo_documento_id']
        ]);
        
        $pretramite = new \PretramiteController;
        $expediente = $pretramite->postCreateservicio();
        
        Auth::logout();
        $result['rst'] = 1;
        $result['expediente'] = $expediente;
        $result['fecha_expediente'] = date("Y-m-d");
        return $result;
    }

    public function AprobarMatricula($r)
    {
        $datos = array(
            "opcion" => $r['opcion'],
            "matricula_id" => $r['matricula_id'],
            "dni" => $r['dni']
        );
        $datos = json_encode($datos);
        $key = base64_encode(hash_hmac("sha256", $datos.date("Ymd"), $_ENV['KEY'], true));
        
        $parametros = array(
            'key' => $key,
            'datos' => $datos,
        );
        $url = $_ENV['URL_FC']."?".http_build_query($parametros);
        $objArr = \Menu::curl($url, $parametros);
        $result['rst'] = 1;
        if( isset($objArr->rst) AND $objArr->rst*1 == 1 ){ /*No realiza nada...*/ }
        else{
            $result['rst'] = 2;
        }
        return $result;
    }

    public function AnularMatricula($r)
    {
        $datos = array(
            "opcion" => $r['opcion'],
            "matricula_id" => $r['matricula_id'],
            "dni" => $r['dni']
        );
        $datos = json_encode($datos);
        $key = base64_encode(hash_hmac("sha256", $datos.date("Ymd"), $_ENV['KEY'], true));
        
        $parametros = array(
            'key' => $key,
            'datos' => $datos,
        );
        $url = $_ENV['URL_FC']."?".http_build_query($parametros);
        $objArr = \Menu::curl($url, $parametros);
        $result['rst'] = 1;
        if( isset($objArr->rst) AND $objArr->rst*1 == 1 ){ /*No realiza nada...*/ }
        else{
            $result['rst'] = 2;
        }

        if( $result['rst'] == 1 ){
            $persona = \Persona::where('dni', $r['dni'])->first();
            $ruta_id = $r['ruta_id'];
            DB::beginTransaction();
            
            $r=\Ruta::find($ruta_id);
            $r['estado']=0;
            $r['usuario_updated_at']=$persona->id;
            $r->save();

            $tr=\TablaRelacion::find($r->tabla_relacion_id);
            $tr['estado']=0;
            $tr['usuario_updated_at']=$persona->id;
            $tr->save();

            if( isset($tr->tramite_id) AND trim($tr->tramite_id) != '' ){
                $tra = \Tramite::find($tr->tramite_id);
                $tra->estado = 0;
                $tra->usuario_updated_at = $persona->id;
                $tra->save();

                if( isset($tra->pretramite_id) AND trim($tra->pretramite_id) != ''){
                    $ptra = \Pretramite::find($tra->pretramite_id);
                    $ptra->estado_atencion = 2;
                    $ptra->observacion = $ptra->observacion." <b>( Trámite anulado por tesorería )</b>";
                    $ptra->usuario_updated_at = $persona->id;
                    $ptra->save();
                }
            }

            DB::commit();
            $result['anular'] = 1;
        }
        return $result;
    }

    public function RegistrarMatricula($r)
    {
        $datos = array(
            "opcion" => $r['opcion'],
            "matricula_id" => $r['matricula_id'],
            "dni" => $r['dni']
        );
        $datos = json_encode($datos);
        $key = base64_encode(hash_hmac("sha256", $datos.date("Ymd"), $_ENV['KEY'], true));
        
        $parametros = array(
            'key' => $key,
            'datos' => $datos,
        );
        $url = $_ENV['URL_FC']."?".http_build_query($parametros);
        $objArr = \Menu::curl($url, $parametros);
        $result['rst'] = 1;
        if( isset($objArr->rst) AND $objArr->rst*1 == 1 ){ /*No realiza nada...*/ }
        else{
            $result['rst'] = 2;
        }
        return $result;
    }

    public function CorregirMatricula($r)
    {
        $datos = array(
            "opcion" => $r['opcion'],
            "matricula_id" => $r['matricula_id'],
            "dni" => $r['dni']
        );
        $datos = json_encode($datos);
        $key = base64_encode(hash_hmac("sha256", $datos.date("Ymd"), $_ENV['KEY'], true));
        
        $parametros = array(
            'key' => $key,
            'datos' => $datos,
        );
        $url = $_ENV['URL_FC']."?".http_build_query($parametros);
        $objArr = \Menu::curl($url, $parametros);
        $result['rst'] = 1;
        if( isset($objArr->rst) AND $objArr->rst*1 == 1 ){ /*No realiza nada...*/ }
        else{
            $result['rst'] = 2;
        }

        if( $result['rst'] == 1 ){
            $persona = \Persona::where('dni', $r['dni'])->first();
            $ruta_id = $r['ruta_id'];
            DB::beginTransaction();
            
            $r=\Ruta::find($ruta_id);
            $r['estado']=0;
            $r['usuario_updated_at']=$persona->id;
            $r->save();

            $tr=\TablaRelacion::find($r->tabla_relacion_id);
            $tr['estado']=0;
            $tr['usuario_updated_at']=$persona->id;
            $tr->save();

            if( isset($tr->tramite_id) AND trim($tr->tramite_id) != '' ){
                $tra = \Tramite::find($tr->tramite_id);
                $tra->estado = 0;
                $tra->usuario_updated_at = $persona->id;
                $tra->save();

                if( isset($tra->pretramite_id) AND trim($tra->pretramite_id) != ''){
                    $ptra = \Pretramite::find($tra->pretramite_id);
                    $ptra->estado_atencion = 2;
                    $ptra->observacion = $ptra->observacion." <b>( Trámite anulado por Coordinación Académica )</b>";
                    $ptra->usuario_updated_at = $persona->id;
                    $ptra->save();
                }
            }

            DB::commit();
            $result['anular'] = 1;
        }
        return $result;
    }
}
