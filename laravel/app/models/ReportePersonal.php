<?php

class ReportePersonal extends \Eloquent {

    protected $fillable = [];
    public $table = "sw_asistencias";

    public function __construct($tbl="sw_asistencias"){
    	$this->table=$tbl;
    }
    

}
