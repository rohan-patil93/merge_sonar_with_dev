<?php

// Initialize variables
$fromDate = $_POST['from_date'] ?? '';
$toDate = $_POST['to_date'] ?? '';
$filteredData = [];

// Fetch data based on the selected date range
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fromDate && $toDate) {
    $url = API . "?mode=getCSRHistory&method=get&from_date={$fromDate
}&to_date={$toDate
}";
    $response = callAPI($url);
    $filteredData = json_decode($response, true)['csr_history'] ?? [];
}
?>

<link rel="stylesheet" href="assets/css/custom_list_csr_history.css">

<div class="page-body">
    <div class="card proj-progress-card">
        <div class="card-header">
            <h5>CSR History</h5>
            <span class="text-muted">View CSR history based on the selected date range.</span>
        </div>
        <div class="card-block">
            <form method="POST" action="" class="form-inline mb-3">

                <div class="row">
                    <div class="col-md-4 form-group mr-3">
                        <label for="from_date" class="mr-2">From Date:</label>
                        <input type="date"
                            id="from_date"
                            name="from_date"
                            class="form-control csr-from-date"
                            value="<?php echo htmlspecialchars($fromDate); ?>"
                            required>

                    </div>
                    <div class="col-md-4 form-group">
                        <label for="to_date">To Date:</label>
                        <input type="date"
                            id="to_date"
                            name="to_date"
                            class="form-control csr-to-date"
                            value="<?php echo htmlspecialchars($toDate); ?>"
                            required>

                    </div>
                    <div class="col-md-4 align-self-end form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Sr. No</th>
                            <th scope="col">Creator</th>
                            <th scope="col">Project Name</th>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                            <th scope="col">Total Budget</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$filteredData) > 0) { ?>
                            <?php $i = 1;
                            foreach ($filteredData as $row) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <?php if ($_SESSION['user_image'][$row['created_by']] != ""): ?>
                                            <img src="uploads/all/<?php echo $_SESSION['user_image'][$row['created_by']]; ?>" class="img-radius img-40 align-top" alt="User Profile">
                                        <?php else: ?>
                                            <span alt="user" class="img-radius align-top custom_txt_workentry f-12 text-center"><?php echo initials($_SESSION['users'][$row['created_by']]); ?></span>
                                        <?php endif; ?>
                                        <span class='mt-2 ml-2'><?php echo $_SESSION['users'][$row['created_by']]; ?></span>
                                    </td>
                                    <td>
                                        <a class='text-primary m-r-5'
                                            href='index.php?option=COM_VIEW_CSR_PROPOSAL_DETAILS&Rid=<?= deocdeId('id:' . $row['id']) ?>'
                                            data-bs-toggle='tooltip'
                                            data-bs-placement='top'
                                            data-bs-html='true'
                                            title='<?= htmlspecialchars($row['subject'], ENT_QUOTES, 'UTF-8') ?>'>
                                            <?= htmlspecialchars(substr($row['subject'], 0, 40), ENT_QUOTES, 'UTF-8') . (strlen($row['subject']) > 40 ? '...' : '') ?>
                                        </a>
                                    </td>
                                    <td><?php echo fromDate($row['project_start_date']) ?? ''; ?></td>
                                    <td><?php echo fromDate($row['project_end_date']) ?? ''; ?></td>
                                    <td class="text-end"><?php echo customeummarmonyformate($row['total_budget_grand_total']); ?></td>
                                    <td>
                                        <span class="form-label label label-md csr-status-label"
                                            data-color="<?php echo $_SESSION['status'][$row['current_status']]['color']; ?>"
                                            data-bg="<?php echo $_SESSION['status'][$row['current_status']]['background_color'] ?? 'blue'; ?>">

                                            <?php
                                            if ($row['current_status'] == 0) {
                                                echo "Active";
                                            } elseif ($row['current_status'] > 0 && $row['current_status'] < 199) {
                                                echo "In Progress";
                                            } elseif ($row['current_status'] == 199 && $row['tracking_status'] == 0) {
                                                echo "Tracking";
                                            } elseif ($row['current_status'] == 199 && $row['tracking_status'] == 1) {
                                                echo "Completed";
                                            } elseif ($row['current_status'] == 199) {
                                                echo "Approved";
                                            } else {
                                                echo "Unknown";
                                            }
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                        <?php }
                        } else {
                            echo '<tr> <td colspan="7" class="text-center">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" >
                                        </div>
                                     </td> </tr> ';
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="assets/js/custom_list_csr_history.js"></script>
