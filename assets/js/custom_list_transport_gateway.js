/**
 * Transport Gateway List - JavaScript Functions
 * Page: custom_list_transport_gateway.php
 */

// Session variables (will be set from PHP data)
let companyName = 'Company Name';
let companyLogo = '';

/**
 * Initialize session data from PHP
 */
function initTransportGatewayListData() {
    const dataContainer = document.querySelector('.transport-gateway-list-php-data');
    if (dataContainer) {
        companyName = dataContainer.dataset.companyName || 'Company Name';
        companyLogo = dataContainer.dataset.companyLogo || '';
    }
}

/**
 * Print vehicle slip function
 * @param {number} vehicleId - Vehicle ID
 */
function printVehicleSlip(vehicleId) {
    const tableRows = document.querySelectorAll('.vehicle-gateway-table tbody tr');
    let vehicleData = null;
    
    for (let row of tableRows) {
        const printBtn = row.querySelector('.print-slip-btn[data-vehicle-id="' + vehicleId + '"]');
        if (printBtn) {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 13) {
                vehicleData = {
                    vehicle_number: cells[1].textContent.trim(),
                    vehicle_type: cells[2].textContent.trim(),
                    driver_name: cells[3].textContent.trim(),
                    driver_contact: cells[4].textContent.trim(),
                    visit_purpose: cells[5].textContent.split('\n')[0].trim(),
                    visiting_person: cells[5].textContent.includes('To:') ? cells[5].textContent.split('To:')[1].trim() : '',
                    location: cells[6].textContent.trim(),
                    method_type: cells[7].textContent.includes('Exit First') ? 'exit_first' : 'entry_first',
                    entry_date: cells[8].textContent.trim(),
                    entry_time: cells[9].textContent.includes('Pending') ? '' : cells[9].textContent.trim(),
                    exit_time: cells[10].textContent.includes('Pending') ? '' : cells[10].textContent.trim()
                };
                break;
            }
        }
    }
    
    if (vehicleData) {
        showPrintSlip(vehicleData);
    } else {
        alert('Vehicle data not found for printing.');
    }
}

/**
 * Show print slip with vehicle data
 * @param {Object} vehicleData - Vehicle data object
 */
function showPrintSlip(vehicleData) {
    const methodLabel = vehicleData.method_type === 'exit_first' ? 'Exit First' : 'Entry First';
    
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
                    <div class="left-info"><strong>Date:</strong> ${vehicleData.entry_date}</div>
                    <div class="right-info"><strong>Visitor:</strong> ${vehicleData.driver_name}</div>
                </div>
                <div class="info-row">
                    <div class="phone-info"><strong>Phone:</strong> ${vehicleData.driver_contact || 'N/A'}</div>
                </div>
                <div class="info-row">
                    <div class="left-info"><strong>Vehicle:</strong> ${vehicleData.vehicle_number}</div>
                    <div class="right-info"><strong>Purpose:</strong> ${vehicleData.visit_purpose}</div>
                </div>
                <div class="info-row">
                    <div class="left-info"><strong>Location:</strong> ${vehicleData.location || 'N/A'}</div>
                    <div class="right-info"><strong>Method:</strong> ${methodLabel}</div>
                </div>
                <div class="info-row">
                    <div class="left-info"><strong>In Time:</strong> ${vehicleData.entry_time || '___________'}</div>
                    <div class="right-info"><strong>Out Time:</strong> ${vehicleData.exit_time || '___________'}</div>
                </div>
            </div>
            <div class="pass-footer">
                <small>Vehicle Gate Pass</small>
            </div>
        </div>
        
        <style>
            .gate-pass-container { border: 2px solid #000; padding: 10px; max-width: 300px; margin: 0 auto; font-family: Arial, sans-serif; font-size: 11px; background: white; }
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
        </style>
    `;
    
    document.getElementById('printSlipContent').innerHTML = slipContent;
    openModal('printSlipModal');
}

/**
 * Record action function - handles both entry and exit based on method_type
 * @param {number} vehicleId - Vehicle ID
 * @param {string} vehicleNumber - Vehicle number
 * @param {string} methodType - Method type (entry_first or exit_first)
 */
function recordAction(vehicleId, vehicleNumber, methodType) {
    document.getElementById('exitVehicleId').value = vehicleId;
    document.getElementById('exitVehicleNumber').textContent = vehicleNumber;
    document.getElementById('actionMethodType').value = methodType;
    
    const modalTitle = document.getElementById('actionModalTitle');
    const alertText = document.getElementById('actionAlertText');
    const submitBtn = document.getElementById('actionSubmitBtn');
    const submitText = document.getElementById('actionSubmitText');
    
    if (methodType === 'exit_first') {
        modalTitle.innerHTML = '<i class="ti-import text-success"></i> Record In Time';
        alertText.textContent = 'Recording In time for vehicle:';
        submitBtn.className = 'btn btn-success waves-effect waves-light';
        submitText.textContent = 'Record In';
    } else {
        modalTitle.innerHTML = '<i class="ti-export text-danger"></i> Record Out Time';
        alertText.textContent = 'Recording Out time for vehicle:';
        submitBtn.className = 'btn btn-danger waves-effect waves-light';
        submitText.textContent = 'Record Out';
    }
    
    openModal('exitModal');
}

/**
 * Keep old function for backward compatibility
 */
function recordExit(vehicleId, vehicleNumber) {
    recordAction(vehicleId, vehicleNumber, 'entry_first');
}

/**
 * Open modal helper function
 * @param {string} modalId - Modal element ID
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        
        let backdrop = document.querySelector('.modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    }
}

/**
 * Close modal helper function
 * @param {string} modalId - Modal element ID
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
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

/**
 * Close print modal
 */
function closePrintModal() {
    closeModal('printSlipModal');
}

/**
 * Close exit modal
 */
function closeExitModal() {
    closeModal('exitModal');
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
                <title>Vehicle Gate Pass</title>
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
 * Submit vehicle exit via AJAX
 */
function submitVehicleExit() {
    const form = document.getElementById('exitForm');
    const formData = new FormData(form);
    formData.append('page', 'update_vehicle_exit');
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Recording...';
    submitBtn.disabled = true;
    
    fetch('ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success' || result.status === 'sucess') {
            closeExitModal();
            setTimeout(() => { refreshVehicleTable(); }, 500);
        } else {
            alert(result.message || 'Error recording. Please try again.');
        }
    })
    .catch(error => {
        alert('Network error. Please try again.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

/**
 * Refresh vehicle table via AJAX
 */
function refreshVehicleTable() {
    const tableBody = document.querySelector('.vehicle-gateway-table tbody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '<tr><td colspan="14" class="text-center"><i class="fa fa-spinner fa-spin"></i> Refreshing data...</td></tr>';
    
    const formData = new FormData();
    const fromDate = document.querySelector('input[name="from"]');
    const toDate = document.querySelector('input[name="to"]');
    const pickDate = document.querySelector('input[name="pick_date"]');
    const shortcodeDate = document.querySelector('input[name="shortcode_date"]');
    
    if (fromDate) formData.append('from', fromDate.value);
    if (toDate) formData.append('to', toDate.value);
    if (pickDate) formData.append('pick_date', pickDate.value);
    if (shortcodeDate) formData.append('shortcode_date', shortcodeDate.value);
    formData.append('applyform', 'apply');
    formData.append('page', 'refresh_vehicle_table');
    
    fetch('ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTableBody = doc.querySelector('table tbody');
        
        if (newTableBody) {
            tableBody.innerHTML = newTableBody.innerHTML;
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error refreshing table:', error);
        window.location.reload();
    });
}

/**
 * Initialize all event listeners
 */
function initTransportGatewayListEventListeners() {
    // Initialize PHP data
    initTransportGatewayListData();
    
    // Exit form submit handler
    const exitForm = document.getElementById('exitForm');
    if (exitForm) {
        exitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitVehicleExit();
        });
    }
    
    // Event delegation for print slip buttons
    document.addEventListener('click', function(e) {
        const printBtn = e.target.closest('.print-slip-btn');
        if (printBtn) {
            const vehicleId = printBtn.dataset.vehicleId;
            if (vehicleId) {
                printVehicleSlip(parseInt(vehicleId));
            }
        }
        
        const recordBtn = e.target.closest('.record-action-btn');
        if (recordBtn) {
            const vehicleId = recordBtn.dataset.vehicleId;
            const vehicleNumber = recordBtn.dataset.vehicleNumber;
            const methodType = recordBtn.dataset.methodType;
            if (vehicleId && vehicleNumber) {
                recordAction(parseInt(vehicleId), vehicleNumber, methodType);
            }
        }
        
        // Close print modal button
        if (e.target.closest('.close-print-modal-btn')) {
            closePrintModal();
        }
        
        // Close exit modal button
        if (e.target.closest('.close-exit-modal-btn')) {
            closeExitModal();
        }
        
        // Print slip button in modal
        if (e.target.closest('.print-slip-btn-action')) {
            printSlip();
        }
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initTransportGatewayListEventListeners);
