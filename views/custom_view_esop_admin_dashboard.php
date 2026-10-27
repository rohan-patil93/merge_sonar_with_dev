<?php
$_SESSION['apply_js']['float'] =1;
$url1 = API."?mode=getesopadmindashboard&limit=4";
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$company_valuations = $result['company_valuations'];
$esop_plans = $result['esop_plans'];
$contribution_users_type = $result['contribution_users_type'];
$company_valuation_graph = $result['company_valuation_graph'];
$investergrpary = array();
$investertypeary = array('Month');
$lastinvestertypeary = array();
$usertype = array();
foreach($contribution_users_type as $cut){
  $investertypeary[] = $cut['genc_detail_name'];
  $lastinvestertypeary[] = $cut['genc_detail_name'];
  $usertype[$cut['genc_detail_code']] =$cut['genc_detail_name'];
}
$investergrpary[] = $investertypeary;
foreach($company_valuation_graph as $yr=>$vl){
  $investerary = array();
  $investerary[] = "$yr";
  foreach($contribution_users_type as $cut){
    $percentage = $vl[$cut['genc_detail_code']]['total_percentage'];
    $investerary[] = ($percentage!="" && $percentage!="0")?round($percentage):0;
  }
  $investergrpary[] = $investerary;
}
$total_users_with_type_cnt = $result['total_users_with_type_cnt'];
$esop_plans_cnt = $result['esop_plans_cnt'];

$last_company_valuations = $result['last_company_valuations'];
$last_company_valuation_distributions = $result['last_company_valuation_distributions'];

$last_company_valuation_distributions_ary[] = array('Type','Percentage');
foreach($last_company_valuation_distributions as $cvda){
  $last_company_valuation_distributions_ary[] = array($usertype[$cvda['user_type']],round($cvda['total_percentage']));
}

$esop_allocations_cnt = $result['esop_allocations_cnt'];
$company_valuations_trend = $result['company_valuations_trend'];
  $valuation_trend = $share_price_trend = array();
  $m =0;

if(count((array)$company_valuations_trend) !=0){
  foreach($company_valuations_trend as $cvt1){
    $share_price_trendmaxnumber[] = round($cvt1['per_share_value']);
    $valuation_trendmaxnumber[] = round($cvt1['company_value']);
  }

  $sptdivnumber = round(max((array)$share_price_trendmaxnumber)/100);
  $valuationtrendmaxnumbernumber = round(max((array)$valuation_trendmaxnumber)/100);
  $sptdivnumber  = $sptdivnumber !=0?$sptdivnumber:1;

  foreach($company_valuations_trend as $cvt){
      $valuation_trend[] = array($m,($cvt['company_value']/$valuationtrendmaxnumbernumber));
      $share_price_trend[] = array($m,($cvt['per_share_value']/$sptdivnumber));
      $m++;
  }
}

  $esoplanstrend = $result['esop_plans_trend'];
if(count((array)$esoplanstrend) !=0){
  foreach($esoplanstrend as $ept1){
      $esop_plans_trendmaxnumber[] = round($ept1['company_valuation']);
  }
  $eptdivnumber = max((array)$esop_plans_trendmaxnumber)/100;
  $m =0;
  foreach($esoplanstrend as $ept){
      $esop_plans_trend[] = array($m,round(($ept['company_valuation']/$eptdivnumber),2));
      $m++;
  }
}

$esop_allocations = $result['esop_allocations'];
function getNumberConvertName($val,$title){
   if(strlen($val) >= 8){
          return 'Crore';
  }elseif(strlen($val) >= 6){
      return 'lakh';  //millian
  }elseif(strlen($val) >= 4){
      return 'Thousand';
  }elseif(strlen($val) == 3){
      return 'hundred';
  }else{
    return $title;
  }
}
?>
<div class="row">

  <div class="col-lg-2">


    <div class="col-lg-12">
      <div class="card prod-p-card card-green">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="m-b-5 text-white">Last Valuation</h6>
              <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo $last_company_valuations['valuation_date'] !=""?date('M d-Y',strtotime($last_company_valuations['valuation_date'])):"-";?></h3>
            </div>
            <div class="col-auto">
              <i class="fa fa-calendar-check text-c-blue f-18"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="card prod-p-card card-yellow">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="m-b-5 text-white">Total Valuation</h6>
              <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo convert_Rupee_Number($last_company_valuations['company_value']);?></h3>
            </div>
            <div class="col-auto">
              <i class="fas fa-money-bill-alt text-c-red f-18"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="card prod-p-card card-blue">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="m-b-5 text-white">Total Shares</h6>
              <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo customNanAndEmptyValidation($last_company_valuations['total_share_qty']);?></h3>
            </div>
            <div class="col-auto">
              <i class="fas fa-database text-c-blue f-18"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="card prod-p-card card-red">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="m-b-5 text-white">Share Price</h6>
              <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo customNanAndEmptyValidation($last_company_valuations['per_share_value']);?></h3>
            </div>
            <div class="col-auto">
              <i class="fas fa-dollar-sign text-c-green f-18"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h5>Ownership</h5>
      </div>
      <div class="card-block">
        <input type="hidden" id="ownership_encode" value='<?php echo json_encode($last_company_valuation_distributions_ary); ?>'>
        <div id="ownership_chart" style="width: 100%; height: 300px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
      <div class="card card-yellow text-white">
        <div class="card-block p-2">
            <div class="row align-items-center">
                <div class="col-8">
                    <h6 class="m-b-0">Valuation Trend</h6>
                </div>
                <div class="col-4 text-end">
                    <i class="fas fa-bullhorn f-20"></i>
                </div>
            </div>
            <div class="p-2">

              <input type="hidden" id="valuation_trend_chart_type" value='<?php echo getNumberConvertName($valuationtrendmaxnumbernumber,'Valuation Trend'); ?>'>
              <input type="hidden" id="valuation_trend_chart_id" value='<?php echo json_encode($valuation_trend); ?>'>
             <div id="valuation_trend_chart" style="height:40px"></div>
            <?php if(count((array)$valuation_trend) ==0){ echo "<div class='p-0'>No Trends</div>";
} ?>


          </div>
        </div>
    </div>
  <div class="col-lg-12">
    <div class="card card-blue text-white">
      <div class="card-block p-2">
        <div class="row align-items-center">
          <div class="col-8">
            <h6 class="m-b-0">Share Price Trend</h6>
          </div>
          <div class="col-4 text-end">
            <i class="fas fa-dollar-sign f-20"></i>
          </div>
        </div>
        <div class="p-2">
          <input type="hidden" id="share_price_trends_chart_type" value='<?php echo getNumberConvertName($sptdivnumber,'Share Price Trends'); ?>'>
         <input type="hidden" id="share_price_trends_chart_id" value='<?php echo json_encode($share_price_trend); ?>'>
          <div id="share_price_trends_chart" style="height:40px"></div>
       <?php if(count((array)$share_price_trend) ==0){ echo "<div class='p-0'>No Trends</div>";
} ?>
      </div>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
    <div class="card card-red text-white">
      <div class="card-block p-2">
        <div class="row align-items-center">
          <div class="col-8">
            <h6 class="m-b-0">ESOP Trends</h6>
          </div>
          <div class="col-4 text-end">
            <i class="fas fa-user f-20"></i>
          </div>
        </div>
        <div class="p-2">
         <input type="hidden" id="esop_trends_chart_id" value='<?php echo json_encode($esop_plans_trend); ?>'>
         <input type="hidden" id="esop_trends_chart_type" value='<?php echo getNumberConvertName($share_price_trends_chart_type,'ESOP Trends'); ?>'>
         <div id="esop_trends_chart" style="height:40px"></div>
            <?php if(count((array)$esop_plans_trend) ==0){ echo "<div class='p-0'>No Trends</div>";
} ?>
      </div>
      </div>
    </div>
  </div>

  </div>

  <div class="col-lg-3">
     <div class="card">
      <div class="card-body p-4">
        <div class="row">
          <div class="col-6 text-center">
            <div class="s-caption  text-primary"><i class="ti-user f-20"></i></div>
            <div class="s-cont d-inline-block">
              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($total_users_with_type_cnt['founder_cnt']); ?></h5>
              <p class="m-b-0 f-12">Founders</p>
            </div>
          </div>
          <div class="col-6 b-l-default text-center">
            <div class="s-caption  text-danger"><i class="ti-crown f-20"></i></div>
            <div class="s-cont d-inline-block">
              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($total_users_with_type_cnt['invester_cnt']); ?></h5>
              <p class="m-b-0 f-12">Investers</p>
            </div>
          </div>
        </div>
      </div>
     </div>
     <div class="card">
      <div class="card-body p-4">
        <div class="row mt-3">
          <div class="col-6  text-center">
            <div class="s-caption  text-info"><i class="ti-id-badge f-20"></i></div>
            <div class="s-cont d-inline-block">
              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($total_users_with_type_cnt['employee_cnt']); ?></h5>
              <p class="m-b-0  f-12">Employees</p>
            </div>
          </div>
          <div class="col-6 b-l-default text-center">
            <div class="s-caption  text-info"><i class="ti-envelope f-20"></i></div>
            <div class="s-cont d-inline-block">
              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation(count((array)$esop_plans)); ?></h5>
              <p class="m-b-0  f-12">ESOP Plans</p>
            </div>
          </div>
        </div>
        <div class="row mt-5">
          <div class="col-6  text-center">
            <div class="s-caption  text-info"><i class="ti-check f-20"></i></div>
            <div class="s-cont d-inline-block">
              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($esop_allocations_cnt['approved_cnt']); ?></h5>
              <p class="m-b-0  f-12">Vested</p>
            </div>
          </div>
          <div class="col-6 b-l-default text-center">
            <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
            <div class="s-cont d-inline-block">
              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($esop_allocations_cnt['pendding_cnt']); ?></h5>
              <p class="m-b-0  f-12">Pending</p>
            </div>
          </div>
        </div>
        </div>
      </div>
  </div>

</div>
<div class="row">
    <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h5>Ownership Distribution</h5>
        <div class="float-right">
        </div>
      </div>
      <div class="card-block">
        <input type="hidden" id="distribution_graph" value='<?php echo json_encode($investergrpary); ?>'>
        <div id="chart_Combo" style="width: 100%; height: 300px;"></div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card" style="height:400px">
      <div class="card-header">
        <h5>Recent Valuations</h5>
        <div class="float-right">
           <a href="index.php?option=COM_LIST_COMPANY_VALUATIONS" class=" b-b-primary text-primary">View all</a>
        </div>
      </div>
      <div class="card-block " >
        <div class="table-responsive">
        <?php if(count((array)$company_valuations) != 0){ ?>
        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
          <thead>
            <tr>
              <th scope="col">VAL. Name</th>
              <th scope="col">VAL. Date</th>
              <th scope="col" class="text-right">Company Value</th>
              <th scope="col" class="text-right">New Shares</th>
              <th scope="col" class="text-right">Total Shares</th>
              <th scope="col" class="text-right">Share Price</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($company_valuations as $cval){?>
              <tr>
                <td><a class="text-primary" href="index.php?option=COM_INSERT_COMPANY_VALUATIONS&Rid=<?php echo deocdeId('id:'.$cval['id']); ?>"><?php echo $cval['valuation_name']; ?></a></td>
                <td><?php echo fromDate($cval['valuation_date']); ?></td>
                <td class="text-right"><?php echo customeummarmonyformateifine($cval['company_value']); ?></td>
                <td class="text-right"><?php echo $cval['new_share_qty']; ?></td>
                <td class="text-right"><?php echo $cval['total_share_qty']; ?></td>
                <td class="text-right"><?php echo customeummarmonyformateifine($cval['per_share_value']); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
         <?php }else{ ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Valuations</p>
                  <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 200px!important;">
                  </div>
              </div>
          <?php } ?>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="card" style="height:450px">
      <div class="card-header">
        <h5>ESOP Plans</h5>
        <div class="float-right">
           <a href="index.php?option=COM_LIST_ESOP_PLANS" class=" b-b-primary text-primary">View all</a>
        </div>
      </div>
      <div class="card-block" >
        <?php if(count((array)$esop_plans) != 0){ ?>
        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
          <thead>
            <tr>
              <th scope="col">Plan Name</th>
              <th scope="col">Date Of Grant</th>
              <th scope="col" class="text-right">Company Value</th>
              <th scope="col" class="text-right">Share</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($esop_plans as $cval){?>
              <tr>
                <td><a class="text-primary" href="index.php?option=COM_INSERT_ESOP_PLANS&Rid=<?php echo deocdeId('id:'.$cval['id']); ?>"><?php echo $cval['plan_name']; ?></a></td>
                <td><?php echo fromDate($cval['date_of_grant']); ?></td>
                <td class="text-right"><?php echo customeummarmonyformateifine($cval['company_valuation']); ?></td>
                <td class="text-right"><?php echo $cval['share']; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
         <?php }else{ ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No ESOP Plans</p>
                  <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
              </div>
          <?php } ?>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card" style="height:450px">
      <div class="card-header">
        <h5>ESOP Allocations</h5>
        <div class="float-right">
          <a href="index.php?option=COM_INSERT_ESOP_ALLOCATIONS" class=" b-b-primary text-primary">View all</a>
        </div>
      </div>
      <div class="card-block" >
        <?php if(count((array)$esop_allocations) != 0){ ?>
          <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_3">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Emp Code</th>
              <th scope="col" class="text-right">Joining Date</th>
              <th scope="col" class="text-right">Option Granted</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($esop_allocations as $cval){?>
              <tr>
                <td><a class="text-primary" href="index.php?option=COM_INSERT_ESOP_ALLOCATIONS&Rid=<?php echo deocdeId('id:'.$cval['id']); ?>"><?php echo $cval['emp_name']; ?></a></td>
                <td><?php echo $cval['emp_code']; ?></td>
                <td class="text-right"><?php echo fromDate($cval['date_of_joining']); ?></td>
                <td class="text-right"><?php echo $cval['option_granted']; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php }else{ ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No ESOP Allocations</p>
                  <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
              </div>
          <?php } ?>
      </div>
    </div>
  </div>
</div>
