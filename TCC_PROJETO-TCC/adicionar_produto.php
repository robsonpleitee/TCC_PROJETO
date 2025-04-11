<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] == 'visualizador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Validação dos dados recebidos
$erros = [];

// Verifica se os campos foram preenchidos
if (empty($_POST['nome'])) {
    $erros[] = "O nome do produto é obrigatório";
}

if (!isset($_POST['quantidade']) || !is_numeric($_POST['quantidade']) || $_POST['quantidade'] < 0) {
    $erros[] = "A quantidade deve ser um número positivo";
}

if (empty($_POST['preco']) || !is_numeric(str_replace(',', '.', $_POST['preco'])) || floatval(str_replace(',', '.', $_POST['preco'])) <= 0) {
    $erros[] = "O preço deve ser um valor numérico positivo";
}

// Se houver erros, redireciona de volta com as mensagens
if (!empty($erros)) {
    $_SESSION['erros_produto'] = $erros;
    $_SESSION['dados_produto'] = [
        'nome' => $_POST['nome'] ?? '',
        'quantidade' => $_POST['quantidade'] ?? '',
        'preco' => $_POST['preco'] ?? ''
    ];
    header("Location: index.php");
    exit();
}

// Sanitiza os dados
$nome = htmlspecialchars(trim($_POST['nome']));
$quantidade = (int)$_POST['quantidade'];
$preco = floatval(str_replace(',', '.', $_POST['preco']));

// Prepara e executa a query usando prepared statements
$sql = "INSERT INTO produtos (nome, quantidade, preco) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sid", $nome, $quantidade, $preco);

if ($stmt->execute()) {
    $_SESSION['mensagem'] = "Produto adicionado com sucesso!";
} else {
    $_SESSION['erros_produto'] = ["Erro ao adicionar produto: " . $stmt->error];
}

$stmt->close();
$conn->close();

header("Location: index.php");
?>