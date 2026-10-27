<?php
	 $vurl = API."?mode=customadmdashboardmodule3"; 
    $configesponse = callAPI($vurl);
	$mcats = json_decode($configesponse,1);
	//echo "<pre>"; print_r($mcats); exit;
	foreach($mcats as $mkey=>$mca){
		$maingra['months'][]=date('M',strtotime($mkey));
		$budget = 0; $bilable =0;
		foreach($mca as $madmin){
			$vendor_admin[$madmin['vendor_id']][] = $madmin;
			$cc_admin[$madmin['cc_id']][$mkey][] = $madmin;
			$vertical_admin[$madmin['vertical_id']][] = $madmin;
			$department_admin[$madmin['department_id']][] = $madmin;
			$budget =$budget+$madmin['budgeted_amount'];
			$bilable =$bilable+$madmin['billable_amount'];
		}
		$maingra['bdget'][]=$budget;
		$maingra['billable'][]=$bilable;
	}
	foreach($vertical_admin as $vkey=>$vertical_a){
		foreach($vertical_a as $vertical_ad){
			$name = $vertical_ad['vertical_name'];
			$vbdget = $vbdget+$vertical_ad['budgeted_amount'];
			$vbillable = $vbillable+$vertical_ad['billable_amount'];
		}
		$vtdetail['name'][] = $name;
		$vtdetail['bdget'][] = $vbdget;
	}
	foreach($cc_admin as $cckey=>$cc_admins){
		$ym =array();
		foreach($cc_admins as $yymm=>$cc_adminsq){
			foreach($cc_adminsq as $cc_adm){
				$name = $cc_adm['cost_center_name'];

				//$vbdget = $vbdget+$vertical_ad['budgeted_amount'];
				$ccbillable[$cckey][$yymm]['billable_amount'] = $vbillable[$cckey][$yymm]['billable_amount']+$cc_adm['billable_amount'];
			}
			$ym[] = $yymm;
		}
		$cctdetail['name'][$cckey] = $name;
		$cctdetail['ym'] = $ym;

	}

	foreach($ccbillable as $cckey=>$ccbillaa){
		$ccarray = array();
		foreach($ccbillaa as $ccbil){
			$ccarray[]=$ccbil['billable_amount']/1000;
		}
		//print_r($ccarray);
		$cdatas[$cckey] = implode('|',(array)$ccarray);
	}
	//ksort($cdatas[$cckey]);
	$cctdetail['data'] = implode(',',(array)$cdatas);



	//echo "<pre>"; print_r($ccbillable); print_r($cctdetail); exit;
    $vurl = API."?mode=togetverticaldata"; 
    $configesponse = callAPI($vurl);
	$vcats = json_decode($configesponse,1);
	//print_r($vcats);
    foreach($vcats as $vcat){
        $vnames[] = $vcat['verticallame'];
        $vvalue[] = $vcat['charg'];
    }

    $vurl = API."?mode=togetcoscentergraphdata"; 
    $configesponse = callAPI($vurl);
	$cdatas = json_decode($configesponse,1);
	$vurl = API."?mode=togetvendorgraphdata"; 
    $configesponse = callAPI($vurl);
	$vendordatas= json_decode($configesponse,1);
	foreach($cdatas as $cdata){
	    if($cdata['cost_center']!=""){
    	    $cname[$cdata['cost_center']] = $cdata['cost_center_name'];
    	    //$cyx[$cdata['monthdate']] = $cdata['monthdate'];
    	    $ccosval[$cdata['cost_center']][$cdata['monthdate']] = round($cdata['chargable_amount']);
	    }
	}
	foreach($vendordatas as $vendordata){
	    if($vendordata['vendor_id']!=""){
    	    $vname[$vendordata['vendor_id']] = $vendordata['vendor_name'];
    	    //$cyx[$cdata['monthdate']] = $cdata['monthdate'];
    	    $vendorval[$vendordata['vendor_id']][$vendordata['monthdate']] = round($vendordata['chargable_amount']);
	    }
	}
	$fmont = date('m');
	if($fmont >3){ $fdate = date('Y')."-04"; }else{ $fdate = date("Y", strtotime("-1 years"))."-04"; }
	for($i=1; $i<13 ;$i++){
	   $cyx[] = $fdate;
	   
	   foreach($ccosval as $key=>$ccos){
	       $ccosval[$key][$fdate] = $ccos[$fdate]!="" ? $ccos[$fdate] : 0;
	   }
	   foreach($vendorval as $key=>$vendorva){
	       $vendorval[$key][$fdate] = $vendorva[$fdate]!="" ? $vendorva[$fdate] : 0;
	   }
	   $fdate = date('Y-m', strtotime($fdate." +1 month"));
	}
	foreach($ccosval as $key=>$ccos){
	    $ccosvaldata[]= implode("|",(array)$ccos);
	}
	foreach($vendorval as $key=>$vendorv){
	    $vendorvaldata[]= implode("|",(array)$vendorv);
	}
	//print_r($ccosvaldata);
	$ccosvaldatas = implode(",",(array)$ccosvaldata);
	$vendorvaldatas = implode(",",(array)$vendorvaldata);
	//echo "ravi".$ccosvaldatas; exit;
    
?>
<div class="row p-5">
	<!--<div class="col-lg-6 col-xxl-6">
	<?php //include('custom_view_vms_jobs.php'); ?>
</div>-->

<div class="col-lg-8  draggable-zone">
	<?php include('custom_view_vms_budget.php'); ?>
</div>	  
<div class="col-lg-4">
	<!--begin::Card-->
	<input type="hidden" name="vname" id="vname" value="<?php echo implode("|",(array)$vtdetail['name']);?>">
	<input type="hidden" name="vvalue" id="vvalue" value="<?php echo implode("|",(array)$vtdetail['bdget']);?>">
	<div class="card card-custom gutter-b">
		<div class="card-header">
			<div class="card-title">
				<h3 class="card-label">Vertical Wise  Billing</h3>
			</div>
		</div>
		<div class="card-body">
			<!--begin::Chart-->
			<div id="admin_dash_chart2" class="d-flex justify-content-center"></div>
			<!--end::Chart-->
		</div>
	</div>
	<!--end::Card-->
</div>
<div class="col-lg-6">
	<!--begin::Card-->
	<input type="hidden" name="coscentervalue" id="coscentervalue" value="<?php echo $cctdetail['data']; ?>">
	<input type="hidden" name="coscentercount" id="coscentercount" value="<?php echo count((array)$cctdetail['name']); ?>">
	<input type="hidden" name="coscentery" id="coscentery" value="<?php echo implode("|",(array)$cctdetail['ym']); ?>">
	<input type="hidden" name="coscentertitle" id="coscentertitle" value="<?php echo implode("|",(array)$cctdetail['name']); ?>">
	<div class="card card-custom gutter-b">
		<div class="card-header">
			<div class="card-title">
				<h3 class="card-label">CostCenter Wise Usage </h3>
			</div>
		</div>
		<div class="card-body">
			<!--begin::Chart-->
			<div id="admin_dash_chart3"></div>
			<!--end::Chart-->
		</div>
	</div>
	<!--end::Card-->
</div>
<div class="col-lg-6">
    <input type="hidden" name="vendorvalue" id="vendorvalue" value="<?php echo $vendorvaldatas; ?>">
	<input type="hidden" name="vendorcount" id="vendorcount" value="<?php echo count((array)$vname); ?>">
	<input type="hidden" name="vendory" id="vendory" value="<?php echo implode("|",(array)$cyx); ?>">
	<input type="hidden" name="coscentertitle" id="vendortitle" value="<?php echo implode("|",(array)$vname); ?>">
    <div class="card card-custom gutter-b">
		<div class="card-header">
			<div class="card-title">
				<h3 class="card-label">Vendor Wise Usage </h3>
			</div>
		</div>
		<div class="card-body">
			<!--begin::Chart-->
			<div id="admin_dash_chart4"></div>
			<!--end::Chart-->
		</div>
	</div>
</div>

				
	<div class="col-lg-6 order-2 order-xxl-1">
		<!--begin::Advance Table Widget 2-->
		<div class="card card-custom card-stretch gutter-b">
			<!--begin::Header-->
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label font-weight-bolder text-dark">All Vendors</span>
					<span class="text-muted mt-3 font-weight-bold font-size-sm">More than 400+ new members</span>
				</h3>
				<div class="card-toolbar">
					<ul class="nav nav-pills nav-pills-sm nav-dark-75">
						<li class="nav-item">
							<a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_tab_pane_11_1">Month</a>
						</li>
						<li class="nav-item">
							<a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_tab_pane_11_2">Week</a>
						</li>
						<li class="nav-item">
							<a class="nav-link py-2 px-4 active" data-toggle="tab" href="#kt_tab_pane_11_3">Day</a>
						</li>
					</ul>
				</div>
			</div>
			<!--end::Header-->
			<!--begin::Body-->
			<div class="card-body pt-2 pb-0 mt-n3">
				<div class="tab-content mt-5" id="myTabTables11">
					<!--begin::Tap pane-->
					<div class="tab-pane fade show active" id="kt_tab_pane_11_3" role="tabpanel" aria-labelledby="kt_tab_pane_11_1">
						<!--begin::Table-->
						<div class="table-responsive">
							<table class="table table-borderless table-vertical-center">
								<thead>
									<tr>
										<th class="p-0 w-40px"></th>
										<th class="p-0 min-w-200px"> Vendor Name</th>
										<th class="p-0 min-w-100px text-center"> All Jobs</th>
										<th class="p-0 min-w-125px text-center"> Apply Candidates</th>
										<th class="p-0 min-w-110px text-center">Approved Candidates</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="pl-0 py-4">
											<div class="symbol symbol-50 symbol-light">
												<span class="symbol-label">
													<img src="assets/media/svg/misc/003-puzzle.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td class="pl-0">
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Payrol Application</a>
											
										</td>
										<td class="text-center">
											<span class="text-dark-75 font-weight-bolder d-block font-size-lg">15</span>
											
										</td>
										<td class="text-center">
											<span class="text-muted font-weight-500">1200</span>
										</td>
										<td class="text-center">
											<span class="label label-lg label-light-success label-inline">10</span>
										</td>
										
									</tr>
									
								</tbody>
							</table>
						</div>
						<!--end::Table-->
					</div>
					<!--end::Tap pane-->
					<!--begin::Tap pane-->
							
				</div>
			</div>
			<!--end::Body-->
		</div>
		<!--end::Advance Table Widget 2-->
	</div>
	<div class="col-lg-6 order-2 order-xxl-1">
		<!--begin::Advance Table Widget 2-->
		<div class="card card-custom card-stretch gutter-b">
			<!--begin::Header-->
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label font-weight-bolder text-dark">All Jobs</span>
					<span class="text-muted mt-3 font-weight-bold font-size-sm">More than 400+ new members</span>
				</h3>
				<div class="card-toolbar">
					<ul class="nav nav-pills nav-pills-sm nav-dark-75">
						<li class="nav-item">
							<a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_tab_pane_111_1">Month QQ</a>
						</li>
						<li class="nav-item">
							<a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_tab_pane_111_2">Week</a>
						</li>
						<li class="nav-item">
							<a class="nav-link py-2 px-4 active" data-toggle="tab" href="#kt_tab_pane_111_3">Day</a>
						</li>
					</ul>
				</div>
			</div>
			<!--end::Header-->
			<!--begin::Body-->
			<div class="card-body pt-2 pb-0 mt-n3">
				<div class="tab-content mt-5" id="myTabTables11">
					<!--begin::Tap pane-->
					<div class="tab-pane fade show active" id="kt_tab_pane_111_1" role="tabpanel" aria-labelledby="kt_tab_pane_11_1">
						<!--begin::Table-->
						<div class="table-responsive">
							<table class="table table-borderless table-vertical-center">
								<thead>
									<tr>
										<th class="p-0 w-40px"></th>
										<th class="p-0 min-w-200px">Job Title</th>
										<th class="p-0 min-w-100px"> All Vendors</th>
										<th class="p-0 min-w-125px"> Apply Candidates</th>
										<th class="p-0 min-w-110px">Approved Candidates</th>
										
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="pl-0 py-4">
											<div class="symbol symbol-50 symbol-light">
												<span class="symbol-label">
													<img src="assets/media/svg/misc/003-puzzle.svg" class="h-50 align-self-center" alt="" />
												</span>
											</div>
										</td>
										<td class="pl-0">
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Payrol Application</a>
											
										</td>
										<td class="text-center">
											<span class="text-dark-75 font-weight-bolder d-block font-size-lg">15</span>
											
										</td>
										<td class="text-center">
											<span class="text-muted font-weight-500">1200</span>
										</td>
										<td class="text-center">
											<span class="label label-lg label-light-success label-inline">10</span>
										</td>
										
									</tr>
									
								</tbody>
							</table>
						</div>
						<!--end::Table-->
					</div>
					<!--end::Tap pane-->
					
				</div>
			</div>
			<!--end::Body-->
		</div>
		<!--end::Advance Table Widget 2-->
	</div>	
</div>
	<div class="col-lg-8 draggable-zone">
         <?php include('custom_view_admin_dashboard_report.php'); ?>
      </div>	
					

													