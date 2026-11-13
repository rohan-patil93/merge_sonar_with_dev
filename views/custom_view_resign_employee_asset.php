<?php

include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$url = API . "?mode=getResignedEmployeeData&method=get";
$result = callAPI($url);
$result = json_decode($result, 1);
$employees = $result['query_res'];
$assets = $result['assets'];
$all_emp =  $result['emp_data'];

$default_emp_id = isset($employees[0]['id']) ? $employees[0]['id'] : '';
$default_user_id = isset($employees[0]['user_id']) ? $employees[0]['user_id'] : '';
?>


<div class="col-lg-3">
    <h5> <?= __('Employees'); ?> </h5>
    <ul class="nav flex-column md-tabs infisuite_tab mt-3 m-l-5">
        <li class="nav-item p-1">
                <a class="nav-link " data-bs-toggle="tab"
                    href="#tabnew"
                    role="tab"
                    class="text-left"
                    data-emp-id="new"
                    data-user-id="new"
                    onclick="toselectemployevaluereplace('new','new')"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    + <?= __('Assets with out resign employee'); ?>
                </a>
            </li>
        <?php foreach ($employees as $index => $value) { ?>
            <li class="nav-item p-1">
                <a class="nav-link <?php echo $index == 0 ? 'active' : '' ?>"  onclick="toselectemployevaluereplace(<?php echo $value['id'].",".$value['user_id']; ?>)" data-bs-toggle="tab"
                    href="#tab<?php echo $value['id']; ?>"
                    role="tab"
                    class="text-left"
                    data-emp-id="<?php echo $value['id']; ?>"
                    data-user-id="<?php echo $value['user_id']; ?>">
                    <?php echo $value['name']; ?>
                </a>
            </li>
        <?php } ?>

    </ul>
</div>
<div class="col-lg-9" >

    <form action="" method="post">
        <div id="showonetime" class="row" style="display: none;" >
            <div  class= "col-lg-4 m-b-5">
            <label for=""><?= __('Employee'); ?> </label>
            <select aria-label="<?= htmlspecialchars(__('Emp'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-select js-example-basic-single" id="sel_emp_id" onchange="onselectemp(this.value)">
                <option value="0"><?= __('Select Any One'); ?></option>
                <?php foreach($all_emp as $aemp){ ?>
                    <option value="<?php echo $aemp['id']."#".$aemp['user_id']; ?>" > <?php echo $aemp['display_name'] ?></option>
                <?php } ?>
            </select>

            </div>
        </div>
        <input type="hidden" name="user_id" id="user_id" value="<?= $default_user_id; ?>">
        <input type="hidden" name="emp_id" id="emp_id" value="<?= $default_emp_id; ?>">
        <div class="row">
            <div class="col-lg-4"><label for=""><?= __('Assest'); ?> </label></div>
            <div class="col-lg-4"><label for=""><?= __('Date'); ?> </label></div>
            <div class="col-lg-4"><label for=""><?= __('Remarks'); ?> </label></div>
        </div>
        <div id="resignContainer">
            <div class="row asset-row">
                <div class="col-lg-4 cc_val">
                    <div class="form-group">
                        <select aria-label="<?= htmlspecialchars(__('Asset'), ENT_QUOTES, 'UTF-8'); ?> []" class="form-control js-example-basic-single form-select asset-select " name="asset_id[]" id="asset_id_1" required>
                            <option value=""><?= __('Select Any One'); ?></option>
                            <?php foreach ($assets as $asset) : ?>
                                <option value="<?php echo $asset['id']; ?>"><?php echo $asset['asset_description']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <input aria-label="<?= htmlspecialchars(__('Received Date'), ENT_QUOTES, 'UTF-8'); ?>[]" type="date" class="form-control" name="received_date[]" id="received_date" required>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <textarea aria-label="<?= htmlspecialchars(__('Remarks'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="<?= htmlspecialchars(__('Remarks'), ENT_QUOTES, 'UTF-8'); ?>" name="received_note[]" id="received_note"></textarea>
                    </div>
                </div>
                <div class="col-lg-2">
                    <button type="button" class="removeRow btn text-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mt-2">
            <button type="button" class="btn btn-success" id="addMore"><?= __('Add More'); ?></button>
        </div>
        <div class="col-lg-11 text-end mt-2">
            <button type="submit" name="submit" value="insert" class="btn btn-primary"><?= __('Save'); ?></button>
        </div>
    </form>

</div>

<script>

</script>
