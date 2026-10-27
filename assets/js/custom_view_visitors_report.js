// custom_view_visitors_report.js - Modernized Report Actions

window.addEventListener('load', function () {
    $(document).on('click', '.js-visitors-report', function () {
        const visitType = $('#report_visit_type').val();
        const reportDate = $('#report_date').val();
        const targetBox  = $(this).data('target');

        if (!reportDate) {
            if (typeof tostarnotify === 'function') {
                tostarnotify(
                    'top',
                    'right',
                    'fa fa-thumbs-down',
                    'form-label label label-danger',
                    'animated flipInX',
                    'animated flipOutX',
                    'Please select a date.',
                    ''
                );
            } else {
                alert('Please select a date.');
            }
            return;
        }

        const loader = `
            <div class="loader-center">
                <img src="assets/images/spiner/spiner3.gif" class="loader-lg" style="width: 120px;">
                <p class="text-muted mt-3 font-weight-bold">Generating Report Data...</p>
            </div>`;

        $(targetBox).html(loader);

        $.ajax({
            url: 'views/custom_view_visitors_report_table.php',
            type: 'GET',
            data: {
                date: reportDate,
                visit_type: visitType,
                page: 'AJAX_CALL'
            },
            success: function (response) {
                $(targetBox).html(response);
            },
            error: function () {
                alert('Error fetching visitor data.');
            }
        });
    });

    // Click on Details to open Modal
    $(document).on('click', '.js-view-visitor-details', function() {
        var logId = $(this).data('id');
        if (!logId) return;

        $('#visitorDetailsBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-3 font-weight-bold">Fetching visitor details...</p>
            </div>
        `);
        $('#visitorDetailsModal').modal('show');

        $.ajax({
            url: 'views/custom_view_visitor_details_ajax.php',
            type: 'GET',
            data: { id: logId },
            success: function(response) {
                $('#visitorDetailsBody').html(response);
            },
            error: function() {
                $('#visitorDetailsBody').html('<div class="alert alert-danger font-weight-bold"><i class="fa fa-exclamation-triangle"></i> Failed to fetch visitor details. Please try again.</div>');
            }
        });
    });
});

