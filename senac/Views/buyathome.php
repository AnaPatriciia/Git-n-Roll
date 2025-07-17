<?php

require_once '../../senac/Model/Login.php';
require_once '../../senac/Model/Cliente.php';
require_once '../../senac/Model/Checkin_diario.php';

session_start();

// Redireciona se não estiver logado
if (!isset($_SESSION['usuarios']['id_usuario'])) {
    header('Location: ../../senac/Views/cadastro.php');
    exit;
}

$conn = new mysqli("localhost", "root", "", "sistema_usuarios");

if ($conn->connect_error) {
    die('Erro ao conectar com o banco de dados.');
}

$id_usuario = $_SESSION['usuarios']['id_usuario'];

// ---------- BÔNUS POR CÓDIGO (3 moedas) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'registrar_bonus_checkin') {
    $codigo = trim($_POST['codigo_checkin']);
    $hoje = date('Y-m-d');
    $recompensa = 3;

    // Verifica se o usuário já usou 4 códigos hoje
    $qtdHoje = $conn->prepare("SELECT COUNT(*) as total FROM checkin_diario 
        WHERE id_usuario = ? AND data_checkin = ? AND dia_sequencia = 0");
    $qtdHoje->bind_param("is", $id_usuario, $hoje);
    $qtdHoje->execute();
    $result = $qtdHoje->get_result()->fetch_assoc();

    if ($result['total'] >= 4) {
        echo "Limite diário atingido.";
        exit;
    }

    // Atualiza a recompensa somando as moedas ao contador
    $stmt = $conn->prepare("UPDATE checkin_diario 
                            SET recompensa = recompensa + ? 
                            WHERE id_usuario = ? AND data_checkin = ?");
    $stmt->bind_param("iis", $recompensa, $id_usuario, $hoje);

    if ($stmt->execute()) {
        // Retorna o número atualizado de moedas
        $total_moedas = getTotalMoedas($conn, $id_usuario); // Atualiza a quantidade de moedas
        echo "Bônus de 3 moedas adicionado com sucesso! Atualizado para: " . $total_moedas;
    } else {
        echo "Erro ao registrar bônus.";
    }
    exit;
}


// CHECK-IN DIÁRIO (1 moeda, sequência) 
$checkin = new CheckinDiario($conn, $id_usuario);
if (!$checkin->jaFezCheckinHoje()) {
    $checkin->registrarCheckin();
    $_SESSION['checkin_hoje'] = true;
}

//  CÁLCULO DA SEQUÊNCIA 
$hoje = date('Y-m-d');
$ontem = date('Y-m-d', strtotime('-1 day'));

// Pega último check-in REAL (ignora bônus com dia_sequencia = 0)
$stmt = $conn->prepare("SELECT * FROM checkin_diario 
                        WHERE id_usuario = ? AND dia_sequencia > 0 
                        ORDER BY data_checkin DESC LIMIT 1");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$ultimo = $stmt->get_result()->fetch_assoc();

if (!$ultimo) {
    $dia_disponivel = 1;
} elseif ($ultimo['data_checkin'] === $hoje) {
    $dia_disponivel = $ultimo['dia_sequencia']; // já fez hoje
} elseif ($ultimo['data_checkin'] === $ontem) {
    $dia_disponivel = $ultimo['dia_sequencia'] + 1; // continua sequência
} else {
    $dia_disponivel = 1; // perdeu sequência
}

// MOEDAS ACUMULADAS (inclui bônus e check-ins) 
function getTotalMoedas($conn, $id_usuario) {
    $q = "SELECT SUM(recompensa) AS total_moedas FROM checkin_diario WHERE id_usuario = ?";
    $stmt = $conn->prepare($q);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    return $r['total_moedas'] ?? 0;
}

$total_moedas = getTotalMoedas($conn, $id_usuario);

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Check-in Diário</title>
  <link rel="stylesheet" href="../public/Css/index_style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
 <div class="container">

 <div class="home-header">
  <div class="left-space"></div> <!-- espaço à esquerda -->
  <img src="../public/Imagens/logo_club.png" alt="Logo" class="logo">
  <div class="mini-menu">
    <button class="menu-toggle"><i class="fa-solid fa-bars"></i></button>
    <ul class="menu-opcoes">
      <li><a href="resgatar-moedas.php">Resgatar meus pontos</a></li>
      <li><a href="login.php">Sair</a></li>
    </ul>
  </div>
</div>

    <h1 class="titulo-home">Check-in diário</h1>
    <div class="card">
      <div class="coin-counter"><i class="fa-solid fa-coins"></i> <?= $total_moedas ?></div>

      <div class="checkin-header">
        <span class="icon-checkin"><i class="fa-solid fa-circle-check"></i></span>
        <h2><?= $dia_disponivel ?>º dia</h2>
      </div>

      <div class="timer">Volte amanhã e seu check-in automático te dará mais uma moeda!</div>
    </div>

    <div class="campo-oval">
      <label for="numero">Digite o código do seu check-in compra:</label>
      <input type="number" id="numero" name="numero" placeholder="Apenas números" maxlength="4" />
    </div>

    <div id="bonus-feedback" style="margin-top: 10px; color: green;"></div>

    <script>
    document.getElementById("numero").addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault();

        const numero = this.value.trim();
        if (!numero) return;

        const formData = new FormData();
        formData.append("acao", "registrar_bonus_checkin");
        formData.append("codigo_checkin", numero);

        fetch("", {
            method: "POST",
            body: formData
        })
            .then(res => res.text())
            .then(res => {
                // Exibe mensagem de sucesso ou erro
                document.getElementById("bonus-feedback").innerText = res;

                if (res.includes('sucesso')) {
                    // Atualiza o contador de moedas no front-end
                    let currentCoins = parseInt(document.querySelector('.coin-counter').innerText.replace(/\D/g, ''));
                    document.querySelector('.coin-counter').innerText = `Moedas: ${currentCoins + 3}`;
                }
            });
    }
});


    </script>

    <div class="sobre">
      <h2>Sobre o Check-in</h2>
      <p>Saiba como funciona e receba suas moedas!</p>

      <div class="item">
        <span class="icon"><i class="fa-solid fa-calendar-days"></i></span>
        <div>
          <strong>Recompensa diária</strong>
          <p>A cada check-in você recebe uma moeda como recompensa do dia</p>
        </div>
      </div>

      <div class="item">
        <span class="icon"><i class="fa-solid fa-store"></i></span>
        <div>
          <strong>Recompensa por compra</strong>
          <p>A cada compra feita, um código será liberado na maquininha! Digite-a no campo "Checkin de compra" e acumule ainda mais pontos.</p>
        </div>
      </div>
    </div>

    <div class="catalogo-promocoes">
      <a href="promocoes.php" class="promo-card">
        <i class="fa-solid fa-percent"></i>
        <span>Promoções da semana</span>
      </a>
    
    </div>

  </div>

  <script>
    // Limita input a números
    const input = document.getElementById('numero');
    input.addEventListener('input', function () {
      this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Mini menu toggle
    const toggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu-opcoes');

    toggle.addEventListener('click', () => {
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
      if (!document.querySelector('.mini-menu').contains(e.target)) {
        menu.style.display = 'none';
      }
      
    }
  
  );
  
    
  </script>
</body>
</html>
