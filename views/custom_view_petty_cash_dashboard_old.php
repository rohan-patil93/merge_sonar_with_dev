<?php
$startdate = date('Y-m-01');
$enddate = date('Y-m-t');
$url1 = API."?mode=pettycashdashboard&startdate=".$startdate."&enddate=".$enddate;
$resultapi = callAPI($url1);
$result = json_decode($resultapi,1);
$location_pettycashbox = $result['location_pettycashbox'];
$locations = $result['locations'];
foreach ($locations as $locationvalue) {
    $locations_list[$locationvalue['id']] = $locationvalue['location_name'];
}
foreach ($location_pettycashbox as $location_pettycashboxs) {
    $location_pettycashboxlist[$location_pettycashboxs['location_id']]['pc_limit'] += $location_pettycashboxs['pc_limit'];
    $location_pettycashboxlist[$location_pettycashboxs['location_id']]['pc_balance'] += $location_pettycashboxs['pc_balance'];
}
?>
<script type="text/javascript">
    function getLocationTransaction(loc_id){
        $(".locationpettycash").html('');
        $("#locationpettycash_"+loc_id).html('<i class="fa fa-check-circle text-warning f-18"></i>');
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#htmlpage").html(loader);

        $.ajax({
            url: "views/custom_view_petty_cash_dashboard_ajax.php",
            data:'page=getlocationpettycashtransaction&loc_id='+loc_id,
            type: "POST",
            success:function(data){
                $("#htmlpage").html(data);
                console.log(data);
             },
            error:function (){}
        });
    }
</script>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Location Wise Cash Balances</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($location_pettycashboxlist as $lockey=>$locationpettycashbox){
                        $locationpettycashbox['pc_limit'] = ($locationpettycashbox['pc_limit'] != 0 && $locationpettycashbox['pc_limit'] !="")?$locationpettycashbox['pc_limit']:1;
                        $completed = customNanAndEmptyValidation(round(($locationpettycashbox['pc_balance']/$locationpettycashbox['pc_limit'])*100));
                        ?>
                    <div class="col-xl-4  cursor-pointer" onclick="getLocationTransaction('<?php echo $lockey;?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <div class="card">

                            <div class="card-body" style="text-align: -webkit-center;">
                                <span class="float-end locationpettycash" id="locationpettycash_<?php echo $lockey;?>"></span>
                                <div class="circle_gradiant" style="--p1:<?php echo $completed*3.5; ?>deg; --p1_color: #2ed8b6;--p2_color: #dc3545;">
                                    <span class="text-dark f-16 f-w-600 d-block spanwhite"><?php echo $completed."%";?></span>
                                </div>
                            </div>
                            <p class="text-center f-w-600 f-18"><?php echo $locations_list[$lockey]; ?></p>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="s-cont d-inline-block">
                                        <h5 class="f-w-700 m-b-0 f-14">Limit</h5>
                                        <p class="m-b-0 f-12"><?php echo customeummarmonyformateifine($locationpettycashbox['pc_limit']); ?></p>
                                    </div>
                                </div>
                                <div class="col-6 b-l-default">
                                    <div class="s-cont d-inline-block">
                                        <h5 class="f-w-700 m-b-0 f-14">Balance</h5>
                                        <p class="m-b-0 f-12"><?php echo customeummarmonyformateifine($locationpettycashbox['pc_balance']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Peety Cash</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="htmlpage">

                </div>
            </div>
        </div>
    </div>
</div>
