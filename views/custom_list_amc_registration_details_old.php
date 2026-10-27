<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// $response = togetamcaccountdetails();
$url = API . "?mode=getamcregistrationdetails&method=get";
$che = callAPI($url);
$return = json_decode($che, 1);
$response = $return['amc_registration_details'];

?>
<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h4>AMC Register Details</h4>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-primary waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm f-12">
                <tr class="f-14">
                    <th>SL NO</th>
                    <th>Product Name</th>
                    <th>Customer Name</th>
                    <th>Activation Date</th>
                    <th>Deactivation Date</th>
                    <th>Payment Type</th>
                    <th>Service Charge</th>
                    <th>Service On</th>
                    <th>No. Of Services</th>
                    <th>Technician</th>
                    <th>Edit</th>
                </tr>
                <?php $i = 1;
                foreach ($response as $row) { ?>
                    <tr>
                        <td>
                            <?php echo $i; ?>
                        </td>
                        <td>
                            <?php echo $row['product_id']; ?>
                        </td>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo fromDate($row['activation_date']); ?></td>
                        <td><?php echo fromDate($row['deactivation_date']); ?></td>
                        <td><?php echo $row['payment_type']; ?></td>
                        <td class="text-right"><?php echo customeummarmonyformate($row['service_charge']); ?></td>
                        <td><?php echo $row['service_on']; ?></td>
                        <td><?php echo $row['no_of_services']; ?></td>
                        <td><?php echo $row['technician_id']; ?></td>
                        <td align="center">
                            <a class="text-primary m-r-5"
                                href="index.php?option=COM_EDIT_AMC_REGISTER_DETAILS&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">
                                <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                            </a>
                            <button type="button" class="btn-success f-14 b-radius-5 f-w-600"> Schedule</button>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </table>
        </div>
    </div>
</div>
<!-- Pradeep HTML End here -->