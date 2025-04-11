<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] == 'visualizador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    header("Location: lista_produtos.php");
    exit();
}

$id = (int)$_GET['id'];

// Verifica se o produto existe
$sql = "SELECT id FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['erro'] = "Produto não encontrado.";
    header("Location: lista_produtos.php");
    exit();
}

// Exclui o produto
$sql = "DELETE FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['mensagem'] = "Produto excluído com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao excluir produto: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: lista_produtos.php");
exit(); 