<?php
$cusurl = API."?mode=gettabledetails&table=".TBL_CUSTOMERS; 
$cusresponse = callAPI($cusurl); //file_get_contents($cusurl, false, $context);	
$cusresponse = json_decode($cusresponse,1);
//print_r($cusresponse);
$title = viewTitle($_GET['option']);

$cusummurl = API."?mode=gettabledetails&table=".TBL_CUSTOMER_SUMMARY; 
$cusummresponse = callAPI($cusummurl);//file_get_contents($cusummurl, false, $context);	
$cusummresponse = json_decode($cusummresponse,1);
foreach ($cusummresponse as $summ) {
	$array[$summ[id]]=$summ;
}
$url = API."?mode=singlerowtabledata&fid=application_table_name&rid=".TBL_CUSTOMER_SUMMARY."&table=".TBL_DISPLAYATTRIBUTES;
//echo $url;
$response = callAPI($url);//file_get_contents($url, false, $context);
$header = json_decode($response,1);
$headers = json_decode($header['list_attributes'],1);
//print_r($headers);

// echo $vurl;

$configesponse = callAPI($vurl);
return json_decode($configesponse,1);

$invoiceurl = API."?mode=gettabledetails&table=sales_invoices"; 
$cusresponse = callAPI($cusurl); //file_get_contents($cusurl, false, $context);	
$cusresponse = json_decode($cusresponse,1);
 ?>
 <div class="d-flex flex-column-fluid">
<div class="container-fluid">
	<div class="card card-custom card-sticky" id="kt_page_sticky_card">
				<div class="card-header">
					<div class="card-title">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand flaticon2-line-chart"></i>
				</span>
				<h3 class="card-label">&nbsp;<?php echo $title;  ?> </h3>		
			</div>
									
								<div class="kt-portlet__head-toolbar">
				<div class="card-toolbar">
						<a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
							<i class="ki ki-long-arrow-back icon-xs"></i>
							Back
						</a>
					&nbsp;
						<div class="dropdown dropdown-inline">
							<a href="index.php?option=COM_INSERT_CUSTOMERS"  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">
							<i class="la la-plus"></i>
							<?php echo "ADD NEW";  ?>
							</a>
					</div>
				</div>
			</div>
		</div>
		
		
				
					<?php foreach ($cusresponse as $key => $value) { 
							  $total_sales = $array[$value[id]]['total_sales'] !='' ? $array[$value[id]]['total_sales'] : '0.00';
							  $yearly_sales = $array[$value[id]]['yearly_sales'] !='' ? $array[$value[id]]['yearly_sales'] : '0.00';
							  $quarterly_sales = $array[$value[id]]['quarterly_sales'] !='' ? $array[$value[id]]['quarterly_sales'] : '0.00';
							  $monthly_sales = $array[$value[id]]['monthly_sales'] !='' ? $array[$value[id]]['monthly_sales'] : '0.00';
							  $total_payments = $array[$value[id]]['total_payments'] !='' ? $array[$value[id]]['total_payments'] : '0.00';
							  $yearly_payments = $array[$value[id]]['yearly_payments'] !='' ? $array[$value[id]]['yearly_payments'] : '0.00';
							  $quarterly_payments = $array[$value[id]]['quarterly_payments'] !='' ? $array[$value[id]]['quarterly_payments'] : '0.00';
							  $monthly_payments = $array[$value[id]]['monthly_payments'] !='' ? $array[$value[id]]['monthly_payments'] : '0.00';
							  $due_30 = $array[$value[id]]['due_30'] !='' ? $array[$value[id]]['due_30'] : '0';
							  $due_45 = $array[$value[id]]['due_45'] !='' ? $array[$value[id]]['due_45'] : '0';
							  $due_90 = $array[$value[id]]['due_90'] !='' ? $array[$value[id]]['due_90'] : '0';
							  $due_120 = $array[$value[id]]['due_120'] !='' ? $array[$value[id]]['due_120'] : '0';
							  $percentage = ($total_sales!=0 AND !is_nan($total_sales)) ? round((($total_sales-$total_payments)/$total_sales)*100) : '0';
							 //print_r($value);
							   $rid = deocdeId($value['id']);?>
<div class="card-body">
			<div class="card card-custom">
											<!--begin::Pic-->
<div class="row mb-2">
	<div class="form-group col-lg-12">
		<div class="d-flex py-2">
		   <div class="flex-shrink-0 mr-7 ">
		      <div class="symbol symbol-50 symbol-lg-120">
		         <img alt="Pic" src="assets/media//users/300_1.jpg" />
		      </div>
		   </div>
		   <div class="flex-grow-1">
		      <!--begin::Title-->
		      <div class="d-flex align-items-center justify-content-between flex-wrap mt-2">
		         <!--begin::User-->
		         <div class="mr-3">
		            <!--begin::Name-->
		            <a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3"><?php echo $value['display_name']; ?>
		            <i class="flaticon2-correct text-success icon-md ml-2"></i></a>
		            <!--end::Name-->
		            <!--begin::Contacts-->
		            <div class="d-flex flex-wrap my-2">
		               <a href="#" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
		                  <span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
		                     <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-notification.svg-->
		                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
		                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
		                           <rect x="0" y="0" width="24" height="24" />
		                           <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000" />
		                           <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5" />
		                        </g>
		                     </svg>
		                     <!--end::Svg Icon-->
		                  </span>
		                  j<?php echo $value['email']; ?>
		               </a>
		               <a href="#" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
		                  <span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
		                     <!--begin::Svg Icon | path:assets/media/svg/icons/General/Lock.svg-->
		                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
		                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
		                           <mask fill="white">
		                              <use xlink:href="#path-1" />
		                           </mask>
		                           <g />
		                           <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" fill="#000000" />
		                        </g>
		                     </svg>
		                     <!--end::Svg Icon-->
		                  </span>
		                  <?php echo $value['phone']; ?>
		               </a>
		               <a href="#" class="text-muted text-hover-primary font-weight-bold">
		                  <span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
		                     <!--begin::Svg Icon | path:assets/media/svg/icons/Map/Marker2.svg-->
		                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
		                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
		                           <rect x="0" y="0" width="24" height="24" />
		                           <path d="M9.82829464,16.6565893 C7.02541569,15.7427556 5,13.1079084 5,10 C5,6.13400675 8.13400675,3 12,3 C15.8659932,3 19,6.13400675 19,10 C19,13.1079084 16.9745843,15.7427556 14.1717054,16.6565893 L12,21 L9.82829464,16.6565893 Z M12,12 C13.1045695,12 14,11.1045695 14,10 C14,8.8954305 13.1045695,8 12,8 C10.8954305,8 10,8.8954305 10,10 C10,11.1045695 10.8954305,12 12,12 Z" fill="#000000" />
		                        </g>
		                     </svg>
		                     <!--end::Svg Icon-->
		                  </span>
		                  <?php echo $value['company']; ?>
		               </a>
		            </div>
		            <!--end::Contacts-->
		         </div>
		         <!--begin::User-->
		         <!--begin::Actions-->
		         <div class="my-lg-0 my-1">
		            <a href="index.php?option=COM_INSERT_INVOICES&Rid=<?= $rid ?>" class="btn btn-sm btn-light-primary font-weight-bolder text-uppercase mr-2">Create Invoice</a>
		            <a href="index.php?option=COM_INSERT_CUSTOMERS&Rid=<?= $rid ?>" class="btn btn-sm btn-primary font-weight-bolder text-uppercase">edit profile</a>
		         </div>
		         <!--end::Actions-->
		      </div>
		      <!--end::Title-->
		      <!--begin::Content-->
		      <div class="d-flex align-items-center flex-wrap justify-content-between">
		         <!--begin::Description-->
		         <div class="flex-grow-1 font-weight-bold text-dark-50 py-2 py-lg-2 mr-5">
		            <div class="kt-widget__desc">
		               Address : <?php echo $value['billing_address']; ?>
		               <br>  Website : <?php echo $value['website_url']; ?>
		            </div>
		         </div>
		         <!--end::Description-->
		         <!--begin::Progress-->
		         <div class="d-flex mt-4 mt-sm-0">
		            <span class="font-weight-bold mr-4">Due Progress</span>
		            <?php
		               if($percentage < 31){
		               	$color = '#00ff00';
		               }else if($percentage < 51){
		               	$color = '#FFA500';
		               }else{
		               	$color = '#ff0000';
		               }
		                $spp= 'style="width:'.$percentage .'%;background:'.$color.';"'; 
		                //$spp='style="width:'.$percentage .'%;"'; ?>
		            <div class="progress progress-xs mt-2 mb-2 flex-shrink-0 w-150px w-xl-250px">
		               <div class="progress-bar bg-success" role="progressbar"  <?php echo $spp; ?> aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
		            </div>
		            <span class="font-weight-bolder text-dark ml-4"><?php echo $percentage."%"; ?></span>
		         </div>
		         <!--end::Progress-->
		      </div>
		      <!--end::Content-->
		   </div>
		</div>
	</div>
	<div class="separator separator-solid my-12"></div>
	<div class="d-flex align-items-center flex-wrap">
   <!--begin: Item-->
   <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
      <span class="mr-4">
      <i class="flaticon-piggy-bank icon-2x text-muted font-weight-bold"></i>
      </span>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['total_sales']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($total_sales); ?></span>
      </div>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['yearly_sales']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($yearly_sales); ?></span>
      </div>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['quarterly_sales']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($quarterly_sales); ?></span>
      </div>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['monthly_sales']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($monthly_sales); ?></span>
      </div>
   </div>
   <!--end: Item-->
   <!--begin: Item-->
   <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
      <span class="mr-4">
      <i class="flaticon-confetti icon-2x text-muted font-weight-bold"></i>
      </span>
      <div class="d-flex flex-column text-dark-75">
         <span class="font-weight-bolder font-size-sm">Payment</span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($total_payments); ?></span>
      </div>
   </div>
   <!--end: Item-->
   <!--begin: Item-->
   <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
      <span class="mr-4">
      <i class="flaticon-pie-chart icon-2x text-muted font-weight-bold"></i>
      </span>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['due_30']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($due_30); ?></span>
      </div>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['due_45']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($due_45); ?></span>
      </div>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['due_90']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($due_90); ?></span>
      </div>
      <div class="d-flex flex-column text-dark-75 mr-5">
         <span class="font-weight-bolder font-size-sm"> <?php echo $headers['due_120']['lable']; ?></span>
         <span class="font-weight-bolder font-size-h5"><?php echo customeummarmonyformate($due_120); ?></span>
      </div>
   </div>
   <!--end: Item-->
   <!--begin: Item-->
   <div class="d-flex align-items-center flex-lg-fill mr-5 my-1">
      <span class="mr-4">
      <i class="flaticon-file-2 icon-2x text-muted font-weight-bold"></i>
      </span>
      <div class="d-flex flex-column flex-lg-fill">
         <span class="text-dark-75 font-weight-bolder font-size-sm">73 Invoice</span>
         <a href="#" class="text-primary font-weight-bolder">View</a>
      </div>
   </div>
</div>
</div>
</div>
</div>
<?php } ?>