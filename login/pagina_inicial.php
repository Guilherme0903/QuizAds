<?php
session_start();

// Redireciona para login se não estiver logado
if (!isset($_SESSION['pessoa_id'])) {
    header('Location: login.html');
    exit;
}

$apelido = htmlspecialchars($_SESSION['apelido']);
$is_admin = isset($_SESSION['administrador']) && $_SESSION['administrador'] == 1;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página Inicial</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 400px; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        h2 { text-align: center; }
        ul { list-style: none; padding: 0; }
        li { margin: 12px 0; }
        a, button { text-decoration: none; color: #333; font-size: 16px; }
        form { display: inline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bem-vindo, <?php echo $apelido; ?>!</h2>
        <ul>
            <?php if ($is_admin): ?>
                <li><a href="controleUsuarios.php">Controle de Usuários</a></li>
                <li><a href="admin/exibir_perguntas.php">Exibir Perguntas</a></li>
                <li><a href="admin/criar_pergunta.php">Criar Pergunta</a></li>
                <li><a href="admin/editar_pergunta.php">Editar Pergunta</a></li>
                <li><a href="admin/excluir_pergunta.php">Excluir Pergunta</a></li>
                <li><a href="responder_quiz/listarJogadores.php">Listar Jogadores</a></li>
            <?php else: ?>
                <li><a href="../responder_quiz/quiz.php">Responder Quiz</a></li>
            <?php endif; ?>
            <li><a href="../cadastro/editarUsuario.php">Editar Usuário</a></li>
            <li><a href="../cadastro/excluirConta.php">Excluir Conta</a></li>
            <li>
                <form action="login/logout.php" method="post" style="display:inline;">
                    <button type="submit">Sair</button>
                </form>
            </li>
        </ul>
    </div>
</body>
</html>
