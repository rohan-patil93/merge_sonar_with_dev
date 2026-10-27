<?php

$url = API."?mode=getjoboverview&id=".encodeId($_GET['Rid']);
$response = callAPI($url);
$list = json_decode($response,1);
function cal_percentage($total, $number) {
    if($total !=0 && $total!=""){
          $value = round(($number * 100) / $total, 2);
    }else{
        $value = round($number * 100, 2);
    }
  if(is_nan($value)){
          return 0;
  }else{
      return $value;
  }
}
$color = array("bg-info","bg-primary",'bg-warning','bg-danger','bg-dark',"bg-info","bg-primary",'bg-warning',);
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
        <div class="card-header">
            <div class="card-title">
        <span class="kt-portlet__head-icon">
            <i class="kt-font-brand flaticon2-line-chart"></i>
        </span>
            <h3 class="card-label">&nbsp;<?php echo "Candidate Funnel";  ?> </h3>

        </div>
        <div class="card-toolbar">
                        &nbsp;
                                                <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            Back
                        </a>

                    </div>
        </div>
        <div class="card card-custom">
            <div class="card-body">

<div class="card-label"></div>
<div class="col-lg-12 row mt-5 navi-item">

<div class="d-flex align-items-center pb-9 mr-15">
<div class="symbol symbol-45 symbol-light mr-4">
    <span class="symbol-label">
        <i class="icon-2x text-dark-50 flaticon2-list-3"></i>
    </span>
</div>
<div class="d-flex flex-column flex-grow-1">

    <span class="font-weight-bold text-primary"><?php echo $list['title']; ?></span>
</div>
</div>
<div class="d-flex align-items-center pb-9 mr-5">
<div class="symbol symbol-45 symbol-light mr-4">
    <span class="symbol-label">
        <i class="icon-2x text-dark-50 flaticon-users"></i>
    </span>
</div>
<div class="d-flex flex-column flex-grow-1">
    <span  class="text-dark-75 text-hover-primary mb-1 font-size-lg font-weight-bolder"><?php echo $list['cntcandidate']; ?></span>
    <span class="font-weight-bold text-primary">Total Candidates</span>
</div>
</div>

        </div>

<div class="col-lg-12 row mt-5">
            <span  class="col-form-label col-lg-2">Total Candidates</span>
            <div class="col-lg-7">
                <div class="progress" style="height: 40px;">
                    <div class="progress-bar progress-bar-animated <?php echo $color['1'];?>" role="progressbar" style="width: <?php echo cal_percentage($list['cntcandidate'],$list['cntcandidate']); ?>%;     text-align: start;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"><p class="font-size-lg mt-4">&nbsp;&nbsp;<i class=" flaticon-users-1"></i> <?php echo $list['cntcandidate']." (".cal_percentage($list['cntcandidate'],$list['cntcandidate'])."%)"; ?> </p></div>
                </div>
            </div>
        </div>
<div class="col-lg-12 row mt-5">
            <span  class="col-form-label col-lg-2">Short List Candidates</span>
            <div class="col-lg-7">
                <div class="progress" style="height: 40px; ">
                    <div class="progress-bar  progress-bar-animated <?php echo $color['2'];?>" role="progressbar" style="width: <?php echo cal_percentage($list['cntcandidate'],$list['shortlisted']); ?>%; text-align: start;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
<p class="font-size-lg mt-4">&nbsp;&nbsp;<i class=" flaticon-users-1"></i> <?php echo $list['shortlisted']." (".cal_percentage($list['cntcandidate'],$list['shortlisted'])."%)"; ?> </p>

                    </div>
                </div>
            </div>
        </div>

<?php $mm=1; $colr =3;  foreach(array_reverse($list['list']) as $lists){ ?>
        <div class="col-lg-12 row mt-5">
            <span  class="col-form-label col-lg-2"><?php echo $lists['name']; ?></span>
            <div class="col-lg-7">
                <div class="progress" style="height: 40px;">
                    <div class="progress-bar  progress-bar-animated <?php  echo count((array)$list['list'])==$mm?$color['6']:$color[$colr]; ?>" role="progressbar" style="width: <?php echo cal_percentage($list['cntcandidate'],$lists['cnt']); ?>%;text-align: start;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                        <p class="font-size-lg mt-4">&nbsp;&nbsp;<i class=" flaticon-users-1"></i> <?php echo $lists['cnt']." (".cal_percentage($list['cntcandidate'],$lists['cnt'])."%)"; ?> </p>
                    </div>
                </div>
            </div>
        </div>
    <?php  $mm++; $colr++;
} ?>

        <div class="col-lg-12 row mt-5">
            <span  class="col-form-label col-lg-2">Hire Candidates</span>
            <div class="col-lg-7">
                <div class="progress" style="height: 40px;">
                    <div class="progress-bar  progress-bar-animated bg-success" role="progressbar" style="width: <?php echo cal_percentage($list['cntcandidate'],$list['aprovecnt']); ?>%; text-align: start;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
<p class="font-size-lg mt-4">&nbsp;&nbsp;<i class=" flaticon-users-1"></i> <?php echo $list['aprovecnt']." (".cal_percentage($list['cntcandidate'],$list['aprovecnt'])."%)"; ?> </p>
                    </div>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
    </div>
</div>
