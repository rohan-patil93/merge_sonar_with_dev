<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['todo'] = 1;
$transaction_table = $_POST['transaction_table'];
$transaction_id = $_POST['transaction_id'];
$finance_tax_year = $_POST['finance_tax_year']!=""?$_POST['finance_tax_year']:"";
if($_GET['Rid'] !=""){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $task_id = explode(":", $rid);
    $tas_id = $task_id['1'];
}
$url = API."?mode=gettransactionactivityschedules&transaction_id=".$transaction_id."&transaction_table=".$transaction_table."&admin_type=".$_SESSION['admin_type']."&tas_id=".$tas_id."&finance_tax_year=".$finance_tax_year;
$response = callAPI($url);
$result = json_decode($response,1);
$transaction_activity_schedule = $result['transaction_activity_schedule'];
$self_task_cnt =$action_task_tab_cnt =$self_yet_to_start_tab_cnt =$self_in_progress_tab_cnt = $self_approved_tab_cnt = $action_pendding_tab_cnt = $action_approved_tab_cnt = $pendding_approve_cnt = $inprogress_cnt = 0;
$self_yet_to_start_tab =$self_in_progress_tab = $self_approved_tab = $action_pendding_tab = $action_approved_tab = array();
foreach($transaction_activity_schedule as $tasks_category_id=>$tas_res){
    foreach($tas_res as $tas){
        $self_task_cnt++;
        if($tas['current_status']==ST_ACTIVE){
            $self_yet_to_start_tab[$tasks_category_id][] = $tas;
            $self_yet_to_start_tab_cnt++;
        }elseif($tas['current_status']==ST_APPROVED){
            $self_approved_tab[$tasks_category_id][] = $tas;
            $self_approved_tab_cnt++;
        }else{
            $self_in_progress_tab[$tasks_category_id][] = $tas;
            $self_in_progress_tab_cnt++;
        }
        if($tas['current_status']==ST_APPROVED1 || $tas['current_status']==ST_APPROVED2){
            $pendding_approve_cnt++;
        }elseif($tas['current_status']==ST_INPROGRESS){
            $inprogress_cnt++;
        }
    }
}
$action_transaction_activity_schedule = $result['action_transaction_activity_schedule'];
foreach($action_transaction_activity_schedule as $tasks_category_id=>$tas_res){
    foreach($tas_res as $tas){
        $action_task_tab_cnt++;
        if($tas['response_type']!='0'){
            $action_approved_tab[$tasks_category_id][] = $tas;
            $action_approved_tab_cnt++;
        }else{
            $action_pendding_tab[$tasks_category_id][] = $tas;
            $action_pendding_tab_cnt++;
        }
    }
}



$tasks_categories = $result['tasks_categories'];
$status_progress = $result['status_progress'];
?>
<script type="text/javascript" src="../assets/js/custom_insert_transaction_activity_schedules.js"></script>

<input type="hidden" id="js_tas_id" value="<?php echo $tas_id; ?>">
<input type="hidden" id="transaction_table_id" value="<?php echo $transaction_table; ?>">
<div class="card">
    <div class="card-body">
        <div class="row p-3">
            <div class="col-lg-3">
                <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 updateUrlParameter_task <?php echo ($_REQUEST['T2']=='self_task_tab' || $_REQUEST['T2'] =='' && ($_REQUEST['T2']!='action_task_tab'))?"active":"";?>" data-bs-toggle="tab" href="#self_task_tab" role="tab" data-param="T2" data-paramvalue="self_task_tab" onclick="updateUrlParameter('T2','self_task_tab')"> Task <span class="form-label label label-xs label-success m-l-10"><?php echo $self_task_cnt; ?></span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 updateUrlParameter_task <?php echo $_REQUEST['T2']=='action_task_tab'?"active":"";?>" data-bs-toggle="tab" href="#action_task_tab" role="tab" data-param="T2" data-paramvalue="action_task_tab">Action <span class="form-label label label-xs label-danger m-l-10"><?php echo $action_pendding_tab_cnt; ?></span> <span class="form-label label label-xs label-success m-l-10"><?php echo $action_approved_tab_cnt; ?></span> </a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-2">
                <?php if($_SESSION['emp_id'] == $transaction_id && $inprogress_cnt !=0){ ?>
              <button class="btn btn-danger waves-effect waves-light  btn-sm p-1 mt-3 SubmitForApprovalTask"  ><i class="ti-thumb-up"></i> Submit For Approval</button>
          <?php } ?>
          </div>
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-2  text-center">

                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $self_task_cnt; ?></h5>
                            <p class="m-b-0 f-12">Total Task</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $self_yet_to_start_tab_cnt; ?></h5>
                            <p class="m-b-0 f-12">Yet To Start</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $inprogress_cnt; ?></h5>
                            <p class="m-b-0  f-12">In Progress</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $pendding_approve_cnt; ?></h5>
                            <p class="m-b-0 f-12">Review Pending</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $self_approved_tab_cnt; ?></h5>
                            <p class="m-b-0 f-12">Approved</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12" id="tasklistdiv">
        <div class="tab-content card-block p-0">
            <div class="tab-pane <?php echo ($_REQUEST['T2']=='self_task_tab' || $_REQUEST['T2'] =='' && ($_REQUEST['T2']!='action_task_tab'))?"active":"";?>" id="self_task_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12 <?php echo ($_REQUEST['T3']=='self_yet_to_start_tab' || $_REQUEST['T3']=="" || ($_REQUEST['T3']!="self_in_progress_tab" && $_REQUEST['T3']!="self_approved_tab"))?"active":"";?>" data-bs-toggle="tab" href="#self_yet_to_start_tab" role="tab" onclick="updateUrlParameter('T3','self_yet_to_start_tab')"> Yet To Start <span class="form-label label label-xs label-success m-l-10"><?php echo $self_yet_to_start_tab_cnt; ?></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12 <?php echo $_REQUEST['T3']=='self_in_progress_tab'?"active":"";?>" data-bs-toggle="tab" href="#self_in_progress_tab" role="tab" onclick="updateUrlParameter('T3','self_in_progress_tab')">In-Progress <span class="form-label label label-xs label-danger m-l-10"><?php echo $self_in_progress_tab_cnt; ?></span> </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12 <?php echo $_REQUEST['T3']=='self_approved_tab'?"active":"";?>" data-bs-toggle="tab" href="#self_approved_tab" role="tab" onclick="updateUrlParameter('T3','self_approved_tab')">Approved <span class="form-label label label-xs label-success m-l-10"><?php echo $self_approved_tab_cnt; ?></span> </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content card-block p-0">
                        <div class="tab-pane <?php echo ($_REQUEST['T3']=='self_yet_to_start_tab' || $_REQUEST['T3']=="" || ($_REQUEST['T3']!="self_in_progress_tab" && $_REQUEST['T3']!="self_approved_tab"))?"active":"";?>" id="self_yet_to_start_tab" role="tabpanel">
                              <div class="card-header  ">
                                <h5>Yet To Start Task</h5>
                              </div>
                              <div class="card-body">
                                  <div class="accordion-desc">
                                    <div class="accordion-block color-accordion-block">
                                        <div class="color-accordion" id="color-accordion">
                                        <?php if(count((array)$self_yet_to_start_tab) != 0){
                                        foreach($self_yet_to_start_tab as $tasks_category_id=>$tas_res){ ?>
                                          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $tasks_categories[$tasks_category_id]['category_name'];?>
                                          <span class="form-label label label-md label-info float-right"><?php echo "Task : ".count((array)$tas_res); ?></span>
                                        </a>
                                        <div class="accordion-desc">
                                            <table class="table table-hover">
                                                <tbody>
                                                  <?php foreach($tas_res as $tas){ ?>
                                                    <tr  class="<?php echo $tas_id == $tas['id']?"b-success":""; ?> select_task_<?php echo $tas['id'] ?> all_select_task">
                                                          <th scope="col" class="w-b-w-s text-primary getCheckList"  data-id="<?php echo $tas['id']; ?>"  data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open task" >
                                                               <h6 class="mb-0 f-14" ><?php echo $tas['task_name']; ?></h6>
                                                           </th>
                                                         <th scope="col" style="text-align: center;" >
                                                           <?php
                                                                   $stausresultv = $status_progress[$tas['current_status']];
                                                                   $stausresult = $_SESSION['status'][$tas['current_status']];
                                                           ?>
                                                           <span  class="statusid_<?php echo $tas['id']; ?>">
                                                                   <span class="form-label label label-success" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $stausresultv['name']; ?>
                                                                   </span>
                                                           </span>
                                                        </th>
                                                     </tr>
                                                   <?php } ?>
                                                 </tbody>
                                               </table>
                                         </div>
                                       <?php } ?>
                                       <?php } else { ?>
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
                        <div class="tab-pane <?php echo $_REQUEST['T3']=='self_in_progress_tab'?"active":"";?>" id="self_in_progress_tab" role="tabpanel">
                            <div class="card-header  ">
                                <h5>In Progress Task</h5>
                              </div>
                              <div class="card-body">
                                  <div class="accordion-desc">
                                    <div class="accordion-block color-accordion-block">
                                        <div class="color-accordion" id="color-accordion_2">
                                        <?php if(count((array)$self_in_progress_tab) != 0){
                                        foreach($self_in_progress_tab as $tasks_category_id=>$tas_res){ ?>
                                          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $tasks_categories[$tasks_category_id]['category_name'];?>
                                          <span class="form-label label label-md label-info float-right"><?php echo "Task : ".count((array)$tas_res); ?></span>
                                        </a>
                                        <div class="accordion-desc">
                                            <table class="table table-hover">
                                                <tbody>
                                                  <?php foreach($tas_res as $tas){ ?>
                                                    <tr class="<?php echo $tas_id == $tas['id']?"b-success":""; ?> select_task_<?php echo $tas['id'] ?> all_select_task">
                                                          <th scope="col" class="w-b-w-s text-primary getCheckList"  data-id="<?php echo $tas['id']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open task">
                                                               <h6 class="mb-0 f-14" ><?php echo $tas['task_name']; ?>
                                                                   <small class="d-block"><?php echo $tas['completion_date']!=""?"Request Date : ".$tas['completion_date']:' - '; ?></small>
                                                               </h6>
                                                         </th>
                                                         <th scope="col" style="text-align: center;" >
                                                           <?php
                                                                   $stausresultv = $status_progress[$tas['current_status']];
                                                                   $stausresult = $_SESSION['status'][$tas['current_status']];
                                                           ?>
                                                           <span  class="statusid_<?php echo $tas['id']; ?>">
                                                                   <span class="form-label label label-success" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $stausresultv['name']; ?>
                                                                   </span>
                                                           </span>
                                                        </th>
                                                     </tr>
                                                   <?php } ?>
                                                 </tbody>
                                               </table>
                                         </div>
                                       <?php } ?>
                                       <?php } else { ?>
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
                        <div class="tab-pane <?php echo $_REQUEST['T3']=='self_approved_tab'?"active":"";?>" id="self_approved_tab" role="tabpanel">
                            <div class="card-header  ">
                                <h5>Approved Task</h5>
                              </div>
                              <div class="card-body">
                                  <div class="accordion-desc">
                                    <div class="accordion-block color-accordion-block">
                                        <div class="color-accordion" id="color-accordion_3">
                                        <?php if(count((array)$self_approved_tab) != 0){
                                        foreach($self_approved_tab as $tasks_category_id=>$tas_res){ ?>
                                          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $tasks_categories[$tasks_category_id]['category_name'];?>
                                          <span class="form-label label label-md label-info float-right"><?php echo "Task : ".count((array)$tas_res); ?></span>
                                        </a>
                                        <div class="accordion-desc">
                                            <table class="table table-hover">
                                                <tbody>
                                                  <?php foreach($tas_res as $tas){ ?>
                                                    <tr class="<?php echo $tas_id == $tas['id']?"b-success":""; ?> select_task_<?php echo $tas['id'] ?> all_select_task">
                                                          <th scope="col" class="w-b-w-s text-primary getCheckList"  data-id="<?php echo $tas['id']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open task">
                                                               <h6 class="mb-0 f-14" ><?php echo $tas['task_name']; ?>
                                                                   <small class="d-block">Approved Date : <?php echo $tas['approval_date'] !=""?$tas['approval_date']:" - "; ?></small>
                                                               </h6>
                                                         </th>
                                                         <th scope="col" style="text-align: center;" >
                                                           <?php
                                                                   $stausresultv = $status_progress[$tas['current_status']];
                                                                   $stausresult = $_SESSION['status'][$tas['current_status']];
                                                           ?>
                                                           <span  class="statusid_<?php echo $tas['id']; ?>">
                                                                   <span class="form-label label label-success" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $stausresultv['name']; ?>
                                                                   </span>
                                                           </span>
                                                        </th>
                                                     </tr>
                                                   <?php } ?>
                                                 </tbody>
                                               </table>
                                         </div>
                                       <?php } ?>
                                       <?php } else { ?>
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
                    </div>



                  </div>
            </div>
            <div class="tab-pane <?php echo $_REQUEST['T2']=='action_task_tab'?"active":"";?>" id="action_task_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12 <?php echo ($_REQUEST['T3']=='action_pendding_tab' || $_REQUEST['T3']=="" || $_REQUEST['T3']!='action_approved_tab')?"active":"";?>" data-bs-toggle="tab" href="#action_pendding_tab" role="tab" onclick="updateUrlParameter('T3','action_pendding_tab')"> Pending <span class="form-label label label-xs label-danger m-l-10"><?php echo $action_pendding_tab_cnt; ?></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12 <?php echo $_REQUEST['T3']=='action_approved_tab'?"active":"";?>" data-bs-toggle="tab" href="#action_approved_tab" role="tab" onclick="updateUrlParameter('T3','action_approved_tab')">Approved <span class="form-label label label-xs label-success m-l-10"><?php echo $action_approved_tab_cnt; ?></span> </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content card-block p-0">
                        <div class="tab-pane <?php echo ($_REQUEST['T3']=='action_pendding_tab' || $_REQUEST['T3']=="" || $_REQUEST['T3']!='action_approved_tab')?"active":"";?>" id="action_pendding_tab" role="tabpanel">
                            <div class="card-header  ">
                                <h5>Pending Tasks</h5>
                              </div>
                              <div class="card-body">
                                  <div class="accordion-desc">
                                    <div class="accordion-block color-accordion-block">
                                        <div class="color-accordion" id="color-accordion_4">
                                        <?php if(count((array)$action_pendding_tab) != 0){
                                        foreach($action_pendding_tab as $tasks_category_id=>$tas_res){ ?>
                                          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $tasks_categories[$tasks_category_id]['category_name'];?>
                                          <span class="form-label label label-md label-info float-right"><?php echo "Task : ".count((array)$tas_res); ?></span>
                                        </a>
                                        <div class="accordion-desc">
                                            <table class="table table-hover">
                                                <tbody>
                                                  <?php foreach($tas_res as $tas){ ?>
                                                    <tr class="<?php echo $tas_id == $tas['id']?"b-success":""; ?> select_task_<?php echo $tas['id'] ?> all_select_task">
                                                          <th scope="col" class="w-b-w-s text-primary getCheckList"  data-id="<?php echo $tas['id']; ?>"  data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open task">
                                                               <h6 class="mb-0 f-14" ><?php echo $tas['task_name']; ?>
                                                                   <small class="d-block">Request Date : <?php echo $tas['request_date']; ?></small>
                                                               </h6>
                                                         </th>
                                                         <th scope="col" style="text-align: center;" >
                                                           <?php
                                                                   $stausresultv = $status_progress[$tas['current_status']];
                                                                   $stausresult = $_SESSION['status'][$tas['current_status']];
                                                           ?>
                                                           <span  class="statusid_<?php echo $tas['id']; ?>">
                                                                   <span class="form-label label label-success" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $stausresultv['name']; ?>
                                                                   </span>
                                                           </span>
                                                        </th>
                                                     </tr>
                                                   <?php } ?>
                                                 </tbody>
                                               </table>
                                         </div>
                                       <?php } ?>
                                       <?php } else { ?>
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
                        <div class="tab-pane <?php echo ($_REQUEST['T3']=='action_approved_tab')?"active":"";?>" id="action_approved_tab" role="tabpanel">
                            <div class="card-header  ">
                                <h5>Approved Tasks</h5>
                              </div>
                              <div class="card-body">
                                  <div class="accordion-desc">
                                    <div class="accordion-block color-accordion-block">
                                        <div class="color-accordion" id="color-accordion_5">
                                        <?php if(count((array)$action_approved_tab) != 0){
                                        foreach($action_approved_tab as $tasks_category_id=>$tas_res){ ?>
                                          <a class="accordion-msg b-none waves-effect waves-light f-w-700"><?php echo $tasks_categories[$tasks_category_id]['category_name'];?>
                                          <span class="form-label label label-md label-info float-right"><?php echo "Task : ".count((array)$tas_res); ?></span>
                                        </a>
                                        <div class="accordion-desc">
                                            <table class="table table-hover">
                                                <tbody>
                                                  <?php foreach($tas_res as $tas){ ?>
                                                    <tr class="<?php echo $tas_id == $tas['id']?"b-success":""; ?> select_task_<?php echo $tas['id'] ?> all_select_task">
                                                          <th scope="col" class="w-b-w-s text-primary getCheckList"  data-id="<?php echo $tas['id']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open task">
                                                               <h6 class="mb-0 f-14" ><?php echo $tas['task_name']; ?>
                                                               <small class="d-block">Comments : <?php echo $tas['comments']; ?></small>
                                                               <small class="d-block"> Approved Date : <?php echo $tas['response_date']; ?></small></h6>
                                                         </th>
                                                         <th scope="col" style="text-align: center;" >
                                                           <?php
                                                                   $stausresultv = $status_progress[$tas['current_status']];
                                                                   $stausresult = $_SESSION['status'][$tas['current_status']];
                                                           ?>
                                                           <span  class="statusid_<?php echo $tas['id']; ?>">
                                                                   <span class="form-label label label-success" style="color: <?php echo "#".$stausresult['color']; ?>;    background:  <?php echo $stausresult['background_color']; ?>; border-color: <?php echo $stausresult['background_color']; ?>;" ><?php echo $stausresultv['name']; ?>
                                                                   </span>
                                                           </span>
                                                        </th>
                                                     </tr>
                                                   <?php } ?>
                                                 </tbody>
                                               </table>
                                         </div>
                                       <?php } ?>
                                       <?php } else { ?>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12" id="htmlchecklist" style="display: none">

    </div>
    <div class="col-lg-4" id="htmlsinglechecklist" style="display: none">

    </div>


</div>
<?php  ?>
<?php if($_POST['ajaxpage'] == 'Y'){ ?>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
<?php } ?>
