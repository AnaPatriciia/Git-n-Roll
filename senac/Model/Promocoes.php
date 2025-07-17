<?php
require_once __DIR__ . '/../Database/Database.php';

class Promocoes {
    private Database $db;

    public ?int $id_promocao = null;
    public string $imagem_promocao = '';
    public string $promocao_titulo = '';
    public string $promocao_subtitulo = '';
    public int $status_produto = 1; // 1 = ativo, 0 = inativo
    public int $tipo_promocao = 0;  // 0 = semanal, 1 = sazonal

    public function __construct() {
        $this->db = new Database('promocoes');
    }

public function cadastrar() {
    $values = [
        'imagem_promocao'      => $this->imagem_promocao,
        'promocao_titulo'      => $this->promocao_titulo,
        'promocao_subtitulo'   => $this->promocao_subtitulo,
        'status_produto'       => $this->status_produto ?? 1, // força ativo
        'tipo_promocao'        => $this->tipo_promocao ?? 1   // opcional
    ];

    return $this->db->insert($values);
}

    public function atualizar() {
        if (!$this->id_promocao) {
            throw new Exception("ID da promoção é obrigatório para atualizar.");
        }

        $values = [
            'imagem_promocao' => $this->imagem_promocao,
            'promocao_titulo' => $this->promocao_titulo,
            'promocao_subtitulo' => $this->promocao_subtitulo,
            'status_produto' => $this->status_produto,
            'tipo_promocao' => $this->tipo_promocao
        ];

        return $this->db->update('id_promocao = ' . $this->id_promocao, $values);
    }

    public function editar($id_promocao) {
    $values = [
        'imagem_promocao'      => $this->imagem_promocao,
        'promocao_titulo'      => $this->promocao_titulo,
        'promocao_subtitulo'   => $this->promocao_subtitulo,
        'status_produto'       => $this->status_produto ?? 1,
        'tipo_promocao'        => $this->tipo_promocao ?? 1,
    ];

    $where = "id_promocao = :id";
    $bind = ['id' => $id_promocao];

    return $this->db->update($values, $where, $bind);
}

    public static function buscar($where = null, $order = null, $limit = null) {
        $db = new Database('promocoes');
        return $db->select($where, $order, $limit)->fetchAll(PDO::FETCH_ASSOC);
    }
   // Buscar somente promoções ativas
    public static function buscarAtivas(): array {
        return self::buscar('status_produto = 0', 'id_promocao DESC');
    }
    // Buscar somente promoções inativas
    public static function buscarInativas(): array {
        return self::buscar('status_produto = 1', 'id_promocao DESC');
    }
public function buscarPorId($id)
{
    $query = "SELECT * FROM promocoes WHERE id_promocao = :id";
    $params = ['id' => $id];
    $stmt = $this->db->execute($query, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
}
