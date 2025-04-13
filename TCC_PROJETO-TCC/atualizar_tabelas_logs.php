<?php
include 'conexao.php';

echo "<h1>Atualização da Tabela de Logs</h1>";

// Verificar se a tabela logs_acesso existe
$tabela_existe = $conn->query("SHOW TABLES LIKE 'logs_acesso'")->num_rows > 0;

if (!$tabela_existe) {
    echo "<p style='color: red;'>A tabela logs_acesso não existe. Execute primeiro o arquivo criar_tabelas_logs.php</p>";
    echo "<p><a href='criar_tabelas_logs.php'>Criar tabelas de logs</a></p>";
} else {
    // Verificar se o campo eh_teste já existe na tabela
    $resultado = $conn->query("SHOW COLUMNS FROM logs_acesso LIKE 'eh_teste'");
    $campo_existe = $resultado->num_rows > 0;
    
    if ($campo_existe) {
        echo "<p>O campo 'eh_teste' já existe na tabela logs_acesso.</p>";
    } else {
        // Adicionar o campo eh_teste à tabela logs_acesso
        $sql_alter = "ALTER TABLE logs_acesso ADD COLUMN eh_teste TINYINT(1) DEFAULT 0";
        
        if ($conn->query($sql_alter)) {
            echo "<p style='color: green;'>✅ Campo 'eh_teste' adicionado com sucesso à tabela logs_acesso!</p>";
            
            // Marcar todos os logs existentes que contenham "teste" no detalhes como eh_teste=1
            $sql_update = "UPDATE logs_acesso SET eh_teste = 1 WHERE detalhes LIKE '%teste%'";
            if ($conn->query($sql_update)) {
                echo "<p style='color: green;'>✅ Logs de teste existentes marcados corretamente.</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Erro ao marcar logs de teste existentes: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Erro ao adicionar o campo 'eh_teste': " . $conn->error . "</p>";
        }
    }
}

echo "<p><a href='index.php'>Voltar para o sistema</a></p>";
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
    display: inline-block;
    background-color: #2196F3;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 4px;
    margin-top: 20px;
}
a:hover {
    background-color: #0b7dda;
}
</style>