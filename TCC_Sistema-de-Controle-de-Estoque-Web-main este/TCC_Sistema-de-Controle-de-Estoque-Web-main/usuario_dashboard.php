<?php
// Verifica se a sessão já foi iniciada antes de chamar session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e é um usuário comum
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'usuario') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Usuário</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Bem-vindo, Usuário!</h2>
    </div>

    <div class="w3-container">
        <p>Você pode adicionar e editar produtos.</p>
        <a href="lista_produtos.php" class="w3-button w3-blue">Ver Produtos</a>
        <a href="logout.php" class="w3-button w3-red">Sair</a>
    </div>

</body>
</html>