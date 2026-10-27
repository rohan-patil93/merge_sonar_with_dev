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
$url = API."?mode=togetcandidatebyracegender&post_id=".$postids."&fromdate=".$fromdate."&todate=".$todate;
$apires = callAPI($url);
$result = json_decode($apires,1);
$job_postings = $result['job_postings'];
$races = $result['races'];

foreach($result['data'] as $candida){
  $data[$candida['race_id']]= $candida;
  $total_male = $total_male + $candida['ma'];
  $total_female =  $total_female+$candida['fm'];
  $total_count = $total_count+$candida['gc'];
  if($candida['race_id']=='0' && $candida['gc'] >0){
      $races[] = array('id'=>'0','race_name'=>'Not Answered');
  }
}
foreach($races as $rac){
  $race_name[$rac['id']] = $rac['race_name'];
}
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Candidates by Race and Gender</h5>
      <div class="card-header-right">
       <div class="dropdown-primary dropdown open">
        <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
        <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

         <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate by Race and Gender" target="_blank" >CSV</a>
         <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate by Race and Gender" target="_blank">Excel</a>
       </div>
     </div>
    </div>
  </div>
    <?php include_once 'custom_filter_report_topbar.php'; ?>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-lg-9">
           <div class="row mt-3 col-lg-12">
            <?php foreach($races as $listdata){
              $valm =  $data[$listdata['id']]['ma']!="" ? $data[$listdata['id']]['ma'] : 0;
              $valf =  $data[$listdata['id']]['fm']!="" ? $data[$listdata['id']]['fm'] : 0;
              if($total_count !=0 && $total_count !=""){
                $total_count1 = 1;
              }else{
                $total_count1 = $total_count;
              }
              $nodm = (($valm/$total_count1)*100);
              $nodf = (($valf/$total_count1)*100);
              $nod =  $data[$listdata['id']]['gc']!="" ? $data[$listdata['id']]['gc'] : 0;
             ?>
                <div class="col-lg-12 f-w-600">
                  <p class="mb-0 f-w-600"><?php echo $listdata['race_name']; ?></p>
                </div>
                <div class="col-lg-3">
                  <div class="bg-gray p-4">
                    <p class="mb-0"> Total : <?php echo $nod; ?> </p>
                    <p class="mb-0"> Male : <?php echo $valm; ?> Female :  <?php echo $valf; ?> </p>
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
            <?php } ?>

          </div>
        </div>
        <div class="col-lg-3">
          <div class="bg-gray">
            <h4 class="mt-2">
              <?php echo date('M d, Y', strtotime($fromdate))." - " .date('M d, Y', strtotime($todate)); ?>
            </h4>
            <h5 class="mt-3">Total Job Candidates
              <span class="f-30 f-w-600"><?php echo $total_count; ?></span>
            </h5>
            <?php if($total_count == 0){ ?>
              <div class="d-block text-center total-job">
                <span class="text-muted"><i class="fa fa-venus-mars d-block f-24"></i> Gender</span>
              </div>
            <?php }else{ ?>
              <div class="m-t-20" style="text-align: -webkit-center;">
                <div class="circle_gradiant" style="--p1: <?php echo round(($total_male/$total_count1)*100)*3.6; ?>deg; --p1_color: #82bac7;--p2_color: #cf9eb5; width: 150px; height: 150px;">
                </div>
              </div>
            <?php } ?>
            <p class="mt-4 text-center"> Male : <?php echo $total_male !=""?$total_male:"0"; ?> Female :  <?php echo $total_female !=""?$total_female:"0"; ?> </p>
            <p class="mt-4 text-center">Only jobs with an AA/EEO Job Category can be included in this report.</p>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">Race</th>
            <th scope="col">Male</th>
            <th scope="col">Female</th>
            <th scope="col">Count</th>
            <th scope="col">Hired Count</th>
          </tr>
           <?php if($total_count>0){ $csv = array();
              foreach($result['data'] as $rdata){
                $csv[] = array("Race"=>$race_name[$rdata['race_id']],"Male"=>$rdata['ma'],"Female"=>$rdata['fm'],"Count"=>$rdata['gc'],"Hired Count"=>$rdata['mahc']+$data['fmhc']);
            ?>
              <tr style="background-color: #eeeeee;">
              <th scope="col"><?php echo $race_name[$rdata['race_id']]; ?></th>
              <th scope="col"><?php echo $rdata['ma']; ?></th>
              <th scope="col"><?php echo $rdata['fm']; ?></th>
              <th scope="col"><?php echo $rdata['gc']; ?></th>
              <th scope="col"><?php echo $rdata['mahc']+$data['fmhc']; ?></th>
            </tr>

          <?php } ?>
          <?php } else { ?>
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
        <?php }
        $_SESSION['download_data'] = $csv;
         ?>
        </table>
      </div>
    </div>
  </div>
</div>
