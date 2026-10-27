 function loginemailpopup(){
    jQuery.ajax({
        url: "emailmessagesendpopup.php",
        data:'option=option',
        type: "POST",
        success:function(data){
            $('#dynamic_popup2').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
        },
        error:function (){}
    });
}
$(document).on("click", "#mnpopup", function() {
  alert("Clicked!");
});