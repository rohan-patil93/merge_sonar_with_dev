<?php
// /$_SESSION['apply_js']['table'] = 1;
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
$is_edit_mode = !empty($contract_id);
$is_renew_mode = isset($_GET['renew']) && $_GET['renew'] === 'Y';
// $show_contract_type_toggle = !$is_edit_mode && !$is_renew_mode;
$contractRoleAccess = resolveContractCreateRoleAccess();
$show_contract_type_toggle = !$is_edit_mode && !$is_renew_mode && !empty($contractRoleAccess['show_toggle']);
$formData = ['contract_name' => '', 'transaction_no' => '', 'invoice_number' => '', 'rt_number' => '', 'business_code' => '', 'parent_contract_id' => '', 'contract_payment_terms' => '', 'locations' => '', 'typeofaggrements' => '', 'party_type' => 'vendor', 'contract_owner_name' => '', 'contract_owner_email' => '', 'contract_owner_number' => '', 'party_id' => '', 'start_date' => '', 'end_date' => '', 'renewal_frequency' => 'quarterly', 'contract_amount' => '', 'description' => '', 'additional_notes' => '', 'payment_term_id' => '', 'termination_condition' => '', 'contract_status' => 'Pending', 'auto_renew' => false, 'renew_type' => '', 'state' => '', 'isp' => '', 'paid_by' => '', 'internet_charges' => '', 'hub_spoc_name' => '', 'hub_spoc_number' => '', 'wifi_provider' => '', 'wifi_pass' => '', 'additional_document' => '', 'id' => '', 'is_oem' => 'No', 'oem_registration_number' => '', 'oem_distributor_name' => '', 'oem_product_category' => '', 'contract_type' => '', 'contract_services_id' => '', 'contact_persons' => '']; // additional document new
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
            case 'submit_amc_contract':
            handleAmcContractSubmission();
            break;
            case 'renew_contract':
            handleContractRenewal();
            break;
            case 'renew_amc_contract':
            handleAmcContractRenewal();
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

// $url = API . "?mode=getcontractdetailsreactamc&method=get&rid={$contract_id}";
// $response = callAPI($url);
// $datatest = json_decode($response, true);
// echo "<pre> 0--------------"; print_r($datatest); echo "</pre>"; //exit;

$url = API . "?mode=getcontractdetailsreactamc&method=get&rid={$contract_id}"."&admin_type=".$_SESSION['admin_type'];
//$url = API . "?mode=getcontractdetailsreact&method=get&rid={$contract_id}";
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
$contract_services = $data['contract_services'] ?? [];


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
if (!is_array($insert_attributes)) {
    $insert_attributes = array();
}
// echo "<pre>"; print_r($milestones);
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

function isContractFieldMandatory($field, $insert_attributes)
{
    return (($insert_attributes[$field]['mandatory'] ?? 2) == 1);
}
function resolveContractCreateRoleAccess()
{
    global $company_id, $user_id;

    $access = array(
        'is_admin' => false,
        'effective_role' => '',
        'can_standard' => true,
        'can_amc' => true,
        'show_toggle' => true,
    );

    $adminType = $_SESSION['admin_type'] ?? '';
    $workingRole = strtoupper(trim((string) ($_SESSION['working_role_short_code'] ?? '')));
    $userRoles = array();
    $wh = array(
        'where' => array(
            'id' => $user_id,
            'company_id' => $company_id,
            'is_active' => '0',
        ),
        'field' => 'role_id',
    );
    $url = API . "?mode=gettabledetailswithconditions&table=application_users&data=" . urlencode(json_encode($wh));
    $roleRows = json_decode(callAPI($url), true);
    $roleJson = '';
    if (is_array($roleRows) && isset($roleRows['role_id'])) {
        $roleJson = $roleRows['role_id'];
    } elseif (count((array)$roleRows) > 0) {
        foreach ((array)$roleRows as $roleRow) {
            $roleRowArr = is_array($roleRow) ? $roleRow : json_decode(json_encode($roleRow), true);
            $roleJson = $roleRowArr['role_id'] ?? '';
            break;
        }
    }
    $decodedRoles = json_decode($roleJson, true);
    if (is_array($decodedRoles)) {
        $userRoles = $decodedRoles;
    }

    $normalizedRoles = array();
    if (count((array)$userRoles) > 0) {
        foreach ($userRoles as $roleCode) {
            $normalizedRoles[] = strtoupper(trim((string) $roleCode));
        }
    }

    $isAdmin = ($adminType == '1')
        || in_array('ADMIN', (array)$normalizedRoles)
        || in_array('CONTRACT_ADMIN', (array)$normalizedRoles)
        || ($workingRole == 'ADMIN')
        || ($workingRole == 'CONTRACT_ADMIN');

    $effectiveRole = $workingRole;
    if (count((array)$normalizedRoles) == 1) {
        $effectiveRole = $normalizedRoles[0];
    } elseif (count((array)$normalizedRoles) > 1 && $workingRole != '' && in_array($workingRole, (array)$normalizedRoles)) {
        $effectiveRole = $workingRole;
    }

    $access['is_admin'] = $isAdmin;
    $access['effective_role'] = $effectiveRole;
    if ($isAdmin) {
        return $access;
    }

    if ($effectiveRole == 'CONTRACT_FM') {
        $access['can_standard'] = false;
        $access['can_amc'] = true;
        $access['show_toggle'] = false;
    } elseif ($effectiveRole == 'CONTRACT_USER') {
        $access['can_standard'] = true;
        $access['can_amc'] = false;
        $access['show_toggle'] = false;
    }

    return $access;
}
$fields = ['contract_name', 'transaction_no', 'invoice_number', 'rt_number', 'business_code', 'parent_contract_id', 'contract_payment_terms', 'locations', 'typeofaggrements', 'party_type', 'contract_owner_name', 'contract_owner_email', 'contract_owner_number', 'party_id', 'party_email', 'party_phone', 'party_address', 'start_date', 'end_date', 'renewal_frequency', 'contract_amount', 'description', 'additional_notes', 'payment_term_id', 'termination_condition', 'contract_status', 'auto_renew', 'renew_type', 'state', 'isp', 'paid_by', 'internet_charges', 'hub_spoc_name', 'hub_spoc_number', 'wifi_provider', 'wifi_pass', 'additional_document', 'id', 'description_id', 'milestone', 'is_oem', 'oem_registration_number', 'oem_distributor_name', 'oem_product_category', 'contract_type', 'department_id', 'type_of_contract_id', 'contract_services_id', 'contact_persons']; // additional document new

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

$contract_is_approved = $is_edit_mode && isContractApproved($formData);
if ($is_renew_mode && $is_edit_mode && !$contract_is_approved) {
    $is_renew_mode = false;
    if (empty($notification['message'])) {
        $notification = ['message' => 'Contract must be approved before it can be renewed.', 'type' => 'warning'];
    }
}

// $page_contract_type = (isset($_GET['contract_type']) && $_GET['contract_type'] == 'amc') ? 'amc' : 'standard';
// $is_amc_mode = $is_edit_mode ? (($formData['is_amc_contract'] ?? 'N') == 'Y') : ($page_contract_type == 'amc');
// $is_amc_edit_mode = $is_edit_mode && $is_amc_mode;
$page_contract_type = (isset($_GET['contract_type']) && $_GET['contract_type'] == 'amc') ? 'amc' : 'standard';
$db_is_amc = (($formData['is_amc_contract'] ?? 'N') == 'Y');
$convert_to = isset($_GET['convert_to']) ? trim((string) $_GET['convert_to']) : '';

if ($is_edit_mode && $convert_to == 'amc') {
    $is_amc_mode = true;
} elseif ($is_edit_mode && $convert_to == 'standard') {
    $is_amc_mode = false;
} elseif ($is_edit_mode) {
    $is_amc_mode = $db_is_amc;
} else {
    $is_amc_mode = ($page_contract_type == 'amc');
}
if (!$is_edit_mode && !$is_renew_mode) {
    if (empty($contractRoleAccess['can_amc']) && !empty($contractRoleAccess['can_standard'])) {
        $is_amc_mode = false;
        $page_contract_type = 'standard';
    } elseif (empty($contractRoleAccess['can_standard']) && !empty($contractRoleAccess['can_amc'])) {
        $is_amc_mode = true;
        $page_contract_type = 'amc';
    }
}
$is_amc_edit_mode = $is_edit_mode && $is_amc_mode;


$amc_contract_data = !empty($data['amccontractres']) ? $data['amccontractres'] : array();
$amc_assets = array();
$amc_service_frequencies = array();
$amc_asset_categories = array();
if (true) {
    $amcLookupResponse = json_decode(callAPI(API . "?mode=getallproductdetails"), true);
    $amc_assets = $amcLookupResponse['asset_details'] ?? array();
    $amc_asset_categories = $amcLookupResponse['asset_categories'] ?? array();
    $amc_departments = $amcLookupResponse['departments'] ?? array();
    $amc_gc_details = $amcLookupResponse['gc_details'] ?? array();
    $amc_service_frequencies_raw = array();
    if (count((array)$amc_gc_details) > 0) {
        foreach ($amc_gc_details as $gcRow) {
            $gcRowArr = is_array($gcRow) ? $gcRow : json_decode(json_encode($gcRow), true);
            if (($gcRowArr['genc_code'] ?? '') == 'AMCSERVICE') {
                $amc_service_frequencies_raw[] = $gcRowArr;
            }
        }
    }
    $amc_service_frequencies = merge_amc_service_frequency_options($amc_service_frequencies_raw);
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

function fetchContractWorkflowLevel($contract_id, $current_status)
{
    if (empty($contract_id)) {
        return null;
    }
    $url = API . "?mode=getdynamicworkflowsteps&transaction_table=contracts&transaction_no=" . urlencode($contract_id) . "&current_status=" . urlencode($current_status);
    $response = callAPI($url);
    $result = json_decode($response, true);
    if (!is_array($result)) {
        return null;
    }
    return $result['workflow_details']['workflow_level'] ?? null;
}

/**
 * AMC service started when any schedule row matches lock criteria (via GET API per asset).
 * Non-AMC: treat as started once contract start_date is on or before today.
 */
/**
 * Service started for lock when contract start_date is on/before today,
 * or when any AMC schedule row matches lock criteria (via GET API per asset).
 */
function hasAmcServiceStarted($contract_id, $company_id, $formData, $amc_contract_data = array())
{
    if (empty($contract_id)) {
        return false;
    }

    $startDate = $formData['start_date'] ?? '';
    if ($startDate != '' && $startDate <= date('Y-m-d')) {
        return true;
    }

    $assetIds = array();
    $locations = $amc_contract_data['locations'] ?? array();
    if (count((array) $locations) > 0) {
        foreach ($locations as $locRow) {
            $assets = $locRow['assets'] ?? array();
            if (count((array) $assets) > 0) {
                foreach ($assets as $assetRow) {
                    $assetRowId = (int) ($assetRow['id'] ?? 0);
                    if ($assetRowId > 0) {
                        $assetIds[$assetRowId] = $assetRowId;
                    }
                }
            }
        }
    }

    if (count((array) $assetIds) == 0) {
        return false;
    }

    $nowTs = time();
    foreach ($assetIds as $assetRowId) {
        $scheduleUrl = API . "?mode=getamccontractassetschedules&method=get&contract_id=" . urlencode($contract_id) . "&contract_amc_asset_id=" . urlencode($assetRowId) . "&company_id=" . urlencode($company_id);
        $scheduleResponse = callAPI($scheduleUrl);
        $scheduleData = json_decode($scheduleResponse, true);
        $schedules = $scheduleData['schedules'] ?? array();
        if (count((array) $schedules) > 0) {
            foreach ($schedules as $scheduleRow) {
                if (!empty($scheduleRow['actual_start_datetime'])) {
                    return true;
                }
                $planned = $scheduleRow['planned_service_datetime'] ?? '';
                if ($planned != '' && strtotime($planned) !== false && strtotime($planned) <= $nowTs) {
                    return true;
                }
            }
        }
    }

    return false;
}

function getContractFormLockState($contract_id, $formData, $company_id, $workflow_level = null, $amc_contract_data = array())
{
    $formdisable = '';
    $locked = false;
    $reason = '';

    if (empty($contract_id)) {
        return array(
            'locked' => false,
            'formdisable' => $formdisable,
            'reason' => $reason,
        );
    }

    $current_status = $formData['current_status'] ?? '';
    if ($current_status === null) {
        $current_status = '';
    }

    $editableStatuses = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK, '', 0, '0');
    if (in_array($current_status, (array) $editableStatuses)) {
        return array(
            'locked' => false,
            'formdisable' => $formdisable,
            'reason' => $reason,
        );
    }

    if ($workflow_level === null) {
        $workflow_level = fetchContractWorkflowLevel($contract_id, $current_status);
    }

    // workflow_level 0 = submit for approval goes directly to approved; allow edit until schedule starts.
    // workflow_level > 0 = regular multi-step workflow; approved contracts stay locked.
    if ($workflow_level == 0 && $current_status == ST_APPROVED) {
        if (!hasAmcServiceStarted($contract_id, $company_id, $formData, $amc_contract_data)) {
            return array(
                'locked' => false,
                'formdisable' => $formdisable,
                'reason' => $reason,
            );
        }
        $locked = true;
        $formdisable = 'event_disabled';
        $reason = 'Service schedule has started; contract can no longer be edited.';
        return array(
            'locked' => $locked,
            'formdisable' => $formdisable,
            'reason' => $reason,
        );
    }

    $locked = true;
    $formdisable = 'event_disabled';
    if ($current_status == ST_APPROVED) {
        $reason = 'This contract is approved and can no longer be edited.';
    } else {
        $reason = 'This contract is in workflow approval and cannot be edited until it is sent back or rejected.';
    }

    return array(
        'locked' => $locked,
        'formdisable' => $formdisable,
        'reason' => $reason,
    );
}

function isContractApproved($formData)
{
    $current_status = $formData['current_status'] ?? '';
    if ($current_status === null) {
        $current_status = '';
    }
    return $current_status == ST_APPROVED;
}

function fetchContractIsApproved($contract_id)
{
    if (empty($contract_id)) {
        return false;
    }
    $apiResponse = json_decode(callAPI(API . "?mode=getcontractdetailsreact&method=get&rid={$contract_id}"), true);
    if (empty($apiResponse['editcontractsres'])) {
        return false;
    }
    $contractRow = is_object($apiResponse['editcontractsres'])
    ? (array) $apiResponse['editcontractsres']
    : $apiResponse['editcontractsres'];
    return isContractApproved($contractRow);
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

function collectMilestonesFromPost()
{
    $milestones = array();
    $indices = array();
    foreach ((array) $_POST as $key => $val) {
        if (preg_match('/^milestone_name_(\d+)$/', (string) $key, $matches)) {
            $indices[(int) $matches[1]] = true;
        }
    }
    if (count((array) $indices) > 0) {
        ksort($indices);
        foreach (array_keys($indices) as $index) {
            $name = trim((string) ($_POST["milestone_name_{$index}"] ?? ''));
            $date = trim((string) ($_POST["milestone_date_{$index}"] ?? ''));
            $price = trim((string) ($_POST["milestone_price_{$index}"] ?? ''));
            if ($name != '' || $date != '' || $price != '') {
                $milestones[] = array(
                    'milestone_name' => $name,
                    'milestone_date' => $date,
                    'milestone_price' => $price
                );
            }
        }
    }
    return $milestones;
}

function renewPostValue($key, $formData, $default = '')
{
    if (array_key_exists($key, $_POST)) {
        $val = $_POST[$key];
        if (is_array($val) && count((array) $val) > 0) {
            return $val;
        }
        if (!is_array($val) && trim((string) $val) != '') {
            return $val;
        }
    }
    if (isset($formData[$key]) && $formData[$key] != '' && $formData[$key] != null) {
        return $formData[$key];
    }
    return $default;
}

function buildRenewalAmountHistoryChain($contractId, $contracts)
{
    $chain = array();
    $contractId = (int) $contractId;
    if ($contractId <= 0) {
        return $chain;
    }

    $contractsById = array();
    if (count((array) $contracts) > 0) {
        foreach ($contracts as $contractRow) {
            $row = is_array($contractRow) ? $contractRow : json_decode(json_encode($contractRow), true);
            $id = (int) ($row['id'] ?? 0);
            if ($id > 0) {
                $contractsById[$id] = $row;
            }
        }
    }

    $currentId = $contractId;
    $visited = array();
    $maxDepth = 50;

    while ($currentId > 0 && count((array) $chain) < $maxDepth) {
        if (isset($visited[$currentId])) {
            break;
        }
        $visited[$currentId] = true;

        if (!isset($contractsById[$currentId])) {
            break;
        }

        $row = $contractsById[$currentId];
        $chain[] = array(
            'start_date' => !empty($row['start_date']) ? fromDate($row['start_date']) : '',
            'end_date' => !empty($row['end_date']) ? fromDate($row['end_date']) : '',
            'amount' => (float) ($row['contract_amount'] ?? 0),
        );

        $parentId = (int) ($row['parent_contract'] ?? 0);
        if ($parentId <= 0) {
            $parentId = (int) ($row['parent_contract_id'] ?? 0);
        }
        if ($parentId <= 0 || $parentId == $currentId) {
            break;
        }
        $currentId = $parentId;
    }

    return $chain;
}

function parseUnifiedContractDates(&$notification)
{
    $startYmd = dateconvertdbformat(trim((string) ($_POST['start_date'] ?? '')));
    $endYmd = dateconvertdbformat(trim((string) ($_POST['end_date'] ?? '')));

    if ($startYmd == '' || $endYmd == '' || $startYmd == '1970-01-01' || $endYmd == '1970-01-01') {
        $notification = array('message' => 'Effective and expiration dates are required.', 'type' => 'danger');
        return false;
    }

    if (strtotime($startYmd) > strtotime($endYmd)) {
        $notification = array('message' => 'Effective(Start) Date cannot be after Expiration(End) Date.', 'type' => 'danger');
        return false;
    }

    return array('start_date' => $startYmd, 'end_date' => $endYmd);
}

function normalizeAmcPostData($contractStartYmd, $contractEndYmd, $forceAlignToContract = false)
{
    if (!isset($_POST['amc_loc']) || !is_array($_POST['amc_loc'])) {
        return;
    }
    foreach ($_POST['amc_loc'] as $locIdx => $locRow) {
        if (!is_array($locRow)) {
            continue;
        }
        $locStart = dateconvertdbformat(trim((string) ($locRow['service_start_date'] ?? '')));
        $locEnd = dateconvertdbformat(trim((string) ($locRow['service_end_date'] ?? '')));
        if ($forceAlignToContract && $contractStartYmd != '') {
            $locStart = $contractStartYmd;
        } elseif (($locStart == '' || $locStart == '1970-01-01') && $contractStartYmd != '') {
            $locStart = $contractStartYmd;
        }
        if ($forceAlignToContract && $contractEndYmd != '') {
            $locEnd = $contractEndYmd;
        } elseif (($locEnd == '' || $locEnd == '1970-01-01') && $contractEndYmd != '') {
            $locEnd = $contractEndYmd;
        }
        $_POST['amc_loc'][$locIdx]['service_start_date'] = $locStart;
        $_POST['amc_loc'][$locIdx]['service_end_date'] = $locEnd;

        if (!isset($_POST['amc_asset'][$locIdx]) || !is_array($_POST['amc_asset'][$locIdx])) {
            continue;
        }
        foreach ($_POST['amc_asset'][$locIdx] as $assetIdx => $assetRow) {
            if (!is_array($assetRow)) {
                continue;
            }
            if ($forceAlignToContract) {
                $assetStart = $locStart;
                $assetEnd = $locEnd;
            } else {
                $assetStart = dateconvertdbformat(trim((string) ($assetRow['service_start_date'] ?? '')));
                $assetEnd = dateconvertdbformat(trim((string) ($assetRow['service_end_date'] ?? '')));
                if (($assetStart == '' || $assetStart == '1970-01-01') && $locStart != '') {
                    $assetStart = $locStart;
                }
                if (($assetEnd == '' || $assetEnd == '1970-01-01') && $locEnd != '') {
                    $assetEnd = $locEnd;
                }
                if (($assetStart == '' || $assetStart == '1970-01-01') && $contractStartYmd != '') {
                    $assetStart = $contractStartYmd;
                }
                if (($assetEnd == '' || $assetEnd == '1970-01-01') && $contractEndYmd != '') {
                    $assetEnd = $contractEndYmd;
                }
            }
            $_POST['amc_asset'][$locIdx][$assetIdx]['service_start_date'] = $assetStart;
            $_POST['amc_asset'][$locIdx][$assetIdx]['service_end_date'] = $assetEnd;
        }
    }
}

function validateUnifiedContractBasics(&$notification)
{
    if (trim($_POST['contract_name'] ?? '') == '') {
        $notification = array('message' => 'Contract name is required.', 'type' => 'danger');
        return false;
    }

    if (parseUnifiedContractDates($notification) == false) {
        return false;
    }

    $partyType = $_POST['party_type'] ?? '';
    $isInhouse = ($partyType == 'inhouse') || (($_POST['amc_service_mode'] ?? '') == 'inhouse');
    if (!$isInhouse && empty($_POST['party_id'])) {
        $notification = array('message' => 'Party is required.', 'type' => 'danger');
        return false;
    }
    if (empty($_POST['department_id'])) {
        $notification = array('message' => 'Department is required.', 'type' => 'danger');
        return false;
    }

    $amount = trim((string) ($_POST['contract_amount'] ?? ''));
    if ($amount == '') {
        $notification = array('message' => 'Contract amount is required.', 'type' => 'danger');
        return false;
    }
    if (!is_numeric($amount)) {
        $notification = array('message' => 'Contract amount must be a valid number.', 'type' => 'danger');
        return false;
    }

    return true;
}

function handleContractRenewal()
{
    global $formData, $contract_id, $versionId;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    $renewContractId = $contract_id ?? ($_POST['Rid'] ?? '');
    if (!fetchContractIsApproved($renewContractId)) {
        $notification = ['message' => 'Contract must be approved before it can be renewed.', 'type' => 'danger'];
        return;
    }

    $contractDates = parseUnifiedContractDates($notification);
    if ($contractDates == false) {
        return;
    }
    $startYmd = $contractDates['start_date'];
    $endYmd = $contractDates['end_date'];

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
        'contract_name' => renewPostValue('contract_name', $formData),
        'transaction_no' => renewPostValue('transaction_no', $formData),
        'invoice_number' => renewPostValue('invoice_number', $formData),
        'rt_number' => renewPostValue('rt_number', $formData),
        'business_code' => renewPostValue('business_code', $formData),
        'parent_contract_id' => renewPostValue('parent_contract_id', $formData),
        'contract_payment_terms' => renewPostValue('contract_payment_terms', $formData),
        'locations' => renewPostValue('locations', $formData),
        'typeofaggrements' => renewPostValue('typeofaggrements', $formData),
        'party_type' => renewPostValue('party_type', $formData),
        'contract_owner_name' => renewPostValue('contract_owner_name', $formData),
        'contract_owner_email' => renewPostValue('contract_owner_email', $formData),
        'contract_owner_number' => renewPostValue('contract_owner_number', $formData),
        'party_id' => renewPostValue('party_id', $formData),
        'department_id' => renewPostValue('department_id', $formData),
        'type_of_contract_id' => renewPostValue('type_of_contract_id', $formData),
        'start_date' => $startYmd,
        'end_date' => $endYmd,
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
        'description_id' => renewPostValue('description_id', $formData),
        'is_oem' => renewPostValue('is_oem', $formData, 'No'),
        'oem_registration_number' => renewPostValue('oem_registration_number', $formData),
        'oem_distributor_name' => renewPostValue('oem_distributor_name', $formData),
        'oem_product_category' => renewPostValue('oem_product_category', $formData),
        'contract_services_id' => renewPostValue('contract_services_id', $formData),
        'contact_persons' => renewPostValue('contact_persons', $formData),
        'amc_payment_mode' => $_POST['amc_payment_mode'] ?? 'billing_cycle',
        'amc_billing_cycle' => $_POST['amc_billing_cycle'] ?? 'quarterly',
        'amc_billing_day' => $_POST['amc_billing_day'] ?? 1,
        'contract_id' => $contract_id,
        'Rid' => $contract_id ?? '',
        'version_id' => $versionId,
        'company_id' => $company_id,
        'module_id' => $module_id,
        'user_id' => $user_id
    ];

    $milestones = array();
    $paymentMode = $_POST['amc_payment_mode'] ?? 'billing_cycle';
    if ($paymentMode === 'billing_cycle') {
        $milestones = generateAmcBillingMilestones(
            $startYmd,
            $endYmd,
            $_POST['amc_billing_cycle'] ?? 'quarterly',
            $_POST['amc_billing_day'] ?? 1,
            $_POST['contract_amount'] ?? 0
        );
    } else {
        $milestones = collectMilestonesFromPost();
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
    global $company_id, $data_name, $user_id, $module_id, $notification, $insert_attributes, $contractRoleAccess;

    $saveContractId = trim((string) ($_POST['Rid'] ?? ''));
    if ($saveContractId == '' && !empty($contract_id)) {
        $saveContractId = (string) $contract_id;
    }
    if ($saveContractId != '') {
        $lockFormData = $formData;
        $lockAmcData = array();
        $lockApiResponse = json_decode(callAPI(API . "?mode=getcontractdetailsreact&method=get&rid={$saveContractId}"), true);
        if (!empty($lockApiResponse['editcontractsres'])) {
            $lockRow = is_object($lockApiResponse['editcontractsres']) ? (array) $lockApiResponse['editcontractsres'] : $lockApiResponse['editcontractsres'];
            $lockFormData = array_merge($lockFormData, $lockRow);
        }
        if (!empty($lockApiResponse['amccontractres'])) {
            $lockAmcData = $lockApiResponse['amccontractres'];
        }
        $lockState = getContractFormLockState($saveContractId, $lockFormData, $company_id, null, $lockAmcData);
        if (!empty($lockState['locked'])) {
            $notification = ['message' => $lockState['reason'] ?: 'This contract cannot be modified in its current workflow status.', 'type' => 'danger'];
            return;
        }
    }

    if (!validateUnifiedContractBasics($notification)) {
        return;
    }

    if (($_POST['is_amc_contract'] ?? 'N') == 'N' && isContractFieldMandatory('locations', $insert_attributes)) {
        $locValues = $_POST['locations'] ?? array();
        if (!is_array($locValues)) {
            $decodedLocations = json_decode((string) $locValues, true);
            $locValues = is_array($decodedLocations) ? $decodedLocations : array();
        }
        $hasLocation = false;
        if (count((array) $locValues) > 0) {
            foreach ($locValues as $loc) {
                if (!empty($loc) && $loc != '0') {
                    $hasLocation = true;
                    break;
                }
            }
        }
        if (!$hasLocation) {
            $notification = array('message' => 'Please select at least one location.', 'type' => 'danger');
            return;
        }
    }

    $contractDates = parseUnifiedContractDates($notification);
    if ($contractDates == false) {
        return;
    }
    $startYmd = $contractDates['start_date'];
    $endYmd = $contractDates['end_date'];

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

    $sanitizedFormData = ['contract_name' => $_POST['contract_name'] ?? '', 'transaction_no' => $_POST['transaction_no'] ?? '', 'invoice_number' => $_POST['invoice_number'] ?? '', 'rt_number' => $_POST['rt_number'] ?? '', 'business_code' => $_POST['business_code'] ?? '', 'parent_contract_id' => $_POST['parent_contract_id'] ?? '', 'contract_payment_terms' => $_POST['contract_payment_terms'] ?? '', 'locations' => $_POST['locations'] ?? '', 'typeofaggrements' => $_POST['typeofaggrements'] ?? '', 'party_type' => $_POST['party_type'] ?? '', 'contract_owner_name' => $_POST['contract_owner_name'] ?? '', 'contract_owner_email' => $_POST['contract_owner_email'] ?? '', 'contract_owner_number' => $_POST['contract_owner_number'] ?? '', 'party_id' => $_POST['party_id'] ?? '', 'department_id' => $_POST['department_id'] ?? '', 'type_of_contract_id' => $_POST['type_of_contract_id'] ?? '', 'start_date' => $startYmd, 'end_date' => $endYmd, 'renewal_frequency' => $_POST['renewal_frequency'] ?? 'quarterly', 'contract_amount' => $_POST['contract_amount'] ?? '', 'description' => $_POST['description'] ?? '', 'additional_notes' => $_POST['additional_notes'] ?? '', 'payment_term_id' => $_POST['payment_term_id'] ?? '', 'termination_condition' => $_POST['termination_condition'] ?? '', 'contract_status' => $_POST['contract_status'] ?? 'Pending', 'auto_renew' => isset($_POST['auto_renew']) ? true : false, 'renew_type' => $_POST['renew_type'] ?? '', 'state' => $_POST['state'] ?? '', 'isp' => $_POST['isp'] ?? '', 'paid_by' => $_POST['paid_by'] ?? '', 'internet_charges' => $_POST['internet_charges'] ?? '', 'hub_spoc_name' => $_POST['hub_spoc_name'] ?? '', 'hub_spoc_number' => $_POST['hub_spoc_number'] ?? '', 'wifi_provider' => $_POST['wifi_provider'] ?? '', 'wifi_pass' => $_POST['wifi_pass'] ?? '', 'additional_document' => $additionalDocumentFileName, 'description_id' => $_POST['description_id'] ?? '', 'is_oem' => $_POST['is_oem'] ?? 'No', 'oem_registration_number' => $_POST['oem_registration_number'] ?? '', 'oem_distributor_name' => $_POST['oem_distributor_name'] ?? '', 'oem_product_category' => $_POST['oem_product_category'] ?? '', 'is_amc_contract' => 'N', 'contract_services_id' => $_POST['contract_services_id'] ?? '', 'contact_persons' => $_POST['contact_persons'] ?? '', 'amc_payment_mode' => $_POST['amc_payment_mode'] ?? 'billing_cycle', 'amc_billing_cycle' => $_POST['amc_billing_cycle'] ?? 'quarterly', 'amc_billing_day' => $_POST['amc_billing_day'] ?? 1, 'Rid' => $saveContractId != '' ? $saveContractId : ($contract_id ?? ''), 'edit_option' => $_POST['edit_option'] ?? 'SaveUpdate'];

    $milestones = array();
    $paymentMode = $_POST['amc_payment_mode'] ?? 'billing_cycle';
    if ($paymentMode === 'billing_cycle') {
        $milestones = generateAmcBillingMilestones(
            $startYmd,
            $endYmd,
            $_POST['amc_billing_cycle'] ?? 'quarterly',
            $_POST['amc_billing_day'] ?? 1,
            $_POST['contract_amount'] ?? 0
        );
    } else {
        $milestones = collectMilestonesFromPost();
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

        // echo "<pre> test";
        // print_r($result);
        // echo "</pre>";

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

function normalizeBillingCycleValue($cycle)
{
    $cycle = trim((string) $cycle);
    if ($cycle == 'halfyearly') {
        return 'half_yearly';
    }
    return $cycle;
}

function generateAmcBillingMilestones($startYmd, $endYmd, $cycle, $billingDay, $totalAmount)
{
    $cycle = normalizeBillingCycleValue($cycle);
    $intervalMap = array('monthly' => 1, 'quarterly' => 3, 'half_yearly' => 6, 'yearly' => 12);
    $intervalMonths = $intervalMap[$cycle] ?? 3;
    $start = DateTime::createFromFormat('Y-m-d', $startYmd);
    $end = DateTime::createFromFormat('Y-m-d', $endYmd);
    if (!$start || !$end || $start > $end) {
        return array();
    }

    $billingDay = max(1, min(31, (int) $billingDay));
    $dates = array();
    $cursor = clone $start;
    $daysInMonth = (int) $cursor->format('t');
    $cursor->setDate((int) $cursor->format('Y'), (int) $cursor->format('m'), min($billingDay, $daysInMonth));
    if ($cursor < $start) {
        $cursor->modify('+' . $intervalMonths . ' months');
        $daysInMonth = (int) $cursor->format('t');
        $cursor->setDate((int) $cursor->format('Y'), (int) $cursor->format('m'), min($billingDay, $daysInMonth));
    }

    while ($cursor <= $end) {
        $dates[] = $cursor->format('Y-m-d');
        $cursor->modify('+' . $intervalMonths . ' months');
        $daysInMonth = (int) $cursor->format('t');
        $cursor->setDate((int) $cursor->format('Y'), (int) $cursor->format('m'), min($billingDay, $daysInMonth));
    }

    if (count($dates) === 0) {
        $dates[] = $start->format('Y-m-d');
    }

    $count = count($dates);
    $priceEach = $count > 0 ? round((float) $totalAmount / $count, 2) : 0;
    $labelMap = array('monthly' => 'Month', 'quarterly' => 'Quarter', 'half_yearly' => 'Half-Year', 'yearly' => 'Year');
    $periodPrefix = $labelMap[$cycle] ?? 'Period';
    $milestones = array();
    $runningTotal = 0;
    foreach ($dates as $i => $date) {
        $price = $priceEach;
        if ($i === $count - 1) {
            $price = round((float) $totalAmount - $runningTotal, 2);
        } else {
            $runningTotal += $price;
        }
        $milestones[] = array(
            'milestone_name' => $periodPrefix . ' ' . ($i + 1),
            'milestone_date' => $date,
            'milestone_price' => $price
        );
    }
    return $milestones;
}

function handleAmcContractSubmission($isRenewFlow = false)
{
    global $formData, $permissionDetails, $workflow, $documentDetails, $versionId, $contract_id;
    global $company_id, $data_name, $user_id, $module_id, $notification, $insert_attributes, $contractRoleAccess;

    if ($isRenewFlow) {
        $parentContractId = trim((string) ($contract_id ?? ''));
        if ($parentContractId == '') {
            $parentContractId = trim((string) ($_POST['Rid'] ?? ''));
        }
        if ($parentContractId == '') {
            $notification = ['message' => 'Invalid contract for renewal.', 'type' => 'danger'];
            return;
        }
    }

    $saveContractId = trim((string) ($_POST['Rid'] ?? ''));
    if ($saveContractId == '' && !empty($contract_id)) {
        $saveContractId = (string) $contract_id;
    }
    if ($saveContractId != '') {
        $lockFormData = $formData;
        $lockAmcData = array();
        $lockApiResponse = json_decode(callAPI(API . "?mode=getcontractdetailsreact&method=get&rid={$saveContractId}"), true);
        if (!empty($lockApiResponse['editcontractsres'])) {
            $lockRow = is_object($lockApiResponse['editcontractsres']) ? (array) $lockApiResponse['editcontractsres'] : $lockApiResponse['editcontractsres'];
            $lockFormData = array_merge($lockFormData, $lockRow);
        }
        if (!empty($lockApiResponse['amccontractres'])) {
            $lockAmcData = $lockApiResponse['amccontractres'];
        }
        $lockState = getContractFormLockState($saveContractId, $lockFormData, $company_id, null, $lockAmcData);
        if (!empty($lockState['locked'])) {
            $notification = ['message' => $lockState['reason'] ?: 'This contract cannot be modified in its current workflow status.', 'type' => 'danger'];
            return;
        }
    }
    if (!$isRenewFlow && $saveContractId == '' && empty($contractRoleAccess['can_amc'])) {
        $notification = array('message' => 'You are not allowed to create an AMC contract.', 'type' => 'danger');
        return;
    }

    if (!validateUnifiedContractBasics($notification)) {
        return;
    }

    $contractDates = parseUnifiedContractDates($notification);
    if ($contractDates == false) {
        return;
    }
    $startYmd = $contractDates['start_date'];
    $endYmd = $contractDates['end_date'];

    normalizeAmcPostData($startYmd, $endYmd);

    $amcLocations = $_POST['amc_loc'] ?? array();
    $hasLocation = false;
    if (is_array($amcLocations)) {
        foreach ($amcLocations as $locRow) {
            if (!empty($locRow['location_id'])) {
                $hasLocation = true;
                break;
            }
        }
    }
    if (!$hasLocation && isContractFieldMandatory('locations', $insert_attributes)) {
        $notification = ['message' => 'At least one AMC location is required.', 'type' => 'danger'];
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

                if (in_array($fileExt, $allowedTypes) && $_FILES['additional_document_file']['size'][$i] <= $maxSize) {
                    $newFileName = time() . "_amc_additional_" . $i . "_" . basename($fileName);
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

    $locationIds = array();
    if (isset($_POST['amc_loc']) && is_array($_POST['amc_loc'])) {
        foreach ($_POST['amc_loc'] as $locRow) {
            $lid = (int) ($locRow['location_id'] ?? 0);
            if ($lid > 0) {
                $locationIds[] = $lid;
            }
        }
    }

    $milestones = array();
    $paymentMode = $_POST['amc_payment_mode'] ?? 'billing_cycle';
    if ($paymentMode === 'billing_cycle') {
        $milestones = generateAmcBillingMilestones(
            $startYmd,
            $endYmd,
            $_POST['amc_billing_cycle'] ?? 'quarterly',
            $_POST['amc_billing_day'] ?? 1,
            $_POST['contract_amount'] ?? 0
        );
    } else {
        $milestones = collectMilestonesFromPost();
    }

    $editContractId = trim((string) ($_POST['Rid'] ?? ''));
    if ($editContractId === '' && !empty($contract_id)) {
        $editContractId = (string) $contract_id;
    }
    if ($isRenewFlow) {
        $editContractId = '';
    }

    $sanitizedFormData = array(
        'contract_name' => $_POST['contract_name'] ?? '',
        'transaction_no' => $_POST['transaction_no'] ?? '',
        'invoice_number' => $_POST['invoice_number'] ?? '',
        'rt_number' => $_POST['rt_number'] ?? '',
        'business_code' => $_POST['business_code'] ?? '',
        'parent_contract_id' => $_POST['parent_contract_id'] ?? '',
        'contract_payment_terms' => $_POST['contract_payment_terms'] ?? '',
        'locations' => $locationIds,
        'typeofaggrements' => $_POST['typeofaggrements'] ?? '',
        'party_type' => $_POST['party_type'] ?? '',
        'contract_owner_name' => $_POST['contract_owner_name'] ?? '',
        'contract_owner_email' => $_POST['contract_owner_email'] ?? '',
        'contract_owner_number' => $_POST['contract_owner_number'] ?? '',
        'party_id' => $_POST['party_id'] ?? '',
        'department_id' => $_POST['department_id'] ?? '',
        'type_of_contract_id' => $_POST['type_of_contract_id'] ?? '',
        'start_date' => $startYmd,
        'end_date' => $endYmd,
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
        'customer_connect_id' => $_POST['customer_connect_id'] ?? '',
        'amc_default_employee_id' => $_POST['amc_default_employee_id'] ?? '',
        'amc_service_mode' => $_POST['amc_service_mode'] ?? '',
        'Rid' => $editContractId,
        'edit_option' => $_POST['edit_option'] ?? 'SaveUpdate',
        'is_amc_contract' => 'Y',
        'amc_payment_mode' => $paymentMode,
        'amc_billing_cycle' => $_POST['amc_billing_cycle'] ?? '',
        'amc_billing_day' => $_POST['amc_billing_day'] ?? '',
        'gate_entry' => $_POST['gate_entry'] ?? '',
        'reminder_days' => $_POST['reminder_days'] ?? array(),
        'completion_req' => $_POST['completion_req'] ?? array(),
        'amc_loc' => $_POST['amc_loc'] ?? array(),
        'amc_asset' => $_POST['amc_asset'] ?? array(),
        'amc_escalation_config' => $_POST['amc_escalation_config'] ?? array(),
        'contract_services_id' => $_POST['contract_services_id'] ?? '',
        'contact_persons' => $_POST['contact_persons'] ?? '',
        'milestones' => $milestones,
        'permission_data' => $permissionDetails,
        'workflow_data' => $workflow,
        'document_data' => $documentDetails,
        'contract_data' => $formData,
        'version_id' => $versionId,
    );
    if ($editContractId != '' && trim((string) ($_POST['payment_term_id'] ?? '')) == '') {
        unset($sanitizedFormData['payment_term_id']);
    }

    try {
        $contractUrl = API . "?mode=amcinsertcontractsreact&method=post&user_id={$user_id}&company_id={$company_id}";
        $response = trim((string) file_post_contents($contractUrl, $sanitizedFormData, 'insert'));
        error_log("AMC API Response for " . $contractUrl . ": " . $response);
        $result = json_decode($response, true);
        echo "<pre>"; print_r($result); echo "</pre>";
        $jsonOk = is_array($result);
        if (!$jsonOk && preg_match('/\{.*\}/s', (string) $response, $jsonMatch)) {
            $result = json_decode($jsonMatch[0], true);
            $jsonOk = is_array($result);
        }
        if (!$jsonOk) {
            $result = array();
        }

        $contractResult = $result['contract_result'] ?? array();
        $amcResult = $result['amc_contract_result'] ?? array();
        $scheduleCount = (int) ($amcResult['schedule_count'] ?? 0);
        $assetCount = count((array) ($amcResult['assets'] ?? array()));
        $scheduleErrors = $amcResult['schedule_errors'] ?? array();
        $insertOk = (int) ($contractResult['LASTID'] ?? 0) > 0;
        $updateOk = $editContractId != ''
        && in_array(strtolower($contractResult['status'] ?? ''), array('sucess', 'success'));
        $amcSaveOk = !empty($amcResult['details'])
        && in_array($amcResult['details']['status'] ?? '', array('sucess', 'success'));
        $scheduleOk = $assetCount == 0 || $scheduleCount > 0;
        $saveOk = $insertOk || $updateOk || $amcSaveOk;
        if (!$saveOk && $editContractId != '' && preg_match('/"(sucess|success)"/i', $response)) {
            $saveOk = true;
        }
        if (!$saveOk && $editContractId != '' && count((array) ($amcResult['assets'] ?? array())) > 0) {
            $saveOk = true;
        }

        if ($saveOk) {
            $_SESSION['insert_msg'] = 3000;
            if ($assetCount > 0 && !$scheduleOk) {
                $warnMsg = 'Contract saved but service schedule was not generated. Check location/asset service dates.';
                if (count((array) $scheduleErrors) > 0) {
                    $warnMsg .= ' ' . implode(' ', array_slice((array) $scheduleErrors, 0, 2));
                }
                $_SESSION['amc_schedule_warning'] = $warnMsg;
            }
            if (!headers_sent()) {
                header("location:index.php?option=COM_LIST_CONTRACTS");
                exit;
            }
            echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=index.php?option=COM_LIST_CONTRACTS">'
            . '<script type="text/javascript">window.location.replace("index.php?option=COM_LIST_CONTRACTS");</script>'
            . '</head><body></body></html>';
            exit;
        }

        $failMsg = 'Failed to save AMC contract.';
        if (!$jsonOk && trim((string) $response) != '') {
            $failMsg = 'Unexpected API response. Please try again.';
            error_log('AMC API invalid JSON: ' . substr((string) $response, 0, 500));
        } elseif (trim((string) $response) == '') {
            $failMsg = 'Empty API response from server.';
        } else {
            if (!empty($amcResult['message'])) {
                $failMsg = $amcResult['message'];
            } elseif (!empty($contractResult['message'])) {
                $failMsg = $contractResult['message'];
            } elseif (!empty($_SESSION['error_message'][3001])) {
                $failMsg = $_SESSION['error_message'][3001];
            }
        }
        $notification = ['message' => $failMsg, 'type' => 'danger'];
    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while submitting the AMC contract!', 'type' => 'danger'];
    }
}

function handleAmcContractRenewal()
{
    global $formData, $contract_id, $versionId;
    global $company_id, $user_id, $module_id, $notification, $insert_attributes;

    if (empty($contract_id)) {
        $notification = ['message' => 'Contract id is required for renewal.', 'type' => 'danger'];
        return;
    }

    if (!fetchContractIsApproved($contract_id)) {
        $notification = ['message' => 'Contract must be approved before it can be renewed.', 'type' => 'danger'];
        return;
    }

    if (!validateUnifiedContractBasics($notification)) {
        return;
    }

    $renewPartyId = renewPostValue('party_id', $formData);
    $renewPartyType = renewPostValue('party_type', $formData);
    $isInhouseRenew = ($renewPartyType == 'inhouse') || (($_POST['amc_service_mode'] ?? '') == 'inhouse');
    if (!$isInhouseRenew && empty($renewPartyId)) {
        $notification = ['message' => 'Party is required.', 'type' => 'danger'];
        return;
    }

    $contractDates = parseUnifiedContractDates($notification);
    if ($contractDates == false) {
        return;
    }
    $startYmd = $contractDates['start_date'];
    $endYmd = $contractDates['end_date'];
    normalizeAmcPostData($startYmd, $endYmd, true);

    $amcLocations = $_POST['amc_loc'] ?? array();
    $hasLocation = false;
    if (count((array)$amcLocations) > 0) {
        foreach ($amcLocations as $locRow) {
            if (!empty($locRow['location_id'])) {
                $hasLocation = true;
                break;
            }
        }
    }
    if (!$hasLocation && isContractFieldMandatory('locations', $insert_attributes)) {
        $notification = ['message' => 'At least one AMC location is required.', 'type' => 'danger'];
        return;
    }

    $locationIds = array();
    if (is_array($amcLocations)) {
        foreach ($amcLocations as $locRow) {
            $lid = (int) ($locRow['location_id'] ?? 0);
            if ($lid > 0) {
                $locationIds[] = $lid;
            }
        }
    }

    $paymentMode = $_POST['amc_payment_mode'] ?? 'billing_cycle';
    $milestones = array();
    if ($paymentMode == 'billing_cycle') {
        $milestones = generateAmcBillingMilestones(
            $startYmd,
            $endYmd,
            $_POST['amc_billing_cycle'] ?? 'quarterly',
            $_POST['amc_billing_day'] ?? 1,
            $_POST['contract_amount'] ?? 0
        );
    } else {
        $milestones = collectMilestonesFromPost();
    }

    $renewalData = array(
        'contract_name' => renewPostValue('contract_name', $formData),
        'transaction_no' => renewPostValue('transaction_no', $formData),
        'invoice_number' => renewPostValue('invoice_number', $formData),
        'rt_number' => renewPostValue('rt_number', $formData),
        'business_code' => renewPostValue('business_code', $formData),
        'parent_contract_id' => renewPostValue('parent_contract_id', $formData),
        'contract_payment_terms' => renewPostValue('contract_payment_terms', $formData),
        'locations' => $locationIds,
        'typeofaggrements' => renewPostValue('typeofaggrements', $formData),
        'party_type' => renewPostValue('party_type', $formData),
        'contract_owner_name' => renewPostValue('contract_owner_name', $formData),
        'contract_owner_email' => renewPostValue('contract_owner_email', $formData),
        'contract_owner_number' => renewPostValue('contract_owner_number', $formData),
        'party_id' => $renewPartyId,
        'department_id' => renewPostValue('department_id', $formData),
        'type_of_contract_id' => renewPostValue('type_of_contract_id', $formData),
        'start_date' => $startYmd,
        'end_date' => $endYmd,
        'renewal_frequency' => $_POST['renewal_frequency'] ?? 'quarterly',
        'contract_amount' => renewPostValue('contract_amount', $formData),
        'description' => $_POST['description'] ?? '',
        'additional_notes' => $_POST['additional_notes'] ?? '',
        'payment_term_id' => $_POST['payment_term_id'] ?? '',
        'termination_condition' => $_POST['termination_condition'] ?? '',
        'contract_status' => $_POST['contract_status'] ?? 'Pending',
        'auto_renew' => false,
        'renew_type' => $_POST['renew_type'] ?? '',
        'state' => $_POST['state'] ?? '',
        'isp' => renewPostValue('isp', $formData),
        'paid_by' => renewPostValue('paid_by', $formData),
        'internet_charges' => renewPostValue('internet_charges', $formData),
        'hub_spoc_name' => renewPostValue('hub_spoc_name', $formData),
        'hub_spoc_number' => renewPostValue('hub_spoc_number', $formData),
        'wifi_provider' => renewPostValue('wifi_provider', $formData),
        'wifi_pass' => renewPostValue('wifi_pass', $formData),
        'additional_document' => $_POST['all_additional_documents'] ?? '',
        'description_id' => renewPostValue('description_id', $formData),
        'is_oem' => renewPostValue('is_oem', $formData, 'No'),
        'oem_registration_number' => renewPostValue('oem_registration_number', $formData),
        'oem_distributor_name' => renewPostValue('oem_distributor_name', $formData),
        'oem_product_category' => renewPostValue('oem_product_category', $formData),
        'customer_connect_id' => renewPostValue('customer_connect_id', $formData),
        'amc_default_employee_id' => renewPostValue('amc_default_employee_id', $formData),
        'amc_service_mode' => renewPostValue('amc_service_mode', $formData),
        'is_amc_contract' => 'Y',
        'amc_payment_mode' => $paymentMode,
        'amc_billing_cycle' => $_POST['amc_billing_cycle'] ?? '',
        'amc_billing_day' => $_POST['amc_billing_day'] ?? '',
        'gate_entry' => $_POST['gate_entry'] ?? '',
        'reminder_days' => $_POST['reminder_days'] ?? array(),
        'completion_req' => $_POST['completion_req'] ?? array(),
        'amc_loc' => $_POST['amc_loc'] ?? array(),
        'amc_asset' => $_POST['amc_asset'] ?? array(),
        'amc_escalation_config' => $_POST['amc_escalation_config'] ?? array(),
        'contract_services_id' => renewPostValue('contract_services_id', $formData),
        'contact_persons' => renewPostValue('contact_persons', $formData),
        'milestones' => $milestones,
        'Rid' => $contract_id,
        'version_id' => $versionId,
        'company_id' => $company_id,
        'module_id' => $module_id,
        'user_id' => $user_id,
    );

    try {
        $renewalUrl = API . "?mode=renewcontractreact&method=post&user_id={$user_id}&company_id={$company_id}";
        $response = file_post_contents($renewalUrl, $renewalData, 'renew');
        $result = json_decode($response, true);
        $jsonOk = is_array($result);
        if (!$jsonOk) {
            $result = array();
        }
        $renewId = (int) ($result['renew_id'] ?? 0);
        $amcResult = $result['amc_contract_result'] ?? array();
        $scheduleCount = (int) ($amcResult['schedule_count'] ?? 0);
        $assetCount = count((array) ($amcResult['assets'] ?? array()));
        $scheduleErrors = $amcResult['schedule_errors'] ?? array();
        $scheduleOk = $assetCount == 0 || $scheduleCount > 0;

        if ($renewId > 0) {
            $_SESSION['insert_msg'] = 3000;
            if ($assetCount > 0 && !$scheduleOk) {
                $warnMsg = 'Contract renewed but service schedule was not generated. Check location/asset service dates.';
                if (count((array) $scheduleErrors) > 0) {
                    $warnMsg .= ' ' . implode(' ', array_slice((array) $scheduleErrors, 0, 2));
                }
                $_SESSION['amc_schedule_warning'] = $warnMsg;
            }
            if (!headers_sent()) {
                header("location:index.php?option=COM_LIST_CONTRACTS");
                exit;
            }
            ?>
            <script type="text/javascript">
                window.location.href = 'index.php?option=COM_LIST_CONTRACTS';
            </script>
            <?php
            exit;
        }

        $failMsg = 'An error occurred while renewing the AMC contract!';
        if (!$jsonOk && trim((string) $response) != '') {
            $failMsg = 'Unexpected API response during renewal. Please try again.';
            error_log('AMC renewal API invalid JSON: ' . substr((string) $response, 0, 500));
        } elseif (trim((string) $response) == '') {
            $failMsg = 'Empty API response from server during renewal.';
        } elseif (!empty($amcResult['message'])) {
            $failMsg = $amcResult['message'];
        }
        $notification = ['message' => $failMsg, 'type' => 'danger'];
    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while renewing the AMC contract!', 'type' => 'danger'];
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
$formdisable = '';
$workflowsteps = getWorkflowStatus('contracts', $contract_id, $formData['current_status']);
if ($is_renew_mode) {
    $contractLockState = array('locked' => false, 'formdisable' => '', 'reason' => '');
} else {
    $contractLockState = getContractFormLockState(
        $contract_id,
        $formData,
        $company_id,
        null,
        $amc_contract_data
    );
}
$formdisable = $contractLockState['formdisable'];
$contract_form_locked = !empty($contractLockState['locked']);
?>

<?php
$amc_party_type = $_GET['party_type'] ?? ($formData['party_type'] ?? 'vendor');
$amc_party_id = $_GET['party_id'] ?? ($formData['party_id'] ?? '');

// $amc_is_edit = !empty($is_edit_mode);
// $amc_is_renew_mode = !empty($is_renew_mode) && $amc_is_edit;
// $amc_is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';
$amc_is_edit = !empty($is_edit_mode);
$amc_is_renew_mode = !empty($is_renew_mode) && $amc_is_edit;
$amc_is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';

$show_convert_button = $is_edit_mode && !$is_renew_mode && empty($contract_form_locked) && !$amc_is_cancelled;
$convert_target = $is_amc_mode ? 'standard' : 'amc';
$convert_button_label = $is_amc_mode ? 'Convert to Standard' : 'Convert to AMC';
$convert_confirm_msg = $is_amc_mode
    ? 'Convert this AMC contract to Standard? AMC locations, assets and schedules will be removed from active use on Save.'
    : 'Convert this Standard contract to AMC? Header data will be kept. Fill AMC locations/assets before Save.';

$convert_rid_param = isset($_GET['Rid']) ? (string) $_GET['Rid'] : '';
$convert_redirect_url = 'index.php?option=COM_INSERT_CONTRACTS&Rid=' . rawurlencode($convert_rid_param) . '&convert_to=' . $convert_target;

$amc_renew_start_display = '';
$amc_renew_end_display = '';
if ($amc_is_renew_mode && !empty($formData['end_date'])) {
    $amc_old_end = DateTime::createFromFormat('Y-m-d', substr((string) $formData['end_date'], 0, 10));
    if ($amc_old_end) {
        $amc_new_start = clone $amc_old_end;
        $amc_new_start->modify('+1 day');
        $amc_new_end = clone $amc_new_start;
        $amc_new_end->modify('+1 year');
        $amc_renew_start_display = $amc_new_start->format('d-m-Y');
        $amc_renew_end_display = $amc_new_end->format('d-m-Y');
    }
}
$renewalAmountHistoryData = array('periods' => array(), 'new_period_start' => '', 'new_period_end' => '');
if ($is_renew_mode && !empty($contract_id)) {
    $renewalAmountHistoryData['periods'] = buildRenewalAmountHistoryChain($contract_id, $contracts);
    $renewalAmountHistoryData['new_period_start'] = $amc_renew_start_display;
    $renewalAmountHistoryData['new_period_end'] = $amc_renew_end_display;
}
$amc_renew_readonly = $amc_is_renew_mode ? 'readonly' : '';
$amc_renew_disabled = $amc_is_renew_mode ? 'disabled' : '';
$amc_details = $amc_contract_data['details'] ?? array();
$amc_saved_reminder_days = $amc_details['reminder_days'] ?? array('15');
$amc_saved_completion_rules = $amc_details['completion_rules'] ?? array('service_report', 'technician_signoff', 'customer_signoff');
$amc_saved_gate_entry = $amc_details['gate_entry'] ?? 'yes';
$amc_saved_payment_mode = $amc_details['payment_mode'] ?? 'billing_cycle';
$amc_saved_billing_cycle = normalizeBillingCycleValue($amc_details['billing_cycle'] ?? 'quarterly');
$amc_saved_billing_day = $amc_details['billing_day'] ?? 1;
$amc_escalations = $amc_details['escalation_config'] ?? array();
if (empty($amc_escalations)) {
    $amc_escalations = array(
        array('condition' => 'not_started', 'hours' => 4, 'notify_role' => 'service_manager'),
        array('condition' => 'not_completed', 'hours' => 24, 'notify_role' => 'regional_manager'),
    );
}
$amc_notify_roles = array(
    'service_manager' => 'Service Manager',
    'regional_manager' => 'Regional Manager',
    'contract_owner' => 'Contract Owner',
);
$amc_condition_labels = array(
    'not_started' => 'If service not started within',
    'not_completed' => 'If service not completed within',
);
$amc_service_mode = $amc_details['service_mode'] ?? 'vendor';
$amc_default_employee_id = $amc_details['default_employee_id'] ?? '';
$amc_customer_connect_id = $amc_details['customer_connect_id'] ?? '';
$amc_is_inhouse = ($amc_service_mode == 'inhouse');
if ($amc_is_inhouse && empty($formData['department_id']) && !empty($amc_details['department_id'])) {
    $formData['department_id'] = $amc_details['department_id'];
}
if ($amc_is_inhouse) {
    $amc_party_type = 'inhouse';
}
$selectedLocations = array();
if (!empty($formData['locations'])) {
    $selectedLocations = json_decode($formData['locations'], true);
    if (!is_array($selectedLocations)) {
        $selectedLocations = array();
    }
}
$milestoneCount = 0;
$billingScheduleTotal = 0;
$billingScheduleServerPrefilled = ($amc_saved_payment_mode !== 'milestones' && count((array)$milestones) > 0);

if (count((array)$milestones) > 0) {
    foreach ($milestones as $m) {
        $milestoneCount++;
        if ($billingScheduleServerPrefilled) {
            $mRow = is_array($m) ? $m : json_decode(json_encode($m), true);
            $billingScheduleTotal += (float) ($mRow['milestone_price'] ?? 0);
        }
    }
} else {
    $milestoneCount = 1;
}
?><div class="card<?= $show_contract_type_toggle ? ' unified-contract-create-card' : '' ?>">
    <?php if (!empty($contract_id)) { echo $workflowsteps['current_status_ribbon']; } ?>
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
        <?php if ($show_contract_type_toggle && empty($contract_form_locked) && !$amc_is_cancelled): ?>
            <!-- <button type="button" class="btn btn-primary btn-sm cc-btn-submit desktop-view ms-1">
                <i class="icofont icofont-check"></i> Submit Contract
            </button> -->
        <?php endif; ?>
        <?php
        $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';
        $is_renewed = !empty($formData['renew_data']);
        if (!empty($contract_id) && !$is_cancelled && $contract_is_approved):
            ?>
            <?php if (!$is_renewed && !$is_renew_mode): ?>
                <button type="button" class="btn btn-primary mb-2 desktop-view renew-contract-btn" onclick="renewContractBTN()">
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
    <?php if (!empty($contract_id)): ?>
        <input type="hidden" id="contractId" value="<?= htmlspecialchars($contract_id) ?>">
    <?php endif; ?>
    <?php if (!empty($contract_id)): ?>
        <div class="row">
            <div class="col-lg-6">
                <?php echo $workflowsteps['breadcrumb'];?>
            </div>
            <div class="col-lg-6 text-right">
                <?php if (empty($formData['cancel_status']) || $formData['cancel_status'] != 'Y') {
                    echo $workflowsteps['workflow_btn'];
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($contractLockState['reason']) && $contract_form_locked): ?>
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="alert alert-warning mb-0" role="alert">
                    <?= htmlspecialchars($contractLockState['reason']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y'): ?>
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="alert alert-premium-danger d-flex align-items-center p-0 border-0 shadow-sm mb-0 overflow-hidden"
                style="border-radius: 12px; background: linear-gradient(135deg, #fff5f5 0%, #fff 100%); border-left: 5px solid #dc3545 !important;">
                <div class="bg-danger px-4 py-4 d-flex align-items-center justify-content-center text-white" style="min-width: 80px;">
                    <i class="fa fa-ban fa-2x"></i>
                </div>
                <div class="flex-grow-1 px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="mb-0 text-danger fw-bold uppercase tracking-wider" style="letter-spacing: 1px;">Contract Cancelled</h5>
                        <span class="badge bg-danger rounded-pill px-3 py-2">Inactive</span>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <p class="mb-0 text-muted small"><strong class="text-dark">Reason:</strong>
                                <span class="ms-1"><?= htmlspecialchars($formData['cancel_reason'] ?? 'Not specified') ?></span></p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <p class="mb-0 text-muted small"><strong class="text-dark">Date:</strong>
                                    <span class="ms-1"><?= !empty($formData['cancel_date']) ? fromDate($formData['cancel_date']) : 'N/A' ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($show_contract_type_toggle): ?>
            <div class="row mb-0 contract-type-toggle-row">
                <div class="col-lg-12">
                    <label class="form-label d-block mb-2 fw-bold">Contract Type</label>
                    <div class="contract-type-icon-grid" role="radiogroup" aria-label="Contract Type">
                        <div class="contract-type-icon-card">
                            <input type="radio" name="page_contract_type" id="page_contract_type_standard" value="standard" class="contract-type-icon-input" aria-label="Standard Contract" <?= !$is_amc_mode ? 'checked' : '' ?>>
                            <label class="contract-type-icon-label" for="page_contract_type_standard" title="Standard Contract">
                                <span class="contract-type-icon-wrap contract-type-icon-standard">
                                    <i class="icofont icofont-law-document" aria-hidden="true"></i>
                                </span>
                                <span class="contract-type-icon-name">Standard</span>
                            </label>
                        </div>
                        <div class="contract-type-icon-card">
                            <input type="radio" name="page_contract_type" id="page_contract_type_amc" value="amc" class="contract-type-icon-input" aria-label="AMC Contract" <?= $is_amc_mode ? 'checked' : '' ?>>
                            <label class="contract-type-icon-label" for="page_contract_type_amc" title="AMC Contract">
                                <span class="contract-type-icon-wrap contract-type-icon-amc">
                                    <i class="icofont icofont-repair" aria-hidden="true"></i>
                                </span>
                                <span class="contract-type-icon-name">AMC</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($is_edit_mode && !$is_renew_mode): ?>
            <div class="row mb-2">
                <div class="col-lg-12">
                    <ul class="nav md-tabs infisuite_tab mb-2 unified-edit-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#contractdetails">Contract Details</a></li>
                        <?php if (in_array('document', $userPermissions)): ?><li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#documents">Documents</a></li><?php endif; ?>
                        <?php if (in_array('associated', $userPermissions)): ?><li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#associatedcontract">Associated Contract</a></li><?php endif; ?>
                        <?php /*
                        if (in_array('permission', $userPermissions)): ?><li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#permissions">Permissions</a></li><?php endif; */
                        ?>
                        <?php if (in_array('activity', $userPermissions)): ?><li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#activities">Activities</a></li><?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="tab-content unified-edit-tab-content">
                <div id="contractdetails" class="tab-pane active">
                <?php endif; ?>
                <div id="unified-contract-page" class="<?= htmlspecialchars($formdisable) ?>">
                    <div class="page-body cc-wizard-page">
                    <form id="amcContractForm" method="post" enctype="multipart/form-data" action="">
                        <input type="hidden" name="action" id="unified_contract_action" value="<?= $amc_is_renew_mode ? 'renew_amc_contract' : ($is_amc_mode ? 'submit_amc_contract' : 'submit_contract') ?>">
                        <input type="hidden" name="is_amc_contract" id="unified_is_amc_contract" value="<?= $is_amc_mode ? 'Y' : 'N' ?>">
                        <input type="hidden" name="edit_option" id="unified_edit_option" value="SaveUpdate">
                        <input type="hidden" name="amc_service_mode" id="amc_service_mode" value="<?= htmlspecialchars($amc_service_mode) ?>">
                        <input type="hidden" name="renewal_frequency" value="<?= htmlspecialchars($formData['renewal_frequency'] ?? 'quarterly') ?>">
                        <input type="hidden" name="contract_status" value="<?= htmlspecialchars($formData['contract_status'] ?? 'Pending') ?>">
                        <input type="hidden" name="state" value="<?= htmlspecialchars($formData['state'] ?? '') ?>">

                        <?php if (!empty($contract_id)): ?>
                            <input type="hidden" name="Rid" value="<?= htmlspecialchars($contract_id) ?>">
                        <?php endif; ?>

                        <div class="cc-wizard-header">
                            <div class="d-flex align-items-center gap-3">
                                <h5 class="mb-0"><?= $is_edit_mode ? 'Edit Contract' : 'Create Contract' ?></h5>
                            </div>
                            <div class="cc-wizard-header-actions">
                                <?php if ($show_convert_button && $convert_rid_param != ''): ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-convert-contract-type"
                                        data-convert-url="<?= htmlspecialchars($convert_redirect_url) ?>"
                                        data-convert-confirm="<?= htmlspecialchars($convert_confirm_msg) ?>">
                                        <i class="icofont icofont-exchange"></i> <?= htmlspecialchars($convert_button_label) ?>
                                    </button>

                                    <script type="text/javascript">
                                        (function () {
                                            var btn = document.getElementById('btn-convert-contract-type');
                                            if (!btn) { return; }
                                            btn.addEventListener('click', function () {
                                                var msg = btn.getAttribute('data-convert-confirm') || 'Convert contract type?';
                                                var url = btn.getAttribute('data-convert-url') || '';
                                                if (url == '') { return; }
                                                swal({
                                                    title: "Convert Contract Type",
                                                    text: msg,
                                                    type: "warning",
                                                    showCancelButton: true,
                                                    confirmButtonClass: "btn-danger",
                                                    confirmButtonText: "Yes",
                                                    cancelButtonText: "No",
                                                    closeOnConfirm: true
                                                }, function (isConfirm) {
                                                    if (isConfirm) {
                                                        window.location.href = url;
                                                    }
                                                });
                                            });
                                        })();
                                    </script>
                                    
                                <?php endif; ?>

                                <?php if (empty($contract_form_locked) && !$amc_is_cancelled): ?>
                                    <?php if ($amc_is_renew_mode): ?>
                                        <button type="button" class="btn btn-primary btn-sm cc-btn-submit">
                                            <i class="fa fa-refresh"></i> Renew Contract
                                        </button>
                                    <?php else: ?>
                                       <!--  <button type="button" class="btn btn-primary btn-sm cc-btn-submit">
                                            <i class="icofont icofont-check"></i> Submit Contract
                                        </button> -->
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($notification['message'])): ?>
                            <div class="alert alert-<?= htmlspecialchars($notification['type'] ?: 'info') ?> alert-dismissible fade show mx-0 mt-2 mb-0" role="alert">
                                <strong><?= htmlspecialchars($notification['message']) ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="cc-wizard-layout">
                            <aside class="cc-wizard-sidebar">
                                <div class="cc-wizard-steps-list">
                                    <a href="#" class="cc-wizard-step active" data-step="0">
                                        <span class="cc-step-number">1</span>
                                        <div class="cc-step-info">
                                            <div class="cc-step-title">Contract Details</div>
                                            <div class="cc-step-desc">Basic contract information</div>
                                        </div>
                                    </a>
                                    <a href="#" class="cc-wizard-step cc-amc-only" data-step="1" data-wizard-mode="amc">
                                        <span class="cc-step-number">2</span>
                                        <div class="cc-step-info">
                                            <div class="cc-step-title">Locations &amp; Assets</div>
                                            <div class="cc-step-desc">Locations, assets &amp; service periods</div>
                                        </div>
                                    </a>
                                    <a href="#" class="cc-wizard-step" data-step="2">
                                        <span class="cc-step-number">3</span>
                                        <div class="cc-step-info">
                                            <div class="cc-step-title">Commercials</div>
                                            <div class="cc-step-desc">Contract amount &amp; billing</div>
                                        </div>
                                    </a>
                                    <a href="#" class="cc-wizard-step cc-amc-only" data-step="3" data-wizard-mode="amc">
                                        <span class="cc-step-number">4</span>
                                        <div class="cc-step-info">
                                            <div class="cc-step-title">Service Setup</div>
                                            <div class="cc-step-desc">Reminders &amp; service rules</div>
                                        </div>
                                    </a>
                                    <a href="#" class="cc-wizard-step cc-amc-only" data-step="4" data-wizard-mode="amc">
                                        <span class="cc-step-number">5</span>
                                        <div class="cc-step-info">
                                            <div class="cc-step-title">Escalations</div>
                                            <div class="cc-step-desc">Escalation configuration</div>
                                        </div>
                                    </a>
                                    <a href="#" class="cc-wizard-step cc-amc-only" data-step="5" data-wizard-mode="amc">
                                        <span class="cc-step-number">6</span>
                                        <div class="cc-step-info">
                                            <div class="cc-step-title">Gate Entry &amp; Service Completion</div>
                                            <div class="cc-step-desc">Entry &amp; exit requirements</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="cc-sidebar-box help-box">
                                    <h6><i class="icofont icofont-question-circle"></i> Need Help?</h6>
                                    <p class="mb-0"><?= $amc_is_renew_mode ? 'Review renewal dates and amount, then click Renew Contract to create the new AMC period and schedules.' : 'Complete all wizard sections, then use Submit Contract to save and generate the service schedule.' ?></p>
                                </div>
                                <div class="cc-sidebar-box amc-box cc-sidebar-amc-box cc-amc-only">
                                    <h6><i class="icofont icofont-file-document"></i> AMC Contract</h6>
                                    <p class="mb-0">Location-based asset mapping, service schedules, and escalation rules.</p>
                                </div>
                            </aside>

                            <div class="cc-wizard-main">
                                <div class="cc-section" id="cc-step-1" data-step="0">
                                    <div class="cc-section-header">
                                        <h6><span class="cc-section-num">1</span><i class="icofont icofont-file-document"></i> Contract Details</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4" style="<?= $fieldDisplay['parent_contract_id']['style'] ?>">
                                            <label class="form-label">Parent/Associated Contract <?= $fieldDisplay['parent_contract_id']['star'] ?></label>
                                            <select class="form-control js-example-basic-single" name="parent_contract_id" <?= $fieldDisplay['parent_contract_id']['required'] ?>>
                                                <option value="0">Select Associated Contract</option>
                                                <?php
                                                $parentContractOptions = (array)($contracts ?? []);
                                                $selectedParentContractId = $formData['parent_contract_id'] ?? '';
                                                if ($selectedParentContractId == '' && !empty($formData['parent_contract'])) {
                                                    $selectedParentContractId = $formData['parent_contract'];
                                                }
                                                $excludeContractId = (int)($contract_id ?? $formData['id'] ?? 0);
                                                if (count((array)$parentContractOptions) > 0) {
                                                    foreach ($parentContractOptions as $contractRow) {
                                                        $cRow = is_array($contractRow) ? $contractRow : json_decode(json_encode($contractRow), true);
                                                        $optionId = (int)($cRow['id'] ?? 0);
                                                        if ($optionId <= 0 || ($excludeContractId > 0 && $optionId == $excludeContractId)) {
                                                            continue;
                                                        }
                                                        $sel = ((string)$selectedParentContractId === (string)$optionId) ? 'selected' : '';
                                                        $optionLabel = trim(($cRow['contract_name'] ?? '') . ' (' . fromDate($cRow['start_date'] ?? '') . ' To ' . fromDate($cRow['end_date'] ?? '') . ') -' . ($cRow['contract_type'] ?? ''));
                                                        ?>
                                                        <option value="<?= htmlspecialchars((string)$optionId) ?>" <?= $sel ?>><?= htmlspecialchars($optionLabel) ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Contract Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="contract_name" placeholder="Enter contract name" value="<?= htmlspecialchars($formData['contract_name'] ?? '') ?>" required <?= $amc_renew_readonly ?>>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['transaction_no']['style'] ?>">
                                            <label class="form-label">Contract Number <?= $fieldDisplay['transaction_no']['star'] ?></label>
                                            <input type="text" class="form-control" name="transaction_no" placeholder="Auto-generated" value="<?= htmlspecialchars($formData['transaction_no'] ?? '') ?>" readonly>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['rt_number']['style'] ?>">
                                            <label class="form-label">RT Number <?= $fieldDisplay['rt_number']['star'] ?></label>
                                            <input type="text" class="form-control" name="rt_number" placeholder="Enter RT Number" value="<?= htmlspecialchars($formData['rt_number'] ?? '') ?>" <?= $fieldDisplay['rt_number']['required'] ?> <?= $amc_renew_readonly ?? '' ?>>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['invoice_number']['style'] ?>">
                                            <label class="form-label">Invoice Number <?= $fieldDisplay['invoice_number']['star'] ?></label>
                                            <input type="text" class="form-control" name="invoice_number" placeholder="Enter Invoice Number" value="<?= htmlspecialchars($formData['invoice_number'] ?? '') ?>" <?= $fieldDisplay['invoice_number']['required'] ?>>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['business_code']['style'] ?>">
                                            <label class="form-label">Business Code <?= $fieldDisplay['business_code']['star'] ?></label>
                                            <input type="text" class="form-control" name="business_code" placeholder="Enter Business Code" value="<?= htmlspecialchars($formData['business_code'] ?? '') ?>" <?= $fieldDisplay['business_code']['required'] ?>>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Department <span class="text-danger">*</span></label>
                                            <?php if ($amc_is_renew_mode && !empty($formData['department_id'])) { ?>
                                                <input type="hidden" name="department_id" value="<?= htmlspecialchars($formData['department_id']) ?>">
                                            <?php } ?>
                                            <select class="form-control js-example-basic-single" name="<?= $amc_is_renew_mode ? 'department_id_display' : 'department_id' ?>" id="amc_contract_department_id" <?= $amc_renew_disabled ?>>
                                                <option value="">Select Department</option>
                                                <?php if (count((array)$departments) > 0) {
                                                    foreach ($departments as $dept) { ?>
                                                        <option value="<?= $dept['id'] ?>" <?= (isset($formData['department_id']) && (string) $formData['department_id'] == (string) $dept['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['department_name']) ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['type_of_contract_id']['style'] ?>">
                                            <label class="form-label">Contract Category <?= $fieldDisplay['type_of_contract_id']['star'] ?></label>
                                            <select class="form-control js-example-basic-single" name="type_of_contract_id" <?= $fieldDisplay['type_of_contract_id']['required'] ?> <?= $amc_renew_disabled ?>>
                                                <option value="">Select Category</option>
                                                <?php if (count((array)$contractTypes) > 0) {
                                                    foreach ($contractTypes as $type) { ?>
                                                        <option value="<?= htmlspecialchars($type['genc_detail_code']) ?>" <?= (isset($formData['type_of_contract_id']) && (string) $formData['type_of_contract_id'] == (string) ($type['genc_detail_code'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($type['genc_detail_name']) ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['contract_payment_terms']['style'] ?>">
                                            <label class="form-label">Payment Terms <?= $fieldDisplay['contract_payment_terms']['star'] ?></label>
                                            <select class="form-control js-example-basic-single" name="contract_payment_terms" <?= $fieldDisplay['contract_payment_terms']['required'] ?> <?= $amc_renew_disabled ?>>
                                                <option value="">Select Payment Terms</option>
                                                <?php if (count((array)$payment_terms) > 0) {
                                                    foreach ($payment_terms as $term) {
                                                        $t = is_array($term) ? $term : json_decode(json_encode($term), true); ?>
                                                        <option value="<?= htmlspecialchars($t['id'] ?? '') ?>" <?= (isset($formData['contract_payment_terms']) && (string) $formData['contract_payment_terms'] == (string) ($t['id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($t['payment_term_name'] ?? '') ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                        </div>


                                        <?php $contract_agreement_section = 'classification'; include __DIR__ . '/_contract_agreement_detail_fields.php'; ?>
                                        <?php $contract_services_contact_section = 'services'; include __DIR__ . '/_contract_services_contact_fields.php'; ?>
                                        <div class="col-md-12">
                                            <label class="form-label d-block">Party Type <span class="text-danger">*</span></label>
                                            <div class="radio radio-outline radio-inline">
                                                <input id="amc_customer" type="radio" <?= $amc_is_renew_mode ? '' : 'name="party_type"' ?> value="customer" <?= $amc_party_type == 'customer' ? 'checked' : '' ?> <?= $amc_renew_disabled ?>>
                                                <label class="form-label align-top" for="amc_customer">Customer</label>
                                            </div>
                                            <div class="radio radio-outline radio-inline ms-3">
                                                <input id="amc_vendor" type="radio" <?= $amc_is_renew_mode ? '' : 'name="party_type"' ?> value="vendor" <?= ($amc_party_type != 'customer' && $amc_party_type != 'inhouse') ? 'checked' : '' ?> <?= $amc_renew_disabled ?>>
                                                <label class="form-label align-top" for="amc_vendor">Vendor</label>
                                            </div>
                                            <div class="radio radio-outline radio-inline ms-3 cc-amc-only">
                                                <input id="amc_inhouse" type="radio" <?= $amc_is_renew_mode ? '' : 'name="party_type"' ?> value="inhouse" <?= $amc_party_type == 'inhouse' ? 'checked' : '' ?> <?= $amc_renew_disabled ?>>
                                                <label class="form-label align-top" for="amc_inhouse">In-house</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="amc_party_name_box" style="<?= $amc_is_inhouse ? 'display:none;' : '' ?>">
                                            <label class="form-label">Party Name <span class="text-danger amc-party-required">*</span></label>
                                            <?php if ($amc_is_renew_mode) { ?>
                                                <input type="hidden" name="party_type" value="<?= htmlspecialchars($amc_party_type) ?>">
                                                <input type="hidden" name="party_id" value="<?= htmlspecialchars($amc_party_id != '' ? $amc_party_id : '0') ?>">
                                                <?php if ($amc_is_inhouse) { ?>
                                                    <input type="hidden" name="amc_service_mode" value="inhouse">
                                                <?php } ?>
                                            <?php } ?>
                                            <select class="form-control js-example-basic-single" name="<?= $amc_is_renew_mode ? 'party_id_display' : 'party_id' ?>" id="amc_party_id" data-selected="<?= htmlspecialchars($amc_party_id) ?>" <?= $amc_renew_disabled ?>>
                                                <option value="">Select a Party</option>
                                                <?php if (count((array)$customers) > 0) {
                                                    foreach ($customers as $partyRow) {
                                                        $party = is_array($partyRow) ? $partyRow : json_decode(json_encode($partyRow), true);
                                                        $pid = $party['id'] ?? '';
                                                        $partyCompany = $party['company'] ?? 'N/A';
                                                        $partyDisplayName = $party['display_name'] ?? 'N/A';
                                                        $partyEmail = $party['email'] ?? 'N/A';
                                                        $partyPhone = $party['phone'] ?? '—';
                                                        $partyBpCode = $party['bp_code'] ?? '—';
                                                        $partyBilling = $party['billing_address'] ?? '—';
                                                        $partyShipping = $party['shipping_address'] ?? '—';
                                                        $label = trim($partyCompany . ' | ' . $partyDisplayName . ' | ' . $partyEmail);
                                                        $selected = ((string) $amc_party_id === (string) $pid) ? 'selected' : ''; ?>
                                                        <option value="<?= htmlspecialchars($pid) ?>" <?= $selected ?>
                                                        data-name="<?= htmlspecialchars($partyCompany) ?>"
                                                        data-email="<?= htmlspecialchars($partyEmail) ?>"
                                                        data-phone="<?= htmlspecialchars($partyPhone) ?>"
                                                        data-bp-code="<?= htmlspecialchars($partyBpCode) ?>"
                                                        data-billing-address="<?= htmlspecialchars($partyBilling) ?>"
                                                        data-shipping-address="<?= htmlspecialchars($partyShipping) ?>"><?= htmlspecialchars($label) ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                            <div id="party-details-preview" class="mt-2 mb-3 p-3 border rounded bg-light d-none" style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); border-left: 4px solid #4e73df !important;">
                                                <h6 class="mb-3 text-primary border-bottom pb-2" style="font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; font-weight: 700;">
                                                    <i class="fa fa-user-circle me-1"></i> Party Details
                                                </h6>
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 600;">Name</small>
                                                        <span id="preview-party-name" class="fw-bold text-dark small">—</span>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 600;">Email</small>
                                                        <span id="preview-party-email" class="fw-bold text-dark small">—</span>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 600;">Phone</small>
                                                        <span id="preview-party-phone" class="fw-bold text-dark small">—</span>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 600;">BP Code</small>
                                                        <span id="preview-bp-code" class="fw-bold text-dark small">—</span>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 600;">Billing Address</small>
                                                        <span id="preview-billing-address" class="fw-bold text-dark small">—</span>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <small class="text-muted d-block text-uppercase" style="font-size: 0.65rem; font-weight: 600;">Shipping Address</small>
                                                        <span id="preview-shipping-address" class="fw-bold text-dark small">—</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="unified-add-party-btn"><i class="fa fa-plus-circle me-1"></i> Add New Party</button>
                                        </div>
                                        <?php
                                        $amc_show_customer_connect = $is_amc_mode && $amc_party_type != 'customer' && $amc_party_type != 'inhouse';
                                        ?>
                                        <div class="col-md-4 align-self-start cc-amc-only" id="amc_customer_connect_box" style="<?= $amc_show_customer_connect ? '' : 'display:none;' ?>">
                                            <label class="form-label">Customer Connect (Facility Manager)</label>
                                            <select class="form-control js-example-basic-single" name="customer_connect_id" id="amc_customer_connect_id" data-selected="<?= htmlspecialchars($amc_customer_connect_id) ?>">
                                                <option value="">Select Customer Connect</option>
                                                <?php if (count((array)$users) > 0) {
                                                    foreach ($users as $connectRow) {
                                                        $connect = is_array($connectRow) ? $connectRow : json_decode(json_encode($connectRow), true);
                                                        $cid = $connect['id'] ?? '';
                                                        $clabel = trim($connect['display_name'] ?? $connect['user_name'] ?? ('Customer ' . $cid));
                                                        $cselected = ((string) $amc_customer_connect_id === (string) $cid) ? 'selected' : ''; ?>
                                                        <option value="<?= htmlspecialchars($cid) ?>" <?= $cselected ?>><?= htmlspecialchars($clabel) ?></option>
                                                    <?php }
                                                } ?>
                                            </select>
                                            <div class="form-text amc-field-help-text mb-1">Site contact who confirms vendor visits after job completion</div>
                                        </div>
                                        <div class="col-md-4 cc-amc-only" id="amc_default_employee_box" style="<?= $amc_is_inhouse ? '' : 'display:none;' ?>">
                                            <label class="form-label">Default Employee</label>
                                            <select class="form-control js-example-basic-single" name="amc_default_employee_id" id="amc_default_employee_id" data-selected="<?= htmlspecialchars($amc_default_employee_id) ?>">
                                                <option value="">Select Employee (optional)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['contract_owner_name']['style'] ?>">
                                            <label class="form-label">Contract Owner Name <?= $fieldDisplay['contract_owner_name']['star'] ?></label>
                                            <input type="text" class="form-control" name="contract_owner_name" placeholder="Enter contract owner name" value="<?= htmlspecialchars($formData['contract_owner_name'] ?? '') ?>" <?= $fieldDisplay['contract_owner_name']['required'] ?>>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['contract_owner_email']['style'] ?>">
                                            <label class="form-label">Contract Owner Email <?= $fieldDisplay['contract_owner_email']['star'] ?></label>
                                            <input type="email" class="form-control" name="contract_owner_email" placeholder="Enter contract owner email" value="<?= htmlspecialchars($formData['contract_owner_email'] ?? '') ?>" <?= $fieldDisplay['contract_owner_email']['required'] ?>>
                                        </div>
                                        <div class="col-md-4" style="<?= $fieldDisplay['contract_owner_number']['style'] ?>">
                                            <label class="form-label">Contract Owner Contact <?= $fieldDisplay['contract_owner_number']['star'] ?></label>
                                            <input type="tel" class="form-control" name="contract_owner_number" placeholder="Enter contract owner contact number" value="<?= htmlspecialchars($formData['contract_owner_number'] ?? '') ?>" <?= $fieldDisplay['contract_owner_number']['required'] ?>>
                                        </div>
                                        <?php $contract_services_contact_section = 'contact_persons'; include __DIR__ . '/_contract_services_contact_fields.php'; ?>
                                        <div class="col-md-4">
                                            <label class="form-label">Effective(Start) Date <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control startdatepicker_2 rangedatepicker " data-id="2" name="start_date" value="<?= $amc_renew_start_display != '' ? htmlspecialchars($amc_renew_start_display) : (!empty($formData['start_date']) ? fromDate($formData['start_date']) : '') ?>" autocomplete="off" <?= $amc_is_renew_mode ? 'readonly style="background-color:#f8f9fa;"' : '' ?>>

                                        </div>
                                         
                            
                                        <div class="col-md-4">
                                            <label class="form-label">Expiration(End) Date <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control amc-end-datepicker enddatepicker_2" name="end_date" value="<?= $amc_renew_end_display != '' ? htmlspecialchars($amc_renew_end_display) : (!empty($formData['end_date']) ? fromDate($formData['end_date']) : '') ?>" autocomplete="off">
                                        </div>
                                        <?php $contract_agreement_section = 'extended'; include __DIR__ . '/_contract_agreement_detail_fields.php'; ?>
                                        <div class="col-md-12" style="<?= $fieldDisplay['description']['style'] ?>">
                                            <label class="form-label">Contract Description <?= $fieldDisplay['description']['star'] ?></label>
                                            <textarea class="form-control" name="description" rows="3" placeholder="Enter contract description" <?= $fieldDisplay['description']['required'] ?>><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-md-12" style="<?= $fieldDisplay['additional_document']['style'] ?>">
                                            <label class="form-label">Contract Document <?= $fieldDisplay['additional_document']['star'] ?></label>
                                            <div class="cc-upload-zone">
                                                <i class="icofont icofont-cloud-upload"></i>
                                                <p>Click to upload or drag and drop</p>
                                                <small class="text-muted cc-upload-filename"></small>
                                            </div>
                                            <input type="file" id="amc_contract_document" name="additional_document_file[]" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                                            <div id="amc_additional_file_buttons" class="cc-uploaded-files mt-2 d-flex flex-column gap-2">
                                                <?php
                                                $amc_saved_documents = $formData['additional_document'] ?? '';
                                                if (!empty($amc_saved_documents)) {
                                                    $amc_document_files = explode(',', $amc_saved_documents);
                                                    if (count((array)$amc_document_files) > 0) {
                                                        foreach ($amc_document_files as $amc_doc_file) {
                                                            $amc_doc_file = trim($amc_doc_file);
                                                            if ($amc_doc_file == '') {
                                                                continue;
                                                            }
                                                            $amc_display_name = preg_replace('/^\d+_(amc_)?additional_\d+_/', '', $amc_doc_file);
                                                            if ($amc_display_name == '') {
                                                                $amc_display_name = $amc_doc_file;
                                                            }
                                                            ?>
                                                            <div class="cc-uploaded-file-item d-flex align-items-center justify-content-between gap-2" data-filename="<?= htmlspecialchars($amc_doc_file) ?>">
                                                                <span class="text-truncate" title="<?= htmlspecialchars($amc_display_name) ?>">
                                                                    <i class="icofont icofont-file-document"></i> <?= htmlspecialchars($amc_display_name) ?>
                                                                </span>
                                                                <div class="d-flex gap-1 flex-shrink-0">
                                                                    <a href="uploads/contracts/<?= htmlspecialchars($amc_doc_file) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info" title="View <?= htmlspecialchars($amc_display_name) ?>">
                                                                        <i class="fa fa-eye m-0"></i>
                                                                    </a>
                                                                    <a href="uploads/contracts/<?= htmlspecialchars($amc_doc_file) ?>" download class="btn btn-sm btn-outline-primary" title="Download <?= htmlspecialchars($amc_display_name) ?>">
                                                                        <i class="fa fa-download m-0"></i>
                                                                    </a>
                                                    <?php /* Remove disabled — re-enable when delete-on-save is implemented
                                                    <button type="button" class="btn btn-sm btn-outline-danger amc-remove-additional-document-btn" data-filename="<?= htmlspecialchars($amc_doc_file) ?>" title="Remove <?= htmlspecialchars($amc_display_name) ?>">
                                                        <i class="fa fa-trash m-0"></i>
                                                    </button>
                                                    */ ?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <input type="hidden" name="all_additional_documents" id="amc_all_additional_documents" value="<?= htmlspecialchars($amc_saved_documents) ?>">
                            <small class="text-muted d-block mt-1">PDF, Word, Excel, Images (Max 10MB each)</small>
                        </div>
                        <div class="col-md-12">
                            <div class="cc-amc-banner cc-amc-only">
                                <i class="icofont icofont-check-circled"></i> AMC Enabled — Location-based asset mapping and service scheduling are active.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cc-section cc-amc-sections cc-amc-only" id="cc-step-2" data-step="1">
                    <div class="cc-section-header">
                        <h6><span class="cc-section-num">2</span><i class="icofont icofont-location-pin"></i> Locations, Assets &amp; Service Periods</h6>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="amc-add-location-btn">
                            <i class="icofont icofont-plus"></i> Add Location
                        </button>
                    </div>
                    <div id="amc_locations_container"></div>
                </div>

                <div class="cc-section" id="cc-step-3" data-step="2">
                    <div class="cc-section-header">
                        <h6><span class="cc-section-num">3</span><i class="icofont icofont-money"></i> Commercials</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Contract Amount <span class="text-danger">*</span></label>
                            <input type="number" step="any" class="form-control text-end" name="contract_amount" id="amc_contract_amount" placeholder="Enter Contract Amount" value="<?= htmlspecialchars($formData['contract_amount'] ?? '') ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label d-block">Payment Plan</label>
                            <div class="cc-inline-radios">
                                <label><input type="radio" name="amc_payment_mode" value="billing_cycle" <?= $amc_saved_payment_mode !== 'milestones' ? 'checked' : '' ?>> Billing Cycle</label>
                                <label><input type="radio" name="amc_payment_mode" value="milestones" <?= $amc_saved_payment_mode === 'milestones' ? 'checked' : '' ?>> Milestones</label>
                            </div>
                        </div>
                        <?php if ($is_renew_mode): ?>
                        <div class="col-md-12 cc-renewal-history-panel" id="renewal-amount-history-panel">
                            <div class="cc-renewal-history-header">Renewal Amount History</div>
                            <p class="cc-renewal-history-subtitle text-muted small mb-2">Shown during renewal — compares each period vs the previous year.</p>
                            <div id="renewal-amount-history-list"></div>
                            <p class="text-muted small mb-0">Current year trend updates when you change Contract Amount.</p>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-12 unified-billing-cycle-block amc-billing-cycle-block">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Billing Cycle</label>
                                    <select class="form-control js-example-basic-single" name="amc_billing_cycle" id="unified_billing_cycle">
                                        <option value="monthly" <?= $amc_saved_billing_cycle === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                        <option value="quarterly" <?= $amc_saved_billing_cycle === 'quarterly' || $amc_saved_billing_cycle === '' ? 'selected' : '' ?>>Quarterly</option>
                                        <option value="half_yearly" <?= $amc_saved_billing_cycle === 'half_yearly' ? 'selected' : '' ?>>Half-Yearly</option>
                                        <option value="yearly" <?= $amc_saved_billing_cycle === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Billing Date (Day)</label>
                                    <input type="number" class="form-control text-end" name="amc_billing_day" id="unified_billing_day" value="<?= htmlspecialchars((string) $amc_saved_billing_day) ?>" min="1" max="31">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 unified-milestones-block amc-milestones-block" id="unified-milestones-editor">
                            <label class="form-label d-block">Milestones</label>
                            <div id="unified-milestone-container">
                                <?php
                                $unifiedMilestoneIdx = 0;
                                $prefillMilestones = ($amc_saved_payment_mode === 'milestones' && count((array)$milestones) > 0);
                                if ($prefillMilestones) {
                                    foreach ($milestones as $m) {
                                        $unifiedMilestoneIdx++;
                                        $mName = $m['milestone'] ?? $m['milestone_name'] ?? '';
                                        $mDate = !empty($m['milestone_date']) ? fromDate($m['milestone_date']) : '';
                                        $mPrice = $m['milestone_price'] ?? '';
                                        ?>
                                        <div class="milestone-row mb-2 d-flex align-items-center gap-2">
                                            <input type="text" name="milestone_name_<?= $unifiedMilestoneIdx ?>" class="form-control milestone-name" placeholder="Milestone Name" value="<?= htmlspecialchars($mName) ?>">
                                            <input type="text" name="milestone_date_<?= $unifiedMilestoneIdx ?>" class="form-control datepicker milestone-date" placeholder="Date" value="<?= htmlspecialchars($mDate) ?>">
                                            <input type="number" step="any" name="milestone_price_<?= $unifiedMilestoneIdx ?>" class="form-control milestone-price text-end" placeholder="Price" value="<?= htmlspecialchars($mPrice) ?>">
                                            <button type="button" class="btn btn-danger btn-sm remove-milestone" title="Remove milestone">&times;</button>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    $unifiedMilestoneIdx = 1;
                                    ?>
                                    <div class="milestone-row mb-2 d-flex align-items-center gap-2">
                                        <input type="text" name="milestone_name_1" class="form-control milestone-name" placeholder="Milestone Name">
                                        <input type="text" name="milestone_date_1" class="form-control datepicker milestone-date" placeholder="Date">
                                        <input type="number" step="any" name="milestone_price_1" class="form-control milestone-price text-end" placeholder="Price">
                                        <button type="button" class="btn btn-danger btn-sm remove-milestone" title="Remove milestone">&times;</button>
                                    </div>
                                <?php } ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="unified-add-milestone">+ Add Milestone</button>
                            <div id="unified-milestones-total-row" class="mt-3 p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Total</span>
                                <span class="fw-bold" id="unified-milestones-total">0.00</span>
                            </div>
                        </div>
                        <div class="col-md-12 unified-billing-schedule-block" id="unified-billing-schedule-block">
                            <label class="form-label d-block">Billing Schedule</label>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" id="unified-billing-schedule-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Period</th>
                                            <th class="text-end">Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="unified-billing-schedule-body"<?= $billingScheduleServerPrefilled ? 'data-server-prefilled="1"' : '' ?>>
                                        <?php
                                        if ($billingScheduleServerPrefilled) {
                                            foreach ($milestones as $billingRow) {
                                                $billingRow = is_array($billingRow) ? $billingRow : json_decode(json_encode($billingRow), true);
                                                $periodName = $billingRow['milestone'] ?? $billingRow['milestone_name'] ?? '';
                                                $periodDate = !empty($billingRow['milestone_date']) ? fromDate($billingRow['milestone_date']) : '—';
                                                $periodPrice = isset($billingRow['milestone_price']) ? number_format((float) $billingRow['milestone_price'], 2, '.', '') : '0.00';
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($periodName) ?></td>
                                                    <td class="text-end"><?= htmlspecialchars($periodPrice) ?></td>
                                                    <td><?= htmlspecialchars($periodDate) ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light fw-semibold">
                                            <td>Total</td>
                                            <td class="text-end" id="unified-billing-schedule-total"><?= number_format($billingScheduleTotal, 2, '.', '') ?></td>
                                            <td>—</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <p id="unified-billing-schedule-hint" class="text-muted small mt-2 mb-0<?= ($amc_saved_payment_mode !== 'milestones' && count((array)$milestones) > 0) ? ' d-none' : '' ?>">Set <strong>Effective(Start) Date</strong> and <strong>Expiration(End) Date</strong> in Contract Details to generate the billing schedule.</p>
                        </div>
                    </div>
                </div>

                <div class="row g-3 cc-section-pair-row cc-amc-only">
                    <div class="col-md-6">
                        <div class="cc-section h-100 mb-0 cc-amc-only" id="cc-step-4" data-step="3">
                            <div class="cc-section-header">
                                <h6><span class="cc-section-num">4</span><i class="icofont icofont-settings"></i> Service Setup</h6>
                            </div>
                            <label class="form-label d-block">Service Reminder</label>
                            <div class="cc-inline-checks">
                                <label><input type="checkbox" name="reminder_days[]" value="7" <?= in_array('7', $amc_saved_reminder_days, true) ? 'checked' : '' ?>> 7 days before</label>
                                <label><input type="checkbox" name="reminder_days[]" value="15" <?= in_array('15', $amc_saved_reminder_days, true) ? 'checked' : '' ?>> 15 days before</label>
                                <label><input type="checkbox" name="reminder_days[]" value="30" <?= in_array('30', $amc_saved_reminder_days, true) ? 'checked' : '' ?>> 30 days before</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cc-section h-100 mb-0 cc-amc-only" id="cc-step-5" data-step="4">
                            <div class="cc-section-header">
                                <h6><span class="cc-section-num">5</span><i class="icofont icofont-warning"></i> Escalations</h6>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="amc-add-escalation-btn">
                                    <i class="icofont icofont-plus"></i> Add Escalation
                                </button>
                            </div>
                            <div id="amc-escalation-container">
                                <?php foreach ($amc_escalations as $escIdx => $escRow):
                                    $condition = $escRow['condition'] ?? 'not_started';
                                    $hours = (int) ($escRow['hours'] ?? 4);
                                    $notifyRole = $escRow['notify_role'] ?? 'service_manager';
                                    ?>
                                    <div class="cc-escalation-level" data-esc-idx="<?= (int) $escIdx ?>">
                                        <span class="level-badge">Level <?= (int) $escIdx + 1 ?></span>
                                        <div class="row g-2 align-items-center">
                                            <div class="col-auto text-muted small"><?= htmlspecialchars($amc_condition_labels[$condition] ?? $amc_condition_labels['not_started']) ?></div>
                                            <div class="col-auto">
                                                <input type="hidden" name="amc_escalation_config[<?= (int) $escIdx ?>][condition]" value="<?= htmlspecialchars($condition) ?>">
                                                <input type="number" class="form-control form-control-sm amc-esc-hours text-end" name="amc_escalation_config[<?= (int) $escIdx ?>][hours]" value="<?= $hours ?>" min="1" max="720" style="width:70px;">
                                            </div>
                                            <div class="col-auto text-muted small">hours, Notify</div>
                                            <div class="col-md-5">
                                                <select class="form-control form-control-sm js-example-basic-single amc-esc-notify" name="amc_escalation_config[<?= (int) $escIdx ?>][notify_role]">
                                                    <?php foreach ($amc_notify_roles as $roleKey => $roleLabel): ?>
                                                        <option value="<?= htmlspecialchars($roleKey) ?>" <?= $notifyRole === $roleKey ? 'selected' : '' ?>><?= htmlspecialchars($roleLabel) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-link btn-sm text-danger p-0 amc-remove-escalation" title="Remove level">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 cc-section-pair-row cc-amc-only">
                    <div class="col-md-6">
                        <div class="cc-section h-100 mb-0 cc-amc-only" id="cc-step-6" data-step="5">
                            <div class="cc-section-header">
                                <h6><span class="cc-section-num">6</span><i class="icofont icofont-lock"></i> Gate Entry &amp; Service Completion</h6>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label d-block">Gate entry required for service execution?</label>
                                <div class="cc-inline-radios">
                                    <label><input type="radio" name="gate_entry" value="yes" <?= $amc_saved_gate_entry !== 'no' ? 'checked' : '' ?>> Yes</label>
                                    <label><input type="radio" name="gate_entry" value="no" <?= $amc_saved_gate_entry === 'no' ? 'checked' : '' ?>> No</label>
                                </div>
                            </div>
                            <label class="form-label d-block">Service Completion Requirements</label>
                            <div class="cc-inline-checks d-flex flex-column gap-2">
                                <label><input type="checkbox" name="completion_req[]" value="service_report" <?= in_array('service_report', $amc_saved_completion_rules, true) ? 'checked' : '' ?>> Service Report upload mandatory</label>
                                <label><input type="checkbox" name="completion_req[]" value="technician_signoff" <?= in_array('technician_signoff', $amc_saved_completion_rules, true) ? 'checked' : '' ?>> Technician Sign-off</label>
                                <label><input type="checkbox" name="completion_req[]" value="customer_signoff" <?= in_array('customer_signoff', $amc_saved_completion_rules, true) ? 'checked' : '' ?>> Customer Sign-off</label>
                                <label><input type="checkbox" name="completion_req[]" value="photo_upload" <?= in_array('photo_upload', $amc_saved_completion_rules, true) ? 'checked' : '' ?>> Photo Upload</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cc-section cc-summary-section h-100 mb-0">
                            <div class="cc-section-header">
                                <h6><i class="icofont icofont-chart-pie"></i> Summary</h6>
                            </div>
                            <div id="amc-summary-content" class="small text-muted">Fill the form sections above to see a summary before submit.</div>
                        </div>
                    </div>
                </div>

                <div class="cc-wizard-footer d-flex justify-content-start gap-2">
                    <?php if (empty($contract_form_locked) && !$amc_is_cancelled): ?>
                        <?php if ($amc_is_renew_mode): ?>
                            <button type="button" class="btn btn-primary cc-btn-submit">
                                <i class="fa fa-refresh"></i> Renew Contract
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary cc-btn-submit">
                                <i class="icofont icofont-check"></i> Submit Contract
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

</div><!-- #unified-contract-page -->
<div id="amc-php-data" style="display:none;">
    <div id="amc-assets-data"><?= htmlspecialchars(json_encode(array_values((array)$amc_assets)), ENT_QUOTES, 'UTF-8') ?></div>
    <div id="amc-service-frequencies-data"><?= htmlspecialchars(json_encode($amc_service_frequencies), ENT_QUOTES, 'UTF-8') ?></div>
    <div id="amc-edit-data"><?= htmlspecialchars(json_encode($amc_contract_data ?? array()), ENT_QUOTES, 'UTF-8') ?></div>
</div>

<template id="amc-escalation-level-template">
    <div class="cc-escalation-level" data-esc-idx="">
        <span class="level-badge">Level 1</span>
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <select class="form-select form-select-sm amc-esc-condition">
                    <option value="not_started">If service not started within</option>
                    <option value="not_completed">If service not completed within</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="number" class="form-control form-control-sm amc-esc-hours text-end" value="4" min="1" max="720" style="width:70px;">
            </div>
            <div class="col-auto text-muted small">hours, Notify</div>
            <div class="col-md-5">
                <select class="form-select form-select-sm js-example-basic-single amc-esc-notify">
                    <option value="service_manager">Service Manager</option>
                    <option value="regional_manager">Regional Manager</option>
                    <option value="contract_owner">Contract Owner</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-link btn-sm text-danger p-0 amc-remove-escalation" title="Remove level">&times;</button>
            </div>
        </div>
    </div>
</template>

<template id="amc-location-template">
    <div class="card mb-3 amc-location-block">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 amc-location-title">Location 1</h6>
            <button type="button" class="btn btn-danger btn-sm amc-remove-location" title="Remove Location">
                <i class="ti-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Location</th>
                            <th>Service Start Date</th>
                            <th>Service End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="min-width: 220px;">
                                <select class="form-select js-example-basic-single amc-location-select">
                                    <option value="">Select Location</option>
                                    <?php if (count((array)$locations) > 0) {
                                        foreach ($locations as $location) {
                                            $loc = is_array($location) ? $location : json_decode(json_encode($location), true); ?>
                                            <option value="<?= htmlspecialchars($loc['id'] ?? '') ?>"><?= htmlspecialchars($loc['location_name'] ?? '') ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </td>
                            <td><input type="date" class="form-control    amc-loc-service-start"  ></td>
                            <td><input type="date" class="form-control  amc-loc-service-end"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <h6 class="mb-0">Assets Mapping</h6>
                    <div class="d-flex flex-wrap gap-2 align-items-end">
                        <div class="d-flex flex-wrap gap-2 align-items-end amc-add-by-category-wrap">
                            <div style="min-width:160px;">
                                <label class="form-label f-12 mb-1">Category</label>
                                <select class="form-select form-select-sm js-example-basic-single amc-bulk-category-select">
                                    <option value="">Select category</option>
                                    <?php if (count((array)$amc_asset_categories) > 0) {
                                        foreach ($amc_asset_categories as $catRow) {
                                            $cat = is_array($catRow) ? $catRow : json_decode(json_encode($catRow), true); ?>
                                            <option value="<?= htmlspecialchars($cat['id'] ?? '') ?>"><?= htmlspecialchars($cat['category_description'] ?? '') ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm amc-add-by-category-btn">
                                <i class="icofont icofont-plus"></i> Add by category
                            </button>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm amc-add-asset-btn">
                            <i class="icofont icofont-plus"></i> Add Asset
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm f-12 amc-assets-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Asset No.</th>
                                <th>Asset Name</th>
                                <th>Category</th>
                                <th>Service Frequency</th>
                                <th>Visits / Interval</th>
                                <th>Service Start</th>
                                <th>Service End</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="amc-assets-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="amc-asset-row-template">
    <tr class="amc-asset-row">
        <td style="min-width: 160px;">
            <select class="form-select js-example-basic-single amc-asset-select">
                <option value="">Select location first</option>
            </select>
        </td>
        <td><input type="text" class="form-control amc-asset-name" readonly placeholder="Asset Name"></td>
        <td><input type="text" class="form-control amc-asset-category" readonly placeholder="Category"></td>
        <td style="min-width: 160px;">
            <select class="form-select js-example-basic-single amc-service-frequency-select">
                <option value="">Select Frequency</option>
                <?php foreach ($amc_service_frequencies as $freq) {
                    $fr = is_array($freq) ? $freq : json_decode(json_encode($freq), true); ?>
                    <option value="<?= htmlspecialchars($fr['genc_detail_code'] ?? '') ?>"><?= htmlspecialchars($fr['genc_detail_name'] ?? '') ?></option>
                <?php } ?>
            </select>
        </td>
        <td style="min-width: 90px;">
            <input type="number" class="form-control amc-visits-per-interval text-end" min="1" max="31" value="1" title="Visits per interval">
        </td>
        <td><input type="date" class="form-control amc-asset-service-start" readonly tabindex="-1"></td>
        <td><input type="date" class="form-control amc-asset-service-end" readonly tabindex="-1"></td>
        <td class="text-center text-nowrap">
            <?php if ($amc_is_edit): ?>
                <span class="amc-schedule-actions">
                    <button type="button" class="btn btn-link btn-sm p-1 amc-view-history" title="Planned Schedule"><i class="ti-time text-info f-16"></i></button>
                    <button type="button" class="btn btn-link btn-sm p-1 amc-service-calendar-btn" title="Service Calendar"><i class="ti-calendar text-primary f-16"></i></button>
                </span>
            <?php endif; ?>
            <button type="button" class="btn btn-link btn-sm p-1 text-danger amc-remove-asset" title="Remove"><i class="ti-trash f-16"></i></button>
        </td>
    </tr>
    <tr class="amc-asset-slots-row d-none">
        <td colspan="8" class="bg-light py-2">
            <div class="amc-slot-config small">
                <span class="text-muted me-2 amc-slot-config-label">Visit slots:</span>
                <div class="amc-slot-fields d-flex flex-wrap gap-2 align-items-end"></div>
            </div>
        </td>
    </tr>
</template>

<div class="modal fade" id="amcAssetHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Planned Schedule — <span id="amcHistoryAssetLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body custom-scroll" id="amcAssetHistoryBody" style="max-height: 600px;">
                <p class="text-center text-muted mb-0">Loading schedule…</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="amcServiceCalendarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Service Calendar — <span id="amcCalendarAssetLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body custom-scroll" style="max-height: 600px;"><div id="amc-service-calendar"></div></div>
        </div>
    </div>
</div>

<?php if ($is_edit_mode && !$is_renew_mode): ?>
</div><!-- #contractdetails -->
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
                                <?= $disabledAttr ?>>
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
</div><!-- #documents -->
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

</div><!-- .tab-content -->
<?php endif; ?>
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
    <div id="amc-edit-mode-flag"><?php echo $is_amc_mode ? 'true' : 'false'; ?></div>
    <div id="is-amc-mode"><?php echo $is_amc_mode ? 'true' : 'false'; ?></div>
    <div id="unified-locations-required"><?php echo isContractFieldMandatory('locations', $insert_attributes) ? 'true' : 'false'; ?></div>
    <div id="is-renew-mode"><?php echo $is_renew_mode ? 'true' : 'false'; ?></div>
    <div id="renewal-amount-history-data"><?php echo json_encode($renewalAmountHistoryData ?? array('periods' => array(), 'new_period_start' => '', 'new_period_end' => '')); ?></div>
    <div id="contract-id"><?php echo $contract_id ?? 0; ?></div>
    <div id="company-id"><?php echo $company_id ?? 0; ?></div>
    <div id="user-id"><?php echo $user_id ?? 0; ?></div>
    <div id="data-name"><?php echo $_SESSION['DB_NAME'] ?? 'infisuite'; ?></div>
    <div id="config-name"><?php echo isset($_SESSION['config']['name']) ? ucwords($_SESSION['config']['name']) : ''; ?></div>
</div>

<link rel="stylesheet" href="assets/css/custom_insert_create_contract.css?v=<?= @filemtime(__DIR__ . '/../assets/css/custom_insert_create_contract.css') ?: time() ?>">
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script src="assets/js/custom_insert_amc_contract.js?v=<?= @filemtime(__DIR__ . '/../assets/js/custom_insert_amc_contract.js') ?: time() ?>"></script>
</div><!-- .card-block -->
</div><!-- .card -->
