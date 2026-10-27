<?php
$_SESSION['apply_js']['multiselest'] = "1";

$table_name = 'project_timesheets';

// Determine selected date and calculate Monday-Sunday week bounds
$selected_date = date('Y-m-d');
if(isset($_GET['CHANGE_DATE']) && !empty($_GET['CHANGE_DATE'])){
    $selected_date = date('Y-m-d', strtotime($_GET['CHANGE_DATE']));
} elseif(isset($_POST['selected_date']) && !empty($_POST['selected_date'])){
    $selected_date = date('Y-m-d', strtotime($_POST['selected_date']));
}

$start_date = date('Y-m-d', strtotime('monday this week', strtotime($selected_date)));
$end_date   = date('Y-m-d', strtotime('sunday this week', strtotime($selected_date)));
$prev_week  = date('Y-m-d', strtotime('-1 week', strtotime($start_date)));
$next_week  = date('Y-m-d', strtotime('+1 week', strtotime($start_date)));

// Resolve Employee ID
$emp_id = $_SESSION['emp_id'] ?? 0;
if(isset($_GET['EMPID']) && !empty($_GET['EMPID'])){
    $emp_id = intval($_GET['EMPID']);
} elseif(isset($_GET['EMP']) && !empty($_GET['EMP'])){
    $emp_id = intval($_GET['EMP']);
}
if(empty($emp_id) && isset($_SESSION['userid'])){
    global $callConfig;
    $emp_row = $callConfig->getRow("SELECT id FROM employees WHERE user_id = ".intval($_SESSION['userid'])." AND is_active = 0");
    if($emp_row && !empty($emp_row->id)){
        $emp_id = $emp_row->id;
        $_SESSION['emp_id'] = $emp_id;
    }
}

// Call API for weekly timesheet entries (NO Work Order needed)
$url = API."?mode=getweeklytimesheetentries&start_date=".$start_date."&end_date=".$end_date."&empid=".$emp_id;
$configresponse = callAPI($url);
$data = json_decode($configresponse, 1);

$company_id = !empty($_SESSION['company_id']) ? intval($_SESSION['company_id']) : 1;

$work_array = array(
    'P' => array('name' => 'Present',  'code' => 'P', 'color' => '#2a9d8f'),
    'A' => array('name' => 'Absent',   'code' => 'A', 'color' => '#d62828'),
    'H' => array('name' => 'Holiday',  'code' => 'H', 'color' => '#d5bdaf'),
    'Z' => array('name' => 'Half Day', 'code' => 'Z', 'color' => '#e76f51'),
    'L' => array('name' => 'Leave',    'code' => 'L', 'color' => '#e9c46a'),
    'O' => array('name' => 'Week Off', 'code' => 'O', 'color' => '#eeeeee')
);

$weekdays = $data['working_days'] ?? array('monday','tuesday','wednesday','thursday','friday');

// Filter timesheets into active draft vs above-active (submitted / approved)
$listactive = array(); 
$aboveactive = array();
$approved_count = 0;
foreach((array)($data['timesheet'] ?? array()) as $listtime){
    if(($listtime['current_status'] ?? 0) == 0){
        $listactive[] = $listtime;
    } else {
        $aboveactive[] = $listtime;
        if(($listtime['current_status'] ?? 0) == 199){
            $approved_count++;
        }
    }
}

$cur_status = 10;
if(isset($_SESSION['user']['approval_manager']) && isset($_SESSION['user']['reports_to']) && $_SESSION['user']['approval_manager'] != $_SESSION['user']['reports_to']){
    $cur_status = 8;
}

$statusprogress = gettableStatusArray('project_timesheets');
$sprogress = $statusprogress['full'] ?? array();
?>

<style type="text/css">
.week_table tr th { 
    position: sticky; top: 0; color: #FFFFFF; border-width: 2px; border-color: #333333; text-align: center;
}
.week_table tr td {
    height: 120px; border-width: 2px; border-color: #e0e0e0; padding: 12px; vertical-align: top;
}
.week_table tr td.week_p { background: #2a9d8f33; border-top: 3px solid #2a9d8f; }
.week_table tr td.week_a { background: #d6282833; border-top: 3px solid #d62828; }
.week_table tr td.week_h { background: #d5bdaf4d; border-top: 3px solid #b5838d; }
.week_table tr td.week_z { background: #e76f5133; border-top: 3px solid #e76f51; }
.week_table tr td.week_l { background: #e9c46a4d; border-top: 3px solid #e9c46a; }
.week_table tr td.week_o { background: #f8f9fa; border-top: 3px solid #cccccc; }

.btn-att-group {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
    align-items: center;
}
.badge-hover {
    box-shadow: none; padding: 0; width: 25px; height: 22px; border-radius: 4px; line-height: 22px; text-align: center; color: #fff; font-weight: 600; display: inline-block; cursor: pointer; transition: transform 0.1s, opacity 0.1s;
}
.badge-hover:hover { opacity: 0.85; transform: scale(1.08); }
.badge-sec { display: flex; flex-wrap: wrap; gap: 8px; }
.week-nav-btn { height: 38px; line-height: 24px; }
.modal-header .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
</style>

<script type="text/javascript">
    function submitWeeklyWorkflowPopUp(status, start_date, end_date){
        var table_name = 'project_timesheets';
        var emp_id = $("#emp_id").val();
        jQuery.ajax({
           url: "views/custom_insert_week_projecttimesheet_status_popup.php",
           data: 'table=' + table_name + '&status=' + status + '&start_date=' + start_date + '&end_date=' + end_date + '&emp_id=' + emp_id,
           type: "POST",
           success: function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
          },
          error: function (){
            alert("Unable to open workflow popup.");
          }
        });
    }

    function attendanceupload(type, date, num, detail){
        var start_date = $("#start_date").val();
        var end_date   = $("#end_date").val();
        var emp_id     = $("#emp_id").val();
        $.ajax({
            url: "ajax.php",
            data: 'page=projecttimesheetweekly&date=' + date + '&emp_id=' + emp_id + '&type=' + type + '&detail=' + detail + '&start_date=' + start_date + '&end_date=' + end_date,
            type: "POST",
            success: function(data){
                try {
                    var obj = (typeof data === 'object') ? data : $.parseJSON(data);
                    if(obj.status === 'fail'){
                        if(typeof swal === 'function'){
                            swal({ title: "Attendance Notice", text: obj.message || "Cannot update attendance.", type: "warning" });
                        } else {
                            alert(obj.message || "Cannot update attendance.");
                        }
                        return;
                    }
                    $("#td_" + num).removeClass('week_p week_a week_z week_h week_l week_o');  
                    $("#td_" + num).addClass("week_" + type);                
                    $("#pp_val").html(obj.present_days);
                    $("#ab_val").html(obj.absent_day);
                    $("#half_val").html(obj.half_days);
                    if(obj.leave_days !== undefined) $("#leave_val").html(obj.leave_days);
                    $("#btn_leave_" + num).remove();

                    // Update label in cell
                    var labelText = "Present (8h)";
                    if(type === 'z') labelText = "Half Day (4h)";
                    if(type === 'a') labelText = "Absent";
                    var $td = $("#td_" + num);
                    $td.find('.att-status-text').remove();
                    $('<div class="mb-1 att-status-text"><small class="f-w-600 d-block">' + labelText + '</small></div>').insertAfter($td.find('.d-flex.justify-content-between'));

                    var total_work_days = parseInt(obj.work_days);
                    var total_fill_days = parseInt(obj.leave_days) + parseInt(obj.present_days) + parseInt(obj.half_days) + parseInt(obj.absent_day);
                    
                    if(obj.can_submit == 1 || (total_work_days > 0 && total_fill_days >= total_work_days)){
                        $("#status_id").show();
                    } else {
                        $("#status_id").hide();
                    }
                    if(typeof tostarnotify === 'function'){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success pt-2 pb-2','animated flipInX','animated flipOutX','Weekly timesheet updated successfully','');
                    }
                } catch(e) {
                    console.error("Parse error:", e, data);
                }
            },
            error: function (){
                alert("Failed to update timesheet.");
            }
        });   
    }

    function changeselectweek(targetDate){
        var emp_id = $("#emp_id").val();
        var option = $("#optionvalue").val();
        var dateVal = targetDate ? targetDate : $("#selected_week_date").val();
        if(!dateVal){
            alert("Please select a date/week.");
            return;
        }
        window.location = "index.php?option=" + option + "&EMP=" + emp_id + "&CHANGE_DATE=" + dateVal; 
    }

    function alradysubmitedmanager(){
        if(typeof tostarnotify === 'function'){
            tostarnotify('top','right','fa fa-info-circle','form-label label label-danger pt-2 pb-2','animated flipInX','animated flipOutX','Weekly timesheet is already submitted to manager','');
        } else {
            alert("Weekly timesheet is already submitted to manager.");
        }
    }

    // Direct Leave Application on Clicking "L" (No modal, static leave_type_id=0, policy bypass)
    var is_applying_leave = false;
    function directApplyLeave(date, num){
        if(is_applying_leave){
            return;
        }
        var emp_id     = $("#emp_id").val();
        var start_date = $("#start_date").val();
        var end_date   = $("#end_date").val();

        if(!date || !emp_id){
            alert("Missing date or employee information.");
            return;
        }

        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Are you sure you want to apply for leave? Once confirmed, you cannot change the leave request.',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                is_applying_leave = true;
                var $btn = $("#btn_leave_" + num);
                var oldHtml = $btn.html();
                $btn.html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: "ajax.php",
                    type: "POST",
                    data: {
                        page: 'directapplyweeklyleave',
                        date: date,
                        emp_id: emp_id,
                        start_date: start_date,
                        end_date: end_date
                    },
                    success: function(response){
                        is_applying_leave = false;
                        $btn.html(oldHtml);
                        try {
                            var obj = (typeof response === 'object') ? response : JSON.parse(response);
                            if(obj.status === 'fail'){
                                if(typeof swal === 'function'){
                                    swal({ title: "Leave Notice", text: obj.message || "Cannot apply leave.", type: "warning" });
                                } else {
                                    alert(obj.message || "Cannot apply leave.");
                                }
                                return;
                            }

                            // 1. Update cell styling to Leave
                            var $td = $("#td_" + num);
                            $td.removeClass('week_p week_a week_z week_h week_o').addClass('week_l');

                            // 2. Add pending badge to header if not already present
                            var $header = $td.find('.d-flex.justify-content-between');
                            if($header.find('.badge.bg-warning').length === 0){
                                $header.append('<span class="badge bg-warning text-white" title="Leave pending manager approval"><i class="fa fa-clock-o"></i> Pending</span>');
                            }

                            // 3. Update status label
                            $td.find('.att-status-text').remove();
                            $('<div class="mb-1 att-status-text"><small class="text-warning f-w-600 d-block"><i class="fa fa-hourglass-half"></i> Leave (Pending)</small></div>').insertAfter($header);

                            // 4. Remove attendance action buttons since leave is applied
                            $td.find('.btn-att-group').remove();

                            // 5. Update KPI summaries
                            if(obj.present_days !== undefined) $("#pp_val").html(obj.present_days);
                            if(obj.absent_day !== undefined)   $("#ab_val").html(obj.absent_day);
                            if(obj.half_days !== undefined)    $("#half_val").html(obj.half_days);
                            if(obj.leave_days !== undefined)   $("#leave_val").html(obj.leave_days);

                            var total_work_days = parseInt(obj.work_days || 0);
                            var total_fill_days = parseInt(obj.leave_days || 0) + parseInt(obj.present_days || 0) + parseInt(obj.half_days || 0) + parseInt(obj.absent_day || 0);

                            if(obj.can_submit == 1 || (total_work_days > 0 && total_fill_days >= total_work_days)){
                                $("#status_id").show();
                            } else {
                                $("#status_id").hide();
                            }

                            if(typeof tostarnotify === 'function'){
                                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success pt-2 pb-2','animated flipInX','animated flipOutX', obj.message || 'Leave applied successfully for ' + date,'');
                            } else {
                                alert(obj.message || 'Leave applied successfully.');
                            }
                        } catch(e) {
                            console.error("Parse error:", e, response);
                            window.location.reload();
                        }
                    },
                    error: function(err){
                        is_applying_leave = false;
                        $btn.html(oldHtml);
                        alert("Failed to submit leave request. Please check network connection.");
                    }
                });
            } 
        });
    }
</script>

<div class="card">
    <div class="card-header table-card-header">
        <h5>Weekly Attendance (Without Work Order)</h5> 
        <label class="form-label badge bg-primary p-2 m-l-5 text-white">
            <i class='ti-user'></i> <?php echo htmlspecialchars($_SESSION['users'][$_SESSION['userid']] ?? 'Employee'); ?>
        </label>
        <div class="card-header-right table-card-header">
           <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i> Back
          </a>
        </div>
    </div>
    
    <div class="card-body">
      <div class="p-4 pt-2">
        <!-- Week Selection and Navigation -->
        <div class="row align-items-center mb-3">
            <div class="col-lg-5 col-md-6 d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary week-nav-btn" title="Previous Week" onclick="changeselectweek('<?php echo $prev_week; ?>')">
                    <i class="fa fa-chevron-left"></i> Prev Week
                </button>
                <div class="input-group mt-3" style="max-width: 200px;">
                    <input type="date" name="week_date" id="selected_week_date" value="<?php echo $start_date; ?>" onchange="changeselectweek(this.value)" class="form-control form-control-sm" style="height: 38px;">
                </div>
                <button type="button" class="btn btn-outline-secondary week-nav-btn" title="Next Week" onclick="changeselectweek('<?php echo $next_week; ?>')">
                    Next Week <i class="fa fa-chevron-right"></i>
                </button>
            </div>

            <div class="col-lg-3 col-md-6 text-center text-md-start">
                <span class="badge bg-light text-dark border p-2 f-14">
                    <i class="ti-calendar text-primary"></i> 
                    <b><?php echo date('d M Y', strtotime($start_date)); ?></b> &ndash; <b><?php echo date('d M Y', strtotime($end_date)); ?></b>
                </span>
            </div>

            <div class="col-lg-4 text-end d-flex justify-content-end align-items-center gap-2">
                <?php 
                $can_submit = $data['all_data']['can_submit'] ?? 0;
                $has_above_active = count((array)$aboveactive) > 0;
                $has_active = count((array)$listactive) > 0;
                ?>
                <div class="btn-group workflow-btn" id="status_id" <?php if($can_submit != 1 || $has_above_active){ ?> style="display:none;" <?php } ?>>
                    <button type="button" class="btn btn-info btn-md waves-effect waves-light" onclick="submitWeeklyWorkflowPopUp(<?php echo $cur_status; ?>, '<?php echo $start_date; ?>', '<?php echo $end_date; ?>')"> 
                        <span class="fa fa-thumbs-up text-white"></span> &nbsp; Submit For Approval
                    </button>   
                </div> 

                <?php if($has_above_active && $approved_count < count((array)$aboveactive)){ ?>
                <div class="btn-group workflow-btn" id="status_procee_id">
                    <button type="button" class="btn btn-warning text-white btn-md waves-effect waves-light" style="pointer-events: none;"> 
                        <span class="fa fa-clock-o text-white"></span> &nbsp; APPROVAL PENDING
                    </button>   
                </div>  
                <?php } elseif($has_above_active && $approved_count == count((array)$aboveactive)){ ?>
                <div class="btn-group workflow-btn">
                    <button type="button" class="btn btn-success btn-md waves-effect waves-light" style="pointer-events: none;"> 
                        <span class="fa fa-check text-white"></span> &nbsp; APPROVED
                    </button>   
                </div>  
                <?php } ?>
            </div>
        </div>

        <!-- Legend -->
        <div class="row mb-3">
            <div class="col-12 badge-sec">
                <?php foreach($work_array as $work){ ?>
                    <span class="btn btn-white waves-effect waves-light f-13 border">
                        <?php echo $work['name']; ?>
                        <span class="badge" style="background-color:<?php echo $work['color']; ?>;"><?php echo $work['code']; ?></span>
                    </span>
                <?php } ?>
            </div>
        </div>

        <!-- Main Layout: 7 Days Grid (Left) + Summary Cards (Right) -->
        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive week_table shadow-sm rounded">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr bgcolor="#1b1b1b">
                                <?php 
                                for($i = 0; $i < 7; $i++){
                                    $col_date = date('Y-m-d', strtotime("+$i days", strtotime($start_date)));
                                    $col_day_name = date('l', strtotime($col_date));
                                ?> 
                                    <th width="14.28%" class="p-2">
                                        <div><?php echo $col_day_name; ?></div>
                                        <small class="text-white-50"><?php echo date('d M', strtotime($col_date)); ?></small>
                                    </th>
                                <?php } ?> 
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php 
                                for($k = 0; $k < 7; $k++){
                                    $predate = date('Y-m-d', strtotime("+$k days", strtotime($start_date)));
                                    $num = date('j', strtotime($predate));
                                    $day = strtolower(date('l', strtotime($predate)));
                                    $is_future = strtotime($predate) > strtotime(date('Y-m-d'));

                                    $ccww = ""; 
                                    $ccedit = 0;
                                    $view_data = "";
                                    $checkcurrent_status = 0;

                                    // 1. Check Holiday
                                    if(in_array($predate, (array)($data['listholidays'] ?? array()))){
                                        $ccww = "h";
                                        $holiday_info = $data['holidays'][$predate]->holiday_name ?? 'Holiday';
                                        $view_data = "Holiday (" . htmlspecialchars($holiday_info) . ")";
                                    }

                                    // 2. Check Approved Leave
                                    $is_appr_leave = in_array($predate, (array)($data['approved_leave_dates'] ?? $data['leave'] ?? array()));
                                    if($is_appr_leave){
                                        $ccww = "l";
                                        $leave_cat_name = $data['approved_leaves_info'][$predate]['leave_type_name'] ?? 'Leave';
                                        $view_data = htmlspecialchars($leave_cat_name) . " (Approved)";
                                    }

                                    // 2b. Check Pending Leave
                                    $is_pend_leave = in_array($predate, (array)($data['pending_leave_dates'] ?? array()));
                                    $pend_leave_name = "";
                                    if($is_pend_leave){
                                        $pend_leave_name = $data['pending_leaves_info'][$predate]['leave_type_name'] ?? 'Leave';
                                    }

                                    // 3. Check Timesheet entry (Present, Absent, Half Day)
                                    if(in_array($predate, (array)($data['timesheet_date'] ?? array()))){
                                        $ccedit = 1;
                                        $entry = $data['timesheet'][$predate] ?? array();
                                        $bhrs = floatval($entry['billable_hrs'] ?? 0);
                                        if($bhrs == 8){
                                            $ccww = "p";
                                            $view_data = "Present (8h)";
                                        } elseif($bhrs == 4){
                                            $ccww = "z";
                                            $view_data = "Half Day (4h)";
                                        } else {
                                            $ccww = "a";
                                            $view_data = "Absent";
                                        }
                                        if(($entry['current_status'] ?? 0) > 0){
                                            $checkcurrent_status = 1;
                                        }
                                    }

                                    // 4. Check Weekly Off
                                    if(!in_array($day, (array)$weekdays)){
                                        $ccww = "o";
                                        $view_data = "Week Off";
                                    }
                                ?>
                                    <td id="td_<?php echo $num; ?>" class="week_<?php echo $ccww; ?>" <?php if($is_future){ echo "style='pointer-events:none; opacity:0.4;'"; } ?>>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-secondary"><?php echo date('d', strtotime($predate)); ?></span>
                                            <?php if($is_future){ ?>
                                                <small class="text-muted"><i class="fa fa-lock"></i></small>
                                            <?php } elseif($is_pend_leave && !$is_appr_leave){ ?>
                                                <span class="badge bg-warning text-white" title="Leave pending manager approval"><i class="fa fa-clock-o"></i> Pending</span>
                                            <?php } ?>
                                        </div>

                                        <!-- Display Status Label -->
                                        <?php if(!empty($view_data)){ ?>
                                            <div class="mb-1 att-status-text">
                                                <small class="f-w-600 d-block"><?php echo $view_data; ?></small>
                                            </div>
                                        <?php } ?>

                                        <?php if($is_pend_leave && !$is_appr_leave){ ?>
                                            <div class="mb-1 att-status-text">
                                                <small class="text-warning f-w-600 d-block"><i class="fa fa-hourglass-half"></i> <?php echo !empty($pend_leave_name) ? htmlspecialchars($pend_leave_name) : 'Leave'; ?> (Pending)</small>
                                            </div>
                                        <?php } ?>

                                        <!-- Attendance Action Buttons (P / Z / A / L) -->
                                        <?php if(!$is_future && $ccww != 'o' && $ccww != 'h' && !$is_appr_leave && !$is_pend_leave){ ?>
                                            <div class="mt-2 btn-att-group">
                                                <span class="badge badge-hover" style="background-color:#2a9d8f;" title="Mark Present (8h)"
                                                    <?php if($checkcurrent_status != 1){ ?> onclick="attendanceupload('p','<?php echo $predate; ?>',<?php echo $num; ?>,'Present')" <?php }else{ ?> onclick="alradysubmitedmanager()" <?php } ?>>P</span>
                                                <span class="badge badge-hover" style="background-color:#e76f51;" title="Mark Half Day (4h)"
                                                    <?php if($checkcurrent_status != 1){ ?> onclick="attendanceupload('z','<?php echo $predate; ?>',<?php echo $num; ?>,'Half Day')" <?php }else{ ?> onclick="alradysubmitedmanager()" <?php } ?>>Z</span>
                                                <span class="badge badge-hover" style="background-color:#d62828;" title="Mark Absent"
                                                    <?php if($checkcurrent_status != 1){ ?> onclick="attendanceupload('a','<?php echo $predate; ?>',<?php echo $num; ?>,'Absent')" <?php }else{ ?> onclick="alradysubmitedmanager()" <?php } ?>>A</span>
                                                <?php if($ccedit == 0){ ?>
                                                    <span class="badge badge-hover" id="btn_leave_<?php echo $num; ?>" style="background-color:#e9c46a; color:#fff;" title="Apply Leave for <?php echo date('d M Y', strtotime($predate)); ?>"
                                                        onclick="directApplyLeave('<?php echo $predate; ?>', <?php echo $num; ?>)">L</span>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 

            <!-- Right Side KPI & Status Buckets -->
            <div class="col-lg-4">
                <!-- Summary Metrics -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 f-w-600">Weekly Summary</h6>
                    </div>
                    <div class="card-body p-2 text-center">
                        <div class="d-flex justify-content-around flex-wrap gap-1">
                            <button type="button" class="btn btn-primary btn-sm p-2" title="Total Working Days">
                                Work Days
                                <span class="badge bg-white text-primary d-block mt-1"><?php echo $data['all_data']['work_days'] ?? 0; ?></span>
                            </button>
                            <button type="button" class="btn btn-success btn-sm p-2" style="background:#2a9d8f;" title="Present">
                                Present
                                <span class="badge bg-white text-success d-block mt-1" id="pp_val"><?php echo $data['all_data']['present_days'] ?? 0; ?></span>
                            </button>
                            <button type="button" class="btn btn-warning text-white btn-sm p-2" style="background:#e76f51;" title="Half Day">
                                Half Day
                                <span class="badge bg-white text-dark d-block mt-1" id="half_val"><?php echo $data['all_data']['half_days'] ?? 0; ?></span>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm p-2" style="background:#d62828;" title="Absent">
                                Absent
                                <span class="badge bg-white text-danger d-block mt-1" id="ab_val"><?php echo $data['all_data']['absent_day'] ?? 0; ?></span>
                            </button>
                            <button type="button" class="btn btn-info btn-sm p-2" style="background:#e9c46a; color:#000;" title="Leaves">
                                Leaves
                                <span class="badge bg-white text-dark d-block mt-1" id="leave_val"><?php echo $data['all_data']['leave_days'] ?? 0; ?></span>
                            </button>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- With Me (Draft) -->
                <div class="card shadow-sm mb-2">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 f-w-600">With Me (Drafts)</h6>
                    </div>
                    <div class="card-body custom-scroll text-center p-2" style="max-height:140px; overflow-y:auto;">
                        <?php 
                        $avbl_with_me = 0;
                        foreach((array)($data['timesheet'] ?? array()) as $submited){ 
                            if(($submited['current_status'] ?? 0) < 10){ 
                                $avbl_with_me++;
                        ?>
                            <div class="row align-items-center py-1 border-bottom">
                                <div class="col-3">
                                    <span class="badge bg-secondary p-1"><i class="ti-calendar"></i> <?php echo date('d M', strtotime($submited['timesheet_date'])); ?></span>
                                </div>
                                <div class="col-5 text-start">
                                    <small class="f-w-600">
                                        <?php 
                                        $hrs = floatval($submited['billable_hrs'] ?? 0);
                                        echo $hrs == 8 ? 'Present (8h)' : ($hrs == 4 ? 'Half Day (4h)' : 'Absent');
                                        ?>
                                    </small>
                                </div>
                                <div class="col-4 text-end">
                                    <span class="badge bg-info">Draft</span>
                                </div>
                            </div>
                        <?php } } 
                        if($avbl_with_me == 0){ ?>
                            <p class="text-muted small my-2">No unsubmitted draft entries for this week.</p>
                        <?php } ?>
                    </div>
                </div>

                <!-- With Manager (Pending Approval) -->
                <div class="card shadow-sm mb-2">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 f-w-600">With Manager (Pending)</h6>
                    </div>
                    <div class="card-body custom-scroll text-center p-2" style="max-height:140px; overflow-y:auto;">
                        <?php 
                        $avbl_with_mgr = 0;
                        foreach((array)($data['timesheet'] ?? array()) as $submited){ 
                            $cst = intval($submited['current_status'] ?? 0);
                            if($cst >= 10 && $cst < 199){ 
                                $avbl_with_mgr++;
                        ?>
                            <div class="row align-items-center py-1 border-bottom">
                                <div class="col-3">
                                    <span class="badge bg-secondary p-1"><i class="ti-calendar"></i> <?php echo date('d M', strtotime($submited['timesheet_date'])); ?></span>
                                </div>
                                <div class="col-5 text-start">
                                    <small class="f-w-600">
                                        <?php 
                                        $hrs = floatval($submited['billable_hrs'] ?? 0);
                                        echo $hrs == 8 ? 'Present (8h)' : ($hrs == 4 ? 'Half Day (4h)' : 'Absent');
                                        ?>
                                    </small>
                                </div>
                                <div class="col-4 text-end">
                                    <span class="badge bg-warning text-white">Pending</span>
                                </div>
                            </div>
                        <?php } } 
                        if($avbl_with_mgr == 0){ ?>
                            <p class="text-muted small my-2">No pending approval records for this week.</p>
                        <?php } ?>
                    </div>
                </div>

                <!-- Approved -->
                <div class="card shadow-sm mb-2">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 f-w-600">Approved</h6>
                    </div>
                    <div class="card-body custom-scroll text-center p-2" style="max-height:140px; overflow-y:auto;">
                        <?php 
                        $avbl_appr = 0;
                        foreach((array)($data['timesheet'] ?? array()) as $submited){ 
                            if(intval($submited['current_status'] ?? 0) == 199){ 
                                $avbl_appr++;
                        ?>
                            <div class="row align-items-center py-1 border-bottom">
                                <div class="col-3">
                                    <span class="badge bg-secondary p-1"><i class="ti-calendar"></i> <?php echo date('d M', strtotime($submited['timesheet_date'])); ?></span>
                                </div>
                                <div class="col-5 text-start">
                                    <small class="f-w-600">
                                        <?php 
                                        $hrs = floatval($submited['billable_hrs'] ?? 0);
                                        echo $hrs == 8 ? 'Present (8h)' : ($hrs == 4 ? 'Half Day (4h)' : 'Absent');
                                        ?>
                                    </small>
                                </div>
                                <div class="col-4 text-end">
                                    <span class="badge bg-success">Approved</span>
                                </div>
                            </div>
                        <?php } } 
                        if($avbl_appr == 0){ ?>
                            <p class="text-muted small my-2">No approved records for this week.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $emp_id; ?>">
        <input type="hidden" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
        <input type="hidden" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
        <input type="hidden" name="optionvalue" id="optionvalue" value="COM_VIEW_WEEKLY_WORK_ATTENDANCES">
      </div>
    </div>
</div>


