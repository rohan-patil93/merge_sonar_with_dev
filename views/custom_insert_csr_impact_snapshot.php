<?php
$_SESSION['apply_js']['multiselest'] = 1;

$table_name = 'csr_impact_snapshot';

function csrImpactSnapshotText($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrImpactSnapshotValue($row, $key) {
    return is_array($row) ? ($row[$key] ?? '') : ($row->$key ?? '');
}

function csrImpactSnapshotFinancialYearStart($financialYear) {
    $matches = $matches ?? '';
    if (preg_match('/^([0-9]{4})/', (string)$financialYear, $matches)) {
        return (int)$matches[1];
    }
    return 0;
}

$currentYear = (int)date('Y');
$currentMonth = (int)date('n');
$currentFinancialYearStart = $currentMonth >= 4 ? $currentYear : $currentYear - 1;
$currentFinancialYear = $currentFinancialYearStart . '-' . substr((string)($currentFinancialYearStart + 1), -2);

if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $ridParts = explode(':', urldecode(encodeId($_GET['Rid'])));
    $rid = $ridParts[1] ?? '';
} else {
    $rid = '';
}

$isViewMode = isset($_GET['view']) && $_GET['view'] == '1';

if (isset($_POST['submit']) && !$isViewMode) {
    $postData = $_POST;
    $submitType = $postData['submit'];
    unset($postData['submit']);

    if ($submitType == 'insert') {
        unset($postData['rid']);
        $postData['current_status'] = '0';
        $postData['is_active'] = '0';
        $postData['status_date'] = date('Y-m-d H:i:s');
        $postData['company_id'] = $_SESSION['company_id'];
        $postData['created_date'] = date('Y-m-d H:i:s');
        $postData['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        file_post_contents($url, $postData, 'insert');
        header("location:index.php?option=COM_LIST_CSR_IMPACT_SNAPSHOT");
        exit;
    }

    if ($submitType == 'update') {
        $rid = $postData['rid'];
        unset($postData['rid']);
        $postData['updated_date'] = date('Y-m-d H:i:s');
        $postData['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        file_post_contents($url, $postData, 'update');
        header("location:index.php?option=COM_LIST_CSR_IMPACT_SNAPSHOT");
        exit;
    }
}

$selectedYear = $_GET['financial_year'] ?? '';
if ($selectedYear == '' || csrImpactSnapshotFinancialYearStart($selectedYear) > $currentFinancialYearStart) {
    $selectedYear = $currentFinancialYear;
}
$url = API . "?mode=getCsrImpactSnapshotData&method=get&rid=" . $rid . "&financial_year=" . urlencode($selectedYear);
$response = callAPI($url);
$impactData = json_decode($response, true);

$financialYears = $impactData['financial_years'] ?? array();
$projectsByYear = $impactData['projects_by_year'] ?? array();
$valueData = $impactData['impact_snapshot'] ?? array();

if (count((array)$valueData) > 0) {
    $selectedYear = csrImpactSnapshotValue($valueData, 'financial_year');
} elseif ($selectedYear == '') {
    $selectedYear = $impactData['selected_financial_year'] ?? '';
}
if (csrImpactSnapshotFinancialYearStart($selectedYear) > $currentFinancialYearStart) {
    $selectedYear = $currentFinancialYear;
}

$selectedProjectId = csrImpactSnapshotValue($valueData, 'project_id');
$submitValue = $rid != '' ? 'update' : 'insert';
$submitLabel = $rid != '' ? 'Update' : 'Submit';
$readonlyAttr = $isViewMode ? 'readonly' : '';
$disabledAttr = $isViewMode ? 'disabled' : '';
?>

<link rel="stylesheet" href="assets/css/custom_insert_csr_impact_snapshot.css">

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>CSR Impact Snapshot</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_LIST_CSR_IMPACT_SNAPSHOT" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-list"></i>
                    List
                </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-block">
            <form method="POST" action="" id="csrImpactSnapshotForm" autocomplete="off">
                <input type="hidden" name="rid" value="<?php echo csrImpactSnapshotText($rid); ?>">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="financial_year">Financial Year</label>
                        <select name="financial_year" id="financial_year" class="form-control form-select js-example-basic-single select2" required <?php echo $disabledAttr; ?>>
                            <option value="">Select Financial Year</option>
                            <?php foreach ($financialYears as $fyRow) {
                                $fyCode = csrImpactSnapshotValue($fyRow, 'genc_detail_code');
                                $fyName = csrImpactSnapshotValue($fyRow, 'genc_detail_name');
                                if (csrImpactSnapshotFinancialYearStart($fyCode) > $currentFinancialYearStart) {
                                    continue;
                                }
                            ?>
                                <option value="<?php echo csrImpactSnapshotText($fyCode); ?>" <?php echo $selectedYear == $fyCode ? 'selected' : ''; ?>>
                                    <?php echo csrImpactSnapshotText($fyName ?: $fyCode); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-8 form-group">
                        <label for="project_id">Project</label>
                        <select name="project_id" id="project_id" class="form-control form-select js-example-basic-single select2" required <?php echo $disabledAttr; ?>>
                            <option value="">Select Project</option>
                            <?php foreach (($projectsByYear[$selectedYear] ?? array()) as $projectRow) {
                                $projectId = csrImpactSnapshotValue($projectRow, 'id');
                            ?>
                                <option value="<?php echo csrImpactSnapshotText($projectId); ?>" <?php echo $selectedProjectId == $projectId ? 'selected' : ''; ?>>
                                    <?php echo csrImpactSnapshotText(csrImpactSnapshotValue($projectRow, 'subject')); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="form-text text-muted js-project-empty <?php echo count((array)($projectsByYear[$selectedYear] ?? array())) > 0 ? 'd-none' : ''; ?>">No approved projects found for selected financial year.</small>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4 form-group">
                        <label for="student_supported">Student Supported</label>
                        <input type="number" min="0" id="student_supported" name="student_supported" class="form-control text-end" required <?php echo $readonlyAttr; ?> value="<?php echo csrImpactSnapshotText(csrImpactSnapshotValue($valueData, 'student_supported') ?: 0); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="villages_covered">Villages Covered</label>
                        <input type="number" min="0" id="villages_covered" name="villages_covered" class="form-control text-end" required <?php echo $readonlyAttr; ?> value="<?php echo csrImpactSnapshotText(csrImpactSnapshotValue($valueData, 'villages_covered') ?: 0); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="health_camps_conducted">Health Camps Conducted</label>
                        <input type="number" min="0" id="health_camps_conducted" name="health_camps_conducted" class="form-control text-end" required <?php echo $readonlyAttr; ?> value="<?php echo csrImpactSnapshotText(csrImpactSnapshotValue($valueData, 'health_camps_conducted') ?: 0); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="trees_planted">Trees Planted</label>
                        <input type="number" min="0" id="trees_planted" name="trees_planted" class="form-control text-end" required <?php echo $readonlyAttr; ?> value="<?php echo csrImpactSnapshotText(csrImpactSnapshotValue($valueData, 'trees_planted') ?: 0); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="women_benefited">Women Benefited</label>
                        <input type="number" min="0" id="women_benefited" name="women_benefited" class="form-control text-end" required <?php echo $readonlyAttr; ?> value="<?php echo csrImpactSnapshotText(csrImpactSnapshotValue($valueData, 'women_benefited') ?: 0); ?>">
                    </div>
                </div>

                <?php if (!$isViewMode) { ?>
                    <div class="text-end mt-3">
                        <button type="submit" name="submit" value="<?php echo csrImpactSnapshotText($submitValue); ?>" class="btn btn-primary">
                            <?php echo csrImpactSnapshotText($submitLabel); ?>
                        </button>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>

<div id="csrImpactSnapshotConfig"
    data-projects-by-year='<?php echo csrImpactSnapshotText(json_encode($projectsByYear)); ?>'
    data-selected-project="<?php echo csrImpactSnapshotText($selectedProjectId); ?>"
    hidden>
</div>

<script src="assets/js/custom_insert_csr_impact_snapshot.js?v=<?php echo filemtime('assets/js/custom_insert_csr_impact_snapshot.js'); ?>"></script>
