<?php
if ($_POST['submit']){
    if ($_POST['submit'] == 'insert') {
        $url = API . "?mode=InsertCustomerEmployeeBillings&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
    }
    if ($_POST['submit'] == 'update') {
        $url = API . "?mode=InsertCustomerEmployeeBillings&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }
    $res = json_decode($response, 1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        header("location:index.php?option=COM_LIST_EMP_EMPLOYMENT_TYPE_HISTORIES");
    }
}
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $url = API . "?mode=getCustomerBillingDetails&method=get&rid=" . $rid . "&month=" . $_GET['month'];
    $che = callAPI($url);
    $return = json_decode($che, 1);
    $customerData = $return['customerData'] ?? array();
    $empList = $return['empList'] ?? array();
    $billingData = $return['billingData'] ?? array();
    $billingDetailsData = $return['billingDetailsData'] ?? array();
}
?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Customer Employee Billing</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_LIST_EMP_EMPLOYMENT_TYPE_HISTORIES&Rid=<?php echo  deocdeId('month:' . $_GET['month']) ?>" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-block">
            <form method="POST" action="">
                <div class="form-group row">
                    <span class="col-sm-2 col-form-label">Customer Name</span>
                    <div class="col-sm-4">
                        <input aria-label="Customer Name" type="text" class="form-control" name="customer_name" value="<?= htmlspecialchars($customerData['display_name']) ?>" readonly>
                        <input type="hidden" name="customer_id" value="<?= $rid ?>">
                    </div>
                    <span class="col-sm-2 col-form-label">Billing Month</span>
                    <div class="col-sm-4">
                        <input aria-label="Billing Month" type="month" class="form-control" name="billing_month" value="<?= $_GET['month'] ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <span class="col-sm-2 col-form-label">Total Working Days</span>
                    <div class="col-sm-4">
                        <input aria-label="Total Working Days" type="number" name="total_working_days" class="form-control" value="<?php echo $billingData['total_working_days'] ?? ''; ?>" required>
                    </div>
                </div>

                <h5 class="mt-4">Employees </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Employee Name</th>
                                <th scope="col">LOP Days</th>
                                <th scope="col">Customer Billing Amount</th>
                                <th scope="col">Actual Billing Amount</th>
                                <th scope="col">Employee Client Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count((array)$billingDetailsData)>0){
                                foreach ($billingDetailsData as $row){
                                    ?>
                                    <tr>
                                        <input type="hidden" name="billing_details_id[]" value="<?= $row['id'] ?>">
                                        <td>
                                            <input type="hidden" name="emp_id[]" value="<?= $row['emp_id'] ?>">
                                            <input aria-label="Emp Name[]" type="text" class="form-control" name="emp_name[]" value="<?= htmlspecialchars($row['emp_name']) ?>" readonly>
                                        </td>
                                        <td><input aria-label="Lop Days[]" type="number" name="lop_days[]" class="form-control" value="<?= $row['lop_days'] ?>" min="0" required></td>
                                        <td><input aria-label="Customer Billing[]" type="number" step="0.01" name="customer_billing[]" class="form-control" value="<?= $row['customer_billing'] ?>" required></td>
                                        <td><input aria-label="Actual Billing[]" type="number" step="0.01" name="actual_billing[]" class="form-control" value="<?= $row['actual_billing'] ?>" required></td>
                                        <td><input aria-label="Client Pay[]" type="number" step="0.01" name="client_pay[]" class="form-control" value="<?= $row['client_pay'] ?>" required></td>
                                    </tr>
                            <?php } ?>
                            <?php } else {
                                foreach ($empList as $index => $emp) {
                                    $empId = $emp['id'];
                                    $empName = htmlspecialchars($emp['display_name']);
                            ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="emp_id[]" value="<?= $empId ?>">
                                            <input aria-label="Emp Name[]" type="text" class="form-control" name="emp_name[]" value="<?= $empName ?>" readonly>
                                        </td>
                                        <td><input aria-label="Lop Days[]" type="number" name="lop_days[]" class="form-control" min="0" required></td>
                                        <td><input aria-label="Customer Billing[]" type="number" step="0.01" name="customer_billing[]" class="form-control" required></td>
                                        <td><input aria-label="Actual Billing[]" type="number" step="0.01" name="actual_billing[]" class="form-control" required></td>
                                        <td><input aria-label="Client Pay[]" type="number" step="0.01" name="client_pay[]" class="form-control" required></td>
                                    </tr>
                                <?php }
                            }  ?>

                        </tbody>
                    </table>
                </div>

                <div class="form-group mt-3">
                    <?php if ($billingData['id'] != "") {  ?>
                        <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                            Update
                        </button>
                        <input name="billing_id" type="hidden" value="<?php echo $billingData['id']; ?>">
                    <?php } else {  ?>
                        <button type="submit" name='submit' value='insert' class="btn waves-effect waves-light btn-primary">Save</button>
                    <?php } ?>


                </div>
            </form>
        </div>
    </div>
</div>
