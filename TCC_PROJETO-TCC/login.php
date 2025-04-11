<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <?php
    session_start();
    ?>
    <div class="w3-container w3-blue">
        <h2>Login</h2>
    </div>

    <div class="w3-container">
        <?php if (isset($_SESSION['erros_login']) && !empty($_SESSION['erros_login'])): ?>
            <div class="w3-panel w3-red">
                <ul>
                    <?php foreach ($_SESSION['erros_login'] as $erro): ?>
                        <li><?php echo $erro; ?></li>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['erros_login']); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="w3-container" action="autenticar.php" method="post">
            <label class="w3-text-blue"><b>Email</b></label>
            <input class="w3-input w3-border" type="email" name="email" 
                   value="<?php echo isset($_SESSION['email_login']) ? htmlspecialchars($_SESSION['email_login']) : ''; ?>" required>
            <?php unset($_SESSION['email_login']); ?>

            <label class="w3-text-blue"><b>Senha</b></label>
            <input class="w3-input w3-border" type="password" name="senha" required>

            <button class="w3-btn w3-blue w3-margin-top" type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>