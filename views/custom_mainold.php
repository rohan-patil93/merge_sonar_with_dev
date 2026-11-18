					<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content" style="margin-top: 20px;">
						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

							<!--Begin::Dashboard 6-->

							<!--begin:: Widgets/Stats-->
							<div class="kt-portlet">
								<div class="kt-portlet__body  kt-portlet__body--fit">
									<div class="row row-no-padding row-col-separator-lg">
										<div class="col-md-12 col-lg-6 col-xl-3">

											<!--begin::Total Profit-->
											<div class="kt-widget24">
												<div class="kt-widget24__details">
													<div class="kt-widget24__info">
														<h4 class="kt-widget24__title">
															<?= __('Total Income'); ?>
														</h4>
														<span class="kt-widget24__desc">
															<?= __('Total Acounts Receivable'); ?>
														</span>
													</div>
													<span class="kt-widget24__stats kt-font-brand">
														<?= __('₹ 18 Lac'); ?>
													</span>
												</div>
												<div class="progress progress--sm">
													<div class="progress-bar kt-bg-brand" role="progressbar" style="width: 78%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
												</div>
												<div class="kt-widget24__action">
													<span class="kt-widget24__change">
														<?= __('Due'); ?>
													</span>
													<span class="kt-widget24__number">
														78%
													</span>
												</div>
											</div>

											<!--end::Total Profit-->
										</div>
										<div class="col-md-12 col-lg-6 col-xl-3">

											<!--begin::New Feedbacks-->
											<div class="kt-widget24">
												<div class="kt-widget24__details">
													<div class="kt-widget24__info">
														<h4 class="kt-widget24__title">
															<?= __('Total Expenses'); ?>
														</h4>
														<span class="kt-widget24__desc">
															<?= __('Total Accounts Payable'); ?>
														</span>
													</div>
													<span class="kt-widget24__stats kt-font-warning">
														<?= __('₹ 13 Lac'); ?>
													</span>
												</div>
												<div class="progress progress--sm">
													<div class="progress-bar kt-bg-warning" role="progressbar" style="width: 84%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
												</div>
												<div class="kt-widget24__action">
													<span class="kt-widget24__change">
														<?= __('Due'); ?>
													</span>
													<span class="kt-widget24__number">
														84%
													</span>
												</div>
											</div>

											<!--end::New Feedbacks-->
										</div>
										<div class="col-md-12 col-lg-6 col-xl-3">

											<!--begin::New Feedbacks-->
											<div class="kt-widget1">
												<div class="kt-widget1__item">
													<div class="kt-widget1__info">
														<h3 class="kt-widget1__title"><?= __('Equity Ratio'); ?> </h3>
													</div>
													<span class="kt-widget1__number kt-font-brand">+$17,800</span>
												</div>
												<div class="kt-widget1__item">
													<div class="kt-widget1__info">
														<h3 class="kt-widget1__title"><?= __('Debt Equity'); ?></h3>
													</div>
													<span class="kt-widget1__number kt-font-danger">+1,800</span>
												</div>
												<div class="kt-widget1__item">
													<div class="kt-widget1__info">
														<h3 class="kt-widget1__title"><?= __('Current Ratio'); ?></h3>
													</div>
													<span class="kt-widget1__number kt-font-success">-27,49%</span>
												</div>
											</div>

											<!--end::New Feedbacks-->
										</div>
										<div class="col-md-12 col-lg-6 col-xl-3">

											<!--begin::New Feedbacks-->
											<div class="kt-widget24">
												<div class="kt-widget24__details" style="margin-top: 40px;">
													<div class="kt-widget24__info">
														<h4 class="kt-widget24__title">
															<?= __('Current Ratio'); ?>
														</h4>
														<span class="kt-widget24__desc">

														</span>
													</div>
													<span class="kt-widget24__stats kt-font-success">
														33%
													</span>
												</div>

											</div>

											<!--end::New Feedbacks-->
										</div>
									</div>
								</div>
							</div>

							<!--end:: Widgets/Stats-->
							<!--Begin::Row-->
								<div class="row">
									<div class="col-xl-4 col-lg-4">

										<!--begin:: Widgets/Daily Sales-->
										<div class="kt-portlet kt-portlet--height-fluid">
											<div class="kt-widget14">
												<div class="kt-widget14__header kt-margin-b-30">
													<h3 class="kt-widget14__title">
														<?= __('Daily Sales'); ?>
													</h3>
													<span class="kt-widget14__desc">
														<?= __('Check out each collumn for more details'); ?>
													</span>
												</div>
												<div class="kt-widget14__chart" style="height:120px;">
													<canvas id="kt_chart_daily_sales"></canvas>
												</div>
											</div>
										</div>

										<!--end:: Widgets/Daily Sales-->
									</div>
									<div class="col-xl-4 col-lg-4">

										<!--begin:: Widgets/Profit Share-->
										<div class="kt-portlet kt-portlet--height-fluid">
											<div class="kt-widget14">
												<div class="kt-widget14__header">
													<h3 class="kt-widget14__title">
														<?= __('Profit Share'); ?>
													</h3>
													<span class="kt-widget14__desc">
														<?= __('Profit Share between customers'); ?>
													</span>
												</div>
												<div class="kt-widget14__content">
													<div class="kt-widget14__chart">
														<div class="kt-widget14__stat">45</div>
														<canvas id="kt_chart_profit_share" style="height: 140px; width: 140px;"></canvas>
													</div>
													<div class="kt-widget14__legends">
														<div class="kt-widget14__legend">
															<span class="kt-widget14__bullet kt-bg-success"></span>
															<span class="kt-widget14__stats">37% Sport Tickets</span>
														</div>
														<div class="kt-widget14__legend">
															<span class="kt-widget14__bullet kt-bg-warning"></span>
															<span class="kt-widget14__stats">47% Business Events</span>
														</div>
														<div class="kt-widget14__legend">
															<span class="kt-widget14__bullet kt-bg-brand"></span>
															<span class="kt-widget14__stats">19% Others</span>
														</div>
													</div>
												</div>
											</div>
										</div>

										<!--end:: Widgets/Profit Share-->
									</div>
									<div class="col-xl-4 col-lg-4">

										<!--begin:: Widgets/Revenue Change-->
										<div class="kt-portlet kt-portlet--height-fluid">
											<div class="kt-widget14">
												<div class="kt-widget14__header">
													<h3 class="kt-widget14__title">
														<?= __('Revenue Change'); ?>
													</h3>
													<span class="kt-widget14__desc">
														<?= __('Revenue change breakdown by cities'); ?>
													</span>
												</div>
												<div class="kt-widget14__content">
													<div class="kt-widget14__chart">
														<div id="kt_chart_revenue_change" style="height: 150px; width: 150px;"></div>
													</div>
													<div class="kt-widget14__legends">
														<div class="kt-widget14__legend">
															<span class="kt-widget14__bullet kt-bg-success"></span>
															<span class="kt-widget14__stats">+10% New York</span>
														</div>
														<div class="kt-widget14__legend">
															<span class="kt-widget14__bullet kt-bg-warning"></span>
															<span class="kt-widget14__stats">-7% London</span>
														</div>
														<div class="kt-widget14__legend">
															<span class="kt-widget14__bullet kt-bg-brand"></span>
															<span class="kt-widget14__stats">+20% California</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<!--Begin::Row-->
							<div class="row">
								<div class="col-xl-6">

									<!--begin:: Widgets/Order Statistics-->
									<div class="kt-portlet kt-portlet--height-fluid">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													<?= __('Current Month'); ?>
												</h3>
											</div>
											<div class="kt-portlet__head-toolbar">
												<a href="#" class="btn btn-label-brand btn-bold btn-sm dropdown-toggle" data-toggle="dropdown">
													<?= __('Sales Reports'); ?>
												</a>
												<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">

													<!--begin::Nav-->
													<ul class="kt-nav">
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link">
																<i class="kt-nav__link-icon flaticon2-drop"></i>
																<span class="kt-nav__link-text"><?= __('Current Month'); ?></span>
															</a>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link">
																<i class="kt-nav__link-icon flaticon2-calendar-8"></i>
																<span class="kt-nav__link-text"><?= __('Last Month'); ?></span>
															</a>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link">
																<i class="kt-nav__link-icon flaticon2-telegram-logo"></i>
																<span class="kt-nav__link-text"><?= __('Quarterly'); ?></span>
															</a>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link">
																<i class="kt-nav__link-icon flaticon2-calendar-8"></i>
																<span class="kt-nav__link-text"><?= __('Yearly'); ?></span>
															</a>
														</li>
														<li class="kt-nav__item">
															<a href="#" class="kt-nav__link">
																<i class="kt-nav__link-icon flaticon2-calendar-8"></i>
																<span class="kt-nav__link-text"><?= __('All Time'); ?></span>
															</a>
														</li>

													</ul>

													<!--end::Nav-->
												</div>
											</div>
										</div>

										<div class="kt-portlet__body">

											<!--Begin::Tab Content-->
											<div class="tab-content">

												<!--begin::tab 1 content-->
												<div class="tab-pane active" id="kt_widget11_tab1_content">

													<!--begin::Widget 11-->
														<div class="kt-widget11">
														<div class="table-responsive">
															<table class="table">
																<thead>
																	<tr>
																		<th scope="col" style="width:1%">#</th>
																		<th scope="col" style="width:40%"><?= __('Customer'); ?></th>
																		<th scope="col" style="width:14%"><?= __('Amount'); ?>		 </th>
																		<th scope="col" style="width:14%"><?= __('Paid'); ?> </th>
																		<th scope="col" style="width:15%"><?= __('Due'); ?> </th>
																		<th scope="col" style="width:15%"><?= __('Status'); ?></th>
																		<th scope="col" style="width:15%" class="kt-align-right"><?= __('Total'); ?> </th>
																	</tr>
																</thead>
																<tbody>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single">
																				<input type="checkbox"><span></span>
																			</label>
																		</td>
																		<td>
																			<a href="#" class="kt-widget11__title"><?= __('Loop'); ?></a>
																			<span class="kt-widget11__sub"><?= __('CRM System'); ?></span>
																		</td>
																		<td>19,200</td>
																		<td>19,200</td>
																		<td>$63</td>
																		<td>
																			<div class="progress progress--sm">
																					<div class="progress-bar kt-bg-info" role="progressbar" style="width: 84%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
																			</div>
																		</td>
																		<td class="kt-align-right kt-font-brand kt-font-bold">$34,740</td>
																	</tr>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single"><input type="checkbox"><span></span></label>
																		</td>
																		<td>
																			<a href="#" class="kt-widget11__title"><?= __('Selto'); ?></a>
																			<span class="kt-widget11__sub"><?= __('Powerful Website Builder'); ?></span>
																		</td>
																		<td>24,310</td>
																		<td>24,310</td>
																		<td>$39</td>
																		<td>
																			<div class="progress progress--sm">
																					<div class="progress-bar kt-bg-info" role="progressbar" style="width: 48%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
																			</div>
																		</td>
																		<td class="kt-align-right kt-font-brand kt-font-bold">$46,010</td>
																	</tr>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single"><input type="checkbox"><span></span></label>
																		</td>
																		<td>
																			<a href="#" class="kt-widget11__title"><?= __('Jippo'); ?></a>
																			<span class="kt-widget11__sub"><?= __('The Best Selling App'); ?></span>
																		</td>
																		<td>9,076</td>
																		<td>24,310</td>
																		<td>$105</td>
																		<td>
																			<div class="progress progress--sm">
																					<div class="progress-bar kt-bg-info" role="progressbar" style="width: 36%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
																			</div>
																		</td>
																		<td class="kt-align-right kt-font-brand kt-font-bold">$67,800</td>
																	</tr>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single"><input type="checkbox"><span></span></label>
																		</td>
																		<td>
																			<a href="#" class="kt-widget11__title"><?= __('Verto'); ?></a>
																			<span class="kt-widget11__sub"><?= __('Web Development Tool'); ?></span>
																		</td>
																		<td>11,094</td>
																		<td>24,310</td>
																		<td>$16</td>
																		<td>
																			<div class="progress progress--sm">
																					<div class="progress-bar kt-bg-info" role="progressbar" style="width: 66%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
																			</div>
																		</td>
																		<td class="kt-align-right kt-font-brand kt-font-bold">$18,520</td>
																	</tr>
																</tbody>
															</table>
														</div>
														<div class="kt-widget11__action kt-align-right">
															<button type="button" class="btn btn-label-brand btn-bold btn-sm"><?= __('Import Report'); ?></button>
														</div>
													</div>
													<!--end::Widget 11-->
												</div>

												<!--end::tab 1 content-->

												<!--begin::tab 2 content-->
												<div class="tab-pane" id="kt_widget11_tab2_content">

													<!--begin::Widget 11-->
													<div class="kt-widget11">
														<div class="table-responsive">
															<table class="table">
																<thead>
																	<tr>
																		<th scope="col" style="width:1%">#</th>
																		<th scope="col" style="width:40%"><?= __('Application'); ?></th>
																		<th scope="col" style="width:14%"><?= __('Sales'); ?></th>
																		<th scope="col" style="width:15%"><?= __('Change'); ?></th>
																		<th scope="col" style="width:15%"><?= __('Status'); ?></th>
																		<th scope="col" style="width:15%" class="kt-align-right"><?= __('Total'); ?></th>
																	</tr>
																</thead>
																<tbody>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single">
																				<input type="checkbox"><span></span>
																			</label>
																		</td>
																		<td>
																			<span class="kt-widget11__title"><?= __('Loop'); ?></span>
																			<span class="kt-widget11__sub"><?= __('CRM System'); ?></span>
																		</td>
																		<td>19,200</td>
																		<td>$63</td>
																		<td><span class="kt-badge kt-badge--inline kt-badge--danger"><?= __('pending'); ?></span></td>
																		<td class="kt-align-right kt-font-brand  kt-font-bold">$23,740</td>
																	</tr>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single"><input type="checkbox"><span></span></label>
																		</td>
																		<td>
																			<span class="kt-widget11__title"><?= __('Selto'); ?></span>
																			<span class="kt-widget11__sub"><?= __('Powerful Website Builder'); ?></span>
																		</td>
																		<td>24,310</td>
																		<td>$39</td>
																		<td><span class="kt-badge kt-badge--inline kt-badge--success"><?= __('new'); ?></span></td>
																		<td class="kt-align-right kt-font-success  kt-font-bold">$46,010</td>
																	</tr>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single"><input type="checkbox"><span></span></label>
																		</td>
																		<td>
																			<span class="kt-widget11__title"><?= __('Jippo'); ?></span>
																			<span class="kt-widget11__sub"><?= __('The Best Selling App'); ?></span>
																		</td>
																		<td>9,076</td>
																		<td>$105</td>
																		<td><span class="kt-badge kt-badge--inline kt-badge--brand"><?= __('approved'); ?></span></td>
																		<td class="kt-align-right kt-font-danger kt-font-bold">$15,800</td>
																	</tr>
																	<tr>
																		<td>
																			<label aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>" class="kt-checkbox kt-checkbox--single"><input type="checkbox"><span></span></label>
																		</td>
																		<td>
																			<span class="kt-widget11__title"><?= __('Verto'); ?></span>
																			<span class="kt-widget11__sub"><?= __('Web Development Tool'); ?></span>
																		</td>
																		<td>11,094</td>
																		<td>$16</td>
																		<td><span class="kt-badge kt-badge--inline kt-badge--info"><?= __('done'); ?></span></td>
																		<td class="kt-align-right kt-font-warning kt-font-bold">$8,520</td>
																	</tr>
																</tbody>
															</table>
														</div>
														<div class="kt-widget11__action kt-align-right">
															<button type="button" class="btn btn-label-success btn-bold btn-sm"><?= __('Generate Report1'); ?></button>
														</div>
													</div>

													<!--end::Widget 11-->
												</div>

												<!--end::tab 2 content-->

												<!--begin::tab 3 content-->
												<div class="tab-pane" id="kt_widget11_tab3_content">
												</div>

												<!--end::tab 3 content-->
											</div>

											<!--End::Tab Content-->
										</div>
									</div>

									<!--end:: Widgets/Sale Reports-->
								</div>
								<div class="col-xl-6">

									<!--begin:: Widgets/Sale Reports-->
									<div class="kt-portlet kt-portlet--tabs kt-portlet--height-fluid">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													<?= __('MIS'); ?>
												</h3>

											</div>
											<div class="kt-portlet__head-toolbar">
												<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-brand">
													<li class="nav-item">
														<a class="nav-link active" data-toggle="tab" href="#kt_widget11_tab1_content" role="tab">
															<?= __('YTD'); ?>
														</a>
													</li>

													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
															<?= __('QTD'); ?>
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
															<?= __('MTD'); ?>
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="kt-portlet__body kt-portlet__body--fluid">
											<div class="kt-widget12">
												<div class="kt-widget12__content">
													<div class="kt-widget12__item">
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"> <?= __('Taxes'); ?></span>
															<span class="kt-widget12__value">$400,000</span>
														</div>
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"><?= __('Finance Review Date'); ?></span>
															<span class="kt-widget12__value"><?= __('July 24,2019'); ?></span>
														</div>
													</div>
													<div class="kt-widget12__item">
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"> <?= __('Revenue'); ?></span>
															<span class="kt-widget12__value">$60M</span>
														</div>
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"><?= __('Revenue Margin'); ?></span>
															<div class="kt-widget12__progress">
																<div class="progress kt-progress--sm">
																	<div class="progress-bar kt-bg-brand" role="progressbar" style="width: 40%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
																</div>
																<span class="kt-widget12__stat">
																	40%
																</span>
															</div>
														</div>
													</div>
												</div>
												<div class="kt-widget12__chart" style="height:250px;">
													<canvas id="kt_chart_order_statistics"></canvas>
												</div>
											</div>
										</div>
									</div>

									<!--end:: Widgets/Order Statistics-->
								</div>
							</div>
							<!--- END Row  -->
							<!--- Begin Row-->


							<!--End::Row-->
							<div class="row">

								<div class="col-xl-12">

									<!--begin:: Widgets/Sale Reports-->
									<div class="kt-portlet kt-portlet--tabs kt-portlet--height-fluid">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													<?= __('Cash'); ?>
												</h3>

											</div>
											<div class="kt-portlet__head-toolbar">
												<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-brand">
													<li class="nav-item">
														<a class="nav-link active" data-toggle="tab" href="#kt_widget11_tab1_content" role="tab">
															<?= __('YTD'); ?>
														</a>
													</li>

													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
															<?= __('QTD'); ?>
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
															<?= __('MTD'); ?>
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="kt-portlet__body kt-portlet__body--fluid">
											<div class="kt-widget12">
												<div class="kt-widget12__content">
													<div class="kt-widget12__item">
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"> <?= __('Taxes'); ?></span>
															<span class="kt-widget12__value">$400,000</span>
														</div>
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"><?= __('Finance Review Date'); ?></span>
															<span class="kt-widget12__value"><?= __('July 24,2019'); ?></span>
														</div>
													</div>
													<div class="kt-widget12__item">
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"> <?= __('Revenue'); ?></span>
															<span class="kt-widget12__value">$60M</span>
														</div>
														<div class="kt-widget12__info">
															<span class="kt-widget12__desc"><?= __('Revenue Margin'); ?></span>
															<div class="kt-widget12__progress">
																<div class="progress kt-progress--sm">
																	<div class="progress-bar kt-bg-brand" role="progressbar" style="width: 40%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
																</div>
																<span class="kt-widget12__stat">
																	40%
																</span>
															</div>
														</div>
													</div>
												</div>
												 <div class="kt-portlet__body">
													<div id="kt_amcharts_3" style="height: 250px;"></div>
												</div>
											</div>
										</div>
									</div>

									<!--end:: Widgets/Order Statistics-->
								</div>
							</div>

							<!--Begin::Row-->


							<!--End::Row-->

							<!--Begin::Row-->
							<div class="row">



								<div class="col-xl-6 col-lg-6 order-lg-1 order-xl-1">

									<!--Begin::Portlet-->
									<div class="kt-portlet kt-portlet--height-fluid">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													<?= __('Recent Activities'); ?>
												</h3>
											</div>
											<div class="kt-portlet__head-toolbar">
												<div class="dropdown dropdown-inline">
													<button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="flaticon-more-1"></i>
													</button>
													<div class="dropdown-menu dropdown-menu-right dropdown-menu-fit dropdown-menu-md">

														<!--begin::Nav-->
														<ul class="kt-nav">
															<li class="kt-nav__head">
																<?= __('Export Options'); ?>
																<span data-toggle="kt-tooltip" data-placement="right" title="<?= htmlspecialchars(__('Click to learn more...'), ENT_QUOTES, 'UTF-8'); ?>">
																	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--brand kt-svg-icon--md1">
																		<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
																			<rect x="0" y="0" width="24" height="24" />
																			<circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
																			<rect fill="#000000" x="11" y="10" width="2" height="7" rx="1" />
																			<rect fill="#000000" x="11" y="7" width="2" height="2" rx="1" />
																		</g>
																	</svg> </span>
															</li>
															<li class="kt-nav__separator"></li>
															<li class="kt-nav__item">
																<a href="#" class="kt-nav__link">
																	<i class="kt-nav__link-icon flaticon2-drop"></i>
																	<span class="kt-nav__link-text"><?= __('Activity'); ?></span>
																</a>
															</li>
															<li class="kt-nav__item">
																<a href="#" class="kt-nav__link">
																	<i class="kt-nav__link-icon flaticon2-calendar-8"></i>
																	<span class="kt-nav__link-text"><?= __('FAQ'); ?></span>
																</a>
															</li>
															<li class="kt-nav__item">
																<a href="#" class="kt-nav__link">
																	<i class="kt-nav__link-icon flaticon2-telegram-logo"></i>
																	<span class="kt-nav__link-text"><?= __('Settings'); ?></span>
																</a>
															</li>
															<li class="kt-nav__item">
																<a href="#" class="kt-nav__link">
																	<i class="kt-nav__link-icon flaticon2-new-email"></i>
																	<span class="kt-nav__link-text"><?= __('Support'); ?></span>
																	<span class="kt-nav__link-badge">
																		<span class="kt-badge kt-badge--success kt-badge--rounded">5</span>
																	</span>
																</a>
															</li>
															<li class="kt-nav__separator"></li>
															<li class="kt-nav__foot">
																<a class="btn btn-label-danger btn-bold btn-sm" href="#"><?= __('Upgrade plan'); ?></a>
																<a class="btn btn-clean btn-bold btn-sm" href="#" data-toggle="kt-tooltip" data-placement="right" title="<?= htmlspecialchars(__('Click to learn more...'), ENT_QUOTES, 'UTF-8'); ?>"><?= __('Learn more'); ?></a>
															</li>
														</ul>

														<!--end::Nav-->
													</div>
												</div>
											</div>
										</div>
										<div class="kt-portlet__body">

											<!--Begin::Timeline 3 -->
											<div class="kt-timeline-v2">
												<div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">10:00</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-danger"></i>
														</div>
														<div class="kt-timeline-v2__item-text  kt-padding-top-5">
															<?= __('Lorem ipsum dolor sit amit,consectetur eiusmdd tempor'); ?><br>
															<?= __('incididunt ut labore et dolore magna'); ?>
														</div>
													</div>
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">12:45</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-success"></i>
														</div>
														<div class="kt-timeline-v2__item-text kt-timeline-v2__item-text--bold">
															<?= __('AEOL Meeting With'); ?>
														</div>
														<div class="kt-list-pics kt-list-pics--sm kt-padding-l-20">
															<a href="#"><img src="assets/media/users/100_4.jpg" title="" alt="" ></a>
															<a href="#"><img src="assets/media/users/100_13.jpg" title="" alt="" ></a>
															<a href="#"><img src="assets/media/users/100_11.jpg" title="" alt="" ></a>
															<a href="#"><img src="assets/media/users/100_14.jpg" title="" alt="" ></a>
														</div>
													</div>
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">14:00</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-brand"></i>
														</div>
														<div class="kt-timeline-v2__item-text kt-padding-top-5">
															<?= __('Make Deposit'); ?> <a href="#" class="kt-link kt-link--brand kt-font-bolder"><?= __('USD 700'); ?></a> <?= __('To ESL.'); ?>
														</div>
													</div>
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">16:00</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-warning"></i>
														</div>
														<div class="kt-timeline-v2__item-text kt-padding-top-5">
															<?= __('Lorem ipsum dolor sit amit,consectetur eiusmdd tempor'); ?><br>
															<?= __('incididunt ut labore et dolore magna elit enim at minim'); ?><br>
															<?= __('veniam quis nostrud'); ?>
														</div>
													</div>
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">17:00</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-info"></i>
														</div>
														<div class="kt-timeline-v2__item-text kt-padding-top-5">
															<?= __('Placed a new order in'); ?> <a href="#" class="kt-link kt-link--brand kt-font-bolder"><?= __('SIGNATURE MOBILE'); ?></a> <?= __('marketplace.'); ?>
														</div>
													</div>
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">16:00</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-brand"></i>
														</div>
														<div class="kt-timeline-v2__item-text kt-padding-top-5">
															<?= __('Lorem ipsum dolor sit amit,consectetur eiusmdd tempor'); ?><br>
															<?= __('incididunt ut labore et dolore magna elit enim at minim'); ?><br>
															<?= __('veniam quis nostrud'); ?>
														</div>
													</div>
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">17:00</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-danger"></i>
														</div>
														<div class="kt-timeline-v2__item-text kt-padding-top-5">
															<?= __('Received a new feedback on'); ?> <a href="#" class="kt-link kt-link--brand kt-font-bolder"><?= __('FinancePro App'); ?></a> <?= __('product.'); ?>
														</div>
													</div>
													<div class="kt-timeline-v2__item">
														<span class="kt-timeline-v2__item-time">15:30</span>
														<div class="kt-timeline-v2__item-cricle">
															<i class="fa fa-genderless kt-font-danger"></i>
														</div>
														<div class="kt-timeline-v2__item-text kt-padding-top-5">
															<?= __('New notification message has been received on'); ?> <a href="#" class="kt-link kt-link--brand kt-font-bolder"><?= __('LoopFin Pro'); ?></a> <?= __('product.'); ?>
														</div>
													</div>
												</div>
											</div>

											<!--End::Timeline 3 -->
										</div>
									</div>

									<!--End::Portlet-->
								</div>
								<div class="col-xl-6 col-lg-6 order-lg-1 order-xl-1">

									<!--Begin::Portlet-->
									<div class="kt-portlet kt-portlet--height-fluid">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													<?= __('Recent Notifications'); ?>
												</h3>
											</div>
											<div class="kt-portlet__head-toolbar">
												<ul class="nav nav-pills nav-pills-sm nav-pills-label nav-pills-bold">
													<li class="nav-item">
														<a class="nav-link active" data-toggle="tab" href="#kt_widget3_tab1_content" role="tab">
															<?= __('Today'); ?>
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-toggle="tab" href="#kt_widget3_tab2_content" role="tab">
															<?= __('Month'); ?>
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="kt-portlet__body">
											<div class="tab-content">
												<div class="tab-pane active" id="kt_widget3_tab1_content">

													<!--Begin::Timeline 3 -->
													<div class="kt-timeline-v3">
														<div class="kt-timeline-v3__items">
															<div class="kt-timeline-v3__item kt-timeline-v3__item--info">
																<span class="kt-timeline-v3__item-time">09:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem ipsum dolor sit amit,consectetur eiusmdd tempor'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Bob'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--warning">
																<span class="kt-timeline-v3__item-time">10:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem ipsum dolor sit amit'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Sean'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
																<span class="kt-timeline-v3__item-time">11:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem ipsum dolor sit amit eiusmdd tempor'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By James'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--success">
																<span class="kt-timeline-v3__item-time">12:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem ipsum dolor'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By James'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--danger">
																<span class="kt-timeline-v3__item-time">14:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem ipsum dolor sit amit,consectetur eiusmdd'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Derrick'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--info">
																<span class="kt-timeline-v3__item-time">15:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem ipsum dolor sit amit,consectetur'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Iman'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
																<span class="kt-timeline-v3__item-time">17:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem ipsum dolor sit consectetur eiusmdd tempor'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Aziko'); ?>
																		</a>
																	</span>
																</div>
															</div>
														</div>
													</div>

													<!--End::Timeline 3 -->
												</div>
												<div class="tab-pane" id="kt_widget3_tab2_content">

													<!--Begin::Timeline 3 -->
													<div class="kt-timeline-v3">
														<div class="kt-timeline-v3__items">
															<div class="kt-timeline-v3__item kt-timeline-v3__item--info">
																<span class="kt-timeline-v3__item-time kt-font-success">09:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Contrary to popular belief, Lorem Ipsum is not simply random text.'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Bob'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--warning">
																<span class="kt-timeline-v3__item-time kt-font-warning">10:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('There are many variations of passages of Lorem Ipsum available.'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Sean'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
																<span class="kt-timeline-v3__item-time kt-font-primary">11:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Contrary to popular belief, Lorem Ipsum is not simply random text.'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By James'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--success">
																<span class="kt-timeline-v3__item-time kt-font-success">12:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('The standard chunk of Lorem Ipsum used since the 1500s is reproduced.'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By James'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--danger">
																<span class="kt-timeline-v3__item-time kt-font-warning">14:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Latin words, combined with a handful of model sentence structures.'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Derrick'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--info">
																<span class="kt-timeline-v3__item-time kt-font-info">15:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Contrary to popular belief, Lorem Ipsum is not simply random text.'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Iman'); ?>
																		</a>
																	</span>
																</div>
															</div>
															<div class="kt-timeline-v3__item kt-timeline-v3__item--brand">
																<span class="kt-timeline-v3__item-time kt-font-danger">17:00</span>
																<div class="kt-timeline-v3__item-desc">
																	<span class="kt-timeline-v3__item-text">
																		<?= __('Lorem Ipsum is therefore always free from repetition, injected humour.'); ?>
																	</span><br>
																	<span class="kt-timeline-v3__item-user-name">
																		<a href="#" class="kt-link kt-link--dark kt-timeline-v3__itek-link">
																			<?= __('By Aziko'); ?>
																		</a>
																	</span>
																</div>
															</div>
														</div>
													</div>

													<!--End::Timeline 3 -->
												</div>
											</div>
										</div>
									</div>

									<!--End::Portlet-->
								</div>
							</div>

							<!--End::Row-->
							<!--End::Dashboard 6-->
						</div>

						<!-- end:: Content -->
					</div>

