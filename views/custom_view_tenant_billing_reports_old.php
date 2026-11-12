<?php 
$start_month = $_POST['start_month'] !=""?$_POST['start_month']:date('Y-01');
$end_month = $_POST['end_month'] !=""?$_POST['end_month']:date('Y-12');
$url = API."?mode=gettenantbillingreports&start_month=".$start_month."&end_month=".$end_month; 
$api = callAPI($url);
$result = json_decode($api,1);  

$monthyearary = $result['monthyearary'];
$property_tenant_details = $result['property_tenant_details'];
$property_owner_details = $result['property_owner_details'];
$property_owners = $result['property_owners'];
$ptbilling = $result['property_tenant_billings'];
$rental_invoices = $result['rental_invoices'];
$utility_rental_invoices = $result['utility_rental_invoices'];
$opening_balance_inv = $result['opening_balance_inv'];
//echo "<pre>";print_r($opening_balance_inv); echo "</pre>";
?>
<div class="card">
    <form method="post">
	<div class="card-header  row">
        
        <div class="col-lg-6">
            <h5>Tenant Invoice Reports</h5>
        </div>
        <div class="col-lg-2">
            <div class="form-group w-full table-card-header">
                <label class="form-label ">Start Date<span class="text-danger">*</span></label>
				<input type="month" class="form-control" name="start_month"  value="<?php echo $start_month; ?>" >
            </div>
        </div>
        <div class="col-lg-2">
            <div class=" form-group">
                <label class="form-label"> End Date</label>
                <input type="month" class="form-control" name="end_month"  value="<?php echo $end_month; ?>" >
            </div>
        </div>
        <div class="col-lg-1">
             <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> Search</button>
        </div>
        <div class="col-lg-1">
         	<a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>  
         </div>
    
    </div>
     </form>
    <div class="card-body" style="overflow: scroll;">
        <?php if(count((array)$property_tenant_details) !=0){ ?>
        <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc"  > 
            <thead>
                <tr> 
                    <th rowspan="2">#</th>      
                    <th rowspan="2">Tenant Name</th> 
                    <th rowspan="2">Owner Name</th> 
                    <th rowspan="2" class="align-center">Property Name</th>
                    <th rowspan="2" class="align-center">Opening Balance</th>
                    <?php foreach($monthyearary as $mothkey=>$mothlable){ ?> 
                         <th colspan="2" class="text-center"><?php echo $mothlable; ?></th> 
                    <?php } ?> 
                </tr>
                <tr> 
                  <?php foreach($monthyearary as $mothkey=>$mothlable){ ?> 
                         <th>Rent</th> 
                         <th>Utility</th>
                    <?php } ?> 
                </tr>
            </thead>
            <tbody>
                <?php $mk= 1;foreach($property_tenant_details as $ptd){
                    ?>
                 <tr> 
                    <td class="p-2"><?php echo $mk; ?></td>
                    <td class="p-2"><span class="d-block"><?php echo $ptd['display_name']; ?></span>
                    <span class="d-block"><?php echo $ptd['email']; ?></span>
                <span class="d-block"><?php echo $ptd['phone']; ?></span></td>
                <td class="p-2"><?php echo $property_owners[$ptd['property_owner_id']]; ?></td>
                <td class="p-2"><?php echo $property_owner_details[$ptd['property_owner_detail_id']]; ?></td>
                <td class="p-2" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Opening Balance">
                  <table>
                    <tr>
                <?php 
                    $obinvoice = $opening_balance_inv[$ptd['property_tenant_detail_id']];
                    foreach($obinvoice as $obinvoices){  ?>
                      <td class="p-1">
                        
                        <p class="d-block text-dark text-start mb-2"><?php echo fromDate($obinvoices['transaction_date']); ?></p>
                        <p class="d-block text-start mb-2"><a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $bilamt['rental_invoice_id']; ?>&table=rental_invoices&title=Rental Invoice" target="_blank" class="text-primary text-start">I &nbsp; : <?php echo customeummarmonyformateifine($obinvoices['total_amount']); ?></a></p>
                          <p class="d-block text-danger text-start mb-0">D : <?php echo customeummarmonyformateifine($obinvoices['pending_amount']); ?></p>
                      </td>
                <?php } ?>
                      </tr>
                  </table>
                </td>
                  <?php 
                    foreach($monthyearary as $mothkey=>$mothlable){ 
                      $bilamt =  $ptbilling[$ptd['property_tenant_detail_id']][$mothkey];
                      $rentinv = $rental_invoices[$bilamt['rental_invoice_id']];
                      $utilinv = $utility_rental_invoices[$bilamt['utility_rental_invoice_id']];

                  ?>
                      <td class="text-right p-2">
                      <?php 
                        if($bilamt['rental_invoice_id'] ==""){ 
                          echo '<p class="d-block text-dark text-start mb-2" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Bill Amount">B : '.customeummarmonyformateifine($bilamt['rent_total_amount'])."</span>"; 
                        }else{ // print_r($rentinv);
                        ?> 
                        <p class="d-block text-dark text-start mb-2" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Bill Amount">B : <?php echo customeummarmonyformateifine($bilamt['rent_total_amount']); ?></p>
                         <p class="d-block text-start mb-2" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Invoice Amount"><a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $bilamt['rental_invoice_id']; ?>&table=rental_invoices&title=Rental Invoice" target="_blank" class="text-primary text-start">I &nbsp; : <?php echo customeummarmonyformateifine($rentinv['total_amount']); ?></a></p>
                          <p class="d-block text-danger text-start mb-0" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Due Amount">D : <?php echo customeummarmonyformateifine($rentinv['pending_amount']); ?></p>
                      <?php } ?>
                      </td> 
                      <td class="text-right p-2">
                      <?php 
                        if($bilamt['utility_rental_invoice_id'] ==""){ 
                          echo '<p class="d-block text-dark text-start mb-2">B : '.customeummarmonyformateifine($bilamt['utility_amount'])."</span>"; 
                        }else{
                        ?> 
                         <p class="d-block text-dark text-start mb-2" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Bill Amount">B : <?php echo customeummarmonyformateifine($bilamt['utility_amount']); ?></p>
                         <p class="d-block text-start mb-2" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Invoice Amount"><a href="index.php?option=COM_VIEW_UTILITY_RENTAL_INVOICES&rid=<?php echo $bilamt['utility_rental_invoice_id']; ?>&table=utility_rental_invoices&title=Utility Rental Invoice" target="_blank" class="text-primary text-start">I &nbsp; : <?php echo customeummarmonyformateifine($utilinv['total_amount']); ?></a></p>
                          <p class="d-block text-danger text-start mb-0" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Due Amount">D : <?php echo customeummarmonyformateifine($utilinv['pending_amount']); ?></p>
                      <?php } ?>
                      </td>
                    <?php } ?>
                 </tr>
             <?php $mk++;} ?>
            </tbody>
        </table>
        <?php  }else{ ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Tenant Invoice Reports</p>   
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>   
                </div>
            <?php } ?>
    </div>
</div>



                    