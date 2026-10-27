window.addEventListener("load", function() {

  

  $(document).on("blur", ".checkEmailidAllReadyAvilableOrNot", function () {
        checkEmailidAllReadyAvilableOrNot();
    });

  $(document).on("click", ".removeOrganizationSetup", function () {
    var rowid = $(this).data("rowid");
    removeOrganizationSetup(rowid);
  });

$(document).on("click", ".addMoreInvitation", function () {
        addMoreInvitation();
    });

  
  
});



var maxvendors = 100;
function addMoreInvitation(){
    var application_roles_html_option = $("#application_roles_html_option").html();
    maxvendors = maxvendors+1;
    var rowid = 'rowdivision_'+maxvendors; 
    var errorvendorrowid = 'errorrowvendor_'+maxvendors; 
    var vendorrowid = 'rowvendor_'+maxvendors;
    var email_id = "email_id";

    //count++;
    var htmlRows = '<div class="row mt-3" id="'+rowid+'">';    
    
    htmlRows += '<div class="col-lg-3"><input type="text" class="form-control" name="first_name[]" required="" ><input type="hidden" name="user_type[]" value="1"></div>';
    
    htmlRows += ' <div class="col-lg-3"><input type="text" class="form-control" name="last_name[]" required="" autocomplete="off"></div>';
    
     htmlRows += '<div class="col-lg-3"><input type="text" class="form-control allemails checkEmailidAllReadyAvilableOrNot" name="user_email[]" data-errorcls="'+errorvendorrowid+'" data-errorcls1="validrepeateemailerror_'+maxvendors+'" id="'+vendorrowid+'" required="" autocomplete="off"><span class="text-danger f-12 allemailerrer" id="'+errorvendorrowid+'"  style="display:none">Email Id Already Register</span><span class="text-danger f-12 allemailerrer" id="validrepeateemailerror_'+maxvendors+'"  style="display:none">Please Enter Valid Email Id</span></div>';
    
    //htmlRows += '<div class="col-lg-3"><input type="text" class="form-control allemails" name="user_email[]" data-errorcls="'+errorvendorrowid+'" id="'+vendorrowid+'"  onblur="checkemailidallreadyavilableornot(\''+vendorrowid+'\',\''+email_id+'\',\''+errorvendorrowid+'\')" required="" ><span class="text-danger f-12" id="'+errorvendorrowid+'"  style="display:none">Email Id Already Register</span></div>';
    
    htmlRows += '<div class="col-lg-2"><select  class ="form-select js-example-basic-single" name="role_id[]" required="" >';
    htmlRows += application_roles_html_option;
    htmlRows += '</select></div>';
    
    htmlRows += '<div class="col-lg-1"><i class="ti-trash text-danger removeOrganizationSetup" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove"  data-rowid="'+rowid+'"></i></div></div>';
    
    htmlRows += '</tr>';
    $('#append_invitatiouser').append(htmlRows);
     $(".js-example-basic-single").select2();
    $('[data-bs-toggle="tooltip"]').tooltip();
  }
  function deleteRows(id){
      $(".delectedrow_"+id).remove();  
  }
  
  function checkMailidAvailableOrNot(email_id,rowid){
    var usedemailids = $("#usedemailids").val();
    var data  = JSON.parse(usedemailids);
    $(".invitemailid").each(function(index) {
        var arrarowid = $(this).attr("data-rowid");
        if(rowid != arrarowid){
            data.push($(this).val());
        }
    });
    var emailid = $.trim(email_id);
    if ($.inArray(emailid, data) != -1)
    {      
        $("#iduser_email_"+rowid).show();
        $("#inviteuser_btn").attr("disabled", true);
       // console.log("match - "+emailid);
    }else{
        $("#iduser_email_"+rowid).hide();
        $("#inviteuser_btn").removeAttr("disabled");
       // console.log("Not match - "+emailid);
    }
  }

  function checkEmailidAllReadyAvilableOrNot(){
    $(".allemailerrer").addClass("d-none");
    $("#submituserbtn").removeClass("disabled");
    var poststop = 0;
    var posterrerdata = [];
    $(".allemails").each(function(index) {
      var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if(!emailReg.test($(this).val())){
        var validationemail = $(this).attr("data-errorcls1"); 
       // $("#"+validationemail).show();
        $("#"+validationemail).removeClass('d-none');
        poststop = 1;
        $("#submituserbtn").addClass("disabled");
      }else{
        console.log('No');
      }
      if($(this).val() !=""){
        var arrarowid = $(this).attr("data-errorcls");
        var emailval = $(this).val();
        if($.inArray(emailval, posterrerdata)>=0){
          $("#"+arrarowid).removeClass('d-none');
          $("#submituserbtn").addClass("disabled");
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
                      $("#submituserbtn").addClass("disabled");
                    }else{
                      console.log("not match - "+emailval);
                    }
                  }
              });
            },
            error:function (){}
        });
    }
  }
/**
  function checkemailidallreadyavilableornot(id,field,type){
    var value = $("#"+id).val(); 
    
    console.log(posterrerdata);
    console.log("id - "+id+" field - "+field+" type - "+type+" value - "+value);
    $(".allemailshide").hide();
    $("#submituserbtn").removeClass("disabled");
      $.ajax({
          url: "r_ajax_o.php",
          data:'page=checkemailidallreadyavilableornot&domain='+value+'&field='+field+"&postemails="+posterrerdata,
          type: "POST",
          success:function(data){
            console.log(data);
            var res = JSON.parse(data);
            $("#"+type).hide();
            if(res.res == 'true'){
              $("#"+type).show();
               $("#submituserbtn").addClass("disabled");
            }else{
              var errerdata = [];
              $(".allemails").each(function(index) {
                  var arrarowid = $(this).attr("data-errorcls");
                  if(type !=arrarowid){
                    errerdata.push($(this).val());
                  }
              });
           
              if($.inArray(value, errerdata)>=0){
                  $("#"+type).show();
                  $("#submituserbtn").addClass("disabled");
              }
            }
          },
          error:function (){}
      });        
  }  **/
function removeOrganizationSetup(idd){
    $("#"+idd).remove();
  }