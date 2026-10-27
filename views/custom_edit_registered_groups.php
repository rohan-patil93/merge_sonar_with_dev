
<?php 
if(isset($_POST['submit'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertcompanysubscription&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
    ?>
    <script type="text/javascript">
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
    </script>
    <?php
}
$getrid =  encodeId($_GET['Rid']);
$getrid = explode(':', $getrid);
$rid = $getrid['1'];
$url = API."?mode=getsubscriptionusers&rid=".$rid;
$subscripusers = callAPI($url);
$subscriptusers = json_decode($subscripusers,1);
$applicationuser = $subscriptusers['application_user'];
$company_subscription_plans = $subscriptusers['company_subscription_plans'];
$subscription_plans = $subscriptusers['subscription_plans'];
$subscriptiontypeunit = $subscriptusers['subscriptiontypeunit'];
$module_setup = $subscriptusers['module_setup'];
$company_subscription_plan_licenses = $subscriptusers['company_subscription_plan_licenses'];
//print_r($company_subscription_plan_licenses);


// echo "<pre>";print_r($response1);
// echo "</pre>";
?>
<script type="text/javascript">
    function addSubscriptionPlans(){
        var subscription_group_id = $("#subscription_group_id").val();
        jQuery.ajax({
            url: "views/custom_subscription_plans_popup.php",
            data: 'subscription_group_id='+subscription_group_id,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            },
            error:function (){}
        });         
    } 
    function checkUsageTreshold(){
        if($("#usagetreshold").is(':checked')){
            $("#usagetresholddays").show('slow');
        }else{
         $("#usagetresholddays").hide('slow');
     }
 }

 function addMoreField(){
    var htmlRows = '';
    var cnt = $(".fieldnamecnt").length+1;
    htmlRows += '<tr id="deleterow'+cnt+'"><td><input class="form-control fieldnamecnt" type="text" name="fieldname[]"></td><td><input class="form-control" type="text" name="fieldvalue[]"></td><td><i class="ti-trash" onclick="deleteRow('+cnt+')"></i> </td></tr>';  
    $('#appendfield').append(htmlRows);
}
function deleteRow(row){
    Swal.fire({
      title: 'Do You Want To Delete The Field?',
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: 'Yes',
      denyButtonText: `No`,
  }).then((result) => {
      if (result.isConfirmed) {
       $('#deleterow'+row).remove();
       tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');       
   }
})

}
function doftDeleteRow(id){
    Swal.fire({
      title: 'Do You Want To Delete The Product?',
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: 'Yes',
      denyButtonText: `No`,
  }).then((result) => {
      if (result.isConfirmed) {
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=softdeletedrecord&id='+id+'&table=company_subscription_plans',
            type: "POST",
            success:function(data){
                console.log(data);
                $('#softdeleteid'+id).remove();
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
            },
            error:function (){}
        }); 
    }
})


}


function chekTotalAmount(id=""){
    var agreeduserlimit = 0;
    var agreedprice = 0;
    var discount_prices = 0;
    var standard_price = $("#standard_price_"+id).val();
    var discount_price = $("#discount_price_"+id).val();
    var aggredpricetotal = parseInt(standard_price)-parseInt(discount_price);
    $("#agreed_price_"+id).val(isNaN(aggredpricetotal)?standard_price:aggredpricetotal);    
    $(".discount_prices").each(function(index) {
        discount_prices =discount_prices+ parseFloat($(this).val()); 
    });
    // $(".agreeduserlimit").each(function(index) {
    //     agreeduserlimit =agreeduserlimit+ parseFloat($(this).val()); 
    // });
    $(".agreedprice").each(function(index) {
        agreedprice = agreedprice+parseFloat($(this).val()); 
    });
    //$("#totalactualuser").text(agreeduserlimit);
    console.log(discount_prices);
    $("#totaldiscount_prices").text("Rs."+discount_prices);
    $("#totalactualprice").text("Rs."+agreedprice);
    $("#totalpricesubscription").text("Rs."+agreedprice);

}

function AdditionalSubscription(company_subscr_id){
    Swal.fire({
      title: 'Additional Subscription Same Price',
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: 'Yes',
      denyButtonText: `No`,
  }).then((result) => {
      if (result.isConfirmed){  
        var agreed_price= $("#agreed_price_"+company_subscr_id).val(); 
        var discount_price= $("#discount_price_"+company_subscr_id).val(); 
        $('#discount_price_'+company_subscr_id).attr('readonly', true); 
        jQuery.ajax({
            url: "views/custom_insert_additional_subscription_popup.php",
            data: 'company_subscr_id='+company_subscr_id+"&agreed_price="+agreed_price+"&discount_price="+discount_price,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            }
        });    
    }else{
        addSubscriptionPlans();
    }
})
}

function insertPaymentSubScription(company_subscr_id){
    var subscription_group_id = $("#subscription_group_id").val();
        var post = $("#insertpaymentsubscrifrm").serialize();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertpaymentsubscription&'+post,
        success:function(data){
            console.log(data);
            $("#popup_users").text('');
            $("#popup_price").text('');
            $("#payment_id_popup").val('');   
            $('#modal_payment_details').modal("hide");
        }
    });   
}

function acceptPaymentSubscription(id,userd,price){
     $("#modal_payment_details").modal({
        backdrop: 'static',
        keyboard: false
    });
    $("#popup_users").text(userd);
    $("#popup_price").text(price);
    $("#payment_id_popup").val(id);
    //$('#dynamic_popupxxl').html(data);
    //$('#kt_modal_dynamic_popupxxl').modal("show");
    $('#modal_payment_details').modal("show");
}
function refrashpage(){
  location.reload();
}


</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<div class="card card-custom card-sticky">
			<div class="card-header">
				<h5>Subscription Plans</h5>
				<div class="card-header-right">
					<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
						<i class="icofont icofont-reply"></i>
						Back
					</a>
				</div>
			</div>
			<div class="card-block">
                <form method="post">
                    <div class="card-body">
                       <div class="card">	
                         <div class="row">
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Customer</h5>
                                    </div>
                                    <div class="card-body">
                                        <input type="hidden" id="subscription_group_id"  name="subscription_group_id" value="<?php echo $rid; ?>">
                                        <div class="align-middle">
                                            <?php if($applicationuser['user_photo']) {?>
                                                <img src="uploads/all/<?php echo $applicationuser['user_photo']; ?>"  class="img-radius img-50 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
                                            <?php } else { ?>
                                                <span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($applicationuser['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <h6><?php echo $applicationuser['display_name']; ?></h6>
                                                <p class="text-muted m-b-0"><?php echo $applicationuser['user_email']; ?></p>
                                            </div>
                                        </div>
                                        <div class="alert alert-primary background-primary alert-dismissible mt-4">
                                            <h4 class="f-w-600">This is a very important privacy notice!</h4>
                                            <p class="mb-0">Writing headlines for blog posts is much science and probably cool audience.
                                                <a href="#">Learn More</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>









                                <div class="card">
                                    <div class="card-header">
                                        <h5>Products</h5>
                                        <div class="card-header-right">
                                            <button type="button" class="btn btn-primary waves-effect" onclick="addSubscriptionPlans()">Add Product</button>

                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive" id="productajax">
                                            <?php include("views/custom_insert_subscription_product_table.php"); ?>

                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h5>Invoices</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if(count((array)$company_subscription_plan_licenses) !='0'){ ?>
                                            <table class="table table-hover m-b-0">
                                                <thead>
                                                    <tr>
                                                        <th><span class="d-block">#</span></th>
                                                        <th><span class="d-block">Request Date</span></th>
                                                        <th><span class="d-block">User</span></th>
                                                        <th><span class="d-block">Amount</span></th>
                                                        <th><span class="d-block">Payment</span></th>
                                                        <th><span class="d-block">Status</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i=1; foreach($company_subscription_plan_licenses as $subscriptuser){?>
                                                        <tr>
                                                            <td><?php echo $i; ?></td>
                                                            <td><?php echo fromDate($subscriptuser['request_date']); ?></td>
                                                            <td><?php echo $subscriptuser['added_license_users']!='0'?'<label class="form-label label label-md label-success">'.$subscriptuser['added_license_users'].'</label>':'<label class="form-label label label-md label-danger">'.$subscriptuser['deletd_license_users'].'</label>'; ?></td> 
                                                            <td><?php echo $subscriptuser['price']; ?></td>
                                                            <td><?php echo $subscriptuser['payment_date'] !=""?fromDate($subscriptuser['payment_date']):"- "; ?></td>
                                                           


                                                            <td>
<input type="checkbox" class="js-switch" <?php echo $subscriptuser['payment_type']=="Y"?"checked disabled":"";?> onchange="acceptPaymentSubscription('<?php echo $subscriptuser['id']; ?>','<?php echo $subscriptuser['added_license_users']; ?>','<?php echo $subscriptuser['price']; ?>')"   /></td>
                                                        </tr>
                                                        <?php $i++; } ?>
                                                    </tbody>
                                                </table>
                                            <?php }else{ ?>

                                                <div class="card-px text-center py-20 m-t-30">
                                                    <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No Invoice </p>   
                                                    <div class="text-center px-4">
                                                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                  </div>   
                                              </div>
                                          <?php } ?>

                                      </div>
                                  </div>




                                  <div class="card">
                                    <div class="card-header">
                                        <h5>Advanced Options</h5>
                                        <h6 class="mt-5">Custom fields 
                                            <i class="ti-info ms-1 fs-7 color-default text-white border-radius p-1" data-bs-toggle="tooltip" data-placement="bottom" title="Add custom field to the billing invoice"></i>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover m-b-0" id="appendfield">
                                                <thead>
                                                    <tr>
                                                        <th>Field Name</th>
                                                        <th>Field Value</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   <?php $custom_fields = json_decode($applicationuser['custom_fields'],1);// print_r($custom_fields);
                                                   foreach($custom_fields as $kk=>$custoval){
                                                     ?> 
                                                     <tr id="deleterow1">
                                                        <td>
                                                            <input class="form-control fieldnamecnt" type="text" name="fieldname[]" value="<?php echo $kk; ?>">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="text" name="fieldvalue[]" value="<?php echo $custoval; ?>">
                                                        </td>
                                                        <td>
                                                            <i class="ti-trash text-danger" onclick="deleteRow('1')"></i>

                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-primary waves-effect mt-3" onclick="addMoreField()">Add Field</button>
                                    </div>
                                </div>
                                <div class="card-header">
                                    <h6 class="mt-2">Invoice footer 
                                        <i class="ti-info ms-1 fs-7 color-default text-white border-radius p-1" data-bs-toggle="tooltip" data-placement="bottom" title="Add custom field to the billing invoice"></i>
                                    </h6>
                                    <textarea class="form-control" name="invoice_footer" style="height:80px"><?php echo $applicationuser['invoice_footer']; ?></textarea>
                                    <div class="alert alert-primary background-primary alert-dismissible mt-4">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="checkbox-fade fade-in-warning  d-block">
                                                    <label class="form-label  d-block mb-0">
                                                        <input type="checkbox" name="usage_trash_holder_type" id="usagetreshold" value="Y" onchange="checkUsageTreshold()" <?php echo $applicationuser['usage_trash_holder_type']=='Y'?"checked":""; ?>>
                                                        <span class="cr mt-0">
                                                          <i class="cr-icon icofont icofont-ui-check txt-warning"></i>
                                                      </span>
                                                      <p class="mb-0"> Usage Trash Holder <i class="ti-info ms-1 fs-7 bg-white text-primary border-radius p-1" data-bs-toggle="tooltip" data-placement="bottom" title="Add custom field to the billing invoice"></i>
                                                      </p>
                                                  </label>
                                              </div> 
                                          </div>
                                          <div class="col-lg-5" id="usagetresholddays" <?php echo $applicationuser['usage_trash_holder_type']=='N'?"style='display:none'":"";?>>
                                            <div class="row align-items-center">
                                                <label class="form-label col-lg-3 f-w-700 f-14">Days</label>
                                                <div class="col-lg-7">
                                                    <input type="number" name="usage_trash_holder_day"  class="form-control" value="<?php echo $applicationuser['usage_trash_holder_day']; ?>" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-primary background-primary alert-dismissible mt-4">

                                    <div class="checkbox-fade fade-in-warning  d-block">
                                        <label class="form-label  d-block">
                                            <input type="checkbox" name="pro_rata_billing" id="usagetreshold" value="Y" <?php echo $applicationuser['pro_rata_billing']=='Y'?"checked":""; ?>>
                                            <span class="cr mt-0">
                                              <i class="cr-icon icofont icofont-ui-check txt-warning"></i>
                                          </span>
                                          <p> Pro-Rata billing<i class="ti-info ms-1 fs-7 bg-white text-primary border-radius p-1" data-bs-toggle="tooltip" data-placement="bottom" title="Add custom field to the billing invoice"></i>
                                          </p>
                                      </label>
                                  </div>
                                  <div class="border-checkbox-section">
                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                        <label class="form-label border-checkbox-label" for="checkbox2">Allow pro-rated billing when treshold usage is paid before end of billing cycle.</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Summary</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="mt-5 f-w-600">Customer Details</h6>
                            <div class="d-block mt-3">
                                <a class="f-w-600" href=""><?php echo $applicationuser['display_name']; ?></a>
                                <div class="badge label-success ms-2">Active</div>
                            </div>
                            <div class="d-block border-bottom pb-4">
                                <a class="f-w-600" href=""><?php echo $applicationuser['user_email']; ?></a>
                            </div>
                            <div class="d-block border-bottom pb-4 mt-3">
                                <div class="badge bg-light text-primary ms-2">Basic Bundle</div>
                                <span class="d-inline-block mr-2 f-w-600" id="totalpricesubscription"><?php echo "Rs.".$totalactualprice; ?></span>
                            </div>
                            <div class="d-block mt-3">
                                <h6 class="mt-5 f-w-600">Payment Details</h6>
                                <div class="d-block mt-3">
                                    <span class="d-inline-block mr-2 f-w-600">Master Card</span>
                                    <img src="../files/assets/images/mastercard.svg" alt="" class="h-25px img-50">
                                </div>
                                <div class="d-block mt-2">
                                    <span class="d-inline-block mr-2 f-w-600">Expires Dec 2025</span>
                                </div>
                                <button type="submit" class="btn btn-primary waves-effect mt-4"  name="submit">Create Subscription</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pradeep code end here -->
        </div>	
    </div>
</form>
</div>
</div>
</div>
</div>
</div>


<!-- 

<div id="kt_modal_dynamic_popupxxl" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl"  style="max-width: 98% !important;" >
        <div class="modal-content"  id="dynamic_popupxxl" style="border-radius: 20px;" style="border:none;">

        </div>
    </div>
</div>
 -->


<div class="modal fade" id="modal_payment_details" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" style="max-width: 98% !important;" >
    <div class="modal-content" id="" style="border-radius: 20px;" style="border:none;">
      <div class="modal-header">
        <h5 class="modal-title">Payment Details</h5>
        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
      </div>   
        <form class="form" id="insertpaymentsubscrifrm">  
            <div class="card-body">
                <div class="row" >
                    <div class="col-lg-3 form-group text-center">
                        <label class="col-form-label d-block f-w-700">Users</label>
                        <span id="popup_users"></span>
                    </div>
                    <div class="col-lg-3 form-group text-center"> 
                            <label   class="col-form-label d-block f-w-700">Price</label>
                            <span id="popup_price"></span>
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="example-date-input" class="col-4 col-form-label f-w-700">Payment Date</label>
                        <input class="form-control datepicker" type="text" name="payment_date"  id="popup_paymentdate">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="payment_id_popup" id="payment_id_popup" value="">
                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" onclick="refrashpage()">Cancel</button>
                <button type="button" class="btn btn-primary " onclick="insertPaymentSubScription()" >Submit</button>
            </div>
        </form>
    </div>
  </div>
</div>