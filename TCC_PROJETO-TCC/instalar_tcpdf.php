<?php
// Configurações
$tcpdf_url = 'https://github.com/tecnickcom/TCPDF/archive/refs/tags/6.6.2.zip';
$zipFile = 'tcpdf.zip';
$extractDir = './';
$finalDir = 'tcpdf';

echo "<h1>Instalador do TCPDF</h1>";

// Verificar se as funções necessárias estão disponíveis
if (!function_exists('file_get_contents') || !function_exists('file_put_contents')) {
    echo "<p>❌ Funções de arquivo necessárias não estão disponíveis neste servidor.</p>";
    echo "<p>Por favor, use o método de instalação manual conforme o README.</p>";
    exit;
}

if (!class_exists('ZipArchive')) {
    echo "<p>❌ A extensão ZipArchive não está disponível neste servidor.</p>";
    echo "<p>Por favor, use o método de instalação manual conforme o README.</p>";
    exit;
}

// 1. Baixar o arquivo ZIP da versão estável
echo "<p>Baixando TCPDF...</p>";
try {
    // Configurar contexto com timeout maior e user agent
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $content = file_get_contents($tcpdf_url, false, $context);
    
    if ($content === false) {
        throw new Exception("Falha no download");
    }
    
    if (file_put_contents($zipFile, $content)) {
        echo "<p>✅ Download concluído!</p>";
    } else {
        throw new Exception("Não foi possível salvar o arquivo");
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao baixar o arquivo: " . $e->getMessage() . "</p>";
    echo "<p>Tentando alternativa 2...</p>";
    
    // Link alternativo
    $tcpdf_alt_url = 'https://sourceforge.net/projects/tcpdf/files/latest/download';
    try {
        $content = file_get_contents($tcpdf_alt_url, false, $context);
        if ($content === false) {
            throw new Exception("Falha no download da fonte alternativa");
        }
        if (file_put_contents($zipFile, $content)) {
            echo "<p>✅ Download alternativo concluído!</p>";
        } else {
            throw new Exception("Não foi possível salvar o arquivo da fonte alternativa");
        }
    } catch (Exception $e2) {
        echo "<p>❌ Erro no download alternativo: " . $e2->getMessage() . "</p>";
        echo "<p>Por favor, faça o download e a instalação manual seguindo as instruções:</p>";
        echo "<ol>";
        echo "<li>Baixe o TCPDF de <a href='https://github.com/tecnickcom/TCPDF/releases' target='_blank'>https://github.com/tecnickcom/TCPDF/releases</a></li>";
        echo "<li>Extraia o conteúdo do arquivo baixado</li>";
        echo "<li>Renomeie a pasta extraída para 'tcpdf'</li>";
        echo "<li>Copie esta pasta para a raiz do seu projeto</li>";
        echo "</ol>";
        exit;
    }
}

// 2. Extrair o arquivo ZIP
echo "<p>Extraindo arquivos...</p>";
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractDir);
    $zip->close();
    echo "<p>✅ Arquivos extraídos com sucesso!</p>";

    // 3. Renomear a pasta extraída para o nome final desejado
    // Obter nome da pasta extraída - lidar com diferentes formatos de nomes
    $extractedFolders = array_filter(glob('*'), 'is_dir');
    $tcpdfFolders = array_filter($extractedFolders, function($folder) {
        return (strpos($folder, 'TCPDF') === 0 || strpos($folder, 'tcpdf') === 0);
    });
    
    if (empty($tcpdfFolders)) {
        echo "<p>❌ Pasta TCPDF não encontrada após a extração. Verifique o conteúdo do ZIP manualmente.</p>";
        exit;
    }
    
    $extractedFolder = reset($tcpdfFolders); // Pega o primeiro elemento do array
    
    if (is_dir($finalDir)) {
        // Se a pasta de destino já existir, apaga ela primeiro
        echo "<p>Removendo instalação anterior do TCPDF...</p>";
        
        function removeDir($dir) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                is_dir("$dir/$file") ? removeDir("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        
        if (removeDir($finalDir)) {
            echo "<p>✅ Pasta antiga removida!</p>";
        } else {
            echo "<p>⚠️ Não foi possível remover a pasta antiga.</p>";
        }
    }

    if (rename($extractedFolder, $finalDir)) {
        echo "<p>✅ TCPDF renomeado para '{$finalDir}'!</p>";
    } else {
        echo "<p>❌ Erro ao renomear a pasta. Verifique as permissões.</p>";
    }
} else {
    echo "<p>❌ Falha ao extrair o arquivo ZIP. Verifique se o PHP tem permissão para escrever nesta pasta.</p>";
}

// 4. Limpar o arquivo ZIP baixado
if (file_exists($zipFile) && unlink($zipFile)) {
    echo "<p>✅ Arquivo ZIP temporário removido.</p>";
}

// 5. Verificar se a instalação foi bem-sucedida
if (file_exists("{$finalDir}/tcpdf.php")) {
    echo "<div style='background-color: #dff0d8; color: #3c763d; padding: 15px; border-radius: 4px; margin-top: 20px;'>";
    echo "<h2>✅ TCPDF instalado com sucesso!</h2>";
    echo "<p>A biblioteca TCPDF foi instalada corretamente na pasta 'tcpdf'.</p>";
    echo "<p>Para testar se a instalação funciona, acesse o <a href='teste_tcpdf.php'>arquivo de teste</a>.</p>";
    
    // Criar arquivo de teste com buffer de saída para evitar erros
    $teste = '<?php
// Iniciar buffer de saída para prevenir envio prematuro de dados
ob_start();

// Verificar se o TCPDF está disponível
if (!file_exists("tcpdf/tcpdf.php")) {
    // Limpar buffer
    ob_end_clean();
    echo "<h1>TCPDF não encontrado!</h1>";
    echo "<p>Verifique se a biblioteca TCPDF está instalada corretamente.</p>";
    exit;
}

// Limpar qualquer saída anterior
ob_end_clean();

// Agora incluímos o TCPDF com segurança
require_once "tcpdf/tcpdf.php";

// Criar objeto TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false);

// Configurar informações
$pdf->SetCreator("Sistema de Controle de Estoque");
$pdf->SetAuthor("Sistema");
$pdf->SetTitle("Teste do TCPDF");

// Desabilitar cabeçalho e rodapé
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Adicionar página
$pdf->AddPage();

// Definir fonte
$pdf->SetFont("helvetica", "B", 16);

// Adicionar conteúdo
$pdf->Cell(0, 10, "Teste do TCPDF funcionando!", 0, 1, "C");
$pdf->Cell(0, 10, "Se você está vendo esta mensagem, a instalação foi bem-sucedida!", 0, 1, "C");

// Gerar o PDF
$pdf->Output("teste_tcpdf.pdf", "I");
';
    file_put_contents("teste_tcpdf.php", $teste);
    
    // Atualizar o arquivo relatorios_avancados.php para usar o caminho correto
    $relatoriosFile = "relatorios_avancados.php";
    if (file_exists($relatoriosFile)) {
        $content = file_get_contents($relatoriosFile);
        
        // Substituir o caminho do TCPDF
        $content = str_replace(
            "require_once 'vendor/tcpdf/tcpdf.php';", 
            "require_once 'tcpdf/tcpdf.php';", 
            $content
        );
        
        // Adicionar buffer de saída se não existir
        if (strpos($content, "ob_start();") === false) {
            $content = str_replace(
                "<?php",
                "<?php\n// Iniciar buffer de saída para prevenir envio prematuro de dados\nob_start();",
                $content
            );
            
            // Adicionar limpeza de buffer antes da geração do PDF
            $content = str_replace(
                "// Exportar para PDF\nelseif (\$formato == 'pdf') {",
                "// Exportar para PDF\nelseif (\$formato == 'pdf') {\n    // Limpar qualquer saída anterior\n    ob_end_clean();",
                $content
            );
        }
        
        file_put_contents($relatoriosFile, $content);
        echo "<p>✅ Arquivo relatorios_avancados.php atualizado para usar o novo caminho do TCPDF.</p>";
    }
    
    echo "</div>";
} else {
    echo "<div style='background-color: #f2dede; color: #a94442; padding: 15px; border-radius: 4px; margin-top: 20px;'>";
    echo "<h2>❌ Erro na instalação</h2>";
    echo "<p>A biblioteca TCPDF não foi instalada corretamente. Por favor, tente novamente ou instale manualmente.</p>";
    echo "<p>Passos para instalação manual:</p>";
    echo "<ol>";
    echo "<li>Baixe o TCPDF de <a href='https://github.com/tecnickcom/TCPDF/releases' target='_blank'>https://github.com/tecnickcom/TCPDF/releases</a></li>";
    echo "<li>Extraia o conteúdo do arquivo baixado</li>";
    echo "<li>Renomeie a pasta extraída para 'tcpdf'</li>";
    echo "<li>Copie esta pasta para a raiz do seu projeto</li>";
    echo "</ol>";
    echo "</div>";
}
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
ol {
    margin: 10px 0 10px 25px;
}
</style>