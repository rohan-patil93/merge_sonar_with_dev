<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$type = $_POST['type'];
$userlist = getList('application_users');
$userlist = json_decode($userlist,1);
$workflowlist = getList('workflow_configuration',$_POST['rid']);
$workflowlist = json_decode($workflowlist,1);
?>
<script type="text/javascript">
    function deletstep(id){
        $(".deletestepid"+id).remove();
    }
    var step = 100;
    function getstepdata(){
        var mac = $(".sttp").length+1
        var otpion = "<option>Select Any One</option>";
        <?php foreach($userlist as $userlists){ ?>
            otpion += "<option value='<?php echo $userlists['id'];?>'><?php echo $userlists['display_name'];?></option>"
        <?php  } ?>
        var rows = '<div class="row mt-1 deletestepid'+step+' sttp"><div class="col-lg-1"></div><div class="col-lg-2">Step - <span class="form-label badge bg-info ">'+mac+'</span></div><div class="col-lg-6"><select aria-label="Customsteps[]"  class="kt-dual-listbox form-select js-example-basic-single"   name="customsteps[]">'+otpion+'</select></div><div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti ti-trash text-danger" onclick="deletstep('+step+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>';
        $(".stepdata").append(rows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        step++;
    }

function submitrom(val){
    var formdata = $("#"+val).serialize();
    jQuery.ajax({
            url: "ajax.php",
            data:formdata+'&page=commanworkflowsubmit',
            type: "POST",
            success:function(data){
                if($('#type').val() == "custom_process"){
                    $(".hidaprove_updatefields").val(data);
                }
                $('#kt_modal_dynamic_popup').modal("hide");
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
                $('#kt_modal_dynamic_popup2').modal("hide");
            },
            error:function (){}
        });

}

</script>

<?php
if($type == "afteraprovassigntask"){
    $list = getList('process_task_settings');
    $list = json_decode($list,1);
    $afterapprovaltask = json_decode($workflowlist['after_approval_task'],1);
    ?>
    <script type="text/javascript">
        function addnewtask(id,process_type,type){
            jQuery.ajax({
                url: "views/custom_insert_process_task_settings.php",
                data:'option=COM_INSERT_PROCESS_TASK_SETTINGS&popup=0&workflowid='+id+'&process_type='+process_type+'&type='+type,
                type: "GET",
                success:function(data){
                    console.log(data);
                    $('#popupm_task').html(data);
                    $('#kt_modal_dynamic_popup2').modal("show");
                },
                error:function (){}
            });
        }
    </script>
<form method="post" id="afteraprovassigntask">
    <div class="modal-header">
        <h5 class="modal-title">Configration Action After Final Approval Assign Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body j-pro" >
        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
            <?php if($_POST['approval_type'] =='role_levels' || $_POST['approval_type'] =='user_hierarchical'){
                for($i=1;$i <= $_POST['sub_approve_type'];$i++){ $slecttaks = $afterapprovaltask['step_'.$i];
                    ?>
                    <div class="form-group row">
                        <span class="col-lg-2 form-label">Step <span class="form-label badge bg-info "><?php echo $i; ?></span> Task</span>
                        <div  class="col-lg-8">
                            <select aria-label="Select option" class="form-select js-example-basic-multiple"  name="after_approval_task[step_<?php echo $i; ?>][]" multiple>
                                <?php foreach($list as $lists){   ?>
                                    <option value="<?php echo $lists['id']; ?>" <?php echo in_array($lists['id'], $slecttaks)?"selected":""; ?>> <?php echo $lists['task_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php if($i == $_POST['sub_approve_type']){ ?>
                            <div class="col-lg-2">
                                <button type="button" class="ml-2 form-label label label-info border-0" data-bs-toggle="tooltip" data-placement="bottom" title="Add New task" onclick="addnewtask(<?php echo $_POST['rid']; ?>,'<?php  echo $_POST['process_type']?>','<?php echo $_POST['type']; ?>')"><i class="ti-plus"></i> New Task</button>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php } else { $slecttaks = $afterapprovaltask['step_1']; ?>
                    <div class="form-group row">
                        <span class="col-lg-2 form-label">Step 1 Task</span>
                        <div  class="col-lg-8">
                            <select aria-label="After Approval Task[step 1][]" class="form-select js-example-basic-multiple" name="after_approval_task[step_1][]"  multiple>
                                <?php foreach($list as $lists){ ?>
                                    <option value="<?php echo $lists['id']; ?>" <?php echo in_array($lists['id'], $slecttaks)?"selected":""; ?>> <?php echo $lists['task_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="ml-2 form-label label label-info border-0" data-bs-toggle="tooltip" data-placement="bottom" title="Add New task" onclick="addnewtask(<?php echo $_POST['rid']; ?>,'<?php  echo $_POST['process_type']?>','<?php echo $_POST['type']; ?>')"><i class="ti-plus"></i> New Task</button>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
    <?php }elseif($type == "afteraprovalemailnotification"){
        $list = getList('email_content');
        $list = json_decode($list,1);
        ?>
    <form method="post" id="afteraprovalemailnotification">

        <div class="modal-header">
            <h5 class="modal-title">Configration Action After Final Approval Email Notification</h5>
            <a href="index.php?option=COM_INSERT_EMAIL_CONTENTS" class="btn btn-info" target="_blank"><i class="ti-plus"></i> Create New Mail</a>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" >
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                <div class="form-group row">
                        <span class="col-lg-2 form-label mt-2">Send Email To</span>
                        <div  class="col-lg-4">
                            <?php $sendemailtype = ['requester_manager'=>'Requester Manager','everyone_approve'=>'Everyone Approve','everyone_till_approved'=>'Everyone Till Approved']?>
                            <select aria-label="Send Aproval Email To" class="form-select js-example-basic-single" name="send_aproval_email_to" >
                                <?php foreach($sendemailtype as $kky => $types){?>
                                    <option value="<?php echo $kky;?>"  <?php echo $workflowlist['send_aproval_email_to']==$kky?"selected":""; ?>><?php echo $types;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <div class="form-group row">
                    <div class="dt-responsive table-responsive">
                        <table id="new-cons" class="table table-striped table-bordered nowrap">
                            <thead><tr><th scope="col"></th><th scope="col">Subject</th></tr> </thead>
                            <tbody>
                                <?php foreach($list as $lists){ ?><tr><td><div class="form-radio"><div class="radio radio-inline">
                                    <label class="form-label">
                                        <input aria-label="After Approval Email" type="radio" name="after_approval_email" class="d-none" value="<?php echo $lists['id']; ?>"  <?php echo $workflowlist['after_approval_email']==$lists['id']?"checked":""; ?>>
                                        <i class="helper"></i>
                                    </label>
                                    </div></div></td><td><?php echo $lists['email_subject']; ?></td></tr> <?php
} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        <?php }elseif($type == "afteraprovalwebhook"){
            ?>
            <form method="post" id="afteraprovalwebhook">
            <div class="modal-header">
                <h5 class="modal-title">Configration Action After Final Approval Web Hook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="form-group row">
                        <div class="form-group">
                            <span>Web Hooks</span>
                            <textarea aria-label="After Approval Call Webhook" class="form-control form-control-lg" name="after_approval_call_webhook" spellcheck="false"><?php echo $workflowlist['after_approval_call_webhook']; ?>
                            </textarea>
                        </div>
                    </div>
                </div>
            </div>

        <?php }elseif($type == "afteraprovalcallfunction"){
            ?>
            <form method="post" id="afteraprovalcallfunction">
            <div class="modal-header">
                <h5 class="modal-
                title"> Configration Action After Final Approval Call Function</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="form-group row">
                        <div class="form-group">
                            <span>Call Function</span>
                            <textarea aria-label="After Approve Call Function" class="form-control form-control-lg" name="after_approve_call_function" spellcheck="false">      <?php echo $workflowlist['after_approve_call_function']; ?>    </textarea>
                        </div>
                    </div>
                </div>
            </div>

        <?php }elseif($type == "afterrejectionassigntask"){
            $list = getList('process_task_settings');
            $list = json_decode($list,1);
            $afterapprovaltask = json_decode($workflowlist['after_rejection_task'],1);
            ?>
            <form method="post" id="afterrejectionassigntask">
            <div class="modal-header">
                <h5 class="modal-title"> Configration Action After Rejection Assign Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <?php if($_POST['approval_type'] =='role_levels' || $_POST['approval_type'] =='user_hierarchical'){
                for($i=1;$i <= $_POST['sub_approve_type'];$i++){ $slecttaks = $afterapprovaltask['step_'.$i];
                    ?>
                    <div class="form-group row">
                        <span class="col-lg-2 form-label">Step <span class="form-label badge bg-info "><?php echo $i; ?></span> Task</span>
                        <div  class="col-lg-8">
                            <select aria-label="Select option" class="form-select js-example-basic-multiple"  name="after_rejection_task[step_<?php echo $i; ?>][]" multiple>
                                <?php foreach($list as $lists){   ?>
                                    <option value="<?php echo $lists['id']; ?>" <?php echo in_array($lists['id'], $slecttaks)?"selected":""; ?>> <?php echo $lists['task_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php if($i == $_POST['sub_approve_type']){ ?>
                            <div class="col-lg-2">
                                <button type="button" class="ml-2 form-label label label-info border-0" data-bs-toggle="tooltip" data-placement="bottom" title="Add New task" onclick="addnewtask(<?php echo $_POST['rid']; ?>,'<?php  echo $_POST['process_type']?>','<?php echo $_POST['type']; ?>')"><i class="ti-plus"></i> New Task</button>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php } else { $slecttaks = $afterapprovaltask['step_1']; ?>
                    <div class="form-group row">
                        <span class="col-lg-2 form-label">Step <span class="form-label badge bg-info ">1</span> Task</span>
                        <div  class="col-lg-8">
                            <select aria-label="After Rejection Task[step 1][]" class="form-select js-example-basic-multiple" name="after_rejection_task[step_1][]"  multiple>
                                <?php foreach($list as $lists){ ?>
                                    <option value="<?php echo $lists['id']; ?>" <?php echo in_array($lists['id'], $slecttaks)?"selected":""; ?>> <?php echo $lists['task_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="ml-2 form-label label label-info border-0" data-bs-toggle="tooltip" data-placement="bottom" title="Add New task" onclick="addnewtask(<?php echo $_POST['rid']; ?>,'<?php  echo $_POST['process_type']?>','<?php echo $_POST['type']; ?>')"><i class="ti-plus"></i> New Task</button>
                        </div>
                    </div>

                <?php } ?>
                    </div>
                </div>
            <?php }elseif($type == "afterrejectionemailnotification"){
                $list = getList('email_content');
                $list = json_decode($list,1);
                ?>
                <form method="post" id="afterrejectionemailnotification">
                <div class="modal-header">
                    <h5 class="modal-title"> Configration Action After Rejection Email Notification</h5>

                    <a href="index.php?option=COM_INSERT_EMAIL_CONTENTS" class="btn btn-info" target="_blank"><i class="ti-plus"></i> Create New Mail</a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <div class="form-group row">
                        <span class="col-lg-2 form-label mt-2">Send Email To</span>
                        <div  class="col-lg-4">
                            <?php $sendemailtype = ['requester_manager'=>'Requester Manager','everyone_approve'=>'Everyone Approve','everyone_till_approved'=>'Everyone Till Approved']?>
                            <select aria-label="Send Rejection Email To" class="form-select js-example-basic-single" name="send_rejection_email_to" >
                                <?php foreach($sendemailtype as $kky => $types){?>
                                    <option value="<?php echo $kky;?>"  <?php echo $workflowlist['send_rejection_email_to']==$kky?"selected":""; ?>><?php echo $types;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                        <div class="form-group row">
                            <div class="dt-responsive table-responsive">
                                <table id="new-cons_2" class="table table-striped table-bordered nowrap">
                                    <thead><tr><th scope="col"></th><th scope="col">Subject</th></tr> </thead>
                                    <tbody>
                                        <?php foreach($list as $lists){ ?><tr><td><div class="form-radio"><div class="radio radio-inline">
                                            <label class="form-label">
                                                <input aria-label="After Rejection Email" type="radio" name="after_rejection_email" class="d-none" value="<?php echo $lists['id']; ?>" <?php echo $workflowlist['after_rejection_email']==$lists['id']?"checked":""; ?>>
                                                <i class="helper"></i>
                                            </label>
                                            </div></div></td><td><?php echo $lists['email_subject']; ?></td></tr> <?php
} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php }elseif($type == "afterrejectionwebhook"){
                    $list = getList('email_content');
                    $list = json_decode($list,1);
                    ?>
                    <form method="post" id="afterrejectionwebhook">
                    <div class="modal-header">
                        <h5 class="modal-title"> Configration Action After Rejection Web Hook</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" >
                        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="form-group">
                                    <span>Web Hooks</span>
                                    <textarea aria-label="After Rejection Call Webhook" class="form-control form-control-lg " name="after_rejection_call_webhook" spellcheck="false">    <?php echo $workflowlist['after_rejection_call_webhook']; ?>       </textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php }elseif($type == "afterrejectioncallfunction"){
                    $list = getList('email_content');
                    $list = json_decode($list,1);
                    ?>
                    <form method="post" id="afterrejectioncallfunction">
                    <div class="modal-header">
                        <h5 class="modal-title"> Configration Action After Rejection Call Function</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" >
                        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="form-group">
                                    <span>Call Function</span>
                                    <textarea aria-label="After Rejection Call Function" class="form-control form-control-lg" name="after_rejection_call_function" spellcheck="false">     <?php echo $workflowlist['after_rejection_call_function']; ?>    </textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php }elseif($type=='custom_process'){  $steps = json_decode($workflowlist['sub_approve_type'],1); ?>

        <form method="post" id="custom_process">
            <input type="hidden" name="approval_type" value="<?php echo $_POST['approval_type']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title"> Configration Custom Steps</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" >
                        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group  stepdata">
                            <?php $k=1; foreach($steps as $key=>$steps){ ?>
                                <div class="row mt-1 deletestepid1 sttp">
                                    <div class="col-lg-1"></div>
                                    <div class="col-lg-2"> step - <span class="form-label badge bg-info "><?php echo $k; ?></span>  </div>
                                    <div class="col-lg-6"><select aria-label="Customsteps[]"  class="kt-dual-listbox form-select js-example-basic-single" name="customsteps[]">
                                        <option>Select Any One</option>
                                        <?php foreach($userlist as $userlistss){ ?>
                                        <option value="<?php echo $userlistss['id']; ?>" <?php echo $userlistss['id']==$steps?"selected":""; ?>><?php echo $userlistss['display_name'];?></option>
                                <?php } ?>
                                    </select></div>
                                    <div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti ti-trash text-danger" onclick="deletstep(1)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div>
                                </div>
                            <?php $k++;
} ?>
                                </div>
                                <button type="button" class="btn btn-info waves-effect waves-light col-lg-2" onclick="getstepdata()"><i class="ti-plus"></i> More Step</button>
                            </div>
                        </div>

                    <?php   } ?>

                    <div class="modal-footer">
                        <input type="hidden" name="Rid" value="<?php echo $_POST['rid']; ?>">
                        <input type="hidden" name="type" id="type" value="<?php echo $_POST['type']; ?>">
                         <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary mr-2" onclick="submitrom('<?php echo $_POST['type']; ?>')" >Submit</button>
                    </div>
                </form>

<div id="kt_modal_KTDatatable_remotem_task" class="modal fade" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-xl " >
        <div class="modal-content"  id="popupm_task">

        </div>
    </div>
</div>

                    <!-- data-table js -->
                    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
                    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
                    <script src="assets/pages/data-table/js/jszip.min.js"></script>
                    <script src="assets/pages/data-table/js/vfs_fonts.js"></script>
                    <script src="assets/pages/data-table/extensions/buttons/js/dataTables.buttons.min.js"></script>
                    <script src="assets/pages/data-table/extensions/buttons/js/buttons.flash.min.js"></script>
                    <script src="assets/pages/data-table/extensions/buttons/js/jszip.min.js"></script>
                    <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
                    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
                    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
                    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
                    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
                    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
                    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
                    <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>

                    <script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

                    <script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
                    </script>
                    <script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
                    <script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
                    <!-- Custom js -->
                    <script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
