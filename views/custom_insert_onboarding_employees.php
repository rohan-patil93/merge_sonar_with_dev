<?php
if(isset($_POST['emp_id'])){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
}
if(isset($_GET['Rid']) && $_GET['Rid']!=""){
    $idvalue = explode(':',encodeId($_GET['Rid']));
      $employee_id = $idvalue[1];
}elseif(isset($_POST['emp_id']) && $_POST['emp_id']!=""){
      $employee_id = $_POST['emp_id'];
}else{
      $employee_id = $_SESSION['emp_id'];
}
$url = API."?mode=getemployees&employee_id=".$employee_id."&working_role_short_code=".$_SESSION['working_role_short_code'];
$response = callAPI($url);
$result = json_decode($response,1);
$data = $result['employees'];
$employees_display_fields = $result['employees_display_fields'];
$vafield = json_decode($employees_display_fields['field_list'],1);
$departments = $result['departments'];
$designations = $result['designations'];
$locations = $result['locations'];
$employment_bands = $result['employment_bands'];
$employment_levels = $result['employment_levels'];
$employment_roles = $result['employment_roles'];
$application_roles = $result['application_roles'];
$marital_statuses = $result['marital_statuses'];
$blood_groups = $result['blood_groups'];
$verticals = $result['verticals'];
$gender_details = $result['gender_details'];
$employeement_types = $result['employeement_types'];
$education_type = $result['education_type'];
$banks = $result['banks'];
$emp_educations = $result['emp_educations'];
$emp_dependents = $result['emp_dependents'];
$emp_contacts = $result['emp_contacts'];
$emp_work_experiences = $result['emp_work_experiences'];
$relations = $result['relations'];
$application_users = $result['application_users'];
$all_employees = $result['all_employees'];
$countries = $result['countries'];
$emp_visa = $result['emp_visa'];
$emp_skills = $result['emp_skills'];
$skills = $result['skills'];
$teams =  $result['teams'];
$reporting_manager =  $result['reporting_manager'];
$emp_leave_balances = $result['emp_leave_balances'];
$leavetype_detrails  = $emp_leave_balances['leavetype_detrails'];

function replaceNetTabAndsinglequote($string){
    $search = ["\t", "'"];
    $replace = ''; // Empty string to remove the characters
    $result = str_replace($search, $replace, $string);
    return $result;
}

$bank_work_flow = "Y";
?>
<script type="text/javascript">
    function editEmployeeProfile(pagetype="",edittype="",loadpage="",loadpagebtn="",formname="",transaction_table){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#'+loadpage).html(htmloader);
    var employee_id = '<?php echo $employee_id; ?>'
    var functionpagetype = "'"+pagetype+"'";
    var functionloadpage = "'"+loadpage+"'";
    var functionloadpagebtn = "'"+loadpagebtn+"'";
    var functionformname = "'"+formname+"'";
    var functiontransactiontable = "'"+transaction_table+"'";
    if(pagetype =='experience_details'){
       $("#experience_details_div").addClass("div-covert-popup");
    }else if(pagetype =='employee_qualifications'){
       $("#employee_qualifications_div").addClass("div-covert-popup");
    }else if(pagetype =='employee_dependents'){
       $("#employee_dependents_div").addClass("div-covert-popup");
    }else if(pagetype =='employee_contacts'){
       $("#employee_contacts_div").addClass("div-covert-popup");
    }else if(pagetype =='emp_visa'){
       $("#visa_detail_div").addClass("div-covert-popup");
    }

    jQuery.ajax({
        url: "views/custom_insert_onboarding_employees_ajax.php",
        data:'employee_id='+employee_id+"&ajaxcallemployee=Y&pagetype="+pagetype+"&edittype="+edittype,
        type: "POST",
        success:function(data){
            $("#"+loadpage).html(data);
            $("#"+loadpagebtn).html();
            $(".js-example-basic-single").select2();
            $('[data-bs-toggle="tooltip"]').tooltip();
            if(edittype =="VIEW"){
                var functionedittype = "'INSERT'";
                $("#"+loadpagebtn).html('<button class="btn btn-primary" onclick="editEmployeeProfile('+functionpagetype+','+functionedittype+','+functionloadpage+','+functionloadpagebtn+','+functionformname+','+functiontransactiontable+')"><i class="ti-pencil text-white"></i> Edit</button>');
                if(pagetype =='experience_details'){
                   $("#experience_details_div").removeClass("div-covert-popup");
                }else if(pagetype =='employee_qualifications'){
                   $("#employee_qualifications_div").removeClass("div-covert-popup");
                }else if(pagetype =='employee_dependents'){
                   $("#employee_dependents_div").removeClass("div-covert-popup");
                }else if(pagetype =='employee_contacts'){
                   $("#employee_contacts_div").removeClass("div-covert-popup");
                }else if(pagetype =='emp_visa'){
                   $("#visa_detail_div").removeClass("div-covert-popup");
                }
            }else{
                var functionedittype = "'VIEW'";
                var htmlbutton = "";
                htmlbutton += '<button class="btn btn-primary m-r-20" onclick="insertEmployeeProfile('+functionpagetype+','+functionedittype+','+functionloadpage+','+functionloadpagebtn+','+functionformname+','+functiontransactiontable+')"><i class="ti-check text-white"></i> Save</button>';

                htmlbutton += '<button class="btn btn-danger" onclick="editEmployeeProfile('+functionpagetype+','+functionedittype+','+functionloadpage+','+functionloadpagebtn+','+functionformname+','+functiontransactiontable+')"><i class="ti-close text-white"></i> Cancel</button>'

                $("#"+loadpagebtn).html(htmlbutton);

            }
            $( ".datepicker" ).datepicker({
                showOn: "both",
                buttonText: "<i class='ti-calendar'></i>",
                dateFormat: "<?php echo $_SESSION['system_date_formate'] ?>",
                changeMonth: true,
                changeYear: true
            }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
        },
        error:function (){}
      });
}

function reloadEmployeeSkills(emp_id){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#ajaxemployeempskillstab').html(htmloader);
    var pagetype = "emp_skills";
    var edittype ="VIEW";
    jQuery.ajax({
        url: "views/custom_insert_onboarding_employees_ajax.php",
        data:'employee_id='+emp_id+"&ajaxcallemployee=Y&pagetype="+pagetype+"&edittype="+edittype,
        type: "POST",
        success:function(data){
            $("#ajaxemployeempskillstab").html(data);
        }
    });
}
function insertEmployeeProfile(pagetype="",edittype="",loadpage="",loadpagebtn="",formname="",transaction_table){
    $("#"+loadpage).css('opacity','0.2');

    var employee_id = '<?php echo $employee_id; ?>'
    var functionpagetype = "'"+pagetype+"'";
    var functionloadpage = "'"+loadpage+"'";
    var functionloadpagebtn = "'"+loadpagebtn+"'";
    var functionformname = "'"+formname+"'";
    var functiontransactiontable = "'"+transaction_table+"'";

    var formData = new FormData();
    formData.append("page", "updateemployees");
    formData.append("employee_id",employee_id);
    formData.append("transaction_table", transaction_table);

    var dataArray = $("#"+formname).serializeArray()
    $(dataArray).each(function(i, field){
        formData.append(field.name, field.value);
    });
    if(transaction_table =='emp_work_experiences'){
        $.each($("input[name=work_experience_attachment]"), function(i, obj) {
            $.each(obj.files,function(j, file){
                formData.append('file'+i, file);
            })
        });
    }else if(transaction_table =='emp_educations'){
        $.each($("input[name=educations_attachment]"), function(i, obj) {
            $.each(obj.files,function(j, file){
                formData.append('file'+i, file);
            })
        });
    }
    if(pagetype =='basic_information'){
        var profile_image = document.getElementById('profile_image').files[0];
         formData.append("profile_image",profile_image);
     }

     if(pagetype =='proof_details'){
        var aadhar_attachment = document.getElementById('aadhar_attachment').files[0];
         formData.append("aadhar_attachment",aadhar_attachment);
         var passport_attachment = document.getElementById('passport_attachment').files[0];
         formData.append("passport_attachment",passport_attachment);
         var driving_licence_attachment = document.getElementById('driving_licence_attachment').files[0];
         formData.append("driving_licence_attachment",driving_licence_attachment);
         var pan_attachment = document.getElementById('pan_attachment').files[0];
         formData.append("pan_attachment",pan_attachment);
         var pf_attachment = document.getElementById('pf_attachment').files[0];
         formData.append("pf_attachment",pf_attachment);
         var pf_uan_attachment = document.getElementById('pf_uan_attachment').files[0];
         formData.append("pf_uan_attachment",pf_uan_attachment);
         var gosi_attachment = document.getElementById('gosi_attachment').files[0];
         formData.append("gosi_attachment",pf_uan_attachment);
     }

    jQuery.ajax({
        url: "ajax.php",
           data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        success:function(data){
            $("#"+loadpage).css('opacity','1');
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            editEmployeeProfile(pagetype,edittype,loadpage,loadpagebtn,formname,transaction_table);
        }
    });
}

function getBandid(id){
  $.ajax({
   url: "ajax.php",
   data:'page=getemployeementlevel&id='+id,
   type: "POST",
   success:function(data){
     console.log(data);
     $("#employeement_level").html(data);
   },
   error:function (){}
 });
}
function showmarriagedate(value){
    if(value=='2') {
           document.getElementById('marriagedateshow').style.display ='block';
           document.getElementById('marriagedateshow1').style.display ='block';
     }else{
           document.getElementById('marriagedateshow').style.display ='none';
           document.getElementById('marriagedateshow1').style.display ='none';
           $("#wedding_date").val("");
     }
}
function selectDisability(){

    var disability = $('#disability:checked').val();
    console.log(disability);
    if(disability=='0') {
           document.getElementById('disabilitypercentage_id').style.display ='block';
       }else{
           document.getElementById('disabilitypercentage_id').style.display ='none';
       }
}

function getAttachedFileName(thisval,showfilenameid){
    $("#"+showfilenameid).html(thisval.files[0].name);
}
function removedRow(id){
    console.log(id);
    $("#"+id).remove();
}

var maxexpr = 100;
function addexpreencerows(){
    maxexpr+=1;
     var htmlRows = '';
     var rowid = 'exp_details_'+maxexpr;
     htmlRows += '<tr id="'+rowid+'">';

     htmlRows += '<td class="p-2"><input aria-label="Enter Company Name" type="text" name="company_name[]"  class="form-control " autocomplete="off" placeholder="Enter Company Name">  </td>';
     htmlRows += '<td class="p-2"><input aria-label="Enter Job Descrption" type="text" name="job_descrption[]"  class="form-control " autocomplete="off" placeholder="Enter Job Descrption">  </td>';
     htmlRows += '<td class="p-2"><input aria-label="Enter From Date" type="text" name="work_from[]"  class="form-control datepicker"   autocomplete="off" placeholder="Enter From Date"> </td>';
     htmlRows += '<td class="p-2"><input aria-label="Enter To Date" type="text" name="work_to[]"  class="form-control datepicker"  autocomplete="off" placeholder="Enter To Date">  </td>';

     var onchval = "'workexperiencefilename_"+maxexpr+"'";
     htmlRows += '<td class="p-2 w-b-w-s"><div class="row"><div class="col-lg-6"><div class="image-input image-input-empty image-input-outline" id="kt_image_5"><label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Upload"><span><i class="fa fa-upload"></i></span><input aria-label="Work Experience Attachment" type="file" name="work_experience_attachment" class="form-control" onchange="getAttachedFileName(this,'+onchval+')"><input type="hidden" name="old_work_experience_attachment[]" value=""></label></div></div><div class="col-lg-6 p-0"></div></div><span id="workexperiencefilename_'+maxexpr+'" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span></td>';

    htmlRows += '<td class="p-2"><button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow(\''+rowid+'\')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></td>';

     htmlRows += '</tr>';
     $('#extable').append(htmlRows);
     $(".js-example-basic-single").select2();
     $(".js-example-disabled-results").select2();
     $( ".datepicker" ).datepicker({
      showOn: "both",
      buttonText: "<i class='ti-calendar'></i>",
      dateFormat: "<?php echo $_SESSION['system_date_formate'] ?>",
      changeMonth: true,
      changeYear: true
    }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
}

var maxedurow = 1000;
function addEmpEducationsRows(){
    maxedurow+=1;
     var htmlRows = '';
     var rowid = 'emp_educations_'+maxedurow;
     htmlRows += '<tr id="'+rowid+'">';

     htmlRows += '<td class="p-2"><input aria-label="Enter Institute Name" type="text" name="institute_name[]"  class="form-control " autocomplete="off" placeholder="Enter Institute Name">  </td>';
     htmlRows += '<td class="w-full p-2"><select aria-label="Education Type[]" class="form-control form-select js-example-basic-single" name="education_type[]" > <option value=""> Select Any One</option> <?php foreach ($education_type as  $edutype) {   ?> <option   value="<?php echo $edutype['genc_detail_code']; ?>"> <?php echo $edutype['genc_detail_name']; ?></option> <?php
} ?></select> </td>';
     htmlRows += '<td  class="p-2"><input aria-label="Enter Start date" type="text" name="start_date[]"  class="form-control datepicker"  autocomplete="off" placeholder="Enter Start date"> </td>';
     htmlRows += '<td  class="p-2"><input aria-label="Enter End Date" type="text" name="end_date[]"  class="form-control datepicker"   autocomplete="off" placeholder="Enter End Date">  </td>';
     htmlRows += '<td  class="p-2"><input aria-label="Enter Score" type="text" name="score[]"  class="form-control " autocomplete="off" placeholder="Enter Score">  </td>';

     var onchval = "'educationsfilename_"+maxedurow+"'";

     htmlRows += '<td class="p-2 w-b-w-s"><div class="row"><div class="col-lg-6"><div class="image-input image-input-empty image-input-outline" id="kt_image_5_2"><label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="" data-bs-original-title="Upload"><span><i class="fa fa-upload"></i></span><input aria-label="Educations Attachment" type="file" name="educations_attachment" class="form-control" onchange="getAttachedFileName(this,'+onchval+')"><input type="hidden" name="old_educations_attachment[]" value=""></label></div></div><div class="col-lg-6 p-0"></div></div><span id="educationsfilename_'+maxedurow+'" class="cursor-pointer custom-limit-text" style="--linehide: 1;"></span></td>';

    htmlRows += '<td class="p-2"><button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow(\''+rowid+'\')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></td>';

     htmlRows += '</tr>';
     $('#edutable').append(htmlRows);
     $(".js-example-basic-single").select2();
     $(".js-example-disabled-results").select2();
     $( ".datepicker" ).datepicker({
      showOn: "both",
      buttonText: "<i class='ti-calendar'></i>",
      dateFormat: "<?php echo $_SESSION['system_date_formate'] ?>",
      changeMonth: true,
      changeYear: true
    });
}
var maxdependrow = 2000;
function adddeprows(){
    maxdependrow+=1;
    var rowid = 'employee_dependents_'+maxdependrow;
     var htmlRows = '';
      htmlRows += '<tr id="'+rowid+'">';
     htmlRows += '<td><input aria-label="Enter Dependent Name" type="text" name="dependent_name[]"  class="form-control " autocomplete="off" placeholder="Enter Dependent Name">  </td>';
     htmlRows += '<td class="w-full"><select aria-label="Dependent Relationship Type[]" class="form-control form-select js-example-basic-single" name="dependent_relationship_type[]" > <option value=""> Select Any One</option> <?php foreach ($relations as  $reladata) {   ?> <option   value="<?php echo $reladata['id']; ?>"> <?php echo replaceNetTabAndsinglequote($reladata['relation_name']); ?></option> <?php
} ?></select>  </td>';
     htmlRows += '<td><input aria-label="Enter Date Of Birth" type="text" name="dependent_date_of_birth[]"  class="form-control datepicker" autocomplete="off" placeholder="Enter Date Of Birth">  </td>';

    htmlRows += '<td class="p-2"><button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow(\''+rowid+'\')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></td>';
     htmlRows += '</tr>';
     $('#deptable').append(htmlRows);
     $(".js-example-basic-single").select2();
     $(".js-example-disabled-results").select2();
     $( ".datepicker" ).datepicker({
      showOn: "both",
      buttonText: "<i class='ti-calendar'></i>",
      dateFormat: "<?php echo $_SESSION['system_date_formate'] ?>",
      changeMonth: true,
      changeYear: true
    });
}

var maxempcontactrow = 3000;
function addEmployeeContactsRows(){
    maxempcontactrow+=1;
    var rowid = 'emp_contacts_'+maxempcontactrow;
     var htmlRows = '';
    htmlRows += '<tr id="'+rowid+'">';
     htmlRows += '<td class="p-2"><input aria-label="Enter Contact Name" type="text" name="contact_name[]"  class="form-control " autocomplete="off" placeholder="Enter Contact Name"> </td>';
     htmlRows += '<td class="w-full p-2"><select aria-label="Contact Relationship[]" class="form-control form-select js-example-basic-single" name="contact_relationship[]" > <option value=""> Select Any One</option> <?php foreach ($relations as  $reladata) {   ?> <option   value="<?php echo $reladata['id']; ?>"> <?php echo replaceNetTabAndsinglequote($reladata['relation_name']); ?></option> <?php
} ?></select>    </td>';
     htmlRows += '<td class="p-2"><input aria-label="Enter Contact Home No" type="text" name="contact_home_no[]"  class="form-control "  autocomplete="off" placeholder="Enter Contact Home No">  </td>';
     htmlRows += '<td class="p-2"><input aria-label="Enter Contact Mobile No" type="text" name="contact_mobile_no[]"  class="form-control "  autocomplete="off" placeholder="Enter Contact Mobile No">  </td>';
     htmlRows += '<td class="p-2"><button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow(\''+rowid+'\')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></td>';
     htmlRows += '</tr>';
     $('#emergetable').append(htmlRows);
     $(".js-example-basic-single").select2();
     $(".js-example-disabled-results").select2();
     $( ".datepicker" ).datepicker({
      showOn: "both",
      buttonText: "<i class='ti-calendar'></i>",
      dateFormat: "<?php echo $_SESSION['system_date_formate'] ?>",
      changeMonth: true,
      changeYear: true
    }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
}
var maxempvisarow = 3000;
function addEmpVisatableRows(){
    maxempvisarow+=1;
    var rowid = 'emp_visa_'+maxempvisarow;
     var htmlRows = '';
    htmlRows += '<tr id="'+rowid+'">';
     htmlRows += '<td class="p-2"><input type="hidden" name="visa_rowid[]" class="form-control " value=""><input aria-label="Enter Visa Number" type="text" name="visa_number[]"  class="form-control " autocomplete="off" placeholder="Enter Visa Number"> </td>';
     htmlRows += '<td class="w-full p-2"><select aria-label="Country []" class="form-control form-select js-example-basic-single" name="country_id[]" > <option value=""> Select Any One</option> <?php foreach ($countries as  $countrys) {   ?> <option   value="<?php echo $countrys['id']; ?>"> <?php echo replaceNetTabAndsinglequote($countrys['country_name']); ?></option> <?php
} ?></select>    </td>';
     htmlRows += '<td class="p-2"><input aria-label="Enter Visa Issue Date" type="text" name="visa_issue_date[]" class="form-control datepicker" value="" autocomplete="off" placeholder="Enter Visa Issue Date"> </td>';
     htmlRows += '<td class="p-2"><input aria-label="Enter Visa Expiry Date" type="text" name="visa_expiry_date[]" class="form-control datepicker" value="" autocomplete="off" placeholder="Enter Visa Expiry Date"></td>';
     htmlRows += '<td class="p-2"><button type="button" class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="" onclick="removedRow(\''+rowid+'\')" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button></td>';
     htmlRows += '</tr>';
     $('#emp_visatable').append(htmlRows);
     $(".js-example-basic-single").select2();
     $(".js-example-disabled-results").select2();
     $( ".datepicker" ).datepicker({
      showOn: "both",
      buttonText: "<i class='ti-calendar'></i>",
      dateFormat: "<?php echo $_SESSION['system_date_formate'] ?>",
      changeMonth: true,
      changeYear: true
    }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
}
function selectdateOfBirth(valu){
    var selected_dates = getDateMonthYear(valu,'<?php echo $_SESSION['system_date_formate'] ?>');
    var this_year = new Date().getFullYear()
    var this_month = new Date().getMonth();
    var ageyear = parseInt(this_year)-parseInt(selected_dates['year']);
    var agemonth = parseInt(this_month)-parseInt(selected_dates['month']-1);
    if (agemonth < 0) {
        ageyear--;
        agemonth += 13;
    }
    agemonth = (agemonth < 10 && agemonth >= 0) ? '0' + agemonth : agemonth;
    $("#age").val(ageyear+"."+agemonth);
}

function getDateMonthYear(dateString, format) {
            var day, month, year;
            if (format === "yy-mm-dd") {
                var parts = dateString.split('-');
                if (parts.length === 3) {
                    year = parseInt(parts[0]);
                    month = parts[1]; // Months are zero-based
                    day = parseInt(parts[2]);
                }
            } else if (format === "dd-mm-yy") {
                var parts = dateString.split('-');
                if (parts.length === 3) {
                    day = parseInt(parts[0]);
                    month = parts[1]; // Months are zero-based
                    year = parseInt(parts[2]);
                }
            } else if (format === "mm-dd-yy") {
                var parts = dateString.split('-');
                if (parts.length === 3) {
                    month = parseInt(parts[0]); // Months are zero-based
                    day = parts[1];
                    year = parseInt(parts[2]);
                }
            } else {
                console.error("Unsupported date format:", format);
                return null;
            }
            if (isNaN(day) || isNaN(month) || isNaN(year)) {
                console.error("Unable to parse date string:", dateString);
                return null;
            }
            // Construct the Date object
               var dateary = [];
               dateary['year'] = year;
               dateary['month'] = month;
               dateary['day'] = day;
            return dateary;
        }
function skillpopupNew(rid=""){
        jQuery.ajax({
        url: "views/custom_insert_employee_skill_popup.php",
        data:'option=1&emp_id=<?php echo $employee_id; ?>&rid='+rid,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}
function changeonsite(id,name){
    var onsite=  $("#onsite_"+id).val();
    var status = "";
    var color = "";
    if(onsite==0){ onsite =1;  status = "Off Site"; color="red"; }else{ onsite = 0; status = "On Site";}
    $('#modal_status').modal({
        backdrop: 'static',
        keyboard: false
    })
    $(".modallabel").text("On Site OR Off Site");
      $(".modal_view_employee_name").text(name);
      $(".modal_view_status").text(status);
      $("#emp_id").val(id);
      $("#field_name").val("Offsite and Onsite");
      $("#field_value").val(onsite);
      $('#modal_status').modal('show');
      $("#onsite_"+id).val(onsite);
    $(".onsite_lable"+id).text(status);
      $(".onsite_lable"+id).css("color",color);

}
function changebillable(id,name){
      var billable=  $("#billable_"+id).val();
      var status = "";
      var color = "";
      if(billable==0){ billable =1; status = "Non Billable"; color="red";  }else{ billable = 0; status = "Billable";  }
      $('#modal_status').modal({
            backdrop: 'static',
            keyboard: false
        })
      $(".modallabel").text("Billable OR Non-Billable");
      $(".modal_view_employee_name").text(name);
      $(".modal_view_status").text(status);
      $("#emp_id").val(id);
      $("#field_name").val("Billable and Non Billable");
      $("#field_value").val(billable);
      $('#modal_status').modal('show');
      $("#billable_"+id).val(billable);
      $(".billable_lable"+id).text(status);
       $(".billable_lable"+id).css("color",color);

}
function save_statuse_bilable_onsite(){
    $.ajax({
           type: 'post',
           url: 'ajax.php',
           data: $('#insertemphistries').serialize()+"&page=insertemphistries",
           success: function (response) {
              $('#modal_status').modal('hide');
        }
    });
}
function reloadPage(){
    window.location.href = window.location.href;
}
function cancelAddBankRequest(rid=""){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#ajaxemployeebankdetailstab').html(htmloader);
    jQuery.ajax({
        url: "views/custom_insert_onboarding_employees_ajax.php",
        data:'employee_id=<?php echo $employee_id; ?>&ajaxcallemployee=Y&pagetype=bank_details&edittype=VIEW',
        type: "POST",
        success:function(data){
            $("#ajaxemployeebankdetailstab").html(data);

            var htmlbutton = '<button class="btn btn-success " onclick="addBankRequest()"><i class="icofont icofont-plus icon-white "></i> Add Bank Request</button>'
            $("#bank_details_employee_btn").html(htmlbutton);

            $(".js-example-basic-single").select2();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}
function addBankRequest(rid=""){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#ajaxemployeebankdetailstab').html(htmloader);
    var htmlbutton = "";
    htmlbutton += '<button class="btn btn-primary m-r-20" onclick="insertEMPBank('+rid+')"><i class="ti-check text-white"></i> Save</button>';
    htmlbutton += '<button class="btn btn-danger" onclick="cancelAddBankRequest('+rid+')"><i class="ti-close text-white"></i> Cancel</button>'
    $("#bank_details_employee_btn").html(htmlbutton);
    jQuery.ajax({
        url: "views/custom_insert_emp_banks.php",
        data:'employee_id=<?php echo $employee_id; ?>&ajaxbank=Y&rid='+rid,
        type: "POST",
        success:function(data){
            $("#ajaxemployeebankdetailstab").html(data);
            $(".js-example-basic-single").select2();
        },
        error:function (){}
      });
}
function insertEMPBank(rid=""){
    $("#ajaxemployeebankdetailstab").css('opacity','0.2');
    var formData = new FormData();
    formData.append("page", "insertempbanks");
    formData.append("employee_id",'<?php echo $employee_id; ?>');
    var dataArray = $("#bank_details_form").serializeArray()
    $(dataArray).each(function(i, field){
        formData.append(field.name, field.value);
    });
    jQuery.ajax({
        url: "ajax.php",
           data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        success:function(data){
            $("#ajaxemployeebankdetailstab").css('opacity','1');
            var result = JSON.parse(data);
            if(result.emp_banks['status'] == "fail"){
                swal("<?php echo ucwords($_SESSION['config']['name']); ?>", result.emp_banks['message'], "error");
            }else{
              if(result.emp_banks['LASTID'] == "0"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                }else{
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                }
               cancelAddBankRequest(rid);
            }

        }
    });
}
function insertReleive(){

    var rev=  $("#releive_date").val();
    if(rev!=''){
        $("#releive_date_error").hide();
        var rev_comments =  $("#releive_comments").val();
        var emp_id  = '<?php echo $employee_id; ?>'
        var termination_type  = $("#termination_type").val();
        var effective_date  = $("#effective_date").val();
        var settle_on = $("#settle_on").val();
        var formData = 'page=toemployeereleive&effective_date='+effective_date+'&releive_date='+rev+'&emp_id='+emp_id+'&termination_type='+termination_type+'&remarks_by_admin='+rev_comments+'&settle_on='+settle_on;
        console.log("Check Releving data "+formData);
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=toemployeereleive&effective_date='+effective_date+'&releive_date='+rev+'&emp_id='+emp_id+'&termination_type='+termination_type+'&remarks_by_admin='+rev_comments+'&settle_on='+settle_on,
            type: "POST",
            success:function(data){
              console.log("Check Releving data "+data);
                swal("<?php echo ucwords($_SESSION['config']['name']); ?>","<?php echo $_SESSION['error_message']['9158']; ?>", "success");
            }
        });
    }else{
         $("#releive_date_error").show();
    }
}
</script>
<?php if(isset($_POST['emp_id'])){  ?>
<div class="modal-header">
    <h5 class="modal-title">
        Employee Information
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<?php } ?>
<div class="<?php echo $_POST['emp_id']!="" || $_POST['ajaxpage'] =="Y"?"p-3":""; ?>">
    <?php
    $accessbtnclass = "btn-primary";
            $accessbtnclasssuccess = "btn-success";
            if($data['date_of_leaving']!=""){
            $is_was = 'is';
            $is_was_color = 'warning';

            if(strtotime($data['date_of_leaving']) < strtotime(date('Y-m-d'))){
               $is_was = 'was';
               $is_was_color = 'danger';

            }
            $accessbtnclass = $accessbtnclasssuccess = "btn-danger disabled";
       ?>
      <div class="alert alert-<?php echo $is_was_color; ?> background-<?php echo $is_was_color; ?> alert-dismissible mt-2">
           <p class="text-white"><strong> <?php echo $data['display_name']." ( ".$data['emp_code']." )"; ?> is resigned,</strong> <small> The last working day  <?php echo $is_was." ".fromDate($data['date_of_leaving']); ?> </small></p>
         <button type="button" class="btn-close"  data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
   <?php } ?>

    <div class="mb-3">
        <ul class="nav md-tabs infisuite_tab">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#employee_basic_information" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <span class="nav-text">Basic Information</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link besic-info-txt-start" id="home-tab-6" data-bs-toggle="tab" href="#home-6" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <span class="nav-text">My Team</span>
                </a>
            </li>
            <li class="nav-item">
              <a class="nav-link besic-info-txt-start" id="home-tab-7" data-bs-toggle="tab" href="#home-7"  role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <span class="nav-text">Calendar</span>
                </a>
            </li>
             <li class="nav-item">
              <a class="nav-link besic-info-txt-start" id="home-tab-att" data-bs-toggle="tab" href="#home-att"  role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <span class="nav-text">Attendance</span>
                </a>
            </li>
            <?php if(in_array('8',(array)$_SESSION['module_list']) || $data['employee_type'] == '1'){ ?>
                <li class="nav-item">
                    <a class="nav-link besic-info-txt-start" id="home-tab-70" data-bs-toggle="tab" href="#home-70" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <span class="nav-text">Pay</span>
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item">
              <a class="nav-link besic-info-txt-start"  data-bs-toggle="tab" href="#leave_balance_tab"  role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <span class="nav-text">Leave Balances</span>
                </a>
            </li>
            <?php  if($_SESSION['admin_type']==1){ ?>
                <li class="nav-item">
                <a class="nav-link besic-info-txt-start" id="resignation-tab-7" data-bs-toggle="tab" href="#resignation-7"  role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <span class="nav-text">Resignation </span>
                </a>
            </li>
                <?php } ?>
        </ul>
    </div>
     <div class="tab-content">
             <div class="tab-pane fade"  id="resignation-7" role="tabpanel" aria-labelledby="resignation-tab-7">
            <?php $data_termination = $result['emp_termination']; ?>
             <div class="card">
                <div class="card-header">
                    <h5>Resignation Details</h5>
                    <div class="card-header-right" id="">
                        <?php if(count((array)$data_termination)==0){ ?>
                        <button class="btn btn-primary m-r-20" onclick="insertReleive()">
                            <i class="ti-check text-white"></i> Save
                        </button>
                    <?php } ?>
                    </div>
                </div>
                <div class="card-body">
                     <div class="row col-lg-12">

                     <div class="form-group col-lg-6">
                             <span class="form-label">Relieve Reason </span>
                             <select aria-label="Termination Type" class="js-example-basic-single" id="termination_type" name="termination_type">
                                 <option value="0">Select Any One</option>
                                 <?php foreach($result['termination_type'] as $termination_types){ ?>
                                     <option <?php if($data_termination['termination_type']==$termination_types['genc_detail_code']){ echo "selected";
} ?> value="<?php echo $termination_types['genc_detail_code']; ?>"><?php echo $termination_types['genc_detail_name']; ?></option>
                                 <?php } ?>
                             </select>
                         </div>
                         <div class="form-group col-lg-6">
                             <span class="form-label">Resignation Date </span>
                             <span class="d-block error-message text-danger"></span>
                             <input aria-label="Effective Date" type="text" class="form-control datepicker " id="effective_date" name="effective_date" value="<?php echo fromDate($data_termination['effective_date']); ?>">
                         </div>
                         <div class="form-group col-lg-6">
                             <span class="form-label">Last Working  Date </span>
                             <span class="d-block error-message text-danger"></span>
                             <input aria-label="Releive Date" type="text" class="form-control datepicker " id="releive_date" name="releive_date" value="<?php echo fromDate($data_termination['releive_date']); ?>">
                         </div>
                         <div class="form-group col-lg-6">
                             <span class="form-label">Settle On Date </span>
                             <span class="d-block error-message text-danger"></span>
                             <input aria-label="Settle On" type="text" class="form-control datepicker " id="settle_on" name="settle_on" value="<?php echo fromDate($data_termination['settle_on']); ?>">
                         </div>
                         <div id="releive_date_error" style="display: none;"> Please Fill The Releive Date</div>
                         <div class="form-group col-lg-6 ">
                             <span>Comments</span>
                             <textarea aria-label="Releive Comments" class="form-control " id="releive_comments" name="releive_comments"><?php echo $data_termination['remarks_by_admin']; ?> </textarea>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="tab-pane fade" id="leave_balance_tab" role="tabpanel" >
             <div class="row p-4">
                <?php
                $mk=0; if(count((array)$emp_leave_balances['available_category']) !='0'){ ?>
                <?php
                foreach($emp_leave_balances['available_category'] as $key=>$values){
                    $leave_details  = $leavetype_detrails[$key]['leavedetails']; ?>
                    <div class="col-lg-2">
                        <div class="card card-custom gutter-b mb-0" >
                            <div class="p-3">
                                <span class="text-inverse-success font-weight-bolder  font-size-sm mt-1 f-14 ">
                                    <?php echo $values; ?>
                                </span>
                                <div class="text-inverse-success font-weight-bolder font-size-h3 mt-3 d-none"><?php echo $leave_details['applicable_leaves'];?></div>

                                <div class="row mt-3">
                                    <div class="col-6 text-center">
                                        <h5 class="f-w-700 m-b-0"><?php echo $leave_details['consumed_leaves']; ?></h5>
                                        <p class="m-b-0">Consumed</p>
                                    </div>
                                    <div class="col-6 b-l-default text-center">
                                        <h5 class="f-w-700 m-b-0"><?php echo $leave_details['applicable_leaves']-$leave_details['consumed_leaves']; ?></h5>
                                        <p class="m-b-0">Available</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php $mk++;
}
}else{  ?>
                    <div class="card-px text-center py-20 mt-2">
                        <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Leaves </p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>

                <?php } ?>
            </div>
         </div>
         <div class="tab-pane fade active show" id="employee_basic_information" role="tabpanel" >
            <div class="row">
                <div class="col-lg-7">
                    <?php
                        $fieldaccesright = $vafield['basic_information'];
                        if($fieldaccesright['access'] !="NA"){
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Basic Information</h5>
                            <div class="card-header-right" id="basic_information_employee_btn">
                                <?php if($fieldaccesright['access'] !="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('basic_information','INSERT','ajaxemployeebasicinformationtab','basic_information_employee_btn','basic_information_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="basic_information_form" >
                                <input type="hidden" name="sadsfc" value="sdfedrfv">
                                <div id="ajaxemployeebasicinformationtab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "basic_information";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                          </div>
                       </div>
                       <?php
                           }
                        $fieldaccesright = $vafield['work'];
                        if($fieldaccesright['access'] !="NA"){
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Work Details</h5>
                            <div class="card-header-right" id="work_details_employee_btn">
                                <?php if($fieldaccesright['access'] !="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('work_details','INSERT','ajaxemployeeworkdetailstab','work_details_employee_btn','work_detail_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body" >
                            <form method="post" id="work_detail_form" >
                                  <div id="ajaxemployeeworkdetailstab">
                                    <?php
                                          $_POST['edittype'] = "VIEW";
                                          $_POST['showpagetype'] = "work_details";
                                          if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                            include_once "views/custom_insert_onboarding_employees_ajax.php";
                                        }else{
                                            include_once "custom_insert_onboarding_employees_ajax.php";
                                        }
                                          unset($_POST['edittype']); unset($_POST['showpagetype']);
                                      ?>
                                  </div>
                              </form>
                          </div>
                       </div>
                       <?php } ?>
                       <?php
                        $fieldaccesright = $vafield['personal_details'];
                        if($fieldaccesright['access'] !="NA"){
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Personal Details</h5>
                            <div class="card-header-right" id="personal_details_employee_btn">
                                <?php if($fieldaccesright['access'] !="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('personal_details','INSERT','ajaxemployeepersonaldetailstab','personal_details_employee_btn','personal_detail_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="personal_detail_form" >
                                  <div id="ajaxemployeepersonaldetailstab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "personal_details";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                          </div>
                       </div>
                       <?php } ?>
                       <?php if($bank_work_flow == 'Y'){
                        $fieldaccesright = $vafield['bank_details'];
                        if($fieldaccesright['access'] !="NA" && $fieldaccesright['field']['bank_details']!="NA"){
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Bank Details</h5>
                            <div class="card-header-right" id="bank_details_employee_btn">
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['bank_details']!="V"){ ?>
                                <button class="btn <?php echo $accessbtnclasssuccess; ?>" onclick="addBankRequest()"><i class="icofont icofont-plus icon-white "></i> Add Bank Request</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                              <div id="ajaxemployeebankdetailstab">
                        <?php
                              $_POST['edittype'] = "VIEW";
                              $_POST['showpagetype'] = "bank_details";
                              if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                include_once "views/custom_insert_onboarding_employees_ajax.php";
                            }else{
                                include_once "custom_insert_onboarding_employees_ajax.php";
                            }
                              unset($_POST['edittype']); unset($_POST['showpagetype']);
                          ?>
                              </div>
                          </div>
                       </div>
                       <?php } ?>
                       <?php }else{
                        $fieldaccesright = $vafield['bank_details'];
                        if($fieldaccesright['access'] !="NA"){
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Bank Details</h5>
                            <div class="card-header-right" id="bank_details_employee_btn_2">
                                <?php if($fieldaccesright['access'] !="V"){ ?>
                                <button class="btn btn-primary" onclick="editEmployeeProfile('bank_details','INSERT','ajaxemployeebankdetailstab','bank_details_employee_btn','bank_details_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="bank_details_form" >
                                  <div id="ajaxemployeebankdetailstab_2">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "bank_details";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                          </div>
                       </div>
                       <?php } ?>
                       <?php } ?>
                       <?php
                        $fieldaccesright = $vafield['employee_buddies'];
                        if($fieldaccesright['access'] !="NA"){
                        ?>
                       <div class="card">
                        <div class="card-header">
                            <h5>Buddies / Others</h5>
                            <div class="card-header-right" id="employee_buddies_employee_btn">
                                <?php if($fieldaccesright['access'] !="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('employee_buddies','INSERT','ajaxemployeeemployeebuddiestab','employee_buddies_employee_btn','employee_buddies_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="employee_buddies_form" >
                                  <div id="ajaxemployeeemployeebuddiestab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "employee_buddies";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                          </div>
                       </div>
                       <?php } ?>

<?php
   $fieldaccesright = $vafield['employee_access'];
   if($fieldaccesright['access'] !="NA"){
    ?>
<div class="card">
<div class="card-header">
    <h5>Employee Access</h5>
    <div class="card-header-right" id="employee_access_employee_btn">
        <?php if($fieldaccesright['access'] !="V"){ ?>
        <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('employee_access','INSERT','ajaxemployeeemployeeaccesstab','employee_access_employee_btn','employee_access_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
        <?php } ?>
    </div>
</div>
<div class="card-body">
    <form method="post" id="employee_access_form" >
            <div id="ajaxemployeeemployeeaccesstab">
    <?php
            $_POST['edittype'] = "VIEW";
            $_POST['showpagetype'] = "employee_access";
            if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
            include_once "views/custom_insert_onboarding_employees_ajax.php";
        }else{
            include_once "custom_insert_onboarding_employees_ajax.php";
        }
            unset($_POST['edittype']); unset($_POST['showpagetype']);
        ?>
            </div>
        </form>
    </div>
</div>
<?php } ?>

<?php
   $fieldaccesright = $vafield['employee_other_access'] ?? array();
   if(($fieldaccesright['access'] ?? '') != "NA" && ($fieldaccesright['field']['employee_other_access'] ?? '') != "NA"){
?>
<div class="card">
<div class="card-header">
    <h5>Country Specific Details</h5>
    <div class="card-header-right" id="employee_other_access_employee_btn">
    	<?php if(($fieldaccesright['access'] ?? '') != "V"){ ?>
    	<button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('employee_other_access','INSERT','ajaxemployeeemployeeotheraccesstab','employee_other_access_employee_btn','employee_other_access_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
    	<?php } ?>
    </div>
</div>
<div class="card-body">
	<form method="post" id="employee_other_access_form">
			<div id="ajaxemployeeemployeeotheraccesstab">
	<?php
			$_POST['edittype'] = "VIEW";
			$_POST['showpagetype'] = "employee_other_access";
			if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
			include_once "views/custom_insert_onboarding_employees_ajax.php";
		}else{
			include_once "custom_insert_onboarding_employees_ajax.php";
		}
			unset($_POST['edittype']); unset($_POST['showpagetype']);
		?>
			</div>
		</form>
	</div>
</div>
<?php } ?>
                </div>
                <div class="col-lg-5">
                    <?php
                        $fieldaccesright = $vafield['proof_details'];
                        if($fieldaccesright['access'] !="NA"){
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Identity Details</h5>
                            <div class="card-header-right" id="proof_details_employee_btn">
                                <?php if($fieldaccesright['access'] !="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('proof_details','INSERT','ajaxemployeeproofdetailstab','proof_details_employee_btn','proof_details_form','employees')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="proof_details_form" >
                                  <div id="ajaxemployeeproofdetailstab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "proof_details";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                        </div>
                    </div>
                    <?php } ?>
                       <?php
                        $fieldaccesright = $vafield['experience_details'];
                        if($fieldaccesright['access'] !="NA" && $fieldaccesright['field']['employee_experiances']!="NA"){
                    ?>
                    <div class="card latest-update-card" id="experience_details_div">
                        <div class="card-header">
                            <h5>Employment History</h5>
                            <div class="card-header-right" id="experience_details_employee_btn">
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['employee_experiances']!="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('experience_details','INSERT','ajaxemployeeexperiencedetailstab','experience_details_employee_btn','employment_history_form','emp_work_experiences')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="employment_history_form" >
                                  <div id="ajaxemployeeexperiencedetailstab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "experience_details";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                        </div>
                    </div>
                    <?php } ?>
                       <?php
                        $fieldaccesright = $vafield['employee_qualifications'];
                        if($fieldaccesright['access'] !="NA" && $fieldaccesright['field']['employee_qualifications']!="NA"){
                    ?>
                    <div class="card latest-update-card" id="employee_qualifications_div">
                        <div class="card-header">
                            <h5>Educational Details</h5>
                            <div class="card-header-right" id="employee_qualifications_employee_btn">
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['employee_qualifications']!="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('employee_qualifications','INSERT','ajaxemployeeemployeequalificationstab','employee_qualifications_employee_btn','employee_qualifications_form','emp_educations')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="employee_qualifications_form" >
                                  <div id="ajaxemployeeemployeequalificationstab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "employee_qualifications";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                        </div>
                    </div>
                    <?php } ?>
                    <?php
                        $fieldaccesright = $vafield['employee_dependents'];
                        if($fieldaccesright['access'] !="NA" && $fieldaccesright['field']['employee_dependents']!="NA"){
                    ?>
                    <div class="card" id="employee_dependents_div">
                        <div class="card-header">
                            <h5>Dependents</h5>
                            <div class="card-header-right" id="employee_dependents_employee_btn">
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['employee_dependents']!="V"){ ?>
                                  <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('employee_dependents','INSERT','ajaxemployeeemployeedependentstab','employee_dependents_employee_btn','employee_dependents_form','emp_dependents')"><i class="ti-pencil text-white"></i> Edit</button>
                                  <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="employee_dependents_form" >
                                  <div id="ajaxemployeeemployeedependentstab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "employee_dependents";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                        </div>
                    </div>
                    <?php } ?>
                    <?php
                        $fieldaccesright = $vafield['employee_contacts'];
                        if($fieldaccesright['access'] !="NA" && $fieldaccesright['field']['employee_contacts']!="NA"){
                    ?>
                    <div class="card" id="employee_contacts_div">
                        <div class="card-header">
                            <h5>Emergency Contact</h5>
                            <div class="card-header-right" id="employee_contacts_employee_btn">
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['employee_contacts']!="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('employee_contacts','INSERT','ajaxemployeeemployeecontactstab','employee_contacts_employee_btn','employee_contacts_form','emp_contacts')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="employee_contacts_form" >
                                  <div id="ajaxemployeeemployeecontactstab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "employee_contacts";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                        </div>
                    </div>
                    <?php } ?>
                    <?php
                        $fieldaccesright = $vafield['emp_visa'];
                        if($fieldaccesright['access'] !="NA" && $fieldaccesright['field']['emp_visa']!="NA"){
                    ?>
                    <div class="card" id="visa_detail_div">
                        <div class="card-header">
                            <h5>VISA Details</h5>
                            <div class="card-header-right" id="employee_emp_visa_btn">
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['emp_visa']!="V"){ ?>
                                <button class="btn <?php echo $accessbtnclass; ?>" onclick="editEmployeeProfile('emp_visa','INSERT','ajaxemployeeempvisatab','employee_emp_visa_btn','emp_visa_form','emp_visa')"><i class="ti-pencil text-white"></i> Edit</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="emp_visa_form" >
                                  <div id="ajaxemployeeempvisatab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "emp_visa";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                        </div>
                    </div>
                    <?php } ?>
                    <?php
                        $fieldaccesright = $vafield['emp_skills'];
                        if($fieldaccesright['access'] !="NA" && $fieldaccesright['field']['emp_skills']!="NA"){
                    ?>
                    <div class="card" >
                        <div class="card-header">
                            <h5>Skills Details</h5>
                            <div class="card-header-right" id="employee_emp_skills_btn">
                                <?php if($fieldaccesright['access'] !="V" && $fieldaccesright['field']['emp_skills']!="V"){ ?>
                                    <button class="btn <?php echo $accessbtnclasssuccess; ?> " onclick="skillpopupNew()"><i class="icofont icofont-plus icon-white "></i> Add Skill</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="emp_skills_form" >
                                  <div id="ajaxemployeempskillstab">
                            <?php
                                  $_POST['edittype'] = "VIEW";
                                  $_POST['showpagetype'] = "emp_skills";
                                  if(file_exists('views/custom_insert_onboarding_employees_ajax.php')){
                                    include_once "views/custom_insert_onboarding_employees_ajax.php";
                                }else{
                                    include_once "custom_insert_onboarding_employees_ajax.php";
                                }
                                  unset($_POST['edittype']); unset($_POST['showpagetype']);
                              ?>
                                  </div>
                              </form>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="home-6" role="tabpanel" aria-labelledby="home-tab-6">
            <?php if($reporting_manager['id'] !=""){  ?>
            <div class="card" >
                <div class="card-header">
                    <h5>Reporting Manager</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card sale-card ">
                                <div class="card-header">
                                    <h5><a href="#" class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0"><?php echo ucwords($reporting_manager['display_name']);?></a></h5>
                                </div>
                                <div class="card-block text-center">
                                    <div data-label="<?php echo initials($reporting_manager['display_name']); ?>" class="radial-bar custom-radial-bar radial-bar-40 radial-bar-lg radial-bar-danger mt-0 mb-2">
                                        <?php if($reporting_manager['user_photo']!=""){ ?>
                                            <img src="uploads/all/<?php echo $reporting_manager['user_photo']; ?>" alt="User Profile">
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <?php if($reporting_manager['user_email'] !=""){?>
                                            <span class="text-muted font-weight-bold f-12"><?php echo '<i class="ti-email"></i> '.$reporting_manager['user_email'];?></span>
                                        <?php } ?>
                                        <?php if($reporting_manager['user_mobile'] !=""){?>
                                            <span class="text-muted font-weight-bold f-12"><?php echo '<i class="fa fa-phone"></i> '.$reporting_manager['user_mobile'];?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }if(count((array)$teams) !=0){ ?>
            <div class="card" >
                <div class="card-header">
                    <h5>Teams</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($teams as $team) {  ?>
                            <div class="col-xl-3 col-md-6">
                                <div class="card sale-card ">
                                    <div class="card-header">
                                        <h5><?php echo ucwords($team['display_name']);?></h5>
                                    </div>
                                    <div class="card-block text-center">
                                        <div data-label="<?php echo initials($team['display_name']); ?>" class="radial-bar custom-radial-bar radial-bar-40 radial-bar-lg radial-bar-danger mt-0 mb-2">
                                            <?php if($team['user_photo']!=""){ ?>
                                                <img src="uploads/all/<?php echo $team['user_photo']; ?>" alt="User Profile">
                                            <?php } ?>

                                        </div>
                                        <div class="row  mb-3">
                                            <span class="text-muted font-weight-bold f-12">&nbsp;<?php  echo $emp_designation[$team['designation_id']]; ?></span>
                                            <span class="text-muted font-weight-bold f-12"><?php echo $team['user_email']!=""?'<i class="ti-email"></i> '.$team['user_email']:"";?></span>
                                            <span class="text-muted font-weight-bold f-12">&nbsp;<?php echo $team['user_mobile']!=""?'<i class="fa fa-phone"></i> '.$team['user_mobile']:"";?></span>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="s-cont d-inline-block">
                                                    <input aria-label="Checkbox" type="checkbox" class="js-success-sm" <?php if($team['on_off_site']==0){ echo "checked";
} ?> onchange="changeonsite(<?php echo $team['id']; ?>,'<?php echo ucwords($team['display_name']);?>')" value="1"/>&nbsp;&nbsp;<span style="font-size: 12px;
 <?php if($team['on_off_site']==1){ echo "color: red";
} ?>" class="col-form-label onsite_lable<?php echo $team['id']; ?>">
<?php if($team['on_off_site']==1){ echo "Off-Site";
}else{ echo "On-Site";
} ?> </span>
                                                    <input type="hidden" id="onsite_<?php echo $team['id']; ?>" value="<?php echo $team['on_off_site']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-6 b-l-default">
                                                <div class="s-cont d-inline-block">
                                                    <input aria-label="Checkbox" type="checkbox" class="js-primary-sm" <?php if($team['billable']=='0'){ echo "checked";
} ?> onchange="changebillable(<?php echo $team['id']; ?>,'<?php echo ucwords($team['display_name']);?>')" value="1"/>&nbsp;&nbsp;
                                                    <span style="font-size: 12px; <?php if($team['billable']=='1'){ echo "color: red";
} ?>" class="col-form-label billable_lable<?php echo $team['id']; ?>" >
 <?php if($team['billable']=='1'){ echo "Non Billable";
}else{ echo "Billable";
} ?> </span>
                                                    <input type="hidden" id="billable_<?php echo $team['id']; ?>" value="<?php echo $team['billable']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php }else{  ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Team</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
        </div>
        <div class="tab-pane fade" id="home-7" role="tabpanel" aria-labelledby="home-tab-7">
            <?php include_once 'custom_list_calendor.php'; ?>
        </div>
        <div class="tab-pane fade" id="home-att" role="tabpanel" aria-labelledby="home-tab-att">
            <?php include_once 'custom_view_monthly_attendance.php'; ?>
        </div>
        <div class="tab-pane fade" id="home-70" role="tabpanel" aria-labelledby="home-tab-70">
              <?php include_once "custom_basic_info_pay.php";   ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal_status" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title modallabel" id="exampleModalLabel">Effort Share Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="reloadPage()"></button>
                </div>
                <form class="form" id="insertemphistries">
                    <input type="hidden" name="emp_id" id="emp_id">
                    <input type="hidden" name="field_name" id="field_name">
                    <input type="hidden" name="field_value" id="field_value">
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-6 form-group">
                                      <span class="form-label d-block">Employee name </span>
                                      <p class="modal_view_employee_name"></p>
                                  </div>
                                  <div class="col-xl-6 form-group">
                                      <span class="form-label d-block">Status</span>
                                      <p class="modal_view_status"></p>
                                  </div>
                              </div>
                            <div class="form-group row">
                                <label for="example-date-input-from" class="col-4 col-form-label">Effective From</label>
                                <div class="col-5">
                                    <input class="form-control datepicker" type="text" name="effective_from" value="2011-08-19" id="example-date-input-from">
                                </div>
                                <label for="example-date-input-to" class="col-4 col-form-label mt-2">Effective To</label>
                                <div class="col-5 mt-2">
                                    <input class="form-control datepicker" type="text" name="effective_to" value="2011-08-19" id="example-date-input-to">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary " onclick="save_statuse_bilable_onsite()">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
