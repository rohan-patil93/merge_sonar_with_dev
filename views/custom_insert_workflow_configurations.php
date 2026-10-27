<?php
$_SESSION['apply_js']['multiselest'] = '1';
if(isset($_POST['submit'])){
    $data = array();
    $postdata['id'] = $_POST['Rid'];
    $postdata['is_active'] = 0;
    $postdata['current_status'] = 0;
    $postdata['company_id'] = $_SESSION['company_id'];
    $postdata['process_name'] = $_POST['process_name'];
    $postdata['process_type'] = $_POST['process_type'];
    $postdata['process_description'] = $_POST['process_description'];
    $postdata['approval_type'] = $_POST['approval_type'];
    $postdata['initial_status'] = $_POST['initial_status'];
    if($_POST['Rid']==""){
        $postdata['created_by'] = $_SESSION['userid'];
    }else{
        $postdata['updated_by'] = $_SESSION['userid'];
    }
    $ret = array();
    foreach($_POST['status'] as $key => $filelds){
        $ret[] = array('status'=>$_POST['status'][$key],'followup_days'=>$_POST['followup_days'][$key],'auto_forword_days'=>$_POST['auto_forword_days'][$key],'work_flow_field'=>$_POST['work_flow_field'][$key],'action_status'=>urlencode($_POST['action_status'][$key]));
    }
    $postdata['approve_update_status'] = json_encode($ret);

    $nolevelarray = ['manager','Managers_Manager','user','email_groups','buddy','access_levels'];
    $postdata['sub_approve_type'] = $_POST['sub_approve_type'];
    $maxlimitarray = ['email_groups','access_levels','individual_employee_role'];
    if(in_array($_POST['approval_type'], $nolevelarray)){
        $postdata['approval_process'] = $_POST['approval_process'];
    }else{
        $postdata['approval_process'] = "";
    }
    $postdata['minimum'] = $_POST['minimum'];
    $postdata['process_priority'] = $_POST['process_priority'];
    $postdata['when_to_exicute'] = json_encode($_POST['when_to_exicute']);
    $postdata['criteria_sql'] = urlencode($_POST['criteria_sql']);
    $wheredata = array();
    foreach($_POST['wherefield'] as $key => $filelds){
        $filed = explode("-",$_POST['wherefield'][$key]);
        $listfileds = ["checkbox","radio","select","auto_select","multi_select"];
        $wheredata[$key] = array("wherefield"=>$_POST['wherefield'][$key],'whereoperators'=>$_POST['whereoperators'][$key],'whvaluedata'=>$_POST['whvaluedata'][$key],'whereconditions'=>$_POST['whereconditions'][$key],'field_name'=>$filed[0],'table_name'=>$filed[1],'field_type'=>$filed[2]);
    }
    $transaction_message = array('approve_message'=>$_POST['approve_message'],'reject_message'=>$_POST['reject_message'],'sendback_message'=>$_POST['sendback_message'],'completed_workflow_message'=>$_POST['completed_workflow_message'],'employee_message'=>$_POST['employee_message']);
    $postdata['transaction_message'] = json_encode($transaction_message);

    $postdata['criteria'] = json_encode($wheredata);
    $approvearray = array();
    foreach($_POST['approvefield'] as $key => $filelds){
        $approvearray[$key] = array("field"=>$key,'display_postion'=>$_POST['display_postion'][$key],'approveshow'=>$_POST['approveshow'][$key],'label'=>$_POST['approvefieldlable'][$key],'type'=>$_POST['approvetype'][$key]);
    }
    $postdata['approve_field'] = json_encode($approvearray);
    $url = API."?mode=insertworkflownew&method=post";
    $response = file_post_contents($url,$postdata,'insert');
    $response = json_decode($response,1);

    if($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }else{
        if($_POST['Rid']==""){
            header("location:index.php?option=COM_INSERT_WORKFLOW_CONFIGURATIONS&Rid=".deocdeId("id:".$response['LASTID']));
        }else{
            header("location:index.php?option=COM_INSERT_WORKFLOW_CONFIGURATIONS&Rid=".deocdeId("id:".$_POST['Rid']));
        }
    }
}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(":",$rid);
$rid = $rid['1'];
$geturl = API."?mode=getworkflowconfiguration&rid=".$rid."&process_type=".$workflowlist['process_type'];
$getapi = callAPI($geturl);
$getresult = json_decode($getapi,1);

$workflowlist = $getresult['workflow_configuration'];
$when_to_exicute =  json_decode($workflowlist['when_to_exicute'],1);
$criteria = json_decode($workflowlist['criteria'],1);
$transaction_message_res = json_decode($workflowlist['transaction_message'],1);
$approvefield_list = json_decode($workflowlist['approve_field'],1);
$listfields = json_decode($getresult['display_attributes']['list_attributes'],1);
$updatefields = json_decode($workflowlist['approve_update_status'],1);

$pagenamesrow = $getresult['display_attributes_list'];
$priorityresult= $getresult['priority'];
$statusrow  = $getresult['status_progress'];
$mopdulesetup = $getresult['module_setup'];


function subProcessType($val){
    $wh1 = $wh1 ?? '';
    $listval = $listval ?? [];
    $option = "<option value=''>Select Any</option>";

    if($val=="user"){
        $list = getList('application_users');
        $list = json_decode($list,1);
        foreach($list as $lists){
            $listval[$lists['id']] =  $lists['display_name'];
        }
    }elseif($val == 'email_groups'){
        $list = getList('email_groups');
        $list = json_decode($list,1);
        foreach($list as $lists){
            $listval[$lists['id']] =  $lists['name'];
        }
    }elseif($val == 'access_levels'){
        $list = getList('application_roles');
        $list = json_decode($list,1);
        foreach($list as $lists){
            $listval[$lists['id']] =  $lists['name'];
        }
    }elseif($val == 'role_levels' || $val =='user_hierarchical'){
        $wh1["where"] = array('genc_code'=>"HIERARCHICALLEVEL",'is_active'=>0,'company_id'=>$_SESSION['company_id']);
        $wh1["field"] = "genc_detail_code ,genc_detail_name";
        $data1 = urlencode(json_encode($wh1));
        $url1 = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$data1;
        $jsondecode = callAPI($url1);
        $list = json_decode($jsondecode,1);
        foreach($list as $lists){
            if($lists['genc_detail_name'] != "Others"){
                $listval[$lists['genc_detail_code']] =  $lists['genc_detail_name'];
            }
        }
    }elseif($val == 'buddy'){
        $wh1["where"] = array('genc_code'=>"EMPLOYEEBUDDIES",'is_active'=>0,'company_id'=>$_SESSION['company_id']);
        $wh1["field"] = "genc_detail_code ,genc_detail_name";
        $data1 = urlencode(json_encode($wh1));
        $url1 = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$data1;
        $jsondecode = callAPI($url1);
        $list = json_decode($jsondecode,1);
        foreach($list as $lists){
            if($lists['genc_detail_name'] != "Others"){
                $listval[$lists['genc_detail_code']] =  $lists['genc_detail_name'];
            }
        }
    }elseif($val == 'individual_employee_role'){
        $list = getList('employment_roles');
        $list = json_decode($list,1);
        foreach($list as $lists){
            $listval[$lists['id']] =  $lists['role_name'];
        }
    }

    return $listval;

}
$stausresult = ViewStatusData();
?>

<script type="text/javascript">

    function deletewhererow(id){
        $(".wharerow"+id).remove();
        buildQueryr()
    }
    var whmax = 100;
    function whereClouseSet(){
        var process_type = $(".process_type").val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=selectworkflowfiledlist&table_name='+process_type,
            type: "POST",
            success:function(data){
                var data  = JSON.parse(data);
                var rows = '<tr class="wharerow'+whmax+' allremove"><td><button type="button" class="btn p-0 border-0 bg-transparent ti ti-trash text-danger" onclick="deletewhererow('+whmax+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></td><td><select aria-label="Wherefield[]"  class="kt-dual-listbox form-select js-example-basic-single criteria_fields" onchange="wherefiledfun(this.value,'+whmax+')"  name="wherefield[]">'+data.option+'</select></td><td><div class="operationfileds'+whmax+'"><select aria-label="Whereoperators[]"  class="kt-dual-listbox form-select js-example-basic-single whereoperators" name="whereoperators[]"  onchange="buildQueryr()"><option value="="> = </option><option value="!=">!=</option><option value=">"> > </option><option value="<"> < </option><option value=">="> >= </option><option value="<="> <= </option></select></div></td><td><div class="textorfeild'+whmax+'"><input aria-label="Whvaluedata[]" class="form-control whvaluedata" id="" type="text" name="whvaluedata[]" onblur="buildQueryr()"></div></td><td><select aria-label="Whereconditions[]"  class="kt-dual-listbox form-select js-example-basic-single whereconditions"    name="whereconditions[]" onchange="buildQueryr()"><option value="AND">AND  </option><option value="OR">OR</option></select></td></tr>';
                $(".wherecondition").append(rows);
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
            },
            error:function (){}

        });
        whmax++;
    }

    function wherefiledfun(val,no){
        var textdorpdownlist = ["text","email","textarea","checkbox","radio","select","auto_select","multi_select","editor"];
        var splitval = val.split("-");
        var list = ["checkbox","radio","select","auto_select","multi_select"];
        if(jQuery.inArray(splitval[2], textdorpdownlist) != -1){
            var rowdrop = '<select aria-label="Whereoperators[]"  class="kt-dual-listbox form-select js-example-basic-single whereoperators" name="whereoperators[]" onchange="buildQueryr()"><option value="is"> IS </option><option value="isnot">Is Not</option><option value="contains">Contains</option><option value="does_not_contains">Does Not Contains</option></select>';
            $(".operationfileds"+no).html(rowdrop);
            $(".js-example-basic-single").select2();
            $(".js-example-disabled-results").select2();
        }else{


            var rowdrop = '<select aria-label="Whereoperators[]"  class="kt-dual-listbox form-select js-example-basic-single whereoperators" name="whereoperators[]"  onchange="buildQueryr()"><option value="="> = </option><option value="!=">!=</option><option value=">"> > </option><option value="<"> < </option><option value=">="> >= </option><option value="<="> <= </option></select>';
            $(".operationfileds"+no).html(rowdrop);
            $(".js-example-basic-single").select2();
            $(".js-example-disabled-results").select2();
        }
        if(jQuery.inArray(splitval[2], list) != -1) {
            var mxnu = $(".allremove").length-1;
            jQuery.ajax({
                url: "ajax.php",
                data:'page=getworkflowdisplayattributevalues&table_name='+splitval[1]+'&field_name='+splitval[0],
                type: "POST",
                success:function(data){
                    console.log(data);
                    var data  = JSON.parse(data);
                    var rows = '<select aria-label="Whvaluedata["  class="kt-dual-listbox form-select js-example-basic-multiple whvaluedata" name="whvaluedata['+mxnu+'][]" onchange="buildQueryr()" multiple required>'+data.option+'</select>';
                    $(".textorfeild"+no).html(rows);
                    $(".js-example-basic-multiple").select2();
                    $(".js-example-disabled-results").select2();
                }
            });
        }else if(splitval[2] == "date_pick"){
            var rows = '<input aria-label="Whvaluedata[]" class="form-control whvaluedata" id="" onblur="buildQueryr()" type="date" name="whvaluedata[]">';
            $(".textorfeild"+no).html(rows);
        }else{
            var rows = '<input aria-label="Whvaluedata[]" class="form-control whvaluedata" id="" onblur="buildQueryr()" type="text" name="whvaluedata[]">';
            $(".textorfeild"+no).html(rows);
        }
        buildQueryr()
    }

    function selectapprovetypefun(val,id){

        $("#minimums").val('');
        var showsublist = ["user","email_groups","access_levels","role_levels","user_hierarchical","buddy","individual_employee_role"];

        var showsublistanyall = ["user","manager","Managers_Manager","role_levels","user_hierarchical","custom_process","buddy"];

        if(jQuery.inArray(val, showsublistanyall) != -1){
            $(".approval_order_process").hide();
            $(".minimum").hide();
            $("#minimums").val("");
        }else{
            $(".approval_order_process").show();
            $(".minimum").hide();
            $("#minimums").val("");
        }

        if(jQuery.inArray(val, showsublist) != -1) {
            $("#sub_approve_type").show();
            $('.sub_approve_type ').attr('name', 'sub_approve_type');
        }else{
            $("#sub_approve_type").hide();
            $('.sub_approve_type ').attr('name', 'yourNewname');
        }
        if(val == "custom_process"){
            custompopups('custom_process',id);
        }else{
            jQuery.ajax({
                url: "ajax.php",
                data:'page=getapprovetypelist&val='+val,
                type: "POST",
                success:function(data){
                    console.log(data);
                    var data  = JSON.parse(data);
                    $(".sub_approve_type").html(data.option);
                }
            });
        }
        updateaApproveStatus(id);
    }

    function assigntaskcheck(id){
        if($('.assigntaskcheck').prop('checked')){
            $("#tasklist").show();
            jQuery.ajax({
                url: "ajax.php",
                data:'page=gettasklistworkflow',
                type: "POST",
                success:function(data){
                    var data  = JSON.parse(data);
                    $(".tasklist").html(data.option);
                    $(".js-example-basic-multiple").select2();
                    $(".js-example-disabled-results").select2();
                }
            });
        }else {
            $("#tasklist").hide();
        }

    }

function updateaApproveStatus(id=""){
    var process_type  = $(".process_type").val();
    var approval_type  = $(".approval_type").val();
    var sub_approve_type  = $(".sub_approve_type").val();
    jQuery.ajax({
        url: "views/custom_insert_workflow_configurations_status_ajax.php",
        data:'ajax=Y&process_type='+process_type+'&approval_type='+approval_type+'&sub_approve_type='+sub_approve_type+'&rid='+id,
        type: "POST",
        success:function(data){
            $("#approvedstatusselect").html(data);
        },
        error:function (){}
    });
}

    function updatefileds(val,id=""){
        var process_type  = $(".process_type").val();
        var approval_type  = $(".approval_type").val();
        var sub_approve_type  = $(".sub_approve_type").val();
        jQuery.ajax({
            url: "views/com_insert_workflow_update_fileds.php",
            data:'type='+val+'&process_type='+process_type+'&approval_type='+approval_type+'&sub_approve_type='+sub_approve_type+'&rid='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            },
            error:function (){}
        });
    }
    function custompopups(val,id=""){
        var process_type  = $(".process_type").val();
        var approval_type  = $(".approval_type").val();
        var sub_approve_type   = $(".sub_approve_type").val();
        jQuery.ajax({
            url: "views/custom_workflow_after_action_approval.php",
            data:'type='+val+'&process_type='+process_type+'&approval_type='+approval_type+'&sub_approve_type='+sub_approve_type+'&rid='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            },
            error:function (){}
        });

    }

    function approvalProcess(val){

        if(val == 'M'){
            $(".minimum").show();
            $("#minimums").val('');
        }else if(val == 'E'){
            $(".minimum").hide();
            $("#minimums").val('');
        }else{
            $(".minimum").hide();
            $("#minimums").val('1');
        }
    }

    function buildQueryr(){
        console.log("work");
        var query = "SELECT ";
        var from =  $(".process_type").val();
        var criteria_fields= [];
        var criteria_fields_type = ['is','isnot','contains','does_not_contains','starts_with'];
        $(".criteria_fields").each(function( index ) {
            var cval = $(this).val().split("-");
            criteria_fields[index] = cval[0];
        });
        var whereoperators= [];
        $(".whereoperators").each(function( index ) {
            whereoperators[index] = $(this).val();
        });
        var whvaluedata = [];
        $(".whvaluedata").each(function( index ) {
            whvaluedata[index] = $(this).val();
        });
        var whereconditions  = [];
        $(".whereconditions").each(function( index ) {
            whereconditions[index] = $(this).val();
        });
        var where = "";
        $(".criteria_fields").each(function( index ){
            if(index != 0){
                where += " "+whereconditions[index]+" ";
            }else{
                where +=" WHERE ";
            }
            if(jQuery.inArray(whereoperators[index], criteria_fields_type) != -1) {
                if(whereoperators[index]=='is'){
                    where += criteria_fields[index]+" = "+whvaluedata[index];
                }else if(whereoperators[index]=='isnot'){
                    where += criteria_fields[index]+" != "+whvaluedata[index];
                }else if(whereoperators[index]=='contains'){
                    where += criteria_fields[index]+" IN("+whvaluedata[index]+")";
                }else if(whereoperators[index]=='does_not_contains'){
                    where += criteria_fields[index]+" NOT IN("+whvaluedata[index]+")";
                }else if(whereoperators[index]=='starts_with'){
                    where += criteria_fields[index]+" LIKE '%"+whvaluedata[index]+"%'";
                }
            }else{
                where += criteria_fields[index]+" "+whereoperators[index]+" "+whvaluedata[index];
            }
        });
        query +=" * FROM  "+from;
        query += where;
        $("#criteria_qeury").val(query);
        console.log(query);
    }

function copyfiled(element) {
  var $temp = $("<input aria-label="Input field">");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}

function getfield(id){
    $("#process_fields").hide();
    $("#session_fields").hide();
    $("#employee_fields").hide();
    if(id == 'T'){
        $("#process_fields").show('slow');
    }else if(id == 'S'){
        $("#session_fields").show('slow');
    }else if(id == 'E'){
        $("#employee_fields").show('slow');
    }
}

function selectActionStatus(seq){
    var actionstatusarray = $("#action_status_"+seq).val();
    var selectkey = $("#actionstatus_"+seq).val();
    var selectval= $('#actionstatus_'+seq+' option:selected').text()
    jQuery.ajax({
        url: "ajax.php",
        data:'page=setarraystatus&selectkey='+selectkey+'&selectval='+selectval+'&oldarray='+actionstatusarray,
        type: "POST",
        success:function(data){
            console.log(data);
            var dataval = JSON.parse(data);
            console.log(dataval.status_array);
            var stringify = JSON.stringify(dataval.status_array);
            console.log(stringify);
            $("#action_status_"+seq).val(stringify);
            $("#view_action_status_"+seq).html(dataval.label);
        },
        error:function (){}
    });

}
</script>

        <div class="card">

    <?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>
            <div class="card-header">
                <h5>Workflow</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row ml-20 mr-20 ">
                    <form class=""  method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="Rid" value="<?php echo $rid; ?>">
                        <input type="hidden" class="hidaprove_updatefields" name="hidaprove_updatefields" value='<?php echo $workflowlist['approve_update_status']; ?>'>
                        <div class="row">
                            <div class="row">
                                 <div class="form-group col-lg-4 w-full">
                                      <span class="form-label">Module</span>
                                      <select aria-label="Module Setup" class="form-select js-example-basic-single" id="module_setup_id" name="module_setup_id">
                                        <?php foreach($mopdulesetup as $modulesetup){ ?>
                                          <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $workflowlist['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                                        <?php } ?>
                                      </select>
                                    </div>
                                <div class="form-group col-lg-4">
                                    <span class="form-label">Process Type</span>
                                    <select aria-label="Process Type" class="form-select js-example-basic-single process_type" name="process_type" >
                                        <option value="0" data-select2-id="2"> Select Any One</option>
                                        <?php foreach($pagenamesrow as $process){ ?>
                                            <option value="<?php echo $process['application_table_name']; ?>" <?php echo $workflowlist['process_type']==$process['application_table_name']?"selected":"" ?> ><?php echo $process['page_title']==""?ucwords(str_replace("_"," ",($process['application_table_name']))):$process['page_title']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-4">
                                    <span class="form-label">Process Name</span>
                                    <input aria-label="Process Name" class="form-control"  type="text" name="process_name" value="<?php echo $workflowlist['process_name']; ?>">
                                </div>
                                <div class="form-group col-lg-4">
                                    <span class="form-label">Description</span>
                                    <textarea aria-label="Process Description" class="form-control " name="process_description"><?php echo $workflowlist['process_description']; ?></textarea>
                            </div>

                            <div class="form-group col-lg-4">
                                <span class="form-label">When To Exicute</span></br>
                                <div class="checkbox-fade fade-in-primary">
                                    <label class="form-label">
                                        <input aria-label="When To Exicute[]" type="checkbox" name="when_to_exicute[]" value="record_create" <?php  echo (in_array("record_create", $when_to_exicute) || !isset($_GET['Rid']))?"checked":""; ?>>
                                        <span class="cr">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span>Record Creation</span>
                                    </label>
                                    <label class="form-label m-l-20">
                                        <input aria-label="When To Exicute[]" type="checkbox"  name="when_to_exicute[]" value="record_edit" <?php  echo (in_array("record_edit", $when_to_exicute))?"checked":""; ?>>
                                        <span class="cr">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span>Record Edit</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Priority</span>

                                <input aria-label="Process Priority" class="form-control"  type="text" name="process_priority" value="<?php echo $workflowlist['process_priority']; ?>">
                            </div>

                        </div>
                        <?php if(isset($_GET['Rid'])){ ?>
                            <div class="row">
                                <div class="card-block accordion-block">
                                    <div class="accordion-box ui-accordion ui-widget ui-helper-reset" id="single-open" role="tablist">

                                        <a class="accordion-msg waves-effect waves-dark f-14 f-w-600"><i class="ti-settings"></i> Criteria</a>
                                        <div class="accordion-desc mt-2">
                                            <div class="row mt-2 ">
                                                <input type="hidden" id="criteria_qeury" name="criteria_sql" type="hidden" value="<?php echo urldecode($workflowlist['criteria_sql']); ?>">
                                                <table class="table table-hover " style="border-radius: 10px;" id="invoiceItem">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" class=""></th>
                                                            <th scope="col" class="" >Field </th>
                                                            <th scope="col"> Operators</th>
                                                            <th scope="col" class="" >Value</th>
                                                            <th scope="col" class="" >Condition</th>
                                                        </tr> </thead>
                                                        <tbody class="wherecondition">
                                                            <?php
                                                            $operationarrayno = array('=','!=','>','<','>=','<=');
                                                            $operationarraystring = array('is','isnot','contains','does_not_contains');
                                                            $textdorpdownlist = ["text","email","textarea","checkbox","radio","select","auto_select","multi_select","editor"];
                                                            $list = ["checkbox","radio","select","auto_select","multi_select"];
                                                            foreach($criteria as $key=>$criteria){
                                                            ?>

                                                            <tr class="wharerow<?php echo $key;?> allremove">
                                                                <td><i class="ti ti-trash text-danger" onclick="deletewhererow('<?php echo $key;?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td>
                                                                <td>
                                                                    <select aria-label="Select option"  class="kt-dual-listbox form-select js-example-basic-single criteria_fields" onchange="wherefiledfun(this.value,'<?php echo $key; ?>')"  name="wherefield[]" onchange="buildQueryr()">
                                                                        <?php foreach($listfields as $keyy=>$aray){
                                                                            $valuefield = $keyy."-".$workflowlist['process_type']."-".$aray['type'];
                                                                            ?>
                                                                            <option value='<?php echo $valuefield; ?>' <?php echo $valuefield==$criteria['wherefield']?"selected":""; ?>><?php echo $aray['lable'] ?></option>
                                                                        <?php }    ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="operationfileds<?php echo $key; ?>">
                                                                        <select aria-label="Whereoperators[]"  class="kt-dual-listbox form-select js-example-basic-single whereoperators" name="whereoperators[]" onchange="buildQueryr()">
                                                                            <?php if(in_array($criteria['field_type'], $textdorpdownlist)){ $operationarr= $operationarraystring;
}else{ $operationarr =$operationarrayno;
} foreach($operationarr as $valoperationarrayno){ ?>
                                                                                <option value="<?php echo $valoperationarrayno; ?>" <?php echo $valoperationarrayno==$criteria['whereoperators']?"selected":""; ?>><?php echo $valoperationarrayno; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="textorfeild<?php echo $key; ?>">
                                                                        <?php if(in_array($criteria['field_type'], $list)){
                                                                            $listval = dyanamicselectdata($criteria['table_name'],$criteria['field_name']);
                                                                            ?>
                                                                            <select aria-label="Select option"  class="kt-dual-listbox form-select js-example-basic-multiple whvaluedata" name="whvaluedata[<?php echo $key; ?>][]" onchange="buildQueryr()" multiple required>
                                                                                <?php
                                                                                foreach($listval as $keys=>$aray){ ?>
                                                                                    <option value='<?php echo $keys; ?>' <?php echo  in_array($keys, $criteria['whvaluedata'])?"selected":""; ?>><?php echo $aray; ?></option>
                                                                                <?php     } ?>
                                                                            </select>
                                                                        <?php }elseif($criteria['field_type'] == "date_pick"){ ?>
                                                                            <input aria-label="Whvaluedata[]" class="form-control whvaluedata" id="" type="date" name="whvaluedata[]" value="<?php echo $criteria['whvaluedata']; ?>" onblur="buildQueryr()">
                                                                        <?php }else{ ?>
                                                                            <input aria-label="Whvaluedata[]" class="form-control whvaluedata" id="" type="text" name="whvaluedata[]" value="<?php echo $criteria['whvaluedata']; ?>" onblur="buildQueryr()">
                                                                        <?php } ?>
                                                                    </div>
                                                                </td>
                                                                <td><select aria-label="Whereconditions[]"  class="kt-dual-listbox form-select js-example-basic-single whereconditions" name="whereconditions[]" onchange="buildQueryr()">
                                                                    <option value="AND" <?php echo $criteria['whereconditions']=='AND'?"selected":""; ?>>AND  </option>
                                                                    <option value="OR" <?php echo $criteria['whereconditions']=='OR'?"selected":""; ?>>OR</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-info waves-effect waves-light col-lg-2" onclick="whereClouseSet()"><i class="ti-plus"></i> Add Criteria</button>
                                        </div>
                                    </div>

                            <a class="accordion-msg waves-effect waves-dark f-14 f-w-600"><i class="ti-user"></i> Who Should Approve</a>
                                    <div class="accordion-desc mt-2">
                                        <?php $roleprecesstypearray = array('user'=>'User','email_groups'=>'Email Group','access_levels'=>'Access Levels','role_levels'=>'Employement Role','manager'=>'Manager','Managers_Manager'=>'Managers Manager','individual_employee_role'=>'Individual Employee Role','buddy'=>'Buddy','individual_employee_role'=>'Individual Employee Role','user_hierarchical'=>'User Hierarchical','custom_process'=>'Custom Process');    ?>
                                        <div class="row mt-4">
                                            <div class="col-lg-3 form-group w-full">
                                                <span class="form-label d-block">Approval Type</span>
                                                <select aria-label="Approval Type" class="form-select js-example-basic-single approval_type" name="approval_type" onchange="selectapprovetypefun(this.value,<?php echo $rid; ?>)" >
                                                    <option value="">Select Any</option>
                                                    <?php foreach($roleprecesstypearray as $key=>$val){ ?>
                                                        <option value="<?php echo $key; ?>" <?php echo $workflowlist['approval_type']==$key?"selected":""; ?>> <?php echo $val; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?php
                                            $showsublist = ["user","email_groups","access_levels","role_levels","user_hierarchical","buddy","individual_employee_role"];
                                            ?>
                                            <div class="col-lg-3 form-group w-full" id="sub_approve_type" style="display:<?php echo (in_array($workflowlist['approval_type'], $showsublist) && $workflowlist['approval_type']!="" )?"":"none"?>">
                                                <span class="form-label d-block">Sub Type</span>
                                                <select aria-label="Sub Approve Type" class="form-select js-example-basic-single sub_approve_type" name="sub_approve_type" onchange="updateaApproveStatus('<?php echo $rid; ?>')">

                                                    <?php $sublist = subProcessType($workflowlist['approval_type']);
                                                    foreach($sublist as $keyk=>$aray){  ?>
                                                        <option value='<?php echo $keyk; ?>'   <?php echo $workflowlist['sub_approve_type']==$keyk?"selected":""; ?>><?php echo $aray; ?></option>";

                                                    <?php    } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-4 ">
                                            <?php $showsublistanyall = ["user","manager","Managers_Manager","role_levels","user_hierarchical","custom_process"]; ?>
                                            <div class="col-lg-5 approval_order_process" style="display:<?php echo (!in_array($workflowlist['approval_type'], $showsublistanyall) && $workflowlist['approval_type']!="" )?"":"none"?>">
                                                <div class="form-radio">
                                                    <div class="radio radio-inline">
                                                        <label class="form-label">
                                                            <input aria-label="Approval Process" type="radio" name="approval_process" value="A" class="d-none" <?php echo $workflowlist['approval_process']=="A"?"checked":""; ?> onchange="approvalProcess(this.value)">
                                                            <i class="helper"></i>Anyone
                                                        </label>
                                                    </div>
                                                    <div class="radio radio-inline">
                                                        <label class="form-label">
                                                            <input aria-label="Approval Process" type="radio" name="approval_process" value="E"  class="d-none" <?php echo $workflowlist['approval_process']=="E" || $workflowlist['approval_process']==""?"checked":""; ?> onchange="approvalProcess(this.value)">
                                                            <i class="helper"></i>Everyone
                                                        </label>
                                                    </div>
                                                    <div class="radio radio-inline">
                                                        <label class="form-label">
                                                            <input aria-label="Approval Process" type="radio" name="approval_process" value="M" class="d-none" <?php echo $workflowlist['approval_process']=="M"?"checked":""; ?> onchange="approvalProcess(this.value)">
                                                            <i class="helper"></i>Minimum
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-7 row" >
                                                <div class="col-lg-2 minimum" style="display:<?php echo (!in_array($workflowlist['approval_type'], $showsublistanyall) && $workflowlist['approval_process']=='M')?"":"none"; ?>">
                                                    <input aria-label="Minimum" class="form-control " id="minimums" type="text" name="minimum" value="<?php echo $workflowlist['minimum']; ?>" placeholder="Eg. 11">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2" id="approvedstatusselect">

                                            <?php
                                            if($workflowlist['approval_type'] !=""){
                                    include_once "views/custom_insert_workflow_configurations_status_ajax.php";
                                } ?>
                                        </div>
                                    </div>


                                    <a class="accordion-msg waves-effect waves-dark f-14 f-w-600"> <i class="ti-thumb-up"></i> Action's Upon Approve</a>
                                    <div class="accordion-desc mt-2">

                                        <div class="row form-group">
                                            <span>Configration Action After Final Approval</span>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afteraprovassigntask',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-notepad"></i></div>
                                                        <p class="text-facebook f-w-700">Assign Task</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afteraprovalemailnotification',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-email"></i></div>
                                                        <p class="text-facebook f-w-700">Email Notification</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afteraprovalwebhook',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-link"></i></div>
                                                        <p class="text-facebook f-w-700">Call Webhooks</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afteraprovalcallfunction',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-settings"></i></div>
                                                        <p class="text-facebook f-w-700">call function</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="accordion-msg waves-effect waves-dark f-14 f-w-600"> <i class="ti-thumb-down"></i> Action's Upon Reject</a>
                                    <div class="accordion-desc mt-2">

                                        <div class="row form-group">
                                            <span>Configration Action After Rejection</span>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afterrejectionassigntask',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-notepad"></i></div>
                                                        <p class="text-facebook f-w-700">Assign Task</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afterrejectionemailnotification',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-email"></i></div>
                                                        <p class="text-facebook f-w-700">Email Notification</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afterrejectionwebhook',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-link"></i></div>
                                                        <p class="text-facebook f-w-700">Call Webhooks</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6 mt-2" onclick="custompopups('afterrejectioncallfunction',<?php echo $rid; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <div class="card social-card">
                                                    <div class="card-body text-center">
                                                        <div class="text-facebook m-b-20"><i class="ti-settings"></i></div>
                                                        <p class="text-facebook f-w-700">call function</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                        <a class="accordion-msg waves-effect waves-dark f-14 f-w-600"> <i class="ti-email"></i>  Approval Feilds </a>
                        <div class="accordion-desc mt-2">
                            <?php
                            $divarray = (count((array)$listfields)/2);
                            $fieldliastapprove = array_chunk($listfields,$divarray,true);?>

                            <div class="row">
                              <div class="col-lg-12 col-xl-6">
                                 <div class="table-responsive">
                                    <table class="table table-xs">
                                       <thead>
                                               <tr>
                                                   <th scope="col">Label</th>
                                                   <th scope="col">Sequence</th>
                                                   <th scope="col">Action</th>
                                               </tr>
                                       </thead>
                                       <tbody>
                                           <?php foreach($fieldliastapprove[0] as $fieldname =>$liastapprove){ ?>
                                          <tr>
                                            <td>
                                                 <input type="hidden" name="approvefield[<?php echo $fieldname; ?>]" value="<?php echo $fieldname; ?>">
                                                 <input type="hidden" name="approvefieldlable[<?php echo $fieldname; ?>]" value="<?php echo $liastapprove['lable']; ?>">
                                                 <input type="hidden" name="approvetype[<?php echo $fieldname; ?>]" value="<?php echo $liastapprove['type']; ?>">
                                                 <?php echo $liastapprove['lable']; ?>
                                            </td>
                                            <td>
                                                 <input aria-label="Input field" type="text" class="form-control form-control-lg" name="display_postion[<?php echo $fieldname; ?>]" value="<?php echo $approvefield_list[$fieldname]['display_postion']!=""?$approvefield_list[$fieldname]['display_postion']:$liastapprove['display_postion']; ?>" >
                                            </td>
                                            <td>
                                                <input aria-label="Checkbox" type="checkbox" class="js-switch" <?php echo $approvefield_list[$fieldname]['approveshow']=="Y"?"checked":""; ?> name="approveshow[<?php echo $fieldname; ?>]"  value="Y" />
                                            </td>
                                          </tr>
                                         <?php } ?>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                              <div class="col-lg-12 col-xl-6">
                                 <div class="table-responsive">
                                    <table class="table m-0 table-xs">
                                       <thead>
                                               <tr>
                                                   <th scope="col">Label</th>
                                                   <th scope="col">Sequence</th>
                                                   <th scope="col">Action</th>
                                               </tr>
                                       </thead>
                                       <tbody>
                                           <?php foreach($fieldliastapprove[1] as $fieldname =>$liastapprove){ ?>
                                           <tr>
                                            <td>
                                                 <input type="hidden" name="approvefield[<?php echo $fieldname; ?>]" value="<?php echo $fieldname; ?>">
                                                 <input type="hidden" name="approvefieldlable[<?php echo $fieldname; ?>]" value="<?php echo $liastapprove['lable']; ?>">
                                                 <input type="hidden" name="approvetype[<?php echo $fieldname; ?>]" value="<?php echo $liastapprove['type']; ?>">
                                                 <?php echo $liastapprove['lable']; ?>
                                            </td>
                                            <td>
                                                 <input aria-label="Input field" type="text" class="form-control form-control-lg" name="display_postion[<?php echo $fieldname; ?>]" value="<?php echo $approvefield_list[$fieldname]['display_postion']!=""?$approvefield_list[$fieldname]['display_postion']:$liastapprove['display_postion']; ?>" >
                                            </td>
                                            <td>
                                                <input aria-label="Checkbox" type="checkbox" class="js-switch" <?php echo $approvefield_list[$fieldname]['approveshow']=="Y"?"checked":""; ?> name="approveshow[<?php echo $fieldname; ?>]"  value="Y" />
                                            </td>
                                          </tr>
                                         <?php } ?>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>


                                    <a class="accordion-msg waves-effect waves-dark f-14 f-w-600"> <i class="ti-email"></i> Messages</a>
                                    <div class="accordion-desc mt-2">
                                        <div class="row mb-4">
                                            <div class="col-lg-4 text-center"><button type="button" class="btn p-0 border-0 bg-transparent form-label label label-lg label-primary" onclick="getfield('T')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Process Fields</button></div>
                                            <div class="col-lg-4 text-center"><button type="button" class="btn p-0 border-0 bg-transparent form-label label label-lg label-primary" onclick="getfield('S')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Session Fields</button></div>
                                            <div class="col-lg-4 text-center"><button type="button" class="btn p-0 border-0 bg-transparent form-label label label-lg label-primary" onclick="getfield('E')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Employee Fields</button></div>
                                        </div>
                                        <div class="row mb-4 card">

                                            <div class="col-lg-8"  id="process_fields" style="display:none">
                                                <?php $listfields['process_name'] = array('lable'=>'Process Name -'.$workflowlist['process_type']);  $nmt = 1; foreach($listfields as $keyy=>$aray){
                                                    $valuefields = "{[".$workflowlist['process_type'].".".$keyy."]}";
                                                    ?>
                                                    <p id="fieldspro_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
                                                    <span class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldspro_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo ucwords($aray['lable']); ?> <i class="ti-layers"></i></span>
                                                <?php $nmt++;
}    ?>
                                            </div>
                                            <div class="col-lg-12 "  id="employee_fields" style="display:none">
                                                <?php $nm0 = 1; foreach($_SESSION['employee'] as $keyy=>$aray){
                                                    $valuefields = "{[employee.".$keyy."]}";
                                                    ?>
                                                    <p id="fieldsemp_<?php echo $nm0;?>" class="d-none"><?php echo $valuefields; ?></p>
                                                    <span class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldsemp_<?php echo $nm0;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo ucwords($keyy); ?> <i class="ti-layers"></i></span>
                                                <?php $nm0++;
}    ?>
                                            </div>
                                            <div class="col-lg-12 " id="session_fields" style="display:none">

                                                <div class="col-lg-12 col-xl-12 mt-4">
                                                                <ul class="nav nav-tabs md-tabs">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" data-bs-toggle="tab" href="#homeuser" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">User</a>
                                                                        <div class="slide"></div>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" data-bs-toggle="tab" href="#profileemployee" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Employee</a>
                                                                        <div class="slide"></div>
                                                                    </li>
                                                                </ul>
                                                                <!-- Tab panes -->
                                                                <div class="tab-content card-block">
                                                                    <div class="tab-pane active" id="homeuser" role="tabpanel">
                                                                        <?php $nmt = 1; foreach($_SESSION['user'] as $keyy=>$aray){
                                                    $valuefields = "{[session_user.".$keyy."]}";
                                                    ?>
                                                    <p id="fieldssesiuser_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
                                                    <span class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldssesiuser_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo ucwords($keyy); ?> <i class="ti-layers"></i></span>
                                                <?php $nmt++;
}    ?>
                                                                    </div>
                                                                    <div class="tab-pane" id="profileemployee" role="tabpanel">
                                                                        <?php $nmt = 1; foreach($_SESSION['employee'] as $keyy=>$aray){
                                                    $valuefields = "{[session_employee.".$keyy."]}";
                                                    ?>
                                                    <p id="fieldssesiemp_<?php echo $nmt;?>" class="d-none"><?php echo $valuefields; ?></p>
                                                    <span class="btn btn-info btn-mini waves-effect waves-light m-1 f-w-600" onclick="copyfiled('#fieldssesiemp_<?php echo $nmt;?>')" data-bs-toggle="tooltip" data-placement="bottom" title="Click Copy To used" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo ucwords($keyy); ?> <i class="ti-layers"></i></span>
                                                <?php $nmt++;
}    ?>
                                                                    </div>
                                                                </div>
                                                            </div>

                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="form-group col-lg-6">
                                                <span>Submit/Approve</span>
                                                <textarea aria-label="Approve Message" class="form-control " name="approve_message"><?php echo $transaction_message_res['approve_message']; ?></textarea>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <span>Reject</span>
                                                <textarea aria-label="Reject Message" class="form-control " name="reject_message"><?php echo $transaction_message_res['reject_message']; ?></textarea>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <span>Send Back</span>
                                                <textarea aria-label="Sendback Message" class="form-control " name="sendback_message"><?php echo $transaction_message_res['sendback_message']; ?></textarea>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <span>Completed Workflow</span>
                                                <textarea aria-label="Completed Workflow Message" class="form-control " name="completed_workflow_message"><?php echo $transaction_message_res['completed_workflow_message']; ?></textarea>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <span>Information</span>
                                                <textarea aria-label="Employee Message" class="form-control " name="employee_message"><?php echo $transaction_message_res['employee_message']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-9">
                            <input type="hidden" name="submit" value="Add">
                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="exit"><i class="la la-check"></i> <?php if(isset($_GET['Rid'])){ echo "Save";
}else{ echo "Add Criteria";
}?>  </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

