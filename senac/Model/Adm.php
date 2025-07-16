<?php

require_once(__DIR__ . '/../Database/Database.php');

require_once 'Cliente.php';
require_once 'Login.php';


class Adm{

    public int $id_administrador;

    

    
    public static function getAdmByUsuarioId($id_administrador) {
        $db = new Database('administrador');
        $result = $db->select("id_administrador = $id_administrador");
        return $result->fetchObject(self::class);
    }

    public static function getAdm($where=null, $order =null, $limit = null){
        return (new Database('administrador'))->select($where,$order,$limit)
                                        ->fetchAll(PDO::FETCH_CLASS,self::class);

    }

    public static function getAdmById($id_adm) {
        $db = new Database('administrador');
        $result = $db->select("id_administrador = $id_adm");
        return $result->fetchObject(self::class);
    }


    

    
}



