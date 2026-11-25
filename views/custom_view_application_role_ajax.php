<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if($_POST['ajax_call'] == "Y"){
    $rid = $_POST['roll_id'];
    $app_id = $_POST['app_id'];
    $vurl = API."?mode=userlistwithrole&ajax_call=Y&roleid=".$rid."&app_id=".$app_id;
    $vdata_details = callAPI($vurl);
    $result  = json_decode($vdata_details,1);
    $application_process_pages_single = $result['application_process_pages'][0];
    if($_POST['sync'] != 'Y'){
        $transaction_access_permission = $result['transaction_access_permission'];
        $transaction_access_permission_id = $transaction_access_permission['id'];
    }else{
        $transaction_access_permission_id = $_POST['transaction_access_permission_id'];
    }

}
$application_users = $result['application_users'];
$not_role_application_users = $result['not_role_application_users'];
$approved_user_criteria = $result['approved_user_criteria'];
$capexopexres = $result['capexopexres'];
$item_categories = $result['item_categories'];
$optiondetails = getOptionPageTableName($application_process_pages_single['application_page_name']);
$application_table_name = $optiondetails['application_table_name'];
$application_roles = $result['application_roles'];
$transactiontype = $result['transactiontype'];
$neuserlistary = $not_role_application_users;
if(count((array)$application_users) != 0){
    $neuserlistary = array_merge($application_users,$not_role_application_users);

}
$processpagename =mb_convert_case($application_process_pages_single['name'], MB_CASE_TITLE, "UTF-8");
?>
<script type="text/javascript">
    function showUnAssignUsers(){
        if($("#showunassignuser").is(":checked")){
            $('.assignrole').each(function(){
                console.log($(this).val());
                if($(this).is(':checked')){
                    $(".tr"+$(this).val()).hide();
                }else{
                    $(".tr"+$(this).val()).show();
                }

            });
        }else{
            $('.assignrole').each(function(){
                console.log($(this).val());
                if($(this).is(':checked')){
                    $(".tr"+$(this).val()).show();
                }else{
                    $(".tr"+$(this).val()).hide();
                }
            });
        }
    }
    function allowUserRole(userid){
        if($("#allowuserrole"+userid).is(':checked')){
            $(".allowuserrole_"+userid).css({'pointer-events': '','opacity': ''});
        }else{
            $(".allowuserrole_"+userid).css({'pointer-events': 'none','opacity': '0.4'});
        }
        allowCreatAccess(userid);
    }
    function checkedBox(clss,userid){
        var sumcheck = 0;
        $('.'+clss+userid).each(function(){
            if($(this).is(':checked')){
                sumcheck++;
            }
        });
        if(sumcheck == 2){
                swal({
                    title: <?php echo json_encode(__('Both Not Selected'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                    text: <?php echo json_encode(__('please Select Any One Excludes/Includes'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: <?php echo json_encode(__('Ok'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                    closeOnConfirm: false
                });
                 $('.'+clss+userid).prop('checked', false);
        }
    }
    function onBeHalfType(val,uid){
        $("#foruser_"+uid).hide();
        if(val == "U"){
            $("#foruser_"+uid).show();
        }
    }
    function allowCreatAccess(uid){
        if($(".createcheckedbox"+uid).is(':checked')){
            $(".aca_"+uid).css({'pointer-events': '','opacity': ''});
        }else{
            $(".aca_"+uid).css({'pointer-events': 'none','opacity': '0.4'});
        }
    }

</script>
<div class="Card">

    <div class="card-header">
        <h5><?php echo $processpagename; ?> <?= __('Access Users'); ?></h5>
        <div class="card-header-right">
            <div class="label-main" >
                <span class="form-label label label-md label-primary">V</span> <?= __('View'); ?>
            </div>
            <div class="label-main">
                <span class="form-label label label-md label-primary">C</span> <?= __('Create'); ?>
            </div>
            <div class="label-main">
                <span class="form-label label label-md label-primary">E</span> <?= __('Edit'); ?>
            </div>
            <div class="label-main">
                <span class="form-label label label-md label-primary">D</span> <?= __('Delete'); ?>
            </div>
            <div class="label-main">
                <span class="form-label label label-md label-primary">P</span> <?= __('Print'); ?>
            </div>
            <div class="label-main">
                <span class="form-label label label-md label-primary">I</span> <?= __('import'); ?>
            </div>
            <div class="label-main">
                <span class="form-label label label-md label-primary">L</span> <?= __('Download'); ?>
            </div>
        </div>
    </div>
    <div class="card-body" >
        <div class="row">
            <div class="col-lg-3">
                <span class="form-label  f-w-600"><?= __('Show Un-Assign Role Users'); ?></span> <span class="m-l-10"><input aria-label="<?= htmlspecialchars(__('Showunassignuser'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="js-success-sm" id="showunassignuser" onchange="showUnAssignUsers()"></span>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-4 text-right">

                <button class="btn btn-primary mr-2" onclick="getTransactionAccessPermission('<?php echo $rid; ?>','<?php echo $app_id; ?>','Y','<?php echo $transaction_access_permission_id; ?>')" type="button" ><i class="ti-loop"></i> <?= __('Synchronize Users Roles'); ?></button>
            </div>
            <div class="col-lg-3">
                <div class="input-group input-group-button mb-0" >
                    <input aria-label="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="dynamicfilterhideshow" data-id='listfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="input-group-text addon-style">
                        <span class=""><span class="ti-search text-white mt-2"></span></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <form method="post" class="">
        <div class="card-body  custom-scroll" style=" height: 600px;" >
            <input type="hidden" name="application_process_pages_id" value="<?php echo $application_process_pages_single['application_process_pages_id']; ?>">
            <input type="hidden" name="module_id" value="<?php echo $application_roles['application_module']; ?>">
            <input type="hidden" name="role_id" value="<?php echo $application_process_pages_single['role_id']; ?>">
            <input type="hidden" name="application_page_name" value="<?php echo $application_process_pages_single['application_page_name']; ?>">
            <input type="hidden" name="role_short_code" value="<?php echo $application_process_pages_single['role_short_code']; ?>">
            <input type="hidden" name="rid" value="<?php echo $transaction_access_permission_id; ?>">
            <input type="hidden" name="transaction_table" value="<?php echo $application_table_name; ?>">
            <input type="hidden" name="submit" value="submit">
            <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
            <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">
        <div>
            <table class="table table-hover table-bordered m-b-0" >
                <thead>
                    <tr>
                        <th scope="col" class="text-center align-middle" rowspan="2"><?= __('User Detail'); ?></th>
                        <th scope="col" class="text-center align-middle" rowspan="2"><span class="d-block"><?= __('Assign'); ?> </span><?= __('Role'); ?></th>
                        <th scope="col" colspan="9" class="text-center"><?= __('Permisions'); ?> <span class="text-muted f-14 d-block"><?= __('(if you want to exclude one or more access)'); ?></span></th>
                        <th scope="col" colspan="7" class="text-center" ><?= __('Setups'); ?> <span class="text-muted f-14 d-block"><?= __('(Document Access Setups User Can Create)'); ?></span></th>
                    </tr>
                    <tr>
                        <th scope="col" class="text-center p-1"><?= __('List'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Create'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Edit'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Delete'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Print'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Import'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Download'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Approval'); ?></th>
                        <th scope="col" class="text-center p-1"><?= __('Analytic'); ?></th>
                        <th scope="col" class="p-1"><?php echo $processpagename; ?> <?= __('for Their'); ?></th>
                        <th scope="col" class="p-1"><?php echo $processpagename; ?> <?= __('for CAPEX/OPEX'); ?></th>
                        <th scope="col" class="p-1"><?php echo $processpagename; ?> <?= __('Type'); ?></th>
                        <th scope="col" class="p-1"><?php echo $processpagename; ?> <?= __('Item Category'); ?></th>
                        <th scope="col" class="text-right p-1"><?php echo $processpagename; ?> <?= __('upto'); ?></th>
                        <?php if($application_table_name == 'requisitions'){ ?>
                            <th scope="col" class="p-1"><?= __('On Behalf'); ?></th>
                            <th scope="col" class="p-1"><?php echo $processpagename; ?> <?= __('Only For Following Users'); ?></th>
                        <?php } ?>
                        <th scope="col" class="p-1"><?= __('Backdate Limit'); ?></th>
                        <th scope="col" class="p-1"><?= __('From Date'); ?></th>
                    </tr>
                </thead>
                <tbody id="listfilter_2">
                    <?php
                    $i = 1;
                    foreach($neuserlistary as $userlists){
                        if(!isset($application_users[$userlists['id']])){
                            $stylecss = "pointer-events: none; opacity:0.4";
                        }
                        $approvedusercriteria = $approved_user_criteria[$userlists['id']];
                        ?>
                        <tr class="tr<?php echo $userlists['id'];?>" id="userfillter" style="<?php echo $application_users[$userlists['id']]?"":"display: none"; ?>">
                            <td class="p-2">
                                <div class="align-middle">
                                    <?php if($userlists['user_photo']!="") { ?>
                                        <img src="uploads/all/<?php echo $userlists['user_photo']; ?>"  class="img-radius img-50 align-top m-r-15" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php } else { ?>
                                        <span  alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($userlists['display_name']); ?></span>
                                    <?php } ?>

                                    <input type="hidden" name="usernamelist[]" value="<?php echo $userlists['display_name']; ?>">

                                    <div class="d-inline-block width73">
                                        <h6><?php echo  mb_convert_case($userlists['display_name'], MB_CASE_TITLE, "UTF-8"); ?>
                                    </h6>
                                    <p class="text-muted m-b-0"><?php echo $userlists['user_email']; ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="p-1 align-middle text-center">
                            <span>
                                <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" id="allowuserrole<?php echo $userlists['id'];?>" class="js-success-sm assignrole" name="rolepermision[<?php echo $userlists['id']; ?>]" value="<?php echo $userlists['id'];?>" <?php echo $application_users[$userlists['id']]?"checked":""; ?> onchange="allowUserRole('<?php echo $userlists['id'];?>')">
                            </span>
                        </td>
                        <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                            <?php $checkedbox = "";
                            if ($approvedusercriteria['permission_view'] == '0' || ($application_process_pages_single['permission_view'] == '0' && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                            <div class="checkbox-fade fade-in-primary m-0">
                                <label class="form-label  m-0">
                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="listcheckedbox<?php echo $userlists['id'];?>"  name="excludes[list][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                    <span class="cr m-0">
                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                            <?php $checkedbox =$stylecsscreat = "";
                            if ($approvedusercriteria['permission_insert'] == '0' || ($application_process_pages_single['permission_insert'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } else {
                                $stylecsscreat = "pointer-events: none; opacity:0.4";
} ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="createcheckedbox<?php echo $userlists['id'];?>"  name="excludes[create][<?php echo $userlists['id'];?>]" onchange="allowCreatAccess('<?php echo $userlists['id'];?>')" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center p-1  align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <?php $checkedbox = "";
                                if ($approvedusercriteria['permission_update'] == '0' || ($application_process_pages_single['permission_update'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="editcheckedbox<?php echo $userlists['id'];?>"  name="excludes[edit][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <?php $checkedbox = "";
                                if ($approvedusercriteria['permission_delete'] == '0' || ($application_process_pages_single['permission_delete'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="deletecheckedbox<?php echo $userlists['id'];?>"  name="excludes[delete][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <?php $checkedbox = "";
                                if ($approvedusercriteria['permission_print'] == '0' || ($application_process_pages_single['permission_print'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="printcheckedbox<?php echo $userlists['id'];?>"   name="excludes[print][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <?php $checkedbox = "";
                                if ($approvedusercriteria['permission_import'] == '0' || ($application_process_pages_single['permission_import'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="importcheckedbox<?php echo $userlists['id'];?>"   name="excludes[import][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <?php $checkedbox = "";
                                if ($approvedusercriteria['permission_download'] == '0' || ($application_process_pages_single['permission_download'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="downloadcheckedbox<?php echo $userlists['id'];?>"  name="excludes[download][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <?php $checkedbox = "";
                                if ($approvedusercriteria['permission_approval'] == '0' || ($application_process_pages_single['permission_approval'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="approvalcheckedbox<?php echo $userlists['id'];?>"  name="excludes[approval][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center p-1 align-middle allowuserrole_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <?php $checkedbox = "";
                                if ($approvedusercriteria['permission_analytic'] == '0' || ($application_process_pages_single['permission_download'] == 0 && count((array)$transaction_access_permission) == 0)) { $checkedbox = "checked"; } ?>
                                <div class="checkbox-fade fade-in-primary m-0">
                                    <label class="form-label  m-0">
                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" class="analyticcheckedbox<?php echo $userlists['id'];?>"  name="excludes[analytic][<?php echo $userlists['id'];?>]" <?php echo $checkedbox; ?> >
                                        <span class="cr m-0">
                                            <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <!---------------------------------------------------------------------------->
                            <td class="p-2 w-full allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>" >
                                <input type="hidden" name="dasu_id[<?php echo $userlists['id']; ?>]" value="<?php echo $approvedusercriteria['id']; ?>">
                                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="approved_type[<?php echo $userlists['id']; ?>]">
                                    <option value="" ><?= __('For All'); ?></option>
                                    <option value="DIV" <?php echo $approvedusercriteria['approved_type'] == "DIV"?"selected":""; ?>><?= __('Divisions'); ?></option>
                                    <option value="D" <?php echo $approvedusercriteria['approved_type'] == "D"?"selected":""; ?>><?= __('Departments'); ?></option>
                                    <option value="cost_center_id" <?php echo $approvedusercriteria['approved_type'] == "cost_center_id"?"selected":""; ?>><?= __('Cost Centers'); ?></option>
                                    <option value="L" <?php echo $approvedusercriteria['approved_type'] == "L"?"selected":""; ?>><?= __('Locations'); ?></option>
                                </select>
                            </td>
                            <td class="p-2 w-full allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>">
                                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="opex_cpex[<?php echo $userlists['id']; ?>]">
                                    <option value="" ><?= __('Both'); ?></option>
                                    <?php foreach($capexopexres as $opex_cpex){
                                        $selected= "";
                                        if($approvedusercriteria['opex_cpex'] == $opex_cpex['genc_detail_code']){
                                            $selected ="selected";
                                        }
                                        echo '<option value="'.$opex_cpex['genc_detail_code'].'" '.$selected.'>'.htmlspecialchars(__(strtoupper($opex_cpex['genc_detail_name'])), ENT_QUOTES, 'UTF-8').'</option>';
                                    } ?>
                                </select>
                            </td>
                            <td class="p-2 w-full allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>">
                                <?php
                                $transaction_type = array();
                                if($approvedusercriteria['transaction_type'] !=""){
                                    $transaction_type = json_decode($approvedusercriteria['transaction_type'],1);
                                }
                                ?>

                                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-multiple" name="transaction_type[<?php echo $userlists['id']; ?>][]" multiple>
                                    <?php foreach($transactiontype as $transactiontypes){
                                        $selected= "";
                                        if(in_array($transactiontypes['genc_detail_code'],(array)$transaction_type)){
                                            $selected ="selected";
                                        }
                                        echo '<option value="'.$transactiontypes['genc_detail_code'].'" '.$selected.'>'.htmlspecialchars(__($transactiontypes['genc_detail_name']), ENT_QUOTES, 'UTF-8').'</option>';
                                    } ?>
                                </select>
                            </td>
                            <td class="p-2 w-full allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>">
                                <?php if($approvedusercriteria['item_category'] !=""){
                                    $item_category = json_decode($approvedusercriteria['item_category'],1);
                                }
                                ?>
                                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-multiple" name="item_category[<?php echo $userlists['id']; ?>][]" multiple>
                                    <?php foreach($item_categories as $item_categorie){
                                        $selected= "";
                                        if(in_array($item_categorie['id'],(array)$item_category)){
                                            $selected ="selected";
                                        }
                                        echo '<option value="'.$item_categorie['id'].'" '.$selected.'>'.$item_categorie['category_description'].'</option>';
                                    } ?>
                                </select>
                            </td>
                            <td class="p-2 allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss;?>">
                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" name="amount[<?php echo  $userlists['id']; ?>]" class="form-control text-right" placeholder="<?= htmlspecialchars(__('amount'), ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo $approvedusercriteria['amount'];?>">
                            </td>
                            <?php if($application_table_name == 'requisitions'){ ?>
                                <td class="p-2 w-full allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>">
                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="onbehalf[<?php echo  $userlists['id']; ?>]" onchange="onBeHalfType(this.value,'<?php echo  $userlists['id']; ?>')">
                                        <option  value="0"><?= __('Select Any One'); ?></option>
                                        <option value="DIV" <?php echo $approvedusercriteria['onbehalf'] == "DIV"?"selected":""; ?>><?= __('Divisions'); ?></option>
                                        <option value="D" <?php echo $approvedusercriteria['onbehalf'] == "D"?"selected":""; ?>><?= __('Departments'); ?></option>
                                        <option value="cost_center_id" <?php echo $approvedusercriteria['onbehalf'] == "cost_center_id"?"selected":""; ?>><?= __('Cost Centers'); ?></option>
                                        <option value="L" <?php echo $approvedusercriteria['onbehalf'] == "L"?"selected":""; ?>><?= __('Locations'); ?></option>
                                        <option value="U" <?php echo $approvedusercriteria['onbehalf'] == "U"?"selected":""; ?>><?= __('For User'); ?></option>
                                    </select>
                                </td>
                                <td class="p-2 w-full allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>">
                                    <?php
                                    if($approvedusercriteria['foruser'] !=""){
                                        $foruser = json_decode($approvedusercriteria['foruser'],1);
                                    }
                                    ?>
                                    <div  id="foruser_<?php echo  $userlists['id']; ?>" style="<?php echo $approvedusercriteria['onbehalf'] != "U"?"display:none":""; ?>">
                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-multiple" name="foruser[<?php echo  $userlists['id']; ?>][]" multiple>
                                            <?php foreach($neuserlistary as $appus0){
                                                $selected= "";
                                                if(in_array($appus0['id'],(array)$foruser)){
                                                    $selected ="selected";
                                                }
                                                echo '<option value="'.$appus0['id'].'" '.$selected.'>'.$appus0['display_name'].'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </td>
                            <?php }  ?>
                            <td class="p-2 allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>">
                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" name="backdate_limit[<?php echo  $userlists['id']; ?>]" class="form-control text-right" placeholder="<?= htmlspecialchars(__('Backdate Limit'), ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo $approvedusercriteria['backdate_limit'];?>">
                            </td>
                            <td class="p-2 allowuserrole_<?php echo $userlists['id'];?> aca_<?php echo $userlists['id'];?>" style="<?php echo $stylecss." ".$stylecsscreat;?>">
                                <?php
                                $transactiondate = $approvedusercriteria['from_date']!=""?$approvedusercriteria['from_date']:date('Y-m-d');

                                ?>
                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" name="from_date[<?php echo  $userlists['id']; ?>]" class="form-control datepicker w-auto" value="<?php echo fromDate($transactiondate); ?>">

                                <input type="hidden" name="old_from_date[<?php echo $userlists['id']; ?>]" value="<?php echo fromDate($transactiondate); ?>">
                            </td>
                        </tr>
                        <?php $i++;
} ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary mr-2" type="submit" name="submit" value="submit"><i class="la la-check"></i><?= __('Save'); ?></button>
        </div>
    </form>
</div>
</div>
