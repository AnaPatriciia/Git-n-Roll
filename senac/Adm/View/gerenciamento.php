<?php
require_once '../../Database/Database.php';
require_once '../../Model/Login.php';
require_once '../../Model/Cliente.php';
require_once '../../Model/Adm.php';
require_once '../../Model/Promocoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    if (
        isset($_FILES['imagem_promocao']) &&
        isset($_POST['promocao_titulo']) &&
        isset($_POST['promocao_subtitulo']) &&
        isset($_POST['status_produto']) &&
        isset($_POST['tipo_promocao'])
    ) {
        // Obtém as informações do arquivo enviado via formulário, no campo 'imagem_promocao
        $arquivo = $_FILES['imagem_promocao'];
        if ($arquivo['error']) {
            die("Falha ao enviar a foto.");
        }
        // Define o caminho absoluto para a pasta onde as imagens serão salvas
        $pasta = realpath(__DIR__ . '/../public/uploads') . '/';
        // Verifica se a pasta existe. Se não existir, cria com permissões 0777 (total acesso)
        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);// `true` permite criar diretórios pai que não existam
        }

        $nome_foto = $arquivo['name'];
        $novo_nome = uniqid();
        $extensao = strtolower(pathinfo($nome_foto, PATHINFO_EXTENSION));
        // Verifica se a extensão é permitida (somente formatos de imagem específicos)
        if (!in_array($extensao, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp'])) {
            die("Falha ao enviar a foto: formato inválido.");
        }

        $caminho_completo = $pasta . $novo_nome . '.' . $extensao;
        $caminho_relativo = 'public/uploads/' . $novo_nome . '.' . $extensao;

        $upload = move_uploaded_file($arquivo['tmp_name'], $caminho_completo);
        if (!$upload) {
            die('Erro ao salvar a imagem.');
        }

        $promo = new Promocoes();
        $promo->imagem_promocao = $caminho_relativo;
        $promo->promocao_titulo = $_POST['promocao_titulo'];
        $promo->promocao_subtitulo = $_POST['promocao_subtitulo'];
        $promo->status_produto = (int) $_POST['status_produto'];
        $promo->tipo_promocao = (int) $_POST['tipo_promocao'];

        if ($promo->cadastrar()) {
            echo "<script>alert('Promoção cadastrada com sucesso!');</script>";
        } else {
            echo "<p style='color:red;'>Erro ao cadastrar promoção.</p>";
        }
    } else {
        echo "<p style='color:red;'>Preencha todos os campos corretamente.</p>";
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
        <tbody>
          <?php
          $db = new Database();
          $sql = "SELECT u.id_usuario, u.telefone, c.recompensa FROM usuarios u INNER JOIN checkin_diario c ON u.id_usuario = c.id_usuario WHERE u.ativo = 1";
          $clientes = $db->execute($sql)->fetchAll(PDO::FETCH_ASSOC);

          if (!empty($clientes)) {
            foreach ($clientes as $cliente) {
              echo "<tr>";
              echo "<td>{$cliente['id_usuario']}</td>";
              echo "<td>{$cliente['telefone']}</td>";
              echo "<td>" . ($cliente['recompensa'] ?? 'Sem Recompensa') . "</td>";
              echo "<td><button class='btn inativar'>Inativar</button></td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='4'>Nenhum cliente ativo encontrado.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section id="promoAtivas" class="pane">
      <h2>Promoções Ativas</h2>
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

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Título</th>
            <th>Subtítulo</th>
            <th>Tipo</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $promocoes = Promocoes::buscarAtivas();
          if (!empty($promocoes)) {
            foreach ($promocoes as $promo) {
              $tipoTexto = $promo['tipo_promocao'] == 0 ? 'Semanal' : 'Sazonal';
              $statusTexto = $promo['status_produto'] == 1 ? 'Ativa' : 'Inativa';
              echo "<tr>";
              echo "<td>{$promo['id_promocao']}</td>";
              echo "<td><img src='../../{$promo['imagem_promocao']}'></td>";
              echo "<td>{$promo['promocao_titulo']}</td>";
              echo "<td>{$promo['promocao_subtitulo']}</td>";
              echo "<td>{$tipoTexto}</td>";
              echo "<td>{$statusTexto}</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='6'>Nenhuma promoção ativa encontrada.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
              <!-- promoções inativas -->
     <section id="promoInativas" class="pane">
      <h2>Promoções Inativas</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Imagem</th>
            <th>Título</th>
            <th>Subtítulo</th>
            <th>Tipo</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        <?php
            $promocoes = Promocoes::buscarInativas(); // agora correto
            if (!empty($promocoes)) {
              foreach ($promocoes as $promo) {
                $tipoTexto = $promo['tipo_promocao'] == 0 ? 'Semanal' : 'Sazonal';
                $statusTexto = $promo['status_produto'] == 1 ? 'Ativa' : 'Inativa';
                echo "<tr>";
                echo "<td>{$promo['id_promocao']}</td>";
                echo "<td><img src='../../{$promo['imagem_promocao']}'></td>";
                echo "<td>{$promo['promocao_titulo']}</td>";
                echo "<td>{$promo['promocao_subtitulo']}</td>";
                echo "<td>{$tipoTexto}</td>";
                echo "<td>{$statusTexto}</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='6'>Nenhuma promoção inativa encontrada.</td></tr>";
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