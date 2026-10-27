<?php
$url1 = API."?mode=getesopadmindashboard&result_type=USER";
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$esop_allocations = $result['esop_allocations'];
$esop_allocationsapproved =$esop_allocationspendding = array();
foreach($esop_allocations as $esopallocations){
    if($esopallocations['current_status'] == ST_APPROVED){
        $esop_allocationsapproved[] = $esopallocations;
    }else{
        $esop_allocationspendding[] = $esopallocations;
    }
}
$esopallocationscnt = $result['esop_allocations_cnt'];
$esop_plans = $result['esop_plans'];
$esop_plan_buckets = $result['esop_plan_buckets'];
$esop_vesting_schedule_cnt = $result['esop_vesting_schedule_cnt'];

?>
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-30 bg-c-green lbl-card"><i class="fas fa-folder-open"></i> Total Allocation Share</p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation(round($esop_vesting_schedule_cnt['allocated_share'])); ?></h2>
                </div>
            </div>
        </div>
    </div>
   <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-30 bg-c-yellow lbl-card"><i class="fas fa-folder-open"></i> Total ESOP</p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($esopallocationscnt['total_cnt']); ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-30 bg-c-red lbl-card"><i class="fas fa-folder-open"></i> ESOP Pending
</p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $esopallocationscnt['pendding_cnt'] !=""?$esopallocationscnt['pendding_cnt']:0; ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-30 bg-c-blue lbl-card"><i class="fas fa-folder-open"></i> ESOP Approved</p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $esopallocationscnt['approved_cnt']!=""?$esopallocationscnt['approved_cnt']:"0"; ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card" style="height:450px">
            <div class="card-header">
                <h5>Pedding ESOP</h5>
                <div class="card-header-right table-card-header"></div>
            </div>
            <div class="card-body">
                <?php if(count((array)$esop_allocationspendding) != 0){ ?>
                 <table id="basic-btn" class="table table-striped table-bordered nowrap" >
                    <thead>
                        <tr>
                            <th scope="col">Esop</th>
                            <th scope="col">Bucket</th>
                            <th scope="col" class="text-right">Annual CTC</th>
                            <th scope="col" class="text-right">Total Benefit</th>
                            <th scope="col" class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($esop_allocationspendding as $esopa){?>
                         <tr>
                            <td><a class="text-primary" href="index.php?option=COM_INSERT_ESOP_ALLOCATION_APPROVALS&Rid=<?php echo deocdeId('id:'.$esopa['id']); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View"><?php echo $esop_plans[$esopa['esop_plan_id']]; ?></a></td>
                            <td><?php echo $esop_plan_buckets[$esopa['esop_bucket_id']]; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformateifine($esopa['annual_ctc']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformateifine($esopa['total_expected_benefit']); ?></td>
                            <td class="text-center"><?php echo $esopa['benefit_ctc']."%"; ?></td>
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
    <div class="col-lg-5">
        <div class="card" style="height:450px">
            <div class="card-header">
                <h5>Approved ESOP</h5>
                <div class="card-header-right table-card-header"></div>
            </div>
            <div class="card-body">
                <?php if(count((array)$esop_allocationsapproved) != 0){ ?>
                <table id="basic-btn_2" class="table table-striped table-bordered nowrap" >
                    <thead>
                        <tr>
                            <th scope="col">Esop</th>
                            <th scope="col">Bucket</th>
                            <th scope="col" class="text-right">Annual CTC</th>
                            <th scope="col" class="text-right">Total Benefit</th>
                            <th scope="col" class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($esop_allocationsapproved as $esopa){?>
                         <tr>
                            <td><a class="text-primary" href="index.php?option=COM_INSERT_ESOP_ALLOCATION_APPROVALS&Rid=<?php echo deocdeId('id:'.$esopa['id']); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View"><?php echo $esop_plans[$esopa['esop_plan_id']]; ?></a></td>
                            <td><?php echo $esop_plan_buckets[$esopa['esop_bucket_id']]; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformateifine($esopa['annual_ctc']); ?></td>
                            <td class="text-right"><?php echo customeummarmonyformateifine($esopa['total_expected_benefit']); ?></td>
                            <td class="text-center"><?php echo $esopa['benefit_ctc']."%"; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Approved ESOP</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="card">
          <div class="card-body p-4">
            <div class="row mt-3">
              <div class="text-center">
                <div class="s-caption  text-info"><i class="fas fa-database  f-20"></i></div>
                <div class="s-cont d-inline-block">
                  <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($esop_vesting_schedule_cnt['total_share']); ?></h5>
                  <p class="m-b-0  f-12">Total Share</p>
                </div>
              </div>
              <div class="text-center mt-3">
                <div class="s-caption  text-info"><i class="fas fa-database  f-20"></i></div>
                <div class="s-cont d-inline-block">
                  <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($esop_vesting_schedule_cnt['un_allocated_share']); ?></h5>
                  <p class="m-b-0  f-12">Un-Allocated Share</p>
                </div>
              </div>
              <div class="text-center mt-3">
                <div class="s-caption  text-info"><i class="fas fa-money-bill-alt text-c-red f-20"></i></div>
                <div class="s-cont d-inline-block">
                  <h5 class="f-w-700 m-b-0"><?php echo customeummarmonyformateifine($esop_vesting_schedule_cnt['total_ammount']); ?></h5>
                  <p class="m-b-0  f-12">Total Ammount</p>
                </div>
              </div>
              <div class="text-center mt-3">
                <div class="s-caption  text-info"><i class="fas fa-money-bill-alt text-c-red f-20"></i></div>
                <div class="s-cont d-inline-block">
                  <h5 class="f-w-700 m-b-0"><?php echo customeummarmonyformateifine($esop_vesting_schedule_cnt['allocated_ammount']); ?></h5>
                  <p class="m-b-0  f-12">Allocated Ammount</p>
                </div>
              </div>
              <div class="text-center mt-3">
                <div class="s-caption  text-info"><i class="fas fa-money-bill-alt text-c-red f-20"></i></div>
                <div class="s-cont d-inline-block">
                  <h5 class="f-w-700 m-b-0"><?php echo customeummarmonyformateifine($esop_vesting_schedule_cnt['un_allocated_ammount']); ?></h5>
                  <p class="m-b-0  f-12">Un-Allocated Ammount</p>
                </div>
              </div>
            </div>
            </div>
    </div>
</div>
