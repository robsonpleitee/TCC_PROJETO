## 4. Criar um arquivo para criar as tabelas de logs

```php
<?php
// Incluir conexão com o banco de dados
include 'conexao.php';

echo "<h1>Criação de Tabelas de Logs</h1>";

// Primeiro verificar se as tabelas existem e removê-las se for o caso
$tabelas = ['logs_acesso', 'logs_operacoes'];

foreach ($tabelas as $tabela) {
    $sql = "DROP TABLE IF EXISTS $tabela";
    if ($conn->query($sql)) {
        echo "<p>Tabela $tabela removida (se existia).</p>";
    } else {
        echo "<p>Erro ao tentar remover tabela $tabela: " . $conn->error . "</p>";
    }
}

// SQL para criar as tabelas de logs
$sql_criar_tabelas = "
-- Criação da tabela de logs de acesso
CREATE TABLE IF NOT EXISTS logs_acesso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(50) NOT NULL,
    detalhes TEXT,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Criação da tabela de logs de operações
CREATE TABLE IF NOT EXISTS logs_operacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    tipo_operacao VARCHAR(50) NOT NULL,
    tabela_afetada VARCHAR(50) NOT NULL,
    registro_id INT,
    detalhes TEXT,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);";

// Executar o SQL
if ($conn->multi_query($sql_criar_tabelas)) {
    echo "<p style='color: green;'>✅ Tabelas de logs criadas com sucesso!</p>";
    
    // Limpar resultados para executar mais queries
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    // Inserir alguns registros de exemplo para teste
    $sql_exemplos = "
    -- Exemplo de logs de acesso
    INSERT INTO logs_acesso (usuario_id, acao, detalhes) VALUES 
    (1, 'login', 'Login realizado com sucesso. IP: 127.0.0.1'),
    (1, 'logout', 'Logout realizado. IP: 127.0.0.1'),
    (2, 'login', 'Login realizado com sucesso. IP: 127.0.0.1');
    
    -- Exemplo de logs de operações
    INSERT INTO logs_operacoes (usuario_id, tipo_operacao, tabela_afetada, registro_id, detalhes) VALUES 
    (1, 'inserir', 'produtos', 1, 'Produto Notebook Dell adicionado'),
    (1, 'atualizar', 'produtos', 1, 'Produto Notebook Dell atualizado'),
    (2, 'inserir', 'produtos', 2, 'Produto Mouse Wireless adicionado');";
    
    // Executar as inserções de exemplo
    if ($conn->multi_query($sql_exemplos)) {
        echo "<p style='color: green;'>✅ Registros de exemplo adicionados para teste!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Tabelas criadas, mas não foi possível adicionar registros de exemplo: " . $conn->error . "</p>";
    }
    
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #dff0d8; border-radius: 4px;'>";
    echo "<h2>Tabelas criadas com sucesso!</h2>";
    echo "<p>Agora você pode:</p>";
    echo "<a href='verificar_tabelas.php' style='display: inline-block; background-color: #5cb85c; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Verificar Tabelas</a>";
    echo "<a href='relatorios_avancados.php' style='display: inline-block; background-color: #f0ad4e; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Gerar Relatórios</a>";
    echo "<a href='index.php' style='display: inline-block; background-color: #337ab7; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Voltar ao Sistema</a>";
    echo "</div>";
} else {
    echo "<p style='color: red;'>❌ Erro ao criar tabelas de logs: " . $conn->error . "</p>";
}

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1 {
    color: #2196F3;
    border-bottom: 2px solid #2196F3;
    padding-bottom: 10px;
}
p {
    margin: 10px 0;
}
a {
    color: #2196F3;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>