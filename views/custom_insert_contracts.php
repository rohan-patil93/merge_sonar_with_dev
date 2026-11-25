<?php
defined('CSS_DISPLAY_NONE_SEMI') || define('CSS_DISPLAY_NONE_SEMI', 'display:none;');
defined('MSG_CONTRACT_APPROVED_RENEW') || define('MSG_CONTRACT_APPROVED_RENEW', 'Contract must be approved before it can be renewed.');
defined('DATE_EPOCH') || define('DATE_EPOCH', '1970-01-01');
defined('DIR_UPLOADS_CONTRACTS') || define('DIR_UPLOADS_CONTRACTS', './uploads/contracts/');
defined('HEADER_REDIRECT_CONTRACTS') || define('HEADER_REDIRECT_CONTRACTS', 'location:index.php?option=COM_LIST_CONTRACTS');
defined('CLS_ACTIVE_SHOW') || define('CLS_ACTIVE_SHOW', 'active show');
defined('ATTR_PARTY_TYPE') || define('ATTR_PARTY_TYPE', 'name="party_type"');

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
// LIVE PATCH: role gating
// OLD role-based contract type access (kept for reference)
// $contractRoleAccess = resolveContractCreateRoleAccess();
// $show_contract_type_toggle = !$is_edit_mode && !$is_renew_mode && !empty($contractRoleAccess['show_toggle']);
// NEW: user-assigned contract types from single API
$contractRoleAccess = resolveUserContractTypeAccessFromApi();
$allowedContractTypes = $contractRoleAccess['types'] ?? array();
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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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


$url = API . "?mode=getcontractdetailsreactamc&method=get&rid={$contract_id}" . "&admin_type=" . $_SESSION['admin_type'];
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
$document_parent = $data['document_parent_res'] ?? [];
print_r($document_parent);
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
function getFieldDisplay($field, $insert_attributes)
{
    $display = $insert_attributes[$field]['insert_display_list'] ?? 1;
    $mandatory = $insert_attributes[$field]['mandatory'] ?? 2;

    return [
        'style' => $display == 2 ? CSS_DISPLAY_NONE_SEMI : '',
        'required' => $mandatory == 1 ? 'required' : '',
        'star' => $mandatory == 1 ? '<span class="text-danger">*</span>' : ''
    ];
}

function isContractFieldMandatory($field, $insert_attributes)
{
    return ($insert_attributes[$field]['mandatory'] ?? 2) == 1;
}

function fetchDecodedUserRoles($userId, $companyId)
{
    $wh = array(
        'where' => array(
            'id' => $userId,
            'company_id' => $companyId,
            'is_active' => '0',
        ),
        'field' => 'role_id',
    );
    $url = API . "?mode=gettabledetailswithconditions&table=application_users&data=" . urlencode(json_encode($wh));
    $roleRows = json_decode(callAPI($url), true);
    $roleJson = '';
    if (is_array($roleRows) && isset($roleRows['role_id'])) {
        $roleJson = $roleRows['role_id'];
    } elseif (count((array) $roleRows) > 0) {
        foreach ((array) $roleRows as $roleRow) {
            $roleRowArr = is_array($roleRow) ? $roleRow : json_decode(json_encode($roleRow), true);
            $roleJson = $roleRowArr['role_id'] ?? '';
            break;
        }
    }
    $decodedRoles = json_decode($roleJson, true);
    return is_array($decodedRoles) ? $decodedRoles : array();
}

// LIVE PATCH: role gating
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
    $userRoles = fetchDecodedUserRoles($user_id, $company_id);

    $normalizedRoles = array();
    if (count((array) $userRoles) > 0) {
        foreach ($userRoles as $roleCode) {
            $normalizedRoles[] = strtoupper(trim((string) $roleCode));
        }
    }

    $isAdmin = ($adminType == '1')
        || in_array('ADMIN', (array) $normalizedRoles)
        || in_array('CONTRACT_ADMIN', (array) $normalizedRoles)
        || ($workingRole == 'ADMIN')
        || ($workingRole == 'CONTRACT_ADMIN');

    $effectiveRole = $workingRole;
    if (count((array) $normalizedRoles) == 1) {
        $effectiveRole = $normalizedRoles[0];
    } elseif (count((array) $normalizedRoles) > 1 && $workingRole != '' && in_array($workingRole, (array) $normalizedRoles)) {
        $effectiveRole = $workingRole;
    }

    $access['is_admin'] = $isAdmin;
    $access['effective_role'] = $effectiveRole;
    if (!$isAdmin) {
        if ($effectiveRole == 'CONTRACT_FM') {
            $access['can_standard'] = false;
            $access['can_amc'] = true;
            $access['show_toggle'] = false;
        } elseif ($effectiveRole == 'CONTRACT_USER') {
            $access['can_standard'] = true;
            $access['can_amc'] = false;
            $access['show_toggle'] = false;
        }
    }

    return $access;
}

/**
 * Load user-assigned contract types via single API (label/code/class).
 * Shape keeps can_standard / can_amc / show_toggle for existing submit guards.
 */
function normalizeContractTypeRows($types, &$access)
{
    $normalized = array();
    foreach ((array) $types as $typeRow) {
        $rowArr = is_array($typeRow) ? $typeRow : json_decode(json_encode($typeRow), true);
        $code = trim((string) ($rowArr['code'] ?? ''));
        if ($code == '') {
            continue;
        }
        $normalized[] = array(
            'label' => (string) ($rowArr['label'] ?? ''),
            'code' => $code,
            'class' => (string) ($rowArr['class'] ?? ''),
        );
        if ($code == 'standard') {
            $access['can_standard'] = true;
        }
        if ($code == 'amc') {
            $access['can_amc'] = true;
        }
    }
    return $normalized;
}

function resolveUserContractTypeAccessFromApi()
{
    $access = array(
        'types' => array(),
        'can_standard' => false,
        'can_amc' => false,
        'show_toggle' => false,
    );

    $url = API . "?mode=getusercontracttypelist&method=get";
    $res = json_decode(callAPI($url), true);
    $types = (is_array($res) && isset($res['types']) && is_array($res['types'])) ? $res['types'] : array();

    $normalized = normalizeContractTypeRows($types, $access);
    $access['types'] = $normalized;
    $access['show_toggle'] = (count((array) $normalized) > 1);

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
    loadContractDataFallback($data);
}

$contract_is_approved = $is_edit_mode && isContractApproved($formData);
if ($is_renew_mode && $is_edit_mode && !$contract_is_approved) {
    $is_renew_mode = false;
    if (empty($notification['message'])) {
        $notification = ['message' => MSG_CONTRACT_APPROVED_RENEW, 'type' => 'warning'];
    }
}

$page_contract_type = (isset($_GET['contract_type']) && $_GET['contract_type'] == 'amc') ? 'amc' : 'standard';
// $is_amc_mode = $is_edit_mode ? (($formData['is_amc_contract'] ?? 'N') == 'Y') : ($page_contract_type == 'amc');
// LIVE PATCH: convert type + role gating
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
// OLD role-based create-mode override (kept for reference)
// if (!$is_edit_mode && !$is_renew_mode) {
//     if (empty($contractRoleAccess['can_amc']) && !empty($contractRoleAccess['can_standard'])) {
//         $is_amc_mode = false;
//         $page_contract_type = 'standard';
//     } elseif (empty($contractRoleAccess['can_standard']) && !empty($contractRoleAccess['can_amc'])) {
//         $is_amc_mode = true;
//         $page_contract_type = 'amc';
//     }
// }
// NEW: force create mode from user-assigned type codes
if (!$is_edit_mode && !$is_renew_mode) {
    $allowedCodes = array();
    if (count((array) $allowedContractTypes) > 0) {
        foreach ($allowedContractTypes as $typeItem) {
            $typeItemArr = is_array($typeItem) ? $typeItem : json_decode(json_encode($typeItem), true);
            $codeVal = trim((string) ($typeItemArr['code'] ?? ''));
            if ($codeVal != '') {
                $allowedCodes[] = $codeVal;
            }
        }
    }
    if (count((array) $allowedCodes) == 1) {
        $page_contract_type = $allowedCodes[0];
    } elseif (count((array) $allowedCodes) > 1) {
        if (!in_array($page_contract_type, (array) $allowedCodes)) {
            $page_contract_type = $allowedCodes[0];
        }
    } else {
        $page_contract_type = 'standard';
    }
    $is_amc_mode = ($page_contract_type == 'amc');
}
$is_amc_edit_mode = $is_edit_mode && $is_amc_mode;
$amc_contract_data = !empty($data['amccontractres']) ? $data['amccontractres'] : array();
$amc_assets = array();
$amc_service_frequencies = array();
$amc_asset_categories = array();
$amcLookupResponse = json_decode(callAPI(API . "?mode=getallproductdetails"), true);
$amc_assets = $amcLookupResponse['asset_details'] ?? array();
$amc_asset_categories = $amcLookupResponse['asset_categories'] ?? array();
$amc_departments = $amcLookupResponse['departments'] ?? array();
$amc_gc_details = $amcLookupResponse['gc_details'] ?? array();
$amc_service_frequencies_raw = array();
if (count((array) $amc_gc_details) > 0) {
    foreach ($amc_gc_details as $gcRow) {
        $gcRowArr = is_array($gcRow) ? $gcRow : json_decode(json_encode($gcRow), true);
        if (($gcRowArr['genc_code'] ?? '') == 'AMCSERVICE') {
            $amc_service_frequencies_raw[] = $gcRowArr;
        }
    }
}
$amc_service_frequencies = merge_amc_service_frequency_options($amc_service_frequencies_raw);
function loadContractDataFallback($data = [])
{
    global $formData, $documentDetails, $permissionDetails, $workflow, $originator;

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

    loadActivityDetailsFallback($data);
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
function collectAmcAssetIds($amc_contract_data)
{
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
    return $assetIds;
}

function checkAmcScheduleStarted($contract_id, $company_id, $assetIds)
{
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

function hasAmcServiceStarted($contract_id, $company_id, $formData, $amc_contract_data = array())
{
    $started = false;
    if (!empty($contract_id)) {
        $isAmc = ($formData['is_amc_contract'] ?? 'N') == 'Y';
        if (!$isAmc) {
            $startDate = $formData['start_date'] ?? '';
            $started = $startDate != '' && $startDate <= date('Y-m-d');
        } else {
            $assetIds = collectAmcAssetIds($amc_contract_data);
            $started = count((array) $assetIds) > 0 && checkAmcScheduleStarted($contract_id, $company_id, $assetIds);
        }
    }
    return $started;
}

function getContractFormLockState($contract_id, $formData, $company_id, $workflow_level = null, $amc_contract_data = array())
{
    $state = array(
        'locked' => false,
        'formdisable' => '',
        'reason' => '',
    );

    $current_status = $formData['current_status'] ?? '';
    if ($current_status === null) {
        $current_status = '';
    }
    $editableStatuses = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK, '', 0, '0');

    if (!empty($contract_id) && !in_array($current_status, (array) $editableStatuses)) {
        if ($workflow_level === null) {
            $workflow_level = fetchContractWorkflowLevel($contract_id, $current_status);
        }
        if ($workflow_level == 0 && $current_status == ST_APPROVED) {
            if (hasAmcServiceStarted($contract_id, $company_id, $formData, $amc_contract_data)) {
                $state['locked'] = true;
                $state['formdisable'] = 'event_disabled';
                $state['reason'] = 'Service schedule has started; contract can no longer be edited.';
            }
        } else {
            $state['locked'] = true;
            $state['formdisable'] = 'event_disabled';
            $state['reason'] = ($current_status == ST_APPROVED)
                ? 'This contract is approved and can no longer be edited.'
                : 'This contract is in workflow approval and cannot be edited until it is sent back or rejected.';
        }
    }

    return $state;
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
        if ((is_array($val) && count((array) $val) > 0) || (!is_array($val) && trim((string) $val) != '')) {
            return $val;
        }
    }
    return (isset($formData[$key]) && $formData[$key] != '' && $formData[$key] != null) ? $formData[$key] : $default;
}

function indexContractsById($contracts)
{
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
    return $contractsById;
}

function buildRenewalAmountHistoryChain($contractId, $contracts)
{
    $chain = array();
    $contractId = (int) $contractId;
    if ($contractId <= 0) {
        return $chain;
    }

    $contractsById = indexContractsById($contracts);
    $currentId = $contractId;
    $visited = array();
    $maxDepth = 50;

    while ($currentId > 0 && count((array) $chain) < $maxDepth) {
        if (isset($visited[$currentId]) || !isset($contractsById[$currentId])) {
            break;
        }
        $visited[$currentId] = true;

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

    if ($startYmd == '' || $endYmd == '' || $startYmd == DATE_EPOCH || $endYmd == DATE_EPOCH) {
        $notification = array('message' => 'Effective and expiration dates are required.', 'type' => 'danger');
        return false;
    }

    if (strtotime($startYmd) > strtotime($endYmd)) {
        $notification = array('message' => 'Effective(Start) Date cannot be after Expiration(End) Date.', 'type' => 'danger');
        return false;
    }

    return array('start_date' => $startYmd, 'end_date' => $endYmd);
}

function resolveNormalizedServiceDates($start, $end, $contractStartYmd, $contractEndYmd, $forceAlign = false)
{
    $startVal = dateconvertdbformat(trim((string) $start));
    $endVal = dateconvertdbformat(trim((string) $end));

    if (($forceAlign || $startVal == '' || $startVal == DATE_EPOCH) && $contractStartYmd != '') {
        $startVal = $contractStartYmd;
    }
    if (($forceAlign || $endVal == '' || $endVal == DATE_EPOCH) && $contractEndYmd != '') {
        $endVal = $contractEndYmd;
    }

    return array($startVal, $endVal);
}

function fallbackNormalizedDate($dateVal, $fallbackPrimary, $fallbackSecondary)
{
    if ($dateVal === '' || $dateVal === DATE_EPOCH) {
        if ($fallbackPrimary !== '') {
            return $fallbackPrimary;
        }
        if ($fallbackSecondary !== '') {
            return $fallbackSecondary;
        }
    }
    return $dateVal;
}

function normalizeAmcAssetsForLocation($locIdx, $locStart, $locEnd, $contractStartYmd, $contractEndYmd, $forceAlign)
{
    if (!isset($_POST['amc_asset'][$locIdx]) || !is_array($_POST['amc_asset'][$locIdx])) {
        return;
    }
    foreach ($_POST['amc_asset'][$locIdx] as $assetIdx => $assetRow) {
        if (!is_array($assetRow)) {
            continue;
        }
        if ($forceAlign) {
            $assetStart = $locStart;
            $assetEnd = $locEnd;
        } else {
            $rawStart = dateconvertdbformat(trim((string) ($assetRow['service_start_date'] ?? '')));
            $rawEnd = dateconvertdbformat(trim((string) ($assetRow['service_end_date'] ?? '')));
            $assetStart = fallbackNormalizedDate($rawStart, $locStart, $contractStartYmd);
            $assetEnd = fallbackNormalizedDate($rawEnd, $locEnd, $contractEndYmd);
        }
        $_POST['amc_asset'][$locIdx][$assetIdx]['service_start_date'] = $assetStart;
        $_POST['amc_asset'][$locIdx][$assetIdx]['service_end_date'] = $assetEnd;
    }
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
        list($locStart, $locEnd) = resolveNormalizedServiceDates(
            $locRow['service_start_date'] ?? '',
            $locRow['service_end_date'] ?? '',
            $contractStartYmd,
            $contractEndYmd,
            $forceAlignToContract
        );
        $_POST['amc_loc'][$locIdx]['service_start_date'] = $locStart;
        $_POST['amc_loc'][$locIdx]['service_end_date'] = $locEnd;

        normalizeAmcAssetsForLocation($locIdx, $locStart, $locEnd, $contractStartYmd, $contractEndYmd, $forceAlignToContract);
    }
}

function validateUnifiedContractBasics(&$notification)
{
    $partyType = $_POST['party_type'] ?? '';
    $isInhouse = ($partyType == 'inhouse') || (($_POST['amc_service_mode'] ?? '') == 'inhouse');
    $amount = trim((string) ($_POST['contract_amount'] ?? ''));
    $isValid = false;

    if (trim($_POST['contract_name'] ?? '') == '') {
        $notification = array('message' => 'Contract name is required.', 'type' => 'danger');
    } elseif (!parseUnifiedContractDates($notification)) {
        $isValid = false;
    } elseif (!$isInhouse && empty($_POST['party_id'])) {
        $notification = array('message' => 'Party is required.', 'type' => 'danger');
    } elseif (empty($_POST['department_id'])) {
        $notification = array('message' => 'Department is required.', 'type' => 'danger');
    } elseif ($amount == '') {
        $notification = array('message' => 'Contract amount is required.', 'type' => 'danger');
    } elseif (!is_numeric($amount)) {
        $notification = array('message' => 'Contract amount must be a valid number.', 'type' => 'danger');
    } else {
        $isValid = true;
    }

    return $isValid;
}

function uploadSingleContractAdditionalFile($i, $prefix, $uploadDir, $allowedTypes, $maxSize)
{
    if ($_FILES['additional_document_file']['error'][$i] !== UPLOAD_ERR_OK) {
        return;
    }
    $fileName = $_FILES['additional_document_file']['name'][$i];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExt, (array) $allowedTypes) && $_FILES['additional_document_file']['size'][$i] <= $maxSize) {
        $newFileName = time() . "_" . $prefix . $i . "_" . basename($fileName);
        $filePath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['additional_document_file']['tmp_name'][$i], $filePath)) {
            $currentFiles = $_POST['all_additional_documents'] ?? '';
            $currentFiles = !empty($currentFiles) ? $currentFiles . ',' . $newFileName : $newFileName;
            $_POST['all_additional_documents'] = $currentFiles;
        }
    }
}

function uploadContractAdditionalFiles($prefix = 'renewal_')
{
    if (isset($_FILES['additional_document_file']) && is_array($_FILES['additional_document_file']['name'])) {
        $allowedTypes = ['pdf', 'xls', 'xlsx', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $maxSize = 10 * 1024 * 1024;
        $uploadDir = DIR_UPLOADS_CONTRACTS;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0750, true);
        }

        $fileCount = count($_FILES['additional_document_file']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            uploadSingleContractAdditionalFile($i, $prefix, $uploadDir, $allowedTypes, $maxSize);
        }
    }
    return $_POST['all_additional_documents'] ?? '';
}

function verifyContractLockStatus($saveContractId, $formData, $company_id, &$notification)
{
    if ($saveContractId == '') {
        return true;
    }
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
        return false;
    }
    return true;
}

function validateStandardLocationsMandatory($insert_attributes, &$notification)
{
    if (($_POST['is_amc_contract'] ?? 'N') != 'N' || !isContractFieldMandatory('locations', $insert_attributes)) {
        return true;
    }
    $locValues = $_POST['locations'] ?? array();
    if (!is_array($locValues)) {
        $decodedLocations = json_decode((string) $locValues, true);
        $locValues = is_array($decodedLocations) ? $decodedLocations : array();
    }
    foreach ((array) $locValues as $loc) {
        if (!empty($loc) && $loc != '0') {
            return true;
        }
    }
    $notification = array('message' => 'Please select at least one location.', 'type' => 'danger');
    return false;
}

function handleContractRenewal()
{
    global $formData, $contract_id, $versionId;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    $renewContractId = $contract_id ?? ($_POST['Rid'] ?? '');
    if (!fetchContractIsApproved($renewContractId)) {
        $notification = ['message' => MSG_CONTRACT_APPROVED_RENEW, 'type' => 'danger'];
        return;
    }

    $contractDates = parseUnifiedContractDates($notification);
    if (!$contractDates) {
        return;
    }
    $startYmd = $contractDates['start_date'];
    $endYmd = $contractDates['end_date'];

    $additionalDocumentFileName = uploadContractAdditionalFiles('renewal_');

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
        file_post_contents($renewalUrl, $renewalData, 'renew');

        $notification = ['message' => 'Contract renewed successfully!', 'type' => 'success'];

    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while renewing the contract!', 'type' => 'danger'];
    }
}

function handleContractSubmission()
{
    global $formData, $permissionDetails, $workflow, $documentDetails, $versionId, $contract_id;
    // global $company_id, $data_name, $user_id, $module_id, $notification, $insert_attributes;
    // LIVE PATCH: role gating
    global $company_id, $data_name, $user_id, $module_id, $notification, $insert_attributes, $contractRoleAccess;

    $saveContractId = trim((string) ($_POST['Rid'] ?? ''));
    if ($saveContractId == '' && !empty($contract_id)) {
        $saveContractId = (string) $contract_id;
    }
    if (!verifyContractLockStatus($saveContractId, $formData, $company_id, $notification)) {
        return;
    }
    if ($saveContractId == '' && empty($contractRoleAccess['can_standard'])) {
        $notification = array('message' => 'You are not allowed to create a Standard contract.', 'type' => 'danger');
        return;
    }
    $contractDates = (validateUnifiedContractBasics($notification) && validateStandardLocationsMandatory($insert_attributes, $notification)) ? parseUnifiedContractDates($notification) : false;
    if (!$contractDates) {
        return;
    }
    $startYmd = $contractDates['start_date'];
    $endYmd = $contractDates['end_date'];

    $additionalDocumentFileName = uploadContractAdditionalFiles('additional_');

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


        if ($result['contract_result']['LASTID'] == 0) {
            $msgcode = 3001;
        } else {
            $msgcode = 3000;
            $_SESSION['insert_msg'] = $msgcode;
            header(HEADER_REDIRECT_CONTRACTS);
        }
        ?>
        <script>
            document.onreadystatechange = function () {
                if (document.readyState == "complete") {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message'][$msgcode]; ?>', '');
                }
            }
        </script>
        <?php



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

    if (empty($dates)) {
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

function validateAmcLocationsMandatory($insert_attributes, &$notification)
{
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
        return false;
    }
    return true;
}

function extractAmcLocationIds()
{
    $locationIds = array();
    if (isset($_POST['amc_loc']) && is_array($_POST['amc_loc'])) {
        foreach ($_POST['amc_loc'] as $locRow) {
            $lid = (int) ($locRow['location_id'] ?? 0);
            if ($lid > 0) {
                $locationIds[] = $lid;
            }
        }
    }
    return $locationIds;
}

function buildAmcMilestonesForPost($startYmd, $endYmd)
{
    $paymentMode = $_POST['amc_payment_mode'] ?? 'billing_cycle';
    if ($paymentMode === 'billing_cycle') {
        return generateAmcBillingMilestones(
            $startYmd,
            $endYmd,
            $_POST['amc_billing_cycle'] ?? 'quarterly',
            $_POST['amc_billing_day'] ?? 1,
            $_POST['contract_amount'] ?? 0
        );
    }
    return collectMilestonesFromPost();
}

function isAmcSaveSuccessful($result, $editContractId, $response)
{
    $contractResult = $result['contract_result'] ?? array();
    $amcResult = $result['amc_contract_result'] ?? array();
    $insertOk = (int) ($contractResult['LASTID'] ?? 0) > 0;
    $updateOk = $editContractId != '' && in_array(strtolower($contractResult['status'] ?? ''), array('sucess', 'success'));
    $amcSaveOk = !empty($amcResult['details']) && in_array($amcResult['details']['status'] ?? '', array('sucess', 'success'));
    $saveOk = $insertOk || $updateOk || $amcSaveOk;
    if (!$saveOk && $editContractId != '' && (preg_match('/"(sucess|success)"/i', $response) || count((array) ($amcResult['assets'] ?? array())) > 0)) {
        $saveOk = true;
    }
    return $saveOk;
}

function parseAmcApiResponsePayload($response)
{
    $result = json_decode($response, true);
    $jsonOk = is_array($result);
    if (!$jsonOk && preg_match('/\{.*\}/s', (string) $response, $jsonMatch)) {
        $result = json_decode($jsonMatch[0], true);
        $jsonOk = is_array($result);
    }
    return array($jsonOk ? $result : array(), $jsonOk);
}

function getAmcSaveFailureMessage($response, $jsonOk, $result)
{
    $contractResult = $result['contract_result'] ?? array();
    $amcResult = $result['amc_contract_result'] ?? array();

    $message = 'Failed to save AMC contract.';
    if (!$jsonOk && trim((string) $response) != '') {
        error_log('AMC API invalid JSON: ' . substr((string) $response, 0, 500));
        $message = 'Unexpected API response. Please try again.';
    } elseif (trim((string) $response) == '') {
        $message = 'Empty API response from server.';
    } elseif (!empty($amcResult['message'])) {
        $message = $amcResult['message'];
    } elseif (!empty($contractResult['message'])) {
        $message = $contractResult['message'];
    } elseif (!empty($_SESSION['error_message'][3001])) {
        $message = $_SESSION['error_message'][3001];
    }
    return $message;
}

function handleAmcSaveSuccessRedirect($amcResult)
{
    $scheduleCount = (int) ($amcResult['schedule_count'] ?? 0);
    $assetCount = count((array) ($amcResult['assets'] ?? array()));
    $scheduleErrors = $amcResult['schedule_errors'] ?? array();

    $_SESSION['insert_msg'] = 3000;
    if ($assetCount > 0 && $scheduleCount == 0) {
        $warnMsg = 'Contract saved but service schedule was not generated. Check location/asset service dates.';
        if (count((array) $scheduleErrors) > 0) {
            $warnMsg .= ' ' . implode(' ', array_slice((array) $scheduleErrors, 0, 2));
        }
        $_SESSION['amc_schedule_warning'] = $warnMsg;
    }
    if (!headers_sent()) {
        header(HEADER_REDIRECT_CONTRACTS);
        exit;
    }
    echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=index.php?option=COM_LIST_CONTRACTS"><script>window.location.replace("index.php?option=COM_LIST_CONTRACTS");</script></head><body></body></html>';
    exit;
}

function processAmcSaveResponse($response, $editContractId, &$notification)
{
    list($result, $jsonOk) = parseAmcApiResponsePayload($response);
    echo "<pre>";
    print_r($result);
    echo "</pre>";

    if (isAmcSaveSuccessful($result, $editContractId, $response)) {
        handleAmcSaveSuccessRedirect($result['amc_contract_result'] ?? array());
    }

    $notification = ['message' => getAmcSaveFailureMessage($response, $jsonOk, $result), 'type' => 'danger'];
}

function processAmcRenewalResponse($response, &$notification)
{
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

    if ($renewId > 0) {
        $_SESSION['insert_msg'] = 3000;
        if ($assetCount > 0 && $scheduleCount == 0) {
            $warnMsg = 'Contract renewed but service schedule was not generated. Check location/asset service dates.';
            if (count((array) $scheduleErrors) > 0) {
                $warnMsg .= ' ' . implode(' ', array_slice((array) $scheduleErrors, 0, 2));
            }
            $_SESSION['amc_schedule_warning'] = $warnMsg;
        }
        if (!headers_sent()) {
            header(HEADER_REDIRECT_CONTRACTS);
            exit;
        }
        echo '<script>window.location.href = "index.php?option=COM_LIST_CONTRACTS";</script>';
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
}

function validateAmcContractSubmissionPreconditions($isRenewFlow, $saveContractId, $formData, $company_id, $contractRoleAccess, &$notification)
{
    $valid = true;
    if ($isRenewFlow && $saveContractId == '') {
        $notification = ['message' => 'Invalid contract for renewal.', 'type' => 'danger'];
        $valid = false;
    } elseif (!verifyContractLockStatus($saveContractId, $formData, $company_id, $notification)) {
        $valid = false;
    } else {
        $canCreateAmc = $isRenewFlow || $saveContractId != '' || !empty($contractRoleAccess['can_amc']);
        if (!$canCreateAmc) {
            $notification = array('message' => 'You are not allowed to create an AMC contract.', 'type' => 'danger');
            $valid = false;
        }
    }
    return $valid;
}

function handleAmcContractSubmission($isRenewFlow = false)
{
    global $formData, $permissionDetails, $workflow, $documentDetails, $versionId, $contract_id;
    global $company_id, $user_id, $notification, $insert_attributes, $contractRoleAccess;

    $saveContractId = trim((string) ($_POST['Rid'] ?? ''));
    if ($saveContractId == '' && !empty($contract_id)) {
        $saveContractId = (string) $contract_id;
    }
    if (!validateAmcContractSubmissionPreconditions($isRenewFlow, $saveContractId, $formData, $company_id, $contractRoleAccess, $notification)) {
        return;
    }
    $contractDates = validateUnifiedContractBasics($notification) ? parseUnifiedContractDates($notification) : false;
    $startYmd = $contractDates ? $contractDates['start_date'] : '';
    $endYmd = $contractDates ? $contractDates['end_date'] : '';
    if ($contractDates) {
        normalizeAmcPostData($startYmd, $endYmd);
    }
    if (!$contractDates || !validateAmcLocationsMandatory($insert_attributes, $notification)) {
        return;
    }

    $additionalDocumentFileName = uploadContractAdditionalFiles('amc_additional_');
    $locationIds = extractAmcLocationIds();
    $milestones = buildAmcMilestonesForPost($startYmd, $endYmd);
    $editContractId = $isRenewFlow ? '' : $saveContractId;
    $paymentMode = $_POST['amc_payment_mode'] ?? 'billing_cycle';

    $sanitizedFormData = array(
        'contract_name' => $_POST['contract_name'] ?? '', 'transaction_no' => $_POST['transaction_no'] ?? '',
        'invoice_number' => $_POST['invoice_number'] ?? '', 'rt_number' => $_POST['rt_number'] ?? '',
        'business_code' => $_POST['business_code'] ?? '', 'parent_contract_id' => $_POST['parent_contract_id'] ?? '',
        'contract_payment_terms' => $_POST['contract_payment_terms'] ?? '', 'locations' => $locationIds,
        'typeofaggrements' => $_POST['typeofaggrements'] ?? '', 'party_type' => $_POST['party_type'] ?? '',
        'contract_owner_name' => $_POST['contract_owner_name'] ?? '', 'contract_owner_email' => $_POST['contract_owner_email'] ?? '',
        'contract_owner_number' => $_POST['contract_owner_number'] ?? '', 'party_id' => $_POST['party_id'] ?? '',
        'department_id' => $_POST['department_id'] ?? '', 'type_of_contract_id' => $_POST['type_of_contract_id'] ?? '',
        'start_date' => $startYmd, 'end_date' => $endYmd, 'renewal_frequency' => $_POST['renewal_frequency'] ?? 'quarterly',
        'contract_amount' => $_POST['contract_amount'] ?? '', 'description' => $_POST['description'] ?? '',
        'additional_notes' => $_POST['additional_notes'] ?? '', 'payment_term_id' => $_POST['payment_term_id'] ?? '',
        'termination_condition' => $_POST['termination_condition'] ?? '', 'contract_status' => $_POST['contract_status'] ?? 'Pending',
        'auto_renew' => isset($_POST['auto_renew']), 'renew_type' => $_POST['renew_type'] ?? '',
        'state' => $_POST['state'] ?? '', 'isp' => $_POST['isp'] ?? '', 'paid_by' => $_POST['paid_by'] ?? '',
        'internet_charges' => $_POST['internet_charges'] ?? '', 'hub_spoc_name' => $_POST['hub_spoc_name'] ?? '',
        'hub_spoc_number' => $_POST['hub_spoc_number'] ?? '', 'wifi_provider' => $_POST['wifi_provider'] ?? '',
        'wifi_pass' => $_POST['wifi_pass'] ?? '', 'additional_document' => $additionalDocumentFileName,
        'description_id' => $_POST['description_id'] ?? '', 'is_oem' => $_POST['is_oem'] ?? 'No',
        'oem_registration_number' => $_POST['oem_registration_number'] ?? '', 'oem_distributor_name' => $_POST['oem_distributor_name'] ?? '',
        'oem_product_category' => $_POST['oem_product_category'] ?? '', 'customer_connect_id' => $_POST['customer_connect_id'] ?? '',
        'amc_default_employee_id' => $_POST['amc_default_employee_id'] ?? '', 'amc_service_mode' => $_POST['amc_service_mode'] ?? '',
        'Rid' => $editContractId, 'edit_option' => $_POST['edit_option'] ?? 'SaveUpdate', 'is_amc_contract' => 'Y',
        'amc_payment_mode' => $paymentMode, 'amc_billing_cycle' => $_POST['amc_billing_cycle'] ?? '',
        'amc_billing_day' => $_POST['amc_billing_day'] ?? '', 'gate_entry' => $_POST['gate_entry'] ?? '',
        'reminder_days' => $_POST['reminder_days'] ?? array(), 'completion_req' => $_POST['completion_req'] ?? array(),
        'amc_loc' => $_POST['amc_loc'] ?? array(), 'amc_asset' => $_POST['amc_asset'] ?? array(),
        'amc_escalation_config' => $_POST['amc_escalation_config'] ?? array(), 'contract_services_id' => $_POST['contract_services_id'] ?? '',
        'contact_persons' => $_POST['contact_persons'] ?? '', 'milestones' => $milestones,
        'permission_data' => $permissionDetails, 'workflow_data' => $workflow, 'document_data' => $documentDetails,
        'contract_data' => $formData, 'version_id' => $versionId,
    );
    if ($editContractId != '' && trim((string) ($_POST['payment_term_id'] ?? '')) == '') {
        unset($sanitizedFormData['payment_term_id']);
    }

    try {
        $contractUrl = API . "?mode=amcinsertcontractsreact&method=post&user_id={$user_id}&company_id={$company_id}";
        $response = trim((string) file_post_contents($contractUrl, $sanitizedFormData, 'insert'));
        error_log("AMC API Response for " . $contractUrl . ": " . $response);
        processAmcSaveResponse($response, $editContractId, $notification);
    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while submitting the AMC contract!', 'type' => 'danger'];
    }
}

function validateAmcContractRenewalPreconditions($formData, &$notification)
{
    $renewPartyId = renewPostValue('party_id', $formData);
    $renewPartyType = renewPostValue('party_type', $formData);
    $isInhouseRenew = ($renewPartyType == 'inhouse') || (($_POST['amc_service_mode'] ?? '') == 'inhouse');
    if (!validateUnifiedContractBasics($notification) || (!$isInhouseRenew && empty($renewPartyId))) {
        if (!$isInhouseRenew && empty($renewPartyId) && empty($notification['message'])) {
            $notification = ['message' => 'Party is required.', 'type' => 'danger'];
        }
        return false;
    }
    return true;
}

function handleAmcContractRenewal()
{
    global $formData, $contract_id, $versionId;
    global $company_id, $user_id, $module_id, $notification, $insert_attributes;

    if (empty($contract_id) || !fetchContractIsApproved($contract_id)) {
        $msg = empty($contract_id) ? 'Contract id is required for renewal.' : MSG_CONTRACT_APPROVED_RENEW;
        $notification = ['message' => $msg, 'type' => 'danger'];
        return;
    }
    if (!validateAmcContractRenewalPreconditions($formData, $notification)) {
        return;
    }

    $contractDates = parseUnifiedContractDates($notification);
    $startYmd = $contractDates ? $contractDates['start_date'] : '';
    $endYmd = $contractDates ? $contractDates['end_date'] : '';
    if ($contractDates) {
        normalizeAmcPostData($startYmd, $endYmd, true);
    }
    if (!$contractDates || !validateAmcLocationsMandatory($insert_attributes, $notification)) {
        return;
    }

    $locationIds = extractAmcLocationIds();
    $milestones = buildAmcMilestonesForPost($startYmd, $endYmd);
    $paymentMode = $_POST['amc_payment_mode'] ?? 'billing_cycle';

    $renewalData = array(
        'contract_name' => renewPostValue('contract_name', $formData), 'transaction_no' => renewPostValue('transaction_no', $formData),
        'invoice_number' => renewPostValue('invoice_number', $formData), 'rt_number' => renewPostValue('rt_number', $formData),
        'business_code' => renewPostValue('business_code', $formData), 'parent_contract_id' => renewPostValue('parent_contract_id', $formData),
        'contract_payment_terms' => renewPostValue('contract_payment_terms', $formData), 'locations' => $locationIds,
        'typeofaggrements' => renewPostValue('typeofaggrements', $formData), 'party_type' => $renewPartyType ?? renewPostValue('party_type', $formData),
        'contract_owner_name' => renewPostValue('contract_owner_name', $formData), 'contract_owner_email' => renewPostValue('contract_owner_email', $formData),
        'contract_owner_number' => renewPostValue('contract_owner_number', $formData), 'party_id' => $renewPartyId ?? renewPostValue('party_id', $formData),
        'department_id' => renewPostValue('department_id', $formData), 'type_of_contract_id' => renewPostValue('type_of_contract_id', $formData),
        'start_date' => $startYmd, 'end_date' => $endYmd, 'renewal_frequency' => renewPostValue('renewal_frequency', $formData, 'quarterly'),
        'contract_amount' => renewPostValue('contract_amount', $formData), 'description' => renewPostValue('description', $formData),
        'additional_notes' => renewPostValue('additional_notes', $formData), 'payment_term_id' => renewPostValue('payment_term_id', $formData),
        'termination_condition' => renewPostValue('termination_condition', $formData), 'contract_status' => renewPostValue('contract_status', $formData, 'Pending'),
        'auto_renew' => isset($_POST['auto_renew']), 'renew_type' => renewPostValue('renew_type', $formData),
        'state' => renewPostValue('state', $formData), 'isp' => renewPostValue('isp', $formData),
        'paid_by' => renewPostValue('paid_by', $formData), 'internet_charges' => renewPostValue('internet_charges', $formData),
        'hub_spoc_name' => renewPostValue('hub_spoc_name', $formData), 'hub_spoc_number' => renewPostValue('hub_spoc_number', $formData),
        'wifi_provider' => renewPostValue('wifi_provider', $formData), 'wifi_pass' => renewPostValue('wifi_pass', $formData),
        'additional_document' => $_POST['all_additional_documents'] ?? '', 'description_id' => renewPostValue('description_id', $formData),
        'is_oem' => renewPostValue('is_oem', $formData, 'No'), 'oem_registration_number' => renewPostValue('oem_registration_number', $formData),
        'oem_distributor_name' => renewPostValue('oem_distributor_name', $formData), 'oem_product_category' => renewPostValue('oem_product_category', $formData),
        'customer_connect_id' => renewPostValue('customer_connect_id', $formData), 'amc_default_employee_id' => renewPostValue('amc_default_employee_id', $formData),
        'amc_service_mode' => renewPostValue('amc_service_mode', $formData), 'is_amc_contract' => 'Y',
        'amc_payment_mode' => $paymentMode, 'amc_billing_cycle' => $_POST['amc_billing_cycle'] ?? '',
        'amc_billing_day' => $_POST['amc_billing_day'] ?? '', 'gate_entry' => $_POST['gate_entry'] ?? '',
        'reminder_days' => $_POST['reminder_days'] ?? array(), 'completion_req' => $_POST['completion_req'] ?? array(),
        'amc_loc' => $_POST['amc_loc'] ?? array(), 'amc_asset' => $_POST['amc_asset'] ?? array(),
        'amc_escalation_config' => $_POST['amc_escalation_config'] ?? array(), 'contract_services_id' => renewPostValue('contract_services_id', $formData),
        'contact_persons' => renewPostValue('contact_persons', $formData), 'milestones' => $milestones,
        'Rid' => $contract_id, 'version_id' => $versionId, 'company_id' => $company_id, 'module_id' => $module_id, 'user_id' => $user_id,
    );

    try {
        $renewalUrl = API . "?mode=renewcontractreact&method=post&user_id={$user_id}&company_id={$company_id}";
        $response = file_post_contents($renewalUrl, $renewalData, 'renew');
        processAmcRenewalResponse($response, $notification);
    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while renewing the AMC contract!', 'type' => 'danger'];
    }
}

function validateSingleDocumentUploadFile($fileName, $fileSize, &$notification)
{
    $allowedTypes = ['pdf', 'xls', 'xlsx'];
    $maxSize = 10 * 1024 * 1024;
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes)) {
        $notification = ['message' => 'Only PDF or Excel files allowed!', 'type' => 'danger'];
        return false;
    }
    if ($fileSize > $maxSize) {
        $notification = ['message' => 'Each file must be 10 MB or smaller!', 'type' => 'danger'];
        return false;
    }
    return true;
}

function appendUploadedDocumentToHiddenField($hiddenKey, $newFileName, $originalFileName)
{
    $currentDocs = [];
    if (isset($_POST[$hiddenKey])) {
        $decoded = json_decode($_POST[$hiddenKey], true);
        $currentDocs = is_array($decoded) ? $decoded : [];
    }
    $currentDocs[] = ['id' => '', 'file_name' => $newFileName, 'comments' => $originalFileName, 'is_new' => true];
    $_POST[$hiddenKey] = json_encode($currentDocs);
}

function processSingleDocumentUpload($documentId, $fileData, $i, $uploadDir, &$notification)
{
    $fileName = $fileData['name'][$i];
    if (empty($fileName)) {
        return true;
    }
    if (!validateSingleDocumentUploadFile($fileName, $fileData['size'][$i], $notification)) {
        return false;
    }
    $newFileName = time() . "_doc{$documentId}_" . $i . "_" . basename($fileName);
    $moved = move_uploaded_file($fileData['tmp_name'][$i], $uploadDir . $newFileName);
    if (!$moved) {
        $notification = ['message' => "Failed to move file: $fileName", 'type' => 'danger'];
    } else {
        appendUploadedDocumentToHiddenField("all_documents_{$documentId}", $newFileName, $fileName);
    }
    return $moved;
}

function processUploadedContractDocuments($uploadDir, &$notification)
{
    foreach ($_FILES as $fileKey => $fileData) {
        if (strpos($fileKey, 'document_file_') === 0 && is_array($fileData['name'])) {
            $documentId = str_replace('document_file_', '', $fileKey);
            $fileCount = count($fileData['name']);
            for ($i = 0; $i < $fileCount; $i++) {
                if (!processSingleDocumentUpload($documentId, $fileData, $i, $uploadDir, $notification)) {
                    return false;
                }
            }
        }
    }
    return true;
}

function parseContractDocumentEntries($documentId, $rawJson)
{
    $parsed = [];
    $allDocs = json_decode($rawJson, true);
    if (is_array($allDocs)) {
        foreach ($allDocs as $doc) {
            $parsed[] = [
                'document_list_id' => $documentId,
                'comments' => $doc['comments'] ?? $doc['file_name'] ?? '',
                'Rid' => $doc['id'] ?? '',
                'file_name' => $doc['file_name'],
                'action' => !empty($doc['id']) && !isset($doc['is_new']) ? 'keep_existing' : 'new_upload'
            ];
        }
    }
    return $parsed;
}

function collectContractDocumentsFromPost(&$processedDocumentTypes)
{
    $documentsForJSON = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'all_documents_') === 0) {
            $documentId = str_replace('all_documents_', '', $key);
            $processedDocumentTypes[] = $documentId;
            $docEntries = parseContractDocumentEntries($documentId, $value);
            foreach ($docEntries as $entry) {
                $documentsForJSON[] = $entry;
            }
        }
    }
    return $documentsForJSON;
}

function handleDocumentSubmission()
{
    global $documentDetails, $permissionDetails, $workflow, $formData, $versionId, $contract_id;
    global $company_id, $data_name, $user_id, $module_id, $notification;

    $uploadDir = DIR_UPLOADS_CONTRACTS;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $processedDocumentTypes = [];

    if (!processUploadedContractDocuments($uploadDir, $notification)) {
        return;
    }

    $documentsForJSON = collectContractDocumentsFromPost($processedDocumentTypes);

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

    json_decode($response, true);

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
            'document_permissions' => (in_array('document', (array) $_POST['permissions'])) ? ($_POST['document_permissions'] ?? []) : [],
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
        file_post_contents($permUrl, $submissionData, 'insert');

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
        file_post_contents($workflowUrl, $submissionData, 'insert');

        $notification = ['message' => 'Workflow updated successfully!', 'type' => 'success'];

    } catch (Exception $error) {
        $notification = ['message' => 'An error occurred while updating workflow: ' . $error->getMessage(), 'type' => 'danger'];
    }
}
function loadActivityDetailsFallback($data = [])
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
$isObserver = false;

foreach ($editpermissionres as $permDetail) {
    if ($permDetail['user'] == $user_id) {
        $userPermissions = json_decode($permDetail['basic_permissions'], true) ?? [];
        $userDocPermissions = !empty($permDetail['document_permissions']) ? json_decode($permDetail['document_permissions'], true) : [];


        if (in_array('observer', (array) $userPermissions)) {
            $isObserver = true;
        }
        break;
    }
}

if ($admin_type == '1' || $editcontractsres['originator'] == $user_id) {
    $userPermissions = ['edit', 'document', 'workflows', 'activity', 'associated', 'permission', 'observer'];
    $userDocPermissions = array_column((array) $documents, 'id');
}

$readonlyAttr = ($isObserver && $admin_type != '1') ? 'readonly' : '';
$disabledAttr = ($isObserver && $admin_type != '1') ? 'disabled' : '';
$observerClass = $isObserver ? 'event_disabled' : '';


if ($rid != "" && $editcontractsres['is_amc_contract'] == 'Y') {
    $originatorId = $formData['originator'];
    $isOriginator = (!empty($user_id) && !empty($originatorId) && ((string) $user_id === (string) $originatorId));
    $canEditAmcStep1And3 = $isOriginator;
    $step13DisabledClass = !$canEditAmcStep1And3 ? 'event_disabled' : '';
}

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
$amc_is_edit = !empty($is_edit_mode);
$amc_is_renew_mode = !empty($is_renew_mode) && $amc_is_edit;
$amc_is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';
// LIVE PATCH: convert type
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
$billingScheduleServerPrefilled = ($amc_saved_payment_mode !== 'milestones' && count((array) $milestones) > 0);

if (count((array) $milestones) > 0) {
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
?>
<div class="card<?= $show_contract_type_toggle ? ' unified-contract-create-card' : '' ?>">
    <?php if (!empty($contract_id)) {
        echo $workflowsteps['current_status_ribbon'];
    } ?>
    <div class="card-header">
        <h5><?= __('Contracts'); ?>
            <?php if (!empty($formData['contract_type'])): ?>
                <span class="float-end ms-2 px-3 py-1 bg-light border rounded-pill text-primary fw-bold"
                    style="font-size: 0.85rem; vertical-align: middle; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <i class="fa fa-file-text-o me-1"></i> <?= htmlspecialchars($formData['contract_type']) ?>
                </span>
            <?php endif; ?>
        </h5>
        <div class="card-header-right table-card-header">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 me-5 desktop-view">
                <i class="icofont icofont-reply"></i><?= __('Back'); ?>
            </a>
            <?php if ($show_contract_type_toggle && empty($contract_form_locked) && !$amc_is_cancelled): ?>
            <?php endif; ?>
            <?php
            $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';
            $is_renewed = !empty($formData['renew_data']);
            if (!empty($contract_id) && !$is_cancelled && $contract_is_approved):
                ?>
                <?php if (!$is_renewed && !$is_renew_mode): ?>
                    <button type="button" class="btn btn-primary mb-2 desktop-view renew-contract-btn"
                        onclick="renewContractBTN()">
                        <?= __('Renew'); ?>
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-danger mb-2 ms-1 desktop-view" onclick="showCancelModal()">
                    <?= __('Cancel Contract'); ?>
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
                    <?php echo $workflowsteps['breadcrumb']; ?>
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
                        <div class="bg-danger px-4 py-4 d-flex align-items-center justify-content-center text-white"
                            style="min-width: 80px;">
                            <i class="fa fa-ban fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="mb-0 text-danger fw-bold uppercase tracking-wider" style="letter-spacing: 1px;">
                                    <?= __('Contract Cancelled'); ?></h5>
                                <span class="badge bg-danger rounded-pill px-3 py-2"><?= __('Inactive'); ?></span>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-8">
                                    <p class="mb-0 text-muted small"><strong class="text-dark"><?= __('Reason'); ?>:</strong>
                                        <span
                                            class="ms-1"><?= htmlspecialchars($formData['cancel_reason'] ?? 'Not specified') ?></span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <p class="mb-0 text-muted small"><strong class="text-dark"><?= __('Date'); ?>:</strong>
                                        <span
                                            class="ms-1"><?= !empty($formData['cancel_date']) ? fromDate($formData['cancel_date']) : 'N/A' ?></span>
                                    </p>
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
                    <span class="form-label d-block mb-2 fw-bold"><?= __('Contract Type'); ?></span>
                    <div class="contract-type-icon-grid" role="radiogroup" aria-label="<?= htmlspecialchars(__('Contract Type'), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php // Standard/AMC radios handled dynamically ?>

                        <?php if (count((array) $allowedContractTypes) > 0) {
                            foreach ($allowedContractTypes as $ctype) {
                                $ctypeArr = is_array($ctype) ? $ctype : json_decode(json_encode($ctype), true);
                                $ctypeCode = trim((string) ($ctypeArr['code'] ?? ''));
                                $ctypeLabel = (string) ($ctypeArr['label'] ?? '');
                                $ctypeClass = (string) ($ctypeArr['class'] ?? '');
                                if ($ctypeCode == '') {
                                    continue;
                                }
                                $ctypeId = 'page_contract_type_' . preg_replace('/[^a-zA-Z0-9_\-]/', '', $ctypeCode);
                                $ctypeChecked = ($page_contract_type == $ctypeCode) ? 'checked' : '';
                                $wrapExtra = '';
                                if ($ctypeCode == 'amc') {
                                    $wrapExtra = 'contract-type-icon-amc';
                                } elseif ($ctypeCode == 'standard') {
                                    $wrapExtra = 'contract-type-icon-standard';
                                }
                                ?>
                                <div class="contract-type-icon-card">
                                    <input type="radio" name="page_contract_type" id="<?= htmlspecialchars($ctypeId) ?>"
                                        value="<?= htmlspecialchars($ctypeCode) ?>" class="contract-type-icon-input"
                                        aria-label="<?= htmlspecialchars($ctypeLabel) ?>" <?= $ctypeChecked ?>>
                                    <label class="contract-type-icon-label" for="<?= htmlspecialchars($ctypeId) ?>"
                                        title="<?= htmlspecialchars($ctypeLabel) ?>">
                                        <span class="contract-type-icon-wrap <?= htmlspecialchars($wrapExtra) ?>">
                                            <i class="<?= htmlspecialchars($ctypeClass) ?>" aria-hidden="true"></i>
                                        </span>
                                        <span class="contract-type-icon-name"><?= htmlspecialchars($ctypeLabel) ?></span>
                                    </label>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        <?php elseif (!$is_edit_mode && !$is_renew_mode && count((array) $allowedContractTypes) == 0): ?>
            <div class="row mb-2">
                <div class="col-lg-12">
                    <div class="alert alert-warning mb-0" role="alert">
                        <?= __('No contract types assigned to your user. Please contact the administrator.'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($is_edit_mode && !$is_renew_mode): ?>
            <?php
            $requestedTab = isset($_GET['TAB1']) ? (int) $_GET['TAB1'] : 1;
            $allowedTabs = [1];
            if (in_array('document', (array) $userPermissions)) {
                $allowedTabs[] = 2;
            }
            if (in_array('associated', (array) $userPermissions)) {
                $allowedTabs[] = 3;
            }
            if (in_array('permission', (array) $userPermissions)) {
                $allowedTabs[] = 4;
            }
            if (in_array('activity', (array) $userPermissions)) {
                $allowedTabs[] = 5;
            }
            $activeTab = in_array($requestedTab, $allowedTabs) ? $requestedTab : 1;
            ?>
            <div class="row mb-2">
                <div class="col-lg-12">
                    <ul class="nav md-tabs infisuite_tab mb-2 unified-edit-tabs">
                        <li class="nav-item"><a onclick="updateUrlParameter('TAB1',1);"
                                class="nav-link <?= ($activeTab === 1) ? 'active' : '' ?> <?= $observerClass ?>" data-bs-toggle="pill"
                                href="#contractdetails"><?= __('Contract Details'); ?></a></li>
                        <?php if (in_array('document', (array) $userPermissions)): ?>
                            <li class="nav-item"><a onclick="updateUrlParameter('TAB1',2);"
                                    class="nav-link <?= ($activeTab === 2) ? 'active' : '' ?> <?= $observerClass ?>" data-bs-toggle="pill" href="#documents"><?= __('Documents'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (in_array('associated', (array) $userPermissions)): ?>
                            <li class="nav-item"><a onclick="updateUrlParameter('TAB1',3);"
                                    class="nav-link <?= ($activeTab === 3) ? 'active' : '' ?> <?= $observerClass ?>" data-bs-toggle="pill"
                                    href="#associatedcontract"><?= __('Associated Contract'); ?></a></li><?php endif; ?>
                        <?php if (in_array('permission', (array) $userPermissions)): ?>
                            <li class="nav-item"><a onclick="updateUrlParameter('TAB1',4);"
                                    class="nav-link <?= ($activeTab === 4) ? 'active' : '' ?> <?= $observerClass ?>" data-bs-toggle="pill"
                                    href="#permissions"><?= __('Permissions'); ?></a>
                            </li><?php endif; ?>
                        <?php if (in_array('activity', (array) $userPermissions)): ?>
                            <li class="nav-item"><a onclick="updateUrlParameter('TAB1',5);"
                                    class="nav-link <?= ($activeTab === 5) ? 'active' : '' ?> <?= $observerClass ?>" data-bs-toggle="pill"
                                    href="#activities"><?= __('Activities'); ?></a>
                            </li><?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="tab-content unified-edit-tab-content <?= $observerClass ?>">
                <div id="contractdetails" class="tab-pane <?= ($activeTab === 1) ? CLS_ACTIVE_SHOW : 'fade' ?> <?= $observerClass ?>">
                <?php endif; ?>
                <div id="unified-contract-page" class="<?= htmlspecialchars($formdisable) ?>">
                    <div class="page-body cc-wizard-page">
                        <form id="amcContractForm" method="post" enctype="multipart/form-data" action="">
                            <?php
                            $actionSubmitVal = 'submit_contract';
                            if ($amc_is_renew_mode) {
                                $actionSubmitVal = 'renew_amc_contract';
                            } elseif ($is_amc_mode) {
                                $actionSubmitVal = 'submit_amc_contract';
                            }
                            ?>
                            <input type="hidden" name="action" id="unified_contract_action"
                                value="<?= $actionSubmitVal ?>">
                            <input type="hidden" name="is_amc_contract" id="unified_is_amc_contract"
                                value="<?= $is_amc_mode ? 'Y' : 'N' ?>">
                            <input type="hidden" name="edit_option" id="unified_edit_option" value="SaveUpdate">
                            <input type="hidden" name="amc_service_mode" id="amc_service_mode"
                                value="<?= htmlspecialchars($amc_service_mode) ?>">
                            <input type="hidden" name="renewal_frequency"
                                value="<?= htmlspecialchars($formData['renewal_frequency'] ?? 'quarterly') ?>">
                            <input type="hidden" name="contract_status"
                                value="<?= htmlspecialchars($formData['contract_status'] ?? 'Pending') ?>">
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
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm <?= $step13DisabledClass ?>"
                                            id="btn-convert-contract-type"
                                            data-convert-url="<?= htmlspecialchars($convert_redirect_url) ?>"
                                            data-convert-confirm="<?= htmlspecialchars($convert_confirm_msg) ?>">
                                            <i class="icofont icofont-exchange"></i>
                                            <?= htmlspecialchars($convert_button_label) ?>
                                        </button>
                                        <script>
                                            (function () {
                                                var btn = document.getElementById('btn-convert-contract-type');
                                                if (!btn) { return; }
                                                btn.addEventListener('click', function () {
                                                    var msg = btn.getAttribute('data-convert-confirm') || 'Convert contract type?';
                                                    var url = btn.getAttribute('data-convert-url') || '';
                                                    if (url == '') { return; }

                                                    swal({
                                                        title: <?php echo json_encode(__('Convert Contract Type'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                                        text: msg,
                                                        type: "warning",
                                                        showCancelButton: true,
                                                        confirmButtonClass: "btn-danger",
                                                        confirmButtonText: <?php echo json_encode(__('Yes'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                                        cancelButtonText: <?php echo json_encode(__('No'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
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
                                                <i class="fa fa-refresh"></i> <?= __('Renew Contract'); ?>
                                            </button>
                                        <?php else: ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($notification['message'])): ?>
                                <div class="alert alert-<?= htmlspecialchars($notification['type'] ?: 'info') ?> alert-dismissible fade show mx-0 mt-2 mb-0"
                                    role="alert">
                                    <strong><?= htmlspecialchars($notification['message']) ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                                </div>
                            <?php endif; ?>

                            <div class="cc-wizard-layout">
                                <aside class="cc-wizard-sidebar">
                                    <div class="cc-wizard-steps-list">
                                        <a href="#" class="cc-wizard-step active" data-step="0">
                                            <span class="cc-step-number">1</span>
                                            <div class="cc-step-info">
                                                <div class="cc-step-title"><?= __('Contract Details'); ?></div>
                                                <div class="cc-step-desc"><?= __('Basic contract information'); ?></div>
                                            </div>
                                        </a>
                                        <a href="#" class="cc-wizard-step cc-amc-only" data-step="1"
                                            data-wizard-mode="amc">
                                            <span class="cc-step-number">2</span>
                                            <div class="cc-step-info">
                                                <div class="cc-step-title"><?= htmlspecialchars(__('Locations & Assets'), ENT_QUOTES, 'UTF-8'); ?></div>
                                                <div class="cc-step-desc"><?= htmlspecialchars(__('Locations, assets & service periods'), ENT_QUOTES, 'UTF-8'); ?></div>
                                            </div>
                                        </a>
                                        <a href="#" class="cc-wizard-step" data-step="2">
                                            <span class="cc-step-number">3</span>
                                            <div class="cc-step-info">
                                                <div class="cc-step-title"><?= __('Commercials'); ?></div>
                                                <div class="cc-step-desc"><?= htmlspecialchars(__('Contract amount & billing'), ENT_QUOTES, 'UTF-8'); ?></div>
                                            </div>
                                        </a>
                                        <a href="#" class="cc-wizard-step cc-amc-only" data-step="3"
                                            data-wizard-mode="amc">
                                            <span class="cc-step-number">4</span>
                                            <div class="cc-step-info">
                                                <div class="cc-step-title"><?= __('Service Setup'); ?></div>
                                                <div class="cc-step-desc"><?= htmlspecialchars(__('Reminders & service rules'), ENT_QUOTES, 'UTF-8'); ?></div>
                                            </div>
                                        </a>
                                        <a href="#" class="cc-wizard-step cc-amc-only" data-step="4"
                                            data-wizard-mode="amc">
                                            <span class="cc-step-number">5</span>
                                            <div class="cc-step-info">
                                                <div class="cc-step-title"><?= __('Escalations'); ?></div>
                                                <div class="cc-step-desc"><?= __('Escalation configuration'); ?></div>
                                            </div>
                                        </a>
                                        <a href="#" class="cc-wizard-step cc-amc-only" data-step="5"
                                            data-wizard-mode="amc">
                                            <span class="cc-step-number">6</span>
                                            <div class="cc-step-info">
                                                <div class="cc-step-title"><?= htmlspecialchars(__('Gate Entry & Service Completion'), ENT_QUOTES, 'UTF-8'); ?></div>
                                                <div class="cc-step-desc"><?= htmlspecialchars(__('Entry & exit requirements'), ENT_QUOTES, 'UTF-8'); ?></div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="cc-sidebar-box help-box">
                                        <h6><i class="icofont icofont-question-circle"></i> <?= __('Need Help?'); ?></h6>
                                        <p class="mb-0">
                                            <?= $amc_is_renew_mode ? __(__('Review renewal dates and amount, then click Renew Contract to create the new AMC period and schedules.')) : __(__('Complete all wizard sections, then use Submit Contract to save and generate the service schedule.')) ?>
                                        </p>
                                    </div>
                                    <div class="cc-sidebar-box amc-box cc-sidebar-amc-box cc-amc-only">
                                        <h6><i class="icofont icofont-file-document"></i> <?= __('AMC Contract'); ?></h6>
                                        <p class="mb-0"><?= __('Location-based asset mapping, service schedules, and escalation rules.'); ?></p>
                                    </div>
                                </aside>

                                <div class="cc-wizard-main">
                                    <div class="cc-section <?= $step13DisabledClass ?>" id="cc-step-1" data-step="0">
                                        <div class="cc-section-header">
                                            <h6><span class="cc-section-num">1</span><i
                                                    class="icofont icofont-file-document"></i> <?= __('Contract Details'); ?></h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4"
                                                style="<?= $fieldDisplay['parent_contract_id']['style'] ?>">
                                                <span class="form-label"><?= __('Parent/Associated Contract'); ?>
                                                    <?= $fieldDisplay['parent_contract_id']['star'] ?></span>
                                                <select class="form-control js-example-basic-single"
                                                    name="parent_contract_id"
                                                    <?= $fieldDisplay['parent_contract_id']['required'] ?> title="<?= __('Parent Contract Id') ?>" aria-label="<?= htmlspecialchars(__('Parent Contract Id'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value="0"><?= __('Select Associated Contract') ?></option>
                                                    <?php
                                                    $parentContractOptions = (array) ($contracts ?? []);
                                                    $selectedParentContractId = $formData['parent_contract_id'] ?? '';
                                                    if ($selectedParentContractId == '' && !empty($formData['parent_contract'])) {
                                                        $selectedParentContractId = $formData['parent_contract'];
                                                    }
                                                    $excludeContractId = (int) ($contract_id ?? $formData['id'] ?? 0);
                                                    if (count((array) $parentContractOptions) > 0) {
                                                        foreach ($parentContractOptions as $contractRow) {
                                                            $cRow = is_array($contractRow) ? $contractRow : json_decode(json_encode($contractRow), true);
                                                            $optionId = (int) ($cRow['id'] ?? 0);
                                                            if ($optionId <= 0 || ($excludeContractId > 0 && $optionId == $excludeContractId)) {
                                                                continue;
                                                            }
                                                            $sel = ((string) $selectedParentContractId === (string) $optionId) ? 'selected' : '';
                                                            $optionLabel = trim(($cRow['contract_name'] ?? '') . ' (' . fromDate($cRow['start_date'] ?? '') . ' To ' . fromDate($cRow['end_date'] ?? '') . ') -' . ($cRow['contract_type'] ?? ''));
                                                            ?>
                                                            <option value="<?= htmlspecialchars((string) $optionId) ?>" <?= $sel ?>><?= htmlspecialchars($optionLabel) ?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4"
                                                style="<?= $fieldDisplay['contract_name']['style'] ?>">
                                                <span class="form-label"><?= __('Contract Name'); ?>
                                                    <?= $fieldDisplay['contract_name']['star'] ?></span>
                                                <input aria-label="Contract Name" type="text" class="form-control" name="contract_name"
                                                    placeholder="<?= __('Enter contract name') ?>"
                                                    value="<?= htmlspecialchars($formData['contract_name'] ?? '') ?>"
                                                    <?= $fieldDisplay['contract_name']['required'] ?>
                                                    <?= $amc_renew_readonly ?> title="<?= __('Enter contract name') ?>" aria-label="<?= htmlspecialchars(__('Enter contract name'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-4"
                                                style="<?= $fieldDisplay['transaction_no']['style'] ?>">
                                                <span class="form-label"><?= __('Contract Number'); ?>
                                                    <?= $fieldDisplay['transaction_no']['star'] ?></span>
                                                <input aria-label="Transaction No" type="text" class="form-control" name="transaction_no"
                                                    placeholder="<?= __('Auto-generated') ?>"
                                                    value="<?= htmlspecialchars(dynamicDataDecode($formData['transaction_no'] ?? '')) ?>"
                                                    readonly title="<?= __('Auto-generated') ?>" aria-label="<?= htmlspecialchars(__('Auto-generated'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('RT Number'); ?>
                                                    <?= $fieldDisplay['rt_number']['star'] ?></span>
                                                <input aria-label="Rt Number" type="text" class="form-control" name="rt_number"
                                                    placeholder="<?= __('Enter RT Number') ?>"
                                                    value="<?= htmlspecialchars(dynamicDataDecode($formData['rt_number'] ?? '')) ?>"
                                                    <?= $fieldDisplay['rt_number']['required'] ?> <?= $amc_renew_readonly ?? '' ?> title="<?= __('Enter RT Number') ?>" aria-label="<?= htmlspecialchars(__('Enter RT Number'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Invoice Number'); ?>
                                                    <?= $fieldDisplay['invoice_number']['star'] ?></span>
                                                <input aria-label="Invoice Number" type="text" class="form-control" name="invoice_number"
                                                    placeholder="<?= __('Enter Invoice Number') ?>"
                                                    value="<?= htmlspecialchars(dynamicDataDecode($formData['invoice_number'] ?? '')) ?>"
                                                    <?= $fieldDisplay['invoice_number']['required'] ?> title="<?= __('Enter Invoice Number') ?>" aria-label="<?= htmlspecialchars(__('Enter Invoice Number'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Business Code'); ?>
                                                    <?= $fieldDisplay['business_code']['star'] ?></span>
                                                <input aria-label="Business Code" type="text" class="form-control" name="business_code"
                                                    placeholder="<?= __('Enter Business Code') ?>"
                                                    value="<?= htmlspecialchars($formData['business_code'] ?? '') ?>"
                                                    <?= $fieldDisplay['business_code']['required'] ?> title="<?= __('Enter Business Code') ?>" aria-label="<?= htmlspecialchars(__('Enter Business Code'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Department'); ?> <span
                                                        class="text-danger">*</span></span>
                                                <?php if ($amc_is_renew_mode && !empty($formData['department_id'])) { ?>
                                                    <input type="hidden" name="department_id"
                                                        value="<?= htmlspecialchars($formData['department_id']) ?>">
                                                <?php } ?>
                                                <select class="form-control js-example-basic-single"
                                                    name="<?= $amc_is_renew_mode ? 'department_id_display' : 'department_id' ?>"
                                                    id="amc_contract_department_id" <?= $amc_renew_disabled ?> title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select Department') ?></option>
                                                    <?php if (count((array) $departments) > 0) {
                                                        foreach ($departments as $dept) { ?>
                                                            <option value="<?= $dept['id'] ?>"
                                                                <?= (isset($formData['department_id']) && (string) $formData['department_id'] == (string) $dept['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['department_name']) ?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Contract Category') ?></span>
                                                <select class="form-control js-example-basic-single"
                                                    name="type_of_contract_id" <?= $amc_renew_disabled ?> title="<?= __('Type Of Contract Id') ?>" aria-label="<?= htmlspecialchars(__('Type Of Contract Id'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select Category') ?></option>
                                                    <?php if (count((array) $contractTypes) > 0) {
                                                        foreach ($contractTypes as $type) { ?>
                                                            <option value="<?= htmlspecialchars($type['genc_detail_code']) ?>"
                                                                <?= (isset($formData['type_of_contract_id']) && (string) $formData['type_of_contract_id'] == (string) ($type['genc_detail_code'] ?? '')) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars(__($type['genc_detail_name'])) ?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Payment Terms') ?></span>
                                                <select class="form-control js-example-basic-single"
                                                    name="contract_payment_terms" <?= $amc_renew_disabled ?> title="<?= __('Contract Payment Terms') ?>" aria-label="<?= htmlspecialchars(__('Contract Payment Terms'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select Payment Terms') ?></option>
                                                    <?php if (count((array) $payment_terms) > 0) {
                                                        foreach ($payment_terms as $term) {
                                                            $t = is_array($term) ? $term : json_decode(json_encode($term), true); ?>
                                                            <option value="<?= htmlspecialchars($t['id'] ?? '') ?>"
                                                                <?= (isset($formData['contract_payment_terms']) && (string) $formData['contract_payment_terms'] == (string) ($t['id'] ?? '')) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($t['payment_term_name'] ?? '') ?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>


                                            <?php
                                            include_once __DIR__ . '/_contract_agreement_detail_fields.php';
                                            include_once __DIR__ . '/_contract_services_contact_fields.php';
                                            $contract_agreement_section = 'classification';
                                            renderContractAgreementDetailFields(array(
                                                'contract_agreement_section' => $contract_agreement_section,
                                                'contract_agreement_locations_section' => $contract_agreement_locations_section ?? '',
                                                'fieldDisplay' => $fieldDisplay ?? array(),
                                                'genc' => $genc ?? array(),
                                                'des' => $des ?? array(),
                                                'locations' => $locations ?? array(),
                                                'selectedLocations' => $selectedLocations ?? array(),
                                                'formData' => $formData ?? array(),
                                            )); ?>
                                            <?php $contract_services_contact_section = 'services';
                                            renderContractServicesContactFields(array(
                                                'contract_services_contact_section' => $contract_services_contact_section,
                                                'formData' => $formData ?? array(),
                                                'fieldDisplay' => $fieldDisplay ?? array(),
                                                'amc_renew_disabled' => $amc_renew_disabled ?? '',
                                                'contract_services' => $contract_services ?? array(),
                                            )); ?>
                                            <div class="col-md-12">
                                                <span class="form-label d-block"><?= __('Party Type'); ?> <span
                                                        class="text-danger">*</span></span>
                                                <div class="radio radio-outline radio-inline">
                                                    <input id="amc_customer" type="radio" <?= $amc_is_renew_mode ? '' : ATTR_PARTY_TYPE ?> value="customer"
                                                        <?= $amc_party_type == 'customer' ? 'checked' : '' ?>
                                                        <?= $amc_renew_disabled ?> title="<?= __('Party Type') ?>" aria-label="<?= htmlspecialchars(__('Party Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <label class="form-label align-top"
                                                        for="amc_customer"><?= __('Customer') ?></label>
                                                </div>
                                                <div class="radio radio-outline radio-inline ms-3">
                                                    <input id="amc_vendor" type="radio" <?= $amc_is_renew_mode ? '' : ATTR_PARTY_TYPE ?> value="vendor" <?= ($amc_party_type != 'customer' && $amc_party_type != 'inhouse') ? 'checked' : '' ?>
                                                        <?= $amc_renew_disabled ?> title="<?= __('Party Type') ?>" aria-label="<?= htmlspecialchars(__('Party Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <label class="form-label align-top" for="amc_vendor"><?= __('Vendor') ?></label>
                                                </div>
                                                <div class="radio radio-outline radio-inline ms-3 cc-amc-only">
                                                    <input id="amc_inhouse" type="radio" <?= $amc_is_renew_mode ? '' : ATTR_PARTY_TYPE ?> value="inhouse"
                                                        <?= $amc_party_type == 'inhouse' ? 'checked' : '' ?>
                                                        <?= $amc_renew_disabled ?> title="<?= __('Party Type') ?>" aria-label="<?= htmlspecialchars(__('Party Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <label class="form-label align-top"
                                                        for="amc_inhouse"><?= __('In-house') ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-12" id="amc_party_name_box"
                                                style="<?= $amc_is_inhouse ? CSS_DISPLAY_NONE_SEMI : '' ?>">
                                                <span class="form-label"><?= __('Party Name'); ?> <span
                                                        class="text-danger amc-party-required">*</span></span>
                                                <?php if ($amc_is_renew_mode) { ?>
                                                    <input type="hidden" name="party_type"
                                                        value="<?= htmlspecialchars($amc_party_type) ?>">
                                                    <input type="hidden" name="party_id"
                                                        value="<?= htmlspecialchars($amc_party_id != '' ? $amc_party_id : '0') ?>">
                                                    <?php if ($amc_is_inhouse) { ?>
                                                        <input type="hidden" name="amc_service_mode" value="inhouse">
                                                    <?php } ?>
                                                <?php } ?>
                                                <select class="form-control js-example-basic-single"
                                                    name="<?= $amc_is_renew_mode ? 'party_id_display' : 'party_id' ?>"
                                                    id="amc_party_id"
                                                    data-selected="<?= htmlspecialchars($amc_party_id) ?>"
                                                    <?= $amc_renew_disabled ?> title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select a Party') ?></option>
                                                    <?php if (count((array) $customers) > 0) {
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
                                                                data-shipping-address="<?= htmlspecialchars($partyShipping) ?>">
                                                                <?= htmlspecialchars($label) ?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                                <div id="party-details-preview"
                                                    class="mt-2 mb-3 p-3 border rounded bg-light d-none"
                                                    style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); border-left: 4px solid #4e73df !important;">
                                                    <h6 class="mb-3 text-primary border-bottom pb-2"
                                                        style="font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; font-weight: 700;">
                                                        <i class="fa fa-user-circle me-1"></i> <?= __('Party Details'); ?>
                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2">
                                                            <small class="text-muted d-block text-uppercase"
                                                                style="font-size: 0.65rem; font-weight: 600;"><?= __('Name'); ?></small>
                                                            <span id="preview-party-name"
                                                                class="fw-bold text-dark small">—</span>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <small class="text-muted d-block text-uppercase"
                                                                style="font-size: 0.65rem; font-weight: 600;"><?= __('Email'); ?></small>
                                                            <span id="preview-party-email"
                                                                class="fw-bold text-dark small">—</span>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <small class="text-muted d-block text-uppercase"
                                                                style="font-size: 0.65rem; font-weight: 600;"><?= __('Phone'); ?></small>
                                                            <span id="preview-party-phone"
                                                                class="fw-bold text-dark small">—</span>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <small class="text-muted d-block text-uppercase"
                                                                style="font-size: 0.65rem; font-weight: 600;"><?= __('BP Code'); ?></small>
                                                            <span id="preview-bp-code"
                                                                class="fw-bold text-dark small">—</span>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <small class="text-muted d-block text-uppercase"
                                                                style="font-size: 0.65rem; font-weight: 600;"><?= __('Billing Address'); ?></small>
                                                            <span id="preview-billing-address"
                                                                class="fw-bold text-dark small">—</span>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <small class="text-muted d-block text-uppercase"
                                                                style="font-size: 0.65rem; font-weight: 600;"><?= __('Shipping Address'); ?></small>
                                                            <span id="preview-shipping-address"
                                                                class="fw-bold text-dark small">—</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                    id="unified-add-party-btn"><i class="fa fa-plus-circle me-1"></i>
                                                    <?= __('Add New Party'); ?></button>
                                            </div>
                                            <?php
                                            $amc_show_customer_connect = $is_amc_mode && $amc_party_type != 'customer' && $amc_party_type != 'inhouse';
                                            ?>
                                            <div class="col-md-4 align-self-start cc-amc-only"
                                                id="amc_customer_connect_box"
                                                style="<?= $amc_show_customer_connect ? '' : CSS_DISPLAY_NONE_SEMI ?>">
                                                <span class="form-label"><?= __('Customer Connect (Facility Manager)') ?></span>
                                                <select class="form-control js-example-basic-single"
                                                    name="customer_connect_id" id="amc_customer_connect_id"
                                                    data-selected="<?= htmlspecialchars($amc_customer_connect_id) ?>" title="<?= __('Customer Connect Id') ?>" aria-label="<?= htmlspecialchars(__('Customer Connect Id'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select Customer Connect') ?></option>
                                                    <?php if (count((array) $users) > 0) {
                                                        foreach ($users as $connectRow) {
                                                            $connect = is_array($connectRow) ? $connectRow : json_decode(json_encode($connectRow), true);
                                                            $cid = $connect['id'] ?? '';
                                                            $clabel = trim($connect['display_name'] ?? $connect['user_name'] ?? ('Customer ' . $cid));
                                                            $cselected = ((string) $amc_customer_connect_id === (string) $cid) ? 'selected' : ''; ?>
                                                            <option value="<?= htmlspecialchars($cid) ?>" <?= $cselected ?>>
                                                                <?= htmlspecialchars($clabel) ?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                                <div class="form-text amc-field-help-text mb-1"><?= __('Site contact who confirms vendor visits after job completion'); ?></div>
                                            </div>
                                            <div class="col-md-4 cc-amc-only" id="amc_default_employee_box"
                                                style="<?= $amc_is_inhouse ? '' : CSS_DISPLAY_NONE_SEMI ?>">
                                                <span class="form-label"><?= __('Default Employee') ?></span>
                                                <select class="form-control js-example-basic-single"
                                                    name="amc_default_employee_id" id="amc_default_employee_id"
                                                    data-selected="<?= htmlspecialchars($amc_default_employee_id) ?>" title="<?= __('Amc Default Employee Id') ?>" aria-label="<?= htmlspecialchars(__('Amc Default Employee Id'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select Employee (optional)') ?></option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Contract Owner Name') ?></span>
                                                <input aria-label="Contract Owner Name" type="text" class="form-control" name="contract_owner_name"
                                                    placeholder="<?= __('Enter contract owner name') ?>"
                                                    value="<?= htmlspecialchars(dynamicDataDecode($formData['contract_owner_name'] ?? '')) ?>" title="<?= __('Enter contract owner name') ?>" aria-label="<?= htmlspecialchars(__('Enter contract owner name'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Contract Owner Email') ?></span>
                                                <input aria-label="Contract Owner Email" type="email" class="form-control" name="contract_owner_email"
                                                    placeholder="<?= __('Enter contract owner email') ?>"
                                                    value="<?= htmlspecialchars(dynamicDataDecode($formData['contract_owner_email'] ?? '')) ?>" title="<?= __('Enter contract owner email') ?>" aria-label="<?= htmlspecialchars(__('Enter contract owner email'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Contract Owner Contact') ?></span>
                                                <input aria-label="Contract Owner Number" type="tel" class="form-control" name="contract_owner_number"
                                                    placeholder="<?= __('Enter contract owner contact number') ?>"
                                                    value="<?= htmlspecialchars(dynamicDataDecode($formData['contract_owner_number'] ?? '')) ?>" title="<?= __('Enter contract owner contact number') ?>" aria-label="<?= htmlspecialchars(__('Enter contract owner contact number'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <?php $contract_services_contact_section = 'contact_persons';
                                            renderContractServicesContactFields(array(
                                                'contract_services_contact_section' => $contract_services_contact_section,
                                                'formData' => $formData ?? array(),
                                                'fieldDisplay' => $fieldDisplay ?? array(),
                                                'amc_renew_disabled' => $amc_renew_disabled ?? '',
                                                'contract_services' => $contract_services ?? array(),
                                            )); ?>
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Effective(Start) Date'); ?> <span
                                                        class="text-danger">*</span></span>
                                                <?php
                                                $defStartVal = !empty($formData['start_date']) ? fromDate($formData['start_date']) : '';
                                                $startDisplayVal = $amc_renew_start_display != '' ? htmlspecialchars($amc_renew_start_display) : $defStartVal;
                                                ?>
                                                <input type="text"
                                                    class="form-control startdatepicker_2 rangedatepicker " data-id="2"
                                                    name="start_date"
                                                    value="<?= $startDisplayVal ?>"
                                                    autocomplete="off" <?= $amc_is_renew_mode ? 'readonly style="background-color:#f8f9fa;"' : '' ?> title="<?= __('Start Date') ?>" aria-label="<?= htmlspecialchars(__('Start Date'), ENT_QUOTES, 'UTF-8'); ?>">

                                            </div>


                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Expiration(End) Date'); ?> <span
                                                        class="text-danger">*</span></span>
                                                <?php
                                                $defEndVal = !empty($formData['end_date']) ? fromDate($formData['end_date']) : '';
                                                $endDisplayVal = $amc_renew_end_display != '' ? htmlspecialchars($amc_renew_end_display) : $defEndVal;
                                                ?>
                                                <input aria-label="End Date" type="text"
                                                    class="form-control amc-end-datepicker enddatepicker_2"
                                                    name="end_date"
                                                    value="<?= $endDisplayVal ?>"
                                                    autocomplete="off" title="<?= __('End Date') ?>" aria-label="<?= htmlspecialchars(__('End Date'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <?php $contract_agreement_section = 'extended';
                                            $contract_agreement_locations_section = 'Y';
                                            renderContractAgreementDetailFields(array(
                                                'contract_agreement_section' => $contract_agreement_section,
                                                'contract_agreement_locations_section' => $contract_agreement_locations_section,
                                                'fieldDisplay' => $fieldDisplay ?? array(),
                                                'genc' => $genc ?? array(),
                                                'des' => $des ?? array(),
                                                'locations' => $locations ?? array(),
                                                'selectedLocations' => $selectedLocations ?? array(),
                                                'formData' => $formData ?? array(),
                                            )); ?>
                                            <div class="col-md-12">
                                                <span class="form-label"><?= __('Contract Description') ?></span>
                                                <textarea class="form-control" name="description" rows="3"
                                                    placeholder="<?= __('Enter contract description') ?>" title="<?= __('Enter contract description') ?>" aria-label="<?= htmlspecialchars(__('Enter contract description'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <span class="form-label"><?= __('Contract Document') ?></span>
                                                <div class="cc-upload-zone">
                                                    <i class="icofont icofont-cloud-upload"></i>
                                                    <p><?= __('Click to upload or drag and drop'); ?></p>
                                                    <small class="text-muted cc-upload-filename"></small>
                                                </div>
                                                <input type="file" id="amc_contract_document"
                                                    name="additional_document_file[]" class="d-none"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple title="<?= __('Additional Document File') ?>" aria-label="<?= htmlspecialchars(__('Additional Document File'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <div id="amc_additional_file_buttons"
                                                    class="cc-uploaded-files mt-2 d-flex flex-column gap-2">
                                                    <?php
                                                    $amc_saved_documents = $formData['additional_document'] ?? '';
                                                    if (!empty($amc_saved_documents)) {
                                                        $amc_document_files = explode(',', $amc_saved_documents);
                                                        if (count((array) $amc_document_files) > 0) {
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
                                                                <div class="cc-uploaded-file-item d-flex align-items-center justify-content-between gap-2"
                                                                    data-filename="<?= htmlspecialchars($amc_doc_file) ?>">
                                                                    <span class="text-truncate"
                                                                        title="<?= htmlspecialchars($amc_display_name) ?>">
                                                                        <i class="icofont icofont-file-document"></i>
                                                                        <?= htmlspecialchars($amc_display_name) ?>
                                                                    </span>
                                                                    <div class="d-flex gap-1 flex-shrink-0">
                                                                        <a href="uploads/contracts/<?= htmlspecialchars($amc_doc_file) ?>"
                                                                            target="_blank" rel="noopener"
                                                                            class="btn btn-sm btn-outline-info"
                                                                            title="View <?= htmlspecialchars($amc_display_name) ?>">
                                                                            <i class="fa fa-eye m-0"></i>
                                                                        </a>
                                                                        <a href="uploads/contracts/<?= htmlspecialchars($amc_doc_file) ?>"
                                                                            download class="btn btn-sm btn-outline-primary"
                                                                            title="Download <?= htmlspecialchars($amc_display_name) ?>">
                                                                            <i class="fa fa-download m-0"></i>
                                                                        </a>
                                                                        <?php // Additional document removal handled on save ?>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <input type="hidden" name="all_additional_documents"
                                                    id="amc_all_additional_documents"
                                                    value="<?= htmlspecialchars($amc_saved_documents) ?>">
                                                <small class="text-muted d-block mt-1"><?= __('PDF, Word, Excel, Images (Max 10MB each)'); ?></small>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="cc-amc-banner cc-amc-only">
                                                    <i class="icofont icofont-check-circled"></i> <?= __('AMC Enabled — Location-based asset mapping and service scheduling are active.'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cc-section cc-amc-sections cc-amc-only" id="cc-step-2" data-step="1">
                                        <div class="cc-section-header">
                                            <h6><span class="cc-section-num">2</span><i
                                                    class="icofont icofont-location-pin"></i> <?= htmlspecialchars(__('Locations, Assets & Service Periods'), ENT_QUOTES, 'UTF-8'); ?></h6>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                id="amc-add-location-btn">
                                                <i class="icofont icofont-plus"></i> <?= __('Add Location'); ?>
                                            </button>
                                        </div>
                                        <div id="amc_locations_container"></div>
                                    </div>

                                    <div class="cc-section <?= $step13DisabledClass ?>" id="cc-step-3" data-step="2">
                                        <div class="cc-section-header">
                                            <h6><span class="cc-section-num">3</span><i
                                                    class="icofont icofont-money"></i> <?= __('Commercials'); ?></h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <span class="form-label"><?= __('Contract Amount'); ?> <span
                                                        class="text-danger">*</span></span>
                                                <input type="number" step="any" class="form-control text-end"
                                                    name="contract_amount" id="amc_contract_amount"
                                                    placeholder="<?= __('Enter Contract Amount') ?>"
                                                    value="<?= htmlspecialchars($formData['contract_amount'] ?? '') ?>" title="<?= __('Enter Contract Amount') ?>" aria-label="<?= htmlspecialchars(__('Enter Contract Amount'), ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                            <div class="col-md-8">
                                                <span class="form-label d-block"><?= __('Payment Plan'); ?></span>
                                                <div class="cc-inline-radios">
                                                    <span><input aria-label="Amc Payment Mode" type="radio" name="amc_payment_mode"
                                                            value="billing_cycle" <?= $amc_saved_payment_mode !== 'milestones' ? 'checked' : '' ?> title="<?= __('Amc Payment Mode') ?>" aria-label="<?= htmlspecialchars(__('Amc Payment Mode'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __('Billing Cycle'); ?></span>
                                                    <span><input aria-label="Amc Payment Mode" type="radio" name="amc_payment_mode"
                                                            value="milestones" <?= $amc_saved_payment_mode === 'milestones' ? 'checked' : '' ?> title="<?= __('Amc Payment Mode') ?>" aria-label="<?= htmlspecialchars(__('Amc Payment Mode'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __('Milestones'); ?></span>
                                                </div>
                                            </div>
                                            <?php if ($is_renew_mode): ?>
                                                <div class="col-md-12 cc-renewal-history-panel"
                                                    id="renewal-amount-history-panel">
                                                    <div class="cc-renewal-history-header"><?= __('Renewal Amount History'); ?></div>
                                                    <p class="cc-renewal-history-subtitle text-muted small mb-2"><?= __('Shown during renewal — compares each period vs the previous year.'); ?></p>
                                                    <div id="renewal-amount-history-list"></div>
                                                    <p class="text-muted small mb-0"><?= __('Current year trend updates when you change Contract Amount.'); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="col-md-12 unified-billing-cycle-block amc-billing-cycle-block">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <span class="form-label"><?= __('Billing Cycle') ?></span>
                                                        <select class="form-control js-example-basic-single"
                                                            name="amc_billing_cycle" id="unified_billing_cycle" title="<?= __('Amc Billing Cycle') ?>" aria-label="<?= htmlspecialchars(__('Amc Billing Cycle'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <option value="monthly"
                                                                <?= $amc_saved_billing_cycle === 'monthly' ? 'selected' : '' ?>><?= __('Monthly') ?></option>
                                                            <option value="quarterly"
                                                                <?= $amc_saved_billing_cycle === 'quarterly' || $amc_saved_billing_cycle === '' ? 'selected' : '' ?>><?= __('Quarterly') ?></option>
                                                            <option value="half_yearly"
                                                                <?= $amc_saved_billing_cycle === 'half_yearly' ? 'selected' : '' ?>><?= __('Half-Yearly') ?></option>
                                                            <option value="yearly"
                                                                <?= $amc_saved_billing_cycle === 'yearly' ? 'selected' : '' ?>><?= __('Yearly') ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <span class="form-label"><?= __('Billing Date (Day)') ?></span>
                                                        <input type="number" class="form-control text-end"
                                                            name="amc_billing_day" id="unified_billing_day"
                                                            value="<?= htmlspecialchars((string) $amc_saved_billing_day) ?>"
                                                            min="1" max="31" title="<?= __('Amc Billing Day') ?>" aria-label="<?= htmlspecialchars(__('Amc Billing Day'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 unified-milestones-block amc-milestones-block"
                                                id="unified-milestones-editor">
                                                <span class="form-label d-block"><?= __('Milestones'); ?></span>
                                                <div id="unified-milestone-container">
                                                    <?php
                                                    $unifiedMilestoneIdx = 0;
                                                    $prefillMilestones = ($amc_saved_payment_mode === 'milestones' && count((array) $milestones) > 0);
                                                    if ($prefillMilestones) {
                                                        foreach ($milestones as $m) {
                                                            $unifiedMilestoneIdx++;
                                                            $mName = $m['milestone'] ?? $m['milestone_name'] ?? '';
                                                            $mDate = !empty($m['milestone_date']) ? fromDate($m['milestone_date']) : '';
                                                            $mPrice = $m['milestone_price'] ?? '';
                                                            ?>
                                                            <div class="milestone-row mb-2 d-flex align-items-center gap-2">
                                                                <input aria-label="Contract input" type="text"
                                                                    name="milestone_name_<?= $unifiedMilestoneIdx ?>"
                                                                    class="form-control milestone-name"
                                                                    placeholder="<?= __('Milestone Name') ?>"
                                                                    value="<?= htmlspecialchars($mName) ?>" title="<?= __('Milestone Name') ?>" aria-label="<?= htmlspecialchars(__('Milestone Name'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input aria-label="Contract input" type="text"
                                                                    name="milestone_date_<?= $unifiedMilestoneIdx ?>"
                                                                    class="form-control datepicker milestone-date"
                                                                    placeholder="<?= __('Date') ?>" value="<?= htmlspecialchars($mDate) ?>" title="<?= __('Date') ?>" aria-label="<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <input aria-label="Contract input" type="number" step="any"
                                                                    name="milestone_price_<?= $unifiedMilestoneIdx ?>"
                                                                    class="form-control milestone-price text-end"
                                                                    placeholder="<?= __('Price') ?>"
                                                                    value="<?= htmlspecialchars($mPrice) ?>" title="<?= __('Price') ?>" aria-label="<?= htmlspecialchars(__('Price'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm remove-milestone"
                                                                    title="<?= __('Remove milestone') ?>">&times;</button>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        $unifiedMilestoneIdx = 1;
                                                        ?>
                                                        <div class="milestone-row mb-2 d-flex align-items-center gap-2">
                                                            <input aria-label="Milestone Name 1" type="text" name="milestone_name_1"
                                                                class="form-control milestone-name"
                                                                placeholder="<?= __('Milestone Name') ?>" title="<?= __('Milestone Name') ?>" aria-label="<?= htmlspecialchars(__('Milestone Name'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <input aria-label="Milestone Date 1" type="text" name="milestone_date_1"
                                                                class="form-control datepicker milestone-date"
                                                                placeholder="<?= __('Date') ?>" title="<?= __('Date') ?>" aria-label="<?= htmlspecialchars(__('Date'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <input aria-label="Milestone Price 1" type="number" step="any" name="milestone_price_1"
                                                                class="form-control milestone-price text-end"
                                                                placeholder="<?= __('Price') ?>" title="<?= __('Price') ?>" aria-label="<?= htmlspecialchars(__('Price'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-milestone"
                                                                title="<?= __('Remove milestone') ?>">&times;</button>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                                    id="unified-add-milestone"><?= __('+ Add Milestone') ?></button>
                                                <div id="unified-milestones-total-row"
                                                    class="mt-3 p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                                    <span class="fw-semibold"><?= __('Total'); ?></span>
                                                    <span class="fw-bold" id="unified-milestones-total">0.00</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 unified-billing-schedule-block"
                                                id="unified-billing-schedule-block">
                                                <span class="form-label d-block"><?= __('Billing Schedule'); ?></span>

                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle mb-0"
                                                        id="unified-billing-schedule-table">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th scope="col"><?= __('Period') ?></th>
                                                                <th scope="col" class="text-end"><?= __('Amount') ?></th>
                                                                <th scope="col"><?= __('Date') ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="unified-billing-schedule-body"
                                                            <?= $billingScheduleServerPrefilled ? 'data-server-prefilled="1"' : '' ?>>
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
                                                                        <td class="text-end">
                                                                            <?= htmlspecialchars($periodPrice) ?>
                                                                        </td>
                                                                        <td><?= htmlspecialchars($periodDate) ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="table-light fw-semibold">
                                                                <td><?= __('Total'); ?></td>
                                                                <td class="text-end"
                                                                    id="unified-billing-schedule-total">
                                                                    <?= number_format($billingScheduleTotal, 2, '.', '') ?>
                                                                </td>
                                                                <td>—</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                <p id="unified-billing-schedule-hint"
                                                    class="text-muted small mt-2 mb-0<?= ($amc_saved_payment_mode !== 'milestones' && count((array) $milestones) > 0) ? ' d-none' : '' ?>">
                                                    <?= __('Set'); ?> <strong><?= __('Effective(Start) Date'); ?></strong> <?= __('and'); ?>
                                                    <strong><?= __('Expiration(End) Date'); ?></strong> <?= __('in Contract Details to generate the billing schedule.'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 cc-section-pair-row cc-amc-only">
                                        <div class="col-md-6">
                                            <div class="cc-section h-100 mb-0 cc-amc-only" id="cc-step-4" data-step="3">
                                                <div class="cc-section-header">
                                                    <h6><span class="cc-section-num">4</span><i
                                                            class="icofont icofont-settings"></i> <?= __('Service Setup'); ?></h6>
                                                </div>
                                                <span class="form-label d-block"><?= __('Service Reminder'); ?></span>
                                                <div class="cc-inline-checks">
                                                    <span><input aria-label="Reminder Days" type="checkbox" name="reminder_days[]" value="7"
                                                            <?= in_array('7', $amc_saved_reminder_days, true) ? 'checked' : '' ?> title="<?= __('Reminder Days') ?>" aria-label="<?= htmlspecialchars(__('Reminder Days'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __('7 days before'); ?></span>
                                                    <span><input aria-label="Reminder Days" type="checkbox" name="reminder_days[]" value="15"
                                                            <?= in_array('15', $amc_saved_reminder_days, true) ? 'checked' : '' ?> title="<?= __('Reminder Days') ?>" aria-label="<?= htmlspecialchars(__('Reminder Days'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __('15 days before'); ?></span>
                                                    <span><input aria-label="Reminder Days" type="checkbox" name="reminder_days[]" value="30"
                                                            <?= in_array('30', $amc_saved_reminder_days, true) ? 'checked' : '' ?> title="<?= __('Reminder Days') ?>" aria-label="<?= htmlspecialchars(__('Reminder Days'), ENT_QUOTES, 'UTF-8'); ?>"> <?= __('30 days before'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="cc-section h-100 mb-0 cc-amc-only" id="cc-step-5" data-step="4">
                                                <div class="cc-section-header">
                                                    <h6><span class="cc-section-num">5</span><i
                                                            class="icofont icofont-warning"></i> <?= __('Escalations'); ?></h6>
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                        id="amc-add-escalation-btn">
                                                        <i class="icofont icofont-plus"></i> <?= __('Add Escalation'); ?>
                                                    </button>
                                                </div>
                                                <div id="amc-escalation-container">
                                                    <?php foreach ($amc_escalations as $escIdx => $escRow):
                                                        $condition = $escRow['condition'] ?? 'not_started';
                                                        $hours = (int) ($escRow['hours'] ?? 4);
                                                        $notifyRole = $escRow['notify_role'] ?? 'service_manager';
                                                        ?>
                                                        <div class="cc-escalation-level"
                                                            data-esc-idx="<?= (int) $escIdx ?>">
                                                            <span class="level-badge"><?= __('Level'); ?> <?= (int) $escIdx + 1 ?></span>
                                                            <div class="row g-2 align-items-center">
                                                                <div class="col-auto text-muted small">
                                                                    <?= htmlspecialchars($amc_condition_labels[$condition] ?? $amc_condition_labels['not_started']) ?>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <input type="hidden"
                                                                        name="amc_escalation_config[<?= (int) $escIdx ?>][condition]"
                                                                        value="<?= htmlspecialchars($condition) ?>">
                                                                    <input aria-label="Contract input" type="number"
                                                                        class="form-control form-control-sm amc-esc-hours text-end"
                                                                        name="amc_escalation_config[<?= (int) $escIdx ?>][hours]"
                                                                        value="<?= $hours ?>" min="1" max="720"
                                                                        style="width:70px;" title="<?= __('Input') ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                </div>
                                                                <div class="col-auto text-muted small"><?= __('hours, Notify'); ?></div>
                                                                <div class="col-md-5">
                                                                    <select
                                                                        class="form-control form-control-sm js-example-basic-single amc-esc-notify"
                                                                        name="amc_escalation_config[<?= (int) $escIdx ?>][notify_role]" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                        <?php foreach ($amc_notify_roles as $roleKey => $roleLabel): ?>
                                                                            <option value="<?= htmlspecialchars($roleKey) ?>"
                                                                                <?= $notifyRole === $roleKey ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($roleLabel) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <button type="button"
                                                                        class="btn btn-link btn-sm text-danger p-0 amc-remove-escalation"
                                                                        title="<?= __('Remove level') ?>">&times;</button>
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
                                                    <h6><span class="cc-section-num">6</span><i
                                                            class="icofont icofont-lock"></i> <?= htmlspecialchars(__('Gate Entry & Service Completion'), ENT_QUOTES, 'UTF-8'); ?></h6>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <span class="form-label d-block"><?= __('Gate entry required for service execution?'); ?></span>
                                                    <div class="cc-inline-radios">
                                                        <span><input aria-label="Gate Entry" type="radio" name="gate_entry" value="yes"
                                                                <?= $amc_saved_gate_entry !== 'no' ? 'checked' : '' ?> title="<?= __('Gate Entry') ?>" aria-label="<?= htmlspecialchars(__('Gate Entry'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?= __('Yes'); ?></span>
                                                        <span><input aria-label="Gate Entry" type="radio" name="gate_entry" value="no"
                                                                <?= $amc_saved_gate_entry === 'no' ? 'checked' : '' ?> title="<?= __('Gate Entry') ?>" aria-label="<?= htmlspecialchars(__('Gate Entry'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?= __('No'); ?></span>
                                                    </div>
                                                </div>
                                                <span class="form-label d-block"><?= __('Service Completion Requirements'); ?></span>
                                                <div class="cc-inline-checks d-flex flex-column gap-2">
                                                    <span><input aria-label="Completion Req" type="checkbox" name="completion_req[]"
                                                            value="service_report" <?= in_array('service_report', $amc_saved_completion_rules, true) ? 'checked' : '' ?> title="<?= __('Completion Req') ?>" aria-label="<?= htmlspecialchars(__('Completion Req'), ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?= __('Service Report upload mandatory'); ?></span>
                                                    <span><input aria-label="Completion Req" type="checkbox" name="completion_req[]"
                                                            value="technician_signoff" <?= in_array('technician_signoff', $amc_saved_completion_rules, true) ? 'checked' : '' ?> title="<?= __('Completion Req') ?>" aria-label="<?= htmlspecialchars(__('Completion Req'), ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?= __('Technician Sign-off'); ?></span>
                                                    <span><input aria-label="Completion Req" type="checkbox" name="completion_req[]"
                                                            value="customer_signoff" <?= in_array('customer_signoff', $amc_saved_completion_rules, true) ? 'checked' : '' ?> title="<?= __('Completion Req') ?>" aria-label="<?= htmlspecialchars(__('Completion Req'), ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?= __('Customer Sign-off'); ?></span>
                                                    <span><input aria-label="Completion Req" type="checkbox" name="completion_req[]"
                                                            value="photo_upload" <?= in_array('photo_upload', $amc_saved_completion_rules, true) ? 'checked' : '' ?> title="<?= __('Completion Req') ?>" aria-label="<?= htmlspecialchars(__('Completion Req'), ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?= __('Photo Upload'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="cc-section cc-summary-section h-100 mb-0">
                                                <div class="cc-section-header">
                                                    <h6><i class="icofont icofont-chart-pie"></i> <?= __('Summary'); ?></h6>
                                                </div>
                                                <div id="amc-summary-content" class="small text-muted"><?= __('Fill the form sections above to see a summary before submit.'); ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cc-wizard-footer d-flex justify-content-start gap-2">
                                        <?php if (empty($contract_form_locked) && !$amc_is_cancelled): ?>
                                            <?php if ($amc_is_renew_mode): ?>
                                                <button type="button" class="btn btn-primary cc-btn-submit">
                                                    <i class="fa fa-refresh"></i> <?= __('Renew Contract'); ?>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-primary cc-btn-submit">
                                                    <i class="icofont icofont-check"></i> <?= __('Submit Contract'); ?>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div><!-- #unified-contract-page -->
                <div id="amc-php-data" style="<?= CSS_DISPLAY_NONE_SEMI ?>">
                    <div id="amc-assets-data">
                        <?= htmlspecialchars(json_encode(array_values((array) $amc_assets)), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div id="amc-service-frequencies-data">
                        <?= htmlspecialchars(json_encode($amc_service_frequencies), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div id="amc-edit-data">
                        <?= htmlspecialchars(json_encode($amc_contract_data ?? array()), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <template id="amc-escalation-level-template">
                    <div class="cc-escalation-level" data-esc-idx="">
                        <span class="level-badge"><?= __('Level 1'); ?></span>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <select class="form-select form-select-sm amc-esc-condition" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <option value="not_started"><?= __('If service not started within') ?></option>
                                    <option value="not_completed"><?= __('If service not completed within') ?></option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <input aria-label="Contract input" type="number" class="form-control form-control-sm amc-esc-hours text-end"
                                    value="4" min="1" max="720" style="width:70px;" title="<?= __('Input') ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-auto text-muted small"><?= __('hours, Notify'); ?></div>
                            <div class="col-md-5">
                                <select class="form-select form-select-sm js-example-basic-single amc-esc-notify" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <option value="service_manager"><?= __('Service Manager') ?></option>
                                    <option value="regional_manager"><?= __('Regional Manager') ?></option>
                                    <option value="contract_owner"><?= __('Contract Owner') ?></option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 amc-remove-escalation"
                                    title="<?= __('Remove level') ?>">&times;</button>
                            </div>
                        </div>
                    </div>
                </template>

                <template id="amc-location-template">
                    <div class="card mb-3 amc-location-block">
                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0 amc-location-title"><?= __('Location 1') ?></h6>
                            <button type="button" class="btn btn-danger btn-sm amc-remove-location"
                                title="<?= __('Remove Location') ?>">
                                <i class="ti-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col"><?= __('Location') ?></th>
                                            <th scope="col"><?= __('Service Start Date') ?></th>
                                            <th scope="col"><?= __('Service End Date') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="min-width: 220px;">
                                                <select class="form-select js-example-basic-single amc-location-select" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select Location') ?></option>
                                                    <?php if (count((array) $locations) > 0) {
                                                        foreach ($locations as $location) {
                                                            $loc = is_array($location) ? $location : json_decode(json_encode($location), true); ?>
                                                            <option value="<?= htmlspecialchars($loc['id'] ?? '') ?>">
                                                                <?= htmlspecialchars($loc['location_name'] ?? '') ?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </td>
                                            <td><input aria-label="Contract input" type="date" class="form-control    amc-loc-service-start" title="<?= __('Input') ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                            <td><input aria-label="Contract input" type="date" class="form-control  amc-loc-service-end" title="<?= __('Input') ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <h6 class="mb-0"><?= __('Assets Mapping') ?></h6>
                                    <div class="d-flex flex-wrap gap-2 align-items-end">
                                        <div class="d-flex flex-wrap gap-2 align-items-end amc-add-by-category-wrap">
                                            <div style="min-width:160px;">
                                                <span class="form-label f-12 mb-1"><?= __('Category'); ?></span>
                                                <select
                                                    class="form-select form-select-sm js-example-basic-single amc-bulk-category-select" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select category') ?></option>
                                                    <?php if (count((array) $amc_asset_categories) > 0) {
                                                        foreach ($amc_asset_categories as $catRow) {
                                                            $cat = is_array($catRow) ? $catRow : json_decode(json_encode($catRow), true); ?>
                                                            <option value="<?= htmlspecialchars($cat['id'] ?? '') ?>">
                                                                <?= htmlspecialchars($cat['category_description'] ?? '') ?>
                                                            </option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm amc-add-by-category-btn">
                                                <i class="icofont icofont-plus"></i> <?= __('Add by category'); ?>
                                            </button>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm amc-add-asset-btn">
                                            <i class="icofont icofont-plus"></i> <?= __('Add Asset'); ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm f-12 amc-assets-table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col"><?= __('Asset No.') ?></th>
                                                <th scope="col"><?= __('Asset Name') ?></th>
                                                <th scope="col"><?= __('Category') ?></th>
                                                <th scope="col"><?= __('Service Frequency') ?></th>
                                                <th scope="col"><?= __('Visits / Interval') ?></th>
                                                <th scope="col"><?= __('Service Start') ?></th>
                                                <th scope="col"><?= __('Service End') ?></th>
                                                <th scope="col" class="text-center"><?= __('Action') ?></th>
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
                            <select class="form-select js-example-basic-single amc-asset-select" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                <option value=""><?= __('Select location first') ?></option>
                            </select>
                        </td>
                        <td><input aria-label="Contract input" type="text" class="form-control amc-asset-name" readonly placeholder="<?= __('Asset Name') ?>" title="<?= __('Asset Name') ?>" aria-label="<?= htmlspecialchars(__('Asset Name'), ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <td><input aria-label="Contract input" type="text" class="form-control amc-asset-category" readonly placeholder="<?= __('Category') ?>" title="<?= __('Category') ?>" aria-label="<?= htmlspecialchars(__('Category'), ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <td style="min-width: 160px;">
                            <select class="form-select js-example-basic-single amc-service-frequency-select" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                <option value=""><?= __('Select Frequency') ?></option>
                                <?php foreach ($amc_service_frequencies as $freq) {
                                    $fr = is_array($freq) ? $freq : json_decode(json_encode($freq), true); ?>
                                    <option value="<?= htmlspecialchars($fr['genc_detail_code'] ?? '') ?>">
                                        <?= htmlspecialchars(__($fr['genc_detail_name']) ?? '') ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="min-width: 90px;">
                            <input aria-label="Contract input" type="number" class="form-control amc-visits-per-interval text-end" min="1" max="31"
                                value="1" title="<?= __('Visits per interval') ?>" aria-label="<?= htmlspecialchars(__('Visits per interval'), ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <td><input aria-label="Contract input" type="date" class="form-control amc-asset-service-start" readonly tabindex="-1" title="<?= __('Input') ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <td><input aria-label="Contract input" type="date" class="form-control amc-asset-service-end" readonly tabindex="-1" title="<?= __('Input') ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>"></td>
                        <td class="text-center text-nowrap">
                            <?php if ($amc_is_edit): ?>
                                <span class="amc-schedule-actions">
                                    <button type="button" class="btn btn-link btn-sm p-1 amc-view-history"
                                        title="<?= __('Planned Schedule') ?>"><i class="ti-time text-info f-16"></i></button>
                                    <button type="button" class="btn btn-link btn-sm p-1 amc-service-calendar-btn"
                                        title="<?= __('Service Calendar') ?>"><i class="ti-calendar text-primary f-16"></i></button>
                                </span>
                            <?php endif; ?>
                            <button type="button" class="btn btn-link btn-sm p-1 text-danger amc-remove-asset"
                                title="<?= __('Remove') ?>"><i class="ti-trash f-16"></i></button>
                        </td>
                    </tr>
                    <tr class="amc-asset-slots-row d-none">
                        <td colspan="8" class="bg-light py-2">
                            <div class="amc-slot-config small">
                                <span class="text-muted me-2 amc-slot-config-label"><?= __('Visit slots'); ?>:</span>
                                <div class="amc-slot-fields d-flex flex-wrap gap-2 align-items-end"></div>
                            </div>
                        </td>
                    </tr>
                </template>

                <div class="modal fade" id="amcAssetHistoryModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= __('Planned Schedule —'); ?> <span id="amcHistoryAssetLabel"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                            </div>
                            <div class="modal-body custom-scroll" id="amcAssetHistoryBody" style="max-height: 600px;">
                                <p class="text-center text-muted mb-0"><?= __('Loading schedule…'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="amcServiceCalendarModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= __('Service Calendar —'); ?> <span id="amcCalendarAssetLabel"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                            </div>
                            <div class="modal-body custom-scroll" style="max-height: 600px;">
                                <div id="amc-service-calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($is_edit_mode && !$is_renew_mode): ?>
                </div><!-- #contractdetails -->
                <div id="documents" class="tab-pane <?= ($activeTab === 2) ? CLS_ACTIVE_SHOW : 'fade' ?> <?= $observerClass ?>">
                    <form method="POST" enctype="multipart/form-data" id="documentsForm">
                        <input type="hidden" name="action" value="submit_documents">

                        <!-- Hidden inputs for contract data -->
                        <input type="hidden" id="hidden_contract_name" name="contract_name" value="">
                        <input type="hidden" id="hidden_transaction_no" name="transaction_no" value="">
                        <input type="hidden" id="hidden_invoice_number" name="invoice_number" value="">
                        <input type="hidden" id="hidden_rt_number" name="rt_number" value="">
                        <input type="hidden" id="hidden_business_code" name="business_code" value="">
                        <input type="hidden" id="hidden_parent_contract_id" name="parent_contract_id" value="">
                        <input type="hidden" id="hidden_contract_payment_terms" name="contract_payment_terms" value="">
                        <input type="hidden" id="hidden_typeofaggrements" name="typeofaggrements" value="">
                        <input type="hidden" id="hidden_contract_owner_name" name="contract_owner_name" value="">
                        <input type="hidden" id="hidden_contract_owner_email" name="contract_owner_email" value="">
                        <input type="hidden" id="hidden_contract_owner_number" name="contract_owner_number" value="">
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
                        <input type="hidden" id="hidden_termination_condition" name="termination_condition" value="">
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
                        <input type="hidden" id="hidden_oem_registration_number" name="oem_registration_number" value="">
                        <input type="hidden" id="hidden_oem_distributor_name" name="oem_distributor_name" value="">
                        <input type="hidden" id="hidden_oem_product_category" name="oem_product_category" value="">
                        <div id="hidden_milestones_container"></div>

                        <div class="row">
                            <?php
                            foreach($document_parent as $parent){
                                $parent_id  = $parent['id']; ?>
                            <div class="col-lg-12 mb-3">
                                <h5><?php echo htmlspecialchars($parent['document_name']); ?> </h5>
                            </div>
                           <?php  foreach ($documents as $document): ?>
                                <?php
                                if($document['parent_document_id']==$parent_id ){
                                if ($admin_type !== '1' && !in_array($document['id'], (array) $userDocPermissions)) {
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
                                        <span
                                            class="form-label d-block d-block"><?= htmlspecialchars($document['document_name']) ?></span>
                                        <div class="col-lg-12 mb-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <input aria-label="Document File &lt;?= $document[" type="file" name="document_file_<?= $document['id'] ?>[]"
                                                    class="form-control document-file-input" multiple accept=".pdf,.xls,.xlsx"
                                                    data-document-id="<?= $document['id'] ?>"
                                                    data-document-name="<?= htmlspecialchars($document['document_name']) ?>"
                                                    <?= $disabledAttr ?> title="<?= __('Input') ?>" aria-label="<?= htmlspecialchars(__('Input'), ENT_QUOTES, 'UTF-8'); ?>">
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
                                            <small class="text-muted"><?= __('PDF, Excel (Max 10MB each)'); ?></small>

                                            <!-- Single hidden input to store all files for this document type -->
                                            <input type="hidden" name="all_documents_<?= $document['id'] ?>"
                                                id="all_documents_<?= $document['id'] ?>"
                                                value="<?= htmlspecialchars(json_encode($matchedDocuments)) ?>">
                                        </div>

                                    </div>
                                </div>
                            <?php } endforeach;
                              }
                            ?>
                            <div class="col-lg-12">
                                <?php
                                $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';
                                if (!empty($contract_id) && !$is_cancelled):
                                    ?>
                                    <button type="submit" class="btn btn-primary"><?= __('Submit Documents') ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div><!-- #documents -->
                <div id="associatedcontract" class="tab-pane <?= ($activeTab === 3) ? CLS_ACTIVE_SHOW : 'fade' ?> <?= $observerClass ?>">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="bg-white p-4 shadow-sm d-flex flex-column align-items-center text-center">
                                <div class="table-responsive w-100">
                                    <?php if (!empty($associatedContracts)): ?>
                                        <table class="table table-custom table-bordered-full table-hover m-0">
                                            <tbody>
                                                <?php foreach ($associatedContracts as $contract): ?>
                                                    <tr class="bg-gray">
                                                        <th scope="col" colspan="4"
                                                            class="py-2 px-4 text-left text-lg font-semibold text-gray-800">
                                                            <?= htmlspecialchars($contract['contract_name']) ?>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="py-3 px-5 text-center"><?= __('Contract ID') ?></th>
                                                        <th scope="col" class="py-3 px-5 text-center"><?= __('Contract Name') ?></th>
                                                        <th scope="col" class="py-3 px-5 text-center"><?= __('Effective Date') ?></th>
                                                        <th scope="col" class="py-3 px-5 text-center"><?= __('Expiration Date') ?></th>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-3 px-5 text-gray-700">
                                                            <?= renderSensitiveDataWithToggle($contract['transaction_no'], 'text') ?>
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
                                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> <?= __('No Associated Contract'); ?></p>
                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-300px mx-h-300 mx-height-300" alt="<?= htmlspecialchars(__('Work in progress'), ENT_QUOTES, 'UTF-8'); ?>"
                                                    src="assets/images/work.png">
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="permissions" class="tab-pane <?= ($activeTab === 4) ? CLS_ACTIVE_SHOW : 'fade' ?> <?= $observerClass ?>">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (!$isObserver || $admin_type == '1'): ?>
                                <form method="POST" id="permissionsForm">
                                    <input type="hidden" name="action" value="submit_permissions">
                                    <input type="hidden" name="prid" id="permission_rid" value="">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <div class="form-group w-full">
                                                <span class="form-label d-block"><?= __('Select Location'); ?> <span
                                                        class="text-danger">*</span></span>
                                                <select class="form-select js-example-basic-single"
                                                    id="permissionLocationSelect" title="<?= __('Select') ?>" aria-label="<?= htmlspecialchars(__('Select'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select Location') ?></option>
                                                    <?php if (!empty($locations)): ?>
                                                        <?php foreach ($locations as $loc):
                                                            $locRow = is_array($loc) ? $loc : (array) $loc;
                                                            $locId = $locRow['id'] ?? '';
                                                            $locName = $locRow['location_name'] ?? ('Location ' . $locId);
                                                            ?>
                                                            <option value="<?= htmlspecialchars($locId) ?>">
                                                                <?= htmlspecialchars($locName) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <small class="text-muted permission-edit-user-hint d-none"><?= __('Location cannot be changed when editing an existing permission.'); ?></small>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <div class="form-group w-full">
                                                <span class="form-label d-block"><?= __('Select User'); ?> <span
                                                        class="text-danger">*</span></span>
                                                <select class="form-select js-example-basic-single" name="selected_user"
                                                    id="permissionUserSelect" required title="<?= __('Selected User') ?>" aria-label="<?= htmlspecialchars(__('Selected User'), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <option value=""><?= __('Select User') ?></option>
                                                    <?php
                                                    $assignedUsers = array_column($permissionDetails ?? [], 'user');
                                                    foreach ($users as $user):
                                                        $uRow = is_array($user) ? $user : (array) $user;
                                                        $userId = $uRow['id'] ?? '';
                                                        $userName = htmlspecialchars($uRow['display_name'] ?? $uRow['user_name'] ?? $uRow['name'] ?? "User {$userId}");
                                                        $userLocId = $uRow['location_id'] ?? '';
                                                        $isAssigned = in_array($userId, $assignedUsers);
                                                        $disabled = $isAssigned ? 'disabled' : '';
                                                        ?>
                                                        <option value="<?= $userId ?>" <?= $disabled ?>
                                                            data-assigned="<?= $isAssigned ? '1' : '0' ?>"
                                                            data-location-id="<?= htmlspecialchars($userLocId) ?>">
                                                            <?= $userName ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small class="text-muted permission-edit-user-hint d-none"><?= __('User cannot be changed when editing an existing permission.'); ?></small>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3">
                                            <div class="form-group">
                                                <span class="form-label d-block"><?= __('Select Contract Permission'); ?></span>
                                                <div class="form-group">
                                                    <?php
                                                    $permissionTypes = ['edit', 'document', 'activity', 'associated', 'permission', 'observer'];
                                                    foreach ($permissionTypes as $perm):
                                                        ?>
                                                        <div class="checkbox-section d-inline-block me-3">
                                                            <input aria-label="Permissions" class="form-check-input" type="checkbox" name="permissions[]"
                                                                value="<?= $perm ?>" id="<?= $perm ?>" title="<?= __('Permissions') ?>" aria-label="<?= htmlspecialchars(__('Permissions'), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <label class="form-check-label" for="<?= $perm ?>">
                                                                <?= ucfirst($perm) ?> <?= __('Permission'); ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3 d-none" id="documentPermissionsSection">
                                            <div class="form-group">
                                                <span class="form-label d-block"><?= __('Select Document Permissions'); ?></span>
                                                <div class="form-group">
                                                    <?php if (!empty($documents)): ?>
                                                        <?php foreach ($documents as $doc): ?>
                                                            <div class="checkbox-section d-inline-block me-3">
                                                                <input aria-label="Document Permissions" class="form-check-input doc-checkbox" type="checkbox"
                                                                    name="document_permissions[]" value="<?= $doc['id'] ?>"
                                                                    id="doc-<?= $doc['id'] ?>" title="<?= __('Document Permissions') ?>" aria-label="<?= htmlspecialchars(__('Document Permissions'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                <label class="form-check-label" for="doc-<?= $doc['id'] ?>">
                                                                    <?= htmlspecialchars($doc['document_name'] ?? $doc['name'] ?? "Document {$doc['id']}") ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted"><?= __('No documents available. Please add documents first.'); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">

                                            <?php
                                            $is_cancelled = !empty($formData['cancel_status']) && $formData['cancel_status'] == 'Y';
                                            if (!empty($contract_id) && !$is_cancelled):
                                                ?>
                                                <button type="submit" class="btn btn-primary" id="permissionSubmitBtn"><?= __('Submit Permissions') ?></button>
                                                <button type="button"
                                                    class="btn btn-secondary ms-2 d-none cancel-permission-edit-btn"
                                                    id="cancelEditBtn" onclick="cancelPermissionEdit()"><?= __('Cancel Edit') ?></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>

                            <!-- Display existing permissions -->
                            <?php if (!empty($permissionDetails)): ?>
                                <div class="mt-4">
                                    <h5><?= __('Existing Permissions') ?></h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th scope="col"><?= __('User') ?></th>
                                                    <th scope="col"><?= __('Permissions') ?></th>
                                                    <th scope="col"><?= __('Document Permissions') ?></th>
                                                    <th scope="col"><?= __('Actions') ?></th>
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
                                                            <?php
                                                            $permUserLocId = '';
                                                            foreach ($users as $u) {
                                                                $uArr = is_array($u) ? $u : (array) $u;
                                                                if ($uArr['id'] == $permission['user']) {
                                                                    $permUserLocId = $uArr['location_id'] ?? '';
                                                                    break;
                                                                }
                                                            }
                                                            $permissionWithLoc = $permission;
                                                            $permissionWithLoc['location_id'] = $permUserLocId;
                                                            ?>
                                                            <button type="button" class="btn btn-sm btn-warning edit-permission-btn"
                                                                data-permission="<?= htmlspecialchars(json_encode($permissionWithLoc)) ?>">
                                                                <i class="fa fa-edit"></i> <?= __('Edit'); ?>
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
                <div class="tab-pane latest-update-card <?= ($activeTab === 5) ? CLS_ACTIVE_SHOW : 'fade' ?> <?= $observerClass ?>" id="activities" role="tabpanel">
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
                                                    <?= __('The contract activity "'); ?><strong><?= strtoupper($activity['activity'] ?? 'ACTIVITY') ?></strong><?= __('" was updated on'); ?>
                                                    <?= date('m/d/Y', strtotime($activity['activity_date'] ?? 'now')) ?>
                                                    <?= __('at'); ?>
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
                                            <h6><?= __('No Activity Found') ?></h6>
                                            <p class="text-muted m-b-0"><?= __('There are no recorded activities for this contract.'); ?></p>
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
        <div class="modal fade" id="addPartyModal" aria-labelledby="addPartyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPartyModalLabel"><?= __('Add New'); ?> <span id="partyTypeText"><?= __('Party'); ?></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addPartyForm" method="POST" class="add-party-form">
                            <input type="hidden" name="action" value="add_party">
                            <input type="hidden" name="party_type" id="modalPartyType" value="">

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <span class="form-label d-block"><span id="partyTypeText2"><?= __('Party'); ?></span> <?= __('Name'); ?>
                                            <span class="text-danger">*</span></span>
                                        <input aria-label="Party Name" type="text" name="party_name" class="form-control"
                                            placeholder="<?= __('Enter party name') ?>" required title="<?= __('Enter party name') ?>" aria-label="<?= htmlspecialchars(__('Enter party name'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="form-group mb-3">
                                        <span class="form-label d-block"><?= __('Email'); ?> <span
                                                class="text-danger">*</span></span>
                                        <input aria-label="Email" type="email" name="email" class="form-control" placeholder="<?= __('Enter email') ?>"
                                            required title="<?= __('Enter email') ?>" aria-label="<?= htmlspecialchars(__('Enter email'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="form-group mb-3">
                                        <span class="form-label d-block"><?= __('Phone'); ?> <span
                                                class="text-danger">*</span></span>
                                        <input aria-label="Phone" type="text" name="phone" class="form-control"
                                            placeholder="<?= __('Enter phone number') ?>" required title="<?= __('Enter phone number') ?>" aria-label="<?= htmlspecialchars(__('Enter phone number'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group mb-3">
                                        <span class="form-label d-block"><?= __('Company'); ?> <span
                                                class="text-danger">*</span></span>
                                        <input aria-label="Company" type="text" name="company" class="form-control"
                                            placeholder="<?= __('Enter company name') ?>" required title="<?= __('Enter company name') ?>" aria-label="<?= htmlspecialchars(__('Enter company name'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="form-group mb-3">
                                        <span class="form-label d-block"><?= __('Address'); ?> <span
                                                class="text-danger">*</span></span>
                                        <textarea name="address" class="form-control" rows="3"
                                            placeholder="<?= __('Enter address') ?>" required title="<?= __('Enter address') ?>" aria-label="<?= htmlspecialchars(__('Enter address'), ENT_QUOTES, 'UTF-8'); ?>"></textarea>
                                    </div>
                                    <div class="form-group mb-3">
                                        <span class="form-label d-block"><?= __('BP Code'); ?> <span
                                                class="text-danger">*</span></span>
                                        <input aria-label="Bp Code" type="text" name="bp_code" class="form-control"
                                            placeholder="<?= __('Enter BP Code') ?>" required title="<?= __('Enter BP Code') ?>" aria-label="<?= htmlspecialchars(__('Enter BP Code'), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('Cancel') ?></button>
                            <button type="submit" class="btn btn-primary"> <?= __('Add'); ?> <span id="partyTypeText3"></span> <?= __('Party'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple Recharge Plan Modal -->
        <?php if ($is_edit_mode): ?>
            <div class="modal fade" id="rechargeModal" tabindex="-1" aria-labelledby="rechargeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rechargeModalLabel"><?= __('Select Recharge Plan') ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                        </div>
                        <form id="rechargeForm">
                            <div class="modal-body">
                                <div class="form-group mb-3  w-full">
                                    <label for="subscriptionType" class="form-label d-block"><?= __('Select Plan'); ?></label>
                                    <select class="form-select js-example-basic-single" id="subscriptionType"
                                        name="subscription_type" title="<?= __('Subscription Type') ?>" aria-label="<?= htmlspecialchars(__('Subscription Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                        <option value="30"><?= __('Monthly') ?></option>
                                        <option value="90"><?= __('Quarterly') ?></option>
                                        <option value="180"><?= __('Half Yearly') ?></option>
                                        <option value="365"><?= __('Yearly') ?></option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="activationDate" class="form-label d-block"><?= __('Activation Date'); ?> <span
                                            class="text-danger">*</span></label>
                                    <input type="date" id="activationDate" name="activation_date" class="form-control"
                                        required title="<?= __('Activation Date') ?>" aria-label="<?= htmlspecialchars(__('Activation Date'), ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <input type="hidden" name="contract_id" value="<?= $contract_id ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('Cancel') ?></button>
                                <button type="submit" class="btn btn-primary"><?= __('Save') ?></button>
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
                            <h5 class="modal-title" id="rechargeHistoryModalLabel"><?= __('Recharge History') ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col"><?= __('S.N') ?></th>
                                            <th scope="col"><?= __('Recharge Date') ?></th>
                                            <th scope="col"><?= __('Activation Date') ?></th>
                                            <th scope="col"><?= __('Expiry Date') ?></th>
                                            <th scope="col"><?= __('Plan Type') ?></th>
                                            <th scope="col"><?= __('Remaining Days') ?></th>
                                            <th scope="col"><?= __('Status') ?></th>
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
                        <h5 class="modal-title" id="editConfirmationModalLabel"><?= __('Save Options') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                    </div>
                    <div class="modal-body">
                        <p><?= __('How would you like to save these changes?'); ?></p>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary text-start"
                                onclick="submitWithOption('Amendment')">
                                <strong><?= __('1. Amendment'); ?></strong><br>
                                <small><?= __('Create an Amendment for this contract.'); ?></small>
                            </button>
                            <button type="button" class="btn btn-outline-warning text-start"
                                onclick="submitWithOption('Addendum')">
                                <strong><?= __('2. Addendum'); ?></strong><br>
                                <small><?= __('Create an Addendum for this contract.'); ?></small>
                            </button>
                            <button type="button" class="btn btn-outline-secondary text-start"
                                onclick="submitWithOption('SaveUpdate')">
                                <strong><?= htmlspecialchars(__('3. Save & Update'), ENT_QUOTES, 'UTF-8'); ?></strong><br>
                                <small><?= __('Standard update of current contract data.'); ?></small>
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
                        <h5 class="modal-title" id="cancelContractModalLabel"><?= __('Cancel Contract') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                    </div>
                    <div class="modal-body">
                        <p><?= __('Are you sure you want to cancel this contract? This action cannot be undone.'); ?></p>
                        <div class="form-group">
                            <label for="cancel_reason"><?= __('Reason for Cancellation'); ?> <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="cancel_reason" rows="3"
                                placeholder="<?= __('Please enter the reason...') ?>" title="<?= __('Please enter the reason...') ?>" aria-label="<?= htmlspecialchars(__('Please enter the reason...'), ENT_QUOTES, 'UTF-8'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('Close') ?></button>
                        <button type="button" class="btn btn-danger" onclick="submitCancellation()"><?= __('Confirm Cancellation') ?></button>
                    </div>
                </div>
            </div>
        </div>


        <!-- SweetAlert2 CDN -->

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
            <div id="unified-locations-required">
                <?php echo isContractFieldMandatory('locations', $insert_attributes) ? 'true' : 'false'; ?>
            </div>
            <div id="is-renew-mode"><?php echo $is_renew_mode ? 'true' : 'false'; ?></div>
            <div id="renewal-amount-history-data">
                <?php echo json_encode($renewalAmountHistoryData ?? array('periods' => array(), 'new_period_start' => '', 'new_period_end' => '')); ?>
            </div>
            <div id="contract-id"><?php echo $contract_id ?? 0; ?></div>
            <div id="company-id"><?php echo $company_id ?? 0; ?></div>
            <div id="user-id"><?php echo $user_id ?? 0; ?></div>
            <div id="data-name"><?php echo $_SESSION['DB_NAME'] ?? 'infisuite'; ?></div>
            <div id="config-name">
                <?php echo isset($_SESSION['config']['name']) ? ucwords($_SESSION['config']['name']) : ''; ?>
            </div>
        </div>

        <link rel="stylesheet"
            href="assets/css/custom_insert_create_contract.css?v=<?= @filemtime(__DIR__ . '/assets/css/custom_insert_create_contract.css') ?: time() ?>">
        <script
            src="../assets/js/custom_insert_amc_contract.js?v=<?= @filemtime(__DIR__ . '/assets/js/custom_insert_amc_contract.js') ?: time() ?>"></script>
    </div><!-- .card-block -->
</div><!-- .card -->
