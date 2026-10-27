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
    $(document).on("click", "#downloadContractsPDF", function () {
        downloadContractsListPDF();
    });
    
});


function getContractList(){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#dyanmictableshow").html(htmloader);
    var formData = $("#formcontractlistpage").serialize();
    jQuery.ajax({
        url: "views/custom_microchip_list_contracts_ajax.php",
        data: formData+"&filterajax=Y",
        type: "POST",
        success:function(data){
            $("#dyanmictableshow").html(data);
        },
        error:function (){}
    });
}


function downloadContractsListPDF() {
    var formData = $("#formcontractlistpage").serialize();
    var $btn = $("#downloadContractsPDF");
    $btn.text("Generating...");
    $.ajax({
        url: "views/custom_microchip_list_contracts_ajax.php",
        type: "POST",
        data: formData + "&filterajax=Y&pdfexport=Y",
        success: function (htmlContent) {
            $.ajax({
                url: "convert_html_to_pdf.php",
                type: "POST",
                data: { html_content: htmlContent, title: "Contract_List", orientation: "landscape" },
                success: function (pdfdetail) {
                    var result = JSON.parse(pdfdetail);
                    var link = document.createElement("a");
                    link.href = result.pdf_url;
                    link.download = result.pdf_name;
                    link.style.display = "none";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $.post("convert_html_to_pdf.php", { removepdf: result.pdf_name });
                    $btn.text("PDF");
                },
                error: function () {
                    $btn.text("PDF");
                }
            });
        },
        error: function () {
            $btn.text("PDF");
        }
    });
}