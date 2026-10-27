<?php
$postids ="";
$todate = date("Y-m-d");
$fromdate =  date("Y-m-d",strtotime("-3 months"));
if(isset($_POST['applyform'])) {
  $fromdate = date('Y-m-d',strtotime(str_replace('/"', '-', $_POST['fromdate'])));
  $todate = date('Y-m-d',strtotime(str_replace('/"', '-', $_POST['todate'])));
  $view_post = $_POST['job_post'];
  $postids = implode(",", (array)$_POST['job_post']);
}
$url = API."?mode=togetcandidatebydisabilitygender&post_id=".$postids."&fromdate=".$fromdate."&todate=".$todate;
$apires = callAPI($url);
$result = json_decode($apires,1);
$job_postings = $result['job_postings'];

foreach($result['data'] as $candida){
  if($candida['disability'] == ""){
    $candida['disability'] = 3;
  }
  $data[$candida['disability']]= $candida;
  $total_count = $total_count+$candida['gc'];
}
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Candidates by Disability and Gender</h5>
      <div class="card-header-right">
       <div class="dropdown-primary dropdown open">
        <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
        <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

         <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate by Disability and Gender" target="_blank" >CSV</a>
         <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate by Disability and Gender" target="_blank">Excel</a>
       </div>
     </div>
   </div>
 </div>
 <?php include_once 'custom_filter_report_topbar.php'; ?>
</div>
<div class="card">
  <div class="card-body">
    <div class="row col-lg-12">
      <div class="col-lg-9">
        <div class="row mt-3 col-lg-12">
          <?php
          if($data['1']['gc']==""){ $nod = 0;
          }else{
            $nodm = (($data['1']['ma']/$total_count)*100);
            $nodf = (($data['1']['fm']/$total_count)*100);
            $nod = $data['1']['gc'];
          } ?>
          <div class="col-lg-12 f-w-600">
            <p class="mb-0 f-w-600">No, I don't have a disability</p>
          </div>
          <div class="col-lg-3">
            <div class="bg-gray p-4">
              <p class="mb-0"> Total : <?php echo $data['1']['ma']+$data['1']['fm']; ?> </p>
              <p class="mb-0"> Male : <?php echo $data['1']['ma']; ?> Female :  <?php echo $data['1']['fm']; ?> </p>
            </div>
          </div>
          <div class="col-lg-9">
            <div class="bar multi-color">
              <?php if($nodm!=0){ ?>
                <div class="color bg-success" style="width: <?php echo $nodm; ?>%;background-color:#82bac7 !important; "></div>
              <?php } ?>
              <?php if($nodf!=0){ ?>
                <div class="color bg-success" style="width: <?php echo $nodf; ?>%;background-color:#cf9eb5 !important; "> </div>
              <?php } ?>
            </div>
          </div>

          <?php
          if($data['0']['gc']==""){
            $nod = 0;
          }else{
            $nodm = (($data['0']['ma']/$total_count)*100);
            $nodf = (($data['0']['fm']/$total_count)*100);
            $nod = $data['0']['gc'];
          }
          ?>
          <div class="col-lg-12 mb-2">
             <p class="mb-0 f-w-600">Yes, I have a disability (or previously had a disability)</p>
          </div>
          <div class="col-lg-3">
            <div class="bg-gray p-4">
              <p class="mb-0"> Total : <?php echo $data['0']['ma']+$data['0']['fm']; ?> </p>
              <p class="mb-0"> Male : <?php echo $data['0']['ma']; ?> Female :  <?php echo $data['0']['fm']; ?> </p>
            </div>
          </div>
          <div class="col-lg-9">
            <div class="bar multi-color">
              <?php if($nodm!=0){ ?>
                <div class="color bg-success" style="width: <?php echo $nodm; ?>%;background-color:#82bac7 !important; "></div>
              <?php } ?>
              <?php if($nodf!=0){ ?>
                <div class="color bg-success" style="width: <?php echo $nodf; ?>%;background-color:#cf9eb5 !important; "> </div>
              <?php } ?>
            </div>
          </div>
          <?php
          if($data['3']['gc']==""){
            $nod = 0;
          }else{
            $nodm = (($data['3']['ma']/$total_count)*100);
            $nodf = (($data['3']['fm']/$total_count)*100);
            $nod = $data['3']['gc'];
          }
          ?>

          <div class="col-lg-12 mb-2">
             <p class="mb-0 f-w-600">Decline to answer</p>
          </div>
          <div class="col-lg-3">
            <div class="bg-gray p-4">
              <p class="mb-0"> Total : <?php echo $data['3']['ma']+$data['3']['fm']; ?> </p>
              <p class="mb-0"> Male : <?php echo $data['3']['ma']; ?> Female :  <?php echo $data['3']['fm']; ?> </p>
            </div>
          </div>
          <div class="col-lg-9">
            <div class="bar multi-color">
              <?php if($nodm!=0){ ?>
                <div class="color bg-success" style="width: <?php echo $nodm; ?>%;background-color:#82bac7 !important; "></div>
              <?php } ?>
              <?php if($nodf!=0){ ?>
                <div class="color bg-success" style="width: <?php echo $nodf; ?>%;background-color:#cf9eb5 !important; "> </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="bg-gray">
          <h4 class="mt-2">
            <?php
              $male_c = $data['0']['ma']+$data['1']['ma']+$data['3']['ma'];
              $female_c = $data['0']['fm']+$data['1']['fm']+$data['3']['fm'];
            echo date('M d, Y', strtotime($fromdate))." - " .date('M d, Y', strtotime($todate)); ?>
          </h4>
          <h5 class="mt-3">Total Job Candidates <span class="f-30 f-w-600"><?php echo $total_count; ?></span></h5>
          <?php if($total_count == 0){ ?>
            <div class="d-block text-center total-job">
              <span class="text-muted"><i class="fa fa-venus-mars d-block f-24"></i> Gender</span>
            </div>
          <?php }else{ ?>
              <div class="m-t-20" style="text-align: -webkit-center;">
                <div class="circle_gradiant" style="--p1: <?php echo round(($male_c/$total_count)*100)*3.6; ?>deg; --p1_color: #82bac7;--p2_color: #cf9eb5; width: 150px; height: 150px;">
                </div>
              </div>
          <?php } ?>
         <p class="mt-4 text-center"> Male : <?php echo $male_c !=""?$male_c:"0"; ?> Female :  <?php echo $female_c !=""?$female_c:"0"; ?> </p>
          <p class="mt-4 text-center">Only jobs with an AA/EEO Job Category can be included in this report.</p>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover">
        <tr style="background-color: #eeeeee;">
          <th scope="col">Disabilities</th>
          <th scope="col">Male</th>
          <th scope="col">Female</th>
          <th scope="col">Count</th>
          <th scope="col">Hired Count</th>
        </tr>
        <?php
          $csv = array();
          $csv[] = array('Disabilities'=>'No I dont have a disability','Male'=>$data['1']['ma'],'Female'=>$data['1']['ma'],'Count'=>$data['1']['gc'],'Hired Count'=>$data['1']['mahc']+$data['1']['fmhc']);
          $csv[] = array('Disabilities'=>'Yes I have a disability','Male'=>$data['0']['ma'],'Female'=>$data['0']['fm'],'Count'=>$data['0']['gc'],'Hired Count'=>$data['0']['mahc']+$data['0']['fmhc']);
          $csv[] = array('Disabilities'=>'Decline to answer','Male'=>$data['3']['ma'],'Female'=>$data['3']['fm'],'Count'=>$data['3']['gc'],'Hired Count'=>$data['3']['mahc']+$data['3']['fmhc']);
          $_SESSION['download_data'] = $csv;
        ?>
        <?php if(count((array)$data)>0){?>
          <?php if($data['1']['gc']!=0){ ?>
            <tr style="background-color: #eeeeee;">
              <th scope="col">No, I don't have a disability</th>
              <th scope="col"><?php echo $data['1']['ma']; ?></th>
              <th scope="col"><?php echo $data['1']['fm']; ?></th>
              <th scope="col"><?php echo $data['1']['gc']; ?></th>
              <th scope="col"><?php echo $data['1']['mahc']+$data['1']['fmhc']; ?></th>
            </tr>
          <?php } ?>
          <?php if($data['0']['gc']!=0){ ?>
            <tr style="background-color: #eeeeee;">
              <th scope="col">Yes, I have a disability</th>
              <th scope="col"><?php echo $data['0']['ma']; ?></th>
              <th scope="col"><?php echo $data['0']['fm']; ?></th>
              <th scope="col"><?php echo $data['0']['gc']; ?></th>
              <th scope="col"><?php echo $data['0']['mahc']+$data['0']['fmhc']; ?></th>
            </tr>
          <?php } ?>
          <?php if($data['3']['gc']!=0){ ?>
            <tr style="background-color: #eeeeee;">
              <th scope="col">Decline to answer</th>
              <th scope="col"><?php echo $data['3']['ma']; ?></th>
              <th scope="col"><?php echo $data['3']['fm']; ?></th>
              <th scope="col"><?php echo $data['3']['gc']; ?></th>
              <th scope="col"><?php echo $data['3']['mahc']+$data['3']['fmhc']; ?></th>
            </tr>
          <?php } ?>
        <?php }else{ ?>
          <tr>
            <td colspan="5">

              <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Candidates</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
            </td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>
