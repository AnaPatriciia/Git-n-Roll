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
  <script>
  // Verifica se a URL não tem hash (#)
  if (!location.hash) {
    location.hash = "#promoInativas";
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
    <a href="promocoes-on.php" >Promoções On</a>
    <a href="promocoes-off.php"class="active">Promoções Off</a>
  </nav>

  <div class="admin-body">
               
  
                 <!-- promoções inativas -->
     <section id="promoInativas" class="pane">
      <h2>Promoções Inativas</h2>
      <br>
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
$promocoesInativas = Promocoes::buscar('status_produto = 1', 'id_promocao DESC');

if (!empty($promocoesInativas)) {
    foreach ($promocoesInativas as $promo) {
        $tipoTexto = $promo['tipo_promocao'] == 0 ? 'Semanal' : 'Sazonal';
        $statusTexto = $promo['status_produto'] == 1 ? 'Ativa' : 'Inativa';

        echo "<tr>";
        echo "<td>{$promo['id_promocao']}</td>";

        // ✅ Caminho corrigido conforme estrutura de diretórios
        $caminho_imagem = "/senac/senac/Adm/public/" . $promo['imagem_promocao'];
        echo "<td><img src='{$caminho_imagem}' alt='Imagem Promoção' width='80'></td>";

        echo "<td>{$promo['promocao_titulo']}</td>";
        echo "<td>{$promo['promocao_subtitulo']}</td>";
        echo "<td>{$tipoTexto}</td>";
        echo "<td>
                <form method='POST' action='ativar_promocao.php' onsubmit='return confirm(\"Deseja ativar essa promoção?\");'>
                    <input type='hidden' name='id_promocao' value='{$promo['id_promocao']}'>
                    <button type='submit' class='btn ativar'>Ativar</button>
                </form>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Nenhuma promoção inativa encontrada.</td></tr>";
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