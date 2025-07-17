<?php
require_once '../../Database/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_promocao'])) {
    $id = (int) $_POST['id_promocao'];

    $db = new Database('promocoes');

    // Aqui usamos a função updatePromo que você já tem, que atualiza status_produto para 1
    $resultado = $db->updatePromo(['status_produto' => 0], 'id_promocao = ' . $id);

    if ($resultado) {
        // Redireciona para a página de gerenciamento mostrando as promoções inativas (ou ativas)
        header('Location: gerenciamento.php#promoInativas');
        exit;
    } else {
        echo "Erro ao ativar a promoção.";
    }
} else {
    echo "Requisição inválida.";
}
