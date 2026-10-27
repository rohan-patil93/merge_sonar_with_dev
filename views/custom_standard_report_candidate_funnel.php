<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);

$userrolelist = json_encode($user_roles);
$url = API."?mode=togetcandidatefunnel&post_id=".$_GET['post_id']."&userrole=".$userrolelist;
$apires = callAPI($url);
$result = json_decode($apires,1);
$job_postings = $result['job_postings'];
$jobpost = $result['jobpost'];
$cand_count = count((array)$result['candidates']);
foreach($result['status'] as $casta){
    $statusval[] = $casta['status'];
    $status_name[$casta['status']] = $casta['name'];
}
$listholdstatus = array(); $listnorstatus = array(); $liststatus = array(); $rejectondata = array();
$hirec=0;
foreach($result['candidates'] as $candida){
    $liststatus[$candida['current_status']][] = $candida;
    if($candida['hold_yes_no']=='0'){
      $listholdstatus[$candida['current_status']][] = $candida;
    }elseif($candida['rejection_yes_no']=='0'){
      $listrejstatus[$candida['current_status']][] = $candida;
      $rejectondata[$candida['rejection_reason_id']][]=$candida;
    }else{
      $listnorstatus[$candida['current_status']][] = $candida;
    }
    foreach($statusval as $stval){
      if($stval<$candida['current_status']){
        $listnorstatus[$stval][] = $candida;
        $liststatus[$stval][] = $candida;
      }
    }
    if($candida['current_status']==ST_LOIACCEPTED){
      $hirec = $hirec+1;
    }
    $cssdata[] = array('Job Code'=> $jobpost['job_number'],'Job Name'=> $jobpost['job_name'], 'Candidate Name'=> $candida['display_name'], 'Candidate Email'=> $candida['email_id'],'Applied Date'=> date('Y-m-d',strtotime($candida['created_date'])),'Status'=> $status_name[$candida['current_status']], 'Status Date'=> date('Y-m-d',strtotime($candida['ca_updated_date'])));
}
$_SESSION['download_data'] = $cssdata;
?>
<script type="text/javascript">
  function jobpostclick(postid){
    window.location.href = 'index.php?option=COM_STANDARD_REPORT_CANDIDATE_FUNNEL&post_id='+postid;
  }
</script>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="">Candidate Funnel</h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate Funnel" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate Funnel" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="col-lg-3">
      <nav class="all-employee">
        <ul>
          <li class="d-block border"><a href="#"><?php echo $jobpost['job_name'] !=""?$jobpost['job_name']:"Select Any Job"; ?></a>
            <ul>
              <?php foreach($job_postings as $jobtitle){ ?>
              <li><a href="#" ><?php echo $jobtitle['name']; ?> <i class="ti-angle-right float-end f-12 m-t-5"></i> </a>
                <ul>
                   <?php foreach($jobtitle['data'] as $jobposts){ ?>
                    <li><button type="button" onclick="jobpostclick(<?php echo $jobposts['id']; ?>)"> <?php echo $jobposts['job_name']." (".$jobposts['job_number'].")"; ?></button></li>

                  <?php } ?>
                </ul>
              </li>
              <?php } ?>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
      <div class="mt-5">
        <h3 class="text-primary"><i class="ti-hand-point-right"></i> Ready to go? Just select a job opening to see the details.</h3>
        <p>We'll show you the history of all the candidates for the job opening; where they got stuck, where they ended up, all of it.</p>
      </div>
    </div>
  </div>
  <?php if($_GET['post_id']!=""){ ?>
  <div class="card finance-analyst-sec">
    <div class="card-body">
      <div class="row mt-4">
        <div class="col-lg-5">
          <div class="row">
            <div class="col-lg-1"><i class="ti-briefcase f-40 text-success"></i></div>
            <div class="col-lg-9">
              <h4 class="f-w-600 text-success"><?php echo $jobpost['job_name']." ( ".$jobpost['job_number']." )" ; ?></h4>
              <p class="mb-0 f-12"><?php echo date('M dS, Y', strtotime($jobpost['requested_date'])); ?></p>
              <p class="mb-0 f-12"><?php echo notificationdatesdiff($jobpost['requested_date']); ?></p>
            </div>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="row">
            <div class="col-lg-1 text-center">
              <i  class="fa fa-eye f-24 text-success border p-2 img-radius"></i>
            </div>
            <div class="col-lg-3">
              <h4 class="f-w-600 text-success mb-2"><?php echo $result['viewed']; ?></h4>
              <p class="f-12 mb-0">POSTING VIEWS</p>
            </div>
            <div class="col-lg-1 text-center">
              <i  class="fa fa-user f-24 text-success border p-2 img-radius"></i>
            </div>
            <div class="col-lg-3">
              <h4 class="f-w-600 text-success mb-2"> <?php echo count((array)$result['candidates']); ?> <span class="f-w-300 text-muted f-14"></span></h4>
              <p class="f-12 mb-0">TOTAL CANDIDATES</p>
            </div>
            <div class="col-lg-1 text-center">
              <i class="fa fa-calendar f-24 text-success border p-2 img-radius"></i>
            </div>
            <div class="col-lg-3">
              <h4 class="f-w-600 text-success mb-2"> <?php echo $result['candidates']['0']['created_date'] !=""?trim(notificationdatesdiff($result['candidates']['0']['created_date'],'N'),","):"-"; ?></h4>
              <p class="f-12 mb-0">LAST APPLIED</p>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-5">
        <div class="float-left">
        <div class="d-inline-block m-r-10 align-top"><i class="fa fa-check-circle text-success f-24"></i></div>
          <div class="d-inline-block">
          <h4 class="text-success f-w-600">Active Status Workflow</h4>
          <p>This shows how all candidates to this job opening have progressed through the status workflow.</p>
          </div>
         </div>
        </div>
         <div class="float-right">
            <div class="float-">
            <h4 class="f-w-600 mb-0"> <?php if($hirec=='0'){ echo "Zero";
} else{ echo $hirec;
} ?> Hire(s)</h4>
            <p>Average time to hire is <?php echo $result['average']; ?> days.</p>
            </div>
          </div>
      </div>

    <div class="clear-both"></div>
      <div class="card-body">
      <?php $cntcnad= count((array)$result['status']); $j=1; foreach($result['status'] as $castatus){
        $st_count =0; $sthold_count =0; $strej_count=0;
        $st_count = count((array)$liststatus[$castatus['status']]);
        $st_p =  $cand_count !=0?($st_count/$cand_count)*100:"0";
        $sthold_count = count((array)$listholdstatus[$castatus['status']]);
        $sthold_p =  $cand_count !=0?($sthold_count/$cand_count)*100:0;
        $stnor_count = count((array)$listnorstatus[$castatus['status']]);
        $stnor_p =  $cand_count !=0?($stnor_count/$cand_count)*100:0;
        $strej_count = count((array)$listrejstatus[$castatus['status']]);
        $strej_p =  $cand_count !=0?($strej_count/$cand_count)*100:0;

      ?>
        <div class="row mt-3">
          <div class="col-lg-3">
            <div class="bg-gray p-4">
              <h5><?php echo $castatus['action_label']; ?></h5>
              <p class="mb-0"><i class="fa fa-clock-o"></i> Last Applied <?php echo $result['candidates']['0']['created_date'] !=""?trim(notificationdatesdiff($result['candidates']['0']['created_date'],'N'),","):"-"; ?></p>
            </div>
            <?php if($cntcnad != $j){ ?>
            <div class="text-center">
              <div class="dropdown-primary dropdown open">
                <i class="ti-arrow-down d-block mb-1"></i>
              </div>
            </div>
          <?php } ?>
          </div>
          <div class="col-lg-9">
            <div class="bar multi-color">

              <div class="color bg-success" style="width: <?php echo customNanAndEmptyValidation($stnor_p); ?>%; <?php if($st_p>27){ echo "color: #FFFFFF;";
}?>">
                <p class="mb-0"><i class="fa fa-user"></i> <?php echo customNanAndEmptyValidation($st_count);  ?> Candidate (<?php echo customNanAndEmptyValidation($st_p); ?>% of Total All-Time)</p>
                <p class="mb-0"><i class="fa fa-sign-in"></i> <?php echo $strej_count;  ?> (<?php echo $st_count !=0?customNanAndEmptyValidation(round((($strej_count)/$st_count)*100)):0; ?>%) SET TO NOT HIRED FROM THIS STATUS</p>
                <p class="mb-0"><i class="fa fa-sign-in"></i> <?php echo $sthold_count;  ?> (<?php echo $st_count !=0?customNanAndEmptyValidation(round((($sthold_count)/$st_count)*100)):0; ?>%) SET TO BE HOLD</p>

              </div>
              <?php if($sthold_count>0){ ?>
                <div class="color bg-warning" style="width: <?php echo $sthold_p; ?>%; "></div>
              <?php } ?>
              <?php if($strej_count>0){ ?>
                <div class="color bg-danger" style="width: <?php echo $strej_p; ?>%; "></div>
              <?php } ?>


            </div>
          </div>
        </div>
      <?php $j++;
} ?>


      <div class="mt-5">
        <div class="d-inline-block m-r-10 align-top"><i class="fa fa-times-circle text-danger f-24"></i></div>
        <div class="d-inline-block">
          <h4 class="text-danger f-w-600">'Not Hired Because...' </h4>
          <p>This shows where all inactive candidates ended up in your status workflow.</p>
        </div>
      </div>

      <?php if(count((array)$rejectondata)>0){ ?>
      <?php foreach($rejectondata as $keyrej=>$rej){
          $ccrej = count((array)$rej);
          $ccd =date('Y-m-d');  $m=1;
          foreach($rej as $rrcan){
            if(strtotime($rrcan['created_date'])<strtotime($ccd)){ $ccd = $rrcan['created_date']; if($m==1){$upd = $ccd;
}
}
            if(strtotime($rrcan['updated_date'])>strtotime($upd)){ $upd = $rrcan['updated_date'];
}
          }
      ?>
      <div class="row mt-3">
        <div class="col-lg-3">
          <div class="bg-gray p-3">
            <h5><?php echo $result['hold_rejection'][$keyrej]; ?></h5>
            <p> Active for  <?php echo trim(notificationdatesdiff($ccd,'N',$upd),","); ?> - Average</p>
          </div>
          <div class="text-center">
            <div class="dropdown-primary dropdown open">

            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <div class="bar multi-color">
            <div class="color bg-danger" style="width: 100%;color: #FFFFFF;">
              <p class="mb-0"><i class="fa fa-user"></i> <?php echo $cand_count; ?> Candidate (100% of Total All-Time)</p>
              <p class="mb-0"><i class="fa fa-sign-in"></i> <?php echo $ccrej; ?>  (<?php echo round(($ccrej/$cand_count)*100) ?>%) SET TO NOT HIRED</p>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
    <?php }else{?>
      <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Candidates </p>
                    <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                  </div>
      <?php } ?>
    </div>
  </div>
</div>
  <?php } ?>
</div>
