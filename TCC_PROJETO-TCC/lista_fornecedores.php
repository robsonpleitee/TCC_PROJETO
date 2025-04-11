<?php
session_start();
// Corrigir para permitir apenas administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
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
                <p><?php echo htmlspecialchars($_SESSION['mensagem'] ?? ''); ?></p>
                <?php unset($_SESSION['mensagem']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class="w3-panel w3-red">
                <p><?php echo htmlspecialchars($_SESSION['erro'] ?? ''); ?></p>
                <?php unset($_SESSION['erro']); ?>
            </div>
        <?php endif; ?>

        <table class="w3-table w3-striped w3-bordered w3-card-4">
            <thead>
                <tr class="w3-blue">
                    <th>ID</th>
                    <th>CNPJ</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Data Cadastro</th>
                    <?php if ($_SESSION['nivel_acesso'] != 'visualizador'): ?>
                        <th>Ações</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id, cnpj, nome, telefone, rua, numero, bairro, cidade, uf, data_cadastro 
                        FROM fornecedores 
                        ORDER BY nome";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $endereco = sprintf("%s, %s - %s<br>%s - %s",
                            htmlspecialchars($row['rua'] ?? ''),
                            htmlspecialchars($row['numero'] ?? ''),
                            htmlspecialchars($row['bairro'] ?? ''),
                            htmlspecialchars($row['cidade'] ?? ''),
                            htmlspecialchars($row['uf'] ?? '')
                        );
                        ?>
                        <tr>
                            <td><?php echo (int)$row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['cnpj'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['nome'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['telefone'] ?? ''); ?></td>
                            <td><?php echo $endereco; ?></td>
                            <td><?php echo $row['data_cadastro'] ? date('d/m/Y H:i', strtotime($row['data_cadastro'])) : ''; ?></td>
                            <?php if ($_SESSION['nivel_acesso'] != 'visualizador'): ?>
                                <td>
                                    <a href="editar_fornecedor.php?id=<?php echo (int)$row['id']; ?>" 
                                       class="w3-button w3-blue w3-small">Editar</a>
                                    <a href="excluir_fornecedor.php?id=<?php echo (int)$row['id']; ?>" 
                                       class="w3-button w3-red w3-small"
                                       onclick="return confirm('Tem certeza que deseja excluir este fornecedor?')">
                                        Excluir
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="7" class="w3-center">Nenhum fornecedor encontrado</td>
                    </tr>
                    <?php
                }
                $conn->close();
                ?>
            </tbody>
        </table>

        <div class="w3-container w3-padding-16">
            <a href="cadastrar_fornecedor.php" class="w3-button w3-green">Cadastrar Novo Fornecedor</a>
            <a href="index.php" class="w3-button w3-blue">Voltar para o Dashboard</a>
        </div>
    </div>
</body>
</html>