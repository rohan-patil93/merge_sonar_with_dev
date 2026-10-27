<?php
$_SESSION['apply_js']['multiselest'] = '1';
$interest_rate   = $_SESSION['loan_interest_rate'] ?? 12;          // e.g. 12
$interest_method = $_SESSION['loan_interest_method'] ?? 'REDUCING';    // or 'REDUCING'
$loan_id = '';
if ($_GET['Rid'] != "") {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $loan_id = $rid['1'];
}
// Get employees list
$url = API . "?mode=getloanemployeesdata&company_id=" . $_SESSION['company_id'] . "&rid=" . $loan_id;
$response = callAPI($url);
$res = json_decode($response, true);
$employees = $res['employees'] ?? array();
$loan           = $res['loan'] ?? array();
$installments   = $res['installments'] ?? array();

// Handle save

if (isset($_POST['save'])) {
    $url = API . "?mode=saveemployeeloan&method=post&company_id=" . $_SESSION['company_id'] . "&user_id=" . $_SESSION['userid'];
    $saveResponse = file_post_contents($url, $_POST, 'insert');
    $saveResult   = json_decode($saveResponse, true);

    if ($saveResult['status'] == 'success') {
        $_SESSION['msg_show'] = "Loan/Advance saved successfully";
        header("location:index.php?option=COM_LIST_EMPLOYEE_LOANS");
        exit;
    } else {
        $error_message = $saveResult['message'] ?? 'Error saving loan';
    }
}
?>
<style>
    .summary-box .row {
        font-size: 14px;
        padding: 3px 0;
    }

    .summary-box hr {
        opacity: 0.3;
    }
</style>
<script type="text/javascript">
    function submitDynamicWorkflowPopUp(table_name, transaction_id, current_status, next_status) {
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data: 'table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
            type: "POST",
            success: function(data) {
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error: function() {}
        });
    }

    function submitDynamicWorkflow(table_name, transaction_id, current_status, next_status, old_status_name, new_status_name) {
        swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "Please note the status of the document will change to " + new_status_name,
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false
            },
            function() {
                console.log("dataa aa");
                jQuery.ajax({
                    url: "ajax.php",
                    data: 'page=submitstatusdynamicworkflow&table_name=' + table_name + '&current_status=' + current_status + '&transaction_id=' + transaction_id + '&next_status=' + next_status,
                    type: "POST",
                    success: function(response) {
                        console.log(response);
                        var res = JSON.parse(response);
                        window.location.reload();
                        if (res.transaction_update.status == 'sucess') {
                            window.location.reload();
                            swal({
                                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true
                            }, function() {
                                window.location.reload();
                            });
                        } else {
                            swal({
                                title: "Oops...",
                                text: res.transaction_update.message,
                                type: "error",
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: "Ok",
                                confirmButtonClass: "btn-danger",
                                closeOnCancel: false
                            });
                        }
                    },
                    error: function() {}
                });
            });
    }
</script>
<?php
$loan_data = $loan;
if (isset($loan_data['id']) && $loan_data['current_status'] != "") {
    $workflowsteps = getWorkflowStatus('employee_loans', $loan_data['id'], $loan_data['current_status']);
    echo $workflowsteps['current_status_ribbon'];
}
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Employee Advance / Loan</h5>
            <div class="card-header-right">
                <?php
                echo $workflowsteps['workflow_btn'];
                ?>
                <?php
                $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK, '');
                if (in_array($loan_data['current_status'], (array)$check_array)) {
                ?>
                <?php
                } else {
                    $disable = "disabled";
                    $formdisable = "event_disabled";
                }

                ?>
                <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                    <i class="icofont icofont-reply mr-1"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-block">

            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>

            <form method="post" id="loanForm">
                <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                <div class="row">
                    <!-- Employee -->
                    <div class="col-lg-4 form-group">
                        <span class="form-label">Employee</span>
                        <select aria-label="Employee" name="employee_id" id="employee_id" class="form-select js-example-basic-single">
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $emp) { ?>
                                <option value="<?php echo $emp['id']; ?>"
                                    <?php if (!empty($loan['employee_id']) && $loan['employee_id'] == $emp['id']) { echo "selected";
} ?>>
                                    <?php echo $emp['display_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Type -->
                    <div class="col-lg-2">
                        <span class="form-label">Type</span>
                        <select aria-label="Loan Type" name="loan_type" id="loan_type" class="form-select js-example-basic-single">
                            <option value="ADVANCE" <?php if (($loan['loan_type'] ?? '') == "ADVANCE") { echo "selected";
} ?>>Advance</option>
                            <option value="LOAN" <?php if (($loan['loan_type'] ?? '') == "LOAN") { echo "selected";
} ?>>Loan</option>
                        </select>
                    </div>
                    <!-- Taken Date -->
                    <div class="col-lg-2">
                        <span class="form-label">Taken Date</span>
                        <input aria-label="Start Date" type="date" name="start_date" id="start_date" class="form-control"
                            value="<?php echo $loan['start_date'] ?? date('Y-m-d'); ?>" required>
                    </div>
                    <!-- Amount -->
                    <div class="col-lg-2">
                        <span class="form-label">Amount</span>
                        <input aria-label="Amount" type="number" step="any" class="form-control"
                            name="amount" id="amount"
                            value="<?php echo $loan['principal_amount'] ?? 0; ?>" required>
                    </div>


                    <div class="col-lg-2">
                        <span class="form-label">No. of Months</span>
                        <input aria-label="No Of Months" type="number" class="form-control" name="no_of_months" id="no_of_months"
                            value="<?php echo $loan['no_of_months'] ?? 0; ?>" required>
                    </div>

                </div>

                <!-- Hidden Config Fields -->
                <input type="hidden" name="interest_rate" id="interest_rate" value="<?php echo $interest_rate; ?>">
                <input type="hidden" name="interest_method" id="interest_method" value="<?php echo $interest_method; ?>">
                <input type="hidden" name="total_amount" id="total_amount" value="<?php echo $loan['total_amount'] ?? 0; ?>">

                <div class="row mt-3">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-info" onclick="calculateSchedule()">Calculate Installments</button>
                    </div>
                </div>


                <div id="schedule_row"
                    <?php
                    if (!empty($installments)) {
                        echo 'style="display: ;"';
                    } else {
                        echo 'style="display:none;"';
                    }
                    ?>>
                    <div class="row mt-3">
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-body custom-scroll" style="max-height: 300px;">
                                    <!-- Installments Table -->
                                    <div id="installments_table">
                                        <?php if (!empty($installments)) { ?>
                                            <div class="table-responsive">
                                                <h5>Monthly Installments</h5>
                                                <table class="table table-hover table-sm">
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Date</th>
                                                        <th scope="col">Amount</th>
                                                    </tr>

                                                    <?php foreach ($installments as $inst) { ?>
                                                        <tr>
                                                            <td><?php echo $inst['installment_no']; ?></td>
                                                            <td><?php echo date('M-Y', strtotime($inst['installment_date'])); ?></td>
                                                            <td><?php echo $inst['installment_amount']; ?></td>
                                                            <input type="hidden" name="installment_no[]" value="<?php echo $inst['installment_no']; ?>">
                                                            <input type="hidden" name="installment_date[]" value="<?php echo $inst['installment_date']; ?>">
                                                            <input type="hidden" name="installment_amount[]" value="<?php echo $inst['installment_amount']; ?>">
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body custom-scroll" style="max-height: 300px;">
                                <div  id="loan_summary">
                                    <?php
                                    $summaryHtml = '';

                                    if (!empty($loan)) {

                                        $empId = $loan['employee_id'] ?? '';
                                        $empName = '';
                                        foreach ($employees as $e) {
                                            if ($e['id'] == $empId) {
                                                $empName = $e['display_name'];
                                                break;
                                            }
                                        }

                                        $amountVal      = number_format($loan['principal_amount'], 2);
                                        $monthsVal      = $loan['no_of_months'];
                                        $rateVal        = $loan['interest_rate'] . '%';
                                        $typeVal        = $loan['loan_type'];
                                        $dateVal        = $loan['start_date'];

                                        $rate = $loan['interest_rate'];
                                        $method = $loan['interest_method'];
                                        $principal = $loan['principal_amount'];
                                        $months = $loan['no_of_months'];

                                        $monthlyInterest = 0;

                                        if ($typeVal == "LOAN" && $method == "FLAT") {
                                            $totalInterest = ($principal * ($rate / 100) * ($months / 12));
                                            $monthlyInterest = $totalInterest / $months;
                                        } elseif ($typeVal == "LOAN" && $method == "REDUCING") {
                                            // First month interest
                                            $monthlyInterest = ($principal * ($rate / 100)) / 12;
                                        } else {
                                            $monthlyInterest = 0;
                                        }

                                        $totalInterest   = $monthlyInterest * $months;
                                        $totalPayable    = $loan['total_amount'];

                                        echo "
                                            <div class='summary-box'>
                                            <h5 class='mb-3'>Advance / Loan Summary</h5>

                                            <div class='row mb-2'>
                                                <div class='col-6 text-muted'>Employee Name</div>
                                                <div class='col-6 fw-bold text-end'>{$empName
}</div>
                                            </div>

                                            <div class='row mb-2'>
                                                <div class='col-6 text-muted'>Type</div>
                                                <div class='col-6 fw-bold text-end'>{$typeVal
}</div>
                                            </div>

                                            <div class='row mb-2'>
                                                <div class='col-6 text-muted'>Amount</div>
                                                <div class='col-6 fw-bold text-end'>{$amountVal
}</div>
                                            </div>

                                            <div class='row mb-2'>
                                                <div class='col-6 text-muted'>Date Taken</div>
                                                <div class='col-6 fw-bold text-end'>{$dateVal
}</div>
                                            </div>

                                            <div class='row mb-2'>
                                                <div class='col-6 text-muted'>No. of Months</div>
                                                <div class='col-6 fw-bold text-end'>{$monthsVal
}</div>
                                            </div>";
                                        if ($typeVal == "LOAN") {
                                            echo "<div class='row mb-2'>
                                                <div class='col-6 text-muted'>Interest Rate</div>
                                                <div class='col-6 fw-bold text-end'>{$rateVal
}</div>
                                            </div>
                                            <div class='row mb-2'>
                                                <div class='col-6 text-muted'>Monthly Interest</div>
                                                <div class='col-6 fw-bold text-end'>" . number_format($monthlyInterest, 2) . "</div>
                                            </div>

                                            <div class='row mb-2'>
                                                <div class='col-6 text-muted'>Total Interest</div>
                                                <div class='col-6 fw-bold text-end'>" . number_format($totalInterest, 2) . "</div>
                                            </div>";
                                        }
                                        echo "<hr class='my-2' />

                                            <div class='row'>
                                                <div class='col-6 text-muted'>Total Amount Payable</div>
                                                <div class='col-6 fw-bold text-end text-primary'>" . number_format($totalPayable, 2) . "</div>
                                            </div>
                                            </div>
                                        ";
                                    }
                                    ?>

                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-lg-12 text-start">
                            <button type="submit" name="save" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
<script>
    function calculateSchedule() {
        var loanType = $('#loan_type').val();
        var amount = parseFloat($('#amount').val() || 0);

        var startDate = $('#start_date').val();
        var months = parseInt($('#no_of_months').val() || 0);
        var rate = parseFloat($('#interest_rate').val() || 0);
        var method = $('#interest_method').val(); // FLAT / REDUCING

        if (!amount || !startDate || !months) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX',
                'Please enter Amount, Date Taken and No. of Months', '');
            return;
        }

        var scheduleHtml = '';
        scheduleHtml += '<div class="table-responsive">';
        scheduleHtml += '<h5>Monthly Installments</h5>';
        scheduleHtml += '<table class="table table-hover table-sm">';
        scheduleHtml += '<tr><th scope="col">#</th><th scope="col">Date</th><th scope="col">Amount</th></tr>';

        var totalPayable = 0;

        // Helper: add months to date
        function addMonths(startDate, add) {

            let parts = startDate.split('-');

            let yyyy, mm;

            if (parts[0].length == 4) {
                // yyyy-mm-dd format
                yyyy = parseInt(parts[0]);
                mm = parseInt(parts[1]);
            } else {
                // dd-mm-yyyy format
                yyyy = parseInt(parts[2]);
                mm = parseInt(parts[1]);
            }

            // Add months safely without skipping
            mm = mm + add;

            // Handle year rollover
            while (mm > 12) {
                mm -= 12;
                yyyy++;
            }

            mm = ("0" + mm).slice(-2);
            return yyyy + "-" + mm + "-01";
        }

        // Clear previous hidden inputs
        $('#installments_table').empty();

        var hiddenInputs = '';

        if (loanType === 'ADVANCE') {
            // Simple: equal installment = amount / months
            var monthly = amount / months;

            for (var i = 1; i <= months; i++) {
                var rawDate = addMonths(startDate, i - 1); // get full date
                console.log('rawDate :', rawDate);
                var d = new Date(rawDate);

                var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                var instMonthYear = monthNames[d.getMonth()] + "-" + d.getFullYear();

                var instAmt = parseFloat(monthly.toFixed(2));
                totalPayable += instAmt;

                scheduleHtml += '<tr>' +
                    '<td>' + i + '</td>' +
                    '<td>' + instMonthYear + '</td>' +
                    '<td>' + instAmt.toFixed(2) + '</td>' +
                    '</tr>';

                hiddenInputs += '<input type="hidden" name="installment_no[]" value="' + i + '">';
                hiddenInputs += '<input type="hidden" name="installment_date[]" value="' + rawDate + '">';
                hiddenInputs += '<input type="hidden" name="installment_amount[]" value="' + instAmt.toFixed(2) + '">';
            }

        } else if (loanType === 'LOAN') {

            var principal = amount;
            var monthlyInterestRate = (rate / 12) / 100; // e.g. 12% → 1% per month

            if (method === 'FLAT') {
                var totalInterest = (principal * (rate / 100)) * (months / 12); // full period
                totalPayable = principal + totalInterest;
                var monthlyPay = totalPayable / months;

                for (var i = 1; i <= months; i++) {
                    var rawDate = addMonths(startDate, i - 1); // get full date
                    var d = new Date(rawDate);

                    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    var instMonthYear = monthNames[d.getMonth()] + "-" + d.getFullYear();

                    var instAmt = parseFloat(monthlyPay.toFixed(2));

                    scheduleHtml += '<tr>' +
                        '<td>' + i + '</td>' +
                        '<td>' + instMonthYear + '</td>' +
                        '<td>' + instAmt.toFixed(2) + '</td>' +
                        '</tr>';

                    hiddenInputs += '<input type="hidden" name="installment_no[]" value="' + i + '">';
                    hiddenInputs += '<input type="hidden" name="installment_date[]" value="' + rawDate + '">';
                    hiddenInputs += '<input type="hidden" name="installment_amount[]" value="' + instAmt.toFixed(2) + '">';
                }

            } else if (method === 'REDUCING') {
                // REDUCING: equal principal each month + interest on remaining principal
                var principalPart = principal / months;
                var remaining = principal;

                for (var i = 1; i <= months; i++) {
                    var interest = remaining * monthlyInterestRate;
                    var instAmt = principalPart + interest;
                    remaining -= principalPart;

                    var rawDate = addMonths(startDate, i - 1); // get full date
                    var d = new Date(rawDate);

                    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    var instMonthYear = monthNames[d.getMonth()] + "-" + d.getFullYear();
                    instAmt = parseFloat(instAmt.toFixed(2));
                    totalPayable += instAmt;

                    scheduleHtml += '<tr>' +
                        '<td>' + i + '</td>' +
                        '<td>' + instMonthYear + '</td>' +
                        '<td>' + instAmt.toFixed(2) + '</td>' +
                        '</tr>';

                    hiddenInputs += '<input type="hidden" name="installment_no[]" value="' + i + '">';
                    hiddenInputs += '<input type="hidden" name="installment_date[]" value="' + rawDate + '">';
                    hiddenInputs += '<input type="hidden" name="installment_amount[]" value="' + instAmt.toFixed(2) + '">';
                }
            }
        }

        scheduleHtml += '</table> </div>';

        $('#installments_table').html(scheduleHtml + hiddenInputs);

        $('#total_amount').val(totalPayable.toFixed(2));
        var empName = $("#employee_id option:selected").text();
        var type = $("#loan_type").val();
        var amountVal = parseFloat(amount).toFixed(2);
        var monthsVal = months;
        var rateVal = rate + "%";
        var dateVal = startDate;

        let perMonthInterest = 0;

        if (loanType === 'LOAN' && method === 'FLAT') {
            let totalInterest = (amount * (rate / 100) * (months / 12));
            perMonthInterest = (totalInterest / months).toFixed(2);
        } else if (loanType === 'LOAN' && method === 'REDUCING') {
            perMonthInterest = ((amount * (rate / 100)) / 12).toFixed(2);
        } else {
            // Advance has no interest
            perMonthInterest = "0.00";
        }

        var totalPay = totalPayable.toFixed(2);
        var totalInterestValue = (perMonthInterest * monthsVal).toFixed(2);

        var summaryHtml = `
            <div class="summary-box">
            <h5 class="mb-3">Advance / Loan Summary</h5>

            <div class="row mb-2">
                <div class="col-6 text-muted">Employee Name</div>
                <div class="col-6 fw-bold text-end">${empName}</div>
            </div>

            <div class="row mb-2">
                <div class="col-6 text-muted">Type</div>
                <div class="col-6 fw-bold text-end">${type}</div>
            </div>

            <div class="row mb-2">
                <div class="col-6 text-muted">Amount</div>
                <div class="col-6 fw-bold text-end">${amountVal}</div>
            </div>

            <div class="row mb-2">
                <div class="col-6 text-muted">Date Taken</div>
                <div class="col-6 fw-bold text-end">${dateVal}</div>
            </div>

            <div class="row mb-2">
                <div class="col-6 text-muted">No. of Months</div>
                <div class="col-6 fw-bold text-end">${monthsVal}</div>
            </div>`
        if (loanType === 'LOAN') {
            summaryHtml += `<div class="row mb-2">
                <div class="col-6 text-muted">Interest Rate</div>
                <div class="col-6 fw-bold text-end">${rateVal}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 text-muted">Monthly Interest</div>
                <div class="col-6 fw-bold text-end">${perMonthInterest}</div>
            </div>

            <div class="row mb-2">
                <div class="col-6 text-muted">Total Interest</div>
                <div class="col-6 fw-bold text-end">${totalInterestValue}</div>
            </div>`;
        }
        summaryHtml += `
            <hr class="my-2" />

            <div class="row">
                <div class="col-6 text-muted">Total Amount Payable</div>
                <div class="col-6 fw-bold text-end text-primary">${totalPay}</div>
            </div>
        </div>

        `;

        $("#loan_summary").html(summaryHtml);
        // Show schedule row when calculating
        document.getElementById('schedule_row').style.display = 'block';

    }
</script>
