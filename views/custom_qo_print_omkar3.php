<?php

if ($_GET['print'] == "Y") {
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
    $callapi = "Y";
} else {
    include_once('dbconfig.php');
}
if ($_POST['callapi'] == 'Y') {
    $config = $_POST['config'];
    $transaction = $_POST['rental_invoices'];
    $transaction_items = $_POST['rental_invoice_items'];
    $rental_invoice_item_taxes = $_POST['rental_invoice_item_taxes'];
    $bill_by = $_POST['bill_by'];
    $bank_accounts = $_POST['bank_accounts'];
    $callapi = "Y";
} else {
    $rid = $_GET['rid'];
    $table = $_GET['table'];
    $api = API . "?mode=getdynamicinvoiceprint&transaction_id=" . $rid . "&transaction_table=" . $table;
    $apicall = callAPI($api);
    $result = json_decode($apicall, 1);
    $config = $result['config'];
    $transaction = $result['rental_invoices'];
    $transaction_items = $result['rental_invoice_items'];
    $rental_invoice_item_taxes = $result['rental_invoice_item_taxes'];
    $bill_by = $result['bill_by'];
    $bank_accounts = $result['bank_accounts'];
    $_SESSION['pdf_file_name'] = $table;
    $tax_authorities_rates_split = $result['tax_authorities_rates_split'];

}
//echo "<pre>"; print_r($result); echo "</pre>"; 
$dutax = array();
$distax = array();
foreach ((array) $rental_invoice_item_taxes as $taxvalue) {
    if (in_array(number_format($taxvalue['tax_value'], 2), (array) $dutax)) {
        $distax[$taxvalue['tax_value']]['amount'] = $distax[$taxvalue['tax_value']]['amount'] + $taxvalue['amount'];
        $distax[$taxvalue['tax_value']]['tax_amount'] = $distax[$taxvalue['tax_value']]['tax_amount'] + $taxvalue['tax_amount'];
    } else {
        $dutax[] = number_format($taxvalue['tax_value'], 2);
        $distax[$taxvalue['tax_value']]['percentage'] = $taxvalue['tax_value'];
        $distax[$taxvalue['tax_value']]['amount'] = $taxvalue['amount'];
        $distax[$taxvalue['tax_value']]['tax_amount'] = $taxvalue['tax_amount'];
    }
}

//print_r($tax_authorities_rates_split);
// Split Should Come for Tax Autharities split part
if ($transaction['originator_state_id'] == $transaction['supplier_state_id']) {
    $split_1_name = $tax_authorities_rates_split[0]['split_name'];
    $split_2_name = $tax_authorities_rates_split[1]['split_name'];
    $split_1 = $tax_authorities_rates_split[0]['tax_rate'];
    $split_2 = $tax_authorities_rates_split[1]['tax_rate'];
    $split_1_gl_account_code = $tax_authorities_rates_split[0]['account_code'];
    $split_2_gl_account_code = $tax_authorities_rates_split[1]['account_code'];
} else {
    $split_1_name = $tax_authorities_rates_split[2]['split_name'];
    $split_1 = $tax_authorities_rates_split[2]['tax_rate'];
    $split_1_gl_account_code = $tax_authorities_rates_split[2]['account_code'];
}


// echo "<pre>";print_r($distax); echo "</pre>";
$party_additional_details = json_decode($transaction['party_additional_details'], 1);
function currencyFormat($number, $config)
{
    if ($number != "") {
        $expoldform = explode('.', $config['number_format']);
        if ($expoldform[0] == 1) {
            $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
            $formattedNumber = $formatter->formatCurrency($number, 'INR');
            $formattedCurrencyWithoutSymbol = trim(str_replace($formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL), '', $formattedNumber));
            return $formattedCurrencyWithoutSymbol;
        } else {
            return number_format($number, $expoldform[1], '.', ',');
        }
    }
}
function numberToWordFormate($number)
{

    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        '0' => '',
        '1' => 'one',
        '2' => 'two',
        '3' => 'three',
        '4' => 'four',
        '5' => 'five',
        '6' => 'six',
        '7' => 'seven',
        '8' => 'eight',
        '9' => 'nine',
        '10' => 'ten',
        '11' => 'eleven',
        '12' => 'twelve',
        '13' => 'thirteen',
        '14' => 'fourteen',
        '15' => 'fifteen',
        '16' => 'sixteen',
        '17' => 'seventeen',
        '18' => 'eighteen',
        '19' => 'nineteen',
        '20' => 'twenty',
        '30' => 'thirty',
        '40' => 'forty',
        '50' => 'fifty',
        '60' => 'sixty',
        '70' => 'seventy',
        '80' => 'eighty',
        '90' => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count((array) $str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] .
                " " . $digits[$counter] . $plural . " " . $hundred
                :
                $words[floor($number / 10) * 10]
                . " " . $words[$number % 10] . " "
                . $digits[$counter] . $plural . " " . $hundred;
        } else
            $str[] = null;
    }
    $str = array_reverse((array) $str);
    $result = implode('', (array) $str);
    $points = ($point) ?
        ". " . $words[$point / 10] . " " .
        $words[$point = $point % 10] : '';
    if ($result != '') {
        $result = $result . "Rupees  ";
    }
    if ($points != '') {
        $result .= $points . " Paise  ";
    }
    echo ucwords($result . " only");
}
function emptyCheck($valu)
{
    $value = "";
    if ($valu != "") {
        $value = $valu;
    } else {
        $value = " - ";
    }
    return $value;
}
function dateFormat($from = '', $config)
{
    $configdate_format = str_replace('-', $config['date_separator'], strtolower($config['date_format']));
    $date_formate = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $configdate_format)));
    $from = $from != '0000-00-00' ? $from : '';
    $dateformat = $date_formate != "0" ? $date_formate : 'Y-m-d';
    if ($from != "") {
        $from = date($dateformat, strtotime($from));
    } else {
        $from = date($dateformat);
    }
    return $from;
}

$rid = 1;
$url = API . "?mode=getdynamicinvocedetails&rid=" . $rid;
$response = callAPI($url);
$result2 = json_decode($response, true);
//print_r($result2);
//$application_dynamic_invoice_settings = $result2['application_dynamic_invoice_settings'];
$invData = isset($result2['application_dynamic_invoice_settings']) ? $result2['application_dynamic_invoice_settings'] : [];
echo "<script>";
echo "const invoiceData = " . json_encode($invData) . ";";
echo "const ApiData = " . json_encode($result) . ";";
if (isset($invData['footer_options'])) {
    $footerOpts = json_decode($invData['footer_options'], true);
}
echo "</script>";
?>

<?php
// $_SESSION['apply_js']['multiselest'] = 1;
// $rid = 44;
// $url = API . "?mode=getdynamicinvocedetails&rid=" . $rid;
// // echo $url;
// $response = callAPI($url);
// // echo $response;
// $result = json_decode($response, true);
// // echo $result;
// $invData = isset($result['application_dynamic_invoice_settings']) ? $result['application_dynamic_invoice_settings'] : [];
// $footerOptions = isset($invData['footer_options']) ? json_decode($invData['footer_options'], true) : [];

// $url = "https://dev.infistaq.in/api.php?mode=getdynamicinvoiceprint&transaction_id=24&transaction_table=purchase_invoices&data_name=infistaqin_infisuite_2&user_id=94&company_id=1";
// $api = file_get_contents($url);
// $api = json_decode($api, true);

// // echo "invoiceData = " . json_encode($invData) . ";";
// // echo "ApiData = " . json_encode($api) . ";";

// echo "<script>";
// echo "const invoiceData = " . json_encode($invData) . ";";
// echo "const ApiData = " . json_encode($api) . ";";
// // Debug footer visibility
// if (isset($invData['footer_options'])) {
//     $footerOpts = json_decode($invData['footer_options'], true);
//     echo "console.log('showFooter setting:', " . json_encode($footerOpts['showFooter'] ?? false) . ");";
// }
// echo "</script>";
?>
<?php if ($callapi != 'Y') { ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style type="text/css">
            @media (orientation: portrait) {
                .container {
                    width: 100%;
                    border: none;
                    padding: 0;
                }
            }

            @print {
                .container {
                    100%;
                    padding: 0;
                    border: none;
                }
            }

            td,
            th {

                font-size: 12px;
            }

            @media (max-width:772px) {
                .scan {
                    width: 36%;
                }
            }

            table {
                border-collapse: collapse;
            }
        </style>
        <script type="text/javascript">
            function printDiv(divId) {
                var printContents = document.getElementById(divId).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = "<html><head><title></title><style>@media print { .invoice-preview { page-break-inside: avoid; } .page-indicator { page-break-before: avoid; page-break-after: avoid; } }</style></head><body>" + printContents + "</body>";
                window.print();
                document.body.innerHTML = originalContents;
            }
            function convertHTMLToPDF() {
                var title = '<?php echo urldecode($_GET['title']); ?>';
                var htmlContent = $('#printDiv').html();
                $.ajax({
                    url: 'convert_html_to_pdf.php',
                    type: 'POST',
                    data: { html_content: htmlContent, title: title },
                    success: function (pdfdetail) {
                        var result = JSON.parse(pdfdetail);
                        var link = document.createElement('a');
                        link.href = result.pdf_url;
                        link.download = result.pdf_name;
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        $.ajax({
                            url: 'convert_html_to_pdf.php',
                            type: 'POST',
                            data: 'removepdf=' + result.pdf_name,
                            success: function (res) {
                                console.log(res);
                            }
                        });
                    }
                });
            }
        </script>
    </head>

    <body style="margin: 0;padding: 0;font-family: Arial;font-size: 12px;">
        <div class="" style="width: 861px;margin: 0 auto;"> <?php } ?>
        <div class="card" style="width: 861px; margin: 0 auto;">
            <div class="card-body" id="printDiv">
                <!-- Required Fremwork -->
                <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
                <link rel="stylesheet" type="text/css" href="assets/css/style.css">
                <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
                <style type="text/css">
                    @media (orientation: portrait) {
                        .container {
                            width: 100%;
                            border: none;
                            padding: 0;
                        }
                        .logosec{
                             width: 80%;
                        }
                    }

                    @print {
                        .container {
                            100%;
                            padding: 0;
                            border: none;
                        }

                        td,
                        th {
                            font-size: 70%;
                        }
                    }

                    td,
                    th {
                        font-size: 13px;
                        padding: 5px !important;
                        vertical-align: middle;
                        white-space: normal !important;
                    }

                    @media (max-width:772px) {
                        .scan {
                            width: 36%;
                        }
                    }

                    table {
                        border-collapse: collapse;
                    }

                    .swatches {
                        width: 20px;
                        height: 20px;
                        border-radius: 50px;
                        border: 1px solid #cccccc;
                        display: inline-block;
                    }

                    /* Pagination styles */
                    @media print {
                        .invoice-preview {
                            page-break-inside: avoid;
                        }

                        .page-indicator {
                            page-break-before: avoid;
                            page-break-after: avoid;
                        }

                        td,
                        th {
                            font-size: 70%;
                        }
                    }

                    .page-indicator {
                        text-align: center;
                        margin: 10px 0;
                        font-weight: bold;
                        color: #666;
                        font-size: 14px;
                    }
                    .container-fluid, h6,  .page_footer, .totalinwords{
                        font-size: 12px !important;
                    }
                </style>
                <div class="container-fluid">
                    <div class="row ml-20 mr-20">
                        <div class="col-lg-12">
                            <div class="invoice-preview bg-light bordered"
                                style="min-height: 500px; position: relative; margin-top: 10px;">
                                <div class="top-header logosec" style="position:relative;width: 100%;">
                                    <div class="head-one">
                                        <img id="logoPreview" src="" alt=""
                                            style="display: block;position: absolute; max-height: 80px; left: 0; top: 0;">
                                        <div class="invoice-no" style="display: block; text-align: right;">
                                            <p class="invoice" data-label="Invoice No"
                                                style="margin: 0;"></p>
                                            <p class="date" data-label="Date" style="margin: 0;"></p>
                                            <p class="due_date" data-label="Due Date"
                                                style="margin: 0;"></p>
                                            <p class="po_num" data-label="P.O. Number"
                                                style="margin: 0;"></p>
                                            <p class="po_date" data-label="P.O. Date"
                                                style="margin: 0;"></p>
                                                <!-- om new  -->
                                            <p class="name" data-label="Name"
                                                style="margin: 0;"></p>
                                            <p class="email" data-label="Email"
                                                style="margin: 0;"></p>
                                            <p class="phone" data-label="Phone"
                                                style="margin: 0;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="address-sec bordered headersec" style="margin-top: 5px;width: 100%; overflow: hidden;">
                                    <div class="address-sec" style="display: block; justify-content: space-between;">
                                        <div class="first_address" style="vertical-align:top;padding: .5rem;">
                                            <strong class="address1heading" style="font-weight: 600;padding: 0px 5px;">

                                            </strong>
                                            <address class="address1data" style="padding: 0px 5px; white-space: normal;">

                                            </address>
                                        </div>
                                        <div class="second_address" style="vertical-align:top;padding: .5rem;">
                                            <strong class="address2heading" style="font-weight: 600;padding: 0px 5px;">

                                            </strong>
                                            <address class="address2data" style="padding: 0px 5px; white-space: normal;">

                                            </address>
                                        </div>
                                        <div class="third_address" style="vertical-align:top;padding: .5rem;">
                                            <strong class="address3heading" style="font-weight: 600;padding: 0px 5px;">

                                            </strong>
                                            <address class="address3data" style="padding: 0px 5px; white-space: normal;">

                                            </address>
                                        </div>
                                    </div>
                                </div>
                                <div class="items tablesec" style="margin-top: .5rem;">
                                    <div class="table-responsive">
                                        <table class="table bordered mb-10">
                                            <thead>
                                                <tr class="">
                                                    <th class="bordered"><strong>S.No.</strong></th>
                                                    <th class="bordered img"><strong>Image</strong></th>
                                                    <th class="bordered item"><strong>Item Name</strong></th>
                                                    <th class="bordered des"><strong>Description</strong></th>
                                                    <th class="bordered uom"><strong>UOM</strong></td>
                                                    <th class="bordered hsnsac text-end"><strong>HSN/SAC</strong></th>
                                                    <th class="bordered qty text-end"><strong>QTY</strong></th>
                                                    <th class="bordered rate text-end"><strong>Rate</strong></th>
                                                    <th class="bordered tax text-end"><strong>Tax</strong></th>
                                                    <th class="bordered discount text-end"><strong>Discount</strong></th>
                                                    <th class="bordered amount text-end"><strong>Amount</strong></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                <tr class="data">
                                                    <td class="bordered"><?= $i ?></td>
                                                    <td class="bordered img"><img width="35px"
                                                            src="uploads/all/no-image.jpg" alt=""></td>
                                                    <td class="bordered item text-1"></td>
                                                    <td class="bordered des text-1"></td>
                                                    <td class="bordered uom text-1"></td>
                                                    <td class="bordered hsnsac text-end"></td>
                                                    <td class="bordered qty text-end"></td>
                                                    <td class="bordered rate text-end"></td>
                                                    <td class="bordered tax text-1 text-end" ata-original="23.00"></td>
                                                    <td class="bordered discount text-end"></td>
                                                    <td class="bordered amount text-end"></td>
                                                </tr>
                                                <?php $i++; ?>
                                                <tr class="tax-summary">
                                                    <td class="bordered" style="vertical-align: top;"></td>
                                                    <td class="bordered img" style="vertical-align: top;"></td>
                                                    <td class="bordered item text-end" style="vertical-align: top;">
                                                    </td>
                                                    <td class="bordered des text-1" style="vertical-align: top;"></td>
                                                    <td class="bordered uom text-1" style="vertical-align: top;"></td>
                                                    <td class="bordered hsnsac text-end" style="vertical-align: top;"></td>
                                                    <td class="bordered qty text-end" style="vertical-align: top;"></td>
                                                    <td class="bordered rate text-end" style="vertical-align: top;"></td>
                                                    <td class="bordered tax text-end" style="vertical-align: top;">
                                                        <span style="font-weight: 700;"></span>
                                                    </td>
                                                    <td class="bordered discount text-end" style="vertical-align: top;"></td>
                                                    <td class="bordered amount text-end" style="vertical-align: top;">
                                                        <span style="font-weight: 700;"></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="page_footer bordered footersec" style="margin-top: .5rem;">
                                    <div
                                        style="display: block; padding: 5px;">
                                        <div class="first_col signatureqr" style="width: 48%; display:inline-block; vertical-align:top;">
                                            <div class="bankdetails"
                                                style="display: none; margin-bottom: 10px;vertical-align:top;">
                                                <h6 style="text-decoration: underline;margin-bottom: 5px;">
                                                    Bank Details :</h6>
                                                <div style="display: block; padding: 5px;width: 100%;">
                                                    <div style="display: inline-block; vertical-align: top; width: 50%;">
                                                        <h6
                                                            style="text-align: left;white-space: nowrap;font-weight: 600;">
                                                            Bank Name:</h6>
                                                        <h6
                                                            style="text-align: left;white-space: nowrap;font-weight: 600;">
                                                            Account Holder:</h6>
                                                        <h6
                                                            style="text-align: left;white-space: nowrap;font-weight: 600;">
                                                            Account Number:</h6>
                                                        <h6
                                                            style="text-align: left;white-space: nowrap;font-weight: 600;">
                                                            IFSC Number:</h6>
                                                        <h6
                                                            style="text-align: left;white-space: nowrap;font-weight: 600;">
                                                            Account Type:</h6>
                                                        <h6
                                                            style="text-align: left;white-space: nowrap;font-weight: 600;">
                                                            UPI:</h6>
                                                    </div>
                                                    <div style="display: inline-block; vertical-align: top;">
                                                        <h6 style="font-weight: normal;" class="bank_name">xxxx</h6>
                                                        <h6 style="font-weight: normal;" class="bank_contact_person">xxxx</h6>
                                                        <h6 style="font-weight: normal;" class="bank_account_number">xxxx</h6>
                                                        <h6 style="font-weight: normal;" class="ifsc_code">xxxx</h6>
                                                        <h6 style="font-weight: normal;" class="account_type">xxxx</h6>
                                                        <h6 style="font-weight: normal;" class="upi_id">xxxx</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="additional_notes" style="display: none; margin-bottom: 10px;">
                                                <h6 style="text-decoration: underline;margin-bottom: 5px;">
                                                    Additional Notes :
                                                </h6>
                                                <p style="padding:10px;background: #e1e1e1;">

                                                </p>
                                            </div>
                                        </div>
                                        <div class="invoicesummary" style="width: 50%; display: inline-block; vertical-align: top;">
                                            <div>
                                                <div style="display: block; justify-content: space-between; flex-wrap: nowrap; gap: 20px;padding: 5px;">
                                                    <div class="sec_col" style="width:36%; display:inline-block; vertical-align:top;">
                                                        <h6 class="totalbeforetax heading1"
                                                            style="text-align: left;white-space: nowrap;"></h6>
                                                        <h6 class="discount heading2"
                                                            style="text-align: left;white-space: nowrap;"></h6>
                                                        <h6 class="totaltaxamount heading3" style="text-align: left;">
                                                        </h6>

                                                        <h6 class="roundedoff heading4"
                                                            style="text-align: left;white-space: nowrap;"></h6>
                                                        <h6 class="securityamount heading4_5"
                                                            style="text-align: left;white-space: nowrap;"></h6>
                                                        <h6 class="totalamount heading5"
                                                            style="text-align: left;white-space: nowrap;"></h6>
                                                        <h6 class="totaldue heading6"
                                                            style="text-align: left;white-space: nowrap;"></h6>
                                                    </div>
                                                    <div class="third_col" style="width:62%; display:inline-block; vertical-align:top; text-align: right;">
                                                        <h6 class="totalbeforetax value1" style="text-align: right;font-weight: normal;">
                                                        </h6>
                                                        <h6 class="discount value2" style="text-align: right;font-weight: normal;"></h6>
                                                        <h6 class="totaltaxamount" style="font-weight: normal;text-align: right; display:flex;justify-content:end;">
                                                            <span class="cgst"></span>
                                                            <span class="sgst"></span>
                                                            <span class="igst"></span>
                                                        </h6>
                                                        <h6 class="roundedoff value4" style="text-align: right;font-weight: normal;"></h6>
                                                        <h6 class="securityamount value4_5" style="font-weight: normal;text-align: right;">
                                                        </h6>
                                                        <h6 class="totalamount value5" style="text-align: right;font-weight: normal;"></h6>
                                                        <h6 class="totaldue value6" style="text-align: right;font-weight: normal;"></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="totalinwords" ></p>
                                                <div
                                                    style="display: block;">
                                                    <div class="qr" style="width:50%; vertical-align: top;">
                                                        <img width="120px" src="" alt="QR Code">
                                                        <span class="qr-fallback" style="display:none;">-</span>
                                                    </div>
                                                    <div class="signature" style="width:48%; vertical-align: top;">
                                                        <h6 style="text-decoration: underline;margin-bottom: 5px;">
                                                            Autherized Signature :
                                                        </h6>
                                                        <img width="100px" src="" alt="Authorized Signature">
                                                        <span class="signature-fallback" style="display:none;">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($callapi != 'Y') { ?>
        </div>
        <div class="invoice-btn-section clearfix d-print-none" style="margin-top:5px;">
            <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printDiv');">
                <i class="fa fa-print"></i> Print
            </button>
            <button class="btn waves-effect waves-light btn-grd-info" onclick="convertHTMLToPDF();">
                <i class="fa fa-download"></i> Download
            </button>
        </div>
    </body>

    </html>
<?php } ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (invoiceData.hasOwnProperty("border_options")) {
            let borderOptions = invoiceData.border_options;
            if (typeof borderOptions === "string") {
                borderOptions = JSON.parse(borderOptions);
            }
            function applyBorderStyles() {
                const elements = document.querySelectorAll(".bordered");
                elements.forEach(element => {
                    element.style.borderStyle = borderOptions.borderStyle;
                    element.style.borderColor = borderOptions.borderColor;
                    element.style.borderWidth = borderOptions.borderWidth + "px";
                });
            }
            applyBorderStyles();
        }
    });
    document.addEventListener("DOMContentLoaded", function () {
        if (invoiceData.hasOwnProperty("basic_options")) {
            let basicOptions = invoiceData.basic_options;
            if (typeof basicOptions === "string") {
                basicOptions = JSON.parse(basicOptions);
            }
            function applyBasicStyles() {
                const elements = document.querySelectorAll(".invoice-preview");
                elements.forEach(element => {
                    element.style.fontFamily = basicOptions.fontFamily;
                    element.style.margin = basicOptions.margin + "px";
                    element.style.padding = basicOptions.padding + "px";
                    // element.style.height = basicOptions.height + "%";
                });
            }
            applyBasicStyles();
        }
    });
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof ApiData !== "undefined" && ApiData) {
            let rentalInvoiceData = ApiData.hasOwnProperty("rental_invoices") ? ApiData.rental_invoices : null;
            let configData = ApiData.hasOwnProperty("config") ? ApiData.config : null;

            // Date change
            function formatDateFromConfig(dateString, config) {
                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) {
                        return dateString;
                    }
                   
                    let dateFormat = config.date_format || 'DD-MM-YYYY';
                    let dateSeparator = config.date_separator || '-';
                   
                    dateFormat = dateFormat.replace(/-/g, dateSeparator);
                   
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                   
                    let formattedDate = dateFormat.toLowerCase();
                    formattedDate = formattedDate.replace('dd', day);
                    formattedDate = formattedDate.replace('mm', month);
                    formattedDate = formattedDate.replace('yyyy', year);
                   
                    return formattedDate;
                } catch (error) {
                    console.error('Date formatting error:', error);
                    return dateString;
                }
            }
 
            // Date change
            async function fetchConfigData() {
                if (configData && configData.date_format) {
                    return configData;
                }
               
                try {
                    const urlParams = new URLSearchParams(window.location.search);
                    const companyId = urlParams.get('company_id') || '1';
                   
                    const response = await fetch(`api.php?mode=getconfigsettings&company_id=${companyId}`);
                    const result = await response.json();
                   
                    if (result && result.date_format) {
                        configData = result;
                        return configData;
                    }
                } catch (error) {
                    console.error('Error fetching config data:', error);
                }
               
                return {
                    date_format: 'DD-MM-YYYY',
                    date_separator: '-'
                };
            }
 



            if (typeof invoiceData !== "undefined" && invoiceData.hasOwnProperty("logo_options")) {
                let logoOptions = invoiceData.logo_options;

                if (typeof logoOptions === "string") {
                    try {
                        logoOptions = JSON.parse(logoOptions);
                    } catch (error) {
                        logoOptions = {};
                    }
                }

                const logoElement = document.getElementById("logoPreview");
                const invoiceElement = document.querySelector(".top-header .invoice-no");
                const headerContainer = document.querySelector(".invoice-header");
                const fileUploadsPath = "uploads/all/";
                const defaultLogo = fileUploadsPath + "no-image.jpg";

                if (logoElement && logoOptions.showLogo) {
                    if (configData && configData.hasOwnProperty("display_logo") && configData.display_logo.trim() !== "") {
                        logoElement.src = fileUploadsPath + configData.display_logo;
                    } else {
                        logoElement.src = defaultLogo;
                    }

                    logoElement.alt = fileUploadsPath + configData.display_logo;
                    logoElement.style.display = "block";
                    logoElement.style.maxHeight =
                        logoOptions.logoSize === "custom"
                            ? `${logoOptions.customLogoSize}px`
                            : logoOptions.logoSize === "small"
                                ? "40px"
                                : logoOptions.logoSize === "medium"
                                    ? "80px"
                                    : "95px";

                    const positionStyles = {
                        left: {
                            left: "0", right: "auto", transform: "none",
                            textAlign: "left", position: "absolute"
                        },
                        center: {
                            left: "50%", transform: "translateX(-50%)", textAlign: "center",
                            position: "relative", paddingBottom: "10px"
                        },
                        right: {
                            left: "auto", right: "0", transform: "none",
                            textAlign: "right", position: "absolute"
                        }
                    };

                    Object.assign(logoElement.style, positionStyles[logoOptions.logoPosition] || {});
                    if (invoiceElement) {
                        invoiceElement.style.display = "block";
                        invoiceElement.style.left = logoOptions.logoPosition === "right" ? "auto" : "0";
                        invoiceElement.style.right = logoOptions.logoPosition === "right" ? "0" : "0";
                        invoiceElement.style.transform = logoOptions.logoPosition === "right" ? "none" : "0";
                        invoiceElement.style.textAlign = logoOptions.logoPosition === "right" ? "left" : "0";
                        invoiceElement.style.textAlign = logoOptions.logoPosition === "center" ? "center" : "0";
                    }

                    if (headerContainer) {
                        headerContainer.style.textAlign = positionStyles[logoOptions.logoPosition]?.textAlign || "left";
                    }
                } else if (logoElement) {
                    logoElement.style.display = "none";
                }

                async function updateField(selector, valueKey, isVisible, headingLabel = "") {
                    const element = document.querySelector(selector);
 
                    if (!element || !rentalInvoiceData || !valueKey || !isVisible) {
                        if (element) element.style.display = "none";
                        return;
                    }
 
                    let value = rentalInvoiceData[valueKey] ?? "";
                   
                    // Date change
                    const dateFields = ['transaction_date', 'invoice_date', 'due_date', 'po_date', 'date', 'created_date', 'updated_date'];
                    const isDateField = dateFields.some(field => valueKey.toLowerCase().includes(field.toLowerCase())) ||
                                       selector.includes('.date') || selector.includes('.due_date') || selector.includes('.po_date');
                   
                    if (isDateField && value) {
                        const config = await fetchConfigData();
                        value = formatDateFromConfig(value, config);
                    }
                   
                    element.style.display = "block";
                    const heading = headingLabel || element.dataset.label || "";
                    element.innerHTML = `<span>${heading} :</span> ${value}`;
 
                    if (invoiceElement) {
                        element.style.textAlign = invoiceElement.style.textAlign;
                    }
                }
 
                // Date change
                updateField(".invoice", logoOptions.invoiceNoValue, logoOptions.showInvoiceNo, logoOptions.invoiceNoHeading);
                updateField(".date", logoOptions.dateValue, logoOptions.showDate, logoOptions.dateHeading);
                updateField(".due_date", logoOptions.dueDateValue, logoOptions.showDueDate, logoOptions.dueDateHeading);
                updateField(".po_num", logoOptions.poNumValue, logoOptions.showPoNum, logoOptions.poNumHeading);
                updateField(".po_date", logoOptions.poDateValue, logoOptions.showPoDate, logoOptions.poDateHeading);
               
                // om new
                updateField(".name", logoOptions.nameValue, logoOptions.showName, logoOptions.nameHeading);
                updateField(".email", logoOptions.emailValue, logoOptions.showEmail, logoOptions.emailHeading);
                updateField(".phone", logoOptions.phoneValue, logoOptions.showPhone, logoOptions.phoneHeading);
 


            }
        }
    });


    document.addEventListener("DOMContentLoaded", function () {
        if (typeof ApiData !== "undefined" && ApiData) {
            let rentalInvoiceData = ApiData.hasOwnProperty("rental_invoices") ? ApiData.rental_invoices : null;
            let configData = ApiData.hasOwnProperty("config") ? ApiData.config : null;

            if (typeof invoiceData !== "undefined" && invoiceData && invoiceData.hasOwnProperty("header_options")) {
                let headerOptions = invoiceData.header_options;

                if (typeof headerOptions === "string") {
                    try {
                        headerOptions = JSON.parse(headerOptions);
                    } catch (error) {
                        console.error("Invalid JSON in header_options:", error);
                        return;
                    }
                }

                function applyHeaderOptions() {
                    const addressSection = document.querySelector(".address-sec");
                    const addressContainer = document.querySelector(".address-sec > div");

                    const firstAddressContainer = document.querySelector(".first_address");
                    const firstAddressHeading = document.querySelector(".address1heading");
                    const firstAddressData = document.querySelector(".address1data");

                    const secondAddressContainer = document.querySelector(".second_address");
                    const secondAddressHeading = document.querySelector(".address2heading");
                    const secondAddressData = document.querySelector(".address2data");

                    const thirdAddressContainer = document.querySelector(".third_address");
                    const thirdAddressHeading = document.querySelector(".address3heading");
                    const thirdAddressData = document.querySelector(".address3data");

                    if (headerOptions.showAddress) {
                        addressSection.style.display = "inline-block";
                    } else {
                        addressSection.style.display = "none";
                        return;
                    }

                    // Position mapping for flex order
                    const positionOrder = {
                        "left": 1,
                        "center": 2,
                        "right": 3
                    };

                    // om address part 
                    function updateAddressField(containerElem, headingElem, dataElem, headingKey, valueKey, showKey, positionKey) {
                        if (!rentalInvoiceData || !headingElem || !dataElem || !containerElem) return;

                        const isVisible = headerOptions[showKey];
                        const heading = headerOptions[headingKey];
                        const valueKeyMapped = headerOptions[valueKey];
                        const position = headerOptions[positionKey];
                        
                        console.log(heading,"---------------",valueKeyMapped," ------------",position);
                        
                        if (!isVisible) {
                            containerElem.style.display = "none";
                            return;
                        }

                        // Get field visibility settings based on address type
                        let addressFieldKey, pincodeFieldKey, gstFieldKey, panFieldKey;
                        if (showKey === 'showFirstAddress') {
                            addressFieldKey = 'showFirstAddressField';
                            pincodeFieldKey = 'showFirstPincodeField';
                            gstFieldKey = 'showFirstGstField';
                            panFieldKey = 'showFirstPanField';
                        } else if (showKey === 'showSecondAddress') {
                            addressFieldKey = 'showSecondAddressField';
                            pincodeFieldKey = 'showSecondPincodeField';
                            gstFieldKey = 'showSecondGstField';
                            panFieldKey = 'showSecondPanField';
                        } else if (showKey === 'showThirdAddress') {
                            addressFieldKey = 'showThirdAddressField';
                            pincodeFieldKey = 'showThirdPincodeField';
                            gstFieldKey = 'showThirdGstField';
                            panFieldKey = 'showThirdPanField';
                        }

                        // om address part 
                        let addressData, gstNumber, panNumber, phoneNumber;
                        
                        if (valueKeyMapped === "company_address" && configData) {
                            // om address part 
                            addressData = configData.contact_address;
                            gstNumber = configData.gst_number;
                            panNumber = configData.pan_number;
                            phoneNumber = configData.phone_number;
                        } else if (valueKeyMapped === "party_address" || valueKeyMapped === "party_ship_adderss") {
                            // om address part 
                            let rawAddressData = valueKeyMapped === "party_address" ? rentalInvoiceData.party_address : rentalInvoiceData.party_ship_adderss;

                            addressData = rawAddressData ? rawAddressData.replace(/\t+/g, ' ').trim() : '';
                            
                            let partyAdditionalDetails = {};
                            try {
                                partyAdditionalDetails = typeof rentalInvoiceData.party_additional_details === 'string' 
                                    ? JSON.parse(rentalInvoiceData.party_additional_details) 
                                    : rentalInvoiceData.party_additional_details || {};
                            } catch (e) {
                                console.log('Error parsing party_additional_details:', e);
                                partyAdditionalDetails = {};
                            }
                            
                            gstNumber = partyAdditionalDetails.gstin || rentalInvoiceData.party_tax_number;
                            panNumber = partyAdditionalDetails.pan_no || rentalInvoiceData.party_tax_number;
                            phoneNumber = rentalInvoiceData.party_phone;
                            
                            console.log('Party address data:', {
                                addressData: addressData,
                                gstNumber: gstNumber,
                                panNumber: panNumber,
                                phoneNumber: phoneNumber,
                                partyAdditionalDetails: partyAdditionalDetails
                            });
                        }

                        // om address part  
                        let formattedAddress = '';
                        
                        // om address part 
                        console.log(`Address field check for ${addressFieldKey}:`, {
                            fieldVisible: headerOptions[addressFieldKey],
                            hasAddressData: !!addressData,
                            addressDataTrimmed: addressData ? addressData.trim() : null
                        });
                        
                        // Show address if field is visible OR if there's valid address data (override visibility for actual data)
                        if ((headerOptions[addressFieldKey] !== false || (addressData && addressData.trim().length > 0)) && addressData && addressData.trim()) {
                            formattedAddress += addressData.trim();
                        }
                        
                        // om address part 
                        if (headerOptions[pincodeFieldKey] !== false && phoneNumber) {
                            if (formattedAddress) formattedAddress += '\n';
                            formattedAddress += 'Phone: ' + phoneNumber;
                        }
                        
                        // om address part 
                        if (headerOptions[gstFieldKey] !== false && gstNumber && gstNumber !== 'NA' && gstNumber !== '') {
                            if (formattedAddress) formattedAddress += '\n';
                            formattedAddress += 'GSTIN: ' + gstNumber;
                        }
                        
                        // om address part 
                        if (headerOptions[panFieldKey] !== false && panNumber && panNumber !== 'NA' && panNumber !== '') {
                            if (formattedAddress) formattedAddress += '\n';
                            formattedAddress += 'PAN: ' + panNumber;
                        }

                        console.log(`Updating ${headingKey} (${showKey}):`, heading, formattedAddress, isVisible, position);

                        if (formattedAddress) {
                            headingElem.textContent = heading || 'Address';
                            dataElem.innerHTML = formattedAddress.replace(/\n/g, '<br>');
                            containerElem.style.display = "inline-block";

                            // Apply positioning using flex order
                            if (position && positionOrder[position]) {
                                containerElem.style.order = positionOrder[position];
                            }
                        } else {
                            console.log(`No formatted address for ${headingKey}:`, {
                                heading: heading,
                                formattedAddress: formattedAddress,
                                addressData: addressData,
                                isVisible: isVisible
                            });
                            containerElem.style.display = "none";
                        }
                    }


                    console.log("--------------------headerOptions-------------------- ",headerOptions);
                    var position = positionOrder[headerOptions['firstAddressPosition']];
                    if(position == 1){
                        updateAddressField(firstAddressContainer, firstAddressHeading, firstAddressData, "firstAddressHeading", "firstAddressValue", "showFirstAddress", "firstAddressPosition");
                    }else if(position == 2){
                        updateAddressField(secondAddressContainer, secondAddressHeading, secondAddressData, "firstAddressHeading", "firstAddressValue", "showFirstAddress", "firstAddressPosition");
                    }else if(position == 3){
                        updateAddressField(thirdAddressContainer, thirdAddressHeading, thirdAddressData, "firstAddressHeading", "firstAddressValue", "showFirstAddress", "firstAddressPosition");
                    }
                    console.log("--------------------headerOptions-------"+position);
                    var position = positionOrder[headerOptions['secondAddressPosition']];
                    if(position == 1){
                        updateAddressField(firstAddressContainer, firstAddressHeading, firstAddressData, "secondAddressHeading", "secondAddressValue", "showSecondAddress", "secondAddressPosition");
                    }else if(position == 2){
                        updateAddressField(secondAddressContainer, secondAddressHeading, secondAddressData, "secondAddressHeading", "secondAddressValue", "showSecondAddress", "secondAddressPosition");
                    }else if(position == 3){
                        updateAddressField(thirdAddressContainer, thirdAddressHeading, thirdAddressData, "secondAddressHeading", "secondAddressValue", "showSecondAddress", "secondAddressPosition");
                    }
                    
                    console.log("--------------------headerOptions-------"+position);
                    var position = positionOrder[headerOptions['thirdAddressPosition']];
                    console.log("--------------------headerOptions-------"+position);
                     if(position == 1){
                        updateAddressField(firstAddressContainer, firstAddressHeading, firstAddressData, "thirdAddressHeading", "thirdAddressValue", "showThirdAddress", "thirdAddressPosition");
                    }else if(position == 2){
                        updateAddressField(secondAddressContainer, secondAddressHeading, secondAddressData, "thirdAddressHeading", "thirdAddressValue", "showThirdAddress", "thirdAddressPosition");
                    }else if(position == 3){
                        updateAddressField(thirdAddressContainer, thirdAddressHeading, thirdAddressData, "thirdAddressHeading", "thirdAddressValue", "showThirdAddress", "thirdAddressPosition");
                    }




                    // updateAddressField(firstAddressContainer, firstAddressHeading, firstAddressData, "firstAddressHeading", "firstAddressValue", "showFirstAddress", "firstAddressPosition");
                    // updateAddressField(secondAddressContainer, secondAddressHeading, secondAddressData, "secondAddressHeading", "secondAddressValue", "showSecondAddress", "secondAddressPosition");
                    // updateAddressField(thirdAddressContainer, thirdAddressHeading, thirdAddressData, "thirdAddressHeading", "thirdAddressValue", "showThirdAddress", "thirdAddressPosition");

                    const visibleContainers = [firstAddressContainer, secondAddressContainer, thirdAddressContainer]
                        .filter(container => container.style.display !== "none");
 
                    let width = "100%";
                    if (visibleContainers.length === 2) width = "49%";
                    if (visibleContainers.length === 3) width = "30%";
 
                    visibleContainers.forEach(container => {
                        container.style.width = width;
                    });
                }

                applyHeaderOptions();
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        if (typeof ApiData !== "undefined" && ApiData && ApiData.hasOwnProperty("rental_invoices")) {
            let rentalInvoiceData = ApiData.rental_invoices;
            const bank_accounts = ApiData.bank_accounts;
            if (typeof invoiceData !== "undefined" && invoiceData.hasOwnProperty("footer_options")) {
                let footerOptions = invoiceData.footer_options;

                if (typeof footerOptions === "string") {
                    footerOptions = JSON.parse(footerOptions);
                }

                let numberFormatConfig = { number_format: "1.2" };
                function formatCurrency(number, config) {
                    if (number !== "" && !isNaN(number)) {
                        let formatParts = config.number_format.split(".");
                        let decimalPlaces = formatParts[1] ? parseInt(formatParts[1]) : 2;

                        return new Intl.NumberFormat("en-IN", {
                            style: "currency",
                            currency: "INR",
                            minimumFractionDigits: decimalPlaces,
                            maximumFractionDigits: decimalPlaces
                        }).format(number).replace("₹", "").trim();
                    }
                    return "0.00";
                }

                async function updateFooterField(headingSelector, valueSelector, optionKey) {
                    const headingElement = document.querySelector(headingSelector);
                    const valueElement = document.querySelector(valueSelector);
 
                    if (footerOptions.showFields && footerOptions.showFields[optionKey]?.visible) {
                        const keyFromDb = footerOptions.showFields[optionKey].value;
                        const headingText = footerOptions.showFields[optionKey].heading;
                        let matchingValue = rentalInvoiceData[keyFromDb];
 
                        // Date change
                        const dateFields = ['transaction_date', 'invoice_date', 'due_date', 'po_date', 'date', 'created_date', 'updated_date'];
                        const isDateField = dateFields.some(field => keyFromDb.toLowerCase().includes(field.toLowerCase()));
                       
                        if (isDateField && matchingValue) {
                            const config = await fetchConfigData();
                            matchingValue = formatDateFromConfig(matchingValue, config);
                        }
 
                        if (headingElement) {
                            headingElement.style.display = "block";
                            headingElement.innerHTML = `<span style="font-weight: 600;">${headingText} :</span>`;
                        }
                        if (valueElement) {
                            valueElement.style.display = "block";
                            valueElement.textContent = matchingValue !== undefined
                                ? (isDateField ? matchingValue : formatCurrency(matchingValue, numberFormatConfig))
                                : "-";
                        }
                    } else {
                        if (headingElement) headingElement.style.display = "none";
                        if (valueElement) valueElement.style.display = "none";
                    }
                }
 

                function updateSignatureAndQr() {
                    const signatureElement = document.querySelector(".signature");
                    const qrElement = document.querySelector(".qr");

                    if (footerOptions.showSignature && signatureElement) {
                        signatureElement.style.display = "inline-block";
                        // const signatureImg = signatureElement.querySelector("img");
                        // if (signatureImg) {
                        //     signatureImg.src = rentalInvoiceData.signature_url || "uploads/all/no-image.jpg";
                        //     signatureImg.alt = "Authorized Signature";
                        // }
                    } else if (signatureElement) {
                        signatureElement.style.display = "none";
                    }

                    if (footerOptions.showQr && qrElement) {
                        qrElement.style.display = "inline-block";
                        const qrImg = qrElement.querySelector("img");
                        if (qrImg) {
                            qrImg.src = "uploads/all/"+bank_accounts.upi_qr_code_image || "uploads/all/no-image.jpg";
                            qrImg.alt = "QR Code";
                        }
                    } else if (qrElement) {
                        qrElement.style.display = "none";
                    }
                }

                updateSignatureAndQr();

                function numberToWords(num) {
                    const a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
                    const b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

                    function convertToWords(n) {
                        if (n < 20) return a[n];
                        if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? " " + a[n % 10] : "");
                        if (n < 1000) return a[Math.floor(n / 100)] + " Hundred" + (n % 100 ? " " + convertToWords(n % 100) : "");
                        if (n < 100000) return convertToWords(Math.floor(n / 1000)) + " Thousand" + (n % 1000 ? " " + convertToWords(n % 1000) : "");
                        if (n < 10000000) return convertToWords(Math.floor(n / 100000)) + " Lakh" + (n % 100000 ? " " + convertToWords(n % 100000) : "");
                        return convertToWords(Math.floor(n / 10000000)) + " Crore" + (n % 10000000 ? " " + convertToWords(n % 10000000) : "");
                    }
                    return convertToWords(num);
                }

                if (footerOptions.showTotalAlpha) {
                    const totalAmountKey = "total_amount";
                    if (rentalInvoiceData.hasOwnProperty(totalAmountKey)) {
                        const totalAmountValue = rentalInvoiceData[totalAmountKey];
                        const totalInWords = numberToWords(parseInt(totalAmountValue));
                        const totalWordsElement = document.querySelector(".totalinwords");

                        if (totalWordsElement) {
                            totalWordsElement.style.display = "block";
                            totalWordsElement.innerHTML = `<span style='font-weight: 600;'>Invoice total in words</span>: ${totalInWords}`;
                        }
                    }
                } else {
                    const totalWordsElement = document.querySelector(".totalinwords");
                    if (totalWordsElement) {
                        totalWordsElement.style.display = "none";
                    }
                }

                const taxStateKey = "tax_state";
                const totalTaxKey = "tax_amount";
                if (rentalInvoiceData.hasOwnProperty(taxStateKey) && rentalInvoiceData.hasOwnProperty(totalTaxKey)) {
                    const taxStateValue = rentalInvoiceData[taxStateKey];
                    const totalTaxValue = parseFloat(rentalInvoiceData[totalTaxKey]) || 0;

                    const cgstElement = document.querySelector(".cgst");
                    const sgstElement = document.querySelector(".sgst");
                    const igstElement = document.querySelector(".igst");

                    function formatCurrency(number, config) {
                        if (number !== "" && !isNaN(number)) {
                            let formatParts = config.number_format.split(".");
                            let decimalPlaces = formatParts[1] ? parseInt(formatParts[1]) : 2;

                            return new Intl.NumberFormat("en-IN", {
                                style: "currency",
                                currency: "INR",
                                minimumFractionDigits: decimalPlaces,
                                maximumFractionDigits: decimalPlaces
                            }).format(number).replace("₹", "").trim();
                        }
                        return "0.00";
                    }

                    let numberFormatConfig = { number_format: "1.2" };

                    if (taxStateValue === "S") {
                        const halfTax = totalTaxValue / 2;
                        const formattedHalfTax = formatCurrency(halfTax, numberFormatConfig);

                        if (cgstElement && sgstElement) {
                            cgstElement.style.display = "inline";
                            cgstElement.innerHTML = `CGST: ${formattedHalfTax}<br>SGST: ${formattedHalfTax}`;
                            // Hide sgstElement since we're combining both in cgstElement
                            sgstElement.style.display = "none";
                        }
                        if (igstElement) {
                            igstElement.style.display = "none";
                            igstElement.textContent = "";
                        }
                    } else if (taxStateValue === "I") {
                        const formattedTax = formatCurrency(totalTaxValue, numberFormatConfig);
                        if (igstElement) {
                            igstElement.style.display = "inline";
                            igstElement.textContent = `IGST: ${formattedTax}`;
                        }
                        if (cgstElement) {
                            cgstElement.style.display = "none";
                            cgstElement.textContent = "";
                        }
                        if (sgstElement) {
                            sgstElement.style.display = "none";
                            sgstElement.textContent = "";
                        }
                    } else {
                        if (cgstElement) cgstElement.style.display = "none";
                        if (sgstElement) sgstElement.style.display = "none";
                        if (igstElement) igstElement.style.display = "none";
                    }
                }

                updateFooterField(".heading1", ".value1", "totalbeforetax");
                updateFooterField(".heading2", ".value2", "discount");
                updateFooterField(".heading3", ".value3", "totaltaxamount");
                updateFooterField(".heading4", ".value4", "roundedoff");
                updateFooterField(".heading4_5", ".value4_5", "securityamount");
                updateFooterField(".heading5", ".value5", "totalamount");
                updateFooterField(".heading6", ".value6", "totaldue");
            }
        }
    });

    document.addEventListener("DOMContentLoaded", async function () {
        if (!invoiceData || !invoiceData.table_options || !ApiData || !ApiData.rental_invoice_items) {
            console.error("Missing data: invoiceData or ApiData is undefined.");
            return;
        }

        let tableOptions;
        try {
            tableOptions = JSON.parse(invoiceData.table_options);
        } catch (error) {
            console.error("Error parsing table options:", error);
            return;
        }

        const {
            showColumns = {},
            bodyBgColor,
            bodyTextColor,
            fontSize,
            rowHeight,
            headerBgColor,
            headerTextColor,
            headingHeight,
            footerBgColor,
            footerTextColor,
            heading_amount,
            heading_tax,
            heading_des,
            heading_hsnsac,
            heading_img,
            heading_item,
            heading_qty,
            heading_discount,
            heading_rate,
            heading_uom,
            value_amount,
            value_tax,
            value_des,
            value_hsnsac,
            value_img,
            value_item,
            value_qty,
            value_discount,
            value_rate,
            value_uom
        } = tableOptions;

        const table = document.querySelector(".table");
        if (!table) {
            console.error("Table element not found.");
            return;
        }

        Object.assign(table.style, {
            backgroundColor: bodyBgColor || "#ffffff",
            color: bodyTextColor || "#000000",
            fontSize: fontSize ? `${fontSize}px` : "14px",
            borderCollapse: "collapse",
            width: "100%"
        });

        document.querySelectorAll(".table tr").forEach(row => {
            row.style.height = rowHeight ? `${rowHeight}px` : "auto";
        });

        const applyStyles = (selector, styles) => {
            document.querySelectorAll(selector).forEach(element => {
                Object.assign(element.style, styles);
            });
        };

        applyStyles(".table thead", {
            backgroundColor: headerBgColor || "#000000",
            color: headerTextColor || "#ffffff",
            height: headingHeight ? `${headingHeight}px` : "auto",
            fontWeight: "bold"
        });

        applyStyles(".table thead th, .table thead td", {
            color: headerTextColor || "#ffffff"
        });

        applyStyles(".table tfoot", {
            backgroundColor: footerBgColor || "#f8f9fa",
            color: footerTextColor || "#000000"
        });

        applyStyles(".table tfoot td", {
            color: footerTextColor || "#000000"
        });

        applyStyles(".table thead th, .table thead td, .table tbody tr td, .table tfoot td", {
            padding: "8px",
            border: "1px solid #ddd",
            textAlign: "left"
        });

        Object.keys(showColumns).forEach(column => {
            if (!showColumns[column] && column !== "item") {
                document.querySelectorAll(`.table .${column}`).forEach(cell => {
                    cell.style.display = "none";
                });
            }
        });

        const headerMap = {
            "amount": heading_amount,
            "tax": heading_tax,
            "des": heading_des,
            "hsnsac": heading_hsnsac,
            "img": heading_img,
            "item": heading_item,
            "qty": heading_qty,
            "discount": heading_discount,
            "rate": heading_rate,
            "uom": heading_uom
        };

        Object.keys(headerMap).forEach(column => {
            const headerCell = document.querySelector(`.table thead .${column} strong`);
            if (headerCell && headerMap[column]) {
                headerCell.textContent = headerMap[column];
            }
        });

        function formatCurrency(number, config) {
            if (number !== "" && !isNaN(number)) {
                let formatParts = config.number_format.split(".");
                let decimalPlaces = formatParts[1] ? parseInt(formatParts[1]) : 2;

                return new Intl.NumberFormat("en-IN", {
                    style: "currency",
                    currency: "INR",
                    minimumFractionDigits: decimalPlaces,
                    maximumFractionDigits: decimalPlaces
                }).format(number).replace("₹", "").trim();
            }
            return "0.00";
        }

        function formatNumber(number, decimalPlaces = 2) {
            if (number !== "" && !isNaN(number)) {
                return new Intl.NumberFormat("en-IN", {
                    minimumFractionDigits: decimalPlaces,
                    maximumFractionDigits: decimalPlaces
                }).format(number);
            }
            return "0";
        }

        const fieldMapping = {
            "col-1 sno": "index",
            "col-2 img": value_img,
            "col-3 item": value_item,
            "col-3 des": value_des,
            "col-3 uom": value_uom,
            "col-3 hsnsac text-end": value_hsnsac,
            "col-1 qty text-end": value_qty,
            "col-2 rate text-end": value_rate,
            "col-3 tax text-end": value_tax,
            "col-1 discount text-end": value_discount,
            "col-2 amount text-end": value_amount
        };

        // Get itemstable value from table_options for pagination
        const itemsPerPage = parseInt(tableOptions.itemstable) || 10; // Default to 10 if not set
        const allItems = ApiData.rental_invoice_items || [];
        const totalItems = allItems.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        console.log('Pagination Info:', {
            itemsPerPage: itemsPerPage,
            totalItems: totalItems,
            totalPages: totalPages,
            tableOptions: tableOptions
        });

        // Currency formatting function
        function formatCurrency(number, config = { number_format: "1.2" }) {
            if (number !== "" && !isNaN(number)) {
                let formatParts = config.number_format.split(".");
                let decimalPlaces = formatParts[1] ? parseInt(formatParts[1]) : 2;

                return new Intl.NumberFormat("en-IN", {
                    style: "currency",
                    currency: "INR",
                    minimumFractionDigits: decimalPlaces,
                    maximumFractionDigits: decimalPlaces
                }).format(number).replace("₹", "").trim();
            }
            return "0.00";
        }

        // Number to words conversion function
        function numberToWords(num) {
            const a = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
            const b = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

            function convertToWords(n) {
                if (n < 20) return a[n];
                if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? " " + a[n % 10] : "");
                if (n < 1000) return a[Math.floor(n / 100)] + " Hundred" + (n % 100 ? " " + convertToWords(n % 100) : "");
                if (n < 100000) return convertToWords(Math.floor(n / 1000)) + " Thousand" + (n % 1000 ? " " + convertToWords(n % 1000) : "");
                if (n < 10000000) return convertToWords(Math.floor(n / 100000)) + " Lakh" + (n % 100000 ? " " + convertToWords(n % 100000) : "");
                return convertToWords(Math.floor(n / 10000000)) + " Crore" + (n % 10000000 ? " " + convertToWords(n % 10000000) : "");
            }
            return convertToWords(num);
        }

        // Function to apply footer data to each page
        function applyFooterDataToPage(pageContainer, pageIndex, totalPages) {
            // Check if footer should be shown based on database setting
            const footerSection = pageContainer.querySelector('.page_footer.footersec');
            if (footerSection) {
                // Get showFooter setting from invoiceData
                let showFooter = false;
                if (typeof invoiceData !== "undefined" && invoiceData.hasOwnProperty("footer_options")) {
                    let footerOptions = invoiceData.footer_options;
                    if (typeof footerOptions === "string") {
                        try {
                            footerOptions = JSON.parse(footerOptions);
                            showFooter = footerOptions.showFooter || false;
                        } catch (error) {
                            showFooter = false;
                        }
                    } else if (typeof footerOptions === "object") {
                        showFooter = footerOptions.showFooter || false;
                    }
                }

                // Show footer only on last page AND if showFooter is true
                if (pageIndex === totalPages - 1 && showFooter) {
                    footerSection.style.display = 'block';
                } else {
                    // Hide footer on all other pages or when showFooter is false
                    footerSection.style.display = 'none';
                    return; // Exit early since we don't need to process footer data
                }
            }

            if (typeof invoiceData !== "undefined" && invoiceData.hasOwnProperty("footer_options")) {
                let footerOptions = invoiceData.footer_options;

                if (typeof footerOptions === "string") {
                    try {
                        footerOptions = JSON.parse(footerOptions);
                    } catch (error) {
                        footerOptions = {};
                    }
                }

                // Apply signature and QR code
                const signatureElement = pageContainer.querySelector(".signature");
                const qrElement = pageContainer.querySelector(".qr");

                // Signature
                if (footerOptions.showSignature && signatureElement) {
                    const signatureImg = signatureElement.querySelector("img");
                   // const signatureFallback = signatureElement.querySelector(".signature-fallback");

                    const hasSignature = ApiData?.rental_invoices?.signature_url?.trim();

                    if (hasSignature) {
                        signatureImg.style.display = "inline-block";
                        signatureImg.src = ApiData.rental_invoices.signature_url;
                        signatureImg.alt = "Authorized Signature";
                       // if (signatureFallback) signatureFallback.style.display = "none";
                    } else {
                        signatureImg.style.display = "none";
                        // if (signatureFallback) {
                        //     signatureFallback.textContent = "-";
                        //     signatureFallback.style.display = "block";
                        // }
                    }
                    signatureElement.style.display = "inline-block";
                } else if (signatureElement) {
                    signatureElement.style.display = "none";
                }

                // QR
                if (footerOptions.showQr && qrElement) {
                    const qrImg = qrElement.querySelector("img");
                    const qrFallback = qrElement.querySelector(".qr-fallback");

                    const hasQr = ApiData.bank_accounts.upi_qr_code_image;

                    if (hasQr) {
                        if (qrImg) {
                            qrImg.style.display = "block";
                            qrImg.src = "uploads/all/"+ApiData.bank_accounts.upi_qr_code_image;
                            qrImg.alt = "QR Code";
                        }
                        if (qrFallback) qrFallback.style.display = "none";
                        qrElement.style.display = "block";
                    } else {
                        if (qrImg) qrImg.remove();
 
                        if (qrFallback) {
                            qrFallback.textContent = "-";
                            qrFallback.style.display = "block";
                        } else {
                            qrElement.remove();
                        }
                    }
                    qrElement.style.display = "inline-block";
                } else if (qrElement) {
                    qrElement.style.display = "none";
                }

                // Apply bank details
                if (footerOptions.showBankDetails && ApiData.bank_accounts) {
                    const bankData = ApiData.bank_accounts;
                    const bankDetailsSection = pageContainer.querySelector('.bankdetails');

                    if (bankDetailsSection) {
                        bankDetailsSection.style.display = 'block';

                        const bankNameElement = pageContainer.querySelector('.bank_name');
                        const accountHolderElement = pageContainer.querySelector('.bank_contact_person');
                        const bankAccountNumberElement = pageContainer.querySelector('.bank_account_number');
                        const ifscCodeElement = pageContainer.querySelector('.ifsc_code');
                        const accountTypeElement = pageContainer.querySelector('.account_type');
                        const upiIdElement = pageContainer.querySelector('.upi_id');

                        if (bankNameElement) bankNameElement.textContent = bankData.bank_name || '-';
                        if (accountHolderElement) accountHolderElement.textContent = bankData.bank_contact_person || '-';
                        if (bankAccountNumberElement) bankAccountNumberElement.textContent = bankData.bank_account_number || '-';
                        if (ifscCodeElement) ifscCodeElement.textContent = bankData.ifsc_code || '-';

                        if (accountTypeElement) {
                            const accountType = bankData.account_type?.toUpperCase();
                            if (accountType === 'C') {
                                accountTypeElement.textContent = 'Current';
                            } else if (accountType === 'S') {
                                accountTypeElement.textContent = 'Saving';
                            } else {
                                accountTypeElement.textContent = accountType || '-';
                            }
                        }

                        if (upiIdElement) upiIdElement.textContent = bankData.upi_id || '-';
                    }
                } else {
                    const bankDetailsSection = pageContainer.querySelector('.bankdetails');
                    if (bankDetailsSection) {
                        bankDetailsSection.style.display = 'none';
                    }
                }


                // Apply total in words
                if (footerOptions.showTotalAlpha && ApiData.rental_invoices) {
                    const totalAmountKey = "total_amount";
                    if (ApiData.rental_invoices.hasOwnProperty(totalAmountKey)) {
                        const totalAmountValue = ApiData.rental_invoices[totalAmountKey];
                        const totalInWords = numberToWords(parseInt(totalAmountValue));
                        const totalWordsElement = pageContainer.querySelector(".totalinwords");

                        if (totalWordsElement) {
                            totalWordsElement.style.display = "block";
                            totalWordsElement.innerHTML = `<span style='font-weight: 600;'>Invoice total in words</span>: ${totalInWords}`;
                        }
                    }
                } else {
                    const totalWordsElement = pageContainer.querySelector(".totalinwords");
                    if (totalWordsElement) {
                        totalWordsElement.style.display = "none";
                    }
                }

                // Apply invoice summary fields
                function updateFooterFieldForPage(pageContainer, headingSelector, valueSelector, optionKey) {
                    const headingElement = pageContainer.querySelector(headingSelector);
                    const valueElement = pageContainer.querySelector(valueSelector);

                    if (footerOptions.showFields && footerOptions.showFields[optionKey]?.visible && ApiData.rental_invoices) {
                        const keyFromDb = footerOptions.showFields[optionKey].value;
                        const headingText = footerOptions.showFields[optionKey].heading;
                        const matchingValue = ApiData.rental_invoices[keyFromDb];

                        if (headingElement) {
                            headingElement.style.display = "block";
                            headingElement.innerHTML = `<span style="font-weight: 600;">${headingText} :</span>`;
                        }
                        if (valueElement) {
                            valueElement.style.display = "block";
                            valueElement.textContent = matchingValue !== undefined
                                ? formatCurrency(matchingValue, { number_format: "1.2" })
                                : "-";
                        }
                    } else {
                        if (headingElement) headingElement.style.display = "none";
                        if (valueElement) valueElement.style.display = "none";
                    }
                }

                updateFooterFieldForPage(pageContainer, ".heading1", ".value1", "totalbeforetax");
                updateFooterFieldForPage(pageContainer, ".heading2", ".value2", "discount");
                updateFooterFieldForPage(pageContainer, ".heading3", ".value3", "totaltaxamount");
                updateFooterFieldForPage(pageContainer, ".heading4", ".value4", "roundedoff");
                updateFooterFieldForPage(pageContainer, ".heading4_5", ".value4_5", "securityamount");
                updateFooterFieldForPage(pageContainer, ".heading5", ".value5", "totalamount");
                updateFooterFieldForPage(pageContainer, ".heading6", ".value6", "totaldue");

                // Apply tax breakdown (CGST/SGST/IGST)
                if (ApiData.rental_invoices) {
                    const taxStateKey = "tax_state";
                    const totalTaxKey = "tax_amount";

                    if (ApiData.rental_invoices.hasOwnProperty(taxStateKey) && ApiData.rental_invoices.hasOwnProperty(totalTaxKey)) {
                        const taxStateValue = ApiData.rental_invoices[taxStateKey];
                        const totalTaxValue = parseFloat(ApiData.rental_invoices[totalTaxKey]) || 0;

                        const cgstElement = pageContainer.querySelector(".cgst");
                        const sgstElement = pageContainer.querySelector(".sgst");
                        const igstElement = pageContainer.querySelector(".igst");
                        const totalTaxHeading = pageContainer.querySelector(".totaltaxamount.heading3");

                        if (taxStateValue === "S") {
                            const halfTax = totalTaxValue / 2;
                            const formattedHalfTax = formatCurrency(halfTax, { number_format: "1.2" });
 
                            if (cgstElement && sgstElement) {
                                cgstElement.style.display = "contents";
                                cgstElement.innerHTML = `CGST: ${formattedHalfTax}`;
                            }
                            if (sgstElement) {
                                sgstElement.style.display = "contents";
                                sgstElement.textContent = ` / SGST: ${formattedHalfTax}`;
                            }
                            if (igstElement) {
                                igstElement.style.display = "none";
                                igstElement.textContent = "";
                            }
 
                            // Add <br> tag after h6 element when CGST and SGST have data
                            // if (totalTaxHeading && totalTaxValue > 0) {
                            //     if (!totalTaxHeading.nextElementSibling || totalTaxHeading.nextElementSibling.tagName !== 'BR') {
                            //         const brElement = document.createElement('br');
                            //         totalTaxHeading.parentNode.insertBefore(brElement, totalTaxHeading.nextSibling);
                            //     }
                            // }
                        } else if (taxStateValue === "I") {
                            const formattedTax = formatCurrency(totalTaxValue, { number_format: "1.2" });
                            if (igstElement) {
                                igstElement.style.display = "contents";
                                igstElement.textContent = `IGST: ${formattedTax}`;
                            }
                            if (cgstElement) {
                                cgstElement.style.display = "none";
                                cgstElement.textContent = "";
                            }
                            if (sgstElement) {
                                sgstElement.style.display = "none";
                                sgstElement.textContent = "";
                            }

                            // Remove <br> tag when IGST is displayed
                            if (totalTaxHeading && totalTaxHeading.nextElementSibling && totalTaxHeading.nextElementSibling.tagName === 'BR') {
                                totalTaxHeading.nextElementSibling.remove();
                            }
                        } else {
                            if (cgstElement) cgstElement.style.display = "none";
                            if (sgstElement) sgstElement.style.display = "none";
                            if (igstElement) igstElement.style.display = "none";

                            // Remove <br> tag when no tax data
                            if (totalTaxHeading && totalTaxHeading.nextElementSibling && totalTaxHeading.nextElementSibling.tagName === 'BR') {
                                totalTaxHeading.nextElementSibling.remove();
                            }
                        }
                    }
                }

                // Apply additional notes
                const additionalNotesSection = pageContainer.querySelector('.additional_notes');
                if (additionalNotesSection) {
                    if (footerOptions.showAdditionalDetails) {
                        let additionalNotes = '';

                        if (ApiData.rental_invoices && ApiData.rental_invoices.printing_notes) {
                            additionalNotes = ApiData.rental_invoices.printing_notes.trim();
                        }

                        const content = additionalNotes !== '' ? additionalNotes : '<br><br><br>';
                        const notesP = additionalNotesSection.querySelector('p');
                        if (notesP) {
                            notesP.innerHTML = content;
                        }
                        additionalNotesSection.style.display = 'inline-block';
                        additionalNotesSection.style.width = '100%';
                    } else {
                        additionalNotesSection.style.display = 'none';
                    }
                }
            }
        }

        // Get the main invoice container
        const mainInvoiceContainer = document.querySelector('.invoice-preview');
        const parentContainer = mainInvoiceContainer.parentNode;

        // Clear existing content
        parentContainer.innerHTML = '';

        // Create multiple invoice pages
        for (let pageIndex = 0; pageIndex < totalPages; pageIndex++) {
            // Clone the original invoice structure for each page
            const pageContainer = mainInvoiceContainer.cloneNode(true);
            pageContainer.classList.add(`invoice-page-${pageIndex + 1}`);

            // Add page indicator
            const pageIndicator = document.createElement('div');
            pageIndicator.className = 'page-indicator';
            pageIndicator.textContent = `Page ${pageIndex + 1} of ${totalPages}`;

            // Add page break style for printing (except last page)
            if (pageIndex < totalPages - 1) {
                pageContainer.style.pageBreakAfter = 'always';
            }

            parentContainer.appendChild(pageIndicator);
            parentContainer.appendChild(pageContainer);

            // Apply footer data to each page (footer will only show on last page)
            applyFooterDataToPage(pageContainer, pageIndex, totalPages);

            // Get items for this page
            const startIndex = pageIndex * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
            const pageItems = allItems.slice(startIndex, endIndex);

            // Get table body for this page
            const tableBody = pageContainer.querySelector(".table tbody");
            const taxSummaryRow = pageContainer.querySelector(".table tbody .tax-summary");
            tableBody.innerHTML = "";

            const itemsArray = Object.values(ApiData.items || []);
            const unitMeasurements = Object.values(ApiData.unit_of_measurements || []);
            const rentalinvoiceitemtaxes = Object.values(ApiData.rental_invoice_item_taxes || []);

            let numberFormatConfig = { number_format: "1.2" };

            // Process items for this page
            pageItems.forEach((item, index) => {
                const globalIndex = startIndex + index; // Global index across all pages
                let row = document.createElement("tr");
                row.classList.add("data");

                Object.keys(fieldMapping).forEach(columnClass => {
                    let cell = document.createElement("td");
                    cell.className = `bordered ${columnClass}`;

                    let key = fieldMapping[columnClass];
                    let value = key === "index" ? globalIndex + 1 : (item[key] ?? "-");

                    if (value === "-") {
                        let itemData = itemsArray.find(i => i.id === item.item_id);
                        value = itemData ? itemData[key] : "-";
                    }

                    if (columnClass.includes("hsnsac")) {
                        let hsnsacData = rentalinvoiceitemtaxes.find(i => i.id === item.id);
                        value = hsnsacData ? hsnsacData.tax_name : "-";
                    }

                    if (columnClass.includes("uom")) {
                        let uomData = unitMeasurements.find(uom => uom.id === value);
                        value = uomData ? uomData.uom_name : "-";
                    }

                    if (columnClass.includes("img")) {
                        let img = document.createElement("img");
                        const fileUploadsPath = "uploads/all/";
                        const defaultImage = fileUploadsPath + "no-image.jpg";

                        if (value && value !== "-" && value.trim() !== "") {
                            img.src = fileUploadsPath + value;
                        } else {
                            img.src = defaultImage;
                        }

                        img.width = 35;
                        img.alt = value || "Item image";
                        cell.appendChild(img);
                    } else {
                        if (columnClass.includes("rate") || columnClass.includes("amount")) {
                            value = formatCurrency(parseFloat(value) || 0, numberFormatConfig);
                        } else if (columnClass.includes("qty") || columnClass.includes("tax")) {
                            value = formatNumber(parseFloat(value) || 0);
                        }
                        cell.textContent = value || "-";
                    }

                    let columnKey = columnClass.split(" ")[1];
                    if (columnKey && columnKey !== "sno" && !showColumns[columnKey]) {
                        cell.style.display = "none";
                    }

                    row.appendChild(cell);
                });

                tableBody.appendChild(row);
            });

            // Add tax summary row only to the last page
            if (pageIndex === totalPages - 1 && showColumns.taxsum && taxSummaryRow) {
                tableBody.appendChild(taxSummaryRow.cloneNode(true));
            }
        }

        // Calculate tax summary for the last page only
        try {
            let taxData = ApiData.rental_invoice_item_taxes;
            let taxSummary = {};

            function formatCurrency(number, config = { number_format: "1.2" }) {
                if (number !== "" && !isNaN(number)) {
                    let formatParts = config.number_format.split(".");
                    let decimalPlaces = formatParts[1] ? parseInt(formatParts[1]) : 2;

                    return new Intl.NumberFormat("en-IN", {
                        style: "currency",
                        currency: "INR",
                        minimumFractionDigits: decimalPlaces,
                        maximumFractionDigits: decimalPlaces
                    }).format(number).replace("₹", "").trim();
                }
                return "0.00";
            }

            taxData.forEach(item => {
                let taxValue = item.tax_value;

                if (!taxSummary[taxValue]) {
                    taxSummary[taxValue] = {
                        tax_value: taxValue,
                        tax_amount: 0,
                        total_amount: 0
                    };
                }
                taxSummary[taxValue].tax_amount += parseFloat(item.tax_amount) || 0;
                taxSummary[taxValue].total_amount += parseFloat(item.total_amount) || 0;
            });

            // Update tax summary only on the last page

            // let taxTd = document.querySelector(`.invoice-page-${totalPages} .tax-summary .hsnsac`);
            // let taxAmountTd = document.querySelector(`.invoice-page-${totalPages} .tax-summary .tax`);
            // let totalAmountTd = document.querySelector(`.invoice-page-${totalPages} .tax-summary .amount`);

            // if (taxTd && taxAmountTd && totalAmountTd) {
            //     let taxValues = Object.values(taxSummary)
            //         .map(({ tax_value }) => `@${tax_value}%`)
            //         .join("<br>");
            //     let taxAmounts = Object.values(taxSummary)
            //         .map(({ tax_amount }) => `${formatCurrency(tax_amount)}`)
            //         .join("<br>");
            //     let totalAmounts = Object.values(taxSummary)
            //         .map(({ total_amount }) => `${formatCurrency(total_amount)}`)
            //         .join("<br>");

            //     let totalTaxAmount = Object.values(taxSummary).reduce((sum, { tax_amount }) => sum + tax_amount, 0);
            //     let totalTotalAmount = Object.values(taxSummary).reduce((sum, { total_amount }) => sum + total_amount, 0);

            //     taxTd.innerHTML = taxValues;
            //     taxAmountTd.innerHTML = `${taxAmounts} <br><span style="font-weight: 700;">${formatCurrency(totalTaxAmount)}</span>`;
            //     totalAmountTd.innerHTML = `${totalAmounts} <br><span style="font-weight: 700;">${formatCurrency(totalTotalAmount)}</span>`;
            // }


            let taxSummaryRow = document.querySelector(`.invoice-page-${totalPages} .tax-summary`);
            let taxAmountTd = taxSummaryRow.querySelector(".tax");
            let totalAmountTd = taxSummaryRow.querySelector(".amount");
 
            if (taxSummaryRow && taxAmountTd && totalAmountTd) {
                let taxValues = Object.values(taxSummary)
                    .map(({ tax_value }) => `@${tax_value}%`)
                    .join("<br>");
                let taxAmounts = Object.values(taxSummary)
                    .map(({ tax_amount }) => `${formatCurrency(tax_amount)}`)
                    .join("<br>");
                let totalAmounts = Object.values(taxSummary)
                    .map(({ total_amount }) => `${formatCurrency(total_amount)}`)
                    .join("<br>");
 
                let totalTaxAmount = Object.values(taxSummary)
                    .reduce((sum, { tax_amount }) => sum + tax_amount, 0);
                let totalTotalAmount = Object.values(taxSummary)
                    .reduce((sum, { total_amount }) => sum + total_amount, 0);
 
                let cells = Array.from(taxSummaryRow.children);
                let taxIndex = cells.indexOf(taxAmountTd);
                let cellsToMerge = [];
 
                for (let i = 0; i < taxIndex; i++) {
                    let cell = cells[i];
                    if (cell && window.getComputedStyle(cell).display !== "none") {
                        cellsToMerge.push(cell);
                    }
                }
 
                if (cellsToMerge.length > 0) {
                    let firstCell = cellsToMerge[0];
                    firstCell.setAttribute("colspan", cellsToMerge.length);
                    firstCell.style.borderLeft = "none";
                    firstCell.style.borderRight = "none";
                    firstCell.style.textAlign = "right";
 
                    for (let i = 1; i < cellsToMerge.length; i++) {
                        cellsToMerge[i].remove();
                    }
   
                    firstCell.innerHTML = `${taxValues} <br><span style="font-weight:700;">Total</span>`;
                }
 
                taxAmountTd.innerHTML = `${taxAmounts} <br><span style="font-weight:700;">${formatCurrency(totalTaxAmount)}</span>`;
                totalAmountTd.innerHTML = `${totalAmounts} <br><span style="font-weight:700;">${formatCurrency(totalTotalAmount)}</span>`;
            }
 



        } catch (error) {
            console.error(error);
        }

        // Final safety check for footer visibility
        document.querySelectorAll('.page_footer.footersec').forEach((footerElement, index) => {
            let showFooter = false;
            if (typeof invoiceData !== "undefined" && invoiceData.hasOwnProperty("footer_options")) {
                let footerOptions = invoiceData.footer_options;
                if (typeof footerOptions === "string") {
                    try {
                        footerOptions = JSON.parse(footerOptions);
                        showFooter = footerOptions.showFooter || false;
                    } catch (error) {
                        showFooter = false;
                    }
                } else if (typeof footerOptions === "object") {
                    showFooter = footerOptions.showFooter || false;
                }
            }

            // If showFooter is false, hide all footers
            if (!showFooter) {
                footerElement.style.display = 'none';
                console.log('Footer hidden due to showFooter: false');
            }
        });
    });
</script>