<?php
$_SESSION['apply_js']['multiselest'] = 1;
//$_SESSION['apply_js']['table'] = 1;
$filterdata = ""; $chosse_list=1;
$monthlist = array('1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
$type_array = array('select','multi_select','disable_select','radio','checklist');
$cur_year = date('Y');
$cur_month = date('m');
$vurl = API."?mode=getheadcountreportemployefilter&table=employees&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
$hcdetails = callAPI($vurl);
$headcount_data = json_decode($hcdetails,1);
//echo "<pre>"; print_r($headcount_data); echo "</pre>";
echo "working"; exit;
$curyear_monthwise_headcount = $headcount_data['curyear_monthwise_headcount'];
$prevyear_monthwise_headcount = $headcount_data['prevyear_monthwise_headcount'];
$Total_Head_Count = $headcount_data['Total_Head_Count'];
$Total_Head_Count_CurrentYear = $headcount_data['Total_Head_Count_CurrentYear'];
$Total_Head_Count_PrevYear = $headcount_data['Total_Head_Count_PrevYear'];

$yearslist = $headcount_data['years'];
$years = implode('|', (array)$yearslist);
$prev_year_monthly_headcount = implode('|', (array)$prevyear_monthwise_headcount);
$cur_year_monthly_headcount =implode('|', (array)$curyear_monthwise_headcount);
$empidlist = $headcount_data['selected_employees'];
if(count((array)$empidlist)>0){
	$employeeslist = array_keys($empidlist);
}
$employment_categories = $headcount_data['employment_categories'];
$designations = $headcount_data['designations'];
$departments = $headcount_data['departments'];
$locations = $headcount_data['locations'];

//$vurl = API."?mode=getbulkchangecategories&empidlist=".implode(',',$employeeslist);
$vurl = API."?mode=getbulkchangecategories&admin_type=".$_SESSION['admin_type']."&employee_id=".$_SESSION['emp_id']."&empidlist=".implode(',',(array)$employeeslist);
$bulkhcdetails = callAPI($vurl);
$bulk_data = json_decode($bulkhcdetails,1);
echo "<pre> sdvdfgrgtrgrtg"; print_r($bulk_data); echo "</pre>";
$bulkchangecategory = $bulk_data['bulkchange_categories'];
$emp_details = $bulk_data['emp_details'];
$emp_history = $bulk_data['emp_history'];
$employee_data = $bulk_data['employee_data'];
$employees_fieldlist = $bulk_data['employees_fieldlist'];
//echo "<pre> part - 1 ";  print_r($bulk_data['employee_data_sql']);  print_r($bulk_data['ausql']); echo "</pre>";
$defalut_fields = array('emp_code','billable','designation_id','department_id','location_id','employment_category_id');
//print_r($locations);
if(isset($_POST['applyform'])) {
	$defalut_fields = $_POST['displayattributes'];
	if(count((array)$defalut_fields) == 0) {
		$defalut_fields = array('emp_code','billable','designation_id','department_id','location_id','employment_category_id');
	}
	//print_r($displayattributes); exit;
	$filtertbl_rowid = $_POST['filtertbl_rowid'];
	//$monyr = explode('-',$_POST['month_year_cal']);
	//$cur_year = $monyr[0];
	//$cur_month = $monyr[1];
	if($_POST['filterby']=='All Employees'){
		$filterdata = "";
	}else{
		$filterby = json_decode($_POST['filterby'],1);
		unset($filterby['matches_count']);
		unset($filterby['matches_names']);
		$filterdata = urlencode(json_encode($filterby));
	}
	// print_r($filterby);
	$vurl = API."?mode=getheadcountreportemployefilter&table=employees&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
	$hcdetails = callAPI($vurl);
	$headcount_data = json_decode($hcdetails,1);
	$curyear_monthwise_headcount = $headcount_data['curyear_monthwise_headcount'];
	$prevyear_monthwise_headcount = $headcount_data['prevyear_monthwise_headcount'];
	$Total_Head_Count = $headcount_data['Total_Head_Count'];
	$Total_Head_Count_CurrentYear = $headcount_data['Total_Head_Count_CurrentYear'];
	$Total_Head_Count_PrevYear = $headcount_data['Total_Head_Count_PrevYear'];
	$yearslist = $headcount_data['years'];
	$years = implode('|', $yearslist);
	$prev_year_monthly_headcount = implode('|', (array)$prevyear_monthwise_headcount);
	$cur_year_monthly_headcount =implode('|', (array)$curyear_monthwise_headcount);
	$empidlist = $headcount_data['selected_employees'];
	$employeeslist = array_keys($empidlist);
	$employment_categories = $headcount_data['employment_categories'];
	$designations = $headcount_data['designations'];
	$departments = $headcount_data['departments'];
	$locations = $headcount_data['locations'];

	//$vurl = API."?mode=getbulkchangecategories&empidlist=".implode(',',(array)$employeeslist);
	$vurl = API."?mode=getbulkchangecategories&admin_type=".$_SESSION['admin_type']."&employee_id=".$_SESSION['emp_id']."&empidlist=".implode(',',(array)$employeeslist);
	$bulkhcdetails = callAPI($vurl);
	$bulk_data = json_decode($bulkhcdetails,1);
	//echo "<pre> part - 2 "; print_r($bulk_data['employee_data_sql']); echo "</pre>";
	$bulkchangecategory = $bulk_data['bulkchange_categories'];
	$emp_details = $bulk_data['emp_details'];
	$emp_history = $bulk_data['emp_history'];
	$employee_data = $bulk_data['employee_data'];
	$employees_fieldlist = $bulk_data['employees_fieldlist'];
	

	//print_r($employee_data);
}
foreach ($defalut_fields as $dfield) {
	if(in_array($employees_fieldlist[$dfield]['type'], $type_array)){
		$field[$dfield] =  dyanamicselectdata("employees",$dfield);
	}
}
?>
<div class="d-block">
	<div class="container-fluid">
		<form class="kt-form" id="kt_form" method="post" action="" >
			<input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >
			<div class="row">
				<div class="col-lg-12 mt-3">
					<div class="card card-custom" id="kt_page_sticky_card">
						<div class="card-header">
							<div class="card-title">
								<h5 class="card-label">Employee List</h5>
								<!-- <div class="col-lg-4">
									<input class="form-control" type="month" name="month_year_cal" value="<?php echo $cur_year."-".$cur_month?>"  id="att_month_year" />
								</div> -->
							</div>
						</div>
						<div class="card-body row">
							<div class="col-lg-1 mt-3">
								<a href="#" class="btn btn-warning showpopup" ><i class="ti-filter"></i>Filter</a>
							</div>
							<div class="col-lg-5 mt-3">
								<select class="form-select js-example-basic-multiple-limit_custom" name="displayattributes[]" multiple="multiple">
									<?php $jd=0; foreach ($employees_fieldlist as $fkey => $fieldvalue) { 

										if(in_array($fkey, $defalut_fields)){ $jd++; } 

										?>
										<option value="<?php echo $fkey; ?>" <?php echo in_array($fkey, (array)$defalut_fields) ? 'selected' : ''; ?>  <?php echo $jd==1?'locked="locked"':''; ?>> <?php echo $fieldvalue['lable']; ?> </option>
									<?php }	?>

								</select>
							</div>
							<div class="col-lg-2 mt-3">
								<input type="hidden" id="filterby" name="filterby"> 
								<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
							</div>
						</div>
					</div>
				<!--end::Charts Widget 2-->
				</div>
			</div>
		</form>
	</div>

		<div class="card card-custom">
			<div class="row d-none" >
				<div class="form-group col-lg-3 mt-10 mb-10 d-none"  id="bulkcategory" >
					<label>Transfer Category</label>
					<select  id="bulkchangecategory" name="bulkchangecategory" class="form-control showbulkchangefilter" >
						<?php
						foreach ($bulkchangecategory as $filter) {
							?>
							<option <?php if($selectedoption==$filter['application_tablename']) {?> selected <?php } ?> value="<?php echo $filter['application_tablename']."|".$filter['application_table_fieldname'].'|'.$filter['id'].'|'.$filter['update_employees_fieldname'].'|'.$filter['insert_history_tablename'];?>"><?php echo $filter['category_name'];?></option>
						<?php } ?>
					</select>
					<span class="form-text text-muted">Select Bulk Transfer Category</span>

				</div>


				<div class="col-lg-3 mt-10 mb-10" id="application_tablename_filter">
					
				</div>
				<div class="col-lg-3 mt-10 mb-10 d-none" id="effectdate" >
					<label>Effective From</label>
					<div class="input-group date">
						<input type="text" class="form-control"  id="kt_datepicker_4_1"  name="effective_date" />
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar-check-o"></i>
							</span>
						</div>
					</div>
					<span class="form-text text-muted">Enter Effective From</span>

				</div>
				<div class="col-lg-2 mt-10 mb-10">
					<input type="hidden" id="application_tablename">
					<input type="hidden" id="application_table_fieldname">
					<input type="hidden" id="update_employees_fieldname">
					<input type="hidden" id="insert_history_tablename">
					<input type="hidden" id="bulkmove_empids">
					<input type="hidden" id="bulkchange_categoryid">
					<input type="hidden" id="bulkmove_toid">
					<button id="previewbtn"  class="btn btn-primary btn-shadow-hover font-weight-bold mt-8 bulkconfirmpopup d-none" >Preview</button>
				</div>
			</div>
		</div>

		
			<div class="p-4">
				<div class="row" >
					<ul class="nav  md-tabs infisuite_tab mb-2 " role="tablist">
						<li class="nav-item">
							<a class="nav-link <?php echo (!isset($_GET['vtype']) || $_GET['vtype']=="list")?"active":""; ?>"  href="index.php?option=COM_LIST_EMPLOYEE_FILTER&vtype=list" role="tab"> <i class="fa fa-navicon"></i> List</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo $_GET['vtype']=="grid"?"active":""; ?>"  href="index.php?option=COM_LIST_EMPLOYEE_FILTER&vtype=grid" role="tab"> <i class="fa fa-building"></i> Grid</a>
						</li>
						<!---<li class="nav-item">
							<a class="nav-link <?php echo $_GET['vtype']=="tree"?"active":""; ?>"  href="index.php?option=COM_LIST_EMPLOYEE_FILTER&vtype=tree" role="tab"> <i class="fa fa-sitemap"></i> Tree</a>
						</li>  --->
					</ul>
				</div>


				<?php if($_GET['vtype']=="tree"){ 
					include 'views/custom_list_employees_tree.php';	
				}elseif($_GET['vtype']=="grid"){ ?>

			<div class="row"> <!-- j-pro -->							
			<?php 
				$serialno = 1;
				//print_r($employee_data);
				foreach ($employee_data as $empidkey => $empvalue) { 
			?>
				<div class="col-xl-3 col-md-6 p-2">
					<div class="card sale-card ">
						<div class="card-header mt-2">
							<h5><?php echo $empvalue['display_name']; ?></h5>
						</div>
						<div class="card-block text-center">
		<?php if($empvalue['image']!="") { ?>
												
							<div data-label="40%" class="radial-bar radial-bar-lg radial-bar-0  radial-bar-danger">
								<img src="uploads/all/<?php echo $empvalue['image']; ?>" alt="User-Image">
							</div>
		<?php }else{  ?>					
 <div data-label="<?php echo initials($empvalue['display_name']); ?>" class="radial-bar custom-radial-bar radial-bar-0 radial-bar-lg radial-bar-danger"></div>
               <?php } ?>
            			
				
						<div class="row ">

							<?php 
						/*	foreach ($employees_fieldlist as $empkey => $empfield) {   
								if(in_array($empkey, $defalut_fields))  {   ?>
									<span class="text-muted font-weight-bold f-14 row">
										<?php if(in_array($empfield['type'], $type_array)){ 
											$filedvalue = $field[$empkey][$empvalue[$empkey]]==""?"-":$field[$empkey][$empvalue[$empkey]];
										?>	
											<div class='col-lg-6 text-right f-w-700'><?php echo ucwords(str_replace("_"," ",($empkey)))." : "; ?></div>
											<div class='col-lg-6 p-l-0'><?php echo $filedvalue ?></div>
							
									<?php	}else {
										$filedvalue = $empvalue[$empkey]==""?"-":$empvalue[$empkey];											
									?>	<div class='col-lg-6 text-right f-w-700'><?php echo ucwords(str_replace("_"," ",($empkey)))." : "; ?></div>
										<div class='col-lg-6'><?php echo $filedvalue; ?></div>
							
									<?php 	} ?>
									</span>
								<?php }  } */?>


				<?php 
							foreach ($employees_fieldlist as $empkey => $empfield) {   
								if(in_array($empkey, $defalut_fields))  {   ?>
									<span class="text-muted font-weight-bold f-14">
										<?php if(in_array($empfield['type'], $type_array)){ 
											echo $field[$empkey][$empvalue[$empkey]]==""?"-":$field[$empkey][$empvalue[$empkey]];
							//echo "man";
										}else {
											echo $empvalue[$empkey]==""?"-":$empvalue[$empkey];
										} ?>
									</span>
								<?php }  }?>

						</div>
					</div>
				</div>
			</div>
			<?php } ?>	
			</div>	



					<?php }else{ ?>


						<!--begin: Datatable -->
						 <div class="dt-responsive table-responsive mt-4 row">
        					<table id="new-cons" class="table table-striped table-bordered nowrap">
							<thead>
								<tr>
									<th>SN</th>

									<th>Name </th>

									<?php foreach ($employees_fieldlist as $empkey => $empfield) { 
										if(in_array($empkey, $defalut_fields))  { ?>
											<th>
												<?php echo $empfield['lable']; ?>
											 </th>
									<?php  } } ?>
									<th>Action</th>
									</tr>
										
									</thead>
									<tbody>

										<?php 
										$serialno = 1;
										foreach ($employee_data as $empidkey => $empvalue) { 
											?>
											<tr>
												<td><?php echo $serialno; ?></td>
												<td >
											<?php if($empvalue['image']!="") { ?>
												<img src="uploads/all/<?php echo $empvalue['image']; ?>"   class="img-radius img-50 align-top m-r-15" >
											<?php } else { ?>
												<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($empvalue['display_name']); ?></span>
											<?php } ?>
	<a class="cursor-pointer getBasicInfoPopUp"  data-empid="<?php echo $empvalue['id'];?>"><?php echo $empvalue['display_name']; ?></a>
				<?php //echo $empvalue['display_name']; ?>
												</td>

												<?php 

												foreach ($employees_fieldlist as $empkey => $empfield) {  
													if(in_array($empkey, $defalut_fields))  { ?>
														<td> 
															<?php if(in_array($empfield['type'], $type_array)){
																echo $field[$empkey][$empvalue[$empkey]];
												//echo "man";
															}else {
																echo $empvalue[$empkey];
															} ?>
														</td>
													<?php }  }?>
												<td>
												
												   <a href="index.php?option=COM_INSERT_ONBOARDING_EMPLOYEES&Rid=<?php echo deocdeId("id:".$empvalue['id']); ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm"   title="Edit Datails <?php echo $empvalue['id']; ?>" ><i class="fa fa-edit"></i></a>
                             									   <a  class="btn btn-sm btn-clean btn-icon btn-icon-sm getBasicInfoPopUp" data-empid="<?php echo $empvalue['id'];?>" title="view Datails"><i class="fa fa-eye"></i></a>
												</td>
												</tr>

												<?php $serialno++; } ?>
											</tbody>
										</table>

									<?php } ?>			
								</div>
							</div>
						
					</div>
				</div>
			</div>
			<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-scrollable"  >
					<div class="modal-content"  id="popup2">

					</div>
				</div>
			</div>

  
<?php if($_POST['ajaxpage'] == 'Y'){?>
	<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
	<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
	<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
	<!-- Custom js -->
	<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
	<script type="text/javascript" src="assets/pages/advance-elements/moment-with-locales.min.js"></script>

	
<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script src="assets/pages/data-table/js/jszip.min.js"></script><!-- 
    <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script> 
    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script> -->
    <?php } ?>