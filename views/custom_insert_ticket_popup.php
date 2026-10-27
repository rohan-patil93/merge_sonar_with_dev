<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$user_id = $_SESSION['userid'];
$company_id = $_SESSION['company_id'];
$ticketId = $_POST['ticket_id'];
$email = $_SESSION['email'];
$domain_name = $_SESSION['domain_name'];
$ticketData = [];
if ($ticketId) {
    $url = SITEURL."/ticketing/api.php?method=post&mode=getemployeetickets&data_name=infistaqin_infisuite_2_ticketing";
    $postData = ['data_name' => 'infistaqin_infisuite_2_ticketing', 'user_id' => $user_id, 'company_id' => $company_id, 'email' => $email, 'module_id' => 1, 'rid' => $ticketId, 'popup' => "Y", ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $response = curl_exec($ch);
    $ticketData = json_decode($response, true)['project_tasks'] ?? [];
}
$user = $_SESSION['user'];
$company = $_SESSION['company'];
?>
<div class="modal-header">
    <h5 class="modal-title"><?= $ticketId ? "Edit Ticket" : "Add Ticket"; ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="insertTicketForm" enctype="multipart/form-data">
        <input type="hidden" name="rid" value="<?= htmlspecialchars($ticketId); ?>">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">
        <input type="hidden" name="company_id" value="<?= htmlspecialchars($company_id); ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
        <input type="hidden" name="domain_name" value="<?= htmlspecialchars($domain_name); ?>">
        <input type="hidden" name="user_data" value='<?= htmlspecialchars(json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG)); ?>'>
        <input type="hidden" name="company_data" value='<?= htmlspecialchars(json_encode($company, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG)); ?>'>
        <div class="row">
            <div class="col-lg-6 mb-2">
                <span class="form-label">Ticket No.</span>
                <input aria-label="Ticket" type="text" class="form-control" name="ticket_id"
                    value="<?= htmlspecialchars($ticketData['ticket_id'] ??""); ?>"
                    readonly>
            </div>
            <div class="col-lg-6 mb-2">
                <span class="form-label">Subject</span>
                <input aria-label="Task Name" type="text" class="form-control" name="task_name"
                    value="<?= htmlspecialchars($ticketData['task_name'] ?? ''); ?>" required>
            </div>
            <div class="col-lg-6 mb-2 w-full">
                <span class="form-label d-block">Priority</span>
                <select aria-label="Priority" class="form-select js-example-basic-single" name="priority">
                    <option value="1" <?= ($ticketData['priority'] ?? '') == '1' ? "selected" : ""; ?>>Low</option>
                    <option value="2" <?= ($ticketData['priority'] ?? '') == '2' ? "selected" : ""; ?>>Medium</option>
                    <option value="3" <?= ($ticketData['priority'] ?? '') == '3' ? "selected" : ""; ?>>High</option>
                </select>
            </div>
            <div class="col-lg-6 mb-2">
                <span class="form-label">Attachment</span>
                <input aria-label="Attachment" type="file" class="form-control" name="attachment">
            </div>
            <div class="col-lg-12 mb-2">
                <span class="form-label">Description</span>
                <textarea aria-label="Description" class="form-control" name="description" rows="3"><?= htmlspecialchars($ticketData['description'] ?? ''); ?></textarea>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" onclick="submitInsertTicket()">Save</button>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script>
    function submitInsertTicket() {
        var form = document.getElementById('insertTicketForm');
        var formData = new FormData();
        formData.append('popuprid', form.querySelector('[name="rid"]').value);
        formData.append('popupticket_id', form.querySelector('[name="ticket_id"]').value);
        formData.append('pouptask_name', form.querySelector('[name="task_name"]').value);
        formData.append('popuppriority', form.querySelector('[name="priority"]').value);
        formData.append('popupdescription', form.querySelector('[name="description"]').value);
        formData.append('popupcreated_by', form.querySelector('[name="user_id"]').value);
        formData.append('user_id', form.querySelector('[name="user_id"]').value);
        formData.append('company_id', form.querySelector('[name="company_id"]').value);
        formData.append('email', form.querySelector('[name="email"]').value);
        formData.append('domain_name', form.querySelector('[name="domain_name"]').value);
        formData.append('user_data', form.querySelector('[name="user_data"]').value);
        formData.append('company_data', form.querySelector('[name="company_data"]').value);
        var attachment = form.querySelector('[name="attachment"]').files[0];
        if (attachment) {
            formData.append('popattachment', attachment);
        }
        console.log("🚀 Posting data to inserttickets API:");
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        $.ajax({
            url: "<?php echo SITEURL; ?>/ticketing/api.php/?mode=inserttickets&method=post&data_name=infistaqin_infisuite_2_ticketing",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("✅ Response received:", response);
                try {
                    var json = JSON.parse(response);
                    if (json.status && (json.status.toLowerCase() === 'success' || json.status.toLowerCase() === 'sucess')) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Ticket saved successfully!'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: json.message
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
            },
            error: function (err) {
                console.error("❌ AJAX Error:", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to reach API'
                });
            }
        });
    }
    $(".js-example-basic-single").select2();
</script>
