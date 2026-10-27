<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$filter_user_id = isset($_POST['filter_user_id']) ? $_POST['filter_user_id'] : '';
$filter_module_id = isset($_POST['filter_module_id']) ? $_POST['filter_module_id'] : '';
$filter_role_id = isset($_POST['filter_role_id']) ? $_POST['filter_role_id'] : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';
$action_by = isset($_POST['action_by']) ? $_POST['action_by'] : '';

$url = API . "?mode=getmodulerolelogs&method=get&from_date=" . urlencode($from_date) . "&to_date=" . urlencode($to_date) . "&filter_user_id=" . urlencode($filter_user_id) . "&filter_module_id=" . urlencode($filter_module_id) . "&filter_role_id=" . urlencode($filter_role_id) . "&action=" . urlencode($action) . "&action_by=" . urlencode($action_by);
$apires = callAPI($url);
$result = json_decode($apires, 1);
$module_role_logs = isset($result['module_role_logs']) ? $result['module_role_logs'] : array();
$session_users = isset($_SESSION['users']) ? $_SESSION['users'] : array();
?>
<div class="col-12 table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">Sr. No</th>
                <th scope="col">User</th>
                <th scope="col">Action</th>
                <th scope="col">Module</th>
                <th scope="col">Role</th>
                <th scope="col">Modified By</th>
                <th scope="col">Modified At</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count((array)$module_role_logs) > 0){
                $i = 1;
                foreach($module_role_logs as $row){
                    $user_id = isset($row['user_id']) ? $row['user_id'] : '';
                    $action_type = isset($row['action_type']) ? $row['action_type'] : '';
                    $module_name = isset($row['module_name']) ? $row['module_name'] : '';
                    $role_name = isset($row['role_name']) ? $row['role_name'] : '';
                    $role_short_code = isset($row['role_short_code']) ? $row['role_short_code'] : '';
                    $row_action_by = isset($row['action_by']) ? $row['action_by'] : '';
                    $action_date = isset($row['action_date']) ? formatAmcScheduleDateTime($row['action_date']) : '';
                    $action_class = ($action_type == 'Removed') ? 'label-danger' : 'label-success';
                    $user_name = (isset($session_users[$user_id]) && $session_users[$user_id] != '') ? $session_users[$user_id] : '-';
                    $done_by_name = (isset($session_users[$row_action_by]) && $session_users[$row_action_by] != '') ? $session_users[$row_action_by] : '-';
                    $role_display = ($role_name != '') ? $role_name : (($role_short_code != '') ? $role_short_code : '-');
            ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars($user_name); ?></td>
                    <td><span class="label <?php echo $action_class; ?>"><?php echo htmlspecialchars($action_type); ?></span></td>
                    <td><?php echo ($module_name != '') ? htmlspecialchars($module_name) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($role_display); ?></td>
                    <td><?php echo htmlspecialchars($done_by_name); ?></td>
                    <td><?php echo ($action_date != '') ? htmlspecialchars($action_date) : '-'; ?></td>
                </tr>
            <?php
                    $i++;
                }
            }else{ ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No Module Role Logs found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
