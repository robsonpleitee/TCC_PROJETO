<?php
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
