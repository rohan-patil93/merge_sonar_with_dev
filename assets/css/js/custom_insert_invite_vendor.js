window.addEventListener("load", function() {
   $(document).on("blur", ".checkCompanyNameAllReadyAvilableOrNot", function () {
        checkCompanyNameAllReadyAvilableOrNot();
    });
  $(document).on("blur", ".checkEmailidAllReadyAvilableOrNot", function () {
        checkEmailidAllReadyAvilableOrNot();
    });   

  $(document).on("click", ".sendInvitation", function () {
        sendInvitation();
    }); 

  var hid_vendor_registration_requests_id = $("#hid_vendor_registration_requests_id").val();
  if(hid_vendor_registration_requests_id !=""){
     checkEmailidAllReadyAvilableOrNot();
    checkCompanyNameAllReadyAvilableOrNot(); 
  }

});


function sendInvitation(){
   var validations = ajaxFormSubmitValidation();
      if(validations == 0){
        $("#send_invitation_form").submit();
      } 
      //$("#send_invitation_form").submit();
  }    
  function checkEmailidAllReadyAvilableOrNot(){
    $(".allemailerrer").addClass('d-none');
    //$("#invitevendorbtn").removeClass("disabled");
    $("#user_email_validate").val(0);         
    var poststop = 0;
    var posterrerdata = [];
    $(".allemails").each(function(index) {
      var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if(!emailReg.test($(this).val())){
        var validationemail = $(this).attr("data-errorcls1"); 
        console.log(validationemail);
        $("#"+validationemail).removeClass("d-none");
        poststop = 1;
        $("#user_email_validate").val(1);
        console.log('yes');
      }else{
        console.log('No');
      }
      if($(this).val() !=""){
        var arrarowid = $(this).attr("data-errorcls");
        var emailval = $(this).val();
        if($.inArray(emailval, posterrerdata)>=0){
          $("#"+arrarowid).removeClass("d-none");
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
                      $("#"+arrarowid).removeClass("d-none");
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
    var company_name = $("#company_name").val();
  	var company_name_validate = $("#company_name_validate").val();
  	var user_email_validate = $("#user_email_validate").val();
  	console.log("company_name_validate"+ company_name_validate);
  	console.log("user_email_validate"+ user_email_validate);
    $("#allreadycompanyname").addClass('d-none');
  	$("#invitevendorbtn").removeClass("disabled");
  	if(company_name_validate == '0' && user_email_validate == '0'){
  		$("#invitevendorbtn").removeClass("disabled");
  	}else{
      console.log(company_name_validate);
      if(company_name ==""){
        $("#invitevendorbtn").addClass("disabled");
      }else if(company_name_validate == '1'){
  	 		$("#allreadycompanyname").removeClass("d-none");
  	 		$("#invitevendorbtn").addClass("disabled");
  	 	}else{
  	 		$("#allreadycompanyname").addClass('d-none');
  	 	}
  	 	if(user_email_validate == '1'){
  	 		$("#invitevendorbtn").addClass("disabled");
  	 	}
  	}
  } 