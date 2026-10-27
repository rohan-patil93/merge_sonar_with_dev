

/**
 * Clock Time Inline Editing
 * Handles double-click to edit clock_in and clock_out times
 * Saves changes via AJAX with automatic attendance code calculation
 */

$(document).on('dblclick', '.clock-in-cell, .clock-out-cell', function(e) {
    e.preventDefault();

    const $cell = $(this);
    const rid = $cell.data('rid');
    const empId = $cell.data('emp-id');
    const cellDate = $cell.data('date');
    const cellValue = $cell.text().trim();
    const isClockIn = $cell.hasClass('clock-in-cell');
    const checkType = isClockIn ? 'clockin' : 'clockout';

    // Skip if already in edit mode
    if ($cell.find('input').length > 0) {
        return;
    }

    // Extract HH:MM from HH:MM:SS
    const timeValue = cellValue.substring(0, 5); // Extract HH:MM from HH:MM:SS

    // Store original value for revert
    const originalHtml = $cell.html();

    // Create input element (time input - HH:MM format)
    const $input = $('<input>')
        .attr('type', 'time')
        .attr('class', 'clock-time-input')
        .attr('value', timeValue);

    // Replace cell content with input
    $cell.empty().append($input);

    // Focus and select input
    $input.focus().select();

    // Save on blur
    $input.on('blur', function() {
        saveClockTime(rid, empId, cellDate, checkType, $(this).val(), $cell, originalHtml);
    });

    // Save on Enter key
    $input.on('keypress', function(e) {
        if (e.which == 13) { // Enter key
            $(this).blur();
        }
    });

    // Cancel on Escape key
    $input.on('keydown', function(e) {
        if (e.which == 27) { // Escape key
            $cell.empty().append(originalHtml);
        }
    });
});

/**
 * Save clock time via AJAX
 * @param {number} rid - Record ID (can be 0 for new insert)
 * @param {number} empId - Employee ID
 * @param {string} attDate - Attendance date (YYYY-MM-DD)
 * @param {string} checkType - 'clockin' or 'clockout'
 * @param {string} timeValue - Time value (HH:MM format from time input)
 * @param {jQuery} $cell - Table cell element
 * @param {string} originalHtml - Original HTML to restore on error
 */
function saveClockTime(rid, empId, attDate, checkType, timeValue, $cell, originalHtml) {
    // Validate time value (HH:MM format)
    if (!timeValue || timeValue.length == 0) {
        $cell.empty().append(originalHtml);
        return;
    }

    // Validate HH:MM format
    if (!/^\d{2}:\d{2}$/.test(timeValue)) {
        tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', 'Invalid time format (use HH:MM)', '');
        $cell.empty().append(originalHtml);
        return;
    }

    const clockTime = timeValue; // "10:30"

    // Show loading state
    $cell.empty().html('<i class="fa fa-spinner fa-spin"></i>');

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
            page: 'saveClockTime',
            rid: rid,
            employee_id: empId,
            attendace_date: attDate,
            check_type: checkType,
            clock_value: clockTime
        },
        dataType: 'json',
        timeout: 10000,
        success: function(res) {
            // ==============================
            // Full Success
            // ==============================
            if (res.status == 'success') {
                // Display returned time (formatted from server)
                const displayValue = checkType == 'clockin' ? res.clock_in : res.clock_out;
                $cell.empty().text(displayValue);
                $cell.addClass('saved-state');

                // Remove saved-state class after 2 seconds
                setTimeout(function() {
                    $cell.removeClass('saved-state');
                }, 2000);

                // Show success notification
                tostarnotify('top', 'right', 'fa fa-check', 'form-label label label-success',
                    'animated flipInX', 'animated flipOutX', res.message || 'Time updated successfully', '');

                // Reload the table after success
                setTimeout(function() {
                    reloadAttendanceTable();
                }, 1500);
            }
            // ==============================
            // Partial Success (Main updated, detail had issue)
            // ==============================
            else if (res.status == 'partial_success') {
                // Display returned time even though detail had issue
                const displayValue = checkType == 'clockin' ? res.clock_in : res.clock_out;
                $cell.empty().text(displayValue);
                $cell.addClass('saved-state');

                // Remove saved-state class after 2 seconds
                setTimeout(function() {
                    $cell.removeClass('saved-state');
                }, 2000);

                // Show warning notification
                tostarnotify('top', 'right', 'fa fa-exclamation-triangle', 'form-label label label-warning',
                    'animated flipInX', 'animated flipOutX', res.message || 'Partial update: Main data saved but detail record had an issue', '');
                
                // Log for debugging
                console.warn('Partial Success Response:', res);
                
                // Reload the table after partial success
                setTimeout(function() {
                    reloadAttendanceTable();
                }, 1500);
            }
            // ==============================
            // Error
            // ==============================
            else {
                // Restore original HTML
                $cell.empty().append(originalHtml);
                
                // Show error notification with specific error message
                tostarnotify('top', 'right', 'fa fa-exclamation-circle', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', res.message || 'Failed to save time', '');
                
                // Log error for debugging
                console.error('saveClockTime Error Response:', res);
            }
        },
        error: function(xhr, status, error) {
            // Network or server error
            $cell.empty().append(originalHtml);
            let errorMsg = 'Error saving time';
            let errorIcon = 'fa fa-times-circle';
            
            if (status == 'timeout') {
                errorMsg = 'Request timeout - try again';
            } else if (status == 'error') {
                errorMsg = 'Server error - please try again';
            } else if (status == 'parsererror') {
                errorMsg = 'Invalid response from server';
            }
            
            tostarnotify('top', 'right', errorIcon, 'form-label label label-danger',
                'animated flipInX', 'animated flipOutX', errorMsg, '');

            console.error('saveClockTime AJAX Error:', {
                status: status,
                error: error,
                xhr: xhr,
                responseText: xhr.responseText
            });
        }
    });
}
