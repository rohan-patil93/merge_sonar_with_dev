function showSimpleAlert(message, type) {
    swal('Notice', message, type || 'info');
}

function showAlertThenSubmitSearchForm(message, type) {
    var searchButton = document.querySelector('#issueRawMaterialsForm button[name="search_issue_materials"]');

    function triggerSearchButton() {
        if (searchButton) {
            searchButton.click();
        }
    }

    swal({
        title: 'Notice',
        text: message,
        type: type || 'info',
        confirmButtonText: 'OK',
        closeOnConfirm: true
    }, function () {
        triggerSearchButton();
    });
}

function showSubmitConfirmation(onConfirm) {
    swal({
        title: 'Confirm Submit',
        text: 'This is not editable, so be sure before submit.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, submit',
        cancelButtonText: 'Cancel',
        closeOnConfirm: true
    }, function (isConfirm) {
        if (isConfirm) {
            onConfirm();
        }
    });
}

function validateIssueQtyInput(inputEl) {
    if (!inputEl) {
        return true;
    }

    var rawValue = (inputEl.value || '').trim();
    if (rawValue === '') {
        return true;
    }

    var issueQty = parseFloat(rawValue);
    var pendingQty = parseFloat(inputEl.getAttribute('data-pending-qty') || '0');

    if (!isNaN(issueQty) && !isNaN(pendingQty) && issueQty > pendingQty) {
        showSimpleAlert('Issue qty cannot be greater than pending qty.', 'warning');
        inputEl.value = '';
        inputEl.focus();
        return false;
    }

    return true;
}

document.addEventListener('change', function (e) {
    var inputEl = e.target.closest('input[name="issues_qty[]"]');
    if (!inputEl) {
        return;
    }

    validateIssueQtyInput(inputEl);
});

document.addEventListener('click', function (e) {
    var header = e.target.closest('[data-action="toggle-history"]');
    if (!header) {
        return;
    }

    var index = header.getAttribute('data-index');
    var body = document.getElementById('issue-history-body-' + index);
    var arrow = header.querySelector('.issue-history-arrow i');

    if (!body) {
        return;
    }

    var isOpen = body.style.display !== 'none';
    body.style.display = isOpen ? 'none' : 'block';
    if (arrow) {
        arrow.className = isOpen ? 'fa fa-chevron-down' : 'fa fa-chevron-up';
    }
});

document.addEventListener('submit', function (e) {
    var form = e.target.closest('#issueItemsForm');
    if (!form) {
        return;
    }
    e.preventDefault();

    var qtyInputs = form.querySelectorAll('input[name="issues_qty[]"]');
    var hasIssueQty = false;
    var hasInvalidQty = false;

    qtyInputs.forEach(function (input) {
        if (!validateIssueQtyInput(input)) {
            hasInvalidQty = true;
            return;
        }

        var val = (input.value || '').trim();
        if (val !== '' && parseFloat(val) > 0) {
            hasIssueQty = true;
        }
    });

    if (hasInvalidQty) {
        return;
    }

    if (!hasIssueQty) {
        showSimpleAlert('Please enter at least one issue qty.', 'warning');
        return;
    }

    var requestBody = new URLSearchParams(new FormData(form)).toString();

    showSubmitConfirmation(function () {
        try {
            $.ajax({
                url: 'ajax.php',
                data: requestBody,
                type: 'POST',
                success: function (data) {
                    var result;
                    try {
                        result = JSON.parse(data);
                    } catch (err) {
                        showSimpleAlert('Unexpected server response', 'error');
                        return;
                    }

                    if (result.status === 'success') {
                        showAlertThenSubmitSearchForm(result.message || 'Submitted successfully', 'success');
                        
                    } else {
                        showSimpleAlert(result.message || 'Failed to submit data', 'error');
                    }
                },
                error: function () {
                    showSimpleAlert('Unable to submit issue quantities.', 'error');
                }
            });
        } catch (err) {
            showSimpleAlert('Unable to submit issue quantities.', 'error');
        }
    });
});
