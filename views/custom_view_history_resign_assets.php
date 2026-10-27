<?php

include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$url = API . "?mode=getResignedEmployeeHistoryData&method=get&emp_id=".$_GET['reshis_emp_id'];
$result = callAPI($url);
$result = json_decode($result, 1);
$employees = $result['query_res'];
$assets = $result['assets'];
$assets_history = $result['user_assets'];

$default_emp_id = isset($employees[0]['id']) ? $employees[0]['id'] : '';
$default_user_id = isset($employees[0]['user_id']) ? $employees[0]['user_id'] : '';
?>

<div class="col-lg-3">
    <h5> Employees </h5>
    <ul class="nav flex-column md-tabs infisuite_tab mt-3 m-l-5">
        <?php foreach ($employees as $index => $value) { ?>
            <li class="nav-item p-1">
                <a class="nav-link <?php echo $value['id'] == $_GET['reshis_emp_id'] ? 'active' : '' ?>"
                    onclick="resignhistorydaybyempid(<?php echo $value['id'].",".$value['user_id']; ?>)"
                 data-bs-toggle="tab"
                    href="#tab<?php echo $value['id']; ?>"
                    role="tab"
                    data-emp-id="<?php echo $value['id']; ?>"
                    data-user-id="<?php echo $value['user_id']; ?>">
                    <?php echo $value['name']; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
<div class="col-lg-9" >
    <?php if($_GET['reshis_emp_id']!=""){ ?>
    <form action="" method="post">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $_GET['reshis_user_id']; ?>">
            <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $_GET['reshis_emp_id']; ?>">
        <div class="row">
            <div class="col-lg-4"><label for="">Assest </label></div>
            <div class="col-lg-4"><label for="">Date </label></div>
            <div class="col-lg-4"><label for="">Remarks </label></div>
        </div>
        <div id="resignContainer">
        <?php $k=1;
            if (!empty($assets_history)) {
                foreach ($assets_history as $history) { ?>
                    <div class="row asset-row">
                        <div class="col-lg-4 ">
                            <div class="form-group">
                                <select aria-label="Asset []" class="form-control js-example-basic-single form-select  asset-select" name="asset_id[]" id="asset_did_<?php echo $k; ?>" required>
                                    <option value="">Select Any One</option>
                                    <?php foreach ($assets as $asset) : ?>
                                        <option value="<?php echo $asset['id']; ?>"
                                            <?php echo ($asset['id'] == $history['asset_id']) ? 'selected' : ''; ?>>
                                            <?php echo $asset['asset_description']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <input aria-label="Received Date[]" type="date" class="form-control" name="received_date[]" required
                                       value="<?php echo $history['received_date']; ?>">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <textarea aria-label="Remarks" class="form-control" name="received_note[]"
                                          placeholder="Remarks"><?php echo $history['received_note']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="removeRow btn text-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php $k++;
}
            } else { ?>

                <div class="row asset-row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <select aria-label="Select option" class="form-control form-select asset-select js-example-basic-single" id="asset_did_<?php echo $k; ?>" name="asset_id[]" required>
                                <option value="">Select Any One</option>
                                <?php foreach ($assets as $asset) : ?>
                                    <option value="<?php echo $asset['id']; ?>"><?php echo $asset['asset_description']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <input aria-label="Received Date[]" type="date" class="form-control" name="received_date[]" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <textarea aria-label="Remarks" class="form-control" name="received_note[]" placeholder="Remarks"></textarea>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col-lg-12 mt-2">
            <button type="button" class="btn btn-success" id="addMore">Add More</button>
        </div>
        <div class="col-lg-11 text-end mt-2">

            <button type="submit" name="submit" value="update" class="btn btn-primary">Update</button>
        </div>
    </form>
    <?php }else{ ?>
        <div class="text-center">
        <h3 class="f-18 f-w-600 text-danger">Please Select Any One Employee </h3>
        </div>
     <?php } ?>
</div>

