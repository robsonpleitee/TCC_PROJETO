<?php
session_start();
// Verificar se o usuário está logado (qualquer nível de acesso é permitido)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Código para gerar relatórios
// ...

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios de Produtos</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container w3-blue">
        <h2>Relatórios de Produtos</h2>
    </div>

    <div class="w3-container">
        <!-- Conteúdo do relatório aqui -->
        
        <!-- Botões de navegação adaptados conforme o tipo de usuário -->
        <div class="w3-padding-16">
            <?php if ($_SESSION['nivel_acesso'] == 'administrador'): ?>
                <a href="admin_dashboard.php" class="w3-button w3-blue">Voltar para o Dashboard</a>
            <?php elseif ($_SESSION['nivel_acesso'] == 'usuario'): ?>
                <a href="usuario_dashboard.php" class="w3-button w3-blue">Voltar para o Dashboard</a>
            <?php else: ?>
                <a href="visualizador_dashboard.php" class="w3-button w3-blue">Voltar para o Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>