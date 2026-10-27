<?php
$url = API."?mode=togetreleasedocumentlistdetails&emp_id=".$_SESSION['emp_id'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$row = $details['data'];
$formate = "d-m-Y";
$rid = $_GET['RID'];
?>
<script type="text/javascript">
  function openclospage(cat,id){
    var idd = cat+'_val_'+id;
    var val = $("#"+idd).val();
    if(val ==0){
      $('#'+cat+'_'+id).show();
      $("#"+idd).val(1);
    }else{
      $('#'+cat+'_'+id).hide();
      $("#"+idd).val(0);
    }

  }
</script>
<div class="card card-block email-card">
  <div class="card-header">
    <h5>Document List</h5>
    <div class="card-header-right">
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <!-- Left-side section start -->
      <div class="col-lg-12 col-xl-3 p-b-20">
        <div class="user-body">

          <ul class="page-list nav nav-tabs flex-column" id="pills-tab">
            <?php foreach($details['cat'] as $cat){
              $allcat[$cat['id']] = $cat['document_name'];
              $allcatdetail[$cat['id']] = $cat;
            ?>
            <li class="nav-item mail-section">
              <a class="nav-link waves-effect d-block " onclick="openclospage('cat', <?php echo $cat['id']; ?>)"  data-toggle="pill" href="#e-inbox" role="tab">
                <i class="icofont icofont-inbox"></i> <?php echo $cat['document_name']; ?>
                <span class="label label-primary float-end"><?php echo count((array)$row[$cat['id']]); ?></span>
              </a>
              <input type="hidden" name="" id="cat_val_<?php echo $cat['id']; ?>" value="0">

              <div class="p-l-20 p-r-20" id="cat_<?php echo $cat['id']; ?>" style="display:none;">
                <?php foreach($row[$cat['id']] as $alevkey=>$lev){ ?>
                <a  class="nav-link waves-effect d-block " onclick="openclospage('subcat', <?php echo "'".$alevkey."'"; ?>)"  data-toggle="pill" href="#e-inbox" role="tab">
                <i class="icofont icofont-inbox"></i> <?php echo $alevkey; ?>
                <span class="label label-warning float-end"><?php echo count((array)$lev); ?></span>
                </a>
                  <input type="hidden" name="" id="subcat_val_<?php echo $alevkey; ?>" value="0">
                  <div class="p-l-20 p-r-20"  id="subcat_<?php echo $alevkey; ?>" style="display:none;">
                    <?php foreach($lev as $min){
                            $mainlist[$min['id']] = $min;
                    ?>
                    <a target="_blank" class="nav-link waves-effect d-block " data-toggle="pill" href="index.php?option=<?php echo $cat['page']; ?>&RID=<?php echo $min['id']; ?>" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <i class="icofont icofont-inbox"></i> <?php echo $cat['document_name']." - ".$alevkey; ?>
                    </a>
                    <?php } ?>
                  </div>
                <?php } ?>
            </div>
            </li>

            <?php } ?>
          </ul>
        </div>
      </div>
      <!-- Left-side section end -->
      <!-- Right-side section start -->
      <div class="col-lg-12 col-xl-9">
        <?php
        if($rid!=""){
          if($mainlist[$rid]['file']!=""){ ?>
              <iframe src="uploads/all/<?php echo $mainlist[$rid]['file']; ?>" title="Document Preview"></iframe>
          <?php }else{
            include_once $mainlist[$rid]['page'].".php";
          }
        }else{
        ?>
        <div class="mail-body-content">
          <div class="table-responsive">
            <table class="table">
              <?php foreach($mainlist as $listmain){   ?>
              <tr class="<?php if($listmain['read_type']=="Y"){ echo "unread";
}else{ echo "read";
} ?> ">
                <th scope="col">
                  <div class="check-star mt-2">

                    <i class="icofont icofont-star text-warning"></i>
                  </div>
                </th>
                <th scope="col">
                  <?php
                  $view_mon = "";
                    $url = "index.php?option=".$allcatdetail[$listmain['document_list_id']]['page']."&RID=".$listmain['id'];
                    if($listmain['document_month']!="" && $listmain['document_month']!="0" ){
                        $exp = explode("-", $listmain['assessment_year'] );
                        $ass_year = $exp['0'];
                        if($listmain['document_month'] <4){
                          $ass_year = $exp['1'];
                        }
                        $view_mon = $ass_year." - ".date("F", mktime(0, 0, 0, $listmain['document_month'], 1));
                    }
                  ?>

                  <a target="_blank" href="index.php?option=<?php echo $allcatdetail[$listmain['document_list_id']]['page'];  ?>&RID=<?php echo $listmain['id']; ?>" class="email-name waves-effect"><?php echo $allcat[$listmain['document_list_id']]. " - ".$listmain['assessment_year'];  ?> &nbsp;&nbsp; <?php echo $view_mon; ?>  </a></th>
                <th scope="col" class="email-attch">
                  <?php if($listmain['file']!=""){ ?>
                  <a href="#"><i class="icofont icofont-clip"></i></a>
                  <?php } ?>
                </th>
                <th scope="col" class=""><?php echo date('d M Y',strtotime($listmain['send_date'])); ?></th>
              </tr>
            <?php } ?>
            </table>
          </div>
        </div>
        <?php } ?>
      </div>
      <!-- Right-side section end -->
    </div>
  </div>
</div>
