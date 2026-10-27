<?php
if($_SESSION['insert_msg'] !=""){
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = "";
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
            }
        }
    </script>
<?php
}
$_SESSION['apply_js']['multiselest'] = '1';
$cusurl = API."?mode=getreceipts&type=list&vendor_id=".$_GET['vendor_id']."&customer_id=".$_GET['customer_id']."&employee_id=".$_GET['employee_id']."&filter_text=".$_GET['filter_text'];
$apicall = callAPI($cusurl);
$result = json_decode($apicall,1);
$vendorlist = $result['vendors'];
$vendors = array();
foreach($vendorlist as $ven){
  $vendors[$ven['id']] = $ven['display_name'];
}
$bank_accounts = $result['bank_accounts'];
$customers = $result['customers'];
$customer = array();
foreach($customers as $cust){
  $customer[$cust['id']] = $cust['display_name'];
}
$payments = $result['payments'];
$payment_allocations = $result['payment_allocations'];

$employees = $result['employees'];
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
  function closePopUp(rowid){
      $(".custom-popup-open").hide();
    }
    function opentTenantUtilityPopUp(rowid){
      $(".custom-popup-open").hide();
      $(".open_popup_tenant_billing_"+rowid).show('slow');
    }
</script>
<div class="card">
    <div class="card-header">
        <h5> Receipts</h5>
        <div class="card-header-right table-card-header">
           <a href="index?option=COM_INSERT_RECEIPTS" class="btn btn-success mb-2 desktop-view">
              <i class="icofont icofont-plus icon-white "></i> Receipts</a>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
          </div>
        </div>
    <div class="card-body m-t-20">
      <form method="GET">
        <div class="row">
            <input type="hidden" name="option" value="<?php echo $_GET['option']; ?>">
            <div class="form-group col-lg-3 w-full" id='transaction_customers' >
                <span class="form-label">Customer </span>
                <select aria-label="Customer" class="form-control form-select js-example-basic-single" id="customer_id" name="customer_id">
                    <option value=""> Select Any Customer </option>
                    <?php foreach ($customers as $cus){  ?>
                        <option  <?php if($_GET['customer_id'] == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id']; ?>   > <?php echo $cus['display_name'].' ('.$cus['company'].')'; ?>
                    </option>
                <?php } ?>
                </select>
                <span class="messages"></span>
            </div>

            <div class="col-lg-2 form-group form-validation-class">
                <span class="form-label">Name/Date/ref_no/etc </span>
                <input aria-label="Filter Text" class="form-control" type="text" name="filter_text"  id="filter_text" value="<?php echo $_GET['filter_text']; ?>" >
                 <span class="messages"></span>
            </div>
            <div class="col-lg-1">
               <button class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-t-30" name="filterbtn" value="filterbtn">Search</button>
            </div>

        </div>
            </form>
        <div class="row mt-5">
                 <?php

                 foreach($payments as $pay){
                    $partyname = array();
                    if($pay['transaction_type'] == 'customers'){
                        $partyname = $customer;
                    }
                    ?>
            <div class="col-lg-3" id="categoryfilter">
                <div class="card <?php echo count((array)$payment_allocations[$pay['id']])=='0'?"bg-info":""; ?>">
                    <div class="card-body">
                        <table class="table table-hover m-b-0" style="font-size:13px;">
                          <tbody>
                                <tr>
                                  <th scope="col" class="p-2">Number <span class="form-label label" style="color: <?php echo $stausresult[$pay['current_status']]['color']; ?>;    background:  <?php echo $stausresult[$pay['current_status']]['background_color']; ?>; border-color: <?php echo $stausresult[$pay['current_status']]['background_color']; ?>;"><?php echo $stausresult[$pay['current_status']]['name']; ?></span></th>
                                  <td class="text-right p-2 "> <a href="index?option=COM_INSERT_RECEIPTS&Rid=<?php echo deocdeId("id:".$pay['id']);?>" class="text-primary"><i class="fa fa-edit"></i>  <?php echo $pay['transaction_no']; ?></a></td>
                                </tr>
                                <tr>
                                  <th scope="col" class="p-2">Transaction Date</th>
                                  <td class="text-right p-2 w-b-w-s"><?php echo fromDate($pay['payment_date']); ?></td>
                                </tr>
                                <tr>
                                  <th scope="col" class="p-2">Part Name</th>
                                  <td class="text-right p-2 text-capitalize w-b-w-s"><?php
                                  $partynameli = json_decode($pay['party_id'],1);
                                  foreach($partynameli as $partyid){
                                    echo "<span class='d-block'>".$partyname[$partyid]."</span>";
                                  }
                                   ?></td>
                                </tr>
                                <tr >
                                  <th scope="col" class="p-2" >Payment
                                  </th>
                                  <td class="text-right p-2 text-capitalize"><?php echo $pay['payment_type']; ?></td>
                                </tr>
                                <tr>
                                  <th scope="col" class="p-2">Ref No.</th>
                                  <td class="text-right p-2"><?php echo $pay['ref_no']; ?></td>
                                </tr>
                                <tr>
                                  <th scope="col" class="p-2">Due Date</th>
                                  <td class="text-right p-2"><?php echo fromDate($pay['due_date']); ?></td>
                                </tr>
                                <tr>
                                  <th scope="col" class="p-2">Total</th>
                                  <td class="text-right p-2"><?php echo customeummarmonyformate($pay['amount']); ?></td>
                              </tr>
                               <tr>
                                  <th scope="col" class="p-2">Un-Allocated Amount</th>
                                  <td class="text-right p-2"><?php echo customeummarmonyformate($pay['unallocated_amount']); ?></td>
                              </tr>
                              <tr>
                                  <th scope="col" class="p-2">Allocated Amount</th>
                                  <td class="text-right p-2 text-danger"><?php echo customeummarmonyformate($pay['allocated_amount']); ?></td>
                              </tr>
                              <tr>
                                  <th scope="col" class="p-2" >Transactions</th>
                                  <td class="text-right p-2 text-primary" id="custom-popup"><span class="cursor-pointer" onclick="opentTenantUtilityPopUp('<?php echo $pay['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-eye"></i> <?php echo count((array)$payment_allocations[$pay['id']]); ?></span>

                                    <div class="custom-popup-open open_popup_tenant_billing_<?php echo $pay['id']; ?> text-start" style="width: 333px;  display: none;">
                                      <div class="card">
                                        <div class="card-header">
                                          <h5>Payment History</h5>
                                          <div class="card-header-right">
                                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closePopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                          </div>
                                        </div>
                                         <div class="card-body">
                                            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                                              <thead>
                                                <tr>
                                                  <th scope="col" class="p-1" style="width: 60%;">Number</th>
                                                  <th scope="col" class="p-1 text-right" style="width: 40%;">Paid Amount</th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                              <?php foreach($payment_allocations[$pay['id']] as $payalloc){ ?>
                                                <tr>
                                                  <td class="p-1"><?php echo $payalloc['transaction_no']; ?></td>
                                                  <td class="p-1 text-right"><?php echo $payalloc['paid_amonut']; ?></td>
                                                </tr>
                                              <?php } ?>

                                            </tbody>
                                        </table>
                                      </div>
                                  </div>
                                </div>
                                  </td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>

          <?php } ?>
            </div>
        </div>
    </div>
</div>
