<?php
if($_POST['ajaxpagecall'] == "Y"){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $url = API."?mode=getamcschedulesbydate&admin_type=".$_SESSION['admin_type']."&date=".$_POST['date'];
    $data_details = callAPI($url);
    $details = json_decode($data_details,1);
}
$schedule_data = $details['today_services']['schedule_data'];
?>
<div class="card">
    <div class="card-header">
        <h5><?= __('AMC Schedule'); ?> </h5>

    </div>
    <div class="card-body">
        <div class="custom-scroll" style="height: 500px;">
        <?php
        if(count((array)$schedule_data) != 0){ ?>
            <table  class="table table-hover m-b-0">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?= __('AMC Name'); ?></th>
                        <th scope="col"><?= __('Product Name'); ?></th>
                        <th scope="col"><?= __('Customer Name'); ?></th>
                        <th scope="col"><?= __('Service Type'); ?></th>
                        <th scope="col"><?= __('Service Name'); ?></th>
                        <th scope="col"><?= __('Service Company'); ?></th>
                        <th scope="col"><?= __('Date Time'); ?></th>
                        <th scope="col"><?= __('Status Name'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php $l=1; foreach($schedule_data as $schedule){  ?>
                <tr>
                    <td class="p-2">
                        <?php echo $l; ?>
                    </td>
                    <td class="p-2">
                        <?php echo $schedule['amc_name'] !=""?$schedule['amc_name']:"-"; ?>
                    </td>
                    <td class="p-2">
                        <?php echo $schedule['product_name'] !=""?$schedule['product_name']:"-"; ?>
                    </td>
                    <td class="p-2">
                        <?php echo $schedule['customer_name'] !=""?$schedule['customer_name']:"-"; ?>
                    </td>
                    <td class="p-2">
                        <?php echo $schedule['service_type'] !=""?$schedule['service_type']:"-"; ?>
                    </td>
                    <td class="p-2">
                        <?php echo $schedule['service_name'] !=""?$schedule['service_name']:"-"; ?>
                    </td>
                    <td class="p-2">
                        <?php echo $schedule['service_company'] !=""?$schedule['service_company']:"-"; ?>
                    </td>
                    <td class="p-2">
                        <?php echo $schedule['date'] !=""? date("h:i A", strtotime($schedule['date'])):"-"; ?>
                    </td>
                    <td class="p-2">
                        <?php $viw= "text-warning"; if($schedule['current_status']){ $viw= "text-danger";
}?>
                        <h6 class="m-0 <?php echo $viw; ?> ">
                        <?php echo $schedule['status_name'] !=""?$schedule['status_name']:"-"; ?>
                        </h6>
                    </td>
                </tr>
            <?php $l++;
} ?>
                </tbody>
            </table>
            <?php }else{ ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> <?= __('No Data Found'); ?></p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
