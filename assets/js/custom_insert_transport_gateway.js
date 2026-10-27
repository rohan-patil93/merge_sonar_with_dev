/**
 * Transport Gateway - JavaScript Functions
 * Page: custom_insert_transport_gateway.php
 */

// Additional persons array
let additionalPersons = [];

// Track whether vehicle was found in lookup
let vehicleFoundInLookup = false;

// Store original vehicle data for search/filtering
let pendingVehiclesData = [];
let missingVehiclesData = [];

// Session variables (will be set from PHP data)
let company_id = 0;
let user_id = 0;
let companyName = 'Company Name';
let companyLogo = '';
let empArray = {};

/**
 * Initialize session data from PHP
 * Called on page load with data from hidden div
 */
function initTransportGatewayData() {
    const dataContainer = document.querySelector('.transport-gateway-php-data');
    if (dataContainer) {
        company_id = parseInt(dataContainer.dataset.companyId) || 0;
        user_id = parseInt(dataContainer.dataset.userId) || 0;
        companyName = dataContainer.dataset.companyName || 'Company Name';
        companyLogo = dataContainer.dataset.companyLogo || '';
        try {
            empArray = dataContainer.dataset.employeeMap ? JSON.parse(dataContainer.dataset.employeeMap) : {};
        } catch (error) {
            empArray = {};
        }
    }
}

function getDriverDisplayName(driverName) {
    const rawValue = driverName == undefined || driverName == null ? '' : String(driverName).trim();
    if (rawValue == '') {
        return '-';
    }
    return empArray[rawValue] || rawValue;
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
 * Toggle vehicle type options (Demo vs Other types)
 * Triggered by: vehicleType select change
 */
function toggleVehicleTypeOptions() {
    const vehicleType = document.getElementById('vehicleType').value;
    const vehicleNumberInputDiv = document.getElementById('vehicleNumberInputDiv');
    const vehicleNumberSelectDiv = document.getElementById('vehicleNumberSelectDiv');
    const vehicleNumberMotorCycleDiv = document.getElementById('vehicleNumberMotorCycleDiv');
    const vehicleNumberInput = document.getElementById('vehicle_number');
    const vehicleNumberSelect = document.getElementById('vehicle_number_select');
    const otherRow = document.getElementById('otherVehicleTypeRow');
    const otherInput = document.getElementById('otherVehicleType');

    // Hide ownership row when vehicle type changes (will be shown again when vehicle is selected)
    hideVehicleOwnershipSelection();

    if (vehicleType === 'demo') {
        vehicleNumberInputDiv.style.display = 'none';
        vehicleNumberSelectDiv.style.display = 'block';
        vehicleNumberMotorCycleDiv.style.display = 'none';
        vehicleNumberMotorCycleDiv.required = false;
        vehicleNumberMotorCycleDiv.value = '';
        vehicleNumberInput.required = false;
        vehicleNumberInput.value = '';
        vehicleNumberSelect.required = true;
        if (typeof jQuery != 'undefined') {
            $('#vehicle_number_select').select2();
        }
        otherRow.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    } else if (vehicleType == 'motorcycle') {
        vehicleNumberInputDiv.style.display = 'none';
        vehicleNumberSelectDiv.style.display = 'none';
        vehicleNumberMotorCycleDiv.style.display = 'block';
        vehicleNumberMotorCycleDiv.required = true;
        vehicleNumberInput.required = false;
        vehicleNumberSelect.required = false;
        vehicleNumberSelect.value = '';
        otherRow.style.display = 'none';
        otherInput.required = false;
    }else if (vehicleType == 'other') {
        vehicleNumberMotorCycleDiv.style.display = 'none';
        vehicleNumberMotorCycleDiv.required = false;
        vehicleNumberMotorCycleDiv.value = '';
        vehicleNumberInputDiv.style.display = 'block';
        vehicleNumberSelectDiv.style.display = 'none';
        vehicleNumberInput.required = true;
        vehicleNumberSelect.required = false;
        vehicleNumberSelect.value = '';
        otherRow.style.display = 'block';
        otherInput.required = true;
    } else {
        vehicleNumberMotorCycleDiv.style.display = 'none';
        vehicleNumberMotorCycleDiv.required = false;
        vehicleNumberMotorCycleDiv.value = '';
        vehicleNumberInputDiv.style.display = 'block';
        vehicleNumberSelectDiv.style.display = 'none';
        vehicleNumberInput.required = true;
        vehicleNumberSelect.required = false;
        vehicleNumberSelect.value = '';
        otherRow.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}

/**
 * Handle demo vehicle select change
 * Triggered by: vehicle_number_select change
 */
function handleDemoVehicleChange() {
    const vehicleNumberSelect = document.getElementById('vehicle_number_select');
    const selectedValue = vehicleNumberSelect.value;
    
    if (selectedValue && selectedValue !== '') {
        // Show ownership selection when a demo vehicle is selected
        showVehicleOwnershipSelection();
        // Auto-select company vehicle for demo vehicles
        const companyVehicleRadio = document.getElementById('company_vehicle');
        if (companyVehicleRadio) {
            companyVehicleRadio.checked = true;
        }
    } else {
        // Hide ownership selection if no vehicle selected
        hideVehicleOwnershipSelection();
    }
}
/**
 * Toggle other location input visibility
 */
function toggleOtherLocation() {
    const locationSelect = document.getElementById('locationSelect');
    const otherLocationCol = document.getElementById('otherLocationCol');
    const otherLocationInput = document.getElementById('otherLocation');

    if (!locationSelect || !otherLocationCol || !otherLocationInput) {
        return;
    }

    if (locationSelect.value == '99999') {
        otherLocationCol.style.display = 'block';
        otherLocationInput.required = true;
    } else {
        otherLocationCol.style.display = 'none';
        otherLocationInput.required = false;
        otherLocationInput.value = '';
    }
}


/**
 * Toggle other purpose input visibility
 * Triggered by: visitPurpose select change
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
 * Toggle other vehicle type input
 * Triggered by: vehicleType select change
 */
function toggleOtherVehicleType() {
    const vehicleType = document.getElementById('vehicleType').value;
    const otherRow = document.getElementById('otherVehicleTypeRow');
    const otherInput = document.getElementById('otherVehicleType');

    if (vehicleType === 'other') {
        otherRow.style.display = 'block';
        otherInput.required = true;
    } else {
        otherRow.style.display = 'none';
        otherInput.required = false;
        otherInput.value = '';
    }
}

/**
 * Show add person form
 * Triggered by: showAddPersonBtn click
 */
function showAddPersonForm() {
    document.getElementById('newPersonInputRow').style.display = 'block';
    
    // Initialize select2 for the new person select if jQuery is available
    if (typeof jQuery !== 'undefined') {
        $('#newPersonSelect').select2();
    }
    
    document.getElementById('newPersonSelect').focus();
}

/**
 * Hide add person form
 */
function hideAddPersonForm() {
    document.getElementById('newPersonInputRow').style.display = 'none';
    
    // Reset the select and contact field
    const personSelect = document.getElementById('newPersonSelect');
    personSelect.value = '';
    if (typeof jQuery !== 'undefined') {
        $('#newPersonSelect').val('').trigger('change');
    }
    document.getElementById('newPersonContact').value = '';
}

/**
 * Handle person select change - auto-fill contact number
 * Triggered by: newPersonSelect change
 */
function handlePersonSelectChange() {
    const personSelect = document.getElementById('newPersonSelect');
    const contactInput = document.getElementById('newPersonContact');
    const selectedOption = personSelect.options[personSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        const mobile = selectedOption.dataset.mobile || '';
        contactInput.value = mobile;
    } else {
        contactInput.value = '';
    }
}

/**
 * Add person to the list
 * Triggered by: addPersonBtn click
 */
function addPerson() {
    const personSelect = document.getElementById('newPersonSelect');
    const contactInput = document.getElementById('newPersonContact');
    const selectedOption = personSelect.options[personSelect.selectedIndex];
    
    const userId = personSelect.value;
    const name = selectedOption ? (selectedOption.dataset.name || selectedOption.text) : '';
    const contact = contactInput.value.trim();

    if (!userId || !name) {
        tostarnotify(
            'top', 'right',
            'fa fa-exclamation-triangle',
            'form-label label label-warning',
            'animated flipInX',
            'animated flipOutX',
            'Please select a person to add.',
            ''
        );
        return;
    }

    // Check if person already added
    const alreadyAdded = additionalPersons.some(p => p.user_id === userId);
    if (alreadyAdded) {
        tostarnotify(
            'top', 'right',
            'fa fa-exclamation-triangle',
            'form-label label label-warning',
            'animated flipInX',
            'animated flipOutX',
            'This person is already added.',
            ''
        );
        return;
    }

    additionalPersons.push({ user_id: userId, name: name, contact: contact });
    document.getElementById('personDetailsJson').value = JSON.stringify(additionalPersons);
    renderAdditionalPersons();
    hideAddPersonForm();
}

/**
 * Remove person from the list
 * @param {number} index - Index of person to remove
 */
function removePerson(index) {
    additionalPersons.splice(index, 1);
    document.getElementById('personDetailsJson').value = JSON.stringify(additionalPersons);
    renderAdditionalPersons();
}

/**
 * Render additional persons list
 */
function renderAdditionalPersons() {
    const container = document.getElementById('additionalPersonsContainer');
    
    if (additionalPersons.length === 0) {
        container.innerHTML = '';
        return;
    }

    container.innerHTML = additionalPersons.map((person, index) => `
        <div class="d-flex align-items-center mb-2 person-row p-2 bg-light rounded" data-index="${index}">
            <span class="mx-1"><i class="ti-user text-primary"></i></span>
            <span class="flex-grow-1">${person.name} - ${person.contact}</span>
            <button type="button" class="btn btn-sm btn-warning remove-person-btn" data-index="${index}">
                <i class="ti-trash"></i>
            </button>
        </div>
    `).join('');
}

/**
 * Close print modal function
 */
function closePrintModal() {
    if (typeof jQuery !== 'undefined') {
        $('#printSlipModal').modal('hide');
        setTimeout(function() {
            window.location.reload();
        }, 200);
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
        window.location.reload();
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
                <title>Visitor Gate Pass</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 10px; 
                        font-size: 11px;
                    }
                    .gate-pass-container {
                        border: 2px solid #000;
                        padding: 10px;
                        max-width: 300px;
                        margin: 0 auto;
                        background: white;
                    }
                    .pass-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 8px;
                    }
                    .logo-section {
                        flex: 0 0 auto;
                    }
                    .company-logo {
                        max-width: 40px;
                        max-height: 40px;
                        object-fit: contain;
                    }
                    .logo-placeholder {
                        width: 40px;
                        height: 40px;
                        border: 1px solid #000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 8px;
                        font-weight: bold;
                    }
                    .company-section {
                        flex: 1;
                        text-align: right;
                    }
                    .company-section h4 {
                        margin: 0;
                        font-size: 12px;
                        font-weight: bold;
                    }
                    .header-divider {
                        border: none;
                        border-top: 1px solid #000;
                        margin: 8px 0;
                    }
                    .pass-content {
                        margin: 8px 0;
                    }
                    .info-row {
                        display: flex;
                        justify-content: space-between;
                        margin: 4px 0;
                        font-size: 10px;
                    }
                    .left-info, .right-info {
                        flex: 1;
                    }
                    .right-info {
                        text-align: right;
                    }
                    .phone-info, .full-info {
                        width: 100%;
                    }
                    .pass-footer {
                        text-align: center;
                        margin-top: 8px;
                        border-top: 1px solid #000;
                        padding-top: 4px;
                    }
                    .pass-footer small {
                        font-size: 9px;
                        font-weight: bold;
                    }
                    @media print { 
                        body { margin: 0; }
                        .gate-pass-container { margin: 0; }
                    }
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
 * Show print slip with vehicle data
 * @param {Object} vehicleData - Vehicle data object
 */
function showPrintSlip(vehicleData) {
    const methodType = vehicleData.method_type || 'entry_first';
    const methodLabel = methodType === 'exit_first' ? 'Exit First' : 'Entry First';
    
    // Determine In Time and Out Time based on method_type
    let inTime = vehicleData.entry_time || '';
    let outTime = vehicleData.exit_time || '';
    
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
                        <strong>Date:</strong> ${vehicleData.entry_date}
                    </div>
                    <div class="right-info">
                        <strong>Visitor:</strong> ${getDriverDisplayName(vehicleData.driver_name)}
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="phone-info">
                        <strong>Phone:</strong> ${vehicleData.driver_contact || 'N/A'}
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="left-info">
                        <strong>Vehicle:</strong> ${vehicleData.vehicle_number}
                    </div>
                    <div class="right-info">
                        <strong>Purpose:</strong> ${vehicleData.visit_purpose}
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="left-info">
                        <strong>Location:</strong> ${vehicleData.location || 'N/A'}
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
                <small>Vehicle Gate Pass</small>
            </div>
        </div>
    `;
    
    document.getElementById('printSlipContent').innerHTML = slipContent;
    $('#printSlipModal').modal('show');
}

/**
 * Record exit function
 * @param {number} vehicleId - Vehicle ID
 * @param {string} vehicleNumber - Vehicle number
 */
function recordExit(vehicleId, vehicleNumber) {
    recordAction(vehicleId, vehicleNumber, 'entry_first', 'exit');
}

/**
 * Record action function - handles both entry and exit based on action parameter
 * @param {number} vehicleId - Vehicle ID
 * @param {string} vehicleNumber - Vehicle number
 * @param {string} methodType - Method type (entry_first or exit_first)
 * @param {string} action - Action to record: 'entry' or 'exit'
 */
function recordAction(vehicleId, vehicleNumber, methodType, action) {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    document.getElementById('exitVehicleId').value = vehicleId;
    document.getElementById('exitVehicleNumber').textContent = vehicleNumber;
    
    // Set method_type in hidden field if it exists
    const methodTypeInput = document.getElementById('actionMethodType');
    if (methodTypeInput) {
        methodTypeInput.value = methodType;
    }
    
    // Update modal title and button based on action parameter
    const modalTitle = document.querySelector('#exitModal .modal-title');
    const submitBtn = document.querySelector('#exitModal button[type="submit"]');
    const alertText = document.querySelector('#exitModal .alert');
    
    if (action == 'exit') {
        // Recording Exit time
        if (modalTitle) modalTitle.innerHTML = '<i class="ti-export text-danger"></i> Record Exit Time';
        if (submitBtn) {
            submitBtn.className = 'btn btn-danger waves-effect waves-light';
            submitBtn.innerHTML = '<i class="ti-check"></i> Record Exit';
        }
        if (alertText) alertText.innerHTML = '<i class="ti-info-alt"></i> Recording exit time for vehicle: <strong id="exitVehicleNumber">' + vehicleNumber + '</strong>';
    } else {
        // Recording Entry/In time
        if (modalTitle) modalTitle.innerHTML = '<i class="ti-import text-success"></i> Record Entry Time';
        if (submitBtn) {
            submitBtn.className = 'btn btn-success waves-effect waves-light';
            submitBtn.innerHTML = '<i class="ti-check"></i> Record Entry';
        }
        if (alertText) alertText.innerHTML = '<i class="ti-info-alt"></i> Recording entry time for vehicle: <strong id="exitVehicleNumber">' + vehicleNumber + '</strong>';
    }

    // Load vehicle details including additional persons
    loadVehicleDetailsForExit(vehicleId);
    
    $('#exitModal').modal('show');
}

function loadVehicleDetailsForExit(vehicleId) { 
    const detailsCard = document.getElementById('exitVehicleDetails');
    if (detailsCard) {
        detailsCard.style.display = 'block';
        detailsCard.innerHTML = `
            <div class="card-body py-2 text-center">
                <i class="fa fa-spinner fa-spin"></i> Loading vehicle details...
            </div>
        `;
    }
     
    $.post('ajax.php', {
        page: 'get_vehicle_details',
        vehicle_id: vehicleId
    }, function(response) {
        try {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                displayVehicleDetailsInExitModal(result.data);
            } else {
                hideVehicleDetailsInExitModal();
            }
        } catch (e) {
            console.error('Error parsing vehicle details response:', e);
            hideVehicleDetailsInExitModal();
        }
    }).fail(function(xhr, status, error) {
        console.error('Failed to load vehicle details:', error);
        hideVehicleDetailsInExitModal();
    });
}
 
function displayVehicleDetailsInExitModal(vehicleData) {
    const detailsCard = document.getElementById('exitVehicleDetails');
    document.getElementById('meater_reading').value = 0.00;
    if (!detailsCard) return;
     
    let additionalPersonsHtml = '';
    if (vehicleData.person_details) {
        try {
            const persons = JSON.parse(vehicleData.person_details);
            if (persons && persons.length > 0) {
                const personList = persons.map(person => 
                    `<div class="mb-1">
                        <i class="ti-user"></i> ${person.name}
                        ${person.contact ? ` - ${person.contact}` : ''}
                    </div>`
                ).join('');
                
                additionalPersonsHtml = `
                    <div id="exitAdditionalPersons">
                        <hr class="my-2">
                        <p class="mb-1"><strong><i class="ti-user"></i> Additional Persons (${persons.length}):</strong></p>
                        <div id="exitAdditionalPersonsList" class="text-muted">
                            ${personList}
                        </div>
                    </div>
                `;
            }
        } catch (e) {
            console.error('Error parsing person_details:', e);
        }
    }
    var location = vehicleData.location;
    if(location == '99999'){
        location = vehicleData.other_location;
    }
    
    detailsCard.innerHTML = `
        <div class="card-body py-2">
            <h6 class="card-title mb-2"><i class="ti-car"></i> Vehicle Details</h6>
            <div class="row">
                <div class="col-md-6">
                    ${(vehicleData.driver_name || vehicleData.driver_contact) ? `
                        <p class="mb-1"><strong>Employee Name:</strong> ${getDriverDisplayName(vehicleData.driver_name)}</p>
                        <p class="mb-1"><strong>Contact:</strong> ${vehicleData.driver_contact || '-'}</p>
                    ` : ''}

                    ${(vehicleData.emp_list && vehicleData.emp_list.length > 0) ? `
                        <p class="mb-1"><strong>Employees:</strong></p>
                        ${vehicleData.emp_list.map(emp => `
                            <div class="mb-1">- ${emp.display_name}</div>
                        `).join('')}
                    ` : ''}

                    <p class="mb-1"><strong>Reading:</strong> ${vehicleData.vehicle_exit_reading || vehicleData.vehicle_entry_reading}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Purpose:</strong> ${vehicleData.visit_purpose || '-'}</p>
                    <p class="mb-1"><strong>Location:</strong> ${location || '-'}</p>
                    
                </div>
            </div>
            ${additionalPersonsHtml}
        </div>
    `;
    
    detailsCard.style.display = 'block';
    var reading = vehicleData.vehicle_exit_reading ?? vehicleData.vehicle_entry_reading ?? '';
    document.getElementById('meater_reading').value = reading;
}

function hideVehicleDetailsInExitModal() {
    const detailsCard = document.getElementById('exitVehicleDetails');
    if (detailsCard) {
        detailsCard.style.display = 'none';
    }
}
 



/**
 * Print vehicle slip function
 * @param {number} vehicleId - Vehicle ID
 */
function printVehicleSlip(vehicleId) {
    console.log('printVehicleSlip called with vehicleId:', vehicleId);
    
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not available');
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    console.log('Making AJAX request to get_vehicle_details');
    $.post('ajax.php', {page: 'get_vehicle_details', vehicle_id: vehicleId}, function(response) {
        console.log('get_vehicle_details response:', response);
        try {
            const result = JSON.parse(response);
            console.log('Parsed result:', result);
            if (result.status === 'success') {
                showPrintSlip(result.data);
            } else {
                console.error('Error in response:', result);
                alert('Error loading vehicle details for printing: ' + (result.message || 'Unknown error'));
            }
        } catch (e) {
            console.error('Error parsing response:', e);
            console.error('Raw response was:', response);
            alert('Error processing vehicle details.');
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX request failed:', error, status, xhr);
        alert('Network error loading vehicle details.');
    });
}

/**
 * Load all vehicle gateway data
 */
function loadVehicleGatewayData() {
    let pendingCount = 0;
    let missingCount = 0;
    let loadedCount = 0;
    
    // Helper function to check if both loads are complete
    const checkAndUpdateCounts = function() {
        loadedCount++;
        if (loadedCount === 2) {
            updateTabCounts(pendingCount, missingCount);
        }
    };
    
    // Load pending vehicles
    if (typeof jQuery !== 'undefined') {
        $.post('ajax.php', {page: 'get_pending_vehicles'}, function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    pendingCount = result.data.length;
                    pendingVehiclesData = result.data;  // Store original data
                    updateVehiclesTable('#pendingVehiclesTable', result.data, 'pending');
                } else {
                    showErrorInTable('#pendingVehiclesTable', 'Error loading vehicles');
                }
            } catch (e) {
                showErrorInTable('#pendingVehiclesTable', 'Error loading vehicles');
            }
            checkAndUpdateCounts();
        }).fail(function(xhr, status, error) {
            showErrorInTable('#pendingVehiclesTable', 'Network error loading vehicles');
            checkAndUpdateCounts();
        });
    } else {
        checkAndUpdateCounts();
    }
    
    // Load missing vehicles
    if (typeof jQuery !== 'undefined') {
        $.post('ajax.php', {page: 'get_missing_vehicles'}, function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    missingCount = result.data.length;
                    missingVehiclesData = result.data;  // Store original data
                    updateVehiclesTable('#missingVehiclesTable', result.data, 'initial');
                } else {
                    showErrorInTable('#missingVehiclesTable', 'Error loading vehicles');
                }
            } catch (e) {
                showErrorInTable('#missingVehiclesTable', 'Error loading vehicles');
            }
            checkAndUpdateCounts();
        }).fail(function(xhr, status, error) {
            showErrorInTable('#missingVehiclesTable', 'Network error loading vehicles');
            checkAndUpdateCounts();
        });
    } else {
        checkAndUpdateCounts();
    }
    
    loadVehicleStatistics();
    loadRecentActivity();
}

/**
 * Load vehicle statistics
 */
function loadVehicleStatistics() {
    if (typeof jQuery === 'undefined') {
        return;
    }
    $.post('ajax.php', {page: 'get_vehicle_statistics'}, function(response) {
        try {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                updateStatisticsCards(result.data);
            }
        } catch (e) {
            // Silent error handling
        }
    }).fail(function(xhr, status, error) {
        // Silent error handling
    });
}

/**
 * Load recent activity
 */
function loadRecentActivity() {
    if (typeof jQuery === 'undefined') {
        return;
    }
    $.post('ajax.php', {page: 'get_recent_activity'}, function(response) {
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
            console.error('Error parsing recent activity response:', e);
            showErrorInTable('#recentActivityTable', 'Error loading recent activity');
        }
    }).fail(function(xhr, status, error) {
        showErrorInTable('#recentActivityTable', 'Network error loading recent activity');
    });
}

/**
 * Show error in table
 * @param {string} tableSelector - Table selector
 * @param {string} message - Error message
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
 * Lookup vehicle details
 * @param {string} vehicleNumber - Vehicle number to lookup
 */
function lookupVehicleDetails(vehicleNumber) {
    if (typeof jQuery === 'undefined') {
        return;
    }
    
    const vehicleNumberInput = document.querySelector('input[name="vehicle_number"]');
    const originalPlaceholder = vehicleNumberInput.placeholder;
    vehicleNumberInput.placeholder = 'Looking up vehicle...';
    vehicleNumberInput.disabled = true;
    
    $.post('ajax.php', {
        page: 'lookup_vehicle_details',
        vehicle_number: vehicleNumber
    }, function(response) {
        try {
            const result = JSON.parse(response);
            if (result.status === 'success' && result.data) {
                fillVehicleDetails(result.data);
            } else {
                vehicleFoundInLookup = false;
                showVehicleOwnershipSelection();
            }
        } catch (e) {
            console.error('Error parsing vehicle lookup response:', e);
            console.error('Raw response:', response);
            vehicleFoundInLookup = false;
            showVehicleOwnershipSelection();
        }
    }).fail(function(xhr, status, error) {
        console.error('Vehicle lookup failed:', error);
        vehicleFoundInLookup = false;
        showVehicleOwnershipSelection();
    }).always(function() {
        vehicleNumberInput.placeholder = originalPlaceholder;
        vehicleNumberInput.disabled = false;
    });
}

/**
 * Fill vehicle details from existing data
 * @param {Object} vehicleData - Vehicle data object
 */
function fillVehicleDetails(vehicleData) {
    vehicleFoundInLookup = true;

    const driverField = document.querySelector('select[name="driver_name"]');
    const setDriverFieldValue = function(value) {
        if (!driverField || value === undefined || value === null || value === '') {
            return;
        }

        const normalizedValue = String(value).trim().toLowerCase();
        let matched = false;

        Array.from(driverField.options).forEach(option => {
            const optionValue = (option.value || '').trim().toLowerCase();
            const optionText = (option.text || '').trim().toLowerCase();
            if (optionValue === normalizedValue || optionText === normalizedValue || optionText.includes(normalizedValue)) {
                option.selected = true;
                matched = true;
            }
        });

        if (matched && typeof jQuery !== 'undefined') {
            $(driverField).trigger('change');
        }
    };
    
    if (vehicleData.vehicle_name) {
        setDriverFieldValue(vehicleData.vehicle_name);
    }
    if (vehicleData.vehicle_type) {
        const vehicleTypeSelect = document.querySelector('select[name="vehicle_type"]');
        vehicleTypeSelect.value = vehicleData.vehicle_type;
        if (typeof jQuery !== 'undefined') {
            $(vehicleTypeSelect).trigger('change');
        }
    }
    if (vehicleData.owner_name) {
        setDriverFieldValue(vehicleData.owner_name);
    }
    if (vehicleData.owner_contract) {
        document.querySelector('input[name="driver_contact"]').value = vehicleData.owner_contract;
    }
    
    showVehicleOwnershipSelection();
    document.getElementById('company_vehicle').checked = true;
    
    if (typeof tostarnotify !== 'undefined') {
        tostarnotify(
            'top', 'right',
            'fa fa-info-circle',
            'form-label label label-info',
            'animated flipInX',
            'animated flipOutX',
            'Vehicle details found and filled from company records',
            ''
        );
    }
}

/**
 * Show vehicle ownership selection
 */
function showVehicleOwnershipSelection() {
    const ownershipRow = document.getElementById('vehicleOwnershipRow');
    if (ownershipRow) {
        ownershipRow.style.display = 'block';
        document.querySelectorAll('input[name="vehicle_ownership"]').forEach(radio => {
            radio.required = true;
        });
    }
}

/**
 * Hide vehicle ownership selection
 */
function hideVehicleOwnershipSelection() {
    const ownershipRow = document.getElementById('vehicleOwnershipRow');
    if (ownershipRow) {
        ownershipRow.style.display = 'none';
        document.querySelectorAll('input[name="vehicle_ownership"]').forEach(radio => {
            radio.checked = false;
            radio.required = false;
        });
    }
}

/**
 * Submit vehicle entry via AJAX
 */
function submitVehicleEntry() {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    const form = document.getElementById('vehicleEntryForm');
    const formData = new FormData(form);
    formData.append('page', 'insert_vehicle_entry');
    
    // Explicitly get and set method_type - IMPORTANT: must be set before sending
    const methodTypeRadio = document.querySelector('input[name="method_type"]:checked');
    const methodType = methodTypeRadio ? methodTypeRadio.value : 'entry_first';
    formData.set('method_type', methodType);
    
    const vehicleType = formData.get('vehicle_type');
    if (vehicleType == 'demo') {
        const demoVehicleNumber = document.getElementById('vehicle_number_select').value;
        formData.set('vehicle_number', demoVehicleNumber);
    }
    if (vehicleType == 'motorcycle') {
        var motorCycleVehicleNumber = document.getElementById('motor_cycle_vehicle_number').value;
        formData.set('vehicle_number', motorCycleVehicleNumber);
    }
    
    formData.set('person_details', document.getElementById('personDetailsJson').value);
    
    if (company_id) {
        formData.append('company_id', company_id);
    }
    if (user_id) {
        formData.append('user_id', user_id);
    }
    
    formData.append('vehicle_found_in_lookup', vehicleFoundInLookup);

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Recording...';
    submitBtn.disabled = true;

    // Determine success message based on method type
    const successMessage = methodType === 'exit_first' 
        ? 'Vehicle exit recorded successfully! Waiting for return.' 
        : 'Vehicle entry recorded successfully!';

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
                
                const vehicleData = Object.fromEntries(formData);
                vehicleData.entry_date = vehicleData.entry_date || new Date().toISOString().split('T')[0];
                
                // Set in_time and out_time based on method_type for print slip
                const timeValue = vehicleData.entry_time || new Date().toTimeString().slice(0, 5);
                if (methodType === 'exit_first') {
                    vehicleData.exit_time = timeValue;
                    vehicleData.entry_time = '';
                } else {
                    vehicleData.entry_time = timeValue;
                    vehicleData.exit_time = '';
                }
                vehicleData.method_type = methodType;
                
                showPrintSlip(vehicleData);
                
                form.reset();
                additionalPersons = [];
                document.getElementById('personDetailsJson').value = '[]';
                renderAdditionalPersons();
                hideAddPersonForm();
                toggleVehicleTypeOptions();
                loadVehicleGatewayData();
                toggleOtherLocation();
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
    }).fail(function(xhr, status, error) {
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
 * Submit vehicle exit via AJAX
 */
function submitVehicleExit() {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    var meater_reading = document.getElementById('meater_reading');
    if(!meater_reading){
        tostarnotify(
                    'top', 'right',
                    'fa fa-thumbs-down',
                    'form-label label label-danger',
                    'animated flipInX',
                    'animated flipOutX',
                    'please add Vehicle Reading',
                    ''
                );
        return;
    }
    const form = document.getElementById('exitForm');
    const formData = new FormData(form);
    formData.append('page', 'update_vehicle_exit');
    
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
        ? 'Vehicle In time recorded successfully!' 
        : 'Vehicle Out time recorded successfully!';

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
                loadVehicleGatewayData();
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
    }).fail(function(xhr, status, error) {
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
 * Update tab counts
 * @param {number} pendingCount - Count of pending vehicles
 * @param {number} missingCount - Count of missing vehicles
 */
function updateTabCounts(pendingCount, missingCount) {
    const pendingBadge = document.getElementById('pending-vehicles-count');
    const missingBadge = document.getElementById('missing-vehicles-count');
    
    if (pendingBadge) {
        pendingBadge.textContent = pendingCount;
    }
    if (missingBadge) {
        missingBadge.textContent = missingCount;
    }
}

/**
 * Update vehicle count badge
 * @param {number} count - Vehicle count
 */
function updateVehicleCountBadge(count) {
    const badge = document.getElementById('vehicles-count-badge');
    if (badge) {
        badge.textContent = count + ' vehicle' + (count !== 1 ? 's' : '');
    }
}

/**
 * Update vehicles table
 * @param {string} tableSelector - Table selector
 * @param {Array} vehicles - Array of vehicle objects
 * @param {string} actionMode - Button mode: pending or initial
 */
function updateVehiclesTable(tableSelector, vehicles, actionMode) {
    const tbody = document.querySelector(tableSelector + ' tbody');
    if (!tbody) return;

    if (vehicles.length === 0) {
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
        tbody.innerHTML = vehicles.map(vehicle => {
            const methodType = vehicle.method_type || 'entry_first';
            const isExitFirst = methodType == 'exit_first';
            const vehicleType = vehicle.vehicle_type || '';
            const visitPurpose = vehicle.visit_purpose || '';
            
            // Determine which timestamp to show based on method_type
            let timeDisplay = '';
            if (isExitFirst && vehicle.exit_timestamp) {
                timeDisplay = new Date(vehicle.exit_timestamp).toLocaleDateString('en-US', {month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true});
            } else if (!isExitFirst && vehicle.entry_timestamp) {
                timeDisplay = new Date(vehicle.entry_timestamp).toLocaleDateString('en-US', {month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true});
            }
            
            const isInitialAction = actionMode == 'initial';
            const btnClass = isExitFirst
                ? (isInitialAction ? 'btn-danger' : 'btn-success')
                : (isInitialAction ? 'btn-success' : 'btn-danger');
            const btnIcon = isExitFirst
                ? (isInitialAction ? 'ti-export' : 'ti-import')
                : (isInitialAction ? 'ti-import' : 'ti-export');
            const btnText = isExitFirst
                ? (isInitialAction ? 'Exit' : 'In')
                : (isInitialAction ? 'In' : 'Exit');
            const action = (isInitialAction && isExitFirst) || (!isInitialAction && !isExitFirst) ? 'exit' : 'entry';
            
            // Status badge logic: depends on action mode (initial vs pending)
            let statusBadge;
            if (isInitialAction) {
                // Tab 2: Both times missing - status based on method_type (starting location)
                statusBadge = isExitFirst
                    ? '<span class="badge badge-success" style="font-size:9px;"><i class="ti-import"></i> Inside</span>'
                    : '<span class="badge badge-danger" style="font-size:9px;"><i class="ti-export"></i> Outside</span>';
            } else {
                // Tab 1: One time missing - status based on which time is recorded
                const exitTimeRecorded = vehicle.exit_time && vehicle.exit_time !== '00:00:00';
                const entryTimeRecorded = vehicle.entry_time && vehicle.entry_time !== '00:00:00';
                
                if (isExitFirst) {
                    // exit_first: check if exit is recorded
                    statusBadge = exitTimeRecorded
                        ? '<span class="badge badge-danger" style="font-size:9px;"><i class="ti-export"></i> Outside</span>'
                        : '<span class="badge badge-success" style="font-size:9px;"><i class="ti-import"></i> Inside</span>';
                } else {
                    // entry_first: check if entry is recorded
                    statusBadge = entryTimeRecorded
                        ? '<span class="badge badge-success" style="font-size:9px;"><i class="ti-import"></i> Inside</span>'
                        : '<span class="badge badge-danger" style="font-size:9px;"><i class="ti-export"></i> Outside</span>';
                }
            }
            var location = vehicle.location;
            if(location == '99999'){
                location = vehicle.other_location
            }
            return `
            <tr>
                <td>
                    <div class="d-inline-block align-middle">
                        <div class="d-inline-block">
                            <h6 class="m-b-0">${vehicle.vehicle_number} ${statusBadge}</h6>
                            <h6 class="m-b-0">${vehicle.display_name}</h6>
                            <p class="m-b-0 text-muted">
                                ${(vehicleType ? vehicleType.charAt(0).toUpperCase() + vehicleType.slice(1) : 'Unknown')} •
                                ${(visitPurpose ? visitPurpose.charAt(0).toUpperCase() + visitPurpose.slice(1) : 'Unknown')}
                            </p>
                            <p class="m-b-0 text-muted">
                                <small>${getDriverDisplayName(vehicle.driver_name)}</small>
                            </p>
                            <p class="m-b-0 text-muted">
                                <small><i class="ti-location-pin"></i> ${location || 'Location not specified'}</small>
                            </p>
                            <p class="m-b-0 text-muted">
                                <small>${timeDisplay}</small>
                            </p>
                        </div>
                    </div>
                </td>
                <td class="d-flex flex-column align-items-center">
                    <div class="btn-group" role="group"> 
                        <button type="button" class="btn btn-sm ${btnClass} btn-round record-action-btn"
                            data-vehicle-id="${vehicle.id}" data-vehicle-number="${vehicle.vehicle_number}" data-method-type="${methodType}" data-action="${action}">
                            <i class="${btnIcon}"></i> ${btnText}
                        </button>
                    </div>
                    ${isInitialAction ? `
                    <div class="btn-group mt-1" role="group">
                        <button type="button" class="btn btn-sm btn-danger btn-round cancel-vehicle-entry-btn"
                            data-vehicle-id="${vehicle.id}" data-vehicle-number="${vehicle.vehicle_number}">
                            <i class="ti-na"></i> Cancel
                        </button>
                    </div>` : ''}
                </td>
            </tr>
        `}).join('');
    }
}

/**
 * Update statistics cards
 * @param {Object} stats - Statistics object
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
    
    updateVehicleCountBadge(stats.current_count || 0);
}

/**
 * Format date only
 * @param {string} timestamp - Timestamp
 * @param {string} date - Date string
 * @returns {string} Formatted date
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
        
        if (isNaN(dateToFormat.getTime())) {
            return 'N/A';
        }
        
        return dateToFormat.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    } catch (e) {
        console.error('Error formatting date:', e);
        return 'N/A';
    }
}

/**
 * Format date and time
 * @param {string} timestamp - Timestamp
 * @param {string} date - Date string
 * @param {string} time - Time string
 * @returns {string} Formatted date/time
 */
function formatDateTime(timestamp, date, time) {
    try {
        if (!timestamp && !date && !time) {
            return 'Pending';
        }
        
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
        
        if (isNaN(dateToFormat.getTime())) {
            return 'N/A';
        }
        
        return dateToFormat.toLocaleTimeString('en-US', {
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true
        });
    } catch (e) {
        console.error('Error formatting date/time:', e);
        return 'N/A';
    }
}

/**
 * Update recent activity table
 * @param {Array} activities - Array of activity objects
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
        const entryTime = activity.entry_time === '00:00:00' ? null : activity.entry_time;
        const exitTime = activity.exit_time === '00:00:00' ? null : activity.exit_time;
        
        // Determine action badge based on method_type and completion status
        let actionBadge;
        if (methodType === 'exit_first') {
            // Exit first: show Exit badge initially, Entry badge when returned
            if (entryTime && exitTime) {
                actionBadge = '<span class="badge badge-success p-2"><i class="ti-import"></i> Entry</span>';
            } else {
                actionBadge = '<span class="badge badge-danger p-2"><i class="ti-export"></i> Exit</span>';
            }
        } else {
            // Entry first: show Entry badge initially, Exit badge when exited
            if (exitTime) {
                actionBadge = '<span class="badge badge-danger p-2"><i class="ti-export"></i> Exit</span>';
            } else {
                actionBadge = '<span class="badge badge-success p-2"><i class="ti-import"></i> Entry</span>';
            }
        }
        
        // Format In/Out times - show Pending for missing times
        const inTimeDisplay = entryTime ? formatDateTime(activity.entry_timestamp, activity.entry_date, entryTime) : 'Pending';
        const outTimeDisplay = exitTime ? formatDateTime(activity.exit_timestamp, activity.exit_date, exitTime) : 'Pending';
        var location = activity.location;
        if(location == '99999'){
            location = activity.other_location;
        }
        return `
        <tr>
            <td>
                <strong>${activity.vehicle_number || 'N/A'}</strong>
                <br><small class="text-muted">${(activity.vehicle_type || 'unknown').charAt(0).toUpperCase() + (activity.vehicle_type || 'unknown').slice(1)}</small>
                ${activity.location ? `<br><small class="text-muted"><i class="ti-location-pin"></i> ${location}</small>` : ''}
            </td>
            <td>${getDriverDisplayName(activity.driver_name)}</td>
            <td>${actionBadge}</td>
            <td>${(activity.visit_purpose || 'unknown').charAt(0).toUpperCase() + (activity.visit_purpose || 'unknown').slice(1)}</td>
            <td>
                <small>${formatDate(activity.entry_timestamp || activity.exit_timestamp, activity.entry_date || activity.exit_date)}</small>
            </td>
            <td>
                <small>
                    <strong>In:</strong> ${inTimeDisplay}<br>
                    <strong>Out:</strong> ${outTimeDisplay}
                </small>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-info btn-round print-vehicle-slip-btn"
                    data-vehicle-id="${activity.vehicle_id || activity.id}" title="Print Slip">
                    <i class="ti-printer"></i>
                </button>
            </td>
        </tr>
    `}).join('');
}


/**
 * Wait for jQuery and initialize
 */
function waitForjQueryAndInitialize() {
    if (typeof jQuery !== 'undefined') {
        $(document).ready(function () {
            // Initialize select2
            $('.js-example-basic-single').select2();
            
            // Re-attach change handlers for select2 elements after initialization
            $('#vehicleType').on('change', toggleVehicleTypeOptions);
            $('#visitPurpose').on('change', toggleOtherPurpose);
            $('#vehicle_number_select').on('change', handleDemoVehicleChange);
            $('#newPersonSelect').on('change', handlePersonSelectChange);
            $('#locationSelect').on('change', toggleOtherLocation);
            
            // Method type change handler
            $('input[name="method_type"]').on('change', handleMethodTypeChange);
            
            // Load all data on page load
            loadVehicleGatewayData();
            toggleOtherLocation();

            setInterval(function () {
                const now = new Date();
                const timeString = now.toTimeString().slice(0, 5);
                const entryTimeInput = document.querySelector('input[name="entry_time"]');
                if (entryTimeInput) {
                    entryTimeInput.value = timeString;
                }
            }, 60000);
     
            $('[data-toggle="tooltip"]').tooltip();
        });
    } else {
        setTimeout(waitForjQueryAndInitialize, 100);
    }
}

/**
 * Initialize modal handlers when jQuery is available
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
 * Initialize all event listeners using event delegation
 */
function initTransportGatewayEventListeners() {
    // Initialize PHP data
    initTransportGatewayData();
    
    // Has goods checkbox handler
    const hasGoodsCheckbox = document.getElementById('hasGoods');
    if (hasGoodsCheckbox) {
        hasGoodsCheckbox.addEventListener('change', function () {
            const goodsDetails = document.getElementById('goodsDetails');
            goodsDetails.style.display = this.checked ? 'block' : 'none';
        });
    }
    
    // Show add person button handler
    const showAddPersonBtn = document.getElementById('showAddPersonBtn');
    if (showAddPersonBtn) {
        showAddPersonBtn.addEventListener('click', showAddPersonForm);
    }
    
    // Add person button handler
    const addPersonBtn = document.querySelector('.add-person-btn');
    if (addPersonBtn) {
        addPersonBtn.addEventListener('click', addPerson);
    }
    
    // Event delegation for dynamically added remove person buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-person-btn')) {
            const btn = e.target.closest('.remove-person-btn');
            const index = parseInt(btn.dataset.index);
            removePerson(index);
        }
        
        // Record action button handler (new - supports method_type and action)
        if (e.target.closest('.record-action-btn')) {
            const btn = e.target.closest('.record-action-btn');
            const vehicleId = btn.dataset.vehicleId;
            const vehicleNumber = btn.dataset.vehicleNumber;
            const methodType = btn.dataset.methodType || 'entry_first';
            const action = btn.dataset.action || 'entry';
            recordAction(vehicleId, vehicleNumber, methodType, action);
        }
        
        // Record exit button handler (legacy support)
        if (e.target.closest('.record-exit-btn')) {
            const btn = e.target.closest('.record-exit-btn');
            const vehicleId = btn.dataset.vehicleId;
            const vehicleNumber = btn.dataset.vehicleNumber;
            const methodType = btn.dataset.methodType || 'entry_first';
            recordAction(vehicleId, vehicleNumber, methodType);
        }
        
        // Print vehicle slip button handler (dynamically added)
        if (e.target.closest('.print-vehicle-slip-btn')) {
            const btn = e.target.closest('.print-vehicle-slip-btn');
            const vehicleId = btn.dataset.vehicleId;
            printVehicleSlip(vehicleId);
        }

        // Cancel vehicle entry button handler
        if (e.target.closest('.cancel-vehicle-entry-btn')) {
            const btn = e.target.closest('.cancel-vehicle-entry-btn');
            const vehicleId = btn.dataset.vehicleId;
            const vehicleNumber = btn.dataset.vehicleNumber;

            swal({
                title: 'Are you sure?',
                text: 'Cancel entry for vehicle ' + vehicleNumber + '?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No',
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (!isConfirm) { return; }

                $.post('ajax.php', { page: 'cancel_vehicle_entry', vehicle_id: vehicleId }, function(response) {
                    var res = response;
                    if (typeof response === 'string') {
                        try { res = JSON.parse(response); } catch(e) { res = { status: 'error', message: 'Unable to process server response.' }; }
                    }
                    if (res.status === 'sucess') {
                        swal('Cancelled', 'Vehicle entry cancelled successfully.', 'success');
                        setTimeout(function() { loadVehicleGatewayData(); }, 800);
                        return;
                    }
                    swal('Error', res.message || 'Unable to cancel vehicle entry.', 'error');
                }).fail(function() {
                    swal('Error', 'Network error. Please try again.', 'error');
                });
            });
        }
    });
    
    // Close print modal button handler
    const closePrintModalBtn = document.querySelector('.close-print-modal-btn');
    if (closePrintModalBtn) {
        closePrintModalBtn.addEventListener('click', closePrintModal);
    }
    
    // Print slip button handler
    const printSlipBtn = document.querySelector('.print-slip-btn');
    if (printSlipBtn) {
        printSlipBtn.addEventListener('click', printSlip);
    }
    
    // Vehicle number input handlers
    const vehicleNumberInput = document.querySelector('input[name="vehicle_number"]');
    if (vehicleNumberInput) {
        // Auto-uppercase
        vehicleNumberInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
            
            // Reset lookup flag when user changes vehicle number
            vehicleFoundInLookup = false;
            
            // Hide ownership selection when user starts typing new vehicle number
            hideVehicleOwnershipSelection();
        });
        
        // Vehicle number lookup on blur
        vehicleNumberInput.addEventListener('blur', function () {
            const vehicleNumber = this.value.trim();
            if (vehicleNumber && vehicleNumber.length >= 3) {
                lookupVehicleDetails(vehicleNumber);
            }
        });
    }
    
    // Vehicle entry form submit handler
    const vehicleEntryForm = document.getElementById('vehicleEntryForm');
    if (vehicleEntryForm) {
        vehicleEntryForm.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const vehicleType = document.querySelector('select[name="vehicle_type"]').value;
            let vehicleNumber;
            
            if (vehicleType == 'demo') {
                vehicleNumber = document.querySelector('select[name="vehicle_number_select"]').value;
            } else if (vehicleType == 'motorcycle') {
                vehicleNumber = document.getElementById('motor_cycle_vehicle_number').value;
            } else {
                vehicleNumber = document.querySelector('input[name="vehicle_number"]').value.trim();
            }
            
            const driverNameField = document.querySelector('select[name="driver_name"]');
            const driverName = driverNameField ? (driverNameField.value || '').trim() : '';
            const visitPurpose = document.querySelector('select[name="visit_purpose"]').value;
            const location = document.querySelector('select[name="location"]').value;

            const vehicle_reading = document.getElementById('vehicle_reading').value;


            if (!vehicleNumber || !vehicleType || !driverName || !visitPurpose || !location || !vehicle_reading) {
                alert('Please fill in all required fields.');
                return false;
            }

            if (vehicleType === 'other') {
                const otherVehicleType = document.getElementById('otherVehicleType').value.trim();
                if (!otherVehicleType) {
                    alert('Please specify the vehicle type.');
                    document.getElementById('otherVehicleType').focus();
                    return false;
                }
            }

            if (visitPurpose === 'other') {
                const otherPurpose = document.getElementById('otherPurpose').value.trim();
                if (!otherPurpose) {
                    alert('Please specify the purpose of visit.');
                    document.getElementById('otherPurpose').focus();
                    return false;
                }
            }

            const ownershipRow = document.getElementById('vehicleOwnershipRow');
            if (ownershipRow && ownershipRow.style.display !== 'none') {
                const selectedOwnership = document.querySelector('input[name="vehicle_ownership"]:checked');
                if (!selectedOwnership) {
                    alert('Please select vehicle ownership type.');
                    return false;
                }
            }

            if (vehicleType !== 'demo') {
                const vehiclePattern = /^[A-Z0-9\-\s]+$/;
                if (!vehiclePattern.test(vehicleNumber)) {
                    alert('Please enter a valid vehicle number format (letters, numbers, hyphens, and spaces only).');
                    return false;
                }
            }

            const driverContact = document.querySelector('input[name="driver_contact"]').value.trim();
            if (driverContact && !/^[\+]?[0-9\-\s\(\)]{10,15}$/.test(driverContact)) {
                alert('Please enter a valid phone number.');
                return false;
            }
            if (location == '99999') {
                const otherLocation = document.getElementById('otherLocation').value.trim();
                if (!otherLocation) {
                    alert('Please enter other location.');
                    document.getElementById('otherLocation').focus();
                    return false;
                }
            }

            submitVehicleEntry();
        });
    }
    
    // Exit form submit handler
    const exitForm = document.getElementById('exitForm');
    if (exitForm) {
        exitForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitVehicleExit();
        });
    }
    
    // Auto-refresh current vehicles list every 2 minutes
    setInterval(function () {
        if (typeof jQuery !== 'undefined' && !$('.modal').hasClass('show')) {
            loadVehicleGatewayData();
        }
    }, 120000);
    
    // Start waiting for jQuery
    waitForjQueryAndInitialize();
    
    // Start initializing modal handlers
    initializeModalHandlers();

    initializeVehicleTabs();
}

/**
 * Initialize vehicle tabs without relying on Bootstrap JS
 */
function initializeVehicleTabs() {
    const tabsContainer = document.getElementById('currentVehiclesTabs');
    if (!tabsContainer) return;

    tabsContainer.addEventListener('click', function (e) {
        const tabLink = e.target.closest('.nav-link');
        if (!tabLink) return;
        e.preventDefault();

        const targetSelector = tabLink.getAttribute('href');
        if (!targetSelector) return;

        tabsContainer.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            link.setAttribute('aria-selected', 'false');
        });
        tabLink.classList.add('active');
        tabLink.setAttribute('aria-selected', 'true');

        const tabContent = document.getElementById('currentVehiclesTabContent');
        if (!tabContent) return;

        tabContent.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        const targetPane = document.querySelector(targetSelector);
        if (targetPane) {
            targetPane.classList.add('show', 'active');
        }
    });
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

/**
 * Filter vehicles based on search term
 * @param {array} vehicles - Array of vehicle objects
 * @param {string} searchTerm - Search term
 * @returns {array} Filtered vehicles
 */
function filterVehicles(vehicles, searchTerm) {
    if (!searchTerm || searchTerm.trim() === '') {
        return vehicles;
    }
    
    const term = searchTerm.trim().toLowerCase();
    
    return vehicles.filter(vehicle => {
        const vehicle_number = (vehicle.vehicle_number || '').toLowerCase();
        const driver_name = (getDriverDisplayName(vehicle.driver_name) || '').toLowerCase();
        const vehicle_type = (vehicle.vehicle_type || '').toLowerCase();
        const visit_purpose = (vehicle.visit_purpose || '').toLowerCase();
        const location = (vehicle.location || '').toLowerCase();
        
        return vehicle_number.includes(term) ||
               driver_name.includes(term) ||
               vehicle_type.includes(term) ||
               visit_purpose.includes(term) ||
               location.includes(term);
    });
}

/**
 * Initialize search/filter event listeners
 */
function initializeVehicleSearch() {
    const searchInput = document.getElementById('vehicleSearchInput');
    
    if (!searchInput) {
        return;
    }
    
    // Real-time search on input
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value;
        
        // Determine which tab is active
        const activeTab = document.querySelector('#currentVehiclesTabs .nav-link.active');
        if (!activeTab) {
            return;
        }
        
        const tabTarget = activeTab.getAttribute('data-tab');
        
        if (tabTarget === 'pending') {
            const filteredVehicles = filterVehicles(pendingVehiclesData, searchTerm);
            updateVehiclesTable('#pendingVehiclesTable', filteredVehicles, 'pending');
        } else if (tabTarget === 'missing') {
            const filteredVehicles = filterVehicles(missingVehiclesData, searchTerm);
            updateVehiclesTable('#missingVehiclesTable', filteredVehicles, 'initial');
        }
    });
    
    // Clear search on tab switch
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-tab]') && e.target.closest('[data-tab]').getAttribute('role') === 'tab') {
            searchInput.value = '';
            
            const tabTarget = e.target.closest('[data-tab]').getAttribute('data-tab');
            if (tabTarget === 'pending') {
                updateVehiclesTable('#pendingVehiclesTable', pendingVehiclesData, 'pending');
            } else if (tabTarget === 'missing') {
                updateVehiclesTable('#missingVehiclesTable', missingVehiclesData, 'initial');
            }
        }
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initTransportGatewayEventListeners();
    initializeVehicleSearch();
});
