<?php
// Leave Balance Management View
date_default_timezone_set('Asia/Kolkata');
$currentYear = date('Y');
?>

<link rel="stylesheet" href="assets/css/custom_view_employee_leave_balance.css">

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h3 class="card-title mb-0">Leave Balance Management</h3>
        </div>


        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="employee-dropdown" class="form-label"><strong>Employee</strong></label>
                    <select id="employee-dropdown"
                            class="form-select form-control js-example-basic-single"
                            name="employee_id"
                            data-action="employee-changed">
                        <option value="">-- Select Employee --</option>
                    </select>
                    <input type="hidden" id="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                    <input type="hidden" id="user_id" value="<?php echo $_SESSION['userid']; ?>">
                    <input type="hidden" id="current_year" value="<?php echo $currentYear; ?>">
                </div>
            </div>


            <div id="balance-section" class="balance-section is-hidden">
                <h5 class="mb-3"><strong>Leave Balance Summary - <?php echo $currentYear; ?></strong></h5>
                <div id="balance-content" class="row">
                    <!-- Balances will be loaded here -->
                </div>
            </div>

            <!-- Allocations Table Section -->
            <div id="allocation-section" class="allocation-section allocation-section-spacer is-hidden">
                <div class="allocation-header">
                    <h5 class="mb-0"><strong>Leave Allocations History - <?php echo $currentYear; ?></strong></h5>
                    <button class="btn btn-primary btn-sm add-allocation-btn"
                            id="btn-add-allocation"
                            data-action="add-leave-allocation">
                        <i class="fa fa-plus"></i> Add Leave Allocation
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped" id="allocations-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Leave Type</th>
                                <th scope="col">Transaction Date</th>
                                <th scope="col">Applicable Leaves</th>
                                <th scope="col">Consumed Leaves</th>
                                <th scope="col">Note</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody id="allocations-tbody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- No Data Message -->
            <div id="no-data-message" class="alert alert-info is-hidden">
                No leave data available for the selected employee.
            </div>
        </div>
    </div>
</div>

<!-- Add Leave Allocation Modal -->
<div class="modal fade" id="addAllocationModal" tabindex="-1" role="dialog" aria-labelledby="addAllocationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAllocationModalLabel">Add Leave Allocation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="leave-allocation-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="leave_type_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select id="leave_type_id"
                                    class="form-select form-control js-example-basic-single"
                                    name="leave_type_id"
                                    required>
                                <option value="">-- Select Leave Type --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   id="transaction_date"
                                   class="form-control"
                                   name="transaction_date"
                                   value="<?php echo date('Y-m-d'); ?>"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="applicable_leaves" class="form-label">Applicable Leaves</label>
                            <input type="number"
                                   id="applicable_leaves"
                                   class="form-control text-right"
                                   name="applicable_leaves"
                                   step="0.5"
                                   min="0"
                                   value="0"
                                   placeholder="0">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="consumed_leaves" class="form-label">Consumed Leaves</label>
                            <input type="number"
                                   id="consumed_leaves"
                                   class="form-control text-right"
                                   name="consumed_leaves"
                                   step="0.5"
                                   min="0"
                                   value="0"
                                   placeholder="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="note" class="form-label">Note</label>
                            <textarea id="note"
                                      class="form-control"
                                      name="note"
                                      rows="2"
                                      placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button"
                        class="btn btn-primary"
                        id="btn-save-allocation"
                        data-action="save-allocation">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/custom_view_employee_leave_balance.js"></script>
