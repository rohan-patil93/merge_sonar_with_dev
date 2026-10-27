<?php 
$chart_data = array();
$curl = API."?mode=blukapprovalvendorchart&approval_manager=".$_SESSION['userid']."&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR']; 
$cudata = callAPI($curl);
$vendor_charts = json_decode($cudata,1);
foreach($vendor_charts as $vendor_chart){
	$chart_data['vendor'][] = $vendor_chart['display_name'];
	$chart_data['budget'][] = $vendor_chart['esign_approved_amount'];
	$chart_data['bill'][] = $vendor_chart['bill_amount'];
} 
?>
<style type="text/css">
	h6 a {
		cursor: pointer;
	}
</style>
<!--begin::Card-->
<div class="card card-custom gutter-b">
	<div class="card-header">
		<div class="card-title">
			<h5 class="card-label">Vendor-Wise Budget Vs Actuals <?php //print_r($chart_data); ?></h5>
		</div>
	</div>
	<div class="card-body">
		<!--begin::Chart-->
		<!-- <input type="hidden" id="monthdata" value="<?php //echo implode("|", $chart_data['vendor']); ?>"> 
		<input type="hidden" id="estimationdata" value="<?php //echo implode("|", $chart_data['budget']); ?>"> 
		<input type="hidden" id="actualdata" value="<?php //echo implode("|", $chart_data['bill']); ?>">
		<div id="chart_3"></div> -->
		<!--end::Chart-->
		<!-- <?php foreach($vendor_charts as $vendor_chart){ ?> 
		<div class="d-flex align-items-center mb-10">
			<div class="symbol symbol-40 symbol-light-success mr-5">
				<span class="symbol-label">
					<img src="assets/media/svg/avatars/009-boy-4.svg" class="h-75 align-self-end" alt="" />
				</span>
			</div>
			<div class="d-flex flex-column flex-grow-1 font-weight-bold">
				<a href="#" class="text-dark text-hover-primary mb-1 font-size-lg"><?php echo $vendor_chart['display_name']; ?></a>
				<span class="text-dark">Budget Amount: 
				<span class="text-muted"><?php echo $vendor_chart['esign_approved_amount']; ?></span> </span>
				<span class="text-dark">Approved Amount: 
				<span class="text-muted"><?php echo $vendor_chart['bill_amount']; ?></span></span>
			</div>
		</div>
		<?php } ?> -->
		<div class="table-responsive">
			<table class="table table-hover m-b-0">
				<thead>
					<tr class="">
						<th class=""></th>
						<th class="" name="vendors_count"> Vendor Name</th>
						<th class="text-right" name="resources_count"> Budget Amount</th>
						<th class="text-right" name="leaves_count"> Available</th>
						
					</tr>
				</thead>
				<tbody>
					<?php foreach($vendor_charts as $vendor_chart){ //if(isset($vendors[$vendor_chart['id']])){ ?> 
						<tr>
							<td class="">
								<?php if($vendor_chart['image']!=""){ ?>
									<div class="d-flex align-items-center">
										<div class="symbol symbol-40">														
											<img src="uploads/customer/<?php echo $vendor_chart['image']; ?>" class="img-radius img-50" alt="" />
										</div>
									</div>
								<?php }else{ ?>
									<div class="img-radius img-50 align-top m-r-15 text-center userimageround"> <span class="symbol-label font-size-h5"><?php echo initials($vendor_chart['display_name']); ?></span> </div>
								<?php } ?>
							</td>
							<td class="">
								<a  class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-sm">&nbsp;
									<?php echo $vendor_chart['display_name']; ?> </a>	</br><small>(<?php echo $vendor_chart['esign_ref_no']; ?> ) Exp From : <?php echo fromDate($vendor_chart['effective_from_date']); ?>  Exp To : <?php echo fromDate($vendor_chart['effective_to_date']); ?></small>
										
								</td>
								<td class="text-right">
									<span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php  $eam= $vendor_chart['esign_approved_amount']!=""?$vendor_chart['esign_approved_amount']:"-"; echo customeummarmonyformate($eam); ?></span>

								</td>
								<td class="text-right">
									<span class="text-muted font-weight-bolder d-block font-size-lg"><?php $eba=$vendor_chart['available']!=""?$vendor_chart['available']:"-"; echo customeummarmonyformate($eba); ?> <?php if($eam<$eba){ ?> <i class="fas fa-level-down-alt text-c-red m-l-10"></i> <?php }else{ ?>  <i class="fas fa-level-up-alt text-c-green m-l-10"></i><?php } ?></span>
								</td>
								
							</tr>
						<?php } //} ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!--end::Card-->

