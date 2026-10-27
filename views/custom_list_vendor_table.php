<?php
if($_POST['filter'] == "Y"){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  $perfect_match = $_POST['perfect_match'] =='0'?"0":"1";
  $url = API."?mode=getvendors&filter=Y&location_id=".$_POST['location_id']."&vendor_name=".$_POST['vendor_name']."&vendor_email=".$_POST['vendor_email']."&gstin=".$_POST['gstin']."&vendor_tax_reg_no=".$_POST['vendor_tax_reg_no']."&cst_reg_no=".$_POST['cst_reg_no']."&pan_no=".$_POST['pan_no']."&total_experiance_end=".$_POST['total_experiance_end']."&alphabet=".$_POST['display_name_alphabet']."&skill=".json_encode($_POST['skill'])."&sort_by=".$_POST['sort_by']."&limit=".$_POST['limit']."&perfect_match=".$perfect_match;
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
$vendor_rate = $result['vendor_rate'];

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
                <tr><th scope="col">#</th><th scope="col">Name</th><th scope="col">Email ID</th><th scope="col">Phone No</th><th scope="col">Location</th><th scope="col">Ratting</th><th scope="col">Action</th></tr>
              </thead>
              <tbody>
                <?php $mk=1; foreach($candidates as $candidate){
                  ?>
                  <tr>
                    <td><?php echo $mk; ?></td>

                    <td><?php echo $candidate['display_name']; ?></td>
                    <td><?php echo $candidate['email_id']; ?></td>
                    <td><?php echo DynamicPhoneNumberFormt($candidate['phone']); ?></td>
                    <td><?php echo $locationslist[$candidate['location_id']]; ?></td>

                    <td>
                      <div class="rating f-12 text-warning d-inline-block">
                        <?php if(isset($vendor_rate[$candidate['id']])){
                           for($n=1;$n<11;$n++){ ?>
                            <i class="fa fa-star f-12" <?php if($n>$vendor_rate[$candidate['id']]){ ?> style= "color: #b7aeae"; <?php
} ?>></i>
                        <?php } ?>
                        <?php } ?>
                        </div>
                    </td>
                    <td>
                      <a href="index.php?option=COM_INSERT_VENDORS&Rid=<?php echo deocdeId("id:".$candidate['id']); ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
                      <a href="index.php?option=COM_INSERT_VENDORS&Rid=<?php echo deocdeId("id:".$candidate['id']); ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Survey Histories"><i class="ti-eye"></i></a>
                      <a href="index.php?option=COM_INSERT_VENDORS&Rid=<?php echo deocdeId("id:".$candidate['id']); ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Survey"><i class="ti-write"></i></a>
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

        <div class="col-lg-12 row" style="min-height: 65px;">
        <div class="col-lg-2 p-r-0">
          <?php if($candidate['image'] != ""){
            $dispath = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate['id']."/".$candidate['image'];
            ?>
            <img src="<?php echo $dispath; ?>" class="img-radius img-50" alt="User">
          <?php }else{ ?>
            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($candidate['company']); ?></span>
          <?php } ?>

        </div>
        <div class="col-lg-10 p-l-20" >
           <a class="f-w-600 f-14 text-capitalize d-block m-l-10" href="index.php?option=COM_INSERT_VENDORS&Rid=<?php echo deocdeId("id:".$candidate['id']); ?>" >
           <?php if($candidate['company'] !=""){ ?>
              <h6 class="mb-0 text-primary"><?php echo $candidate['company']; ?></h6>
              <p class="text-muted m-b-0"> <?php echo $candidate['display_name']; ?></p>
          <?php }else{ ?>
              <h6 class="mb-0 text-primary"> <?php echo $candidate['display_name']; ?></h6>
          <?php } ?></a>

          <p class="f-12 f-w-600 text-muted mb-0" ><?php echo $candidate['candidate_code']; ?>


          <?php if(isset($jobpostingcandidates[$candidate['id']])){ ?>
            <i class='fa fa-check text-info'></i>
          <?php }else{ ?>

          <?php } ?>
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
    </div>
    <div class="col-lg-12 row" style="min-height: 200px;">
    <div class="col-lg-3 ">
      <span class="f-12 f-w-600  mb-0">Email</span>
    </div>
    <div class="col-lg-9 ">
      <span class="f-12 f-w-600 text-muted mb-0"><?php echo $candidate['email'] !=""?$candidate['email'] : "-"; ?></span>
    </div>
     <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600  mb-0">Phone</span>
    </div>
     <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 mb-0">Location</span>
    </div>
    <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 text-muted mb-0"><?php echo $candidate['phone'] !=""?$candidate['phone'] : "-"; ?></span>
    </div>
    <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 text-muted mb-0"><?php echo $locationslist[$candidate['location_id']] !=""?$locationslist[$candidate['location_id']]:"-"; ?></span>
    </div>
     <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600  mb-0">GST No</span>
    </div>
     <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 mb-0">Tax Registation No</span>
    </div>
    <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 text-muted mb-0"><?php echo $candidate['gstin'] !=""?$candidate['gstin'] : "-"; ?></span>
    </div>
    <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 text-muted mb-0"><?php echo $candidate['vendor_tax_reg_no'] !=""?$candidate['vendor_tax_reg_no'] : "-"; ?></span>
    </div>
     <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600  mb-0">CST Registation No</span>
    </div>
     <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 mb-0">PAN No</span>
    </div>
    <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 text-muted mb-0"><?php echo $candidate['cst_reg_no'] !=""?$candidate['cst_reg_no'] : "-"; ?></span>
    </div>
    <div class="col-lg-6 mt-1">
      <span class="f-12 f-w-600 text-muted mb-0"><?php echo $candidate['pan_no'] !=""?$candidate['pan_no'] : "-"; ?></span>
    </div>
    <div class="col-lg-3 mt-1 mb-2">
      Ratting
    </div>
    <div class="col-lg-9 mt-1 mb-2">
      <div class="rating f-12 text-warning d-inline-block">
        <?php if(isset($vendor_rate[$candidate['id']])){
           for($n=1;$n<11;$n++){ ?>
            <i class="fa fa-star f-12" <?php if($n>$vendor_rate[$candidate['id']]){ ?> style= "color: #b7aeae"; <?php
} ?>></i>
        <?php } ?>
        <?php } else { echo "<span class='text-danger'>Yet To Rate</span>";
} ?>
        </div>
    </div>
   </div>

</div>
<div class="row mt-3">
   <div class="col-lg-12 text-center">


      <div class="position-relative d-inline-block job cursor-pointer " onclick="appliedvendorfeedback('<?php echo $candidate['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Provide Feedback" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <i class="fa fa-edit m-l-15 f-16 text-danger"></i>
        <span class="badge bg-c-red"></span>
      </div>
      <div class="position-relative d-inline-block">
       <?php $emailcnt = $email_logs[$candidate['email_id']][17]; echo ($emailcnt !='0' && $emailcnt !='')?"<i class='fa fa-check text-info position-absolute mt-4 m-l-20 f-10'></i>":""; ?>
      <i class="fa fa-history m-l-15 text-danger f-16 cursor-pointer"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Feedback History" onclick="vendorhistories('<?php echo $candidate['id']; ?>','<?php echo $candidate['email_id']; ?>','17')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
    </div>

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
    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Vendors </p>
    <div class="text-center px-4">
      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
  </div>
</div>

<?php } ?>
