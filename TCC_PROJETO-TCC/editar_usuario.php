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

// Busca os dados do usuário
$sql = "SELECT id, nome, email, nivel_acesso FROM usuarios WHERE id = ?";
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

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = [];
    
    // Validação dos dados
    if (empty($_POST['nome'])) {
        $erros[] = "O nome é obrigatório";
    }
    
    if (empty($_POST['email'])) {
        $erros[] = "O email é obrigatório";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido";
    }
    
    // Verifica se o email já existe para outro usuário
    $sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_POST['email'], $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $erros[] = "Este email já está em uso por outro usuário";
    }
    
    // Validação do nível de acesso
    $niveis_validos = ['administrador', 'usuario', 'visualizador'];
    if (!in_array($_POST['nivel_acesso'], $niveis_validos)) {
        $erros[] = "Nível de acesso inválido";
    }
    
    if (empty($erros)) {
        $nome = htmlspecialchars(trim($_POST['nome']));
        $email = htmlspecialchars(trim($_POST['email']));
        $nivel_acesso = $_POST['nivel_acesso'];
        
        // Se uma nova senha foi fornecida
        if (!empty($_POST['senha'])) {
            if (strlen($_POST['senha']) < 6) {
                $erros[] = "A senha deve ter pelo menos 6 caracteres";
            } else {
                $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, nivel_acesso = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $nome, $email, $senha_hash, $nivel_acesso, $id);
            }
        } else {
            $sql = "UPDATE usuarios SET nome = ?, email = ?, nivel_acesso = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $nivel_acesso, $id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Usuário atualizado com sucesso!";
            header("Location: lista_usuarios.php");
            exit();
        } else {
            $erros[] = "Erro ao atualizar usuário: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Editar Usuário</h2>
    </div>

    <div class="w3-container">
        <?php if (!empty($erros)): ?>
            <div class="w3-panel w3-red">
                <h3>Erros encontrados:</h3>
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="w3-container" method="post">
            <label class="w3-text-blue"><b>Nome</b></label>
            <input class="w3-input w3-border" type="text" name="nome" 
                   value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>

            <label class="w3-text-blue"><b>Email</b></label>
            <input class="w3-input w3-border" type="email" name="email" 
                   value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

            <label class="w3-text-blue"><b>Nova Senha (deixe em branco para manter a atual)</b></label>
            <input class="w3-input w3-border" type="password" name="senha">

            <label class="w3-text-blue"><b>Nível de Acesso</b></label>
            <select class="w3-select w3-border" name="nivel_acesso" required>
                <option value="administrador" <?php echo $usuario['nivel_acesso'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                <option value="usuario" <?php echo $usuario['nivel_acesso'] == 'usuario' ? 'selected' : ''; ?>>Usuário</option>
                <option value="visualizador" <?php echo $usuario['nivel_acesso'] == 'visualizador' ? 'selected' : ''; ?>>Visualizador</option>
            </select>

            <button class="w3-btn w3-blue w3-margin-top" type="submit">Atualizar Usuário</button>
            <a href="lista_usuarios.php" class="w3-btn w3-gray w3-margin-top">Cancelar</a>
        </form>
    </div>

</body>
</html>