
<?php
$_SESSION['apply_js']['multiselest'] = 1;

if(isset($_POST['save']) && $_POST['save'] == 'SAVE') {
    $url = API . "?mode=updateamcservicedetailsandchecklist&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $res = json_decode($response1, 1);
    if ($res['res']['status'] == 'sucess') {
        ?>
        <script>
            window.addEventListener("load", function() {
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX',
                    'Data Saved Successfully', '');
            });
        </script>
        <?php
    }

}

if (isset($_GET['Rid']) && $_GET['Rid'] != '') {
    $isEdit = true;
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid)[1];

    $url = API . "?mode=getamcservicedetails&method=get&id=" . $rid;
    $res = json_decode(callAPI($url), true);
    $amc_service_schedules = $res['amc_service_schedules'];
    $gc_details = $res['gc_details'] ?? [];
    $ag_details = $res['ag_details'] ?? [];
    $checklist_items = $res['checklist_items'] ?? [];
    $check_list_status = $res['check_list_status'] ?? [];
}
function formatServiceDate($service_date) {
    if (empty($service_date)) {
        return [
            'type' => 'date',
            'value' => ''
        ];
    }

    $timestamp = strtotime($service_date);

    $has_time = date('H:i', $timestamp) != '00:00';

    if ($has_time) {
        return [
            'type' => 'datetime-local',
            'value' => date('Y-m-d\TH:i', $timestamp)
        ];
    } else {
        return [
            'type' => 'date',
            'value' => date('Y-m-d', $timestamp)
        ];
    }
}
?>

<div class="page-body">
    <div class="card">
        <!-- Modal Header -->
        <div class="card-header">
            <h5 >Update Service Details</h5>
        </div>

        <form id="serviceDetailsForm" method="post">
            <!-- Modal Body -->
            <div class="card-body row">
                <input type="hidden" id="recordId" name="record_id" value="<?php echo $amc_service_schedules['id'] ?? ''; ?>">
                <input type="hidden" id="registration_id" name="registration_id" value="<?php echo $amc_service_schedules['registration_id'] ?? ''; ?>">
                <!-- Service Date -->
                <div class="form-group col-md-4">
                    <label for="serviceDate" class="form-label">Service Date</label>
                    <?php
                    $formatted = formatServiceDate($amc_service_schedules['service_date']);
                    ?>

                    <input aria-label="Input field"
                        type="<?php echo $formatted['type']; ?>"
                        class="form-control"
                        id="serviceDate"
                        name="service_date"
                        value="<?php echo $formatted['value']; ?>"
                    >
                </div>

                <!-- Service Type -->
                <div class="form-group col-md-4">
                    <label for="paymentType" class="form-label">Payment/Service Type</label>
                    <select class="form-select js-example-basic-single" id="paymentType" name="payment_type" onchange="toggleServiceCharge()">
                        <option value="0">Select Any One</option>
                        <?php foreach ($gc_details as $key => $value){ ?>
                            <option value="<?php echo $key; ?>" <?php echo (isset($amc_service_schedules['payment_type']) && $amc_service_schedules['payment_type'] == $key) ? 'selected' : ''; ?>>
                                <?php echo $value; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Service Charge -->
                <div class="form-group col-md-4" id='serviceChargeContainer'>
                    <label for="serviceCharge" class="form-label">Service Charge</label>
                    <input type="number" step="0.01" class="form-control text-right" id="serviceCharge" name="service_charge" placeholder="0.00" value="<?php echo $amc_service_schedules['service_charge'] ?? ''; ?>">
                </div>

                <!-- Service Expenditure -->
                <div class="form-group col-md-4">
                    <label for="serviceExpenditure" class="form-label">Service Expenditure</label>
                    <input type="number" step="0.01" class="form-control text-right" id="serviceExpenditure" name="service_expenditure" placeholder="0.00" value="<?php echo $amc_service_schedules['service_expenditure'] ?? ''; ?>">
                </div>


                <div class="form-group col-md-4">
                    <label for="travelExpenditure" class="form-label">Travel Expenditure (TA/DA)</label>
                    <input type="number" step="0.01" class="form-control text-right" id="travelExpenditure" name="ta_or_da" placeholder="0.00" value="<?php echo $amc_service_schedules['ta_or_da'] ?? ''; ?>">
                </div>
                <div class="form-group col-md-4">
                    <span class="form-label">Service Name</span>
                    <select aria-label="Account Group" name="account_group_id" class="form-control js-example-basic-single" id='accountGroupId' onchange="updateGroups(this)" data-technician-id="<?php echo $amc_service_schedules['technician_id'] ?? ''; ?>">
                        <option value="0">Select Any One</option>
                        <?php foreach ($ag_details as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php echo (isset($amc_service_schedules['account_group_id']) && $amc_service_schedules['account_group_id'] == $key) ? 'selected' : ''; ?>>
                                <?php echo $value; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <span class="form-label">Service Company</span>
                    <select aria-label="Technician" name="technician_id" class="form-control js-example-basic-single" id='selectTech'>
                        <option value="0">Select Any One</option>
                    </select>
                </div>

                <!-- Service Reports -->
                <div class="form-group col-md-4">
                    <label for="serviceReports" class="form-label">Service Reports</label>
                    <textarea class="form-control" id="serviceReports" name="service_reports" rows="2" placeholder="Enter report details"><?php echo $amc_service_schedules['service_reports'] ;?>  </textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php if (!empty($checklist_items)){ foreach($checklist_items as $item_id => $item){
                                $itemText =  $item;
                                $itemStatus = isset($check_list_status[$item_id]['check_status']) ? $check_list_status[$item_id]['check_status'] : 0;
                                $remarks = $check_list_status[$item_id]['check_remarks'] ?? '';
                                $newId = $check_list_status[$item_id]['id'] ?? '';
                            ?>
                            <div class="col-md-12 d-flex align-items-start checklist-item mb-2">
                                <div class="form-check me-2 mt-2">
                                    <input aria-label="Checkbox" class="form-check-input checklist-status"
                                        type="checkbox"
                                        name="checklist_status[<?php echo $item_id; ?>]"
                                        value="1" <?php echo $itemStatus ? 'checked readonly' : ''; ?>>
                                </div>
                                <div class="form-group flex-grow-1 me-2">
                                    <input type="hidden" name="existing_checklist_ids[]" value="<?php echo htmlspecialchars($item_id); ?>" >
                                    <input aria-label="Checklist Items[]" type="text"
                                        name="checklist_items[]"
                                        class="form-control checklist-text"
                                        value="<?php echo htmlspecialchars($itemText); ?>" readonly>
                                </div>
                                <div class="form-group flex-grow-1 me-2">
                                    <input type="hidden" name="new_checklist_ids[]" value="<?php echo $newId; ?>" >

                                    <textarea aria-label="Enter Remarks" name="checklist_remarks[]" class="form-control checklist-text fill"  placeholder="Enter Remarks"><?php echo htmlspecialchars($remarks); ?></textarea>
                                </div>

                            </div>
                        <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="card-footer">
                <button type="submit" name="save" value="SAVE" class="btn btn-primary" >Save Changes</button>
            </div>
        </form>
    </div>
</div>
<script src="assets/bower_components/jquery/js/jquery.min.js"></script>

<script>
    $(document).ready(function () {
        const accountGroupEl = document.getElementById('accountGroupId');
        if (accountGroupEl.value !== "0") {
            updateGroups(accountGroupEl);   // this will auto-select technician too
        }
    });
    function updateGroups(selectElement) {
        const selectedValue = selectElement.value;
        const field = selectElement.name;
        const technicianId = $(selectElement).data('technician-id');
        let table_name = 'amc_account_details';

        jQuery.ajax({
            url: "ajax.php",
            data: `page=getselectedlist&id=${selectedValue}&field=${field}&table_name=${table_name}`,
            type: "POST",
            success: function(data) {
                if (data) {
                const response = JSON.parse(data);
                const techSelect = $('#selectTech');
                techSelect.empty().append('<option value="0">Select Any One</option>');

                response.forEach(function(row) {
                    const selected = (row.id == technicianId) ? 'selected' : '';
                    techSelect.append(`<option value="${row.id}" ${selected}>${row.account_name}</option>`);
                });
            } else {
                    console.error("No data received.");
                }
            },
            error: function() {
                console.error("Error occurred while fetching data.");
            }
        });
    }

    function toggleServiceCharge() {
        const serviceType = $('#paymentType').val();
        if (serviceType === '2') {
            $('#serviceChargeContainer').show();
            $('#serviceCharge').prop('required', true);
        } else {
            $('#serviceChargeContainer').hide();
            $('#serviceCharge').val('0.00');
            $('#serviceCharge').prop('required', false);
        }
    }

</script>
