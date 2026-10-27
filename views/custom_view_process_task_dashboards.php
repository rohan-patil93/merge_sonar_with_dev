<?php
$startdate = date("Y-m-01", strtotime("-1 month", strtotime(date("Y-m-d"))));
$enddate =  date("Y-m-t", strtotime("+1 month", strtotime(date("Y-m-d"))));
$currentmonth = date("F");
$previousmonth = date("F",strtotime($startdate));
$nextmonth = date("F",strtotime($enddate));
$url = API."?mode=getemployeeonboardingtask&employee_id=".$employee_id."&admin_type=".$admin_type."&document_category_id=".$document_category_id."&dashboard_type=HR&startdate=".$startdate."&enddate=".$enddate;
$api = callAPI($url);
$result = json_decode($api,1);
$onboard_employeesall = $result['onboard_employees'];
$tasks_result = $result['tasks_result'];
$monthlycnt = $result['monthlycnt'];
$tasksresultemp = array();
$tasksresultuser = array();
$reviewpendinguser = $unallocatetaskemployee = array();
$totaltask = 0;
$totalinprogresstask = 0;
$totalapprovedtask = 0;
$totalreviewpendingtask =$totalyettostartcnttask=$totaldelayedcnttask =  0;
foreach($tasks_result as $tr){
    $tasksresultuser[$tr['assigned_to']] = $tr; // assigned_to is user id
    $tasksresultemp[$tr['transaction_id']] = $tr;// transaction_id is employee id
    if($tr['review_cnt'] !=0){
        $reviewpendinguser[$tr['assigned_to']] = $tr['assigned_to'];
    }
    $totaltask =$totaltask+$tr['total_task_cnt'];
    $totalinprogresstask =$totalinprogresstask+$tr['inprogress_cnt'];
    $totalreviewpendingtask =$totalreviewpendingtask+$tr['review_cnt'];
    $totalapprovedtask =$totalapprovedtask+$tr['approve_cnt'];
    $totalyettostartcnttask =$totalyettostartcnttask+$tr['yet_to_start_cnt'];
    $totaldelayedcnttask = $totaldelayedcnttask+$tr['delayed_cnt'];
}
$onboardinguserid = array();
foreach($onboard_employeesall as $onboardemployees){
    $onboardinguserid[$onboardemployees['user_id']] = $onboardemployees;

    $taskresults = $tasksresultuser[$onboardemployees['user_id']];
    if($taskresults['total_task_cnt'] !="" && $taskresults['total_task_cnt'] !="0"){
        $onboard_employees[] = $onboardemployees;
    }else{
        $unallocatetaskemployee[] = $onboardemployees;
    }
}
$totaltaskper = $totaltask !=0?round(($totalapprovedtask/$totaltask)*100):"0";
?>
<script type="text/javascript">
    function changeMonth(valu){
        console.log(valu);
        if(valu == "all"){
            $(".showallemp").show('slow');
        }else{
            $(".showallemp").hide('slow');
            $(".showonlyemp_"+valu).show('slow');
        }
    }
    function assignTaskEmployee(emp_id){
        jQuery.ajax({
            url: "views/custom_onboarding_employee_task_assigns.php",
            data:'emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $("#kt_modal_KTDatatable_remotem").modal("show");
            },
            error:function (){}
        });
    }
</script>
<div class="card product-progress-card mb-3">
    <div class="card-block">
        <div class="row ">
            <div class="col-lg-4">
                <div class="d-flex">
                <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                    <?php if($_SESSION['user']['user_photo']!=""){ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['user']['user_photo']; ?>" class="img-120 " alt="Details" onerror="this.src='uploads/all/no-image.jpg';"/>
                    </div>

                  <?php }else{ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="img-120 " alt="Details" onerror="this.src='uploads/all/no-image.jpg';"/>
                    </div>
                  <?php } ?>
                    <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                          <span class="f-12 f-w-600">Task Proc </span>
                          <span class="f-12 f-w-600"><?php echo $totaltaskper; ?>%</span>
                        </div>
                        <div class="col-md-12">
                          <div class="progress progress-xs">
                            <div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $totaltaskper; ?>%;" aria-valuenow="<?php echo $totaltaskper; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between flex-wrap mt-1">
                        <div class="d-flex mr-3">
                            <span class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $_SESSION['user']['display_name']; ?></span>
                        </div>
                    </div>
                    <div class="">
                        <div class="d-flex flex-wrap justify-content-between mt-1 ">
                            <div class="d-flex flex-column flex-grow-1 pr-8  ">
                                <div class="d-flex flex-wrap mb-2 mt-2">
                                    <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                    <i class="ti-email f-14 text-primary"></i>&nbsp;  <?php echo $_SESSION['user']['user_email']; ?></a>
                                </div>
                            </div>
                            <input type="hidden" id="location_id" value="">
                            <input type="hidden" id="start_date" value="2023-08-11">
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-xl-3 col-md-6 ">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo count((array)$onboard_employees)+count((array)$unallocatetaskemployee); ?></h3>
                                <p>Total Process</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo $monthlycnt[$currentmonth]!=""?$monthlycnt[$currentmonth]:"0"; ?></h3>
                                <p>Current Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo $monthlycnt[$previousmonth]!=""?$monthlycnt[$previousmonth]:"0"; ?></h3>
                                <p>Previous Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo $monthlycnt[$nextmonth]!=""?$monthlycnt[$nextmonth]:"0"; ?></h3>
                                <p>Next Month</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-2 text-center">
                        <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $totaltask; ?></h5>
                            <p class="m-b-0 f-12">Total Task</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $totalyettostartcnttask; ?></h5>
                            <p class="m-b-0 f-12">Yet To Start</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $totalinprogresstask; ?></h5>
                            <p class="m-b-0  f-12">In Progress</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $totalreviewpendingtask; ?></h5>
                            <p class="m-b-0 f-12">Review</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $totalapprovedtask; ?></h5>
                            <p class="m-b-0 f-12">Completed</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti-pulse f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0 text-danger"><?php echo $totaldelayedcnttask; ?></h5>
                            <p class="m-b-0 f-12 text-danger">Delayed Task</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-lg-9"></div>
    <div class="col-lg-3 text-right">
        <a class="btn btn-primary waves-effect waves-light" href="index?option=COM_INSERT_EMPLOYEES"><i class="ti-plus"></i> Add Employee</a>
        <a class="btn btn-primary waves-effect waves-light" href="index?option=COM_LIST_PROCESS_TASK_SETTINGS"><i class="ti-plus"></i> Task</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="row p-r-15">
                <div class="col-lg-8">
                    <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#onboardning" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Onboarding<span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$onboard_employees);?></span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#tasktab" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Task<span class="form-label label label-xs label-danger m-l-10"><?php echo count((array)$onboard_employees);?></span></a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#unallocatedtask" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Unallocated Task<span class="form-label label label-xs label-danger m-l-10"><?php echo count((array)$unallocatetaskemployee);?></span></a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 p-2">
                    <select aria-label="Select option" class="form-select js-example-basic-signle" onchange="changeMonth(this.value)">
                        <option value="all">All</option>
                        <option value="pm">Previous Month</option>
                        <option value="cm">Current Month</option>
                        <option value="nm">Next Month</option>
                    </select>
                </div>
            </div>
            <div class="tab-content tabs">
                <div class="tab-pane card-body p-0" id="tasktab" role="tabpanel">
                    <div class="card-header  ">
                        <h5>Employees Task&nbsp;<span class="form-label label label-md label-warning float-right"><?php echo count((array)$onboard_employees); ?> </span></h5>
                        <div class="card-header-right table-card-header ">
                            <div class="input-group input-group-button mb-0">
                                <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                                <span class="input-group-text addon-style">
                                    <span class=""><span class="ti-search text-white mt-2"></span></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                         <?php if(count((array)($onboard_employees)) != 0){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                            <thead>
                                <tr><th scope="col">Emp Code</th>
                                    <th scope="col">OnBoarding</th>
                                    <th scope="col">Task</th>
                                    <th scope="col">Yet To Start</th>
                                    <th scope="col">Inprogress</th>
                                    <th scope="col">Review</th>
                                    <th scope="col">Completed</th>
                                    <th scope="col" class="text-danger">Delayed</th>
                                    <th scope="col">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($onboard_employees as $onboard_emp){
                                    $taskresult = $tasksresultuser[$onboard_emp['user_id']];
                                    $dynmonth = date("F",strtotime($onboard_emp['joining_date']));
                                    $dynclass="showonlyemp_";
                                    if($dynmonth == $currentmonth){
                                        $dynclass .="cm";
                                    }elseif($dynmonth == $previousmonth){
                                        $dynclass .="pm";
                                    }elseif($dynmonth == $nextmonth){
                                        $dynclass .="nm";
                                    }
                                    $empiddecode = deocdeId('id:'.$onboard_emp['id']);
                                    ?>
                                <tr class="showallemp <?php echo $dynclass; ?>">
                                    <td>
                                        <a href="index.php?option=COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS&Rid=<?php echo $empiddecode; ?>" target="_blank">


                                        <div class="align-middle">
                                            <?php if($onboard_emp['image'] !=""){?>
                                                <img src="uploads/all/<?php echo $onboard_emp['image']; ?>"  class="img-radius img-40 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round pt-1 f-12 text-center"><?php echo initials($onboard_emp['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73 text-primary">
                                                <h6 class="mb-0"><?php echo $onboard_emp['display_name']; ?></h6>
                                                <p class="text-muted m-b-0"><?php echo $onboard_emp['emp_code']; ?></p>
                                            </div>
                                        </div>
                                       </a>

                                    </td>
                                    <td><?php echo fromDate($onboard_emp['joining_date']); ?></td>
                                    <td class="text-right"><?php echo $taskresult['total_task_cnt']; ?></td>
                                    <td class="text-right"><?php echo $taskresult['yet_to_start_cnt']; ?></td>
                                    <td class="text-right"><?php echo $taskresult['inprogress_cnt']; ?></td>
                                    <td class="text-right"><?php echo $taskresult['review_cnt']; ?></td>
                                    <td class="text-right"><?php echo $taskresult['approve_cnt']; ?></td>
                                    <td class="text-right text-danger"><?php echo $taskresult['delayed_cnt'];?></td>
                                    <td>
                                        <?php
                                            $complettaskper = $taskresult['total_task_cnt'] !=0?($taskresult['approve_cnt']/$taskresult['total_task_cnt'])*100:0;
                                        ?>
                                        <div class="row align-items-center m-b-15">
                                            <div class="col-auto">
                                                <p class="m-b-0">Approved</p>
                                            </div>
                                            <div class="col text-end">
                                                <p class="m-b-0 text-c-green"><?php echo $complettaskper; ?>%</p>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 0.4rem;">
                                            <div class="progress-bar bg-c-green" style="width:<?php echo $complettaskper; ?>%"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                               <?php  }else{ ?>
     <div class="card-px text-center py-20 mt-2">
      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>
      <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
      </div>
    </div>
  <?php } ?>
                    </div>
                </div>
                <div class="tab-pane card-body active p-0" id="onboardning" role="tabpanel">
                    <div class="card-header">
                        <h5>Employees</h5>
                        <div class="card-header-right table-card-header ">
                            <div class="input-group input-group-button mb-0">
                                <input type="text" id="dynamicfilterhideshow_2" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                                <span class="input-group-text addon-style">
                                    <span class=""><span class="ti-search text-white mt-2"></span></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                           <?php if(count((array)($onboard_employees)) != 0){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                            <thead>
                                <tr><th scope="col">Emp Code</th>
                                    <th scope="col">OnBoarding</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($onboard_employees as $onboard_emp){
                                    $taskresult = $tasksresultuser[$onboard_emp['user_id']];
                                    $empiddecode = deocdeId('id:'.$onboard_emp['id']);
                                    $dynmonth = date("F",strtotime($onboard_emp['joining_date']));
                                    $dynclass="showonlyemp_";
                                    if($dynmonth == $currentmonth){
                                        $dynclass .="cm";
                                    }elseif($dynmonth == $previousmonth){
                                        $dynclass .="pm";
                                    }elseif($dynmonth == $nextmonth){
                                        $dynclass .="nm";
                                    }
                                    $empiddecode = deocdeId('id:'.$onboard_emp['id']);
                                    ?>
                                <tr class="showallemp <?php echo $dynclass; ?>">
                                    <td><a href="index.php?option=COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS&Rid=<?php echo $empiddecode; ?>" target="_blank">
                                         <div class="align-middle">
                                            <?php if($onboard_emp['image'] !=""){?>
                                                <img src="uploads/all/<?php echo $onboard_emp['image']; ?>"  class="img-radius img-40 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round pt-1 f-12 text-center"><?php echo initials($onboard_emp['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73 text-primary">
                                                <h6 class="mb-0"><?php echo $onboard_emp['display_name']; ?></h6>
                                                <p class="text-muted m-b-0"><?php echo $onboard_emp['emp_code']; ?></p>
                                            </div>
                                        </div>
                                       </a>
                                    </td>
                                    <td><?php echo fromDate($onboard_emp['joining_date']); ?></td>
                                    <td>
                                    <?php if($taskresult['total_task_cnt'] <= $taskresult['approve_cnt']){ ?>
                                        <span class="form-label label label-xs label-success m-l-10">Completed</span>
                                    <?php  }else{ ?>
                                        <span class="form-label label label-xs label-danger m-l-10">Pendding</span>
                                    <?php  }
                                         ?>
                                    </td>

                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php  }else{ ?>
     <div class="card-px text-center py-20 mt-2">
      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>
      <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
      </div>
    </div>
  <?php } ?>
                    </div>
                </div>
                <div class="tab-pane card-body p-0" id="unallocatedtask" role="tabpanel">
                    <div class="card-header">
                        <h5>Unallocated Task</h5>
                        <div class="card-header-right table-card-header ">
                            <div class="input-group input-group-button mb-0">
                                <input type="text" id="dynamicfilterhideshow_3" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                                <span class="input-group-text addon-style">
                                    <span class=""><span class="ti-search text-white mt-2"></span></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                          <?php if(count((array)($unallocatetaskemployee)) != 0){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                            <thead>
                                <tr><th scope="col">Emp Code</th>
                                    <th scope="col">OnBoarding</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($unallocatetaskemployee as $onboard_emp){
                                    $taskresult = $tasksresultuser[$onboard_emp['user_id']];
                                    $empiddecode = deocdeId('id:'.$onboard_emp['id']);
                                    $dynmonth = date("F",strtotime($onboard_emp['joining_date']))
                                ?>
                                <tr class="showallemp <?php echo $dynclass; ?>">
                                     <td><a href="index.php?option=COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS&Rid=<?php echo $empiddecode; ?>" target="_blank">
                                         <div class="align-middle">
                                            <?php if($onboard_emp['image'] !=""){?>
                                                <img src="uploads/all/<?php echo $onboard_emp['image']; ?>"  class="img-radius img-40 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round pt-1 f-12 text-center"><?php echo initials($onboard_emp['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73 text-primary">
                                                <h6 class="mb-0"><?php echo $onboard_emp['display_name']; ?></h6>
                                                <p class="text-muted m-b-0"><?php echo $onboard_emp['emp_code']; ?></p>
                                            </div>
                                        </div>
                                       </a>
                                     </td>
                                    <td><?php echo fromDate($onboard_emp['joining_date']); ?></td>
                                    <td>
                                        <button class="btn btn-primary waves-effect waves-light" onclick="assignTaskEmployee('<?php echo $onboard_emp['id']; ?>')"><i class="ti-plus"></i> Assign Task</button></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php  }else{ ?>
                             <div class="card-px text-center py-20 mt-2">
                              <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>
                              <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                              </div>
                            </div>
                          <?php } ?>
                    </div>
                </div>

           </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header  ">
                    <h5>Review Pending  &nbsp;<span class="form-label label label-md label-warning float-right"><?php echo count((array)$reviewpendinguser); ?> </span></h5>
                    <div class="card-header-right table-card-header ">
                        <div class="input-group input-group-button mb-0">
                            <input type="text" id="dynamicfilterhideshow_4" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                            <span class="input-group-text addon-style">
                                <span class=""><span class="ti-search text-white mt-2"></span></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)($reviewpendinguser)) != 0){ ?>
                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                        <thead>
                            <tr><th scope="col">Emp Code</th>
                                <th scope="col">Name</th>
                                <th scope="col">OnBoarding Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reviewpendinguser as $uid=>$uidd){
                                $onboardinguseridres = $onboardinguserid[$uid];
                                 $empiddecode = deocdeId('id:'.$onboardinguseridres['id']);
                                ?>
                            <tr>
                                <td><a href="index.php?option=COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS&Rid=<?php echo $empiddecode; ?>" target="_blank"><div class="align-middle">
                                            <?php if($onboardinguseridres['image'] !=""){?>
                                                <img src="uploads/all/<?php echo $onboardinguseridres['image']; ?>"  class="img-radius img-40 align-top m-r-15" alt="User Profile" onerror="this.src='uploads/all/no-image.jpg';">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round pt-1 f-12 text-center"><?php echo initials($onboardinguseridres['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73 text-primary">
                                                <h6 class="mb-0"><?php echo $onboardinguseridres['display_name']; ?></h6>
                                                <p class="text-muted m-b-0"><?php echo $onboardinguseridres['emp_code']; ?></p>
                                            </div>
                                        </div>
                                       </a>
                                </td>
                                <td class="w-b-w-s"><?php echo $onboardinguseridres['display_name']; ?></td>
                                <td><?php echo fromDate($onboardinguseridres['joining_date']); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php  }else{ ?>
     <div class="card-px text-center py-20 mt-2">
      <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Task </p>
      <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
      </div>
    </div>
  <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
