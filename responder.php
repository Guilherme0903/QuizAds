<?php
session_start();
include 'conexao/conexao.php';

$pessoa_id = $_SESSION['pessoa_id'] ?? 3;
$pergunta_id = $_POST['pergunta_id'];
$opcao_id = $_POST['opcao_id'];

// Verifica se a opção era correta
$stmt = $conn->prepare("SELECT correta FROM opcoes WHERE id = ?");
$stmt->bind_param("i", $opcao_id);
$stmt->execute();
$stmt->bind_result($correta);
$stmt->fetch();
$stmt->close();

// Grava a resposta
$stmt = $conn->prepare("INSERT INTO respostas_pessoa (pessoa_id, pergunta_id, opcao_escolhida_id, acertou) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $pessoa_id, $pergunta_id, $opcao_id, $correta);
$stmt->execute();
$stmt->close();

// Avança para a próxima pergunta
$_SESSION['atual']++;

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
            <button id='btn-ver-classificacao' class='btn btn-primary'>Ver classificação</button>
          </div>
          <script>
            $('#btn-ver-classificacao').on('click', function() {
                $.get('/QuizAds/menu/listarJogadores.php', function(data) {
                    $('#quiz-container').html(data);
                });
            });
          </script>";
    exit;
}

$pergunta_atual = $perguntas[$indice];

// Pega as novas opções
$stmt = $conn->prepare("SELECT * FROM opcoes WHERE pergunta_id = ?");
$stmt->bind_param("i", $pergunta_atual['id']);
$stmt->execute();
$opcoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Retorna o HTML da próxima pergunta
?>

<form id="form-quiz">
    <p><strong><?= $pergunta_atual['texto'] ?></strong></p>

    <?php foreach ($opcoes as $opcao): ?>
        <label>
            <input class="form-check-input" type="radio" name="opcao_id" value="<?= $opcao['id'] ?>" required>
            <?= htmlspecialchars($opcao['texto']) ?>
        </label><br>
    <?php endforeach; ?>

    <input type="hidden" name="pergunta_id" value="<?= $pergunta_atual['id'] ?>">
    <button class="btn btn-primary type="submit">Responder</button>
</form>

<script>
$("#form-quiz").on("submit", function(e) {
    e.preventDefault();
    $.post("/QuizAds/responder.php", $(this).serialize(), function(data) {
        $("#quiz-container").html(data);
    });
});
</script>
