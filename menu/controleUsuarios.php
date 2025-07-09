<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2 style="text-align: center;">Lista de Usuários</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Apelido</th>
                <th>Administrador?</th> 
            </tr>
        </thead>
        <tbody>
            <?php

                include '../conexao/conexao.php';

                $stmt = null;
                $rs = null;
                try {
                    $stmt = $conn->prepare("select * from pessoa");
                    $stmt->execute();
                    $rs = $stmt->get_result();
                    if ($rs->num_rows > 0) {
                        while ($usuario = $rs->fetch_assoc()) {
                            echo "<tr id='linha-{$usuario['id']}'>
                                <td>{$usuario['id']}</td>
                                <td>{$usuario['email']}</td>
                                <td>{$usuario['apelido']}</td>
                                <td>" . ($usuario['administrador'] ? "Sim" : "Não") . "</td> 
                               
                            </tr>";
                        }
                    }
                    $rs->close();
                    $stmt->close();
                } catch (Exception $e) {
                    echo "erro ao listar usuários " . $e->getMessage();
                } finally {
                    if ($conn != null) {
                        $conn->close();
                    }
                }
            ?>
        </tbody>
    </table>

    <script>
        $(document).on('click', '.btnExcluir', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            if (confirm('Deseja realmente excluir este usuário?')) {
                $.ajax({
                    url: 'excluirConta.php',
                    type: 'POST',
                    data: { id: id },
                    success: function (resposta) {
                        if (resposta.trim() === 'ok') {
                            $('#linha-' + id).remove();
                        } else {
                            alert('erro ao excluir o usuário: ', resposta);
                        }
                    },
                    error: function () {
                        alert('erro ao fazer requisição');
                    }
                });
            }
        });
    </script>

</body>
</html>
