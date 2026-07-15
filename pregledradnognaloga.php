<?php
include_once('connection.php');
include_once('class/getObject.php');
require_once __DIR__ . '/includes/radni_nalog_pdf.php';

$ids = array();
if (!empty($_GET['ids'])) {
    foreach (explode(',', (string) $_GET['ids']) as $part) {
        $id = (int) trim($part);
        if ($id > 0) {
            $ids[] = $id;
        }
    }
    $ids = array_values(array_unique($ids));
} elseif (!empty($_GET['radninalog'])) {
    $ids = array((int) $_GET['radninalog']);
}

$ids = array_slice($ids, 0, 50);
if (empty($ids)) {
    http_response_code(400);
    exit('Nema radnih naloga za PDF.');
}

$pdf = new NormaRadniNalogPdf();
$outputName = 'radni-nalozi.pdf';

foreach ($ids as $radninalogId) {
    $pdf->setRadniNalogId($radninalogId);
    // Header učitava Calibri sa uni=true — mora prije SetFont
    $pdf->AddPage();
    $pdf->AddFont('Calibri-Regular', '', 'calibri-regular.ttf', true);
    $pdf->SetFont('Calibri-Regular', '', 12);
    norma_radni_nalog_pdf_render($pdf, $radninalogId);

    if (count($ids) === 1) {
        $radninalog = new singleObject;
        $radninalog = $radninalog->fetch_single_object('radninalozi', 'radninalozi_id', $radninalogId);
        if ($radninalog) {
            $outputName = norma_radni_nalog_pdf_naziv($radninalog) . '.pdf';
        }
    }
}

$pdf->Output('I', $outputName);
exit;

?>
