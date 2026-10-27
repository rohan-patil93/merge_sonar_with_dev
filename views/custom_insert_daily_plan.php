
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
        <strong>This function is used for maintaining <strong>Field Work</strong>. * represents mandatory fields.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>Field Work Daily Plans</h5>
        </div>
        <div class="card-body p-3">
            <form id="fieldWorkDailyPlansForm" method="post">
                <div class="row">
                    <div  class="col-lg-6 col-xs-12 form-group">
                        <span>User <span style="color:red;">*</span></span>
                        <input aria-label="Display Name" class="form-control" type="text" name="display_name" id="display_name" value="<?php echo isset($display_name) && $display_name != '' ? ($display_name) :  ''; ?>"  disabled />
                        <div style="display: none;color: red;" id="user_id_err"></div>
                        <input class="form-control" type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>"/>
                    </div>
                    <div  class="col-lg-6 col-xs-12 form-group">
                        <span>Date <span style="color:red;">*</span></span>
                        <input aria-label="Plan Date" class="form-control" type="date" name="plan_date" id="plan_date" value="<?php echo isset($map_date) && $map_date != '' ? date('Y-m-d', strtotime($map_date)) :  date('Y-m-d'); ?>"  readonly />
                        <div style="display: none;color: red;" id="plan_date_err"></div>
                    </div>
                    <div class="col-lg-6 col-xs-6 form-group">
                        <span>Contact person </span>
                        <input aria-label="Contact Person" class="form-control lat" type="text" name="contact_person" id="contact_person" >

                    </div>
                    <div class="col-lg-6 col-xs-6 form-group">
                        <span>Phone No.  </span>
                        <input aria-label="Mobile" class="form-control lat" type="text" name="mobile" id="mobile" >

                    </div>

                    <!-- Latitude -->
                    <div class="col-lg-4 col-xs-6 form-group">
                        <span>Latitude <span style="color:red;">*</span> </span>
                        <input aria-label="Plan Latitude" class="form-control lat" type="text" name="plan_latitude" id="plan_latitude" value="<?php echo $lat; ?>" readonly>
                        <div style="display: none;color: red;" id="plan_latitude_err"></div>
                    </div>

                    <!-- Longitude -->
                    <div class="col-lg-4 col-xs-6 form-group">
                        <span>Longitude <span style="color:red;">*</span></span>
                        <input aria-label="Plan Longitude" class="form-control lng" type="text" name="plan_longitude" id="plan_longitude" value="<?php echo $lng; ?>" readonly>
                        <div style="display: none;color: red;" id="plan_longitude_err"></div>
                    </div>
                    <div class="col-lg-4 col-xs-12 form-group">
                        <span>Address</span>
                        <input aria-label="Plan Address" class="form-control" type="text" name="plan_address" id="plan_address" readonly>
                    </div>
                    <!-- Location -->
                    <div class="col-lg-6 col-xs-12 form-group">
                        <span>Location</span>
                        <input aria-label="Plan Location" class="form-control" type="text" name="plan_location" id="plan_location">
                    </div>

                    <div class="col-lg-6 col-xs-6 form-group d-flex justify-content-center align-items-end">
                        <button type="button" class="btn btn-success" id="addMapBtn">Add Map Point</button>
                    </div>

                    <div class="border-top">
                        <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary">
                            Save
                        </button>

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
