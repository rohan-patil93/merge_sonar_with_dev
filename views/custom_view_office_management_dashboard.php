<?php
	$_SESSION['apply_js']['office_management_transport'] = 1;
	$url = API."?mode=togetofficemanagementdashboard";
   $response = callAPI($url);
   $data = json_decode($response,1);
   $food = $data['food'];
?>
<script type="text/javascript">
	function recicedcourier(id=""){
        jQuery.ajax({
            url: "views/custom_insert_received_courier_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");                
            },
            error:function (){}
        });
    }
    function sendcourier(id=""){
        jQuery.ajax({
            url: "views/custom_insert_send_courier_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");                
            },
            error:function (){}
        });
    }
    function cartviewreqlist(val){
        jQuery.ajax({
            url: "views/custom_view_stationery_cart_details_popup.php",
            data:'option=noption&request_no='+val,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");                
            },
            error:function (){}
        });
    }
    function viewfoodinsert(val){
        if(val!=0){
            jQuery.ajax({
                url: "views/custom_insert_food_request_popup.php",
                data:'option=noption&rowid='+val,
                type: "POST",
                success:function(data){
                    $('#popup1').html(data);
                    $('#kt_modal_KTDatatable_remote').modal("show"); 
                                   
                },
                error:function (){}
            });
        }
    }
     function viewtransportrequest(val){
        console.log("UUUU");
        jQuery.ajax({
            url: "views/customtransportrequestpopup.php",
            data:'option=noption&rowid='+val,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show"); 
                             
            },
            error:function (){}
        });
     }
     function addandupdatestationery(item_id){
        jQuery.ajax({
            url: "views/custom_insert_stationery_popup.php",
            data:'option=noption&item_id='+item_id+'&type=topup',
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show"); 
                               
            },
            error:function (){}
        });
    }
</script>
<div class="row col-lg-12">
	<div class="col-lg-12">
		<div class="text-right">
			<a href="index.php?option=COM_VIEW_OFFICE_MANAGEMENT" class="btn btn-primary m-b-10">  
				View Details 
			</a> 
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card">
			<div class="card-header">
				<h5>In Couriers</h5>
			</div>
			<div class="card-body" style="padding:20px !important;">				
				<div class="row ">
					<div class="col-4 text-center">
						<div class="s-caption text-primary">
							<i class="ti-id-badge f-20"></i>
						</div>
						<div class="s-cont d-inline-block">
							<h5 class="f-w-700 m-b-0"><?php echo $data['couriers']['total_rec']; ?></h5>
							<p class="m-b-0 f-12">Total</p>
						</div>
					</div>
					<div class="col-4 b-l-default text-center">
						<div class="s-caption text-primary">
							<i class="ti-envelope f-20"></i>
						</div>
						<div class="s-cont d-inline-block">
							<h5 class="f-w-700 m-b-0"><?php echo $data['couriers']['rec_pending']; ?></h5>
							<p class="m-b-0 f-12">Pending </p>
						</div>
					</div>
					<div class="col-4 b-l-default text-center">
						<div class="s-caption text-primary">
							<i class="ti-envelope f-20"></i>
						</div>
						<div class="s-cont d-inline-block">
							<h5 class="f-w-700 m-b-0"><?php echo $data['couriers']['total_rec']-$data['couriers']['rec_pending']; ?></h5>
							<p class="m-b-0 f-12">Delivered</p>
						</div>
					</div>
				</div>
			</div>
			<div class="card-header">
				<h5>Out Couriers</h5>
			</div>
			<div class="card-body mb-2" style="padding: 20px !important;">				
				<div class="row ">
					<div class="col-4  text-center">
						<div class="s-caption text-primary">
							<i class="ti-id-badge f-20"></i>
						</div>
						<div class="s-cont d-inline-block">
							<h5 class="f-w-700 m-b-0"><?php echo $data['couriers']['total_send']; ?></h5>
							<p class="m-b-0 f-12">Total</p>
						</div>
					</div>
					<div class="col-4 b-l-default text-center">
						<div class="s-caption text-primary">
							<i class="ti-envelope f-20"></i>
						</div>
						<div class="s-cont d-inline-block">
							<h5 class="f-w-700 m-b-0"><?php echo $data['couriers']['send_pending']; ?></h5>
							<p class="m-b-0 f-12">Pending </p>
						</div>
					</div>
					<div class="col-4 b-l-default text-center">
						<div class="s-caption text-primary">
							<i class="ti-envelope f-20"></i>
						</div>
						<div class="s-cont d-inline-block">
							<h5 class="f-w-700 m-b-0"><?php echo $data['couriers']['total_send']-$data['couriers']['send_pending']; ?></h5>
							<p class="m-b-0 f-12">Delivered</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="col-lg-12">
			<div class="card prod-p-card card-blue m-b-10">
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col">
							<h6 class="m-b-5 text-white">Total Stationery</h6>
							<h3 class="m-b-0 f-w-700 text-white f-18"><?php echo $data['stationery']['total_stat']; ?></h3>
						</div>
						<div class="col-auto">
							<i class="fa fa-calendar-check text-c-blue f-18"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
			<div class="card prod-p-card card-yellow m-b-10">
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col">
							<h6 class="m-b-5 text-white">Pending Stationery</h6>
							<h3 class="m-b-0 f-w-700 text-white f-18"><?php echo $data['stationery']['stat_pending']; ?></h3>
						</div>
						<div class="col-auto">
							<i class="fas fa-money-bill-alt text-c-red f-18"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
			<div class="card prod-p-card card-green m-b-10">
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col">
							<h6 class="m-b-5 text-white">Received Stationery </h6>
							<h3 class="m-b-0 f-w-700 text-white f-18"><?php echo $data['stationery']['total_stat']-$data['stationery']['stat_pending']; ?></h3>
						</div>
						<div class="col-auto">
							<i class="fas fa-database text-c-blue f-18"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
			<div class="card prod-p-card card-red m-b-10">
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col">
							<h6 class="m-b-5 text-white">Below Minimum Quantity</h6>
							<h3 class="m-b-0 f-w-700 text-white f-18"><?php echo $data['min_sat']['minqty']; ?></h3>
						</div>
						<div class="col-auto">
							<i class="fas fa-dollar-sign text-c-green f-18"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card">
			<div class="card-header">
				<h5>Transport Requests</h5>
			</div>
			<div id="chart_Donut_mdash" style="width: 100%; height: 280px;"></div>
			<input type="hidden" name="" id="manga_ransport_sahboard" value="Total Requests:<?php echo $data['employee_transport']['total_emptr']; ?>||Pending Requests:<?php echo $data['employee_transport']['emptr_pending']; ?>||Approved:<?php echo $data['employee_transport']['total_emptr']-$data['employee_transport']['emptr_pending']; ?>">
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card">
			<div class="card-header">
				<h5>Food Requests</h5>
			</div>
			<div class="p-3 mb-2">
				<div class="pp-cont mt-2">
					<div class="row align-items-center m-b-20">
						<div class="col-auto">
							<i class="fas fa fa-life-ring f-24 "></i> <span class="f-20 f-w-600">&nbsp; &nbsp; Total Reuests</span>
						</div>
						<div class="col text-right">
							<h2 class="m-b-0 text-c-blue"><?php echo $data['employee_food']['total_empfr']; ?></h2>
						</div>
					</div>
					<div class="row align-items-center m-b-15">
						<div class="col-auto">
							<p class="m-b-0">Total </p>
						</div>
						<div class="col text-right">
							<p class="m-b-0 text-c-blue"><i class="fas fa-long-arrow-alt-up m-r-10"></i>
								<?php 
									$pr = 0;
									if($data['employee_food']['total_empfr']>0 && $data['employee_food']['total_empfr']!=""){
										$pr = round(($data['employee_food']['emptr_pending']/ $data['employee_food']['total_empfr'])*100);
									}
									$vr = 100-$pr; echo $vr;
								 ?> %
							</p>
						</div>
					</div>
					<div class="progress">
						<div class="progress-bar bg-c-blue" style="width:<?php echo $vr; ?>%"></div>
					</div>
				</div>
				<div class=" row col-lg-12 mt-3">
					<div class="col-lg-6 f-14">Pending : <?php echo $data['employee_food']['emptr_pending']; ?></div>
					<div class="col-lg-6 f-14">Approved :   <?php echo $data['employee_food']['total_empfr'] - $data['employee_food']['emptr_pending']; ?></div>
				</div>
				<div class="mt-3">
					<div class="card-block">
						<div class="row align-items-center">
							<div class="col">
								<h6 class="m-b-0"> This Month Food Requests</h6>
							</div>
							<div class="col-auto">
								<h5 class="m-b-0"><?php echo $data['month_food']['record_count']; ?></h5>
							</div>
							<div class="col-auto"><i class="fas fa-arrow-up text-c-green"></i></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card new-cust-card">
			<div class="card-header">
				<h5>In Couriers</h5>
				<div class="card-header-right" style="top:15px;right: 15px" >
					<ul class="nav nav-tabs md-tabs infisuite_tab border-0">
						<li class="nav-item">
							<a class="nav-link btn-sm active f-12 f-w-600" data-bs-toggle="tab" href="#month" role="tab">Pending</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn-sm f-12 f-w-600" data-bs-toggle="tab" href="#week" role="tab">Approved</a>

						</li>
					</ul>
				</div>
			</div>
			<div class="tab-content table-border-style custom-scroll" style="height: 280px;">
                <div class="tab-pane active" id="month" role="tabpanel">
					<div class="card-block">
						<?php $checka =0;
							foreach($data['list_couriers'] as $down){
							if($down['courier_type']==2 &&  $down['current_status']!=ST_APPROVED){
								$checka =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" style="margin-top: 0 !important;" alt=""></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="recicedcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['courier_company']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo $down['courier_ref_no']; ?></p>
								<span class="deactive text-muted">
									<i class="far fa-clock m-r-10"></i>
									<?php echo notificationdatesdiff($down['courier_date'], 'N'); ?>
								</span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checka ==0){?>
								<div class="card-px text-center">
									<p class="text-muted f-16 fw-bold">
										<i class="ti-alert text-warning"></i> No Couriers
									</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height:250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
				<div class="tab-pane" id="week" role="tabpanel">
					<div class="card-block">
						<?php $checkb =0; foreach($data['list_couriers'] as $down){
							if($down['courier_type']==2 &&  $down['current_status']==ST_APPROVED){
								$checkb =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="recicedcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['courier_company']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo $down['courier_ref_no']; ?></p>
								<span class="deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['courier_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checkb ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card new-cust-card">
			<div class="card-header">
				<h5>Out Couriers</h5>
				<div class="card-header-right" style="top:15px;right:15px" >
					<ul class="nav nav-tabs md-tabs infisuite_tab border-0">
						<li class="nav-item">
							<a class="nav-link btn-sm active f-12 f-w-600" data-bs-toggle="tab" href="#OOmonth" role="tab">Pending</a>

						</li>
						<li class="nav-item">
							<a class="nav-link btn-sm f-12 f-w-600" data-bs-toggle="tab" href="#OOweek" role="tab">Approved</a>

						</li>
					</ul>
				</div>
			</div>
			<div class="tab-content table-border-style custom-scroll" style="height: 280px;">
                <div class="tab-pane active" id="OOmonth" role="tabpanel">
					<div class="card-block">
						<?php $checka =0;
							foreach($data['list_couriers'] as $down){
							if($down['courier_type']==1 &&  $down['current_status']!=ST_APPROVED){
								$checka =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="sendcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['courier_name']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo $down['courier_mobile']; ?></p>
								<span class="deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['courier_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checka ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
				<div class="tab-pane" id="OOweek" role="tabpanel">
					<div class="card-block">
						<?php $checkb =0; foreach($data['list_couriers'] as $down){
							if($down['courier_type']==1 &&  $down['current_status']==ST_APPROVED){
								$checkb =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="sendcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['courier_company']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo $down['courier_ref_no']; ?></p>
								<span class="deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['courier_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checkb ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card new-cust-card">
			<div class="card-header">
				<h5>Stationery</h5>
				<div class="card-header-right" style="top:15px;right:15px" >
					<ul class="nav nav-tabs md-tabs infisuite_tab border-0">
						<li class="nav-item">
							<a class="nav-link btn-sm active f-12 f-w-600" data-bs-toggle="tab" href="#stmonth" role="tab">Pending</a>

						</li>
						<li class="nav-item">
							<a class="nav-link btn-sm f-12 f-w-600" data-bs-toggle="tab" href="#stweek" role="tab">Approved</a>

						</li>
					</ul>
				</div>
			</div>
			<div class="tab-content table-border-style custom-scroll" style="height:280px;">
                <div class="tab-pane active" id="stmonth" role="tabpanel">
					<div class="card-block">
						<?php $checka =0;
							foreach($data['list_stationery_requests'] as $down){
							if($down['current_status']!=ST_APPROVED){
								$checka =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['request_no']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo displayDate($down['require_date']); ?></p>
								<span class="deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['request_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checka ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
				<div class="tab-pane" id="stweek" role="tabpanel">
					<div class="card-block">
						<?php $checkb =0; foreach($data['list_stationery_requests'] as $down){
							if(  $down['current_status']==ST_APPROVED){
								$checkb =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['request_no']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo displayDate($down['require_date']); ?></p>
								<span class="deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['request_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checkb ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card new-cust-card">
			<div class="card-header">
				<h5>Stationery Below Minmum Qty</h5>				
			</div>
			<div class="tab-content table-border-style custom-scroll" style="height: 280px;">
				<div class="card-block">
					<?php $checkb =0; foreach($data['all_min_sat'] as $down){
							$checkb =1;
					 ?>
					<div class="align-middle mb-2 bg-gray b-radius-5">
						
						<div class="d-inline-block cursor-pointer" onclick="addandupdatestationery(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
							<a href="#!"><h6>
								<?php echo $down['item_name']; ?>
							</h6></a>
							<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
							<p class="text-muted m-b-0">
								Available Qty: <span class="mt-16 f-w-600"><?php echo $down['available_qty']; ?></span> 
								Minmum Qty: <span class="mt-16 f-w-600"><?php echo $down['minmum_qty']; ?> </span> 
							</p>
						</div>
					</div>
					<?php } 
						if($checkb ==0){?>
							<div class="card-px text-center ">
								<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
								<div class="text-center px-4">
									<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
								</div>   
							</div>
						<?php }
					?>
				</div>				
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card new-cust-card">
			<div class="card-header">
				<h5>Transport</h5>
				<div class="card-header-right" style="top:15px;right:15px" >
					<ul class="nav nav-tabs md-tabs infisuite_tab border-0">
						<li class="nav-item">
							<a class="nav-link btn-sm active f-12 f-w-600" data-bs-toggle="tab" href="#trmonth" role="tab">Pending</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn-sm f-12 f-w-600" data-bs-toggle="tab" href="#trweek" role="tab">Approved</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="tab-content table-border-style custom-scroll" style="height: 280px;">
                <div class="tab-pane active" id="trmonth" role="tabpanel">
					<div class="card-block">
						<?php $checka =0;
							foreach($data['list_employee_transports'] as $down){
							if($down['current_status']!=ST_APPROVED){
								$checka =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['pickup_point']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo displayDate($down['transport_date']); ?></p>
								<span class="deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['created_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checka ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
				<div class="tab-pane" id="trweek" role="tabpanel">
					<div class="card-block">
						<?php $checkb =0; foreach($data['list_employee_transports'] as $down){
							if(  $down['current_status']==ST_APPROVED){
								$checkb =1;
						 ?>
						<div class="align-middle mb-3 border-bottom pb-3">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer m-l-10" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!">
									<h6 class="text-primary"><?php echo $down['pickup_point']; ?></h6>
								</a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0 f-12 f-w-600 m-t-5"><?php echo displayDate($down['transport_date']); ?></p>
								<span class="deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['created_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checkb ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card new-cust-card">
			<div class="card-header">
				<h5>Food</h5>
				<div class="card-header-right" style="top:15px;right:15px" >
					<ul class="nav nav-tabs md-tabs infisuite_tab border-0" style="">
						<li class="nav-item">
							<a class="nav-link btn-sm active f-12 f-w-600" data-bs-toggle="tab" href="#fdmonth" role="tab">Pending</a>
						</li>
						<li class="nav-item">
							<a class="nav-link btn-sm f-12 f-w-600" data-bs-toggle="tab" href="#fdweek" role="tab">Approved</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="tab-content card-block table-border-style custom-scroll" style="height: 280px;">
                <div class="tab-pane active" id="fdmonth" role="tabpanel">
					<div class="card-block">
						<?php $checka =0;
							foreach($data['list_employee_food'] as $down){
							if($down['current_status']!=ST_APPROVED){
								$checka =1;
						 ?>
						<div class="align-middle m-b-25">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer" onclick="viewfoodinsert(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!"><h6>
									<?php echo $food[$down['food_option_id']]['food_option']; ?>
								</h6></a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0"><?php echo displayDate($down['start_date']); ?></p>
								<span class="status deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['created_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checka ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
				<div class="tab-pane" id="fdweek" role="tabpanel">
					<div class="card-block">
						<?php $checkb =0; foreach($data['list_employee_food'] as $down){
							if(  $down['current_status']==ST_APPROVED){
								$checkb =1;
						 ?>
						<div class="align-middle m-b-25">
							<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
								<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
							<?php }else{ ?>
								<a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
							<?php } ?>
							<div class="d-inline-block cursor-pointer" onclick="viewfoodinsert(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
								<a href="#!"><h6>
									
									<?php echo $food[$down['food_option_id']]['food_option']; ?>
								</h6></a>
								<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
								<p class="text-muted m-b-0"><?php echo displayDate($down['start_date']); ?></p>
								<span class="status deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['created_date'], 'N'); ?></span>
							</div>
						</div>
						<?php } ?>
						<?php } 
							if($checkb ==0){?>
								<div class="card-px text-center ">
									<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
									<div class="text-center px-4">
										<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
									</div>   
								</div>
							<?php }
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-12 col-12">
		<div class="card new-cust-card">
			<div class="card-header">
				<h5>This Month Employee Food Requests</h5>				
			</div>
			<div class="tab-content card-block table-border-style custom-scroll" style="height: 280px;">
				<div class="card-block">
					<?php $checkb =0; foreach($data['emp_food'] as $down){
							$checkb =1;
					 ?>
					 <div class="align-middle m-b-25">
					 	<?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
					 		<a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
					 	<?php }else{ ?>
					 		<a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
					 	<?php } ?>
					 	<div class="d-inline-block">
					 		<a href="#!"><h6>
					 			<?php echo $food[$down['food_option_id']]['food_option']; ?>
					 		</h6></a>
					 		<span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
					 		<p class="text-muted m-b-0"><?php echo displayDate($down['start_date']); ?></p>
					 		<span class="status deactive text-muted"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['created_date'], 'N'); ?></span>
					 	</div>
					 </div>
					<?php } 
						if($checkb ==0){?>
							<div class="card-px text-center ">
								<p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>   
								<div class="text-center px-4">
									<img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
								</div>   
							</div>
						<?php }
					?>
				</div>				
			</div>
		</div>
	</div>
</div>



