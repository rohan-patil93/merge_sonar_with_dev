<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if($_POST['filterajax'] == "Y"){
    $current_page = $_POST['current_page'];
    $limit_select = 20;
    $post['admin_type'] = $_SESSION['admin_type'];
    $post['limit_select'] = $limit_select;
    $post['current_page'] = $_POST['current_page'];
     $post['entervendornamefind'] = $_POST['entervendornamefind'];
    $post['user_id'] = $_SESSION['userid'];
    $post['company_id'] = $_SESSION['company_id'];
    $url = API."?mode=getvendoradmindashboardpost&method=post";
    $api = file_post_contents($url, $post, 'update');
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
}
$total_pages = $result['total_pages'];

?> <div class="custom-scroll height-500" >
<?php
if(count((array)($vendors)) != 0){  ?>
    <table class="table table-hover m-b-0 register_vendor_filter f-13">
        <thead>
            <tr>
                <th scope="col" class="p-2" style="width: 35%;">Vendor Details</th>
                <th scope="col" class="p-2" style="width: 10%;">Satuse</th>
                <th scope="col" class="text-end p-2" style="width: 10%;">Total</th>
                <th scope="col" class="text-end p-2" style="width: 10%;">To Start</th>
                <th scope="col" class="text-end p-2" style="width: 10%;">Inprogress</th>
                <th scope="col" class="text-end p-2" style="width: 10%;">Review</th>
                <th scope="col" class="text-end p-2" style="width: 10%;">Completed</th>

            </tr>
        </thead>
        <tbody id="registervendorfilter">
            <?php foreach($vendors as $onboard_emp){
                $taskresult = $tasksresultemp[$onboard_emp['id']];
                $empiddecode = deocdeId('id:'.$onboard_emp['id']);
                $allaproveprofile = 0;
                $verification_profile_users = json_decode($onboard_emp['verification_profile_users'],1);
                if(count((array)$verification_profile_users) != 0){
                    foreach($verification_profile_users as $uidkey=>$vpu){
                        if($vpu['status'] == 'Y'){
                        }elseif($vpu['status'] == 'N'){
                            $allaproveprofile = 1;
                        }else{
                            $allaproveprofile = 1;
                        }
                    }
                }else{
                    $allaproveprofile = 1;
                }
                $allaproveprofile = $taskresult['total_task_cnt'] = $taskresult['approve_cnt'] = 0;
                ?>
                <tr class="showallemp <?php echo $dynclass; ?>">
                    <td class="p-2" style="width: 50%;">
                        <div class="align-middle">
                            <?php if($onboard_emp['image'] !=""){?>
                                <img src="uploads/all/<?php echo $onboard_emp['image']; ?>"  class="img-radius img-40 align-top m-r-5" onerror="this.src='uploads/all/no-image.jpg';" alt="Attachment Preview">
                            <?php } else { ?>
                                <span  alt="user" class="img-radius img-40 align-top m-r-5 custom_txt_round pt-1 f-12 text-center"><?php echo initials($onboard_emp['display_name']); ?></span>
                            <?php } ?>
                            <div class="d-inline-block width73 text-primary">
                                <a href="index.php?option=COM_INSERT_VENDOR_REGISTERS&VTYPE=VR&Rid=<?php echo $empiddecode; ?>" target="_blank" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open Registration Vendor Details"><h6 class="mb-0 text-primary"><?php
                                if($onboard_emp['company'] !=""){ ?>
                                    <h6 class="mb-0 text-primary"><?php echo $onboard_emp['company']; ?></h6>
                                    <p class="text-muted m-b-0"><i class="ti-user f-12"></i> <?php echo $onboard_emp['display_name']; ?></p>
                                <?php }else{ ?>
                                    <h6 class="mb-0 text-primary"><i class="ti-user"></i> <?php echo $onboard_emp['display_name']; ?></h6>
                                    <?php } ?></a>
                                    <?php $allaproveprofile = 0;
                                    if ($onboard_emp['send_verification_profile_status'] == "Y") { ?>
                                        <?php
                                        echo "<div class=' mt-2'>";
                                        foreach ($verification_profile_users as $uidkey => $vpu) {
                                            $bgcolor = "";
                                            $label_title = $_SESSION['users'][$uidkey] . " : ";
                                            if ($vpu['status'] == 'Y') {
                                                $label_title .=  " recommend";
                                                $bgcolor = "bg-success";
                                            } elseif ($vpu['status'] == 'N') {
                                                $label_title .=  " Object";
                                                $bgcolor = "bg-danger";
                                                $allaproveprofile = 1;
                                            } else {
                                                $label_title .=  " Pending";
                                                $allaproveprofile = 1;
                                            }
                                            ?>
                                            <span alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round f-12 text-center <?php echo $bgcolor; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $label_title; ?>"><?php echo initials($_SESSION['users'][$uidkey]); ?></span>
                                        <?php }
                                        echo "</div>";
                                    } ?>

                                    <?php
                                    if (($onboard_emp['vendor_id'] == "" && ($_SESSION['admin_type'] == '1' || $onboard_emp['originator'] == $_SESSION['userid']) && $taskresult['total_task_cnt'] == $taskresult['approve_cnt'] && $onboard_emp['send_verification_profile_status'] == "Y" && $allaproveprofile == '0' && $onboard_emp['vendor_type_id'] == "1")) {

                                        ?>
                                        <button class="btn btn-info waves-effect waves-light btn-mini createVendorRegisterToVendor" data-vendor-id="<?php echo $onboard_emp['id']; ?>"><i class="ti ti-user"></i> Create Vendor</button>
                                    <?php } ?>

                                </div>
                            </div>
                        </td>
                        <td class="p-2" style="width: 10%;">
                            <span class="form-label label label-md label-warning" style="color: <?php echo $stausresult[$onboard_emp['current_status']]['color']; ?>;    background:  <?php echo $stausresult[$onboard_emp['current_status']]['background_color']; ?>; border-color: <?php echo $stausresult[$onboard_emp['current_status']]['background_color']; ?>;"><?php echo $stausresult[$onboard_emp['current_status']]['name']; ?> </span>
                        </td>
                        <td style="text-align: right; width: 10%;" class="p-2"><?php echo customNanAndEmptyValidation($taskresult['total_task_cnt']); ?></td>
                        <td style="text-align: right; width: 10%;" class="p-2"><?php echo customNanAndEmptyValidation($taskresult['yet_to_start_cnt']); ?></td>
                        <td style="text-align: right; width: 10%;" class="p-2"><?php echo customNanAndEmptyValidation($taskresult['inprogress_cnt']); ?></td>
                        <td style="text-align: right; width: 10%;" class="p-2"><?php echo customNanAndEmptyValidation($taskresult['review_cnt']); ?></td>
                        <td style="text-align: right; width: 10%;" class="p-2"><?php echo customNanAndEmptyValidation($taskresult['approve_cnt']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php  }else{ ?>
        <div class="card-px text-center py-20 mt-2">
            <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Registration Vendors </p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px mx-height-300" alt="" src="assets/images/work.png">
            </div>
        </div>
    <?php } ?>
 </div>
 <div class="mt-5   custom-scroll height-75" >
    <nav aria-label="Page navigation example p-2" >
        <ul class="pagination">
          <li class="page-item <?php echo ($current_page =="1" || $current_page =="")?"disabled":""; ?>">
              <a class="page-link dynamicVendorAdminDashboard"  data-current_status = "" data-current_page="<?php echo $current_page-1; ?>" href="#!" tabindex="-1" >Previous</a>
          </li>
        <?php
          for ($i = 1; $i <= $total_pages; $i++){
              if ($i == $current_page){ ?>
                  <li class="page-item active"><a class="page-link active dynamicVendorAdminDashboard"  data-current_status = "" data-current_page="<?php echo $i; ?>"   href="#!" ><?php echo $i; ?></a></li>
            <?php }else{ ?>
                  <li class="page-item"><a class="page-link dynamicVendorAdminDashboard"  data-current_status = "" data-current_page="<?php echo $i; ?>"  href="#!"><?php echo $i; ?></a></li>
            <?php } ?>
            <?php } ?>

            <li class="page-item <?php echo ($total_pages <= $current_page)?"disabled":""; ?>">
                <a class="page-link dynamicVendorAdminDashboard"  data-current_status = "" data-current_page="<?php echo $current_page+1; ?>"  href="#!" tabindex="-1">Next</a>
            </li>
        </ul>
    </nav>
</div>
