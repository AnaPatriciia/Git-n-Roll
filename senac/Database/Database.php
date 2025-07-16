<?php


class Database{

    public $conection;
    public string $local = 'localhost';
    public string $db = 'sistema_usuarios';
    public string $user = 'root';
    public string $password = '';
    public $table;




    public function __construct($table = null) {
        $this->table = $table;
        $this->conecta();
    }

        
    // Função conectar com o banco de dados

    private function conecta(){
        try{
            $this->conection = new PDO("mysql:host=".$this->local.";dbname=$this->db",
            $this->user,$this->password); 
            $this->conection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return true;
        }catch (PDOException $err){
            die("Connection Failed" . $err->getMessage());
            return false;
        }
    }

    // Função para excutar uma função do banco de dados

    public function execute($query,$binds = []){
        //BINDS = parametros
        try{
            $stmt = $this->conection->prepare($query);
            $stmt->execute($binds);
            return $stmt;
        }catch (PDOException $err) {
            //retirar msg em produção
            die("Connection Failed " . $err->getMessage());

        }

    }

    // Função para inserir dados no banco de dados

    public function insert($values){
        $fields = array_keys($values);
        $binds = array_pad([],count($fields),'?');

        $query = 'INSERT INTO ' . $this->table .'  (' .implode(',',$fields). ') VALUES (' .implode(',',$binds).')';


        // echo $query ;
        // print_r( array_values($values));
        // die();


        $result = $this->execute($query,array_values($values));

        if($result){
            return true;
        }
        else{
            return false;
        }

        
    }

    public function insert_LastId($values){
        $fields = array_keys($values);
        $binds = array_pad([],count($fields),'?');

        $query = 'INSERT INTO ' . $this->table .'  (' .implode(',',$fields). ') VALUES (' .implode(',',$binds).')';


        $res = $this->execute($query, array_values($values));   

        $lastId = $this->conection->lastInsertId();  

        if($res){
            return $lastId;
        }
        else{
            return false;
        }
        
    }


    // Função para listar dados do banco de dados
    
   public function select($where = null, $order = null, $limit = null, $fields = '*') {
    $where = strlen($where) ? 'WHERE ' . $where : '';
    $order = strlen($order) ? 'ORDER BY ' . $order : '';
    $limit = strlen($limit) ? 'LIMIT ' . $limit : '';

    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' ' . $where . ' ' . $order . ' ' . $limit;

    return $this->execute($query);
}




    // Função para deletar dados do banco de dados
    public function delete($where){
        $query = 'DELETE FROM'.$this->table.'WHERE'.$where;

        $this->execute($query);

        return true;

        // Monta a cláusula WHERE se fornecida
        $where = strlen($where) ? 'WHERE '.$where : '';

        // Monta a query de DELETE
        $query = 'DELETE FROM '.$this->table.' '.$where;

        // Executa a query
        return $this->execute($query);
    }


    // Função para editar a dados do banco de dados

    public function update($where, $values){
        $fields = array_keys($values);
        $query = 'UPDATE '.$this->table.' SET '.implode('=?,',$fields).' =? WHERE '.$where;
    
        return $this->execute($query,array_values($values));
    }

 



public function selectWithBinds($where = null, $binds = [], $order = null, $limit = null, $fields = '*') {
    $whereClause = strlen($where) ? 'WHERE ' . $where : '';
    $orderClause = strlen($order) ? 'ORDER BY ' . $order : '';
    $limitClause = strlen($limit) ? 'LIMIT ' . $limit : '';

    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' ' . $whereClause . ' ' . $orderClause . ' ' . $limitClause;

    return $this->execute($query, $binds);
}

function listarClientesAtivos() {
    // Cria instância da classe Database (não precisa passar a tabela, pois faremos JOIN)
    $db = new Database();

    // Query SQL para buscar id_usuario e recompensa
    $sql = "
        SELECT u.id_usuario, c.recompensa
        FROM usuarios u
        INNER JOIN checkin_diario c ON u.id_usuario = c.id_usuario
        WHERE u.ativo = 1
    ";

    // Executa a query usando o método da classe
    $result = $db->execute($sql);

    // Retorna todos os resultados como array associativo
    return $result->fetchAll(PDO::FETCH_ASSOC);
}


}


?>

