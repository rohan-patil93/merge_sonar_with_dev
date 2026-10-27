(function () {
    function validateAttendanceForm() {
        var valid = true;
        var employeeId = $('#employee_id').val();
        var month = $('#report_month').val();

        if (employeeId == 0) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                'animated flipInX', 'animated flipOutX', 'Please select employee', '');
            valid = false;
        }

        if (!month) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                'animated flipInX', 'animated flipOutX', 'Please select month', '');
            valid = false;
        }

        return valid;
    }

    $(document).on('submit', '#attendanceReportForm', function (e) {
        if (!validateAttendanceForm()) {
            e.preventDefault();
        }
    });

    window.reloadAttendanceTable = function () {
        var form = document.getElementById('attendanceReportForm');
        if (!form) {
            return;
        }

        if (!validateAttendanceForm()) {
            return;
        }

        form.submit();
    };
})();
