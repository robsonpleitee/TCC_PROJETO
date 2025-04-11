<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Recebe os dados do formulário
$cnpj = trim($_POST['cnpj']);
$nome = trim($_POST['nome']);
$rua = trim($_POST['rua']);
$numero = trim($_POST['numero']);
$bairro = trim($_POST['bairro']);
$cidade = trim($_POST['cidade']);
$uf = trim($_POST['uf']);

// Validações
$erros = [];

// Validar CNPJ
if (strlen($cnpj) !== 14) {
    $erros[] = "CNPJ deve ter 14 dígitos";
} else {
    // Verificar se o CNPJ já existe
    $sql = "SELECT id FROM fornecedores WHERE cnpj = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cnpj);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $erros[] = "Este CNPJ já está cadastrado";
    }
}

// Validar nome
if (strlen($nome) < 3) {
    $erros[] = "O nome deve ter pelo menos 3 caracteres";
}

// Validar rua
if (strlen($rua) < 3) {
    $erros[] = "A rua deve ter pelo menos 3 caracteres";
}

// Validar número
if (empty($numero)) {
    $erros[] = "O número é obrigatório";
}

// Validar bairro
if (strlen($bairro) < 3) {
    $erros[] = "O bairro deve ter pelo menos 3 caracteres";
}

// Validar cidade
if (strlen($cidade) < 3) {
    $erros[] = "A cidade deve ter pelo menos 3 caracteres";
}

// Validar UF
$ufs_validos = array('AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO');
if (!in_array($uf, $ufs_validos)) {
    $erros[] = "UF inválido";
}

// Se houver erros, redireciona de volta com as mensagens
if (!empty($erros)) {
    $_SESSION['erros_fornecedor'] = $erros;
    $_SESSION['dados_fornecedor'] = [
        'cnpj' => $cnpj,
        'nome' => $nome,
        'rua' => $rua,
        'numero' => $numero,
        'bairro' => $bairro,
        'cidade' => $cidade,
        'uf' => $uf
    ];
    header("Location: cadastrar_fornecedor.php");
    exit();
}

// Se chegou aqui, não há erros. Insere o fornecedor
$sql = "INSERT INTO fornecedores (cnpj, nome, rua, numero, bairro, cidade, uf) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $cnpj, $nome, $rua, $numero, $bairro, $cidade, $uf);

if ($stmt->execute()) {
    $_SESSION['mensagem'] = "Fornecedor cadastrado com sucesso!";
} else {
    $_SESSION['erros_fornecedor'] = ["Erro ao cadastrar fornecedor: " . $stmt->error];
}

$stmt->close();
$conn->close();

// Redireciona de volta para a página de cadastro
header("Location: cadastrar_fornecedor.php");
?>