<?php
$startdate = date("Y-m-01");
$enddate =  date("Y-m-t");
$currentmonth = date("F");
$previousmonth = date("F",strtotime($startdate));
$nextmonth = date("F",strtotime($enddate));
$url = API."?mode=getvendoradmindashboard&admin_type=".$_SESSION['admin_type']."&startdate=".$startdate."&enddate=".$enddate."&admin_type=".$_SESSION['admin_type']."&limit_select=20&current_page=1";
$api = callAPI($url);
$result = json_decode($api,1);
$vendors = $result['vendors'];
$vendor_registers = $result['vendor_registers'];
$msme_confirmation_logs = $result['msme_confirmation_logs'];
$action_transaction_activity_schedule = $result['action_transaction_activity_schedule'];
$transaction_activity_scheduleres = $result['transaction_activity_scheduleres'];
$vendor_registration_requests = $result['vendor_registration_requests'];
$monthlycnt = $result['monthlycnt'];
$transaction_vendor_cnt = $result['transaction_vendor_cnt'];
$tasksresultemp = array();
$tasksresultuser = array();
$reviewpendinguser = $unallocatetaskemployee = array();
$totaltask = 0;
$totalinprogresstask = 0;
$totalapprovedtask = 0;
$totalreviewpendingtask =$totalyettostartcnttask=$totaldelayedcnttask =  0;
foreach($transaction_activity_scheduleres as $tr){
    $tasksresultuser[$tr['assigned_to']] = $tr; // assigned_to is user id
    $tasksresultemp[$tr['transaction_id']] = $tr;// transaction_id is employee id
    $totaltask =$totaltask+$tr['total_task_cnt'];
    $totalinprogresstask =$totalinprogresstask+$tr['inprogress_cnt'];
    $totalreviewpendingtask =$totalreviewpendingtask+$tr['review_cnt'];
    $totalapprovedtask =$totalapprovedtask+$tr['approve_cnt'];
    $totalyettostartcnttask =$totalyettostartcnttask+$tr['yet_to_start_cnt'];
    $totaldelayedcnttask = $totaldelayedcnttask+$tr['delayed_cnt'];
}
$onboardinguserid = array();
foreach($vendors as $onboardemployees){
    $onboardinguserid[$onboardemployees['id']] = $onboardemployees;

    $taskresults = $tasksresultuser[$onboardemployees['user_id']];
    if($taskresults['total_task_cnt'] !="" && $taskresults['total_task_cnt'] !="0"){
        $onboard_employees[] = $onboardemployees;
    }else{
        $unallocatetaskemployee[] = $onboardemployees;
    }
}
$totaltaskper = $totaltask !=0?round(($totalapprovedtask/$totaltask)*100):"0";
$stausresult = ViewStatusData();
?>
<script type="text/javascript" src="../assets/js/custom_view_vendor_admin_dashboard.js"></script>

<link rel="stylesheet" type="text/css" href="../assets/css/custom_insert_vendor_display_fields.css">

<div class="card product-progress-card mb-3">
    <div class="card-block">
        <div class="row ">
            <div class="col-lg-5">
                <div class="d-flex">
                <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                    <?php if($_SESSION['user']['user_photo']!=""){ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['user']['user_photo']; ?>" class="img-120 " onerror="this.src='uploads/all/no-image.jpg';" alt="Details"/>
                    </div>

                  <?php }else{ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="img-120 " onerror="this.src='uploads/all/no-image.jpg';" alt="Details"/>
                    </div>
                  <?php } ?>
                    <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                          <span class="f-12 f-w-600">Task </span>
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
            <div class="col-lg-7" >
            <div class="col-lg-12" >
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?option=COM_LIST_VENDOR_REGISTERS">
                            <div class="card social-card">
                                <div class="card-body text-center p-0">
                                    <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                    <h3 class="text-facebook f-w-700"><?php echo $result['total_vendors']!=""?$result['total_vendors']:"0"; ?></h3>
                                    <p>Total Vendors</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-6 ">
                        <a href="index.php?option=COM_LIST_VENDOR_REGISTERS">
                            <div class="card social-card">
                                <div class="card-body text-center p-0">
                                    <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                    <h3 class="text-facebook f-w-700"><?php echo $transaction_vendor_cnt['active_cnt']!=""?$transaction_vendor_cnt['active_cnt']:0; ?></h3>
                                    <p>Active Vendors</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?option=COM_LIST_VENDOR_REGISTERS">
                            <div class="card social-card">
                                <div class="card-body text-center p-0">
                                    <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                    <h3 class="text-facebook f-w-700"><?php echo $transaction_vendor_cnt['inprogress_cnt']!=""?$transaction_vendor_cnt['inprogress_cnt']:"0"; ?></h3>
                                    <p>In-Progress Vendors</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?option=COM_LIST_VENDOR_REGISTERS">
                            <div class="card social-card">
                                <div class="card-body text-center p-0">
                                    <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                    <h3 class="text-facebook f-w-700"><?php echo $transaction_vendor_cnt['approved_cnt']!=""?$transaction_vendor_cnt['approved_cnt']:"0"; ?></h3>
                                    <p>Approved Vendors</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="row mt-4">
                     <div class="col-1 text-center"></div>
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
                    <div class="col-1 text-center"></div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<?php if($_SESSION['admin_type'] == '1'){ ?>
    <div class="row mb-3">
        <div class="col-lg-12 text-left"></div>
        <div class="col-lg-12 text-right">

            <a class="btn btn-warning waves-effect waves-light" href="index.php?option=com_logout" target="_blank"><i class="ti-plus"></i> Document Verification</a>


            <a class="btn btn-info waves-effect waves-light" href="index?option=COM_LIST_APPLICATION_USER_INVITATIONS"><i class="ti-plus"></i> Invite User</a>

            <a class="btn btn-success waves-effect waves-light" href="index?option=COM_INSERT_VENDOR_REGISTERS&transaction_table=vendor_registers"><i class="ti-plus"></i> Create Vendor</a>
            <a class="btn btn-primary waves-effect waves-light" href="index?option=COM_LIST_PROCESS_TASK_SETTINGS"><i class="ti-plus"></i> Task</a>
        </div>
    </div>
<?php } ?>
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header  ">
                <h5>Registration Requests</h5>
                <div class="card-header-right table-card-header ">
                    <span class="form-label label label-md label-warning float-right"><?php echo count((array)$vendor_registration_requests); ?> </span>
                </div>
            </div>
            <div class="card-body">
                <div class="custom-scroll height-500">
                <?php if(count((array)($vendor_registration_requests)) != 0){ ?>
                <table class="table table-hover m-b-0 register_vendor_filter f-13">
                    <thead>
                        <tr>
                            <th scope="col" class="p-2">Vendor Registration Requests Details</th>
                            <th scope="col" class="p-2"></th>
                        </tr>
                    </thead>
                    <tbody id="vendorfilter" >
                        <?php foreach($vendor_registration_requests as $vendorsres){
                            $empiddecode = deocdeId('id:'.$vendorsres['id']);
                            ?>
                        <tr class="showallemp">
                            <td class="p-2">
                                 <div class="align-middle">
                                    <span  alt="user" class="img-radius img-40 align-top m-r-5 custom_txt_round pt-1 f-12 text-center"><?php echo initials($vendorsres['company_name']); ?></span>
                                    <div class="d-inline-block width73 text-primary">
                                            <?php
                                            if($vendorsres['company_name'] !=""){ ?>
                                                <h6 class="mb-0 text-primary"><?php echo $vendorsres['company_name']; ?></h6>
                                                <p class="text-muted m-b-0"><i class="f-12 ti-user"></i> <?php echo $vendorsres['display_name']; ?></p>
                                            <?php }else{ ?>
                                                <h6 class="mb-0 text-primary  mt-2"><i class="ti-user"></i> <?php echo $vendorsres['display_name']; ?></h6>
                                            <?php } ?>
                                        <p class="text-muted m-b-0  mt-2"><?php echo $vendorsres['user_email']; ?></p>
                                        <p class="text-muted m-b-0 f-12 f-w-600 ">Requested Date : <?php echo $vendorsres['request_datetime'] !=""?fromDate($vendorsres['request_datetime']):"Pending"; ?></p>
                                    </div>
                                </div>
                                <div class=" mt-2 text-end">


                                    <a href="index?option=COM_INSERT_VENDOR_REGISTRATION_REQUESTS&Rid=<?php echo deocdeId('id:'.$vendorsres['id']); ?>" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="<?php if($vendorsres['share_pdf'] == "Y"){ ?>View Registration Details<?php
}else{  ?>Edit Registration Details<?php
} ?>"><button class="btn btn-warning waves-effect waves-light btn-mini">
 <?php if($vendorsres['share_pdf'] == "Y"){ ?>View<?php
}else{  ?>Edit<?php
} ?></button></a>
                                    <?php if($_SESSION['admin_type'] == 1){ ?>
                                        <a href="index?option=COM_INSERT_VENDOR_REGISTERS&VTYPE=VR&VRRid=<?php echo deocdeId('id:'.$vendorsres['id']); ?>&transaction_table=vendor_registers" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Register Vendor"><button class="btn btn-success waves-effect waves-light btn-mini"><i class="ti-pencil-alt"></i> Register</button></a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php  }else{ ?>
                <div class="card-px text-center py-20 mt-2">
                    <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Vendors </p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
                    </div>
                </div>
                <?php } ?>
            </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-8">
                <ul class="nav md-tabs infisuite_tab mb-2">

                    <li class="nav-item" role="presentation">
                        <a class="nav-link f-w-600 f-12 active"  data-clone="quotations" data-bs-toggle="tab" href="#vendor-tab" role="tab" aria-selected="false" tabindex="-1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Vendors &nbsp; <span class="form-label label label-md label-success float-right"><?php echo $result['total_vendors']!=""?$result['total_vendors']:"0"; ?> </span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link f-w-600 f-12 masmVerificationTab"  data-bs-toggle="tab" href="#masm-verification-tab" role="tab" aria-selected="false" tabindex="-1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending MSME Req &nbsp; <span class="form-label label label-md label-success float-right"><?php echo count((array)$msme_confirmation_logs); ?> </span></a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4 text-right">
                <a class="btn btn-primary waves-effect waves-light" href="index?option=COM_LIST_VENDORS_AND_VENDOR_REGISTERS_REPORTS"><i class="ti-receipt"></i> Report </a>
            </div>
        </div>
        <div class="tab-content tabs">
            <div class="tab-pane card-body p-0 active" id="vendor-tab" role="tabpanel">
                <div class="card">
                    <div class="card-header  ">
                        <h5>Vendors &nbsp; <span class="form-label label label-md label-warning float-right"><?php echo $result['total_vendors']!=""?$result['total_vendors']:"0"; ?> </span></h5>
                        <div class="card-header-right table-card-header ">
                            <div class="input-group input-group-button mb-0">
                                <input type="text" id="entervendornamefind"  class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                                <span class="input-group-text addon-style">
                                    <span class=""><span class="ti-search text-white mt-2"></span></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="dyanmictableshow">
                        <?php include_once 'views/custom_view_vendor_admin_dashboard_ajax.php'; ?>

                    </div>
                </div>
            </div>
            <div class="tab-pane card-body p-0 " id="masm-verification-tab" role="tabpanel">

                     <div class="card">
                    <div class="card-body">
                        <div class="custom-scroll height-500">
                            <?php if(count((array)($msme_confirmation_logs)) != 0){ ?>
                            <table class="table table-hover m-b-0 register_vendor_filter f-13">
                                <thead>
                                    <tr>
                                        <th scope="col" class="p-2">Vendor Name</th>
                                        <th scope="col" class="p-2"></th>
                                    </tr>
                                </thead>
                                <tbody id="vendorfilter_list">
                                    <?php foreach($msme_confirmation_logs as $msmlog){

                                        if($msmlog['transaction_table'] ="vendor_registers"){
                                            $msmeoption = "COM_INSERT_VENDOR_REGISTERS";
                                            $msmecompanyname = $msmlog['vendor_register_name'];
                                        }else{
                                            $msmeoption = "COM_INSERT_VENDORS";
                                            $msmecompanyname = $msmlog['vendor_name'];
                                        }
                                        $empiddecode = deocdeId('id:'.$msmlog['transaction_id']);

                                        ?>
                                    <tr class="showallemp">
                                        <td class="p-2">
                                             <div class="align-middle">
                                                    <span  alt="user" class="img-radius img-40 align-top m-r-5 custom_txt_round pt-1 f-12 text-center"><?php echo initials($msmecompanyname); ?></span>
                                                <div class="d-inline-block width73 text-primary">
                                                    <a href="index.php?option=<?php echo $msmeoption; ?>&Rid=<?php echo $empiddecode; ?>" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open Vendor Details">
                                                        <h6 class="mb-0 text-primary"><?php echo $msmecompanyname; ?></h6>
                                                    </a>
                                                    <p class="text-muted m-b-0 f-12 f-w-600">Request date : <?php echo fromDate($msmlog['created_date']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <?php  }else{ ?>
                            <div class="card-px text-center py-20 mt-2">
                                <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Vendors </p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header  ">
                <h5>Review Pending </h5>
                <div class="card-header-right table-card-header ">
                    <span class="form-label label label-md label-warning float-right">
                        <?php echo count((array)$action_transaction_activity_schedule);?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="custom-scroll height-500">
                    <?php if(count((array)($action_transaction_activity_schedule)) != 0){ ?>
                    <table class="table table-hover m-b-0 register_vendor_filter f-13" id="categoryfilter">
                        <thead>
                            <tr>
                                <th scope="col" class="p-2">Vendor Registration Details</th>
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
                                                 <img src="uploads/all/<?php echo $onboardinguseridres['image']; ?>"  class="img-radius img-40 align-top m-r-5" onerror="this.src='uploads/all/no-image.jpg';" alt="User Profile">
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
                            <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

