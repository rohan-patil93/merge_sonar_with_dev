<?php
// =============================================
// CALL API: GET ONLY MAIN REQUESTS
// =============================================
$role = '';
if (in_array('UNIFORM_ADMIN', $_SESSION['role_ids'])) {
    $role = "UNIFORM_ADMIN";
}
$url = API . "?mode=getUniformDashboardData&company_id=" . $_SESSION['company_id']."&role=".$role;
$che = callAPI($url);
$data = json_decode($che, true);

$requests = $data['requests'] ?? [];

// SPLIT INTO 4 BUCKETS
$all      = $requests;
$active   = [];
$pending  = [];
$approved = [];

foreach ($requests as $r) {
    if ($r['current_status'] == 0) {
        $active[] = $r;
    } elseif ($r['current_status'] == 199) {
        $approved[] = $r;
    } elseif ($r['current_status'] > 0 && $r['current_status'] < 199) {
        $pending[] = $r;
    }
}

$count_all      = count($all);
$count_active   = count($active);
$count_pending  = count($pending);
$count_approved = count($approved);
?>
<style>
    .card-max-hegight{
        height: 350px;
    }
</style>

<!-- COUNT CARDS -->
<div class="text-end">
    <a class="btn btn-primary" href="index.php?option=COM_VIEW_UNIFORM_MASTER">+ Request Uniform</a>
</div>
<div class="row mt-2">

    <div class="col-md-3">
        <div class="card bg-primary">
            <div class="card-body text-center">
                <h5 class="text-white">All Requests</h5>
                <h2><?= $count_all ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info">
            <div class="card-body text-center">
                <h5 class="text-white">Active</h5>
                <h2><?= $count_active ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning">
            <div class="card-body text-center">
                <h5 class="text-white">Pending</h5>
                <h2><?= $count_pending ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success">
            <div class="card-body text-center">
                <h5 class="text-white">Approved</h5>
                <h2><?= $count_approved ?></h2>
            </div>
        </div>
    </div>

</div>

<div class="row mt-2">

    <!-- ALL REQUESTS TABLE -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>All Requests</h5><span class="form-label label label-sm label-success "> <?php echo $count_all; ?> </span>
                <div class="card-header-right">
                    <input aria-label="Search..." type="text" class="form-control mb-2 search-box" placeholder="Search..." data-target="tbl-all">

                </div>
            </div>

            <div class=" card-body card-max-hegight custom-scroll">
                <div class="table-responsive">
                    <table class="table table-hover" id="tbl-all">
                        <thead>
                        <tr class="bg-light">
                            <th scope="col">Request No</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Location</th>
                            <th scope="col">Request Date</th>
                            <th scope="col">Require Date</th>
                            <th scope="col">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($all)) {
                            foreach ($all as $r) { ?>

                            <!-- MAIN ROW -->
                            <tr>
                                <td>
                                    <a href="javascript:void(0)"
                                       class="req-toggle text-primary fw-bold"
                                       data-id="<?= $r['id'] ?>"
                                       data-scope="all">
                                        <?= $r['request_no'] ?>
                                    </a>
                                </td>
                                <td><?= $r['employee_name'] ?></td>
                                <td><?= $r['location_name'] ?></td>
                                <td><?= $r['request_date'] ?></td>
                                <td><?= $r['require_date'] ?></td>

                                <td>
                                    <?php if ($r['current_status'] == 0) { ?>
                                        <span class="badge bg-primary">Active</span>
                                    <?php } elseif ($r['current_status'] == 199) { ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php } elseif ($r['current_status'] > 0 && $r['current_status'] < 199) { ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary"><?= $r['current_status'] ?></span>
                                    <?php } ?>
                                </td>
                            </tr>

                            <!-- EXPANDABLE ROW -->
                            <tr id="items-row-all-<?= $r['id'] ?>"
                                class="items-row-all" style="display:none;">
                                <td colspan="6">
                                    <div class="table-responsive items-container-all-<?= $r['id'] ?>"></div>
                                </td>
                            </tr>

                        <?php }
                        } else { ?>

                            <tr>
                                <td colspan="6">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="px-4">
                                            <img class="mw-100 mh-300px" src="assets/images/work.png"
                                                style="max-height:300px;" alt="Preview">
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ACTIVE REQUESTS TABLE -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Active Requests</h5><span class="form-label label label-sm label-success "> <?php echo $count_active; ?> </span>
                <div class="card-header-right">
                    <input aria-label="Search..." type="text" class="form-control mb-2 search-box" placeholder="Search..." data-target="tbl-active">
                </div>
            </div>

            <div class=" card-body card-max-hegight custom-scroll">
                <div class="table-responsive">
                    <table class="table table-hover" id="tbl-active">
                        <tr class="bg-light">
                            <th scope="col">Request No</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Location</th>
                            <th scope="col">Request Date</th>
                            <th scope="col">Require Date</th>
                            <th scope="col">Status</th>
                        </tr>

                        <?php if (!empty($active)) {
                            foreach ($active as $r) { ?>

                            <tr>
                                <td>
                                    <a href="javascript:void(0)"
                                       class="req-toggle text-primary fw-bold"
                                       data-id="<?= $r['id'] ?>"
                                       data-scope="active">
                                        <?= $r['request_no'] ?>
                                    </a>
                                </td>
                                <td><?= $r['employee_name'] ?></td>
                                <td><?= $r['location_name'] ?></td>
                                <td><?= $r['request_date'] ?></td>
                                <td><?= $r['require_date'] ?></td>
                                <td><span class="badge bg-primary">Active</span></td>
                            </tr>

                            <tr id="items-row-active-<?= $r['id'] ?>"
                                class="items-row-active" style="display:none;">
                                <td colspan="6">
                                    <div class="table-responsive items-container-active-<?= $r['id'] ?>"></div>
                                </td>
                            </tr>

                        <?php }
                        } else { ?>

                            <tr>
                                <td colspan="6">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="px-4">
                                            <img class="mw-100 mh-300px" src="assets/images/work.png"
                                                style="max-height:300px;" alt="Preview">
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>

                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="row mt-2">

    <!-- PENDING REQUESTS TABLE -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Pending Requests</h5><span class="form-label label label-sm label-success "> <?php echo $count_pending; ?> </span>
                <div class="card-header-right">
                    <input aria-label="Search..." type="text" class="form-control mb-2 search-box" placeholder="Search..." data-target="tbl-pending">
                </div>
            </div>

            <div class=" card-body card-max-hegight custom-scroll">
                <div class="table-responsive">
                    <table class="table table-hover"  id="tbl-pending">
                        <tr class="bg-light">
                            <th scope="col">Request No</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Location</th>
                            <th scope="col">Request Date</th>
                            <th scope="col">Require Date</th>
                            <th scope="col">Status</th>
                        </tr>

                        <?php if (!empty($pending)) {
                            foreach ($pending as $r) { ?>

                            <tr>
                                <td>
                                    <a href="javascript:void(0)"
                                       class="req-toggle text-primary fw-bold"
                                       data-id="<?= $r['id'] ?>"
                                       data-scope="pending">
                                        <?= $r['request_no'] ?>
                                    </a>
                                </td>
                                <td><?= $r['employee_name'] ?></td>
                                <td><?= $r['location_name'] ?></td>
                                <td><?= $r['request_date'] ?></td>
                                <td><?= $r['require_date'] ?></td>
                                <td><span class="badge bg-warning">Pending</span></td>
                            </tr>

                            <tr id="items-row-pending-<?= $r['id'] ?>"
                                class="items-row-pending" style="display:none;">
                                <td colspan="6">
                                    <div class="table-responsive items-container-pending-<?= $r['id'] ?>"></div>
                                </td>
                            </tr>

                        <?php }
                        } else { ?>

                            <tr>
                                <td colspan="6">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="px-4">
                                            <img class="mw-100 mh-300px" src="assets/images/work.png"
                                                style="max-height:300px;" alt="Preview">
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- APPROVED REQUESTS TABLE -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Approved Requests</h5><span class="form-label label label-sm label-success "> <?php echo $count_approved; ?> </span>
                <div class="card-header-right">
                    <input aria-label="Search..." type="text" class="form-control mb-2 search-box" placeholder="Search..." data-target="tbl-approved">
                </div>
            </div>

            <div class=" card-body card-max-hegight custom-scroll">
                <div class="table-responsive">
                    <table class="table table-hover" id="tbl-approved">
                        <tr class="bg-light">
                            <th scope="col">Request No</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Location</th>
                            <th scope="col">Request Date</th>
                            <th scope="col">Require Date</th>
                            <th scope="col">Status</th>
                        </tr>

                        <?php if (!empty($approved)) {
                            foreach ($approved as $r) { ?>

                            <tr>
                                <td>
                                    <a href="javascript:void(0)"
                                       class="req-toggle text-primary fw-bold"
                                       data-id="<?= $r['id'] ?>"
                                       data-scope="approved">
                                        <?= $r['request_no'] ?>
                                    </a>
                                </td>
                                <td><?= $r['employee_name'] ?></td>
                                <td><?= $r['location_name'] ?></td>
                                <td><?= $r['request_date'] ?></td>
                                <td><?= $r['require_date'] ?></td>
                                <td><span class="badge bg-success">Approved</span></td>
                            </tr>

                            <tr id="items-row-approved-<?= $r['id'] ?>"
                                class="items-row-approved" style="display:none;">
                                <td colspan="6">
                                    <div class="table-responsive items-container-approved-<?= $r['id'] ?>"></div>
                                </td>
                            </tr>

                        <?php }
                        } else { ?>

                            <tr>
                                <td colspan="6">
                                    <div class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class="fa fa-warning"></i> No Records
                                        </p>
                                        <div class="px-4">
                                            <img class="mw-100 mh-300px" src="assets/images/work.png"
                                                style="max-height:300px;" alt="Preview">
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        <?php } ?>

                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
<script>
$(document).on("click", ".req-toggle", function() {

    let reqId  = $(this).data("id");
    let scope  = $(this).data("scope"); // all / active / pending / approved
    let rowSel = "#items-row-" + scope + "-" + reqId;
    let contSel = ".items-container-" + scope + "-" + reqId;

    // Close other expanded rows in same scope
    $(".items-row-" + scope).not(rowSel).slideUp();

    if ($(rowSel).is(":visible")) {
        $(rowSel).slideUp();
        return;
    }

    $.ajax({
        url: "ajax/uniform_ajax.php",
        type: "POST",
        data: {
            page: "getUniformRequestItems",
            request_id: reqId
        },
        success: function(res) {
            let data = JSON.parse(res);

            let html = `

                <table class="table table table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Color</th>
                            <th scope="col">Size</th>
                            <th scope="col">Uniform Type</th>
                            <th scope="col">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.item_name || ''}</td>
                            <td>${item.color_name || ''}</td>
                            <td>${item.size_name || ''}</td>
                            <td>${item.uniform_type_name || ''}</td>
                            <td>${item.qty}</td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="5" class="text-center text-muted">No Items Found</td>
                    </tr>
                `;
            }

            html += "</tbody></table>";

            $(contSel).html(html);
            $(rowSel).slideDown();
        }
    });

});

$(document).on("keyup", ".search-box", function () {

    let value = $(this).val().toLowerCase();
    let tableId = $(this).data("target");

    $("#" + tableId + " tbody tr").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });

});

</script>
