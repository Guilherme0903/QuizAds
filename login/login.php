<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

include '../conexao/conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Log simples para depuração
file_put_contents('debug.log', "Email: $email, Senha: $senha\n", FILE_APPEND);

if (empty($email) || empty($senha)) {
    echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos.']);
    exit();
}

$stmt = $conn->prepare('SELECT id, apelido, email, senha, administrador FROM pessoa WHERE email = ?');
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Erro na consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
file_put_contents('debug.log', "Email: $email\nSenha digitada: $senha\nHash do banco: " . ($usuario['senha'] ?? 'NULO') . "\n\n", FILE_APPEND);

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