<?php
if(isset($_POST['submit_sh'])){
	//print_r($_POST); exit;
	$data = $_POST;
	$data['current_status'] = 0;
	$data['status_date'] = nowDate();
	$data['from_date'] = dateFormate($_POST['from_date']);
	$data['to_date'] = dateFormate($_POST['to_date']);
	$data['created_date'] = nowDate();
	$data['created_by'] = $_SESSION['userid'];
	$url = API."?mode=addeployeeshifts&method=post";
	$response = file_post_contents($url, $data, 'insert');
}
$vdata1["where"] = array('is_active'=> 0);
	$vdata = urlencode(json_encode($vdata1)); 
	$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata; 
	$configesponse = callAPI($vurl);
	$emp = json_decode($configesponse,1);
	$response = $emp;
if(isset($_POST['submit_shift'])){
	$url = API."?mode=tosearchemployee&department=".$_POST['department']."&designation=".$_POST['designation']."&employee=".json_encode($_POST['employee'],1);
	//echo $url;// exit;
	$response = callAPI($url);
	$response = json_decode($response,1);
	//print_r($response); exit;
	$sfwr["where"] = array('is_active'=> 0);
	$sfwre = urlencode(json_encode($sfwr)); 

}

	$vurl = API."?mode=gettabledetailswithconditions&table=shifts&data=".$sfwre; 
	//echo $vurl;
	$Shifts = callAPI($vurl);
	$Shifts = json_decode($Shifts,1);
	//print_r($Shifts);
	
$dpwr["where"] = array('is_active'=> 0);
$dpwr = urlencode(json_encode($dpwr)); 
$vurl = API."?mode=gettabledetailswithconditions&table=departments&data=".$dpwr; 
$departments = callAPI($vurl);
$departments = json_decode($departments,1);


$vurl = API."?mode=gettabledetailswithconditions&table=designations&data=".$dpwr; 
$designations = callAPI($vurl);
$designations = json_decode($designations,1);

//print_r($emp); exit;
?>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<div class="card card-custom">

			<div class="row">
      <div class="col-lg-12">
         <div class="card">            
            <div class="card-header table-card-header">
               <h5><?= __("Employee shift Assign") ?></h5>
         <div class="card-header-right table-card-header">
               <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                  <i class="ki ki-long-arrow-back icon-xs"></i>
                  <?= __("Back") ?>
               </a>
            </div>
         </div>
			<div class="card-body">
				<form method="post" action="" enctype="multipart/form-data">
					<div class="row align-items-center">
						<div class="col-lg-12">
							<div class="row align-items-center">
								<div class="col-lg-3 my-2 my-md-0">
									<div class="d-flex align-items-center">
										<label class="mr-3 mb-0 d-none d-md-block" for="kt_datatable_search_department"><?= __("Departments:") ?>&nbsp;</label>
										<select class="form-select js-example-basic-single" id="kt_datatable_search_department" name="department">
											<option value=""><?= __("All") ?></option>
											<?php foreach ($departments as $department) { ?>
											<option  <?php if($_POST['department'] == $department['id']){ echo "selected"; } ?> value="<?php echo $department['id']; ?>"><?= htmlspecialchars(__($department['department_name'] ?? '')) ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-lg-3 my-2 my-md-0">
									<div class="d-flex align-items-center">
										<label class="mr-3 mb-0 d-none d-md-block" for="kt_datatable_search_designation"><?= __("Designations:") ?>&nbsp;</label>
										<select  class="form-select js-example-basic-single" id="kt_datatable_search_designation" name="designation">
											<option value=""><?= __("All") ?></option>
											<?php foreach ($designations as $designation) { ?>
											<option <?php if($_POST['designation'] == $department['id']){ echo "selected"; } ?>  value="<?php echo $department['id']; ?>"><?= htmlspecialchars(__($designation['designation_name'] ?? '')) ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<?php $empsel = json_decode(json) ?>
								<div class="col-lg-3 my-2 my-md-0">
									<div class="d-flex align-items-center">
										<label class="mr-3 mb-0 d-none d-md-block" for="kt_select2_31"><?= __("Employees:") ?>&nbsp;</label>
										<select class="form-select js-example-basic-multiple" id="kt_select2_31" name="employee[]" multiple="multiple">
											<option value=""><?= __("All") ?></option>
											<?php foreach ($emp as $employee) { ?>
											<option <?php if(in_array($employee['id'], (array)$_POST['employee'])){ echo "selected"; } ?>  value="<?php echo $employee['id']; ?>"><?php echo $employee['display_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-lg-3 my-2 my-md-0">
									<input type="hidden" name="submit_shift" value="submit_shift">
									<button class="btn btn-primary px-6 font-weight-bold" id="kt_search_submit" ><?= __("Search") ?></button>
								</div>
							</div>
						</div>
					</div>
					<!--begin: Datatable-->
				</form> 
				<form method="post" action="" enctype="multipart/form-data">
				<div class="row align-items-center mt-3">
					<div class="col-lg-12">
						<div class="row align-items-center">
							<div class="col-lg-3 my-2 my-md-0">
								<div class="d-flex align-items-center">
									<label class="mr-3 mb-0 d-none d-md-block" for="kt_datatable_search_shift"><?= __("Shifts:") ?>&nbsp;</label>
									<select class="form-select js-example-basic-single" id="kt_datatable_search_shift" name="shift_id">
										<option value=""><?= __("Select Any One") ?></option>
										<?php foreach ($Shifts as $shif) { ?>
										<option  value="<?php echo $shif['id']; ?>"><?= htmlspecialchars(__($shif['shift_name'] ?? '')) ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-lg-3 my-2 my-md-0">
								<div class="d-flex align-items-center">
									<label class="mr-3 mb-0 d-none d-md-block" for="kt_datepicker_from"><?= __("From:") ?>&nbsp;</label>
									<div class="input-group date mt-3">
										<?php $date_pic = fromDate($role[$key]); ?>
										<input type="date" class="form-control" style="height: 45px;"  value="<?php echo  $date_pic; ?>"  name="from"  id="kt_datepicker_from" />
									</div>
								</div>
							</div>
							<?php $empsel = json_decode(json) ?>
							<div class="col-lg-3 my-2 my-md-0">
								<div class="d-flex align-items-center">
									<label class="mr-3 mb-0 d-none d-md-block" for="kt_datepicker_to"><?= __("To:") ?>&nbsp;</label>
									<div class="input-group date mt-3">
										<?php $date_pic = fromDate($role[$key]); ?>
										<input type="date" class="form-control" style="height: 45px;" value="<?php echo  $date_pic; ?>"  name="to"  id="kt_datepicker_to" />										
									</div>
								</div>
							</div>
							<div class="col-lg-3 my-2 my-md-0">
								<input type="hidden" name="submit_sh" value="submit_sh">
								<button class="btn btn-primary px-6 font-weight-bold" id="kt_assign_submit" ><?= __("Submit") ?></button>
							</div>
						</div>
					</div>
				</div>

				<table class="table table-separate table-head-custom table-checkable" id="kt_datatable_2">
					<thead>
						<tr>
							<th scope="col"></th>
							<!-- <th scope="col">Company Name</th> -->
							<th scope="col"><?= __("Employee Code") ?></th>
							<th scope="col"><?= __("Employee Name") ?></th>
							<th scope="col"><?= __("Employee Email") ?></th>
							<th scope="col"><?= __("Phone") ?></th>
							<th scope="col"><?= __("Date of birth") ?></th>
							<th scope="col"><?= __("Actions") ?></th>
						</tr>
					</thead>	
					<?php foreach ($response as $empp) { ?>		
						<tr>
							<td>
								<div class="checkbox-inline">
					                <label class="checkbox checkbox-primary">
					                    <input type="checkbox" value="<?php echo $empp['id'];  ?>"  name="check_employee[]"/>
					                    <span></span>
					                    
					                </label>	
					            </div>
							</td>
							<!-- <td><?php echo $empp['emp_code']; ?></td> -->
							<td><?php echo $empp['emp_code']; ?></td>
							<td><?php echo $empp['display_name']; ?></td>
							<td><?php echo $empp['email_id']; ?></td>
							<td><?php echo $empp['phone']; ?></td>
							<td><?php echo $empp['date_of_birth']; ?></td>
							<td nowrap="nowrap"></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<!--end: Datatable-->
				</form> 
			</div>
		</div>
	</div>
</div>


