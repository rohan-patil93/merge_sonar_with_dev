<?php
$_SESSION['apply_js']['multiselest'] = 1;
$table_name = 'manufacturing_forecast';

// ===== SAVE LOGIC =====
if (isset($_POST['submit']) && $_POST['submit'] === 'insert') {
    $data = $_POST;
    unset($data['submit'],$data['record_id']);
    $yearly = floatval($data['forecast_yearly'] ?? 0);
    $half1  = floatval($data['forecast_half_1'] ?? 0);
    $half2  = floatval($data['forecast_half_2'] ?? 0);
    $quarter = [
        floatval($data['forecast_quarterly_1'] ?? 0),
        floatval($data['forecast_quarterly_2'] ?? 0),
        floatval($data['forecast_quarterly_3'] ?? 0),
        floatval($data['forecast_quarterly_4'] ?? 0)
    ];
    $months = [];
    for ($i = 1; $i <= 12; $i++) { $months[$i] = floatval($data["forecast_month_$i"] ?? 0);
}

    switch ($data['interval_type']) {
        case 'yearly':
            $perHalf = round($yearly / 2, 2);
            $perQuarter = round($yearly / 4, 2);
            $perMonth = round($yearly / 12, 2);
            $data['forecast_half_1'] = $perHalf;
            $data['forecast_half_2'] = $perHalf;
            for ($i = 1; $i <= 4; $i++) { $data["forecast_quarterly_$i"] = $perQuarter;
}
            for ($i = 1; $i <= 12; $i++) { $data["forecast_month_$i"] = $perMonth;
}
            $data['total_forecast'] = round($yearly, 2);
            break;

        case 'half_yearly':
            $total = $half1 + $half2;
            $data['forecast_yearly'] = $total;
            $data['forecast_quarterly_1'] = round($half1 / 2, 2);
            $data['forecast_quarterly_2'] = round($half1 / 2, 2);
            $data['forecast_quarterly_3'] = round($half2 / 2, 2);
            $data['forecast_quarterly_4'] = round($half2 / 2, 2);
            for ($i = 1; $i <= 6; $i++) { $data["forecast_month_$i"] = round($half1 / 6, 2);
}
            for ($i = 7; $i <= 12; $i++) { $data["forecast_month_$i"] = round($half2 / 6, 2);
}
            $data['total_forecast'] = round($total,2);
            break;

        case 'quarterly':
            $total = array_sum($quarter);
            $data['forecast_yearly'] = $total;
            $data['forecast_half_1'] = round($quarter[0] + $quarter[1], 2);
            $data['forecast_half_2'] = round($quarter[2] + $quarter[3] ,2);
            $data['forecast_month_1'] = $data['forecast_month_2'] = $data['forecast_month_3'] = round($quarter[0] / 3, 2);
            $data['forecast_month_4'] = $data['forecast_month_5'] = $data['forecast_month_6'] = round($quarter[1] / 3, 2);
            $data['forecast_month_7'] = $data['forecast_month_8'] = $data['forecast_month_9'] = round($quarter[2] / 3, 2);
            $data['forecast_month_10'] = $data['forecast_month_11'] = $data['forecast_month_12'] = round($quarter[3] / 3, 2);
            $data['total_forecast'] = round($total, 2);
            break;

        case 'monthly':
            $total = array_sum($months);
            $data['forecast_yearly'] = $total;
            $data['forecast_quarterly_1'] = $months[1] + $months[2] + $months[3];
            $data['forecast_quarterly_2'] = $months[4] + $months[5] + $months[6];
            $data['forecast_quarterly_3'] = $months[7] + $months[8] + $months[9];
            $data['forecast_quarterly_4'] = $months[10] + $months[11] + $months[12];
            $data['forecast_half_1'] = array_sum(array_slice($months, 1, 6));
            $data['forecast_half_2'] = array_sum(array_slice($months, 7, 6));
            $data['total_forecast'] = round($total,2);
            break;

                        default:
                            break;
}
    if(isset($_POST['record_id']) && $_POST['record_id'] != ''){
        $data['updated_by']   = $_SESSION['userid'];
        $data['updated_date'] = date('Y-m-d H:i:s');
        $url = API . "?mode=updaterowtabledata&rid=" . $_POST['record_id'] . "&table=" . $table_name . "&method=post";
        $res = file_post_contents($url, $data, 'update');
    }else{
        $data['company_id']   = $_SESSION['company_id'];
        $data['created_by']   = $_SESSION['userid'];
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['is_active']    = '0';
        $data['current_status'] = '0';
        $url = API . "?mode=adddata&table=$table_name&method=post";
        $res = json_decode(file_post_contents($url, $data, 'insert'), true);
    }

}

// ===== FETCH =====
$rid = '';
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
}
$url = API . "?mode=getmanufacturingforecasedata&method=get&rid=" . $rid;
$result = json_decode(callAPI($url), true);
$products = $result['items_res'] ?? [];
$data = $result['data'] ?? [];

$showMonthly   = ($data['interval_type'] == 'monthly') ? '' : 'none';
$showQuarterly = ($data['interval_type'] == 'quarterly') ? '' : 'none';
$showHalf      = ($data['interval_type'] == 'half_yearly') ? '' : 'none';
$showYearly    = ($data['interval_type'] == 'yearly') ? '' : 'none';
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Manufacturing Forecast</h5>
        </div>
        <div class="card-body p-4">
            <form method="post" id="forecastForm">

                <div class="row">
                    <div class="col-md-4 form-group">
                        <span>Year <span class="text-danger">*</span></span>
                        <select aria-label="Year" name="year" id="year" class="form-select js-example-basic-single select1">
                            <option value="">Select Year</option>
                            <?php
                            $currentYear = date('Y');
                            for ($i = 0; $i <= 5; $i++) {
                                $y = $currentYear + $i;
                                echo "<option value='$y' " . (($data['year'] == $y) ? 'selected' : '') . ">$y</option>";
                            }
                            ?>
                        </select>
                        <div id="year_err" style="display:none;color:red;"></div>
                    </div>

                    <div class="col-md-4 form-group">
                        <span>Product <span class="text-danger">*</span></span>
                        <select aria-label="Product" name="product_id" id="product_id" class="form-select js-example-basic-single select1">
                            <option value="">Select Product</option>
                            <?php foreach ($products as $row): ?>
                                <option value="<?= $row['id'] ?>" <?= ($data['product_id'] == $row['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($row['item_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="product_id_err" style="display:none;color:red;"></div>
                    </div>

                    <div class="col-md-4 form-group">
                        <span>Interval <span class="text-danger">*</span></span>
                        <select aria-label="Interval Type" name="interval_type" id="interval_type" class="form-select js-example-basic-single select1">
                            <option value="">Select Interval</option>
                            <option value="monthly" <?= ($data['interval_type'] == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                            <option value="quarterly" <?= ($data['interval_type'] == 'quarterly') ? 'selected' : ''; ?>>Quarterly</option>
                            <option value="half_yearly" <?= ($data['interval_type'] == 'half_yearly') ? 'selected' : ''; ?>>Half Yearly</option>
                            <option value="yearly" <?= ($data['interval_type'] == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
                        </select>
                        <div id="interval_type_err" style="display:none;color:red;"></div>
                    </div>
                </div>


                <div id="monthly_div" class="row mt-3" style="display:<?= $showMonthly ?>;">
                    <?php
                    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    foreach ($months as $i => $m) {
                        $idx = $i + 1;
                        echo "<div class='col-md-3 form-group'>
                        <span>$m</span>
                        <input aria-label='Forecast Month' type='number' step='any' min='0' class='form-control interval-field text-right'
                            name='forecast_month_$idx' id='forecast_month_$idx'
                            value='" . ($data["forecast_month_$idx"] ?? '') . "' placeholder='0'>
                        <div id='forecast_month_{$idx
}_err' style='display:none;color:red;'></div>
                        </div>";
                    }
                    ?>
                </div>

                <!-- Quarterly -->
                <div id="quarterly_div" class=" row mt-3" style="display:<?= $showQuarterly ?>;">
                    <div class="col-md-3 form-group"><span>Quarter 1</span><input aria-label="Forecast Quarterly 1" type="number" step="any" min="0" class="form-control interval-field text-right" name="forecast_quarterly_1" id="forecast_quarterly_1" value="<?= $data['forecast_quarterly_1'] ?? '' ?>" placeholder="0"><div id='forecast_quarterly_1_err' style='display:none;color:red;'></div></div>
                    <div class="col-md-3 form-group"><span>Quarter 2</span><input aria-label="Forecast Quarterly 2" type="number" step="any" min="0" class="form-control interval-field text-right" name="forecast_quarterly_2" id="forecast_quarterly_2" value="<?= $data['forecast_quarterly_2'] ?? '' ?>" placeholder="0"><div id='forecast_quarterly_2_err' style='display:none;color:red;'></div></div>
                    <div class="col-md-3 form-group"><span>Quarter 3</span><input aria-label="Forecast Quarterly 3" type="number" step="any" min="0" class="form-control interval-field text-right" name="forecast_quarterly_3" id="forecast_quarterly_3" value="<?= $data['forecast_quarterly_3'] ?? '' ?>" placeholder="0"><div id='forecast_quarterly_3_err' style='display:none;color:red;'></div></div>
                    <div class="col-md-3 form-group"><span>Quarter 4</span><input aria-label="Forecast Quarterly 4" type="number" step="any" min="0" class="form-control interval-field text-right" name="forecast_quarterly_4" id="forecast_quarterly_4" value="<?= $data['forecast_quarterly_4'] ?? '' ?>" placeholder="0"><div id='forecast_quarterly_4_err' style='display:none;color:red;'></div></div>
                </div>

                <!-- Half Yearly -->
                <div id="half_yearly_div" class="row mt-3" style="display:<?= $showHalf ?>;">
                    <div class="col-md-4 form-group"><span>Half 1</span><input aria-label="Forecast Half 1" type="number" step="any" min="0" class="form-control interval-field text-right" name="forecast_half_1" id="forecast_half_1" value="<?= $data['forecast_half_1'] ?? '' ?>" placeholder="0"><div id='forecast_half_1_err' style='display:none;color:red;'></div></div>
                    <div class="col-md-4 form-group"><span>Half 2</span><input aria-label="Forecast Half 2" type="number" step="any" min="0" class="form-control interval-field text-right" name="forecast_half_2" id="forecast_half_2" value="<?= $data['forecast_half_2'] ?? '' ?>" placeholder="0"><div id='forecast_half_2_err' style='display:none;color:red;'></div></div>
                </div>

                <!-- Yearly -->
                <div id="yearly_div" class="row mt-3" style="display:<?= $showYearly ?>;">
                    <div class="col-md-4 form-group"><span>Yearly</span><input aria-label="Forecast Yearly" type="number" step="any" min="0" class="form-control interval-field text-right" name="forecast_yearly" id="forecast_yearly" value="<?= $data['forecast_yearly'] ?? '' ?>" placeholder="0"><div id='forecast_yearly_err' style='display:none;color:red;'></div></div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4 form-group">
                        <span>Total Forecast</span>
                        <input aria-label="Total Forecast" type="number" readonly id="total_forecast" name="total_forecast" class="form-control text-right" value="<?= $data['total_forecast'] ?? '' ?>">
                    </div>
                </div>

                <div class="border-top mt-3 pt-3">
                        <input type="hidden"  id="record_id" name="record_id"  value="<?= $data['id'] ?? '' ?>">

                    <button name="submit" value="insert" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>
<script>
    $(function() {

        // Show/hide by interval
        $('#interval_type').change(function() {
            let val = $(this).val();
            $('#monthly_div,#quarterly_div,#half_yearly_div,#yearly_div').hide();
            if (val === 'monthly') $('#monthly_div').show();
            else if (val === 'quarterly') $('#quarterly_div').show();
            else if (val === 'half_yearly') $('#half_yearly_div').show();
            else if (val === 'yearly') $('#yearly_div').show();
            recalcTotal();
        });

        // Total
        $(document).on('input', '.interval-field', function() {
            const id = $(this).attr('id');
            $('#' + id + '_err').hide();
            recalcTotal();
        });

        function recalcTotal() {
            let total = 0;
            $('.interval-field:visible').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_forecast').val(total);
        }

        // Validation
        $('#forecastForm').submit(function(e) {
            let valid = true;
            $('div[id$="_err"]').hide();

            if (!$('#year').val()) {
                $('#year_err').text('Please select year').show();
                valid = false;
            }
            if (!$('#product_id').val()) {
                $('#product_id_err').text('Please select product').show();
                valid = false;
            }
            if (!$('#interval_type').val()) {
                $('#interval_type_err').text('Please select interval').show();
                valid = false;
            }

            let intervalType = $('#interval_type').val();
            $('#' + intervalType + '_div .interval-field').each(function() {
                const id = $(this).attr('id');
                const val = $(this).val().trim();  // ensure no space issues
                if (val == ''|| isNaN(val) || val == null) {
                    $('#' + id + '_err').text('Required').show();
                    valid = false;
                }
            });

            if (!valid) e.preventDefault();
        });
    });
</script>
