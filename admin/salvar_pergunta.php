<?php
include '../conexao/conexao.php';

$texto = $_POST['texto_pergunta']; // recebe o texto da pergunta
$correta = $_POST['correta']; // recebe as opção correta (1 a 4)

$conn->begin_transaction();

try {
    // Inserir a pergunta
    $stmt = $conn->prepare("INSERT INTO perguntas (texto) VALUES (?)");
    $stmt->bind_param("s", $texto);
    $stmt->execute();
    $pergunta_id = $stmt->insert_id;

    // Inserir as 4 opções
    for ($i = 1; $i <= 4; $i++) { // Loop para processar as 4 opções
        $opcao = $_POST["opcao$i"]; // recebe a opção atual

        // Verifica se esta opção é a correta (1 se sim, 0 se não)
        $eh_correta;
        if ($i == $correta) {
            $eh_correta = 1;
        } else {
            $eh_correta = 0;
        }

        $stmt_op = $conn->prepare("INSERT INTO opcoes (pergunta_id, texto, correta) VALUES (?, ?, ?)"); // Prepara e executa a inserção na tabela opcoes
        $stmt_op->bind_param("isi", $pergunta_id, $opcao, $eh_correta); //associa os valores às interrogações na query SQL (tipos: i = inteiro, s = string, i = inteiro).
        $stmt_op->execute(); //envia a instrução ao banco de dados.
    }

    $conn->commit();

    echo "Pergunta salva com sucesso! <br>";
    echo "<a href='criar_pergunta.php'>Cadastrar outra</a> | ";
    echo "<a href='../menu/exibir_perguntas.php'>Ver todas</a>";
} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao salvar: " . $e->getMessage();
}
