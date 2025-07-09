<?php
include '../conexao/conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Exclui a pergunta e automaticamente as opções (por causa do ON DELETE CASCADE)
    $stmt = $conn->prepare("DELETE FROM perguntas WHERE id = ?");
    $stmt->bind_param("i", $id);
try {
    if ($stmt->execute()) { 
        header("Location: /QuizAds/login/pagina2.php");
        exit();
    } else {
        // Verifica se é erro de integridade referencial (código 1451)
        if ($stmt->errno == 1451) {
            echo "<div class='alert alert-danger'>Não é possível excluir esta pergunta porque existem respostas relacionadas a ela.</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao excluir a pergunta: " . $stmt->error . "</div>";
        }
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erro ao excluir a pergunta: " . $e->getMessage() . "</div>";
}
    
} else {
    echo "ID da pergunta não informado.";
}
