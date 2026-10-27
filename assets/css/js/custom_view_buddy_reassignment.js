/**
 * Custom View Buddy Reassignment JavaScript
 * Handles buddy reassignment functionality with preview and confirmation
 */

jQuery(document).ready(function($) {
    'use strict';

    // Initialize Select2 for buddy dropdowns
    $('.js-example-basic-single').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Select Employee --',
        allowClear: true
    });

    // Store affected employees data
    let affectedEmployeesData = [];
    let employeesDataTable = null;

    // Handle Buddy Type Change - Populate the dropdowns
    $(document).on('change', '#buddy_type', function(e) {
        e.preventDefault();
        
        const buddyType = $(this).val();
        
        // Clear selections
        $('#from_buddy').val(null).trigger('change');
        $('#to_buddy').val(null).trigger('change');
        $('#preview_section').hide();
        affectedEmployeesData = [];
        
        // If no buddy type selected, clear the dropdowns
        if (!buddyType) {
            $('#from_buddy').html('<option value="">-- Select Employee --</option>').trigger('change');
            $('#to_buddy').html('<option value="">-- Select Employee --</option>').trigger('change');
            return;
        }
        
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching employees for ' + buddyType,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // AJAX call to get employees with this buddy type
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                page: 'get_employees_by_buddy_type',
                buddy_type: buddyType
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success && response.data && response.data.length > 0) {
                    // Build options for both dropdowns
                    let optionsHtml = '<option value="">-- Select Employee --</option>';
                    
                    response.data.forEach(function(emp) {
                        optionsHtml += '<option value="' + emp.id + '">' + 
                            emp.emp_code + ' - ' + emp.display_name + 
                            '</option>';
                    });
                    
                    // Update both dropdowns
                    $('#from_buddy').html(optionsHtml).trigger('change');
                    $('#to_buddy').html(optionsHtml).trigger('change');
                    
                } else {
                    // No employees found for this buddy type
                    const noDataHtml = '<option value="">-- No employees assigned --</option>';
                    $('#from_buddy').html(noDataHtml).trigger('change');
                    $('#to_buddy').html(noDataHtml).trigger('change');
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'No Employees Found',
                        text: 'No employees are assigned as ' + buddyType + '. Please select another buddy type.'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                
                // Reset dropdowns on error
                const noDataHtml = '<option value="">-- Select Employee --</option>';
                $('#from_buddy').html(noDataHtml).trigger('change');
                $('#to_buddy').html(noDataHtml).trigger('change');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error Loading Employees',
                    text: 'An error occurred while fetching employees: ' + error
                });
            }
        });
    });

    // Preview Button Click Handler
    $(document).on('click', '#btn_preview', function(e) {
        e.preventDefault();
        
        const buddyType = $('#buddy_type').val();
        const fromBuddy = $('#from_buddy').val();
        const toBuddy = $('#to_buddy').val();
        
        // Validation
        if (!buddyType) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select a Buddy Type'
            });
            return;
        }
        
        if (!fromBuddy) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select From Buddy'
            });
            return;
        }
        
        if (!toBuddy) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select To Buddy'
            });
            return;
        }
        
        if (fromBuddy === toBuddy) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Selection',
                text: 'From Buddy and To Buddy cannot be the same'
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching affected employees',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // AJAX call to get affected employees
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                page: 'get_buddy_employees',
                buddy_type: buddyType,
                from_buddy: fromBuddy,
                to_buddy: toBuddy
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    affectedEmployeesData = response.data;
                    
                    if (affectedEmployeesData.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Employees Found',
                            text: 'No employees are assigned to the selected buddy'
                        });
                        $('#preview_section').hide();
                        return;
                    }
                    
                    // Update preview info
                    const buddyTypeName = $('#buddy_type option:selected').text();
                    const fromBuddyName = $('#from_buddy option:selected').text();
                    const toBuddyName = $('#to_buddy option:selected').text();
                    
                    $('#preview_info').html(
                        '<strong>Reassignment Summary:</strong> ' +
                        '<span class="badge bg-info">' + buddyTypeName + '</span> ' +
                        'from <strong>' + fromBuddyName + '</strong> ' +
                        'to <strong>' + toBuddyName + '</strong><br><br>' +
                        '<strong>Total Employees Affected:</strong> ' + 
                        '<span class="badge bg-success">' + affectedEmployeesData.length + '</span>'
                    );
                    
                    // Populate table
                    populateEmployeesTable();
                    
                    // Show preview section
                    $('#preview_section').fadeIn();
                    
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to fetch employees'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'AJAX Error',
                    text: 'An error occurred while fetching employees: ' + error
                });
            }
        });
    });

    // Populate Employees DataTable
    function populateEmployeesTable() {
        // Destroy existing DataTable if it exists
        if (employeesDataTable) {
            employeesDataTable.destroy();
        }
        
        // Clear table body
        $('#employees_tbody').empty();
        
        // Build table rows
        let rows = '';
        affectedEmployeesData.forEach(function(emp) {
            rows += '<tr>' +
                '<td>' + emp.emp_code + '</td>' +
                '<td>' + emp.display_name + '</td>' +
                '<td>' + (emp.department_name || '-') + '</td>' +
                '<td>' + (emp.designation_name || '-') + '</td>' +
                '<td>' + (emp.current_buddy_name || '-') + '</td>' +
                '</tr>';
        });
        
        $('#employees_tbody').html(rows);
        
        // Initialize DataTable
        employeesDataTable = $('#employees_table').DataTable({
            pageLength: 10,
            ordering: true,
            searching: true,
            lengthChange: true,
            info: true,
            autoWidth: false,
            responsive: true
        });
    }

    // Confirm Reassignment Button Click Handler
    $(document).on('click', '#btn_confirm', function(e) {
        e.preventDefault();
        
        const buddyType = $('#buddy_type').val();
        const fromBuddy = $('#from_buddy').val();
        const toBuddy = $('#to_buddy').val();
        
        // Extract employee IDs
        const employeeIds = affectedEmployeesData.map(emp => emp.id);
        
        if (employeeIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Employees',
                text: 'No employees to reassign'
            });
            return;
        }
        
        // Confirmation dialog
        Swal.fire({
            title: 'Confirm Reassignment',
            html: 'Are you sure you want to reassign <strong>' + employeeIds.length + ' employee(s)</strong>?<br>' +
                  'This action will update their buddy assignments.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, Confirm',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform reassignment
                performReassignment(buddyType, fromBuddy, toBuddy, employeeIds);
            }
        });
    });

    // Perform Reassignment AJAX Call
    function performReassignment(buddyType, fromBuddy, toBuddy, employeeIds) {
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Updating employee buddy assignments',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                page: 'confirm_buddy_reassignment',
                buddy_type: buddyType,
                from_buddy: fromBuddy,
                to_buddy: toBuddy,
                employee_ids: JSON.stringify(employeeIds)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: true
                    }).then(() => {
                        // Reset form and hide preview
                        resetForm();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: response.message || 'Failed to complete reassignment'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'AJAX Error',
                    text: 'An error occurred during reassignment: ' + error
                });
            }
        });
    }

    // Cancel Preview Button Click Handler
    $(document).on('click', '#btn_cancel_preview', function(e) {
        e.preventDefault();
        $('#preview_section').fadeOut();
        affectedEmployeesData = [];
    });

    // Form Reset Handler
    $('form#buddy_reassignment_form').on('reset', function() {
        resetForm();
    });

    // Reset Form Function
    function resetForm() {
        $('#buddy_reassignment_form')[0].reset();
        $('.js-example-basic-single').val(null).trigger('change');
        $('#preview_section').hide();
        affectedEmployeesData = [];
        if (employeesDataTable) {
            employeesDataTable.destroy();
            employeesDataTable = null;
        }
        $('#employees_tbody').empty();
    }

    // From/To Buddy Change - Hide preview if shown
    $(document).on('change', '#from_buddy, #to_buddy', function() {
        if ($('#preview_section').is(':visible')) {
            $('#preview_section').fadeOut();
            affectedEmployeesData = [];
        }
    });

});
