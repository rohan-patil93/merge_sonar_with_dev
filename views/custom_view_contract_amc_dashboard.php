<?php

$url = API . "?mode=getcontractamcdashboarddata&method=get";

$response = callAPI($url);

$details = json_decode($response, true);

$todayVisits = (int) ($details['today_visits'] ?? 0);

$next7 = (int) ($details['next_7_days'] ?? 0);

$overdue = (int) ($details['overdue'] ?? 0);

$inProgress = (int) ($details['in_progress'] ?? 0);

$completedToday = (int) ($details['completed_today'] ?? 0);

$sections = array(

    array('title' => "Today's Visits", 'list' => $details['today_list'] ?? array(), 'empty' => 'No visits scheduled for today.'),

    array('title' => 'Next 7 Days', 'list' => $details['next_7_days_list'] ?? array(), 'empty' => 'No upcoming visits in the next 7 days.'),

    array('title' => 'Overdue', 'list' => $details['overdue_list'] ?? array(), 'empty' => 'No overdue visits.'),

    array('title' => 'In Progress', 'list' => $details['in_progress_list'] ?? array(), 'empty' => 'No visits in progress.'),

    array('title' => 'Completed Today', 'list' => $details['completed_today_list'] ?? array(), 'empty' => 'No visits completed today.'),

);

?>

<div class="page-body">

    <div class="row">

        <div class="col-lg-2 col-md-4 col-6 mb-3">

            <div class="card ticket-card bg-info bgtopbt"><div class="card-body text-center">

                <h2 class="m-b-0"><?php echo $todayVisits; ?></h2>

                <p class="m-b-0 m-t-5 f-12">Today's Visits</p>

            </div></div>

        </div>

        <div class="col-lg-2 col-md-4 col-6 mb-3">

            <div class="card ticket-card bg-primary bgtopbt"><div class="card-body text-center">

                <h2 class="m-b-0"><?php echo $next7; ?></h2>

                <p class="m-b-0 m-t-5 f-12">Next 7 Days</p>

            </div></div>

        </div>

        <div class="col-lg-2 col-md-4 col-6 mb-3">

            <div class="card ticket-card bg-danger bgtopbt"><div class="card-body text-center">

                <h2 class="m-b-0"><?php echo $overdue; ?></h2>

                <p class="m-b-0 m-t-5 f-12">Overdue</p>

            </div></div>

        </div>

        <div class="col-lg-2 col-md-4 col-6 mb-3">

            <div class="card ticket-card bg-warning bgtopbt"><div class="card-body text-center">

                <h2 class="m-b-0"><?php echo $inProgress; ?></h2>

                <p class="m-b-0 m-t-5 f-12">In Progress</p>

            </div></div>

        </div>

        <div class="col-lg-2 col-md-4 col-6 mb-3">

            <div class="card ticket-card bg-success bgtopbt"><div class="card-body text-center">

                <h2 class="m-b-0"><?php echo $completedToday; ?></h2>

                <p class="m-b-0 m-t-5 f-12">Completed Today</p>

            </div></div>

        </div>

    </div>

    <div class="row">

        <?php if (count((array)$sections) > 0) {

            foreach ($sections as $section) {

                $list = $section['list'] ?? array();

                ?>

                <div class="col-md-6 mb-3">

                    <div class="card h-100">

                        <div class="card-header"><h5 class="mb-0"><?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?></h5></div>

                        <div class="card-body table-responsive">

                            <table class="table table-sm table-hover f-12 mb-0">

                                <thead>

                                    <tr>

                                        <th scope="col">Contract</th>

                                        <th scope="col">Transaction No</th>

                                        <th scope="col">Planned</th>

                                        <th scope="col">Status</th>

                                        <th scope="col">Action</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if (count((array)$list) > 0) {

                                        foreach ($list as $row) {

                                            if (is_object($row)) {

                                                $row = json_decode(json_encode($row), true);

                                            }

                                            $status = (int) ($row['current_status'] ?? 0);

                                            $statusLabel = $status >= 199 ? 'Completed' : ($status > 0 ? 'In Progress' : 'Pending');

                                            ?>

                                            <tr>

                                                <td><?php echo htmlspecialchars($row['contract_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>

                                                <td><?php echo htmlspecialchars($row['transaction_no'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>

                                                <td><?php echo !empty($row['planned_service_datetime']) ? fromDate($row['planned_service_datetime']) : '-'; ?></td>

                                                <td><?php echo $statusLabel; ?></td>

                                                <td>
                                                    <a href="index.php?option=COM_VIEW_CONTRACT_AMC_SCHEDULES&contract_id=<?php echo (int) ($row['contract_id'] ?? 0); ?>" title="View Schedules" class="m-r-10">
                                                        <i class="ti-eye text-info f-16"></i>
                                                    </a>
                                                    <a href="index.php?option=COM_ASSIGN_CONTRACT_AMC_TECHNICIANS&contract_id=<?php echo (int) ($row['contract_id'] ?? 0); ?>" title="Manage Default Technicians">
                                                        <i class="ti-user text-primary f-16"></i>
                                                    </a>
                                                </td>

                                            </tr>

                                        <?php }

                                    } else { ?>

                                        <tr><td colspan="5" class="text-center text-muted"><?php echo htmlspecialchars($section['empty'], ENT_QUOTES, 'UTF-8'); ?></td></tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            <?php }

        } ?>

    </div>

</div>

