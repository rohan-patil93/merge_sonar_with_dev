<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$error = '';

$rid = '';
if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid[1] ?? '';
}

$editMode = ($rid !== '');

if (isset($_POST['submit']) && in_array($_POST['submit'], ['insert', 'update'], true)) {
    $gate_name = trim($_POST['name'] ?? '');
    $location_id = (int)($_POST['location_id'] ?? 0);
    $is_active = (int)($_POST['is_active'] ?? 0);

    if (empty($gate_name)) {
        $error = 'Please enter Gate Name';
    } else {
        $post_data = [
            'name' => $gate_name,
            'location_id' => $location_id,
            'is_active' => $is_active,
            'company_id' => $_SESSION['company_id']
        ];

        if ($editMode) {
            $post_data['updated_by'] = $_SESSION['userid'] ?? 1;
            $post_data['updated_date'] = date('Y-m-d H:i:s');
            $url = API . "?mode=updaterowtabledata&table=building_gates&fid=id&rid=" . $rid;
            $type = 'update';
        } else {
            $post_data['created_by'] = $_SESSION['userid'] ?? 1;
            $post_data['created_date'] = date('Y-m-d H:i:s');
            $url = API . "?mode=adddata&table=building_gates&method=post";
            $type = 'insert';
        }

        $resp = file_post_contents($url, $post_data, $type);
        $result = json_decode($resp, true);

        if (!empty($result) && ($result['status'] === 'success' || $result['status'] === 'sucess')) {
            header("location:index.php?option=custom_list_building_gates");
            exit;
        } else {
            $error = 'Failed to save the gate configuration.';
        }
    }
}

$gate = null;
if ($editMode) {
    $data1['where'] = array('id' => $rid);
    $data = urlencode(json_encode($data1));
    $url = API . "?mode=gettabledetailswithconditions&table=building_gates&data=" . $data;
    $res = callAPI($url);
    $gate_list = json_decode($res, true) ?: [];
    $gate = $gate_list[0] ?? null;

    if (!$gate) {
        $error = 'Gate not found or deleted.';
    }
}

$loc_url = API . "?mode=gettabledetailswithconditions&table=locations&data=" . urlencode(json_encode(['where' => ['company_id' => $_SESSION['company_id']]]));
$loc_res = callAPI($loc_url);
$locations = json_decode($loc_res, true) ?: [];
?>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" integrity="sha384-aqK8GioSeku4LA3EdWsiN2vmOKlqIJUGyu3llzFeAhBz6lOqiBKzSaKmEfvTRm2v" crossorigin="anonymous">
<link rel="stylesheet" href="assets/css/custom_insert_building_gates.css">

<div class="form-container">
    <div class="form-header">
        <h4><?= $editMode ? 'Edit Building Gate' : 'Add New Building Gate' ?></h4>
        <p>Configure gate name, physical location mapping, and status.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert-custom alert-custom-danger">
            <i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form id="gateForm" method="post">
        <div class="form-group">
            <label for="name">Gate Name <span class="text-danger">*</span></label>
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control-custom"
                   placeholder="e.g. Main Gate, South Cargo Gate"
                   value="<?= htmlspecialchars($gate['name'] ?? '') ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="location_id">Mapped Location</label>
            <select id="location_id" name="location_id" class="form-control-custom">
                <option value="0">All Locations</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= (int)$loc['id'] ?>" <?= (isset($gate['location_id']) && (int)$gate['location_id'] === (int)$loc['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($loc['short_name']) ?> (<?= htmlspecialchars($loc['location_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="is_active">Status</label>
            <select id="is_active" name="is_active" class="form-control-custom">
                <option value="0" <?= (isset($gate['is_active']) && (int)$gate['is_active'] === 0) ? 'selected' : '' ?>>Active</option>
                <option value="1" <?= (isset($gate['is_active']) && (int)$gate['is_active'] === 1) ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="btn-submit-container">
            <a href="index.php?option=custom_list_building_gates" class="btn-custom btn-custom-secondary">
                Cancel
            </a>
            <button type="submit" name="submit" value="<?= $editMode ? 'update' : 'insert' ?>" class="btn-custom btn-custom-primary">
                <i class="fa fa-save"></i> <?= $editMode ? 'Save Changes' : 'Add Gate' ?>
            </button>
        </div>
    </form>
</div>
