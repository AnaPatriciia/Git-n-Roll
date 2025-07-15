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
      <div class="moedas"><i class="fa-solid fa-coins"></i> <span id="moeda-count">120</span> moedas</div>
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
    </div>
  </div>

  <script>
    const moedas = 120; // Exemplo: valor acumulado
    const btnResgatar = document.getElementById('btn-resgatar');
    const cupons = document.querySelectorAll('.cupom');

    btnResgatar.addEventListener('click', () => {
      cupons.forEach(cupom => {
        const necessario = parseInt(cupom.dataset.requisito);
        if (moedas >= necessario) {
          cupom.classList.add('desbloqueado');
        }
      });
    });

    cupons.forEach(cupom => {
      cupom.addEventListener('click', () => {
        if (cupom.classList.contains('desbloqueado')) {
          const valor = cupom.querySelector('h3').innerText;
          alert(`✅ Cupom ${valor} resgatado com sucesso!`);
        }
      });
    });

    // Atualiza a contagem
    document.getElementById('moeda-count').innerText = moedas;
  </script>
</body>
</html>
