window.addEventListener("load", function() {
$(document).ready(function() {
    $('#search_available_items').on('click', function() {
        var category_ids = $('#category_ids').val();
        console.log("Data category_ids:"+category_ids);
        $.ajax({
            type: "POST",
            url: "views/custom_stationery_available_items_table.php",
            data: {
                category_ids: category_ids
            },
            success: function(res) {
                $('#dynamic_table_page').html(res);
            }
        });
    });
});
});