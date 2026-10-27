<?php

if($_POST['ajax'] == "Y"){
    include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $candidate_id = $_POST['candidate_id'];
    $_GET['option'] = $_POST['option'];
    $url = API."?mode=getcandidatedetails&Rid=".$candidate_id."&option=".$_POST['option'];
    $response = callAPI($url);
    $result = json_decode($response,1);
    $candidates = $result['candidates'];
    $blood_group = $result['blood_group'];
    foreach((array)$blood_group as $blood_group){
      $bloodgroup[$blood_group['id']] = $blood_group['blood_group'];
    }
    $vendors = $result['vendors'];
    foreach((array)$vendors as $vendor){
      $vendorlist[$vendor['id']] = $vendor['display_name'];
    }
    $locations = $result['locations'];
    foreach((array)$locations as $location){
      $locationlist[$location['id']] = $location['location_name'];
    }

    $job_grades = $result['job_grade'];
    foreach((array)$job_grades as $job_grade){
      $jobgradelist[$job_grade['id']] = $job_grade['job_grade_name'];
    }
    $job_grade_templates = $result['job_grade_templates'];
    foreach((array)$job_grade_templates as $job_grade_templatesre){
      $jobgradetemplatelist[$job_grade_templatesre['id']] = $job_grade_templatesre['name'];
    }
    $relations = $result['relations'];
    foreach((array)$relations as $relation){
      $relationlist[$relation['id']] = $relation['relation_name'];
    }
    $educationtype = $result['educationtype'];
    foreach((array)$educationtype as $education){
      $edutypedata[$education['genc_detail_code']] = $education['genc_detail_name'];
    }
}
$display_file = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_id."/Documents";

if($_POST['edittype'] == "dependancy"){
    $depen= json_decode($candidates['employee_dependents'],1);
    if($_POST['editsubmit'] == "edit"){
?>
<form id="dependancyform">
    <input type="hidden" name="edittype" value="dependancy">
<div class="row">
    <div class="form-group col-lg-12 dt-responsive table-responsive">
      <table class="table table-framed" id="deptable">
       <tr>
        <th scope="col" class="" style="width: 25%;"> Name</th>

        <th scope="col" class="thheader" style="width: 20%;">Relationship</th>
        <th scope="col" class="thheader" style="width: 25%;">DOB</th>
        <th scope="col" class="" style="width: 20%;"> Occupation</th>
        <th scope="col" class="" style="width: 20%;"> Monthly Income</th>
        <th scope="col" style="width: 2%;">--</th>
      </tr>
      <?php
      if(count((array)$depen) == '0'){
          $depen[] = array('dependent_relationship_type'=>'','dependent_name'=>'','dependent_relationship_type'=>'','dependent_date_of_birth'=>'','income'=>'');
      }
        foreach ((array)$depen as $depe) { ?>
         <tr>
          <td><input aria-label="Dependent Name[]" type="text" name="dependent_name[]" class="form-control " value="<?php echo $depe['dependent_name']; ?>" autocomplete="off">  </td>
          <td>
           <select aria-label="Dependent Relationship Type[]" class="form-select js-example-basic-single" name="dependent_relationship_type[]" >
            <option value="0"> Select Any One</option>
            <?php foreach ((array)$relationlist as $relationlistkey => $reladata) {   ?>
              <option   <?php if($depe['dependent_relationship_type']== $relationlistkey){  ?> selected <?php
} ?> value="<?php echo $relationlistkey; ?>"> <?php echo $reladata; ?></option>
            <?php   } ?>
          </select>
        </td>
        <td>
            <input aria-label="Dependent Date Of Birth[]" type="text" name="dependent_date_of_birth[]" class="form-control datepicker" value="<?php echo $depe['dependent_date_of_birth']; ?>" autocomplete="off">  </td>
            <td><input aria-label="Occupation[]" type="text" name="occupation[]" class="form-control " value="<?php echo $depe['occupation']; ?>" autocomplete="off">  </td>

              <td><input aria-label="Income[]" type="text" name="income[]" class="form-control " value="<?php echo $depe['income']; ?>" autocomplete="off">  </td>

            <td><i class="fa fa-trash text-danger"></i></td>
              </tr>
            <?php }  ?>
        </table>
        <div class="row">
           <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <button class="btn btn-success float-left mb-2"  onclick="adddeprows()" id="addRows_dep" type="button">Add More</button>
          </div>
        </div>
    </div>
</div></form>
    <?php }else{ ?>
    <div class="row">
        <?php
        foreach ((array)$depen as $depe) { ?>
        <div class="col-xl-4 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <p class="m-b-0 f-w-600">Name</p>
                    <p><?php echo $depe['dependent_name']; ?></p>
                    <p class="m-b-0 f-w-600">Relationship</p>
                       <p><?php echo $relationlist[$depe['dependent_relationship_type']]; ?></p>
                       <p class="m-b-0 f-w-600">Date Of Birth</p>
                       <p><?php echo $depe['dependent_date_of_birth']; ?></p>
                </div>
            </div>
        </div>
    <?php }  ?>

    </div>

    <?php } ?>
<?php }elseif($_POST['edittype'] == "award"){
    $awards= json_decode($candidates['candidate_awards'],1);
    if($_POST['editsubmit'] == "edit"){ ?>
<form id="awardform">
    <input type="hidden" name="edittype" value="award">
    <div class="dt-responsive table-responsive">
        <table class="table table-framed " id="awdtable">
           <tr>
            <th scope="col" class="" style="width: 50%;">Award Name</th>
            <th scope="col" class="thheader" style="width: 30%;">Award Descrption </th>
            <th scope="col" class="" style="width: 10%;"> Received Year</th>
            <th scope="col" style="width: 10%;">Action</th>
          </tr>
          <?php $k=1;
          $awards= json_decode($candidates['candidate_awards'],1);
          if(count((array)$awards) == '0'){
              $awards[] = array('award_name'=>'','award_description'=>'','award_year'=>'');
          }
            foreach ((array)$awards as $awd) { ?>
             <tr>
              <td>
                  <input aria-label="Award Name[]" type="text" name="award_name[]" class="form-control " autocomplete="off" value="<?php echo $awd['award_name']; ?>">
              </td>
              <td>
                  <input aria-label="Award Description[]" type="text" name="award_description[]" class="form-control" value="<?php echo $awd['award_description']; ?>" autocomplete="off">
              </td>
              <td>
                  <input aria-label="Award Year[]" type="month" name="award_year[]" class="form-control " value="<?php echo $awd['award_year']; ?>" >
              </td>
              <td><i class="fa fa-trash text-danger"></i></td>
            </tr>
          <?php  } ?>
        </table>
            <div class="row">
             <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
              <button class="btn btn-success float-left mb-2"  onclick="addawardrows()" id="addRows_award" type="button">Add More</button>
            </div>
          </div>
      </div>
  </form>

<?php }else{ ?>
<div class="row">
        <?php
        foreach ((array)$awards as $awardd) { ?>
        <div class="col-xl-4 col-md-6">
            <div class="card social-res-card">
                <div class="card-body">
                    <p class="m-b-0 f-w-600">Award Name</p>
                    <p><?php echo $awardd['award_name']; ?></p>
                    <p class="m-b-0 f-w-600">Description</p>
                       <p><?php echo $awardd['award_description']; ?></p>
                       <p class="m-b-0 f-w-600">Award Year</p>
                       <p><?php echo $awardd['award_year']; ?></p>
                </div>
            </div>
        </div>
    <?php }  ?>

    </div>

<?php }
}elseif($_POST['edittype'] == "skills"){
    $skils = json_decode($candidates['skills'],1);
    $emplode = implode(',',(array)$skils);
    if($_POST['editsubmit'] == "edit"){ ?>
        <form id="skillsform">
            <input type="hidden" name="edittype" value="skills">
             <div class="form-group pb-3">
                <span class="form-label">Skills</span>
                <div class="tags_add">
                  <input aria-label="Skills" type="text" name="skills" class="form-control" value="<?php echo $emplode; ?>" data-role="tagsinput">
                </div>
              </div>
        </form>
<?php }else{
        foreach((array)$skils as  $skil){ ?>
          <span class="form-label label label-success"><?php echo  $skil; ?></span>
<?php } ?>
<?php } ?>

<script type="text/javascript" src="assets/bower_components/bootstrap-tagsinput/js/bootstrap-tagsinput.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.10.4/typeahead.bundle.min.js" integrity="sha384-L+5n452EWFXO4Wnt9PcDq8OdxBC6vu1YnigaNE43IWRV3vpu1nY1H+iflfUs7N6N" crossorigin="anonymous"></script>

<?php   }elseif($_POST['edittype'] == "workexperience"){
    $exper= json_decode($candidates['candidate_experiances'],1);
if($_POST['editsubmit'] == "edit"){ ?>
    <form id="workexperienceform">
        <input type="hidden" name="edittype" value="workexperience">
        <div class="row">
            <div class="form-group col-lg-12 dt-responsive table-responsive">

    <div class="row">
     <div class="form-group col-lg-4">
      <span class="form-label">Total Experiance</span>
      <div class="input-group input-icon-right input-group-button">
       <input aria-label="Total Experiance" class="form-control width1"  type="text"  id="total_experiance" name="total_experiance"  value="<?php echo $candidates['total_experiance']; ?>">
       <span class="input-group-text addon-style">
                        <span class="">Months</span>
                    </span>
     </div>
   </div>
    <div class="form-group col-lg-12 dt-responsive table-responsive">
    <table class="table table-framed " id="extable">
     <tr>
      <th scope="col" class="" style="width: 30%;">Company Name</th>
      <th scope="col" class="thheader" style="width: 30%;">job Descrption </th>
      <th scope="col" class="" style="width: 20%;"> From</th>
      <th scope="col" class="thheader" style="width: 20%;">To </th>
      <th scope="col" style="width: 10%;">Files </th>
    </tr>
    <?php
    if(count((array)$exper) == '0'){
        $exper[] = array('company_name'=>'','job_descrption'=>'','work_from'=>'','work_to'=>'','compay_file'=>'');
    }
    $display_file = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_id."/Documents";
    $k=1;
      foreach ((array)$exper as $mm=> $expe) { ?>
       <tr id="removedworkexperiance<?php echo $mm; ?>">
        <td><input aria-label="Company Name[]" type="text" name="company_name[]" class="form-control " autocomplete="off" value="<?php echo $expe['company_name']; ?>">  </td>
        <td><input aria-label="Job Descrption[]" type="text" name="job_descrption[]" class="form-control" value="<?php echo $expe['job_descrption']; ?>" autocomplete="off">  </td>
        <td><input aria-label="Work From[]" type="text" name="work_from[]" class="form-control datepicker" value="<?php echo $expe['work_from']; ?>" autocomplete="off">  </td>
        <td><input aria-label="Work To[]" type="text" name="work_to[]" class="form-control datepicker" value="<?php echo $expe['work_to']; ?>"autocomplete="off">  </td>
        <td>

            <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
             <label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="Upload" style="left:-5px;top:-15px;">
              <span><i class="fa fa-upload"></i></span>
              <input aria-label="Dyanamic File" type="file" name="dyanamic_file" class="qualification_file"/>
              <input type="hidden" name="hidden_file[]" value="<?php echo $expe['compay_file']; ?>"/>
            </label>
            <?php if($expe['compay_file']!=''){ ?>
            <a href="<?php echo $display_file."/".$expe['compay_file']; ?>"  data-action="remove" data-bs-toggle="tooltip" title="Download" target="_blank" class="mt-2">
              <span><i class="fa fa-download"></i></span>
              </a>
            <?php } ?>
            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="Remove" onclick="removedRow('removedworkexperiance<?php echo $mm; ?>')"  role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
              <i class="ti-trash"></i>
            </span>

          </div>

        </td>
      </tr>
    <?php }  ?>
  </table>
  <div class="row">
   <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <button class="btn btn-success float-left mb-2"  onclick="addexpreencerows()" id="addRows_exp" type="button">Add More</button>
  </div>
</div>
</div>
</div>

            </div>
        </div>
    </form>

<?php  }else{ ?>
<div class="card-block">
    <div class="scroll-widget">
      <div class="latest-update-box">
        <?php foreach((array)$exper as $exper1){  ?>
        <div class="row p-t-20 p-b-30">
          <div class="col-auto text-end update-meta p-r-0">
            <i class="b-primary update-icon ring"></i>
          </div>
          <div class="col p-l-5">
            <a href="#!"><h6><?php echo $exper1['company_name']; ?></h6></a>
            <p class="text-muted m-b-0"><?php echo $exper1['work_from']." To ".$exper1['work_to']; ?>
            <?php if($exper1['compay_file']!=''){ ?>
<a href="<?php echo $display_file."/".$exper1['compay_file']; ?>"  data-action="remove" data-bs-toggle="tooltip" title="Download" target="_blank" style="float:left;position:absolute;right:18px;" class="mt-2 float-end" download>
        <i class="fa fa-download"></i>
              </a>
          <?php } ?>


            </p>
          </div>
        </div>
    <?php } ?>

      </div>
    </div>
    </div>

<?php } ?>

<?php  }elseif($_POST['edittype'] == "education"){
    $insts= json_decode($candidates['candidate_qualifications'],1);
    if($_POST['editsubmit'] == "edit"){
?>
<form id="educationform">
    <input type="hidden" name="edittype" value="education">
    <div class="row">


   <table class="table table-framed " id="edutable">
       <tr>
        <th scope="col" class="" style="width: 25%;">Institute Name</th>
        <th scope="col" class="thheader" style="width: 20%;">Type</th>
        <th scope="col" class="" style="width: 20%;"> Start Date</th>
        <th scope="col" class="thheader" style="width: 20%;">End Date </th>
        <th scope="col" class="thheader" style="width: 15%;">Score </th>
        <th scope="col" class="" style="width: 25%;">Subject taken</th>
        <th scope="col" class="thheader" style="width: 15%;">Files </th>

      </tr>
      <?php  if(count((array)$insts) == '0'){
          $insts[] = array('institute_name'=>'','education_type'=>'','start_date'=>'','end_date'=>'','subject'=>'','qualification_file'=>'');
           }
        foreach ((array)$insts as $kk=>$inst){  ?>
         <tr id="removeeducation<?php echo $kk; ?>">
          <td><input aria-label="Institute Name[]" type="text" name="institute_name[]" class="form-control" value="<?php echo $inst['institute_name']; ?>" autocomplete="off">  </td>
          <td>
           <select aria-label="Education Type[]" class="form-select js-example-basic-single" name="education_type[]" >
            <option value="0"> Select Any One</option>
            <?php foreach ((array)$edutypedata as $educode=> $edutype) {   ?>
              <option  <?php if($inst['education_type']== $educode){  ?> selected <?php
} ?>   value="<?php echo $educode; ?>"> <?php echo $edutype; ?></option>
            <?php   } ?>
          </select>
        </td>
        <td><input aria-label="Start Date[]" type="text" name="start_date[]" class="form-control datepicker"  value="<?php echo $inst['start_date']; ?>"  autocomplete="off">  </td>
        <td><input aria-label="End Date[]" type="text" name="end_date[]" class="form-control datepicker"   value="<?php echo $inst['end_date']; ?>" autocomplete="off">  </td>
        <td><input aria-label="Score[]" type="text" name="score[]" class="form-control "  value="<?php echo $inst['score']; ?>"  autocomplete="off">  </td>
         <td><input aria-label="Subject[]" type="text" name="subject[]" class="form-control "  value="<?php echo $inst['subject']; ?>"  autocomplete="off">  </td>
        <td>

            <div class="image-input image-input-empty image-input-outline" id="kt_image_5_2" >
             <label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="Upload" >
              <span><i class="fa fa-upload"></i></span>
              <input aria-label="Dyanamic File" type="file" name="dyanamic_file" class="qualification_file" />
              <input type="hidden" name="hidden_qualification_file[]" value="<?php echo $inst['qualification_file']; ?>"/>
            </label>
             <?php if($inst['qualification_file']!=''){ ?>
            <a href="<?php echo $display_file."/".$inst['qualification_file']; ?>"  data-action="remove" data-bs-toggle="tooltip" title="Download"  target="_blank"  class="mt-2">
              <span ><i class="fa fa-download"></i></span>
              </a>
            <?php } ?>

            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-bs-toggle="tooltip" title="Remove" onclick="removedRow('removeeducation<?php echo $kk; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
               <i class="ti-trash"></i>
            </span>
          </div>

        </td>

      </tr>
    <?php } ?>
  </table>
  <div class="row">
   <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

    <button class="btn btn-success float-left mb-2"  onclick="addedurows()" id="addRows_edu" type="button">Add More</button>
  </div>
</div>

    </div>
</form>

<?php }else{ ?>

<div class="card-block">
            <div class="scroll-widget">
              <div class="latest-update-box">
                <?php foreach((array)$insts as $inst){  ?>
                <div class="row p-t-20 p-b-30">
                  <div class="col-auto text-end update-meta p-r-0">
                    <i class="b-primary update-icon ring"></i>
                  </div>
                  <div class="col p-l-5">
                    <a href="#!"><h6><?php echo $inst['institute_name'].'-'.$edutypedata[$inst['education_type']]; ?></h6></a>
                    <p class="text-muted m-b-0"><?php echo $inst['start_date']." To ".$inst['end_date']; ?>

 <?php if($inst['qualification_file']!=''){ ?>
<a href="<?php echo $display_file."/".$inst['qualification_file']; ?>"  data-toggle="tooltip" title="Download"  target="_blank"  class="float-end" download>
        <i class="fa fa-download"></i>
              </a>
          <?php } ?>

         <span class="form-label label label-sm label-success float-end"><?php echo $inst['score']; ?></span>
                    </p>
                  </div>
                </div>
            <?php } ?>

              </div>
            </div>
          </div>

<?php } ?>
<?php } ?>
