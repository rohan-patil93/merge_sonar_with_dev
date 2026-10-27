<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$feasibility_id = '';
$last_id = '';
if (isset($_GET['previous_id']) && $_GET['previous_id'] != '') {
    $feasibility_id = $_GET['previous_id'];
}
if (isset($_GET['last_id']) && $_GET['last_id'] != '' &&  isset($_GET['feasibility_id'])  && $_GET['feasibility_id'] != '') {
    $last_id = $_GET['last_id'];
    $feasibility_id = $_GET['feasibility_id'];
}

$url = API . "?mode=getFeasiblityData&method=get&feasibility_id=" . $feasibility_id;
$response = callAPI($url);
$res = json_decode($response, true);
$feasibility_bom_res = $res['feasibility_bom_res'] ?? array();
$uom_res = $res['uom_res'] ?? array();
$items_res = $res['items_res'] ?? array();
$feasibility_requirement_res = $res['feasibility_requirement_res'] ?? array();

?>

<div class="slide" data-slide="3" <?php echo ($feasibility_requirement_res['current_status'] == ST_APPROVED) ? 'style="pointer-events:none; opacity:0.7"' : '';  ?>>
    <div class="row mt-4">
        <h4>Bill of Materials (BOM)</h4>
        <div class="table-responsive">
            <form id="bomForm">
                <table class="table " id="bomTable">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 30%;">Item Name/UOM<span class='text-danger'>*</span></th>

                            <th scope="col" style="width: 10%;">Qty/Price <span class='text-danger'>*</span></th>

                            <th scope="col" style="width: 10%;">Amount</th>
                            <th scope="col" style="width: 20%;">Planned/Actual Required</th>
                            <th scope="col" style="width: 20%;">Planned/Actual Returned</th>

                            <th scope="col" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bomTableBody">
                        <?php $final_total_cost = 0;
                        if (count((array)$feasibility_bom_res) > 0) {

                            foreach ($feasibility_bom_res as $row) {
                                $final_total_cost += $row['total_amount'] ?? 0;
                        ?>
                                <tr>
                                    <td class="w-full" style="width: 30%;">

                                        <select aria-label="Item Name[]" name="item_name[]" class="form-select js-example-basic-single form-control item-select" required onchange="onBOMItemChange(this)">
                                            <option value="">Select Item</option>
                                            <?php foreach ($items_res as $id => $item) { ?>
                                                <option value="<?php echo $id; ?>" <?php echo ($row['item_id'] == $id) ? 'selected' : ''; ?> data-uom="<?php echo $item['purchase_uom']; ?>"><?php echo htmlspecialchars($item['item_name']); ?></option>
                                            <?php } ?>
                                            <option value="others">Others</option>
                                        </select>
                                        <input type="hidden" name="bom_row_id[]" value="<?php echo $row['id']; ?>" />

                                        <input aria-label="Enter Item Name" type="text" name="new_item_name[]" class="form-control new-item-name mt-3" style="display: none;" placeholder="Enter Item Name">
                                        <div class="mt-2 w-full" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="UOM">
                                            <select aria-label="Unit[]" style="width:5.5rem;" name="unit[]" class="form-select js-example-basic-single form-control unit-select" required>
                                                <option value="">Select UOM</option>
                                                <?php foreach ($uom_res as $id => $item) { ?>
                                                    <option value="<?php echo $id; ?>" <?php echo ($row['unit'] == $id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($item['uom_short_code']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </td>

                                    <td style="width: 10%;">
                                        <input aria-label="Quantity[]" min='0' type="number" class="form-control text-right" name="quantity[]" value="<?= htmlspecialchars($row['item_quantity']) ?>" required oninput="calculateRowTotal(this)" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Qty">

                                        <input aria-label="Cost[]" min='0' style="width:5.5rem;" type="number" class="form-control text-right mt-2" name="cost[]" value="<?= htmlspecialchars($row['item_price']) ?>" oninput="calculateRowTotal(this)" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Price">
                                    </td>
                                    <td style="width: 10%;">
                                        <input aria-label="Total Cost[]" style="width:5.5rem;" type="number" name="total_cost[]" class="form-control total-cost text-right" value="<?php echo htmlspecialchars($row['total_amount']); ?>" readonly>
                                    </td>
                                    <td style="width: 20%;">
                                        <input aria-label="Planned Request By[]" type="text" class="form-control datepicker" name="planned_request_by[]" value="<?= $row['planned_request_by'] ? fromDate($row['planned_request_by']) : '';  ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Planned Request Date">
                                        <input aria-label="Actual Request By[]" type="text" class="form-control datepicker mt-2" name="actual_request_by[]" value="<?= $row['actual_request_by'] ? fromDate($row['actual_request_by']) : ''; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Actual Request Date">
                                    </td>
                                    <td style="width: 20%;">
                                        <input aria-label="Planned Return By[]" type="text" class="form-control datepicker" name="planned_return_by[]" value="<?= $row['planned_return_by'] ? fromDate($row['planned_return_by']) : ''; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Planned return Date">
                                        <input aria-label="Actual Return By[]" type="text" class="form-control datepicker mt-2" name="actual_return_by[]" value="<?= $row['actual_return_by'] ? fromDate($row['actual_return_by']) : ''; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Actual return Date">
                                    </td>
                                    <td style="width: 10%;">
                                        <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeBomRowBtn(this)"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td class="w-full" style="width: 30%;">


                                    <select aria-label="Item Name[]" name="item_name[]" class="form-select js-example-basic-single form-control item-select" required onchange="onBOMItemChange(this)">
                                        <option value="">Select Item</option>
                                        <?php foreach ($items_res as $id => $item) { ?>
                                            <option value="<?php echo $id; ?>" data-uom="<?php echo $item['purchase_uom']; ?>"><?php echo htmlspecialchars($item['item_name']); ?></option>
                                        <?php } ?>
                                        <option value="others">Others</option>
                                    </select>
                                    <input type="hidden" name="bom_row_id[]" value="" />

                                    <input aria-label="Enter Item Name" type="text" name="new_item_name[]" class="form-control new-item-name mt-3" style="display: none;" placeholder="Enter Item Name">
                                    <div class="mt-2 w-full" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="UOM">
                                        <select aria-label="Unit[]" style="width:5.5rem;" name="unit[]" class="form-select js-example-basic-single form-control unit-select" required>
                                            <option value="">Select UOM</option>
                                            <?php foreach ($uom_res as $id => $item) { ?>
                                                <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($item['uom_short_code']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </td>

                                <td style="width: 10%;">
                                    <input aria-label="Quantity[]" id="quantity_0" min='0' type="number" class="form-control text-right" name="quantity[]" required oninput="calculateRowTotal(this)" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Qty" />
                                    <input aria-label="Cost[]" id="cost_0" min='0' style="width:5.5rem;" type="number" class="form-control text-right mt-2" name="cost[]" oninput="calculateRowTotal(this)" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Price" />
                                </td>
                                <td style="width: 10%;">
                                    <input aria-label="Total Cost[]" style="width:5.5rem;" type="number" name="total_cost[]" class="form-control total-cost text-right" readonly>
                                </td>

                                <td style="width: 20%;">
                                    <input aria-label="Planned Request By[]" id="planned_request_by" type="text" class="form-control datepicker" name="planned_request_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Planned Request Date">
                                    <input aria-label="Actual Request By[]" id="actual_request_by" type="text" class="form-control datepicker" name="actual_request_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Actual Request Date">

                                </td>
                                <td style="width: 20%;">
                                    <input aria-label="Planned Return By[]" id="planned_return_by" type="text" class="form-control datepicker mt-2" name="planned_return_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Planned return Date">

                                    <input aria-label="Actual Return By[]" id="actual_return_by" type="text" class="form-control datepicker mt-2" name="actual_return_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Actual return Date">
                                </td>
                                <td style="width: 10%;">
                                    <button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeBomRowBtn(this)"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>

                        <tr>
                            <th scope="col">
                                <button class="btn btn-primary" type="button" onclick="addBomRowBtn(this)"><i class="fa fa-plus"></i> Add More</button>
                            </th>
                            <th scope="col" colspan="1" class="text-end"><b>Total Amount</b></th>
                            <td class="text-right">
                                <input aria-label="Final Total Cost" type="number" id="finalTotalCost_2" class="form-control text-right" value="<?php echo $final_total_cost; ?>" readonly />
                            </td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                </table>
                <input type="hidden" name="feasibility_id" value="<?= htmlspecialchars($feasibility_id); ?>">
                <div style="pointer-events: auto; opacity: 1;" class="row">
                    <div class="col-lg-6 mt-3">
                        <button class="btn btn-secondary prev-btn" type="button" onclick="previous('custom_insert_lead_feasibility_evaluations',  <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">Previous</button>
                    </div>
                    <div class="col-lg-6 text-end mt-3">
                        <?php if ($feasibility_requirement_res['current_status'] == ST_APPROVED) { ?>
                            <button class="btn btn-primary next-btn" type="button" id="saveNextBtn" onclick="withOutSaving('custom_insert_lead_feasibility_checklists', <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">Save & Next</button>
                        <?php } else { ?>
                            <button class="btn btn-primary next-btn" type="button" onclick="saveAndNextBOM('custom_insert_lead_feasibility_checklists', <?php echo $feasibility_id; ?>, <?php echo $_GET['lead_id']; ?>)">Save & Next</button>
                        <?php } ?>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
    var uom_res = <?php echo json_encode($uom_res); ?>;
    var items_res = <?php echo json_encode($items_res); ?>;

    function saveAndNextBOM(nextPage, feasibility_id, lead_id) {
        const form = document.getElementById('bomForm');
        const rows = form.querySelectorAll('#bomTableBody tr');
        const formData = new FormData(form);

        for (let i = 0; i < rows.length; i++) {
            var error = '';
            const itemInput = rows[i].querySelector('[name="item_name[]"]');
            if (!itemInput) continue; // skip rows without item_name input
            const item = itemInput.value.trim();
            const qtyInput = rows[i].querySelector('[name="quantity[]"]');
            if (!qtyInput) continue; // skip rows without item_name input
            const qty = qtyInput.value.trim();
            if (!item) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Item Name is required.`, '');
                return;
            }
            if (item === 'others') {
                const newItemInput = rows[i].querySelector('[name="new_item_name[]"]');
                if (!newItemInput || !newItemInput.value.trim()) {
                    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: New Item Name is required when "Others" is selected.`, '');
                    return;
                }
            }
            if (!qty || isNaN(qty) || qty <= 0) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Quantity must be a positive number.`, '');
                return;
            }
            const plannedFrom = rows[i].querySelector('[name="planned_request_by[]"]').value;
            if (!plannedFrom) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Planned Required By Date is required.`, '');
                return;
            }
            const plannedTo = rows[i].querySelector('[name="planned_return_by[]"]').value;
            if (!plannedTo) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Planned Returned By Date is required.`, '');
                return;
            }
            if (plannedFrom && plannedTo && new Date(plannedTo) < new Date(plannedFrom)) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Planned Returned By cannot be before Planned Required By.`, '');
                return;
            }

            const actualFrom = rows[i].querySelector('[name="actual_request_by[]"]').value;
            const actualTo = rows[i].querySelector('[name="actual_return_by[]"]').value;
            if (actualFrom && actualTo && new Date(actualTo) < new Date(actualFrom)) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', `Row ${i + 1}: Actual Returned By cannot be before Actual Required By.`, '');
                return;
            }
        }
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);
        formData.append('page', 'insertFeasibilityBomAndChecklist');
        formData.append('table_name', 'lead_feasibility_boms');
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Response data:", response);
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
                            handleAjaxError(xhr, status, err, "Error loading next page.");

                        }
                    });
                } catch (e) {
                    console.error('Invalid JSON response:', response);
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

    function addBomRowBtn(btn) {
        console.log('test:', btn);
        // Add BOM Row
        const tbody = document.getElementById('bomTableBody');
        const newRow = document.createElement('tr');
        let item_options = '<option value="">Select Item</option>';
        for (const id in items_res) {
            item_options += `<option value="${id}" data-uom="${items_res[id].purchase_uom}">${items_res[id].item_name}</option>`;
        }
        item_options += '<option value="others">Others</option>';
        let options = '<option value="">Select UOM</option>';
        for (const id in uom_res) {
            options += `<option value="${id}">${uom_res[id].uom_short_code}</option>`;
        }
        newRow.innerHTML = `
            <td class="w-full" style="width: 30%;">
            <select aria-label="Item Name[]" name="item_name[]" class="form-select js-example-basic-single form-control  item-select" onchange="onBOMItemChange(this)">
                ${item_options}
            </select>
            <input type="hidden" name="bom_row_id[]"  value="" />
            <input aria-label="Enter Item Name" type="text" name="new_item_name[]" class="form-control new-item-name mt-3" style="display: none;" placeholder="Enter Item Name">
            <div class="mt-2 w-full" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="UOM">
            <select aria-label="Unit[]" style="width:5.5rem;" name="unit[]" class="form-select js-example-basic-single form-control unit-select" >
                ${options}
            </select>
            </div>
            </td>
            <td style="width: 10%;"><input aria-label="Quantity[]" min='0' type="number" class="form-control text-right" name="quantity[]" required oninput="calculateRowTotal(this)" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Qty"/><input aria-label="Cost[]" min='0' style="width:5.5rem;" type="number" class="form-control text-right mt-2" name="cost[]" oninput="calculateRowTotal(this)" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Price"/></td>
            <td style="width: 10%;"><input aria-label="Total Cost[]" style="width:5.5rem;" type="number" name="total_cost[]" class="form-control total-cost text-right"   readonly></td>
            <td style="width: 20%;"><input aria-label="Planned Request By[]" id="planned_request_by_dyn" type="text" class="form-control datepicker" name="planned_request_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Planned Request Date"><input aria-label="Actual Request By[]" id="actual_request_by_dyn" type="text" class="form-control datepicker mt-2" name="actual_request_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Actual Request Date"></td>
            <td style="width: 20%;"><input aria-label="Planned Return By[]" id="planned_return_by_dyn" type="text" class="form-control datepicker" name="planned_return_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Planned return Date"><input aria-label="Actual Return By[]" id="actual_return_by_dyn" type="text" class="form-control datepicker mt-2" name="actual_return_by[]" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Actual return Date"></td>
            <td style="width: 10%;"><button type="button" class="btn btn-danger btn-sm remove-row" onclick="removeBomRowBtn(this)"><i class="fa fa-trash"></i></button></td>
        `;
        tbody.appendChild(newRow);
        $(".datepicker").datepicker({
            showOn: "both",
            buttonText: "<i class='ti-calendar'></i>",
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true
        });
        $('[data-bs-toggle="tooltip"]').tooltip();
        document.querySelectorAll('.js-example-basic-single').forEach(function(el) {
            if ($(el).data('select2')) {
                $(el).select2('destroy');
            }
            $(el).select2();
        });

    }

    function removeBomRowBtn(btn) {
        // Remove BOM Row
        console.log('test:', btn);

        const row = btn.closest('tr');
        row.remove();
    }

    function calculateRowTotal(input) {
        const row = input.closest('tr');
        const qty = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
        const cost = parseFloat(row.querySelector('[name="cost[]"]').value) || 0;
        const total = qty * cost;
        row.querySelector('[name="total_cost[]"]').value = total.toFixed(2);

        calculateFinalTotal();
    }

    function calculateFinalTotal() {
        let finalTotal = 0;
        document.querySelectorAll('[name="total_cost[]"]').forEach(input => {
            const val = parseFloat(input.value) || 0;
            finalTotal += val;
        });
        document.getElementById('finalTotalCost').value = finalTotal.toFixed(2);
    }

    function onBOMItemChange(selectElement) {
        const selectedItem = selectElement.value;
        const row = $(selectElement).closest('tr');
        const unitSelect = row.find('[name="unit[]"]');
        const newItemInput = row.find('.new-item-name');

        if (selectedItem === 'others') {
            newItemInput.show();
        } else {
            newItemInput.hide();
            const uom = $(selectElement).find('option:selected').data('uom');
            unitSelect.val(uom); // Set the corresponding UOM
            unitSelect.trigger('change');
        }
    }

    function withOutSaving(page, feasibility_id, lead_id) {
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#feasibilitySlides").html(loader);

        $.ajax({
            url: `views/${page}.php`,
            type: 'GET',
            data: {
                'feasibility_id': feasibility_id,
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
                });
                reinitSelect2(); // Reinitialize select2 after loading new content
            },
            error: function(xhr, status, error) {
                alert('Error loading next page: ' + error);
            }
        });
    }
</script>
