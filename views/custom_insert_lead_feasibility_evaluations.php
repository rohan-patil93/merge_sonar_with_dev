<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$feasibility_id = '';
if (isset($_GET['previous_id']) && $_GET['previous_id'] != '') {
    $feasibility_id = $_GET['previous_id'];
}
if (isset($_GET['last_id']) && $_GET['last_id'] != '') {
    $feasibility_id = $_GET['last_id'];
}

$url = API . "?mode=getFeasiblityData&method=get&feasibility_id=" . $feasibility_id;
$response = callAPI($url);
$res = json_decode($response, true);
$data = $res['feasibility_evaluation_res'] ?? array();
$feasibility_requirement_res = $res['feasibility_requirement_res'] ?? array();

$users_res = $res['users_res'];
?>

<div class="slide" data-slide="2">
    <form id="evaluationForm" <?php echo isset($feasibility_requirement_res['current_status']) && $feasibility_requirement_res['current_status'] == ST_APPROVED ? 'style="pointer-events:none; opacity:0.7"' : '';  ?>>
        <div class="row mt-4">
            <div class="col-lg-12 m-t-10 m-b-10 ">
                <h5 style="font-weight: bold;">Evaluation</h5>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <span class="form-label">Evaluation Date <span class='text-danger'>*</span></span>
                    <input aria-label="Evaluation Date" type="text" class="form-control datepicker" name="evaluation_date" value="<?php echo isset($data['evaluation_date']) ? htmlspecialchars(fromDate($data['evaluation_date'])) : ''; ?>" required>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <span class="form-label">Evaluation By <span class='text-danger'>*</span></span>
                    <select aria-label="Evaluated By" class="form-select js-example-basic-single form-control" name="evaluated_by" required>
                        <option value="">Select Evaluator</option>
                        <?php foreach ($users_res as $id => $name) { ?>
                            <option value="<?php echo $id; ?>"
                                <?php
                                if (isset($data['evaluated_by'])) {
                                    echo ($data['evaluated_by'] == $id) ? 'selected' : '';
                                } else {
                                    echo ($_SESSION['userid'] == $id) ? 'selected' : '';
                                }
                                ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <span class="form-label">Start Date <span class='text-danger'>*</span></span>
                    <input aria-label="Start Date" type="text" class="form-control datepicker" name="start_date" value="<?php echo isset($data['start_date']) ? htmlspecialchars(fromDate($data['start_date'])) : ''; ?>" required>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <span class="form-label">End Date <span class='text-danger'>*</span></span>
                    <input aria-label="End Date" type="text" class="form-control datepicker" name="end_date" value="<?php echo isset($data['end_date']) ? htmlspecialchars(fromDate($data['end_date'])) : ''; ?>" required onchange="validateEndDate()">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <span class="form-label">Estimated Budget <span class='text-danger'>*</span></span>
                    <input aria-label="Estimated Budget" type="number" class="form-control text-right" name="estimated_budget" value="<?php echo isset($data['estimated_budget']) ? htmlspecialchars($data['estimated_budget']) : ''; ?>" required>
                </div>
            </div>
            <div class="col-lg-6"></div>
            <div style="pointer-events: auto; opacity: 1;" class="col-lg-6 mt-3">
                <button type="button" class="btn btn-secondary prev-btn" onclick="previous('custom_insert_feasibility_requirements', <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">Previous</button>
            </div>
            <div style="pointer-events: auto; opacity: 1;" class="col-lg-6 text-end mt-3">
                <?php if ($feasibility_requirement_res['current_status'] == ST_APPROVED) { ?>
                    <button class="btn btn-primary next-btn" type="button" id="saveNextBtn" onclick="withOutSaving('custom_insert_lead_feasibility_boms', <?php echo $data['id']; ?>, <?php echo $_GET['lead_id']; ?>, <?php echo $feasibility_id; ?>)">Save & Next</button>
                <?php } else { ?>
                    <button type="button" class="btn btn-primary next-btn" onclick="saveAndNext('custom_insert_lead_feasibility_boms', <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">Save & Next</button>
                <?php } ?>
            </div>
        </div>
        <input type="hidden" name="rid" id='feasibilty_rid' value="<?php echo $data['id'] ?? ''; ?>">
        <input type="hidden" name="feasibility_id" id="feasibility_id" value="<?php echo $feasibility_id ?? ''; ?>">
    </form>
</div>

<script>
    function saveAndNext(page, feasibility_id, lead_id) {
        // Validate required fields before submitting
        let isValid = true;
        $('#evaluationForm').find('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please fill in all required fields.', '');
            return;
        }

        var form = document.getElementById('evaluationForm');
        var formData = new FormData(form);
        var rid = document.getElementById('feasibilty_rid').value;
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);
        formData.append('page', 'insertFeasibility');
        formData.append('table_name', 'lead_feasibility_evaluations');
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from processing FormData
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'sucess') {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message']['3001']; ?>', '');
                    var last_id = data.LASTID && data.LASTID != 0 ? data.LASTID : rid; // Use last_id from response or fallback to rid
                    console.log("Last ID:", last_id);
                    $.ajax({
                        url: `views/${page}.php`, // Replace with your backend URL
                        type: 'GET',
                        data: {
                            'last_id': last_id,
                            'feasibility_id': feasibility_id,
                            'lead_id': lead_id
                        },
                        success: function(response) {
                            $('#feasibilitySlides').html(response);
                            $(".datepicker").datepicker({
                                showOn: "both",
                                buttonText: "<i class='ti-calendar'></i>",
                                dateFormat: date_format,
                                changeMonth: true,
                                changeYear: true
                             }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
                            $('[data-bs-toggle="tooltip"]').tooltip();
                            reinitSelect2(); // Reinitialize select2 after loading new content

                        },
                        error: function(xhr, status, error) {
                            handleAjaxError(xhr, status, err, "Error loading next page.");
                        }
                    });
                } else {
                    handleAjaxError(null, null, null, "Save failed: " + (data.message || "Unknown error."));
                }
            },
            error: function(xhr, status, error) {
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

    function validateEndDate() {
        const start = document.querySelector('[name="start_date"]');
        const end = document.querySelector('[name="end_date"]');

        if (start && end && start.value && end.value) {
            const startDate = new Date(start.value);
            const endDate = new Date(end.value);

            if (endDate < startDate) {
                alert("End Date cannot be earlier than Start Date.");
                end.value = ''; // clear the incorrect value
                end.focus();
            }
        }
    }

    function withOutSaving(page, rid, lead_id, feasibility_id) {
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);

        $.ajax({
            url: `views/${page}.php`,
            type: 'GET',
            data: {
                'last_id': rid,
                'lead_id': lead_id,
                'feasibility_id': feasibility_id,
            },
            success: function(res) {
                $('#feasibilitySlides').html(res);
                $(".datepicker").datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true
                 }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
                reinitSelect2(); // Reinitialize select2 after loading new content
            },
            error: function(xhr, status, error) {
                alert('Error loading next page: ' + error);
            }
        });
    }
</script>
