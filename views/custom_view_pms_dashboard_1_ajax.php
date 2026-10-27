<?php 
$_SESSION['apply_js']['pms_dashboard'] = '1';
if(isset($_POST['appraisal_id'])){
	include('../includes/session.php');
	include('../dbconfig.php');
	include('../comm.php');
	$url1 = API."?mode=pmsdashboard1&appraisal_id=".$_POST['appraisal_id']; 
	$resultapi = callAPI($url1);
	$result = json_decode($resultapi,1);
	//echo "<pre>"; print_r($result['performance_setup_id']); echo "</pre>";
	$employees = $result['employees'];
	$performance_setup = $result['performance_setup'];
	$appraisalemployees = $result['employee_performance_appraisal_headers'];
	$arslabs = $result['arslabs'];
}
foreach ($appraisalemployees as $appraisalemploye){
	if($appraisalemploye['current_status'] == ST_APPROVAL_PENDING){
		$appraisalemp['hrpendning']++;
	}elseif($appraisalemploye['current_status'] == ST_APPROVED){
		$appraisalemp['approved']++; 
	}else{
		$appraisalemp['emp_pending']++; 
	}
}
$performancereviewsummary = array("self_assessment"=>$appraisalemp['emp_pending'],"manager_assessment"=>$appraisalemp['hrpendning'],"completed"=>$appraisalemp['approved']);

?>
<div class="row">
	<div class="col-lg-4">
		<div class="card">
            <div class="card-header">
                <h5>Performance Review Summary</h5>
            </div>
            <div class="card-block">
            	<input type="hidden" id="performance_review_summary" value='<?php echo json_encode($performancereviewsummary); ?>'>
                <div id="performance_review_summary_chasrt" style="width: 100%; height: 200px;"></div>
            </div>
        </div>
	</div>
	<div class="col-lg-8 row">
		<div class="col-lg-6">
			<div class="card-red">
				<div class="card-body">
					<h3><?php echo count((array)$appraisalemployees); ?></h3>
					<p class="f-w-600 mb-0">Total Users</p>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="card-blue">
				<div class="card-body">
					<h3><?php echo customNanAndEmptyValidation($appraisalemp['emp_pending']); ?></h3>
					<p class="f-w-600 mb-0">Self Assessment</p>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="card-yellow">
				<div class="card-body">
					<h3><?php echo customNanAndEmptyValidation($appraisalemp['hrpendning']); ?></h3>
					<p class="f-w-600 mb-0">Manager Assessment</p>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="card-green">
				<div class="card-body">
					<h3><?php echo customNanAndEmptyValidation($appraisalemp['approved']); ?></h3>
					<p class="f-w-600 mb-0">Completed</p>
				</div>
			</div>	
		</div>
		<!-- <ul>
			<li class="card card-red">
				<div class="card-body text-white">
					<h3><?php echo count((array)$appraisalemployees); ?></h3>
					<p class="f-w-600 mb-0">Total Users</p>
				</div>
			</li>
			<li class="card card-blue m-l-10">
				<div class="card-body text-white">
					<h3><?php echo customNanAndEmptyValidation($appraisalemp['emp_pending']); ?></h3>
					<p class="f-w-600 mb-0">Self Assessment</p>
				</div>
			</li>
			<li class="card card-green m-l-10">
				<div class="card-body text-white">
					<h3><?php echo customNanAndEmptyValidation($appraisalemp['hrpendning']); ?></h3>
					<p class="f-w-600 mb-0">Manager Assessment</p>
				</div>
			</li>
			<li class="card card-red m-l-10">
				<div class="card-body text-white">
					<h3><?php echo customNanAndEmptyValidation($appraisalemp['approved']); ?></h3>
					<p class="f-w-600 mb-0">Completed</p>
				</div>
			</li>
		</ul> -->
	</div>
</div>
<div class="card custom-scroll" style=" height: 500px;">
	<div class="card-body">
		<div class="table-responsive">
			<?php if(count((array)$appraisalemployees) !=0){ ?>
			<table class="table table-hover m-b-0" style="font-size:13px;">
				<tr>
					<th>Self Assessment</th>
					<th>Manager Assessment</th>
                  <!--<th>Peer Assessment</th>
                  	<th>Secondary Assessment</th> -->
                  	<th>Self Score</th>
                  	<th class="text-right">Manager score</th>
                  	<th class="text-right">Performance Score</th>
                  </tr>
                  <?php foreach($appraisalemployees as $appraisalemployee){ ?>
                  	<tr>
                  		<td>
                  			<div>
                  				<?php if($employees[$appraisalemployee['employee_id']]['image']!="") {?>
                  					<img src="uploads/all/<?php echo $employees[$appraisalemployee['employee_id']]['image']; ?>"   class="img-radius img-30 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
                  				<?php } else { ?>
                  					<span  alt="user image" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($employees[$appraisalemployee['employee_id']]['display_name']); ?></span> 
                  				<?php } ?>
                  				<div class="d-inline-block width73">
                  					<span class="text-capitalize f-12"><span class="f-14"><?php echo $employees[$appraisalemployee['employee_id']]['display_name']; ?></span></span>
                  				</div>
                  			</div>
                  		</td>
                  		<td>
                  			<div>
                  				<?php if($_SESSION['user_image'][$appraisalemployee['hr_user_id']]!="") {?>
                  					<img src="uploads/all/<?php echo $_SESSION['user_image'][$appraisalemployee['hr_user_id']]; ?>"   class="img-radius img-30 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
                  				<?php } else { ?>
                  					<span  alt="user image" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$appraisalemployee['hr_user_id']]); ?></span>
                  				<?php } ?>
                  				<div class="d-inline-block width73">
                  					<span class=" text-capitalize f-12"><span class="f-14"><?php echo $_SESSION['users'][$appraisalemployee['hr_user_id']]; ?></span></span>
                  				</div>
                  			</div>
                  		</td>
                  		<td class="text-right"><?php echo $appraisalemployee['self_score']; ?></td>
                  		<td class="text-right"><?php echo $appraisalemployee['approved_score']; ?></td>
                  		<td >
                  			<?php 
                  			$scoreslab = 0; $bgcolor="";  foreach($arslabs as $arslabs){
                  				if($arslabs['start_range'] <= $appraisalemployee['approved_score'] AND $arslabs['end_range'] >= $appraisalemployee['approved_score']){
                  					$scoreslab = $arslabs['slabs_name'];
                  					$bgcolor = $arslabs['background_color'];
                  				}   
                  			}
                  			?>  
                  			<label class="form-label label label-primary p-2" style="background-color:<?php echo $bgcolor; ?>"><?php echo $scoreslab !=0?$scoreslab:"Pending"; ?></label>
                  		</td>
                  	</tr>
                  <?php } ?>
              </table>
          <?php }else{ ?>
          	<div class="card-px text-center ">
			<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Employees</p>   
			<div class="text-center px-4">
				<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
			</div>   
		</div>

          <?php } ?>
          </div>
      </div>
  </div>    
<?php if(isset($_POST['appraisal_id'])){ ?>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="assets/js/custom_view_pms_dashboard_1.js"></script>
    <?php } ?>