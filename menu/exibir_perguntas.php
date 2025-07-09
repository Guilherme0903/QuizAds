<?php
include '../conexao/conexao.php';

// Consulta todas as perguntas cadastradas no banco
$sql_perguntas = "SELECT * FROM perguntas";
$result_perguntas = $conn->query($sql_perguntas);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Perguntas Cadastradas</title>
</head>

<body>
    <h2>Perguntas do Quiz ADS</h2>

    <hr>

    <?php
    // Verifica se $result_perguntas é verdadeiro e se retornou alguma linha
    if ($result_perguntas && $result_perguntas->num_rows > 0) {
        // Loop para exibir cada pergunta
        while ($pergunta = $result_perguntas->fetch_assoc()) {
            echo '<div style="margin-bottom: 20px;">';
            echo '<strong>' . htmlspecialchars($pergunta['texto']) . '</strong><br>';

            // Buscar opções da pergunta atual
            $stmt_opcoes = $conn->prepare("SELECT * FROM opcoes WHERE pergunta_id = ?");
            $stmt_opcoes->bind_param("i", $pergunta['id']);
            $stmt_opcoes->execute();
            $result_opcoes = $stmt_opcoes->get_result();

            echo '<ul>';
            // Loop para percorrer e exibir cada pergunta retornada do banco
            while ($opcao = $result_opcoes->fetch_assoc()) {
                // Exibe o texto da opção coom htmlspecialchars para evitar problemas com caracteres especiais
                echo '<li>' . htmlspecialchars($opcao['texto']);
                // Se a opção for marcada como correta no banaco adiciona "(Correta)" ao lado
                if ($opcao['correta']) {
                    echo ' <strong>(Correta)</strong>';
                }
                echo '</li>';
            }
            echo '</ul>';

            echo '<a href="../admin/editar_pergunta.php?id=' . $pergunta['id'] . '">Editar</a> | ';
            echo '<a href="../admin/excluir_pergunta.php?id=' . $pergunta['id'] . '" onclick="return confirm(\'Tem certeza que deseja excluir esta pergunta?\');">Excluir</a>';

            echo '</div><hr>';
        }
    } else {
        echo '<p>Nenhuma pergunta cadastrada ainda.</p>';
    }
    ?>

</body>

</html>