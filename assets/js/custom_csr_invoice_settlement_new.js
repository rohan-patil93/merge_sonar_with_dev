$(document).ready(function () {
    const csrDataEl = document.getElementById('csrData');

    let invRowIndex = parseInt(
        csrDataEl?.dataset?.inv_row_count || 1,
        10
    );
    const csr_pos = csrDataEl?.dataset?.csr_pos
        ? JSON.parse(csrDataEl.dataset.csr_pos)
        : [];

    const fyRows = csrDataEl?.dataset?.fy_rows
        ? JSON.parse(csrDataEl.dataset.fy_rows)
        : [];


    /* Add row */
    $('#addInvRowBtn').on('click', function () {
        invRowIndex++;
        $('#invoiceRowsWrapper').append(getInvRowHtml(invRowIndex));
        $('.js-example-basic-single').select2();
    });

    $('#invoiceExcelUploadBtn').on('click', function () {
        openInvoiceExcelUpload();
    });

    /* Remove row + soft delete */
    $(document).on('click', '.removeInvRowBtn', function () {
        const row = $(this).closest('.inv-row');
        const invoice_id = row.find("input[name='id[]']").val();

        row.remove();

        if (invoice_id) {
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: {
                    page: "softdeletedrecord",
                    id: invoice_id,
                    table: "csr_invoice_details"
                }
            });
        }
    });

    /* Auto calculations */
    $(document).on('input', '.taxable_value, .gst_percent, .gst_amount', function () {
        const row = $(this).closest('.inv-row');
        const taxable = parseFloat(row.find('.taxable_value').val()) || 0;
        const gstPercentVal = row.find('.gst_percent').val();
        const gstPercent = parseFloat(gstPercentVal);
        const tds = parseFloat(row.find('.tds_deducted').val()) || 0;

        if ($(this).hasClass('taxable_value') || $(this).hasClass('gst_percent')) {
            if (gstPercentVal !== '' && !isNaN(gstPercent)) {
                row.find('.gst_amount').val(((taxable * gstPercent) / 100).toFixed(2));
            }
        }

        const gst = parseFloat(row.find('.gst_amount').val()) || 0;
        const total = taxable + gst;
        row.find('.total_spent').val(total.toFixed(2));
        row.find('.net_payable').val((total - tds).toFixed(2));
    });





    $(document).on('input', '.total_spent, .tds_deducted', function () {
        const row = $(this).closest('.inv-row');
        const total = parseFloat(row.find('.total_spent').val()) || 0;
        const tds = parseFloat(row.find('.tds_deducted').val()) || 0;
        row.find('.net_payable').val((total - tds).toFixed(2));
    });

    /* Submit */
    $('#invoiceForm').on('submit', function (e) {
        e.preventDefault();
        let valid = true;

        $('.inv-row').each(function (i, el) {
            const row = $(el);
            const required = [
                ['.financial_year','Financial Year'],
                ['.inv_no','Invoice No'],
                ['.inv_date','Invoice Date'],
                ['.paid_date','Paid Date'],
                ['.paid_amount','Paid Amount'],
                ['.paid_bank','Paid Bank'],
                ['.taxable_value','Taxable Value'],
                ['.gst_percent','GST %'],
                ['.gst_amount','GST Amount'],
                ['.total_spent','Total Spent'],
                ['.net_payable','Net Payable']
            ];

            required.forEach(r => {
                if (row.find(r[0]).val().trim() === '') {
                    notifyError(`${r[1]} missing (Row ${i+1})`);
                    valid = false;
                }
            });
        });

        if (!valid) return;

        const formData = new FormData(this);
        formData.append('page', 'save_invoice_details');
        formData.append('table_name', 'csr_invoice_details');

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                tostarnotify(
                    'top','right','fa fa-thumbs-up',
                    'form-label label label-success',
                    'animated flipInX','animated flipOutX',
                    'Invoice details saved successfully',''
                );
                setTimeout(function () {
                    if (typeof openCsrPoInvoiceResult === 'function') {
                        openCsrPoInvoiceResult({
                            project_id: $('input[name="project_id"]').val() || '',
                            tracking_id: $('input[name="tracking_id"]').val() || '',
                            doc_type: 'INVOICE'
                        });
                    }
                }, 1500);
            },
            error: function () {
                notifyError('An error occurred. Please try again.');
            }
        });
    });

    function openInvoiceExcelUpload() {
        const trackingId = $('input[name="tracking_id"]').val() || '';
        const projectId = $('input[name="project_id"]').val() || '';

        if (!projectId || !trackingId) {
            notifyError('Project or tracking details are missing.');
            return;
        }

        const loader = `<div class="text-center" id="loader-show">
                <img src="assets/images/spiner/spiner3.gif" class="csr-loader">
            </div>`;

        $('#TableContent').html(loader);

        $.ajax({
            url: 'views/custom_csr_invoice_excel_upload.php',
            type: 'POST',
            data: {
                project_id: projectId,
                tracking_id: trackingId,
                traking_id: trackingId,
                doc_type: 'INVOICE'
            },
            success: function (response) {
                $('#TableContent').html(response);
            },
            error: function () {
                $('#TableContent').html('<div class="text-danger text-center">Failed to load invoice upload page.</div>');
            }
        });
    }

    function getInvRowHtml(index) {
        let poOptions = '<option value="">Select Any One</option>';
        csr_pos.forEach(po => {
            poOptions += `<option value="${po.id}">${po.po_no}</option>`;
        });

        let fyOptions = '<option value="">Select FY</option>';
        fyRows.forEach(fy => {
            fyOptions += `<option value="${fy.genc_detail_code}">${fy.genc_detail_code}</option>`;
        });

        return `
        <div class="row inv-row border-bottom mb-3 pb-2" data-row="${index}">
            <div class="col-lg-2 form-group">
                <label>PO No</label>
                <select class="form-control js-example-basic-single" name="csr_po_details_id[]">
                    <option value="">Select Any One</option>
                    ${poOptions}
                </select>
            </div>

            <div class="col-lg-2 form-group">
                <label>Financial Year <span class="text-danger">*</span></label>
                <select name="financial_year[]" class="form-control form-select financial_year" required>
                    ${fyOptions}
                </select>
            </div>

            <div class="col-lg-2 form-group">
                <label>Invoice No <span class="text-danger">*</span></label>
                <input class="form-control inv_no" name="inv_no[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>Invoice Date <span class="text-danger">*</span></label>
                <input class="form-control inv_date" type="date" name="inv_date[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>Taxable Value <span class="text-danger">*</span></label>
                <input class="form-control taxable_value" type="number" step="any" name="taxable_value[]">
            </div>
            <div class="col-lg-2 form-group">
                <label>GST % <span class="text-danger">*</span></label>
                <input class="form-control gst_percent text-end" min="0" type="number" step="any" name="gst_percent[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>GST Amount <span class="text-danger">*</span></label>
                <input class="form-control gst_amount" type="number" step="any" name="gst_amount[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>Total Spent <span class="text-danger">*</span></label>
                <input class="form-control total_spent" type="number" step="any" name="total_spent[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>TDS Deducted</label>
                <input class="form-control tds_deducted" type="number" step="any" name="tds_deducted[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>Net Payable <span class="text-danger">*</span></label>
                <input class="form-control net_payable" type="number" step="any" name="net_payable[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>Paid Date <span class="text-danger">*</span></label>
                <input class="form-control paid_date" type="date" name="paid_date[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>Paid Amount <span class="text-danger">*</span></label>
                <input class="form-control paid_amount" type="number" step="any" name="paid_amount[]">
            </div>

            <div class="col-lg-2 form-group">
                <label>Paid Bank <span class="text-danger">*</span></label>
                <input class="form-control paid_bank" name="paid_bank[]">
            </div>

            <div class="col-lg-1">
                <button type="button" class="btn btn-danger btn-sm removeInvRowBtn">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>`;
    }

});

/* Helpers */
function notifyError(msg) {
    tostarnotify(
        'top','right','fa fa-thumbs-down',
        'form-label label label-danger',
        'animated flipInX','animated flipOutX',
        msg,''
    );
}


