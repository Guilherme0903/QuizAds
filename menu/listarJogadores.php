<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pontuação de jogadores</title>
</head>
<body>

    <h2 style="text-align: center;">Pontuação de jogadores</h2>

    <?php
    include '../conexao/conexao.php';

        $stmt = null;
        $rs = null;
        try {
            $stmt = $conn->prepare("SELECT rp.pessoa_id, p.apelido, p.administrador, SUM(rp.acertou) AS pontos, COUNT(rp.pergunta_id) AS qtd_perguntas
            FROM respostas_pessoa rp
            INNER JOIN pessoa p ON rp.pessoa_id = p.id
            GROUP BY rp.pessoa_id, p.apelido, p.administrador
            HAVING COUNT(rp.pergunta_id) = COUNT(*)
            ORDER BY pontos DESC;");
            $stmt->execute();
            $rs = $stmt->get_result();

            echo "<table>
                <tr>
                    <th>Apelido</th>
                    <th>Pontos</th>
                </tr>";

            while ($usuario = $rs->fetch_assoc()) {
                if ($usuario['administrador'] != 1) {
                    echo "<tr>
                    <td>{$usuario['apelido']}</td>
                    <td>{$usuario['pontos']}</td>
                    </tr>";
                }
            }
            echo "</table>";
            echo '<form action="/QuizAds/login/logout.php"              method="post" style="margin-top: 20px;">
            <button type="submit">Sair</button>
            </form>';

        } catch (Exception $e) {
            echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
        } finally {
            if ($conn != null) {
                $conn->close();
            }
            if ($stmt != null) {
                $stmt->close();
            }
            if ($rs != null) {
                $rs->close();
            }
        }
    ?>

</body>
</html>
