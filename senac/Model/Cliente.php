<?php

require_once(__DIR__ . '/../Database/Database.php');


class Cliente{

    public int $id_usuario;
    public int $telefone;
    public string $senha;
    public string $id_perfil;
   

    public function cadastrarCliente(){


        $db = new Database('usuarios');
        $res_id = $db->insert_LastId(
            [
                
                'telefone' => $this->telefone,
                'senha' => $this->senha,
                'id_perfil' => $this->id_perfil
            ]
        );
               
        return $res_id;
    }



    public static function getClienteByUsuarioId($id_usuario) {
    $db = new Database('usuarios');
    $result = $db->select("id_usuario = {$id_usuario}");
    return $result->fetchObject(self::class);
}


    
    public static function getCliente($where=null, $order =null, $limit = null){
        return (new Database('usuarios'))->select($where,$order,$limit)
                                        ->fetchAll(PDO::FETCH_CLASS,self::class);

    }



    public static function getUsuarioPorEmail($where=null, $order =null, $limit = null){

        return (new Database('usuarios'))->select('telefone = "'. $where .'"')->fetchObject(self::class);

    }


    public function atualizarCliente() {
        // Atualizar tabela 'usuario'
        $db = new Database('usuarios');
        $resUsuario = $db->update('usuario = ' . $this->id_usuario, [
            'telefone' => $this->telefone,
            'senha' => $this->senha,
        ]);
    
       
    
        return ($resUsuario);
    }
    
}

