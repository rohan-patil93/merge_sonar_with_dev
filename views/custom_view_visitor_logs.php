<?php
// visitor_out.php

$_SESSION['apply_js']['multiselest'] = 1;
date_default_timezone_set('Asia/Kolkata');
$rid = '';
if(isset($_GET['Rid']) && $_GET['Rid']!=''){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
}
$message = '';
$url = API ."?mode=getVisitorPassDetails&rid=".$rid;
$che = callAPI($url);
$data = json_decode($che, 1);
$row = $data['visitor_logs_res'] ?? array();

$building_gates_res = $data['building_gates_res'] ?? array();
$gates = array();
foreach((array)$data['building_gates_res'] as $gate){
    $gates[$gate['id']] = $gate['name'];
}

// example
?>
<?php if (!empty($message)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', <?= json_encode($message) ?>, '');
        });
    </script>
<?php endif; ?>

<div class="card">
    <div class="continer-fluid">
        <div class="card-header">
            <h3 class="mb-3">Visitor View</h3>
            <div class="card-heder-right">

            </div>
        </div>
            <div class="card-body">
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
                            <div class="visitor-col"><?= ($row['name'] ?? '') ?></div>
                            <div class="visitor-col">Whom to meet</div>
                            <div class="visitor-col"><?= $_SESSION['users'][$row['whom_to_meet']] ?? '' ?></div>
                        </div>

                        <div class="visitor-row">
                            <div class="visitor-col">Phone</div>
                            <div class="visitor-col"><?= ($row['phone'] ?? '') ?></div>
                            <div class="visitor-col">Purpose</div>
                            <div class="visitor-col"><?= ($row['purpose'] ?? '') ?></div>
                        </div>

                        <div class="visitor-row">
                            <div class="visitor-col">Email</div>
                            <div class="visitor-col"><?= ($row['email'] ?? '') ?></div>
                            <div class="visitor-col">Visitor Pass No</div>
                            <div class="visitor-col"><?= ($row['visitor_idcard_no'] ?? '') ?></div>
                        </div>

                        <div class="visitor-row">
                            <div class="visitor-col">Address</div>
                            <div class="visitor-col"><?= ($row['address'] ?? '') ?></div>
                            <div class="visitor-col">In Gate No</div>
                            <div class="visitor-col"><?=($gates[$row['in_gate_no']] ?? '') ?></div>
                        </div>

                        <div class="visitor-row">
                            <div class="visitor-col">ID Card No(<?= ($row['genc_detail_name'] ?? '') ?>)</div>
                            <div class="visitor-col"><?= ($row['idcard_number'] ?? '') ?></div>
                            <div class="visitor-col">In Date Time</div>
                            <div class="visitor-col"><?= ($row['in_datetime'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

