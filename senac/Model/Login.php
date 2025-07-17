<?php


require_once(__DIR__ . '/../Database/Database.php');




class User{


    public int $id_usuario;
    public int $telefone;
    public string $senha;
    public string $id_perfil;
   



  
    // public function cadastrarUser(){
    //     $db = new Database('usuarios');

    //     $result = $db->insert(
    //         [
    //             'usuario'=> $this->id_usuario,
    //             'telefone'=> $this->telefone,
    //             'senha' => $this->senha,
    //             'id_perfil' => $this->id_perfil,
    //         ]
    //         );
    //     return $result;
    // }

    public function cadastrarUser() {
    $db = new Database('usuarios');

    // Cria o hash seguro da senha
    $senhaHash = password_hash($this->senha, PASSWORD_DEFAULT);

    // Insere os dados no banco
    $result = $db->insert([
        'usuario'    => $this->id_usuario,
        'telefone'   => $this->telefone,
        'senha'      => $senhaHash, // usa o hash aqui
        'id_perfil'  => $this->id_perfil,
    ]);

    return $result;
}

    
    //Função que lista dados da table de clientes do banco de dados
  public static function getUsuarioByTelefone($telefone) {
    $db = new Database('usuarios');
    $result = $db->select("telefone = '$telefone'");

    // Garantir que temos um resultado antes de tentar o fetch
    if ($result) {
        $usuario = $result->fetchObject(self::class);  // Retorna o objeto do tipo User
        return $usuario ?: false;  // Se não encontrou, retorna false
    }

    return false;  // Caso a consulta falhe ou não retorne resultados
}






    public static function getUsuarioById($id_usuario) {
        $db = new Database('usuarios');
        $result = $db->select("id_usuario = '$id_usuario'");
        return $result->fetchObject(self::class); 
    }

    public static function getUser($where=null, $order =null, $limit = null){
        return (new Database('user'))->select($where,$order,$limit)
                                        ->fetchAll(PDO::FETCH_CLASS,self::class);

    }




    public function updateUser(){
        return (new Database('usuarios'))->update('usuario = '.$this->id_usuario,[
                                            'usuario'=> $this->id_usuario,
                                            'telefone' => $this->telefone,
                                            'senha' => $this->senha,
                                            'id_perfil' => $this->id_perfil,
        ]);
        
    }

}


