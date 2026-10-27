<?php
$reporttable = "cost_centers";
$reportfieldname = "cost_center_name";
$reportempfieldname = "cost_center_id";
$selecteddate = $_POST['from_date'] != ""?$_POST['from_date']:date('Y-m'); 
$url = API."?mode=yearlyprojecttimesheetreports&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&fromdate=".$selecteddate."&reporttable=".$reporttable."&reportfieldname=".$reportfieldname."&reportempfieldname=".$reportempfieldname;
$response = callAPI($url);
$roles = json_decode($response,1);
function valueemtpydash($val){
	return $val==""?" - ":$val;
}
?>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<div class="card card-custom card-sticky" id="kt_page_sticky_card">
			<!-- <div class="card-header">
				<h5 class="fl">Cost Center wise Yearly Summary</h5>
				<form class="kt-form" id="kt_form" method="post" action="" >	
					<div style="float: left; margin-left: 40px;" class="mr-3">
						<input class="form-control" type="month" name="from_date" value="<?php echo $selecteddate;?>"  id="att_month_year" />
					</div>
					<div class="float-left m-l-10">
						<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
					</div>
				</form>
				<div class="card-header-right">
					<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
						<i class="icofont icofont-reply"></i>
						Back
					</a>
				</div>
			</div>
		</div> -->
			<div class="card-header">
<div class="row">
				<div class="col-lg-3">		
					<div class="col bg-light-primary ">
						<span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
							<img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
						</span>
						<span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $_SESSION['config']['name']; ?></span>
					</div>
				</div>
				<div class="col-lg-6">	
					<h4 class="card-label text-center f-w-700">Cost center Wise Yearly Summary</h4>
				</div>
				<div class="col-lg-3">		
					<form class="kt-form" id="kt_form" method="post" action="" >
						<div class="input-group input-group-button"> 
							<input type="month" name="from_date" value="<?php echo $selecteddate;?>"  id="att_month_year"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">  <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0">
								Submit
							</button> </div>
						</form>
					</div>

				</div>
</div>
		<div class="card card-custom">
			<div class="card-body">

				<div class="dt-responsive table-responsive">
					<!-- <table id="new-cons" class="table table-striped table-bordered nowrap"> -->
							<table id="basic-btn" class="table  table-bordered nowrap">
							<thead>
								<tr> 
									<th>Cost Center Name</th>
									<?php foreach((array)$roles['months'] as $mkey => $monthlist){ ?>
										<th class="text-right"><?php echo $monthlist; ?></th>
									<?php } ?>
									<th class="text-right">Total</th>
								</tr>
							</thead>
							<tbody>

								<?php foreach((array)$roles['headlist'] as $vkey => $headlist){  $total = 0;?>
									<tr> 
										<td><?php echo $headlist;?></td>
										<?php foreach((array)$roles['months'] as $mkey => $monthlist){ 
											$totalmonth[$mkey] +=  $roles['list'][$vkey][$mkey]['vrc_billable_amount'];
											$total += $roles['list'][$vkey][$mkey]['vrc_billable_amount'];
											?>
											<td class="text-right"><?php echo valueemtpydash(customeummarmonyformateround($roles['list'][$vkey][$mkey]['vrc_billable_amount'])); ?></td>
										<?php } ?>
										<td class="text-right"><?php echo valueemtpydash(customeummarmonyformateround($total));?></td>
									</tr>
								<?php } ?>
							</tbody>
							<?php if(count((array)$roles['headlist']) != '0'){ ?>	
							<tfoot>
								<tr>
									<td> <span class="f-w-600">Cost Center Total</span> </td>
									<?php $allvendortotal = 0; foreach((array)$roles['months'] as $mkeyd => $monthlist){   $allvendortotal += $totalmonth[$mkeyd]
										?>
										<td class="text-right"><?php echo valueemtpydash(customeummarmonyformateround($totalmonth[$mkeyd])); ?></td>
									<?php } ?>	
									<td class="text-right"><?php echo valueemtpydash(customeummarmonyformateround($allvendortotal)); ?></td>
								</tr>
							</tfoot>
						<?php } ?>
						</table>
					</div>	
				</div>
			</div>
		</div>
	</div>
