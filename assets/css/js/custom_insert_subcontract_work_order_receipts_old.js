function toReceiptNumber(value) {
    var parsed = parseFloat(value);
    return isNaN(parsed) ? 0 : parsed;
}

function formatReceiptNumber(value) {
    return toReceiptNumber(value).toFixed(2);
}

function calculateReceiptAmounts() {
    var receivedQtyInput = document.getElementById('received_qty');
    if (!receivedQtyInput) {
        return true;
    }

    var receivedQty = toReceiptNumber(receivedQtyInput.value);
    var pendingQty = toReceiptNumber(document.getElementById('pending_receipt_qty').value);
    var balanceAfterReceipt = pendingQty - receivedQty;
    var isValid = balanceAfterReceipt >= 0;
    document.getElementById('balance_after_receipt_display').textContent = formatReceiptNumber(isValid ? balanceAfterReceipt : 0);

    var errorNode = document.getElementById('received_qty_error');
    if (errorNode) {
        if (isValid) {
            errorNode.classList.add('d-none');
        } else {
            errorNode.classList.remove('d-none');
        }
    }

    return isValid;
}

document.addEventListener('input', function (e) {
    if (e.target.closest('#received_qty')) {
        calculateReceiptAmounts();
    }
});

document.addEventListener('submit', function (e) {
    var form = e.target.closest('#subcontractWorkOrderReceiptForm');
    if (!form) {
        return;
    }

    e.preventDefault();

    var workOrderId = document.getElementById('subcontract_work_order_id').value;
    var receivedQty = toReceiptNumber(document.getElementById('received_qty').value);
    var pendingQty = toReceiptNumber(document.getElementById('pending_receipt_qty').value);

    if (!workOrderId) {
        swal('Validation', 'Please select a subcontract work order.', 'warning');
        return;
    }

    if (receivedQty <= 0) {
        swal('Validation', 'Received qty should be greater than zero.', 'warning');
        return;
    }

    if (receivedQty > pendingQty || !calculateReceiptAmounts()) {
        swal('Validation', 'Received qty cannot be greater than pending receipt qty.', 'warning');
        return;
    }

    swal({
        title: 'Confirm submission?',
        text: 'This receipt cannot be edited later. Please verify all details before submit.',
        icon: 'warning',
        buttons: ['Cancel', 'Yes, Submit'],
        dangerMode: true
    }).then(function (willSubmit) {
        if (willSubmit) {
            form.submit();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    calculateReceiptAmounts();
});