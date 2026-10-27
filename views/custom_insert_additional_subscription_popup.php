<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

 	 $url = API."?mode=getsubscriptionusers&additionalsubscription=Y&company_subscr_id=".$_POST['company_subscr_id'];
    $subscripusers = callAPI($url);
    $subscriptusers = json_decode($subscripusers,1);
    $company_subscription_plans = $subscriptusers['company_subscription_plans'];
     $subscription_plans = $subscriptusers['subscription_plans'];
    $subscriptiontypeunit = $subscriptusers['subscriptiontypeunit'];
$additional_license_lot =$subscription_plans['additional_license_lot'];
$standard_price = $_POST['agreed_price'];

if($company_subscription_plans['standard_user_limit'] !=0 AND $company_subscription_plans['standard_user_limit'] !=""){
	$additional_license_lot_price=round($standard_price/$company_subscription_plans['standard_user_limit']); 
}else{
	$additional_license_lot_price = round($standard_price);
}
?>
<script type="text/javascript">
	function additionalLicenseValidation(){
		var additional_license_lot_popup = $("#additional_license_lot_popup").val();
		var additional_license_lot_limit_popup= $("#additional_license_lot_limit_popup").val();
		var additional_license_lot_price_popup= $("#additional_license_lot_price_popup").val();
		var standard_user_limit_popup= $("#standard_user_limit_popup").val();
		var standard_price_popup= $("#standard_price_popup").val();
		
		var thislicenstotal = parseInt(additional_license_lot_popup) + parseInt(standard_user_limit_popup);
		console.log(thislicenstotal);
		if(thislicenstotal % additional_license_lot_limit_popup){
			Swal.fire({
			  icon: 'error',
			  title: 'Oops...',
			  text: 'Something went wrong!'
			})
			$("#additional_license_lot_price").val('');
			$("#total_license_lot_price").val('');
		}else{
			//console.log("ok - "+thislicenstotal);
			var aditional_ll_price = parseInt(additional_license_lot_price_popup)*parseInt(additional_license_lot_popup);
			//var total_aditional_ll_price = parseInt(aditional_ll_price)+parseInt(standard_price_popup);
			

			$("#additional_license_lot_price").val(isNaN(aditional_ll_price)?'0':aditional_ll_price);
			$("#total_license_lot_price").val(isNaN(aditional_ll_price)?'0':aditional_ll_price);
			$("#total_advancetotalprice").val(isNaN(aditional_ll_price)?'0':aditional_ll_price);

		//	$("#total_license_lot_price").val(isNaN(total_aditional_ll_price)?'0':total_aditional_ll_price);
		//	$("#total_advancetotalprice").val(isNaN(total_aditional_ll_price)?'0':total_aditional_ll_price);
		}
	}
	function submitFormSubscribdditionserv(){
		var subscription_group_id = $("#subscription_group_id").val();
		var post = $("#submitformadditionalservice").serialize();
		 $.ajax({
	        url: "ajax.php",
	        type: "POST",
	        data: 'page=insertadditionalserviceslicense&'+post,
	        success: function(response) {
	        	console.log(response);
	          	$('#kt_modal_KTDatatable_remotem').modal('hide');
	          	 tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
	      //       $.ajax({
			    //     url: "views/custom_insert_subscription_product_table.php",
			    //     type: "POST",
			    //     data: 'ajaxsub=Y&subscription_group_id='+subscription_group_id,
			    //     success: function(response) {
			    //     	$("#productajax").html(response);	
			    //     	console.log(response);
			             	
			    //     }
			    // }); 	
	        }
	    });
	}

function popDiscount(){
	var discount = $("#additional_license_lot_discount").val();
	var totallotprice = $("#total_advancetotalprice").val();
	var total = parseFloat(totallotprice)-parseFloat(discount);
	$("#total_license_lot_price").val(total);
}





</script>
<div class="modal-header">
	<h5 class="modal-title">Subscription Plans</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="submitformadditionalservice">

<div class="modal-body">
			<div class="row mb-2">
				<div class="col-lg-3 form-group text-center">
					<label class="form-label f-w-700">Plan Name</label>
					<p><?php echo $subscription_plans['plan_name'];?></p>
				</div>
				<div class="col-lg-2 form-group text-center">
					<label class="form-label f-w-700">Plan Price</label>
					<p><?php echo $standard_price;?></p>
				</div>
				<div class="col-lg-4 form-group text-center">
					<label class="form-label f-w-700">Additional Lic. Lot limit</label>
					<p><?php echo $additional_license_lot;?></p>
				</div>
				<div class="col-lg-3 form-group text-center">
					<label class="form-label f-w-700">Additi Lot Price</label>
					<p><?php echo $additional_license_lot_price; ?></p>
				</div>
				<div class="col-lg-3 form-group">
					<label class="form-label ">Additi License Users</label>
				 	<input class="form-control text-right" type="text" name="additional_license_lot_popup" id="additional_license_lot_popup" value="" onblur="additionalLicenseValidation()"> 
				</div>
				<div class="col-lg-3 form-group">
					<label class="form-label">Additi License Price</label>
				 	<input class="form-control text-right" type="text" name="additional_license_lot_price" id="additional_license_lot_price" value="0" readonly=""> 
				</div>
				<div class="col-lg-3 form-group">
					<label class="form-label">Discount</label>
				 	<input class="form-control text-right" type="text" name="additional_license_lot_discount" id="additional_license_lot_discount" value="0" onblur="popDiscount()"> 
				</div>
				<div class="col-lg-3 form-group">
					<label class="form-label">Total Price</label>

				 	<input class="form-control text-right" type="text" name="total_license_lot_price" id="total_license_lot_price" value="0" readonly=""> 
				</div>
			</div>

</div>
<div class="modal-footer">
	<input type="hidden" name="subscription_type"  class="form-control" value="<?php echo $company_subscription_plans['subscription_type']; ?>" >
	<input type="hidden" name="discount_price"  class="form-control" value="<?php echo $_POST['discount_price']; ?>" >

	<input class="form-control text-right" type="hidden" name="total_advancetotalprice" id="total_advancetotalprice" value="0" readonly=""> 
	<input type="hidden" name="company_subscr_id_popup" id="company_subscr_id_popup" value="<?php echo $_POST['company_subscr_id'];?>" >
	<input type="hidden" name="standard_price_popup" id="standard_price_popup" value="<?php echo $standard_price;?>" >
	<input type="hidden" name="additional_license_lot_limit_popup" id="additional_license_lot_limit_popup" value="<?php echo $additional_license_lot; ?>" >
	<input type="hidden" name="additional_license_lot_price_popup" id="additional_license_lot_price_popup" value="<?php echo $additional_license_lot_price; ?>" >
	<input type="hidden" name="standard_user_limit_popup" id="standard_user_limit_popup" value="<?php echo $company_subscription_plans['standard_user_limit'];?>" >
	<button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary mr-2" onclick="submitFormSubscribdditionserv()" >Submit</button>
</div>
	</form> 
