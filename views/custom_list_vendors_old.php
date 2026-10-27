<?php
$cusurl = API."?mode=gettabledetails&table=".TBL_VENDOR; 
$cusresponse = callAPI($cusurl); //file_get_contents($cusurl, false, $context);	
$cusresponse = json_decode($cusresponse,1);
$title = viewTitle($_GET['option']);
$cusummurl = API."?mode=gettabledetails&table=".TBL_VENDOR_SUMMARY; 
$cusummresponse = callAPI($cusummurl);//file_get_contents($cusummurl, false, $context);	
$cusummresponse = json_decode($cusummresponse,1);
foreach ($cusummresponse as $summ) {
	$array[$summ['vendor_id']]=$summ;
}
$url = API."?mode=singlerowtabledata&fid=application_table_name&rid=".TBL_VENDOR_SUMMARY."&table=".TBL_DISPLAYATTRIBUTES;
//echo $url;
$response = callAPI($url);//file_get_contents($url, false, $context);
$header = json_decode($response,1);
$headers = json_decode($header['list_attributes'],1);
//print_r($headers); exit;
 ?>
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

						<!-- begin:: Content Head -->
						<div class="kt-subheader   kt-grid__item" id="kt_subheader">
							<div class="kt-container  kt-container--fluid ">
								<div class="kt-subheader__main">
									<h3 class="kt-subheader__title">
										<?php echo $title; ?>
									</h3>
									<span class="kt-subheader__separator kt-subheader__separator--v"></span>
									<div class="kt-subheader__group" id="kt_subheader_search">
										<span class="kt-subheader__desc" id="kt_subheader_total">
											<?php echo count((array)$cusresponse); ?> Total </span>
										<form class="kt-margin-l-20" id="kt_subheader_search_form">
											<div class="kt-input-icon kt-input-icon--right kt-subheader__search">
												<input type="text" class="form-control" placeholder="Search..." id="generalSearch">
												<span class="kt-input-icon__icon kt-input-icon__icon--right">
													<span>
														<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
															<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																<rect x="0" y="0" width="24" height="24" />
																<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
																<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
															</g>
														</svg>

														<!--<i class="flaticon2-search-1"></i>-->
													</span>
												</span>
											</div>
										</form>
									</div>
									<div class="kt-subheader__group kt-hidden" id="kt_subheader_group_actions">
										<div class="kt-subheader__desc"><span id="kt_subheader_group_selected_rows"></span> Selected:</div>
										<div class="btn-toolbar kt-margin-l-20">
											<div class="dropdown" id="kt_subheader_group_actions_status_change">
												<button type="button" class="btn btn-label-brand btn-bold btn-sm dropdown-toggle" data-toggle="dropdown">
													Update Status
												</button>
												<div class="dropdown-menu">
													<ul class="kt-nav">
														<li class="kt-nav__section kt-nav__section--first">
															<span class="kt-nav__section-text">Change status to:</span>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link" data-toggle="status-change" data-status="1">
																<span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-success kt-badge--inline kt-badge--bold">Approved</span></span>
															</a>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link" data-toggle="status-change" data-status="2">
																<span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-danger kt-badge--inline kt-badge--bold">Rejected</span></span>
															</a>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link" data-toggle="status-change" data-status="3">
																<span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-warning kt-badge--inline kt-badge--bold">Pending</span></span>
															</a>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link" data-toggle="status-change" data-status="4">
																<span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-info kt-badge--inline kt-badge--bold">On Hold</span></span>
															</a>
														</li>
													</ul>
												</div>
											</div>
											<button class="btn btn-label-success btn-bold btn-sm btn-icon-h" id="kt_subheader_group_actions_fetch" data-toggle="modal" data-target="#kt_datatable_records_fetch_modal">
												Fetch Selected
											</button>
											<button class="btn btn-label-danger btn-bold btn-sm btn-icon-h" id="kt_subheader_group_actions_delete_all">
												Delete All
											</button>
										</div>
									</div>
								</div>
								<div class="kt-subheader__toolbar">
									<a href="#" class="">
									</a>
									<a href="index.php?option=COM_INSERT_VENDORS" class="btn btn-label-brand btn-bold">
										Add User </a>
									<div class="kt-subheader__wrapper">
										<div class="dropdown dropdown-inline" data-toggle="kt-tooltip-" title="Quick actions" data-placement="left">
											<a href="#" class="btn btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--success kt-svg-icon--md">
													<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<polygon points="0 0 24 0 24 24 0 24" />
														<path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
														<path d="M11,14 L9,14 C8.44771525,14 8,13.5522847 8,13 C8,12.4477153 8.44771525,12 9,12 L11,12 L11,10 C11,9.44771525 11.4477153,9 12,9 C12.5522847,9 13,9.44771525 13,10 L13,12 L15,12 C15.5522847,12 16,12.4477153 16,13 C16,13.5522847 15.5522847,14 15,14 L13,14 L13,16 C13,16.5522847 12.5522847,17 12,17 C11.4477153,17 11,16.5522847 11,16 L11,14 Z" fill="#000000" />
													</g>
												</svg>

												<!--<i class="flaticon2-plus"></i>-->
											</a>
											<div class="dropdown-menu dropdown-menu-fit dropdown-menu-md dropdown-menu-right">

												<!--begin::Nav-->
												<ul class="kt-nav">
													<li class="kt-nav__head">
														Add New:
														<i class="flaticon2-information" data-toggle="kt-tooltip" data-placement="right" title="Click to learn more..."></i>
													</li>
													<li class="kt-nav__separator"></li>
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link">
															<i class="kt-nav__link-icon flaticon2-drop"></i>
															<span class="kt-nav__link-text">Orders</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link">
															<i class="kt-nav__link-icon flaticon2-new-email"></i>
															<span class="kt-nav__link-text">Members</span>
															<span class="kt-nav__link-badge">
																<span class="kt-badge kt-badge--brand kt-badge--rounded">15</span>
															</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link">
															<i class="kt-nav__link-icon flaticon2-calendar-8"></i>
															<span class="kt-nav__link-text">Reports</span>
														</a>
													</li>
													<li class="kt-nav__item">
														<a href="#" class="kt-nav__link">
															<i class="kt-nav__link-icon flaticon2-link"></i>
															<span class="kt-nav__link-text">Finance</span>
														</a>
													</li>
													<li class="kt-nav__separator"></li>
													<li class="kt-nav__foot">
														<a class="btn btn-label-brand btn-bold btn-sm" href="#">More options</a>
														<a class="btn btn-clean btn-bold btn-sm kt-hidden" href="#" data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">Learn more</a>
													</li>
												</ul>

												<!--end::Nav-->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- end:: Content Head -->
<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
						<!--begin:: Portlet-->
						<?php foreach ($cusresponse as $key => $value) { 
							  $total_sales = $array[$value['id']]['total_sales'] !='' ? $array[$value['id']]['total_sales'] : '0.00';
							  $yearly_sales = $array[$value['id']]['yearly_sales'] !='' ? $array[$value['id']]['yearly_sales'] : '0.00';
							  $quarterly_sales = $array[$value['id']]['quarterly_sales'] !='' ? $array[$value['id']]['quarterly_sales'] : '0.00';
							  $monthly_sales = $array[$value['id']]['monthly_sales'] !='' ? $array[$value['id']]['monthly_sales'] : '0.00';
							  $total_payments = $array[$value['id']]['total_payments'] !='' ? $array[$value['id']]['total_payments'] : '0.00';
							  $yearly_payments = $array[$value['id']]['yearly_payments'] !='' ? $array[$value['id']]['yearly_payments'] : '0.00';
							  $quarterly_payments = $array[$value['id']]['quarterly_payments'] !='' ? $array[$value['id']]['quarterly_payments'] : '0.00';
							  $monthly_payments = $array[$value['id']]['monthly_payments'] !='' ? $array[$value['id']]['monthly_payments'] : '0.00';
							  $due_30 = $array[$value['id']]['due_30'] !='' ? $array[$value['id']]['due_30'] : '0';
							  $due_45 = $array[$value['id']]['due_45'] !='' ? $array[$value['id']]['due_45'] : '0';
							  $due_90 = $array[$value['id']]['due_90'] !='' ? $array[$value['id']]['due_90'] : '0';
							  $due_120 = $array[$value['id']]['due_120'] !='' ? $array[$value['id']]['due_120'] : '0';
							  $percentage = ($total_sales!=0 AND $total_sales!="") ? round((($total_sales-$total_payments)/$total_sales)*100) : '0';
							 //print_r($value);
							   $rid = deocdeId($value['id']);?>
							<div class="kt-portlet">
								<div class="kt-portlet__body">
									<div class="kt-widget kt-widget--user-profile-3">
										<div class="kt-widget__top">
											<?php if($value['image']!=''){ ?>
											<div class="kt-widget__media kt-hidden-">
												<img src="uploads/customer/<?php echo $value['image'] ; ?>" alt="image" width="100px" height="100px">
											</div>
										<?php }else{ ?> 
											<div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-boldest kt-font-light kt-hidden-">
												<?php echo initials($value['display_name']); ?>
											</div>
										<?php } ?>
											<div class="kt-widget__content">
												<div class="kt-widget__head">
													<a href="#" class="kt-widget__username">
														<?php echo $value['display_name']; ?>
													</a>
													<div class="kt-widget__action">
														<a href="index.php?option=COM_INSERT_VENDORS&Rid=<?= $rid ?>"class="kt-widget__username">
														<button type="button" class="btn  btn-brand btn-sm btn-upper">Create Invoice</button>&nbsp;
														</a>
														<a href="index.php?option=COM_INSERT_VENDORS&Rid=<?= $rid ?>"class="kt-widget__username">
														 <button type="button" class="btn btn-brand btn-sm btn-upper" style="background-color: #FFB822;border-color: #FFB822; ">edit profile</button>
														</a>
													</div>
												</div>
												<div class="kt-widget__subhead">
													<a href="#"><i class="flaticon2-new-email"></i><?php echo $value['email']; ?></a>
													<a href="#"><i class="flaticon2-phone"></i><?php echo $value['phone']; ?></a> 
													<a href="#"><i class="flaticon2-menu-4"></i><?php echo $value['company']; ?></a>
												</div>
												<div class="kt-widget__info">
													<div class="kt-widget__desc">
														Address : <?php echo $value['billing_address']; ?>
														<br>  Website : <?php echo $value['website_url']; ?>
													</div>
													<div class="kt-widget__progress">
														<div class="kt-widget__text">
															Due Progress
														</div>
														<div class="progress" style="height: 5px;width: 100%;">
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
															<div class="progress-bar" role="progressbar" <?php echo $spp; ?> aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<div class="kt-widget__stats">
															<?php echo $percentage."%"; ?>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="kt-widget__bottom">
											<div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-piggy-bank"></i>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['total_sales']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($total_sales); ?></span>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['yearly_sales']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($yearly_sales); ?></span>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['quarterly_sales']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($quarterly_sales); ?></span>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['monthly_sales']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($monthly_sales); ?></span>
												</div>
										</div>
											
											<div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-confetti"></i>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title">Payment</span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($total_payments); ?></span>
												</div>
											</div>
											<div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-pie-chart"></i>

												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> Due</span>
													<span class="kt-widget__value"></span>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['due_30']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($due_30); ?></span>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['due_45']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($due_45); ?></span>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['due_90']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($due_90); ?></span>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title"> <?php echo $headers['due_120']['lable']; ?></span>
													<span class="kt-widget__value"><?php echo customeummarmonyformate($due_120); ?></span>
												</div>

											</div>
											<div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-file-2"></i>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title">45 Tasks</span>
													<a href="#" class="kt-widget__value kt-font-brand">View</a>
												</div>
											</div>
											<!-- <div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-chat-1"></i>
												</div>
												<div class="kt-widget__details">
													<span class="kt-widget__title">968 Comments</span>
													<a href="#" class="kt-widget__value kt-font-brand">View</a>
												</div>
											</div> -->
											<!-- <div class="kt-widget__item">
												<div class="kt-widget__icon">
													<i class="flaticon-network"></i>
												</div>
												<div class="kt-widget__details">
													<div class="kt-section__content kt-section__content--solid">
														<div class="kt-media-group">
															<a href="#" class="kt-media kt-media--sm kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="John Myer">
																<img src="assets/media/users/100_1.jpg" alt="image">
															</a>
															<a href="#" class="kt-media kt-media--sm kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="Alison Brandy">
																<img src="assets/media/users/100_10.jpg" alt="image">
															</a>
															<a href="#" class="kt-media kt-media--sm kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="Selina Cranson">
																<img src="assets/media/users/100_11.jpg" alt="image">
															</a>
															<a href="#" class="kt-media kt-media--sm kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="Luke Walls">
																<img src="assets/media/users/100_2.jpg" alt="image">
															</a>
															<a href="#" class="kt-media kt-media--sm kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="Micheal York">
																<img src="assets/media/users/100_3.jpg" alt="image">
															</a>
															<a href="#" class="kt-media kt-media--sm kt-media--circle" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="Micheal York">
																<span>+3</span>
															</a>
														</div>
													</div>
												</div>
											</div> -->
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
							
							<!--Begin::Pagination-->
							<div class="row">
								<div class="col-xl-12">

									<!--begin:: Components/Pagination/Default-->
									<!-- <div class="kt-portlet">
										<div class="kt-portlet__body">

											
											<div class="kt-pagination kt-pagination--brand">
												<ul class="kt-pagination__links">
													<li class="kt-pagination__link--first">
														<a href="#"><i class="fa fa-angle-double-left kt-font-brand"></i></a>
													</li>
													<li class="kt-pagination__link--next">
														<a href="#"><i class="fa fa-angle-left kt-font-brand"></i></a>
													</li>
													<li>
														<a href="#">...</a>
													</li>
													<li>
														<a href="#">29</a>
													</li>
													<li>
														<a href="#">30</a>
													</li>
													<li class="kt-pagination__link--active">
														<a href="#">31</a>
													</li>
													<li>
														<a href="#">32</a>
													</li>
													<li>
														<a href="#">33</a>
													</li>
													<li>
														<a href="#">34</a>
													</li>
													<li>
														<a href="#">...</a>
													</li>
													<li class="kt-pagination__link--prev">
														<a href="#"><i class="fa fa-angle-right kt-font-brand"></i></a>
													</li>
													<li class="kt-pagination__link--last">
														<a href="#"><i class="fa fa-angle-double-right kt-font-brand"></i></a>
													</li>
												</ul>
												<div class="kt-pagination__toolbar">
													<select class="form-control kt-font-brand" style="width: 60px">
														<option value="10">10</option>
														<option value="20">20</option>
														<option value="30">30</option>
														<option value="50">50</option>
														<option value="100">100</option>
													</select>
													<span class="pagination__desc">
														Displaying 10 of 230 records
													</span>
												</div>
											</div>

											
										</div>
									</div> -->

									<!--end:: Components/Pagination/Default-->
								</div>
							</div>

							<!--End::Pagination-->
						</div>

						<!-- end:: Content -->
					</div>

					</div>
				</div>		