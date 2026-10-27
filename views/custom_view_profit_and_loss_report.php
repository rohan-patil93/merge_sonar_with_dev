<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
  $_POST['start_date'] = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
  $_POST['end_date'] = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
} else {
  $_POST['start_date'] = date('Y-m-01');
  $_POST['end_date'] = date('Y-m-t');
}

$prev_start_date = date('Y-m-d', strtotime('-1 year', strtotime($_POST['start_date'])));
$prev_end_date = date('Y-m-d', strtotime('-1 year', strtotime($_POST['end_date'])));

$parent_account_code = $_POST['account_code'] !=""?$_POST['account_code']:0;
$balance_date = $_POST['balance_date'];
$getapi = API."?mode=getprofitandlossreports&start_date=".$_POST['start_date']."&end_date=".$_POST['end_date']."&prev_start_date=".$prev_start_date."&prev_end_date=".$prev_end_date;
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);

//Start bAlances
$start_balances = $result['start_balances'];
$parentcoa = $parentchildacount = array();
foreach($start_balances as $coa){
    if($coa['parent_account_code'] == 0){
        $parentcoa[] = $coa;
    }
    $parentchildacount[$coa['parent_account_code']][] = $coa;
}
$parentchildaccount = $totalcoa = $parentlist =  array();
foreach($parentcoa as $pcoa1){
    $parentchildaccount[$pcoa1['account_code']][] = $pcoa1;
    $totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa1['closing_balance'];
    foreach($parentchildacount[$pcoa1['account_code']] as $pcoa2){
        $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;
        $totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa2['closing_balance'];
        $totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa2['closing_balance'];
        foreach($parentchildacount[$pcoa2['account_code']] as $pcoa3){
            $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;
            $totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];
            $totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];
            $totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa3['closing_balance'];
            foreach($parentchildacount[$pcoa3['account_code']] as $pcoa4){
                $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;

                $totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa4['closing_balance'];
                foreach($parentchildacount[$pcoa4['account_code']] as $pcoa5){
                    $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;

                    $totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa5['account_code']]['start_balance']  +=  $pcoa5['closing_balance'];
                    foreach($parentchildacount[$pcoa5['account_code']] as $pcoa6){
                        $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
                        $totalcoa[$pcoa1['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa2['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa3['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa4['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa5['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa6['account_code']]['start_balance']  +=  $pcoa6['closing_balance'];
                    }
                }
            }
        }
    }
}

//End bAlances
$end_balances = $result['end_balances'];
$endparentcoa = $endparentchildacount = array();
foreach($end_balances as $coa){
    if($coa['parent_account_code'] == 0){
        $endparentcoa[] = $coa;
    }
    $endparentchildacount[$coa['parent_account_code']][] = $coa;
}
$endparentchildaccount =  $parentlist =  array();
foreach($endparentcoa as $pcoa1){
    $endparentchildaccount[$pcoa1['account_code']][] = $pcoa1;
    $totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa1['closing_balance'];
    foreach($endparentchildacount[$pcoa1['account_code']] as $pcoa2){
        $endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;
        $totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa2['closing_balance'];
        $totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa2['closing_balance'];
        foreach($endparentchildacount[$pcoa2['account_code']] as $pcoa3){
            $endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;
            $totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa3['closing_balance'];
            $totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa3['closing_balance'];
            $totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa3['closing_balance'];
            foreach($endparentchildacount[$pcoa3['account_code']] as $pcoa4){
                $endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;

                $totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa4['account_code']]['end_balance']  +=  $pcoa4['closing_balance'];
                foreach($endparentchildacount[$pcoa4['account_code']] as $pcoa5){
                    $endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;

                    $totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa4['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa5['account_code']]['end_balance']  +=  $pcoa5['closing_balance'];
                    foreach($endparentchildacount[$pcoa5['account_code']] as $pcoa6){
                        $endparentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
                        $totalcoa[$pcoa1['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa2['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa3['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa4['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa5['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa6['account_code']]['end_balance']  +=  $pcoa6['closing_balance'];
                    }
                }
            }
        }
    }
}

$prev_revenue_from_operations = accountingAmontFormat($totalcoa['50002700']['prev_end_balance']-$totalcoa['50002700']['prev_start_balance']);
$prev_otherincome = accountingAmontFormat($totalcoa['40002000']['prev_end_balance']-$totalcoa['40002000']['prev_start_balance']);
$prev_Costofmaterialsconsumed = accountingAmontFormat($totalcoa['50000100']['prev_end_balance']-$totalcoa['50000100']['prev_start_balance']);
$prev_employeebenefitsexpense = accountingAmontFormat($totalcoa['50002100']['prev_end_balance']-$totalcoa['50002100']['prev_start_balance']);
$prev_financecosts  = accountingAmontFormat($totalcoa['50003500']['prev_end_balance']-$totalcoa['50003500']['prev_start_balance']);
$prev_otheroperatingexpenses = accountingAmontFormat($totalcoa['50002700']['prev_end_balance']-$totalcoa['50002700']['prev_start_balance'])+($totalcoa['50002750']['prev_end_balance']-$totalcoa['50002750']['prev_start_balance']);

$prev_depreciationandamortisation  = accountingAmontFormat($totalcoa['50003600']['prev_end_balance']-$totalcoa['50003600']['prev_start_balance']);

$prev_totalexpenses = accountingAmontFormat(($totalcoa['50000100']['prev_end_balance']-$totalcoa['50000100']['prev_start_balance'])+($totalcoa['50002100']['prev_end_balance']-$totalcoa['50002100']['prev_start_balance'])+($totalcoa['50003500']['prev_end_balance']-$totalcoa['50003500']['prev_start_balance'])+($totalcoa['50002700']['prev_end_balance']-$totalcoa['50002700']['prev_start_balance'])+($totalcoa['50002750']['prev_end_balance']-$totalcoa['50002750']['prev_start_balance'])+($totalcoa['50003600']['prev_end_balance']-$totalcoa['50003600']['prev_start_balance']));
$prev_currenttax = accountingAmontFormat($totalcoa['50003451']['prev_end_balance']-$totalcoa['50003451']['prev_start_balance']);
//deferredtax
$prev_deferredtaxexpense = accountingAmontFormat($totalcoa['50003452']['prev_end_balance']-$totalcoa['50003452']['prev_start_balance']);
$prev_deferredtaxcharge = accountingAmontFormat($totalcoa['50003453']['prev_end_balance']-$totalcoa['50003453']['prev_start_balance']);
//deferredtax
$prev_deferredtax =accountingAmontFormat(($totalcoa['50003452']['prev_end_balance']-$totalcoa['50003452']['prev_start_balance']) +($totalcoa['50003453']['prev_end_balance']-$totalcoa['50003453']['prev_start_balance']));
/**/

?>
<script type="text/javascript">
    function printDiv(divId) {
       var printContents = document.getElementById(divId).innerHTML;
       var originalContents = document.body.innerHTML;
       document.body.innerHTML = "<html lang=\"en\"><head><title>Profit and Loss Report</title></head><body>" + printContents + "</body>";
       window.print();
       document.body.innerHTML = originalContents;
    }
    function convertHTMLToPDF() {
        var  title ="profit_and_loss";
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
<div class="card ">
    <div class="card-block">
        <form method="post">
        <div class="row">
            <div class="col-lg-3">

            </div>
            <div class="col-lg-4">


            </div>
            <?php
          $shortcode = $_POST['shortcode_date'] ?? "TM";
          $pick_date = $_POST['pick_date'] ?? "This Month";
          $from_date = $_POST['from'] ?? fromDate(date("Y-m-01"));
          $to_date = $_POST['to'] ?? fromDate(date("Y-m-t")); ?>
          <div class="col-lg-4 mb-2">
            <input type="hidden" name="pick_date" id="pick_date" value="Today">
            <input type="hidden" name="shortcode_date" id="shortcode_date" value="TD">
            <div class="btn-group dropdown-split-primary" style="width: 100%;">
              <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                <span id="btn_date"><?php echo $pick_date; ?></span>
              </button>
              <button type="button"
                class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                <span class="visually-hidden">Toggle primary</span>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TD" ? "active" : ""; ?> empty_active"
                  id="TD" href="#!"
                  onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TW" ? "active" : ""; ?> empty_active"
                  id="TW" href="#!"
                  onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week'))); ?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday '))); ?>')">This
                  Week</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TM" ? "active" : ""; ?> empty_active"
                  id="TM" href="#!"
                  onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This
                  Month</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TY" ? "active" : ""; ?> empty_active"
                  id="TY" href="#!"
                  onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This
                  Year</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "YD" ? "active" : ""; ?> empty_active"
                  id="YD" href="#!"
                  onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>')">Yesterday</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PW" ? "active" : ""; ?> empty_active"
                  id="PW" href="#!"
                  onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous
                  Week</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PM" ? "active" : ""; ?> empty_active"
                  id="PM" href="#!"
                  onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01", strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t", strtotime('last month'))); ?>')">Previous
                  Month</a>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PY" ? "active" : ""; ?> empty_active"
                  id="PY" href="#!"
                  onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01", strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31", strtotime('last year'))); ?>')">Previous
                  Year</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "CD" ? "active" : ""; ?> empty_active"
                  id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
              </div>
            </div>
            <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none' ?>" id="custome_date">
              <div class="col-lg-6">
                <input aria-label="From" type="text" class="form-control filterdynamic datepicker" name="from" id="from_date"
                  value="<?php echo $from_date; ?>">
              </div>
              <div class="col-lg-6">
                <input aria-label="To" type="text" class="form-control filterdynamic datepicker" name="to" id="to_date"
                  value="<?php echo $to_date; ?>">
              </div>
            </div>
          </div>
            <div class="col-lg-1">
                <button name="applyform" value="apply" class="btn btn-primary form-control" type="submit" ><i class="ti-search m-r-2"></i>View</button>
            </div>
        </div>
    </form>
    </div>
</div>
<div class="card">
    <div class="card-body" >
        <div class="container" id="printableArea">
            <div class="row mb-5">
                <div class="col-lg-4">
                    <div class="col bg-light-primary ">
                        <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                            <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                        </span>
                        <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo ucwords($_SESSION['config']['name']); ?></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h4 class="card-label text-center f-w-700">Profit And Loss <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small>
                        <small class="f-10 d-block">CIN : <?php echo $_SESSION['config']['cin_number'] !=""?$_SESSION['config']['cin_number']:"-"; ?></small>
                    </h4>

                </div>
            </div>
        <table class="table table-hover table-bordered"  style="width: 100%; border-collapse: collapse;">
            <tr>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3" >Profit and Loss statement for the Period </th>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: center;"><?php echo fromDate($_POST['start_date'])." To ".fromDate($_POST['end_date']); ?></th>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: center;"><?php echo fromDate($prev_start_date)." To ".fromDate($prev_end_date); ?></th>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Revenue from operations
                </td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">
                    <?php echo $revenue_from_operations; ?>
                </td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">
                    <?php echo $prev_revenue_from_operations; ?>
                </td>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Other income</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" >
                    <?php echo $otherincome; ?>
                </td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" >
                    <?php echo $prev_otherincome; ?>
                </td>
            </tr>
            <tr>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Total income</th>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $revenue_from_operations+$otherincome;?></th>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_revenue_from_operations+$otherincome;?></th>
            </tr>
            <tr>
                <th scope="col" style="font-size:16px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5" >Expenses
                </th>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Cost of materials consumed</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $Costofmaterialsconsumed; ?></td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_Costofmaterialsconsumed; ?></td>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">(a) purchases of stock-in-trade</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" >-</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">(b) Changes in inventories of finished goods, work-in-progress and stock-in-trade</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" >-</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Employee Benefits Expense
                </td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $employeebenefitsexpense; ?></td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_employeebenefitsexpense; ?></td>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Finance Costs</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $financecosts; ?></td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_financecosts; ?></td>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Other Operating Expenses
                </td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $otheroperatingexpenses; ?></td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_otheroperatingexpenses; ?></td>
            </tr>
            <tr>
                 <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Depreciation and Amortisation</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $depreciationandamortisation; ?></td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_depreciationandamortisation; ?></td>
            </tr>
            <tr>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Total Expenses</th>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $totalexpenses; ?></th>
                <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_totalexpenses; ?></th>
            </tr>
            <tr>
                <th scope="col" style="font-size:14px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5" >Profit before exceptional and extraordinary items and tax</th>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Exceptional items</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" >-</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
            </tr>
            <tr>
                <th scope="col" style="font-size:14px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5" >Profit before extraordinary items and tax</th>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Extraordinary items</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" >-</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
            </tr>
            <tr>
                <th scope="col" style="font-size:16px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5" >Profit before tax</th>
            </tr>
            <tr>
                <th scope="col" style="font-size:14px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5" >Tax expense:</th>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3"> Current tax</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $currenttax; ?></td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;" ><?php echo $prev_currenttax; ?></td>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Deferred tax</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;"><?php echo $deferredtax; ?></td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;"><?php echo $prev_deferredtax; ?></td>
            </tr>
            <tr>
                <th scope="col" style="font-size:16px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">Profit for the year</th>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
            </tr>
            <tr>
                <th scope="col" style="font-size:14px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">Earning per equity share (EPS)</th>
            </tr>
            <tr>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">- Basic and diluted (₹)
                </td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
                <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right;">-</td>
            </tr>
        </table>
    </div>

    </div>
</div>

<div class="invoice-btn-section clearfix d-print-none" style="margin-top:15px;">
    <button class="btn waves-effect waves-light btn-grd-success" onclick="printDiv('printableArea');">
        <i class="fa fa-print"></i>  Print
    </button>
    <button class="btn waves-effect waves-light btn-grd-info"  onclick="convertHTMLToPDF();">
        <i class="fa fa-download"></i>  Download
    </button>
</div>

<script>
  function dynamicSelectDateRange(type, name, start = "", end = "") {
    $("#btn_date").text(name);
    $("#pick_date").val(name);
    $("#shortcode_date").val(type);
    $(".empty_active").removeClass("active");
    $("#" + type).addClass("active");
    if (name == "Custom Date") {
      $("#custome_date").show('slow');

    } else {
      $("#custome_date").hide();
      $("#from_date").val(start);
      $("#to_date").val(end);
    }
  }
</script>
