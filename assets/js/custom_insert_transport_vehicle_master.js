/**
 * Transport Vehicle Master - JavaScript Functions
 * Page: custom_insert_transport_vehicle_master.php
 */

// Session variables
let company_id = 0;
let user_id = 0;
let editMode = false;
let editId = '';
let vehicleData = null;

/**
 * Initialize data from PHP hidden div
 */
function initTransportVehicleMasterData() {
    const dataContainer = document.querySelector('.transport-vehicle-master-php-data');
    if (dataContainer) {
        company_id = parseInt(dataContainer.dataset.companyId) || 0;
        user_id = parseInt(dataContainer.dataset.userId) || 0;
        editMode = dataContainer.dataset.editMode === '1';
        editId = dataContainer.dataset.editId || '';
        
        if (dataContainer.dataset.vehicleData) {
            try {
                vehicleData = JSON.parse(dataContainer.dataset.vehicleData);
            } catch (e) {
                console.error('Error parsing vehicle data:', e);
            }
        }
    }
}

/**
 * Auto-uppercase vehicle registration number
 */
function setupAutoUppercase() {
    const uppercaseFields = ['vehicleRegNo', 'chassisNo', 'engineNo'];
    
    uppercaseFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    });
}

/**
 * Validate form before submission
 */
function validateForm() {
    const vehicleRegNo = document.getElementById('vehicleRegNo').value.trim();
    const vehicleType = document.getElementById('vehicleType').value;
    
    if (!vehicleRegNo) {
        showNotification('error', 'Please enter vehicle registration number.');
        document.getElementById('vehicleRegNo').focus();
        return false;
    }
    
    if (!vehicleType) {
        showNotification('error', 'Please select vehicle type.');
        document.getElementById('vehicleType').focus();
        return false;
    }
    
    // Validate vehicle registration format (basic)
    const regPattern = /^[A-Z0-9\-\s]+$/;
    if (!regPattern.test(vehicleRegNo)) {
        showNotification('error', 'Please enter a valid vehicle registration number (letters, numbers, hyphens, and spaces only).');
        document.getElementById('vehicleRegNo').focus();
        return false;
    }
    
    return true;
}

/**
 * Show notification
 */
function showNotification(type, message) {
    if (typeof tostarnotify === 'function') {
        const icon = type === 'success' ? 'fa fa-thumbs-up' : 'fa fa-exclamation-triangle';
        const labelClass = type === 'success' ? 'form-label label label-success' : 'form-label label label-danger';
        
        tostarnotify(
            'top', 'right',
            icon,
            labelClass,
            'animated flipInX',
            'animated flipOutX',
            message,
            ''
        );
    } else {
        alert(message);
    }
}

/**
 * Submit vehicle form via AJAX
 */
function submitVehicleForm() {
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please wait a moment and try again.');
        return;
    }
    
    const form = document.getElementById('vehicleMasterForm');
    const formData = new FormData(form);
    formData.append('page', editMode ? 'update_transport_vehicle_master' : 'insert_transport_vehicle_master');
    formData.append('company_id', company_id);
    formData.append('user_id', user_id);
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ' + (editMode ? 'Updating...' : 'Saving...');
    submitBtn.disabled = true;
    
    $.post('ajax.php', Object.fromEntries(formData), function(response) {
        try {
            const result = JSON.parse(response);
            
            if (result.status === 'success' || result.status === 'sucess') {
                showNotification('success', editMode ? 'Vehicle updated successfully!' : 'Vehicle added successfully!');
                
                if (!editMode) {
                    // Reset form for new entry
                    form.reset();
                    // Refresh select2 dropdowns
                    if (typeof jQuery !== 'undefined') {
                        $('.js-example-basic-single').trigger('change');
                    }
                }
                
                // Redirect to list page after short delay
                setTimeout(function() {
                    window.location.href = 'index.php?option=custom_list_transport_vehicle_master';
                }, 1500);
            } else {
                showNotification('error', result.message || 'Error saving vehicle. Please try again.');
            }
        } catch (e) {
            console.error('Error parsing response:', e);
            showNotification('error', 'Error processing response. Please try again.');
        }
    }).fail(function(xhr, status, error) {
        console.error('AJAX error:', error);
        showNotification('error', 'Network error. Please try again.');
    }).always(function() {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}


/**
 * Initialize event listeners
 */
function initTransportVehicleMasterEventListeners() {
    // Initialize PHP data
    initTransportVehicleMasterData();
    
    // Setup auto-uppercase for specific fields
    setupAutoUppercase();
    
    // Form submit handler
    const form = document.getElementById('vehicleMasterForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                submitVehicleForm();
            }
        });
    }
}

/**
 * Wait for jQuery and initialize
 */
function waitForjQueryAndInitialize() {
    if (typeof jQuery !== 'undefined') {
        $(document).ready(function() {
            // Initialize select2
            $('.js-example-basic-single').select2();
        });
    } else {
        setTimeout(waitForjQueryAndInitialize, 100);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initTransportVehicleMasterEventListeners();
    waitForjQueryAndInitialize();
});
