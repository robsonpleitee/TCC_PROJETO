<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] == 'visualizador') {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

// Verifica se o ID foi fornecido
if (!isset($_GET['id'])) {
    header("Location: lista_fornecedores.php");
    exit();
}

$id = (int)$_GET['id'];

// Busca os dados do fornecedor
$sql = "SELECT * FROM fornecedores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: lista_fornecedores.php");
    exit();
}

$fornecedor = $result->fetch_assoc();

// Processa o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = [];
    
    // Validação dos dados
    $cnpj = preg_replace("/\D/", "", trim($_POST['cnpj']));
    $nome = trim($_POST['nome']);
    $telefone = preg_replace("/\D/", "", trim($_POST['telefone']));
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $uf = trim($_POST['uf']);
    
    // Validar CNPJ
    if (strlen($cnpj) !== 14) {
        $erros[] = "CNPJ deve ter 14 dígitos";
    } else {
        // Verificar se o CNPJ já existe para outro fornecedor
        $sql = "SELECT id FROM fornecedores WHERE cnpj = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $cnpj, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $erros[] = "Este CNPJ já está cadastrado para outro fornecedor";
        }
    }
    
    // Validar telefone
    if (!empty($telefone) && !preg_match("/^\d{10,11}$/", $telefone)) {
        $erros[] = "Telefone inválido. Digite entre 10 e 11 dígitos";
    }
    
    // Validar nome
    if (strlen($nome) < 3) {
        $erros[] = "O nome deve ter pelo menos 3 caracteres";
    }
    
    // Validar rua
    if (strlen($rua) < 3) {
        $erros[] = "A rua deve ter pelo menos 3 caracteres";
    }
    
    // Validar número
    if (empty($numero)) {
        $erros[] = "O número é obrigatório";
    }
    
    // Validar bairro
    if (strlen($bairro) < 3) {
        $erros[] = "O bairro deve ter pelo menos 3 caracteres";
    }
    
    // Validar cidade
    if (strlen($cidade) < 3) {
        $erros[] = "A cidade deve ter pelo menos 3 caracteres";
    }
    
    // Validar UF
    $ufs_validos = array('AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO');
    if (!in_array($uf, $ufs_validos)) {
        $erros[] = "UF inválido";
    }
    
    if (empty($erros)) {
        $sql = "UPDATE fornecedores SET cnpj = ?, nome = ?, telefone = ?, rua = ?, numero = ?, bairro = ?, cidade = ?, uf = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $cnpj, $nome, $telefone, $rua, $numero, $bairro, $cidade, $uf, $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = "Fornecedor atualizado com sucesso!";
            header("Location: lista_fornecedores.php");
            exit();
        } else {
            $erros[] = "Erro ao atualizar fornecedor: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Fornecedor</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div class="w3-container w3-blue">
        <h2>Editar Fornecedor</h2>
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
            <label class="w3-text-blue"><b>CNPJ</b></label>
            <input class="w3-input w3-border" type="text" name="cnpj" id="cnpj" 
                   value="<?php echo htmlspecialchars($fornecedor['cnpj']); ?>" 
                   required maxlength="14" placeholder="Digite apenas números">

            <label class="w3-text-blue"><b>Nome</b></label>
            <input class="w3-input w3-border" type="text" name="nome" 
                   value="<?php echo htmlspecialchars($fornecedor['nome']); ?>" required>

            <label class="w3-text-blue"><b>Telefone</b></label>
            <input class="w3-input w3-border" type="text" name="telefone" 
                   value="<?php echo htmlspecialchars($fornecedor['telefone'] ?? ''); ?>" 
                   maxlength="11" placeholder="Digite apenas números">

            <label class="w3-text-blue"><b>Rua</b></label>
            <input class="w3-input w3-border" type="text" name="rua" 
                   value="<?php echo htmlspecialchars($fornecedor['rua']); ?>" required>

            <label class="w3-text-blue"><b>Número</b></label>
            <input class="w3-input w3-border" type="text" name="numero" 
                   value="<?php echo htmlspecialchars($fornecedor['numero']); ?>" required>

            <label class="w3-text-blue"><b>Bairro</b></label>
            <input class="w3-input w3-border" type="text" name="bairro" 
                   value="<?php echo htmlspecialchars($fornecedor['bairro']); ?>" required>

            <label class="w3-text-blue"><b>Cidade</b></label>
            <input class="w3-input w3-border" type="text" name="cidade" 
                   value="<?php echo htmlspecialchars($fornecedor['cidade']); ?>" required>

            <label class="w3-text-blue"><b>UF</b></label>
            <select class="w3-select w3-border" name="uf" required>
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
                    $selected = ($fornecedor['uf'] == $sigla) ? 'selected' : '';
                    echo "<option value='$sigla' $selected>$nome</option>";
                }
                ?>
            </select>

            <button class="w3-btn w3-blue w3-margin-top" type="submit">Atualizar Fornecedor</button>
            <a href="lista_fornecedores.php" class="w3-btn w3-gray w3-margin-top">Cancelar</a>
        </form>
    </div>

    <script>
        // Máscara para o CNPJ
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 14) value = value.slice(0, 14);
            e.target.value = value;
        });
    </script>

</body>
</html>