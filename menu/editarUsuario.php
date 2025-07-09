<?php
session_start();
include '../conexao/conexao.php';

// Se for POST (AJAX), só retorna JSON e não mostra HTML
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pessoa_id = $_SESSION['pessoa_id'];
    $email = $_POST['email'];
    $apelido = $_POST['apelido'];
    $admin = isset($_POST['administrador']) ? 1 : 0;
    $senhaNova = $_POST['senha'];

    try {
        if (!empty($senhaNova)) {
            $senhaHash = password_hash($senhaNova, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("update pessoa set email = ?, senha = ?, administrador = ?, apelido = ? where id = ?");
            $stmt->bind_param("ssisi", $email, $senhaHash, $admin, $apelido, $pessoa_id);
        } else {
            $stmt = $conn->prepare("update pessoa set email = ?, administrador = ?, apelido = ? where id = ?");
            $stmt->bind_param("sisi", $email, $admin, $apelido, $pessoa_id);
        }

        if ($stmt->execute()) {
            $_SESSION['apelido'] = $apelido;
            echo json_encode([
                'status' => 'success',
                'mensagem' => "<div class='alert alert-success'>Usuário atualizado com sucesso!</div>",
                'novo_apelido' => $apelido
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'mensagem' => "<div class='alert alert-danger'>Erro ao atualizar: " . $stmt->error . "</div>"
            ]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'mensagem' => "<div class='alert alert-danger'>Erro ao editar usuário: " . $e->getMessage() . "</div>"
        ]);
    }
    $conn->close();
    exit;
}

// Se não for POST, exibe o formulário normalmente
$pessoa_id = $_SESSION['pessoa_id'];
$stmt = $conn->prepare("select email, apelido, administrador from pessoa where id = ?");
$stmt->bind_param("i", $pessoa_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <h2>Editar Usuário</h2>
    <form id="form-editar-usuario" method="post" action="../menu/editarUsuario.php">
        Email: <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required><br><br>
        Apelido: <input class="form-control" type="text" name="apelido" value="<?= htmlspecialchars($usuario['apelido']) ?>" required><br><br>
        Nova senha (opcional): <input class="form-control" type="password" name="senha"><br><br>
        Administrador? <input class="form-check-input" type="checkbox" name="administrador" <?= $usuario['administrador'] ? 'checked' : '' ?>><br><br>
        <button class="btn btn-primary" type="submit">Salvar Alterações</button>
    </form>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $('#form-editar-usuario').on('submit', function(e) {
        e.preventDefault();
        $.post('../menu/editarUsuario.php', $(this).serialize(), function(resposta) {
            // Remove mensagens antigas
            $('.alert').remove();
            let data;
            try {
                data = JSON.parse(resposta);
            } catch {
                $('#form-editar-usuario').before(resposta);
                return;
            }
            $('#form-editar-usuario').before(data.mensagem);
            if (data.status === 'success' && data.novo_apelido) {
                // Atualiza o apelido no cabeçalho
                $('.navbar-brand').text('Quiz ADS - Olá, ' + data.novo_apelido + '!');
            }
        });
    });
    </script>
</body>
</html>