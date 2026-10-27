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
$url = API . "?mode=getFeasiblityData&method=get&feasibility_id=" . $feasibility_id;
$response = callAPI($url);
$res = json_decode($response, true);
$checklist_res = $res['feasibility_checklist_res'] ?? array();
$feasibility_requirement_res = $res['feasibility_requirement_res'] ?? array();

?>

<div class="slide" data-slide="4" <?php echo ($feasibility_requirement_res['current_status'] == ST_APPROVED) ? 'style="pointer-events:none; opacity:0.7"' : '';  ?>>
    <div class="row mt-4">
        <div class="col-lg-12 m-t-10 m-b-10">
            <h5 style="font-weight: bold;">Feasibility Checklist</h5>
        </div>
        <div class="table-responsive">
            <form id="checklistForm">
                <table class="table table-bordered" id="checklistTable">
                    <thead>
                        <tr>
                            <th scope="col">Checklist Item <span class='text-danger'>*</span></th>
                            <th scope="col">Type <span class='text-danger'>*</span></th>
                            <th scope="col">Is Done?</th>
                            <th scope="col">Comment</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="checklistTableBody">
                        <?php if (!empty($checklist_res)): ?>
                            <?php foreach ($checklist_res as $row): ?>
                                <tr>
                                    <td><input aria-label="Checklist Item Name[]" type="text" class="form-control" name="checklist_item_name[]" value="<?php echo htmlspecialchars($row['item_name']) ?>" required></td>
                                    <input type="hidden" name="check_row_id[]" value="<?php echo $row['id']; ?>" />

                                    <td>
                                        <select aria-label="Checklist Type[]" class="form-select js-example-basic-single " name="checklist_type[]" required>
                                            <option value="">Select Type</option>
                                            <option value="technical" <?php echo $row['item_type'] == 'technical' ? 'selected' : '' ?>>Technical</option>
                                            <option value="legal" <?php echo $row['item_type'] == 'legal' ? 'selected' : '' ?>>Legal</option>
                                            <option value="safety" <?php echo $row['item_type'] == 'safety' ? 'selected' : '' ?>>Safety</option>
                                            <option value="operational" <?php echo $row['item_type'] == 'operational' ? 'selected' : '' ?>>Operational</option>
                                            <option value="others" <?php echo $row['item_type'] == 'others' ? 'selected' : '' ?>>Others</option>
                                        </select>
                                    </td>
                                    <td><input aria-label="Checklist Is Done[]" type="checkbox" class="js-single" name="checklist_is_done[]" value="1" <?php echo $row['is_done'] == 1 ? 'checked' : '' ?>></td>
                                    <td><input aria-label="Checklist Comment[]" type="text" class="form-control" name="checklist_comment[]" value="<?php echo htmlspecialchars($row['comment']) ?>"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeChecklistRowBtn(this)"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td><input aria-label="Checklist Item Name[]" type="text" class="form-control" name="checklist_item_name[]" required></td>
                                <input type="hidden" name="check_row_id[]" value="" />
                                <td>
                                    <select aria-label="Checklist Type[]" class="form-select js-example-basic-single " name="checklist_type[]" required>
                                        <option value="">Select Type</option>
                                        <option value="technical">Technical</option>
                                        <option value="legal">Legal</option>
                                        <option value="safety">Safety</option>
                                        <option value="operational">Operational</option>
                                        <option value="others">Others</option>
                                    </select>
                                </td>
                                <td><input aria-label="Checklist Is Done[]" type="checkbox" class="js-single" name="checklist_is_done[]" value="1"></td>
                                <td><input aria-label="Checklist Comment[]" type="text" class="form-control" name="checklist_comment[]"></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeChecklistRowBtn(this)"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                <button type="button" class="btn btn-primary" onclick="addChecklistRowBtn(this)"><i class="fa fa-plus"></i> Add More</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <input type="hidden" name="feasibility_id" value="<?php echo htmlspecialchars($feasibility_id); ?>">

                <div style="pointer-events: auto; opacity: 1;" class="row">
                    <div class="col-lg-6 mt-3">
                        <button class="btn btn-secondary prev-btn" type="button" onclick="previous('custom_insert_lead_feasibility_boms', <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">Previous</button>
                    </div>
                    <div class="col-lg-6 text-end mt-3">
                        <?php if ($feasibility_requirement_res['current_status'] == ST_APPROVED) { ?>
                            <button class="btn btn-primary next-btn" type="button" id="saveNextBtn" onclick="withOutSaving('custom_insert_feasibility_docs', <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>, <?php echo $feasibility_requirement_res['current_status']; ?>)">Save & Next</button>
                        <?php } else { ?>
                            <button class="btn btn-primary next-btn" type="button" onclick="saveAndNextChecklist('custom_insert_feasibility_docs', <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">
                                Save & Next
                            </button><?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function saveAndNextChecklist(nextPage, feasibility_id, lead_id) {
        const form = document.getElementById('checklistForm');
        const rows = form.querySelectorAll('#checklistTableBody tr');

        for (let i = 0; i < rows.length; i++) {
            const itemName = rows[i].querySelector('[name="checklist_item_name[]"]').value.trim();
            const itemType = rows[i].querySelector('[name="checklist_type[]"]').value;

            if (!itemName) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Checklist Item is required.`, '');
                return;
            }

            if (!itemType) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Type is required.`, '');
                return;
            }
        }
        const formData = new FormData(form);

        formData.append('page', 'insertFeasibilityBomAndChecklist');
        formData.append('table_name', 'lead_feasibility_checklists');
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message']['3001']; ?>', '');

                try {
                    const data = JSON.parse(response);
                    $.ajax({
                        url: `views/${nextPage}.php`,
                        type: 'GET',
                        data: {
                            'feasibility_id': feasibility_id,
                            'lead_id': lead_id
                        },
                        success: function(res) {
                            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message']['3001']; ?>', '');

                            $('#feasibilitySlides').html(res);

                            reinitSelect2(); // Reinitialize select2 after loading new content

                        },
                        error: function(xhr, status, error) {
                            handleAjaxError(xhr, status, err, "Error loading next page.");

                        }
                    });
                } catch (e) {
                    console.error("Invalid response:", response);
                    handleAjaxError(null, null, null, "Received invalid response from server.");
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

    function addChecklistRowBtn(btn) {
        console.log('test');
        const tbody = document.getElementById('checklistTableBody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input aria-label="Checklist Item Name[]" type="text" class="form-control" name="checklist_item_name[]" required></td>
            <input type="hidden" name="check_row_id[]" value="" />
            <td>
                <select aria-label="Checklist Type[]" class="form-select js-example-basic-single " name="checklist_type[]" required>
                    <option value="">Select Type</option>
                    <option value="technical">Technical</option>
                    <option value="legal">Legal</option>
                    <option value="safety">Safety</option>
                    <option value="operational">Operational</option>
                    <option value="others">Others</option>
                </select>
            </td>
            <td><input aria-label="Checklist Is Done[]" type="checkbox" class="js-single" name="checklist_is_done[]" value="1"></td>
            <td><input aria-label="Checklist Comment[]" type="text" class="form-control" name="checklist_comment[]"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeChecklistRowBtn(this)"><i class="fa fa-trash"></i></button></td>
        `;
        tbody.appendChild(newRow);
        document.querySelectorAll('.js-example-basic-single').forEach(function(el) {
            if ($(el).data('select2')) {
                $(el).select2('destroy');
            }
            $(el).select2();
        });

    }

    function removeChecklistRowBtn(btn) {
        console.log('test:', btn);

        const row = btn.closest('tr');
        row.remove();
    }

    function withOutSaving(page, feasibility_id, lead_id, current_status = '') {
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);

        $.ajax({
            url: `views/${page}.php`,
            type: 'GET',
            data: {
                'feasibility_id': feasibility_id,
                'lead_id': lead_id,
                'current_status': current_status
            },
            success: function(res) {
                $('#feasibilitySlides').html(res);
                $(".datepicker").datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true
                });
                reinitSelect2(); // Reinitialize select2 after loading new content
            },
            error: function(xhr, status, error) {
                alert('Error loading next page: ' + error);
            }
        });
    }
</script>
