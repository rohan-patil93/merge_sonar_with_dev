<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$title = $_GET['title'] ?? 'Reports';

$reports = array(
    array('label' => 'Vendor AMC Tracker', 'option' => 'COM_VIEW_VENDOR_AMC_TRACKER_REPORT'),
    array('label' => 'Vendor AMC Schedule', 'option' => 'COM_VIEW_VENDOR_AMC_SCHEDULE_REPORT'),
    array('label' => 'In-house PPM Report', 'option' => 'COM_VIEW_PPM_INHOUSE_REPORT'),
    array('label' => 'Complaints Report', 'option' => 'COM_LIST_AMC_COMPLAINTS_REPORT'),
);
?>

<div class="modal-header">
    <h5 class="modal-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="card table-card mb-0">
        <div class="card-block custom-scroll" style="height: 320px;">
            <div class="table-responsive">
                <table class="table table-hover m-b-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Report Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count((array)$reports) > 0) {
                            $serial = 1;
                            foreach ($reports as $report) {
                                $reportUrl = 'index.php?option=' . urlencode($report['option']);
                                $reportLabel = htmlspecialchars($report['label'], ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr>
                                    <td><?php echo $serial; ?></td>
                                    <td><a class="text-primary" href="<?php echo $reportUrl; ?>"><?php echo $reportLabel; ?></a></td>
                                </tr>
                                <?php
                                $serial++;
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
</div>
