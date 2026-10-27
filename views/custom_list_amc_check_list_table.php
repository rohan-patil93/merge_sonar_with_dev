<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$data = json_encode($_POST);
$url = API . "?mode=getamcchecklistdetails&data=".urlencode($data);
$result = json_decode(callAPI($url), true)?? [];
$response = $result['service'];
$ag_details = $result['ag_details'];
$gc_details = $result['gc_details'];
$tech_details = $result['tech_details'];
$payment_types = $services = [];
foreach ($gc_details as $row) {
    if ($row['genc_code'] == 'AMCPAYTYPE') { $payment_types[$row['genc_detail_code']] = $row['genc_detail_name'];
}
    if ($row['genc_code'] == 'AMCSERVICE') { $services[$row['genc_detail_code']] = $row['genc_detail_name'];
}
}

?>
<div class="card">
    <div class="card-body">

        <?php
        if(count((array)$response)>0){ ?>
        <div class="table-responsive mt-3">
            <table class="table  table-sm f-12">
                <tr class="f-14">
                    <th scope="col">S.NO</th>
                    <th scope="col">Service Date</th>
                    <th scope="col">Service Type</th>
                    <th scope="col">Service Charge</th>
                    <th scope="col">Service Expenditure</th>
                    <th scope="col">TA/DA</th>
                    <th scope="col">Service Name</th>
                    <th scope="col">Service Person</th>
                    <th scope="col">Action</th>
                </tr>
                <?php $i = 1;
                foreach ($response as $row) { ?>
                    <tr>
                        <td><?php echo $i; ?> </td>
                        <td><?php echo fromDate($row['service_date']); ?> </td>
                        <td><?php echo $payment_types[$row['payment_type']] ?? ''; ?></td>
                        <td><?php echo $row['service_charge']; ?></td>
                        <td><?php echo $row['service_expenditure']; ?></td>
                        <td><?php echo $row['ta_or_da']; ?></td>
                        <td><?php echo $ag_details[$row['account_group_id']]; ?></td>
                        <td><?php echo $tech_details[$row['technician_id']]; ?></td>
                        <td style="text-align: center;">

                                <a class="text-primary m-r-5" target="_blank"
                                    href="index.php?option=COM_LIST_AMC_SERVICE_SCHEDULES_EDIT&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">
                                    <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                                </a>
                        </td>
                    </tr>
                <?php $i++;
                }
                } else{?>
                <div class="card-px text-center py-20 m-t-30">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>

                <?php }?>
            </table>
        </div>
    </div>
</div>

