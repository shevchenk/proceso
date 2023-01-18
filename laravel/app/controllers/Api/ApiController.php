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
        $campos = explode(",", $_ENV['CAMPOS']);
        $empresa = array(
            'cbo_tiposolicitante' => 1,
            'cbo_tipotramite' => 1,
            'idclasitramite' => 746, 
            'idarea' => 85, 
            'local' => $local_estudios->id, 
            'cbo_tipodoc' => 492,
            'numfolio' => 1,
            'campos' => array(
                $campos[0] => $r['id'],
                $campos[1] => $r['sexo'],
                $campos[2] => $r['fecha_nacimiento'],
                $campos[3] => $r['celular_alumno'],
                $campos[4] => $r['email_alumno'],

                $campos[5] => $r['carrera'],
                $campos[6] => $r['curso'],
                $campos[7] => $r['modalidad'],
                $campos[8] => $r['fecha_inicio'],
                $campos[9] => $r['horario'],
                $campos[10] => $r['frecuencia'],
                $campos[11] => $r['local_estudios'],

                $campos[12] => $r['inscripcion'],
                $campos[13] => $r['matricula'],
                $campos[14] => $r['cuotas'],
                $campos[15] => $r['1c'],
                $campos[16] => $r['2c'],
                $campos[17] => $r['3c'],
                $campos[18] => $r['adicional1'],
                $campos[19] => $r['adicional2'],

                $campos[18] => $r['nro_ins'],
                $campos[19] => $r['tipo_ins'],
                $campos[20] => $r['monto_ins'],

                $campos[21] => $r['nro_mat'],
                $campos[22] => $r['tipo_mat'],
                $campos[23] => $r['monto_mat'],

                $campos[24] => $r['nro_cur'],
                $campos[25] => $r['tipo_cur'],
                $campos[26] => $r['monto_cur'],
                $campos[27] => $r['total_cur'],

                $campos[28] => $r['nro_pro'],
                $campos[29] => $r['tipo_pro'],
                $campos[30] => $r['monto_pro'],

                $campos[31] => $r['cajero'],
                $campos[32] => $r['vendedor'],
                $campos[33] => $r['responsable'],
                $campos[34] => $r['created_at'],
                $campos[35] => $r['medio_captacion2'],
                $campos[36] => $r['supervisor'],
                $campos[37] => $r['updated_at'],
            ),
        );

        Input::merge([
            'areas' => 0,
            'cbo_tiposolicitante' => $empresa['cbo_tiposolicitante'],
            'cbo_tipotramite' => $empresa['cbo_tipotramite'], 
            'empresa_id_sol' => array(0),
            'persona_id_sol' => array( $persona_alumno->id ),
            'telefono_sol' => array( $r['telefono_alumno'] ),
            'celular_sol' => array( $r['celular_alumno'] ),
            'email_sol' => array( $r['email_alumno'] ),
            'direccion_sol' => array( $r['direccion_alumno'] ),
            'idclasitramite' => $empresa['idclasitramite'], 
            'idarea' => $empresa['idarea'], 
            'local' => $empresa['local'], 
            'cbo_tipodoc' => $empresa['cbo_tipodoc'],
            'numfolio' => $empresa['numfolio'],
            'tipodoc' => 'S/N',
            'observacion' => 'Proceso automático',
            'apiproceso' => 1,
            'campos' => $empresa['campos'],
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
        $objArr = \Menu::curl($url);
        $result['rst'] = 1;
        $result['anular'] = 0;
        if( isset($objArr->rst) AND $objArr->rst*1 == 1 ){ 
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
        else{
            $result['rst'] = 2;
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
        $objArr = \Menu::curl($url);
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
