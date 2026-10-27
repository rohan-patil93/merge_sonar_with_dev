<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

if ($_GET['Rid'] != "") {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $feasibility_id = $rid['1'];
} else {
    $feasibility_id = $_GET['feasibility_id'] ?? '';
}

$api_url = API . "?mode=getFeasibilityReport&feasibility_id=" . $feasibility_id;
$response = callAPI($api_url);
$data = json_decode($response, true);
?>
<div class="modal-header  ">
    <h5 class="modal-title">Feasibility Report</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body custom-scroll" id="feasibilityReportCard">

    <h6 class="f-w-600 m-b-10 m-t-10">Feasibility Type: <?php echo htmlspecialchars($data['requirement']['feasibility_type']) ?? '-' ?></h6>


    <table class="table table-bordered">
        <tr>
            <th scope="col" colspan="2">Feasibility Requirement</th>
        </tr>
        <?php

        if (!empty($data['requirement']['fields'])) {
            foreach ($data['requirement']['fields'] as $label => $value) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($label) ?></td>
                    <td><?php echo htmlspecialchars($value) ?></td>
                </tr>
        <?php }
        } ?>
    </table>

    <!-- Evaluation -->

    <h6 class="f-w-600 m-b-10 m-t-10">Evaluation</h6>
    <table class="table table-bordered">
        <tr>
            <th scope="col">Evaluation Date</th>
            <th scope="col">Evaluation By</th>
            <th scope="col">Start Date</th>
            <th scope="col">End Date</th>
            <th scope="col">Estimated Budget</th>
        </tr>
        <?php if (!empty($data['evaluation'])) { ?>
            <tr>
                <td><?php echo fromDate($data['evaluation']['evaluation_date']) ?? '-' ?></td>
                <td><?php echo $_SESSION['users'][$data['evaluation']['evaluated_by']] ?? '-' ?></td>
                <td><?php echo fromDate($data['evaluation']['start_date']) ?? '-' ?></td>
                <td><?php echo fromDate($data['evaluation']['end_date']) ?? '-' ?></td>
                <td class="text-right"><?php echo customeummarmonyformate($data['evaluation']['estimated_budget']) ?? '-' ?></td>
            </tr>
        <?php } ?>
    </table>

    <!-- BOM -->

    <h6 class="f-w-600 m-b-10 m-t-10">Bill of Materials (BOM)</h6>
    <table class="table table-bordered">
        <tr>
            <th scope="col">Item Name</th>
            <th scope="col">Unit</th>
            <th scope="col">Quantity</th>
            <th scope="col">Price</th>
            <th scope="col">Amount</th>
            <th scope="col">Planned Required By</th>
            <th scope="col">Planned Returned By</th>
            <th scope="col">Actual Required By</th>
            <th scope="col">Actual Returned By</th>
        </tr>
        <?php if (!empty($data['bom'])) {
            $final_total_cost = 0.0;
            foreach ($data['bom'] as $row) {
                $final_total_cost += floatval($row['total_amount']) ?? 0; ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['items_res'][$row['item_id']]) ?? ''; ?></td>
                    <td><?php echo htmlspecialchars($data['uom_res'][$row['unit']]['uom_short_code']) ?? '';
                        ?></td>
                    <td class="text-right"><?php echo htmlspecialchars($row['item_quantity']) ?? ''; ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate(htmlspecialchars($row['item_price']) ?? 0);   ?></td>
                    <td class="text-right"><?php echo customeummarmonyformate($row['total_amount'] ?? '0'); ?></td>
                    <td><?php echo $row['planned_request_by'] ? fromDate($row['planned_request_by']) : '';   ?></td>
                    <td><?php echo $row['planned_return_by'] ? fromDate($row['planned_return_by']) : ''; ?></td>
                    <td><?php echo $row['actual_request_by'] ? fromDate($row['actual_request_by']) : ''; ?></td>
                    <td><?php echo $row['actual_return_by'] ? fromDate($row['actual_return_by']) : ''; ?></td>
                </tr>

            <?php }
            ?>
            <tr>
                <th scope="col" colspan="4" class="text-right">Total Amount:</th>
                <td class="text-right">
                    <?php echo customeummarmonyformate($final_total_cost); ?>
                </td>
                <td colspan="4"></td>
            </tr>
        <?php  }
        ?>
    </table>

    <h6 class="f-w-600 m-b-10 m-t-10">Feasibility Checklist</h6>
    <table class="table table-bordered">
        <tr>
            <th scope="col">Checklist Item</th>
            <th scope="col">Type</th>
            <th scope="col">Is Done?</th>
            <th scope="col">Comment</th>
        </tr>
        <?php if (!empty($data['checklist'])) {
            foreach ($data['checklist'] as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_name']) ?></td>
                    <td><?php echo htmlspecialchars($row['item_type']) ?></td>
                    <td><?php echo $row['is_done'] == 1 ? 'Yes' : 'No' ?></td>
                    <td><?php echo htmlspecialchars($row['comment']) ?></td>
                </tr>
        <?php }
        } ?>
    </table>

    <!-- Files -->
    <div class="form-group col-xl-12 col-md-12 mt-2 no-print" style="pointer-events: auto;">
        <h6 class="f-w-600 m-b-10 m-t-10">Uploaded Files</h6>
        <div class="row">
            <?php if (count((array)$data['files']) > 0) {
                foreach ($data['files'] as $row) { ?>
                    <div class="col-lg-4 "><?php echo $row['actual_file_name']; ?></div>
                    <div class="col-lg-2 ">
                        <a target="_blank" href="uploads/<?php echo htmlspecialchars($row['file_name']); ?>" class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    </div>
            <?php  }
            } ?>
        </div>
    </div>

</div>

<div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>

    <?php if ($data['feasibility_requirements']['current_status'] != ST_APPROVED) { ?>
        <button class="btn btn-primary waves-effect waves-light <?php echo count((array)$data['bom']) != "0" ? "" : "disabled";  ?>" onclick="closeFeasibility(<?= $feasibility_id ?>)">
            Close Feasibility
        </button>
    <?php } ?>
    <button onclick="printPDF('feasibilityReportCard')" class="btn btn-success btn-primary waves-effect waves-light">
        Print
    </button>
</div>

<script>
    function printPDF(divName) {

        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        var printWindow = window.open('', '', 'height=800,width=1000');
        printWindow.document.write('<html lang="en"><head>
  <title>Feasibility Report - InfiSuite</title>');

        printWindow.document.write(`
        <style>

            @media print {
            body { font-family: sans-serif; zoom: 90%; }
            table { width: 100%; border-collapse: collapse; font-size: 11px; }
            th, td { border: 1px solid #000; padding: 4px; text-align: left; }
            .no-print { display: none !important; }
            }
        </style>
        `);
        printWindow.document.write(`
            <div style="text-align:center;">
                <h4>Feasibility Report</h4>
                <p>Date & Time: ${new Date().toLocaleString()}</p>
            </div>
        `);
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }

    function closeFeasibility(feasibility_id) {
        $.ajax({
            url: 'ajax.php',
            method: 'POST',
            data: {
                page: 'checkBOMReturns',
                feasibility_id: feasibility_id
            },
            success: function(response) {
                const res = JSON.parse(response);
                const appName = "<?php echo ucwords($_SESSION['config']['name']); ?>";
                if (res.unreturned_items && res.unreturned_items.length > 0) {
                    const itemList = res.unreturned_items.join('\n');
                    swal({
                        title: appName,
                        text: "The following items are not yet returned:\n" + itemList + "\n\nDo you want to close this feasibility?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function(isConfirm) {
                        if (isConfirm) {
                            $.post('ajax.php', {
                                page: 'closeFeasibility',
                                feasibility_id: feasibility_id
                            }, function(resp) {
                                swal(appName, "Feasibility Closed Successfully!", "success");
                                setTimeout(() => location.reload(), 1500);
                            });
                        }
                    });
                } else {
                    swal({
                        title: appName,
                        text: "All items are returned. Do you want to close this feasibility?",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function(isConfirm) {
                        if (isConfirm) {
                            $.post('ajax.php', {
                                page: 'closeFeasibility',
                                feasibility_id: feasibility_id
                            }, function(resp) {
                                swal(appName, "Feasibility Closed Successfully!", "success");
                                setTimeout(() => location.reload(), 1500);
                            });
                        }
                    });

                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX error here
                const appName = "<?php echo ucwords($_SESSION['config']['name']); ?>";
                swal({
                    title: appName,
                    text: "An error occurred while loading data.\n\nStatus: " + status + "\nError: " + error,
                    type: "error",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "OK"
                });
            }
        });
    }
</script>
