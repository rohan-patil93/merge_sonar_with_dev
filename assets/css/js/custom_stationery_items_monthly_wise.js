window.addEventListener("load", function() {
$(document).ready(function(){
    $('#search_monthly_report').on('click', function(){
        var category_ids = $('#category_ids').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.ajax({
            type: "POST",
            url: "views/custom_stationery_items_monthly_wise_table.php",
            data: {
                category_ids: category_ids,
                from_date: from_date,
                to_date: to_date
            },
            success: function(res){
                $('#dynamic_table_page').html(res);
            }
        });

    });

});
});