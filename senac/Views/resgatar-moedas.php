
<?php
session_start(); 
require_once '../../senac/Database/Database.php';
require_once '../../senac/Session/Login.php';
require_once '../../senac/Model/Cliente.php';

$db = new Database();

Login::init(); // inicia a sessão, se ainda não tiver sido iniciada
$id_usuario = $_SESSION['usuarios']['id_usuario'] ?? null;

if (!$id_usuario) {
    // Redireciona se não estiver logado corretamente
    header("Location: login.php");
    exit;
}

$moedas = $db->buscarMoedasPorUsuario($id_usuario);

$sql = "SELECT SUM(recompensa) AS total FROM checkin_diario WHERE id_usuario = :id";
$params = [':id' => $id_usuario];

$result = $db->selectCustomQuery($sql, $params);
$total = $result[0]['total'] ?? 0;

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Resgatar Pontos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --cor-fundo: #0d0d0d;
      --cor-laranja: #ff6b00;
      --cor-texto: #ffffff;
      --cor-cinza: #444;
      --radius: 16px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-color: var(--cor-fundo);
      color: var(--cor-texto);
      padding: 16px;
    }

    .resgate-container {
      max-width: 420px;
      margin: 0 auto;
      background-color: #1a1a1a;
      border-radius: var(--radius);
      padding: 24px 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    .resgate-header {
      text-align: center;
      margin-bottom: 24px;
    }

    .resgate-header h2 {
      font-size: 22px;
      margin-bottom: 10px;
      color: var(--cor-laranja);
    }

    .moedas {
      font-size: 18px;
      font-weight: bold;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      color: var(--cor-texto);
    }

    .moedas i {
      color: gold;
    }

    .resgatar-btn {
      width: 100%;
      margin: 24px 0 20px;
      padding: 14px;
      background-color: var(--cor-laranja);
      color: #000;
      border: none;
      border-radius: var(--radius);
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .resgatar-btn:hover {
      filter: brightness(1.1);
    }

    .cupons {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .cupom {
      background-color: #262626;
      border-radius: var(--radius);
      padding: 20px;
      text-align: center;
      border: 2px solid #333;
      transition: 0.3s ease;
      opacity: 0.4;
      cursor: not-allowed;
      position: relative;
    }

    .cupom.desbloqueado {
      border-color: var(--cor-laranja);
      opacity: 1;
      cursor: pointer;
    }

    .cupom:hover.desbloqueado {
      background-color: #333;
    }

    .cupom h3 {
      font-size: 22px;
      margin-bottom: 6px;
      color: var(--cor-laranja);
    }

    .cupom span {
      font-size: 13px;
      color: #bbb;
    }

    .cupom i {
      font-size: 30px;
      margin-bottom: 10px;
      color: var(--cor-laranja);
      display: block;
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

    @media (max-width: 480px) {
      .resgate-container {
        padding: 20px 16px;
      }

      .cupom h3 {
        font-size: 20px;
      }

      .resgatar-btn {
        font-size: 15px;
      }
      
    }
  </style>
</head>
<body>
  <div class="resgate-container">
    <div class="resgate-header">
      <h2><i class="fa-solid fa-gift"></i> Resgatar Pontos</h2>
      <div class="moedas"><i class="fa-solid fa-coins"></i> <span id="moeda-count"><?= htmlspecialchars($moedas) ?></span> moedas</div>


    </div>

    <button class="resgatar-btn" id="btn-resgatar"><i class="fa-solid fa-arrow-rotate-right"></i> Resgatar moedas</button>

    <div class="cupons">
      <div class="cupom" data-requisito="50">
        <i class="fa-solid fa-ticket"></i>
        <h3>5% OFF</h3>
        <span>Requer 50 moedas</span>
      </div>
      <div class="cupom" data-requisito="100">
        <i class="fa-solid fa-ticket"></i>
        <h3>10% OFF</h3>
        <span>Requer 100 moedas</span>
      </div>
      <div class="cupom" data-requisito="150">
        <i class="fa-solid fa-ticket"></i>
        <h3>15% OFF</h3>
        <span>Requer 150 moedas</span>
      </div>
      <div class="voltar-container">
      <a href="buyathome.php" class="botoes-acesso">
        </i> Voltar
      </a>
    </div>
    </div>
  </div>

  <script>

  const moedas = parseInt(document.getElementById('moeda-count').innerText);
  const cupons = document.querySelectorAll('.cupom');

  function desbloquearCupons() {
    cupons.forEach(cupom => {
      const necessario = parseInt(cupom.dataset.requisito);
      if (moedas >= necessario) {
        cupom.classList.add('desbloqueado');
        cupom.addEventListener('click', () => resgatarCupom(cupom, necessario));
      }
    });
  }

  function resgatarCupom(cupom, custo) {
    if (cupom.classList.contains('desbloqueado')) {
      const confirmar = confirm(`Deseja resgatar este cupom por ${custo} moedas?`);
      if (!confirmar) return;

      fetch('resgatar_cupom.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `custo=${custo}`
      })
      .then(res => res.text())
      .then(data => {
        alert(data);
        location.reload(); // atualiza moedas na tela
      });
    }
  }

  // Inicializa
  desbloquearCupons();
</script>

  </script>
</body>
</html>
