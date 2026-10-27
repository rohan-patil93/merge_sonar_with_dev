<?php
$_SESSION['apply_js']['table'] = 1;
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['dropzone'] = 1;

$company_id = $_SESSION['company_id'];
$data_name = $_SESSION['data_name'];
$user_id = $_SESSION['userid'];
$module_id = $_SESSION['work_module_setup_id'];

$contract_id = $_GET['id'] ?? null;
if (isset($_GET['Rid'])) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $contract_id = $rid['1'];
}
// $contract_id = 114;

$is_edit_mode = !empty($contract_id);

$is_renew_mode = isset($_GET['renew']) && $_GET['renew'] === 'Y';

$formData = ['contract_name' => '', 'transaction_no' => '', 'invoice_number' => '', 'rt_number' => '', 'business_code' => '', 'parent_contract_id' => '', 'contract_payment_terms' => '', 'locations' => '', 'typeofaggrements' => '', 'party_type' => 'vendor', 'contract_owner_name' => '', 'contract_owner_email' => '', 'contract_owner_number' => '', 'party_id' => '', 'start_date' => '', 'end_date' => '', 'renewal_frequency' => 'quarterly', 'contract_amount' => '', 'description' => '', 'additional_notes' => '', 'payment_term_id' => '', 'termination_condition' => '', 'contract_status' => 'Pending', 'auto_renew' => false, 'renew_type' => '', 'state' => '', 'isp' => '', 'paid_by' => '', 'internet_charges' => '', 'hub_spoc_name' => '', 'hub_spoc_number' => '', 'wifi_provider' => '', 'wifi_pass' => '', 'additional_document' => '', 'id' => '', 'is_oem' => 'No', 'oem_registration_number' => '', 'oem_distributor_name' => '', 'oem_product_category' => '', 'contract_type' => '','contract_services_id'=>'','contact_persons'=>'']; // additional document new

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

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {

        switch ($_POST['action']) {
            case 'submit_contract':
                handleContractSubmission();
                break;
            case 'renew_contract':
                handleContractRenewal();
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
            default:
                break;
        }
    }
}

$url = API . "?mode=getcontractdetailsreact&method=get&rid={$contract_id}";
$response = callAPI($url);
$data = json_decode($response, true);

$contracts = $data['contractsres'] ?? [];
$genc = $data['gencres'] ?? [];
$des = $data['desres'] ?? [];
$payment_terms = $data['paymenttermsres'] ?? [];
$locations = $data['locationsres'] ?? [];
$historycontracts = $data['historycontractsres'] ?? [];
$parentcontractsres = $data['parentcontractsres'] ?? [];
$insert_attributes = $data['displayattributesres']['list_attributes'] ?? [];
$users = $data['useres'] ?? [];
$documents = $data['documentres'] ?? [];
$contract_services = $data['contract_services'];

$associatedData = $data['associatedcontractres'] ?? [];
$editcontractsres = $data['editcontractsres'] ?? [];
$editdocumnentres = $data['editdocumnentres'] ?? [];
$editpermissionres = $data['editpermissionres'] ?? [];
$editworkflowres = $data['editworkflowres'] ?? [];
$activityres = $data['activityres'] ?? [];
$milestones = $data['milestoneres'] ?? [];
$departments = $data['departmentsres'] ?? [];
$contractTypes = $data['contracttypesres'] ?? [];

$insert_attributes = json_decode($insert_attributes, true);
//echo "<pre>"; print_r($data); echo "</pre>";
function getFieldDisplay($field, $insert_attributes)
{
    $display = $insert_attributes[$field]['insert_display_list'] ?? 1;
    $mandatory = $insert_attributes[$field]['mandatory'] ?? 2;

    return [
        'style' => $display == 2 ? 'display:none;' : '',
        'required' => $mandatory == 1 ? 'required' : '',
        'star' => $mandatory == 1 ? '<span class="text-danger">*</span>' : ''
    ];
}

$fields = ['contract_name', 'transaction_no', 'invoice_number', 'rt_number', 'business_code', 'parent_contract_id', 'contract_payment_terms', 'locations', 'typeofaggrements', 'party_type', 'contract_owner_name', 'contract_owner_email', 'contract_owner_number', 'party_id', 'party_email', 'party_phone', 'party_address', 'start_date', 'end_date', 'renewal_frequency', 'contract_amount', 'description', 'additional_notes', 'payment_term_id', 'termination_condition', 'contract_status', 'auto_renew', 'renew_type', 'state', 'isp', 'paid_by', 'internet_charges', 'hub_spoc_name', 'hub_spoc_number', 'wifi_provider', 'wifi_pass', 'additional_document', 'id', 'description_id', 'milestone', 'is_oem', 'oem_registration_number', 'oem_distributor_name', 'oem_product_category', 'contract_type', 'department_id', 'type_of_contract_id','contract_services_id','contact_persons']; // additional document new

$fieldDisplay = [];
foreach ($fields as $f) {
    $fieldDisplay[$f] = getFieldDisplay($f, $insert_attributes);
}

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
        // Convert object to array if needed
        $contractData = is_object($data['editcontractsres']) ? (array) $data['editcontractsres'] : $data['editcontractsres'];

        // Ensure OEM fields have default values if they're null or empty
        $contractData['is_oem'] = $contractData['is_oem'] ?? 'No';
        $contractData['oem_registration_number'] = $contractData['oem_registration_number'] ?? '';
        $contractData['oem_distributor_name'] = $contractData['oem_distributor_name'] ?? '';
        $contractData['oem_product_category'] = $contractData['oem_product_category'] ?? '';

        $formData = array_merge($formData, $contractData);
        $originator = $contractData['originator'] ?? null;
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
function handleContractRenewal()
{
    global $formData, $contract_id, $versionId;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    $startDateObj = DateTime::createFromFormat('d-m-Y', $_POST['start_date'] ?? '');
    $endDateObj = DateTime::createFromFormat('d-m-Y', $_POST['end_date'] ?? '');

    if (!$startDateObj || !$endDateObj) {
        $notification = ['message' => 'Invalid start or end date format. Use DD-MM-YYYY.', 'type' => 'danger'];
        return;
    }

    if ($startDateObj > $endDateObj) {
        $notification = ['message' => 'Effective Date cannot be after Expiration Date.', 'type' => 'danger'];
        return;
    }

    $additionalDocumentFileName = '';

    if (isset($_FILES['additional_document_file']) && is_array($_FILES['additional_document_file']['name'])) {
        $allowedTypes = ['pdf', 'xls', 'xlsx', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $maxSize = 10 * 1024 * 1024;
        $uploadDir = "./uploads/contracts/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileCount = count($_FILES['additional_document_file']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['additional_document_file']['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['additional_document_file']['name'][$i];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($fileExt, (array)$allowedTypes) && $_FILES['additional_document_file']['size'][$i] <= $maxSize) {
                    $newFileName = time() . "_renewal_" . $i . "_" . basename($fileName);
                    $filePath = $uploadDir . $newFileName;

                    if (move_uploaded_file($_FILES['additional_document_file']['tmp_name'][$i], $filePath)) {
                        $currentFiles = $_POST['all_additional_documents'] ?? '';
                        $currentFiles = !empty($currentFiles) ? $currentFiles . ',' . $newFileName : $newFileName;
                        $_POST['all_additional_documents'] = $currentFiles;
                    }
                }
            }
        }
    }

    $additionalDocumentFileName = $_POST['all_additional_documents'] ?? '';

    $renewalData = [
        'contract_name' => $_POST['contract_name'] ?? '',
        'transaction_no' => $_POST['transaction_no'] ?? '',
        'invoice_number' => $_POST['invoice_number'] ?? '',
        'rt_number' => $_POST['rt_number'] ?? '',
        'business_code' => $_POST['business_code'] ?? '',
        'parent_contract_id' => $_POST['parent_contract_id'] ?? '',
        'contract_payment_terms' => $_POST['contract_payment_terms'] ?? '',
        'locations' => $_POST['locations'] ?? '',
        'typeofaggrements' => $_POST['typeofaggrements'] ?? '',
        'party_type' => $_POST['party_type'] ?? '',
        'contract_owner_name' => $_POST['contract_owner_name'] ?? '',
        'contract_owner_email' => $_POST['contract_owner_email'] ?? '',
        'contract_owner_number' => $_POST['contract_owner_number'] ?? '',
        'party_id' => $_POST['party_id'] ?? '',
        'department_id' => $_POST['department_id'] ?? '',
        'type_of_contract_id' => $_POST['type_of_contract_id'] ?? '',
        'start_date' => $startDateObj->format('Y-m-d'),
        'end_date' => $endDateObj->format('Y-m-d'),
        'renewal_frequency' => $_POST['renewal_frequency'] ?? 'quarterly',
        'contract_amount' => $_POST['contract_amount'] ?? '',
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
        'additional_document' => $additionalDocumentFileName,
        'description_id' => $_POST['description_id'] ?? '',
        'is_oem' => $_POST['is_oem'] ?? 'No',
        'oem_registration_number' => $_POST['oem_registration_number'] ?? '',
        'oem_distributor_name' => $_POST['oem_distributor_name'] ?? '',
        'oem_product_category' => $_POST['oem_product_category'] ?? '',
        'contract_services_id' => $_POST['contract_services_id'] ?? '',
        'contact_persons' => $_POST['contact_persons'] ?? '',
        'contract_id' => $contract_id,
        'Rid' => $contract_id ?? '',
        'version_id' => $versionId,
        'company_id' => $company_id,
        'module_id' => $module_id,
        'user_id' => $user_id,

    ];

    $milestones = [];
    $index = 1;
    while (isset($_POST["milestone_name_{$index}"])) {
        $name = trim($_POST["milestone_name_{$index}"] ?? '');
        $date = trim($_POST["milestone_date_{$index}"] ?? '');
        $price = trim($_POST["milestone_price_{$index}"] ?? '');

        if ($name !== '' || $date !== '' || $price !== '') {
            $milestones[] = [
                'milestone_name' => $name,
                'milestone_date' => $date,
                'milestone_price' => $price
            ];
        }
        $index++;
    }
    $renewalData['milestones'] = $milestones;

    try {
        $renewalUrl = API . "?mode=renewcontractreact&method=post&user_id={$user_id}&company_id={$company_id}";
        $response = file_post_contents($renewalUrl, $renewalData, 'renew');
        $result = json_decode($response, true);

        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";

        $notification = ['message' => 'Contract renewed successfully!', 'type' => 'success'];

    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while renewing the contract!', 'type' => 'danger'];
    }
}

function handleContractSubmission()
{
    global $formData, $permissionDetails, $workflow, $documentDetails, $versionId, $contract_id;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    $startDateObj = DateTime::createFromFormat('d-m-Y', $_POST['start_date'] ?? '');
    $endDateObj = DateTime::createFromFormat('d-m-Y', $_POST['end_date'] ?? '');

    if (!$startDateObj || !$endDateObj) {
        $notification = ['message' => 'Invalid start or end date format. Use DD-MM-YYYY.', 'type' => 'danger'];
        return;
    }

    if ($startDateObj > $endDateObj) {
        $notification = ['message' => 'Effective Date cannot be after Expiration Date.', 'type' => 'danger'];
        return;
    }

    // Handle multiple additional document file uploads (additional document new)
    $additionalDocumentFileName = '';

    // Process new file uploads first
    if (isset($_FILES['additional_document_file']) && is_array($_FILES['additional_document_file']['name'])) {
        $allowedTypes = ['pdf', 'xls', 'xlsx', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        $uploadDir = "./uploads/contracts/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileCount = count($_FILES['additional_document_file']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['additional_document_file']['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['additional_document_file']['name'][$i];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (in_array($fileExt, $allowedTypes) && $_FILES['additional_document_file']['size'][$i] <= $maxSize) {
                    $newFileName = time() . "_additional_" . $i . "_" . basename($fileName);
                    $filePath = $uploadDir . $newFileName;

                    if (move_uploaded_file($_FILES['additional_document_file']['tmp_name'][$i], $filePath)) {
                        $currentFiles = $_POST['all_additional_documents'] ?? '';
                        $currentFiles = !empty($currentFiles) ? $currentFiles . ',' . $newFileName : $newFileName;
                        $_POST['all_additional_documents'] = $currentFiles;
                    }
                }
            }
        }
    }

    $additionalDocumentFileName = $_POST['all_additional_documents'] ?? '';

    $sanitizedFormData = ['contract_name' => $_POST['contract_name'] ?? '', 'transaction_no' => $_POST['transaction_no'] ?? '', 'invoice_number' => $_POST['invoice_number'] ?? '', 'rt_number' => $_POST['rt_number'] ?? '', 'business_code' => $_POST['business_code'] ?? '', 'parent_contract_id' => $_POST['parent_contract_id'] ?? '', 'contract_payment_terms' => $_POST['contract_payment_terms'] ?? '', 'locations' => $_POST['locations'] ?? '', 'typeofaggrements' => $_POST['typeofaggrements'] ?? '', 'party_type' => $_POST['party_type'] ?? '', 'contract_owner_name' => $_POST['contract_owner_name'] ?? '', 'contract_owner_email' => $_POST['contract_owner_email'] ?? '', 'contract_owner_number' => $_POST['contract_owner_number'] ?? '', 'party_id' => $_POST['party_id'] ?? '', 'department_id' => $_POST['department_id'] ?? '', 'type_of_contract_id' => $_POST['type_of_contract_id'] ?? '', 'start_date' => $startDateObj->format('Y-m-d'), 'end_date' => $endDateObj->format('Y-m-d'), 'renewal_frequency' => $_POST['renewal_frequency'] ?? 'quarterly', 'contract_amount' => $_POST['contract_amount'] ?? '', 'description' => $_POST['description'] ?? '', 'additional_notes' => $_POST['additional_notes'] ?? '', 'payment_term_id' => $_POST['payment_term_id'] ?? '', 'termination_condition' => $_POST['termination_condition'] ?? '', 'contract_status' => $_POST['contract_status'] ?? 'Pending', 'auto_renew' => isset($_POST['auto_renew']) ? true : false, 'renew_type' => $_POST['renew_type'] ?? '', 'state' => $_POST['state'] ?? '', 'isp' => $_POST['isp'] ?? '', 'paid_by' => $_POST['paid_by'] ?? '', 'internet_charges' => $_POST['internet_charges'] ?? '', 'hub_spoc_name' => $_POST['hub_spoc_name'] ?? '', 'hub_spoc_number' => $_POST['hub_spoc_number'] ?? '', 'wifi_provider' => $_POST['wifi_provider'] ?? '', 'wifi_pass' => $_POST['wifi_pass'] ?? '', 'additional_document' => $additionalDocumentFileName, 'description_id' => $_POST['description_id'] ?? '', 'is_oem' => $_POST['is_oem'] ?? 'No', 'oem_registration_number' => $_POST['oem_registration_number'] ?? '', 'oem_distributor_name' => $_POST['oem_distributor_name'] ?? '', 'oem_product_category' => $_POST['oem_product_category'] ?? '', 'Rid' => $contract_id ?? '', 'edit_option' => $_POST['edit_option'] ?? 'SaveUpdate', 'contract_services_id' => $_POST['contract_services_id'] ?? '', 'contact_persons' => $_POST['contact_persons'] ?? '',]; // additional document new


    $milestones = [];
    $index = 1;

    while (isset($_POST["milestone_name_{$index}"])) {
        $name = trim($_POST["milestone_name_{$index}"] ?? '');
        $date = trim($_POST["milestone_date_{$index}"] ?? '');
        $price = trim($_POST["milestone_price_{$index}"] ?? '');

        if ($name !== '' || $date !== '' || $price !== '') {
            $milestones[] = [
                'milestone_name' => $name,
                'milestone_date' => $date,
                'milestone_price' => $price
            ];
        }

        $index++;
    }

    $sanitizedFormData['milestones'] = $milestones;
    $sanitizedFormData['permission_data'] = $permissionDetails;
    $sanitizedFormData['workflow_data'] = $workflow;
    $sanitizedFormData['document_data'] = $documentDetails;
    $sanitizedFormData['contract_data'] = $formData;
    $sanitizedFormData['version_id'] = $versionId;

    try {
        $contractUrl = API . "?mode=insertcontractsreact&method=post&user_id={$user_id}&company_id={$company_id}";

        $response = file_post_contents($contractUrl, $sanitizedFormData, 'insert');
        error_log("API Response for " . $contractUrl . ": " . $response);
        $result = json_decode($response, true);

        //echo "<pre> test";print_r($result); echo "</pre>";

        if ($result['contract_result']['LASTID'] == 0) {
            $msgcode = 3001;
        } else {
            $msgcode = 3000;
            $_SESSION['insert_msg'] = $msgcode; 
            header("location:index.php?option=COM_LIST_CONTRACTS");
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function () {
                if (document.readyState == "complete") {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message'][$msgcode]; ?>', '');
                }
            }
        </script>
        <?php

        // if (!empty($result['contract_result']['LASTID'])) {
        //     $contract_id = $result['contract_result']['LASTID'];
        // }

        // $notification = ['message' => 'Data submitted successfully!', 'type' => 'success'];

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
    $processedDocumentTypes = [];

    foreach ($_FILES as $fileKey => $fileData) {
        if (strpos($fileKey, 'document_file_') === 0) {
            $documentId = str_replace('document_file_', '', $fileKey);

            if (is_array($fileData['name'])) {
                $fileCount = count($fileData['name']);

                for ($i = 0; $i < $fileCount; $i++) {
                    $fileName = $fileData['name'][$i];

                    if (empty($fileName))
                        continue;

                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    if (!in_array($fileExt, $allowedTypes)) {
                        $notification = ['message' => 'Only PDF or Excel files allowed!', 'type' => 'danger'];
                        return;
                    }

                    if ($fileData['size'][$i] > $maxSize) {
                        $notification = ['message' => 'Each file must be 10 MB or smaller!', 'type' => 'danger'];
                        return;
                    }

                    $newFileName = time() . "_doc{$documentId}_" . $i . "_" . basename($fileName);
                    $filePath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileData['tmp_name'][$i], $filePath)) {

                        $hiddenKey = "all_documents_{$documentId}";
                        $currentDocs = [];
                        if (isset($_POST[$hiddenKey])) {
                            $currentDocs = json_decode($_POST[$hiddenKey], true) ?: [];
                        }

                        $currentDocs[] = [
                            'id' => '',
                            'file_name' => $newFileName,
                            'comments' => $fileName,
                            'is_new' => true
                        ];

                        $_POST[$hiddenKey] = json_encode($currentDocs);
                    } else {
                        $notification = ['message' => "Failed to move file: $fileName", 'type' => 'danger'];
                        return;
                    }
                }
            }
        }
    }

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'all_documents_') === 0) {
            $documentId = str_replace('all_documents_', '', $key);
            $processedDocumentTypes[] = $documentId;

            // Get all documents for this document type from hidden input
            $allDocs = json_decode($value, true);
            if (is_array($allDocs)) {
                foreach ($allDocs as $doc) {
                    // Skip files that were marked as deleted (they won't be in the hidden input)
                    $documentsForJSON[] = [
                        'document_list_id' => $documentId,
                        'comments' => $doc['comments'] ?? $doc['file_name'] ?? '',
                        'Rid' => $doc['id'] ?? '',
                        'file_name' => $doc['file_name'],
                        'action' => !empty($doc['id']) && !isset($doc['is_new']) ? 'keep_existing' : 'new_upload'
                    ];
                }
            }
        }
    }

    $postData = [
        'user_id' => $user_id,
        'contract_id' => $contract_id,
        'category_id' => 5,
        'company_id' => $company_id,
        'module_id' => $module_id,
        'platform' => 'php',
        'documents_meta' => json_encode($documentsForJSON),
        'processed_document_types' => json_encode($processedDocumentTypes)
    ];

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

    if (empty($_POST['selected_user'])) {
        $notification = ['message' => 'Please select a user for permissions!', 'type' => 'danger'];
        return;
    }

    if (empty($_POST['permissions']) || !is_array($_POST['permissions'])) {
        $notification = ['message' => 'Please select at least one permission!', 'type' => 'danger'];
        return;
    }

    try {
        $submissionData = [
            'user' => $_POST['selected_user'],
            'contract_id' => $contract_id,
            'Rid' => $_POST['prid'] ?? '',
            'permissions' => $_POST['permissions'],
            'document_permissions' => $_POST['document_permissions'] ?? [],
            'company_id' => $company_id,
            'module_id' => $module_id,
            'user_id' => $user_id,

            'permission_data' => $permissionDetails,
            'workflow_data' => $workflow,
            'document_data' => $documentDetails,
            'contract_data' => $formData,
            'version_id' => $versionId,
            'acitivity_data' => 'permission'
        ];

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
$admin_type = 1;
// $admin_type = $_SESSION['admin_type'];
$userPermissions = [];
$userDocPermissions = [];
$isObserver = false;

if ($admin_type == '1') {
    $userPermissions = ['edit', 'delete', 'document', 'workflows', 'activity', 'associated', 'permission', 'observer'];
    $userDocPermissions = array_column($documents, 'id');
} else {
    foreach ($editpermissionres as $permDetail) {
        if ($permDetail['user'] == $user_id) {
            $userPermissions = json_decode($permDetail['basic_permissions'], true) ?? [];
            $userDocPermissions = !empty($permDetail['document_permissions']) ? json_decode($permDetail['document_permissions'], true) : [];

            if (in_array('observer', $userPermissions)) {
                $isObserver = true;
            }
            break;
        }
    }
}

$readonlyAttr = ($isObserver && $admin_type != '1') ? 'readonly' : '';
$disabledAttr = ($isObserver && $admin_type != '1') ? 'disabled' : '';

if ($is_renew_mode) {
    $renewableFields = ['start_date', 'end_date', 'renewal_frequency', 'contract_amount', 'additional_notes'];
    $readonlyAttrRenew = 'readonly';
    $disabledAttrRenew = 'disabled';
}
?>
<?php
		$workflowsteps = getWorkflowStatus('contracts',$contract_id,$formData['current_status']);
		$check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK,'');
		if(in_array($formData['current_status'], (array)$check_array)){

		}else{
			$disable = "disabled";
			$formdisable = "event_disabled";
		}
	?>
<div class="card">
	<?php if($rid != ""){ echo $workflowsteps['current_status_ribbon']; } ?>
    <div class="card-header">
        <h5>Contracts
            <?php if (!empty($formData['contract_type'])): ?>
                <span class="float-end ms-2 px-3 py-1 bg-light border rounded-pill text-primary fw-bold"
                    style="font-size: 0.85rem; vertical-align: middle; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <i class="fa fa-file-text-o me-1"></i> <?= htmlspecialchars($formData['contract_type']) ?>
                </span>
            <?php endif; ?>
        </h5>
        <div class="card-header-right table-card-header">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>Back
            </a>
            <?php
            $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';
            $is_renewed = !empty($formData['renew_data']);
            if (!empty($contract_id) && !$is_cancelled):
                ?>
                <?php if (!$is_renewed): ?>
                    <button type="button" class="btn btn-primary mb-2 desktop-view renew-contract-btn">
                        Renew
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-danger mb-2 ms-1 desktop-view" onclick="showCancelModal()">
                    Cancel Contract
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-block">
	<div class="row">
	<div class="col-lg-6">
	<?php echo $workflowsteps['breadcrumb'];?>
	</div>
	<div class="col-lg-6 text-right">
		<?php echo $workflowsteps['workflow_btn']; ?>
	</div>
</div>
        <div class="row">
            <div class="col-lg-10">
                <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="pill" href="#contractdetails">
                            Contract Details
                        </a>
                    </li>

                    <?php if ($is_edit_mode && !$is_renew_mode): ?>
                        <?php if (in_array('document', $userPermissions)): ?>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#documents">Documents</a>
                            </li>
                        <?php endif; ?>

                        <?php  /**if (in_array('workflows', $userPermissions)): ?>
<li class="nav-item">
<a class="nav-link" data-bs-toggle="pill" href="#workflow">Workflow</a>
</li>
<?php endif; **/ ?>

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
            <div class="col-lg-12 mt-1">
                <?php if (!empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y'): ?>
                    <div class="alert alert-premium-danger d-flex align-items-center p-0 border-0 shadow-sm mb-4 overflow-hidden"
                        style="border-radius: 12px; background: linear-gradient(135deg, #fff5f5 0%, #fff 100%); border-left: 5px solid #dc3545 !important;">
                        <div class="bg-danger px-4 py-4 d-flex align-items-center justify-content-center text-white"
                            style="min-width: 80px;">
                            <i class="fa fa-ban fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0 text-danger fw-bold uppercase tracking-wider" style="letter-spacing: 1px;">
                                    Contract Cancelled</h5>
                                <span class="badge bg-danger rounded-pill px-3 py-2">Inactive</span>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-8">
                                    <p class="mb-0 text-muted small">
                                        <strong class="text-dark">Reason:</strong>
                                        <span
                                            class="ms-1"><?= htmlspecialchars($formData['cancel_reason'] ?? 'Not specified') ?></span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <p class="mb-0 text-muted small">
                                        <strong class="text-dark">Date:</strong>
                                        <span
                                            class="ms-1"><?= !empty($formData['cancel_date']) ? fromDate($formData['cancel_date']) : 'N/A' ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($notification['message'])): ?>
                    <div id="autoDismissAlert" class="alert alert-<?= $notification['type'] ?> alert-dismissible fade show"
                        role="alert">
                        <strong><?= htmlspecialchars($notification['message']) ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div id="auto-dismiss-alert" class="alert-auto-dismiss" data-timeout="5000"></div>
                <?php endif; ?>
            </div>
        </div>
<div class="row <?php echo $formdisable; ?>" >		
            <div class="col-lg-12">
                <div class="tab-content">
                    <div id="contractdetails" class="tab-pane active">
                        <form method="POST" id="contractForm" enctype="multipart/form-data">
                            
                            <input type="hidden" name="action"
                                value="<?= $is_renew_mode ? 'renew_contract' : 'submit_contract' ?>">
                            <input type="hidden" name="Rid" value="<?= htmlspecialchars($formData['id']) ?>">
                            <input type="hidden" id="contractId" value="<?= htmlspecialchars($contract_id) ?>">
                            <input type="hidden" name="edit_option" id="edit_option" value="3">

                            <div class="row">
                                <div class="col-lg-6">
                                     <div class="form-group mb-3  w-full"
                                        style="<?= $fieldDisplay['parent_contract_id']['style'] ?>">
                                        <label class="form-label d-block d-block">Parent/Associated Contract
                                            <?= $fieldDisplay['parent_contract_id']['star'] ?></label>
                                        <select name="parent_contract_id" class="form-select js-example-basic-single"
                                            <?= $fieldDisplay['parent_contract_id']['required'] ?> <?= $disabledAttr ?>>
                                            <option value="0">Select Associated Contract</option>
                                            <?php foreach ($contracts as $contract): ?>
                                                <option value="<?php echo $contract['id'] ?>" <?php echo $formData['parent_contract_id'] == $contract['id'] ? 'selected' : ''; ?>>
                                                         <?php echo $contract['contract_name']." (".fromDate($contract['start_date'])." To ".fromDate($contract['end_date']).") -".$contract['contract_type']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3" style="<?= $fieldDisplay['contract_name']['style'] ?>">
                                        <label class="form-label d-block d-block">Contract Name
                                            <?= $fieldDisplay['contract_name']['star'] ?></label>
                                        <input type="text" name="contract_name"
                                            value="<?= htmlspecialchars($formData['contract_name']) ?>"
                                            class="form-control" placeholder="Enter contract name"
                                            <?= $fieldDisplay['contract_name']['required'] ?> <?= $is_renew_mode ? $readonlyAttrRenew : $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['transaction_no']['style'] ?>">
                                        <label class="form-label d-block d-block">Contract Number</label>
                                        <input type="text" name="transaction_no"
                                            value="<?= htmlspecialchars($formData['transaction_no']) ?>"
                                            class="form-control" readonly>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['rt_number']['style'] ?>">
                                        <label class="form-label d-block d-block">RT Number
                                            <?= $fieldDisplay['rt_number']['star'] ?></label>
                                        <input type="text" name="rt_number"
                                            value="<?= htmlspecialchars($formData['rt_number']) ?>" class="form-control"
                                            placeholder="Enter RT Number" <?= $fieldDisplay['rt_number']['required'] ?>
                                            <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['business_code']['style'] ?>">
                                        <label class="form-label d-block d-block">Business Code
                                            <?= $fieldDisplay['business_code']['star'] ?></label>
                                        <input type="text" name="business_code"
                                            value="<?= htmlspecialchars($formData['business_code']) ?>"
                                            class="form-control" placeholder="Enter Business Code"
                                            <?= $fieldDisplay['business_code']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['invoice_number']['style'] ?>">
                                        <label class="form-label d-block d-block">Invoice Number
                                            <?= $fieldDisplay['invoice_number']['star'] ?></label>
                                        <input type="text" name="invoice_number"
                                            value="<?= htmlspecialchars($formData['invoice_number']) ?>"
                                            class="form-control" placeholder="Enter Invoice Number"
                                            <?= $fieldDisplay['invoice_number']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['department_id']['style'] ?>">
                                        <label class="form-label d-block">Department
                                            <?= $fieldDisplay['department_id']['star'] ?></label>
                                        <select name="department_id" class="form-select js-example-basic-single"
                                            <?= $fieldDisplay['department_id']['required'] ?> <?= $disabledAttr ?>>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?= $dept['id'] ?>" <?= (isset($formData['department_id']) && $formData['department_id'] == $dept['id']) || ($editcontractsres['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($dept['department_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                   
                                    <div class="form-group mb-3  w-full" style="<?= $fieldDisplay['contract_services_id']['style'] ?>"> 
                                        <label class="form-label d-block d-block">Services <?= $fieldDisplay['contract_services_id']['star'] ?></label>
                                        <select name="contract_services_id[]" multiple class="form-select js-example-basic-multiple" <?= $fieldDisplay['contract_services_id']['required'] ?> <?= $disabledAttr ?>>
                                            <?php
                                            $selectedcontractservices = [];
                                            if (!empty($formData['contract_services_id'])) {
                                                $selectedcontractservices = json_decode($formData['contract_services_id'], true);
                                                if (!is_array($selectedcontractservices))
                                                    $selectedcontractservices = [];
                                            }
                                            ?>
                                            <?php foreach ($contract_services as $conserv): ?>
                                                <option value="<?= $conserv['id'] ?>" <?= in_array($conserv['id'], $selectedcontractservices) ? 'selected' : '' ?>><?= htmlspecialchars($conserv['service_name']) ?> </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>
                                    

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['type_of_contract_id']['style'] ?>">
                                        <label class="form-label d-block">Contract Category
                                            <?= $fieldDisplay['type_of_contract_id']['star'] ?></label>
                                        <select name="type_of_contract_id" class="form-select js-example-basic-single"
                                            <?= $fieldDisplay['type_of_contract_id']['required'] ?> <?= $disabledAttr ?>>
                                            <option value="">Select Category</option>
                                            <?php foreach ($contractTypes as $type): ?>
                                                <option value="<?php echo $type['genc_detail_code']; ?>"
                                                    <?php echo ($editcontractsres['type_of_contract_id'] == $type['genc_detail_code'])?'selected':''; ?>>
                                                    <?php echo htmlspecialchars($type['genc_detail_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="form-group mb-3 col-lg-4"
                                            style="<?= $fieldDisplay['contract_owner_name']['style'] ?>">
                                            <label class="form-label d-block d-block">Owner Name
                                                <?= $fieldDisplay['contract_owner_name']['star'] ?></label>
                                            <input type="text" name="contract_owner_name"
                                                value="<?php echo htmlspecialchars($formData['contract_owner_name']); ?>"
                                                class="form-control" placeholder="Enter contract owner name"
                                                <?php echo $fieldDisplay['contract_owner_name']['required']; ?> <?php echo $readonlyAttr; ?>>
                                        </div>

                                        <div class="form-group mb-3 col-lg-4"
                                            style="<?= $fieldDisplay['contract_owner_email']['style'] ?>">
                                            <label class="form-label d-block d-block">Owner Email
                                                <?= $fieldDisplay['contract_owner_email']['star'] ?></label>
                                            <input type="email" name="contract_owner_email"
                                                value="<?= htmlspecialchars($formData['contract_owner_email']) ?>"
                                                class="form-control" placeholder="Enter contract owner email"
                                                <?= $fieldDisplay['contract_owner_email']['required'] ?> <?= $readonlyAttr ?>>
                                        </div>

                                        <div class="form-group mb-3 col-lg-4"
                                            style="<?= $fieldDisplay['contract_owner_number']['style'] ?>">
                                            <label class="form-label d-block d-block">Owner Contact Number
                                                <?= $fieldDisplay['contract_owner_number']['star'] ?></label>
                                            <input type="text" name="contract_owner_number"
                                                value="<?= htmlspecialchars($formData['contract_owner_number']) ?>"
                                                class="form-control" placeholder="Enter contract owner contact number"
                                                <?= $fieldDisplay['contract_owner_number']['required'] ?> <?= $readonlyAttr ?>>
                                        </div>
                                    </div>
                                    <div id="MoreContactPerson">
                                        <?php  $contact_persons = json_decode($formData['contact_persons'], true); 
                                        $mk=1;
                                        foreach($contact_persons as $conpers){ 
                                        ?>
                                        <div class="row" id="contact_persons_remove_<?php echo $mk; ?>">
                                            <div class="form-group mb-3 col-lg-4" >
                                                <input type="text" name="contact_persons[name][]"   value="<?php echo $conpers['name']; ?>" class="form-control" placeholder="Contact Person Name">
                                            </div>
                                            <div class="form-group mb-3 col-lg-4" >
                                                <input type="email" name="contact_persons[email][]"  value="<?php echo $conpers['email']; ?>" class="form-control" placeholder="Contact Person Email">
                                            </div>
                                            <div class="form-group mb-3 col-lg-3">
                                                <input type="number" name="contact_persons[number][]" value="<?php echo $conpers['number']; ?>" class="form-control" placeholder="Contact Person Number">
                                            </div>
                                            <div class="form-group mb-3 col-lg-1">
                                                <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click to Remove" onclick="removeContactPersonsRemove('contact_persons_remove_<?php echo $mk; ?>')"></i>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary " onclick="addMoreContactPerson()">
                                            <i class="fa fa-plus-circle me-1"></i> Add More Contact Person
                                        </button>
                                    </div>
                                    <div class="form-group mb-3" style="<?= $fieldDisplay['party_type']['style'] ?>">
                                        <label class="form-label d-block d-block">Party Type
                                            <?= $fieldDisplay['party_type']['star'] ?></label>
                                        <div class="form-group">
                                            <?php if(CONTRACT_CUSTOMERS != "N"){ ?>
                                            <div class="radio radio-outline radio-inline">
                                                <input id="customer" type="radio" name="party_type" value="customer"
                                                    <?php echo  isset($formData['party_type']) && $formData['party_type'] == 'customer' ? 'checked' : ''; ?>
                                                    <?php echo $is_renew_mode ? $disabledAttrRenew : $disabledAttr; ?>>
                                                <label class="form-label align-top" for="customer">Customer</label>
                                            </div>
                                            <?php } ?>
                                            <div class="radio radio-outline radio-inline">
                                                <input id="vendor" type="radio" name="party_type" value="vendor"
                                                    <?php echo isset($formData['party_type']) && $formData['party_type'] == 'vendor' ? 'checked' : ''; ?>
                                                    <?php echo $is_renew_mode ? $disabledAttrRenew : $disabledAttr; ?>>
                                                <label class="form-label align-top" for="vendor">Vendor</label>
                                            </div>
 
                                        </div>
                                    </div>

                                    <?php $currentAmount = $formData['contract_amount'] ?? 0; ?>

                                    <div class="form-group mb-1" style="<?= $fieldDisplay['party_id']['style'] ?>">
                                        <label class="form-label d-block d-block">Party Name
                                            <?= $fieldDisplay['party_type']['star'] ?></label>
                                        <div class="d-flex align-items-start justify-content-between flex-wrap">
                                            <div class="input-group  w-full" style="flex:1; min-width:250px;">
                                                <select name="party_id" id="party_id"
                                                    class="form-select js-example-basic-single"
                                                    data-selected="<?= htmlspecialchars($formData['party_id'] ?? '', ENT_QUOTES) ?>" required
                                                    <?= $fieldDisplay['party_id']['required'] ?> <?= $disabledAttr ?>>
                                                    <option value="">Select a Party</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Party Info Preview (Relocated) -->
                                    <div id="party-details-preview" class="mt-2 mb-3 p-3 border rounded bg-light d-none"
                                        style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); border-left: 4px solid #4e73df !important;">
                                        <h6 class="mb-3 text-primary border-bottom pb-2"
                                            style="font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; font-weight: 700;">
                                            <i class="fa fa-user-circle me-1"></i> Party Details
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block text-uppercase"
                                                    style="font-size: 0.65rem; font-weight: 600;">Name</small>
                                                <span id="preview-party-name" class="fw-bold text-dark small">—</span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block text-uppercase"
                                                    style="font-size: 0.65rem; font-weight: 600;">Email</small>
                                                <span id="preview-party-email" class="fw-bold text-dark small">—</span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block text-uppercase"
                                                    style="font-size: 0.65rem; font-weight: 600;">Phone</small>
                                                <span id="preview-party-phone" class="fw-bold text-dark small">—</span>
                                            </div>
                                            <!-- <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block text-uppercase"
                                                    style="font-size: 0.65rem; font-weight: 600;">Created Date</small>
                                                <span id="preview-party-created"
                                                    class="fw-bold text-dark small">—</span>
                                            </div> -->
                                            <div class="col-md-6 mb-2" >
                                                <small class="text-muted d-block text-uppercase"
                                                    style="font-size: 0.65rem; font-weight: 600;">BP Code</small>
                                                <span id="preview-bp-code" class="fw-bold text-dark small">—</span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block text-uppercase"
                                                    style="font-size: 0.65rem; font-weight: 600;">Billing Address</small>
                                                <span id="preview-billing-address"
                                                    class="fw-bold text-dark small">—</span>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <small class="text-muted d-block text-uppercase"
                                                    style="font-size: 0.65rem; font-weight: 600;">Shipping Address</small>
                                                <span id="preview-shipping-address"
                                                    class="fw-bold text-dark small">—</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <!-- <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" onclick="openAddPartyModal()" data-bs-target="#addPartyModal" <?= $disabledAttr ?>>
                                            <i class="fa fa-plus-circle me-1"></i> Add New Party
                                        </button> -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openAddPartyModal()" <?= $disabledAttr ?>>
                                            <i class="fa fa-plus-circle me-1"></i> Add New Party
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-9">
                                            <?php if (!empty($historycontracts)): ?>
                                                <div class="table-responsive mt-2">
                                                    <table class="table table-sm table-bordered align-middle mb-0">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th>Contract</th>
                                                                <th>Start Date</th>
                                                                <th>Expiry Date</th>
                                                                <th>Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($historycontracts as $hc):
                                                                $newAmt = floatval($hc['contract_amount']);
                                                                $oldAmt = floatval($currentAmount);
                                                                $diff = $oldAmt && $newAmt ? (($newAmt - $oldAmt) / $oldAmt) * 100 : 0;

                                                                $arrow = $diff > 0
                                                                    ? "<span class='text-success' title='Increased'>&#9650; " . round($diff, 2) . "%</span>"
                                                                    : ($diff < 0
                                                                        ? "<span class='text-danger' title='Decreased'>&#9660; " . round(abs($diff), 2) . "%</span>"
                                                                        : "<span class='text-muted'>—</span>");
                                                                ?>
                                                                <tr class="text-center">
                                                                    <td>
                                                                        <div class="fw-bold">
                                                                            <?= htmlspecialchars($hc['contract_type'] ?: 'Contract') ?>
                                                                        </div>
                                                                        <small
                                                                            class="text-muted"><?= htmlspecialchars($hc['contract_name'] ?: '—') ?></small>
                                                                    </td>
                                                                    <td><?= fromDate($hc['start_date']) ?></td>
                                                                    <td><?= fromDate($hc['end_date']) ?></td>
                                                                    <td>
                                                                        <?= customeummarmonyformate($hc['contract_amount']) ?>
                                                                        <?= $arrow ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            <?php elseif (!empty($parentcontractsres)): ?>
                                                <div class="table-responsive mt-2">
                                                    <table class="table table-sm table-bordered align-middle mb-0">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th>Parent Contract</th>
                                                                <th>Parent Amount</th>
                                                                <th>Amount Increase</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($parentcontractsres as $pc):
                                                                $parentAmt = floatval($pc['contract_amount'] ?? 0);
                                                                $childAmt = floatval($currentAmount);
                                                                $diff = $parentAmt && $childAmt ? (($childAmt - $parentAmt) / $parentAmt) * 100 : 0;

                                                                $arrow = $diff > 0
                                                                    ? "<span class='text-success' title='Increased'>&#9650; " . round($diff, 2) . "%</span>"
                                                                    : ($diff < 0
                                                                        ? "<span class='text-danger' title='Decreased'>&#9660; " . round(abs($diff), 2) . "%</span>"
                                                                        : "<span class='text-muted'>—</span>");
                                                                ?>
                                                                <tr class="text-center">
                                                                    <td><?= htmlspecialchars($pc['contract_name'] ?: '—') ?>
                                                                    </td>
                                                                    <td><?= $parentAmt > 0 ? customeummarmonyformate($pc['contract_amount']) : '—' ?>
                                                                    </td>
                                                                    <td><?= $arrow ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3 mt-2  w-full"
                                        style="<?= $fieldDisplay['contract_payment_terms']['style'] ?>">
                                        <label class="form-label d-block d-block">Payment Terms
                                            <?= $fieldDisplay['contract_payment_terms']['star'] ?></label>
                                        <select name="contract_payment_terms"
                                            class="form-select js-example-basic-single"
                                            <?= $fieldDisplay['contract_payment_terms']['required'] ?> <?= $disabledAttr ?>>
                                            <option value="0">Select Payment Terms</option>
                                            <?php foreach ($payment_terms as $term): ?>
                                                <option value="<?= $term['id'] ?>"
                                                    <?= isset($formData['contract_payment_terms']) && $formData['contract_payment_terms'] == $term['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($term['payment_term_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                   

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['start_date']['style'] ?>">
                                        <label class="form-label d-block d-block">Effective Date
                                            <?= $fieldDisplay['start_date']['star'] ?></label>
                                        <input type="text" name="start_date"
                                            value="<?= fromDate($formData['start_date']) ?>" data-id="1"
                                            class="form-control startdatepicker_1 rangedatepicker"
                                            <?= $fieldDisplay['start_date']['required'] ?> <?= $is_renew_mode ? 'readonly style="background-color: #f8f9fa;"' : $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['end_date']['style'] ?>">
                                        <label class="form-label d-block d-block">Expiration Date
                                            <?= $fieldDisplay['end_date']['star'] ?></label>
                                        <input type="text" name="end_date"
                                            value="<?= fromDate($formData['end_date']) ?>"
                                            class="form-control enddatepicker_1"
                                            <?= $fieldDisplay['end_date']['required'] ?> <?= $is_renew_mode ? '' : $readonlyAttr ?>>
                                    </div>
                                    <?php /** ?>
                                    <div class="form-group mb-3  w-full"
                                        style="<?= $fieldDisplay['renewal_frequency']['style'] ?>">
                                        <label class="form-label d-block d-block">Renewal Notification Frequency
                                            <?= $fieldDisplay['renewal_frequency']['star'] ?></label>
                                        <select name="renewal_frequency" class="form-select js-example-basic-single"
                                            <?= $fieldDisplay['renewal_frequency']['required'] ?> <?= $is_renew_mode ? '' : $disabledAttr ?>>
                                            <option value="onday" <?= isset($formData['renewal_frequency']) && $formData['renewal_frequency'] == 'onday' ? 'selected' : '' ?>>On Day
                                            </option>
                                            <option value="weekly" <?= isset($formData['renewal_frequency']) && $formData['renewal_frequency'] == 'weekly' ? 'selected' : '' ?>>Weekly
                                            </option>
                                            <option value="monthly" <?= isset($formData['renewal_frequency']) && $formData['renewal_frequency'] == 'monthly' ? 'selected' : '' ?>>Monthly
                                            </option>
                                            <option value="quarterly" <?= !isset($formData['renewal_frequency']) || $formData['renewal_frequency'] == 'quarterly' ? 'selected' : '' ?>>
                                                Quarterly</option>
                                        </select>
                                    </div>
                                    <?php **/ ?>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['contract_amount']['style'] ?>">
                                        <label class="form-label d-block d-block">Contract Amount
                                            <?= $fieldDisplay['contract_amount']['star'] ?></label>
                                        <input type="text" name="contract_amount" id="contract_amount"
                                            value="<?= $editcontractsres['contract_amount'] ?>" class="form-control"
                                            placeholder="Enter Contract Amount"
                                            <?= $fieldDisplay['contract_amount']['required'] ?> <?= $is_renew_mode ? '' : $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['milestone']['style'] ?>">
                                        <label class="form-label d-block d-block">Milestones
                                            <?= $fieldDisplay['milestone']['star'] ?></label>

                                        <div id="milestone-container">
                                            <?php
                                            $milestoneCount = 0;
                                            if (!empty($milestones)) {
                                                foreach ($milestones as $m) {
                                                    $milestoneCount++;
                                                    ?>
                                                    <div class="milestone-row mb-2 d-flex align-items-center gap-2">
                                                        <input type="text" name="milestone_name_<?= $milestoneCount ?>"
                                                            value="<?= $m['milestone'] ?? '' ?>"
                                                            class="form-control milestone-name" placeholder="Milestone Name"
                                                            <?= $fieldDisplay['milestone']['required'] ?>         <?= $readonlyAttr ?>>

                                                        <input type="text" name="milestone_date_<?= $milestoneCount ?>"
                                                            value="<?= fromDate($m['milestone_date']) ?>"
                                                            class="form-control datepicker milestone-date"
                                                            <?= $fieldDisplay['milestone']['required'] ?>  <?= $readonlyAttr ?>>

                                                        <input type="number" step="0.01"
                                                            name="milestone_price_<?= $milestoneCount ?>"
                                                            value="<?= $m['milestone_price'] ?>"
                                                            class="form-control milestone-price" placeholder="Price"
                                                            <?= $fieldDisplay['milestone']['required'] ?>         <?= $readonlyAttr ?>>

                                                        <?php if (!$isObserver || $admin_type == '1'): ?>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-milestone">×</button>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                $milestoneCount = 1;
                                                ?>
                                                <div class="milestone-row mb-2 d-flex align-items-center gap-2">
                                                    <input type="text" name="milestone_name_1"
                                                        class="form-control milestone-name" placeholder="Milestone Name"
                                                        <?= $fieldDisplay['milestone']['required'] ?>     <?= $readonlyAttr ?>>
                                                    <input type="text" name="milestone_date_1"
                                                        class="form-control datepicker milestone-date"
                                                        <?= $fieldDisplay['milestone']['required'] ?>     <?= $readonlyAttr ?>>
                                                    <input type="number" step="0.01" name="milestone_price_1"
                                                        class="form-control milestone-price" placeholder="Price"
                                                        <?= $fieldDisplay['milestone']['required'] ?>     <?= $readonlyAttr ?>>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div id="milestone-error" class="text-danger small mt-1 d-none">
                                            Please fill all milestone fields before adding a new one.
                                        </div>

                                        <?php if (!$isObserver || $admin_type == '1'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                id="add-milestone">+ Add Milestone</button>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group mb-3  w-full"
                                        style="<?= $fieldDisplay['description_data']['style'] ?>">
                                        <label class="form-label d-block d-block">Description
                                            <?= $fieldDisplay['description_id']['star'] ?></label>
                                        <select name="description_id" class="form-select js-example-basic-single"
                                            <?= $fieldDisplay['description_id']['required'] ?> <?= $disabledAttr ?>>
                                            <option value="">Select Description</option>
                                            <?php foreach ($des as $d): ?>
                                                <option value="<?= $d['id'] ?>" <?= $formData['description_id'] == $d['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($d['description_type']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3" style="<?= $fieldDisplay['description']['style'] ?>">
                                        <label class="form-label d-block d-block">Description
                                            <?= $fieldDisplay['description']['star'] ?></label>
                                        <textarea name="description" class="form-control" rows="3"
                                            <?= $fieldDisplay['description']['required'] ?> <?= $readonlyAttr ?>><?= htmlspecialchars($formData['description']) ?></textarea>
                                    </div>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['additional_notes']['style'] ?>">
                                        <label class="form-label d-block d-block">Additional Notes
                                            <?= $fieldDisplay['additional_notes']['star'] ?></label>
                                        <textarea name="additional_notes" class="form-control" rows="3"
                                            <?= $fieldDisplay['additional_notes']['required'] ?> <?= $is_renew_mode ? '' : $readonlyAttr ?>><?= htmlspecialchars($formData['additional_notes']) ?></textarea>
                                    </div>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['termination_condition']['style'] ?>">
                                        <label class="form-label d-block d-block">Termination Condition
                                            <?= $fieldDisplay['termination_condition']['star'] ?></label>
                                        <textarea name="termination_condition" class="form-control" rows="3"
                                            <?= $fieldDisplay['termination_condition']['required'] ?> <?= $readonlyAttr ?>><?= htmlspecialchars($formData['termination_condition']) ?></textarea>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['state']['style'] ?>">
                                        <label class="form-label d-block d-block">State
                                            <?= $fieldDisplay['state']['star'] ?></label>
                                        <input type="text" name="state"
                                            value="<?= htmlspecialchars($formData['state']) ?>"
                                            class="form-control input-lg" placeholder="Enter State"
                                            <?= $fieldDisplay['state']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['isp']['style'] ?>">
                                        <label class="form-label d-block d-block">ISP
                                            <?= $fieldDisplay['isp']['star'] ?></label>
                                        <input type="text" name="isp" value="<?= htmlspecialchars($formData['isp']) ?>"
                                            class="form-control input-lg" placeholder="Enter ISP"
                                            <?= $fieldDisplay['isp']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['paid_by']['style'] ?>">
                                        <label class="form-label d-block d-block">Paid By
                                            <?= $fieldDisplay['paid_by']['star'] ?></label>
                                        <input type="text" name="paid_by"
                                            value="<?= htmlspecialchars($formData['paid_by']) ?>"
                                            class="form-control input-lg" placeholder="Enter Paid By"
                                            <?= $fieldDisplay['paid_by']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['internet_charges']['style'] ?>">
                                        <label class="form-label d-block d-block">Internet Charges
                                            <?= $fieldDisplay['internet_charges']['star'] ?></label>
                                        <input type="text" name="internet_charges"
                                            value="<?= htmlspecialchars($formData['internet_charges']) ?>"
                                            class="form-control input-lg" placeholder="Enter Internet Charges"
                                            <?= $fieldDisplay['internet_charges']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['hub_spoc_name']['style'] ?>">
                                        <label class="form-label d-block d-block">Hub SPOC Name
                                            <?= $fieldDisplay['hub_spoc_name']['star'] ?></label>
                                        <input type="text" name="hub_spoc_name"
                                            value="<?= htmlspecialchars($formData['hub_spoc_name']) ?>"
                                            class="form-control input-lg" placeholder="Enter Hub SPOC Name"
                                            <?= $fieldDisplay['hub_spoc_name']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['hub_spoc_number']['style'] ?>">
                                        <label class="form-label d-block d-block">Hub SPOC Number
                                            <?= $fieldDisplay['hub_spoc_number']['star'] ?></label>
                                        <input type="text" name="hub_spoc_number"
                                            value="<?= htmlspecialchars($formData['hub_spoc_number']) ?>"
                                            class="form-control input-lg" placeholder="Enter Hub SPOC Number"
                                            <?= $fieldDisplay['hub_spoc_number']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['wifi_provider']['style'] ?>">
                                        <label class="form-label d-block d-block">WiFi Provider
                                            <?= $fieldDisplay['wifi_provider']['star'] ?></label>
                                        <input type="text" name="wifi_provider"
                                            value="<?= htmlspecialchars($formData['wifi_provider']) ?>"
                                            class="form-control input-lg" placeholder="Enter WiFi Provider"
                                            <?= $fieldDisplay['wifi_provider']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3" style="<?= $fieldDisplay['wifi_pass']['style'] ?>">
                                        <label class="form-label d-block d-block">WiFi Pass
                                            <?= $fieldDisplay['wifi_pass']['star'] ?></label>
                                        <input type="text" name="wifi_pass"
                                            value="<?= htmlspecialchars($formData['wifi_pass']) ?>"
                                            class="form-control input-lg" placeholder="Enter WiFi Pass"
                                            <?= $fieldDisplay['wifi_pass']['required'] ?> <?= $readonlyAttr ?>>
                                    </div>

                                    <div class="form-group mb-3"
                                        style="<?= $fieldDisplay['additional_document']['style'] ?>">
                                        <label class="form-label d-block d-block">Document
                                            <?= $fieldDisplay['additional_document']['star'] ?></label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="file" name="additional_document_file[]"
                                                class="form-control additional-document-file-input" multiple
                                                accept=".pdf,.xls,.xlsx,.doc,.docx,.jpg,.jpeg,.png"
                                                class="form-control additional-document-file-input" <?= $disabledAttr ?>>

                                            <!-- File buttons container -->
                                            <div id="additional_file_buttons" class="d-flex gap-1">
                                                <?php if (!empty($formData['additional_document'])): ?>
                                                    <?php
                                                    $documentFiles = explode(',', $formData['additional_document']);
                                                    foreach ($documentFiles as $index => $fileName):
                                                        if (!empty(trim($fileName))):
                                                            ?>
                                                            <a href="uploads/contracts/<?= htmlspecialchars(trim($fileName)) ?>"
                                                                download class="btn btn-sm btn-outline-primary"
                                                                title="Download: <?= htmlspecialchars(trim($fileName)) ?>"
                                                                data-filename="<?= htmlspecialchars(trim($fileName)) ?>">
                                                                <i class="fa fa-download m-0"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-additional-document-btn"
                                                                title="Remove: <?= htmlspecialchars(trim($fileName)) ?>"
                                                                data-filename="<?= htmlspecialchars(trim($fileName)) ?>"
                                                                <?= $disabledAttr ?>>
                                                                <i class="fa fa-trash m-0"></i>
                                                            </button>
                                                            <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <small class="text-muted">PDF, Excel, Word, Images (Max 10MB each)</small>

                                        <!-- Single hidden input to store all files -->
                                        <input type="hidden" name="all_additional_documents"
                                            id="all_additional_documents"
                                            value="<?= htmlspecialchars($formData['additional_document'] ?? '') ?>">
                                    </div>

                                    <!-- Locations -->
                                    <div class="form-group mb-3  w-full"
                                        style="<?= $fieldDisplay['locations']['style'] ?>">
                                        <label class="form-label d-block d-block">Locations
                                            <?= $fieldDisplay['locations']['star'] ?></label>
                                        <select name="locations[]" multiple
                                            class="form-select js-example-basic-multiple"
                                            <?= $fieldDisplay['locations']['required'] ?> <?= $disabledAttr ?>>
                                            <?php
                                            $selectedLocations = [];
                                            if (!empty($formData['locations'])) {
                                                $selectedLocations = json_decode($formData['locations'], true);
                                                if (!is_array($selectedLocations))
                                                    $selectedLocations = [];
                                            }
                                            ?>

                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?= $location['id'] ?>" <?= in_array($location['id'], $selectedLocations) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($location['location_name']) ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>


                                    <!-- Type of Agreements -->
                                    <div class="form-group mb-3  w-full"
                                        style="<?= $fieldDisplay['typeofaggrements']['style'] ?>">
                                        <label class="form-label d-block d-block">Type of Agreements
                                            <?= $fieldDisplay['typeofaggrements']['star'] ?></label>
                                        <select name="typeofaggrements" class="form-select js-example-basic-multiple"
                                            <?= $fieldDisplay['typeofaggrements']['required'] ?> <?= $disabledAttr ?>>
                                            <option value="">Select Type of Agreements</option>
                                            <?php
                                            $genc = $data['gencres'] ?? [];
                                            foreach ($genc as $item): ?>
                                                <option value="<?= htmlspecialchars($item['genc_detail_code']) ?>"
                                                    <?= isset($formData['typeofaggrements']) && $formData['typeofaggrements'] == $item['genc_detail_code'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($item['genc_detail_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <?php /** ?>    
                                    <!-- Approval Status -->
                                    <div class="form-group mb-3  w-full"
                                        style="<?= $fieldDisplay['contract_status']['style'] ?>">
                                        <label class="form-label d-block d-block">Approval Status
                                            <?= $fieldDisplay['contract_status']['star'] ?></label>
                                        <select name="contract_status" class="form-select js-example-basic-single"
                                            <?= $fieldDisplay['contract_status']['required'] ?> <?= $disabledAttr ?>>
                                            <option value="">Select Approval Status</option>
                                            <option value="Approved" <?= $formData['contract_status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Pending" <?= $formData['contract_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Rejected" <?= $formData['contract_status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                            <option value="Under Review" <?= $formData['contract_status'] == 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                                        </select>
                                    </div> 
                                    

                                    <!-- Auto Renew -->
                                    <div class="form-group mb-3" style="<?= $fieldDisplay['auto_renew']['style'] ?>">
                                        <div class="form-check">
                                            <input type="checkbox" name="auto_renew" value="1" class="form-check-input"
                                                id="auto_renew" <?= $formData['auto_renew'] ? 'checked' : '' ?>
                                                <?= $disabledAttr ?>>
                                            <label class="form-check-label" for="auto_renew">Auto Renewal</label>
                                        </div>
                                    </div>
                                    <?php **/ ?>
                                    <!-- OEM Option -->
                                    <div class="form-group mb-3" style="<?= $fieldDisplay['is_oem']['style'] ?>">
                                        <label class="form-label">OEM (Original Equipment Manufacturer)
                                            <?= $fieldDisplay['is_oem']['star'] ?></label>
                                        <div class="form-group">
                                            <div class="radio radio-outline radio-inline">
                                                <input id="oem_yes" type="radio" name="is_oem" value="Yes"
                                                    <?= (isset($formData['is_oem']) && ($formData['is_oem'] == 'Yes' || $formData['is_oem'] == 'Y')) ? 'checked' : '' ?> <?= $disabledAttr ?>>
                                                <label class="form-label align-top" for="oem_yes">Yes</label>
                                            </div>
                                            <div class="radio radio-outline radio-inline">
                                                <input id="oem_no" type="radio" name="is_oem" value="No"
                                                    <?= (!isset($formData['is_oem']) || empty($formData['is_oem']) || strtoupper($formData['is_oem']) == 'NO' || strtoupper($formData['is_oem']) == 'N') ? 'checked' : '' ?>
                                                    <?= $disabledAttr ?>>
                                                <label class="form-label align-top" for="oem_no">No</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- OEM Details (Hidden by default) -->
                                    <div id="oem_details"
                                        class="oem-details <?= (isset($formData['is_oem']) && ($formData['is_oem'] == 'Yes' || $formData['is_oem'] == 'Y')) ? '' : 'd-none' ?>">
                                        <div class="form-group mb-3"
                                            style="<?= $fieldDisplay['oem_registration_number']['style'] ?>">
                                            <label class="form-label">OEM Registration / Authorization Number
                                                <?= $fieldDisplay['oem_registration_number']['star'] ?></label>
                                            <input type="text" name="oem_registration_number"
                                                value="<?= htmlspecialchars($formData['oem_registration_number'] ?? '') ?>"
                                                class="form-control"
                                                placeholder="Enter OEM Registration/Authorization Number"
                                                <?= $fieldDisplay['oem_registration_number']['required'] ?>
                                                <?= $readonlyAttr ?>>
                                        </div>

                                        <div class="form-group mb-3"
                                            style="<?= $fieldDisplay['oem_distributor_name']['style'] ?>">
                                            <label class="form-label">Authorized Distributor / Dealer Name (if
                                                applicable) <?= $fieldDisplay['oem_distributor_name']['star'] ?></label>
                                            <input type="text" name="oem_distributor_name"
                                                value="<?= htmlspecialchars($formData['oem_distributor_name'] ?? '') ?>"
                                                class="form-control"
                                                placeholder="Enter Authorized Distributor/Dealer Name"
                                                <?= $fieldDisplay['oem_distributor_name']['required'] ?> <?= $readonlyAttr ?>>
                                        </div>

                                        <div class="form-group mb-3"
                                            style="<?= $fieldDisplay['oem_product_category']['style'] ?>">
                                            <label class="form-label">OEM Product Category
                                                <?= $fieldDisplay['oem_product_category']['star'] ?></label>
                                            <input type="text" name="oem_product_category"
                                                value="<?= htmlspecialchars($formData['oem_product_category'] ?? '') ?>"
                                                class="form-control" placeholder="Enter OEM Product Category"
                                                <?= $fieldDisplay['oem_product_category']['required'] ?> <?= $readonlyAttr ?>>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-12 mt-5">
                                    <div class="form-group text-center">
                                        <?php if ($is_renew_mode): ?>
                                            <button type="submit" class="btn btn-success float-left">
                                                <i class="fa fa-refresh"></i> Renew Contract
                                            </button>
                                        <?php elseif ($is_edit_mode): ?>
                                                                   <?php
            $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y'; 
            if (!empty($contract_id) && !$is_cancelled):
?>

					<button type="submit" class="btn btn-primary float-left" 
                                            >Submit Contract</button>
                                          <!--  <button type="button" class="btn btn-primary float-left"
                                                onclick="showEditOptions()">Submit Contract</button>--->
            <?php endif; ?>
                                            <button type="button" class="btn btn-warning ms-2 float-right"
                                                data-bs-toggle="modal" data-bs-target="#rechargeModal"
                                                style="<?= $fieldDisplay['internet_charges']['style'] ?>">
                                                <i class="fa fa-credit-card"></i> Recharge Plans
                                            </button>
                                            <button type="button" class="btn btn-info ms-2 float-right"
                                                data-bs-toggle="modal" data-bs-target="#rechargeHistoryModal"
                                                style="<?= $fieldDisplay['internet_charges']['style'] ?>">
                                                <i class="fa fa-history"></i> Recharge History
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-primary float-left">Submit
                                                Contract</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="documents" class="tab-pane fade">
                        <form method="POST" enctype="multipart/form-data" id="documentsForm">
                            <input type="hidden" name="action" value="submit_documents">

                            <!-- Hidden inputs for contract data -->
                            <input type="hidden" id="hidden_contract_name" name="contract_name" value="">
                            <input type="hidden" id="hidden_transaction_no" name="transaction_no" value="">
                            <input type="hidden" id="hidden_invoice_number" name="invoice_number" value="">
                            <input type="hidden" id="hidden_rt_number" name="rt_number" value="">
                            <input type="hidden" id="hidden_business_code" name="business_code" value="">
                            <input type="hidden" id="hidden_parent_contract_id" name="parent_contract_id" value="">
                            <input type="hidden" id="hidden_contract_payment_terms" name="contract_payment_terms"
                                value="">
                            <input type="hidden" id="hidden_typeofaggrements" name="typeofaggrements" value="">
                            <input type="hidden" id="hidden_contract_owner_name" name="contract_owner_name" value="">
                            <input type="hidden" id="hidden_contract_owner_email" name="contract_owner_email" value="">
                            <input type="hidden" id="hidden_contract_owner_number" name="contract_owner_number"
                                value="">
                            <input type="hidden" id="hidden_party_id" name="party_id" value="">
                            <input type="hidden" id="hidden_department_id" name="department_id" value="">
                            <input type="hidden" id="hidden_type_of_contract_id" name="type_of_contract_id" value="">
                            <input type="hidden" id="hidden_start_date" name="start_date" value="">
                            <input type="hidden" id="hidden_end_date" name="end_date" value="">
                            <input type="hidden" id="hidden_renewal_frequency" name="renewal_frequency" value="">
                            <input type="hidden" id="hidden_contract_amount" name="contract_amount" value="">
                            <input type="hidden" id="hidden_description" name="description" value="">
                            <input type="hidden" id="hidden_additional_notes" name="additional_notes" value="">
                            <input type="hidden" id="hidden_payment_term_id" name="payment_term_id" value="">
                            <input type="hidden" id="hidden_termination_condition" name="termination_condition"
                                value="">
                            <input type="hidden" id="hidden_contract_status" name="contract_status" value="">
                            <input type="hidden" id="hidden_renew_type" name="renew_type" value="">
                            <input type="hidden" id="hidden_state" name="state" value="">
                            <input type="hidden" id="hidden_isp" name="isp" value="">
                            <input type="hidden" id="hidden_paid_by" name="paid_by" value="">
                            <input type="hidden" id="hidden_internet_charges" name="internet_charges" value="">
                            <input type="hidden" id="hidden_hub_spoc_name" name="hub_spoc_name" value="">
                            <input type="hidden" id="hidden_hub_spoc_number" name="hub_spoc_number" value="">
                            <input type="hidden" id="hidden_wifi_provider" name="wifi_provider" value="">
                            <input type="hidden" id="hidden_wifi_pass" name="wifi_pass" value="">
                            <input type="hidden" id="hidden_description_id" name="description_id" value="">
                            <input type="hidden" id="hidden_party_type" name="party_type" value="">
                            <input type="hidden" id="hidden_auto_renew" name="auto_renew" value="">
                            <input type="hidden" id="hidden_locations" name="locations" value="">
                            <input type="hidden" id="hidden_is_oem" name="is_oem" value="">
                            <input type="hidden" id="hidden_oem_registration_number" name="oem_registration_number"
                                value="">
                            <input type="hidden" id="hidden_oem_distributor_name" name="oem_distributor_name" value="">
                            <input type="hidden" id="hidden_oem_product_category" name="oem_product_category" value="">
                            <div id="hidden_milestones_container"></div>

                            <div class="row">
                                <?php foreach ($documents as $document): ?>
                                    <?php
                                    if ($admin_type !== '1' && !in_array($document['id'], $userDocPermissions)) {
                                        continue;
                                    }

                                    // Get all matched documents for this document type
                                    $matchedDocuments = [];
                                    foreach ($documentDetails as $docDetail) {
                                        if ($docDetail['document_list_id'] == $document['id']) {
                                            $matchedDocuments[] = $docDetail;
                                        }
                                    }
                                    ?>
                                    <div class="col-lg-6 mb-3">
                                        <div class="form-group">
                                            <label
                                                class="form-label d-block d-block"><?= htmlspecialchars($document['document_name']) ?></label>
                                            <div class="col-lg-12 mb-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" name="document_file_<?= $document['id'] ?>[]"
                                                        class="form-control document-file-input" multiple
                                                        accept=".pdf,.xls,.xlsx" data-document-id="<?= $document['id'] ?>"
                                                        data-document-name="<?= htmlspecialchars($document['document_name']) ?>"
                                                        class="form-control document-file-input" <?= $disabledAttr ?>>

                                                    <!-- File buttons container -->
                                                    <div id="file_buttons_<?= $document['id'] ?>" class="d-flex gap-1">
                                                        <?php foreach ($matchedDocuments as $docDetail): ?>
                                                            <a href="uploads/contracts/<?= htmlspecialchars($docDetail['file_name']) ?>"
                                                                download class="btn btn-sm btn-outline-primary"
                                                                title="Download: <?= htmlspecialchars($docDetail['file_name']) ?>"
                                                                data-file-id="<?= $docDetail['id'] ?>"
                                                                data-filename="<?= htmlspecialchars($docDetail['file_name']) ?>">
                                                                <i class="fa fa-download m-0"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-document-file-btn"
                                                                title="Remove: <?= htmlspecialchars($docDetail['file_name']) ?>"
                                                                data-file-id="<?= $docDetail['id'] ?>"
                                                                data-filename="<?= htmlspecialchars($docDetail['file_name']) ?>"
                                                                data-document-id="<?= $document['id'] ?>" <?= $disabledAttr ?>>
                                                                <i class="fa fa-trash m-0"></i>
                                                            </button>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                                <small class="text-muted">PDF, Excel (Max 10MB each)</small>

                                                <!-- Single hidden input to store all files for this document type -->
                                                <input type="hidden" name="all_documents_<?= $document['id'] ?>"
                                                    id="all_documents_<?= $document['id'] ?>"
                                                    value="<?= htmlspecialchars(json_encode($matchedDocuments)) ?>">
                                            </div>

                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="col-lg-12">
                                                                                                       <?php
            $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y'; 
            if (!empty($contract_id) && !$is_cancelled):
                ?>
                                    <button type="submit" class="btn btn-primary">Submit Documents</button>
            <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="workflow" class="tab-pane fade">
                        <form method="POST" id="workflowForm">
                            <input type="hidden" name="action" value="submit_workflow">

                            <div class="mt-3" id="workflow-container">
                                <?php if (empty($workflow)): ?>
                                    <!-- Start Button -->
                                    <?php if (!$isObserver || $admin_type == '1'): ?>
                                        <button type="button" class="btn btn-primary start-workflow-btn">Start
                                            Workflow</button>
                                    <?php else: ?>
                                        <p class="text-muted">No workflow configured yet.</p>
                                    <?php endif; ?>
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
                                                    <div class="step-content  w-full">
                                                        <select name="step_permission_<?= $step ?>"
                                                            id="step_permission_<?= $step ?>"
                                                            class="form-select js-example-basic-single" required <?= $disabledAttr ?>>
                                                            <option value="">Select User</option>
                                                            <?php foreach ($users as $user): ?>
                                                                <option value="<?= $user['id'] ?>" <?= (isset($stepsPermissions[$step]) && $stepsPermissions[$step] == $user['id']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($user['user_name'] ?? $user['name'] ?? "User {$user['id']}") ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <?php if (!$isObserver || $admin_type == '1'): ?>
                                                            <div class="button-group">
                                                                <button type="button"
                                                                    class="btn btn-primary img-30 btn-sm p-1 add-workflow-step-btn"
                                                                    data-step="<?= $step ?>">
                                                                    <i class="fa fa-plus m-0"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-danger img-30 btn-sm p-1 remove-workflow-step-btn">
                                                                    <i class="fa fa-trash m-0"></i>
                                                                </button>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endfor;
                                        }
                                        ?>
                                    </div>
                                    <input type="hidden" name="Rid"
                                        value="<?= htmlspecialchars($workflowData['id'] ?? '') ?>">
                                    <?php if (!$isObserver || $admin_type == '1'): ?>
                                        
                                        <div class="submit-container mt-3">
                                            
                                                                                                       <?php
            $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y'; 
            if (!empty($contract_id) && !$is_cancelled):
                ?>
                                            <button type="submit" class="btn btn-primary">Submit Workflow</button>
            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
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
                                                            <th class="py-3 px-5 text-center">Contract Name</th>
                                                            <th class="py-3 px-5 text-center">Effective Date</th>
                                                            <th class="py-3 px-5 text-center">Expiration Date</th>
                                                        </tr>
                                                        <tr>
                                                            <td class="py-3 px-5 text-gray-700">
                                                                <?= htmlspecialchars($contract['transaction_no']) ?>
                                                            </td>
                                                            <td class="py-3 px-5 text-gray-700">
                                                                <?= htmlspecialchars($contract['contract_name']) ?>
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
                                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No
                                                    Associated Contract</p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px mx-h-300 mx-height-300" alt=""
                                                        src="assets/images/work.png">
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
                                <?php if (!$isObserver || $admin_type == '1'): ?>
                                    <form method="POST" id="permissionsForm">
                                        <input type="hidden" name="action" value="submit_permissions">
                                        <input type="hidden" name="prid" id="permission_rid" value="">
                                        <div class="row">
                                            <div class="col-lg-12 mb-3">
                                                <div class="form-group w-full">
                                                    <label class="form-label d-block">Select User <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select js-example-basic-single" name="selected_user"
                                                        required>
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
                                                    <label class="form-label d-block">Select Contract Permission</label>
                                                    <div class="form-group">
                                                        <?php
                                                        //$permissionTypes = ['edit', 'delete', 'document', 'workflows', 'activity', 'associated', 'permission', 'observer'];
                                                        $permissionTypes = ['edit', 'delete', 'document', 'activity', 'associated', 'permission', 'observer'];
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

                                            <div class="col-lg-12 mb-3 d-none" id="documentPermissionsSection">
                                                <div class="form-group">
                                                    <label class="form-label d-block">Select Document
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
                                                
                                                                                                       <?php
            $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y'; 
            if (!empty($contract_id) && !$is_cancelled):
                ?>
                                                <button type="submit" class="btn btn-primary"
                                                    id="permissionSubmitBtn">Submit Permissions</button>
                                                <button type="button"
                                                    class="btn btn-secondary ms-2 d-none cancel-permission-edit-btn"
                                                    id="cancelEditBtn" onclick="cancelPermissionEdit()">Cancel Edit</button>
            <?php endif; ?>
                                            </div>
                                        </div>
                                    </form>
                                <?php endif; ?>

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
                                                                <button type="button"
                                                                    class="btn btn-sm btn-warning edit-permission-btn"
                                                                    data-permission="<?= htmlspecialchars(json_encode($permission)) ?>">
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
<div class="modal fade" id="addPartyModal" aria-labelledby="addPartyModalLabel" aria-hidden="true" role="dialog" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPartyModalLabel">Add New <span id="partyTypeText">Party</span>
                </h5> 
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPartyForm" method="POST" class="add-party-form">
                    <input type="hidden" name="action" value="add_party">
                    <input type="hidden" name="party_type" id="modalPartyType" value="">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label class="form-label d-block"><span id="partyTypeText2">Party</span> Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="party_name" class="form-control" placeholder="Enter party name"
                                    required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">Phone <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="Enter phone number" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label class="form-label d-block">Company <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="company" class="form-control" placeholder="Enter company name" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">Address <span
                                        class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" rows="3"
                                    placeholder="Enter address" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label d-block">BP Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="bp_code" class="form-control" placeholder="Enter BP Code" required>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"> Add <span id="partyTypeText3"></span> Party </button>
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
                        <div class="form-group mb-3  w-full">
                            <label for="subscriptionType" class="form-label d-block">Select Plan</label>
                            <select class="form-select js-example-basic-single" id="subscriptionType"
                                name="subscription_type">
                                <option value="30">Monthly</option>
                                <option value="90">Quarterly</option>
                                <option value="180">Half Yearly</option>
                                <option value="365">Yearly</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="activationDate" class="form-label d-block">Activation Date <span
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

<!-- Edit Confirmation Modal -->
<div class="modal fade" id="editConfirmationModal" tabindex="-1" aria-labelledby="editConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editConfirmationModalLabel">Save Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>How would you like to save these changes?</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary text-start"
                        onclick="submitWithOption('Amendment')">
                        <strong>1. Amendment</strong><br>
                        <small>Create an Amendment for this contract.</small>
                    </button>
                    <button type="button" class="btn btn-outline-warning text-start"
                        onclick="submitWithOption('Addendum')">
                        <strong>2. Addendum</strong><br>
                        <small>Create an Addendum for this contract.</small>
                    </button>
                    <button type="button" class="btn btn-outline-secondary text-start"
                        onclick="submitWithOption('SaveUpdate')">
                        <strong>3. Save & Update</strong><br>
                        <small>Standard update of current contract data.</small>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Contract Modal -->
<div class="modal fade" id="cancelContractModal" tabindex="-1" aria-labelledby="cancelContractModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelContractModalLabel">Cancel Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this contract? This action cannot be undone.</p>
                <div class="form-group">
                    <label for="cancel_reason">Reason for Cancellation <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="cancel_reason" rows="3"
                        placeholder="Please enter the reason..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="submitCancellation()">Confirm
                    Cancellation</button>
            </div>
        </div>
    </div>
</div>


<!-- SweetAlert2 CDN -->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

<!-- Hidden data for JavaScript -->
<div id="php-data" style="display: none;">
    <div id="contract-data"><?php echo json_encode($formData); ?></div>
    <div id="users-data"><?php echo json_encode($users); ?></div>
    <div id="documents-data"><?php echo json_encode($documents); ?></div>
    <div id="locations-data"><?php echo json_encode($locations); ?></div>
    <div id="payment-terms-data"><?php echo json_encode($payment_terms); ?></div>
    <div id="contracts-data"><?php echo json_encode($contracts); ?></div>
    <div id="workflow-steps-data"><?php echo json_encode($workflow ?? []); ?></div>
    <div id="milestone-count"><?php echo $milestoneCount ?? 1; ?></div>
    <div id="is-edit-mode"><?php echo $is_edit_mode ? 'true' : 'false'; ?></div>
    <div id="is-renew-mode"><?php echo $is_renew_mode ? 'true' : 'false'; ?></div>
    <div id="contract-id"><?php echo $contract_id ?? 0; ?></div>
    <div id="company-id"><?php echo $company_id ?? 0; ?></div>
    <div id="user-id"><?php echo $user_id ?? 0; ?></div>
    <div id="data-name"><?php echo $_SESSION['DB_NAME'] ?? 'infisuite'; ?></div>
    <div id="config-name"><?php echo isset($_SESSION['config']['name']) ? ucwords($_SESSION['config']['name']) : ''; ?>
    </div>
</div>

<script src="assets/js/custom_insert_contracts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
