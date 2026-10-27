<?php
/** Conditions to list out data in table **/
if(isset($_POST['get_epf_summary'])) {

	$from_date = $_POST['fromdate'];
	$to_date = $_POST['todate'];

}else{
	$from_date = date('Y-m-d');
	$to_date = date('Y-m-d');

}
$company_name = "Infisuite";
$pay_year_from = date('Y',strtotime($from_date)); 
$pay_month_from = date('m',strtotime($from_date)); 
$pay_year_to = date('Y',strtotime($to_date)); 
$pay_month_to = date('m',strtotime($to_date)); 

$vurl = API."?mode=togetpayslipctcreports&company_id=".$_SESSION['company_id']."&pay_year_from=".$pay_year_from."&pay_month_from=".$pay_month_from."&pay_year_to=".$pay_year_to."&pay_month_to=".$pay_month_to."&from_date=".$from_date."&to_date=".$to_date;
echo $vurl;
$configesponse = callAPI($vurl);
$payslip_list = json_decode($configesponse,1);
$category_list = $payslip_list['salary_category'];
$component_list = $payslip_list['salary_components'];
$payslip_data = $payslip_list['payslip_data'];

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
														<span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>

															
														</div>
												
		</div>
		<div class="col-md-4">
			<form method="post" action="#">
				<div class="form-group row">
					<label for="example-date-input" class="col-2 col-form-label">From</label>
					<div class="col-10">
						<input class="form-control" type="date" name="fromdate" value="<?php echo date('Y-m-d');?>" id="example-date-input" />
					</div>
				</div>

				<div class="form-group row">
					<label for="example-date-input" class="col-2 col-form-label">To</label>
					<div class="col-10">
						<input class="form-control" type="date" name="todate" value="<?php echo date('Y-m-d');?>" id="example-date-input" />
					</div>
				</div>

															<input type="submit" name="get_epf_summary" class="btn btn-success mr-2">
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

<div<div class="card card-custom">
		<div class="card-header flex-wrap border-0 pt-4 pb-0 pl-5 mx-auto">
			<div class="card-title">
				<h3 class="card-label text-center">Employee's CTC Details 
				<span class="d-block text-muted pt-2 font-size-sm">Contribution period from <?php echo date("d/m/Y", strtotime($from_date)); ?> to <?php echo date("d/m/Y", strtotime($to_date)); ?></span></h3>
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
									<?php 
									foreach ($category_list as $cat_value) {
										?>
										<th colspan="<?php echo $cat_value['total_components'];?>" rowspan="1"><?php echo $cat_value['category_name']; ?> </th>
										<?php
									} ?>
									
								</tr>

								<tr role="row">
									<th></th>
									<th></th>

									<?php 
									foreach ($component_list as $component_value) { ?>
										<th class="sorting" tabindex="0" aria-controls="kt_datatable" rowspan="1" colspan="1" style="width: 47px;" aria-label="Record ID: activate to sort column ascending"><?php echo $component_value['component_name']; ?></th>
										<?php
									} ?>

								</tr>
							</thead>
							<tbody>
								<tr role="row" class="odd">
									<?php 
									foreach ($payslip_data as $values) { ?>

										<th><?php echo $values['employee_id']; ?></th>
										<th><?php echo $values['employee_name']; ?></th>
										<?php foreach ($component_list as $component_value) {
											$amount = $values[$component_value['component_id']];
											if($amount==''){
												$amount = 0;
											}
										 ?>
									
										<th><?php echo $amount; ?></th>
										<?php 
									} ?>
										
									</tr>
									<?php
								}
								?>
</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 col-md-5">
							<div class="dataTables_info" id="kt_datatable_info" role="status" aria-live="polite">Showing 1 to 10 of 50 entries
							</div>
						</div>
						<div class="col-sm-12 col-md-7">
							<div class="dataTables_paginate paging_simple_numbers" id="kt_datatable_paginate">
								<ul class="pagination">
									<li class="paginate_button page-item previous disabled" id="kt_datatable_previous">
										<a href="#" aria-controls="kt_datatable" data-dt-idx="0" tabindex="0" class="page-link"><i class="ki ki-arrow-back"></i></a>
									</li>
									<li class="paginate_button page-item active">
										<a href="#" aria-controls="kt_datatable" data-dt-idx="1" tabindex="0" class="page-link">1</a>
									</li>
									<li class="paginate_button page-item ">
										<a href="#" aria-controls="kt_datatable" data-dt-idx="2" tabindex="0" class="page-link">2</a>
									</li>
									<li class="paginate_button page-item ">
										<a href="#" aria-controls="kt_datatable" data-dt-idx="3" tabindex="0" class="page-link">3</a>
									</li>
									<li class="paginate_button page-item ">
										<a href="#" aria-controls="kt_datatable" data-dt-idx="4" tabindex="0" class="page-link">4</a>
									</li>
									<li class="paginate_button page-item ">
										<a href="#" aria-controls="kt_datatable" data-dt-idx="5" tabindex="0" class="page-link">5</a>
									</li>
									<li class="paginate_button page-item next" id="kt_datatable_next">
										<a href="#" aria-controls="kt_datatable" data-dt-idx="6" tabindex="0" class="page-link"><i class="ki ki-arrow-next"></i></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<!--end: Datatable-->
			</div>
		</div>	


