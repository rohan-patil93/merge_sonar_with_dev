<style>

.start{
    background: #ccc;
    border:1px solid #333;
    cursor:pointer;
    padding:10px;
}
.start:hover{
    background: #999;
    color:#fff;
}

/********* INTRO CSS******/
.introjs-overlay {
  position: absolute;
  z-index: 999999;
  background-color: #000;
  opacity: 0;
  background: -moz-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);
  background: -webkit-gradient(radial,center center,0px,center center,100%,color-stop(0%,rgba(0,0,0,0.4)),color-stop(100%,rgba(0,0,0,0.9)));
  background: -webkit-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);
  background: -o-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);
  background: -ms-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);
  background: radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#66000000',endColorstr='#e6000000',GradientType=1);
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
  filter: alpha(opacity=50);
  -webkit-transition: all 0.3s ease-out;
     -moz-transition: all 0.3s ease-out;
      -ms-transition: all 0.3s ease-out;
       -o-transition: all 0.3s ease-out;
          transition: all 0.3s ease-out;
}

.introjs-fixParent {
  z-index: auto !important;
}

.introjs-showElement {
  z-index: 9999999 !important;
}

.introjs-relativePosition {
  position: relative;
}

.introjs-helperLayer {
  position: absolute;
  z-index: 9999998;
  background-color: #FFF;
  background-color: rgba(255,255,255,.9);
  border: 1px solid #777;
  border: 1px solid rgba(0,0,0,.5);
  border-radius: 4px;
  box-shadow: 0 2px 15px rgba(0,0,0,.4);
  -webkit-transition: all 0.3s ease-out;
     -moz-transition: all 0.3s ease-out;
      -ms-transition: all 0.3s ease-out;
       -o-transition: all 0.3s ease-out;
          transition: all 0.3s ease-out;
}

.introjs-helperNumberLayer {
  position: absolute;
  top: -16px;
  left: -16px;
  z-index: 9999999999 !important;
  padding: 2px;
  font-family: Arial, verdana, tahoma;
  font-size: 13px;
  font-weight: bold;
  color: white;
  text-align: center;
  text-shadow: 1px 1px 1px rgba(0,0,0,.3);
  background: #ff3019; /* Old browsers */
  background: -webkit-linear-gradient(top, #ff3019 0%, #cf0404 100%); /* Chrome10+,Safari5.1+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ff3019), color-stop(100%, #cf0404)); /* Chrome,Safari4+ */
  background:    -moz-linear-gradient(top, #ff3019 0%, #cf0404 100%); /* FF3.6+ */
  background:     -ms-linear-gradient(top, #ff3019 0%, #cf0404 100%); /* IE10+ */
  background:      -o-linear-gradient(top, #ff3019 0%, #cf0404 100%); /* Opera 11.10+ */
  background:         linear-gradient(to bottom, #ff3019 0%, #cf0404 100%);  /* W3C */
  width: 20px;
  height:20px;
  line-height: 20px;
  border: 3px solid white;
  border-radius: 50%;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff3019', endColorstr='#cf0404', GradientType=0); /* IE6-9 */
  filter: progid:DXImageTransform.Microsoft.Shadow(direction=135, strength=2, color=ff0000); /* IE10 text shadows */
  box-shadow: 0 2px 5px rgba(0,0,0,.4);
}

.introjs-arrow {
  border: 5px solid white;
  content:'';
  position: absolute;
}
.introjs-arrow.top {
  top: -10px;
  border-top-color:transparent;
  border-right-color:transparent;
  border-bottom-color:white;
  border-left-color:transparent;
}
.introjs-arrow.right {
  right: -10px;
  top: 10px;
  border-top-color:transparent;
  border-right-color:transparent;
  border-bottom-color:transparent;
  border-left-color:white;
}
.introjs-arrow.bottom {
  bottom: -10px;
  border-top-color:white;
  border-right-color:transparent;
  border-bottom-color:transparent;
  border-left-color:transparent;
}
.introjs-arrow.left {
  left: -10px;
  top: 10px;
  border-top-color:transparent;
  border-right-color:white;
  border-bottom-color:transparent;
  border-left-color:transparent;
}

.introjs-tooltip {
  position: absolute;
  padding: 10px;
  background-color: white;
  min-width: 200px;
  max-width: 300px;
  border-radius: 3px;
  box-shadow: 0 1px 10px rgba(0,0,0,.4);
  -webkit-transition: opacity 0.1s ease-out;
     -moz-transition: opacity 0.1s ease-out;
      -ms-transition: opacity 0.1s ease-out;
       -o-transition: opacity 0.1s ease-out;
          transition: opacity 0.1s ease-out;
}

.introjs-tooltipbuttons {
  text-align: right;
}

.introjs-button {
  position: relative;
  overflow: visible;
  display: inline-block;
  padding: 0.3em 0.8em;
  border: 1px solid #d4d4d4;
  margin: 0;
  text-decoration: none;
  text-shadow: 1px 1px 0 #fff;
  font: 11px/normal sans-serif;
  color: #333;
  white-space: nowrap;
  cursor: pointer;
  outline: none;
  background-color: #ececec;
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f4f4f4), to(#ececec));
  background-image: -moz-linear-gradient(#f4f4f4, #ececec);
  background-image: -o-linear-gradient(#f4f4f4, #ececec);
  background-image: linear-gradient(#f4f4f4, #ececec);
  -webkit-background-clip: padding;
  -moz-background-clip: padding;
  -o-background-clip: padding-box;
   /* commented out due to Opera 11.10 bug */
  -webkit-border-radius: 0.2em;
  -moz-border-radius: 0.2em;
  border-radius: 0.2em;
  /* IE hacks */
  zoom: 1;
  *display: inline;
  margin-top: 10px;
}

.introjs-button:hover {
  border-color: #bcbcbc;
  text-decoration: none;
  box-shadow: 0px 1px 1px #e3e3e3;
}

.introjs-button:focus,
.introjs-button:active {
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ececec), to(#f4f4f4));
  background-image: -moz-linear-gradient(#ececec, #f4f4f4);
  background-image: -o-linear-gradient(#ececec, #f4f4f4);
  background-image: linear-gradient(#ececec, #f4f4f4);
}

/* overrides extra padding on button elements in Firefox */
.introjs-button::-moz-focus-inner {
  padding: 0;
  border: 0;
}

.introjs-skipbutton {
  margin-right: 5px;
  color: #7a7a7a;
}

.introjs-prevbutton {
  -webkit-border-radius: 0.2em 0 0 0.2em;
  -moz-border-radius: 0.2em 0 0 0.2em;
  border-radius: 0.2em 0 0 0.2em;
  border-right: none;
}

.introjs-nextbutton {
  -webkit-border-radius: 0 0.2em 0.2em 0;
  -moz-border-radius: 0 0.2em 0.2em 0;
  border-radius: 0 0.2em 0.2em 0;
}

.introjs-disabled, .introjs-disabled:hover, .introjs-disabled:focus {
  color: #9a9a9a;
  border-color: #d4d4d4;
  box-shadow: none;
  cursor: default;
  background-color: #f4f4f4;
  background-image: none;
  text-decoration: none;
}
</style>

<?php

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
      $response = file_post_contents($url, $_POST);
      $return = json_decode($response,1);
       if($_POST['post_job']=="exit"){
            header('location:index.php?option=COM_LIST_JOB_POSTINGS');
       }else{
            echo '<input type="hidden" id="message" value="'.$_POST['post_job'].'" data-id="'.$return['LASTID'].'">';

       }
 }
$jobnumber = toGetApplicationSerialNumber('job_postings');
   $wh1["where"] = array('is_active'=> 0,'company_id'=>1);
      $wh1["field"] = "id,job_title,job_description";
      $data1 = urlencode(json_encode($wh1));
      $url1 = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$data1;
      $data_details1 = callAPI($url1);
      $title_list  = json_decode($data_details1,1);
      $wh2["where"] = array('is_active'=> 0,'company_id'=>1);
      $wh2["field"] = "id,location_name";
      $data2 = urlencode(json_encode($wh2));
      $url2 = API."?mode=gettabledetailswithconditions&table=locations&data=".$data2;
      $data_details2 = callAPI($url2);
      $location_list  = json_decode($data_details2,1);
      $wh3["where"] = array('is_active'=> 0,'company_id'=>1);
      $wh3["field"] = "id,display_name";
      $data3 = urlencode(json_encode($wh3));
      $url3 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data3;
      $data_details3 = callAPI($url3);
      $user_list  = json_decode($data_details3,1);
      $wh4["where"] = array('is_active'=> 0,'company_id'=>1);
      $wh4["field"] = "id,department_name";
      $data4 = urlencode(json_encode($wh4));
      $url4 = API."?mode=gettabledetailswithconditions&table=departments&data=".$data4;
      $data_details4 = callAPI($url4);
      $department_list  = json_decode($data_details4,1);
      $wh5["where"] = array('is_active'=> 0,'company_id'=>1);
      $wh5["field"] = "id,job_grade_name";
      $data5 = urlencode(json_encode($wh5));
      $url5 = API."?mode=gettabledetailswithconditions&table=job_grade&data=".$data5;
      $data_details5 = callAPI($url5);
      $joblevel_list  = json_decode($data_details5,1);
      $wh6["where"] = array('is_active'=> 0,'company_id'=>1);
      $wh6["field"] = "id,transaction_number";
      $data6 = urlencode(json_encode($wh6));
      $url6 = API."?mode=gettabledetailswithconditions&table=esgin_budget_approvals&data=".$data6;
      $data_details6 = callAPI($url6);
      $esginbudget_list  = json_decode($data_details6,1);

   $wh7["where"] = array('is_active'=> 0,'company_id'=>1);
      $wh7["field"] = "id,process_name";
      $data7 = urlencode(json_encode($wh7));
      $url7 = API."?mode=gettabledetailswithconditions&table=job_selection_process&data=".$data7;
      $data_details7 = callAPI($url7);
      $jobselection_process_list  = json_decode($data_details7,1);

      $rid = encodeId($_GET['Rid']);
      $wh8["where"] = array('id'=> $rid);
      $data8 = urlencode(json_encode($wh8));
      $url8 = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$data8;
      $data_details8 = callAPI($url8);
      $jobpostlist  = json_decode($data_details8,1);
      $jobpostlist =$jobpostlist[0];

      $wh9["field"] = "max(id) AS max_id";
      $data9 = urlencode(json_encode($wh9));
      $url9 = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$data9;
      $data_details9 = callAPI($url9);
      $maxid  = json_decode($data_details9,1);
      $maxid =$maxid[0];
      $maxid = $maxid['max_id'];

       $wh10["where"] = array('transaction_id'=> $rid,'application_table_name'=> 'job_postings');
      $data10 = urlencode(json_encode($wh10));
      $url10 = API."?mode=gettabledetailswithconditions&table=application_tags&data=".$data10;
      $data_details10 = callAPI($url10);
      $tagslist  = json_decode($data_details10,1);

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

   <div class=" d-flex flex-column-fluid flex-column flex-root">
         <div class="d-flex flex-row flex-column-fluid page">
            <div class="container-fluid" id="kt_wrapper">
               <div class="content  d-flex flex-column flex-column-fluid card card-custom" id="kt_content">
                  <div class="row post">

                       <div class="col-md-8">
                         <form method="post">
                           <input type="hidden" name="id" value="<?php echo $rid; ?>">
                           <input type="hidden" name="maxid" value="<?php echo $maxid+1; ?>">

<div class="card-header ">
                                                <h5>Job Posting</h5>

                               <button type="button" data-toggle="tooltip" onclick="introJs().start()" data-original-title="Help Text" id="kt_quick_cart_toggle" class="btn p-0 border-0 bg-transparent fa fa-info-circle" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>

                               <button type="button" onclick="proceduressteps()" class="btn p-0 border-0 bg-transparent fa fa-question-circle" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>

                                                <hr>
                                            </div>

                           <div class="card-body">

                           <div class="col-8">
                            <div class="row mt-2">
                              <div class="col-lg-6" data-step="1" data-intro="Hello world">
                                 <span class="form-label ">Job Title</span>
                                 <div>

 <select aria-label="Job Title" class="js-example-data-array col-sm-12 selectjobtitle" onchange="selectJobTitle()"  name="job_title">
         <option value="-1">Select Job Title</option>
                                          <?php foreach($title_list as  $title_listrow){ ?>
                                             <option value="<?php echo $title_listrow['id']; ?>"  <?php echo $jobpostlist['job_code']==$title_listrow['id']?"selected":""; ?> ><?php echo $title_listrow['job_title']; ?></option>
                                          <?php } ?>
</select>

                                 </div>
                              </div>
                             <div class="col-lg-6" data-step="2" data-intro="Job number">
                              <span class="form-label mt-1">Job Number</span>
                              <div class="">
                                 <input aria-label="Input field" class="form-control" type="text" value="<?php echo $jobpostlist['job_number']!=""?$jobpostlist['job_number']:$jobnumber; ?>"  name="job_number" id="" readonly="readonly">
                              </div>
                           </div>
                           </div>

                           <div class="row mt-2">
                              <div class="col-lg-12" data-step="3" data-intro="Post Name">
                              <span class="form-label mt-1">Name</span>
                              <div class="">
                                 <input aria-label="Job Name" class="form-control" type="text" name="job_name" value="<?php echo $jobpostlist['job_name']; ?>" id="job_name">
                              </div>
                           </div>
                             <div class="col-lg-12" data-step="4" data-intro="Location">

                              <span class="form-label mt-1">Location</span>
                            <div >
                                    <select aria-label="Location" class="dcdfvdffgbfgb form-control kt-select2" id="kt_select2_1" name="location" onchange="selectoptions('locations','location_name',this.value,'location')">


                                             <option value="-1">Select location</option>
<?php foreach($location_list as $location_listrow){ ?>
         <option value="<?php echo $location_listrow['id']; ?>"  <?php echo $jobpostlist['location_id']==$location_listrow['id']?"selected":""; ?>><?php echo $location_listrow['location_name']; ?></option>
<?php } ?>
<option value="-0">Other</option>

</select>


                                 </div>

                           </div>
                           </div>


                            <div class="row mt-2">
                              <div class="col-lg-6" data-step="5" data-intro="Request By">
                              <span class="form-label mt-1">Requested By   <?php $request = isset($jobpostlist['requested_by'])?$jobpostlist['requested_by']:$_SESSION['userid']; ?>
    </span>
                             <div >

                         <select aria-label="Requested By"   class=" form-control kt-select2" id="kt_select2_5" name="requested_by">
                                         <option value="-1">Select Requested</option>

<?php
            foreach($user_list as $user_listrow){ ?>
            <option value="<?php echo $user_listrow['id']; ?>"  <?php echo $user_listrow['id']==$request?"selected":""; ?>><?php echo $user_listrow['display_name']; ?></option>
            <?php } ?>

                                    </select>
                                 </div>
                           </div>
                             <div class="col-lg-6" data-step="6" data-intro="Requested Date">

                              <span class=" form-label mt-1">Requested Date</span>
                              <div class="">
                                 <input aria-label="Select date" class="form-control" type="date" value="<?php echo $jobpostlist['requested_date']!=""?$jobpostlist['requested_date']:date('Y-m-d'); ?>" name="requested_date" id="example-date-input-1" />
                              </div>
                           </div>
                           </div>
                                <div class="row mt-2">
                              <div class="col-lg-6" data-step="7" data-intro="Recruitment Manager">
                              <span class="form-label mt-1">Recruitment Manager </span>
                              <div >
                                    <select aria-label="Recruitment By" class=" form-control kt-select2" id="kt_select2_8" name="recruitment_by">
                                           <option value="-1">Select Recruitment</option>
                          <?php foreach($user_list as $user_listrow){ ?>
            <option value="<?php echo $user_listrow['id']; ?>"  <?php echo $jobpostlist['recruitment_manager']==$user_listrow['id']?"selected":""; ?> ><?php echo $user_listrow['display_name']; ?></option>
            <?php } ?>

                                    </select>
                                 </div>
                           </div>
                             <div class="col-lg-6" data-step="8" data-intro="Recruitment Date">

                              <span class=" form-label mt-1">Recruitment Date</span>
                              <div class="">
                                 <input aria-label="Select date" class="form-control" type="date" value="<?php echo $jobpostlist['required_date']!=""?$jobpostlist['required_date']:date('Y-m-d'); ?>" name="recruitment_date" id="example-date-input-to" />
                              </div>
                           </div>
                           </div>

                           <div class="row mt-2">
                              <span class="col-8 form-label mt-1">Year of Experience</span>
                              <div class="col-12">
                                 <div class="row">
                                    <div class="col-6">
                                       <select aria-label="Min Experience" class="form-control kt-select2" id="" name="min_experience">
                                          <option value="0">Select Min Exp</option>
                                          <?php for($m=1;$m<=10;$m++){ ?>
                                          <option value="<?php echo $m; ?>" <?php echo number_format($jobpostlist['min_exp'])==$m?"selected":""; ?>><?php echo $m; ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                    <div class="col-6">
                                       <select aria-label="Max Experience" class="form-control" id="" name="max_experience">
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
                                      <span class="col-8 form-label mt-1">Job Responsibility</span>
                                      <div class="col-12">
                                          <textarea aria-label="Job Responsibility" class="form-control" placeholder="" name="job_responsibility" style="height: 80px;"><?php echo $jobpostlist['job_responsibility'];?></textarea>
                                      </div>
                                  </div>
                          </div>
                                  <div class="row mt-2">
                                      <span class="col-8 form-label mt-1">Showcase Your Brand</span>
                              <span class="col-8 mb-4">Add a video to tell your brand's story and show candidates what it is like to work at your company</span>
                                      <div class="col-8">

                                 <input aria-label="Video Url" class="form-control" type="text" name="video_url"   id="video_url" value="<?php echo $jobpostlist['video_url']; ?>" placeholder="Eg. https://www.youtube.com/">
                                 <div id="videoextrafileds" >
                                 </div>
                                 <button type="button" onclick="addvideoextrafileds()" class="btn btn-outline-success mt-2">
                                    <i class="fas fa-plus"></i> Priview Video
                                 </button>

                                 <div class="accordion accordion-toggle-arrow mt-4" id="accordionExample4">
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
                                 </div>
                                      </div>
                                  </div>
                                  <h2 class="text-dark font-weight-bold mt-10 mb-2 mr-5">Applications</h2>
                           <hr>

                           <h4 class="mt-5">Screening Questions</h4>
                           <h6 class="font-weight-normal mb-4">You can ask some questions before the candidates apply to your job!</h6>
                           <div class="col-lg-8">
                  <?php $ij=1; if(isset($jobpostlist['question'])){ $question = json_decode($jobpostlist['question'],1); foreach($question as $question){ ?>
                 <div class="row rows<?php echo $ij; ?>">

                  <input aria-label="Question[]" class="form-control mt-2 col-lg-10" type="text" name="question[]" value="<?php echo $question; ?>" placeholder="Eg. Tell me about yourself"><i title="Delete Question" onclick="deleteQuestion(<?php echo $ij; ?>>)" class="text-dark-50 flaticon-delete col-lg-2 mt-4" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                    </div>     <?php $ij++;
}
}else{ ?>
 <div class="row rows1">
                  <input aria-label="Eg. Tell me about yourself" class="form-control mt-2 col-lg-10" type="text" name="question[]" value="" placeholder="Eg. Tell me about yourself" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><button type="button" title="Delete Question" onclick="deleteQuestion(1)" class="btn p-0 border-0 bg-transparent text-dark-50 flaticon-delete col-lg-2 mt-4" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                    </div>
<?php } ?>
                           <div id="videoquestiontextfileds" >
                           </div>
                            <button type="button" onclick="addQuestionsTextFileds()" class="btn btn-outline-success mt-2">
                               <i class="fas fa-plus"></i> Add Question
                           </button>
                     </div>
                           <h4 class="text-dark font-weight-bold mt-10 mb-2 mr-5">Targeting</h4>
                           <hr>
                           <div class="row">
                              <div class="col-8">
                                 <div class="row">
                                    <div class="col-6">
                                       <span class="form-label mt-1">Department Department</span>
                                       <select aria-label="Department Name" class="form-control" id="" onchange="selectoptions('departments','department_name',this.value,'department_name')" name="department_name">
                                          <option value="-1">Select</option>
                                         <?php foreach($department_list as $department_listrow){ ?>
   <option value="<?php echo $department_listrow['id']; ?>"  <?php echo $jobpostlist['department_id']==$department_listrow['id']?"selected":""; ?>><?php echo $department_listrow['department_name']; ?></option>
                                         <?php } ?>
                                         <option value="-0">Other</option>
                                       </select>
                                    </div>
                                    <div class="col-6">
                                       <span class="form-label mt-1">Job Level</span>
                                       <select aria-label="Job Level" class="form-control" id="" name="job_level">
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
                              <div class="col-8">
                                 <div class="row">
                                    <div class="col-6">
                                       <span class="form-label mt-1">Job Selection Process</span>
                                     <select aria-label="Job Selection Process" class="form-control" id="" name="job_selection_process">
                                          <option value="">Select Job Selection process</option>
                                          <?php foreach($jobselection_process_list as $jobselection_process_listroe){ ?>
<option value="<?php echo $jobselection_process_listroe['id']; ?>" <?php echo $jobpostlist['job_selection_process']==$jobselection_process_listroe['id']?"selected":""; ?>><?php echo $jobselection_process_listroe['process_name']; ?></option>

            <?php } ?>
                                       </select>
                                    </div>
                                    <div class="col-6">
                                    <span class="form-label mt-1">Number Of Employees</span>
                                    <div class="">
                                       <input aria-label="Input field" class="form-control" type="text" value="<?php echo $jobpostlist['no_of_resources']; ?>" name="number_of_employee" id="number_of_employee" placeholder="Eg: 4" />
                                    </div>
                                    </div>


                                 </div>
                              </div>
                           </div>
 <div class="row">
                              <div class="col-8">
                                  <span class="form-label mt-1">Annual Salary(Rs.)</span>
                                 <div class="row">
                                       <div class="col-lg-4">
                                        <span >Min Salary</span>
                                       <input aria-label="Input field" class="form-control inr_amount" type="text" value="<?php echo $jobpostlist['min_salary']; ?>" name="min_salary" placeholder="eg. 4,000,000"  />
                                    </div>

                                    <div class="col-lg-4">
                                        <span >Max Salary</span>
                                       <input aria-label="Input field" class="form-control inr_amount" type="text" value="<?php echo $jobpostlist['max_salary']; ?>" onblur="maxamountemplye(this.value)" name="max_salary" placeholder="eg. 4,000,000" />
                                    </div>
                                     <div class="col-lg-4">
                                        <span >Total Salary</span>
                                       <input aria-label="Input field" class="form-control inr_amount" type="text" value="<?php echo $jobpostlist['total_resource_cost']; ?>"  name="annual_salary"  id="annual_salary" >
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
<div class="col-lg-8">
   <div class="row">
       <div class="col-6">
            <span class="form-label mt-1"> Esgin Budget</span>
            <select aria-label="Esgin Budget" class="form-control" id="" name="esgin_budget">
               <option value="">Select Esgin Budget</option>
               <?php foreach($esginbudget_list as $esginbudget_list){ ?>
<option value="<?php echo $esginbudget_list['id']; ?>"  <?php echo $jobpostlist['esgin_budget_approval_id']==$esginbudget_list['id']?"selected":""; ?>><?php echo $esginbudget_list['transaction_number']; ?></option>

<?php } ?>
            </select>
         </div>
    <div class="col-6">
                                       <span class="form-label mt-1">Employment Type</span>
                                    <div class="radio-inline">

                                    <label class="radio"> <input aria-label="Radio option" type="radio" <?php echo $jobpostlist['employment_type']=='2'?"checked":"";?> name="employment_type" value="2"> <span></span>Contract</label>

                                    <label class="radio"> <input aria-label="Employment Type" type="radio" name="employment_type" <?php echo $jobpostlist['employment_type']=='1'?"checked":"";?> value="1">   <span></span>Regular</label>
                                             </div>
                                    </div>
</div></div></div>
               <div class="col-12 mt-3">
                                 <div class="checkbox-inline">
                                    <label class="checkbox checkbox-outline checkbox-success">
                                       <input aria-label="Candidate Show Status" type="checkbox" name="candidate_show_status" <?php echo $jobpostlist['candidate_show_status']=="0"?"checked":"";?>  />
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
                                          <i class="fas fa-user-graduate mb-4"></i>
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


<div class="col-12">
   <h2 class="text-dark font-weight-bold mt-10 mb-2 mr-5">Job Tags</h2>
   <hr>
</div>
                              <div class="col-12">

      <div class="">
         <select aria-label="Job Tag[]" class="form-control select2" id="kt_select2_11" multiple="multiple" name="job_tag[]">
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
                                       <textarea aria-label="Summa" class="summernote" name="summa" id="kt_summernote_1"><?php echo base64_decode($jobpostlist['job_desc']); ?></textarea>

                                    </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="mt-5">
                                     <button class="btn btn-primary" name="post_job" value="exit">Post Job</button>
                                    <button class="btn btn-outline-primary" name="post_job" value="post">Post Job and add another job</button>
                                    <button class="btn btn-link">Cancel</button>
                                 </div>
                              </div>
                           </div>
                           </div>
                        </form>
                       </div>

                     <div class="col-4">
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

