<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

include '../conexao/conexao.php';

// le as informações de e-mail e senha que vieram do ajax
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Cria arquivo de log
file_put_contents('debug.log', "Email: $email, Senha: $senha\n", FILE_APPEND);

//valida se os campos estão vazios
if (empty($email) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos.']);
    exit();
}
//consulta se o perfil logado esta no banco
$stmt = $conn->prepare('SELECT id, apelido, email, senha, administrador FROM pessoa WHERE email = ?');
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Erro na consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param('s', $email); //vicula os parametros da consulta
$stmt->execute(); // executa a consulta
$result = $stmt->get_result(); // paga o retorno da consulta
$usuario = $result->fetch_assoc(); //converte a consulta em array

//faz parte do log para ver a senha digitada com a senha salva no banco
file_put_contents('debug.log', "Email: $email\nSenha digitada: $senha\nHash do banco: " . ($usuario['senha'] ?? 'NULO') . "\n\n", FILE_APPEND);

//faz a comparação do login salvo no banco
if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['pessoa_id'] = $usuario['id'];
    $_SESSION['apelido'] = $usuario['apelido'];
    $_SESSION['administrador'] = $usuario['administrador'];

    // Limpa progresso do quiz ao trocar de usuário
    unset($_SESSION['perguntas']);
    unset($_SESSION['atual']);

    echo json_encode([
        'status' => 'success',
        'message' => 'Login bem-sucedido!',
        'is_admin' => $usuario['administrador']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email ou senha inválidos.']);
}

$stmt->close();
exit;
