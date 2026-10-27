<?php
$rid = isset($_REQUEST['rid']) ? trim($_REQUEST['rid']) : '';
if($_REQUEST['AJAX'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}

$display_employee_name = '';
if(isset($_REQUEST['employee_name']) && trim((string)$_REQUEST['employee_name']) != ''){
    $display_employee_name = trim((string)$_REQUEST['employee_name']);
}

$display_attendance_date = '';
if(isset($_REQUEST['attendance_date']) && trim((string)$_REQUEST['attendance_date']) != ''){
    $display_attendance_date = trim((string)$_REQUEST['attendance_date']);
}

$history_response = array(
    'status' => 'success',
    'message' => 'No history for this',
    'attendance' => array(),
    'audits' => array(),
    'users' => array()
);

if($rid != ''){
    $url = API."?mode=getattendanceaudithistorybyrid&rid=".urlencode($rid);
    $apires = callAPI($url);
    $decoded = json_decode($apires, true);
    if(is_array($decoded)){
        $history_response = $decoded;
    }
}

$attendance = isset($history_response['attendance']) && is_array($history_response['attendance']) ? $history_response['attendance'] : array();
$audits = isset($history_response['audits']) && is_array($history_response['audits']) ? $history_response['audits'] : array();
$users = isset($history_response['users']) && is_array($history_response['users']) ? $history_response['users'] : array();

function attendanceHistorySafe($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function attendanceHistoryUserName($user_id, $users){
    $users = $users ?? [];
    $user_id = trim((string)$user_id);
    if($user_id == '' || $user_id == '1' || $user_id == '0'){
        return 'Biometric Machine';
    }
    if(isset($users[$user_id]) && trim((string)$users[$user_id]) != ''){
        return $users[$user_id];
    }
    return 'User #'.$user_id;
}

function attendanceHistoryDecodeDetails($json_string){
    if(trim((string)$json_string) == ''){
        return array();
    }
    $decoded = json_decode(urldecode($json_string), true);
    return is_array($decoded) ? $decoded : array();
}

function attendanceHistoryChangedFields($before_json, $after_json){
    $before = attendanceHistoryDecodeDetails($before_json);
    $after = attendanceHistoryDecodeDetails($after_json);
    $changed_fields = array();
    foreach((array)$after as $field_name => $field_value){
        $before_value = isset($before[$field_name]) ? $before[$field_name] : '';
        if($field_value != $before_value){
            $changed_fields[] = array(
                'label'  => viewTitlePage($field_name),
                'before' => (string)$before_value,
                'after'  => (string)$field_value
            );
        }
    }
    return $changed_fields;
}

function attendanceHistoryDetailPairs($attendance){
    $detail_pairs = array();
    $fields = array(
        'attendace_date' => 'Attendance Date',
        'clock_in' => 'Clock In',
        'clock_out' => 'Clock Out',
        'check_in_type' => 'Check In Type',
        'check_out_type' => 'Check Out Type',
        'device_type' => 'Device Type',
        'swipes' => 'Swipes',
        'attendance_key_short_code' => 'Attendance Key',
        'shift_code' => 'Shift Code'
    );
    foreach($fields as $field_name => $label){
        if(isset($attendance[$field_name]) && trim((string)$attendance[$field_name]) != ''){
            $detail_pairs[] = $label.': '.(string)$attendance[$field_name];
        }
    }
    return $detail_pairs;
}

$has_history = !empty($attendance);
$created_actor = $has_history ? attendanceHistoryUserName(isset($attendance['created_by']) ? $attendance['created_by'] : '', $users) : '';
$attendance_details = $has_history ? attendanceHistoryDetailPairs($attendance) : array();

$audits_with_changes = array();
foreach((array)$audits as $audit_row){
    $changed_fields_row = attendanceHistoryChangedFields(isset($audit_row['before_transaction_details']) ? $audit_row['before_transaction_details'] : '', isset($audit_row['after_transaction_details']) ? $audit_row['after_transaction_details'] : '');
    if(count((array)$changed_fields_row) > 0){
        $audit_row['_changed_fields'] = $changed_fields_row;
        $audits_with_changes[] = $audit_row;
    }
}
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Attendance Audit History</h5>
            <div class="card-header-right">
                <button type="button" class="btn-primary" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="bg-light p-3 rounded border">
                        <h6 class="mb-2">Attendance Record</h6>
                        <p class="mb-1 text-muted">RID: <?php echo attendanceHistorySafe($rid != '' ? $rid : 'NA'); ?></p>
                        <p class="mb-1 text-muted">Employee: <?php echo attendanceHistorySafe($display_employee_name != '' ? $display_employee_name : 'NA'); ?></p>
                        <p class="mb-0 text-muted">Attendance Date: <?php echo attendanceHistorySafe($display_attendance_date != '' ? $display_attendance_date : 'NA'); ?></p>
                    </div>
                </div>
            </div>

            <?php if($rid == '' || !$has_history){ ?>
                <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No history for this</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                </div>
            <?php }else{ ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <h5 class="mb-3">Created At</h5>
                            <div class="row p-t-20 p-b-20 border-top border-bottom today-border">
                                <div class="col-lg-2 text-start">
                                    <p><?php echo attendanceHistorySafe($attendance['created_date'] != '' ? date('d M Y h:i:s A', strtotime($attendance['created_date'])) : '-'); ?></p>
                                </div>
                                <div class="col-lg-1 text-center" style="z-index:1">
                                    <span class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon bg-white">
                                        <span class="ti-plus"></span>
                                    </span>
                                </div>
                                <div class="col-lg-9">
                                    <div class="align-middle">
                                        <span class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo attendanceHistorySafe(initials($created_actor)); ?></span>

                                        <div class="d-inline-block width73 text-capitalize align-top">
                                            <h6 class="mb-1"><?php echo attendanceHistorySafe($created_actor); ?> created attendance record -<?php echo attendanceHistorySafe($attendance['id']); ?></h6>
                                            <?php if(count((array)$attendance_details) > 0){ ?>
                                                <p class="text-muted m-b-0"><?php echo attendanceHistorySafe(implode(' | ', $attendance_details)); ?></p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5 class="mb-3">Updated At</h5>
                            <?php if(count((array)$audits_with_changes) == 0){ ?>
                                <div class="card-px text-center py-20 border-top border-bottom">
                                    <p class="text-muted f-16 fw-bold m-b-0"><i class="ti-alert text-warning"></i> No update history found</p>
                                </div>
                            <?php }else{ ?>
                                <?php foreach($audits_with_changes as $audit){
                                    $audit_actor_id = isset($audit['created_by']) && trim((string)$audit['created_by']) != '' ? $audit['created_by'] : (isset($audit['user_id']) ? $audit['user_id'] : '');
                                    $audit_actor = attendanceHistoryUserName($audit_actor_id, $users);
                                    $changed_fields = isset($audit['_changed_fields']) && is_array($audit['_changed_fields']) ? $audit['_changed_fields'] : array();
                                    $icon_type = '<span class="ti-pencil"></span>';
                                    if(isset($audit['type']) && $audit['type'] == 'INSERT'){
                                        $icon_type = '<span class="ti-plus"></span>';
                                    }elseif(isset($audit['type']) && $audit['type'] == 'DELETE'){
                                        $icon_type = '<span class="ti-trash"></span>';
                                    }elseif(isset($audit['type']) && $audit['type'] == 'VIEW'){
                                        $icon_type = '<span class="ti-eye"></span>';
                                    }
                                ?>
                                <div class="row p-t-20 p-b-20 border-top border-bottom today-border">
                                    <div class="col-lg-2 text-start">
                                        <p><?php echo attendanceHistorySafe(isset($audit['created_date']) && $audit['created_date'] != '' ? date('d M Y h:i:s A', strtotime($audit['created_date'])) : '-'); ?></p>
                                    </div>
                                    <div class="col-lg-1 text-center" style="z-index:1">
                                        <span class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon bg-white"><?php echo $icon_type; ?></span>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="align-middle">
                                            <span class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo attendanceHistorySafe(initials($audit_actor)); ?></span>
                                            <div class="d-inline-block width73 text-capitalize align-top">
                                                <h6 class="mb-1"><?php echo attendanceHistorySafe($audit_actor); ?> <span class="f-w-600"><?php echo attendanceHistorySafe(isset($audit['type']) ? $audit['type'] : 'UPDATE'); ?></span> attendance record -<?php echo attendanceHistorySafe(isset($audit['transaction_id']) ? $audit['transaction_id'] : $attendance['id']); ?></h6>
                                                <?php if(count((array)$changed_fields) > 0){ ?>
                                            <table style="font-size:12px;margin-top:6px;border-collapse:collapse;width:100%;">
                                                <thead>
                                                    <tr style="background:#f5f5f5;">
                                                        <th scope="col" style="padding:4px 8px;border:1px solid #ddd;text-align:left;">Field</th>
                                                        <th scope="col" style="padding:4px 8px;border:1px solid #ddd;text-align:left;">Before</th>
                                                        <th scope="col" style="padding:4px 8px;border:1px solid #ddd;text-align:left;">After</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($changed_fields as $cf){ ?>
                                                        <tr>
                                                            <td style="padding:4px 8px;border:1px solid #ddd;font-weight:600;"><?php echo attendanceHistorySafe($cf['label']); ?></td>
                                                            <td style="padding:4px 8px;border:1px solid #ddd;color:#d32f2f;"><?php echo attendanceHistorySafe($cf['before'] != '' ? $cf['before'] : '-'); ?></td>
                                                            <td style="padding:4px 8px;border:1px solid #ddd;color:#2e7d32;"><?php echo attendanceHistorySafe($cf['after'] != '' ? $cf['after'] : '-'); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                                        <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
