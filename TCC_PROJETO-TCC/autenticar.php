<?php
session_start();
include 'conexao.php';

// Validação dos dados de entrada
$erros = [];

// Verifica se os campos foram preenchidos
if (empty($_POST['email'])) {
    $erros[] = "O campo email é obrigatório";
}

if (empty($_POST['senha'])) {
    $erros[] = "O campo senha é obrigatório";
}

// Valida o formato do email
if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Formato de email inválido";
}

// Se houver erros, redireciona de volta com as mensagens
if (!empty($erros)) {
    $_SESSION['erros_login'] = $erros;
    $_SESSION['email_login'] = $_POST['email'] ?? '';
    header("Location: login.php");
    exit();
}

$email = trim($_POST['email']);
$senha = $_POST['senha'];

// Busca o usuário no banco de dados
$sql = "SELECT id, nome, senha, nivel_acesso FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    // Verifica a senha
    if (password_verify($senha, $usuario['senha'])) {
        // Login bem-sucedido
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];

        // Redireciona conforme o nível de acesso
        switch ($usuario['nivel_acesso']) {
            case 'administrador':
                header("Location: admin_dashboard.php");
                break;
            case 'usuario':
                header("Location: usuario_dashboard.php");
                break;
            case 'visualizador':
                header("Location: visualizador_dashboard.php");
                break;
        }
        exit();
    } else {
        $_SESSION['erros_login'] = ["Senha incorreta"];
        $_SESSION['email_login'] = $email;
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['erros_login'] = ["Usuário não encontrado"];
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();
?>