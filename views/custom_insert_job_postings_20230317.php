
<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['textediter'] = '1'; 
$_SESSION['apply_js']['jobposting'] = '1';
   function convertamountnumber($ammunt){
      $amun =explode("Rs.",$ammunt);
      return str_replace(',', '',$amun[1]);
   }
if(isset($_POST['post_job'])){
      $url = API."?mode=insertjobpost&method=post";
      $_POST['company_id'] = $_SESSION['company_id'];
      $_POST['userid'] = $_SESSION['userid'];
      $_POST['question'] = json_encode($_POST['question']);
      $_POST['job_tag'] = json_encode($_POST['job_tag']);
      $_POST['summa'] = base64_encode($_POST['summa']);
 $_POST['min_salary'] = convertamountnumber($_POST['min_salary']);
 $_POST['max_salary'] = convertamountnumber($_POST['max_salary']);
  $_POST['annual_salary'] = convertamountnumber($_POST['annual_salary']);
     $_POST['candidate_show_status'] = isset($_POST['candidate_show_status'])?"0":"1";
      $response0 = file_post_contents($url, $_POST);
      $response = json_decode($response0,1);

if($response['status'] == 'fail'){
		if(isset($_SESSION['error'][$response['error_no']])){
			$dis_err = explode(",", $response['other']);
			//print_r($dis_labels); exit;
			foreach ($dis_err as $va) {
				$dis_er [] = $dis_labels[$va];
			}
			$error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
		}else{
			$error_message = $response['message'];
		}
	}else{
		if ($_POST['submit_value'] == 'continue'){
				//header("location:index.php?option=$add_url"); exit;
			}else{
				header("location:index.php?option=COM_LIST_JOB_POSTINGS"); exit;
			}
	}


	/*   if($_POST['post_job']=="exit"){
		   header('location:index.php?option=COM_LIST_JOB_POSTINGS');
	   }else{
			echo '<input type="hidden" id="message" value="'.$_POST['post_job'].'" data-id="'.$return['LASTID'].'">';
		
	   } */
 }
      //$jobnumber = toGetApplicationSerialNumber('job_postings');
       $wh1["where"] = array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
      $wh1["field"] = "id,job_title,job_description";
      //$wh1['order'] = "id DESC";
      $data1 = urlencode(json_encode($wh1));
      $url1 = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$data1; 
      $data_details1 = callAPI($url1);
      $title_list  = json_decode($data_details1,1);
      $wh2["where"] = array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
      $wh2["field"] = "id,location_name";
      $data2 = urlencode(json_encode($wh2));
      $url2 = API."?mode=gettabledetailswithconditions&table=locations&data=".$data2; 
      $data_details2 = callAPI($url2);
      $location_list  = json_decode($data_details2,1);
      $wh3["where"] = array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
      $wh3["field"] = "id,display_name";
      $data3 = urlencode(json_encode($wh3));
      $url3 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data3; 
      $data_details3 = callAPI($url3);
      $user_list  = json_decode($data_details3,1);
      $wh4["where"] = array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
      $wh4["field"] = "id,department_name";
      $data4 = urlencode(json_encode($wh4));
      $url4 = API."?mode=gettabledetailswithconditions&table=departments&data=".$data4; 
      $data_details4 = callAPI($url4);
      $department_list  = json_decode($data_details4,1);
      $wh5["where"] = array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
      $wh5["field"] = "id,job_grade_name";
      $data5 = urlencode(json_encode($wh5));
      $url5 = API."?mode=gettabledetailswithconditions&table=job_grade&data=".$data5; 
      $data_details5 = callAPI($url5);
      $joblevel_list  = json_decode($data_details5,1);
      $wh6["where"] = array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
      $wh6["field"] = "id,transaction_number";
      $data6 = urlencode(json_encode($wh6));
      $url6 = API."?mode=gettabledetailswithconditions&table=esgin_budget_approvals&data=".$data6; 
      $data_details6 = callAPI($url6);
      $esginbudget_list  = json_decode($data_details6,1);

   $wh7["where"] =array('is_active'=> 0,'company_id'=>$_SESSION['company_id']);
      $wh7["field"] = "id,process_name";
      $data7 = urlencode(json_encode($wh7));
      $url7 = API."?mode=gettabledetailswithconditions&table=job_selection_process&data=".$data7; 
      $data_details7 = callAPI($url7);
      $jobselection_process_list  = json_decode($data_details7,1);

      $rid = encodeId($_GET['Rid']);
      $rid = explode(":", $rid);
      $rid = $rid[1];
      $wh8["where"] = array('id'=> $rid);
      $data8 = urlencode(json_encode($wh8));
      $url8 = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$data8; 
      $data_details8 = callAPI($url8);
      $jobpostlist  = json_decode($data_details8,1);
      $jobpostlist =$jobpostlist[0];

//print_r($jobpostlist);

      //$rid = encodeId($_GET['Rid']);
      //$wh9["where"] = array('id'=> $rid);
      $wh9["field"] = "max(id) AS max_id";
      $data9 = urlencode(json_encode($wh9));
      $url9 = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$data9; 
      $data_details9 = callAPI($url9);
      $maxid  = json_decode($data_details9,1);
      $maxid =$maxid[0];
      $maxid = $maxid['max_id'];


       $wh10["where"] = array('transaction_id'=> $rid,'application_table_name'=> 'job_postings');
      //$wh9["field"] = "max(id) AS max_id";
      $data10 = urlencode(json_encode($wh10));
      $url10 = API."?mode=gettabledetailswithconditions&table=application_tags&data=".$data10; 
      $data_details10 = callAPI($url10);
      $tagslist  = json_decode($data_details10,1);
      $tagslist =$tagslist;
          
    // print_r($maxid);
   ?>
<style>
   iframe{
      width: 200%;
   }
</style>

<script>
   function selectoptions(tablename,filedname,value,name){
       if(value == "-0"){  
         $.post("views/addoption.php",{"tablename":tablename,"filedname":filedname,"value":value,"name":name},function(result){
         $('.optionvalue').html(result);
          $('#exampleModalSizeSm').modal('show');
         })
      }      
   }
</script>




   <div class="card d-flex flex-column-fluid flex-column flex-root  ">
         <div class="d-flex flex-row flex-column-fluid page ">
            <div class="container-fluid" id="kt_wrapper">
               <div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
			   
			   <?php
 if($error_message !=''){ ?>
		<div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>	
<?php } ?>
                  <div class="row post ">
                   
                     <div class="col-lg-8 col-md-12 col-sm-12 col-12">
                        <form method="post"> 
                           <input type="hidden" name="id" value="<?php echo $rid; ?>">
                           <input type="hidden" name="maxid" value="<?php echo $maxid+1; ?>">
                         
                              <div class="card-header ">
                                 <h5>Job Posting</h5>      

                                 <!--   <i  data-toggle="tooltip" onclick="introJs().start()" data-original-title="Help Text" id="kt_quick_cart_toggle" class="fa fa-info-circle"></i>

                                 <i  onclick="proceduressteps()" class="fa fa-question-circle"></i> -->

                              </div>

                              <div class="card-body">
                                 <div class="border p-3 mb-2">



                           <div class="col-12 ">
                            <div class="row mt-2">
                              <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10" data-step="1" data-intro="Hello world">
                                 <label class="form-label ">Job Title</label>
                                 <div>
                                    <select class="form-select js-example-basic-single col-sm-12 selectjobtitle p-2" onchange="selectJobTitle()"  name="job_title">
                                             <option value="-1">Select Job Title</option>
                                                                              <?php foreach($title_list as  $title_listrow){ ?>
                                                                                 <option value="<?php echo $title_listrow['id']; ?>"  <?php echo $jobpostlist['job_code']==$title_listrow['id']?"selected":""; ?> ><?php echo $title_listrow['job_title']; ?></option>
                                                                              <?php } ?>
                                    </select>



                                 </div>
                              </div>
                             <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10" data-step="2" data-intro="Job number">
                              <label class="form-label">Job Number</label>
                              <div class="">
                                 <input class="form-control p-2" type="text" value="<?php echo $jobpostlist['job_number']; ?>"  name="job_number" id="" readonly="readonly">
                              </div>
                           </div>
                           </div>
                      
                           <div class="row mt-2">
                              <div class="col-lg-12 m-b-10" data-step="3" data-intro="Post Name">
                                 <label class="form-label mt-1">Name</label>
                                 <div class="">
                                    <input class="form-control p-2" type="text" name="job_name" value="<?php echo $jobpostlist['job_name']; ?>" id="job_name">
                                 </div>
                              </div>
                             <div class="col-lg-12 m-b-10" data-step="4" data-intro="Location">
                              
                                 <label class="form-label mt-1">Location</label>
                              <div>
                                    <select class="form-select js-example-basic-single" id="kt_select2_1" name="location" onchange="selectoptions('locations','location_name',this.value,'location')">
                                             

                                             <option value="-1">Select location</option>
                                       <?php foreach($location_list as $location_listrow){ ?>
                                                <option value="<?php echo $location_listrow['id']; ?>"  <?php echo $jobpostlist['location_id']==$location_listrow['id']?"selected":""; ?>><?php echo $location_listrow['location_name']; ?></option>
                                       <?php } ?>
                                       <option value="-0">Other</option>

                                    </select>
                              </div>

                              <!-- <div class="input-group">
                                 <input type="text"  name="textlocation" class="form-control"  placeholder="Eg. option">
                                 <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" type="button" onclick="addOptions()">Add Option</button>
                                    <button type="button" class="btn btn-danger" type="button">Cancel</button>
                                 </div>
                              </div>  -->
                           </div>
                           </div>
                  


                            <div class="row mt-2">
                              <div class="col-lg-6 m-b-10" data-step="5" data-intro="Request By">
                              <label class="form-label mt-1">Requested By   <?php $request = isset($jobpostlist['requested_by'])?$jobpostlist['requested_by']:$_SESSION['userid']; ?>
    </label>
                             <div >

                         <select   class=" form-select js-example-basic-single" id="kt_select2_5" name="requested_by">
                                         <option value="-1">Select Requested</option>
           
<?php
            foreach($user_list as $user_listrow){ ?>
            <option value="<?php echo $user_listrow['id']; ?>"  <?php echo $user_listrow['id']==$request?"selected":""; ?>><?php echo $user_listrow['display_name']; ?></option>
            <?php } ?>

                                    </select>
                                 </div>
                           </div>
                             <div class="col-lg-6 m-b-10" data-step="6" data-intro="Requested Date">
                              
                              <label class=" form-label mt-1">Requested Date</label>
                              <div class="">
                                 <input class="form-control datepicker" type="text" value="<?php echo $jobpostlist['requested_date']!=""?$jobpostlist['requested_date']:date('Y-m-d'); ?>" name="requested_date" id="example-date-input" />
                              </div>
                           </div>
                           </div>
                                <div class="row mt-2">
                              <div class="col-lg-6 m-b-10" data-step="7" data-intro="Recruitment Manager">
                              <label class="form-label mt-1">Recruitment Manager <?php //print_r($user_list); ?></label>
                              <div >
                                    <select class=" form-select js-example-basic-single" id="kt_select2_8" name="recruitment_by">
                                           <option value="-1">Select Recruitment</option>
                          <?php foreach($user_list as $user_listrow){ ?>
            <option value="<?php echo $user_listrow['id']; ?>"  <?php echo $jobpostlist['recruitment_manager']==$user_listrow['id']?"selected":""; ?> ><?php echo $user_listrow['display_name']; ?></option>
            <?php } ?>

                                    </select>
                                 </div>
                           </div>
                             <div class="col-lg-6 m-b-10" data-step="8" data-intro="Recruitment Date">
                              
                              <label class=" form-label mt-1">Recruitment Date</label>
                              <div class="">
                                 <input class="form-control datepicker" type="text" value="<?php echo $jobpostlist['required_date']!=""?$jobpostlist['required_date']:date('Y-m-d'); ?>" name="recruitment_date" id="example-date-input" />
                              </div>
                           </div>
                           </div>

                           <div class="row mt-2">
                              <label class="col-8 form-label mt-1">Year of Experience</label>
                              <div class="col-12">
                                 <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
                                       <select class="form-select js-example-basic-single" id="" name="min_experience">
                                          <option value="0">Select Min Exp</option>
                                          <?php for($m=1;$m<=10;$m++){ ?>
                                          <option value="<?php echo $m; ?>" <?php echo number_format($jobpostlist['min_exp'])==$m?"selected":""; ?>><?php echo $m; ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
                                       <select class="form-select js-example-basic-single" id="" name="max_experience">
                                          <option value="1">Select Max Exp</option>
                                             <?php for($m=1;$m<=10;$m++){ ?>
                                          <option value="<?php echo $m; ?>" <?php echo number_format($jobpostlist['max_exp'])==$m?"selected":""; ?>><?php echo $m; ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                           </div>
                    <div class="row mt-2">
                      <label class="col-12 form-label mt-1">Job Responsibility</label>
                      <div class="col-12">
                        <textarea class="form-control" placeholder="" name="job_responsibility" style="height: 80px;"><?php echo $jobpostlist['job_responsibility'];?></textarea>
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
                               <!--   <button type="button" onclick="addvideoextrafileds()" class="btn btn-outline-success mt-2">
                                    <i class="fas fa-plus"></i> Priview Video
                                 </button> -->

                                <!--  <div class="accordion accordion-toggle-arrow mt-4" id="accordionExample4">
                                    <div class="card">
                                       <div class="card-header" id="headingOne4">
                                          <div class="card-title" data-toggle="collapse" data-target="#collapseOne4">
                                             <i class="far fa-lightbulb"></i> Ideas for videos that you can add 
                                          </div>
                                       </div>
                                       <div id="collapseOne4" class="collapse" data-parent="#accordionExample4">
                                          <div class="card-body video" style="width:50%">
                                          </div>
                                       </div>
                                    </div>
                                 </div> -->
                      </div>
                    </div>
                    <h3 class="text-dark font-weight-bold mt-5 mt-3 mr-5">Applications</h3>
                           <hr>
                       
                           <h4 class="mt-2">Screening Questions</h4>
                           <h6 class="font-weight-normal mb-4">You can ask some questions before the candidates apply to your job!</h6>
                           <div class="col-lg-8">  
                  <?php $ij=1; if(isset($jobpostlist['question'])){ $question = json_decode($jobpostlist['question'],1); foreach($question as $question){ ?>
                 <div class="<?php echo $ij; ?>"> 

                  <input class="form-control mt-2 col-lg-10" type="text" name="question[]" value="<?php echo $question; ?>" placeholder="Eg. Tell me about yourself"><i title="Delete Question" onclick="deleteQuestion(<?php echo $ij; ?>)" class="text-dark-50 flaticon-delete col-lg-2 mt-4"></i> 

                  </div>     <?php $ij++; }}else{ ?> 
                  <div class="rows1"> 
                  <input class="form-control mt-2 col-lg-10" type="text" name="question[]" value="" placeholder="Eg. Tell me about yourself"><i title="Delete Question" onclick="deleteQuestion(1)" class="text-dark-50 flaticon-delete col-lg-2 mt-4"></i> 
                    </div> 
<?php } ?>
                           <div id="videoquestiontextfileds" class="m-l-10 m-r-10">
                           </div>
                            <button type="button" onclick="addQuestionsTextFileds()" class="btn btn-outline-success mt-2">
                               <i class="fas fa-plus"></i> Add Question
                           </button>
                     </div>
                           <h4 class="text-dark font-weight-bold mt-5 mb-3 mr-5">Targeting</h4>
                           <hr>
                           <div class="row">
                              <div class="col-12">
                                 <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
                                       <label class="form-label mt-1">Department Department</label>
                                       <select class="form-select js-example-basic-single form-control" id="" onchange="selectoptions('departments','department_name',this.value,'department_name')" name="department_name">
                                          <option value="-1">Select</option>
                                         <?php foreach($department_list as $department_listrow){ ?>
                                          <option value="<?php echo $department_listrow['id']; ?>"  <?php echo $jobpostlist['department_id']==$department_listrow['id']?"selected":""; ?>><?php echo $department_listrow['department_name']; ?></option>
                                         <?php } ?>
                                         <option value="-0">Other</option>
                                       </select>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
                                       <label class="form-label mt-1">Job Level</label>
                                       <select class="form-select js-example-basic-single form-control" id="" name="job_level">
                                          <option value="-1">Select job Level</option>
                                          <?php foreach($joblevel_list as $joblevel_list){ ?>
                                             
                                          <option value="<?php echo $joblevel_list['id']; ?>" <?php echo $jobpostlist['job_level_id']==$joblevel_list['id']?"selected":""; ?>><?php echo $joblevel_list['job_grade_name']; ?></option>   
                                          <?php   }?>

                                       </select>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-12">
                                 <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
                                       <label class="form-label mt-1">Job Selection Process</label>
                                       <select class="form-select js-example-basic-single form-control" id="" name="job_selection_process">
                                          <option value="">Select Job Selection process</option>
                                          <?php foreach($jobselection_process_list as $jobselection_process_listroe){ ?>
                                          <option value="<?php echo $jobselection_process_listroe['id']; ?>" <?php echo $jobpostlist['job_selection_process']==$jobselection_process_listroe['id']?"selected":""; ?>><?php echo $jobselection_process_listroe['process_name']; ?></option>
         
                                             <?php } ?>
                                       </select>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-12 m-b-10">
                                       <label class="form-label mt-1">Number Of Employees</label>
                                       <div class="">
                                          <input class="form-control" type="text" value="<?php echo $jobpostlist['no_of_resources']; ?>" name="number_of_employee" id="number_of_employee" placeholder="Eg: 4" />
                                       </div>
                                    </div>  
                                 </div>
                              </div>
                           </div>
                           <div class="row m-b-10">
                              <div class="col-12">
                                  <label class="form-label mt-1">Annual Salary(Rs.)</label>
                                 <div class="row">
                                       <div class="col-lg-4 m-b-10">
                                        <label class="form-label">Min Salary</label>
                                       <input class="form-control autonumber" type="text" value="<?php echo $jobpostlist['min_salary']; ?>" name="min_salary" data-a-sign="Rs. " placeholder="eg. 4,000,000"  />
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
                                    <!--    <input class="form-control inr_amount" type="text" value="<?php echo $jobpostlist['max_salary']; ?>" onblur="maxamountemplye(this.value)" name="max_salary" placeholder="eg. 4,000,000" />
                                     -->

                                       <input type="text" class="form-control autonumber"  value="<?php echo $jobpostlist['max_salary']; ?>" onblur="maxamountemplye(this.value)" name="max_salary" placeholder="eg. 4,000,000" data-a-sign="Rs. ">

                                    </div>
                                     <div class="col-lg-4 m-b-10">
                                        <label class="form-label">Total Salary</label>
                             <!--           <input class="form-control inr_amount" type="text" value="<?php echo $jobpostlist['total_resource_cost']; ?>"  name="annual_salary"  id="annual_salary" > -->

<input type="text" class="form-control autonumber inr_amount"  value="<?php echo $jobpostlist['total_resource_cost']; ?>"  name="annual_salary"  id="annual_salary" data-a-sign="Rs. " >



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
                                    <div class="radio radio-outline ">
                                 
                                    <label class="radio radio-inline m-b-10"> <input type="radio" <?php echo $jobpostlist['employment_type']=='2'?"checked":"";?> name="employment_type" value="2"> <span></span>Contract</label>
                                     
                                    <label class="radio radio-inline m-b-10"> <input type="radio" name="employment_type" <?php echo $jobpostlist['employment_type']=='1'?"checked":"";?> value="1">   <span></span>Regular</label>
                                             </div>
                                    </div>
</div></div></div>
               <div class="col-12 mt-3">
                                 <div class="checkbox-inline">
                                    <label class="checkbox checkbox-outline checkbox-success">
                                       <input type="checkbox" name="candidate_show_status" <?php echo $jobpostlist['candidate_show_status']=="0"?"checked":"";?>  />
                                       <span></span>
                                       <b>Don't show this to candidates</b>
                                       <p></p>
                                    </label>
                                 </div>
                              </div>
                           <div class="row">
                           
                              <div class="col-12">
                                 <h6 class="mt-5">Employee Mode</h6>
                                 <input type="hidden" name="employee_mode" id="employee_mode" value="<?php echo $jobpostlist['employment_mode']; ?>">
                                 <ul class="post-job">
                                    <li>
                                       <a class="btn <?php echo $jobpostlist['employment_mode']=="1"?"btn-info":"btn-outline-info"; ?> coursetype"  data-id="1">
                                          <i class="far fa-user-circle mb-4"></i>
                                          <h6>Full Time</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn <?php echo $jobpostlist['employment_mode']=="2"?"btn-info":"btn-outline-info"; ?> coursetype" data-id="2">
                                          <i class="fas fa-chart-pie mb-4"></i>
                                          <h6>Part Time</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn <?php echo $jobpostlist['employment_mode']=="3"?"btn-info":"btn-outline-info"; ?> coursetype" data-id="3">
                                          <i class="far fa-user-circle mb-4"></i>
                                          <h6>Distance Learning Program</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn <?php echo $jobpostlist['employment_mode']=="4"?"btn-info":"btn-outline-info"; ?> coursetype" data-id="4">
                                          <i class="far fa-user-circle mb-4"></i>
                                          <h6>Executive Program</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn <?php echo $jobpostlist['employment_mode']=="5"?"btn-info":"btn-outline-info"; ?> coursetype" data-id="5">
                                          <i class="far fa-user-circle mb-4"></i>
                                          <h6>Certification</h6>
                                       </a>
                                    </li>
                                 </ul>
                              </div>
                            <!--   <div class="col-12">
                                 <h6 class="mt-5">Increase your reach to diversity candidates</h6>
                                 <ul class="post-job post-job2">
                                    <li>
                                       <a class="btn btn-outline-primary">
                                          <i class="far fa-user-circle mb-4"></i>
                                          <h6>Female Candidates</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn btn-outline-primary">
                                          <i class="fas fa-chart-pie mb-4"></i>
                                          <h6>Women joining back the workforce</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn btn-outline-primary">
                                          <i class="fas fa-user-graduate mb-4"></i>
                                          <h6>EX-defence personal</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn btn-outline-primary">
                                          <i class="far fa-user-circle mb-4"></i>
                                          <h6>Differently-added candidates</h6>
                                       </a>
                                    </li>
                                    <li>
                                       <a class="btn btn-outline-primary">
                                          <i class="far fa-user-circle mb-4"></i>
                                          <h6>Work Form Home</h6>
                                       </a>
                                    </li>
                                 </ul>
                              </div> -->

<div class="col-12">
   <h3 class="text-dark font-weight-bold mt-5 mb-2 mr-5">Job Tags</h3>
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
 


                              <div class="col-12 mt-5">
                                 <div class="py-2 px-4 boost">
                                    <h5><i class="far fa-user-circle mb-4 mr-3"></i>Job Description</h5>
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
                                     <button class="btn btn-primary m-b-10" name="post_job" value="exit">Post Job</button>
                                    <button class="btn btn-out waves-effect waves-light btn-primary btn-square m-b-10" name="post_job" value="post">Post Job & exit</button>
                                   <!--  <button class="btn btn-link" >Cancel</button> -->
                                 </div>
                              </div>
                           </div>
                        </div>
                    </div>
                        </form>
                  </div>




                     <div class="col-lg-4 col-md-12 col-sm-12 col-12">
                        <div class="few mb-3">
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













            <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 384px; right: -2px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 231px;"></div></div></div>
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


