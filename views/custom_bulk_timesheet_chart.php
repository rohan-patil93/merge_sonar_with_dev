<?php
$chart_data = array();
$curl = API."?mode=blukapprovalvendorchart&approval_manager=".$_SESSION['userid']."&month=".$_GET['PAY_MONTH']."&year=".$_GET['PAY_YEAR'];
$cudata = callAPI($curl);
$vendor_charts = json_decode($cudata,1);
foreach($vendor_charts as $vendor_chart){
    $chart_data['vendor'][] = $vendor_chart['display_name'];
    $chart_data['budget'][] = $vendor_chart['esign_approved_amount'];
    $chart_data['bill'][] = $vendor_chart['bill_amount'];
}
?>
<style type="text/css">
    h6 a {
        cursor: pointer;
    }
</style>
<!--begin::Card-->
<div class="card card-custom gutter-b">
    <div class="card-header">
        <div class="card-title">
            <h5 class="card-label"><?= __("Vendor-Wise Budget Vs Actuals") ?> <?php ?></h5>
        </div>
    </div>
    <div class="card-body">
        <!--begin::Chart-->

        <!--end::Chart-->

        <div class="table-responsive">
            <table class="table table-hover m-b-0">
                <thead>
                    <tr class="">
                        <th scope="col" class=""></th>
                        <th scope="col" class="" name="vendors_count"> <?= __("Vendor Name") ?></th>
                        <th scope="col" class="text-right" name="resources_count"> <?= __("Budget Amount") ?></th>
                        <th scope="col" class="text-right" name="leaves_count"> <?= __("Available") ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach($vendor_charts as $vendor_chart){?>
                        <tr>
                            <td class="">
                                <?php if($vendor_chart['image']!=""){ ?>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40">
                                            <img src="uploads/customer/<?php echo $vendor_chart['image']; ?>" class="img-radius img-50" alt="" />
                                        </div>
                                    </div>
                                <?php }else{ ?>
                                    <div class="img-radius img-50 align-top m-r-15 text-center userimageround"> <span class="symbol-label font-size-h5"><?php echo initials($vendor_chart['display_name']); ?></span> </div>
                                <?php } ?>
                            </td>
                            <td class="">
                                <a  class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-sm">&nbsp;
                                    <?php echo $vendor_chart['display_name']; ?> </a>    </br><small>(<?php echo $vendor_chart['esign_ref_no']; ?> ) <?= __("Exp From :") ?> <?php echo fromDate($vendor_chart['effective_from_date']); ?>  <?= __("Exp To :") ?> <?php echo fromDate($vendor_chart['effective_to_date']); ?></small>

                                </td>
                                <td class="text-right">
                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php  $eam= $vendor_chart['esign_approved_amount']!=""?$vendor_chart['esign_approved_amount']:"-"; echo customeummarmonyformate($eam); ?></span>

                                </td>
                                <td class="text-right">
                                    <span class="text-muted font-weight-bolder d-block font-size-lg"><?php $eba=$vendor_chart['available']!=""?$vendor_chart['available']:"-"; echo customeummarmonyformate($eba); ?> <?php if($eam<$eba){ ?> <i class="fas fa-level-down-alt text-c-red m-l-10"></i> <?php }else{ ?>  <i class="fas fa-level-up-alt text-c-green m-l-10"></i><?php } ?></span>
                                </td>

                            </tr>
                        <?php } //} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end::Card-->

