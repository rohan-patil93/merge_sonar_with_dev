<?php
$_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submit'])) {
    $table_name = 'companies';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $_POST['current_status'] = '0';
        $_POST['status_date'] = nowDate();
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowbasepimarydata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }
    $res = json_decode($response, 1);
    $response = $res['res'];
    if ($response) {
        header("location:index.php?option=COM_INSERT_COMPANIES");
    }
}
$_GET['Rid'] = deocdeId('id:9');
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name = 'companies';
    $url = API . "?mode=singlerowtabledata&method=get&rid=" . $rid . "&table=" . $table_name;
    $che = callAPI($url);
    $return = json_decode($che, 1);
}

?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"  integrity="sha384-sHL9NAb7lN7rfvG5lfHpm643Xkcjzp4jFvuavGOndn6pjVqS6ny56CAt3nsEVT4H" crossorigin="anonymous"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-search/dist/leaflet-search.min.css"  integrity="sha384-hmGjQL+woE2q+PPiSWoyxr2OIxUwljVBnQsJUMxM9phtUCIDhJHicsFADtTMMoHj" crossorigin="anonymous"/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-cxOPjt7s7Iz04uaHJceBmS+qpjv2JkIHNVcuOrM+YHwZOmJGBXI00mdUXEq65HTH" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet-search/dist/leaflet-search.min.js" integrity="sha384-R0LItKioY9lIP7pUbpLvkw3VYcmDSKW+mR5/8UKh8NiEtz0EUVgoJwzdVZl343Yz" crossorigin="anonymous"></script>

<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <!-- Page body start -->
            <div class="page-body">
                <div class="alert alert-light alert-dismissible">
                    <strong>This function is used for maintaining <strong>Companies</strong>. * represents mandatory fields.</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5>Companies</h5>
                        <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-infisuite mt-4">
                        <div class="card-body p-4 ">
                            <form id="locationForm" method="post">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <span class="">Name <span style="color:red;">*</span></span>
                                        <input aria-label="Name" class="form-control" type="text" id="company_name" name="name" value="<?php echo $return['name']; ?>">
                                        <div style="display: none;color: red;" id="company_name_err"> </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Company Number <span style="color:red;">*</span></span>
                                        <select aria-label="Companynumber" class="form-select js-example-basic-single  col-lg-4" name="companynumber" id="companynumber">
                                            <option value="" label="0">Select Any one</option>
                                            <option value="1" <?php echo $return['companynumber'] == '1' ? 'selected' : ''; ?>>1</option>
                                            <option value="2" <?php echo $return['companynumber'] == '2' ? 'selected' : ''; ?>>2</option>
                                            <option value="3" <?php echo $return['companynumber'] == '3' ? 'selected' : ''; ?>>3</option>
                                            <option value="4" <?php echo $return['companynumber'] == '4' ? 'selected' : ''; ?>>4</option>
                                            <option value="5" <?php echo $return['companynumber'] == '5' ? 'selected' : ''; ?>>5</option>
                                            <option value="6" <?php echo $return['companynumber'] == '6' ? 'selected' : ''; ?>>6</option>
                                        </select>
                                        <div style="display: none;color: red;" id="companynumber_err"> </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Base Country <span style="color:red;">*</span></span>
                                        <select aria-label="Base Country" class="form-select js-example-basic-single  col-lg-4" name="base_country" id="base_country">
                                            <option value="0" <?php echo $return['base_country'] == '0' ? 'selected' : ''; ?>> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="base_country_err"> </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>GST State</span>
                                        <select aria-label="Gst State" class="form-select js-example-basic-single" id="gst_state" name="gst_state">
                                            <option value="0" <?php echo $return['gst_state'] == '0' ? 'selected' : ''; ?>> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="gst_state_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Incharge Name <span style="color:red;">*</span></span>
                                        <input aria-label="Incharge Name" class="form-control" type="text" id="incharge_name" name="incharge_name" value="<?php echo $return['incharge_name']; ?>">
                                        <div style="display: none;color: red;" id="incharge_name_err"> </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Telephone <span style="color:red;">*</span></span>
                                        <input aria-label="Telephone" class="form-control" type="text" id="telephone" name="telephone" value="<?php echo $return['telephone']; ?>">
                                        <div style="display: none;color: red;" id="telephone_err"></div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Email <span style="color:red;">*</span></span>
                                        <input aria-label="Email" class="form-control" type="text" id="email" name="email" value="<?php echo $return['email']; ?>">
                                        <div style="display: none;color: red;" id="email_err"> </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Parent Company</span>
                                        <select aria-label="Parent" class="form-select js-example-basic-single" id="parent_id" name="parent_id">
                                            <option value="0" <?php echo $return['parent_id'] == '0' ? 'selected' : ''; ?>> Select Any one</option>
                                            <option value="1" <?php echo $return['parent_id'] == '1' ? 'selected' : ''; ?>>tickday</option>
                                            </optgroup>
                                        </select>
                                        <div style="display: none;color: red;" id="parent_id_err"> Please Select Parent Company </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="form-group col-lg-3">
                                                <button type="button" class="btn btn-primary" id="getLocationBtn">Get Location</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Latitude <span style="color:red;">*</span></span>
                                        <input aria-label="Location Latitude" class="form-control" type="text" id="location_latitude" name="location_latitude" value="<?php echo $return['location_latitude']; ?>" readonly>
                                        <div style="display: none;color: red;" id="location_latitude_err"> </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Longitude <span style="color:red;">*</span></span>
                                        <input aria-label="Location Longitude" class="form-control" type="text" id="location_longitude" name="location_longitude" value="<?php echo $return['location_longitude']; ?>" readonly>
                                        <div style="display: none;color: red;" id="location_longitude_err"> </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Fax</span>
                                        <input aria-label="Fax" class="form-control" type="text" id="fax" name="fax" value="<?php echo $return['fax']; ?>">
                                        <div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Hr Connect</span>
                                        <select aria-label="Hr Connect" class="form-select js-example-basic-single" id="hr_connect" name="hr_connect">
                                            <option value="0" <?php echo $return['hr_connect'] == '0' ? 'selected' : ''; ?>> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="hr_connect_err"> Please Select Hr Connect </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Finance Connect</span>
                                        <select aria-label="Finance Connect" class="form-select js-example-basic-single" id="finance_connect" name="finance_connect">
                                            <option value="0" <?php echo $return['finance_connect'] == '0' ? 'selected' : ''; ?>> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="finance_connect_err"> Please Select Finance Connect </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span>Admin Connect</span>
                                        <select aria-label="Admin Connect" class="form-select js-example-basic-single" id="admin_connect" name="admin_connect">
                                            <option value="0" <?php echo $return['admin_connect'] == '0' ? 'selected' : ''; ?>> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="admin_connect_err"> Please Select Admin Connect </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span>Default Currency</span>
                                        <select aria-label="Currency" class="form-select js-example-basic-single" id="currency_id" name="currency_id">
                                            <option value="0" <?php echo $return['currency_id'] == '0' ? 'selected' : ''; ?>> Select Any one </option>
                                        </select>
                                        <div style="display: none;color: red;" id="currency_id_err"> Please Select Default Currency </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Bank Account</span>
                                        <input aria-label="Bank Account" class="form-control" type="text" id="bank_account" name="bank_account" value="<?php echo $return['bank_account']; ?>">
                                        <div style="display: none;color: red;" id="bank_account_err"> Please enter Bank Account </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Payable Account</span>
                                        <input aria-label="Payable Account" class="form-control" type="text" id="payable_account" name="payable_account" value="<?php echo $return['payable_account']; ?>">
                                        <div style="display: none;color: red;" id="payable_account_err"> Please enter Payable Account </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span class="">Receiable Account</span>
                                        <input aria-label="Receiable Account" class="form-control" type="text" id="receiable_account" name="receiable_account" value="<?php echo $return['receiable_account']; ?>">
                                        <div style="display: none;color: red;" id="receiable_account_err"> Please enter Receiable Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Income Account</span>
                                        <input aria-label="Income Account" class="form-control" type="text" id="income_account" name="income_account" value="<?php echo $return['income_account']; ?>">
                                        <div style="display: none;color: red;" id="income_account_err"> Please enter Income Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Cogs Account</span>
                                        <input aria-label="Cogs Account" class="form-control" type="text" id="cogs_account" name="cogs_account" value="<?php echo $return['cogs_account']; ?>">
                                        <div style="display: none;color: red;" id="cogs_account_err"> Please enter Cogs Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Round Off Account</span>
                                        <input aria-label="Round Off Account" class="form-control" type="text" id="round_off_account" name="round_off_account" value="<?php echo $return['round_off_account']; ?>">
                                        <div style="display: none;color: red;" id="round_off_account_err"> Please enter Round Off Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Employee Adv Account</span>
                                        <input aria-label="Employee Adv Account" class="form-control" type="text" id="employee_adv_account" name="employee_adv_account" value="<?php echo $return['employee_adv_account']; ?>">
                                        <div style="display: none;color: red;" id="employee_adv_account_err"> Please enter Employee Adv Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Deferred Revenu Account</span>
                                        <input aria-label="Deferred Revenu Account" class="form-control" type="text" id="deferred_revenu_account" name="deferred_revenu_account" value="<?php echo $return['deferred_revenu_account']; ?>">
                                        <div style="display: none;color: red;" id="deferred_revenu_account_err"> Please enter Deferred Revenu Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Deferred Expenses Account</span>
                                        <input aria-label="Deferred Expenses Account" class="form-control" type="text" id="deferred_expenses_account" name="deferred_expenses_account" value="<?php echo $return['deferred_expenses_account']; ?>">
                                        <div style="display: none;color: red;" id="deferred_expenses_account_err"> Please enter Deferred Expenses Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Write Off Account</span>
                                        <input aria-label="Write Off Account" class="form-control" type="text" id="write_off_account" name="write_off_account" value="<?php echo $return['write_off_account']; ?>">
                                        <div style="display: none;color: red;" id="write_off_account_err"> Please enter Write Off Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Disc Allowed Account</span>
                                        <input aria-label="Disc Allowed Account" class="form-control" type="text" id="disc_allowed_account" name="disc_allowed_account" value="<?php echo $return['disc_allowed_account']; ?>">
                                        <div style="display: none;color: red;" id="disc_allowed_account_err"> Please enter Disc Allowed Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Disc Received Account</span>
                                        <input aria-label="Disc Received Account" class="form-control" type="text" id="disc_received_account" name="disc_received_account" value="<?php echo $return['disc_received_account']; ?>">
                                        <div style="display: none;color: red;" id="disc_received_account_err"> Please enter Disc Received Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Sale Exchange Diff Account</span>
                                        <input aria-label="Sale Exchange Diff Account" class="form-control" type="text" id="sale_exchange_diff_account" name="sale_exchange_diff_account" value="<?php echo $return['sale_exchange_diff_account']; ?>">
                                        <div style="display: none;color: red;" id="sale_exchange_diff_account_err"> Please enter Sale Exchange Diff Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Purchase Exchange Diff Account</span>
                                        <input aria-label="Purchase Exchange Diff Account" class="form-control" type="text" id="purchase_exchange_diff_account" name="purchase_exchange_diff_account" value="<?php echo $return['purchase_exchange_diff_account']; ?>">
                                        <div style="display: none;color: red;" id="purchase_exchange_diff_account_err"> Please enter Purchase Exchange Diff Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Stock In Hand Account</span>
                                        <input aria-label="Stock In Hand Account" class="form-control" type="text" id="stock_in_hand_account" name="stock_in_hand_account" value="<?php echo $return['stock_in_hand_account']; ?>">
                                        <div style="display: none;color: red;" id="stock_in_hand_account_err"> Please enter Stock In Hand Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Stock Adj Account</span>
                                        <input aria-label="Stock Adj Account" class="form-control" type="text" id="stock_adj_account" name="stock_adj_account" value="<?php echo $return['stock_adj_account']; ?>">
                                        <div style="display: none;color: red;" id="stock_adj_account_err"> Please enter Stock Adj Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Stock Unbilled Account</span>
                                        <input aria-label="Stock Unbilled Account" class="form-control" type="text" id="stock_unbilled_account" name="stock_unbilled_account" value="<?php echo $return['stock_unbilled_account']; ?>">
                                        <div style="display: none;color: red;" id="stock_unbilled_account_err"> Please enter Stock Unbilled Account </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Cost Center</span>
                                        <input aria-label="Cost Center" class="form-control" type="text" id="cost_center" name="cost_center" value="<?php echo $return['cost_center']; ?>">
                                        <div style="display: none;color: red;" id="cost_center_err"> Please enter Cost Center </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Inventory Valuation</span>
                                        <input aria-label="Inventory Valuation" class="form-control" type="text" id="inventory_valuation" name="inventory_valuation" value="<?php echo $return['inventory_valuation']; ?>">
                                        <div style="display: none;color: red;" id="inventory_valuation_err"> Please enter Inventory Valuation </div>
                                    </div>

                                    <div class="col-lg-6 col-xs-12 form-group m-b-10">
                                        <span>From Date</span>
                                        <input aria-label="From Date" class="form-control" type="date" name="from_date"
                                            value="<?php echo isset($return['from_date']) && $return['from_date'] != '' ? date('Y-m-d', strtotime($return['from_date'])) : date('Y-m-d'); ?>" readonly />
                                    </div>

                                    <div class="col-lg-6 col-xs-12 form-group">
                                        <span>To Date</span>
                                        <input aria-label="To Date" class="form-control" type="date" name="to_date" value="<?php echo isset($return['to_date']) && $return['to_date'] != '' ? date('Y-m-d', strtotime($return['to_date'])) :  date('Y-m-d', strtotime('+5 years')); ?>" />
                                    </div>

                                </div>
                                <div class="card-footer border-top">
                                    <div class="text-center">
                                        <?php if ($rid != "") {  ?>
                                            <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                                                <i class="fa fa-file-text-o"></i> Update
                                            </button>
                                            <input name="rid" type="hidden" value="<?php echo $return['id']; ?>">
                                        <?php } else {  ?>
                                            <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary">
                                                <i class="fa fa-file-text-o"></i> Save
                                            </button>
                                        <?php } ?>
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

<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Select Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let map, marker;
    let mapInitialized = false;
    $(document).ready(function() {
        $("#getLocationBtn").click(function() {
            $("#mapModal").modal("show");

            if (!mapInitialized) {
                initializeMap();
                mapInitialized = true;
            }
        });
        $("#mapModal").on("shown.bs.modal", function() {
            map.invalidateSize();
        });

        $("#locationForm").submit(function(e) {
            $(".error").hide();
            let isValid = true;

            const fields = [{
                    id: "company_name",
                    errorId: "company_name_err",
                    errorMsg: "Please enter Name"
                },
                {
                    id: "companynumber",
                    errorId: "companynumber_err",
                    errorMsg: "Please select a company number"
                },
                {
                    id: "base_country",
                    errorId: "base_country_err",
                    errorMsg: "Please select Base Country"
                },
                {
                    id: "gst_state",
                    errorId: "gst_state_err",
                    errorMsg: "Please select GST State"
                },
                {
                    id: "incharge_name",
                    errorId: "incharge_name_err",
                    errorMsg: "Please enter Incharge Name"
                },
                {
                    id: "telephone",
                    errorId: "telephone_err",
                    errorMsg: "Please enter Telephone"
                },
                {
                    id: "email",
                    errorId: "email_err",
                    errorMsg: "Please enter Email"
                },
                {
                    id: "location_latitude",
                    errorId: "location_latitude_err",
                    errorMsg: "Please select Latitude"
                },
                {
                    id: "location_longitude",
                    errorId: "location_longitude_err",
                    errorMsg: "Please select Longitude"
                },
            ];

            fields.forEach(field => {
                const value = $("#" + field.id).val();
                if (!value || value === "") {
                    $("#" + field.errorId).text(field.errorMsg).show();
                    isValid = false;
                } else {
                    $("#" + field.errorId).hide();
                }
            });
            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    function initializeMap() {
        map = L.map('map').setView([22.836946, 78.354492], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const searchControl = new L.Control.Search({
            url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
            jsonpParam: 'json_callback',
            propertyName: 'display_name',
            propertyLoc: ['lat', 'lon'],
            marker: false,
            autoCollapse: true,
            autoType: false,
            minLength: 2,
        });

        map.addControl(searchControl);

        searchControl.on('search:locationfound', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            if (marker) {
                marker.remove();
            }
            marker = L.marker([lat, lng]).addTo(map);

            $("#location_latitude").val(lat);
            $("#location_longitude").val(lng);

            map.setView([lat, lng], 12);
        });
        map.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            if (marker) {
                marker.remove();
            }

            marker = L.marker([lat, lng]).addTo(map);

            $("#location_latitude").val(lat);
            $("#location_longitude").val(lng);
        });

    }
</script>
