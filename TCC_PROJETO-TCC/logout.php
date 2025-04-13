<?php
session_start();
include 'conexao.php';

// Registra o logout no log se o usuário estava logado
if (isset($_SESSION['usuario_id'])) {
    $sql_log = "INSERT INTO logs_acesso (usuario_id, acao, detalhes) VALUES (?, ?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $usuario_id = $_SESSION['usuario_id'];
    $acao = "logout";
    $detalhes = "Logout do sistema. IP: " . $_SERVER['REMOTE_ADDR'];
    $stmt_log->bind_param("iss", $usuario_id, $acao, $detalhes);
    $stmt_log->execute();
    $stmt_log->close();
}

// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header("Location: login.php");
exit();
?>