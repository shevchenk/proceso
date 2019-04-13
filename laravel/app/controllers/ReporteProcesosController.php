<?php
class ReporteProcesosController extends \BaseController
{


    public function postProcesosarea(){ 
        ini_set('memory_limit', '512M');
        $fechaIni = (Input::has('fecha_ini') ? Input::get('fecha_ini') : '');
        $fechaFin = (Input::has('fecha_fin') ? Input::get('fecha_fin') : '');
        $areas = (Input::has('area_id') ? Input::get('area_id') : '');

        $areas = implode(",", $areas);

        $re = ReporteProceso::getReporteProceso($areas,$fechaIni,$fechaFin);
        return Response::json(
                array(
                    'rst'   => '1',
                    'msj'   => 'Procesos cargados',
                    'datos' => $re
                )
            );
    }


    public function postTramitesarea(){ 
        ini_set('memory_limit', '512M');
        $fechaIni = (Input::has('fecha_ini') ? Input::get('fecha_ini') : '');
        $fechaFin = (Input::has('fecha_fin') ? Input::get('fecha_fin') : '');
        $areas = (Input::has('area_id') ? Input::get('area_id') : '');

        $areas = implode(",", $areas);

        $re = ReporteProceso::getReporteTramites($areas,$fechaIni,$fechaFin);
        return Response::json(
                array(
                    'rst'   => '1',
                    'msj'   => 'Tramites cargados',
                    'datos' => $re
                )
            );
    }


    public function postTramitesdocumento(){
        ini_set('memory_limit', '512M');
        $docName = (Input::has('docName') ? Input::get('docName') : '');
        if($docName!=""){
            $re = ReporteProceso::getReporteProcesoDetalle($docName);
            return Response::json(
                    array(
                        'rst'   => '1',
                        'msj'   => 'Tramite cargados',
                        'datos' => $re
                    )
                );

        }else{
            return Response::json(
                    array(
                        'rst'   => '0',
                        'msj'   => 'Indique nombre del documento'
                    )
                );

        }
    }


}
