/**
 * Month year change event handler
 */

// Attach event listener using jQuery for Select2 compatibility
$(document).ready(function() {
    console.log('Document ready - initializing event listeners');
    
    // Attach change listener for att_month_year using jQuery (required for Select2 compatibility)
    $('#att_month_year').on('change', function() {
        console.log('Month year changed:', $(this).val());
        togetvistplanlist($(this).val());
    });
});

function togetvistplanlist(ym) {
    $.ajax({
        url: "views/custom_filed_employee_tab_ajax.php",
        data:'year_month='+ym,
        type: "POST",
        success:function(data){
            $('#tablist').html(data);
        }
    }); 
}