<?php



require_once '../../senac/Model/Login.php';
require_once '../../senac/Model/Cliente.php';


require '../../senac/Session/Login.php';

Login::RequireLogout();


$erro = '';
$succes ='';

if(isset($_POST['cadastrar'])){


    if(!empty($_POST['telefone']) || !empty($_POST['senha'])){

        $telefone  = $_POST['telefone'];
        $senha  = $_POST['senha'];
                $cliente =  new Cliente();

                $cliente->telefone = $telefone;
                $cliente->senha = $senha;
                $cliente->id_perfil = "cli";



                $cliente->cadastrarCliente();

                if($cliente){
                    $succes='Cadastro realizado com successo';
                }else{
                    $erro='Erro ao cadastrar';
                }
    };
}




?> 


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../public/Css/cadastro.css">
  <title>Cadastro</title>
</head>
<body class="body-cadastro">
  <div class="cadastro">
    <div class="cadastro-header">
      <img src="../public/Imagens/logo_club.png" alt="Logo">
    </div>
    <div class="cadastro-body">
      <h2>Cadastro</h2>
      <form method="post">
        <input type="tel" name='telefone' id="telefone-login" class="form__field" placeholder="Telefone" required>
         <input autocomplete="off" type="password" name="senha" id="senha-cad" class="form__field" placeholder="Senha" required>
        <input type="password" placeholder="Confirmar Senha">
        <button name="cadastrar" class="botoes-acesso">Cadastrar</button>
      </form>
      <div class="tenho-conta">
        <a href="login.html">Já tenho uma conta</a>
      </div>
    </div>
    <div class="cadastro-footer">
      Ao se cadastrar, você aceita os <a href="#">termos de uso</a>.
    </div>
  </div>
</body>

</html>
