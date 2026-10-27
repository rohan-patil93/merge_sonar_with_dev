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
?>


    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha384-3B6NwesSXE7YJlcLI9RpRqGf2p/EgVH8BgoKTaUrmKNDkHPStTQ3EyoYjCGXaOTS" crossorigin="anonymous">


    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"  integrity="sha384-OXVF05DQEe311p6ohU11NwlnX08FzMCsyoXzGOaL+83dKAb3qS17yZJxESl8YrJQ" crossorigin="anonymous"/>
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"  integrity="sha384-IrMr0LFnIMa9H6HhC5VVqVuWNEIwspnRLKQc0SUyPj4Cy4s02DiWDZEoJOo5WNK6" crossorigin="anonymous"/>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" integrity="sha384-V05SibXwq2x9UKqEnsL0EnGlGPdbHwwdJdMjmp/lw3ruUri9L34ioOghMTZ8IHiI" crossorigin="anonymous">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" integrity="sha384-dCW5imOdApH6OwpFau8cZNKjqVbJYnCA5q+8YsMYP3XwXKsV6Jfz1u6MZLnXaBsS" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom_view_buddy_reassignment.css">

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-users-cog me-2"></i>Employee Buddy Reassignment
                        </h4>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <a href="index.php?option=COM_VIEW_BUDDY_REASSIGNMENT_HISTORY" class="btn btn-secondary">
                                <i class="fas fa-history me-1"></i>View Reassignment History
                            </a>
                        </div>


                        <form id="buddy_reassignment_form">
                            <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
                            <input type="hidden" name="created_by" id="created_by" value="<?php echo $_SESSION['userid']; ?>">

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="buddy_type" class="form-label">Buddy Type <span class="text-danger">*</span></label>
                                    <select name="buddy_type" id="buddy_type" class="form-select" required>
                                        <option value="">-- Select Buddy Type --</option>
                                        <option value="admin_buddy">Approval 1</option>
                                        <option value="project_buddy">Approval 2</option>
                                        <option value="finance_buddy">Approval 3</option>

                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="from_buddy" class="form-label">From Buddy <span class="text-danger">*</span></label>
                                    <select name="from_buddy" id="from_buddy" class="form-select js-example-basic-single" required>
                                        <option value="">-- Select Employee --</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="to_buddy" class="form-label">To Buddy <span class="text-danger">*</span></label>
                                    <select name="to_buddy" id="to_buddy" class="form-select js-example-basic-single" required>
                                        <option value="">-- Select Employee --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" id="btn_preview" class="btn btn-info">
                                        <i class="fas fa-eye me-1"></i>Preview Affected Employees
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="fas fa-redo me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Preview Section (Initially Hidden) -->
                        <div id="preview_section" class="mt-4" style="display: none;">
                            <hr>
                            <h5 class="mb-3">
                                <i class="fas fa-users me-2"></i>Affected Employees Preview
                            </h5>
                            <div id="preview_info" class="alert alert-info mb-3"></div>

                            <div class="table-responsive">
                                <table id="employees_table" class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Emp Code</th>
                                            <th scope="col">Employee Name</th>
                                            <th scope="col">Department</th>
                                            <th scope="col">Designation</th>
                                            <th scope="col">Current Buddy</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employees_tbody">

                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="button" id="btn_confirm" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle me-1"></i>Confirm Reassignment
                                </button>
                                <button type="button" id="btn_cancel_preview" class="btn btn-danger btn-lg">
                                    <i class="fas fa-times-circle me-1"></i>Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>




    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" integrity="sha384-d3UHjPdzJkZuk5H3qKYMLRyWLAQBJbby2yr2Q58hXXtAGF8RSNO9jpLDlKKPv5v3" crossorigin="anonymous"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js" integrity="sha384-ZuLbSl+Zt/ry1/xGxjZPkp9P5MEDotJcsuoHT0cM8oWr+e1Ide//SZLebdVrzb2X" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js" integrity="sha384-jIAE3P7Re8BgMkT0XOtfQ6lzZgbDw/02WeRMJvXK3WMHBNynEx5xofqia1OHuGh0" crossorigin="anonymous"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>

    <!-- Custom JS -->
    <script src="assets/js/custom_view_buddy_reassignment.js"></script>

