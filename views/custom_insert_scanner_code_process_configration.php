<?php
$_SESSION['apply_js']['multiselest'] = '1';

$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$process_id = $rid['1'];

$url = API . "?mode=gettableandprocessdetails&method=get&Rid=".$process_id;
$response = callAPI($url);
$data = json_decode($response, true);
$tables = $data['tables'];
$process_details = $data['process_details'] ?? null;
$existing_field_mappings = [];
if (!empty($process_details['field_mappings'])) {
    $existing_field_mappings = json_decode($process_details['field_mappings'], true);
}

$table_fields = [];

if (isset($_POST['submit_process'])) {

    $process_data = [
        'Rid' => $_POST['Rid'],
        'process_name' => $_POST['process_name'],
        'selected_table' => $_POST['selected_table'],
        'field_mappings' => []
    ];

    if (!empty($_POST['selected_fields'])) {
        foreach ($_POST['selected_fields'] as $index => $field) {
            if (!empty($field)) {
                $process_data['field_mappings'][] = [
                    'original_field' => $field,
                    'custom_label' => $_POST['custom_labels'][$index] ?? $field,
                    'order_number' => $_POST['field_orders'][$index] ?? ($index + 1)
                ];
            }
        }
    }

    $url = API . "?mode=insertprocessdetails&method=post";
    $response = file_post_contents($url, $process_data, 'update');
    $result = json_decode($response, true);
    $response = $result['result'] ?? null;
    if ($response && $response['status'] == 'sucess') {
        if ($response['LASTID'] == 0) {
            $msgcode = 3001;
        } else {
            $msgcode = 3000;
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index.php?option=COM_LIST_SCANNER_CODE_PROCESS_CONFIGRATION");
            exit;
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function () {
                if (document.readyState == "complete") {
                    tostarnotify(
                        'top', 'right',
                        'fa fa-thumbs-up',
                        'form-label label label-success',
                        'animated flipInX',
                        'animated flipOutX',
                        '<?php echo $_SESSION['error_message'][$msgcode]; ?>',
                        ''
                    );
                }
            }
        </script>
        <?php
    } else {
        if (!empty($response['status']) && $response['status'] == 'fail') {
            $dis_err = explode(",", $response['other'] ?? '');
            $dis_er = [];
            foreach ($dis_err as $va) {
                $dis_er[] = $dis_labels[$va] ?? $va;
            }
            $error_message =
                ($response['error_no'] ?? '') . " : " .
                ($_SESSION['error'][$response['error_no']] ?? '') . " : " .
                implode(", ", (array)$dis_er);
        } else {
            $error_message = $response['message'] ?? "Unknown Error";
        }
    }
}
?>

<div class="page-body">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="ti-settings me-2 text-primary"></i>Scanner Code Process Configuration</h5>
                    <a href="javascript:history.go(-1)" class="btn btn-outline-primary btn-sm">
                        <i class="icofont icofont-reply"></i> Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="progress mb-4" style="height:25px;border-radius:50px;">
                        <div class="progress-bar bg-primary text-white" id="formProgress" style="width:33.3%;border-radius:50px;">Step 1/3</div>
                    </div>

                    <form id="processForm" method="POST">
                        <!-- Hidden field for Rid -->
                        <input type="hidden" name="Rid" value="<?php echo $process_details ? $process_details['id'] : $process_id; ?>">

                        <!-- STEP 1: Process Name -->
                        <div class="step" id="step1">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <span class="form-label">Process Name</span>
                                    <input aria-label="Process Name" type="text" class="form-control" name="process_name" value="<?php echo $process_id ? htmlspecialchars($process_details['process_name']) : ''; ?>" placeholder="Enter process name">
                                </div>
                            </div>
                        </div>


                        <div class="step d-none" id="step2">
                            <h5 class="fw-bold text-secondary mb-3"><i class="ti-database me-2 text-primary"></i>Table Selection</h5>
                            <div class="row g-3">
                                <div class="col-md-6 w-full">
                                    <span class="form-label">Application Table <span class="text-danger">*</span></span></br>
                                    <select aria-label="Ected Table" class="form-select js-example-basic-single required" name="selected_table" id="tableSelect" onchange="onTableSelect(this.value)">
                                        <option value="">Choose a table to configure...</option>
                                        <?php foreach ($tables as $table_key => $table_name): ?>
                                            <option value="<?php echo $table_key; ?>"
                                                <?php echo ($process_details && $process_details['process_table'] == $table_key) ? 'selected' : ''; ?>>
                                                <?php echo $table_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select></br>
                                    <small class="text-muted">Choose a table to create the custom process.</small>
                                </div>
                            </div>
                        </div>

                        <!-- STEP 3: Field Configuration -->
                        <div class="step d-none" id="step3">
                            <h5 class="fw-bold text-secondary mb-3"><i class="ti-list me-2 text-primary"></i>Field Configuration</h5>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Field Mappings</span>
                                <button type="button" class="btn btn-success btn-sm" id="addFieldBtn">
                                    <i class="fa fa-plus me-1"></i>Add Field
                                </button>
                            </div>

                            <div id="fieldMappingContainer">
                                <!-- Dynamic field mapping rows will be added here -->
                            </div>
                        </div>

                        <div class="d-flex justify-content-between pt-3">
                            <div class="d-flex w-100 gap-3">
                                <button type="button" id="prevBtn" class="btn btn-outline-secondary step-btn d-none">Back</button>
                                <button type="button" id="nextBtn" class="btn btn-primary step-btn">Next</button>
                                <button type="submit" name="submit_process" id="saveBtn" class="btn btn-success step-btn d-none">
                                    <i class="fa fa-save me-1"></i>Save Process
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="col-lg-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold" id="previewHeader">Configuration Preview</div>
                <div class="card-body" id="previewCard">
                    <p class="text-muted">Fill the form to see configuration preview...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const tableFields = <?php echo json_encode($table_fields); ?>;
const existingFieldMappings = <?php echo json_encode($existing_field_mappings); ?>;
const processDetails = <?php echo json_encode($process_details); ?>;
let currentStep = 1;
const totalSteps = 3;
let fieldCounter = 0;

function showStep(step) {
    document.querySelectorAll('.step').forEach(x => x.classList.add('d-none'));
    document.getElementById('step' + step).classList.remove('d-none');

    const progressBar = document.getElementById('formProgress');
    progressBar.style.width = (step / totalSteps * 100) + '%';
    progressBar.innerText = `Step ${step}/${totalSteps}`;

    document.getElementById('prevBtn').classList.toggle('d-none', step === 1);
    document.getElementById('nextBtn').classList.toggle('d-none', step === totalSteps);
    document.getElementById('saveBtn').classList.toggle('d-none', step !== totalSteps);

    updatePreview();
}

function validateStep(step) {
    let isValid = true;
    const stepEl = document.getElementById('step' + step);
    const requiredFields = stepEl.querySelectorAll('.required');

    requiredFields.forEach(field => {
        field.classList.remove('is-invalid');
        if (!field.readOnly && !field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        }
    });

    if (step === 3) {
        const fieldRows = document.querySelectorAll('.field-mapping-row');
        if (fieldRows.length === 0) {
            alert('Please add at least one field mapping');
            return false;
        }

        fieldRows.forEach(row => {
            const selectedField = row.querySelector('select[name="selected_fields[]"]');
            const customLabel = row.querySelector('input[name="custom_labels[]"]');
            const fieldOrder = row.querySelector('select[name="field_orders[]"]');

            if (!selectedField.value || !customLabel.value.trim() || !fieldOrder.value) {
                selectedField.classList.add('is-invalid');
                customLabel.classList.add('is-invalid');
                fieldOrder.classList.add('is-invalid');
                isValid = false;
            }
        });
    }

    return isValid;
}

document.getElementById('nextBtn').onclick = () => {
    if (!validateStep(currentStep)) return;

    if (currentStep === 2) {
        const selectedTable = document.getElementById('tableSelect').value;
        if (selectedTable) {
            loadTableFields(selectedTable);
        }
    }

    currentStep++;
    showStep(currentStep);
};

document.getElementById('prevBtn').onclick = () => {
    currentStep = Math.max(1, currentStep - 1);
    showStep(currentStep);
};

function onTableSelect(selectedTable) {
    if (selectedTable) {
        fetchTableFields(selectedTable);
    }
    updatePreview();
}

document.getElementById('tableSelect').addEventListener('change', function() {
    if (!this.value) return;
    onTableSelect(this.value);
});

document.getElementById('addFieldBtn').addEventListener('click', function() {
    const selectedTable = document.getElementById('tableSelect').value;
    if (selectedTable) {
        addFieldMappingRow(selectedTable);
    }
});

function fetchTableFields(tableName) {
    const container = document.getElementById('fieldMappingContainer');
    container.innerHTML = '<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading table fields...</div>';

    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {
            page: 'gettablestructure',
            table_name: tableName
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result && Array.isArray(result) && result.length > 0) {
                    console.log("Found field names array:", result);
                    const fields = {};
                    result.forEach(fieldName => {
                        const readableName = fieldName.split('_')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(' ');
                        fields[fieldName] = readableName;
                    });
                    tableFields[tableName] = fields;
                    loadTableFields(tableName);
                } else {
                    console.log("No valid field names found, trying fallback method");
                    fetchTableFieldsFallback(tableName);
                }
            } catch (e) {
                console.error('Error parsing structure response:', e);
                console.error('Raw response:', response);
                fetchTableFieldsFallback(tableName);
            }
        },
        error: function(xhr, status, error) {
            console.error('Structure AJAX Error:', error);
            fetchTableFieldsFallback(tableName);
        }
    });
}

function fetchTableFieldsFallback(tableName) {
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {
            page: 'gettablefields',
            table_name: tableName
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.rows && typeof result.rows === 'object' && result.rows !== null) {
                    console.log("Processing rows data:", result.rows);
                    const fields = extractFieldsFromResponse(result.rows);
                    tableFields[tableName] = fields;
                    loadTableFields(tableName);
                } else if (result.status === 'success' && result.fields) {
                    console.log("Processing fields data:", result.fields);
                    tableFields[tableName] = result.fields;
                    loadTableFields(tableName);
                } else {
                    console.log("No valid data found in fallback. Rows:", result.rows, "Status:", result.status);
                    createBasicFieldStructure(tableName);
                }
            } catch (e) {
                console.error('Error parsing fallback response:', e);
                console.error('Raw response:', response);
                createBasicFieldStructure(tableName);
            }
        },
        error: function(xhr, status, error) {
            console.error('Fallback AJAX Error:', error);
            createBasicFieldStructure(tableName);
        }
    });
}

function createBasicFieldStructure(tableName) {
    const container = document.getElementById('fieldMappingContainer');
    container.innerHTML = '<div class="alert alert-warning">Unable to load table fields automatically. Please check if the table exists and has data.</div>';
}

function fetchTableStructure(tableName, selectQuery = null) {
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {
            page: 'gettablestructure',
            table_name: tableName,
            query: selectQuery
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.fields && typeof result.fields === 'object') {
                    tableFields[tableName] = result.fields;
                    loadTableFields(tableName);
                } else if (result.columns && Array.isArray(result.columns)) {
                    const fields = {};
                    result.columns.forEach(col => {
                        const fieldName = col.Field || col.column_name || col;
                        const readableName = fieldName.split('_')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(' ');
                        fields[fieldName] = readableName;
                    });
                    tableFields[tableName] = fields;
                    loadTableFields(tableName);
                } else {
                    console.log("No valid structure data found, trying DESCRIBE query");
                }
            } catch (e) {
                console.error('Error parsing structure response:', e);
            }
        },
        error: function(xhr, status, error) {
            console.error('Structure AJAX Error:', error);
        }
    });
}

function extractFieldsFromResponse(rowsData) {
    const fields = {};
    if (rowsData && typeof rowsData === 'object') {
        Object.keys(rowsData).forEach(fieldName => {
            const readableName = fieldName.split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
            fields[fieldName] = readableName;
        });
    }
    return fields;
}

function loadTableFields(tableName) {
    clearFieldMappings();
    const fields = tableFields[tableName];

    if (fields && Object.keys(fields).length > 0) {
        if (existingFieldMappings && existingFieldMappings.length > 0) {
            existingFieldMappings.forEach((mapping, index) => {
                addFieldMappingRow(tableName, mapping);
            });
        } else {
            addFieldMappingRow(tableName);
        }
    } else {
        const container = document.getElementById('fieldMappingContainer');
        container.innerHTML = '<div class="alert alert-warning">No fields found for this table</div>';
    }
}

function addFieldMappingRow(tableName, existingMapping = null) {
    const container = document.getElementById('fieldMappingContainer');
    const fields = tableFields[tableName];

    if (!fields || Object.keys(fields).length === 0) {
        container.innerHTML = '<div class="alert alert-warning">No fields available for this table</div>';
        return;
    }

    fieldCounter++;

    const rowDiv = document.createElement('div');
    rowDiv.className = 'field-mapping-row mb-3 p-3 border rounded bg-light';
    rowDiv.setAttribute('data-row', fieldCounter);
    const selectedField = existingMapping ? existingMapping.original_field : '';
    const customLabel = existingMapping ? existingMapping.custom_label : '';
    const orderNumber = existingMapping ? existingMapping.order_number : fieldCounter;

    rowDiv.innerHTML = `
        <div class="row g-3 align-items-center">
            <div class="col-md-4 w-full">
                <span class="form-label fw-bold">Original Field</span></br>
                <select aria-label="Ected Fields[]" class="form-select js-example-basic-single" name="selected_fields[]" onchange="updatePreview()">
                    <option value="">Select Field...</option>
                    ${Object.entries(fields).map(([key, label]) =>
                        `<option value="${key}" ${key === selectedField ? 'selected' : ''}>${label}</option>`
                    ).join('')}
                </select>
            </div>
            <div class="col-md-4">
                <span class="form-label fw-bold">Custom Label</span>
                <input aria-label="Enter custom field name" type="text" class="form-control" name="custom_labels[]"
                       value="${customLabel}" placeholder="Enter custom field name" onchange="updatePreview()">
            </div>
            <div class="col-md-3">
                <span class="form-label fw-bold">Display Order</span>
                <select aria-label="Field Orders[]" class="form-select" name="field_orders[]" onchange="updatePreview()">
                    ${Array.from({length: 20}, (_, i) => i + 1).map(num =>
                        `<option value="${num}" ${num == orderNumber ? 'selected' : ''}>${num}</option>`
                    ).join('')}
                </select>
            </div>
            <div class="col-md-1">
                <span class="form-label">&nbsp;</span>
                <button type="button" class="btn btn-danger btn-sm d-block" onclick="removeFieldRow(${fieldCounter})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(rowDiv);
    $(rowDiv).find('.js-example-basic-single').select2();

    updatePreview();
}

function removeFieldRow(rowId) {
    const row = document.querySelector(`[data-row="${rowId}"]`);
    if (row) {
        row.remove();
        updatePreview();
    }
}

function clearFieldMappings() {
    document.getElementById('fieldMappingContainer').innerHTML = '';
    fieldCounter = 0;
}

function updatePreview() {
    let html = '<div class="list-group list-group-flush">';
    const form = document.getElementById('processForm');
    let hasData = false;

    const processName = form.querySelector('input[name="process_name"]').value;
    if (processName) {
        hasData = true;
        html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold">📋 Process Name</span>
                    <span class="badge bg-primary text-white px-3 py-2">${processName}</span>
                 </div>`;
    }

    const tableSelect = document.getElementById('tableSelect');
    if (tableSelect.value) {
        hasData = true;
        const tableName = tableSelect.options[tableSelect.selectedIndex].text;
        html += `<div class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fw-bold">🗃️ Process Table</span>
                    <span class="badge bg-info text-white px-3 py-2">${tableName}</span>
                 </div>`;
    }

    const fieldRows = document.querySelectorAll('.field-mapping-row');
    if (fieldRows.length > 0) {
        hasData = true;
        html += `<div class="list-group-item">
                    <span class="fw-bold">⚙️ Field Mappings (${fieldRows.length})</span>
                    <div class="mt-2">`;

        fieldRows.forEach((row, index) => {
            const selectedField = row.querySelector('select[name="selected_fields[]"]');
            const customLabel = row.querySelector('input[name="custom_labels[]"]');
            const fieldOrder = row.querySelector('select[name="field_orders[]"]');

            if (selectedField.value && customLabel.value) {
                const originalFieldText = selectedField.options[selectedField.selectedIndex].text;
                html += `<small class="d-block text-muted">
                            ${fieldOrder.value}. ${originalFieldText} → ${customLabel.value}
                         </small>`;
            }
        });
        html += `</div></div>`;
    }
    html += '</div>';
    document.getElementById('previewCard').innerHTML = hasData ? html : '<p class="text-muted">Fill the form to see configuration preview...</p>';
    document.getElementById('previewHeader').innerText = hasData ? 'Configuration Preview' : 'Preview';
}

document.getElementById('processForm').addEventListener('submit', function(e) {
    if (!validateStep(3)) {
        e.preventDefault();
        return false;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    showStep(currentStep);
    document.querySelector('input[name="process_name"]').addEventListener('input', updatePreview);

    if (processDetails && processDetails.process_table) {
        fetchTableFields(processDetails.process_table);
    }
    updatePreview();
});
</script>
