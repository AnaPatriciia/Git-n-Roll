<?php
require_once __DIR__ . '/../Model/Promocoes.php';
;

$dados_produto = new Promocoes();
$produto_banco = $dados_produto->buscar();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo</title>
   <link rel="stylesheet" href="../public/Css/promocoes-style.css">
</head>
<body-promocoes>
    <div class="promocoes-container">

  <h2 class="promocoes-titulo">Cada dia temático é uma oferta especial!</h2>


  <a href="../Views/visualizacao_catalogo1.html" class="promocoes-link-card">
  <div class="promocoes-card promocoes-card-2">
    <img src="../public/Imagens/catalogo-teste-01.jpg" alt="Produto 2" class="promocoes-img">
    <div class="promocoes-info">
      <h3 class="promocoes-nome">Hoje é dia da Pizza!</h3>
      <p class="promocoes-descricao">Aproveite ofertas especiais pra comemorar esse dia!</p>
    </div>
  </div>
</a>

<a href="../Views/visualizacao_catalogo1.html" class="promocoes-link-card">
  <div class="promocoes-card promocoes-card-3">
    <img src="../public/Imagens/catalogo-teste-01.jpg" alt="Produto 3" class="promocoes-img">
    <div class="promocoes-info">
      <h3 class="promocoes-nome">Amanhã é dia do hamburguer!</h3>
      <p class="promocoes-descricao">Tudo pra você matar a vontade de um burgão hoje!</p>
    </div>
  </div>
</a>

 <a href="../Views/visualizacao_catalogo1.html" class="promocoes-link-card">
  <div class="promocoes-card promocoes-card-4">
    <img src="../public/Imagens/catalogo-teste-01.jpg" alt="Produto 4" class="promocoes-img">
    <div class="promocoes-info">
      <h3 class="promocoes-nome">Em breve dia do café!</h3>
      <p class="promocoes-descricao">Aguarde em breve promoções especiais em nossas unidades!</p>
    </div>
  </div>
<div class="voltar-container">
  <button class="voltar-btn" >
    <i class="fa-solid fa-arrow-left"></i>Voltar
  </button>
</div>
</body>
</html>

