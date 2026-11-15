<?php
$_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submit'])) {
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
        unset($_POST['submit']);
        $_POST['rid'];
        $url = API . "?mode=insertamcaccountdetails&method=post";
        $response1 = file_post_contents($url, $_POST, 'update');
    } else {
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
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $url = API . "?mode=getamcaccountdetails&id=" . $rid;
    $response = callAPI($url);
    $result = json_decode($response, 1);
    $details = $result['amc_details'];
}
$url = API . "?mode=getalldetails";
$all_details_response = callAPI($url);
$all_details_response = json_decode($all_details_response, 1);

$gc_details = $all_details_response['gc_details'];
$st_details = $all_details_response['st_details'];

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
        <h4><?php echo __('Create Service'); ?></h4>
        <div class="card-header-right">
            <a href="account-management.html" class="btn waves-effect waves-light btn-primary">
                <i class="fa fa-reply text-white"></i> <?php echo __('Back'); ?>
            </a>
        </div>
    </div>
</div>
<form method="post" action="">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><?php echo __('Basic Information'); ?></h5>
                    <input name="rid" class="form-control" type="hidden" value="<?php echo $details['id'] != "" ? $details['id'] : ""; ?>">
                </div>
                <div class="card-body">
                    <div class="row mt-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Service Name'); ?></span>
                                <select name="account_group_id" class="form-control js-example-basic-single" title="Account Group Id" aria-label="Account Group Id">
                                    <option value="0"><?php echo __('Select Any One'); ?></option>
                                    <?php foreach ($ag_list as $id => $group_name): ?>
                                        <option value="<?php echo $id; ?>"
                                            <?php if ($details['account_group_id'] == $id) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars(__($group_name ?? '')); ?>
                                        </option>
                                    <?php endforeach; ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Service Company Name'); ?></span>
                                <input name="account_name" class="form-control" type="text" placeholder="<?php echo __('Enter Service Company Name'); ?>" value="<?php echo $details['account_name'] ?? ""; ?>" title="<?php echo __('Enter Service Company Name'); ?>" aria-label="<?php echo __('Enter Service Company Name'); ?>">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Payable/Reciveable'); ?></span>
                                <select name="payble_or_reciveable" class="form-control js-example-basic-single" title="Payble Or Reciveable" aria-label="Payble Or Reciveable">
                                    <option><?php echo __('Select Any One'); ?></option>
                                    <?php foreach ($gcd_list as $genc_detail_code => $genc_detail_name): ?>
                                        <option value="<?php echo $genc_detail_code; ?>"
                                            <?php if ($details['payble_or_reciveable'] == $genc_detail_code) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars(__($genc_detail_name ?? '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Email ID'); ?></span>
                                <input name="email_id" class="form-control" type="text" placeholder="<?php echo __('Enter E-mail ID'); ?>" value="<?php echo $details['email_id'] ?? ''; ?>" title="<?php echo __('Enter E-mail ID'); ?>" aria-label="<?php echo __('Enter E-mail ID'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Mobile No'); ?></span>
                                <input name="mobile_no" class="form-control" type="text" placeholder="<?php echo __('Enter Mobile No'); ?>" value="<?php echo $details['mobile_no'] ?? ''; ?>" title="<?php echo __('Enter Mobile No'); ?>" aria-label="<?php echo __('Enter Mobile No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Alternative No'); ?></span>
                                <input name="alternative_no" class="form-control" type="text" placeholder="<?php echo __('Enter Alternative No'); ?>" value="<?php echo $details['alternative_no'] ?? ''; ?>" title="<?php echo __('Enter Alternative No'); ?>" aria-label="<?php echo __('Enter Alternative No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Address Line 1'); ?></span>
                                <textarea name="address_line1" class="form-control" placeholder="<?php echo __('Enter Address'); ?>" title="<?php echo __('Enter Address'); ?>" aria-label="<?php echo __('Enter Address'); ?>"><?php echo $details['address_line1'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Address Line 2'); ?></span>
                                <textarea name="address_line2" class="form-control" placeholder="<?php echo __('Enter Address'); ?>" title="<?php echo __('Enter Address'); ?>" aria-label="<?php echo __('Enter Address'); ?>"><?php echo $details['address_line2'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Country'); ?></span>
                                <select name="country_id" id="country_id" class="form-control js-example-basic-single" onchange="updateStatesandCities(this)" data-selected-state="<?php echo $details['state_id']; ?>" data-selected-city="<?php echo $details['city_id']; ?>" title="<?php echo __('Country Id'); ?>" aria-label="<?php echo __('Country Id'); ?>">
                                    <option value="0"><?php echo __('Select Any One'); ?></option>
                                    <?php foreach ($states_list as $id => $country_name): ?>
                                        <option value="<?php echo $id; ?>" <?php if ($details['country_id'] == $id) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars(__($country_name ?? '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('State'); ?></span>
                                <select name="state_id" id="state_id" class="form-control js-example-basic-single" onchange="updateStatesandCities(this)" title="<?php echo __('State Id'); ?>" aria-label="<?php echo __('State Id'); ?>">
                                    <option value="0"><?php echo __('Select Any One'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('City'); ?></span>
                                <select name="city_id" id="city_id" class="form-control js-example-basic-single" title="<?php echo __('City Id'); ?>" aria-label="<?php echo __('City Id'); ?>">
                                    <option value="0"><?php echo __('Select Any One'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Pin'); ?></span>
                                <input name="pin" class="form-control" type="text" placeholder="<?php echo __('Enter Pin'); ?>" value="<?php echo $details['pin'] ?? ''; ?>" title="<?php echo __('Enter Pin'); ?>" aria-label="<?php echo __('Enter Pin'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('GST No'); ?></span>
                                <input name="gst_no" class="form-control" type="text" placeholder="<?php echo __('Enter GST No'); ?>" value="<?php echo $details['gst_no'] ?? ''; ?>" title="<?php echo __('Enter GST No'); ?>" aria-label="<?php echo __('Enter GST No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('CST No'); ?></span>
                                <input name="cst_no" class="form-control" type="text" placeholder="<?php echo __('Enter CST No'); ?>" value="<?php echo $details['cst_no'] ?? ''; ?>" title="<?php echo __('Enter CST No'); ?>" aria-label="<?php echo __('Enter CST No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Vat No'); ?></span>
                                <input name="vat_no" class="form-control" type="text" placeholder="<?php echo __('Enter Vat No'); ?>" value="<?php echo $details['vat_no'] ?? ''; ?>" title="<?php echo __('Enter Vat No'); ?>" aria-label="<?php echo __('Enter Vat No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Membership Card No'); ?></span>
                                <input name="membership_card_no" class="form-control" type="text" placeholder="<?php echo __('Enter Membership Card No'); ?>" value="<?php echo $details['membership_card_no'] ?? ''; ?>" title="<?php echo __('Enter Membership Card No'); ?>" aria-label="<?php echo __('Enter Membership Card No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Pan No'); ?></span>
                                <input name="pan_no" class="form-control" type="text" placeholder="<?php echo __('Enter Pan Card No'); ?>" value="<?php echo $details['pan_no'] ?? ''; ?>" title="<?php echo __('Enter Pan Card No'); ?>" aria-label="<?php echo __('Enter Pan Card No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Aadhaar No'); ?></span>
                                <input name="aadhaar_no" class="form-control" type="text" placeholder="<?php echo __('Enter Aadhaar No'); ?>" value="<?php echo $details['aadhaar_no'] ?? ''; ?>" title="<?php echo __('Enter Aadhaar No'); ?>" aria-label="<?php echo __('Enter Aadhaar No'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('D.O.B'); ?></span>
                                <input name="dob" class="form-control" type="date" placeholder="" value="<?php echo $details['dob'] ?? ''; ?>" title="<?php echo __('Dob'); ?>" aria-label="<?php echo __('Dob'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Aniversary'); ?></span>
                                <input name="aniversary" class="form-control" type="date" placeholder="" value="<?php echo $details['aniversary'] ?? ''; ?>" title="<?php echo __('Aniversary'); ?>" aria-label="<?php echo __('Aniversary'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Credit Limit'); ?></span>
                                <input name="credit_limit" class="form-control" type="text" placeholder="<?php echo __('Enter Credit Limit'); ?>" value="<?php echo $details['credit_limit'] ?? ''; ?>" title="<?php echo __('Enter Credit Limit'); ?>" aria-label="<?php echo __('Enter Credit Limit'); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <span class="form-label d-inline-block fw-bold"><?php echo __('Bill Limit'); ?></span>
                                <input name="bill_limit" class="form-control" type="text" placeholder="<?php echo __('Enter Bill Limit'); ?>" value="<?php echo $details['bill_limit'] ?? ''; ?>" title="<?php echo __('Enter Bill Limit'); ?>" aria-label="<?php echo __('Enter Bill Limit'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top text-center">
                    <?php if ($rid != "") {  ?>
                        <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                            <i class="fa fa-file-text-o"></i> <?php echo __('Update Account'); ?>
                        </button>
                    <?php } else {  ?>
                        <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                            <i class="fa fa-file-text-o"></i> <?php echo __('Save Account'); ?>
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="f-18 f-w-600"><?php echo __('Additional Information'); ?></h4>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Contact Person'); ?></span>
                        <input name="contact_person" class="form-control" type="text" placeholder="<?php echo __('Contact Person'); ?>" value="<?php echo $details['contact_person'] ?? ''; ?>" title="<?php echo __('Contact Person'); ?>" aria-label="<?php echo __('Contact Person'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Opening Balance'); ?></span>
                        <input name="opening_balance" class="form-control" type="text" placeholder="<?php echo __('Enter Opening Balance'); ?>" value="<?php echo $details['opening_balance'] ?? ''; ?>" title="<?php echo __('Enter Opening Balance'); ?>" aria-label="<?php echo __('Enter Opening Balance'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Credit Days for Sale'); ?></span>
                        <input name="credit_days_for_sale" class="form-control" type="text" placeholder="<?php echo __('Credit Days for Sale'); ?>" value="<?php echo $details['credit_days_for_sale'] ?? ''; ?>" title="<?php echo __('Credit Days for Sale'); ?>" aria-label="<?php echo __('Credit Days for Sale'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Credit Days for Purchase'); ?></span>
                        <input name="credit_days_for_purchase" class="form-control" type="text" placeholder="<?php echo __('Credit Days for Purchase'); ?>" value="<?php echo $details['credit_days_for_purchase'] ?? ''; ?>" title="<?php echo __('Credit Days for Purchase'); ?>" aria-label="<?php echo __('Credit Days for Purchase'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Audit Upto'); ?></span>
                        <input name="audit_upto" class="form-control" type="text" placeholder="<?php echo __('Audit Upto'); ?>" value="<?php echo $details['audit_upto'] ?? ''; ?>" title="<?php echo __('Audit Upto'); ?>" aria-label="<?php echo __('Audit Upto'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Any License No 1'); ?></span>
                        <input name="any_liense_no1" class="form-control" type="text" placeholder="<?php echo __('License No'); ?>" value="<?php echo $details['any_liense_no1'] ?? ''; ?>" title="<?php echo __('License No'); ?>" aria-label="<?php echo __('License No'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Any License No 2'); ?></span>
                        <input name="any_license_no2" class="form-control" type="text" placeholder="<?php echo __('License No'); ?>" value="<?php echo $details['any_license_no2'] ?? ''; ?>" title="<?php echo __('License No'); ?>" aria-label="<?php echo __('License No'); ?>">
                    </div>
                    <h4 class="f-18 f-w-600"><?php echo __('Bank Information'); ?></h4>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Bank Name'); ?></span>
                        <input name="bank_name" class="form-control" type="text" placeholder="<?php echo __('Bank Name'); ?>" value="<?php echo $details['bank_name'] ?? ''; ?>" title="<?php echo __('Bank Name'); ?>" aria-label="<?php echo __('Bank Name'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Branch Name'); ?></span>
                        <input name="branch_name" class="form-control" type="text" placeholder="<?php echo __('Branch Name'); ?>" value="<?php echo $details['branch_name'] ?? ''; ?>" title="<?php echo __('Branch Name'); ?>" aria-label="<?php echo __('Branch Name'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('Account No'); ?></span>
                        <input name="account_no" class="form-control" type="text" placeholder="<?php echo __('Account No'); ?>" value="<?php echo $details['account_no'] ?? ''; ?>" title="<?php echo __('Account No'); ?>" aria-label="<?php echo __('Account No'); ?>">
                    </div>
                    <div class="form-group mt-3">
                        <span class="form-label d-inline-block fw-bold"><?php echo __('IFSC Code'); ?></span>
                        <input name="ifsc_code" class="form-control" type="text" placeholder="<?php echo __('IFSC Code'); ?>" value="<?php echo $details['ifsc_code'] ?? ''; ?>" title="<?php echo __('IFSC Code'); ?>" aria-label="<?php echo __('IFSC Code'); ?>">
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
                        stateSelect.innerHTML = '<option value="0"><?php echo __('Select Any One'); ?></option>';

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
                        citySelect.innerHTML = '<option value="0"><?php echo __('Select Any One'); ?></option>';

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
    //                 stateSelect.innerHTML = '<option value="0"><?php echo __('Select Any One'); ?></option>';

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
    //                 stateSelect.innerHTML = '<option value="0"><?php echo __('Select Any One'); ?></option>';

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