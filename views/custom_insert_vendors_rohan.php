<?php 
$_SESSION['apply_js']['todo'] = 1;
//echo "<pre>"; print_r($_POST); echo "</pre>";
if(isset($_POST['submit_vendor'])){
	$data = $_POST;
	$data_agreements = $_POST;
	//echo "<pre>"; print_r($_FILES); echo "</pre>";
	//echo "<pre>"; print_r($_POST); echo "</pre>";
	$image = imageupload('all','image',$_POST['hid_image']);
	unset($data['submit_vendor']);
	unset($data['submit_value']);
	unset($data['image']);
	unset($data['hid_image']);
	unset($data['hid']);
	unset($data['ag_type']);
	unset($data['ag_number']);
	unset($data['file_url']);
	unset($data['ag_ex_date']);
	unset($data['ag_sin_date']);
	unset($data['ag_rowid']);
	unset($data['ag_rowid']);
	$vendor_user_id = $data['vendor_user_id'];
	unset($data['vendor_user_id']);
	unset($data['transaction_table']);
	$transaction_table = $_POST['transaction_table'];
	unset($data['hidden_agreement_file']);
	$data['hiring_clause'] = $data['hiring_clause'] =='on'?'0':'1';
	$data['tds_apply'] = $data['tds_apply'] =='on'?'0':'1';
	$data['asof'] = dateconvertdbformat($data['asof']);
	$data['image'] = $image;
	$data['other_contact_information'] = json_encode($_POST['other_contact_information']);
	//echo "<pre>"; print_r($_POST['vendor_questions']); echo "</pre>";
	
	$data['vendor_questions'] =  json_encode($_POST['vendor_questions']);

	if($_POST['submit_vendor'] == 'Update'){	
		$data_agreements = $_POST;
		$data_agreements['created_date'] = nowDate();
		$data_agreements['created_by'] = $_SESSION['userid'];
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$data['display_name'] = $_POST['first_name']." ".$_POST['last_name'];
		$data['msme_register_yes_no'] = $_POST['msme_register_yes_no']=="Y"?"Y":"N";
		$data['subsidiary_vendor_check'] = $_POST['subsidiary_vendor_check']=="Y"?"Y":"N";
		$vendor_id1 = $_POST['vendor_id'];
		unset($data['vendor_id']);
		$data_agreements['vendor_id'] = $vendor_id1;
		$url = API."?mode=updaterowbasepimarydata&rid=id:".$_POST['hid']."&table=".$transaction_table."&method=post";
		$response = file_post_contents($url, $data, 'update');
		$response250 = json_decode($response,1);
		//echo "<pre>eeeeeeeeeeeeeeeeeeeeeeeeeeeee"; print_r($response250); echo "</pre>";
		?>
		<script type="text/javascript">
        	window.addEventListener("load", function() {
				tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
			});
        </script>
		<?php 
		$postdata['company_id'] = $_SESSION['company_id'];
		$postdata['is_active'] = '0';
		$postdata['current_status'] = '0';
		$postdata['vendor_id'] = $vendor_id1;
		
		$count = count((array)$_FILES["agreement_file"]["name"]);
		for($f=0; $f<$count; $f++) {
			$File_Uploads_Path = "uploads/all/";
			$temp = explode(".", basename($_FILES["agreement_file"]["name"][$f]));
			$Filename=  "file_".time()."_".uniqid();
			$newfilename = $Filename.'.' .end($temp);
			$data_agreements['file_url'][$f] = $newfilename;
			move_uploaded_file($_FILES["agreement_file"]["tmp_name"][$f], $File_Uploads_Path.$newfilename);
		}
		foreach ($data_agreements['ag_type'] as $agkey => $ag){ 
			if($data_agreements['ag_type'][$agkey] !="" AND $data_agreements['ag_number'][$agkey] !=""){
				$postdata['agreement_name'] = $data_agreements['ag_type'][$agkey];
				$postdata['agreement_ref_no'] = $data_agreements['ag_number'][$agkey];
				$postdata['signed_date'] = date('Y-m-d', strtotime($data_agreements['ag_sin_date'][$agkey]));
				$postdata['expiry_date'] = date('Y-m-d', strtotime($data_agreements['ag_ex_date'][$agkey]));
				$postdata['file_url'] = $data_agreements['file_url'][$agkey];
				
				if(!empty($data_agreements['ag_rowid'][$agkey]))
				{
		    		// update
					$row_id = $data_agreements['ag_rowid'][$agkey];
					$postdata['updated_date'] = $data_agreements['created_date'];
					$postdata['updated_by'] = $data_agreements['created_by'];
				//	print_r($postdata);
					$url = API."?mode=updaterowtabledata&rid=".$row_id."&table=".TBL_VENDOR_AGREEMENTS."&method=post";
					$response33 = file_post_contents($url, $postdata, 'update');
				}else {
		    		//insert
					//echo "<pre>";print_r($postdata);
					$postdata['created_date'] = $data['created_date'];
					$postdata['created_by'] = $data['created_by'];
					$url = API."?mode=adddata&table=".TBL_VENDOR_AGREEMENTS."&method=post";
					$response33 = file_post_contents($url, $postdata, 'insert');
				}
			}
		}
		/**if($vendor_user_id == $_SESSION['userid']){
		?>
		<script type="text/javascript">
			window.addEventListener("load", function() {
			  	swal({
					title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
					text: '<?php echo $_SESSION['error_message']['9123']; ?>',
					type: "warning",
					showCancelButton: true,
					confirmButtonClass: "btn-danger",
					confirmButtonText: "Submit",
					cancelButtonText: "Cancel",
					closeOnConfirm: false,
					closeOnCancel: false
				},
				function(isConfirm) {
					if (isConfirm) {
						UpdateSendVerificationProfileStatus();
						swal({
							title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
							text: '<?php echo $_SESSION['error_message']['9124']; ?>',
							type: "info",
							showCancelButton: false,
							confirmButtonClass: "btn-danger",
							confirmButtonText: "Ok",
							cancelButtonText: "No",
							closeOnConfirm: true,
							closeOnCancel: false
						},
						function(isConfirm) {
							if (isConfirm) {
								window.location.href = window.location.href
							}
						});
					}else{
						window.location.href = window.location.href
					}
				});
			});
		</script>
		<?php 
		} **/
		if ($response) {
			 // header("location:index.php?option=$view_url");
		} 
	}
}




if($_GET['VTYPE'] !="VR"){
	if($_GET['Rid'] !=""){
		$rid1 =$_GET['Rid'];
	}else{
		$rid1 = deocdeId('id:'.$_SESSION['vendor_id']);
	}
	$rid =  encodeId($rid1);
	$rid = urldecode($rid);
	$vendorid = explode(":", $rid);
	$vendor_id = $vendorid['1'];
	$transaction_table = "vendors";
}else{
	if($_GET['Rid'] !=""){
		$rid =  encodeId($_GET['Rid']);
		$rid = urldecode($rid);
		$vendorid = explode(":", $rid);
		$vendor_id = $vendorid['1'];
	}
	$transaction_table = "vendor_registers";
}
$url = API."?mode=getvendorsdetails&transaction_table=".$transaction_table."&id=".$vendor_id."&admin_type=".$_SESSION['admin_type'];
$response = callAPI($url);//file_get_contents($url, false, $context);
$result = json_decode($response,1);
//echo "<pre>"; print_r($result); echo "</pre>";
if($_GET['VTYPE'] !="VR"){
	$vendordetail = $result['vendors'];
}else{
	$vendordetail = $result['vendor_registers'];
}
$vendor_id = $vendordetail['id'];
//echo "<pre>"; print_r($vendordetail); echo "</pre>";
$aggrements =$result['vendor_agreements'];
$payment_method =$result['payment_method'];
$salestype =$result['salestype'];
$emplist = $result['employess'];
$countrieslist = $result['countries'];
$states = $result['states'];
$cities = $result['cities'];
$vendorslist = $result['vendorslist'];
$paymenttype = $result['paymenttype'];
$vendor_type = $result['vendor_type'];
$countrylist =$stateslist = $citieslist =  array();
foreach($countrieslist as $country_list){
	$countrylist[$country_list['id']] = $country_list['country_name'];
}
foreach($states as $states_list){
	$stateslist[$states_list['id']] = $states_list['state_name'];
}
foreach($cities as $cities_list){
	$citieslist[$cities_list['id']] = $cities_list['city_name'];
}
$vendor_contact_info_type = $result['vendor_contact_info_type'];
$bank_acoount_type = $result['bank_acoount_type'];
$other_contact_information = json_decode($vendordetail['other_contact_information'],1);
$vendor_questions = json_decode($vendordetail['vendor_questions'],1);
$company_types = array();
foreach($result['company_types'] as $ctype) { 
	$company_types[$ctype['id']] = $ctype;
}

$payment_terms = array();
foreach($result['payment_terms'] as $payt) { 
	$payment_terms[$payt['id']] = $payt; //payment_term_name
}
$incoterms = array();
foreach($result['incoterms'] as $payt) { 
	$incoterms[$payt['id']] = $payt; //incoterm_name
}

$stausresult = ViewStatusData();
$taskscategories = array();
foreach($result['tasks_categories'] as $tasks_categories){
 	$taskscategories[$tasks_categories['id']] = $tasks_categories['category_name'];
}
$transactionactivityschedule = array();
$transactionactivityschedulecompletedstatus = array();
$rejetcedcnt = $totaltasks = $yettostartcnt = $totalinprogresstask = $completedcnt = $totalapprovedtask = $totaldelayedcnttask =$totaltaskreview_cnt =  0;
foreach($result['transaction_activity_schedule'] as $transaction_activity_schedule){
	if($transaction_activity_schedule['current_status'] == ST_COMPLETE){
		$transactionactivityschedulecompletedstatus[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
		$totaltaskreview_cnt++;
	}else{
		$transactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
	}
    if($transaction_activity_schedule['current_status'] == ST_APPROVED){
      $complitedtask[$transaction_activity_schedule['process_id']]++;
      $totalapprovedtask++;
    }elseif($transaction_activity_schedule['current_status'] == ST_COMPLETE){
     	$completedcnt++;
    }elseif($transaction_activity_schedule['current_status'] == ST_REJECTED){
     	$rejetcedcnt++;
    }elseif($transaction_activity_schedule['current_status'] == ST_INPROGRESS){
     	$totalinprogresstask++;
    }elseif($transaction_activity_schedule['current_status'] == ST_ACTIVE){
     	$yettostartcnt++;
    } 
  	if($transaction_activity_schedule['link_page'] == '0'){
    	$linktransactionactivityschedule[$transaction_activity_schedule['process_id']][] = $transaction_activity_schedule;
    	if($transaction_activity_schedule['current_status'] == ST_APPROVED){
      		$linkcomplitedtask[$transaction_activity_schedule['process_id']]++;
    	}
  	}
 	$totaltask[$transaction_activity_schedule['process_id']]++;
 	$totaltasks++;
}
$File_Uploads_Path = "uploads/all/";
if($vendordetail['image'] !=''){
	$image = $File_Uploads_Path.$vendordetail['image']; 
}else{
	$image = $File_Uploads_Path."no-image.jpg";
} 


$profilereadiness = array("Business Information"=>array('company','website_url','company_trading_name','company_subsidiary_division','first_name','middle_name','last_name','phone','email','phone','billing_address','billing_country','billing_state','billing_city','billing_post_code','contact_person','mobile','shipping_address','shipping_country','shipping_state','shipping_city','shipping_post_code'),"Company Information"=>array('vendor_tax_reg_no','business_type','duns_no','company_registration_number','company_registration_country','payment_terms','gstin','pan_no','tan_number','incoterm_id','image','msme_number'),"Banking Information"=>array('bank_name','bank_address','bank_country','bank_state','bank_city','bank_post_code','bank_beneficiary_name','bank_account_no','account_type','routing_aba_dfi_ach','routing_wire_Code','swift_bic_code','iban_code','bank_query_person_name','bank_query_contact_no','bank_query_email_id','ifsc_code'),"Note"=>array('vendor_po_notes','vendor_other_notes'));

$company_types = array();
foreach($result['company_types'] as $ctype) { 
	$company_types[$ctype['id']] = $ctype;
}

$payment_terms = array();
foreach($result['payment_terms'] as $payt) { 
	$payment_terms[$payt['id']] = $payt; //payment_term_name
}
$incoterms = array();
foreach($result['incoterms'] as $payt) { 
	$incoterms[$payt['id']] = $payt; //incoterm_name
}
$vendor_questions = json_decode($vendordetail['vendor_questions'],1);
$verification_profile_users = json_decode($vendordetail['verification_profile_users'],1);
$transaction_activity_schedule_cnt = $result['transaction_activity_schedule_cnt'];
$vendor_verifications = json_decode($vendordetail['vendor_verifications'],1);
$vendor_display_fields = $result['vendor_display_fields'];
$vafield = json_decode($vendor_display_fields['field_list'],1);
//print_r($vafield);
?>
<script type="text/javascript">
	<?php if($_GET['T1'] =="task_tab"){ ?>
// document.onreadystatechange = function () {
//   	if (document.readyState == "complete"){
// 		getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab');
// 	} 
// }
window.addEventListener("load", function() {
	getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab');
});
<?php } ?>



function showUpdateChecklist(id){
	$(".custom-popup-open").hide();
 	$(".custom-popup-slide-updatechecklist-"+id).show('slow');
}
function closeSmallPopUp(){
	$(".custom-popup-open").hide('slow');
}
// function getTaskCheckList(id,aryposition,candidate_id){
// 	var transaction_table = "vendor_registers";
// 	$('.checkcheckboxcopliance_'+id+'_'+aryposition).attr("disabled", true);
// 	$("#kt_modal_KTDatatable_remote").modal({
//   		backdrop: 'static',
// 	  	keyboard: false
// 	});
// 	jQuery.ajax({
// 	  	url: "views/custom_insert_transaction_activity_schedule_popup.php",
// 	  	data:'id='+id+"&aryposition="+aryposition+"&candidate_id="+candidate_id+'&transaction_table='+transaction_table,
// 	  	type: "POST",
// 	  	success:function(data){
// 	    	$('#popup1').html(data);
// 	    	$('#kt_modal_KTDatatable_remote').modal("show");
// 	  	},
// 	  	error:function (){}
// 	});
// }

// function updateTaskStatus(task_id,status){
//   var transaction_table = "vendor_registers";
//   jQuery.ajax({
//     url: "views/custom_update_onboardning_task_status_popup.php",
//     data:'task_id='+task_id+"&status="+status+'&transaction_action_table='+transaction_table,
//     type: "POST",
//     success:function(data){
//       $('#popup1').html(data);
//       $('#kt_modal_KTDatatable_remote').modal("show");
//     },
//     error:function (){}
//   });
// }
function editVendorProfile(edittype=""){
	var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
	$('#ajaxvendorprofile').html(htmloader);
	var transaction_table = '<?php echo $transaction_table; ?>';
	var vendor_id = '<?php echo $vendor_id; ?>'
	var vtype = '<?php echo $_GET['VTYPE']; ?>'
	var savetext = "'EDIT'";
	jQuery.ajax({
	    url: "views/custom_insert_vendors_ajax.php",
	    data:'transaction_table='+transaction_table+"&vendor_id="+vendor_id+"&ajaxcall=Y&edittype="+edittype+"&vtype="+vtype,
	    type: "POST",
	    success:function(data){
	    	$("#ajaxvendorprofile").html(data);
	    	$("#chnagebtndiv").html();
	    	$(".js-example-basic-single").select2();
	    	if(edittype ==""){
	    		$("#chnagebtndiv").html('<button class="btn btn-primary" onclick="editVendorProfile('+savetext+')"><i class="ti-pencil text-white"></i> Edit</button>');
			}else{
				$("#chnagebtndiv").html('<button class="btn btn-danger" onclick="editVendorProfile()"><i class="ti-close text-white"></i> Cancel</button>');
			}
			$( ".datepicker" ).datepicker({
				showOn: "both",
				buttonText: "<i class='ti-calendar'></i>",
				dateFormat: date_format,
				changeMonth: true,
				changeYear: true
			});
			var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch-vendor'));
			elem.forEach(function(html) {
				var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff' });
			});
	    	
	    },
	    error:function (){}
  	});

	//$("#ajaxvendorprofile").html();
}
function addedurows(){
	var date_format = "yy-mm-dd";
	var count = $(".itemRow").length;
	count = count + 1;
	jQuery(document).ready(function() {    
		KTBootstrapDatepicker.init();
	});
	var htmlRows = '';
	htmlRows += '<tr class="">'; 

	htmlRows += '<td>'+count+'</td>'; 
	htmlRows += '<td><input type="text" name="ag_type[]"  class="form-control rowcount itemRow " autocomplete="off">  </td>'; 
	htmlRows += '<td><input type="text" name="ag_number[]"  class="form-control " autocomplete="off"> </td>'; 
	htmlRows += '<td><input type="text" name="ag_sin_date[]"  class="form-control datepicker"  autocomplete="off">  </td>'; 
	htmlRows += '<td><input type="text" name="ag_ex_date[]"  class="form-control datepicker"  autocomplete="off">  </td>'; 
   //htmlRows += '<td><input type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';   
   htmlRows +='<td><div class="image-input col-lg-3" id="kt_image_5" style="display: inherit; margin-top: -2px;"><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" ><i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i><input type="file" name="agreement_file[]" /><input type="hidden" name="hidden_agreement_file[]" value=""/></label></div></td>';
  // htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary mt-2" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" > <i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i></div></td>'
	htmlRows += '</tr>';
	$('#edutable').append(htmlRows);
	$(".js-example-basic-single").select2();
	$( ".datepicker" ).datepicker({
		showOn: "both",
		buttonText: "<i class='ti-calendar'></i>",
		dateFormat: date_format,
		changeMonth: true,
		changeYear: true
	});
}
function selectedcategory(selected_id,element_id,table_name,field_name,targetelement) {
  var selectedvalues = selected_id;
                //alert(selected_id);

                if(table_name=='states' || table_name=='cities'){
                 $.ajax({
                  url: "ajax.php",
                  data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues+'&field='+field_name,
                  type: "POST",
                  success: function(response) {
                    var response = JSON.parse(response);
                    console.log(response.length);
                    console.log(response);
                    var select = document.getElementById(element_id); 
                    if (select) {
				        select.innerHTML = "";
				    }
                    //document.getElementById(element_id).innerHTML = "";
                    for (var i = 0; i < response.length; i++) 
                    {
                     var el = document.createElement("option");
                     if(table_name=='states'){
                      el.textContent = response[i].state_name;
                      el.value = response[i].id;

                    }
                    if(table_name=='cities'){
                      el.textContent = response[i].city_name;
                      el.value = response[i].id;

                    }
                    select.add(el);
                  }
                }
              });
               }

             }

function createVendorRegisterToVendor(){
	swal({
		title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
		text: '<?php echo $_SESSION['error_message']['9117']; ?>',
		type: "info",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "Yes",
		cancelButtonText: "No",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			var transaction_table = '<?php echo $transaction_table; ?>';
			var vendor_id = '<?php echo $vendor_id; ?>';
			var vtype = '<?php echo $_GET['VTYPE']; ?>';
			jQuery.ajax({
			    url: "ajax.php",
			    data:'page=createvendorregistertovendor&transaction_table='+transaction_table+"&vendor_id="+vendor_id+"&vtype="+vtype,
			    type: "POST",
			    async:false,
			    success:function(data){
			    	 tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['9110']; ?>','');
			    	 location.reload();
			    },
			    error:function (){}
		  	});
		}
	});
}
function getAjaxDynamicPage(option,divid,pagetype="",rid=""){
	console.log(rid);
	updateUrlParameter('T1',divid);
	var transaction_table = '<?php echo $transaction_table; ?>';
	var transaction_id = '<?php echo $vendor_id; ?>';
	var currentUrl = window.location.href;
	var urlParts = currentUrl.split('?');
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#"+divid).html(loader);
    $.ajax({
        url: "views/"+option+".php",
        data:'option='+option+'&ajaxpage=Y&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&'+urlParts['1'],
        type: "POST",
        async: true,
        success:function(data){
            $('#'+divid).html(data);
            $('[data-bs-toggle="tooltip"]').tooltip();
            if(rid !=""){
            	getCheckList(rid);
        	}
        },
        error:function (){}
    });
}
function onchangeVRQAnswer(valuess){
	console.log("dfdfgdgbv--"+valuess);
	if(valuess == 'Y'){
		$(".vrq_extra").show();
	}else{
		$(".vrq_extra").hide();
	}				
}
function onchangeWorkManufracturerFacilityAnswer(valuess){
	if(valuess == 'Y'){
		$(".work_manufacturer_facility").show();
		$(".supplychainresponsibility_5").hide();
		
	}else{
		$(".work_manufacturer_facility").hide();
		$(".supplychainresponsibility_5").show();
	}	
}
function onchangeHoldOriginalPassportAnswer(valuess){
	if(valuess == 'Y'){
		$(".hold_original_passport").show();
	}else{
		$(".hold_original_passport").hide();
	}	
}
function onchangeAssociatedAnswer(valuess){
	if(valuess == 'Y'){
		$(".associated_team").show();
	}else{
		$(".associated_team").hide();
	}	
}
function onchangeSupplyChainResponsibilityAnswer(valuess,seq){
	if(seq == '3'){
		if(valuess == 'Y'){
			$(".supplychainresponsibility_7").hide();
		}else{
			$(".supplychainresponsibility_7").show();
		}	
	}
}
function checkInstructions(){
	var transaction_table = '<?php echo $transaction_table; ?>';
	var transaction_id = '<?php echo $vendor_id; ?>';
	$("#instructions_checkbox_div").addClass('event_disabled');
    $.ajax({
        url: "ajax.php",
        data:'page=checkvendorinstructions&transaction_table='+transaction_table+'&transaction_id='+transaction_id,
        type: "POST",
        async: true,
        success:function(data){
        	//console.log(data);
        	location.reload();
        },
        error:function (){}
    });
}
function getVendorSummary(){
	var transaction_table = '<?php echo $transaction_table; ?>';
	var transaction_id = '<?php echo $vendor_id; ?>';
	var vtype = '<?php echo $_GET['VTYPE']; ?>';
	jQuery.ajax({
        url: "views/custom_view_vendors_popup.php",
        data: "transaction_table="+transaction_table+"&transaction_id="+transaction_id+"&vtype="+vtype,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
             //$(".js-example-basic-single").select2();
        }
    });
}
function SubmitForVendorCreation(){
	var transaction_table = '<?php echo $transaction_table; ?>';
	var vendor_id = '<?php echo $vendor_id; ?>'
	var vtype = '<?php echo $_GET['VTYPE']; ?>'
	jQuery.ajax({
	    url: "views/custom_insert_vendors_ajax.php",
	    data:'transaction_table='+transaction_table+"&vendor_id="+vendor_id+"&ajaxcall=Y&edittype=SendVerificationProfile&vtype="+vtype,
	    type: "POST",
	    success:function(data){
	    	$('#dynamic_popup11').html(data);
    		$('#kt_modal_dynamic_popup2').modal("show");
	    }
	});	
}

function UpdateSendVerificationProfileStatus(){
	swal({
		title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
		text: '<?php echo $_SESSION['error_message']['9123']; ?>',
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "Submit",
		cancelButtonText: "Cancel",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			$("#popsubmitforreview_btn").text("Please Wait........");
			$("#popsubmitforreview_btn").addClass("disabled");
			var transaction_table = '<?php echo $transaction_table; ?>';
			var transaction_id = '<?php echo $vendor_id; ?>';
		    $.ajax({
		        url: "ajax.php",
		        data:'page=updatesendverificationprofilestatus&transaction_table='+transaction_table+'&transaction_id='+transaction_id,
		        type: "POST",
		        async: true,
		        success:function(data){
		        	//console.log(data);
		        	window.location.href=window.location.href;
		        },
		        error:function (){}
		    });
		}
	});

}
function vendorProfileUpdateStatus(){
	var status = $("#selected_check_box").val();
	var transaction_table = '<?php echo $transaction_table; ?>';
	var transaction_id = '<?php echo $vendor_id; ?>';
	var profile_review_comments = $("#profile_review_comments").val();
	$("#profile_review_comments_error").hide();
	if(profile_review_comments ==""){
		$("#profile_review_comments_error").show();
	}else{
		$("#profile_review_comment_div").show('slow');
		//$("#instructions_checkbox_div").addClass('event_disabled');
		if(status == "Y"){
	    	var extramsg = " Recommend The Vendor";
		}else{
			var extramsg = " Object The Vendor";
		}
		swal({
			title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
			text: '<?php echo $_SESSION['error_message']['9122']; ?>'+extramsg,
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes",
			cancelButtonText: "No",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {
				$.ajax({
			        url: "ajax.php",
			        data:'page=updatevendorprofileupdatestatus&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&status='+status+'&profile_review_comments='+profile_review_comments,
			        type: "POST",
			        async: true,
			        success:function(data){
			        	console.log(data);
			        	location.reload();
			        },
			        error:function (){}
			    });
			}else{
				//location.reload();
			}
		});
	}

}
function cancelComment(){
	$('.coomment_check_box').prop('checked', false);
	$("#selected_check_box").val('');
	$("#profile_review_comment_div").hide();
	$("#dispabled_div").css('pointer-events','all')
}
function showComment(valu){
	$("#dispabled_div").css('pointer-events','none');
	$("#profile_review_comment_div").show();
	$("#selected_check_box").val(valu);
	
}

function verificationVendorDetails(verification_type =""){
	var transaction_table = '<?php echo $transaction_table; ?>';
	var vendor_id = '<?php echo $vendor_id; ?>'
	var vtype = '<?php echo $_GET['VTYPE']; ?>'
	jQuery.ajax({
	    url: "views/custom_insert_vendor_verifications_popup.php",
	    data:'transaction_table='+transaction_table+"&vendor_id="+vendor_id+"&vtype="+vtype+'&verification_type='+verification_type,
	    type: "POST",
	    success:function(data){
	    	$('#dynamic_popup1').html(data);
    		$('#kt_modal_dynamic_popup').modal("show");
	    }
	});	
}
function selectItemsWithParty(item_id,type,party_item_id=""){
	var transaction_table = 'vendors';
	var transaction_id = '<?php echo $vendor_id; ?>';
	if(type == "REMOVE"){
		 swal({
		    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
		    text: "<?php echo $_SESSION['error_message']['9167']; ?>",
		    type: "warning",
		    showCancelButton: true,
		    confirmButtonClass: "btn-danger",
		    confirmButtonText: "Yes",
		    cancelButtonText: "No",
		    closeOnConfirm: true,
		    closeOnCancel: true
		},
		function(isConfirm) {
		    if (isConfirm) {
				$.ajax({
					url: "ajax.php",
					type: "POST",
					data: 'page=insertvendorcustomeritems&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&party_item_id='+party_item_id+'&type='+type,
					success: function(response) {
						console.log(response);
						tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"<?php echo $_SESSION['error_message']['9165']; ?>",'');
						getVendorCustomerItems();
					}
				});
			}
		});
	
	}else{
		jQuery.ajax({
		    url: "views/custom_insert_vendor_customer_item_popup.php",
		    data:'transaction_table='+transaction_table+"&transaction_id="+transaction_id+"&item_id="+item_id+'&type='+type,
		    type: "POST",
		    success:function(data){
		    	$('#dynamic_popup1').html(data);
	    		$('#kt_modal_dynamic_popup').modal("show");
		    }
		});	
	}

}
function getVendorCustomerItems(){
	var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#vendor_items").html(loader);
    var transaction_table = 'vendors';
	var transaction_id = '<?php echo $vendor_id; ?>';
	jQuery.ajax({
	    url: "views/custom_list_vendor_customer_items.php",
	    data:'transaction_table='+transaction_table+"&transaction_id="+transaction_id,
	    type: "POST",
	    success:function(data){
	    	$('#vendor_items').html(data);
	    	$(".js-example-basic-single").select2();
			$('[data-bs-toggle="tooltip"]').tooltip();
      	}
	});	
}
</script>
<?php if($vendordetail['instruction_status'] == "N" AND $vendordetail['user_id'] == $_SESSION['userid']){
	include('views/custom_view_vendor_instructions.php');
}else{ ?>
<div class="card mb-1 mb-xl-10">
	<div class="card-body pt-9 pb-0">
		<div class="banner">
			<img src="assets/images/user-profile/bg-img1.jpg" class="w-100 b-radius-20" alt="">
		</div>
	</div>
	<div class="profile-img" style="margin:-40px 0 0 0;">
		<div class="row align-items-bottom">
			<div class="col-lg-1"></div>
			<div class="col-lg-5">
				<div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
					<form method="post" id="profile_pic_file" enctype="multipart/form-data" class="position-relative">
						<label class="btn-hover-text-primary position-absolute  text-white" data-action="remove" data-bs-toggle="tooltip" title="Edit Profile Pic" style="left: -3px; top: -2px;">
							<span><i class="ti-pencil-altj"></i></span>
							<input type="file" name="profile_pic_file" accept=".png, .jpg, .jpeg"  onchange="updateProfilePic()" />
						</label>
						<?php if($vendordetail['image']!=""){ ?>
							<img src="<?php echo $image; ?>" class="img-radius img-90" alt=""> 
						<?php  }else{ ?>
							<div class="symbol symbol-50 symbol-lg-120 symbol-primary ">
								<span class="font-size-h3 symbol-label font-weight-boldest img-90 p-20 f-40" style=" border-radius: 50%; line-height: 1.1;"><?php echo $vendordetail['display_name']!=""?initials($vendordetail['display_name']):"NA"; ?></span>
							</div>	
						<?php } ?>
						<?php if($vendordetail['finance_connect'] ==$_SESSION['userid'] || $_SESSION['admin_type'] == '1'){ 
							if($vendor_verifications['PAN']['status'] =="Y" AND $vendor_verifications['BANK']['status'] =="Y" AND $vendor_verifications['GSTIN']['status'] =="Y" AND ($vendordetail['msme_register_yes_no'] == "N" || $vendor_verifications['MSME']['status'] =="Y")){
								$verify_text = '<i class="ti-check-box m-r-5"></i> Verifiaction Complitade';
								$verify_btn = 'btn-success';
							}else{
								$verify_text = '<i class="ti-alert m-r-5"></i> Verifiaction Pending';
								$verify_btn = 'btn-danger';
							}
							?>
						<span id="verified_btn"> 
							<button type="button" class="btn waves-effect waves-light <?php echo $verify_btn; ?> pt-1 pb-1 m-l-20 cursor-pointer" onclick="verificationVendorDetails()" ><?php echo $verify_text; ?></button>
	                        
	                    </span>
	                <?php } ?>
					</form>
				</div> 
				<div class="mt-3 d-block mb-3">
					<div class="d-flex mr-3 mb-2">
						<?php if($vendordetail['company'] !=""){ ?>
							<a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600">
							<?php echo $vendordetail['company'];?>
								<small class="d-block f-14"><?php echo $vendordetail['display_name'];?></small>
							</a>
						<?php }else{  ?>
							<a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600">
							<?php echo $vendordetail['display_name'];?></a>
						<?php } ?>
					</div>	
					<?php if($vendordetail['email']!=""){ ?>
						<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
							<i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $vendordetail['email'];?></a>
						<?php } if($vendordetail['phone']!=""){  ?>	
							<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
								<i class="fa fa-phone f-14 text-primary"></i>&nbsp;<?php echo DynamicPhoneNumberFormt($vendordetail['phone']);?></a>
							<?php }  ?>	
							<?php if($vendordetail['billing_address']!=""){ ?>
								<a class="font-weight-bold text-dark-50 d-block mb-2 mt-2"><i class="ti-location-pin text-primary f-18"></i>&nbsp;<?php echo $vendordetail['billing_address']." ".$vendordetail['post_code'];?> </a>
							<?php } ?>
							<div class="font-weight-bold text-dark-50 mt-1 f-14">
								<?php if($vendordetail['established_year'] !="" AND $vendordetail['established_year'] !="0"){  ?>
									<b class="f-w-600">Established Year : </b><?php echo $vendordetail['established_year']; ?>&nbsp;
								<?php }  ?>
							</div>
						</div>

						<?php 
						if($verification_profile_users[$_SESSION['userid']]['complete'] != ''){
							//echo $transaction_activity_schedule_cnt['total_task']." != ".$transaction_activity_schedule_cnt['total_approved'];
							$total_approved = $transaction_activity_schedule_cnt['total_approved'] !=""?$transaction_activity_schedule_cnt['total_approved']:0;
							if($transaction_activity_schedule_cnt['total_task'] != $total_approved){
								echo "<span class='text-danger'><i class='ti-alert'></i>".$_SESSION['error_message']['9125']."</span>";
								$verification_profile_users_status = 'style="pointer-events: none;"';
							}elseif($verification_profile_users[$_SESSION['userid']]['status'] == 'Y'){
								$verifivcatiostatus_y = "checked";
								$verification_profile_users_status = 'style="pointer-events: none;"';
								$lablestauss .= '<span class="d-block"> <span class="f-w-700">Comment : </span>'.$verification_profile_users[$_SESSION['userid']]['comment'].'</span>';
								$lablestauss .= '<span class="d-block"> <span class="f-w-700">Reviewed Date Time : </span>'.$verification_profile_users[$_SESSION['userid']]['updatedatetime'].'</span>';
							}elseif($verification_profile_users[$_SESSION['userid']]['status'] == 'N'){
								$verifivcatiostatus_n = "checked";
								$verification_profile_users_status = 'style="pointer-events: none;"';
								$lablestauss .= '<span class="d-block"> comment : '.$verification_profile_users[$_SESSION['userid']]['comment'].'</span>';
								$lablestauss .= '<span class="d-block"> Reviewed Date Time : '.$verification_profile_users[$_SESSION['userid']]['updatedatetime'].'</span>';
							}
						?>
							<div class="card"  <?php echo $verification_profile_users_status; ?>>	
								<div class="card-body">
									<span class="text-info "><i class="ti-alert"></i> I have reviewed the information provided for this vendor and am confident in their ability to successfully fulfill the agreed-upon terms of the contract</span>
									<div class="mt-3" id="dispabled_div">
										<div class="checkbox-fade fade-in-primary" onclick="showComment('Y')">
											<label class="form-label">
												<input type="checkbox" value="" <?php echo $verifivcatiostatus_y; ?> class="coomment_check_box">
												<span class="cr">
													<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
												</span>
												<span>I Recommend</span>
											</label>
										</div>
										<div class="checkbox-fade fade-in-primary text-danger" onclick="showComment('N')">
											<label class="form-label">
												<input type="checkbox" value="" <?php echo $verifivcatiostatus_n; ?> class="coomment_check_box">
												<span class="cr">
													<i class="cr-icon icofont icofont-ui-check txt-primary"></i>
												</span>
												<span>I Object</span>
											</label>
										</div>
										<?php echo $lablestauss; ?>
									</div>
									<div class="mt-3 row" id="profile_review_comment_div" style="display: none">
										<div class="col-lg-8">
											<label class="form-label">Comment <span class="text-danger">*</span></label>
											<input type="hidden" class="form-control" id="selected_check_box">
											<textarea class="form-control" id="profile_review_comments"></textarea>
											<span class="text-danger" id="profile_review_comments_error" style="display: none">Please Enter Comment</span>
										</div>
										<div class="col-lg-2">
											<button class="btn btn-warning waves-effect waves-light mt-4" onclick="vendorProfileUpdateStatus()"> Save</button>
										</div>
										<div class="col-lg-2">
											<button class="btn btn-danger waves-effect waves-light mt-4" onclick="cancelComment()"> Cancel</button>
										</div>
									</div>

								</div>
							</div>
						<?php  } ?>
					</div>
					<div class="col-lg-6 p-3">
						<div class="row mt-5">
							<div class="f-15 f-w-600 text-info mb-2" style="clear: both;">Important Instructions</div>
							<span class="d-block">1) Fields marked with Asterisk (*) sign are mandatory</span>
							<span class="d-block mt-2">2) All Attachments should be in PDF or JPG format ONLY.</span>
							<span class="d-block mt-2">3) PAN Card copy compulsory for vendor registration.</span>
							<span class="d-block mt-2">4) Only PDF, jpeg files of maximum size 1 MB are allowed to be uploaded</span>
							<span class="d-block mt-2">5) For any technical queries, please write <?php echo $_SESSION['config']['admin_email']; ?></span>
							<span class="d-block mt-2">6) Attach Scan copy of cheque (With vendor name printed on it) AND bank details on vendor letter head stamped/verify by bankers.
							</span>
							 <div class="border-checkbox-section mt-2 text-right" style="pointer-events: none;">
		                        <div class="border-checkbox-group border-checkbox-group-primary">
		                            <input class="border-checkbox" type="checkbox" <?php echo $vendordetail['instruction_status'] =="Y"?"checked":""; ?>>
		                            <label class="form-label border-checkbox-label f-w-600" >I / We have read the instructions, will follow them during registration</label>
		                        </div>
                    		</div>
							
						</div>
						<!-- <div class="row mt-5">
							<div class="col-2 text-center">
								<div class="s-caption  text-primary"><i class="ti ti-user f-20"></i></div>
								<div class="s-cont d-inline-block">
									<span class="f-w-700 m-b-0">HR </span>
									<p class="m-b-0 f-12"><?php echo emptytextdashShow($_SESSION['users'][$vendordetail['hr_connect']]); ?></p>
								</div>
							</div>
							<div class="col-2 b-l-default text-center">
								<div class="s-caption  text-danger"><i class="ti ti-user f-20"></i></div>
								<div class="s-cont d-inline-block">
									<span class="f-w-700 m-b-0">Finance</span>
									<p class="m-b-0 f-12"><?php echo emptytextdashShow($_SESSION['users'][$vendordetail['finance_connect']]); ?></p>
								</div>
							</div>
							<div class="col-2 b-l-default text-center">
								<div class="s-caption  text-info"><i class="ti ti-user f-20"></i></div>
								<div class="s-cont d-inline-block">
									<span class="f-w-700 m-b-0">Admin</span>
									<p class="m-b-0 f-12"><?php echo emptytextdashShow($_SESSION['users'][$vendordetail['admin_connect']]); ?></p>
								</div>
							</div>
							<div class="col-2 b-l-default text-center">
								<div class="s-caption  text-success"><i class=" ti ti-user f-20"></i></div>
								<div class="s-cont d-inline-block">
									<span class="f-w-700 m-b-0">Project</span>
									<p class="m-b-0 f-12"> <?php echo emptytextdashShow($_SESSION['users'][$vendordetail['project_connect']]); ?></p>
								</div>
							</div>
							<div class="col-2 b-l-default text-center">
								<div class="s-caption"><i class="ti ti-user f-20"></i></div>
								<div class="s-cont d-inline-block">
									<span class="f-w-700 m-b-0">Location</span>
									<p class="m-b-0 f-12 "><?php echo emptytextdashShow($_SESSION['users'][$vendordetail['location_connect']]); ?> </p>
								</div>
							</div>
							<div class="col-2 b-l-default text-center">
								<div class="s-caption  text-info"><i class="ti ti-user f-20"></i></div>
								<div class="s-cont d-inline-block">
									<span class="f-w-700 m-b-0">Legal</span>
									<p class="m-b-0  f-12"><?php echo emptytextdashShow($_SESSION['users'][$vendordetail['legal_connect']]); ?></p>
								</div>
							</div>
						</div> -->
						
					</div>

				</div>
				<div class="row p-l-10 p-r-10">
					<div class="col-lg-6 p-3 text-left">
						<?php $allaproveprofile = 0; if($vendordetail['send_verification_profile_status'] == "Y"){ ?>
							<span class="text-warning d-block">* Profile Verification In-Process</span>
							<?php if($vendordetail['user_id'] != $_SESSION['userid']){ 
								echo "<div class=' mt-2'>";
							 foreach($verification_profile_users as $uidkey=>$vpu){ 
									$bgcolor = "";
									$label_title = $_SESSION['users'][$uidkey]." : ";
									if($vpu['status'] == 'Y'){
										$label_title .=  " recommend";
										$bgcolor = "bg-success";
									}elseif($vpu['status'] == 'Y'){
										$label_title .=  " Object";
										$bgcolor = "bg-danger";
										$allaproveprofile = 1;
									}else{
										$label_title .=  " Pending";
										$allaproveprofile = 1;
									}
								
								?>
								<span  alt="user image" class="img-radius img-30 align-top m-r-10 custom_txt_round f-12 text-center <?php echo $bgcolor; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $label_title; ?>"><?php echo initials($_SESSION['users'][$uidkey]); ?></span>
							<?php } echo "</div>"; } ?>
						<?php } ?>	
					</div>
					<div class="col-lg-6 p-3 text-right">
						<?php if($transaction_table == "vendor_registers"){ ?>
						<span class="text-info">* Registration in-progress</span>
						<?php } ?>
					</div>
				</div>
			</div>

		</div>

		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-6">
						<ul class="nav md-tabs infisuite_tab mt-2 ml-2" role="tablist">
							<li class="nav-item">
								<a class="nav-link <?php echo ($_GET['T1']=='overview_tab' OR $_GET['T1']=="")?"active":"";?>" data-bs-toggle="tab" href="#overview_tab" role="tab" onclick="updateUrlParameter('T1','overview_tab')">Overview</a>
								<div class="slide"></div>
							</li>
							<li class="nav-item">
								<a class="nav-link <?php echo $_GET['T1']=='task_tab'?"active":"";?>" data-bs-toggle="tab" href="#task_tab" role="tab" onclick="getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab')">Task</a>
								<div class="slide"></div>
							</li>
						</ul>
					</div>
					<div class="col-lg-6 text-right">
						<?php if($vendordetail['user_id'] == $_SESSION['userid'] AND $vendordetail['send_verification_profile_status'] == "N"){ ?>
						<button class="btn btn-danger waves-effect waves-light"  onclick="SubmitForVendorCreation()"><i class="ti-thumb-up"></i> Submit For Approval</button>
						<?php } ?>
						<button class="btn btn-success waves-effect waves-light"  onclick="getVendorSummary()"><i class="ti ti-eye"></i> View Vendor Summary</button>

						<?php if($vendordetail['vendor_id'] =="" AND $_SESSION['admin_type'] == '1' AND $totalapprovedtask == $totaltasks AND $transaction_table == "vendor_registers" AND $vendordetail['send_verification_profile_status'] == "Y" AND $allaproveprofile == '0'){ ?>
						<button class="btn btn-primary waves-effect waves-light" onclick="createVendorRegisterToVendor()"><i class="ti ti-user"></i> Create Vendor</button>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	
		<div class="tab-content card-block p-0">
			<div class="tab-pane <?php echo ($_GET['T1']=='overview_tab' OR $_GET['T1']=="")?"active":"";?>" id="overview_tab" role="tabpanel">
				<div class="row">
		  			<div class="col-lg-3">
		  				<div class="card">
							<div class="card-header">
							    <h5>Profile Completion</h5>
							    <div class="card-header-right">

						        </div>
							</div>
							<div class="card-body">
				  				<table class="table table-hover table-md">
                  <tbody>
                    <?php foreach($profilereadiness as $keylabel=>$profilereadines){
                      $complete_field =  $total_field = 0; 
                      $total_field = count((array)$profilereadines);
                      foreach($profilereadines as $profilereadines){
                        if($vendordetail[$profilereadines] != ""){
                          $complete_field++; 
                        }
                      }
                      $percentage = (($complete_field/$total_field)*100);
                      ?>
                      <tr>
                        <td>
                          <h6 class="mb-0 f-14"><?php echo $keylabel; ?></h6>
                        </td>
                        <td align="right">
                          <div class="d-flex align-items-center w-100px w-sm-120px flex-column ">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                              <span class="f-12 f-w-600">Completion </span>
                              <span class="f-12 f-w-600"><?php echo round($percentage); ?>%</span>
                            </div>
                            <div class="col-md-12">
                              <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-<?php echo round($percentage)>=50?"success":"danger"; ?>" role="progressbar" style="width:  <?php echo round($percentage); ?>%;" aria-valuenow=" 55" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody></table>
				  			</div>
				  		</div>
				  	</div>
		  			<div class="col-lg-9">
		  				<div class="card">
		  					<div class="card-header">
							    <h5>Overview</h5>
							    <div class="card-header-right" id="chnagebtndiv">
							    	<?php //echo "created_by - ".$vendordetail['created_by']." -- userid -- ".$_SESSION['userid']." -- Vendor user_id --".$vendordetail['user_id']; //if(($vendordetail['vendor_id'] =="" AND $transaction_table == 	"vendor_registers") OR $transaction_table == "vendors"){
if(($vendordetail['send_verification_profile_status'] == "N" AND $vendordetail['vendor_id'] =="" AND $transaction_table == "vendor_registers" AND ($vendordetail['originator'] == $_SESSION['userid'] OR $vendordetail['user_id'] == $_SESSION['userid'])) OR ($transaction_table == "vendors" AND ($vendordetail['originator'] == $_SESSION['userid'] OR $vendordetail['user_id'] == $_SESSION['userid'] OR $vendordetail['created_by'] == $_SESSION['userid']) ) OR $_SESSION['admin_type'] == '1'){
							    	 ?>
							    		<button class="btn btn-primary" onclick="editVendorProfile('EDIT')"><i class="ti-pencil text-white"></i> Edit</button>
							    	<?php } ?>
						        </div>
							</div>
							<div class="card-body" id="ajaxvendorprofile">
				  				<?php include('views/custom_insert_vendors_ajax.php');?>
				  			</div>
				  		</div>
					</div>
				</div>
			</div>
			<div class="tab-pane <?php echo $_GET['T1']=='task_tab'?"active":"";?>" id="task_tab" role="tabpanel">
				<?php //include('views/custom_insert_transaction_activity_schedules.php'); ?>
			</div>
		</div>
		<?php } ?>