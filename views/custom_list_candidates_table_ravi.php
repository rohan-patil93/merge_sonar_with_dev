<?php 
if($_POST['filter'] == "Y"){
  include('../includes/session.php');
  include('../dbconfig.php');
  include('../comm.php');
  $perfect_match = $_POST['perfect_match'] =='0'?"0":"1";
  $url = API."?mode=getcandidates&filter=Y&location_id=".$_POST['location_id']."&job_tittle_id=".json_encode((array)$_POST['job_tittle_id'])."&gender=".json_encode((array)$_POST['gender'])."&job_type=".json_encode((array)$_POST['job_type'])."&expected_ctc_start=".$_POST['expected_ctc_start']."&expected_ctc_end=".$_POST['expected_ctc_end']."&total_experiance_start=".$_POST['total_experiance_start']."&total_experiance_end=".$_POST['total_experiance_end']."&alphabet=".$_POST['display_name_alphabet']."&skill=".json_encode((array)$_POST['skill'])."&sort_by=".$_POST['sort_by']."&limit=".$_POST['limit']."&perfect_match=".$perfect_match; 
  $api = callAPI($url);
  $result = json_decode($api,1); 
  $locations = $result['locations'];
  $locationslist = array();
  foreach((array)$locations as $location){
    $locationslist[$location['id']] = $location['location_name'];
  }
  $job_titles = $result['job_titles'];
  $candidates = $result['candidates'];
  $genders = array("M"=>"Male","F"=>"Female");
  $jobtype = array("1"=>"Contract","2"=>"Regular");
  //echo "<pre>"; print_r($result);
}
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
  foreach((array)$skill as $skills) {
    if(in_array($skills, (array)$skils)) { 
      $matching_score += 1;
    }
  }
  if(round($candidate['expected_ctc']) >= $expected_ctc_start AND round($candidate['expected_ctc']) <= $expected_ctc_end){
    $matching_score += 1;
  } 
  if($candidate['total_experiance'] >= $total_experiance_start AND $candidate['total_experiance'] <= $total_experiance_end){
    $matching_score += 1;
  }
  return $matching_score;
}
//echo "<pre>"; print_r($_POST); print_r($candidates);
if(count((array)$candidates)>0){
      if($_POST['table_type'] =="L"){
        ?>
        <div class="col-lg-12">
          <div class="card table-responsive">
            <table class="table table-hover m-b-0 " style="font-size:13px;">
              <thead>
                <tr><th>#</th><th>Candidate Code</th><th>Name</th><th>Email ID</th><th>Phone No</th><th>Location</th><th>Type</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php $mk=1; foreach((array)$candidates as $candidate){ 
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
                  <?php $mk++; } ?> 
                </tbody>
              </table>
            </div>
          </div>
<?php }else{ 
          foreach((array)$candidates as $candidate){ 
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
            <img src="<?php echo $dispath; ?>" class="img-radius img-50" alt="User-Image" onerror="this.src='uploads/all/no-image.jpg';">
          <?php }else{ ?>
            <span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($candidate['display_name']); ?></span>
          <?php } ?>
        </div>
        <div class="col-lg-10 p-l-20">
          <a class="f-w-600 f-18 text-capitalize d-block" href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$candidate['id']); ?>" ><?php echo $candidate['display_name']; ?> 
          <?php if($candidate['employee_id']!=""){?>
          <label class="form-label label label-sm label-warning">Emp</label> 
          <?php } ?>
          </a>
           <p class="f-12 f-w-600 text-muted mb-0" ><?php echo $candidate['candidate_code']; ?>  
         
          <?php echo $candidate['phone'] !=""?" <b class='float-end d-inline-block'><a href='tel:".$candidate['phone']."'>".$candidate['phone']."</a></b>":""; ?>
        </p>
        
        <?php echo $candidate['email_id'] !=""?" <b class='float-end d-inline-block' style='word-break: break-all;'> ".$candidate['email_id']."</b>":""; ?>

        </div>
         <?php $matchpercentage= 100; 

      /*if($_POST['perfect_match'] == '0' OR !isset((array)$_POST['location_id'])){  
       $matchpercentage= 100; 
     }else{
      //$matchpercentage = round((matchingResult($_POST,$candidate)/9) * 100);
$matchpercentage= 100; 
    }*/
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
<?php } ?>
<?php } ?>

<?php }else{ ?>
  <div class="col-lg-12">
   <div class="card">
      <div class="card-body">
          <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Candidates</p>   
            <div class="text-center px-4">
              <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>   
          </div>
      </div>
    </div>
  </div>


<?php } ?>