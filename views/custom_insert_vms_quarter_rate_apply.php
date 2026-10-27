<?php
$_SESSION['apply_js']['multiselest'] = 1;

$current_fy_start = (int)date('n') >= 4 ? (int)date('Y') : ((int)date('Y') - 1);
$financial_years = array();
for ($offset = -2; $offset <= 2; $offset++) {
    $year = $current_fy_start + $offset;
    $financial_years[] = $year . '-' . substr((string)($year + 1), -2);
}

$quarters = array(
    'Q1' => 'Q1 (April-June)',
    'Q2' => 'Q2 (July-September)',
    'Q3' => 'Q3 (October-December)',
    'Q4' => 'Q4 (January-March)',
);

$form_data = array(
    'from_financial_year' => '',
    'from_quarter' => '',
    'to_financial_year' => '',
    'to_quarter' => '',
    'extension' => '',
);
$flash_message = '';
$flash_type = '';

if ($_POST['submit'] && $_POST['submit'] == 'insert') {
        $form_data['from_financial_year'] = $_POST['from_financial_year'];
        $form_data['from_quarter'] = $_POST['from_quarter'];
        $form_data['to_financial_year'] = $_POST['to_financial_year'];
        $form_data['to_quarter'] = $_POST['to_quarter'];
        $form_data['extension'] = $_POST['extension'];

        unset($_POST['submit']);
        $_POST['current_status'] = '0';
        $_POST['is_active'] = '0';
        $_POST['status_date'] = nowDate();
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=insertvmsquarterrateapply&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response, 1);

        if ($response["status"] == "sucess") {
            $flash_message = $response['msg'] != '' ? $response['msg'] : 'Quarter rate apply request saved successfully';
            $flash_type = 'success';
            $form_data = array(
                'from_financial_year' => '',
                'from_quarter' => '',
                'to_financial_year' => '',
                'to_quarter' => '',
                'extension' => '',
            );
        } elseif ($response["status"] == "duplicate") {
            $flash_message = $response['msg'] != '' ? $response['msg'] : 'This quarter apply mapping already exists';
            $flash_type = 'warning';
        } else {
            $flash_message = $response['msg'] != '' ? $response['msg'] : 'Unable to save quarter rate apply request';
            $flash_type = 'danger';
        }
    }
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Quarter Rate Apply</h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-infisuite mt-2">
            <div class="card-body p-4">
                <form id="vmsQuarterRateApplyForm" method="post">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <span>From Financial Year <span style="color: red;">*</span></span>
                            <select aria-label="From Financial Year" class="form-select js-example-basic-single" id="from_financial_year" name="from_financial_year" required>
                                <option value="">Select Financial Year</option>
                                <?php if (count((array)$financial_years) > 0) {
                                    foreach ($financial_years as $fy) {
                                        $selected = ($form_data['from_financial_year'] == $fy) ? 'selected' : '';
                                        echo "<option value='{$fy
}' {$selected
}>{$fy
}</option>";
                                    }
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="from_financial_year_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>From Quarter <span style="color: red;">*</span></span>
                            <select aria-label="From Quarter" class="form-select js-example-basic-single" id="from_quarter" name="from_quarter" required>
                                <option value="">Select Quarter</option>
                                <?php if (count((array)$quarters) > 0) {
                                    foreach ($quarters as $q_key => $q_label) {
                                        $selected = ($form_data['from_quarter'] == $q_key) ? 'selected' : '';
                                        echo "<option value='{$q_key
}' {$selected
}>{$q_label
}</option>";
                                    }
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="from_quarter_err"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-4">
                            <span>To Financial Year <span style="color: red;">*</span></span>
                            <select aria-label="To Financial Year" class="form-select js-example-basic-single" id="to_financial_year" name="to_financial_year" required>
                                <option value="">Select Financial Year</option>
                                <?php if (count((array)$financial_years) > 0) {
                                    foreach ($financial_years as $fy) {
                                        $selected = ($form_data['to_financial_year'] == $fy) ? 'selected' : '';
                                        echo "<option value='{$fy
}' {$selected
}>{$fy
}</option>";
                                    }
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="to_financial_year_err"></div>
                        </div>
                        <div class="form-group col-lg-4">
                            <span>To Quarter <span style="color: red;">*</span></span>
                            <select aria-label="To Quarter" class="form-select js-example-basic-single" id="to_quarter" name="to_quarter" required>
                                <option value="">Select Quarter</option>
                                <?php if (count((array)$quarters) > 0) {
                                    foreach ($quarters as $q_key => $q_label) {
                                        $selected = ($form_data['to_quarter'] == $q_key) ? 'selected' : '';
                                        echo "<option value='{$q_key
}' {$selected
}>{$q_label
}</option>";
                                    }
                                } ?>
                            </select>
                            <div style="display: none; color: red;" id="to_quarter_err"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-4">
                            <span>Extension <span style="color: red;">*</span></span>
                            <input aria-label="Extension" class="form-control" type="text" id="extension" name="extension" value="<?php echo htmlspecialchars($form_data['extension']); ?>" required />
                            <div style="display: none; color: red;" id="extension_err"></div>
                        </div>
                    </div>

                    <div class="border-top">
                        <button name="submit" value="insert" class="btn waves-effect waves-light btn-primary mt-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#vmsQuarterRateApplyForm").submit(function(e) {
            if (!validateVmsQuarterRateApplyForm()) {
                e.preventDefault();
            }
        });
    });

    function validateVmsQuarterRateApplyForm() {
        var isValid = true;
        var fields = [
            { id: 'from_financial_year', err: 'from_financial_year_err', msg: 'From Financial Year is required' },
            { id: 'from_quarter', err: 'from_quarter_err', msg: 'From Quarter is required' },
            { id: 'to_financial_year', err: 'to_financial_year_err', msg: 'To Financial Year is required' },
            { id: 'to_quarter', err: 'to_quarter_err', msg: 'To Quarter is required' },
            { id: 'extension', err: 'extension_err', msg: 'Extension is required' }
        ];

        if (fields.length > 0) {
            for (var i = 0; i < fields.length; i++) {
                var field = fields[i];
                var value = $.trim($("#" + field.id).val());
                if (value == '') {
                    $("#" + field.err).text(field.msg).show();
                    isValid = false;
                } else {
                    $("#" + field.err).hide();
                }
            }
        }

        return isValid;
    }
</script>

<?php if ($flash_message != '') {
    $notify_icon = 'fa fa-thumbs-up';
    $notify_class = 'form-label label label-success';
    if ($flash_type == 'warning') {
        $notify_icon = 'fa fa-exclamation-triangle';
        $notify_class = 'form-label label label-warning';
    } elseif ($flash_type == 'danger') {
        $notify_icon = 'fa fa-thumbs-down';
        $notify_class = 'form-label label label-danger';
    }
    ?>
<script>
    window.addEventListener("load", function() {
        tostarnotify('top', 'right', '<?php echo $notify_icon; ?>', '<?php echo $notify_class; ?>', 'animated flipInX', 'animated flipOutX',
            '<?php echo addslashes($flash_message); ?>', '');
    });
</script>
<?php } ?>
