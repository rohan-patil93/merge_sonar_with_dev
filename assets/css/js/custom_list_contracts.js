window.addEventListener("load", function () {
    $(document).on("click", ".submitfilervtn", function () {
        $("#hide_current_page").val(1);
        getContractList();
    });
    $(document).on("click", ".dynamicContractSelectPage", function () {
        var current_page = $(this).data("current_page");
        $("#hide_current_page").val(current_page);
        getContractList();
    });
});


function getContractList(){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#dyanmictableshow").html(htmloader);
    var formData = $("#formcontractlistpage").serialize();
    jQuery.ajax({
        url: "views/custom_list_contracts_ajax.php",
        data: formData+"&filterajax=Y",
        type: "POST",
        success:function(data){
            $("#dyanmictableshow").html(data);
        },
        error:function (){}
    });
}
