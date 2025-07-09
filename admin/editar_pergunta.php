<?php
include '../conexao/conexao.php';


// Verifica se o ID da pergunta foi passado via GET
if (!isset($_GET['id'])) {
    echo "ID da pergunta não fornecido.";
    exit;
}

$id = $_GET['id'];

// Buscar a pergunta
$sql_pergunta = "SELECT * FROM perguntas WHERE id = ?";
// Prepara a consulta para evitar SQL Injection
$stmt = $conn->prepare($sql_pergunta);
// Associa o ID da pergunta ao parâmetro da consulta
$stmt->bind_param("i", $id);
// Executa a consulta
$stmt->execute();
// Obtém o resultado da consulta em formato de objeto
$result = $stmt->get_result();

// Verifica se a pergunta foi encontrada
if ($result->num_rows === 0) {
    echo "Pergunta não encontrada.";
    exit;
}

// Busca a pergunta 
$pergunta = $result->fetch_assoc();

// Buscar as opções
$sql_opcoes = "SELECT * FROM opcoes WHERE pergunta_id = ?";
$stmt_op = $conn->prepare($sql_opcoes);
$stmt_op->bind_param("i", $id);
$stmt_op->execute();
$result_op = $stmt_op->get_result();

// Cria um array vazio para armazenar as opções da pergunta
$opcoes = [];

// Loop para percorrer cada linha retornada pela consulta das opções
while ($row = $result_op->fetch_assoc()) {
    // adiciona cada opção ao array $opcoes
    // Cada opção é um array associativo com os campos 'id', 'texto' e
    $opcoes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Pergunta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Editar Pergunta</h2>

    <form method="POST" action="atualizar_pergunta.php">
        <!-- campo oculto que armazena o ID da pergunta a ser atualizada !-->
        <input type="hidden" name="id" value="<?= $pergunta['id'] ?>">

        <label>Pergunta:</label><br>
        <!-- campo para editar o texto da pergunta, que permite caracteres especiais com htmlspecialchars -->
        <input class="form-control"type="text" name="texto_pergunta" value="<?= htmlspecialchars($pergunta['texto']) ?>" required><br><br>

        <label>Opções:</label><br>

        <?php
        // Exibe as opções da pergunta
        foreach ($opcoes as $index => $op) {
            $num = $index + 1; // define o número da opção (1 a 4)
            // Campo oculto que armazena o ID da opção atual no banco de dados
            echo '<input type="hidden" name="opcao_id' . $num . '" value="' . $op['id'] . '">';
            // Campo de texto preenchido com o texto da opção atual, htmlspecialchars permite caracteres especiais
            echo '<input type="text" name="opcao' . $num . '" value="' . htmlspecialchars($op['texto']) . '" required>';
            // Se esta opção for a correta, o radio é marcado como "checked"
            echo '<input class="form-check-input" type="radio" name="correta" value="' . $num . '"';
            if ($op['correta'] == 1) {
                echo ' checked';
            }
            echo '> Correta<br>';
        }
        ?>

        <br>

        <button class="btn btn-primary" type="submit">Atualizar e Salvar</button>
    </form>

    <br>
    <a href="../login/pagina2.php">Voltar</a>
</body>
<?php
// Verifica se a URL tem o parâmetro ?sucesso no arqivo atualizar_pergunta.php
// Se sim, exibe uma mensagem de sucesso
if (isset($_GET['sucesso'])) {
    echo '<p style="color: green;">Pergunta atualizada com sucesso!</p>';
}
?>



</html>