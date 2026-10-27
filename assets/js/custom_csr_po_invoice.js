$(document).ready(function () {
    $('#project_id').change(function () {
        const projectId = $(this).val();
        $('#tracking_id').html('<option value="0">Select Tracking</option>');
        if (projectId == 0) {
            return;
        }
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'getselectedlist',
                table_name: 'csr_project_tracking',
                field: 'proposal_id',
                id: projectId
            },
            success: function (res) {
                let result = JSON.parse(res);
                let options = '<option value="0">Select Tracking</option>';

                if (result && result.length > 0) {
                    $.each(result, function (i, row) {
                        options += `<option value="${row.id}">${row.tracking_name}</option>`;
                    });
                }

                $('#tracking_id').html(options).trigger('change');
            }
        });
    });

    $('#searchBtn').click(function () {
        openCsrPoInvoiceResult();
    });
});

function openCsrPoInvoiceResult(options = {}) {
	let valid = true;
	const project_id = options.project_id || $('#project_id').val();
	const tracking_id = options.tracking_id || $('#tracking_id').val();
	const doc_type = options.doc_type || $('input[name="doc_type"]:checked').val();
	// Project validation
	if (project_id == 0 || project_id === '') {
		tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
			'animated flipInX', 'animated flipOutX', 'Please select a project', '');
		valid = false;
	}
	// Tracking validation
	if (tracking_id == 0 || tracking_id === '') {
		tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
			'animated flipInX', 'animated flipOutX', 'Please select a tracking', '');
		valid = false;
	}
	// Radio (PO / Invoice) validation
	if (!doc_type) {
		tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
			'animated flipInX', 'animated flipOutX', 'Please Choose PO or Invoice', '');
		valid = false;
	}
	if (!valid) return;
	var loader = `<div class="text-center" id="loader-show">
	        <img src="assets/images/spiner/spiner3.gif" class="csr-loader">
	    </div>`;
	$('#TableContent').html(loader);
	$.ajax({
        url: "views/custom_csr_tracking_po_invoice_popup.php",
        type: "POST",
        data: {
            project_id: project_id,
            traking_id: tracking_id,
            doc_type: doc_type
        },
        success: function (data) {
            $('#TableContent').html(data);
            $('.js-example-basic-single').select2();
        },
        error: function () {
            $('#TableContent').html('<div class="text-danger text-center">Something went wrong</div>');
        }
    });
}
