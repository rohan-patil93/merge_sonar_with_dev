<?php
$url = API."?mode=getassetverifications&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
$data_details = callAPI($url);
$result = json_decode($data_details,1);
$asset_verifications = $result['asset_verifications'];
foreach($result['locations'] as $locations_list){
    $locations_lists[$locations_list['id']] = $locations_list['location_name'];
}
$avcnt = $result['asset_verifications_cnt'];
?>
<script type="text/javascript">
    function assetVerification(verification_id){
        jQuery.ajax({
            url: "views/custom_view_asset_verifications_popup.php",
            data: "verification_id="+verification_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            }
        });
    }
    function getVerifyList(type){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="<?= htmlspecialchars(__('Preview'), ENT_QUOTES, 'UTF-8'); ?>"></div>';
        $("#htmlview").html(loader);
        jQuery.ajax({
            url: "views/custom_view_asset_verifications_table.php",
            data: "ajax=Y&type="+type,
            type: "POST",
            success:function(data){
                $('#htmlview').html(data);
            }
        });
    }

</script>
<div class="row">
    <div class="col-xl-3 col-md-6">
        <button type="button" class="btn p-0 border-0 bg-transparent text-start w-100 card ticket-card" onclick="getVerifyList('ALL','')">
            <div class="card-body">
                <p class="bg-c-red lbl-card"><i class="fas fa-folder-open"></i> <?= __('Total Verifications'); ?></p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $avcnt['total'] !=""?$avcnt['total']:0; ?></h2>
                </div>
            </div>
        </button>
    </div>
    <div class="col-xl-3 col-md-6">
        <button type="button" class="btn p-0 border-0 bg-transparent text-start w-100 card ticket-card" onclick="getVerifyList('IP')">
            <div class="card-body">
                <p class="bg-c-blue lbl-card"><i class="fas fa-file-archive"></i> <?= __('In-process'); ?></p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-blue"><?php echo $avcnt['inprocess_cnt'] !=""?$avcnt['inprocess_cnt']:0; ?></h2>
                </div>
            </div>
        </button>
    </div>
      <div class="col-xl-3 col-md-6">
        <button type="button" class="btn p-0 border-0 bg-transparent text-start w-100 card ticket-card" onclick="getVerifyList('UCV')">
            <div class="card-body">
                <p class="bg-c-yellow lbl-card"><i class="fas fa-database"></i> <?= __('Upcoming Schedule Verifications'); ?></p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-yellow"><?php echo $avcnt['upcoming_cnt'] !=""?$avcnt['upcoming_cnt']:0; ?></h2>
                </div>
            </div>
        </button>
    </div>
    <div class="col-xl-3 col-md-6">
        <button type="button" class="btn p-0 border-0 bg-transparent text-start w-100 card ticket-card" onclick="getVerifyList('COMPL')">
            <div class="card-body">
                <p class="bg-c-green lbl-card"><i class="fas fa-users"></i> <?= __('Completed Verifications'); ?></p>
                <div class="text-center">
                    <h2 class="m-b-0 d-inline-block text-c-green"><?php echo $avcnt['completed'] !=""?$avcnt['completed']:0; ?></h2>
                </div>
            </div>
        </button>
    </div>
</div>
<div class="row" id="htmlview">
    <div class="col-lg-12">
        <?php include_once "custom_view_asset_verifications_table.php"; ?>
    </div>
</div>
