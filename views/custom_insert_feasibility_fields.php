<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$rid = $_GET['previous_id'] ?? '';

$url = API . "?mode=getFeasiblityData&method=get&rid={$rid
}&lead_id={$_GET['lead_id']
}";
$response = callAPI($url);
$res = json_decode($response, true);
$feasibility_requirement_res = $res['feasibility_requirement_res'] ?? array();
$requirement_json = $feasibility_requirement_res['requirement_json'] ?? '{}';

$feasibility_types_res = $res['feasibility_types_res'] ?? array();

?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-label" for="feasibility_type_id">Feasibility Type</label>
                    <select class="form-select js-example-basic-single form-control" id="feasibility_type_id"
                        onchange="loadFeasibilityFields()">
                        <?php foreach ($feasibility_types_res as $id => $name) { ?>
                            <option value="<?php echo $id; ?>"
                                <?= (isset($feasibility_requirement_res['feasibility_type_id']) && $feasibility_requirement_res['feasibility_type_id'] == $id) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
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


            <div class="row mt-4">
                <div class="col-lg-12 m-t-10 m-b-10 ">
                    <h5 style="font-weight: bold;">Evaluation</h5>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <span class="form-label">Evaluation Date <span class='text-danger'>*</span></span>
                        <input aria-label="Evaluation Date" type="date" class="form-control" name="evaluation_date"
                            value="<?php echo isset($data['evaluation_date']) ? htmlspecialchars($data['evaluation_date']) : ''; ?>">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <span class="form-label">Evaluation By <span class='text-danger'>*</span></span>
                        <select aria-label="Evaluated By" class="form-select js-example-basic-single form-control" name="evaluated_by">
                            <option value="">Select Evaluator</option>
                            <?php foreach ($users_res as $id => $name) { ?>
                                <option value="<?php echo $id; ?>" <?php
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
                        <input aria-label="Start Date" type="date" class="form-control" name="start_date"
                            value="<?php echo isset($data['start_date']) ? htmlspecialchars($data['start_date']) : ''; ?>">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <span class="form-label">End Date <span class='text-danger'>*</span></span>
                        <input aria-label="End Date" type="date" class="form-control" name="end_date"
                            value="<?php echo isset($data['end_date']) ? htmlspecialchars($data['end_date']) : ''; ?>"
                            onchange="validateEndDate()">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <span class="form-label">Estimated Budget <span class='text-danger'>*</span></span>
                        <input aria-label="Estimated Budget" type="number" class="form-control text-right" name="estimated_budget"
                            value="<?php echo isset($data['estimated_budget']) ? htmlspecialchars($data['estimated_budget']) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="col-lg-12 m-t-10 m-b-10 ">
                <h5 style="font-weight: bold;">Bill of Materials (BOM)</h5>
            </div>
            <table class="table " id="bomTable">
                <thead>
                    <tr>
                        <th scope="col">Item Name <span class='text-danger'>*</span></th>
                        <th scope="col">Unit Of Measurement</th>
                        <th scope="col">Quantity <span class='text-danger'>*</span></th>
                        <th scope="col">Cost</th>
                        <th scope="col">Planned Required By</th>
                        <th scope="col">Planned Returned By</th>
                        <th scope="col">Actual Required By</th>
                        <th scope="col">Actual Returned By</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="bomTableBody">
                    <?php if (count((array) $feasibility_bom_res) > 0) {
                        foreach ($feasibility_bom_res as $row) { ?>
                            <tr>
                                <td><input aria-label="Item Name[]" type="text" class="form-control" name="item_name[]"
                                        value="<?= htmlspecialchars($row['item_name']) ?>"></td>
                                <td><input aria-label="Unit[]" type="text" class="form-control" name="unit[]"
                                        value="<?= htmlspecialchars($row['unit']) ?>"></td>
                                <td><input aria-label="Quantity[]" type="number" class="form-control text-right" name="quantity[]"
                                        value="<?= htmlspecialchars($row['quantity']) ?>"></td>
                                <td><input aria-label="Cost[]" style="width:5.5rem;" type="number" class="form-control text-right" name="cost[]"
                                        value="<?= htmlspecialchars($row['estimated_cost']) ?>"></td>
                                <td><input aria-label="Planned Request By[]" type="date" class="form-control" name="planned_request_by[]"
                                        value="<?= htmlspecialchars($row['planned_request_by']) ?>"></td>
                                <td><input aria-label="Planned Return By[]" type="date" class="form-control" name="planned_return_by[]"
                                        value="<?= htmlspecialchars($row['planned_return_by']) ?>"></td>
                                <td><input aria-label="Actual Request By[]" type="date" class="form-control" name="actual_request_by[]"
                                        value="<?= htmlspecialchars($row['actual_request_by']) ?>"></td>
                                <td><input aria-label="Actual Return By[]" type="date" class="form-control" name="actual_return_by[]"
                                        value="<?= htmlspecialchars($row['actual_return_by']) ?>"></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i
                                            class="fa fa-trash"></i></button></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td><input aria-label="Item Name[]" type="text" class="form-control" name="item_name[]" /></td>
                            <td><input aria-label="Unit[]" type="text" class="form-control" name="unit[]" /></td>
                            <td><input aria-label="Quantity[]" type="number" class="form-control text-right" name="quantity[]" /></td>
                            <td><input aria-label="Cost[]" style="width:5.5rem;" type="number" class="form-control text-right" name="cost[]" />
                            </td>
                            <td><input aria-label="Planned Request By[]" type="date" class="form-control" name="planned_request_by[]"></td>
                            <td><input aria-label="Planned Return By[]" type="date" class="form-control" name="planned_return_by[]"></td>
                            <td><input aria-label="Actual Request By[]" type="date" class="form-control" name="actual_request_by[]"></td>
                            <td><input aria-label="Actual Return By[]" type="date" class="form-control" name="actual_return_by[]"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i
                                        class="fa fa-trash"></i></button></td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="5">
                            <button class="btn btn-primary" type="button" id="addBomRowBtn"><i class="fa fa-plus"></i>
                                Add More</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="row mt-4">
                <div class="col-lg-12 m-t-10 m-b-10">
                    <h5 style="font-weight: bold;">Feasibility Checklist</h5>
                </div>
                <div class="table-responsive">
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
                            <tr>
                                <td><input aria-label="Checklist Item Name[]" type="text" class="form-control" name="checklist_item_name[]"></td>
                                <td>
                                    <select aria-label="Checklist Type[]" class="form-select js-example-basic-single " name="checklist_type[]">
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
                                <td><button type="button" class="btn btn-danger btn-sm remove-row"><i
                                            class="fa fa-trash"></i></button></td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" class="btn btn-primary" id="addChecklistRowBtn"><i
                                            class="fa fa-plus"></i> Add More</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-12">
                    <h4 class="mb-3">Upload Supporting Documents</h4>
                    <div class="mb-3">
                        <input aria-label="File[]" type="file" class="form-control" name="file[]" multiple>
                        <input type="hidden" name="feasibility_id" id="feasibility_id"
                            value="<?php echo htmlspecialchars($feasibility_id); ?>">
                    </div>
                </div>
            </div>
        </form>
        <input type="hidden" id="requirement_json"
            value='<?= htmlspecialchars($requirement_json, ENT_QUOTES, "UTF-8") ?>'>
        <input type="hidden" name="rid" id='rid' value="<?php echo $feasibility_requirement_res['id'] ?? ''; ?>">
    </div>
</div>

<div class="modal fade" id="optionsModal" tabindex="-1" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="optionsModalLabel">Edit Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="optionsModalBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="addOptionBtn">Add New Option</button>
                <button type="button" class="btn btn-primary" id="updateOptionsBtn">Update</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
<script>
    document.addEventListener('click', function(e) {
        // Add BOM Row
        if (e.target && e.target.id === 'addBomRowBtn') {
            const tbody = document.getElementById('bomTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td><input aria-label="Item Name[]" type="text" class="form-control" name="item_name[]"  /></td>
            <td><input aria-label="Unit[]" type="text" class="form-control" name="unit[]" /></td>
            <td><input aria-label="Quantity[]" type="number" class="form-control text-right" name="quantity[]"  /></td>
            <td><input aria-label="Cost[]" style="width:5.5rem;" type="number" class="form-control text-right" name="cost[]" /></td>
            <td><input aria-label="Planned Request By[]" type="date" class="form-control" name="planned_request_by[]" ></td>
            <td><input aria-label="Planned Return By[]" type="date" class="form-control" name="planned_return_by[]" ></td>
            <td><input aria-label="Actual Request By[]" type="date" class="form-control" name="actual_request_by[]" ></td>
            <td><input aria-label="Actual Return By[]" type="date" class="form-control" name="actual_return_by[]" ></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
        `;
            tbody.appendChild(newRow);
        }
        // Remove Row
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            row.remove();
        }
    });
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'addChecklistRowBtn') {
            const tbody = document.getElementById('checklistTableBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td><input aria-label="Checklist Item Name[]" type="text" class="form-control" name="checklist_item_name[]" ></td>
            <td>
                <select aria-label="Checklist Type[]" class="form-select js-example-basic-single" name="checklist_type[]" >
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
            <td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
        `;
            tbody.appendChild(newRow);
        }

        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            row.remove();
        }
    });
</script>

<script>
    let newFieldCodeIndex = 1;

    function padCodeNumber(num) {
        return num.toString().padStart(3, '0');
    }

    function attachFieldTableListeners() {
        // Remove previous listeners to avoid duplicates
        $('#feasibilityFieldsTable').off('input change', "input[name='field_label[]'], select[name='field_type[]'], input[name='field_value[]'], select[name='field_length[]']");
        $('#feasibilityFieldsTable').on('input change', "input[name='field_label[]'], select[name='field_type[]'], input[name='field_value[]'], select[name='field_length[]']", function() {
            renderDynamicPreview();
        });
    }

    function addNewFieldRow() {
        const code = 'RQ' + padCodeNumber(newFieldCodeIndex++);
        const newRow = `
        <tr>
            <td style="width: 20%;">
                <input aria-label="Field Label[]" type='text' class='form-control' name='field_label[]' oninput='renderDynamicPreview()'>
                <input type='hidden' name='code[]' value='${code}'>
                <input type='hidden' name='is_active[]' value='0'>
            </td>
            <td style="width: 20%;">
                <select aria-label="Field Type[]" class='form-select field-type-select' name='field_type[]' onchange='handleFieldTypeChange(this); renderDynamicPreview()'>
                    <option value='text'>Text</option>
                    <option value='number'>Number</option>
                    <option value='date'>Date</option>
                    <option value='select'>Select</option>
                    <option value='radio'>Radio</option>
                </select>
            </td>
            <td style="width: 20%;">
                <input aria-label="Field Value[]" type='text' class='form-control field-value-input' name='field_value[]' oninput='renderDynamicPreview()'>
                <button type='button' class='btn btn-sm btn-success mt-3 add-options-btn' style='display:none; z-index:2;' onclick='openOptionsModal(this)'>Edit Options</button>
            </td>
            <td style="width: 20%;"><select aria-label="Field Length[]" class='form-select js-example-basic-single' name='field_length[]' onchange='renderDynamicPreview()'>
                <option value='col-md-1'>col-md-1</option>
                <option value='col-md-2'>col-md-2</option>
                <option value='col-md-3'>col-md-3</option>
                <option value='col-md-4'>col-md-4</option>
                <option value='col-md-5'>col-md-5</option>
                <option value='col-md-6' selected>col-md-6</option>
                <option value='col-md-7'>col-md-7</option>
                <option value='col-md-8'>col-md-8</option>
                <option value='col-md-9'>col-md-9</option>
                <option value='col-md-10'>col-md-10</option>
                <option value='col-md-11'>col-md-11</option>
                <option value='col-md-12'>col-md-12</option>
            </select></td>
            <td style="width: 10%;">
                <button type='button' class='btn btn-warning btn-sm toggle-mandatory-btn' onclick='toggleMandatory(this)' data-mandatory='0'>Mandatory</button>
                <input type='hidden' name='mandatory[]' value='0'>
            </td>
            <td style="width: 10%;"><button type='button' class="btn btn-danger btn-sm" onclick='removeFieldRow(this)'><i class="fa fa-trash"></i></button></td>
        </tr>`;
        $('#feasibilityFieldsTable tbody').append(newRow);
        renderDynamicPreview();
        $(".js-example-basic-single").select2();
        attachFieldTableListeners(); // Attach listeners after adding new row
    }

    function handleFieldTypeChange(select) {
        const row = $(select).closest('tr');
        const type = $(select).val();
        const addBtn = row.find('.add-options-btn');
        const valueInput = row.find('.field-value-input');
        if (type === 'select' || type === 'radio') {
            addBtn.show();
            valueInput.prop('readonly', true);
        } else {
            addBtn.hide();
            valueInput.prop('readonly', false);
            valueInput.val('');
        }
    }

    let currentOptionsRow = null;

    function openOptionsModal(btn) {
        currentOptionsRow = $(btn).closest('tr');
        const valueInput = currentOptionsRow.find(".field-value-input");
        let options = [];
        try {
            options = JSON.parse(valueInput.val() || '[]');
        } catch (e) {
            options = [];
        }
        if (!Array.isArray(options) || options.length === 0) {
            options = ["Yes", "No"];
        }
        // Clear modal body
        $('#optionsModalBody').empty();
        options.forEach(opt => {
            $('#optionsModalBody').append(`<div class='input-group mb-2 option-row'>
                <input aria-label="Input field" type='text' class='form-control option-input' value='${opt}'>
                <button type='button' class='btn btn-danger btn-sm remove-option-btn'><i class='fa fa-trash'></i></button>
            </div>`);
        });
        $('#optionsModal').modal('show');
    }

    $(document).on('click', '#addOptionBtn', function() {
        $('#optionsModalBody').append(`<div class='input-group mb-2 option-row'>
            <input aria-label="Input field" type='text' class='form-control option-input' value=''>
            <button type='button' class='btn btn-danger btn-sm remove-option-btn'><i class='fa fa-trash'></i></button>
        </div>`);
    });
    // Remove option in modal
    $(document).on('click', '.remove-option-btn', function() {
        $(this).closest('.option-row').remove();
    });
    $(document).on('click', '#updateOptionsBtn', function() {
        let opts = [];
        let hasEmpty = false;
        $('#optionsModalBody .option-input').each(function() {
            const val = $(this).val().trim();
            if (!val) {
                hasEmpty = true;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
                opts.push(val);
            }
        });
        if (hasEmpty) {
            if ($('#optionsModalBody .option-error').length === 0) {
                $('#optionsModalBody').prepend('<div class="option-error text-danger mb-2">All options are required.</div>');
            }
            return;
        } else {
            $('#optionsModalBody .option-error').remove();
        }
        if (currentOptionsRow) {
            currentOptionsRow.find('.field-value-input').val(JSON.stringify(opts)).trigger('input');
        }
        $('#optionsModal').modal('hide');
    });

    function removeFieldRow(button) {
        $(button).closest('tr').remove();
        renderDynamicPreview();
    }

    function renderDynamicPreview() {
        let html = '';

        $('#feasibilityFieldsTable tbody tr').each(function() {
            const label = $(this).find("input[name='field_label[]']").val();
            const code = $(this).find("input[name='code[]']").val();
            const type = $(this).find("select[name='field_type[]']").val();
            const values = $(this).find("input[name='field_value[]']").val();
            const length = $(this).find("select[name='field_length[]']").val();
            const active = $(this).find("select[name='is_active[]']").val();
            const mandatory = $(this).find("input[name='mandatory[]']").val();

            if (active === '1') return; // skip inactive

            let isRequired = mandatory === '0';
            let star = isRequired ? ' <span class="text-danger">*</span>' : '';
            let requiredAttr = isRequired ? 'required' : '';

            html += `<div class='${length} mb-3'>`;
            html += `<span class='form-label'>${label}${star}</span>`;

            if (['text', 'number', 'date'].includes(type)) {
                html += `<input aria-label="${code}" type='${type}' name='${code}' class='form-control ${type === 'number' ? 'text-right' : ''}' ${requiredAttr}>`;
            } else if (type === 'select') {
                let opts = [];
                try {
                    opts = JSON.parse(values || '[]');
                } catch (e) {
                    opts = [];
                }
                html += `<select aria-label="${code}" name='${code}' class='form-select js-example-basic-single' ${requiredAttr}>`;
                opts.forEach(v => {
                    html += `<option value='${v}'>${v}</option>`;
                });
                html += `</select>`;
            } else if (type === 'radio') {
                var firstRow = true;
                let opts = [];
                try {
                    opts = JSON.parse(values || '[]');
                } catch (e) {
                    opts = [];
                }
                opts.forEach(v => {
                    var required = requiredAttr && firstRow ? 'checked' : '';

                    html += `<div class='form-check'>
                        <input class='form-check-input' type='radio' name='${code}' value='${v}' id='${code}_${v}'  ${required}>
                        <label class='form-check-label' for='${code}_${v}'>${v}</label>
                    </div>`;
                    firstRow = false;

                });
            }
            html += `</div>`;
        });
        $('#dynamic_preview').html(html);
    }

    function toggleMandatory(btn) {
        var $btn = $(btn);
        var $input = $btn.closest('tr').find("input[name='mandatory[]']");
        var current = $btn.data('mandatory');
        if (current == 0) {
            $btn.data('mandatory', 1);
            $btn.text('Unmandatory');
            $input.val('1');
            $btn.removeClass('btn-warning').addClass('btn-success');
        } else {
            $btn.data('mandatory', 0);
            $btn.text('Mandatory');
            $input.val('0');
            $btn.removeClass('btn-success').addClass('btn-warning');
        }
        renderDynamicPreview();
    }
</script>

<script>
    $(document).ready(function() {
        // Load fields on page load
        loadFeasibilityFields();
    });

    function saveCustomFields() {
        let customFields = [];
        $('#feasibilityFieldsTable tbody tr').each(function() {
            const label = $(this).find("input[name='field_label[]']").val();
            const code = $(this).find("input[name='code[]']").val();
            const type = $(this).find("select[name='field_type[]']").val();
            const value = $(this).find("input[name='field_value[]']").val();
            const length = $(this).find("select[name='field_length[]']").val();
            const isActive = $(this).find("select[name='is_active[]']").val();
            const mandatory = $(this).find("input[name='mandatory[]']").val();
            if (label && code) {
                customFields.push({
                    field_label: label,
                    code: code,
                    field_type: type,
                    field_value: value,
                    field_length: length,
                    is_active: isActive,
                    mandatory: mandatory
                });
            }
        });

        if (customFields.length === 0) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please add at least one field.', '');

            return;
        }

        const feasibility_id = $('#feasibility_type_id').val();
        const lead_id = $('#lead_id').val();

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                page: 'insertFeasibilityfeildslist',
                table_name: 'feasibility_fields',
                feasibility_id: feasibility_id,
                lead_id: lead_id,
                fields_data: JSON.stringify(customFields)
            },
            success: function(response) {
                console.log("Raw response:", response);
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.status === 'success') {
                    swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Custom fields saved successfully!", "success");

                    loadFeasibilityFields();
                } else {
                    swal("<?php echo ucwords($_SESSION['config']['name']); ?>", 'Failed to save fields: ' + (res.message || 'Unknown error.'), "danger");

                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX error here
                const appName = "<?php echo ucwords($_SESSION['config']['name']); ?>";
                swal({
                    title: appName,
                    text: "An error occurred while Saving data.\n\nStatus: " + status + "\nError: " + error,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "OK"
                });
            }
        });
    }

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
                'page': 'getFeasibilityTableAndFields'
            },
            success: function(response) {
                $('#dynamic_fields').html(JSON.parse(response));
                $(".js-example-basic-single").select2();

                attachFieldTableListeners(); // Attach listeners after loading fields
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
</script>
