<?php
session_start();
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
    <title>Lista de Usuários</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Lista de Usuários</h2>
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
                    <th>Email</th>
                    <th>Nível de Acesso</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para buscar os usuários
                $sql = "SELECT id, nome, email, nivel_acesso FROM usuarios ORDER BY nome";
                $result = $conn->query($sql);

                // Verifica se há resultados e exibe os dados
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["nome"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . ucfirst($row["nivel_acesso"]) . "</td>";
                        echo "<td>
                                <a href='editar_usuario.php?id=" . $row["id"] . "' class='w3-button w3-blue w3-small'>Editar</a>
                                <a href='excluir_usuario.php?id=" . $row["id"] . "' class='w3-button w3-red w3-small' onclick='return confirm(\"Tem certeza que deseja excluir este usuário?\")'>Excluir</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhum usuário encontrado</td></tr>";
                }

                // Fecha a conexão com o banco de dados
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="w3-container w3-margin-top">
        <a href="cadastrar_usuario.php" class="w3-button w3-green">Cadastrar Novo Usuário</a>
        <a href="admin_dashboard.php" class="w3-button w3-blue">Voltar para o Dashboard</a>
    </div>

</body>
</html> 