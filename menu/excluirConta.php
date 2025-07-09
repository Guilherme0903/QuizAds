<?php
include '../conexao/conexao.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Verifica se o usuário já respondeu algum quiz
    $stmtCheck = $conn->prepare("SELECT 1 FROM respostas_pessoa WHERE pessoa_id = ? LIMIT 1");
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        echo "nao_exclui_dependencia";
    } else {
        $stmt = $conn->prepare("DELETE FROM pessoa WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo "erro";
        }
    }
    $stmtCheck->close();
} else {
    echo "id ausente";
}
?>