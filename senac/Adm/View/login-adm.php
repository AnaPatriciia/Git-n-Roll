<?php

require_once '../../Model/Login.php';
require_once '../../Model/Cliente.php';
require_once '../..//Model/Adm.php';


require '../../Session/Login.php';



session_start();

$erro = '';
$succes = '';

echo "<pre>";
var_dump($_POST);
echo "</pre>";

if (isset($_POST['logar'])) {

    if (!empty($_POST['telefone']) && !empty($_POST['senha'])) {
        $telefone = $_POST['telefone'];
        $senha    = $_POST['senha'];

        /* 1. Buscar usuário na tabela usuarios */
        $usuario = User::getUsuarioByTelefone($telefone);

        if (!$usuario) {
            die('Usuário não encontrado na tabela usuarios.');
        }

        /* 2. Verificar se o perfil é ADM */
        if ($usuario->id_perfil !== 'adm') {
            die('Este usuário não possui perfil de administrador.');
        }

        /* 3. Verificar senha */
        if ($senha != $usuario->senha) {
            die('Senha incorreta para administrador.');
        }

        /* 4. Realizar login */
        Login::loginAdm($usuario);  

        exit;

    } else {
        die('Telefone ou senha não preenchidos.');
    }
}

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../public/Css/login-adm.css">
  <title>Login ADM</title>
</head>
<body class="login-adm-body">
  <div class="login-adm">
    <div class="login-adm-header">
      <img src="../../public/Imagens/logo_club.png" alt="Logo">
    </div>
    <div class="login-adm-body">
      <h2>Login Administrativo</h2>
      <form action="login-adm.php" method="post">
         <input type="tel" name='telefone' id="telefone-login" class="form__field" placeholder="Matrícula" required>
        <input type="password" name='senha' id="senha-login"  class="form__field" placeholder="Senha" required>
        <button class="botoes-acesso" name="logar" value="Entrar" type="submit">Entrar</button>
      </form>
      <!-- <div class="login-adm-esqueci">
        <a href="esqueci-senha.html">Esqueci minha senha</a>
      </div> -->
    </div>
    <div class="login-adm-footer">
      Você é Cliente? <a href="../../Views/login.php">Acesse aqui</a>
    </div>
  </div>
</body>
</html>
