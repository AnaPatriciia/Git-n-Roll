	<?php


clASs Database{

    public $conection;
    public string $local = 'localhost';
    public string $db = 'sistema_usuarios';
    public string $user = 'root';
    public string $password = 'senac';
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

    public function select_perfil($id_cli){

        // COM FIELDS NA FUNÇÃO SELECT COMO PARAMENTRO = "$fields = '*'
        $query = 'SELECT usuario.nome,cliente.sobrenome,usuario.email,cliente.cpf,usuario.foto_perfil,cliente.cep,cliente.telefone,cliente.numero_casa,cliente.rua,cliente.bairro,cliente.cidade,cliente.estado, usuario.id_usuario
        from cliente inner join usuario
        on usuario.id_usuario = cliente.id_usuario
        and cliente.id_cliente = '.$id_cli;

        return $this->execute($query);
        
    }

    public function select_pedido(){
        $query = "SELECT pedido.id_pedido AS ID, sacola.valor_total AS Valor, pedido.tipo AS Tipo,  cliente.estado AS UF FROM pedido 
        JOIN sacola ON pedido.sacola_id_sacola = sacola.id_sacola
        JOIN cliente ON sacola.cliente_id_cliente = cliente.id_cliente";

        return $result = $this->execute($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select_by_id($id){
        $query = "SELECT 
                    produto.nome AS nome_produto, 
                    produto.quantidade AS quantidade, 
                    produto.cor AS cor, 
                    produto.imagem AS imagem,
                    sacola.valor_total AS valor_total, 
                    pedido.codigo_rastreio AS rastreio, 
                    pedido.status_pedido AS status_pedido,
                    cliente.telefone AS contato, 
                    cliente.cep AS cep, 
                    cliente.rua AS rua, 
                    cliente.numero_casa AS numero, 
                    cliente.bairro AS bairro,
                    cliente.cidade AS cidade, 
                    cliente.estado AS estado, 
                    usuario.nome AS nome_cliente, 
                    cliente.sobrenome AS sobrenome
                FROM produto 
                JOIN sacola ON produto.id_produto = sacola.produto_id_produto 
                JOIN pedido ON sacola.produto_id_produto = pedido.sacola_produto_id_produto 
                JOIN cliente ON pedido.sacola_cliente_id_cliente = cliente.id_cliente 
                JOIN usuario ON cliente.id_usuario = usuario.id_usuario
                WHERE pedido.id_pedido = ?";
    
        $stmt = $this->execute($query, [$id]);
    
        return $stmt;
    }



public function selectWithBinds($where = null, $binds = [], $order = null, $limit = null, $fields = '*') {
    $whereClause = strlen($where) ? 'WHERE ' . $where : '';
    $orderClause = strlen($order) ? 'ORDER BY ' . $order : '';
    $limitClause = strlen($limit) ? 'LIMIT ' . $limit : '';

    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' ' . $whereClause . ' ' . $orderClause . ' ' . $limitClause;

    return $this->execute($query, $binds);
}

}


?>

