<?php 
$_SESSION['apply_js']['todo'] = 1;

if(isset($_POST['submit'])){
	$data = $_POST;
	$data_agreements = $_POST;
	//echo "<pre>"; print_r($_FILES); echo "</pre>";
	//echo "<pre>"; print_r($_POST); echo "</pre>";
	$image = imageupload('all','image',$_POST['hid_image']);
	unset($data['submit']);
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
	unset($data['transaction_table']);
	$transaction_table = $_POST['transaction_table'];
	unset($data['hidden_agreement_file']);
	$data['hiring_clause'] = $data['hiring_clause'] =='on'?'0':'1';
	$data['tds_apply'] = $data['tds_apply'] =='on'?'0':'1';
	$data['asof'] = dateconvertdbformat($data['asof']);
	$data['image'] = $image;
	if($_POST['submit'] == 'Update'){	
		$data_agreements = $_POST;
		$data_agreements['created_date'] = nowDate();
		$data_agreements['created_by'] = $_SESSION['userid'];
		$data['updated_date'] = nowDate();
		$data['updated_by'] = $_SESSION['userid'];
		$vendor_id1 = $_POST['vendor_id'];
		unset($data['vendor_id']);
		$data_agreements['vendor_id'] = $vendor_id1;
		$url = API."?mode=updaterowbasepimarydata&rid=id:".$_POST['hid']."&table=".$transaction_table."&method=post";
		$response = file_post_contents($url, $data, 'update');
		$response250 = json_decode($response,1);
		//echo "<pre>"; print_r($response250); echo "</pre>";
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
					$response = file_post_contents($url, $postdata, 'update');
				}else {
		    		//insert
					//echo "<pre>";print_r($postdata);
					$postdata['created_date'] = $data['created_date'];
					$postdata['created_by'] = $data['created_by'];
					$url = API."?mode=adddata&table=".TBL_VENDOR_AGREEMENTS."&method=post";
					$response = file_post_contents($url, $postdata, 'insert');
				}
			}
		}
		if ($response) {
			  //header("location:index.php?option=$view_url");
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



$profilereadiness = array("Basic Info"=>array('title','first_name','middle_name','last_name','email','phone','suffix','company','display_name','gsttype','gstin','bill_with_parent','language_id','hold_reason','image'),"Shipping Details"=>array('shipping_address','shipping_post_code','shipping_country','shipping_state','shipping_city'),"Billing Adderss"=>array('contact_person','mobile','fax','other','website_url','billing_address','Pincode','billing_country','billing_state','billing_city'),"Tax Info"=>array('vendor_tax_reg_no','cst_reg_no','pan_no','tds_apply'),"Payment & Billing"=>array('pref_payment_method','payment_terms','preferred_deliver_method','opening_balance','asof','pay_with_me','credit_limit'),"Sale & Discount"=>array('default_discount_perc','payment_discount_perc','sales_type','np_negotiatable','first_pay','current_pay','gaps_in_employment_reason','performance_ratings'));



?>
<script type="text/javascript">
	<?php if($_GET['T1'] =="task_tab"){ ?>
document.onreadystatechange = function () {
  	if (document.readyState == "complete"){
		getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab');
	} 
}
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
                    document.getElementById(element_id).innerHTML = "";
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

function getAjaxDynamicPage(option,divid,pagetype){
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
        success:function(data){
            $('#'+divid).html(data);
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        error:function (){}
    });
}
</script>

<div class="card mb-1 mb-xl-10">
	<div class="card-body pt-9 pb-0">
		<div class="banner">
			<img src="assets/images/user-profile/bg-img1.jpg" class="w-100 b-radius-20" alt="">
		</div>
	</div>
	<div class="profile-img" style="margin:-40px 0 0 0;">
		<div class="row align-items-bottom">
			<div class="col-lg-1"></div>
			<div class="col-lg-4">
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
								<?php if($vendordetail['established_year']!=""){  ?>
									<b class="f-w-600">Established Year : </b><?php echo $vendordetail['established_year'];?>&nbsp;
								<?php } if($vendordetail['company']!=""){  ?>
									<b class="f-w-600">company : </b><?php echo $vendordetail['company']; ?>&nbsp;
								<?php }  ?>
							</div>
						</div>
					</div>
					<div class="col-lg-7 p-3">
						<div class="row mt-5">
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
						</div>
						
					</div>
					<?php if($transaction_table == "vendor_registers"){ ?>
					<span class="text-info text-right p-r-50">* Registration in-progress</span>
					<?php } ?>
				</div>

			</div>

		</div>

		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-8">
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
					<div class="col-lg-4 text-right">
						<?php if($vendordetail['vendor_id'] =="" AND in_array(VMS_ADMIN_ROLE,(array)$_SESSION['role_ids']) AND $totalapprovedtask == $totaltasks AND $transaction_table == "vendor_registers"){ ?>
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
							    	<?php if(($vendordetail['vendor_id'] =="" AND $transaction_table == "vendor_registers") OR $transaction_table == "vendors"){ ?>
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
<?php /** ?>
			<div class="tab-pane" id="task_tab" role="tabpanel">
				<div class="row">
				  <div class="col-lg-6">
				<div class="card">
				  <div class="card-header  ">
				    <h5>Tasks  <?php if($rejetcedcnt != 0){ ?>&nbsp;<label class="form-label label label-md label-danger float-right"><?php echo "Reject : ".$rejetcedcnt; ?></label><?php } ?></h5>
				  </div>
				  <div class="accordion-desc">
				   <div class="accordion-block color-accordion-block">
				    <div class="color-accordion" id="color-accordion">
				      <?php if(count((array)$transactionactivityschedule) != 0){  
				        foreach($transactionactivityschedule as $key1=>$transactionactivityschedule){ ?>
				          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $taskscategories[$key1];?> 
				          <label class="form-label label label-md label-info float-right"><?php echo "Task : ".count((array)$transactionactivityschedule); ?></label>
				        </a> 
				        <div class="accordion-desc">
				          <table class="table table-hover">
				            <tbody>
				              <?php foreach($transactionactivityschedule as $tas){ ?>
				                <tr id="custom-popup" >
				                  <td class="w-b-w-s" onclick="showUpdateChecklist('<?php echo $tas['id']; ?>')">
				                   <h6 class="mb-0 f-14" >
				                    <?php if($tas['link_page'] == '1'){?>
				                      <i class="ti-check-box"></i>
				                    <?php  }else{ ?>
				                     <i class="ti-new-window"></i>
				                   <?php } ?>
				                   <?php echo $tas['task_name']; ?></h6>
				                 </td>
				                 <td align="center" >
				                   <?php $stausresultv = $stausresult[$tas['current_status']]; 
				                   ?>
				                   <span  class="statusid_<?php echo $tas['id']; ?>"><label class="form-label label label-success" style="color: <?php echo "#".$stausresultv['color']; ?>;    background:  <?php echo $stausresultv['background_color']; ?>; border-color: <?php echo $stausresultv['background_color']; ?>;" ><?php echo $stausresultv['name']; ?></label></span>
				                   <?php if($tas['check_list'] !=""){ ?>
				                    <div class="custom-popup-open custom-popup-slide-updatechecklist-<?php echo $tas['id']; ?> text-start  " style="display: none;left: 18px;top: 55px; ">
				                      <div class="card">
				                        <div class="card-header">
				                          <h5>Task</h5>
				                          <div class="card-header-right"> 
				                            <i class="ti-close" onclick="closeSmallPopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
				                          </div>
				                        </div>
				                        <div class="card-body custom-scroll <?php echo $tas['assigned_to'] != $_SESSION['userid']?"custom_readonly":"";  ?>" style="height: 300px;">
				                        <?php if($tas['assigned_to'] != $_SESSION['userid']){ ?> 
				                        	 <span class="text-danger"><i class="ti-alert"></i> <?php echo $_SESSION['error_message']['9002']; ?></span>
				                        	<?php } ?>
				                          <table width="100%">
				                            <tbody>
				                              <?php 
				                              $check_list = json_decode($tas['check_list'], 1);
				                              foreach($check_list as $keyval=>$list){   
				                                $checkboxcl=$classcheck="";
				                                if($list['checklist_completed']=="Y"){ 
				                                  $taskstatus++; 
				                                  $classcheck="check-task check-task done-task";
				                                  $checkboxcl = "checked disabled";
				                                } 
				                                ?>
				                                <tr>
				                                 <td width="90%" class="w-b-w-s">
				                                  <div class="to-do-list">
				                                    <div class="checkbox-fade fade-in-primary" onchange="getTaskCheckList('<?php echo $tas['id']; ?>','<?php echo $keyval; ?>','<?php echo $vendordetail['id']; ?>')">
				                                      <label class="form-label check-task <?php echo $classcheck; ?> campliancecros_<?php echo $tas['id']."_".$keyval; ?>">
				                                        <input type="checkbox" value="" <?php echo $checkboxcl; ?>  class="checkcheckboxcopliance_<?php echo $tas['id']."_".$keyval; ?>" >
				                                        <span class="cr">
				                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
				                                        </span>
				                                        <span style="display: table;"><?php echo $list['name'];?> <?php if($list['mandatory_checklist']=="Y"){ ?><span class="text-danger">*</span><?php } ?></span>
				                                      </label>
				                                    </div>
				                                  </div>
				                                </td> 
				                                <td width="10%">
				                                  <span class="float_end">
				                                    <?php
				                                    if(trim($list['downloadfile']) !="" AND strlen($list['downloadfile']) !='0'){ ?>
				                                      <a href="<?php echo "uploads/all/".$list['downloadfile']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Demo Form Download" download><i class="ti-cloud-down"></i></a> 
				                                      <?php
				                                    }if(trim($list['attachment_name']) !="" AND strlen($list['attachment_name']) !='0'){ 
				                                     $path = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$transaction_table."/".$vendordetail['id']."/Documents/";
				                                     ?>
				                                     <a href="<?php echo $path.$list['attachment_name']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Uploaded Form Download" download><i class="ti-cloud-down m-l-20"></i></a>
				                                   <?php } if($tas['link_page'] == '0'){ ?> 
				                                     <a href="<?php echo $tas['page_url'];  ?>" target="_blank" onclick="UpdateTransactionActivitySchedule1('<?php echo $tas['id']; ?>')" class="text-primary"><h6 class="mb-0 f-14" ><i class="ti-new-window"></i></h6></a>
				                                     <?php } ?>
				                                   </span>
				                                 </td>
				                               </tr>
				                             <?php }  ?>
				                           </tbody>
				                         </table>
				                       </div>
				                     </div>
				                   </div>
				                 <?php } ?>
				                </td>

				             </tr>
				           <?php } ?>
				         </tbody>
				       </table>
				     </div>
				   <?php }  }else{ ?>
				     <div class="card-px text-center py-20 mt-2">
				      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>   
				      <div class="text-center px-4">
				        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
				      </div>   
				    </div> 
				  <?php } ?>
				</div>
				</div>
				</div>
				</div>
				  </div>
				  <div class="col-lg-6">
				    <div class="card">
				            <div class="card-header  ">
				                <h5>Review Pending  &nbsp;<label class="form-label label label-md label-warning float-right"><?php echo $totaltaskreview_cnt;?> </label></h5>
				            </div>  
				            <div class="card-body">
				                      <div class="accordion-desc">
				   <div class="accordion-block color-accordion-block">
				    <div class="color-accordion" id="color-accordion">
				      <?php if(count((array)$transactionactivityschedulecompletedstatus) != 0){  
				        foreach($transactionactivityschedulecompletedstatus as $key1=>$tascs){ ?>
				          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $taskscategories[$key1];?>
				        </a> 
				        <div class="accordion-desc p-0">
				            <table class="table table-hover">
				            <tbody>
				              <?php foreach($tascs as $tas){ //print_r($tas); ?>
				                <tr id="custom-popup" >
				                  <td class="w-b-w-s" onclick="showUpdateChecklist('qq<?php echo $tas['id']; ?>')">
				                   <h6 class="mb-0 f-14" >
				                    <?php if($tas['link_page'] == '1'){?>
				                      <i class="ti-check-box"></i>
				                    <?php  }else{ ?>
				                     <i class="ti-new-window"></i>
				                   <?php } ?>
				                   <?php echo $tas['task_name']; ?> 
				               </h6>
				               <?php if($tas['approval_user_id'] != $_SESSION['userid']){ 
                   					echo  "<span class='text-danger'>".$_SESSION['error_message']['9002']." Accept/Decline"."</span>";

                   				} ?>
				                 </td>
				                 <td align="center" class="statusid_<?php echo $tas['id']; ?>">
				                   <?php 
				                      $stausresultv = $stausresult[$tas['current_status']]; 
				                   ?>
				                   <?php if($tas['approval_user_id'] == $_SESSION['userid']){ ?>
				                   <button class="btn waves-effect waves-light btn-success btn-icon" onclick="updateTaskStatus('<?php echo $tas['id']; ?>','<?php echo ST_APPROVED; ?>')" ><i class="ti-thumb-up f-18" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Approve"></i></button>
                   					<button class="btn waves-effect waves-light btn-danger btn-icon"><i class="ti-thumb-down f-18" onclick="updateTaskStatus('<?php echo $tas['id']; ?>','<?php echo ST_REJECTED; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Reject"></i></button>
                   				<?php } ?>
				                   <?php if($tas['check_list'] !=""){ ?>
				                    <div class="custom-popup-open custom-popup-slide-updatechecklist-qq<?php echo $tas['id']; ?> text-start " style="display: none;right: 100px;top: 55px; width: 550px;">
				                      <div class="card">
				                        <div class="card-header">
				                          <h5>Task</h5>
				                          <div class="card-header-right">
				                            <i class="ti-close" onclick="closeSmallPopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
				                          </div>
				                        </div>
				                        <div class="card-body custom-scroll <?php echo $tas['approval_user_id'] != $_SESSION['userid']?"custom_readonly":"";  ?>" style="height: 300px;"> 
				                        	<?php if($tas['approval_user_id'] != $_SESSION['userid']){ ?> 
				                        	 <span class="text-danger"><i class="ti-alert"></i> <?php echo $_SESSION['error_message']['9002']; ?></span>
				                        	<?php } ?>
				                          <table width="100%">
				                            <tbody>
				                              <?php 
				                              $check_list = json_decode($tas['check_list'], 1);
				                              foreach($check_list as $keyval=>$list){  
				                              //print_r($list); 
				                                $checkboxcl=$classcheck="";
				                                if($list['checklist_completed']=="Y"){ 
				                                  $taskstatus++; 
				                                  $classcheck="check-task check-task done-task";
				                                  $checkboxcl = "checked disabled";
				                                } 
				                                ?>
				                                <tr>
				                                 <td width="90%">
				                                  <div class="to-do-list event_disabled">
				                                    <div class="checkbox-fade fade-in-primary" onchange="getTaskCheckList('<?php echo $tas['id']; ?>','<?php echo $keyval; ?>','<?php echo $vendordetail['id']; ?>')">
				                                      <label class="form-label check-task <?php echo $classcheck; ?> campliancecros_<?php echo $tas['id']."_".$keyval; ?>">
				                                        <input type="checkbox" value="" <?php echo $checkboxcl; ?>  class="checkcheckboxcopliance_<?php echo $tas['id']."_".$keyval; ?>" >
				                                        <span class="cr">
				                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
				                                        </span>
				                                        <span class="w-b-w-s"><?php echo $list['name'];?> <?php if($list['mandatory_checklist']=="Y"){ ?><span class="text-danger">*</span><?php } ?></span>
				                                    </label>
				                                   <?php if($list['comment'] !=""){ ?> <span class="d-block"><span class="ti-notepad"></span> <?php echo $list['comment'];?> </span> <?php } ?>

				                                    </div>
				                                  </div>
				                                </td> 
				                                <td width="10%">
				                                  <span class="float_end">
				                                    <?php
				                                    if(trim($list['downloadfile']) !="" AND strlen($list['downloadfile']) !='0'){ ?>
				                                      <a href="<?php echo "uploads/all/".$list['downloadfile']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Demo Form Download" download><i class="ti-cloud-down"></i></a> 
				                                      <?php
				                                    }if(trim($list['attachment_name']) !="" AND strlen($list['attachment_name']) !='0'){ 
				                                     $path = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$transaction_table."/".$vendordetail['id']."/Documents/";
				                                     ?>
				                                     <a href="<?php echo $path.$list['attachment_name']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Uploaded Form Download" download><i class="ti-cloud-down m-l-20"></i></a>
				                                   <?php } if($tas['link_page'] == '0'){ ?> 
				                                     <a href="<?php echo $tas['page_url'];  ?>" target="_blank" onclick="UpdateTransactionActivitySchedule1('<?php echo $tas['id']; ?>')" class="text-primary"><h6 class="mb-0 f-14" ><i class="ti-new-window"></i></h6></a>
				                                     <?php } ?>
				                                   </span>
				                                 </td>
				                               </tr>
				                             <?php }  ?>
				                           </tbody>
				                         </table>
				                       </div>
				                     </div>
				                   </div>
				                 <?php } ?>
				               </td>
				             </tr>
				           <?php } ?>
				         </tbody>
				       </table>
				     </div>
				      <?php }  }else{ ?>
				     <div class="card-px text-center py-20 mt-2">
				      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>   
				      <div class="text-center px-4">
				        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
				      </div>   
				    </div> 
				  <?php } ?>
				   </div>
				 </div>
				</div>


						  </div>
						</div>
					</div>
				</div>
			</div> <?php **/ ?>
		</div>