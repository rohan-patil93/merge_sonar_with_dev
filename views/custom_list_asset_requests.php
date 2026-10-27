<?php 
	$url1 = API."?mode=getassettransferrequests&admin_type=".$_SESSION['admin_type']; 
	$data_details1 = callAPI($url1);
	$result = json_decode($data_details1,1);
	//echo "<pre>"; print_r($result); echo "</pre>";
   	$asset_requests = $result['asset_requests'];
   	$asset_categories = $result['asset_categories'];
   	$locations = $result['locations'];
?>
<script type="text/javascript">
	function requestAssets(rid=""){
	jQuery.ajax({
		url: "views/custom_insert_asset_request_transfers.php",
		data: "rid="+rid,
		type: "POST",
		success:function(data){
			$('#dynamic_popup11').html(data);
			$('#kt_modal_dynamic_popup2').modal("show");
			$(".js-example-basic-single").select2();
				$(".js-example-disabled-results").select2();
				$( ".datepicker" ).datepicker({
					showOn: "both",
					buttonText: "calendar",
					dateFormat: date_format,
					yearRange: "-60:+10",
					setDate: new Date(),
					changeMonth: true,
					changeYear: true,
					beforeShow: function (input, inst) {
					 var rect = input.getBoundingClientRect();
					 setTimeout(function () {
					 inst.dpDiv.css({ top: rect.top + 10, left: rect.left + 0 });
					 }, 0);
					 }
					}).next("button.ui-datepicker-trigger")
		      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
		}
	});
}

</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid" >
		<div class="row">
			<div class="col-lg-12">
				<div class="card card-custom card-sticky" id="kt_page_sticky_card">
					<div class="card-header mb-3">
						<h5>Assets Request</h5>
						<div class="card-header-right">
							<a href="javascript:void(0)" onclick="history.go(-1)" role="button" class="btn waves-effect waves-light">
								<i class="icofont icofont-reply"></i>
								Back
							</a>
						</div>
					</div>
					<div class="card-body">
						<?php if(count((array)$asset_requests) !=0){ ?>
						<div class="dt-responsive table-responsive">
							<table id="basic-btn" class="table table-striped table-bordered nowrap"> 
								<thead>
									<tr>
										<th>User</th>
										<th>Asset Category</th>
										<th>Request Date</th>
										<th>Description</th>
										<th>Location</th>
										<th>Required From</th>
										<th>Required To</th>
										<th>Request qty</th>
										<th>Transfers qty</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($asset_requests as $requestdeatil){ ?>
										<tr class="<?php echo $varianc_class; ?>">
											<td><?php echo $_SESSION['users'][$requestdeatil['originator']]; ?></td>
											<td><?php echo $asset_categories[$requestdeatil['category_id']]['category_description']; ?></td>
											<td><?php echo $requestdeatil['request_datetime'] !=""?fromDate($requestdeatil['request_datetime']):"-"; ?></td>
											<td><?php echo $requestdeatil['description']; ?></td>
											<td><?php echo $locations[$requestdeatil['location_id']]['location_name']; ?></td>
											
											<td><?php echo $requestdeatil['asset_required_start_date'] !=""?fromDate($requestdeatil['asset_required_start_date']):"-"; ?></td>
											<td><?php echo $requestdeatil['asset_required_end_date'] !=""?fromDate($requestdeatil['asset_required_end_date']):"-"; ?></td>
											<td class="text-right"><?php echo $requestdeatil['quantity'] !=""?$requestdeatil['quantity']:"0"; ?></td>
											<td class="text-right"><?php echo $requestdeatil['asset_request_transfers_cnt'] !=""?$requestdeatil['asset_request_transfers_cnt']:"0"; ?></td>
											

											<td class="text-center"> 
												<i class="fa fa-eye" onclick="requestAssets('<?php echo $requestdeatil['id']; ?>')"></i>
                                    		</td>
										</tr>
									<?php } ?>

								</tbody>
							</table>
						</div>
						<?php }else{ ?>
							<div class="card-px text-center ">
								<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Asset Requists</p>   
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