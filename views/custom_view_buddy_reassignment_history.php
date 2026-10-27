<?php

$allowed_roles = ['ADMIN', 'HR_ADMIN', 'HR_MANAGER', 'HR'];
$user_roles = isset($_SESSION['role_ids']) ? $_SESSION['role_ids'] : [];
$has_access = false;

foreach ($allowed_roles as $role) {
    if (in_array($role, $user_roles)) {
        $has_access = true;
        break;
    }
}

if ($_SESSION['admin_type'] == '1') {
    $has_access = true;
}

if (!$has_access) {
    echo "<script>alert('Access Denied: You do not have permission to access this page.'); window.location.href='../index.php';</script>";
    exit;
}

$company_id = $_SESSION['company_id'];

// Fetch buddy reassignment history
$table_name = 'employee_buddy_change_log';
$url = API . "?mode=gettabledetails&table=" . $table_name;
$response = callAPI($url);
$history_data = json_decode($response, true);

$filtered_history = array();
if ($history_data && is_array($history_data)) {
    foreach ($history_data as $record) {
        if ($record['company_id'] == $company_id && $record['current_status'] == '0') {
            $filtered_history[] = $record;
        }
    }
}

$url = API . "?mode=gettabledetails&table=" . TBL_EMPLOYEES;
$response = callAPI($url);
$employees = json_decode($response, true);

// Create employee lookup array
$employee_names = array();

if ($employees && is_array($employees)) {
    foreach ($employees as $emp) {
        $display_name = $emp['display_name'] ?: ($emp['first_name'] . ' ' . $emp['last_name']);
        $employee_names[$emp['id']] = array(
            'name' => $display_name,
            'emp_code' => $emp['emp_code']
        );
    }
}

function getEmployeeName($emp_id, $employee_names) {
    $employee_names = $employee_names ?? [];
    if (isset($employee_names[$emp_id])) {
        return $employee_names[$emp_id]['emp_code'] . ' - ' . $employee_names[$emp_id]['name'];
    }
    return 'Unknown (ID: ' . $emp_id . ')';
}

function formatBuddyType($buddy_type) {
    $types = array(
        'hr_buddy' => 'HR Buddy',
        'finance_buddy' => 'Finance Buddy',
        'admin_buddy' => 'Admin Buddy',
        'project_buddy' => 'Project Buddy',
        'loc_buddy' => 'Location Buddy'
    );
    return isset($types[$buddy_type]) ? $types[$buddy_type] : ucfirst(str_replace('_', ' ', $buddy_type));
}
?>


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS" crossorigin="anonymous">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" integrity="sha384-V05SibXwq2x9UKqEnsL0EnGlGPdbHwwdJdMjmp/lw3ruUri9L34ioOghMTZ8IHiI" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom_view_buddy_reassignment_history.css">

    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-history me-2"></i>Buddy Reassignment History
                </h4>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <a href="index.php?option=COM_VIEW_BUDDY_REASSIGNMENT" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Reassignment
                    </a>
                </div>

                <!-- History Table -->
                <div class="table-responsive">
                    <table id="history_table" class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Buddy Type</th>
                                <th scope="col">From Buddy</th>
                                <th scope="col">To Buddy</th>
                                <th scope="col">Employees Affected</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Created Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($filtered_history)): ?>
                                <?php foreach ($filtered_history as $record): ?>
                                    <?php
                                    $employee_ids_array = json_decode($record['employee_ids'], true);
                                    $emp_count = is_array($employee_ids_array) ? count($employee_ids_array) : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo $record['id']; ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo formatBuddyType($record['buddy_type']); ?></span>
                                        </td>
                                        <td><?php echo getEmployeeName($record['from_buddy'], $employee_names); ?></td>
                                        <td><?php echo getEmployeeName($record['to_buddy'], $employee_names); ?></td>
                                        <td>
                                            <span class="badge bg-success"><?php echo $emp_count; ?> employee(s)</span>
                                        </td>
                                        <td><?php echo $_SESSION['users'][$record['created_by']] ?? 'Unknown'; ?></td>
                                        <td><?php echo fromDate($record['created_date']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary view-details-btn"
                                                    data-record-id="<?php echo $record['id']; ?>"
                                                    data-employee-ids='<?php echo htmlspecialchars($record['employee_ids']); ?>'
                                                    data-buddy-type="<?php echo formatBuddyType($record['buddy_type']); ?>"
                                                    data-from-buddy="<?php echo getEmployeeName($record['from_buddy'], $employee_names); ?>"
                                                    data-to-buddy="<?php echo getEmployeeName($record['to_buddy'], $employee_names); ?>">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No reassignment history found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailsModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Reassignment Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Buddy Type:</strong>
                            <p id="modal_buddy_type"></p>
                        </div>
                        <div class="col-md-6">
                            <strong>From Buddy:</strong>
                            <p id="modal_from_buddy"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>To Buddy:</strong>
                            <p id="modal_to_buddy"></p>
                        </div>
                    </div>
                    <hr>
                    <h6><strong>Affected Employees:</strong></h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Employee Code</th>
                                    <th scope="col">Employee Name</th>
                                </tr>
                            </thead>
                            <tbody id="modal_employees_tbody">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Names Data (for JavaScript) -->
    <script type="application/json" id="employee_names_data">
        <?php echo json_encode($employee_names); ?>
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js" integrity="sha384-ZuLbSl+Zt/ry1/xGxjZPkp9P5MEDotJcsuoHT0cM8oWr+e1Ide//SZLebdVrzb2X" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js" integrity="sha384-jIAE3P7Re8BgMkT0XOtfQ6lzZgbDw/02WeRMJvXK3WMHBNynEx5xofqia1OHuGh0" crossorigin="anonymous"></script>

    <!-- Custom JS -->
    <script src="assets/js/custom_view_buddy_reassignment_history.js"></script>

