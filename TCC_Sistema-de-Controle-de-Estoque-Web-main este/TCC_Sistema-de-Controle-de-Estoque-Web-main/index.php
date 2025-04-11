<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Controle de Estoque</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Controle de Estoque</h2>
        <p>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?>!</p>
    </div>
<!-- Adicione este código no index.php antes do formulário de adicionar produto -->
<div class="w3-container">
    <?php if (isset($_SESSION['erros_produto']) && !empty($_SESSION['erros_produto'])): ?>
        <div class="w3-panel w3-red">
            <h3>Erros encontrados:</h3>
            <ul>
                <?php foreach ($_SESSION['erros_produto'] as $erro): ?>
                    <li><?php echo $erro; ?></li>
                <?php endforeach; ?>
                <?php unset($_SESSION['erros_produto']); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="w3-panel w3-green">
            <p><?php echo $_SESSION['mensagem']; ?></p>
            <?php unset($_SESSION['mensagem']); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modifique o formulário para manter os valores após erro de validação -->
<div class="w3-container">
    <h3>Adicionar Novo Produto</h3>
    <form class="w3-container" action="adicionar_produto.php" method="post">
        <label class="w3-text-blue"><b>Nome</b></label>
        <input class="w3-input w3-border" type="text" name="nome" 
               value="<?php echo isset($_SESSION['dados_produto']['nome']) ? htmlspecialchars($_SESSION['dados_produto']['nome']) : ''; ?>" required>

        <label class="w3-text-blue"><b>Quantidade</b></label>
        <input class="w3-input w3-border" type="number" name="quantidade" 
               value="<?php echo isset($_SESSION['dados_produto']['quantidade']) ? htmlspecialchars($_SESSION['dados_produto']['quantidade']) : ''; ?>" required>

        <label class="w3-text-blue"><b>Preço</b></label>
        <input class="w3-input w3-border" type="text" name="preco" 
               value="<?php echo isset($_SESSION['dados_produto']['preco']) ? htmlspecialchars($_SESSION['dados_produto']['preco']) : ''; ?>" required>

        <button class="w3-btn w3-blue" type="submit">Adicionar Produto</button>
    </form>
    <?php unset($_SESSION['dados_produto']); ?>
</div>
   

<div class="w3-container w3-margin-top">
    <a href="lista_produtos.php" class="w3-button w3-blue">Ver Lista Completa de Produtos</a>
    <a href="lista_fornecedores.php" class="w3-button w3-blue">Gerenciar Fornecedores</a>
    <?php if ($_SESSION['nivel_acesso'] == 'administrador'): ?>
        <a href="admin_dashboard.php" class="w3-button w3-green">Painel do Administrador</a>
    <?php endif; ?>
    <a href="logout.php" class="w3-button w3-red">Sair</a>
</div>

</body>
</html>