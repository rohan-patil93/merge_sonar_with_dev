<div class="invoice-top">
    <table class="table" style="width:100%;">
        <tr>
            <td>
                <div class="invoice">
                    <h4 class="inv-header-1">Rental Invoice</h4>
                    <h4 class="inv-header-1">Rohan</h4>
                </div>
            </td>
            <td>
                <div class="invoice-name text-end">
                    <div class="logo text-white">
                        <img style="width:150px;float: right;" class="logo" src="uploads/all/file_1689142287_64ae440fe26df.png" alt="logo">
                    </div>
                    <p class="mt-4"></p>
                </div>
            </td>
        </tr>
    </table>
    <table class="table mt-4" style="width:100%;">
        <tr>
            <td>
                <div class="informeshon"> <p> Rental Name &nbsp;&nbsp;: &nbsp;&nbsp;Rohan<br/><br/>Email&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;dinkarinfistaq@live.infistaq.com<br/><br/>Phone&nbsp;&nbsp; :&nbsp;&nbsp;1111111<br/><br/>
                    Taxes&nbsp;&nbsp;&nbsp; :&nbsp;&nbsp;
                    </p>
                </div>
            </td>
            <td>
                <div class="informeshon">
                    <p> Transaction Date &nbsp;&nbsp;: &nbsp;&nbsp; 2023-10-27<br/><br/>
                    Payment Terms &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp; NET +  DAYS<br/><br/>
                    Place Of Supply &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;
                    </p>
                </div>
            </td>
        </tr>
    </table>
    <table class="table mt-4" style="width:100%;">
        <tr>
            <td>
                <div class="informeshon">
                    <p class="inv-title-1">Billing Adderss:</p>
                    <p class="invo-addr-1">
                        cfefv<br/>
                    </p>
                    <p class="inv-title-1">Shiping Adderss :</p>
                    <p class="invo-addr-1">
                        <br/>
                    </p>
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="invoice-center">
    <div class="table-section table-responsive clearfix">
        <table class="table caption-top invoice-table"  style="width:100%;">
            <thead class="bg-active">
                <tr>
                    <th style="padding: 5px;">Name</th>
                    <th style="padding: 5px;">Descrpition</th>
                    <th style="padding: 5px;" class="text-center">UOM</th>
                    <th style="text-align:right; padding: 5px;" class="text-center">Quantity</th>
                    <th style="text-align:right; padding: 5px;" class="text-center">Price</th>
                    <th style="text-align:right; padding: 5px;" class="text-center">Amount</th>
                    <th style="text-align:right; padding: 5px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:5px;"></td>
                    <td style="padding:5px;">
                        <div class="item-desc-1">
                            <small></small>
                        </div>
                    </td>
                    <td style="padding:5px;" class="text-center"><?php echo $uomlist[1]; ?></td>
                    <td style="padding:5px;" class="text-center"><p style="float: right;margin: 0;">1.00</p></td>
                    <td style="padding:5px;" class="text-right"><p style="float: right; margin: 0;">7000.0000</p></td>
                    <td style="padding:5px;" class="text-right"><p style="float: right; margin: 0;">7000.0000</p></td>
                    <td style="padding:5px;" class="text-right"><p style="float: right;margin: 0;">0.0000</p></td>
                </tr>
                <tr>
                    <td style="padding:5px;" colspan="6" class="text-end"><b style="float: right;">SubTotal</b></td>
                    <td style="padding:5px;" class="text-right text-end"><p style="float: right;margin: 0;">8000.00</p></td>
            </tr>
            <tr>
                <td style="padding:5px;" colspan="6" class="text-end"><b style="float: right;">Tax</b></td>
                <td style="padding:5px;" class="text-right"><p style="float: right;margin: 0;">0.00</p></td>
            </tr>
            <tr>
                <td style="padding:5px;" colspan="6" class="text-end fw-bold"><b style="float: right;">Grand Total</b></td>
                <td style="padding:5px;" class="text-right fw-bold text-end"><p style="float: right;margin: 0;">8000.00</p></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
    <div class="invoice-bottom">
        <div class="row">
            <div class="col-sm-6">
                <div class="bank-transfer" style="display:none;">
                    <h3 class="inv-title-1">Bank Transfer</h3>
                    <ul class="bank-transfer-list-1">
                        <li><strong>Account Name:</strong>Syamsuddin</li>
                        <li><strong>Account Number:</strong>1234567890934</li>
                        <li><strong>Code:</strong>BARC0032UK</li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="amount text-end">
                    <h3 style="text-align: right;" class="inv-title-1">Total Amount</h3>
                    <h1 style="text-align: right;">8000.00</h1>
                </div>
            </div>
        </div>
        <div class="note mt-3">
            <p class="text-muted"></p>
        </div>
    </div>
</div>
<?php /**
//$to = 'rohanpathan@live.infistaq.com'; rohan.tickday@infistaq.com
// Recipient email address
$to = 'rohanpathan@live.infistaq.com';

// Email subject
$subject = 'Email with HTML table and PDF attachment';

// Sender email address
$from = 'rohan.tickday@infistaq.com';

// HTML table content
$message = '<html><body>';
$message .= '<h1>Your HTML Table</h1>';
$message .= '<table border="1">';
$message .= '<tr><td>Row 1, Column 1</td><td>Row 1, Column 2</td></tr>';
$message .= '<tr><td>Row 2, Column 1</td><td>Row 2, Column 2</td></tr>';
$message .= '</table>';
$message .= '</body></html>';

// PDF attachment file path
$attachment = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/1698385763_file.pdf'; // Replace with the actual path to your PDF file

// Read the PDF file content
$attachment_data = file_get_contents($attachment);

// Encode the PDF attachment in base64
$attachment_base64 = base64_encode($attachment_data);
// Define the boundary for the email parts
$boundary = md5(uniqid(time()));
// Email headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "From: $from\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
// Create the email message
$message_body = "--$boundary\r\n";
$message_body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
$message_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$message_body .= $message . "\r\n\r\n";

$message_body .= "--$boundary\r\n";
$message_body .= "Content-Type: application/pdf; name=\"" . basename($attachment) . "\"\r\n";
$message_body .= "Content-Transfer-Encoding: base64\r\n";
$message_body .= "Content-Disposition: attachment; filename=\"" . basename($attachment) . "\"\r\n\r\n";
$message_body .= $attachment_base64 . "\r\n\r\n";
$message_body .= "--$boundary--";

// Send the email
$mail_success = mail($to, $subject, $message_body, $headers);

if ($mail_success) {
    echo "Email sent successfully.";
} else {
    echo "Email not sent. Check your configuration.";
}

/**
require('htmltopdfconverterTCPDF/tcpdf.php');   
      $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("Export HTML Table data to PDF using TCPDF in PHP");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(TRUE, 10);  
      $obj_pdf->SetFont('helvetica', '', 12);  
      $obj_pdf->AddPage();  
      $content = '';  
//       $url = 'http://www.tickday.infistaq.com/index.php?option=COM_QO_PRINT&rid=1&table=rental_invoices&title=Rental+Invoices'; // Replace with the URL you want to convert to a PDF
// 	$getcontent .= file_get_contents($url);
// 	$contenthtml = str_get_html($getcontent);

// // Find and extract specific data
// $content .= $contenthtml->find('#printDiv'); // Replace with the ID or selector of the element you want to extract


      $content .= '  
      <h3 align="center">Export HTML Table data to PDF using TCPDF in PHP</h3><br /><br />  
      <table border="1" cellspacing="0" cellpadding="5">  
           <tr>  
                <th width="5%">ID</th>  
                <th width="30%">Name</th>  
                <th width="10%">Gender</th>  
                <th width="45%">Designation</th>  
                <th width="10%">Age</th>  
           </tr>  
      ';  
      $content .= '</table>';  
//       //$obj_pdf->writeHTML($content);  
//       //$obj_pdf->Output('sample.pdf', 'I');  



//      $html = '<h2>Welcome toTcpdf</h2>'; 
// $html.= '<p>Welcome toTcpdf</p>'; 

$html = $obj_pdf->writeHTML($content);
$obj_pdf->writeHTML($html, true, false, false, false, '');
$time = time();
$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$time.'.pdf' ;
//$pdfContent = $obj_pdf->Output($sFilePath,'F'); // Save to folder
$pdfContent = $obj_pdf->Output($sFilePath,'S'); // Only Generate
//$obj_pdf->Output( $sFilePath , 'I'); // view to browser
//$obj_pdf->Output( $sFilePath , 'D'); // Download instant

//header('Content-Type: application/pdf');
//header('Content-Disposition: attachment; filename="'.$sFilePath.'"');

// Output the PDF content
//echo $pdfContent;

$to = 'rohanpathan@live.infistaq.com';
$subject = 'PDF Attachment';
$message = 'Please find the PDF attached.';
$headers = 'From: rohan.tickday@infistaq.com' . "\r\n" .
    'MIME-Version: 1.0' . "\r\n" .
    'Content-Type: multipart/mixed; boundary="boundary"';
$boundary = 'boundary';

$emailContent = "--$boundary\r\n";
$emailContent .= "Content-Type: text/plain; charset=us-ascii\r\n";
$emailContent .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$emailContent .= $message . "\r\n";
$emailContent .= "--$boundary\r\n";
$emailContent .= "Content-Type: application/pdf; name=\"example3.pdf\"\r\n";
$emailContent .= "Content-Transfer-Encoding: base64\r\n";
$emailContent .= "Content-Disposition: attachment; filename=\"example4.pdf\"\r\n\r\n";
$emailContent .= base64_encode($pdfContent) . "\r\n";
$emailContent .= "--$boundary--\r\n";

mail($to, $subject, $emailContent, $headers);








/**$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("Export HTML Table data to PDF using TCPDF in PHP");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(TRUE, 10);  
      $obj_pdf->SetFont('helvetica', '', 12);  
      $obj_pdf->AddPage();  
      $content = '';  
      $content .= '  
      <h3 align="center">Export HTML Table data to PDF using TCPDF in PHP</h3><br /><br />  
      <table border="1" cellspacing="0" cellpadding="5">  
           <tr>  
                <th width="5%">ID</th>  
                <th width="30%">Name</th>  
                <th width="10%">Gender</th>  
                <th width="45%">Designation</th>  
                <th width="10%">Age</th>  
           </tr>  
      ';  
      $content .= '</table>';  
      $html = $obj_pdf->writeHTML($content);
$obj_pdf->writeHTML($html, true, false, false, false, '');
$time = time();
$sFilePath = '/uploads/rental_invoices/'.$time.'.pdf';
echo $sFilePath;
$obj_pdf->Output($sFilePath,'F');  // Save to folder

/**
$params=array(
'token' => 'dic6laah8z9guqbq',
'to' => '9970623214',
'body' => 'WhatsApp API on UltraMsg.com works good'
);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.ultramsg.com/instance65723/messages/chat",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => http_build_query($params),
  CURLOPT_HTTPHEADER => array(
    "content-type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

/*
$dataurl = API."?mode=sendmailwithpdf";
$view = callAPI($dataurl);
$view = json_decode($view,1);
print_r($view);
?>
<script type="text/javascript">
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;
}
</script>
<div id="printableArea">			
	<div class="row justify-content-center p-4">
		<div class="col-md-12">
			<!-- begin: Invoice header-->
			<div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
				<table class="table table-responsive payslip-table">
					<tbody>
						<tr>
							<td align="left">
								<h3 class="font-weight-boldest m-t-30 text-primary m-r-50">PAYSLIP FOR July 2023</h3>
								<p>  Anthony Johnston</p>
							</td>

							<td align="right">
								<img src="uploads/all/file_1689142287_64ae440fe26df.png" style="width: 150px;" alt="">
								<!--end::Logo-->
								<span class="d-flex flex-column align-items-md-end font-size-lg font-weight-bold text-muted" style="word-break: break-word;white-space: initial;">
									<span> </span> 
									<span> </span>
									<span></span>
								</span>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
							<!-- <div class="rounded-xl overflow-hidden w-100 max-h-md-250px mb-30">
								<img src="assets/media/bg/bg-invoice-5.jpg" class="w-100" alt="" />
							</div> -->
							<!--end: Invoice header-->
							<!--begin: Invoice body-->
							<div class="border-bottom pb-10">
								<table class="table table-responsive" style="width: 100%;">
									<tbody><tr>
										<td>
											<div class="col-md-12 py-md-10 pr-md-10">
												<div class="table-responsive">
													<table class="table">
														<thead>
															<tr>															
																<th class="pl-0 font-weight-boldest text-muted text-uppercase ">Earnings</th>
																<th class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
																<th class="pl-0 font-weight-boldest text-muted text-uppercase">Deductions</th>
																<th class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
															</tr>
														</thead>
														<tbody>

															<tr class="font-weight-normal font-size-lg">
																<td class="p-2">
																Basic														</td>
																<td class="text-right p-2">
																40,000														</td>
																<td class="p-2">
																Professional Tax														</td>
																<td class="p-2 text-right">
																200														</td>
															</tr>
															<tr class="font-weight-normal font-size-lg">
																<td class="p-2">
																HRA														</td>
																<td class="text-right p-2">
																16,000														</td>
																<td class="p-2">
																Provident Fund														</td>
																<td class="p-2 text-right">
																4,800														</td>
															</tr>
															<tr class="font-weight-normal font-size-lg">
																<td class="p-2">
																Other Allowance														</td>
																<td class="text-right p-2">
																24,000														</td>
																<td class="p-2">
																ESI														</td>
																<td class="p-2 text-right">
																-														</td>
															</tr>
															<tr class="font-weight-normal font-size-lg">
																<td class="p-2">
																APLVP														</td>
																<td class="text-right p-2">
																-														</td>
																<td class="p-2">
																TDS														</td>
																<td class="p-2 text-right">
																5,750														</td>
															</tr>

															<tr class="font-weight-boldest border-bottom-0 font-size-lg">
																<td class="p-2">GROSS EARNINGS</td>
																<td class="text-right p-2 text-primary">80,000</td>
																<td class="p-2">TOTAL DEDUCTIONS</td>
																<td class="text-right p-2 text-danger">10,750</td>
															</tr>

															<tr class="font-weight-boldest border-bottom-0 font-size-lg">
																<td class="p-2">TOTAL NET PAYABLE</td>
																<td class="text-right p-2 text-primary"></td>
																<td class="p-2"></td>
																<td class="text-right p-2 text-primary">69,250</td>
															</tr>
														</tbody>
													</table>

												</div>
												<div class="border-bottom w-100 mt-7 mb-13"></div>
												<div class="d-flex flex-column flex-md-row">
													<div class="d-flex flex-column mb-10 mb-md-0">
														<div class="font-weight-bold font-size-h6 mb-3">BANK TRANSFER</div>
														<div class="d-flex justify-content-between font-size-lg mb-3">
															<span class="font-weight-bold mr-15">Bank Name:</span>
															<span class="text-right"></span>
														</div>
														<div class="d-flex justify-content-between font-size-lg mb-3">
															<span class="font-weight-bold mr-15">Account Number:</span>
															<span class="text-right">
															XXXXXXXXX0												</span>
														</div>
														<div class="d-flex justify-content-between mb-3">
															<span class="mr-15 font-weight-bold">PF A/C:</span>
															<span class="text-right"></span>
														</div>
														<div class="d-flex justify-content-between mb-3">
															<span class="mr-15 font-weight-bold">UAN:</span>
															<span class="text-right"></span>
														</div>
														<div class="d-flex justify-content-between mb-3">
															<span class="mr-15 font-weight-bold">PAN:</span>
															<span class="text-right">Undefined</span>
														</div>
														<div class="d-flex justify-content-between mb-3">
															<span class="mr-15 font-weight-bold">ESI:</span>
															<span class="text-right"></span>
														</div>
														<div class="d-flex justify-content-between mb-3">
															<span class="mr-15 font-weight-bold">PRAN:</span>
															<span class="text-right"></span>
														</div>

													</div>
												</div>
											</div>
										</td>
										<td style="">
											<div class="col-md-12 border-left-md pl-md-10 py-md-10 text-right word-break" style="">
												<!--begin::Total Amount-->
												<div class="font-size-h4 font-weight-bolder text-muted mb-3" style="margin-top: 23px;">NET SALARY</div>
												<div class="font-size-h1 font-weight-boldest">₹69,250</div>
												<div class="text-muted font-weight-bold mb-16 p-b-35">(Sixty Nine Thousands Two Hundred  And Fifty   Rupees   Only )</div>
												<!--end::Total Amount-->
												<div class="border-bottom w-100 mb-16"></div>
												<!--begin::Invoice To-->
												<div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 p-t-35 font-weight-bolder">EMPLOYEE </div>
												<div class="font-size-lg font-weight-bold mb-10">
													<span>INFI00114 - Anthony Johnston</span><br>
													<span class="opacity-70"> Assistant </span><br>
													<span class="opacity-70">Production</span>
												</div>
												<div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">DATE OF JOINING</div>
												<div class="font-size-lg font-weight-bold">April 30, 2019</div>
												<!--end::Invoice To-->
												<!--begin::Invoice No-->

												<div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">STD DAYS</div>
												<div class="font-size-lg font-weight-bold mb-10">30</div>
												<!--end::Invoice No-->
												<!--begin::Invoice Date-->
												<div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">WORKED DAYS</div>
												<div class="font-size-lg font-weight-bold">26.00</div>

												<div class="text-dark-50 font-size-lg font-weight-bold text-muted mt-3 font-weight-bolder">LWP DAYS</div>
												<div class="font-size-lg font-weight-bold mb-10">0.00</div>
												<!--end::Invoice Date-->
											</div>
										</td>
									</tr>
								</tbody></table>
							</div>
							<!--end: Invoice body-->
						</div>
					</div>
				</div>
				<div class="row justify-content-center">
						<div class="col-md-9">
							<div class="d-flex font-size-sm flex-wrap p-t-10 p-b-10">
								<button type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 my-1" onclick="printDiv('printableArea')">Print Payslip</button>
								<button type="button" class="btn btn-light-primary font-weight-bolder mr-3 my-1">Download</button>

							</div>
						</div>
					</div>
					<?php **/ ?>