<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$view_type = isset($_POST['view_type']) ? $_POST['view_type'] : 'summary';
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$frequency = isset($_POST['frequency']) ? $_POST['frequency'] : '';
$email_id = isset($_POST['email_id']) ? $_POST['email_id'] : '';
$domain_name = isset($_POST['domain_name']) ? $_POST['domain_name'] : '';
$device_source = isset($_POST['device_source']) ? $_POST['device_source'] : '';

if($view_type == 'detail'){
    $url = API . "?mode=getfailedloginattemptdetails&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&email_id=" . urlencode($email_id);
    $apires = callAPI($url);
    $result = json_decode($apires, 1);
    $details = isset($result['failed_login_attempt_details']) ? $result['failed_login_attempt_details'] : array();
?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Sl. No</th>
                    <th scope="col">Email</th>

                    <th scope="col">Device</th>


                    <th scope="col">loggin at</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count((array)$details) > 0){
                    $i = 1;
                    foreach($details as $row){
                        $row_email = isset($row['email_id']) ? $row['email_id'] : '';
                        $row_password = isset($row['password']) ? $row['password'] : '';
                        $row_device = isset($row['device_source']) ? $row['device_source'] : '';
                        $row_domain = isset($row['domain_name']) ? $row['domain_name'] : '';
                        $row_zqr_db = isset($row['zqr_db_name']) ? $row['zqr_db_name'] : '';
                        $row_date = isset($row['created_date']) ? formatAmcScheduleDateTime($row['created_date']) : '';
                ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo htmlspecialchars($row_email); ?></td>
                        <td><?php echo ($row_device != '') ? htmlspecialchars($row_device) : '-'; ?></td>
                        <td><?php echo ($row_date != '') ? htmlspecialchars($row_date) : '-'; ?></td>
                    </tr>
                <?php
                        $i++;
                    }
                }else{ ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No attempts found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
    exit;
}

$url = API . "?mode=getfailedloginattempts&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&frequency=" . urlencode($frequency) . "&email_id=" . urlencode($email_id) . "&domain_name=" . urlencode($domain_name) . "&device_source=" . urlencode($device_source);
$apires = callAPI($url);
$result = json_decode($apires, 1);
$failed_login_attempts = isset($result['failed_login_attempts']) ? $result['failed_login_attempts'] : array();
?>
<div class="col-12 table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Sl. No</th>
                <th scope="col">Email</th>
                <th scope="col">Attempts</th>

                <th scope="col">Device Source</th>
                <th scope="col">Last Attempt</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count((array)$failed_login_attempts) > 0){
                $i = 1;
                foreach($failed_login_attempts as $row){
                    $row_email = isset($row['email_id']) ? $row['email_id'] : '';
                    $attempt_count = isset($row['attempt_count']) ? $row['attempt_count'] : 0;
                    $row_domain = isset($row['domain_name']) ? $row['domain_name'] : '';
                    $row_device = isset($row['device_source']) ? $row['device_source'] : '';
                    $last_attempt = isset($row['last_attempt_date']) ? formatAmcScheduleDateTime($row['last_attempt_date']) : '';
            ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars($row_email); ?></td>
                    <td>
                        <a href="javascript:void(0);" class="text-primary failed-login-count-link" data-email="<?php echo htmlspecialchars($row_email); ?>">
                            <?php echo (int)$attempt_count; ?>
                        </a>
                    </td>
                    <td><?php echo ($row_device != '') ? htmlspecialchars($row_device) : '-'; ?></td>
                    <td><?php echo ($last_attempt != '') ? htmlspecialchars($last_attempt) : '-'; ?></td>
                </tr>
            <?php
                    $i++;
                }
            }else{ ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No Failed Login Attempts found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
