<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cadastro de Pessoas</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                include 'conexao/conexao.php';

                $email = $_POST['email'];
                // Verifica se o email é válido
                $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                echo "chegou na 2";
                $admin = isset($_POST['administrador']) ? 1 : 0;
                echo "chegou na 3";
                $apelido = $_POST['apelido'];
                echo "chegou na 4";

                try {

                    // Verifica se já existe o email ou apelido
                    $stmt = $conn->prepare("select email, apelido from pessoa where email = ? or apelido = ?");
                    echo "chegou na validcao";
                    $stmt->bind_param("ss", $email, $apelido);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows > 0) {
                        echo "<script>
                            alert('Email ou apelido já cadastrado.');
                            window.location.href = 'cadastro.php';
                        </script>";
                        exit;
                    }
                    $stmt->close();

                    $stmt = $conn->prepare("insert into pessoa (email, senha, administrador, apelido) values (?, ?, ?, ?)");
                    $stmt->bind_param("ssis", $email, $senha, $admin, $apelido);

                   if ($stmt->execute()) {
                    header("Location: /QuizAds/login/login.html");
                    exit;
                    } else {
                        echo "<p>Erro ao cadastrar: " . $stmt->error . "</p>";
                    }

                    exit;
                } catch (Exception $e) {
                    echo "<p>Erro: " . $e->getMessage() . "</p>";
                }
            }
        ?>
    <button id="btn-voltar" class="btn btn-primary mb-3">Voltar para Login</button>
    <div class="input-group mb-3">
        <form method="POST" onsubmit="return validaLogin()">
            Email: <input class="form-control" type="email" name="email" required><br>
            Senha: <input class="form-control" id="idSenha" type="password" name="senha" required ><br>
            Apelido: <input class="form-control" type="text" name="apelido" required><br>
            Administrador? <input type="checkbox" name="administrador"><br>
            <button class="btn btn-primary" type="submit">Cadastrar</button>
        </form>
    </div>

    </body>

    <script>
        function validaLogin() {
            var senha = document.getElementById("idSenha");

            if (senha.value.length < 8) {
                window.alert("A senha deve ter pelo menos 8 caracteres.");
                return false;
            }

            return true;
        }
    </script>
</html>