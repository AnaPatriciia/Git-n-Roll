<?php
require_once __DIR__ . '/../Database/Database.php';

class Promocoes {
    public ?int $id_promocao = null;
    public string $imagem_promocao;
    public string $promocao_titulo;
    public string $promocao_subtitulo; 
    public int $status_produto;
    public function cadastrar() {
        $sql = "INSERT INTO promocoes 
            (imagem_promocao, promocao_titulo, promocao_subtitulo, status_produto, tipo_promocao) 
            VALUES 
            (:imagem, :titulo, :subtitulo, :status, :tipo)";
    
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindValue(':imagem', $this->imagem_promocao);
        $stmt->bindValue(':titulo', $this->promocao_titulo);
        $stmt->bindValue(':subtitulo', $this->promocao_subtitulo);
        $stmt->bindValue(':status', $this->status_produto);
        $stmt->bindValue(':tipo', $this->tipo_promocao);
        
        return $stmt->execute();
    }
    

    public function atualizar() {
        if (!$this->id_promocao) {
            throw new Exception("ID da promoção é obrigatório para atualizar.");
        }
        return (new Database('promocoes'))->update([
            'imagem_promocao' => $this->imagem_promocao,
            'promocao_titulo' => $this->promocao_titulo,
            'promocao_subtitulo' => $this->promocao_subtitulo,
            'status_produto' => $this->status_produto,
            'tipo_promocao' => $this->tipo_promocao,
        ], 'id_promocao = ' . $this->id_promocao);
    }

    public static function buscar($where = null, $order = null, $limit = null) {
        $condicaoBase = 'status_produto = 1';
        if ($where) {
            $condicaoBase .= ' AND ' . $where;
        }
        return (new Database('promocoes'))->select($condicaoBase, $order, $limit)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>