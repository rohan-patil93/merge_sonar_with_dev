<?php
$_SESSION['apply_js']['multiselest'] = "1";
$File_Uploads_Path =  "uploads/all/";
if(isset($_POST['submit_continue']) && $_POST['submit_continue'] == "submit_policy"){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertexpensespolicies&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
    $response =$response1['res'];
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
        if($_GET['Rid'] !=""){
            $ridrecengtbils = deocdeId("id:".$response1['LASTID']);
            header("location:index.php?option=COM_INSERT_REIMBURSEMENT_POLICIES&Rid=".$ridrecengtbils);
        }
    }
    $_GET['TYPE'] = 1;
    ?>
    <script>
        document.onreadystatechange = function () {
            if (document.readyState == "complete"){

                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
            }
        }
    </script>
    <?php
}
if(isset($_POST['submit_continue']) && $_POST['submit_continue'] == "submit_category"){
    $postarray['policy_id'] = $_POST['rid'];
    $sendarray =array();
    foreach($_POST['category'] as $catk => $catv){
        $sendarray[$catk]['category_id']= $catk;
        $sendarray[$catk]['max_expense_limit']= $_POST['max_expense_limit'][$catk];
        $sendarray[$catk]['receipt_required_limit']= $_POST['receipt_required_limit'][$catk];
        $sendarray[$catk]['description_mandatory']= $_POST['description_mandatory_cat'][$catk];
        $sendarray[$catk]['old_id']= $_POST['old_id'][$catk];
        $sendarray[$catk]['policy_time_period']= $_POST['policy_time_period'][$catk];
    }
    $postarray['details'] = $sendarray;
    $postarray['company_id'] = $_SESSION['company_id'];
    $postarray['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertexpensespoliciescategory&method=post";
    $response = file_post_contents($url, $postarray, 'insert');
    $response = json_decode($response,1);
    $_GET['TYPE'] = 2;
    ?>
    <script>
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            }
        }
    </script>
    <?php
}
if(isset($_POST['submit_continue']) &&  $_POST['submit_continue']=='saveexpmileage'){
    $postaray['subtype'] = "saveexpmileage";
    foreach($_POST['exp_rowid'] as $rkey=>$rid){
        $postarray[$rkey]['company_id'] =  $_SESSION['company_id'];
        $postarray[$rkey]['expense_start_date'] = $_POST['expense_start_date'][$rkey];
        $postarray[$rkey]['name'] = $_POST['name'][$rkey];
        $postarray[$rkey]['expense_type'] = '0';
        $postarray[$rkey]['default_unit'] = $_POST['default_unit'];
        $postarray[$rkey]['expenses_category_id'] = '0';
        $postarray[$rkey]['designation_id'] = '0';
        $postarray[$rkey]['amount'] = $_POST['amount'][$rkey];
        $bill_date = str_replace('/', '-', $_POST['expense_start_date'][$rkey]);
        $bill_date =  date('Y-m-d', strtotime($bill_date));
        $postarray[$rkey]['expense_start_date'] = $bill_date;
        $postarray[$rkey]['policy_id'] = $_POST['rid'];
        $postarray[$rkey]['exp_rowid'] = $_POST['exp_rowid'][$rkey];

    }
    $postaray['data'] = $postarray;
    $url = API."?mode=insertexpensesmileagesperdiem&method=post";
    $response = file_post_contents($url, $postaray, 'insert');
    $_GET['TYPE'] = 3;
    ?>
    <script>
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            }
        }
    </script>
    <?php
}
if(isset($_POST['submit_continue']) &&  $_POST['submit_continue']=='saveexpperdiem'){
    $postaray['subtype'] = "saveexpperdiem";
    foreach($_POST['perdiem_rowid'] as $rkey=>$rid){
        $postarray[$rkey]['default_unit'] = $_POST['default_unit'];
        $postarray[$rkey]['company_id'] = $_SESSION['company_id'];
        $postarray[$rkey]['perdiem_rowid'] = $_POST['perdiem_rowid'][$rkey];
        $postarray[$rkey]['name'] = $_POST['perdiem_name'][$rkey];
        $postarray[$rkey]['expense_type'] = '1';
        $postarray[$rkey]['default_unit'] = $_POST['default_unit'];
        $postarray[$rkey]['expenses_category_id'] = '0';
        $postarray[$rkey]['designation_id'] = '0';
        $postarray[$rkey]['amount'] = $_POST['perdiem_amount'][$rkey];
        $postarray[$rkey]['expense_start_date'] = date("Y-m-d");
        $postarray[$rkey]['policy_id'] = $_POST['rid'];
    }
    $postaray['data'] = $postarray;
    $url = API."?mode=insertexpensesmileagesperdiem&method=post";
    $response = file_post_contents($url, $postaray, 'insert');
    $response = json_decode($response,1);
    $_GET['TYPE'] = 4;
    ?>
    <script>
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            }
        }
    </script>
    <?php
}

$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$rid = $erid['1'];
$caturl = API."?mode=getreimbursementpolicies&rid=".$rid;
$catdata = callAPI($caturl);
$polresult = json_decode($catdata,1);
$expense_categories = $polresult['expense_categories'];
foreach($expense_categories as $catdat){
    if($catdat['parent_category_id']==0){
        $parentcat[$catdat['id']]=$catdat;
    }else{
        $childcat[$catdat['parent_category_id']][]=$catdat;
    }
    $catgerorname[$catdat['id']]= $catdat['category_name'];
}
$policies_list = $polresult['policies'];
$policy_categ = $polresult['policy_categories'];
foreach($policy_categ as $policy_categ1){
    $policy_categories[$policy_categ1['category_id']] =  $policy_categ1;
    $role_cat[] = $policy_categ1['category_id'];
}
$expense_milelist = $polresult['expenses_mileages'];
$vehicleTypes = $polresult['vehicleTypes'];
$expense_perdiemlist = $polresult['expenses_perdiem'];
$countries = $polresult['countries'];

$type = $_GET['TYPE']!=""?$_GET['TYPE']:"1";
?>
<script type="text/javascript">
    function jobcate(catid){
        if($("#cat_"+catid ).prop('checked')) {
            $( "#cate_"+catid ).show("slow");
            $(".child_check_"+catid).prop('checked', false);
        }else{
            $( "#cate_"+catid ).hide("slow");
        }
    }
    function assignemployeelistnew(policy_number){
        $('#kt_modal_dynamic_popupxxl').modal("show");
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#dynamic_popupxxl").html(loader);
        jQuery.ajax({
            url: "views/custom_policy_assign_employee_popup.php",
            data:'policy_number='+policy_number,
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $(".js-example-basic-single").select2();

            },
            error:function (){}
        });
    }
</script>
<div class="card card-custom card-sticky j-content" id="kt_page_sticky_card">
    <div class="card-header">
        <h5>Expenses</h5>
        <div class="card-header-right">
        <?php if($rid != "" && (count((array)$policy_categ) !="0") && (count((array)$expense_milelist) !="0") && (count((array)$expense_perdiemlist) !="0")){ ?>
        <button type="button" onclick="assignemployeelistnew(<?php echo $rid; ?>)" class="btn btn-success mb-2 desktop-view">
            Assign Policy To Users
            </button> <?php } ?>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
        <div class="card-body">
            <?php if($error_message !=''){ ?>
                <div class="alert alert-danger background-danger alert-dismissible">
                    <strong><?php echo $error_message; ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
            <div class="row col-lg-12">
                <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
                    <li class="nav-item">
                        <a class="nav-link <?php if($type==1){ echo "active";
} ?>" id="profile-tab-1" data-bs-toggle="tab" href="#people-1" aria-controls="HOME">
                            <span class="nav-icon">
                                <i class="flaticon2-layers-1"></i>
                            </span>
                            <span class="nav-text">Policy Settings</span>
                        </a>
                    </li>
                    <li class="nav-item" >
                        <a class="nav-link <?php if($type==2){ echo "active";
} ?>" id="profile-tab-1_2" data-bs-toggle="tab" href="#manger-1" aria-controls="DATA">
                            <?php if(count((array)$policy_categ) =="0"){ echo '<i class="ti-help-alt f-14 text-danger"></i>';
}else{
                                echo '<i class="ti-thumb-up"></i>';
} ?>
                            <span class="nav-text">Categories</span>
                        </a>
                    </li>
                    <li class="nav-item" >
                        <a class="nav-link <?php if($type==3){ echo "active";
} ?>" id="profile-tab-1_3" data-bs-toggle="tab" href="#manger-2" aria-controls="manager">
                            <?php if(count((array)$expense_milelist) =="0"){ echo '<i class="ti-help-alt f-14 text-danger"></i>';
}else{
                                echo '<i class="ti-thumb-up"></i>';
} ?>
                            <span class="nav-text">Mileage</span>
                        </a>
                    </li>
                    <li class="nav-item" >
                        <a class="nav-link <?php if($type==4){ echo "active";
} ?>" id="profile-tab-1_4" data-bs-toggle="tab" href="#manger-3" aria-controls="manager">
                            <?php if(count((array)$expense_perdiemlist) =="0"){ echo '<i class="ti-help-alt  f-14 text-danger"></i>';
}else{
                                echo '<i class="ti-thumb-up"></i>';
} ?>
                            <span class="nav-text">Per Diem</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="">
                <div class="tab-content mb-5" id="myTabContent1">
                    <div class="tab-pane fade <?php if($type==1){ echo "show active";
} ?>" id="people-1" role="tabpanel" aria-labelledby="home-tab-1">
                        <div class="card card-custom">
                            <form class="kt-form" id="kt_form" method="post" action=""  name="myForm" enctype="multipart/form-data">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <span class="font-weight-bolder">Policy Name<span class="text-danger">*</span></span>
                                            <input aria-label="Policy Name" type="text" name="policy_name" class="form-control" value="<?php echo $policies_list['policy_name']; ?>" required>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="font-weight-bolder">Description</span>
                                            <textarea aria-label="Description" name="description" class="form-control" > <?php echo $policies_list['description']; ?></textarea>
                                        </div>
                                        <div class="form-group col-lg-4 w-full">
                                            <span class="font-weight-bolder d-block">Policy Admin<span class="text-danger">*</span></span>
                                            <select aria-label="Policy Admin" class="form-control form-select js-example-basic-single "  name="policy_admin">
                                                <option value="0"> Select Any One </option>
                                                <?php foreach($_SESSION['users'] as $ekey => $user_name){ ?>
                                                    <option <?php if(($policies_list['policy_admin'] =="" && $_SESSION['userid']== $ekey) || $policies_list['policy_admin']==$ekey){ echo "selected";
}  ?> value="<?php echo $ekey;?>" ><?php echo $user_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="font-weight-bolder">Expense Amount Limit<span class="text-danger">*</span></span>
                                            <input aria-label="Max Expense Limit Par" type="text" name="max_expense_limit_par" class="form-control text-right decimalnumberallowe" value="<?php echo $policies_list['max_expense_limit']; ?>" required>
                                        </div>
                                            <div class="form-group col-lg-4">
                                                <span class="font-weight-bolder">Receipt Required Limit<span class="text-danger">*</span></span><input aria-label="Receipt Required Limit Par" type="text" name="receipt_required_limit_par" class="form-control text-right decimalnumberallowe" value="<?php echo $policies_list['receipt_required_limit']; ?>" required>
                                            </div>
                                                <div class="form-group col-lg-4">
                                                    <div class="checkbox-fade fade-in-primary mt-4">
                                                        <label class="form-label">
                                                            <input aria-label="Checkbox" type="checkbox" id="checkbox" <?php echo $policies_list['description_mandatory']=='1'?"checked":"";?> name="description_mandatory" value="1">
                                                            <span class="cr">
                                                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                            </span>
                                                            <span>Description Mandatory</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                                        <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submit_continue" value="submit_policy"><i class="la la-check"></i>Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade <?php if($type==2){ echo "show active";
} ?> " id="manger-1" role="tabpanel" aria-labelledby="contact-tab-1">
                                <div class="card">

                                <?php $categroylimit = array("A"=>"Actual","PM"=>"Per Month","PY"=>"Per Year"); ?>
                                <div class="card-body">
                                <form method="post" action="">
                                    <div class="col-lg-12">
                                        <div class="checkbox-list">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <span class="form-label">Catgeory</span>
                                                </div>
                                                <div class="col-lg-2">
                                                    <span class="form-label">Policy Limit Period</span>
                                                </div>
                                                <div class="col-lg-2 text-center">
                                                    <span class="form-label">Amount</span>
                                                </div>
                                                <div class="col-lg-2 text-center">
                                                    <span class="form-label">Receipt Limit</span>
                                                </div>

                                            </div>
                                            <?php foreach ($parentcat as $parentkey=> $parent_categorie) {
                                                $policy_category = $policy_categories[$parent_categorie['id']];
                                                ?>
                                                <div class="mt-4 row bg-info">
                                                    <div class="col-lg-6" >
                                                        <div class="checkbox-fade fade-in-white mt-2">
                                                            <label class="form-label line-height24">
                                                                <input aria-label="Checkbox" type="checkbox" <?php if(in_array($parent_categorie['id'],(array)$role_cat)){ echo "checked";
} ?>  name="category[<?php echo $parent_categorie['id']; ?>]" id="cat_<?php echo $parent_categorie['id']; ?>" onclick="jobcate(<?php echo $parent_categorie['id']; ?>)" onkeydown="" />
                                                                <span class="cr float-left">
                                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                                </span>
                                                                <?php echo $parent_categorie['category_name'];  ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 w-full" >
                                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single"  name="policy_time_period[<?php echo $parent_categorie['id']; ?>]"  id="policy_time_period_<?php echo $parent_categorie['id']; ?>">
                                                            <?php foreach ($categroylimit as $kky=>$valkk) {  ?>
                                                                <option   <?php if($policy_category['policy_time_period'] == $kky){ ?> selected <?php
} ?>  value ="<?php echo $kky; ?>" > <?php echo $valkk; ?> </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2" >
                                                            <input aria-label="Max Expense Limit" type="text" class="form-control text-right decimalnumberallowe" placeholder="Max Expense Limit" name="max_expense_limit[<?php echo $parent_categorie['id']; ?>]"  value="<?php echo $policy_category['max_expense_limit']!=""?$policy_category['max_expense_limit']:$parent_categorie['max_expense_limit'];  ?>">

                                                    </div>
                                                        <div class="col-lg-2" >
                                                            <input aria-label="Receipt Required Limit" type="text" class="form-control text-right decimalnumberallowe" placeholder="Receipt Required Limit" name="receipt_required_limit[<?php echo $parent_categorie['id']; ?>]"  value="<?php echo $policy_category['receipt_required_limit']!=""?$policy_category['receipt_required_limit']:$parent_categorie['receipt_required_limit'];  ?>">


                                                            <input type="hidden"  value="<?php echo $policy_category['description_mandatory']!=""?$policy_category['description_mandatory']:$policies_list['description_mandatory']?>" name="description_mandatory_cat[<?php echo $parent_categorie['id']; ?>]">
                                                            <input type="hidden" value="<?php echo $policy_category['id']; ?>" name="old_id[<?php echo $parent_categorie['id']; ?>]">
                                                    </div>
                                                </div>
                                                <div class="ml-10" id="cate_<?php echo $parent_categorie['id']; ?>" >

                                                    <?php foreach ($childcat[$parent_categorie['id']] as $childcatdetails) {
                                                        $policy_category = $policy_categories[$childcatdetails['id']];
                                                        ?>
                                                    <div class="row mt-2">
                                                        <div class="col-lg-6  p-l-50">

                                                            <div class="checkbox-fade fade-in-primary ">
                                                                <label class="form-label line-height24">
                                                                    <input aria-label="Checkbox"  type="checkbox" class="child_check_<?php echo $parent_categorie['id']; ?>" <?php if(in_array($childcatdetails['id'],(array)$role_cat)){ echo "checked";
} ?>  name="category[<?php echo $childcatdetails['id']; ?>]" id="jobdes_<?php echo $childcatdetails['id']; ?>"  />
                                                                    <span class="cr float-left">
                                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                                    </span>
                                                                    <?php echo $childcatdetails['category_name'];  ?>
                                                                </label>
                                                            </div>

                                                        </div>

                                                        <div class="col-lg-2 w-full" >
                                                            <select aria-label="Select option" class="form-control form-select js-example-basic-single"  name="policy_time_period[<?php echo $childcatdetails['id']; ?>]"  >
                                                                <?php foreach ($categroylimit as $kky=>$valkk) {  ?>
                                                                    <option   <?php if($policy_category['policy_time_period'] == $kky){ ?> selected <?php
} ?>  value ="<?php echo $kky; ?>" > <?php echo $valkk; ?> </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2">
                                                                <input aria-label="Max Expense Limit" type="text" class="form-control text-right decimalnumberallowe" placeholder="Max Expense Limit" name="max_expense_limit[<?php echo $childcatdetails['id']; ?>]"  value="<?php echo $policy_category['max_expense_limit']!=""?$policy_category['max_expense_limit']:$childcatdetails['max_expense_limit'];  ?>">
                                                        </div>
                                                        <div class="col-sm-2">
                                                                <input aria-label="Receipt Required Limit" type="text" class="form-control text-right decimalnumberallowe" placeholder="Receipt Required Limit" name="receipt_required_limit[<?php echo $childcatdetails['id']; ?>]"  value="<?php echo $policy_category['receipt_required_limit']!=""?$policy_category['receipt_required_limit']:$childcatdetails['receipt_required_limit'];  ?>">
                                                            <input type="hidden" value="<?php echo $policy_category['description_mandatory']!=""?$policy_category['description_mandatory']:$policies_list['description_mandatory']?>" name="description_mandatory_cat[<?php echo $childcatdetails['id']; ?>]">

                                                            <input type="hidden" value="<?php echo $policy_category['id']; ?>" name="old_id[<?php echo $childcatdetails['id']; ?>]">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                                <button id="kt_login_signin_submit_continue" class="btn btn-primary mr-2"   name="submit_continue" value="submit_category"><i class="la la-check"></i>Submit</button>
                            </form>
                            </div>
                            </div>

                        </div>
                        <div class="tab-pane fade <?php if($type==3){ echo "show active";
} ?>" id="manger-2" role="tabpanel" aria-labelledby="contact-tab-1">
                            <?php include_once 'custom_insert_expenses_mileage.php'; ?>
                        </div>
                        <div class="tab-pane fade <?php if($type==4){ echo "show active";
} ?>" id="manger-3" role="tabpanel" aria-labelledby="contact-tab-1">
                            <?php include_once 'custom_insert_expenses_perdiem.php'; ?>
                        </div>

                    </div>
                </div>
            </div>
    </div>
