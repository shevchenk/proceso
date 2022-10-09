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
            'idclasitramite' => 746, 
            'idarea' => 85, 
            'local' => 42, 
            'cbo_tipodoc' => 492,
            'numfolio' => 1,
            'campos' => array(
                '2762' => $r['id'],
                '2757' => $r['sexo'],
                '2756' => $r['fecha_nacimiento'],
                '2755' => $r['celular_alumno'],
                '2754' => $r['email_alumno'],

                '2708' => $r['carrera'],
                '2709' => $r['curso'],
                '2710' => $r['modalidad'],
                '2711' => $r['fecha_inicio'],
                '2712' => $r['horario'],
                '2713' => $r['frecuencia'],
                '2714' => $r['local_estudios'],

                '2741' => $r['inscripcion'],
                '2742' => $r['matricula'],
                '2743' => $r['cuotas'],
                '2744' => $r['1c'],
                '2745' => $r['2c'],
                '2746' => $r['3c'],
                '2748' => $r['adicional1'],
                '2749' => $r['adicional2'],

                '2716' => $r['nro_ins'],
                '2717' => $r['tipo_ins'],
                '2718' => $r['monto_ins'],

                '2720' => $r['nro_mat'],
                '2721' => $r['tipo_mat'],
                '2722' => $r['monto_mat'],

                '2724' => $r['nro_cur'],
                '2725' => $r['tipo_cur'],
                '2726' => $r['monto_cur'],
                '2727' => $r['total_cur'],

                '2729' => $r['nro_pro'],
                '2730' => $r['tipo_pro'],
                '2731' => $r['monto_pro'],

                '2737' => $r['cajero'],
                '2738' => $r['vendedor'],
                '2739' => $r['responsable'],
                '2771' => $r['created_at'],
                '2759' => $r['medio_captacion2'],
                '2772' => $r['supervisor'],
                '2773' => $r['updated_at'],
            ),
        );

        $empresa['e42'] = array(
            'cbo_tiposolicitante' => 1,
            'cbo_tipotramite' => 1,
            'idclasitramite' => 746, 
            'idarea' => 85, 
            'local' => 42, 
            'cbo_tipodoc' => 492,
            'numfolio' => 1,
            'campos' => array(
                '2762' => $r['id'],
                '2757' => $r['sexo'],
                '2756' => $r['fecha_nacimiento'],
                '2755' => $r['celular_alumno'],
                '2754' => $r['email_alumno'],

                '2708' => $r['carrera'],
                '2709' => $r['curso'],
                '2710' => $r['modalidad'],
                '2711' => $r['fecha_inicio'],
                '2712' => $r['horario'],
                '2713' => $r['frecuencia'],
                '2714' => $r['local_estudios'],

                '2741' => $r['inscripcion'],
                '2742' => $r['matricula'],
                '2743' => $r['cuotas'],
                '2744' => $r['1c'],
                '2745' => $r['2c'],
                '2746' => $r['3c'],
                '2748' => $r['adicional1'],
                '2749' => $r['adicional2'],

                '2716' => $r['nro_ins'],
                '2717' => $r['tipo_ins'],
                '2718' => $r['monto_ins'],

                '2720' => $r['nro_mat'],
                '2721' => $r['tipo_mat'],
                '2722' => $r['monto_mat'],

                '2724' => $r['nro_cur'],
                '2725' => $r['tipo_cur'],
                '2726' => $r['monto_cur'],
                '2727' => $r['total_cur'],

                '2729' => $r['nro_pro'],
                '2730' => $r['tipo_pro'],
                '2731' => $r['monto_pro'],

                '2737' => $r['cajero'],
                '2738' => $r['vendedor'],
                '2739' => $r['responsable'],
                '2771' => $r['created_at'],
                '2759' => $r['medio_captacion2'],
                '2772' => $r['supervisor'],
                '2773' => $r['updated_at'],
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
