<?php 
if($_GET['print'] =="Y"){
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
    $callapi = "Y";
}else{
    include_once('dbconfig.php');
}
if($_POST['callapi'] =='Y'){
    $config = $_POST['config'];
    $transaction = $_POST['rental_invoices'];
    $transaction_items = $_POST['rental_invoice_items'];
    $bill_by = $_POST['bill_by'];
    $bank_accounts = $_POST['bank_accounts'];
    $items  = $_POST['items'];
    $callapi = "Y";
}else{
    $rid = $_GET['rid'];
    $table = $_GET['table'];    
    $api = API."?mode=getdynamicinvoiceprint&transaction_id=".$rid."&transaction_table=".$table; 
    $apicall = callAPI($api);
    $result = json_decode($apicall, 1);
    $config = $result['config'];
    $transaction = $result['rental_invoices'];
    $transaction_items = $result['rental_invoice_items'];
    $bill_by = $result['bill_by'];
    $bank_accounts = $result['bank_accounts'];
    $_SESSION['pdf_file_name'] = $table;
    $items  = $result['items'];
//echo "<pre>";print_r($result); echo "</pre>";
}
$party_additional_details = json_decode($transaction['party_additional_details'],1);
function currencyFormat($number,$config){
    if($number !=""){
        $expoldform = explode('.',$config['number_format']);
        if($expoldform[0]== 1){
            $formatter = new NumberFormatter('en_IN', NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $expoldform[1]);
            $formattedNumber = $formatter->formatCurrency($number, 'INR');
            return $formattedNumber;
        }else{
            return number_format($number, $expoldform[1], '.', ',');
        }
    }
}
function numberToWordFormate($number){

    $no = floor($number ?? 0);
    $point = (int) round((($number ?? 0) - $no) * 100);
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = array();
    $words = array('0' => '', '1' => 'one', '2' => 'two',
        '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
        '7' => 'seven', '8' => 'eight', '9' => 'nine',
        '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
        '13' => 'thirteen', '14' => 'fourteen',
        '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
        '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
        '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
        '60' => 'sixty', '70' => 'seventy',
        '80' => 'eighty', '90' => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count((array)$str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
        } else $str[] = null;
    }
    $str = array_reverse((array)$str);
    $result = implode('', (array)$str);
    $points = ($point) ?
    ". " . $words[(int)($point / 10)] . " " . $words[$point = $point % 10] : '';
    if($result !=''){
        $result = $result . "Rupees  ";
    }
    if($points !=''){
        $result.= $points . " Paise  ";
    }
    echo ucwords($result." only");
}
function emptyCheck($valu){
    $value = "";
    if($valu !=""){
        $value = $valu;
    }else{
        $value = " - ";
    }
    return $value;
}
// function dateFormat($from='',$config){
//  $configdate_format = str_replace('-',$config['date_separator'], strtolower($config['date_format']));
//  $date_formate = str_replace('yyyy', 'Y', str_replace('mm', 'm', str_replace('dd', 'd', $configdate_format)));
//  $from = $from!='0000-00-00' ? $from :'';
//  $dateformat = $date_formate!="0"?$date_formate: 'Y-m-d';
//     if($from != ""){
//      $from =  date($dateformat,strtotime($from));
//  }else{
//      $from = date($dateformat);
//  }
//  return $from; 
// }
?>
<script>
    function printDiv(divName) {
       var printContents = document.getElementById(divName).innerHTML;
       var originalContents = document.body.innerHTML;
                // document.getElementById('header').style.display = 'none';
                 //document.getElementById('footer').style.display = 'none';
       document.body.innerHTML = printContents;

       window.print();

       document.body.innerHTML = originalContents;
   }
</script>
<!-- [ navigation menu ] end -->
 


<div class="card" style="width: 793px; margin: 0 auto;">
    <div class="card-body" id="printableArea">

        <!-- Required Fremwork -->
        <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
        <style type="text/css">
            @media (orientation: portrait) {
                .container {width: 100%; border: none; padding: 0;}
            }
            @print{
                .container{100%;padding:0;border:none;}
            }
            td, th{font-size: 13px; padding: 5px !important; vertical-align: middle; white-space: normal !important;}
            @media (max-width:772px){
                .scan{width: 36%;}
            }
            table{
                border-collapse : collapse;
            }
            .swatches{width: 20px; height: 20px; border-radius: 50px; border: 1px solid #cccccc; display: inline-block;}
        </style>


            <table class="table">
                <tr>
                    <td class="p-2 f-12 border-0">
                        <img src="uploads/all/file_1704095913_659270a975d4a.png" style="width:100px; height: 34px;">
                    </td>
                    <td class="p-2 f-12 border-0">
                        <b class="f-40">Invoice</b>
                        <table>
                            <tr>
                                <th class="f-12 p-0">
                                    Invoice#:      
                                </th>
                                <th class="f-12 p-0">
                                    &nbsp 57989            
                                </th>
                            </tr>
                            <tr>
                                <th class="f-12 p-0">
                                    Date:            
                                </th>
                                <th class="f-12 p-0">
                                    &nbsp 04 June 2024            
                                </th>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="p-2 f-12 border-0 pt-4 pb-4">
                        <span>Bill To</span>
                        <b class="f-w-600 d-block">Jonathan Deo</b>
                        <p class="m-0 f-12">812, Block 5, MH2 BUILDING, HYD, <br />500009</p>
                    </td>
                    <td class="p-2 f-12 border-0 pt-4 pb-4">
                        <p class="m-0 f-12">Total Due</p>
                        <p class="m-0 f-w-600 f-14">USD: $1400</p>
                    </td>
                </tr>
          
                <tr style="border-bottom: 1px solid #000000;">
                    <th class="f-12">ITEM DESCRIPTION</th>
                    <th class="f-12 text-center">PRICE</th>
                    <th class="f-12 text-cneter">QTY</th>
                    <th class="f-12 text-end">TOTAL</th>
                </tr>
                <tr>
                    <td class="f-12" style="border-right: 1px solid #000000;">
                        <b class="f-w-600 m-0">Web Design</b>
                        <p class="m-0 f-12">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    </td>
                    <th class="f-12 text-center" align="center" style="border-right: 1px solid #000000;">
                        $150.00
                    </th>
                    <th class="f-12 text-center" align="center" style="border-right: 1px solid #000000;">
                        2
                    </th>
                    <th class="f-12 text-end" align="right">
                        $150.00
                    </th>
                </tr>
                <tr>
                    <td class="f-12" style="border-right: 1px solid #000000;">
                        <b class="f-w-600 m-0">Wedding Photography</b>
                        <p class="m-0 f-12">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    </td>
                    <th class="f-12 text-center" align="right" style="border-right: 1px solid #000000;">
                        $150.00
                    </th>
                    <th class="f-12 text-center" align="right" style="border-right: 1px solid #000000;">
                        2
                    </th>
                    <th class="f-12 text-end" align="right">
                        $150.00
                    </th>
                </tr>
                <tr>
                    <td class="f-12" style="border-right: 1px solid #000000;">
                        <b class="f-w-600 m-0">Web Development</b>
                        <p class="m-0 f-12">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    </td>
                    <th class="f-12 text-center" align="right" style="border-right: 1px solid #000000;">
                        $150.00
                    </th>
                    <th class="f-12 text-center" align="right" style="border-right: 1px solid #000000;">
                        2
                    </th>
                    <th class="f-12 text-end" align="right">
                        $150.00
                    </th>
                </tr>
                <tr style="border-bottom: 1px solid #000000;">
                    <td class="f-12" style="border-right: 1px solid #000000;">
                        -
                    </td>
                    <td class="f-12" align="right" style="border-right: 1px solid #000000;">
                        -
                    </td>
                    <td class="f-12" align="right" style="border-right: 1px solid #000000;">
                        -
                    </td>
                    <td class="f-12" align="right">
                        -
                    </td>
                </tr>
                <tr>
                    <td class="border-0"></td>
                    <td class="f-12 border-0" colspan="" align="left">
                        <b class="f-w-600">Sub Total</b>
                        <b class="d-block">Tax Vet 10%</b>
                        <b class="d-block">Discount 10%</b>
                    </td>
                    <td class="f-12 border-0" colspan="2" align="right">
                        <b class="f-w-600">$450.00</b>
                        <b class="d-block">$120.00</b>
                        <b class="d-block">-$100.00</b>
                    </td>
                </tr>
                <tr>
                    <td class="border-0 f-12">
                        <b class="f-w-600 d-block">Payment Method</b>
                        <p class="m-0 f-12">
                            <b class="f-w-600">Payment: </b>
                            <b>Lorem Ipsum is simply dummy text of</b>
                        </p>
                        <p class="m-0 f-12">
                            <b class="f-w-600">Paypal: </b>
                            <b>Lorem Ipsum is simply dummy text of</b>
                        </p>
                    </td>
                    <td class="f-12" colspan="" align="left" style="border-top:1px solid #000000; border-bottom: 0; vertical-align: top;">
                        <b class="f-w-600">Grand Total</b>
                    </td>
                    <td class="f-12" colspan="2" align="right" style="border-top:1px solid #000000; border-bottom: 0; vertical-align: top;">
                        <b class="f-w-600">$670.00</b>
                    </td>
                </tr>
                <tr>
                    <td class="f-12 border-0 pt-3" colspan="2" align="">
                        <b class="f-w-600">Contact</b>
                        <p class="m-0 f-12">
                            812, Block 5, MH2 BUILDING, HYD,500009
                        </p>
                        <b class="f-w-600">xyz@gmail.com</b>
                    </td>
                    <td class="f-12 border-0" colspan="2" align="">
                        <b class="d-block" style="font-family: cursive; font-style: oblique; font-size: 14px;">Mark Willams</b>
                        <b class="f-w-600">Mark Willams</b>
                        <p class="m-0 f-12">
                            Manager
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="f-12 pt-3 pb-3 border-0" colspan="4" align="">
                        <b class="f-w-600">Term & Condition</b>
                        <b class="d-block">Lorem Ipsum is simply dummy text of</b>
                    </td>
                </tr>
            </table>
        
    </div> 
    <div class="card-body">
        <button type="button" class="btn btn-primary font-weight-bolder mr-3 mr-sm-14 my-1" onclick="printDiv('printableArea')" >Print Payslip</button>
    </div>
</div> 

<!-- [ style Customizer ] start -->
