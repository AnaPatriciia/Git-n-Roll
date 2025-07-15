<?php

require_once(__DIR__ . '/../Database/Database.php');

// require_once 'User.php';


class Adm{

    public int $id_usuario;
    

    
    
    public static function getAdmByUsuarioId($id_usuario) {
        $db = new Database('administrador');
        $result = $db->select("id_usuario = $id_usuario");
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

    // public function updateAdm() {
    //     // Atualiza a tabela 'usuario'
    //     $dbUsuario = new Database('usuario');
    //     $resUsuario = $dbUsuario->update(
    //         'id_usuario = ' . $this->id_usuario,    // ← corrigido aqui
    //         [
    //             'nome'        => $this->nome,
    //             'email'       => $this->email,
    //             'senha'       => $this->senha,
    //             'foto_perfil' => $this->foto_perfil
    //         ]
    //     );
    
    //     // Atualiza a tabela 'administrador' (se você realmente tiver foto_perfil lá)
    //     $dbAdministrador = new Database('administrador');
    //     $resAdministrador = $dbAdministrador->update(
    //         'id_administrador = ' . $this->id_administrador,
    //         ['foto_perfil' => $this->foto_perfil]
    //     );
    
    //     return $resUsuario && $resAdministrador;
    // }
    
    
    
}



