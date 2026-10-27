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
//echo "<pre>"; print_r($config); echo "</pre>"; 
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
//echo "Y-m-d -- ".dateFormat(date('Y-m-d'),$config);
$rid = 2;
$url = API . "?mode=getdynamicinvocedetails&rid=" . $rid;
$response = callAPI($url);
$result2 = json_decode($response, true);
//print_r($result2);
//$application_dynamic_invoice_settings = $result2['application_dynamic_invoice_settings'];
$invData = isset($result2['application_dynamic_invoice_settings']) ? $result2['application_dynamic_invoice_settings'] : [];
echo "<script>";
echo "const invoiceData = " . json_encode($invData) . ";";
echo "const ApiData = " . json_encode($result) . ";";
echo "</script>";
?>

<?php
// $_SESSION['apply_js']['multiselest'] = 1;
// $rid = 13;
// $url = API . "?mode=getdynamicinvocedetails&rid=" . $rid;
// $response = callAPI($url);
// $result = json_decode($response, true);

// $invData = isset($result['dynamic_invoice_data']) ? $result['dynamic_invoice_data'] : [];

// $url = "https://dev.infistaq.in/api.php?mode=getdynamicinvoiceprint&transaction_id=24&transaction_table=purchase_invoices&data_name=infistaqin_infisuite_2&user_id=94&company_id=1";
// $api = file_get_contents($url);
// $api = json_decode($api, true);

// echo "<script>";
// echo "const invoiceData = " . json_encode($invData) . ";";
// echo "const ApiData = " . json_encode($api) . ";";
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
                document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
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
        <div class="" style="width: 793px;margin: 0 auto;"> <?php } ?>
        <div class="card" style="width: 793px; margin: 0 auto;">
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
                </style>
                <div class="container mt-4">
                    <div class="row ml-20 mr-20">
                        <div class="col-lg-12">
                            <div class="invoice-preview bg-light bordered"
                                style="min-height: 400px; position: relative; margin-top: 10px;">
                                <div class="top-header logosec" style="position:relative;width: 100%;">
                                    <div class="head-one">
                                        <img id="logoPreview" src="" alt=""
                                            style="display: block;position: absolute; max-height: 80px; left: 0; top: 0;">
                                        <div class="invoice-no" style="display: block; text-align: right;">
                                            <p class="invoice" data-label="Invoice No"
                                                style="font-weight: 100;margin: 0;"></p>
                                            <p class="date" data-label="Date" style="font-weight: 100;margin: 0;"></p>
                                            <p class="due_date" data-label="Due Date"
                                                style="font-weight: 100;margin: 0;"></p>
                                            <p class="po_num" data-label="P.O. Number"
                                                style="font-weight: 100;margin: 0;"></p>
                                            <p class="po_date" data-label="P.O. Date"
                                                style="font-weight: 100;margin: 0;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="address-sec bordered headersec" style="padding: .5rem;margin-top: 5px;">
                                    <div class="address-sec"
                                        style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;">
                                        <div class="first_address">
                                            <strong class="address1heading" style="font-weight: 600;"></strong>
                                            <address class="address1data">

                                            </address>
                                        </div>
                                        <div class="second_address">
                                            <strong class="address2heading" style="font-weight: 600;"></strong>
                                            <address class="address2data">

                                            </address>
                                        </div>
                                        <div class="third_address">
                                            <strong class="address3heading" style="font-weight: 600;"></strong>
                                            <address class="address3data">

                                            </address>
                                        </div>
                                    </div>
                                </div>
                                <div class="items tablesec" style="margin-top: .5rem;">
                                    <div class="table-responsive">
                                        <table class="table bordered mb-10">
                                            <thead>
                                                <tr class="">
                                                    <td class="bordered col-1"><strong>S.No.</strong></td>
                                                    <td class="bordered img col-2"><strong>Image</strong></td>
                                                    <td class="bordered item col-2"><strong>Item Name</strong></td>
                                                    <td class="bordered des col-2"><strong>Description</strong></td>
                                                    <td class="bordered hsnsac col-2 text-end"><strong>HSN/SAC</strong>
                                                    </td>
                                                    <td class="bordered uom col-2"><strong>UOM</strong></td>
                                                    <td class="bordered tax col-2 text-end"><strong>Tax</strong></td>
                                                    <td class="bordered rate col-2 text-end"><strong>Rate</strong></td>
                                                    <td class="bordered qty col-1 text-end"><strong>QTY</strong></td>
                                                    <td class="bordered amount col-2 text-end"><strong>Amount</strong>
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                <tr class="data">
                                                    <td class="bordered col-1"><?= $i ?></td>
                                                    <td class="bordered img col-2"><img width="35px" src="" alt=""></td>
                                                    <td class="bordered item col-3 text-1"></td>
                                                    <td class="bordered des col-3 text-1"></td>
                                                    <td class="bordered hsnsac col-3 text-end"></td>
                                                    <td class="bordered uom col-3 text-1"></td>
                                                    <td class="bordered tax col-3 text-1 text-end"
                                                        data-original="23.00"></td>
                                                    <td class="bordered rate col-2 text-end"></td>
                                                    <td class="bordered qty col-1 text-end"></td>
                                                    <td class="bordered amount col-2 text-end"></td>
                                                </tr>
                                                <?php $i++; ?>
                                                <tr class="tax-summary">
                                                    <td class="bordered col-1"></td>
                                                    <td class="bordered img col-2"></td>
                                                    <td class="bordered item col-3 text-end"><br><br>
                                                        <span style="font-weight: 700;">Total</span>
                                                    </td>
                                                    <td class="bordered des col-3 text-1"></td>
                                                    <td class="bordered hsnsac col-3 text-end"></td>
                                                    <td class="bordered uom col-3 text-1"></td>
                                                    <td class="bordered tax col-2 text-end">
                                                        <span style="font-weight: 700;"></span>
                                                    </td>
                                                    <td class="bordered rate col-2 text-end"></td>
                                                    <td class="bordered qty col-1 text-end"></td>
                                                    <td class="bordered amount col-2 text-end">
                                                        <span style="font-weight: 700;"></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="page_footer bordered footersec" style="margin-top: .5rem;">
                                    

                                    <table style="width: 100%; justify-content: space-between; flex-wrap: nowrap; gap: 20px;padding: 5px;">
                                        <tr>
                                        <td class="first_col signatureqr" style="vertical-align:top">
                                            <p class="totalinwords"></p>
                                            <div
                                                style="display: flex; justify-content: space-between; flex-wrap: nowrap; gap: 20px;">
                                                <div class="signature">
                                                    <h6 style="text-decoration: underline;margin-bottom: 5px;">
                                                        Autherized Signature</h6>
                                                    <img width="180px" src="" alt="">
                                                </div>
                                                <div class="qr">
                                                    <img width="120px" src="" alt="">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="invoicesummary"
                                            style="justify-content: space-between; flex-wrap: nowrap; gap: 20px;padding: 5px;">
                                            <div class="sec_col">
                                                <table style="width: 100%; border-collapse: collapse;">
                                                <tbody>
                                                        <tr>
                                                            <td class="bordered totalbeforetax heading1"
                                                                style="text-align: left;"></td>
                                                            <td class="bordered totalbeforetax value1"
                                                                style="text-align: right;"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bordered discount heading2" style="text-align: left;">
                                                            </td>
                                                            <td class="bordered discount value2" style="text-align: right;"></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bordered totaltaxamount heading3"
                                                                style="text-align: left;"></td>
                                                            <td class="bordered totaltaxamount" style="text-align: right;">
                                                                <span class="cgst"></span>
                                                                <span class="sgst"></span>
                                                                <span class="igst"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bordered roundedoff heading4" style="text-align: left;">
                                                            </td>
                                                            <td class="bordered roundedoff value4" style="text-align: right;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bordered totalamount heading5" style="text-align: left;">
                                                            </td>
                                                            <td class="bordered totalamount value5" style="text-align: right;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bordered totaldue heading6" style="text-align: left;">
                                                            </td>
                                                            <td class="bordered totaldue value6" style="text-align: right;"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    </table>
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
                    // element.style.height = basicOptions.height + "px";
                });
            }
            applyBasicStyles();
        }
    });
        document.addEventListener("DOMContentLoaded", function () {
        if (typeof ApiData !== "undefined" && ApiData) {
            let rentalInvoiceData = ApiData.hasOwnProperty("rental_invoices") ? ApiData.rental_invoices : null;
            let configData = ApiData.hasOwnProperty("config") ? ApiData.config : null;
 
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
 
                function dateFormat(dateStr) {
                    if (!dateStr || dateStr === "0000-00-00") return "";
 
                    const date = new Date(dateStr);
                    if (isNaN(date)) return dateStr;
 
                    if (!configData || !configData.date_format || !configData.date_separator) {
                        return dateStr; // Fallback to original value if config is missing
                    }
 
                    let format = configData.date_format
                        .toLowerCase()
                        .replace("yyyy", "Y")
                        .replace("mm", "m")
                        .replace("dd", "d")
                        .replace("-", configData.date_separator);
 
                    const formattedDate = format
                        .replace("Y", date.getFullYear())
                        .replace("m", String(date.getMonth() + 1).padStart(2, "0"))
                        .replace("d", String(date.getDate()).padStart(2, "0"));
 
                    return formattedDate;
                }
 
                function updateField(selector, apiKey, isVisible, isDateField = false) {
                    const element = document.querySelector(selector);
 
                    if (!element || !rentalInvoiceData || !apiKey || !rentalInvoiceData.hasOwnProperty(apiKey) || !isVisible) {
                        if (element) element.style.display = "none";
                        return;
                    }
 
                    let value = rentalInvoiceData[apiKey];
 
                    if (isDateField) {
                        value = dateFormat(value);
                    }
 
                    element.style.display = "block";
                    element.innerHTML = `<span style="font-weight: 600;">${element.dataset.label} :</span> ${value}`;
 
                    if (invoiceElement) {
                        element.style.textAlign = invoiceElement.style.textAlign;
                    }
                }
 
                updateField(".invoice", logoOptions.invoiceNoValue, logoOptions.showInvoiceNo);
                updateField(".date", logoOptions.dateValue, logoOptions.showDate, true);
                updateField(".due_date", logoOptions.dueDateValue, logoOptions.showDueDate, true);
                updateField(".po_num", logoOptions.poNumValue, logoOptions.showPoNum);
                updateField(".po_date", logoOptions.poDateValue, logoOptions.showPoDate, true);
            }
        }
    });
 



    // document.addEventListener("DOMContentLoaded", function () {
    //     if (typeof ApiData !== "undefined" && ApiData) {
    //         let rentalInvoiceData = ApiData.hasOwnProperty("rental_invoices") ? ApiData.rental_invoices : null;
    //         let configData = ApiData.hasOwnProperty("config") ? ApiData.config : null;

    //         if (typeof invoiceData !== "undefined" && invoiceData.hasOwnProperty("logo_options")) {
    //             let logoOptions = invoiceData.logo_options;

    //             if (typeof logoOptions === "string") {
    //                 try {
    //                     logoOptions = JSON.parse(logoOptions);
    //                 } catch (error) {
    //                     logoOptions = {};
    //                 }
    //             }

    //             const logoElement = document.getElementById("logoPreview");
    //             const invoiceElement = document.querySelector(".top-header .invoice-no");
    //             const headerContainer = document.querySelector(".invoice-header");
    //             const fileUploadsPath = "uploads/all/";
    //             const defaultLogo = fileUploadsPath + "no-image.jpg";

    //             if (logoElement && logoOptions.showLogo) {
    //                 if (configData && configData.hasOwnProperty("display_logo") && configData.display_logo.trim() !== "") {
    //                     logoElement.src = fileUploadsPath + configData.display_logo;
    //                 } else {
    //                     logoElement.src = defaultLogo;
    //                 }

    //                 logoElement.alt = fileUploadsPath + configData.display_logo;
    //                 logoElement.style.display = "block";
    //                 logoElement.style.maxHeight =
    //                     logoOptions.logoSize === "custom"
    //                         ? `${logoOptions.customLogoSize}px`
    //                         : logoOptions.logoSize === "small"
    //                             ? "40px"
    //                             : logoOptions.logoSize === "medium"
    //                                 ? "80px"
    //                                 : "95px";

    //                 const positionStyles = {
    //                     left: {
    //                         left: "0", right: "auto", transform: "none",
    //                         textAlign: "left", position: "absolute"
    //                     },
    //                     center: {
    //                         left: "50%", transform: "translateX(-50%)", textAlign: "center",
    //                         position: "relative", paddingBottom: "10px"
    //                     },
    //                     right: {
    //                         left: "auto", right: "0", transform: "none",
    //                         textAlign: "right", position: "absolute"
    //                     }
    //                 };

    //                 Object.assign(logoElement.style, positionStyles[logoOptions.logoPosition] || {});

    //                 if (invoiceElement) {
    //                     invoiceElement.style.display = "block";
    //                     invoiceElement.style.left = logoOptions.logoPosition === "right" ? "auto" : "0";
    //                     invoiceElement.style.right = logoOptions.logoPosition === "right" ? "0" : "0";
    //                     invoiceElement.style.transform = logoOptions.logoPosition === "right" ? "none" : "0";
    //                     invoiceElement.style.textAlign = logoOptions.logoPosition === "right" ? "left" : "0";
    //                     invoiceElement.style.textAlign = logoOptions.logoPosition === "center" ? "center" : "0";
    //                 }

    //                 if (headerContainer) {
    //                     headerContainer.style.textAlign = positionStyles[logoOptions.logoPosition]?.textAlign || "left";
    //                 }
    //             } else if (logoElement) {
    //                 logoElement.style.display = "none";
    //             }

    //             function dateFormat(dateStr, config) {
    //                 if (!dateStr || dateStr === "0000-00-00") return "";

    //                 const date = new Date(dateStr);
    //                 if (isNaN(date)) return dateStr;

    //                 let format = config.date_format
    //                     .toLowerCase()
    //                     .replace("yyyy", "Y")
    //                     .replace("mm", "m")
    //                     .replace("dd", "d");

    //                 format = format.replace("-", config.date_separator);

    //                 const formattedDate = format
    //                     .replace("Y", date.getFullYear())
    //                     .replace("m", String(date.getMonth() + 1).padStart(2, "0"))
    //                     .replace("d", String(date.getDate()).padStart(2, "0"));

    //                 return formattedDate;
    //             }

    //             function updateField(selector, apiKey, isVisible, isDateField = false) {
    //                 const element = document.querySelector(selector);

    //                 if (!element || !rentalInvoiceData || !apiKey || !rentalInvoiceData.hasOwnProperty(apiKey) || !isVisible) {
    //                     if (element) element.style.display = "none";
    //                     return;
    //                 }

    //                 let value = rentalInvoiceData[apiKey];

    //                 if (isDateField) {
    //                     value = dateFormat(value, configData);
    //                 }
    //                 console.log(configData);
    //                 element.style.display = "block";
    //                 element.innerHTML = `<span style="font-weight: 600;">${element.dataset.label} :</span> ${value}`;

    //                 if (invoiceElement) {
    //                     element.style.textAlign = invoiceElement.style.textAlign;
    //                 }
    //             }

    //             updateField(".invoice", logoOptions.invoiceNoValue, logoOptions.showInvoiceNo);
    //             updateField(".date", logoOptions.dateValue, logoOptions.showDate);
    //             updateField(".due_date", logoOptions.dueDateValue, logoOptions.showDueDate);
    //             updateField(".po_num", logoOptions.poNumValue, logoOptions.showPoNum);
    //             updateField(".po_date", logoOptions.poDateValue, logoOptions.showPoDate);
    //         }
    //     }
    // });


    document.addEventListener("DOMContentLoaded", function () {
        if (typeof ApiData !== "undefined" && ApiData) {
            let rentalInvoiceData = ApiData.hasOwnProperty("rental_invoices") ? ApiData.rental_invoices : null;

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

                    const firstAddressHeading = document.querySelector(".address1heading");
                    const firstAddressData = document.querySelector(".address1data");

                    const secondAddressHeading = document.querySelector(".address2heading");
                    const secondAddressData = document.querySelector(".address2data");

                    const thirdAddressHeading = document.querySelector(".address3heading");
                    const thirdAddressData = document.querySelector(".address3data");

                    if (headerOptions.showAddress) {
                        addressSection.style.display = "block";
                    } else {
                        addressSection.style.display = "none";
                        return;
                    }

                    function updateAddressField(headingElem, dataElem, headingKey, valueKey) {
                        if (!rentalInvoiceData || !headingElem || !dataElem) return;

                        let heading = headerOptions[headingKey];
                        let valueKeyMapped = headerOptions[valueKey];
                        let value = rentalInvoiceData.hasOwnProperty(valueKeyMapped) ? rentalInvoiceData[valueKeyMapped] : null;

                        console.log(`Updating ${headingKey}:`, heading, value);

                        if (heading && value) {
                            headingElem.textContent = heading;
                            dataElem.textContent = value;
                            headingElem.style.display = "block";
                            dataElem.style.display = "block";
                        } else {
                            headingElem.style.display = "none";
                            dataElem.style.display = "none";
                        }
                    }

                    updateAddressField(firstAddressHeading, firstAddressData, "firstAddressHeading", "firstAddressValue");
                    updateAddressField(secondAddressHeading, secondAddressData, "secondAddressHeading", "secondAddressValue");
                    updateAddressField(thirdAddressHeading, thirdAddressData, "thirdAddressHeading", "thirdAddressValue");
                }

                applyHeaderOptions();
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        if (typeof ApiData !== "undefined" && ApiData && ApiData.hasOwnProperty("rental_invoices")) {
            let rentalInvoiceData = ApiData.rental_invoices;

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

                function updateFooterField(headingSelector, valueSelector, optionKey) {
                    const headingElement = document.querySelector(headingSelector);
                    const valueElement = document.querySelector(valueSelector);

                    if (footerOptions.showFields && footerOptions.showFields[optionKey]?.visible) {
                        const keyFromDb = footerOptions.showFields[optionKey].value;
                        const headingText = footerOptions.showFields[optionKey].heading;
                        const matchingValue = rentalInvoiceData[keyFromDb];

                        if (headingElement) {
                            headingElement.style.display = "table-cell";
                            headingElement.innerHTML = `<span style="font-weight: 600;white-space: nowrap;">${headingText}</span>`;
                        }
                        if (valueElement) {
                            valueElement.style.display = "table-cell";
                            valueElement.textContent = matchingValue !== undefined
                                ? formatCurrency(matchingValue, numberFormatConfig)
                                : "N/A";
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
                        signatureElement.style.display = "block";
                        const signatureImg = signatureElement.querySelector("img");
                        if (signatureImg) {
                            signatureImg.src = rentalInvoiceData.signature_url || "";
                            signatureImg.alt = "Authorized Signature";
                        }
                    } else if (signatureElement) {
                        signatureElement.style.display = "none";
                    }

                    if (footerOptions.showQr && qrElement) {
                        qrElement.style.display = "block";
                        const qrImg = qrElement.querySelector("img");
                        if (qrImg) {
                            qrImg.src = rentalInvoiceData.qr_url || "";
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
                            totalWordsElement.textContent = `${totalInWords}`;
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

                        if (cgstElement) {
                            cgstElement.style.display = "inline";
                            cgstElement.textContent = `CGST: ${formattedHalfTax}`;
                        }
                        if (sgstElement) {
                            sgstElement.style.display = "inline";
                            sgstElement.textContent = `/ SGST: ${formattedHalfTax}`;
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
            heading_rate,
            heading_uom,
            value_amount,
            value_tax,
            value_des,
            value_hsnsac,
            value_img,
            value_item,
            value_qty,
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
            "col-3 hsnsac text-end": value_hsnsac,
            "col-3 uom": value_uom,
            "col-3 tax text-end": value_tax,
            "col-2 rate text-end": value_rate,
            "col-1 qty text-end": value_qty,
            "col-2 amount text-end": value_amount
        };

        const tableBody = document.querySelector(".table tbody");

        const taxSummaryRow = document.querySelector(".table tbody .tax-summary");
        tableBody.innerHTML = "";

        const itemsArray = Object.values(ApiData.items || []);
        const unitMeasurements = Object.values(ApiData.unit_of_measurements || []);
        const rentalinvoiceitemtaxes = Object.values(ApiData.rental_invoice_item_taxes || []);

        let numberFormatConfig = { number_format: "1.2" };

        ApiData.rental_invoice_items.forEach((item, index) => {
            let row = document.createElement("tr");
            row.classList.add("data");

            Object.keys(fieldMapping).forEach(columnClass => {
                let cell = document.createElement("td");
                cell.className = `bordered ${columnClass}`;

                let key = fieldMapping[columnClass];
                let value = key === "index" ? index + 1 : (item[key] ?? "-");

                if (value === "-") {
                    let itemData = itemsArray.find(i => i.id === item.item_id);
                    value = itemData ? itemData[key] : "-";
                }

                if (columnClass.includes("hsnsac") && item.value_hsnsac == "hsn_and_sac_code_id") {
                    let hsnsacData = rentalinvoiceitemtaxes.find(i => i.id === item.id);
                    value = hsnsacData ? hsnsacData.tax_name : "-";
                }

                if (columnClass.includes("uom") && item.value_uom == "purchase_uom") {
                    let uomData = unitMeasurements.find(uom => uom.id === value);
                    value = uomData ? uomData.uom_name : "-";
                }

                if (columnClass.includes("img")) {
                    let img = document.createElement("img");
                    img.src = value;
                    img.width = 35;
                    img.alt = value;
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

        if (showColumns.taxsum && taxSummaryRow) {
            tableBody.appendChild(taxSummaryRow);
        }

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

            let taxTd = document.querySelector(".tax-summary .item");
            let taxAmountTd = document.querySelector(".tax-summary .tax");
            let totalAmountTd = document.querySelector(".tax-summary .amount");

            if (taxTd && taxAmountTd && totalAmountTd) {
                let taxValues = Object.values(taxSummary)
                    .map(({ tax_value }) => `@${tax_value}%`)
                    .join("<br>");
                let taxAmounts = Object.values(taxSummary)
                    .map(({ tax_amount }) => `${formatCurrency(tax_amount)}`)
                    .join("<br>");
                let totalAmounts = Object.values(taxSummary)
                    .map(({ total_amount }) => `${formatCurrency(total_amount)}`)
                    .join("<br>");

                let totalTaxAmount = Object.values(taxSummary).reduce((sum, { tax_amount }) => sum + tax_amount, 0);
                let totalTotalAmount = Object.values(taxSummary).reduce((sum, { total_amount }) => sum + total_amount, 0);

                taxTd.innerHTML = `${taxValues} <br> <span style="font-weight: 600;">Total :</span> `;
                taxAmountTd.innerHTML = `${taxAmounts} <br><span style="font-weight: 700;">${formatCurrency(totalTaxAmount)}</span>`;
                totalAmountTd.innerHTML = `${totalAmounts} <br><span style="font-weight: 700;">${formatCurrency(totalTotalAmount)}</span>`;
            }
        } catch (error) {
            console.error(error);
        }
    });

</script>