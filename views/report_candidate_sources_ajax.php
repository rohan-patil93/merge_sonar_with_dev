<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rgar = json_decode(urldecode($_POST['reason_data']),1);
 $checkvalue =0;
$ckgo = explode("||",$rgar[$_POST['reason_id']]);
foreach($ckgo as $ckg){
  $ckn = explode(":",$ckg);
  if($ckn['1']>0){

    $checkvalue = $ckn['1'];
    break;
  }
}
?>
  <?php if($checkvalue>0){ ?>
  <input type="hidden" name="" id="dchart<?php echo $_POST['disid']; ?>" value="<?php echo $rgar[$_POST['reason_id']]; ?>">
  <div class="card-block">
      <div id="chart_Donut_hiring_recejtion_<?php echo $_POST['disid']; ?>" style="width: 100%; height: 300px;"></div>
  </div>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js" integrity="sha384-Q4nTc23a1YNtnl17XDjJkYn/j5Ksb7rsGG1NTcIxbz6sTGfGXZJ8WdvzALeeuafr" crossorigin="anonymous"></script>
  <script type="text/javascript" src="assets/pages/chart/google/js/custom_report_candidate_sourcs.js"></script>
  <?php }else{?>
    <div class="card-px text-center py-20 m-t-30">
      <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Data Found </p>
      <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
      </div>
    </div>
  <?php } ?>
