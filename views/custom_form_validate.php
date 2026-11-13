<?php  $_SESSION['apply_js']['multiselest'] = "1"; 

$_SESSION['apply_js']['from-validation'] = "1";
?>

<form class="kt-form kt-form--label-right" name="myForm" id="form-validation" action="" method="post" enctype="multipart/form-data">
	<div class="kt-portlet">
		<div class="card">
			<div class="card-header table-card-header">
				<h5><?php echo __('Goods Receipt Notes'); ?></h5>
				<div class="card-header-right table-card-header m-r-50">

					&nbsp;  &nbsp; 
					<!-- <input type="hidden" name="status_values" id="status_values" value=""> -->
					<input type="hidden" name="status_values" id="status_values" value="2">
					<input type="hidden" name="status_old_value" id="status_old_value" value="">
				</div>
			</div>

			<div class="mt-3 mb-3">
			</div>
			<div style="">

				<script type="text/javascript">
					function selectchange(field, key, table){
						var option = 'COM_INSERT_GOODS_RECEIPT_NOTES';
						var main_table = document.getElementById("status_values").value;	
						if(field == 'party_id'){
				if(table == 'application_users'){ //alert("T");
				if(key == '0'){
					document.getElementById('party_name').value="";
					document.getElementById('party_email').value="";
					document.getElementById('party_phone').value="";
					document.getElementById('party_tax_number').value="";
					document.getElementById('party_address').value="";
					document.getElementById('party_ship_adderss').value="";
					document.getElementById('cost_center').value="";
					document.getElementById('party_payment_terms').value="0";
				}else{
					$.ajax({
						url: "ajax.php",
						data:'page=userDetails&user_id='+key,
						type: "POST",
						success:function(data){
							console.log(data);
							var obj = $.parseJSON(data);
					            //alert(obj.payment_terms);
					            document.getElementById('party_name').value=obj.display_name;
					            document.getElementById('party_email').value=obj.email;
					            document.getElementById('party_phone').value=obj.phone;
					            document.getElementById('party_tax_number').value=obj.gstin;
					            document.getElementById('currency_id').value=obj.currency_id;
					            document.getElementById('cost_center').value=obj.cost_center;
					            document.getElementById('party_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
					            document.getElementById('party_ship_adderss').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
					            document.getElementById("party_payment_terms").value = obj.payment_terms;

					            duedate();
					        },
					        error:function (){}
					    })
				}
			}
				if(table == 'vendors'){ //alert("V");
				$.ajax({
					url: "ajax.php",
					data:'page=vendor&ven_id='+key,
					type: "POST",
					success:function(data){
						console.log(data);
						var obj = $.parseJSON(data);
						document.getElementById('party_name').value=obj.display_name;
						document.getElementById('party_email').value=obj.email;
						document.getElementById('party_phone').value=obj.phone;
						document.getElementById('party_tax_number').value=obj.gstin;
						document.getElementById('currency_id').value=obj.currency_id;
						document.getElementById('party_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
						document.getElementById('party_ship_adderss').value=obj.shipping_address+','+obj.shipping_city+','+obj.shipping_state+','+obj.shipping_post_code;
						document.getElementById("party_payment_terms").value = obj.payment_terms;
					},
					error:function (){}
				});	
			}
				if(table == 'customers'){ //alert("V");
				$.ajax({
					url: "ajax.php",
					data:'page=bill_customer&cus_id='+key,
					type: "POST",
					success:function(data){
						console.log(data);
						var obj = $.parseJSON(data);
						document.getElementById('party_name').value=obj.display_name;
						document.getElementById('party_email').value=obj.email;
						document.getElementById('party_phone').value=obj.phone;
						document.getElementById('party_tax_number').value=obj.gstin;
						if(obj.currency_id !=null){
							document.getElementById('currency_id').value=obj.currency_id;
						}
				            //document.getElementById('currency_id').value=obj.currency_id;
				            document.getElementById('party_address').value=obj.billing_address+','+obj.billing_city+','+obj.billing_state+','+obj.billing_post_code;
				            document.getElementById('party_ship_adderss').value=obj.shipping_address+','+obj.shipping_city+','+obj.shipping_state+','+obj.shipping_post_code;
				            if(obj.payment_terms !=null){
				            	document.getElementById("party_payment_terms").value = obj.payment_terms;
				            	//$("#party_payment_terms").select2("val", obj.payment_terms);
				            	//$("#party_payment_terms").select2().select2('val', obj.payment_terms);
				            }
				        },
				        error:function (){}
				    });	
			}
			if(main_table == 'sales_invoices'){ 
				$.ajax({
					url: "ajax.php",
					data:'page=dynmictabledata&table=sales_orders&field=id&value=transaction_number&rid=party_id&fid='+key,
					type: "POST",
					success:function(data){
						console.log(data);
						$("#grade").html("<select class='form-control' name='pay_template' aria-label='<?php echo __('Select'); ?>'>"+data+"</select>");
					},
					error:function (){}
				});	
			}
		}
		if(field == 'ref_id'){
				//if(table == 'application_users'){ 
					if($("#ref_type").val() !=""){
						table = $("#ref_type").val();
					}
					var trandate = $("#transaction_date").val();
					window.location.href = 'index.php?option='+option+'&table='+table+'&Rid='+key+'&TD='+trandate;
				//}
			}
			if(field == 'ref_type'){ //alert("V");
			var trandate = $("#transaction_date").val();
			$.ajax({
				url: "ajax.php",
				data:'page=getdynamicreftype&field='+field+'&key='+key,
				type: "POST",
				success:function(data){
					var fun ="selectchange('ref_id',this.value,'"+key+"')";
					$(".refkey_ref_no").html("<select class='form-control' name='ref_id' data-tablename='ref_id'  data-fieldsname='ref_id' data-pagename='comman_form' name='ref_id' id='ref_id' onchange="+fun+" aria-label='<?php echo __('Select'); ?>'>"+data+"</select>");
					window.location.href = 'index.php?option='+option+'&table='+key+'&Rid=0&TD='+trandate;
				}
			}); 
		}
		if(field =="currency_id"){
			var originator_currency_id = $("#originator_currency_id").val();
			var trandate = $("#transaction_date").val();
			$.ajax({
				url: "ajax.php",
				data:'page=currencyconversionrate&originator_currency_id='+originator_currency_id+'&currency_id='+key+'&trandate='+trandate,
				type: "POST",
				success:function(data){
					console.log(data);
					var currncyrate = JSON.parse(data);
					$("#currency_rate").val(currncyrate.rate)
					changeexchange();
				}
			}); 

		}
		if(field =="payment_terms"){
			var trandate = $("#transaction_date").val();
			$.ajax({
				url: "ajax.php",
				data:'page=getleavingdate&effective_date='+trandate+'&numberofdays='+key,
				type: "POST",
				success:function(date){
					$("#due_date").val(date);
				}
			}); 
		}
		if(field =="place_of_supply"){
			$.ajax({
				url: "ajax.php",
				data:'page=getselectedlist&field=id&id='+key+'&table_name=cities',
				type: "POST",
				success:function(data){
					var statedetails = JSON.parse(data);
					$("#supplier_state_id").val(statedetails[0].state_id);
				}
			}); 
		}
		if(field =="transaction_location_id"){
			$.ajax({
				url: "ajax.php",
				data:'page=gettransactionwarehouse&field=id&id='+key,
				type: "POST",
				success:function(data){
			        	//var statedetails = JSON.parse(data);
			        	$("#transaction_warehouse_id").html(data);
			        }
			    }); 
		}
		if(field =="originator"){
			onchangedate();
			}
		}	
		function amountLimitCheck(grandtotal){
		//$(".btndisableck").removeAttr('disabled');
		  	//$(".btndisableck").attr('disabled','disabled');
		  	$("#btndisableck1").prop("disabled", false);
		  	$("#btndisableck12").prop("disabled", false);

		  }
		  function onchangedate(){
		  	var oid = $("#originator").val();
		  	var trandate = $("#transaction_date").val();
		  	var option = 'COM_INSERT_GOODS_RECEIPT_NOTES';
		  	var url = "";
		  	url +='index.php?option='+option;

		  	window.location.href = url+'&TD='+trandate+'&oid='+oid;
		  }
		</script>
		<div class="card-body">
			<input type="hidden" name="" value="goods_receipt_notes " id="ajax_table_name">
			 <div class="form-group row"> 
				<label id="label_transaction_number" for="transaction_number" class="col-lg-2 col-form-label text-lg-right "><?php echo __('Transaction Number'); ?></label>
				<div class="col-lg-3 " style="">
					<input class="form-control" type="text" readonly="" autocomplete="off" value="" id="transaction_number" name="transaction_number" aria-label="<?php echo __('Transaction Number'); ?>">
					<span class="messages"></span>
				</div>
		<div class="col-lg-1"></div>  
				<label id="label_transaction_date" for="transaction_date" class="col-lg-2 col-form-label text-lg-right "><?php echo __('Transaction Date'); ?>&nbsp;<span class="text-danger">*</span></label>
				<div class="col-lg-3" style="">
					<div class="">
						<input class="form-control datepicker hasDatepicker" type="text" value="29-08-2023" dateformat="d M y" id="transaction_date" name="transaction_date"  required="" aria-label="<?php echo __('Transaction Date'); ?>"><button type="button" class="ui-datepicker-trigger" aria-label="<?php echo __('Choose Transaction Date'); ?>"><i class="ti-calendar text-dark f-20 f-w-800 m-r-10" aria-hidden="true"></i></button>
				</div>
				<span class="messages"></span>
			</div>
		</div> 
		<div class="form-group row"> 
			<label id="label_party_id" for="party_id" class="col-lg-2 col-form-label text-lg-right "><?php echo __('Party'); ?>&nbsp;<span class="text-danger">*</span></label>
			<div class="col-lg-3 " style="">
				<div class="w-full refkey_party_id form-validation-class">
					
					<select class="form-select js-example-basic-single" onchange="selectchange('party_id',this.value,'vendors')" name="party_id" id="party_id" aria-label="<?php echo __('Party'); ?>">
						<option value="" data-select2-id="2"> <?php echo __('Select Any one'); ?> </option>
						<option value="4"><?php echo __('Infisuite Hospitals'); ?></option>
						<option value="1"><?php echo __('Infisuite Hotel'); ?></option>
						<option value="3"><?php echo __('Infisuite Schools'); ?></option>
						<option value="2"><?php echo __('Infisuite Trader'); ?></option>
					</select>
					<span class="messages"></span>


				</div>
				
			</div>
			<div class="col-lg-1"></div>  
			<label id="label_ref_id" for="ref_id" class="col-lg-2 col-form-label text-lg-right "><?php echo __('Reference Number'); ?>&nbsp;<span class="text-danger">*</span></label>
			<div class="col-lg-3 form-validation-class" style="">
				<div class="w-full refkey_ref_id">
					<select class="form-select js-example-basic-single select2-hidden-accessible" onchange="selectchange('ref_id',this.value,'purchase_orders')" name="ref_id" id="ref_id" aria-label="<?php echo __('Reference Number'); ?>">
						<option value="" data-select2-id="4"> <?php echo __('Select Any one'); ?> </option>
						<option value="1">PO00003</option>
						<option value="2">PO00004</option>
					</select>
				</div>
				<span class="messages"></span>
			</div> 
		</div> 
</div>
</div>
</div>
</div>

<div class="mb-3 row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
				<button type="submit" class="btn btn-primary m-b-0"><?php echo __('Submit'); ?></button>
				<button id="btndisableck12" class="btn btn-primary mr-2"   name="submit_value" value="exit" <?php echo $disable; ?>><i class="la la-check" aria-hidden="true"></i><?php echo __('Save & exit'); ?></button>
                                    <button  class="btn btn-primary mr-2 " id="btndisableck1"  name="submit_value" value="continue" <?php echo $disable; ?>><i class="la la-check-circle" aria-hidden="true"></i><?php echo __('Save & continue'); ?></button>
			</div>
		</div>

</form>

