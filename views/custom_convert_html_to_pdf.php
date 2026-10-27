<?php 
require_once "../dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;
$content = $_POST['html_content'];
$options = new Options();
//echo realpath('');
$options->set('chroot', '/home/infistaq/live_infistaq_com/');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($content);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$filename = "Test_invoice".time();
$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$filename.'.pdf';
$output = $dompdf->output();
file_put_contents($sFilePath, $output); 
echo "uploads/rental_invoices/".$filename.".pdf";
?>