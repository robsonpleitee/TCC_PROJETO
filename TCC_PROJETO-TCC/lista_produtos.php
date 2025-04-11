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

        <table class="w3-table w3-striped">
            <thead>
                <tr>
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
                $sql = "SELECT id, nome, quantidade, preco FROM produtos";
                $result = $conn->query($sql);

                // Verifica se há resultados e exibe os dados
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nome"] . "</td>";
                        echo "<td>" . $row["quantidade"] . "</td>";
                        echo "<td>R$ " . number_format($row["preco"], 2, ',', '.') . "</td>";
                        if ($_SESSION['nivel_acesso'] != 'visualizador') {
                            echo "<td>
                                    <a href='editar_produto.php?id=" . $row["id"] . "' class='w3-button w3-blue w3-small'>Editar</a>
                                    <a href='excluir_produto.php?id=" . $row["id"] . "' class='w3-button w3-red w3-small' onclick='return confirm(\"Tem certeza que deseja excluir este produto?\")'>Excluir</a>
                                  </td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Nenhum produto encontrado</td></tr>";
                }

                // Fecha a conexão com o banco de dados
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="w3-container w3-margin-top">
        <a href="index.php" class="w3-button w3-blue">Voltar para o Dashboard</a>
    </div>

</body>
</html>