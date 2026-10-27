// custom_view_visitors_report.js

$(document).on('click', '.js-visitors-report', function () {

    const inDateTime  = $('#in_datetime').val();
    const outDateTime = $('#out_datetime').val();
    const visitType = $('#visit_type').val();
    const targetBox  = $(this).data('target');

    if (!inDateTime || !outDateTime) {
        tostarnotify(
            'top',
            'right',
            'fa fa-thumbs-down',
            'form-label label label-danger',
            'animated flipInX',
            'animated flipOutX',
            'Please select both In and Out Date Time.',
            ''
        );
        return;
    }

    if (new Date(outDateTime) < new Date(inDateTime)) {
        tostarnotify(
            'top',
            'right',
            'fa fa-thumbs-down',
            'form-label label label-danger',
            'animated flipInX',
            'animated flipOutX',
            'Out Date Time must be after In Date Time.',
            ''
        );
        return;
    }

    const loader =
        '<div class="loader-center">' +
            '<img src="assets/images/spiner/spiner3.gif" class="loader-lg">' +
        '</div>';

    $(targetBox).html(loader);

    $.ajax({
        url: 'views/custom_view_visitors_report_table.php',
        type: 'GET',
        data: {
            in_datetime: inDateTime,
            out_datetime: outDateTime,
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
