<?php
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
      if ($tipo === 'semanal') {
          $promo = new PromocoesSemanal();
      } elseif ($tipo === 'sazonal') {
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="../public/Css/gerenciamento.css">
</head>
<body>
  <div class="admin">
    <div class="admin-header">
      <img src="../../Imagens/logo_club.png" alt="Logo">
      <button class="logout-btn">Sair</button>
    </div>

    <div class="admin-nav">
      <button class="active" data-target="ativos">Clientes Ativos</button>
      <button data-target="inativos">Clientes Inativos</button>
      <button data-target="promoAtivas">Promoções Ativas</button>
      <button data-target="promoInativas">Promoções Inativas</button>
    </div>

    <div class="admin-body">
      <section id="ativos" class="pane active">
        <h2 style="margin-bottom:12px">Clientes Ativos</h2>
        <table>
          <thead><tr><th>Nome</th><th>Pontos</th><th>Resgates</th><th>Ação</th></tr></thead>
          <tbody id="tbAtivos">
            <tr><td>Camila</td><td>340</td><td>2</td><td><button class="btn inativar" onclick="inativar(this)">Inativar</button></td></tr>
            <tr><td>João</td><td>180</td><td>1</td><td><button class="btn inativar" onclick="inativar(this)">Inativar</button></td></tr>
          </tbody>
        </table>
      </section>

      <section id="inativos" class="pane">
        <h2 style="margin-bottom:12px">Clientes Inativos</h2>
        <table>
          <thead><tr><th>Nome</th><th>Pontos</th><th>Resgates</th><th>Ação</th></tr></thead>
          <tbody id="tbInativos"></tbody>
        </table>
      </section>

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

            <button type="submit" name="cadastrar" class="btn-salvar">Salvar</button>
          </div>
      </form>


      <form method="POST" enctype="multipart/form-data" style="margin-bottom: 40px;">
        <div class="promo-card-upload">
          <div class="upload-label">Promoção Sazonal</div>

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

          <button type="submit" name="cadastrar" class="btn-salvar">Salvar</button>
        </div>
      </form>
      


      </section>

      <section id="promoInativas" class="pane">
        <h2 style="margin-bottom:12px">Promoções Inativas</h2>
        <div id="promoInativasList"></div>
      </section>
    </div>

    <div class="admin-footer">Gerenciamento Club Buy At Home | Desenvolvido por Git'n'Roll</div>
  </div>

  <div class="modal" id="imgModal" onclick="this.style.display='none'">
    <img id="modalImg" src="" alt="Imagem Ampliada">
  </div>

  <script>
    document.querySelectorAll('.admin-nav button').forEach(b => {
      b.onclick = () => {
        document.querySelector('.admin-nav button.active').classList.remove('active');
        b.classList.add('active');
        document.querySelector('.pane.active').classList.remove('active');
        document.getElementById(b.dataset.target).classList.add('active');
      }
    });

    function deslogar() {
      alert('Deslogado!');
      location.href = 'login.html';
    }

    function inativar(el) {
      const row = el.closest('tr');
      document.getElementById('tbInativos').appendChild(row);
      el.textContent = 'Reativar';
      el.className = 'btn reativar';
      el.onclick = reativar;
    }
    function reativar(el) {
      const row = el.closest('tr');
      document.getElementById('tbAtivos').appendChild(row);
      el.textContent = 'Inativar';
      el.className = 'btn inativar';
      el.onclick = inativar;
    }

    const sazInputs = document.getElementById('saz-inputs');
    for (let i = 1; i <= 3; i++) {
      sazInputs.insertAdjacentHTML('beforeend', `
        <label class="fake-upload"><i class="fas fa-upload"></i> Imagem ${i}
          <input type="file" id="saz-img${i}" accept="image/*">
        </label>
        <input type="text" id="saz-titulo${i}" placeholder="Título ${i}">
        <input type="text" id="saz-sub${i}" placeholder="Subtítulo ${i}">
      `);
    }

    function gerarCardPromo(url, titulo, sub) {
      const dataCadastro = new Date().toLocaleDateString();
      const card = `
        <div class="promo-inativa-card">
          <img src="${url}" onclick="ampliarImagem('${url}')">
          <strong>${titulo}</strong>
          <small>${sub}</small><br>
          <small>Cadastrada: ${dataCadastro}</small>
          <small>Inativada: ${dataCadastro}</small>
        </div>`;
      return card;
    }

    function salvarSemanal() {
      const file = document.getElementById('sem-img').files[0];
      const titulo = document.getElementById('sem-titulo').value;
      const sub = document.getElementById('sem-sub').value;
      const prev = document.getElementById('sem-preview');
      const inativas = document.getElementById('promoInativasList');
      prev.innerHTML = '';
      if (file) {
        const url = URL.createObjectURL(file);
        const card = gerarCardPromo(url, titulo, sub);
        prev.innerHTML = card;
        inativas.insertAdjacentHTML('beforeend', card);
      }
    }

    function salvarSazonal() {
      const prev = document.getElementById('saz-preview');
      const inativas = document.getElementById('promoInativasList');
      prev.innerHTML = '';
      for (let i = 1; i <= 3; i++) {
        const file = document.getElementById(`saz-img${i}`).files[0];
        const titulo = document.getElementById(`saz-titulo${i}`).value;
        const sub = document.getElementById(`saz-sub${i}`).value;
        if (file) {
          const url = URL.createObjectURL(file);
          const card = gerarCardPromo(url, titulo, sub);
          prev.insertAdjacentHTML('beforeend', card);
          inativas.insertAdjacentHTML('beforeend', card);
        }
      }
    }

    function ampliarImagem(url) {
      const modal = document.getElementById('imgModal');
      const img = document.getElementById('modalImg');
      img.src = url;
      modal.style.display = 'flex';
    }
  </script>
</body>
</html>