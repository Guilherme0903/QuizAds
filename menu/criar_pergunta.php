<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Nova Pergunta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Nova Pergunta</h2>

    <form action="../admin/salvar_pergunta.php" method="post">
        <label>Pergunta:</label><br>
        <input class="form-control" type="text" name="texto_pergunta" required><br><br>

        <label>Opções:</label><br>
        <input class="form-control" type="text" name="opcao1" required> <input class="form-check-input" type="radio" name="correta" value="1" required> Correta<br>
        <input class="form-control" type="text" name="opcao2" required> <input class="form-check-input" type="radio" name="correta" value="2" required> Correta<br>
        <input class="form-control" type="text" name="opcao3" required> <input class="form-check-input" type="radio" name="correta" value="3" required> Correta<br>
        <input class="form-control" type="text" name="opcao4" required> <input class="form-check-input" type="radio" name="correta" value="4" required> Correta<br><br>

        <button  class="btn btn-primary" type="submit">Salvar Pergunta</button>
    </form>

    <br>
    <a href="exibir_perguntas.php">Ver todas as perguntas</a>
</body>

</html>