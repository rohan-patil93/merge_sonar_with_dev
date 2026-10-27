<?php
if($_POST['filter'] == "Y"){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  $perfect_match = $_POST['perfect_match'] =='0'?"0":"1";
  $url = API."?mode=getcandidates&filter=Y&location_id=".$_POST['location_id']."&job_tittle_id=".json_encode($_POST['job_tittle_id'])."&gender=".json_encode($_POST['gender'])."&job_type=".json_encode($_POST['job_type'])."&expected_ctc_start=".$_POST['expected_ctc_start']."&expected_ctc_end=".$_POST['expected_ctc_end']."&total_experiance_start=".$_POST['total_experiance_start']."&total_experiance_end=".$_POST['total_experiance_end']."&alphabet=".$_POST['display_name_alphabet']."&skill=".json_encode($_POST['skill'])."&sort_by=".$_POST['sort_by']."&limit=".$_POST['limit']."&perfect_match=".$perfect_match;
  $api = callAPI($url);
  $result = json_decode($api,1);
  $locations = $result['locations'];
  $locationslist = array();
  foreach($locations as $location){
    $locationslist[$location['id']] = $location['location_name'];
  }
  $job_titles = $result['job_titles'];
  $candidates = $result['candidates'];
  $genders = array("M"=>"Male","F"=>"Female");
  $jobtype = array("1"=>"Contract","2"=>"Regular");
}
$stausresult = ViewStatusData();

function matchingResult($post,$candidate){
  $matching_score = 0;
  $skils = json_decode($candidate['skills'],1);
      $location_id = $post['location_id']; //done
      $job_tittle_id=$post['job_tittle_id'];//done
      $gender=$post['gender']; //done
      $job_type= $post['job_type'];//done
      $expected_ctc_start= $post['expected_ctc_start'];
      $expected_ctc_end=$post['expected_ctc_end'];
      $total_experiance_start= $post['total_experiance_start'];
      $total_experiance_end= $post['total_experiance_end'];
      $alphabet= $post['display_name_alphabet']; //done
      $skill = $post['skill'];//done

      if ($location_id == $candidate['location_id']){
        $matching_score += 1;
      }
      if(in_array($candidate['gender'],(array)$gender)){
        $matching_score += 1;
      }
      if(in_array($candidate['job_type'],(array)$job_type)){
        $matching_score += 1;
      }
      if(in_array($candidate['job_type'],(array)$job_type)){
        $matching_score += 1;
      }
      if(in_array($candidate['job_tittle_id'],(array)$job_tittle_id)){
        $matching_score += 1;
      }
      if ($alphabet == $candidate['display_name'][0]){
        $matching_score += 1;
      }
      foreach($skill as $skills) {
        if(in_array($skills, $skils)) {
          $matching_score += 1;
        }
      }
      if(round($candidate['expected_ctc']) >= $expected_ctc_start && round($candidate['expected_ctc']) <= $expected_ctc_end){
        $matching_score += 1;
      }
      if($candidate['total_experiance'] >= $total_experiance_start && $candidate['total_experiance'] <= $total_experiance_end){
        $matching_score += 1;
      }
      return $matching_score;
    }

    if(count((array)$candidates) !=0){
      if($_POST['table_type'] =="L"){
        ?>
        <div class="col-lg-12">
          <div class="card table-responsive">
            <table class="table table-hover m-b-0 " style="font-size:13px;">
              <thead>
                <tr><th scope="col">#</th><th scope="col">Candidate Code</th><th scope="col">Name</th><th scope="col">Email ID</th><th scope="col">Phone No</th><th scope="col">Location</th><th scope="col">Type</th><th scope="col">Action</th></tr>
              </thead>
              <tbody>
                <?php $mk=1; foreach($candidates as $candidate){
                  ?>
                  <tr>
                    <td><?php echo $mk; ?></td>
                    <td><?php echo $candidate['candidate_code']; ?></td>
                    <td><?php echo $candidate['display_name']; ?></td>
                    <td><?php echo $candidate['email_id']; ?></td>
                    <td><?php echo DynamicPhoneNumberFormt($candidate['phone']); ?></td>
                    <td><?php echo $locationslist[$candidate['location_id']]; ?></td>
                    <td><?php echo $jobtype[$candidate['job_type']]; ?></td>
                    <td>
                      <a href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$candidate['id']); ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
                    </td>
                  </tr>
                  <?php $mk++;
} ?>
                </tbody>
              </table>
            </div>
          </div>

        <?php }else{

          foreach($candidates as $candidate){
            $skils = json_decode($candidate['skills'],1);

            ?>
            <div class="col-lg-4">
              <div class="card sale-card" style="min-height: 270px;">
                <div class="card-block">
                  <div class="row align-items-center  border-bottom">

        <div class="col-lg-2 p-r-0">
          <?php if($candidate['image'] != ""){
            $dispath = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate['id']."/".$candidate['image'];
            ?>
            <img src="<?php echo $dispath; ?>" class="img-radius img-50" alt="User">
          <?php }else{ ?>
            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($candidate['display_name']); ?></span>
          <?php } ?>

        </div>
        <div class="col-lg-10 p-l-20">
          <a class="f-w-600 f-18 text-capitalize d-block" href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$candidate['id']); ?>" ><?php echo $candidate['display_name']; ?>
          <?php if($candidate['employee_id']!=""){?>
          <span class="form-label label label-sm label-warning">Emp</span> <?php } ?></a>
          <p class="f-12 f-w-600 text-muted mb-0" ><?php echo $candidate['candidate_code']; ?>
          <?php if(isset($jobpostingcandidates[$candidate['id']])){ ?>
            <i class='fa fa-check text-info'></i>
          <?php }else{ ?>

          <?php } ?>
          <?php echo $candidate['phone'] !=""?" <b class='float-end d-inline-block'><a href='tel:".$candidate['phone']."'>".DynamicPhoneNumberFormt($candidate['phone'])."</a></b>":""; ?>
        </p>

        <?php echo $candidate['email_id'] !=""?" <b class='float-end d-inline-block' style='word-break: break-all;'> ".$candidate['email_id']."</b>":""; ?>

      </div>
      <?php
      if($_POST['perfect_match'] == '0' || !isset($_POST['location_id'])){
       $matchpercentage= 100;
     }else{
      $matchpercentage = round((matchingResult($_POST,$candidate)/9) * 100);

    }
    ?>
    <div class="col-lg-7 mt-2">
      <div class="rating f-12 text-warning mt-2 d-inline-block">
        <?php echo $matchpercentage >= '0'?"<i class='fa fa-star'></i>":""; ?>
        <?php echo $matchpercentage >= '20'?"<i class='fa fa-star'></i>":""; ?>
        <?php echo $matchpercentage >= '40'?"<i class='fa fa-star'></i>":""; ?>
        <?php echo $matchpercentage >= '60'?"<i class='fa fa-star'></i>":""; ?>
        <?php echo $matchpercentage >= '80'?"<i class='fa fa-star'></i>":""; ?>
        <b class="text-muted f-12 f-w-600 m-l-10"><?php
        echo $matchpercentage;
        ?>%</b>
      </div>
    </div>
    <div class="col-lg-5 text-right">
      <span class="form-label label" style="color: <?php echo $stausresult[$candidate['current_status']]['color']; ?>;    background:  <?php echo $stausresult[$candidate['current_status']]['background_color']; ?>; border-color: <?php echo $stausresult[$candidate['current_status']]['background_color']; ?>; font-size: 8px;" ><?php echo  $stausresult[$candidate['current_status']]['name']; ?> </span>
    </div>
    <div class="col-lg-7 mt-2">
      <span class="f-12 f-w-600 text-muted mb-0">Available By: <?php echo $candidate['excepted_doj'] !=""?fromDate($candidate['excepted_doj']) : "-"; ?></span>
    </div>
    <div class="col-lg-5 text-right mt-2" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Expected CTC">
     <span class="f-14 f-w-600 text-danger mb-0"><?php echo $candidate['expected_ctc'] !="0"?customeummarmonyformateifine($candidate['expected_ctc']):"-";?></span>
   </div>

   <div class="col-lg-6">
    <span class="f-12 f-w-600 text-muted mb-0"><?php echo $locationslist[$candidate['location_id']] !=""?$locationslist[$candidate['location_id']]:"-"; ?></span>
  </div>
  <div class="col-lg-6 text-right">
    <span class="f-12 f-w-600 text-muted mb-0"><?php echo $jobtype[$candidate['job_type']]!=""?$jobtype[$candidate['job_type']]:"-"; ?></span>
  </div>

  <div class="col-lg-12 mt-0" style="max-height: 50px; min-height: 50px;">

    <p class="mb-0 f-12 text-muted mt-3 custom-limit-text" style="--linehide:2;"><?php echo $candidate['bio']; ?></p>
  </div>
  <div class="col-lg-12 mt-3 custom-scroll"  style="min-height: 60px;max-height: 60px;overflow-y: scroll;">
    <div class="mb-3 pb-3">
      <?php foreach($skils as  $skil){ ?>
        <span class="form-label label label-success"><?php echo  $skil; ?></span>
      <?php } ?>
    </div>
  </div>

</div>
<div class="row mt-3">
   <div class="col-lg-12 text-center">
     <div class="position-relative d-inline-block">
        <?php $emailcnt = $email_logs[$candidate['email_id']][12]; echo ($emailcnt !='0' && $emailcnt !='')?"<i class='fa fa-check text-info position-absolute mt-4 f-10 m-l-5'></i>":""; ?>
        <i class="ti-mobile text-danger f-18 cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Phone Conversation Request <?php echo $emailcnt !=""?" -".$emailcnt:"";?>" onclick="sendEmailCandidate('<?php echo $candidate['id']; ?>','<?php echo $candidate['email_id']; ?>','12')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
      </div>
      <div class="position-relative d-inline-block">
        <?php $emailcnt = $email_logs[$candidate['email_id']][13]; echo ($emailcnt !='0' && $emailcnt !='')?"<i class='fa fa-check text-info position-absolute mt-4 m-l-20 f-10'></i>":""; ?>
      <i class="ti-themify-favicon m-l-15 text-danger f-18 cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Invitation to In-Person Interview <?php echo $emailcnt !=""?" -".$emailcnt:"";?>" onclick="sendEmailCandidate('<?php echo $candidate['id']; ?>','<?php echo $candidate['email_id']; ?>','13')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
    </div>
    <div class="position-relative d-inline-block">
         <?php $emailcnt = $email_logs[$candidate['email_id']][14]; echo ($emailcnt !='0' && $emailcnt !='')?"<i class='fa fa-check text-info position-absolute mt-4 m-l-20 f-10'></i>":""; ?>
       <i class="ti-thumb-up m-l-15 text-danger f-18 cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Inform Selection <?php echo $emailcnt !=""?" -".$emailcnt:"";?>" onclick="sendEmailCandidate('<?php echo $candidate['id']; ?>','<?php echo $candidate['email_id']; ?>','14')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
    </div>
    <div class="position-relative d-inline-block">
       <?php $emailcnt = $email_logs[$candidate['email_id']][15]; echo ($emailcnt !='0' && $emailcnt !='')?"<i class='fa fa-check text-info position-absolute mt-4 m-l-20 f-10'></i>":""; ?>
      <i class="ti-thumb-down m-l-15 text-danger f-18 cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Regret To Inform <?php echo $emailcnt !=""?" -".$emailcnt:"";?>" onclick="sendEmailCandidate('<?php echo $candidate['id']; ?>','<?php echo $candidate['email_id']; ?>','15')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
    </div>
    <div class="position-relative d-inline-block">
       <?php $emailcnt = $email_logs[$candidate['email_id']][17]; echo ($emailcnt !='0' && $emailcnt !='')?"<i class='fa fa-check text-info position-absolute mt-4 m-l-20 f-10'></i>":""; ?>
      <i class="ti-medall m-l-15 text-danger f-18 cursor-pointer"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Request for References <?php echo $emailcnt !=""?" -".$emailcnt:"";?>" onclick="sendEmailCandidate('<?php echo $candidate['id']; ?>','<?php echo $candidate['email_id']; ?>','17')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
    </div>
      <div class="position-relative d-inline-block job cursor-pointer " onclick="assignedCandidateFiletr('<?php echo $candidate['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Applied Jobs" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <i class="ti-briefcase m-l-15 f-18 text-danger"></i>
        <span class="badge bg-c-red"><?php echo $jobpostingcandidates[$candidate['id']]; ?></span>
      </div>

      <i class="ti-email m-l-15 f-18 text-danger cursor-pointer"  onclick="sendEmailCandidate('<?php echo $candidate['id']; ?>','<?php echo $candidate['email_id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Send Email" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
  </div>

      </div>
    </div>
  </div>
</div>
<?php }
}
}else{ ?>
 <div clas="card">
  <div class="card-px text-center py-20 m-t-30">
    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates </p>
    <div class="text-center px-4">
      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
  </div>
</div>

<?php } ?>
