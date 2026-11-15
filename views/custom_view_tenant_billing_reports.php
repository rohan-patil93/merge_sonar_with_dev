<?php
$_SESSION['apply_js']['multiselest'] = "1";
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-12');
$url = API."?mode=gettenantbillingreports&start_month=".$start_month."&end_month=".$end_month."&customer_id=".$_POST['customer_id'];
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
$all_this_date_range_payments = $result['all_this_date_range_payments_res'];
//echo "<pre>";print_r($all_this_date_range_payments); echo "</pre>";
//echo "<pre>"; print_r($result['debit_notes']); echo "</pre>";
//echo "<pre>";print_r($result['credit_notes_sql']); echo "</pre>";
// echo "<pre>";print_r($result['debit_notes_sql']); echo "</pre>";
?>
<style>
  @media print {
    .col-lg-2 {
        width: 16.666667% !important;
        float: left !important;
    }

    .col-lg-3 {
        width: 25% !important;
        float: left !important;
    }

    .col-lg-4 {
        width: 33.333333% !important;
        float: left !important;
    }

    .col-lg-6 {
        width: 50% !important;
        float: left !important;
    }

    .col-lg-12 {
        width: 100% !important;
        float: left !important;
    }

    .row {
        display: block !important;
        width: 100% !important;
    }
}
</style>
<script type="text/javascript">
function printReport(divId) {
   var printContents = document.getElementById(divId).innerHTML;
   var originalContents = document.body.innerHTML;
   document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
   window.print();
   document.body.innerHTML = originalContents;
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
      <?php foreach($customers as $cust){ ?>
        <?php
          $property_tenant_detail = $property_tenant_details[$cust['id']];
          foreach($property_tenant_detail as $ptd){ ?>

          <div class="row mt-2">
            <h3 class="text-center text-primary">
              <?php echo ucwords($property_owners[$ptd['property_owner_id']]['owner_name']); ?>
              <h4 class="f-14 d-block text-dark mt-1 text-center"><?php echo $property_owner_details[$ptd['property_owner_detail_id']]['property_name']; ?></h4>
              <h4 class="f-12 d-block text-dark mt-1 text-center"><?php echo "From - ".date("Y M",strtotime($start_month))." To - ".date("Y M",strtotime($end_month)); ?></h4>
            </h3>
          </div>
          <h4 class="text-start text-primary mt-5">
             <?php echo ucwords($cust['company']); ?>
            <span class="f-14 d-block text-dark mt-1"><?php echo $cust['display_name']; ?></span>
          </h4>
          <table class="table m-b-0 border" style="font-size:13px;">
              <thead>
                  <tr>
                      <th rowspan="2">#</th>
                      <th rowspan="2" class="border-right-black"><?= __('Month Year'); ?></th>
                      <th colspan="3" class="text-center border-right-black"><?= __('Rent'); ?></th>
                      <th colspan="3" class="text-center border-right-black"><?= __('Utility'); ?></th>
                      <th colspan="3" class="text-center border-right-black"><?= __('Total'); ?></th>
                  </tr>
                  <tr>
                    <th scope="col" class="text-right"><?= __('Invoice'); ?></th>
                    <th scope="col" class="text-right"><?= __('Payment'); ?></th>
                    <th scope="col" class="text-right border-right-black"><?= __('Due'); ?></th>
                    <th scope="col" class="text-right"><?= __('Invoice'); ?></th>
                    <th scope="col" class="text-right"><?= __('Payment'); ?></th>
                    <th scope="col" class="text-right border-right-black"><?= __('Due'); ?></th>
                    <th scope="col" class="text-right"><?= __('Invoice'); ?></th>
                    <th scope="col" class="text-right"><?= __('Payment'); ?></th>
                    <th scope="col" class="text-right border-right-black"><?= __('Due'); ?></th>
                  </tr>
              </thead>
              <tbody>
                <?php
                $rentinvoice=$utilityinvoice=$rentpayment=$utilitypayment=$st_total=$st_payment=$st_due=$t_rent_total_amount=$t_rent_payment=$t_rent_pending_amount=$t_utility_total_amount=$t_utility_payment=$t_utility_pending_amount=$t_total=$t_payment = $t_due =$st_ob_payment= 0;

                  $opening_balance = $opening_balances[$ptd['id']];
                  //payments
                  $obrentpayment = ($opening_balance['ob_rent']['total_amount']-$opening_balance['ob_rent']['pending_amount']);
                  $obutilitypayment = ($opening_balance['ob_utility']['total_amount']-$opening_balance['ob_utility']['pending_amount']);
                   //payments
                  $st_ob_total += $opening_balance['ob_rent']['total_amount']+$opening_balance['ob_utility']['total_amount'];
                  $st_ob_payment = $obrentpayment+$obutilitypayment;
                  $st_ob_due = $opening_balance['ob_rent']['pending_amount']+$opening_balance['ob_utility']['pending_amount'];


                  $t_rent_total_amount += $opening_balance['ob_rent']['total_amount'];
                  $t_rent_payment += $obrentpayment;
                  $t_rent_pending_amount += $opening_balance['ob_rent']['pending_amount'];
                  $t_utility_total_amount += $opening_balance['ob_utility']['total_amount'];
                  $t_utility_payment += $obutilitypayment;
                  $t_utility_pending_amount += $opening_balance['ob_utility']['pending_amount'];
                  $t_total += $st_ob_total;
                  $t_payment += $st_ob_payment;
                  $t_due += $st_ob_due;
                ?>
                <tr>
                  <th class="p-2 border-right-black" colspan="2"><?= __('Opening Balance'); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['ob_rent']['total_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($obrentpayment); ?></th>
                  <th scope="col" class="p-2 text-right border-right-black"><?php echo customeummarmonyformateifine($opening_balance['ob_rent']['pending_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['ob_utility']['total_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($obutilitypayment); ?></th>
                  <th scope="col" class="p-2 text-right border-right-black"><?php echo customeummarmonyformateifine($opening_balance['ob_utility']['pending_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($st_ob_total); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($st_ob_payment); ?></th>
                    <th scope="col" class="p-2 text-right border-right-black"><?php echo customeummarmonyformateifine($st_ob_due); ?></th>
                 </tr>

                <?php
                  $i = 1; foreach($monthyearary as $mothkey=>$mothlable){

                  $rentinvoice = $invoices[$ptd['id']]['rent'][$mothkey];
                  $utilityinvoice = $invoices[$ptd['id']]['utility'][$mothkey];

                  $rentpayment = $rentinvoice['total_amount']-$rentinvoice['pending_amount'];
                  $utilitypayment = $utilityinvoice['total_amount']-$utilityinvoice['pending_amount'];

                  $st_total = $rentinvoice['total_amount']+$utilityinvoice['total_amount'];
                  $st_payment = $rentpayment+$utilitypayment;

                  $st_due = $rentinvoice['pending_amount']+$utilityinvoice['pending_amount'];

                  $t_rent_total_amount += $rentinvoice['total_amount'];
                  $t_rent_payment += $rentpayment;
                  $t_rent_pending_amount += $rentinvoice['pending_amount'];
                  $t_utility_total_amount += $utilityinvoice['total_amount'];
                  $t_utility_payment += $utilitypayment;
                  $t_utility_pending_amount += $utilityinvoice['pending_amount'];
                  $t_total += $st_total;
                  $t_payment += $st_payment;
                  $t_due += $st_due;
                  ?>
                <tr>
                  <td class="p-2"><?php echo $i; //print_r($rentinvoice); ?></td>
                  <td class="p-2 border-right-black"><?php echo $mothlable; ?>
                    <!-- <p class="d-block text-start mt-1 mb-0 " >
                          <span data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Opening Balance">O : <?php echo customeummarmonyformateifine($rentinvoice['ob_total_amount']); ?></span>
                            <span class="m-l-5" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Payments">P : <?php echo customeummarmonyformateifine($rentinvoice['ob_total_amount']-$rentinvoice['ob_pending_amount']); ?></span>
                            <span class="m-l-5" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Due Amount">D : <?php echo customeummarmonyformateifine($rentinvoice['ob_pending_amount']); ?></span></p> -->
                  </td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentpayment); ?></td>
                  <td class="p-2 text-right border-right-black"><?php echo customeummarmonyformateifine($rentinvoice['pending_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilityinvoice['total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilitypayment); ?></td>
                  <td class="p-2 text-right border-right-black"><?php echo customeummarmonyformateifine($utilityinvoice['pending_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($st_total); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($st_payment); ?></td>
                  <td class="p-2 text-right border-right-black"><?php echo customeummarmonyformateifine($st_due); ?></td>
                </tr>
                <?php $i++; } ?>
                <tr>
                  <th colspan="2" class="text-right border-right-black"><?= __('Total'); ?></th>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($t_rent_total_amount); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($t_rent_payment); ?></td>
                  <td class="text-right border-right-black fw-bold"><?php echo customeummarmonyformateifine($t_rent_pending_amount); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($t_utility_total_amount); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($t_utility_payment); ?></td>
                  <td class="text-right border-right-black fw-bold"><?php echo customeummarmonyformateifine($t_utility_pending_amount); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($t_total); ?></td>
                  <td class="text-right fw-bold"><?php echo customeummarmonyformateifine($t_payment); ?></td>
                  <td class="text-right text-danger border-right-black fw-bold"><?php echo customeummarmonyformateifine($t_due); ?></td>
                </tr>
                <tr>
                  <th class="text-right border-right-black" colspan="8">-</th>
                  <th scope="row" colspan="2" class="text-right"><?= __('Total Credit Notes (-)'); ?></th>
                  <td class="text-right text-danger border-right-black fw-bold"><?php echo customeummarmonyformateifine($credit_notes[$cust['id']][$ptd['id']]['total_amount']); ?></td>
                </tr>
                <tr>
                  <th class="text-right border-right-black" colspan="8">-</th>
                  <th scope="row" colspan="2" class="text-right"><?= __('Total Debit Notes (+)'); ?></th>
                  <td class="text-right border-right-black fw-bold"><?php echo customeummarmonyformateifine($debit_notes[$cust['id']][$ptd['id']]['total_amount']); ?></td>
                </tr>
                <tr>
                  <th class="text-right border-right-black" colspan="8">-</th>
                  <th scope="row" colspan="2" class="text-right"><?= __('Un-Allocated Payment'); ?></th>
                  <td class="text-right text-danger border-right-black fw-bold"><?php echo customeummarmonyformateifine($payments[$cust['id']]['unallocated_amount']); ?></td>
                </tr>
                <tr>
                  <th class="text-right border-right-black" colspan="8">-</th>
                  <th scope="row" colspan="2" class="text-right"><?= __('Final Due'); ?></th>
                  <td class="text-right text-danger  fw-bold"><?php echo customeummarmonyformateifine(($t_due+$debit_notes[$cust['id']][$ptd['id']]['total_amount'])-($payments[$cust['id']]['unallocated_amount']+$credit_notes[$cust['id']][$ptd['id']]['total_amount'])); ?></td>
                </tr>


              </tbody>
            </table>
          <?php } ?>

  <h4 class="text-start text-primary mt-5"><?= __('Payments'); ?></h4>
  <?php if(count((array)$all_this_date_range_payments[$cust['id']]) != 0){ ?>
  <div class="row">
  <?php
  $chunks = array_chunk($all_this_date_range_payments[$cust['id']] , 10);
  foreach ($chunks as $tableData) {
      $totalQty = 0;
      $totalAmount = 0;
  ?>
  <div class="col-lg-2" style="width: 25% !important;float: left !important;">
      <table class="table m-b-0 border" style="font-size:13px;">

          <!-- Same Heading for every table -->
          <thead>
              <tr>
                  <th><?= __('Sr No'); ?></th>
                  <th><?= __('date'); ?></th>
                  <th><?= __('Amount'); ?></th>
              </tr>
          </thead>
          <tbody>
          <?php foreach ($tableData as $key => $payvalue) {

              $totalAmount += $payvalue['total_paid_amonut'];
          ?>
              <tr>
                  <td class="p-1"><?php echo $key + 1 ?></td>
                  <td class="p-1"><?php echo htmlspecialchars(fromDate($payvalue['payment_date'])); ?></td>
                  <td class="p-1 text-right"><?php echo $payvalue['total_paid_amonut'] ?></td>
              </tr>

          <?php } ?>
          </tbody>
          <!-- Sum Footer -->
          <tfoot>
              <tr>
                  <th colspan="2"><?= __('Total'); ?></th>
                  <td class="text-right fw-bold"><?= $totalAmount ?></td>
              </tr>
          </tfoot>
      </table>
  </div>
  <?php } ?>
  </div>
<?php }else{ echo "<span>".__('No Records')."</span>";  } ?>


       <?php } ?>
       </div>
       <div class="row mt-5 m-l-5">
        <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp <?= __('Print Report'); ?></button>
      </div>
         </div>
     </div>

    </div>
</div>



