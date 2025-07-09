<?php 
include '../conexao/conexao.php';

$pessoa_id = $_SESSION['pessoa_id'];

$stmt = $conn->prepare("SELECT (SELECT COUNT(acertou) FROM respostas_pessoa WHERE pessoa_id = ? AND acertou = 1) AS acertos, COUNT(pergunta_id) AS qtd_perguntas FROM respostas_pessoa WHERE pessoa_id = ?;");
$stmt->bind_param("ii", $pessoa_id, $pessoa_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$acertos = $row['acertos'] ?? 0;
$total = $row['qtd_perguntas'] ?? 0;
?>
<div class='text-center'>
    <h4>Quiz finalizado!</h4>
    <p>Pontuação: <strong><?= $acertos ?>/<?= $total ?></strong></p>
    <button id='btn-ver-classificacao' class='btn btn-primary'>Ver classificação</button>
</div>