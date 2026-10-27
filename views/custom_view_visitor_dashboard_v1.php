<?php

$_SESSION['apply_js']['multiselest'] = 1;
$table_name = 'locations';
$vdata1['order'] = "id ASC";
// $vdata1['limit'] = "0,5";
$vdata1["where"] = array('company_id' => $_SESSION['company_id'], 'is_active' => 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=gettabledetailswithconditions&table=$table_name&data=$vdata";
$che = callAPI($vurl);
$return = json_decode($che, 1);
$location_res = $return;

// $return = $return['0'];
?>
<link rel="stylesheet" href="assets/css/custom_view_visitor_dashboard.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="page-body">
    <div class="row">
        <?php
            $location_id = $_SESSION['location_id']; 
        if (in_array('SECURITY_ADMIN', (array)$_SESSION['role_ids'])) {
            $location_id = 0;
            ?>
        <div class="form-group col-lg-4">
            <label>Location</label>
            <select class="form-select js-example-basic-single js-location-select" id="location_id" name="location_id"  data-action="filter-location">
                <option value="0">All</option>
                <?php if (count((array)$location_res) > 0) {
                    foreach ($location_res as $value) {  ?>
                        <option value="<?php echo $value['id']; ?>"><?php echo $value['short_name']; ?></option>
                <?php }
                } ?>
            </select>
        </div>
        <?php }?>

        <div class="mb-3 d-flex flex-wrap gap-2 col-lg-4 align-items-end">

            <button type="button"
                    class="btn btn-success js-nav-btn"
                    data-url="index.php?option=COM_INSERT_VISITOR_LOGS">
                <i class="fa fa-sign-in-alt me-1"></i> Entry
            </button>

            <button type="button"
                    class="btn btn-warning js-nav-btn"
                    data-url="index.php?option=COM_VIEW_VISITOR_OUT">
                <i class="fa fa-sign-out-alt me-1"></i> Exit
            </button>

            <button type="button"
                    class="btn btn-info text-white js-nav-btn"
                    data-url="index.php?option=COM_VIEW_VISITORS_REPORT">
                <i class="fa fa-search me-1"></i> Search
            </button>

            <button type="button"
                    class="btn btn-primary js-nav-btn"
                    data-url="index.php?option=COM_LIST_VISITOR_LOGS">
                <i class="fa fa-calendar-check me-1"></i> All Visitors
            </button>
        </div>
    </div>

    <div class="DashboardMain">

        <?php include('custom_view_visitor_dashboard_main.php'); ?>
    </div>

</div>
<script src="assets/js/custom_view_visitor_dashboard.js"></script>

