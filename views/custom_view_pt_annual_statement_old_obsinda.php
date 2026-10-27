<?php
//$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['table']= 1;
 $company_start = date('Y', strtotime($_SESSION['config']['created_date']));
if(isset($_POST['get_ecr_summary'])) {
	$pay_year = $_POST['pay_year'];
} else {
	$pay_year = date('Y');
}

$start_month_year_ar = explode("-", $start_month_year);
$end_month_year_ar =explode("-", $end_month_year);
//print_r($end_month_year_ar);
$company_name = $_SESSION['config']['name'];

//$component_id = EMPLOYEER_PF_CONTRIBUTION;
$salary_component_id = GROSS_SALARY;
$start_year = $start_month_year_ar[0]; 
$end_year = $end_month_year_ar[0]; 
$start_month = $start_month_year_ar[1]; 
$end_month = $end_month_year_ar[1]; 
 $vurl1 = API."?mode=togetptanualstatment&pay_year=".$pay_year."&component_code=".PT_CODE;
$configesponse = callAPI($vurl1);
$ecr_lists = json_decode($configesponse,1);
//echo "<pre>"; print_r($ecr_lists); echo "</pre>";
$ecr_list = $ecr_lists['data'];
$unique = $ecr_list['componant'];
function valueemtpydash($val){
	return $val=="" || $val=="0"?" - ":$val;
}
$list_year = $ecr_lists['pay_year']!="" ? $ecr_lists['pay_year'] : date('Y');
$now_year = date('Y');
//echo $list_year."-".$now_year;
?>
<div class="flex-column-fluid">
<div class="container-fluid ">
<div class=" flex-row ">
	<div class="card card-custom">
		<div class="card-header flex-wrap border-0 pt-4 pb-0 pl-5 ">
			<div class="card-title">
				<div class="row">
							<div class="col-lg-3">		
							<div class="col bg-light-primary ">
								<span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
									<img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
								</span>
								<span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
							</div>
						</div>
						<div class="col-lg-6">	
							<h4 class="card-label text-center f-w-700">PT Annual Return Statement </br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small></h4>
						</div>
						<div class="col-lg-3">		
							<form method="post" action="#">
								<div class="input-group input-group-button"> 
									<!-- <input type="month" name="pay_month_year" value="<?php //echo $pay_year."-".$pay_month?>"  id="example-month-input"  class="form-control form-control-sm width1" style="font-size: 18px;height: 48px !important;padding: 10px 15px;">  -->
									<select class="js-example-basic-single form-control form-control-sm width1 table-card-header" style=" border: 1px solid #00bcd4;" name="pay_year" >

										<?php for($i=$list_year;$i<=$now_year;$i++){ ?>
										<option <?php if($pay_year == $i){ echo "selected"; } ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php } ?>
									</select>
									 <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0">
										Submit
									</button> </div>
								</form>
							</div>

						</div>
			
			</div>
		</div>
		<div class="card-body ">
			<!--begin: Datatable-->
			<div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
				<div class="row">
					<div class="dt-responsive table-responsive">
							<table id="basic-btn" class=" table table-striped table-bordered nowrap">	
						<thead>
								<tr role="row" bgcolor="#f7f7f7">
									<th>#</th>
									<th>Period</th>
									<th style="text-align:right">No. of Employee's</th>
									<th style="text-align:right">PT Amount</th>
								</tr>
								
							</thead>
							<tbody>
								<?php $m=1;foreach($ecr_list AS $key=>$ecr_data){  $ecr_data = $ecr_data[0];?>
								<tr role="row" class="odd">
									<th><?php echo $m; ?></th>
									<th><?php echo $key; ?> - <?php echo date('t-m-Y',strtotime($key)); ?> </th>
									<th style="text-align:right"><?php echo valueemtpydash($ecr_data['emp']); ?></th>
									<th style="text-align:right"><?php echo valueemtpydash($ecr_data['sumpt']); ?></th>

								</tr>
							<?php $m++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!--end: Datatable-->
		</div>
	</div>	
</div></div></div>