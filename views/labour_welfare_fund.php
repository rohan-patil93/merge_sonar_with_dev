<?php
$wdata1q["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0);
$wssdata = urlencode(json_encode($wdata1q));
$wcsuurl = API."?mode=gettabledetailswithconditions&table=labour_welfare_fund&data=".$wssdata;
$wesudata = callAPI($wcsuurl);
$wesudata = json_decode($wesudata,1);
?>
<div class="card card-custom gutter-b">
    <!--begin::Header-->

<div class="card-header">
    <h5>Labour Welfare Fund</h5>

    <div class="card-header-right">
               <a href="#" onclick="dynamicinsertpopup('COM_INSERT_LABOUR_WELFARE_FUND')" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"> <i class="la la-plus"></i>Labour Welfare Fund</a>
    </div>
</div>

    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body row">
        <?php foreach ($wesudata as  $value) { ?>
        <div class="col-md-4">
            <div class="row">
            <div class="col-md-6">
            <span class="font-size-lg font-weight-bold"><?php echo $value['work_location_id']; ?></span>
            </div>
            <div class="col-md-5">
            <span class="font-weight-bold float-right font-size-lg">

                     <button type="button" onclick="dynamicinsertpopup('COM_INSERT_LABOUR_WELFARE_FUND',<?php echo "'".deocdeId("id:".$value['id'])."'" ?>)" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">
                        <i class="fas fa-pencil-alt"></i>
                        </button>

            </span>
                </div>
            </div>
             <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">State</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold f-w-700 float-right font-size-lg text-muted"><?php echo $value['state_id']; ?></span>
                </div>
            </div>
            <hr>

            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">Deduction Cycle</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold f-w-700 float-right font-size-lg text-muted"><?php echo $value['deduction_cycle']; ?></span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">Employee's Contribution</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold f-w-700 float-right font-size-lg text-muted"><?php echo $value['employee_contribution']; ?></span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">Employer's Contribution</span>
                </div>
                <div class="col-md-5">
                    <span class="font-weight-bold f-w-700 float-right font-size-lg text-muted"><?php echo $value['employer_contribution']; ?></span>
                </div>
            </div>

                                                        <hr>
            <div class="row">
                <div class="col-md-6">
                    <span class="font-weight-bold font-size-lg text-muted">PT Slabs</span>
                </div>
                <div class="col-md-5">
                    <a href="#" class="text-muted f-w-700 text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg float-right">View Tax Slabs</a>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>
