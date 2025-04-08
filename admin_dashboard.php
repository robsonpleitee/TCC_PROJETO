<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Administrador</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Bem-vindo, Administrador!</h2>
    </div>

    <div class="w3-container">
        <p>Você tem acesso total ao sistema.</p>
        <a href="lista_produtos.php" class="w3-button w3-blue">Ver Produtos</a>
        <a href="cadastrar_usuario.php" class="w3-button w3-green">Cadastrar Usuário</a>
        <a href="lista_usuarios.php" class="w3-button w3-orange">Gerenciar Usuários</a>
        <a href="logout.php" class="w3-button w3-red">Sair</a>
    </div>

</body>
</html>