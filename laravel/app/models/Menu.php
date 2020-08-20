<?php

class Menu extends Base
{
    public $table = "menus";
    public static $where =['id', 'nombre', 'ruta', 'class_icono', 'estado'];
    public static $selec =['id', 'nombre', 'ruta', 'class_icono', 'estado'];
    /**
     * Opciones relationship
     */
    public function opciones()
    {
        return $this->hasMany('Opcion');
    }

    public static function fileToFile($file, $url)
    {
        $urld=explode("/",$url);
        $urlt=array();
        for ($i=0; $i < (count($urld)-1) ; $i++) { 
            array_push($urlt, $urld[$i]);
            $urltexto=implode("/",$urlt);
            if ( !is_dir($urltexto) ) {
                mkdir($urltexto,0777);
            }
        }
        
        list($type, $file) = explode(';', $file);
        list(, $type) = explode('/', $type);
        if ($type=='jpeg') $type='jpg';
        if ($type=='x-icon') $type='ico';
        if (strpos($type,'document')!==False) $type='docx';
        if (strpos($type,'msword')!==False) $type='doc';
        if (strpos($type,'presentation')!==False) $type='pptx';
        if (strpos($type,'powerpoint')!==False) $type='ppt';
        if (strpos($type, 'sheet') !== False) $type='xlsx';
        if (strpos($type, 'excel') !== False) $type='xls';
        if (strpos($type, 'pdf') !== False) $type='pdf';
        if ($type=='plain') $type='txt';
        list(, $file)      = explode(',', $file);
        $file = base64_decode($file);
        file_put_contents($url , $file);
        return $url.$type;
    }

}
