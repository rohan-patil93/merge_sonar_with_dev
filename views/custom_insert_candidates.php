  <?php
$_SESSION['apply_js']['tag'] = 1;
$_SESSION['apply_js']['todo'] = "1";
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];

$url = API."?mode=getcandidatedetails&Rid=".$candidate_id."&role_short_code=".$_SESSION['role_ids'][0];
$response = callAPI($url);
$result = json_decode($response,1);
$candidates = $result['candidates'];
$blood_group = $result['blood_group'];
foreach($blood_group as $blood_group){
  $bloodgroup[$blood_group['id']] = $blood_group['blood_group'];
}
$vendors = $result['vendors'];
foreach($vendors as $vendor){
  $vendorlist[$vendor['id']] = $vendor['display_name'];
}
$locations = $result['locations'];
foreach($locations as $location){
  $locationlist[$location['id']] = $location['location_name'];
}

$job_grades = $result['job_grade'];
foreach($job_grades as $job_grade){
  $jobgradelist[$job_grade['id']] = $job_grade['job_grade_name'];
}
$job_grade_templates = $result['job_grade_templates'];
foreach($job_grade_templates as $job_grade_templatesre){
  $jobgradetemplatelist[$job_grade_templatesre['id']] = $job_grade_templatesre['name'];
}
$relations = $result['relations'];
foreach($relations as $relation){
  $relationlist[$relation['id']] = $relation['relation_name'];
}
$educationtype = $result['educationtype'];
foreach($educationtype as $education){
  $edutypedata[$education['genc_detail_code']] = $education['genc_detail_name'];
}

foreach($result['genders'] as $gender){
  $genders[$gender['genc_detail_code']] = $gender['genc_detail_name'];
}


foreach($result['present_house'] as $gender){
  $present_house[$gender['genc_detail_code']] = $gender['genc_detail_name'];
}

foreach($result['jobtype'] as $jobtypes){
  $jobtype[$jobtypes['genc_detail_code']] = $jobtypes['genc_detail_name'];
}

foreach($result['relocate'] as $relocate1){
  $relocate[$relocate1['genc_detail_code']] = $relocate1['genc_detail_name'];
}

foreach($result['maritalstatus'] as $maritalstatus1){
  $maritalstatus[$maritalstatus1['id']] = $maritalstatus1['marital_status_name'];
}

foreach($result['religions'] as $maritalstatus1){
  $religions[$maritalstatus1['id']] = $maritalstatus1['religion_name'];
}
foreach($result['castes'] as $maritalstatus1){
  $castes[$maritalstatus1['id']] = $maritalstatus1['caste_name'];
}




$job_posting_candidates = $result['job_posting_candidates'];
$job_postingslist = $result['job_postings'];
$candidate_display_fields = $result['candidate_display_fields'];
$accessrightarry = json_decode($candidate_display_fields['field_list'],1);




  $display_file = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_id."/Documents";





  ?>
  <script type="text/javascript">
    var cntmax = 80000;
    function adddeprows(){
        cntmax++;
      var id =  "'removeeducation"+cntmax+"'";
       var htmlRows = '';
       htmlRows += '<tr id='+id+'>';
        htmlRows += '<td><input aria-label="Dependent Name[]" type="text" name="dependent_name[]"  class="form-control " autocomplete="off">  </td>';
        htmlRows += '<td><select aria-label="Dependent Relationship Type[]" class="form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value="0"> Select Any One</option> <?php foreach ($relationlist as $relationlistkey => $reladata) {   ?> <option   value="<?php echo $relationlistkey; ?>"> <?php echo $reladata; ?></option> <?php
} ?></select>  </td>';
       htmlRows += '<td><input aria-label="Dependent Date Of Birth[]" type="text" name="dependent_date_of_birth[]"  class="form-control datepicker" autocomplete="off">  </td>';
       htmlRows += '<td><i class="fa fa-trash text-danger"></i></td>';
       htmlRows += '</tr>';
       $('#deptable').append(htmlRows);
       $( ".datepicker" ).datepicker({
          showOn: "both",
          buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
          dateFormat: date_format,
          changeMonth: true,
          changeYear: true
        });
    }
    var cntmax = 90000;
    function addawardrows(){
       cntmax++;
      var id =  "'removeeducation"+cntmax+"'";
       var htmlRows = '';
       htmlRows += '<tr id='+id+_2'>';
       htmlRows += '<td><input aria-label="Award Name[]" type="text" name="award_name[]"  class="form-control " autocomplete="off">  </td>';
       htmlRows += '<td><input aria-label="Award Description[]" type="text" name="award_description[]"  class="form-control " autocomplete="off">  </td>';
       htmlRows += '<td><input aria-label="Award Year[]" type="month" name="award_year[]"  class="form-control"  autocomplete="off">  </td>';
       htmlRows += '<td><button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="Remove" onclick="removedRow('+id+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></td>';
       htmlRows += '</tr>';
       $('#awdtable').append(htmlRows);
    }
    var cntmax = 10000;
    function addexpreencerows(){
      cntmax++;
      var id =  "'removeeducation"+cntmax+"'";
       var htmlRows = '';
       htmlRows += '<tr id='+id+_3'>';
       htmlRows += '<td><input aria-label="Company Name[]" type="text" name="company_name[]"  class="form-control " autocomplete="off">  </td>';
       htmlRows += '<td><input aria-label="Job Descrption[]" type="text" name="job_descrption[]"  class="form-control " autocomplete="off">  </td>';
       htmlRows += '<td><input aria-label="Work From[]" type="text" name="work_from[]"  class="form-control datepicker"  autocomplete="off">  </td>';
       htmlRows += '<td><input aria-label="Work To[]" type="text" name="work_to[]"  class="form-control datepicker"   autocomplete="off">  </td>';
     htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="left:-5px;top:-15px;">  <span class="svg-icon svg-icon-primary svg-icon-lg" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-upload"></i></span><input aria-label="Dyanamic File" type="file" name="dyanamic_file"  class="qualification_file"/>  <input type="hidden" name="hidden_file[]" value=""/> </label> <button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="Remove" onclick="removedRow('+id+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></div></td>'
     htmlRows += '</tr>';
     $('#extable').append(htmlRows);
     $( ".datepicker" ).datepicker({
          showOn: "both",
          buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
          dateFormat: date_format,
          changeMonth: true,
          changeYear: true
        });
   }
   var cntmax = 11000;
   function addedurows(){
    cntmax++;
      //  count++;

     var id =  "'removeeducation"+cntmax+"'";
      var htmlRows = '';
      htmlRows += '<tr id='+id+_4'>';
      htmlRows += '<td><input aria-label="Institute Name[]" type="text" name="institute_name[]"  class="form-control " autocomplete="off">  </td>';
      htmlRows += '<td><select aria-label="Education Type" class="form-select js-example-basic-single" name="education_type" > <option value="0"> Select Any One</option> <?php foreach ($edutypedata as $educode=> $edutype) {  ?> <option   value="<?php echo $educode; ?>"> <?php echo $edutype; ?></option> <?php
} ?></select> </td>';
      htmlRows += '<td><input aria-label="Start Date[]" type="text" name="start_date[]"  class="form-control datepicker"  autocomplete="off">  </td>';
      htmlRows += '<td><input aria-label="End Date[]" type="text" name="end_date[]"  class="form-control datepicker"   autocomplete="off">  </td>';
      htmlRows += '<td><input aria-label="Score[]" type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';
      htmlRows += '<td><input aria-label="Subject[]" type="text" name="subject[]"  class="form-control " autocomplete="off">  </td>';
      htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5_2" ><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="left:-5px;top:-15px;"> <span class="" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-upload"></i> </span><input aria-label="Dyanamic File" type="file" name="dyanamic_file" />  <input type="hidden" name="hidden_file[]" value=""/> </label> <button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="Remove" onclick="removedRow('+id+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></div></td>'

      htmlRows += '</tr>';
      $('#edutable').append(htmlRows);
      $( ".datepicker" ).datepicker({
          showOn: "both",
          buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
          dateFormat: date_format,
          changeMonth: true,
          changeYear: true
        });
    }


    function cancelChange(hideclass,showclass,hide_btn,show_btn){
        $("."+hideclass).hide();
        $("."+showclass).show('slow');
        $("#"+hide_btn).hide();
        $("#"+show_btn).show('slow');
        $("#"+hide_btn+"_undo").hide();
        $(".datepickershow_"+hideclass).hide();
    }

    function editFormShow(hideclass,showclass,hide_btn,show_btn,submit_type,form_name=""){
        var getoption  = '<?php echo $_GET['option']; ?>';
        var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
        $("."+hideclass).hide();
        $("."+showclass).show('slow');
        $("#"+hide_btn).hide();
        $("#"+show_btn).show('slow');
        $("#"+hide_btn+"_undo").hide();
        $("#"+show_btn+"_undo").show('slow');
        if(submit_type == "Edit"){
            $(".datepickershow_"+showclass).show();
        }
        if(submit_type == "Save"){
        $(".datepickershow_"+hideclass).hide();
        $("."+hideclass).each(function(index) {
            var type = $(this).attr("data-type");
            if(type == 'select'){
              if($(this).val() != "" && $(this).val() !='0'){
                var value  = $(this).find('option:selected').text();
              }
            }else{
              var value = $(this).val();
            }
            if(value ==""){
                value  = "-";
            }
            $("."+showclass+":eq("+index+")").html(value);
            console.log(type+"---"+value);
        });
        var candidate_id = '<?php echo $candidate_id; ?>';
        var postarray = $("#"+form_name).serialize();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertcandidatedetails&candidate_id='+candidate_id+'&option='+getoption+'&extra_paramiter='+extra_paramiter+'&'+postarray,
            success: function(response){
                console.log(response);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');

            }
        });
      }


    }





    function editFrom(formtype,divid,editsubmit){
      var getoption  = '<?php echo $_GET['option']; ?>';
      var candidate_id = '<?php echo $candidate_id; ?>';
      var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
      if(editsubmit == 'edit'){
        $("#"+divid+"_edit").hide();
        $("#"+divid+"_save").show('slow');
        $("#"+divid+"_save_undo").show('slow');
        if(formtype =='education'){
           $("#educationajax_popup").addClass("div-covert-popup");
        }
        if(formtype =='workexperience'){
           $("#workexperiencejax_popup").addClass("div-covert-popup");
        }
      }else{
        $("#"+divid+"_save").hide();
        $("#"+divid+"_edit").show('slow');
        $("#"+divid+"_save_undo").hide();
        if(formtype =='education'){
           $("#educationajax_popup").removeClass("div-covert-popup");
        }
        if(formtype =='workexperience'){
           $("#workexperiencejax_popup").removeClass("div-covert-popup");
        }
      }
      $.ajax({
          url: "views/custom_insert_candidates_ajax.php",
          type: "POST",
          data: 'edittype='+formtype+'&editsubmit='+editsubmit+'&candidate_id='+candidate_id+'&option='+getoption+'&ajax=Y&extra_paramiter='+extra_paramiter,
          success: function(response){

              $("#"+divid).html(response);
              $('[data-bs-toggle="tooltip"]').tooltip();
              $( ".datepicker" ).datepicker({
                showOn: "both",
                buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                dateFormat: date_format,
                changeMonth: true,
                changeYear: true
              });
              console.log(response);
          }
      });
    }
    //         url: "ajax.php",
    //         type: "POST",
    //         data: 'page=insertcandidatedetails&candidate_id='+candidate_id+'&'+postarray,
    function submitFromTable(classid,formtype,divid,editsubmit){
      var getoption  = '<?php echo $_GET['option']; ?>';
        var candidate_id = '<?php echo $candidate_id; ?>';
        var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
        var formData = new FormData();
        formData.append("page", "insertcandidatedetails");
        formData.append("candidate_id", candidate_id);
        formData.append("option", getoption);
        formData.append("extra_paramiter", extra_paramiter);
        var dataArray = $("#"+classid).serializeArray(),
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
               editFrom(formtype,divid,editsubmit);
               tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');

            }
        });





        //     url: "ajax.php",
        //     type: "POST",
        //     data: 'page=insertcandidatedetails&candidate_id='+candidate_id+'&'+postarray,
    }
function assignedCandidateFiletr(cand_id){
    jQuery.ajax({
        url: "views/custom_insert_assigned_candidate_job_posting_popup.php",
        data:'cand_id='+cand_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}

    function removedRow(id){
        $("#"+id).remove();
    }

  function maritalstatuschange(valu){
    if(valu == 2){
      $(".marital_option_select").show();
      $(".maritalremovedclass").removeClass("personal_detailsL");
      $(".maritalremovedclass").removeClass("datepickershow_personal_detailsT");
      $(".maritalremovedclass").removeClass("personal_detailsT");

    }else{
      $(".marital_option_select").hide();
    }
  }
  function NewMessageAdded(carddiv){
    $(".custom-popup-open").hide();
    $("."+carddiv).show('slow');
    //     url: "views/custom_insert_project_logs.php",
    //     data:'ajaxcall=Y&candidate_id='+candidate_id,
    //     type: "POST",
  }
  function closedPopupcompliance22(){
        $(".custom-popup-open").hide();
        console.log("closed");
      }
  </script>
  <div class="row mt-3">
    <div class="col-lg-8">
        <div class="card" >
          <?php $fieldaccesright = $accessrightarry['contact_details'];
          if($fieldaccesright['access'] !="NA"){
          ?>
          <div id="Contact_Details">
          <div class="card-header" >
            <h5> Contact Details </h5>
            <div class="card-header-right" >
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" onclick="editFormShow('contact_detailsL','contact_detailsT','edit_contact_details','save_contact_details','Edit')" id="edit_contact_details" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="save_contact_details_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('contact_detailsT','contact_detailsL','save_contact_details','edit_contact_details')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_contact_details" onclick="editFormShow('contact_detailsT','contact_detailsL','save_contact_details','edit_contact_details','Save','contactdetailsfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
            </div>
          </div>
          <div class="card-body border-bottom ">

            <form id="contactdetailsfrom">
              <div class="row">
                <?php
                    if($fieldaccesright['field']['first_name']!="NA"){
                       $readonly = $fieldaccesright['field']['first_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                  <span class="form-label f-w-600">First Name</span>
                  <p class="contact_detailsL"><?php echo $candidates['first_name']; ?></p>
                  <input aria-label="Input field" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text"  type="text" name="first_name"  value="<?php echo $candidates['first_name']; ?>" style="display: none">
                </div>
              <?php }
                    if($fieldaccesright['field']['middle_name']!="NA"){
                       $readonly = $fieldaccesright['field']['middle_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Middle Name</span>
                  <p class="contact_detailsL"><?php echo $candidates['middle_name']; ?> </p>
                  <input aria-label="Input field" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text"  type="text"  id="middle_name" name="middle_name"  value="<?php echo $candidates['middle_name']; ?>" style="display: none">
                </div>
              <?php }
                    if($fieldaccesright['field']['last_name']!="NA"){
                       $readonly = $fieldaccesright['field']['last_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Last Name</span>
                  <p class="contact_detailsL"><?php echo $candidates['last_name']; ?> </p>
                  <input aria-label="Input field" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text"  type="text"  id="last_name" name="last_name"  value="<?php echo $candidates['last_name']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['display_name']!="NA"){
                       $readonly = $fieldaccesright['field']['display_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Display Name</span>
                  <p class="contact_detailsL"> <?php echo $candidates['display_name']; ?> </p>
                   <input aria-label="Input field" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text" type="text"  id="display_name" name="display_name"  value="<?php echo $candidates['display_name']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['phone']!="NA"){
                       $readonly = $fieldaccesright['field']['phone'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Phone</span>
                  <p class="contact_detailsL"> <?php echo DynamicPhoneNumberFormt($candidates['phone']); ?> </p>
                  <input aria-label="Input field" class="form-control contact_detailsT <?php echo $readonly; ?>"  data-type="text" type="text"  id="phone" name="phone"  value="<?php echo $candidates['phone']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['email_id']!="NA"){
                       $readonly = $fieldaccesright['field']['email_id'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Email Id</span>
                  <p class="contact_detailsL"> <?php echo $candidates['email_id']; ?></p>
                  <input aria-label="Input field" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text"  type="text"  id="email_id" name="email_id"  value="<?php echo $candidates['email_id']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['address']!="NA"){
                       $readonly = $fieldaccesright['field']['address'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Permanent Address</span>
                  <p class="contact_detailsL"> <?php echo $candidates['address']; ?> </p>
                  <textarea aria-label="Text area" type="text" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text" id="address" name="address" style="display: none"><?php echo $candidates['address']; ?></textarea>
                </div>
                <?php }
                    if($fieldaccesright['field']['present_address']!="NA"){
                       $readonly = $fieldaccesright['field']['present_address'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Present Address</span>
                  <p class="contact_detailsL"> <?php echo $candidates['present_address']; ?> </p>
                  <textarea aria-label="Text area" type="text" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text" id="present_address" name="present_address" style="display: none"><?php echo $candidates['present_address']; ?></textarea>
                </div>

                <?php }
                    if($fieldaccesright['field']['post_code']!="NA"){
                       $readonly = $fieldaccesright['field']['post_code'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Post Code</span>
                  <p class="contact_detailsL"> <?php echo $candidates['post_code']; ?> </p>
                  <input aria-label="Input field" class="form-control contact_detailsT <?php echo $readonly; ?>"  data-type="text" type="text"  id="post_code" name="post_code"  value="<?php echo $candidates['post_code']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['bio']!="NA"){
                       $readonly = $fieldaccesright['field']['bio'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-12 form-group">
                  <span class="form-label f-w-600">Bio</span>
                  <p class="contact_detailsL"> <?php echo $candidates['bio']; ?> </p>
                  <textarea aria-label="Text area" type="text" class="form-control contact_detailsT <?php echo $readonly; ?>" data-type="text" id="bio" name="bio" style="display: none"><?php echo $candidates['bio']; ?></textarea>
                </div>
                <?php }  ?>
              </div>
            </form>
          </div>
        </div>
      <?php } ?>
      <?php $fieldaccesright = $accessrightarry['personal_details'];
          if($fieldaccesright['access'] !="NA"){
          ?>
        <div id="Personal_Details">
          <div class="card-header">
            <h5> Personal Details  </h5>
            <div class="card-header-right">
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" onclick="editFormShow('personal_detailsL','personal_detailsT','edit_personal_details','save_personal_details','Edit')" id="edit_personal_details" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="save_personal_details_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('personal_detailsT','personal_detailsL','save_personal_details','edit_personal_details')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_personal_details" onclick="editFormShow('personal_detailsT','personal_detailsL','save_personal_details','edit_personal_details','Save','personaldetailsfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
            </div>
          </div>
          <div class="card-body border-bottom ">
            <form id="personaldetailsfrom">
              <div class="row">
                <?php
                    if($fieldaccesright['field']['gender']!="NA"){
                       $readonly = $fieldaccesright['field']['gender'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600 ">Gender</span>
                  <p class="personal_detailsL"><?php echo  $genders[$candidates['gender']]; ?></p>
                  <select aria-label="Select option" class="form-select js-example-basic-single personal_detailsT <?php echo $readonly; ?>" data-type="select" id="gender" name="gender" style="display: none">
                  <?php foreach($genders as $genderkey=>$genderval){ ?>
                      <option <?php if($candidates['gender'] == $genderkey){  ?> selected <?php
} ?>  value="<?php echo $genderkey; ?>"><?php echo $genderval;  ?></option>
                  <?php } ?>
                 </select>
                </div>
                <?php }
                    if($fieldaccesright['field']['date_of_birth']!="NA"){
                       $readonly = $fieldaccesright['field']['date_of_birth'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Date Of Birth</span>
                  <p class="personal_detailsL"><?php echo fromDate($candidates['date_of_birth']); ?> </p>
                   <div class="datepickershow_personal_detailsT <?php echo $readonly; ?>" style="display: none">
                      <input aria-label="Input field" type="text" class="form-control datepicker personal_detailsT" data-type="text" value="<?php echo $candidates['date_of_birth']; ?>"  name="date_of_birth"   style="display: none"/>
                    </div>
                </div>
                <?php }
                    if($fieldaccesright['field']['marital_status']!="NA"){
                       $readonly = $fieldaccesright['field']['marital_status'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                  <span class="form-label f-w-600">Marital Status</span>
                  <p class="personal_detailsL"><?php echo $maritalstatus[$candidates['marital_status']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single personal_detailsT <?php echo $readonly; ?>" data-type="select" id="marital_status" name="marital_status" onchange="maritalstatuschange(this.value)" style="display: none">
                     <option value=""> Select Any One</option>
                     <?php foreach($maritalstatus as $marrkey=>$marrval){ ?>
                      <option <?php if($candidates['marital_status'] == $marrkey){  ?> selected <?php
} ?>  value="<?php echo $marrkey; ?>"><?php echo $marrval;  ?></option>
                  <?php } ?>
                 </select>
                </div>
                <?php }
                    if($fieldaccesright['field']['religion']!="NA"){
                       $readonly = $fieldaccesright['field']['religion'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                  <span class="form-label f-w-600">Religion</span>
                  <p class="personal_detailsL"><?php echo $religions[$candidates['religion']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single personal_detailsT <?php echo $readonly; ?>" data-type="select" id="religion" name="religion" onchange="maritalstatuschange(this.value)" style="display: none">
                     <option value=""> Select Any One</option>
                     <?php foreach($religions as $marrkey=>$marrval){ ?>
                      <option <?php if($candidates['religion'] == $marrkey){  ?> selected <?php
} ?>  value="<?php echo $marrkey; ?>"><?php echo $marrval;  ?></option>
                  <?php } ?>
                 </select>
                </div>
                <?php }
                    if($fieldaccesright['field']['caste']!="NA"){
                       $readonly = $fieldaccesright['field']['caste'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                  <span class="form-label f-w-600">Caste</span>
                  <p class="personal_detailsL"><?php echo $religions[$candidates['caste']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single personal_detailsT <?php echo $readonly; ?>" data-type="select" id="caste" name="caste" onchange="maritalstatuschange(this.value)" style="display: none">
                     <option value=""> Select Any One</option>
                     <?php foreach($castes as $marrkey=>$marrval){ ?>
                      <option <?php if($candidates['caste'] == $marrkey){  ?> selected <?php
} ?>  value="<?php echo $marrkey; ?>"><?php echo $marrval;  ?></option>
                  <?php } ?>
                 </select>
                </div>
                 <?php }
                    if($fieldaccesright['field']['present_house']!="NA"){
                       $readonly = $fieldaccesright['field']['  present_house'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group" >
                  <span class="form-label f-w-600">Present House</span>
                  <p class="personal_detailsL"><?php echo $religions[$candidates['present_house']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single personal_detailsT <?php echo $readonly; ?>" data-type="select" id="present_house" name="present_house" onchange="maritalstatuschange(this.value)" style="display: none">
                     <option value=""> Select Any One</option>
                      <?php foreach($present_house as $genderkey=>$genderval){ ?>
                      <option <?php if($candidates['present_house'] == $genderkey){  ?> selected <?php
} ?>  value="<?php echo $genderkey; ?>"><?php echo $genderval;  ?></option>
                  <?php } ?>
                 </select>
                </div>
                <?php }
                    if($fieldaccesright['field']['wedding_date']!="NA"){
                       $readonly = $fieldaccesright['field']['wedding_date'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group marital_option_select">
                  <span class="form-label f-w-600">Anniversary Date</span>
                  <p class="personal_detailsL maritalremovedclass" > <?php echo fromDate($candidates['wedding_date']); ?> </p>
                   <div class="datepickershow_personal_detailsT maritalremovedclass <?php echo $readonly; ?>" style="display: none">
                   <input aria-label="Input field" type="text" class="form-control datepicker personal_detailsT " data-type="text"  value="<?php echo $candidates['wedding_date']; ?>"  name="wedding_date"  style="display: none"/>
                 </div>
                </div>
                <?php }
                    if($fieldaccesright['field']['blood_group']!="NA"){
                       $readonly = $fieldaccesright['field']['blood_group'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600 ">Blood Group</span>
                  <p class="personal_detailsL"> <?php echo $bloodgroup[$candidates['blood_group']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single personal_detailsT <?php echo $readonly; ?>" data-type="select"  name="blood_group" style="display: none">
                     <option value=""> Select Any one</option>
                     <?php foreach ($bloodgroup as $bloodkey=>$bloodval) { ?>
                       <option <?php if($candidates['blood_group'] == $bloodkey){ ?>selected <?php
} ?> value="<?php echo $bloodkey; ?>" >
                        <?php echo $bloodval; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
                <?php }
                    if($fieldaccesright['field']['father_name']!="NA"){
                       $readonly = $fieldaccesright['field']['father_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Father Name</span>
                  <p class="personal_detailsL"> <?php echo $candidates['father_name']; ?></p>
                  <input aria-label="Input field" class="form-control personal_detailsT <?php echo $readonly; ?>"  type="text"  data-type="text" id="father_name" name="father_name"   value="<?php echo $candidates['father_name']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['pan_number']!="NA"){
                       $readonly = $fieldaccesright['field']['pan_number'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Pan Number</span>
                  <p class="personal_detailsL"> <?php echo $candidates['pan_number']; ?> </p>
                  <input aria-label="Input field" class="form-control personal_detailsT <?php echo $readonly; ?>"  type="text" data-type="text" id="pan_number" name="pan_number"   value="<?php echo $candidates['pan_number']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['spouse_name']!="NA"){
                       $readonly = $fieldaccesright['field']['spouse_name'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group marital_option_select">
                  <span class="form-label f-w-600">Spouse Name</span>
                  <p class="personal_detailsL maritalremovedclass"> <?php echo $candidates['spouse_name']; ?> </p>
                  <input aria-label="Input field" class="form-control personal_detailsT maritalremovedclass <?php echo $readonly; ?>"  type="text"  data-type="text" id="spouse_name" name="spouse_name"   value="<?php echo $candidates['spouse_name']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['adhar_no']!="NA"){
                       $readonly = $fieldaccesright['field']['adhar_no'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Adhar Number</span>
                  <p class="personal_detailsL"> <?php echo $candidates['adhar_no']; ?> </p>
                  <input aria-label="Input field" class="form-control personal_detailsT <?php echo $readonly; ?>"  type="text"  data-type="text" id="adhar_no" name="adhar_no"   value="<?php echo $candidates['adhar_no']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['identification_mark']!="NA"){
                       $readonly = $fieldaccesright['field']['identification_mark'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-6 form-group">
                  <span class="form-label f-w-600"> Identification Mark  </span>
                  <p class="personal_detailsL"> <?php echo $candidates['identification_mark']; ?> </p>
                  <input aria-label="Input field" class="form-control personal_detailsT <?php echo $readonly; ?>"  type="text" data-type="text" id="identification_mark" name="identification_mark"   value="<?php echo $candidates['identification_mark']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['hobbies']!="NA"){
                       $readonly = $fieldaccesright['field']['hobbies'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-6 form-group">
                  <span class="form-label f-w-600">Hobbies</span>
                  <p class="personal_detailsL"> <?php echo $candidates['hobbies']; ?> </p>
                  <input aria-label="Input field" class="form-control personal_detailsT <?php echo $readonly; ?>"  type="text" data-type="text" id="hobbies" name="hobbies"   value="<?php echo $candidates['hobbies']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['height']!="NA"){
                       $readonly = $fieldaccesright['field']['height'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Height</span>
                  <p class="personal_detailsL"> <?php echo $candidates['height']; ?> </p>
                  <input aria-label="Input field" class="form-control personal_detailsT <?php echo $readonly; ?>"  type="text" data-type="text" id="height" name="height"   value="<?php echo $candidates['pan_number']; ?>" style="display: none">
                </div>
                <?php }
                    if($fieldaccesright['field']['weight']!="NA"){
                       $readonly = $fieldaccesright['field']['weight'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Weight</span>
                  <p class="personal_detailsL"> <?php echo $candidates['weight']; ?> </p>
                  <input aria-label="Input field" class="form-control personal_detailsT <?php echo $readonly; ?>"  type="text" data-type="text" id="weight" name="weight"   value="<?php echo $candidates['weight']; ?>" style="display: none">
                </div>

                <?php }
                    if($fieldaccesright['field']['hiring_source']!="NA"){
                       $readonly = $fieldaccesright['field']['hiring_source'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Hiring Source</span>
                  <p class="personal_detailsL"> <?php echo $vendorlist[$candidates['hiring_source']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single personal_detailsT <?php echo $readonly; ?>" data-type="select"  id="kt_select2_41" name="hiring_source" style="display: none">
                     <option value="0"> Select Any One </option>
                     <?php foreach ($vendorlist as $vendorkey => $vender) { ?>
                       <option <?php if($candidates['hiring_source'] == $vendorkey){  ?> selected <?php
} ?>  value="<?php echo $vendorkey ?>"><?php echo $vender; ?> </option>
                     <?php } ?>
                   </select>
                </div>
                <?php } ?>
              </div>
            </form>
          </div>
        </div>
      <?php } ?>
      <?php $fieldaccesright = $accessrightarry['dependents'];

          if($fieldaccesright['access'] !="NA"){
          ?>
        <div id="Dependents">

          <div class="card-header">
            <h5> Dependents  </h5>
            <div class="card-header-right">
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" id="dependancyajax_edit" onclick="editFrom('dependancy','dependancyajax','edit')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="dependancyajax_save_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="editFrom('dependancy','dependancyajax','save')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20"  onclick="submitFromTable('dependancyform','dependancy','dependancyajax','save')" id="dependancyajax_save" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" style="display: none">Save</button>
            </div>
          </div>
          <?php
              if($fieldaccesright['field']['employee_dependents']!="NA"){
                  $readonly = $fieldaccesright['field']['employee_dependents'] !="E"?"custom_readonly":"";
          ?>
          <div class="card-body border-bottom <?php echo $readonly; ?>" id="dependancyajax">
               <?php $_POST['edittype'] = "dependancy";
               $pageurl = "custom_insert_candidates_ajax.php";
                    if(file_exists($pageurl)){
                      include_once $pageurl;
                    }else{
                      include_once "views/".$pageurl;
                     } ?>
          </div>
        <?php } ?>
        </div>
      <?php } ?>
      <?php $fieldaccesright = $accessrightarry['current_employment'];
          if($fieldaccesright['access'] !="NA"){
          ?>
        <div id="Current_Employment">
          <div class="card-header">
            <h5> Current Employment  </h5>
            <div class="card-header-right">
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" onclick="editFormShow('current_employementL','current_employementT','edit_current_employement','save_current_employement','Edit')" id="edit_current_employement" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="save_current_employement_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('current_employementT','current_employementL','save_current_employement','edit_current_employement')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_current_employement" onclick="editFormShow('current_employementT','current_employementL','save_current_employement','edit_current_employement','Save','currentemployementfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
            </div>
          </div>
           <div class="card-body border-bottom ">
            <form id="currentemployementfrom">
              <div class="row">
                <?php
                  if($fieldaccesright['field']['employee_contacts']!="NA"){
                    $readonly = $fieldaccesright['field']['employee_contacts'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Contacts Number</span>
                  <p class="current_employementL"><?php echo $candidates['employee_contacts']; ?></p>
                  <input aria-label="Input field" class="form-control current_employementT <?php echo $readonly; ?>" data-type="text"  type="text"  id="employee_contacts" name="employee_contacts"  value="<?php echo $candidates['employee_contacts']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['current_location']!="NA"){
                    $readonly = $fieldaccesright['field']['current_location'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Current Location</span>
                  <p class="current_employementL"><?php echo $candidates['current_location']; ?> </p>
                  <input aria-label="Input field" class="form-control current_employementT <?php echo $readonly; ?>" data-type="text"  type="text"  id="current_location" name="current_location"  value="<?php echo $candidates['current_location']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['reason_for_change']!="NA"){
                    $readonly = $fieldaccesright['field']['reason_for_change'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Reason For Change</span>
                  <p class="current_employementL"><?php echo $candidates['reason_for_change']; ?> </p>
                  <input aria-label="Input field" class="form-control current_employementT <?php echo $readonly; ?>" data-type="text"  type="text"  id="reason_for_change" name="reason_for_change"  value="<?php echo $candidates['reason_for_change']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['relocate']!="NA"){
                    $readonly = $fieldaccesright['field']['relocate'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Relocate</span>
                  <p class="current_employementL"> <?php echo $relocate[$candidates['relocate']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single current_employementT <?php echo $readonly; ?>" data-type="select"  name="relocate" style="display: none">
                     <option value="0"> Select Any One </option>
                     <?php foreach ($relocate as $relocatekey => $relocateval) { ?>
                       <option <?php if($candidates['relocate'] == $relocatekey){  ?> selected <?php
} ?>  value="<?php echo $relocatekey ?>"><?php echo $relocateval; ?> </option>
                     <?php } ?>
                   </select>
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['current_ctc']!="NA"){
                    $readonly = $fieldaccesright['field']['current_ctc'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Current CTC</span>
                  <p class="current_employementL"> <?php echo customeummarmonyformateifine($candidates['current_ctc']); ?> </p>
                  <input aria-label="Input field" class="form-control current_employementT <?php echo $readonly; ?>"  type="text" data-type="text"   id="current_ctc" name="current_ctc"  value="<?php echo $candidates['current_ctc']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['expected_ctc']!="NA"){
                    $readonly = $fieldaccesright['field']['expected_ctc'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Expected CTC</span>
                  <p class="current_employementL"> <?php echo customeummarmonyformateifine($candidates['expected_ctc']); ?></p>
                  <input aria-label="Input field" class="form-control current_employementT <?php echo $readonly; ?>"  type="text" data-type="text"  id="expected_ctc" name="expected_ctc"  value="<?php echo $candidates['expected_ctc']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['no_of_reportees']!="NA"){
                    $readonly = $fieldaccesright['field']['no_of_reportees'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Number of Reportes</span>
                  <p class="current_employementL"> <?php echo $candidates['no_of_reportees']; ?> </p>
                  <input aria-label="Input field" class="form-control current_employementT <?php echo $readonly; ?>"  type="text" data-type="text"  id="no_of_reportees" name="no_of_reportees"  value="<?php echo $candidates['no_of_reportees']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['job_grade']!="NA"){
                    $readonly = $fieldaccesright['field']['job_grade'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Job Grad</span>
                  <p class="current_employementL"> <?php echo $jobgradelist[$candidates['job_grade']]; ?> </p>
                  <select aria-label="Select option" class="form-select js-example-basic-single current_employementT <?php echo $readonly; ?>" data-type="select"  name="job_grade" onchange="gradechange(this.value)" style="display: none">
                     <option value="0"> Select Any One</option>
                     <?php foreach ($jobgradelist as $jobgradkey =>$jobgradval) { ?>
                       <option  <?php if($candidates['job_grade'] == $jobgradkey){  ?> selected <?php
} ?> value="<?php echo $jobgradkey; ?>"><?php echo $jobgradval; ?></option>
                     <?php  } ?>
                   </select>
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['pay_template']!="NA"){
                    $readonly = $fieldaccesright['field']['pay_template'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Pay template</span>
                  <p class="current_employementL"> <?php echo $jobgradetemplatelist[$candidates['pay_template']]; ?> </p>
                  <select aria-label="Select option"   class="form-select js-example-basic-single current_employementT <?php echo $readonly; ?>"  data-type="select"  name="pay_template" id="grade" style="display: none">
                   <option value="0"> Select Any One</option>
                   <?php foreach ($jobgradetemplatelist as $jobgradetemplatekey=> $jobgradetemplateval) { ?>
                     <option  <?php if($candidates['pay_template'] == $jobgradetemplatekey){  ?> selected <?php
} ?> value="<?php echo $jobgradetemplatekey; ?>"><?php echo $jobgradetemplateval; ?></option>
                   <?php  } ?>
                 </select>
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['job_type']!="NA"){
                    $readonly = $fieldaccesright['field']['job_type'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Job Type</span>
                  <p class="current_employementL"> <?php echo $jobtype[$candidates['job_type']]; ?> </p>
                  <select aria-label="Select option" class="form-control js-example-basic-single current_employementT <?php echo $readonly; ?>" data-type="select" name="job_type" onchange="filterapplay()" style="display: none">
                    <?php foreach($jobtype as $jobk=>$jobtype1){ ?>
                      <option value="<?php echo $jobk; ?>" <?php echo $candidates['job_type']==$jobk?"selected":""; ?>><?php echo $jobtype1; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['location_id']!="NA"){
                    $readonly = $fieldaccesright['field']['location_id'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Working Location</span>
                  <p class="current_employementL"> <?php echo $locationlist[$candidates['location_id']]; ?> </p>
                  <select aria-label="Select option" class="form-control js-example-basic-single current_employementT <?php echo $readonly; ?>" name="location_id" data-type="select" onchange="filterapplay()" style="display: none">
                    <?php foreach($locations as $location){ ?>
                      <option value="<?php echo $location['id']; ?>" <?php echo $candidates['location_id']==$location['id']?"selected":""; ?>><?php echo $location['location_name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <?php  } ?>

              </div>
              </form>
          </div>
        </div>
      <?php } ?>
         <?php $fieldaccesright = $accessrightarry['awards_and_accolades'];
          if($fieldaccesright['access'] !="NA"){
          ?>
        <div id="Awards_and_Accolades">
          <div class="card-header">
            <h5> Awards and Accolades  </h5>
            <div class="card-header-right">
               <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" id="awardajax_edit" onclick="editFrom('award','awardajax','edit')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
               <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="awardajax_save_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="editFrom('award','awardajax','save')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20"  onclick="submitFromTable('awardform','award','awardajax','save')" id="awardajax_save" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" style="display: none">Save</button>
            </div>
          </div>
          <?php if($fieldaccesright['field']['candidate_awards']!="NA"){
                    $readonly = $fieldaccesright['field']['candidate_awards'] !="E"?"custom_readonly":"";
          ?>
          <div class="card-body border-bottom <?php echo $readonly; ?>" id="awardajax">
              <?php $_POST['edittype'] = "award";
              $pageurl = "custom_insert_candidates_ajax.php";
                    if(file_exists($pageurl)){
                      include_once $pageurl;
                    }else{
                      include_once "views/".$pageurl;
                    } ?>
          </div>
        <?php } ?>
        </div>
      <?php } ?>
        <?php $fieldaccesright = $accessrightarry['hr_details'];
          if($fieldaccesright['access'] !="NA"){ ?>
        <div id="HR_Details">
          <div class="card-header">
            <h5> HR Details </h5>
            <div class="card-header-right">
               <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" onclick="editFormShow('hr_detailL','hr_detailT','edit_hr_detail','save_hr_detail','Edit')" id="edit_hr_detail" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="save_hr_detail_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="cancelChange('hr_detailT','hr_detailL','save_hr_detail','edit_hr_detail')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20" id="save_hr_detail" onclick="editFormShow('hr_detailT','hr_detailL','save_hr_detail','edit_hr_detail','Save','hrdetailfrom')" style="display: none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save">Save</button>
            </div>
          </div>
           <div class="card-body border-bottom ">
            <form id="hrdetailfrom">
              <div class="row">
                <?php
                  if($fieldaccesright['field']['first_role']!="NA"){
                    $readonly = $fieldaccesright['field']['first_role'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">First Role</span>
                  <p class="hr_detailL"><?php echo $candidates['first_role']; ?></p>
                  <input aria-label="Input field" class="form-control hr_detailT <?php echo $readonly; ?>"  type="text"  id="first_role" name="first_role"  value="<?php echo $candidates['first_role']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['candidate_current_role']!="NA"){
                    $readonly = $fieldaccesright['field']['candidate_current_role'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Current Role</span>
                  <p class="hr_detailL"><?php echo $candidates['candidate_current_role']; ?> </p>
                   <input aria-label="Input field" class="form-control hr_detailT <?php echo $readonly; ?>"  type="text"  id="candidate_current_role" name="candidate_current_role"  value="<?php echo $candidates['candidate_current_role']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['notice_period']!="NA"){
                    $readonly = $fieldaccesright['field']['notice_period'] !="E"?"custom_readonly":"";
                ?>
                <div class="form-group col-lg-2">
                    <span class="form-label">Notice Period</span>
                    <p class="hr_detailL"><?php echo $candidates['notice_period']; ?> </p>
                    <div class="input-group input-icon-right input-group-button datepickershow_hr_detailT" style="display: none">
                     <input aria-label="Input field" class="form-control hr_detailT width1 <?php echo $readonly; ?>"  type="text"  id="notice_period" name="notice_period"  value="<?php echo $candidates['notice_period']; ?>" style="display: none">
                      <span class="input-group-text addon-style">
                      <span class="">Days</span>
                  </span>
                   </div>
                 </div>
                 <?php
                  }
                  if($fieldaccesright['field']['np_negotiatable']!="NA"){
                    $readonly = $fieldaccesright['field']['np_negotiatable'] !="E"?"custom_readonly":"";
                ?>
                   <div class="form-group col-lg-2">
                    <span class="mb-3 form-label">Negotiable</span>
                    <p class="hr_detailL"><?php echo $candidates['np_negotiatable'] == '0'?"Yes":"No"; ?> </p>
                      <select aria-label="Select option"   class="form-select js-example-basic-single hr_detailT <?php echo $readonly; ?>"  data-type="select"  name="np_negotiatable" style="display: none">
                        <option  <?php  if($candidates['np_negotiatable'] == '1'){ ?> selected <?php
} ?> value="<?php echo '1'; ?>">No</option>
                        <option  <?php  if($candidates['np_negotiatable'] == '0'){ ?> selected <?php
} ?> value="<?php echo '0'; ?>">Yes</option>
                   </select>
                  </div>
                  <?php
                  }
                  if($fieldaccesright['field']['first_pay']!="NA"){
                    $readonly = $fieldaccesright['field']['first_pay'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">First Pay</span>
                  <p class="hr_detailL "><?php echo $candidates['first_pay']; ?> </p>
                    <div class="input-group input-icon-right input-group-button datepickershow_hr_detailT " style="display: none">
                      <input aria-label="Input field" class="form-control hr_detailT text-right width1 <?php echo $readonly; ?>"  type="text"  id="first_pay" name="first_pay"  value="<?php echo $candidates['first_pay']; ?>" style="display: none">
                      <span class="input-group-text addon-style">
                        <span class="">Monthly</span>
                      </span>
                    </div>
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['current_pay']!="NA"){
                    $readonly = $fieldaccesright['field']['current_pay'] !="E"?"custom_readonly":"";
                ?>
                 <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Current Pay</span>
                  <p class="hr_detailL"><?php echo $candidates['current_pay']; ?> </p>
                  <div class="input-group input-icon-right input-group-button datepickershow_hr_detailT" style="display: none">
                    <input aria-label="Input field" class="form-control hr_detailT text-right width1 <?php echo $readonly; ?>"  type="text"  id="current_pay" name="current_pay"  value="<?php echo $candidates['current_pay']; ?>" style="display: none">
                    <span class="input-group-text addon-style">
                        <span class="">Monthly</span>
                      </span>
                    </div>
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['gaps_in_employment_reason']!="NA"){
                    $readonly = $fieldaccesright['field']['gaps_in_employment_reason'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Gap in employment And Reason</span>
                  <p class="hr_detailL"><?php echo $candidates['gaps_in_employment_reason']; ?> </p>
                  <input aria-label="Input field" class="form-control hr_detailT <?php echo $readonly; ?>"  type="text"  id="gaps_in_employment_reason" name="gaps_in_employment_reason"  value="<?php echo $candidates['gaps_in_employment_reason']; ?>" style="display: none">
                </div>
                <?php
                  }
                  if($fieldaccesright['field']['performance_ratings']!="NA"){
                    $readonly = $fieldaccesright['field']['performance_ratings'] !="E"?"custom_readonly":"";
                ?>
                <div class="col-lg-4 form-group">
                  <span class="form-label f-w-600">Performance Rating</span>
                  <p class="hr_detailL"><?php echo $candidates['performance_ratings']; ?> </p>
                  <input aria-label="Input field" class="form-control hr_detailT text-right <?php echo $readonly; ?>"  type="text"  id="performance_ratings" name="performance_ratings"  value="<?php echo $candidates['performance_ratings']; ?>" style="display: none">
                </div>
                <?php } ?>
               </div>
             </form>
             </div>
           </div>
<?php  } ?>






        </div>

    </div>


    <div class="col-lg-4">
          <?php $fieldaccesright = $accessrightarry['skills'];
          if($fieldaccesright['access'] !="NA"){ ?>
      <div class="row" >
        <div class="card">
          <div id="Skills">
          <div class="card-header">
            <h5>Skills</h5>
            <div class="card-header-right">
              <?php if($fieldaccesright['field']['performance_ratings']!="NA"){
                    $readonly = $fieldaccesright['field']['performance_ratings'] !="E"?"custom_readonly":""; ?>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" id="skillsajax_edit" onclick="editFrom('skills','skillsajax','edit')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
            <?php } ?>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="skillsajax_save_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="editFrom('skills','skillsajax','save')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20"  onclick="submitFromTable('skillsform','skills','skillsajax','save')" id="skillsajax_save" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" style="display: none">Save</button>
            </div>
          </div>
          <div class="card-body border-bottom" id="skillsajax" >
              <?php $_POST['edittype'] = "skills";
                    $pageurl = "custom_insert_candidates_ajax.php";
                    if(file_exists($pageurl)){
                      include_once $pageurl;
                    }else{
                      include_once "views/".$pageurl;
                    }
              ?>
          </div>

        </div>
        </div>
      </div>
    <?php } ?>
     <?php $fieldaccesright = $accessrightarry['work_experiences'];
          if($fieldaccesright['access'] !="NA"){ ?>
      <div class="row" >
        <div class="card latest-update-card" id="workexperiencejax_popup">
          <div  id="Work_Experiences">
          <div class="card-header">
            <h5>Work Experiences</h5>
            <div class="card-header-right">
              <?php if($fieldaccesright['field']['performance_ratings']!="NA"){
                    $readonly = $fieldaccesright['field']['performance_ratings'] !="E"?"custom_readonly":""; ?>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" id="workexperienceajax_edit" onclick="editFrom('workexperience','workexperienceajax','edit')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                <?php } ?>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="workexperienceajax_save_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="editFrom('workexperience','workexperienceajax','save')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20"  onclick="submitFromTable('workexperienceform','workexperience','workexperienceajax','save')" id="workexperienceajax_save" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" style="display: none">Save</button>
            </div>
          </div>

          <div class="card-body border-bottom " id="workexperienceajax">
              <?php $_POST['edittype'] = "workexperience";
              $pageurl = "custom_insert_candidates_ajax.php";
                    if(file_exists($pageurl)){
                      include_once $pageurl;
                    }else{
                      include_once "views/".$pageurl;
                    } ?>
          </div>
        </div>



        </div>
      </div>
     <?php } ?>
      <?php $fieldaccesright = $accessrightarry['education_details'];
          if($fieldaccesright['access'] !="NA"){ ?>
      <div class="row" >
        <div class="card latest-update-card" id="educationajax_popup">
          <div id="Education_Details">
          <div class="card-header">

            <h5>Education Details</h5>
            <div class="card-header-right">
              <?php if($fieldaccesright['field']['candidate_qualifications']!="NA"){
                    $readonly = $fieldaccesright['field']['candidate_qualifications'] !="E"?"custom_readonly":""; ?>
              <button type="button" class="btn p-0 border-0 bg-transparent ti-pencil" id="educationajax_edit" onclick="editFrom('education','educationajax','edit')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
               <?php } ?>
               <button type="button" class="btn p-0 border-0 bg-transparent ti-back-left" data-bs-toggle="tooltip" id="educationajax_save_undo" data-placement="bottom" title="" data-bs-original-title="Cancel" onclick="editFrom('education','educationajax','save')" style="display: none" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
              <button class="btn waves-effect waves-light btn-primary pt-1 pb-1 m-l-20"  onclick="submitFromTable('educationform','education','educationajax','save')" id="educationajax_save" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" style="display: none">Save</button>
            </div>
          </div>
          <div class="card-body border-bottom" id="educationajax">
               <?php $_POST['edittype'] = "education";
               $pageurl = "custom_insert_candidates_ajax.php";
                    if(file_exists($pageurl)){
                      include_once $pageurl;
                    }else{
                      include_once "views/".$pageurl;
                    } ?>
          </div>
        </div>




        </div>
      </div>
      <?php } ?>
<div class="row">

<div class="card">
    <div class="card-header">
            <h5>Assigned - Job Postings</h5>
            <div class="card-header-right">
              <i class="ti-pencil" id="educationajax_edit_2" onclick="assignedCandidateFiletr('<?php echo $candidates['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
            </div>
    </div>
 <div class="card-body">
<table id="new-cons" class="table table-bordered table-md">
    <thead>
      <tr><th scope="col" class="w-b-w-s">Job Posting</th><th scope="col" class="w-b-w-s">HR</th><th scope="col" class="w-b-w-s"> HM</th></tr>
    </thead>
    <tbody>
      <?php $mk=1;  if(count((array)$job_posting_candidates) !=0){  foreach($job_posting_candidates as $resulttable){

        $hm_shortlisted = "";
        if($resulttable['hm_shortlisted'] == ""){ $hm_shortlisted  = "<i class='fa fa-hourglass-half'></i>";
}elseif($resulttable['hm_shortlisted'] == "0"){ $hm_shortlisted  = "<i class='fa fa-thumbs-up'></i>";
}elseif($resulttable['hm_shortlisted'] == "1"){ $hm_shortlisted  = "<i class='fa fa-thumbs-down'></i>";
}
        $shortlisted = "";
        if($resulttable['shortlisted'] == ""){ $shortlisted  = "<i class='fa fa-hourglass-half'></i>";
}elseif($resulttable['shortlisted'] == "0"){ $shortlisted  = "<i class='fa fa-thumbs-up'></i>";
}elseif($resulttable['shortlisted'] == "1"){ $shortlisted  = "<i class='fa fa-thumbs-down'></i>";
}
        ?>
      <tr>
        <td class="w-b-w-s"><?php echo $job_postingslist[$resulttable['job_posting_id']]; ?></td>
        <td class="w-b-w-s"><?php echo $shortlisted; ?></td>
        <td class="w-b-w-s"><?php echo $hm_shortlisted; ?></td>
      </tr>
     <?php $mk++;
}
}else{ echo "<tr><td colspan='4' class='text-center'>No Apply Any job Post</td></tr>";
} ?>
    </tbody>
  </table>
</div>
</div>

</div>




<div class="row">
  <div class="card chat-card">
      <div class="card-header">
          <h5>Query</h5>
      </div>
     <div class="card-body">
      <?php
      $pageurl = "custom_insert_project_logs.php";
                    if(file_exists($pageurl)){
                      include_once $pageurl;
                    }else{
                      include_once "views/".$pageurl;
                    } ?>
     </div>
  </div>
</div>







    </div>
  </div>
