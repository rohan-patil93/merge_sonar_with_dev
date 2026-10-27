<?php
if(isset($_POST['get_payslip_summary'])) {
	$pay_month_year = $_POST['pay_month_year'];
} else {
	$pay_month_year = date('F, Y');
}
$title = "Payslip";
$pay_year = date('Y',strtotime($pay_month_year)); 
$pay_month = date('m',strtotime($pay_month_year)); 
$cusurl = API."?mode=togetallpayslipdetails&month=".$pay_month."&year=".$pay_year."&company_id=".$_SESSION['company_id']."&is_active='0'"; 
$cusresponse = callAPI($cusurl);
$pay_return_list = json_decode($cusresponse,1);
?>
<div class="row">
	<div class="col-md-8">
		<div class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
			<span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Add-user.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
						<path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
			<span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $title; ?></span>


		</div>

	</div>
	<div class="col-md-4">
		<form method="post" action="#">
			<div class="form-group row">
				<label for="example-month-input" class="col-2 col-form-label">Month</label>
				<div class="col-10">
					<input class="form-control" type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input" />
				</div>
			</div>


			<input type="submit" name="get_payslip_summary" class="btn btn-success mr-2">
		</div>
	</form>
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
			<h3 class="card-label text-center">PAYSLIP Report for <?php echo $pay_month_year; ?>
			<!-- <span class="d-block text-muted pt-2 font-size-sm">Contribution period from 01/09/2020 to 30/09/2020</span> -->
		</h3>
	</div>
</div>
<div class="card-body">
	<!--begin: Datatable-->
	<div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
		<div class="row">
			<div class="col-sm-12">
				<table class="table table-bordered table-hover table-checkable mt-10 dataTable no-footer dtr-inline" id="kt_datatable" role="grid" aria-describedby="kt_datatable_info" style="width: 1236px;">
					<thead>
						<tr role="row" bgcolor="#f7f7f7">
							<th>Employee ID</th>
							<th>Employee Name</th>
							<th>Date Of Joining</th>
							<th>Date of Leaving</th>
							<th>Number of Working Days</th>
							<th>Number of LOP Days</th>
							<th>Total Gross</th>
							<th>Total Deductions</th>
							<th>Net Payable</th>

							<th>View Details</th>
							<th>View Payslip</th>


						</tr>
					</thead>
					<tbody>
						<?php if(!empty($pay_return_list)) {
							foreach ($pay_return_list as $pys_data) { ?>
								<tr role="row" class="odd">
									<th><?php echo $pys_data['employee_id']; ?></th>
									<th><?php echo $pys_data['emp_name']; ?></th>
									<th><?php echo $pys_data['date_of_joining']; ?></th>
									<th><?php echo $pys_data['date_of_leaving']=='0000-00-00'?'-':$pys_data['date_of_leaving']; ?></th>
									<th><?php echo $pys_data['total_no_of_working_days']; ?></th>
									<th><?php echo $pys_data['total_lop_days']; ?></th>
									<th style="text-align: right;"><?php echo $pys_data['earning_amount']; ?></th>
									<th style="text-align: right;"><?php echo $pys_data['deduction_amount']; ?></th>
									<th style="text-align: right;"><?php echo $pys_data['net']; ?></th>
									
									<th><a href="index.php?option=COM_INSERT_ONBOARDING_EMPLOYEES?Rid=empid:<?php echo $pys_data['employee_id']; ?>"  class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Employee Details" target="_blank"><i class="flaticon-eye"> </i> </a></th>
									<th>

										<!-- Example single danger button-->
										<div class="btn-group">
											<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Payslip</button>
											<div class="dropdown-menu">
												<a class="dropdown-item" href="index.php?option=COM_LIST_PAYSLIP&month=<?php echo $pay_month;?>&year=<?php echo $pay_year;?>&emp_id=<?php echo $pys_data['employee_id']; ?>" target="_blank">General Payslip </a>
												<a class="dropdown-item" href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE1&month=<?php echo $pay_month;?>&year=<?php echo $pay_year;?>&emp_id=<?php echo $pys_data['employee_id']; ?>" target="_blank">Template 1 </a>

												<a class="dropdown-item" href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE2&month=<?php echo $pay_month;?>&year=<?php echo $pay_year;?>&emp_id=<?php echo $pys_data['employee_id']; ?>" target="_blank">Template 2 </a>


												<a class="dropdown-item" href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE3&month=<?php echo $pay_month;?>&year=<?php echo $pay_year;?>&emp_id=<?php echo $pys_data['employee_id']; ?>" target="_blank">Template 3 </a>


												<a class="dropdown-item" href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE4&month=<?php echo $pay_month;?>&year=<?php echo $pay_year;?>&emp_id=<?php echo $pys_data['employee_id']; ?>" target="_blank">Template 4 </a>


												<a class="dropdown-item" href="index.php?option=COM_LIST_PAYSLIP_TEMPLATE6&month=<?php echo $pay_month;?>&year=<?php echo $pay_year;?>&emp_id=<?php echo $pys_data['employee_id']; ?>" target="_blank">Template 5 </a>


											</div>
										</div>
									</th>
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

