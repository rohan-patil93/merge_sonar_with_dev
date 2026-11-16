<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if($_POST['ajaxcall'] =="Y"){
    $Rid = $_POST['Rid'];
    $apiurl = API."?mode=getdocumentaccesssetups&ajaxcall=Y&userlist=".$_POST['user_ids']."&rid=".$Rid;
    $callapi = callAPI($apiurl);
    $result = json_decode($callapi,1);
}
$document_access_setups = $result['document_access_setups'];
$create_access_users = json_decode($document_access_setups['create_access_users'],1);
$approved_user_criteria = $result['approved_user_criteria'];
$application_users = $result['application_users'];
$application_user_all = $result['application_users_all'];
$capexopexres = $result['capexopexres'];
$item_categories = $result['item_categories'];

?>
 <div class="dt-responsive table-responsive">
<table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_1">
    <thead>
        <tr>
            <th scope="col" style="width: 25%;"><?= __('User Details') ?></th>
            <th scope="col" style="width: 20%;"><?= __('User Can Create Requisition for Their') ?></th>
            <th scope="col" style="width: 20%;"><?= __('User Can Create Requisition for CAPEX/OPEX') ?></th>
            <th scope="col" style="width: 20%;"><?= __('User Can Create Requisition Item Category') ?></th>
            <th scope="col" class="text-right" style="width: 20%;"><?= __('User Can Create Requisition upto') ?></th>
            <?php if($_POST['transaction_table'] == 'requisitions'){ ?>
            <th scope="col" style="width: 25%;"><?= __('On Behalf') ?></th>
            <th scope="col" style="width: 20%;"><?= __('User Can Create Requisition only for following users') ?></th>
            <?php } ?>
            <th scope="col" style="width: 40%;"><?= __('From Date') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $mk=0; foreach($application_users as $appuser){
            $approvedusercriteria = $approved_user_criteria[$appuser['id']];
            ?>

        <tr>
            <td class="p-2" >
                <input type="hidden" name="dasu_id[<?php echo $appuser['id']; ?>]" value="<?php echo $approvedusercriteria['id']; ?>">
                <div class="align-middle float-left ml-2">
                    <?php if($appuser['user_photo']!=""){?>
                        <img src="uploads/all/<?php echo $appuser['user_photo']; ?>"   class="img-radius img-30 align-top m-r-15" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                    <?php }else{ ?>
                        <span  alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($appuser['display_name']); ?></span>
                    <?php } ?>
                    <div class="d-inline-block">
                        <p class="f-10"><?php echo $appuser['display_name']; ?></p>
                    </div>
                </div>
            </td>
            <td class="p-2 w-full">
                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="approved_type[<?php echo $appuser['id']; ?>]">
                    <option value="" ><?= __('For All') ?></option>
                    <option value="DIV" <?php echo $approvedusercriteria['approved_type'] == "DIV"?"selected":""; ?>><?= __('Divisions') ?></option>
                    <option value="D" <?php echo $approvedusercriteria['approved_type'] == "D"?"selected":""; ?>><?= __('Departments') ?></option>
                    <option value="cost_center_id" <?php echo $approvedusercriteria['approved_type'] == "cost_center_id"?"selected":""; ?>><?= __('Cost Centers') ?></option>

                </select>
            </td>
            <td class="p-2 w-full">
                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="opex_cpex[<?php echo $appuser['id']; ?>]">
                    <option value="" ><?= __('Both') ?></option>
                    <?php foreach($capexopexres as $opex_cpex){
                        $selected= "";
                        if($approvedusercriteria['opex_cpex'] == $opex_cpex['genc_detail_code']){
                            $selected ="selected";
                        }
                        echo '<option value="'.$opex_cpex['genc_detail_code'].'" '.$selected.'>'.htmlspecialchars(__(strtoupper($opex_cpex['genc_detail_name']) ?? '')).'</option>';
                    } ?>
                </select>
            </td>
            <td class="p-2 w-full">
                <?php $item_category = json_decode($approvedusercriteria['item_category'],1);
                ?>
                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-multiple" name="item_category[<?php echo $appuser['id']; ?>][]" multiple>
                    <?php foreach($item_categories as $item_categorie){
                        $selected= "";
                        if(in_array($item_categorie['id'],(array)$item_category)){
                            $selected ="selected";
                        }
                        echo '<option value="'.$item_categorie['id'].'" '.$selected.'>'.htmlspecialchars(__($item_categorie['category_description'] ?? '')).'</option>';
                    } ?>
                </select>
            </td>
            <td class="p-2">
                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" name="amount[<?php echo  $appuser['id']; ?>]" class="form-control text-right" placeholder="<?= __('amount') ?>" value="<?php echo $approvedusercriteria['amount'];?>">
            </td>
        <?php if($_POST['transaction_table'] == 'requisitions'){ ?>

            <td class="p-2 w-full">
                <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="onbehalf[<?php echo  $appuser['id']; ?>]" onchange="onBeHalfType(this.value,'<?php echo  $appuser['id']; ?>')">
                    <option value="0"><?= __('Select Any One') ?></option>
                    <option value="DIV" <?php echo $approvedusercriteria['onbehalf'] == "DIV"?"selected":""; ?>><?= __('Divisions') ?></option>
                    <option value="D" <?php echo $approvedusercriteria['onbehalf'] == "D"?"selected":""; ?>><?= __('Departments') ?></option>
                    <option value="cost_center_id" <?php echo $approvedusercriteria['onbehalf'] == "cost_center_id"?"selected":""; ?>><?= __('Cost Centers') ?></option>
                    <option value="L" <?php echo $approvedusercriteria['onbehalf'] == "L"?"selected":""; ?>><?= __('Locations') ?></option>
                    <option value="U" <?php echo $approvedusercriteria['onbehalf'] == "U"?"selected":""; ?>><?= __('For User') ?></option>
                </select>
            </td>
            <td class="p-2 w-full">
                <?php $foruser = json_decode($approvedusercriteria['foruser'],1);
                ?>
                <div  id="foruser_<?php echo  $appuser['id']; ?>" style="<?php echo $approvedusercriteria['onbehalf'] != "U"?"display:none":""; ?>">
                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-multiple" name="foruser[<?php echo  $appuser['id']; ?>][]" multiple>
                    <?php foreach($application_user_all as $appus0){
                        $selected= "";
                        if(in_array($appus0['id'],(array)$foruser)){
                            $selected ="selected";
                        }
                        echo '<option value="'.$appus0['id'].'" '.$selected.'>'.htmlspecialchars(__($appus0['display_name'] ?? '')).'</option>';
                     } ?>
                    </select>
                </div>
            </td>
        <?php } ?>
            <td class="p-2">
                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" name="from_date[<?php echo  $appuser['id']; ?>]" class="form-control datepicker w-auto" value="<?php echo fromDate($approvedusercriteria['from_date']); ?>">
                <input type="hidden" name="old_from_date[<?php echo $appuser['id']; ?>]" value="<?php echo fromDate($approvedusercriteria['from_date']); ?>">
            </td>
        </tr>
    <?php $mk++;
} ?>
    </tbody>
</table>
</div>
<div class="card-footer mt-5">
         <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold" type="submit" name="submit"><i class="la la-check"></i><?= __('Submit'); ?></button>
    </div>
