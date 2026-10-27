<?php
if(isset($_POST['esop_id']) && $_POST['esop_id'] !=""){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url1 = API."?mode=getesopdetails&esop_id=".$_POST['esop_id'];
    $data_details1 = callAPI($url1);
    $result =  json_decode($data_details1,1);
    $esop_plans = $result['esop_plans'];
    $esop_plans_details = $result['esop_plans_details'];
    $esop_plan_buckets = $result['esop_plan_buckets'];
    $esop_allocations_cnt = $result['esop_allocations_cnt'];
    $esop_allocations = $result['esop_allocations'];
    $esopplanbucketsname = array();
    foreach($esop_plan_buckets as $epb){
        $esopplanbucketsname[$epb['id']] = $epb['bucket_name'];
    }
}
?>
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-green lbl-card"><i class="fas fa-folder-open"></i>  Total ESOP Allocations</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-green"><?php echo $esop_allocations_cnt['cnt']; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-blue lbl-card"><i class="fas fa-folder-open"></i>  Total Shares</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-blue"><?php echo $esop_allocations_cnt['total_share']; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-yellow lbl-card"><i class="fas fa-folder-open"></i> Allocation Shares</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-yellow"><?php echo $esop_allocations_cnt['total_allocated_shares']; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-red lbl-card"><i class="fas fa-folder-open"></i>  Un-allocation Shares</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-red"><?php echo $esop_allocations_cnt['total_unallocated_shares']; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-5">
        <div class="card social-res-card">
            <div class="card-header">
                <h5>Buckets</h5>
            </div>
            <div class="card-body">
                <div class="card-block accordion-block color-accordion-block">
                    <div class="color-accordion" id="color-accordion">
                        <?php foreach($esop_plan_buckets as $epb){
                            if($epb['options_granted'] != 0 && $epb['options_granted'] !=""){
                                $allocateper = round(($epb['allocated_shares']/$epb['options_granted'])*100);
                            }else{
                                $allocateper = 0;
                            }
                            $vestingschedule = json_decode($epb['vesting_schedule'],1);
                            ?>
                            <a class="accordion-msg b-none waves-effect waves-light p-2">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <p class="m-b-0 f-w-700"><?php echo $epb['bucket_name']; ?> </p>
                                        <h7><?php echo $epb['options_granted']; ?> <span class="f-12">(Allocated Shares - <?php echo $epb['allocated_shares']; ?>)</span></h7>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="text-right mb-0">
                                            <span class="text-c-green f-w-700"><?php echo $allocateper; ?>%</span></p>
                                            <div class="progress m-b-30">
                                                <div class="progress-bar bg-facebook" style="width:<?php echo $allocateper; ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="accordion-desc p-0">
                                    <div class="card-header">
                                        <h5>Vesting Schedule</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row f-w-700 mb-2">
                                            <div class="col-lg-5">
                                                <span>Allocation Date</span>
                                            </div>
                                            <div class="col-lg-3 text-right">
                                                <span>Per(%)</span>
                                            </div>
                                            <div class="col-lg-4">
                                                <span>Till Date</span>
                                            </div>
                                        </div>
                                        <?php foreach($vestingschedule as $vp){ ?>
                                            <div class="row">
                                                <div class="form-group col-lg-5">
                                                    <span><?php echo fromDate($vp['vesting_date']); ?></span>
                                                </div>
                                                <div class="form-group col-lg-3 text-right">
                                                    <span><?php echo $vp['vesting_percentage']; ?>%</span>
                                                </div>
                                                <div class="form-group col-lg-4">
                                                    <span><?php echo fromDate($vp['till_date']); ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card social-res-card">
                <div class="card-header">
                    <h5>ESOP Allocation</h5>
                </div>
                <div class="card-body">
                <?php if(count((array)$esop_allocations) != 0){ ?>
                    <div class="row mb-3 f-w-700">
                        <div class="col-lg-4">
                            <span>Employee</span>
                        </div>
                        <div class="col-lg-2">
                            <span>Bucket</span>
                        </div>
                        <div class="col-lg-2 text-right p-0">
                            <span>Total</span>
                        </div>
                        <div class="col-lg-2 text-right p-0">
                            Allocate

                        </div>
                        <div class="col-lg-2 text-right p-0">
                            Un-Allocate

                        </div>
                    </div>
                    <?php foreach($esop_allocations as $ea){ ?>
                        <a href="index.php?option=COM_INSERT_ESOP_ALLOCATION_APPROVALS&Rid=<?php echo deocdeId('id:'.$ea['id']); ?>" target="_blank">
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <div class="align-middle">
                                        <?php if($ea['image'] !="") {?>
                                            <img src="uploads/all/<?php echo $ea['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($ea['display_name']); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <span class="f-12 text-capitalize"><?php echo $ea['display_name']; ?></span>
                                            <p class="text-muted m-b-0 text-capitalize"><?php echo $ea['designation']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-2">
                                    <span><?php echo $esopplanbucketsname[$ea['esop_bucket_id']]; ?></span>
                                </div>
                                <div class="form-group col-lg-2 text-right">
                                    <span><?php echo $ea['option_granted']; ?></span>
                                </div>
                                <div class="form-group col-lg-2 text-right">
                                    <span><?php echo $ea['allocation_share']; ?></span>
                                </div>
                                <div class="form-group col-lg-2 text-right">
                                    <span><?php echo $ea['option_granted']-$ea['allocation_share']; ?></span>
                                </div>
                            </div> </a>
                        <?php } ?>
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
<?php if(isset($_POST['esop_id']) && $_POST['esop_id'] !=""){  ?>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
<?php } ?>
