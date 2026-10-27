<?php
if($_GET['refresh'] == "Y"){
	include_once('../includes/session.php');
	include_once('../dbconfig.php');
	include_once('../comm.php');
}
$itsum_company_id = $_GET['company_id'];
$itsum_data_name = $_GET['data_name'];
$itsum_user_id = $_GET['user_id'];
$apiurl = "https://dev.infistaq.in/api.php";
if($_GET['refresh'] =="Y"){
	$postdata = array('company_id'=>$itsum_company_id,'data_name'=>$itsum_data_name,'user_id'=>$itsum_user_id);
	$sendpostreq = json_encode($postdata);
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://aidev.digixlabs.cloud/api/stock/refresh',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>$sendpostreq,
	  CURLOPT_HTTPHEADER => array(
	    'API-TOKEN: X9mQ2Zp7Lk4T8wE5rV1yU0cN6bH3sAqD',
	    'Accept: */*',
	    'Accept-Encoding: gzip, deflate, br',
	    'Connection: keep-alive',
	    'Content-Type: application/json'
	  ),
	));
	$response23 = curl_exec($curl);
	$decores = json_decode($response23,1);
	$returnresult = array('company_id'=>$itsum_company_id,'application_table'=>$transaction_table,'transaction_id'=>$transaction_id,'api_master_id'=>47,'api_url'=>'','api_request_parameters'=>$sendpostreq,'request_date_time'=>date("Y-m-d H:i:s"),'api_response_details'=>urlencode(json_encode($decores)),'response_date_time'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'0','created_date'=>date("Y-m-d H:i:s"),'created_by'=>$itsum_user_id,'response_status'=>200);
 	$inserturl = API."?mode=adddata&table=api_hits&method=post&company_id=".$itsum_company_id."&data_name=".$itsum_data_name."&userid=".$itsum_user_id; 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $inserturl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $returnresult);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$insertresponse = curl_exec($ch);
	$decore11 = json_decode($insertresponse,1);
}else{
	
	$data1["where"] = array('id'=>556);
	$data1["field"] = "id,request_date_time,api_response_details";
	$data1['order'] = "id DESC";
	$data = urlencode(json_encode($data1));
	
	$curl = $apiurl."?mode=gettabledetailswithconditions&table=api_hits&company_id=".$itsum_company_id."&data_name=".$itsum_data_name."&userid=".$itsum_user_id."&data=".$data; 
	$cudata = callAPI($curl);
	$returnresult11 = json_decode($cudata,1);
	$returnresult = $returnresult11[0];
	$returnres11 = $returnresult['api_response_details'];
	$decores = json_decode(urldecode($returnres11),1);
}
$itemaummaryary = array();
foreach($decores['flagged_items'] as $flagitem){ 
 	if($flagitem['status'] == 'WARNING'){
 		$itemaummaryary['WARNING'][] = $flagitem;
 	}elseif($flagitem['status'] == 'CRITICAL'){
 		$itemaummaryary['CRITICAL'][] = $flagitem;
 	}elseif($flagitem['status'] == 'INFO'){
 		$itemaummaryary['INFO'][] = $flagitem;	
 	}
}
$loopstatus = array('WARNING','CRITICAL','INFO');
?>

<div class="page-body">
	<div class="card mt-4">
		<div class="card-header">
			<div class="row align-items-center">
				<div class="col-lg-8">
					<h3 class="mb-0 f-w-600">Stock Alert Report</h3>
					<p class="mb-0"> <i class="fa fa-clock-o"></i> Last refreshed: <?php echo $returnresult['request_date_time']; ?></p>
				</div>
				<div class="col-lg-4 text-end">
					<button class="btn btn-primary itemSummaryRefreshPage" data-cid="<?php echo $itsum_company_id; ?>" data-dnm="<?php echo $itsum_data_name; ?>"  data-udid="<?php echo $itsum_user_id; ?>" id="refreshBtn" ><i class="fa fa-refresh"></i> Refresh</button>
				</div>
			</div>
		</div>
	</div>

	<div class="dashboard-tabs">
		<div class="tab-card critical itemSummaryfilterTable"  data-status="CRITICAL">
			<h2 id="count-critical"><?php echo $decores['critical_count']; ?></h2>
			<p>CRITICAL</p>
		</div>
		<div class="tab-card warning itemSummaryfilterTable"  data-status="WARNING">
			<h2 id="count-warning"><?php echo $decores['warning_count']; ?></h2>
			<p>WARNING</p>
		</div>
		<div class="tab-card info itemSummaryfilterTable"  data-status="INFO">
			<h2 id="count-info"><?php echo $decores['info_count']; ?></h2>
			<p>INFO</p>
		</div>
		<div class="tab-card total" >
			<h2 id="count-total"><?php echo $decores['total_items']; ?></h2>
			<p>TOTAL ITEMS</p>
		</div>
	</div>
	<table class="mb-4" id="stockTable">
		<thead>
			<tr>
				<th>ITEM</th>
				<th>STOCK NUMBERS</th>
				<th>ISSUE</th>
				<th>ACTION</th>
			</tr>
		</thead>
		<tbody id="tableBody">
			<?php
			foreach($loopstatus as $lstat){
			 if(count((array)$itemaummaryary[$lstat]) !='0'){ 
					foreach($itemaummaryary[$lstat]  as $flagitem){ ?>
			
			<tr class="stock-row row-<?php echo $flagitem['status']; ?> <?php echo $lstat == "CRITICAL"?"":"d-none"; ?>">
				<td class="w-b-w-s">
					<strong><?php echo $flagitem['item_name']; ?></strong><br>
					<span class="status-badge"><?php echo $lstat; ?></span><br>
					<div class="mt-1">
						<?php foreach($flagitem['rules_triggered'] as $ruletag){ ?>
						<b class="bg-gray f-10 d-inline-block p-1 mt-1"><?php echo $ruletag; ?></b>
						<?php } ?>
					</div>
				</td>
				<td >In Stock: <?php echo $flagitem['balance_qty']!=""?$flagitem['balance_qty']:0; ?><br>
					Min Level: <?php echo $flagitem['minimum_level']!=""?$flagitem['minimum_level']:0; ?><br>
					Danger Level: <?php echo $flagitem['danger_level']!=""?$flagitem['danger_level']:0; ?><br>
					On Order: <?php echo $flagitem['inprogress_qty']!=""?$flagitem['inprogress_qty']:0; ?><br>
					Orders Waiting: <?php echo $flagitem['only_created_qty']!=""?$flagitem['only_created_qty']:0; ?><br>
					Available: <?php echo $flagitem['effective_available']!=""?$flagitem['effective_available']:0; ?><br>
					Days Left: <?php echo $flagitem['days_until_stockout']!=""?$flagitem['days_until_stockout']:0; ?><br>
				</td>
				<td class="w-b-w-s"><?php echo $flagitem['gpt_issue']; ?></td>
				<td class="w-b-w-s"><?php echo $flagitem['gpt_recommendation']; ?></td>
			</tr>
			<?php } }else{ ?>
			<tr class="stock-row row-<?php echo $lstat; ?> <?php echo $lstat == "CRITICAL"?"":"d-none"; ?>">
				<td class="w-b-w-s text-center" colspan="4"><i class="ti-alert"></i> No Data</td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>