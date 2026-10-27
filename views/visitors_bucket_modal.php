<?php
// visitors_bucket_modal.php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;

date_default_timezone_set('Asia/Kolkata');

// Sanitize inputs with allowlists
$period = strtoupper($_GET['period'] ?? 'TODAY');
$type   = strtolower($_GET['type']   ?? 'visitor');
$bucket = strtolower($_GET['bucket'] ?? 'total');
$location_id = $_GET['location_id'] ?? 0;

$periodAllow = ['TODAY', 'YESTERDAY', 'LAST7DAYS', 'ALL'];
$typeAllow   = ['visitor', 'delivery', 'service', 'employee', 'doctor', 'others'];
$bucketAllow = ['total', 'in_only', 'out'];

if (!in_array($period, $periodAllow, true)) { $period = 'TODAY';
}
if (!in_array($type,   $typeAllow,   true)) { $type   = 'visitor';
}
if (!in_array($bucket, $bucketAllow, true)) { $bucket = 'total';
}

// Adjust mode/name to match your backend
$url = API . "?mode=getVisitorsList&method=get"
    . "&period=" . urlencode($period)
    . "&type="   . urlencode($type)
    . "&bucket=" . urlencode($bucket)
    . "&location_id=" . urlencode($location_id);

$resp    = callAPI($url);
$decoded = json_decode($resp, true);
$list    = $decoded['list'] ?? [];
$delivery_company_res    = $decoded['delivery_company_res'] ?? [];
$service_company_res    = $decoded['service_company_res'] ?? [];
$emp_arr    = $decoded['emp_arr'] ?? [];

function fmtDt($s)
{
    $s = $s ?? [];
    return $s ? date('d-M-Y h:i A', strtotime($s)) : '';
}

$title = ucfirst($type) . ' – ' . strtoupper(str_replace('_', ' ', $bucket)) . " ($period)";
$count = count($list);
?>
<link rel="stylesheet" href="assets/css/visitors_bucket_modal.css">

<div class="modal-header py-2">
    <h6 class="modal-title"><?= $title ?> · <?= $count ?> record<?= $count === 1 ? '' : 's' ?></h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-0">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0 f-12">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="th-w-56">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Visitor ID</th>
                    <th scope="col">Whom to Meet</th>
                    <th scope="col">In Date Time</th>
                    <th scope="col">Out Date Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count((array)$list) == 0): ?>
                    <tr>
                        <td colspan="7">
                            <div class="text-center p-4">
                                <p class="text-muted fw-bold mb-3"><i class="fa fa-warning me-1"></i>No Records</p>
                                <img class="img-fluid empty-img-300" alt="" src="assets/images/work.png" >
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($list as $i => $v):
                        $vt = strtolower($v['visit_type'] ?? '');
                        $displayName = trim($v['name'] ?? 'NA');
                        $line2 = trim($v['email'] ?? '');
                        $line3 = trim($v['phone'] ?? '');
                        $whoCell = htmlspecialchars($_SESSION['users'][$v['whom_to_meet']] ??  '-');
                        $name_label_class = 'bg-c-red';
                        if (!empty($v['in_datetime']) && !empty($v['out_datetime'])) {
                            $name_label_class = 'bg-c-green';
                        }

                        switch ($vt) {
                            case 'delivery':
                                $displayName = trim($delivery_company_res[$v['delivery_company_person']] ?? '');
                                $line2 = trim($v['delivery_phone'] ?? '') ?: trim($v['phone'] ?? '');
                                $line3 = '';
                                $whoCell = htmlspecialchars($_SESSION['users'][$v['deliver_to']] ?? '-');
                                break;

                            case 'service':
                                $svcCompany = trim($service_company_res[$v['service_company']] ?? '');
                                $svcPerson  = trim($v['service_person'] ?? '');
                                $displayName = $svcCompany ?: ($svcPerson ?? '');
                                $line2 = $svcPerson ? ('Person: ' . htmlspecialchars($svcPerson)) : '';
                                $line3 = '';
                                $authEmp = $_SESSION['users'][$v['authorized_employee']] ?? '-';
                                $whoCell = htmlspecialchars($authEmp) ?? '';
                                break;

                            case 'employee':
                                $empEmail = $v['email'] ?? '';
                                $empPhone = $v['emp_temp_phone'] ?? '';
                                $displayName = $v['emp_temp_name'] ?? '';
                                $emp = $emp_arr[$v['employee_code']] ?? null;
                                if (is_array($emp)) {
                                    $empEmail = $emp['email'] ?? $empEmail;
                                    $empPhone = $emp['email_id'] ?? $empPhone;
                                    if (empty($displayName)) { $displayName = $emp['display_name'] ?? $displayName;
}
                                }

                                if (empty($displayName)) { $displayName = htmlspecialchars($v['emp_temp_name'] ?? '-');
}
                                $line2 = htmlspecialchars($empEmail);
                                $line3 = htmlspecialchars($empPhone);
                                $whoCell = htmlspecialchars($v['employee_department'] ?? '-');
                                break;

                            case 'doctor':
                                $displayName = trim($v['doctor_name'] ?? '');
                                $line2 =  '-';
                                $line3 = trim($v['doctor_phone'] ?? '');
                                $whoCell =   '-';

                                break;

                            default:
                                // visitor / others keep defaults: name/email/phone, whom_to_meet
                                break;
                        }
                        $roundClass = $v['out_datetime'] ? 'out' : 'in';
                        $visit_id = $v['visitor_idcard_no'] ?? $v['employee_code']  ?? '';

                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?php if (!empty($v['image_path'])) { ?>
                                    <span class="avatar-hover">

                                        <span class="rounded-circle-pop">
                                            <?php $visitor_img_src = !empty($v['image_path']) ? 'uploads/visitors/' . htmlspecialchars($v['image_path']) : 'uploads/all/no-image.jpg'; ?>
                                            <img class="rounded-circle in me-2 avatar-40" src="<?= $visitor_img_src ?>" alt="Visitor">
                                            <div class="idcard-caption"><?php echo $visit_id; ?> </div>
                                        </span>
                                    </span>
                                <?php } else { ?>
                                    <span class="avatar-hover">
                                        <span class="d-inline-flex justify-content-center align-items-center rounded-circle <?php echo $roundClass; ?>  me-2 avatar-initial avatar-20" title="<?= htmlspecialchars($displayName) ?>">
                                            <?= htmlspecialchars(initials($displayName)) ?>
                                        </span>
                                        <span class="rounded-circle-pop ">
                                            <div class="initials-big">
                                                <div class=""><?= htmlspecialchars($visit_id ?: '-') ?></div>
                                            </div>
                                        </span>
                                    </span>
                                <?php } ?>
                                <span class="label f-12 mt-1 d-inline-block <?php echo $name_label_class; ?>"><?= htmlspecialchars($displayName ?: '-') ?></span><br>
                            </td>

                            <td><?= $v['visit_type'] ?? '' ?></td>
                            <td><?= $v['visiting_company_id'] ?? ($v['visitor_idcard_no'] ?? ($v['employee_code'] ?? '')) ?></td>
                            <td><?= $_SESSION['users'][$v['whom_to_meet']] ?? $_SESSION['users'][$v['deliver_to']] ?? $_SESSION['users'][$v['authorized_employee']] ?? '-' ?></td>
                            <td><?= fmtDt($v['in_datetime']  ?? '') ?></td>
                            <td>
                                <?php if (!empty($v['out_datetime'])): ?>
                                    <?= fmtDt($v['out_datetime']) ?>
                                <?php else: ?>
                                    <a href="javascript:void(0);"
                                       class="btn btn-sm btn-primary js-checkout f-12 p-1"
                                       data-url="index.php?option=VISITOR_OUT_VIEW&type=<?php echo $v['visit_type']; ?>&gate_id=<?php echo $_SESSION['gate_id'] ?? 1; ?>&Rid=<?= deocdeId('id:' . $v['id']) ?>"
                                       data-target="_blank"
                                       rel="noopener">
                                       Check out
                                    </a>

                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="assets/js/visitors_bucket_modal.js"></script>

<div class="modal-footer py-2">
    <small class="text-muted">
        Period: <?= $period ?> · Type: <?= $type ?> · Bucket: <?= $bucket ?>
    </small>
</div>
