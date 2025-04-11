<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] == 'visualizador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    header("Location: lista_produtos.php");
    exit();
}

$id = (int)$_GET['id'];

// Busca os dados do produto
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: lista_produtos.php");
    exit();
}

$produto = $result->fetch_assoc();

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = [];
    
    // Validação dos dados
    if (empty($_POST['nome'])) {
        $erros[] = "O nome do produto é obrigatório";
    }
    
    if (!isset($_POST['quantidade']) || !is_numeric($_POST['quantidade']) || $_POST['quantidade'] < 0) {
        $erros[] = "A quantidade deve ser um número positivo";
    }
    
    if (empty($_POST['preco']) || !is_numeric(str_replace(',', '.', $_POST['preco'])) || floatval(str_replace(',', '.', $_POST['preco'])) <= 0) {
        $erros[] = "O preço deve ser um valor numérico positivo";
    }
    
    if (empty($erros)) {
        $nome = htmlspecialchars(trim($_POST['nome']));
        $quantidade = (int)$_POST['quantidade'];
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        
        $sql = "UPDATE produtos SET nome = ?, quantidade = ?, preco = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidi", $nome, $quantidade, $preco, $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Produto atualizado com sucesso!";
            header("Location: lista_produtos.php");
            exit();
        } else {
            $erros[] = "Erro ao atualizar produto: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Editar Produto</h2>
    </div>

    <div class="w3-container">
        <?php if (!empty($erros)): ?>
            <div class="w3-panel w3-red">
                <h3>Erros encontrados:</h3>
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="w3-container" method="post">
            <label class="w3-text-blue"><b>Nome</b></label>
            <input class="w3-input w3-border" type="text" name="nome" 
                   value="<?php echo htmlspecialchars($produto['nome']); ?>" required>

            <label class="w3-text-blue"><b>Quantidade</b></label>
            <input class="w3-input w3-border" type="number" name="quantidade" 
                   value="<?php echo htmlspecialchars($produto['quantidade']); ?>" required>

            <label class="w3-text-blue"><b>Preço</b></label>
            <input class="w3-input w3-border" type="text" name="preco" 
                   value="<?php echo number_format($produto['preco'], 2, ',', '.'); ?>" required>

            <button class="w3-btn w3-blue w3-margin-top" type="submit">Atualizar Produto</button>
            <a href="lista_produtos.php" class="w3-btn w3-gray w3-margin-top">Cancelar</a>
        </form>
    </div>

</body>
</html> 