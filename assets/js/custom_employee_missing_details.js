$(document).ready(function () {
    function createUserWithModuleList(empId, $btn) {
        swal({
            title: 'Create User?',
            text: 'Do you want to create user mapping for this employee?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            closeOnConfirm: false
        }, function () {
            $btn.prop('disabled', true);

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    page: 'employee_missing_details',
                    action: 'create_user_with_module_list',
                    emp_id: empId
                }
            }).done(function (res) {
                if (res && (res.status === 'success' || res.status === 'info')) {
                    swal('Success', res.message || 'User mapping processed.', 'success');
                    window.location.reload();
                    return;
                }
                swal('Error', (res && res.message) ? res.message : 'Failed to create user mapping.', 'error');
                $btn.prop('disabled', false);
            }).fail(function () {
                swal('Error', 'Unable to process request. Please try again.', 'error');
                $btn.prop('disabled', false);
            });
        });
    }

    function createPayAreaEmployee(empId, $btn) {
        swal({
            title: 'Create Pay Area?',
            text: 'Do you want to create pay area mapping for this employee?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            closeOnConfirm: false
        }, function () {
            $btn.prop('disabled', true);

            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    page: 'employee_missing_details',
                    action: 'create_pay_area_employee',
                    emp_id: empId
                }
            }).done(function (res) {
                if (res && (res.status === 'success' || res.status === 'info')) {
                    swal('Success', res.message || 'Pay area mapping processed.', 'success');
                    window.location.reload();
                    return;
                }
                swal('Error', (res && res.message) ? res.message : 'Failed to create pay area mapping.', 'error');
                $btn.prop('disabled', false);
            }).fail(function () {
                swal('Error', 'Unable to process request. Please try again.', 'error');
                $btn.prop('disabled', false);
            });
        });
    }

    $(document).on('click', '.js-missing-action', function () {
        var $btn = $(this);
        var missingKey = String($btn.data('missing-key') || '').toLowerCase();
        var empId = String($btn.data('empid') || '');

        if (empId === '') {
            return false;
        }

        if (missingKey === 'email') {
            return false;
        }

        if (missingKey === 'user') {
            createUserWithModuleList(empId, $btn);
            return false;
        }

        if (missingKey === 'pay_area') {
            createPayAreaEmployee(empId, $btn);
            return false;
        }

        return false;
    });
});
