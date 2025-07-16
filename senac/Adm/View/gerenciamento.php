<?php
require_once '../../Database/Database.php';
require_once '../../Model/Login.php';
require_once '../../Model/Cliente.php';
require_once '../../Model/Adm.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
  $tipo = $_POST['tipo_promocao'] ?? '';

  // upload e validação da imagem (pode criar uma função para reutilizar)

  if (
      isset($_FILES['imagem_promocao']) &&
      isset($_POST['promocao_titulo']) &&
      isset($_POST['promocao_subtitulo']) &&
      isset($_POST['status_produto'])
  ) {
      $arquivo = $_FILES['imagem_promocao'];
      if ($arquivo['error']) {
          die("Falha ao enviar a foto.");
      }

      $pasta = realpath(__DIR__ . '/../public/uploads') . '/';
      if (!is_dir($pasta)) {
          mkdir($pasta, 0777, true);
      }

      $nome_foto = $arquivo['name'];
      $novo_nome = uniqid();
      $extensao = strtolower(pathinfo($nome_foto, PATHINFO_EXTENSION));

      if (!in_array($extensao, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'])) {
          die("Falha ao enviar a foto: formato inválido.");
      }

      $caminho_completo = $pasta . $novo_nome . '.' . $extensao;
      $caminho_relativo = 'public/uploads/' . $novo_nome . '.' . $extensao;

      $upload = move_uploaded_file($arquivo['tmp_name'], $caminho_completo);
      if (!$upload) {
          die('Erro ao salvar a imagem.');
      }

      // Agora criar objeto promocao de acordo com o tipo
      $tipo = $_POST['tipo_promocao'] ?? null;
      if ($tipo === '0') {
          $promo = new PromocoesSemanal();
      } elseif ($tipo === '1') {
          $promo = new PromocoesSazonal();
      } else {
          die('Tipo de promoção inválido.');
      }

      $promo->imagem_promocao = $caminho_relativo;
      $promo->promocao_titulo = $_POST['promocao_titulo'];
      $promo->promocao_subtitulo = $_POST['promocao_subtitulo'];
      $promo->status_produto = (int) $_POST['status_produto'];

      $result = $promo->cadastrar();

      if ($result) {
          echo '<script>alert("Promoção cadastrada com sucesso!");</script>';
      } else {
          echo '<p style="color:red;">Erro ao cadastrar a promoção.</p>';
      }
  } else {
      echo '<p style="color:red;">Preencha todos os campos corretamente.</p>';
  }
}

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
    /* Esconde todas as abas */
    .pane {
      display: none;
    }
    /* Mostra a aba cujo id bate com o hash da URL */
    .pane:target {
      display: block;
    }
    /* Se não tiver hash na URL, mostra a aba ativos por padrão */
    body:not(:target) #ativos {
      display: block;
    }
    /* Estiliza o menu com links */
    .admin-nav a {
      padding: 10px 15px;
      display: inline-block;
      text-decoration: none;
      color: #333;
      border-bottom: 2px solid transparent;
      margin-right: 10px;
    }
    /* Link ativo (quando o href bate com hash da URL) */
    .admin-nav a:focus,
    .admin-nav a:hover,
    .admin-nav a.active {
      border-color: #007BFF;
      color: #007BFF;
      font-weight: bold;
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
      <button class="logout-btn" onclick="location.href='login.html'">Sair</button>
    </div>

    <nav class="admin-nav">
      <a href="gerenciamento.php" class="active">Clientes Ativos</a>
      <a href="Clientes-inativos.php">Clientes Inativos</a>
      <a href="#promoAtivas">Promoções Ativas</a>
      <a href="#promoInativas">Promoções Inativas</a>
      <section id="promoAtivas" class="pane">
        <h2 style="margin-bottom:12px">Promoções Ativas</h2>

        <form method="POST" enctype="multipart/form-data" style="margin-bottom: 40px;">
          <div class="promo-card-upload">
            <div class="upload-label">Cadastrar Nova Promoção</div>

            <label class="fake-upload">
              <i class="fas fa-upload"></i> Imagem
              <input type="file" name="imagem_promocao" accept="image/*" required>
            </label>

            <input type="text" name="promocao_titulo" placeholder="Título da promoção" required>
            <input type="text" name="promocao_subtitulo" placeholder="Subtítulo da promoção" required>

            <select name="status_produto" required>
              <option value="1">Ativa</option>
              <option value="0">Inativa</option>
            </select>
            <select name="tipo_promocao" required>
              <option value="0">Semanal</option>
              <option value="1">Sazonal</option>
            </select>

            <button type="submit" name="cadastrar" class="btn-salvar">Salvar</button>
          </div>
      </form>
      </section>
    </nav>

    <div class="admin-body">
      <section id="ativos" class="pane">
        <h2 style="margin-bottom:12px">Clientes Ativos</h2>
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
      <section>
   

    </div>

    <div class="admin-footer">Gerenciamento Club Buy At Home | Desenvolvido por Git'n'Roll</div>
  </div>
</body>
</html>
