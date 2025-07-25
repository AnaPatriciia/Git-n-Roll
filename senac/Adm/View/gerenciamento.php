<?php
require_once '../../Database/Database.php';
require_once '../../Model/Login.php';
require_once '../../Model/Cliente.php';
require_once '../../Model/Adm.php';
require_once '../../Model/Promocoes.php';


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="../public/Css/gerenciamento.css">
  <style>
    .pane { display: none; }
    .pane:target { display: block; }
    body:not(:target) #ativos { display: block; }
    .admin-nav a {
      padding: 10px 15px;
      display: inline-block;
      text-decoration: none;
      color: #333;
      border-bottom: 2px solid transparent;
      margin-right: 10px;
    }
    .admin-nav a:focus,
    .admin-nav a:hover,
    .admin-nav a.active {
      border-color: #007BFF;
      color: #007BFF;
      font-weight: bold;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }
    img {
      width: 80px;
      height: auto;
    }
  </style>
   <script>
   // Função chamada ao clicar no botão "Inativar"
function inativar(button) {
  
  const idUsuario = button.closest('tr').querySelector('td').textContent;  

  console.log("ID do usuário a ser inativado: ", idUsuario);  


  const formData = new FormData();
  formData.append('id_usuario', idUsuario);  

  
  fetch('atualizar_cliente.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      
      button.textContent = 'Desativado';
      button.classList.remove('inativar');
      button.classList.add('ativar');
      button.setAttribute('onclick', 'reativar(this)');  
    } else {
      alert('Ocorreu um erro ao atualizar o status do cliente.');
    }
  })
  .catch(error => {
    console.error('Erro:', error);
    alert('Erro na requisição AJAX.');
  });
}

  </script>
</head>
<body>
<div class="admin">
  <div class="admin-header">
    <img src="../../public/Imagens/logo_club.png" alt="Logo">
    <button class="logout-btn" onclick="location.href='login-adm.php'">Sair</button>
  </div>
  <nav class="admin-nav">
    <a href="gerenciamento.php" class="active">Clientes Ativos</a>
    <a href="Clientes-inativos.php">Clientes Inativos</a>
    <a href="promocoes-on.php">Promoções On</a>
    <a href="promocoes-off.php">Promoções Off</a>
  </nav>

  <div class="admin-body">
    <section id="ativos" class="pane">
      <h2>Clientes Ativos</h2>
      
      <table>
        <thead>
          <tr>
            <th>Id Cliente</th>
            <th>Telefone</th>
            <th>Recompensas</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody id="tbAtivos">
          <?php
            function listarClientesAtivos() {
              $db = new Database();
              $sql = "
              SELECT u.id_usuario, u.telefone, c.recompensa
              FROM usuarios u
              INNER JOIN checkin_diario c ON u.id_usuario = c.id_usuario
              WHERE u.ativo = 1";
              $result = $db->execute($sql);
              return $result->fetchAll(PDO::FETCH_ASSOC);
            }

            $clientes = listarClientesAtivos();

            if (!empty($clientes)) {
              foreach ($clientes as $cliente) {
                echo "<tr>";
                echo "<td>{$cliente['id_usuario']}</td>";
                echo "<td>{$cliente['telefone']}</td>";
                echo "<td>" . ($cliente['recompensa'] ?? 'Sem Recompensa') . "</td>";
                
                echo "<td><button class='btn inativar' onclick='inativar(this)'>Inativar</button></td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='4'>Nenhum cliente ativo encontrado.</td></tr>";
            }
            ?>

        </tbody>
      </table>
    </section>

  </div>
  <div class="admin-footer">Gerenciamento Club Buy At Home | Desenvolvido por Git'n'Roll</div>
</div>
</body>
</html>