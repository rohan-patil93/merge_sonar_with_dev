<?php
$_SESSION['apply_js']['multiselest'] = "1";
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-m');
$party_table = 'vendors';
$party_lable = 'Vendors';
$transaction_table = 'purchase_invoices';
$url = API."?mode=getapplicationreports&start_month=".$start_month."&end_month=".$end_month."&party_table=".$party_table."&transaction_table=".$transaction_table."&party_id=".$_POST['party_id']."&reporttype=MONTHLY";
$api = callAPI($url);
$result = json_decode($api,1);
$monthyearary = $result['monthyearary'];
$party_list = $result['party_list'];
$all_parties = $result['all_parties'];
$opening_balances = $result['opening_balance'];
$invoices = $result['invoices'];

?>
<script type="text/javascript">
function printReport(divId) {
    var printContents = $("#" + divId).html();
  var printWindow = window.open('', '_blank');
  printWindow.document.write('<html lang="en"><head><title>Purchase Invoice Monthly Report</title></head><body>' + printContents + '</body></html>');
  printWindow.document.close(); // close the document stream
  printWindow.print();
  printWindow.onafterprint = function() {
      printWindow.close();
  };
}
</script>
<div class="card">
    <div class="card-header">
        <h5><?php echo $party_lable; ?> Monthly Reports</h5>
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

      <!-- Google font-->


      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="assets/css/style.css">
      <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
      <?php foreach($party_list as $cust){ ?>
        <div class="row mt-2">
          <h3 class="text-center text-primary">
            <?php echo ucwords($cust['company']); ?>
            <h4 class="f-14 d-block text-dark mt-1 text-center"><?php echo $cust['display_name']; ?></h4>
            <h4 class="f-12 d-block text-dark mt-1 text-center"><?php echo "From - ".date("Y M",strtotime($start_month))." To - ".date("Y M",strtotime($end_month)); ?></h4>
          </h3>
        </div>
          <table class="table m-b-0" style="font-size:13px;">
              <thead>
                  <tr>
                      <th scope="col">#</th>
                      <th scope="col">Month Year</th>
                      <th scope="col" class="text-right">Invoice</th>
                      <th scope="col" class="text-right">Payment</th>
                      <th scope="col" class="text-right">Due</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                  $obpayment = $t_total_amount = $t_payment = $t_pending_amount  =  '0';
                  $opening_balance = $opening_balances[$cust['id']];
                  $obpayment = ($opening_balance['total_amount']-$opening_balance['pending_amount']);

                  $t_total_amount += $opening_balance['total_amount'];
                  $t_payment += $obpayment;
                  $t_pending_amount += $opening_balance['pending_amount'];
                ?>
                <tr>
                  <th scope="col" class="p-2" colspan="2">Opening Balance</th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['total_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($obpayment); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['pending_amount']); ?></th>
                 </tr>
                <?php
                  $i = 1; foreach($monthyearary as $mothkey=>$mothlable){
                  $rentinvoice = $invoices[$cust['id']][$mothkey];
                  $payment = $rentinvoice['total_amount']-$rentinvoice['pending_amount'];
                  $t_total_amount += $rentinvoice['total_amount'];
                  $t_payment += $payment;
                  $t_pending_amount += $rentinvoice['pending_amount'];
                ?>
                <tr>
                  <td class="p-2"><?php echo $i; ?></td>
                  <td class="p-2"><?php echo $mothlable; ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($payment); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['pending_amount']); ?></td>
                </tr>
                <?php $i++;
} ?>
                <tr>
                  <th scope="col" colspan="2" class="p-2 text-right">Total</th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_total_amount); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_payment); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_pending_amount); ?></th>
                </tr>
                <tr>
                  <th scope="col" class=" p-2 text-right" colspan="2"></th>
                  <th scope="col" colspan="2" class="p-2 text-right">Total Credit Notes (-)</th>
                  <th scope="col" class="p-2 text-right text-danger"><?php echo customeummarmonyformateifine($credit_notes[$cust['id']]['total_amount']); ?></th>
                </tr>
                <tr>
                  <th scope="col" class="p-2 text-right" colspan="2"></th>
                  <th scope="col" colspan="2" class="p-2 text-right">Total Debit Notes (+)</th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($debit_notes[$cust['id']]['total_amount']); ?></th>
                </tr>
                <tr>
                  <th scope="col" class="p-2 text-right" colspan="2"></th>
                  <th scope="col" colspan="2" class="p-2 text-right">Un-Allocated Payment</th>
                  <th scope="col" class="p-2 text-right text-danger"><?php echo customeummarmonyformateifine($payments[$cust['id']]['unallocated_amount']); ?></th>
                </tr>
                <tr>
                  <th scope="col" class="p-2 text-right" colspan="2"></th>
                  <th scope="col" colspan="2" class="p-2 text-right">Final Due</th>
                  <th scope="col" class="p-2 text-right text-danger"><?php echo customeummarmonyformateifine(($t_pending_amount+$debit_notes[$cust['id']]['total_amount'])-($payments[$cust['id']]['unallocated_amount']+$credit_notes[$cust['id']]['total_amount'])); ?></th>
                </tr>

              </tbody>
            </table>
       <?php } ?>
       </div>
       <div class="row mt-5 m-l-5">
        <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp Print Report</button>
      </div>
         </div>
     </div>

    </div>
</div>

