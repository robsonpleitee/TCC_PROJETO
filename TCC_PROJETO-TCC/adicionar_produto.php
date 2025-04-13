<?php
session_start();
include 'conexao.php';

// Verificar se o arquivo registrar_logs.php existe antes de tentar incluí-lo
$usar_logs = file_exists('registrar_logs.php');
if ($usar_logs) {
    include 'registrar_logs.php';
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] == 'visualizador') {
    header("Location: login.php");
    exit();
}

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
    $produto_id = $conn->insert_id;
    
    // Registrar a operação no log apenas se o sistema de logs estiver disponível
    if ($usar_logs && function_exists('registrarOperacao')) {
        $detalhes = "Produto '{$nome}' (Qtd: {$quantidade}, Preço: R$ " . number_format($preco, 2, ',', '.') . ") adicionado";
        registrarOperacao($conn, $_SESSION['usuario_id'], 'inserir', 'produtos', $produto_id, $detalhes);
    }
    
    $_SESSION['mensagem'] = "Produto adicionado com sucesso!";
    header("Location: index.php");
    exit();
} else {
    $_SESSION['erros_produto'] = ["Erro ao adicionar produto: " . $stmt->error];
}

$stmt->close();
$conn->close();

header("Location: index.php");
?>