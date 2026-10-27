<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_POST['post_array'])) {
    die("Invalid request (missing post_array)");
}
$postdetails = json_decode($_POST['post_array'], true);
if (!$postdetails || !isset($postdetails['rid'])) {
    die("Invalid request (bad post_array)");
}
$rid = $postdetails['rid'];
$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['userid'];
$email = $_SESSION['email'];
$nextStatus = $postdetails['next_status'];
$holdingType = $postdetails['holding_type'];

$postData = ['data_name' => 'infistaqin_infisuite_2_ticketing', 'email' => $email, 'user_id' => $user_id, 'company_id' => $company_id, 'module_id' => 1, 'rid' => $rid, 'popup' => "Y"];
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://dev.infistaq.in/ticketing/api.php?method=post&mode=getemployeetickets&data_name=infistaqin_infisuite_2_ticketing',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $postData,
));

$response = curl_exec($curl);

$data = json_decode($response, true);
if (!$data || !isset($data['project_tasks'])) {
    die("No ticket data found from API.<br>Raw response:<br><pre>" . htmlspecialchars($response) . "</pre>");
}
$task = $data['project_tasks'];
$priority_categories = $data['priority_categories'] ?? [];
$severity_categories = $data['severity_categories'] ?? [];
$resolution_types = $data['resolution_type_categories'] ?? [];
$fixing_types = $data['fixing_type_categories'] ?? [];
$statusList = $data['table_statusList'];
$statusNameMap = [];
foreach ($statusList as $s) {
    $statusNameMap[$s['status']] = $s['name'];
}
$currentStatusCode = $task['current_status'] ?? '';
$currentStatusName = $statusNameMap[$currentStatusCode] ?? $currentStatusCode;
$reqDateTime = !empty($task['request_completion_date']) ? explode(" ", $task['request_completion_date']) : ["", ""];
?>
<div class="modal-header">
    <h5 class="modal-title">
        Update Status - <?= htmlspecialchars($currentStatusName); ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="popupform">
        <input type="hidden" name="rid" value="<?= htmlspecialchars($rid); ?>">
        <input type="hidden" name="company_id" value="<?= htmlspecialchars($company_id); ?>">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
        <input type="hidden" id="sla_actual_response_date" name="sla_actual_response_date" value="<?= htmlspecialchars($task['sla_actual_response_date'] ?? ''); ?>">
        <input type="hidden" id="sla_resolution_date" name="sla_resolution_date" value="<?= htmlspecialchars($task['sla_resolution_date'] ?? ''); ?>">
        <input type="hidden" id="sla_responce_time" name="sla_responce_time" value="<?= htmlspecialchars($task['sla_responce_time'] ?? ''); ?>">
        <input type="hidden" id="sla_resolution_time" name="sla_resolution_time" value="<?= htmlspecialchars($task['sla_resolution_time'] ?? ''); ?>">
        <div class="row mb-3">
            <div class="col-md-4">
                <span class="form-label">Ticket No.</span>
                <p><?= htmlspecialchars($task['ticket_id']); ?></p>
            </div>
            <div class="col-md-4">
                <span class="form-label">Subject</span>
                <p><?= htmlspecialchars($task['task_name']); ?></p>
            </div>
            <div class="col-md-4">
                <span class="form-label">Priority</span>
                <span class="badge bg-primary">
                    <?= htmlspecialchars($priority_categories[$task['priority']]['name'] ?? ''); ?>
                </span>
            </div>
        </div>
        <div class="row g-3">
            <?php if ($nextStatus == "ST_APPROVAL_PENDING" || $nextStatus == "ST_INPROGRESS"): ?>
                <div class="col-md-6"
                    style="pointer-events: <?= $nextStatus == "ST_APPROVAL_PENDING" ? "auto" : "none" ?>; opacity: <?= $nextStatus == "ST_APPROVAL_PENDING" ? "1" : "0.7" ?>;">
                    <span class="form-label">Severity <span class="text-danger">*</span></span>
                    <select aria-label="Severity" class="form-select" name="severity_id" id="severity_idselect"
                        onchange="getSeverityType(this.value,'<?= $task['project_id']; ?>')">
                        <?php foreach ($severity_categories as $sev): ?>
                            <option value="<?= $sev['id']; ?>" <?= $task['severity_id'] == $sev['id'] ? "selected" : ""; ?>>
                                <?= htmlspecialchars($sev['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3"
                    style="pointer-events: <?= $nextStatus == "ST_APPROVAL_PENDING" ? "auto" : "none" ?>; opacity: <?= $nextStatus == "ST_APPROVAL_PENDING" ? "1" : "0.7" ?>;">
                    <span class="form-label">Requested Date</span>
                    <input aria-label="Request Completion Date" type="text" class="form-control datepicker" id="request_completion_date"
                        name="request_completion_date" value="<?= htmlspecialchars($reqDateTime[0]); ?>">
                </div>
                <div class="col-md-3"
                    style="pointer-events: <?= $nextStatus == "ST_APPROVAL_PENDING" ? "auto" : "none" ?>; opacity: <?= $nextStatus == "ST_APPROVAL_PENDING" ? "1" : "0.7" ?>;">
                    <span class="form-label">Requested Time</span>
                    <input aria-label="Request Completion Time" type="time" class="form-control" id="request_completion_time" name="request_completion_time"
                        value="<?= htmlspecialchars($reqDateTime[1]); ?>">
                </div>
            <?php endif; ?>
            <?php if ($nextStatus == "ST_INPROGRESS" || $nextStatus == "ST_COMPLETE"): ?>
                <div class="col-md-6">
                    <span class="form-label">Planned Completion Date</span>
                    <input aria-label="Planed Completion Date" type="text" class="form-control datepicker" name="planed_completion_date" value="">
                </div>
                <div class="col-md-6">
                    <span class="form-label">Planned Completion Time</span>
                    <input aria-label="Planed Completion Time" type="time" class="form-control" name="planed_completion_time" value="">
                </div>
            <?php endif; ?>
            <?php if ($nextStatus == "ST_COMPLETE"): ?>
                <div class="col-md-6">
                    <span class="form-label">Resolution Type</span>
                    <select aria-label="Resolution Type" class="form-select" name="resolution_type">
                        <option value="">Select</option>
                        <?php foreach ($resolution_types as $rt): ?>
                            <option value="<?= $rt['id']; ?>"><?= htmlspecialchars($rt['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <span class="form-label">Fixing Type</span>
                    <select aria-label="Fixing Type" class="form-select" name="fixing_type">
                        <option value="">Select</option>
                        <?php foreach ($fixing_types as $ft): ?>
                            <option value="<?= $ft['id']; ?>"><?= htmlspecialchars($ft['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <span class="form-label">Step Taken</span>
                    <textarea aria-label="Step Taken" class="form-control" name="step_taken" rows="2"></textarea>
                </div>
                <div class="col-12">
                    <span class="form-label">Resolution Description</span>
                    <textarea aria-label="Resolution Description" class="form-control" name="resolution_description" rows="2"></textarea>
                </div>
            <?php endif; ?>
            <div class="col-12">
                <span class="form-label">Comment</span>
                <textarea aria-label="Comment" class="form-control" name="comment" rows="3"></textarea>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary"
        onclick="dynamicStatusUpdatePopUp('<?= urlencode($_POST['post_array']); ?>','','','','<?= $nextStatus; ?>')">
        Submit
    </button>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script>
    function getSeverityType(severity_id, project_id) {
        if (!severity_id) {
            $("#request_completion_date").val('');
            $("#request_completion_time").val('');
            $("#sla_actual_response_date").val('');
            $("#sla_resolution_date").val('');
            $("#sla_responce_time").val('');
            $("#sla_resolution_time").val('');
            return false;
        }
        var cleint_id = $("#popup_cleint_id").val();
        var formData = new FormData();
        formData.append('page', 'getseverity');
        formData.append('severity_id', severity_id);
        formData.append('project_id', project_id);
        formData.append('cleint_id', cleint_id);
        console.log("🚀 Posting data to getseverity API:");
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        $.ajax({
            url: "<?php echo SITEURL; ?>/ticketing/api.php?mode=getseverity&method=post&data_name=infistaqin_infisuite_2_ticketing",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("✅ Response received from getseverity:", response);
                try {
                    var result = JSON.parse(response);
                    if (result.sla_response && result.sla_response['error'] === "") {
                        $("#request_completion_date").val(result.sla_response['date']);
                        $("#request_completion_time").val(result.sla_response['time']);
                        $("#sla_actual_response_date").val(result.sla_response['sla_actual_response_date']);
                        $("#sla_resolution_date").val(result.sla_response['sla_resolution_date']);
                        $("#sla_responce_time").val(result.sla_response['sla_responce_time']);
                        $("#sla_resolution_time").val(result.sla_response['sla_resolution_time']);
                        console.log("📌 SLA fields updated successfully.");
                    } else {
                        Swal.fire({ icon: 'error', title: 'Oops', text: result.sla_response['error'] || 'Unknown error' });
                        $("#request_completion_date, #request_completion_time, #sla_actual_response_date, #sla_resolution_date, #sla_responce_time, #sla_resolution_time").val('');
                    }
                } catch (e) {
                    console.error("⚠️ Failed to parse response:", e, response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid API response'
                    });
                    $("#request_completion_date, #request_completion_time, #sla_actual_response_date, #sla_resolution_date, #sla_responce_time, #sla_resolution_time").val('');
                }
            },
            error: function (xhr, status, error) {
                console.error("❌ Ajax error:", status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: 'Could not reach getseverity API'
                });
            }
        });
    }
    $(document).ready(function () {
        var severity_idselect = $("#severity_idselect").val();
        var projectId = "<?= $task['project_id']; ?>";
        if (severity_idselect) {
            getSeverityType(severity_idselect, projectId);
        }
    });
    function dynamicStatusUpdatePopUp(post_array, select_user_id = "", transaction_log_id = "", notilog = "", status = "") {
        var form = document.getElementById('popupform');
        var formData = new FormData(form);
        formData.append('post_array', post_array);
        formData.append('select_user_id', select_user_id);
        formData.append('transaction_log_id', transaction_log_id);
        formData.append('notilog', notilog);
        formData.append('status', status);
        formData.append('table', 'project_tasks');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        $.ajax({
            url: "<?php echo SITEURL; ?>/ticketing/api.php?mode=dynamicupdatestatus&method=post&data_name=infistaqin_infisuite_2_ticketing",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("✅ Response received:", response);
                try {
                    var json = JSON.parse(response);
                    let isSuccess = false;
                    if (json.status && json.status.toLowerCase() === "success") {
                        isSuccess = true;
                    } else if (
                        (json.trantimelog && json.trantimelog.status && json.trantimelog.status.toLowerCase() === "sucess") ||
                        (json.tableupdate && json.tableupdate.status && json.tableupdate.status.toLowerCase() === "sucess") ||
                        (json.ticket_logs && json.ticket_logs.status && json.ticket_logs.status.toLowerCase() === "sucess")
                    ) {
                        isSuccess = true;
                    }
                    if (isSuccess) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated',
                            text: 'Status updated successfully!'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'API returned error!'
                        });
                    }
                } catch (e) {
                    console.error("⚠️ Failed to parse response:", e, response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid API response'
                    });
                }
            }
        });
    }
</script>
