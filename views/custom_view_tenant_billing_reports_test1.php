<?php
$_SESSION['apply_js']['multiselest'] = "1";
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-12');
$url = API."?mode=gettenantbillingreportsnewreport&start_month=".$start_month."&end_month=".$end_month."&customer_id=".$_POST['customer_id'];
$api = callAPI($url);
$result = json_decode($api,1);
$monthyearary = $result['monthyearary'];
$customers = $result['customers'];
$property_tenant_details = $result['property_tenant_details'];
$property_owners = $result['property_owners'];
$property_owner_details = $result['property_owner_details'];
$invoices = $result['invoices'];
$all_customers = $result['all_customers'];
$opening_balances = $result['opening_balance'];
$payments = $result['payments'];
$credit_notes = $result['credit_notes'];
$debit_notes = $result['debit_notes'];
$payments_month_wyse = $result['payments_month_wyse'];
$opening_balance_payments_month_wyse = $result['opening_balance_payments_month_wyse'];
//echo "<pre>";print_r($result); echo "</pre>";
//echo "<pre>";print_r($opening_balance_payments_month_wyse); echo "</pre>";
?>
<script type="text/javascript">
function printReport(divId) {
    var printContents = $("#" + divId).html();
  var printWindow = window.open('', '_blank');
  printWindow.document.write('<html><head><title></title></head><body>' + printContents + '</body></html>');
  printWindow.document.close(); // close the document stream
  printWindow.print();
  printWindow.onafterprint = function() {
      printWindow.close();
  };
}
</script>
<div class="card">
    <div class="card-header">
        <h5><?= __('Tenant Invoice Reports'); ?></h5>
        <div class="card-header-right table-card-header ">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i><?= __('Back'); ?></a>
      </div>
    </div>
    <div class="card-body">
      <form method="post">
        <div class="row">
          <div class="col-lg-3">
                <div class=" form-group">
                    <label for="fld_customer_id" class="form-label"> <?= __('Tenant'); ?></label>
                    <select id="fld_customer_id" class="js-example-basic-single form-control"  name="customer_id">
                      <option value=""><?= __('All Tenant'); ?></option>
                      <?php foreach($all_customers as $cust){ ?>
                        <option value="<?php echo $cust['id'];?>" <?php echo $_POST['customer_id'] == $cust['id']?"selected":""; ?>><?php echo $cust['display_name'];?></option>
                      <?php } ?>
                    </select>
                </div>
            </div>
          <div class="col-lg-2">
              <div class="form-group w-full">
                  <label for="fld_start_month" class="form-label "><?= __('Start Date'); ?><span class="text-danger">*</span></label>
                  <input id="fld_start_month" type="month" class="form-control" name="start_month"  value="<?php echo $start_month; ?>" >
              </div>
          </div>
          <div class="col-lg-2">
              <div class=" form-group">
                  <label for="fld_end_month" class="form-label"> <?= __('End Date'); ?></label>
                  <input id="fld_end_month" type="month" class="form-control" name="end_month"  value="<?php echo $end_month; ?>" >
              </div>
          </div>
          <div class="col-lg-1">
               <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> <?= __('Search'); ?></button>
          </div>
        </div>
     </form>
     <div id="print_report_div">

      <!-- Google font-->
      <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet"> -->

      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" type="text/css" href="assets/css/style.css">
      <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
      <?php foreach($customers as $cust){
        $paymentmonthwyse = $payments_month_wyse[$cust['id']];
        //print_r($payment);
        ?>
        <div class="row mt-2">
          <h3 class="text-center text-primary">
            <?php echo ucwords($cust['company']); ?>
            <h4 class="f-14 d-block text-dark mt-1 text-center"><?php echo $cust['display_name']; ?></h4>
            <h4 class="f-12 d-block text-dark mt-1 text-center"><?php echo "From - ".date("Y M",strtotime($start_month))." To - ".date("Y M",strtotime($end_month)); ?></h4>
          </h3>
        </div>
        <?php
          $property_tenant_detail = $property_tenant_details[$cust['id']];
          foreach($property_tenant_detail as $ptd){ ?>
            <h4 class="text-start text-primary mt-5">
            <?php echo ucwords($property_owners[$ptd['property_owner_id']]['owner_name']); ?>
            <span class="f-14 d-block text-dark mt-1"><?php echo $property_owner_details[$ptd['property_owner_detail_id']]['property_name']; ?></span>
          </h4>
          <table class="table m-b-0 border" style="font-size:13px;">
              <thead>
                  <tr>
                      <th rowspan="2">#</th>
                      <th rowspan="2" class="border-right-black"><?= __('Month Year'); ?></th>
                      <th colspan="3" class="text-center border-right-black"><?= __('Rent'); ?></th>
                      <th colspan="3" class="text-center border-right-black"><?= __('Utility'); ?></th>
                      <th colspan="3" class="text-center"><?= __('Total'); ?></th>
                  </tr>
                  <tr>
                    <th scope="col" class="text-right"><?= __('Invoice'); ?></th>
                    <th scope="col" class="text-right "><?= __('Payment'); ?></th>
                    <th scope="col" class="text-right border-right-black"><?= __('Due'); ?></th>
                    <th scope="col" class="text-right"><?= __('Invoice'); ?></th>
                    <th scope="col" class="text-right "><?= __('Payment'); ?></th>
                    <th scope="col" class="text-right border-right-black"><?= __('Due'); ?></th>
                    <th scope="col" class="text-right"><?= __('Invoice'); ?></th>
                    <th scope="col" class="text-right "><?= __('Payment'); ?></th>
                    <th scope="col" class="text-right"><?= __('Due'); ?></th>
                  </tr>
              </thead>
              <tbody>
                <?php


                $column_rent_utility_total = $column_rent_utility_payment_total = $column_rent_total = $column_rent_payment_total = $column_utility_total = $column_utility_payment_total = 0;

                $rentinvoice=$utilityinvoice=$rentpayment=$utilitypayment=$st_total=$st_payment=$st_due=$t_rent_total_amount=$t_rent_payment=$t_rent_pending_amount=$t_utility_total_amount=$t_utility_payment=$t_utility_pending_amount=$t_total=$t_payment = $t_due =$st_ob_payment= 0;

                   $opening_balance = $opening_balances[$ptd['id']];
                   $payment_opening_balance = $opening_balance_payments_month_wyse[$ptd['id']];

                  $ob_row_total_inv = $opening_balance['ob_rent']['total_amount']+$opening_balance['ob_utility']['total_amount'];
                  $ob_row_total_payment = $payment_opening_balance['ob_rent']['total_paid']+$payment_opening_balance['ob_utility']['total_paid'];
                  $openingbaldue += $ob_row_total_inv-$ob_row_total_payment;


                  $column_rent_total+= $opening_balance['ob_rent']['total_amount'];
                  $column_rent_payment_total += $payment_opening_balance['ob_rent']['total_paid'];
                  $column_utility_total += $opening_balance['ob_utility']['total_amount'];
                  $column_utility_payment_total += $payment_opening_balance['ob_utility']['total_paid'];

                  $column_rent_utility_total += $ob_row_total_inv;
                  $column_rent_utility_payment_total += $ob_row_total_payment;


                  // //payments
                  //$obrentpayment = ($opening_balance['ob_rent']['total_amount']-$opening_balance['ob_rent']['pending_amount']);
                  // $obutilitypayment = ($opening_balance['ob_utility']['total_amount']-$opening_balance['ob_utility']['pending_amount']);
                  //  //payments
                  // $st_ob_total += $opening_balance['ob_rent']['total_amount']+$opening_balance['ob_utility']['total_amount'];
                  // $st_ob_payment = $obrentpayment+$obutilitypayment;
                  // $st_ob_due = $opening_balance['ob_rent']['pending_amount']+$opening_balance['ob_utility']['pending_amount'];


                  // $t_rent_total_amount += $opening_balance['ob_rent']['total_amount'];
                  // $t_rent_payment += $obrentpayment;
                  // $t_rent_pending_amount += $opening_balance['ob_rent']['pending_amount'];
                  // $t_utility_total_amount += $opening_balance['ob_utility']['total_amount'];
                  // $t_utility_payment += $obutilitypayment;
                  // $t_utility_pending_amount += $opening_balance['ob_utility']['pending_amount'];
                  // $t_total += $st_ob_total;
                  // $t_payment += $st_ob_payment;
                  // $t_due += $st_ob_due;

                ?>
                <tr>
                  <th class="p-2 border-right-black" colspan="2"><?= __('Opening Balance'); ?> </th>
                  <td class="p-2 text-right fw-bold"><?php echo customeummarmonyformateifine($opening_balance['ob_rent']['total_amount']); ?></td>
                  <td class="p-2 text-right  fw-bold"><?php echo customeummarmonyformateifine($payment_opening_balance['ob_rent']['total_paid']); ?></td>
                  <td class="p-2 text-right border-right-black fw-bold">-</td>
                  <td class="p-2 text-right fw-bold"><?php echo customeummarmonyformateifine($opening_balance['ob_utility']['total_amount']); ?></td>
                  <td class="p-2 text-right fw-bold"><?php echo customeummarmonyformateifine($payment_opening_balance['ob_utility']['total_paid']); ?></td>
                  <td class="p-2 text-right border-right-black fw-bold">-</td>
                  <td class="p-2 text-right fw-bold"><?php echo customeummarmonyformateifine($ob_row_total_inv); ?></td>
                  <td class="p-2 text-right  fw-bold"><?php echo customeummarmonyformateifine($ob_row_total_payment); ?></td>
                  <td class="p-2 text-right fw-bold"><?php echo customeummarmonyformateifine($openingbaldue); ?></td>
                 </tr>

                <?php
                  $i = 1; foreach($monthyearary as $mothkey=>$mothlable){


                    $rentinvoice = $invoices[$ptd['id']]['rent'][$mothkey];
                    $utilityinvoice = $invoices[$ptd['id']]['utility'][$mothkey];
                    $rentpaymentsmonthwyse = $payments_month_wyse[$ptd['id']]['rent'][$mothkey];
                    $utilitypaymentsmonthwyse = $payments_month_wyse[$ptd['id']]['utility'][$mothkey];

                    //calculate Start
                    $row_total_inv = $rentinvoice['total_amount']+$utilityinvoice['total_amount'];
                    $row_total_payment = $rentpaymentsmonthwyse['total_paid']+$utilitypaymentsmonthwyse['total_paid'];
                    $column_rent_total+= $rentinvoice['total_amount'];
                    $column_rent_payment_total += $rentpaymentsmonthwyse['total_paid'];
                    $column_utility_total += $utilityinvoice['total_amount'];
                    $column_utility_payment_total += $utilitypaymentsmonthwyse['total_paid'];

                    $column_rent_utility_total += $row_total_inv;
                    $column_rent_utility_payment_total += $row_total_payment;

                    $openingbaldue += ($row_total_inv-$row_total_payment);

                  //  $row_duo_amt_inv = (($ob_row_total_inv+$row_total_inv)-($ob_row_total_payment+$row_total_payment));

                     //calculate End
                  ?>
                <tr>
                  <td class="p-2"><?php echo $i;  ?></td>
                  <td class="p-2 border-right-black"><?php echo $mothlable; ?></td>
                  <td class="p-2 text-right "><?php echo customeummarmonyformateifine($rentinvoice['total_amount']); ?></td>
                  <td class="p-2 text-right "><?php echo customeummarmonyformateifine($rentpaymentsmonthwyse['total_paid']); ?></td>
                  <td class="p-2 text-right  border-right-black"><?php echo customeummarmonyformateifine($rentinvoice['total_amount']-$rentpaymentsmonthwyse['total_paid']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilityinvoice['total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilitypaymentsmonthwyse['total_paid']); ?></td>
                  <td class="p-2 text-right border-right-black"><?php echo customeummarmonyformateifine($utilityinvoice['total_amount']-$utilitypaymentsmonthwyse['total_paid']); ?></td>
                  <td class="p-2 text-right "><?php echo customeummarmonyformateifine($row_total_inv); ?></td>
                  <td class="p-2 text-right "><?php echo customeummarmonyformateifine($row_total_payment); ?></td>
                  <td class="p-2 text-right "><?php echo customeummarmonyformateifine($openingbaldue); ?></td>
                </tr>
                <?php $i++; } ?>
                <tr>
                  <th colspan="2" class="text-right border-right-black"><?= __('Total'); ?></th>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($column_rent_total); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($column_rent_payment_total); ?></td>
                  <td class="text-right border-right-black fw-bold">-</td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($column_utility_total); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($column_utility_payment_total); ?></td>
                  <td class="text-right border-right-black fw-bold">-</td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($column_rent_utility_total); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($column_rent_utility_payment_total); ?></td>
                   <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($column_rent_utility_total-$column_rent_utility_payment_total); ?></td>
                </tr>
                <tr>
                  <th class="text-right" colspan="8">-</th>
                  <th scope="row" colspan="2" class="text-right"><?= __('Total Credit Notes (-)'); ?></th>
                  <td class="text-right text-danger fw-bold"><?php echo customeummarmonyformateifine($credit_notes[$cust['id']][$ptd['id']]['total_amount']); ?></td>
                </tr>
                <tr>
                  <th class="text-right" colspan="8">-</th>
                  <th scope="row" colspan="2" class="text-right"><?= __('Total Debit Notes (+)'); ?></th>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($debit_notes[$cust['id']][$ptd['id']]['total_amount']); ?></td>
                </tr>
               <!--  <tr>
                  <th class="text-right" colspan="6">-</th>
                  <th colspan="2" class="text-right">Un-Allocated Payment</th>
                  <th class="text-right text-danger"><?php echo customeummarmonyformateifine($payments[$cust['id']]['unallocated_amount']); ?></th>
                </tr> -->
                <tr>
                  <th class="text-right" colspan="8">-</th>
                  <th scope="row" colspan="2" class="text-right"><?= __('Final Due'); ?></th>
                  <td class="text-right text-danger fw-bold"><?php echo customeummarmonyformateifine(($column_rent_utility_total+$debit_notes[$cust['id']][$ptd['id']]['total_amount'])-($credit_notes[$cust['id']][$ptd['id']]['total_amount']+$column_rent_utility_payment_total)); ?></td>
                </tr>


              </tbody>
            </table>
          <?php } ?>
       <?php } ?>
       </div>
       <div class="row mt-5 m-l-5">
        <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp <?= __('Print Report'); ?></button>
      </div>
         </div>
     </div>

    </div>
</div>



