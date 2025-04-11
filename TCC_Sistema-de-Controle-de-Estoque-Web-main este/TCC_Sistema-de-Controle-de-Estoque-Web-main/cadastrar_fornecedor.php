<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] == 'visualizador') {
    header("Location: login.php");
    exit();
}

// Recupera mensagens de erro e dados do formulário
$erros = isset($_SESSION['erros_fornecedor']) ? $_SESSION['erros_fornecedor'] : [];
$dados_form = isset($_SESSION['dados_fornecedor']) ? $_SESSION['dados_fornecedor'] : [];
$mensagem = isset($_SESSION['mensagem']) ? $_SESSION['mensagem'] : '';

// Limpa as mensagens da sessão
unset($_SESSION['erros_fornecedor']);
unset($_SESSION['dados_fornecedor']);
unset($_SESSION['mensagem']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fornecedor</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        .w3-container {
            max-width: 800px;
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
        <h2>Cadastrar Novo Fornecedor</h2>
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

        <form class="w3-container" action="salvar_fornecedor.php" method="post" onsubmit="return validarFormulario()">
            <div class="form-group">
                <label class="w3-text-blue"><b>CNPJ</b></label>
                <input class="w3-input w3-border" type="text" name="cnpj" id="cnpj" 
                       value="<?php echo isset($dados_form['cnpj']) ? htmlspecialchars($dados_form['cnpj']) : ''; ?>" 
                       required maxlength="14" placeholder="Digite apenas números">
                <div class="error-message" id="cnpj-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Nome</b></label>
                <input class="w3-input w3-border" type="text" name="nome" id="nome" 
                       value="<?php echo isset($dados_form['nome']) ? htmlspecialchars($dados_form['nome']) : ''; ?>" required>
                <div class="error-message" id="nome-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Rua</b></label>
                <input class="w3-input w3-border" type="text" name="rua" id="rua" 
                       value="<?php echo isset($dados_form['rua']) ? htmlspecialchars($dados_form['rua']) : ''; ?>" required>
                <div class="error-message" id="rua-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Número</b></label>
                <input class="w3-input w3-border" type="text" name="numero" id="numero" 
                       value="<?php echo isset($dados_form['numero']) ? htmlspecialchars($dados_form['numero']) : ''; ?>" required>
                <div class="error-message" id="numero-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Bairro</b></label>
                <input class="w3-input w3-border" type="text" name="bairro" id="bairro" 
                       value="<?php echo isset($dados_form['bairro']) ? htmlspecialchars($dados_form['bairro']) : ''; ?>" required>
                <div class="error-message" id="bairro-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>Cidade</b></label>
                <input class="w3-input w3-border" type="text" name="cidade" id="cidade" 
                       value="<?php echo isset($dados_form['cidade']) ? htmlspecialchars($dados_form['cidade']) : ''; ?>" required>
                <div class="error-message" id="cidade-error"></div>
            </div>

            <div class="form-group">
                <label class="w3-text-blue"><b>UF</b></label>
                <select class="w3-select w3-border" name="uf" id="uf" required>
                    <option value="">Selecione o estado</option>
                    <?php
                    $estados = array(
                        'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas', 'BA' => 'Bahia',
                        'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo', 'GO' => 'Goiás',
                        'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                        'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná', 'PE' => 'Pernambuco', 'PI' => 'Piauí',
                        'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                        'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina', 'SP' => 'São Paulo',
                        'SE' => 'Sergipe', 'TO' => 'Tocantins'
                    );
                    foreach ($estados as $sigla => $nome) {
                        $selected = (isset($dados_form['uf']) && $dados_form['uf'] == $sigla) ? 'selected' : '';
                        echo "<option value='$sigla' $selected>$nome</option>";
                    }
                    ?>
                </select>
                <div class="error-message" id="uf-error"></div>
            </div>

            <div class="form-group">
                <button class="w3-btn w3-blue" type="submit">Cadastrar Fornecedor</button>
                <a href="lista_fornecedores.php" class="w3-btn w3-gray">Ver Lista de Fornecedores</a>
                <a href="index.php" class="w3-btn w3-gray">Voltar</a>
            </div>
        </form>
    </div>

    <script>
        function validarFormulario() {
            let isValid = true;
            
            // Limpar mensagens de erro anteriores
            document.querySelectorAll('.error-message').forEach(elem => elem.textContent = '');
            
            // Validar CNPJ
            const cnpj = document.getElementById('cnpj').value;
            if (cnpj.length !== 14) {
                document.getElementById('cnpj-error').textContent = 'CNPJ deve ter 14 dígitos';
                isValid = false;
            }
            
            // Validar nome
            const nome = document.getElementById('nome').value;
            if (nome.length < 3) {
                document.getElementById('nome-error').textContent = 'O nome deve ter pelo menos 3 caracteres';
                isValid = false;
            }
            
            // Validar rua
            const rua = document.getElementById('rua').value;
            if (rua.length < 3) {
                document.getElementById('rua-error').textContent = 'A rua deve ter pelo menos 3 caracteres';
                isValid = false;
            }
            
            // Validar número
            const numero = document.getElementById('numero').value;
            if (numero.length < 1) {
                document.getElementById('numero-error').textContent = 'O número é obrigatório';
                isValid = false;
            }
            
            // Validar bairro
            const bairro = document.getElementById('bairro').value;
            if (bairro.length < 3) {
                document.getElementById('bairro-error').textContent = 'O bairro deve ter pelo menos 3 caracteres';
                isValid = false;
            }
            
            // Validar cidade
            const cidade = document.getElementById('cidade').value;
            if (cidade.length < 3) {
                document.getElementById('cidade-error').textContent = 'A cidade deve ter pelo menos 3 caracteres';
                isValid = false;
            }
            
            // Validar UF
            const uf = document.getElementById('uf').value;
            if (!uf) {
                document.getElementById('uf-error').textContent = 'Selecione um estado';
                isValid = false;
            }
            
            return isValid;
        }

        // Máscara para o CNPJ
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 14) value = value.slice(0, 14);
            e.target.value = value;
        });
    </script>

</body>
</html> 