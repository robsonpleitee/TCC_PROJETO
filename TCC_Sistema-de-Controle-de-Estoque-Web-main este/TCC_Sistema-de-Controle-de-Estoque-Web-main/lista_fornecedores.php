<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

include 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Fornecedores</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Lista de Fornecedores</h2>
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
                    <th>CNPJ</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Data Cadastro</th>
                    <?php if ($_SESSION['nivel_acesso'] != 'visualizador'): ?>
                    <th>Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para buscar os fornecedores
                $sql = "SELECT id, cnpj, nome, rua, numero, bairro, cidade, uf, data_cadastro FROM fornecedores ORDER BY nome";
                $result = $conn->query($sql);

                // Verifica se há resultados e exibe os dados
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["cnpj"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["nome"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["rua"]) . ", " . 
                             htmlspecialchars($row["numero"]) . " - " . 
                             htmlspecialchars($row["bairro"]) . "<br>" . 
                             htmlspecialchars($row["cidade"]) . " - " . 
                             htmlspecialchars($row["uf"]) . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($row["data_cadastro"])) . "</td>";
                        if ($_SESSION['nivel_acesso'] != 'visualizador') {
                            echo "<td>
                                    <a href='editar_fornecedor.php?id=" . $row["id"] . "' class='w3-button w3-blue w3-small'>Editar</a>
                                    <a href='excluir_fornecedor.php?id=" . $row["id"] . "' class='w3-button w3-red w3-small' onclick='return confirm(\"Tem certeza que deseja excluir este fornecedor?\")'>Excluir</a>
                                  </td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Nenhum fornecedor encontrado</td></tr>";
                }

                // Fecha a conexão com o banco de dados
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="w3-container w3-margin-top">
        <a href="cadastrar_fornecedor.php" class="w3-button w3-green">Cadastrar Novo Fornecedor</a>
        <a href="index.php" class="w3-button w3-blue">Voltar para o Dashboard</a>
    </div>

</body>
</html> 