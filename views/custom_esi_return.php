<?php
if(isset($_POST['get_ecr_summary'])) {

	$pay_month_year = $_POST['pay_month_year'];

} else {
	$pay_month_year = date('F, Y');

}
//$pay_month_year = "2021-01";
$company_name = "Infisuite";
$basic_pay_component_id =1;
$company_id=$_SESSION['company_id'];

$pay_year = date('Y',strtotime($pay_month_year)); 
 $pay_month = date('m',strtotime($pay_month_year)); 


$whrcon["where"] = array('company_id'=> $company_id,'is_active'=>0,'pay_year'=>$pay_year,'pay_month'=>$pay_month);
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=pay_run_summary&data=".$vdata; 
$response = callAPI($url);
$empdata = json_decode($response,1);

//print_r($whrcon);
	foreach ($empdata as  $data) {
		$employee_id = $data['employee_id'];
		$date_of_leaving = $data['date_of_leaving'];
		$total_no_of_working_days = $data['total_no_of_working_days'];

$whrcon1["where"] = array('company_id'=> $company_id,'is_active'=>0,'id'=>$employee_id);
$vdata1 = urlencode(json_encode($whrcon1));
//print_r($whrcon1);

$url = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata1; 
$response = callAPI($url);
$employee_data = json_decode($response,1);
$emp_details = $employee_data[0];
//print_r($url);

$employee_name =$emp_details['display_name'];

$vurl = API."?mode=togetsumofempsalaryoncomponentid&company_id=".$company_id."&employee_id=".$employee_id."&component_id=".$basic_pay_component_id."&pay_month=".$pay_month."&pay_year=".$pay_year;
		$configesponse = callAPI($vurl);
		$emp_salary_array = json_decode($configesponse,1);
		$emp_basic = $emp_salary_array[0]['totalactual'];

		$esi_return_list[] = array('employee_id'=>$employee_id,'employee_name'=>$employee_name,'date_of_leaving'=>$date_of_leaving,'total_no_of_working_days'=>$total_no_of_working_days,'emp_basic'=>$emp_basic);

	}

//print_r($esi_return_list);

?>

<div class="flex-column-fluid j-wrapper">
<div class="container-fluid j-pro">
<div class=" flex-row j-content">
<div class="row">
		<div class="col-md-7">
			<div class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
			<span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2"><i class="fa fa-user-plus text-info f-40"></i></span>
		<span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>

															
														</div>
												
		</div>
		


	<div class="col-md-5">
			<form method="post" action="#">
				<div class="form-group row">
					<label for="example-month-input" class="col-2 f-w-700 mt-3 form-label">Month</label>
					<div class="col-7">
						<input class="form-control" type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input" />
					</div>
					<div class="col-3">
					<button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light">Submit</button>
				</div>
				</div>
				
			
		</form>
		</div>


	</div>




	<div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
		<div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Mobile Toggle-->
				<button class="burger-icon burger-icon-left mr-4 d-inline-block d-lg-none" id="kt_subheader_mobile_toggle">
					<span></span>
				</button>
				<!--end::Mobile Toggle-->
				<!--begin::Page Heading-->
				
				<!--end::Page Heading-->
			</div>
			<!--end::Info-->
			
		</div>
	</div>

	<div class="card card-custom">
		<div class="card-header flex-wrap border-0 pt-4 pb-0 pl-5 mx-auto">
			<div class="card-title">
				<h3 class="card-label text-center">EPF-ECR Report for <?php echo $pay_month_year; ?>
				<!-- <span class="d-block text-muted pt-2 font-size-sm">Contribution period from 01/09/2020 to 30/09/2020</span> -->
			</h3>
			</div>
		</div>
			<div class="card-body dt-responsive table-responsive">
			<!--begin: Datatable-->
			<div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
				<div class="row">
					<div class="col-sm-12">
						<table class="table table-bordered table-hover table-checkable mt-10 dataTable no-footer dtr-inline" id="kt_datatable" role="grid" aria-describedby="kt_datatable_info" style="width: 1236px;">
							<thead>
								<tr role="row" bgcolor="#f7f7f7">
									<th>Employee ID</th>
									<th>Insurance Person Name</th>
									<th>Paid Days</th>
									<th>Total Monthly Wages</th>
									<th>Reason Code For Zero Working Days</th>
									<th>Last Working Days</th>
								</tr>
							</thead>
							<tbody>
								<?php if(!empty($esi_return_list)) {
										foreach ($esi_return_list as $esi_data) { ?>
								<tr role="row" class="odd">
									<th><?php echo $esi_data['employee_id']; ?></th>
									<th><?php echo $esi_data['employee_name']; ?></th>
									<th><?php echo $esi_data['total_no_of_working_days']; ?></th>
									<th><?php echo $esi_data['emp_basic']; ?></th>
									<th>---TBD---</th>
									<th><?php echo $esi_data['date_of_leaving']; ?></th>
								</tr>

								<?php
							}
							} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!--end: Datatable-->
		</div>
	</div>	
</div></div></div>
