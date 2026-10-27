<?php 
include('dbconfig.php');
require_once "dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;
if($_POST['removepdf'] !=""){
	if(file_exists(SITEROOT.'/uploads/rental_invoices/'.$_POST['removepdf'])){
        unlink(SITEROOT.'/uploads/rental_invoices/'.$_POST['removepdf']);
    }
}else{
	$content = $_POST['html_content'];
	$options = new Options();
	$options->set('chroot', realpath(''));
	$options->set('isHtml5ParserEnabled', true);
	$options->set('isPhpEnabled', true);
	$dompdf = new Dompdf($options);
	//$dompdf->set_option('dpi', 150);
	$dompdf->set_option('margin-top', '10mm');
	$dompdf->set_option('margin-right', '10mm');
	$dompdf->set_option('margin-bottom', '10mm');
	$dompdf->set_option('margin-left', '10mm');
	$dompdf->loadHtml($content);
	$orientation = ($_POST['orientation'] == 'landscape') ? 'landscape' : 'portrait';
	$dompdf->setPaper('A4', $orientation);
	// $dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$filename = $_POST['title']."_".time().'.pdf';
	$sFilePath = SITEROOT.'/uploads/rental_invoices/'.$filename;
	$output = $dompdf->output();
	file_put_contents($sFilePath, $output); 
	$response = ["pdf_url" => "uploads/rental_invoices/".$filename,"pdf_name" =>$filename];
	echo json_encode($response);
}
//$pdfContent = $dompdf->output();
// file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND); // Log POST data
// file_put_contents('debug.log', print_r($htmlContent, true), FILE_APPEND); // Log HTML content

// header('Content-Type: application/pdf');
// header('Content-Disposition: attachment; filename="output.pdf"');
// echo $pdfContent;


?>