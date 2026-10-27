<?php
$_SESSION['apply_js']['multiselest'] = '1';
$from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-01-01")); 
$to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-12-31"));
$getapi = API."?mode=getslatrackinglogreports&customer_id=".$_POST['customer_id']."&from_date=".$from_date."&to_date=".$to_date; 
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
//echo "<pre>"; print_r($result); echo "</pre>";
$dynamic_mapping_tables = $result['dynamic_mapping_tables'];
$last_account_balances = $result['last_account_balances'];
//$customers = $result['customers'];
?>

<script type="text/javascript">
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
		gettTransactionIDRecords();
	}
	function gettTransactionRecords(){
		var transaction_table =$("#transaction_table").val();
		$.ajax({
			url: "ajax.php",
			data:'page=dynmictabledata&table='+transaction_table+'&rid=is_Active&fid=0&field=id&value=transaction_number',
			type: "POST",
			success:function(data){
				$("#transaction_id").html(data);
				gettTransactionIDRecords();
			}
		});
	}
	function gettTransactionIDRecords(){
		var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    	$("#getajaxcallpage").html(loader);
		var transaction_table =$("#transaction_table").val();
		var transaction_id =$("#transaction_id").val();
		var from_date=$("#from_date").val();
		var  to_date=$("#to_date").val();
		$.ajax({
			url: "views/custom_view_sla_tracking_log_transaction_reports_ajax.php",
			data:'page=dynmictabledata&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&from_date='+from_date+'&to_date='+to_date+'&ajaxcll=Y',
			type: "POST",
			success:function(data){
				$("#getajaxcallpage").html(data);
			}
		});
	}

</script>
<div class="card">
	<div class="card-header">
		<h5>SLA Tracking Log Reports</h5>
		<div class="card-header-right table-card-header">
		</div>
	</div>
	<div class="card-body p-l-50 p-r-50">
		<form method="post">
			<div class="row">
				<!-- <div class="col-lg-3">
					<label  class="form-label">Transaction</label>
					<select class="form-select js-example-basic-single" name="transaction_table"   id="transaction_table" onchange="gettTransactionIDRecords()">
	             	<option value="">Select Any One</option>
	                <?php foreach($dynamic_mapping_tables as $dmt) {  ?>
	                   <option value="<?php echo $dmt['application_table_name']; ?>"><?php echo $dmt['page_title'];?></option>
	                <?php } ?>
	              </select>
				</div> -->
				<!-- <div class="col-lg-3">
					<label  class="form-label">Transaction No</label>
					<select class="form-select js-example-basic-single" name="transaction_id"   id="transaction_id" onchange="gettTransactionIDRecords()">
	             	<option value="">Select Any One</option>
	                <?php foreach($customers as $cust) {  ?>
	                   <option value="<?php echo $cust['id']; ?>" <?php echo $cust['id'] ==$_POST['customer_id']?"selected":""; ?>><?php echo $cust['company']." (".$cust['display_name'].")";?></option>
	                <?php } ?>
	              </select>
				</div> -->
				<div class="col-lg-6">
					<div class="row">
						 <div class="col-lg-6">
						 	<label  class="form-label">Date Range</label>
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
						</div>
						<div class="col-lg-6" style="display: none" id="custome_date">
							<div class="row" >
							    <div  class="col-lg-6">
							    	<label  class="form-label">Start Date</label>
									<input type="text" class="form-control filterdynamic datepicker" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
								</div>
								<div class="col-lg-6">
									<label  class="form-label">End Date</label>
									<input type="text" class="form-control filterdynamic datepicker" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
								</div>
							</div>				            
						</div> 
						<!-- <div class="col-lg-2">
							<label class="form-label d-block">&nbsp</label>
							<button class="btn btn-primary form-control" onclick="filterSubmitForm()" type="submit" >
								<i class="ti-search m-r-2"></i>View
							</button>
						</div> -->
					</div>
				</div>
			</div>
		</form>
	
		<div id="getajaxcallpage" class="table-responsive">
			<?php include('custom_view_sla_tracking_log_transaction_reports_ajax.php'); ?>
			<!-- <div class="card-px text-center ">
				<p class="text-muted f-16 fw-bold m-b-30"><i class="fa ti-alert"></i> Please Select Transaction Table</p>   
				<div class="text-center px-4">
					<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
				</div>   
			</div> -->
		</div>
		

			
	</div>
</div>