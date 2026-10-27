(function () {
    var form = document.querySelector('.employee-report-form');
    var validationBox = document.getElementById('employee-report-validation');

    function hideValidation() {
        if (!validationBox) {
            return;
        }
        validationBox.classList.add('d-none');
        validationBox.textContent = '';
    }

    function showValidation(message) {
        if (!validationBox) {
            return;
        }
        validationBox.textContent = message;
        validationBox.classList.remove('d-none');
    }

    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.js-employee-report-select').select2({
            width: '100%'
        });
    }

    function validateDateRanges() {
        hideValidation();

        if (!form) {
            return true;
        }

        var joiningFrom = form.querySelector('input[name="joining_date_from"]');
        var joiningTo = form.querySelector('input[name="joining_date_to"]');
        var relievingFrom = form.querySelector('input[name="relieving_date_from"]');
        var relievingTo = form.querySelector('input[name="relieving_date_to"]');

        var joiningFromValue = joiningFrom ? joiningFrom.value : '';
        var joiningToValue = joiningTo ? joiningTo.value : '';
        if (joiningFromValue !== '' && joiningToValue !== '' && joiningFromValue > joiningToValue) {
            showValidation('Join Date From should be less than or equal to Join Date To.');
            return false;
        }

        var relievingFromValue = relievingFrom ? relievingFrom.value : '';
        var relievingToValue = relievingTo ? relievingTo.value : '';
        if (relievingFromValue !== '' && relievingToValue !== '' && relievingFromValue > relievingToValue) {
            showValidation('Relieving Date From should be less than or equal to Relieving Date To.');
            return false;
        }

        return true;
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            if (!validateDateRanges()) {
                event.preventDefault();
            }
        });
    }

    document.addEventListener('click', function (event) {
        var searchBtn = event.target.closest('.employee-report-form button[type="submit"]');
        if (searchBtn) {
            if (!validateDateRanges()) {
                event.preventDefault();
            }
            return;
        }

        var resetBtn = event.target.closest('[data-action="reset-employee-report"]');
        if (!resetBtn) {
            return;
        }

        event.preventDefault();

        var form = document.querySelector('.employee-report-form');
        if (!form) {
            return;
        }

        var selects = form.querySelectorAll('select');
        selects.forEach(function (select) {
            select.value = '';
            if (window.jQuery && jQuery.fn.select2 && select.classList.contains('js-employee-report-select')) {
                jQuery(select).val('').trigger('change');
            }
        });

        var inputs = form.querySelectorAll('input:not([type="hidden"])');
        inputs.forEach(function (input) {
            input.value = '';
        });

        hideValidation();
        form.submit();
    });
})();
