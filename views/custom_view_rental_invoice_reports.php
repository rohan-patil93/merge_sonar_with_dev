<?php
$_SESSION['apply_js']['multiselest'] = "1";
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-12');
$url = API."?mode=getownerinvoicereports&start_month=".$start_month."&end_month=".$end_month."&property_owner_id=".$_POST['property_owner_id']."&property_owner_detail_id=".$_POST['property_owner_detail_id']."&type=INVOICES";
$api = callAPI($url);
$result = json_decode($api,1);
$property_owners = $result['property_owners'];
$property_owner_details = $result['property_owner_details'];
$customers = $result['customers'];

$invoices = $result['invoices'];
//filter
$all_property_owners = $result['all_property_owners'];
$all_property_owner_details = $result['all_property_owner_details'];
//filter
?>
<script type="text/javascript">
function printReport(divId) {
       var printContents = $("#" + divId).html();
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html lang="en"><head><title>Rental Invoice Report</title></head><body>' + printContents + '</body></html>');
    printWindow.document.close(); // close the document stream
    printWindow.print();
    printWindow.onafterprint = function() {
        printWindow.close();
    };
}
function getOwnerProperty(valu){
    $.ajax({
          url: "ajax.php",
          type: "POST",
          sync: false,
          data: "page=getpropertyownerdetails&property_owner_id="+valu,
          success: function(response){
            $("#property_owner_detail_id").html(response);
          }
      });
}

</script>
<div class="card">
    <div class="card-header">
        <h5>Owner Invoice Reports</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</a>
        </div>
    </div>
    <div class="card-body">
        <form method="post">
            <div class="row">
                <div class="col-lg-3">
                    <div class=" form-group">
                        <span class="form-label"> Owner Name</span>
                        <select aria-label="Property Owner" class="js-example-basic-single form-control" onchange="getOwnerProperty(this.value)" name="property_owner_id">
                            <option value="">All Owner</option>
                            <?php foreach($all_property_owners as $apo){ ?>
                                <option value="<?php echo $apo['id'];?>" <?php echo $_POST['property_owner_id'] == $apo['id']?"selected":""; ?>><?php echo $apo['owner_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class=" form-group">
                        <span class="form-label"> Property Name</span>
                        <select aria-label="Property Owner Detail" class="js-example-basic-single form-control " id="property_owner_detail_id" name="property_owner_detail_id">
                            <option value="">All Property</option>
                            <?php foreach($all_property_owner_details as $apo){ ?>
                                <option value="<?php echo $apo['id'];?>" <?php echo $_POST['property_owner_detail_id'] == $apo['id']?"selected":""; ?>><?php echo $apo['property_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group w-full table-card-header">
                        <span class="form-label ">Start Month<span class="text-danger">*</span></span>
                        <input aria-label="Start Month" type="month" class="form-control" name="start_month"  value="<?php echo $start_month; ?>" >
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class=" form-group">
                        <span class="form-label"> End Month</span>
                        <input aria-label="End Month" type="month" class="form-control" name="end_month"  value="<?php echo $end_month; ?>" >
                    </div>
                </div>
                <div class="col-lg-1">
                     <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> Search</button>
                </div>
            </div>
        </form>
        <div id="print_report_div">
            <!-- Required Fremwork -->
            <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" type="text/css" href="assets/css/style.css">
            <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
            <?php foreach($property_owners as $powner){ ?>
            <div class="row mt-2">
                <h3 class="text-center text-primary heading">
                    <?php echo ucwords($powner['owner_name']); ?>
                    <span class="f-14 d-block text-dark mt-1 text-center description"><?php echo $powner['address']; ?></span>
                    <span class="f-14 d-block text-dark mt-1 text-center description">GST : <?php echo $powner['gst_no']; ?></span>
                    <span class="f-14 d-block text-dark mt-1 text-center description"><?php echo "From - ".date("Y M",strtotime($start_month))." To - ".date("Y M",strtotime($end_month)); ?></span>
                </h3>
                <?php
                    $podetails = $property_owner_details[$powner['id']];
                    foreach($podetails as $podetail){
                ?>
                <h4 class="text-start text-primary mt-5 heading">
                    <?php echo ucwords($podetail['property_name']); ?>
                    <span class="f-14 d-block text-dark mt-1"><?php echo $podetail['property_address']; ?></span>
                </h4>
                <?php
                    $rentinvoices = $invoices[$powner['id']][$podetail['id']];
                    foreach($rentinvoices as $custid =>$rentinvoice){
                        $customer_detail = $customers[$custid];

                ?>
                    <h6 class="text-center text-primary heading">
                        <?php echo $customer_detail['company']; ?>
                    <span class="f-12 d-block text-dark mt-1 text-center description"><?php echo ucwords($customer_detail['display_name']); ?></span>
                    </h6>
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="table m-b-0" style="font-size:13px;">
                                <thead>
                                    <tr>
                                        <th scope="col">Rent Inv.</th>
                                        <th scope="col" class="text-right">Amount</th>
                                        <th scope="col" class="text-right">Tax</th>
                                        <th scope="col" class="text-right">Total</th>
                                        <th scope="col" class="text-right">Payment</th>
                                        <th scope="col" class="text-right">Due</th>
                                        <th scope="col" class="text-right">Debit Note</th>
                                         <th scope="col" class="text-right">Credit Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php $mk=1; foreach($rentinvoice['rent'] as $inv){ ?>
                                    <tr class="<?php echo $inv['ref_bill_type'] == "OB"?"bg-success":"";?>" <?php echo $inv['ref_bill_type'] == "OB"?'data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Opening Balance"':'';?> >
                                        <td class="p-2">
                                            <?php echo $inv['transaction_number']; ?>
                                            <span class="d-block"><?php echo $inv['transaction_date']; ?></span>
                                        </td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['tax_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['total_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['total_amount']-$inv['pending_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['pending_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['debit_total_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['credit_total_amount']); ?></td>
                                    </tr>
                            <?php $mk++;
} ?>
                                </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <table class="table m-b-0" style="font-size:13px;">
                            <thead>
                                <tr>
                                    <th scope="col">Utility Inv.</th>
                                    <th scope="col" class="text-right">Amount</th>
                                    <th scope="col" class="text-right">Tax</th>
                                    <th scope="col" class="text-right">Total</th>
                                    <th scope="col" class="text-right">Payment</th>
                                    <th scope="col" class="text-right">Due</th>
                                    <th scope="col" class="text-right">Debit Note</th>
                                     <th scope="col" class="text-right">Credit Note</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $mk=1; foreach($rentinvoice['utility'] as $inv){ ?>
                                    <tr>
                                        <td class="p-2">
                                            <?php echo $inv['transaction_number']; ?>
                                            <span class="d-block"><?php echo $inv['transaction_date']; ?></span>
                                        </td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['tax_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['total_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['total_amount']-$inv['pending_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['pending_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['debit_total_amount']); ?></td>
                                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($inv['credit_total_amount']); ?></td>
                                    </tr>
                            <?php $mk++;
} ?>
                                </tbody>
                        </table>
                    </div>
                </div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
        </div>
        <div class="row mt-5 m-l-5">
              <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp Print Report</button>
          </div>
    </div>
</div>
