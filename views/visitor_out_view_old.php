<?php
// visitor_view.php
session_start();
$_SESSION['apply_js']['multiselest'] = 1;
include_once('../dbconfig.php');
include_once('../comm.php');

date_default_timezone_set('Asia/Kolkata');
$id = isset($_GET['id']) ? $_GET['id'] : '';

$url = API ."?mode=getVisitorPassDetails&visitor_idcard_no=".$id;
$che = callAPI($url);
$data = json_decode($che, 1);

$row = $data['visitor_logs_res'] ?? array();
$building_gates_res = $data['building_gates_res'] ?? array();
$gates = array();
foreach((array)$data['building_gates_res'] as $gate){
    $gates[$gate['id']] = $gate['name'];
}


$outSecurityId = $_SESSION['userid'] ?? null;


function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Helper: default value for datetime-local input in local time


$alreadyOut = empty($row['id']);
?>
<?php if ($alreadyOut): ?>
    <div class="alert alert-info">
        This visitor was already checked out / No visiter with that ID.
    </div>
<?php else: ?>


    <style>
        .visitor-details {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .visitor-details-header {
            padding: 10px 15px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
        }

        .visitor-row {
            display: flex;
            border-bottom: 1px solid #eee;
        }

        .visitor-row:nth-child(odd) {
            background-color: #f5f5f5;
        }

        .visitor-row:hover {
            background-color: #e0eaf3;
        }

        .visitor-col {
            flex: 1;
            padding: 10px 15px;
            border-right: 1px solid #eee;
            font-weight: bold;
        }

        .visitor-col:nth-child(2) {
            font-weight: normal;
        }

        .visitor-col:last-child {
            border-right: none;
            font-weight: normal;
        }
    </style>

    <div class="card">
        <div class="card-header">
            <h5>Visitor Details </h5>
        </div>
        <div class="card-body">
            <div class="visitor-details">
                <div class="visitor-row">
                    <div class="visitor-col">Name</div>
                    <div class="visitor-col"><?= e($row['name'] ?? '') ?></div>
                    <div class="visitor-col">Whom to meet</div>
                    <div class="visitor-col"><?= $_SESSION['users'][$row['whom_to_meet']] ?? '' ?></div>
                </div>

                <div class="visitor-row">
                    <div class="visitor-col">Phone</div>
                    <div class="visitor-col"><?= e($row['phone'] ?? '') ?></div>
                    <div class="visitor-col">Purpose</div>
                    <div class="visitor-col"><?= e($row['purpose'] ?? '') ?></div>
                </div>

                <div class="visitor-row">
                    <div class="visitor-col">Email</div>
                    <div class="visitor-col"><?= e($row['email'] ?? '') ?></div>
                    <div class="visitor-col">Visitor Pass No</div>
                    <div class="visitor-col"><?= e($row['visitor_idcard_no'] ?? '') ?></div>
                </div>

                <div class="visitor-row">
                    <div class="visitor-col">Address</div>
                    <div class="visitor-col"><?= e($row['address'] ?? '') ?></div>
                    <div class="visitor-col">In Gate No</div>
                    <div class="visitor-col"><?= e($gates[$row['in_gate_no']] ?? '') ?></div>
                </div>

                <div class="visitor-row">
                    <div class="visitor-col">ID Card No</div>
                    <div class="visitor-col"><?= e($row['idcard_number'] ?? '') ?></div>
                    <div class="visitor-col">In Date Time</div>
                    <div class="visitor-col"><?= e($row['in_datetime'] ?? '') ?></div>
                </div>
            </div>
        </div>
    </div>

    <form id="visitorOutForm" class="card" method="post">
        <div class="card-header">
            <h5>Visitor Out</h5>
            
        </div>
        <div class="card-body row g-3">
            <!-- hidden essentials -->
            <input type="hidden" id="rid" name="rid" value="<?= e($row['id'] ?? '') ?>">
            <input type="hidden" id="out_security_id" name="out_security_id" value="<?= e($outSecurityId) ?>">

            <div class="col-md-4">
                <label class="form-label">Out Date &amp; Time</label>
                <input type="datetime-local" class="form-control" id="out_datetime" name="out_datetime"
                    value="<?= e(date('Y-m-d\TH:i')) ?>" min="<?= e(date('Y-m-d\TH:i')) ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Out Gate No</label>
                <!-- <input type="text" class="form-control" id="out_gate_no" name="out_gate_no" required> -->
                <select class="form-select js-example-basic-single  " id="out_gate_no" name="out_gate_no" required>
                    <option value="">Select Gate</option>
                    <?php foreach ($building_gates_res as $gate): ?>
                    <option value="<?= e($gate['id']) ?>" <?php if (($row['in_gate_no'] ?? '') == $gate['id']) echo 'selected'; ?>>
                        <?= e($gate['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-8">
                <label class="form-label">Remarks</label>
                <textarea type="text" class="form-control" id="remarks" name="remarks" rows="3"
                    placeholder="Optional note"></textarea>
            </div>
        </div>
        <?php $disabled = $row['id'] ? '' : 'disabled'; ?>
        <div class="card-footer d-flex align-items-center gap-3">
            <button type="submit" id="btnSubmitOut" class="btn btn-success" name="submit" value="update" <?php echo $disabled; ?>>Submit Out</button>
            <span id="outMsg" class="small text-muted"></span>
        </div>
    </form>
<?php endif; ?>