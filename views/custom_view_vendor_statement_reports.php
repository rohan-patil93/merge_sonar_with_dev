<?php
$_SESSION['apply_js']['multiselest'] = '1';
$shortcode = $_REQUEST['shortcode_date'] !=""?$_REQUEST['shortcode_date']:"TY";
$from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-01-01"));
$to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-12-31"));
$getapi = API."?mode=getvendorstatements&customer_id=".$_POST['customer_id']."&from_date=".$from_date."&to_date=".$to_date;
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
$sales_invoices = $result['sales_invoices'];
$last_account_balances = $result['last_account_balances'];
$customers = $result['customers'];
$transactionwithdates = $result['transactionwithdates'];
ksort($transactionwithdates);
$debitside = array('supplier_bills','debit_notes');
$creditside = array('credit_notes','receipts');

$credit_notes_amt = $result['credit_notes_amt'];
$debit_notes_amt = $result['debit_notes_amt'];
$receipts_amt = $result['receipts_amt'];

$closingbal = $last_account_balances['closing_balance'];
$transaction_amt = 0;
foreach($transactionwithdates as $keydate=>$transction){
    foreach($transction as $pi){
        if(in_array($pi['transaction_table'], $creditside)){
            $closingbal = $closingbal + $pi['p_total_amount'];
            $transaction_amt = $transaction_amt + $pi['p_total_amount'];
        }else{
            $closingbal = $closingbal - $pi['p_total_amount'];
            $transaction_amt = $transaction_amt - $pi['p_total_amount'];
        }
    }
}

 ?>
<script type="text/javascript">
    function dynamicSelectDateRange(type,name,start="",end=""){
        $("#btn_date").text(name);
        $("#pick_date").val(name);
        $("#shortcode_date").val(type);
        $(".empty_active").removeClass("active");
        $("#"+type).addClass("active");
        if(name == "Custom Date"){
            $("#custome_date").show('slow');

        }else{
            $("#custome_date").hide();
            $("#from_date").val(start);
            $("#to_date").val(end);
        }
    }
</script>
<div class="card">
    <div class="card-header">
        <h5>Vendor Statement</h5>
        <div class="card-header-right table-card-header">
        </div>
    </div>
    <div class="card-body p-l-50 p-r-50">
        <form method="post">
            <div class="row">
                <div class="col-lg-4">
                    <span  class="form-label">Vendor</span>
                    <select aria-label="Customer" class="form-select js-example-basic-single" name="customer_id"   id="customer_id" required="">
                     <option value="">Select Any One</option>
                    <?php foreach($customers as $cust) {  ?>
                       <option value="<?php echo $cust['id']; ?>" <?php echo $cust['id'] ==$_POST['customer_id']?"selected":""; ?>><?php echo $cust['company']." (".$cust['display_name'].")";?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4">
                             <span  class="form-label">Date Range</span>
                             <input type="hidden" name="pick_date" id="pick_date" value="This Year">
                            <input type="hidden" name="shortcode_date" id="shortcode_date" value="TY">
                            <div class="btn-group dropdown-split-primary" style="width: 100%;">
                                <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                                    <span id="btn_date">This Year</span>
                                </button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                                    <span class="visually-hidden">Toggle primary</span>
                                </button>
                                <div class="dropdown-menu">
                                       <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TD"?"active":"";?> empty_active" id="TD" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TW"?"active":"";?> empty_active" id="TW" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week')));?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday ')));?>')">This Week</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TM"?"active":"";?> empty_active" id="TM" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TY"?"active":"";?> empty_active" id="TY" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="YD"?"active":"";?> empty_active" id="YD" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>')">Yesterday</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PW"?"active":"";?> empty_active" id="PW" href="#!" onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PM"?"active":"";?> empty_active" id="PM" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01",strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t",strtotime('last month'))); ?>')">Previous Month</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PY"?"active":"";?> empty_active" id="PY" href="#!" onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01",strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31",strtotime('last year'))); ?>')">Previous Year</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="CD"?"active":""; ?> empty_active" id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6" style="display: none" id="custome_date">
                            <div class="row" >
                                <div  class="col-lg-6">
                                    <span  class="form-label">Start Date</span>
                                    <input aria-label="From Date" type="text" class="form-control filterdynamic datepicker" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
                                </div>
                                <div class="col-lg-6">
                                    <span  class="form-label">End Date</span>
                                    <input aria-label="To Date" type="text" class="form-control filterdynamic datepicker" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <span class="form-label d-block">&nbsp</span>
                            <button class="btn btn-primary form-control" onclick="filterSubmitForm()" type="submit" >
                                <i class="ti-search m-r-2"></i>View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-responsive">
        <?php if($_POST['customer_id'] != ""){     ?>
        <table role="presentation" style="width:100%;border-collapse:collapse;" class="mt-5">
        <tr>
            <td style="font-size:24px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">Customer Statement <?php echo date('M Y',strtotime($from_date)); ?> to <?php echo date('M Y',strtotime($to_date)); ?></td>
            <td style="font-size:24px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo $customers[$_POST['vendor_id']]['company']." (".$customers[$_POST['customer_id']]['display_name'].")"; ?></td>
        </tr>
        <tr>
            <td colspan="1" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">Date: <?php echo fromDate(); ?></td>
            <td colspan="2"></td>
            <td class="f-w-600 text-dark">Opening Balance </td>
            <td ><?php echo accountingAmontFormat($last_account_balances['closing_balance'],0); ?></td>
            <td class="f-w-600 text-dark">Transaction</td>
            <td ><?php echo accountingAmontFormat($transaction_amt); ?></td>
            <td class="f-w-600 text-dark">Closing Balance</td>
            <td ><?php echo accountingAmontFormat($closingbal); ?></td>
        </tr>

        <tr>
            <td colspan="1" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">Time:  <?php echo date('H:i'); ?></td>
            <td colspan="10"></td>

        </tr>

        <tr>
            <td colspan="11">
                <table style="width:100%;border-collapse: collapse;">
                    <tr>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding:5px; text-align: left;">Trans. No</th>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding: 5px; text-align: left;">Date</th>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding:5px; text-align: left;">Name</th>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding:5px; text-align: right;">Ref Number</th>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding:5px; text-align: right;">Ref Date</th>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding:5px; text-align: right;">Debit</th>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding:5px; text-align: right;">Credit</th>
                        <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:500;border:1px solid #eeeeee;padding:5px; text-align: right;">Balance</th>

                    </tr>
                    <tr>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">Opening Balance
                        </td>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($last_account_balances['balance_date']); ?></td>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">-</td>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;color:#f00; text-align: right;">
                            -
                        </td>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                            -
                        </td>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                            -
                        </td>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                            -
                        </td>
                        <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($last_account_balances['closing_balance'] < 0) ? "color:#f00" : ""; ?>"><?php echo $last_account_balances['closing_balance']!=""?accountingAmontFormat($last_account_balances['closing_balance']):"-"; ?></td>

                    </tr>

                    <?php
                    $closingbal = $last_account_balances['closing_balance'];
                    foreach($transactionwithdates as $keydate=>$transction){ ?>
                            <?php foreach($transction as $pi){
                                if(in_array($pi['transaction_table'], $creditside)){
                                    $closingbal = $closingbal - $pi['p_total_amount'];
                                }else{
                                    $closingbal = $closingbal + $pi['p_total_amount'];
                                }
                                ?>
                            <tr>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo $pi['transaction_number']." - ".$pi['transaction_table']; ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($pi['transaction_date']); ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo $pi['party_name']; ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: left;"><?php echo $pi['ref_no']; ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: left;"><?php echo fromDate($pi['ref_date']); ?></td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                                    <?php if(in_array($pi['transaction_table'], $creditside)){ echo $pi['p_total_amount'];
}else{ echo "-";
} ?>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                                    <?php if(in_array($pi['transaction_table'], $debitside)){ echo $pi['p_total_amount'];
}else{ echo "-";
} ?>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($closingbal < 0) ? "color:#f00" : ""; ?>"> <?php echo ($closingbal!="" && $closingbal!="0")?accountingAmontFormat($closingbal):"-"; ?> </td>
                            </tr>
                            <?php } ?>
                    <?php } ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>

<!-- Agging -->

<?php
    $dretrundataind = array( '15'=>0, '30'=>0, '45'=>0, '60'=>0, '90'=>0);
    $retrundataind = $retrundata  = $returnvendorind = $vendortotal = $allids = $billdays = $vendordaytotal = $vendordayleveltotal = $returnvendorind = array();
    foreach($sales_invoices as $data){
        $paymentamount = $receipts_amt[$data['id']];
        $payment_term_history = json_decode($data['payment_term_history'],1);
        if(count((array)$payment_term_history) != "0"){
            $pth_cnt = count((array)$payment_term_history);
            foreach($payment_term_history as $key=>$pterh){

                if($paymentamount > 0){
                    if($paymentamount >= $pterh['amount']){
                        $paymentamount = $paymentamount-$pterh['amount'];
                        $pterh['amount'] = 0;
                    }else{
                        $pterh['amount'] = $pterh['amount'] - $paymentamount;
                    }
                }
                $todaydate = date('Y-m-d');
                $sdate = new DateTime($pterh['due_date']);
                $edate = new DateTime($todaydate);
                $totald= $sdate->diff($edate);
                $totaldays = $totald->days;
                if($totaldays < 15){
                    $payment_terms = 15;
                }elseif($totaldays < 30){
                    $payment_terms = 30;
                }elseif($totaldays < 45){
                    $payment_terms = 45;
                }elseif($totaldays < 60){
                    $payment_terms = 60;
                }elseif($totaldays < 90){
                   $payment_terms = 90;
                }else{
                   $payment_terms = 120;
                }
                 $total =  $total + $pterh['amount'];
                   $billdays[$payment_terms] += $pterh['amount'];
            }
        }
    }
    $_SESSION['download_data'] = array();
?>
<?php /**/ ?>
 <div class="card-header">
      <h5 class="f-30">Aging Report</h5>
      <div class="card-header-right">
     </div>
   </div>

    <table class=" table table-hover m-b-0">
          <thead>
            <th scope="col"> Invoice </th>
            <th scope="col" class="text-right">Total </th>
            <th scope="col" class="text-right">15 Days</th>
            <th scope="col" class="text-right">30 Days</th>
            <th scope="col" class="text-right">45 Days</th>
            <th scope="col" class="text-right">60 Days</th>
            <th scope="col" class="text-right">90 Days</th>
            <th scope="col" class="text-right"> > 90 Days</th>
          </thead>
          <tbody>
            <td>Sales invoice</td>
            <td class="text-right"> <span class="cursor-pointer"  ><?php echo customeummarmonyformate($total); ?> </span> </td>
            <td class="text-right"> <span class="cursor-pointer" > <?php echo customeummarmonyformate($billdays['15']);  ?> </span>
            </td>
            <td class="text-right"> <span class="cursor-pointer" > <?php echo customeummarmonyformate($billdays['30']); ?> </span> </td>
            <td class="text-right"> <span class="cursor-pointer" > <?php echo customeummarmonyformate($billdays['45']); ?> </span> </td>
            <td class="text-right"> <span class="cursor-pointer" > <?php echo customeummarmonyformate($billdays['60']); ?> </span></td>
            <td class="text-right"> <span class="cursor-pointer" > <?php echo customeummarmonyformate($billdays['90']); ?> </span> </td>
            <td class="text-right"> <span class="cursor-pointer" > <?php echo customeummarmonyformate($billdays['120']); ?></span></td>
          </tbody>
    </table>

<?php }else{ ?>
        <div class="card-px text-center mt-5">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> Please Select Vendor</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>

    </div>
</div>
