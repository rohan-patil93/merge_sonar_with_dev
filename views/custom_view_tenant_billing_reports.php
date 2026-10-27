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
?>
<script type="text/javascript">
function printReport(divId) {
    var printContents = $("#" + divId).html();
  var printWindow = window.open('', '_blank');
  printWindow.document.write('<html lang="en"><head><title>Tenant Billing Report</title></head><body>' + printContents + '</body></html>');
  printWindow.document.close(); // close the document stream
  printWindow.print();
  printWindow.onafterprint = function() {
      printWindow.close();
  };
}
</script>
<div class="card">
    <div class="card-header">
        <h5>Tenant Invoice Reports</h5>
        <div class="card-header-right table-card-header ">
      <a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</a>
      </div>
    </div>
    <div class="card-body">
      <form method="post">
        <div class="row">
          <div class="col-lg-3">
                <div class=" form-group">
                    <span class="form-label"> Tenant</span>
                    <select aria-label="Customer" class="js-example-basic-single form-control" onchange="getOwnerProperty(this.value)" name="customer_id">
                      <option value="">All Tenant</option>
                      <?php foreach($all_customers as $cust){ ?>
                        <option value="<?php echo $cust['id'];?>" <?php echo $_POST['customer_id'] == $cust['id']?"selected":""; ?>><?php echo $cust['display_name'];?></option>
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
      <?php foreach($customers as $cust){ ?>
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
          <table class="table m-b-0" style="font-size:13px;">
              <thead>
                  <tr>
                      <th scope="col" rowspan="2">#</th>
                      <th scope="col" rowspan="2">Month Year</th>
                      <th scope="col" colspan="3" class="text-center">Rent</th>
                      <th scope="col" colspan="3" class="text-center">Utility</th>
                      <th scope="col" colspan="3" class="text-center">Total</th>
                  </tr>
                  <tr>
                    <th scope="col" class="text-right">Invoice</th>
                    <th scope="col" class="text-right">Payment</th>
                    <th scope="col" class="text-right">Due</th>
                    <th scope="col" class="text-right">Invoice</th>
                    <th scope="col" class="text-right">Payment</th>
                    <th scope="col" class="text-right">Due</th>
                    <th scope="col" class="text-right">Invoice</th>
                    <th scope="col" class="text-right">Payment</th>
                    <th scope="col" class="text-right">Due</th>
                  </tr>
              </thead>
              <tbody>
                <?php
                $rentinvoice=$utilityinvoice=$rentpayment=$utilitypayment=$st_total=$st_payment=$st_due=$t_rent_total_amount=$t_rent_payment=$t_rent_pending_amount=$t_utility_total_amount=$t_utility_payment=$t_utility_pending_amount=$t_total=$t_payment = $t_due = 0;

                  $opening_balance = $opening_balances[$ptd['id']];
                  $obrentpayment = ($opening_balance['ob_rent']['total_amount']-$opening_balance['ob_rent']['pending_amount']);
                  $obutilitypayment = ($opening_balance['ob_utility']['total_amount']-$opening_balance['ob_utility']['pending_amount']);
                  $st_ob_total += $opening_balance['ob_rent']['total_amount']+$opening_balance['ob_utility']['total_amount'];
                  $st_ob_payment = $obrentpayment+$obutilitypayment;
                  $st_ob_due = $opening_balance['ob_rent']['pending_amount']+$opening_balance['ob_utility']['pending_amount'];

                  $t_rent_total_amount += $opening_balance['ob_rent']['total_amount'];
                  $t_rent_payment += $obrentpayment;
                  $t_rent_pending_amount += $opening_balance['ob_rent']['pending_amount'];
                  $t_utility_total_amount += $opening_balance['ob_utility']['total_amount'];
                  $t_utility_payment += $st_ob_payment;
                  $t_utility_pending_amount += $opening_balance['ob_utility']['pending_amount'];
                  $t_total += $st_ob_total;
                  $t_payment += $st_ob_payment;
                  $t_due += $st_ob_due;
                ?>
                <tr>
                  <th scope="col" class="p-2" colspan="2">Opening Balance</th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['ob_rent']['total_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($obrentpayment); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['ob_rent']['pending_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['ob_utility']['total_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($obutilitypayment); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($opening_balance['ob_utility']['pending_amount']); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($st_ob_total); ?></th>
                  <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($st_ob_payment); ?></th>
                    <th scope="col" class="p-2 text-right"><?php echo customeummarmonyformateifine($st_ob_due); ?></th>
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
                  <td class="p-2"><?php echo $i; ?></td>
                  <td class="p-2"><?php echo $mothlable; ?>

                  </td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentpayment); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($rentinvoice['pending_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilityinvoice['total_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilitypayment); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($utilityinvoice['pending_amount']); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($st_total); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($st_payment); ?></td>
                  <td class="p-2 text-right"><?php echo customeummarmonyformateifine($st_due); ?></td>
                </tr>
                <?php $i++;
} ?>
                <tr>
                  <th scope="col" colspan="2" class="text-right">Total</th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_rent_total_amount); ?></th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_rent_payment); ?></th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_rent_pending_amount); ?></th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_utility_total_amount); ?></th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_utility_payment); ?></th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_utility_pending_amount); ?></th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_total); ?></th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($t_payment); ?></th>
                  <th scope="col" class="text-right text-danger"><?php echo customeummarmonyformateifine($t_due); ?></th>
                </tr>
                <tr>
                  <th scope="col" class="text-right" colspan="8">-</th>
                  <th scope="col" colspan="2" class="text-right">Total Credit Notes (-)</th>
                  <th scope="col" class="text-right text-danger"><?php echo customeummarmonyformateifine($credit_notes[$cust['id']]['total_amount']); ?></th>
                </tr>
                <tr>
                  <th scope="col" class="text-right" colspan="8">-</th>
                  <th scope="col" colspan="2" class="text-right">Total Debit Notes (+)</th>
                  <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($debit_notes[$cust['id']]['total_amount']); ?></th>
                </tr>
                <tr>
                  <th scope="col" class="text-right" colspan="8">-</th>
                  <th scope="col" colspan="2" class="text-right">Un-Allocated Payment</th>
                  <th scope="col" class="text-right text-danger"><?php echo customeummarmonyformateifine($payments[$cust['id']]['unallocated_amount']); ?></th>
                </tr>
                <tr>
                  <th scope="col" class="text-right" colspan="8">-</th>
                  <th scope="col" colspan="2" class="text-right">Final Due</th>
                  <th scope="col" class="text-right text-danger"><?php echo customeummarmonyformateifine(($t_due+$debit_notes[$cust['id']]['total_amount'])-($payments[$cust['id']]['unallocated_amount']+$credit_notes[$cust['id']]['total_amount'])); ?></th>
                </tr>

              </tbody>
            </table>
          <?php } ?>
       <?php } ?>
       </div>
       <div class="row mt-5 m-l-5">
        <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp Print Report</button>
      </div>
         </div>
     </div>

    </div>
</div>

