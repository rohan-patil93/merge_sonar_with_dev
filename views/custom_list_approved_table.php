<?php 
//$data = array();
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
$_SESSION['apply_js']['table']= 1;
$_SESSION['apply_js']['multiselest'] = '1';
if (isset($_POST['submit'])){
	if($_POST['submit'] == 'search'){
		//print_r($_POST); exit;
		$array =  array('category_id', 'vendor_id', 'cost_center_id', 'location_id','opex_cpex','from','to');
		foreach ($array as  $value) {
			if($_POST[$value] != ''){
				$ress[$value] = $_POST[$value];
			}
		}
		// $data1["where"] = $ress;
		// $data = urlencode(json_encode($data1));

		//print_r($data); exit;
	}
}
$table_name = $_REQUEST['table'];
if(!isset($_POST['from'])){
	$_POST['from'] = date('Y-m-d', strtotime('-1 month'));
	$ress['from'] = date('Y-m-d', strtotime('-1 month'));
}
if(!isset($_POST['to'])){
	$_POST['to'] = date('Y-m-d');
	$ress['to'] = date('Y-m-d');
}
$_POST['get_table'] = $table_name;
$_POST['company_id'] = $_SESSION['company_id'];
$_POST['user_id'] = $_SESSION['userid'];
$_POST['clone_table'] = $_REQUEST['clone'];
$_POST['table'] = $table_name;
$_POST['get_display_table'] = $_POST['get_display_table']!="" ? $_POST['get_display_table'] : 'vendors';
$_POST['get_option'] = $_GET['option'];

//echo "<pre>"; print_r($_POST); echo "</pre>";
$tableurl = API."?mode=serchforpos&method=post";
$response = file_post_contents($tableurl, $_POST, '');
$result = json_decode($response,1);
//echo "<pre>"; print_r($result); echo "</pre>";
$quotations = $result['quotations'];
$purchase_orders = $result['purchase_orders'];
$data = $result['transaction_details'];
$already_trasnaction = $result['already_trasnaction'];
$capexopex = $result['capexopex'];
$datacost = $result['cost_centers'];
$datalocation = $result['locations'];
$datavendor = $result['vendors'];
$dis_list = $result['display_attributes'];
$status_progress = $result['status_progress'];
// $tablecat = API."?mode=gettabledetails&table=".TBL_ITEM_CATEGORIES;
// $rescat = callAPI($tablecat);
// $datacat = json_decode($rescat,1);


// $tablecost = API."?mode=gettabledetails&table=".TBL_COST_CENTERS;
// $rescost = callAPI($tablecost);
// $datacost = json_decode($rescost,1);
// $tablelocation = API."?mode=gettabledetails&table=".TBL_LOCATIONS;
// $reslocation = callAPI($tablelocation);
// $datalocation = json_decode($reslocation,1);
// $tablevendor = API."?mode=gettabledetails&table=".TBL_VENDOR;
// $resvendor = callAPI($tablevendor);
// $datavendor = json_decode($resvendor,1);

$content = $data['help_text']!='' ? $data['help_text'] : "Help Test"; 
$rr_data = toGetpageOptionName($table_name,$_REQUEST['option']);
//echo "<pre>";print_r($rr_data);echo "</pre>";
$nextlevelsteptable = $_REQUEST['clone'] !=""?$_REQUEST['clone']:$rr_data['genrate_next_level'];
$rr_data['genrate_next_level'] = $nextlevelsteptable;
$pr_data = toGetpageOptionName($nextlevelsteptable);

// $pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$table_name;
// $page_response = callAPI($pageurl);
// $dis_list = json_decode($page_response,1); 
$lists = json_decode($dis_list['list_attributes'],1);

$cus_list = $lists['party_id'];
$selectdata = dyanamicselectdata($table_name,'party_id');


$vdata1["where"] = array('application_table_name'=> $table_name,'field_name'=>'party_id');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=display_attribute_types&data=".$vdata; 
$configesponse = callAPI($vurl);
$return = json_decode($configesponse,1);

$return = $return['0'];
$return_data = json_decode(urldecode($return['content_details']),1);
$get_table =$return_data['table_name'];
//$get_table =$_GET['table']; //$return_data['table_name'];
$viewpageotion = getPagenametotable($table_name,'insert_page_name');

$stausresult = ViewStatusData();
$get_option = $_REQUEST['option'];
//echo $_REQUEST['option'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		filterSubmitForm();	
	});
</script>
<script type="text/javascript">
	//$(document).ready(function(){
		$(document).on('click', '#checkall1', function() {
			alert('haii');
	  // $(".itemRow").prop("checked", this.checked);           

	});
		function chackd(e){
			alert(check);  
			var checkboxes = document.getelementbyid('check');
			if (e.checked) {
				for (var i = 0; i < checkboxes.length; i++) { 
					checkboxes[i].checked = true;
				}
			} else {
				for (var i = 0; i < checkboxes.length; i++) {
					checkboxes[i].checked = false;
				}
			}
		}
		/*});*/

		function changestatus(val,tranid,allowitemid,party_type=""){
			var x = val;
			var table_name = '<?php echo $table_name; ?>'
			jQuery.ajax({
				url: "views/custom_quotation_vender_pop.php",
				data:'table='+table_name+'&field='+x+'&rid='+tranid+'&tranid='+tranid+'&allowitemid='+allowitemid+'&party_type='+party_type,
				type: "POST",
				success:function(data){
					$('#dynamic_popup1').html(data);
					$('#kt_modal_dynamic_popup').modal("show");
					$(".js-example-basic-multiple").select2(); 
				},
				error:function (){}
			});
		} 
		function filterSubmitForm(){
			var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    		$("#filterpageshow").html(loader);
			var frmdetails = $("#filterformid").serialize();
			jQuery.ajax({
				url: "views/custom_list_approved_table_ajax.php",
				data: frmdetails,
				type: "POST",
				success:function(data){
					$("#filterpageshow").html(data);
					$('[data-bs-toggle="tooltip"]').tooltip();
					$( ".datepicker" ).datepicker({
			            showOn: "both",
			            buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
			            //dateFormat: date_format,
			            yearRange: "-60:+10",
			            setDate: new Date(),
			            changeMonth: true,
			            changeYear: true
			        }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
				},
				error:function (){}
			});
			
		}

		function dynamicSelectDateRange(type,name,start="",end=""){
		$("#btn_date").text(name);
		$("#pick_date").val(name);
		$("#shortcode_date").val(type);
		$(".empty_active").removeClass("active");
		$("#"+type).addClass("active");
		if(name == "Custom Date"){
			$("#custome_date").show('slow');
			
		}else{
			$("#custome_date").hide();
			$("#from_date").val(start);
			$("#to_date").val(end);
		}
	}	


	</script>
				<?php  /** if($content !=''){ ?>


			<div class="alert alert-warning alert-dismissible">
				<strong><?php echo $content; ?></strong>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php } **/?>
<div class="card" >
	
	<div class="card-header">
		<h5>Approved <?php echo $rr_data['page_title']!="" ? $rr_data['page_title'] : ucwords($_REQUEST['table']); ?></h5>
		<?php if($_POST['ajaxpage'] != 'Y'){ ?>
		<div class="card-header-right">
			<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
				<i class="icofont icofont-reply"></i>
				Back
			</a>
		</div>
		<?php } ?>
	</div>

	<div class="card-custom mt-4">
		<div class="card-body">
			<form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post" id="filterformid">
				<div class="row kt-margin-b-20">
					<div class="col-lg-2 kt-margin-b-10-tablet-and-mobile mb-2">
						<!-- <label  class="form-label"><?php echo $cus_list['lable']; ?>:</label> -->
						<select class="form-control form-select js-example-basic-single"  name="vendor_id">
							<option value=""> All <?php echo $cus_list['lable']; ?> </option>
							<?php foreach ($selectdata as $selectkey => $selectvalue) {  ?> 
								<option  <?php if($ress['vendor_id'] == $selectkey  ){ ?> selected <?php } ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
							<?php  } ?>
						</select>
					</div>
					<div class="col-lg-2 kt-margin-b-10-tablet-and-mobile mb-2">
						<!-- <label  class="form-label"><?php //echo "Capex/Opex"; ?>:</label> -->
						<select class="form-control form-select js-example-basic-single" id="kt_select2_42" name="opex_cpex">
							<option value=""> All CAPEX / OPEX </option>
							<?php foreach($capexopex as $selectcat){ ?>
								<option  <?php if($ress['opex_cpex'] == $selectcat['genc_detail_code'] ){ ?> selected <?php } ?> value="<?php echo $selectcat['genc_detail_code']; ?>"><?php echo $selectcat['genc_detail_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<!-- <div class="col-lg-2 kt-margin-b-10-tablet-and-mobile mb-2">
						<select class="form-control form-select js-example-basic-single" id="kt_select2_43" name="cost_center_id">
							<option value=""> All Cost Center </option>
							<?php foreach($datacost as $selectcost){ ?>
								<option   <?php if($ress['cost_center_id'] == $selectcost['id'] ){ ?> selected <?php } ?> value="<?php echo $selectcost['id']; ?>"><?php echo $selectcost['cost_center_name']; ?></option>
							<?php } ?>
						</select>
					</div> -->
				<?php /** ?>	
					<div class="col-lg-2">
						<label class="form-label">Start Date <span class="text-danger">*</span></label>
						<input type="text" class="form-control datepicker" name="from" placeholder="From" data-col-index="5" value="<?php echo $ress['from']; ?>" required/>
					</div>
					<div class="col-lg-2">
						<label class="form-label">End Date <span class="text-danger">*</span></label>
						<input type="text" class="form-control datepicker" name="to" placeholder="To" data-col-index="5" value="<?php echo $ress['to']; ?>" required="" />
					</div> 
<?php **/ ?>



<?php /**/ $shortcode = "TM"; $from_date = fromDate(date("Y-m-01")); $to_date =fromDate(date("Y-m-t")); ?>
<div class="col-lg-4 mb-2">
								<!-- <span class="form-label d-block">Transaction Date</label>	 -->
								<input type="hidden" name="pick_date" id="pick_date" value="Today">
								<input type="hidden" name="shortcode_date" id="shortcode_date" value="TD">
							<div class="btn-group dropdown-split-primary" style="width: 100%;">
							    <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
							    	<span id="btn_date">This Month</span>
							    </button>
							    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
							        <span class="visually-hidden">Toggle primary</span>
							    </button>
							    <div class="dropdown-menu">
							       	<a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TD"?"active":"";?> empty_active" id="TD" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TW"?"active":"";?> empty_active" id="TW" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week')));?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday ')));?>')">This Week</a>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TM"?"active":"";?> empty_active" id="TM" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TY"?"active":"";?> empty_active" id="TY" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
							        <div class="dropdown-divider"></div>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="YD"?"active":"";?> empty_active" id="YD" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>')">Yesterday</a>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PW"?"active":"";?> empty_active" id="PW" href="#!" onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PM"?"active":"";?> empty_active" id="PM" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01",strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t",strtotime('last month'))); ?>')">Previous Month</a>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PY"?"active":"";?> empty_active" id="PY" href="#!" onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01",strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31",strtotime('last year'))); ?>')">Previous Year</a>
							        <div class="dropdown-divider"></div>
							        <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="CD"?"active":""; ?> empty_active" id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
							    </div>
							</div>
							<div class="row mt-2" style="display: none" id="custome_date">
							    <div  class="col-lg-6">
									<input type="text" class="form-control filterdynamic datepicker" name="from" id="from_date" value="<?php echo $from_date; ?>">
								</div>
								<div class="col-lg-6">
									<input type="text" class="form-control filterdynamic datepicker" name="to" id="to_date" value="<?php echo $to_date; ?>">
								</div>
							</div>				            
						</div> 

<?php /**/ ?>



				
				<input type="hidden" name="table" value="<?php echo $table_name; ?>">
				<input type="hidden" name="get_table" value="<?php echo $table_name; ?>">
				<input type="hidden" name="clone" value="<?php echo $nextlevelsteptable; ?>">
				

				<?php $csheck = $cus_list['lable']=="vendor" ?"vendors":"application_users"; ?>
				<input type="hidden" name="get_display_table" value="<?php echo $get_table; ?>">
				<input type="hidden" name="get_option" value="<?php echo $get_option; ?>">
				<div class="col-lg-2">
					<div class="col-lg-12">
						<input type="hidden" name="submit" value="search" />
						<button class="btn btn-primary btn-brand--icon" id="kt_search" onclick="filterSubmitForm()" type="button" style="height:40px;">
							<span class="f-12">
								<i class="ti-search m-r-2"></i>
								<span>Search</span>
							</span>
						</button>
					</div>
				</div>
				</div>
			</form>
			<!--begin: Datatable -->
			<div class="card card-custom mt-4">
				<div class="card-body" id="filterpageshow">		
					<?php 	//include_once "views/custom_list_approved_table_ajax.php"; ?>
					<div class="card-px text-center ">
							<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Please Apply Filter</p>   
							<div class="text-center px-4">
								<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
							</div>   
						</div>
				</div>
			</div>
		</div>
	</div>
</div>
