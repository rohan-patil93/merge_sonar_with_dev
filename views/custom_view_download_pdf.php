<?php
require_once '../htmltopdfconverterTCPDF/tcpdf.php';

$html = $_POST['html'];

// Create TCPDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('HTML to PDF');
$pdf->SetSubject('HTML to PDF');
$pdf->SetKeywords('TCPDF, PDF, HTML, PHP');

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Write HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('output.pdf', 'D');
