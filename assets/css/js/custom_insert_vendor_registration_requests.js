window.addEventListener("load", function() {


 $(document).on("blur", ".checkCompanyNameAllReadyAvilableOrNot", function () {
        checkCompanyNameAllReadyAvilableOrNot();
    });


 $(document).on("blur", ".checkEmailidAllReadyAvilableOrNot", function () {
        checkEmailidAllReadyAvilableOrNot();
    });

$(document).on("click", ".buttonEnableOrDisable", function () {
        buttonEnableOrDisable();
    });

  $(document).on("click", ".onchangeQuestionAnswer", function () {
      var thval = $(this).val();
      onchangeQuestionAnswer(thval);
  });


$(document).on("click", ".submittopupproductrfrom", function () {
      submittopupproductrfrom();
  });

 $(document).on("click", ".sendInvitation", function () {
  var sendinvtype = $(this).data("sendinvtype");
      sendInvitation(sendinvtype);
  });

$(document).on("change", ".SelectVendorTypes", function () {
  var vendor_types = $(this).val();
      SelectVendorTypes(vendor_types);
  });




$(document).on("change", ".selectedcategory", function () {
        var selected_id = $(this).val();
       var element_id = $(this).data("element_id");
       var table_name = $(this).data("table_name");
       var field_name = $(this).data("field_name");
        selectedcategory(selected_id, element_id, table_name, field_name);
    });

});

function SelectVendorTypes(vendor_types){
  if(vendor_types == 2){
    $('#read_policy').prop('checked', true);
    $(".read_policy_div").addClass('d-none');
    checkEmailidAllReadyAvilableOrNot();
    checkCompanyNameAllReadyAvilableOrNot();
  }else{
    $(".read_policy_div").removeClass('d-none');
  }

}

function sendInvitation(stype){
  	$("#submit_type").val(stype);
   	var validations = ajaxFormSubmitValidation();
      if(validations == 0){
      	if(stype == 'SE'){
	  		submittopupproductrfrom();
	  	}else{
	        var postdata = $("#send_invitation_form").serialize();
	        jQuery.ajax({
	            url: "views/custom_insert_vendor_registration_request_popup.php",
	            data: postdata+"&viewpost=Y",
	            type: "POST",
	            success:function(data){
	                $('#dynamic_popup11').html(data);
	                $('#kt_modal_dynamic_popup2').modal("show");
	                $('[data-bs-toggle="tooltip"]').tooltip();
	            }
	        });
	    }
        
        //$("#send_invitation_form").submit();
      } 
  } 
  function submittopupproductrfrom(){
  		$("#send_invitation_form").submit();
  }   
  function checkEmailidAllReadyAvilableOrNot(){
    $(".allemailerrer").addClass("d-none");
    //$("#invitevendorbtn").removeClass("disabled");
    $("#user_email_validate").val(0);         
    var poststop = 0;
    var posterrerdata = [];
    $(".allemails").each(function(index) {
      var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if(!emailReg.test($(this).val())){
        var validationemail = $(this).attr("data-errorcls1"); 
        console.log(validationemail);
        $("#"+validationemail).removeClass('d-none');
        poststop = 1;
        console.log('yes');
      }else{
        console.log('No');
      }
      if($(this).val() !=""){
        var arrarowid = $(this).attr("data-errorcls");
        var emailval = $(this).val();
        if($.inArray(emailval, posterrerdata)>=0){
          $("#"+arrarowid).removeClass('d-none');
          //$("#invitevendorbtn").addClass("disabled");
           $("#user_email_validate").val(1);
                   
          poststop = 1;
        }else{
           posterrerdata.push($(this).val());
        }
      }
    });
    if(poststop == '0'){
      $.ajax({
            url: "ajax.php",
            data:'page=checkemailidallreadyavilableornot&email_ids='+posterrerdata,
            type: "POST",
            async: false,
            success:function(data){
              var res = JSON.parse(data);
              var allreadyavailable = res.application_users;
              console.log(allreadyavailable);
              $(".allemails").each(function(index){
                  if($(this).val() !=""){
                    var arrarowid = $(this).attr("data-errorcls");
                    var emailval = $(this).val();
                    if($.inArray(emailval, allreadyavailable)>=0){
                      $("#"+arrarowid).removeClass('d-none');
                      console.log("match - "+emailval);
                      $("#user_email_validate").val(1);
                    }else{
                      console.log("not match - "+emailval);
                    }
                  }
              });
            },
            error:function (){}
        });
    }
    buttonEnableOrDisable();
  }
  function checkCompanyNameAllReadyAvilableOrNot(){
  	var company_name = $("#company_name").val();
  	if(company_name !=""){
  	 $.ajax({
            url: "ajax.php",
            data:'page=checkcompanynameallreadyavilableornot&company_name='+company_name,
            type: "POST",
            async: false,
            success:function(data){
            	console.log(data);
              var res = JSON.parse(data);
              if(res.company_name == '1'){
              	console.log('already');
              	$("#company_name_validate").val(1);
              }else{
              	console.log('frash');
              	$("#company_name_validate").val(0);
              } 
            },
            error:function (){}
        });
      setTimeout(function() { 
       buttonEnableOrDisable();
    }, 700);
  	 
  	}

  }
  function buttonEnableOrDisable(){
  	var company_name_validate = $("#company_name_validate").val();
  	var user_email_validate = $("#user_email_validate").val();
  	console.log("company_name_validate"+ company_name_validate);
  	console.log("user_email_validate"+ user_email_validate);
    $("#allreadycompanyname").addClass("d-none");
  	$(".invitevendorbtn").removeClass("disabled");
  	if(company_name_validate == '0' && user_email_validate == '0'){
  		$(".invitevendorbtn").removeClass("disabled");
      if(!$("#read_policy").is(":checked")) {
        $(".invitevendorbtn").addClass("disabled");
      }
      if(!$("#termsandcondition_3").is(":checked")) {
        $(".invitevendorbtn").addClass("disabled");
      }

  	}else{
      console.log(company_name_validate);
  	 	if(company_name_validate == '1'){
        if($("#company_name").val() ==""){

        }else{
          $("#allreadycompanyname").removeClass('d-none');
        }
  	 		$(".invitevendorbtn").addClass("disabled");
  	 	}else{
  	 		$("#allreadycompanyname").addClass("d-none");
  	 	}
  	 	if(user_email_validate == '1'){
  	 		$(".invitevendorbtn").addClass("disabled");
  	 	}
  	}
  } 
  function selectedcategory(selected_id,element_id,table_name,field_name,targetelement="") {
  var selectedvalues = selected_id;
                if(table_name=='states' || table_name=='cities'){
                 $.ajax({
                  url: "ajax.php",
                  data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues+'&field='+field_name,
                  type: "POST",
                  success: function(response) {
                    var response = JSON.parse(response);
                    console.log(response.length);
                    console.log(response);
                    var select = document.getElementById(element_id); 
                    document.getElementById(element_id).innerHTML = "";
                    for (var i = 0; i < response.length; i++) 
                    {
                     var el = document.createElement("option");
                     if(table_name=='states'){
                      el.textContent = response[i].state_name;
                      el.value = response[i].id;

                    }
                    if(table_name=='cities'){
                      el.textContent = response[i].city_name;
                      el.value = response[i].id;

                    }
                    select.add(el);
                  }
                }
              });
               }

             }

function onchangeQuestionAnswer(valuess){
  if(valuess == 'Y'){
    $("#registration_questions_7_yes").removeClass('d-none');
    $("#registration_questions_7_no").addClass("d-none");
  }else{
    $("#registration_questions_7_yes").addClass("d-none");
    $("#registration_questions_7_no").removeClass('d-none');
  } 
} 