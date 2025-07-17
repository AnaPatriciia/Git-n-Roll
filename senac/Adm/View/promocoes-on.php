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
        isset($_POST['promocao_subtitulo'])
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
        $caminho_relativo = 'uploads/' . $novo_nome . '.' . $extensao;

        $upload = move_uploaded_file($arquivo['tmp_name'], $caminho_completo);
        if (!$upload) {
            die('Erro ao salvar a imagem.');
        }

        $promo = new Promocoes();
        $promo->imagem_promocao = $caminho_relativo;
        $promo->promocao_titulo = $_POST['promocao_titulo'];
        $promo->promocao_subtitulo = $_POST['promocao_subtitulo'];

        // Valores padrão para status e tipo
        $promo->status_produto = 0; // por exemplo, cadastrar sempre como ativa
        $promo->tipo_promocao = 0;  // por exemplo, cadastrar sempre como semanal

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
  <script>
  // Verifica se a URL não tem hash (#)
  if (!location.hash) {
    location.hash = "#promoAtivas";
  }
</script>
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

    #imagem-promo{
      width: 60px;
      
    }

    
.btn-editar {
  padding: 6px 12px;
  background-color: gray;
  color: white;
  border-radius: 5px;
  text-decoration: none;
  font-weight: bold;
}

  </style>
</head>
<body>
<div class="admin">
  <div class="admin-header">
    <img src="../../public/Imagens/logo_club.png" alt="Logo">
    <button class="logout-btn" onclick="location.href='login-adm.php'">Sair</button>
  </div>
  <nav class="admin-nav">
    <a href="gerenciamento.php">Clientes Ativos</a>
    <a href="Clientes-inativos.php">Clientes Inativos</a>
    <a href="promocoes-on.php" class="active">Promoções On</a>
    <a href="promocoes-off.php">Promoções Off</a>
  </nav>

  <div class="admin-body">
               
    <section id="promoAtivas" class="pane">
      <h2>Promoções Ativas</h2>
      <br>
      <form method="POST" enctype="multipart/form-data" style="margin-bottom: 40px;">
        <div class="promo-card-upload">
          <div class="upload-label">Cadastrar Nova Promoção</div>
          <label class="fake-upload">
            <i class="fas fa-upload"></i> Imagem
            <input type="file" name="imagem_promocao" accept="image/*" required>
          </label>
          <input type="text" name="promocao_titulo" placeholder="Título da promoção" required>
          <input type="text" name="promocao_subtitulo" placeholder="Subtítulo da promoção" required>
          <!-- <select name="status_produto" required>
            <option value="1">Ativa</option>
            <option value="0">Inativa</option>
          </select>
          <select name="tipo_promocao" required>
            <option value="0">Semanal</option>
            <option value="1">Sazonal</option>
          </select> -->
          <button type="submit" name="cadastrar" class="btn-salvar">Salvar</button>
        </div>
      </form>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th div="imagem-promo">Imagem</th>
            <th>Título</th>
            <th>Subtítulo</th>
            <th>Editar</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
         <?php
$promocoes = Promocoes::buscarAtivas();

if (!empty($promocoes)) {
    foreach ($promocoes as $promo) {
        $tipo = $promo['tipo_promocao'] == 0 ? 'Semanal' : 'Sazonal';
        $status = $promo['status_produto'] == 1 ? 'Ativa' : 'Inativa';

        echo "<tr>";
        echo "<td>{$promo['id_promocao']}</td>";

        // Caminho ajustado para refletir a estrutura correta
        $caminho_imagem = "/senac/senac/Adm/public/" . $promo['imagem_promocao'];
        echo "<td><img src='{$caminho_imagem}' width='80'></td>";

        echo "<td>{$promo['promocao_titulo']}</td>";
        echo "<td>{$promo['promocao_subtitulo']}</td>";
       echo "<td>
        <a href='editar_promocao.php?id={$promo['id_promocao']}' class='btn-editar'>Editar</a>
      </td>";

        echo "<td>
                <form method='POST' action='inativar_promocao.php' onsubmit='return confirm(\"Deseja inativar essa promoção?\");'>
                    <input type='hidden' name='id_promocao' value='{$promo['id_promocao']}'>
                    <button type='submit' class='btn inativar'>Inativar</button>
                </form>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Nenhuma promoção ativa encontrada.</td></tr>";
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