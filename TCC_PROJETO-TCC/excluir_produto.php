<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] == 'visualizador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Verificar se o arquivo de logs existe
$usar_logs = file_exists('registrar_logs.php');
if ($usar_logs) {
    include 'registrar_logs.php';
}

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

// Antes de excluir o produto, obter suas informações para o log
$sql_info = "SELECT nome FROM produtos WHERE id = ?";
$stmt_info = $conn->prepare($sql_info);
$stmt_info->bind_param("i", $id);
$stmt_info->execute();
$result_info = $stmt_info->get_result();
$produto_info = $result_info->fetch_assoc();
$nome_produto = $produto_info['nome'];
$stmt_info->close();

// Exclui o produto
$sql = "DELETE FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Registrar a operação no log
    if ($usar_logs && function_exists('registrarOperacao')) {
        $detalhes = "Produto ID {$id} ('{$nome_produto}') excluído";
        registrarOperacao($conn, $_SESSION['usuario_id'], 'excluir', 'produtos', $id, $detalhes);
    }
    
    $_SESSION['mensagem'] = "Produto excluído com sucesso!";
    header("Location: lista_produtos.php");
    exit();
} else {
    $_SESSION['erro'] = "Erro ao excluir produto: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: lista_produtos.php");
exit();