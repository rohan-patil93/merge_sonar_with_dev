  <?php

  $url1 = API."?mode=admindashboarddetails&company_id=".$_SESSION['company_id']."&vendor_id=".$_SESSION['userid'];
  $data_details1 = callAPI($url1);
  $emp_count_dashboard = json_decode($data_details1,1);
  $url13 = API."?mode=timesheethrsdashboard&company_id=".$_SESSION['company_id']."&vendor_id=".$_SESSION['userid'];
  $data_details13 = callAPI($url13);
  $timesheet = json_decode($data_details13,1);
  $stringconverttimesheet = implode(',',(array)$timesheet['timesheet_total_hrs']);
  $summary_data = array('Yearly','Quaterly','Monthly','Weekly','Non-Billable');
  $total_requests= array('2341','1222','222','111','0');
  $bgcolorlist = array('0'=>'success','1'=>'warning','2'=>'primary','3'=>'info','4'=>'dark','5'=>'secondary');
  $stausresult = ViewStatusData();
  $emp_count_dashboard = json_decode($data_details1,1);
   $fromdate = date('Y-m')."-01";
   $todate = date('Y-m-d');
  $emp_url = API."?mode=togetvendoremployeedetails&company_id=".$_SESSION['company_id']."&vendor_id=".$_SESSION['vendor_id']."&fromdate=".$fromdate."&todate=".$todate;
  $emp_data_list = callAPI($emp_url);
  $emp_data_list = json_decode($emp_data_list,1);
  ?>

  <div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-folder-open"></i> Total Head Count </p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $emp_count_dashboard['total_emp']; ?></h2>
                    <p class="m-b-0 m-t-15">As of Today</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> This month joinings</p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-blue"><?php echo $emp_count_dashboard['this_month_join']; ?></h2>
                    <p class="m-b-0 m-t-15">
                        <?php echo $emp_count_dashboard['this_month_join'] >= $emp_count_dashboard['previous_month_join'] ?"<i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":"<i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>
                        From Previous Month (<?php echo $emp_count_dashboard['previous_month_join'];?>)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card ticket-card">
                <div class="card-body">
                    <p class="m-b-30 bg-c-green lbl-card"><i class="fas fa-users"></i>This Month Separation </p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-green"><?php echo $emp_count_dashboard['this_month_leaving']; ?></h2>

                        <p class="m-b-0 m-t-15"><?php echo $emp_count_dashboard['this_month_leaving'] >= $emp_count_dashboard['previous_month_leaving'] ?"<i class='fas fa-caret-up m-r-10 f-18 text-c-blue'></i>":"<i class='fas fa-caret-down m-r-10 f-18 text-c-red'></i>"; ?>From Previous Month (<?php echo $emp_count_dashboard['previous_month_leaving'];?>)</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-red text-white">
                <div class="card-block">
                    <div class="row align-items-center">
                        <input type="hidden" name="timesheetdata"  id="timesheetdata" value="<?php echo $stringconverttimesheet; ?>">
                        <div class="col-8">
                            <h4 class="m-b-0"><?php echo customeummarmonyformate(array_sum($timesheet['timesheet_total_hrs'])); ?></h4>
                            <p class="m-b-0">Timesheet Summary</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="fas fa-user f-20"></i>
                        </div>
                    </div>
                    <div id="Widget-line-chart1" class="chart-line chart-shadow" style="width:100%;height:75px"></div>
                </div>
            </div>
        </div>
        <!-- ticket, proj, clent end -->
    </div>

    <div class="col-md-12">
        <div class="card table-card">
            <div class="card-header">
                <h5>Employee TimeSheets</h5>
            </div>
            <div class="row col-lg-12 mt-2">
                <div class="col-lg-12">
                    <div class="row col-lg-12 ml-2">
                        <?php
                        $colorindex = 0;
                        if(count((array)$total_requests)>0) {
                            foreach($total_requests as $rkey=>$request){ ?>
                                <button type="button" class="btn circle-circle-xl border-<?php echo $bgcolorlist[$colorindex]; ?> ml-2 "><div class="text-center font-size-h3"><?php echo $request==''?'0':$request; ?></div> <div class="text-center font-size-h6"><?php echo $summary_data[$rkey];?></div></button>
                            <?php $colorindex++;
                            }
                        } ?>
                    </div>
                </div>
            </div>
            <div class="card-block p-b-0">
                <div class="table-responsive">
                    <table class="table table-hover ">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Employe Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Billable Amount</th>
                                <th scope="col">Billable Hrs </th>
                                <th scope="col">Non Billable Hrs</th>
                                <th scope="col">Actieon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;foreach($emp_data_list as $emp_data){ ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <?php echo $emp_data['name'];?>
                                </td>
                                <td>
                                    <?php echo $emp_data['email_id'];?>
                                </td>
                                <td><?php echo $emp_data['bill_amount'];?></td>
                                <td><?php echo $emp_data['billable_hrs'];?></td>
                                <td><?php echo $emp_data['nonbillable_hrs'];?></td>
                                <td>
                                    <a class="btn btn-primary waves-effect waves-light" href="index.php?option=COM_VIEW_EMP_TIMESHEETS&PAY_YEAR=<?php echo date('Y',strtotime($fromdate)) ?>&PAY_MONTH=<?php echo date('m',strtotime($fromdate)) ?>&EMP=<?php echo $emp_data['emp_id'];?>">View
                                    </a>
                                </td>
                            </tr>
                            <?php $i++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
