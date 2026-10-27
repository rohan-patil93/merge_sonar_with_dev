<?php

$data1q["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$ssdata = urlencode(json_encode($data1q));
$csuurl = API."?mode=gettabledetailswithconditions&table=professional_tax&data=".$ssdata;
$esudata = callAPI($csuurl);
$esudata = json_decode($esudata,1);
$statearray = array();
$chkstatewhe["where"] = array('is_active'=>0);
$chkstatewhe["field"] = "id,state_name";
$chks = urlencode(json_encode($chkstatewhe));
$sateurl = API."?mode=gettabledetailswithconditions&table=states&data=".$chks;
$datasdata =  callAPI($sateurl);
$datasdata = json_decode($datasdata,1);
foreach($datasdata as $datas){
        $statearray[$datas['id']] = $datas['state_name'];
}

$locationsrray = array();
$locationwhe["where"] = array('is_active'=>0);
$locationwhe["field"] = "id,location_name";
$locationmchks = urlencode(json_encode($locationwhe));
$lolcationsateurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$locationmchks;
$lolcationdatasdata =  callAPI($lolcationsateurl);
$lolcationdatasdata = json_decode($lolcationdatasdata,1);
foreach($lolcationdatasdata as $locationn){
        $locationsrray[$locationn['id']] = $locationn['location_name'];
}
?>
<div class="card card-custom gutter-b">
<div class="card-header">
    <h5>Professional Tax</h5>
    <span>This tax is lived on an exployee's incoe by the state Government. Tax slabs differ  ineach state.</span>
    <div class="card-header-right">
            <button type="button" onclick="dynamiciptpopup('custom_insert_professional_tax.php','')" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"><i class="icofont icofont-plus icon-white"></i>  Professional Tax  </button>
            <button type="button" onclick="dynamiciptpopup('custom_insert_professional_tax_exemptions.php','')" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"><i class="icofont icofont-plus icon-white"></i>  Professional Tax Exemptions</button>
    </div>
</div>

    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body row">
        <?php foreach ($esudata as  $value) { ?>
        <div class="col-md-4 p-3">
            <div class="row">
            <div class="col-md-6">
            <span class="font-size-lg font-weight-bold">
                <?php echo $locationsrray[$value['work_location_id']]; ?>
            </span>
            </div>
            <div class="col-md-5">
            <span class="font-weight-bold float-right font-size-lg">

                    <button type="button" onclick="dynamiciptpopup('custom_insert_professional_tax.php',<?php echo $value['id']; ?>)" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">
                       <i class="fas fa-pencil-alt"></i>
                   </button>

            </span>
                </div>
            </div>
             <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">PT Number</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold f-w-700 float-right font-size-lg text-muted"><?php echo $value['pt_number']; ?></span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">Location</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold f-w-700 float-right font-size-lg text-muted"><?php echo $locationsrray[$value['work_location_id']]; ?></span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">State</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold f-w-700 float-right font-size-lg text-muted"><?php echo $statearray[$value['state_id']]; ?></span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">Deduction Cycle</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold  f-w-700 float-right font-size-lg text-muted"><?php echo "-"; ?></span>
                </div>
            </div>

        </div>
    <?php } ?>
    </div>
</div>
<script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/bootstrap-maxlength/js/bootstrap-maxlength.js"></script>
    <script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>
