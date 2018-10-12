<?php

class DocumentoRecuperado extends Base{
    public $table = "doc_recuperado";

    public static function getRecuperadosArea($area_id){
        $ppath=url('/').'/';
        $sql = "SELECT id, tipo_doc, '$ppath' as dir, archivo,fecha_doc,numero FROM doc_recuperado WHERE estado=1 AND area=".$area_id.' ORDER BY fecha_doc DESC';
        $r= DB::select($sql);
        return $r;

    }

    public static function getRecuperados(){
        $ppath=url('/').'/';
        $sql = "SELECT id, tipo_doc, '$ppath' as dir, archivo,fecha_doc,numero FROM doc_recuperado WHERE estado=1".' ORDER BY fecha_doc DESC';
        $r= DB::select($sql);
        return $r;
        
    }



/*
    public function opciones(){
        return $this->belongsToMany('Opcion');
    }

    P@ssw0rd..*2018
*/
}
