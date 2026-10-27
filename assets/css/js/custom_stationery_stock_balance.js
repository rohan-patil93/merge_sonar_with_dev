window.addEventListener("load", function() {
$(document).ready(function(){

    $('#search_stock_balance').on('click', function(){

        var category_ids = $('#category_ids').val();

        $.ajax({
            type: "POST",
            url: "views/custom_stationery_stock_balance_table.php",
            data: {
                category_ids: category_ids
            },
            success: function(res){
                $('#dynamic_table_page').html(res);
            }
        });

    });

});
});