<?php
if($_POST['finance_tax_year'] != ""){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}
$admin_type = $_SESSION['admin_type'];
$employee_id = $_SESSION['emp_id'];
$finance_tax_year = $_POST['finance_tax_year']!=""?$_POST['finance_tax_year']:$finance_tax_year;
$url = API."?mode=getemdmindashboard&employee_id=".$employee_id."&admin_type=".$admin_type."&finance_tax_year=".$finance_tax_year; $api = callAPI($url);
$result = json_decode($api,1);
$employees = $result['employees'];
$totaltask=$totalyettostartcnttask=$totalinprogresstask=$totalreviewpendingtask=$totalapprovedtask=$totaldelayedcnttask=0;
$tasks_result = $result['tasks_result'];
$action_transaction_activity_schedule = $result['action_transaction_activity_schedule'];
$empidwithuserid = $result['empidwithuserid'];
 $allocatedTaskemployees = $unallocatedTaskemployees = array();
foreach($tasks_result as $tr){
    $tasksresultuser[$tr['transaction_id']] = $tr; // assigned_to is user id
    $totaltask =$totaltask+$tr['total_task_cnt'];
    $totalinprogresstask =$totalinprogresstask+$tr['inprogress_cnt'];
    $totalreviewpendingtask =$totalreviewpendingtask+$tr['review_cnt'];
    $totalapprovedtask =$totalapprovedtask+$tr['approve_cnt'];
    $totalyettostartcnttask =$totalyettostartcnttask+$tr['yet_to_start_cnt'];
    $totaldelayedcnttask = $totaldelayedcnttask+$tr['delayed_cnt'];
}

$onboardinguserid = array();
$totalempids = array();
foreach($employees as $emplist){
    $totalempids[$emplist['id']] =$emplist['id'];
    $onboardinguserid[$emplist['id']] = $emplist;

    $taskresults = $tasksresultuser[$emplist['id']];
    if($taskresults['total_task_cnt'] !="" && $taskresults['total_task_cnt'] !="0"){
        $allocatedTaskemployees[] = $emplist;
        $informedtotalempids[$emplist['id']] =$emplist['id'];
    }else{
        $unallocatedTaskemployees[] = $emplist;
        $yettoinformedtotalempids[$emplist['id']] =$emplist['id'];

    }
}

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
        var finance_tax_year = $("#finance_tax_year").val();
        console.log(" finance_tax_year - "+finance_tax_year);
        jQuery.ajax({
            url: "views/custom_onboarding_employee_task_assigns.php",
            data:'emp_id='+emp_id+"&finance_tax_year="+finance_tax_year,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $("#kt_modal_KTDatatable_remotem").modal("show");
            },
            error:function (){}
        });
    }
    function insertEmployeePopUp(emp_id=""){

        $('#kt_modal_dynamic_popup').modal("show");
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#dynamic_popup1').html(htmloader);

        jQuery.ajax({
            url: "views/custom_insert_building_employee_popup.php",
            data:'emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);

                $(".js-example-basic-single").select2();
                $('.tooltip').remove();
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error:function (){}
        });
    }
    function assigneTaskChecked(){
        var check_emp_id = [];
        $('.check_emp_id').each(function(){
            if($(this).is(':checked')){
                check_emp_id.push($(this).val());
            }
        });
        console.log(check_emp_id);
        if (check_emp_id.length === 0){
            $(".checked_emp").hide();
            $(".btn_emp").show('slow');
        }else{
            $(".btn_emp").hide();
            $(".checked_emp").show('slow');
        }
        //     url: "views/custom_insert_asset_allocation_popup.php",
        //     data: "assetid="+assetid,
        //     type: "POST",
    }
    function assignTaskCheckedEmployee(){
        var finance_tax_year = $("#finance_tax_year").val();

        var check_emp_id = [];
        $('.check_emp_id').each(function(){
            if($(this).is(':checked')){
                check_emp_id.push($(this).val());
            }
        });
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: '<?php echo $_SESSION['error_message']['9178']; ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                jQuery.ajax({
                    url: "ajax.php",
                    data: "page=assigntasktoemployeesmanagement&check_emp_id="+check_emp_id+"&finance_tax_year="+finance_tax_year,
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        location.reload();
                    }
                });
            }
        });
    }
    function moreemployees(id_name) {
    console.log ('inserted to filtertable'+id_name);
    var employee_names_list = document.getElementById(id_name).value;
     console.log ('inserted to filtertable'+employee_names_list);

    jQuery.ajax({
                url: "views/custom_view_employees_popup.php",
                data:'empids='+employee_names_list,
                type: "POST",
                success:function(data){
          console.log("popup open ok");
                    $('#dynamic_popupxxl').html(data);
                    $('#kt_modal_dynamic_popupxxl').modal("show");
                },
                error:function (){}
            });

  }
</script>
<div class="card product-progress-card mb-3">
    <div class="card-block">
        <div class="row ">
            <div class="col-lg-5">
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

                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between flex-wrap mt-1">
                        <div class="d-flex mr-3">
                            <span class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $_SESSION['user']['display_name']; ?> <small class="f-10">(<?php echo "Apr ".date($finance_tax_year)." To Mar ".date($finance_tax_year+1); ?>)</small> </span>
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
            <div class="col-lg-7">
                <div class="row">

                    <div class="col-xl-4 col-md-6 ">
                        <input type="hidden" id="view_total_emp" value="<?php echo urlencode(json_encode($totalempids)); ?>">
                        <div class="card social-card cursor-pointer" onclick="moreemployees('view_total_emp')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo count((array)$allocatedTaskemployees)+count((array)$unallocatedTaskemployees); ?></h3>
                                <p>Total Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <input type="hidden" id="inform_total_emp" value="<?php echo urlencode(json_encode($informedtotalempids)); ?>">
                        <div class="card social-card cursor-pointer" onclick="moreemployees('inform_total_emp')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo count((array)$allocatedTaskemployees); ?></h3>
                                <p>Informed Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <input type="hidden" id="yetinform_total_emp" value="<?php echo urlencode(json_encode($yettoinformedtotalempids)); ?>">
                        <div class="card social-card cursor-pointer" onclick="moreemployees('yetinform_total_emp')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo count((array)$unallocatedTaskemployees); ?></h3>
                                <p>Yet To Inform</p>
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
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="row p-r-15">
                <div class="col-lg-8">
                    <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#allocatedtask" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Informed Employees  <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$allocatedTaskemployees);?></span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#unallocatedtask" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Yet To Inform   <span class="form-label label label-xs label-danger m-l-10"><?php echo count((array)$unallocatedTaskemployees);?></span></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content tabs">
                <div class="tab-pane card-body p-0 active" id="allocatedtask" role="tabpanel">
                    <div class="card-header  ">
                        <h5>Allocated Task &nbsp;<span class="form-label label label-md label-warning float-right"><?php echo count((array)$allocatedTaskemployees); ?> </span></h5>
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
                        <div class="card-block table-responsive">
                         <?php
                         $yet_to_start_cnt = $inprogress_cnt = $review_cnt = $approve_cnt = $delayed_cnt = 0;
                         if(count((array)($allocatedTaskemployees)) != 0){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                            <thead>
                                <tr><th scope="col">Employees</th>
                                    <th scope="col">Task</th>
                                    <th scope="col">Yet To Start</th>
                                    <th scope="col">In-progress</th>
                                    <th scope="col">Review</th>
                                    <th scope="col">Completed</th>

                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($allocatedTaskemployees as $onboard_emp){
                                    $taskresult = $tasksresultuser[$onboard_emp['id']];

                                    $yet_to_start_cnt += $taskresult['yet_to_start_cnt'];
                                    $inprogress_cnt += $taskresult['inprogress_cnt'];
                                    $review_cnt += $taskresult['review_cnt'];
                                    $approve_cnt += $taskresult['approve_cnt'];
                                    $delayed_cnt += $taskresult['delayed_cnt'];
                                     $empiddecode = deocdeId('id:'.$onboard_emp['id']);
                                    ?>
                                <tr class="showallemp">
                                    <td>
                                        <a href="index.php?option=COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS&Rid=<?php echo $empiddecode; ?>&FY=<?php echo $finance_tax_year; ?>" target="_blank">

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
                                    <td class="text-right"><?php echo $taskresult['total_task_cnt']!=""?$taskresult['total_task_cnt']:0; ?></td>
                                    <td class="text-right"><?php echo $taskresult['yet_to_start_cnt']!=""?$taskresult['yet_to_start_cnt']:0; ?></td>
                                    <td class="text-right"><?php echo $taskresult['inprogress_cnt']!=""?$taskresult['inprogress_cnt']:0; ?></td>
                                    <td class="text-right"><?php echo $taskresult['review_cnt']!=""?$taskresult['review_cnt']:0; ?></td>
                                    <td class="text-right"><?php echo $taskresult['approve_cnt']!=""?$taskresult['approve_cnt']:0; ?></td>

                                    <td>
                                        <?php
                                            $complettaskper = $taskresult['approve_requist_cnt'] !=0?($taskresult['approve_cnt']/$taskresult['approve_requist_cnt'])*100:0;
                                        ?>
                                        <div class="row align-items-center m-b-15">
                                            <div class="col-auto">
                                                <p class="m-b-0"></p>
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
                </div>
                <div class="tab-pane card-body p-0" id="unallocatedtask" role="tabpanel">
                    <div class="card-header">
                        <h5>Unallocated Task</h5>
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
                            <button class="btn btn-primary checked_emp" style="display: none;" onclick="assignTaskCheckedEmployee()"> Requist Tax Declaration </button>
                          <?php if(count((array)($unallocatedTaskemployees)) != 0){ ?>
                        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                            <thead>
                                <tr><th scope="col"></th>
                                    <th scope="col">Employees</th>
                                    <th scope="col">Joining Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($unallocatedTaskemployees as $onboard_emp){
                                    $taskresult = $tasksresultuser[$onboard_emp['user_id']];
                                    $empiddecode = deocdeId('id:'.$onboard_emp['id']);
                                    $dynmonth = date("F",strtotime($onboard_emp['joining_date']))
                                ?>
                                <tr class="showallemp <?php echo $dynclass; ?>">
                                    <td>
                                        <div class="checkbox-fade fade-in-primary m-0" role="group" onchange="assigneTaskChecked()">
                                            <label class="form-label text-auto-hight line-height24 m-0" >
                                                <input aria-label="Checkbox" type="checkbox" value="<?php echo $onboard_emp['id']; ?>" class="check_emp_id" >
                                                <span class="cr float-left m-0">
                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td><a href="index.php?option=COM_VIEW_ONBOARDING_EMPLOYEE_DASHBOARDS&Rid=<?php echo $empiddecode; ?>&FY=<?php echo $finance_tax_year; ?>" target="_blank">
                                         <div class="align-middle">
                                            <?php if($onboard_emp['image'] !=""){?>
                                                <img src="uploads/all/<?php echo $onboard_emp['image']; ?>"  class="img-radius img-40 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round pt-1 f-12 text-center"><?php echo initials($onboard_emp['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73 text-primary">
                                                <h6 class="mb-0"><?php echo $onboard_emp['display_name']; ?></h6>
                                                <p class="text-muted m-b-0"><?php echo $onboard_emp['emp_code']; ?> (<?php echo "FB - ".$_SESSION['users'][$empidwithuserid[$onboard_emp['finance_buddy']]]; ?>)</p>
                                            </div>
                                        </div>
                                       </a>
                                     </td>

                                    <td>
                                        <span class="d-block"><?php echo fromDate($onboard_emp['joining_date']); ?></span>
                                        <?php echo $onboard_emp['date_of_leaving']!=""?"<span class='text-danger'>".fromDate($onboard_emp['joining_date'])."</span>":"-"; ?>
                                    </td>
                                    <td>
                                        <?php if($empidwithuserid[$onboard_emp['finance_buddy']] !=""){ ?>
                                        <button class="btn btn-primary btn_emp" onclick="assignTaskEmployee('<?php echo $onboard_emp['id']; ?>')"> Requist Tax Declaration</button></td>
                                        <?php }else{ echo "-";
} ?>
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
    <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
        <li class="nav-item">
            <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#taskstatustab" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Task Status</a>
        </li>
        <li class="nav-item">
            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#reviewpenddingtab" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Review Pendings  <span class="form-label label label-xs label-danger m-l-10"><?php echo count((array)$action_transaction_activity_schedule);?></span></a>
        </li>
    </ul>
    <div class="tab-content tabs">
        <div class="tab-pane card-body p-0 active" id="taskstatustab" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5>Task Status</h5>
                    <div class="card-header-right"></div>
                </div>
                <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                    <?php  $crm_contacts_chart = array(array('Yet To Start',(int)$yet_to_start_cnt), array('In-Progress',(int)$inprogress_cnt), array('Review',(int)$review_cnt), array('Approved',(int)$approve_cnt), array('Delayed',(int)$delayed_cnt)); ?>
                    <input type="hidden" name="chart_data_contact_chart" id="chart_data_contact_chart" value='<?php echo json_encode((array)$crm_contacts_chart); ?>' >
                    <?php if(count((array)$crm_contacts_chart) =='0'){ ?>
                      <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php }else{ ?>
                      <div id="chart_by_contact" style="width: 100%; height: 400px;"></div>
                    <?php }  ?>
                </div>
            </div>
        </div>
        <div class="tab-pane card-body p-0" id="reviewpenddingtab" role="tabpanel">
            <div class="card">
                <div class="card-header  ">
                    <h5>Review Pendings </h5>
                        <div class="card-header-right table-card-header "><span class="form-label label label-md label-warning float-right"><?php echo count((array)$action_transaction_activity_schedule); ?> </span></div>
                </div>
                <div class="card-body">
                    <div class="custom-scroll" style="height: 500px;">
                        <?php if(count((array)($action_transaction_activity_schedule)) != 0){ ?>
                        <table class="table table-hover m-b-0 register_vendor_filter" style="font-size:13px;" id="categoryfilter">
                            <thead>
                                <tr>
                                    <th scope="col" class="p-2">Task Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($action_transaction_activity_schedule as $taskdetails){
                                    $onboardinguseridres = $onboardinguserid[$taskdetails['transaction_id']];
                                     $empiddecode = deocdeId('id:'.$taskdetails['id']);
                                    ?>
                                <tr>
                                    <td class="w-b-w-s p-2"><a href="index.php?option=COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULES&T1=task_tab&T2=action_task_tab&T3=action_pendding_tab&Rid=<?php echo $empiddecode; ?>" target="_blank"><div class="align-middle">
                                                <?php if($onboardinguseridres['image'] !=""){?>
                                                    <img src="uploads/all/<?php echo $onboardinguseridres['image']; ?>"  class="img-radius img-40 align-top m-r-5" alt="User Profile" onerror="this.src='uploads/all/no-image.jpg';">
                                                <?php } else { ?>
                                                    <span  alt="user" class="img-radius img-40 align-top m-r-5 custom_txt_round pt-1 f-12 text-center"><?php echo initials($onboardinguseridres['display_name']); ?></span>
                                                <?php } ?>
                                                <div class="d-inline-block width73 text-primary">
                                                    <?php
                                                    if($onboardinguseridres['company'] !=""){ ?>
                                                       <h6 class="mb-0 text-primary"><?php echo $onboardinguseridres['company']; ?></h6>
                                                        <p class="text-muted m-b-0"><i class="ti-user f-12"></i> <?php echo $onboardinguseridres['display_name']; ?></p>
                                                    <?php }else{ ?>
                                                        <h6 class="mb-0 text-primary"><i class="ti-user"></i> <?php echo $onboardinguseridres['display_name']; ?></h6>
                                                    <?php } ?>
                                                    <p class="text-muted m-b-0"><?php echo $taskdetails['task_name']; ?></p>
                                                    <p class="text-muted m-b-0 f-12 f-w-600">Request Date : <?php echo fromDate($vendorsres['request_date']); ?></p>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php  }else{ ?>
                        <div class="card-px text-center py-20 mt-2">
                            <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Review Pending  </p>
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
