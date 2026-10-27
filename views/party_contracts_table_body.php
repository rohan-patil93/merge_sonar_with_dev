<?php
if (!function_exists('party_contracts_render_rows')) {
    function party_contracts_render_rows($contracts)
    {
        if (count((array)$contracts) > 0) {
            $i = 1;
            foreach ($contracts as $row) {
                if (is_object($row)) {
                    $row = json_decode(json_encode($row), true);
                }
                $isAmc = ($row['is_amc_contract'] ?? '') == 'Y';
                $nextVisit = '—';
                $overdue = '—';
                $lastCompleted = '—';
                $pending = '—';
                if ($isAmc) {
                    $nextVisit = !empty($row['next_visit']) ? fromDate($row['next_visit']) : '—';
                    $overdueCnt = (int) ($row['overdue_count'] ?? 0);
                    $overdue = $overdueCnt > 0 ? '<span class="badge bg-danger">' . $overdueCnt . ' overdue</span>' : '0';
                    $lastCompleted = !empty($row['last_completed']) ? fromDate($row['last_completed']) : '—';
                    $pending = (int) ($row['pending_count'] ?? 0);
                }
                $amcBadge = $isAmc ? '<span class="badge bg-success">Y</span>' : '<span class="badge bg-secondary">N</span>';
                $contractRid = deocdeId('id:' . $row['id']);
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['contract_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_no'] ?? '-'); ?></td>
                    <td><?php echo $amcBadge; ?></td>
                    <td><?php echo $nextVisit; ?></td>
                    <td><?php echo $overdue; ?></td>
                    <td><?php echo $lastCompleted; ?></td>
                    <td><?php echo $pending; ?></td>
                    <td><?php echo !empty($row['start_date']) ? fromDate($row['start_date']) : '-'; ?></td>
                    <td><?php echo !empty($row['end_date']) ? fromDate($row['end_date']) : '-'; ?></td>
                    <td><?php echo htmlspecialchars(contractCurrentStatusLabel($row['current_status'] ?? 0, $row['cancel_status'] ?? 'N')); ?></td>
                    <td>
                        <a href="index.php?option=COM_INSERT_CONTRACTS&Rid=<?php echo $contractRid; ?>" title="Open Contract" class="m-r-10">
                            <i class="ti-pencil text-primary f-16"></i>
                        </a>
                        <?php if ($isAmc) { ?>
                        <a href="index.php?option=COM_VIEW_CONTRACT_AMC_SCHEDULES&contract_id=<?php echo (int) $row['id']; ?>" title="View Schedules" class="m-r-10">
                            <i class="ti-calendar text-info f-16"></i>
                        </a>
                        <a href="index.php?option=COM_ASSIGN_CONTRACT_AMC_TECHNICIANS&contract_id=<?php echo (int) $row['id']; ?>" title="Manage Default Technicians">
                            <i class="ti-user text-primary f-16"></i>
                        </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="12" class="text-center text-muted">No contracts found.</td>
            </tr>
            <?php
        }
    }
}
