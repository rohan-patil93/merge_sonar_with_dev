<?php
$asset = $scheduleData['asset'] ?? array();
$schedules = $scheduleData['schedules'] ?? array();
$assetCode = htmlspecialchars($asset['asset_code'] ?? '—', ENT_QUOTES, 'UTF-8');
$assetName = htmlspecialchars($asset['asset_name'] ?? '—', ENT_QUOTES, 'UTF-8');
?>
<table class="table table-bordered table-sm f-12 mb-0">
    <thead class="table-light">
        <tr>
            <th>Asset Code</th>
            <th>Asset Name</th>
            <th>Planned Date</th>
            <th>Actual Start</th>
            <th>Actual End</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count((array)$schedules) == 0): ?>
            <tr class="amc-history-empty">
                <td colspan="6" class="text-center text-muted">No planned visits — submit the contract to generate the schedule.</td>
            </tr>
        <?php else: ?>
            <?php if (count((array)$schedules) > 0) {
                foreach ($schedules as $row) {
                    $status = (int) ($row['current_status'] ?? 0);
                    $statusLabel = 'Pending';
                    $statusClass = 'bg-primary';
                    if ($status > 0 && $status < 199) {
                        $statusLabel = 'In Progress';
                        $statusClass = 'bg-warning';
                    } elseif ($status >= 199) {
                        $statusLabel = 'Completed';
                        $statusClass = 'bg-success';
                    }
            ?>
            <tr>
                <td><?= $assetCode ?></td>
                <td><?= $assetName ?></td>
                <td><?= htmlspecialchars($row['planned_display'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['actual_start_display'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['actual_end_display'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
            </tr>
            <?php }
            } ?>
        <?php endif; ?>
    </tbody>
</table>
