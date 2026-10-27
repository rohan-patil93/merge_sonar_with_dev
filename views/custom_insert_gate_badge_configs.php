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

$gate_url = API . "?mode=gettabledetailswithconditions&table=building_gates&data=" . urlencode(json_encode(['where' => ['company_id' => $_SESSION['company_id']]]));
$gate_res = callAPI($gate_url);
$gates = json_decode($gate_res, true) ?: [];

if (isset($_POST['submit']) && in_array($_POST['submit'], ['insert', 'update'], true)) {
    $gate_id = (int)($_POST['gate_id'] ?? 0);
    $visit_code = strtoupper(trim($_POST['visit_code'] ?? ''));
    $range_start = (int)($_POST['range_start'] ?? 1);
    $range_end = (int)($_POST['range_end'] ?? 9999);
    $padding = (int)($_POST['padding'] ?? 3);
    $is_active = (int)($_POST['is_active'] ?? 0);

    if ($gate_id <= 0) {
        $error = 'Please select a Gate';
    } elseif (empty($visit_code)) {
        $error = 'Please enter a Prefix Code';
    } elseif ($range_start < 0 || $range_end <= $range_start) {
        $error = 'Range end must be greater than range start';
    } else {
        $selected_gate_loc = 0;
        foreach ($gates as $g) {
            if ((int)$g['id'] === $gate_id) {
                $selected_gate_loc = (int)$g['location_id'];
                break;
            }
        }

        $post_data = [
            'gate_id' => $gate_id,
            'visit_code' => $visit_code,
            'range_start' => $range_start,
            'range_end' => $range_end,
            'padding' => $padding,
            'is_active' => $is_active,
            'company_id' => $_SESSION['company_id'],
            'location_id' => $selected_gate_loc ?: ($_SESSION['location_id'] ?? 1)
        ];

        if ($editMode) {
            $post_data['updated_by'] = $_SESSION['userid'] ?? 1;
            $post_data['updated_date'] = date('Y-m-d H:i:s');
            $url = API . "?mode=updaterowtabledata&table=gate_badge_configs&fid=id&rid=" . $rid;
            $type = 'update';
        } else {
            $post_data['created_by'] = $_SESSION['userid'] ?? 1;
            $post_data['created_date'] = date('Y-m-d H:i:s');
            $url = API . "?mode=adddata&table=gate_badge_configs&method=post";
            $type = 'insert';
        }

        $resp = file_post_contents($url, $post_data, $type);
        $result = json_decode($resp, true);

        if (!empty($result) && ($result['status'] === 'success' || $result['status'] === 'sucess')) {
            header("location:index.php?option=custom_list_gate_badge_configs");
            exit;
        } else {
            $error = 'Failed to save the badge configuration.';
        }
    }
}

$config = null;
if ($editMode) {
    $data1['where'] = array('id' => $rid);
    $data = urlencode(json_encode($data1));
    $url = API . "?mode=gettabledetailswithconditions&table=gate_badge_configs&data=" . $data;
    $res = callAPI($url);
    $config_list = json_decode($res, true) ?: [];
    $config = $config_list[0] ?? null;

    if (!$config) {
        $error = 'Configuration not found or deleted.';
    }
}
?>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet" integrity="sha384-aqK8GioSeku4LA3EdWsiN2vmOKlqIJUGyu3llzFeAhBz6lOqiBKzSaKmEfvTRm2v" crossorigin="anonymous">
<link rel="stylesheet" href="assets/css/custom_insert_gate_badge_configs.css">

<div class="form-container">
    <div class="form-header">
        <h4><?= $editMode ? 'Edit Gate Badge Config' : 'Add Gate Badge Config' ?></h4>
        <p>Set numbering ranges, padding, and labels for badge generations at specific gates.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert-custom alert-custom-danger">
            <i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form id="configForm" method="post">
        <div class="form-group">
            <label for="gate_id">Select Gate <span class="text-danger">*</span></label>
            <select id="gate_id" name="gate_id" class="form-control-custom" required>
                <option value="">-- Choose Gate --</option>
                <?php foreach ($gates as $g): ?>
                    <option value="<?= (int)$g['id'] ?>" <?= (isset($config['gate_id']) && (int)$config['gate_id'] === (int)$g['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row-flex">
            <div class="form-group">
                <label for="visit_code">Prefix Code <span class="text-danger">*</span></label>
                <input type="text"
                       id="visit_code"
                       name="visit_code"
                       class="form-control-custom"
                       placeholder="e.g. VISIT, CARGO, CONTR"
                       value="<?= htmlspecialchars($config['visit_code'] ?? 'VISIT') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="padding">Zero Padding</label>
                <select id="padding" name="padding" class="form-control-custom">
                    <option value="1" <?= (isset($config['padding']) && (int)$config['padding'] === 1) ? 'selected' : '' ?>>1 digit (no padding)</option>
                    <option value="2" <?= (isset($config['padding']) && (int)$config['padding'] === 2) ? 'selected' : '' ?>>2 digits (e.g. 01)</option>
                    <option value="3" <?= (!isset($config['padding']) || (int)$config['padding'] === 3) ? 'selected' : '' ?>>3 digits (e.g. 001)</option>
                    <option value="4" <?= (isset($config['padding']) && (int)$config['padding'] === 4) ? 'selected' : '' ?>>4 digits (e.g. 0001)</option>
                    <option value="5" <?= (isset($config['padding']) && (int)$config['padding'] === 5) ? 'selected' : '' ?>>5 digits (e.g. 00001)</option>
                </select>
            </div>
        </div>

        <div class="row-flex">
            <div class="form-group">
                <label for="range_start">Range Start <span class="text-danger">*</span></label>
                <input type="number"
                       id="range_start"
                       name="range_start"
                       class="form-control-custom"
                       min="1"
                       value="<?= htmlspecialchars($config['range_start'] ?? '1') ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="range_end">Range End <span class="text-danger">*</span></label>
                <input type="number"
                       id="range_end"
                       name="range_end"
                       class="form-control-custom"
                       min="1"
                       value="<?= htmlspecialchars($config['range_end'] ?? '9999') ?>"
                       required>
            </div>
        </div>

        <div class="form-group">
            <label for="is_active">Status</label>
            <select id="is_active" name="is_active" class="form-control-custom">
                <option value="0" <?= (isset($config['is_active']) && (int)$config['is_active'] === 0) ? 'selected' : '' ?>>Active</option>
                <option value="1" <?= (isset($config['is_active']) && (int)$config['is_active'] === 1) ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div class="btn-submit-container">
            <a href="index.php?option=custom_list_gate_badge_configs" class="btn-custom btn-custom-secondary">
                Cancel
            </a>
            <button type="submit" name="submit" value="<?= $editMode ? 'update' : 'insert' ?>" class="btn-custom btn-custom-primary">
                <i class="fa fa-save"></i> <?= $editMode ? 'Save Changes' : 'Save Config' ?>
            </button>
        </div>
    </form>
</div>
