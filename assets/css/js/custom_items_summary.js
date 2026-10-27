window.addEventListener("load", function() {
    
    
    $(document).off("click", ".itemSummaryfilterTable").on("click", ".itemSummaryfilterTable", function () {
        var status = $(this).data('status');
        itemSummaryfilterTable(status);
    });
    
    $(document).off("click", ".itemSummaryRefreshPage").on("click", ".itemSummaryRefreshPage", function () {
        var cid = $(this).data('cid');
        var dnm = $(this).data('dnm');
        var udid = $(this).data('udid');
        itemSummaryRefreshPage(cid,dnm,udid);
    });

});

function itemSummaryfilterTable(status) {
    const rows = document.querySelectorAll('.stock-row');
    rows.forEach(row => {
        if (status === 'all') {
            row.classList.remove('d-none'); // show all
        } else {
            if (row.classList.contains('row-' + status)) {
                row.classList.remove('d-none'); // show matched
            } else {
                row.classList.add('d-none'); // hide others
            }
        }
    });
}
function itemSummaryRefreshPage(cid,dnm,udid) {
	var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
	$("#itemssummary").html(loader);
    $.ajax({
        url: "apis/custom_items_summary.php",
        data:'company_id='+cid+'&data_name='+dnm+'&user_id='+udid+'&refresh=Y',
        type: "GET",
        success:function(data){
            $("#itemssummary").html(data);
        },
        error:function (){}
    });


}