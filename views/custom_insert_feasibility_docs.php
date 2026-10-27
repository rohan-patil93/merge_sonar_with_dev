<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$feasibility_id = '';
if (!empty($_GET['previous_id'])) {
    $feasibility_id = $_GET['previous_id'];
}
if (!empty($_GET['feasibility_id'])) {
    $feasibility_id = $_GET['feasibility_id'];
}

$table_name = 'application_attachments';
$vdata1['order'] = "id ASC";
$vdata1["where"] = array(
    'company_id' => $_SESSION['company_id'],
    'transaction_id' => $feasibility_id,
    'application_table_name' => 'feasibility_requirements',
    'is_active' => 0
);
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=gettabledetailswithconditions&table=$table_name&data=$vdata";
$che = callAPI($vurl);
$return = json_decode($che, true);
?>

<div class="slide" data-slide="5">
    <div class="row mt-4">
        <div class="col-lg-12">
            <h4 class="mb-3">Upload Supporting Documents</h4>
            <form id="fileUploadForm" enctype="multipart/form-data" <?php echo  isset($_GET['current_status']) && $_GET['current_status'] == ST_APPROVED ? 'style="pointer-events:none; opacity:0.7"' : ''; ?>>
                <div class="mb-3">
                    <input aria-label="File[]" type="file" class="form-control" name="file[]" multiple required>
                    <input type="hidden" name="feasibility_id" id="feasibility_id" value="<?php echo htmlspecialchars($feasibility_id); ?>" >
                </div>

                <?php if (!empty($return)) { ?>
                    <div class="mb-4">
                        <span class="form-label fw-bold">Uploaded Files:</span>
                        <div class="form-group col-xl-12 col-md-12 mt-2" style="pointer-events: auto;">
                            <div class="row">
                                <?php if (count((array)$return) > 0) {
                                    foreach ($return as $row) { ?>
                                        <div class="col-lg-4 "><?php echo $row['actual_file_name']; ?></div>
                                        <div class="col-lg-2 ">
                                            <a target="_blank" href="uploads/<?php echo htmlspecialchars($row['file_name']); ?>" class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                        </div>
                                <?php  }
                                } ?>
                            </div>
                        </div>

                    </div>
                <?php } ?>

                <div style="pointer-events: auto; opacity: 1;" class="row">
                    <div class="col-lg-6">
                        <button type="button" class="btn btn-secondary" onclick="previous('custom_insert_lead_feasibility_checklists', <?= $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">Previous</button>
                    </div>
                    <div class="col-lg-6 text-end">
                        <button type="button" class="btn btn-primary" onclick="uploadFiles(<?php echo htmlspecialchars($feasibility_id); ?>)">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function uploadFiles(feasibility_id) {
    const form = document.getElementById('fileUploadForm');
    const fileInput = form.querySelector('input[type="file"]');
    const formData = new FormData(form);

    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#dynamic_popupxxl").html(loader);

    if (!fileInput.files || fileInput.files.length === 0) {
        jQuery.ajax({
            url: "views/custom_view_feasibility_report.php",
            data: { 'feasibility_id': feasibility_id },
            type: "GET",
            success: function (data) {
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error: function () {
                handleAjaxError(xhr, status, err, "Error loading next page.");

            }
        });
        return;
    }

    formData.append('page', 'uploadFeasibilityFiles');
    formData.append('table_name', 'feasibility_requirements');

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message']['3001']; ?>', '');

                    jQuery.ajax({
                        url: "views/custom_view_feasibility_report.php",
                        data:{'feasibility_id': feasibility_id},
                        type: "GET",
                        success:function(data){
                            $('#dynamic_popupxxl').html(data);
                            $('#kt_modal_dynamic_popupxxl').modal("show");
                        },
                        error:function (){}
                    });

                } else {
                    handleAjaxError(null, null, null, "Save failed: " + (data.message || "Upload failed."));

                }
            } catch (e) {
                handleAjaxError(null, null, null, "Received invalid response from server.");
                console.error(e);
            }
        },
        error: function () {
            handleAjaxError(xhr, status, err);

        }
    });
}
    function handleAjaxError(xhr, status, err, customMessage = null) {
        const appName = "<?php echo ucwords($_SESSION['config']['name']); ?>";
        let errorMessage = customMessage || "An unexpected error occurred.";

        // Optional: more specific messages based on error type
        if (!customMessage) {
            if (status === 'timeout') {
                errorMessage = "The request timed out. Please check your internet connection.";
            } else if (status === 'parsererror') {
                errorMessage = "Failed to parse server response. Please try again.";
            } else if (xhr.status >= 500) {
                errorMessage = "Server error. Please contact support or try again later.";
            } else if (status === 'abort') {
                errorMessage = "Request was aborted.";
            } else {
                errorMessage = "An error occurred while processing your request.";
            }
        }

        swal({
            title: appName,
            text: errorMessage,
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "OK"
        });

        console.error("AJAX Error:", {
            status: status,
            error: err,
            xhr: xhr
        });
    }
</script>
