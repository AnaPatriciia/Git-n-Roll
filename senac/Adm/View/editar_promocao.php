<?php
require_once '../../Model/Promocoes.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID da promoção não fornecido.";
    exit;
}

$id = (int)$_GET['id'];

$promo = new Promocoes();
$dados = $promo->buscarPorId($id);

if (!$dados) {
    echo "Promoção não encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Editar Promoção</title>
    <link rel="stylesheet" href="../public/Css/editar-promocao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
 
  <div class="editar-promocao-container">
     <h1>Editar Promoção</h1>
    <form method="POST" action="salvar_edicao.php" enctype="multipart/form-data">
      <input type="hidden" name="id_promocao" value="<?= htmlspecialchars($dados['id_promocao']) ?>">

      <label>Título:</label>
      <input type="text" name="promocao_titulo" value="<?= htmlspecialchars($dados['promocao_titulo']) ?>" required>

      <label>Subtítulo:</label>
      <input type="text" name="promocao_subtitulo" value="<?= htmlspecialchars($dados['promocao_subtitulo']) ?>" required>

      <label>Imagem:</label>
      <div class="file-upload">
        <i class="fa-solid fa-image"></i>
        <input type="file" name="imagem_promocao">
      </div>

      <button type="submit" name="salvar">Salvar alterações</button>
       <button class="logout-btn" onclick="location.href='gerenciamento-adm.php'">Voltar</button>
      
    </form>
  </div>
</body>

</html>
