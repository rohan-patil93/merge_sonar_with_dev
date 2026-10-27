/* Leave Balance Management Page JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    initializePage();
});

/**
 * Initialize the page
 */
function initializePage() {
    const companyId = document.getElementById('company_id').value;
    const userId = document.getElementById('user_id').value;
    const currentYear = document.getElementById('current_year').value;

    // Initialize Select2 for employee dropdown
    if (jQuery.fn.select2) {
        jQuery('#employee-dropdown').select2({
            placeholder: '-- Select Employee --',
            allowClear: true,
            width: '100%'
        });
    }

    // Load employees and initial data
    loadEmployeesAndData(companyId, userId);

    // Attach event listeners
    attachEventListeners(companyId, userId, currentYear);
}

/**
 * Attach all event listeners using event delegation
 */
function attachEventListeners(companyId, userId, currentYear) {
    // Employee dropdown change - using jQuery for Select2 compatibility
    jQuery('#employee-dropdown').on('change', function() {
        const selectedEmployeeId = jQuery(this).val();
        if (selectedEmployeeId) {
            loadLeaveData(selectedEmployeeId, companyId, userId, currentYear);
        } else {
            clearLeaveData();
        }
    });

    // Add Leave Allocation button
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action="add-leave-allocation"]');
        if (btn) {
            const selectedEmployeeId = document.getElementById('employee-dropdown').value;
            if (!selectedEmployeeId) {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', 'Please select an employee first', '');
                return;
            }
            openAddAllocationModal(selectedEmployeeId, companyId, userId, currentYear);
        }
    });

    // Save Allocation button
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action="save-allocation"]');
        if (btn) {
            validateAndSaveAllocation(companyId, userId, currentYear);
        }
    });

    // Modal cancel button
    const modal = document.getElementById('addAllocationModal');
    if (modal) {
        const cancelBtn = modal.querySelector('[data-bs-dismiss="modal"]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                resetAllocationForm();
            });
        }
    }
}

/**
 * Load employees and initial data
 */
function loadEmployeesAndData(companyId, userId) {
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=load_leave_employees&company_id=' + companyId + '&user_id=' + userId,
        success: function(response) {
            try {
                var jsonResponse = typeof response == 'string' ? JSON.parse(response) : response;
                if (jsonResponse.status == 'success') {
                    populateEmployeeDropdown(jsonResponse.employees);
                    if (jsonResponse.leave_types) {
                        populateLeaveTypeDropdown(jsonResponse.leave_types);
                    }
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
        },
        error: function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                'animated flipInX', 'animated flipOutX', 'Error loading employees', '');
        }
    });
}

/**
 * Populate employee dropdown
 */
function populateEmployeeDropdown(employees) {
    const dropdown = document.getElementById('employee-dropdown');
    if (!dropdown) return;

    // Clear existing options except the first one
    while (dropdown.options.length > 1) {
        dropdown.remove(1);
    }

    // Add employee options
    employees.forEach(function(emp) {
        const option = document.createElement('option');
        option.value = emp.id;
        option.textContent = emp.display_name + '-'+ emp.emp_code;
        dropdown.appendChild(option);
    });

    // Refresh Select2 if available
    if (jQuery.fn.select2) {
        jQuery(dropdown).trigger('change');
    }
}

/**
 * Populate leave type dropdown in modal
 */
function populateLeaveTypeDropdown(leaveTypes) {
    const dropdown = document.getElementById('leave_type_id');
    if (!dropdown) return;

    while (dropdown.options.length > 1) {
        dropdown.remove(1);
    }

    leaveTypes.forEach(function(leaveType) {
        const option = document.createElement('option');
        option.value = leaveType.id;
        option.textContent = leaveType.leave_type_name;
        dropdown.appendChild(option);
    });
}

/**
 * Load leave balance and allocation data for selected employee
 */
function loadLeaveData(employeeId, companyId, userId, currentYear) {
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=load_leave_balance_data&employee_id=' + employeeId + '&company_id=' + companyId + 
              '&user_id=' + userId + '&leave_year=' + currentYear,
        success: function(response) {
            try {
                var jsonResponse = typeof response == 'string' ? JSON.parse(response) : response;
                if (jsonResponse.status == 'success') {
                    renderLeaveBalances(jsonResponse.balances || []);
                    renderLeaveAllocations(jsonResponse.allocations || []);

                    // Show sections if there's data
                    if (jsonResponse.balances && jsonResponse.balances.length > 0) {
                        document.getElementById('balance-section').classList.remove('is-hidden');
                        document.getElementById('allocation-section').classList.remove('is-hidden');
                        document.getElementById('no-data-message').classList.add('is-hidden');
                    } else {
                        document.getElementById('balance-section').classList.add('is-hidden');
                        document.getElementById('allocation-section').classList.add('is-hidden');
                        document.getElementById('no-data-message').classList.remove('is-hidden');
                    }
                } else {
                    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                        'animated flipInX', 'animated flipOutX', jsonResponse.message || 'Error loading data', '');
                    clearLeaveData();
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                clearLeaveData();
            }
        },
        error: function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                'animated flipInX', 'animated flipOutX', 'Error loading leave data', '');
            clearLeaveData();
        }
    });
}

/**
 * Render leave balance cards
 */
function renderLeaveBalances(balances) {
    const content = document.getElementById('balance-content');
    if (!content) return;

    content.innerHTML = '';

    if (!balances || balances.length == 0) {
        content.innerHTML = '<p class="text-muted">No leave balance data available.</p>';
        return;
    }

    balances.forEach(function(balance) {
        const card = document.createElement('div');
        card.className = 'col-md-4 mb-3';
        
        const applicableValue = parseFloat(balance.applicable_leaves) || 0;
        const consumedValue = parseFloat(balance.consumed_leaves) || 0;
        const availableValue = applicableValue - consumedValue;
        
        const availableClass = availableValue > 0 ? 'positive' : (availableValue == 0 ? '' : 'danger');

        card.innerHTML = `
            <div class="balance-card">
                <div class="balance-card-header">
                    <span class="leave-type-name">${balance.leave_type_name || 'Unknown'}</span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Applicable Leaves:</span>
                    <span class="balance-value">${applicableValue}</span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Consumed Leaves:</span>
                    <span class="balance-value">${consumedValue}</span>
                </div>
                <div class="balance-item">
                    <span class="balance-label">Available Leaves:</span>
                    <span class="balance-value ${availableClass}">${availableValue.toFixed(2)}</span>
                </div>
                ${balance.unapproved_leaves ? `
                    <div class="balance-item">
                        <span class="balance-label">Unapproved Leaves:</span>
                        <span class="balance-value warning">${balance.unapproved_leaves}</span>
                    </div>
                ` : ''}
            </div>
        `;
        content.appendChild(card);
    });
}

/**
 * Render leave allocations table
 */
function renderLeaveAllocations(allocations) {
    const tbody = document.getElementById('allocations-tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!allocations || allocations.length == 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="6" class="text-center text-muted">No allocation records found.</td>';
        tbody.appendChild(tr);
        return;
    }

    allocations.forEach(function(allocation) {
        const tr = document.createElement('tr');
        
        const statusClass = allocation.current_status == 0 ? 'status-active' : 
                           (allocation.current_status == 1 ? 'status-pending' : 'status-inactive');
        const statusText = allocation.current_status == 0 ? 'Active' : 
                          (allocation.current_status == 1 ? 'Pending' : 'Inactive');
        
        const displayDate = formatDisplayDate(allocation.transaction_date || '');

        tr.innerHTML = `
            <td>${allocation.leave_type_name || 'Unknown'}</td>
            <td>${displayDate}</td>
            <td>${parseFloat(allocation.applicable_leaves || 0).toFixed(2)}</td>
            <td>${parseFloat(allocation.consumed_leaves || 0).toFixed(2)}</td>
            <td>${allocation.note || '-'}</td>
            <td><span class="allocation-status ${statusClass}">${statusText}</span></td>
        `;
        tbody.appendChild(tr);
    });
}

/**
 * Format date string to dd-mm-yyyy
 */
function formatDisplayDate(dateStr) {
    if (!dateStr) return '';
    const clean = dateStr.split(' ')[0];
    const parts = clean.split('-');
    if (parts.length !== 3) return dateStr;
    return parts[2] + '-' + parts[1] + '-' + parts[0];
}

/**
 * Clear leave data display
 */
function clearLeaveData() {
    document.getElementById('balance-section').classList.add('is-hidden');
    document.getElementById('allocation-section').classList.add('is-hidden');
    document.getElementById('no-data-message').classList.remove('is-hidden');
    document.getElementById('balance-content').innerHTML = '';
    document.getElementById('allocations-tbody').innerHTML = '';
}

/**
 * Open Add Allocation Modal
 */
function openAddAllocationModal(employeeId, companyId, userId, currentYear) {
    // Store values in form for later use
    document.getElementById('leave-allocation-form').dataset.employeeId = employeeId;
    document.getElementById('leave-allocation-form').dataset.companyId = companyId;
    document.getElementById('leave-allocation-form').dataset.userId = userId;
    document.getElementById('leave-allocation-form').dataset.currentYear = currentYear;

    // Reset form fields
    resetAllocationForm();

    // Show modal using Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('addAllocationModal'));
    modal.show();
}

/**
 * Validate and save leave allocation
 */
function validateAndSaveAllocation(companyId, userId, currentYear) {
    const leaveTypeId = document.getElementById('leave_type_id').value;
    const transactionDate = document.getElementById('transaction_date').value;
    const applicableLeaves = parseFloat(document.getElementById('applicable_leaves').value) || 0;
    const consumedLeaves = parseFloat(document.getElementById('consumed_leaves').value) || 0;
    const note = document.getElementById('note').value;
    const employeeId = document.getElementById('leave-allocation-form').dataset.employeeId;

    // Validation
    if (!leaveTypeId) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', 'Please select a leave type', '');
        return;
    }

    if (!transactionDate) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', 'Please select a transaction date', '');
        return;
    }

    if (applicableLeaves == 0 && consumedLeaves == 0) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', 'Please enter at least one leave value', '');
        return;
    }

    // Confirmation
    swal({
        title: 'Confirm Leave Allocation',
        text: `Add ${applicableLeaves} applicable leave(s) and ${consumedLeaves} consumed leave(s) on ${transactionDate}?`,
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, Save',
        cancelButtonText: 'Cancel',
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            sendSaveRequest(employeeId, leaveTypeId, applicableLeaves, consumedLeaves, note, 
                           transactionDate, companyId, userId, currentYear);
        }
    });
}

/**
 * Send save request to backend
 */
function sendSaveRequest(employeeId, leaveTypeId, applicableLeaves, consumedLeaves, note, 
                         transactionDate, companyId, userId, currentYear) {
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=save_leave_allocation&employee_id=' + employeeId + 
              '&leave_type_id=' + leaveTypeId + 
              '&applicable_leaves=' + applicableLeaves + 
              '&consumed_leaves=' + consumedLeaves + 
              '&transaction_date=' + transactionDate + 
              '&note=' + encodeURIComponent(note) + 
              '&company_id=' + companyId + 
              '&user_id=' + userId + 
              '&leave_year=' + currentYear,
        success: function(response) {
            try {
                var jsonResponse = typeof response == 'string' ? JSON.parse(response) : response;
                if (jsonResponse.status == 'success') {
                    swal.close();
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success',
                        'animated flipInX', 'animated flipOutX', jsonResponse.message || 'Leave allocated successfully', '');
                    
                    // Close modal
                    var modal = bootstrap.Modal.getInstance(document.getElementById('addAllocationModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Reload data
                    setTimeout(function() {
                        loadLeaveData(employeeId, companyId, userId, currentYear);
                    }, 1500);
                } else {
                    swal.close();
                    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                        'animated flipInX', 'animated flipOutX', jsonResponse.message || 'Error saving allocation', '');
                }
            } catch (e) {
                swal.close();
                console.error('Error parsing response:', e);
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', 'Error processing request', '');
            }
        },
        error: function() {
            swal.close();
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                'animated flipInX', 'animated flipOutX', 'Network error', '');
        }
    });
}

/**
 * Reset allocation form
 */
function resetAllocationForm() {
    document.getElementById('leave_type_id').value = '';
    document.getElementById('transaction_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('applicable_leaves').value = '0';
    document.getElementById('consumed_leaves').value = '0';
    document.getElementById('note').value = '';

    // Remove validation classes if any
    const inputs = document.querySelectorAll('#leave-allocation-form input, #leave-allocation-form select, #leave-allocation-form textarea');
    inputs.forEach(function(input) {
        input.classList.remove('is-invalid');
        const feedback = input.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.remove();
        }
    });
}

/**
 * Notification utility function (fallback if not defined elsewhere)
 */
function tostarnotify(position1, position2, icon, label, inAnimation, outAnimation, title, message) {
    // This function should be defined in the main app utilities
    // If not available, use browser alert as fallback
    if (typeof $.notify !== 'undefined') {
        $.notify({
            title: title,
            message: message,
            icon: icon
        }, {
            type: label.includes('danger') ? 'danger' : (label.includes('success') ? 'success' : 'info'),
            placement: {
                from: position1,
                align: position2
            }
        });
    } else {
        alert(title + ': ' + message);
    }
}
