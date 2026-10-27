$( ".coursetype" ).click(function() {
       $(".coursetype").removeClass("btn-outline-info");
      $(".coursetype").addClass("btn-outline-info");
      $(this).removeClass("btn-outline-info");
      $(this).addClass("btn-info");
      // /alert($(this).attr("data-id"));
      $("#employee_mode").val($(this).attr("data-id"));
});

function deleteQuestion(id){
       $(".rows"+id).remove();
}

// var message = $("#message").val();
//    toastr.options = {
//   "closeButton": false,
//   "debug": false,
//   "newestOnTop": false,
//   "progressBar": false,
//   "positionClass": "toast-top-right",
//   "preventDuplicates": false,
//   "onclick": null,
//   "showDuration": "300",
//   "hideDuration": "1000",
//   "timeOut": "5000",
//   "extendedTimeOut": "1000",
//   "showEasing": "swing",
//   "hideEasing": "linear",
//   "showMethod": "fadeIn",
//   "hideMethod": "fadeOut"
// };
// if(message == 'post'){
//        var lastid = $("#message").attr("data-id");
// if(lastid == "0"){ 
//        toastr.success("Record Update Successfully");
// }else{
//        toastr.success("Record Inserted Successfully");

// }
// }

     function addvideoextrafileds(){
      $(".video").empty();
         $(".video").append($("#video_url").val());
         $("#collapseOne4").addClass('collapse show');
     }
      var max = 500;
      function addQuestionsTextFileds(){
         max++;
         $("#videoquestiontextfileds").append('<div class="row rows'+max+'"><input class="form-control mt-2 col-lg-10" type="text" name="question[]" value="" placeholder="Eg. Tell me about yourself"><i title="Delete Question" onclick="deleteQuestion('+max+')" class="text-dark-50 flaticon-delete col-lg-2 mt-4"></i></div> ');
     }


function selectJobTitle(){
    $('.note-codable').attr('name','description');
   var id = $(".selectjobtitle").val();
   $.post("ajax.php",{'page':'getjobdecription','id':id},function(data){
        var datas = jQuery.parseJSON(data);
        $("#job_name").val(datas[0]['job_title']);
        $("#summa_ifr").contents().find("#tinymce").html(datas[0]['job_description']);
        $("#textediters").html(datas[0]['job_description']);
        $("#noteguidelines").html(datas[0]['job_description']);
   }); 
} 

function maxamountemplye(value){
    value = value.split("Rs.");
    value = value[1].replaceAll(",", '');
    var number_of_employee = $("#number_of_employee").val();
    var total = value*number_of_employee;
    var output=parseInt(total).toLocaleString()+".00";
   $("#annual_salary").val("Rs. "+output);
  //$("#annual_salary").val(output);
   // alert(output);
}





// Inputmask.extendAliases({
//   pesos: {
//             groupSeparator: ".",
//             alias: "numeric",
//             placeholder: "0",
//             autoGroup: true,
//             digits: 2,
//             digitsOptional: false,
//             clearMaskOnLostFocus: false
//         }
// });
// $(document).ready(function(){
//   $(".inr_amount").inputmask({ alias : "currency", prefix: 'Rs. ' });
// });