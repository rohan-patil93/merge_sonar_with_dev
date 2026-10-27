function toReceiptNumber(value) {
    var parsed = parseFloat(value);
    return isNaN(parsed) ? 0 : parsed;
}

function formatReceiptNumber(value) {
    return toReceiptNumber(value).toFixed(2);
}

function escapeReceiptHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

var receiptTaxPreviewRequestId = 0;
var receiptTaxPreviewReady = false;
var receiptTaxPreviewQty = '';
var receiptTaxPreviewWorkOrderId = '';

function setReceiptHiddenValue(id, value) {
    var node = document.getElementById(id);
    if (node) {
        node.value = value;
    }
}

function setReceiptText(id, value) {
    var node = document.getElementById(id);
    if (node) {
        node.textContent = value;
    }
}

function getReceiptWorkOrderId() {
    var receiptFormWorkOrder = document.querySelector('#subcontractWorkOrderReceiptForm input[name="subcontract_work_order_id"]');
    if (receiptFormWorkOrder && receiptFormWorkOrder.value) {
        return receiptFormWorkOrder.value;
    }

    var selectedWorkOrder = document.getElementById('subcontract_work_order_id');
    return selectedWorkOrder ? selectedWorkOrder.value : '';
}

function openReceiptHistoryModal() {
    var modalNode = document.getElementById('subcontractReceiptHistoryModal');
    if (!modalNode) {
        return;
    }

    if (window.jQuery && jQuery.fn.modal) {
        jQuery(modalNode).modal('show');
    } else if (window.bootstrap && bootstrap.Modal) {
        if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
            bootstrap.Modal.getOrCreateInstance(modalNode).show();
        } else {
            new bootstrap.Modal(modalNode).show();
        }
    }
}

function setReceiptHistoryStatus(message, type) {
    var statusNode = document.getElementById('receipt_history_status');
    if (!statusNode) {
        return;
    }

    statusNode.className = 'alert alert-' + (type || 'info') + ' mb-3';
    statusNode.textContent = message;
}

function renderReceiptHistoryTaxSplit(taxSplitDetails) {
    if (!Array.isArray(taxSplitDetails) || taxSplitDetails.length === 0) {
        return '-';
    }

    return taxSplitDetails.map(function (tax) {
        return escapeReceiptHtml(tax.tax_name || '-') + ' ' +
            formatReceiptNumber(tax.tax_percentage) + '%: ' +
            formatReceiptNumber(tax.tax_amount);
    }).join('<br>');
}

function renderReceiptHistory(response) {
    var summaryNode = document.getElementById('receipt_history_summary');
    var bodyNode = document.getElementById('receipt_history_body');

    if (!bodyNode) {
        return;
    }

    if (!response || response.status !== 'success') {
        if (summaryNode) {
            summaryNode.classList.add('d-none');
        }
        bodyNode.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No history loaded.</td></tr>';
        setReceiptHistoryStatus(response && response.message ? response.message : 'Unable to load receipt history.', 'danger');
        return;
    }

    var workOrder = response.work_order || {};
    setReceiptText('history_work_order_no', workOrder.transaction_number || '-');
    setReceiptText('history_issued_qty', formatReceiptNumber(workOrder.work_order_item_qty));
    setReceiptText('history_total_received_qty', formatReceiptNumber(workOrder.total_received_qty));
    setReceiptText('history_pending_qty', formatReceiptNumber(workOrder.pending_receipt_qty));

    if (summaryNode) {
        summaryNode.classList.remove('d-none');
    }

    var history = Array.isArray(response.history) ? response.history : [];
    if (history.length === 0) {
        bodyNode.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No receipts found for this work order.</td></tr>';
        setReceiptHistoryStatus('No previous receipts found for this work order.', 'info');
        return;
    }

    bodyNode.innerHTML = history.map(function (row) {
        return '<tr>' +
            '<td>' + escapeReceiptHtml(row.transaction_number || '-') + '</td>' +
            '<td>' + escapeReceiptHtml(row.transaction_date || '-') + '</td>' +
            '<td class="text-end">' + formatReceiptNumber(row.received_qty) + '</td>' +
            '<td class="text-end">' + formatReceiptNumber(row.pending_qty) + '</td>' +
            '<td class="text-end">' + formatReceiptNumber(row.amount) + '</td>' +
            '<td class="text-end">' + formatReceiptNumber(row.tax_amount) + '</td>' +
            '<td class="text-end">' + formatReceiptNumber(row.total_amount) + '</td>' +
            '<td>' + renderReceiptHistoryTaxSplit(row.tax_split_details) + '</td>' +
            '<td>' + escapeReceiptHtml(row.notes || '-') + '</td>' +
            '</tr>';
    }).join('');

    setReceiptHistoryStatus('Receipt history loaded.', 'success');
}

function loadReceiptHistory(workOrderId) {
    var selectedWorkOrderId = workOrderId || getReceiptWorkOrderId();

    openReceiptHistoryModal();

    if (!selectedWorkOrderId) {
        renderReceiptHistory({
            status: 'fail',
            message: 'Please select a subcontract work order first.'
        });
        return;
    }

    setReceiptHistoryStatus('Loading receipt history...', 'info');
    setReceiptText('history_work_order_no', '-');
    setReceiptText('history_issued_qty', '0.00');
    setReceiptText('history_total_received_qty', '0.00');
    setReceiptText('history_pending_qty', '0.00');

    var bodyNode = document.getElementById('receipt_history_body');
    if (bodyNode) {
        bodyNode.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Loading...</td></tr>';
    }

    jQuery.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
            page: 'get_subcontract_work_order_receipt_history',
            subcontract_work_order_id: selectedWorkOrderId
        },
        success: function (response) {
            renderReceiptHistory(response);
        },
        error: function () {
            renderReceiptHistory({
                status: 'fail',
                message: 'Unable to load receipt history.'
            });
        }
    });
}

function clearReceiptTaxPreview(message) {
    receiptTaxPreviewReady = false;
    receiptTaxPreviewQty = '';
    receiptTaxPreviewWorkOrderId = '';

    setReceiptHiddenValue('receipt_amount', '0.00');
    setReceiptHiddenValue('receipt_tax_amount', '0.00');
    setReceiptHiddenValue('receipt_total_amount', '0.00');
    setReceiptHiddenValue('receipt_tax_type', '');
    setReceiptHiddenValue('receipt_tax_split_details', '');
    setReceiptHiddenValue('receipt_gst_percentage', '0.00');
    setReceiptHiddenValue('receipt_hsn_code_id', '0');
    setReceiptHiddenValue('receipt_hsn_code', '');
    setReceiptText('receipt_amount_display', '0.00');
    setReceiptText('receipt_tax_amount_display', '0.00');
    setReceiptText('receipt_total_amount_display', '0.00');
    setReceiptText('receipt_tax_status', message || 'Enter received qty to calculate tax.');

    var taxBody = document.getElementById('receipt_tax_breakdown_body');
    if (taxBody) {
        taxBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No tax calculated.</td></tr>';
    }
}

function renderReceiptTaxPreview(data, receivedQty, workOrderId) {
    var amount = formatReceiptNumber(data.amount);
    var taxAmount = formatReceiptNumber(data.tax_amount);
    var totalAmount = formatReceiptNumber(data.total_amount);
    var taxSplitDetails = Array.isArray(data.tax_split_details) ? data.tax_split_details : [];

    setReceiptHiddenValue('receipt_amount', amount);
    setReceiptHiddenValue('receipt_tax_amount', taxAmount);
    setReceiptHiddenValue('receipt_total_amount', totalAmount);
    setReceiptHiddenValue('receipt_tax_type', data.tax_type || '');
    setReceiptHiddenValue('receipt_tax_split_details', JSON.stringify(taxSplitDetails));
    setReceiptHiddenValue('receipt_gst_percentage', formatReceiptNumber(data.gst_percentage));
    setReceiptHiddenValue('receipt_hsn_code_id', data.hsn_code_id || '0');
    setReceiptHiddenValue('receipt_hsn_code', data.hsn_code || '');
    setReceiptText('receipt_amount_display', amount);
    setReceiptText('receipt_tax_amount_display', taxAmount);
    setReceiptText('receipt_total_amount_display', totalAmount);

    var taxLabel = data.hsn_code ? 'HSN ' + data.hsn_code : 'Item tax';
    setReceiptText('receipt_tax_status', taxLabel + ' | GST ' + formatReceiptNumber(data.gst_percentage) + '%');

    var taxBody = document.getElementById('receipt_tax_breakdown_body');
    if (taxBody) {
        if (taxSplitDetails.length > 0) {
            taxBody.innerHTML = taxSplitDetails.map(function (tax) {
                return '<tr>' +
                    '<td>' + escapeReceiptHtml(tax.tax_name || '-') + '</td>' +
                    '<td class="text-end">' + formatReceiptNumber(tax.tax_percentage) + '%</td>' +
                    '<td class="text-end">' + formatReceiptNumber(tax.tax_amount) + '</td>' +
                    '</tr>';
            }).join('');
        } else {
            taxBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No tax applicable for this item.</td></tr>';
        }
    }

    receiptTaxPreviewReady = true;
    receiptTaxPreviewQty = String(receivedQty);
    receiptTaxPreviewWorkOrderId = String(workOrderId);
}

function loadReceiptTaxPreview() {
    var receivedQtyInput = document.getElementById('received_qty');
    if (!receivedQtyInput) {
        return null;
    }

    var workOrderId = getReceiptWorkOrderId();
    var receivedQty = toReceiptNumber(receivedQtyInput.value);

    if (!workOrderId || receivedQty <= 0 || !calculateReceiptAmounts()) {
        clearReceiptTaxPreview('Enter valid received qty to calculate tax.');
        return null;
    }

    var requestId = ++receiptTaxPreviewRequestId;
    setReceiptText('receipt_tax_status', 'Calculating tax...');

    return jQuery.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
            page: 'get_subcontract_work_order_receipt_tax_preview',
            subcontract_work_order_id: workOrderId,
            received_qty: receivedQty
        },
        success: function (response) {
            if (requestId !== receiptTaxPreviewRequestId) {
                return;
            }

            if (response && response.status === 'success') {
                renderReceiptTaxPreview(response, receivedQty, workOrderId);
            } else {
                clearReceiptTaxPreview(response && response.message ? response.message : 'Unable to calculate tax.');
            }
        },
        error: function () {
            if (requestId === receiptTaxPreviewRequestId) {
                clearReceiptTaxPreview('Unable to calculate tax.');
            }
        }
    });
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
        loadReceiptTaxPreview();
    }
});

document.addEventListener('click', function (e) {
    var historyBtn = e.target.closest('[data-action="open-receipt-history"]');
    if (!historyBtn) {
        return;
    }

    loadReceiptHistory(historyBtn.dataset.workOrderId || '');
});

document.addEventListener('submit', function (e) {
    var form = e.target.closest('#subcontractWorkOrderReceiptForm');
    if (!form) {
        return;
    }

    e.preventDefault();

    var workOrderId = getReceiptWorkOrderId();
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

    function confirmReceiptSubmit() {
        swal({
            title: 'Confirm submission?',
            text: 'This receipt cannot be edited later. Please verify all details before submit.',
            icon: 'warning',
            buttons: ['Cancel', 'Yes, Submit'],
            dangerMode: true
        }, function (willSubmit) {
            if (willSubmit) {
                form.submit();
            }
        });
    }

    if (!receiptTaxPreviewReady || receiptTaxPreviewQty !== String(receivedQty) || receiptTaxPreviewWorkOrderId !== String(workOrderId)) {
        var taxRequest = loadReceiptTaxPreview();
        if (taxRequest) {
            taxRequest.done(function (response) {
                if (response && response.status === 'success') {
                    confirmReceiptSubmit();
                } else {
                    swal('Validation', response && response.message ? response.message : 'Unable to calculate tax.', 'warning');
                }
            }).fail(function () {
                swal('Validation', 'Unable to calculate tax.', 'warning');
            });
        }
        return;
    }

    confirmReceiptSubmit();
});

document.addEventListener('DOMContentLoaded', function () {
    calculateReceiptAmounts();
    loadReceiptTaxPreview();
});
