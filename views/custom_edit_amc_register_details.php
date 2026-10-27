<?php
$_SESSION['apply_js']['multiselest'] = 1;

$url = API . "?mode=getallproductdetails";
$response = callAPI($url);
$response = json_decode($response, 1);
// print_r($response);
$products = $response['pm_details'];
$customers = $response['cust_details'];
$tech_details = $response['tech_details'];

$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API . "?mode=getamcregistrationdetails&method=get&id=" . $rid;
$che = callAPI($url);
$return = json_decode($che, 1);
$registration_details = $return['amc_registration_details'];
$dynamic_fields = $return['dynamic_fields_details'];
// print_r($dynamic_fields);

?>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Edit AMC Registration</h4>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn btn-primary waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>

    </div>

    <!-- AMC Edit Form -->
    <div class="modal-content mt-3">
        <div class="modal-header">
            <h5 class="modal-title" id="amcModalLabel">Edit AMC</h5>
        </div>
        <form action="amc_update.php" method="POST">
            <div class="modal-body">
                <input type="hidden" id="amcId" name="amc_id" value="<?php echo $amc_id; ?>">
                <input type="hidden" id="productId" name="product_id" value="<?php echo $data['product_id']; ?>">

                <div class="row">

                    <div class="col-lg-4 text-center">
                        <label class="form-label">Select Customer</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <select name="customer_id" class="form-control js-example-basic-single" id="selectCustomer">
                                <option value="0">--Select--</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?php echo $customer['id']; ?>" <?php echo $customer['id'] == $registration_details['customer_id'] ? 'selected' : ''; ?>>
                                        <?php echo $customer['customer_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Activation Date -->
                    <div class="col-lg-4 text-center">
                        <label class="form-label">Activation Date</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input name="activation_date" class="form-control" type="date" id="activationDate" value="<?php echo $registration_details['activation_date']; ?>" onchange="setDeactivationDate()">
                        </div>
                    </div>

                    <!-- Deactivation Date -->
                    <div class="col-lg-4 text-center">
                        <label class="form-label">Deactivation Date</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input name="deactivation_date" class="form-control" type="date" id="deactivationDate" value="<?php echo $registration_details['deactivation_date']; ?>" readonly>
                        </div>
                    </div>

                    <!-- Payment/Service Type -->
                    <div class="col-lg-4 text-center">
                        <label class="form-label">Payment/Service Type</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <select name="payment_type" class="form-control js-example-basic-single" id="serviceType" onchange="toggleServiceCharge()">
                                <option value="0">--Select--</option>
                                <option value="Free" <?php echo $registration_details['payment_type'] == 'Free' ? 'selected' : ''; ?>>Free</option>
                                <option value="Paid" <?php echo $registration_details['payment_type'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Service Charge -->
                    <div class="col-lg-4 text-center" id="serviceChargeContainer" style="display: none;">
                        <label class="form-label">Service Charge</label>
                    </div>
                    <div class="col-lg-8" id="serviceChargeInputContainer" style="display: none;">
                        <div class="form-group">
                            <input name="service_charge" type="number" class="form-control border" id="serviceCharge" value="<?php echo $registration_details['service_charge']; ?>" placeholder="0.00" />
                        </div>
                    </div>

                    <div class="col-lg-4 text-center">
                        <label class="form-label">Service on</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <select name="service_on" class="form-control js-example-basic-single" id="serviceOn" name="serviceOn" onchange="handleServiceTypeChange()">
                                <option value="0">--Select--</option>
                                <option value="Daily" <?php echo $registration_details['service_on'] == 'Daily' ? 'selected' : ''; ?>>Daily</option>
                                <option value="Weekly" <?php echo $registration_details['service_on'] == 'Weekly' ? 'selected' : ''; ?>>Weekly</option>
                                <option value="Monthly" <?php echo $registration_details['service_on'] == 'Monthly' ? 'selected' : ''; ?>>Monthly</option>
                                <option value="Yearly" <?php echo $registration_details['service_on'] == 'Yearly' ? 'selected' : ''; ?>>Yearly</option>
                            </select>
                        </div>
                    </div>

                    <!-- No of Service -->
                    <div class="col-lg-4 text-center">
                        <label class="form-label">No of Services</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input name="no_of_services" value="<?php echo $registration_details['no_of_services']; ?>" class="form-control" type="number" id="noOfService" min="0" placeholder="Enter Number of Service" oninput="generateServiceFields()">
                        </div>
                    </div>

                    <!-- Dynamic Fields Container -->
                    <div id="dynamicFieldsContainer" class="row ">

                    </div>
                    <div class="col-lg-4 text-center">
                        <label class="form-label">Select Technician</label>
                    </div>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <select name="technician_id" class="form-control js-example-basic-single" id='selectTech'>
                                <option value="0">--Select--</option>
                                <?php foreach ($tech_details as $tech): ?>
                                    <option value="<?php echo $tech['account_id']; ?>" <?php echo $tech['account_id'] == $registration_details['technician_id'] ? 'selected' : ''; ?>>
                                        <?php echo $tech['account_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="amc_list.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>


<script>
    function generateDynamicFields(serviceOn, dynamicFields) {

        dynamicFields = Object.values(dynamicFields);
        if (!Array.isArray(dynamicFields)) {
            console.error('dynamicFields is not an array:', dynamicFields);
            return;
        }
        const container = document.getElementById('dynamicFieldsContainer');
        container.innerHTML = ''; // Clear previous fields

        dynamicFields.forEach((field) => {
            const fieldKey = field['service_key'];
            const fieldValue = field['service_value'];

            // Create a wrapper div for the label
            const labelDiv = document.createElement('div');
            labelDiv.className = 'col-lg-4 text-center';
            const label = document.createElement('label');
            label.className = 'form-label';
            label.textContent = fieldKey.replace(/_/g, ' '); // Replace underscores with spaces for display
            labelDiv.appendChild(label);

            // Create a wrapper div for the input/select
            const inputDiv = document.createElement('div');
            inputDiv.className = 'col-lg-8';
            const formGroupDiv = document.createElement('div');
            formGroupDiv.className = 'form-group';

            // Determine field type and create the appropriate input field
            if (serviceOn === 'Daily' && fieldKey.startsWith('time_')) {
                const timeInput = document.createElement('input');
                timeInput.type = 'time';
                timeInput.name = `dynamic_field[${fieldKey}]`;
                timeInput.className = 'form-control';
                timeInput.value = fieldValue; // Set the prefilled value
                formGroupDiv.appendChild(timeInput);
            } else if (serviceOn === 'Weekly' && fieldKey.startsWith('week_')) {
                const select = document.createElement('select');
                select.className = 'form-control js-example-basic-single';
                select.name = `dynamic_field[${fieldKey}]`;

                const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                daysOfWeek.forEach(day => {
                    const option = document.createElement('option');
                    option.value = day;
                    option.textContent = day;
                    if (day === fieldValue) option.selected = true;
                    select.appendChild(option);
                });
                formGroupDiv.appendChild(select);
            } else if (serviceOn === 'Monthly' && fieldKey.startsWith('month_')) {
                const select = document.createElement('select');
                select.className = 'form-control js-example-basic-single';
                select.name = `dynamic_field[${fieldKey}]`;

                for (let day = 1; day <= 30; day++) {
                    const option = document.createElement('option');
                    option.value = day;
                    option.textContent = `Day ${day}`;
                    if (day == fieldValue) option.selected = true;
                    select.appendChild(option);
                }
                formGroupDiv.appendChild(select);
            } else if (serviceOn === 'Yearly' && fieldKey.startsWith('year_')) {
                const dateInput = document.createElement('input');
                dateInput.type = 'date';
                dateInput.name = `dynamic_field[${fieldKey}]`;
                dateInput.className = 'form-control';
                dateInput.value = fieldValue; // Set the prefilled value
                formGroupDiv.appendChild(dateInput);
            }

            // Append label and input to the container
            inputDiv.appendChild(formGroupDiv);
            container.appendChild(labelDiv);
            container.appendChild(inputDiv);
        });
    }

    // Call generateDynamicFields function to populate dynamic fields based on fetched data
    generateDynamicFields('<?php echo $registration_details['service_on']; ?>', <?php echo json_encode($dynamic_fields); ?>);
</script>