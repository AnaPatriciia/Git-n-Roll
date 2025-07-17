<?php

// require '../../App/config.inc.php';

require_once '../../senac/Model/Login.php';
require_once '../../senac/Model/Cliente.php';


require '../../senac/Session/Login.php';

// Login::RequireLogout();

session_start();

$erro = '';
$succes = '';



if (isset($_POST['logar'])) {
    if (!empty($_POST['telefone']) && !empty($_POST['senha'])) {
        $telefone = $_POST['telefone'];
        $senha = $_POST['senha'];

        // Validação do formato do telefone
        if (!preg_match('/^\(?\d{2}\)?[\s-]?\d{4,5}-?\d{4}$/', $telefone)) {
            $erro = 'Telefone não é válido';
        } else {
            $usuario = User::getUsuarioByTelefone($telefone); // Atualize essa função no seu model

            if ($usuario) {
                $idUsuario = $usuario->id_usuario;

                if ($usuario->id_perfil == 'adm' && $senha == 'adm') {
                    $adm = Adm::getAdmByUsuarioId($idUsuario);
                    if ($adm) {
                        $adm->id_usuario = $idUsuario;
                        $adm->telefone = $usuario->telefone;
                        Login::loginAdm($adm);
                        exit;
                    }
                }

                if ($senha == $usuario->senha) {
                    $cliente = Cliente::getClienteByUsuarioId($idUsuario);
                    if ($cliente) {
                        $cliente->telefone = $usuario->telefone;
                        Login::loginCLiente($cliente);
                        echo '<script>alert("Login bem-sucedido!")</script>';
                        exit;
                    }

                    if ($usuario->id_perfil == 'adm') {
                        $adm = Adm::getAdmByUsuarioId($idUsuario);
                        if ($adm) {
                            $adm->id_usuario = $usuario->id_usuario;
                            $_SESSION["id_usuario"] = $idUsuario;
                            Login::loginAdm($adm);
                            exit;
                        }
                    }

                    $erro = 'Usuário não possui perfil (adm ou cliente).';
                } else {
                    $erro = 'Telefone ou senha incorretos.';
                }
            } else {
                $erro = 'Usuário não encontrado.';
            }
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}







?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../public/Css/login.css">
  <title>Login</title>
</head>
<body-login>
  <div class="login">
    <div class="login-header">
      <img src="../public/Imagens/logo_club.png" alt="Logo">
    </div>
    <div class="login-body">
         <img class="onboarding_login" src="../public/Imagens/onboarding_login.png" alt="">
      <h2>Login</h2>
      <form action="login.php" method="post">
        <input type="tel" name='telefone' id="telefone-login" class="form__field" placeholder="Telefone" required>
        <input type="password" name='senha' id="senha-login"  class="form__field" placeholder="Senha" required>
        <label for="senha" class="form__label">Senha*</label>
       <button id="botoes-acesso" name="logar" value="Entrar" type="submit">Entrar</button>

      <div class="esqueci-senha">
      <a href="esqueci-senha.php">Esqueci minha Senha</a><br>
      <br>
      <a href="../Adm/View/login-adm.php">Acesso Corporativo</a>

    </div>
    
    </div>
    <div class="login-footer">
      Não tem conta? <a href="cadastro.php">Cadastre-se</a>
    </div>
  </div>
</body>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input');
    const botaoLogin = document.getElementById('botoes-acesso');

    inputs.forEach(input => {
      input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
          event.preventDefault(); // impede comportamento padrão
          botaoLogin.click(); // simula clique no botão de login
        }
      });
    });
  });
</script>
</html>
