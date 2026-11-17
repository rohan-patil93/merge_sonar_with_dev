<?php
    if(isset($_POST['submit'])){
        $data = $_POST;
        $data['application_table_name'] = "project_timesheets";
        $data['lock_type_list'] = json_encode($_POST['lock_list']);
        $month = explode("-",$_POST['month']);
        $data['month'] = $month['1'];
        $data['year'] = $month['0'];
        if($_POST['submit'] == 'Add'){
            unset($data['submit_value']);
            unset($data['submit_value_exit']);
            unset($data['submit']);
            unset($data['lock_list']);
            $data['current_status'] = $data['current_status']!="" ? $data['current_status'] : '0';
            $data['created_date'] = nowDate();
            $data['created_by'] = $_SESSION['userid'];
            $data['is_active'] = '0';
            $url = API."?mode=adddata&table=application_monthly_lock&method=post";
            $response = file_post_contents($url, $data, 'insert');
        }
        if($_POST['submit'] == 'Update'){
            unset($data['submit_value']);
            unset($data['submit_value_exit']);
            unset($data['submit']);
            unset($data['lock_list']);
            unset($data['hid']);
            $_SESSION['check'] = 1;
            $data['updated_date'] = nowDate();
            $data['updated_by'] = $_SESSION['userid'];
            $url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=application_monthly_lock&method=post";
            $response = file_post_contents($url, $data, 'update');
        }
        if ($response) {
                header("location:index.php?option=COM_LIST_APPLICATION_MONTHLY_LOCK");
        }
    }
    if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
        $vdata_details = array();
        $rid =  encodeId($_GET['Rid']);
        $rid = urldecode($rid);
        $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=application_monthly_lock";
        $response = callAPI($url);
        $role = json_decode($response,1);

        $checkven = json_decode($role['lock_type_list'],1);
    }
    $vendorwhc["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
    $vendorwhc['field'] = "id,company";
    $vendorwh = urlencode(json_encode($vendorwhc));
    $vendorurl = API."?mode=gettabledetailswithconditions&table=vendors&data=".$vendorwh;
    $vendordata = callAPI($vendorurl);
    $vendordata = json_decode($vendordata,1);
?>
<script type="text/javascript">
function checkAll(ele) {
     var checkboxes = document.getElementsByTagName('input');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
 function  vendorsshow(val) {
    if(val==1){
        $("#locktype").show();
    }else{
        $("#locktype").hide();
    }
 }

    function validateForm(){
        //alert('hai');
        var res = 'N';
        var z ='';
        var z = document.forms["myForm"]["lock_type"].value;
        z = z.trim();
        //alert('Lock type'+z);
        if (z == "" ){
            res = 'Y';
            document.getElementById('lock_type_err').style.display = "block";
        } else {
            document.getElementById('lock_type_err').style.display = "none";
        }
        var x ='';
        var x = document.forms["myForm"]["month"].value;
        x = x.trim();
        //alert('Lock month'+x);
        if (x == "" || x == "0"){
            res = 'Y';
            document.getElementById('month_err').style.display = "block";
        } else {
            document.getElementById('month_err').style.display = "none";
        }
        if (res == 'Y'){
            return false;
        }
    }
</script>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid ">
        <div class="card card-custom card-sticky j-content" id="kt_page_sticky_card">
            <div class="card-header">
                <h5><?php echo __("Application Monthly Lock");  ?></h5>
                <div class="header-right card-header-right">
                    <a  href="javascript:history.go(-1)" class="btn btn-primary waves-effect waves-ligh"> <i class="ti-arrow-left text-white" aria-hidden="true"></i> <?= __('Back') ?></a>
                </div>
            </div>
            <div class="card card-custom mt-2">
                <div class="card-body ">
                    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                        <div class="form-group row col-lg-12">
                            <div class="form-group col-lg-6">
                                <label class="form-label" for="lock_type"><?= __('Lock Type') ?>&nbsp;<span class="requried">*</span></label>
                                <div id="frist" class="mt-2">
                                    <select class="form-control form-select js-example-basic-single" id="lock_type" onchange="vendorsshow(this.value)" name="lock_type" aria-label="<?= htmlspecialchars(__('Lock Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <option value=""> <?= __('Select Any one'); ?> </option>
                                        <?php $listtype = array('0'=>"Month Wise Lock", '1'=>"Vendor Wise Lock");
                                         foreach ($listtype as  $lkey=>$listlock) {  ?>
                                            <option <?php if($lkey == $role['lock_type'] ){ ?> selected <?php } ?> value="<?php echo $lkey; ?>"><?php echo htmlspecialchars(__($listlock)); ?></option>
                                        <?php  } ?>
                                    </select>
                                </div>
                                <div style="display: none;color: red;" id="lock_type_err"> <?= __('Please Select Lock Type'); ?> </div>
                            </div>
                            <?php $lom =""; if($role['year']!="" && $role['month']!=""){
                                $lom = date("Y-m",strtotime($role['year']."-".$role['month']."-01"));
                            }   ?>
                            <div class="form-group col-lg-6">
                                <label class="form-label" for="month"><?= __('Month Lock') ?>&nbsp;<span class="requried">*</span></label>
                                <input type="month" name="month" value="<?php echo $lom; ?>" id="month" class="form-control mt-2" aria-label="<?= htmlspecialchars(__('Month Lock'), ENT_QUOTES, 'UTF-8'); ?>">
                                <div style="display: none;color: red;" id="month_err"> <?= __('Please Enter Month Lock'); ?></div>
                            </div>


                            <input type="hidden" name="company_id" id="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                            <div class="form-group row col-lg-12" id="locktype" <?php if(count((array)$checkven)>0){ echo "";
                            }else{ ?>  style="display:none;" <?php } ?>>

                                <div class="col-lg-9 mb-3"> <span class="form-label fw-bold font-weight-bold"> <?= __('Vendors'); ?> </span> </div>
                                <div class="col-lg-3 mb-3">
                                    <div class="checkbox-zoom zoom-primary mt-1">
                                        <label class="form-label" for="check_all_vendors">
                                            <input type="checkbox" id="check_all_vendors" name="check_all_vendors" value="" onclick="checkAll(this)" onkeydown="if(event.key==='Enter'){this.click();}" aria-label="<?= __('Check or Uncheck All') ?>">
                                            <span class="cr">
                                                 <i class="cr-icon icofont icofont-ui-check txt-primary" aria-hidden="true"></i>
                                             </span>  <span class="f-16"> <?= __('Check / Uncheck All') ?></span>
                                         </label>
                                    </div>
                                </div>

                                <?php foreach($vendordata as $vkey=>$vendor){ ?>
                                 <div class="form-group col-lg-3">
                                   <?php /**?> <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?php echo $vendor['company']; ?></span>
                                    <input type="checkbox"  class="js-switch" value="<?php echo $vendor['id']; ?>"  name="lock_list[<?php echo $vendor['id']; ?>]" / aria-label="Input">
                                    </div> <?php **/ ?>
                                    <div class="checkbox-zoom zoom-primary mt-1">
                                        <label class="form-label" for="vendor_<?php echo $vendor['id']; ?>">
                                            <input type="checkbox" id="vendor_<?php echo $vendor['id']; ?>" <?php if(in_array($vendor['id'],(array)$checkven) ){  echo "checked";
                                            } ?>  name="lock_list[<?php echo $vendor['id']; ?>]" value="<?php echo $vendor['id']; ?>" aria-label="<?php echo htmlspecialchars($vendor['company']); ?>">
                                            <span class="cr">
                                                 <i class="cr-icon icofont icofont-ui-check txt-primary" aria-hidden="true"></i>
                                             </span>  <span class="f-14"><?php echo htmlspecialchars($vendor['company']); ?> </span>
                                         </label>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-footer p-0">
                            <div class="row">
                            <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
                                <div class="col-lg-9 col-md-6 col-sm-6 col-6">
                                    <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                                    <input type="hidden" name="submit" value="Update" />
                                    <button id="kt_llogin_signup_submit" <?php if($status_res == '1'){ ?> disabled="disabled" <?php } ?> class="btn btn-primary mr-2" ><i class="la la-check" aria-hidden="true"></i><?= __('Update') ?></button>
                            <?php } else{ ?>
                                <div class="col-lg-9">
                                    <input type="hidden" name="submit" value="Add" />
                                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10"   name="submit_value_exit" value="exit"><i class="la la-check" aria-hidden="true"></i><?= __('Save & exit') ?></button>
                                    <?php if($_POST['option'] ==""){ ?>
                                    <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10"   name="submit_value" value="continue"><i class="la la-check-circle" aria-hidden="true"></i><?= __('Save & continue') ?></button>
                                    <?php } ?>
                                    <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close" aria-hidden="true"></i><?= __('Cancel') ?></button>
                            <?php } ?>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-6 text-right">
                                    <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash" aria-hidden="true"></i><?= __('Delete') ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>