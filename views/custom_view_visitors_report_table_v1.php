<?php
// visitor_view.php
date_default_timezone_set('Asia/Kolkata');
if(isset($_GET['page']) && $_GET['page'] === 'AJAX_CALL' && isset($_GET['in_datetime']) && isset($_GET['out_datetime']) ){
    // AJAX call to fetch data based on date range
    $in_datetime = $_GET['in_datetime'];
    $out_datetime = $_GET['out_datetime'];
    $visit_type = $_GET['visit_type'] ?? '';
    include_once('../includes/session.php');
    include_once('../dbconfig.php');
    include_once('../comm.php');
}

if (!isset($visit_type)) {
    $visit_type = '';
}

$url = API . "?mode=getVisitorsReportData&in_datetime=" . $in_datetime . "&out_datetime=" . $out_datetime . "&visit_type=" . urlencode($visit_type);
// echo $url;
$che = callAPI($url);
$data = json_decode($che, 1); 
$visitor_logs_res = $data['visitor_logs_res'] ?? array();
$delivery_company_res = $data['delivery_company_res'] ?? array();
$service_company_res = $data['service_company_res'] ?? array();
$employees_res = $data['employees_res'] ?? array();

$_SESSION['download_data'] = [];
$_SESSION['csv_header']['company_name'] = $_SESSION['config']['name'] ?? '';
$_SESSION['csv_header']['title'] = 'Visitors';
$_SESSION['csv_header']['name'] = $_SESSION['users'][$_SESSION['userid']] ?? ($_SESSION['us_name'] ?? '');
$_SESSION['csv_header']['date_time'] = date('Y-m-d H:i:s');


?>


<div class="card">
    <div class="card-body">
        <div class="card-title  mb-4">
            <div class="row">
                <div class="col-lg-3">
                    <div class="col bg-light-primary ">
                        <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                            <img src="<?php echo $_SESSION['config']['logo'];?>" class="img-fluid report-logo" alt="Theme-Logo" style="height: 50px;">
                        </span>
                        <span class="text-dark-75 font-weight-bolder font-size-h3">Infisuite</span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4 class="card-label text-center f-w-700"> <br>
                        <small class="f-14">Name : <?php echo $_SESSION['users'][$_SESSION['userid']] ?? $_SESSION['us_name']; ?> &nbsp; Date &amp; Time : <?php echo date('Y-m-d H:i:s'); ?></small>
                    </h4>
                </div>
                <div class="col-lg-3"></div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5>Vistors</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th>Sl No</th>
                    <th>Visit Type</th>
                    <th>Name/Company Name</th>
                    <th>Visiting ID/Emp ID</th>
                    <th>Phone</th>
                    <th>In Date Time</th>
                    <th>Out Date Time</th>
                    <th>Whom to meet/Authorized</th>
                </tr>
                <?php if (count((array)$visitor_logs_res) > 0) {
                    $csvData = [];
                    foreach ($visitor_logs_res as $index => $visitor) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars(ucwords($visitor['visit_type']) ?? '') ?></td>
                            <td>
                                <?php if ($visitor['image_path'] != "") { ?>
                                    <img src="uploads/all/<?php echo  $visitor['image_path'] ?>" class="img-radius img-40 align-top">
                                <?php } else { ?>
                                    <span class="avatar-initial-sm img-radius align-top text-center">
                                        <?= htmlspecialchars(initials($visitor['name']) ?? initials($visitor['service_person']) ?? initials($visitor['employee_name']) ?? initials($visitor['doctor_name']) ?? initials($delivery_company_res[$visitor['delivery_company_person']]) ?? initials($service_company_res[$visitor['service_company']]) ?? ''); ?> 
                                    </span>

                                <?php } ?>
                                <label class="mt-2 ml-2"> <?= htmlspecialchars(
                                    $visitor['name'] ?? 
                                    $visitor['service_person'] ?? 
                                    $visitor['employee_name'] ?? 
                                    $visitor['doctor_name'] ?? 
                                    $delivery_company_res[$visitor['delivery_company_person']] ??
                                    $service_company_res[$visitor['service_company']]) ?? ''; ?> </label> 
                            </td>
                            <td><?= htmlspecialchars($visitor['visitor_idcard_no'] ?? $visitor['employee_code'] ??'') ?></td>
                            <td><?= htmlspecialchars($visitor['phone'] ?? $visitor['delivery_phone'] ?? $visitor['service_phone'] ?? $visitor['doctor_phone'] ?? '') ?></td> 
                            
                            <td>
                                <?= isset($visitor['in_datetime']) ? date('d-M-Y h:i A', strtotime($visitor['in_datetime'])) : '' ?>
                            </td>
                            <td>
                                <?= isset($visitor['out_datetime']) ? date('d-M-Y h:i A', strtotime($visitor['out_datetime'])) : '' ?>
                            </td>
                            <td><?= htmlspecialchars($_SESSION['users'][$visitor['whom_to_meet']] ?? $_SESSION['users'][$visitor['deliver_to']] ?? $_SESSION['users'][$visitor['authorized_employee']] ?? '-') ?></td>
                        </tr>
                        <?php
                        $csvData[] = [
                            'Sl No' => $index + 1,
                            'Visit Type' => ucwords($visitor['visit_type'] ?? ''),
                            'Name/Company Name' => $visitor['name'] ?? 
                                    $visitor['service_person'] ?? 
                                    $visitor['employee_name'] ?? 
                                    $visitor['doctor_name'] ?? 
                                    $delivery_company_res[$visitor['delivery_company_person']] ??
                                    $service_company_res[$visitor['service_company']] ?? '',
                            'Visiting ID/Emp ID' => $visitor['visitor_idcard_no'] ?? ($visitor['employee_code'] ?? ''),
                            'Phone' => $visitor['phone'] ?? ($visitor['delivery_phone'] ?? ($visitor['service_phone'] ?? ($visitor['doctor_phone'] ?? ''))),
                            
                            'In Date Time' => isset($visitor['in_datetime']) ? date('d-M-Y h:i A', strtotime($visitor['in_datetime'])) : '',
                            'Out Date Time' => isset($visitor['out_datetime']) ? date('d-M-Y h:i A', strtotime($visitor['out_datetime'])) : '',
                            'Whom to meet/Authorized' => $_SESSION['users'][$visitor['whom_to_meet']] ?? ($_SESSION['users'][$visitor['deliver_to']] ?? $_SESSION['users'][$visitor['authorized_employee']]  ?? '-')
                        ];
                        ?>
                    <?php }
                    $_SESSION['download_data'] = $csvData;
                } else { ?>
                    <tr>
                        <td colspan="7">
                            <div class="text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 empty-img-300" alt="" src="assets/images/work.png" >
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>

            </table>
        </div>
    </div>
</div>