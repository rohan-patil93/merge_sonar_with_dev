<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['datepicker'] = '1';

$editMode = false;
$fuelData = null;
if(isset($_GET['Rid']) && !empty($_GET['Rid'])) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $id = $rid['1'];

    if($id && is_numeric($id)) {
        $url = API . "?mode=getfuelentrydata&method=get&rid=". $id . "&company_id=" . $_SESSION['company_id'];
        $response = callAPI($url);
        $data = json_decode($response, true);

        if($data && isset($data['fueldata']) && !empty($data['fueldata'])) {
            $editMode = true;
            $fuelData = $data['fueldata'][0]; // Get the first (and should be only) record
        }
    }
}

// Fuel types available
$fuelTypes = [
    'petrol' => 'Petrol',
    'diesel' => 'Diesel',
    'cng' => 'CNG',
    'lpg' => 'LPG',
    'electric' => 'Electric Charging',
    'other' => 'Other'
];

// Vehicle ownership types
$vehicleOwnership = [
    'company' => 'Company Vehicle',
    'private' => 'Private Vehicle',
    'rental' => 'Rental Vehicle'
];

// Payment methods
$paymentMethods = [
    'cash' => 'Cash',
    'card' => 'Card Payment',
    'fuel_card' => 'Fuel Card',
    'company_account' => 'Company Account',
    'advance' => 'Advance Payment'
];

// Get fuel statistics
$statistics = [
    'today_fuel_amount' => 0,
    'today_fuel_quantity' => 0,
    'monthly_fuel_cost' => 0,
    'total_vehicles_fueled' => 0
];
?>

<div class="page-body">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card bg-primary order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Fuel Cost</h6>
                    <h2 class="text-right text-white"><i class="ti-money f-left"></i> <span id="stat-today-amount">₹0</span></h2>
                    <p class="m-b-0 text-white">Total amount spent today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-success order-card">
                <div class="card-body">
                    <h6 class="text-white">Today's Fuel Quantity</h6>
                    <h2 class="text-right text-white"><i class="ti-dropbox f-left"></i> <span id="stat-today-quantity">0L</span></h2>
                    <p class="m-b-0 text-white">Total liters dispensed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-warning order-card">
                <div class="card-body">
                    <h6 class="text-white">Monthly Cost</h6>
                    <h2 class="text-right text-white"><i class="ti-stats-up f-left"></i> <span id="stat-monthly-cost">₹0</span></h2>
                    <p class="m-b-0 text-white">This month's total</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-info order-card">
                <div class="card-body">
                    <h6 class="text-white">Vehicles Fueled</h6>
                    <h2 class="text-right text-white"><i class="ti-car f-left"></i> <span id="stat-vehicles-count">0</span></h2>
                    <p class="m-b-0 text-white">Today's vehicle count</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-12">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-dropbox"></i> <?php echo $editMode ? 'Edit Fuel Distribution Entry' : 'Fuel Distribution Entry'; ?></h5>
                    <div class="card-header-right table-card-header">
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="fuelEntryForm">
                        <!-- Hidden field for edit mode -->
                        <?php if($editMode && $fuelData): ?>
                            <input type="hidden" name="fuel_id" value="<?php echo $fuelData['id']; ?>">
                            <input type="hidden" name="edit_mode" value="1">
                            <input type="hidden" id="original_department" value="<?php echo htmlspecialchars($fuelData['department']); ?>">
                            <input type="hidden" id="original_employee" value="<?php echo htmlspecialchars($fuelData['employee']); ?>">
                        <?php endif; ?>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Vehicle Number <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="e.g., ABC-1234" type="text" class="form-control text-uppercase" name="vehicle_number"
                                        id="vehicle_number" placeholder="e.g., ABC-1234"
                                        value="<?php echo $editMode && $fuelData ? htmlspecialchars($fuelData['vehicle_number']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Vehicle Ownership <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Vehicle Ownership" class="form-select js-example-basic-single" name="vehicle_ownership" id="vehicleOwnership" onchange="toggleDepartmentSection()" required>
                                        <option value="">Select Ownership Type</option>
                                        <?php foreach ($vehicleOwnership as $key => $type): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($editMode && $fuelData && $fuelData['vehicle_ownership'] == $key) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="form-group row" id="departmentRow" style="display: <?php echo ($editMode && $fuelData && $fuelData['vehicle_ownership'] == 'company') ? 'flex' : 'none'; ?>;">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Department <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Department" class="form-select js-example-basic-single" name="department" id="departmentSelect" onchange="loadEmployeesByDepartment()">
                                        <option value="">Select Department</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Employee <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Employee" class="form-select js-example-basic-single" name="employee" id="employeeSelect" onchange="fillEmployeeDetails()">
                                        <option value="">Select Employee</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Driver/Employee Name <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="Full name of driver/employee" type="text" class="form-control" name="driver_name"
                                        placeholder="Full name of driver/employee"
                                        value="<?php echo $editMode && $fuelData ? htmlspecialchars($fuelData['driver_name']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Contact Number</span>
                                <div>
                                    <input aria-label="Phone number" type="tel" class="form-control" name="contact_number"
                                        placeholder="Phone number"
                                        value="<?php echo $editMode && $fuelData ? htmlspecialchars($fuelData['contact_number']) : ''; ?>">
                                </div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Fuel Type <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Fuel Type" class="form-select js-example-basic-single" name="fuel_type"
                                        id="fuelType" onchange="toggleOtherFuelType()" required>
                                        <option value="">Select Fuel Type</option>
                                        <?php foreach ($fuelTypes as $key => $type): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($editMode && $fuelData && $fuelData['fuel_type'] == $key) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Fuel Quantity (Liters) <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="0.00" type="number" class="form-control" name="fuel_quantity" step="0.01" min="0"
                                        placeholder="0.00" onchange="calculateTotal()"
                                        value="<?php echo $editMode && $fuelData ? $fuelData['fuel_quantity'] : ''; ?>" required>
                                </div>
                            </div>
                        </div>


                        <div class="form-group row" id="otherFuelTypeRow" style="display: none;">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Specify Fuel Type <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="Please specify fuel type" type="text" class="form-control" name="other_fuel_type"
                                        id="otherFuelType" placeholder="Please specify fuel type"
                                        value="<?php echo $editMode && $fuelData ? htmlspecialchars($fuelData['other_fuel_type']) : ''; ?>">
                                </div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Price per Liter (₹) <span class="text-danger">*</span></span>
                                <div>
                                    <input aria-label="0.00" type="number" class="form-control" name="price_per_liter" step="0.01" min="0"
                                        placeholder="0.00" onchange="calculateTotal()"
                                        value="<?php echo $editMode && $fuelData ? $fuelData['price_per_liter'] : ''; ?>" required>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Total Amount (₹)</span>
                                <div>
                                    <input aria-label="0.00" type="number" class="form-control" name="total_amount" step="0.01" min="0"
                                        placeholder="0.00" value="<?php echo $editMode && $fuelData ? $fuelData['total_amount'] : ''; ?>" readonly>
                                </div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Payment Method <span class="text-danger">*</span></span>
                                <div>
                                    <select aria-label="Payment Method" class="form-select js-example-basic-single" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <?php foreach ($paymentMethods as $key => $method): ?>
                                            <option value="<?php echo $key; ?>" <?php echo ($editMode && $fuelData && $fuelData['payment_method'] == $key) ? 'selected' : ''; ?>><?php echo $method; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Fuel Date</span>
                                <div>
                                    <input aria-label="Fuel Date" type="date" class="form-control" name="fuel_date"
                                        value="<?php echo $editMode && $fuelData ? $fuelData['fuel_date'] : date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="form-group col-lg-12">
                                <span class="form-label">Additional Notes</span>
                                <div>
                                    <textarea aria-label="Any additional information or remarks..." class="form-control" name="notes" rows="3"
                                        placeholder="Any additional information or remarks..."><?php echo $editMode && $fuelData ? htmlspecialchars($fuelData['notes']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Checkboxes -->
                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input aria-label="Is Emergency" type="checkbox" name="is_emergency" value="1" <?php echo ($editMode && $fuelData && $fuelData['is_emergency'] == 1) ? 'checked' : ''; ?>>
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Emergency fuel requirement</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <div class="checkbox-fade fade-in-primary">
                                    <label>
                                        <input aria-label="Is Advance" type="checkbox" name="is_advance" value="1" <?php echo ($editMode && $fuelData && $fuelData['is_advance'] == 1) ? 'checked' : ''; ?>>
                                        <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                        <span>Advance fuel allocation</span>
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-12">
                            <button type="submit"
                                class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile">
                                <i class="la la-check"></i><?php echo $editMode ? 'Update Fuel Entry' : 'Record Fuel Entry'; ?>
                            </button>
                            <button type="reset"
                                class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile">
                                <i class="la la-refresh"></i>Clear Form
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Fuel Summary -->
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-time"></i> Today's Fuel Distribution Summary</h5>
                    <div class="card-header-right">
                        <a href="index.php?option=custom_list_transport_fuel" class="btn btn-sm btn-primary">
                            <i class="ti-eye"></i> View All Records
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="todayFuelSummaryTable">
                            <thead>
                                <tr>
                                    <th scope="col">Vehicle</th>
                                    <th scope="col">Driver</th>
                                    <th scope="col">Fuel Type</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center p-4">
                                        <i class="fa fa-spinner fa-spin text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">Loading today's summary...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Receipt Modal -->
<div class="modal fade" id="printReceiptModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="ti-printer"></i> Fuel Receipt</h4>
                <button type="button" class="close" onclick="closeReceiptModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="printReceiptContent">
                <!-- Receipt content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeReceiptModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()">
                    <i class="ti-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Receipt Modal Styling */
#printReceiptContent .fuel-receipt-container {
    border: 2px solid #000;
    padding: 15px;
    max-width: 400px;
    margin: 0 auto;
    font-family: Arial, sans-serif;
    font-size: 12px;
    background: white;
}

#printReceiptContent .receipt-header {
    text-align: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #000;
    padding-bottom: 10px;
}

#printReceiptContent .receipt-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

#printReceiptContent .receipt-content {
    margin: 10px 0;
}

#printReceiptContent .receipt-row {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
    font-size: 11px;
}

#printReceiptContent .receipt-row.total {
    border-top: 1px solid #000;
    padding-top: 5px;
    font-weight: bold;
    font-size: 12px;
}

#printReceiptContent .receipt-footer {
    text-align: center;
    margin-top: 15px;
    border-top: 1px solid #000;
    padding-top: 10px;
}

#printReceiptContent .receipt-footer small {
    font-size: 10px;
}
</style>

<script type="text/javascript">
    // Toggle other fuel type input
    function toggleOtherFuelType() {
        const fuelType = document.getElementById('fuelType').value;
        const otherRow = document.getElementById('otherFuelTypeRow');
        const otherInput = document.getElementById('otherFuelType');

        if (fuelType === 'other') {
            otherRow.style.display = 'block';
            otherInput.required = true;
        } else {
            otherRow.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    }

    // Calculate total amount
    function calculateTotal() {
        const quantity = parseFloat(document.querySelector('input[name="fuel_quantity"]').value) || 0;
        const pricePerLiter = parseFloat(document.querySelector('input[name="price_per_liter"]').value) || 0;
        const total = quantity * pricePerLiter;

        document.querySelector('input[name="total_amount"]').value = total.toFixed(2);
    }

    // Auto-uppercase vehicle number
    const vehicleNumberInput = document.querySelector('input[name="vehicle_number"]');
    vehicleNumberInput.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
    });

    // Toggle department section based on vehicle ownership
    function toggleDepartmentSection() {
        const ownership = document.getElementById('vehicleOwnership').value;
        const departmentRow = document.getElementById('departmentRow');
        const departmentSelect = document.getElementById('departmentSelect');
        const employeeSelect = document.getElementById('employeeSelect');

        if (ownership === 'company') {
            departmentRow.style.display = 'flex';
            departmentSelect.required = true;
            employeeSelect.required = true;
            loadDepartments();
        } else {
            departmentRow.style.display = 'none';
            departmentSelect.required = false;
            employeeSelect.required = false;
            // Clear selections
            departmentSelect.value = '';
            employeeSelect.value = '';
            if (typeof jQuery !== 'undefined') {
                $(departmentSelect).trigger('change');
                $(employeeSelect).trigger('change');
            }
        }
    }

    function loadDepartments() {
        if (typeof jQuery === 'undefined') {
            return;
        }

        $.post('ajax/employee_loan_ajax.php', {
            page: 'get_departments'
        }, function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    populateDepartmentSelect(result.data);
                } else {
                    console.error('Error loading departments:', result.message);
                }
            } catch (e) {
                console.error('Error parsing departments response:', e);
            }
        }).fail(function(xhr, status, error) {
            console.error('Network error loading departments:', error);
        });
    }

    function populateDepartmentSelect(departments) {
        const departmentSelect = document.getElementById('departmentSelect');
        departmentSelect.innerHTML = '<option value="">Select Department</option>';

        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.department_name;
            departmentSelect.appendChild(option);
        });

        if (typeof jQuery !== 'undefined') {
            $(departmentSelect).trigger('change');
        }
    }

    // Load employees by department
    function loadEmployeesByDepartment() {
        const departmentId = document.getElementById('departmentSelect').value;
        const employeeSelect = document.getElementById('employeeSelect');

        if (!departmentId) {
            employeeSelect.innerHTML = '<option value="">Select Employee</option>';
            if (typeof jQuery !== 'undefined') {
                $(employeeSelect).trigger('change');
            }
            return;
        }

        if (typeof jQuery === 'undefined') {
            return;
        }

        $.post('ajax/employee_loan_ajax.php', {
            page: 'get_employees_by_department',
            department_id: departmentId
        }, function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    populateEmployeeSelect(result.data);
                } else {
                    console.error('Error loading employees:', result.message);
                    employeeSelect.innerHTML = '<option value="">No employees found</option>';
                }
            } catch (e) {
                console.error('Error parsing employees response:', e);
                employeeSelect.innerHTML = '<option value="">Error loading employees</option>';
            }
        }).fail(function(xhr, status, error) {
            console.error('Network error loading employees:', error);
            employeeSelect.innerHTML = '<option value="">Network error</option>';
        });
    }

    function populateEmployeeSelect(employees) {
        const employeeSelect = document.getElementById('employeeSelect');
        employeeSelect.innerHTML = '<option value="">Select Employee</option>';

        employees.forEach(emp => {
            const option = document.createElement('option');
            option.value = emp.id;
            option.textContent = emp.display_name || `${emp.first_name} ${emp.last_name}`.trim();
            option.dataset.phone = emp.phone || '';
            employeeSelect.appendChild(option);
        });

        if (typeof jQuery !== 'undefined') {
            $(employeeSelect).trigger('change');
        }
    }

    // Fill employee details when employee is selected
    function fillEmployeeDetails() {
        const employeeSelect = document.getElementById('employeeSelect');
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];

        if (selectedOption && selectedOption.value) {
            // Fill driver name
            const driverNameInput = document.querySelector('input[name="driver_name"]');
            driverNameInput.value = selectedOption.textContent;

            const contactInput = document.querySelector('input[name="contact_number"]');
            if (selectedOption.dataset.phone) {
                contactInput.value = selectedOption.dataset.phone;
            }
        }
    }

    function closeReceiptModal() {
        if (typeof jQuery !== 'undefined') {
            $('#printReceiptModal').modal('hide');
        } else {
            const modal = document.getElementById('printReceiptModal');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }
        }
    }

    function printReceipt() {
        const printContent = document.getElementById('printReceiptContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html lang="en">
                <head>
                    <title>Fuel Receipt</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 10px;
                            font-size: 12px;
                        }
                        .fuel-receipt-container {
                            border: 2px solid #000;
                            padding: 15px;
                            max-width: 400px;
                            margin: 0 auto;
                            background: white;
                        }
                        .receipt-header {
                            text-align: center;
                            margin-bottom: 15px;
                            border-bottom: 1px solid #000;
                            padding-bottom: 10px;
                        }
                        .receipt-header h4 {
                            margin: 0;
                            font-size: 16px;
                            font-weight: bold;
                        }
                        .receipt-content {
                            margin: 10px 0;
                        }
                        .receipt-row {
                            display: flex;
                            justify-content: space-between;
                            margin: 5px 0;
                            font-size: 11px;
                        }
                        .receipt-row.total {
                            border-top: 1px solid #000;
                            padding-top: 5px;
                            font-weight: bold;
                            font-size: 12px;
                        }
                        .receipt-footer {
                            text-align: center;
                            margin-top: 15px;
                            border-top: 1px solid #000;
                            padding-top: 10px;
                        }
                        .receipt-footer small {
                            font-size: 10px;
                        }
                        @media print {
                            body { margin: 0; }
                            .fuel-receipt-container { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }

    function showPrintReceipt(fuelData) {
        const companyName = '<?php echo isset($_SESSION["config"]["name"]) ? $_SESSION["config"]["name"] : "Company Name"; ?>';

        const receiptContent = `
            <div class="fuel-receipt-container">
                <div class="receipt-header">
                    <h4>${companyName}</h4>
                    <p>Fuel Distribution Receipt</p>
                </div>

                <div class="receipt-content">
                    <div class="receipt-row">
                        <span><strong>Date:</strong></span>
                        <span>${fuelData.fuel_date}</span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Time:</strong></span>
                        <span>${fuelData.fuel_time}</span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Vehicle Number:</strong></span>
                        <span>${fuelData.vehicle_number}</span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Driver Name:</strong></span>
                        <span>${fuelData.driver_name}</span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Contact:</strong></span>
                        <span>${fuelData.contact_number || 'N/A'}</span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Fuel Type:</strong></span>
                        <span>${fuelData.fuel_type_display || fuelData.fuel_type}</span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Quantity:</strong></span>
                        <span>${fuelData.fuel_quantity} Liters</span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Price per Liter:</strong></span>
                        <span>₹${fuelData.price_per_liter}</span>
                    </div>
                    <div class="receipt-row total">
                        <span><strong>Total Amount:</strong></span>
                        <span><strong>₹${fuelData.total_amount}</strong></span>
                    </div>
                    <div class="receipt-row">
                        <span><strong>Payment Method:</strong></span>
                        <span>${fuelData.payment_method_display || fuelData.payment_method}</span>
                    </div>

                </div>

                <div class="receipt-footer">
                    <small>Fuel Distribution Receipt</small><br>
                    <small>Generated on ${new Date().toLocaleString()}</small>
                </div>
            </div>
        `;

        document.getElementById('printReceiptContent').innerHTML = receiptContent;
        $('#printReceiptModal').modal('show');
    }

    // Load fuel data on page load
    function loadFuelData() {
        loadFuelStatistics();
        loadTodayFuelSummary();
    }

    function loadFuelStatistics() {
        if (typeof jQuery === 'undefined') {
            return;
        }
        $.post('ajax/employee_loan_ajax.php', {page: 'get_fuel_statistics'}, function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    updateStatisticsCards(result.data);
                }
            } catch (e) {
                // Silent error handling
            }
        }).fail(function(xhr, status, error) {
            // Silent error handling
        });
    }

    function loadTodayFuelSummary() {
        if (typeof jQuery === 'undefined') {
            return;
        }
        $.post('ajax/employee_loan_ajax.php', {page: 'get_today_fuel_summary'}, function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    updateTodayFuelSummaryTable(result.data);
                } else {
                    showErrorInTable('#todayFuelSummaryTable', 'Error loading today\'s summary');
                }
            } catch (e) {
                showErrorInTable('#todayFuelSummaryTable', 'Error loading today\'s summary');
            }
        }).fail(function(xhr, status, error) {
            showErrorInTable('#todayFuelSummaryTable', 'Network error loading summary');
        });
    }

    function showErrorInTable(tableSelector, message) {
        const tbody = document.querySelector(tableSelector + ' tbody');
        if (tbody) {
            const colCount = tbody.closest('table').querySelectorAll('thead th').length || 2;
            tbody.innerHTML = `
                <tr>
                    <td colspan="${colCount}" class="text-center p-4">
                        <i class="ti-alert text-danger" style="font-size: 2rem;"></i>
                        <p class="text-danger mt-2">${message}</p>
                    </td>
                </tr>
            `;
        }
    }

    function updateStatisticsCards(stats) {
        const todayAmountEl = document.getElementById('stat-today-amount');
        const todayQuantityEl = document.getElementById('stat-today-quantity');
        const monthlyCostEl = document.getElementById('stat-monthly-cost');
        const vehiclesCountEl = document.getElementById('stat-vehicles-count');

        if (todayAmountEl) todayAmountEl.textContent = '₹' + (stats.today_fuel_amount || 0);
        if (todayQuantityEl) todayQuantityEl.textContent = (stats.today_fuel_quantity || 0) + 'L';
        if (monthlyCostEl) monthlyCostEl.textContent = '₹' + (stats.monthly_fuel_cost || 0);
        if (vehiclesCountEl) vehiclesCountEl.textContent = stats.total_vehicles_fueled || 0;
    }

    function updateEntriesCountBadge(count) {
        const badge = document.getElementById('entries-count-badge');
        if (badge) {
            badge.textContent = count + ' entr' + (count !== 1 ? 'ies' : 'y');
        }
    }

    function updateRecentFuelTable(entries) {
        const tbody = document.querySelector('#recentFuelTable tbody');
        if (!tbody) return;

        if (entries.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="2">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30">
                                <i class="ti-alert text-warning"></i> No Recent Entries
                            </p>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            tbody.innerHTML = entries.slice(0, 5).map(entry => `
                <tr>
                    <td>
                        <div class="d-inline-block align-middle">
                            <div class="d-inline-block">
                                <h6 class="m-b-0">${entry.vehicle_number}</h6>
                                <p class="m-b-0 text-muted">
                                    ${entry.fuel_type} • ${entry.fuel_quantity}L
                                </p>
                                <p class="m-b-0 text-muted">
                                    <small>${entry.driver_name}</small>
                                </p>
                                <p class="m-b-0 text-success">
                                    <small><strong>₹${entry.total_amount}</strong></small>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <button type="button" class="btn btn-sm btn-info btn-round"
                            onclick="printFuelReceipt(${entry.id})" title="Print Receipt">
                            <i class="ti-printer"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    }

    function updateTodayFuelSummaryTable(entries) {
        const tbody = document.querySelector('#todayFuelSummaryTable tbody');
        if (!tbody) return;

        if (entries.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30">
                                <i class="ti-alert text-warning"></i> No Data Available
                            </p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            tbody.innerHTML = entries.map(entry => `
                <tr>
                    <td>
                        <strong>${entry.vehicle_number}</strong><br>
                        <small class="text-muted">${entry.vehicle_ownership}</small>
                    </td>
                    <td>
                        ${entry.driver_name}<br>
                        <small class="text-muted">${entry.contact_number || 'N/A'}</small>
                    </td>
                    <td>
                        <span class="badge badge-info">${entry.fuel_type}</span>
                    </td>
                    <td>
                        <strong>${entry.fuel_quantity}L</strong><br>
                        <small class="text-muted">₹${entry.price_per_liter}/L</small>
                    </td>
                    <td>
                        <strong class="text-success">₹${entry.total_amount}</strong><br>
                        <small class="text-muted">${entry.payment_method}</small>
                    </td>
                    <td>
                        <small>${entry.fuel_date}</small>
                    </td>
                </tr>
            `).join('');
        }
    }

    function printFuelReceipt(fuelId) {
        if (typeof jQuery === 'undefined') {
            alert('System not ready. Please wait a moment and try again.');
            return;
        }

        $.post('ajax/employee_loan_ajax.php', {page: 'get_fuel_details', fuel_id: fuelId}, function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    showPrintReceipt(result.data);
                } else {
                    alert('Error loading fuel details for printing: ' + (result.message || 'Unknown error'));
                }
            } catch (e) {
                alert('Error processing fuel details.');
            }
        }).fail(function(xhr, status, error) {
            alert('Network error loading fuel details.');
        });
    }

    document.getElementById('fuelEntryForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const vehicleNumber = document.querySelector('input[name="vehicle_number"]').value.trim();
        const vehicleOwnership = document.querySelector('select[name="vehicle_ownership"]').value;
        const driverName = document.querySelector('input[name="driver_name"]').value.trim();
        const fuelType = document.querySelector('select[name="fuel_type"]').value;
        const fuelQuantity = parseFloat(document.querySelector('input[name="fuel_quantity"]').value);
        const pricePerLiter = parseFloat(document.querySelector('input[name="price_per_liter"]').value);
        const paymentMethod = document.querySelector('select[name="payment_method"]').value;

        // Check required fields
        if (!vehicleNumber || !vehicleOwnership || !driverName || !fuelType || !fuelQuantity || !pricePerLiter || !paymentMethod) {
            alert('Please fill in all required fields.');
            return false;
        }

        if (vehicleOwnership === 'company') {
            const departmentId = document.getElementById('departmentSelect').value;
            const employeeId = document.getElementById('employeeSelect').value;

            if (!departmentId) {
                alert('Please select a department for company vehicles.');
                return false;
            }

            if (!employeeId) {
                alert('Please select an employee for company vehicles.');
                return false;
            }
        }

        if (fuelType === 'other') {
            const otherFuelType = document.getElementById('otherFuelType').value.trim();
            if (!otherFuelType) {
                alert('Please specify the fuel type.');
                document.getElementById('otherFuelType').focus();
                return false;
            }
        }

        // Validate fuel quantity
        if (fuelQuantity <= 0 || fuelQuantity > 1000) {
            alert('Please enter a valid fuel quantity (0.01 to 1000 liters).');
            return false;
        }

        // Validate price per liter
        if (pricePerLiter <= 0 || pricePerLiter > 500) {
            alert('Please enter a valid price per liter (₹0.01 to ₹500).');
            return false;
        }

        // Basic vehicle number format validation
        const vehiclePattern = /^[A-Z0-9\-\s]+$/;
        if (!vehiclePattern.test(vehicleNumber)) {
            alert('Please enter a valid vehicle number format.');
            return false;
        }

        // Driver name validation
        if (driverName.length < 2) {
            alert('Please enter a valid driver name (at least 2 characters).');
            return false;
        }

        const contactNumber = document.querySelector('input[name="contact_number"]').value.trim();
        if (contactNumber && !/^[\+]?[0-9\-\s\(\)]{10,15}$/.test(contactNumber)) {
            alert('Please enter a valid phone number.');
            return false;
        }

        // Submit via AJAX
        submitFuelEntry();
    });

    function submitFuelEntry() {
        if (typeof jQuery === 'undefined') {
            alert('System not ready. Please wait a moment and try again.');
            return;
        }

        const form = document.getElementById('fuelEntryForm');
        const formData = new FormData(form);

        const isEditMode = form.querySelector('input[name="edit_mode"]');
        if (isEditMode) {
            formData.append('page', 'update_fuel_entry');
        } else {
            formData.append('page', 'insert_fuel_entry');
        }

        if (typeof company_id !== 'undefined') {
            formData.append('company_id', company_id);
        }
        if (typeof user_id !== 'undefined') {
            formData.append('user_id', user_id);
        }

        const vehicleOwnership = document.getElementById('vehicleOwnership').value;
        if (vehicleOwnership === 'company') {
            const departmentId = document.getElementById('departmentSelect').value;
            const employeeId = document.getElementById('employeeSelect').value;
            const departmentName = document.getElementById('departmentSelect').options[document.getElementById('departmentSelect').selectedIndex].text;

            formData.append('department_id', departmentId);
            formData.append('employee_id', employeeId);
            formData.append('department', departmentName);
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Recording...';
        submitBtn.disabled = true;

        $.post('ajax/employee_loan_ajax.php', Object.fromEntries(formData), function(response) {
            try {
                const result = JSON.parse(response);

                if (result.status === 'success' || result.status === 'sucess') {
                    const isUpdate = form.querySelector('input[name="edit_mode"]');
                    const message = isUpdate ? 'Fuel entry updated successfully!' : 'Fuel entry recorded successfully!';

                    tostarnotify(
                        'top', 'right',
                        'fa fa-thumbs-up',
                        'form-label label label-success',
                        'animated flipInX',
                        'animated flipOutX',
                        message,
                        ''
                    );




                    if (!isUpdate) {
                        form.reset();
                    } else {
                        window.location.href = 'index.php?option=custom_list_transport_fuel';
                    }

                    // Refresh all data after successful operation
                    loadFuelData();
                } else {
                    tostarnotify(
                        'top', 'right',
                        'fa fa-exclamation-triangle',
                        'form-label label label-danger',
                        'animated flipInX',
                        'animated flipOutX',
                        result.message || 'Error recording fuel entry. Please try again.',
                        ''
                    );
                }
            } catch (e) {
                tostarnotify(
                    'top', 'right',
                    'fa fa-exclamation-triangle',
                    'form-label label label-danger',
                    'animated flipInX',
                    'animated flipOutX',
                    'Error processing response. Please try again.',
                    ''
                );
            }
        }).fail(function(xhr, status, error) {
            tostarnotify(
                'top', 'right',
                'fa fa-exclamation-triangle',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Network error. Please try again.',
                ''
            );
        }).always(function() {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    // Auto-refresh data every 3 minutes
    setInterval(function () {
        if (typeof jQuery !== 'undefined' && !$('.modal').hasClass('show')) {
            loadFuelData();
        }
    }, 180000);

    function waitForjQueryAndInitialize() {
        if (typeof jQuery !== 'undefined') {
            $(document).ready(function () {
                $('.js-example-basic-single').select2();

                // Load all data on page load
                loadFuelData();

                populateEditData();

                $('[data-toggle="tooltip"]').tooltip();
            });
        } else {
            setTimeout(waitForjQueryAndInitialize, 100);
        }
    }

    const company_id = <?php echo isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '0'; ?>;
    const user_id = <?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : '0'; ?>;

    // Edit mode data
    const editMode = <?php echo $editMode ? 'true' : 'false'; ?>;
    const fuelData = <?php echo $editMode && $fuelData ? json_encode($fuelData) : 'null'; ?>;

    function populateEditData() {
        if (editMode && fuelData) {
            if (fuelData.vehicle_ownership === 'company') {
                const departmentRow = document.getElementById('departmentRow');
                if (departmentRow) {
                    departmentRow.style.display = 'flex';

                    // Load departments first, then set values
                    loadDepartments();

                    setTimeout(function() {
                        const originalDept = document.getElementById('original_department');
                        const originalEmp = document.getElementById('original_employee');

                        if (originalDept && originalDept.value) {
                            const departmentSelect = document.getElementById('departmentSelect');
                            for (let i = 0; i < departmentSelect.options.length; i++) {
                                if (departmentSelect.options[i].text === originalDept.value) {
                                    departmentSelect.selectedIndex = i;
                                    if (typeof jQuery !== 'undefined') {
                                        $(departmentSelect).trigger('change');
                                    }
                                    loadEmployeesByDepartment();

                                    // Set employee after employees are loaded
                                    setTimeout(function() {
                                        if (originalEmp && originalEmp.value) {
                                            const employeeSelect = document.getElementById('employeeSelect');
                                            employeeSelect.value = originalEmp.value;
                                            if (typeof jQuery !== 'undefined') {
                                                $(employeeSelect).trigger('change');
                                            }
                                        }
                                    }, 1000);
                                    break;
                                }
                            }
                        }
                    }, 1000);
                }
            }

            if (fuelData.fuel_type === 'other') {
                setTimeout(function() {
                    toggleOtherFuelType();
                }, 100);
            }
        }
    }

    waitForjQueryAndInitialize();
</script>
