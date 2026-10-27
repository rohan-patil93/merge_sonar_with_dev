<?php
    $url1 = API."?mode=getesopdetails&rid=".$rid;
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
    $totalshare = $allocatedshare = 0;
    foreach($esop_plans as $ep){
        $totalshare += $ep['share'];
    }
    foreach($esop_plans_details as $epbd){
        $allocatedshare += $epbd['allocated_shares'];
    }
?>
<script type="text/javascript">
    function getESOPAllocationDetails(esop_id){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#ajaxesopplansdetails").html(loader);
        $('.allcrd').removeClass('card-green')
        $('.allcrd').removeClass('text-white')

        $(".cardid_"+esop_id).addClass('card-green');
        $(".cardid_"+esop_id).addClass('text-white')
        jQuery.ajax({
                url: "views/custom_view_esop_allocations_ajax.php",
                data:'esop_id='+esop_id,
                type: "POST",
                success:function(response){
                    $("#ajaxesopplansdetails").html(response);
                }
            });

    }
</script>
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-green lbl-card"><i class="fas fa-folder-open"></i>  Total ESOP Plans</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-green"><?php echo count((array)$esop_plans); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-blue lbl-card"><i class="fas fa-folder-open"></i>  Total Shares</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-blue"><?php echo $totalshare; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-yellow lbl-card"><i class="fas fa-folder-open"></i> Allocation Shares</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-yellow"><?php echo $allocatedshare; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card ticket-card">
            <div class="card-body">
                <p class="m-b-10 bg-c-red lbl-card"><i class="fas fa-folder-open"></i>  Un-allocation Shares</p>
                <div class="text-center">
                    <h3 class="m-b-0 d-inline-block text-c-red"><?php echo $totalshare - $allocatedshare; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5>ESOP Plans</h5>
                <div class="card-header-right table-card-header  m-r-50">
                </div>
            </div>
            <div class="card-body">
            <?php $i=0; foreach($esop_plans as $esopplans){
                    $esop_plans_detail = $esop_plans_details[$esopplans['id']];
                    if($esopplans['share'] != 0 && $esopplans['share'] !=""){
                        $allocateper = round(($esop_plans_detail['allocated_shares']/$esopplans['share'])*100);
                    }else{
                        $allocateper = 0;
                    }

?>
                <div class="card  proj-progress-card allcrd cardid_<?php echo $esopplans['id']; ?> <?php echo $i=='0'?"card-green text-white":""; ?>" onclick="getESOPAllocationDetails('<?php echo $esopplans['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <div class="card-body ">
                        <h6><?php echo $esopplans['plan_name']; ?> <span class="form-label label label-md label-primary float-end">Buckets : <?php echo $esop_plans_detail['total_bucket_cnt']; ?></span></h6>
                        <h5 class="mt-3 mb-2 f-w-700"><?php echo $esopplans['share']; ?> <span class="f-12">(Allocated Shares - <?php echo $esop_plans_detail['allocated_shares']; ?>)</span><span class=" float-end"><?php echo $allocateper; ?>%</span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-c-red" style="width:<?php echo $allocateper; ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php $i++;
} ?>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body" id="ajaxesopplansdetails">
                <?php include_once "views/custom_view_esop_allocations_ajax.php"; ?>
            </div>
        </div>
    </div>
</div>
