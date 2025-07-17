<?php
require_once '../../Model/Promocoes.php';

// Verifica se o formulário foi enviado corretamente
if (!isset($_POST['id_promocao']) || empty($_POST['id_promocao'])) {
    echo "ID da promoção não fornecido.";
    exit;
}

$id = (int) $_POST['id_promocao'];
$titulo = $_POST['promocao_titulo'];
$subtitulo = $_POST['promocao_subtitulo'];
$status = $_POST['status_produto'];
$tipo = $_POST['tipo_promocao'];

$promo = new Promocoes();
$dados = $promo->buscarPorId($id);

if (!$dados) {
    echo "Promoção não encontrada.";
    exit;
}

// Atualiza os dados
// Atualiza os dados
$promo->id_promocao = $id;
$promo->promocao_titulo = $titulo;
$promo->promocao_subtitulo = $subtitulo;
$promo->status_produto = $status ?? 0;
$promo->tipo_promocao = $tipo ?? 1;

// Mantém a imagem atual como padrão
$promo->imagem_promocao = $dados['imagem_promocao'];

// Substitui somente se uma nova imagem for enviada
if (!empty($_FILES['imagem_promocao']['name'])) {
    $arquivo = $_FILES['imagem_promocao'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
        die("Formato de imagem inválido.");
    }

    $novo_nome = uniqid() . "." . $extensao;
    $pasta = realpath(__DIR__ . '/../public/uploads') . '/';
    $caminho_completo = $pasta . $novo_nome;
    $caminho_relativo = 'uploads/' . $novo_nome;

    if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
        $promo->imagem_promocao = $caminho_relativo;
    } else {
        die("Erro ao fazer upload da nova imagem.");
    }
}

if ($promo->atualizar()) {
    echo "<script>alert('Promoção atualizada com sucesso!'); window.location.href='promocoes-on.php';</script>";
} else {
    echo "Erro ao atualizar promoção.";
}
?>
