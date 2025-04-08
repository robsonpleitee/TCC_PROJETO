<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    header("Location: lista_usuarios.php");
    exit();
}

$id = (int)$_GET['id'];

// Não permite excluir o próprio usuário
if ($id == $_SESSION['usuario_id']) {
    $_SESSION['erro'] = "Você não pode excluir seu próprio usuário.";
    header("Location: lista_usuarios.php");
    exit();
}

// Verifica se o usuário existe
$sql = "SELECT id, nivel_acesso FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['erro'] = "Usuário não encontrado.";
    header("Location: lista_usuarios.php");
    exit();
}

$usuario = $result->fetch_assoc();

// Não permite excluir outros administradores
if ($usuario['nivel_acesso'] == 'administrador') {
    $_SESSION['erro'] = "Não é possível excluir um usuário administrador.";
    header("Location: lista_usuarios.php");
    exit();
}

// Exclui o usuário
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['mensagem'] = "Usuário excluído com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao excluir usuário: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: lista_usuarios.php");
exit(); 