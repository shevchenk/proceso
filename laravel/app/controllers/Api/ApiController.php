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
        if( isset($persona->id) ){
            Auth::loginUsingId($persona->id);
        }
        else{
            $result['rst'] = 2;
            return $result;
        }

        if( !isset($persona_alumno->id) ){
            $result['rst'] = 3;
            return $result;
        }

        $empresa = array();
        $empresa['e2'] = array(
            'cbo_tiposolicitante' => 1,
            'cbo_tipotramite' => 1,
            'idclasitramite' => 294, 
            'idarea' => 85, 
            'local' => 2, 
            'cbo_tipodoc' => 1,
            'numfolio' => 1,
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
        ]);
        $pretramite = new \PretramiteController;
        $expediente = $pretramite->postCreateservicio();
        
        Auth::logout();
        $result['rst'] = 1;
        $result['expediente'] = $expediente;
        $result['fecha_expediente'] = date("Y-m-d");
        return $result;
    }
}
