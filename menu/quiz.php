<?php
session_start();
include '../conexao/conexao.php';

$pessoa_id = $_SESSION['pessoa_id'];

if (!isset($_SESSION['perguntas'])) {
    $todas = $conn->query("SELECT * FROM perguntas")->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("SELECT pergunta_id FROM respostas_pessoa WHERE pessoa_id = ?");
    $stmt->bind_param("i", $pessoa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $respondidas = $result->fetch_all(MYSQLI_ASSOC);

    $respondidas_ids = array_column($respondidas, 'pergunta_id');
    $pendentes = array_filter($todas, function($pergunta) use ($respondidas_ids) {
        return !in_array($pergunta['id'], $respondidas_ids);
    });

    $_SESSION['atual'] = 0;
    $_SESSION['perguntas'] = array_values($pendentes);
}

$indice = $_SESSION['atual'];
$perguntas = $_SESSION['perguntas'];

if ($indice >= count($perguntas)) {
        // Busca a pontuação do usuário
    $stmt = $conn->prepare("select (select count(acertou) from respostas_pessoa where pessoa_id = ? and acertou = 1) as acertos, count(pergunta_id) as qtd_perguntas from respostas_pessoa
    where pessoa_id = ?;");
    $stmt->bind_param("ii", $pessoa_id, $pessoa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $acertos = $row['acertos'] ?? 0;
    $total = $row['qtd_perguntas'] ?? 0;

    // Retorna HTML simples com pontuação e botão
    echo "<div class='text-center'>
            <h4>Quiz finalizado!</h4>
            <p>Pontuação: <strong>{$acertos}/{$total}</strong></p>
            
          </div>";
    exit;
}

$pergunta_atual = $perguntas[$indice];

$stmt = $conn->prepare("SELECT * FROM opcoes WHERE pergunta_id = ?");
$stmt->bind_param("i", $pergunta_atual['id']);
$stmt->execute();
$opcoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="quiz-container">
        <form id="form-quiz">
            <p><strong><?php echo $pergunta_atual['texto']; ?></strong></p>

            <?php foreach ($opcoes as $opcao): ?>
                <label>
                    <input class="form-check-input" type="radio" name="opcao_id" value="<?= $opcao['id'] ?>" required>
                    <?= htmlspecialchars($opcao['texto']) ?>
                </label><br>
            <?php endforeach; ?>

            <input type="hidden" name="pergunta_id" value="<?= $pergunta_atual['id'] ?>">
            <button class="btn btn-primary type="submit">Responder</button>
        </form>
    </div>

    <div id="mensagem" style="margin-top: 10px;"></div>

    <script>
    $(document).ready(function() {
        $("#form-quiz").on("submit", function(e) {
            e.preventDefault(); // impede o envio padrão do formulário

            $.ajax({
                url: "/QuizAds/responder.php",
                method: "POST",
                data: $(this).serialize(),
                success: function(data) {
                    // Atualiza o conteúdo da pergunta com a próxima pergunta retornada
                    $("#quiz-container").html(data);
                    $("#mensagem").text(""); // limpa mensagem de erro
                },
                error: function() {
                    $("#mensagem").text("Erro ao enviar resposta.");
                }
            });
        });
    });

    // $(document).on('click', '#btn-ver-classificacao', function() {
    //     $.get('/QuizAds/menu/listarJogadores.php', function(data) {
    //         $('#quiz-container').html(data);
    //     });
    // });
    </script>

    
</body>
</html>
