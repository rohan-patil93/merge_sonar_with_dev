<?php
$_SESSION['apply_js']['colorpicker'] = "1";
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['inputmask'] = 1;
$_SESSION['apply_js']['tour']=1;
$_SESSION['apply_css']['wizardcss'] = 1;
$card_sticky  ="card-sticky";
$poppadding ="";
$role = array();
if($_POST['option']!=""){
    $_GET['option'] = $_POST['option'];

    if(isset($_POST['Rid'])){ $_GET['Rid'] = $_POST['Rid'];
}
    if(isset($_POST['display'])){ $role = $_POST;
}
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$card_sticky = "";
$poppadding  = "style ='padding: 0px !important;'";
}

if($call_out_side !=""){
    $_GET['option'] = $call_out_side;
    $_GET['Rid'] =  $Rid;
}
$role['emp_id'] = $_SESSION['emp_id'];
$role['employee_id'] = $_SESSION['emp_id'];
$role['requested_by'] = $_SESSION['userid'];



$page_details= toGetTableNameBaseApplicationPagesList('insert_page_name', $_GET['option']);
if($_GET['option']=='COM_INSERT_EMPLOYEES'){ $page_details['application_table_name'] = 'employees';
}
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$page_details['application_table_name'];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$page_title1 = $list['page_title'];
$content = $list['help_text'] != '' ? $list['help_text'] : "Help Text";
$wizard_sitting  = $list['wizard'];
$wizard_details = $list['wizard_details'];
$wizard_details  = json_decode($wizard_details,1);
$accord = json_decode($list['additional_attributes'],1);
$add_url = $page_details['insert_page_name'];
$view_url = $page_details['list_page_name'];
if($_POST['reload_option']!=""){
    $view_url = $_POST['reload_option'];
}
$table_name = $list['application_table_name'];


include_once "new_from_submit.php";

$icon = "flaticon-add-circular-button";
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $vdata_details = array();
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=".$table_name;
    $response = callAPI($url);
    $role = json_decode($response,1);
    if(isset($role['additional_attributes']) && $role['additional_attributes']){
        $extara = json_decode($role['additional_attributes'],1);
        foreach ($extara as $extr_key => $extar) {
            $role[$extr_key] = $extar;
        }
    }


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

$title = $page_title1 !=""?$page_title1:viewTitle($_GET['option']);
$stausresult = statusData($table_name);
$lists = json_decode($list['list_attributes'],1);
$image = array(); $insert_arr = array(); $add_insert_arr = array(); $right = array(); $left = array(); $normal = array();
foreach ($lists as $key => $value) {

    if($value['insert_display_list']== 1 ){
        if($wizard_sitting == 0){
            $wizard [$value['insert_view_wizard']] [$value['display_postion']]['lable'] = $value['lable'];
            $wizard [$value['insert_view_wizard']] [$value['display_postion']]['type'] = $value['type'];
            $wizard [$value['insert_view_wizard']] [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
            $wizard [$value['insert_view_wizard']] [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
            $wizard [$value['insert_view_wizard']] [$value['display_postion']]['table'] = $key;
            $wizard_count[$value['insert_view_wizard']] = $dis [$value['insert_view_wizard']];

         }else{
            if($value['accordions'] == 'L'){
                $left [$value['display_postion']]['lable'] = $value['lable'];
                $left [$value['display_postion']]['type'] = $value['type'];
                $left [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
                $left [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
                $left [$value['display_postion']]['table'] = $key;
            }elseif($value['accordions'] == 'R'){
                $right [$value['display_postion']]['lable'] = $value['lable'];
                $right [$value['display_postion']]['type'] = $value['type'];
                $right [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
                $right [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
                $right [$value['display_postion']]['table'] = $key;
            }else{
                $dis [$value['display_postion']]['lable'] = $value['lable'];
                $dis [$value['display_postion']]['type'] = $value['type'];
                $dis [$value['display_postion']]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
                $dis [$value['display_postion']]['after'] = (isset($value['add_custom_field']) && $value['add_custom_field']!='') ? $value['add_custom_field']: '2';
                $dis [$value['display_postion']]['table'] = $key;
            }
        }
        if($value['mandatory']==1){
            if($value['type']=='select'){
                $mandatory[]="kt_select2_".$kl;
            }else{
                $mandatory[]=$key;
            }
        }
        if($value['type']=='select'){
            $kl = $kl+1;
        }
        if($value['type']=='image' || $value['type']=='file_upload'){
            $image[] = $key;
        }
        if($value['add_exter'] != 1){
            $insert_arr[]=$key;
        }else{
            $add_insert_arr[]=$key;
        }
        $dis_label[$key] = $value['lable'];
         }
    }

if(count((array)$dis) > 0){ ksort($dis);  $disarray['normal']=$dis;
}
if(count((array)$right) > 0){ ksort($right);  $disarray['right']=$right;
}
if(count((array)$left) > 0){ ksort($left);  $disarray['left']=$left;
}

$mandatory[] = 'from_date';
$mandatory[] = 'to_date';
$mandatory_json = json_encode($mandatory);
if(count((array)$insert_arr) != 0){
    $mandatory_str = implode(',', (array)$insert_arr);
}
if(count((array)$add_insert_arr) != 0){
    $add_mandatory_str = implode(',', (array)$add_insert_arr);
}
if(count((array)$image) != 0){
    $image = implode(',', (array)$image);
}

$dis_label = urlencode(json_encode($dis_label));

?>

<script type="text/javascript">
    function validateForm(){
        var data = <?php echo $mandatory_json; ?>;
        var res = 'N';
        if(typeof(data) == 'object'){
            for(var item in data) {
                var x ='';
                var x = document.forms["myForm"][data[item]].value;
                x = x.trim();
                if (x == "" || x == "0"){
                    res = 'Y';
                    document.getElementById(data[item]+'_err').style.display = "block";
                  } else {
                      document.getElementById(data[item]+'_err').style.display = "none";
                  }
            }
            if (res == 'Y'){
                return false;
            }
        }
    }
     function eywview(val){
         $('#'+val).attr('type', 'text');
     }
      function statuspopup(status,id){
     var table_name = document.getElementById('duplicate_table_name').value;
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                 $("#kt_modal_KTDatatable_remote").modal("show");
            },
            error:function (){}
        });
   }

    function displaystausdrop(id,table,status_id){
        $.ajax({
            url: "ajax.php",
            data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist").html(data);
            },
            error:function (){}
        });

    }


    function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#popup1").html(loader);
    $('#kt_modal_KTDatatable_remote').modal("show");
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
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
                console.log(response);
                var res = JSON.parse(response);
                window.location.reload();
                if(res.transaction_update.status=='sucess'){
                     window.location.reload();
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

</script>
<?php
    $workflowsteps = getWorkflowStatus($table_name,$role['id'],$role['current_status']);
    $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
    if(in_array($trip_res['current_status'], (array)$check_array)){
    }else{
        $disable = "disabled";
        $formdisable = "event_disabled";
    }
?>
<div class="pcoded-inner-content <?php echo $formdisable; ?>">
  <div class="main-body">
    <div class="page-wrapper">
     <!-- Page-body start -->
     <div class="page-body">
        <div class="alert alert-light alert-dismissible">
            <strong><?php echo $content; ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card 1 ">
                <?php if($role['id'] != ""){ echo $workflowsteps['current_status_ribbon'];
} ?>

                <div class="card-header table-card-header m-r-50">
                      <h5><?php echo $title;  ?></h5><button type="button" class="btn p-0 border-0 bg-transparent ti-info-alt f-20 text-primary" onclick="proceduressteps()" data-bs-toggle="tooltip" data-placement="bottom" title="Go Tour" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                      <div class="card-header-right table-card-header ">

                     <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
             <?php echo $workflowsteps['workflow_btn']; ?>

                    </div>
                   </div>
                   <?php echo  $dis_status=todisplaystatuslist(array('table'=>$table_name,'grand_total'=>'0','current_status'=>$role['current_status'])); ?>
            <!--begin::Portlet-->
            <div class="card-infisuite">
                 <?php echo $workflowsteps['breadcrumb'];?>
                    <div class="card-body p-4 <?php echo $card_sticky; ?>" id="kt_page_sticky_card">
                    <?php $wiz = 1; if(count((array)$wizard) >1){?>
                        <div class="wizard wizard-2" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
                                            <!--begin: Wizard Nav-->
                                            <div class="wizard-nav border-right py-8 px-8 py-lg-20 px-lg-10">
                                                <!--begin::Wizard Step 1 Nav-->
                                                <div class="wizard-steps">
                                                 <?php foreach ($wizard as $wiza) { ?>

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?php echo $wizard_details[$wiz-1] !="" ? $wizard_details[$wiz-1]: "Setup".$wiz; ?></h3>
                                                                <div class="wizard-desc">Setup Your Account Details</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->
                                                     <?php $wiz++;
} ?>

                                                </div>
                                            </div>
            <?php } ?>

$select= 1; $after=2; $mutiselet=1; $dateid=1; $img=1; $slider=1; $date_pick = array();

<?php if($wizard_sitting == 1){
    ?>
    <div class="form-group row ml-20 mr-20">
    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
        <div class="row">
                <?php

                include_once 'comman_from.php';

                }else{
                    include_once 'wizard_from.php';
                }

                ?>


            <?php     if(count((array)$date_pick) !=0){ $date_pick = implode(',',(array)$date_pick);
}  ?>
            <input type="hidden" name="in_date_pic" value="<?php echo $date_pick; ?>">
            <?php if(count((array)$in_currency_fileds) !=0 && $in_currency_fileds !=""){ $in_currency_fileds = implode(',',(array)$in_currency_fileds);
}  ?>
            <input type="hidden" name="in_currency_fileds" value="<?php echo $in_currency_fileds; ?>">
            <?php if(count((array)$in_phone_fileds) !=0 && $in_phone_fileds !=""){
                $in_phone_fileds = implode(',',(array)$in_phone_fileds);
}  ?>
            <input type="hidden" name="in_phone_fileds" value="<?php echo $in_phone_fileds; ?>">
            <input type="hidden" name="mandatory_str" value="<?php echo $mandatory_str; ?>">
            <input type="hidden" name="add_mandatory_str" value="<?php echo $add_mandatory_str; ?>">
            <?php $notification_users = togetnotificationuserslist();
             $notification_message = "New ".strtolower($title)." page has been created"; ?>
            <input type="hidden" name="notification_users" value="<?php echo $notification_users; ?>">
            <input type="hidden" name="notification_message" value="<?php echo $notification_message; ?>">
            <input type="hidden" name="dis_label" value="<?php echo $dis_label; ?>">
            <input type="hidden" name="image" value="<?php echo $image; ?>">
            <input type="hidden" name="duplicate_table_name" id="duplicate_table_name" value="<?php echo $table_name; ?>">
            <input type="hidden" name="insert_page_names" value="<?php echo $add_url; ?>">
            <input type="hidden" name="list_page_names" value="<?php echo $view_url; ?>">
            <input type="hidden" name="option" value="<?php echo $_POST['option']; ?>">

        <?php if($wizard_sitting == 1){
             include_once 'submit.php';
          }else{   ?>
          </div>
  </form>
  <?php }
 ?>


<?php if($_POST['option']!=""){ ?>

<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<?php } ?>
<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">

        </div>
    </div>
</div>
