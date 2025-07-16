<?php
require_once __DIR__ . '/../Database/Database.php';

class Promocoes {
    public ?int $id_promocao = null;
    public string $imagem_promocao;
    public string $promocao_titulo;
    public string $promocao_subtitulo; 
    public int $status_produto;
    public function cadastrar() {
        $db = new Database('promocoes');
        $result = $db->insert([
            'imagem_promocao' => $this->imagem_promocao,
            'promocao_titulo' => $this->promocao_titulo,
            'promocao_subtitulo' => $this->promocao_subtitulo,
            'status_produto' => $this->status_produto
        ]);
        return $result ? true : false;
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
