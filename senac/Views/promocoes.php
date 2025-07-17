<?php
require_once '../Model/Promocoes.php';

$promocoes = Promocoes::buscarAtivas();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo</title>
  <link rel="stylesheet" href="../public/Css/promocoes-style.css">
</head>
<body class="body-promocoes">
  <div class="promocoes-container">
    <h2 class="promocoes-titulo">Catálogo de Promoções</h2>

    <?php if (!empty($promocoes)): ?>
      <?php foreach ($promocoes as $promo): ?>
        <?php
          $caminho_imagem = "/senac/senac/Adm/public/" . $promo['imagem_promocao'];
        ?>
        <div class="promocoes-card">
          <img 
          src="<?= htmlspecialchars($caminho_imagem) ?>" 
          alt="Imagem Promoção" 
          class="promocoes-img thumbnail" 
          data-full="<?= htmlspecialchars($caminho_imagem) ?>"
          style="cursor:pointer;"
        >
          <div class="promocoes-info">
            <h3 class="promocoes-nome"><?= htmlspecialchars($promo['promocao_titulo']) ?></h3>
            <p class="promocoes-descricao"><?= htmlspecialchars($promo['promocao_subtitulo']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nenhuma promoção ativa encontrada.</p>
    <?php endif; ?>

    <div class="voltar-container">
      <a href="buyathome.php" class="botoes-acesso">
        <i class="fa-solid fa-arrow-left"></i> Voltar
      </a>
    </div>
  </div>
</body>
<!-- Modal para imagem ampliada -->
<div id="modalImagem" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="imgModal">
  <div id="caption"></div>
</div>

<style>
  /* Estilo modal */
  .modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    padding-top: 60px; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.8);
  }

  .modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 80vh;
    border-radius: 8px;
  }

  #caption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
  }

  .close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
  }

  .close:hover,
  .close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
  }
   .botoes-acesso {
      display: block;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: black;
      color: white;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
      transition: 0.25s;
      margin-bottom: 10px;
    }

    .botoes-acesso:hover {
      background-color: orangered;
      filter: brightness(1.1);
    }
    
</style>

<script>
  // Pega o modal e seus elementos
  const modal = document.getElementById("modalImagem");
  const modalImg = document.getElementById("imgModal");
  const captionText = document.getElementById("caption");
  const closeBtn = document.querySelector(".close");

  // Pega todas as miniaturas
  const thumbnails = document.querySelectorAll('.thumbnail');

  thumbnails.forEach(img => {
    img.addEventListener('click', () => {
      modal.style.display = "block";
      modalImg.src = img.dataset.full;
      captionText.textContent = img.alt;
    });
  });

  // Fecha o modal ao clicar no X
  closeBtn.onclick = function() {
    modal.style.display = "none";
  }

  // Fecha o modal ao clicar fora da imagem
  modal.onclick = function(e) {
    if(e.target === modal) {
      modal.style.display = "none";
    }
  }
</script>

</html>
