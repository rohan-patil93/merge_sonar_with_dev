<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['datepicker'] = '1';

$editMode = false;
$vehicleData = null;
$editId = '';

if (isset($_GET['Rid']) && !empty($_GET['Rid'])) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $id = $rid['1'];

    if ($id && is_numeric($id)) {
        $editId = $id;
        $url = API . "?mode=gettransportvehiclemaster&method=get&rid=" . $id . "&company_id=" . $_SESSION['company_id'];
        $response = callAPI($url);
        $data = json_decode($response, true);

        if (!empty($data) && isset($data['data']) && is_array($data['data'])) {
            $editMode = true;
            $vehicleData = $data['data'];
        }
    }
}

// Vehicle types
$vehicleTypes = [
    'demo' => 'Demo Car',
    'car' => 'Car',
    'truck' => 'Truck',
    'van' => 'Van',
    'motorcycle' => 'Motorcycle',
    'bus' => 'Bus',
    'trailer' => 'Trailer',
    'other' => 'Other'
];

// Fuel types
$fuelTypes = [
    'petrol' => 'Petrol',
    'diesel' => 'Diesel',
    'cng' => 'CNG',
    'lpg' => 'LPG',
    'electric' => 'Electric',
    'hybrid' => 'Hybrid'
];

// Transportation types
$transportationTypes = [
    '0' => 'Internal',
    '1' => 'External'
];

// Department types
$departmentTypes = [
    'sales' => 'Sales',
    'service' => 'Service',
    'admin' => 'Administration',
    'logistics' => 'Logistics',
    'marketing' => 'Marketing',
    'other' => 'Other'
];

// Hypothication status
$hypothicationStatuses = [
    'none' => 'None',
    'bank' => 'Bank Financed',
    'leased' => 'Leased',
    'owned' => 'Fully Owned'
];
?>

<div class="d-none transport-vehicle-master-php-data"
    data-company-id="<?php echo isset($_SESSION['company_id']) ? $_SESSION['company_id'] : '0'; ?>"
    data-user-id="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : '0'; ?>"
    data-edit-mode="<?php echo $editMode ? '1' : '0'; ?>"
    data-edit-id="<?php echo $editId; ?>"
    data-vehicle-data="<?php echo $editMode && $vehicleData ? htmlspecialchars(json_encode($vehicleData), ENT_QUOTES, 'UTF-8') : ''; ?>">
</div>

<!-- Include external CSS -->
<link rel="stylesheet" href="assets/css/custom_insert_transport_vehicle_master.css">

<div class="page-body">
    <div class="row">

        <div class="col-lg-12">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header table-card-header">
                    <h5><i class="ti-car"></i> <?php echo $editMode ? 'Edit Vehicle Information' : 'Add New Vehicle'; ?></h5>
                    <div class="card-header-right table-card-header">
                        <a href="index.php?option=custom_list_transport_vehicle_master" class="btn btn-info waves-effect waves-light mr-2">
                            <i class="ti-list"></i> <?= __('View All Vehicles'); ?>
                        </a>
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i> <?= __('Back'); ?>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="vehicleMasterForm">
                        <?php if ($editMode && $vehicleData): ?>
                            <input type="hidden" name="vehicle_id" value="<?php echo $vehicleData['id']; ?>">
                            <input type="hidden" name="edit_mode" value="1">
                        <?php endif; ?>


                        <h6 class="mb-3 text-primary"><i class="mx-1 ti-info-alt"></i> <?= __('Basic Vehicle Information'); ?></h6>
                        <div class="form-group row">
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Vehicle Registration No.'); ?> <span class="text-danger">*</span></span>
                                <input aria-label="<?= htmlspecialchars(__('e.g., ABC-1234'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control text-uppercase" name="vehicle_reg_no" id="vehicleRegNo"
                                    placeholder="<?= htmlspecialchars(__('e.g., ABC-1234'), ENT_QUOTES, 'UTF-8'); ?>" required
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['vehicle_reg_no']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Chassis No.'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter chassis number'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control text-uppercase" name="chassis_no" id="chassisNo"
                                    placeholder="<?= htmlspecialchars(__('Enter chassis number'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['chassis_no']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Engine No.'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter engine number'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control text-uppercase" name="engine_no" id="engineNo"
                                    placeholder="<?= htmlspecialchars(__('Enter engine number'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['engine_no']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Vehicle Type'); ?> <span class="text-danger">*</span></span>
                                <select aria-label="<?= htmlspecialchars(__('Vehicle Type'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" name="vehicle_type" id="vehicleType" required>
                                    <option value=""><?= __('Select Vehicle Type'); ?></option>
                                    <?php foreach ($vehicleTypes as $key => $type): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($editMode && $vehicleData && $vehicleData['vehicle_type'] == $key) ? 'selected' : ''; ?>>
                                            <?php echo $type; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Model'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('e.g., Toyota Camry'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="model" id="model"
                                    placeholder="<?= htmlspecialchars(__('e.g., Toyota Camry'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['model']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Model Group'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('e.g., Sedan'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="model_group" id="modelGroup"
                                    placeholder="<?= htmlspecialchars(__('e.g., Sedan'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['model_group']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-3">
                                <span class="form-label"><?= __('Colour'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('e.g., White'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="colour" id="colour"
                                    placeholder="<?= htmlspecialchars(__('e.g., White'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['colour']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-3">
                                <span class="form-label"><?= __('Fuel Type'); ?></span>
                                <select aria-label="<?= htmlspecialchars(__('Fuel Type'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" name="fuel_type" id="fuelType">
                                    <option value=""><?= __('Select Fuel Type'); ?></option>
                                    <?php foreach ($fuelTypes as $key => $type): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($editMode && $vehicleData && $vehicleData['fuel_type'] == $key) ? 'selected' : ''; ?>>
                                            <?php echo $type; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-3">
                                <span class="form-label"><?= __('KM Driven'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('e.g., 50000'), ENT_QUOTES, 'UTF-8'); ?>" type="number" class="form-control" name="km_driven" id="kmDriven"
                                    placeholder="<?= htmlspecialchars(__('e.g., 50000'), ENT_QUOTES, 'UTF-8'); ?>" min="0"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['km_driven']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-3">
                                <span class="form-label"><?= __('Average (km/l)'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('e.g., 15'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="average" id="average"
                                    placeholder="<?= htmlspecialchars(__('e.g., 15'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['average']) : ''; ?>">
                            </div>
                        </div>

                        <hr class="my-4">


                        <h6 class="mb-3 text-primary"><i class="mx-1 ti-location-pin"></i> <?= htmlspecialchars(__('Registration & Branch Information'), ENT_QUOTES, 'UTF-8'); ?></h6>
                        <div class="form-group row">
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Registered Branch'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter registered branch'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="registered_branch" id="registeredBranch"
                                    placeholder="<?= htmlspecialchars(__('Enter registered branch'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['registered_branch']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Registration Name'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Name on registration'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="registration_name" id="registrationName"
                                    placeholder="<?= htmlspecialchars(__('Name on registration'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['registration_name']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Responsible Person'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Person responsible for vehicle'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="responsible_person" id="responsiblePerson"
                                    placeholder="<?= htmlspecialchars(__('Person responsible for vehicle'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['responsible_person']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Transportation Type'); ?></span>
                                <select aria-label="<?= htmlspecialchars(__('Transportation Type'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" name="transportation_type" id="transportationType">
                                    <option value=""><?= __('Select Type'); ?></option>
                                    <?php foreach ($transportationTypes as $key => $type): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($editMode && $vehicleData && $vehicleData['transportation_type'] == $key) ? 'selected' : ''; ?>>
                                            <?php echo $type; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Department Type'); ?></span>
                                <select aria-label="<?= htmlspecialchars(__('Department Type'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" name="department_type" id="departmentType">
                                    <option value=""><?= __('Select Department'); ?></option>
                                    <?php foreach ($departmentTypes as $key => $type): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($editMode && $vehicleData && $vehicleData['department_type'] == $key) ? 'selected' : ''; ?>>
                                            <?php echo $type; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Hypothication Status'); ?></span>
                                <select aria-label="<?= htmlspecialchars(__('Hypothication Status'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single" name="hypothication_status" id="hypothicationStatus">
                                    <option value=""><?= __('Select Status'); ?></option>
                                    <?php foreach ($hypothicationStatuses as $key => $status): ?>
                                        <option value="<?php echo $key; ?>" <?php echo ($editMode && $vehicleData && $vehicleData['hypothication_status'] == $key) ? 'selected' : ''; ?>>
                                            <?php echo $status; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">


                        <h6 class="mb-3 text-primary"><i class="mx-1 ti-shield"></i> <?= htmlspecialchars(__('Insurance & Compliance'), ENT_QUOTES, 'UTF-8'); ?></h6>
                        <div class="form-group row">
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Insurance No.'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter insurance number'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="insurance_no" id="insuranceNo"
                                    placeholder="<?= htmlspecialchars(__('Enter insurance number'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['insurance_no']) : ''; ?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Insurance Expiry Date'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Insurance Exp Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" class="form-control" name="insurance_exp_date" id="insuranceExpDate"
                                    value="<?php echo $editMode && $vehicleData && $vehicleData['insurance_exp_date'] ? $vehicleData['insurance_exp_date'] : ''; ?>">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('PUC Issue Date'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Puc Issue Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" class="form-control" name="puc_issue_date" id="pucIssueDate"
                                    value="<?php echo $editMode && $vehicleData && $vehicleData['puc_issue_date'] ? $vehicleData['puc_issue_date'] : ''; ?>">
                            </div>
                        </div>

                        <hr class="my-4">


                        <h6 class="mb-3 text-primary"><i class="mx-1 ti-clipboard"></i> <?= __('Audit Information'); ?></h6>
                        <div class="form-group row">
                            <div class="form-group col-lg-4">
                                <span class="form-label"><?= __('Audit Date'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Audit Date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" class="form-control" name="audit_date" id="auditDate"
                                    value="<?php echo $editMode && $vehicleData && $vehicleData['audit_date'] ? $vehicleData['audit_date'] : ''; ?>">
                            </div>
                            <div class="form-group col-lg-8">
                                <span class="form-label"><?= __('Audit Remark'); ?></span>
                                <input aria-label="<?= htmlspecialchars(__('Enter audit remarks'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="audit_remark" id="auditRemark"
                                    placeholder="<?= htmlspecialchars(__('Enter audit remarks'), ENT_QUOTES, 'UTF-8'); ?>"
                                    value="<?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['audit_remark']) : ''; ?>">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Notes -->
                        <h6 class="mb-3 text-primary"><i class="mx-1 ti-notepad"></i><?= __('Notes'); ?></h6>

                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label"><?= __('Description'); ?></span>
                                <textarea aria-label="<?= htmlspecialchars(__('Enter vehicle description...'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" name="description" id="description" rows="3"
                                    placeholder="<?= htmlspecialchars(__('Enter vehicle description...'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['description']) : ''; ?></textarea>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="form-label"><?= __('Additional Notes'); ?></span>
                                <textarea aria-label="<?= htmlspecialchars(__('Any additional notes...'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" name="additional_notes" id="additionalNotes" rows="3"
                                    placeholder="<?= htmlspecialchars(__('Any additional notes...'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $editMode && $vehicleData ? htmlspecialchars($vehicleData['additional_notes']) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-4">
                            <button type="submit" id="submitBtn"
                                class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile">
                                <i class="la la-check"></i> <?php echo $editMode ? 'Update Vehicle' : 'Add Vehicle'; ?>
                            </button>
                            <button type="reset"
                                class="btn btn-secondary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile">
                                <i class="la la-refresh"></i> <?= __('Clear Form'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include external JavaScript -->
<script src="assets/js/custom_insert_transport_vehicle_master.js"></script>
