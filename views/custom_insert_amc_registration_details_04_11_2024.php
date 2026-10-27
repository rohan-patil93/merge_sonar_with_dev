<?php
$_SESSION['apply_js']['multiselest'] = 1;

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
if (isset($_POST['submit'])) {
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $activation_date = str_replace('/"', '-', $_POST['activation_date']);
    $activation_date =  date('Y-m-d', strtotime($activation_date));
    $deactivation_date = str_replace('/"', '-', $_POST['deactivation_date']);
    $deactivation_date =  date('Y-m-d', strtotime($deactivation_date));
    $_POST['activation_date'] = $activation_date;
    $_POST['deactivation_date'] = $deactivation_date;
    unset($_POST['submit']);

    $url = API . "?mode=insertamcregistrationdetails&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
     if ($response1 != ''){
         header("location:index.php?option=COM_LIST_AMC_REGISTRATION_DETAILS"); exit; 
    }
}

$url = API . "?mode=getallproductdetails";
$response = callAPI($url);
$response = json_decode($response, 1);
// print_r($response);
$products = $response['pm_details'];
$customers = $response['cust_details'];
$tech_details = $response['tech_details'];
?>

<style>
    .box {
        height: 200px;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-color: #ffffff;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 15px;
        margin: 10px;
    }

    .box-title {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .box-description {
        flex-grow: 1;
        margin-top: 10px;
        font-size: 1rem;
    }

    .box-button {
        align-self: flex-end;

    }
</style>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h4>All Products</h4>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn btn-primary  waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="box">
                            <div class="box-title">
                                <?php echo htmlspecialchars($product['product_name']); ?>
                            </div>
                            <div class="box-description">
                                <?php echo htmlspecialchars($product['product_description']); ?>
                            </div>
                            <!-- <div class="box-button">
                                <a href="checkout.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                    Add Service
                                </a>
                            </div> -->
                            <div class="box-button">
                                <!-- Button to trigger modal -->
                                <button class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#amcModal"
                                    onclick="openAMCModal(<?php echo $product['id']; ?>)">
                                    Add Service
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Bootstrap Modal for Register New AMC -->
<div class="modal fade" id="amcModal" tabindex="-1" aria-labelledby="amcModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="amcModalLabel">Register New AMC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="productId" name="product_id">
                    <div class="row">

                        <div class="col-lg-4 text-center">
                            <label class="form-label">Customer</label>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group">
                                <select name="customer_id" class="form-control js-example-basic-single" id="selectCustomer">
                                    <option value="0">--Select--</option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer['id']; ?>">
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
                                <input name="activation_date" class="form-control" type="date" id="activationDate" onchange="setDeactivationDate()">
                            </div>
                        </div>

                        <!-- Deactivation Date -->
                        <div class="col-lg-4 text-center">
                            <label class="form-label">Deactivation Date</label>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group">
                                <input name="deactivation_date" class="form-control" type="date" id="deactivationDate" readonly>
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
                                    <option value="Free">Free</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </div>
                        </div>

                        <!-- Service Charge -->
                        <div class="col-lg-4 text-center" id="serviceChargeContainer" style="display: none;">
                            <label class="form-label">Service Charge</label>
                        </div>
                        <div class="col-lg-8" id="serviceChargeInputContainer" style="display: none;">
                            <div class="form-group">
                                <input name="service_charge" type="number" class="form-control border" id="serviceCharge" placeholder="0.00" />
                            </div>
                        </div>

                        <div class="col-lg-4 text-center">
                            <label class="form-label">Service on</label>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group">
                                <select name="service_on" class="form-control js-example-basic-single" id="serviceOn" name="serviceOn" onchange="handleServiceTypeChange()">
                                    <option value="0">--Select--</option>
                                    <option value="Daily">Daily</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>

                        <!-- No of Service -->
                        <div class="col-lg-4 text-center">
                            <label class="form-label">No of Services</label>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group">
                                <input name="no_of_services" class="form-control" type="number" id="noOfService" min="0" placeholder="Enter Number of Service" oninput="generateServiceFields()">
                            </div>
                        </div>
                    </div>
                        <!-- Dynamic Fields Container -->
                        <div id="dynamicFieldsContainer" class="row ">

                        </div>
                    <div class="row">
                        <div class="col-lg-4 text-center">
                            <label class="form-label">Select Technician</label>
                        </div>
                        <div class="col-lg-8">
                            <div class="form-group">
                                <select name="technician_id" class="form-control js-example-basic-single" id='selectTech'>
                                    <option value="0">--Select--</option>
                                    <?php foreach ($tech_details as $tech): ?>
                                        <option value="<?php echo $tech['account_id']; ?>">
                                            <?php echo $tech['account_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function openAMCModal(productId) {
        console.log("Product ID:", productId); // For testing
        document.getElementById('productId').value = productId;
    }
</script>


<script>
    function toggleServiceCharge() {
        const serviceType = $('#serviceType').val();
        if (serviceType === 'Paid') {
            $('#serviceChargeContainer').show();
            $('#serviceChargeInputContainer').show();
            $('#serviceCharge').prop('required', true);
        } else {
            $('#serviceChargeContainer').hide();
            $('#serviceChargeInputContainer').hide();
            $('#serviceCharge').val(''); // clear the value if hidden
            $('#serviceCharge').prop('required', false);
        }
    }

    // Attach the function to the change event of the select element
    // $(document).ready(function() {
    //     $('#serviceType').on('change', toggleServiceCharge);
    // });
</script>
<script>
    function setDeactivationDate() {
        const activationDate = document.getElementById('activationDate').value;
        const deactivationDate = document.getElementById('deactivationDate');

        if (activationDate) {
            const date = new Date(activationDate);
            date.setFullYear(date.getFullYear() + 1); // Add one year to the activation date

            // Format the date to YYYY-MM-DD for the input field
            const formattedDate = date.toISOString().split('T')[0];
            deactivationDate.value = formattedDate;
        }
    }
</script>
<script>
    // Function to handle service type change
    function handleServiceTypeChange() {
        document.getElementById('dynamicFieldsContainer').innerHTML = ''; // Clear previous fields
        document.getElementById('noOfService').value = '';
    }

    function generateServiceFields() {
        const serviceOn = document.getElementById('serviceOn').value;
        const noOfService = parseInt(document.getElementById('noOfService').value);
        const container = document.getElementById('dynamicFieldsContainer');

        container.innerHTML = ''; // Clear previous fields

        if (!serviceOn || isNaN(noOfService) || noOfService <= 0) {
            return;
        }

        for (let i = 1; i <= noOfService; i++) {
            // Create wrapper div for label
            const labelDiv = document.createElement('div');
            labelDiv.className = 'col-lg-4 text-center';

            // Create label
            const label = document.createElement('label');
            label.className = 'form-label';
            label.textContent = `Service ${i}`;
            labelDiv.appendChild(label);

            // Create wrapper div for input/select
            const inputDiv = document.createElement('div');
            inputDiv.className = 'col-lg-8';

            // Create inner div with form-group class
            const formGroupDiv = document.createElement('div');
            formGroupDiv.className = 'form-group';

            // Add input/select based on service type
            if (serviceOn === 'Daily') {
                const timeInput = document.createElement('input');
                timeInput.type = 'time';
                timeInput.name = `dynamic_field[time_${i}]`;
                timeInput.className = 'form-control';
                timeInput.placeholder = `Select Time ${i}`;
                formGroupDiv.appendChild(timeInput);
            } else if (serviceOn === 'Weekly') {
                const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                const select = document.createElement('select');
                select.className = 'form-control js-example-basic-single';
                select.name = `dynamic_field[week_${i}]`;
                select.id = `dynamic_field_week_${i}`;

                daysOfWeek.forEach(day => {
                    const option = document.createElement('option');
                    option.value = day;
                    option.textContent = day;
                    select.appendChild(option);
                    $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                });
                formGroupDiv.appendChild(select);
            } else if (serviceOn === 'Monthly') {
                const select = document.createElement('select');
                select.className = 'form-control js-example-basic-single ';
                select.name = `dynamic_field[month_${i}]`;
                select.id = `dynamic_field_month_${i}`;

                for (let day = 1; day <= 30; day++) {
                    const option = document.createElement('option');
                    option.value = day;
                    option.textContent = `Day ${day}`;
                    select.appendChild(option);
                    $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                }
                formGroupDiv.appendChild(select);
                
            } else if (serviceOn === 'Yearly') {
                const dateInput = document.createElement('input');
                dateInput.type = 'date';
                dateInput.name = `dynamic_field[year_${i}]`;
                dateInput.className = 'form-control';
                formGroupDiv.appendChild(dateInput);
            }

            inputDiv.appendChild(formGroupDiv);

            // Append label and input divs to container
            container.appendChild(labelDiv);
            container.appendChild(inputDiv);
        }

          $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
    }
</script>