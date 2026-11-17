
<?php
print_r($_GET);
$map_date = $_GET['map_date'];
$user_id = $_GET['user_id'];
$display_name = $_GET['display_name'];
$lat = $_GET['lat'];
$lng = $_GET['lng'];
?>
<div class="page-body">
    <div class="alert alert-light alert-dismissible">
        <strong><?= __('This function is used for maintaining') ?> <strong><?= __('Field Work') ?></strong>. <?= __('* represents mandatory fields.') ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
    </div>
    <div class="card">
        <div class="card-header">
            <h5><?= __('Field Work Daily Plans') ?></h5>
        </div>
        <div class="card-body p-3">
            <form id="fieldWorkDailyPlansForm" method="post">
                <div class="row">
                    <div  class="col-lg-6 col-xs-12 form-group">
                        <span><?= __('User') ?> <span style="color:red;">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Display Name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" name="display_name" id="display_name" value="<?php echo isset($display_name) && $display_name != '' ? ($display_name) :  ''; ?>"  disabled />
                        <div style="display: none;color: red;" id="user_id_err"></div>
                        <input class="form-control" type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>"/>
                    </div>
                    <div  class="col-lg-6 col-xs-12 form-group">
                        <span><?= __('Date') ?> <span style="color:red;">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Plan Date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" name="plan_date" id="plan_date" value="<?php echo isset($map_date) && $map_date != '' ? date('Y-m-d', strtotime($map_date)) :  date('Y-m-d'); ?>"  readonly />
                        <div style="display: none;color: red;" id="plan_date_err"></div>
                    </div>
                    <div class="col-lg-6 col-xs-6 form-group">
                        <span><?= __('Contact person') ?></span>
                        <input aria-label="<?= htmlspecialchars(__('Contact Person'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control lat" type="text" name="contact_person" id="contact_person" >

                    </div>
                    <div class="col-lg-6 col-xs-6 form-group">
                        <span><?= __('Phone No.') ?></span>
                        <input aria-label="<?= htmlspecialchars(__('Mobile'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control lat" type="text" name="mobile" id="mobile" >

                    </div>

                    <!-- Latitude -->
                    <div class="col-lg-4 col-xs-6 form-group">
                        <span><?= __('Latitude') ?> <span style="color:red;">*</span> </span>
                        <input aria-label="<?= htmlspecialchars(__('Plan Latitude'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control lat" type="text" name="plan_latitude" id="plan_latitude" value="<?php echo $lat; ?>" readonly>
                        <div style="display: none;color: red;" id="plan_latitude_err"></div>
                    </div>

                    <!-- Longitude -->
                    <div class="col-lg-4 col-xs-6 form-group">
                        <span><?= __('Longitude') ?> <span style="color:red;">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Plan Longitude'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control lng" type="text" name="plan_longitude" id="plan_longitude" value="<?php echo $lng; ?>" readonly>
                        <div style="display: none;color: red;" id="plan_longitude_err"></div>
                    </div>
                    <div class="col-lg-4 col-xs-12 form-group">
                        <span><?= __('Address') ?></span>
                        <input aria-label="<?= htmlspecialchars(__('Plan Address'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" name="plan_address" id="plan_address" readonly>
                    </div>
                    <!-- Location -->
                    <div class="col-lg-6 col-xs-12 form-group">
                        <span><?= __('Location') ?></span>
                        <input aria-label="<?= htmlspecialchars(__('Plan Location'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" name="plan_location" id="plan_location">
                    </div>

                    <div class="col-lg-6 col-xs-6 form-group d-flex justify-content-center align-items-end">
                        <button type="button" class="btn btn-success" id="addMapBtn"><?= __('Add Map Point') ?></button>
                    </div>

                    <div class="border-top">
                        <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary"><?= __('Save') ?></button>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->

<script type="text/javascript">

    //     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //         url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
    //         jsonpParam: 'json_callback',
    //         propertyName: 'display_name',
    //         propertyLoc: ['lat', 'lon'],
    //         marker: false,
    //         autoCollapse: true,
    //         autoType: false,
    //         minLength: 2,

</script>
