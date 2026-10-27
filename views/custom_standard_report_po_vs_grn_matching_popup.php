<?php
// visitors_bucket_modal.php
session_start();
$_SESSION['apply_js']['multiselest'] = 1;
include_once '../dbconfig.php';
include_once '../comm.php';
date_default_timezone_set('Asia/Kolkata');

// Sanitize inputs with allowlists
$rid = ($_GET['rid'] ?? '-');
$type   = ($_GET['type']   ?? '-');
$transaction_no = ($_GET['transaction_no'] ?? '-');

$url = API . "?mode=togetpovsgrndata&method=get"
    . "&rid=" . urlencode($rid)
    . "&type="   . urlencode($type);
$resp    = callAPI($url);
$decoded = json_decode($resp, true);
$items_res    = $decoded['items_res'] ?? array();

$title = ucfirst($type);
$count = count($items_res);
?>
<div class="modal-header py-2">
    <h6 class="modal-title"><?= $title .' - '. $transaction_no . ' - '.$count ?> record<?= $count === 1 ? '' : 's' ?></h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-0">
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width:56px">#</th>
                    <th scope="col">Item Name</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Pending Quantity</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Tax</th>
                    <th scope="col">Total</th>

                </tr>
            </thead>
            <tbody>
                <?php if (count((array)$items_res) == 0): ?>
                    <tr>
                        <td colspan="7">
                            <div class="text-center p-4">
                                <p class="text-muted fw-bold mb-3"><i class="fa fa-warning me-1"></i>No Records</p>
                                <img class="img-fluid" alt="" src="assets/images/work.png" style="max-height:300px;">
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items_res as $i => $v):
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?= htmlspecialchars($v['item_name'] ?: '-') ?>
                            </td>
                            <td><?= customeummarmonyformate($v['item_quantity']) ?? '' ?></td>
                            <td><?=  customeummarmonyformate($v['pending_qty']) ?? '-' ?></td>
                            <td><?= customeummarmonyformate($v['item_amount'])  ?? '-' ?></td>
                            <td><?= customeummarmonyformate($v['tax_amount'])  ?? '-' ?></td>
                            <td> <?= customeummarmonyformate($v['total_amount'])  ?? '-' ?></td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal-footer py-2">
    <small class="text-muted">
       Type: <?= $type ?>
    </small>
</div>
