/**
 * Custom View Buddy Reassignment History JavaScript
 * Handles history display and details modal
 */

jQuery(document).ready(function($) {
    'use strict';

    // Parse employee names data from embedded JSON
    let employeeNamesData = {};
    try {
        const jsonData = $('#employee_names_data').text();
        if (jsonData) {
            employeeNamesData = JSON.parse(jsonData);
        }
    } catch (e) {
        console.error('Error parsing employee names data:', e);
    }

    // Initialize DataTable for history
    const historyTable = $('#history_table').DataTable({
        pageLength: 25,
        order: [[0, 'desc']], // Sort by ID descending (most recent first)
        ordering: true,
        searching: true,
        lengthChange: true,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [
            { targets: [7], orderable: false } // Action column not sortable
        ]
    });

    // View Details Button Click Handler
    $(document).on('click', '.view-details-btn', function(e) {
        e.preventDefault();
        
        const recordId = $(this).data('record-id');
        const employeeIdsJson = $(this).data('employee-ids');
        const buddyType = $(this).data('buddy-type');
        const fromBuddy = $(this).data('from-buddy');
        const toBuddy = $(this).data('to-buddy');
        
        // Populate modal header information
        $('#modal_buddy_type').html('<span class="badge bg-info">' + buddyType + '</span>');
        $('#modal_from_buddy').text(fromBuddy);
        $('#modal_to_buddy').text(toBuddy);
        
        // Parse employee IDs
        let employeeIds = [];
        try {
            if (typeof employeeIdsJson === 'string') {
                employeeIds = JSON.parse(employeeIdsJson);
            } else if (Array.isArray(employeeIdsJson)) {
                employeeIds = employeeIdsJson;
            }
        } catch (e) {
            console.error('Error parsing employee IDs:', e);
            employeeIds = [];
        }
        
        // Populate employee details table
        populateEmployeeDetailsTable(employeeIds);
        
        // Show modal
        const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
        detailsModal.show();
    });

    // Populate Employee Details Table in Modal
    function populateEmployeeDetailsTable(employeeIds) {
        const tbody = $('#modal_employees_tbody');
        tbody.empty();
        
        if (!employeeIds || employeeIds.length === 0) {
            tbody.html('<tr><td colspan="3" class="text-center">No employees found</td></tr>');
            return;
        }
        
        let rows = '';
        employeeIds.forEach(function(empId, index) {
            let empCode = 'N/A';
            let empName = 'Unknown';
            
            // Look up employee details from employeeNamesData
            if (employeeNamesData[empId]) {
                empCode = employeeNamesData[empId].emp_code || 'N/A';
                empName = employeeNamesData[empId].name || 'Unknown';
            }
            
            rows += '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + empCode + '</td>' +
                '<td>' + empName + '</td>' +
                '</tr>';
        });
        
        tbody.html(rows);
    }

    // Format Date (if needed for any dynamic date formatting)
    function formatDate(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        
        return date.toLocaleDateString('en-US', options);
    }

    // Format Buddy Type for Display
    function formatBuddyType(buddyType) {
        const types = {
            'hr_buddy': 'HR Buddy',
            'finance_buddy': 'Finance Buddy',
            'admin_buddy': 'Admin Buddy',
            'project_buddy': 'Project Buddy',
            'loc_buddy': 'Location Buddy'
        };
        
        return types[buddyType] || buddyType.replace('_', ' ').toUpperCase();
    }

    // Export functionality (optional - can be added later)
    $(document).on('click', '#btn_export_history', function(e) {
        e.preventDefault();
        // Can implement CSV/Excel export if needed
        console.log('Export functionality - to be implemented');
    });

});
