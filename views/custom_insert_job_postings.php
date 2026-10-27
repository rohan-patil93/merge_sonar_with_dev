<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['textediter'] = '1'; 
$_SESSION['apply_js']['inputmask'] = '1';
//$_SESSION['apply_js']['jobposting'] = '1'; 
//echo "<pre>"; print_r($_POST); echo "</pre>";
$mandetorycandidatevalue = array('first_name'=>'First Name','middle_name'=>'Middle Name','last_name'=>'Last Name','address'=>'Address','post_code'=>'Post Code','email_id'=>'Email','gender'=>'Gender','disability'=>'Disability','marital_status'=>'Marital Status','date_of_birth'=>'Date Of Birth','blood_group'=>'Blood Group','adhar_no'=>'Aadhar No','pan_number'=>'Pan Number','total_experiance'=>'Total Experiance','notice_period'=>'Notice Period','excepted_doj'=>'Expected Date Of Join','current_location'=>'Current Location','current_ctc'=>'Current CTC','expected_ctc'=>'Expected CTC');
if(isset($_POST['post_job']) && $alreadypost_job != '1'){
    $url = API."?mode=insertjobpost&method=post";
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['userid'] = $_SESSION['userid'];
    $screen_question = array();
    foreach($_POST['screening_question_seq'] as $quekey=>$qua){
        $questionlist = array();
        if($_POST['screening_question'][$quekey] !=""){
            $questionlist['question'] = $_POST['screening_question'][$quekey];
            $questionlist['mandatory_question'] = $_POST['screening_mandatory_question'][$quekey] =="Y"?"Y":"N";
            $questionlist['question_answer'] = $_POST['screening_question_answer'][$quekey];
        }
        $screen_question[] =$questionlist; 
    }
    $_POST['question'] = json_encode($screen_question);
    $_POST['candidate_mandatory_field'] = json_encode($_POST['candidate_mandatory_field']);
    $_POST['job_tag'] = json_encode($_POST['job_tag']);
    $_POST['summa'] = base64_encode($_POST['summa']);
    $_POST['min_salary'] = convertamountnumber($_POST['min_salary']);
    $_POST['max_salary'] = convertamountnumber($_POST['max_salary']);
    $_POST['annual_salary'] = convertamountnumber($_POST['annual_salary']);
    $_POST['candidate_show_status'] = isset($_POST['candidate_show_status'])?"0":"1";
    $_POST['aa_eeo'] = isset($_POST['aa_eeo'])?"0":"1";
  //   echo "<pre>"; print_r($_POST); echo "</pre>";
    $responses = file_post_contents($url, $_POST);
    $response = json_decode($responses,1);
    if($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
           $error_message = $response['message'];
        }
    }else{
        if($_POST['submit_value'] == 'exit'){
           header("location:index.php?option=COM_LIST_JOB_POSTING_CANDIDATES"); exit;
        }else{
          ?>
          <script type="text/javascript">
              document.onreadystatechange = function () {
                if (document.readyState == "complete") {
                  tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3000]; ?>','');
                 //location.reload();
              } 
          }
        </script>
    <?php
        }
    }
}
$rid = encodeId($_GET['Rid']);
$rid = explode(":", $rid);
$rid = $rid[1];
$url = API."?mode=getjobpostings&rid=".$rid; 
$resret = callAPI($url);
$results  = json_decode($resret,1);
//echo "<pre>"; print_r($results); echo "</pre>";
$title_list  =$results['job_titles'];
$location_list = $results['locations'];
$user_list = $results['application_users'];
$recuitmanager  = array();
foreach($user_list as $userlist){
  $role = json_decode($userlist['role_id'],1);
  if(in_array(RECRUITINGMANAGER_ROLE, (array)$role)){
    $recuitmanager[] = $userlist;
}
if(in_array(HIRINGMANAGER_ROLE, (array)$role)){
    $hiringmanager[] = $userlist;
}
$userlists[] =$userlist;
}

$department_list = $results['departments'];
$customers = $results['customers'];
$esginbudget_list = $results['esgin_budget_approvals'];
$jobselection_process_list  = $results['job_selection_process'];
$jobpostlist = $results['job_postings'];
$tagslist = $results['application_tags'];
$joblevel_list = $results['job_grade'];

?>
<style>
   iframe{
      width: 200%;
  }
</style>
<script>
    function aIGenerateJobDescription() {
        $("#ai_generate_job_description").text("Please Wait........");
        $("#ai_generate_job_description").addClass("disabled");
        var post = {};
        $('#jobpostongform').serializeArray().forEach(function(item) {
            var field = $('[name="' + item.name + '"]');
            if (field.is('select')) {
                post[item.name] = field.find('option:selected').text();
            } else {
                post[item.name] = item.value;
            }
        });
        $.ajax({
            url: "apis/recruitstac_ai_api_callings.php",
        //url: "https://callen-ungossiping-nonoily.ngrok-free.dev/api/jobs/create-jd",
            type: "POST",
            method: "POST",
            data: post,
            success: function(response) {
                console.log(response);
                var datas = jQuery.parseJSON(response);
                $("#job_responsibility").val(datas.responsibilities);

                var screening_questions = datas.screening_questions;
                screening_questions.forEach(function(sque){
                       addQuestionsTextFileds(sque); 
                });

                var tags = datas.tags;
                var select = $('#kt_select2_11');
                tags.forEach(function(tag){
                    var option = new Option(tag, tag, true, true);
                    select.append(option);
                });
                select.trigger('change');

                $("#summa_ifr").contents().find("#tinymce").html(datas.description.replace(/\n/g, '<br>'));
                $("#textediters").html(datas.description.replace(/\n/g, '<br>'));
                $("#noteguidelines").html(datas.description.replace(/\n/g, '<br>'));

                $("#ai_generate_job_description").text("AI Generate");
                $("#ai_generate_job_description").removeClass("disabled");
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $("#ai_generate_job_description").text("AI Generate");
                $("#ai_generate_job_description").removeClass("disabled");
                alert(xhr.status);
                alert(thrownError);
            }
        });
    }
    function selectoptions(tablename,filedname,value,name){
        if(value == "-0"){  
            $.post("views/addoption.php",{"tablename":tablename,"filedname":filedname,"value":value,"name":name},function(result){
                $('.optionvalue').html(result);
                $('#exampleModalSizeSm').modal('show');
            })
        }      
    }
    function coursetypefun(id){
          $(".coursetypeemty").addClass("btn-outline-info");
          $(".coursetypeemty").removeClass("btn-info");
          $(".coursetypeemty").removeClass("text-white");
          $("#coursetype"+id).removeClass("btn-outline-info");
          $("#coursetype"+id).addClass("btn-info");
          $("#coursetype"+id).addClass("text-white");
          $("#employee_mode").val(id);
    }
    function deleteQuestion(id){
       $(".rows"+id).remove();
    }
    function addvideoextrafileds(){
      $(".video").empty();
      $(".video").append($("#video_url").val());
      $("#collapseOne4").addClass('collapse show');
    }
    var max = 500;
    function addQuestionsTextFileds(val=''){
        max++;
        var html = `<div class="row rows`+max+` mt-2"> 
                <input type="hidden" name="screening_question_seq[`+max+`]" name="`+max+`">
                <div class="col-lg-5">
                    <input class="form-control" type="text" name="screening_question[`+max+`]" value="`+val+`" placeholder="Eg. Tell me about yourself">
                </div>
                <div class="col-lg-1">
                    <div class="switch-field">
                        <input class="js-switch-`+max+`" name="screening_mandatory_question[`+max+`]" type="checkbox" value="Y">
                    </div>
                </div>
                <div class="col-lg-3">
                    <input class="form-control" type="text" name="screening_question_answer[`+max+`]" value="" placeholder="Eg. Yes,No">
                </div>
                <div class="col-lg-2">
                    <i title="Delete Question" onclick="deleteQuestion(`+max+`)" class="fa fa-trash text-danger cursor-pointer"></i> 
                </div>
            </div>`;
        // var html = `<div class="row rows`+max+` mt-2"><input type="hidden" name="question_seq[]" name="`+max+`"> 
        //         <div class="col-lg-10">
        //             <input class="form-control" type="text" name="question[`+max+`]" value="`+val+`" placeholder="Eg. Tell me about yourself">
        //         </div>
        //         <div class="col-lg-2">
        //             <i title="Delete Question" onclick="deleteQuestion(`+max+`)" class="fa fa-trash text-danger cursor-pointer"></i> 
        //         </div>
        //     </div>`;
        $("#videoquestiontextfileds").append(html);
        var elemdefault = document.querySelector('.js-switch-'+max);
        var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff' });

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
        value = value.split("<?php echo $_SESSION['config']['currency_symbol']; ?>");
        value = value[1].replaceAll(",", '');
        var number_of_employee = $("#number_of_employee").val();
        var total = value*number_of_employee;
        var output=parseInt(total).toLocaleString()+".00";
        $("#annual_salary").val("<?php echo $_SESSION['config']['currency_symbol']; ?>"+output);
    }
    function cehckPostigType(){
        $('input[name="posting_type"]:checked').each(function() {
           if(this.value == '1'){
                 $("#customershow").hide();
                 $("#departmentshow").show();

            }else{
                $("#departmentshow").hide();
                $("#customershow").show();
            }
        });
    }
    function checkQuetion(divclass){
        if ($("#"+divclass).is(":checked")) {
            $("."+divclass).show('slow');
            $("."+divclass+"-input").prop('disabled', false);
        }else{
            $("."+divclass).hide('slow');
            $("."+divclass+"-input").prop('disabled', true);
        }   
    }
</script>

<div class=" d-flex flex-column-fluid flex-column flex-root  ">
 <div class="d-flex flex-row flex-column-fluid page ">
    <div class="container-fluid" id="kt_wrapper">
       <div class="content  d-flex flex-column flex-column-fluid card card-custom" id="kt_content">
          <?php if($error_message !=''){ ?>
              <div class="alert alert-danger background-danger alert-dismissible">
                <strong><?php echo $error_message; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>	
        <?php } ?>
        <div class="row post">

         <div class="col-lg-8 col-md-12 col-sm-12 col-12">
            <form method="post" id="jobpostongform"> 
                <input class="form-control" type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>" >
                <input class="form-control" type="hidden" name="data_name" value="<?php echo $_SESSION['DB_NAME']; ?>" >
                <input class="form-control" type="hidden" name="user_id" value="<?php echo $_SESSION['userid']; ?>" >

                <input type="hidden" name="id" value="<?php echo $rid; ?>">

                <div class="card-header ">
                 <h5>Job Posting</h5>      
             </div>
             <div class="card-body">
                 <div class="">
                   <div class="col-12 ">
                    <div class="row mt-2">
                      <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10" data-step="1" data-intro="Hello world">
                         <label class="form-label">Job Title<span class="requried text-danger">*</span></label>
                         <div>
                          <span class="d-block error-message text-danger"></span>
                          <select class="form-select js-example-basic-single col-sm-12 selectjobtitle requried_field" onchange="selectJobTitle()"  name="job_title" id="job_title" required>
                             <option value="-1">Select Job Title</option>
                             <?php foreach($title_list as  $title_listrow){ ?>
                                 <option value="<?php echo $title_listrow['id']; ?>"  <?php echo $jobpostlist['job_code']==$title_listrow['id']?"selected":""; ?> ><?php echo $title_listrow['job_title']; ?></option>
                             <?php } ?>
                         </select>
                     </div>
                 </div>
                 <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10" data-step="2" data-intro="Job number">
                  <label class="form-label mt-1">Job Number</label>
                  <div class="">
                   <span class="d-block error-message text-danger"></span>
                   <input class="form-control" type="text" value="<?php echo $jobpostlist['job_number']!=""?$jobpostlist['job_number']:$jobnumber; ?>"  name="job_number" id="job_number" readonly="readonly">
               </div>
           </div>
       </div>
       <div class="row mt-2">
          <div class="col-lg-12 m-b-10" data-step="3" data-intro="Post Name">
             <label class="form-label mt-1">Name <span class="requried text-danger">*</span></label>
             <div class="">
              <span class="d-block error-message text-danger"></span>
              <input class="form-control requried_field" type="text" name="job_name" value="<?php echo $jobpostlist['job_name']; ?>" id="job_name" required>
          </div>
      </div>
      <div class="col-lg-12 m-b-10" data-step="4" data-intro="Location">
         <label class="form-label mt-1">Location</label>
         <div>
            <select class="form-select js-example-basic-single" id="kt_select2_1" name="location" id="location" onchange="selectoptions('locations','location_name',this.value,'location')">
             <option value="-1">Select location</option>
             <?php foreach($location_list as $location_listrow){ ?>
                <option value="<?php echo $location_listrow['id']; ?>"  <?php echo $jobpostlist['location_id']==$location_listrow['id']?"selected":""; ?>><?php echo $location_listrow['location_name']; ?></option>
            <?php } ?>

        </select>
    </div>
</div>
</div>
<div class="row mt-2">
  <div class="col-lg-6 m-b-10" data-step="5" data-intro="Request By">
      <label class="form-label mt-1">Requested By   <?php $request = isset($jobpostlist['requested_by'])?$jobpostlist['requested_by']:$_SESSION['userid']; ?>
  </label>
  <div >
      <span class="d-block error-message text-danger"></span>
      <select   class=" form-select js-example-basic-single requried_field" id="requested_by" name="requested_by">
         <option value="-1">Select Requested</option>

         <?php
         foreach($hiringmanager as $user_listrow){ ?>
            <option value="<?php echo $user_listrow['id']; ?>"  <?php echo $user_listrow['id']==$request?"selected":""; ?>><?php echo $user_listrow['display_name']; ?></option>
        <?php } ?>

    </select>
</div>
</div>
<div class="col-lg-6 m-b-10" data-step="6" data-intro="Requested Date">

  <label class=" form-label mt-1">Requested Date</label>
  <div class="">
    <span class="d-block error-message text-danger"></span>
    <input class="form-control datepicker requried_field" type="text" value="<?php echo $jobpostlist['requested_date']!=""?fromDate($jobpostlist['requested_date']):fromDate(date('Y-m-d')); ?>" name="requested_date"  id="requested_date" />
</div>
</div>
</div>
<div class="row mt-2">
  <div class="col-lg-6 m-b-10" data-step="7" data-intro="Recruitment Manager">
      <label class="form-label mt-1">Recruitment Manager <?php //print_r($user_list); ?></label>
      <div >
        <span class="d-block error-message text-danger"></span>
        <select class=" form-select js-example-basic-single requried_field" id="recruitment_by" name="recruitment_by">
           <option value="-1">Select Recruitment</option>
           <?php foreach($recuitmanager as $user_listrow){  ?>
            <option value="<?php echo $user_listrow['id']; ?>"  <?php echo $jobpostlist['recruitment_manager']==$user_listrow['id']?"selected":""; ?> ><?php echo $user_listrow['display_name']; ?></option>
        <?php } ?>

    </select>
</div>
</div>
<div class="col-lg-6 m-b-10" data-step="8" data-intro="Recruitment Date">
  <label class=" form-label mt-1">Required By Date</label>
  <div class="">
    <span class="d-block error-message text-danger"></span>
    <input class="form-control datepicker requried_field" type="text" value="<?php echo $jobpostlist['required_date']!=""?fromDate($jobpostlist['required_date']):fromDate(date('Y-m-d')); ?>" name="recruitment_date"  id="recruitment_date"/>
</div>
</div>
</div>

<div class="row mt-2">
  <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
    <label class="form-label">Minimum Experience Year(s) <span class="requried text-danger">*</span></label>
    <span class=" d-block error-message text-danger"></span>
    <select class="form-select js-example-basic-single requried_field" id="min_experience" name="min_experience" id="min_experience">
      <?php for($m=0;$m<=10;$m++){ ?>
          <option value="<?php echo $m; ?>" <?php echo number_format($jobpostlist['min_exp'])==$m?"selected":""; ?>><?php echo $m; echo $m=="1"?" Year":" Years"; ?></option>
      <?php } ?>
  </select>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
  <label class="form-label">Maximum Experience Year(s) <span class="requried text-danger">*</span></label>
  <span class="d-block error-message text-danger"></span>
  <select class="form-select js-example-basic-single requried_field" id="max_experience" name="max_experience" required>
      <option value="-1">Select Max Exp</option>
      <?php for($m=1;$m<=10;$m++){ ?>
          <option value="<?php echo $m; ?>" <?php echo number_format($jobpostlist['max_exp'])==$m?"selected":""; ?>><?php echo $m; echo $m=="1"?" Year":" Years"; ?></option>
      <?php } ?>
  </select>
</div>

</div>
</div> 
<div class="row mt-2">
  <label class="col-lg-8 col-md-8 col-sm-12 col-12 form-label mt-1">Showcase Your Brand</label>
  <span class="col-lg-8 col-md-8 col-sm-12 col-12 mb-4">Add a video to tell your brand's story and show candidates what it is like to work at your company</span>
  <div class="col-lg-8 col-md-8 col-sm-12 col-12">

     <input class="form-control" type="text" name="video_url"   id="video_url" value="<?php echo $jobpostlist['video_url']; ?>" placeholder="Eg. https://www.youtube.com/">
     <div id="videoextrafileds" >
     </div>

 </div>
</div>

<h3 class="text-dark font-weight-bold mt-5 mt-3 mr-5">Targeting</h3>
<hr>
<div class="row">
  <div class="col-12">
     <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-12">

           <label class="form-label mt-1 d-block">Job Posting Type</label>
           <div class="form-radio d-inline-block align-middle">
            <div class="radio radio-inline">
              <label class="form-label">
                <input type="radio" <?php echo $jobpostlist['posting_type']=='1'?"checked":"";?> name="posting_type" value="1" onclick="cehckPostigType()"><i class="helper"></i>Internal
            </label>
        </div>
        <div class="radio radio-inline">
          <label class="form-label">
           <input type="radio" name="posting_type" <?php echo $jobpostlist['posting_type']=='2'?"checked":"";?> value="2" onclick="cehckPostigType()">
           <i class="helper"></i>External
       </label>
   </div>
</div>

</div>

<div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10 w-full" id="departmentshow" <?php echo $jobpostlist['posting_type']=='1'?"":"style='display:none'";?>>
   <label class="form-label mt-1"> Department</label>
   <select class="form-select js-example-basic-single form-control" id="" onchange="selectoptions('departments','department_name',this.value,'department_name')" name="department_name">
      <option value="-1">Select Any One</option>
      <?php foreach($department_list as $department_listrow){ ?>
          <option value="<?php echo $department_listrow['id']; ?>"  <?php echo $jobpostlist['department_id']==$department_listrow['id']?"selected":""; ?>><?php echo $department_listrow['department_name']; ?></option>
      <?php } ?>

  </select>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10 w-full" id="customershow" <?php echo $jobpostlist['posting_type']=='2'?"":"style='display:none'";?>>
   <label class="form-label mt-1"> Customer</label>
   <select class="form-select js-example-basic-single form-control" id=""  name="customer_id">
      <option value="-1">Select Any One</option>
      <?php foreach($customers as $customer){ ?>
          <option value="<?php echo $customer['id']; ?>"  <?php echo $jobpostlist['customer_id']==$customer['id']?"selected":""; ?>><?php echo $customer['display_name']; ?></option>
      <?php } ?>

  </select>
</div>
</div>
</div>
</div>
<div class="row">
  <div class="col-12">
     <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12 col-12 m-b-10">
           <label class="form-label mt-1">Job Level</label>
           <select class="form-select js-example-basic-single form-control" id="" name="job_level">
              <option value="-1">Select job Level</option>
              <?php foreach($joblevel_list as $joblevel_list){ ?>

                  <option value="<?php echo $joblevel_list['id']; ?>" <?php echo $jobpostlist['job_level_id']==$joblevel_list['id']?"selected":""; ?>><?php echo $joblevel_list['job_grade_name']; ?></option>   
              <?php   }?>

          </select>
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12 col-12 m-b-10">
       <label class="form-label mt-1">Job Selection Process <span class="requried text-danger">*</span></label>
       <span class="d-block error-message text-danger"></span>
       <select class="form-select js-example-basic-single form-control requried_field" id="job_selection_process" name="job_selection_process" required> 
          <option value="">Select Job Selection process</option>
          <?php foreach($jobselection_process_list as $jobselection_process_listroe){ ?>
              <option value="<?php echo $jobselection_process_listroe['id']; ?>" <?php echo $jobpostlist['job_selection_process']==$jobselection_process_listroe['id']?"selected":""; ?>><?php echo $jobselection_process_listroe['process_name']; ?></option>

          <?php } ?>
      </select>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-12 col-12 m-b-10">
    <label class="form-label mt-1">Number Of Openings</label>
    <div class="">
       <input class="form-control" type="text" value="<?php echo $jobpostlist['no_of_resources']; ?>" name="number_of_employee" id="number_of_employee" placeholder="Eg: 4" />
   </div>
</div>  


</div>
</div>
</div>
<div class="row m-b-10">
  <div class="col-12">
      <label class="form-label mt-1">Annual Salary(<?php echo $_SESSION['config']['currency_symbol']; ?>)</label>
      <div class="row">
       <div class="col-lg-4 m-b-10">
        <label class="form-label">Min Salary</label>
        <input class="form-control autonumber" type="text" value="<?php echo $jobpostlist['min_salary']; ?>" name="min_salary" data-a-sign="<?php echo $_SESSION['config']['currency_symbol']; ?>" placeholder="eg. 4,000,000"  />
    </div>
                                      <!--  <div class="col-6">
                                        <label>Min Salary</label>
                                       <select class="form-control" id="" name="annual_salary">
                                          <option value="">Min Salary(in lakhs)</option>
                                             <?php for($m=1;$m<=10;$m++){ ?>
                                          <option value="<?php echo $m; ?>" <?php echo $jobpostlist['total_resource_cost']==$m?"selected":""; ?>><?php echo $m; ?></option>
                                          <?php } ?>
                                       </select>
                                    </div> -->
                                    <div class="col-lg-4 m-b-10">
                                        <label class="form-label">Max Salary</label>
                                        <input type="text" class="form-control autonumber"  value="<?php echo $jobpostlist['max_salary']; ?>" onblur="maxamountemplye(this.value)" name="max_salary" placeholder="eg. 4,000,000" data-a-sign="<?php echo $_SESSION['config']['currency_symbol']; ?>">

                                    </div>
                                    <div class="col-lg-4 m-b-10">
                                        <label class="form-label">Total Salary</label>
                                        <!--           <input class="form-control inr_amount" type="text" value="<?php echo $jobpostlist['total_resource_cost']; ?>"  name="annual_salary"  id="annual_salary" > -->

                                        <input type="text" class="form-control autonumber inr_amount"  value="<?php echo $jobpostlist['total_resource_cost']; ?>"  name="annual_salary"  id="annual_salary" data-a-sign="<?php echo $_SESSION['config']['currency_symbol']; ?>" >

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8">  
                               <div class="row">
                                   <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
                                        <label class="form-label mt-1"> Esgin Budget</label>
                                        <select class="form-select js-example-basic-single" id="" name="esgin_budget">
                                           <option value="">Select Esgin Budget</option>
                                           <?php foreach($esginbudget_list as $esginbudget_list){ ?>
                                            <option value="<?php echo $esginbudget_list['id']; ?>"  <?php echo $jobpostlist['esgin_budget_approval_id']==$esginbudget_list['id']?"selected":""; ?>><?php echo $esginbudget_list['transaction_number']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                        <label class="form-label mt-1">Employment Type</label>
                                        <div class="form-radio d-inline-block align-middle">
                                            <div class="radio radio-inline">
                                                  <label class="form-label">
                                                    <input  type="radio" <?php echo $jobpostlist['employment_type']=='2'?"checked":"";?> name="employment_type" value="2"><i class="helper"></i>Contract
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                    <label class="form-label">
                                                    <input type="radio" name="employment_type" <?php echo $jobpostlist['employment_type']=='1'?"checked":"";?> value="1">
                                                    <i class="helper"></i>Regular
                                                    </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12 m-b-10">
                                        <label class="form-label mt-1"> Candidate Mandatory Field Value </label>
                                        <?php  
                                        $candidate_mandatory_field = json_decode($jobpostlist['candidate_mandatory_field'],1);
                                        ?>
                                        <select class="form-select js-example-basic-multiple" id="" name="candidate_mandatory_field[]" multiple>
                                           <?php foreach($mandetorycandidatevalue as $fkey=>$fval){ ?>
                                            <option value="<?php echo $fkey; ?>"  <?php echo in_array($fkey, (array)$candidate_mandatory_field)?"selected" : ""; ?>><?php echo $fval; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
            <div class="col-12 mt-3">

             <div class="checkbox-fade fade-in-primary">
                <label class="form-label">
                  <input type="checkbox" name="candidate_show_status" <?php echo $jobpostlist['candidate_show_status']=="0"?"checked":"";?> >
                  <span class="cr">
                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                <span style="display: table;">Don't show this to candidates</span>
            </label>
        </div>
    </div>
    <div class="row">

      <div class="col-12">
         <h3 class="text-dark font-weight-bold mt-5 mt-3 mr-5">Education Type</h3>
         <input type="hidden" name="employee_mode" id="employee_mode" value="<?php echo $jobpostlist['employment_mode']; ?>">
         <ul class="post-job">
            <li onclick="coursetypefun(1)">
               <a class="btn <?php echo $jobpostlist['employment_mode']=="1"?"btn-info text-white":"btn-outline-info"; ?> coursetypeemty" id="coursetype1"  data-id="1" >
                  <i class="fa fa-graduation-cap mb-4"></i>
                  <h6>Full Time</h6>
              </a>
          </li>
          <li onclick="coursetypefun(2)">
           <a class="btn <?php echo $jobpostlist['employment_mode']=="2"?"btn-info text-white":"btn-outline-info"; ?> coursetypeemty" id="coursetype2" data-id="2" >
              <i class="fa fa-graduation-cap mb-4"></i>
              <h6>Part Time</h6>
          </a>
      </li>
      <li onclick="coursetypefun(3)">
       <a class="btn <?php echo $jobpostlist['employment_mode']=="3"?"btn-info text-white":"btn-outline-info"; ?> coursetypeemty" id="coursetype3" data-id="3" >
          <i class="fa fa-graduation-cap mb-4"></i>
          <h6>Distance Learning Program</h6>
      </a>
  </li>
  <li onclick="coursetypefun(4)">
   <a class="btn <?php echo $jobpostlist['employment_mode']=="4"?"btn-info text-white":"btn-outline-info"; ?> coursetypeemty"  id="coursetype4" data-id="4"  >
      <i class="fa fa-graduation-cap mb-4"></i>
      <h6>Executive Program</h6>
  </a>
</li>
<li onclick="coursetypefun(5)">
   <a class="btn <?php echo $jobpostlist['employment_mode']=="5"?"btn-info text-white":"btn-outline-info"; ?> coursetypeemty" id="coursetype5" data-id="5">
      <i class="fa fa-graduation-cap mb-4"></i>
      <h6>Certification</h6>
  </a>
</li>
</ul>
</div>

<div class="row mt-2">
    <label class="col-4 form-label mt-1">Job Responsibility</label> 
    <div class="col-8 mt-1">
       <button type="button" class="btn btn-primary float-right mb-1" type="button" id="ai_generate_job_description" onclick="aIGenerateJobDescription()" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click here to generate AI - Responsibilities, Screening Questions, Job Description & Tags" ><i class="ti-light-bulb"></i> AI Generate</button>
   </div>
   <div class="col-12">
    <textarea class="form-control" placeholder="" name="job_responsibility" id = "job_responsibility" style="height: 80px;"><?php echo $jobpostlist['job_responsibility'];?></textarea>
</div>
</div> 


<h3 class="text-dark font-weight-bold mt-5 mt-3 mr-5">Applications</h3>
<hr>

<h4 class="mt-2">Screening Questions</h4>
<h6 class="font-weight-normal mb-4">You can ask some questions before the candidates apply to your job!</h6>
<div class="col-lg-12"> 
    <div class="row rows<?php echo $ij; ?> mt-2"> 
        <div class="col-lg-5">
            <label class="form-label">Questions</label> 
        </div>
        <div class="col-lg-1">
            <label class="form-label">Mandatory</label> 
        </div>
        <div class="col-lg-4">
            <label class="form-label">Accepted Answer</label> 
        </div>
        <div class="col-lg-2">

        </div>   
    </div> 
</div>
<div class="col-lg-12" id="videoquestiontextfileds"> 
    <?php $ij=1; 
    $questiona = json_decode($jobpostlist['question'],1);
    if(count((array)$questiona) == '0'){  $questiona[] =  array(); } 
        foreach($questiona as $question){ ?>
             <div class="row rows<?php echo $ij; ?> mt-2"> 
                <input type="hidden" name="screening_question_seq[<?php echo $ij; ?>]" name="<?php echo $ij; ?>">
                <div class="col-lg-5">
                    <input class="form-control" type="text" name="screening_question[<?php echo $ij; ?>]" value="<?php echo $question['question']; ?>" placeholder="Eg. Tell me about yourself">
                </div>
                <div class="col-lg-1">
                    <div class="switch-field">
                        <input class="js-switch" name="screening_mandatory_question[<?php echo $ij; ?>]" type="checkbox" value="Y" <?php echo $question['mandatory_question'] == "Y"?"checked":""; ?>>
                    </div>
                </div>
                <div class="col-lg-3">
                    <input class="form-control" type="text" name="screening_question_answer[<?php echo $ij; ?>]" value="<?php echo $question['question_answer']; ?>" placeholder="Eg. Yes,No">
                </div>
                <div class="col-lg-2">
                    <i title="Delete Question" onclick="deleteQuestion(<?php echo $ij; ?>)" class="fa fa-trash text-danger cursor-pointer"></i> 
                </div>
            </div>     
        <?php 
            $ij++; } 
        ?>
</div>
<div class="col-lg-3">
<button type="button" onclick="addQuestionsTextFileds()" class="btn btn-outline-success mt-2">
   <i class="fas fa-plus"></i> Add Question
</button>
</div>
 <?php /** ?> 
<div class="col-lg-12">
    <div class="checkbox-fade fade-in-primary">
      <label class="form-label">
        <input type="checkbox" id="aa_eeo" name="aa_eeo" value="" <?php echo $jobpostlist['aa_eeo'] == '0'?"checked":"";?> onchange="checkQuetion('aa_eeo')">
        <span class="cr">
          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
      </span>
      <span style="display: table;">My company is a government contractor or I'd like to capture applicant gender, ethnicity, disability, or veteran status.</span>
  </label>
</div>

</div> 

<div class="col-lg-8"> 
  
    <div class="row rows aa_eeo" <?php echo $jobpostlist['aa_eeo'] == '0'?'':'style="display: none"';?>> 
      <input class="form-control mt-2 col-lg-10 aa_eeo-input" type="text" name="question1[]" value="Do you have any disability or accessibility requirements?" >
  </div> 
  <div class="row rows aa_eeo" <?php echo $jobpostlist['aa_eeo'] == '0'?'':'style="display: none"';?>> 
      <input class="form-control mt-2 col-lg-10 aa_eeo-input" type="text" name="question1[]" value="Gender" >
  </div> 
  <div class="row rows aa_eeo" <?php echo $jobpostlist['aa_eeo'] == '0'?'':'style="display: none"';?>> 
      <input class="form-control mt-2 col-lg-10 aa_eeo-input" type="text" name="question1[]" value="Ethnicity" > 
  </div> 
  <div class="row rows aa_eeo" <?php echo $jobpostlist['aa_eeo'] == '0'?'':'style="display: none"';?>> 
      <input class="form-control mt-2 col-lg-10 aa_eeo-input" type="text" name="question1[]" value="Veteran Status" >
  </div> 
  <?php $ij=4; 
    if(isset($jobpostlist['question'])){ 
        $question = json_decode($jobpostlist['question'],1); 
        foreach($question as $question){ ?>
             <div class="row rows<?php echo $ij; ?>"> 

              <input class="form-control mt-2 col-lg-10" type="text" name="question[]" value="<?php echo $question; ?>" placeholder="Eg. Tell me about yourself">
              <i title="Delete Question" onclick="deleteQuestion(<?php echo $ij; ?>)" class="text-dark-50 flaticon-delete col-lg-2 mt-4"></i> 
          </div>     
          <?php $ij++; }
        }else{ ?> 
          <div class="row rows7"> 
              <input class="form-control mt-2 col-lg-10" type="text" name="question[]" value="" placeholder="Eg. Tell me about yourself"><i title="Delete Question" onclick="deleteQuestion(4)" class="text-dark-50 fa fa-trash col-lg-2 mt-4"></i> 
          </div> 
<?php } ?>

<!-- <div id="videoquestiontextfileds" >
</div> -->

<button type="button" onclick="addQuestionsTextFileds()" class="btn btn-outline-success mt-2">
   <i class="fas fa-plus"></i> Add Question
</button>
</div>
 <?php **/ ?> 
<div class="col-12">
   <h3 class="text-dark font-weight-bold mt-5 mt-3 mr-5">Job Tags</h3>
   <hr>
</div>
<div class="col-12">
    <div class="">
     <select class="form-select js-example-tags" id="kt_select2_11" multiple="multiple" name="job_tag[]">
      <?php foreach($tagslist as $tagslists){  ?>

        <option  selected><?php echo $tagslists['tages']; ?></option>
    <?php } ?>
</select>
</div>
</div>


<div class="col-12">
 <div class="py-2 px-4 boost">
    <h5><i class="fa fa-graduation-cap mb-4 mr-3"></i>Job Description</h5>

    <hr>
    <div class="col-12 mt-3">
       <div class="checkbox-inline">
        <div class="col-lg-12 col-md-12 col-sm-12">
         <!--   <textarea class="summernote" name="summa" id="kt_summernote_1"><?php echo base64_decode($jobpostlist['job_desc']); ?></textarea> -->
         <!--     <textarea class="summernote" name="description" ></div> -->

           <textarea class="form-control textediters" name="summa" id="<?php echo $key; ?>"><?php echo base64_decode($jobpostlist['job_desc']); ?></textarea>


       </div>
   </div>
</div>
</div>
<div class="mt-5">
   <input type="hidden" name="post_job"  value="save">
   <input type="hidden" name="submit_value" id="submit_form_type"   value="exit">
							 <!--   <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
						<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button> -->

                        <?php 
                        $enablebutton = array(ST_ACTIVE,'');
                        $disabledbtn = "";
                        if(!in_array($jobpostlist['current_status'], $enablebutton)){
                            $disabledbtn = "disabled";
                        }
                        ?>
                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 <?php echo $disabledbtn; ?>"   type="button" onclick="formSubmitValidation('jobpostongform','exit')"><i class="la la-check"></i>Save & exit</button>
                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 <?php echo $disabledbtn; ?>" type="button" onclick="formSubmitValidation('jobpostongform','continue')" ><i class="la la-check-circle"></i>Save & continue</button>


                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>




<div class="col-lg-4 col-md-12 col-sm-12 col-12">
    <div class="few">
       <h5>Few Guidelines</h5>
       <div id="noteguidelines"><?php echo base64_decode($jobpostlist['job_desc']);   ?></div>
   </div>
</div>
</div>
</div>
<!-- custom html end here -->

</div>
<!--end::Wrapper-->
</div>
<!--end::Page-->
</div>

































<?php 

$text = array("Job Title"=>"Job title list please select any one","Job Number"=>"Job bumber auto generate eg.JB00062 ","Job Name"=>"Enter job post name ","Location"=>"Select Office Location");


?>



<div id="kt_quick_cart" class="offcanvas offcanvas-right p-10">
 <!--begin::Header-->
 <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7" style="" kt-hidden-height="46">
    <h4 class="font-weight-bold m-0">Help Text</h4>
    <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_cart_close">
       <i class="ki ki-close icon-xs text-muted"></i>
   </a>
</div>
<!--end::Header-->
<!--begin::Content-->
<div class="offcanvas-content">
    <!--begin::Wrapper-->
    <div class="offcanvas-wrapper mb-5 scroll-pull scroll ps ps--active-y" style="height: 384px; overflow: hidden;">


        <?php foreach($text as $key => $text1){ ?>
           <div class="d-flex align-items-center justify-content-between py-2">
              <div class="d-flex flex-column mr-2">
                 <a href="#" class="font-weight-bold text-dark-75 font-size-lg text-hover-primary"><?php echo $key; ?></a>
                 <span class="text-muted"><i class=" fas fa-info-circle"></i> <?php echo $text1; ?></span>
             </div>
         </div>
         <div class="separator separator-solid"></div>
     <?php } ?>




     <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></div></div><div class="ps__rail-y" style="top: 0px; height: 384px; right: -2px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 231px;" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></div></div></div>
<!--end::Wrapper-->
</div>
<!--end::Content-->
</div>
<!-- Modal-->
<div class="modal fade" id="exampleModalSizeSm" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Enter Option</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body optionvalue">
            </div>
        </div>
    </div>
</div>


