<?php
defined('PARAM_DATE') || define('PARAM_DATE', '&date=');
defined('LOC_FIELD_WORK_DAILY_PLANS_USER') || define('LOC_FIELD_WORK_DAILY_PLANS_USER', 'location:index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS&user_id=');

$_SESSION['apply_js']['multiselest'] = 1;

$admin_type = 1;
if($_SESSION['working_role_short_code'] == 'FIELD_EMPLOYEE'){
    $admin_type = 3;
}
if($_SESSION['working_role_short_code'] == 'FIELD_MANAGER'){
    $admin_type = 2;
}
if (isset($_POST['submit'])) {

    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        if ($_POST['user_field_work_type'] == '1' || $_POST['user_field_work_type'] == '2') {
            unset($_POST['user_field_work_type']);
            $table_name = 'field_work_daily_plans';
            $data = $_POST;
            $res =  insertData($table_name, $data);
            if ($res) {
                header(LOC_FIELD_WORK_DAILY_PLANS_USER.$_POST['user_id'].PARAM_DATE.$_POST['plan_date']."&error=9999");
            }
        } else {
            unset($_POST['user_field_work_type'], $_POST['plan_date']);
            $table_name = 'user_field_work_details';
            $data = $_POST;
            $res =  insertUserFieldData($table_name, $data);
            if ($res) {
                header(LOC_FIELD_WORK_DAILY_PLANS_USER.$_POST['user_id'].PARAM_DATE.$_POST['work_from_date']."&error=9999");
            }
        }
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        if ($_POST['user_field_work_type'] == '1' || $_POST['user_field_work_type'] == '2') {
            unset($_POST['user_field_work_type']);
            $table_name = 'field_work_daily_plans';
            $rid = $_POST['rid'];
            $date = $_POST['date'];
            $url = API . "?mode=updateFieldWorkDailyPlans&method=get&rid=" . $rid . PARAM_DATE . $date . "&table=" . $table_name;
            $result = callAPI($url);
            $result = json_decode($result, 1);

            if ($result['status'] == 'sucess') {
                unset($_POST['rid'], $_POST['date']);
                $data = $_POST;
                $res =  insertData($table_name, $data);
                if ($res) {
                    header(LOC_FIELD_WORK_DAILY_PLANS_USER.$_POST['user_id'].PARAM_DATE.$_POST['plan_date']."&error=9998");
                }
            }
        } else {
            $table_name = 'user_field_work_details';
            unset($_POST['user_field_work_type'], $_POST['plan_date']);
            $id = $_POST['user_field_work_details_id'];
            $url = API . "?mode=updateFieldWorkDailyPlans&method=get&rid=" . $id . "&table=" . $table_name;
            $result = callAPI($url);
            $result = json_decode($result, 1);
            if ($result['status'] == 'sucess') {
                unset($_POST['rid'], $_POST['date'], $_POST['user_field_work_details_id']);
                $data = $_POST;
                $res =  insertUserFieldData($table_name, $data);
                if ($res) {
                    header(LOC_FIELD_WORK_DAILY_PLANS_USER.$_POST['user_id'].PARAM_DATE.$_POST['work_from_date']."&error=9998");
                }
            }
        }
    }
}
function insertData($table_name, $data)
{
    $result = array();
    $data['current_status'] = '0';
    $data['is_active'] = '0';
    $data['status_date'] = nowDate();
    $data['company_id'] = $_SESSION['company_id'];
    $data['created_date'] = nowDate();
    $data['created_by'] = $_SESSION['userid'];
    $k = count($data['plan_latitude']);
    $contact_person = $data['contact_person'];
    $mobile = $data['mobile'];
    $plan_latitude = $data['plan_latitude'];
    $plan_longitude = $data['plan_longitude'];
    $plan_location = $data['plan_location'];
    $plan_address = $data['plan_address'];
    unset($data['plan_latitude'], $data['plan_longitude'], $data['plan_location'], $data['plan_address'], $data['contact_person'], $data['mobile']);
    for ($i = 0; $i < $k; $i++) {
        $data['contact_person'] = $contact_person[$i];
        $data['mobile'] = $mobile[$i];
        $data['plan_latitude'] = $plan_latitude[$i];
        $data['plan_longitude'] = $plan_longitude[$i];
        $data['plan_location'] = $plan_location[$i];
        $data['plan_address'] = $plan_address[$i];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $result[] = $response;
    }
    return $result;
}
function insertUserFieldData($table_name, $data)
{
    $data['current_status'] = '0';
    $data['is_active'] = '0';
    $data['status_date'] = nowDate();
    $data['company_id'] = $_SESSION['company_id'];
    $data['created_date'] = nowDate();
    $data['created_by'] = $_SESSION['userid'];
    $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
    return file_post_contents($url, $data, 'insert');
}

$url = API . "?mode=getApplicationUsersDataforInsert&method=get&admin_type=" . $admin_type;
$result = callAPI($url);
$result = json_decode($result, 1);
$application_users  = array();
$application_users = $result['application_users'];

if ($_GET) {
    $rid = $_GET['user_id'] ?? '';
    $date = $_GET['date'] ?? '';
    $view = $_GET['view'] ?? '';
    $copy = $_GET['copy'] ?? '';
    $error = $_GET['error'] ?? '';
}

?>

<!-- Leaflet CSS -->

<link rel="stylesheet" href="assets/bower_components/leaflet/leaflet.css">
<link rel="stylesheet" href="assets/bower_components/leaflet/leaflet-search.min.css">

<link rel="stylesheet" href="assets/css/custom_insert_field_work_daily_plans.css">
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>

<script src="assets/bower_components/leaflet/leaflet.js" ></script>
<script src="assets/bower_components/leaflet/leaflet-search.min.js" ></script>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <!-- Page body start -->
            <div class="page-body">
                <?php if($error){ ?>
                    <div class="alert <?php echo ($error == '9999') ? 'bg-success' : 'bg-warning'; ?> alert-dismissible">
                        <strong>
                            <?php
                            if ($error == '9999') {
                                echo __('Insert successful.');
                            } elseif ($error == '9998') {
                                echo __('Update successful. ');
                            }
                            ?>
                        </strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo __('Close'); ?>"></button>
                        </div>         <?php  }else{      ?>
                            <div class="alert alert-light alert-dismissible">
                                <strong><?php echo __('This function is used for maintaining'); ?> <strong><?php echo __('Field Work'); ?></strong><?php echo __('. * represents mandatory fields.'); ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo __('Close'); ?>"></button>
                            </div>
                        <?php  } ?>
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo __('Field Work Daily Plans'); ?></h5>

                                <div class="card-header-right">
                                    <a href="index.php?option=COM_LIST_FIELD_WORK_DAILY_PLANS" class="btn btn-success mb-2 desktop-view">

                                  <?php echo __('User Work Daily Plans List'); ?>          </a>
                                  <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                                    <i class="icofont icofont-reply"></i>
                                    <?php echo __('Back'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="card-infisuite mt-2">
                            <div class="card-body p-4 ">
                                <form id="fieldWorkDailyPlansForm" method="post">
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <span><?php echo __('User'); ?> <span class="text-danger" >*</span></span>
                                            <select aria-label="<?php echo __('User'); ?>" class="form-select js-example-basic-single" id="user_id" name="user_id" >
                                                <option value="0"> <?php echo __('Select Any one'); ?> </option>
                                                <?php

                                                foreach ($application_users as $row){ ?>
                                                    <option value="<?php echo $row['id']; ?>" data-work-type="<?php echo $row['user_field_work_type']; ?>"
                                                        <?php echo $rid == $row['id'] ? 'selected' : ''; ?>>
                                                        <?php echo $row['display_name']; ?>
                                                    </option>
                                                <?php } ?>

                                            </select>
                                            <div class="d-none text-danger"  id="user_id_err"></div>
                                        </div>
                                        <div id="toDateDiv" class="col-lg-6 col-xs-12 form-group d-none" >
                                            <span><?php echo __('Date'); ?> <span class="text-danger" >*</span></span>
                                            <?php
                                            $readonly = '';
                                            if ($rid != "" && $view == '1') {
                                                $readonly = 'readonly';
                                            }
                                            ?>

                                            <input aria-label="<?php echo __('Plan Date'); ?>" class="form-control" type="date" name="plan_date" id="plan_date" value="<?php echo isset($date) && $date != '' ? date('Y-m-d', strtotime($date)) :  '' ?>" <?php echo $readonly;  ?> min="<?php echo date('Y-m-d');?>"  />
                                            <div class="d-none text-danger"  id="plan_date_err"></div>
                                        </div>

                                        <input type="hidden" id="user_field_work_type" name="user_field_work_type" value="">
                                        <!-- Daily Plan -->

                                        <div id="workType1" class=" p-4 work-type d-none" >
                                            <div class="row">
                                                <table class="table table-inverse" id="inputTable">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col"></th>
                                                            <th scope="col"><?php echo __('Contact Person'); ?></th>
                                                            <th scope="col"><?php echo __('Phone No.'); ?></th>
                                                            <th scope="col"><?php echo __('Location'); ?></th>
                                                            <th scope="col"><?php echo __('Address'); ?></th>
                                                            <th scope="col"><?php echo __('Latitude'); ?> <span class="text-danger" >*</span></th>
                                                            <th scope="col"><?php echo __('Longitude'); ?> <span class="text-danger" >*</span></th>
                                                            <th scope="col"><?php echo __('Action'); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                             <i class="fas fa-trash-alt text-danger cursor-pointer removeRow" ></i>
                                                         </td>
                                                         <td><input aria-label="<?php echo __('Contact Person[]'); ?>" class="form-control " type="text" name="contact_person[]"></td>
                                                         <td><input aria-label="<?php echo __('Mobile[]'); ?>" class="form-control " type="text" name="mobile[]"></td>
                                                         <td><input aria-label="<?php echo __('Plan Location[]'); ?>" class="form-control" type="text" name="plan_location[]"></td>
                                                         <td><textarea aria-label="<?php echo __('Plan Address[]'); ?>" class="form-control" name="plan_address[]" rows="2" readonly></textarea></td>
                                                         <td><input aria-label="<?php echo __('Plan Latitude[]'); ?>" class="form-control lat" type="text" name="plan_latitude[]" readonly></td>
                                                         <td><input aria-label="<?php echo __('Plan Longitude[]'); ?>" class="form-control lng" type="text" name="plan_longitude[]" readonly></td>
                                                         <td>
                                                            <button type="button" class="btn btn-success addMapBtn"><?php echo __('Map'); ?></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="8">
                                                            <div class="d-flex justify-content-between">
                                                                <?php if ($view != '1'): ?>
                                                                    <button type="button" class="btn btn-primary" id="addMoreBtn"><?php echo __('Add More'); ?></button>
                                                                <?php endif; ?>
                                                                <button type="button" class="btn btn-success" id="updateMapBtn"><?php echo __('View All Map Points'); ?></button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                        </div>
                                    </div>
                                    <!-- Security -->

                                    <div id="workType2" class="work-type d-none" >
                                        <div class="row">
                                            <!-- From Date -->
                                            <div class="col-lg-6 col-xs-12 form-group">
                                                <span><?php echo __('From Date'); ?> <span class="text-danger" >*</span></span>
                                                <input aria-label="<?php echo __('Work From Date'); ?>" class="form-control" type="date" name="work_from_date" id="work_from_date" value=""  min="<?php echo date('Y-m-d');?>">
                                                <div class="d-none text-danger"  id="work_from_date_err"></div>
                                            </div>

                                            <!-- To Date -->
                                            <div class="col-lg-6 col-xs-12 form-group">
                                                <span><?php echo __('To Date'); ?> <span class="text-danger" >*</span></span>
                                                <input aria-label="<?php echo __('Work To Date'); ?>" class="form-control" type="date" name="work_to_date" id="work_to_date">
                                                <div class="d-none text-danger"  id="work_to_date_err"></div>
                                            </div>
                                            <!-- Time -->
                                            <div class="col-lg-6 col-xs-12 form-group">
                                                <span><?php echo __('Time'); ?> <span class="text-danger" >*</span></span>
                                                <input aria-label="<?php echo __('Work Time'); ?>" class="form-control" type="time" name="work_time" id="work_time">
                                                <div class="d-none text-danger"  id="work_time_err"></div>
                                            </div>

                                            <!-- Work Radius -->
                                            <div class="col-lg-6 col-xs-12 form-group">
                                                <span><?php echo __('Work Radius'); ?></span>
                                                <input aria-label="<?php echo __('Work Radius'); ?>" class="form-control" type="number" name="work_radius" id="work_radius">
                                                <div class="d-none text-danger"  id="work_radius_err"></div>
                                            </div>
                                            <!-- Contact person -->
                                            <div class="col-lg-4 col-xs-6 form-group">
                                                <span><?php echo __('Contact Person'); ?></span>
                                                <input aria-label="<?php echo __('Contact Person'); ?>" class="form-control" type="text" name="contact_person" id="contact_person">

                                            </div>
                                            <!-- Phone No. -->
                                            <div class="col-lg-4 col-xs-6 form-group">
                                                <span><?php echo __('Phone No.'); ?></span>
                                                <input aria-label="<?php echo __('Mobile'); ?>" class="form-control" type="text" name="mobile" id="mobile">

                                            </div>

                                            <!-- Location -->
                                            <div class="col-lg-4 col-xs-6 form-group">
                                                <span><?php echo __('Location Name'); ?></span>
                                                <input aria-label="<?php echo __('Location Name'); ?>" class="form-control" type="text" name="location_name" id="location_name">

                                            </div>

                                            <!-- Latitude -->

                                            <div class="col-lg-3 col-xs-6 form-group">
                                                <span><?php echo __('Latitude'); ?> <span class="text-danger" >*</span> </span>
                                                <input aria-label="<?php echo __('Work Latitude'); ?>" class="form-control lat" type="text" name="work_latitude" id="work_latitude" readonly>
                                                <div class="d-none text-danger"  id="work_latitude_err"></div>
                                            </div>

                                            <!-- Longitude -->
                                            <div class="col-lg-3 col-xs-6 form-group">
                                                <span><?php echo __('Longitude'); ?> <span class="text-danger" >*</span></span>
                                                <input aria-label="<?php echo __('Work Longitude'); ?>" class="form-control lng" type="text" name="work_longitude" id="work_longitude" readonly>
                                                <div class="d-none text-danger"  id="work_longitude_err"></div>
                                            </div>

                                            <div class="col-lg-3 col-xs-6 form-group">
                                                <span><?php echo __('Address'); ?></span>
                                                <textarea aria-label="<?php echo __('Work Address'); ?>" class="form-control lng" type="text" name="work_address" id="work_address" readonly></textarea>

                                            </div>

                                            <div class="col-lg-3 col-xs-6 form-group d-flex justify-content-center align-items-end">
                                                <button type="button" class="btn btn-success addMapBtn"><?php echo __('Add Map Point'); ?></button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div id="saveButton" class="d-none" >
                                    <div class="">
                                        <?php if ($view != '1'):
                                            if ($rid != "" && $copy != '1') {  ?>
                                                <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                                                    <?php echo __('Update'); ?>
                                                </button>
                                                <input name="rid" type="hidden" value="<?php echo $rid; ?>">
                                                <input name="date" type="hidden" value="<?php echo $date; ?>">

                                            <?php } else {  ?>
                                                <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary">
                                                    <?php echo __('Save'); ?>
                                                </button>
                                            <?php }
                                        endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page body end -->
        </div>
    </div>
</div>
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel"><?php echo __('Map View'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('Close'); ?>"></button>
            </div>
            <div class="modal-body">
                <div id="map" class="m-height-500"></div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/custom_insert_field_work_daily_plans.js"></script>
