
                        <div class="d-flex flex-column-fluid">
                            
                                <div class="row">
                                    <div class="col-xl-12">
                                        
                                            <div class="card-body">
                                                
                                                <div class="d-flex align-items-center">
                                                    
                                                    
                                                
                                                    <div class="bgi-no-repeat bgi-size-cover rounded min-h-265px" style="background-image: url(assets/media/stock-900x600/3.jpg)"></div>
                                                    
                                                        </span>24</a>
                                                        <a href="#" class="btn btn-icon-danger btn-sm btn-text-dark-50 bg-hover-light-danger btn-hover-text-danger rounded font-weight-bolder font-size-sm p-2">
                                                        <span class="svg-icon svg-icon-md svg-icon-danger pr-1">

                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <polygon points="0 0 24 0 24 24 0 24" />
                                                                    <path d="M16.5,4.5 C14.8905,4.5 13.00825,6.32463215 12,7.5 C10.99175,6.32463215 9.1095,4.5 7.5,4.5 C4.651,4.5 3,6.72217984 3,9.55040872 C3,12.6834696 6,16 12,19.5 C18,16 21,12.75 21,9.75 C21,6.92177112 19.349,4.5 16.5,4.5 Z" fill="#000000" fill-rule="nonzero" />
                                                                </g>
                                                            </svg>
                                                            
                                                
                                            </div>
            </div>

    </div>
    <div class="col-lg-4 draggable-zone">
                            <section class="card card-custom gutter-b draggable" style="">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label"><?php echo __('Attendance Summary'); ?></h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                                            <i class="ki ki-menu"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card flex-fill">
                                       <div class="card-body">
                                          <div class="statistics">
                                             <div class="row">
                                                <div class="col-md-6 col-6 text-center">
                                                   <div class="stats-box mb-4">
                                                      <p><?php echo __('Total Employees'); ?></p>
                                                      <h3>385</h3>
                                                   </div>
                                                </div>
                                                <div class="col-md-6 col-6 text-center">
                                                   <div class="stats-box mb-4">
                                                      <p><?php echo __('Present'); ?></p>
                                                      <h3>19</h3>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="progress mb-4">
                                             <progress class="progress-bar bg-purple" value="30%" max="100" style="width: 30%;">30%</progress>
                                             <progress class="progress-bar bg-warning" value="22%" max="100" style="width: 22%;">22%</progress>
                                             <progress class="progress-bar bg-success" value="24%" max="100" style="width: 24%;">24%</progress>
                                             <progress class="progress-bar bg-danger" value="26%" max="100" style="width: 26%;">21%</progress>
                                             <progress class="progress-bar bg-info" value="10%" max="100" style="width: 10%;">10%</progress>
                                          </div>
                                          <div>
                                             <p><i class="far fa-dot-circle text-purple mr-2"></i><?php echo __('Users have checked in'); ?><span class="float-right">166</span></p>
                                             <p><i class="far fa-dot-circle text-warning mr-2"></i><?php echo __('Users are currently inside office'); ?> <span class="float-right">115</span></p>
                                             <p><i class="far fa-dot-circle text-success mr-2"></i><?php echo __('Users are yet to check in'); ?> <span class="float-right">31</span></p>
                                             <p><i class="far fa-dot-circle text-danger mr-2"></i><?php echo __('Users are on paid leave'); ?> <span class="float-right">47</span></p>
                                             <p class=""><i class="far fa-dot-circle text-info mr-2"></i><?php echo __('Users are on weekend today'); ?> <span class="float-right">5</span></p>
                                             <p class=""><i class="far fa-dot-circle text-purple mr-2"></i><?php echo __('Users are on unpaid leave'); ?> <span class="float-right">5</span></p>
                                             <p class=""><i class="far fa-dot-circle text-warning mr-2"></i><?php echo __('Users are on Holiday today'); ?> <span class="float-right">5</span></p>
                                             <p class=""><i class="far fa-dot-circle text-success mr-2"></i><?php echo __('Users are on onduty today'); ?> <span class="float-right">5</span></p>
                                             <p class="mb-0"><i class="far fa-dot-circle text-info mr-2"></i><?php echo __('Users are on work from home today'); ?> <span class="float-right">5</span></p>
                                          </div>
                                       </div>
                                    </div>
                                </div>
                            </section>
                        </div>
    <div class="col-lg-4 draggable-zone">
        <?php include_once 'dash_borad_leaves.php'; ?>
    </div>
    <div class="col-lg-4 draggable-zone">
        <?php include_once 'dash_borad_approval.php'; ?>
    </div>
    <div class="col-lg-4 draggable-zone">
        <?php include_once 'dash_borad_timesheet.php'; ?>
    </div>
    <div class="col-lg-4 draggable-zone">
        <?php include_once 'dash_borad_upcoming_holidays.php'; ?>
    </div>
</div>
</div>
     <div class="tab-pane fade" id="tab_forms_widget_2" role="tabpanel" aria-labelledby="tab_forms_widget_2"><?php echo __('Tab tab_forms_widget_2 2'); ?></div>
     <div class="tab-pane fade" id="tab_forms_widget_3" role="tabpanel" aria-labelledby="tab_forms_widget_3"><?php echo __('Tab tab_forms_widget_3 2'); ?></div>
     <div class="tab-pane fade" id="tab_forms_widget_4" role="tabpanel" aria-labelledby="tab_forms_widget_4"><?php echo __('Tab tab_forms_widget_4 2'); ?></div>
     <div class="tab-pane fade" id="tab_forms_widget_5" role="tabpanel" aria-labelledby="tab_forms_widget_5"><?php echo __('Tab tab_forms_widget_5 2'); ?></div>
     <div class="tab-pane fade" id="tab_forms_widget_6" role="tabpanel" aria-labelledby="tab_forms_widget_6"><?php echo __('Tab forms_widget_tab_6 2'); ?></div>
</div>

                            </div>
                            <!--end::Container-->
                        </div>
