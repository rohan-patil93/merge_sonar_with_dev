/**
 * Personal Gateway - JavaScript Functions
 * Page: custom_insert_personal_gateway.php
 */

// Session variables (will be set from PHP data)
let company_id = 0;
let user_id = 0;
let companyName = 'Company Name';
let companyLogo = '';

/**
 * Initialize session data from PHP
 */
function initPersonalGatewayData() {
    const dataContainer = document.querySelector('.personal-gateway-php-data');
    if (dataContainer) {
        company_id = parseInt(dataContainer.dataset.companyId) || 0;
        user_id = parseInt(dataContainer.dataset.userId) || 0;
        companyName = dataContainer.dataset.companyName || 'Company Name';
        companyLogo = dataContainer.dataset.companyLogo || '';
    }
}

/**
 * Handle method type change - update form labels and behavior
 * Triggered by: method_type radio change
 */
function handleMethodTypeChange() {
    const methodType = document.querySelector('input[name="method_type"]:checked').value;
    const submitBtnText = document.getElementById('submitBtnText');
    
    if (methodType === 'exit_first') {
        if (submitBtnText) submitBtnText.textContent = 'Record Exit';
    } else {
        if (submitBtnText) submitBtnText.textContent = 'Record Entry';
    }
}

/**
 * Handle employee select change - auto-fill contact and email
 */
function handleEmployeeSelectChange() {
    const employeeSelect = document.getElementById('employeeSelect');
    const contactInput = document.getElementById('employeeContact');
    const emailInput = document.getElementById('employeeEmail');
    const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const mobile = selectedOption.dataset.mobile || '';
        const email = selectedOption.dataset.email || '';
        contactInput.value = mobile;
        emailInput.value = email;
    } else {
        contactInput.value = '';
        emailInput.value = '';
    }
}

/**
 * Toggle other purpose input visibility
 */
function toggleOtherPurpose() {
    const visitPurpose = document.getElementById('visitPurpose').value;
    const otherRow = document.getElementById('otherPurposeRow');
    const otherInput = document.getElementById('otherPurpose');

    if (visitPurpose === 'other') {
        otherRow.style.display = 'flex';
        otherInput.required = true;
    } else {
        otherRow.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}

/**
 * Close print modal function
 */
function closePrintModal() {
    if (typeof jQuery !== 'undefined') {
        $('#printSlipModal').modal('hide');
    } else {
        const modal = document.getElementById('printSlipModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
}

/**
 * Print slip function
 */
function printSlip() {
    const printContent = document.getElementById('printSlipContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Personal Gate Pass</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 10px; font-size: 11px; }
                    .gate-pass-container { border: 2px solid #000; padding: 10px; max-width: 300px; margin: 0 auto; background: white; }
                    .pass-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
                    .logo-section { flex: 0 0 auto; }
                    .company-logo { max-width: 40px; max-height: 40px; object-fit: contain; }
                    .logo-placeholder { width: 40px; height: 40px; border: 1px solid #000; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: bold; }
                    .company-section { flex: 1; text-align: right; }
                    .company-section h4 { margin: 0; font-size: 12px; font-weight: bold; }
                    .header-divider { border: none; border-top: 1px solid #000; margin: 8px 0; }
                    .pass-content { margin: 8px 0; }
                    .info-row { display: flex; justify-content: space-between; margin: 4px 0; font-size: 10px; }
                    .left-info, .right-info { flex: 1; }
                    .right-info { text-align: right; }
                    .phone-info, .full-info { width: 100%; }
                    .pass-footer { text-align: center; margin-top: 8px; border-top: 1px solid #000; padding-top: 4px; }
                    .pass-footer small { font-size: 9px; font-weight: bold; }
                    @media print { body { margin: 0; } .gate-pass-container { margin: 0; } }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
}

/**
 * Show print slip with entry data
 */
function showPrintSlip(entryData) {
    const methodType = entryData.method_type || 'entry_first';
    const methodLabel = methodType === 'exit_first' ? 'Exit First' : 'Entry First';
    
    // Determine In Time and Out Time based on method_type
    let inTime = entryData.entry_time || entryData.in_time || '';
    let outTime = entryData.exit_time || entryData.out_time || '';
    
    const slipContent = `
        <div class="gate-pass-container">
            <div class="pass-header">
                <div class="logo-section">
                    ${companyLogo ? `<img src="${companyLogo}" alt="Logo" class="company-logo">` : '<div class="logo-placeholder">LOGO</div>'}
                </div>
                <div class="company-section">
                    <h4>${companyName}</h4>
                </div>
            </div>
            
            <hr class="header-divider">
            
            <div class="pass-content">
                <div class="info-row">
                    <div class="left-info">
                        <strong>Date:</strong> ${entryData.entry_date || new Date().toLocaleDateString()}
                    </div>
                    <div class="right-info">
                        <strong>Visitor:</strong> ${entryData.employee_name}
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="phone-info">
                        <strong>Phone:</strong> ${entryData.employee_contact || 'N/A'}
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="left-info">
                        <strong>Vehicle:</strong> ${entryData.vehicle_number || 'N/A'}
                    </div>
                    <div class="right-info">
                        <strong>Purpose:</strong> ${entryData.visit_purpose}
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="left-info">
                        <strong>Location:</strong> ${entryData.location || 'N/A'}
                    </div>
                    <div class="right-info">
                        <strong>Method:</strong> ${methodLabel}
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="left-info">
                        <strong>In Time:</strong> ${inTime || '___________'}
                    </div>
                    <div class="right-info">
                        <strong>Out Time:</strong> ${outTime || '___________'}
                    </div>
                </div>
            </div>
            
            <div class="pass-footer">
                <small>Personal Gate Pass</small>
            </div>
        </div>
    `;
    
    document.getElementById('printSlipContent').innerHTML = slipContent;
    $('#printSlipModal').modal('show');
}

/**
 * Record exit function
 */
function recordExit(entryId, employeeName) {
    recordAction(entryId, employeeName, 'entry_first');
}

/**
 * Record action function - handles both entry and exit based on method_type
 */
function recordAction(entryId, employeeName, methodType) {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    document.getElementById('exitEntryId').value = entryId;
    document.getElementById('exitEmployeeName').textContent = employeeName;
    
    // Set method_type in hidden field if it exists
    const methodTypeInput = document.getElementById('actionMethodType');
    if (methodTypeInput) {
        methodTypeInput.value = methodType;
    }
    
    // Update modal title and button based on method_type
    const modalTitle = document.querySelector('#exitModal .modal-title');
    const submitBtn = document.querySelector('#exitModal button[type="submit"]');
    const alertText = document.querySelector('#exitModal .alert');
    
    if (methodType === 'exit_first') {
        // Recording In time for exit-first method
        if (modalTitle) modalTitle.innerHTML = '<i class="ti-import text-success"></i> Record In Time';
        if (submitBtn) {
            submitBtn.className = 'btn btn-success waves-effect waves-light';
            submitBtn.innerHTML = '<i class="ti-check"></i> Record In';
        }
        if (alertText) alertText.innerHTML = '<i class="ti-info-alt"></i> Recording In time for: <strong id="exitEmployeeName">' + employeeName + '</strong>';
    } else {
        // Recording Out time for entry-first method
        if (modalTitle) modalTitle.innerHTML = '<i class="ti-export text-danger"></i> Record Out Time';
        if (submitBtn) {
            submitBtn.className = 'btn btn-danger waves-effect waves-light';
            submitBtn.innerHTML = '<i class="ti-check"></i> Record Out';
        }
        if (alertText) alertText.innerHTML = '<i class="ti-info-alt"></i> Recording Out time for: <strong id="exitEmployeeName">' + employeeName + '</strong>';
    }
    
    $('#exitModal').modal('show');
}

/**
 * Print person slip function
 */
function printPersonSlip(entryId) {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    $.post('ajax.php', {page: 'get_personal_entry_details', entry_id: entryId}, function(response) {
        try {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                showPrintSlip(result.data);
            } else {
                alert('Error loading entry details for printing: ' + (result.message || 'Unknown error'));
            }
        } catch (e) {
            alert('Error processing entry details.');
        }
    }).fail(function() {
        alert('Network error loading entry details.');
    });
}

/**
 * Load all personal gateway data
 */
function loadPersonalGatewayData() {
    loadCurrentPersons();
    loadPersonStatistics();
    loadRecentActivity();
}

/**
 * Load current persons
 */
function loadCurrentPersons() {
    if (typeof jQuery === 'undefined') return;
    
    $.post('ajax.php', {page: 'get_current_persons'}, function(response) {
        try {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                updateCurrentPersonsTable(result.data);
                updatePersonCountBadge(result.data.length);
            } else {
                showErrorInTable('#currentPersonsTable', 'Error loading persons');
            }
        } catch (e) {
            showErrorInTable('#currentPersonsTable', 'Error loading persons');
        }
    }).fail(function() {
        showErrorInTable('#currentPersonsTable', 'Network error loading persons');
    });
}

/**
 * Load person statistics
 */
function loadPersonStatistics() {
    if (typeof jQuery === 'undefined') return;
    
    $.post('ajax.php', {page: 'get_personal_statistics'}, function(response) {
        try {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                updateStatisticsCards(result.data);
            }
        } catch (e) {
            // Silent error handling
        }
    });
}

/**
 * Load recent activity
 */
function loadRecentActivity() {
    if (typeof jQuery === 'undefined') return;
    
    $.post('ajax.php', {page: 'get_personal_recent_activity'}, function(response) {
        try {
            const result = JSON.parse(response);
            if (result.status === 'success' && result.data) {
                updateRecentActivityTable(result.data);
            } else if (result.activities) {
                updateRecentActivityTable(result.activities);
            } else {
                showErrorInTable('#recentActivityTable', 'Error loading recent activity');
            }
        } catch (e) {
            showErrorInTable('#recentActivityTable', 'Error loading recent activity');
        }
    }).fail(function() {
        showErrorInTable('#recentActivityTable', 'Network error loading recent activity');
    });
}

/**
 * Show error in table
 */
function showErrorInTable(tableSelector, message) {
    const tbody = document.querySelector(tableSelector + ' tbody');
    if (tbody) {
        const colCount = tbody.closest('table').querySelectorAll('thead th').length || 2;
        tbody.innerHTML = `
            <tr>
                <td colspan="${colCount}" class="text-center p-4">
                    <i class="ti-alert text-danger" style="font-size: 2rem;"></i>
                    <p class="text-danger mt-2">${message}</p>
                </td>
            </tr>
        `;
    }
}

/**
 * Update person count badge
 */
function updatePersonCountBadge(count) {
    const badge = document.getElementById('persons-count-badge');
    if (badge) {
        badge.textContent = count + ' person' + (count !== 1 ? 's' : '');
    }
}

/**
 * Update current persons table
 */
function updateCurrentPersonsTable(persons) {
    const tbody = document.querySelector('#currentPersonsTable tbody');
    if (!tbody) return;

    if (persons.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="2">
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30">
                            <i class="ti-alert text-warning"></i> No Data Available
                        </p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                </td>
            </tr>
        `;
    } else {
        tbody.innerHTML = persons.map(person => {
            const methodType = person.method_type || 'entry_first';
            const isExitFirst = methodType === 'exit_first';
            
            // Determine which date and time to show based on method_type
            let timeDisplay = '';
            if (isExitFirst) {
                // For exit_first, show exit_date and exit_time (when they went out)
                const dateToUse = person.exit_date || person.entry_date || new Date().toISOString().split('T')[0];
                const timeToUse = person.exit_time;
                if (timeToUse && timeToUse !== '00:00:00') {
                    timeDisplay = new Date(dateToUse + ' ' + timeToUse).toLocaleDateString('en-US', {month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true});
                }
            } else {
                // For entry_first, show entry_date and entry_time (when they came in)
                const dateToUse = person.entry_date || new Date().toISOString().split('T')[0];
                const timeToUse = person.entry_time;
                if (timeToUse && timeToUse !== '00:00:00') {
                    timeDisplay = new Date(dateToUse + ' ' + timeToUse).toLocaleDateString('en-US', {month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true});
                }
            }
            
            // Determine button based on method_type
            const btnClass = isExitFirst ? 'btn-success' : 'btn-danger';
            const btnIcon = isExitFirst ? 'ti-import' : 'ti-export';
            const btnText = isExitFirst ? 'In' : 'Exit';
            const statusBadge = isExitFirst 
                ? '<span class="badge badge-danger" style="font-size:9px;"><i class="ti-export"></i> Outside</span>'
                : '<span class="badge badge-success" style="font-size:9px;"><i class="ti-import"></i> Inside</span>';
            
            return `
            <tr>
                <td>
                    <div class="d-inline-block align-middle">
                        <div class="d-inline-block">
                            <h6 class="m-b-0">${person.employee_name} ${statusBadge}</h6>
                            <p class="m-b-0 text-muted">
                                ${(person.visit_purpose || 'unknown').charAt(0).toUpperCase() + (person.visit_purpose || 'unknown').slice(1)}
                            </p>
                            <p class="m-b-0 text-muted">
                                <small>${person.vehicle_number || 'No Vehicle'}</small>
                            </p>
                            <p class="m-b-0 text-muted">
                                <small><i class="ti-location-pin"></i> ${person.location || 'Location not specified'}</small>
                            </p>
                            <p class="m-b-0 text-muted">
                                <small>${timeDisplay}</small>
                            </p>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm ${btnClass} btn-round record-action-btn"
                            data-entry-id="${person.id}" data-employee-name="${person.employee_name}" data-method-type="${methodType}">
                            <i class="${btnIcon}"></i> ${btnText}
                        </button>
                    </div>
                </td>
            </tr>
        `}).join('');
    }
}

/**
 * Update statistics cards
 */
function updateStatisticsCards(stats) {
    const currentCountEl = document.getElementById('stat-current-count');
    const todayEntriesEl = document.getElementById('stat-today-entries');
    const todayExitsEl = document.getElementById('stat-today-exits');
    const avgDurationEl = document.getElementById('stat-avg-duration');
    
    if (currentCountEl) currentCountEl.textContent = stats.current_count || 0;
    if (todayEntriesEl) todayEntriesEl.textContent = stats.today_entries || 0;
    if (todayExitsEl) todayExitsEl.textContent = stats.today_exits || 0;
    if (avgDurationEl) {
        const hours = Math.round((stats.avg_duration_minutes || 0) / 60 * 10) / 10;
        avgDurationEl.textContent = hours + 'h';
    }
    
    updatePersonCountBadge(stats.current_count || 0);
}


/**
 * Format date only
 */
function formatDate(timestamp, date) {
    try {
        let dateToFormat;
        if (timestamp && timestamp !== null && timestamp !== '' && timestamp !== 'null') {
            dateToFormat = new Date(timestamp);
        } else if (date) {
            dateToFormat = new Date(date);
        } else {
            return 'N/A';
        }
        if (isNaN(dateToFormat.getTime())) return 'N/A';
        return dateToFormat.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } catch (e) {
        return 'N/A';
    }
}

/**
 * Format date and time
 */
function formatDateTime(timestamp, date, time) {
    try {
        if (!timestamp && !date && !time) return 'Pending';
        let dateToFormat;
        if (timestamp && timestamp !== null && timestamp !== '' && timestamp !== 'null') {
            dateToFormat = new Date(timestamp);
        } else if (date && time) {
            dateToFormat = new Date(date + ' ' + time);
        } else if (date) {
            dateToFormat = new Date(date);
        } else {
            return 'N/A';
        }
        if (isNaN(dateToFormat.getTime())) return 'N/A';
        return dateToFormat.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    } catch (e) {
        return 'N/A';
    }
}

/**
 * Update recent activity table
 */
function updateRecentActivityTable(activities) {
    const tbody = document.querySelector('#recentActivityTable tbody');
    if (!tbody) return;

    if (!activities || activities.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30">
                            <i class="ti-alert text-warning"></i> No Data Available
                        </p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = activities.map(activity => {
        const methodType = activity.method_type || 'entry_first';
        const isExitFirst = methodType === 'exit_first';
        
        // Determine the date to show based on method_type
        const dateToShow = isExitFirst ? (activity.exit_date || activity.entry_date) : activity.entry_date;
        
        // Format In/Out times - handle null/empty values
        const inTime = activity.entry_time && activity.entry_time !== '00:00:00' 
            ? formatDateTime(null, activity.entry_date, activity.entry_time) 
            : '<span class="text-muted">Pending</span>';
        const outTime = activity.exit_time && activity.exit_time !== '00:00:00' 
            ? formatDateTime(null, activity.exit_date || activity.entry_date, activity.exit_time) 
            : '<span class="text-muted">Pending</span>';
        
        // Action badge based on method_type
        const actionBadge = isExitFirst 
            ? '<span class="badge badge-danger p-2"><i class="ti-export"></i> Exit First</span>'
            : '<span class="badge badge-success p-2"><i class="ti-import"></i> Entry First</span>';
        
        return `
        <tr>
            <td>
                <strong>${activity.employee_name || 'N/A'}</strong>
                <br><small class="text-muted">${activity.employee_contact || ''}</small>
                ${activity.location ? `<br><small class="text-muted"><i class="ti-location-pin"></i> ${activity.location}</small>` : ''}
            </td>
            <td>${actionBadge}</td>
            <td>${(activity.visit_purpose || 'unknown').charAt(0).toUpperCase() + (activity.visit_purpose || 'unknown').slice(1)}</td>
            <td>${activity.vehicle_number || '-'}</td>
            <td><small>${formatDate(null, dateToShow)}</small></td>
            <td>
                <small>
                    <strong>In:</strong> ${inTime}<br>
                    <strong>Out:</strong> ${outTime}
                </small>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-info btn-round print-person-slip-btn"
                    data-entry-id="${activity.id}" title="Print Slip">
                    <i class="ti-printer"></i>
                </button>
            </td>
        </tr>
    `}).join('');
}

/**
 * Submit personal entry via AJAX
 */
function submitPersonalEntry() {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    const form = document.getElementById('personalEntryForm');
    const formData = new FormData(form);
    formData.append('page', 'insert_personal_entry');
    
    // Explicitly get and set method_type - IMPORTANT: must be set before sending
    const methodTypeRadio = document.querySelector('input[name="method_type"]:checked');
    const methodType = methodTypeRadio ? methodTypeRadio.value : 'entry_first';
    formData.set('method_type', methodType);
    
    // Get employee name from select
    const employeeSelect = document.getElementById('employeeSelect');
    const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
    if (selectedOption) {
        formData.append('employee_name', selectedOption.dataset.name || selectedOption.text);
    }
    
    if (company_id) formData.append('company_id', company_id);
    if (user_id) formData.append('user_id', user_id);

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Recording...';
    submitBtn.disabled = true;

    // Determine success message based on method type
    const successMessage = methodType === 'exit_first' 
        ? 'Exit recorded successfully! Waiting for return.' 
        : 'Entry recorded successfully!';

    $.post('ajax.php', Object.fromEntries(formData), function(response) {
        try {
            const result = JSON.parse(response);
            
            if (result.status === 'sucess' || result.status === 'success') {
                tostarnotify(
                    'top', 'right',
                    'fa fa-thumbs-up',
                    'form-label label label-success',
                    'animated flipInX',
                    'animated flipOutX',
                    successMessage,
                    ''
                );
                
                const entryData = Object.fromEntries(formData);
                entryData.entry_date = entryData.entry_date || new Date().toISOString().split('T')[0];
                
                // Set in_time and out_time based on method_type for print slip
                const timeValue = entryData.in_time || new Date().toTimeString().slice(0, 5);
                if (methodType === 'exit_first') {
                    entryData.exit_time = timeValue;
                    entryData.entry_time = '';
                } else {
                    entryData.entry_time = timeValue;
                    entryData.exit_time = '';
                }
                entryData.method_type = methodType;
                
                showPrintSlip(entryData);
                
                form.reset();
                document.getElementById('employeeContact').value = '';
                document.getElementById('employeeEmail').value = '';
                toggleOtherPurpose();
                loadPersonalGatewayData();
            } else {
                tostarnotify(
                    'top', 'right',
                    'fa fa-exclamation-triangle',
                    'form-label label label-danger',
                    'animated flipInX',
                    'animated flipOutX',
                    result.message || 'Error recording. Please try again.',
                    ''
                );
            }
        } catch (e) {
            tostarnotify(
                'top', 'right',
                'fa fa-exclamation-triangle',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Error processing response. Please try again.',
                ''
            );
        }
    }).fail(function() {
        tostarnotify(
            'top', 'right',
            'fa fa-exclamation-triangle',
            'form-label label label-danger',
            'animated flipInX',
            'animated flipOutX',
            'Network error. Please try again.',
            ''
        );
    }).always(function() {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Submit exit via AJAX
 */
function submitPersonalExit() {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    const form = document.getElementById('exitForm');
    const formData = new FormData(form);
    formData.append('page', 'update_personal_exit');
    
    // Get method_type from hidden field
    const methodTypeInput = document.getElementById('actionMethodType');
    const methodType = methodTypeInput ? methodTypeInput.value : 'entry_first';
    formData.set('method_type', methodType);
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Recording...';
    submitBtn.disabled = true;
    
    // Determine success message based on method_type
    const successMessage = methodType === 'exit_first' 
        ? 'In time recorded successfully!' 
        : 'Out time recorded successfully!';

    $.post('ajax.php', Object.fromEntries(formData), function(response) {
        try {
            const result = JSON.parse(response);
            
            if (result.status === 'success' || result.status === 'sucess') {
                tostarnotify(
                    'top', 'right',
                    'fa fa-thumbs-up',
                    'form-label label label-success',
                    'animated flipInX',
                    'animated flipOutX',
                    successMessage,
                    ''
                );
                $('#exitModal').modal('hide');
                loadPersonalGatewayData();
            } else {
                tostarnotify(
                    'top', 'right',
                    'fa fa-exclamation-triangle',
                    'form-label label label-danger',
                    'animated flipInX',
                    'animated flipOutX',
                    result.message || 'Error recording. Please try again.',
                    ''
                );
            }
        } catch (e) {
            tostarnotify(
                'top', 'right',
                'fa fa-exclamation-triangle',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Error processing response. Please try again.',
                ''
            );
        }
    }).fail(function() {
        tostarnotify(
            'top', 'right',
            'fa fa-exclamation-triangle',
            'form-label label label-danger',
            'animated flipInX',
            'animated flipOutX',
            'Network error. Please try again.',
            ''
        );
    }).always(function() {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Wait for jQuery and initialize
 */
function waitForjQueryAndInitialize() {
    if (typeof jQuery !== 'undefined') {
        $(document).ready(function () {
            // Initialize select2
            $('.js-example-basic-single').select2();
            
            // Re-attach change handlers for select2 elements
            $('#employeeSelect').on('change', handleEmployeeSelectChange);
            $('#visitPurpose').on('change', toggleOtherPurpose);
            
            // Method type change handler
            $('input[name="method_type"]').on('change', handleMethodTypeChange);
            
            // Load all data on page load
            loadPersonalGatewayData();

            // Update time every minute
            setInterval(function () {
                const now = new Date();
                const timeString = now.toTimeString().slice(0, 5);
                const inTimeInput = document.getElementById('inTime');
                if (inTimeInput && !inTimeInput.value) {
                    inTimeInput.value = timeString;
                }
            }, 60000);
            
            $('[data-toggle="tooltip"]').tooltip();
        });
    } else {
        setTimeout(waitForjQueryAndInitialize, 100);
    }
}

/**
 * Initialize modal handlers
 */
function initializeModalHandlers() {
    if (typeof jQuery !== 'undefined') {
        $('#exitModal').on('show.bs.modal', function () {
            const now = new Date();
            const timeString = now.toTimeString().slice(0, 5);
            const exitTimeInput = document.querySelector('input[name="exit_time"]');
            if (exitTimeInput) {
                exitTimeInput.value = timeString;
            }
        });
    } else {
        setTimeout(initializeModalHandlers, 100);
    }
}

/**
 * Initialize all event listeners
 */
function initPersonalGatewayEventListeners() {
    // Initialize PHP data
    initPersonalGatewayData();
    
    // Event delegation for dynamically added buttons
    document.addEventListener('click', function(e) {
        // Record action button handler (new - supports method_type)
        if (e.target.closest('.record-action-btn')) {
            const btn = e.target.closest('.record-action-btn');
            const entryId = btn.dataset.entryId;
            const employeeName = btn.dataset.employeeName;
            const methodType = btn.dataset.methodType || 'entry_first';
            recordAction(entryId, employeeName, methodType);
        }
        
        // Record exit button handler (legacy support)
        if (e.target.closest('.record-exit-btn')) {
            const btn = e.target.closest('.record-exit-btn');
            const entryId = btn.dataset.entryId;
            const employeeName = btn.dataset.employeeName;
            const methodType = btn.dataset.methodType || 'entry_first';
            recordAction(entryId, employeeName, methodType);
        }
        
        // Print slip button handler
        if (e.target.closest('.print-person-slip-btn')) {
            const btn = e.target.closest('.print-person-slip-btn');
            const entryId = btn.dataset.entryId;
            printPersonSlip(entryId);
        }
    });
    
    // Close print modal button handler
    const closePrintModalBtns = document.querySelectorAll('.close-print-modal-btn');
    closePrintModalBtns.forEach(btn => {
        btn.addEventListener('click', closePrintModal);
    });
    
    // Print slip button handler
    const printSlipBtn = document.querySelector('.print-slip-btn');
    if (printSlipBtn) {
        printSlipBtn.addEventListener('click', printSlip);
    }
    
    // Personal entry form submit handler
    const personalEntryForm = document.getElementById('personalEntryForm');
    if (personalEntryForm) {
        personalEntryForm.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const employeeId = document.getElementById('employeeSelect').value;
            const visitPurpose = document.getElementById('visitPurpose').value;
            const location = document.querySelector('select[name="location"]').value;

            if (!employeeId || !visitPurpose || !location) {
                alert('Please fill in all required fields.');
                return false;
            }

            if (visitPurpose === 'other') {
                const otherPurpose = document.getElementById('otherPurpose').value.trim();
                if (!otherPurpose) {
                    alert('Please specify the purpose.');
                    document.getElementById('otherPurpose').focus();
                    return false;
                }
            }

            submitPersonalEntry();
        });
    }
    
    // Exit form submit handler
    const exitForm = document.getElementById('exitForm');
    if (exitForm) {
        exitForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitPersonalExit();
        });
    }
    
    // Auto-refresh every 2 minutes
    setInterval(function () {
        if (typeof jQuery !== 'undefined' && !$('.modal').hasClass('show')) {
            loadPersonalGatewayData();
        }
    }, 120000);
    
    // Start waiting for jQuery
    waitForjQueryAndInitialize();
    
    // Start initializing modal handlers
    initializeModalHandlers();
}

/**
 * Close the exit modal
 */
function closeExitModal() {
    if (typeof jQuery !== 'undefined') {
        $('#exitModal').modal('hide');
    } else {
        const modal = document.getElementById('exitModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initPersonalGatewayEventListeners);
