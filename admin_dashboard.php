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
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }
        .menu-section {
            margin: 20px 0;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .menu-title {
            margin-bottom: 15px;
            color: #2196F3;
            border-bottom: 2px solid #2196F3;
            padding-bottom: 5px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .w3-button {
            margin: 5px;
        }
    </style>
</head>
<body>

    <div class="w3-bar w3-blue">
        <span class="w3-bar-item w3-large">Painel Administrativo</span>
        <a href="admin_dashboard.php" class="w3-bar-item w3-button">Início</a>
        <a href="logout.php" class="w3-bar-item w3-button w3-right">Sair</a>
        <span class="w3-bar-item w3-right">Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'Administrador'); ?>!</span>
    </div>

    <div class="dashboard-container">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="w3-panel w3-green">
                <p><?php echo htmlspecialchars($_SESSION['mensagem']); ?></p>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <div class="menu-section">
            <h3 class="menu-title">Gestão de Usuários</h3>
            <div class="button-group">
                <a href="cadastrar_usuario.php" class="w3-button w3-green">
                    <i class="fas fa-user-plus"></i> Cadastrar Usuário
                </a>
                <a href="lista_usuarios.php" class="w3-button w3-orange">
                    <i class="fas fa-users"></i> Gerenciar Usuários
                </a>
            </div>
        </div>

        <div class="menu-section">
            <h3 class="menu-title">Gestão de Produtos</h3>
            <div class="button-group">
                <a href="cadastrar_produto.php" class="w3-button w3-blue">
                    <i class="fas fa-plus"></i> Novo Produto
                </a>
                <a href="lista_produtos.php" class="w3-button w3-blue">
                    <i class="fas fa-list"></i> Ver Produtos
                </a>
                <a href="relatorio_produtos.php" class="w3-button w3-blue">
                    <i class="fas fa-chart-bar"></i> Relatórios
                </a>
            </div>
        </div>

        <div class="menu-section">
            <h3 class="menu-title">Sistema</h3>
            <div class="button-group">
                <a href="configuracoes.php" class="w3-button w3-grey">
                    <i class="fas fa-cog"></i> Configurações
                </a>
                <a href="backup.php" class="w3-button w3-grey">
                    <i class="fas fa-database"></i> Backup
                </a>
                <a href="logs.php" class="w3-button w3-grey">
                    <i class="fas fa-history"></i> Logs do Sistema
                </a>
            </div>
        </div>
    </div>

    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</body>
</html>