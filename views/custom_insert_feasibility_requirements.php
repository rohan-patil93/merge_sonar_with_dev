<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$_GET['lead_id'] = $rid ?? $_GET['lead_id'];
$url = API . "?mode=getFeasiblityData&method=get&rid={$_GET['previous_id']
}&lead_id={$_GET['lead_id']
}";
$response = callAPI($url);
$res = json_decode($response, true);
$feasibility_requirement_res = $res['feasibility_requirement_res'] ?? array();
$requirement_json = $feasibility_requirement_res['requirement_json'] ?? '{}';
$feasibility_types_res = $res['feasibility_types_res'] ?? array();
?>
<?php if ($feasibility_requirement_res['current_status'] == ST_APPROVED) {

    if ($feasibility_requirement_res['sales_order_id'] != "") {
        $deocdeId = deocdeId("id:" . $feasibility_requirement_res['sales_order_id']); ?>
        <div class="row mb-2">
            <a target="_blank" href="index.php?option=COM_INSERT_SALES_ORDERS&Rid=<?php echo $deocdeId; ?>" class="btn btn-primary col-lg-2">Sales Order</a>
            <div class="col-lg-9"></div>
        </div>
    <?php } else { ?>
        <div class="row mb-2">
            <button class="btn btn-primary col-lg-3" onclick="createSalesOrdersWithFeasibilty('<?php echo $feasibility_requirement_res['id']; ?>','<?php echo $feasibility_requirement_res['lead_id']; ?>')">Generate SO</button>
            <div class="col-lg-6"></div>
            <button class="btn btn-danger col-lg-3" onclick="reOpenFeasibility('<?php echo $feasibility_requirement_res['id']; ?>')">Reopen Feasibility</button>
        </div>
    <?php }
}
if ($feasibility_requirement_res['id'] != '' && $feasibility_requirement_res['current_status'] != ST_APPROVED) { ?>
    <div class="row mb-2">
        <div class="col-lg-9"></div>
        <button class="btn btn-success col-lg-3" onclick="showFeasibilityReport('<?php echo $feasibility_requirement_res['id']; ?>')">Close Feasibility</button>

    </div>

<?php } ?>

<div class="slide active" data-slide="1" <?php echo ($feasibility_requirement_res['current_status'] == ST_APPROVED) ? 'style="pointer-events:none; opacity:0.7"' : '';  ?>>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group w-full">
                <label class="form-label d-block" for="feasibility_type_id">Feasibility Type</label>
                <select class="form-select js-example-basic-single form-control" id="feasibility_type_id" onchange="loadFeasibilityFields()">
                    <?php foreach ($feasibility_types_res as $id => $name) { ?>
                        <option value="<?php echo $id; ?>"
                            <?= (isset($feasibility_requirement_res['feasibility_type_id']) && $feasibility_requirement_res['feasibility_type_id'] == $id) ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($name); ?></option>
                    <?php } ?>

                </select>
            </div>
        </div>

    </div>
    <form id="feasibilityForm">
        <div class="col-lg-12 m-t-10 m-b-10 ">
            <h5 style="font-weight: bold;">Feasibility Requirement</h5>
        </div>
        <div id="dynamic_fields" class="row"></div>


    </form>
    <input type="hidden" id="requirement_json" value='<?= htmlspecialchars($requirement_json, ENT_QUOTES, "UTF-8") ?>'>
    <input type="hidden" name="rid" id='feasibilty_rid' value="<?php echo $feasibility_requirement_res['id'] ?? ''; ?>">
    <input type="hidden" name="lead_id" id='lead_id' value="<?php echo $_GET['lead_id'] ?? ''; ?>">
    <div style="pointer-events: auto; opacity: 1;" class="col-lg-12 text-end mt-4">
        <?php if ($feasibility_requirement_res['current_status'] == ST_APPROVED) { ?>
            <button class="btn btn-primary next-btn" type="button" id="saveNextBtn" onclick="withOutSaving('custom_insert_lead_feasibility_evaluations', <?php echo $feasibility_requirement_res['id']; ?>, <?php echo $_GET['lead_id']; ?>)">Save & Next</button>
        <?php } else { ?>
            <button class="btn btn-primary next-btn" type="button" id="saveNextBtn_2" onclick="saveAndNextRequirement('custom_insert_lead_feasibility_evaluations')">Save & Next</button>
        <?php } ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Load fields on page load
        loadFeasibilityFields();
    });

    function loadFeasibilityFields() {
        const typeId = $('#feasibility_type_id').val();
        const savedJson = $('#requirement_json').val();
        let savedValues = {};

        try {
            savedValues = JSON.parse(savedJson);
        } catch (e) {
            console.warn("Invalid JSON in requirement_json");
        }
        if (!typeId) {
            $('#dynamic_fields').html('<div class="text-danger">Please select a feasibility type.</div>');
            return;
        }
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                'id': typeId,
                'page': 'getFeasibilityFields'
            },
            success: function(response) {
                $('#dynamic_fields').html(JSON.parse(response));
                // Pre-fill values
                for (const key in savedValues) {
                    const val = savedValues[key];
                    const field = $(`[name="${key}"]`);

                    if (field.length) {
                        const type = field.attr('type');

                        if (type === 'radio') {
                            $(`input[name="${key}"][value="${val}"]`).prop('checked', true);
                        } else {
                            field.val(val).trigger('change');
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("Error loading fields:", error);
                $('#dynamic_fields').html('<div class="text-danger">Failed to load fields.</div>');
            }
        });
    }

    function saveAndNextRequirement(page) {
        // Validate required fields
        let isValid = true;
        $('#feasibilityForm').find('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid'); // Bootstrap red border (optional)
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please fill in all required fields.', '');
            return;
        }
        var form = document.getElementById('feasibilityForm');
        var formData = new FormData();

        var requirementData = {};
        var feasibility_type_id = document.getElementById('feasibility_type_id').value;
        var rid = document.getElementById('feasibilty_rid').value;
        var lead_id = document.getElementById('lead_id').value;
        var transaction_table = document.getElementById('transaction_table').value;
        formData.append('feasibility_type_id', feasibility_type_id);
        formData.append('rid', rid);
        formData.append('lead_id', lead_id);
        formData.append('transaction_table', transaction_table);
        $('#feasibilityForm').find('input, select, textarea, radio').each(function() {
            var type = $(this).attr('type');
            var name = $(this).attr('name');

            if (!name || name === 'feasibility_type_id') return;

            if (type === 'radio') {
                if ($(this).is(':checked')) {
                    requirementData[name] = $(this).val();
                }
            } else {
                requirementData[name] = $(this).val();
            }

        });
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);
        // Append as JSON string
        formData.append('requirement_json', JSON.stringify(requirementData));
        formData.append('page', 'insertFeasibility');
        formData.append('table_name', 'feasibility_requirements');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    console.log("Response data:", data);
                    if (data.status === 'sucess') {
                        tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message']['3001']; ?>', '');
                        var last_id = data.LASTID && data.LASTID != 0 ? data.LASTID : rid; // Use last_id from response or fallback to rid
                        console.log("Last ID:", last_id);
                        $('#feasibility_rid_from_ajax').val(last_id);

                        $.ajax({
                            url: `views/${page}.php`,
                            type: 'GET',
                            data: {
                                'last_id': last_id,
                                'lead_id': lead_id
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
                            error: function(xhr, status, err) {
                                handleAjaxError(xhr, status, err, "Error loading next page.");
                            }
                        });

                    } else {
                        handleAjaxError(null, null, null, "Save failed: " + (data.message || "Unknown error."));
                    }

                } catch (e) {
                    console.error('Invalid JSON response:', response);
                    handleAjaxError(null, null, null, "Received invalid response from server.");
                }
            },
            error: function(xhr, status, err) {
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

    function showFeasibilityReport(feasibility_id) {
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#dynamic_popupxxl").html(loader);
        jQuery.ajax({
            url: "views/custom_view_feasibility_report.php",
            data: {
                'feasibility_id': feasibility_id
            },
            type: "GET",
            success: function(data) {
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error: function() {}
        });
    }

    function withOutSaving(page, rid = '', lead_id = '') {
        var loader = '<div class="text-center" id="loader-show_3"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);

        $.ajax({
            url: `views/${page}.php`,
            type: 'GET',
            data: {
                'last_id': rid,
                'lead_id': lead_id
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

    function reOpenFeasibility(feasibility_id) {
        if (!feasibility_id) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Invalid feasibility ID.', '');
            return;
        }
        const appName = "<?php echo ucwords($_SESSION['config']['name']); ?>";

        swal({
            title: appName,
            text: "Are you sure you want to reopen this feasibility?",
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                $.post('ajax.php', {
                    page: 'reOpenFeasibility',
                    feasibility_id: feasibility_id
                }, function(resp) {
                    swal(appName, "Feasibility reopened successfully.", "success");
                    setTimeout(() => withOutSaving('custom_insert_feasibility_requirements', '', feasibility_id), 1500);
                });
            }
        });

    }
</script>
