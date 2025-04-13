<?php
session_start();
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
    <title>Lista de Produtos em Estoque</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Lista de Produtos em Estoque</h2>
    </div>

    <div class="w3-container">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="w3-panel w3-green">
                <p><?php echo $_SESSION['mensagem']; ?></p>
                <?php unset($_SESSION['mensagem']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="w3-panel w3-red">
                <p><?php echo $_SESSION['erro']; ?></p>
                <?php unset($_SESSION['erro']); ?>
            </div>
        <?php endif; ?>

        <table class="w3-table w3-striped w3-bordered w3-hoverable w3-card-4">
            <thead>
                <tr class="w3-blue">
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <?php if ($_SESSION['nivel_acesso'] != 'visualizador'): ?>
                    <th>Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Inclui o arquivo de conexão
                include 'conexao.php';

                // Consulta para buscar os produtos
                $sql = "SELECT id, nome, quantidade, preco FROM produtos ORDER BY nome";
                $result = $conn->query($sql);

                // Verifica se há resultados e exibe os dados
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["nome"]) . "</td>";
                        echo "<td>" . $row["quantidade"] . "</td>";
                        echo "<td>R$ " . number_format($row["preco"], 2, ',', '.') . "</td>";
                        
                        // Adiciona botões de ação apenas para administradores e usuários comuns
                        if ($_SESSION['nivel_acesso'] != 'visualizador') {
                            echo "<td>";
                            echo "<a href='editar_produto.php?id=" . $row["id"] . "' class='w3-button w3-small w3-blue'><i class='fas fa-edit'></i> Editar</a> ";
                            echo "<a href='excluir_produto.php?id=" . $row["id"] . "' class='w3-button w3-small w3-red' onclick=\"return confirm('Tem certeza que deseja excluir este produto?');\"><i class='fas fa-trash'></i> Excluir</a>";
                            echo "</td>";
                        }
                        
                        echo "</tr>";
                    }
                } else {
                    $colspan = ($_SESSION['nivel_acesso'] != 'visualizador') ? 5 : 4;
                    echo "<tr><td colspan='$colspan' class='w3-center'>Nenhum produto encontrado</td></tr>";
                }

                // Fecha a conexão com o banco de dados
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="w3-container w3-margin-top">
        <?php if ($_SESSION['nivel_acesso'] != 'visualizador'): ?>
            <a href="index.php" class="w3-button w3-green"><i class="fas fa-plus"></i> Adicionar Novo Produto</a>
        <?php endif; ?>
        
        <?php if ($_SESSION['nivel_acesso'] == 'administrador'): ?>
            <a href="admin_dashboard.php" class="w3-button w3-blue"><i class="fas fa-tachometer-alt"></i> Voltar para o Dashboard</a>
        <?php elseif ($_SESSION['nivel_acesso'] == 'usuario'): ?>
            <a href="usuario_dashboard.php" class="w3-button w3-blue"><i class="fas fa-tachometer-alt"></i> Voltar para o Dashboard</a>
        <?php else: ?>
            <a href="visualizador_dashboard.php" class="w3-button w3-blue"><i class="fas fa-tachometer-alt"></i> Voltar para o Dashboard</a>
        <?php endif; ?>
    </div>

</body>
</html>