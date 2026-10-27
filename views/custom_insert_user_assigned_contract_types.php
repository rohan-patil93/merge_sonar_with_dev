<?php
$_SESSION['apply_js']['multiselest'] = 1;

$notification = array('message' => '', 'type' => '');
$rid = '';
if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $decodedRid = encodeId($_GET['Rid']);
    $decodedRid = urldecode($decodedRid);
    $decodedParts = explode(':', $decodedRid);
    $rid = isset($decodedParts['1']) ? $decodedParts['1'] : '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $postRid = isset($_POST['rid']) ? trim((string) $_POST['rid']) : '';
    $postUserId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
    $postTypeIds = isset($_POST['type_ids']) ? $_POST['type_ids'] : array();
    if (!is_array($postTypeIds)) {
        $postTypeIds = array();
    }

    $payload = array(
        'rid' => $postRid,
        'user_id' => $postUserId,
        'type_ids' => $postTypeIds,
    );
    // print_r($payload);
    $saveUrl = API . "?mode=saveuserassignedcontracttypes&method=post";
    $saveType = ($postRid != '' && $postRid != '0') ? 'update' : 'insert';
    $result = file_post_contents($saveUrl, $payload, $saveType);
    // print_r($result); // Debugging line to check the response from the API
    $saveRes = json_decode($result, 1);

     // Debugging line to check the response from the API
    if (isset($saveRes['status']) && $saveRes['status'] == 'sucess') {
        $error = $saveRes['message'] ?? 'Saved successfully.';
        header('Location: index.php?option=COM_LIST_USER_ASSIGNED_CONTRACT_TYPES');
        exit;
    }

    $notification['message'] = $saveRes['message'] ?? 'Failed to save.';
    $notification['type'] = 'danger';
    $rid = $postRid;
    $postedUserId = $postUserId;
    $postedTypeIds = $postTypeIds;
}

$formUrl = API . "?mode=getuserassignedcontracttypesformdata&method=get";
if ($rid != '' && $rid != '0') {
    $formUrl .= "&rid=" . urlencode($rid);
}
$formRes = json_decode(callAPI($formUrl), true);
$users = array();
$contractTypes = array();
$editData = array('id' => '', 'user_id' => '', 'type_ids' => array());
if (is_array($formRes)) {
    if (isset($formRes['users']) && is_array($formRes['users'])) {
        $users = $formRes['users'];
    }
    if (isset($formRes['contract_types']) && is_array($formRes['contract_types'])) {
        $contractTypes = $formRes['contract_types'];
    }
    if (isset($formRes['edit']) && is_array($formRes['edit']) && !empty($formRes['edit'])) {
        $editData = $formRes['edit'];
    }
}

if (isset($postedUserId)) {
    $editData['user_id'] = $postedUserId;
}
if (isset($postedTypeIds)) {
    $editData['type_ids'] = $postedTypeIds;
}

$selectedUserId = (string) ($editData['user_id'] ?? '');
$selectedTypeIds = array();
if (isset($editData['type_ids']) && is_array($editData['type_ids'])) {
    if (count((array) $editData['type_ids']) > 0) {
        foreach ($editData['type_ids'] as $tid) {
            $selectedTypeIds[] = (string) $tid;
        }
    }
}
$isEdit = ($rid != '' && $rid != '0');
$pageTitle = $isEdit ? 'Edit User Assigned Contract Types' : 'Assign Contract Types to User';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<style>
.uact-form-card .form-label { font-weight: 600; }
.uact-form-card .select2-container { width: 100% !important; }
</style>

<div class="page-body">
    <div class="card uact-form-card">
        <div class="card-header table-card-header">
            <h5><?php echo htmlspecialchars($pageTitle); ?></h5>
            <div class="card-header-right table-card-header">
                <a href="index.php?option=COM_LIST_USER_ASSIGNED_CONTRACT_TYPES" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-block">
            <?php if (!empty($notification['message'])) { ?>
                <div class="alert alert-<?php echo ($notification['type'] == 'danger') ? 'danger' : 'success'; ?> mb-3" role="alert">
                    <?php echo htmlspecialchars($notification['message']); ?>
                </div>
            <?php } ?>
            <form method="post" action="" id="uactAssignForm" autocomplete="off">
                <input type="hidden" name="rid" value="<?php echo htmlspecialchars((string) $rid); ?>">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="uact_user_id">User <span class="text-danger">*</span></label>
                        <select name="user_id" id="uact_user_id" class="form-control js-example-basic-single" required>
                            <option value="">Select User</option>
                            <?php if (count((array) $users) > 0) {
                                foreach ($users as $userRow) {
                                    $uArr = is_array($userRow) ? $userRow : json_decode(json_encode($userRow), true);
                                    $uid = (string) ($uArr['id'] ?? '');
                                    $ulabel = (string) ($uArr['display_name'] ?? '');
                                    $sel = ($selectedUserId != '' && $selectedUserId == $uid) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo htmlspecialchars($uid); ?>" <?php echo $sel; ?>>
                                        <?php echo htmlspecialchars($ulabel); ?>
                                    </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="uact_type_ids">Contract Types <span class="text-danger">*</span></label>
                        <select name="type_ids[]" id="uact_type_ids" class="form-control js-example-basic-multiple" multiple="multiple">
                            <?php if (count((array) $contractTypes) > 0) {
                                foreach ($contractTypes as $typeRow) {
                                    $tArr = is_array($typeRow) ? $typeRow : json_decode(json_encode($typeRow), true);
                                    $tid = (string) ($tArr['id'] ?? '');
                                    $tlabel = (string) ($tArr['type_name'] ?? '');
                                    $tsel = in_array($tid, (array) $selectedTypeIds) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo htmlspecialchars($tid); ?>" <?php echo $tsel; ?>>
                                        <?php echo htmlspecialchars($tlabel); ?>
                                    </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <button type="submit" name="submit" value="<?php echo $isEdit ? 'update' : 'insert'; ?>" class="btn btn-primary">
                            <?php echo $isEdit ? 'Update' : 'Save'; ?>
                        </button>
                        <a href="index.php?option=COM_LIST_USER_ASSIGNED_CONTRACT_TYPES" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    function showToast(text, isError) {
        if (typeof Toastify === 'undefined') {
            alert(text);
            return;
        }
        Toastify({
            text: text,
            duration: 3500,
            gravity: 'top',
            position: 'right',
            backgroundColor: isError ? '#dc3545' : '#28a745'
        }).showToast();
    }

    var serverMsg = <?php echo json_encode((string) ($notification['message'] ?? '')); ?>;
    var serverType = <?php echo json_encode((string) ($notification['type'] ?? '')); ?>;
    if (serverMsg) {
        showToast(serverMsg, serverType === 'danger');
    }

    var form = document.getElementById('uactAssignForm');
    if (!form) {
        return;
    }
    form.addEventListener('submit', function (e) {
        var userEl = document.getElementById('uact_user_id');
        var typeEl = document.getElementById('uact_type_ids');
        var userVal = userEl ? userEl.value : '';
        var typeVals = [];
        if (typeEl && window.jQuery && jQuery(typeEl).hasClass('select2-hidden-accessible')) {
            typeVals = jQuery(typeEl).val() || [];
        } else if (typeEl) {
            for (var i = 0; i < typeEl.options.length; i++) {
                if (typeEl.options[i].selected) {
                    typeVals.push(typeEl.options[i].value);
                }
            }
        }
        if (!userVal) {
            e.preventDefault();
            showToast('Please select a user.', true);
            return false;
        }
        if (!typeVals || typeVals.length === 0) {
            e.preventDefault();
            showToast('Please select at least one contract type.', true);
            return false;
        }
        return true;
    });
})();
</script>
