<?php
require_once '../../Database/Database.php';
require_once '../../Model/Promocoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_promocao'])) {
    $id = (int) $_POST['id_promocao'];

   $db = new Database('promocoes');
   $db->updatePromo(['status_produto' => 1], 'id_promocao = ' . $id);


    header('Location: gerenciamento.php#promoAtivas');
    exit;
}
?>
