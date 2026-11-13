<?php
$_SESSION['apply_js']['multiselest'] = "1";
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-m');
$party_table = 'vendors';
$party_lable = 'Vendors';
$transaction_table = 'purchase_invoices';
$url = API."?mode=getapplicationreports&start_month=".$start_month."&end_month=".$end_month."&party_table=".$party_table."&transaction_table=".$transaction_table."&party_id=".$_POST['party_id']."&reporttype=INVOICEREPORT";
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
  printWindow.document.write('<html lang="en"><head><title><?= htmlspecialchars(__('Purchase Invoice Report'), ENT_QUOTES, 'UTF-8'); ?></title></head><body>' + printContents + '</body></html>');
  printWindow.document.close(); // close the document stream
  printWindow.print();
  printWindow.onafterprint = function() {
      printWindow.close();
  };
}
</script>
<div class="card">
    <div class="card-header">
        <h5><?php echo $party_lable; ?> <?= __('Monthly Reports'); ?></h5>
        <div class="card-header-right table-card-header ">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i><?= __('Back'); ?></a>
      </div>
    </div>
    <div class="card-body">
      <form method="post">
        <div class="row">
          <div class="col-lg-3">
                <div class=" form-group">
                    <span class="form-label"> <?php echo $party_lable; ?></span>
                    <select aria-label="<?= htmlspecialchars(__('Party'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single form-control" name="party_id">
                      <option value=""><?= __('All'); ?> <?php echo $party_lable; ?></option>
                      <?php foreach($all_parties as $cust){ ?>
                        <option value="<?php echo $cust['id'];?>" <?php echo $_POST['party_id'] == $cust['id']?"selected":""; ?>><?php echo "(".$cust['company'].") ".$cust['display_name'];?></option>
                      <?php } ?>
                    </select>
                </div>
            </div>
          <div class="col-lg-2">
              <div class="form-group w-full">
                  <span class="form-label "><?= __('Start Date'); ?><span class="text-danger">*</span></span>
                  <input aria-label="<?= htmlspecialchars(__('Start Month'), ENT_QUOTES, 'UTF-8'); ?>" type="month" class="form-control" name="start_month"  value="<?php echo $start_month; ?>" >
              </div>
          </div>
          <div class="col-lg-2">
              <div class=" form-group">
                  <span class="form-label"> <?= __('End Date'); ?></span>
                  <input aria-label="<?= htmlspecialchars(__('End Month'), ENT_QUOTES, 'UTF-8'); ?>" type="month" class="form-control" name="end_month"  value="<?php echo $end_month; ?>" >
              </div>
          </div>
          <div class="col-lg-1">
               <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> <?= __('Search'); ?></button>
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
        <div class="row mt-5">
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
                      <th scope="col"><?= __('Inv Number'); ?></th>
                      <th scope="col"><?= __('Inv Date'); ?></th>
                      <th scope="col"><?= __('Due Date'); ?></th>
                      <th scope="col" class="text-right"><?= __('Invoice'); ?></th>
                      <th scope="col" class="text-right"><?= __('Payment'); ?></th>
                      <th scope="col" class="text-right"><?= __('Due'); ?></th>
                      <th scope="col" class="text-right"><?= __('Debit Note'); ?></th>
                      <th scope="col" class="text-right"><?= __('Credit Note'); ?></th>
                  </tr>
              </thead>
              <tbody>
                <?php
                $payment = $t_total_amount = $t_payment = $t_pending_amount = $t_debit_total_amount = $t_credit_total_amount = 0;
                if(count((array)$invoices[$cust['id']]) != 0){
                    $i = 1; foreach($invoices[$cust['id']] as $mothkey=>$invoicereport){
                    $payment = $invoicereport['total_amount']-$invoicereport['pending_amount'];
                    $t_total_amount += $invoicereport['total_amount'];
                    $t_payment += $payment;
                    $t_pending_amount += $invoicereport['pending_amount'];
                    $t_debit_total_amount += $invoicereport['debit_total_amount'];
                    $t_credit_total_amount += $invoicereport['credit_total_amount'];
                  ?>
                  <tr>
                    <td class="p-2"><?php echo $i; ?></td>
                    <td class="p-2"><?php echo $invoicereport['transaction_number']; ?></td>
                    <td class="p-2"><?php echo fromDate($invoicereport['transaction_date']); ?></td>
                    <td class="p-2"><?php echo fromDate($invoicereport['due_date']); ?></td>
                    <td class="p-2 text-right"><?php echo customeummarmonyformateifine($invoicereport['total_amount']); ?></td>
                    <td class="p-2 text-right"><?php echo customeummarmonyformateifine($payment); ?></td>
                    <td class="p-2 text-right"><?php echo customeummarmonyformateifine($invoicereport['pending_amount']); ?></td>
                    <td class="p-2 text-right"><?php echo customeummarmonyformateifine($invoicereport['debit_total_amount']); ?></td>
                    <td class="p-2 text-right"><?php echo customeummarmonyformateifine($invoicereport['credit_total_amount']); ?></td>
                  </tr>
                  <?php $i++;
} ?>
                  <tr>
                    <th scope="col" colspan="4" class="p-2 text-right"><?= __('Total'); ?></th>
                    <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_total_amount); ?></th>
                    <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_payment); ?></th>
                    <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_pending_amount); ?></th>
                    <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_debit_total_amount); ?></th>
                    <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($t_credit_total_amount); ?></th>
                  </tr>
                <?php }else{ ?>
                  <tr>
                    <th scope="col" colspan="10"  class="text-center"><i class="ti-alert text-warning"></i> <?= __('No Invoices'); ?></th>
                  </tr>
                <?php } ?>

                </tbody>
              </table>
         <?php } ?>
       </div>
       <div class="row mt-5 m-l-5">
        <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp <?= __('Print Report'); ?></button>
      </div>
         </div>
     </div>

    </div>
</div>

