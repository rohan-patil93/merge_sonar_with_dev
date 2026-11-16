<?php
$_SESSION['apply_js']['multiselest'] = 1;
$map_date = '2024-12-17';

$admin_type = 1;
if($_SESSION['working_role_short_code'] == 'FIELD_EMPLOYEE'){
    $admin_type = 3;
}
if($_SESSION['working_role_short_code'] == 'FIELD_MANAGER'){
    $admin_type = 2;
}

$url = API . "?mode=getApplicationUsersData&method=get&admin_type=".$admin_type;
$result = callAPI($url);
$result = json_decode($result, 1);
$application_users  = array();
$application_users = $result['application_users'];

?>

<link rel="stylesheet" href="assets/css/custom_list_field_work_user_map_points.css">
<link rel="stylesheet" href="assets/bower_components/leaflet/leaflet.css">
<link rel="stylesheet" href="assets/bower_components/leaflet/leaflet.extra-markers.min.css">

<!-- Page body start -->
<div class="page-body">
    <div class="alert alert-light alert-dismissible">
        <strong>This function is used for maintaining <strong><?= __('Field Work'); ?></strong><?= __('. * represents mandatory fields.'); ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
    </div>
    <div class="card">
        <div class="card-header">
            <h5><?= __('Field Work Daily Plans Map View'); ?></h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    <?= __('Back'); ?>
                </a>
            </div>
        </div>
        <div class="card-body p-4 ">
            <div class="row">
                <div class="form-group col-lg-5">
                    <span><?= __('User'); ?> <span class="text-danger">*</span></span>
                    <select aria-label="<?= htmlspecialchars(__('User'), ENT_QUOTES, 'UTF-8'); ?> []" class="form-select js-example-basic-multiple" id="user_id" name="user_id[]" multiple="multiple">
                        <option value="0"> <?= __('Select Any one'); ?> </option>
                        <?php foreach ($application_users as $row): ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['display_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="d-none text-danger"  id="user_id_err"></div>
                </div>

                <div class="col-lg-3 col-xs-4 form-group">
                    <span><?= __('From Date'); ?> <span class="text-danger">*</span></span>
                    <input aria-label="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" name="from_date" id="from_date" value="" >
                    <div class="d-none text-danger"  id="from_date_err"></div>
                </div>
                <div class="col-lg-3 col-xs-4 form-group">
                    <span><?= __('To Date'); ?> <span class="text-danger">*</span></span>
                    <input aria-label="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" name="to_date" id="to_date" value="">
                    <div class="d-none text-danger"  id="to_date_err"></div>
                </div>

                <div class="col-lg-1 col-xs-4 form-group">
                    <span> &nbsp; </span>
                    <button type="button" class="btn btn-primary form-control" id="getDataButton"><?= __('Serach'); ?></button>
                </div>
            </div>

            <div class="row">
                <div id="workType1" class="work-type d-none" >

                    <table class="table table-inverse" id="fiedWorkTable">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('S No.'); ?></th>
                                <th scope="col"><?= __('User'); ?></th>
                                <th scope="col"><?= __('Email'); ?></th>
                                <th scope="col"><?= __('Date'); ?> </th>
                                <th scope="col"><?= __('Visited'); ?></th>
                                <th scope="col"><?= __('Extra'); ?></th>
                                <th scope="col"><?= __('Pending'); ?></th>
                                <th scope="col"><?= __('Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>

                </div>
            </div>

        </div>

    </div>
</div>
<!-- Page body end -->

<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel"><?= __('Map View'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
            </div>
            <div class="modal-body">
                <div id="map" class="map-container"></div>
            </div>
        </div>
    </div>
</div>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script src="assets/bower_components/leaflet/leaflet.js" ></script>

<script src="assets/bower_components/leaflet/leaflet.extra-markers.min.js" ></script>

<script src="assets/js/custom_list_field_work_user_map_points.js"></script>

