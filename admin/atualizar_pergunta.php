<?php
include '../conexao/conexao.php';

$id = $_POST['id']; // ID da pergunta a ser atualizada
$texto = $_POST['texto_pergunta']; // texto da pergunta
$correta = $_POST['correta']; // número de 1 a 4

$conn->begin_transaction();

try {
    // Prepara a atualização do texto da pergunta na tabela 'perguntas'
    $stmt = $conn->prepare("UPDATE perguntas SET texto = ? WHERE id = ?");
    // Associa os parâmetros: texto da pergunta e ID
    $stmt->bind_param("si", $texto, $id);
    // Executa a atualização da pergunta
    $stmt->execute();

    // Atualiza as opções
    for ($i = 1; $i <= 4; $i++) {
        // Pega o novo texto da opção atual via POST 
        $texto_opcao = $_POST["opcao$i"];
        // Pega o ID da opção atual via POST
        $opcao_id = $_POST["opcao_id$i"];

        // Verifica se esta opção é a correta (1 se sim, 0 se não)
        $eh_correta;
        if ($i == $correta) {
            $eh_correta = 1;
        } else {
            $eh_correta = 0;
        }

        $stmt_op = $conn->prepare("UPDATE opcoes SET texto = ?, correta = ? WHERE id = ?"); // Prepara e executa a inserção na tabela opcoes
        $stmt_op->bind_param("sii", $texto_opcao, $eh_correta, $opcao_id); // associa os valores às interrogações na query (tipos: s = string, i = inteiro, i = inteiro).
        $stmt_op->execute();
    }

    $conn->commit();

    // Redireciona para a página de edição pela url informa o id da pergunta e um parâmetro de sucesso
    header("Location: editar_pergunta.php?id=$id&sucesso=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao atualizar: " . $e->getMessage();
}
