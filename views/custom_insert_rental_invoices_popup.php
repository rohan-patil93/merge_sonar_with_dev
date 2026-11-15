<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
$url1 = API."?mode=gettenantproperty&type=".$_POST['type']."&valu=".$_POST['valu'];  
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$customers = $result['customers'];
//print_r($_POST);
?>
<script type="text/javascript">
function getTenantDetails(type,valu){
	// if(type == 'getproperty_tenant_billings_id'){
	// 	var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
	//     $("#ajaxbillingtable").html(loader);
	// }
	if(valu !=""){
		$.ajax({
			url: "ajax.php",
			data:'page=gettenantproperty&type='+type+'&valu='+valu+'&bill_type=<?php echo $_POST['bill_type']; ?>',
			type: "POST",
			success:function(date){
				if(type == 'getcustid'){
					$("#property_tenant_detail_id").html(date);
					$("#property_tenant_billings_id").html('<option value=""><?php echo __('Select Any One'); ?></option>');
					$("#ajaxbillingtable").html('');
					$("#showfooter").css("pointer-events","none");
					$("#showfooter").css("opacity","0.7");
				}else if(type == 'getproperty_tenant_detail_id'){
					$("#property_tenant_billings_id").html(date);
					$("#ajaxbillingtable").html('');
				}else if(type == 'getproperty_tenant_billings_id'){
					$("#ajaxbillingtable").html(date);
					$("#showfooter").css("pointer-events","all");
					$("#showfooter").css("opacity","1");
					$( ".datepicker" ).datepicker({
						showOn: "both",
						buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
						dateFormat: date_format,
						setDate: new Date(),
						changeMonth: true,
						changeYear: true,
        			}).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

					var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch-utility-gst'));
					elem.forEach(function(html) {
						var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'small' });
					});
				}
			}
		});
	}else{
		$("#ajaxbillingtable").html('');
		$("#showfooter").css("pointer-events","none");
		$("#showfooter").css("opacity","0.7");
	}
}
function generateTenantBill(type){
	var property_tenant_billing_id = $("#property_tenant_billing_idd").val();
	submitTenantBillss(property_tenant_billing_id,type,'new_','<?php echo $_POST['bill_type']; ?>');
}

function submitTenantBillss(property_tenant_billing_id,type,extrform="",billtype=""){
	console.log("#form_id_"+extrform+property_tenant_billing_id);
	console.log("#form_id_"+extrform+' ---- '+property_tenant_billing_id);
	var emtycheck = 0;
	$('.checkamountnotepmty_'+extrform+property_tenant_billing_id).each(function(){
		//if($(this).val() =='' || $(this).val() =='0'){
		if($(this).val() ==''){
			emtycheck = 1;
		}
	});

	$('[id^="rentinvpopup_parent_rent_"]').each(function() {
	    var id = $(this).attr('id');
	    var value = parseFloat($(this).val());
	    if(value =='' || value =='0'){
	    	emtycheck = 1;
	    }
	});

	if(emtycheck == 0 || type == 'S'){
		$(".loading_popup_btn").text(<?php echo json_encode(__('Please Wait........')); ?>);
		$(".loading_popup_btn").addClass("disabled");
		var formdata = $("#form_id_"+extrform+property_tenant_billing_id).serialize();
		jQuery.ajax({
			url: "ajax.php",
			data:'page=updatepropertytenantbillings&type='+type+'&property_tenant_billing_id='+property_tenant_billing_id+'&bill_type='+billtype+'&'+formdata,
			type: "POST",
			success:function(res){
			console.log(res);
				var data = JSON.parse(res);
				console.log(data);
				var respon = data.result;
				if(respon.status =="fail"){
					swal("<?php echo ucwords($_SESSION['config']['name']); ?>",respon.message, "error");
				}else{
					/**/
					if(type =="S"){
						swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['3001']; ?>', "success");
						$('#kt_modal_dynamic_popup2').modal("hide");
					}else{
						$('#kt_modal_dynamic_popup2').modal("hide");
						console.log("rental_invoice_id === "+data.rental_invoice_id);
						console.log("rental_invoice_id === "+data.decode_rental_invoice_id);
						if(data.rental_invoice_id !=""){ 								
							swal({
								title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
								text: '<?php echo $_SESSION['error_message']['9080']; ?>',
								type: "warning",
								showCancelButton: true,
								confirmButtonClass: "btn-danger",
								confirmButtonText: <?php echo json_encode(__('Yes')); ?>,
								cancelButtonText: <?php echo json_encode(__('No')); ?>,
								closeOnConfirm: true,
								closeOnCancel: true
							},
							function(isConfirm) {
								<?php if($_POST['bill_type']=='U'){ ?>
									if(isConfirm){ 
										//window.location.href = "index?option=COM_INSERT_UTILITY_RENTAL_INVOICES&Rid="+data.decode_rental_invoice_id;
										window.location.href = "index?option=COM_VIEW_RENTAL_INVOICES&rid="+data.rental_invoice_id+"&table=utility_rental_invoices&title=Utility Invoice";
									}else{
										window.location.href = "index?option=COM_LIST_UTILITY_RENTAL_INVOICES";
									} 
								<?php }elseif($_POST['bill_type']=='AMC'){ ?>
									if(isConfirm){ 
										//window.location.href = "index?option=COM_INSERT_UTILITY_RENTAL_INVOICES&Rid="+data.decode_rental_invoice_id;
										window.location.href = "index?option=COM_VIEW_RENTAL_INVOICES&rid="+data.rental_invoice_id+"&table=utility_amc_rental_invoices&title=Utility AMC Invoice";
									}else{
										window.location.href = "index?option=COM_LIST_UTILITY_AMC_RENTAL_INVOICES";
									} 
								<?php }else{ ?>
									if(isConfirm){
										window.location.href = "index?option=COM_VIEW_RENTAL_INVOICES&rid="+data.rental_invoice_id+"&table=rental_invoices&title=Rental Invoice";
										//window.location.href = "index?option=COM_INSERT_RENTAL_INVOICES&Rid="+data.decode_rental_invoice_id;
									}else{
										window.location.href = "index?option=COM_LIST_RENTAL_INVOICES";
									}
								<?php } ?>
							});
						}else{
							swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['3001']; ?>', "success");
							$('#kt_modal_dynamic_popup2').modal("hide");
						}
					}
					/**/
				}
				//location.reload();
			},
			error:function (){}
		});
	}else{
		swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Utility Amount Is Required", "error");
	}
}

function rentalInvoicePopupCalculattion(ruid,parent_id){
	var rentinvpopup_qty = $("#rentinvpopup_qty_"+ruid).val();
	var rentinvpopup_rate = $("#rentinvpopup_rate_"+ruid).val();
	var rentinvpopup_condition = $("#rentinvpopup_condition_"+ruid).val();
	var calculate = 0;
	if(rentinvpopup_qty !="" && rentinvpopup_condition !=""  && rentinvpopup_rate !=""){
		if(rentinvpopup_condition == "="){
			calculate = parseFloat(rentinvpopup_rate);
		}else if(rentinvpopup_condition == "%"){
			calculate = (parseFloat(rentinvpopup_qty) / 100) * parseFloat(rentinvpopup_rate);
		}else{
			calculate = parseFloat(rentinvpopup_qty)+rentinvpopup_condition+parseFloat(rentinvpopup_rate);
		}
	}
	var returnvalue = Math.round(eval(calculate));
	$("#rentinvpopup_rent_"+ruid).val(returnvalue);
	var totalamt = 0;
	$(".rentinvpopup_child_rent_"+parent_id).each(function() {
		totalamt = parseFloat(totalamt)+parseFloat(this.value);
		console.log(this.value);
	});
	var total_percentage = percentage = 0
	console.log('without Checked - ', totalamt);
	if ($('#utility_gst_'+parent_id).is(':checked')) {
		$("#utility_gst_utility_text_"+parent_id).text(totalamt);
		$("#utility_gst_utility_"+parent_id).val(totalamt);
		
		var percentage = $('#utility_gst_percentage_'+parent_id).val();
		total_percentage = ((totalamt * percentage) / 100);
		totalamt = totalamt+total_percentage;
	    
	    $("#utility_gst_total_tax_text_"+parent_id).text(total_percentage+" ("+percentage+"%)");
	    $("#utility_gst_total_tax_"+parent_id).val(total_percentage.toFixed(2));
	} else {
		$("#utility_gst_utility_text_"+parent_id).text(totalamt.toFixed(2));
	    $("#utility_gst_total_tax_text_"+parent_id).text("0");
	}
	$("#rentinvpopup_parent_rent_"+parent_id).val(totalamt.toFixed(2));
}



</script>
<div class="modal-header">
	<h4 class="modal-title">
		<?php  
			if($_POST['bill_type'] =="U"){
				echo __('Utility Invoice');
			}elseif($_POST['bill_type'] =="AMC"){
				echo __('Utility AMC Invoice');
			}else{
				echo __('Rental Invoice');
			}
		?>
			
	</h4>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('Close'); ?>"></button>
</div>
<div class="modal-body m-l-50 m-r-50">

	<div class="row">
		<div class="form-group col-lg-4">
			<span class="col-lg-4 d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Tenant'); ?></span>
			<select aria-label="<?= htmlspecialchars(__('Cust id'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-select js-example-basic-single" name="cust_id" id="cust_id" onchange="getTenantDetails('getcustid',this.value)">
				<option value=""><?php echo __('Select Any One'); ?></option>
				<?php foreach ($customers as $cust) { ?>					
					<option  value="<?php echo $cust['id']; ?>" <?php echo ($_SESSION['userid']!=$cust['created_by'] AND $_SESSION['admin_type'] != 1)?"disabled":""; ?>><?php echo ucwords($cust['display_name']); ?></option>
				<?php } ?>
			</select> 
		</div>
		<div class="form-group col-lg-4">
			<span class="col-lg-4 d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Property'); ?></span>
			<select aria-label="<?= htmlspecialchars(__('Property tenant detail id'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-select js-example-basic-single " name="property_tenant_detail_id" id="property_tenant_detail_id" onchange="getTenantDetails('getproperty_tenant_detail_id',this.value)">
				<option value=""><?php echo __('Select Any One'); ?></option>
				<?php foreach ($ass_status as $ass_statukey=>$ass_statu) { ?>					
					<option  value="<?php echo $ass_statukey; ?>"><?php echo ucwords($ass_statu); ?></option>
				<?php } ?>
			</select> 
		</div>
		<div class="form-group col-lg-4">
			<span class="col-lg-4 d-inline-block fw-bold" style="margin-bottom:5px"><?php echo __('Bills'); ?></span>
			<select aria-label="<?= htmlspecialchars(__('Property tenant billings id'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-select js-example-basic-single " name="property_tenant_billings_id" id="property_tenant_billings_id" onchange="getTenantDetails('getproperty_tenant_billings_id',this.value)">
				<option value=""><?php echo __('Select Any One'); ?></option>
				<?php foreach ($ass_status as $ass_statukey=>$ass_statu) { ?>					
					<option  value="<?php echo $ass_statukey; ?>"><?php echo ucwords($ass_statu); ?></option>
				<?php } ?>
			</select> 
		</div>
	</div>
	<div id="ajaxbillingtable">
	</div>
</div>
<div class="modal-footer" id="showfooter" style="pointer-events: none; opacity: 0.7;">
	<button type="button" class="btn btn-primary waves-effect waves-light loading_popup_btn" onclick="generateTenantBill('S')"> <?php echo __('Save'); ?></button>
	<button type="button" class="btn btn-success waves-effect waves-light m-l-5 loading_popup_btn" onclick="generateTenantBill('SG')"> <?php echo __('Generate Invoice'); ?></button>
	<button type="button" class="btn btn-warning waves-effect waves-light m-l-5 loading_popup_btn" onclick="generateTenantBill('SGS')"> <?php echo __('Generate Invoice & Send Email'); ?></button>
	<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?php echo __('Close'); ?></button>
</div>