<?php
session_start();

// Verifica se o usuário está logado e é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $erros = [];
    $dados_form = $_POST;

    // Validação dos campos
    if (empty($_POST['nome']) || strlen(trim($_POST['nome'])) < 3) {
        $erros[] = "O nome deve ter pelo menos 3 caracteres";
    }

    if (empty($_POST['email'])) {
        $erros[] = "O email é obrigatório";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido";
    }

    if (empty($_POST['senha'])) {
        $erros[] = "A senha é obrigatória";
    }

    if ($_POST['senha'] !== $_POST['confirmar_senha']) {
        $erros[] = "As senhas não coincidem";
    }

    // Validação do nível de acesso
    $niveis_permitidos = ['administrador', 'usuario', 'visualizador'];
    if (!in_array($_POST['nivel_acesso'], $niveis_permitidos)) {
        $erros[] = "Nível de acesso inválido";
    }

    // Verifica se o email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $erros[] = "Este email já está cadastrado";
    }

    if (empty($erros)) {
        $nome = htmlspecialchars(trim($_POST['nome']));
        $email = htmlspecialchars(trim($_POST['email']));
        $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $nivel_acesso = $_POST['nivel_acesso'];

        $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $nivel_acesso);

        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Usuário cadastrado com sucesso!";
            header("Location: lista_usuarios.php");
            exit();
        } else {
            $erros[] = "Erro ao cadastrar usuário: " . $conn->error;
        }
    }

    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        $_SESSION['dados_form'] = $dados_form;
        header("Location: cadastrar_usuario.php");
        exit();
    }
}
?>