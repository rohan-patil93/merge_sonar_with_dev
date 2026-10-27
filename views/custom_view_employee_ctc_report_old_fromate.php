
<?php 
$_SESSION['apply_js']['table']='1';
if(isset($_POST['get_ecr_summary'])) {
	$pay_month_year = $_POST['pay_month_year'];
} else {
	$pay_month_year = date('F, Y');
}
//echo $pay_month_year;
$company_name = $_SESSION['config']['name'];
$pay_year = date('Y',strtotime($pay_month_year)); 
$pay_month = date('m',strtotime($pay_month_year)); 
$vurl = API."?mode=getmonthlyctcreports&pay_year=".$pay_year."&pay_month=".$pay_month; 
$configesponse = callAPI($vurl);
//echo "<pre> dcsdc"; print_r($configesponse); echo "</pre>";
$presulta = json_decode($configesponse,1);
$unique = $presulta['componant'];
if(count((array)$unique) !=0){
	ksort($unique);
}
function valueemtpydash($val){
	return $val==""?" - ":$val;
}
// $premy =  date('Y-m-d',strtotime($pay_year."-".$pay_month."-01" . " - 1 month"));
// $pre_m = date('m',strtotime($premy));
// $pre_y = date('Y',strtotime($premy));


// echo $pvurl = API."?mode=getmonthlyctcreports&pay_year=".$pre_y."&pay_month=".$pre_m; 
// $pconfigesponse = callAPI($pvurl);
// $presulta = json_decode($pconfigesponse,1);



foreach($presulta['list'] AS $key=>$rows){  
	$sal =0;
	foreach($unique AS $names1){
	
		if($names1=='Net Salary'){
		    $listvalue = filterArrayValue($presulta['list'][$key],'name',$names1);
	    	$avall = $listvalue[0]['salary_component_actual']==""?"0":$listvalue[0]['salary_component_actual'];
		    $sal= $sal+ $avall;
		    
		}							
	}
	$premondetail[$key]['sal'] = $sal;
}

foreach($resulta['terminations'] as $termination){
	$teremp[]=$termination['emp_id'];
}
?>
<div class="page-body">
	<div class="row">
		<div class="col-sm-12">
			<div class="card ">
				<div class="card-block">
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
							<h4 class="card-label text-center f-w-700">Employee's CTC Details <?php echo date("M Y",strtotime($pay_month_year)); ?> </br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small></h4>
					
						</div>
						<div class="col-lg-3">		
							<form method="post" action="#">
								<div class="input-group input-group-button"> 
									<input type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input"  class="form-control form-control-sm width1" style="font-size: 18px;height: 48px !important;padding: 10px 15px;">  <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0">
										Submit
									</button> </div>
								</form>
							</div>

						</div>
						</div>
					
				<div class="dt-responsive table-responsive mt-2">
					<?php //echo "<pre>";  print_r($resulta); 
					$parray = array('Gross Salary','Provident Fund','CTC','Basic','HRA','Other Allowance','APLVP','Gross Salary');
					//$aarray = array('Basic','HRA','Other Allowance','APLVP','Gross Salary','Provident Fund','ESI','Professional Tax','Advance','Other Recoveries','TDS','Net Salary');
					//$aarray = array('Basic','HRA','Other Allowance','APLVP','Gross Salary','Provident Fund','ESI','Professional Tax','Advance','Other Recoveries','TDS','Net Salary','Arrear Basic','Arrear HRA','Arrear Other Allowance','Arrear PF','Arrear NET');
					
					//$aarray_salarys = array('Arrear Basic','Arrear HRA','Arrear Other Allowance','Arrear PF','Arrear NET');
					
					//$aarray = array('Basic'=>'Basic','HRA'=>'HRA','Other Allowance'=>'Other Allowance','APLVP'=>'APLVP','Gross Salary'=>'Gross Salary','Provident Fund'=>'Provident Fund','ESI'=>'ESI','Professional Tax'=>'Professional Tax','Advance'=>'Advance','Other rec'=>'OTHERRECO','TDS'=>'TDS','Net Salary'=>'Net Salary');		
 ?>
					<table id="basic-btn" class="table table-striped table-bordered nowrap">
						<thead>
							<tr>
								<th> SL NO </th>	
								<th>Name</th>
								<th>Code</th>
								<th>Organisation</th>	
								<th>DOJ</th>
								<th>Department</th>
								<th>Designation</th> 
								<th>Month Days </th>
								<th>Payable Days</th> 
								<?php foreach($unique  AS $aaarray ){ ?>
									<th><?php echo $aaarray; ?></th>
								<?php } ?>
													
								<th> <?php echo date("M",strtotime($pay_month_year)); ?> Salary </th>
								<th><?php echo date("M",strtotime($premy)); ?>  Salary </th>
								<th>  Difference </th>
								<th>  Remarks </th>
							</tr>
						</thead>
						<tbody>
							<?php $i=1; foreach($presulta['list'] AS $key=>$rows){  
							    $rows = $rows[0]; $cval = array(); 
							    /*if($rows['emp_code']=='INFIC001'){
							    	print_r($presulta['list']);
								}*/
							?>
								<tr <?php if(in_array($femp_id,(array)$teremp)){ ?> class="bg-warning" <?php }?> >
									<td><?php echo $i; ?></td>
									<td><?php echo valueemtpydash($rows['Employee Name']); ?></td>
									<td><?php echo valueemtpydash($rows['emp_code']); ?></td>
									<td>EVOS</td>
									<!-- <td><?php echo valueemtpydash($rows['emp_code']); ?></td> -->
									<td><?php echo valueemtpydash($rows['DOJ']); ?></td>
									<td><?php echo valueemtpydash($rows['Department']); ?></td>
									<td><?php echo valueemtpydash($rows['Designation']); ?></td>
									
									<td class="text-right"><?php echo valueemtpydash($rows['Total Days']); ?></td>
									<td class="text-right"><?php echo valueemtpydash($rows['Payable Days']); ?></td>

									<?php $npb=0; $epf_w=0; $esic_w=0;  $sal=0;  $otde=0;
										foreach($unique AS $names1){
										$listvalue = filterArrayValue($presulta['list'][$key],'name',$names1);
										$aval = $listvalue[0]['salary_component_actual']==""?"-":$listvalue[0]['salary_component_actual'];
										?>
										<td class="text-right">
										<?php echo customeummarmonyformate(round($aval));?>
										</td>
									<?php 
									    if($names1=='Net Salary'){$sal = $aval; }
									    } ?>
									
									<td class="text-right"> <?php echo customeummarmonyformate(round($sal)); ?></td>
									<td class="text-right"> <?php echo customeummarmonyformate(round($premondetail[$key]['sal'])); ?> </td>
									<td class="text-right"> <?php $diff = $sal-$premondetail[$key]['sal'];
										echo $diff; ?> </td>
									<td> </td>
								</tr>
							<?php $i++;} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>