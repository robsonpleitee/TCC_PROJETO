<?php
// Verifica se a senha foi enviada via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha'])) {
    $senha = $_POST['senha'];

    // Verifica se a função password_hash está disponível
    if (function_exists('password_hash')) {
        // Gera o hash da senha
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        // Exibe o hash gerado
        echo "Senha: " . htmlspecialchars($senha) . "<br>";
        echo "Hash gerado: " . $hash;
    } else {
        echo "A função password_hash não está disponível. Verifique a versão do PHP.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Hash de Senha</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Gerar Hash de Senha</h2>
    </div>

    <div class="w3-container">
        <form class="w3-container" method="post">
            <label class="w3-text-blue"><b>Senha</b></label>
            <input class="w3-input w3-border" type="password" name="senha" required>
            <button class="w3-btn w3-blue w3-margin-top" type="submit">Gerar Hash</button>
        </form>
    </div>

</body>
</html>