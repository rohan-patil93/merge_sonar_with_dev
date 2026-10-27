window.addEventListener("load", function() {
$(document).ready(function(){

    $('#search_employee_issuance').on('click', function(){

        var employee_ids = $('#employee_ids').val();

        $.ajax({
            type: "POST",
            url: "views/custom_stationery_items_employee_wise_issuance_table.php",
            data: {
                employee_ids: employee_ids
            },
            success: function(res){
                $('#dynamic_table_page').html(res);
            }
        });

    });

});
});