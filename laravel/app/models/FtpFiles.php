<?php

class FtpFiles extends Base
{
    public $table = "archivos_ftp";


	public static function getVideoLink($file){



        preg_replace("/[^A-Za-z0-9\ ]/", "",trim($file));

        $sql = "SELECT * FROM archivos_ftp WHERE link LIKE '$file'";
        //echo $sql;

        $r= DB::select($sql);

        return $r;
        
    }

}
