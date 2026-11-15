<?php
include_once 'dbconfig.php';
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
	$dompdf->set_option('margin-top', '10mm');
	$dompdf->set_option('margin-right', '10mm');
	$dompdf->set_option('margin-bottom', '10mm');
	$dompdf->set_option('margin-left', '10mm');
	$dompdf->loadHtml($content);
	$orientation = ($_POST['orientation'] == 'landscape') ? 'landscape' : 'portrait';
	$dompdf->setPaper('A4', $orientation);
	$dompdf->render();
	$filename = $_POST['title']."_".time().'.pdf';
	$sFilePath = SITEROOT.'/uploads/rental_invoices/'.$filename;
	$output = $dompdf->output();
	file_put_contents($sFilePath, $output);
	$response = ["pdf_url" => "uploads/rental_invoices/".$filename,"pdf_name" =>$filename];
	echo json_encode($response);
}



