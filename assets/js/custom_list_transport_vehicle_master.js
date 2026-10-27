/**
 * Transport Vehicle Master List - JavaScript Functions
 * Page: custom_list_transport_vehicle_master.php
 */

// Session variables
let company_id = 0;
let user_id = 0;

/**
 * Initialize data from PHP hidden div
 */
function initListData() {
    const dataContainer = document.querySelector('.transport-vehicle-master-list-php-data');
    if (dataContainer) {
        company_id = parseInt(dataContainer.dataset.companyId) || 0;
        user_id = parseInt(dataContainer.dataset.userId) || 0;
    }
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
 * Open modal helper
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
 * Close modal helper
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
 * View vehicle details
 */
function viewVehicleDetails(vehicleId) {
    const contentDiv = document.getElementById('viewVehicleContent');
    contentDiv.innerHTML = `
        <div class="text-center p-4">
            <i class="fa fa-spinner fa-spin loading-spinner-icon"></i>
            <p class="text-muted mt-2">Loading vehicle details...</p>
        </div>
    `;
    
    openModal('viewVehicleModal');
    
    if (typeof jQuery === 'undefined') {
        contentDiv.innerHTML = '<div class="alert alert-danger">System not ready. Please refresh the page.</div>';
        return;
    }
    
    $.post('ajax.php', {
        page: 'get_transport_vehicle_master_details',
        vehicle_id: vehicleId,
        company_id: company_id
    }, function(response) {
        try {
            const result = JSON.parse(response);
            
            if (result.status === 'success' && result.data) {
                renderVehicleDetails(result.data);
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger">Error loading vehicle details.</div>';
            }
        } catch (e) {
            console.error('Error parsing response:', e);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error processing response.</div>';
        }
    }).fail(function() {
        contentDiv.innerHTML = '<div class="alert alert-danger">Network error. Please try again.</div>';
    });
}

/**
 * Render vehicle details in modal
 */
function renderVehicleDetails(vehicle) {
    const vehicleTypes = {
        'demo': 'Demo Car', 'car': 'Car', 'truck': 'Truck', 'van': 'Van',
        'motorcycle': 'Motorcycle', 'bus': 'Bus', 'trailer': 'Trailer', 'other': 'Other'
    };
    const fuelTypes = {
        'petrol': 'Petrol', 'diesel': 'Diesel', 'cng': 'CNG',
        'lpg': 'LPG', 'electric': 'Electric', 'hybrid': 'Hybrid'
    };
    
    // Get status name from the status_name field returned by API, or fallback
    const statusName = vehicle.status_name || 'Unknown';
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3"><i class="ti-info-alt"></i> Basic Information</h6>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Registration No.:</span>
                    <span class="vehicle-detail-value"><strong>${vehicle.vehicle_reg_no || '-'}</strong></span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Chassis No.:</span>
                    <span class="vehicle-detail-value">${vehicle.chassis_no || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Engine No.:</span>
                    <span class="vehicle-detail-value">${vehicle.engine_no || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Vehicle Type:</span>
                    <span class="vehicle-detail-value">${vehicleTypes[vehicle.vehicle_type] || vehicle.vehicle_type || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Model:</span>
                    <span class="vehicle-detail-value">${vehicle.model || '-'} ${vehicle.model_group ? '(' + vehicle.model_group + ')' : ''}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Colour:</span>
                    <span class="vehicle-detail-value">${vehicle.colour || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Fuel Type:</span>
                    <span class="vehicle-detail-value">${fuelTypes[vehicle.fuel_type] || vehicle.fuel_type || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">KM Driven:</span>
                    <span class="vehicle-detail-value">${vehicle.km_driven ? Number(vehicle.km_driven).toLocaleString() : '0'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Average:</span>
                    <span class="vehicle-detail-value">${vehicle.average || '-'} km/l</span>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary mb-3"><i class="ti-location-pin"></i> Registration & Branch</h6>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Registered Branch:</span>
                    <span class="vehicle-detail-value">${vehicle.registered_branch || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Registration Name:</span>
                    <span class="vehicle-detail-value">${vehicle.registration_name || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Responsible Person:</span>
                    <span class="vehicle-detail-value">${vehicle.responsible_person || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Department:</span>
                    <span class="vehicle-detail-value">${vehicle.department_type || '-'}</span>
                </div>
                
                <h6 class="text-primary mb-3 mt-4"><i class="ti-shield"></i> Insurance & Compliance</h6>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Insurance No.:</span>
                    <span class="vehicle-detail-value">${vehicle.insurance_no || '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Insurance Expiry:</span>
                    <span class="vehicle-detail-value">${vehicle.insurance_exp_date ? formatDate(vehicle.insurance_exp_date) : '-'}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">PUC Issue Date:</span>
                    <span class="vehicle-detail-value">${vehicle.puc_issue_date ? formatDate(vehicle.puc_issue_date) : '-'}</span>
                </div>
                
                <h6 class="text-primary mb-3 mt-4"><i class="ti-check-box"></i> Status</h6>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Current Status:</span>
                    <span class="vehicle-detail-value">${statusName}</span>
                </div>
                <div class="vehicle-detail-row">
                    <span class="vehicle-detail-label">Active:</span>
                    <span class="vehicle-detail-value">${vehicle.is_active == '0' ? '<span class="vehicle-active-badge">Active</span>' : '<span class="vehicle-inactive-badge">Inactive</span>'}</span>
                </div>
            </div>
        </div>
        ${vehicle.description || vehicle.additional_notes ? `
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-primary mb-3"><i class="ti-notepad"></i> Notes</h6>
                ${vehicle.description ? `<p><strong>Description:</strong> ${vehicle.description}</p>` : ''}
                ${vehicle.additional_notes ? `<p><strong>Additional Notes:</strong> ${vehicle.additional_notes}</p>` : ''}
            </div>
        </div>
        ` : ''}
    `;
    
    document.getElementById('viewVehicleContent').innerHTML = html;
}

/**
 * Format date helper
 */
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}


/**
 * Show delete confirmation modal
 */
function showDeleteConfirmation(vehicleId, vehicleReg) {
    document.getElementById('deleteVehicleId').value = vehicleId;
    document.getElementById('deleteVehicleReg').textContent = vehicleReg;
    openModal('deleteVehicleModal');
}

/**
 * Delete vehicle
 */
function deleteVehicle() {
    const vehicleId = document.getElementById('deleteVehicleId').value;
    
    if (!vehicleId) {
        showNotification('error', 'Invalid vehicle ID.');
        return;
    }
    
    if (typeof jQuery === 'undefined') {
        alert('System not ready. Please refresh the page.');
        return;
    }
    
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Deleting...';
    deleteBtn.disabled = true;
    
    $.post('ajax.php', {
        page: 'delete_transport_vehicle_master',
        vehicle_id: vehicleId,
        company_id: company_id,
        user_id: user_id
    }, function(response) {
        try {
            const result = JSON.parse(response);
            
            if (result.status === 'success' || result.status === 'sucess') {
                showNotification('success', 'Vehicle deleted successfully!');
                closeModal('deleteVehicleModal');
                
                // Remove row from table
                const row = document.querySelector(`tr[data-vehicle-id="${vehicleId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Check if table is empty
                const tbody = document.getElementById('vehicleTableBody');
                if (tbody && tbody.querySelectorAll('tr[data-vehicle-id]').length === 0) {
                    tbody.innerHTML = `
                        <tr id="noDataRow">
                            <td colspan="10">
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30">
                                        <i class="ti-alert text-warning"></i> No Vehicles Found
                                    </p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png"
                                            style="max-height: 300px!important;">
                                    </div>
                                    <a href="index.php?option=custom_insert_transport_vehicle_master" class="btn btn-primary mt-3">
                                        <i class="ti-plus"></i> Add First Vehicle
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            } else {
                showNotification('error', result.message || 'Error deleting vehicle. Please try again.');
            }
        } catch (e) {
            console.error('Error parsing response:', e);
            showNotification('error', 'Error processing response. Please try again.');
        }
    }).fail(function() {
        showNotification('error', 'Network error. Please try again.');
    }).always(function() {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    });
}

/**
 * Initialize event listeners
 */
function initListEventListeners() {
    // Initialize data
    initListData();
    
    // View vehicle button click (event delegation)
    document.addEventListener('click', function(e) {
        // View button
        if (e.target.closest('.view-vehicle-btn')) {
            const btn = e.target.closest('.view-vehicle-btn');
            const vehicleId = btn.dataset.vehicleId;
            viewVehicleDetails(vehicleId);
        }
        
        // Delete button
        if (e.target.closest('.delete-vehicle-btn')) {
            const btn = e.target.closest('.delete-vehicle-btn');
            const vehicleId = btn.dataset.vehicleId;
            const vehicleReg = btn.dataset.vehicleReg;
            showDeleteConfirmation(vehicleId, vehicleReg);
        }
        
        // Close view modal buttons
        if (e.target.closest('.close-view-modal-btn')) {
            closeModal('viewVehicleModal');
        }
        
        // Close delete modal buttons
        if (e.target.closest('.close-delete-modal-btn')) {
            closeModal('deleteVehicleModal');
        }
    });
    
    // Confirm delete button
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', deleteVehicle);
    }
}

/**
 * Wait for jQuery and initialize
 */
function waitForjQueryAndInitialize() {
    if (typeof jQuery !== 'undefined') {
        $(document).ready(function() {
            // Initialize select2 if any
            if ($('.js-example-basic-single').length) {
                $('.js-example-basic-single').select2();
            }
        });
    } else {
        setTimeout(waitForjQueryAndInitialize, 100);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initListEventListeners();
    waitForjQueryAndInitialize();
});
