<?php $_SESSION['apply_js']['multiselest'] = '1';
$_REQUEST['account_code'] =$_POST['account_code'] !=""?$_POST['account_code']:$_REQUEST['account_code'];
$_REQUEST['from_date'] = $_POST['from_date'] !=""?$_POST['from_date']:$_REQUEST['from_date'];
$_REQUEST['to_date'] = $_POST['to_date'] !=""?$_POST['to_date']:$_REQUEST['to_date'];
 ?>
<div class="card">
    <div class="card-body ">
        <div class="row">
            <div class="col-lg-11">
                <ul class="nav md-tabs infisuite_tab mb-2">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 <?php echo ($_GET['GLT'] =="" || $_GET['GLT'] =="GL")?"active":""; ?>" href="index.php?option=COM_VIEW_GENERAL_LEDGER" >General ledger</a>
                    </li>
                    <li class="nav-item float-right">
                        <a class="nav-link f-w-600 f-12 <?php echo ($_GET['GLT'] =="BV")?"active":""; ?>"  href="index.php?option=COM_VIEW_GENERAL_LEDGER&GLT=BV" >Bank ledger</a>
                    </li>
                    <li class="nav-item float-right">
                        <a class="nav-link f-w-600 f-12 <?php echo ($_GET['GLT'] =="CV")?"active":""; ?>"  href="index.php?option=COM_VIEW_GENERAL_LEDGER&GLT=CV" >Cash ledger</a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-1 text-right">
                <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
            </div>
        </div>
    </div>
</div>
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
    function selectParentAccount(accountno,id){
        $.ajax({
            url: "ajax.php",
            data:'page=getchildchartofaccount&accountno='+accountno,
            type: "POST",
            success:function(data){
                console.log(data);
                var res = JSON.parse(data);
                   if(res.result_cnt != "0"){
                       $("."+id).show();
                       $("#"+id).html(res.option);
                   }else{
                       $("."+id).hide();
                       $("#"+id).html();
                   }
            },
            error:function (){}
        });
    }
    function printDiv(divId) {
           var printContents = document.getElementById(divId).innerHTML;
           var originalContents = document.body.innerHTML;
           document.body.innerHTML = "<html lang=\"en\"><head><title>General Ledger Report</title></head><body>" + printContents + "</body>";
           window.print();
           document.body.innerHTML = originalContents;
        }
    function convertHTMLToPDF() {
            var  title ="ledger_account_"+$("#account_code").val();
            var htmlContent = $('#printableArea').html();
            $.ajax({
                url: 'convert_html_to_pdf.php',
                type: 'POST',
                data: { html_content: htmlContent,title:title },
                success: function(pdfdetail) {
                    var result = JSON.parse(pdfdetail);
                    var link = document.createElement('a');
                    link.href = result.pdf_url;
                    link.download =result.pdf_name;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $.ajax({
                        url: 'convert_html_to_pdf.php',
                        type: 'POST',
                        data: 'removepdf='+result.pdf_name,
                        success: function(res) {
                            console.log(res);
                        }
                    });
                }
            });
        }
</script>
<?php if($_GET['GLT'] =="" || $_GET['GLT'] =="GL"){

$getapi = API."?mode=getgeneralledger&account_code=".$_REQUEST['account_code']."&first_account_code=".$_POST['first_account_code']."&second_account_code=".$_POST['second_account_code']."&third_account_code=".$_POST['third_account_code']."&fourth_account_code=".$_POST['fourth_account_code']."&from_date=".$_REQUEST['from_date']."&to_date=".$_REQUEST['to_date'];
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
$chart_of_accounts = $result['chart_of_accounts'];
foreach($chart_of_accounts as $cocc){
    $cocaccount[$cocc['account_code']] = $cocc;
}
$last_account_balances = $result['last_account_balances'];
$general_ledger = $result['general_ledger'];
?>
<script type="text/javascript">
    function viewJournalVoucher(rid){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#dynamic_popup11").html(loader);
         $('#kt_modal_dynamic_popup2').modal("show");

        jQuery.ajax({
            url: "views/custom_view_journal_vouchers_popup.php",
            data: "rid="+rid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);

            }
        });
    }
</script>
<div class="card">
    <div class="card-header">
        <h5>General ledger</h5>
        <div class="card-header-right table-card-header">
        </div>
    </div>
    <div class="card-body p-l-50 p-r-50">
        <form method="post">
        <div class="row">
            <div class="col-lg-4">
                <span  class="form-label">Account Number</span>
                <select aria-label="Account Code" class="form-select js-example-basic-single" name="account_code"   id="account_code">
                 <option value="">Select Any One</option>
                <?php foreach($chart_of_accounts as $coa) {  ?>
                   <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$_REQUEST['account_code']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4">
                         <span  class="form-label">Date Range</span>
                         <?php $shortcode = $_REQUEST['shortcode_date'] !=""?$_REQUEST['shortcode_date']:"TM";
                         $from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-m-01"));
                         $to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-m-t")); ?>
                        <input type="hidden" name="pick_date" id="pick_date" value="Today">
                        <input type="hidden" name="shortcode_date" id="shortcode_date" value="TD">
                        <div class="btn-group dropdown-split-primary" style="width: 100%;">
                            <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                                <span id="btn_date">This Month</span>
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
<?php if($_REQUEST['account_code'] != ""){
$closingbal1 = $last_account_balances['closing_balance'];
$debitallamount = $creditallamount = 0;
foreach($general_ledger as $gl){
    $amountChange1= 0;
    $debit_amount11 = ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?$gl['debit_amount']:0;
    $credit_amount11 = ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?$gl['credit_amount']:0;
    if(in_array($gl['account_type'], array('1', '4'))){
        $amountChange1 = $debit_amount11 - $credit_amount11;
    }else{
        $amountChange1 = $credit_amount11 - $debit_amount11;
    }
    $debitallamount +=  $debit_amount11;
    $creditallamount +=  $credit_amount11;
    $closingbal1 = $closingbal1 + $amountChange1;
}
$debitcreditamount =$debitallamount -$creditallamount;
        ?>
        <div class="mt-5" id="printableArea">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <th scope="col" colspan="7" style="font-size:18px;font-family:'Tahoma';padding:15px;text-align:center;">General Ledger</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma'; width: 26%;font-weight:normal;padding:5px;text-align:left;">Account Number</th>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma'; width: 27%;font-weight:normal;padding:5px;text-align:left;"><?php echo $_POST['account_code'] !=""?$cocaccount[$_POST['account_code']]['account_name']." (".$cocaccount[$_POST['account_code']]['account_code'].")":"-"?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma'; width: 16%;font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;text-align:center;">Opening Balance</th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma'; width: 16%;font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;text-align:center;">Transactions</th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma'; width: 15%;font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;text-align:center;">Closing Balance</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-align:left;">Date Range</th>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-align:left;">
                        <?php echo fromDate($from_date)." To ".fromDate($to_date); ?>
                    </th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee;text-align: right; <?php echo ($last_account_balances['closing_balance'] < 0)?"color:#f00":""; ?>"><?php echo accountingAmontFormat($last_account_balances['closing_balance'],0); ?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee;text-align: right; <?php echo ($debitcreditamount < 0)?"color:#f00":""; ?>" ><?php echo accountingAmontFormat($debitcreditamount,0); ?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee;text-align: right; <?php echo ($closingbal1 < 0)?"color:#f00":""; ?>"><?php echo accountingAmontFormat($closingbal1,0); ?></th>
                </tr>
                <tr>
                    <td colspan="7">
                        <table style="width:100%;border-collapse: collapse;">
                            <tr>&nbsp</tr>
                            <tr>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; text-align: left;">Date</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Account Code/Name</th>

                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Post Reference</th>
                                <th scope="col" style="background-color: #eeeeee; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Debit</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Credit</th>
                                <th scope="col" style="background-color: #eeeeee; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Balance</th>
                            </tr>
                            <tr>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($last_account_balances['balance_date']); ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">Opening Balance
                                    <span style="font-size:14px; font-family:'Tahoma'; font-weight:500;display:block;">-</span>
                                </td>


                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">-</td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;color:#f00; text-align: right;">
                                    -
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                                    -
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($last_account_balances['closing_balance'] < 0) ? "color:#f00" : ""; ?>"><?php echo $last_account_balances['closing_balance']!=""?accountingAmontFormat($last_account_balances['closing_balance']):"-"; ?></td>
                            </tr>
                            <?php
                            $closingbal = $last_account_balances['closing_balance'];
                            foreach($general_ledger as $gl){
                                $amountChange= 0;
                                $debitbg = $credit = "";
                                $debit_amount = ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?$gl['debit_amount']:0;
                                $credit_amount = ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?$gl['credit_amount']:0;

                                if(in_array($gl['account_type'], array('1', '4'))){
                                    $amountChange = $debit_amount - $credit_amount;
                                    if($credit_amount !=0 && $credit_amount !=""){
                                        $credit = 'color:#f00';
                                    }
                                }else{
                                    $amountChange = $credit_amount - $debit_amount;
                                    if($debit_amount !=0 && $debit_amount !=""){
                                        $debitbg = 'color:#f00';
                                    }
                                }
                                $closingbal = $closingbal + $amountChange;
                                ?>
                            <tr>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($gl['voucher_date']); ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo $gl['account_code']; ?> - <?php echo $gl['account_name']; ?>
                                    <span style="display: block;"><?php echo $gl['remarks']; ?>
                                </td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; " onclick="viewJournalVoucher('<?php echo $gl['voucher_id']; ?>')" class="text-primary cursor-pointer" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $gl['voucher_ref_no']; ?></td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($gl['debit_amount'] < 0) ? "color:#f00" : ""; ?> <?php //echo $debitbg; ?>" >
                                    <?php echo ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?accountingAmontFormat($gl['debit_amount']):"-"; ?>
                                </td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($gl['credit_amount'] < 0) ? "color:#f00" : ""; ?> <?php //echo $credit; ?>">
                                    <?php echo ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?accountingAmontFormat($gl['credit_amount']):"-"; ?>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($closingbal < 0) ? "color:#f00" : ""; ?>"> <?php echo ($closingbal!="" && $closingbal!="0")?accountingAmontFormat($closingbal):"-"; ?> </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-btn-section clearfix d-print-none" style="margin-top:15px;">
            <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printableArea');">
                <i class="fa fa-print"></i>  Print
            </button>
            <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
                <i class="fa fa-download"></i>  Download
            </button>
        </div>

    <?php }else{ ?>
        <div class="card-px text-center mt-5">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> Please Select Account</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
    </div>
</div>
<?php }elseif($_GET['GLT'] =="BV"){

$getapi = API."?mode=getgeneralledger&account_code=".$_REQUEST['account_code']."&from_date=".$_REQUEST['from_date']."&to_date=".$_REQUEST['to_date']."&GLT=BV";
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
$chart_of_accounts = $result['chart_of_accounts'];
foreach($chart_of_accounts as $cocc){
    $cocaccount[$cocc['account_code']] = $cocc;
}
$last_account_balances = $result['last_account_balances'];
$general_ledger = $result['general_ledger'];
$bank_accounts = $result['bank_accounts'];
$bank_account_list = array();
foreach($bank_accounts as $bacc){
    $bank_account_list[$bacc['account_code']] = $bacc;
}
?>

<div class="card">
    <div class="card-header">
        <h5>Bank General ledger</h5>
        <div class="card-header-right table-card-header">
        </div>
    </div>
    <div class="card-body p-l-50 p-r-50">
        <form method="post">
        <div class="row">
            <div class="col-lg-4">
                <span  class="form-label">Bank Account Number</span>
                <select aria-label="Account Code" class="form-select js-example-basic-single" name="account_code"  >
                 <option value="">All Acounts</option>
                <?php foreach($bank_accounts as $coa) {  ?>
                   <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$_POST['account_code']?"selected":""; ?>><?php echo $coa['bank_account_name']." (".$coa['account_code'].")";?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4">
                         <span  class="form-label">Date Range</span>
                         <?php $shortcode = $_REQUEST['shortcode_date'] !=""?$_REQUEST['shortcode_date']:"TM";
                         $from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-m-01"));
                         $to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-m-t")); ?>
                        <input type="hidden" name="pick_date" id="pick_date_2" value="Today">
                        <input type="hidden" name="shortcode_date" id="shortcode_date_2" value="TD">
                        <div class="btn-group dropdown-split-primary" style="width: 100%;">
                            <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                                <span id="btn_date_2">This Month</span>
                            </button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                                <span class="visually-hidden">Toggle primary</span>
                            </button>
                            <div class="dropdown-menu">
                                   <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TD"?"active":"";?> empty_active" id="TD_2" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TW"?"active":"";?> empty_active" id="TW_2" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week')));?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday ')));?>')">This Week</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TM"?"active":"";?> empty_active" id="TM_2" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TY"?"active":"";?> empty_active" id="TY_2" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="YD"?"active":"";?> empty_active" id="YD_2" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>')">Yesterday</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PW"?"active":"";?> empty_active" id="PW_2" href="#!" onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PM"?"active":"";?> empty_active" id="PM_2" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01",strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t",strtotime('last month'))); ?>')">Previous Month</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PY"?"active":"";?> empty_active" id="PY_2" href="#!" onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01",strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31",strtotime('last year'))); ?>')">Previous Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="CD"?"active":""; ?> empty_active" id="CD_2" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" style="display: none" id="custome_date_2">
                        <div class="row" >
                            <div  class="col-lg-6">
                                <span  class="form-label">Start Date</span>
                                <input aria-label="From Date" type="text" class="form-control filterdynamic datepicker" name="from_date" id="from_date_2" value="<?php echo $from_date; ?>">
                            </div>
                            <div class="col-lg-6">
                                <span  class="form-label">End Date</span>
                                <input aria-label="To Date" type="text" class="form-control filterdynamic datepicker" name="to_date" id="to_date_2" value="<?php echo $to_date; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button class="btn btn-primary mt-4" onclick="filterSubmitForm()" type="submit" >
                                <i class="ti-search m-r-2"></i>
                                View
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php if($_REQUEST['account_code'] != ""){
$closingbal1 = $last_account_balances['closing_balance'];
$debitallamount = $creditallamount = 0;
foreach($general_ledger as $gl){
    $amountChange1= 0;
    $debit_amount11 = ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?$gl['debit_amount']:0;
    $credit_amount11 = ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?$gl['credit_amount']:0;
    if(in_array($gl['account_type'], array('1', '4'))){
        $amountChange1 = $debit_amount11 - $credit_amount11;
    }else{
        $amountChange1 = $credit_amount11 - $debit_amount11;
    }
    $debitallamount +=  $debit_amount11;
    $creditallamount +=  $credit_amount11;
    $closingbal1 = $closingbal1 + $amountChange1;
}
$debitcreditamount =$debitallamount -$creditallamount;
        ?>
        <div class="mt-5" id="printableArea_2">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px; text-align: right;">Account Number</th>
                    <th scope="col" colspan="2" style="text-align: left;"><?php echo $_POST['account_code'] !=""?$bank_account_list[$_POST['account_code']]['bank_account_name']." (".$bank_account_list[$_POST['account_code']]['account_code'].")":"-"?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;">Opening Balance</th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;">Transactions</th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;">Closing Balance</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px; text-align: right;">Date Range</th>
                    <th scope="col" colspan="2" style="text-align: left;">
                         <?php echo fromDate($from_date)." To ".fromDate($to_date); ?>
                    </th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee; text-align: right; <?php echo ($last_account_balances['closing_balance'] < 0) ? "color:#f00" : ""; ?>"><?php echo accountingAmontFormat($last_account_balances['closing_balance'],0); ?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee; text-align: right; <?php echo ($debitcreditamount < 0) ? "color:#f00" : ""; ?>" ><?php echo accountingAmontFormat($debitcreditamount,0); ?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee; text-align: right; <?php echo ($closingbal1 < 0) ? "color:#f00" : ""; ?>"><?php echo accountingAmontFormat($closingbal1,0); ?></th>
                </tr>
                <tr>
                    <td colspan="7">
                        <table style="width:100%;border-collapse: collapse;">
                            <tr>&nbsp</tr>
                            <tr>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; text-align: left;">Date</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Account Code/Name</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Description</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Post Reference</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Debit</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Credit</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Balance</th>
                            </tr>
                            <tr>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($last_account_balances['balance_date']); ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">Opening Balance</td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><span class="d-block">-</span>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">-</td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;color:#f00; text-align: right;">
                                    -
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                                    -
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($last_account_balances['closing_balance'] < 0) ? "color:#f00" : ""; ?>"><?php echo $last_account_balances['closing_balance']!=""?accountingAmontFormat($last_account_balances['closing_balance']):"-"; ?></td>
                            </tr>
                            <?php
                            $closingbal = $last_account_balances['closing_balance'];
                            foreach($general_ledger as $gl){
                                $amountChange= 0;
                                $debitbg = $credit = "";
                                $debit_amount = ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?$gl['debit_amount']:0;
                                $credit_amount = ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?$gl['credit_amount']:0;

                                if(in_array($gl['account_type'], array('1', '4'))){
                                    $amountChange = $debit_amount - $credit_amount;
                                    if($credit_amount !=0 && $credit_amount !=""){
                                        $credit = 'color:#f00';
                                    }
                                }else{
                                    $amountChange = $credit_amount - $debit_amount;
                                    if($debit_amount !=0 && $debit_amount !=""){
                                        $debitbg = 'color:#f00';
                                    }
                                }
                                $closingbal = $closingbal + $amountChange;
                                ?>
                            <tr>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($gl['voucher_date']); ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo $gl['account_code']; ?> - <?php echo $gl['account_name']; ?>        </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><span class="d-block"><?php echo $gl['remarks']; ?></span>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; " onclick="viewJournalVoucher('<?php echo $gl['voucher_id']; ?>')" class="text-primary cursor-pointer" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $gl['voucher_ref_no']; ?></td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($gl['debit_amount'] < 0) ? "color:#f00" : ""; ?> <?php //echo $debitbg; ?>" >
                                    <?php echo ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?accountingAmontFormat($gl['debit_amount']):"-"; ?>
                                </td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($gl['credit_amount'] < 0) ? "color:#f00" : ""; ?> <?php //echo $credit; ?>">
                                    <?php echo ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?accountingAmontFormat($gl['credit_amount']):"-"; ?>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($closingbal < 0) ? "color:#f00" : ""; ?>"> <?php echo ($closingbal!="" && $closingbal!="0")?accountingAmontFormat($closingbal):"-"; ?> </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-btn-section clearfix d-print-none" style="margin-top:15px;">
            <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printableArea');">
                <i class="fa fa-print"></i>  Print
            </button>
            <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
                <i class="fa fa-download"></i>  Download
            </button>
        </div>

    <?php }else{ ?>
        <div class="card-px text-center mt-5">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> Please Select Account</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
    </div>
</div>
<?php }elseif($_GET['GLT'] =="CV"){

$getapi = API."?mode=getgeneralledger&account_code=".$_REQUEST['account_code']."&from_date=".$_REQUEST['from_date']."&to_date=".$_REQUEST['to_date']."&GLT=CV";
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
$chart_of_accounts = $result['chart_of_accounts'];
foreach($chart_of_accounts as $cocc){
    $cocaccount[$cocc['account_code']] = $cocc;
}
$last_account_balances = $result['last_account_balances'];
$general_ledger = $result['general_ledger'];
$petty_cash_boxes = $result['petty_cash_boxes'];
$petty_cash_boxes_list = array();
foreach($petty_cash_boxes as $bacc){
    $petty_cash_boxes_list[$bacc['gl_account_pc_cash']] = $bacc;
}

?>
<div class="card">
    <div class="card-header">
        <h5>Cash General ledger</h5>
        <div class="card-header-right table-card-header">
        </div>
    </div>
    <div class="card-body p-l-50 p-r-50">
        <form method="post">
        <div class="row">
            <div class="col-lg-4">
                <span  class="form-label">Cash Account Number</span>
                <select aria-label="Account Code" class="form-select js-example-basic-single" name="account_code"  >
                 <option value="">All Acounts</option>
                <?php foreach($petty_cash_boxes as $coa) {  ?>
                   <option value="<?php echo $coa['gl_account_pc_cash']; ?>" <?php echo $coa['gl_account_pc_cash'] ==$_POST['account_code']?"selected":""; ?>><?php echo $coa['pc_box_description']." (".$coa['gl_account_pc_cash'].")";?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4">
                         <span  class="form-label">Date Range</span>
                         <?php $shortcode = $_REQUEST['shortcode_date'] !=""?$_REQUEST['shortcode_date']:"TM";
                         $from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-m-01"));
                         $to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-m-t")); ?>
                        <input type="hidden" name="pick_date" id="pick_date_3" value="Today">
                        <input type="hidden" name="shortcode_date" id="shortcode_date_3" value="TD">
                        <div class="btn-group dropdown-split-primary" style="width: 100%;">
                            <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                                <span id="btn_date_3">This Month</span>
                            </button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                                <span class="visually-hidden">Toggle primary</span>
                            </button>
                            <div class="dropdown-menu">
                                   <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TD"?"active":"";?> empty_active" id="TD_3" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TW"?"active":"";?> empty_active" id="TW_3" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week')));?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday ')));?>')">This Week</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TM"?"active":"";?> empty_active" id="TM_3" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TY"?"active":"";?> empty_active" id="TY_3" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="YD"?"active":"";?> empty_active" id="YD_3" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>')">Yesterday</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PW"?"active":"";?> empty_active" id="PW_3" href="#!" onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PM"?"active":"";?> empty_active" id="PM_3" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01",strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t",strtotime('last month'))); ?>')">Previous Month</a>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PY"?"active":"";?> empty_active" id="PY_3" href="#!" onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01",strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31",strtotime('last year'))); ?>')">Previous Year</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="CD"?"active":""; ?> empty_active" id="CD_3" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" style="display: none" id="custome_date_3">
                        <div class="row" >
                            <div  class="col-lg-6">
                                <span  class="form-label">Start Date</span>
                                <input aria-label="From Date" type="text" class="form-control filterdynamic datepicker" name="from_date" id="from_date_3" value="<?php echo $from_date; ?>">
                            </div>
                            <div class="col-lg-6">
                                <span  class="form-label">End Date</span>
                                <input aria-label="To Date" type="text" class="form-control filterdynamic datepicker" name="to_date" id="to_date_3" value="<?php echo $to_date; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button class="btn btn-primary mt-4" onclick="filterSubmitForm()" type="submit" >
                                <i class="ti-search m-r-2"></i>
                                View
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php if($_REQUEST['account_code'] != ""){
$closingbal1 = $last_account_balances['closing_balance'];
$debitallamount = $creditallamount = 0;
foreach($general_ledger as $gl){
    $amountChange1= 0;
    $debit_amount11 = ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?$gl['debit_amount']:0;
    $credit_amount11 = ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?$gl['credit_amount']:0;
    if(in_array($gl['account_type'], array('1', '4'))){
        $amountChange1 = $debit_amount11 - $credit_amount11;
    }else{
        $amountChange1 = $credit_amount11 - $debit_amount11;
    }
    $debitallamount +=  $debit_amount11;
    $creditallamount +=  $credit_amount11;
    $closingbal1 = $closingbal1 + $amountChange1;
}
$debitcreditamount =$debitallamount -$creditallamount;
        ?>
        <div class="mt-5" id="printableArea_3">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px; text-align: right;">Account Number</th>
                    <th scope="col" colspan="2" style="text-align: left;"><?php echo $_POST['account_code'] !=""?$petty_cash_boxes_list[$_POST['account_code']]['pc_box_description']." (".$petty_cash_boxes_list[$_POST['account_code']]['gl_account_pc_cash'].")":"-"?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;">Opening Balance</th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;">Transactions</th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px;text-transform:uppercase;border:1px solid #eeeeee;">Closing Balance</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:normal;padding:5px; text-align: right;">Date Range</th>
                    <th scope="col" colspan="2" style="text-align: left;">
                         <?php echo fromDate($from_date)." To ".fromDate($to_date); ?>
                    </th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee; text-align: right; <?php echo ($last_account_balances['closing_balance'] < 0) ? "color:#f00" : ""; ?>"><?php echo accountingAmontFormat($last_account_balances['closing_balance'],0); ?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee; text-align: right; <?php echo ($debitcreditamount < 0) ? "color:#f00" : ""; ?>" ><?php echo accountingAmontFormat($debitcreditamount,0); ?></th>
                    <th scope="col" style="background-color: #cccccc; font-size:14px;font-family:'Tahoma';padding:5px;font-weight:normal;text-transform:uppercase;border:1px solid #eeeeee; text-align: right; <?php echo ($closingbal1 < 0) ? "color:#f00" : ""; ?>"><?php echo accountingAmontFormat($closingbal1,0); ?></th>
                </tr>
                <tr>
                    <td colspan="7">
                        <table style="width:100%;border-collapse: collapse;">
                            <tr>&nbsp</tr>
                            <tr>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; text-align: left;">Date</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Account Code/Name</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Description</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Post Reference</th>
                                <th scope="col" style="background-color: #eeeeee; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Debit</th>
                                <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Credit</th>
                                <th scope="col" style="background-color: #eeeeee; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Balance</th>
                            </tr>
                            <tr>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($last_account_balances['balance_date']); ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">Opening Balance</td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><span class="d-block">-</span>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;">-</td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;color:#f00; text-align: right;">
                                    -
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;">
                                    -
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($last_account_balances['closing_balance'] < 0) ? "color:#f00" : ""; ?>"><?php echo $last_account_balances['closing_balance']!=""?accountingAmontFormat($last_account_balances['closing_balance']):"-"; ?></td>
                            </tr>
                            <?php
                            $closingbal = $last_account_balances['closing_balance'];
                            foreach($general_ledger as $gl){
                                $amountChange= 0;
                                $debitbg = $credit = "";
                                $debit_amount = ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?$gl['debit_amount']:0;
                                $credit_amount = ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?$gl['credit_amount']:0;

                                if(in_array($gl['account_type'], array('1', '4'))){
                                    $amountChange = $debit_amount - $credit_amount;
                                    if($credit_amount !=0 && $credit_amount !=""){
                                        $credit = 'color:#f00';
                                    }
                                }else{
                                    $amountChange = $credit_amount - $debit_amount;
                                    if($debit_amount !=0 && $debit_amount !=""){
                                        $debitbg = 'color:#f00';
                                    }
                                }
                                $closingbal = $closingbal + $amountChange;
                                ?>
                            <tr>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo fromDate($gl['voucher_date']); ?></td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo $gl['account_code']; ?> - <?php echo $gl['account_name']; ?>        </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><span class="d-block"><?php echo $gl['remarks']; ?></span>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; " onclick="viewJournalVoucher('<?php echo $gl['voucher_id']; ?>')" class="text-primary cursor-pointer" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $gl['voucher_ref_no']; ?></td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($gl['debit_amount'] < 0) ? "color:#f00" : ""; ?> <?php //echo $debitbg; ?>" >
                                    <?php echo ($gl['debit_amount']!="" && $gl['debit_amount']!="0")?accountingAmontFormat($gl['debit_amount']):"-"; ?>
                                </td>

                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($gl['credit_amount'] < 0) ? "color:#f00" : ""; ?> <?php //echo $credit; ?>">
                                    <?php echo ($gl['credit_amount']!="" && $gl['credit_amount']!="0")?accountingAmontFormat($gl['credit_amount']):"-"; ?>
                                </td>
                                <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right; <?php echo ($closingbal < 0) ? "color:#f00" : ""; ?>"> <?php echo ($closingbal!="" && $closingbal!="0")?accountingAmontFormat($closingbal):"-"; ?> </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-btn-section clearfix d-print-none" style="margin-top:15px;">
            <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printableArea');">
                <i class="fa fa-print"></i>  Print
            </button>
            <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
                <i class="fa fa-download"></i>  Download
            </button>
        </div>

    <?php }else{ ?>
        <div class="card-px text-center mt-5">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> Please Select Account</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
    </div>
</div>
<?php } ?>
