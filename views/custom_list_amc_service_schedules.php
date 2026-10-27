<?php
$_SESSION['apply_js']['multiselest'] = 1;
$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API . "?mode=getservicescheduledetails&id=" . $rid;
$res = callAPI($url);
$res = json_decode($res, 1);
$response = $res['serviceDetails'];
$gc_details = $res['gc_details'];
$ag_details = $res['ag_details'];
$tech_details = $res['tech_details'];
$customer_details = $res['customer_details'];

function formatServiceDate($date) {
    if (empty($date)) { return '';
}

    $timestamp = strtotime($date);

    $has_time = date('H:i', $timestamp) != '00:00';

    if ($has_time) {
        return date('d-m-Y h:i A', $timestamp); // with time
    } else {
        return date('d-m-Y', $timestamp); // only date
    }
}
?>
<style>
    .detail-row {
        background-color: #c8e6c9;
        border-radius: 5px;
    }

    .detail-label {
        font-weight: bold;
        color: #333;
    }

    .back-btn {
        background-color: #2e7d32;
        color: white;
    }
</style>
<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h4>AMC Registration Details</h4>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn  waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="mt-2 p-2">

        <div class="card-body">
            <div class="row detail-row p-3 mb-2">
                <div class="col-md-6">
                    <p class="mb-1"><span class="detail-label">AMC Name:</span> <?php echo $customer_details['amc_name']; ?></p>
                    <p class="mb-1"><span class="detail-label">Mobile:</span> <?php echo $customer_details['mobile_no']; ?></p>
                    <p class="mb-1"><span class="detail-label">Address:</span> <?php echo $customer_details['address_line1'] . $customer_details['address_line2']; ?></p>
                    <p class="mb-1"><span class="detail-label">Service On :</span> <?php echo $customer_details['service_frequency']; ?></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><span class="detail-label">Customer:</span> <?php echo $customer_details['customer_name']; ?></p>
                    <p class="mb-1"><span class="detail-label">Activation Date:</span> <?php echo fromDate($customer_details['activation_date']); ?></p>
                    <p class="mb-1"><span class="detail-label">Renewal/Deactivation Date:</span> <?php echo fromDate($customer_details['deactivation_date']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body custom-scroll" style="max-height: 600px;">
        <div class="row">
            <div class="col-lg-8">
                <h4>Schedule List</h4>
            </div>
            <div class="col-lg-4 text-end">
                <div class="input-group">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="fa fa-calendar"></i></span>
                    </span>
                    <input aria-label="Service Date Filter" type="date" class="form-control" id="serviceDateFilter">
                    <button type="button" class="btn btn-secondary" id="clearServiceDateFilter">Clear</button>
                </div>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table  table-sm f-12">
                <tr class="f-14">
                    <th scope="col">S.NO</th>
                    <th scope="col">Service Date</th>
                    <th scope="col">Service Type</th>
                    <th scope="col">Service Charge</th>
                    <th scope="col">Service Expenditure</th>
                    <th scope="col">TA/DA</th>
                    <th scope="col">Service</th>
                    <th scope="col">Service Company</th>
                    <th scope="col">Action</th>
                </tr>
                <?php $i = 1;
                foreach ($response as $row) { ?>
                    <tr class="service-schedule-row" data-service-date="<?php echo date('Y-m-d', strtotime($row['service_date'])); ?>">
                        <td class="service-sno"><?php echo $i; ?> </td>
                        <td><?php echo formatServiceDate($row['service_date']); ?> </td>
                        <td><?php echo $gc_details[$row['payment_type']] ?? ''; ?></td>
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
                } ?>
            </table>
        </div>
    </div>
</div>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script>
    function filterServiceSchedulesByDate() {
        var selectedDate = $('#serviceDateFilter').val();
        var serialNo = 1;

        $('.service-schedule-row').each(function() {
            var rowDate = $(this).data('service-date');
            if (selectedDate === '' || rowDate === selectedDate) {
                $(this).show();
                $(this).find('.service-sno').text(serialNo);
                serialNo++;
            } else {
                $(this).hide();
            }
        });
    }

    $('#serviceDateFilter').on('change', function() {
        filterServiceSchedulesByDate();
    });

    $('#clearServiceDateFilter').on('click', function() {
        $('#serviceDateFilter').val('');
        filterServiceSchedulesByDate();
    });

    function openAMCModal(recordId) {
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#dynamic_popup1").html(loader);
        $('#kt_modal_dynamic_popup').modal("show");
        $.ajax({
            url: 'views/custom_list_amc_service_schedules_edit.php',
            type: 'POST',
            data: {
                record_id: recordId
            },
            success: function(response) {
                $('#dynamic_popup1').html(response);
                $(".js-example-basic-single").select2();

            },
            error: function(xhr) {
                alert('Failed to load data');
            }
        });

    }

</script>
