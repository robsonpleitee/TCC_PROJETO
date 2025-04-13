<?php
// Desativar avisos específicos para evitar saída indesejada
error_reporting(E_ALL & ~E_DEPRECATED);

// Iniciar buffer de saída para prevenir envio prematuro de dados
ob_start();

session_start();
// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Incluir a conexão com o banco de dados
include 'conexao.php';

// Função para formatar datas
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}

// Adicione esta função junto às outras funções auxiliares no início do arquivo:
function formatarDataHora($data, $formato = 'd/m/Y H:i:s') {
    if (empty($data)) {
        return 'N/A';
    }
    return date($formato, strtotime($data));
}

// Obter parâmetros do relatório
$tipo_relatorio = isset($_GET['tipo']) ? $_GET['tipo'] : 'diario';
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'html';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d');
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d');

// Configurar data de início e fim com base no tipo de relatório
if ($tipo_relatorio == 'diario') {
    $data_inicio = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
    $data_fim = $data_inicio;
} elseif ($tipo_relatorio == 'semanal') {
    // Se a data de início não for uma segunda-feira, ajustar para a segunda anterior
    $dia_semana = date('N', strtotime($data_inicio));
    if ($dia_semana != 1) {
        $data_inicio = date('Y-m-d', strtotime("-" . ($dia_semana - 1) . " days", strtotime($data_inicio)));
    }
    // Definir data final como 6 dias após a data de início (domingo)
    $data_fim = date('Y-m-d', strtotime("+6 days", strtotime($data_inicio)));
} elseif ($tipo_relatorio == 'mensal') {
    $data_inicio = date('Y-m-01', strtotime($data_inicio)); // Primeiro dia do mês
    $data_fim = date('Y-m-t', strtotime($data_inicio)); // Último dia do mês
}

// Primeiro verificar se as tabelas existem
$tabelas_ok = true;
$sql_check = "SHOW TABLES LIKE 'logs_acesso'";
$result_check = $conn->query($sql_check);
if ($result_check->num_rows === 0) {
    $tabelas_ok = false;
}

$sql_check = "SHOW TABLES LIKE 'logs_operacoes'";
$result_check = $conn->query($sql_check);
if ($result_check->num_rows === 0) {
    $tabelas_ok = false;
}

// Se as tabelas não existirem, redirecionar
if (!$tabelas_ok) {
    ob_end_clean();
    header("Location: verificar_tabelas.php");
    exit;
}

// Adicione este código após a verificação das tabelas e antes das consultas SQL
// Aproximadamente após a linha 67 (logo depois da verificação $tabelas_ok)

// Verificar se há logs de acesso no período
$tem_logs_no_periodo = false; // Inicializar com false por padrão

if ($tabelas_ok) {
    // Verificar logs de acesso
    $sql_check_logs = "SELECT COUNT(*) as total FROM logs_acesso 
                       WHERE data_hora BETWEEN ? AND ? + INTERVAL 1 DAY";
    $stmt_check = $conn->prepare($sql_check_logs);
    $stmt_check->bind_param("ss", $data_inicio, $data_fim);
    $stmt_check->execute();
    $result_check_logs = $stmt_check->get_result();
    $row_check = $result_check_logs->fetch_assoc();
    $tem_logs_no_periodo = ($row_check['total'] > 0);
    
    // Se quiser verificar também operações, descomente as linhas abaixo:
    /*
    if (!$tem_logs_no_periodo) {
        // Verificar logs de operações se não houver logs de acesso
        $sql_check_ops = "SELECT COUNT(*) as total FROM logs_operacoes 
                          WHERE data_hora BETWEEN ? AND ? + INTERVAL 1 DAY";
        $stmt_check = $conn->prepare($sql_check_ops);
        $stmt_check->bind_param("ss", $data_inicio, $data_fim);
        $stmt_check->execute();
        $result_check_ops = $stmt_check->get_result();
        $row_check_ops = $result_check_ops->fetch_assoc();
        $tem_logs_no_periodo = ($row_check_ops['total'] > 0);
    }
    */
}

// Consulta SQL para buscar informações dos produtos no período
$sql_produtos = "SELECT p.id, p.nome, p.quantidade, p.preco, 
                COUNT(DISTINCT lo.id) as total_operacoes,
                MAX(lo.data_hora) as ultima_operacao
                FROM produtos p
                LEFT JOIN logs_operacoes lo ON (lo.tabela_afetada = 'produtos' AND lo.registro_id = p.id)
                WHERE (lo.data_hora IS NULL OR (lo.data_hora BETWEEN ? AND ? + INTERVAL 1 DAY))
                GROUP BY p.id
                ORDER BY p.nome";

$stmt_produtos = $conn->prepare($sql_produtos);
$stmt_produtos->bind_param("ss", $data_inicio, $data_fim);
$stmt_produtos->execute();
$result_produtos = $stmt_produtos->get_result();

// Verifique se o campo eh_teste existe para usá-lo na consulta
$campo_eh_teste_existe = $conn->query("SHOW COLUMNS FROM logs_acesso LIKE 'eh_teste'")->num_rows > 0;

// Adicione um parâmetro para mostrar ou não logs de teste
$mostrar_logs_teste = isset($_GET['mostrar_logs_teste']) ? $_GET['mostrar_logs_teste'] : false;

// Modifique a consulta de usuários
if ($campo_eh_teste_existe) {
    // Versão com filtro de eh_teste
    $sql_usuarios = "SELECT u.id, u.nome, u.email, u.nivel_acesso,
                    COUNT(la.id) as total_acessos,
                    MIN(la.data_hora) as primeiro_acesso,
                    MAX(la.data_hora) as ultimo_acesso
                    FROM usuarios u
                    LEFT JOIN logs_acesso la ON la.usuario_id = u.id 
                        AND la.data_hora BETWEEN ? AND ? + INTERVAL 1 DAY
                        " . (!$mostrar_logs_teste ? "AND (la.eh_teste = 0 OR la.eh_teste IS NULL)" : "") . "
                    GROUP BY u.id
                    ORDER BY total_acessos DESC, u.nome";
} else {
    // Versão antiga sem o campo eh_teste
    $sql_usuarios = "SELECT u.id, u.nome, u.email, u.nivel_acesso,
                    COUNT(la.id) as total_acessos,
                    MIN(la.data_hora) as primeiro_acesso,
                    MAX(la.data_hora) as ultimo_acesso
                    FROM usuarios u
                    LEFT JOIN logs_acesso la ON la.usuario_id = u.id 
                        AND la.data_hora BETWEEN ? AND ? + INTERVAL 1 DAY
                        " . (!$mostrar_logs_teste ? "AND la.detalhes NOT LIKE '%teste%'" : "") . "
                    GROUP BY u.id
                    ORDER BY total_acessos DESC, u.nome";
}

$stmt_usuarios = $conn->prepare($sql_usuarios);
$stmt_usuarios->bind_param("ss", $data_inicio, $data_fim);
$stmt_usuarios->execute();
$result_usuarios = $stmt_usuarios->get_result();

// Consulta para obter estatísticas de operações no período
$sql_operacoes = "SELECT tipo_operacao, COUNT(*) as total
                FROM logs_operacoes
                WHERE data_hora BETWEEN ? AND ? + INTERVAL 1 DAY
                GROUP BY tipo_operacao
                ORDER BY total DESC";

$stmt_operacoes = $conn->prepare($sql_operacoes);
$stmt_operacoes->bind_param("ss", $data_inicio, $data_fim);
$stmt_operacoes->execute();
$result_operacoes = $stmt_operacoes->get_result();

// Exportar para Excel
if ($formato == 'excel') {
    // Limpar qualquer saída anterior
    ob_end_clean();
    
    // Configurar cabeçalhos para download do Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="relatorio_' . $tipo_relatorio . '_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Saída do Excel
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<title>Relatório ' . ucfirst($tipo_relatorio) . '</title>';
    echo '</head>';
    echo '<body>';
    
    echo '<h2>Relatório ' . ucfirst($tipo_relatorio) . ' - Período: ' . formatarData($data_inicio) . ' a ' . formatarData($data_fim) . '</h2>';
    
    // Tabela de produtos
    echo '<table border="1">';
    echo '<caption><h3>Produtos</h3></caption>';
    echo '<thead>';
    echo '<tr><th>ID</th><th>Nome</th><th>Quantidade</th><th>Preço</th><th>Total Operações</th><th>Última Operação</th></tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($produto = $result_produtos->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $produto['id'] . '</td>';
        echo '<td>' . htmlspecialchars($produto['nome']) . '</td>';
        echo '<td>' . $produto['quantidade'] . '</td>';
        echo '<td>R$ ' . number_format($produto['preco'], 2, ',', '.') . '</td>';
        echo '<td>' . $produto['total_operacoes'] . '</td>';
        echo '<td>' . ($produto['ultima_operacao'] ? date('d/m/Y H:i:s', strtotime($produto['ultima_operacao'])) : 'N/A') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Tabela de usuários
    echo '<table border="1">';
    echo '<caption><h3>Usuários que utilizaram o sistema</h3></caption>';
    echo '<thead>';
    echo '<tr><th>ID</th><th>Nome</th><th>Email</th><th>Nível de Acesso</th><th>Total Acessos</th><th>Primeiro Acesso</th><th>Último Acesso</th></tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($usuario = $result_usuarios->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $usuario['id'] . '</td>';
        echo '<td>' . htmlspecialchars($usuario['nome']) . '</td>';
        echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
        echo '<td>' . htmlspecialchars($usuario['nivel_acesso']) . '</td>';
        echo '<td>' . $usuario['total_acessos'] . '</td>';
        echo '<td>' . ($usuario['primeiro_acesso'] ? date('d/m/Y H:i:s', strtotime($usuario['primeiro_acesso'])) : 'N/A') . '</td>';
        echo '<td>' . ($usuario['ultimo_acesso'] ? date('d/m/Y H:i:s', strtotime($usuario['ultimo_acesso'])) : 'N/A') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Tabela de operações
    echo '<table border="1">';
    echo '<caption><h3>Operações realizadas</h3></caption>';
    echo '<thead>';
    echo '<tr><th>Tipo de Operação</th><th>Total</th></tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($operacao = $result_operacoes->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($operacao['tipo_operacao']) . '</td>';
        echo '<td>' . $operacao['total'] . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    echo '</body>';
    echo '</html>';
    exit;
}

// Exportar para PDF
elseif ($formato == 'pdf') {
    // Limpar qualquer saída anterior
    ob_end_clean();
    
    // Verificar diferentes caminhos para o TCPDF
    $tcpdf_paths = ['tcpdf/tcpdf.php', 'vendor/tecnickcom/tcpdf/tcpdf.php', 'vendor/tcpdf/tcpdf.php'];
    $tcpdf_loaded = false;
    
    foreach ($tcpdf_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $tcpdf_loaded = true;
            break;
        }
    }
    
    if (!$tcpdf_loaded) {
        echo "<h1>TCPDF não encontrado!</h1>";
        echo "<p>O TCPDF não foi encontrado em nenhum dos caminhos esperados.</p>";
        echo "<p>Por favor, <a href='instalar_tcpdf.php'>instale o TCPDF</a> antes de gerar relatórios em PDF.</p>";
        exit;
    }
    
    // Criar nova instância do TCPDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Configurar informações do documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistema de Controle de Estoque');
    $pdf->SetTitle('Relatório ' . ucfirst($tipo_relatorio));
    $pdf->SetSubject('Relatório de Estoque e Acessos');
    
    // Remover cabeçalho e rodapé padrão
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Configurar margens
    $pdf->SetMargins(15, 15, 15);
    
    // Adicionar página
    $pdf->AddPage();
    
    // Definir fonte
    $pdf->SetFont('helvetica', 'B', 14);
    
    // Título do relatório
    $pdf->Cell(0, 10, 'Relatório ' . ucfirst($tipo_relatorio) . ' - Período: ' . formatarData($data_inicio) . ' a ' . formatarData($data_fim), 0, 1, 'C');
    $pdf->Ln(5);
    
    // Tabela de produtos
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Produtos', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(15, 7, 'ID', 1, 0, 'C', 1);
    $pdf->Cell(60, 7, 'Nome', 1, 0, 'C', 1);
    $pdf->Cell(25, 7, 'Quantidade', 1, 0, 'C', 1);
    $pdf->Cell(30, 7, 'Preço', 1, 0, 'C', 1);
    $pdf->Cell(30, 7, 'Operações', 1, 0, 'C', 1);
    $pdf->Cell(30, 7, 'Última Op.', 1, 1, 'C', 1);
    
    $pdf->SetFont('helvetica', '', 9);
    
    $result_produtos->data_seek(0);
    while ($produto = $result_produtos->fetch_assoc()) {
        $pdf->Cell(15, 6, $produto['id'], 1, 0, 'C');
        $pdf->Cell(60, 6, $produto['nome'], 1, 0, 'L');
        $pdf->Cell(25, 6, $produto['quantidade'], 1, 0, 'C');
        $pdf->Cell(30, 6, 'R$ ' . number_format($produto['preco'], 2, ',', '.'), 1, 0, 'R');
        $pdf->Cell(30, 6, $produto['total_operacoes'], 1, 0, 'C');
        $pdf->Cell(30, 6, ($produto['ultima_operacao'] ? date('d/m/Y H:i', strtotime($produto['ultima_operacao'])) : 'N/A'), 1, 1, 'C');
    }
    
    $pdf->Ln(10);
    
    // Tabela de usuários
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Usuários que utilizaram o sistema', 0, 1, 'L');
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(15, 7, 'ID', 1, 0, 'C', 1);
    $pdf->Cell(40, 7, 'Nome', 1, 0, 'C', 1);
    $pdf->Cell(50, 7, 'Email', 1, 0, 'C', 1);
    $pdf->Cell(25, 7, 'Nível', 1, 0, 'C', 1);
    $pdf->Cell(20, 7, 'Acessos', 1, 0, 'C', 1);
    $pdf->Cell(40, 7, 'Último Acesso', 1, 1, 'C', 1);
    
    $pdf->SetFont('helvetica', '', 9);
    
    $result_usuarios->data_seek(0);
    while ($usuario = $result_usuarios->fetch_assoc()) {
        $pdf->Cell(15, 6, $usuario['id'], 1, 0, 'C');
        $pdf->Cell(40, 6, $usuario['nome'], 1, 0, 'L');
        $pdf->Cell(50, 6, $usuario['email'], 1, 0, 'L');
        $pdf->Cell(25, 6, $usuario['nivel_acesso'], 1, 0, 'C');
        $pdf->Cell(20, 6, $usuario['total_acessos'], 1, 0, 'C');
        $pdf->Cell(40, 6, date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])), 1, 1, 'C');
    }
    
    // Gerar PDF
    $pdf->Output('relatorio_' . $tipo_relatorio . '_' . date('Y-m-d') . '.pdf', 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Avançados</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .stats-box {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f0f8ff;
            border-left: 5px solid #2196F3;
            border-radius: 3px;
        }
        .action-buttons {
            margin: 20px 0;
        }
        .w3-table-all {
            margin-bottom: 30px;
        }
        .date-range {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .report-title {
            color: #2196F3;
            border-bottom: 2px solid #2196F3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .w3-check {
            position: relative;
            padding: 5px 0;
        }
        .w3-check input[type="checkbox"] {
            margin-right: 8px;
            vertical-align: middle;
        }
        .w3-check label {
            display: inline-block;
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="w3-container w3-blue">
        <h2><i class="fas fa-chart-line"></i> Relatórios Avançados</h2>
    </div>

    <div class="report-container">
        <?php if (!$tabelas_ok): ?>
            <div class="w3-panel w3-red">
                <h3><i class="fas fa-exclamation-triangle"></i> As tabelas de logs não foram encontradas!</h3>
                <p>Por favor, <a href="criar_tabelas_logs.php" class="w3-button w3-white">Criar tabelas de logs</a> antes de gerar relatórios.</p>
            </div>
        <?php endif; ?>
        
        <div class="filter-form w3-card">
            <h3>Configurações do Relatório</h3>
            <form method="get" action="relatorios_avancados.php">
                <div class="w3-row-padding">
                    <div class="w3-third">
                        <label><b>Tipo de Relatório:</b></label>
                        <select class="w3-select w3-border" name="tipo" id="tipo-relatorio" onchange="ajustarCamposData()">
                            <option value="diario" <?php echo $tipo_relatorio == 'diario' ? 'selected' : ''; ?>>Diário</option>
                            <option value="semanal" <?php echo $tipo_relatorio == 'semanal' ? 'selected' : ''; ?>>Semanal</option>
                            <option value="mensal" <?php echo $tipo_relatorio == 'mensal' ? 'selected' : ''; ?>>Mensal</option>
                            <option value="personalizado" <?php echo $tipo_relatorio == 'personalizado' ? 'selected' : ''; ?>>Personalizado</option>
                        </select>
                    </div>
                    
                    <div class="w3-third" id="single-date-container" style="<?php echo $tipo_relatorio != 'diario' ? 'display:none' : ''; ?>">
                        <label><b>Data:</b></label>
                        <input type="date" class="w3-input w3-border" name="data" value="<?php echo $data_inicio; ?>">
                    </div>
                    
                    <div class="w3-third" id="date-range-container" style="<?php echo $tipo_relatorio == 'diario' ? 'display:none' : ''; ?>">
                        <div class="date-range">
                            <div style="flex: 1;">
                                <label><b>De:</b></label>
                                <input type="date" class="w3-input w3-border" name="data_inicio" value="<?php echo $data_inicio; ?>">
                            </div>
                            <div style="flex: 1;">
                                <label><b>Até:</b></label>
                                <input type="date" class="w3-input w3-border" name="data_fim" value="<?php echo $data_fim; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="w3-row-padding" style="margin-top:10px">
                    <div class="w3-third">
                        <label><b>Opções:</b></label>
                        <div style="margin-top: 8px; display: block; width: 100%; clear: both; padding: 8px 0;">
                            <input type="checkbox" id="mostrar_logs_teste" name="mostrar_logs_teste" value="1" <?php echo $mostrar_logs_teste ? 'checked' : ''; ?> style="margin-right: 10px; vertical-align: middle;">
                            <label for="mostrar_logs_teste" style="display: inline-block; vertical-align: middle;">Incluir logs de teste</label>
                        </div>
                    </div>
                </div>

                <div class="w3-padding-16">
                    <button type="submit" class="w3-button w3-blue">Gerar Relatório</button>
                    <button type="submit" name="formato" value="excel" class="w3-button w3-green">
                        <i class="fas fa-file-excel"></i> Exportar para Excel
                    </button>
                    <button type="submit" name="formato" value="pdf" class="w3-button w3-red">
                        <i class="fas fa-file-pdf"></i> Exportar para PDF
                    </button>
                </div>
            </form>
        </div>

        <h3 class="report-title">Relatório <?php echo ucfirst($tipo_relatorio); ?> - Período: <?php echo formatarData($data_inicio); ?> a <?php echo formatarData($data_fim); ?></h3>

        <?php if (!$tem_logs_no_periodo): ?>
            <div class="w3-panel w3-pale-yellow w3-leftbar w3-border-yellow">
                <p><i class="fas fa-exclamation-triangle"></i> Não há registros de acesso no período selecionado. 
                <?php if ($_SESSION['nivel_acesso'] == 'administrador'): ?>
                    <a href="diagnostico_logs.php" class="w3-button w3-amber w3-small">Executar diagnóstico</a>
                <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="stats-box">
            <h4><i class="fas fa-users"></i> Resumo de Acessos</h4>
            <?php
            $result_usuarios->data_seek(0);
            $total_usuarios = $result_usuarios->num_rows;
            $total_acessos = 0;
            
            while ($usuario = $result_usuarios->fetch_assoc()) {
                $total_acessos += $usuario['total_acessos'];
            }
            ?>
            <div class="w3-row-padding">
                <div class="w3-half">
                    <p><strong>Total de usuários ativos:</strong> <?php echo $total_usuarios; ?></p>
                </div>
                <div class="w3-half">
                    <p><strong>Total de acessos registrados:</strong> <?php echo $total_acessos; ?></p>
                </div>
            </div>
        </div>

        <!-- Tabela de produtos -->
        <h4><i class="fas fa-box"></i> Produtos</h4>
        <div class="w3-responsive">
            <table class="w3-table-all">
                <thead>
                    <tr class="w3-blue">
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Total de Operações</th>
                        <th>Última Operação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result_produtos->data_seek(0);
                    
                    if ($result_produtos->num_rows > 0) {
                        while ($produto = $result_produtos->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $produto['id'] . '</td>';
                            echo '<td>' . htmlspecialchars($produto['nome']) . '</td>';
                            echo '<td>' . $produto['quantidade'] . '</td>';
                            echo '<td>R$ ' . number_format($produto['preco'], 2, ',', '.') . '</td>';
                            echo '<td>' . $produto['total_operacoes'] . '</td>';
                            echo '<td>' . ($produto['ultima_operacao'] ? date('d/m/Y H:i:s', strtotime($produto['ultima_operacao'])) : 'N/A') . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" class="w3-center">Nenhum produto encontrado</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Tabela de usuários -->
        <h4><i class="fas fa-user-clock"></i> Usuários que utilizaram o sistema</h4>
        <div class="w3-responsive">
            <table class="w3-table-all">
                <thead>
                    <tr class="w3-blue">
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Nível de Acesso</th>
                        <th>Total de Acessos</th>
                        <th>Primeiro Acesso</th>
                        <th>Último Acesso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result_usuarios->data_seek(0);
                    
                    if ($result_usuarios->num_rows > 0) {
                        while ($usuario = $result_usuarios->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $usuario['id'] . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['nome']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['nivel_acesso']) . '</td>';
                            echo '<td>' . $usuario['total_acessos'] . '</td>';
                            echo '<td>' . ($usuario['primeiro_acesso'] ? date('d/m/Y H:i:s', strtotime($usuario['primeiro_acesso'])) : 'N/A') . '</td>';
                            echo '<td>' . ($usuario['ultimo_acesso'] ? date('d/m/Y H:i:s', strtotime($usuario['ultimo_acesso'])) : 'N/A') . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="7" class="w3-center">Nenhum acesso registrado no período</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Operações realizadas -->
        <h4><i class="fas fa-tasks"></i> Operações realizadas</h4>
        <div class="w3-responsive">
            <table class="w3-table-all">
                <thead>
                    <tr class="w3-blue">
                        <th>Tipo de Operação</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_operacoes->num_rows > 0) {
                        while ($operacao = $result_operacoes->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($operacao['tipo_operacao']) . '</td>';
                            echo '<td>' . $operacao['total'] . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="2" class="w3-center">Nenhuma operação registrada no período</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="w3-padding-16">
            <?php if ($_SESSION['nivel_acesso'] == 'administrador'): ?>
                <a href="admin_dashboard.php" class="w3-button w3-blue"><i class="fas fa-tachometer-alt"></i> Voltar para o Dashboard</a>
            <?php elseif ($_SESSION['nivel_acesso'] == 'usuario'): ?>
                <a href="usuario_dashboard.php" class="w3-button w3-blue"><i class="fas fa-tachometer-alt"></i> Voltar para o Dashboard</a>
            <?php else: ?>
                <a href="visualizador_dashboard.php" class="w3-button w3-blue"><i class="fas fa-tachometer-alt"></i> Voltar para o Dashboard</a>
            <?php endif; ?>
            <?php if ($_SESSION['nivel_acesso'] == 'administrador'): ?>
                <a href="diagnostico_logs.php" class="w3-button w3-amber"><i class="fas fa-stethoscope"></i> Diagnóstico de Logs</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function ajustarCamposData() {
            const tipoRelatorio = document.getElementById('tipo-relatorio').value;
            const singleDateContainer = document.getElementById('single-date-container');
            const dateRangeContainer = document.getElementById('date-range-container');
            
            if (tipoRelatorio === 'diario') {
                singleDateContainer.style.display = 'block';
                dateRangeContainer.style.display = 'none';
            } else {
                singleDateContainer.style.display = 'none';
                dateRangeContainer.style.display = 'block';
            }
        }
    </script>
</body>
</html>