<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submit'])) {
    // echo "<pre>"; print_r($_POST);  
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $dob = str_replace('/"', '-', $_POST['dob']);
    $dob =  date('Y-m-d', strtotime($dob));
    $aniversary = str_replace('/"', '-', $_POST['aniversary']);
    $aniversary =  date('Y-m-d', strtotime($aniversary));
    $_POST['dob'] = $dob;
    $_POST['aniversary'] = $aniversary;

    $rid = $_POST['rid'];
    if ($rid != "") {
        // echo "Update";  print_r($_POST); exit;
        unset($_POST['submit']);
        $_POST['rid'];
        $url = API . "?mode=insertamcaccountdetails&method=post";
        $response1 = file_post_contents($url, $_POST, 'update');
    } else {
        // echo "ADD";  print_r($_POST); exit;
        unset($_POST['submit']);
        $url = API . "?mode=insertamcaccountdetails&method=post";
        $response1 = file_post_contents($url, $_POST, 'insert');
    }
    $res = json_decode($response1, 1);
    $response = $res['res'];
    if ($response) {
        header("location:index.php?option=COM_LIST_AMC_ACCOUNT_DETAILS");
    }
}
if ($_GET['Rid'] != "") {
    // print_r($_GET);
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $url = API . "?mode=getamcaccountdetails&id=" . $rid;
    // echo $url;   exit;
    $response = callAPI($url);
    $result = json_decode($response, 1);
    // print_r($result);
    $details = $result['amc_details'];
}
$url = API . "?mode=getalldetails";
$all_details_response = callAPI($url);
$all_details_response = json_decode($all_details_response, 1);

$gc_details = $all_details_response['gc_details'];
$st_details = $all_details_response['st_details'];
// $ct_details = $all_details_response['ct_details'];

$account_groups = $all_details_response['account_groups'];

$gcd_list = $states_list  = $ag_list =  array();
foreach ($gc_details as $gcd) {
    $gcd_list[$gcd['genc_detail_code']] = $gcd['genc_detail_name'];
}
foreach ($st_details as $st) {
    $states_list[$st['id']] = $st['country_name'];
}
foreach ($account_groups as $ag) {
    $ag_list[$ag['id']] = $ag['group_name'];
}

?>

<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h4>Create Service</h4>
        <div class="card-header-right">
            <a href="account-management.html" class="btn waves-effect waves-light btn-primary">
                <i class="fa fa-reply text-white"></i> Back
            </a>
        </div>
    </div>
</div>
<form method="post" action="">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>Basic Information</h5>
                    <input name="rid" class="form-control" type="hidden" value="<?php echo $details['id'] != "" ? $details['id'] : ""; ?>">
                </div>
                <div class="card-body">
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Service Name</label>
                                <select name="account_group_id" class="form-control js-example-basic-single">
                                    <option value="0">Select Any One</option>
                                    <?php foreach ($ag_list as $id => $group_name): ?>
                                        <option value="<?php echo $id; ?>"
                                            <?php if ($details['account_group_id'] == $id) echo 'selected'; ?>>
                                            <?php echo $group_name; ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Service Company Name</label>
                                <input name="account_name" class="form-control" type="text" placeholder="Enter Service Company Name" value="<?php echo $details['account_name'] ?? ""; ?>">
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Payable/Reciveable</label>
                                <select name="payble_or_reciveable" class="form-control js-example-basic-single">
                                    <option>Select Any One</option>
                                    <?php foreach ($gcd_list as $genc_detail_code => $genc_detail_name): ?>
                                        <option value="<?php echo $genc_detail_code; ?>"
                                            <?php if ($details['payble_or_reciveable'] == $genc_detail_code) echo 'selected'; ?>>
                                            <?php echo $genc_detail_name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Email ID</label>
                                <input name="email_id" class="form-control" type="text" placeholder="Enter E-mail ID" value="<?php echo $details['email_id'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Mobile No</label>
                                <input name="mobile_no" class="form-control" type="text" placeholder="Enter Mobile No" value="<?php echo $details['mobile_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Alternative No</label>
                                <input name="alternative_no" class="form-control" type="text" placeholder="Enter Alternative No" value="<?php echo $details['alternative_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Address Line 1</label>
                                <textarea name="address_line1" class="form-control" placeholder="Enter Address"><?php echo $details['address_line1'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Address Line 2</label>
                                <textarea name="address_line2" class="form-control" placeholder="Enter Address"><?php echo $details['address_line2'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label">Country</label>
                                <select name="country_id" id="country_id" class="form-control js-example-basic-single" onchange="updateStatesandCities(this)" data-selected-state="<?php echo $details['state_id']; ?>" data-selected-city="<?php echo $details['city_id']; ?>">
                                    <option value="0">Select Any One</option>
                                    <?php foreach ($states_list as $id => $country_name): ?>
                                        <option value="<?php echo $id; ?>" <?php if ($details['country_id'] == $id) echo 'selected'; ?>>
                                            <?php echo $country_name; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label">State</label>
                                <select name="state_id" id="state_id" class="form-control js-example-basic-single" onchange="updateStatesandCities(this)">
                                    <option value="0">Select Any One</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <select name="city_id" id="city_id" class="form-control js-example-basic-single">
                                    <option value="0">Select Any One</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <label class="form-label">Pin</label>
                                <input name="pin" class="form-control" type="text" placeholder="Enter Pin" value="<?php echo $details['pin'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">GST No</label>
                                <input name="gst_no" class="form-control" type="text" placeholder="Enter GST No" value="<?php echo $details['gst_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">CST No</label>
                                <input name="cst_no" class="form-control" type="text" placeholder="Enter CST No" value="<?php echo $details['cst_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Vat No</label>
                                <input name="vat_no" class="form-control" type="text" placeholder="Enter Vat No" value="<?php echo $details['vat_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Membership Card No</label>
                                <input name="membership_card_no" class="form-control" type="text" placeholder="Enter Membership Card No" value="<?php echo $details['membership_card_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Pan No</label>
                                <input name="pan_no" class="form-control" type="text" placeholder="Enter Pan Card No" value="<?php echo $details['pan_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Aadhaar No</label>
                                <input name="aadhaar_no" class="form-control" type="text" placeholder="Enter Aadhaar No" value="<?php echo $details['aadhaar_no'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">D.O.B</label>
                                <input name="dob" class="form-control" type="date" placeholder="" value="<?php echo $details['dob'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Aniversary</label>
                                <input name="aniversary" class="form-control" type="date" placeholder="" value="<?php echo $details['aniversary'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Credit Limit</label>
                                <input name="credit_limit" class="form-control" type="text" placeholder="Enter Credit Limit" value="<?php echo $details['credit_limit'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Bill Limit</label>
                                <input name="bill_limit" class="form-control" type="text" placeholder="Enter Bill Limit" value="<?php echo $details['bill_limit'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top text-center">
                    <?php if ($rid != "") {  ?>
                        <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                            <i class="fa fa-file-text-o"></i> Update Account
                        </button>
                    <?php } else {  ?>
                        <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                            <i class="fa fa-file-text-o"></i> Save Account
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="f-18 f-w-600">Additional Information</h4>
                    <div class="form-group mt-3">
                        <label class="form-label">Contact Person</label>
                        <input name="contact_person" class="form-control" type="text" placeholder="Contact Person" value="<?php echo $details['contact_person'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Opening Balance</label>
                        <input name="opening_balance" class="form-control" type="text" placeholder="Enter Opening Balance" value="<?php echo $details['opening_balance'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Credit Days for Sale</label>
                        <input name="credit_days_for_sale" class="form-control" type="text" placeholder="Credit Days for Sale" value="<?php echo $details['credit_days_for_sale'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Credit Days for Purchase</label>
                        <input name="credit_days_for_purchase" class="form-control" type="text" placeholder="Credit Days for Purchase" value="<?php echo $details['credit_days_for_purchase'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Audit Upto</label>
                        <input name="audit_upto" class="form-control" type="text" placeholder="Audit Upto" value="<?php echo $details['audit_upto'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Any License No 1</label>
                        <input name="any_liense_no1" class="form-control" type="text" placeholder="License No" value="<?php echo $details['any_liense_no1'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Any License No 2</label>
                        <input name="any_license_no2" class="form-control" type="text" placeholder="License No" value="<?php echo $details['any_license_no2'] ?? ''; ?>">
                    </div>
                    <h4 class="f-18 f-w-600">Bank Information</h4>
                    <div class="form-group mt-3">
                        <label class="form-label">Bank Name</label>
                        <input name="bank_name" class="form-control" type="text" placeholder="Bank Name" value="<?php echo $details['bank_name'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Branch Name</label>
                        <input name="branch_name" class="form-control" type="text" placeholder="Branch Name" value="<?php echo $details['branch_name'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">Account No</label>
                        <input name="account_no" class="form-control" type="text" placeholder="Account No" value="<?php echo $details['account_no'] ?? ''; ?>">
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label">IFSC Code</label>
                        <input name="ifsc_code" class="form-control" type="text" placeholder="IFSC Code" value="<?php echo $details['ifsc_code'] ?? ''; ?>">
                    </div>
                </div>
</form>
</div>
</div>
</div>
<!-- Pradeep HTML End here -->

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
                        stateSelect.innerHTML = '<option value="0">Select Any One</option>';

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
                        citySelect.innerHTML = '<option value="0">Select Any One</option>';

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


    // function updateStatesandCities(selectElement){
    //     const selectedValue = selectElement.value;
    //     const elementName = selectElement.name;

    //     console.log(selectedValue, elementName );
    //     if (elementName == 'country_id'){
    //         var table_name = 'states';
    //         var field = 'country_id';
    //     }
    //     if (elementName == 'state_id'){
    //         var table_name = 'cities';
    //         var field = 'state_id';
    //     }
    //     jQuery.ajax({
    //             url: "ajax.php",
    //             data:'page=getselectedlist&id='+selectedValue+'&field='+field+'&table_name='+table_name,
    //             type: "POST",
    //             success:function(data){
    //             // console.log("Responce:"+data);
    //             const response = JSON.parse(data);
    //             console.log(table_name)
    //             if (table_name === 'states') {
    //                 const stateSelect = document.getElementById('state_id');
    //                 stateSelect.innerHTML = '<option value="0">Select Any One</option>';

    //                 // Populate the state dropdown with the response data
    //                 response.forEach(function(state) {
    //                     const option = document.createElement('option');
    //                     option.value = state.id;
    //                     option.textContent = state.state_name;

    //                     // Append each option to the state dropdown
    //                     stateSelect.appendChild(option);
    //                 });
    //             }
    //             if (table_name === 'cities') {
    //                 const stateSelect = document.getElementById('city_id');
    //                 stateSelect.innerHTML = '<option value="0">Select Any One</option>';

    //                 // Populate the state dropdown with the response data
    //                 response.forEach(function(state) {
    //                     const option = document.createElement('option');
    //                     option.value = state.id;
    //                     option.textContent = state.city_name;

    //                     // Append each option to the state dropdown
    //                     stateSelect.appendChild(option);
    //                 });
    //             }


    //         },
    //         error:function (){
    //             console.error("Error occurred while fetching data.");
    //         }
    //     });        
    // }
</script>