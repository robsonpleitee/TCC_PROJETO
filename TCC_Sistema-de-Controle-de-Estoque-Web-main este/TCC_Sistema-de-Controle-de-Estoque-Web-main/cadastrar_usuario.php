<?php
session_start();

// Verifica se o usuário está logado e é um administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Recupera mensagens de erro e dados do formulário
$erros = isset($_SESSION['erros']) ? $_SESSION['erros'] : [];
$dados_form = isset($_SESSION['dados_form']) ? $_SESSION['dados_form'] : [];
$mensagem = isset($_SESSION['mensagem']) ? $_SESSION['mensagem'] : '';

// Limpa as mensagens da sessão
unset($_SESSION['erros']);
unset($_SESSION['dados_form']);
unset($_SESSION['mensagem']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        .w3-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
        .success-message {
            color: green;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Cadastrar Novo Usuário</h2>
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

        <?php if (!empty($mensagem)): ?>
            <div class="w3-panel w3-green">
                <p><?php echo htmlspecialchars($mensagem); ?></p>
            </div>
        <?php endif; ?>

        <form class="w3-container" action="salvar_usuario.php" method="post" onsubmit="return validarFormulario()">
            <div class="form-group">
                <label class="w3-text-blue"><b>Nome Completo</b></label>
                <input class="w3-input w3-border" type="text" name="nome" id="nome" value="<?php echo isset($dados_form['nome']) ? htmlspecialchars($dados_form['nome']) : ''; ?>" required>
                <div class="error-message" id="nome-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Email</b></label>
                <input class="w3-input w3-border" type="email" name="email" id="email" value="<?php echo isset($dados_form['email']) ? htmlspecialchars($dados_form['email']) : ''; ?>" required>
                <div class="error-message" id="email-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Senha</b></label>
                <input class="w3-input w3-border" type="password" name="senha" id="senha" required>
                <div class="error-message" id="senha-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Confirmar Senha</b></label>
                <input class="w3-input w3-border" type="password" name="confirmar_senha" id="confirmar_senha" required>
                <div class="error-message" id="confirmar-senha-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Nível de Acesso</b></label>
                <select class="w3-select w3-border" name="nivel_acesso" required>
                    <option value="">Selecione o nível de acesso</option>
                    <option value="usuario" <?php echo isset($dados_form['nivel_acesso']) && $dados_form['nivel_acesso'] == 'usuario' ? 'selected' : ''; ?>>Usuário</option>
                    <option value="visualizador" <?php echo isset($dados_form['nivel_acesso']) && $dados_form['nivel_acesso'] == 'visualizador' ? 'selected' : ''; ?>>Visualizador</option>
                </select>
            </div>

            <div class="form-group">
                <button class="w3-btn w3-blue" type="submit">Cadastrar Usuário</button>
                <a href="admin_dashboard.php" class="w3-btn w3-gray">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        function validarFormulario() {
            let isValid = true;
            
            // Limpar mensagens de erro anteriores
            document.querySelectorAll('.error-message').forEach(elem => elem.textContent = '');
            
            // Validar nome
            const nome = document.getElementById('nome').value;
            if (nome.length < 3) {
                document.getElementById('nome-error').textContent = 'O nome deve ter pelo menos 3 caracteres';
                isValid = false;
            }
            
            // Validar email
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email-error').textContent = 'Email inválido';
                isValid = false;
            }
            
            // Validar senha
            const senha = document.getElementById('senha').value;
            if (senha.length < 6) {
                document.getElementById('senha-error').textContent = 'A senha deve ter pelo menos 6 caracteres';
                isValid = false;
            }
            
            // Validar confirmação de senha
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            if (senha !== confirmarSenha) {
                document.getElementById('confirmar-senha-error').textContent = 'As senhas não coincidem';
                isValid = false;
            }
            
            return isValid;
        }
    </script>

</body>
</html>