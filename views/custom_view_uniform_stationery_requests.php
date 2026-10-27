<?php
$rid = $_GET['rid'] ?? 31;
if(isset($_GET['Rid']) && $_GET['Rid']!=''){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
}

$url = API . "?mode=getStationeryRequestView"
        . "&company_id=" . $_SESSION['company_id']
        . "&user_id=" . $_SESSION['userid']
        . "&rid=" . $rid;
$res = callAPI($url);
$data = json_decode($res, true);

$parent = $data['parent'] ?? [];
$items  = $data['items'] ?? [];
?>

<style>
    .view-title {
        font-size: 20px;
        font-weight: 700;
        color: #4B49AC;
    }
    .section-title {
        font-size: 17px;
        font-weight: 700;
        color: #4B49AC;
        margin-bottom: 15px;
        border-left: 4px solid #4B49AC;
        padding-left: 10px;
    }
    .item-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        background: #fff;
        transition: 0.2s;
    }
    .item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .item-title {
        font-size: 16px;
        font-weight: 700;
        color: #333;
    }
    .badge-type { background:#e8f0fe; color:#1a73e8; padding:4px 8px; border-radius:6px; font-size:12px; }
    .badge-color { background:#fee8e8; color:#e81a1a; padding:4px 8px; border-radius:6px; font-size:12px; }
    .badge-size { background:#fff4e6; color:#d17936; padding:4px 8px; border-radius:6px; font-size:12px; }
    .badge-qty { background:#e9ffe8; color:#2e7d32; padding:4px 10px; border-radius:6px; font-size:13px; }
    .parent-card {
        border: 1px solid #ccc;
        border-radius: 12px;
        padding: 20px;
        background: #ffffff;
    }
    .lbl {
        font-weight:600;
        color:#555;
    }
</style>

<div class="page-body">

    <!-- PAGE HEADER -->
    <div class="card">
        <div class="card-header">
        <div class="view-title">Stationery Request Details</div>
        <div class="card-header-right">
            <a href="index.php?option=COM_LIST_UNIFORM_STATIONERY_REQUESTS" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        </div>
        </div>

    </div>

    <!-- PARENT DETAILS -->
    <div class="card ">
        <div class="card-header">
        <div class="section-title">Request Information</div>
        </div>
        <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                <div class="lbl">Request No</div>
                <div><?= $parent['request_no'] ?></div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="lbl">Request Date</div>
                <div><?= fromDate($parent['request_date']) ?></div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="lbl">Required Date</div>
                <div><?= fromDate($parent['require_date']) ?></div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="lbl">Requested By</div>
                <div><?= $parent['employee_name'] ?></div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="lbl">Location</div>
                <div><?= $parent['location_name'] ?></div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="lbl">Status</div>
                <span class="badge badge-info p-2"><?= $parent['status_text'] ?></span>
            </div>

            <?php if (!empty($parent['request_comment'])) { ?>
            <div class="col-md-12 mt-3">
                <div class="lbl">Remarks</div>
                <div><?= nl2br($parent['request_comment']) ?></div>
            </div>
            <?php } ?>
        </div>
        </div>
    </div>
    <div class="card ">
        <div class="card-header">
        <div class="section-title">Requested Items</div>
        </div>
    <div class="card-body">
    <!-- ITEMS SECTION -->
    <div class="row">
        <?php if (!empty($items)) {
            foreach ($items as $it) { ?>

        <div class="col-md-4 mb-3">
            <div class="item-card">
                <div class="item-title"><?= $it['item_name'] ?></div>

                <div class="mt-2">
                    <span class="badge-type">Type: <?= $it['uniform_type_name'] ?></span>
                </div>

                <div class="mt-2">
                    <span class="badge-color">Color: <?= $it['color_name'] ?></span>
                </div>

                <div class="mt-2">
                    <span class="badge-size">Size: <?= $it['size_name'] ?></span>
                </div>

                <div class="mt-3">
                    <span class="badge-qty">Requested Qty: <?= $it['qty'] ?></span>
                </div>
            </div>
        </div>

        <?php } ?>
        <?php } else { ?>

        <div class="col-12 text-center text-muted">
            <p class="mt-4">No items available for this request.</p>
            <img src="assets/images/work.png" class="mw-100" style="max-height:250px;" alt="Preview">
        </div>

        <?php } ?>
    </div>
    </div>
    </div>

</div>
