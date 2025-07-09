<?php
$host = 'localhost';
$dbname = 'quiz_ads';
$user = 'root';
$password = 'aluno'; // coloque aqui sua senha do MySQL

$conn = new mysqli($host, $user, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Define o charset como UTF-8 (opcional, mas recomendado)
$conn->set_charset("utf8");
