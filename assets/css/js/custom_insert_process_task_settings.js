window.addEventListener("load", function() {
  
   $(document).on("click", ".showemplist", function () {
      var thisval = $(this).val();
      showemplist(thisval);
  });

$(document).on("click", ".showpopup", function () {
      showpopup('newtask');
  });
  
  $(document).on("click", ".deleteRows", function () {
     var thisval = $(this).attr("id");
      deleteRows(thisval);
  }); 
    $(document).on("click", ".submittasksettings", function () {
      submittasksettings();
  });  

  $(document).on("click", ".addstatickRows123", function () {
      addstatickRows123();
  });  

$(document).on("click", ".updateresume", function () {
      updateresume();
  }); 


});

function showemplist(value) {
     if(value=='OB') {
      $("#emplistshow").removeClass("d-none");
      //  document.getElementById('emplistshow').style.display ='block';
     } else {
      $("#emplistshow").addClass("d-none");
      // document.getElementById('emplistshow').style.display ='none';
     }
  }
  function showemplist1(value) {
     if(value=='OB') {
      $("#emplistshowapprovaltype").removeClass("d-none");
       // document.getElementById('emplistshowapprovaltype').style.display ='block';
     } else {
      $("#emplistshowapprovaltype").addClass("d-none");
       //document.getElementById('emplistshowapprovaltype').style.display ='none';
     }
  }
   function showpopup(popup_function_name){

      if(popup_function_name=="newtask"){
         var filter_id = document.getElementById('filtertbl_rowid').value;
         var process_type = document.getElementById('process_type').value;
         
       jQuery.ajax({
            url: "views/custom_insert_filters.php",
            data:'filter_id='+filter_id+'&process_type='+process_type,
            type: "GET",
            success:function(data){
                $('#dynamic_popup_task_settings').html(data);
               // $('#kt_modal_dynamic_popup').modal("show");
                $("#kt_modal_dynamic_popup_task_setting").modal("show");
              //  $(".updaterolemodal").modal("show");
            },
            error:function (){}
        });
     }
     if(popup_function_name=="addfiles"){
       var files = document.getElementById('files').value;
       var sendfile = files;
       jQuery.ajax({
            url: "views/newtasks_addfiles.php",
            data:'filelist='+sendfile,
            type: "GET",
            success:function(data){
                $('#dynamic_popup_task_settings').html(data);
               // $('#kt_modal_dynamic_popup').modal({ show: true});
                 $("#kt_modal_dynamic_popup_task_setting").modal("show");
              
            },
            error:function (){}
        });
     }
  }

function getfile()
{

  var selectedfileslist = document.getElementById('files').value;
  console.log("************************"+selectedfileslist);

   $.ajax({
     url: "ajax.php",
       data:'page=selectedfiles&files='+selectedfileslist,
       type: "POST",
       success: function(response) {
         console.log("response ="+response);
          var division = document.getElementById('divshowfiles');
           division.innerHTML = response;
      }
    });
}

function getnamelist(){
  var selectednames = document.getElementById('filter').value;
   $.ajax({
     url: "ajax.php",
       data:'page=selectednames&names='+selectednames,
       type: "POST",
       success: function(response) {
          var division = document.getElementById('tsk_fortable');
         console.log("response ="+response);
           division.innerHTML = response;
      }
    });
}
function deleteRows(val){
    $("#"+val).remove();
}
function addstatickRows123(){
    var count = $(".sfitemRow").length;
    //alert('la-hand-o-right'+count);
    count++;
    var htmlRows = '';    
    htmlRows += '<div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;">';
    htmlRows += '<div class="col-lg-4">';
    htmlRows += '<input type="text" class="form-control"  id="static_value_'+count+'"  value=""  name="checklist[]">';
    htmlRows += '</div>';

    htmlRows +='<div class="col-lg-1 text-center"><div class="checkbox-fade fade-in-primary"> <label class="form-label"> <input type="checkbox" value="Y" name="mandatory_checklist['+count+']" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></div>';

    htmlRows +='<div class="col-lg-1 text-center"><input type="hidden" name="checkboxid[]" value="'+count+'"><div class="checkbox-fade fade-in-primary"> <label class="form-label"> <input type="checkbox" value="Y" name="attachment_requerd['+count+']" ><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span></label></div></div>';

    htmlRows +='<div class="col-lg-1 text-center"><div class="row"><div class="col-lg-6"><div class="image-input image-input-empty image-input-outline" id="kt_image_5" > <label class="btn-hover-text-primary text-dark" data-action="remove" data-bs-toggle="tooltip" title="Upload File" style="left: -9px;top: 0px;"> <span> <i class="fa fa-upload"></i></span> <input type="file" name="dyanamic_file" accept=".pdf" class="updateresume"  /> </label> </div></div></div></div>';

    htmlRows += '<div class="col-lg-1 text-center">';               
    htmlRows += ' <input type="hidden" value="0" id="static_delete_value_'+count+'" > <i class="fa fa-trash text-danger deleteRows" id="static_div_'+count+'" ></i>';           
    htmlRows += '</div>';
    htmlRows += '</div>';
    $('#append_statick').append(htmlRows);
    $(".js-example-basic-single").select2();
  }


function submittasksettings(){
  var getpopup_id = $("#getpopup_id").val();
  var validations = ajaxFormSubmitValidation();
    if(validations == 0){
      $("#submittasksettings_btn").text("Please Wait......");
      $("#submittasksettings_btn").addClass("disabled");
      var formData = new FormData();
      formData.append("page", "insertprocesstasksettings"); 
      var dataArray = $("#kt_form_submits").serializeArray(),
      dataObj = {};
      $(dataArray).each(function(i, field){
        formData.append(field.name, field.value); 
      });
      $.each($("input[name=dyanamic_file]"), function(i, obj) {
          $.each(obj.files,function(j, file){
              formData.append('file'+i, file);
          })
      });
      jQuery.ajax({
          url: "ajax.php",
          data: formData,
          type: "POST",
          contentType: false,
          processData: false,
          success:function(response){
            console.log(response);
            var result = JSON.parse(response);
            
          if(getpopup_id == 0){
              $('#kt_modal_KTDatatable_remotem_task').modal("hide"); 
               $('#kt_modal_dynamic_popup').modal("hide");   
              //custompopups('<?php echo $_GET['type']; ?>',<?php echo $_GET['workflowid']; ?>);
           }else{ 
                if(result.LASTID == '0'){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Updated Successfully','');
                    location.reload();
                }else{
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Inserted Successfully','');
                    window.location = "index.php?option=COM_LIST_PROCESS_TASK_SETTINGS";
                }  

          }
          }
      });
    }
}

function pageChange(page){
  console.log(page);
  $("#page_url").val("index.php?OPTION="+page);
}
function checkPageInternalLink(){
	 if($("#internal_link").is(':checked')){
	 		console.log("ok");	
	 		$(".internallinkclass").show('slow');	
	 }else{
	 	console.log("no");
	 		$(".internallinkclass").hide();	
	 }
}


function checkPageRedirect(){
    if($("#link_page").is(':checked')){
      $(".hidenoturl").hide('slow');
      $(".hideurl").show('slow');
    }else{
      $(".hideurl").hide('slow');
      $(".hidenoturl").show('slow');
    }
}
