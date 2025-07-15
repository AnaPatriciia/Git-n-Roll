<?php
require_once __DIR__ . '/../Database/Database.php';


class Promocoes{

    public ?int $id_promocao = null;
    public string $id_usuario ;
    public string $promocao_semanal ;
    public int $promocao_sazonal ;
    public string $status_produto;
   

    public function cadastrar(){
        $db = new Database('promocoes');
        $result =  $db->insert(
                            [
                            'id_promocao' => $this->id_promocao,    
                            'id_usuario ' => $this->id_usuario ,
                            'promocao_semanal ' => $this->promocao_semanal ,
                            'promocao_sazonal ' => $this->promocao_sazonal ,                           
                            'status_produto' => $this->status_produto,

                            ]
                        );
        
        if($result) {
            return true;
        }
        else{
            return false;
        }
    }

    public function atualizar(){
            return (new Database('promocoes'))->update([
                'id_promocao' => $this->id_promocao, 
                'id_usuario ' => $this->id_usuario ,
                'promocao_semanal ' => $this->promocao_semanal ,
                'promocao_sazonal ' => $this->promocao_sazonal ,                
                'status_produto' => $this->status_produto,
                
            ],'id_promocao ='.$this->id_promocao );
    }

    // public static function buscar($where=null,$order=null,$limit=null){
    //     //FETCHALL
    //     return (new Database('promocoes'))->select()->fetchAll(PDO::FETCH_ASSOC);
    // }

    // essa função está fazendo um select no banco apenas dos produtos ativos
    public static function buscar($where = null, $order = null, $limit = null) {
        // Adiciona a condição de status_produto = 1 ao where
        $condicaoBase = 'status_produto = 1';
    
        // Se já houver uma condição passada pelo usuário, concatena com AND
        if ($where) {
            $condicaoBase .= ' AND ' . $where;
        }
    
       
        return (new Database('promocoes'))->select($condicaoBase, $order, $limit)->fetchAll(PDO::FETCH_ASSOC);
    }
}