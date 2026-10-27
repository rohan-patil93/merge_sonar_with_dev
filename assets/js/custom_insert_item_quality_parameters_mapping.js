$(document).on('click', '.job-category-checkbox', function () {
    var catid = $(this).data('catid');
    jobcate(catid);
});

function jobcate(catid) {
    if ($("#cat_" + catid).prop("checked")) {
        $("#cate_" + catid).removeClass("d-none");
    } else {
        $("#cate_" + catid).addClass("d-none");
    }
}

$(document).ready(function () {
    $('.item-select').on('change', function () {
        var itemid = $(this).val();
        var catid = $('#item_cat_id').val();
        var process = $('#process').val();

        var url = "index.php?option=COM_INSERT_ITEM_QUALITY_PARAMETERS_MAPPING";
        url += "&ITEMID=" + itemid;
        url += "&CATID=" + catid;
        url += "&PROCESS=" + process;

        window.location = url;
    });
});

$(document).ready(function () {
    var catid = $('#item_cat_id').val();
    var selectedItem = $('#item_cat_id').data('selected-item') || '0';

    loadItemsByCategory(catid, selectedItem);

    $('#item_cat_id').on('change', function () {
        var catid = $(this).val();
        // category changed manually, so reset item to All
        loadItemsByCategory(catid, '0');
    });

});

function loadItemsByCategory(catid,selectedItem) {
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: {
            page: "get_items_by_category",
            item_cat_id: catid,
            item_id:selectedItem
        },
        success: function (response) {
            $('#item_id').html(response);
            $('#item_id').val(selectedItem).trigger('change.select2');
        }
    });
}