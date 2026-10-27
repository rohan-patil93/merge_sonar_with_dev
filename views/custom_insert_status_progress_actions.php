<?php
$page_details= toGetTableNameBaseApplicationPagesList('insert_page_name', $_GET['option']);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$page_details['application_table_name'];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$content = $list['help_text'] != '' ? $list['help_text'] : "Help Text";
$add_url = "COM_INSERT_STATUS_PROGRESS_ACTIONS";
$view_url = "COM_LIST_STATUS_PROGRESS_ACTIONS";
$table_name = $list['application_table_name'];
 $_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submit'])){
    $data= array();
    $now_date = fromDate($role['from_date']);
    if($_POST['current_status']==0 ){
        $data['is_active'] = '0';
    }else{
        $data['is_active'] = '';
    }
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['current_status'] = 0;
    $_POST['status_date'] = nowDate();
    $_POST['from_date'] = date("Y-m-d", strtotime($_POST['from_date']));
    $_POST['to_date'] = date("Y-m-d", strtotime($_POST['to_date']));
    $_POST['notify_users'] = json_encode($_POST['notify_users'],1);
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API."?mode=statusprogressaction&method=post";
        $response = file_post_contents($url, $_POST, 'insert');

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
        // Response handled
    }
}
$role = array();
$icon = "flaticon-add-circular-button";
$status_table = $_GET['table'];
$progress = $_GET['progress'];
$module = $_GET['module'];
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $vdata_details = array();
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=".$table_name;
    $response = callAPI($url);
    $role = json_decode($response,1);
    $permissions = json_decode($role['permissions'],1);
    $icon = "flaticon-edit-1";
    $erid = explode(':', $rid);
         $vdata1["where"] = array('transaction_primary_1'=> $erid[1],'transaction_table'=>$table_name);
        $vdata1["field"] = "id,transaction_type,user_id,action_log,action_date,status_value";
        $vdata1['order'] = "id DESC";
        $vdata = urlencode(json_encode($vdata1));
        $vurl = API."?mode=gettabledetailswithconditions&table=".TBL_ACTION_LOG."&data=".$vdata;
        $vdata_details = callAPI($vurl);
        $vdata_details = json_decode($vdata_details,1);
}
$title = viewTitle($_GET['option']);
$stausresult = statusData($table_name);
$lists = json_decode($list['list_attributes'],1);
$status_progress_url =  API."?mode=togetstatusprogresstables";
$status_progress = callAPI($status_progress_url);
$status_progress = json_decode($status_progress,1);
if($status_table != ""){
    $data1["where"] = array('company_id'=>$_SESSION['company_id'],'table_name'=> $_GET['table']);
    $data = urlencode(json_encode($data1));
    $curl = API."?mode=gettabledetailswithconditions&table=status_progress&data=".$data;
    $cudata = callAPI($curl);
    $cudata = json_decode($cudata,1);

    $data12["where"] = array('company_id'=> $_SESSION['company_id'],'status_progress_id'=> $progress,'is_active'=> 0);
    if($module !=""){
        $data12["where"]['module_setup_id'] = $module;
    }

    $data23 = urlencode(json_encode($data12));
    $purl = API."?mode=gettabledetailswithconditions&table=status_progress_actions&data=".$data23;
    $pdata = callAPI($purl);
    $prdata = json_decode($pdata,1);
    $sprdata = $prdata['0'];
    $mulcheck = array();
    foreach ($prdata as  $pdata) {
        $mulcheck[] = $pdata['target_status'];
        $emailgroup = $pdata['email_groups'];
    }
    $emaildata["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
    $emaildata1 = urlencode(json_encode($emaildata));
    $emailurl = API."?mode=gettabledetailswithconditions&table=email_groups&data=".$emaildata1;
    $emailgroups = callAPI($emailurl);
    $emailgroups = json_decode($emailgroups,1);

    $tablefieldurl = API."?mode=filednames&table=".$_GET['table'];
    $tablefield = callAPI($tablefieldurl);
    $tablefield = json_decode($tablefield,1);

}
    $wh5["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
    $wh5["field"] = "id,module_id,module_name";
    $data5 = urlencode(json_encode($wh5));
    $url5 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data5;
    $mopdule_setup = callAPI($url5);
    $mopdulesetup = json_decode($mopdule_setup,1);
?>
<script type="text/javascript">
    function itemChange(val) {
         $.ajax({
        url: "ajax.php",
        data:'page=statusprogrssacton&company_id='+$('#company_id').val()+'&table_name='+val,
        type: "POST",
        success:function(data){
            console.log(data);
            $("#sec").html("<select aria-label='Status Progress' class='form-control kt-select2 js-example-basic-single' onchange='checkreload(this.value)' name='status_progress'><option>Select Any One</option>"+data+"</select>");

            $("#multiplechecks").html("<select aria-label='Status Progress' class='form-control kt-select2 js-example-basic-multiple'  onchange='checkreload(this.value)' name='status_progress' multiple><option>Select Any One</option></select>");
            $(".js-example-basic-single").select2();
            $(".js-example-basic-multiple").select2();

        },
        error:function (){}
        });
     }
     function checkreload(val){
         var table = $('#kt_select2_4').val();
         var module_setup_id = $('#module_setup_id').val();

         window.location.href = 'index.php?option=COM_INSERT_STATUS_PROGRESS_ACTIONS&table='+table+'&progress='+val+'&module='+module_setup_id;
     }
     function checkreloadmodule(val){
         var table = $('#kt_select2_4').val();
         var module_setup_id = $('#module_setup_id').val();
         window.location.href = 'index.php?option=COM_INSERT_STATUS_PROGRESS_ACTIONS&table='+table+'&progress='+val+'&module='+module_setup_id;
     }
</script>
<style type="text/css">
    .j-wrapper-65p, .j-wrapper-60p {
    width: 94% !important;
}
</style>
<div class="flex-column-fluid">

    <div class="container-fluid">
    <?php if($content !=''){ ?>

     <div class="alert alert-warning alert-dismissible">
      <strong><?php echo $content; ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
    <?php } ?>
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
    <h5><?php echo $title;  ?></h5>
    <div class="card-header-right">
             <button type="button" onclick="history.go(-1)" class="btn btn-primary waves-effect waves-ligh"><i class="ti-arrow-left text-white"></i> Back</button>
    </div>
</div>
<div class="card card-custom mt-2">
    <div class="card-body ">
        <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="form-group row">

                        <div class="form-group col-lg-4">
                            <span class="form-label">Transaction Type&nbsp;<span class="requried">*</span></span>
                            <div id="frist">
                                <select aria-label="Chh" class="form-control form-select js-example-basic-single" onchange="itemChange(this.value)"  id="kt_select2_4"  name="chh">
                                    <option value=""> Select Any One </option>
                                <?php foreach ($status_progress as  $selectvalue) {  ?>
                                        <option <?php if($status_table !="" && $status_table == $selectvalue['table_name'] ){ ?> selected <?php
} ?> value="<?php echo $selectvalue['table_name']; ?>"><?php echo viewTitle($selectvalue['table_name']); ?></option>
                                <?php  } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span class="form-label">Status Progress&nbsp;<span class="requried">*</span></span>
                            <div id="sec">
                                <select aria-label="Status Progress" class="form-control form-select js-example-basic-single" id="kt_select2_3" onchange="checkreload(this.value)" name="status_progress">
                                    <option value=""> Select Any one </option>
                                    <?php foreach ($cudata as  $cdata) {  ?>
                                            <option <?php if($progress== $cdata['id'] ){ ?> selected <?php
} ?> value="<?php echo $cdata['id']; ?>"><?php echo $cdata['name']; ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                        </div>
                        <?php if(!isset($sprdata['id'])){ ?>
                        <div class="form-group col-lg-4 w-full">
                          <span class="form-label">Module</span>
                          <select aria-label="Module Setup []" class="form-select js-example-basic-multiple" id="module_setup_id" name="module_setup_id[]" multiple>
                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                              <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $sprdata['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <?php }else{ ?>
                          <div class="form-group col-lg-4 w-full">
                          <span class="form-label">Module</span>
                          <select aria-label="Module Setup" class="form-select js-example-basic-single" id="module_setup_id_2" name="module_setup_id" onchange="checkreloadmodule(this.value)">
                            <?php foreach($mopdulesetup as $modulesetup){ ?>
                              <option value="<?php echo $modulesetup['module_id']; ?>" <?php echo $sprdata['module_setup_id'] == $modulesetup['module_id']?"selected":""; ?>><?php echo $modulesetup['module_name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
            <?php } ?>
            <div class="form-group col-lg-6">
                            <span class="form-label">Target Status&nbsp;<span class="requried">*</span></span>
                            <div id="multiplechecks">
                                <select aria-label="Target[]" class="form-control form-select js-example-basic-single" id="kt_select2_31" name="target[]" multiple="multiple">
                                    <option value=""> Select Any one </option>
                                    <?php foreach ($cudata as  $cdata) {  ?>
                                            <option <?php if(in_array($cdata['id'], $mulcheck)){ ?> selected <?php
} ?> value="<?php echo $cdata['id']; ?>"><?php echo $cdata['name']; ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label">Email Group&nbsp;<span class="requried">*</span></span>
                            <select aria-label="Email Groups" class="form-control form-select js-example-basic-single" id="kt_select2_1" name="email_groups" >
                                <option value=""> Select Any one </option>
                                <?php foreach ($emailgroups as  $emailgroup) {  ?>
                                        <option  <?php if($sprdata['email_groups']== $emailgroup['id']){ ?> selected <?php
} ?> value="<?php echo $emailgroup['id']; ?>"><?php echo $emailgroup['name']; ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <span class="form-label">Work Flow Field&nbsp;<span class="requried">*</span></span>
                            <select aria-label="Work Flow Field" class="form-control form-select js-example-basic-single" id="kt_select2_34" name="work_flow_field" >
                                <option value=""> Select Any one </option>
                                <?php foreach ($tablefield as  $field) {  ?>
                                        <option  <?php if($sprdata['work_flow_field']== $field ){ ?> selected <?php
} ?> value="<?php echo $field; ?>"><?php echo $field; ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <span classs="form-label">Send Notifications&nbsp;<span class="requried">*</span></span>
                        </div>
                        <input type="hidden" name="company_id" id="company_id" value="<?php echo $_SESSION['company_id']; ?>">
<?php include_once 'submit.php'; ?>
