<?php
require_once '../Database/Database.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = $_POST['telefone'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';

    if (!empty($telefone) && !empty($nova_senha)) {
        // hash da nova senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $db = new Database(); // use a sua instância do banco
        $sql = "UPDATE usuarios SET senha = :senha WHERE telefone = :telefone";
        $params = [':senha' => $senha_hash, ':telefone' => $telefone];

        if ($db->execute($sql, $params)) {
            echo "Senha atualizada com sucesso.";
            // redirecionar se quiser
        } else {
            echo "Erro ao atualizar a senha.";
        }
    } else {
        echo "Preencha todos os campos.";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../public/Css/esqueci-senha.css" />
  <title>Recuperar Senha</title>
</head>
<body class="body-reset">
  <div class="reset">
    <div class="reset-header">
      <img src="../public/Imagens/logo_club.png" alt="Logo">
    </div>
    <div class="reset-body">
      <h2>Recuperar Senha</h2>
      <form method="post" action="atualizar_senha.php">
      <input type="tel" name="telefone" placeholder="Celular cadastrado" required>
      <input type="password" name="nova_senha" placeholder="Nova Senha" required>
      <button type="submit" class="botoes-acesso">Alterar</button>
    </form>
      <div class="voltar-login">
        <a href="login.php">Voltar para o login</a>
      </div>
    </div>
    <div class="reset-footer">
      Em breve você receberá um link para redefinir sua senha.
    </div>
  </div>
</body>
</html>
