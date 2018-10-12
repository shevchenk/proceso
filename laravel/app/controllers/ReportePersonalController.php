<?php

class ReportePersonalController extends BaseController
{   // extends \BaseController
    protected $_errorController;
    /**
     * Valida sesion activa
     */
    public function __construct(ErrorController $ErrorController)
    {        
        if (!Request::is('*reportepersonaladmsource*'))
        $this->beforeFilter('auth');

        $this->_errorController = $ErrorController;
    }

    public function postReportepersonal()
    {
        $fecha = explode('-', Input::get('fecha_ini'));

        $result = file_get_contents("http://10.0.120.13:8088/spersonal/index.php?mes=".$fecha[1]."&anno=".$fecha[0]);

        return utf8_encode($result);
        /*
        return Response::json(
            array(
                'rst'=>1,
                'datos'=>$rst
                //'datos' => array('data' => $rst)
            )
        );
        */
    }

    /*
    private function verDiasTranscurridos($fecha_ini, $fecha_fin)
    {
        $fecha_i = str_replace('/', '-', $fecha_ini);
        $fecha_f = str_replace('/', '-', $fecha_fin);
        $dias = (strtotime($fecha_i) - strtotime($fecha_f))/86400;
        $dias = abs($dias); 
        $dias = floor($dias);
        return ($dias + 1);
    }
    */

    public function postReportepersonaladmtemp(){
            $fecha_ini = Input::get('fecha_ini');
            $fecha_fin = Input::get('fecha_fin');
            $tipo = Input::get('tipo');
            $ar=Input::get('area_id');
            $filtro=Input::get('filtro');

            if(count($ar) > 0 && $ar!="" ){
                $areas = " AND a.id IN (".implode(",",$ar).")";
            }else{
                $areas = "";
            }

            $qryFiltro = "";
            $qryRegimen = "";

            if(count($filtro) > 0 && $filtro!="" )
                foreach ($filtro as $item) {
                    switch ($item) {

                    case '1.1': // 3 Faltas o más
                       $qryFiltro .= "AND SUM(faltas) > 2 ";
                        break;
                    case '1.2': // 1 o más permisos medicos
                       $qryFiltro .= "AND SUM(descanso_med) > 0 ";
                        break;
                    case '2.1': // C.A.S.
                       $qryRegimen .= "'C.A.S.',";
                        break;
                    case '2.2': // TERCEROS
                       $qryRegimen .= "'TERCEROS',";
                        break;
                    case '2.3': // EMPLEADO NOMBRADO
                       $qryRegimen .= "'EMPLEADO NOMBRADO',";
                        break;
                    case '2.4': // EMPLEADO CONTRATADO
                       $qryRegimen .= "'EMPLEADO CONTRATADO',";
                        break;
                    case '2.5': // FUNCIONARIOS CAS
                       $qryRegimen .= "'FUNCIONARIOS CAS',";
                        break;
                    case '2.6': // OBREROS
                       $qryRegimen .= "'OBREROS',";
                        break;
                    case '2.7': // FUNCIONARIOS
                       $qryRegimen .= "'FUNCIONARIOS',";
                        break;
                    case '2.8': // C.A.S. REINCORPORADOS
                       $qryRegimen .= "'C.A.S. REINCORPORADOS',";
                        break;
                    default:
                        # code...
                        break;
                }
            }

            $qryRegimen = ( strlen($qryRegimen) > 2 ? ' AND sat.regimen IN ('.substr($qryRegimen, 0,-1).')' : '' );
            $qryFiltro = ( strlen($qryFiltro) > 3 ? ' HAVING 1 '.$qryFiltro : '' );
            
            if($tipo=='1'){
              $qryTipo = "GROUP BY nombres";
            }elseif($tipo=='6'){    
              $qryTipo = "GROUP BY area";
            }else{
              $qryTipo = "GROUP BY nombres";
            }

            $sql = "SELECT 
            sat.persona_id,
            sat.foto,
            a.nombre as area,
            sat.nombres,
            sat.dni,
            sat.cargo,
            sat.regimen,
            SUM(sat.faltas) as faltas,
            SUM(sat.tardanza) as tardanza,
            SUM(sat.lic_sg) as lic_sg,
            SUM(sat.sancion_dici) as sancion_dici,
            SUM(sat.lic_sindical) as lic_sindical,
            SUM(sat.descanso_med) as descanso_med,
            SUM(sat.min_permiso) as min_permiso,
            SUM(sat.comision) as comision,
            SUM(sat.citacion) as citacion,
            SUM(sat.essalud) as essalud,
            SUM(sat.permiso) as permiso,
            SUM(sat.compensatorio) as compensatorio,
            SUM(sat.onomastico) as onomastico 

            FROM sw_asistencias_temp as sat 
            INNER JOIN areas as a 

            WHERE sat.fecha_asistencia 
            BETWEEN '$fecha_ini' 
            AND '$fecha_fin' 
            AND sat.area = a.id $areas 
            AND sat.nombres != '' 

            $qryRegimen
            
            GROUP BY nombres

            $qryFiltro            
            
            ORDER BY SUM(sat.faltas) DESC";

            $lis = DB::select($sql);

            return Response::json(
                array(
                    'rst'=>1,
                    'reporte'=> $lis,
                    'sql' => $sql
                )
            );

    }
    

    public function postReportepersonaladm()
    {
        //try 
        //{
            ini_set('max_execution_time', 300);
            AuditoriaAcceso::getAuditoria();
            $fecha_ini = Input::get('fecha_ini'); // 2017/09/01
            $fecha_fin = Input::get('fecha_fin'); // 2017/09/15
            $area_ws = Input::get('area_ws');

            $dias=20-1;
            $fecha_i = str_replace('/', '-', $fecha_ini);
            $fecha_f = str_replace('/', '-', $fecha_fin);
            $fecha_iaux =$fecha_i;
            $fecha_faux = date("Y-m-d", strtotime($fecha_i ."+".$dias." days"));

            if($area_ws <> 0)
                $bus_area = "&area=".$area_ws;
            else
                $bus_area = "";

            //$dias = $this->verDiasTranscurridos($fecha_ini, $fecha_fin);

            //DB::table('sw_asistencias')->truncate();
            DB::table('sw_asistencias')->where('usuario_created_at', '=', Auth::user()->id)->delete();

            while ( $fecha_iaux <= $fecha_f ) {
                
                if( $fecha_faux>$fecha_f ){
                    $fecha_faux=$fecha_f;
                }

                $fini=date("Y/m/d",strtotime($fecha_iaux));
                $ffin=date("Y/m/d",strtotime($fecha_faux));

//                die("http://10.0.120.13:8088/spersonal/consulta.php?inicio=".$fini.""."&fin=".$ffin.$bus_area);

                $res = file_get_contents("http://10.0.120.13:8088/spersonal/consulta.php?inicio=".$fini.""."&fin=".$ffin.$bus_area);


                $result = json_decode(utf8_encode($res));

                foreach($result->reporte as $key => $lis)  
                {
                    DB::beginTransaction();
                    $obj = new ReportePersonal;
                    $obj->foto = $lis->foto;
                    $obj->area = $lis->AREA;
                    $obj->nombres = $lis->nombres_completos;
                    $obj->dni = $lis->dni;
                    $obj->cargo = $lis->cargo;
                    $obj->regimen = $lis->condicion;
                    $obj->faltas = $lis->FALTAS;
                    $obj->tardanza = $lis->TARDANZAS;
                    $obj->lic_sg = $lis->SLSG;
                    $obj->sancion_dici = $lis->Sancion_Dici;
                    $obj->lic_sindical = $lis->Licencia_Sindical;
                    $obj->descanso_med = $lis->DESCANSO_MEDICO;
                    $obj->min_permiso = $lis->MINPERMISO;
                    $obj->comision = $lis->comision;
                    $obj->citacion = $lis->CITACION;
                    $obj->essalud = $lis->ESSALUD;
                    $obj->permiso = $lis->PERMISO;
                    $obj->compensatorio = $lis->COMPENSATORIO;
                    $obj->onomastico = $lis->ONOMASTICO;

                    $obj->estado = 1;
                    $obj->usuario_created_at = Auth::user()->id;
                    $obj->save();

                    DB::commit();
                }

                $fecha_iaux= date("Y-m-d", strtotime($fecha_faux ."+1 days"));
                $fecha_faux= date("Y-m-d", strtotime($fecha_iaux ."+".$dias." days"));
            }

            // Actualiza campo "persona_id" en base al "id" de la tabla "personas".            
            $sql = "UPDATE sw_asistencias sa
                        INNER JOIN personas p ON sa.dni=p.dni
                        SET sa.persona_id = p.id;";
            DB::update($sql);
            // --

            // Actualiza campo "persona_id" en base que no tenga asociado un "id" de la tabla "personas". 
            DB::table('sw_asistencias')
                ->whereNull('persona_id')
                ->update(array('persona_id' => 1272));
            // --      

            $sql = "SELECT sw.*,
                        ca.cant_act,
                        doc.docu,
                        tt.total_tramites,
                        t.tareas
                        FROM (
                            SELECT a.foto, a.area, a.nombres, a.dni, a.cargo, a.regimen,
                                                SUM(a.faltas) faltas, SUM(a.tardanza) tardanza, SUM(a.lic_sg) lic_sg, SUM(a.sancion_dici) sancion_dici,
                                                SUM(a.lic_sindical) lic_sindical, SUM(a.descanso_med) descanso_med, SUM(a.min_permiso) min_permiso, SUM(a.comision) comision,
                                                SUM(a.citacion) citacion, SUM(a.essalud) essalud, SUM(a.permiso) permiso, SUM(a.compensatorio) compensatorio,
                                                SUM(a.onomastico) onomastico, a.usuario_created_at,a.persona_id
                                FROM sw_asistencias a
                                WHERE NOT a.dni = '07135876'
                                AND a.usuario_created_at = '".Auth::user()->id."'
                                GROUP BY a.area, a.nombres, a.dni, a.cargo
                        ) sw
                        LEFT JOIN (SELECT COUNT(rdv.id) tareas, rdv.usuario_updated_at persona_id
                                FROM rutas_detalle_verbo rdv
                                WHERE rdv.finalizo=1 
                                AND rdv.updated_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                GROUP BY rdv.usuario_updated_at
                        ) AS t ON t.persona_id=sw.persona_id
                        LEFT JOIN (SELECT ROUND((SUM(ap.ot_tiempo_transcurrido) / 60), 2) cant_act, ap.persona_id
                                FROM actividad_personal ap
                                WHERE ap.persona_id=ap.usuario_created_at
                                AND ap.fecha_inicio BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                GROUP BY ap.persona_id
                            ) AS ca ON ca.persona_id=sw.persona_id
                        LEFT JOIN (SELECT COUNT(r.id) total_tramites, r.usuario_created_at persona_id
                                FROM rutas r
                                WHERE r.created_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                AND r.estado=1
                                GROUP BY r.usuario_created_at
                            ) AS tt ON tt.persona_id=sw.persona_id
                        LEFT JOIN (SELECT COUNT(dd.id) docu, dd.usuario_created_at persona_id
                                FROM doc_digital_temporal dd
                                WHERE dd.created_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                AND dd.estado = 1
                                GROUP BY dd.usuario_created_at
                            ) AS doc ON doc.persona_id=sw.persona_id; ";

            $lis = DB::select($sql);

            return Response::json(
                        array(
                            'rst'=>1,
                            'reporte'=> $lis
                        )
                    );
        /*}
        catch (\Exception $e) 
        {
            DB::rollback();
            return Response::json(
                        array(
                            'rst'=>2,
                            'reporte'=> 'not_data'
                        )
                    );
        }*/
    }


    public function getReportepersonaladmsource($fi)
    {
        //try 
        //{}

            ini_set('max_execution_time', 300);
            //AuditoriaAcceso::getAuditoria();
            $area_ws = 0;


            $areasINFORMATICA = array(
            'Alcadía'=>'44',
            'Gerencia de Administración y Finanzas'=>'26',
            'Gerencia de Asesoria Legal'=>'27',
            'Gerencia de Desarrollo Economico Local'=>'9',
            'Gerencia de Desarrollo Social'=>'15',
            'Gerencia de Desarrollo Urbano'=>'24',
            'Gerencia de Fiscalizacion y Control Municipal'=>'10',
            'Gerencia de Gestion Ambiental'=>'21',
            'Gerencia de Infraestructura Pública'=>'25',
            'Gerencia de Modernización de la Gestión Municipal'=>'94',
            'Gerencia de Planificación, Presupuesto y Racionalización'=>'28',
            'Gerencia de Promoción de la Inversión y Cooperación'=>'12',
            'Gerencia de Rentas'=>'11',
            'Gerencia de Secretaría General'=>'30',
            'Gerencia de Seguimiento y Evaluación'=>'31',
            'Gerencia de Seguridad Ciudadana'=>'19',
            'Gerencia Municipal'=>'32',
            'Organo de Control Institucional'=>'33',
            'Procuraduria Municipal'=>'34',
            'Sub Gerencia de Areas Verdes y Saneamiento Ambiental'=>'22',
            'Sub Gerencia de Contabilidad y Costos'=>'35',
            'Sub Gerencia de Ejecutoria Coactiva'=>'36',
            'Sub Gerencia de Imagen Instuticional y Participación Vecinal'=>'13',
            'Sub Gerencia de Juventudes, Recreacion y Deporte'=>'17',
            'Sub Gerencia de la Mujer, Educación, Cultura, Serv. Social, CIAM, Y DEMUNA'=>'16',
            'Sub Gerencia de Limpieza Publica'=>'23',
            'Sub Gerencia de Logística'=>'29',
            'Sub Gerencia de Personal'=>'53',
            'Sub Gerencia de Programas Alimentarios Y Salud'=>'18',
            'Sub Gerencia de Servicios Generales'=>'38',
            'Sub Gerencia de Tecnologia de Informacion y la Comunicacion'=>'14',
            'Sub Gerencia de Tesorería'=>'42',
            'Sub Gerencia de Vigilancia Ciudadana e Informacion'=>'20'
            );


            $dias=20-1;
            $fecha_i = str_replace('/', '-', $fi);
            $fecha_f = str_replace('/', '-', $fi);
            $fecha_iaux =$fecha_i;
            $fecha_faux = date("Y-m-d", strtotime($fecha_i ."+".$dias." days"));

            DB::table('sw_asistencias_temp')->whereRaw('DATE_FORMAT(fecha_asistencia,\'%Y-%m-%d\')=\''.$fecha_i.'\'')->delete();

            while ( $fecha_iaux <= $fecha_f ) {
                
                if( $fecha_faux>$fecha_f ){
                    $fecha_faux=$fecha_f;
                }

                $fini=date("Y/m/d",strtotime($fecha_iaux));
                $ffin=date("Y/m/d",strtotime($fecha_faux));

                $res = file_get_contents("http://10.0.120.13:8088/spersonal/consulta.php?inicio=".$fini.""."&fin=".$ffin);

                $result = json_decode(utf8_encode($res));

                foreach($result->reporte as $key => $lis){
                    DB::beginTransaction();
                    $obj = new ReportePersonal("sw_asistencias_temp");
                    $obj->foto = $lis->foto;
                    $obj->area = (isset($areasINFORMATICA[trim($lis->AREA)]) ? $areasINFORMATICA[trim($lis->AREA)] : 0);
                    $obj->nombres = $lis->nombres_completos;
                    $obj->dni = $lis->dni;
                    $obj->cargo = $lis->cargo;
                    $obj->regimen = $lis->condicion;
                    $obj->faltas = $lis->FALTAS;
                    $obj->tardanza = $lis->TARDANZAS;
                    $obj->lic_sg = $lis->SLSG;
                    $obj->sancion_dici = $lis->Sancion_Dici;
                    $obj->lic_sindical = $lis->Licencia_Sindical;
                    $obj->descanso_med = $lis->DESCANSO_MEDICO;
                    $obj->min_permiso = $lis->MINPERMISO;
                    $obj->comision = $lis->comision;
                    $obj->citacion = $lis->CITACION;
                    $obj->essalud = $lis->ESSALUD;
                    $obj->permiso = $lis->PERMISO;
                    $obj->compensatorio = $lis->COMPENSATORIO;
                    $obj->onomastico = $lis->ONOMASTICO;
                    $obj->fecha_asistencia = $fecha_i;

                    $obj->estado = 1;
                    $obj->usuario_created_at =0;
                    $obj->save();

                    DB::commit();
                }

                $fecha_iaux= date("Y-m-d", strtotime($fecha_faux ."+1 days"));
                $fecha_faux= date("Y-m-d", strtotime($fecha_iaux ."+".$dias." days"));
            }

            // Actualiza campo "persona_id" en base al "id" de la tabla "personas".            
            $sql = "UPDATE sw_asistencias_temp sa
                        INNER JOIN personas p ON sa.dni=p.dni
                        SET sa.persona_id = p.id;";
            DB::update($sql);
            // --

            // Actualiza campo "persona_id" en base que no tenga asociado un "id" de la tabla "personas". 
            DB::table('sw_asistencias_temp')
                ->whereNull('persona_id')
                ->update(array('persona_id' => 1272));
            // --      

            $sql = "SELECT sw.*,
                        ca.cant_act,
                        doc.docu,
                        tt.total_tramites,
                        t.tareas
                        FROM (
                            SELECT a.foto, a.area, a.nombres, a.dni, a.cargo, a.regimen,
                                                SUM(a.faltas) faltas, SUM(a.tardanza) tardanza, SUM(a.lic_sg) lic_sg, SUM(a.sancion_dici) sancion_dici,
                                                SUM(a.lic_sindical) lic_sindical, SUM(a.descanso_med) descanso_med, SUM(a.min_permiso) min_permiso, SUM(a.comision) comision,
                                                SUM(a.citacion) citacion, SUM(a.essalud) essalud, SUM(a.permiso) permiso, SUM(a.compensatorio) compensatorio,
                                                SUM(a.onomastico) onomastico, a.usuario_created_at,a.persona_id
                                FROM sw_asistencias_temp a
                                WHERE NOT a.dni = '07135876'
                                AND a.usuario_created_at = '0'
                                GROUP BY a.area, a.nombres, a.dni, a.cargo
                        ) sw
                        LEFT JOIN (SELECT COUNT(rdv.id) tareas, rdv.usuario_updated_at persona_id
                                FROM rutas_detalle_verbo rdv
                                WHERE rdv.finalizo=1 
                                AND rdv.updated_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                GROUP BY rdv.usuario_updated_at
                        ) AS t ON t.persona_id=sw.persona_id
                        LEFT JOIN (SELECT ROUND((SUM(ap.ot_tiempo_transcurrido) / 60), 2) cant_act, ap.persona_id
                                FROM actividad_personal ap
                                WHERE ap.persona_id=ap.usuario_created_at
                                AND ap.fecha_inicio BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                GROUP BY ap.persona_id
                            ) AS ca ON ca.persona_id=sw.persona_id
                        LEFT JOIN (SELECT COUNT(r.id) total_tramites, r.usuario_created_at persona_id
                                FROM rutas r
                                WHERE r.created_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                AND r.estado=1
                                GROUP BY r.usuario_created_at
                            ) AS tt ON tt.persona_id=sw.persona_id
                        LEFT JOIN (SELECT COUNT(dd.id) docu, dd.usuario_created_at persona_id
                                FROM doc_digital_temporal dd
                                WHERE dd.created_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                AND dd.estado = 1
                                GROUP BY dd.usuario_created_at
                            ) AS doc ON doc.persona_id=sw.persona_id; ";

            $lis = DB::select($sql);

            return Response::json(
                        array(
                            'rst'=>1,
                            'reporte'=> $lis
                        )
                    );

            die();

        /*}
        catch (\Exception $e) 
        {
            DB::rollback();
            return Response::json(
                        array(
                            'rst'=>2,
                            'reporte'=> 'not_data'
                        )
                    );
        }*/
    }

    public function postAreasadm()
    {
        $result = file_get_contents("http://www.muniindependencia.gob.pe/spersonal/consul.php?opcion=area");

        return utf8_encode($result);
    }


    // -- METODO PARA GENERAR EXCEL
    public function getExportreportepersonal()
    {
          AuditoriaAcceso::getAuditoria();
          $fecha_i = Input::get('fecha_ini');
          $fecha_f = Input::get('fecha_fin');

          $sql = "SELECT sw.*,
                        ca.cant_act,
                        doc.docu,
                        tt.total_tramites,
                        t.tareas
                        FROM (
                            SELECT a.foto, a.area, a.nombres, a.dni, a.cargo, a.regimen,
                                                SUM(a.faltas) faltas, SUM(a.tardanza) tardanza, SUM(a.lic_sg) lic_sg, SUM(a.sancion_dici) sancion_dici,
                                                SUM(a.lic_sindical) lic_sindical, SUM(a.descanso_med) descanso_med, SUM(a.min_permiso) min_permiso, SUM(a.comision) comision,
                                                SUM(a.citacion) citacion, SUM(a.essalud) essalud, SUM(a.permiso) permiso, SUM(a.compensatorio) compensatorio,
                                                SUM(a.onomastico) onomastico, a.usuario_created_at,a.persona_id
                                FROM sw_asistencias a
                                WHERE NOT a.dni = '07135876'
                                AND a.usuario_created_at = '".Auth::user()->id."'
                                GROUP BY a.area, a.nombres, a.dni, a.cargo
                        ) sw
                        LEFT JOIN (SELECT COUNT(rdv.id) tareas, rdv.usuario_updated_at persona_id
                                FROM rutas_detalle_verbo rdv
                                WHERE rdv.finalizo=1 
                                AND rdv.updated_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                GROUP BY rdv.usuario_updated_at
                        ) AS t ON t.persona_id=sw.persona_id
                        LEFT JOIN (SELECT ROUND((SUM(ap.ot_tiempo_transcurrido) / 60), 2) cant_act, ap.persona_id
                                FROM actividad_personal ap
                                WHERE ap.persona_id=ap.usuario_created_at
                                AND ap.fecha_inicio BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                GROUP BY ap.persona_id
                            ) AS ca ON ca.persona_id=sw.persona_id
                        LEFT JOIN (SELECT COUNT(r.id) total_tramites, r.usuario_created_at persona_id
                                FROM rutas r
                                WHERE r.created_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                AND r.estado=1
                                GROUP BY r.usuario_created_at
                            ) AS tt ON tt.persona_id=sw.persona_id
                        LEFT JOIN (SELECT COUNT(dd.id) docu, dd.usuario_created_at persona_id
                                FROM doc_digital_temporal dd
                                WHERE dd.created_at BETWEEN '$fecha_i 00:00:00' AND '$fecha_f 23:59:59'
                                AND dd.estado = 1
                                GROUP BY dd.usuario_created_at
                            ) AS doc ON doc.persona_id=sw.persona_id; ";

            $result = DB::select($sql);

          /*style*/
          $styleThinBlackBorderAllborders = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('argb' => 'FF000000'),
                  ),
              ),
              'font'    => array(
                  'bold'      => true
              ),
              'alignment' => array(
                  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              )
          );
          $styleAlignmentBold= array(
              'font'    => array(
                  'bold'      => true
              ),
              'alignment' => array(
                  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              ),
          );
          $styleAlignment= array(
              'alignment' => array(
                  'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              ),
          );
          /*end style*/

            /*export*/
              /* instanciar phpExcel!*/
              
              $objPHPExcel = new PHPExcel();

              /*configure*/
              $objPHPExcel->getProperties()->setCreator("Gerencia Modernizacion")
                 ->setSubject("Asistencias de Personal");

              $objPHPExcel->getDefaultStyle()->getFont()->setName('Bookman Old Style');
              $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
              /*end configure*/

              /*head*/
              $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue('A3', 'N°')
                          ->setCellValue('B3', 'AREA')
                          ->setCellValue('C3', 'NOMBRES')
                          ->setCellValue('D3', 'DNI')
                          ->setCellValue('E3', 'CARGO')
                          ->setCellValue('F3', 'REGIMEN')
                          ->setCellValue('G3', 'FALTAS')
                          ->setCellValue('H3', 'TARDE')
                          ->setCellValue('I3', 'LIC. S.G')
                          ->setCellValue('J3', 'SANCION DICI')
                          ->setCellValue('K3', 'LIC. SINDICAL')
                          ->setCellValue('L3', 'DCSO. MED')
                          ->setCellValue('M3', 'MIN. PERM')
                          ->setCellValue('N3', 'COMISION')
                          ->setCellValue('O3', 'CITACION')
                          ->setCellValue('P3', 'ES-SALUD')
                          ->setCellValue('Q3', 'PERM')
                          ->setCellValue('R3', 'COMPEM')
                          ->setCellValue('S3', 'ONOMAS')
                          ->setCellValue('T3', 'C. ACT')
                          ->setCellValue('U3', 'TAREA')
                          ->setCellValue('V3', 'T. TRAMI')
                          ->setCellValue('W3', 'DOC')
                    ->mergeCells('A1:W1')
                    ->setCellValue('A1', 'LISTADO ASISTENCIAS DE PERSONALES')
                    ->getStyle('A1:W1')->getFont()->setSize(18);

              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('T')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('U')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('V')->setAutoSize(true);
              $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('W')->setAutoSize(true);
              /*end head*/
              /*body*/
              if($result){
                $ini = 4;
                foreach ($result as $key => $value) {

                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . $ini, $key + 1)
                                ->setCellValue('B' . $ini, $value->area)
                                ->setCellValue('C' . $ini, $value->nombres)
                                ->setCellValue('D' . $ini, $value->dni)
                                ->setCellValue('E' . $ini, $value->cargo)
                                ->setCellValue('F' . $ini, $value->regimen)
                                ->setCellValue('G' . $ini, $value->faltas)
                                ->setCellValue('H' . $ini, $value->tardanza)
                                ->setCellValue('I' . $ini, $value->lic_sg)
                                ->setCellValue('J' . $ini, $value->sancion_dici)
                                ->setCellValue('K' . $ini, $value->lic_sindical)
                                ->setCellValue('L' . $ini, $value->descanso_med)
                                ->setCellValue('M' . $ini, $value->min_permiso)
                                ->setCellValue('N' . $ini, $value->comision)
                                ->setCellValue('O' . $ini, $value->citacion)
                                ->setCellValue('P' . $ini, $value->essalud)
                                ->setCellValue('Q' . $ini, $value->permiso)
                                ->setCellValue('R' . $ini, $value->compensatorio)
                                ->setCellValue('S' . $ini, $value->onomastico)
                                ->setCellValue('T' . $ini, $value->cant_act)
                                ->setCellValue('U' . $ini, $value->docu)
                                ->setCellValue('V' . $ini, $value->total_tramites)
                                ->setCellValue('W' . $ini, $value->tareas)
                                ;
                    $ini++;
                }
                
              }
              /*end body*/
              $objPHPExcel->getActiveSheet()->getStyle('A3:W3')->applyFromArray($styleThinBlackBorderAllborders);
              $objPHPExcel->getActiveSheet()->getStyle('A1:W1')->applyFromArray($styleAlignment);
              // Rename worksheet
              $objPHPExcel->getActiveSheet()->setTitle('Asistencias');
              // Set active sheet index to the first sheet, so Excel opens this as the first sheet
              $objPHPExcel->setActiveSheetIndex(0);
              // Redirect output to a client’s web browser (Excel5)
              header('Content-Type: application/vnd.ms-excel');
              header('Content-Disposition: attachment;filename="reporteasper.xls"'); // file name of excel
              header('Cache-Control: max-age=0');
              // If you're serving to IE 9, then the following may be needed
              header('Cache-Control: max-age=1');
              // If you're serving to IE over SSL, then the following may be needed
              header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
              header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
              header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
              header ('Pragma: public'); // HTTP/1.0
              $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
              $objWriter->save('php://output');
              exit;
            /* end export*/
    }
    // --

}
