<?php
session_start();
$_SESSION['apply_js']['table'] = 1;
$_SESSION['apply_js']['multiselest'] = 1;

$userData = json_decode($storedUserData, true);
$company_id = $_SESSION['company_id'];
$data_name = $_SESSION['data_name'];
$user_id = $_SESSION['userid'];
$module_id = $_SESSION['work_module_setup_id'];

// $contract_id = $_GET['id'] ?? null;
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$contract_id = $rid['1'];
//$contract_id = 87;
$is_edit_mode = !empty($contract_id);

$formData = [
    'contract_name' => '',
    'transaction_no' => '',
    'invoice_number' => '',
    'rt_number' => '',
    'parent_contract_id' => '',
    'party_type' => '',
    'contract_owner_name' => '',
    'contract_owner_email' => '',
    'contract_owner_number' => '',
    'party_id' => '',
    'start_date' => '',
    'end_date' => '',
    'description' => '',
    'additional_notes' => '',
    'payment_term_id' => '',
    'termination_condition' => '',
    'contract_status' => 'Pending',
    'auto_renew' => false,
    'renew_type' => '',
    'state' => '',
    'isp' => '',
    'paid_by' => '',
    'internet_charges' => '',
    'hub_spoc_name' => '',
    'hub_spoc_number' => '',
    'wifi_provider'=>'',
    'wifi_pass' => '',
    'id' => ''
];

$customers = [];
$contracts = [];
$users = [];
$documents = [];
$documentDetails = [];
$permissionDetails = [];
$workflow = [];
$activityDetails = [];
$associatedContracts = [];
$rechargeHistory = [];
$originator = null;
$notification = ['message' => '', 'type' => ''];

$currentDate = new DateTime();
$versionId = 'V_' . $currentDate->format('Ymd_His');

//echo "<pre>"; print_r($_POST); echo "</pre>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'submit_contract':
                handleContractSubmission();
                break;
            case 'submit_documents':
                handleDocumentSubmission();
                break;
            case 'submit_permissions':
                handlePermissionSubmission();
                break;
            case 'submit_workflow':
                handleWorkflowSubmission();
                break;
        }
    }
}

$url = API . "?mode=getcontractdetailsreact&method=get&rid={$contract_id}";
$response = callAPI($url);
$data = json_decode($response, true);

$contracts = $data['contractsres'] ?? [];
$users = $data['useres'] ?? [];
$documents = $data['documentres'] ?? [];
$associatedData = $data['associatedcontractres'] ?? [];
$editcontractsres = $data['editcontractsres'] ?? [];
$editdocumnentres = $data['editdocumnentres'] ?? [];
$editpermissionres = $data['editpermissionres'] ?? [];
$editworkflowres = $data['editworkflowres'] ?? [];
$activityres = $data['activityres'] ?? [];

if ($is_edit_mode) {
    if (!empty($associatedData) && is_array($associatedData)) {
        $associatedContracts = $associatedData;
    } else {
        $associatedContracts = [];
    }
    loadContractDataFallback($contract_id, $data);
}


function loadContractDataFallback($id, $data)
{
    global $formData, $documentDetails, $permissionDetails, $workflow;

    if (!empty($data['editcontractsres'])) {
        $formData = array_merge($formData, $data['editcontractsres']);
        $originator = $data['editcontractsres']['originator'] ?? null;
    }

    if (!empty($data['editdocumnentres']) && is_array($data['editdocumnentres'])) {
        $documentDetails = $data['editdocumnentres'];
    }

    if (!empty($data['editpermissionres']) && is_array($data['editpermissionres'])) {
        $permissionDetails = $data['editpermissionres'];
    }

    if (!empty($data['editworkflowres']) && is_array($data['editworkflowres'])) {
        $workflow = $data['editworkflowres'];
    }

    loadActivityDetailsFallback($id, $data);
}

function loadParties($partyType)
{
    global $customers;
    global $company_id, $data_name, $user_id, $module_id;

    if (empty($partyType)) {
        $customers = [];
        return;
    }

    $mode = $partyType === "vendor" ? "getvendorslist" : "getcustomerlist";
    $url = API . "?mode={$mode}&method=get";
    $response = callAPI($url);
    $data = json_decode($response, true);

    if ($data['result']) {
        $customers = $data['result'];
    } else {
        $customers = [];
    }
}

function handleContractSubmission()
{
    global $formData, $permissionDetails, $workflow, $documentDetails, $versionId, $contract_id;
    global $company_id, $data_name, $user_id, $module_id, $notification;
 
    $startDate = new DateTime($_POST['start_date']);
    $endDate = new DateTime($_POST['end_date']);
 
    if ($startDate > $endDate) {
        $notification = ['message' => 'Effective Date cannot be after Expiration Date.', 'type' => 'danger'];
        return;
    }
    $sanitizedFormData = [
        'contract_name' => $_POST['contract_name'] ?? '',
        'transaction_no' => $_POST['transaction_no'] ?? '',
        'invoice_number' => $_POST['invoice_number'] ?? '',
        'rt_number' => $_POST['rt_number'] ?? '',
        'parent_contract_id' => $_POST['parent_contract_id'] ?? '',
        'party_type' => $_POST['party_type'] ?? '',
        'contract_owner_name' => $_POST['contract_owner_name'] ?? '',
        'contract_owner_email' => $_POST['contract_owner_email'] ?? '',
        'contract_owner_number' => $_POST['contract_owner_number'] ?? '',
        'party_id' => $_POST['party_id'] ?? '',
        'start_date' => $_POST['start_date'] ?? '',
        'end_date' => $_POST['end_date'] ?? '',
        'description' => $_POST['description'] ?? '',
        'additional_notes' => $_POST['additional_notes'] ?? '',
        'payment_term_id' => $_POST['payment_term_id'] ?? '',
        'termination_condition' => $_POST['termination_condition'] ?? '',
        'contract_status' => $_POST['contract_status'] ?? 'Pending',
        'auto_renew' => isset($_POST['auto_renew']) ? true : false,
        'renew_type' => $_POST['renew_type'] ?? '',
        'state' => $_POST['state'] ?? '',
        'isp' => $_POST['isp'] ?? '',
        'paid_by' => $_POST['paid_by'] ?? '',
        'internet_charges' => $_POST['internet_charges'] ?? '',
        'hub_spoc_name' => $_POST['hub_spoc_name'] ?? '',
        'hub_spoc_number' => $_POST['hub_spoc_number'] ?? '',
        'wifi_provider' => $_POST['wifi_provider'] ?? '',
        'wifi_pass' => $_POST['wifi_pass'] ?? '',
        'Rid' => $contract_id ?? ''
    ];
    $sanitizedFormData['permission_data'] = $permissionDetails;
    $sanitizedFormData['workflow_data'] = $workflow;
    $sanitizedFormData['document_data'] = $documentDetails;
    $sanitizedFormData['contract_data'] = $formData;
    $sanitizedFormData['version_id'] = $versionId;
 
    try {
        $contractUrl = API . "?mode=insertcontractsreact&method=post";
        $response = file_post_contents($contractUrl, $sanitizedFormData, 'insert');
        $result = json_decode($response, true);
 
        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
 
        if (!empty($result['contract_result']['LASTID'])) {
            $contract_id = $result['contract_result']['LASTID'];
        }
 
        $notification = ['message' => 'Data submitted successfully!', 'type' => 'success'];
 
    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while submitting!', 'type' => 'danger'];
    }
}
 

function handleDocumentSubmission()
{
    global $documentDetails, $permissionDetails, $workflow, $formData, $versionId, $contract_id;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    $allowedTypes = ['pdf', 'xls', 'xlsx'];
    $maxSize = 10 * 1024 * 1024;
    $uploadDir = "./uploads/contracts/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $documentsForJSON = [];

    foreach ($_FILES['documents']['name'] as $index => $fileName) {
        if (empty($fileName))
            continue;

        // Validate file type
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedTypes)) {
            $notification = ['message' => 'Only PDF or Excel files allowed!', 'type' => 'danger'];
            return;
        }

        // Validate size
        if ($_FILES['documents']['size'][$index] > $maxSize) {
            $notification = ['message' => 'Each file must be 10 MB or smaller!', 'type' => 'danger'];
            return;
        }

        // Move file before API
        $newFileName = time() . "_" . basename($fileName);
        $filePath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['documents']['tmp_name'][$index], $filePath)) {
            $documentsForJSON[] = [
                'document_list_id' => $_POST['document_list_ids'][$index] ?? '',
                'comments' => $_POST['document_comments'][$index] ?? $fileName,
                'Rid' => $_POST['document_rids'][$index] ?? '',
                'file_name' => $newFileName,   // only send clean file name
                'file_path' => $filePath       // optional: if you want full path
            ];
        } else {
            $notification = ['message' => "Failed to move file: $fileName", 'type' => 'danger'];
            return;
        }
    }

    // prepare post data for API
    $postData = [
        'user_id' => $user_id,
        'contract_id' => $contract_id,
        'category_id' => 5,
        'company_id' => $company_id,
        'module_id' => $module_id,
        'platform' => 'php',
        'documents_meta' => json_encode($documentsForJSON)
    ];

    // Call API with details only (no CURLFile anymore)
    $docUrl = API . "?mode=insertdocumnentreact&method=post";
    $response = file_post_contents($docUrl, $postData, 'insert');

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $notification = ['message' => 'JSON Decode Error: ' . json_last_error_msg(), 'type' => 'danger'];
        return;
    }

    $notification = ['message' => 'Documents uploaded successfully!', 'type' => 'success'];
}
function handlePermissionSubmission()
{
    global $permissionDetails, $workflow, $documentDetails, $formData, $versionId, $contract_id;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    // ---- VALIDATION ----
    if (empty($_POST['selected_user'])) {
        $notification = ['message' => 'Please select a user for permissions!', 'type' => 'danger'];
        return;
    }

    if (empty($_POST['permissions']) || !is_array($_POST['permissions'])) {
        $notification = ['message' => 'Please select at least one permission!', 'type' => 'danger'];
        return;
    }

    try {
        // ---- SINGLE API CALL PAYLOAD ----
        $submissionData = [
            'user' => $_POST['selected_user'],
            'contract_id' => $contract_id,
            'Rid' => $_POST['prid'] ?? '',
            'permissions' => $_POST['permissions'],
            'document_permissions' => $_POST['document_permissions'] ?? [],
            'company_id' => $company_id,
            'module_id' => $module_id,
            'user_id' => $user_id,

            // extra data for control version + activity
            'permission_data' => $permissionDetails,
            'workflow_data' => $workflow,
            'document_data' => $documentDetails,
            'contract_data' => $formData,
            'version_id' => $versionId,
            'acitivity_data' => 'permission'
        ];

        // ---- CALL SINGLE API ----
        $permUrl = API . "?mode=insertpermissionreact&method=post";
        $response = file_post_contents($permUrl, $submissionData, 'insert');
        $result = json_decode($response, true);

        $notification = ['message' => 'Permissions updated successfully!', 'type' => 'success'];

    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while updating permissions: ' . $error->getMessage(), 'type' => 'danger'];
    }
}

function handleWorkflowSubmission()
{
    global $permissionDetails, $workflow, $documentDetails, $formData, $versionId, $contract_id;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    $workflowSteps = $_POST['workflow_steps'] ?? null;
    $stepsPermissions = $_POST['steps_permissions'] ?? null;

    if (empty($workflowSteps) || empty($stepsPermissions)) {
        $notification = ['message' => 'Please configure workflow steps and permissions!', 'type' => 'danger'];
        return;
    }

    try {
        $submissionData = [
            'contract_id' => $contract_id,
            'Rid' => $_POST['Rid'] ?? '',
            'workflow_steps' => $workflowSteps,
            'steps_permissions' => $stepsPermissions,
            'permission_data' => $permissionDetails,
            'workflow_data' => $workflow,
            'document_data' => $documentDetails,
            'contract_data' => $formData,
            'version_id' => $versionId,
            'company_id' => $company_id,
            'module_id' => $module_id,
            'user_id' => $user_id
        ];

        $workflowUrl = API . "?mode=insertworkflowreact&method=post";
        $response = file_post_contents($workflowUrl, $submissionData, 'insert');
        $result = json_decode($response, true);

        $notification = ['message' => 'Workflow updated successfully!', 'type' => 'success'];

    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while updating workflow: ' . $error->getMessage(), 'type' => 'danger'];
    }
}


function loadActivityDetailsFallback($id, $data = [])
{
    global $activityDetails;

    if (!empty($data['activityres']) && is_array($data['activityres'])) {
        $activityDetails = $data['activityres'];
    }
}

if (!empty($_GET['party_type']) || !empty($formData['party_type'])) {
    $partyType = $_GET['party_type'] ?? $formData['party_type'];
    if (empty($customers)) {
        loadParties($partyType);
    }
}

$admin_type = $_SESSION['admin_type'];
$userPermissions = [];
$userDocPermissions = [];
if ($admin_type == '1'){
    $userPermissions = ['edit', 'delete', 'document', 'workflows', 'activity', 'associated', 'permission'];
    $userDocPermissions = array_column($documents, 'id'); // all doc IDs
}else{
    foreach ($permissionDetails as $permDetail) {
        if ($permDetail['user'] == $user_id) {
            $userPermissions = json_decode($permDetail['basic_permissions'], true) ?? [];
            $userDocPermissions = !empty($permDetail['document_permissions'])? json_decode($permDetail['document_permissions'], true):[];
        break;
        }
    }
}
?>
<div class="card">
    <div class="card-header">
          <h5>Contracts</h5>
          <div class="card-header-right table-card-header">
             <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>   
          </div>
    </div>

    <div class="card-block">
        <div class="row">
            <div class="col-lg-10">
                <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="pill" href="#contractdetails">
                            Contract Details
                        </a>
                    </li>
                    <?php if ($is_edit_mode): ?>
                        <?php if (in_array('document', $userPermissions)): ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#documents">Documents</a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('workflows', $userPermissions)): ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#workflow">Workflow</a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('associated', $userPermissions)): ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#associatedcontract">Associated Contract</a>
                            </li>
                        <?php endif; ?>

                        <?php if (in_array('permission', $userPermissions)): ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#permissions">Permissions</a>
                            </li>
                        <?php endif; ?>

                         <?php if (in_array('activity', $userPermissions)): ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#activities">Activities</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                </ul>
            </div>
            <div class="col-lg-2 justify-end">
                
            </div>
            <div class="col-lg-12 mt-3">
                <?php if (!empty($notification['message'])): ?>
                    <div id="autoDismissAlert" class="alert alert-<?= $notification['type'] ?> alert-dismissible fade show"
                        role="alert">
                        <strong><?= htmlspecialchars($notification['message']) ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <script>
                        setTimeout(function () {
                            let alertBox = document.getElementById('autoDismissAlert');
                            if (alertBox) {
                                let bsAlert = new bootstrap.Alert(alertBox);
                                bsAlert.close();
                            }
                        }, 5000);
                    </script>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="tab-content">
                    <div id="contractdetails" class="tab-pane active">
                        <form method="POST" id="contractForm">
                            <input type="hidden" name="action" value="submit_contract">
                            <input type="hidden" name="Rid" value="<?= htmlspecialchars($formData['id']) ?>">
                            <input type="hidden" id="contractId" value="<?= htmlspecialchars($contract_id) ?>">

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Contract Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="contract_name"
                                            value="<?= htmlspecialchars($formData['contract_name']) ?>"
                                            class="form-control " placeholder="Enter contract name" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Contract ID <span class="text-danger">*</span></label>
                                        <input type="text" required name="transaction_no"
                                            value="<?= htmlspecialchars($formData['transaction_no']) ?>"
                                            class="form-control " placeholder="Enter contract ID">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">RT Number <span class="text-danger">*</span></label>
                                        <input type="text" required name="rt_number"
                                            value="<?= htmlspecialchars($formData['rt_number']) ?>"
                                            class="form-control " placeholder="Enter RT Number">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Invoice Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" required name="invoice_number"
                                            value="<?= htmlspecialchars($formData['invoice_number']) ?>"
                                            class="form-control " placeholder="Enter Invoice Number">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Contract Owner Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="contract_owner_name"
                                            value="<?= htmlspecialchars($formData['contract_owner_name']) ?>"
                                            class="form-control " placeholder="Enter contract owner name"
                                            required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Contract Owner Email</label>
                                        <input type="email" name="contract_owner_email"
                                            value="<?= htmlspecialchars($formData['contract_owner_email']) ?>"
                                            class="form-control " placeholder="Enter contract owner email">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Contract Owner Contact Number</label>
                                        <input type="text" name="contract_owner_number"
                                            value="<?= htmlspecialchars($formData['contract_owner_number']) ?>"
                                            class="form-control "
                                            placeholder="Enter contract owner contact number">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <div class="radio-inline mr-20">
                                                <input id="customer" type="radio" name="party_type" value="customer"
                                                    <?= isset($formData['party_type']) && $formData['party_type'] == 'customer' ? 'checked' : '' ?> 
                                                    style="cursor: pointer; margin-right: 8px;">
                                                <label class="form-label align-top" for="customer"
                                                    style="cursor: pointer">Customer</label>
                                            </div>
                                            <div class="radio-inline mr-20">
                                                <input id="vendor" type="radio" name="party_type" value="vendor"
                                                    <?= isset($formData['party_type']) && $formData['party_type'] == 'vendor' ? 'checked' : '' ?> 
                                                    style="cursor: pointer; margin-right: 8px;">
                                                <label class="form-label align-top" for="vendor"
                                                    style="cursor: pointer">Vendor</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Party Name</label>
                                        <div class="input-group">
                                            <select name="party_id" id="party_id"
                                                class="form-select js-example-basic-single"
                                                data-selected="<?= htmlspecialchars($formData['party_id'] ?? '', ENT_QUOTES) ?>">
                                                <option value="">Select a Party</option>
                                            </select>
                                            <button type="button" class="btn btn-primary btn-sm mt-2"
                                                onclick="openAddPartyModal()">
                                                Add New Party
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Associated Contract</label>
                                        <select name="parent_contract_id" class="form-select js-example-basic-single">
                                            <option value="0">Select Associated Contract</option>
                                            <?php foreach ($contracts as $contract): ?>
                                                <option value="<?= $contract['id'] ?>"
                                                    <?= $formData['parent_contract_id'] == $contract['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($contract['contract_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Effective Date <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="start_date"
                                            value="<?= fromDate($formData['start_date']) ?>" data-id="1"
                                            class="form-control startdatepicker_1 rangedatepicker " required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Expiration Date <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="end_date"
                                            value="<?= fromDate($formData['end_date']) ?>"
                                            class="form-control enddatepicker_1 " required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    

                                    <div class="form-group mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3"
                                            placeholder="Enter contract description"><?= htmlspecialchars($formData['description']) ?></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Additional Notes</label>
                                        <textarea name="additional_notes" class="form-control" rows="3"
                                            placeholder="Enter additional notes"><?= htmlspecialchars($formData['additional_notes']) ?></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Termination Condition</label>
                                        <textarea name="termination_condition" class="form-control" rows="3"
                                            placeholder="Enter termination conditions"><?= htmlspecialchars($formData['termination_condition']) ?></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state"
                                            value="<?= htmlspecialchars($formData['state']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter State">
                                    </div>
 
                                    <div class="form-group mb-3">
                                        <label class="form-label">ISP</label>
                                        <input type="text" name="isp"
                                            value="<?= htmlspecialchars($formData['isp']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter ISP">
                                    </div>
 
                                    <div class="form-group mb-3">
                                        <label class="form-label">Paid By</label>
                                        <input type="text" name="paid_by"
                                            value="<?= htmlspecialchars($formData['paid_by']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter Paid By">
                                    </div>
 
                                    <div class="form-group mb-3">
                                        <label class="form-label">Internet Charges</label>
                                        <input type="text" name="internet_charges"
                                            value="<?= htmlspecialchars($formData['internet_charges']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter Internet Charges">
                                    </div>
 
                                    <div class="form-group mb-3">
                                        <label class="form-label">Hub SPOC Name</label>
                                        <input type="text" name="hub_spoc_name"
                                            value="<?= htmlspecialchars($formData['hub_spoc_name']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter Hub SPOC Name">
                                    </div>
 
                                    <div class="form-group mb-3">
                                        <label class="form-label">Hub SPOC Number</label>
                                        <input type="text" name="hub_spoc_number"
                                            value="<?= htmlspecialchars($formData['hub_spoc_number']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter Hub SPOC Number">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">WiFi Provider</label>
                                        <input type="text" name="wifi_provider"
                                            value="<?= htmlspecialchars($formData['wifi_provider']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter Wifi Pass">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Wifi Pass</label>
                                        <input type="text" name="wifi_pass"
                                            value="<?= htmlspecialchars($formData['wifi_pass']) ?>"
                                            class="form-control input-lg"
                                            placeholder="Enter Wifi Pass">
                                    </div>
 




                                    <div class="form-group mb-3">
                                        <label class="form-label">Approval Status <span
                                                class="text-danger">*</span></label>
                                        <select name="contract_status" required
                                            class="form-select js-example-basic-single">
                                            <option value="">Select a Approval Status</option>
                                            <option value="Approved" <?= $formData['contract_status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Pending" <?= $formData['contract_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Rejected" <?= $formData['contract_status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                            <option value="Under Review" <?= $formData['contract_status'] == 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="auto_renew" value="1" class="form-check-input"
                                                id="auto_renew" <?= $formData['auto_renew'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="auto_renew">Auto
                                                Renewal</label>
                                        </div>
                                    </div>


                                </div>

                                <div class="col-lg-12 mt-5">
                                    <div class="form-group text-center">
                                        <button type="submit" class="btn btn-primary  float-left">Submit
                                            Contract</button>
                                        <?php if ($is_edit_mode): ?>
                                            <button type="button" class="btn btn-warning ms-2  float-right" data-bs-toggle="modal"
                                                data-bs-target="#rechargeModal">
                                                <i class="fa fa-credit-card"></i> Recharge Plans
                                            </button>
                                            <button type="button" class="btn btn-info ms-2  float-right" data-bs-toggle="modal"
                                                data-bs-target="#rechargeHistoryModal">
                                                <i class="fa fa-history"></i> Recharge History
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="documents" class="tab-pane fade">
                        <form method="POST" enctype="multipart/form-data" id="documentsForm">
                            <input type="hidden" name="action" value="submit_documents">
                            <div class="row">
                                <?php foreach ($documents as $document): ?>
                                    <?php
                                    if ($admin_type !== '1' && !in_array($document['id'], $userDocPermissions)) {
                                        continue;
                                    }

                                    $matchedDetail = null;
                                    foreach ($documentDetails as $docDetail) {
                                        if ($docDetail['document_list_id'] == $document['id']) {
                                            $matchedDetail = $docDetail;
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label"><?= htmlspecialchars($document['document_name']) ?></label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="documents[]" class="form-control" accept=".pdf,.xls,.xlsx">
                                                    <input type="hidden" name="document_list_ids[]" value="<?= $document['id'] ?>">
                                                    <input type="hidden" name="document_comments[]"
                                                        value="<?= htmlspecialchars($document['document_name']) ?>">
                                                    <input type="hidden" name="document_rids[]" value="<?= $matchedDetail['id'] ?? '' ?>">
                                                </div>
                                                <div class="col-lg-4">
                                                    <?php if ($matchedDetail && !empty($matchedDetail['file_name'])): ?>
                                                        <a href="uploads/contracts/<?= htmlspecialchars($matchedDetail['file_name']) ?>" download
                                                            class="btn btn-primary px-3 py-2">
                                                            Download <i class="fa fa-download"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary">Submit Documents</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php /** ?>
                    <div id="documents" class="tab-pane fade">
                        <form method="POST" enctype="multipart/form-data" id="documentsForm">
                            <input type="hidden" name="action" value="submit_documents">
                            <div class="row">
                                <?php foreach ($documents as $document): ?>
                                    <?php
                                    $matchedDetail = null;
                                    foreach ($documentDetails as $docDetail) {
                                        if ($docDetail['document_list_id'] == $document['id']) {
                                            $matchedDetail = $docDetail;
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="col-lg-6">
                                        <div class="form-group mb-3">
                                            <label
                                                class="form-label"><?= htmlspecialchars($document['document_name']) ?></label>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <input type="file" name="documents[]" class="form-control"
                                                        accept=".pdf,.xls,.xlsx">
                                                    <input type="hidden" name="document_list_ids[]"
                                                        value="<?= $document['id'] ?>">
                                                    <input type="hidden" name="document_comments[]"
                                                        value="<?= htmlspecialchars($document['document_name']) ?>">
                                                    <input type="hidden" name="document_rids[]"
                                                        value="<?= $matchedDetail['id'] ?? '' ?>">
                                                </div>
                                                <div class="col-lg-4">
                                                    <?php if ($matchedDetail && !empty($matchedDetail['file_name'])): ?>
                                                        <a href="uploads/contracts/<?= htmlspecialchars($matchedDetail['file_name']) ?>"
                                                            download class="btn btn-primary px-3 py-2">
                                                            Download <i class="fa fa-download"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-success">Submit Documents</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php **/ ?>

                    <div id="workflow" class="tab-pane fade">
                        <form method="POST" id="workflowForm">
                            <input type="hidden" name="action" value="submit_workflow">

                            <div class="mt-3" id="workflow-container">
                                <?php if (empty($workflow)): ?>
                                    <!-- Start Button -->
                                    <button type="button" class="btn btn-primary" onclick="startWorkflow()">Start
                                        Workflow</button>
                                <?php else: ?>
                                    <div class="workflow-wrapper mt-3" id="workflow-steps">
                                        <?php
                                        $workflowData = $workflow[0] ?? null;
                                        if ($workflowData) {
                                            $stepsPermissionsRaw = $workflowData['steps_permissions'] ?? '{}';
                                            $firstDecode = json_decode($stepsPermissionsRaw, true);
                                            $stepsPermissions = is_string($firstDecode) ? json_decode($firstDecode, true) : $firstDecode;
                                            if (!is_array($stepsPermissions))
                                                $stepsPermissions = [];

                                            $totalSteps = $workflowData['workflow_steps'] ?? 1;

                                            for ($step = 1; $step <= $totalSteps; $step++): ?>
                                                <div class="step-block" data-step="<?= $step ?>">
                                                    <div class="step-title">Step <?= $step ?></div>
                                                    <?php if ($step < $totalSteps): ?>
                                                        <!-- <div class="arrow">
                                                            <img src="assets/images/arrow.jpg" alt="→">
                                                        </div> -->
                                                    <?php endif; ?>
                                                    <div class="step-content">
                                                        <select name="step_permission_<?= $step ?>"
                                                            id="step_permission_<?= $step ?>"
                                                            class="form-select js-example-basic-single" required>
                                                            <option value="">Select User</option>
                                                            <?php foreach ($users as $user): ?>
                                                                <option value="<?= $user['id'] ?>" <?= (isset($stepsPermissions[$step]) && $stepsPermissions[$step] == $user['id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($user['user_name'] ?? $user['name'] ?? "User {$user['id']}") ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <div class="button-group">
                                                            <button type="button" class="btn btn-primary img-30 btn-sm p-1"
                                                                onclick="addWorkflowStep(<?= $step ?>)">
                                                                <i class="fa fa-plus m-0"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger  img-30 btn-sm p-1"
                                                                onclick="removeWorkflowStep(this)">
                                                                <i class="fa fa-trash m-0"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endfor;
                                        }
                                        ?>
                                    </div>
                                    <input type="hidden" name="Rid"
                                        value="<?= htmlspecialchars($workflowData['id'] ?? '') ?>">
                                    <div class="submit-container mt-3">
                                        <button type="submit" class="btn btn-primary">Submit Workflow</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>


                    <div class="tab-pane latest-update-card fade" id="activities" role="tabpanel">
                        <div class="card-block">
                            <div class="scroll-widget">
                                <div class="latest-update-box">
                                    <?php if (!empty($activityDetails)): ?>
                                        <?php foreach ($activityDetails as $index => $activity): ?>
                                            <div class="row p-b-30 <?= $index === 0 ? 'p-t-20' : '' ?>">
                                                <div class="col-auto text-end update-meta p-r-0">
                                                    <?php
                                                    $colorClass = 'b-primary';
                                                    if (stripos($activity['activity'], 'success') !== false) {
                                                        $colorClass = 'b-success';
                                                    } elseif (stripos($activity['activity'], 'error') !== false || stripos($activity['activity'], 'fail') !== false) {
                                                        $colorClass = 'b-danger';
                                                    }
                                                    ?>
                                                    <i class="<?= $colorClass ?> update-icon ring"></i>
                                                </div>
                                                <div class="col p-l-5">
                                                    <a href="#!">
                                                        <h6><?= htmlspecialchars(ucwords($activity['activity'] ?? 'Activity')) ?>
                                                        </h6>
                                                    </a>
                                                    <p class="text-muted m-b-0">
                                                        The contract activity
                                                        "<strong><?= strtoupper($activity['activity'] ?? 'ACTIVITY') ?></strong>"
                                                        was updated on
                                                        <?= date('m/d/Y', strtotime($activity['activity_date'] ?? 'now')) ?>
                                                        at
                                                        <?= date('H:i:s', strtotime($activity['activity_date'] ?? 'now')) ?>.
                                                        
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="row p-t-20 p-b-30">
                                            <div class="col-auto text-end update-meta p-r-0">
                                                <i class="b-secondary update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <h6>No Activity Found</h6>
                                                <p class="text-muted m-b-0">There are no recorded activities for
                                                    this contract.</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="associatedcontract" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="bg-white p-4 shadow-sm d-flex flex-column align-items-center text-center">
                                    <div class="table-responsive w-100">
                                        <?php if (!empty($associatedContracts)): ?>
                                        <table class="table table-custom table-bordered-full table-hover m-0">
                                            <tbody>
                                                    <?php foreach ($associatedContracts as $contract): ?>
                                                        <tr class="bg-gray">
                                                            <th colspan="4"
                                                                class="py-2 px-4 text-left text-lg font-semibold text-gray-800">
                                                                <?= htmlspecialchars($contract['contract_name']) ?>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="py-3 px-5 text-center">Contract ID</th>
                                                            <th class="py-3 px-5 text-center">Party Name</th>
                                                            <th class="py-3 px-5 text-center">Effective Date</th>
                                                            <th class="py-3 px-5 text-center">Expiration Date</th>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3 px-5 text-gray-700">
                                                                <?= htmlspecialchars($contract['transaction_no']) ?>
                                                            </td>
                                                            <td class="py-3 px-5 text-gray-600">
                                                                <?= htmlspecialchars($contract['party_id']) ?>
                                                            </td>
                                                            <td class="py-3 px-5 text-green-600">
                                                                <?= htmlspecialchars($contract['start_date'] ?? 'N/A') ?>
                                                            </td>
                                                            <td class="py-3 px-5 text-red-500">
                                                                <?= htmlspecialchars($contract['end_date'] ?? 'N/A') ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                            <?php else: ?>
                                        <div class="card-px text-center ">
                                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Associated Contract</p>   
                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            </div>   
                                        </div>    
                                    <?php endif; ?>
                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="permissions" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-12">
                                <form method="POST" id="permissionsForm">
                                    <input type="hidden" name="action" value="submit_permissions">
                                    <input type="hidden" name="prid" id="permission_rid" value="">
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Select User <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select js-example-basic-single" name="selected_user" required>
                                                    <option value="">Select User</option>
                                                    <?php
                                                    $assignedUsers = array_column($permissionDetails ?? [], 'user');
                                                    foreach ($users as $user):
                                                        $userId = $user['id'];
                                                        $userName = htmlspecialchars($user['display_name'] ?? $user['name'] ?? "User {$userId}");
                                                        $disabled = in_array($userId, $assignedUsers) ? 'disabled' : '';
                                                        ?>
                                                        <option value="<?= $userId ?>" <?= $disabled ?>>
                                                            <?= $userName ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Select Contract Permission</label>
                                                <div class="form-group">
                                                    <?php
                                                    $permissionTypes = ['edit', 'delete', 'document', 'workflows', 'activity', 'associated', 'permission'];
                                                    foreach ($permissionTypes as $perm):
                                                        ?>
                                                        <div class="checkbox-section d-inline-block me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="permissions[]" value="<?= $perm ?>" id="<?= $perm ?>">
                                                            <label class="form-check-label" for="<?= $perm ?>">
                                                                <?= ucfirst($perm) ?> Permission
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3" id="documentPermissionsSection"
                                            style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label">Select Document
                                                    Permissions</label>
                                                <div class="form-group">
                                                    <?php if (!empty($documents)): ?>
                                                        <?php foreach ($documents as $doc): ?>
                                                            <div class="checkbox-section d-inline-block me-3">
                                                                <input class="form-check-input doc-checkbox" type="checkbox"
                                                                    name="document_permissions[]" value="<?= $doc['id'] ?>"
                                                                    id="doc-<?= $doc['id'] ?>">
                                                                <label class="form-check-label" for="doc-<?= $doc['id'] ?>">
                                                                    <?= htmlspecialchars($doc['document_name'] ?? $doc['name'] ?? "Document {$doc['id']}") ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted">No documents available. Please add
                                                            documents first.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <button type="submit" class="btn btn-primary"
                                                id="permissionSubmitBtn">Submit Permissions</button>
                                            <button type="button" class="btn btn-secondary ms-2" id="cancelEditBtn"
                                                onclick="cancelPermissionEdit()" style="display: none;">Cancel
                                                Edit</button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Display existing permissions -->
                                <?php if (!empty($permissionDetails)): ?>
                                    <div class="mt-4">
                                        <h5>Existing Permissions</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Permissions</th>
                                                        <th>Document Permissions</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($permissionDetails as $permission): ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                $userName = 'Unknown User';
                                                                foreach ($users as $user) {
                                                                    if ($user['id'] == $permission['user']) {
                                                                        $userName = $user['user_name'] ?? $user['name'] ?? "User {$user['id']}";
                                                                        break;
                                                                    }
                                                                }
                                                                echo htmlspecialchars($userName);
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $permissions = json_decode($permission['basic_permissions'] ?? '[]', true);
                                                                echo htmlspecialchars(implode(', ', $permissions));
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $docPermissions = json_decode($permission['document_permissions'] ?? '[]', true);
                                                                if (!empty($docPermissions)) {
                                                                    $docNames = [];
                                                                    foreach ($docPermissions as $docId) {
                                                                        $found = false;
                                                                        foreach ($documentDetails as $doc) {
                                                                            if ($doc['id'] == $docId) {
                                                                                $docNames[] = $doc['comments'] ?? $doc['document_name'] ?? "Document {$doc['id']}";
                                                                                $found = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                        if (!$found) {
                                                                            foreach ($documents as $doc) {
                                                                                if ($doc['id'] == $docId) {
                                                                                    $docNames[] = $doc['document_name'] ?? $doc['name'] ?? "Document {$doc['id']}";
                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                    echo htmlspecialchars(implode(', ', $docNames));
                                                                } else {
                                                                    echo 'None';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-warning"
                                                                    onclick="editPermission(<?= htmlspecialchars(json_encode($permission)) ?>)">
                                                                    <i class="fa fa-edit"></i> Edit
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Party Modal -->
<div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPartyModalLabel">Add New <span id="partyTypeText">Party</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPartyForm" method="POST" onsubmit="submitAddPartyForm(event)">
                    <input type="hidden" name="action" value="add_party">
                    <input type="hidden" name="party_type" id="modalPartyType" value="">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><span id="partyTypeText2">Party</span> Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="party_name" class="form-control" placeholder="Enter party name"
                                    required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter email">
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Company</label>
                                <input type="text" name="company" class="form-control" placeholder="Enter company name">
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3"
                                    placeholder="Enter address"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        Add <span id="partyTypeText3"></span> Party
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Simple Recharge Plan Modal -->
<?php if ($is_edit_mode): ?>
    <div class="modal fade" id="rechargeModal" tabindex="-1" aria-labelledby="rechargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechargeModalLabel">Select Recharge Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rechargeForm">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="subscriptionType" class="form-label">Select Plan</label>
                            <select class="form-select js-example-basic-single" id="subscriptionType"
                                name="subscription_type">
                                <option value="30">Monthly</option>
                                <option value="90">Quarterly</option>
                                <option value="180">Half Yearly</option>
                                <option value="365">Yearly</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="activationDate" class="form-label">Activation Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" id="activationDate" name="activation_date" class="form-control" required>
                        </div>

                        <input type="hidden" name="contract_id" value="<?= $contract_id ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recharge History Modal -->
    <div class="modal fade" id="rechargeHistoryModal" tabindex="-1" aria-labelledby="rechargeHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rechargeHistoryModalLabel">Recharge History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>S.N</th>
                                    <th>Recharge Date</th>
                                    <th>Activation Date</th>
                                    <th>Expiry Date</th>
                                    <th>Plan Type</th>
                                    <th>Remaining Days</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="rechargeHistoryBody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        initializeComponents();
        initializeFormValidation();
        initializeAjaxHandlers();
        initializeModalHandlers();
        initializeWorkflowHandlers();
    });

    function initializeComponents() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });


    }

    function initializeFormValidation() {
        const contractForm = document.getElementById('contractForm');
        if (contractForm) {
            contractForm.addEventListener('submit', function (e) {
                if (!contractForm.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    showAlert('Please fill in all required fields.', 'danger');
                }
                contractForm.classList.add('was-validated');
            });
        }
        const documentCheckbox = document.getElementById('document');
        const documentPermissionsSection = document.getElementById('documentPermissionsSection');

        if (documentCheckbox && documentPermissionsSection) {
            documentCheckbox.addEventListener('change', function () {
                documentPermissionsSection.style.display = this.checked ? 'block' : 'none';

                if (!this.checked) {
                    const docCheckboxes = documentPermissionsSection.querySelectorAll('.doc-checkbox');
                    docCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });
        }

        const documentsForm = document.getElementById('documentsForm');
        if (documentsForm) {
            documentsForm.addEventListener('submit', function (e) {
                const fileInputs = documentsForm.querySelectorAll('input[type="file"]');
                let hasFiles = false;

                fileInputs.forEach(input => {
                    if (input.files && input.files.length > 0) {
                        hasFiles = true;
                    }
                });

                if (!hasFiles) {
                    e.preventDefault();
                    showAlert('Please select at least one document to upload.', 'warning');
                }
            });
        }

        const permissionsForm = document.getElementById('permissionsForm');
        if (permissionsForm) {
            permissionsForm.addEventListener('submit', function (e) {
                const userSelect = permissionsForm.querySelector('select[name="selected_user"]');
                const checkboxes = permissionsForm.querySelectorAll('input[name="permissions[]"]:checked');

                if (!userSelect.value) {
                    e.preventDefault();
                    showAlert('Please select a user.', 'warning');
                    return;
                }

                if (checkboxes.length === 0) {
                    e.preventDefault();
                    showAlert('Please select at least one permission.', 'warning');
                    return;
                }
            });
        }

        console.log('Form validation initialized');
    }

    function initializeAjaxHandlers() {
        console.log("=== initializeAjaxHandlers bound ===");

        const partyTypeInputs = document.querySelectorAll('input[name="party_type"]');
        partyTypeInputs.forEach(input => {
            input.addEventListener('change', function () {
                console.log("Radio button changed:", this.value);
                loadPartiesAjax();
            });
        });

        const selectedPartyType = document.querySelector('input[name="party_type"]:checked');
        if (selectedPartyType) {
            console.log("Auto loading for pre-selected type:", selectedPartyType.value);
            loadPartiesAjax();
        }
    }

    // document.addEventListener('DOMContentLoaded', initializeAjaxHandlers);

    function initializeModalHandlers() {
        const addPartyModal = document.getElementById('addPartyModal');
        if (addPartyModal) {
            addPartyModal.addEventListener('show.bs.modal', function () {
                const partyType = document.querySelector('input[name="party_type"]:checked')?.value;
                if (partyType) {
                    document.getElementById('partyTypeText').textContent = partyType;
                    document.getElementById('partyTypeText2').textContent = partyType;
                    document.getElementById('partyTypeText3').textContent = partyType.toLowerCase();
                }
            });
        }

        const rechargeModal = document.getElementById('rechargeModal');
        if (rechargeModal) {
            rechargeModal.addEventListener('show.bs.modal', function () {
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('activationDate').value = today;
            });
        }

        const rechargeForm = document.getElementById('rechargeForm');
        if (rechargeForm) {
            rechargeForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const subscriptionType = formData.get('subscription_type');
                const activationDate = formData.get('activation_date');
                const contractId = formData.get('contract_id');

                if (!subscriptionType || !activationDate) {
                    alert('Please select a subscription type and activation date.');
                    return;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Saving...';
                submitBtn.disabled = true;

                try { 

                    const response = await fetch('ajax.php', {
                        method: 'POST',
                        body: new URLSearchParams({
                            page: 'insertrechargeplan',
                            contract_id: contractId,
                            subscription_type: subscriptionType,
                            activation_date: activationDate,
                            company_id: <?= $company_id ?>,
                            user_id: <?= $user_id ?>
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const responseText = await response.text();
                    let data;
                    try {
                        data = JSON.parse(responseText); 
                    } catch (parseError) { 
                        throw new Error('Invalid JSON response from server: ' + responseText.substring(0, 200));
                    }

                    if (data.result) { 

                        const modal = bootstrap.Modal.getInstance(rechargeModal);
                        if (modal) modal.hide();
                        this.reset();

                        if (typeof loadRechargeHistoryAjax === 'function') { 
                            loadRechargeHistoryAjax(contractId);
                        }
                    } else {
                        console.warn("[WARN] Insert Failed:", data);
                        alert('Error: ' + (data.message || 'Failed to save recharge plan'));
                    }
                } catch (error) { 
                    alert('Error: ' + error.message);
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }

            });
        }

        const rechargeHistoryModal = document.getElementById('rechargeHistoryModal');
        if (rechargeHistoryModal) {
            rechargeHistoryModal.addEventListener('show.bs.modal', function () {
                const contractId = document.getElementById('contractId')
                    ? document.getElementById('contractId').value
                    : null;
                loadRechargeHistoryAjax(contractId);
            });
        }
    }

    function initializeWorkflowHandlers() {
        const workflowForm = document.getElementById('workflowForm');
        if (workflowForm) {
            workflowForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const steps = document.querySelectorAll('.workflow-step, .step-block');
                const workflowSteps = steps.length;
                const stepsPermissions = {};

                let isValid = true;
                steps.forEach((step, index) => {
                    const select = step.querySelector('select');
                    if (!select.value) {
                        isValid = false;
                        select.classList.add('is-invalid');
                    } else {
                        select.classList.remove('is-invalid');
                        stepsPermissions[index + 1] = select.value;
                    }
                });

                if (!isValid) {
                    showAlert('Please select permissions for all workflow steps.', 'warning');
                    return;
                }

                const workflowStepsInput = document.createElement('input');
                workflowStepsInput.type = 'hidden';
                workflowStepsInput.name = 'workflow_steps';
                workflowStepsInput.value = workflowSteps;

                const stepsPermissionsInput = document.createElement('input');
                stepsPermissionsInput.type = 'hidden';
                stepsPermissionsInput.name = 'steps_permissions';
                stepsPermissionsInput.value = JSON.stringify(stepsPermissions);

                workflowForm.appendChild(workflowStepsInput);
                workflowForm.appendChild(stepsPermissionsInput);

                workflowForm.submit();
            });
        }
    }

    // new data
    function openAddPartyModal() {
        const partyType = document.querySelector('input[name="party_type"]:checked')?.value;
        if (!partyType) {
            return;
        }

        const modalPartyType = document.getElementById('modalPartyType');
        const text1 = document.getElementById('partyTypeText');
        const text2 = document.getElementById('partyTypeText2');
        const text3 = document.getElementById('partyTypeText3');

        if (modalPartyType) modalPartyType.value = partyType;
        if (text1) text1.textContent = partyType.charAt(0).toUpperCase() + partyType.slice(1);
        if (text2) text2.textContent = partyType.charAt(0).toUpperCase() + partyType.slice(1);
        if (text3) text3.textContent = partyType.charAt(0).toUpperCase() + partyType.slice(1);

        const modalEl = document.getElementById('addPartyModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        } else {
            console.error("[ERROR] addPartyModal element not found");
        }
    }

    // new data
    function submitAddPartyForm(e) {
        e.preventDefault();
        const form = document.getElementById('addPartyForm');
        const formData = new FormData(form);
        const partyName = formData.get('party_name');

        if (!partyName || !partyName.trim()) {
            showAlert('Party name is required.', 'danger');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Adding...';

        // prepare payload
        const payload = new URLSearchParams({
            page: "add_party",
            party_type: formData.get("party_type"),
            party_name: formData.get("party_name"),
            email: formData.get("email"),
            phone: formData.get("phone"),
            company: formData.get("company"),
            address: formData.get("address")
        });

        console.log("[DEBUG] Sending payload:", Object.fromEntries(payload));

        fetch("ajax.php", {
            method: "POST",
            body: payload
        })
            .then(res => {
                console.log("[DEBUG] Raw Response Status:", res.status);
                return res.text();
            })
            .then(text => {
                console.log("[DEBUG] Raw Response Text:", text);

                let data;
                try {
                    data = JSON.parse(text);
                    console.log("[DEBUG] Parsed JSON:", data);
                } catch (err) {
                    console.error("[ERROR] Failed to parse JSON:", err, "Raw text:", text);
                    throw err;  // let it bubble so .catch() also runs
                }

                if (data.success) {
                    showAlert('Party added successfully!', 'success');
                    form.reset();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addPartyModal'));
                    if (modal) modal.hide();
                    loadPartiesAjax();
                } else {
                    showAlert(data.message || 'Error adding party.', 'danger');
                }
            })
            .catch(error => {
                console.error("[ERROR] Fetch/Processing Error:", error);
                showAlert('Error adding party. Please try again.', 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
    }

    function loadPartiesAjax() {
        console.log("=== loadPartiesAjax triggered ===");

        const partyType = document.querySelector('input[name="party_type"]:checked')?.value;
        console.log("Selected partyType:", partyType);

        if (!partyType) {
            console.warn("No party type selected. Exiting.");
            return;
        }

        const partySelect = document.getElementById('party_id');
        // keep any preselected id stored in data-selected attribute
        const preselectedId = partySelect.dataset.selected ? String(partySelect.dataset.selected) : '';

        partySelect.innerHTML = '<option value="">Loading...</option>';

        $.ajax({
            url: "ajax.php",
            type: "POST",
            dataType: "json",
            data: {
                page: "load_parties",
                party_type: partyType
            },
            beforeSend: function () {
                console.log("AJAX Request sending with data:", { page: "load_parties", party_type: partyType });
            },
            success: function (data, textStatus, xhr) {
                partySelect.innerHTML = '<option value="">Select a Party</option>';
                const parties = (data && data.result) ? data.result : data;

                if (Array.isArray(parties)) {
                    console.log("Total parties received:", parties.length);
                    parties.forEach((party, index) => {
                        console.log(`Party[${index}] ->`, party);

                        const option = document.createElement('option');
                        option.value = party.id;
                        option.textContent =
                            party.display_name ||
                            party.company ||
                            party.customer_name ||
                            party.vendor_name ||
                            `Party ${party.id}`;
                        partySelect.appendChild(option);
                    });

                    // if we have a preselected id (coming from server $formData), set it
                    if (preselectedId) {
                        // ensure option exists before setting
                        const optExists = Array.from(partySelect.options).some(opt => opt.value === preselectedId);
                        if (optExists) {
                            partySelect.value = preselectedId;
                            // remove the data-selected so future radio toggles won't reuse it incorrectly
                            delete partySelect.dataset.selected;
                            console.log("Preselected party set to:", preselectedId);
                        } else {
                            console.warn("Preselected party id not found in the returned list:", preselectedId);
                        }
                    }

                } else {
                    console.warn("Expected an array, got:", parties);
                    partySelect.innerHTML = '<option value="">No parties found</option>';
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
                console.log("Full xhr object:", xhr);
                partySelect.innerHTML = '<option value="">Error loading parties</option>';
            }
        });
    }


    function startWorkflow() {
        const container = document.getElementById('workflow-container');
        container.innerHTML = `
            <div class="workflow-wrapper mt-3" id="workflow-steps">
                ${renderExistingSteps()}
            </div>
            <div class="submit-container mt-3">
                <button type="submit" class="btn btn-primary">Submit Workflow</button>
            </div>
        `;
        $(".js-example-basic-single").select2(); 
    }

    function renderExistingSteps() {
        const stepsFromDB = <?= json_encode($workflowSteps ?? []) ?>;
        let html = '';

        // If DB has no steps → create first step
        if (!stepsFromDB || Object.keys(stepsFromDB).length === 0) {
            html += createWorkflowStep(1);
        } else {
            let stepNum = 1;
            for (const step in stepsFromDB) {
                html += createWorkflowStep(stepNum, stepsFromDB[step]);
                stepNum++;
            }
        }
        return html;
    }


    function createWorkflowStep(step, selected = '') {
        return `
    <div class="step-block" data-step="${step}">
        <div class="step-title">Step ${step} </div>
        <div class="step-content">
            <select name="step_permission_${step}" id="step_permission_${step}" 
                class="form-select js-example-basic-single" required>
                <option value="">Select User</option>
                ${getUserOptions(selected)}
            </select>
            <div class="button-group">
                <button type="button" class="btn btn-primary img-30 btn-sm p-1" 
                    onclick="addWorkflowStep(${step})">
                    <i class="fa fa-plus m-0"></i>
                </button>
                ${step > 1 ? `
                <button type="button" class="btn btn-danger img-30 btn-sm p-1" 
                    onclick="removeWorkflowStep(this)">
                    <i class="fa fa-trash m-0"></i>
                </button>` : ''}
            </div>
        </div>
    </div>`;
    }



    function getUserOptions(selected = '') {
        const users = <?= json_encode($users) ?>;
        return users.map(user => {
            const userName = user.user_name || user.name || `User ${user.id}`;
            const isSelected = user.id == selected ? 'selected' : '';
            return `<option value="${user.id}" ${isSelected}>${userName}</option>`;
        }).join('');
    }

    function addWorkflowStep(currentStep) {
        const stepsContainer = document.getElementById('workflow-steps');
        const totalSteps = stepsContainer.children.length;
        const newStep = createWorkflowStep(totalSteps + 1);
        stepsContainer.insertAdjacentHTML('beforeend', newStep);
         $(".js-example-basic-single").select2(); 
    }

    function removeWorkflowStep(button) {
        const stepBlock = button.closest('.step-block, .workflow-step');
        stepBlock.remove();
    }

    // new data
    function loadRechargeHistoryAjax(contractId) {
        const tbody = document.getElementById('rechargeHistoryBody');
 
        tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Loading...</td></tr>';
 
        $.ajax({
            url: "ajax.php",
            type: "POST",
            dataType: "json",
            data: {
                page: "load_recharge_history",
                contract_id: contractId
            },
            success: function (data) {
                console.log("load_recharge_history", data);
                tbody.innerHTML = '';
 
                let records = [];
                if (data && data.result && Array.isArray(data.result)) {
                    records = data.result;
                } else if (Array.isArray(data)) {
                    records = data;
                }
 
                if (records.length > 0) {
                    let sn = 1;
                    let totalRemaining = 0;
                    let prevExpiry = null;
 
                    records.forEach(row => {
                        let recharge = row.recharge_date;
                        let activation = row.activation_date;
                        let expiry = row.expiry_date;
 
                        // --- Fix 1: Activation = prevExpiry + 1 day ---
                        if (prevExpiry) {
                            const [dd, mm, yyyy] = prevExpiry.split('-');
                            let prevDate = new Date(`${yyyy}-${mm}-${dd}`);
                            prevDate.setDate(prevDate.getDate() + 1); // next day
                            activation = `${String(prevDate.getDate()).padStart(2, '0')}-${String(prevDate.getMonth() + 1).padStart(2, '0')}-${prevDate.getFullYear()}`;
 
                            // --- Fix 2: Expiry = activation + plan_duration - 1 day ---
                            let expDate = new Date(prevDate);
                            expDate.setDate(expDate.getDate() + (row.plan_duration - 1));
                            expiry = `${String(expDate.getDate()).padStart(2, '0')}-${String(expDate.getMonth() + 1).padStart(2, '0')}-${expDate.getFullYear()}`;
                        }
 
                        prevExpiry = expiry; // update for next iteration
 
                        // --- Remaining Days (unchanged) ---
                        let remainingDays = 0;
                        if (row.expiry_date) {
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
 
                            const [dd, mm, yyyy] = row.expiry_date.split('-');
                            const expiryDate = new Date(`${yyyy}-${mm}-${dd}`);
                            expiryDate.setHours(0, 0, 0, 0);
 
                            const diff = (expiryDate - today) / (1000 * 60 * 60 * 24);
                            remainingDays = Math.max(0, diff);
                        }
 
                        if (row.status === "Active") {
                            totalRemaining += remainingDays;
                        }
 
                        // --- Plan type mapping (unchanged) ---
                        const planMap = {
                            30: 'Monthly',
                            90: 'Quarterly',
                            180: 'Half Yearly',
                            365: 'Yearly'
                        };
                        const planType = planMap[row.plan_duration] || `Custom (${row.plan_duration || 'N/A'} days)`;
 
                        tbody.innerHTML += `
                            <tr>
                                <td>${sn++}</td>
                                <td>${recharge}</td>
                                <td>${activation}</td>
                                <td>${expiry}</td>
                                <td>${planType}</td>
                                <td>${remainingDays}</td>
                                <td>
                                    <span class="badge ${row.status === 'Active' ? 'bg-success' : 'bg-danger'}">
                                        ${row.status === 'Active' ? 'Active' : 'Expired'}
                                    </span>
                                </td>
                            </tr>`;
                    });
 
                    tbody.innerHTML += `
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total Remaining Days (Active Plans Only):</td>
                            <td colspan="2" class="fw-bold">${totalRemaining}</td>
                        </tr>`;
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No recharge history found.</td></tr>';
                }
 
            },
            error: function (xhr, status, error) {
                console.error("Error loading history:", error);
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading history. Check console for details.</td></tr>';
            }
        });
    }
 

    function showAlert(message, type = 'info') {
        const alertContainer = document.querySelector('.col-lg-12');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

        const existingAlerts = alertContainer.querySelectorAll('.alert');
        existingAlerts.forEach(existingAlert => existingAlert.remove());

        alertContainer.appendChild(alert);

        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

            function editPermission(permission) {
        console.log('Edit permission:', permission);

        const permissionsTab = document.querySelector('a[href="#permissions"]');
        if (permissionsTab) {
            permissionsTab.click();
        }

        const ridInput = document.getElementById('permission_rid');
        if (ridInput) {
            ridInput.value = permission.id || '';
        }

        const userSelect = document.querySelector('select[name="selected_user"]');
        if (userSelect && permission.user) {
            const currentOption = userSelect.querySelector(`option[value="${permission.user}"]`);
            if (currentOption) {
                currentOption.disabled = false;
                userSelect.value = permission.user;
            }
        }

        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        if (permission.basic_permissions) {
            let permissionsArray = [];
            if (typeof permission.basic_permissions === 'string') {
                try {
                    permissionsArray = JSON.parse(permission.basic_permissions);
                } catch (e) {
                    permissionsArray = permission.basic_permissions.split(',').map(p => p.trim());
                }
            } else if (Array.isArray(permission.basic_permissions)) {
                permissionsArray = permission.basic_permissions;
            }

            permissionsArray.forEach(perm => {
                const checkbox = document.querySelector(`input[name="permissions[]"][value="${perm}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });

            if (permissionsArray.includes('document')) {
                const docSection = document.getElementById('documentPermissionsSection');
                if (docSection) {
                    docSection.style.display = 'block';
                }
            }
        }

        const docCheckboxes = document.querySelectorAll('input[name="document_permissions[]"]');

        docCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        if (permission.document_permissions) {
            let docPermissionsArray = [];

            if (typeof permission.document_permissions === 'string') {
                try {
                    docPermissionsArray = JSON.parse(permission.document_permissions);
                } catch (e) {
                    docPermissionsArray = permission.document_permissions.split(',').map(p => p.trim());
                }
            } else if (Array.isArray(permission.document_permissions)) {
                docPermissionsArray = permission.document_permissions;
            }

            docPermissionsArray.forEach(docId => {
                const checkbox = document.querySelector(`input[name="document_permissions[]"][value="${docId}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }

        const submitBtn = document.getElementById('permissionSubmitBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');
        if (submitBtn) {
            submitBtn.textContent = 'Update Permissions';
            submitBtn.className = 'btn btn-warning';
        }
        if (cancelBtn) {
            cancelBtn.style.display = 'inline-block';
        }

        const permissionsForm = document.getElementById('permissionsForm');
        if (permissionsForm) {
            permissionsForm.scrollIntoView({ behavior: 'smooth' });
        }

        $(".js-example-basic-single").select2();

        showAlert('Permission data loaded for editing. Make your changes and submit.', 'info');
    }
 
    function cancelPermissionEdit() {
        const permissionsForm = document.getElementById('permissionsForm');
        if (permissionsForm) {
            permissionsForm.reset();
        }
 
        const ridInput = document.getElementById('permission_rid');
        if (ridInput) {
            ridInput.value = '';
        }
 
        const submitBtn = document.getElementById('permissionSubmitBtn');
        const cancelBtn = document.getElementById('cancelEditBtn');
        if (submitBtn) {
            submitBtn.textContent = 'Submit Permissions';
            submitBtn.className = 'btn btn-success';
        }
        if (cancelBtn) {
            cancelBtn.style.display = 'none';
        }
 
        const userSelect = document.querySelector('select[name="selected_user"]');
        if (userSelect) {
            userSelect.disabled = false;
            userSelect.value = '';
        }
 
        showAlert('Edit cancelled. Form reset for new permission entry.', 'info');
    }
 
 document.addEventListener("DOMContentLoaded", () => {
    const rules = {
        contract_name: {
            regex: /^[A-Za-z\s]+$/,
            message: "Contract name must only contain letters and spaces."
        },
    };
    function showNotification(input, message) {
        let alert = input.parentNode.querySelector(".validation-error");
        if (!alert) {
            alert = document.createElement("div");
            alert.className = "validation-error text-danger small mt-1";
            input.parentNode.appendChild(alert);
        }
        alert.textContent = message;
        input.classList.add("is-invalid");
    }
 
    function clearNotification(input) {
        let alert = input.parentNode.querySelector(".validation-error");
        if (alert) alert.remove();
        input.classList.remove("is-invalid");
    }
 
    Object.keys(rules).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input) return;
 
        input.addEventListener("input", () => {
            const { regex, message } = rules[field];
            if (!regex.test(input.value)) {
                showNotification(input, message);
            } else {
                clearNotification(input);
            }
        });
    });
 
    const form = document.getElementById("contractsForm") || document.querySelector("form");
    form?.addEventListener("submit", e => {
        let valid = true;
        Object.keys(rules).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                const { regex, message } = rules[field];
                if (!regex.test(input.value)) {
                    showNotification(input, message);
                    valid = false;
                }
            }
        });
        if (!valid) e.preventDefault();
    });
});
    
</script>