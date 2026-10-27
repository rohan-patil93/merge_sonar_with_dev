window.addEventListener("load", function() {
$(document).ready(function(){
    $('#search_total_issued').on('click', function(){
        $.ajax({
            type: "POST",
            url: "views/custom_total_stationery_items_issued_table.php",
            data: {
                category_ids : $('#category_ids').val(),
                item_ids     : $('#item_ids').val(),
                from_date    : $('#from_date').val(),
                to_date      : $('#to_date').val()
            },
            success: function(res){
                $('#dynamic_table_page').html(res);
            }
        });

    });

});
});