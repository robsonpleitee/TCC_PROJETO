<?php
include 'conexao.php';

echo "<h1>Verificação de Tabelas de Logs</h1>";

// Verificar se as tabelas existem
$tabelas = ['logs_acesso', 'logs_operacoes'];
$problemas = false;

foreach ($tabelas as $tabela) {
    $sql = "SHOW TABLES LIKE '$tabela'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 0) {
        echo "<p style='color: red;'>❌ A tabela <strong>$tabela</strong> não existe.</p>";
        $problemas = true;
    } else {
        echo "<p style='color: green;'>✅ A tabela <strong>$tabela</strong> existe.</p>";
        
        // Verificar estrutura da tabela
        $sql = "DESCRIBE $tabela";
        $result = $conn->query($sql);
        
        echo "<div style='margin-left: 20px;'>";
        echo "<p><strong>Colunas encontradas:</strong></p>";
        echo "<ul>";
        
        $campos_esperados = [
            'logs_acesso' => ['id', 'usuario_id', 'acao', 'detalhes', 'data_hora'],
            'logs_operacoes' => ['id', 'usuario_id', 'tipo_operacao', 'tabela_afetada', 'registro_id', 'detalhes', 'data_hora']
        ];
        
        $campos_encontrados = [];
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['Field']} ({$row['Type']})</li>";
            $campos_encontrados[] = $row['Field'];
        }
        echo "</ul>";
        
        // Verificar campos ausentes
        $campos_ausentes = array_diff($campos_esperados[$tabela], $campos_encontrados);
        if (!empty($campos_ausentes)) {
            echo "<p style='color: red;'>⚠️ Campos ausentes na tabela $tabela: " . implode(', ', $campos_ausentes) . "</p>";
            $problemas = true;
        } else {
            echo "<p style='color: green;'>✅ Todos os campos esperados estão presentes.</p>";
        }
        
        echo "</div>";
    }
}

// Mostrar botões baseados no resultado
if ($problemas) {
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #f2dede; border-radius: 4px;'>";
    echo "<h2>Problemas encontrados nas tabelas!</h2>";
    echo "<p>Clique no botão abaixo para recriar as tabelas de logs:</p>";
    echo "<a href='criar_tabelas_logs.php' style='display: inline-block; background-color: #337ab7; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Recriar Tabelas de Logs</a>";
    echo "</div>";
} else {
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #dff0d8; border-radius: 4px;'>";
    echo "<h2>Todas as tabelas estão configuradas corretamente!</h2>";
    echo "<p>As tabelas de logs existem e possuem todos os campos necessários.</p>";
    echo "<a href='relatorios_avancados.php' style='display: inline-block; background-color: #5cb85c; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-right: 10px;'>Gerar Relatórios</a>";
    echo "<a href='index.php' style='display: inline-block; background-color: #337ab7; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>Voltar para o Sistema</a>";
    echo "</div>";
}

$conn->close();
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
h1 {
    color: #2196F3;
    border-bottom: 2px solid #2196F3;
    padding-bottom: 10px;
}
ul {
    margin-bottom: 20px;
}
</style>