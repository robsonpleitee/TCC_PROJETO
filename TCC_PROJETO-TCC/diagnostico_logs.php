<?php
// Iniciar buffer de saída
ob_start();

session_start();
// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Incluir a conexão com o banco de dados
include 'conexao.php';

echo "<h1>Diagnóstico de Logs de Acesso</h1>";

// Verificar se as tabelas existem
$tabelas = ['logs_acesso', 'logs_operacoes', 'usuarios'];
foreach ($tabelas as $tabela) {
    $sql = "SHOW TABLES LIKE '$tabela'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 0) {
        echo "<p style='color: red;'>A tabela '$tabela' não existe!</p>";
    } else {
        echo "<p style='color: green;'>A tabela '$tabela' existe.</p>";
        
        // Contar registros
        $sql_count = "SELECT COUNT(*) as total FROM $tabela";
        $result_count = $conn->query($sql_count);
        $row = $result_count->fetch_assoc();
        
        echo "<p>Total de registros: " . $row['total'] . "</p>";
        
        // Mostrar 5 registros de exemplo
        $sql_sample = "SELECT * FROM $tabela LIMIT 5";
        $result_sample = $conn->query($sql_sample);
        
        if ($result_sample->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            
            // Cabeçalho da tabela
            $header = true;
            while ($row = $result_sample->fetch_assoc()) {
                if ($header) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        echo "<th style='padding: 8px; background-color: #f2f2f2;'>" . $key . "</th>";
                    }
                    echo "</tr>";
                    $header = false;
                }
                
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $value . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>Nenhum registro encontrado na tabela $tabela</p>";
        }
    }
    
    echo "<hr>";
}

// Verificar relacionamento entre usuários e logs
echo "<h2>Verificando relacionamento entre usuários e logs</h2>";

$sql_relation = "SELECT u.id, u.nome, u.email, COUNT(la.id) as total_logs 
               FROM usuarios u 
               LEFT JOIN logs_acesso la ON u.id = la.usuario_id 
               GROUP BY u.id";
$result_relation = $conn->query($sql_relation);

if ($result_relation->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID Usuário</th><th>Nome</th><th>Email</th><th>Total de Logs</th></tr>";
    
    while ($row = $result_relation->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['total_logs'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Não foi possível verificar o relacionamento entre usuários e logs.</p>";
}

// Adicionar registros de teste
if (isset($_GET['adicionar_teste']) && $_GET['adicionar_teste'] == 1) {
    echo "<h2>Adicionando logs de teste</h2>";
    
    // Obter IDs dos usuários
    $sql_users = "SELECT id FROM usuarios";
    $result_users = $conn->query($sql_users);
    
    $user_ids = [];
    while ($row = $result_users->fetch_assoc()) {
        $user_ids[] = $row['id'];
    }
    
    if (count($user_ids) > 0) {
        // Verificar se o campo eh_teste existe
        $campo_eh_teste_existe = $conn->query("SHOW COLUMNS FROM logs_acesso LIKE 'eh_teste'")->num_rows > 0;

        // Adicionar logs de acesso para cada usuário
        foreach ($user_ids as $user_id) {
            $now = date('Y-m-d H:i:s');
            $yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));
            $last_week = date('Y-m-d H:i:s', strtotime('-1 week'));
            
            if ($campo_eh_teste_existe) {
                // Usando o campo eh_teste se ele existir
                $sql_insert = "INSERT INTO logs_acesso (usuario_id, acao, detalhes, data_hora, eh_teste) VALUES 
                            ($user_id, 'login', 'Login de teste hoje', '$now', 1),
                            ($user_id, 'logout', 'Logout de teste hoje', '$now', 1),
                            ($user_id, 'login', 'Login de teste ontem', '$yesterday', 1),
                            ($user_id, 'logout', 'Logout de teste ontem', '$yesterday', 1),
                            ($user_id, 'login', 'Login de teste semana passada', '$last_week', 1)";
            } else {
                // Versão antiga sem o campo eh_teste
                $sql_insert = "INSERT INTO logs_acesso (usuario_id, acao, detalhes, data_hora) VALUES 
                            ($user_id, 'login', 'Login de teste hoje', '$now'),
                            ($user_id, 'logout', 'Logout de teste hoje', '$now'),
                            ($user_id, 'login', 'Login de teste ontem', '$yesterday'),
                            ($user_id, 'logout', 'Logout de teste ontem', '$yesterday'),
                            ($user_id, 'login', 'Login de teste semana passada', '$last_week')";
            }
            
            if ($conn->query($sql_insert)) {
                echo "<p>Logs de teste adicionados para o usuário ID: $user_id</p>";
            } else {
                echo "<p>Erro ao adicionar logs para o usuário ID: $user_id - " . $conn->error . "</p>";
            }
            
            // Adicionar logs de operações
            $sql_insert_ops = "INSERT INTO logs_operacoes (usuario_id, tipo_operacao, tabela_afetada, registro_id, detalhes, data_hora) VALUES 
                            ($user_id, 'inserir', 'produtos', 1, 'Produto teste adicionado', '$now'),
                            ($user_id, 'atualizar', 'produtos', 1, 'Produto teste atualizado', '$yesterday')";
            
            if ($conn->query($sql_insert_ops)) {
                echo "<p>Logs de operações adicionados para o usuário ID: $user_id</p>";
            } else {
                echo "<p>Erro ao adicionar logs de operações para o usuário ID: $user_id - " . $conn->error . "</p>";
            }
        }
        
        echo "<p>Todos os logs de teste foram adicionados.</p>";
    } else {
        echo "<p>Nenhum usuário encontrado para adicionar logs de teste.</p>";
    }
}

echo "<hr>";
echo "<p><a href='diagnostico_logs.php?adicionar_teste=1' style='padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Adicionar Logs de Teste</a></p>";
echo "<p><a href='relatorios_avancados.php' style='padding: 10px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 4px;'>Voltar aos Relatórios</a></p>";

$conn->close();
?>

<?php
$sql_usuarios = "SELECT u.id, u.nome, u.email, u.nivel_acesso,
            COUNT(la.id) as total_acessos,
            MIN(la.data_hora) as primeiro_acesso,
            MAX(la.data_hora) as ultimo_acesso
            FROM usuarios u
            LEFT JOIN logs_acesso la ON la.usuario_id = u.id 
                AND la.data_hora BETWEEN ? AND ? + INTERVAL 1 DAY
                AND (la.eh_teste = 0 OR la.eh_teste IS NULL)
            GROUP BY u.id
            ORDER BY total_acessos DESC, u.nome";
?>