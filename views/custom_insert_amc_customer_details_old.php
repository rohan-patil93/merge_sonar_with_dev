<?php
$_SESSION['apply_js']['multiselest'] = 1;
$rid = "";
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (isset($_POST['submit'])) {
    $table_name = 'amc_customer_details';
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
        unset($_POST['existing_image']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowbasepimarydata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }
    // echo "<pre>"; print_r($response);
}
$url = API . "?mode=getalldetails";
$all_details_response = callAPI($url);
$all_details_response = json_decode($all_details_response, 1);
$st_details = $all_details_response['st_details'];

$states_list = array();

foreach ($st_details as $st) {
    $states_list[$st['id']] = $st['country_name'];
}

?>

<div class="page-body">
    <!-- Pradeep HTML Start here -->
    <div class="card">
        <div class="card-header">
            <h4>New Customer</h4>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
    <form method="post" action="">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary p-3">
                        <h4 class="mb-0">New Customer</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Customer Name</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="customer_name" class="form-control" type="text" placeholder="Enter Customer Name">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Address 1</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="address_line1" class="form-control" type="text" placeholder="Enter Address">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Address 2</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="address_line2" class="form-control" type="text" placeholder="Enter Address">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Country</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">

                                    <select name="country_id" id="country_id" class="form-control js-example-basic-single" onchange="updateStatesandCities(this)" data-selected-state="<?php echo $details['state_id']; ?>" data-selected-city="<?php echo $details['city_id']; ?>">
                                        <option value="0">--Select--</option>
                                        <?php foreach ($states_list as $id => $country_name): ?>
                                            <option value="<?php echo $id; ?>" <?php if ($details['country_id'] == $id) echo 'selected'; ?>>
                                                <?php echo $country_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">State</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <select name="state_id" id="state_id" class="form-control js-example-basic-single" onchange="updateStatesandCities(this)">
                                        <option value="0">--Select--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">City</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <select name="city_id" id="city_id" class="form-control js-example-basic-single">
                                        <option value="0">--Select--</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6 text-end">
                                <label class="form-label">Pin No</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="pin" class="form-control" type="text" placeholder="Enter Pin No">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="email_id" class="form-control" type="text" placeholder="Enter Email ID">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Mobile No</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="mobile_no" class="form-control" type="text" placeholder="Enter Mobile No">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Alternative No</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="alternative_no" class="form-control" type="text" placeholder="Enter Alternative No">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Customer Pan No</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="pan_no" class="form-control" type="text" placeholder="Enter Customer Pan No">
                                </div>
                            </div>
                            <div class="col-lg-6 text-end">
                                <label class="form-label">Customer GST No</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input name="gst_no" class="form-control" type="text" placeholder="Enter Customer GST No">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top text-center">
                        <?php if ($rid != "") {  ?>
                            <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                                <i class="fa fa-file-text-o"></i> Update Customer
                            </button>
                        <?php } else {  ?>
                            <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary">
                                <i class="fa fa-file-text-o"></i> Save Customer
                            </button>
                        <?php } ?>
                    </div>

                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-success p-3">
                        <h4 class="mb-0">Exiting Customer</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th>Customer</th>
                                    <th>Mobile</th>
                                    <th>Select</th>
                                </tr>
                                <tr>
                                    <td>Parshant Ghosh</td>
                                    <td>+91 000 000 0000</td>
                                    <td>
                                        <a href="" class="form-label label label-success"><i class="fa fa-check text-white"></i> Select</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- Pradeep HTML End here -->
</div>

<script>
    function updateStatesandCities(selectElement) {
        const selectedValue = selectElement.value;
        const elementName = selectElement.name;

        // Get initial state and city from data attributes
        const selectedState = document.getElementById('country_id').dataset.selectedState;
        const selectedCity = document.getElementById('country_id').dataset.selectedCity;

        let table_name, field;

        if (elementName === 'country_id') {
            table_name = 'states';
            field = 'country_id';
        } else if (elementName === 'state_id') {
            table_name = 'cities';
            field = 'state_id';
        }

        jQuery.ajax({
            url: "ajax.php",
            data: `page=getselectedlist&id=${selectedValue}&field=${field}&table_name=${table_name}`,
            type: "POST",
            success: function(data) {
                if (data) {
                    const response = JSON.parse(data);

                    if (table_name === 'states') {
                        const stateSelect = document.getElementById('state_id');
                        stateSelect.innerHTML = '<option value="0">--Select--</option>';

                        response.forEach(function(state) {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.state_name;

                            // Set the option as selected if it matches the initial state
                            if (state.id == selectedState) {
                                option.selected = true;
                            }

                            stateSelect.appendChild(option);
                        });

                        // Trigger change event to load cities if state was pre-selected
                        if (selectedState && elementName === 'country_id') {
                            stateSelect.dispatchEvent(new Event('change'));
                        }
                    }

                    if (table_name === 'cities') {
                        const citySelect = document.getElementById('city_id');
                        citySelect.innerHTML = '<option value="0">--Select--</option>';

                        response.forEach(function(city) {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.city_name;

                            // Set the option as selected if it matches the initial city
                            if (city.id == selectedCity) {
                                option.selected = true;
                            }

                            citySelect.appendChild(option);
                        });
                    }
                } else {
                    console.error("No data received.");
                }
            },
            error: function() {
                console.error("Error occurred while fetching data.");
            }
        });
    }

    // Initial trigger to load states based on selected country
    document.addEventListener("DOMContentLoaded", function() {
        const countrySelect = document.getElementById('country_id');
        if (countrySelect.value !== '0') {
            updateStatesandCities(countrySelect);
        }
    });
</script>