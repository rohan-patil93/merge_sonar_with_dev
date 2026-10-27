<?php
$_SESSION['apply_js']['multiselest'] = 1;

$url = API . "?mode=getamcregistrationdetails&method=get";
$che = callAPI($url);
$return = json_decode($che, 1);
$response = $return['amc_registration_details'];
//modal data
$url = API . "?mode=getallproductdetails";
$response1 = callAPI($url);
$response1 = json_decode($response1, 1);
$products = $response1['pm_details'];
$customers = $response1['cust_details'];
$ven_details = $response1['ven_details'];
$ag_details = $response1['ag_details'];
$tech_details = $response1['tech_details'];
$services = $response1['services'];
$registration_id_list = array();
foreach ($services as $row) {
    $registration_id_list[] = $row['registration_id'];
}

$gc_details = $response1['gc_details'];
$payment_types = $services  = array();

foreach ($gc_details as $row) {
    if ($row['genc_code'] == 'AMCPAYTYPE') {
        $payment_types[$row['genc_detail_code']] = $row['genc_detail_name'];
    }
    if ($row['genc_code'] == 'AMCSERVICE') {
        $services[$row['genc_detail_code']] = $row['genc_detail_name'];
    }
}

?>
<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h4>AMC Register Details</h4>
        <div class="card-header-right">
            <a href="index.php?option=COM_INSERT_AMC_REGISTRATION_DETAILS" class="btn  btn-success waves-effect waves-light mb-2 desktop-view text-white">
                <i class="icofont icofont-plus text-white"></i>
                AMC Register
            </a>
            <a href="javascript:history.go(-1)" class="btn  waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive mt-3">
            <table class="table  table-sm f-12">
                <tr class="f-14">
                    <th scope="col">SL NO</th>
                    <th scope="col">AMC Name</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Customer/Vendor Name</th>
                    <th scope="col">Activation Date</th>
                    <th scope="col">Deactivation Date</th>
                    <th scope="col">Payment Type</th>
                    <th scope="col">Service Charge</th>
                    <th scope="col">Service On</th>
                    <th scope="col">No. Of Services</th>
                    <th scope="col">Service Name</th>
                    <th scope="col">Service Company</th>
                    <th scope="col">Action</th>
                </tr>
                <?php $i = 1;
                foreach ($response as $row) { ?>
                    <tr>
                        <td>
                            <?php echo $i; ?>
                        </td>
                        <td> <?php echo $row['amc_name'] ?? '-'; ?></td>
                        <td>
                            <?php echo $products[$row['product_id']]['item_name']; ?>
                        </td>
                        <td><?php
                        if($row['party_type'] == 'customer'){
                            echo $customers[$row['customer_id']]['display_name'] ?? '-';
                        }elseif($row['party_type'] == 'vendor'){
                            echo $ven_details[$row['vendor_id']]['display_name'] ?? '-';
                        }else{
                            echo $customers[$row['customer_id']]['display_name'] ?? '-';
                        }
                     ?></td>

                        <td><?php echo fromDate($row['activation_date']); ?></td>
                        <td><?php echo fromDate($row['deactivation_date']); ?></td>
                        <td><?php echo isset($payment_types[$row['payment_type']]) ? $payment_types[$row['payment_type']] : ''; ?></td>
                        <td class="text-right"><?php echo customeummarmonyformate($row['service_charge']); ?></td>
                        <td><?php echo isset($services[$row['service_on']]) ? $services[$row['service_on']] : '';
                            ?></td>
                        <td><?php echo $row['no_of_services']; ?></td>
                        <td><?php echo $ag_details[$row['account_group_id']]; ?></td>
                        <td><?php echo $tech_details[$row['technician_id']]; ?></td>
                        <td style="text-align: center;">
                            <?php
                            if (in_array($row['id'], $registration_id_list)) { ?>

                                <a class="btn btn-primary f-14 b-radius-5 f-w-600"
                                    href="index.php?option=COM_LIST_AMC_SERVICE_SCHEDULES&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">
                                    View
                                </a>
                            <?php   } else { ?>
                                <a class="text-primary m-r-5" target="_blank"
                                    href="index.php?option=COM_INSERT_AMC_REGISTRATION_DETAILS&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">
                                    <i class="fa fa-edit btn-warning p-2 b-radius-5"></i>
                                </a>

                                <button type="button" class="btn-success f-14 b-radius-5 f-w-600" onclick="confirmSchedule(<?php echo $row['id']; ?>)">Schedule</button>
                            <?php   }
                            ?>

                        </td>
                    </tr>
                <?php $i++;
                } ?>
            </table>
        </div>
    </div>
</div>
<!-- Pradeep HTML End here -->

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmScheduleModal" tabindex="-1" aria-labelledby="confirmScheduleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmScheduleLabel">Confirm Schedule Creation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to create the service schedule?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Show confirmation modal
    function confirmSchedule(registrationId) {
        $('#confirmScheduleModal').modal('show');
        $('#confirmBtn').off('click').on('click', function() {
            $('#confirmScheduleModal').modal('hide');
            createSchedule(registrationId);
            console.log(registrationId);
        });
    }

    async function createSchedule(registrationId) {
        try {
            const data = await $.ajax({
                url: "ajax.php",
                data: `page=createschedule&id=${registrationId}`,
                type: "POST",
                dataType: "text" // Receive raw text to handle concatenated JSON objects
            });
            console.log('data: ', data);

            // Split the concatenated JSON objects
            let cleanedData = data.split('}{').join('},{'); // Add a comma between objects
            cleanedData = '[' + cleanedData + ']'; // Wrap them in an array

            // Parse the cleaned JSON
            let parsedArray = JSON.parse(cleanedData);

            // Access individual objects
            let message = parsedArray[0].message;

            // Refresh the page after showing the message
            location.reload();
        } catch (e) {
            console.error("Error parsing JSON response:", e);
            console.log("Raw response:", e.responseText || data);
        }
    }
</script>
