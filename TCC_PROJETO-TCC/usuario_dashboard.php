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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .dashboard-container {
            max-width: 1000px;
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
            flex-wrap: wrap;
            gap: 10px;
        }
        .feature-card {
            margin: 10px 0;
            padding: 15px;
            border-radius: 4px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="w3-bar w3-blue">
        <span class="w3-bar-item w3-large">Controle de Estoque</span>
        <a href="index.php" class="w3-bar-item w3-button">Início</a>
        <a href="logout.php" class="w3-bar-item w3-button w3-right">Sair</a>
        <span class="w3-bar-item w3-right">Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</span>
    </div>

    <div class="dashboard-container">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="w3-panel w3-green">
                <p><?php echo htmlspecialchars($_SESSION['mensagem']); ?></p>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <div class="w3-container w3-blue">
            <h2>Dashboard do Usuário</h2>
        </div>

        <div class="menu-section">
            <h3 class="menu-title"><i class="fas fa-box"></i> Gestão de Produtos</h3>
            <div class="w3-row-padding">
                <div class="w3-third">
                    <div class="feature-card">
                        <h4><i class="fas fa-plus-circle"></i> Adicionar Produto</h4>
                        <p>Cadastre novos produtos no sistema de estoque.</p>
                        <a href="index.php" class="w3-button w3-green">Adicionar Produto</a>
                    </div>
                </div>
                <div class="w3-third">
                    <div class="feature-card">
                        <h4><i class="fas fa-edit"></i> Gerenciar Produtos</h4>
                        <p>Visualize, edite e exclua produtos existentes.</p>
                        <a href="lista_produtos.php" class="w3-button w3-blue">Listar Produtos</a>
                    </div>
                </div>
                <div class="w3-third">
                    <div class="feature-card">
                        <h4><i class="fas fa-chart-bar"></i> Relatórios</h4>
                        <p>Gere relatórios sobre o estoque atual.</p>
                        <a href="relatorio_produtos.php" class="w3-button w3-amber">Gerar Relatórios</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w3-panel w3-pale-blue w3-leftbar w3-border-blue">
            <p><i class="fas fa-info-circle"></i> Como usuário padrão, você pode adicionar produtos, gerenciar o estoque e gerar relatórios.</p>
        </div>
    </div>
</body>
</html>