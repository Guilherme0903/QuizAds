<?php
session_start();
if (!isset($_SESSION['pessoa_id'])) {
    header('Location: login.html');
    exit;
}
$apelido = $_SESSION['apelido'];
$is_admin = isset($_SESSION['administrador']) && $_SESSION['administrador'] == 1;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página Inicial</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f7f7; margin: 0; }
        .navbar { box-shadow: 0 2px 8px #0001; }
        .navbar-nav .nav-link, .navbar-brand { font-size: 1.1rem; }
        .container-content { max-width: 900px; margin: 90px auto 0 auto; padding: 30px; background: #fff; border-radius: 10px; min-height: 300px; box-shadow: 0 2px 8px #0001; }
        #conteudo-dinamico { min-height: 200px; }
        .logout-btn { margin-left: 15px; }
        @media (max-width: 768px) {
            .container-content { margin-top: 120px; padding: 10px; }
        }
    </style>
</head>
<body>
    <!-- Menu superior fixo -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
      <div class="container-fluid">
        <span class="navbar-brand">Quiz ADS - Olá, <?php echo htmlspecialchars($apelido); ?>!</span>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarQuiz" aria-controls="navbarQuiz" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarQuiz">

          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <?php if ($is_admin): ?>
                <li class="nav-item"><a class="nav-link btn-menu" data-arquivo="/QuizAds/menu/controleUsuarios.php" href="#">Controle de Usuários</a></li> 
                <li class="nav-item"><a class="nav-link btn-menu" data-arquivo="/QuizAds/menu/exibir_perguntas.php" href="#">Exibir Perguntas</a></li>
                <li class="nav-item"><a class="nav-link btn-menu" data-arquivo="/QuizAds/menu/criar_pergunta.php" href="#">Criar Pergunta</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link btn-menu" data-arquivo="/QuizAds/menu/quiz.php" href="#">Responder Quiz</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link btn-menu" data-arquivo="/QuizAds/menu/editarUsuario.php" href="#">Editar Usuário</a></li>
            <li class="nav-item"><a class="nav-link btn-menu" data-arquivo="/QuizAds/menu/listarJogadores.php" href="#">Ranking do Quiz</a></li>
            <li class="nav-item">
                <a class="nav-link text-danger" data-arquivo="/QuizAds/menu/excluirConta.php"  href="#" id="btn-excluir-conta">Excluir Conta</a>
            </li>
          </ul>
          <form action="logout.php" method="post" class="d-flex">
              <button type="submit" class="btn btn-outline-dark logout-btn">Sair</button>
          </form>
        </div>
      </div>
    </nav>

    <!-- Conteúdo dinâmico -->
    <div class="container-content" id="conteudo-dinamico">
        <div class="text-center text-muted">Selecione uma opção no menu acima.</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Carrega conteúdo via AJAX ao clicar no menu
    $('.btn-menu').click(function(e) {
        e.preventDefault();
        var arquivo = $(this).data('arquivo');
        $('#conteudo-dinamico').html('<div class="text-center text-muted">Carregando...</div>');
     
        $.get(arquivo, function(data) {
            console.log('Carregando conteúdo de: ' + arquivo);
            $('#conteudo-dinamico').html(data);
        }).fail(function() {
            $('#conteudo-dinamico').html('<div class="text-danger">Erro ao carregar conteúdo.</div>');
        });
           
    });

    // Excluir conta via AJAX
    $('#btn-excluir-conta').click(function(e) {
        e.preventDefault();
        if (confirm('Tem certeza que deseja excluir sua conta?')) {
            $.post('../menu/excluirConta.php', { id: <?php echo (int)$_SESSION['pessoa_id']; ?> }, function(resposta) {
                if (resposta.trim() === 'ok') {
                    alert('Conta excluída com sucesso!');
                    window.location.href = 'login.html';
                } else if (resposta.trim() === 'nao_exclui_dependencia') {
                    alert('Não é possível excluir sua conta porque já existem respostas vinculadas a ela.');
                } else {
                    console.log('Erro ao excluir conta:', resposta);
                }
            });
        }
    });
    </script>
</body>
</html>