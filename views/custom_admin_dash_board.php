<?php
$vurl = API."?mode=togetVendorAgreementexpirelist"; 
    $configesponse = callAPI($vurl);
	$vendor_agreement = json_decode($configesponse,1);
	//print_r($vendor_agreement); exit;
	 $vurl = API."?mode=customadmdashboardmodule3"; 
    $configesponse = callAPI($vurl);
	$mcats = json_decode($configesponse,1);
	//echo "<pre>"; print_r($mcats); exit;
	foreach($mcats as $mkey=>$mca){
		$overallemployees[$mkey]=0;
		$maingra['months'][]=date('M',strtotime($mkey));
		$budget = 0; $bilable =0;
		foreach($mca as $madmin){
			$vendor_admin[$madmin['vendor_id']][$mkey][] = $madmin;
			$cc_admin[$madmin['cc_id']][$mkey][] = $madmin;
			$vertical_admin[$madmin['vertical_id']][] = $madmin;
			$department_admin[$madmin['department_id']][] = $madmin;
			$budget =$budget+$madmin['budgeted_amount'];
			$bilable =$bilable+$madmin['billable_amount'];
			$overallemployees[$mkey] = $overallemployees[$mkey]+1;
		}
		$maingra['bdget'][]=$budget;
		$maingra['billable'][]=$bilable;
	}
	//echo "<pre>"; print_r($vendor_admin);exit;
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
				$ccbillable[$cckey][$yymm]['billable_amount'] = $ccbillable[$cckey][$yymm]['billable_amount']+$cc_adm['billable_amount'];
				$ccbillable[$cckey][$yymm]['no_emp'] = $ccbillable[$cckey][$yymm]['no_emp']+1;
			}
			$ym[] = $yymm;
		}
		$cctdetail['name'][$cckey] = $name;
		$cctdetail['ym'] = $ym;

	}
	//echo "<pre>"; print_r($ccbillable); exit;
	foreach($ccbillable as $cckey=>$ccbillaa){
		$ccarray = array(); $ccemparray =array();
		foreach($ccbillaa as $ccbil){
			$ccarray[]=$ccbil['billable_amount']/1000;
			$ccemparray[] = $ccbil['no_emp'];
		}
		//print_r($ccarray);
		$cdatas[$cckey] = implode('|',(array)$ccarray);
		$cdatasemp[$cckey] = implode('|',(array)$ccemparray);
	}
	$cctdetail['data'] = implode(',',(array)$cdatas);
	$cctdetail['noemp'] = implode(',',(array)$cdatasemp);
	foreach($vendor_admin as $vnkey=>$vn_admins){
		$ym =array();
		
		foreach($vn_admins as $yymm=>$vn_adminsq){
			$vnbillable[$vnkey][$yymm]['noemp']=0;
			foreach($vn_adminsq as $vn_adm){
				$name = $vn_adm['vendor_name'];
				$vnbillable[$vnkey][$yymm]['billable_amount'] = $vnbillable[$vnkey][$yymm]['billable_amount']+$vn_adm['billable_amount'];
				$vnbillable[$vnkey][$yymm]['noemp'] = $vnbillable[$vnkey][$yymm]['noemp']+1;
			}
			$ym[] = $yymm;
		}
		$vntdetail['name'][$vnkey] = $name;
		$vntdetail['ym'] = $ym;
	}
	foreach($vnbillable as $vnkey=>$vnbillaa){
		$vnarray = array(); $vnarrayemp= array();
		foreach($vnbillaa as $vnbil){
			$vnarray[]=$vnbil['billable_amount']/1000;
			$vnarrayemp[]=$vnbil['noemp'];
		}
		$vndatas[$vnkey] = implode('|',(array)$vnarray);
		$vndatasemp[$vnkey] = implode('|',(array)$vnarrayemp);
	}
	$vntdetail['data'] = implode(',',(array)$vndatas);
	$vntdetail['noemp'] = implode(',',(array)$vndatasemp);
//echo "<pre>"; print_r($vnbillable); print_r($vntdetail); exit;
/*
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
	    $ccosvaldata[]= implode("|",$ccos);
	}
	foreach($vendorval as $key=>$vendorv){
	    $vendorvaldata[]= implode("|",$vendorv);
	}
	//print_r($ccosvaldata);
	$ccosvaldatas = implode(",",$ccosvaldata);
	$vendorvaldatas = implode(",",$vendorvaldata);
	//echo "ravi".$ccosvaldatas; exit;*/
    
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
			<div class="card-toolbar">
				<a target="_blank" href="index.php?option=COM_VIEW_VERTICAL" class="btn btn-success font-weight-bolder mr-1">
					Full Report
				</a>
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
			<div class="card-toolbar">
				<a target="_blank" href="index.php?option=COM_VIEW_COSCENTER_WISE_USAGE_REPORT" class="btn btn-success font-weight-bolder mr-1">
					Full Report
				</a>
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
	<input type="hidden" name="vendorvalue" id="vendorvalue" value="<?php echo $vntdetail['data']; ?>">
	<input type="hidden" name="vendorcount" id="vendorcount" value="<?php echo count((array)$vntdetail['name']); ?>">
	<input type="hidden" name="vendory" id="vendory" value="<?php echo implode("|",(array)$vntdetail['ym']); ?>">
	<input type="hidden" name="vendortitle" id="vendortitle" value="<?php echo implode("|",(array)$vntdetail['name']); ?>">
    <div class="card card-custom gutter-b">
		<div class="card-header">
			<div class="card-title">
				<h3 class="card-label">Vendor Wise Usage </h3>
			</div>
			<div class="card-toolbar">
				<a target="_blank" href="index.php?option=COM_VIEW_VENDOR_WISE_USAGE" class="btn btn-success font-weight-bolder mr-1">
					Full Report
				</a>
			</div>
		</div>
		<div class="card-body">
			<!--begin::Chart-->
			<div id="admin_dash_chart4"></div>
			<!--end::Chart-->
		</div>
	</div>
</div>

<!---- employee Counts Start ---->
<div class="col-lg-6">
	<!--begin::Card-->
	<input type="hidden" name="coscentervalue" id="coscentervalueemp" value="<?php echo $cctdetail['noemp']; ?>">
	<input type="hidden" name="coscentercount" id="coscentercountemp" value="<?php echo count((array)$cctdetail['name']); ?>">
	<input type="hidden" name="coscentery" id="coscenteryemp" value="<?php echo implode("|",(array)$cctdetail['ym']); ?>">
	<input type="hidden" name="coscentertitle" id="coscentertitleemp" value="<?php echo implode("|",(array)$cctdetail['name']); ?>">
	<div class="card card-custom gutter-b">
		<div class="card-header">
			<div class="card-title">
				<h3 class="card-label">CostCenter Wise Employee Count </h3>
			</div>
			
		</div>
		<div class="card-body">
			<!--begin::Chart-->
			<div id="admin_dash_chart5"></div>
			<!--end::Chart-->
		</div>
	</div>
	<!--end::Card-->
</div>
<div class="col-lg-6">
	<input type="hidden" name="vendorvalue" id="vendorvalueemp" value="<?php echo $vntdetail['noemp']; ?>">
	<input type="hidden" name="vendorcount" id="vendorcountemp" value="<?php echo count((array)$vntdetail['name']); ?>">
	<input type="hidden" name="vendory" id="vendoryemp" value="<?php echo implode("|",(array)$vntdetail['ym']); ?>">
	<input type="hidden" name="vendortitle" id="vendortitleemp" value="<?php echo implode("|",(array)$vntdetail['name']); ?>">
	<input type="hidden" name="vendortitle" id="month_count" value="<?php echo date('n'); ?>">
    <div class="card card-custom gutter-b">
		<div class="card-header">
			<div class="card-title">
				<h3 class="card-label">Vendor Wise Employee Count </h3>
			</div>
			
		</div>
		<div class="card-body">
			<!--begin::Chart-->
			<div id="admin_dash_chart6"></div>
			<!--end::Chart-->
		</div>
	</div>
</div>
<div class="col-lg-6">
	<!--begin::Card-->
	<input type="hidden" name="overallemployees" id="overallemployees" value="<?php echo implode(',',(array)$overallemployees); ?>">
	<input type="hidden" name="overallmonth" id="overallmonth" value="<?php echo implode(',',(array)$maingra['months']); ?>">
	<div class="card card-custom gutter-b">
		<!--begin::Header-->
		<div class="card-header h-auto">
			<!--begin::Title-->
			<div class="card-title py-5">
				<h3 class="card-label">Overall Headcount</h3>
			</div>
			<!--end::Title-->
		</div>
		<!--end::Header-->
		<div class="card-body">
			<!--begin::Chart-->
			<div id="chart_1"></div>
			<!--end::Chart-->
		</div>
	</div>
	<!--end::Card-->
</div>
	<div class="col-lg-6 order-2 order-xxl-1">
		
		<div class="card card-custom card-stretch gutter-b">
			
			<div class="card-header border-0 pt-5">
				<h3 class="card-title align-items-start flex-column">
					<span class="card-label font-weight-bolder text-dark">Vendor Agreements</span>
				</h3>
				<div class="card-toolbar">
					<ul class="nav nav-pills nav-pills-sm nav-dark-75">
						<li class="nav-item">
							<a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_tab_pane_11_1">Quarter</a>
						</li>
						<li class="nav-item">
							<a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_tab_pane_11_2">Month</a>
						</li>
						<li class="nav-item">
							<a class="nav-link py-2 px-4 active" data-toggle="tab" href="#kt_tab_pane_11_3">Week</a>
						</li>
					</ul>
				</div>
			</div>
			
			<div class="card-body pt-2 pb-0 mt-n3" style="max-height: 410px;overflow: auto;">
				<div class="tab-content mt-5" id="myTabTables11">	
					<div class="tab-pane fade show active" id="kt_tab_pane_11_3" role="tabpanel" aria-labelledby="kt_tab_pane_11_1">				
						<div class="table-responsive">
							<table class="table table-borderless table-vertical-center">
								<thead>
									<tr>
										<th class="p-0 w-40px"></th>
										<th class="p-0 min-w-200px"> Vendor Name</th>
										<th class="p-0 min-w-100px text-center"> Agreement Name</th>
										<th class="p-0 min-w-125px text-center"> Sign Date</th>
										<th class="p-0 min-w-110px text-center">Expiry Date	</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($vendor_agreement['week'] as $agreement){ ?>
									<tr>
										<td class="pl-0 py-4">
											<?php if($agreement['image']!=""){ ?>
												<div class="d-flex align-items-center">
													<div class="symbol symbol-40">														
															<img src="uploads/customer/<?php echo $agreement['image']; ?>" class="h-50 align-self-center" alt="" />
													</div>
												</div>
										<?php }else{ ?>
											<div class="symbol symbol-40 mr-3"> <span class="symbol-label font-size-h5"><?php echo initials($agreement['display_name']); ?></span> </div>
										<?php } ?>
										</td>
										<td class="pl-0">
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">&nbsp;
												<?php echo $agreement['display_name']; ?></a>											
										</td>
										<td class="text-center">
											<span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo $agreement['agreement_name']; ?></span>
											
										</td>
										<td class="text-center">
											<span class="text-muted font-weight-500"><?php echo date('d M, Y',strtotime($agreement['signed_date'])); ?></span>
										</td>
										<td class="text-center">
											<span class="label label-lg label-light-success label-inline"><?php echo date('d M, Y',strtotime($agreement['expiry_date'])); ?></span>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="tab-pane" id="kt_tab_pane_11_2" role="tabpanel" aria-labelledby="kt_tab_pane_11_2">				
						<div class="table-responsive">
							<table class="table table-borderless table-vertical-center">
								<thead>
									<tr>
										<th class="p-0 w-40px"></th>
										<th class="p-0 min-w-200px"> Vendor Name</th>
										<th class="p-0 min-w-100px text-center"> Agreement Name</th>
										<th class="p-0 min-w-125px text-center"> Sign Date</th>
										<th class="p-0 min-w-110px text-center">Expiry Date	</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($vendor_agreement['month'] as $agreement){ ?>
									<tr>
										<td class="pl-0 py-4">
												<?php if($agreement['image']!=""){ ?>
												<div class="d-flex align-items-center">
													<div class="symbol symbol-40">														
															<img src="uploads/customer/<?php echo $agreement['image']; ?>" class="h-50 align-self-center" alt="" />
													</div>
												</div>
										<?php }else{ ?>
											<div class="symbol symbol-40 mr-3"> <span class="symbol-label font-size-h5"><?php echo initials($agreement['display_name']); ?></span> </div>
										<?php } ?>
										</td>
										<td class="pl-0">
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">&nbsp;
												<?php echo $agreement['display_name']; ?></a>											
										</td>
										<td class="text-center">
											<span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo $agreement['agreement_name']; ?></span>
											
										</td>
										<td class="text-center">
											<span class="text-muted font-weight-500"><?php echo date('d M, Y',strtotime($agreement['signed_date'])); ?></span>
										</td>
										<td class="text-center">
											<span class="label label-lg label-light-success label-inline"><?php echo date('d M, Y',strtotime($agreement['expiry_date'])); ?></span>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="tab-pane" id="kt_tab_pane_11_1" role="tabpanel" aria-labelledby="kt_tab_pane_11_1">				
						<div class="table-responsive">
							<table class="table table-borderless table-vertical-center">
								<thead>
									<tr>
										<th class="p-0 w-40px"></th>
										<th class="p-0 min-w-200px"> Vendor Name</th>
										<th class="p-0 min-w-100px text-center"> Agreement Name</th>
										<th class="p-0 min-w-125px text-center"> Sign Date</th>
										<th class="p-0 min-w-110px text-center">Expiry Date	</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($vendor_agreement['qtr'] as $agreement){ ?>
									<tr>
										<td class="pl-0 py-4">
										<?php if($agreement['image']!=""){ ?>
												<div class="d-flex align-items-center">
													<div class="symbol symbol-40">														
															<img src="uploads/customer/<?php echo $agreement['image']; ?>" class="h-50 align-self-center" alt="" />
													</div>
												</div>
										<?php }else{ ?>
											<div class="symbol symbol-40 mr-3"> <span class="symbol-label font-size-h5"><?php echo initials($agreement['display_name']); ?></span> </div>
										<?php } ?>
										</td>
										<td class="pl-0">
											<a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">&nbsp;
												<?php echo $agreement['display_name']; ?></a>											
										</td>
										<td class="text-center">
											<span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo $agreement['agreement_name']; ?></span>
											
										</td>
										<td class="text-center">
											<span class="text-muted font-weight-500"><?php echo date('d M, Y',strtotime($agreement['signed_date'])); ?></span>
										</td>
										<td class="text-center">
											<span class="label label-lg label-light-success label-inline"><?php echo date('d M, Y',strtotime($agreement['expiry_date'])); ?></span>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
	<!-- <div class="col-lg-6 order-2 order-xxl-1">
		
		<div class="card card-custom card-stretch gutter-b">
			
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
			
			<div class="card-body pt-2 pb-0 mt-n3">
				<div class="tab-content mt-5" id="myTabTables11">
					
					<div class="tab-pane fade show active" id="kt_tab_pane_11_3" role="tabpanel" aria-labelledby="kt_tab_pane_11_1">
						
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
						
					</div>
					
							
				</div>
			</div>
		
		</div>
	</div>
	<div class="col-lg-6 order-2 order-xxl-1">
		
		<div class="card card-custom card-stretch gutter-b">
			
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
			
			<div class="card-body pt-2 pb-0 mt-n3">
				<div class="tab-content mt-5" id="myTabTables11">
					
					<div class="tab-pane fade show active" id="kt_tab_pane_111_1" role="tabpanel" aria-labelledby="kt_tab_pane_11_1">
						
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
						
					</div>
					
					
				</div>
			</div>
			
		</div>
	
	</div>	 -->
</div>
	<!-- <div class="col-lg-8 draggable-zone">
         <?php //include('custom_view_admin_dashboard_report.php'); ?>
      </div> -->	
					

													