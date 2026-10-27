<?php
$_SESSION['apply_js']['multiselest'] = '1';
$Rid = encodeId($_GET['Rid']);
$rid = explode(":", $Rid);
$rid = $rid['1'];
if (isset($_POST['submitlog'])) {
    $filedetails = array();
    if ($_FILES['files']['error'][0] != '4') {
        foreach ($_FILES['files']['name'] as $key => $files) {
            $filedetailsd['originalname'] = basename($_FILES['files']['name'][$key]);
            $filedetailsd['filesize'] = basename($_FILES['files']['size'][$key]);
            $name = time() . basename($_FILES['files']['name'][$key]);
            if (move_uploaded_file($_FILES["files"]["tmp_name"][$key], "uploads/attachments/" . $name)) {
                $filedetailsd['filename'] = $name;
            }
            $filedetails[] = $filedetailsd;
        }
    }
    $_POST['fila'] = $filedetails;
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API . "?mode=insertprojecttasklogs&method=post";
    $response = file_post_contents($url, $_POST);
}
$url4 = "http://localhost/infisuite/ticketing_two/api.php?method=get&mode=getviewticketdetails&id={$rid}&email={$_SESSION['email']}";
$ch2 = curl_init($url4);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
$tasklists = curl_exec($ch2);
curl_close($ch2);
$tasklist = json_decode($tasklists, true);
$tasklogdetails = $tasklist['logs']['logsdetails'];
$statusList = $tasklist['table_statusList'];
$application_roles = $tasklist['application_roles'];
$stausresultwithcolor = ViewStatusData();
foreach ($statusList as $statusLists) {
    $stausresult[$statusLists['status']] = $statusLists;
}
$priority_categories = $tasklist['priority_categories'];
$users_list = $tasklist['users_list'];
$transactiontimelog = $tasklist['transaction_time_log'];
$clientseveritymatrix = $tasklist['client_severity_matrix'];
$publish_message_field = array("client_id" => "Initiator", "follower_ids" => "Followers", "assigner_id" => "Assigner", "assignee_id" => "Assignee");
?>
<script>
    function addfollowers() {
        $('#modal_follower').modal("show");
    }
    function replayMessage(id, message, message_type) {
        $('.replay_div').show("slow");
        $("#replay_message").text(message);
        $("#replay_id").val(id);
        $("#replay_message_type").val(message_type);
        $(".private_check").prop('disabled', true);
        $("#checkbosshowhide").css("opacity", "0.4");

    }
    function replayMessageRemoved() {
        $('.replay_div').hide("slow");
        $("#replay_message").text(' ');
        $("#replay_id").val('0');
        $("#replay_message_type").val(' ');
        $(".private_check").prop('disabled', false);
        $("#checkbosshowhide").css("opacity", "1");
    }
    function privateMessage() {
        if ($(".private_check").is(":checked")) {
            var html = '<div class="input-group input-group-button"><select aria-label="Message Type[]" class="form-control form-select js-example-placeholder-multiple" name="message_type[]" required multiple><?php foreach ($publish_message_field as $pmfkey => $pmf) { ?><option value="<?php echo $pmfkey; ?>"><?php echo $pmf; ?></option> <?php
} ?></select></div>';
            $("#private_message_select").html(html);
            $(".js-example-placeholder-multiple").select2();
        } else {
            $("#private_message_select").html('');
        }
    }
</script>
<div class="page-body">
    <div class="card table-card ">
        <div class="card-header table-card-header ">
            <h5>Tickets</h5>
            <div class="card-header-right table-card-header ">
                <?php
                $ticketcurrentstatus = $stausresult[$tasklist['tasklist']['current_status']];
                $stausresultwithcolors = $stausresultwithcolor[$tasklist['tasklist']['current_status']];
                ?>
                <span class="form-label label label-md"
                    style="background-color: <?php echo $stausresultwithcolors['background_color'] ?>; color: <?php echo $stausresultwithcolors['color'] ?>; "><?php echo $ticketcurrentstatus['name']; ?></span>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body ">
            <div class="form-group row ml-20 mr-20">
                <div class="row mb-3">
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-6 col-12">
                    <div class="">
                        <?php
                        $client_id = $tasklist['tasklist']['client_id'];
                        $userdetails = $tasklist['users_list'][$client_id];
                        if ($userdetails['user_photo'] != "") { ?>
                            <img src="uploads/all/<?php echo $userdetails['user_photo']; ?>"
                                class="img-radius img-50 align-top " onerror="this.src='uploads/all/no-image.jpg';" alt="User Profile">
                        <?php } else { ?>
                            <span alt="user"
                                class="img-radius img-50 align-top bg-primary custom_txt_round p-2 f-12 text-center"><?php echo initials($userdetails['display_name']); ?></span>
                        <?php } ?>
                        <div class="d-inline-block">
                            <span class="f-w-700">Raised By</span>
                            <a class="text-dark">
                                <h6 class="mt-1"><?php echo $userdetails['display_name']; ?></h6>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-6 col-12">
                    <span class="f-w-700">Ticket Number </span> </br>
                    <span
                        class="form-label label label-md bg-primary"><?php echo $tasklist['tasklist']['ticket_id']; ?></span>
                </div>
                <div class="form-group col-lg-5 col-md-5 col-sm-9 col-12 text-left">
                    <span class="f-w-700">Planned </span></br>
                    <span class="form-label badge badge-inverse-danger">
                        <p class="mb-1">Response</p>
                        <?php $response_details = $tasklist['tasklist']['request_completion_date'] != "" ? $tasklist['tasklist']['request_completion_date'] : $tasklist['tasklist']['sla_response'];
                        echo $response_details != "" ? $response_details : " - "; ?>
                    </span>
                    <span class="form-label badge badge-inverse-primary">
                        <p class="mb-1">Resolution</p>
                        <?php $resolition_details = $tasklist['tasklist']['planed_completion_date'] != "" ? $tasklist['tasklist']['planed_completion_date'] : $tasklist['tasklist']['sla_resolution'];
                        echo $resolition_details != "" ? $resolition_details : " - ";
                        ?>
                    </span>
                    <span class="form-label badge badge-inverse-success">
                        <p class="mb-1">Completion</p>
                        <?php $completion_details = $tasklist['tasklist']['actual_completion_date'] != "" ? $tasklist['tasklist']['actual_completion_date'] : $tasklist['tasklist']['sla_closure'];
                        echo $completion_details != "" ? $completion_details : "-"; ?>
                    </span>
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-3 col-12">
                    <span class="f-w-700">Followers </span> &nbsp;&nbsp;
                    <?php if (defined('TICKET_ASSIGNER_ROLE') && !empty($_SESSION['role_ids']) && in_array(TICKET_ASSIGNER_ROLE, (array) $_SESSION['role_ids'])) { ?>
                        <button type="button" class="btn p-0 border-0 bg-transparent form-label label label-md label-success" onclick="addfollowers()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            Add &nbsp;&nbsp;<i class="fa fa-user-plus"></i>
                        </button>
                    <?php } ?>
                    <div class="symbol-group symbol-hover">
                        <div class="symbol-group symbol-hove ml-2r">
                            <?php if ($tasklist['tasklist']['follower_ids'] != "") {
                                $followlist = explode(',', $tasklist['tasklist']['follower_ids']);
                                $mk = 0;
                                foreach ($followlist as $member) { ?>
                                    <div class="symbol symbol-35 symbol-circle" data-bs-toggle="tooltip" data-placement="bottom"
                                        title="" data-bs-original-title="<?php echo $_SESSION['users'][$member]; ?>">
                                        <span class="symbol-label font-weight-bold">
                                            <?php echo initials($_SESSION['users'][$member]); ?></span>
                                    </div>
                                    <?php $mk++;
                                    if ($mk == '3') {
                                        break;
                                    }
                                } ?>
                                <?php if (count((array) $followlist) > 3) { ?>
                                    <span
                                        class="symbol-label font-weight-bold">+<?php echo count((array) $followlist) - 3; ?></span>
                                <?php }
                            } else { ?>
                                <p class="text-mute f-12">No Follower</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                    <div class="card proj-t-card">
                        <div class="card-header">
                            <h5><?php echo $tasklist['tasklist']['task_name']; ?></h5>
                            <div class="card-header-right">
                                <span
                                    class="form-label label label-md <?php echo $priority_categories[$tasklist['tasklist']['priority']]['background_color']; ?>"><?php echo $priority_categories[$tasklist['tasklist']['priority']]['name']; ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="f-14 f-w-600"><?php echo $tasklist['tasklist']['description']; ?></p>
                            <?php if (count((array) $tasklist['application_attachments']) != '0') { ?>
                                <div class="row">
                                    <span class="form-label col-form-label d-block mt-2"><i class="ti-clip"></i> Attchment
                                        : </span>
                                    <?php $im = 1;
                                    foreach ($tasklist['application_attachments'] as $attchment) { ?>
                                        <a href="uploads/attachments/<?php echo $attchment['actual_file_name']; ?>"
                                            download><?php echo $im . ") " . $attchment['file_name']; ?></a></br>
                                        <?php $im++;
                                    } ?>
                                </div>
                            <?php } ?>
                            <?php if ($tasklist['tasklist']['resolution_description'] != "") { ?>
                                <div class="row">
                                    <h5 class="f-w-600"> Fixing Ticket</h5>
                                    <p class="mt-2">
                                        <?php echo $tasklist['tasklist']['resolution_description']; ?>

                                    </p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
                $inprogrestime = $clientwaitingtime = 0;
                $inprogressdays = $inprogresshours = $inprogresminutes = $inprogresseconds = 0;
                $cleintsdays = $cleintshours = $cleintminutes = $cleintseconds = 0;
                $privestime = '';
                foreach (array_reverse($transactiontimelog) as $transactiontimelogs) {
                    if ($privestime != "") {
                        $transactionstartdatesta = new DateTime($privestime);
                        $transactionenddatesta = $transactionstartdatesta->diff(new DateTime($transactiontimelogs['response_time']));
                        if ($client_id == $privesuserid) {
                            $cleintsdays += $transactionenddatesta->d;
                            $cleintshours += $transactionenddatesta->h;
                            $cleintminutes += $transactionenddatesta->i;
                            $cleintseconds += $transactionenddatesta->s;
                        }
                        $inprogressdays += $transactionenddatesta->d;
                        $inprogresshours += $transactionenddatesta->h;
                        $inprogresminutes += $transactionenddatesta->i;
                        $inprogresseconds += $transactionenddatesta->s;
                    }
                    $privestime = $transactiontimelogs['response_time'];
                    $privesuserid = $transactiontimelogs['user_id'];
                }
                $inprogrestime = $inprogressdays != 0 ? round($inprogressdays * 24 * 60) : 0;
                $inprogrestime += $inprogresshours != 0 ? round($inprogresshours * 60) : 0;
                $inprogrestime += $inprogresminutes != 0 ? $inprogresminutes : 0;
                $inprogrestime += $inprogresseconds != 0 ? round($inprogresseconds / 60) : 0;
                $clientwaitingtime = $cleintsdays != 0 ? round($cleintsdays * 24 * 60) : 0;
                $clientwaitingtime += $cleintshours != 0 ? round($cleintshours * 60) : 0;
                $clientwaitingtime += $cleintminutes != 0 ? $cleintminutes : 0;
                $clientwaitingtime += $cleintseconds != 0 ? round($cleintseconds / 60) : 0;
                ?>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                    <div class="card latest-update-card">
                        <div class="card-header">
                            <h5>Activity</h5>
                        </div>
                        <div class="card-block">
                            <div class="mt-3 mb-2  text-center">
                                <span class="form-label badge badge-inverse-primary text-center">Total Time </br>
                                    <?php echo gmdate("H:i", ($inprogrestime) * 60); ?></span>
                                <span class="form-label badge badge-inverse-danger text-center">Waiting
                                    Time</br><?php echo gmdate("H:i", ($clientwaitingtime * 60)); ?></span>
                                <span class="form-label badge badge-inverse-success text-success">Fixing
                                    Time</br><?php echo gmdate("H:i", (($inprogrestime - $clientwaitingtime) * 60)); ?></span>
                            </div>
                            <div style="height: 480px;">
                                <?php if (count((array) $transactiontimelog) != '0') { ?>
                                    <div class="latest-update-box custom-scroll" style="height: 480px;">
                                        <?php foreach ($transactiontimelog as $transactiontimelogs) {
                                            $ticketcurrentstatus = $stausresult[$transactiontimelogs['response_status']];
                                            $stausresultwithcolors = $stausresultwithcolor[$transactiontimelogs['response_status']];
                                            if ($transactiontimelogs['user_id'] == "" && $transactiontimelogs['response_role'] == "") {
                                                $userheading = $ticketcurrentstatus['name'];
                                            } elseif ($transactiontimelogs['user_id'] == "") {
                                                $userheading = $application_roles[$transactiontimelogs['response_role']]['display_name'];
                                            } else {
                                                $userheading = $_SESSION['users'][$transactiontimelogs['user_id']];
                                            }
                                            ?>
                                            <div class="row p-t-15 p-b-15">
                                                <div class="col-auto text-end update-meta p-r-0">
                                                    <i class="b-primary update-icon ring"></i>
                                                </div>
                                                <div class="col p-l-5">
                                                    <a href="#!">
                                                        <h6><?php echo $userheading; ?>
                                                            <span class="form-label label label-sm float-right"
                                                                style="background-color: <?php echo $stausresultwithcolors['background_color'] ?>; color: <?php echo $stausresultwithcolors['color'] ?>; "><?php echo $ticketcurrentstatus['name']; ?></span>
                                                        </h6>
                                                    </a>
                                                    <p class="text-muted m-b-0">
                                                        <?php echo $transactiontimelogs['response_time']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="card-px text-center py-20">
                                        <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No
                                            Activity </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png"
                                                style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                    <div class="card chat-card">
                        <div class="card-header">
                            <h5>Ticket logs</h5>
                            <div class="card-header-right">
                            </div>
                        </div>
                        <div class="card-block p-2">
                            <div class="row " style=" height: 400px;">
                                <?php if (count((array) $tasklogdetails) != '0') { ?>
                                    <div class="custom-scroll" style=" height: 400px;">
                                        <?php foreach ($tasklogdetails as $ky => $datares) {
                                            $message_type = json_decode($datares['message_type'], 1);
                                            $showornot = '1';
                                            if ($message_type['message_type'] != "0") {
                                                if (in_array($_SESSION['userid'], (array) $message_type['user_id']) || $datares['user_id'] == $_SESSION['userid']) {
                                                    $showornot = '0';
                                                }
                                            } else {
                                                $showornot = '0';
                                            }
                                            if ($showornot == '0') {
                                                $empdeails = $tasklist['users_list'][$datares['user_id']];
                                                $replay_msg = "";
                                                if ($datares['parent_id'] != '0') {
                                                    $replay_details = $tasklogdetails[$datares['parent_id']];
                                                    $replay_msg = '<div class="msg p-3 mt-2"><p class="m-b-0">' . $replay_details['description'] . '</p></div>';
                                                }
                                                $datetime = date('d-m-Y H:i a', strtotime($datares['response_time']));
                                                if ($datares['user_id'] == $_SESSION['userid']) {
                                                    ?>
                                                    <div class="row m-b-20 send-chat">
                                                        <div class="col">
                                                            <div class="msg">
                                                                <i class="fa fa-mail-forward float-left cursor-pointer"
                                                                    data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                                    title="Replay"
                                                                    onclick="replayMessage('<?php echo $datares['id']; ?>','<?php echo $datares['description']; ?>','<?php echo urlencode($datares['message_type']); ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                                                <p class="m-b-0"><?php echo $datares['description']; ?></p>
                                                                <?php
                                                                if (count((array) $tasklist['logs']['attachment'][$ky]) != '0') { ?>
                                                                    <span class="form-label col-form-label d-block"><i class="ti-clip"></i>
                                                                        Attchment : </span>
                                                                    <?php $im = 1;
                                                                    foreach ($tasklist['logs']['attachment'][$ky] as $attchmentt) { ?>
                                                                        <a href="uploads/attachments/<?php echo $attchmentt['actual_file_name']; ?>"
                                                                            download><?php echo $im . ") " . $attchmentt['file_name']; ?></a></br>
                                                                        <?php $im++;
                                                                    }
                                                                } ?>
                                                            </div>
                                                            <p class="text-muted m-b-0 f-12"><i
                                                                    class="fa fa-clock-o m-r-10"></i><?php echo $datetime; ?></p>
                                                        </div>
                                                        <div class="col-auto p-l-0">
                                                            <?php if ($empdeails['user_photo'] != "") { ?>
                                                                <img src="uploads/all/<?php echo $empdeails['user_photo']; ?>"
                                                                    class="img-radius img-40" onerror="this.src='uploads/all/no-image.jpg';" alt="User Profile">
                                                            <?php } else { ?>
                                                                <span alt="user"
                                                                    class="img-radius img-40 p-10"><?php echo initials($empdeails['display_name']); ?></span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="row m-b-20 received-chat">
                                                        <div class="col-auto p-r-0">
                                                            <?php if ($empdeails['user_photo'] != "") { ?>
                                                                <img src="uploads/all/<?php echo $empdeails['user_photo']; ?>"
                                                                    class="img-radius img-40" onerror="this.src='uploads/all/no-image.jpg';" alt="User Profile">
                                                            <?php } else { ?>
                                                                <span alt="user"
                                                                    class="img-radius img-40 p-10"><?php echo initials($empdeails['display_name']); ?></span>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="col">
                                                            <div class="msg">
                                                                <i class="fa fa-mail-forward float-right cursor-pointer"
                                                                    data-container="body" data-toggle="tooltip" data-placement="bottom"
                                                                    title="Replay"
                                                                    onclick="replayMessage('<?php echo $datares['id']; ?>','<?php echo $datares['description']; ?>','<?php echo urlencode($datares['message_type']); ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                                                <p class="m-b-0"><?php echo $datares['description']; ?></p>
                                                                <?php echo $replay_msg;
                                                                if (count((array) $tasklist['logs']['attachment'][$ky]) != '0') { ?>
                                                                    <span class="form-label col-form-label d-block"><i class="ti-clip"></i>
                                                                        Attchment : </span>
                                                                    <?php $im = 1;
                                                                    foreach ($tasklist['logs']['attachment'][$ky] as $attchmentt) { ?>
                                                                        <a href="uploads/attachments/<?php echo $attchmentt['actual_file_name']; ?>"
                                                                            download><?php echo $im . ") " . $attchmentt['file_name']; ?></a></br>
                                                                        <?php $im++;
                                                                    }
                                                                } ?>
                                                            </div>
                                                            <p class="text-muted m-b-0 f-12"><i
                                                                    class="fa fa-clock-o m-r-10"></i><?php echo $datetime; ?></p>
                                                        </div>
                                                    </div>
                                                <?php }
                                            }
                                        } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="card-px text-center py-20 mt-2">
                                        <p class="text-muted f-16 fw-bold "><i class='ti-alert text-warning'></i> No Ticket
                                            Logs </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png"
                                                style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php
                            $notshowmessgae = array(ST_DRAFT, ST_ACTIVE, ST_CANCEL);
                            if (in_array($tasklist['tasklist']['current_status'], (array) $notshowmessgae)) {
                                $notshowmessgaeshow = "style='display:none'";
                            }
                            ?>
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                                <input type="hidden" name="ticket_id"
                                    value="<?php echo $tasklist['tasklist']['ticket_id']; ?>">
                                <input type="hidden" name="display_name"
                                    value="<?php echo $_SESSION['display_name']; ?>">
                                <input type="hidden" name="parent_id" id="replay_id" value="0">
                                <input type="hidden" name="replay_message_type" id="replay_message_type" value="">
                                <div class="bg-info p-2 b-radius-5 replay_div" style="display: none;">
                                    <button type="button" class="btn p-0 border-0 bg-transparent ti-close float-right text-dark f-12" onclick="replayMessageRemoved()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                    <p class="mb-0" id="replay_message"></p>
                                </div>
                                <div class="input-group input-group-button cursor-pointer" <?php echo $notshowmessgaeshow; ?>>
                                    <button type="button" class="input-group-text addon-style p-0 bg-primary">
                                        <div class="image-input image-input-empty image-input-outline mt-2"
                                            id="kt_image_5">
                                            <label class="btn-hover-text-primary mt-1" data-action="change"
                                                data-toggle="tooltip" title="" data-original-title="Uploade File" aria-label="Upload File">
                                                <i class="ti-clip f-30 mr-5 mt-2"></i>
                                                <input aria-label="Files[]" type="file" name="files[]" multiple />
                                            </label>
                                        </div>
                                    </button>
                                    <textarea aria-label="Eg. Descriptions" class="form-control  width1" name="description" value=""
                                        placeholder="Eg. Descriptions" required></textarea>

                                    <button class="input-group-text addon-style cursor-pointer bg-primary"
                                        name="submitlog">
                                        <span class=""><i class="ti-share"></i></span>
                                    </button>
                                </div>
                                <div class="row" <?php echo $notshowmessgaeshow; ?>>
                                    <div class="col-lg-6">
                                        <div class="checkbox-fade fade-in-primary" id="checkbosshowhide">
                                            <label class="form-label">
                                                <input aria-label="Private" type="checkbox" class="private_check" value="0" name="private"
                                                    onclick="privateMessage()" onkeydown="">
                                                <span class="cr">
                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span>Private Message</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 " id="private_message_select">

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo $todisplaythree = todisplaythree(array('table' => 'project_tasks', 'originator' => $tasklist['tasklist']['client_id'], 'encode' => 'no', 'id' => $rid, 'dis_query' => 'No', 'dis_timeline' => 'yes', 'dis_comments' => 'yes'));
        ?>
    </div>
</div>
</div>
<!-- Modal -->
<div class="modal fade" id="modal_follower" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modallabel" id="exampleModalLabel">Followers </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" id="insertfoolwersform">
                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <input type="hidden" name="old_followers" value="<?php echo $tasklist['tasklist']['follower_ids']; ?>">
                <input type="hidden" name="ticket_id" value="<?php echo $tasklist['tasklist']['ticket_id']; ?>">
                <input type="hidden" name="display_name" value="<?php echo $_SESSION['display_name']; ?>">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <?php $followlist = explode(',', $tasklist['tasklist']['follower_ids']); ?>
                            <div class="col-lg-12 w-full">
                                <span class="form-label col-form-label">Select Followers </span>
                                <select aria-label="Popupfollow []" class="form-control form-select js-example-basic-multiple"
                                    name="popupfollow_id[]" multiple style="width:100%">
                                    <?php foreach ($users_list as $uvalue) { ?>
                                        <option value="<?php echo $uvalue['id']; ?>" <?php echo (in_array($uvalue['id'], (array) $followlist)) ? "selected" : ""; ?>>
                                            <?php echo $uvalue['display_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary " onclick="submitFollowers()">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function submitFollowers() {
        var forvalue = $("#insertfoolwersform").serialize();
        $.ajax({
            type: 'post',
            url: 'https://localhost/infisuite/ticketing_two/ajax.php',
            data: forvalue + "&page=updateticketfollowers",
            success: function (response) {
                console.log(response);
                $('#modal_follower').modal('hide');
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', "<?php echo $_SESSION['error_message']['3001']; ?>", '');
                location.reload();
            }
        });
    }
</script>
