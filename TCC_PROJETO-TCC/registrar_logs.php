<?php
/**
 * Função para registrar operações no sistema
 * 
 * @param object $conn Conexão com o banco de dados
 * @param int $usuario_id ID do usuário que realizou a operação
 * @param string $tipo_operacao Tipo de operação (inserir, atualizar, excluir)
 * @param string $tabela_afetada Nome da tabela afetada
 * @param int $registro_id ID do registro afetado
 * @param string $detalhes Detalhes adicionais sobre a operação
 * @return bool Retorna true se o log foi registrado com sucesso
 */
function registrarOperacao($conn, $usuario_id, $tipo_operacao, $tabela_afetada, $registro_id, $detalhes = '') {
    // Verificar se a tabela logs_operacoes existe
    $check_table = $conn->query("SHOW TABLES LIKE 'logs_operacoes'");
    if ($check_table->num_rows == 0) {
        // Se a tabela não existir, tenta criar
        $conn->query("CREATE TABLE IF NOT EXISTS logs_operacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            tipo_operacao VARCHAR(50) NOT NULL,
            tabela_afetada VARCHAR(50) NOT NULL,
            registro_id INT,
            detalhes TEXT,
            data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
        )");
    }
    
    try {
        $sql = "INSERT INTO logs_operacoes (usuario_id, tipo_operacao, tabela_afetada, registro_id, detalhes) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issis", $usuario_id, $tipo_operacao, $tabela_afetada, $registro_id, $detalhes);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    } catch (Exception $e) {
        // Em caso de erro, apenas retorna falso sem quebrar a execução do script
        error_log("Erro ao registrar operação: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para registrar acessos no sistema
 * 
 * @param object $conn Conexão com o banco de dados
 * @param int $usuario_id ID do usuário que realizou o acesso
 * @param string $acao Tipo de ação (login, logout, acesso_pagina)
 * @param string $detalhes Detalhes adicionais sobre o acesso
 * @return bool Retorna true se o log foi registrado com sucesso
 */
function registrarAcesso($conn, $usuario_id, $acao, $detalhes = '') {
    // Verificar se a tabela logs_acesso existe
    $check_table = $conn->query("SHOW TABLES LIKE 'logs_acesso'");
    if ($check_table->num_rows == 0) {
        // Se a tabela não existir, tenta criar
        $conn->query("CREATE TABLE IF NOT EXISTS logs_acesso (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            acao VARCHAR(50) NOT NULL,
            detalhes TEXT,
            data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
        )");
    }
    
    try {
        $sql = "INSERT INTO logs_acesso (usuario_id, acao, detalhes) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $usuario_id, $acao, $detalhes);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    } catch (Exception $e) {
        // Em caso de erro, apenas retorna falso sem quebrar a execução do script
        error_log("Erro ao registrar acesso: " . $e->getMessage());
        return false;
    }
}
?>