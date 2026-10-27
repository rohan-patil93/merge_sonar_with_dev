<?php
$_SESSION['apply_js']['multiselest'] = "1";
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-m');
$party_table = 'vendors';
$party_lable = 'Vendors';
$transaction_table = 'purchase_invoices';
$url = API."?mode=getapplicationreports&start_month=".$start_month."&end_month=".$end_month."&party_table=".$party_table."&transaction_table=".$transaction_table."&party_id=".$_POST['party_id']."&reporttype=INVOICESUM";
$api = callAPI($url);
$result = json_decode($api,1);
$all_parties = $result['all_parties'];
$invoices = $result['invoices'];

?>
<script type="text/javascript">
function printReport(divId) {
    var printContents = $("#" + divId).html();
  var printWindow = window.open('', '_blank');
  printWindow.document.write('<html lang="en"><head><title>Purchase Invoice Total Report</title></head><body>' + printContents + '</body></html>');
  printWindow.document.close(); // close the document stream
  printWindow.print();
  printWindow.onafterprint = function() {
      printWindow.close();
  };
}
</script>
<div class="card">
    <div class="card-header">
        <h5><?php echo $party_lable; ?> Reports</h5>
        <div class="card-header-right table-card-header ">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</a>
      </div>
    </div>
    <div class="card-body">
      <form method="post">
        <div class="row">
          <div class="col-lg-3">
                <div class=" form-group">
                    <span class="form-label"> <?php echo $party_lable; ?></span>
                    <select aria-label="Party" class="js-example-basic-single form-control" name="party_id">
                      <option value="">All <?php echo $party_lable; ?></option>
                      <?php foreach($all_parties as $cust){ ?>
                        <option value="<?php echo $cust['id'];?>" <?php echo $_POST['party_id'] == $cust['id']?"selected":""; ?>><?php echo "(".$cust['company'].") ".$cust['display_name'];?></option>
                      <?php } ?>
                    </select>
                </div>
            </div>
          <div class="col-lg-2">
              <div class="form-group w-full">
                  <span class="form-label ">Start Date<span class="text-danger">*</span></span>
                  <input aria-label="Start Month" type="month" class="form-control" name="start_month"  value="<?php echo $start_month; ?>" >
              </div>
          </div>
          <div class="col-lg-2">
              <div class=" form-group">
                  <span class="form-label"> End Date</span>
                  <input aria-label="End Month" type="month" class="form-control" name="end_month"  value="<?php echo $end_month; ?>" >
              </div>
          </div>
          <div class="col-lg-1">
               <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> Search</button>
          </div>
        </div>
     </form>

      <div id="print_report_div">
      <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="assets/css/style.css">
      <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
        <div class="row mt-2">
          <h3 class="text-center text-primary">
            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
              <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
            </span>
             <h3 class="text-center text-primary"><?php echo ucwords($_SESSION['config']['name']); ?></h3>
            <h4 class="f-12 d-block text-dark mt-1 text-center"><?php echo "From - ".date("Y M",strtotime($start_month))." To - ".date("Y M",strtotime($end_month)); ?></h4>
          </h3>
        </div>
          <table class="table m-b-0" style="font-size:13px;">
              <thead>
                  <tr>
                      <th scope="col">#</th>
                      <th scope="col"><?php echo $party_lable; ?></th>
                      <th scope="col" class="text-right">Invoice Amt</th>
                      <th scope="col" class="text-right">Debit Note</th>
                      <th scope="col" class="text-right">Credit Note</th>
                      <th scope="col" class="text-right">Payments</th>
                      <th scope="col" class="text-right">Due Amt</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  $obpayment = $t_total_amount = $t_payment = $t_pending_amount  =  '0';
                  $i = 1;
                  foreach($invoices as $partdetails){
                    $payment = $partdetails['total_amount']-$partdetails['pending_amount'];
                    $t_total_amount += $partdetails['total_amount'];
                    $t_debit_total_amount += $partdetails['debit_total_amount'];
                    $t_credit_total_amount += $partdetails['credit_total_amount'];
                    $t_payment += $payment;
                    $t_pending_amount += $partdetails['pending_amount'];
                ?>
                <tr>
                  <td class="p-2"><?php echo $i; ?></td>
                  <td class="p-2"><?php echo $all_parties[$partdetails['party_id']]['company']."(".$all_parties[$partdetails['party_id']]['display_name'].")"; ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($partdetails['total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($partdetails['debit_total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($partdetails['credit_total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($payment); ?></td>
                  <td class="p-2 text-right text-danger"><?php echo customeummarmonyformateifine($partdetails['pending_amount']); ?></td>
                </tr>
                <?php $i++;
} ?>
                <tr>
                  <th scope="col" colspan="2" class="p-2 text-center">Total</th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_total_amount); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_debit_total_amount); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_credit_total_amount); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_payment); ?></th>
                  <th scope="col" class="p-2 text-right text-danger"><?php echo customeummarmonyformateifine($t_pending_amount); ?></th>
                </tr>

              </tbody>
            </table>
       </div>
       <div class="row mt-5 m-l-5">
        <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp Print Report</button>
      </div>
         </div>
     </div>

    </div>
</div>

