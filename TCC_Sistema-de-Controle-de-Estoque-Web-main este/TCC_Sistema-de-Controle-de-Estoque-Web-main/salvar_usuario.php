<?php
session_start();

// Verifica se o usuário está logado e é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Recebe os dados do formulário
$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$senha = $_POST['senha'];
$confirmar_senha = $_POST['confirmar_senha'];
$nivel_acesso = $_POST['nivel_acesso'];

// Validações
$erros = [];

// Validar nome
if (strlen($nome) < 3) {
    $erros[] = "O nome deve ter pelo menos 3 caracteres";
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Email inválido";
}

// Verificar se o email já existe
$sql = "SELECT id FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $erros[] = "Este email já está cadastrado";
}

// Validar senha
if (strlen($senha) < 6) {
    $erros[] = "A senha deve ter pelo menos 6 caracteres";
}

// Validar confirmação de senha
if ($senha !== $confirmar_senha) {
    $erros[] = "As senhas não coincidem";
}

// Validar nível de acesso
$niveis_validos = ['usuario', 'visualizador'];
if (!in_array($nivel_acesso, $niveis_validos)) {
    $erros[] = "Nível de acesso inválido";
}

// Se houver erros, redireciona de volta com as mensagens
if (!empty($erros)) {
    $_SESSION['erros'] = $erros;
    $_SESSION['dados_form'] = [
        'nome' => $nome,
        'email' => $email,
        'nivel_acesso' => $nivel_acesso
    ];
    header("Location: cadastrar_usuario.php");
    exit();
}

// Se chegou aqui, não há erros. Criptografa a senha e insere o usuário
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $senha_hash, $nivel_acesso);

if ($stmt->execute()) {
    $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
} else {
    $_SESSION['erros'] = ["Erro ao cadastrar usuário: " . $stmt->error];
}

$stmt->close();
$conn->close();

// Redireciona de volta para a página de cadastro
header("Location: cadastrar_usuario.php");
?>