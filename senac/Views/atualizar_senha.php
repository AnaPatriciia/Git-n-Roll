<?php
require_once '../Database/Database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = $_POST['telefone'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';

    if (!empty($telefone) && !empty($nova_senha)) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $db = new Database();
        $query = "UPDATE usuarios SET senha = :senha WHERE telefone = :telefone";
        $params = [
            ':senha' => $senha_hash,
            ':telefone' => $telefone
        ];

        $result = $db->execute($query, $params);

        if ($result) {
            echo "<script>alert('Senha alterada com sucesso!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Erro ao alterar senha.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Preencha todos os campos.'); window.history.back();</script>";
    }
}
?>
