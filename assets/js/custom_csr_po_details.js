
// This will be appended dynamically
$(document).ready(function () {
    const cfg = document.getElementById('csr_config')?.dataset || {};

    let rowIndex = parseInt(cfg.poRowCount || 1, 10);

    // Add new PO row
    $('#addRowBtn').click(function () {
        addRow();
    });

    $('#poExcelUploadBtn').click(function () {
        openPoExcelUpload();
    });

    function openPoExcelUpload() {
        const trackingId = $('input[name="tracking_id"]').val() || '';
        const projectId = $('input[name="project_id"]').val() || '';

        if (!projectId || !trackingId) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                'animated flipInX', 'animated flipOutX', 'Project or tracking details are missing.', '');
            return;
        }

        const loader = `<div class="text-center" id="loader-show">
                <img src="assets/images/spiner/spiner3.gif" class="csr-loader">
            </div>`;

        $('#TableContent').html(loader);

        $.ajax({
                url: 'views/custom_csr_po_excel_upload.php',
            type: 'POST',
            data: {
                project_id: projectId,
                tracking_id: trackingId,
                traking_id: trackingId,
                doc_type: 'PO'
            },
            success: function (response) {
                $('#TableContent').html(response);
            },
            error: function () {
                $('#TableContent').html('<div class="text-danger text-center">Failed to load PO upload page.</div>');
            }
        });
    }

    function addRow() {
        rowIndex++;
        $('#poRowsWrapper').append(getRowHtml(rowIndex));
    }

    // Remove row
    $(document).on('click', '.removeRowBtn', function () {
        const row = $(this).closest('.po-row');             // get the row container
        const po_id = row.find("input[name='id[]']").val();   // get ID from the row

        // Remove row from DOM
        row.remove();
        // --- If existing record, soft delete via AJAX ---
        if (po_id && po_id !== "") {
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: {
                    page: "softdeletedrecord",
                    id: po_id,
                    table: "csr_po_details"
                },
                success: function (data) {
                    console.log("Soft delete response:", data);
                },
                error: function () {
                    console.error("Soft delete failed");
                }
            });
        }

    });


    // Toggle show/hide PO No
    $(document).on('change', '.po_toggle', function () {
        let row = $(this).closest('.po-row');
        let value = $(this).val();
        if (value == '1') { // No
            row.find('.po_no_div').addClass('d-none');
            row.find('.po_no').val('NON PO');
        } else {
            row.find('.po_no_div').removeClass('d-none');
            row.find('.po_no').val('');
        }
    });

    // Auto calculate PO Value Including Tax per row
    $(document).on('input', '.po_value_ex, .gst', function () {
        let row = $(this).closest('.po-row');
        let exVal = parseFloat(row.find('.po_value_ex').val()) || 0;
        let gstVal = parseFloat(row.find('.gst').val()) || 0;
        let total = exVal + (exVal * gstVal / 100);
        row.find('.po_value_in').val(total.toFixed(2));
    });

    // Validation on submit
    $('#poForm').on('submit', function (e) {
        let isValid = true;

        $('.po-row').each(function (i, rowEl) {
            let row = $(rowEl);
            let poToggle = row.find('.po_toggle:checked').val();
            let financialYear = row.find('.financial_year').val().trim();
            let poNo = row.find('.po_no').val().trim();
            let poDate = row.find('.po_date').val().trim();
            let poAmount = row.find('.po_amount').val().trim();
            let poValueEx = row.find('.po_value_ex').val().trim();

            if (financialYear === '') {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', `Please select Financial Year (Row ${i + 1})`, '');
                isValid = false;
            }

            if (poToggle === '0' && poNo === '') {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', `Please enter PO No. (Row ${i + 1})`, '');
                isValid = false;
            }

            if (poDate === '') {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', `Please select PO Date (Row ${i + 1})`, '');
                isValid = false;
            }

            if (poAmount === '') {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', `Please enter PO Amount (Row ${i + 1})`, '');
                isValid = false;
            }

            if (poValueEx === '') {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', `Please enter PO Value (Excl. Tax) (Row ${i + 1})`, '');
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            return;
        }
        // ✅ Use FormData to collect everything
        const formData = new FormData(this);
        formData.append('page', 'save_po_details');
        formData.append('table_name', 'csr_po_details');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,  // ⚠️ Important for FormData
            contentType: false,  // ⚠️ Important for FormData
            success: function (response) {
                const data = JSON.parse(response);
                if (data) {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success',
                        'animated flipInX', 'animated flipOutX', 'PO  Details saved successfully.', '');
                    // $('#kt_modal_dynamic_popup').modal('hide');
                } else {
                    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                        'animated flipInX', 'animated flipOutX', 'Failed to save: ' + data.message, '');
                }
            },
            error: function () {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', 'An error occurred. Please try again.', '');
            }
        });
    });

    function getRowHtml(index) {
        const currentYear = new Date().getFullYear();
        const startYear = 2019;
        let fyOptions = '<option value="">Select FY</option>';
        for (let year = startYear; year <= currentYear; year++) {
            const nextYear = year + 1;
            fyOptions += `<option value="${year}-${nextYear}">${year}-${nextYear}</option>`;
        }

        return `
            <div class="row po-row border-bottom mb-3 pb-2" data-row="${index}">
                <div class="col-lg-2 form-group">
                    <label>PO Available? <span class="text-danger">*</span></label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input po_toggle" type="radio" name="po_toggle_${index}" value="0" checked>
                        <label class="form-check-label">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input po_toggle" type="radio" name="po_toggle_${index}" value="1">
                        <label class="form-check-label">No</label>
                    </div>
                </div>

                <div class="col-lg-2 form-group">
                    <label>Financial Year <span class="text-danger">*</span></label>
                    <select name="financial_year[]" class="form-control form-select financial_year" required>
                        ${fyOptions}
                    </select>
                </div>

                <div class="col-lg-2 form-group po_no_div">
                    <label>PO No. <span class="text-danger">*</span></label>
                    <input class="form-control po_no" type="text" name="po_no[]">
                </div>

                <div class="col-lg-2 form-group">
                    <label>PO Date <span class="text-danger">*</span></label>
                    <input class="form-control po_date" type="date" name="po_date[]">
                </div>

                <div class="col-lg-2 form-group">
                    <label>PO Amount <span class="text-danger">*</span></label>
                    <input class="form-control po_amount" type="number" step="0.01" name="po_amount[]">
                </div>

                <div class="col-lg-2 form-group">
                    <label>PO Value (Excl. Tax) <span class="text-danger">*</span></label>
                    <input class="form-control po_value_ex" type="number" step="0.01" name="po_value_ex[]">
                </div>

                <div class="col-lg-1 form-group">
                    <label>GST %</label>
                    <input class="form-control gst" type="number" step="0.01" name="gst[]">
                </div>

                <div class="col-lg-2 form-group">
                    <label>PO Value (Incl. Tax)</label>
                    <input class="form-control po_value_in" type="number" step="0.01" name="po_value_in[]" >
                </div>

                <div class="col-lg-2 form-group">
                    <label>Business Code</label>
                    <input class="form-control business_code" type="text" name="business_code[]">
                </div>

                <div class="col-lg-2 form-group">
                    <label>Business Name</label>
                    <input class="form-control business_name" type="text" name="business_name[]">
                </div>

                <div class="col-lg-2 form-group">
                    <label>Other PO  Details</label>
                    <textarea class="form-control po_details" name="po_details[]" rows="1"></textarea>
                </div>

                <div class="col-lg-2 form-group">
                    <label>Remarks</label>
                    <textarea class="form-control remarks" name="remarks[]" rows="1"></textarea>
                </div>

                <div class="col-lg-1 d-flex align-items-center">
                    <button type="button" class="btn btn-danger btn-sm removeRowBtn">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>`;
    }
});
