<?php
$File_Uploads_Path = "uploads/all/";
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submitperformance'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    foreach($_FILES['attchments']['error'] as $f=>$error_status){
        if($error_status =="0"){
            $temp = explode(".", basename($_FILES["attchments"]["name"][$f]));
            $filename=  "apraisal_".$_POST['rid']."_".time()."_".uniqid();
            $newfilename = $filename.'.' .end($temp);
            $data_agreements['file_url'][$f] = $newfilename;
            move_uploaded_file($_FILES["attchments"]["tmp_name"][$f], $File_Uploads_Path.$newfilename);
            $_POST['performance_appraisal_questions'][$f]['attchments'] = $newfilename;
        }else{
            $_POST['performance_appraisal_questions'][$f]['attchments'] = $_POST['old_attchments'][$f];
        }
    }
    $url = API."?mode=insertemployeeperformanceappraisals&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                    $("body").on("contextmenu",function(e){
                        return false;
                       });
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}
$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$rid  = $erid['1'];
$epahurl = API."?mode=getemployeeperformanceappraisals&rid=".$rid;
$epasresult = callAPI($epahurl);
$result = json_decode($epasresult,1);
$employee_performance_appraisals = $result['employee_performance_appraisals'];
$employee = $result['employee'];
$appraisal_key_result_areas = $result['appraisal_key_result_areas'];
$organization_goals = $result['organization_goals'];
$arslabs  = $result['arslabs'];
$rankingslab = array();
foreach($arslabs as $sl){
    $rankingslab[$sl['id']] = $sl;
}
$performance_appraisal_questions  = $result['performance_appraisal_questions'];
$paheaders  = $result['performance_appraisal_headers'];
$employee_performance_appraisal_details = $result['employee_performance_appraisal_details'];
$employee_goals = $result['employee_goals'];
$employee_feedbacks = $result['employee_feedbacks'];
$buddy_details  = $result['buddy_details'];
foreach($employee_performance_appraisal_details as $epa){
    $epappraisal[$epa['header_id']][] = $epa;
}
$total_kpi_target = 0;
$self_achived = json_decode($employee_performance_appraisals['self_achived'],1);
$ro_achived = json_decode($employee_performance_appraisals['ro_achived'],1);
$paq_answer = json_decode($employee_performance_appraisals['performance_appraisal_questions'],1);
$workflowsteps = getWorkflowStatus('employee_performance_appraisals',$employee_performance_appraisals['id'],$employee_performance_appraisals['current_status']);
$approvalbtn = "";
$workflow_approval_level = array();
foreach($workflowsteps['all_steps_workflow'] as $wfstatus=>$wflow){
    $wfuserid = $wflow['aproval_user'][0]['user_id'];
    $workflow_approval_level[$wfstatus] = $wfuserid;
}

if($workflow_approval_level[ST_APPROVED2] == $_SESSION['userid']){
    if($employee_performance_appraisals['current_status'] != ST_APPROVED2){
        $updatebtndisable = 'style="pointer-events: none; opacity: 0.6;"';
    }
    $loginuser_type = "HOD"; //EMP,RO,HOD
}elseif($workflow_approval_level[ST_APPROVED1] == $_SESSION['userid']){
    $loginuser_type = "RO"; //EMP,RO,HOD
    if($employee_performance_appraisals['current_status'] != ST_APPROVED1){
        $updatebtndisable = 'style="pointer-events: none; opacity: 0.6;"';
    }
    if($employee_performance_appraisals['normalization_approved_status'] == '0'){
        $final_ranking_id = $employee_performance_appraisals['normalization_ranking'];
        $ro_ranking_id = 0;
        foreach($arslabs as $slab){
            if($slab['start_range'] <= $employee_performance_appraisals['ro_total'] && $slab['end_range'] >= $employee_performance_appraisals['ro_total']){
                $ro_ranking_id = $slab['id'];
            }
        }
        if($final_ranking_id != $ro_ranking_id){
            $approvalbtn = "<span class='text-danger'> <i class='ti-alert'></i> Final Slab Not Match<span>";
        }
    }
}elseif($employee['user_id'] == $_SESSION['userid'] || $_SESSION['admin_type'] == '1'){
    $loginuser_type = "EMP"; //EMP,RO,HOD
    if($employee_performance_appraisals['current_status'] != ST_DRAFT && $employee_performance_appraisals['current_status'] != ST_ACTIVE && $pepah_res['current_status'] != ST_SEND_BACK && $employee_performance_appraisals['current_status'] != ST_CALLBACK){
        $updatebtndisable = 'style="pointer-events: none; opacity: 0.6;"';
    }
}

if($loginuser_type == "RO" || $loginuser_type == "HOD" || $employee_performance_appraisals['publish_comment']=='0' || $employee_performance_appraisals['current_status'] ==ST_APPROVED){

}else{
    $notshowro = "style='display:none'";
}
if($loginuser_type == "HOD" || $employee_performance_appraisals['publish_comment']=='0' || $employee_performance_appraisals['current_status'] ==ST_APPROVED){

}else{
    $notshowhod = "style='display:none'";
}
if($loginuser_type == "RO" || $loginuser_type == "HOD" || $employee_performance_appraisals['publish_rank']=='0' || $employee_performance_appraisals['current_status'] ==ST_APPROVED){

}else{
    $ranknotshowro = "style='display:none'";
}
if($loginuser_type == "HOD" || $employee_performance_appraisals['publish_rank']=='0' || $employee_performance_appraisals['current_status'] ==ST_APPROVED){

}else{
    $ranknotshowhod = "style='display:none'";
}

?>
<script type="text/javascript">
    function getAchivedTargetSelf(ogid,u_type){
        var weightage = $("#weightage_"+ogid).val();
        var self_target = $("#self_target_"+ogid).val();
        var self_achieved = $("#self_achieved_"+ogid).val();
        var kra_scor_slabs = $("#kra_scor_slabs").val();
        var scor_slabs = JSON.parse(kra_scor_slabs);
        var score = '';
        if(self_achieved !="" && self_achieved !=0){
            var total = parseFloat(self_achieved/self_target*100);
            scor_slabs.forEach(function(slabs) {
                if(parseFloat(slabs.start_range) <= total && parseFloat(slabs.end_range) >= total){
                    score = slabs.score;
                }
            });
        }
        $("#self_scored_"+ogid).val(score);

        var total_self_target = 0;
        $(".self_target_class").each(function( index ) {
            var target = $(this).val()!=""?$(this).val():0;
            total_self_target += parseFloat(target);
        });
        $("#total_self_target").val(total_self_target);

        var total_self_achieved = 0;
        $(".self_achieved").each(function(index) {
            var self_achieved = $(this).val()!=""?$(this).val():0;
            total_self_achieved += parseFloat(self_achieved);
        });
        $("#total_self_achieved").val(total_self_achieved.toFixed(2));

        var total_self_score = 0;
        $(".self_score").each(function(index) {
            var self_score = $(this).val()!=""?$(this).val():0;
            total_self_score += parseFloat(self_score);
        });
        $("#total_self_score").val(total_self_score.toFixed(2));

        var total  = total_self_achieved*parseFloat(100/total_self_target);
        $("#finalkra_total_employee").val(Math.round(total)+"%");
        $("#text_finalkra_total_employee").val(Math.round(total));

    }
    function getAchivedTargetRo(ogid,u_type){
        var weightage = $("#weightage_"+ogid).val();
        var self_target = $("#self_target_"+ogid).val();
        var ro_achieved = $("#ro_achieved_"+ogid).val();
        var kra_scor_slabs = $("#kra_scor_slabs").val();
        var scor_slabs = JSON.parse(kra_scor_slabs);
        var score = '';
        if(ro_achieved !="" && ro_achieved !=0){
            var total = parseFloat(ro_achieved/self_target*100);
            scor_slabs.forEach(function(slabs) {
                if(parseFloat(slabs.start_range) <= total && parseFloat(slabs.end_range) >= total){
                    score = slabs.score;
                }
            });
        }
        $("#ro_scored_"+ogid).val(score);

        var total_self_target = 0;
        $(".self_target_class").each(function( index ) {
            var target = $(this).val()!=""?$(this).val():0;
            total_self_target += parseFloat(target);
        });
        $("#total_self_target").val(total_self_target);

        var total_ro_achieved = 0;
        $(".ro_achieved").each(function(index) {
            var self_achieved = $(this).val()!=""?$(this).val():0;
            total_ro_achieved += parseFloat(self_achieved);
        });
        $("#total_ro_achieved").val(total_ro_achieved.toFixed(2));

        var total_ro_score = 0;
        $(".ro_score").each(function(index) {
            var self_score = $(this).val()!=""?$(this).val():0;
            total_ro_score += parseFloat(self_score);
        });
        $("#total_ro_score").val(total_ro_score.toFixed(2));

        var total  = total_ro_achieved*parseFloat(100/total_self_target);
        $("#finalkra_total_manager").val(Math.round(total)+"%");
        $("#finalkra_total_manager_insert").val(Math.round(total));

    }
    function addMoreRow(header_id){
        var hedcnt = $("#cntrows_"+header_id).val();
        var hedcntadd  = hedcnt;
        hedcntadd++
        $("#cntrows_"+header_id).val(hedcntadd);
        var rid = $("#rid").val();
        var loginuser_type = '<?php echo $loginuser_type; ?>';
        jQuery.ajax({
            url: "views/custom_insert_performance_appraisal_ajax_table.php",
            data:'header_id='+header_id+'&rid='+rid+"&loginuser_type="+loginuser_type+'&hedcnt='+hedcnt,
            type: "POST",
            success:function(htmlRows){
                console.log(htmlRows);
                $('#dinamictable_'+header_id).append(htmlRows);
            },
            error:function (){}
        });
    }
    function appraiserDeleteRow(row_id){
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Do you want to Remove the Row?',
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
                $("#deleterow_"+row_id).remove();
            }
        });
    }
    function getEmployeeFeedbacks(emp_fd_id, emp_id){
        jQuery.ajax({
            url: "views/custom_insert_employee_feedbacks_popup.php",
            data:'emp_fd_id='+emp_fd_id+'&emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $("#kt_modal_KTDatatable_remote").modal("show");
                $(".js-example-basic-single").select2();
            },
            error:function (){}
        });
    }
    function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Please note the status of the document will change to "+new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function(){
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    var res = JSON.parse(response);
                    console.log(res);
                    if(res.transaction_update.status=='sucess'){
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.reload();
                        });
                    }else{
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error:function (){}
            });
        });
    }
    function updateNormalizations(rid){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updatenormalizations&rid='+rid,
            type: "POST",
            success:function(response){
                console.log(response);
            }
        });
    }
    function publishCommentAndRank(type,rid){
        if(type == "C"){
            var msg = "<?php echo $_SESSION['error_message']['9083']; ?>";
            var successmsg = "<?php echo $_SESSION['error_message']['9084']; ?>";
        }else{
            var msg = "<?php echo $_SESSION['error_message']['9085']; ?>";
            var successmsg = "<?php echo $_SESSION['error_message']['9086']; ?>";
        }
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: msg,
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
                    data:'page=publishcommentandrank&type='+type+'&rid='+rid,
                    type: "POST",
                    success:function(response){
                        console.log(response);
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',successmsg,'');
                        setTimeout(function() { location.reload(); }, 700);
                    }
                });
            }
        });
    }
    document.onreadystatechange = function(){
        if(document.readyState == "complete") {
            $("body").on("contextmenu",function(e){
                return false;
               });
        }
    }

</script>
<?php if($employee_performance_appraisals['normalization_approved_status'] == '' && $employee_performance_appraisals['normalization_status'] == '0' && $loginuser_type == 'RO'){ ?>
<div class="alert alert-info border-info alert-dismissible">
    <strong> <i class="ti-alert"></i>&nbsp;  Normalizations In Process</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php }elseif($employee_performance_appraisals['normalization_approved_status'] == '0' && $loginuser_type == 'RO'){ ?>
<div class="alert alert-info border-info alert-dismissible">
    <strong> <i class="fa fa-thumbs-up"></i>  Normalizations Process Is Approved <span class="f-w-700">(<?php echo $rankingslab[$employee_performance_appraisals['normalization_ranking']]['slabs_name']; ?>)</span></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php } ?>
<div class="card">
<?php
if($employee_performance_appraisals['id'] !=""){
    echo $workflowsteps['current_status_ribbon'];
} ?>
    <div class="card-header">
        <h5>Performance Appraisal </h5>
        <div class="card-header-right table-card-header m-r-50">
            <?php
                if($employee_performance_appraisals['id'] !="" && $approvalbtn ==""){
                    echo $workflowsteps['workflow_btn'];
                }
                if($approvalbtn !=""){
                    echo $approvalbtn;
                }
            ?>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">
                <?php echo $workflowsteps['breadcrumb']; ?>
            </div>
            <div class="col-lg-4 text-right">
                <?php if($employee_performance_appraisals['normalization_status'] != '0' && $loginuser_type == 'RO'){ ?>
                <button class="btn btn-primary mb-2 desktop-view mt-4" onclick="updateNormalizations('<?php echo $rid; ?>')"><span class="fa fa-thumbs-up text-white"></span> &nbsp; Normalizations </button>
                <?php } ?>
                <?php

                    if($workflowsteps['next_status'] ==ST_APPROVED && $workflowsteps['next_step_owner'] == $_SESSION['userid']){ ?>
                        <div class="btn-group workflow-btn" role="group" >
                            <?php if($employee_performance_appraisals['publish_comment']!='0'){ ?>
                            <button type="button" class="btn btn-success btn-sm waves-effect waves-light" onclick="publishCommentAndRank('C','<?php echo $employee_performance_appraisals['id']; ?>')"><i class="ti-comment-alt"></i> Publish Comments</button>
                            <?php } ?>
                            <?php if($employee_performance_appraisals['publish_rank']!='0'){ ?>
                            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="publishCommentAndRank('CR','<?php echo $employee_performance_appraisals['id']; ?>')"><i class="ti-cup"></i> Publish <?php echo ($employee_performance_appraisals['publish_comment']!='0')?"Comment And ":""; ?> Ranks</button>
                        <?php }     ?>
                        </div>
                <?php } ?>
                <?php if($employee_performance_appraisals['publish_rank']=='0'){ echo '<span class="form-label label label-md label-primary m-r-50"><span class="fa fa-thumbs-up text-white"></span> Rank publish</span>';
} ?>
            </div>
        </div>

<div class="card product-progress-card mb-3">
    <div class="card-block">
        <div class="row">
            <div class="col-lg-5">
                <div class="d-flex">
                    <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                        <div class="symbol symbol-50 symbol-lg-120">
                            <img src="../uploads/all/135.jpg" class="img-120 " alt="Preview">
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600 d-block">  <?php echo $employee['display_name']; ?></span>

                        <span class="f-12 f-w-600 d-block mt-1">  <i class="ti-id-badge f-14 text-primary"></i>  &nbsp;<?php echo $employee['emp_code']; ?> </span>

                        <span class="f-12 f-w-600 d-block mt-1">  <i class="ti-email f-14 text-primary"></i>  &nbsp;<?php echo $employee['email_id']; ?> </span>

                        <span class="f-12 f-w-600 d-block mt-1">  <i class="ti-briefcase f-14 text-primary"></i>  &nbsp;<?php echo $employee['designation_name']; ?> </span>

                        <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="f-12 f-w-600">Rating </span>
                                <span class="f-12 f-w-600"><?php echo round($employee_performance_appraisals['ro_total']); ?>%</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo round($employee_performance_appraisals['ro_total']); ?>%;" aria-valuenow="<?php echo round($employee_performance_appraisals['ro_total']); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-2 text-center">
                        <div class="s-caption  text-primary"><i class="ti-cup f-20 "></i></div>
                        <div class="s-cont d-inline-block">
                            <p class="m-b-0 f-12">Self Rating</p>
                            <h5 class="f-w-700 m-b-0"><?php echo round($employee_performance_appraisals['self_total']); ?></h5>

                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center" <?php echo $notshowro; ?>>
                        <div class="s-caption  text-danger"><i class="ti-cup f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <p class="m-b-0 f-12">RO Rating</p>
                            <h5 class="f-w-700 m-b-0"><?php echo round($employee_performance_appraisals['ro_total']); ?></h5>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-comments f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <p class="m-b-0  f-12">Publish Comment</p>
                            <h5 class="f-w-700 m-b-0"><?php echo $employee_performance_appraisals['publish_comment']=="0"?"Yes":"No"; ?></h5>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-cup f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <p class="m-b-0  f-12">Publish Rating</p>
                            <h5 class="f-w-700 m-b-0"><?php echo $employee_performance_appraisals['publish_rank']=="0"?"Yes":"No"; ?></h5>
                        </div>
                    </div>
                    <div class="col-4 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-user f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <p class="m-b-0  f-12">RO Name</p>
                            <h5 class="f-w-700 m-b-0"><?php echo $_SESSION['users'][$employee['reports_to']]; ?></h5>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <form method="post" enctype="multipart/form-data">
        <div class="accordion-block color-accordion-block m-t-5">
            <div class="color-accordion" id="color-accordion">
                <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Professional Excellence</a>
                <div class="accordion-desc" <?php echo $updatebtndisable; ?>>
                    <input type="hidden" id="kra_scor_slabs" value='<?php echo json_encode($arslabs); ?>'>
                    <input type="hidden" id="total_kpi_target" value='<?php echo $total_kpi_target; ?>'>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Key Result Area</th>
                                    <th scope="col">Key Performance Indicators</th>
                                    <th scope="col">Weightage</th>
                                    <th scope="col">Target / Achieved (self Score)</th>
                                    <th scope="col">Points Scored<br />(self)</th>
                                    <th scope="col" class="" <?php echo $ranknotshowro; ?>>Percentage achieved<br />(RO's Score)</th>
                                    <th scope="col" <?php echo $ranknotshowro; ?>>Points Scored<br />(RO)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $mk = 1;
                                $total_self_target =$total_self_achieved =$total_self_score =$total_ro_achieved = $total_ro_score = 0;
                                foreach($organization_goals as $ogdetail){
                                    $total_self_target += $ogdetail['traget'] !=""?$ogdetail['traget']:0;
                                    $total_self_achieved += $self_achived[$ogdetail['id']]['self_achived'] !=""?$self_achived[$ogdetail['id']]['self_achived']:0;
                                    $total_self_score += $self_achived[$ogdetail['id']]['self_scored'] !=""?$self_achived[$ogdetail['id']]['self_scored']:0;

                                    $total_ro_achievement += $ro_achived[$ogdetail['id']]['ro_achived'] !=""?$ro_achived[$ogdetail['id']]['ro_achived']:0;
                                    $total_ro_score += $ro_achived[$ogdetail['id']]['ro_scored'] !=""?$ro_achived[$ogdetail['id']]['ro_scored']:0;

                                 ?>
                                    <tr>
                                        <th scope="col" rowspan="">
                                            <?php echo $mk; ?>
                                        </th>
                                        <th scope="col" rowspan=""><?php echo $appraisal_key_result_areas[$ogdetail['key_reslut_id']]['area_name']; ?>
                                        </th>
                                        <th scope="col"><?php echo $ogdetail['indicator_name']; ?> </th>
                                        <td>
                                            <input aria-label="Input field" type="text" class="form-control" id="weightage_<?php echo $ogdetail['id']; ?>"  value="<?php echo $ogdetail['appraisal_key_performance_indicator_weightage']; ?>" readonly>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <input aria-label="Input field" type="text" class="form-control self_target_class" id="self_target_<?php echo $ogdetail['id']; ?>" value="<?php echo $ogdetail['traget']; ?>" readonly>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input aria-label="Enter number" type="number" class="form-control col-lg-6 self_achieved"  id="self_achieved_<?php echo $ogdetail['id']; ?>" onblur="getAchivedTargetSelf(<?php echo $ogdetail['id']; ?>,'emp')" name="self_achived[<?php echo $ogdetail['id']; ?>][self_achived]" value="<?php echo $self_achived[$ogdetail['id']]['self_achived']; ?>" <?php echo $loginuser_type != "EMP"?"readonly":""; ?>>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="">
                                                <input aria-label="Input field" type="text" class="form-control self_score"  id="self_scored_<?php echo $ogdetail['id']; ?>" value="<?php echo $self_achived[$ogdetail['id']]['self_scored']; ?>" name="self_achived[<?php echo $ogdetail['id']; ?>][self_scored]" readonly>
                                            </div>
                                        </td>
                                        <td <?php echo $ranknotshowro; ?>>
                                            <div class="">
                                                <input aria-label="Enter number" type="number" class="form-control col-lg-6 ro_achieved"  id="ro_achieved_<?php echo $ogdetail['id']; ?>" onblur="getAchivedTargetRo(<?php echo $ogdetail['id']; ?>,'')" name="ro_achived[<?php echo $ogdetail['id']; ?>][ro_achived]" value="<?php echo $ro_achived[$ogdetail['id']]['ro_achived']; ?>" <?php echo $loginuser_type != "RO"?"readonly":""; ?>>
                                            </div>
                                        </td>
                                        <td <?php echo $ranknotshowro; ?>>
                                            <div class="">
                                                <input aria-label="Input field" type="text" class="form-control ro_score"  id="ro_scored_<?php echo $ogdetail['id']; ?>" value="<?php echo $ro_achived[$ogdetail['id']]['ro_scored']; ?>" name="ro_achived[<?php echo $ogdetail['id']; ?>][ro_scored]" readonly>
                                            </div>
                                        </td>

                                    </tr>
                                <?php $mk++;
}  ?>
                                <tr>
                                    <th scope="col" colspan="4" class="text-center">Total</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input aria-label="Total Self Target" type="text" class="form-control" id="total_self_target" value="<?php echo $total_self_target; ?>" readonly="">
                                            </div>
                                            <div class="col-lg-6">
                                                <input aria-label="Input field" type="text" class="form-control" value="<?php echo $total_self_achieved; ?>" id="total_self_achieved" readonly>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $total_self_score; ?>" id="total_self_score"  readonly>
                                        </div>
                                    </td>
                                    <td <?php echo $ranknotshowro; ?>>
                                        <div class="">
                                            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $total_ro_achievement; ?>"  id="total_ro_achieved" readonly>
                                        </div>
                                    </td>
                                    <td <?php echo $ranknotshowro; ?>>
                                        <div class="">
                                            <input aria-label="Total Ro Score" type="text" class="form-control" id="total_ro_score" value="<?php echo $total_ro_score; ?>" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col" colspan="3" class="text-center">Total Percentage(%)</th>
                                    <td colspan="2">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group input-group-button">
                                                    <input type="hidden" name="self_total" id="text_finalkra_total_employee" value="<?php echo $employee_performance_appraisals['self_total']; ?>">

                                                    <input aria-label="Employee Total" type="text" class="form-control width1"  readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="Employee Total" value="<?php echo round($employee_performance_appraisals['self_total'],2)."%"; ?>"  id="finalkra_total_employee">
                                                    <span class="input-group-text addon-style">
                                                        <span class=""><i class="fa fa-user"></i></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" <?php echo $ranknotshowro; ?>>
                                                <div class="input-group input-group-button">
                                                    <input type="hidden" name="ro_total" id="finalkra_total_manager_insert" value="<?php echo $employee_performance_appraisals['ro_total']; ?>">

                                                    <input aria-label="Manager Total" type="text" class="form-control width1" id="finalkra_total_manager" readonly="" data-bs-toggle="tooltip" data-placement="bottom" title="Manager Total" value="<?php echo round($employee_performance_appraisals['ro_total'],2)."%"; ?>" >
                                                    <span class="input-group-text addon-style">
                                                        <span class=""><i class="fa fa-user-secret"></i></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="pt-4 text-center" colspan="7">
                                        <?php foreach($arslabs as $slab){
                                                if($slab['start_range'] <= $employee_performance_appraisals['ro_total'] && $slab['end_range'] >= $employee_performance_appraisals['ro_total']){  ?>
                                                    <span class="label label-lg  label-inline f-w-700" style="background-color:<?php echo $slab['background_color'];?> "><?php echo $slab['slabs_name'];?></span>
                                            <?php     }
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="grade-span">
                                            <h4>Grade</h4>
                                            <?php foreach($arslabs as $arslab){ ?>
                                                <span class="label label-lg  label-inline" style="background-color:<?php echo $arslab['background_color'];?> "><b><?php echo $arslab['slabs_name'];?></b></span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Performance Appraisal Questions</a>
                <div class="accordion-desc" <?php echo $updatebtndisable; ?>>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" class="w-b-w-s" style="width: 30%;">Question</th>
                                <th scope="col" style="width: 5%;">Attch</th>
                                <th scope="col" style="width: 20%;">Ratting / Answer Employee</th>
                                <th scope="col" <?php echo $notshowro; ?> style="width: 20%;">Ratting /Answer RO</th>
                                <th scope="col" <?php echo $notshowro; ?> style="width: 20%;">Ratting / Answer HOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $queseq = 1;
                                foreach($performance_appraisal_questions as $paq){
                                    $question_options = json_decode($paq['question_options'],1);
                                    $display_answer = json_decode($paq['display_answer'],1);
                                    $emp_answer = $paq_answer[$paq['id']]['emp']['answer'];
                                    $ro_answer = $paq_answer[$paq['id']]['ro']['answer'];
                                    $hod_answer = $paq_answer[$paq['id']]['hod']['answer'];
                                ?>
                            <tr>
                                <th scope="col">
                                    <input type="hidden" class="form-control" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][paq_id]" value="<?php echo $paq['id']; ?>">
                                    <input type="hidden" class="form-control" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][type]" value="<?php echo $paq['answer_type']; ?>">
                                    <?php echo $queseq; ?>

                                </th>
                                <th scope="col" class="w-b-w-s"><p><?php echo $paq['question']; ?><small class="d-block"><?php echo $paq['description']; ?></small><p></th>
                                <th scope="col" >
                                    <div class="row">
                                        <div class="image-input col-lg-3" style="display: inherit; margin-top: -2px;">
                                            <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" >
                                                <i class="ti-cloud-up text-primary f-18 f-w-800" data-toggle="tooltip" title="Upload"></i>
                                                <input aria-label="File upload" type="file" name="attchments[<?php echo $paq['id']; ?>]" />
                                                <input type="hidden" name="old_attchments[<?php echo $paq['id']; ?>]" value="<?php echo $paq_answer[$paq['id']]['attchments']; ?>"/>
                                            </label>
                                        </div>
                                        <?php if($paq_answer[$paq['id']]['attchments']!=''){ ?>
                                            <div class="col-lg-3">
                                                <a href="<?php echo $File_Uploads_Path."/".$paq_answer[$paq['id']]['attchments']; ?>" data-toggle="tooltip" title="Download" target="_blank" >
                                                    <i class="ti-cloud-down f-18 text-warning f-w-800"></i>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </th>
                                <td class="<?php echo $loginuser_type == "EMP"?'':'event_disabled'; ?>" >
                                    <?php
                                    if(in_array('EMP', $display_answer)){  ?>
                                    <div class="row">
                                        <div class="col-lg-3 p-1">
                                            <select aria-label="Select option" class="form-control js-example-basic-single" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][emp][ratting]">
                                                <?php for($im=1;$im<=10;$im++){?>
                                                <option value="<?php echo $im; ?>" <?php echo $paq_answer[$paq['id']]['emp']['ratting'] == $im?"selected":""; ?>><?php echo $im; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-9 p-1">
                                        <?php
                                        if($paq['answer_type'] == 'MC'){?>
                                            <?php foreach($question_options as $qo){ ?>
                                                <div class="checkbox-fade fade-in-primary mr-5">
                                                    <label class="form-label">
                                                        <input aria-label="Checkbox" type="checkbox" value="<?php echo $qo; ?>" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][emp][answer][]" <?php echo in_array($qo,(array)$emp_answer)?"checked":""; ?>>
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                            <?php }elseif($paq['answer_type'] == 'O'){?>
                                                <select aria-label="Select option" class="form-control js-example-basic-single" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][emp][answer]">
                                                    <option value="">Select Any One</option>
                                                    <?php foreach($question_options as $qo){ ?>
                                                            <option value="<?php echo $qo; ?>" <?php echo $qo ==$emp_answer?"selected":""; ?>><?php echo $qo; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php }else{ ?>
                                                <textarea aria-label="Text area" class="form-control" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][emp][answer]"><?php echo $emp_answer; ?></textarea>
                                            <?php } ?>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </td>
                                <td  class="<?php echo $loginuser_type == "RO"?'':'event_disabled'; ?>"  <?php echo $notshowro; ?>>
                                    <?php
                                    if(in_array('RO', $display_answer)){  ?>
                                    <div class="row">
                                        <div class="col-lg-3 p-1" <?php echo $ranknotshowro; ?>>
                                            <select aria-label="Select option" class="form-control js-example-basic-single" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][ro][ratting]">
                                                <?php for($im=1;$im<=10;$im++){?>
                                                <option value="<?php echo $im; ?>" <?php echo $paq_answer[$paq['id']]['ro']['ratting'] == $im?"selected":""; ?>><?php echo $im; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-9 p-1" <?php echo $notshowro; ?>>
                                        <?php
                                        if($paq['answer_type'] == 'MC'){?>
                                            <?php foreach($question_options as $qo){ ?>
                                                <div class="checkbox-fade fade-in-primary mr-5">
                                                    <label class="form-label">
                                                        <input aria-label="Checkbox" type="checkbox" value="<?php echo $qo; ?>" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][ro][answer][]" <?php echo in_array($qo,(array)$ro_answer)?"checked":""; ?>>
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                            <?php }elseif($paq['answer_type'] == 'O'){?>
                                                <select aria-label="Select option" class="form-control js-example-basic-single" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][ro][answer]">
                                                    <option value="">Select Any One</option>
                                                    <?php foreach($question_options as $qo){ ?>
                                                            <option value="<?php echo $qo; ?>" <?php echo $qo ==$ro_answer?"selected":""; ?>><?php echo $qo; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php }else{ ?>
                                                <textarea aria-label="Text area" class="form-control" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][ro][answer]"><?php echo $ro_answer; ?></textarea>
                                            <?php } ?>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </td>
                                <td class="<?php echo $loginuser_type == "HOD"?'':'event_disabled'; ?>"  <?php echo $notshowhod; ?>>
                                    <?php
                                    if(in_array('HOD', $display_answer)){  ?>
                                    <div class="row">
                                        <div class="col-lg-3 p-1" <?php echo $ranknotshowhod; ?>>
                                            <select aria-label="Select option" class="form-control js-example-basic-single" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][hod][ratting]">
                                                <?php for($im=1;$im<=10;$im++){?>
                                                <option value="<?php echo $im; ?>" <?php echo $paq_answer[$paq['id']]['hod']['ratting'] == $im?"selected":""; ?>><?php echo $im; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-9 p-1" <?php echo $notshowhod; ?>>
                                        <?php
                                        if($paq['answer_type'] == 'MC'){ ?>
                                            <?php foreach($question_options as $qo){ ?>
                                                <div class="checkbox-fade fade-in-primary mr-5">
                                                    <label class="form-label">
                                                        <input aria-label="Checkbox" type="checkbox" value="<?php echo $qo; ?>" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][hod][answer][]" <?php echo in_array($qo,(array)$hod_answer)?"checked":""; ?>>
                                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                            <?php }elseif($paq['answer_type'] == 'O'){?>
                                                <select aria-label="Select option" class="form-control js-example-basic-single" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][hod][answer]">
                                                    <option value="">Select Any One</option>
                                                    <?php foreach($question_options as $qo){ ?>
                                                            <option value="<?php echo $qo; ?>" <?php echo $qo ==$hod_answer?"selected":""; ?>><?php echo $qo; ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php }else{ ?>
                                                <textarea aria-label="Text area" class="form-control" name="performance_appraisal_questions[<?php echo $paq['id']; ?>][hod][answer]"><?php echo $hod_answer; ?></textarea>
                                            <?php } ?>
                                        </div>
                                            <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php $queseq++;
} ?>
                        </tbody>
                    </table>
                </div>
                <?php foreach($paheaders as $paheaders){
                    $header_display_setting = json_decode($paheaders['header_display_setting'],1);
                    $sub_header_name = json_decode($paheaders['sub_header_name'],1);
                    $paheadersrow = $paheaders['id'];
                    if(isset($epappraisal[$paheaders['id']])){
                        $paheaders['sub_header_default_row'] = count((array)$epappraisal[$paheaders['id']]);
                    }
                    if($header_display_setting[$loginuser_type]== "Y"){ ?>
                    <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?php echo $paheaders['header_name']; ?>
                        <p class="f-w-400"><?php echo $paheaders['header_comment']; ?></p>
                    </a>
                    <div class="accordion-desc" <?php echo $updatebtndisable; ?>>
                        <?php if($paheaders['dynamic_page']=='0'){   // custom page call
                            include_once $paheaders['page_name'].".php";
                        }else{  //dyanamic table generated
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                    <thead>
                                        <tr>
                                            <?php foreach($sub_header_name as $sub_headerres){ //if($sub_headerres[$loginuser_type] == "Y"){?>
                                                <th scope="col"><?php echo $sub_headerres['field_name']; ?></th>
                                            <?php } //}
                                            if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                <th scope="col">Action</th>
                                            <?php }  ?>
                                        </tr>
                                    </thead>
                                    <tbody data-repeater-list="">
                                        <?php $hedcnt[$paheadersrow] = 1; for($hed = 0;$hed < $paheaders['sub_header_default_row'];$hed++){ ?>
                                            <tr id="deleterow_<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>">
                                                <input type="hidden" class="form-control" name="row_id[<?php echo $paheaders['id']; ?>][]" value="<?php echo $epappraisal[$paheaders['id']][$hed]['id']; ?>">
                                                <?php $fieldcnt = 1; foreach($sub_header_name as $sub_headerres){
                                                    $fieldname = "filed[".$paheaders['id']."][".$fieldcnt."][]";
                                                    $fieldvalue = $epappraisal[$paheaders['id']][$hed]['filed_'.$fieldcnt];
                                                    ?>
                                                    <td>
                                                        <div class="">
                                                            <?php if($sub_headerres['type']=="S" && $sub_headerres[$loginuser_type] == "Y"){ ?>
                                                                <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="<?php echo $fieldname; ?>">
                                                                    <option value="Yes" <?php echo $fieldvalue=="Yes"?"selected":"";?>>Yes</option>
                                                                    <option value="No" <?php echo $fieldvalue=="No"?"selected":"";?>>No</option>
                                                                    <option value="Dont_know" <?php echo $fieldvalue=="Dont_know"?"selected":"";?>>i don't know</option>
                                                                </select>
                                                            <?php }else{ ?>
                                                                <input aria-label="Input field" type="text" class="form-control" name="<?php echo $fieldname; ?>" value="<?php echo $fieldvalue; ?>" <?php echo $sub_headerres[$loginuser_type] == "N"?"readonly":""; ?>>
                                                            <?php } ?>

                                                        </div>
                                                    </td>
                                                    <?php  $fieldcnt++;
} ?>
                                                    <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
                                                        <td>
                                                            <i class="fa fa-trash text-danger" onclick="appraiserDeleteRow(<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                                <?php $hedcnt[$paheadersrow]++;
} ?>
                                            </tbody>
                                        </table>
                                        <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
                                            <input type="hidden" id="cntrows_<?php echo $paheaders['id']; ?>" value="<?php echo $hedcnt[$paheadersrow];?>">
                                            <button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary mt-2" onclick="addMoreRow('<?php echo $paheaders['id']; ?>')">
                                                <i class="fa fa-plus"></i>Add
                                            </button>
                                        <?php } ?>
                                    </div>
                                <?php  } ?>
                            </div>
                        <?php } ?>
                        <?php } ?>

        </div>
        <div class="row mt-5">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Employee Comment</h5>
                    </div>
                    <div class="card-body">
                        <textarea aria-label="Employee Comment" class="form-control" name="employee_comment" style="height: 270px;" <?php echo $loginuser_type != "EMP"?"readonly":""; ?> ><?php echo $employee_performance_appraisals['employee_comment']; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 "  <?php echo $notshowro; ?>>
                <div class="card">
                    <div class="card-header">
                        <h5>RO Comment</h5>
                    </div>
                    <div class="card-body">
                        <textarea aria-label="Ro Comment" class="form-control" name="ro_comment" style="height: 270px;" <?php echo $loginuser_type != "RO"?"readonly":""; ?>><?php echo $employee_performance_appraisals['ro_comment']; ?></textarea>
                    </div>
                </div>
            </div><?php //print_r($employee_performance_appraisals); ?>
            <div class="col-lg-4 " <?php echo $notshowhod; ?>>
                <div class="card">
                    <div class="card-header">
                        <h5>HOD Comment</h5>
                    </div>
                    <div class="card-body">
                        <textarea aria-label="Hod Comment" class="form-control" name="hod_comment" style="height: 270px;" <?php echo $loginuser_type != "HOD"?"readonly":""; ?>><?php echo $employee_performance_appraisals['hod_comment']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Goals</h5>
                        <div class="card-header-right table-card-header"></div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" class="w-b-w-s" style="width: 35%;">Question</th>
                                <th scope="col" style="width: 20%;">End Date</th>
                                <th scope="col" style="width: 20%;">End Date</th>
                                <th scope="col" style="width: 20%;"></th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $sq= 1; foreach($employee_goals as $eg){ ?>
                            <tr>
                                <td class="p-2"><?php echo $sq; ?></td>
                                <td class="w-b-w-s p-2"><?php echo $eg['goal_name']; ?></td>
                                <td class="p-2"><?php echo fromDate($eg['goal_start_date']); ?></td>
                                <td class="p-2"><?php echo fromDate($eg['goal_end_date']); ?></td>
                                <td class="p-2">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                <span class="fw-bold fs-6 text-gray-400">Progress</span>
                                                <span class="fw-bolder fs-6 ml-2"><?php echo $eg['percentage_of_completion']; ?>%</span>
                                            </div>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-c-green" style="width:<?php echo $eg['percentage_of_completion']; ?>%"></div>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php $sq++;
} ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>FeedBacks</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scroll" style=" height: 450px;">
                            <table class="table table-hover m-b-0" style="font-size:13px;">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 10%;">#</th>
                                        <th scope="col" class="w-b-w-s" style="width: 70%;">FeedBacks</th>
                                        <th scope="col" class="w-b-w-s" style="width: 20%;">Response</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $efseq = 1; foreach($employee_feedbacks as $ef){
                                        ?>
                                        <tr onclick="getEmployeeFeedbacks('<?php echo $ef['id']; ?>','<?php echo $ef['employee_id']?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <td class="p-2" style="width: 10%;"><i class="fa fa-edit"></i> <?php echo $efseq; ?></td>
                                            <td class="p-2 w-b-w-s" style="width: 70%;">
                                                <?php echo $ef['question']; ?>
                                            </td>
                                            <td class="p-2 text-center" style="width: 20%;">
                                                <?php if($ef['response_status']==''){ echo "Pendding";
}else{  ?>
                                                    <i class="ti-eye f-16" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View FeedBack" onclick="getEmployeeFeedbacks('<?php echo $ef['id']; ?>','<?php echo $ef['employee_id']?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php $efseq++;
} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div <?php echo $updatebtndisable; ?>>
            <input type="hidden" name="rid"  id="rid" value="<?php echo $rid; ?>">
            <input type="hidden" name="workflow_approval_level" value='<?php echo json_encode($workflow_approval_level); ?>'>
            <button type="submit" class="btn btn-primary waves-effect waves-light" name="submitperformance"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
        </div>
    </div>

</form>
</div>


