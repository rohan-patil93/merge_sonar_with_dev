<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$parent_account_code = $_POST['account_code'] !=""?$_POST['account_code']:0;
if (isset($_POST['applyform']) && $_POST['applyform'] == 'apply') {
  $balance_date = $_POST['from'] ? date('Y-m-d', strtotime($_POST['from'])) : date('Y-m-01');
  $prev_balance_date = $_POST['to'] ? date('Y-m-d', strtotime($_POST['to'])) : date('Y-m-t');
} else {
  $balance_date = date('Y-m-01');
  $prev_balance_date = date('Y-m-t');
}

$getapi = API."?mode=getbalancesheetreport&balance_date=".$balance_date."&prev_balance_date=".$prev_balance_date;
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
$balances = $result['balances'];
$parentcoa = $parentchildacount = array();
foreach($balances as $coa){
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
$sharecapital = accountingAmontFormat($totalcoa['30000210']['start_balance']);
$reservesandsurplus = accountingAmontFormat($totalcoa['30000250']['start_balance']);
$moneyreceivedagainstsharewarrants = accountingAmontFormat($totalcoa['30000400']['start_balance']);

$longtermborrowings = accountingAmontFormat($totalcoa['20001100']['start_balance']);
$deferredtaxliabilitiess = accountingAmontFormat($totalcoa['2002200']['start_balance']);
$otherlongtermliabilities = accountingAmontFormat($totalcoa['2002300']['start_balance']);
$longtermprovisions    = accountingAmontFormat($totalcoa['2002400']['start_balance']);

$shorttermborrowings = accountingAmontFormat($totalcoa['2003100']['start_balance']);
$tradepayables = accountingAmontFormat($totalcoa['20003500']['start_balance']);
$othercurrentliabilities = accountingAmontFormat($totalcoa['2004000']['start_balance']);
$shorttermprovisions = accountingAmontFormat($totalcoa['2004300']['start_balance']);

$propertyplantequipment= accountingAmontFormat($totalcoa['10001100']['start_balance']);
$intangibleassets= accountingAmontFormat($totalcoa['10001400']['start_balance']+$totalcoa['10001500']['start_balance']+$totalcoa['10001700']['start_balance']);
$capitalworkinprogress= accountingAmontFormat($totalcoa['10001600']['start_balance']!=""?$totalcoa['10001600']['start_balance']:0);
$intangibleassetsunderdevelopment= accountingAmontFormat($totalcoa['10001700']['start_balance'] !=""?$totalcoa['10001700']['start_balance']:0);

$noncurrentinvestments= accountingAmontFormat($totalcoa['10001800']['start_balance']!=""?$totalcoa['10001800']['start_balance']:0);
$deferredtaxassets= accountingAmontFormat($totalcoa['10002400']['start_balance']!=""?$totalcoa['10002400']['start_balance']:0);
$longtermloansandadvances= accountingAmontFormat($totalcoa['10002300']['start_balance']!=""?$totalcoa['10002300']['start_balance']:0);
$othernoncurrentassets= accountingAmontFormat($totalcoa['10002100']['start_balance']!=""?$totalcoa['10002100']['start_balance']:0);
$currentinvestments= accountingAmontFormat($totalcoa['10003100']['start_balance']!=""?$totalcoa['10003100']['start_balance']:0);
$inventories= accountingAmontFormat($totalcoa['10003400']['start_balance']!=""?$totalcoa['10003400']['start_balance']:0);
$tradereceivables= accountingAmontFormat($totalcoa['10003900']['start_balance']!=""?$totalcoa['10003900']['start_balance']:0);
$cashandbankbalances= accountingAmontFormat($totalcoa['10005000']['start_balance']!=""?$totalcoa['10005000']['start_balance']:0);
$shorttermloansandadvances= accountingAmontFormat($totalcoa['10006000']['start_balance']!=""?$totalcoa['10006000']['start_balance']:0);
$othercurrentassets= accountingAmontFormat($totalcoa['10008000']['start_balance']!=""?$totalcoa['10008000']['start_balance']:0);

$prev_balances = $result['prev_balances'];
$prev_parentcoa = $prev_parentchildacount = array();
foreach($prev_balances as $coa){
    if($coa['parent_account_code'] == 0){
        $prev_parentcoa[] = $coa;
    }
    $prev_parentchildacount[$coa['parent_account_code']][] = $coa;
}
$parentchildaccount = $totalcoa = $parentlist =  array();
foreach($prev_parentcoa as $pcoa1){
    $parentchildaccount[$pcoa1['account_code']][] = $pcoa1;
    $totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa1['closing_balance'];
    foreach($prev_parentchildacount[$pcoa1['account_code']] as $pcoa2){
        $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;
        $totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa2['closing_balance'];
        $totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa2['closing_balance'];
        foreach($prev_parentchildacount[$pcoa2['account_code']] as $pcoa3){
            $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;
            $totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa3['closing_balance'];
            $totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa3['closing_balance'];
            $totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa3['closing_balance'];
            foreach($prev_parentchildacount[$pcoa3['account_code']] as $pcoa4){
                $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;

                $totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
                $totalcoa[$pcoa4['account_code']]['pre_start_balance']  +=  $pcoa4['closing_balance'];
                foreach($prev_parentchildacount[$pcoa4['account_code']] as $pcoa5){
                    $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;

                    $totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa4['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
                    $totalcoa[$pcoa5['account_code']]['pre_start_balance']  +=  $pcoa5['closing_balance'];
                    foreach($prev_parentchildacount[$pcoa5['account_code']] as $pcoa6){
                        $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
                        $totalcoa[$pcoa1['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa2['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa3['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa4['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa5['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
                        $totalcoa[$pcoa6['account_code']]['pre_start_balance']  +=  $pcoa6['closing_balance'];
                    }
                }
            }
        }
    }
}
//Start preview Balances
$pre_sharecapital = accountingAmontFormat($totalcoa['30000210']['pre_start_balance']);
$pre_reservesandsurplus = accountingAmontFormat($totalcoa['30000250']['pre_start_balance']);
$pre_moneyreceivedagainstsharewarrants = accountingAmontFormat($totalcoa['30000400']['pre_start_balance']);

$pre_longtermborrowings = accountingAmontFormat($totalcoa['20001100']['pre_start_balance']);
$pre_deferredtaxliabilitiess = accountingAmontFormat($totalcoa['2002200']['pre_start_balance']);
$pre_otherlongtermliabilities = accountingAmontFormat($totalcoa['2002300']['pre_start_balance']);
$pre_longtermprovisions    = accountingAmontFormat($totalcoa['2002400']['pre_start_balance']);

$pre_shorttermborrowings = accountingAmontFormat($totalcoa['2003100']['pre_start_balance']);
$pre_tradepayables = accountingAmontFormat($totalcoa['20003500']['pre_start_balance']);
$pre_othercurrentliabilities = accountingAmontFormat($totalcoa['2004000']['pre_start_balance']);
$pre_shorttermprovisions = accountingAmontFormat($totalcoa['2004300']['pre_start_balance']);

$pre_propertyplantequipment= accountingAmontFormat($totalcoa['10001100']['pre_start_balance']);
$pre_intangibleassets= accountingAmontFormat($totalcoa['10001400']['pre_start_balance']+$totalcoa['10001500']['pre_start_balance']+$totalcoa['10001700']['pre_start_balance']);
$pre_capitalworkinprogress= accountingAmontFormat($totalcoa['10001600']['pre_start_balance']!=""?$totalcoa['10001600']['pre_start_balance']:0);
$pre_intangibleassetsunderdevelopment= accountingAmontFormat($totalcoa['10001700']['pre_start_balance'] !=""?$totalcoa['10001700']['pre_start_balance']:0);

$pre_noncurrentinvestments= accountingAmontFormat($totalcoa['10001800']['pre_start_balance']!=""?$totalcoa['10001800']['pre_start_balance']:0);
$pre_deferredtaxassets= accountingAmontFormat($totalcoa['10002400']['pre_start_balance']!=""?$totalcoa['10002400']['pre_start_balance']:0);
$pre_longtermloansandadvances= accountingAmontFormat($totalcoa['10002300']['pre_start_balance']!=""?$totalcoa['10002300']['pre_start_balance']:0);
$pre_othernoncurrentassets= accountingAmontFormat($totalcoa['10002100']['pre_start_balance']!=""?$totalcoa['10002100']['pre_start_balance']:0);
$pre_currentinvestments= accountingAmontFormat($totalcoa['10003100']['pre_start_balance']!=""?$totalcoa['10003100']['pre_start_balance']:0);
$pre_inventories= accountingAmontFormat($totalcoa['10003400']['pre_start_balance']!=""?$totalcoa['10003400']['pre_start_balance']:0);
$pre_tradereceivables= accountingAmontFormat($totalcoa['10003900']['pre_start_balance']!=""?$totalcoa['10003900']['pre_start_balance']:0);
$pre_cashandbankbalances= accountingAmontFormat($totalcoa['10005000']['pre_start_balance']!=""?$totalcoa['10005000']['pre_start_balance']:0);
$pre_shorttermloansandadvances= accountingAmontFormat($totalcoa['10006000']['pre_start_balance']!=""?$totalcoa['10006000']['pre_start_balance']:0);
$pre_othercurrentassets= accountingAmontFormat($totalcoa['10008000']['pre_start_balance']!=""?$totalcoa['10008000']['pre_start_balance']:0);
//End preview Balances
?>
<script type="text/javascript">
    function printDiv(divId) {
       var printContents = document.getElementById(divId).innerHTML;
       var originalContents = document.body.innerHTML;
       document.body.innerHTML = "<html lang=\"en\"><head><title>Balance Sheet Report</title></head><body>" + printContents + "</body>";
       window.print();
       document.body.innerHTML = originalContents;
    }
    function convertHTMLToPDF() {
        var  title ="balance_sheet";
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
            <div class="col-lg-6">

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
    <div class="card-body">
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
                    <h4 class="card-label text-center f-w-700">Balance Sheet <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small>
                        <small class="f-10 d-block">CIN : <?php echo $_SESSION['config']['cin_number'] !=""?$_SESSION['config']['cin_number']:"-"; ?></small>
                    </h4>
                </div>
            </div>
            <table class="table table-hover table-bordered"  style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3" >Balance Sheet as at
                    </th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: center;"><?php echo fromDate($balance_date); ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: center;"><?php echo fromDate($prev_balance_date); ?></th>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">I. Equity and Liabilities</th>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">
                        <span style=" margin-left: 10px;">(1) Shareholders funds</span>
                    </th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;">(a) Share capital</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $sharecapital; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_sharecapital; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left:10px;">(b) Reserves and surplus</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $reservesandsurplus; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_reservesandsurplus; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left:10px;">(c) Money received against share warrants</span></td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $moneyreceivedagainstsharewarrants; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_moneyreceivedagainstsharewarrants; ?></th>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="4">(2) Share application money pending allotment</th>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: center;">-</td>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">(3) Non-Current liabilities</th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;">(a) Long-term borrowings</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $longtermborrowings;?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_longtermborrowings;?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;">(b) Deferred tax liabilities (net)</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $deferredtaxliabilitiess;?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_deferredtaxliabilitiess;?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;">(c) Other long-term liabilities</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $otherlongtermliabilities;?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_otherlongtermliabilities;?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;">(d) Long-term provisions</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $longtermprovisions; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_longtermprovisions; ?></th>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">(4) Current liabilities</th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;">(a) Short-term borrowings</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $shorttermborrowings; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_shorttermborrowings; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(b) Trade payables</span>
                        <span style="margin-left: 10px;display: block;">- Total outstanding dues of micro enterprises and small enterprises; and</span>
                        <span style="margin-left: 10px;display: block;">- Total outstanding dues of creditors otherthan micro enterprises and small enterprises
                        </span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $tradepayables; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_tradepayables; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(c) Other current liabilities</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $othercurrentliabilities; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_othercurrentliabilities; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(d) Short-term provisions</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $shorttermprovisions; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_shorttermprovisions; ?></th>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">II. Assets</th>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">
                        <span style="margin-left:10px;">(1) Non-current assets</span></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(a) Property, plant & equipment and Intangible assets</span>
                        <span style="margin-left: 15px;display: block;">(i) Property, plant & equipment</span>
                        <span style="margin-left: 15px;display: block;">(ii) Intangible assets</span>
                        <span style="margin-left: 15px;display: block;">(iii) Capital work-in-progress</span>
                        <span style="margin-left: 15px;display: block;">(iv) Intangible assets under development</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: right;" >
                        <span style="display: block;">-</span>
                        <span style="display: block;"><?php echo $propertyplantequipment; ?></span>
                        <span style="display: block;"><?php echo $intangibleassets; ?></span>
                        <span style="display: block;"><?php echo $capitalworkinprogress; ?></span>
                        <span style="display: block;"><?php echo $intangibleassetsunderdevelopment; ?></span>
                    </th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: right;" >
                        <span style="display: block;">-</span>
                        <span style="display: block;"><?php echo $pre_propertyplantequipment; ?></span>
                        <span style="display: block;"><?php echo $pre_intangibleassets; ?></span>
                        <span style="display: block;"><?php echo $pre_capitalworkinprogress; ?></span>
                        <span style="display: block;"><?php echo $pre_intangibleassetsunderdevelopment; ?></span>
                    </th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(b) Non-current investments</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $noncurrentinvestments; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_noncurrentinvestments; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(c) Deferred tax assets (net)</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $deferredtaxassets; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_deferredtaxassets; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(d) Long-term loans and advances</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $longtermloansandadvances; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_longtermloansandadvances; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(e) Other non-current assets</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $othernoncurrentassets; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_othernoncurrentassets; ?></th>
                </tr>
                <tr>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="5">
                        <span style="margin-left:10px;">(2) Current assets</span></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(a) Current investments</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $currentinvestments; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_currentinvestments; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(b) Inventories</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $inventories; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_inventories; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(a) Trade receivables</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $tradereceivables; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_tradereceivables; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(b) Cash and bank balances</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $cashandbankbalances; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_cashandbankbalances; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(c) Short-term loans and advances</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $shorttermloansandadvances; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_shorttermloansandadvances; ?></th>
                </tr>
                <tr>
                    <td style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: left;" colspan="3">
                        <span style="margin-left: 10px;display: block;">(d) Other current assets</span>
                    </td>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $othercurrentassets; ?></th>
                    <th scope="col" style="font-size:13px; font-family: Tahoma; border: 1px solid #cccccc; padding: 5px; text-align: right; text-align: left;" ><?php echo $pre_othercurrentassets; ?></th>
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
