/**
 * Custom Insert ESI JS
 */

/**
 * Dynamic Insert Popup
 */
function dynamicinsertpopup(option) {
    jQuery.ajax({
        url: "views/new_from.php",
        data: 'option=' + option + '&reload_option=COM_INSERT_ESI',
        type: "POST",
        success: function(data) {
            console.log(data);
            $('#popup2').html(data);
            $("#kt_modal_KTDatatable_remote1").modal("show");
        },
        error: function() {
            console.error('Error loading insert form');
        }
    });
}

/**
 * Dynamic Edit Popup
 */
function dynamiceditpopup(option, id) {
    jQuery.ajax({
        url: "views/new_from.php",
        data: 'option=' + option + "&Rid=" + id,
        type: "POST",
        success: function(data) {
            $('#popup2').html(data);
            $("#kt_modal_KTDatatable_remote1").modal("show");
            
            // Initialize Switchery if available
            var elemdefault = document.querySelector('.js-switch');
            if (elemdefault && typeof Switchery !== 'undefined') {
                new Switchery(elemdefault, {
                    color: '#4099ff',
                    jackColor: '#fff',
                    size: 'small'
                });
            }
        },
        error: function() {
            console.error('Error loading edit form');
        }
    });
}

/**
 * Dynamic IPT Popup
 */
function dynamiciptpopup(option, id) {
    jQuery.ajax({
        url: "views/" + option,
        data: 'option=' + option + '&reload_option=COM_INSERT_ESI&Rid=' + id,
        type: "POST",
        success: function(data) {
            console.log(data);
            $('#popup2').html(data);
            $("#kt_modal_KTDatatable_remote1").modal("show");
        },
        error: function() {
            console.error('Error loading form');
        }
    });
}

$(document).ready(function() {
    console.log('ESI Insert - Document ready');
    
    // Initialize Bootstrap tabs if needed
    initTabHandlers();
    
    // Initialize Select2 for all select elements
    if ($.fn.select2) {
        $('select.select2-hidden-accessible').select2();
    }
    
    console.log('ESI Insert - All handlers initialized');
});

/**
 * Initialize tab handlers
 */
function initTabHandlers() {
    // Tab navigation click handlers
    $(document).on('click', '.nav-link', function(e) {
        e.preventDefault();
        var tabHref = $(this).attr('href');
        
        // Remove active class from all tabs and panes
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        
        // Add active class to clicked tab and corresponding pane
        $(this).addClass('active');
        $(tabHref).addClass('show active');
        
        console.log('Tab switched to: ' + tabHref);
    });
}

/**
 * Handle form submissions
 */
$(document).on('submit', 'form', function(e) {
    // Allow normal form submission
    // Add custom validation here if needed
    console.log('Form submitted');
});

/**
 * Handle button clicks for data submission
 */
$(document).on('click', 'button[type="submit"], button[type="reset"]', function(e) {
    console.log('Button clicked: ' + $(this).text());
});

/**
 * Initialize input field handlers
 */
function initInputHandlers() {
    // Currency input formatting
    $(document).on('change', 'input[type="text"][placeholder*="0.00"]', function() {
        var value = $(this).val();
        // Add currency formatting logic here if needed
    });
    
    // Date input handlers
    $(document).on('change', 'input[type="text"][placeholder*="Date"]', function() {
        console.log('Date changed to: ' + $(this).val());
    });
}

/**
 * Utility function to format currency
 */
function formatCurrency(value) {
    return parseFloat(value).toFixed(2);
}

/**
 * Utility function to parse date
 */
function parseDate(dateString) {
    return new Date(dateString);
}
