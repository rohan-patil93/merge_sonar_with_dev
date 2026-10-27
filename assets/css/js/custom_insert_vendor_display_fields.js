window.addEventListener("load", function() {
  $(document).on("change", ".selecteRole", function () {
      let value = $(this).val();
      selecteRole(value);
  });

  $(document).on("click", ".accessDashSetting", function () {
      var fieldgrpname = $(this).data("fieldgrpname");
      var seq = $(this).data("seq");
      var type = $(this).data("type");
      accessDashSetting(fieldgrpname, seq, type);
  });

   $(document).on("click", ".accessFormSetting", function () {
      var fieldgrpname = $(this).data("fieldgrpname");
      var seq = $(this).data("seq");
      var type = $(this).data("type");
      accessFormSetting(fieldgrpname, seq, type);
  });

$(document).on("click", ".accessFormfieldSetting", function () {
      var fieldname = $(this).data("fieldname");
      var fieldgrpname = $(this).data("fieldgrpname");
      var seq = $(this).data("seq");
      var type = $(this).data("type");
      accessFormfieldSetting(fieldname,fieldgrpname,seq,type);
  }); 
}); 

  function accessDashSetting(fieldgrpname,seq,type){
    if(type == "access"){
      $("#dash-"+fieldgrpname+"-"+seq+"-label").html("<i class='ti-unlock'></i> Access");
      $("#dash-hidden-"+fieldgrpname+"-"+seq).val('Y');

         // $("#dash-"+fieldgrpname+"-"+seq+"-div").removeClass("bg-danger");
       }else{
        $("#dash-"+fieldgrpname+"-"+seq+"-label").html("<i class='ti-lock text-danger'></i> No Access");
        $("#dash-hidden-"+fieldgrpname+"-"+seq).val('N');
          //$("#dash-"+fieldgrpname+"-"+seq+"-div").addClass("bg-danger")
        }
        $(".dash-"+fieldgrpname+"-"+seq).removeClass('d-none');
        $("#dash-"+fieldgrpname+"-"+seq+"-"+type).addClass('d-none');
      }

  function accessFormSetting(fieldgrpname,seq,type){
    console.log(type);
    $(".empty-"+fieldgrpname).removeClass("text-warning");
    $(".allempty-"+fieldgrpname).removeClass("text-warning");
    $("#fieldgrpaccess-hidden-"+fieldgrpname).val(type);
    $(".fieldgrpaccess-hidden-"+fieldgrpname).val(type);
    if(type == "NA"){
      $(".form-"+fieldgrpname+"-label").html("<i class='ti-lock text-danger'></i> No Access");
      $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-lock text-danger'></i> No Access");

      $("#field_noaccess-"+fieldgrpname).addClass("text-warning");

    }else if(type == "E"){
      $(".form-"+fieldgrpname+"-label").html("<i class='ti-pencil'></i> Edit");
      $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-pencil'></i> Edit");
      $("#field_edit-"+fieldgrpname).addClass("text-warning");
    }else{
      $(".form-"+fieldgrpname+"-label").html("<i class='ti-eye'></i> View Only");
      $(".allformfield-"+fieldgrpname+"-label").html("<i class='ti-eye'></i> View Only");
      $("#field_viewonly-"+fieldgrpname).addClass("text-warning");
    }
  }
  function accessFormfieldSetting(fieldname,fieldgrpname,seq,type,value=""){
    console.log(fieldname,"  --- ",fieldgrpname,"  --- ",seq,"  --- ",type);
    $(".emptyfield-"+fieldname+"-"+seq).removeClass("text-warning");
    $(".empty-"+fieldgrpname).removeClass("text-warning");
    $(".form-"+fieldgrpname+"-label").html("Access Varies");
    $("#fieldaccess-hidden-"+fieldname+"-"+seq).val(type);

    $("#fieldgrpaccess-hidden-"+fieldgrpname).val(0);
    if(type == "NA"){
      $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-lock text-danger'></i> No Access");
      $("#field_noaccess-"+fieldname+"-"+seq).addClass("text-warning");
    }else if(type == "E"){
      $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-pencil'></i> Edit");
      $("#field_edit-"+fieldname+"-"+seq).addClass("text-warning");
    }else{
      $("#formfield-"+fieldname+"-"+seq+"-label").html("<i class='ti-eye'></i> View Only");
      $("#field_viewonly-"+fieldname+"-"+seq).addClass("text-warning");
    }

  }
  function selecteRole(value){
    window.location.href = "index.php?option=COM_INSERT_VENDOR_DISPLAY_FIELDS&ROLE="+value;
  }
