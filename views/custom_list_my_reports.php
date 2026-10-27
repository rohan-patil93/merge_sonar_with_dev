 <?php
$url = API."?mode=togetmyreportswithdetails&module_list=0,".$_SESSION['work_module_setup_id'];
$apires = callAPI($url);
$result = json_decode($apires,1);
foreach($result['reportdata'] as $freport){
  if($freport['parent_id']==0){
    $step_report[$freport['id']] = $freport;
  }else{
     $step_report[$freport['parent_id']]['data'][]= $freport;
  }
  $list[$freport['id']] = $freport;
}
?>
 <div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="text-primary"><i class="ti-pie-chart m-r-10"></i>Standard Reports</h5>
      <div class="card-header-right table-card-header">
          <div class="input-group input-group-button mb-0">
              <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
              <span class="input-group-text addon-style">
                  <span class=""><span class="ti-search text-white mt-2"></span></span>
              </span>
          </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover" id="categoryfilter_2">
          <tr>
            <th scope="col"><i class="ti-time m-r-10 text-primary f-w-600 f-18"></i> Recent</th>
            <th scope="col">Last Viewed</th>
          </tr>
          <?php foreach($result['view'] as $keyview=>$review){ $rece =$list[$keyview];
                  if(in_array($rece['module_id'], $_SESSION['module_list']) || $rece['module_id'] =='0'){
          ?>
            <tr>
              <td style="width:70%;"><i class="<?php echo $rece['report_icon']; ?> m-r-10 f-w-600 f-18 text-muted"></i> <a class="text-primary" href="index.php?option=<?php echo $rece['page_url']; ?>"><?php echo $rece['page_name']; ?></a></td>
            <td style="width:30%;"> <?php echo date("M d", strtotime($review)); ?></td>
          </tr>
          <?php } ?>
          <?php } ?>
        </table>
      </div>

      <?php foreach($step_report as $step){
        if($step['report_id'] !=""){ ?>
          <div class="table-responsive">
            <table class="table table-hover" id="categoryfilter_3">
              <tr>
                <th scope="col" style="width:70%;"><i class="<?php echo $sub['report_icon']; ?> m-r-10 f-w-600 f-18 text-muted"></i> <a class="text-primary" href="index.php?option=COM_VIEW_MY_REPORT&Rid=<?php echo deocdeId("id:".$step['report_id']); ?>"><?php echo $step['page_name']; ?></a></th>
                <th scope="col" style="width:30%;">
                  <?php if(isset($result['view'][$step['id']]) && $result['view'][$step['id']]!=""){
                    echo date("M d", strtotime($result['view'][$step['id']]));
                  }
                ?></th>
              </tr>
            </table>
          </div>

      <?php }else{
        ?>
      <div class="table-responsive">
        <table class="table table-hover" id="categoryfilter_4">
          <tr>
            <th scope="col" style="width:70%;"><i class="<?php echo $step['report_icon']; ?> m-r-10 text-primary f-w-600 f-18"></i> <?php echo $step['page_name']; ?></th>
            <th scope="col" style="width:30%;"></th>
          </tr>
          <?php foreach($step['data'] as $sub){ ?>
          <tr>
            <td style="width:70%;"><i class="<?php echo $sub['report_icon']; ?> m-r-10 f-w-600 f-18 text-muted"></i> <a class="text-primary" href="index.php?option=<?php echo $sub['page_url']; ?>&RID=<?php echo encodeId($sub['id']).$sub['id']; ?>"><?php echo $sub['page_name']; ?></a></td>
            <td style="width:30%;">
              <?php if(isset($result['view'][$sub['id']]) && $result['view'][$sub['id']]!=""){
                echo date("M d", strtotime($result['view'][$sub['id']]));
              }
            ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>
      <?php } ?>
      <?php } ?>
    </div>
  </div>
</div>
